<?php

/**
* Site reference management.
*
* @package DatingPro
* @subpackage Admin Mode
**/

include_once "../include/config.php";
include_once "../common.php";
include_once "../include/config_admin.php";
include_once "../include/functions_auth.php";
include_once "../include/functions_admin.php";
include_once "../include/class.lang.php";

$auth = auth_user();
login_check($auth);
IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "admin_reference");

$sel = isset($_REQUEST["sel"]) ? $_REQUEST["sel"] : "";
$ref = isset($_REQUEST["ref"]) ? intval($_REQUEST["ref"]) : 13;
$id_spr = isset($_REQUEST["id_spr"]) ? intval($_REQUEST["id_spr"]) : 0;

$multi_lang = new MultiLang();

define("SPR_REF_TABLE", $multi_lang->TableName($ref));

global $file_name;
$file_name = "admin_reference.php";

$have_options = 0;

switch ($ref) {
	case 1:
		$header = "descr";
		$have_options = 1;
		define("VALUE_REF_TABLE", $multi_lang->TableName(2));
		break;
	case 3:
		$header = "interests";
		break;
	case 4:
		$header = "language";
		break;
	case 5:
		$header = "nation";
		break;
	case 6:
		$header = "personal";
		$have_options = 1;
		define("VALUE_REF_TABLE", $multi_lang->TableName(7));
		break;
	case 8:
		$header = "my_portrait";
		$have_options = 1;
		define("VALUE_REF_TABLE", $multi_lang->TableName(9));
		break;
	case 12:
		$header = "weight";
		break;
	case 13:
		$header = "height";
		break;
	case 14:
		$header = "relations";
		break;
	case 16:
		$header = "hotlist";
		break;
	case 22:
		$header = "categories";
		break;
}

switch($sel){
	case "add": AddItem(); break;
	case "del": DelItem(); break;
	case "edit": EditForm("edit"); break;
	case "change": ChangeItem(); break;
	case "listopt": ListOption($id_spr); break;
	case "addopt": AddOption($id_spr); break;
	case "delopt": DelOption($id_spr); break;
	default: ListItem();
}


function ListItem($err='')
{
	global $smarty, $dbconn, $config, $config_admin, $lang, $file_name, $ref, $header, $have_options;

	AdminMainMenu($lang[$header."_spr"]);
	
	$multi_lang = new MultiLang();
	
	$table_key = $multi_lang->TableKey(SPR_REF_TABLE);
	$field_name = $multi_lang->DefaultFieldName();

	$strSQL = " SELECT DISTINCT id, name FROM ".LANGUAGE_TABLE." ORDER BY id";
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$lang_link_arr = array();
	while(!$rs->EOF){
		$lang_link_arr[$i]["id"] = $rs->fields[0];
		$lang_link_arr[$i]["name"] = $rs->fields[1];
		$lang_link_arr[$i]["link"] = "./admin_translate.php?lang_code=".$rs->fields[0]."&key=".$table_key."&ref=".$ref;
		$rs->MoveNext();
		$i++;
	}
	$smarty->assign("lang_link", $lang_link_arr);

	$strSQL = "SELECT COUNT(id) FROM ".SPR_REF_TABLE;
	$rs = $dbconn->Execute($strSQL);
	$num_records = $rs->fields[0];

	$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;
	$lim_min = ($page-1)*$config_admin["reference_numpage"];
	$lim_max = $config_admin["reference_numpage"];
	$limit_str = " limit ".$lim_min.", ".$lim_max;

	$strSQL = " SELECT DISTINCT a.id, b.".$field_name." AS name, a.sorter
				FROM ".SPR_REF_TABLE." a
				LEFT JOIN ".REFERENCE_LANG_TABLE." b ON b.table_key='".$table_key."' AND b.id_reference=a.id
				GROUP BY a.id ORDER BY a.sorter ".$limit_str;
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$spr_arr = array();
	if ($rs->RowCount()>0) {
		while(!$rs->EOF){
			$spr_arr[$i]["number"] = ($page-1)*$config_admin["reference_numpage"]+($i+1);
			$spr_arr[$i]["id"] = $rs->fields[0];
			$spr_arr[$i]["name"] = $rs->fields[1];
			$spr_arr[$i]["sorter"] = $rs->fields[2];
			$spr_arr[$i]["editlink"] = $file_name."?sel=edit&id=".$rs->fields[0]."&ref=".$ref."&page=".$page;
			$spr_arr[$i]["editoptionlink"] = $file_name."?sel=listopt&ref=".$ref."&page=".$page."&id_spr=".$rs->fields[0];
			$rs->MoveNext();
			$i++;
		}

		$smarty->assign("types", $spr_arr);
		$param = $file_name."?sel=list&ref=".$ref."&";
		$smarty->assign("links", GetLinkStr($num_records, $page, $param, $config_admin["reference_numpage"]));
	}

	$form["add_link"] = $file_name."?sel=add&ref=".$ref;
	$form["options"] = $have_options;

	$smarty->assign("form", $form);

	$smarty->assign("button", $lang["button"]);
	$smarty->assign("header", $lang[$header."_spr"]);
	$smarty->assign("top_help", $lang["admin_help"]["ref_".$header]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_ref_table.tpl");
	exit;
}

function EditForm($par='', $err='')
{
	global $smarty, $dbconn, $config, $lang, $file_name, $header, $ref;

	AdminMainMenu($lang[$header."_spr"]);

	$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;

	if($err){
		$form["err"] = $err;
	}

	$rs = $dbconn->Execute("SELECT COUNT(id) FROM ".SPR_REF_TABLE." ");
	$ref_count = $rs->fields[0];

	$sorter_arr = array();
	for ($i = 0; $i < $ref_count; $i++) {
		$sorter_arr[$i]["sel"] = 0;
	}

	if($par != "add"){
		$id = isset($_REQUEST["id"]) ? intval($_REQUEST["id"]) : "";
		if($id == "" || $id <= 0){ ListItem(); return;}

		if($err == ""){
			$multi_lang = new MultiLang();
			$table_key = $multi_lang->TableKey(SPR_REF_TABLE);
			$name_temp = $multi_lang->SelectDefaultLangName($table_key, $id);
			$data["name"] = $name_temp["name"];
			$strSQL = "SELECT sorter FROM ".SPR_REF_TABLE." WHERE id='".$id."'";
			$rs = $dbconn->Execute($strSQL);
			$sorter_arr[$rs->fields[0]-1]["sel"] = "1";
		} else {
			$data = $_POST;
			$sorter_arr[$_POST["sorter"]-1]["sel"] = "1";
		}
		$form["hiddens"] = "<input type=hidden name=sel value=change>";
		$form["hiddens"] .= "<input type=hidden name=id value=".$id.">";
		$form["delete"] = $file_name."?sel=del&id=".$id."&page=".$page."&ref=".$ref;
		$smarty->assign("top_help", $lang["admin_help"]["ref_".$header."_edit"]);
	} else {
		if(!$err){
			$data["name"] = "";
			$sorter_arr[$ref_count]["sel"] = "1";
		}
		$form["hiddens"] = "<input type=hidden name=sel value=add>";
		$form["hiddens"] .= "<input type=hidden name=page value=".$page.">";
		$smarty->assign("top_help", $lang["admin_help"]["ref_".$header."_add"]);
	}
	$form["hiddens"] .= "<input type=hidden name=e value=1>";
	$form["hiddens"] .= "<input type=hidden name=ref value=".$ref.">";

	$form["back"] = $file_name."?page=".$page."&ref=".$ref;
	$form["action"] = $file_name;
	$form["par"] = $par;
	$form["confirm"] = $lang["confirm"]["reference"];

	$smarty->assign("sorter", $sorter_arr);

	$smarty->assign("form", $form);
	if (isset($data)) {
		$smarty->assign("data", $data);
	}
	$smarty->assign("header", $lang[$header."_spr"]);
	$smarty->assign("button", $lang["button"]);


	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_ref_form.tpl");
	exit;

}

function AddItem()
{
	global $dbconn, $lang, $e, $ref;
	
	$name = isset($_REQUEST["name"]) ? trim($_REQUEST["name"]) : "";
	$sorter = isset($_REQUEST["sorter"]) ? intval($_REQUEST["sorter"]) : 1;

	$e = isset($_REQUEST["e"]) ? intval($_REQUEST["e"]) : null;
	$err = "";

	$multi_lang = new MultiLang();
	
	$table_key = $multi_lang->TableKey(SPR_REF_TABLE);
	$field_name = $multi_lang->DefaultFieldName();

	if(strlen($name)<1){
		if ($e) {
			$err = $lang["err"]["invalid_name"];
		}
		EditForm("add", $err); return;
	}

	$strSQL = " SELECT COUNT(id) FROM ".REFERENCE_LANG_TABLE." WHERE ".$field_name." = '".Rep_Slashes($name)."' AND table_key='".$table_key."' ";
	$rs = $dbconn->Execute($strSQL);
	if($rs->fields[0]>0){
		if ($e) {
			$err = $lang["err"]["exists_option"];
		}
		EditForm("add",$err); return;
	}

	$strSQL = "INSERT INTO ".SPR_REF_TABLE." (name) VALUES ('".Rep_Slashes($name)."')";
	$dbconn->Execute($strSQL);

	$rs = $dbconn->Execute("SELECT MAX(id) FROM ".SPR_REF_TABLE."");
	$rs_os = $dbconn->Execute("SELECT MAX(sorter)+1  FROM ".SPR_REF_TABLE."");

	SprSorter($sorter, $rs_os->fields[0], $rs->fields[0]);

	$multi_lang->FirstLangInsert($table_key, $rs->fields[0], Rep_Slashes($name));
	ListItem();
	return;
}

function ChangeItem()
{
	global $dbconn, $lang, $file_name, $spr, $e, $file_name, $ref;

	$id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
	$name = isset($_POST["name"]) ? trim($_POST["name"]) : "";
	$sorter = isset($_POST["sorter"]) ? intval($_POST["sorter"]) : 1;
	$e = isset($_REQUEST["e"]) ? intval($_REQUEST["e"]) : null;

	if(strlen($name)<1){
		if ($e) {
			$err = $lang["err"]["invalid_name"];
		}
		EditForm("edit",$err); return ;
	}
	$strSQL = "UPDATE ".SPR_REF_TABLE." SET name='".Rep_Slashes($name)."' WHERE id='".$id."'";
	$dbconn->Execute($strSQL);

	$multi_lang = new MultiLang();
	
	$table_key = $multi_lang->TableKey(SPR_REF_TABLE);
	$multi_lang->SaveDefaultRefNames($table_key, $name, $id);

	$strSQL = " SELECT sorter FROM ".SPR_REF_TABLE." WHERE id = '".$id."' ";
	$rs = $dbconn->Execute($strSQL);
	$old_sorter = $rs->fields[0];

	SprSorter($sorter, $old_sorter, $id);
	ListItem();
	return;
}

function DelItem()
{
	global $dbconn, $ref;
	
	$id = isset($_REQUEST["id"]) ? intval($_REQUEST["id"]) : "";

	if($id == "" || $id <= 0){ ListItem(); return;}

	$strSQL = "DELETE FROM ".SPR_REF_TABLE." WHERE id='".$id."'";
	$dbconn->Execute($strSQL);

	$multi_lang = new MultiLang();
	
	$table_key = $multi_lang->TableKey(SPR_REF_TABLE);
	$multi_lang->DeleteRefName($id, $table_key);

	SprSorter("", 1);
	ListItem();
	return;
}

function ListOption($id_spr=null, $err="", $name="")
{
	global $smarty, $dbconn, $config, $page, $lang, $file_name, $ref, $header;

	AdminMainMenu($lang[$header."_spr"]);

	$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;

	if(!$id_spr){ ListSpr(); return;}

	$rs = $dbconn->Execute("SELECT name FROM ".SPR_REF_TABLE." WHERE id = '".$id_spr."'");
	$reference_name = $rs->fields[0];

	$multi_lang = new MultiLang();
	
	$table_key = $multi_lang->TableKey(VALUE_REF_TABLE);
	$field_name = $multi_lang->DefaultFieldName();

	$strSQL = "SELECT id, name FROM ".LANGUAGE_TABLE." ORDER BY id";
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$lang_link_arr = array();
	while(!$rs->EOF){
		$lang_link_arr[$i]["id"] = $rs->fields[0];
		$lang_link_arr[$i]["name"] = stripslashes($rs->fields[1]);
		$lang_link_arr[$i]["link"] = "./admin_translate.php?lang_code=".$rs->fields[0]."&key=".$table_key."&id_spr=".$id_spr;
		$rs->MoveNext();
		$i++;
	}
	$smarty->assign("lang_link", $lang_link_arr);

	$strSQL = "	SELECT DISTINCT a.id, b.".$field_name." AS name
				FROM ".VALUE_REF_TABLE." a
				LEFT JOIN ".REFERENCE_LANG_TABLE." b ON b.table_key='".$table_key."' AND b.id_reference=a.id
				WHERE a.id_spr='".$id_spr."' ORDER BY name ";
	$rs = $dbconn->Execute($strSQL);

	$i = 0;
	$spr_arr = array();
	if($rs->RowCount()>0){
		while(!$rs->EOF){
			$spr_arr[$i]["number"] = $i+1;
			$spr_arr[$i]["id"] = $rs->fields[0];
			$spr_arr[$i]["name"] = $rs->fields[1];
			$spr_arr[$i]["dellink"] = $file_name."?sel=delopt&page=".$page."&id=".$rs->fields[0]."&id_spr=".$id_spr."&ref=".$ref."&page=".$page;
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign("references", $spr_arr);
	}
	$smarty->assign("reference_name", $reference_name);
	$smarty->assign("back_link", $file_name."?ref=".$ref."&page=".$page);

	$smarty->assign("header", $lang[$header."_spr"]);

	if (!$err) {
		$name = "";
	}
	$form["hiddens"] = "<input type=hidden name=sel value=addopt>";
	$form["hiddens"] .= "<input type=hidden name=e value=1>";
	$form["hiddens"] .= "<input type=hidden name=page value=".$page.">";
	$form["hiddens"] .= "<input type=hidden name=ref value=".$ref.">";
	$form["hiddens"] .= "<input type=hidden name=id_spr value=".$id_spr.">";

	$form["err"] = $err;
	$form["action"] = $file_name;
	$form["confirm"] = $lang["confirm"]["reference"];

	$smarty->assign("name", $name);

	$smarty->assign("form", $form);
	$smarty->assign("button", $lang["button"]);
	$smarty->assign("top_help", $lang["admin_help"]["ref_".$header."_options"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_spr_option_table.tpl");
	exit;
}


function AddOption($id_spr=null)
{
	global $dbconn, $page, $lang, $ref, $header;

	$name = isset($_REQUEST["name"]) ? $_REQUEST["name"] : "";

	$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;

	if(!$id_spr){ListSpr(); return;}

	$multi_lang = new MultiLang();
	
	$table_key = $multi_lang->TableKey(VALUE_REF_TABLE);
	$field_name = $multi_lang->DefaultFieldName();

	$strSQL = "SELECT id FROM ".VALUE_REF_TABLE." WHERE id_spr='".intval($id_spr)."'";
	$rs = $dbconn->Execute($strSQL);
	$opts = array();
	while(!$rs->EOF){
		$opts[] = $rs->fields[0];
		$rs->MoveNext();
	}

	if (is_array($opts) && sizeof($opts)>0) {
		$opts_str = implode(", ", $opts);
		$strSQL = "	SELECT COUNT(id) FROM ".REFERENCE_LANG_TABLE."
					WHERE ".$field_name." = '".Rep_Slashes($name)."' AND id_reference in (".$opts_str.") AND table_key='".$table_key."' ";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0]>0){
			$err = $lang["err"]["exists_option"];
			ListOption($id_spr, $err, Rep_Slashes($name));
			return;
		}
	}

	if (strlen($name)>0){
		$strSQL = "INSERT INTO ".VALUE_REF_TABLE." (id_spr, name) VALUES ('".$id_spr."', '".Rep_Slashes($name)."')";
		$dbconn->Execute($strSQL);
		$rs = $dbconn->Execute(" SELECT MAX(id) FROM ".VALUE_REF_TABLE." ");
		$last_id = $rs->fields[0];
		$multi_lang->FirstLangInsert($table_key, $last_id, Rep_Slashes($name));
	}
	ListOption($id_spr);
	return;
}

function DelOption($id_spr)
{
	global $dbconn, $page, $e, $ref, $header;

	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : null;

	if (!$id){ ListOption($id_spr); return;}

	$strSQL = "DELETE FROM ".VALUE_REF_TABLE."  WHERE id='".$id."'";
	$dbconn->Execute($strSQL);

	$multi_lang = new MultiLang();
	$table_key = $multi_lang->TableKey(VALUE_REF_TABLE);
	$multi_lang->DeleteRefName($id, $table_key);

	ListOption($id_spr);
	return;
}

function SprSorter($sorter = "", $old_sorter, $id = "")
{
	global $dbconn;
	
	if(!$id){
		$strSQL = "SELECT id FROM ".SPR_REF_TABLE." WHERE sorter >= '".$old_sorter."' ORDER BY sorter";
		$rs = $dbconn->Execute($strSQL);
		$i = 0;
		while(!$rs->EOF){
			$i++;
			$rs_up = $dbconn->Execute("UPDATE ".SPR_REF_TABLE." SET sorter = '".$i."' WHERE id ='".$rs->fields[0]."' ");
			$rs->MoveNext();
		}
		return;
	}
	if($old_sorter<$sorter){
		$strSQL = "select id, sorter from ".SPR_REF_TABLE." where sorter >= '".$old_sorter."' and  sorter <= '".$sorter."'  order by sorter";
		$rs = $dbconn->Execute($strSQL);
		while(!$rs->EOF){
			$rs_up = $dbconn->Execute("update ".SPR_REF_TABLE." set sorter = '".($rs->fields[1]-1)."' where id ='".$rs->fields[0]."' ");
			$rs->MoveNext();
		}
		$rs_up = $dbconn->Execute("update ".SPR_REF_TABLE." set sorter = '".$sorter."' where id ='".$id."' ");
	}elseif($old_sorter>$sorter){
		$strSQL = "select id, sorter from ".SPR_REF_TABLE." where sorter <= '".$old_sorter."' and  sorter >= '".$sorter."' order by sorter";
		$rs = $dbconn->Execute($strSQL);
		while(!$rs->EOF){
			$rs_up = $dbconn->Execute("update ".SPR_REF_TABLE." set sorter = '".($rs->fields[1]+1)."' where id ='".$rs->fields[0]."' ");
			$rs->MoveNext();
		}
		$rs_up = $dbconn->Execute("update ".SPR_REF_TABLE." set sorter = '".$sorter."' where id ='".$id."' ");
	}else{
		$rs_up = $dbconn->Execute("update ".SPR_REF_TABLE." set sorter = '".$sorter."' where id ='".$id."' ");
	}
	return;
}

?>