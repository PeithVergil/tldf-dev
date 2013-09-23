<?php
/**
* User want-to-meet-me management, add to hot and black list
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
include './include/class.phpmailer.php';
include './include/functions_mail.php';

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
$smarty->assign('sub_menu_num', '');

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
			header('Location: meet_me.php?par=send');
			exit;
		} else {
			$_GET['par'] = 'send';
			SearchTable($res['err']);
		}
	break;
	
	case 'addconnection':
		$res = AddToConnections();
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			header('Location: meet_me.php?par=send');
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
			header('Location: meet_me.php?par=send');
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
			header('Location: meet_me.php?par=send');
			exit;
		} else {
			$_GET['par'] = 'send';
			SearchTable($res['err']);
		}
	break;
	
	default:
		SearchTable();
	break;
}

exit;


function SearchTable($err="")
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	/////// our profile ( descr table) comply the perfect match requirements of  other users
	/////// get spr and info(id_values) arrays from descr_user_table
	/////// and get id_user from descr_match_table where such array consist our arrays
	$file_name = "meet_me.php";
	
	if (isset($_SESSION['err'])) {
		$err = $_SESSION['err'];
		unset($_SESSION['err']);
	}
	
	$page = isset($_REQUEST["page"])?$_REQUEST["page"]:0;
	/////////////////////////////////////////////////////////////////////////////////////

	$use_session = 0;

	if(intval($page)>0 && isset($_SESSION["id_arr"]) ){
		$id_arr = $_SESSION["id_arr"];
		$use_session = 1;		//// use a session id array
	}

	if (!isset($_GET["par"])) {
		unset($_SESSION["search_page"]);
	}
	if ((strval($page) == "") || (strval($page) == "0")) {
		$page = (isset($_SESSION["search_page"])) ? $_SESSION["search_page"] : 1;
	} else {
		$page = intval($page);
		$_SESSION["search_page"] = $page;
	}
	
	// settings
	$settings = GetSiteSettings(array('icon_male_default','icon_female_default','icons_folder','show_users_connection_str','icons_folder','show_users_connection_str','show_users_comments','show_users_group_str','use_kiss_types', 'thumb_max_width', 'use_friend_types'));
	$smarty->assign("icon_width", $settings["thumb_max_width"]);

	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];

	$profile_percent = new Percent($user[ AUTH_ID_USER ]);
	
	$user_perc = $profile_percent->GetSectionPercent(1);
	$descr_perc = $profile_percent->GetSectionPercent(2);

	////// $descr_perc ~ 70%, $interests_perc ~ 30%
	/// this is avg percent if  $summ_all< 75 user must fill his perfect mach section
	$summ_all = round($descr_perc*0.55 + $user_perc*0.45);

	if($summ_all < 75){
		$form["not_fill"] = str_replace("[percent]", $summ_all,$lang["err"]["back_to_perfect_match"]);
		$smarty->assign("empty", "2");
		$form["profile_link"] = "./myprofile.php?sel=1";
	}else{
		///////////////////////////////////////////////////////////////////////////////////////
		if(isset($_GET["par"]) && ($_GET["par"]=="back")){	////if user clicked on back to list button
			if(isset($_SESSION["id_arr"])){
				$id_arr = $_SESSION["id_arr"];
				$use_session = 1;		//// use a session id array
			}else{
				$use_session = 0;		//// use a session data array
			}
		}

		if ($use_session == 0) {
			unset($_SESSION["id_arr"]);	// unset session data
			$match_arr = GetWantToMeetMeList($user[ AUTH_ID_USER ], true);
			$_SESSION["id_arr"] = count($match_arr)?$match_arr["id_arr"]:array();
			$id_arr = $_SESSION["id_arr"];
		}
		$num_records = count($id_arr);

		if ($num_records > 0) {
			/// pages
			$lim_min = ($page-1) * $config_index["search_numpage"];
			$lim_max = min($config_index["search_numpage"], ($num_records-$lim_min) );
			$pages_arr = array_slice($id_arr, $lim_min, $lim_max);
			$user_str = implode(", ", $pages_arr);
			if(strlen($user_str)>0)
			$strSQL = "Select distinct a.id, a.fname, a.phone, SUBSTRING(a.comment,1, 165) as comment, a.gender, a.date_birthday, a.id_country, a.id_city, a.id_region, DATE_FORMAT(a.date_last_seen,'".$config["date_format"]."')  as date_last_login, a.icon_path from ".USERS_TABLE." a WHERE a.id IN (".$user_str.") ORDER BY a.date_registration desc ";
			else
			$strSQL = "";
			if(strlen($strSQL)>0){
				$rs = $dbconn->Execute($strSQL);
				$i = 0;
				$search = array();
				$_LANG_NEED_ID = array();
				while (!$rs->EOF) {
					$row = $rs->GetRowAssoc(false);
					$search[$i]['id']			= $row['id'];
					$search[$i]['number']		= ($page-1) * $config_index['search_numpage'] + ($i+1);
					$search[$i]['name']			= stripslashes($row['fname']);
					$search[$i]['gender']		= (int) $row['gender'];
					$search[$i]['phone']		= $row['phone'];
					$search[$i]['annonce']		= stripslashes($row['comment']);
					$search[$i]['age']			= AgeFromBDate($row['date_birthday']);
					$search[$i]['id_country']	= intval($row['id_country']);
					$search[$i]['id_region']	= intval($row['id_region']);
					$search[$i]['id_city']		= intval($row['id_city']);
					$search[$i]['completion']	= $profile_percent->GetAllPercentForUser($row['id']);
					$search[$i]['last_login']	= $row['date_last_login'];
					
					//SH is_verified
					$verified = $dbconn->GetOne('SELECT platinum_verified FROM '.USERS_TABLE.' WHERE id = ?', array($row['id']));
					$search[$i]["is_verified"] = empty($verified) ? false : true;
					
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

					$_LANG_NEED_ID["country"][] = intval($row["id_country"]);
					$_LANG_NEED_ID["region"][] = intval($row["id_region"]);
					$_LANG_NEED_ID["city"][] = intval($row["id_city"]);

					//links
					$search[$i]["email_link"] = "mailbox.php?sel=fs&id=".$row["id"]."&search_type=mm";
					$search[$i]["profile_link"] = "viewprofile.php?id=".$row["id"]."&search_type=mm"."&page=".$page;
					$search[$i]["sendfriend_link"] = "./send_friend.php?sel=send&id_user=".$row["id"];
					$search[$i]["kiss_link"] = $settings["use_kiss_types"] ? "./send_kiss.php?sel=send&id_user=".$row["id"] : $file_name."?sel=kiss&id=".$row["id"]."&page=".$page;
					$search[$i]["gift_link"] = "./giftshop.php?sel=users_add&id_user=".$row["id"];
					$search[$i]['ecard_link'] = './ecards.php?id_user_to='.$row['id'].'&amp;fixuser=Y';
					
					// check hotlisted
					$sub_rs = $dbconn->Execute(
						'SELECT COUNT(*)
						   FROM '.HOTLIST_TABLE.'
						  WHERE id_friend = "'.$row['id'].'" AND id_user = "'.$user[ AUTH_ID_USER ].'"');
					
					$search[$i]['hotlisted'] = !empty($sub_rs->fields[0]) ? 1 : 0;
					
					// check blacklisted
					$sub_rs = $dbconn->Execute('
						SELECT COUNT(*)
						  FROM '.BLACKLIST_TABLE.'
						 WHERE id_enemy = "'.$row['id'].'" AND id_user = "'.$user[ AUTH_ID_USER ].'"');
					
					$search[$i]['blacklisted'] = !empty($sub_rs->fields[0]) ? 1 : 0;
					
					// check connection status
					$search[$i]['connected_status'] = getConnectedStatus($row['id'], $user[ AUTH_ID_USER ]);
					
					// add hotlist link
					if ($search[$i]['hotlisted'] == 0 && $search[$i]['blacklisted'] == 0) {
						if ($settings['use_friend_types']) {
							$search[$i]['add_hotlist_link'] = './hotlist.php?sel=addform&amp;id='.$row['id'];
						} else {
							$search[$i]['add_hotlist_link'] = './meet_me.php?sel=addlist&amp;id='.$row['id'].'&amp;page='.$page;
						}
					}
					
					// blacklist link
					if ($search[$i]['hotlisted'] == 0 && $search[$i]['connected_status'] != CS_CONNECTED && $search[$i]['blacklisted'] == 0) {
						$search[$i]['add_blacklist_link'] = './meet_me.php?sel=blacklist&amp;id='.$row['id'].'&amp;page='.$page;
					}
					
					// connections link
					if ($search[$i]['connected_status'] == CS_NOTHING && $search[$i]['blacklisted'] == 0) {
						if ($settings['use_friend_types']) {
							$search[$i]['add_connection_link'] = './connections.php?sel=addform&amp;id='.$row['id'];
						} else {
							$search[$i]['add_connection_link'] = './meet_me.php?sel=addconnection&amp;id='.$row['id'].'&amp;page='.$page;
						}
					}

					$icon_path = $row["icon_path"]?$row["icon_path"]:$default_photos[$row["gender"]];
					if($icon_path && file_exists($config["site_path"].$settings["icons_folder"]."/".$icon_path)) {
						$search[$i]["icon_path"] = $config["site_root"].$settings["icons_folder"]."/".$icon_path;
						//SH big icon image fetched
						$search[$i]["big_icon_path"] = $config["site_root"].$settings["icons_folder"]."/big_".$icon_path;
					}
					$img_icon = $row["icon_path"] ? 1 : 0;

					$strSQL = "select count(distinct f.upload_path) as photo_count  from ".USER_UPLOAD_TABLE." f where f.id_user = ".$row["id"]." and f.upload_type='f' and f.status='1' and f.allow in ('1', '2')";
					$rs_photo = $dbconn->Execute($strSQL);
					$search[$i]["photo_count"] = $rs_photo->fields[0] + $img_icon;
					if ($config["voipcall_feature"]){
						$search[$i]["call_link"] = "./voip_call.php?sel=rate&id_user=".$search[$i]["id"]; 
					}

					$rs->MoveNext();
					$i++;
				}
				$param = $file_name."?";
				$form["pages_count"] = ceil($num_records/$config_index["search_numpage"]);
				$smarty->assign("links", GetLinkArray($num_records,$page,$param,$config_index["search_numpage"]));
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
	$smarty->assign("user_gender", $user[ AUTH_GENDER ]);

	$form["show_users_connection_str"] = $settings["show_users_connection_str"];
	$form["show_users_comments"] = $settings["show_users_comments"];
	$form["show_users_group_str"] = $settings["show_users_group_str"];
	$form["use_kiss_types"] = $settings["use_kiss_types"];
	$form["use_friend_types"] = $settings["use_friend_types"];

	if (isset($_REQUEST["from"]) && $_REQUEST["from"] == 'organizer') {
		$form["back_link"] = "organizer.php";
	}

	$smarty->assign("form", $form);

	$smarty->assign("section", $lang["subsection"]);
	if (isset($_LANG_NEED_ID) && count($_LANG_NEED_ID)) {
		$smarty->assign("base_lang", GetBaseLang($_LANG_NEED_ID));
	}
	$smarty->assign("header", $lang["homepage"]);
	$smarty->assign("err", $lang["err"]);
	$smarty->assign("header_s", $lang["matches"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/meet_me_table.tpl");
	exit;
}
?>