<?php
/**
* User perfect matches listing
*
* @package DatingPro
* @subpackage User Mode
**/

// THIS MODULE HAS NOT BEEN FULLY INTEGRATED !!!
//
// ADD CONNECTIONS IS MISSING
// DESIGN AND LINKS NEED TO BE STANDARDIZED

include './include/config.php';
include './common.php';
include './include/config_index.php';
include './include/functions_auth.php';
include './include/functions_index.php';
include './include/functions_users.php';
include './include/class.lang.php';
include './include/class.percent.php';

// authentication
$user = auth_index_user();

if (empty($user) || $user == 'err' || empty($user[ AUTH_ID_USER ])) {
	header('location: '.$config['site_root'].'/index.php');
	exit;
}

// check guest
if ($user[ AUTH_GUEST ]) {
	AlertPage(GetRightModulePath(__FILE__));
}

// check group, period, expiration
RefreshAccount();

// check status
if (!$user[ AUTH_STATUS ]) {
	AlertPage(GetRightModulePath(__FILE__));
}

// check permissions
IsFileAllowed(GetRightModulePath(__FILE__));

// alerts and statistics
if (!$user[ AUTH_GUEST ]) {
	GetAlertsMessage();
	SetModuleStatistic(GetRightModulePath(__FILE__));
}

// active menu item
$smarty->assign('sub_menu_num', '3');

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

// limited access for trial users and inactive users: nothing to do here
// addconnections gets a special treatment in function AddToConnections() in functions_index.php

// dispatcher
switch ($sel) {
	case 'kiss':
		$res = SendKiss();
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			header('Location: perfect_match.php?par=send');
			exit;
		} else {
			$_GET['par'] = 'send';
			SearchTable($res['err']);
		}
	break;
	
	case 'addlist':
		$res = AddToHotList();
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			header('Location: perfect_match.php?par=send');
			exit;
		} else {
			$_GET['par'] = 'send';
			SearchTable($res['err']);
		}
	break;
	
	case 'blacklist':
		$res = AddToBlackList();
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			header('Location: perfect_match.php?par=send');
			exit;
		} else {
			$_GET['par'] = 'send';
			SearchTable($res['err']);
		}
	break;
	
	case 'full':
		SearchTable('', 'full');
	break;
	
	default:
		SearchTable();
	break;
}

exit;


function SearchTable($err='', $par='')
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	/////// looking for profile comply the our perfect match requirements
	$file_name = "perfect_match.php";
	
	if (isset($_SESSION['err'])) {
		$err = $_SESSION['err'];
		unset($_SESSION['err']);
	}

	$page = isset($_REQUEST["page"])?$_REQUEST["page"]:"";
	$filter = isset($_REQUEST["filter"])?$_REQUEST["filter"]:"";
	$view = isset($_REQUEST["view"])?$_REQUEST["view"]:"list";
/////////////////////////////////////////////////////////////////////////////////////

	$use_session = 0;

	switch($filter){
		case "all": $id_arr = isset($_SESSION["id_arr"])?$_SESSION["id_arr"]:array(); break;
		case "photo": $id_arr = isset($_SESSION["with_arr"])?$_SESSION["with_arr"]:array(); break;
		case "online": $id_arr = isset($_SESSION["online_arr"])?$_SESSION["online_arr"]:array(); break;
		default: $id_arr = isset($_SESSION["id_arr"])?$_SESSION["id_arr"]:array();
	}
	if(intval($page)>0 && is_array($id_arr) && count($id_arr)>0 ){
		$use_session = 1;		//// use a session id array
	}
	if( (strval($page) == "") || (strval($page) == "0")){ $page = 1;}else{	$page=intval($page);}


	if(!isset($_GET["par"])) unset($_SESSION["search_page"]);
	if((strval($page) == "") || (strval($page) == "0")){
		if (isset($_SESSION["search_page"]))
			$page = $_SESSION["search_page"];
		else
			$page = 1;
	} else {
		$page = intval($page);
		$_SESSION["search_page"] = $page;
	}

	// settings
	$settings = GetSiteSettings(array('icon_male_default','icon_female_default','icons_folder','show_users_connection_str','show_users_comments','show_users_group_str','use_kiss_types','thumb_max_width', 'use_friend_types'));
	$smarty->assign("icon_width", $settings["thumb_max_width"]);

	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];
	
	// profile completion
	$profile_percent = new Percent($user[ AUTH_ID_USER ]);
	
	$descr_perc = $profile_percent->GetSectionPercent(6);
	$interests_perc = $profile_percent->GetSectionPercent(7);

	////// $descr_perc ~ 70%, $interests_perc ~ 30%
	/// this is avg percent if  $summ_all< 75 user must fill his perfect mach section
	$summ_all = round($descr_perc*0.7 + $interests_perc*0.3);

	if($summ_all < 75){
			$form["not_fill"] = str_replace("[percent]", $summ_all,$lang["err"]["back_to_perfect_match"]);
			$smarty->assign("empty", "2");
			$form["perfect_link"] = "./myprofile.php?sel=3";
	}else{
		if(isset($_GET["par"]) && ($_GET["par"]=="back")){ ////if user clicked on back to list button
			$id_arr = $_SESSION["id_arr"];
			if(is_array($id_arr) && count($id_arr)>0){
				if($_SESSION["percent_arr"][$id_arr[0]]<98){
					$perfect_match_type = 2;
				}else{
					$perfect_match_type = 1;
				}
				$use_session = 1; //// use a session id array
			}else{
				$use_session = 0; //// use a session data array
			}
		}

		if($use_session == 0){
			unset($_SESSION["id_arr"], $_SESSION["with_arr"], $_SESSION["without_arr"], $_SESSION["online_arr"], $_SESSION["offline_arr"]);	// unset session data
			$match_arr = GetPerfectUsersList($user[ AUTH_ID_USER ]);

			$in_str = (count($match_arr["id_arr"])>0)?implode(", ", $match_arr["id_arr"]):"0";

			$strSQL =
				'SELECT DISTINCT a.id, a.icon_path, e.id_user AS session
				   FROM '.USERS_TABLE.' a
			  LEFT JOIN '.ACTIVE_SESSIONS_TABLE.' e ON a.id = e.id_user
				  WHERE a.id IN ('.$in_str.')';
			$rs = $dbconn->Execute($strSQL);
			$i = 0;
			$perfect_match_type = 0;
			while(!$rs->EOF){
				$row = $rs->GetRowAssoc(false);
				if($par != 'full' && intval($match_arr["percent_arr"][$row["id"]]) >= 98){	////// profiles ~ match for 100%
					$perfect_match_type = 1;
					$_SESSION["id_arr"][$i] = $row["id"];
					$_SESSION["percent_arr"][$row["id"]] = "100";
					if(strlen($row["icon_path"])){
						$_SESSION["with_arr"][$i] = $row["id"];
					}else{
						$_SESSION["without_arr"][$i] = $row["id"];
					}
					if(intval($row["session"])){
						$_SESSION["online_arr"][$i] = $row["id"];
					}else{
						$_SESSION["offline_arr"][$i] = $row["id"];
					}
					$i++;
					$err = $lang["matches"]["only_full_match"];
				}elseif(intval($match_arr["percent_arr"][$row["id"]]) >= 50){
					if($perfect_match_type == 1)	break;		//// we already have list of 100%-matches
					$perfect_match_type = 2;
					$_SESSION["id_arr"][$i] = $row["id"];
					$_SESSION["percent_arr"][$row["id"]] = $match_arr["percent_arr"][$row["id"]];
					if(strlen($row["icon_path"])){
						$_SESSION["with_arr"][$i] = $row["id"];
					}else{
						$_SESSION["without_arr"][$i] = $row["id"];
					}
					if(intval($row["session"])){
						$_SESSION["online_arr"][$i] = $row["id"];
					}else{
						$_SESSION["offline_arr"][$i] = $row["id"];
					}
					$i++;
				}else{
					break;				///// becouse sort by coll_spr desc
				}
				$rs->MoveNext();
			}
		}elseif($use_session != 0 && count($id_arr)>0){
			$perfect_match_type = 1;
		}
		if($perfect_match_type == 2){
			if ($par != 'full') {
				if(!$err)$err = $lang["err"]["no_matches_in_perfect"];
			}
		}elseif($perfect_match_type == 0){
			$smarty->assign("empty", "1");
		}

		///// if count of matches ==0 then trying to find most close result
		switch($filter){
			case "all": $id_arr = isset($_SESSION["id_arr"])?$_SESSION["id_arr"]:array(); break;
			case "photo": $id_arr = isset($_SESSION["with_arr"])?$_SESSION["with_arr"]:array(); break;
			case "online": $id_arr = isset($_SESSION["online_arr"])?$_SESSION["online_arr"]:array(); break;
			default: $id_arr = isset($_SESSION["id_arr"])?$_SESSION["id_arr"]:array();
		}
		if ($use_session == 0 && is_array($_SESSION["percent_arr"]) && count($_SESSION["percent_arr"])>0) {
			arsort($_SESSION["percent_arr"]);
			$temp_id_arr = array();
			foreach ($_SESSION["percent_arr"] as $temp_id => $temp_percent) {
				if (is_array($id_arr) && in_array($temp_id, $id_arr) && !in_array($temp_id, $temp_id_arr)) {
					$temp_id_arr[] = $temp_id;
				}
			}
			unset($id_arr);
			$_SESSION["id_arr"] = $id_arr = $temp_id_arr;
		}

		$num_records = count($id_arr);

		if ($num_records > 0) {
			/// pages
			$numpage = ($view == "gallery")?$config_index["search_gallery_numpage"]:$config_index["search_numpage"];
			$pages_arr = array();
			$lim_min = ($page-1)*$numpage;
			$lim_max = min($numpage, ($num_records-$lim_min) );
			$pages_arr = array_slice($id_arr, $lim_min, $lim_max);
			$user_str = implode(", ", $pages_arr);
			if(strlen($user_str)>0){
				$i = 0;
				$search = array();
				$_LANG_NEED_ID = array();
				foreach ($pages_arr as $v) {
					$strSQL = "Select distinct a.id, a.fname, a.phone, SUBSTRING(a.comment,1, 165) as comment, a.gender, a.date_birthday, a.id_country, a.id_city, a.id_region, DATE_FORMAT(a.date_last_seen,'".$config["date_format"]."')  as date_last_login, a.icon_path from ".USERS_TABLE." a where a.id = '".$v."' ";
					$rs = $dbconn->Execute($strSQL);
					$row = $rs->GetRowAssoc(false);
					$search[$i]["id"] = $row["id"];
					$search[$i]["number"] = ($page-1)*$config_index["search_numpage"]+($i+1);
					$search[$i]["name"] = $row["fname"];
					$search[$i]["phone"] = $row["phone"];
					$search[$i]["age"] = AgeFromBDate($row["date_birthday"]);
					$search[$i]["id_country"] = intval($row["id_country"]);
					$search[$i]["id_region"] = intval($row["id_region"]);
					$search[$i]["id_city"] = intval($row["id_city"]);

					$_LANG_NEED_ID["country"][] = intval($row["id_country"]);
					$_LANG_NEED_ID["region"][] = intval($row["id_region"]);
					$_LANG_NEED_ID["city"][] = intval($row["id_city"]);

					$search[$i]["profile_link"] = "viewprofile.php?id=".$row["id"]."&search_type=p"."&page=".$page;

					$icon_path = $row["icon_path"]?$row["icon_path"]:$default_photos[$row["gender"]];
					if($icon_path && file_exists($config["site_path"].$settings["icons_folder"]."/".$icon_path))
						$search[$i]["icon_path"] = $config["site_root"].$settings["icons_folder"]."/".$icon_path;
					$img_icon = $row["icon_path"]?1:0;

					$strSQL = "select count(distinct f.upload_path) as photo_count  from ".USER_UPLOAD_TABLE." f where f.id_user = ".$row["id"]." and f.upload_type='f' and f.status='1' and f.allow in ('1', '2')";
					$rs_photo = $dbconn->Execute($strSQL);
						$search[$i]["photo_count"] = $rs_photo->fields[0] + $img_icon;

					if($view != "gallery"){
						$search[$i]["annonce"] = stripslashes($row["comment"]);
						$search[$i]["completion"] = $profile_percent->GetAllPercentForUser($row["id"]);
						$search[$i]["last_login"] = $row["date_last_login"];

						/// get groups
						$sub_strSQL = "Select a.name from ".USER_GROUP_TABLE." b left join ".GROUPS_TABLE." a on a.id=b.id_group where b.id_user='".$row["id"]."'";
						$sub_rs = $dbconn->Execute($sub_strSQL);
						$groups_arr = array();
						while(!$sub_rs->EOF){
							array_push($groups_arr, $sub_rs->fields[0]);
							$sub_rs->MoveNext();
						}
						if(is_array($groups_arr) && count($groups_arr)>0)
							$search[$i]["group"] = implode(",", $groups_arr);

						/// get status
						$sub_rs = $dbconn->Execute('SELECT COUNT(*) FROM '.ACTIVE_SESSIONS_TABLE.' WHERE id_user = ?', array($row['id']));
						$search[$i]['status'] = intval($sub_rs->fields[0]) ? $lang['status']['on'] : $lang['status']['off'];

						/// get user search params
						$sub_strSQL = "SELECT gender as gender_search, age_max, age_min FROM ".USER_MATCH_TABLE." WHERE id_user='".$row["id"]."' ";
						$sub_rs = $dbconn->Execute($sub_strSQL);
						$sub_row = $sub_rs->GetRowAssoc(false);
						$search[$i]["age_max"] = $sub_row["age_max"];
						$search[$i]["age_min"] = $sub_row["age_min"];
						$search[$i]["gender_search"] = $lang["gender_search"][$sub_row["gender_search"]];

						$search[$i]["percent"] = intval($_SESSION["percent_arr"][$row["id"]]);

						//links
						$search[$i]["email_link"] = "mailbox.php?sel=fs&id=".$row["id"]."&search_type=p";
						$search[$i]["sendfriend_link"] = "./send_friend.php?sel=send&id_user=".$row["id"];
						$search[$i]["kiss_link"] = $settings["use_kiss_types"] ? "./send_kiss.php?sel=send&id_user=".$row["id"] : $file_name."?sel=kiss&id=".$row["id"]."&page=".$page;
						$search[$i]["gift_link"] = "./giftshop.php?sel=users_add&id_user=".$row["id"];

						//// get add_friend link
						$sub_strSQL = "SELECT count(*) as id_friend FROM ".HOTLIST_TABLE." WHERE id_friend='".$row["id"]."' and id_user='".$user[ AUTH_ID_USER ]."'";
						$sub_rs = $dbconn->Execute($sub_strSQL);
						$search[$i]["hotlisted"] = $sub_rs->fields[0];

						//// get blacklist_link link
						$sub_strSQL = "SELECT count(*) as id_enemy FROM ".BLACKLIST_TABLE." WHERE id_enemy='".$row["id"]."' and id_user='".$user[ AUTH_ID_USER ]."'";
						$sub_rs = $dbconn->Execute($sub_strSQL);
						$search[$i]["blacklisted"] = $sub_rs->fields[0];

						if (($search[$i]["hotlisted"] == 0) && ($search[$i]["blacklisted"] == 0)) {
							$search[$i]["addfriend_link"] = $settings["use_friend_types"] ? "./hotlist.php?sel=addform&id=".$row["id"] : "./".$file_name."?sel=addlist&id=".$row["id"]."&page=".$page;
							$search[$i]["blacklist_link"] = "./".$file_name."?sel=blacklist&id=".$row["id"]."&page=".$page;
						}
						if ($config["voipcall_feature"]){
							$search[$i]["call_link"] = "./voip_call.php?sel=rate&id_user=".$search[$i]["id"]; 
						}
					}

					$i++;
				}
				$param = $file_name."?filter=".$filter."&view=".$view."&";
				$form["pages_count"] = ceil($num_records/$numpage);
				$smarty->assign("links", GetLinkArray($num_records,$page,$param,$numpage));
				$smarty->assign("search_res", $search);
			}else{
				$smarty->assign("empty", "1");
			}
		}else{
			$smarty->assign("empty", "1");
		}
	}
	$form["err"] = $err;
	$form["guest_user"] = $user[ AUTH_GUEST ];

	$form["online_count"] = isset($_SESSION["online_arr"])?count($_SESSION["online_arr"]):0;
	$form["offline_count"] = isset($_SESSION["offline_arr"])?count($_SESSION["offline_arr"]):0;
	$form["with_count"] = isset($_SESSION["with_arr"])?count($_SESSION["with_arr"]):0;
	$form["without_count"] = isset($_SESSION["without_arr"])?count($_SESSION["without_arr"]):0;

	$form["view_online_link"] = "./".$file_name."?page=1&filter=online&view=".$view;
	$form["view_photo_link"] = "./".$file_name."?page=1&filter=photo&view=".$view;
	$form["view_all_link"] = "./".$file_name."?page=1&filter=all&view=".$view;
	$form["view_gallery_link"] = "./".$file_name."?page=1&filter=".$filter."&view=gallery";
	$form["view_list_link"] = "./".$file_name."?page=1&filter=".$filter;

	$form["show_users_connection_str"] = $settings["show_users_connection_str"];
	$form["show_users_comments"] = $settings["show_users_comments"];
	$form["show_users_group_str"] = $settings["show_users_group_str"];
	$form["use_kiss_types"] = $settings["use_kiss_types"];
	$form["use_friend_types"] = $settings["use_friend_types"];

	$form["filter"] = $filter;
	$form["view"] = $view;
	$form["perfect_match_type"] = $perfect_match_type;

	$smarty->assign("form", $form);

	if (isset($_LANG_NEED_ID) && count($_LANG_NEED_ID)) {
		$smarty->assign("base_lang", GetBaseLang($_LANG_NEED_ID));
	}
	$smarty->assign("section", $lang["subsection"]);
	$smarty->assign("header", $lang["homepage"]);
	$smarty->assign("header_s", $lang["matches"]);
	$smarty->assign("err", $lang["err"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/perfect_match_table.tpl");
	exit;

}
?>