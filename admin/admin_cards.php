<?php

/**
*
* ECards management section
*
* @package DatingPro
* @subpackage Admin Mode
*
**/

include '../include/config.php';
include_once '../common.php';
include '../include/config_admin.php';
include '../include/functions_auth.php';
include '../include/functions_admin.php';
include '../include/class.images.php';

$auth = auth_user();
login_check($auth);
IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "groups");

AdminMainMenu($lang['cards']['admin']);

$sel = isset($_REQUEST['sel']) ? $_REQUEST['sel'] :  "";

switch ($sel) {
	case 'catalog': CategoriesList(); break;
	case 'subcategories': 	SubCategoriesList(); break;
	case 'add_category': 	CategoryForm('add'); break;
	case 'edit_category':	CategoryForm('edit'); break;
	case 'add_subcategory': 	SubCategoryForm('add'); break;
	case 'edit_subcategory':	SubCategoryForm('edit'); break;
	case 'save_category':	SaveCategory(); break;
	case 'save_subcategory':	SaveSubCategory(); break;
	case 'del_category': 	DelCategory(); break;
	case 'del_subcategory': 	DelSubCategory(); break;
	case 'settings_songs': 	ShowSettings('songs'); break;
	case 'settings_import': 	ShowSettings('import'); break;
	case 'add_song': 	SongForm('add'); break;
	case 'edit_song': 	SongForm('edit'); break;
	case 'save_song': 	SaveSong(); break;
	case 'del_song': 	DeleteSong(); break;
	case 'items': 	CardsList(); break;
	case 'add_card': 	CardForm('add'); break;
	case 'edit_card': 	CardForm('edit'); break;
	case 'ajax_subcategories': AjaxSubcategories(); break;
	case 'save_card': SaveCard(); break;
	case 'del_card': DelCard(); break;
	case 'orders': OrdersList(); break;
	default: CategoriesList(); break;
}

function CategoriesList(){
	global $smarty, $config, $dbconn, $lang;

	$categories_per_page = 10;
	$form['file_name'] = "admin_cards.php";

	$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;

	$strSQL = "SELECT COUNT(*) FROM ".ECARDS_CATEGORIES_TABLE." ";
	$rs = $dbconn->Execute($strSQL);

	$num_categories = $rs->fields[0];

	$lim_min = ($page - 1) * $categories_per_page;
	$lim_max = $categories_per_page;
	$limit_str = " LIMIT ".$lim_min.", ".$lim_max;

	$strSQL = "SELECT DISTINCT a.id, a.category_name AS name, a.category_image, a.category_descr AS descr, b.content_name AS name_lang, b.content_body AS descr_lang
				FROM ".ECARDS_CATEGORIES_TABLE." a
				LEFT JOIN ".ECARDS_LANG_CONTENT_TABLE." b ON b.content_id=a.id AND b.content_type='1' AND b.id_lang=".$config['default_lang']."
				GROUP BY a.id ORDER BY a.sorter ".$limit_str;
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] > 0) {
		$i = 0; $_arr = array();
		while (!$rs->EOF) {
			$_arr[$i]["id"] = $rs->fields[0];
			$_arr[$i]["name"] = $rs->fields[4] ? stripslashes($rs->fields[4]) : stripslashes($rs->fields[1]);
			if ($rs->fields[2] != "" && file_exists($config["site_path"]."/uploades/ecards/".$rs->fields[2])) {
				$_arr[$i]["image"] = $config["server"].$config["site_root"]."/uploades/ecards/".stripslashes($rs->fields[2]);
			} else {
				$res = GetRandomImage('category', $_arr[$i]["id"]);
				$_arr[$i]["image"] = $config["server"].$config["site_root"]."/uploades/ecards/".$res;
			}
			$_arr[$i]["descr"] = $rs->fields[5] ? stripslashes($rs->fields[5]) : stripslashes($rs->fields[3]);

			$_arr[$i]["deletelink"] = $form['file_name']."?sel=del_category&id=".$_arr[$i]["id"];
			$_arr[$i]["editlink"] = $form['file_name']."?sel=edit_category&id=".$_arr[$i]["id"];

			//todo get elements count
			$rs->MoveNext();
			$i++;
		}
		$param = $form['file_name']."?sel=catalog&";
		$smarty->assign("links", GetLinkStr($num_categories, $page, $param, $categories_per_page));
		$smarty->assign("categories", $_arr);
	}

	$form["add_link"] = $form['file_name']."?sel=add_category";

	$smarty->assign("form", $form);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_cards_categories_table.tpl");
	exit;
}

function CategoryForm($par='', $err='', $data='') {
	global $smarty, $config, $dbconn, $lang;
	$form['file_name'] = "admin_cards.php";

	$id = (isset($_REQUEST["id"]) && intval($_REQUEST["id"]) > 0) ? intval($_REQUEST["id"]) : null;

	if ($par == 'edit' && $id == null) {
		CategoriesList(); return;
	}
	if ($par == 'edit') {
		$strSQL = "SELECT a.id, a.category_name AS name, a.category_image, a.category_descr AS descr, b.content_name AS name_lang, b.content_body AS descr_lang
					FROM ".ECARDS_CATEGORIES_TABLE." a
					LEFT JOIN ".ECARDS_LANG_CONTENT_TABLE." b ON b.content_id=a.id AND b.content_type='1' AND b.id_lang=".$config['default_lang']."
					WHERE a.id='".$id."'
		";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0] > 0) {
			$data["id"] = $rs->fields[0];
			$data["category_name"] = $rs->fields[4] ? htmlspecialchars(stripslashes($rs->fields[4])) : htmlspecialchars(stripslashes($rs->fields[1]));
			$data["category_descr"] = $rs->fields[5] ? htmlspecialchars(stripslashes($rs->fields[5])) : htmlspecialchars(stripslashes($rs->fields[3]));
			if ($rs->fields[2] != "" && file_exists($config["site_path"]."/uploades/ecards/".$rs->fields[2])) {
				$data["category_image"] = $config["server"].$config["site_root"]."/uploades/ecards/".stripslashes($rs->fields[2]);
			}
		} else {
			CategoriesList(); return ;
		}
	} else {
		$data["category_name"] = "";
		$data["category_descr"] = "";
	}
	$smarty->assign("data", $data);

	$form["action"] = $form['file_name']."?sel=save_category";
	$form["back"] = $form['file_name']."?sel=catalog";
	$form["par"] = $par;

	if ($err != '') {
		$form["err"] = $lang["cards"]["err"][$err];
	}

	$smarty->assign("form", $form);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_cards_category_form.tpl");
	exit;
}

function SaveCategory() {
	global $smarty, $config, $dbconn, $lang, $IMG_TYPE_ARRAY, $IMG_EXT_ARRAY;
	$par = isset($_POST["par"]) ? $_POST["par"] : null;

	switch ($par) {
		case "edit":
			$id = intval($_POST["id"]);
			if ($id < 1){
				CategoriesList(); return;
			}
			$data["category_name"] = strip_tags(trim($_POST["category_name"]));
			$data["category_descr"] = strip_tags(trim($_POST["category_descr"]));
			if ($data["category_name"] == '' || $data["category_descr"] == '') {
				CategoryForm($par, 'category_edit_empty_fields', $_POST);
				return;
			}
			break;
		case "add":
			$data["category_name"] = strip_tags(trim($_POST["category_name"]));
			$data["category_descr"] = strip_tags(trim($_POST["category_descr"]));
			if ($data["category_name"] == '' || $data["category_descr"] == '') {
				CategoryForm($par, 'category_edit_empty_fields', $_POST);
				return;
			}
			$dbconn->Execute("INSERT INTO ".ECARDS_CATEGORIES_TABLE." (category_name, category_descr) VALUES ('".addslashes($data["category_name"])."', '".addslashes($data["category_descr"])."') ");
			$id = $dbconn->Insert_ID();
			$strSQL = "INSERT INTO ".ECARDS_LANG_CONTENT_TABLE." (content_type, content_id, content_name, content_body, id_lang)
					VALUES ('1', '".$id."','".addslashes($data["category_name"])."','".addslashes($data["category_descr"])."','".$config['default_lang']."') ";

			break;
	}

	if ($config["default_lang"] == "1") {
		$dbconn->Execute("UPDATE ".ECARDS_CATEGORIES_TABLE." SET category_name='".addslashes($data["category_name"])."', category_descr='".addslashes($data["category_descr"])."' WHERE id='".$id."' ");
	}

	if ($par == 'edit') {
		$strSQL = "SELECT id FROM ".ECARDS_LANG_CONTENT_TABLE." WHERE content_id='".$id."' AND content_type='1' AND id_lang=".$config['default_lang']." ";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0] > 0) {
			$strSQL = "UPDATE ".ECARDS_LANG_CONTENT_TABLE." SET content_name='".addslashes($data["category_name"])."', content_body='".addslashes($data["category_descr"])."'
						WHERE id='".$rs->fields[0]."'";
		} else {
			$strSQL = "INSERT INTO ".ECARDS_LANG_CONTENT_TABLE." (content_type, content_id, content_name, content_body, id_lang)
						VALUES ('1', '".$id."','".addslashes($data["category_name"])."','".addslashes($data["category_descr"])."','".$config['default_lang']."') ";
		}
	}
	$dbconn->Execute($strSQL);

	if ( isset($id) && isset($_FILES["category_image"]) && isset($_FILES["category_image"]["tmp_name"]) && ($_FILES["category_image"]["tmp_name"]!="") ) {
		$file_type = $_FILES['category_image']["type"];
		$file_name = $_FILES['category_image']["name"];
		$temp_file = $_FILES['category_image']["tmp_name"];

		$ex_arr = explode(".",$file_name);
		$extension = strtolower($ex_arr[count($ex_arr)-1]);

		if ( (!in_array($file_type, $IMG_TYPE_ARRAY)) || (!in_array($extension, $IMG_EXT_ARRAY)) ) {
			CategoryForm($par, 'incorrect_image', $_POST); return;
		}

		$f_short_name = "category_".$id."_".substr(md5(date("ymdhis")),0,6).".".$extension;
		$new_file_name = $config["site_path"]."/uploades/ecards/".$f_short_name;
		if (copy($temp_file, $new_file_name)) {
			$images = new Images($dbconn);
			$images->ReSizeImage($new_file_name, 100, 100, 0);

			//old file deleting
			DeleteCategoryImage($id);
			$dbconn->Execute("UPDATE ".ECARDS_CATEGORIES_TABLE." SET category_image='".$f_short_name."' WHERE id='".$id."' ");
		} else {
			CategoryForm($par, 'upload_err', $_POST); return;
		}
	}
	CategoriesList();
	return;
}

function DelCategory(){
	global $smarty, $config, $dbconn, $lang;
	$id = (isset($_REQUEST["id"]) && intval($_REQUEST["id"]) > 0) ? intval($_REQUEST["id"]) : null;
	if ($id != null) {

		DeleteCategoryImage($id);

		$strSQL = "DELETE FROM ".ECARDS_LANG_CONTENT_TABLE." WHERE content_id='".$id."' ";
		$dbconn->Execute($strSQL);
		$strSQL = "DELETE FROM ".ECARDS_CATEGORIES_TABLE." WHERE id='".$id."' ";
		$dbconn->Execute($strSQL);

		$strSQL = "SELECT id FROM ".ECARDS_SUBCATEGORIES_TABLE." WHERE id_category='".$id."' ";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0] > 0) {
			while (!$rs->EOF) {
				DeleteSubCategoryContent($rs->fields[0]);
				$rs->MoveNext();
			}
		}
	}
	CategoriesList();
	return;
}

function DelSubCategory(){
	global $smarty, $config, $dbconn, $lang;

	$id = (isset($_REQUEST["id"]) && intval($_REQUEST["id"]) > 0) ? intval($_REQUEST["id"]) : null;
	$id_category = (isset($_REQUEST["id_category"]) && intval($_REQUEST["id_category"]) > 0) ? intval($_REQUEST["id_category"]) : null;

	if ($id != null) {
		DeleteSubCategoryContent($id);
	}
	SubCategoriesList();
	return;
}

function DelCard(){
	global $smarty, $config, $dbconn, $lang;

	$id = (isset($_REQUEST["id"]) && intval($_REQUEST["id"]) > 0) ? intval($_REQUEST["id"]) : null;
	$id_category = (isset($_REQUEST["id_category"]) && intval($_REQUEST["id_category"]) > 0) ? intval($_REQUEST["id_category"]) : null;
	$id_subcategory = (isset($_REQUEST["id_subcategory"]) && intval($_REQUEST["id_subcategory"]) > 0) ? intval($_REQUEST["id_subcategory"]) : null;

	if ($id != null) {
		DeleteCardContent($id);
	}
	CardsList();
	return;
}

function DeleteSubCategoryContent($id) {
	global $smarty, $config, $dbconn, $lang;
	$strSQL = "SELECT id FROM ".ECARDS_ITEMS_TABLE." WHERE id_subcategory='".$id."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] > 0) {
		while (!$rs->EOF) {
			DeleteCardContent($rs->fields[0]);
			$rs->MoveNext();
		}
	}

	DeleteSubCategoryImage($id);
	$strSQL = "DELETE FROM ".ECARDS_LANG_CONTENT_TABLE." WHERE content_id='".$id."' ";
	$dbconn->Execute($strSQL);
	$strSQL = "DELETE FROM ".ECARDS_SUBCATEGORIES_TABLE." WHERE id='".$id."' ";
	$dbconn->Execute($strSQL);
	return;
}

function DeleteCardContent($id) {
	global $smarty, $config, $dbconn, $lang;
	DeleteCardImage($id);
	$strSQL = "DELETE FROM ".ECARDS_LANG_CONTENT_TABLE." WHERE content_id='".$id."' ";
	$dbconn->Execute($strSQL);
	$strSQL = "DELETE FROM ".ECARDS_ITEMS_TABLE." WHERE id='".$id."' ";
	$dbconn->Execute($strSQL);
	return;
}

function DeleteCategoryImage($id) {
	global $smarty, $config, $dbconn, $lang;
	$strSQL = "SELECT category_image FROM ".ECARDS_CATEGORIES_TABLE." WHERE id='".$id."'";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] != '') {
		if (file_exists($config["site_path"]."/uploades/ecards/".$rs->fields[0])) {
			unlink($config["site_path"]."/uploades/ecards/".$rs->fields[0]);
		}
	}
	return;
}

function DeleteSubCategoryImage($id) {
	global $smarty, $config, $dbconn, $lang;
	$strSQL = "SELECT subcategory_image FROM ".ECARDS_SUBCATEGORIES_TABLE." WHERE id='".$id."'";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] != '') {
		if (file_exists($config["site_path"]."/uploades/ecards/".$rs->fields[0])) {
			unlink($config["site_path"]."/uploades/ecards/".$rs->fields[0]);
		}
	}
	return;
}

function DeleteCardImage($id) {
	global $smarty, $config, $dbconn, $lang;
	$strSQL = "SELECT card_image FROM ".ECARDS_ITEMS_TABLE." WHERE id='".$id."'";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] != '') {
		if (file_exists($config["site_path"]."/uploades/ecards/".$rs->fields[0])) {
			unlink($config["site_path"]."/uploades/ecards/".$rs->fields[0]);
		}
		if (file_exists($config["site_path"]."/uploades/ecards/thumb_".$rs->fields[0])) {
			unlink($config["site_path"]."/uploades/ecards/thumb_".$rs->fields[0]);
		}
	}
	return;
}

function SubCategoriesList() {
	global $smarty, $config, $dbconn, $lang;

	$id_category = (isset($_REQUEST["id_category"]) && intval($_REQUEST["id_category"]) > 0) ? intval($_REQUEST["id_category"]) : null;


	$strSQL = "SELECT a.id, a.category_name AS name, b.content_name AS name_lang
	FROM ".ECARDS_CATEGORIES_TABLE." a
	LEFT JOIN ".ECARDS_LANG_CONTENT_TABLE." b ON b.content_id=a.id AND b.content_type='1' AND b.id_lang=".$config['default_lang']."
	WHERE a.id='".$id_category."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] > 0) {
		$form["id_category"] = $rs->fields[0];
		$form["category_name"] = $rs->fields[2] ? stripslashes($rs->fields[2]) : stripslashes($rs->fields[1]);
	} else {
		CategoriesList(); return ;
	}

	$smarty->assign("categories", GetAllCategoriesList($id_category));

	$subcategories_per_page = 10;
	$form['file_name'] = "admin_cards.php";

	$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;

	$strSQL = "SELECT COUNT(*) FROM ".ECARDS_SUBCATEGORIES_TABLE." WHERE id_category='".$id_category."' ";
	$rs = $dbconn->Execute($strSQL);

	$num_subcategories = $rs->fields[0];

	$lim_min = ($page - 1) * $subcategories_per_page;
	$lim_max = $subcategories_per_page;
	$limit_str = " LIMIT ".$lim_min.", ".$lim_max;

	$strSQL = "SELECT DISTINCT a.id, a.subcategory_name AS name, a.subcategory_image, a.subcategory_descr AS descr, b.content_name AS name_lang, b.content_body AS descr_lang
	FROM ".ECARDS_SUBCATEGORIES_TABLE." a
	LEFT JOIN ".ECARDS_LANG_CONTENT_TABLE." b ON b.content_id=a.id AND b.content_type='2' AND b.id_lang=".$config['default_lang']."
	WHERE a.id_category='".$id_category."'
	GROUP BY a.id ORDER BY a.sorter ".$limit_str;
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] > 0) {
		$i = 0; $_arr = array();
		while (!$rs->EOF) {
			$_arr[$i]["id"] = $rs->fields[0];
			$_arr[$i]["name"] = $rs->fields[4] ? stripslashes($rs->fields[4]) : stripslashes($rs->fields[1]);
			$_arr[$i]["descr"] = $rs->fields[5] ? stripslashes($rs->fields[5]) : stripslashes($rs->fields[3]);
			if ($rs->fields[2] != "" && file_exists($config["site_path"]."/uploades/ecards/".$rs->fields[2])) {
				$_arr[$i]["image"] = $config["server"].$config["site_root"]."/uploades/ecards/".stripslashes($rs->fields[2]);
			} else {
				$res = GetRandomImage('subcategory', $_arr[$i]["id"]);
				$_arr[$i]["image"] = $config["server"].$config["site_root"]."/uploades/ecards/".$res;
			}
			$_arr[$i]["deletelink"] = $form['file_name']."?sel=del_subcategory&id=".$_arr[$i]["id"]."&id_category=".$id_category;
			$_arr[$i]["editlink"] = $form['file_name']."?sel=edit_subcategory&id=".$_arr[$i]["id"]."&id_category=".$id_category;

			//todo get elements count
			$rs->MoveNext();
			$i++;
		}
		$param = $form['file_name']."?sel=subcategories&id_category=".$id_category."&";
		$smarty->assign("links", GetLinkStr($num_subcategories, $page, $param, $subcategories_per_page));
		$smarty->assign("subcategories", $_arr);
	}

	$form["add_link"] = $form['file_name']."?sel=add_subcategory&id_category=".$id_category;

	$smarty->assign("form", $form);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_cards_subcategories_table.tpl");
	exit;
}

function GetAllCategoriesList($id_category="") {
	global $smarty, $config, $dbconn, $lang;

	$strSQL = "SELECT DISTINCT a.id, a.category_name AS name, b.content_name AS name_lang
	FROM ".ECARDS_CATEGORIES_TABLE." a
	LEFT JOIN ".ECARDS_LANG_CONTENT_TABLE." b ON b.content_id=a.id AND b.content_type='1' AND b.id_lang=".$config['default_lang']."
	GROUP BY a.id ORDER BY a.sorter ";
	$rs = $dbconn->Execute($strSQL);
	$i = 0; $_arr = array();
	while (!$rs->EOF) {
		$_arr[$i]["id"] = $rs->fields[0];
		$_arr[$i]["name"] = $rs->fields[2] ? stripslashes($rs->fields[2]) : stripslashes($rs->fields[1]);
		if (isset($id_category) && $id_category == $rs->fields[0]) {
			$_arr[$i]["sel"] = 1;
		}
		$rs->MoveNext();
		$i++;
	}
	return $_arr;
}

function GetAllSubCategoriesList($id_category="", $id_subcategory="") {
	global $smarty, $config, $dbconn, $lang;

	$strSQL = "SELECT DISTINCT a.id, a.subcategory_name AS name, b.content_name AS name_lang
	FROM ".ECARDS_SUBCATEGORIES_TABLE." a
	LEFT JOIN ".ECARDS_LANG_CONTENT_TABLE." b ON b.content_id=a.id AND b.content_type='2' AND b.id_lang=".$config['default_lang']."
	WHERE a.id_category='".$id_category."'
	GROUP BY a.id ORDER BY a.sorter ";
	$rs = $dbconn->Execute($strSQL);
	$i = 0; $_arr = array();
	while (!$rs->EOF) {
		$_arr[$i]["id"] = $rs->fields[0];
		$_arr[$i]["name"] = $rs->fields[2] ? stripslashes($rs->fields[2]) : stripslashes($rs->fields[1]);
		if (isset($id_subcategory) && $id_subcategory == $rs->fields[0]) {
			$_arr[$i]["sel"] = 1;
		}
		$rs->MoveNext();
		$i++;
	}
	return $_arr;
}

function SubCategoryForm($par='', $err='', $data='') {
	global $smarty, $config, $dbconn, $lang;
	$form['file_name'] = "admin_cards.php";

	$id = (isset($_REQUEST["id"]) && intval($_REQUEST["id"]) > 0) ? intval($_REQUEST["id"]) : null;
	$id_category = (isset($_REQUEST["id_category"]) && intval($_REQUEST["id_category"]) > 0) ? intval($_REQUEST["id_category"]) : null;

	$strSQL = "SELECT a.id, a.category_name AS name, b.content_name AS name_lang
				FROM ".ECARDS_CATEGORIES_TABLE." a
				LEFT JOIN ".ECARDS_LANG_CONTENT_TABLE." b ON b.content_id=a.id AND b.content_type='1' AND b.id_lang=".$config['default_lang']."
	WHERE a.id='".$id_category."' ";

	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] > 0) {
		$form["id_category"] = $rs->fields[0];
		$form["category_name"] = $rs->fields[2] ? stripslashes($rs->fields[2]) : stripslashes($rs->fields[1]);
	} else {
		CategoriesList(); return ;
	}

	if ($par == 'edit' && $id == null) {
		SubCategoriesList(); return;
	}
	if ($par == 'edit') {
		$strSQL = "SELECT a.id, a.subcategory_name AS name, a.subcategory_image, a.subcategory_descr AS descr, b.content_name AS name_lang, b.content_body AS descr_lang
					FROM ".ECARDS_SUBCATEGORIES_TABLE." a
					LEFT JOIN ".ECARDS_LANG_CONTENT_TABLE." b ON b.content_id=a.id AND b.content_type='2' AND b.id_lang=".$config['default_lang']."
					WHERE a.id='".$id."'
		";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0] > 0) {
			$data["id"] = $rs->fields[0];
			$data["subcategory_name"] = $rs->fields[4] ? htmlspecialchars(stripslashes($rs->fields[4])) : htmlspecialchars(stripslashes($rs->fields[1]));
			$data["subcategory_descr"] = $rs->fields[5] ? htmlspecialchars(stripslashes($rs->fields[5])) : htmlspecialchars(stripslashes($rs->fields[3]));
			if ($rs->fields[2] != "" && file_exists($config["site_path"]."/uploades/ecards/".$rs->fields[2])) {
				$data["subcategory_image"] = $config["server"].$config["site_root"]."/uploades/ecards/".stripslashes($rs->fields[2]);
			}
		} else {
			SubCategoriesList(); return ;
		}
	} else {
		$data["subcategory_name"] = "";
		$data["subcategory_descr"] = "";
	}
	$smarty->assign("data", $data);

	$form["action"] = $form['file_name']."?sel=save_subcategory";
	$form["back"] = $form['file_name']."?sel=subcategories&id_category=".$id_category;
	$form["par"] = $par;

	if ($err != '') {
		$form["err"] = $lang["cards"]["err"][$err];
	}

	$smarty->assign("form", $form);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_cards_subcategory_form.tpl");
	exit;
}

function SaveSubCategory() {
	global $smarty, $config, $dbconn, $lang, $IMG_TYPE_ARRAY, $IMG_EXT_ARRAY;

	$par = isset($_POST["par"]) ? $_POST["par"] : null;
	$id_category = (isset($_REQUEST["id_category"]) && intval($_REQUEST["id_category"]) > 0) ? intval($_REQUEST["id_category"]) : null;

	switch ($par) {
		case "edit":
			$id = intval($_POST["id"]);
			if ($id < 1){
				SubCategoriesList(); return;
			}
			$data["subcategory_name"] = strip_tags(trim($_POST["subcategory_name"]));
			$data["subcategory_descr"] = strip_tags(trim($_POST["subcategory_descr"]));
			if ($data["subcategory_name"] == '' || $data["subcategory_descr"] == '') {
				SubCategoryForm($par, 'subcategory_edit_empty_fields', $_POST);
				return;
			}
			break;
		case "add":
			$data["subcategory_name"] = strip_tags(trim($_POST["subcategory_name"]));
			$data["subcategory_descr"] = strip_tags(trim($_POST["subcategory_descr"]));
			if ($data["subcategory_name"] == '' || $data["subcategory_descr"] == '') {
				SubCategoryForm($par, 'subcategory_edit_empty_fields', $_POST);
				return;
			}
			$dbconn->Execute("INSERT INTO ".ECARDS_SUBCATEGORIES_TABLE." (subcategory_name, subcategory_descr, id_category) VALUES ('".addslashes($data["subcategory_name"])."', '".addslashes($data["subcategory_descr"])."', '".$id_category."') ");
			$id = $dbconn->Insert_ID();
			$strSQL = "INSERT INTO ".ECARDS_LANG_CONTENT_TABLE." (content_type, content_id, content_name, content_body, id_lang)
					VALUES ('2', '".$id."','".addslashes($data["subcategory_name"])."','".addslashes($data["subcategory_descr"])."','".$config['default_lang']."') ";

			break;
	}

	if ($config["default_lang"] == "1") {
		$dbconn->Execute("UPDATE ".ECARDS_SUBCATEGORIES_TABLE." SET subcategory_name='".addslashes($data["subcategory_name"])."', subcategory_descr='".addslashes($data["subcategory_descr"])."' WHERE id='".$id."' ");
	}

	if ($par == 'edit') {
		$strSQL = "SELECT id FROM ".ECARDS_LANG_CONTENT_TABLE." WHERE content_id='".$id."' AND content_type='2' AND id_lang=".$config['default_lang']." ";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0] > 0) {
			$strSQL = "UPDATE ".ECARDS_LANG_CONTENT_TABLE." SET content_name='".addslashes($data["subcategory_name"])."', content_body='".addslashes($data["subcategory_descr"])."'
						WHERE id='".$rs->fields[0]."'";
		} else {
			$strSQL = "INSERT INTO ".ECARDS_LANG_CONTENT_TABLE." (content_type, content_id, content_name, content_body, id_lang)
						VALUES ('2', '".$id."','".addslashes($data["subcategory_name"])."','".addslashes($data["subcategory_descr"])."','".$config['default_lang']."') ";
		}
	}
	$dbconn->Execute($strSQL);

	if ( isset($id) && isset($_FILES["subcategory_image"]) && isset($_FILES["subcategory_image"]["tmp_name"]) && ($_FILES["subcategory_image"]["tmp_name"]!="") ) {
		$file_type = $_FILES['subcategory_image']["type"];
		$file_name = $_FILES['subcategory_image']["name"];
		$temp_file = $_FILES['subcategory_image']["tmp_name"];

		$ex_arr = explode(".",$file_name);
		$extension = strtolower($ex_arr[count($ex_arr)-1]);

		if ( (!in_array($file_type, $IMG_TYPE_ARRAY)) || (!in_array($extension, $IMG_EXT_ARRAY)) ) {
			SubCategoryForm($par, 'incorrect_image', $_POST); return;
		}

		$f_short_name = "subcategory_".$id."_".substr(md5(date("ymdhis")),0,6).".".$extension;
		$new_file_name = $config["site_path"]."/uploades/ecards/".$f_short_name;
		if (copy($temp_file, $new_file_name)) {
			$images = new Images($dbconn);
			$images->ReSizeImage($new_file_name, 100, 100, 0);

			//old file deleting
			DeleteSubCategoryImage($id);
			$dbconn->Execute("UPDATE ".ECARDS_SUBCATEGORIES_TABLE." SET subcategory_image='".$f_short_name."' WHERE id='".$id."' ");
		} else {
			SubCategoryForm($par, 'upload_err', $_POST); return;
		}
	}
	SubCategoriesList();
	return;
}

function ShowSettings($par='songs') {
	global $smarty, $config, $dbconn, $lang;

	$songs_per_page = 10;
	$form['file_name'] = "admin_cards.php";
	$form['par'] = $par;

	if ($par == 'songs') {
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;

		$strSQL = "SELECT COUNT(*) FROM ".ECARDS_SONGS_TABLE." ";
		$rs = $dbconn->Execute($strSQL);

		$num_songs = $rs->fields[0];

		$lim_min = ($page - 1) * $songs_per_page;
		$lim_max = $songs_per_page;
		$limit_str = " LIMIT ".$lim_min.", ".$lim_max;

		$strSQL = "SELECT id, song_name, song_file, status FROM ".ECARDS_SONGS_TABLE." ORDER BY id ".$limit_str;
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0] > 0) {
			$i = 0; $_arr = array();
			while (!$rs->EOF) {
				$_arr[$i]["id"] = $rs->fields[0];
				$_arr[$i]["name"] = stripslashes($rs->fields[1]);
				if ($rs->fields[2] != "" && file_exists($config["site_path"]."/uploades/ecards/".$rs->fields[2])) {
					$_arr[$i]["file"] = $config["server"].$config["site_root"]."/uploades/ecards/".stripslashes($rs->fields[2]);
				}
				$_arr[$i]["status"] = intval($rs->fields[3]);

				$_arr[$i]["deletelink"] = $form['file_name']."?sel=del_song&id=".$_arr[$i]["id"];
				$_arr[$i]["editlink"] = $form['file_name']."?sel=edit_song&id=".$_arr[$i]["id"];

				$rs->MoveNext();
				$i++;
			}
			$param = $form['file_name']."?sel=settings_songs&";
			$smarty->assign("links", GetLinkStr($num_songs, $page, $param, $songs_per_page));
			$smarty->assign("songs", $_arr);
		}

		$form["add_link"] = $form['file_name']."?sel=add_song";
	}
	$smarty->assign("form", $form);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_cards_songs_table.tpl");
	exit;
}

function SongForm($par='', $err='', $data='') {
	global $smarty, $config, $dbconn, $lang;
	$form['file_name'] = "admin_cards.php";

	$id = (isset($_REQUEST["id"]) && intval($_REQUEST["id"]) > 0) ? intval($_REQUEST["id"]) : null;

	if ($par == 'edit' && $id == null) {
		ShowSettings('songs');
		return;
	}
	if ($par == 'edit') {
		$strSQL = "SELECT id, song_name, status, song_file FROM ".ECARDS_SONGS_TABLE." WHERE id='".$id."'";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0] > 0) {
			$data["id"] = $rs->fields[0];
			$data["song_name"] = htmlspecialchars(stripslashes($rs->fields[1]));
			$data["song_status"] = (isset($rs->fields[2]) && $rs->fields[2] == 1) ? 1 : 0;
			if ($rs->fields[3] != "" && file_exists($config["site_path"]."/uploades/ecards/".$rs->fields[3])) {
				$data["song_file"] = $config["server"].$config["site_root"]."/uploades/ecards/".stripslashes($rs->fields[3]);
			}
		} else {
			ShowSettings("songs"); return ;
		}
	} else {
		$data["song_name"] = "";
		$data["song_status"] = 1;
	}
	$smarty->assign("data", $data);

	$form["action"] = $form['file_name']."?sel=save_song";
	$form["back"] = $form['file_name']."?sel=settings_songs";
	$form["par"] = $par;

	if ($err != '') {
		$form["err"] = $lang["cards"]["err"][$err];
	}
	$smarty->assign("form", $form);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_cards_song_form.tpl");
	exit;
}

function SaveSong() {
	global $smarty, $config, $dbconn, $lang, $AUDIO_TYPE_ARRAY, $AUDIO_EXT_ARRAY;

	$par = isset($_POST["par"]) ? $_POST["par"] : null;
	switch ($par) {
		case "edit":
			$id = intval($_POST["id"]);
			$data["song_name"] = strip_tags(trim($_POST["song_name"]));
			$data["song_status"] = isset($_POST["song_status"]) && (intval($_POST["song_status"]) > 0) ? 1 : 0;

			if ($data["song_name"] == '') {
				SongForm($par, 'song_edit_empty_fields', $_POST);
				return;
			}
			$dbconn->Execute("UPDATE ".ECARDS_SONGS_TABLE." SET song_name='".addslashes($data["song_name"])."', status='".$data["song_status"]."' WHERE id='".$id."' ");
			break;
		case "add":
			$data["song_name"] = strip_tags(trim($_POST["song_name"]));
			$data["song_status"] = isset($_POST["song_status"]) && (intval($_POST["song_status"]) > 0) ? 1 : 0;

			if ($data["song_name"] == '') {
				SongForm($par, 'song_edit_empty_fields', $_POST);
				return;
			}
			$dbconn->Execute("INSERT INTO ".ECARDS_SONGS_TABLE." (song_name, status) VALUES ('".addslashes($data["song_name"])."', '".$data["song_status"]."') ");
			$id = $dbconn->Insert_ID();
			break;
	}

	if ( isset($id) && isset($_FILES["song_file"]) && isset($_FILES["song_file"]["tmp_name"]) && ($_FILES["song_file"]["tmp_name"]!="")) {
		$file_type = $_FILES['song_file']["type"];
		$file_name = $_FILES['song_file']["name"];
		$temp_file = $_FILES['song_file']["tmp_name"];
		$ex_arr = explode(".",$file_name);
		$extension = strtolower($ex_arr[count($ex_arr)-1]);

		//mp3 only for this player
		$AUDIO_EXT_ARRAY = array("mp3");
		if ( (!in_array($file_type, $AUDIO_TYPE_ARRAY)) || (!in_array($extension, $AUDIO_EXT_ARRAY)) ) {
			SongForm($par, 'incorrect_file_format', $_POST); return;
		}

		$f_short_name = "song_".$id."_".substr(md5(date("ymdhis")),0,6).".".$extension;
		$new_file_name = $config["site_path"]."/uploades/ecards/".$f_short_name;
		if (copy($temp_file, $new_file_name)) {
			//old file deleting
			DeleteSongFile($id);
			$dbconn->Execute("UPDATE ".ECARDS_SONGS_TABLE." SET song_file='".$f_short_name."' WHERE id='".$id."' ");
		} else {
			SongForm($par, 'upload_err', $_POST); return;
		}
	} elseif ($_FILES['song_file']["error"] != 0 && $_FILES['song_file']["name"] != '') {
		SongForm("edit", "upload_err", $data); return ;
	}
	ShowSettings('songs');
	return;
}

function DeleteSong() {
	global $smarty, $config, $dbconn, $lang;
	$id = (isset($_REQUEST["id"]) && intval($_REQUEST["id"]) > 0) ? intval($_REQUEST["id"]) : null;
	if ($id != null) {
		DeleteSongFile($id);
		$strSQL = "DELETE FROM ".ECARDS_SONGS_TABLE." WHERE id='".$id."'";
		$dbconn->Execute($strSQL);
	}
	ShowSettings('songs');
	return;
}

function DeleteSongFile($id) {
	global $smarty, $config, $dbconn, $lang;
	$strSQL = "SELECT song_file FROM ".ECARDS_SONGS_TABLE." WHERE id='".$id."'";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] != '') {
		if (file_exists($config["site_path"]."/uploades/ecards/".$rs->fields[0])) {
			@unlink($config["site_path"]."/uploades/ecards/".$rs->fields[0]);
		}
	}
	return;
}

function CardsList() {
	global $smarty, $config, $dbconn, $lang;

	$id_category = (isset($_REQUEST["id_category"]) && intval($_REQUEST["id_category"]) > 0) ? intval($_REQUEST["id_category"]) : null;
	$id_subcategory = (isset($_REQUEST["id_subcategory"]) && intval($_REQUEST["id_subcategory"]) > 0) ? intval($_REQUEST["id_subcategory"]) : null;

	//get category and categories for select
	$strSQL = "SELECT a.id, a.category_name AS name, b.content_name AS name_lang
	FROM ".ECARDS_CATEGORIES_TABLE." a
	LEFT JOIN ".ECARDS_LANG_CONTENT_TABLE." b ON b.content_id=a.id AND b.content_type='1' AND b.id_lang=".$config['default_lang']."
	WHERE a.id='".$id_category."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] > 0) {
		$form["id_category"] = $rs->fields[0];
		$form["category_name"] = $rs->fields[2] ? stripslashes($rs->fields[2]) : stripslashes($rs->fields[1]);
	} else {
		CategoriesList(); return ;
	}
	$smarty->assign("categories", GetAllCategoriesList($id_category));

	//get subcategory for select
	$strSQL = "SELECT DISTINCT a.id, a.subcategory_name AS name, b.content_name AS name_lang
	FROM ".ECARDS_SUBCATEGORIES_TABLE." a
	LEFT JOIN ".ECARDS_LANG_CONTENT_TABLE." b ON b.content_id=a.id AND b.content_type='2' AND b.id_lang=".$config['default_lang']."
	WHERE a.id_category='".$id_category."' AND a.id='".$id_subcategory."'";

	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] > 0) {
		$form["id_subcategory"] = $rs->fields[0];
		$form["subcategory_name"] = $rs->fields[2] ? stripslashes($rs->fields[2]) : stripslashes($rs->fields[1]);
	} else {
		SubCategoriesList(); return ;
	}
	$smarty->assign("subcategories", GetAllSubCategoriesList($id_category, $id_subcategory));

	$cards_per_page = 10;
	$form['file_name'] = "admin_cards.php";

	$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;

	$strSQL = "SELECT COUNT(*) FROM ".ECARDS_ITEMS_TABLE." WHERE id_category='".$id_category."' AND id_subcategory='".$id_subcategory."' ";
	$rs = $dbconn->Execute($strSQL);

	$num_cards = $rs->fields[0];

	$lim_min = ($page - 1) * $cards_per_page;
	$lim_max = $cards_per_page;
	$limit_str = " LIMIT ".$lim_min.", ".$lim_max;

	$strSQL = "SELECT DISTINCT a.id, a.card_name AS name, a.card_image, b.content_name AS name_lang, a.card_price, a.sorter, a.status
	FROM ".ECARDS_ITEMS_TABLE." a
	LEFT JOIN ".ECARDS_LANG_CONTENT_TABLE." b ON b.content_id=a.id AND b.content_type='3' AND b.id_lang=".$config['default_lang']."
	WHERE a.id_category='".$id_category."' AND a.id_subcategory='".$id_subcategory."'
	GROUP BY a.id ORDER BY a.sorter ".$limit_str;
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] > 0) {
		$i = 0; $_arr = array();
		while (!$rs->EOF) {
			$_arr[$i]["id"] = $rs->fields[0];

			$_arr[$i]["name"] = $rs->fields[3] ? stripslashes($rs->fields[3]) : stripslashes($rs->fields[1]);
			$_arr[$i]["name_unslashed"] = addslashes($_arr[$i]["name"]);

			if ($rs->fields[2] != "" && file_exists($config["site_path"]."/uploades/ecards/thumb_".stripslashes($rs->fields[2]))) {
				$_arr[$i]["card_image"] = $config["server"].$config["site_root"]."/uploades/ecards/thumb_".stripslashes($rs->fields[2]);
			}
			if ($rs->fields[2] != "" && file_exists($config["site_path"]."/uploades/ecards/".stripslashes($rs->fields[2]))) {
				$_arr[$i]["card_image_big"] = $config["server"].$config["site_root"]."/uploades/ecards/".stripslashes($rs->fields[2]);
			}
			$_arr[$i]["deletelink"] = $form['file_name']."?sel=del_card&id=".$_arr[$i]["id"]."&id_category=".$id_category."&id_subcategory=".$id_subcategory;
			$_arr[$i]["editlink"] = $form['file_name']."?sel=edit_card&id=".$_arr[$i]["id"]."&id_category=".$id_category."&id_subcategory=".$id_subcategory;
			$_arr[$i]["card_price"] = sprintf("%01.2f",$rs->fields[4]);
			$_arr[$i]["card_status"] = (isset($rs->fields[6]) && $rs->fields[6] == 1) ? 1 : 0;
			$rs->MoveNext();
			$i++;
		}
		$param = $form['file_name']."?sel=items&id_category=".$id_category."&id_subcategory=".$id_subcategory."&";
		$smarty->assign("links", GetLinkStr($num_cards, $page, $param, $cards_per_page));
		$smarty->assign("cards", $_arr);
	}

	$form["back"] = $form['file_name']."?sel=subcategories&id_category=".$id_category;
	$form["add_link"] = $form['file_name']."?sel=add_card&id_category=".$id_category."&id_subcategory=".$id_subcategory;
	$smarty->assign("form", $form);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_cards_items_table.tpl");
	exit;
}


function GetRandomImage($type = 'category', $id) {
	global $smarty, $config, $dbconn, $lang;
	switch ($type) {
		case "category" :
			$res = "default_category_picture.gif";
			$strSQL = "SELECT id, card_image FROM ".ECARDS_ITEMS_TABLE." WHERE id_category='".$id."' ORDER BY RAND() LIMIT 1 ";
			break;
		case "subcategory" :
			$res = "default_subcategory_picture.gif";
			$strSQL = "SELECT id, card_image FROM ".ECARDS_ITEMS_TABLE." WHERE id_subcategory='".$id."' ORDER BY RAND() LIMIT 1 ";
			break;
	}
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] > 0 && file_exists($config["site_path"]."/uploades/ecards/thumb_".stripslashes($rs->fields[1]))) {
		$res = "thumb_".stripslashes($rs->fields[1]);
	}
	return $res;
}

function CardForm($par='', $err='', $data='') {
	global $smarty, $config, $dbconn, $lang;
	$form['file_name'] = "admin_cards.php";

	$id = (isset($_REQUEST["id"]) && intval($_REQUEST["id"]) > 0) ? intval($_REQUEST["id"]) : null;

	$id_category = (isset($_REQUEST["id_category"]) && intval($_REQUEST["id_category"]) > 0) ? intval($_REQUEST["id_category"]) : null;
	$id_subcategory = (isset($_REQUEST["id_subcategory"]) && intval($_REQUEST["id_subcategory"]) > 0) ? intval($_REQUEST["id_subcategory"]) : null;

	$strSQL = "SELECT a.id, a.category_name AS name, b.content_name AS name_lang
				FROM ".ECARDS_CATEGORIES_TABLE." a
				LEFT JOIN ".ECARDS_LANG_CONTENT_TABLE." b ON b.content_id=a.id AND b.content_type='1' AND b.id_lang=".$config['default_lang']."
	WHERE a.id='".$id_category."' ";

	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] > 0) {
		$form["id_category"] = $rs->fields[0];
		$form["category_name"] = $rs->fields[2] ? stripslashes($rs->fields[2]) : stripslashes($rs->fields[1]);
	} else {
		CategoriesList(); return ;
	}

	$strSQL = "SELECT DISTINCT a.id, a.subcategory_name AS name, b.content_name AS name_lang
	FROM ".ECARDS_SUBCATEGORIES_TABLE." a
	LEFT JOIN ".ECARDS_LANG_CONTENT_TABLE." b ON b.content_id=a.id AND b.content_type='2' AND b.id_lang=".$config['default_lang']."
	WHERE a.id_category='".$id_category."' AND a.id='".$id_subcategory."'";

	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] > 0) {
		$form["id_subcategory"] = $rs->fields[0];
		$form["subcategory_name"] = $rs->fields[2] ? stripslashes($rs->fields[2]) : stripslashes($rs->fields[1]);
	} else {
		SubCategoriesList(); return ;
	}

	if ($par == 'edit' && $id == null) {
		CardsList();
		return;
	}

	$smarty->assign("categories", GetAllCategoriesList($id_category));
	$smarty->assign("subcategories", GetAllSubCategoriesList($id_category, $id_subcategory));
	if ($par == 'edit') {
		$strSQL = "SELECT a.id, a.card_name, a.status, a.card_image, a.card_price, a.sorter, a.id_category, a.id_subcategory, b.content_name AS name_lang
					FROM ".ECARDS_ITEMS_TABLE." AS a
					LEFT JOIN ".ECARDS_LANG_CONTENT_TABLE." b ON b.content_id=a.id AND b.content_type='3' AND b.id_lang=".$config['default_lang']."
					WHERE a.id='".$id."'";

		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0] > 0) {
			$data["id"] = $rs->fields[0];
			$data["card_name"] = (isset($rs->fields[8]) && $rs->fields[8] !='') ? htmlspecialchars(stripslashes($rs->fields[8])) : htmlspecialchars(stripslashes($rs->fields[1]));
			$data["card_status"] = (isset($rs->fields[2]) && $rs->fields[2] == 1) ? 1 : 0;
			if ($rs->fields[3] != "" && file_exists($config["site_path"]."/uploades/ecards/".$rs->fields[3])) {
				$data["card_image"] = $config["server"].$config["site_root"]."/uploades/ecards/".stripslashes($rs->fields[3]);
			}
			$data["card_price"] = round(floatval($rs->fields[4]), 2);
			$data["sorter"] = $rs->fields[5];
			$data["id_category"] = $rs->fields[6];
			$data["id_subcategory"] = $rs->fields[7];
			$data["song_status"] = (isset($rs->fields[2]) && $rs->fields[2] == 1) ? 1 : 0;
		} else {
			CardsList();
		}
	} else {
		$data["card_name"] = "";
		$data["card_status"] = 1;
		$data["sorter"] = 1;
		$data["card_price"] = "0";
		$data["id_category"] = $form["id_category"];
		$data["id_subcategory"] = $form["id_subcategory"];
	}

	$smarty->assign("data", $data);

	$form["action"] = $form['file_name']."?sel=save_card";
	$form["back"] = $form['file_name']."?sel=items&id_category=".$id_category."&id_subcategory=".$id_subcategory;
	$form["par"] = $par;

	$smarty->assign("cur", GetSiteSettings('site_unit_costunit'));
	if ($err != '') {
		$form["err"] = $lang["cards"]["err"][$err];
	}

	$smarty->assign("form", $form);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_cards_item_form.tpl");
}

function AjaxSubcategories() {
	global $smarty, $config, $dbconn, $lang;
	$id_category = (isset($_REQUEST["id_category"]) && intval($_REQUEST["id_category"]) > 0) ? intval($_REQUEST["id_category"]) : null;
	$subcategories = GetAllSubCategoriesList($id_category, '');
	echo "<select name=\"id_subcategory\">";
	foreach ($subcategories as $value) {
		echo "<option value='".$value['id']."'>".$value['name']."</option>";
	}
	echo "</select>";
	exit;
}

function SaveCard() {
	global $smarty, $config, $dbconn, $lang, $IMG_TYPE_ARRAY, $IMG_EXT_ARRAY;

	$par = isset($_POST["par"]) ? $_POST["par"] : null;
	$id_category = (isset($_REQUEST["id_category"]) && intval($_REQUEST["id_category"]) > 0) ? intval($_REQUEST["id_category"]) : null;
	$id_subcategory = (isset($_REQUEST["id_subcategory"]) && intval($_REQUEST["id_subcategory"]) > 0) ? intval($_REQUEST["id_subcategory"]) : null;

	$data["card_name"] = strip_tags(trim($_POST["card_name"]));
	if ($data["card_name"] == '') {
		CardForm($par, 'card_edit_empty_fields', $_POST);
		return;
	}
	$data["card_status"] = intval($_POST["card_status"]);
	$data["card_price"] = floatval($_POST["card_price"]);
	switch ($par) {
		case "edit":
			$id = intval($_POST["id"]);
			if ($id < 1){
				CardsList(); return;
			}
			$strSQL = "UPDATE ".ECARDS_ITEMS_TABLE." SET id_category='".$id_category."', id_subcategory='".$id_subcategory."', card_price='".$data["card_price"]."',
					status='".$data["card_status"]."', sorter='1'  WHERE id='".$id."'";
			$dbconn->Execute($strSQL);

			$strSQL = "SELECT id FROM ".ECARDS_LANG_CONTENT_TABLE." WHERE content_id='".$id."' AND content_type='3' AND id_lang=".$config['default_lang']." ";
			$rs = $dbconn->Execute($strSQL);

			if ($rs->fields[0] > 0) {
				$strSQL = "UPDATE ".ECARDS_LANG_CONTENT_TABLE." SET content_name='".addslashes($data["card_name"])."'
						WHERE id='".$rs->fields[0]."'";
			} else {
				$strSQL = "INSERT INTO ".ECARDS_LANG_CONTENT_TABLE." (content_type, content_id, content_name, content_body, id_lang)
						VALUES ('3', '".$id."','".addslashes($data["card_name"])."','','".$config['default_lang']."') ";
			}
			$strSQL_2 = "UPDATE ".ECARDS_ITEMS_TABLE." SET card_name='".addslashes($data["card_name"])."' WHERE id='".$id."' ";
			break;
		case "add":
			$dbconn->Execute("INSERT INTO ".ECARDS_ITEMS_TABLE." (id_category, id_subcategory, card_name, card_image, card_price, status, sorter)
  		VALUES ('".$id_category."','".$id_subcategory."', '".addslashes($data["card_name"])."', '', '".$data["card_price"]."', '".$data["card_status"]."','1') ");
			$id = $dbconn->Insert_ID();

			$strSQL = "INSERT INTO ".ECARDS_LANG_CONTENT_TABLE." (content_type, content_id, content_name, content_body, id_lang)
					VALUES ('3', '".$id."','".addslashes($data["card_name"])."','','".$config['default_lang']."') ";
			$strSQL_2 = "UPDATE ".ECARDS_ITEMS_TABLE." SET card_name='".addslashes($data["card_name"])."', card_price='".$data["card_price"]."', status='".$data["card_status"]."',
			id_category='".$id_category."', id_subcategory='".$id_subcategory."' WHERE id='".$id."' ";
			break;
	}
	$dbconn->Execute($strSQL);

	if ($config["default_lang"] == "1") {
		$dbconn->Execute($strSQL_2);
	}

	if ( isset($id) && isset($_FILES["card_image"]) && isset($_FILES["card_image"]["tmp_name"]) && ($_FILES["card_image"]["tmp_name"]!="") ) {
		$file_type = $_FILES['card_image']["type"];
		$file_name = $_FILES['card_image']["name"];
		$temp_file = $_FILES['card_image']["tmp_name"];

		$ex_arr = explode(".",$file_name);
		$extension = strtolower($ex_arr[count($ex_arr)-1]);

		if ( (!in_array($file_type, $IMG_TYPE_ARRAY)) || (!in_array($extension, $IMG_EXT_ARRAY)) ) {
			CardForm($par, 'incorrect_image', $_POST); return;
		}

		$f_short_name = "card_".$id."_".substr(md5(date("ymdhis")),0,6).".".$extension;
		$new_file_name = $config["site_path"]."/uploades/ecards/".$f_short_name;
		$new_file_name_thumb = $config["site_path"]."/uploades/ecards/thumb_".$f_short_name;
		if (copy($temp_file, $new_file_name)) {
			copy($new_file_name, $new_file_name_thumb);
			$images = new Images($dbconn);

			$images->ReSizeImage($new_file_name, 500, 354, 0);
			$images->ReSizeImage($new_file_name_thumb, 100, 100, 0);

			//old file deleting
			DeleteCardImage($id);
			$dbconn->Execute("UPDATE ".ECARDS_ITEMS_TABLE." SET card_image='".$f_short_name."' WHERE id='".$id."' ");
		} else {
			CardForm($par, 'upload_err', $_POST); return;
		}
	}
	CardsList();
	return ;
}

function OrdersList() {
	global $smarty, $config, $dbconn, $lang;

	$orders_per_page = 10;
	$form['file_name'] = "admin_cards.php";

	$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;

	$strSQL = "SELECT COUNT(*) FROM ".ECARDS_ORDERS_TABLE." ";
	$rs = $dbconn->Execute($strSQL);

	$num_orders = $rs->fields[0];

	$lim_min = ($page - 1) * $orders_per_page;
	$lim_max = $orders_per_page;
	$limit_str = " LIMIT ".$lim_min.", ".$lim_max;

	$strSQL = "SELECT DISTINCT a.id, a.id_user, a.id_item, a.id_song, a.id_user_to, a.card_header, a.status, b.card_image, b.card_name, c.content_name AS name_lang,
				d.login, e.login, b.card_price
				FROM ".ECARDS_ORDERS_TABLE." a
				LEFT JOIN ".ECARDS_ITEMS_TABLE." b ON b.id=a.id_item
				LEFT JOIN ".ECARDS_LANG_CONTENT_TABLE." c ON c.content_id=a.id_item AND c.content_type='3' AND c.id_lang=".$config['default_lang']."
				LEFT JOIN ".USERS_TABLE." d ON d.id=a.id_user
				LEFT JOIN ".USERS_TABLE." e ON e.id=a.id_user_to
				GROUP BY a.id ORDER BY a.id ".$limit_str;
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] > 0) {
		$i = 0; $_arr = array();
		while (!$rs->EOF) {
			$_arr[$i]['id_order'] = $rs->fields[0];
			$_arr[$i]['id_sender'] = $rs->fields[1];
			$_arr[$i]['id_card'] = $rs->fields[2];
			$_arr[$i]['id_song'] = $rs->fields[3];
			$_arr[$i]['id_user_to'] = $rs->fields[4];
			$_arr[$i]['card_header'] = stripslashes($rs->fields[5]);
			$_arr[$i]['status'] = $rs->fields[6];
			$_arr[$i]['status_lang'] = $lang["cards"]["admin"]["status_value"][$_arr[$i]['status']];
			if ($rs->fields[7] != "" && file_exists($config["site_path"]."/uploades/ecards/".stripslashes($rs->fields[7]))) {
				$_arr[$i]["card_image_big"] = $config["server"].$config["site_root"]."/uploades/ecards/".stripslashes($rs->fields[7]);
			}
			if ($rs->fields[7] != "" && file_exists($config["site_path"]."/uploades/ecards/thumb_".stripslashes($rs->fields[7]))) {
				$_arr[$i]["card_image_thumb"] = $config["server"].$config["site_root"]."/uploades/ecards/thumb_".stripslashes($rs->fields[7]);
			}
			$_arr[$i]["name"] = $rs->fields[9] ? stripslashes($rs->fields[9]) : stripslashes($rs->fields[8]);
			$_arr[$i]["name_unslashed"] = addslashes($_arr[$i]["name"]);
			$_arr[$i]["sender_login"] = $rs->fields[10];
			$_arr[$i]["user_to_login"] = $rs->fields[11];
			$_arr[$i]["card_price"] = sprintf("%01.2f",$rs->fields[12]);
			$rs->MoveNext();
			$i++;
		}
		$param = $form['file_name']."?sel=orders&";
		$smarty->assign("links", GetLinkStr($num_orders, $page, $param, $orders_per_page));
		$smarty->assign("orders", $_arr);
	}
	$smarty->assign("form", $form);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_cards_orders_table.tpl");
	exit;

}

?>