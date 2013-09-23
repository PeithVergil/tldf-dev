<?php
/**
* How It Works area administration.
**/

include '../include/config.php';
include_once '../common.php';
include '../include/config_admin.php';
include '../include/functions_auth.php';
include '../include/functions_admin.php';

$auth = auth_user();
login_check($auth);
//IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "howitworks");

$sel = isset($_REQUEST["sel"]) ? $_REQUEST["sel"] : "";

switch($sel){
	case "add":		AddHowWorks();
	case "edit":	EditForm("edit");
	case "up":		UpHowWorks($_GET["id"]);
	case "down":	DownHowWorks($_GET["id"]);
	case "change":	ChangeHowWorks();
	case "del":		DeleteHowWorks();
	default:		ListHowWorks();
}

function ListHowWorks($err=""){
	global $smarty, $dbconn, $config, $config_admin, $page, $lang;

	if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
	$file_name = "admin_how_works.php";

	AdminMainMenu($lang["how_works"]);

	$page = isset($_REQUEST["page"]) ?  intval($_REQUEST["page"]) : 1;

	$strSQL = "SELECT COUNT(id) from ".HOW_WORKS_INFO_TABLE;
	$rs = $dbconn->Execute($strSQL);
	$num_records = $rs->fields[0];

	$lim_min = ($page-1)*$config_admin["how_works_numpage"];
	$lim_max = $config_admin["how_works_numpage"];
	$limit_str = " limit ".$lim_min.", ".$lim_max;
	
	$strSQL = "SELECT id, title, title_t, status from ".HOW_WORKS_INFO_TABLE." order by sorter ".$limit_str;
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	if($rs->RowCount()>0){
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$how_works[$i]["number"] = ($page-1)*$config_admin["how_works_numpage"]+($i+1);
			$how_works[$i]["id"] = $row["id"];
			$how_works[$i]["title"] = strip_tags(stripslashes($row["title"]));
			if(strlen(utf8_decode($how_works[$i]["title"]))>200)
			{
				$how_works[$i]["title"] = utf8_substr($how_works[$i]["title"], 0, 200)."...";
			}
			$how_works[$i]["title_t"] = strip_tags(stripslashes($row["title_t"]));
			$how_works[$i]["status"] = $row["status"]?"+":"";
			$how_works[$i]["deletelink"] = $file_name."?sel=del&id=".$row["id"];
			$how_works[$i]["editlink"] = $file_name."?sel=edit&page=".$page."&id=".$row["id"];
			$how_works[$i]["uplink"] = $file_name."?sel=up&id=".$row["id"];
			$how_works[$i]["downlink"] = $file_name."?sel=down&id=".$row["id"];
			$rs->MoveNext();
			$i++;
		}
		$param = $file_name."?";
		$smarty->assign("links", GetLinkStr($num_records,$page,$param,$config_admin["how_works_numpage"]));
		$smarty->assign("how_works", $how_works);
	}
	///	form

	$form["err"] = $err;
	$form["confirm"] = $lang["confirm"]["how_works"];

	$smarty->assign("add_link", $file_name."?sel=add&page=".$page);
	$smarty->assign("form", $form);
	$smarty->assign("button", $lang["button"]);
	$smarty->assign("header", $lang["how_works"]);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_how_works_table.tpl");
	exit;
}

function EditForm($par,$err=""){
	global $smarty, $dbconn, $config, $config_admin, $lang, $_FILES;

	if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
	$file_name = "admin_how_works.php";

	AdminMainMenu($lang["how_works"]);

	$page = isset($_REQUEST["page"]) ?  intval($_REQUEST["page"]) : 1;

	if($err){
		$form["err"]	= $err;
		$data["status"]	= $_POST["status"];
		$data["title"]	= $_POST["title"];
		$data["video"]	= $_POST["video"];
		$data["description"] = $_POST["description"];
	}
	if($par != "add"){
		global $id;
		if(!$id) $id = $_GET["id"];

		if(!$err){
			$strSQL = "SELECT id, title, title_t, video, video_t, description, description_t, status FROM ".HOW_WORKS_INFO_TABLE." WHERE id='".$id."'";
			$rs = $dbconn->Execute($strSQL);
			$row = $rs->GetRowAssoc(false);
			$data["title"]		  = stripcslashes($row["title"]);
			$data["video"]		  = $row["video"];
			$data["description"]  = stripcslashes($row["description"]);
			$data["title_t"]	  = stripcslashes($row["title_t"]);
			$data["video_t"]	  = $row["video_t"];
			$data["description_t"]= stripcslashes($row["description_t"]);
			$data["status"]		  = $row["status"];
			
		}

		$form["hiddens"] = "<input type=hidden name=sel value=change>";
		$form["hiddens"] .= "<input type=hidden name=e value=1>";
		$form["hiddens"] .= "<input type=hidden name=page value=\"".$page."\">";
		$form["hiddens"] .= "<input type=hidden name=id value=\"".$id."\">";
		$form["delete"] = $file_name."?sel=del&id=".$id;
	}else{
		$form["hiddens"] = "<input type=hidden name=sel value=add>";
		$form["hiddens"] .= "<input type=hidden name=e value=1>";
		$form["hiddens"] .= "<input type=hidden name=page value=\"".$page."\">";
	}

	$form["back"] = $file_name."?page=".$page;
	$form["action"] = $file_name;
	$form["par"] = $par;
	$form["confirm"] = $lang["confirm"]["how_works"];
	
	if (isset($data)) {
		$smarty->assign("data", $data);
	}
	
	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["how_works"]);
	$smarty->assign("tools", $lang["edit_tool"]);
	$smarty->assign("err", $lang["err"]);
	//$smarty->assign("color", $lang["colors_name"]);
	$smarty->assign("font", $lang["fonts_name"]);
	$smarty->assign("button", $lang["button"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_how_works_form.tpl");
	exit;
}

function AddHowWorks(){
	global $smarty, $dbconn, $config, $config_admin, $page, $lang, $e;
	
	$status = isset($_POST["status"]) ? intval($_POST["status"]) : 0;
	$title = isset($_POST["title"]) ? $_POST["title"] : "";
	$video = isset($_POST["video"]) ? $_POST["video"] : "";
	$description = isset($_POST["description"]) ? $_POST["description"] : "";
	
	$title_t = isset($_POST["title_t"]) ? $_POST["title_t"] : "";
	$video_t = isset($_POST["video_t"]) ? $_POST["video_t"] : "";
	$description_t = isset($_POST["description_t"]) ? $_POST["description_t"] : "";
	
	if(!strlen($title) || !strlen($title_t)){
		if($e){
			$err = $lang["err"]["invalid_fields"];
			$err .= "<br>".$lang["how_works"]["title"];
		} else {
			$err = "";
		}
		EditForm("add", $err);
	}
	
	$rs=$dbconn->Execute("SELECT max(sorter) from ".HOW_WORKS_INFO_TABLE."");
	$sorter = intval($rs->fields[0])+1;
	//////// save entry
	$dbconn->Execute("INSERT INTO ".HOW_WORKS_INFO_TABLE." (title, title_t, description, description_t, video, video_t, status, sorter) values ('".$title."','".$title_t."','".$description."','".$description_t."','".$video."','".$video_t."','".$status."', '".$sorter."')");
	ListHowWorks(); return;
}

function ChangeHowWorks(){
	global $smarty, $dbconn, $config, $config_admin, $page, $lang, $e;
	
	$id = $_POST["id"];
	if(!$id)	{	ListHowWorks(); return;}

	$status = isset($_POST["status"]) ? intval($_POST["status"]) : 0;
	$title = isset($_POST["title"]) ? $_POST["title"] : "";
	$video = isset($_POST["video"]) ? $_POST["video"] : "";
	$description = isset($_POST["description"]) ? $_POST["description"] : "";
	
	$title_t = isset($_POST["title_t"]) ? $_POST["title_t"] : "";
	$video_t = isset($_POST["video_t"]) ? $_POST["video_t"] : "";
	$description_t = isset($_POST["description_t"]) ? $_POST["description_t"] : "";
	
	//////// save
	$dbconn->Execute("UPDATE ".HOW_WORKS_INFO_TABLE." SET status = '".$status."', title = '".$title."', title_t = '".$title_t."', video = '".$video."', video_t = '".$video_t."', description = '".$description."', description_t = '".$description_t."' WHERE id='".$id."'");
	ListHowWorks(); return;
}

function DeleteHowWorks(){
	global $smarty, $dbconn, $config, $config_admin, $page, $lang, $e, $IMG_TYPE_ARRAY, $_FILES;
	
	$id = intval($_GET["id"]);
	if(!$id)	{	ListHowWorks(); return;}
	
	$dbconn->Execute("DELETE FROM ".HOW_WORKS_INFO_TABLE." WHERE id='".$id."'");
	ListHowWorks(); return;
}

function UpHowWorks($id){
	global $smarty, $dbconn, $config, $config_admin, $lang;
	if(!$id) ListHowWorks();
	$rs = $dbconn->Execute("Select id, sorter from ".HOW_WORKS_INFO_TABLE." where id='".$id."'");
	if ($rs->RowCount()>0){
		$id_tour = $rs->fields[0];
		$sorter_howworks = $rs->fields[1];
	}
	$rs = $dbconn->Execute("Select id, sorter from ".HOW_WORKS_INFO_TABLE." where sorter<'".$sorter_howworks."' order by sorter desc");
	if ($rs->RowCount()>0){
		$id_old = $rs->fields[0];
		$sorter_old = $rs->fields[1];
	}
	if(intval($id_old)){
		$dbconn->Execute("Update ".HOW_WORKS_INFO_TABLE." set sorter='".$sorter_howworks."' where id='".$id_old."'");
		$dbconn->Execute("Update ".HOW_WORKS_INFO_TABLE." set sorter='".$sorter_old."' where id='".$id_tour."'");
		///// new page
		$rs=$dbconn->Execute("Select count(*) from ".HOW_WORKS_INFO_TABLE." where sorter<='".$sorter_old."'");
		$count = $rs->fields[0];
		if($count == 0) $_GET["page"] = 1;
		else
		$_GET["page"] = floor(($count-1)/$config_admin["how_works_numpage"])+1;
	}
	ListHowWorks();
}
function DownHowWorks($id){
	global $smarty, $dbconn, $config, $config_admin, $lang;
	if(!$id) ListHowItWorks();
	$rs = $dbconn->Execute("Select id, sorter from ".HOW_WORKS_INFO_TABLE." where id='".$id."'");
	if ($rs->RowCount()>0){
		$id_tour = $rs->fields[0];
		$sorter_howworks = $rs->fields[1];
	}
	$rs = $dbconn->Execute("Select id, sorter from ".HOW_WORKS_INFO_TABLE." where sorter>'".$sorter_howworks."' order by sorter");
	if ($rs->RowCount()>0){
		$id_old = $rs->fields[0];
		$sorter_old = $rs->fields[1];
	}
	if(intval($id_old)){
		$dbconn->Execute("Update ".HOW_WORKS_INFO_TABLE." set sorter='".$sorter_howworks."' where id='".$id_old."'");
		$dbconn->Execute("Update ".HOW_WORKS_INFO_TABLE." set sorter='".$sorter_old."' where id='".$id_tour."'");
		///// new page
		$rs=$dbconn->Execute("Select count(*) from ".HOW_WORKS_INFO_TABLE." where sorter<='".$sorter_old."'");
		$count = $rs->fields[0];
		if($count == 0) $_GET["page"] = 1;
		else
		$_GET["page"] = floor(($count-1)/$config_admin["how_works_numpage"])+1;
	}
	ListHowWorks();
}
?>