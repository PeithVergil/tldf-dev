<?php
/**
*
*
*
*   @author Alexander Kiverin<aki@pilotgroup.net>, Pilot Group <http://www.pilotgroup.net/>
*   @date   11/11/2006
*
**/
include "./common_webmessenger.php";

AdminMainMenu($lang["messenger"]);

if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
else
	$file_name = "settings.php";

$sel = $_GET["sel"]?$_GET["sel"]:$_POST["sel"];

switch($sel){
	case "save_settings": 	SaveSettings(); break;
	case "save_groups": 	SaveGroupsPermissions(); break;
	case "default":		DefaultSettings(); break;
	default:		ListSettings();
}

function ListSettings(){
	global $smarty, $dbconn, $config, $config_admin, $lang, $file_name;

	///////// query
	$query = "select name, value, iscolor from ".UP_MESSENGER_SETTINGS_TABLE;
	$rs = $dbconn->Execute($query);

	$settings = array();
	$i = 0;
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$settings[$i]["name"] = $row["name"];
		$settings[$i]["value"] = $row["value"];
		$settings[$i]["iscolor"] = $row["iscolor"];
		$settings[$i]["title"] = $lang["messenger"]["settings"][$row["name"]]["name"];
		$settings[$i]["comment"] = $lang["messenger"]["settings"][$row["name"]]["comment"];
		$i++;
		$rs->MoveNext();
	}
	$smarty->assign("settings",$settings);

	$query = "SELECT a.id, a.name, b.id as avdisabled FROM ".GROUPS_TABLE." a LEFT JOIN ".UP_MESSENGER_GROUPS_AVDISABLED_TABLE." b on a.id = b.id_group WHERE a.type in ('f','d')";
	$rs = $dbconn->Execute($query);

	$groups = array();
	$i = 0;
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$groups[$i]["id"] = $row["id"];
		$groups[$i]["name"] = stripslashes($row["name"]);
		$groups[$i]["check"] = $row["avdisabled"] ? false : true;
		$i++;
		$rs->MoveNext();
	}
	$smarty->assign("groups",$groups);

	$form["default_link"] = "./".$file_name."?sel=default";

	$form["action"] = "./".$file_name;
	$form["form1_hiddens"] = "<input type=hidden name=sel value=save_settings>";
	$form["form2_hiddens"] = "<input type=hidden name=sel value=save_groups>";

	$smarty->assign("form",$form);
	$smarty->assign("button",$lang["button"]);
	$smarty->display(TrimSlash($config["admin_webmessenger_theme_path"])."/settings.tpl");
	exit;
}

function SaveSettings(){
	global $smarty, $dbconn, $config, $config_admin, $lang, $file_name, $auth;

	foreach ($_POST["settings"] as $name => $value) {
		$query = "update ".UP_MESSENGER_SETTINGS_TABLE." set value='".$value."' where name = '".$name."'";
		$rs = $dbconn->Execute($query);
	}

	ListSettings();
	return;
}

function DefaultSettings(){
	global $smarty, $dbconn, $config, $config_admin, $lang, $file_name, $auth;

	$query = "update ".UP_MESSENGER_SETTINGS_TABLE." set value = default_value";
	$rs = $dbconn->Execute($query);

	ListSettings();
	return;
}

function SaveGroupsPermissions(){
	global $smarty, $dbconn, $config, $config_admin, $lang, $file_name, $auth;

	$query = "SELECT a.id, b.id as avdisabled FROM ".GROUPS_TABLE." a LEFT JOIN ".UP_MESSENGER_GROUPS_AVDISABLED_TABLE." b on a.id = b.id_group WHERE a.type in ('f','d')";
	$rs = $dbconn->Execute($query);
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		if ( (isset($_POST["groups"][$row["id"]])) && ($row["avdisabled"]) ) {
			$dbconn->Execute("delete from ".UP_MESSENGER_GROUPS_AVDISABLED_TABLE." where id_group = '".$row["id"]."'");
		}
		if ( (!isset($_POST["groups"][$row["id"]])) && (!$row["avdisabled"]) ) {
			$dbconn->Execute("insert into ".UP_MESSENGER_GROUPS_AVDISABLED_TABLE." (id_group) values (".$row["id"].")");
		}
		$rs->MoveNext();
	}

	ListSettings();
	return;
}

?>