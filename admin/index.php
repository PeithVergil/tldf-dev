<?php

/**
 * Administration mode Index page (site statistics, login and logout functions)
 *
 * @package DatingPro
 * @subpackage Admin Mode
 **/

include '../include/config.php';
include '../common.php';
include '../include/config_admin.php';
include '../include/functions_auth.php';
include '../include/functions_admin.php';
include '../include/class.news.php';

CheckInstallFolder();

$auth = array();
$auth = auth_user();

if (is_array($auth) && $auth[ AUTH_ID_USER ] > 0 && $auth[4] != 1) {
	header('location: '.$config['server'].$config['site_root'].'/index.php');
	exit;
}

$smarty->assign('err', $lang['err']);

$sel		= isset($_REQUEST['sel']) ? $_REQUEST['sel'] : '';
$err		= isset($_REQUEST['err']) ? $_REQUEST['err'] : '';
$lang_type	= isset($_REQUEST['lang_type']) ? $_REQUEST['lang_type'] : '';

switch ($sel) {
	case 'save':
		SavePass();
	break;
	case 'clear':
		ClearPass();
	break;
	case 'logoff':
		LogoutUser();
	break;
	case 'usermode':
		GoToUserMode();
	break;
	default:
		if (is_array($auth) && $auth[ AUTH_ID_USER ] > 0) {
			IndexPage($lang_type, $err);
		} else {
			PermissionError($err);
		}
	break;
}

exit;


function SavePass()
{
	setcookie('login', $_POST['login'], time()+3600);
	echo (setcookie('pass', $_POST['pass'], time()+3600));
	echo $_COOKIE;
	LoginForm();
}

function ClearPass()
{
	setcookie('login', '', time()-3600);
	setcookie('pass', '', time()-3600);
	LoginForm();
}

function LogoutUser()
{
	setcookie('dp_login', '', time()-7200, '/');
	setcookie('dp_pass', '', time()-7200, '/');
	sess_delete(session_id());
	PermissionError();
	return;
}

function GoToUserMode()
{
	global $config;
	sess_delete(session_id());
	header('location: '.$config['server'].$config['site_root'].'/index.php');
	exit;
}

function IndexPage($lang_type = 'index', $err = '')
{
	global $smarty, $lang, $config, $dbconn;
	
	switch (intval($err)) {
		case '1':
			$err = $lang['err']['not_perm'];
		break;
		default:
			$err = '';
		break;
	}
	
	if (!$lang_type) {
		$lang_type = 'index';
	}
	
	AdminMainMenu($lang[$lang_type]);
	
	//Lady
	//
	//Lady- Signup
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_SIGNUP_LADY_ID;
	$lady_signup["now"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_SIGNUP_LADY_ID."
				AND (u.date_registration + INTERVAL 7 DAY) < NOW()";
	$lady_signup["seven_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_SIGNUP_LADY_ID."
				AND (u.date_registration + INTERVAL 30 DAY) < NOW()";
	$lady_signup["thirty_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_SIGNUP_LADY_ID;
	$lady_signup["all_time"] = intval($dbconn->GetOne($strSQL)) + intval($lady_signup["now"]);
	
	$stat["lady_signup"] = $lady_signup;
	
	//Lady- Trial Pending
	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_TRIAL_LADY_ID."
#				AND u.status = '0'";
#	$lady_trial_pending["now"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_TRIAL_LADY_ID."
#				AND (u.mm_application_submit + INTERVAL 7 DAY) < NOW() AND u.status = '0'";
#	$lady_trial_pending["seven_days"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_TRIAL_LADY_ID."
#				AND (u.mm_application_submit + INTERVAL 30 DAY) < NOW() AND u.status = '0'";
#	$lady_trial_pending["thirty_days"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_TRIAL_LADY_ID." AND status='0' ";
#	$lady_trial_pending["all_time"] = intval($dbconn->GetOne($strSQL)) + intval($lady_trial_pending["now"]);
#	
#	$stat["lady_trial_pending"] = $lady_trial_pending;
	
	//Lady- Trial
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_TRIAL_LADY_ID." AND u.status='1' ";
	$lady_trial["now"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_TRIAL_LADY_ID." AND u.status='1'
				AND (u.mm_application_submit + INTERVAL 7 DAY) < NOW()";
	$lady_trial["seven_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_TRIAL_LADY_ID." AND u.status='1'
				AND (u.mm_application_submit + INTERVAL 30 DAY) < NOW()";
	$lady_trial["thirty_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_TRIAL_LADY_ID." AND status='1' ";
	$lady_trial["all_time"] = intval($dbconn->GetOne($strSQL)) + intval($lady_trial["now"]);
	
	$stat["lady_trial"] = $lady_trial;
	
	//Lady- Trial Onhold
	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_INACT_TRIAL_LADY_ID;
#	$lady_trial_onhold["now"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user LEFT JOIN ".BILLING_USER_PERIOD_TABLE." AS p ON u.id=p.id_user WHERE g.id_group=".MM_INACT_TRIAL_LADY_ID."
#				AND p.date_end < (NOW() - INTERVAL 7 DAY)";
#	$lady_trial_onhold["seven_days"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user LEFT JOIN ".BILLING_USER_PERIOD_TABLE." AS p ON u.id=p.id_user WHERE g.id_group=".MM_INACT_TRIAL_LADY_ID."
#				AND p.date_end < (NOW() - INTERVAL 30 DAY)";
#	$lady_trial_onhold["thirty_days"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_INACT_TRIAL_LADY_ID;
#	$lady_trial_onhold["all_time"] = intval($dbconn->GetOne($strSQL)) + intval($lady_trial_onhold["now"]);
#	
#	$stat["lady_trial_onhold"] = $lady_trial_onhold;
	
	//Lady- Trial Cancelled
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_TRIAL_LADY_ID."
				AND date_termination > (NOW() - INTERVAL 7 DAY)";
	$lady_trial_cancelled["seven_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_TRIAL_LADY_ID."
				AND date_termination > (NOW() - INTERVAL 30 DAY)";
	$lady_trial_cancelled["thirty_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_TRIAL_LADY_ID;
	$lady_trial_cancelled["all_time"] = $dbconn->GetOne($strSQL);
	
	$stat["lady_trial_cancelled"] = $lady_trial_cancelled;
	
	//Lady- Regular Pending
	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_REGULAR_LADY_ID."
#				AND u.status = '0'";
#	$lady_regular_pending["now"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_REGULAR_LADY_ID."
#				AND (u.mm_application_submit + INTERVAL 7 DAY) < NOW() AND u.status = '0'";
#	$lady_regular_pending["seven_days"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_REGULAR_LADY_ID."
#				AND (u.mm_application_submit + INTERVAL 30 DAY) < NOW() AND u.status = '0'";
#	$lady_regular_pending["thirty_days"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_REGULAR_LADY_ID." AND status='0' ";
#	$lady_regular_pending["all_time"] = intval($dbconn->GetOne($strSQL)) + intval($lady_regular_pending["now"]);
#	
#	$stat["lady_regular_pending"] = $lady_regular_pending;
	
	//Lady- Regular
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_REGULAR_LADY_ID."
				AND u.mm_platinum_applied IS NULL";
	$lady_regular["now"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user LEFT JOIN ".BILLING_USER_PERIOD_TABLE." AS p ON u.id=p.id_user WHERE g.id_group=".MM_REGULAR_LADY_ID."
				AND p.date_begin < (NOW() - INTERVAL 7 DAY) AND u.mm_platinum_applied IS NULL";
	
	$lady_regular["seven_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user LEFT JOIN ".BILLING_USER_PERIOD_TABLE." AS p ON u.id=p.id_user WHERE g.id_group=".MM_REGULAR_LADY_ID."
				AND p.date_begin < (NOW() - INTERVAL 30 DAY) AND u.mm_platinum_applied IS NULL";
	$lady_regular["thirty_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_REGULAR_LADY_ID." AND status='1' ";
	$lady_regular["all_time"] = intval($dbconn->GetOne($strSQL)) + intval($lady_regular["now"]);
	
	$stat["lady_regular"] = $lady_regular;
	
	//Lady- Regular Onhold
	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_INACT_REGULAR_LADY_ID;
#	$lady_regular_onhold["now"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user LEFT JOIN ".BILLING_USER_PERIOD_TABLE." AS p ON u.id=p.id_user WHERE g.id_group=".MM_INACT_REGULAR_LADY_ID."
#				AND p.date_end < (NOW() - INTERVAL 7 DAY)";
#	
#	$lady_regular_onhold["seven_days"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user LEFT JOIN ".BILLING_USER_PERIOD_TABLE." AS p ON u.id=p.id_user WHERE g.id_group=".MM_INACT_REGULAR_LADY_ID."
#				AND p.date_end < (NOW() - INTERVAL 30 DAY)";
#	
#	$lady_regular_onhold["thirty_days"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_INACT_REGULAR_LADY_ID;
#	$lady_regular_onhold["all_time"] = intval($dbconn->GetOne($strSQL)) + intval($lady_regular_onhold["now"]);
#	
#	$stat["lady_regular_onhold"] = $lady_regular_onhold;
	
	//Lady- Regular Cancelled
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_REGULAR_LADY_ID."
				AND date_termination > (NOW() - INTERVAL 7 DAY)";
	$lady_regular_cancelled["seven_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_REGULAR_LADY_ID."
				AND date_termination > (NOW() - INTERVAL 30 DAY)";
	$lady_regular_cancelled["thirty_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_REGULAR_LADY_ID;
	$lady_regular_cancelled["all_time"] = $dbconn->GetOne($strSQL);
	
	$stat["lady_regular_cancelled"] = $lady_regular_cancelled;
	
	//Lady- Platinum Pending
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_REGULAR_LADY_ID."
				AND u.mm_platinum_applied IS NOT NULL";
	$lady_platinum_pending["now"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_REGULAR_LADY_ID."
				AND (u.mm_platinum_applied + INTERVAL 7 DAY) < NOW() ";
	$lady_platinum_pending["seven_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_REGULAR_LADY_ID."
				AND (u.mm_platinum_applied + INTERVAL 30 DAY) < NOW()";
	$lady_platinum_pending["thirty_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group = ".MM_REGULAR_LADY_ID." AND date_platinum_applied IS NOT NULL";
	$lady_platinum_pending["all_time"] = intval($dbconn->GetOne($strSQL)) + intval($lady_platinum_pending["now"]);
	
	$stat["lady_platinum_pending"] = $lady_platinum_pending;
	
	//Lady- Platinum
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_PLATINUM_LADY_ID;
	$lady_platinum["now"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_PLATINUM_LADY_ID."
				AND (u.mm_platinum_applied + INTERVAL 7 DAY) < NOW()";
	$lady_platinum["seven_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_PLATINUM_LADY_ID."
				AND (u.mm_platinum_applied + INTERVAL 30 DAY) < NOW()";
	$lady_platinum["thirty_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_PLATINUM_LADY_ID;
	$lady_platinum["all_time"] = intval($dbconn->GetOne($strSQL)) + intval($lady_platinum["now"]);
	
	$stat["lady_platinum"] = $lady_platinum;
	
	//Lady- Platinum Onhold
	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_INACT_PLATINUM_LADY_ID;
#	$lady_platinum_onhold["now"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user LEFT JOIN ".BILLING_USER_PERIOD_TABLE." AS p ON u.id=p.id_user WHERE g.id_group=".MM_INACT_PLATINUM_LADY_ID."
#				AND p.date_end < (NOW() - INTERVAL 7 DAY)";
#	$lady_platinum_onhold["seven_days"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user LEFT JOIN ".BILLING_USER_PERIOD_TABLE." AS p ON u.id=p.id_user WHERE g.id_group=".MM_INACT_PLATINUM_LADY_ID."
#				AND p.date_end < (NOW() - INTERVAL 30 DAY)";
#	$lady_platinum_onhold["thirty_days"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group = ".MM_INACT_PLATINUM_LADY_ID;
#	$lady_platinum_onhold["all_time"] = intval($dbconn->GetOne($strSQL)) + intval($lady_platinum_onhold["now"]);
#	
#	$stat["lady_platinum_onhold"] = $lady_platinum_onhold;
	
	//Lady- Platinum Cancelled
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_PLATINUM_LADY_ID."
				AND date_termination > (NOW() - INTERVAL 7 DAY)";
	$lady_platinum_cancelled["seven_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_PLATINUM_LADY_ID."
				AND date_termination > (NOW() - INTERVAL 30 DAY)";
	$lady_platinum_cancelled["thirty_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_PLATINUM_LADY_ID;
	$lady_platinum_cancelled["all_time"] = $dbconn->GetOne($strSQL);
	
	$stat["lady_platinum_cancelled"] = $lady_platinum_cancelled;
	
	//Lady- Total Cancelled
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE gender = '2'
				AND date_termination > (NOW() - INTERVAL 7 DAY)";
	$lady_total_cancelled["seven_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE gender = '2'
				AND date_termination > (NOW() - INTERVAL 30 DAY)";
	$lady_total_cancelled["thirty_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE gender = '2'";
	$lady_total_cancelled["all_time"] = $dbconn->GetOne($strSQL);
	
	$stat["lady_total_cancelled"] = $lady_total_cancelled;
	
	//Lady- Total Members
	$strSQL = "SELECT COUNT(id) FROM ".USERS_TABLE." WHERE gender = '2' AND root_user !='1' AND guest_user !='1'";
	$lady_total["now"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USERS_TABLE." WHERE gender = '2' AND root_user !='1' AND guest_user !='1' AND (date_registration + INTERVAL 7 DAY) < NOW()";
	$lady_total["seven_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USERS_TABLE." WHERE gender = '2' AND root_user !='1' AND guest_user !='1' AND (date_registration + INTERVAL 30 DAY) < NOW()";
	$lady_total["thirty_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USERS_TABLE." WHERE gender = '2' AND root_user !='1' AND guest_user !='1'";
	$lady_total["all_time"] = $dbconn->GetOne($strSQL);
	
	$stat["lady_total"] = $lady_total;
	
	// Guy
	//
	// Guy- Signup
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_SIGNUP_GUY_ID;
	$guy_signup["now"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_SIGNUP_GUY_ID."
				AND (u.date_registration + INTERVAL 7 DAY) < NOW()";
	$guy_signup["seven_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_SIGNUP_GUY_ID."
				AND (u.date_registration + INTERVAL 30 DAY) < NOW()";
	$guy_signup["thirty_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_SIGNUP_GUY_ID;
	$guy_signup["all_time"] = intval($dbconn->GetOne($strSQL)) + intval($guy_signup["now"]);
	
	$stat["guy_signup"] = $guy_signup;
	
	//Guy- Trial Pending
	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_TRIAL_GUY_ID."
#				AND u.status = '0'";
#	$guy_trial_pending["now"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_TRIAL_GUY_ID."
#				AND (u.mm_application_submit + INTERVAL 7 DAY) < NOW() AND u.status = '0'";
#	$guy_trial_pending["seven_days"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_TRIAL_GUY_ID."
#				AND (u.mm_application_submit + INTERVAL 30 DAY) < NOW() AND u.status = '0'";
#	$guy_trial_pending["thirty_days"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_TRIAL_GUY_ID." AND status='0' ";
#	$guy_trial_pending["all_time"] = intval($dbconn->GetOne($strSQL)) + intval($guy_trial_pending["now"]);
#	
#	$stat["guy_trial_pending"] = $guy_trial_pending;
	
	//Guy- Trial
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_TRIAL_GUY_ID." AND u.status='1' ";
	$guy_trial["now"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_TRIAL_GUY_ID." AND u.status='1'
				AND (u.mm_application_submit + INTERVAL 7 DAY) < NOW()";
	$guy_trial["seven_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_TRIAL_GUY_ID." AND u.status='1'
				AND (u.mm_application_submit + INTERVAL 30 DAY) < NOW()";
	$guy_trial["thirty_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_TRIAL_GUY_ID." AND status='1' ";
	$guy_trial["all_time"] = intval($dbconn->GetOne($strSQL)) + intval($guy_trial["now"]);
	
	$stat["guy_trial"] = $guy_trial;
	
	//Guy- Trial Onhold
	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_INACT_TRIAL_GUY_ID;
#	$guy_trial_onhold["now"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user LEFT JOIN ".BILLING_USER_PERIOD_TABLE." AS p ON u.id=p.id_user WHERE g.id_group=".MM_INACT_TRIAL_GUY_ID."
#				AND p.date_end < (NOW() - INTERVAL 7 DAY)";
#	$guy_trial_onhold["seven_days"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user LEFT JOIN ".BILLING_USER_PERIOD_TABLE." AS p ON u.id=p.id_user WHERE g.id_group=".MM_INACT_TRIAL_GUY_ID."
#				AND p.date_end < (NOW() - INTERVAL 30 DAY)";
#	$guy_trial_onhold["thirty_days"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_INACT_TRIAL_GUY_ID;
#	$guy_trial_onhold["all_time"] = intval($dbconn->GetOne($strSQL)) + intval($guy_trial_onhold["now"]);
#	
#	$stat["guy_trial_onhold"] = $guy_trial_onhold;
	
	//Guy- Trial Cancelled
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_TRIAL_GUY_ID."
				AND date_termination > (NOW() - INTERVAL 7 DAY)";
	$guy_trial_cancelled["seven_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_TRIAL_GUY_ID."
				AND date_termination > (NOW() - INTERVAL 30 DAY)";
	$guy_trial_cancelled["thirty_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_TRIAL_GUY_ID;
	$guy_trial_cancelled["all_time"] = $dbconn->GetOne($strSQL);
	
	$stat["guy_trial_cancelled"] = $guy_trial_cancelled;
	
	//Guy- Regular Pending
	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_REGULAR_GUY_ID."
#				AND u.status = '0'";
#	$guy_regular_pending["now"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_REGULAR_GUY_ID."
#				AND (u.mm_application_submit + INTERVAL 7 DAY) < NOW() AND u.status = '0'";
#	$guy_regular_pending["seven_days"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_REGULAR_GUY_ID."
#				AND (u.mm_application_submit + INTERVAL 30 DAY) < NOW() AND u.status = '0'";
#	$guy_regular_pending["thirty_days"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_REGULAR_GUY_ID." AND status='0' ";
#	$guy_regular_pending["all_time"] = intval($dbconn->GetOne($strSQL)) + intval($guy_regular_pending["now"]);
#	
#	$stat["guy_regular_pending"] = $guy_regular_pending;
	
	//Guy- Regular
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_REGULAR_GUY_ID."
				AND u.mm_platinum_applied IS NULL";
	$guy_regular["now"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user LEFT JOIN ".BILLING_USER_PERIOD_TABLE." AS p ON u.id=p.id_user WHERE g.id_group=".MM_REGULAR_GUY_ID."
				AND p.date_begin < (NOW() - INTERVAL 7 DAY) AND u.mm_platinum_applied IS NULL";
	
	$guy_regular["seven_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user LEFT JOIN ".BILLING_USER_PERIOD_TABLE." AS p ON u.id=p.id_user WHERE g.id_group=".MM_REGULAR_GUY_ID."
				AND p.date_begin < (NOW() - INTERVAL 30 DAY) AND u.mm_platinum_applied IS NULL";
	$guy_regular["thirty_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_REGULAR_GUY_ID." AND status='1' ";
	$guy_regular["all_time"] = intval($dbconn->GetOne($strSQL)) + intval($guy_regular["now"]);
	
	$stat["guy_regular"] = $guy_regular;
	
	//Guy- Regular Onhold
	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_INACT_REGULAR_GUY_ID;
#	$guy_regular_onhold["now"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user LEFT JOIN ".BILLING_USER_PERIOD_TABLE." AS p ON u.id=p.id_user WHERE g.id_group=".MM_INACT_REGULAR_GUY_ID."
#				AND p.date_end < (NOW() - INTERVAL 7 DAY)";
#	
#	$guy_regular_onhold["seven_days"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user LEFT JOIN ".BILLING_USER_PERIOD_TABLE." AS p ON u.id=p.id_user WHERE g.id_group=".MM_INACT_REGULAR_GUY_ID."
#				AND p.date_end < (NOW() - INTERVAL 30 DAY)";
#	
#	$guy_regular_onhold["thirty_days"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_INACT_REGULAR_GUY_ID;
#	$guy_regular_onhold["all_time"] = intval($dbconn->GetOne($strSQL)) + intval($guy_regular_onhold["now"]);
#	
#	$stat["guy_regular_onhold"] = $guy_regular_onhold;
	
	//Guy- Regular Cancelled
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_REGULAR_GUY_ID."
				AND date_termination > (NOW() - INTERVAL 7 DAY)";
	$guy_regular_cancelled["seven_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_REGULAR_GUY_ID."
				AND date_termination > (NOW() - INTERVAL 30 DAY)";
	$guy_regular_cancelled["thirty_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_REGULAR_GUY_ID;
	$guy_regular_cancelled["all_time"] = $dbconn->GetOne($strSQL);
	
	$stat["guy_regular_cancelled"] = $guy_regular_cancelled;
	
	//Guy- Platinum Pending
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_REGULAR_GUY_ID."
				AND u.mm_platinum_applied IS NOT NULL";
	$guy_platinum_pending["now"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_REGULAR_GUY_ID."
				AND (u.mm_platinum_applied + INTERVAL 7 DAY) < NOW()";
	$guy_platinum_pending["seven_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_REGULAR_GUY_ID."
				AND (u.mm_platinum_applied + INTERVAL 30 DAY) < NOW()";
	$guy_platinum_pending["thirty_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group = ".MM_REGULAR_GUY_ID." AND date_platinum_applied IS NOT NULL";
	$guy_platinum_pending["all_time"] = intval($dbconn->GetOne($strSQL)) + intval($guy_platinum_pending["now"]);
	
	$stat["guy_platinum_pending"] = $guy_platinum_pending;
	
	//Guy- Platinum
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_PLATINUM_GUY_ID;
	$guy_platinum["now"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_PLATINUM_GUY_ID."
				AND (u.mm_platinum_applied + INTERVAL 7 DAY) < NOW()";
	$guy_platinum["seven_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_PLATINUM_GUY_ID."
				AND (u.mm_platinum_applied + INTERVAL 30 DAY) < NOW()";
	$guy_platinum["thirty_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_PLATINUM_GUY_ID;
	$guy_platinum["all_time"] = intval($dbconn->GetOne($strSQL)) + intval($guy_platinum["now"]);
	
	$stat["guy_platinum"] = $guy_platinum;
	
	//Guy- Platinum Onhold
	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user WHERE g.id_group=".MM_INACT_PLATINUM_GUY_ID;
#	$guy_platinum_onhold["now"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user LEFT JOIN ".BILLING_USER_PERIOD_TABLE." AS p ON u.id=p.id_user WHERE g.id_group=".MM_INACT_PLATINUM_GUY_ID."
#				AND p.date_end < (NOW() - INTERVAL 7 DAY)";
#	$guy_platinum_onhold["seven_days"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(u.id) FROM ".USERS_TABLE." AS u LEFT JOIN ".USER_GROUP_TABLE." AS g ON u.id = g.id_user LEFT JOIN ".BILLING_USER_PERIOD_TABLE." AS p ON u.id=p.id_user WHERE g.id_group=".MM_INACT_PLATINUM_GUY_ID."
#				AND p.date_end < (NOW() - INTERVAL 30 DAY)";
#	$guy_platinum_onhold["thirty_days"] = $dbconn->GetOne($strSQL);
#	
#	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_INACT_PLATINUM_GUY_ID;
#	$guy_platinum_onhold["all_time"] = intval($dbconn->GetOne($strSQL)) + intval($guy_platinum_onhold["now"]);
#	
#	$stat["guy_platinum_onhold"] = $guy_platinum_onhold;
	
	//Guy- Platinum Cancelled
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_PLATINUM_GUY_ID."
				AND date_termination > (NOW() - INTERVAL 7 DAY)";
	$guy_platinum_cancelled["seven_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_PLATINUM_GUY_ID."
				AND date_termination > (NOW() - INTERVAL 30 DAY)";
	$guy_platinum_cancelled["thirty_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE id_group=".MM_PLATINUM_GUY_ID;
	$guy_platinum_cancelled["all_time"] = $dbconn->GetOne($strSQL);
	
	$stat["guy_platinum_cancelled"] = $guy_platinum_cancelled;
	
	//Guy- Total Cancelled
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE gender = '1'
				AND date_termination > (NOW() - INTERVAL 7 DAY)";
	$guy_total_cancelled["seven_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE gender = '1'
				AND date_termination > (NOW() - INTERVAL 30 DAY)";
	$guy_total_cancelled["thirty_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USER_TERMINATED_TABLE." WHERE gender = '1'";
	$guy_total_cancelled["all_time"] = $dbconn->GetOne($strSQL);
	
	$stat["guy_total_cancelled"] = $guy_total_cancelled;
	
	//Guy- Total Members
	$strSQL = "SELECT COUNT(id) FROM ".USERS_TABLE." WHERE gender = '1' AND root_user !='1' AND guest_user !='1'";
	$guy_total["now"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USERS_TABLE." WHERE gender = '1' AND root_user !='1' AND guest_user !='1' AND (date_registration + INTERVAL 7 DAY) < NOW()";
	$guy_total["seven_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USERS_TABLE." WHERE gender = '1' AND root_user !='1' AND guest_user !='1' AND (date_registration + INTERVAL 30 DAY) < NOW()";
	$guy_total["thirty_days"] = $dbconn->GetOne($strSQL);
	
	$strSQL = "SELECT COUNT(id) FROM ".USERS_TABLE." WHERE gender = '1' AND root_user !='1' AND guest_user !='1'";
	$guy_total["all_time"] = $dbconn->GetOne($strSQL);
	
	$stat["guy_total"] = $guy_total;
	
	
	$rs = $dbconn->Execute("select count(*) from ".USERS_TABLE."  where root_user !='1' and guest_user !='1'");
	$form["stat_all"] = $rs->fields[0];
	$rs = $dbconn->Execute("select count(*) from ".USERS_TABLE."  where gender='1' AND root_user !='1' and guest_user !='1'");
	$form["stat_men"] = $rs->fields[0];
	$rs = $dbconn->Execute("select count(*) from ".USERS_TABLE."  where gender='2' AND root_user !='1' and guest_user !='1'");
	$form["stat_women"] = $rs->fields[0];
	$rs = $dbconn->Execute("SELECT COUNT(DISTINCT a.id) FROM ".USERS_TABLE." a, ".ACTIVE_SESSIONS_TABLE." ea WHERE a.id = ea.id_user AND root_user <> '1' AND guest_user <> '1'");
	$form["stat_active"] = $rs->fields[0];
	$form["stat_active_link"] = $config["site_root"]."/admin/admin_users.php?s_stat=online";
	$rs = $dbconn->Execute("select count(distinct userid)  from ".F_CHAT_CONNECTIONS_TABLE." where userid>'0'");
	$form["stat_chat"] = $rs->fields[0];
	$form["stat_chat_link"] = $config["site_root"]."/admin/admin_users.php?s_stat=chat";
	$rs = $dbconn->Execute("select count(*) from ".USERS_TABLE."  where (date_registration + INTERVAL 1 DAY) > NOW()");
	$form["stat_reg_today"] = $rs->fields[0];
	$form["stat_reg_today_link"] = $config["site_root"]."/admin/admin_users.php?s_stat=reg_today";
	$rs = $dbconn->Execute("select count(*) from ".USERS_TABLE."  where (date_registration + INTERVAL 7 DAY) > NOW()");
	$form["stat_last_week"] = $rs->fields[0];
	$form["stat_last_week_link"] = $config["site_root"]."/admin/admin_users.php?s_stat=reg_week";
	$rs = $dbconn->Execute("select count(*) from ".USERS_TABLE."  where (date_registration + INTERVAL 30 DAY) > NOW()");
	$form["stat_last_month"] = $rs->fields[0];
	$form["stat_last_month_link"] = $config["site_root"]."/admin/admin_users.php?s_stat=reg_month";
	$rs = $dbconn->Execute("select login, login_count/(ROUND((UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(date_registration))/86400, 0)+1) as indicate from ".USERS_TABLE." order by indicate desc limit 0,1");
	$form["stat_most_active"] = $rs->fields[0];
	$rs = $dbconn->Execute("select id_user, login, SUM(count_of_visits) as pop from ".PROFILE_VISIT_TABLE." left join ".USERS_TABLE." b on b.id=id_user group by id_user order by pop desc limit 0,1");
	$form["stat_most_popular"] = $rs->fields[1];
	$rs = $dbconn->Execute("select sum(count) as col, id_module, name from ".MODULE_STATISTIC_TABLE." inner join ".MODULES_TABLE." p on p.id=id_module group by id_module order by col desc, name desc limit 0,2");
	$str = "";
	while(!$rs->EOF)
	{
		$row = $rs->GetRowAssoc(false);
		$str .= $lang["modules"][$row["name"]]["name"].", ";
		$rs->MoveNext();
	}
	$form["stat_most_popular_module"] =  substr($str, 0, -2);

	$str = "";
	$rs = $dbconn->Execute("select count(id_module) as col, sum(count) as visits, id_module, name from ".MODULE_STATISTIC_TABLE." left join ".MODULES_TABLE." p on p.id=id_module group by id_module order by col, visits limit 0,1");
	while(!$rs->EOF)
	{
		$row = $rs->GetRowAssoc(false);
		$str .= $lang["modules"][$row["name"]]["name"].", ";
		$rs->MoveNext();
	}
	$form["stat_not_seen_module"] = substr($str, 0, -2);
	$form["err"] = $err;
	$smarty->assign("stat", $stat);
	$smarty->assign("form", $form);
	$smarty->assign("header", $lang[$lang_type]);

	///// pilotgroup content ///////////////////////////////////////////////

#	@ini_set("max_execution_time",100);
#	$url_blog = "http://www.datingpro.com/blog/index.php?tempskin=_rss2";
#	$url_forum = "http://www.pilotgroup.net/feeder/datingpro/jun_2007/forum.php";
#	$url_version = "http://www.pilotgroup.net/feeder/datingpro/version.php";
#
#	load_dating_blog($url_blog, $articles);
#	$snoopy = new Snoopy;
#
#	@$snoopy->fetch($url_forum);
#	include_once "../include/class.utf8.php";
#	$obj_utf8 = new utf8(CP1251);
#
#	$txt_forum = @$snoopy->results;
#	$cod_forum = @$snoopy->response_code;
#	$out_forum = @$snoopy->timed_out;
#	$txt_forum = $obj_utf8->strToUtf8($txt_forum);
#
#	@$snoopy->fetch($url_version);
#	$txt_version = @$snoopy->results;
#	$cod_version = @$snoopy->response_code;
#	$out_version = @$snoopy->timed_out;
#
#	if ((!$out_forum && !$out_version) && (!empty($txt_forum)) && !empty($txt_version) &&
#	preg_match("/200/",$cod_forum) && preg_match("/200/",$cod_version))
#	{
#		$smarty->assign("page_type","download");
#		$smarty->assign("articles",$articles);
#		$smarty->assign("txt_forum",$txt_forum);
#		if (!isset($_SESSION["new_version"])) $_SESSION["new_version"] = check_new_version($txt_version);
#		$smarty->assign("new_version",$_SESSION["new_version"]);
#	}
#	else
#	{
#		$smarty->assign("page_type","start");
#	}
	
	///// pilotgroup content ///////////////////////////////////////////////

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_index_table.tpl");
	exit;
}

/*
function load_dating_blog($url,&$articles)
{
	$rss_array = array();
	$rss_array = rss2array($url);
	if (is_array($rss_array) && !(isset($rss_array['errors']) && sizeof($rss_array['errors'])>0) ) {
		foreach ( $rss_array["items"] as $key => $value ) {
			$articles[$key]["link"] = $value["link"];
			$articles[$key]["title"] = $value["title"];
			$articles[$key]["pubdate"] = date("m/d/Y H:i:s",$value["unixtimestamp"]);
		}
	}
	return;
}
*/

/*
function check_new_version($cur_version)
{
	global $dbconn, $auth;
	$rs = $dbconn->Execute("select value from ".SETTINGS_TABLE." where name='pilot_datingpro_version'");
	$version = $rs->fields[0];

	$month["JAN"] = "01";
	$month["FEB"] = "02";
	$month["MAR"] = "03";
	$month["APR"] = "04";
	$month["MAY"] = "05";
	$month["JUN"] = "06";
	$month["JUL"] = "07";
	$month["AUG"] = "08";
	$month["SEP"] = "09";
	$month["OCT"] = "10";
	$month["NOV"] = "11";
	$month["DEC"] = "12";

	$cur_version_arr = explode("_",$cur_version);
	$version_arr = explode("_",$version);

	$new_version = false;
	if (intval($cur_version_arr[1]) > intval($version_arr[1]))
	$new_version = true;
	elseif (intval($month[$cur_version_arr[0]]) > intval($month[$version_arr[0]]))
	$new_version = true;
	elseif (intval($cur_version_arr[2]) > intval($version_arr[2]))
	$new_version = true;

	if ($new_version) {
		$rs = $dbconn->Execute("select login_count from ".USERS_TABLE." where id='".$auth[0]."'");
		if ($rs->fields[0] <= 5) {
			$dbconn->Execute("update ".USERS_TABLE." set login_count=login_count+1 where id='".$auth[0]."'");
			return $cur_version;
		} else {
			$dbconn->Execute("update ".USERS_TABLE." set login_count=0 where id='".$auth[0]."'");
			$dbconn->Execute("update ".SETTINGS_TABLE." set value='".$cur_version."' where name='pilot_datingpro_version'");
		}
	}

	return null;
}
*/

?>