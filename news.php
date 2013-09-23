<?php
/**
* Site news listing
*
* @package DatingPro
* @subpackage User Mode
**/

include './include/config.php';
include './common.php';
include './include/config_index.php';
include './include/functions_auth.php';
include './include/functions_index.php';
include './include/class.news.php';
include './include/class.lang.php';				// registration
include './include/class.percent.php';			// registration
include './include/class.ip_info.php';			// registration
include './include/class.phpmailer.php';		// registration
include './include/functions_mail.php';			// registration
include './include/functions_users.php';		// registration
include './include/functions_newsletter.php';	// registration
include './include/functions_affiliate.php';	// registration

// authentication
$user = auth_index_user();

if (empty($user) || $user == 'err' || empty($user[ AUTH_ID_USER ])) {
	header('location: '.$config['site_root'].'/index.php');
	exit;
}

// check guest
// (public access)

// check group, period, expiration
RefreshAccount();

// check status
// (public access)

// check permissions
// (public access)

// alerts and statistics
if (!$user[ AUTH_GUEST ]) {
	GetAlertsMessage();
	SetModuleStatistic(GetRightModulePath(__FILE__));
}

// active menu item
$smarty->assign('sub_menu_num', '');

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

// dispatcher
switch ($sel) {
	case 'save_1':
		SaveProfile();
	break;
	default:
		SearchTable();
	break;
}

exit;


function SearchTable($err = '')
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$file_name = 'news.php';	

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';
	if (strval($page) == '' || strval($page) == '0') {
		$page = 1;
	} else {
		$page = intval($page);
	}

	// settings
	$settings = GetSiteSettings(array('newsimage_folder'));

	// number of entries
	$rs = $dbconn->Execute('SELECT COUNT(id) FROM '.NEWS_TABLE.' WHERE status = "1"');
	$num_records = $rs->fields[0];
	
	// page
	$lim_min = ($page - 1) * $config_index['news_numpage'];
	$lim_max = $config_index['news_numpage'];
	$limit_str = ' limit '.$lim_min.', '.$lim_max;
	if ($num_records > 0) {
		$news = GetLastNews($config_index['news_numpage'], $page);
		foreach ($news as $key => $n){
			// there is no image_path in the news table, so this does not make sense anyway
			if (isset($n['image_path'])) {
				$file_path = $config['site_path'].$settings['newsimage_folder'].'/'.$n['image_path'];
				if (strlen($n['image_path']) > 0 && file_exists($file_path)) {
					$news[$key]['image'] = $config['site_root'].$settings['newsimage_folder'].'/'.$n['image_path'];
				} else {
					$news[$key]['image'] = '';
				}
			}
		}
		
		$param = $file_name.'?';
		$smarty->assign('links', GetLinkArray($num_records, $page, $param, $config_index['news_numpage']));
		$smarty->assign('news', $news);
	}
	
	//RS signup form start
	if (isset($_SESSION['error'])) {
		$err		= $_SESSION['error'];
		$err_field	= $_SESSION['err_field'];
		$_POST		= $_SESSION['data'];
		unset($_SESSION['error']);
		unset($_SESSION['err_field']);
		unset($_SESSION['data']);
		$smarty->assign('err_field', $err_field);
	}
	
	$form = PersonalRegistrationForm();
	//RS signup form end
	
	$form['err'] = $err;
	$smarty->assign('form', $form);

	$smarty->assign('section', $lang['subsection']);
	$smarty->assign('header', $lang['homepage']);
	$smarty->assign('header_n', $lang['homepage']);
	$smarty->display(TrimSlash($config['index_theme_path']).'/news_table.tpl');
	exit;
}

function SaveProfile()
{
	//strip_magic_quotes_gpc();
	
	$err = SavePersonalRegistrationForm();
	
	if ($err) {
		$_SESSION['error'] = $err;
		$_SESSION['data']  = $_POST;
		header('Location: news.php');
		exit;
	}
	
	// $_POST contains sanitized form input
	$data = $_POST;
	
	// submit to newsletter service
	if ($data['gender'] == GENDER_FEMALE) {
		//Creating array for GetResponse (Lady user)
		$resData['webform_id']			= '38741';
		$resData['name']				= html_entity_decode($data['fname']);
		$resData['custom_FirstName']	= html_entity_decode($data['fname']);
		$resData['custom_LastName']		= html_entity_decode($data['sname']);
		$resData['email']				= html_entity_decode($data['email']);
	} else {
		//Creating array for GetResponse (Guy user)
		$resData['webform_id']			= '41168';
		$resData['name']				= html_entity_decode($data['fname']);
		//$resData['custom_FirstName']	= html_entity_decode($data['fname']);
		$resData['custom_LastName']		= html_entity_decode($data['sname']);
		$resData['email']				= html_entity_decode($data['email']);
	}
	
	submitToGetResponse($resData);
	
	// meetme: go straight to My Profile
	## SH echo '<script>location.href="./myprofile.php?vdo=y";</script>';
	## header('location: myprofile.php');
	echo '<script>location.href="./myprofile.php";</script>';
	exit;
	
	// old AWeber code
	/*
	{
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
		//$awebData['custom Country']		= $data['id_nationality'];
		
		//submitting form to aWeber
		submitToAweber($awebData);
	}
	*/
}

?>