<?php
/**
* How It Works
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

// active menu item
$smarty->assign('sub_menu_num', '2');

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');
$id  = isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? $_GET['id'] : 0);

// dispatcher
switch ($sel) {
	case 'video':	 ShowHowItWorksVideo($id); break;
	default:		 ListHowItWorks();
}

exit;


function ListHowItWorks($err='')
{
	global $config, $smarty, $user;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$form['err'] = $err;
	$smarty->assign('form', $form);
	$smarty->assign('VideoPopup', false);
	$smarty->display(TrimSlash($config['index_theme_path']).'/how_it_works.tpl');
	exit;
}

function ShowHowItWorksVideo($id)
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$data = GetHowItWorksVideo($id);
	
	$smarty->assign('data', $data);
	$smarty->assign('VideoPopup', true);
	$smarty->display(TrimSlash($config['index_theme_path']).'/how_it_works.tpl');
	exit;
}
?>