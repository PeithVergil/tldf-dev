<?php
	include "../include/config.php";
	include_once "../common.php";
	include "../include/config_admin.php";
	include "../include/functions_auth.php";
	include "../include/functions_admin.php";
	include_once "../include/class.ado.php";

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
		global $dbconn, $config;

		$lang_code = intval($_POST["lang_code"]);
		$key = intval($_POST["key"]);
		$id_spr = intval($_POST["id_spr"]);

		$sel = isset($_REQUEST['type']) ? $_REQUEST['type'] : "";

		switch ($sel) {
			case 'fields':
				$objAdo = new Ado($dbconn, $config);
				$values=$_POST['lang'];
				foreach ($values as $id_ref=>$value) {
					$strSQL="UPDATE ".REFERENCE_LANG_TABLE." SET lang_".$lang_code."='".addslashes(trim($value))."' WHERE id_reference=".$id_ref." AND table_key=".$objAdo->KEY_FIELDS;							
					$dbconn->Execute($strSQL);
				}
			break;
			default:
				$multi_lang = new MultiLang();
				$multi_lang->SaveNames($key, $lang_code, $_POST["lang"]);
			break;
		}

		$_GET["lang_code"] = $lang_code;
		$_GET["key"] = $key;
		$_GET["id_spr"] = $id_spr;
		WriteLanguage("close");
	}

	function WindowLanguage()
	{
		global $smarty, $config, $lang;

		$file_name = "admin_translate.php";

		AdminMainMenu($lang["language_spr"]);

		$lang_code = $_GET["lang_code"];
		$key = $_GET["key"];
		$id_spr = isset($_REQUEST["id_spr"]) ? $_REQUEST["id_spr"] : "";

		$sel = isset($_REQUEST['type']) ? $_REQUEST['type'] : "";
		if ($sel!=''){
			switch ($sel){
				case 'blocks': $custom_str='&type='.$sel.'&id_form='.intval($_REQUEST['id_form']); break;
				case 'fields': $custom_str='&type='.$sel.'&id_sec='.intval($_REQUEST['id_sec']); break;
			}
		}
		else $custom_str='';
		$form["type"] = "main_page";
		$form["frame_link_1"] = "./".$file_name."?sel=read&lang_code=".$config["default_lang"]."&key=".$key."&id_spr=".$id_spr.$custom_str;
		$form["frame_link_2"] = "./".$file_name."?sel=write&lang_code=".$lang_code."&key=".$key."&id_spr=".$id_spr.$custom_str;
		$form["frame_link_3"] = "./".$file_name."?sel=butt&key=".$key."&id_spr=".$id_spr.$custom_str;


		$smarty->assign("form", $form);
		$smarty->assign("button", $lang["button"]);
		$smarty->assign("header", $lang["language_spr"]);

		$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_translate_table.tpl");
		exit;
	}

	function ButtLanguage()
	{
		global $smarty, $config, $lang;

		AdminMainMenu($lang["language_spr"]);
		
		// not in use
		## $lang_code = isset($_GET["lang_code"]) ? intval($_GET["lang_code"]) : 1;
		## $key = $_GET["key"];

		$form["type"] = "frame_buttons";

		$smarty->assign("form", $form);
		$smarty->assign("button", $lang["button"]);
		$smarty->assign("header", $lang["language_spr"]);

		$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_translate_table.tpl");
		exit;
	}
	
	function ReadLanguage()
	{
		global $smarty, $dbconn, $config, $lang;

		AdminMainMenu($lang["language_spr"]);

		$lang_code = intval($_GET["lang_code"]);
		$key = intval($_GET["key"]);
		$id_spr = intval($_GET["id_spr"]);

		$sel= isset($_REQUEST['type']) ? $_REQUEST['type'] : "";
		switch ($sel){
			case 'fields':
						$id_sec=intval($_REQUEST['id_sec']);
						$config['default_lang']=$lang_code;
						$objAdo=new Ado($dbconn, $config);
						$fields=$objAdo->getSectionAttributes($id_sec);
						$lang=array();
						foreach ($fields as $index=>$data){
							$lang[$index]['id']=$data['id'];
							$lang[$index]['name']=$data['field_name'];
						}
						$smarty->assign("lang_arr", $lang);
						break;
			default:
				$multi_lang = new MultiLang();
				if ($id_spr != 0) {
					$id_arr = array();
					$id_arr = $multi_lang->ValuesIdArray($id_spr, $key);
					$id_str = implode(" ,", $id_arr);
					if (strlen($id_str) > 0) {
						$where_str = "id_reference in (".$id_str.")";
					}
					$smarty->assign("lang_arr", $multi_lang->SelectDiffLangList($key, $lang_code, $where_str));
				} else {
					$smarty->assign("lang_arr", $multi_lang->SelectDiffLangList($key, $lang_code));
				}
			break;
		}

		$strSQL = "select charset, name, code from ".LANGUAGE_TABLE." where id='".$lang_code."'";
		$rs = $dbconn->Execute($strSQL);

		$form["charset"] = $rs->fields[0];
		$form["language"] = $rs->fields[1];
		$form["lang"] = $rs->fields[2];
		$form["type"] = "frame_read";

		$smarty->assign("form", $form);
		$smarty->assign("button", $lang["button"]);
		$smarty->assign("header", $lang["language_spr"]);

		$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_translate_table.tpl");
		exit;
	}
	
	function WriteLanguage($act="")
	{
		global $smarty, $dbconn, $config, $lang;

		$file_name = "admin_translate.php";

		AdminMainMenu($lang["language_spr"]);

		$lang_code = intval($_GET["lang_code"]);
		$key = intval($_GET["key"]);
		$id_spr = intval($_GET["id_spr"]);

		$sel = isset($_REQUEST['type']) ? $_REQUEST['type'] : "";
		$custom_str='';
		switch ($sel) {
			case 'fields':
				$id_sec = intval($_REQUEST['id_sec']);
				$config['default_lang'] = $lang_code;
				$objAdo = new Ado($dbconn, $config);
				$fields = $objAdo->getSectionAttributes($id_sec);
				$lang = array();
				foreach ($fields as $index => $data) {
					$lang[$index]['id'] = $data['id'];
					$lang[$index]['name'] = $data['field_name'];
				}
				$smarty->assign("lang_arr", $lang);
				$custom_str = '?type='.$sel.'&id_sec='.intval($_REQUEST['id_sec']);
			break;
			default:
				$multi_lang = new MultiLang();
				if ($id_spr != 0) {
					$id_arr = array();
					$id_arr = $multi_lang->ValuesIdArray($id_spr, $key);
					$id_str = implode(" ,", $id_arr);
					if (strlen($id_str) > 0) {
						$where_str = "id_reference in (".$id_str.")";
					}
					$smarty->assign("lang_arr", $multi_lang->SelectDiffLangList($key, $lang_code, $where_str));
				} else {
					$smarty->assign("lang_arr", $multi_lang->SelectDiffLangList($key, $lang_code));
				}
			break;
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

		$form["action"] = $file_name.$custom_str;
		$form["hidden"] = "<input type=hidden name=lang_code value=".$lang_code.">";
		$form["hidden"] .= "<input type=hidden name=key value=".$key.">";
		$form["hidden"] .= "<input type=hidden name=id_spr value=".$id_spr.">";
		$form["hidden"] .= "<input type=hidden name=sel value=save>";

		$smarty->assign("form", $form);
		$smarty->assign("button", $lang["button"]);
		$smarty->assign("header", $lang["language_spr"]);

		$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_translate_table.tpl");
		exit;
	}

?>