<?php

/**
* References information for translate.
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

ini_set('display_errors', '0');

$auth = auth_user();
login_check($auth);

$sel = isset($_REQUEST["sel"]) ? $_REQUEST["sel"] : "";

switch($sel){
	case "read": ReadLanguage();
	case "write": WriteLanguage();
	case "save": SaveLanguage();
	case "butt": ButtLanguage();
	case "save": SaveLanguage();
	default: WindowLanguage();
}

function SaveLanguage()
{
	global $dbconn;

	$lang_code = intval($_POST["lang_code"]);
	$table_key = intval($_POST['table_key']);
	$id_info = intval($_REQUEST['id_info']);
	$content = $_POST['code'];
	$title = $_POST['name'];

	if($_REQUEST['add']==1){
		$strSQL="INSERT INTO ".INFO_LANG_CONTENT_TABLE."(table_key, id_info, name, content, id_lang) VALUES(".$table_key.", ".$id_info.", '".addslashes($title)."', '".addslashes($content)."', ".$lang_code.")";
	}
	else {
		$strSQL="UPDATE ".INFO_LANG_CONTENT_TABLE." SET name='".addslashes($title)."', content='".addslashes($content)."' WHERE id_info=".$id_info." AND id_lang=".$lang_code." AND table_key=".$table_key;
	}

	$dbconn->Execute($strSQL);

	$_GET["lang_code"] = $lang_code;
	$_GET["id_info"] = $id_info;
	$_GET["table_key"] = $table_key;

	WriteLanguage("close");
}

function WindowLanguage()
{
	global $smarty, $config, $lang;

	$file_name = "admin_translate.php";

	AdminMainMenu($lang["language_spr"]);

	$lang_code = $_GET["id_lang"];
	$id_info= $_GET["id_info"];
	$table_key= intval($_GET["table_key"]);

	$form["type"] = "main_page";
	$form["frame_link_1"] = "./".$file_name."?sel=read&lang_code=".$config["default_lang"]."&id_info=".$id_info."&table_key=".$table_key;
	$form["frame_link_2"] = "./".$file_name."?sel=write&lang_code=".$lang_code."&id_info=".$id_info."&table_key=".$table_key;
	$form["frame_link_3"] = "./".$file_name."?sel=butt&key=".$key."&id_spr=".$id_spr;


	$smarty->assign("form", $form);
	$smarty->assign("button", $lang["button"]);
	$smarty->assign("header", $lang["language_spr"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_translate_info_table.tpl");
	exit;
}

function ButtLanguage()
{
	global $smarty, $config, $lang;

	AdminMainMenu($lang["language_spr"]);

	$lang_code = $_GET["lang_code"];

	$form["type"] = "frame_buttons";

	$smarty->assign("form", $form);
	$smarty->assign("button", $lang["button"]);
	$smarty->assign("header", $lang["language_spr"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_translate_info_table.tpl");
	exit;
}

function ReadLanguage()
{
	global $smarty, $dbconn, $config, $lang;

	AdminMainMenu($lang["language_spr"]);

	$lang_code = intval($_GET["lang_code"]);
	$id_info=intval($_GET['id_info']);
	$table_key=intval($_GET['table_key']);

	//Get page content
	switch ($table_key){
		case 1:
			$table=INFO_CONTENT_TABLE;
			$name_col='name';
			$content_col='content';
			break;
		case 2:
			$table=ADVICES_CATEGORIES_TABLE;
			$name_col='name';
			$content_col='description';
			break;
		case 3:
			$table=ADVICES_TABLE;
			$name_col='title';
			$content_col='body';
			break;
		case 4:
			$table=HELP_CATEGORIES_TABLE;
			$name_col='name';
			$content_col='description';
			break;
		case 5:
			$table=HELP_QUESTIONS_TABLE;
			$name_col='title';
			$content_col='body';
			break;
	}
	$strSQL="SELECT a.".$name_col." AS name, a.".$content_col." AS content, b.name AS name_lang, b.content AS content_lang
				FROM ".$table." a
				LEFT JOIN ".INFO_LANG_CONTENT_TABLE." b ON a.id=b.id_info AND b.id_lang=".$config['default_lang']." AND b.table_key=".$table_key."
				WHERE a.id=".$id_info;
	$rs=$dbconn->Execute($strSQL);
	$row=$rs->GetRowAssoc(false);

	$data["name"] = $row["name_lang"] ? stripslashes($row["name_lang"]) : stripslashes($row["name"]);
	$data["content"] = $row["content_lang"] ? stripslashes($row["content_lang"]) : stripslashes($row["content"]);

	$strSQL = "select charset, name, code from ".LANGUAGE_TABLE." where id='".$lang_code."'";
	$rs = $dbconn->Execute($strSQL);

	$form["charset"] = $rs->fields[0];
	$form["language"] = $rs->fields[1];
	$form["lang"] = $rs->fields[2];
	$form["type"] = "frame_read";

	$smarty->assign("form", $form);
	$smarty->assign("data", $data);
	$smarty->assign("button", $lang["button"]);
	$smarty->assign("header", $lang["language_spr"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_translate_info_table.tpl");
	exit;
}
function WriteLanguage($act="")
{
	global $smarty, $dbconn, $config, $lang;
	global $spaw_root, $spaw_dir, $sp;

	$file_name = "admin_translate.php";

	AdminMainMenu($lang["language_spr"]);

	$lang_code = intval($_GET["lang_code"]);
	$id_info=intval($_GET['id_info']);
	$table_key=intval($_GET['table_key']);
	//Get page content
	switch ($table_key){
		case 1:
			$table=INFO_CONTENT_TABLE;
			$name_col='name';
			$content_col='content';
			break;
		case 2:
			$table=ADVICES_CATEGORIES_TABLE;
			$name_col='name';
			$content_col='description';
			break;
		case 3:
			$table=ADVICES_TABLE;
			$name_col='title';
			$content_col='body';
			break;
		case 4:
			$table=HELP_CATEGORIES_TABLE;
			$name_col='name';
			$content_col='description';
			break;
		case 5:
			$table=HELP_QUESTIONS_TABLE;
			$name_col='title';
			$content_col='body';
			break;
	}
	$strSQL="SELECT a.".$name_col." AS name, a.".$content_col." AS content, b.name AS name_lang, b.content AS content_lang
				FROM ".$table." a
				LEFT JOIN ".INFO_LANG_CONTENT_TABLE." b ON a.id = b.id_info AND b.id_lang = ".$lang_code." AND b.table_key=".$table_key."
				WHERE a.id = '".$id_info."'";
	$rs=$dbconn->Execute($strSQL);
	if($rs->RowCount()>0){
		$row=$rs->GetRowAssoc(false);
		$data["name"] = $row["name_lang"] ? stripslashes($row["name_lang"]) : stripslashes($row["name"]);
		$data["content"] = $row["content_lang"] ? stripslashes($row["content_lang"]) : stripslashes($row["content"]);
		$add= $row["name_lang"] ? 0 : 1;
	}

	$data['id_info']=$id_info;

	if (RICH_TEXT_EDITOR == 'SPAW-1')
	{
		//Init SPAW
		$spaw_root = $config['site_path'].'/spaw/';
		// include the control file
		include $spaw_root.'spaw_control.class.php';
		// pass $demo_array to the constructor
		$sw = new SPAW_Wysiwyg(
			'code',									/*name*/
			html_entity_decode($data['content']),	/*value*/
			'en',									/*language*/
			'full',									/*toolbar mode*/
			'default',								/*theme*/
			'100%',									/*width*/
			'100%',									/*height*/
			'',										/*stylesheet file*/
			$spaw_dropdown_data						/*dropdown data*/
		);
		$smarty->assign('editor', $sw->show());
	}
	elseif (RICH_TEXT_EDITOR == 'SPAW-2')
	{
		$spaw_root = $config['site_path'].'/spaw2/';
		include $spaw_root.'spaw_control.class.php';
		$sw = new SPAW_Wysiwyg(
			'code',									/*name*/
			html_entity_decode($data['content']),	/*value*/
			'en',									/*language*/
			'full',									/*toolbar mode*/
			'default',								/*theme*/
			'700px',								/*width*/
			'500px',								/*height*/
			'',										/*stylesheet file*/
			$spaw_dropdown_data						/*dropdown data (is NULL)*/
		);
		$smarty->assign('editor', $sw->getHTML());
	}

	if($act == "close"){
		$form["close"] = 1;
	}

	$strSQL = "select charset, name, code from ".LANGUAGE_TABLE." where id='".$lang_code."'";
	$rs = $dbconn->Execute($strSQL);

	$form["charset"] = $rs->fields[0];
	$form["language"] = $rs->fields[1];
	$form["lang"] = $rs->fields[2];
	$form["type"] = "frame_save";

	$form["action"] = $file_name;
	$form["hidden"] = "<input type=hidden name=lang_code value=".$lang_code.">";
	$form["hidden"] .= "<input type=hidden name=add value=".$add.">";
	$form["hidden"] .= "<input type=hidden name=id_info value=".$id_info.">";
	$form["hidden"] .= "<input type=hidden name=sel value=save>";
	$form["hidden"] .= "<input type=hidden name=table_key value=".$table_key.">";

	if ($data['id_info']==1) {
		$form["hidden"] .= "<input type=hidden name=name value=\"".$data["name"]."\">";
	}
	$smarty->assign("table_key", $table_key);
	$smarty->assign("form", $form);
	$smarty->assign("data", $data);
	$smarty->assign("button", $lang["button"]);
	$smarty->assign("header", $lang["language_spr"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_translate_info_table.tpl");
	exit;
}

?>