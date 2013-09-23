<?php

/**
* Site moderators management
*
* @package DatingPro
* @subpackage Admin Mode
**/


include "../include/config.php";
include_once "../common.php";
include "../include/config_admin.php";
include "../include/functions_auth.php";
include "../include/functions_admin.php";

$auth = auth_user();
login_check($auth);
IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "groups");

$sel = $_GET["sel"]?$_GET["sel"]:$_POST["sel"];

switch($sel){
	case "list": 	ListModerators(); break;
	case "status": 	ChangeStatus(); break;
	case "add": 	EditForm("add"); break;
	case "save": 	SaveForm(); break;
	case "edit": 	EditForm("edit"); break;
	case "delete":	DeleteModerator();	break;
	default: ListModerators();
}

function ListModerators($err=""){
	global $smarty, $dbconn, $config, $config_admin, $lang, $file_name;

	if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
	$file_name = "admin_moderators.php";

	AdminMainMenu($lang["moderators"]);

	$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;

	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." u, ".GROUPS_TABLE." g, ".USER_GROUP_TABLE." ug WHERE g.type='m' AND ug.id_user=u.id AND ug.id_group=g.id ";
	$rs = $dbconn->Execute($strSQL);

	$num_records = $rs->fields[0];

	$lim_min = ($page-1)*$config_admin["users_numpage"];
	$lim_max = $config_admin["users_numpage"];
	$limit_str = " LIMIT ".$lim_min.", ".$lim_max;
	$smarty->assign("page", $page);

	$strSQL = " SELECT DISTINCT u.id, u.status, u.login, u.email, DATE_FORMAT(u.date_last_seen, '".$config["date_format"]."'), UNIX_TIMESTAMP(u.date_last_seen)
				FROM ".USERS_TABLE." u, ".GROUPS_TABLE." g, ".USER_GROUP_TABLE." ug
				WHERE g.type='m' AND ug.id_user=u.id AND ug.id_group=g.id
				GROUP BY u.id ORDER BY u.id".$limit_str;
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	if($rs->RowCount()>0){
		$user = array();
		while(!$rs->EOF){
			$user[$i]["number"] = ($page-1)*$config_admin["users_numpage"]+($i+1);
			$user[$i]["id"] = $rs->fields[0];
			$user[$i]["status"] = $rs->fields[1];
			$user[$i]["login"] = $rs->fields[2];
			$user[$i]["email"] = $rs->fields[3];
			if ($rs->fields[5] != '0') {
				$user[$i]["date_last_seen"] = $rs->fields[4];
			} else {
				$user[$i]["date_last_seen"] = $lang["moderators"]["never"];
			}
			$rs->MoveNext();
			$i++;
		}
		$param = $file_name."?sel=list&";
		$smarty->assign("links", GetLinkStr($num_records, $page, $param, $config_admin["users_numpage"]));
		$smarty->assign("user", $user);
	}
	$form["err"] = $err;
	$form["add_link"] = $config["server"].$config["site_root"]."/admin/admin_moderators.php?sel=add";
	$smarty->assign("form", $form);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_moderators_table.tpl");
	exit;
}


function ChangeStatus() {
	global $smarty, $dbconn, $config, $config_admin, $lang, $file_name;

	if (isset($_REQUEST['id_user']) && is_array($_REQUEST['id_user'])) {
		$id_user_str = implode(',',$_REQUEST['id_user']);
		$strSQL = " UPDATE ".USERS_TABLE." SET status='0' WHERE id IN (".$id_user_str.") ";
		$dbconn->Execute($strSQL);
		if (isset($_REQUEST['status']) && is_array($_REQUEST['status'])) {
			$id_status_str = implode(',',$_REQUEST['status']);
			$strSQL = " UPDATE ".USERS_TABLE." SET status='1' WHERE id IN (".$id_status_str.") ";
			$dbconn->Execute($strSQL);
		}
	}
	ListModerators($lang["errors"]["status_refreshed"]);
	return ;
}

function EditForm($par='', $err='', $data='') {
	global $smarty, $dbconn, $config, $config_admin, $lang, $file_name;

	if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
	$file_name = "admin_moderators.php";

	AdminMainMenu($lang["moderators"]);

	if ($par == 'edit') {
		if (isset($_REQUEST["id"]) && intval($_REQUEST["id"])>0) {
			$strSQL = " SELECT id, status, login, email, fname, sname FROM ".USERS_TABLE." WHERE id='".intval($_REQUEST["id"])."' ";
			$rs = $dbconn->Execute($strSQL);
			if ($rs->fields[0]>0) {
				$data["id"] = $rs->fields[0];
				$data["status"] = $rs->fields[1];
				$data["login"] = $rs->fields[2];
				$data["email"] = $rs->fields[3];
				$data["fname"] = stripslashes($rs->fields[4]);
				$data["sname"] = stripslashes($rs->fields[5]);
				$smarty->assign("data", $data);

				$strSQL = " SELECT DISTINCT id, id_user, id_module FROM ".GROUP_MODULE_USER_TABLE." WHERE id_user='".$data["id"]."' GROUP BY id ORDER BY id ";
				$rs = $dbconn->Execute($strSQL);
				if ($rs->fields[0]>0) {
					$i = 0;
					$user_rights = array();
					while(!$rs->EOF){
						$user_rights[$i] = $rs->fields[2];
						$rs->MoveNext();
						$i++;
					}
				}
			}
		}
	}
	if ($data != "") {
		$smarty->assign("data", $data);
	}
	$form["err"] = $err;
	$form["par"] = $par;

	$smarty->assign("form", $form);

	$strSQL = " SELECT DISTINCT id, name FROM ".MODULES_TABLE." WHERE name LIKE 'admin_%' GROUP BY id ORDER BY name ";
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	if($rs->RowCount()>0){
		$rights = array();
		while(!$rs->EOF){

			$rights[$i]["id"] = $rs->fields[0];
			if (isset($user_rights) && is_array($user_rights) && in_array($rights[$i]["id"], $user_rights)) {
				$rights[$i]["sel"] = 1;
			} else {
				$rights[$i]["sel"] = 0;
			}
			$rights[$i]["base_name"] = $rs->fields[1];
			$rights[$i]["name"] = (isset($lang["modules"][$rs->fields[1]]["name"]) && $lang["modules"][$rs->fields[1]]["name"]!='') ? $lang["modules"][$rs->fields[1]]["name"] : $rs->fields[1];
			$rights[$i]["comment"] = (isset($lang["modules"][$rs->fields[1]]["comment"]) && $lang["modules"][$rs->fields[1]]["comment"]!='') ? $lang["modules"][$rs->fields[1]]["comment"] : $rs->fields[1];
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign("rights", $rights);
	}
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_moderators_edit.tpl");
	exit;
}

function SaveForm() {
	global $smarty, $dbconn, $config, $config_admin, $lang, $file_name;
	if ($_REQUEST["par"] == "edit" && isset($_REQUEST["id"]) && intval($_REQUEST["id"])>0) {
		$data["id"] = intval($_REQUEST["id"]);
		$data["login"] = strip_tags(trim($_POST["login"]));
		$data["email"] = strip_tags(trim($_POST["email"]));
		$data["fname"] = addslashes(strip_tags(trim($_POST["fname"])));
		$data["sname"] = addslashes(strip_tags(trim($_POST["sname"])));
		$data["status"] = intval($_POST["status"]);

		if ($data["login"] == "" || $data["email"] == "") {
			EditForm("edit", $lang["errors"]["empty_fields"], $data);
			return ;
		}
		if (trim($_POST["pass"])  != '') {
			$data["new_pass"] = md5($_POST["pass"]);
			$pass_str = ",password='".$data["new_pass"]."'";
		} else {
			$pass_str = "";
		}
		$strSQL = " UPDATE ".USERS_TABLE." SET login='".$data["login"]."', email='".$data["email"]."', fname='".$data["fname"]."',
					sname='".$data["sname"]."', status='".$data["status"]."', root_user='1', guest_user='0', confirm='1', visible='1' ".$pass_str."
					WHERE id='".$data["id"]."' ";
		$dbconn->Execute($strSQL);
		$moderator_id = $data["id"];
	} elseif ($_REQUEST["par"] == "add") {
		$data["login"] = strip_tags(trim($_POST["login"]));
		$data["email"] = strip_tags(trim($_POST["email"]));
		$data["fname"] = addslashes(strip_tags(trim($_POST["fname"])));
		$data["sname"] = addslashes(strip_tags(trim($_POST["sname"])));
		$data["status"] = intval($_POST["status"]);
		$data["pass"] = md5($_POST["pass"]);
		if (trim($_POST["pass"])  == '' || $data["login"] == "" || $data["email"] == "") {
			EditForm("add", $lang["errors"]["empty_fields"], $data);
			return ;
		}
		$strSQL = " SELECT id FROM ".USERS_TABLE." WHERE login='".$data["login"]."' OR email='".$data["email"]."' ";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0]>0) {
			EditForm("add", $lang["errors"]["email_or_login_exists"], $data);
			return ;
		}
		$strSQL = " SELECT id FROM ".GROUPS_TABLE." WHERE type='m' ";
		$rs = $dbconn->Execute($strSQL);
		$id_group = $rs->fields[0];

		$strSQL = " INSERT INTO ".USERS_TABLE." (login, email, password, fname, sname, status, root_user, guest_user, confirm, visible) VALUES
					('".$data["login"]."', '".$data["email"]."', '".$data["pass"]."', '".$data["fname"]."', '".$data["sname"]."',  '".$data["status"]."', '1', '0', '1', '1') ";
		$dbconn->Execute($strSQL);
		$moderator_id = $dbconn->Insert_ID();
		$strSQL = "INSERT INTO ".USER_GROUP_TABLE." (id_user, id_group) VALUES ('".$moderator_id."','".$id_group."') ";
		$dbconn->Execute($strSQL);
	}

	$strSQL = " DELETE FROM ".GROUP_MODULE_USER_TABLE." WHERE id_user='".$moderator_id."' ";
	$dbconn->Execute($strSQL);
	if (isset($_REQUEST["rights"]) && is_array($_REQUEST["rights"]) && sizeof($_REQUEST["rights"])>0) {
		foreach ($_REQUEST["rights"] as $value) {
			$dbconn->Execute(" INSERT INTO ".GROUP_MODULE_USER_TABLE." (id_user, id_module) VALUES ('".$moderator_id."', '".$value."') ");
		}
	}
	ListModerators();
	return ;
}


function DeleteModerator() {
	global $smarty, $dbconn, $config, $config_admin, $lang;

	if (isset($_REQUEST['id']) && intval($_REQUEST['id'])>0) {
		$id_moderator = intval($_REQUEST['id']);
		$strSQL = " DELETE FROM ".USERS_TABLE." WHERE id='".$id_moderator."' ";
		$dbconn->Execute($strSQL);
		$strSQL = " DELETE FROM ".USER_GROUP_TABLE." WHERE id_user='".$id_moderator."' ";
		$dbconn->Execute($strSQL);
		$strSQL = " DELETE FROM ".GROUP_MODULE_USER_TABLE." WHERE id_user='".$id_moderator."' ";
		$dbconn->Execute($strSQL);
	}
	ListModerators($lang["errors"]["moderator_deleted"]);
	return ;
}

?>