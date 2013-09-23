<?php
/**
* Site Advice management (creating, deleting, editing)
*
* @package DatingPro
* @subpackage Admin Mode
**/
include '../include/config.php';
include_once '../common.php';
include '../include/config_admin.php';
include '../include/functions_auth.php';
include '../include/functions_admin.php';
include '../include/class.help_info.php';

$auth = auth_user();
login_check($auth);
IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "advices");

if(isset($_SERVER["PHP_SELF"]))
$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
else
$file_name = "admin_advice.php";

$sel = isset($_REQUEST["sel"]) ? $_REQUEST["sel"] : "";
$par = isset($_REQUEST["par"]) ? $_REQUEST["par"] : (isset($_SESSION["par"]) ? $_SESSION["par"] : "advice");

switch ($par) {
	case "faq":
		$_SESSION["par"] = "faq";
		$local_config['category_table'] = HELP_CATEGORIES_TABLE;
		$local_config['category_table_key'] = 4;
		$local_config['item_table'] = HELP_QUESTIONS_TABLE;
		$local_config['item_table_key'] = 5;
		$local_config['to_lang'] = 'faq';
		$local_config['list_category_tpl'] = 'admin_faq_categories_table';
		$local_config['list_item_tpl'] = 'admin_faq_table';
		$local_config['form_category_tpl'] = 'admin_faq_categories_form';
		$local_config['form_item_tpl'] = 'admin_faq_form';
		$local_config['use_status'] = '1';
		break;
	case "advice":
		$_SESSION["par"] = "advice";
		$local_config['category_table'] = ADVICES_CATEGORIES_TABLE;
		$local_config['category_table_key'] = 2;
		$local_config['item_table'] = ADVICES_TABLE;
		$local_config['item_table_key'] = 3;
		$local_config['to_lang'] = 'advices';
		$local_config['list_category_tpl'] = 'admin_advice_categories_table';
		$local_config['list_item_tpl'] = 'admin_advice_table';
		$local_config['form_category_tpl'] = 'admin_advice_categories_form';
		$local_config['form_item_tpl'] = 'admin_advice_form';
		$local_config['use_status'] = '0';
		break;
}
$smarty->assign('local_config', $local_config);
$helpinfo = new HelpInfo($config, $dbconn, $file_name, $local_config);

switch($sel) {
	case "edit_cat": 	EditCategory("edit"); break;
	case "new_cat": 	EditCategory("new"); break;
	case "del_cat": 	DelCategory(); break;
	case "save_cat": 	ChangeCategory("save"); break;
	case "add_cat": 	ChangeCategory("add"); break;
	case "list_item": 	ListAdvice(); break;
	case "edit_item": 	EditAdvice("edit"); break;
	case "new_item": 	EditAdvice("new"); break;
	case "del_item": 	DelAdvice(); break;
	case "save_item": 	ChangeAdvice("save"); break;
	case "add_item":	ChangeAdvice("add"); break;
	default:		ListCategory();
}

function ListCategory($err=""){
	global $smarty, $config, $lang, $file_name, $local_config, $helpinfo;
	AdminMainMenu($lang[$local_config['to_lang']]);

	$form["add_link"] = "./".$file_name."?sel=new_cat";
	if ($err) $form["err"] = $err;
	$smarty->assign("form", $form);

	$smarty->assign("cat_list", $helpinfo->GetCategoriesList());
	$smarty->display(TrimSlash($config["admin_theme_path"])."/".$local_config['list_category_tpl'].".tpl");
	exit;
}

function ListAdvice($err=""){
	global $smarty, $config, $lang, $file_name, $local_config, $helpinfo;

	AdminMainMenu($lang[$local_config['to_lang']]);

	$cat_id = isset($_REQUEST["id"]) ? intval($_REQUEST["id"]) : 0;
	if ($cat_id > 0) {
		$smarty->assign("adv_list", $helpinfo->GetItemsList($cat_id));
		$form = $helpinfo->GetCategoryInfo($cat_id);
		$form["add_link"] = "./".$file_name."?sel=new_item&id=".$form["category_id"];
		$form["back_link"] = "./".$file_name."?sel=list_cat";
		if ($err) $form["err"] = $err;
		$smarty->assign("form", $form);
		$smarty->display(TrimSlash($config["admin_theme_path"])."/".$local_config['list_item_tpl'].".tpl");
		exit;
	} else {
		ListCategory(); return;
	}
}

function EditCategory($type, $err="")
{
	global $smarty, $config, $lang, $file_name, $spaw_root, $spaw_dir, $spaw_base_url, $spaw_lang_data, $local_config, $helpinfo;

	AdminMainMenu($lang[$local_config['to_lang']]);

	if($type == "edit"){
		$id = isset($_REQUEST["id"]) ? intval($_REQUEST["id"]) : "";
		$form = $helpinfo->GetCategoryEditInfo($id);
		$smarty->assign("lang_link", $helpinfo->GetLanguageLinks($local_config['category_table_key'], $id));
	} else {
		$form["save_link"] = "./".$file_name."?sel=add_cat";
		$form["descr"] = "";
	}

	if (RICH_TEXT_EDITOR == 'SPAW-1')
	{
		$spaw_root = $config['site_path'].'/spaw/';
		include $spaw_root.'spaw_control.class.php';
		$sw = new SPAW_Wysiwyg(
			'code',								/*name*/
			html_entity_decode($form['descr']),	/*value*/
			'en',								/*language*/
			'full',								/*toolbar mode*/
			'default',							/*theme*/
			'100%',								/*width*/
			'100%',								/*height*/
			'',									/*stylesheet file*/
			$spaw_dropdown_data					/*dropdown data*/
		);
		$smarty->assign('editor', $sw->show());
	}
	elseif (RICH_TEXT_EDITOR == 'SPAW-2')
	{
		$spaw_root = $config['site_path'].'/spaw2/';
		include $spaw_root.'spaw_control.class.php';
		$sw = new SPAW_Wysiwyg(
			'code',								/*name*/
			html_entity_decode($form['descr']),	/*value*/
			'en',								/*language*/
			'full',								/*toolbar mode*/
			'default',							/*theme*/
			'700px',							/*width*/
			'500px',							/*height*/
			'',									/*stylesheet file*/
			$spaw_dropdown_data					/*dropdown data (is NULL)*/
		);
		$smarty->assign('editor', $sw->getHTML());
	}

	$form["back_link"] = "./".$file_name."?sel=list_cat";
	$form["type"] = $type;
	if ($err) $form["err"] = $err;

	$smarty->assign("button", $lang["button"]);
	$smarty->assign("form", $form);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/".$local_config['form_category_tpl'].".tpl");
	exit;
}

function EditAdvice($type, $err=""){
	global $smarty, $config, $lang, $file_name, $spaw_root, $spaw_dir, $spaw_base_url, $local_config, $helpinfo;

	AdminMainMenu($lang[$local_config['to_lang']]);

	if ($type == "edit"){
		$id = isset($_REQUEST["id"]) ? intval($_REQUEST["id"]) : "";
		$form = $helpinfo->GetItemEditInfo($id);
		$cat_id = $form["category_id"];
		$smarty->assign("lang_link", $helpinfo->GetLanguageLinks($local_config['item_table_key'], $id));
	} else {
		$cat_id = isset($_REQUEST["id"]) ? intval($_REQUEST["id"]) : "";
		$form = $helpinfo->GetCategoryInfo($cat_id);
		$form["save_link"] = "./".$file_name."?sel=add_item&id=".$cat_id;
		$form["body"] = "";
	}
	$form["back_link"] = "./".$file_name."?sel=list_item&id=".$cat_id;
	$form["type"] = $type;
	if ($err) $form["err"] = $err;

	if (RICH_TEXT_EDITOR == 'SPAW-1')
	{
		$spaw_root = $config['site_path'].'/spaw/';
		include $spaw_root.'spaw_control.class.php';
		$sw = new SPAW_Wysiwyg(
			'code',								/*name*/
			html_entity_decode($form['body']),	/*value*/
			'en',								/*language*/
			'full',								/*toolbar mode*/
			'default',							/*theme*/
			'100%',								/*width*/
			'100%',								/*height*/
			'',									/*stylesheet file*/
			$spaw_dropdown_data					/*dropdown data*/
		);
		$smarty->assign('editor', $sw->show());
	}
	elseif (RICH_TEXT_EDITOR == 'SPAW-2')
	{
		$spaw_root = $config['site_path'].'/spaw2/';
		include $spaw_root.'spaw_control.class.php';
		$sw = new SPAW_Wysiwyg(
			'code',								/*name*/
			html_entity_decode($form['body']),	/*value*/
			'en',								/*language*/
			'full',								/*toolbar mode*/
			'default',							/*theme*/
			'700px',							/*width*/
			'500px',							/*height*/
			'',									/*stylesheet file*/
			$spaw_dropdown_data					/*dropdown data (is NULL)*/
		);
		$smarty->assign('editor', $sw->getHTML());
	}
	
	$smarty->assign("button", $lang["button"]);

	$smarty->assign("form", $form);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/".$local_config['form_item_tpl'].".tpl");
	exit;
}

function DelCategory(){
	global $smarty, $dbconn, $config, $lang, $local_config, $helpinfo;

	$id = isset($_REQUEST['id']) && intval($_REQUEST['id']) > 0 ? intval($_REQUEST['id']) : null;

	if ($id != null) {
		$helpinfo->DeleteCategory($id);
	}
	ListCategory();
	return;
}

function DelAdvice(){
	global $smarty, $dbconn, $config, $lang, $local_config, $helpinfo;

	$id = isset($_REQUEST['id']) && intval($_REQUEST['id']) > 0 ? intval($_REQUEST['id']) : null;
	if ($id != null) {
		$rs = $dbconn->Execute("SELECT id_category FROM ".$local_config['item_table']." WHERE id='".$id."'");
		$_REQUEST["id"] = $rs->fields[0];
		$helpinfo->DeleteItem($id);
	}
	ListAdvice();
	return;
}

function ChangeCategory($type){
	global $smarty, $dbconn, $config, $lang, $local_config, $helpinfo;
	if (isset($_POST["name"]) && $_POST["name"]) {
		$helpinfo->SaveCategory($type);
	}
	ListCategory();
	return;
}

function ChangeAdvice($type){
	global $smarty, $dbconn, $config, $lang, $local_config, $helpinfo;

	if (isset($_POST["title"]) && $_POST["title"] && isset($_POST["code"]) && $_POST["code"]) {
		$cat_id = $helpinfo->SaveItem($type);
	}
	$_REQUEST["id"] = $cat_id;
	ListAdvice();
	return;
}

?>