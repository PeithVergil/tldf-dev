<?php

/**
* Take a tour area administration.
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
IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "taketour");

$sel = isset($_REQUEST["sel"]) ? $_REQUEST["sel"] : "";

switch($sel){
	case "add": AddTaketour();
	case "edit": EditForm("edit");
	case "up": UpTaketour($_GET["id"]);
	case "down": DownTaketour($_GET["id"]);
	case "change": ChangeTaketour();
	case "del": DeleteTaketour();
	default: ListTaketour();
}

function ListTaketour($err=""){
	global $smarty, $dbconn, $config, $config_admin, $page, $lang;

	if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
	$file_name = "admin_taketour.php";

	AdminMainMenu($lang["taketour"]);

	$page = isset($_REQUEST["page"]) ?  intval($_REQUEST["page"]) : 1;

	$strSQL = "SELECT COUNT(id) from ".TAKE_TOUR_TABLE;
	$rs = $dbconn->Execute($strSQL);
	$num_records = $rs->fields[0];

	$lim_min = ($page-1)*$config_admin["taketour_numpage"];
	$lim_max = $config_admin["taketour_numpage"];
	$limit_str = " limit ".$lim_min.", ".$lim_max;
	$strSQL = "select id, file_path, file_type, comment, sorter, status from ".TAKE_TOUR_TABLE." order by sorter ".$limit_str;
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	if($rs->RowCount()>0){
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$taketour[$i]["number"] = ($page-1)*$config_admin["taketour_numpage"]+($i+1);
			$taketour[$i]["id"] = $row["id"];
			$taketour[$i]["comment"] = strip_tags(stripslashes($row["comment"]));
			if(strlen(utf8_decode($taketour[$i]["comment"]))>200)
			$taketour[$i]["comment"] = utf8_substr($taketour[$i]["comment"], 0, 200)."...";
			$taketour[$i]["status"] = $row["status"]?"+":"";
			$taketour[$i]["deletelink"] = $file_name."?sel=del&id=".$row["id"];
			$taketour[$i]["editlink"] = $file_name."?sel=edit&page=".$page."&id=".$row["id"];
			$taketour[$i]["uplink"] = $file_name."?sel=up&id=".$row["id"];
			$taketour[$i]["downlink"] = $file_name."?sel=down&id=".$row["id"];
			$rs->MoveNext();
			$i++;
		}
		$param = $file_name."?";
		$smarty->assign("links", GetLinkStr($num_records,$page,$param,$config_admin["taketour_numpage"]));
		$smarty->assign("taketour", $taketour);
	}
	///	form

	$form["err"] = $err;
	$form["confirm"] = $lang["confirm"]["taketour"];

	$smarty->assign("add_link", $file_name."?sel=add&page=".$page);
	$smarty->assign("form", $form);
	$smarty->assign("button", $lang["button"]);
	$smarty->assign("header", $lang["taketour"]);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_taketour_table.tpl");
	exit;
}

function EditForm($par,$err=""){
	global $smarty, $dbconn, $config, $config_admin, $lang, $_FILES;

	if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
	$file_name = "admin_taketour.php";

	AdminMainMenu($lang["taketour"]);

	////// settings
	$rs = $dbconn->Execute("select name, value from ".SETTINGS_TABLE." where name in ('taketour_folder')");
	while(!$rs->EOF){
		$settings[$rs->fields[0]] = $rs->fields[1];
		$rs->MoveNext();
	}

	$page = isset($_REQUEST["page"]) ?  intval($_REQUEST["page"]) : 1;

	if($err){
		$form["err"] = $err;
		$data["comment"] = $_POST["comment"];
		$data["status"] = $_POST["status"];
	}
	if($par != "add"){
		global $id;
		if(!$id) $id = $_GET["id"];

		if(!$err){
			$strSQL = "select id, comment, status, file_path, file_type from ".TAKE_TOUR_TABLE." where id='".$id."'";
			$rs = $dbconn->Execute($strSQL);
			$row = $rs->GetRowAssoc(false);
			$data["comment"] = $row["comment"];
			$data["status"] = $row["status"];
			$file_path = $config["site_path"].$settings["taketour_folder"]."/".$row["file_path"];
			if(strlen($row["file_path"])>0 && file_exists($file_path)){
				$data["file_type"] = $row["file_type"]?$row["file_type"]:"p";
				$data["file_path"] = $config["site_root"].$settings["taketour_folder"]."/".$row["file_path"];
			}else{
				$data["file_path"] = "";
			}

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
	$form["confirm"] = $lang["confirm"]["taketour"];


	if (isset($data)) {
		$smarty->assign("data", $data);
	}

	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["taketour"]);
	$smarty->assign("tools", $lang["edit_tool"]);
	$smarty->assign("err", $lang["err"]);
	$smarty->assign("color", $lang["colors_name"]);
	$smarty->assign("font", $lang["fonts_name"]);
	$smarty->assign("button", $lang["button"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_taketour_form.tpl");
	exit;
}

function AddTaketour(){
	global $smarty, $dbconn, $config, $config_admin, $page, $lang, $e;
	global $IMG_TYPE_ARRAY, $_FILES, $AUDIO_TYPE_ARRAY, $VIDEO_TYPE_ARRAY, $FLASH_TYPE_ARRAY;
	global $IMG_EXT_ARRAY, $AUDIO_EXT_ARRAY, $VIDEO_EXT_ARRAY, $FLASH_EXT_ARRAY;

	$comment = isset($_POST["comment"]) ? $_POST["comment"] : "";
	$status = isset($_POST["status"]) ? intval($_POST["status"]) : 0;
	$upload_file = isset($_FILES["upload_file"]) ? $_FILES["upload_file"] : null;
	if(!strlen($comment)){
		if($e){
			$err = $lang["err"]["invalid_fields"];
			$err .= "<br>".$lang["taketour"]["comment"];
		} else {
			$err = "";
		}
		EditForm("add", $err);
	}
	$rs=$dbconn->Execute("select max(sorter) from ".TAKE_TOUR_TABLE."");
	$sorter = intval($rs->fields[0])+1;
	//////// save entry
	$dbconn->Execute("insert into ".TAKE_TOUR_TABLE." (comment, status, sorter) values ('".addslashes($comment)."', '".$status."', '".$sorter."')");
	$rs=$dbconn->Execute("select max(id) from ".TAKE_TOUR_TABLE."");
	$new_id = $rs->fields[0];

	// settings
	$rs = $dbconn->Execute("select name, value from ".SETTINGS_TABLE." where name in ('taketour_folder')");
	while(!$rs->EOF){
		$settings[$rs->fields[0]] = $rs->fields[1];
		$rs->MoveNext();
	}
	
	// icon
	$path_to_image = GetTempUploadFile($upload_file['name']);

	$filename_arr = explode('.', $path_to_image);
	$nr = count($filename_arr);
	$ext = strtolower($filename_arr[$nr-1]);

	if (in_array($upload_file['type'], $IMG_TYPE_ARRAY)) {
		$file_type = 'p';
		if (!in_array($ext, $IMG_EXT_ARRAY)) {
			$err = $lang['err']['invalid_file_ext'] . implode(', ', $IMG_EXT_ARRAY);
			$err = str_replace('#EXT#', $ext, $err);
		}
	} elseif (in_array($upload_file['type'], $AUDIO_TYPE_ARRAY)) {
		$file_type = 'a';
		if (!in_array($ext, $AUDIO_EXT_ARRAY)) {
			$err = $lang['err']['invalid_file_ext'] . implode(', ', $AUDIO_EXT_ARRAY);
			$err = str_replace('#EXT#', $ext, $err);
		}
	} elseif (in_array($upload_file['type'], $VIDEO_TYPE_ARRAY)) {
		$file_type = 'v';
		if (!in_array($ext, $VIDEO_EXT_ARRAY)) {
			$err = $lang['err']['invalid_file_ext'] . implode(', ', $VIDEO_EXT_ARRAY);
			$err = str_replace('#EXT#', $ext, $err);
		}
	} elseif (in_array($upload_file['type'], $FLASH_TYPE_ARRAY)) {
		$file_type = 'f';
		if (!in_array($ext, $FLASH_EXT_ARRAY)) {
			$err = $lang['err']['invalid_file_ext'] . implode(', ', $FLASH_EXT_ARRAY);
			$err = str_replace('#EXT#', $ext, $err);
		}
	} else {
		$file_type = '';
		$err = $lang['err']['invalid_file_type']
			. implode(', ', $IMG_TYPE_ARRAY) . ', '
			. implode(', ', $AUDIO_TYPE_ARRAY) . ', '
			. implode(', ', $VIDEO_TYPE_ARRAY) . ', '
			. implode(', ', $FLASH_TYPE_ARRAY);
		$err = str_replace('#TYPE#', $upload_file['type'], $err);
	}
	
	if ($err != '') {
		EditForm('edit', $err);
		return;
	}
	
	if(is_uploaded_file($upload_file["tmp_name"]) && move_uploaded_file($upload_file["tmp_name"],$path_to_image)){
		$upload_file["tmp_name"] = $path_to_image;
		if(strlen($file_type)>0){
			$ex_arr = explode(".",$upload_file["name"]);
			$extension = $ex_arr[count($ex_arr)-1];
			$new_file_name = $new_id."_".date("ymdhis").".".$extension;
			$news_file =$config["site_path"].$settings["taketour_folder"]."/".$new_file_name;
			if(is_dir($config["site_path"].$settings["taketour_folder"])){
				if(copy($upload_file["tmp_name"], $news_file)){
					unlink($upload_file["tmp_name"]);
					///// insert entry into db
					$dbconn->Execute("Update ".TAKE_TOUR_TABLE." set  file_path='".$new_file_name."', file_type='".$file_type."' where id='".$new_id."'");
				}
			}
		}
		if(strlen($err)>0)	{ EditForm("edit", $err); return;}
	}
	ListTaketour(); return;
}

function ChangeTaketour(){
	global $smarty, $dbconn, $config, $config_admin, $page, $lang, $e;
	global $IMG_TYPE_ARRAY, $_FILES, $AUDIO_TYPE_ARRAY, $VIDEO_TYPE_ARRAY, $FLASH_TYPE_ARRAY;

	$id = $_POST["id"];
	if(!$id)	{	ListTaketour(); return;}

	$comment = $_POST["comment"];
	$status = isset($_POST["status"]) ? intval($_POST["status"]) : 0;
	$upload_file=$_FILES["upload_file"];
	if(!strlen($comment)){
		if($e){
			$err = $lang["err"]["invalid_fields"];
			$err .= "<br>".$lang["taketour"]["comment"];
		}
		EditForm("edit", $err);
	}

	//////// save news
	$dbconn->Execute("update ".TAKE_TOUR_TABLE." set comment = '".addslashes($comment)."', status = '".$status."' where id='".$id."'");

	// settings
	$rs = $dbconn->Execute("select name, value from ".SETTINGS_TABLE." where name in ('taketour_folder')");
	while(!$rs->EOF){
		$settings[$rs->fields[0]] = $rs->fields[1];
		$rs->MoveNext();
	}
	
	// icon
	$path_to_image = GetTempUploadFile($upload_file['name']);
	
	if (is_uploaded_file($upload_file['tmp_name']) && move_uploaded_file($upload_file['tmp_name'], $path_to_image)) {
		$upload_file['tmp_name'] = $path_to_image;
		if (in_array($upload_file['type'], $IMG_TYPE_ARRAY)) {
			$file_type = 'p';
		} elseif (in_array($upload_file['type'], $AUDIO_TYPE_ARRAY)) {
			$file_type = 'a';
		} elseif (in_array($upload_file['type'], $VIDEO_TYPE_ARRAY)) {
			$file_type = 'v';
		} elseif (in_array($upload_file['type'], $FLASH_TYPE_ARRAY)) {
			$file_type = 'f';
		} else {
			$file_type = '';
			$err = $lang['err']['invalid_file_type']
				. implode(', ', $IMG_TYPE_ARRAY) . ', '
				. implode(', ', $AUDIO_TYPE_ARRAY) . ', '
				. implode(', ', $VIDEO_TYPE_ARRAY) . ', '
				. implode(', ', $FLASH_TYPE_ARRAY);
			$err = str_replace('#TYPE#', $upload_file['type'], $err);
		}
		
		if (strlen($file_type) > 0) {
			$ex_arr = explode(".",$upload_file["name"]);
			$extension = $ex_arr[count($ex_arr)-1];
			$new_file_name = $id."_".date("ymdhis").".".$extension;
			$news_file =$config["site_path"].$settings["taketour_folder"]."/".$new_file_name;
			if(is_dir($config["site_path"].$settings["taketour_folder"])){
				if(copy($upload_file["tmp_name"], $news_file)){
					///// ���� ������� ������ ���� ���� �� ���
					$rs_img=$dbconn->Execute("Select file_path from ".TAKE_TOUR_TABLE." where id='".$id."'");
					if(strlen($rs_img->fields[0])>0){
						$old_file =$config["site_path"].$settings["taketour_folder"]."/".$rs_img->fields[0];
						if(file_exists($old_file))	unlink($old_file);
					}
					///// insert entry into db
					$dbconn->Execute("Update ".TAKE_TOUR_TABLE." set  file_path='".$new_file_name."', file_type='".$file_type."' where id='".$id."'");
				}
			}
		}
		unlink($upload_file["tmp_name"]);
		if (strlen($err) > 0) {
			EditForm("edit", $err);
			return;
		}
	}

	ListTaketour(); return;
}

function DeleteTaketour(){
	global $smarty, $dbconn, $config, $config_admin, $page, $lang, $e, $IMG_TYPE_ARRAY, $_FILES;

	$id = intval($_GET["id"]);
	if(!$id)	{	ListTaketour(); return;}

	///// ���� ������� ������ ���� ���� �� ���
	$rs_img=$dbconn->Execute("Select file_path from ".TAKE_TOUR_TABLE." where id='".$id."'");
	if(strlen($rs_img->fields[0])>0){
		////////settings
		$rs = $dbconn->Execute("select name, value from ".SETTINGS_TABLE." where name in ('taketour_folder')");
		$settings[$rs->fields[0]] = $rs->fields[1];

		$old_file =$config["site_path"].$settings["taketour_folder"]."/".$rs_img->fields[0];
		if(file_exists($old_file))	unlink($old_file);
	}
	$dbconn->Execute("delete from ".TAKE_TOUR_TABLE." where id='".$id."'");
	ListTaketour(); return;
}

function UpTaketour($id){
	global $smarty, $dbconn, $config, $config_admin, $lang;
	if(!$id) ListTaketour();
	$rs = $dbconn->Execute("Select id, sorter from ".TAKE_TOUR_TABLE." where id='".$id."'");
	if ($rs->RowCount()>0){
		$id_tour = $rs->fields[0];
		$sorter_tour = $rs->fields[1];
	}
	$rs = $dbconn->Execute("Select id, sorter from ".TAKE_TOUR_TABLE." where sorter<'".$sorter_tour."' order by sorter desc");
	if ($rs->RowCount()>0){
		$id_old = $rs->fields[0];
		$sorter_old = $rs->fields[1];
	}
	if(intval($id_old)){
		$dbconn->Execute("Update ".TAKE_TOUR_TABLE." set sorter='".$sorter_tour."' where id='".$id_old."'");
		$dbconn->Execute("Update ".TAKE_TOUR_TABLE." set sorter='".$sorter_old."' where id='".$id_tour."'");
		///// new page
		$rs=$dbconn->Execute("Select count(*) from ".TAKE_TOUR_TABLE." where sorter<='".$sorter_old."'");
		$count = $rs->fields[0];
		if($count == 0) $_GET["page"] = 1;
		else
		$_GET["page"] = floor(($count-1)/$config_admin["taketour_numpage"])+1;
	}
	ListTaketour();
}
function DownTaketour($id){
	global $smarty, $dbconn, $config, $config_admin, $lang;
	if(!$id) ListTaketour();
	$rs = $dbconn->Execute("Select id, sorter from ".TAKE_TOUR_TABLE." where id='".$id."'");
	if ($rs->RowCount()>0){
		$id_tour = $rs->fields[0];
		$sorter_tour = $rs->fields[1];
	}
	$rs = $dbconn->Execute("Select id, sorter from ".TAKE_TOUR_TABLE." where sorter>'".$sorter_tour."' order by sorter");
	if ($rs->RowCount()>0){
		$id_old = $rs->fields[0];
		$sorter_old = $rs->fields[1];
	}
	if(intval($id_old)){
		$dbconn->Execute("Update ".TAKE_TOUR_TABLE." set sorter='".$sorter_tour."' where id='".$id_old."'");
		$dbconn->Execute("Update ".TAKE_TOUR_TABLE." set sorter='".$sorter_old."' where id='".$id_tour."'");
		///// new page
		$rs=$dbconn->Execute("Select count(*) from ".TAKE_TOUR_TABLE." where sorter<='".$sorter_old."'");
		$count = $rs->fields[0];
		if($count == 0) $_GET["page"] = 1;
		else
		$_GET["page"] = floor(($count-1)/$config_admin["taketour_numpage"])+1;
	}
	ListTaketour();
}

?>