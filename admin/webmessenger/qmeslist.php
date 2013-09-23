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
	$file_name = "qmeslist.php";

$sel = $_GET["sel"]?$_GET["sel"]:$_POST["sel"];

switch($sel){
	case "add": 	AddQuickMessage(); break;
	case "save": 	SaveQuickMessage(); break;
	case "del": 	DelQuickMessage(); break;
	default:	ListQuickMessage();
}

function ListQuickMessage(){
	global $smarty, $dbconn, $config, $config_admin, $lang, $file_name;

	///////// query
	$query = "select id, title, body, DATE_FORMAT(created,'".$config["date_format"]."') as date_created
		from ".UP_MESSENGER_QUICKMESSAGES_TABLE;
	$rs = $dbconn->Execute($query);

	$messages = array();
	$i = 0;
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$messages[$i]["number"] = $i+1;
		$messages[$i]["id"] = $row["id"];
		$messages[$i]["title"] = stripslashes($row["title"]);
		if(strlen($row["body"]) > 200)
			$messages[$i]["body"] = nl2br(substr(stripslashes($row["body"]), 0, 200))." ...";
		else
			$messages[$i]["body"] = nl2br(stripslashes($row["body"]));
		$messages[$i]["created"] = $row["date_created"];

		$messages[$i]["delete_link"] = "./".$file_name."?sel=del&id=".$row["id"];

		$i++;
		$rs->MoveNext();
	}
	$smarty->assign("messages",$messages);

	$form["add_link"] = "./".$file_name."?sel=add";

	$smarty->assign("form",$form);
	$smarty->assign("button",$lang["button"]);
	$smarty->display(TrimSlash($config["admin_webmessenger_theme_path"])."/qmeslist.tpl");
	exit;
}

function AddQuickMessage($err=""){
	global $smarty, $dbconn, $config, $config_admin, $lang, $file_name;

	$form["title"] = "";
	$form["body"] = "";

	$form["action"] = $file_name;
	$form["hiddens"] = "<input type=hidden name=sel value=save>";
	if ($err) $form["err"] = $err;
	$form["back_link"] = "./".$file_name;

	$smarty->assign("form",$form);

	$smarty->assign("tools", $lang["edit_tool"]);
	$smarty->assign("color", $lang["colors_name"]);
	$smarty->assign("font", $lang["fonts_name"]);
	$smarty->assign("button",$lang["button"]);
	$smarty->display(TrimSlash($config["admin_webmessenger_theme_path"])."/qmesedit.tpl");
	exit;
}

function SaveQuickMessage(){
	global $smarty, $dbconn, $config, $config_admin, $lang, $file_name, $auth;

	if (trim($_POST["title"]) && trim($_POST["body"])) {
		$rs = $dbconn->Execute("Select id from ".UP_MESSENGER_QUICKMESSAGES_TABLE." where title = '".addslashes($_POST["title"])."'");
		if ($rs->RecordCount()) {
			$err = $lang["messenger"]["err"]["quickmessage_exists"];
			AddQuickMessage($err);
			return;
		}
	
		$query = "insert into ".UP_MESSENGER_QUICKMESSAGES_TABLE." (title, body, created) values ('".addslashes($_POST["title"])."','".addslashes($_POST["body"])."',NOW())";
		$rs = $dbconn->Execute($query);
	} else {
		$err = $lang["messenger"]["err"]["quickmessage_empty"];
		AddQuickMessage($err);
		return;
	}

	ListQuickMessage();
	return;
}

function DelQuickMessage(){
	global $smarty, $dbconn, $config, $config_admin, $lang, $file_name;

	$id = $_GET["id"]?$_GET["id"]:$_POST["id"];

	if ($id) {
		$query = "delete from ".UP_MESSENGER_QUICKMESSAGES_TABLE." where id = '".$id."'";
		$rs = $dbconn->Execute($query);
	}

	ListQuickMessage();
	return;
}

?>