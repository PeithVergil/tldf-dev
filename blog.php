<?php
/**
* Blog
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
include './include/class.images.php';

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

unset($_SESSION['return_to_view']);

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

// dispatcher
switch ($sel) {
	case 'create':				CreateBlogForm();				break;
	case 'add':					AddBlog();						break;
	case 'my_blog':				BlogUserMain();					break;
	case 'post':				NewPostForm();					break;
	case 'image_upload_form':	ImageUploadForm();				break;
	case 'upload_file':			UploadFile();					break;
	case 'save_post':			SavePost();						break;
	case 'edit_post':			NewPostForm('','','edit');		break;
	case 'view_user':			BlogUserMain('','view');		break;
	case 'delete_post':			DeletePost();					break;
	case 'add_comment':			AddCommentForm('','','add');	break;
	case 'view_comments':		AddCommentForm('','','view');	break;
	case 'save_comment':		SaveComment();					break;
	case 'delete_comment':		DeleteBlogComment();			break;
	case 'friends':				FriendsBlog();					break;
	case 'list_category':		ListCategory();					break;
	case 'all_blogs':			AllBlogs();						break;
	default: 					BlogUserMain();					break;
}

exit;


function FriendsBlog()
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$settings = GetSiteSettings(array('icon_male_default','icon_female_default','icons_folder','show_users_connection_str','show_users_comments','show_users_group_str','photos_folder', 'photos_default', 'icon_max_width', 'icon_max_height', 'thumb_max_width', 'thumb_max_height' ));

	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];

	$page = isset($_REQUEST["page"])?$_REQUEST["page"]:"";
	if( (strval($page) == "") || (strval($page) == "0")){ $page = 1;}else{	$page=intval($page);}

	$strSQL = "SELECT a.id_friend FROM ".HOTLIST_TABLE." a WHERE a.id_user='".$user[ AUTH_ID_USER ]."' ORDER BY a.id DESC";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>0) {
		$i = 0;
		while (!$rs->EOF){
			$rs_2 = $dbconn->Execute(" SELECT DISTINCT id FROM ".BLOG_PROFILE_TABLE." WHERE id_user='".$rs->fields[0]."' AND active='1' ");
			if ($rs_2->fields[0]>0) {
				$users_id[$i] = $rs_2->fields[0];
				$i++;
			}
			$rs->MoveNext();
		}
		if (is_array($users_id) && sizeof($users_id)>0) {
			$profile_str = implode(',', $users_id);
		}

		if ($profile_str!='') {
			$strSQL = " SELECT DISTINCT id FROM ".BLOG_POST_TABLE." WHERE id_profile IN (".$profile_str.") ";
			$rs = $dbconn->Execute($strSQL);
			$num_records = $rs->RowCount();
			$lim_min = ($page-1)*$config_index["blog_post_numpage"];
			$lim_max = $config_index["blog_post_numpage"];
			$limit_str = " LIMIT ".$lim_min.", ".$lim_max;
			if ($num_records>0) {
				$strSQL = " SELECT DISTINCT	bpt.id, bpt.id_profile, bpt.title, bpt.body,
											DATE_FORMAT(bpt.creation_date, '".$config["date_format"]."') as creation_date,
											UNIX_TIMESTAMP(bpt.creation_date) as creation_date_stamp,
											bpt.is_hidden, bpt.can_comment,
											bprt.id_user, ut.login, ut.icon_path as author_icon, ut.gender
							FROM ".BLOG_POST_TABLE." bpt
							LEFT JOIN ".BLOG_PROFILE_TABLE." bprt ON bprt.id=bpt.id_profile
							LEFT JOIN ".USERS_TABLE." ut ON bprt.id_user = ut.id
							WHERE bpt.id_profile IN (".$profile_str.") AND bprt.active='1'
							GROUP BY bpt.id ORDER BY bpt.creation_date DESC ".$limit_str;
				$rs = $dbconn->Execute($strSQL);
				if ($rs->fields[0]>0) {
					$i = 0;
					$blog_post = array();
					while(!$rs->EOF){
						$row = $rs->GetRowAssoc(false);
						$blog_post[$i]["id"] = $row["id"];
						$blog_post[$i]["id_profile"] = $row["id_profile"];
						$blog_post[$i]["id_user"] = $row["id_user"];
						$blog_post[$i]["login"] = stripslashes($row["login"]);
						$blog_post[$i]["profile_link"] = "./viewprofile.php?id=".$blog_post[$i]["id_user"];
						$blog_post[$i]["is_hidden"] = $row["is_hidden"];
						$blog_post[$i]["can_comment"] = $row["can_comment"];
						$blog_post[$i]["creation_date"] = $row["creation_date"];
						$blog_post[$i]["creation_time"] = date("H:i", $row["creation_date_stamp"]);

						$strSQL = " SELECT COUNT(*) FROM ".BLOG_COMMENTS_TABLE." WHERE id_post='".$blog_post[$i]["id"]."' ";
						$rs_c = $dbconn->Execute($strSQL);
						if ($rs_c->fields[0]>0) {
							$blog_post[$i]["comments_count"] = $rs_c->fields[0];
						}
						$blog_post[$i]["comments_link"] = "blog.php?sel=view_comments&id_post=".$blog_post[$i]["id"];
						if ($blog_post[$i]["is_hidden"] == 1 && $blog_post[$i]["id_profile"] != $user[ AUTH_ID_USER ]) {
							$blog_post[$i]["show"] = 0;
						} else {
							$blog_post[$i]["show"] = 1;
						}
						if ($blog_post[$i]["can_comment"] == 1 ) {
							$blog_post[$i]["add_comments_link"] = "blog.php?sel=add_comment&id_post=".$blog_post[$i]["id"];
						}
						$blog_post[$i]["title"] = stripslashes($row["title"]);
						$blog_post[$i]["body"] = stripslashes($row["body"]);

						$blog_icon_path = $row["author_icon"]?$row["author_icon"]:$default_photos[$row["gender"]];
						if($blog_icon_path && file_exists($config["site_path"].$settings["icons_folder"]."/".$blog_icon_path)){
							$blog_post[$i]["comment_icon"] = $config["site_root"].$settings["icons_folder"]."/".$blog_icon_path;
						} else {
							$blog_post[$i]["comment_icon"] = $config["server"].$config["site_root"].$settings["photos_folder"]."/".$settings["photos_default"];
						}
						$rs->MoveNext();
						$i++;
					}
					$smarty->assign("blog_posts", $blog_post);
					$param = "blog.php?sel=friends&";
					$smarty->assign("links", GetLinkArray($num_records, $page, $param, $config_index["blog_post_numpage"]));
				}
			}
		} else {
			$smarty->assign("blog_posts", "empty");
		}
	} else {
		$smarty->assign("blog_posts", "empty");
	}
	$form["show_users_connection_str"] = $settings["show_users_connection_str"];
	$form["show_users_comments"] = $settings["show_users_comments"];
	$form["show_users_group_str"] = $settings["show_users_group_str"];

	$form["blog_page"] = 3;
	$form["icon_max_width"] = $settings["thumb_max_width"];
	$form["icon_max_height"] = $settings["thumb_max_height"];
	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["homepage"]);
	$smarty->assign("header_s", $lang["blog"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/blog_user_main_table.tpl");
	exit;
}

function SavePost()
{
	global $dbconn;
	
	$data = $_POST;
	$err = '';
	
	if (isset($data["edit"]) && $data["edit"] == 1){
		$par = "edit";
	}
	
	if (intval($data["id_profile"]) > 0) {
		if (IsUserBlog(intval($data["id_profile"])) == 0) {
			NewPostForm('you_have_no_blog', $data, $par);
			return;
		}
	} else {
		BlogUserMain();
	}
	
	if ((strip_tags(trim($data["post_title"]))) == '' || (strip_tags($data["post_body"])) == '') {
		$err = 'empty_fields';
		NewPostForm($err, $data, $par);
		return;
	}
	
	if (BadWordsCont(strip_tags($data["post_title"]), 7)) {
		NewPostForm("badword_finding_7_3", $data, $par);
		return;
	}
	
	if (check_filter(strip_tags($data["post_title"]))) {
		NewPostForm("info_finding_1", $data, $par);
		return;
	}
	
	if (BadWordsCont(strip_tags($data["post_body"]), 7)) {
		NewPostForm("badword_finding_7_4", $data, $par);
		return;
	}
	
	if (check_filter(strip_tags($data["post_body"]))) {
		NewPostForm("info_finding_1", $data, $par);
		return;
	}

	$post_body = bbcode_blog(strip_tags($data["post_body"]));
	$data["hidden_post"] = isset($data["hidden_post"])?intval($data["hidden_post"]):0;
	$data["can_comment"] = isset($data["can_comment"])?intval($data["can_comment"]):0;

	if (isset($par) && $par == 'edit') {
		$strSQL = " SELECT id FROM ".BLOG_POST_TABLE." WHERE id='".intval($data["id_post"])."' ";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0]<1) {
			BlogUserMain();
			exit;
		}
		$strSQL = " UPDATE ".BLOG_POST_TABLE." SET
					title='".addslashes(strip_tags(trim($data["post_title"])))."', body='".addslashes($post_body)."', creation_date=now(),
					is_hidden='".$data["hidden_post"]."', can_comment='".$data["can_comment"]."'
					WHERE id='".intval($data["id_post"])."' ";
	} else {
		$strSQL = " INSERT INTO ".BLOG_POST_TABLE."
						(id_profile,title, body, creation_date, is_hidden, can_comment)
					VALUES
						('".intval($data["id_profile"])."', '".addslashes(strip_tags(trim($data["post_title"])))."', '".addslashes($post_body)."', now(), '".$data["hidden_post"]."', '".$data["can_comment"]."') ";
		$err = 'post_created';
	}
	$dbconn->Execute($strSQL);
	BlogUserMain($err);
	return;
}

function SaveComment()
{
	global $dbconn, $user;
	
	$data = $_POST;
	$id_reply = isset($_GET["id_reply"])?intval($_GET["id_reply"]):0;
	
	if (isset($data["id_post"]) && intval($data["id_post"]) > 0) {
		$strSQL = " SELECT id_profile, can_comment FROM ".BLOG_POST_TABLE." WHERE id='".intval($data["id_post"])."' ";
		$rs = $dbconn->Execute($strSQL);
		$id_blog = $rs->fields[0];
		$can_comment = $rs->fields[1];
		$is_user = IsUserBlog($id_blog);
		if ($can_comment == '0' && $is_user == 0) {
			$arr["id_post"] = $data["id_post"];
			AddCommentForm('', $arr);
		}
	} else {
		BlogUserMain();
	}

	if (strip_tags($data["comment_body"]) == '') {
		AddCommentForm('empty_fields', $data, 'add', $id_reply);
		return;
	}
	if (BadWordsCont(strip_tags($data["comment_title"]), 7)) {
		AddCommentForm("badword_finding_7_5", $data, 'add', $id_reply);
		return;
	}
	if (check_filter(strip_tags($data["comment_title"]))) {
		AddCommentForm("info_finding_1", $data, 'add', $id_reply);
		return;
	}
	if (BadWordsCont(strip_tags($data["comment_body"]), 7)) {
		AddCommentForm("badword_finding_7_6", $data, 'add', $id_reply);
		return;
	}
	if (check_filter(strip_tags($data["comment_body"]))) {
		AddCommentForm("info_finding_1", $data, 'add', $id_reply);
		return;
	}
	
	$post_body = bbcode_blog(strip_tags($data["comment_body"]));
	
	if ($id_reply > 0) {
		$strSQL = " INSERT INTO ".BLOG_COMMENTS_TABLE."
						(id_user, id_post, title, body, creation_date, id_comment, deleted)
					VALUES
						('".$user[ AUTH_ID_USER ]."', '".$data["id_post"]."', '".addslashes(strip_tags(trim($data["comment_title"])))."', '".addslashes($post_body)."', now(), '".$id_reply."', '0') ";
	} else {
		$strSQL = " INSERT INTO ".BLOG_COMMENTS_TABLE."
						(id_user, id_post, title, body, creation_date, deleted)
					VALUES
						('".$user[ AUTH_ID_USER ]."', '".$data["id_post"]."', '".addslashes(strip_tags(trim($data["comment_title"])))."', '".addslashes($post_body)."', now(), '0') ";
	}
	
	$dbconn->Execute($strSQL);
	$arr["id_post"] = $data["id_post"];
	AddCommentForm('comment_created', $arr);
	return;
}


function ImageUploadForm($err='')
{
	global $lang, $config, $smarty, $dbconn;

	IndexHomePage();
	$id_profile = intval($_GET["id_profile"]);
	if ($id_profile>0) {
		$strSQL = " SELECT COUNT(*) FROM ".BLOG_PROFILE_TABLE." WHERE id='".$id_profile."' AND active='1' ";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0]>0) {
			$form["id_profile"] = $id_profile;
		}
	}
	if ($err !=''){
		$form["err"] = $lang["err"][$err];
	}
	$smarty->assign("form", $form);
	$smarty->display(TrimSlash($config["index_theme_path"])."/blog_image_upload_form.tpl");
	exit;
}


function BlogUserMain($err='', $par='')
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$settings = GetSiteSettings(array('icon_male_default','icon_female_default','icons_folder','show_users_connection_str','show_users_comments','show_users_group_str'));

	switch ($par) {
		case "view":
			$id_blog = intval($_GET["id_blog"]);
			if ($id_blog>0) {
				$blog_info = GetBlogInfo($id_blog, 'user');
				if ($blog_info == 'empty') {
					CreateBlogForm();
					exit;
				} else {
					$form["view"] = 1;
					$smarty->assign("blog_info", $blog_info);
				}
				$blog_posts = GetBlogPost($id_blog, 'user','sel=view_user&id_blog='.$id_blog.'&');
				$smarty->assign("blog_posts", $blog_posts);
			} else {
				CreateBlogForm();
				exit;
			}
			$form["is_user"] = IsUserBlog($blog_info["id"]);
			break;
		default:
			$strSQL = " SELECT id FROM ".BLOG_PROFILE_TABLE." WHERE id_user='".$user[ AUTH_ID_USER ]."' AND active='1' ";
			$rs = $dbconn->Execute($strSQL);
			if ($rs->fields[0]>0) {
				$id_blog = $rs->fields[0];
				$blog_info = GetBlogInfo($id_blog, 'user');
				if ($blog_info == 'empty') {
					CreateBlogForm();
					exit;
				} else {
					$smarty->assign("blog_info", $blog_info);
				}
				$blog_posts = GetBlogPost($id_blog, 'user','?sel=my_blog&');
				$smarty->assign("blog_posts", $blog_posts);
			} else {
				$strSQL = " SELECT id FROM ".BLOG_PROFILE_TABLE." WHERE id_user='".$user[ AUTH_ID_USER ]."' AND active='0' ";
				$rs = $dbconn->Execute($strSQL);
				if ($rs->fields[0]>0) {
					$err = 'you_blog_deactivated_by_admin';
				}
				CreateBlogForm($err);
				exit;
			}
			$form["is_user"] = IsUserBlog($blog_info["id"]);
			break;
	}

	$form["show_users_connection_str"] = $settings["show_users_connection_str"];
	$form["show_users_comments"] = $settings["show_users_comments"];
	$form["show_users_group_str"] = $settings["show_users_group_str"];

	if ($err !=''){
		$form["err"] = $lang["err"][$err];
	}

	$form["blog_page"] = 1;
	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["homepage"]);
	$smarty->assign("header_s", $lang["blog"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/blog_user_main_table.tpl");
	exit;
}

function CreateBlogForm($err='', $data='')
				{
	global $lang, $config, $smarty, $dbconn, $user;
	
	$strSQL = " SELECT id FROM ".BLOG_PROFILE_TABLE." WHERE id_user='".$user[ AUTH_ID_USER ]."' AND active='1' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>0) {
		BlogUserMain('you_have_blog');
		exit;
	}

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$settings = GetSiteSettings(array('icon_male_default','icon_female_default','icons_folder','show_users_connection_str','show_users_comments','show_users_group_str'));

	$form["blog_page"] = 1;

	$form["guest_user"] = $user[ AUTH_GUEST ];
	$form["is_user"] = 1;

	$form["show_users_connection_str"] = $settings["show_users_connection_str"];
	$form["show_users_comments"] = $settings["show_users_comments"];
	$form["show_users_group_str"] = $settings["show_users_group_str"];

	if ($err !=''){
		if ($err =='you_blog_deactivated_by_admin') {
			$form['inactive'] = 1;
		}
		$form["err"] = $lang["blog"]["err"][$err];
	}

	$strSQL = " SELECT id, category_name FROM ".BLOG_CATEGORIES_TABLE." ORDER BY category_name ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->RowCount()>0) {
		$i = 0; $category = array();
		while (!$rs->EOF) {
			$category[$i]["id"] = $rs->fields[0];
			if (isset($data["blog_category"]) && ($data["blog_category"] == $rs->fields[0])) {
				$category[$i]["sel"] = 1;
			}
			$category[$i]["name"] = stripslashes($rs->fields[1]);
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign("categories", $category);
	}

	$smarty->assign("form", $form);
	$smarty->assign("data", $data);

	$smarty->assign("header", $lang["homepage"]);
	$smarty->assign("header_s", $lang["blog"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/blog_create_form.tpl");
	exit;
}

function AddCommentForm($err='', $data='', $par='', $id_reply='')
				{
	global $lang, $config, $smarty, $dbconn, $user;
	
	$id_post = isset($_GET["id_post"])?intval($_GET["id_post"]):0;

	if ($id_post<1) {
		$id_post = intval($data["id_post"]);
		if ($id_post<1) {
			BlogUserMain();
			exit;
		}
	}
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$settings = GetSiteSettings(array('icon_male_default','icon_female_default','icons_folder','show_users_connection_str','show_users_comments','show_users_group_str','icon_max_width', 'icon_max_height','photos_folder','photos_default'));

	$strSQL = " SELECT 	post.id, post.can_comment, prof.id as id_blog, post.is_hidden,
						post.title, post.body, DATE_FORMAT(post.creation_date,'".$config["date_format"]."') as creation_date,
						UNIX_TIMESTAMP(post.creation_date) as creation_date_stamp
				FROM ".BLOG_POST_TABLE." post
				LEFT JOIN ".BLOG_PROFILE_TABLE." prof ON prof.id=post.id_profile
				WHERE post.id='".$id_post."' AND prof.active='1'
				";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>0){
		$row = $rs->GetRowAssoc(false);
		$data["id_blog"] = $row["id_blog"];
		$blog_info = GetBlogInfo($data["id_blog"], 'user');
		if ($blog_info == 'empty') {
			BlogUserMain();
			exit;
		} else {
			$smarty->assign("blog_info", $blog_info);
		}
		$data["is_user"] = IsUserBlog($data["id_blog"]);
		$data["is_hidden"] = $row["is_hidden"];
		if ($data["is_hidden"] == 1) {
			if ($data["is_user"]== 0) {
				BlogUserMain();
			}
			$data["show"] = 0;
		} else {
			$data["show"] = 1;
		}
		$data["can_comment"] = $row["can_comment"];
		if ($par == 'add') {
			if ($id_reply !='') {
				$form["edit"] = 1;
				$form["id_reply"] = $id_reply;
			} else {
				$form["edit"] = 1;
				$form["id_reply"] = 'main';
			}
			$data["blog_comments"] = GetBlogComments($id_post,'user','?sel=add_comment&id_post='.$id_post.'&');
		} else {
			$data["blog_comments"] = GetBlogComments($id_post,'user','?sel=view_comments&id_post='.$id_post.'&');
		}
		$data["edit_link"] = "blog.php?sel=edit_post&id_post=".$id_post;
		$data["delete_link"] = "blog.php?sel=delete_post&id_post=".$id_post;
		$data["title"] = stripslashes($row["title"]);
		$data["body"] = stripslashes($row["body"]);
		$data["creation_date"] = $row["creation_date"];
		$data["creation_time"] = date("H:i", $row["creation_date_stamp"]);

		$strSQL = " SELECT COUNT(*) FROM ".BLOG_COMMENTS_TABLE." WHERE id_post='".$id_post."' ";
		$rs_c = $dbconn->Execute($strSQL);
		$data["comments_count"] = intval($rs_c->fields[0]);
	}
	$form["id_post"] = $id_post;
	if ($data["is_user"] == 1) {
		$form["blog_page"] = 1;
	} else {
		$form["blog_page"] = 3;
	}

	$form["guest_user"] = $user[ AUTH_GUEST ];
	$form["icon_max_width"] = $settings["icon_max_width"];
	$form["icon_max_height"] = $settings["icon_max_height"];

	$form["default_icon"] = $config["server"].$config["site_root"].$settings["photos_folder"]."/".$settings["photos_default"];

	$form["show_users_connection_str"] = $settings["show_users_connection_str"];
	$form["show_users_comments"] = $settings["show_users_comments"];
	$form["show_users_group_str"] = $settings["show_users_group_str"];

	if ($err !=''){
		$form["err"] = $lang["err"][$err];
	}
	$smarty->assign("form", $form);
	$smarty->assign("data", $data);

	$smarty->assign("header", $lang["homepage"]);
	$smarty->assign("header_s", $lang["blog"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/blog_post_table.tpl");
	exit;
}

function NewPostForm($err='', $data='', $par='')
				{
	global $lang, $config, $smarty, $dbconn, $user;
	
	$strSQL = " SELECT id FROM ".BLOG_PROFILE_TABLE." WHERE id_user='".$user[ AUTH_ID_USER ]."' AND active='1' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]<1) {
		CreateBlogForm('you_have_no_blog');
		exit;
	} else {
		$form["id_profile"] = $rs->fields[0];
	}
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$settings = GetSiteSettings(array('icon_male_default','icon_female_default','icons_folder','show_users_connection_str','show_users_comments','show_users_group_str'));

	$form["is_user"] = IsUserBlog($form["id_profile"]);
	if ($par == 'edit' && $form["is_user"] !=1) {
		CreateBlogForm('you_have_no_blog');
		exit;
	} elseif ($par == 'edit') {
		$id_post = intval($_GET["id_post"]);
		if ($id_post < 0) {
			BlogUserMain(); exit;
		}
		$strSQL = " SELECT id, id_profile, title, body, DATE_FORMAT(creation_date,'".$config["date_format"]."') as creation_date, is_hidden, can_comment
					FROM ".BLOG_POST_TABLE."
					WHERE id='".$id_post."' ";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0]>0) {
			$row = $rs->GetRowAssoc(false);
			$data["id"] = $row["id"];
			$data["id_profile"] = $row["id_profile"];
			$data["post_title"] = stripslashes($row["title"]);
			$data["post_body"] = bbdecode_blog(stripslashes($row["body"]));
			$data["creation_date"] = $row["creation_date"];
			$data["is_hidden"] = $row["is_hidden"];
			$data["can_comment"] = $row["can_comment"];
			$form["edit"] = 1;
		} else {
			BlogUserMain(); exit;
		}
	}
	$form["blog_page"] = 1;
	$form["guest_user"] = $user[ AUTH_GUEST ];

	$form["show_users_connection_str"] = $settings["show_users_connection_str"];
	$form["show_users_comments"] = $settings["show_users_comments"];
	$form["show_users_group_str"] = $settings["show_users_group_str"];

	if ($err !=''){
		$form["err"] = $lang["err"][$err];
	}
	$smarty->assign("form", $form);
	$smarty->assign("data", $data);

	$smarty->assign("header", $lang["homepage"]);
	$smarty->assign("header_s", $lang["blog"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/blog_post_form.tpl");
	exit;
}


function AddBlog()
{
	global $lang, $dbconn, $user;
	$strSQL = " SELECT id FROM ".BLOG_PROFILE_TABLE." WHERE id_user='".$user[ AUTH_ID_USER ]."' AND active='1' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>0) {
		BlogUserMain('you_have_blog');
		exit;
	}
	$data = $_POST;

	if ((strip_tags(trim($data["blog_name"]))) == '' || (strip_tags(htmlspecialchars($data["description"]))) == '') {
		$err = 'empty_fields';
		CreateBlogForm($err, $data);
		return;
	}
	if (BadWordsCont(strip_tags($data["blog_name"]), 7)) {
		CreateBlogForm("badword_finding_7_1", $data);
		return;
	}
	if (check_filter(strip_tags($data["blog_name"]))) {
		CreateBlogForm("info_finding_1", $data);
		return;
	}
	if (BadWordsCont(strip_tags(htmlspecialchars($data["description"])), 7)) {
		CreateBlogForm("badword_finding_7_2", $data);
		return;
	}
	if (check_filter(strip_tags(htmlspecialchars($data["description"])))) {
		CreateBlogForm("info_finding_1", $data);
		return;
	}
	$strSQL = " INSERT INTO ".BLOG_PROFILE_TABLE."
					(id_user, title, description, creation_date, is_hidden, id_category)
				VALUES
					('".$user[ AUTH_ID_USER ]."', '".addslashes((strip_tags(trim($data["blog_name"]))))."',
					'".addslashes(htmlspecialchars(strip_tags($data["description"])))."', now(),
					'".intval($data["hidden_blog"])."', '".intval($data["blog_category"])."')
				";
	$rs = $dbconn->Execute($strSQL);

	$blog_id = $dbconn->Insert_Id();

	$first_title = $lang["blog"]["first_blog_title"];
	$first_body = $lang["blog"]["first_blog_post_1"]." <a href='blog.php?sel=post'>".$lang["blog"]["first_blog_post_2"]."</a>!";

	$strSQL = " INSERT INTO ".BLOG_POST_TABLE."
					(id_profile, title, body, creation_date, is_hidden, can_comment)
				VALUES
					('".$blog_id."', '".addslashes($first_title)."', '".addslashes($first_body)."', now(), '1', '0')
				";
	$rs = $dbconn->Execute($strSQL);
	$err = "blog_was_created";
	BlogUserMain($err);
	exit;
}

function UploadFile()
{
	global $config, $dbconn, $user;
	
	$id_profile = intval($_REQUEST["id_profile"]);
	if ($id_profile < 1) {
		echo "<script>window.opener.focus(); window.close();</script>";
		exit;
	}
	if (IsUserBlog($id_profile) == 0) {
		echo "<script>window.opener.focus(); window.close();</script>";
		exit;
	}

	$upload = $_FILES["upload_file"];
	$images_obj = new Images($dbconn);
	$err_upload = $images_obj->UploadImages($upload, $user[ AUTH_ID_USER ], 'blog', '', '', $id_profile, '1'); //1 mean "admin mode" => approve upload without admin approval
	if ($err_upload){
		$err = 'upload_err';
		ImageUploadForm($err);
	} else {
		$blog_folder = GetSiteSettings('blog_folder');
		$strSQL = " SELECT MAX(id) FROM ".BLOG_UPLOADS_TABLE." WHERE id_post='".$id_profile."' ";
		$rs = $dbconn->Execute($strSQL);
		$strSQL = " SELECT upload_path FROM ".BLOG_UPLOADS_TABLE." WHERE id='".$rs->fields[0]."' ";
		$rs = $dbconn->Execute($strSQL);
		$upload_path = $rs->fields[0];
		echo "<script>javascript: window.opener.document.getElementById('post_body').value +='[img=".$config["server"].$config["site_root"].$blog_folder."/".$upload_path." align=left hspace=3 vspace=3]".$config["server"].$config["site_root"].$blog_folder."/thumb_".$upload_path."[/img]'; window.opener.focus(); window.close();</script>";
	}
	exit;
}

function GetBlogInfo($id_blog, $par='')
				{
	global $config, $dbconn, $user;
	$id_blog = intval($id_blog);
	if ($id_blog>0){
		if ($par == 'user') {
			$strSQL = " SELECT	bpt.id, bpt.id_user, bpt.title, bpt.description, DATE_FORMAT(bpt.creation_date,'".$config["date_format"]."') as creation_date,
								bpt.is_hidden, ut.login
						FROM ".BLOG_PROFILE_TABLE." bpt
						LEFT JOIN ".USERS_TABLE." ut ON bpt.id_user = ut.id
						WHERE bpt.id='".$id_blog."' AND bpt.active='1' ";
			$rs = $dbconn->Execute($strSQL);
			if ($rs->fields[0]>0) {
				$blog_info = array();
				$row = $rs->GetRowAssoc(false);
				$blog_info["id"] = $row["id"];
				$blog_info["id_user"] = $row["id_user"];
				$blog_info["user_login"] = stripslashes($row["login"]);
				$blog_info["is_hidden"] = $row["is_hidden"];
				if ($blog_info["id_user"] == $user[ AUTH_ID_USER ]){
					$blog_info["is_user"] = 1;
				}
				if ($blog_info["is_hidden"] == 1 && $blog_info["id_user"] != $user[ AUTH_ID_USER ]){
					$blog_info["show"] = 0;
				} else {
					$blog_info["show"] = 1;
				}
				$blog_info["title"] = stripslashes($row["title"]);
				$blog_info["description"] = stripslashes($row["description"]);
				$blog_info["creation_date"] = $row["creation_date"];
			}
		}
	}
	if (sizeof($blog_info)>0) {
		return $blog_info;
	} else {
		return "empty";
	}
}

function GetBlogPost($id_blog, $par='', $add_to_page='')
				{
	global $config, $config_index, $smarty, $dbconn, $user;
	$id_blog = intval($id_blog);
	$page = isset($_REQUEST["page"])?$_REQUEST["page"]:"";
	if( (strval($page) == "") || (strval($page) == "0")){ $page = 1;}else{	$page=intval($page);}

	if ($id_blog>0){
		if ($par == 'user') {
			$strSQL = " SELECT DISTINCT id FROM ".BLOG_POST_TABLE." WHERE id_profile='".$id_blog."' GROUP BY id ";
			$rs = $dbconn->Execute($strSQL);
			$num_records = $rs->RowCount();
			// page
			$lim_min = ($page-1)*$config_index["blog_post_numpage"];
			$lim_max = $config_index["blog_post_numpage"];
			$limit_str = " LIMIT ".$lim_min.", ".$lim_max;
			if ($num_records>0){
				$strSQL = " SELECT DISTINCT	bpt.id, bpt.id_profile, bpt.title, bpt.body,
											DATE_FORMAT(bpt.creation_date, '".$config["date_format"]."') as creation_date,
											UNIX_TIMESTAMP(bpt.creation_date) as creation_date_stamp,
											bpt.is_hidden, bpt.can_comment,
											bprt.id_user, ut.login
							FROM ".BLOG_POST_TABLE." bpt
							LEFT JOIN ".BLOG_PROFILE_TABLE." bprt ON bprt.id=bpt.id_profile
							LEFT JOIN ".USERS_TABLE." ut ON bprt.id_user = ut.id
							WHERE bpt.id_profile='".$id_blog."' AND bprt.active='1'
							GROUP BY bpt.id ORDER BY bpt.creation_date DESC ".$limit_str;
				$rs = $dbconn->Execute($strSQL);
				if ($rs->fields[0]>0) {
					$i = 0;
					$blog_post = array();
					while(!$rs->EOF){
						$row = $rs->GetRowAssoc(false);
						$blog_post[$i]["id"] = $row["id"];
						$blog_post[$i]["id_profile"] = $row["id_profile"];
						$blog_post[$i]["id_user"] = $row["id_user"];
						$blog_post[$i]["login"] = stripslashes($row["login"]);
						$blog_post[$i]["is_hidden"] = $row["is_hidden"];
						$blog_post[$i]["can_comment"] = $row["can_comment"];
						$blog_post[$i]["creation_date"] = $row["creation_date"];
						$blog_post[$i]["creation_time"] = date("H:i", $row["creation_date_stamp"]);
						if ($blog_post[$i]["id_user"] == $user[ AUTH_ID_USER ]) {
							$blog_post[$i]["is_user"] = 1;
							$blog_post[$i]["edit_link"] = "blog.php?sel=edit_post&id_post=".$blog_post[$i]["id"];
							$blog_post[$i]["delete_link"] = "blog.php?sel=delete_post&id_post=".$blog_post[$i]["id"];
						}
						$strSQL = " SELECT COUNT(*) FROM ".BLOG_COMMENTS_TABLE." WHERE id_post='".$blog_post[$i]["id"]."' ";
						$rs_c = $dbconn->Execute($strSQL);
						if ($rs_c->fields[0]>0) {
							$blog_post[$i]["comments_count"] = $rs_c->fields[0];
							$blog_post[$i]["comments_link"] = "blog.php?sel=view_comments&id_post=".$blog_post[$i]["id"];
						}
						if ($blog_post[$i]["is_hidden"] == 1 && $blog_post[$i]["id_profile"] != $user[ AUTH_ID_USER ]){
							$blog_post[$i]["show"] = 0;
						} else {
							$blog_post[$i]["show"] = 1;
						}
						$blog_post[$i]["add_comments_link"] = "blog.php?sel=add_comment&id_post=".$blog_post[$i]["id"];

						$blog_post[$i]["title"] = stripslashes($row["title"]);
						$blog_post[$i]["body"] = stripslashes($row["body"]);
						$rs->MoveNext();
						$i++;
					}
					$param = "blog.php?".$add_to_page;
					$smarty->assign("links", GetLinkArray($num_records, $page, $param, $config_index["blog_post_numpage"]));
				}
			}
		}
	}
	if (sizeof($blog_post)>0) {
		return $blog_post;
	} else {
		return "empty";
	}
}

function bbcode_blog($text) {
	//	$text = trim($text);
	$text = ereg_replace("\n", "<br>", $text);
	$text = preg_replace("/\[b\](.+?)\[\/b\]/is", "<b>\\1</b>", $text);
	$text = preg_replace("/\[i\](.+?)\[\/i\]/is", "<i>\\1</i>", $text);
	$text = preg_replace("/\[u\](.+?)\[\/u\]/is", "<u>\\1</u>", $text);
	$text = preg_replace("/\[img=(.+?)\salign=(.+?)\shspace=(.+?)\svspace=(.+?)\](.+?)\[\/img\]{1}/is", "<a href='\\1' target='_blank'><img src='\\5' align='\\2' class='icon' alt='' hspace='\\3' vspace='\\4'></a>", $text);
	$text = preg_replace("/\[url=([^<]+?)\](.+?)\[\/url\]{1}/is", "<a href='\\1' target='_blank'>\\2</a>", $text);
	$text = preg_replace("/\[url\](.+?)\[\/url\]{1}/is", "<a href='\\1' target='_blank'>\\1</a>", $text);
	return $text;
}

function bbdecode_blog($text) {
	$text = ereg_replace("<br>", "\n", $text);
	$text = preg_replace("/\<b\>(.+?)\<\/b\>/is", "[b]\\1[/b]", $text);
	$text = preg_replace("/\<i\>(.+?)\<\/i\>/is", "[i]\\1[/i]", $text);
	$text = preg_replace("/\<u\>(.+?)\<\/u\>/is", "[u]\\1[/u]", $text);
	$text = preg_replace("/\<a\shref=\'(.+?)\'\starget=\'\_blank\'\>\<img\ssrc=\'(.+?)\'\salign=\'(.+?)\'\sclass=\'icon\'\salt=\'\'\shspace=\'(.+?)\'\svspace=\'(.+?)\'\>\<\/a\>{1}/is", "[img=\\1 align=\\3 hspace=\\4 vspace=\\5]\\2[/img]", $text);
	$text = preg_replace("/\<a\shref=\'(.+?)\'\starget=\'\_blank\'\>(.+?)\<\/a\>{1}/is", "[url=\\1]\\2[/url]", $text);
	return $text;
}

function IsUserBlog($id_blog)
{
	global $dbconn, $user;
	
	$rs = $dbconn->Execute("SELECT id_user FROM ".BLOG_PROFILE_TABLE." WHERE id='".$id_blog."' AND active='1'");
	if ($rs->fields[0] != $user[ AUTH_ID_USER ]) {
		//not blog author
		return 0;
	}
	
	return 1;
}

function DeletePost()
{
	global $dbconn;
	$id_post = intval($_GET["id_post"]);
	if ($id_post<1) {
		BlogUserMain();exit;
	}
	$strSQL = " SELECT id_profile FROM ".BLOG_POST_TABLE." WHERE id='".$id_post."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>0) {
		if (IsUserBlog($rs->fields[0]) == 1) {
			$dbconn->Execute("DELETE FROM ".BLOG_POST_TABLE." WHERE id='".$id_post."' ");
			$dbconn->Execute("DELETE FROM ".BLOG_COMMENTS_TABLE." WHERE  id_post ='".$id_post."' ");
		}
	}
	BlogUserMain(); exit;
}

function GetBlogComments($id_post, $par='', $add_to_page='')
				{
	global $config, $config_index, $smarty, $dbconn, $user, $arr, $k;

	$settings = GetSiteSettings(array('icon_male_default','icon_female_default','photos_default','icon_max_width','icons_folder','photos_folder'));

	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];

	$id_post = intval($id_post);
	$page = isset($_REQUEST["page"])?$_REQUEST["page"]:"";
	if( (strval($page) == "") || (strval($page) == "0")){ $page = 1;}else{	$page=intval($page);}
	if ($id_post>0){
		if ($par == 'user') {
			$strSQL = " SELECT DISTINCT id FROM ".BLOG_COMMENTS_TABLE." WHERE id_post='".$id_post."' AND id_comment='0' GROUP BY id ";
			$rs = $dbconn->Execute($strSQL);
			$num_records = $rs->RowCount();
			// page
			$lim_min = ($page-1)*$config_index["blog_comment_numpage"];
			$lim_max = $config_index["blog_comment_numpage"];
			$limit_str = " LIMIT ".$lim_min.", ".$lim_max;
			if ($num_records>0){
				$strSQL = "	SELECT DISTINCT com.id, com.id_user, com.title, com.body, com.deleted,
											DATE_FORMAT(com.creation_date, '".$config["date_format"]."') as creation_date,
											UNIX_TIMESTAMP(com.creation_date) as creation_date_stamp, ut.login, ut.icon_path as blog_icon_path, ut.gender,
											pro.id_user as author_id, post.id_profile as id_blog
							FROM ".BLOG_COMMENTS_TABLE." com
							LEFT JOIN ".USERS_TABLE." ut ON com.id_user = ut.id
							LEFT JOIN ".BLOG_POST_TABLE." post ON post.id='".$id_post."'
							LEFT JOIN ".BLOG_PROFILE_TABLE." pro ON pro.id=post.id_profile
							WHERE com.id_post='".$id_post."' AND com.id_comment='0' AND pro.active='1'
							GROUP BY com.id ORDER BY com.creation_date ASC ".$limit_str;
				$rs = $dbconn->Execute($strSQL);
				if ($rs->fields[0]>0) {
					$i = 0;
					$blog_comment = array();
					
					while(!$rs->EOF){
						$row = $rs->GetRowAssoc(false);
						$blog_comment[$i]["id"] = $row["id"];
						$id_comment = $blog_comment[$i]["id"];
						$arr = array(); 	$k = 1;
						GetCommentIdArr($id_comment, $id_post);
						if (sizeof($arr)>0) {
							$blog_comment[$i]["sub_comments"] = GetSubComments($arr, $id_post);
						}
						$blog_comment[$i]["id_user"] = $row["id_user"];
						if ($blog_comment[$i]["id_user"] == $user[ AUTH_ID_USER ]) {
							$blog_comment[$i]["is_user"] = 1;
						}
						$blog_comment[$i]["profile_link"] = "./viewprofile.php?id=".$row["id_user"];
						//						$blog_comment[$i]["profile_link"] = "./blog.php?sel=view_user&id_blog=".$row["id_blog"];
						$blog_comment[$i]["login"] = stripslashes($row["login"]);

						$blog_icon_path = $row["blog_icon_path"]?$row["blog_icon_path"]:$default_photos[$row["gender"]];
						if($blog_icon_path && file_exists($config["site_path"].$settings["icons_folder"]."/".$blog_icon_path)){
							$blog_comment[$i]["comment_icon"] = $config["site_root"].$settings["icons_folder"]."/".$blog_icon_path;
						} else {
							$blog_comment[$i]["comment_icon"] = $config["server"].$config["site_root"].$settings["photos_folder"]."/".$settings["photos_default"];
						}

						$blog_comment[$i]["creation_date"] = $row["creation_date"];
						$blog_comment[$i]["creation_time"] = date("H:i", $row["creation_date_stamp"]);
						$blog_comment[$i]["author_id"] = $row["author_id"];
						if ($blog_comment[$i]["id_user"] == $user[ AUTH_ID_USER ] || $blog_comment[$i]["author_id"] == $user[ AUTH_ID_USER ]) {
							$blog_comment[$i]["can_edit"] = 1;
							$blog_comment[$i]["delete_link"] = "blog.php?sel=delete_comment&id_post=".$id_post."&id_comment=".$blog_comment[$i]["id"];
						}
						$blog_comment[$i]["title"] = stripslashes($row["title"]);
						$blog_comment[$i]["body"] = stripslashes($row["body"]);
						$blog_comment[$i]["deleted"] = $row["deleted"];
						$rs->MoveNext();
						$i++;
					}
					$param = "blog.php".$add_to_page;
					$smarty->assign("links", GetLinkArray($num_records, $page, $param, $config_index["blog_comment_numpage"]));
				}
			}
		}
	}
	if (isset($blog_comment) && sizeof($blog_comment)>0) {
		return $blog_comment;
	} else {
		return "empty";
	}
}

function GetCommentIdArr($id_comment, $id_post)
{
	global $dbconn, $arr, $k;
	$strSQL = " SELECT DISTINCT com.id
				FROM ".BLOG_COMMENTS_TABLE." com
				WHERE com.id_post='".$id_post."' AND com.id_comment='".$id_comment."'
				GROUP BY com.id ORDER BY com.creation_date ASC ";
	$rs_2 = $dbconn->Execute($strSQL);
	if ($rs_2->fields[0]>0) {
		while(!$rs_2->EOF) {
			array_push($arr ,$rs_2->fields[0]."_".$k);
			$k++;
			GetCommentIdArr($rs_2->fields[0], $id_post);
			$k--;
			$rs_2->MoveNext();
		}
	}
	return;
}

function GetSubComments($array, $id_post)
{
	global $config, $config_index, $dbconn, $user;
	
	$new_arr = array();
	$i = 0;
	
	foreach ($array as $value) {
		$new_arr = explode("_", $value);
		$id[$i] = $new_arr[0];
		$level[$new_arr[0]] = $new_arr[1];
		$i++;
	}
	
	$str = implode(",", $id);

	$settings = GetSiteSettings(array('icon_male_default','icon_female_default','photos_default','icon_max_width','icons_folder','photos_folder'));

	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];

	$strSQL = "	SELECT DISTINCT com.id, com.id_user, com.title, com.body, com.id_comment, com.deleted,
							DATE_FORMAT(com.creation_date, '".$config["date_format"]."') as creation_date,
							UNIX_TIMESTAMP(com.creation_date) as creation_date_stamp, ut.login, ut.icon_path as blog_icon_path, ut.gender,
							pro.id_user as author_id, post.id_profile as id_blog
			FROM ".BLOG_COMMENTS_TABLE." com
			LEFT JOIN ".USERS_TABLE." ut ON com.id_user = ut.id
			LEFT JOIN ".BLOG_POST_TABLE." post ON post.id='".$id_post."'
			LEFT JOIN ".BLOG_PROFILE_TABLE." pro ON pro.id=post.id_profile
			WHERE com.id_post='".$id_post."' AND com.id IN (".$str.") AND pro.active='1'
			GROUP BY com.id ORDER BY com.id ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>0) {
		$i = 0;
		$blog_comment = array();
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$blog_comment[$i]["id"] = $row["id"];
			$blog_comment[$i]["id_comment"] = $row["id_comment"];
			$blog_comment[$i]["id_user"] = $row["id_user"];
			$blog_comment[$i]["author_id"] = $row["author_id"];
			if ($blog_comment[$i]["id_user"] == $user[ AUTH_ID_USER ]) {
				$blog_comment[$i]["is_user"] = 1;
			}
			$blog_comment[$i]["profile_link"] = "./viewprofile.php?id=".$row["id_user"];
			//$blog_comment[$i]["profile_link"] = "./blog.php?sel=view_user&id_blog=".$row["id_blog"];

			$blog_comment[$i]["login"] = stripslashes($row["login"]);
			$blog_comment[$i]["creation_date"] = $row["creation_date"];
			$blog_comment[$i]["creation_time"] = date("H:i", $row["creation_date_stamp"]);
			if ($blog_comment[$i]["id_user"] == $user[ AUTH_ID_USER ] || $blog_comment[$i]["author_id"] == $user[ AUTH_ID_USER ]) {
				$blog_comment[$i]["can_edit"] = 1;
				$blog_comment[$i]["delete_link"] = "blog.php?sel=delete_comment&id_post=".$id_post."&id_comment=".$blog_comment[$i]["id"];
			}
			$blog_icon_path = $row["blog_icon_path"]?$row["blog_icon_path"]:$default_photos[$row["gender"]];
			if($blog_icon_path && file_exists($config["site_path"].$settings["icons_folder"]."/".$blog_icon_path)){
				$blog_comment[$i]["comment_icon"] = $config["site_root"].$settings["icons_folder"]."/".$blog_icon_path;
			} else {
				$blog_comment[$i]["comment_icon"] = $config["server"].$config["site_root"].$settings["photos_folder"]."/".$settings["photos_default"];
			}
			$blog_comment[$i]["sub_level"] = $level[$blog_comment[$i]["id"]];
			if ($blog_comment[$i]["sub_level"]>$config_index["blog_max_sub_level"]) {
				$blog_comment[$i]["sub_level"] = $config_index["blog_max_sub_level"];
			}
			$blog_comment[$i]["title"] = stripslashes($row["title"]);
			$blog_comment[$i]["body"] = stripslashes($row["body"]);
			$blog_comment[$i]["deleted"] = $row["deleted"];
			$rs->MoveNext();
			$i++;
		}
		return $blog_comment;
	} else {
		return '';
	}
}

function DeleteBlogComment()
{
	global $lang, $dbconn, $user;
	$id_post = intval($_GET["id_post"]);
	$id_comment = intval($_GET["id_comment"]);
	if (($id_comment<1) || ($id_post<1)) {
		BlogUserMain(); return;
	}
	$strSQL = " SELECT id_profile FROM ".BLOG_POST_TABLE." WHERE id='".$id_post."' ";
	$rs = $dbconn->Execute($strSQL);
	$id_blog = $rs->fields[0];
	if (IsUserBlog($id_blog) == 0) {//if not blog author try to delete comment
		$strSQL = " SELECT id_user FROM ".BLOG_COMMENTS_TABLE." WHERE id='".$id_blog."' ";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0] != $user[ AUTH_ID_USER ]) {
			AddCommentForm('', '','view');
			return;
		}
	}
	$dbconn->Execute("UPDATE ".BLOG_COMMENTS_TABLE." SET title='', body='".addslashes($lang["blog"]["comment_deleted"])."', deleted='1' WHERE id='".$id_comment."'  ");
	AddCommentForm('comment_deleted','','view');
	return;
}

function AllBlogs()
{
	global $lang, $config, $smarty, $dbconn, $user;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$file_name = "blog.php";

	$settings = GetSiteSettings(array('show_users_connection_str','show_users_comments','show_users_group_str'));

	$form["show_users_connection_str"] = $settings["show_users_connection_str"];
	$form["show_users_comments"] = $settings["show_users_comments"];
	$form["show_users_group_str"] = $settings["show_users_group_str"];

	$strSQL = "SELECT DISTINCT id, category_name FROM ".BLOG_CATEGORIES_TABLE." GROUP BY id ORDER BY category_name";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>0) {
		$i = 0;
		$cat_arr = array();
		while (!$rs->EOF) {
			$cat_arr[$i]["id"] = $rs->fields[0];
			$cat_arr[$i]["name"] = stripslashes($rs->fields[1]);

			$strSQL = " SELECT COUNT(*) FROM ".BLOG_PROFILE_TABLE." WHERE id_category='".$cat_arr[$i]["id"]."' ";
			$rs_count = $dbconn->Execute($strSQL);
			$cat_arr[$i]["blogs_count"] = intval($rs_count->fields[0]);
			if ($cat_arr[$i]["blogs_count"]>0){
				$cat_arr[$i]["link"] = $file_name."?sel=list_category&amp;id_category=".$cat_arr[$i]["id"];
			}
			$rs->MoveNext();
			$i++;
		}

		$num_cat = sizeof($cat_arr)%2;
		if ($num_cat==0){
			$smarty->assign("half_num_cat",intval(sizeof($cat_arr)/2));
		} else {
			$smarty->assign("half_num_cat",intval(sizeof($cat_arr)/2)+1);
		}
		$smarty->assign("blog_categories", $cat_arr);
	}

	$form["blog_page"] = 4;

	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["homepage"]);
	$smarty->assign("header_s", $lang["blog"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/blog_categories_table.tpl");
	exit;
}

function ListCategory()
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$settings = GetSiteSettings(array('icon_male_default','icon_female_default','icons_folder','show_users_connection_str','show_users_comments','show_users_group_str','photos_folder', 'photos_default', 'icon_max_width', 'icon_max_height', 'thumb_max_width', 'thumb_max_height' ));

	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];


	$page = isset($_REQUEST["page"]) && intval($_REQUEST["page"])>0 ? intval($_REQUEST["page"]) : 1;
	$form['id_category'] = isset($_REQUEST["id_category"]) && intval($_REQUEST["id_category"])>0 ? intval($_REQUEST["id_category"]) : 1;

	$strSQL = "SELECT id, category_name FROM ".BLOG_CATEGORIES_TABLE." WHERE id='".$form['id_category']."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] > 0) {
		$form['category_name'] = stripcslashes($rs->fields[1]);
	}

	$rs = $dbconn->Execute("SELECT DISTINCT id FROM ".BLOG_PROFILE_TABLE." WHERE id_category='".$form['id_category']."' AND active='1' ");

	if ($rs->fields[0]>0) {
		$i = 0; $users_id = array();
		while(!$rs->EOF){
			$users_id[$i] = $rs->fields[0];
			$rs->MoveNext();
			$i++;
		}
	}
	if (is_array($users_id) && sizeof($users_id)>0) {
		$profile_str = implode(',', $users_id);
	}

	if ($profile_str!='') {
		$strSQL = " SELECT DISTINCT id FROM ".BLOG_POST_TABLE." WHERE id_profile IN (".$profile_str.") ";
		$rs = $dbconn->Execute($strSQL);
		$num_records = $rs->RowCount();
		$lim_min = ($page-1)*$config_index["blog_post_numpage"];
		$lim_max = $config_index["blog_post_numpage"];
		$limit_str = " LIMIT ".$lim_min.", ".$lim_max;
		if ($num_records>0) {
			$strSQL = " SELECT DISTINCT	bpt.id, bpt.id_profile, bpt.title, bpt.body,
											DATE_FORMAT(bpt.creation_date, '".$config["date_format"]."') as creation_date,
											UNIX_TIMESTAMP(bpt.creation_date) as creation_date_stamp,
											bpt.is_hidden, bpt.can_comment,
											bprt.id_user, ut.login, ut.icon_path as author_icon, ut.gender
							FROM ".BLOG_POST_TABLE." bpt
							LEFT JOIN ".BLOG_PROFILE_TABLE." bprt ON bprt.id=bpt.id_profile
							LEFT JOIN ".USERS_TABLE." ut ON bprt.id_user = ut.id
							WHERE bpt.id_profile IN (".$profile_str.") AND bprt.active='1'
							GROUP BY bpt.id ORDER BY bpt.creation_date DESC ".$limit_str;
			$rs = $dbconn->Execute($strSQL);
			if ($rs->fields[0]>0) {
				$i = 0;
				$blog_post = array();
				while(!$rs->EOF){
					$row = $rs->GetRowAssoc(false);
					$blog_post[$i]["id"] = $row["id"];
					$blog_post[$i]["id_profile"] = $row["id_profile"];
					$blog_post[$i]["id_user"] = $row["id_user"];
					$blog_post[$i]["login"] = stripslashes($row["login"]);
					$blog_post[$i]["profile_link"] = "./viewprofile.php?id=".$blog_post[$i]["id_user"];
					$blog_post[$i]["is_hidden"] = $row["is_hidden"];
					$blog_post[$i]["can_comment"] = $row["can_comment"];
					$blog_post[$i]["creation_date"] = $row["creation_date"];
					$blog_post[$i]["creation_time"] = date("H:i", $row["creation_date_stamp"]);

					$strSQL = " SELECT COUNT(*) FROM ".BLOG_COMMENTS_TABLE." WHERE id_post='".$blog_post[$i]["id"]."' ";
					$rs_c = $dbconn->Execute($strSQL);
					if ($rs_c->fields[0]>0) {
						$blog_post[$i]["comments_count"] = $rs_c->fields[0];
					}
					$blog_post[$i]["comments_link"] = "blog.php?sel=view_comments&id_post=".$blog_post[$i]["id"];
					if ($blog_post[$i]["is_hidden"] == 1 && $blog_post[$i]["id_profile"] != $user[ AUTH_ID_USER ]) {
						$blog_post[$i]["show"] = 0;
					} else {
						$blog_post[$i]["show"] = 1;
					}
					if ($blog_post[$i]["can_comment"] == 1 ) {
						$blog_post[$i]["add_comments_link"] = "blog.php?sel=add_comment&id_post=".$blog_post[$i]["id"];
					}
					$blog_post[$i]["title"] = stripslashes($row["title"]);
					$blog_post[$i]["body"] = stripslashes($row["body"]);

					$blog_icon_path = $row["author_icon"]?$row["author_icon"]:$default_photos[$row["gender"]];
					if($blog_icon_path && file_exists($config["site_path"].$settings["icons_folder"]."/".$blog_icon_path)){
						$blog_post[$i]["comment_icon"] = $config["site_root"].$settings["icons_folder"]."/".$blog_icon_path;
					} else {
						$blog_post[$i]["comment_icon"] = $config["server"].$config["site_root"].$settings["photos_folder"]."/".$settings["photos_default"];
					}
					$rs->MoveNext();
					$i++;
				}
				$smarty->assign("blog_posts", $blog_post);
				$param = "blog.php?sel=friends&";
				$smarty->assign("links", GetLinkArray($num_records, $page, $param, $config_index["blog_post_numpage"]));
			}
		}
	} else {
		$smarty->assign("blog_posts", "empty");
	}

	$form["show_users_connection_str"] = $settings["show_users_connection_str"];
	$form["show_users_comments"] = $settings["show_users_comments"];
	$form["show_users_group_str"] = $settings["show_users_group_str"];

	$form["blog_page"] = 4;
	$form["icon_max_width"] = $settings["thumb_max_width"];
	$form["icon_max_height"] = $settings["thumb_max_height"];
	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["homepage"]);
	$smarty->assign("header_s", $lang["blog"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/blog_user_main_table.tpl");
	exit;
}

?>