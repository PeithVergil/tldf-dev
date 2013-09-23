<?php
/**
* Forum
*
* @package DatingPro
* @subpackage User Mode
**/

include './include/config.php';
include './common.php';
include './include/config_index.php';
include './include/functions_auth.php';
include './include/functions_index.php';
include './include/class.lang.php';

// authentication
$user = auth_index_user();

if (empty($user) || $user == 'err' || empty($user[ AUTH_ID_USER ])) {
	header('location: '.$config['site_root'].'/index.php');
	exit;
}

// check guest
if ($user[ AUTH_GUEST ]) {
	AlertPage(GetRightModulePath(__FILE__));
}

// check group, period, expiration
RefreshAccount();

// check status
if (!$user[ AUTH_STATUS ]) {
	AlertPage(GetRightModulePath(__FILE__));
}

// check permissions
IsFileAllowed(GetRightModulePath(__FILE__));

// alerts and statistics
if (!$user[ AUTH_GUEST ]) {
	GetAlertsMessage();
	SetModuleStatistic(GetRightModulePath(__FILE__));
}

// active menu item
$smarty->assign('sub_menu_num', '');

if (IsUserBanned($user[ AUTH_ID_USER ]) == 1) {
	ForumIndexPage('banned');
}

UpdateForumVisit();

// user selection
$sel = isset($_REQUEST['sel']) ? $_REQUEST['sel'] : '';

// dispatcher
switch ($sel) {
	case "help":					ForumIndexPage('help'); break;
	case "category": 				ForumIndexPage('category'); break;
	case "subcategory":				ForumIndexPage('subcategory'); break;

	case "new_subcategory": 		ForumMessage('new_subcategory'); break;
	case "new_post": 				ForumMessage('new_post'); break;
	case "quote":					ForumMessage('quote'); break;

	case "new_subcategory_post": 	NewSubcategoryPost(); break;
	case "new_post_save":			NewPostSave(); break;

	case "edit_post_save":			EditPostSave(); break;

	case "delete_post":				DeleteMessage();	break;
	case "edit_post":				EditMessage();		break;
	default: 						ForumIndexPage(); 	break;
}

exit;


function ForumIndexPage($par='', $err='', $id_category='', $id_subcategory='')
{
	global $lang, $config, $smarty, $dbconn, $user;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$file_name = "forum.php";

	$smarty->assign("guest", $user[ AUTH_GUEST ]);

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
					$forum[$i]["new_posts"] = CheckNewPosts('category', $forum[$i]["id"]);
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

			$cooked_posts = ( isset($_COOKIE['dating_forum_p']) ) ? unserialize($_COOKIE['dating_forum_p']) : array();

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

					if ( count($cooked_posts) >= 150 && empty($cooked_posts[$forum[$i]["id"]]) )
					{
						asort($cooked_posts);
						unset($cooked_posts[key($cooked_posts)]);
					}
					$cooked_posts[$forum[$i]["id"]] = time();

					$forum[$i]["id_user"] = $rs->fields[1];
					if ($user[ AUTH_ID_USER ] == $forum[$i]["id_user"] && !$user[ AUTH_GUEST ]) {
						$forum[$i]["is_author"] = 1;
					} else {
						$forum[$i]["is_author"] = 0;
					}
					$forum[$i]["subject"] = stripslashes($rs->fields[2]);
					$forum[$i]["message"] = stripslashes($rs->fields[3]);
					$forum[$i]["date"] = $rs->fields[4].date(' H:i', $rs->fields[5]);
					$forum[$i]["login_user"] = stripslashes($rs->fields[6]);
					$forum[$i]["poster_profile"] = ($rs->fields[7]==1 || $rs->fields[8]==1) ? 0 : 1;
					$rs->MoveNext();
					$i++;
				}
			}
			setcookie('dating_forum_p', serialize($cooked_posts),time()+86400);
			$smarty->assign("data", $data);
			$smarty->assign("forum", $forum);
			break;
		case "banned":
			$err = 'you_are_banned';
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
					$forum[$i]["new_posts"] = CheckNewPosts('index', $forum[$i]["id"]);
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

	if ($err){
		$smarty->assign('error', $lang['forum'][$err]);
	}
	$smarty->assign("par", $par);
	$smarty->assign("file_name", $file_name);
	$smarty->assign("header", $lang["homepage"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/forum_index_table.tpl");
	exit;
}

function ForumMessage($par='', $err='', $data='')
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;

	IsFileAllowed('forum_edit');

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	if ($err){
		$smarty->assign('error', $lang['forum'][$err]);
	}

	$file_name = "forum.php";

	$data["kcaptcha"] = $config["site_root"].$config_index["kcaptcha"];
	$smarty->assign("guest", $user[ AUTH_GUEST ]);

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

			$data["poster_login"] = $user[ AUTH_LOGIN ];
			$data["poster_id"] = $user[ AUTH_ID_USER ];
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
	$smarty->assign("file_name", $file_name);
	$smarty->display(TrimSlash($config["index_theme_path"])."/forum_index_table.tpl");
}

function NewSubcategoryPost()
{
	global $dbconn, $user;

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
				VALUES ('".$user[ AUTH_ID_USER ]."', '".$data["category_id"]."', '".addslashes($data["subcategory_name"])."', '', now() ) ";
	$dbconn->Execute($strSQL);
	$subcategory_id = $dbconn->_insertid();

	$message = addslashes(bbcode($data["message"]));

	$strSQL = " INSERT INTO ".FORUM_MESSAGES_TABLE." (id_user, subject, message, id_category, id_subcategory, first_post, created_date)
				VALUES ('".$user[ AUTH_ID_USER ]."', '".addslashes($data["post_name"])."', '".$message."', '".$data["category_id"]."', '".$subcategory_id."', '1', now())
				";
	$dbconn->Execute($strSQL);
	ForumIndexPage("subcategory", "new_subcategory_added", $data["category_id"],$subcategory_id);
	exit;

}

function NewPostSave()
{
	global $dbconn, $user;

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
	$data["message"] = strip_tags($_POST["message"]);

	if ( strlen($data["message"])<1 || strlen($data["post_name"])<1 ) {
		ForumMessage("new_post", "empty_fields", $data);
		exit;
	}

	if ($user[ AUTH_GUEST ]) {
		if (!(isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] ==  $_POST['keystring'])) {
			unset($_SESSION['captcha_keystring']);
			ForumMessage("new_post", "invalid_security_code", $data);
			exit;
		}
	}

	$message = addslashes(bbcode($data["message"]));
	$strSQL = " INSERT INTO ".FORUM_MESSAGES_TABLE." (id_user, subject, message, id_category, id_subcategory, first_post, created_date)
				VALUES ('".$user[ AUTH_ID_USER ]."', '".addslashes($data["post_name"])."', '".$message."', '".$data["category_id"]."', '".$data["subcategory_id"]."', '0', now()) ";
	$dbconn->Execute($strSQL);
	ForumIndexPage("subcategory", "new_post_added", $data["category_id"], $data["subcategory_id"]);
	exit;
}

function EditPostSave()
{
	global $dbconn, $user;

	if (isset($_REQUEST['id_post']) && intval($_REQUEST['id_post'])>0 && !$user[ AUTH_GUEST ]) {
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

	if ( strlen($data["message"])<1 || strlen($data["post_name"])<1 ) {
		EditMessage($id_post, "empty_fields", $data);
		exit;
	}

	$strSQL = "SELECT id, created_date FROM ".FORUM_MESSAGES_TABLE." WHERE id='".$id_post."' AND id_user='".$user[ AUTH_ID_USER ]."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]<1) {
		ForumIndexPage();
	} else {
		$created_date = $rs->fields[1];
		$message = addslashes($data["message"]);
		$strSQL = " UPDATE ".FORUM_MESSAGES_TABLE."
					SET subject='".addslashes($data["post_name"])."', message='".$message."', created_date='".$created_date."'
				 	WHERE id='".$id_post."'
				";
		$dbconn->Execute($strSQL);
		ForumIndexPage("subcategory", "edit_post_saved", $data["category_id"], $data["subcategory_id"]);
	}
	exit;
}

function UpdateForumVisit()
{
	global $dbconn, $user;
	
	$strSQL = "SELECT session FROM ".ACTIVE_SESSIONS_TABLE." WHERE id_user='".$user[ AUTH_ID_USER ]."' ";
	$rs = $dbconn->Execute($strSQL);
	$sess = $rs->fields[0];

	$strSQL = "SELECT id, session_id, visit_date, last_visit_date FROM ".FORUM_VISITS_TABLE." WHERE id_user='".$user[ AUTH_ID_USER ]."' ";
	$rs = $dbconn->Execute($strSQL);
	//if we had user session
	if ($rs->fields[0]>0) {
		//check it and if its not active - update
		if ($rs->fields[1] != $sess) {
			$strSQL = "UPDATE ".FORUM_VISITS_TABLE." SET session_id='".$sess."', visit_date=now(), last_visit_date='".$rs->fields[2]."' WHERE id_user='".$user[ AUTH_ID_USER ]."' ";
			$dbconn->Execute($strSQL);
		} else {
			$strSQL = "UPDATE ".FORUM_VISITS_TABLE." SET session_id='".$sess."', visit_date=now(), last_visit_date='".$rs->fields[3]."' WHERE id_user='".$user[ AUTH_ID_USER ]."' ";
			$dbconn->Execute($strSQL);
		}
	} else {
		//insert new session
		$strSQL = "INSERT INTO ".FORUM_VISITS_TABLE." (id_user, session_id, visit_date, last_visit_date) VALUES ('".$user[ AUTH_ID_USER ]."', '".$sess."', now(), '00000000000000') ";
		$dbconn->Execute($strSQL);
	}
	return;
}

function CheckNewPosts($par, $id)
				{
	global $dbconn, $user;

	$cooked_posts = ( isset($_COOKIE['dating_forum_p']) ) ? unserialize($_COOKIE['dating_forum_p']) : array();

	$strSQL = " SELECT last_visit_date FROM ".FORUM_VISITS_TABLE." WHERE id_user='".$user[ AUTH_ID_USER ]."' ";
	$rs = $dbconn->Execute($strSQL);
	$last_visit_date = $rs->fields[0];
	$new = 0;
	switch ($par) {
		case 'index':
			$strSQL = " SELECT DISTINCT id FROM ".FORUM_MESSAGES_TABLE." WHERE id_category='".$id."' AND created_date>'".$last_visit_date."' GROUP BY id ORDER BY id ";
			break;
		case 'category':
			$strSQL = " SELECT DISTINCT id FROM ".FORUM_MESSAGES_TABLE." WHERE id_subcategory='".$id."' AND created_date>'".$last_visit_date."' GROUP BY id ORDER BY id ";
			break;
		default:
			return 0;
			break;
	}
	$rs = $dbconn->Execute($strSQL);
	
	while (!$rs->EOF) {
		if (array_key_exists($rs->fields[0], $cooked_posts)) {
			$new = 0;
		} else {
			$new = 1;
			break;
		}
		$rs->MoveNext();
	}
	return $new;
}

function DeleteMessage()
{
	global $dbconn, $user;
	
	IsFileAllowed('forum_edit');
	
	if (isset($_REQUEST['id_post']) && intval($_REQUEST['id_post'])>0 && !$user[ AUTH_GUEST ]) {
		$id_post = intval($_REQUEST['id_post']);
	} else {
		ForumIndexPage();
		exit;
	}
	$strSQL = " SELECT id, id_category, id_subcategory
				FROM ".FORUM_MESSAGES_TABLE."
				WHERE id='".$id_post."' AND id_user='".$user[ AUTH_ID_USER ]."'
				";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>0) {
		$id_category = $rs->fields[1];
		$id_subcategory = $rs->fields[2];

		$strSQL = " SELECT COUNT(id) FROM ".FORUM_MESSAGES_TABLE." WHERE id_category='".$id_category."' AND id_subcategory='".$id_subcategory."' ";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0] == 1) {
			$strSQL = " DELETE FROM ".FORUM_SUBCATEGORIES_TABLE." WHERE id_category='".$id_category."' AND id='".$id_subcategory."' ";
			$dbconn->Execute($strSQL);
		}
		$strSQL = " DELETE FROM ".FORUM_MESSAGES_TABLE." WHERE id='".$id_post."' ";
		$dbconn->Execute($strSQL);
		ForumIndexPage('subcategory', '', $id_category, $id_subcategory);
		exit;
	} else {
		ForumIndexPage();
		exit;
	}
}

function EditMessage($id_post='', $err='', $data='')
{
	global $lang, $config, $smarty, $dbconn, $user;

	IsFileAllowed('forum_edit');

	if ($id_post == '') {
		if (isset($_REQUEST['id_post']) && intval($_REQUEST['id_post'])>0 && !$user[ AUTH_GUEST ]) {
			$id_post = intval($_REQUEST['id_post']);
		} else {
			ForumIndexPage();
			exit;
		}
	} else {
		$id_post = intval($id_post);
	}
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$file_name = "forum.php";

	if ($err !='') {
		$smarty->assign('error', $lang['forum'][$err]);
	} else {
		$strSQL = "	SELECT 	fm.id, fm.id_user, fm.message, fm.subject,
						fs.id, fs.subcategory,
						fc.id, fc.category
				FROM ".FORUM_MESSAGES_TABLE." fm
				LEFT JOIN ".FORUM_CATEGORIES_TABLE." fc ON fc.id=fm.id_category
				LEFT JOIN ".FORUM_SUBCATEGORIES_TABLE." fs ON fs.id=fm.id_subcategory
				WHERE fm.id='".$id_post."' AND fm.id_user='".$user[ AUTH_ID_USER ]."' ";
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
		}
	}

	$smarty->assign("data", $data);
	$par = 'edit_post';
	$smarty->assign("par", $par);
	$smarty->assign("file_name", $file_name);
	$smarty->assign("header", $lang["homepage"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/forum_index_table.tpl");
	exit;
}

function IsUserBanned($id_user)
{
	global $dbconn;
	
	$rs = $dbconn->Execute("SELECT id FROM ".FORUM_BANS_TABLE." WHERE id_user='".$id_user."'");
	if ($rs->fields[0] > 0) {
		return 1;
	} else {
		return 0;
	}
}

?>