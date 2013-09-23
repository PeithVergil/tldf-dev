<?php

/**
* Site "Distances" reference management.
*
* @package DatingPro
* @subpackage Admin Mode
**/

include '../include/config.php';
include_once '../common.php';
include '../include/config_admin.php';
include '../include/functions_auth.php';
include '../include/functions_admin.php';
include '../include/class.lang.php';

$auth = auth_user();
login_check($auth);
IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "distance");

$sel = isset($_REQUEST["sel"]) ? $_REQUEST["sel"] : "";

switch($sel){
	case "add": AddDistance(); break;
	case "del": DelDistance(); break;
	case "edit": EditForm("edit"); break;
	case "change": ChangeDistance(); break;
	default: ListDistance();
}

function ListDistance($err="", $name="")
{
	global $smarty, $dbconn, $config, $config_admin, $page, $lang;

	if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
	$file_name = "admin_distance.php";

	AdminMainMenu($lang["distance"]);

	$strSQL = " SELECT DISTINCT id, name, type FROM ".DISTANCE_SPR_TABLE." ORDER BY type, name";
	$rs = $dbconn->Execute($strSQL);

	$i = 0;
	if($rs->RowCount()>0){
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$spr_arr[$i]["number"] = $i+1;
			$spr_arr[$i]["id"] = $row["id"];
			$spr_arr[$i]["name"] = $row["name"];
			$spr_arr[$i]["type"] = ($row["type"] == "mile") ? $lang["distance"]["mile"] : $lang["distance"]["km"];
			$spr_arr[$i]["editlink"] = $file_name."?sel=edit&id=".$row["id"];
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign("types", $spr_arr);
	}
	$form["add_link"] = $file_name."?sel=add";
	$smarty->assign("form", $form);
	$smarty->assign("button", $lang["button"]);

	$smarty->assign("header", $lang["distance"]);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_distance_table.tpl");
	exit;
}

function EditForm($par,$err="")
{
	global $smarty, $dbconn, $config, $config_admin, $page, $lang;

	if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
	$file_name = "admin_distance.php";

	if($err){
		$form["err"] = $err;
	}

	AdminMainMenu($lang["distance"]);
	$id = isset($_REQUEST["id"]) ? intval($_REQUEST["id"]) : "";

	if($par != "add"){
		if($id == ""){ ListDistance(); return;}
		if(!$err){
			$strSQL = "select name, type from ".DISTANCE_SPR_TABLE." where id='".$id."'";
			$rs = $dbconn->Execute($strSQL);
			$data["name"] = $rs->fields[0];
			$data["type"] = $rs->fields[1];
		}else{
			$data = $_POST;
		}

		$form["hiddens"] = "<input type=hidden name=sel value=change>";
		$form["hiddens"] .= "<input type=hidden name=e value=1>";
		$form["hiddens"] .= "<input type=hidden name=id value=".$id.">";

	}else{
		if(!$err){
			$data["name"] = "";
			$data["type"] = "mile";
		}
		$form["hiddens"] = "<input type=hidden name=sel value=add>";
		$form["hiddens"] .= "<input type=hidden name=e value=1>";
		$form["hiddens"] .= "<input type=hidden name=page value=".$page.">";
	}
	$form["delete"] = $file_name."?sel=del&id=".$id."&page=".$page;
	$form["back"] = $file_name."?page=".$page;
	$form["action"] = $file_name;
	$form["par"] = $par;
	$form["confirm"] = $lang["confirm"]["reference"];

	$smarty->assign("form", $form);
	if (isset($data)) {
		$smarty->assign("data", $data);
	}
	$smarty->assign("header", $lang["distance"]);
	$smarty->assign("button", $lang["button"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_distance_form.tpl");
	exit;
}

function AddDistance()
{
	global $smarty, $dbconn, $config, $config_admin, $page, $lang, $e;
	$name = isset($_POST["name"]) ? $_POST["name"] : "" ;
	$type = isset($_POST["type"]) ? $_POST["type"] : "" ;

	$e = isset($_POST["e"]) ? intval($_POST["e"]) : "";

	if((!strlen($name))||(!intval($name))){
		$err = ($e!="") ? $lang["err"]["invalid_distance"] : "";
		EditForm("add",$err);
	}

	$strSQL = "SELECT COUNT(id) FROM ".DISTANCE_SPR_TABLE." WHERE name = '".intval($name)."' AND type = '".$type."'";
	$rs= $dbconn->Execute($strSQL);
	if($rs->fields[0]>0){
		if($e)$err = $lang["err"]["exists_option"];
		EditForm("add",$err); return;
	}

	$strSQL = " INSERT INTO ".DISTANCE_SPR_TABLE." (name, type) VALUES ('".intval($name)."', '".$type."')";
	$dbconn->Execute($strSQL);
	ListDistance();
	return;
}

function ChangeDistance()
{
	global $smarty, $dbconn, $config, $config_admin, $page, $spr_abbr, $lang, $file_name, $spr, $e;

	$id = $_POST["id"];
	$name = $_POST["name"];
	$type = $_POST["type"];
	$e = intval($_POST["e"]);

	if((!strlen($name))||(!intval($name))){
		if($e) $err = $lang["err"]["invalid_distance"];
		EditForm("edit",$err);
	}

	$strSQL = " SELECT COUNT(id) FROM ".DISTANCE_SPR_TABLE." WHERE name = '".intval($name)."' and type = '".$type."' and id<>'".$id."'";
	$rs= $dbconn->Execute($strSQL);
	if($rs->fields[0]>0){
		if($e)$err = $lang["err"]["exists_option"];
		EditForm("edit",$err); return;
	}

	$strSQL = "update ".DISTANCE_SPR_TABLE."  set name='".intval($name)."', type='".$type."'  where id='".$id."'";
	$dbconn->Execute($strSQL);
	ListDistance(); return;
}

function DelDistance()
{
	global $smarty, $dbconn, $config, $config_admin, $page, $lang;
	$id = isset($_REQUEST["id"]) ? intval($_REQUEST["id"]) : "";

	if($id == ""){ ListDistance(); return;}
	$strSQL = "delete from ".DISTANCE_SPR_TABLE."  where id='".$id."'";
	$dbconn->Execute($strSQL);
	ListDistance(); return;
}

?>