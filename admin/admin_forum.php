<?php

include_once "../include/config.php";
include_once "../common.php";
include_once "../include/config_admin.php";
include_once "../include/functions_auth.php";
include_once "../include/functions_admin.php";
include_once "../include/functions_forum.php";
include_once "../include/class.images.php";

$auth = auth_user();
login_check($auth);
$mode = IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "admin_forum");

if ($mode == 1) {
	$sel = $_GET["sel"]?$_GET["sel"]:$_POST["sel"];
	switch($sel){
		case "category": 				ForumIndexPage('category'); break;
		case "subcategory":				ForumIndexPage('subcategory'); break;

		case "add_category":			ForumMessage('new_category'); break;
		case "edit_category":			ForumMessage('edit_category');	break;

		case "new_subcategory": 		ForumMessage('new_subcategory'); break;
		case "edit_subcategory":		ForumMessage('edit_subcategory');	break;

		case "new_post": 				ForumMessage('new_post'); break;
		case "quote":					ForumMessage('quote'); break;

		case "save_new_category":		CategorySave('new');	break;
		case "save_edited_category":	CategorySave('edit');	break;
		case "save_edited_subcategory":	SubCategorySave();	break;

		case "new_subcategory_post":	NewSubcategoryPost(); break;

		case "move_up":					ChangeCategoryOrder('up');		break;
		case "move_down":				ChangeCategoryOrder('down');	break;

		case "del_category":			DeleteCategory();	break;
		case "del_subcategory":			DeleteSubCategory();	break;
		case "delete_post":				DeletePost();	break;
		case "edit_post":				EditMessage();		break;
		case "edit_post_save":			EditPostSave(); break;

		case "banned":					BannedList();	break;
		case "unban":					UnbanAction();	break;
		case "ban":						BanAction();	break;
		case "new_post_save":			NewPostSave(); break;
		default: ForumIndexPage(); break;
	}
} else {
	echo "<script>document.location.href='index.php'</script>";
}

exit;


function ForumIndexPage($par='', $err='', $id_category='', $id_subcategory='')
				{
	global $smarty, $dbconn, $config, $page, $lang, $auth;

	if ($err){
		$smarty->assign('error', $lang['forum'][$err]);
	}
	$file_name = (isset($_SERVER["PHP_SELF"]))?AfterLastSlash($_SERVER["PHP_SELF"]):"admin_forum.php";

	AdminMainMenu($lang["forum"]["admin"]);
	$page = $_GET["page"]?$_GET["page"]:$_POST["page"];

	if( (strval($page) == "") || (strval($page) == "0")){ $page = 1;}

	switch ($par) {
		case "help":
			$par = 'help';
			break;
		case "category":
			if ($id_category<1) {
				$id_category = intval($_REQUEST["id_category"]);
			}
			if ($id_category<1) {
				ForumIndexPage();
				exit;
			}
			$strSQL = " SELECT id, category FROM ".FORUM_CATEGORIES_TABLE." WHERE id='".$id_category."' ";
			$rs = $dbconn->Execute($strSQL);
			if ($rs->fields[0]<1) {
				ForumIndexPage();
				exit;
			} else {
				$data["category_id"] = $rs->fields[0];
				$data["category_name"] = stripslashes($rs->fields[1]);
			}

			$strSQL = " SELECT DISTINCT fs.id, fs.subcategory, fs.description, DATE_FORMAT(fm.created_date, '".$config["date_format"]."') as created_date_config,
										UNIX_TIMESTAMP(fm.created_date) as created_date_stamp, fm.id_user, u.login, u.root_user, u.guest_user
						FROM ".FORUM_SUBCATEGORIES_TABLE." fs
						LEFT JOIN ".FORUM_MESSAGES_TABLE." fm ON (fm.id_subcategory=fs.id AND fm.id_category='".$id_category."' AND fm.first_post='1')
						LEFT JOIN ".USERS_TABLE." u ON u.id=fm.id_user
						WHERE fs.id_category='".$id_category."'
						GROUP BY fs.id ORDER BY fs.created_date DESC ";
			$rs = $dbconn->Execute($strSQL);
			if ($rs->fields[0]>0) {
				$i = 0;
				$forum = array();
				while(!$rs->EOF) {
					$forum[$i]["id"] = $rs->fields[0];
					$forum[$i]["subcategory"] = stripslashes($rs->fields[1]);
					$forum[$i]["description"] = stripslashes($rs->fields[2]);
					$forum[$i]["date"] = $rs->fields[3].date(' H:i', $rs->fields[4]);
					$forum[$i]["poster_id"] = $rs->fields[5];
					$forum[$i]["poster_login"] = $rs->fields[6];
					$forum[$i]["poster_profile"] = ($rs->fields[7]==1 || $rs->fields[8]==1) ? 0 : 1;
					$query = "SELECT COUNT(id) FROM ".FORUM_MESSAGES_TABLE." WHERE id_category='".$id_category."' AND id_subcategory='".$forum[$i]["id"]."' ";
					$query_rs = $dbconn->Execute($query);
					$forum[$i]["total_posts"] = $query_rs->fields[0];
					$rs->MoveNext();
					$i++;
				}
			} else {
				$forum["empty"] = 1;
			}
			$smarty->assign("data", $data);
			$smarty->assign("forum", $forum);
			break;
		case "subcategory":
			if ($id_subcategory<1){
				$id_subcategory = intval($_REQUEST["id_subcategory"]);
			}
			if ($id_subcategory<1) {
				ForumIndexPage();
				exit;
			}
			$strSQL = " SELECT fs.id, fs.subcategory, fc.id, fc.category
						FROM ".FORUM_SUBCATEGORIES_TABLE." fs
						LEFT JOIN ".FORUM_CATEGORIES_TABLE." fc ON fc.id=fs.id_category
						WHERE fs.id='".$id_subcategory."' ";
			$rs = $dbconn->Execute($strSQL);
			if ($rs->fields[0]<1) {
				ForumIndexPage();
				exit;
			} else {
				$data["subcategory_id"] = $rs->fields[0];
				$data["subcategory_name"] = stripslashes($rs->fields[1]);
				$data["category_id"] = $rs->fields[2];
				$data["category_name"] = stripslashes($rs->fields[3]);
			}

			$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : "0";
			if( (strval($page) == "") || (strval($page) == "0")){ $page = 1;}

			$strSQL = "SELECT COUNT(id) FROM ".FORUM_MESSAGES_TABLE." WHERE id_category='".$data["category_id"]."' AND id_subcategory='".$data["subcategory_id"]."' ";
			$rs = $dbconn->Execute($strSQL);

			$num_records = $rs->fields[0];
			$max_records = 10;
			if ($num_records > $max_records){
				$param = $file_name."?sel=subcategory&amp;id_subcategory=".$data["subcategory_id"]."&amp;";
				$smarty->assign("links", GetLinkArray($num_records, $page, $param, $max_records));
			}

			$lim_min = ($page-1)*$max_records;
			$lim_max = $max_records;
			$limit_str = " limit ".$lim_min.", ".$lim_max;

			$strSQL = " SELECT DISTINCT fm.id, fm.id_user, fm.subject, fm.message, DATE_FORMAT(fm.created_date, '".$config["date_format"]."') as created_date_config,
										UNIX_TIMESTAMP(fm.created_date) as created_date_stamp, u.login, u.root_user, u.guest_user
						FROM ".FORUM_MESSAGES_TABLE." fm
						LEFT JOIN ".USERS_TABLE." u ON u.id=fm.id_user
						WHERE fm.id_category='".$data["category_id"]."' AND fm.id_subcategory='".$data["subcategory_id"]."'
						GROUP BY fm.id ORDER BY fm.created_date ASC ".$limit_str;
			$rs = $dbconn->Execute($strSQL);
			if ($rs->fields[0]>0) {
				$i = 0;
				while(!$rs->EOF) {
					$forum[$i]["id"] = $rs->fields[0];

					$forum[$i]["id_user"] = $rs->fields[1];
					// RS: we had $user here instead of $auth but this does not make sense
					if ($auth[0] == $forum[$i]["id_user"] && $auth[3] != 1) {
						$forum[$i]["is_author"] = 1;
					} else {
						$forum[$i]["is_author"] = 0;
					}
					$forum[$i]["subject"] = stripslashes($rs->fields[2]);
					$forum[$i]["message"] = stripslashes($rs->fields[3]);
					$forum[$i]["date"] = $rs->fields[4].date(' H:i', $rs->fields[5]);
					$forum[$i]["login_user"] = stripslashes($rs->fields[6]);
					$forum[$i]["poster_profile"] = ($rs->fields[7]==1 || $rs->fields[8]==1) ? 0 : 1;
					$forum[$i]["is_banned"] = IsUserBanned($forum[$i]["id_user"], $forum[$i]["poster_profile"]);
					$rs->MoveNext();
					$i++;
				}
			}
			$smarty->assign("data", $data);
			$smarty->assign("forum", $forum);
			break;
		default:
			//index forum page
			$strSQL = " SELECT DISTINCT fc.id, fc.category, fc.description
						FROM ".FORUM_CATEGORIES_TABLE." fc
						GROUP BY fc.id ORDER BY fc.sorter ASC ";
			$rs = $dbconn->Execute($strSQL);
			if ($rs->fields[0] > 0) {
				$i = 0;
				while(!$rs->EOF) {
					$forum[$i]["id"] = $rs->fields[0];
					$forum[$i]["category"] = stripslashes($rs->fields[1]);
					$forum[$i]["description"] = stripslashes($rs->fields[2]);
					$rs_subcategories = $dbconn->Execute(" SELECT COUNT(id) FROM ".FORUM_SUBCATEGORIES_TABLE." WHERE id_category='".$forum[$i]["id"]."' ");
					$rs_posts = $dbconn->Execute(" SELECT COUNT(id) FROM ".FORUM_MESSAGES_TABLE." WHERE id_category='".$forum[$i]["id"]."' ");
					$forum[$i]["total_subcategories"] = $rs_subcategories->fields[0];
					$forum[$i]["total_posts"] = $rs_posts->fields[0];
					$rs->MoveNext();
					$i++;
				}
			}
			$data['total_categories'] = sizeof($forum);
			$par = 'index';
			$smarty->assign("forum", $forum);
			$smarty->assign("data", $data);
			break;
	}
	$smarty->assign("par", $par);
	$smarty->assign("file_name", $file_name);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_forum_main_table.tpl");
	exit;
}


function ForumMessage($par='', $err='', $data='')
{
	global $smarty, $dbconn, $config, $page, $lang, $auth;

	$file_name = (isset($_SERVER["PHP_SELF"]))?AfterLastSlash($_SERVER["PHP_SELF"]):"admin_forum.php";

	AdminMainMenu($lang["forum"]["admin"]);
	$page = $_GET["page"]?$_GET["page"]:$_POST["page"];

	if ($err){
		$smarty->assign('error', $lang['forum'][$err]);
	}

	if ($data["category_id"]<1) {
		$data["id_category"] = intval($_REQUEST["id_category"]);
	} else {
		$data["id_category"] = $data["category_id"];
	}

	if ($data["subcategory_id"]<1) {
		$data["id_subcategory"] = intval($_REQUEST["id_subcategory"]);
	} else {
		$data["id_subcategory"] = $data["subcategory_id"];
	}

	if ($data["id_post"]<1) {
		$data["id_post"] = intval($_REQUEST["id_post"]);
	} else {
		$data["id_post"] = $data["id_post"];
	}
	switch ($par) {
		case "new_category":
			$strSQL = " SELECT MAX(sorter) FROM ".FORUM_CATEGORIES_TABLE." ";
			$rs = $dbconn->Execute($strSQL);
			$next = $rs->fields[0] + 1;
			for ($i=1; $i <= $next; $i++) {
				$data['sorter'][$i-1]['value'] = $i;
				if ($i == $next) {
					$data['sorter'][$i-1]['sel'] = 1;
				}
			}
			$smarty->assign("data", $data);
			break;
		case "edit_category":
			$strSQL = " SELECT category, description, sorter FROM ".FORUM_CATEGORIES_TABLE." WHERE id='".$data["id_category"]."' ";
			$rs = $dbconn->Execute($strSQL);
			$data['category_name'] = stripslashes($rs->fields[0]);
			$data['category_description'] = stripslashes($rs->fields[1]);
			$data['category_sorter'] = $rs->fields[2];

			$strSQL = " SELECT MAX(sorter) FROM ".FORUM_CATEGORIES_TABLE." ";
			$rs = $dbconn->Execute($strSQL);

			for ($i=1; $i <= $rs->fields[0]; $i++) {
				$data['sorter'][$i-1]['value'] = $i;
				if ($i == $data['category_sorter']) {
					$data['sorter'][$i-1]['sel'] = 1;
				}
			}
			$smarty->assign("data", $data);
			break;
		case  "new_subcategory":
			if ($data["id_category"]<1) {
				ForumIndexPage();
				exit;
			}
			$strSQL = " SELECT id, category FROM ".FORUM_CATEGORIES_TABLE." WHERE id='".$data["id_category"]."' ";
			$rs = $dbconn->Execute($strSQL);
			if ($rs->fields[0]<1) {
				ForumIndexPage();
				exit;
			} else {
				$data["category_id"] = $rs->fields[0];
				$data["category_name"] = stripslashes($rs->fields[1]);
			}

			$data["poster_login"] = $auth[5];
			$data["poster_id"] = $auth[0];
			$smarty->assign("data", $data);
			break;
		case "edit_subcategory":
			if ($data["id_subcategory"]<1) {
				ForumIndexPage();
				exit;
			}
			$strSQL = " SELECT 	fs.id, fs.id_user, fs.id_category, fs.subcategory, fs.created_date,
								fc.category
						FROM ".FORUM_SUBCATEGORIES_TABLE." fs
						LEFT JOIN ".FORUM_CATEGORIES_TABLE." fc ON fc.id=fs.id_category
						WHERE fs.id='".$data["id_subcategory"]."'
					";
			$rs = $dbconn->Execute($strSQL);
			if ($rs->fields[0]<1) {
				ForumIndexPage();
				exit;
			} else {
				$data["subcategory_id"] = $rs->fields[0];
				$data["id_user"] = $rs->fields[1];
				$data["category_id"] = $rs->fields[2];
				$data["subcategory_name"] = stripslashes($rs->fields[3]);
				$data["created_date"] = $rs->fields[4];
				$data["category_name"] = $rs->fields[5];
			}
			$smarty->assign("data", $data);
			break;
		case "new_post":
			if ($data["id_subcategory"]<1) {
				ForumIndexPage();
				exit;
			}
			$strSQL = " SELECT DISTINCT fs.id, fs.subcategory, fc.id, fc.category
						FROM ".FORUM_SUBCATEGORIES_TABLE." fs
						LEFT JOIN ".FORUM_CATEGORIES_TABLE." fc ON fc.id=fs.id_category
						WHERE fs.id='".$data["id_subcategory"]."'
						GROUP BY fs.id ";
			$rs = $dbconn->Execute($strSQL);
			if ($rs->fields[0]<1) {
				ForumIndexPage();
				exit;
			} else {
				$data["subcategory_id"] = $rs->fields[0];
				$data["subcategory_name"] = stripslashes($rs->fields[1]);
				$data["category_id"] = $rs->fields[2];
				$data["category_name"] = stripslashes($rs->fields[3]);
			}
			$smarty->assign("data", $data);
			break;
		case "quote":
			if ($data["id_post"]<1) {
				ForumIndexPage();
				exit;
			}
			$strSQL = "	SELECT 	fm.id, fm.id_user, fm.message, fm.subject, u.login,
								fs.id, fs.subcategory,
								fc.id, fc.category
						FROM ".FORUM_MESSAGES_TABLE." fm
						LEFT JOIN ".FORUM_CATEGORIES_TABLE." fc ON fc.id=fm.id_category
						LEFT JOIN ".FORUM_SUBCATEGORIES_TABLE." fs ON fs.id=fm.id_subcategory
						LEFT JOIN ".USERS_TABLE." u ON u.id=fm.id_user
						WHERE fm.id='".$data["id_post"]."' ";
			$rs = $dbconn->Execute($strSQL);
			if ($rs->fields[0]<1) {
				ForumIndexPage();
				exit;
			} else {
				$data["id_post"] = $rs->fields[0];
				$data["id_user"] = $rs->fields[1];
				$data["message"] = strip_tags(bbdecode((stripslashes($rs->fields[2]))));
				$data["post_name"] = "Re: ".stripslashes($rs->fields[3]);
				$data["login_user"] = stripslashes($rs->fields[4]);

				$data["message"] = '[quote='.$data["login_user"].']'.$data["message"].'[/quote]';

				$data["subcategory_id"] = $rs->fields[5];
				$data["subcategory_name"] = stripslashes($rs->fields[6]);
				$data["category_id"] = $rs->fields[7];
				$data["category_name"] = stripslashes($rs->fields[8]);
				$smarty->assign("data", $data);
			}
			break;
		default:
			ForumIndexPage();
			exit;
			break;
	}
	$smarty->assign("par", $par);
	$smarty->assign("site_images_path", $config['server'].$config['site_root'].$config['index_theme_path'].'/images');

	$smarty->assign("file_name", $file_name);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_forum_main_table.tpl");
}

function CategorySave($par='')
{
	global $dbconn;

	switch ($par) {
		case 'new':
			$data['category_name'] = trim(strip_tags($_REQUEST['category_name']));
			$data['category_description'] = trim(strip_tags($_REQUEST['category_description']));
			$data['category_sorter'] = intval($_REQUEST['category_sorter']);

			$strSQL = "INSERT INTO ".FORUM_CATEGORIES_TABLE." (category, description) VALUES ('".addslashes($data['category_name'])."', '".addslashes($data['category_description'])."') ";
			$dbconn->Execute($strSQL);

			$ins_id = $dbconn->_insertid();
			$rs_os = $dbconn->Execute("SELECT MAX(sorter)+1  FROM ".FORUM_CATEGORIES_TABLE." ");
			SprSorter($data['category_sorter'], $rs_os->fields[0], $ins_id);
			ForumIndexPage();
			break;
		case 'edit':
			$data['category_name'] = trim(strip_tags($_REQUEST['category_name']));
			$data['category_description'] = trim(strip_tags($_REQUEST['category_description']));
			$data['category_sorter'] = intval($_REQUEST['category_sorter'])>0 ? intval($_REQUEST['category_sorter']) : 1;

			$data['category_id'] = intval($_REQUEST['category_id']);

			$strSQL = " UPDATE ".FORUM_CATEGORIES_TABLE." SET category='".$data['category_name']."', description='".$data['category_description']."' WHERE id='".$data['category_id']."' ";
			$dbconn->Execute($strSQL);

			$strSQL = "SELECT sorter FROM ".FORUM_CATEGORIES_TABLE." WHERE id='".$data['category_id']."' ";
			$rs = $dbconn->Execute($strSQL);
			$old_sorter = $rs->fields[0];
			SprSorter($data['category_sorter'], $old_sorter, $data['category_id']);
			ForumIndexPage();
			break;
		default:
			ForumIndexPage();
			break;
	}
	return ;
}

function SubCategorySave()
{
	global $dbconn;
	
	$strSQL = " UPDATE ".FORUM_SUBCATEGORIES_TABLE." SET subcategory='".addslashes(strip_tags(trim($_REQUEST['subcategory_name'])))."',
				description ='', created_date='".$_REQUEST['created_date']."'
				WHERE id='".intval($_REQUEST['subcategory_id'])."' AND id_user='".$_REQUEST['id_user']."'  ";
	$dbconn->Execute($strSQL);
	ForumIndexPage("category", "subcategory_edited", intval($_REQUEST["category_id"]));
	return;
}

function NewSubcategoryPost()
{
	global $dbconn, $auth;

	$data["id_category"] = intval($_REQUEST["id_category"]);
	if ($data["id_category"]<1) {
		ForumIndexPage();
		exit;
	}

	$strSQL = " SELECT id, category FROM ".FORUM_CATEGORIES_TABLE." WHERE id='".$data["id_category"]."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]<1) {
		ForumIndexPage();
		exit;
	} else {
		$data["category_id"] = $rs->fields[0];
		$data["category_name"] = stripslashes($rs->fields[1]);
	}

	$data["subcategory_name"] = strip_tags(trim($_POST["subcategory_name"]));
	$data["post_name"] = strip_tags(trim($_POST["post_name"]));
	$data["message"] = strip_tags($_POST["message"]);

	if ( strlen($data["subcategory_name"])<1 || strlen($data["post_name"])<1 ||strlen($data["message"])<1 ) {
		ForumMessage("new_subcategory", "empty_fields", $data);
		exit;
	}

	$strSQL = " INSERT INTO ".FORUM_SUBCATEGORIES_TABLE." (id_user, id_category, subcategory, description, created_date)
				VALUES ('".$auth[0]."', '".$data["category_id"]."', '".addslashes($data["subcategory_name"])."', '', now() ) ";
	$dbconn->Execute($strSQL);
	$subcategory_id = $dbconn->_insertid();

	$message = addslashes(bbcode($data["message"]));

	$strSQL = " INSERT INTO ".FORUM_MESSAGES_TABLE." (id_user, subject, message, id_category, id_subcategory, first_post, created_date)
				VALUES ('".$auth[0]."', '".addslashes($data["post_name"])."', '".$message."', '".$data["category_id"]."', '".$subcategory_id."', '1', now())
				";
	$dbconn->Execute($strSQL);
	ForumIndexPage("subcategory", "new_subcategory_added", $data["category_id"],$subcategory_id);
	exit;
}


function ChangeCategoryOrder($par)
{
	global $dbconn;
	$id_category = intval($_REQUEST['id_category']);
	if ($id_category>0 ) {
		$strSQL = " SELECT sorter FROM ".FORUM_CATEGORIES_TABLE." WHERE id='".$id_category."' ";
		$rs = $dbconn->Execute($strSQL);
		$old_sorter = $rs->fields[0];

		$strSQL = " SELECT MAX(sorter), MIN(sorter) FROM ".FORUM_CATEGORIES_TABLE." ";
		$rs = $dbconn->Execute($strSQL);
		$max_sorter = $rs->fields[0];
		$min_sorter = $rs->fields[1];

		switch ($par) {
			case 'up':
				if ($min_sorter < $old_sorter) {
					SprSorter($old_sorter-1, $old_sorter, $id_category);
				}
				break;
			case 'down':
				if ($max_sorter > $old_sorter) {
					SprSorter($old_sorter+1, $old_sorter, $id_category);
				}
				break;
		}
	}
	ForumIndexPage();
	return;
}

function DeleteCategory()
{
	global $dbconn;
	$id_category = intval($_REQUEST['id_category']);

	$dbconn->Execute(" DELETE FROM ".FORUM_CATEGORIES_TABLE." WHERE id='".$id_category."' ");
	$dbconn->Execute(" DELETE FROM ".FORUM_SUBCATEGORIES_TABLE." WHERE id_category='".$id_category."' ");
	$dbconn->Execute(" DELETE FROM ".FORUM_MESSAGES_TABLE." WHERE id_category='".$id_category."' ");
	SprSorter('', 1);
	ForumIndexPage();
	return ;
}

function DeleteSubCategory()
{
	global $dbconn;
	$id_subcategory = intval($_REQUEST['id_subcategory']);
	$strSQL = " SELECT id_category FROM ".FORUM_SUBCATEGORIES_TABLE." WHERE id='".$id_subcategory."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>0) {
		$id_category = $rs->fields[0];
		$dbconn->Execute(" DELETE FROM ".FORUM_SUBCATEGORIES_TABLE." WHERE id='".$id_subcategory."' ");
		$dbconn->Execute(" DELETE FROM ".FORUM_MESSAGES_TABLE." WHERE id_subcategory='".$id_subcategory."' ");
		ForumIndexPage('category','', $id_category);
	} else {
		ForumIndexPage();
	}
	return ;
}

function DeletePost()
{
	global $dbconn;
	$id_post = intval($_REQUEST["id_post"]);

	$strSQL = " SELECT id_category, id_subcategory FROM ".FORUM_MESSAGES_TABLE." WHERE id='".$id_post."' ";
	$rs = $dbconn->Execute($strSQL);
	$id_category = $rs->fields[0];
	$id_subcategory = $rs->fields[1];
	$dbconn->Execute(" DELETE FROM ".FORUM_MESSAGES_TABLE." WHERE id='".$id_post."' ");

	ForumIndexPage('subcategory',"", $id_category, $id_subcategory);
}

function EditMessage($id_post='', $err='', $data='')
{
	global $smarty, $dbconn, $config, $lang;

	$file_name = (isset($_SERVER["PHP_SELF"]))?AfterLastSlash($_SERVER["PHP_SELF"]):"admin_forum.php";

	AdminMainMenu($lang["forum"]["admin"]);

	if ($id_post == '') {
		if (isset($_REQUEST['id_post']) && intval($_REQUEST['id_post'])>0) {
			$id_post = intval($_REQUEST['id_post']);
		} else {
			ForumIndexPage();
			exit;
		}
	} else {
		$id_post = intval($id_post);
	}

	if ($err !='') {
		$smarty->assign('error', $lang['forum'][$err]);
	} else {
		$strSQL = "	SELECT 	fm.id, fm.id_user, fm.message, fm.subject,
						fs.id, fs.subcategory,
						fc.id, fc.category,
						fm.first_post, fm.created_date
					FROM ".FORUM_MESSAGES_TABLE." fm
					LEFT JOIN ".FORUM_CATEGORIES_TABLE." fc ON fc.id=fm.id_category
					LEFT JOIN ".FORUM_SUBCATEGORIES_TABLE." fs ON fs.id=fm.id_subcategory
					WHERE fm.id='".$id_post."' ";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0]<1) {
			ForumIndexPage();
			exit;
		} else {
			$data["id_post"] = $rs->fields[0];
			$data["id_user"] = $rs->fields[1];
			$data["message"] = strip_tags(bbdecode((stripslashes($rs->fields[2]))));
			$data["post_name"] = stripslashes($rs->fields[3]);

			$data["subcategory_id"] = $rs->fields[4];
			$data["subcategory_name"] = stripslashes($rs->fields[5]);
			$data["category_id"] = $rs->fields[6];
			$data["category_name"] = stripslashes($rs->fields[7]);

			$data["first_post"] = $rs->fields[8];
			$data["created_date"] = $rs->fields[9];
		}
	}
	$smarty->assign("data", $data);
	$par = 'edit_post';
	$smarty->assign("par", $par);
	$smarty->assign("site_images_path", $config['server'].$config['site_root'].$config['index_theme_path'].'/images');
	$smarty->assign("file_name", $file_name);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_forum_main_table.tpl");
	exit;
}

function EditPostSave()
{
	global $dbconn;
	
	if (isset($_REQUEST['id_post']) && intval($_REQUEST['id_post'])>0) {
		$id_post = intval($_REQUEST['id_post']);
	} else {
		ForumIndexPage();
		exit;
	}

	$data["id_subcategory"] = intval($_POST["id_subcategory"]);
	if ($data["id_subcategory"]<1) {
		ForumIndexPage();
		exit;
	}

	$strSQL = " SELECT fs.id, fs.subcategory, fc.id, fc.category
				FROM ".FORUM_SUBCATEGORIES_TABLE." fs
				LEFT JOIN ".FORUM_CATEGORIES_TABLE." fc ON fc.id=fs.id_category
				WHERE fs.id='".$data["id_subcategory"]."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]<1) {
		ForumIndexPage();
		exit;
	} else {
		$data["subcategory_id"] = $rs->fields[0];
		$data["subcategory_name"] = stripslashes($rs->fields[1]);
		$data["category_id"] = $rs->fields[2];
		$data["category_name"] = stripslashes($rs->fields[3]);
	}

	$data["post_name"] = strip_tags(trim($_POST["post_name"]));
	$data["message"] = bbcode(strip_tags($_POST["message"]));

	$data['id_user'] = intval($_REQUEST['id_user']);
	$data['first_post'] = intval($_REQUEST['first_post']);

	$created_date = $_REQUEST['created_date'];

	$message = addslashes($data["message"]);

	$strSQL = " UPDATE ".FORUM_MESSAGES_TABLE."
				SET subject='".$data["post_name"]."', message='".$message."', created_date='".$created_date."',
				id_user='".$data['id_user']."', first_post='".$data['first_post']."'
			 	WHERE id='".$id_post."'
			";
	$dbconn->Execute($strSQL);
	ForumIndexPage("subcategory", "edit_post_saved", $data["category_id"], $data["subcategory_id"]);
	exit;
}

function BannedList($err = '')
{
	global $smarty, $dbconn, $config, $page, $lang;

	if ($err) {
		$smarty->assign('error', $lang['forum'][$err]);
	}
	
	$file_name = (isset($_SERVER["PHP_SELF"]))?AfterLastSlash($_SERVER["PHP_SELF"]):"admin_forum.php";

	AdminMainMenu($lang["forum"]["admin"]);
	$page = $_GET["page"]?$_GET["page"]:$_POST["page"];

	if( (strval($page) == "") || (strval($page) == "0")){ $page = 1;}

	$strSQL = "SELECT COUNT(id) FROM ".FORUM_BANS_TABLE."  ";
	$rs = $dbconn->Execute($strSQL);

	$num_records = $rs->fields[0];
	$max_records = 15;
	if ($num_records > $max_records){
		$param = $file_name."?sel=banned&";
		$smarty->assign("links", GetLinkStr($num_records,$page,$param,$max_records));
	}

	$lim_min = ($page-1)*$max_records;
	$lim_max = $max_records;
	$limit_str = " limit ".$lim_min.", ".$lim_max;

	$strSQL = " SELECT DISTINCT fb.id, fb.id_user, u.login, DATE_FORMAT(fb.created_date, '".$config["date_format"]."')
				FROM ".FORUM_BANS_TABLE." fb
				LEFT JOIN ".USERS_TABLE." u ON u.id=fb.id_user
				GROUP BY fb.id ORDER BY fb.id_user ASC ".$limit_str;
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>0) {
		$i = 0;
		while(!$rs->EOF) {
			$bans[$i]["num"] = ($page-1)*$max_records+($i+1);
			$bans[$i]["id"] = $rs->fields[0];
			$bans[$i]["id_user"] = $rs->fields[1];
			$bans[$i]["login"] = $rs->fields[2];
			$bans[$i]["b_date"] = $rs->fields[3];
			$rs->MoveNext();
			$i++;
		}
	} else {
		$bans = 'empty';
	}
	$smarty->assign("bans", $bans);
	$smarty->assign("file_name", $file_name);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_forum_banned_table.tpl");
}

function BanAction()
{
	global $dbconn;
	
	$dbconn->Execute("INSERT INTO ".FORUM_BANS_TABLE." (id_user) VALUES ('".$_REQUEST['id_user']."')");
	
	ForumIndexPage('subcategory', '',$_REQUEST['id_category'], $_REQUEST['id_subcategory']);
	return;
}

function UnbanAction()
{
	global $dbconn;
	
	if (sizeof($_REQUEST['id']) > 0) {
		$str = implode(',', $_REQUEST['id']);
		$dbconn->Execute("DELETE FROM ".FORUM_BANS_TABLE." WHERE id IN (".$str.")");
	}
	
	BannedList();
	exit;
}

function NewPostSave()
{
	global $dbconn, $auth;
	
	$data["id_subcategory"] = intval($_POST["id_subcategory"]);
	
	if ($data["id_subcategory"] < 1) {
		ForumIndexPage();
		exit;
	}
	
	$strSQL = " SELECT fs.id, fs.subcategory, fc.id, fc.category
				FROM ".FORUM_SUBCATEGORIES_TABLE." fs
				LEFT JOIN ".FORUM_CATEGORIES_TABLE." fc ON fc.id=fs.id_category
				WHERE fs.id='".$data["id_subcategory"]."' ";
	$rs = $dbconn->Execute($strSQL);
	
	if ($rs->fields[0] < 1) {
		ForumIndexPage();
		exit;
	}
	
	$data["subcategory_id"] = $rs->fields[0];
	$data["subcategory_name"] = stripslashes($rs->fields[1]);
	$data["category_id"] = $rs->fields[2];
	$data["category_name"] = stripslashes($rs->fields[3]);
	
	$data["post_name"] = strip_tags(trim($_POST["post_name"]));
	$data["message"] = strip_tags($_POST["message"]);

	if ( strlen($data["message"])<1 || strlen($data["post_name"])<1 ) {
		ForumMessage("new_post", "empty_fields", $data);
		exit;
	}

	$message = addslashes(bbcode($data["message"]));
	$strSQL = " INSERT INTO ".FORUM_MESSAGES_TABLE." (id_user, subject, message, id_category, id_subcategory, first_post, created_date)
				VALUES ('".$auth[0]."', '".addslashes($data["post_name"])."', '".$message."', '".$data["category_id"]."', '".$data["subcategory_id"]."', '0', now()) ";
	$dbconn->Execute($strSQL);
	ForumIndexPage("subcategory", "new_post_added", $data["category_id"], $data["subcategory_id"]);
	exit;
}

function SprSorter($sorter, $old_sorter, $id="")
{
	global $dbconn;
	
	if (!$id) {
		$rs = $dbconn->Execute("SELECT id FROM ".FORUM_CATEGORIES_TABLE." WHERE sorter >= '".$old_sorter."' ORDER BY sorter");
		$i = 0;
		while(!$rs->EOF){
			$i++;
			$dbconn->Execute("UPDATE ".FORUM_CATEGORIES_TABLE." SET sorter = '".$i."' WHERE id ='".$rs->fields[0]."' ");
			$rs->MoveNext();
		}
		return;
	}

	if ($old_sorter < $sorter) {
		$strSQL = "SELECT DISTINCT id, sorter FROM ".FORUM_CATEGORIES_TABLE." WHERE sorter >= '".$old_sorter."' AND sorter <= '".$sorter."' GROUP BY id ORDER BY sorter";
		$rs = $dbconn->Execute($strSQL);
		while(!$rs->EOF){
			$dbconn->Execute("UPDATE ".FORUM_CATEGORIES_TABLE." SET sorter='".($rs->fields[1]-1)."' WHERE id ='".$rs->fields[0]."' ");
			$rs->MoveNext();
		}
		$dbconn->Execute("UPDATE ".FORUM_CATEGORIES_TABLE." SET sorter='".$sorter."' WHERE id='".$id."' ");

	} elseif ($old_sorter > $sorter) {
		$strSQL = "SELECT DISTINCT id, sorter FROM ".FORUM_CATEGORIES_TABLE." WHERE sorter <= '".$old_sorter."' AND  sorter >= '".$sorter."' GROUP BY id ORDER BY sorter";
		$rs = $dbconn->Execute($strSQL);
		while(!$rs->EOF){
			$dbconn->Execute("UPDATE ".FORUM_CATEGORIES_TABLE." SET sorter='".($rs->fields[1]+1)."' WHERE id='".$rs->fields[0]."' ");
			$rs->MoveNext();
		}
		$dbconn->Execute("UPDATE ".FORUM_CATEGORIES_TABLE." SET sorter='".$sorter."' WHERE id ='".$id."' ");
	} else {
		$dbconn->Execute("UPDATE ".FORUM_CATEGORIES_TABLE." SET sorter='".$sorter."' WHERE id ='".$id."' ");
	}
	return;
}


function IsUserBanned($id_user, $not_root)
{
	global $dbconn;
	
	if ($not_root == 1) {
		$rs = $dbconn->Execute('SELECT id FROM '.FORUM_BANS_TABLE.' WHERE id_user = ?', array($id_user));
		if ($rs->fields[0] > 0) {
			return 1;
		} else {
			return 0;
		}
	} else {
		return 2;
	}
}

?>