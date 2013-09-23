<?php

/**
* Site Kisses list administration.
*
* @package DatingPro
* @subpackage Admin Mode
**/

include "../include/config.php";
include_once "../common.php";
include "../include/config_admin.php";
include "../include/functions_auth.php";
include "../include/functions_admin.php";
include "../include/class.lang.php";

$auth = auth_user();
login_check($auth);
IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "kisses_spr");

$sel = isset($_REQUEST["sel"]) ? $_REQUEST["sel"] : "";

switch($sel){
	case "add": AddKiss(); break;
	case "del": DelKiss(); break;
	case "edit": EditForm("edit"); break;
	case "change": ChangeKiss(); break;
	default: ListKiss();
}

function ListKiss($err="")
{
	global $smarty, $dbconn, $config, $lang;

	$file_name = "admin_kisses.php";

	AdminMainMenu($lang["kisses_spr"]);

	$settings["kiss_folder"] = GetSiteSettings("kiss_folder");

	$multi_lang = new MultiLang();
	
	$table_key = $multi_lang->TableKey(KISSLIST_SPR_TABLE);
	$field_name = $multi_lang->DefaultFieldName();

	$strSQL = "select id, name from ".LANGUAGE_TABLE." order by id";
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$lang_link_arr = array();
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$lang_link_arr[$i]["id"] = $row["id"];
		$lang_link_arr[$i]["name"] = $row["name"];
		$lang_link_arr[$i]["link"] = "./admin_translate.php?lang_code=".$row["id"]."&key=".$table_key."";
		$rs->MoveNext();
		$i++;
	}
	$smarty->assign("lang_link", $lang_link_arr);

	$strSQL = "select distinct a.id, b.".$field_name." as name, a.image_path, a.sorter from ".KISSLIST_SPR_TABLE." a left join ".REFERENCE_LANG_TABLE." b on b.table_key='".$table_key."' and b.id_reference=a.id order by a.sorter";
	$rs = $dbconn->Execute($strSQL);

	$i = 0;
	$spr_arr = array();
	if($rs->RowCount()>0){
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$spr_arr[$i]["number"] = $i+1;
			$spr_arr[$i]["id"] = $row["id"];
			$spr_arr[$i]["name"] = stripslashes($row["name"]);
			if ($row["image_path"] && file_exists($config["site_path"].$settings["kiss_folder"]."/".$row["image_path"]))
			$spr_arr[$i]["image_path"] = $config["site_root"].$settings["kiss_folder"]."/".$row["image_path"];
			$spr_arr[$i]["sorter"] = $row["sorter"];
			$spr_arr[$i]["editlink"] = $file_name."?sel=edit&id=".$row["id"];
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign("types", $spr_arr);
	}
	$form["add_link"] = $file_name."?sel=add";
	$smarty->assign("form", $form);
	$smarty->assign("button", $lang["button"]);

	$smarty->assign("header", $lang["kisses_spr"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_kisses_table.tpl");
	exit;
}
function EditForm($par,$err="")
{
	global $smarty, $dbconn, $config, $lang;

	$file_name = "admin_kisses.php";

	AdminMainMenu($lang["kisses_spr"]);

	$settings["kiss_folder"] = GetSiteSettings("kiss_folder");

	if ($err) {
		$form["err"] = $err;
	}

	$rs = $dbconn->Execute("select count(id) as countids from ".KISSLIST_SPR_TABLE." ");
	$ref_count = $rs->fields[0];
	
	$sorter_arr = array();
	for ($i = 0; $i < $ref_count; $i++) {
		$sorter_arr[$i]["sel"] = 0;
	}

	if($par != "add"){
		$id = $_REQUEST["id"];

		if (!$id) {
			ListKiss();
			return;
		}
		
		if (!$err) {
			$multi_lang = new MultiLang();
			
			$table_key = $multi_lang->TableKey(KISSLIST_SPR_TABLE);
			$name_temp = $multi_lang->SelectDefaultLangName($table_key, $id);
			
			$data["name"] = $name_temp["name"];
			$strSQL = "select sorter, image_path from ".KISSLIST_SPR_TABLE." where id='".$id."'";
			$rs = $dbconn->Execute($strSQL);
			$sorter_arr[$rs->fields[0]-1]["sel"] = "1";
			if ($rs->fields[1] && file_exists($config["site_path"].$settings["kiss_folder"]."/".$rs->fields[1]))
			$data["image_path"] = $config["site_root"].$settings["kiss_folder"]."/".$rs->fields[1];
		}else{
			$data = $_POST;
			$sorter_arr[$_POST["sorter"]-1]["sel"] = "1";
		}

		$form["hiddens"] = "<input type=hidden name=sel value=change>";
		
		$form["hiddens"] .= "<input type=hidden name=id value=".$id.">";
		$form["delete"] = $file_name."?sel=del&id=".$id;
	}else{
		if(!$err){
			$data["name"] = "";
			$sorter_arr[$ref_count]["sel"] = "1";
		}

		$form["hiddens"] = "<input type=hidden name=sel value=add>";
	}
	$form["hiddens"] .= "<input type=hidden name=e value=1>";
	$form["back"] = $file_name;
	$form["action"] = $file_name;
	$form["par"] = $par;
	$form["confirm"] = $lang["confirm"]["reference"];

	$smarty->assign("sorter", $sorter_arr);

	$smarty->assign("form", $form);
	$smarty->assign("data", $data);
	$smarty->assign("header", $lang["kisses_spr"]);
	$smarty->assign("button", $lang["button"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_kisses_form.tpl");
	exit;
}

//////////////  add reference function /////////////////////////////////////////////////////////////
function AddKiss()
{
	global $dbconn, $config, $lang, $e, $IMG_TYPE_ARRAY;
	
	$settings["kiss_folder"] = GetSiteSettings("kiss_folder");
	
	$name = isset($_POST["name"]) ? $_POST["name"] : "";
	$image_file = isset($_FILES["image_path"]) ? $_FILES["image_path"] : "";
	$sorter = isset($_POST["sorter"]) ? intval($_POST["sorter"]) : 1;
	
	$e = isset($_REQUEST["e"]) ? intval($_REQUEST["e"]) : null;
	
	$err = "";
	
	$multi_lang = new MultiLang();
	
	$table_key = $multi_lang->TableKey(KISSLIST_SPR_TABLE);
	$field_name = $multi_lang->DefaultFieldName();
	
	if (strlen($name) == 0) {
		if ($e) {
			$err = $lang["err"]["invalid_name"];
		}
		EditForm("add", $err);
		return;
	}
	
	$strSQL = "select count(*) from ".REFERENCE_LANG_TABLE." where ".$field_name." = '".Rep_Slashes($name)."' and table_key='".$table_key."'";
	$rs = $dbconn->Execute($strSQL);
	
	if ($rs->fields[0] > 0) {
		if($e)$err = $lang["err"]["exists_option"];
		EditForm("add", $err);
		return;
	}
	
	if (is_uploaded_file($image_file["tmp_name"])) {
		if (!in_array($image_file["type"], $IMG_TYPE_ARRAY)){
			if ($e) {
				$err = $lang["err"]["invalid_image_type"] . implode(', ', $IMG_TYPE_ARRAY);
			}
			EditForm("add",$err);
			return;
		}
		if (file_exists($config["site_path"].$settings["kiss_folder"]."/".$image_file["name"])){
			if ($e) {
				$err = $lang["err"]["exists_image"];
			}
			EditForm("add",$err);
			return;
		}
		move_uploaded_file($image_file["tmp_name"], $config["site_path"].$settings["kiss_folder"]."/".$image_file["name"]);
	} else {
		$image_file["name"] = '';
	}

	$strSQL = "insert into ".KISSLIST_SPR_TABLE." (name, image_path) values ('".Rep_Slashes($name)."','".Rep_Slashes($image_file["name"])."')";
	$dbconn->Execute($strSQL);
	$rs = $dbconn->Execute("select max(id) from ".KISSLIST_SPR_TABLE."");
	$rs_os = $dbconn->Execute("select max(sorter)+1  from ".KISSLIST_SPR_TABLE."");

	SprSorter($sorter, $rs_os->fields[0], $rs->fields[0]);

	$multi_lang->FirstLangInsert($table_key, $rs->fields[0], Rep_Slashes($name));
	
	ListKiss();
	return;
}

function ChangeKiss()
{
	global $dbconn, $config, $lang, $e, $IMG_TYPE_ARRAY;

	$settings["kiss_folder"] = GetSiteSettings("kiss_folder");

	$id = $_POST["id"];
	$name = $_POST["name"];
	$image_file = $_FILES["image_path"];
	$sorter = intval($_POST["sorter"]);
	$e = intval($_POST["e"]);

	if(!$sorter)	 $sorter = 1;

	if(strlen($name)<1){
		if($e) $err = $lang["err"]["invalid_name"];
		EditForm("edit",$err); return;
	}

	if (is_uploaded_file($image_file["tmp_name"])) {
		if (!in_array($image_file["type"], $IMG_TYPE_ARRAY)){
			if ($e) {
				$err = $lang["err"]["invalid_image_type"] . implode(', ', $IMG_TYPE_ARRAY);
			}
			EditForm("edit",$err); return;
		}
		if (file_exists($config["site_path"].$settings["kiss_folder"]."/".$image_file["name"])){
			if($e)$err = $lang["err"]["exists_image"];
			EditForm("edit",$err); return;
		}

		$strSQL = "select image_path from ".KISSLIST_SPR_TABLE." where id='".$id."'";
		$rs = $dbconn->Execute($strSQL);
		$file_path = $config["site_path"].$settings["kiss_folder"]."/".$rs->fields[0];
		if ($rs->fields[0] && file_exists($file_path))
		unlink($file_path);

		move_uploaded_file($image_file["tmp_name"], $config["site_path"].$settings["kiss_folder"]."/".$image_file["name"]);
		$strSQL = "update ".KISSLIST_SPR_TABLE."  set image_path='".Rep_Slashes($image_file["name"])."'  where id='".$id."'";
		$dbconn->Execute($strSQL);
	}

	$strSQL = "update ".KISSLIST_SPR_TABLE."  set name='".Rep_Slashes($name)."'  where id='".$id."'";
	$dbconn->Execute($strSQL);

	$multi_lang = new MultiLang();
	
	$table_key = $multi_lang->TableKey(KISSLIST_SPR_TABLE);
	$multi_lang->SaveDefaultRefNames($table_key, Rep_Slashes($name), $id);
	
	$strSQL = "select sorter from ".KISSLIST_SPR_TABLE." where id = '".$id."' ";
	$rs = $dbconn->Execute($strSQL);
	$old_sorter = $rs->fields[0];

	SprSorter($sorter, $old_sorter, $id);
	ListKiss();
	return;
}
//////////////  del reference  /////////////////////////////////////////////////////////////
function DelKiss()
{
	global $dbconn, $config;
	
	$id = isset($_REQUEST["id"]) ? intval($_REQUEST["id"]) : null;
	
	if(!$id){ ListKiss(); return;}

	$settings["kiss_folder"] = GetSiteSettings("kiss_folder");

	$strSQL = "select image_path from ".KISSLIST_SPR_TABLE." where id='".$id."'";
	$rs = $dbconn->Execute($strSQL);
	$file_path = $config["site_path"].$settings["kiss_folder"]."/".$rs->fields[0];
	if ($rs->fields[0] && file_exists($file_path))
	unlink($file_path);

	$strSQL = "delete from ".KISSLIST_SPR_TABLE." where id='".$id."'";
	$dbconn->Execute($strSQL);

	$multi_lang = new MultiLang();
	
	$table_key = $multi_lang->TableKey(KISSLIST_SPR_TABLE);
	$multi_lang->DeleteRefName($id, $table_key);

	SprSorter("", 1);
	ListKiss();
	return;
}

function SprSorter($sorter, $old_sorter, $id="")
{
	global $dbconn;
	if(!$id){
		$strSQL = "SELECT id FROM ".KISSLIST_SPR_TABLE." WHERE sorter >= '".$old_sorter."' ORDER BY sorter";
		$rs = $dbconn->Execute($strSQL);
		$i = 0;
		while(!$rs->EOF){
			$i++;
			$rs_up = $dbconn->Execute("UPDATE ".KISSLIST_SPR_TABLE." SET sorter = '".$i."' WHERE id ='".$rs->fields[0]."' ");
			$rs->MoveNext();
		}
		return;
	}
	//// sorter
	if($old_sorter<$sorter){
		$strSQL = "select id, sorter from ".KISSLIST_SPR_TABLE." where sorter >= '".$old_sorter."' and  sorter <= '".$sorter."'  order by sorter";
		$rs = $dbconn->Execute($strSQL);
		while(!$rs->EOF){
			$rs_up = $dbconn->Execute("update ".KISSLIST_SPR_TABLE." set sorter = '".($rs->fields[1]-1)."' where id ='".$rs->fields[0]."' ");
			$rs->MoveNext();
		}
		//// add sorter
		$rs_up = $dbconn->Execute("update ".KISSLIST_SPR_TABLE." set sorter = '".$sorter."' where id ='".$id."' ");

	}elseif($old_sorter>$sorter){
		$strSQL = "select id, sorter from ".KISSLIST_SPR_TABLE." where sorter <= '".$old_sorter."' and  sorter >= '".$sorter."' order by sorter";
		$rs = $dbconn->Execute($strSQL);
		while(!$rs->EOF){
			$rs_up = $dbconn->Execute("update ".KISSLIST_SPR_TABLE." set sorter = '".($rs->fields[1]+1)."' where id ='".$rs->fields[0]."' ");
			$rs->MoveNext();
		}
		//// add sorter
		$rs_up = $dbconn->Execute("update ".KISSLIST_SPR_TABLE." set sorter = '".$sorter."' where id ='".$id."' ");
	}else{
		$rs_up = $dbconn->Execute("update ".KISSLIST_SPR_TABLE." set sorter = '".$sorter."' where id ='".$id."' ");
	}
	return;
}

?>