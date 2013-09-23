<?php
/**
* VP Site user administration (Select user for promotional mail)
**/

include "../include/config.php";
include_once "../common.php";
include "../include/config_admin.php";
include "../include/functions_auth.php";
include "../include/functions_admin.php";
include "../include/class.phpmailer.php";
include "../include/functions_mail.php";
include "../include/functions_newsletter.php";
include "../include/class.lang.php";
include "../include/class.percent.php";
include "../include/config_index.php";
include "../include/functions_users.php";

$auth = auth_user();
login_check($auth);
//IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "users");

$sel = isset($_REQUEST['sel']) ? $_REQUEST['sel'] : '';

if (isset($_REQUEST["no_invite"])) unset($_SESSION["invite_users"]);

switch ($sel) {
	//case "active":			UpdateStatus(); break;
	default:				ListUser();
}

exit;


function ListUser($err='')
{
	global $smarty, $dbconn, $config, $config_admin, $lang, $page, $sel;
	
	$file_name = "admin_promotions_users.php";

	//AdminMainMenu($lang["users"]);
	AdminMainMenu($lang["promotions"], "1");
	$id = intval($_GET["id"]);

	$page = (isset($_REQUEST['page']) && intval($_REQUEST['page'])>0) ? intval($_REQUEST['page']) : 1;
	$letter = (isset($_REQUEST["letter"]) && intval($_REQUEST["letter"])>0) ? intval($_REQUEST["letter"]) : "*";
	$sorter = (isset($_REQUEST['sorter']) && intval($_REQUEST['sorter'])>0) ? intval($_REQUEST['sorter']) : 5;
	$s_type = (isset($_REQUEST['s_type']) && intval($_REQUEST['s_type'])>0) ? intval($_REQUEST['s_type']) : 1;
	$order = (isset($_REQUEST['order']) && intval($_REQUEST['order'])>0) ? intval($_REQUEST['order']) : 1;

	$search = (isset($_REQUEST['search'])) ? strval($_REQUEST['search']) : "";
	$s_stat = (isset($_REQUEST['s_stat'])) ? strval($_REQUEST['s_stat']) : "";
	$group = (isset($_REQUEST['group'])) ? strval($_REQUEST['group']) : "";
	$s_gender = (isset($_REQUEST['s_gender'])) ? strval($_REQUEST['s_gender']) : "";
	
	$sel = isset($_REQUEST["sel"]) ? $_REQUEST["sel"] : "";
	$pre_sel = isset($_REQUEST["pre_sel"]) ? $_REQUEST["pre_sel"] : $sel;
	$s_pending = isset($_REQUEST["s_pending"]) ? $_REQUEST["s_pending"] : "";
	$plat_applied = isset($_REQUEST["plat_applied"]) ? $_REQUEST["plat_applied"] : "";
	
	$search_str = "";

	if (strval($search)) {
		$search = strip_tags($search);
		switch ($s_type) {
			case 1: $search_str=" and a.login like '%".$search."%'"; break;
			case 2: $search_str=" and a.fname like '%".$search."%'"; break;
			case 3: $search_str=" and a.sname like '%".$search."%'"; break;
			case 4: $search_str=" and a.email like '%".$search."%'"; break;
		}
	}

	if (strval($s_stat)) {
		switch($s_stat) {
			case "online":
				$strSQL =
					'SELECT DISTINCT u.id
					   FROM '.USERS_TABLE.' u
				 INNER JOIN '.ACTIVE_SESSIONS_TABLE.' s ON u.id = s.id_user
					  WHERE u.root_user != "1" AND u.guest_user != "1"';
				$rs = $dbconn->Execute($strSQL);
				$user_arr = array();
				$i = 0;
				while (!$rs->EOF) {
					$user_arr[$i] = $rs->fields[0];
					$i++;
					$rs->MoveNext();
				}
				if (count($user_arr)) {
					$user_str = implode(", ", $user_arr);
				} else {
					$user_str = "''";
				}
				$search_str .= " and a.id in (".$user_str.")";
			break;
			case "reg_today":
				$search_str .= " and (a.date_registration + INTERVAL 1 DAY)>NOW()";
			break;
			case "reg_week":
				$search_str .= " and (a.date_registration + INTERVAL 7 DAY)>NOW()";
			break;
			case "reg_month":
				$search_str .= " and (a.date_registration + INTERVAL 30 DAY)>NOW()";
			break;
			case "chat":
				$rs = $dbconn->Execute("select distinct userid from ".F_CHAT_CONNECTIONS_TABLE." where userid>'0'");
				$user_arr = array();
				$i = 0;
				while (!$rs->EOF) {
					$row = $rs->GetRowAssoc(false);
					$user_arr[$i] = $row["userid"];
					$i++;
					$rs->MoveNext();
				}
				if (count($user_arr)) {
					$user_str = implode(", ", $user_arr);
				} else {
					$user_str = "''";
				}
				$search_str .= " and a.id in (".$user_str.")";
			break;
		}
	}
	
	$smarty->assign("s_stat", $s_stat);

	///////// groups search
	if (intval($group)) {
		$rs = $dbconn->Execute("SELECT DISTINCT id_user FROM ".USER_GROUP_TABLE." WHERE id_group='".intval($group)."'");
		$user_arr = array();
		$i = 0;
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$user_arr[$i] = $row["id_user"];
			$i++;
			$rs->MoveNext();
		}
		if (count($user_arr)) {
			$user_str = implode(", ", $user_arr);
		} else {
			$user_str = "''";
		}
		$search_str .= " and a.id in (".$user_str.")";
	}
	$smarty->assign("group", $group);
	
	///////// gender search
	if (intval($s_gender)) {
		$rs = $dbconn->Execute("SELECT DISTINCT id FROM ".USERS_TABLE." WHERE gender='".intval($s_gender)."'");
		$user_arr = array();
		$i = 0;
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$user_arr[$i] = $row["id"];
			$i++;
			$rs->MoveNext();
		}
		if (count($user_arr)) {
			$user_str = implode(", ", $user_arr);
		} else {
			$user_str = "''";
		}
		$search_str .= " and a.id in (".$user_str.")";
	}
	
	$smarty->assign("gender", $s_gender);
	
	////////////////
	/*
	$strSQL = "	SELECT DISTINCT a.id, a.name, COUNT(b.id) AS count FROM ".GROUPS_TABLE." a
				LEFT JOIN ".USER_GROUP_TABLE." b ON b.id_group = a.id
				WHERE (a.is_gender_group='".$config_admin['gender_membership']."' || a.type='d' || a.type='g' || a.type='r' || a.type='m')
				GROUP BY a.id ORDER BY a.name";
	*/
	
	$strSQL = "	SELECT DISTINCT a.id, a.name, COUNT(b.id) AS count FROM ".GROUPS_TABLE." a
					LEFT JOIN ".USER_GROUP_TABLE." b ON b.id_group = a.id
					WHERE (a.is_gender_group = '1')
					GROUP BY a.id ORDER BY a.name";
	$rs = $dbconn->Execute($strSQL);

	$groups = array();
	$i = 0;
	while (!$rs->EOF) {
		$groups[$i]["id"] = $rs->fields[0];
		$groups[$i]["name"] = stripslashes($rs->fields[1]);
		$groups[$i]["count"] = $rs->fields[2];
		$i++;
		$rs->MoveNext();
	}
	
	$smarty->assign("groups", $groups);
	
	// search form
	$types = array();
	for ($i = 0; $i < 4; $i++) {
		if ($s_type == ($i+1)) {
			$types[$i]["sel"] = "1";
		}
		$types[$i]["value"] = $lang["users"]["type_".($i+1)];
	}
	$smarty->assign("types", $types);
	$smarty->assign("search", $search);

	// letter
	if (strval($letter) != "*") {
		$letter_str = " lower(substring(a.login,1,1)) ='".strtolower(chr($letter))."'";
	} else {
		$letter_str = "";
	}
	$smarty->assign("letter", $letter);

	$form['order'] = $order;
	
	if (intval($sorter)) {
		$sorter_str = " order by";
		switch ($sorter) {
			case "1": $sorter_str.=" a.login"; break;
			case "2": $sorter_str.=" a.fname"; break;
			case "3": $sorter_str.=" a.gender";	break;
			case "4": $sorter_str.=" a.date_birthday"; break;
			case "5": $sorter_str.=" a.date_registration"; break;
			case "6": $sorter_str.=" a.date_last_seen"; break;
			case "7": $sorter_str.=" a.status"; break;
			case "8": $sorter_str.=" a.login_count"; break;
		}
		switch ($order) {
			case '1':
				$form['new_order'] = '2';
				$sorter_str .=" ASC ";
			break;
			case '2':
				$form['new_order'] = '1';
				$sorter_str .=" DESC ";
			break;
			default:
				$form['new_order'] = '2';
				$sorter_str .=" ASC ";
			break;
		}
	} else {
		$sorter_str = "";
	}
	$smarty->assign("sorter", $sorter);
	
	$status_str='';
	if ($s_pending!='') {
		$status_str = " a.status = '$s_pending'";
	}
	
	if ($search_str)
	{
		$where_str = "where a.id>0 ".$search_str." ";
		
		if ($status_str) {
			$where_str .= " and ".$status_str." ";
		}
		
		if ($plat_applied !== "") {
			if ($plat_applied > 0) {
				$where_str .= " AND a.mm_platinum_applied IS NOT NULL ";
			} else {
				$where_str .= " AND a.mm_platinum_applied IS NULL ";
			}
		}
		if ($letter_str) {
			$where_str .= "and ".$letter_str." ";
		}
	}
	else
	{
		if ($letter_str) {
			$where_str = "where ".$letter_str." ";
		} else {
			$where_str = "where (a.guest_user = '0' AND a.root_user = '0') ";
		}
	}
	
	//skipping admin and guest users
	/*
	if ($where_str == "") {
		$where_str = " WHERE a.root_user < 1 AND a.guest_user < 1";
	} else {
		$where_str .= " and a.root_user = '0' AND a.guest_user = '0'";
	}
	*/
	
	$rs = $dbconn->Execute("SELECT count(*) from ".USERS_TABLE." a ".$where_str);
	$num_records = $rs->fields[0];

	///////// page
	$lim_min = ($page-1)*$config_admin["users_numpage"];
	$lim_max = $config_admin["users_numpage"];
	$limit_str = " limit ".$lim_min.", ".$lim_max;
	$smarty->assign("page", $page);

	/////////
	if (isset($_SESSION["id_club"]) && $_SESSION["invite_users"])
	{
		$strSQL = "SELECT id_user FROM ".CLUB_USERS_TABLE." WHERE id_club='".intval($_SESSION["id_club"])."' GROUP BY id_user";
		$rs = $dbconn->Execute($strSQL);
		while (!$rs->EOF) {
			$users_not_invite[] = $rs->fields[0];
			$rs->MoveNext();
		}
		$strSQL = "SELECT id_user FROM ".CLUB_INVITES_TABLE." WHERE id_club='".intval($_SESSION["id_club"])."' GROUP BY id_user";
		$rs = $dbconn->Execute($strSQL);
		while (!$rs->EOF) {
			$users_not_invite[] = $rs->fields[0];
			$rs->MoveNext();
		}
		$users_not_invite = array_unique($users_not_invite);
	}

	// query
	$strSQL =
		"SELECT a.id, a.fname, a.sname, a.status, a.login, a.gender, a.email, a.date_birthday, a.icon_path,
				DATE_FORMAT(a.date_last_seen, '".$config["date_format"]."') AS date_last_seen,
				DATE_FORMAT(a.date_registration, '".$config["date_format"]."') AS date_registration,
				a.login_count, a.root_user, a.guest_user
		   FROM ".USERS_TABLE." a ".$where_str." ".$sorter_str.$limit_str;
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	
	if ($rs->RowCount() > 0) {
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$user[$i]["number"]			= ($page-1)*$config_admin["users_numpage"]+($i+1);
			$user[$i]["id"]				= $row["id"];
			$user[$i]["name"]			= stripslashes($row["fname"]." ".$row["sname"]);
			$user[$i]["nick"]			= $row["login"];
			$user[$i]["gender"]			= $lang["gender"][$row["gender"]];
			$user[$i]["status"]			= intval($row["status"]);
			$user[$i]["email"]			= $row["email"];
			$user[$i]["age"]			= AgeFromBDate($row["date_birthday"]);
			$user[$i]["login_count"]	= intval($row["login_count"]);
			//$user[$i]["date_rigistration"] = $row["date_registration"];
			//$user[$i]["last_login"] = $row["date_last_seen"];
			$user[$i]["profile_link"]	= $config["server"].$config["site_root"]."/viewprofile.php?id=".$rs->fields[0];
			//$user[$i]["profile_link"] = "admin_users.php?sel=edit&id=".$row["id"];
			
			//$user[$i]["edit_link"]	= $file_name."?sel=edit&pre_sel=".$pre_sel."&page=".$page."&id=".$row["id"]."&letter=".$letter."&search=".$search."&s_type=".$s_type."&sorter=".$sorter;
			//$user[$i]["delete_link"]	= $file_name."?sel=del&page=".$page."&id=".$row["id"]."&letter=".$letter."&search=".$search."&s_type=".$s_type."&sorter=".$sorter;
			//$user[$i]["descr_link"]	= "./admin_user_description.php?id=".$row["id"];
			//$user[$i]["personal_link"]= "./admin_user_personality.php?id=".$row["id"];
			//$user[$i]["upload_link"]	= "./admin_user_upload.php?id=".$row["id"];
			//$user[$i]["perfect_link"]	= "./admin_user_perfect.php?id=".$row["id"];
			//$user[$i]["comunicate"]	= "./admin_comunicate.php?id=".$row["id"];
			$user[$i]["root_user"]		= $row["root_user"] ? $row["root_user"] : $row["guest_user"];
			$user[$i]["confirm"]		= addslashes($lang["confirm"]["users"]);
			$user[$i]["guest_user"]		= $row["guest_user"];
			
			$user[$i]["icon_path"]		= $row["icon_path"];
			
			$icon_path = $user[$i]["icon_path"] ? $user[$i]["icon_path"] : $default_photos[$user[$i]["gender"]];
			
			$user[$i]["icon_path"]		= $config["server"].$config["site_root"]."/uploades/icons/".$icon_path;
			
			/*
			$strSQL = "SELECT count(id) FROM ".USER_REFER_TABLE." WHERE id_refer='".$user[$i]["id"]."'";
			$user[$i]["count_invited"] = $dbconn->GetOne($strSQL);
			if ($user[$i]["count_invited"] > 0) {
				$user[$i]["invited_link"] = $config["server"].$config["site_root"]."/admin/admin_pays.php?sel=user&filter=referred&id=".$user[$i]["id"];
				$user[$i]["invited_link_name"] = str_replace("[n]",$user[$i]["count_invited"],$lang["refer_friend"]["invited_link_name"]);
			}
			*/
			//VP fetching id_group and use_active status
			$strSQL = "SELECT id_group FROM ".USER_GROUP_TABLE." WHERE id_user='".$user[$i]["id"]."'";
			$user[$i]["id_group"] = $dbconn->GetOne($strSQL);
			if ($user[$i]["id_group"] > 0) {
				$user[$i]["use_active"] = ($user[$i]["id_group"]==5 || $user[$i]["id_group"]==6) ? false : true;
			}
			if (isset($_SESSION["id_club"]) && $_SESSION["invite_users"] && !in_array($user[$i]["id"], $users_not_invite) && !$user[$i]["guest_user"]) {
				$user[$i]["invite_link"] = "admin_club.php?sel=invite&id_user=".$user[$i]["id"]."&id_club=".$_SESSION["id_club"];
			}
			$rs->MoveNext();
			$i++;
		}
		$param = $file_name."?sel=".$sel."&letter=".$letter."&search=".$search."&s_type=".$s_type."&s_stat=".$s_stat."&sorter=".$sorter."&group=".$group."&order=".$form['order']."&s_gender=".$s_gender."&";
		$smarty->assign("links", GetLinkStr($num_records,$page,$param,$config_admin["users_numpage"]));
		$smarty->assign("user", $user);
	}
	// letter link
	$param_letter = $file_name."?sel=".$sel."&sorter=".$sorter."&order=".$form['order']."&letter=";
	$letter_links = LetersLink_eng($param_letter, $letter);
	$smarty->assign("letter_links", $letter_links);

	$form["hiddens"] = "<input type=hidden name=sel value=".$sel.">";
	$form["hiddens"] .= "<input type=hidden name=pre_sel value=".$pre_sel.">";
	$form["hiddens"] .= "<input type=hidden name=page value=".$page.">";
	$form["hiddens"] .= "<input type=hidden name=letter value=".$letter.">";
	$form["hiddens"] .= "<input type=hidden name=search value=".$search.">";
	$form["hiddens"] .= "<input type=hidden name=s_type value=".$s_type.">";
	$form["hiddens"] .= "<input type=hidden name=s_stat value=".$s_stat.">";
	$form["hiddens"] .= "<input type=hidden name=sorter value=".$sorter.">";
	$form["hiddens"] .= "<input type=hidden name=group value=".$group.">";
	$form["hiddens"] .= "<input type=hidden name=order value=".$order.">";
	
	$form["hiddens"] .= "<input type=hidden name=sel_user id=sel_user >";

	$form["action"] = $file_name;
	
	$form["err"] = $err;

	//$smarty->assign("add_link", $file_name."?sel=add&page=".$page);
	//$smarty->assign("topten_link", $file_name."?sel=top");
	$smarty->assign("header", $lang["users"]);
	$smarty->assign("button", $lang["button"]);
	$smarty->assign("form", $form);
	
	if (isset($_SESSION["id_club"])) {
		$smarty->assign("invite_users",$_SESSION["invite_users"]);
	}
	
	$smarty->assign("sel", $sel);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_promotions_user_table.tpl");
	exit;
}
?>