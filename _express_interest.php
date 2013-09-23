<?php
/**
* Express Interest Page
*
* @package DatingPro
* @subpackage User Mode
**/

include "./include/config.php";
include "./common.php";
include "./include/config_index.php";
include "./include/functions_auth.php";
include "./include/functions_index.php";
include "./include/class.lang.php";
include './include/class.phpmailer.php';
include './include/functions_mail.php';

// authentication
$user = auth_index_user();

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
$smarty->assign('sub_menu_num', '9');

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

// dispatcher
switch ($sel) {
	//case 'send_request':
	//	ExpressInterestTable();
	//break;
	
	default:
		ExpressInterestTable();
	break;
}

exit;


function ExpressInterestTable($err='')
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$file_name = 'express_interest.php';
	
	$form['err'] = $err;
	$form['action'] = $file_name;
	$smarty->assign('form', $form);
	$smarty->display(TrimSlash($config["index_theme_path"])."/express_interest_table.tpl");
	exit;
}
?>