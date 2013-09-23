<?php
/**
* Default Video Player Page for External Videos
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

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

// dispatcher
switch ($sel) {
	default:
		VideoPlayerTable();
	break;
}

exit;


function VideoPlayerTable($err="")
{
	global $lang, $config, $smarty, $user;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$file_name = "video_player.php";
	
	$video_id = isset($_REQUEST['vid']) ? $_REQUEST['vid'] : '';
	
	// Thanks. 3 Jobs. English version		: 2A746A01-CB90-2F1D-6AB5C06DB7C95262
	// Thanks. 3 Jobs. Thai					: 2A7973DC-C133-0DE8-40EC3DD6D5E2D089
	// Wait. Checking your Details. English : 2A9FF901-D8CD-21B5-65C6B15CB02972CD
	// Wait. Checking Your Details. Thai	: 2A76E244-E13C-F005-7825780974D01CAA
	// Welcome. You're Confirmed.  English	: 38B9A8E6-C460-E5F2-C3A54AFB67015428
	// You're Confirmed. Thai				: 2A7E6EA4-EDBE-AD1A-806B4257C78011B3
	
	switch ($video_id) {
		case "2A746A01-CB90-2F1D-6AB5C06DB7C95262":
			$title_var = "signed_up_e";
		break;
		case "2A7973DC-C133-0DE8-40EC3DD6D5E2D089":
			$title_var = "signed_up_t";
		break;
		case "2A9FF901-D8CD-21B5-65C6B15CB02972CD":
			$title_var = "profile_submitted_e";
		break;
		case "2A76E244-E13C-F005-7825780974D01CAA":
			$title_var = "profile_submitted_t";
		break;
		case "38B9A8E6-C460-E5F2-C3A54AFB67015428":
			$title_var = "membership_live_e";
		break;
		case "2A7E6EA4-EDBE-AD1A-806B4257C78011B3":
			$title_var = "membership_live_t";
		break;
		default:
			$err = $lang["video_error"]["not_exists"];
	}
	
	if (!$err) {
		$form['video_id']	 = $video_id;
		$form['video_title'] = $lang["video_title"]["$title_var"];
	}
	
	$form['err'] = $err;
	$form['action'] = $file_name;
	$smarty->assign('form', $form);
	$smarty->display(TrimSlash($config["index_theme_path"])."/video_player.tpl");
	exit;
}
?>