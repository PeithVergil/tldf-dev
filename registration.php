<?php
/**
* Registration pages file
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
include './include/class.lang.php';
include './include/class.percent.php';
include './include/class.images.php';
include './include/class.phpmailer.php';
include './include/functions_mail.php';
include './include/functions_users.php';
include './include/functions_newsletter.php';
include './include/functions_affiliate.php';
include './include/class.ip_info.php';

// authentication
$user = auth_index_user();

if (empty($user) || $user == 'err' || empty($user[ AUTH_ID_USER ])) {
	header('location: '.$config['site_root'].'/index.php');
	exit;
}

// check guest
// (not applicable in registration process)

// check group, period, expiration
RefreshAccount();

// check status
// (not applicable in registration process)

// check permissions
// (not applicable in registration process)

// alerts and statistics
// (not applicable in registration process)

// active menu item
$smarty->assign('sub_menu_num', '');

if (isset($_SESSION['return_to_view']['type']) && ($_SESSION['return_to_view']['type'] == 'viewprofile' || $_SESSION['return_to_view']['type'] == 'gallary')) {
	lastViewed();
}

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

if ($user[ AUTH_IS_APPLICANT ]) {
	header('Location: '.$config['site_root'].'/myprofile.php');
	exit;
}

if ($user[ AUTH_GUEST ]) {
	if ($sel != '' && $sel != '1' && $sel != 'save_1') {
		header('Location: '.$config['site_root'].'/index.php');
		exit;
	}
}

// dispatcher
switch ($sel) {
	case '1': EditProfile(1); break;	// Personal Data
#	case '2': EditProfile(2); break;	// UploadTable (upload icon)
#	case '3': EditProfile(3); break;	// Description
#	case '4': EditProfile(4); break;	// Annonce
#	case '5': EditProfile(5); break;
#	case '6': EditProfile(6); break;
#	case '7': EditProfile(7); break;
#	case '8': EditProfile(8); break;
	
	case 'save_1': SaveProfile(1); break;	// SavePersonalRegistrationForm
#	case 'save_2': SaveProfile(2); break;	// SaveDescriptionForm
#	case 'save_3': SaveProfile(3); break;	// SaveAnnonceForm
#	case 'save_4': SaveProfile(4); break;	// SaveMyPersonalityForm
#	case 'save_5': SaveProfile(5); break;	// SaveMyPortraitForm
#	case 'save_6': SaveProfile(6); break;	// SaveMyInterestsForm
#	case 'save_7': SaveProfile(7); break;	// SaveMyCriteria
#	case 'save_8': SaveProfile(8); break;	// SaveHisInterests
#	case 'save_9': SaveProfile(9); break;	// read upload data, check upload, SaveUploadForm
	
#	case 'upload_del': IconDelete(2); break;
#	case 'upload_view': UploadView(); break;
	
	default: EditProfile(1);
}


function EditProfile($num, $err = '')
{
	global $lang, $config, $smarty, $user;
	
	// big registration form has been replaced by form on index page
	echo '<script>location.href="./index.php";</script>';
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$file_name = 'registration.php';

#	if ($num == '1')
#	{
		// redirect non-guest
		if (!$user[ AUTH_GUEST ]) {
			echo '<script>location.href="./homepage.php";</script>';
			return;
		}
		
		$form = PersonalRegistrationForm();
		$form['header'] = $lang['subsection']['registration'];
		$form['next_link'] = $file_name.'?sel=2';
		$form['site_link'] = 'homepage.php';
#	}
#	elseif ($num == '2')
#	{
#		$form['task'] = 1;
#		$form = IconUpload('registration');
#		$form['header'] = $lang['subsection']['upload'];
#		$form['next_link'] = $file_name.'?sel=3';
#		$form['site_link'] = 'homepage.php';
#	}
#	elseif ($num == '3')
#	{
#		$form = DescriptionForm();
#		$form['header'] = $lang['subsection']['description'];
#		$form['next_link'] = $file_name.'?sel=4';
#		$form['site_link'] = 'homepage.php';
#	}
#	elseif ($num == '4')
#	{
#		$form = AnnonceForm();
#		$form['header'] = $lang['subsection']['notice'];
#		$form['next_link'] = $file_name.'?sel=5';
#		$form['site_link'] = 'homepage.php';
#	}
#	elseif ($num == '5')
#	{
#		$form = MyPersonalityForm();
#		$form['header'] = $lang['subsection']['personal'];
#		$form['next_link'] = $file_name.'?sel=6';
#		$form['site_link'] = 'homepage.php';
#	}
#	elseif ($num == '6')
#	{
#		$form = MyPortraitForm();
#		$form['header'] = $lang['subsection']['portreit'];
#		$form['next_link'] = $file_name.'?sel=7';
#		$form['site_link'] = 'homepage.php';
#	}
#	elseif ($num == '7')
#	{
#		$form = MyInterestsForm();
#		$form['header'] = $lang['subsection']['interest'].' ( '.$lang['interests_opt'][1].' / '.$lang['interests_opt'][2].' / '.$lang['interests_opt'][3].' )';
#		$form['next_link'] = $file_name.'?sel=8';
#		$form['site_link'] = 'homepage.php';
#	}
#	elseif ($num == '8')
#	{
#		$form = MyCriteriaForm();
#		$form['header'] = $lang['subsection']['criteria'];
#		$form['next_link'] = $file_name.'?sel=9';
#		$form['site_link'] = 'homepage.php';
#	}
#	elseif ($num == '9')
#	{
#		$form = HisInterestsForm();
#		$form['header'] = $lang['subsection']['match_interest'].' ( '.$lang['interests_opt'][1].' / '.$lang['interests_opt'][2].' / '.$lang['interests_opt'][3].' )';
#		$form['next_link'] = $file_name.'?sel=10';
#		$form['site_link'] = 'homepage.php';
#	}
#	elseif ($num == '10')
#	{
#		header('location: ./homepage.php');
#		exit;
#	}
	
	$form['err'] = $err;
	$form['num'] = $num;
	$form['action'] = $file_name;
	$smarty->assign('form', $form);
	
	$smarty->assign('header', $lang['profile']);
	$smarty->assign('alt', $lang['alt']);
	
	$smarty->display(TrimSlash($config['index_theme_path']).'/registration_edit.tpl');
}


function SaveProfile($num)
{
#	$profile_percent = new Percent($user[ AUTH_ID_USER ]);
#
#	if ($num == '1')
#	{
		$err = SavePersonalRegistrationForm();
		
		if ($err) {
			EditProfile(1, $err);
			exit;
		}
		
		// $_POST contains sanitized form input
		$data = $_POST;
		
		// submitting user data to newsletter service
		if ($data['gender'] == 2)
		{
			//Creating array for GetResponse (Lady user)
			$resData['webform_id']			= '38741';
			$resData['name']				= html_entity_decode($data['fname']);
			$resData['custom_FirstName']	= html_entity_decode($data['fname']);
			$resData['custom_LastName']		= html_entity_decode($data['sname']);
			$resData['email']				= html_entity_decode($data['email']);
		}
		else
		{
			//Creating array for GetResponse (Guy user)
			$resData['webform_id']			= '41168';
			$resData['name']				= html_entity_decode($data['fname']);
			//$resData['custom_FirstName']	= html_entity_decode($data['fname']);
			$resData['custom_LastName']		= html_entity_decode($data['sname']);
			$resData['email']				= html_entity_decode($data['email']);

			// old aweber code
			/*
			//Guy user
			$currentURL = $config['server'].$_SERVER['PHP_SELF'];

			//Creating array for AWeber
			$awebData['meta_web_form_id']	= "693543256";
			$awebData['meta_split_id']		= "";
			$awebData['listname']			= "tldf-register";
			$awebData['redirect']			= $currentURL;
			$awebData['meta_adtracking']	= "Thai_Lady_Date_Finder_Register";
			$awebData['meta_message']		= "1";
			$awebData['meta_required']		= "name (awf_first),name (awf_last),email";
			$awebData['meta_tooltip']		= "";
			$awebData['name (awf_first)']	= $data['fname'];
			$awebData['name (awf_last)']	= $data['sname'];
			$awebData['email']				= $data['email'];
			#$awebData['custom Country']	= $data['id_nationality'];

			submitToAweber($awebData);
			*/
		}

		submitToGetResponse($resData);

		//RS: we can't use header('Location: ...') because headers are already sent by curl
		//SH echo '<script>location.href="./myprofile.php?vdo=y";</script>';
		echo '<script>location.href="./myprofile.php";</script>';
		exit;
		
		// we stay on page 1
		// $page = 2;
#	}
#	elseif ($num == '2')
#	{
#		$data = $_POST;
#		$info = $_POST['info'];
#		$spr = $_POST['spr'];
#		$err = SaveDescriptionForm($profile_percent, $data, $spr, $info);
#		$page = 4;
#	}
#	elseif ($num == '3')
#	{
#		$data = $_POST;
#		$err = SaveAnnonceForm($data);
#		$page = 5;
#	}
#	elseif ($num == '4')
#	{
#		$data = $_POST;
#		$personal = $_POST['personal'];
#		$spr = $_POST['p_spr'];
#		$err = SaveMyPersonalityForm($profile_percent, $data, $spr, $personal);
#		$page = 6;
#	}
#	elseif ($num == '5')
#	{
#		$portrait = $_POST['portrait']; 
#		$spr = $_POST['port_spr'];
#		$err = SaveMyPortraitForm($profile_percent, $spr, $portrait);
#		$page = 7;
#	}
#	elseif ($num == '6')
#	{
#		$interests = $_POST['interests'];
#		$spr = $_POST['int_spr'];
#		$err = SaveMyInterestsForm($profile_percent, $spr, $interests);
#		$page = 8;
#	}
#	elseif ($num == '7')
#	{
#		$data = $_POST;
#		$info = $_POST['info'];
#		$spr = $_POST['spr'];
#		$err = SaveMyCriteriaForm($profile_percent, $data, $spr, $info);
#		$page = 9;
#	}
#	elseif ($num == '8')
#	{
#		$interests = $_POST['interests'];
#		$spr = $_POST['int_spr'];
#		$err = SaveHisInterestsForm($profile_percent, $spr, $interests);
#		$page = 10;
#	}
#	elseif ($num == '9')
#	{
#		$upload = $_FILES['upload'];
#		$upload_type = $_POST['upload_type'];
#		$upload_allow = isset($_POST['upload_allow']) ? $_POST['upload_allow'] : '';
#		$id_file = isset($_POST['id_file']) ? intval($_POST['id_file']) : 0;
#		$user_comment = isset($_POST['user_comment']) ? stripn(FormFilter($_POST['user_comment'])) : '';
#		
#		if ($temp = BadWordsCont($user_comment, 6)) {
#			$err = $temp;
#		} elseif (check_filter($user_comment)) {
#			$err = $lang['err']['info_finding_1'];
#		} else {
#			if ($id_file && !$upload['size']) {
#				$strSQL =
#					'UPDATE '.USER_UPLOAD_TABLE.' SET allow="'.$upload_allow.'", user_comment="'.addslashes($user_comment).'" 
#						WHERE id_user='.$id_user.' AND id='.$id_file;
#				$dbconn->Execute($strSQL);
#			} else {
#				$err = SaveUploadForm($upload, $upload_type, $upload_allow, $id_file, $user_comment);
#			}
#		}
#		
#		$page = 2;
#		
#		if ($err) {
#			EditProfile(2, $err);
#			return;
#		}
#	}
#	
#	if ($err) {
#		EditProfile($num, $err);
#	} else {
#		EditProfile($page);
#	}
	
	return;
}

/*
function UploadTable()
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$file_name = 'registration.php';
	
	$settings = GetSiteSettings(array('icon_max_width', 'icon_max_height', 'icon_max_size', 'icons_folder',
		'icon_male_default', 'icon_female_default', 'use_image_resize'));
	
	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];
	
	$i = 0;
	
	$rs = $dbconn->Execute('SELECT id, upload_path, allow, upload_type, user_comment FROM '.USER_UPLOAD_TABLE.' 
		WHERE id_user='.$id_user.' ORDER BY upload_type, id');
	
	while (!$rs->EOF)
	{
		$row = $rs->GetRowAssoc(false);
		$db_upload[$row['upload_type']][$i]['id'] = $row['id'];
		$db_upload[$row['upload_type']][$i]['file_path'] = $row['upload_path'];
		$db_upload[$row['upload_type']][$i]['allow'] = $row['allow'];
		$db_upload[$row['upload_type']][$i]['user_comment'] = stripslashes($row['user_comment']);
		$rs->MoveNext();
		$i++;
	}
	
	// icon
	if ($settings['use_image_resize'])
	{
		$data['icon_comment'] = str_replace('[size]', $settings['icon_max_size'], $lang['confirm']['icon_upload_resize']);
	}
	else
	{
		$data['icon_comment'] = str_replace('[size]', $settings['icon_max_size'], $lang['confirm']['icon_upload']);
		$data['icon_comment'] = str_replace('[width]', $settings['icon_max_width'], $data['icon_comment']);
		$data['icon_comment'] = str_replace('[height]', $settings['icon_max_height'], $data['icon_comment']);
	}
	
	$rs = $dbconn->Execute('SELECT big_icon_path, icon_path, icon_path_temp FROM '.USERS_TABLE.' WHERE id='.$id_user);
	
	$file = strlen($rs->fields[0]) ? $rs->fields[0] : (strlen($rs->fields[1]) ? $rs->fields[1] : $rs->fields[2]);
	
	$path = $config['site_path'].$settings['icons_folder'].'/'.$file;
	
	if (file_exists($path) && strlen($file))
	{
		$data['icon_path'] = '.'.$settings['icons_folder'].'/'.$file;
		$data['icon_del_link'] = './'.$file_name.'?sel=upload_del&amp;type_upload=icon';
	}
	else
	{
		$file = $default_photos[$user[ AUTH_GENDER ]];
		$data['icon_path'] = '.'.$settings['icons_folder'].'/'.$file;
	}
	
	$data['gender'] = $user[ AUTH_GENDER ];
	
	$smarty->assign('data', $data);
	return;
}

function UploadDelete()
{
	global $smarty, $dbconn, $config, $config_admin, $lang, $user;
	
	$type_upload = $_GET['type_upload'];
	
	DeleteUploadedFiles($type_upload);
	EditProfile(2);
	
	return;
}
*/
/*
function UploadView()
{
	global $smarty, $dbconn, $config, $config_admin, $lang, $user;
	
	IndexHomePage();
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$id_file = $_GET['id_file'];
	
	$rs = $dbconn->Execute('SELECT a.upload_path, b.login, a.upload_type, a.user_comment FROM '.USER_UPLOAD_TABLE.' a LEFT JOIN '.USERS_TABLE.' b on a.id_user = b.id WHERE a.id="'.$id_file.'"');
	
	$data['upload_type'] = $rs->fields[2];
	$data['user_comment'] = stripslashes($rs->fields[3]);
	$data['file_name'] = $rs->fields[0];
	
	switch ($data['upload_type'])
	{
		case 'f': $folder = GetSiteSettings('photos_folder'); break;
		case 'a': $folder = GetSiteSettings('audio_folder'); break;
		case 'v': $folder = GetSiteSettings('video_folder'); break;
	}
	
	$data['file_path'] = $config['server'].$config['site_root'].$folder.'/'.$data['file_name'];
	
	$smarty->assign('data', $data);
	
	$smarty->display(TrimSlash($config['index_theme_path']).'/myprofile_upload_view.tpl');
	
	exit;
}
*/

?>