<?php
/**
* View user profile page (general, description, self-portrait, desired partner criteria, multimedia album, rating, tags...)
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
include './include/class.percent.php';
include './include/class.images.php';
include './include/class.phpmailer.php';
include './include/functions_mail.php';

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

//VP if ($_GET['clear_return_toview'] == '1') unset($_SESSION['return_to_view']);

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');
$act = isset($_GET['act']) ? $_GET['act'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// send email alert
if (!$user[ AUTH_GUEST ] && $id != $user[ AUTH_ID_USER ])
{
	$rs = $dbconn->Execute('SELECT count_of_visits FROM '.PROFILE_VISIT_TABLE.' WHERE id_user = ? AND id_visiter = ?', array($id, $user[ AUTH_ID_USER ]));
	if ($rs->fields[0] > 0) {
		$rs = $dbconn->Execute(
			'SELECT IF((UNIX_TIMESTAMP(NOW()) - MAX(UNIX_TIMESTAMP(visit_date)) - 60*60) > 0, 1, 0) AS r
			   FROM '.PROFILE_VISIT_TABLE.'
			  WHERE id_user = ? AND id_visiter = ?',
			array($id, $user[ AUTH_ID_USER ]));
		if ($rs->fields[0] == 1) {
			// SendNotice($id);
			SendNotification($id, $user[ AUTH_ID_USER ], 'PROFILE_VIEWED'); // to, from, type
		}
	} else {
		// SendNotice($id);
		SendNotification($id, $user[ AUTH_ID_USER ], 'PROFILE_VIEWED'); // to, from, type
	}
}

// limited access for trial users and inactive users
// addconnections gets a special treatment in function AddToConnections() in functions_index.php
if ($sel == 'vote' || $sel == 'comment' || $sel == 'delcomment')
{
	if ($user[ AUTH_IS_TRIAL ]) {
		ListProfile(1, '', $lang['error']['access_denied_trial']);
		exit;
	}
	if ($user[ AUTH_IS_INACTIVE ]) {
		$_GET['par'] = 'send';
		ListProfile(1, '', $lang['error']['access_denied_inactive']);
		exit;
	}
}

// dispatcher
switch ($sel) {
	case '1': ListProfile(1, $act); break; // personal info
	case '2': ListProfile(2, $act); break; // my fact sheet
	case '3': ListProfile(3, $act); break; // desired partner criteria
	case '4': ListProfile(4, $act); break; // multimedia
	case '5': ListProfile(5, $act); break; // rating
	case '6': ListProfile(6, $act); break; // tags
	
	case 'print':
		PrintProfile();
	break;
	case 'upload_view':
		UploadView();
	break;
	case 'map':
		ViewLocation();
	break;
	case 'vote':
		RateProfile((int)$_GET['id'], (int)$_POST['r']);
		if (REDIRECT_AFTER_ACTION) {
			header('Location: viewprofile.php?sel=5&id='.$id);
			exit;
		} else {
			ListProfile(5);
		}
	break;
	case 'comment':
		$msg = PostComment((int)$_GET['id'], $_POST['message']);
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $msg;
			header('Location: viewprofile.php?sel=5&id='.$id);
			exit;
		} else {
			ListProfile(5, '', $msg);
		}
	break;
	case 'delcomment':
		DeleteComment((int)$_GET['cid']);
		if (REDIRECT_AFTER_ACTION) {
			header('Location: viewprofile.php?sel=5&id='.$id);
			exit;
		} else {
			ListProfile(5);
		}
	break;
	case 'addhotlist':
		$res = AddToHotList();
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			header('Location: viewprofile.php?sel=1&id='.$id);
			exit;
		} else {
			ListProfile(1, '', $res['err']);
		}
	break;
	case 'addconnection':
		$res = AddToConnections();
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			header('Location: viewprofile.php?sel=1&id='.$id);
			exit;
		} else {
			ListProfile(1, '', $res['err']);
		}
	break;
	case 'addblacklist':
		$res = AddToBlackList();
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			header('Location: viewprofile.php?sel=1&id='.$id);
			exit;
		} else {
			ListProfile(1, '', $res['err']);
		}
	break;
	case 'kiss':
		$res = SendKiss();
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			header('Location: viewprofile.php?sel=1&id='.$id);
			exit;
		} else {
			ListProfile(1, '', $res['err']);
		}
	break;
	case 'ajax_image':
		UploadView('ajax');
	break;
	case 'addtag':
		AddTag();
	break;
	case 'deltag':
		DeleteTag();
	break;
	case 'agreement':
		Agreement();
	break;
	default:
		ListProfile(1);
	break;
}

exit;


function ListProfile($page=1, $act='', $err='', $sub='', $action='', $data='')
{
	global $lang, $config, $smarty, $dbconn, $user, $charset;
	
	if (isset($_SESSION['err'])) {
		$err = $_SESSION['err'];
		unset($_SESSION['err']);
	}
	
	$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
	$search_type = isset($_GET['search_type']) ? $_GET['search_type'] : '';
	
	if (isset($_GET['login'])) {
		$id = $dbconn->GetOne('SELECT id FROM '.USERS_TABLE.' WHERE login = ?', array($_GET['login']));
		if (empty($id)) {
			exit;
		}
	}
	
	if ($act == '') {
		Banners(GetRightModulePath(__FILE__));
		IndexHomePage();
		GetActiveUserInfo($user);
		GeneralTable();
		if (SearchLinkTable() == 'guest_err') {
			$form['guest_user_err'] = $lang['err']['guest_user_must_reg'];
			AlertPage(GetRightModulePath(__FILE__));
			return;
		}
	}
	
	switch ($page)
	{
		case 1:
			$user_data = PersonalTable();
			AnnonceTable();
			
			//VM Fetching all the data for popup
			DescriptionTable();
			MyPersonalityTable();
			MyPortraitTable();
			MyInterestsTable();
			
			MyCriteriaTable();
			HisInterestsTable();
		break;
		case 2:
			DescriptionTable();
			MyPersonalityTable();
			MyPortraitTable();
			MyInterestsTable();
		break;
		case 3:
			MyCriteriaTable();
			HisInterestsTable();
		break;
		case 4:
			// overrride with querystring only if not explicitely set before
			if ($sub == '' && isset($_GET['sub'])) {
				$sub = (int)$_GET['sub'];
			}
			if ($action == '' && isset($_GET['action'])) {
				$action = (int)$_GET['action'];
			}
			UploadTable($sub, $action, $data);
		break;
		case 5:
			VotingTable($id, $search_type);
			$smarty->assign('smiles', $config['smiles']);
		break;
		case 6:
			TagsTable();
		break;
	}
	
	if (isset($_GET['view']) && ($_GET['view'] == 'video')) {
		$form['view'] = $_GET['view'];
	}
	
	$form['page'] = (int)$page;
	$form['err'] = $err;
	$form['guest_user'] = $user[ AUTH_GUEST ];
	
	$suffix  = 'id='.$id.'&amp;search_type='.$search_type;
	
	$menu = array();
	
	if (isset($config['use_pilot_module_blog']) && ($config['use_pilot_module_blog'] == 1)) {
		$rs = $dbconn->Execute('SELECT id FROM '.BLOG_PROFILE_TABLE.' WHERE id_user = ?', array($id));
		if ($rs->fields[0] > 0) {
			$menu['blog'] = './blog.php?sel=view_user&amp;id_blog='.$rs->fields[0];
		}
	}
	
	$form['action'] = 'viewprofile.php?'.$suffix;
	$form['suffix'] = $suffix;
	
	$smarty->assign('menu', $menu);
	$smarty->assign('form', $form);
	$smarty->assign('header', $lang['profile']);
	$smarty->assign('button', $lang['button']);
	$smarty->assign('alt', $lang['alt']);
	
	if (isset($config['use_pilot_module_organizer']) && $config['use_pilot_module_organizer'] == 1)
	{
		$strSQL =
			'SELECT id, home_area_color, menu_back_1_color, menu_back_2_color,
					menu_back_3_color, menu_back_4_color, menu_font_1_color, menu_font_2_color,
					menu_font_3_color, menu_font_4_color, link_color, header_color, content_color,
					search_color, shoutbox_color, main_text_color,
					big_bg_color, bg_picture_path
			   FROM '.ORG_USER_LAYOUTS_TABLE.'
			  WHERE id_user = ?';
		$rs = $dbconn->Execute($strSQL, array($id));
		
		if ($rs->fields[0] > 0)
		{
			$row = $rs->GetRowAssoc(false);
			if ($row['home_area_color'] != '') {
				$color['home_menu'] = $row['home_area_color'];
			}
			if ($row['shoutbox_color'] != '') {
				$color['shoutbox_color'] = $row['shoutbox_color'];
				$_SESSION['shoutbox_color_'.$id] = $row['shoutbox_color'];
			}
			if ($row['menu_back_1_color'] != '') {
				$color['menu_block_1'] = $row['menu_back_1_color'];
			}
			if ($row['menu_back_2_color'] != '') {
				$color['menu_block_2'] = $row['menu_back_2_color'];
			}
			if ($row['menu_back_3_color'] != '') {
				$color['menu_block_3'] = $row['menu_back_3_color'];
			}
			if ($row['menu_back_4_color'] != '') {
				$color['menu_block_4'] = $row['menu_back_4_color'];
			}
			if ($row['menu_font_1_color'] != '') {
				$color['menu_link_1'] = $row['menu_font_1_color'];
			}
			if ($row['menu_font_2_color'] != '') {
				$color['menu_link_2'] = $row['menu_font_2_color'];
			}
			if ($row['menu_font_3_color'] != '') {
				$color['menu_link_3'] = $row['menu_font_3_color'];
			}
			if ($row['menu_font_4_color'] != '') {
				$color['menu_link_4'] = $row['menu_font_4_color'];
			}
			if ($row['link_color'] != '') {
				$color['link'] = $row['link_color'];
			}
			if ($row['header_color'] != '') {
				$color['header'] = $row['header_color'];
			}
			if ($row['content_color'] != '') {
				$color['content'] = $row['content_color'];
			}
			if ($row['search_color'] != '') {
				$color['home_search'] = $row['search_color'];
			}
			if ($row['big_bg_color'] != '') {
				$color['bg_color'] = $row['big_bg_color'];
			}
			if ($row['main_text_color'] != '') {
				$color['main_text_color'] = $row['main_text_color'];
			}
			if ($row['bg_picture_path'] != '') {
				$settings = GetSiteSettings(array('photos_folder'));
				$color['bg_picture_path'] = $config['site_root'].$settings['photos_folder'].'/'.$row['bg_picture_path'];
			}
			$smarty->append('css_color', $color, true);
			$smarty->assign('customised', '1');
			$smarty->assign('id_customed', $id);
		}
		else
		{
			unset($_SESSION['shoutbox_color_'.$id]);
		}
	}
	else
	{
		unset($_SESSION['shoutbox_color_'.$id]);
	}
	
	if ($act == '')
	{
		if (isset($user_data))
		{
			$location_arr = array();
			if (isset($user_data['city']) && $user_data['city'] != '') {
				$location_arr[] = $user_data['city'];
			}
			if (isset($user_data['region']) && $user_data['region'] != '') {
				$location_arr[] = $user_data['region'];
			}
			if (isset($user_data['country']) && $user_data['country'] != '') {
				$location_arr[] = $user_data['country'];
			}
			if (!empty($location_arr)) {
				$location = ' - '.implode(', ', $location_arr);
			} else {
				$location = '';
			}
			$lang['main_title'] = $user_data['fname'].', '.$user_data['age'].', '.$user_data['gender'].$location;
			$smarty->assign('lang', $lang);
		}
		
		$smarty->display(TrimSlash($config['index_theme_path']).'/viewprofile_table.tpl');
	}
	else
	{
		header('Content-type: text/html; charset='.$charset);
		$smarty->assign('template_root', $config['index_theme_path']);
		$smarty->display(TrimSlash($config['index_theme_path']).'/viewprofile_view.tpl');
	}
	
	exit;
}

function PrintProfile()
{
	global $lang, $config, $smarty, $dbconn;
	
	IndexHomePage('', true);
	
	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	
	if (isset($_GET['login']))
	{
		$rs = $dbconn->Execute('SELECT id FROM '.USERS_TABLE.' WHERE login = ?', array($_GET['login']));
		if ($rs->fields[0]) {
			$id = $rs->fields[0];
		} else {
			exit;
		}
	}

	$settings['icons_folder'] = GetSiteSettings('icons_folder');

	$rs = $dbconn->Execute('SELECT fname, icon_path FROM '.USERS_TABLE.' WHERE id = ?', array($id));
	$fname = $rs->fields[0];
	if ($rs->fields[1] && file_exists($config['site_path'].$settings['icons_folder'].'/'.$rs->fields[1])) {
		$form['icon_path'] = $config['server'].$config['site_root'].$settings['icons_folder'].'/'.$rs->fields[1];
	}
	
	$_GET['id'] = $id;
	
	PersonalTable();
	DescriptionTable();
	AnnonceTable();
	MyPersonalityTable();
	MyPortraitTable();
	MyInterestsTable();
	MyCriteriaTable();
	HisInterestsTable();
	
	$form['page'] = 'print';
	$lang['main_title'] = $fname;
	
	$smarty->assign('form', $form);
	$smarty->assign('header', $lang['profile']);
	$smarty->assign('button', $lang['button']);
	$smarty->assign('alt', $lang['alt']);
	$smarty->assign('lang', $lang);
	
	$smarty->assign('template_root', $config['index_theme_path']);
	$smarty->display(TrimSlash($config['index_theme_path']).'/viewprofile_print.tpl');
	
	exit;
}

// ---------------------------- Table Functions -------------------------------- //
function SearchLinkTable()
{
	global $smarty, $dbconn, $user;
	
	$file_name = 'viewprofile.php';
	
	$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
	$search_type = isset($_GET['search_type']) ? $_GET['search_type'] : '';
	
	if (isset($_GET['login'])) {
		$login = FormFilter($_GET['login']);
		$id = $dbconn->GetOne('SELECT id FROM '.USERS_TABLE.' WHERE login = ?', array($login));
		if (empty($id)) {
			exit;
		}
	}
	
	if ($user[ AUTH_GUEST ]) {
		$guest_profile_limit = GetSiteSettings('guest_profile_limit');
		$used_lim = isset($_SESSION['guest_profile_limit']) ? count($_SESSION['guest_profile_limit']) : 0;
		if ($used_lim < $guest_profile_limit || $used_lim == $guest_profile_limit && $_SESSION['guest_profile_limit'][$id] == 1) {
			$_SESSION['guest_profile_limit'][$id] = 1;
		}
		if ($used_lim > $guest_profile_limit || $used_lim == $guest_profile_limit && $_SESSION['guest_profile_limit'][$id] == 0) {
			return 'guest_err';
		}
	}
	
	// insert entry in profile_visit table
	$rs = $dbconn->Execute('SELECT count_of_visits FROM '.PROFILE_VISIT_TABLE.' WHERE id_user = ? AND id_visiter = ?', array($id, $user[ AUTH_ID_USER ]));
	if ($rs->fields[0] > 0) {
		$strSQL = 'UPDATE '.PROFILE_VISIT_TABLE.' SET count_of_visits = count_of_visits + 1, visit_date = NOW() WHERE id_user = ? AND id_visiter = ?';
		$dbconn->Execute($strSQL, array($id, $user[ AUTH_ID_USER ]));
	} else {
		$strSQL= 'INSERT INTO '.PROFILE_VISIT_TABLE.' SET id_user = ?, id_visiter = ?, visit_date = NOW(), count_of_visits = 1';
		$dbconn->Execute($strSQL, array($id, $user[ AUTH_ID_USER ]));
	}
	
	// next/previous/back links
	if ($search_type) {
		$id_arr = isset($_SESSION['id_arr']) ? $_SESSION['id_arr'] : array();
		if (count($id_arr) > 0) {
			foreach ($id_arr as $key => $value) {
				if ($value == $id) {
					$curent_key = $key;
					$prev_key = $key - 1;
					$next_key = $key + 1;
					break;
				}
			}
			if (isset($curent_key)) {
				$nav['back_link'] = getBacklink($search_type);
			}
			if (isset($prev_key) && isset($id_arr[$prev_key]) && $id_arr[$prev_key] != 0) {
				$nav['prev_link'] = $file_name.'?id='.$id_arr[$prev_key].'&amp;search_type='.$search_type;
			}
			if (isset($next_key) && isset($id_arr[$next_key]) && $id_arr[$next_key] != 0) {
				$nav['next_link'] = $file_name.'?id='.$id_arr[$next_key].'&amp;search_type='.$search_type;
			}
			if (isset($nav)) {
				$smarty->assign('search_link', $nav);
			}
		}
	}
	
	return '';
}

function GeneralTable()
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
	$search_type = isset($_GET['search_type']) ? $_GET['search_type'] : '';
	
	if ($id <= 0) {
		header('Location: homepage.php');
		exit;
	}
	
	if (isset($_GET['login'])) {
		$login = FormFilter($_GET['login']);
		$rs = $dbconn->Execute('SELECT id FROM '.USERS_TABLE.' WHERE login = ?', array($login));
		if ($rs->fields[0]) {
			$id = $rs->fields[0];
		} else {
			exit;
		}
	}
	
	$suffix = '&amp;id='.$id;
	if ($search_type) {
		$suffix .= '&amp;search_type='.$search_type;
	}
	
	// settings
	$settings = GetSiteSettings(array('icons_folder', 'photos_folder', 'video_folder', 'min_age_limit', 'max_age_limit',
		'photos_default', 'show_users_connection_str', 'show_users_group_str',
		'use_kiss_types', 'use_pilot_module_webrecorder', 'big_thumb_max_width', 'big_thumb_max_height', 'use_friend_types', 'use_horoscope_feature'));
	
	if ($settings['use_pilot_module_webrecorder']) {
		$rs = $dbconn->Execute('SELECT COUNT(*) FROM '.UP_RECORDER_RECORDS_TABLE.' WHERE id_user = ? AND status = "approved"', array($id));
		if ($rs->fields[0] > 0) {
			$data['webrecorder_player'] = $config['site_root'].'/webrecorder/wr.php?type=player&amp;user='.$id;
			$rs = $dbconn->Execute('SELECT value FROM '.UP_RECORDER_SETTINGS_TABLE.' WHERE name = "videosize"');
			$settings['videosize'] = $rs->fields[0];
			$data['webrecorder_width'] = 180;
			$data['webrecorder_height'] = 280;
			if ($settings['videosize'] > 1) {
				$data['webrecorder_width'] = ($settings['videosize'] == 2) ? 340 : 660;
				$data['webrecorder_height'] = ($settings['videosize'] == 2) ? 400 : 640;
			}
		}
	}
	
	// icon
	$rs = $dbconn->Execute('SELECT big_icon_path, gender, fname, sname, login, headline, icon_path_temp FROM '.USERS_TABLE.' WHERE id = ?', array($id));
	$row = $rs->GetRowAssoc(false);
	
	if (($row['icon_path_temp']) == '')
	{
		$icon_path = $row['big_icon_path'];
		if ($icon_path && file_exists($config['site_path'].$settings['icons_folder'].'/'.$icon_path))
		{
			$data['icon_path'] = $config['site_root'].$settings['icons_folder'].'/'.$icon_path;
			//VP big image also added for profile slide show
			$full_icon_path = substr($icon_path, 10, 100);
			$data['full_icon_path'] = $config['site_root'].$settings['icons_folder'].'/'.$full_icon_path;
			$icon_count = 1;
		}
		else
		{
			$icon_count = 0;
		}
	}
	
	// is user a friend? get allow array
	$rs = $dbconn->Execute('SELECT COUNT(id) FROM '.HOTLIST_TABLE.' WHERE id_user = ? AND id_friend = ?', array($id, $id_user));
	if ($rs->fields[0] > 0) {
		// select uploads for all type permissions(guest, user, friend)
		$allow = ' AND allow IN (1, 2, 3)';
	} elseif (!$user[ AUTH_GUEST ] && $id_user > 0) {
		// user registered
		$allow = ' AND allow IN (1, 2)';
	} else {
		$allow = ' AND allow = 1';
	}
	
	$rs = $dbconn->Execute(
		'SELECT id, upload_path, allow, upload_type, user_comment
		   FROM '.USER_UPLOAD_TABLE.'
		  WHERE id_user = ? AND status = "1" '.$allow.'
	   ORDER BY upload_type',
		array($id));
	
	$i = 0;
	$db_upload = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$db_upload[$row['upload_type']][$i]['id'] = $row['id'];
		$db_upload[$row['upload_type']][$i]['file_path'] = $row['upload_path'];
		$db_upload[$row['upload_type']][$i]['user_comment'] = stripslashes($row['user_comment']);
		$rs->MoveNext();
		$i++;
	}
	
	// photo slider
	$i = 0;
	if (isset($db_upload['f']))
	{
		$images_obj = new Images($dbconn);
		
		foreach ($db_upload['f'] as $key => $photo)
		{
			if (file_exists($config['site_path'].$settings['photos_folder'].'/thumb_'.$photo['file_path']) && strlen($photo['file_path']))
			{
				$data['thumb_photo_path'][$i]['path'] = $config['site_root'].$settings['photos_folder'].'/thumb_'.$photo['file_path'];
				//VP Big images added.
				$data['big_photo_path'][$i]['path'] = $config['site_root'].$settings['photos_folder'].'/'.$photo['file_path'];
			}
			if (file_exists($config['site_path'].$settings['photos_folder'].'/'.$photo['file_path']) && !file_exists($config['site_path'].$settings['photos_folder'].'/thumb_'.$photo['file_path']) && strlen($photo['file_path']))
			{
				$data['thumb_photo_path'][$i]['path'] = $config['site_root'].$settings['photos_folder'].'/'.$photo['file_path'];
				$data['thumb_photo_path'][$i]['sizes'] = $images_obj->GetResizeParametrsStr($config['site_path'].$settings['photos_folder'].'/'.$photo['file_path']);
			}
			if (file_exists($config['site_path'].$settings['photos_folder'].'/'.$photo['file_path']) && strlen($photo['file_path']))
			{
				$i++;
			}
		}
	}
	
	// video slider
	$i = 0;
	if (isset($db_upload['v']))
	{
		foreach ($db_upload['v'] as $key => $row)
		{
			if (strlen($row['file_path']) && file_exists($config['site_path'].$settings['video_folder'].'/'.$row['file_path']))
			{
				$data['video'][$i]['id']			= $row['id'];
				#$data['video'][$i]['file_name']	= $row['file_path'];
				$data['video'][$i]['user_comment']	= $row['user_comment'];
				
				$file_name_arr = explode('.', $row['file_path']);
				
				// video thumb
				if (file_exists($config['site_path'].$settings['video_folder'].'/'.$file_name_arr[0].'1.jpg')) {
					$data['video'][$i]['thumb_path'] = $config['server'].$config['site_root'].$settings['video_folder'].'/'.$file_name_arr[0].'1.jpg';
				} else {
					$data['video'][$i]['thumb_path'] = $config['server'].$config['site_root'].$settings['video_folder'].'/'.$settings['video_default'];
				}
				
				// video file
				if (file_exists($config['site_path'].$settings['video_folder'].'/'.$file_name_arr[0].'-out.mp4')) {
					$data['video'][$i]['file_path'] = $config['server'].$config['site_root'].$settings['video_folder'].'/'.$file_name_arr[0].'-out.mp4';
				} else {
					$data['video'][$i]['file_path'] = $config['server'].$config['site_root'].$settings['video_folder'].'/'.$row['file_path'];
				}
				
				// view link
				$data['video'][$i]['view_link'] = './viewprofile.php?sel=upload_view&amp;id_file='.$row['id'].'&amp;type_upload=v';
				
				$i++;
			}
		}
	}
	
	$data['photo_count'] = (isset($db_upload['f']) ? count($db_upload['f']) : 0);
	$data['photo_count']++;
	$data['viewf_link'] = 'viewprofile.php?sel=4'.$suffix;
	
	$data['audio_count'] = isset($db_upload['a']) ? count($db_upload['a']) : 0;
	$data['viewa_link'] = 'viewprofile.php?sel=4'.$suffix;
	
	$data['video_count'] = isset($db_upload['v']) ? count($db_upload['v']) : 0;
	
	$id_album = isset($_GET['id_album']) ? intval($_GET['id_album']) : 0;
	$id_v = isset($_GET['id_v']) ? intval($_GET['id_v']) : 0;
	$data['viewv_link'] = 'viewprofile.php?sel=4&amp;sub=10&amp;action=4&amp;id_album='.$id_album.'&amp;id_v='.$id_v.$suffix;
	
	$data['login'] = $user[ AUTH_LOGIN ];
	
	$strSQL =
		'SELECT a.login, a.status, a.chk_background, a.chk_marital_status, a.chk_work_history, a.chk_interview_photo,
				b.name AS country, c.name AS city, r.name AS region, 
				a.date_birthday AS birthday, DATE_FORMAT(a.date_last_seen,"'.$config['date_format'].'") AS date_last_login, 
				a.gender, h.id_friend, z.id_enemy, e.id_user AS session, a.phone
		   FROM '.USERS_TABLE.' a
	  LEFT JOIN '.COUNTRY_SPR_TABLE.' b ON b.id = a.id_country
	  LEFT JOIN '.CITY_SPR_TABLE.' c ON c.id = a.id_city
	  LEFT JOIN '.ACTIVE_SESSIONS_TABLE.' e ON a.id = e.id_user
	  LEFT JOIN '.HOTLIST_TABLE.' h ON a.id = h.id_friend AND h.id_user = ?
	  LEFT JOIN '.BLACKLIST_TABLE.' z ON a.id = z.id_enemy AND z.id_user = ?
	  LEFT JOIN '.REGION_SPR_TABLE.' r ON r.id = a.id_region
		  WHERE a.id = ?';
	
	$rs = $dbconn->Execute($strSQL, array($id_user, $id_user, $id));
	$row = $rs->GetRowAssoc(false);
	
	$data['id']					= $id;
	$data['login']				= $row['login'];
	$data['check']				= $row['status'];
	$data['chk_background']		= $row['chk_background'];
	$data['chk_marital_status']	= $row['chk_marital_status'];
	$data['chk_work_history']	= $row['chk_work_history'];
	$data['chk_interview_photo'] = $row['chk_interview_photo'];
	$data['phone']				= $row['phone'];
	$data['country']			= stripslashes($row['country']);
	$data['region']				= stripslashes($row['region']);
	$data['city']				= stripslashes($row['city']);
	$data['age']				= AgeFromBDate($row['birthday']);
	$data['last_login']			= $row['date_last_login'];
	$data['gender']				= $row['gender'];
	$data['gender_text']		= $lang['gender'][$row['gender']];
	
	//VP online privacy settings check added
	//$data['status'] = $row['session'] ? $lang['status']['on'] : $lang['status']['off'];
	if ($row['session']) {
		$count = $dbconn->getOne('SELECT COUNT(id) FROM '.USER_PRIVACY_SETTINGS.' WHERE hide_online = "1" AND id_user = ?', array($data['id']));
		$data['status'] = empty($count) ? $lang['status']['on'] : $lang['status']['off'];
	} else {
		$data['status'] = $lang['status']['off'];	
	}
	
	// get groups
	$sub_strSQL = 'SELECT a.name, b.id_group FROM '.USER_GROUP_TABLE.' b LEFT JOIN '.GROUPS_TABLE.' a ON a.id = b.id_group WHERE b.id_user = ?';
	$sub_rs = $dbconn->Execute($sub_strSQL, array($id));
	$groups_arr = array();
	$groups_arr_id = array();
	
	while (!$sub_rs->EOF) {
		array_push($groups_arr, $sub_rs->fields[0]);
		array_push($groups_arr_id, $sub_rs->fields[1]);
		$sub_rs->MoveNext();
	}
	
	if (!empty($groups_arr) && is_array($groups_arr))
	{
		$data['group'] = implode(',', $groups_arr);
		//SH Removing (On Hold) status
		$data['group'] = str_replace('(On Hold)', '', $data['group']);
		// removing Lady and Guy from user group
		$data['group'] = str_replace(' Lady', '', $data['group']);
		$data['group'] = str_replace(' Guy', '', $data['group']);
		$data['group_id'] = implode(',', $groups_arr_id);
		
		if ($data['group_id'] == MM_PLATINUM_GUY_APPLIED_ID || $data['group_id'] == MM_PLATINUM_LADY_APPLIED_ID)
		{
			$data['is_applied'] = true;
		}
		elseif (in_array($data['group_id'], array(MM_PLATINUM_LADY_FIRST_INS_ID, MM_PLATINUM_LADY_SECOND_INS_ID))) 
		{
			// Ralf to Narendra:
			// don't think we need to check here if user is really verified unless there is a good reason
			// but we need to hide installment info to other users
			$data['group'] = 'Platinum Lady';
			
			#if (!$user[ AUTH_IS_PLATINUM ]) {
			#	$data['group'] = $lang['users']['list_inact_plat_lady'];;
			#}
		}
	}
	
	// hotlisted and blacklisted
	$data['hotlisted'] = empty($row['id_friend']) ? 0 : 1;
	$data['blacklisted'] = empty($row['id_enemy']) ? 0 : 1;
	
	// check connected status
	$data['connected_status'] = getConnectedStatus($id_user, $id);
	
	// links
	$data['email_link'] = './mailbox.php?sel=fs'.$suffix;
	$data['sendfriend_link'] = './send_friend.php?sel=send&amp;id_user='.$id;
	$data['kiss_link'] = $settings['use_kiss_types'] ? './send_kiss.php?sel=send&amp;id_user='.$id : './viewprofile.php?sel=kiss'.$suffix;
	//SH //$data['gift_link'] = './giftshop.php?sel=users_add&amp;id_user='.$id;
	$data['ecard_link'] = './ecards.php?id_user_to='.$id.'&amp;fixuser=Y';
	
	// add to hotlist link
	if ($data['hotlisted'] == 0 && $data['blacklisted'] == 0) {
		if ($settings['use_friend_types']) {
			$data['add_hotlist_link'] = './hotlist.php?sel=addform&amp;id='.$id;
		} else {
			$data['add_hotlist_link'] = './viewprofile.php?sel=addhotlist'.$suffix;
		}
	}
	
	// add to blacklist link
	if ($data['hotlisted'] == 0 && $data['connected_status'] != CS_CONNECTED && $data['blacklisted'] == 0) {
		$data['add_blacklist_link'] = './viewprofile.php?sel=addblacklist'.$suffix;
	}
	
	// add to connections link
	if ($data['connected_status'] == CS_NOTHING && $data['blacklisted'] == 0) {
		if ($settings['use_friend_types']) {
			$data['add_connection_link'] = './connections.php?sel=addform&amp;id='.$id;
		} else {
			$data['add_connection_link'] = './viewprofile.php?sel=addconnection'.$suffix;
		}
	}
	
	$data['rss_link'] = './rss.php?user_name='.$data['login'].'&amp;language_code='.$config['default_lang'];
	$data['rss_title'] = str_replace('[user]', $data['login'], $lang['rss']['chanel_title']);
	
	$data['show_users_connection_str'] = $settings['show_users_connection_str'];
	$data['show_users_group_str'] = $settings['show_users_group_str'];
	$data['use_kiss_types'] = $settings['use_kiss_types'];
	
	$data['use_friend_types'] = $settings['use_friend_types'];
	$data['show_horoscope'] = $settings['use_horoscope_feature'];
	
	// horoscope
	if ($settings['use_horoscope_feature'])
	{
		$rs = $dbconn->Execute('SELECT DATE_FORMAT(date_birthday, "%m"), DATE_FORMAT(date_birthday, "%d") FROM '.USERS_TABLE.' WHERE id = ?', array($id));
		
		$birth_month = $rs->fields[0];
		$birth_day = $rs->fields[1];
		
		$rs = $dbconn->Execute('SELECT name FROM '.HOROSCOPE_SIGNS_TABLE.' WHERE DATE_FORMAT(date_start, "%m") = '.$birth_month.' AND DATE_FORMAT(date_start, "%d") <= '.$birth_day);
		
		if ($rs->fields[0]) {
			$sign = $rs->fields[0];
		} else {
			$rs = $dbconn->Execute(
				'SELECT name
				   FROM '.HOROSCOPE_SIGNS_TABLE.'
				  WHERE DATE_FORMAT(date_end, "%m") = '.$birth_month.' AND DATE_FORMAT(date_end, "%d") >= '.$birth_day);
			$sign = $rs->fields[0];
		}
		
		$data['horoscope_sign'] = $lang['horoscope'][$sign]['name'];
		$data['horoscope_link'] = './horoscope.php?sel=view&amp;sign='.$sign;
	}
	
	// google map
	if ($data['city']) {
		$rs = $dbconn->Execute(
			'SELECT b.lat, b.lon
			   FROM '.USERS_TABLE.' a
		 INNER JOIN '.CITY_SPR_TABLE.' b ON a.id_city = b.id
			  WHERE a.id = ?',
		array($id));
		$data['view_on_map'] = './viewprofile.php?sel=map&amp;lat='.$rs->fields[0].'&amp;lon='.$rs->fields[1];
	}
	
	// voip
	if ($config['voipcall_feature']) {
		$data['call_link'] = './voip_call.php?sel=rate&amp;id_user='.$id;
	}
	
	$smarty->assign('settings', $settings);
	$smarty->assign('data', $data);
	$smarty->assign('link_item', $data);
	return;
}

function PersonalTable()
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	
	if (isset($_GET['login'])) {
		$login = FormFilter($_GET['login']);
		$rs = $dbconn->Execute('SELECT id FROM '.USERS_TABLE.' WHERE login = ?', array($login));
		if ($rs->fields[0]) {
			$id = $rs->fields[0];
		} else {
			exit;
		}
	}
	
	$data = array();
	
	// profile completion
	$profile_percent = new Percent($user[ AUTH_ID_USER ]);
	$data['complete'] = $profile_percent->GetSectionPercentForUser(1, $id);
	
	// multi-lang tables
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	$strSQL =
		'SELECT a.login, a.sname, a.fname, a.mm_nickname, b.name AS country, c.name AS city, r.name AS region, d.'.$field_name.' AS nation,
				e.'.$field_name.' as lang1, f.'.$field_name.' as lang2, g.'.$field_name.' as lang3,
				DATE_FORMAT(a.date_birthday,"'.$config['date_format'].'") as date_birthday, a.date_birthday as birthday,
				a.gender, a.couple, a.couple_user, h.session, hot.id_friend as hotlist, a.email, a.zipcode, a.headline,
				a.about_me, a.what_i_do, a.my_idea, a.hoping_to_find,
				up.promotion_1, up.promotion_2, up.promotion_3, up.visible_lady, up.visible_guy,
				up.vis_lady_1, up.vis_lady_2, up.vis_lady_3, up.vis_lady_4, up.vis_lady_5,
				up.vis_guy_1, up.vis_guy_2, up.vis_guy_3, up.vis_guy_4, up.vis_guy_5
		   FROM '.USERS_TABLE.' a
	  LEFT JOIN '.USER_PRIVACY_SETTINGS.' AS up ON up.id_user = a.id
	  LEFT JOIN '.COUNTRY_SPR_TABLE.' b ON b.id = a.id_country
	  LEFT JOIN '.CITY_SPR_TABLE.' c ON c.id = a.id_city
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' d ON d.id_reference = a.id_nationality AND d.table_key = "'.$multi_lang->TableKey(NATION_SPR_TABLE).'"
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' e ON e.id_reference = a.id_language_1 AND e.table_key = "'.$multi_lang->TableKey(LANGUAGE_SPR_TABLE).'"
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' f ON f.id_reference = a.id_language_2 AND f.table_key = "'.$multi_lang->TableKey(LANGUAGE_SPR_TABLE).'"
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' g ON g.id_reference = a.id_language_3 AND g.table_key = "'.$multi_lang->TableKey(LANGUAGE_SPR_TABLE).'"
	  LEFT JOIN '.ACTIVE_SESSIONS_TABLE.' h ON h.id_user = a.id
	  LEFT JOIN '.HOTLIST_TABLE.' hot ON hot.id_user = ? AND hot.id_friend = a.id
	  LEFT JOIN '.REGION_SPR_TABLE.' r ON r.id = a.id_region
		  WHERE a.id = ?';
	$rs = $dbconn->Execute($strSQL, array($id, $id));
	$row = $rs->GetRowAssoc(false);
	
	$data['id']			= $id;
	$data['login']		= $row['login'];
	$data['fname']		= stripslashes($row['fname']);
	$data['sname']		= stripslashes($row['sname']);
	$data['nick']		= stripslashes($row['mm_nickname']);
	$data['country']	= stripslashes($row['country']);
	$data['region']		= stripslashes($row['region']);
	$data['city']		= stripslashes($row['city']);
	
	$data['lives_in'] = $data['city'];
	
	if (strlen($data['region'])) {
		if (strlen($data['lives_in'])) {
			$data['lives_in'] .= ',<br>';
		}
		$data['lives_in'] .= $data['region'];
	}
	
	if (strlen($data['country'])) {
		if (strlen($data['lives_in'])) {
			$data['lives_in'] .= ',<br>';
		}
		$data['lives_in'] .= $data['country'];
	}
	
	$data['date_birthday']	= $row['date_birthday'];
	$data['age']			= AgeFromBDate($row['birthday']);
	$data['gender']			= $row['gender'];
	$data['gender_text']	= $lang['gender'][$row['gender']];
	$data['couple']			= $row['couple'];
	$data['couple_user']	= $row['couple_user'];
	
	if ($row['couple_user']) {
		$rs_couple = $dbconn->Execute('SELECT login, gender, date_birthday, couple_user FROM '.USERS_TABLE.' WHERE id = ?', array($row['couple_user']));
		$data['couple_login']		= $rs_couple->fields[0];
		$data['couple_link']		= 'viewprofile.php?id='.$row['couple_user'];
		$data['couple_gender_text']	= $lang['gender'][$rs_couple->fields[1]];
		$data['couple_age']			= AgeFromBDate($rs_couple->fields[2]);
		$data['couple_accept']		= $rs_couple->fields[3] == $id ? 1 : 0;
	}
	
	$data['nationality']		= stripslashes($row['nation']);
	$data['zipcode']			= $row['zipcode'];
	$data['headline']			= stripslashes($row['headline']);
	
	$data['language1']			= $row['lang1'];
	$data['language2']			= $row['lang2'];
	$data['language3']			= $row['lang3'];
	
	$data['languages'] = $data['language1'];
	if (strlen($data['language2'])) {
		if (strlen($data['languages'])) {
			$data['languages'] .= ', ';
		}
		$data['languages'] .= $data['language2'];
	}
	if (strlen($data['language3'])) {
		if (strlen($data['languages'])) {
			$data['languages'] .= ', ';
		}
		$data['languages'] .= $data['language3'];
	}
	
	// online status
	if ($row['session']) {
		$count = $dbconn->getOne('SELECT COUNT(id) FROM '.USER_PRIVACY_SETTINGS.' WHERE hide_online = "1" AND id_user = ?', array($data['id']));
		$data['status'] = empty($count) ? $lang['status']['on'] : $lang['status']['off'];
	} else {
		$data['status'] = $lang['status']['off'];	
	}
	
	// privacy settings
	$data['visible_lady']	= isset($row['visible_lady']) ? (int) $row['visible_lady'] : 1;
	
	$data['vis_lady_1']		= isset($row['vis_lady_1']) ? (int) $row['vis_lady_1'] : 1;
	$data['vis_lady_2']		= isset($row['vis_lady_2']) ? (int) $row['vis_lady_2'] : 1;
	$data['vis_lady_3']		= isset($row['vis_lady_3']) ? (int) $row['vis_lady_3'] : 1;
	
	$data['visible_guy']	= isset($row['visible_guy']) ? (int) $row['visible_guy'] : 1;
	
	$data['vis_guy_1']		= isset($row['vis_guy_1']) ? (int) $row['vis_guy_1'] : 1;
	$data['vis_guy_2']		= isset($row['vis_guy_2']) ? (int) $row['vis_guy_2'] : 1;
	$data['vis_guy_3']		= isset($row['vis_guy_3']) ? (int) $row['vis_guy_3'] : 1;
	$data['vis_guy_4']		= isset($row['vis_guy_4']) ? (int) $row['vis_guy_4'] : 1;
	
	$data['promotion_1']	= isset($row['promotion_1']) ? (int) $row['promotion_1'] : 0;
	$data['promotion_2']	= isset($row['promotion_2']) ? (int) $row['promotion_2'] : 0;
	$data['promotion_3']	= isset($row['promotion_3']) ? (int) $row['promotion_3'] : 0;
	
	// biography
	$data['about_me']		= stripslashes($row['about_me']);
	$data['what_i_do']		= stripslashes($row['what_i_do']);
	$data['my_idea']		= stripslashes($row['my_idea']);
	$data['hoping_to_find']	= stripslashes($row['hoping_to_find']);
	
	$str_length = 220;
	$data['about_me_short'] 		= substr($data['about_me'], 0, $str_length);
	$data['what_i_do_short']		= substr($data['what_i_do'], 0, $str_length);
	$data['my_idea_short']			= substr($data['my_idea'], 0, $str_length);
	$data['hoping_to_find_short']	= substr($data['hoping_to_find'], 0, $str_length);
	
	// looking for
	$strSQL = 'SELECT a.gender, a.couple, a.age_max, a.age_min, a.id_relationship FROM '.USER_MATCH_TABLE.' a WHERE a.id_user = ?';
	$rs = $dbconn->Execute($strSQL, array($id));
	$row = $rs->GetRowAssoc(false);
	$data['search_gender']	= $lang['gender_search'][$row['gender']];
	$data['search_couple']	= $row['couple'];
	$data['min_age']		= $row['age_min'];
	$data['max_age']		= $row['age_max'];
	
	if (strlen($row['id_relationship'])) {
		$where_str = ($row['id_relationship'] == '0') ? '' : 'WHERE a.id IN ('.$row['id_relationship'].')';
		$strSQL =
			'SELECT a.id, b.'.$field_name.' AS name
			   FROM '.RELATION_SPR_TABLE.' a
		  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.id_reference = a.id AND b.table_key = "'.$multi_lang->TableKey(RELATION_SPR_TABLE).'"
					'.$where_str.'
		   ORDER BY a.sorter';
		$rs = $dbconn->Execute($strSQL);
		$relations = array();
		while(!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$relations[] = stripslashes($row['name']);
			$rs->MoveNext();
		}
		$data['relationship'] = implode(', ', $relations);
	}

	$data['show_users_name_str'] = GetSiteSettings('show_users_name_str');
	$data['show_users_sname_str'] = GetSiteSettings('show_users_sname_str');
	$data['show_users_zipcode_str'] = GetSiteSettings('show_users_zipcode_str');
	$data['show_users_birthdate_str'] = GetSiteSettings('show_users_birthdate_str');

	$smarty->assign('data_1', $data);
	return $data;
}

function DescriptionTable()
{
	global $lang, $smarty, $dbconn;
	
	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	
	if (isset($_GET['login'])) {
		$login = FormFilter($_GET['login']);
		$rs = $dbconn->Execute('SELECT id FROM '.USERS_TABLE.' WHERE login = ?', array($login));
		if ($rs->fields[0]) {
			$id = $rs->fields[0];
		}
		else exit;
	}
	
	$info = array();
	
	// profile completion
	$profile_percent = new Percent($id);
	$info['complete'] = $profile_percent->GetSectionPercentForUser(2, $id);

	// multi-lang tables
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	$strSQL =
		'Select m.'.$field_name.' as weight, k.'.$field_name.' as height
		   from '.USERS_TABLE.' a
 	  left join '.REFERENCE_LANG_TABLE.' m on m.id_reference = a.id_weight and m.table_key = "'.$multi_lang->TableKey(WEIGHT_SPR_TABLE).'"
	  left join '.REFERENCE_LANG_TABLE.' k on k.id_reference=a.id_height and k.table_key="'.$multi_lang->TableKey(HEIGHT_SPR_TABLE).'"
		  where a.id = ?';
	$rs = $dbconn->Execute($strSQL, array($id));
	$row = $rs->GetRowAssoc(false);
	$info['weight'] = $row['weight'];
	$info['height'] = $row['height'];

	$strSQL =
		'Select b.id as id_spr, a.id_value, d.'.$field_name.' as sprname, c.'.$field_name.' as value, b.type
		   from '.DESCR_SPR_TABLE.' b
	  left join '.DESCR_SPR_USER_TABLE.' a on a.id_user="'.$id.'" and b.id=a.id_spr
	  left join '.REFERENCE_LANG_TABLE.' d on d.id_reference=b.id and d.table_key="'.$multi_lang->TableKey(DESCR_SPR_TABLE).'"
	  left join '.REFERENCE_LANG_TABLE.' c on c.id_reference=a.id_value and c.table_key="'.$multi_lang->TableKey(DESCR_SPR_VALUE_TABLE).'"
		  where length(b.name) > 0
		  order by b.sorter, value';
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$spr_id = array();
	
	while (!$rs->EOF)
	{
		$row = $rs->GetRowAssoc(false);
		$id_spr = $row['id_spr'];
		#$id_value = $row['id_value']; // not used
		if (isset($spr_id[$i]) && $id_spr != $spr_id[$i]) {
			$i++;
		}
		$spr_id[$i] = $id_spr;
		$info['info'][$i]['spr'] = $row['sprname'];
		if (!isset($info['info'][$i]['value'])) {
			$info['info'][$i]['value'] = '';
		}
		$value = ($row['type']=='2' && intval($row['id_value']) ==0 )?$lang['button']['all']:$value = $row['value'];
		if (isset($info['info'][$i]['value']) && strlen($info['info'][$i]['value'])>0 && $value != $lang['button']['all'] && $value != '') {
			$info['info'][$i]['value'] .= '<br>';
		}
		if ($value != $lang['button']['all']) {
			$info['info'][$i]['value'] .= $value;
		} else {
			$info['info'][$i]['value'] = $value;
		}
		$rs->MoveNext();
	}
	$smarty->assign('data_2', $info);
	return;
}

function AnnonceTable()
{
	global $smarty, $dbconn;
	
	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	
	if (isset($_GET['login'])) {
		$login = FormFilter($_GET['login']);
		$rs = $dbconn->Execute('SELECT id FROM '.USERS_TABLE.' WHERE login = ?', array($login));
		if ($rs->fields[0]) {
			$id = $rs->fields[0];
		} else {
			exit;
		}
	}
	
	$rs = $dbconn->Execute('SELECT comment FROM '.USERS_TABLE.' WHERE id = ?', array($id));
	$data['complete'] = (strlen($rs->fields[0]) > 0) ? 100 : 0;
	$data['annonce'] = stripslashes($rs->fields[0]);
	$smarty->assign('data_3', $data);
	return;
}

function MyPersonalityTable()
{
	global $smarty, $dbconn;
	
	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	
	$data = array();
	
	// profile completion
	$profile_percent = new Percent($id);
	$data['complete'] = $profile_percent->GetSectionPercentForUser(3, $id);
	
	// multi-lang tables
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	$strSQL =
		'SELECT b.id AS id_spr, a.id_value, d.'.$field_name.' AS sprname, c.'.$field_name.' AS value
		   FROM '.PERSON_SPR_TABLE.' b
	  LEFT JOIN '.PERSON_SPR_USER_TABLE.' a ON a.id_user = "'.$id.'" AND b.id = a.id_spr
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' d ON d.id_reference = b.id AND d.table_key = "'.$multi_lang->TableKey(PERSON_SPR_TABLE).'"
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' c ON c.id_reference = a.id_value AND c.table_key = "'.$multi_lang->TableKey(PERSON_SPR_VALUE_TABLE).'"
		  WHERE LENGTH(b.name) > 0
	   ORDER BY b.sorter, a.id_spr, value';
	$rs = $dbconn->Execute($strSQL);
	
	$i=0;
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$data['personal'][$i]['name'] = $row['sprname'];
		$data['personal'][$i]['value'] = $row['value'];
		$rs->MoveNext();
		$i++;
	}
	$smarty->assign('data_4', $data);
	return;
}

function MyPortraitTable()
{
	global $smarty, $dbconn;
	
	$id = intval($_GET['id']);
	
	$data = array();
	
	// profile completion
	$profile_percent = new Percent($id);
	$data['complete'] = $profile_percent->GetSectionPercentForUser(4, $id);
	
	// multi-lang tables
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	$strSQL =
		"Select b.id as id_spr, a.id_value, d.".$field_name." as sprname, c.".$field_name." as value
		   from ".PORTRAIT_SPR_TABLE." b
	  left join ".PORTRAIT_SPR_USER_TABLE." a on a.id_user = '".$id."' and b.id = a.id_spr
	  left join ".REFERENCE_LANG_TABLE." d on d.id_reference = b.id and d.table_key = '".$multi_lang->TableKey(PORTRAIT_SPR_TABLE)."'
	  left join ".REFERENCE_LANG_TABLE." c on c.id_reference = a.id_value and c.table_key = '".$multi_lang->TableKey(PORTRAIT_SPR_VALUE_TABLE)."'
		  where length(b.name) > 0
	   order by b.sorter, b.id, value";
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$data['portrait'][$i]['name'] = $row['sprname'];
		$data['portrait'][$i]['value'] = $row['value'];
		$rs->MoveNext();
		$i++;
	}
	$smarty->assign('data_5', $data);
	return;
}

function MyInterestsTable()
{
	global $lang, $smarty, $dbconn;
	
	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	
	$data = array();
	
	// profile completion
	$profile_percent = new Percent($id);
	$data['complete'] = $profile_percent->GetSectionPercentForUser(5, $id);
	
	// multi-lang tables
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	$strSQL =
		"Select b.id as id_spr, a.id_value, d.".$field_name." as sprname
		   from ".INTERESTS_SPR_TABLE." b
	  left join ".INTERESTS_SPR_USER_TABLE." a on a.id_user = '".$id."' and b.id = a.id_spr
	  left join ".REFERENCE_LANG_TABLE." d on d.id_reference = b.id and d.table_key = '".$multi_lang->TableKey(INTERESTS_SPR_TABLE)."'
		  where length(b.name) > 0 and a.id_value <> ''
	   order by sprname";
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$data['interests'][$i]['name'] = $row['sprname'];
		$data['interests'][$i]['value'] = $row['id_value'];
		$data['interests'][$i]['lang_value'] = $lang['interests_opt'][$row['id_value']];
		$rs->MoveNext();
		$i++;
	}
	$smarty->assign('data_6', $data);
	return;
}

function MyCriteriaTable()
{
	global $lang, $smarty, $dbconn;
	
	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	
	$data_pers = array();
	
	// profile completion
	$profile_percent = new Percent($id);
	$data_pers['complete'] = $profile_percent->GetSectionPercentForUser(6, $id);
	
	// multi-lang tables
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	$strSQL =
		"Select if(a.id_weight='0', '".$lang["button"]["not_important"]."', f.".$field_name.") as weight,
				if(a.id_height='0', '".$lang["button"]["not_important"]."', g.".$field_name.") as height,
				a.id_country, a.id_nationality, a.id_language
		   from ".USER_MATCH_TABLE." a
	  left join ".REFERENCE_LANG_TABLE." f on f.id_reference=a.id_weight and f.table_key='".$multi_lang->TableKey(WEIGHT_SPR_TABLE)."'
	  left join ".REFERENCE_LANG_TABLE." g on g.id_reference=a.id_height and g.table_key='".$multi_lang->TableKey(HEIGHT_SPR_TABLE)."'
		  where a.id_user = ?";
	$rs = $dbconn->Execute($strSQL, array($id));
	$row = $rs->GetRowAssoc(false);
	$data_pers['weight'] = $row['weight'];
	$data_pers['height'] = $row['height'];

	$country_str = $row['id_country'];
	$nation_str = $row['id_nationality'];
	$lang_str = $row['id_language'];

	if (strlen(trim($country_str)) > 0 && trim($country_str) != '0')
	{
		$strSQL_country = "Select name as country from ".COUNTRY_SPR_TABLE." where id in (".$country_str.") order by country";
		$country_arr = array();
		$rs = $dbconn->Execute($strSQL_country);
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			array_push($country_arr, $row['country']);
			$rs->MoveNext();
		}
		$data_pers['country_match'] = implode(',<br>', $country_arr);
	}
	elseif (trim($country_str) == '0')
	{
		$data_pers['country_match'] = $lang['button']['not_important'];
	}
	
	if (strlen(trim($nation_str))>0 && trim($nation_str) != '0')
	{
		$strSQL_nation =
			"Select b.".$field_name." as nation
			   from ".NATION_SPR_TABLE." a
		  left join ".REFERENCE_LANG_TABLE." b on b.id_reference = a.id and b.table_key = '".$multi_lang->TableKey(NATION_SPR_TABLE)."'
			  where a.id in (".$nation_str.")
		   order by nation";
		$nation_arr = array();
		$rs = $dbconn->Execute($strSQL_nation);
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			array_push($nation_arr, $row['nation']);
			$rs->MoveNext();
		}
		$data_pers['nationality_match'] = implode(',<br>', $nation_arr);
	}
	elseif (trim($nation_str) == '0')
	{
		$data_pers['nationality_match'] = $lang['button']['not_important'];
	}
	
	if (strlen(trim($lang_str))>0 && trim($lang_str) != '0')
	{
		$strSQL_lang =
			"Select b.".$field_name." as lang
			   from ".LANGUAGE_SPR_TABLE." a
		  left join ".REFERENCE_LANG_TABLE." b on b.id_reference = a.id and b.table_key = '".$multi_lang->TableKey(LANGUAGE_SPR_TABLE)."'
			  where a.id in (".$lang_str.")
		   order by lang";
		$lang_arr = array();
		$rs = $dbconn->Execute($strSQL_lang);
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			array_push($lang_arr, $row['lang']);
			$rs->MoveNext();
		}
		$data_pers['language'] = implode(',<br>', $lang_arr);
	}
	elseif (trim($lang_str) == '0')
	{
		$data_pers['language'] = $lang['button']['not_important'];
	}

	// personal info from db
	$strSQL =
		"Select b.id, a.id_value,d.".$field_name." as sprname, c.".$field_name." as value
		   from ".DESCR_SPR_TABLE." b
	  left join ".DESCR_SPR_MATCH_TABLE." a on a.id_user='".$id."' and b.id=a.id_spr
	  left join ".REFERENCE_LANG_TABLE." d on d.id_reference=b.id and d.table_key='".$multi_lang->TableKey(DESCR_SPR_TABLE)."'
	  left join ".REFERENCE_LANG_TABLE." c on c.id_reference=a.id_value and c.table_key='".$multi_lang->TableKey(DESCR_SPR_VALUE_TABLE)."'
		  where length(b.name) > 0
	   order by b.sorter, value";
	$rs = $dbconn->Execute($strSQL);
	$i=0;
	$spr_id = array();
	$id_value_arr[$i] = array();
	$name_value_arr[$i] = array();
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$id_spr = $row['id'];

		if (isset($spr_id[$i]) && $id_spr != $spr_id[$i])
		{
			if (is_array($id_value_arr[$i]) && in_array('0', $id_value_arr[$i]))
			{
				$data_pers['info'][$i]['value'] = $lang['button']['not_important'];
			}
			elseif (is_array($name_value_arr[$i]))
			{
				$data_pers['info'][$i]['value'] = implode(', ', $name_value_arr[$i]);
			}
			$i++;
			$id_value_arr[$i] = array();
			$name_value_arr[$i] = array();
		}
		$spr_id[$i] = $id_spr;
		$data_pers['info'][$i]['name'] = $row['sprname'];
		if (strlen(strval($row['id_value']))>0) {
			array_push($id_value_arr[$i], $row['id_value']);
			array_push($name_value_arr[$i], $row['value']);
		}
		$rs->MoveNext();
	}
	$smarty->assign('data_7', $data_pers);
	return;
}

function HisInterestsTable()
{
	global $smarty, $dbconn;

	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	
	$data = array();
	
	// profile completion
	$profile_percent = new Percent($id);
	$data['complete'] = $profile_percent->GetSectionPercentForUser(7, $id);

	// multi-lang tables
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	$strSQL =
		"Select b.id, a.id_value, d.".$field_name." as sprname
		   from ".INTERESTS_SPR_TABLE." b
	  left join ".INTERESTS_SPR_MATCH_TABLE." a on b.id=a.id_spr and a.id_user='".$id."'
	  left join ".REFERENCE_LANG_TABLE." d on d.id_reference=b.id and d.table_key='".$multi_lang->TableKey(INTERESTS_SPR_TABLE)."'
		  where length(b.name)>0 and a.id_value <> ''
	   order by sprname";
	$rs = $dbconn->Execute($strSQL);
	
	$spr_name_arr = array();
	$spr_values_arr = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$spr_name_arr[$row['id']] = $row['sprname'];
		$spr_values_arr[$row['id']][$row['id_value']] = 1;
		$rs->MoveNext();
	}
	
	$i = 0;
	
	foreach($spr_name_arr as $id_spr => $name_spr) {
		$data['interests'][$i]['name'] = $name_spr;
		$data['interests'][$i]['value_1'] = isset($spr_values_arr[$id_spr][1])?intval($spr_values_arr[$id_spr][1]):0;
		$data['interests'][$i]['value_2'] = isset($spr_values_arr[$id_spr][2])?intval($spr_values_arr[$id_spr][2]):0;
		$data['interests'][$i]['value_3'] = isset($spr_values_arr[$id_spr][3])?intval($spr_values_arr[$id_spr][3]):0;
		$i++;
	}
	$smarty->assign('data_8', $data);
	return;
}


function UploadTable($sub = '', $action = '', $data = '')
{
	global $config, $config_index, $smarty, $dbconn, $user;
	
	// displayed user
	$id = (int) $_GET['id'];
	
	#$action = isset($_GET['action']) ? $_GET['action'] : $action;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$settings = GetSiteSettings(array('photo_max_width', 'photo_max_height', 'photo_max_size', 'icon_max_width', 'icon_max_height',
		'icon_max_size', 'icons_folder', 'icons_default', 'icon_male_default', 'icon_female_default', 'photos_folder', 'photos_default',
		'audio_folder', 'audio_default','video_folder', 'video_default', 'use_embedded_audio',
		'album_icon', 'audio_album_icon','video_album_icon', 'use_ffmpeg', 'icon_adult_default'));
	#removed: 'video_max_size', 'audio_max_size', 
	
	$page = (isset($_REQUEST['page']) && (int) $_REQUEST['page'] > 0) ? (int) $_REQUEST['page'] : 1;
	
	if ($sub == '') {
		$sub = 8; // photos
	}
	
	$data['form_sub_page'] = $sub;
	
	$data['form_act'] = $action;
	
	switch ($sub)
	{
		case 9:
			// audio
			$album_page['upload_type'] = 'a';
			$album_page['album_type'] = '2';
			$album_page['_album_icon'] = $config['site_root'].$settings['audio_folder'].'/'.$settings['audio_album_icon'];
		break;
		
		case 10:
			// video
			$album_page['upload_type'] = 'v';
			$album_page['album_type'] = '3';
			$album_page['_album_icon'] = $config['site_root'].$settings['video_folder'].'/'.$settings['video_album_icon'];
		break;
		
		case 8:
		default:
			// photos
			$album_page['upload_type'] = 'f';
			$album_page['album_type'] = '1';
			$album_page['_album_icon'] = $config['site_root'].$settings['photos_folder'].'/'.$settings['album_icon'];
		break;
	}
	
	$connected_status = getConnectedStatus($id, $id_user);
	
	if ($connected_status == CS_CONNECTED) {
		// select uploads for all type permissions (guest, member, connected)
		$allow = ' AND allow IN (1, 2, 3)';
	} elseif (!$user[ AUTH_GUEST] && $id_user > 0) {
		// allow guest and member
		$allow = ' AND allow IN (1, 2)';
	} else {
		// allow guest
		$allow = ' AND allow = 1';
	}
	
	//get information about selected album type
	$album_page['items_count'] = $dbconn->GetOne(
		'SELECT COUNT(*) FROM '.USER_UPLOAD_TABLE.' WHERE id_user = ? AND upload_type = ? AND status = "1" '.$allow,
		array($id, $album_page['upload_type']));
	
	$album_page['album_count'] = $dbconn->GetOne(
		'SELECT COUNT(*) FROM '.USER_ALBUMS.' WHERE id_user = ? AND album_type = ? '.$allow,
		array($id, $album_page['upload_type']));
	
	$album_page['show_album_items'] = 0;
	
	if ($action == 4)
	{
		// browse album
		$album_page['show_album_items'] = 1;
		
		$data['id_album'] = isset($data['id_album']) ? (int) $data['id_album'] : (int) $_GET['id_album'];
		
		if ($data['id_album'] > 0){
			$rs = $dbconn->Execute(
				'SELECT title, description
				   FROM '.USER_ALBUMS.'
				  WHERE id = ? AND album_type = ?',
				array($data['id_album'], $album_page['upload_type']));
			
			$data['album_title'] = stripslashes($rs->fields[0]);
			$data['album_description'] = stripslashes($rs->fields[1]);
		}
		
		$data['num_items'] = $dbconn->GetOne(
			'SELECT COUNT(*)
			   FROM '.USER_UPLOAD_TABLE.'
			  WHERE id_user = ? AND status = "1" AND id_album = ? AND upload_type = ? '.$allow,
			array($id, $data['id_album'], $album_page['upload_type']));
		
		if ($data['num_items'] > 0)
		{
			$lim_min = ($page - 1) * $config_index['search_numpage'];
			$lim_max = (int) $config_index['search_numpage'];
			
			$rs = $dbconn->Execute(
				'SELECT id, upload_path, allow, user_comment, is_adult
				   FROM '.USER_UPLOAD_TABLE.'
				  WHERE id_user = ? AND status = "1" AND id_album = ? AND upload_type = ? '.$allow.'
			   ORDER BY id
				  LIMIT '.$lim_min.', '.$lim_max,
				array($id, $data['id_album'], $album_page['upload_type']));
			
			if ($album_page['upload_type'] == 'f')
			{
				$images_obj = new Images($dbconn);
				
				$i = 0;
				$data['photo'] = array();
				
				while (!$rs->EOF)
				{
					$row = $rs->GetRowAssoc(false);
					
					$data['photo'][$i]['id'] = $row['id'];
					$data['photo'][$i]['allow'] = $row['allow'];
					$data['photo'][$i]['is_adult'] = $row['is_adult'];
					$data['photo'][$i]['user_comment'] = stripslashes($row['user_comment']);
					
					$photo_path = $config['site_path'].$settings['photos_folder'].'/'.$row['upload_path'];
					$thumb_path = $config['site_path'].$settings['photos_folder'].'/thumb_'.$row['upload_path'];
					
					if ($data['photo'][$i]['is_adult'] == 1) {
						$data['photo'][$i]['adult_allow'] = CheckAdultAllow();
						$data['photo'][$i]['thumb_path'] = '.'.$settings['icons_folder'].'/'.$settings['icon_adult_default'];
					} else {
						if (strlen($row['upload_path']) && file_exists($thumb_path)) {
							$data['photo'][$i]['thumb_path'] = '.'.$settings['photos_folder'].'/thumb_'.$row['upload_path'];
						} elseif (strlen($row['upload_path']) && file_exists($photo_path)) {
							$data['photo'][$i]['thumb_path'] = '.'.$settings['photos_folder'].'/'.$row['upload_path'];
							$data['photo'][$i]['sizes'] = $images_obj->GetResizeParametrsStr($photo_path); // could also be $thumb_path
						} else {
							$data['photo'][$i]['thumb_path'] = '.'.$settings['photos_folder'].'/'.$settings['photos_default'];
						}
					}
					
					if (strlen($row['upload_path']) || file_exists($photo_path)) {
						if (!$user[ AUTH_GUEST]) {
							if ($data['photo'][$i]['is_adult'] == 1) {
								if ($data['photo'][$i]['adult_allow']) {
									$data['photo'][$i]['link_type'] = 1;
									$data['photo'][$i]['view_link'] = './viewprofile.php?sel=agreement&amp;id_file='.$row['id'];
								} else {
									$data['photo'][$i]['link_type'] = 2;
									$data['photo'][$i]['view_link'] = './viewprofile.php?sel=agreement&amp;id_file='.$row['id'].'&amp;allow_no=1';
								}
							} else {
								$data['photo'][$i]['link_type'] = 3;
								$data['photo'][$i]['view_link'] = './viewprofile.php?sel=upload_view&amp;id_file='.$row['id'].'&amp;type_upload=f';
							}
						}
					}
					$rs->MoveNext();
					$i++;
				}
				
				$param = '&amp;id_album='.$data['id_album'].'&amp;';
				$smarty->assign('links_page', GetLinkArray($data['num_items'], $page, $param, $config_index['search_numpage']));
			}
			elseif ($album_page['upload_type'] == 'a')
			{
				$data['embedded_audio'] = $settings['use_embedded_audio'];
				
				$i = 0;
				$data['audio'] = array();
				
				while (!$rs->EOF) {
					$row = $rs->GetRowAssoc(false);
					if (strlen($row['upload_path']) && file_exists($config['site_path'].$settings['audio_folder'].'/'.$row['upload_path'])) {
						$data['audio'][$i]['id']			= $row['id'];
						$data['audio'][$i]['allow']			= $row['allow'];
						$data['audio'][$i]['user_comment']	= stripslashes($row['user_comment']);
						$data['audio'][$i]['thumb_path']	= '.'.$settings['audio_folder'].'/'.$settings['audio_default'];
						$data['audio'][$i]['file_path']		= $config['site_root'].$settings['audio_folder'].'/'.$row['upload_path'];
						$data['audio'][$i]['view_link']		= './viewprofile.php?sel=upload_view&amp;id_file='.$row['id'].'&amp;type_upload=a';
					}
					$rs->MoveNext();
					$i++;
				}
				
				$param = '&amp;id_album='.$data['id_album'].'&amp;';
				$smarty->assign('links_page', GetLinkArray($data['num_items'], $page, $param, $config_index['search_numpage']));
			}
			elseif ($album_page['upload_type'] == 'v')
			{
				$i = 0;
				$data['video'] = array();
				
				while (!$rs->EOF)
				{
					$row = $rs->GetRowAssoc(false);
					
					if (strlen($row['upload_path']) && file_exists($config['site_path'].$settings['video_folder'].'/'.$row['upload_path']))
					{
						$data['video'][$i]['id']			= $row['id'];
						$data['video'][$i]['allow']			= $row['allow'];
						$data['video'][$i]['user_comment']	= stripslashes($row['user_comment']);
						$data['video'][$i]['file_name']		= $row['upload_path'];
						$data['video'][$i]['is_adult']		= $row['is_adult'];
						
						$file_name_arr = explode('.', $row['upload_path']);
						
						// video thumb
						if (file_exists($config['site_path'].$settings['video_folder'].'/'.$file_name_arr[0].'1.jpg')) {
							$data['video'][$i]['thumb_path'] = $config['server'].$config['site_root'].$settings['video_folder'].'/'.$file_name_arr[0].'1.jpg';
						} else {
							$data['video'][$i]['thumb_path'] = $config['server'].$config['site_root'].$settings['video_folder'].'/'.$settings['video_default'];
						}
						
						// video file
						/*
						// $config['server'] before $config['site_root'] removed
						if (file_exists($config['site_path'].$settings['video_folder'].'/'.$file_name_arr[0].'.flv')) {
							$data['video'][$i]['file_path'] = $config['site_root'].$settings['video_folder'].'/'.$file_name_arr[0].'.flv';
							$data['video'][$i]['is_flv'] = 1;
						} else {
							$data['video'][$i]['file_path'] = $config['site_root'].$settings['video_folder'].'/'.$row['upload_path'];
							$data['video'][$i]['is_flv'] = 0;
						}
						*/
						
						if (file_exists($config['site_path'].$settings['video_folder'].'/'.$file_name_arr[0].'-out.mp4')) {
							$data['video'][$i]['file_path'] = $config['server'].$config['site_root'].$settings['video_folder'].'/'.$file_name_arr[0].'-out.mp4';
						} else {
							$data['video'][$i]['file_path'] = $config['server'].$config['site_root'].$settings['video_folder'].'/'.$row['upload_path'];
						}
						
						define('SHOW_VIDEO_ON_PAGE', true);
						
						// view link and current video
						if (SHOW_VIDEO_ON_PAGE) {
							$data['video'][$i]['view_link'] = './viewprofile.php?id='.$id.'&amp;sel=4&amp;sub=10&amp;action=4&amp;id_album='.$data['id_album'].'&amp;id_v='.$row['id'];
							if (isset($_GET['id_v']) && $_GET['id_v'] == $row['id']) {
								$current_video_index = $i;
							}
						} else {
							// open in popup
							$data['video'][$i]['view_link'] = './viewprofile.php?sel=upload_view&amp;id_file='.$row['id'].'&amp;type_upload=v';
						}
					}
					
					$rs->MoveNext();
					$i++;
				}
				
				if (empty($current_video_index)) {
					$current_video_index = 0;
				}
				
				$data['video'][$current_video_index]['sel'] = 1;
				
				$current_video['file_path']		= $data['video'][$current_video_index]['file_path'];
				$current_video['thumb_path']	= $data['video'][$current_video_index]['thumb_path'];
				#$current_video['is_flv']		= $data['video'][$current_video_index]['is_flv'];
				
				require_once './include/class.browser_detection.php';
				
				$browser = strtolower(Browser_Detection::get_browser($_SERVER['HTTP_USER_AGENT']));
				
				// firefox and opera do not support H.264, IE9 does not play converted mp4 which are played by chrome and safari
				if (strpos($browser, 'firefox') !== false || strpos($browser, 'opera') !== false || strpos($browser, 'internet explorer') !== false) {
					$current_video['html5_video'] = false;
				} else {
					$current_video['html5_video'] = true;
				}
				
				$smarty->assign('current_video', $current_video);
				
				$param = '&amp;id_album='.$data['id_album'].'&amp;';
				$smarty->assign('links_page', GetLinkArray($data['num_items'], $page, $param, $config_index['search_numpage']));
			}
		}
	}
		
	//albums page info
	$album_page['show_photos'] = 0;
	
	if ($album_page['album_count'] > 0)
	{
		$lim_min = ($page - 1) * $config_index['albums_num_page'];
		$lim_max = (int) $config_index['albums_num_page'];
		
		$rs = $dbconn->Execute(
			'SELECT id, title, description, DATE_FORMAT(creation_date, "'.$config['date_format'].'") AS creation_date
			   FROM '.USER_ALBUMS.'
			  WHERE id_user = ? AND album_type = ? '.$allow.'
			  LIMIT '.$lim_min.', '.$lim_max,
			array($id, $album_page['upload_type']));
		
		$i = 0;
		$_album = array();
		
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$_album[$i]['id']				= $row['id'];
			$_album[$i]['title']			= stripslashes($row['title']);
			$_album[$i]['description']		= stripslashes($row['description']);
			$_album[$i]['creation_date']	= $row['creation_date'];
			
			$_album[$i]['items_count'] = $dbconn->GetOne(
				'SELECT COUNT(id)
				   FROM '.USER_UPLOAD_TABLE.'
				  WHERE id_user = ? AND status = "1" AND id_album = ? AND upload_type = ? '.$allow,
				array($id, $row['id'], $album_page['upload_type']));
			
			$rs->MoveNext();
			$i++;
		}
		
		$smarty->assign('_album', $_album);
		$param = '&amp;';
		$smarty->assign('albums_links_page', GetLinkArray($album_page['album_count'], $page, $param, $config_index['albums_num_page']));
	}
	else
	{
		$album_page['show_create_link'] = 1;
	}
	
	$smarty->assign('album_page', $album_page);
	$smarty->assign('data_9', $data);
	
	return;
}

function UploadView($par = '')
{
	global $smarty, $dbconn, $config, $user;
	
	IndexHomePage();
	
	$id_user	= $user[ AUTH_ID_USER ];
	
	$id			= isset($_GET['id']) ? (int) $_GET['id'] : 0;
	$id_file	= (int) $_GET['id_file'];
	$videoplay	= isset($_POST['videoplay']) ? trim($_POST['videoplay']) : '';
	
	if ($videoplay) {
		$dbconn->Execute('UPDATE '.USERS_TABLE.' SET videoplay = ? WHERE id = ?', array($videoplay, $id_user));
	}
	
	if ($par == '')
	{
		$rs = $dbconn->Execute(
			'SELECT a.upload_path, b.login, a.upload_type, a.user_comment, a.id_album, a.id_user, b.date_birthday, c.name AS country
			   FROM '.USER_UPLOAD_TABLE.' a
		  LEFT JOIN '.USERS_TABLE.' b ON a.id_user = b.id
		  LEFT JOIN '.COUNTRY_SPR_TABLE.' c ON c.id = b.id_country
			  WHERE a.id = ? AND a.status = "1"',
			  array($id_file));
		
		$data['file_name']		= $rs->fields[0];
		$data['login']			= stripslashes($rs->fields[1]);
		$data['upload_type']	= $rs->fields[2];
		$data['user_comment']	= stripslashes($rs->fields[3]);
		$data['id_album']		= $rs->fields[4];
		$data['age']			= AgeFromBDate($rs->fields[6]);
		$data['country']		= stripslashes($rs->fields[7]);
		$data['not_user']		= 1;
		
		$id = $rs->fields[5];
		
		$connected_status = getConnectedStatus($id, $id_user);
		
		if ($connected_status == CS_CONNECTED) {
			// select uploads for all type permissions(guest, user, friend)
			$allow = ' AND allow IN (1, 2, 3)';
		} elseif (!$user[ AUTH_GUEST ] && $id_user > 0) {
			// user registered
			$allow = ' AND allow IN (1, 2)';
		} else {
			$allow = ' AND allow = 1';
		}
		
		## $is_flv = 0;
		
		switch ($data['upload_type'])
		{
			case 'f':
				$adult_allow = CheckAdultAllow();
				$folder = GetSiteSettings('photos_folder');
				
				$strSQL =
					'SELECT id, upload_path, user_comment, is_adult
					   FROM '.USER_UPLOAD_TABLE.'
					  WHERE status = "1" AND id_album = ? AND id_user = ? '.$allow;
				$rs = $dbconn->Execute($strSQL, array($data['id_album'], $id));
				
				$i = 0;
				while (!$rs->EOF)
				{
					if (($adult_allow == 1 && $rs->fields[3] == 1) || $rs->fields[3] == 0)
					{
						$data['photos'][$i]['id'] = $rs->fields[0];
						if ($rs->fields[1] == $data['file_name']) {
							$data['photos'][$i]['sel'] = 1;
						}
						$data['photos'][$i]['user_comment'] = stripslashes($rs->fields[2]);
						$data['photos'][$i]['thumb_file'] = $config['server'].$config['site_root'].$folder.'/thumb_'.$rs->fields[1];
						$data['photos'][$i]['file'] = $config['server'].$config['site_root'].$folder.'/'.$rs->fields[1];
						$data['photos'][$i]['sizes'] = getimagesize($config['site_path'].$folder.'/'.$rs->fields[1]);
					}
					$rs->MoveNext();
					$i++;
				}
				
				$sizes = getimagesize($config['site_path'].$folder.'/thumb_'.$data['file_name']);
				$smarty->assign('sizes', $sizes);
			break;
			
			case 'a':
				$folder = GetSiteSettings('audio_folder');
			break;
			
			case 'v':
				$settings = GetSiteSettings(array('video_folder', 'flv_player_width', 'flv_player_height'));
				
				$smarty->assign('settings', $settings);
				
				$file_name_arr = explode('.', $data['file_name']);
				
				if (file_exists($config['site_path'].$settings['video_folder'].'/'.$file_name_arr[0].'-out.mp4')) {
					$data['file_name'] = $file_name_arr[0].'-out.mp4';
				}
				
				$data['file_path'] = $config['server'].$config['site_root'].$settings['video_folder'].'/'.$data['file_name'];
				
				if (file_exists($config['site_path'].$settings['video_folder'].'/'.$file_name_arr[0].'1.jpg')) {
					$data['image_path'] = $config['server'].$config['site_root'].$settings['video_folder'].'/'.$file_name_arr[0].'1.jpg';
				} else {
					$data['image_path'] = '';
				}
				
				require_once './include/class.browser_detection.php';
				
				$browser = strtolower(Browser_Detection::get_browser($_SERVER['HTTP_USER_AGENT']));
				
				// firefox and opera do not support H.264, IE9 does not play converted mp4 which are played by chrome and safari
				// IE9 issue is fixed with new ffmpeg and qt_faststart
				if (strpos($browser, 'firefox') !== false || strpos($browser, 'opera') !== false) { # || strpos($browser, 'internet explorer') !== false) {
					$data['html5_video'] = false;
				} else {
					$data['html5_video'] = true;
				}
				
				$data['videoplay'] = $dbconn->GetOne('SELECT videoplay FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
			break;
		}
		
		$smarty->assign('data', $data);
		$smarty->assign('view', 1);
		$smarty->display(TrimSlash($config['index_theme_path']).'/myprofile_upload_view.tpl');
	}
	elseif ($par == 'ajax')
	{
		$folder = GetSiteSettings('photos_folder');
		
		$rs = $dbconn->Execute('SELECT id, upload_path, user_comment FROM '.USER_UPLOAD_TABLE.' WHERE id = ?', array($id_file));
		
		#$id_photo		= $rs->fields[0];
		
		$photo			= $config['server'].$config['site_root'].$folder.'/'.$rs->fields[1];
		$user_comment	= stripslashes($rs->fields[2]);
		
		echo '
			<table cellpadding="0" cellspacing="0" align="center">
				<tr><td><img border="1" bordercolor="1" src="'.$photo.'" alt=""></td></tr>
				<tr><td align="center">'.$user_comment.'</td></tr>
			</table>
		';
	}
	exit;
}

function TagsTable()
{
	global $smarty, $dbconn, $user;

	$id				= isset($_GET['id']) ? intval($_GET['id']) : 0;
	$search_type	= $_GET['search_type'];
	
	$suffix = '&amp;id='.$id;
	
	if ($search_type) {
		$suffix .= '&amp;search_type='.$search_type;
	}
	
	$file_name = 'viewprofile.php';
	
	$data = array();
	
	$rs = $dbconn->Execute('SELECT DISTINCT tag, COUNT(id) AS tag_count FROM '.TAGS_TABLE.' WHERE id_user = ? GROUP BY tag ORDER BY tag', array($id));
	$i = 0;
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$data['user_tags'][$i]['tag'] = stripslashes($row['tag']);
		$data['user_tags'][$i]['count'] = $row['tag_count'];
		$data['user_tags'][$i]['searchlink'] = './quick_search.php?sel=search_tag&amp;tag='.$data['user_tags'][$i]['tag'];
		$rs->MoveNext();
		$i++;
	}
	
	$rs = $dbconn->Execute('SELECT id, tag FROM '.TAGS_TABLE.' WHERE id_user = ? AND id_creator = ? ORDER BY tag', array($id, $user[ AUTH_ID_USER ]));
	$i = 0;
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$data['my_tags'][$i]['tag'] = stripslashes($row['tag']);
		$data['my_tags'][$i]['dellink'] = './'.$file_name.'?sel=deltag&amp;tag='.$row['id'].$suffix;
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('data_10', $data);
	return;
}

function AddTag()
{
	global $dbconn, $user;

	$id = isset($_GET['id'])?intval($_GET['id']):0;
	$search_type = $_GET['search_type'];
	$suffix = '&amp;id='.$id;
	if ($search_type) $suffix .= '&amp;search_type='.$search_type;

	$tag_arr = explode(' ', $_POST['tag']);
	foreach ($tag_arr as $value)
	{
		$tag = addslashes(trim($value));
		if ($tag)
		{
			$rs = $dbconn->Execute('select id from '.TAGS_TABLE.' where id_creator = ? and id_user= ? and tag = ?', array($user[ AUTH_ID_USER ], $id, $tag));
			if(!$rs->fields[0])
			{
				$strSQL = "INSERT INTO ".TAGS_TABLE." (id_creator, id_user, create_date, tag) VALUES ('".$user[ AUTH_ID_USER ]."', '".$id."', '".date("Y-m-d H:i:s")."', '".$tag."')";
				$dbconn->Execute($strSQL);
			}
		}
	}

	ListProfile(6);
	return;
}

function DeleteTag()
{
	global $dbconn, $user;
	
	$id		= intval($_GET['id']);
	$id_tag	= intval($_GET['tag']);
	
	$dbconn->Execute('DELETE FROM '.TAGS_TABLE.' WHERE id_creator = ? AND id_user = ? AND id = ?', array($user[ AUTH_ID_USER ], $id, $id_tag));
	
	ListProfile(6);
	return;
}

function SendNotice($id)
{
	global $lang_mail, $config, $smarty, $dbconn, $user;
	
	$settings = GetSiteSettings(array('icon_male_default', 'icon_female_default', 'icons_folder'));
	
	// Get default icons
	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];
	
	// recipient data
	$strSQL =
		'SELECT a.id_user, b.login, b.fname, b.sname, b.gender, b.email, b.site_language
		   FROM '.SUBSCRIBE_USER_TABLE.' a
	 INNER JOIN '.USERS_TABLE.' b ON b.id = a.id_user
		  WHERE a.type = "s" AND a.id_subscribe = "3" AND b.id = ?';
	$rs = $dbconn->Execute($strSQL, array($id));
	$row = $rs->GetRowAssoc(false);
	
	$id_user['id']			= $row['id_user'];
	$id_user['login']		= $row['login'];
	$id_user['fname']		= stripslashes($row['fname']);
	$id_user['sname']		= stripslashes($row['sname']);
	$id_user['gender']		= $row['gender'];
	$id_user['email']		= $row['email'];
	$id_user['site_lang']	= $row['site_language'];
	
	if ($id_user['id'] > 0)
	{
		// language
		$site_lang = $id_user['site_lang'];

		// include language file
		$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
		$lang_mail = array();
		include $config['path_lang'].'mail/'.$lang_file;
		
		// data of visited profile
		$rs = $dbconn->Execute(
			'SELECT id, fname, date_birthday, icon_path, gender, id_country, id_city, id_region
			   FROM '.USERS_TABLE.'
			  WHERE status = "1" AND id = ?',
			array($user[ AUTH_ID_USER ]));
		$row = $rs->GetRowAssoc(false);
		
		$content				= array();
		$content['id']			= $row['id'];
		$content['fname']		= $row['fname'];
		$content['id_city']		= $row['id_city'];
		$content['id_region']	= $row['id_region'];
		$content['id_country']	= $row['id_country'];
		$content['age']			= AgeFromBDate($row['date_birthday']);
		$content['date']		= date('d-m-Y H:i');
		
		// user icon
		if (isset($row['icon_path']) && $row['icon_path'] != '') {
			$icon_path = $row['icon_path'];
		} else {
			$icon_path = $default_photos[$row['gender']];
		}
		
		$content['icon'] = 'cid:agent'.$config['server'].$config['site_root'].$settings['icons_folder'].'/'.$icon_path;
		
		$attaches					= array();
		$attaches['id'][0]			= $config['server'].$config['site_root'].$settings['icons_folder'].'/'.$icon_path;
		$attaches['image_path'][0]	= $config['site_path'].$settings['icons_folder'].'/'.$icon_path;
		$attaches['image_name'][0]	= '';
		$attaches['image_type'][0]	= 'application/octet-stream';
		
		// get base_lang; base lang contains country, region and city name
		$_LANG_NEED_ID				= array();
		$_LANG_NEED_ID['country'][]	= (int) $row['id_country'];
		$_LANG_NEED_ID['region'][]	= (int) $row['id_region'];
		$_LANG_NEED_ID['city'][]	= (int) $row['id_city'];
		
		$smarty->assign('base_lang', GetBaseLang($_LANG_NEED_ID));
		
		// links
		$content['urls']		= GetUserEmailLinks();
		$content['link_read']	= $config['server'].$config['site_root'].'/viewprofile.php?id='.$row['id'];
		
		// gender suffix
		$suffix = ($id_user['gender'] == GENDER_MALE) ? '_e' : '_t';
		
		// subject
		$subject = $lang_mail['visits_mypage'.$suffix]['subject'];
		$subject = str_replace('[date]', date('d/m/Y'), $subject);
		
		// recipient
		$name_to = trim($id_user['fname'].' '.$id_user['sname']);
		
		SendMail($site_lang, $id_user['email'], $config['site_email'], $subject, $content,
			'mail_visits_mypage_user', $attaches, $name_to, '', 'visits_mypage', $id_user['gender']);
	}
	
	return;
}

function ViewLocation()
{
	global $smarty, $config;
	
	IndexHomePage();

	$settings = GetSiteSettings(array('map_app_id', 'google_app_id', 'map_type'));
	$settings['map_app_id'] = stripslashes($settings['map_app_id']);
	$settings['google_app_id'] = stripslashes($settings['google_app_id']);

	$lat = $_GET['lat'];
	$lon = $_GET['lon'];

	$smarty->assign('lat', $lat);
	$smarty->assign('lon', $lon);
	$smarty->assign('settings', $settings);
	$smarty->display(TrimSlash($config['index_theme_path']).'/view_on_map.tpl');
	exit;
}

function CheckAdultAllow()
{
	global $dbconn, $user;
	
	$strSQL = " SELECT m.id FROM ".GROUP_MODULE_TABLE." gm, ".MODULES_TABLE." m
				LEFT JOIN ".USER_GROUP_TABLE." ug ON ug.id_user = ?
				WHERE m.name='adult_content' AND gm.id_group=ug.id_group AND gm.id_module=m.id ";
	$rs = $dbconn->Execute($strSQL, array($user[ AUTH_ID_USER ]));
	
	if ($rs->fields[0] > 0) {
		return 1;
	} else {
		return 0;
	}
}

function Agreement()
{
	global $smarty, $config;
	
	IndexHomePage();
	
	if (isset($_POST['agree']))
	{
		if ($_POST['agree'] == 'yes') {
			if ($_POST['gallary'] == 1) {
				echo "<script>document.location.href='gallary.php?sel=view_upload&amp;upload_type=".$_REQUEST["upload_type"]."&amp;id=".intval($_POST["id_file"])."';</script>";
			} else {
				echo "<script>document.location.href='viewprofile.php?sel=upload_view&amp;type_upload=f&amp;id_file=".intval($_POST["id_file"])."';</script>";
			}
		} else {
			echo '<script>window.close();</script>';
		}
	}
	else
	{
		if (isset($_REQUEST['gallary']) && intval($_REQUEST['gallary']) == 1) {
			$smarty->assign('gallary', 1);
		} else {
			$smarty->assign('gallary', 0);
		}

		if (isset($_GET['allow_no']) && $_GET['allow_no'] == 1) {
			$smarty->assign('not_allow', 1);
		} else {
			$smarty->assign('not_allow', 0);
		}

		if (isset($_REQUEST['upload_type'])) {
			$smarty->assign('upload_type', $_REQUEST['upload_type']);
		} else {
			$smarty->assign('upload_type', 'f');
		}
		
		$smarty->assign('id_file', $_GET['id_file']);
		$smarty->display(TrimSlash($config['index_theme_path']).'/viewprofile_adult_warning.tpl');
	}
	
	exit;
}

?>