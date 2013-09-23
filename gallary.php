<?php
/**
* Multimedia gallery file (list gallery categories, vote actions)
*
* @package DatingPro
* @subpackage User Mode
**/

include './include/config.php';
include './common.php';
include './include/config_index.php';
include './include/config_admin.php';
include './include/functions_auth.php';
include './include/functions_index.php';
include './include/class.images.php';
include './include/class.lang.php';

// authentication
$user = auth_index_user();

if (empty($user) || $user == 'err' || empty($user[ AUTH_ID_USER ])) {
	header('location: '.$config['site_root'].'/index.php');
	exit;
}

// check guest
// (handled by permissions)

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

if (isset($_GET['sel']) && $_GET['sel'] == 'view_upload') {
	$_SESSION['return_to_view']['type'] = 'gallary';
}

if ($_GET['clear_return_toview'] == '1' || $_SESSION['return_to_view']['type'] == 'other') {
	unset($_SESSION['return_to_view']);
}

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

// dispatcher
switch ($sel) {
	case 'category': 		CategoryMain();  break;
	case 'view_upload': 	ViewFile();	break;
	case 'begin_vote': 		BeginVote();	break;
	case 'vote': 			VotePhoto();	break;
	default: 				SearchTable();	break;
}

exit;


function SearchTable($err="")
{
	global $lang, $config, $smarty, $dbconn, $user;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$settings = GetSiteSettings(array(	'photos_folder', 'photos_default', 'thumb_max_width', 'thumb_max_height',
	'icon_adult_default','icons_folder', 'use_ffmpeg', 'video_folder','video_default'));

	$upload_type = (isset($_REQUEST["upload_type"]) && $_REQUEST["upload_type"]!="" && ( $_REQUEST["upload_type"]=="f" || $_REQUEST["upload_type"]=="v" )) ? $_REQUEST["upload_type"] : "f";

	switch ($upload_type) {
		case 'f':
			$upload_folder = $settings['photos_folder'];
			break;
		case 'v':
			$upload_folder = $settings['video_folder'];
			break;
		default:
			$upload_folder = $settings['photos_folder'];
			break;
	}
	// !!count category parameter!!
	$form['top_limit'] = 6;
	$form['new_limit'] = 6;

	$form['upload_type'] = $upload_type;

	$form['settings'] = $settings;

	// multi-language tables
	$multi_lang = new MultiLang();
	
	//get categories and ($form['top_count']*one photo) for each category
	$strSQL = " SELECT DISTINCT gc.id, rl.".$multi_lang->DefaultFieldName()." as name, COUNT(uu.id) as uploads_count
				FROM ".GALLERY_CATEGORIES_TABLE." gc
				LEFT JOIN ".REFERENCE_LANG_TABLE." rl ON rl.id_reference=gc.id AND rl.table_key='".$multi_lang->TableKey(GALLERY_CATEGORIES_TABLE)."'
				LEFT JOIN ".USER_UPLOAD_TABLE." uu ON (uu.id_gallery=gc.id AND uu.is_gallary='1' AND uu.status='1' AND uu.allow='1' AND uu.upload_type='".$upload_type."')
				GROUP BY gc.id ORDER BY uploads_count DESC ";
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$categories = array();
	$form['top_count'] = $form['top_limit'];
	while(!$rs->EOF){
		$categories[$i]["id"] = $rs->fields[0];
		$categories[$i]["name"] = stripslashes($rs->fields[1]);
		$categories[$i]["photo_count"] = $rs->fields[2];
		if ($i < $form['top_limit']) {
			if ($categories[$i]["photo_count"] == 0) {
				$form['top_count']--;
			}

			$strSQL = "SELECT uu.upload_path, uu.id, uu.is_adult, ut.login, uu.id_user
			 			FROM ".USER_UPLOAD_TABLE." uu
			 			LEFT JOIN ".USERS_TABLE." ut ON ut.id=uu.id_user
						WHERE uu.id_gallery='".$categories[$i]["id"]."' AND uu.status='1' AND uu.is_gallary='1' AND uu.status='1' AND uu.allow='1' AND uu.upload_type='".$upload_type."'
						ORDER BY RAND() LIMIT 1 ";
			$rs_upload = $dbconn->Execute($strSQL);

			$categories[$i]["is_adult"] = $rs_upload->fields[2];
			$categories[$i]['author_login'] = stripslashes($rs_upload->fields[3]);
			$categories[$i]['author_id'] = $rs_upload->fields[4];
			if ($categories[$i]["is_adult"] == 1) {
				$categories[$i]["adult_allow"] = CheckAdultAllow();
				$categories[$i]["icon"] = $config['server'].$config['site_root'].$settings["icons_folder"]."/".$settings["icon_adult_default"];
				$categories[$i]["icon_id"] = $rs_upload->fields[1];

				if ($categories[$i]["adult_allow"]) {
					$categories[$i]["link_type"] = 1;
					$categories[$i]["view_link"] = "viewprofile.php?sel=agreement&gallary=1&upload_type=".$upload_type."&id_file=".$categories[$i]["icon_id"];
				} else {
					$categories[$i]["link_type"] = 2;
					$categories[$i]["view_link"] = "viewprofile.php?sel=agreement&gallary=1&upload_type=".$upload_type."&id_file=".$categories[$i]["icon_id"]."&allow_no=1";
				}
			} else {
				$categories[$i]["icon_id"] = $rs_upload->fields[1];
				$categories[$i]["view_link"] = "gallary.php?sel=view_upload&upload_type=".$upload_type."&id=".$categories[$i]["icon_id"];
				if ($upload_type == 'f') {
					if (isset($rs_upload->fields[0]) && $rs_upload->fields[0] != '' && file_exists($config['site_path'].$upload_folder."/thumb_".$rs_upload->fields[0])) {
						$categories[$i]["icon"] = $config['server'].$config['site_root'].$upload_folder."/thumb_".$rs_upload->fields[0];
					}
				} elseif ($upload_type == 'v') {
					if ($settings["use_ffmpeg"] == 1) {
						$new_file_name_arr = explode(".", $rs_upload->fields[0]);
						if (file_exists($config['site_path'].$upload_folder."/".$new_file_name_arr[0]."1.jpg")) {
							$categories[$i]["icon"] = $config['server'].$config['site_root'].$upload_folder."/".$new_file_name_arr[0]."1.jpg";
						} else {
							$categories[$i]["icon"] = $config['server'].$config['site_root'].$upload_folder."/".$settings['video_default'];
						}
					} else {
						$categories[$i]["icon"] = $config['server'].$config['site_root'].$upload_folder."/".$settings['video_default'];
					}
				}
				$categories[$i]["link_type"] = 3;
			}
		}
		$rs->MoveNext();
		$i++;
	}
	$form['categories_count'] = sizeof($categories);
	$form['categories_without_top'] = $form['categories_count'] - $form['top_count'];

	$num_cat = $form['categories_without_top']%3;

	if ($num_cat == 0){
		$form['categories_1_limit'] = $form['categories_2_limit'] = intval($form['categories_without_top']/3);
	} elseif ($num_cat == 1) {
		$form['categories_1_limit'] = intval($form['categories_without_top']/3)+1;
		$form['categories_2_limit'] = intval($form['categories_without_top']/3);
	} else {
		$form['categories_1_limit'] = $form['categories_2_limit'] = intval($form['categories_without_top']/3)+1;
	}
	$form['categories_2_start'] = $form['categories_1_limit'] + $form['top_count'];
	$form['categories_3_start'] = $form['categories_2_start'] + $form['categories_2_limit'];

	//get last photos
	$strSQL = " SELECT DISTINCT uu.id, uu.upload_path, gc.id, rl.".$multi_lang->DefaultFieldName()." as name, ut.login, uu.id_user, uu.is_adult
				FROM ".USER_UPLOAD_TABLE." uu
				LEFT JOIN ".GALLERY_CATEGORIES_TABLE." gc on gc.id=uu.id_gallery
				LEFT JOIN ".USERS_TABLE." ut ON ut.id=uu.id_user
				LEFT JOIN ".REFERENCE_LANG_TABLE." rl ON gc.id=rl.id_reference AND rl.table_key='".$multi_lang->TableKey(GALLERY_CATEGORIES_TABLE)."'
				WHERE uu.is_gallary='1' AND uu.status='1' AND uu.allow='1' AND uu.upload_type='".$upload_type."' AND gc.id=uu.id_gallery
				GROUP BY uu.id ORDER BY uu.id DESC LIMIT 0,".$form['new_limit'];
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$last_uploads = array();
	while(!$rs->EOF) {
		$last_uploads[$i]['id'] = $rs->fields[0];
		$last_uploads[$i]["is_adult"] = $rs->fields[6];
		if ($last_uploads[$i]["is_adult"] == 1) {
			$last_uploads[$i]["adult_allow"] = CheckAdultAllow();
			$last_uploads[$i]["upload_path"] = $config['server'].$config['site_root'].$settings["icons_folder"]."/".$settings["icon_adult_default"];
			if ($last_uploads[$i]["adult_allow"]) {
				$last_uploads[$i]["link_type"] = 1;
				$last_uploads[$i]["view_link"] = "viewprofile.php?sel=agreement&gallary=1&upload_type=".$upload_type."&id_file=".$last_uploads[$i]['id'];
			} else {
				$last_uploads[$i]["link_type"] = 2;
				$last_uploads[$i]["view_link"] = "viewprofile.php?sel=agreement&gallary=1&upload_type=".$upload_type."&id_file=".$last_uploads[$i]['id']."&allow_no=1";
			}
		} else {
			$last_uploads[$i]["link_type"] = 3;
			$last_uploads[$i]["view_link"] = "gallary.php?sel=view_upload&upload_type=".$upload_type."&id=".$last_uploads[$i]['id'];


			if ($upload_type == 'f') {
				if (isset($rs->fields[1]) && $rs->fields[1] != '' && file_exists($config['site_path'].$upload_folder."/thumb_".$rs->fields[1])) {
					$last_uploads[$i]['upload_path'] = $config['server'].$config['site_root'].$upload_folder."/thumb_".$rs->fields[1];
				}
			} elseif ($upload_type == 'v') {
				if ($settings["use_ffmpeg"] == 1) {
					$new_file_name_arr = explode(".", $rs->fields[1]);
					if (file_exists($config['site_path'].$upload_folder."/".$new_file_name_arr[0]."1.jpg")) {
						$last_uploads[$i]["upload_path"] = $config['server'].$config['site_root'].$upload_folder."/".$new_file_name_arr[0]."1.jpg";
					} else {
						$last_uploads[$i]["upload_path"] = $config['server'].$config['site_root'].$upload_folder."/".$settings['video_default'];
					}
				} else {
					$last_uploads[$i]["upload_path"] = $config['server'].$config['site_root'].$upload_folder."/".$settings['video_default'];
				}
			}
		}
		$last_uploads[$i]['category_id'] = $rs->fields[2];
		$last_uploads[$i]['category_name'] = stripslashes($rs->fields[3]);
		$last_uploads[$i]['author_login'] = stripslashes($rs->fields[4]);
		$last_uploads[$i]['author_id'] = $rs->fields[5];
		$rs->MoveNext();
		$i++;
	}
	
	if(sizeof($categories) > 0)
	{
		$form['top_percent'] = intval(100/sizeof($categories));
	}
	if(sizeof($last_uploads) > 0)
	{
		$form['new_percent'] = intval(100/sizeof($last_uploads));
	}

	$smarty->assign("categories", $categories);
	$smarty->assign("last_uploads", $last_uploads);

	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["homepage"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/gallary_table.tpl");
	exit;
}

function CategoryMain()
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$file_name = "gallary.php";
	
	$settings = GetSiteSettings(array('photos_folder', 'photos_default', 'thumb_max_width', 'thumb_max_height','icon_adult_default','icons_folder',
	'use_ffmpeg', 'video_folder','video_default'));

	$upload_type = (isset($_REQUEST["upload_type"]) && $_REQUEST["upload_type"]!="" && ( $_REQUEST["upload_type"]=="f" || $_REQUEST["upload_type"]=="v" )) ? $_REQUEST["upload_type"] : "f";

	switch ($upload_type) {
		case 'f':
			$upload_folder = $settings['photos_folder'];
			break;
		case 'v':
			$upload_folder = $settings['video_folder'];
			break;
		default:
			$upload_folder = $settings['photos_folder'];
			break;
	}

	$form['upload_type'] = $upload_type;

	$id_category = isset($_REQUEST['id_category']) ? intval($_REQUEST['id_category']) : 0;
	
	if ($id_category < 1) {
		SearchTable(); exit;
	} else {
		// multi-language tables
		$multi_lang = new MultiLang();

		$strSQL = " SELECT g.id, rl.".$multi_lang->DefaultFieldName()." as name
					FROM ".GALLERY_CATEGORIES_TABLE." g
					LEFT JOIN ".REFERENCE_LANG_TABLE." rl ON rl.id_reference=g.id AND rl.table_key='".$multi_lang->TableKey(GALLERY_CATEGORIES_TABLE)."'
					WHERE g.id='".$id_category."' ";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0] < 1) {
			SearchTable(); exit;
		} else {
			$category['id'] = $rs->fields[0];
			$category['name'] = $rs->fields[1];

			$page = isset($_REQUEST["page"])?$_REQUEST["page"]:"";
			if( (strval($page) == "") || (strval($page) == "0")){ $page = 1;}

			$form['sorter'] = (isset($_REQUEST['sorter']) && intval($_REQUEST['sorter'])>0) ? intval($_REQUEST['sorter']) : 1;

			switch ($form['sorter']) {
				case 1:
					$sorter_str = ' uu.gallery_rate DESC ';
					break;
				case 2:
					$sorter_str = ' uu.id DESC ';
					break;
				default:
					$sorter_str = ' uu.gallery_rate DESC ';
					break;
			}
			$strSQL = " SELECT COUNT(id) FROM ".USER_UPLOAD_TABLE."
						WHERE upload_type='".$upload_type."' AND allow='1' AND status='1' AND is_gallary='1' AND id_gallery='".$id_category."' ";
			$rs = $dbconn->Execute($strSQL);
			$num_records = $rs->fields[0];

			$lim_min = ($page-1)*$config_index["search_gallery_numpage"];
			$lim_max = $config_index["search_gallery_numpage"];
			$limit_str = " LIMIT ".$lim_min.", ".$lim_max;
			if ($num_records > 0) {
				$strSQL = " SELECT DISTINCT uu.id, uu.gallery_rate, uu.upload_path, uu.id_user, ut.login, uu.is_adult
							FROM ".USER_UPLOAD_TABLE." uu
							LEFT JOIN ".USERS_TABLE." ut ON ut.id=uu.id_user
							WHERE uu.upload_type='".$upload_type."' AND uu.allow='1' AND uu.status='1' AND uu.is_gallary='1' AND uu.id_gallery='".$id_category."'
							GROUP BY uu.id
							ORDER BY ".$sorter_str."
							".$limit_str;
				$rs = $dbconn->Execute($strSQL);
				$i = 0;
				$_arr = array();
				while (!$rs->EOF){
					$_arr[$i]['id'] = $rs->fields[0];
					$_arr[$i]['place'] = (($page-1)*$config_index["search_gallery_numpage"] + $i + 1);
					$_arr[$i]['rate'] = round($rs->fields[1],2);

					$_arr[$i]["is_adult"] = $rs->fields[5];

					if ($_arr[$i]["is_adult"] == 1) {
						$_arr[$i]["adult_allow"] = CheckAdultAllow();
						$_arr[$i]["upload_path"] = $config['server'].$config['site_root'].$settings["icons_folder"]."/".$settings["icon_adult_default"];
						if ($_arr[$i]["adult_allow"]) {
							$_arr[$i]["link_type"] = 1;
							$_arr[$i]["view_link"] = "viewprofile.php?sel=agreement&gallary=1&upload_type=".$upload_type."&id_file=".$_arr[$i]['id'];
						} else {
							$_arr[$i]["link_type"] = 2;
							$_arr[$i]["view_link"] = "viewprofile.php?sel=agreement&gallary=1&upload_type=".$upload_type."&id_file=".$_arr[$i]['id']."&allow_no=1";
						}
					} else {
						if ($upload_type == 'f') {
							if (isset($rs->fields[2]) && $rs->fields[2] != '' && file_exists($config['site_path'].$upload_folder."/thumb_".$rs->fields[2])) {
								$_arr[$i]["upload_path"] = $config['server'].$config['site_root'].$upload_folder."/thumb_".$rs->fields[2];
							}
						} elseif ($upload_type == 'v') {
							if ($settings["use_ffmpeg"] == 1) {
								$new_file_name_arr = explode(".", $rs->fields[2]);
								if (file_exists($config['site_path'].$upload_folder."/".$new_file_name_arr[0]."1.jpg")) {
									$_arr[$i]["upload_path"] = $config['server'].$config['site_root'].$upload_folder."/".$new_file_name_arr[0]."1.jpg";
								} else {
									$_arr[$i]["upload_path"] = $config['server'].$config['site_root'].$upload_folder."/".$settings['video_default'];
								}
							} else {
								$_arr[$i]["upload_path"] = $config['server'].$config['site_root'].$upload_folder."/".$settings['video_default'];
							}
						}
						$_arr[$i]["link_type"] = 3;
						$_arr[$i]["view_link"] = "gallary.php?sel=view_upload&upload_type=".$upload_type."&id=".$_arr[$i]['id'];
					}
					$_arr[$i]['author_id'] = $rs->fields[3];
					$_arr[$i]['author_login'] = stripslashes($rs->fields[4]);
					$rs->MoveNext();
					$i++;
				}
				$param = $file_name."?sel=category&id_category=".$id_category."&sorter=".$form['sorter']."&upload_type=".$upload_type."&";
				$smarty->assign("links", GetLinkArray($num_records, $page, $param, $config_index["search_gallery_numpage"]));
				$smarty->assign("uploads", $_arr);
				if ($upload_type == 'f') {
					$strSQL = " SELECT DISTINCT id FROM ".USER_UPLOAD_TABLE."
								WHERE upload_type='f' AND allow='1' AND status='1' AND is_gallary='1' AND id_gallery='".$id_category."' AND id_user!='".$user[ AUTH_ID_USER ]."'
								GROUP BY id ORDER BY id DESC ";
					$rs = $dbconn->Execute($strSQL);
					if ($rs->fields[0]>0) {
						$i = 0;
						$_arr = array();
						while (!$rs->EOF){
							$strSQL = "SELECT id FROM ".GALLERY_RATING_TABLE." WHERE id_upload='".$rs->fields[0]."' AND upload_type='f' AND id_user='".$user[ AUTH_ID_USER ]."' ";
							$rs_check = $dbconn->Execute($strSQL);
							if ($rs_check->fields[0]<1) {
								$_arr[$i] = $rs->fields[0];
								$i++;
							}
							$rs->MoveNext();
						}
						if (sizeof($_arr) > 0) {
							$form["show_rate_button"] = 1;
						} else {
							$form["show_rate_button"] = 0;
						}
					} else {
						$form["show_rate_button"] = 0;
					}
				} else {
					$form["show_rate_button"] = 0;
				}

			} else {
				$smarty->assign("uploads", 'empty');
			}
		}
	}
	$smarty->assign("form", $form);
	$smarty->assign("category", $category);
	$smarty->assign("header", $lang["homepage"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/gallary_category_list_table.tpl");
}

function BeginVote()
{
	global $lang, $config, $smarty, $dbconn, $user;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$form['upload_type'] = 'f';
	
	// not used
	## $settings = GetSiteSettings(array('photos_folder', 'photos_default', 'thumb_max_width', 'thumb_max_height'));

	$id_category = isset($_REQUEST['id_category']) ? intval($_REQUEST['id_category']) : 0;
	if ($id_category < 1) {
		SearchTable(); exit;
	} else {
		// multi-language tables
		$multi_lang = new MultiLang();
		
		$strSQL = " SELECT g.id, rl.".$multi_lang->DefaultFieldName()." as name
					FROM ".GALLERY_CATEGORIES_TABLE." g
					LEFT JOIN ".REFERENCE_LANG_TABLE." rl ON rl.id_reference=g.id AND rl.table_key='".$multi_lang->TableKey(GALLERY_CATEGORIES_TABLE)."'
					WHERE g.id='".$id_category."' ";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0] < 1) {
			SearchTable(); exit;
		} else {
			$strSQL = " SELECT DISTINCT id, upload_path FROM ".USER_UPLOAD_TABLE."
						WHERE upload_type='f' AND allow='1' AND status='1' AND is_gallary='1' AND id_gallery='".$id_category."' AND id_user!='".$user[ AUTH_ID_USER ]."'
						GROUP BY id ORDER BY id DESC ";
			$rs = $dbconn->Execute($strSQL);
			if ($rs->fields[0]>0) {
				$i = 0;
				$_arr = array();
				while (!$rs->EOF){
					$strSQL = "SELECT id FROM ".GALLERY_RATING_TABLE." WHERE id_upload='".$rs->fields[0]."' AND upload_type='f' AND id_user='".$user[ AUTH_ID_USER ]."' ";
					$rs_check = $dbconn->Execute($strSQL);
					if ($rs_check->fields[0]<1) {
						$_arr[$i] = $rs->fields[0];
						$i++;
					}
					$rs->MoveNext();
				}
				$_SESSION['vote_arr'][$id_category."_f"] = $_arr;
				$upload = GetOnePhotoById($_SESSION['vote_arr'][$id_category."_f"][0]);
				$smarty->assign("upload", $upload);
			} else {
				$smarty->assign("upload", 'empty');
			}

			$_arr = range(1,10);
			$smarty->assign("vote_arr", $_arr);
			$form['id_category'] = $id_category;

			$form['vote_icon_1_path'] = $config['server'].$config['site_root'].$config["index_theme_path"]."/images/vote_icon_1.gif";
			$form['vote_icon_0_path'] = $config['server'].$config['site_root'].$config["index_theme_path"]."/images/vote_icon_0.gif";
			$smarty->assign("header", $lang["homepage"]);
			$smarty->assign("form", $form);
			$smarty->display(TrimSlash($config["index_theme_path"])."/gallary_voting_table.tpl");
		}
	}
}

function ViewFile()
{
	global $config, $smarty, $dbconn, $user;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();

	$id = intval($_GET["id"]);

	$upload_type = (isset($_REQUEST["upload_type"]) && $_REQUEST["upload_type"]!="" && ( $_REQUEST["upload_type"]=="f" || $_REQUEST["upload_type"]=="v" )) ? $_REQUEST["upload_type"] : "f";

	$form["upload_type"] = $upload_type;
	$settings = GetSiteSettings(array('photos_folder','use_ffmpeg', 'video_folder'));

	$strSQL = "	SELECT a.id, b.login, a.upload_path , a.user_comment, a.id_gallery
				FROM ".USER_UPLOAD_TABLE." a
				LEFT JOIN ".USERS_TABLE." b ON b.id=a.id_user
				WHERE a.id='".$id."' AND a.upload_type='".$upload_type."' AND a.allow='1' AND a.status='1' ";
	$rs = $dbconn->Execute($strSQL);

	if ($rs->fields[0]>0) {
		$form['id_category'] = $rs->fields[4];
		$form["id_upload"] = $rs->fields[0];

		if ($upload_type == 'v') {
			if ($settings["use_ffmpeg"] == 1) {
				$flv_name = explode('.', $rs->fields[2]);
				if (file_exists($config["site_path"].$settings['video_folder']."/".$flv_name[0].".flv")) {
					$form['is_flv'] = 1;
					$smarty->assign("is_flv", 1);
					$form["image_path"] = $config["server"].$config["site_root"].$settings['video_folder']."/".$flv_name[0]."1.jpg";
					$form["file_path"] = $config["server"].$config["site_root"].$settings['video_folder']."/".$flv_name[0].".flv";
				} else {
					$form["file_path"] = $config["server"].$config["site_root"].$settings['video_folder']."/".$rs->fields[2];
				}
			} else {
				$form["file_path"] = $config["server"].$config["site_root"].$settings['video_folder']."/".$rs->fields[2];

			}
		} else {
			$form["image_path"] = $config['server'].$config['site_root'].$settings['photos_folder']."/".$rs->fields[2];
		}

		$strSQL = " SELECT uu.id_user, gr.estimation
						FROM ".USER_UPLOAD_TABLE." uu
						LEFT JOIN ".GALLERY_RATING_TABLE." gr ON gr.id_upload=uu.id AND gr.id_user='".$user[ AUTH_ID_USER ]."'
						WHERE uu.id='".$form["id_upload"]."'
						";
		$rs = $dbconn->Execute($strSQL);
		if ( $rs->fields[0] == $user[ AUTH_ID_USER ] || $rs->fields[1]>0 ) {
			$form["no_rating"] = 1;
		}
		$_arr = range(1,10);
		$smarty->assign("vote_arr", $_arr);

		$form['vote_icon_1_path'] = $config['server'].$config['site_root'].$config["index_theme_path"]."/images/vote_icon_1.gif";
		$form['vote_icon_0_path'] = $config['server'].$config['site_root'].$config["index_theme_path"]."/images/vote_icon_0.gif";

		$form["comment"] = utf8_substr($rs->fields[3],0, 50);
		$smarty->assign("form", $form);
		$smarty->display(TrimSlash($config["index_theme_path"])."/gallary_view_table.tpl");
	} else {
		echo "<script language='JavaScript' type='text/javascript'>window.close();</script>";
	}
	exit;
}

function GetOnePhotoById($id_upload)
{
	global $config, $dbconn;
	
	$settings = GetSiteSettings(array('photos_folder'));
	$strSQL = " SELECT uu.id, uu.upload_path, uu.user_comment, uu.gallery_rate, ut.login, ut.id
				FROM ".USER_UPLOAD_TABLE." uu
				LEFT JOIN ".USERS_TABLE." ut ON ut.id=uu.id_user
				WHERE uu.id='".$id_upload."'
	";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>0) {
		$upload['id_upload'] = $rs->fields[0];
		$upload['upload_path'] = $config['server'].$config['site_root'].$settings['photos_folder']."/".$rs->fields[1];
		$upload['comment'] = stripslashes($rs->fields[2]);
		$upload['gallery_rate'] = round($rs->fields[3],2);
		$upload['login'] = $rs->fields[4];
		$upload['user_id'] = $rs->fields[5];
	} else {
		$upload = 'empty';
	}
	return $upload;
}

function VotePhoto()
{
	global $lang, $config, $smarty, $dbconn, $user;

	$id_category = intval($_REQUEST['id_category']);
	$vote = intval($_REQUEST['vote']);
	$id_upload = intval($_REQUEST['id_upload']);
	$upload_type = (isset($_REQUEST["upload_type"]) && $_REQUEST["upload_type"]!="" && ( $_REQUEST["upload_type"]=="f" || $_REQUEST["upload_type"]=="v" )) ? $_REQUEST["upload_type"] : "f";

	$strSQL = " INSERT INTO ".GALLERY_RATING_TABLE." (id_upload, id_user, estimation, voting_date, upload_type )
				VALUES ('".$id_upload."', '".$user[ AUTH_ID_USER ]."', '".$vote."', now(), '".$upload_type."') ";
	$rs = $dbconn->Execute($strSQL);

	$strSQL = " SELECT AVG(estimation) FROM ".GALLERY_RATING_TABLE."
				WHERE id_upload='".$id_upload."' ";
	$rs = $dbconn->Execute($strSQL);

	$dbconn->Execute(" UPDATE ".USER_UPLOAD_TABLE." SET gallery_rate ='".$rs->fields[0]."' WHERE id='".$id_upload."' ");

	if ($upload_type == 'f') {
		array_shift($_SESSION['vote_arr'][$id_category."_f"]);
		$upload = GetOnePhotoById($_SESSION['vote_arr'][$id_category."_f"][0]);
		$_arr = range(1,10);
		$smarty->assign("vote_arr", $_arr);
		$form['id_category'] = $id_category;
		$form['vote_icon_1_path'] = $config['server'].$config['site_root'].$config["index_theme_path"]."/images/vote_icon_1.gif";
		$form['vote_icon_0_path'] = $config['server'].$config['site_root'].$config["index_theme_path"]."/images/vote_icon_0.gif";
		$smarty->assign('form', $form);
		if ($upload !='empty') {
			$smarty->assign('upload', $upload);
			print_r($smarty->fetch(TrimSlash($config["index_theme_path"]).'/gallary_vote_photo_section.tpl'));
		} else {
			echo $lang["gallary"]["no_photos"];
		}
	} else {
		echo $lang["gallary"]["voice_submitted"];
	}

}

function CheckAdultAllow()
{
	global $dbconn, $user;
	$strSQL = " SELECT m.id FROM ".GROUP_MODULE_TABLE." gm, ".MODULES_TABLE." m
				LEFT JOIN ".USER_GROUP_TABLE." ug ON ug.id_user='".$user[ AUTH_ID_USER ]."'
				WHERE m.name='adult_content' AND gm.id_group=ug.id_group AND gm.id_module=m.id ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] > 0) {
		return 1;
	} else {
		return 0;
	}
}

?>