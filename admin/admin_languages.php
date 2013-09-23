<?php

/**
* Site Languages administration (add new language, editing existing language files, delete language from site).
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
IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "language_spr");

$sel = $_GET["sel"]?$_GET["sel"]:$_POST["sel"];

switch($sel){
	case "add": AddLanguage(); break;
	case "del": DelLanguage(); break;
	default: ListLanguage();
}

function ListLanguage($err="", $name="")
{
	global $smarty, $dbconn, $config, $config_admin, $page, $lang;

	if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
	$file_name = "admin_languages.php";

	AdminMainMenu($lang["language_spr"]);

	$page = $_GET["page"]?$_GET["page"]:$_POST["page"];

	if( (strval($page) == "") || (strval($page) == "0")){ $page = 1;}

	$strSQL = "select count(*) from ".LANGUAGE_SPR_TABLE;
	$rs = $dbconn->Execute($strSQL);
	$num_records = $rs->fields[0];

	$lim_min = ($page-1)*$config_admin["reference_numpage"];
	$lim_max = $config_admin["reference_numpage"];
	$limit_str = " limit ".$lim_min.", ".$lim_max;

	$multi_lang = new MultiLang();
	
	$table_key = $multi_lang->TableKey(LANGUAGE_SPR_TABLE);
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

	$strSQL = "select distinct a.id, b.".$field_name." as name from ".LANGUAGE_SPR_TABLE." a left join ".REFERENCE_LANG_TABLE." b on b.table_key='".$table_key."' and b.id_reference=a.id order by name ".$limit_str;
	$rs = $dbconn->Execute($strSQL);

	$i = 0;
	$spr_arr = array();
	if ($rs->RowCount() > 0) {
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$spr_arr[$i]["number"] = ($page-1)*$config_admin["reference_numpage"]+($i+1);
			$spr_arr[$i]["id"] = $row["id"];
			$spr_arr[$i]["name"] = $row["name"];
			$spr_arr[$i]["deletelink"] = $file_name."?sel=del&page=".$page."&id=".$row["id"];
			$rs->MoveNext();
			$i++;
		}
		$param = $file_name."?";
		$smarty->assign("links", GetLinkStr($num_records,$page,$param,$config_admin["reference_numpage"]));
		$smarty->assign("languages", $spr_arr);
	}
	///	form
	if (!$err) {
		$name = "";
	}

	$form["hiddens"] = "<input type=hidden name=sel value=add>";
	$form["hiddens"] .= "<input type=hidden name=page value=".$page.">";

	$form["action"] = $file_name;
	$form["err"] = $err;
	$form["confirm"] = $lang["confirm"]["languages"];

	$smarty->assign("name", $name);

	$smarty->assign("form", $form);
	$smarty->assign("button", $lang["button"]);


	$smarty->assign("header", $lang["language_spr"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_language_table.tpl");
	exit;
}

//////////////  add language function /////////////////////////////////////////////////////////////
function AddLanguage()
{
	global $dbconn, $page, $lang;
	
	$name = $_POST["name"];
	$page = intval($_POST["page"]);
	
	$multi_lang = new MultiLang();
	
	$table_key = $multi_lang->TableKey(LANGUAGE_SPR_TABLE);
	$field_name = $multi_lang->DefaultFieldName();
	
	$strSQL = "select count(*) from ".REFERENCE_LANG_TABLE." where ".$field_name." = '".Rep_Slashes($name)."' and table_key='".$table_key."'";
	$rs = $dbconn->Execute($strSQL);
	
	if ($rs->fields[0] > 0) {
		$err = $lang["err"]["exists_language"];
		ListLanguage($err, Rep_Slashes($name)); return;
	}
	
	if (strlen($name) > 0) {
		$strSQL = "insert into ".LANGUAGE_SPR_TABLE." (name) values ('".Rep_Slashes($name)."')";
		$dbconn->Execute($strSQL);
		$rs=$dbconn->Execute("Select max(id) from ".LANGUAGE_SPR_TABLE." ");
		$last_id = $rs->fields[0];
		$multi_lang->FirstLangInsert($table_key, $last_id, Rep_Slashes($name));
	}

	ListLanguage();
	return;
}
//////////////  del language  /////////////////////////////////////////////////////////////
function DelLanguage()
{
	global $dbconn;
	
	$id = $_POST["id"] ? $_POST["id"] : $_GET["id"];
	
	if (!$id) {
		ListLanguage();
		return;
	}
	
	$strSQL = "delete from ".LANGUAGE_SPR_TABLE."  where id='".$id."'";
	$dbconn->Execute($strSQL);
	
	$multi_lang = new MultiLang();
	
	$table_key = $multi_lang->TableKey(LANGUAGE_SPR_TABLE);
	$multi_lang->DeleteRefName($id, $table_key);
	
	ListLanguage();
	return;
}
?>