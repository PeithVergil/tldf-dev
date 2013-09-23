<?php

/**
*
* 	@package DatingPro
* 	@subpackage Admin Mode
* 	@copyright Pilot Group <http://www.pilotgroup.net/>
*   @author Yura Tselischev <tselischev@pilotgroup.net>
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
$mode = IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "admin_blog");

if ($mode == 1) {
	$sel = $_GET["sel"]?$_GET["sel"]:$_POST["sel"];
	switch($sel){
		case "change_status": 	ChangeStatus(); break;

		case "add_category": 	CategoryForm('add'); break;
		case "edit_category":	CategoryForm('edit'); break;
		case "del_category": 	DelCategory(); break;
		case "save_category":	SaveCategory(); break;
		case "categories": 		ListCategories(); break;
		default: ListBlogs(); break;
	}
} else {
	echo "<script>document.location.href='index.php'</script>";
}

function ListBlogs() {
	global $smarty, $dbconn, $config, $config_admin, $page, $lang;

	$file_name = (isset($_SERVER["PHP_SELF"]))?AfterLastSlash($_SERVER["PHP_SELF"]):"admin_blog.php";

	AdminMainMenu($lang["blog"]["admin"]);
	$page = $_GET["page"]?$_GET["page"]:$_POST["page"];

	if( (strval($page) == "") || (strval($page) == "0")){ $page = 1;}

	$strSQL = " SELECT COUNT(*) FROM ".BLOG_PROFILE_TABLE." ";
	$rs = $dbconn->Execute($strSQL);
	$num_records = $rs->fields[0];

	$lim_min = ($page-1)*$config_admin["addition_numpage"];
	$lim_max = $config_admin["addition_numpage"];
	$limit_str = " LIMIT ".$lim_min.", ".$lim_max;

	$sorter = intval($_GET["sorter"]);
	$order = intval($_GET["order"]);

	switch ($sorter) {
		case "1":
			$sorter_str = " ORDER BY b.title ";
			$form['sorter'] = "1";
			break;
		case "2":
			$sorter_str = " ORDER BY u.login ";
			$form['sorter'] = "2";
			break;
		case "3":
			$sorter_str = " ORDER BY b.creation_date ";
			$form['sorter'] = "3";
			break;
		case "4":
			$sorter_str = " ORDER BY b.is_hidden ";
			$form['sorter'] = "4";
			break;
		case "5":
			$sorter_str = " ORDER BY b.active ";
			$form['sorter'] = "5";
			break;
		default:
			$sorter_str = " ORDER BY b.title ";
			$form['sorter'] = "1";
			break;
	}
	switch ($order){
		case "1":
			$sorter_str .= " ASC, b.id ";
			$form['order'] = "2";
			$order_topage = "1";
			break;
		case "2":
			$sorter_str .= " DESC, b.id ";
			$form['order'] = "1";
			$order_topage = "2";
			break;
		default:
			$sorter_str .= " ASC, b.id ";
			$form['order'] = "2";
			$order_topage = "1";
			break;
	}

	$strSQL = " SELECT DISTINCT b.id, b.id_user, b.title, DATE_FORMAT(b.creation_date,'".$config["date_format"]."') as creation_date,
								b.is_hidden, u.login, b.active, bc.category_name
				FROM ".BLOG_PROFILE_TABLE." b
				LEFT JOIN ".USERS_TABLE." u ON u.id=b.id_user
				LEFT JOIN ".BLOG_CATEGORIES_TABLE." bc ON bc.id=b.id_category
				GROUP BY b.id ".$sorter_str.$limit_str;
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	if($rs->RowCount()>0){
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$_arr[$i]["number"] = ($page-1)*$config_admin["addition_numpage"]+($i+1);
			$_arr[$i]["id"] = $row["id"];
			$_arr[$i]["title"] = stripslashes($row["title"]);
			$_arr[$i]["id_user"] = $row["id_user"];
			$_arr[$i]["creation_date"] = $row["creation_date"];
			$_arr[$i]["is_hidden"] = $row["is_hidden"];
			$_arr[$i]["login"] = $row["login"];
			$_arr[$i]["user_link"] =	"admin_users.php?sel=edit&id=".$_arr[$i]["id_user"];
			$_arr[$i]["user_comunicate"] = "admin_comunicate.php?id=".$_arr[$i]["id_user"];

			$strSQL = " SELECT COUNT(id) FROM ".BLOG_POST_TABLE." WHERE id_profile='".$_arr[$i]["id"]."' GROUP BY id ";
			$rs_2 = $dbconn->Execute($strSQL);
			$_arr[$i]["posts_count"] = intval($rs_2->fields[0]);

			$strSQL = " SELECT COUNT(pos.id)
						FROM ".BLOG_POST_TABLE." pro
						LEFT JOIN ".BLOG_COMMENTS_TABLE." pos ON pos.id_post=pro.id
						WHERE pro.id_profile='".$_arr[$i]["id"]."'
						GROUP BY pro.id ";
			$rs_3 = $dbconn->Execute($strSQL);
			$_arr[$i]["comments_count"] = intval($rs_3->fields[0]);
			$_arr[$i]["active"] = $row["active"];
			$_arr[$i]["category_name"] = stripslashes($row["category_name"]);
			$rs->MoveNext();
			$i++;
		}
		$param = $file_name."?sel=view&sorter=".$sorter."&order=".$order_topage;
		$smarty->assign("links", GetLinkStr($num_records,$page,$param,$config_admin["addition_numpage"]));
		$smarty->assign("blog_profile", $_arr);
	}
	if(!$err){
		$name = "";
	}
	$form["add_link"] = $file_name."?sel=add_category";
	$form["confirm"] = $lang["blog"]["confirm"];
	$form["err"] = $lang["err"][$err];
	$smarty->assign("form", $form);
	$smarty->assign("button", $lang["button"]);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_blog_list_table.tpl");
	exit;
}

function ChangeStatus() {
	global $smarty, $dbconn, $config, $config_admin, $page, $lang;

	if ($_GET['status'] == 'true') {
		$status = 1;
		$err = $lang["blog"]["admin"]["profile_activated"];
	} else {
		$status = 0;
		$err = $lang["blog"]["admin"]["profile_deactivated"];
	}
	$id_profile = intval($_GET['id_profile']);
	if ($id_profile>0) {
		$strSQL = " SELECT title FROM ".BLOG_PROFILE_TABLE." WHERE id='".$id_profile."' ";
		$rs = $dbconn->Execute($strSQL);
		$title = stripslashes($rs->fields[0]);
		$dbconn->Execute(" UPDATE ".BLOG_PROFILE_TABLE." SET active='".$status."' WHERE id='".$id_profile."' ");
		echo $title."&nbsp;".$err;
	} else {
		echo "error";
	}
	exit;
}

function ListCategories() {
	global $smarty, $dbconn, $config, $lang, $config_admin;

	$file_name = "admin_blog.php";
	AdminMainMenu($lang["blog"]);

	$page = (isset($_REQUEST["page"]) && intval($_REQUEST["page"]) > 0) ? intval($_REQUEST["page"]) : 1;

	$strSQL = " SELECT COUNT(*) FROM ".BLOG_CATEGORIES_TABLE." ";
	$rs = $dbconn->Execute($strSQL);
	$num_records = $rs->fields[0];

	$lim_min = ($page-1)*$config_admin["addition_numpage"];
	$lim_max = $config_admin["addition_numpage"];
	$limit_str = " LIMIT ".$lim_min.", ".$lim_max;

	$strSQL = " SELECT DISTINCT id, category_name FROM ".BLOG_CATEGORIES_TABLE." GROUP BY id ORDER BY category_name ".$limit_str;
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	if($rs->RowCount()>0){
		while(!$rs->EOF){
			$_arr[$i]["number"] = ($page-1)*$config_admin["addition_numpage"]+($i+1);
			$_arr[$i]["id"] = $rs->fields[0];
			$_arr[$i]["name"] = stripslashes($rs->fields[1]);
			$_arr[$i]["deletelink"] = $file_name."?sel=del_category&page=".$page."&id=".$_arr[$i]["id"];
			$_arr[$i]["editlink"] = $file_name."?sel=edit_category&id=".$_arr[$i]["id"];

			$strSQL = " SELECT COUNT(*) FROM ".BLOG_PROFILE_TABLE." WHERE id_category='".$_arr[$i]["id"]."' ";
			$rs_count = $dbconn->Execute($strSQL);
			$_arr[$i]["blogs_count"] = intval($rs_count->fields[0]);
			$rs->MoveNext();
			$i++;
		}
		$param = $file_name."?";
		$smarty->assign("links", GetLinkStr($num_records,$page,$param,$config_admin["addition_numpage"]));
		$smarty->assign("blog_categories", $_arr);
	}

	$form["add_link"] = $file_name."?sel=add_category";
	$smarty->assign("form", $form);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_blog_categories_table.tpl");
	exit;
}

function CategoryForm($par='', $err='', $data='') {
	global $smarty, $dbconn, $config, $config_admin, $lang;

	$file_name = "admin_blog.php";
	AdminMainMenu($lang["blog"]);

	$page = (isset($_REQUEST["page"]) && intval($_REQUEST["page"]) > 0) ? intval($_REQUEST["page"]) : 1;

	$id = (isset($_REQUEST["id"]) && intval($_REQUEST["id"]) > 0) ? intval($_REQUEST["id"]) : null;

	if ($par == 'edit' && $id == null){
		ListCategories(); return;
	}

	if ($par == 'edit'){
		$strSQL = " SELECT id, category_name FROM ".BLOG_CATEGORIES_TABLE." WHERE id='".$id."' ";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0]>0) {
			$data["id"] = $rs->fields[0];
			$data["category_name"] = htmlspecialchars(stripslashes($rs->fields[1]));
			$smarty->assign("data", $data);
		} else {
			ListCategories(); return;
		}
	}

	$form["action"] = $file_name."?sel=save_category&page=".$page;
	$form["back"] = $file_name."?sel=categories&page=".$page;
	$form["par"] = $par;
	if ($err != '') {
		$form["err"] = $lang["blog"]["err"][$err];
	}
	$form["page"] = $page;

	$smarty->assign("form", $form);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_blog_category_form.tpl");
	exit;
}

function SaveCategory() {
	global $smarty, $dbconn, $config, $config_admin, $page, $lang;
	$par = $_POST["par"];
	switch ($par){
		case "edit":
			$id = intval($_POST["id"]);
			if ($id < 1){
				ListCategories(); return;
			}
			$data["category_name"] = strip_tags(trim($_POST["category_name"]));
			if ($data["category_name"] == ''){
				CategoryForm($par, 'empty_fields', $_POST);
				return;
			}
			$dbconn->Execute(" UPDATE ".BLOG_CATEGORIES_TABLE." SET category_name='".addslashes($data["category_name"])."' WHERE id='".$id."' ");
			$err = 'category_saved';
			break;
		case "add":
			$data["category_name"] = strip_tags(trim($_POST["category_name"]));
			if ($data["category_name"] == ''){
				CategoryForm($par, 'empty_fields', $_POST);
				return;
			}
			$dbconn->Execute(" INSERT INTO ".BLOG_CATEGORIES_TABLE." (category_name) VALUES ('".addslashes($data["category_name"])."') ");
			$err = 'category_added';
			break;
	}
	ListCategories();
	return;
}

function DelCategory(){
	global $smarty, $dbconn, $config, $config_admin, $page, $lang;
	$id = (isset($_REQUEST["id"]) && intval($_REQUEST["id"]) > 0) ? intval($_REQUEST["id"]) : null;
	if (!$id) { ListCategories(); return;}
	$strSQL = "DELETE FROM ".BLOG_CATEGORIES_TABLE." WHERE id='".$id."' ";
	$dbconn->Execute($strSQL);
	ListCategories();
	return;
}

?>