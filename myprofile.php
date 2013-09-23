<?php
/**
* User profile page management (general, description, self-portrait, desired partner criteria, multimedia album, rating, tags...)
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
include './include/functions_users.php';
include './include/class.lang.php';
include './include/class.percent.php';
include './include/class.images.php';
include './include/class.phpmailer.php';
include './include/functions_mail.php';
include './include/functions_mm.php';

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
// (user can always access own profile)

// check permissions
IsFileAllowed(GetRightModulePath(__FILE__));

// alerts and statistics
if (!$user[ AUTH_GUEST ]) {
	GetAlertsMessage();
	SetModuleStatistic(GetRightModulePath(__FILE__));
}

// active menu item
if ($user[ AUTH_IS_APPLICANT ]) {
	$smarty->assign('sub_menu_num', '9');
} else {
	$smarty->assign('sub_menu_num', '1');
}

// user selection
global $reqfrom;
$sel		= isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');
$sub		= isset($_POST['sub']) ? $_POST['sub'] : (isset($_GET['sub']) ? $_GET['sub'] : '');
$act		= isset($_GET['act']) ? $_GET['act'] : '';
$reqfrom	= isset($_GET['reqfrom']) ? $_GET['reqfrom'] : '';

// limit applicants to access only selected features
if ($user[ AUTH_IS_APPLICANT ]) {
	if ($sel != '' && $sel != '1' && $sel != 'edit_application' && $sel != 'save_1' && $sel != 'upload_photo'
		&& $sel != 'save_photo' && $sel != 'delete_photo' && $sel != 'confirm_email' && $sel != 'send_confirm' && $sel != 'submit_application')
	{
		header('Location: myprofile.php');
		/*echo '<script>location.href="./myprofile.php";</script>';*/
		exit;
	}
}

// dispatcher
switch ($sel) {
	case '1': ListProfile(1, $act); break;	// Personal Data, Annonce
	case '2': ListProfile(2, $act); break;	// Description, My Personality, My Portrait, My Interests
	case '3': ListProfile(3, $act); break;	// My Criteria, His Interests
	case '4': ListProfile(4, $act, '', $sub); break;	// Multimedia
	case '5': ListProfile(5, $act); break;	// Voting
	case '6': ListProfile(6, $act); break;	// Tags
	
	case 'edit_application':
	case 'edit_1': EditProfile(1); break;	// Personal Data
	case 'edit_2': EditProfile(2); break;	// Description
	case 'edit_3': EditProfile(3); break;	// Annonce
	case 'edit_4': EditProfile(4); break;	// My Personality
	case 'edit_5': EditProfile(5); break;	// My Portrait
	case 'edit_6': EditProfile(6); break;	// My Interests
	case 'edit_7': EditProfile(7); break;	// My Criteria
	case 'edit_8': EditProfile(8); break;	// His Interests
	
	case 'save_1': SaveProfile(1); break;	// Personal Data
	case 'save_2': SaveProfile(2); break;	// Description
	case 'save_3': SaveProfile(3); break;	// Annonce
	case 'save_4': SaveProfile(4); break;	// My Personality
	case 'save_5': SaveProfile(5); break;	// My Portrait
	case 'save_6': SaveProfile(6); break;	// My Interests
	case 'save_7': SaveProfile(7); break;	// My Criteria
	case 'save_8': SaveProfile(8); break;	// His Interests
	case 'save_9': SaveProfile(9); break;	// Multimedia
	
	case 'upload_del': UploadDelete($act); break;
	case 'upload_view': UploadView(); break;
	case 'ajax_image': UploadView('ajax'); break;
	
	case 'addtag': AddTag(); break;
	case 'deltag': DeleteTag(); break;
	
	case 'couple': AcceptCouple(); break;
	
	case 'save_album': SaveAlbum(); break;
	case 'del_album': DeleteAlbum(); break;
	
	case 'upload_photo': EditProfile(11); break;
	case 'save_photo': SaveProfile(11); break;
	case 'delete_photo': IconDelete(11); break;
	
	case 'confirm_email': CheckConfirmation(); break;
	case 'send_confirm': SendConfirmation(); break;
	
	case 'submit_application': SubmitApplication(); break;
	
	default: ListProfile(1);
}

exit;


function ListProfile($page=1, $act='', $err='', $sub='', $action='', $data='')
{
	global $lang, $config, $smarty, $user, $charset;
	
	// vp homepage redirection
	if ($act == '' && isset($data['reqfrom']) && $data['reqfrom'] == 'homepage') {
		header('Location: ./homepage.php');
		exit;
	}
	
	$form['action'] = 'myprofile.php';
	
	$id_user = $user[ AUTH_ID_USER ];
	
	if (isset($_SESSION['err'])) {
		$err = $_SESSION['err'];
		unset($_SESSION['err']);
	}
	
	if ($act == '') {
		Banners(GetRightModulePath(__FILE__));
		IndexHomePage();
		GetActiveUserInfo($user);
		GeneralTable();
	}
	
	/*
	$video_page = isset($_REQUEST['vdo']) ? $_REQUEST['vdo'] : 0;
	if ($video_page) {
		$page = 99;
		if ($user[ AUTH_GENDER ] == GENDER_MALE) {
			//Thanks. 3 Jobs. English version
			$form['video_id']	 = '2A746A01-CB90-2F1D-6AB5C06DB7C95262';
			$form['video_title'] = $lang['video_title']['signed_up_e'];
		} else {
			// Thanks. 3 Jobs. Thai
			$form['video_id']	 = '2A7973DC-C133-0DE8-40EC3DD6D5E2D089';
			$form['video_title'] = $lang['video_title']['signed_up_t'];
		}
	}
	$smarty->assign('video_page', $video_page);
	*/
	
	switch ($page)
	{
		case 1:
			PersonalTable();
			DescriptionTable();
			AnnonceTable();
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
				$sub = (int) $_GET['sub'];
			}
			if ($action == '' && isset($_GET['action'])) {
				$action = $_GET['action'];
			}
			UploadTable($sub, $action, $data);
			$form['session_id'] = session_id();		// flash upload
		break;
		case 5:
			VotingTable($id_user);
		break;
		case 6:
			TagsTable();
		break;
	}
	
	// password change success message (external message passing)
	if (isset($_GET['pwd']) && $_GET['pwd']) {
		$err = $lang['err']['pass_saved'];
	}
	
	// pass error message
	$form['err'] = $err;
	
	// re-send confirmation email link
	if ($sub == 'reconfirm') {
		$form['task'] = 3;
		$form['reconfirm_link'] = 1;
		$form['action'] = 'myprofile.php?sel=send_confirm';
	}
	
	$form['page'] = intval($page);
	
	if ($user[ AUTH_IS_APPLICANT ]) {
		//VP registration steps status
		$steps['upload_photo'] = isPhotoUploaded($id_user);
		$steps['confirm_email'] = isEmailConfirmed($id_user);
		if (USE_PROFILE_EDIT_IN_SIGNUP_SANDBOX) {
			$steps['edit_application'] = isProfileCompleted($id_user);
		}
		$steps['app_submit'] = isApplicationSubmit($id_user); // from functions_users.php
		
		if ($steps['app_submit']) {
			$video_id  = ($user[ AUTH_GENDER ] == GENDER_MALE) ? '2A9FF901-D8CD-21B5-65C6B15CB02972CD' : '2A76E244-E13C-F005-7825780974D01CAA';
			$video_url = $config['server'].$config['site_root'].'/video_player.php?vid='.$video_id;
			$application_submit_msg = str_replace('[VIDEO_LINK]', $video_url, $lang['err']['application_submit_success']);
			$smarty->assign('application_submit_msg', $application_submit_msg);
		}
		
		$smarty->assign('steps', $steps);
	}
	
	// profile confirmation by user
	if (isset($_GET['sel_act']) && $_GET['sel_act'] == 'congrat') {
		#$smarty->assign('congrat', true);
		$form['err'] = $lang['confirm']['success_confirm_user'];
	}
	
	$links['edit_link_1'] = './myprofile.php?sel=edit_1';
	$links['edit_link_2'] = './myprofile.php?sel=edit_2';
	$links['edit_link_3'] = './myprofile.php?sel=edit_3';
	$links['edit_link_4'] = './myprofile.php?sel=edit_4';
	$links['edit_link_5'] = './myprofile.php?sel=edit_5';
	$links['edit_link_6'] = './myprofile.php?sel=edit_6';
	$links['edit_link_7'] = './myprofile.php?sel=edit_7';
	$links['edit_link_8'] = './myprofile.php?sel=edit_8';
	
	$smarty->assign('links', $links);
	
	
	
	/**	
	*	SH2 setting smarty variable to show the password to fb registered useron very first time he logs into the 
	*	account.
	**/
	@$pass = $_SESSION['fpass'] ? $_SESSION['fpass']: '' ;
	$smarty->assign('pass',$pass);
	//

	$smarty->assign('form', $form);
	$smarty->assign('header', $lang['profile']);
	$smarty->assign('button', $lang['button']);
	
	if ($act == '') {
		if ($user[ AUTH_IS_APPLICANT ]) {
			$edit_link = './myprofile.php?sel=edit_application';
		} else {
			$edit_link = './myprofile.php?sel=edit_1';
		}
		$smarty->assign('edit_link', $edit_link);
		$smarty->display(TrimSlash($config['index_theme_path']).'/myprofile_table.tpl');
	} else {
		// ajax
		header('Content-type: text/html; charset='.$charset);
		$smarty->assign('template_root', $config['index_theme_path']);
		$smarty->display(TrimSlash($config['index_theme_path']).'/myprofile_view.tpl');
	}
	
	exit;
}


function EditProfile($num, $err = '')
{
	global $lang, $config, $smarty, $dbconn, $user; // $reqfrom;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	if (isset($_SESSION['err'])) {
		$err = $_SESSION['err'];
		unset($_SESSION['err']);
	}
	
	// check if application has been submitted
	if ($user[ AUTH_IS_APPLICANT ]) {
		$mm_application_submit = $dbconn->GetOne('SELECT mm_application_submit FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
		if (!empty($mm_application_submit)) {
			if ($num == 11) {
				ListProfile(1, '', $lang['err']['application_already_submitted_no_upload']);
			} else {
				ListProfile(1, '', $lang['err']['application_already_submitted_no_edit']);
			}
			exit;
		}
	}
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
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
		
		$rs = $dbconn->Execute($strSQL, array($id_user));
		
		if (!$rs->EOF)
		{
			$row = $rs->GetRowAssoc(false);
			
			if ($row['home_area_color'] != '') {
				$color['home_menu'] = $row['home_area_color'];
			}
			if ($row['shoutbox_color'] != '') {
				$color['shoutbox_color'] = $row['shoutbox_color'];
				$_SESSION['shoutbox_color_my'] = $row['shoutbox_color'];
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
			$smarty->assign('id_customed', $id_user);
		}
		else
		{
			unset($_SESSION['shoutbox_color_my']);
		}
	}
	else
	{
		unset($_SESSION['shoutbox_color_my']);
	}
	
	switch ($num)
	{
		case 1:
			
			$form = PersonalForm();
			$form['menu'] = 'myprofile.php';
			
			if ($user[ AUTH_IS_APPLICANT ]) {
				$form['task'] = 2;
				$form['header'] = $lang['subsection']['edit_application_info'];
				$form['tool_tip'] = $lang['subsection']['edit_application_info_tool'];
				$form['subheader'] = $lang['subsection']['edit_application_info_text'];
				$form['subheader_tool'] = $lang['subsection']['edit_application_info_text_tool'];
			} else {
				$form['header'] = $lang['subsection']['personal_info'];
				$form['tool_tip'] = $lang['subsection']['personal_info_tool_thai'];
				$form['subheader'] = $lang['subsection']['personal_info_text'];
				$form['subheader_tool'] = $lang['subsection']['personal_info_text_thai'];
			}
		
		break;
		
		case 2:
			
			$form = DescriptionForm();
			$form['menu'] = 'myprofile.php?sel=2';
			$form['header'] = $lang['subsection']['description'];
			$form['tool_tip'] = $lang['subsection']['description_tool'];
		
		break;
	
		case 3:
			
			$form = AnnonceForm();
			$form['menu'] = 'myprofile.php';
			$form['header'] = $lang['subsection']['notice'];
			$form['tool_tip'] = $lang['subsection']['notice_tool'];
		
		break;
	
		case 4:
			
			$form = MyPersonalityForm();
			$form['menu'] = 'myprofile.php?sel=2';
			$form['header'] = $lang['subsection']['personal'];
			$form['tool_tip'] = $lang['subsection']['personal_tool'];
		
		break;
		
		case 5:
			
			$form = MyPortraitForm();
			$form['menu'] = 'myprofile.php?sel=2';
			$form['header'] = $lang['subsection']['portreit'];
			$form['tool_tip'] = $lang['subsection']['portreit_tool'];
		
		break;
		
		case 6:
			
			$form = MyInterestsForm();
			$form['menu'] = 'myprofile.php?sel=2';
			$form['header'] = $lang['subsection']['interest'].' ( '.$lang['interests_opt'][1].' / '.$lang['interests_opt'][2].' / '.$lang['interests_opt'][3].' )';
			$form['tool_tip'] = $lang['subsection']['interest'].' ( '.$lang['interests_opt'][1].' / '.$lang['interests_opt'][2].' / '.$lang['interests_opt'][3].' )';
		
		break;
		
		case 7:
			
			$form = MyCriteriaForm();
			$form['menu'] = 'myprofile.php?sel=3';
			$form['header'] = $lang['subsection']['criteria'];
			$form['tool_tip'] = $lang['subsection']['criteria_tool'];
		
		break;
		
		case 8:
			
			$form = HisInterestsForm();
			$form['menu'] = 'myprofile.php?sel=3';
			$form['header'] = $lang['subsection']['match_interest'].' ( '.$lang['interests_opt'][1].' / '.$lang['interests_opt'][2].' / '.$lang['interests_opt'][3].' )';
			$form['tool_tip'] = $lang['subsection']['match_interest'].' ( '.$lang['interests_opt'][1].' / '.$lang['interests_opt'][2].' / '.$lang['interests_opt'][3].' )';
		
		break;
		
		case 10:
			
			$form['task'] = 3;
			$form['menu'] = 'myprofile.php';
			$form['header'] = $lang['subsection']['email_confirmation'];
			$form['tool_tip'] = $lang['subsection']['email_confirmation_thai'];
		
		break;
	
		case 11:
			
			$form = IconUpload('myprofile');
			$form['session_id'] = session_id();		// flash upload
			$form['task'] = 1;
			$form['menu'] = 'myprofile.php';
			$form['header'] = $lang['subsection']['upload'];
			$form['tool_tip'] = $lang['subsection']['upload_tool'];
		
		break;
	}
	
	// handle external messages and errors
	if ($user[ AUTH_IS_APPLICANT ] && isset($_GET['missing']) && $_GET['missing'] == '1') {
		$err = $lang['err']['application_photo_missing'];
	}
	
	//VP registration steps status
	if ($user[ AUTH_IS_APPLICANT ]) {
		$steps['upload_photo'] = isPhotoUploaded($id_user);
		$steps['confirm_email'] = isEmailConfirmed($id_user);
		if (USE_PROFILE_EDIT_IN_SIGNUP_SANDBOX) {
			$steps['edit_application'] = isProfileCompleted($id_user);
		}
		$steps['app_submit'] = isApplicationSubmit($id_user);
		$smarty->assign('steps', $steps);
	}
	
	$form['err'] = $err;
	$form['num'] = $num;
	$form['action'] = 'myprofile.php';
	$smarty->assign('form', $form);
	$smarty->assign('header', $lang['profile']);
	$smarty->assign('tool_tip', $lang['profile']);	
	
	$smarty->display(TrimSlash($config['index_theme_path']).'/myprofile_edit.tpl');
}


function SaveProfile($num)
{
	global $dbconn, $user;
	
	$debug = false;
	
	if ($debug) echo 'SaveProfile('.$num.')<br />';
	if ($debug) print_r($_POST);
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$_action = $_sub = '';
	
	// remove parameter and add to functions
	$profile_percent = new Percent($id_user);
	
	switch ($num)
	{
		case 1:
			
			$err = SavePersonalForm($profile_percent);
			$page = 1;
		
		break;
		
		case 2:
			
			$data = $_POST;
			$info = $_POST['info'];
			$spr = $_POST['spr'];
			$err = SaveDescriptionForm($profile_percent, $data, $spr, $info);
			$page = 2;
			
		break;
		
		case 3:
			
			$data = $_POST;
			$err = SaveAnnonceForm($data);
			$page = 1;
			
		break;
		
		case 4:
			
			$personal = $_POST['personal'];
			$spr = $_POST['p_spr'];
			$err = SaveMyPersonalityForm($profile_percent, $spr, $personal);
			$page = 2;
			
		break;
		
		case 5:
			
			$spr = $_POST['port_spr'];
			$portrait = isset($_POST['portrait']) ? $_POST['portrait'] : '';
			$err = SaveMyPortraitForm($profile_percent, $spr, $portrait);
			$page = 2;
			
		break;
		
		case 6:
			
			$spr = $_POST['int_spr'];
			$interests = isset($_POST['interests']) ? $_POST['interests'] : '';
			$err = SaveMyInterestsForm($profile_percent, $spr, $interests);
			$page = 2;
			
		break;
		
		case 7:
		
			$data = $_POST;
			$spr = $_POST['spr'];
			$info = isset($_POST['info']) ? $_POST['info'] : '';
			$err = SaveMyCriteriaForm($profile_percent, $data, $spr, $info);
			$page = 3;
			
		break;
		
		case 8:
			
			$spr = $_POST['int_spr'];
			$interests = isset($_POST['interests']) ? $_POST['interests'] : '';
			$err = SaveHisInterestsForm($profile_percent, $spr, $interests);
			$page = 3;
			
		break;
		
		case 9:
			
			// multimedia upload
			if ($debug) echo 'multimedia upload<br />';
			
			$data = $_POST;
			
			$data['upload_type'] = $_GET['upload_type'];
			
			switch ($data['upload_type']) {
				case 'icon':
					$_sub = 7;
					$images_obj = new Images($dbconn);
					$err = $images_obj->UploadIcon('file_upload', $id_user);
					$data = $images_obj->data;
				break;
				case 'a':
					$_sub = 9;
					$images_obj = new Images($dbconn);
					$err = $images_obj->UploadAudio('file_upload', $id_user);
					$data = $images_obj->data;
				break;
				case 'v':
					$_sub = 10;
					$images_obj = new Images($dbconn);
					$err = $images_obj->UploadVideo('file_upload', $id_user);
					$data = $images_obj->data;
				break;
				case 'f':
				default:
					$_sub = 8;
					$images_obj = new Images($dbconn);
					$err = $images_obj->UploadPhoto('file_upload', $id_user);
					$data = $images_obj->data;
				break;
			}
			
			if (substr($err, 0, 2) != 'OK') {
				if (isset($_GET['act']) && ($_GET['act'] == 'ajax' || $_GET['act'] == 'flash')) {
					echo $err;
					exit;
				} else {
					ListProfile(4, '', $err, $_sub, 'browse_album', $data);
					return;
				}
			}
			
			if ($_sub == 7) {
				// icon uplod special: load full page so icon in uppper page is refreshed
				echo 'OK';
				$_SESSION['err'] = substr($err, 3);
				exit;
			} else if (isset($_GET['act']) && ($_GET['act'] == 'ajax' || $_GET['act'] == 'flash')) {
				echo $err;
				exit;
			} else {
				$_SESSION['err'] = substr($err, 3);
				header('Location: myprofile.php?sel=4&sub='.$_sub.'&action=browse_album&id_album='.$data['id_album']);
				exit;
				//RS: we can try without redirect and need to see that we somehow display the message
				#ListProfile(4, '', $err, $_sub, 'browse_album', $data);
				#return;
			}
		
		break;
		
		case 11:
			
			// icon upload
			$images_obj = new Images($dbconn);
			$err = $images_obj->UploadIcon('file_upload', $id_user);
			
			if (substr($err, 0, 2) != 'OK') {
				if (isset($_GET['act']) && ($_GET['act'] == 'ajax' || $_GET['act'] == 'flash')) {
					echo $err;
					exit;
				} else {
					EditProfile(11, $err);
					return;
				}
			}
			
			// GA_TRACKING
			$_SESSION['ga_event_code'] = 'picupload';
			
			$_SESSION['err'] = substr($err, 3);
			
			if (isset($_GET['act']) && ($_GET['act'] == 'ajax' || $_GET['act'] == 'flash')) {
				echo $err;
				exit;
			} else {
				header('Location: myprofile.php?sel=upload_photo');
				exit;
			}
			
			//EditProfile(11, substr($err, 3));
			//return;
			
		break;
	}
	
	if ($err) {
		EditProfile($num, $err);
	} else {
		ListProfile($page, '', '', $_sub, $_action, $data);
	}
	
	return;
}


function GeneralTable()
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$settings = GetSiteSettings(array('icons_folder', 'photos_folder', 'min_age_limit', 'max_age_limit', 'photos_default',
		'use_pilot_module_webrecorder', 'big_thumb_max_width', 'big_thumb_max_height', 'icon_male_default', 'icon_female_default'));
	
	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];
	
	$data = array();
	
	if ($settings['use_pilot_module_webrecorder']) {
		$data['webrecorder_recorder'] = $config['site_root'].'/webrecorder/wr.php?type=recorder&user='.$id_user;
	}
	
	// icon
	$rs = $dbconn->Execute('SELECT big_icon_path FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
	$row = $rs->GetRowAssoc(false);
	
	$icon_path = $row['big_icon_path'];
	
	if ($icon_path && file_exists($config['site_path'].$settings['icons_folder'].'/'.$icon_path)) {
		$data['icon_path'] = $config['site_root'].$settings['icons_folder'].'/'.$icon_path;
		$icon_count = 1;
	} else {
		$file = $default_photos[$user[ AUTH_GENDER ]];
		$data['icon_path'] = '.'.$settings['icons_folder'].'/'.$file;
		$icon_count = 0;
	}
	
	$rs = $dbconn->Execute('SELECT id, upload_path, allow, upload_type FROM '.USER_UPLOAD_TABLE.' WHERE id_user = ? ORDER BY upload_type', array($id_user));
	
	$db_upload = array();
	$i = 0;
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$db_upload[$row['upload_type']][$i]['id'] = $row['id'];
		$db_upload[$row['upload_type']][$i]['file_path'] = $row['upload_path'];
		$rs->MoveNext();
		$i++;
	}
	
	$i = 0;
	
	if (isset($db_upload['f']) && is_array($db_upload['f']))
	{
		$images_obj = new Images($dbconn);
		
		foreach ($db_upload['f'] as $photo)
		{
			if (file_exists($config['site_path'].$settings['photos_folder'].'/thumb_'.$photo['file_path']) && strlen($photo['file_path'])) {
				$data['thumb_photo_path'][$i]['path'] = $config['site_root'].$settings['photos_folder'].'/thumb_'.$photo['file_path'];
			}
			
			if (file_exists($config['site_path'].$settings['photos_folder'].'/'.$photo['file_path']) && !file_exists($config['site_path'].$settings['photos_folder'].'/thumb_'.$photo['file_path']) && strlen($photo['file_path'])) {
				$data['thumb_photo_path'][$i]['path'] = $config['site_root'].$settings['photos_folder'].'/'.$photo['file_path'];
				$data['thumb_photo_path'][$i]['sizes'] = $images_obj->GetResizeParametrsStr($config['site_path'].$settings['photos_folder'].'/'.$photo['file_path']);
			}
			
			if (file_exists($config['site_path'].$settings['photos_folder'].'/'.$photo['file_path']) && strlen($photo['file_path'])) {
				$i++;
			}
		}
	}
	
	$data['photo_count'] = (isset($db_upload['f']) ? count($db_upload['f']) : 0) + $icon_count;
	$data['addf_link']='myprofile.php?sel=4';
	
	$data['audio_count'] = isset($db_upload['a']) ? count($db_upload['a']) : 0;
	$data['adda_link']='myprofile.php?sel=4';
	
	$data['video_count'] = isset($db_upload['v']) ? count($db_upload['v']) : 0;
	$data['addv_link']='myprofile.php?sel=4';
	
	$data['login'] = $user[ AUTH_LOGIN ];
	$data['print_link'] = 'viewprofile.php?login='.$data['login'].'&amp;sel=print';
	
	$strSQL =
		'SELECT a.login, b.name AS country, c.name AS city, k.name AS region, a.date_birthday AS birthday, a.gender
		   FROM '.USERS_TABLE.' a
	  LEFT JOIN '.COUNTRY_SPR_TABLE.' b ON b.id=a.id_country
	  LEFT JOIN '.CITY_SPR_TABLE.' c ON c.id=a.id_city
	  LEFT JOIN '.REGION_SPR_TABLE.' k ON k.id=a.id_region
		  WHERE a.id = ?';
	
	$rs = $dbconn->Execute($strSQL, array($id_user));
	$row = $rs->GetRowAssoc(false);
	
	$data['login']		= $row['login'];
	$data['country']	= stripslashes($row['country']);
	$data['region']		= stripslashes($row['region']);
	$data['city']		= stripslashes($row['city']);
	$data['age']		= AgeFromBDate($row['birthday']);
	$data['gender']		= $lang['gender'][$row['gender']];
	
	// profile completion
	$profile_percent = new Percent($id_user);
	$data['complete'] = $profile_percent->GetAllPercent();
	
	$smarty->assign('settings', $settings);
	$smarty->assign('data', $data);
	
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
			
		$rs = $dbconn->Execute($strSQL, array($id_user));
		
		if ($rs->fields[0]>0)
		{
			$row = $rs->GetRowAssoc(false);
			
			if ($row['home_area_color'] != '') {
				$color['home_menu'] = $row['home_area_color'];
			}
			
			if ($row['shoutbox_color'] != '') {
				$color['shoutbox_color'] = $row['shoutbox_color'];
				$_SESSION['shoutbox_color_my'] = $row['shoutbox_color'];
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
				$settings = GetSiteSettings( array('photos_folder'));
				$color['bg_picture_path'] = $config['site_root'].$settings['photos_folder'].'/'.$row['bg_picture_path'];
			}
			
			$smarty->append('css_color', $color, true);
			$smarty->assign('customised', '1');
			$smarty->assign('id_customed', $id_user);
		}
		else
		{
			unset($_SESSION['shoutbox_color_my']);
		}
	}
	else
	{
		unset($_SESSION['shoutbox_color_my']);
	}
	return;
}


function PersonalTable()
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	// profile switchboard
	$use_field = array();
	$mandatory = array();
	
	include './customize/profile_switchboard.php';
	
	$smarty->assign('use_field', $use_field);
	$smarty->assign('mandatory', $mandatory);
	
	// current user
	$id_user = $user[ AUTH_ID_USER ];
	
	// data completion
	$profile_percent = new Percent($id_user);
	$data['complete'] = $profile_percent->GetSectionPercent(1);
	
	// multi-lang tables
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	$strSQL =
		'SELECT a.login, a.sname, a.fname, b.name AS country, r.name AS region, c.name AS city, 
				d.'.$field_name.' AS nationality, e.'.$field_name.' AS lang1, f.'.$field_name.' AS lang2, g.'.$field_name.' AS lang3, 
				DATE_FORMAT(a.date_birthday, "'.$config['date_format'].'") AS date_birthday, a.date_birthday AS birthday, a.gender, 
				a.couple, a.couple_user, h.session, hot.id_friend AS hotlist, a.email, a.phone, a.zipcode, a.headline, l.name AS site_language_text,
				a.mm_nickname, a.mm_id_number, a.mm_contact_phone_number, a.mm_contact_mobile_number, a.mm_marital_status, m.'.$field_name.' AS mm_marital_status_text, 
				a.mm_place_of_birth, a.mm_city, a.mm_address_1, a.mm_address_2, a.mm_level_of_english, english.'.$field_name.' AS mm_level_of_english_text, 
				a.mm_employment_status, emp.'.$field_name.' AS mm_employment_status_text, a.mm_business_name, a.mm_employer_name, 
				a.mm_job_position, a.mm_work_address, a.mm_work_phone_number,
				a.mm_ref_1_first_name, a.mm_ref_1_last_name, a.mm_ref_1_relationship, a.mm_ref_1_phone_number, 
				a.mm_ref_2_first_name, a.mm_ref_2_last_name, a.mm_ref_2_relationship, a.mm_ref_2_phone_number,
				a.about_me, a.what_i_do, a.my_idea, a.hoping_to_find,a.id_height,a.id_weight,
				up.hide_online, up.promotion_1, up.promotion_2, up.promotion_3, up.visible_lady, up.visible_guy,
				up.vis_lady_1, up.vis_lady_2, up.vis_lady_3, up.vis_lady_4, up.vis_lady_5,
				up.vis_guy_1, up.vis_guy_2, up.vis_guy_3, up.vis_guy_4, up.vis_guy_5,
				uh.name as height,wh.name as weight
		   FROM '.USERS_TABLE.' a
	  LEFT JOIN '.COUNTRY_SPR_TABLE.' b ON b.id = a.id_country
	  LEFT JOIN '.CITY_SPR_TABLE.' c ON c.id = a.id_city
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' d ON d.id_reference = a.id_nationality AND d.table_key = '.$multi_lang->TableKey(NATION_SPR_TABLE).'
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' e ON e.id_reference = a.id_language_1 AND e.table_key = '.$multi_lang->TableKey(LANGUAGE_SPR_TABLE).'
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' f ON f.id_reference = a.id_language_2 AND f.table_key = '.$multi_lang->TableKey(LANGUAGE_SPR_TABLE).'
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' g ON g.id_reference = a.id_language_3 AND g.table_key = '.$multi_lang->TableKey(LANGUAGE_SPR_TABLE).'
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' m ON m.id_reference = a.mm_marital_status AND m.table_key = 200
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' english ON english.id_reference = a.mm_level_of_english AND english.table_key = 201
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' emp ON emp.id_reference = a.mm_employment_status AND emp.table_key = 202
	  LEFT JOIN '.ACTIVE_SESSIONS_TABLE.' h ON h.id_user = a.id
	  LEFT JOIN '.HOTLIST_TABLE.' hot ON hot.id_user = '.$id_user.' AND hot.id_friend = a.id
	  LEFT JOIN '.REGION_SPR_TABLE.' r ON r.id = a.id_region
	  LEFT JOIN '.LANGUAGE_TABLE.' l ON l.id = a.site_language
	  LEFT JOIN '.USER_PRIVACY_SETTINGS.' AS up ON up.id_user = a.id
	LEFT JOIN '.HEIGHT_SPR_TABLE.' AS uh ON uh.id = a.id_height
	LEFT JOIN '.WEIGHT_SPR_TABLE.' AS wh ON wh.id = a.id_weight	
		  
		  WHERE a.id = ?';
	
	$rs = $dbconn->Execute($strSQL, array($id_user));
	$row = $rs->GetRowAssoc(false);
	
	// login
	$data['login']						= $row['login'];
	
	// personal info
	$data['fname']						= stripslashes($row['fname']);
	$data['sname']						= stripslashes($row['sname']);
	$data['mm_nickname']				= stripslashes($row['mm_nickname']);
	$data['gender']						= $row['gender'];
	$data['gender_text']				= $lang['gender'][$row['gender']];
	$data['mm_marital_status']			= $row['mm_marital_status'];
	$data['mm_marital_status_text']		= stripslashes($row['mm_marital_status_text']);
	$data['date_birthday']				= $row['date_birthday'];
	$data['age']						= AgeFromBDate($row['birthday']);
	$data['mm_place_of_birth']			= stripslashes($row['mm_place_of_birth']);
	$data['nationality']				= stripslashes($row['nationality']);
	$data['mm_id_number']				= stripslashes($row['mm_id_number']);
	
	//added height and weight
	$data['height']						= $row['height'];
	$data['weight']						= $row['weight'];
	
	// contact info
	$data['email']						= $row['email'];
	$data['mm_contact_phone_number']	= stripslashes($row['mm_contact_phone_number']);
	$data['mm_contact_mobile_number']	= stripslashes($row['mm_contact_mobile_number']);
	$data['phone']						= $row['phone'];
	
	// address info
	$data['country']					= stripslashes($row['country']);
	$data['region']						= stripslashes($row['region']);
	$data['city']						= stripslashes($row['city']);
	$data['mm_city']					= stripslashes($row['mm_city']);
	$data['zipcode']					= stripslashes($row['zipcode']);
	$data['mm_address_1']				= stripslashes($row['mm_address_1']);
	$data['mm_address_2']				= stripslashes($row['mm_address_2']);
	
	// language info
	$data['languages']					= $row['lang1'] . ', ';
	if (strlen($row['lang2']) > 0) $data['languages'] .= $row['lang2'] . ', ';
	if (strlen($row['lang3']) > 0) $data['languages'] .= $row['lang3'] . ', ';
	$data['languages']					= trim($data['languages'], ', ');
	$data['mm_level_of_english_text']	= stripslashes($row['mm_level_of_english_text']);
	$data['site_language_text']			= ucfirst($row['site_language_text']);
	
	// employment info
	$data['mm_employment_status']		= $row['mm_employment_status'];
	$data['mm_employment_status_text']	= stripslashes($row['mm_employment_status_text']);
	$data['mm_business_name']			= stripslashes($row['mm_business_name']);
	$data['mm_employer_name']			= stripslashes($row['mm_employer_name']);
	$data['mm_job_position']			= stripslashes($row['mm_job_position']);
	$data['mm_work_address']			= stripslashes($row['mm_work_address']);
	$data['mm_work_phone_number']		= stripslashes($row['mm_work_phone_number']);
	
	// references
	$data['mm_ref_1_first_name']		= stripslashes($row['mm_ref_1_first_name']);
	$data['mm_ref_1_last_name']			= stripslashes($row['mm_ref_1_last_name']);
	$data['mm_ref_1_relationship']		= stripslashes($row['mm_ref_1_relationship']);
	$data['mm_ref_1_phone_number']		= stripslashes($row['mm_ref_1_phone_number']);
	$data['mm_ref_2_first_name']		= stripslashes($row['mm_ref_2_first_name']);
	$data['mm_ref_2_last_name']			= stripslashes($row['mm_ref_2_last_name']);
	$data['mm_ref_2_relationship']		= stripslashes($row['mm_ref_2_relationship']);
	$data['mm_ref_2_phone_number']		= stripslashes($row['mm_ref_2_phone_number']);
	
	// couple
	$data['couple']						= $row['couple'];
	$data['couple_user']				= $row['couple_user'];
	
	if ($row['couple_user']) {
		$rs_couple = $dbconn->Execute('SELECT login, gender, date_birthday, couple_user FROM '.USERS_TABLE.' WHERE id = ?', array($row['couple_user']));
		$data['couple_login']			= $rs_couple->fields[0];
		$data['couple_link']			= 'viewprofile.php?id='.$row['couple_user'];
		$data['couple_gender']			= $lang['gender'][$rs_couple->fields[1]];
		$data['couple_age']				= AgeFromBDate($rs_couple->fields[2]);
		$data['couple_accept']			= $rs_couple->fields[3] == $id_user ? 1 : 0;
		$rs_couple->Free();
	}
	
	// status
	$data['status'] = $row['session'] ? $lang['status']['on'] : $lang['status']['off'];
	
	// headline
	$data['headline']					= stripslashes($row['headline']);
	
	// personal settings
	$data['visible_lady']				= isset($row['visible_lady']) ? intval($row['visible_lady']) : 1;
	
	$data['vis_lady_1']					= isset($row['vis_lady_1']) ? intval($row['vis_lady_1']) : 1;
	$data['vis_lady_2']					= isset($row['vis_lady_2']) ? intval($row['vis_lady_2']) : 1;
	$data['vis_lady_3']					= isset($row['vis_lady_3']) ? intval($row['vis_lady_3']) : 1;
	
	$data['visible_guy']				= isset($row['visible_guy']) ? intval($row['visible_guy']) : 1;
	
	$data['vis_guy_1']					= isset($row['vis_guy_1']) ? intval($row['vis_guy_1']) : 1;
	$data['vis_guy_2']					= isset($row['vis_guy_2']) ? intval($row['vis_guy_2']) : 1;
	$data['vis_guy_3']					= isset($row['vis_guy_3']) ? intval($row['vis_guy_3']) : 1;
	$data['vis_guy_4']					= isset($row['vis_guy_4']) ? intval($row['vis_guy_4']) : 1;
	
	$data['promotion_1']				= isset($row['promotion_1']) ? intval($row['promotion_1']) : 0;
	$data['promotion_2']				= isset($row['promotion_2']) ? intval($row['promotion_2']) : 0;
	$data['promotion_3']				= isset($row['promotion_3']) ? intval($row['promotion_3']) : 0;
	
	$data['hide_online']				= isset($row['hide_online']) ? intval($row['hide_online']) : 0;
	
	// biography
	$data['about_me']					= stripslashes($row['about_me']);
	$data['what_i_do']					= stripslashes($row['what_i_do']);
	$data['my_idea']					= stripslashes($row['my_idea']);
	$data['hoping_to_find']				= stripslashes($row['hoping_to_find']);
	
	// looking for
	$rs = $dbconn->Execute('SELECT gender, couple, age_max, age_min, id_relationship FROM '.USER_MATCH_TABLE.' WHERE id_user = ?', array($id_user));
	$row = $rs->GetRowAssoc(false);
	$data['search_gender']				= $lang['gender_search'][$row['gender']];
	$data['search_couple']				= $row['couple'];
	$data['min_age']					= $row['age_min'];
	$data['max_age']					= $row['age_max'];
	
	// relationship
	if (strlen($row['id_relationship'])) {
		$where_str = ($row['id_relationship'] == 0) ? '' : 'WHERE a.id IN ('.$row['id_relationship'].')';
		
		$strSQL = 
			'SELECT a.id, b.'.$field_name.' AS name
			   FROM '.RELATION_SPR_TABLE.' a
		  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.id_reference = a.id AND b.table_key = '.$multi_lang->TableKey(RELATION_SPR_TABLE).'
					'.$where_str.'
		   ORDER BY a.sorter';
		$rs = $dbconn->Execute($strSQL);
		
		$relations = array();
		
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$relations[] = stripslashes($row['name']);
			$rs->MoveNext();
		}
		
		$data['relationship']			= implode(', ', $relations);
	}
	
	$smarty->assign('data_1', $data);
	return;
}


function DescriptionTable()
{
	global $lang, $smarty, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$info = array();
	
	$profile_percent = new Percent($id_user);
	$info['complete'] = $profile_percent->GetSectionPercent(2);
	
	// multi-lang tables
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	$strSQL =
		'SELECT m.'.$field_name.' AS weight, k.'.$field_name.' AS height
		   FROM '.USERS_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' m ON m.id_reference = a.id_weight AND m.table_key = '.$multi_lang->TableKey(WEIGHT_SPR_TABLE).' 
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' k ON k.id_reference = a.id_height AND k.table_key = '.$multi_lang->TableKey(HEIGHT_SPR_TABLE).' 
		  WHERE a.id = ?';
	
	$rs = $dbconn->Execute($strSQL, array($id_user));
	$row = $rs->GetRowAssoc(false);
	
	$info['weight'] = $row['weight'];
	$info['height'] = $row['height'];
	
	$strSQL =
		'SELECT b.id AS id_spr, a.id_value, d.'.$field_name.' AS sprname, c.'.$field_name.' AS value, b.type
		   FROM '.DESCR_SPR_TABLE.' b
	  LEFT JOIN '.DESCR_SPR_USER_TABLE.' a ON a.id_user = ? AND b.id = a.id_spr
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' d ON d.id_reference = b.id AND d.table_key = '.$multi_lang->TableKey(DESCR_SPR_TABLE).' 
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' c ON c.id_reference = a.id_value AND c.table_key = '.$multi_lang->TableKey(DESCR_SPR_VALUE_TABLE).' 
		  WHERE LENGTH(b.name) > 0
	   ORDER BY b.sorter, value';
	
	$rs = $dbconn->Execute($strSQL, array($id_user));
	
	$i = 0;
	$spr_id = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		
		$id_spr = $row['id_spr'];
		#$id_value = $row['id_value']; // not in use
		
		if (isset($spr_id[$i]) && $id_spr != $spr_id[$i]) {
			$i++;
		}
		
		$spr_id[$i] = $id_spr;
		$info['info'][$i]['spr'] = $row['sprname'];
		$info['info'][$i]['value'] = '';
		$value = ($row['type'] == '2' && intval($row['id_value']) == 0) ? $lang['button']['all'] : $value = $row['value'];
		
		if (isset($info['info'][$i]['value']) && strlen($info['info'][$i]['value'])>0 && $value != $lang['button']['all'] && $value != '') {
			$info['info'][$i]['value'] .= '<br/>';
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
	global $smarty, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$rs = $dbconn->Execute('SELECT comment FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
	
	$data['complete'] = (strlen($rs->fields[0]) > 0) ? 100 : 0;
	$data['annonce'] = stripslashes($rs->fields[0]);
	
	$smarty->assign('data_3', $data);
	
	return;
}


function MyPersonalityTable()
{
	global $smarty, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$data = array();
	
	$profile_percent = new Percent($id_user);
	$data['complete'] = $profile_percent->GetSectionPercent(3);
	
	// multi-lang tables
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	$strSQL =
		'SELECT b.id as id_spr, a.id_value, d.'.$field_name.' AS sprname, c.'.$field_name.' AS value
		   FROM '.PERSON_SPR_TABLE.' b
	  LEFT JOIN '.PERSON_SPR_USER_TABLE.' a ON a.id_user = ? AND b.id = a.id_spr
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' d ON d.id_reference = b.id AND d.table_key = '.$multi_lang->TableKey(PERSON_SPR_TABLE).' 
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' c ON c.id_reference = a.id_value AND c.table_key = '.$multi_lang->TableKey(PERSON_SPR_VALUE_TABLE).' 
		  WHERE LENGTH(b.name) > 0
		  ORDER BY b.sorter, a.id_spr, value';
		
	$rs = $dbconn->Execute($strSQL, array($id_user));
	
	$i = 0;
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$data['personal'][$i]['name'] = $row['sprname'];
		$data['personal'][$i]['value'] = $row['value'];
		$rs->MoveNext(); $i++;
	}
	
	$smarty->assign('data_4', $data);
	
	return;
}


function MyPortraitTable()
{
	global $smarty, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$profile_percent = new Percent($id_user);
	$data['complete'] = $profile_percent->GetSectionPercent(4);
	
	// multi-lang tables
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	$strSQL =
		'SELECT b.id AS id_spr, a.id_value, d.'.$field_name.' AS sprname, c.'.$field_name.' AS value
		   FROM '.PORTRAIT_SPR_TABLE.' b
	  LEFT JOIN '.PORTRAIT_SPR_USER_TABLE.' a ON a.id_user = ? AND b.id = a.id_spr
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' d ON d.id_reference = b.id AND d.table_key = '.$multi_lang->TableKey(PORTRAIT_SPR_TABLE).' 
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' c on c.id_reference = a.id_value AND c.table_key = '.$multi_lang->TableKey(PORTRAIT_SPR_VALUE_TABLE).' 
		  WHERE LENGTH(b.name) > 0
	   ORDER BY b.sorter, b.id, value';
		
	$rs = $dbconn->Execute($strSQL, array($id_user));
	
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
	global $lang, $smarty, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$profile_percent = new Percent($id_user);
	$data['complete'] = $profile_percent->GetSectionPercent(5);
	
	// multi-lang tables
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	$strSQL =
		'SELECT b.id AS id_spr, a.id_value, d.'.$field_name.' AS sprname
		   FROM '.INTERESTS_SPR_TABLE.' b
	  LEFT JOIN '.INTERESTS_SPR_USER_TABLE.' a ON a.id_user = ? AND b.id = a.id_spr
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' d ON d.id_reference = b.id AND d.table_key = '.$multi_lang->TableKey(INTERESTS_SPR_TABLE).' 
		  WHERE LENGTH(b.name) > 0 AND a.id_value <> ""
	   ORDER BY sprname';
		
	$rs = $dbconn->Execute($strSQL, array($id_user));
	
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
	global $lang, $smarty, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$profile_percent = new Percent($id_user);
	$data_pers['complete'] = $profile_percent->GetSectionPercent(6);
	
	// multi-lang tables
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	$strSQL =
		'SELECT IF(a.id_weight = "0", "'.$lang['button']['not_important'].'", f.'.$field_name.') AS weight,
				IF(a.id_height = "0", "'.$lang['button']['not_important'].'", g.'.$field_name.') AS height,
				a.id_country, a.id_nationality, a.id_language
		   FROM '.USER_MATCH_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' f ON f.id_reference = a.id_weight AND f.table_key = "'.$multi_lang->TableKey(WEIGHT_SPR_TABLE).'"
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' g ON g.id_reference = a.id_height AND g.table_key = "'.$multi_lang->TableKey(HEIGHT_SPR_TABLE).'"
		  WHERE a.id_user = ?';
	
	$rs = $dbconn->Execute($strSQL, array($id_user));
	$row = $rs->GetRowAssoc(false);
	$data_pers['weight'] = $row['weight'];
	$data_pers['height'] = $row['height'];
	
	$country_str = $row['id_country'];
	$nation_str = $row['id_nationality'];
	$lang_str = $row['id_language'];
	
	if (strlen(trim($country_str)) > 0 && trim($country_str) != '0') {
		$country_arr = array();
		$rs = $dbconn->Execute('SELECT name AS country FROM '.COUNTRY_SPR_TABLE.' WHERE id IN ('.$country_str.') ORDER BY country');
		
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
	
	if (strlen(trim($nation_str)) > 0 && trim($nation_str) != '0')
	{
		$nation_arr = array();
		$rs = $dbconn->Execute(
			'SELECT b.'.$field_name.' AS nation
			   FROM '.NATION_SPR_TABLE.' a
		  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.id_reference = a.id AND b.table_key = "'.$multi_lang->TableKey(NATION_SPR_TABLE).'"
			  WHERE a.id IN ('.$nation_str.')
		   ORDER BY nation');
		
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			array_push($nation_arr, $row['nation']);
			$rs->MoveNext();
		}
		
		$data_pers['nationality_match'] = implode(',<br>', $nation_arr);
	}
	elseif( trim($nation_str) == '0')
	{
		$data_pers['nationality_match'] = $lang['button']['not_important'];
	}
	
	if (strlen(trim($lang_str))>0 && trim($lang_str) != '0')
	{
		$rs = $dbconn->Execute(
			'SELECT b.'.$field_name.' AS lang
			   FROM '.LANGUAGE_SPR_TABLE.' a
		  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.id_reference = a.id AND b.table_key = "'.$multi_lang->TableKey(LANGUAGE_SPR_TABLE).'"
			  WHERE a.id IN ('.$lang_str.')
		   ORDER BY lang');
		
		$lang_arr = array();
		
		while (!$rs->EOF) {
			$lang_arr[] = $rs->fields[0];
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
		'SELECT b.id, a.id_value,d.'.$field_name.' AS sprname, c.'.$field_name.' AS value
		   FROM '.DESCR_SPR_TABLE.' b
	  LEFT JOIN '.DESCR_SPR_MATCH_TABLE.' a ON a.id_user = ? AND b.id = a.id_spr
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' d ON d.id_reference = b.id AND d.table_key = "'.$multi_lang->TableKey(DESCR_SPR_TABLE).'"
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' c ON c.id_reference = a.id_value AND c.table_key = "'.$multi_lang->TableKey(DESCR_SPR_VALUE_TABLE).'"
		  WHERE LENGTH(b.name) > 0
	   ORDER BY b.sorter, value';
	
	$rs = $dbconn->Execute($strSQL, array($id_user));
	
	$i = 0;
	$id_spr = 0;
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		
		if ($row['id'] != $id_spr && $id_spr != 0) {
			$i++;
		}
		
		$data_pers['info'][$i]['name'] = $row['sprname'];
		
		if (!isset($data_pers['info'][$i]['value'])) {
			$data_pers['info'][$i]['value'] = '';
		}
		
		if ($row['id_value'] == 0) {
			$data_pers['info'][$i]['value'] = $lang['button']['not_important'];
		} elseif ($row['id_value'] != 0 && $data_pers['info'][$i]['value'] != $lang['button']['not_important']) {
			$data_pers['info'][$i]['value'] .= (strlen($data_pers['info'][$i]['value'])>0)?(', '.$row['value']):($row['value']);
		}
		
		$id_spr = $row['id'];
		
		$rs->MoveNext();
	}
	
	$smarty->assign('data_7', $data_pers);
	return;
}


function HisInterestsTable()
{
	global $smarty, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$profile_percent = new Percent($id_user);
	$data['complete'] = $profile_percent->GetSectionPercent(7);
	
	// multi-lang tables
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	$strSQL =
		'SELECT b.id, a.id_value, d.'.$field_name.' AS sprname
		   FROM '.INTERESTS_SPR_TABLE.' b
	  LEFT JOIN '.INTERESTS_SPR_MATCH_TABLE.' a ON b.id=a.id_spr AND a.id_user = ?
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' d on d.id_reference=b.id AND d.table_key='.$multi_lang->TableKey(INTERESTS_SPR_TABLE).' 
		  WHERE LENGTH(b.name) > 0 AND a.id_value <> ""
	   ORDER BY sprname';
		
	$rs = $dbconn->Execute($strSQL, array($id_user));
	
	$spr_name_arr = array();
	$spr_values_arr = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$spr_name_arr[$row['id']] = $row['sprname'];
		$spr_values_arr[$row['id']][$row['id_value']] = 1;
		$rs->MoveNext();
	}
	
	$i = 0;
	
	foreach ($spr_name_arr as $id_spr => $name_spr) {
		$data['interests'][$i]['name'] = $name_spr;
		$data['interests'][$i]['value_1'] = isset($spr_values_arr[$id_spr][1]) ? intval($spr_values_arr[$id_spr][1]) : 0;
		$data['interests'][$i]['value_2'] = isset($spr_values_arr[$id_spr][2]) ? intval($spr_values_arr[$id_spr][2]) : 0;
		$data['interests'][$i]['value_3'] = isset($spr_values_arr[$id_spr][3]) ? intval($spr_values_arr[$id_spr][3]) : 0;
		$i++;
	}
	
	$smarty->assign('data_8', $data);
	return;
}


function UploadTable($sub='', $action='', $data='')
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;
	global $IMG_TYPE_ARRAY, $IMG_EXT_ARRAY, $VIDEO_TYPE_ARRAY, $VIDEO_EXT_ARRAY, $AUDIO_TYPE_ARRAY, $AUDIO_EXT_ARRAY;
	global $EMBEDDED_AUDIO_TYPE_ARRAY, $EMBEDDED_AUDIO_EXT_ARRAY;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$settings = GetSiteSettings(array('photo_max_width', 'photo_max_height', 'photo_max_size',
		'icon_max_width', 'icon_max_height', 'icon_max_size', 'icons_folder', 'icons_default', 'icon_male_default', 'icon_female_default',
		'photos_folder', 'photos_default', 'audio_folder', 'audio_default', 'video_folder', 'video_default', 'video_max_size',
		'audio_max_size', 'use_image_resize', 'album_icon', 'video_album_icon', 'audio_album_icon', 'use_ffmpeg', 'use_embedded_audio'));
	
	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];
	
	$page = isset($_REQUEST['page']) ? (int) $_REQUEST['page'] : 1;
	
	if ($page <= 0) {
		$page = 1;
	}
	
	if ($sub == '') {
		$sub = 8;
	}
	
	$data['form_sub_page'] = $sub;
	
	$data['form_act'] = $action;
	
	if ($sub == 7)
	{
		// icon
		$data['max_file_size_bytes']	= getFileSizeFromString($settings['icon_max_size']);
		$data['max_file_size_string']	= $settings['icon_max_size'];
		$data['file_exts']				= '*.' . implode('; *.', $IMG_EXT_ARRAY);
		$data['file_types']				= implode('; ', $IMG_TYPE_ARRAY);
		
		if ($settings['use_image_resize']) {
			$data['icon_upload_comment'] = str_replace('[size]', $settings['icon_max_size'], $lang['confirm']['icon_upload_resize']);
		} else {
			$data['icon_upload_comment'] = str_replace('[size]', $settings['icon_max_size'], $lang['confirm']['icon_upload']);
			$data['icon_upload_comment'] = str_replace('[width]', $settings['icon_max_width'], $data['icon_upload_comment']);
			$data['icon_upload_comment'] = str_replace('[height]', $settings['icon_max_height'], $data['icon_upload_comment']);
		}
		
		$rs = $dbconn->Execute('SELECT icon_path, icon_path_temp, gender FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
		
		$file = strlen($rs->fields[0]) ? $rs->fields[0] : $rs->fields[1];
		
		$path = $config['site_path'].$settings['icons_folder'].'/'.$file;
		$big_path = $config['site_path'].$settings['icons_folder'].'/big_'.$file;
		
		if (file_exists($big_path) && strlen($file) > 0) {
			$data['icon_path'] = '.'.$settings['icons_folder'].'/big_'.$file;
			$data['icon_del_link'] = './myprofile.php?sel=upload_del&type_upload=icon';
		} elseif (file_exists($path) && strlen($file) > 0) {
			$data['icon_path'] = '.'.$settings['icons_folder'].'/'.$file;
			$data['icon_del_link'] = './myprofile.php?sel=upload_del&type_upload=icon';
		} else {
			$file = $default_photos[$rs->fields[2]];
			$data['icon_path'] = '.'.$settings['icons_folder'].'/'.$file;
		}
		
		$smarty->assign('data_9', $data);
		return;
	}
	
	switch ($sub)
	{
		case 9: // audio
			$album_page['upload_type'] = 'a';
			$album_page['album_type'] = '2';
			$album_page['_album_icon'] = $config['site_root'].$settings['audio_folder'].'/'.$settings['audio_album_icon'];
			$album_page['limit'] = GetItemLimit('audio_per_album');
		break;
			
		case 10: // video
			$album_page['upload_type'] = 'v';
			$album_page['album_type'] = '3';
			$album_page['_album_icon'] = $config['site_root'].$settings['video_folder'].'/'.$settings['video_album_icon'];
			$album_page['limit'] = GetItemLimit('video_per_album');
		break;
			
		case 8: // photo
		default:
			$album_page['upload_type'] = 'f';
			$album_page['album_type'] = '1';
			$album_page['_album_icon'] = $config['site_root'].$settings['photos_folder'].'/'.$settings['album_icon'];
			$album_page['limit'] = GetItemLimit('photo_per_album');
		break;
	}
	
	//get information about selected album type
	$album_page['items_count'] = $dbconn->GetOne(
		'SELECT COUNT(*) FROM '.USER_UPLOAD_TABLE.' WHERE id_user = ? AND upload_type = ?',
		array($id_user, $album_page['upload_type']));
	
	$num_records = $dbconn->GetOne(
		'SELECT COUNT(*) FROM '.USER_ALBUMS.' WHERE id_user = ? AND album_type = ?',
		array($id_user, $album_page['upload_type']));
	
	$album_page['album_count'] = $num_records;
	
	$album_page['show_more_album_link'] = ($album_page['album_count'] > 0) ? 1 : 0;
	
	switch ($action)
	{
		case 'create_album':
			$album_page['show_create_link'] = 0;
			$album_page['show_create_form'] = 1;
			$album_page['show_photos'] = 0;
		break;
			
		case 'edit_album':
			$data['id_album'] = isset($data['id_album']) ? (int) $data['id_album'] : (int) $_GET['id_album'];
			
			if ($data['id_album'] > 0){
				$rs = $dbconn->Execute(
					'SELECT title, description, allow
					   FROM '.USER_ALBUMS.'
					  WHERE id = ? AND album_type = ?',
					array($data['id_album'], $album_page['upload_type']));
				
				$data['album_title'] = stripslashes($rs->fields[0]);
				$data['album_description'] = stripslashes($rs->fields[1]);
				$data['album_upload_allow'] = $rs->fields[2];
				$data['edit'] = 1;
			}
			
			$album_page['show_create_link'] = 0;
			$album_page['show_create_form'] = 1;
			$album_page['show_photos'] = 0;
		break;
			
		case 'browse_album':
			$album_page['show_create_form'] = 0;
			$album_page['show_create_link'] = 0;
			$album_page['show_album_items'] = 1;
			
			$data['id_album'] = (isset($data['id_album']) && (int) $data['id_album']) ? (int) $data['id_album'] : (int) $_GET['id_album'];
			
			if ($data['id_album'] > 0) {
				$rs = $dbconn->Execute(
					'SELECT title, description
					   FROM '.USER_ALBUMS.'
					  WHERE id = ? AND album_type = ?',
					array($data['id_album'], $album_page['upload_type']));
				
				$data['album_title'] = stripslashes($rs->fields[0]);
				$data['album_description'] = stripslashes($rs->fields[1]);
			}
			
			$num_records = $dbconn->GetOne(
				'SELECT COUNT(*)
				   FROM '.USER_UPLOAD_TABLE.'
				  WHERE id_user = ? AND id_album = ? AND upload_type = ?',
				  array($id_user, $data['id_album'], $album_page['upload_type']));
			
			$data['num_items'] = $num_records;
			
			if ($album_page['limit'] == 0 || $num_records < $album_page['limit'])
			{
				$album_page['show_add_item_link'] = 1;
			}
			else
			{
				$group_name = stripslashes($dbconn->GetOne(
					'SELECT g.name
					   FROM '.GROUPS_TABLE.' g
				 INNER JOIN '.USER_GROUP_TABLE.' ug ON g.id = ug.id_group
					  WHERE ug.id_user = ?',
					  array($id_user)));
				$album_page['show_add_item_link'] = 0;
				$album_page['show_add_item_link_err'] = $lang['err']['album_items_limit_1'].$group_name.$lang['err']['album_items_limit_2'];
			}
			
			// max file size for uplod form and upload comment
			switch ($album_page['upload_type'])
			{
				case 'f':
					$data['max_file_size_bytes']	= getFileSizeFromString($settings['photo_max_size']);
					$data['max_file_size_string']	= $settings['photo_max_size'];
					$data['file_exts']				= '*.' . implode('; *.', $IMG_EXT_ARRAY);
					$data['file_types']				= implode('; ', $IMG_TYPE_ARRAY);
					if ($settings['use_image_resize']) {
						$data['photo_comment'] = str_replace('[size]', $settings['photo_max_size'], $lang['confirm']['photo_upload_resize']);
					} else {
						$data['photo_comment'] = str_replace('[size]', $settings['photo_max_size'], $lang['confirm']['photo_upload']);
						$data['photo_comment'] = str_replace('[width]', $settings['photo_max_width'], $data['photo_comment']);
						$data['photo_comment'] = str_replace('[height]', $settings['photo_max_height'], $data['photo_comment']);
					}
				break;
				
				case 'a':
					$data['max_file_size_bytes']	= getFileSizeFromString($settings['audio_max_size']);
					$data['max_file_size_string']	= $settings['audio_max_size'];
					$data['file_exts']				= '*.' . implode('; *.', $settings['use_embedded_audio'] ? $EMBEDDED_AUDIO_EXT_ARRAY : $AUDIO_EXT_ARRAY);
					$data['file_types']				= implode('; ', $settings['use_embedded_audio'] ? $EMBEDDED_AUDIO_TYPE_ARRAY : $AUDIO_TYPE_ARRAY);
					$data['audio_comment']			= str_replace('[size]', $settings['audio_max_size'], $lang['confirm']['audio_upload']);
					$data['embedded_audio']			= $settings['use_embedded_audio'];
				break;
				
				case 'v':
					$data['max_file_size_bytes']	= getFileSizeFromString($settings['video_max_size']);
					$data['max_file_size_string']	= $settings['video_max_size'];
					$data['file_exts']				= '*.' . implode('; *.', $VIDEO_EXT_ARRAY);
					$data['file_types']				= implode('; ', $VIDEO_TYPE_ARRAY);
					$data['video_comment']			= str_replace('[size]', $settings['video_max_size'], $lang['confirm']['video_upload']);
				break;
			}
			
			if ($num_records > 0)
			{
				$lim_min = ($page-1) * $config_index['search_numpage'];
				$lim_max = (int) $config_index['search_numpage'];
				
				$rs = $dbconn->Execute(
					'SELECT id, upload_path, allow, user_comment, is_gallary, id_gallery
					   FROM '.USER_UPLOAD_TABLE.'
					  WHERE id_user = ? AND id_album = ? AND upload_type = ?
				   ORDER BY id
					  LIMIT '.$lim_min.', '.$lim_max,
					array($id_user, $data['id_album'], $album_page['upload_type']));
				
				$_upload = array();
				$i = 0;
				
				while (!$rs->EOF) {
					$row = $rs->GetRowAssoc(false);
					$_upload[$i]['id']			= $row['id'];
					$_upload[$i]['file_path']	= $row['upload_path'];
					$_upload[$i]['allow']		= $row['allow'];
					$_upload[$i]['user_comment']= stripslashes($row['user_comment']);
					$_upload[$i]['is_gallary']	= $row['is_gallary'];
					$_upload[$i]['id_gallery']	= $row['id_gallery'];
					$rs->MoveNext();
					$i++;
				}
				
				if ($album_page['upload_type'] == 'f')
				{
					// photo
					$images_obj = new Images($dbconn);

					$len = count($_upload);
					
					for ($i = 0; $i < $len; $i++) {
						$item = $_upload[$i];
						
						$path = $config['site_path'].$settings['photos_folder'].'/'.$item['file_path'];
						$thumb_path = $config['site_path'].$settings['photos_folder'].'/thumb_'.$item['file_path'];
						
						if (file_exists($thumb_path) && strlen($item['file_path']) > 0) {
							$_upload[$i]['thumb_file'] = '.'.$settings['photos_folder'].'/thumb_'.$item['file_path'];
						}
						
						if (file_exists($path) && !file_exists($thumb_path) && strlen($item['file_path']) > 0) {
							$_upload[$i]['thumb_file'] = '.'.$settings['photos_folder'].'/'.$item['file_path'];
							$_upload[$i]['sizes'] = $images_obj->GetResizeParametrsStr($path);
						}
						
						if (!file_exists($path) || !strlen($item['file_path'])) {
							$_upload[$i]['thumb_file'] = '.'.$settings['photos_folder'].'/'.$settings['photos_default'];
						}
						
						if (file_exists($path) || strlen($item['file_path'])) {
							$_upload[$i]['crop_link']		= './include/imagecrop/index.php?imageName='.$item['file_path'];
							$_upload[$i]['del_link']		= './myprofile.php?sel=upload_del&id_file='.$item['id'].'&type_upload=f';
							$_upload[$i]['view_link']		= './myprofile.php?sel=upload_view&id_file='.$item['id'].'&type_upload=f';
						}
					}
					
					$smarty->assign('photos', $_upload);
					
					$param = '&id_album='.$data['id_album'].'&';
					$smarty->assign('links_page', GetLinkArray($num_records, $page, $param, $config_index['search_numpage']));
				}
				elseif ($album_page['upload_type'] == 'a')
				{
					// audio
					$len = count($_upload);
					
					for ($i = 0; $i < $len; $i++) {
						$item = $_upload[$i];
						
						$path = $config['site_path'].$settings['audio_folder'].'/'.$item['file_path'];
						
						if (file_exists($path) && strlen($item['file_path']) > 0) {
							$_upload[$i]['file_path']	= '.'.$settings['audio_folder'].'/'.$item['file_path'];
							$_upload[$i]['view_link']	= './myprofile.php?sel=upload_view&id_file='.$item['id'].'&type_upload=a';
							$_upload[$i]['del_link']	= './myprofile.php?sel=upload_del&id_file='.$item['id'].'&type_upload=a';
						}
						
						$_upload[$i]['thumb_file'] = '.'.$settings['audio_folder'].'/'.$settings['audio_default'];
					}
					
					$smarty->assign('audios', $_upload);
					
					$param = '&id_album='.$data['id_album'].'&';
					$smarty->assign('links_page', GetLinkArray($num_records, $page, $param, $config_index['search_numpage']));
				}
				elseif ($album_page['upload_type'] == 'v')
				{
					// video
					$len = count($_upload);
					
					for ($i = 0; $i < $len; $i++) {
						$item = $_upload[$i];
						
						$path = $config['site_path'].$settings['video_folder'].'/'.$item['file_path'];
						
						if (file_exists($path) && strlen($item['file_path']) > 0) {
							$_upload[$i]['view_link']	= './myprofile.php?sel=upload_view&id_file='.$item['id'].'&type_upload=v';
							$_upload[$i]['del_link']	= './myprofile.php?sel=upload_del&id_file='.$item['id'].'&type_upload=v';
						}
						
						$new_file_name_arr = explode('.', $item['file_path']);
						$thumb_file = $new_file_name_arr[0].'1.jpg';
						
						if (file_exists($config['site_path'].$settings['video_folder'].'/'.$thumb_file)) {
							$_upload[$i]['thumb_file'] = $config['site_root'].$settings['video_folder'].'/'.$thumb_file; // $config['server'].
						} else {
							$_upload[$i]['thumb_file'] = $config['site_root'].$settings['video_folder'].'/'.$settings['video_default']; // $config['server'].
						}
					}
					
					$smarty->assign('videos', $_upload);
					
					$param = '&id_album='.$data['id_album'].'&';
					$smarty->assign('links_page', GetLinkArray($num_records, $page, $param, $config_index['search_numpage']));
				}
			}
		break;
		
		case 'album_list':
		default:
			$album_page['show_create_form'] = 0;
			$album_page['show_create_link'] = 0;
			$album_page['show_photos'] = 0;
			
			$lim_min = ($page-1) * $config_index['albums_num_page'];
			$lim_max = (int) $config_index['albums_num_page'];
			
			if ($num_records > 0)
			{
				$rs = $dbconn->Execute(
					'SELECT id, title, description, DATE_FORMAT(creation_date, "'.$config['date_format'].'") AS creation_date
					   FROM '.USER_ALBUMS.'
					  WHERE id_user = ? AND album_type = ?
					  LIMIT '.$lim_min.', '.$lim_max,
					array($id_user, $album_page['upload_type']));
				
				$i = 0;
				$_album = array();
				
				while (!$rs->EOF) {
					$row = $rs->GetRowAssoc(false);
					$_album[$i]['id'] = $row['id'];
					$_album[$i]['title'] = stripslashes($row['title']);
					$_album[$i]['description'] = stripslashes($row['description']);
					$_album[$i]['creation_date'] = $row['creation_date'];
					$_album[$i]['items_count'] = $dbconn->GetOne(
						'SELECT COUNT(*)
						   FROM '.USER_UPLOAD_TABLE.'
						  WHERE id_user = ? AND id_album = ? AND upload_type = ?',
						array($id_user, $_album[$i]['id'], $album_page['upload_type']));
					$rs->MoveNext();
					$i++;
				}
				
				$smarty->assign('_album', $_album);
				$param = '&';
				$smarty->assign('links_page', GetLinkArray($num_records, $page, $param, $config_index['albums_num_page']));
			}
			else
			{
				$album_page['show_create_link'] = 1;
			}
			
		break;
	}
	
	$smarty->assign('album_page', $album_page);
			
	$data['categories'] = GetGalleryCategories();
	$smarty->assign('data_9', $data);
	return;
}


function UploadDelete($act)
{
	global $lang;
	
	$id_file = (int) $_GET['id_file'];
	$type_upload = $_GET['type_upload'];
	
	//VP GALLARY_TABLE not exists in database
	/*
	$upload_path = $dbconn->GetOne('SELECT upload_path FROM '.USER_UPLOAD_TABLE.' WHERE id = ? AND is_gallary = "1"', array($id_file));
	if (!empty($upload_path)) {
		$dbconn->Execute('DELETE FROM '.GALLARY_TABLE.' WHERE id_user='.$id_user.' AND file_path="'.$upload_path.'"');
	}
	*/
	
	$data = array();
	
	$data['id_album'] = DeleteUploadedFiles($type_upload, $id_file);
	
	switch ($type_upload) {
		case 'a':
			$_sub = 9;
			$err = $lang['err']['audio_deleted'];
		break;
		case 'v':
			$_sub = 10;
			$err = $lang['err']['video_deleted'];
		break;
		case 'icon':
			$_sub = 7;
			$err = $lang['err']['icon_deleted'];
		break;
		case 'f':
		default:
			$_sub = 8;
			$err = $lang['err']['photo_deleted'];
		break;
	}
	
	//ListProfile(4, '', $err, $_sub, 'browse_album', $data);
	ListProfile(4, $act, $err, $_sub, 'browse_album', $data);
	
	return;
}


function UploadView($par='')
{
	global $smarty, $dbconn, $config, $user;
	
	IndexHomePage();
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$id_file = (int) $_GET['id_file'];
	$videoplay = isset($_POST['videoplay']) ? trim($_POST['videoplay']) : '';
	
	if ($videoplay) {
		$dbconn->Execute('UPDATE '.USERS_TABLE.' SET videoplay = ? WHERE id = ?', array($videoplay, $id_user));
	}
	
	if ($par == '')
	{
		$rs = $dbconn->Execute(
			'SELECT a.upload_path, a.upload_type, a.user_comment, a.id_album, b.videoplay
			   FROM '.USER_UPLOAD_TABLE.' a
		 INNER JOIN '.USERS_TABLE.' b ON a.id_user = b.id
			  WHERE a.id = ?',
			  array($id_file));
		
		$row = $rs->GetRowAssoc(false);
		
		$data['file_name'] = $row['upload_path'];
		$data['upload_type'] = $row['upload_type'];
		$data['user_comment'] = stripslashes($row['user_comment']);
		$data['id_album'] = $row['id_album'];
		$data['videoplay'] = $row['videoplay'];
		
		unset($row);
		
		switch ($data['upload_type'])
		{
			case 'f':
			
				$folder = GetSiteSettings('photos_folder');
				
				$strSQL =
					'SELECT id, upload_path, user_comment
					   FROM '.USER_UPLOAD_TABLE.'
					  WHERE id_album = ? AND id_user = ?
				   ORDER BY id ';
				$rs = $dbconn->Execute($strSQL, array($data['id_album'], $id_user));
				
				$i = 0;
				while (!$rs->EOF) {
					$row = $rs->GetRowAssoc(false);
					$data['photos'][$i]['id'] = $row['id'];
					if ($row['upload_path'] == $data['file_name']) {
						$data['photos'][$i]['sel'] = 1;
					}
					$data['photos'][$i]['user_comment'] = stripslashes($row['user_comment']);
					$data['photos'][$i]['thumb_file'] = $config['server'].$config['site_root'].$folder.'/thumb_'.$row['upload_path'];
					$data['photos'][$i]['file'] = $config['server'].$config['site_root'].$folder.'/'.$row['upload_path'];
					$data['photos'][$i]['sizes'] = getimagesize($config['site_path'].$folder.'/'.$row['upload_path']);
					$rs->MoveNext();
					$i++;
				}
				
				$sizes = getimagesize($config['site_path'].$folder.'/thumb_'.$data['file_name']);
				$smarty->assign('sizes', $sizes);
				
				$data['file_path'] = $config['server'].$config['site_root'].$folder.'/'.$data['file_name'];
				
			break;
				
			case 'a':
			
				$folder = GetSiteSettings('audio_folder');
			
				$data['file_path'] = $config['server'].$config['site_root'].$folder.'/'.$data['file_name'];
				
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
				
			break;
		}
		
		$smarty->assign('data', $data);
		$smarty->display(TrimSlash($config['index_theme_path']).'/myprofile_upload_view.tpl');
	}
	elseif ($par == 'ajax')
	{
		$folder = GetSiteSettings('photos_folder');
		
		$rs = $dbconn->Execute(
			'SELECT upload_path, user_comment
			   FROM '.USER_UPLOAD_TABLE.'
			  WHERE id = ? AND id_user = ?',
			  array($id_file, $id_user));
		$row = $rs->GetRowAssoc(false);
		
		$photo_path = $config['server'].$config['site_root'].$folder.'/'.$row['upload_path'];
		$user_comment = stripslashes($row['user_comment']);
		
		echo
			'<table cellpadding="0" cellspacing="0" align="center">
				<tr><td><img border="1" bordercolor="1" src="'.$photo_path.'" alt=""></td></tr>
				<tr><td align="center">'.$user_comment.'</td></tr>
			</table>';
	}
	
	exit;
}


function DeleteAlbum()
{
	global $dbconn, $config, $lang, $user;
	
	$id_user	= $user[ AUTH_ID_USER ];
	
	$id_album	= (int) $_GET['id_album'];
	$_sub		= (int) $_GET['sub'];
	
	if ($id_album < 1) {
		ListProfile(4, '', '', $_sub);
		return;
	}
	
	$rs = $dbconn->Execute('SELECT id, album_type FROM '.USER_ALBUMS.' WHERE id = ? AND id_user = ?', array($id_album, $id_user));
	
	if ($rs->fields[0])
	{
		$album_type = $rs->fields[1];
		
		switch ($album_type) {
			case 'f': $file_folder = GetSiteSettings('photos_folder'); break;
			case 'a': $file_folder = GetSiteSettings('audio_folder'); break;
			case 'v': $file_folder = GetSiteSettings('video_folder'); break;
			default: $file_folder = GetSiteSettings('photos_folder'); break;
		}
		
		$rs = $dbconn->Execute(
			'SELECT id, upload_path
			   FROM '.USER_UPLOAD_TABLE.' 
			  WHERE id_album = ? AND id_user = ? AND upload_type = ?',
			array($id_album, $id_user, $album_type));
		
		if ($rs->fields[0] > 0)
		{
			while (!$rs->EOF) {
				@unlink($config['site_path'].$file_folder.'/'.$rs->fields[1]);
				@unlink($config['site_path'].$file_folder.'/thumb_'.$rs->fields[1]);
				$rs->MoveNext();
			}
			
			$dbconn->Execute('DELETE FROM '.USER_UPLOAD_TABLE.' WHERE id_album = ? AND id_user = ? AND upload_type = ?',
				array($id_album, $id_user, $album_type));
		}
		
		$dbconn->Execute('DELETE FROM '.USER_ALBUMS.' WHERE id = ? AND id_user = ?', array($id_album, $id_user));
	}
	
	$err = $lang['err']['album_deleted'];
	ListProfile(4, '', $err, $_sub);
	return;
}


function SaveAlbum()
{
	global $dbconn, $lang, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$data['id_album']			= isset($_POST['id_album']) ? (int) $_POST['id_album'] : null;
	
	$data['album_type']			= $_POST['album_type'];
	$data['album_title']		= strip_tags(trim($_POST['album_title']));
	$data['album_description']	= strip_tags(trim($_POST['album_description']));
	$data['album_upload_allow']	= (int) $_POST['album_upload_allow'];
	
	$action = !empty($_POST['id_album']) ? 'edit_album' : 'create_album';
	
	switch ($data['album_type']) {
		case '2':
			$_type = 'a';
			$_sub = 9;
			$limit = GetItemLimit('audio_album_count');
		break;
		case '3':
			$_type = 'v';
			$_sub = 10;
			$limit = GetItemLimit('video_album_count');
		break;
		case '1':
		default:
			$_type = 'f';
			$_sub = 8;
			$limit = GetItemLimit('photo_album_count');
		break;
	}
	
	if (strlen($data['album_title']) == 0) {
		$err = $lang['errors']['empty_fields'];
		ListProfile(4, '', $err, $_sub, $action, $data);
		return;
	}
	
	if (BadWordsCont($data['album_title'], 6)) {
		$err = $lang['err']['badword_finding_6_1'];
		ListProfile(4, '', $err, $_sub, $action, $data);
		return;
	}
	
	if (check_filter($data['album_title'])) {
		$err = $lang['err']['info_finding_1'];
		ListProfile(4, '', $err, $_sub, $action, $data);
		return;
	}
	
	if (BadWordsCont($data['album_description'], 6)) {
		$err = $lang['err']['badword_finding_6_2'];
		ListProfile(4, '', $err, $_sub, $action, $data);
		return;
	}
	
	if (check_filter($data['album_description'])) {
		$err = $lang['err']['info_finding_1'];
		ListProfile(4, '', $err, $_sub, $action, $data);
		return;
	}
	
	if ($data['id_album'])
	{
		// update
		$dbconn->Execute(
			'UPDATE '.USER_ALBUMS.'
				SET title = ?, description = ?, allow = ?
			  WHERE id = ? AND id_user = ? AND album_type = ?',
			array($data['album_title'], $data['album_description'], (string)$data['album_upload_allow'], $data['id_album'], $id_user, $_type));
		
		$dbconn->Execute(
			'UPDATE '.USER_UPLOAD_TABLE.'
				SET allow = ?
			  WHERE id_album = ? AND id_user = ?',
			array((string)$data['album_upload_allow'], $data['id_album'], $id_user));
		
		$err = $lang['err']['album_saved'];
	}
	else
	{
		// insert
		$cnt = $dbconn->GetOne('SELECT COUNT(*) FROM '.USER_ALBUMS.' WHERE id_user = ? AND album_type = ?', array($id_user, $_type));
		
		if (($limit > 0) && ($cnt >= $limit)) {
			$err = $lang['err']['album_limit'];
			ListProfile(4, '', $err, $_sub, 'album_list');
			return;
		}
		
		$dbconn->Execute(
			'INSERT INTO '.USER_ALBUMS.' (id_user, title, description, album_type, allow, creation_date) VALUES (?, ?, ?, ?, ?, NOW())',
			array($id_user, $data['album_title'], $data['album_description'], $_type, (string)$data['album_upload_allow'])
		);
		
		$err = $lang['err']['album_created'];
	}
	
	ListProfile(4, '', $err, $_sub, 'album_list');
}


function TagsTable()
{
	global $smarty, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$data = array();
	
	$rs = $dbconn->Execute('SELECT DISTINCT tag, COUNT(id) AS tag_count FROM '.TAGS_TABLE.' WHERE id_user = ? GROUP BY tag ORDER BY tag', array($id_user));
	
	$i = 0;
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$data['user_tags'][$i]['tag'] = stripslashes($row['tag']);
		$data['user_tags'][$i]['count'] = $row['tag_count'];
		$data['user_tags'][$i]['searchlink'] = './quick_search.php?sel=search_tag&tag='.$data['user_tags'][$i]['tag'];
		$rs->MoveNext();
		$i++;
	}
	
	$rs = $dbconn->Execute('SELECT id, tag FROM '.TAGS_TABLE.' WHERE id_user = ? AND id_creator = ? ORDER BY tag', array($id_user, $id_user));
	
	$i = 0;
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$data['my_tags'][$i]['tag'] = stripslashes($row['tag']);
		$data['my_tags'][$i]['dellink'] = './myprofile.php?sel=deltag&tag='.$row['id'];
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('data_10', $data);
	return;
}

function AddTag()
{
	global $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$tag_arr = explode(' ', $_POST['tag']);
	
	foreach ($tag_arr as $value) {
		$tag = trim($value);
		if ($tag) {
			$rs = $dbconn->Execute('SELECT id FROM '.TAGS_TABLE.' WHERE id_creator = ? AND id_user = ? AND tag = ?', array($id_user, $id_user, $tag));
			if (!$rs->fields[0]) {
				$dbconn->Execute(
					'INSERT INTO '.TAGS_TABLE.' SET id_creator = ?, id_user = ?, create_date = NOW(), tag = ?',
						array($id_user, $id_user, $tag));
			}
		}
	}
	
	ListProfile(6);
	return;
}

function DeleteTag()
{
	global $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$id_tag = intval($_GET['tag']);
	
	$dbconn->Execute('DELETE FROM '.TAGS_TABLE.' WHERE id_creator = ? AND id_user = ? AND id = ?'.$id_tag, array($id_user, $id_user, $id_tag));
	
	ListProfile(6);
	
	return;
}

function GetGalleryCategories()
{
	global $dbconn;
	
	// multi-lang tables
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	$strSQL =
		'SELECT DISTINCT a.id, b.'.$field_name.' AS name
		   FROM '.GALLERY_CATEGORIES_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = "'.$multi_lang->TableKey(GALLERY_CATEGORIES_TABLE).'" AND b.id_reference = a.id
	   GROUP BY a.id
	   ORDER BY name ';
	$rs = $dbconn->Execute($strSQL);
	
	$categories = array();
	$i = 0;
	
	while (!$rs->EOF) {
		$categories[$i]['id'] = $rs->fields[0];
		$categories[$i]['name'] = $rs->fields[1];
		$rs->MoveNext();
		$i++;
	}
	
	return $categories;
}


function AcceptCouple()
{
	$err = CoupleAction(intval($_GET['id']), 'accept');
	ListProfile(1, '', $err);
	return;
}


function GetItemLimit($perm_name)
{
	global $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$limit = (int) $dbconn->GetOne(
		'SELECT gp.permission_count
		   FROM '.GROUPS_PERMISSIONS_TABLE.' gp
	 INNER JOIN '.PERMISSIONS_TABLE.' p ON gp.id_permission = p.id
	 INNER JOIN '.USER_GROUP_TABLE.' ug ON gp.id_group = ug.id_group
		  WHERE ug.id_user = ? AND p.permission_name = ?',
		array($id_user, $perm_name));
	
	return ($limit > 0) ? $limit : 0;
}


function SubmitApplication()
{
	global $lang, $smarty, $dbconn, $user, $config;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$data = GetPersonalData();
	
	// checking already submitted
	if (!empty($data['mm_application_submit'])) {
		ListProfile(1, '', $lang['err']['application_already_submitted']);
		exit;
	}
	
	// checking photo
	if ($data['icon_path_temp'] != '') {
		ListProfile(1, '', $lang['err']['temp_upload_photo']);
		exit;
	}
	
	if ($data['icon_path'] == '') {
		ListProfile(1, '', $lang['err']['application_photo_missing']);
		exit;
	}
	
	// checking profile data
	// disabled 2012-05-11 to simplify signup process
	if (USE_PROFILE_EDIT_IN_SIGNUP_SANDBOX)
	{
		$mandatory = array();
		
		include './customize/profile_switchboard.php';
		
		$err = '';
		$err_field = array();
		
		if (!strlen($data['login'])) {
			$err .= $lang['users']['nick'] . ', ';
			$err_field['login'] = 1;
		}
		
		if ($mandatory['fname'] & SB_EDIT && !strlen($data['fname'])) {
			$err .= $lang['users']['fname'] . ', ';
			$err_field['fname'] = 1;
		}
		
		if ($mandatory['sname'] & SB_EDIT && !strlen($data['sname'])) {
			$err .= $lang['users']['sname'] . ', ';
			$err_field['sname'] = 1;
		}
		
		if ($mandatory['mm_nickname'] & SB_EDIT && !strlen($data['mm_nickname'])) {
			$err .= $lang['users']['mm_nickname'] . ', ';
			$err_field['mm_nickname'] = 1;
		}
		
		if ($mandatory['gender'] & SB_EDIT && !$data['gender']) {
			$err .= $lang['users']['gender'] . ', ';
			$err_field['gender'] = 1;
		}
		
		if ($mandatory['mm_marital_status'] & SB_EDIT && !$data['mm_marital_status']) {
			$err .= $lang['users']['mm_marital_status'] . ', ';
			$err_field['mm_marital_status'] = 1;
		}
		
		if ($mandatory['mm_place_of_birth'] & SB_EDIT && !strlen($data['mm_place_of_birth'])) {
			$err .= $lang['users']['mm_place_of_birth'] . ', ';
			$err_field['mm_place_of_birth'] = 1;
		}
		
		if ($mandatory['id_nationality'] & SB_EDIT && !$data['id_nationality']) {
			$err .= $lang['users']['nationality'] . ', ';
			$err_field['id_nationality'] = 1;
		}
		
		if ($mandatory['mm_id_number'] & SB_EDIT && $data['gender'] == 2 && !strlen($data['mm_id_number'])) {
			$err .= $lang['users']['mm_id_number'] . ', ';
			$err_field['mm_id_number'] = 1;
		}
		
		if ($mandatory['email'] & SB_EDIT && !strlen($data['email'])) {
			$err .= $lang['users']['email'] . ', ';
			$err_field['email'] = 1;
		}
		
		if ($mandatory['mm_contact_phone_number'] & SB_EDIT && !strlen($data['mm_contact_phone_number'])) {
			$err .= $lang['users']['mm_contact_phone_number'] . ', ';
			$err_field['mm_contact_phone_number'] = 1;
		}
		
		if ($mandatory['mm_contact_mobile_number'] & SB_EDIT && !strlen($data['mm_contact_mobile_number'])) {
			$err .= $lang['users']['mm_contact_mobile_number'] . ', ';
			$err_field['mm_contact_mobile_number'] = 1;
		}
		
		if ($mandatory['id_country'] & SB_EDIT && !$data['id_country']) {
			$err .= $lang['users']['country'] . ', ';
			$err_field['id_country'] = 1;
		}
		
		if ($mandatory['id_region'] & SB_EDIT && !$data['id_region']) {
			$err .= $lang['users']['region'] . ', ';
			$err_field['id_region'] = 1;
		}
		
		if ($mandatory['id_city'] & SB_EDIT && !$data['id_city']) {
			$err .= $lang['users']['city'] . ', ';
			$err_field['id_city'] = 1;
		}
		
		if ($mandatory['mm_city'] & SB_EDIT && !strlen($data['mm_city'])) {
			$err .= $lang['users']['city'] . ', ';
			$err_field['mm_city'] = 1;
		}
		
		if ($mandatory['zipcode'] & SB_EDIT && !strlen($data['zipcode'])) {
			$err .= $lang['users']['zipcode'] . ', ';
			$err_field['zipcode'] = 1;
		}
		
		if ($mandatory['mm_address_1'] & SB_EDIT && !strlen($data['mm_address_1'])) {
			$err .= $lang['users']['mm_address_1'] . ', ';
			$err_field['mm_address_1'] = 1;
		}
		
		if ($mandatory['mm_address_2'] & SB_EDIT && !strlen($data['mm_address_2'])) {
			$err .= $lang['users']['mm_address_2'] . ', ';
			$err_field['mm_address_2'] = 1;
		}
		
		if ($mandatory['id_language_1'] & SB_EDIT && $data['id_language_1'] <= 0) {
			$err .= $lang['users']['language'] . ', ';
			$err_field['id_language_1'] = 1;
		}
		
		if ($mandatory['id_language_2'] & SB_EDIT && $data['id_language_2'] <= 0) {
			$err .= $lang['users']['language'] . ', ';
			$err_field['id_language_2'] = 1;
		}
		
		if ($mandatory['id_language_3'] & SB_EDIT && $data['id_language_3'] <= 0) {
			$err .= $lang['users']['language'] . ', ';
			$err_field['id_language_3'] = 1;
		}
		
		if ($mandatory['mm_level_of_english'] & SB_EDIT && !$data['mm_level_of_english']) {
			$err .= $lang['users']['mm_level_of_english'] . ', ';
			$err_field['mm_level_of_english'] = 1;
		}
		
		if ($mandatory['site_language'] & SB_EDIT && !$data['site_language']) {
			$err .= $lang['users']['site_language'] . ', ';
			$err_field['site_language'] = 1;
		}
		
		if ($mandatory['mm_employment_status'] & SB_EDIT && !$data['mm_employment_status']) {
			$err .= $lang['users']['mm_employment_status'] . ', ';
			$err_field['mm_employment_status'] = 1;
		}
		
		if ($mandatory['mm_business_name'] & SB_EDIT && $data['mm_employment_status'] == 2 && !strlen($data['mm_business_name'])) {
			$err .= $lang['users']['mm_business_name'] . ', ';
			$err_field['mm_business_name'] = 1;
		}
		
		if ($mandatory['mm_employer_name'] & SB_EDIT && $data['mm_employment_status'] == 3 && !strlen($data['mm_employer_name'])) {
			$err .= $lang['users']['mm_employer_name'] . ', ';
			$err_field['mm_employer_name'] = 1;
		}
		
		if ($mandatory['mm_job_position'] & SB_EDIT && $data['mm_employment_status'] != 1 && !strlen($data['mm_job_position'])) {
			$err .= $lang['users']['mm_job_position'] . ', ';
			$err_field['mm_job_position'] = 1;
		}
		
		if ($mandatory['mm_work_address'] & SB_EDIT && $data['mm_employment_status'] != 1 && !strlen($data['mm_work_address'])) {
			$err .= $lang['users']['mm_work_address'] . ', ';
			$err_field['mm_work_address'] = 1;
		}
		
		if ($mandatory['mm_work_phone_number'] & SB_EDIT && $data['mm_employment_status'] != 1 && !strlen($data['mm_work_phone_number'])) {
			$err .= $lang['users']['mm_work_phone_number'] . ', ';
			$err_field['mm_work_phone_number'] = 1;
		}
		
		if ($mandatory['mm_ref_1_first_name'] & SB_EDIT && !strlen($data['mm_ref_1_first_name'])) {
			$err .= $lang['profile_head']['reference_1'].' '.$lang['users']['fname'] . ', ';
			$err_field['mm_ref_1_first_name'] = 1;
		}
		
		if ($mandatory['mm_ref_1_last_name'] & SB_EDIT && !strlen($data['mm_ref_1_last_name'])) {
			$err .= $lang['profile_head']['reference_1'].' '.$lang['users']['sname'] . ', ';
			$err_field['mm_ref_1_last_name'] = 1;
		}
		
		if ($mandatory['mm_ref_1_relationship'] & SB_EDIT && !strlen($data['mm_ref_1_relationship'])) {
			$err .= $lang['profile_head']['reference_1'].' '.$lang['users']['mm_reference_relationship'].', ';
			$err_field['mm_ref_1_relationship'] = 1;
		}
		
		if ($mandatory['mm_ref_1_phone_number'] & SB_EDIT && !strlen($data['mm_ref_1_phone_number'])) {
			$err .= $lang['profile_head']['reference_1'].' '.$lang['users']['mm_reference_phone_number'].', ';
			$err_field['mm_ref_1_phone_number'] = 1;
		}
		
		if ($mandatory['mm_ref_2_first_name'] & SB_EDIT && !strlen($data['mm_ref_2_first_name'])) {
			$err .= $lang['profile_head']['reference_2'].' '.$lang['users']['fname'] . ', ';
			$err_field['mm_ref_2_first_name'] = 1;
		}
		
		if ($mandatory['mm_ref_2_last_name'] & SB_EDIT && !strlen($data['mm_ref_2_last_name'])) {
			$err .= $lang['profile_head']['reference_2'].' '.$lang['users']['sname'] . ', ';
			$err_field['mm_ref_2_last_name'] = 1;
		}
		
		if ($mandatory['mm_ref_2_relationship'] & SB_EDIT && !strlen($data['mm_ref_2_relationship'])) {
			$err .= $lang['profile_head']['reference_2'].' '.$lang['users']['mm_reference_relationship'].', ';
			$err_field['mm_ref_2_relationship'] = 1;
		}
		
		if ($mandatory['mm_ref_2_phone_number'] & SB_EDIT && !strlen($data['mm_ref_2_phone_number'])) {
			$err .= $lang['profile_head']['reference_2'].' '.$lang['users']['mm_reference_phone_number'].', ';
			$err_field['mm_ref_2_phone_number'] = 1;
		}
		
		if ($err) {
			$err = $lang['err']['application_details_missing'].'<br/><br/>'.trim($err, ', ');
			$smarty->assign('err_field', $err_field);
			EditProfile(1, $err);
			exit;
		}
	}
	
	// checking confirmation
	if ($data['confirm'] != '1') {
		CheckConfirmation();
		exit;
	}
	
	// upgrade to Trial
	
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2'));
	
	$id_period = ($user[ AUTH_GENDER ] == GENDER_MALE) ? MM_TRIAL_GUY_PERIOD_ID : MM_TRIAL_LADY_PERIOD_ID;
	
	// get product data
	$rs = $dbconn->Execute('SELECT id_group, cost, cost_2 FROM '.GROUP_PERIOD_TABLE.' WHERE id = ?', array($id_period));
	$row = $rs->GetRowAssoc(false);
	
	$id_group	= (int) $row['id_group'];
	$cost		= $row['cost'];
	$cost_2		= $row['cost_2'];
	
	$product_name = getProductName($id_period);
	
	$currency = $cost_2 ? $settings['site_unit_costunit_2'] : $settings['site_unit_costunit'];
	
	// insert billing request record
	// RS: I do not see any need to create this record, but let's do it for now
	$dbconn->Execute(
		'INSERT INTO '.BILLING_REQUESTS_TABLE.' SET
			id_user = ?, amount = "0", currency = ?, cost = ?, cost_2 = ?, id_group = ?, id_product = ?, date_send = NOW(),
			status = "approve", paysystem = "user_account", recurring = "0", product_name = ?',
		array($id_user, $currency, $cost, $cost_2, $id_group, $id_period, $product_name));
	
	// insert billing entry record
	// RS: this record is necessary, we need it for the user's membership history
	$dbconn->Execute(
		'INSERT INTO '.BILLING_ENTRY_TABLE.' SET
			id_user = ?, amount = "0", currency = ?, cost = ?, id_group = ?,
			id_product = ?, date_entry = NOW(), entry_type = "user_account", txn_type = "site_credits", product_name = ?',
		array($id_user, $currency, $cost, $id_group, $id_period, $product_name));
	
	// assign user group
	AssignUserGroup($id_user, $id_period);
	
	// GA_TRACKING
	$_SESSION['ga_event_code'] = 'trialstarted';
	
	// send emails to user and admin
	ApplicationSubmit_User_Message();
	ApplicationSubmit_Admin_Message();
	
	//SH update application submit date + Verify user automatically. (No need to verify by admin)
	$dbconn->Execute('UPDATE '.USERS_TABLE.' SET mm_application_submit = NOW(), status = "1" WHERE id = ?', array($id_user));
	
	// UPDATE TLDF STATUS IN SOLVE360
	if (SOLVE360_CONNECTION) {
		require_once $config['site_path'].'/include/Solve360Service.php';
		$solve360Service = new Solve360Service(SOLVE360_USER, SOLVE360_TOKEN);
		
		$solve360 = array();
		require $config['site_path'].'/include/Solve360CustomFields.php';
		
		$contactData = array(
			$solve360['TLDF Status'] => 'Good',
		);
		
		if($user[ AUTH_ID_SOLVE360 ]) {
			$contact = $solve360Service->editContact($user[ AUTH_ID_SOLVE360 ], $contactData);
			#var_dump($contact); exit;
			if (isset($contact->errors)) {
				$subject = 'Error while updating TLDF Status after application submission';
				solve360_api_error($contact, $subject, $user[ AUTH_LOGIN ]);
			}
		}
		// maybe add contact if not found
	}
	
	//SH user completed registration - adding credit points to user account as award
	AddCreditPoints($id_user, POINT_USER_REGISTER, PG_INITIAL_CREDIT_POINT_BONUS);
	
	// homepage redirection
	echo '<script>location.href="./homepage.php?act=welcome";</script>';
	exit;
	
	// display success message (we show now whenever page loads)
	#$video_id  = ($user[ AUTH_GENDER ] == GENDER_MALE) ? '2A9FF901-D8CD-21B5-65C6B15CB02972CD' : '2A76E244-E13C-F005-7825780974D01CAA';
	#$video_url = $config['server'].$config['site_root'].'/video_player.php?vid='.$video_id;
	#$success_msg = $lang['err']['application_submit_success'];
	#$success_msg = str_replace('[VIDEO_LINK]', $video_url, $success_msg);
	#ListProfile(1, '', $success_msg);
	
	// old code
	#ListProfile(1);
	#exit;
}


function CheckConfirmation()
{
	global $dbconn, $user, $lang;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$rs = $dbconn->Execute('SELECT confirm, mm_application_submit FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
	$row = $rs->GetRowAssoc(false);
	;
	if ($row['confirm'] == '1')
	{
		if (!empty($row['mm_application_submit'])) {
			ListProfile(1, '', $lang['err']['email_already_confirmed_and_submitted']);
			exit;
		}
		
		ListProfile(1, '', $lang['err']['email_already_confirmed']);
		exit;
	}
	
	EditProfile(10);
	exit;
}


function SendConfirmation()
{
	global $lang, $config, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$settings = GetSiteSettings(array('use_registration_approve', 'use_registration_confirmation'));
	
	// language
	$site_lang = !empty($user[ AUTH_SITE_LANGUAGE ]) ? $user[ AUTH_SITE_LANGUAGE ] : $config['default_lang'];
	
	// include language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	// create login token
	$token = CreateToken($id_user);
	
	// content array
	$content				= array();
	$content['login']	 	= $user[ AUTH_LOGIN ];
	$content['fname']		= $user[ AUTH_FNAME ];
	$content['sname']		= $user[ AUTH_SNAME ];
	$content['date_birthday_formatted'] = formatDateSql($user[ AUTH_DATE_BIRTHDAY ]);
	$content['email']		= $user[ AUTH_EMAIL ];
	
	#$urls['video_eng']		= $server['root'].'/video_player.php?vid=2A746A01-CB90-2F1D-6AB5C06DB7C95262';
	#$urls['video_thai']	= $server['root'].'/video_player.php?vid=2A7973DC-C133-0DE8-40EC3DD6D5E2D089';
	
	$content['urls']		= GetUserEmailLinks();
	
	if ($settings['use_registration_confirmation']) {
		$content['confirm_link'] = $config['server'].$config['site_root'].'/confirm.php?id='.$id_user.'&login_id='.$id_user.'&token='.$token;
	}
	
	#$content['freecd_link']	= $config['server'].$config['site_root'].'/request_info.php?id='.$id_user;
	#$content['approve']		= $settings['use_registration_approve'] ? 1 : 0;
	
	// gender suffix
	$suffix = ($user[ AUTH_GENDER ] == GENDER_MALE) ? '_e' : '_t';
	
	// subject
	$subject = str_replace('[name]', $user[ AUTH_FNAME ], $lang_mail['registration'.$suffix]['subject']);
	
	// recipient
	$name_to = trim($user[ AUTH_FNAME ].' '.$user[ AUTH_SNAME ]);
	
	// send external message
	SendMail($site_lang, $user[ AUTH_EMAIL ], $config['site_email'], $subject, $content,
		'mail_registration_user', null, $name_to, '', 'registration', $user[ AUTH_GENDER ]);
	
	ListProfile(1, '', $lang['err']['email_confirm_sent']);
	
	exit;
}


function ApplicationSubmit_User_Message()
{
	global $config, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	// set globals for SendMail() and include mail language file
	$site_lang = !empty($user[ AUTH_SITE_LANGUAGE ]) ? $user[ AUTH_SITE_LANGUAGE ] : $config['default_lang'];
	
	// include language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	// content array
	$content			= array();
	
	$content['login']	= $user[ AUTH_LOGIN ];
	$content['fname']	= $user[ AUTH_FNAME ];
	$content['sname']	= $user[ AUTH_SNAME ];
	$content['nick']	= $user[ AUTH_FNAME ];
	$content['email']	= $user[ AUTH_EMAIL ];
	
	$content['urls']	= GetUserEmailLinks();
	
	//$urls['video_eng']	= $server['root'].'/video_player.php?vid=2A9FF901-D8CD-21B5-65C6B15CB02972CD';
	//$urls['video_thai']	= $server['root'].'/video_player.php?vid=2A76E244-E13C-F005-7825780974D01CAA';
	
	// gender suffix
	$suffix = ($user[ AUTH_GENDER ] == GENDER_MALE) ? '_e' : '_t';
	
	// subject
	$subject = str_replace('[name]', $user[ AUTH_FNAME ], $lang_mail['application_submit'.$suffix]['subject']);
	
	$name_to = trim($user[ AUTH_FNAME ].' '.$user[ AUTH_SNAME ]);
	
	// send external message
	SendMail($site_lang, $user[ AUTH_EMAIL ], $config['site_email'], $subject, $content, 'mail_application_submit_user', null,
		$name_to, '', 'application_submit', $user[ AUTH_GENDER ]);
	
	// create internal message
	$body = $lang_mail['generic'.$suffix]['hello'].' '.$content['fname'].',<br><br>';
	$body.= $lang_mail['application_submit'.$suffix]['message'].'<br><br>';
	$body.= $lang_mail['generic'.$suffix]['admin_regards'];
	
	$dbconn->Execute(
		'INSERT INTO '.MAILBOX_TABLE.' SET
				id_to = ?, id_from = ?, subject = ?, body = ?, was_read = "0", deleted_to = "0", deleted_from = "0", date_creation = NOW()',
		array($id_user, ID_ADMIN, $subject, $body));
	
	return;
}


function ApplicationSubmit_Admin_Message()
{
	global $config, $dbconn, $user;
	
	// content array
	$content				= array();
	
	$content['login']		= $user[ AUTH_LOGIN ];
	$content['fname']		= $user[ AUTH_FNAME ];
	$content['sname']		= $user[ AUTH_SNAME ];
	$content['email']		= $user[ AUTH_EMAIL ];
	$content['datetime']	= date('d-M-y H:i:s', time());
	
	$content['urls']		= GetUserEmailLinks();
	
	// language
	$site_lang = $config['default_lang'];
	
	// include language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	// subject
	$subject = str_replace('[login]', $content['login'], $lang_mail['application_submit_admin']['subject']);
	
	// recipient
	if (REDIRECT_ADMIN_EMAIL && !IS_LIVE_SERVER) {
		$email_to = REDIRECT_ADMIN_EMAIL_TO;
	} else {
		$email_to = $config['site_email'];
	}
	
	// send external message
	SendMail($site_lang, $email_to, $config['site_email'], $subject, $content,
		'mail_application_submit_admin', null, '', '', 'application_submit_admin');
	
	return;
}


function GetPersonalData()
{
	global $lang, $dbconn, $user;
	
	// current user
	$id_user = $user[ AUTH_ID_USER ];
	
	$strSQL =
		'SELECT u.login, u.fname, u.sname, u.status, u.email, u.date_birthday, u.gender, u.couple, u.couple_user, 
				u.id_country, u.id_city, u.id_region, u.zipcode, u.comment, u.headline, u.id_nationality, 
				u.id_language_1, u.id_language_2, u.id_language_3, u.id_weight, u.id_height, u.root_user, u.guest_user, 
				u.site_language, u.phone, u.icon_path, u.icon_path_temp, u.confirm,
				u.mm_nickname, u.mm_id_number, u.mm_contact_phone_number, u.mm_contact_mobile_number, u.mm_marital_status, u.mm_place_of_birth, 
				u.mm_city, u.mm_address_1, u.mm_address_2,
				u.mm_level_of_english, u.mm_employment_status, u.mm_business_name, u.mm_employer_name, 
				u.mm_job_position, u.mm_work_address, u.mm_work_phone_number,
				u.mm_ref_1_first_name, u.mm_ref_1_last_name, u.mm_ref_1_relationship, u.mm_ref_1_phone_number, 
				u.mm_ref_2_first_name, u.mm_ref_2_last_name, u.mm_ref_2_relationship, u.mm_ref_2_phone_number,
				u.mm_application_submit,
				um.gender AS search_gender, um.couple AS search_couple, um.age_min, um.age_max, um.id_relationship 
		   FROM '.USERS_TABLE.' AS u
	  LEFT JOIN '.USER_MATCH_TABLE.' AS um ON um.id_user = u.id
		  WHERE u.id = ?';
	
	$rs = $dbconn->Execute($strSQL, array($id_user));
	$row = $rs->GetRowAssoc(false);
	
	// login
	$data['login'] = $row['login'];
	
	//confirm
	$data['confirm']				= $row['confirm'];
	
	// personal info
	$data['fname']					= stripslashes($row['fname']);
	$data['sname']					= stripslashes($row['sname']);
	$data['icon_path']				= stripslashes($row['icon_path']);
	$data['icon_path_temp']			= stripslashes($row['icon_path_temp']);
	$data['mm_nickname']			= stripslashes($row['mm_nickname']);
	$data['gender']					= $row['gender'];
	$data['mm_marital_status']		= $row['mm_marital_status'];
	$data['date_birthday']			= $row['date_birthday'];
	$data['age']					= AgeFromBDate($row['date_birthday']);
	$data['mm_place_of_birth']		= stripslashes($row['mm_place_of_birth']);
	$data['id_nationality']			= $row['id_nationality'];
	$data['mm_id_number']			= stripslashes($row['mm_id_number']);
	
	// contact info
	$data['email']					= $row['email'];
	$data['mm_contact_phone_number'] = stripslashes($row['mm_contact_phone_number']);
	$data['mm_contact_mobile_number'] = stripslashes($row['mm_contact_mobile_number']);
	$data['phone']					= $row['phone'];
	
	// address info
	$data['id_country']				= $row['id_country'];
	$data['id_region']				= $row['id_region'];
	$data['id_city']				= $row['id_city'];
	$data['mm_city']				= stripslashes($row['mm_city']);
	$data['zipcode']				= stripslashes($row['zipcode']);
	$data['mm_address_1']			= stripslashes($row['mm_address_1']);
	$data['mm_address_2']			= stripslashes($row['mm_address_2']);
	
	// language info
	$data['id_language_1']			= $row['id_language_1'];
	$data['id_language_2']			= $row['id_language_2'];
	$data['id_language_3']			= $row['id_language_3'];
	$data['mm_level_of_english']	= $row['mm_level_of_english'];
	$data['site_language']			= $row['site_language'];
	
	// employment info
	$data['mm_employment_status']	= $row['mm_employment_status'];
	$data['mm_business_name']		= stripslashes($row['mm_business_name']);
	$data['mm_employer_name']		= stripslashes($row['mm_employer_name']);
	$data['mm_job_position']		= stripslashes($row['mm_job_position']);
	$data['mm_work_address']		= stripslashes($row['mm_work_address']);
	$data['mm_work_phone_number']	= stripslashes($row['mm_work_phone_number']);
	
	// references
	$data['mm_ref_1_first_name']	= stripslashes($row['mm_ref_1_first_name']);
	$data['mm_ref_1_last_name']		= stripslashes($row['mm_ref_1_last_name']);
	$data['mm_ref_1_relationship']	= stripslashes($row['mm_ref_1_relationship']);
	$data['mm_ref_1_phone_number']	= stripslashes($row['mm_ref_1_phone_number']);
	$data['mm_ref_2_first_name']	= stripslashes($row['mm_ref_2_first_name']);
	$data['mm_ref_2_last_name']		= stripslashes($row['mm_ref_2_last_name']);
	$data['mm_ref_2_relationship']	= stripslashes($row['mm_ref_2_relationship']);
	$data['mm_ref_2_phone_number']	= stripslashes($row['mm_ref_2_phone_number']);
	
	// application status
	$data['mm_application_submit']	= $row['mm_application_submit'];
	
	// headline
	$data['headline']				= stripslashes($row['headline']);
	
	// weight and height
	$data['id_weight']				= $row['id_weight'];
	$data['id_height']				= $row['id_height'];
	
	// looking for
	$data['search_gender']			= $row['search_gender'];
	$data['search_couple']			= $row['search_couple'];
	$data['min_age']				= $row['age_min'];
	$data['max_age']				= $row['age_max'];
	$data['id_relationship']		= $row['id_relationship'];
	
	// couple
	$data['couple']					= $row['couple'];
	$data['couple_user']			= $row['couple_user'];
	
	if ($row['couple_user']) {
		$rs_couple = $dbconn->Execute('SELECT login, gender, date_birthday, couple_user FROM '.USERS_TABLE.' WHERE id = ?', array($row['couple_user']));
		$data['couple_login']		= $rs_couple->fields[0];
		$data['couple_link']		= 'viewprofile.php?id='.$row['couple_user'];
		$data['couple_gender']		= $lang['gender'][$rs_couple->fields[1]];
		$data['couple_age']			= AgeFromBDate($rs_couple->fields[2]);
		$data['couple_accept']		= $rs_couple->fields[3] == $id_user ? 1 : 0;
	}
	
	return $data;
}

?>