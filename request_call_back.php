<?php
/**
* Request Call Back
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
include './include/class.phpmailer.php';
include './include/functions_mail.php';

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
	default:
		RequestCallTable();
	break;
}

exit;


function RequestCallTable($err = '')
{
	global $lang, $config, $smarty, $user;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$msg = isset($_REQUEST['msg']) ? $_REQUEST['msg'] : '';
	
	$data = array();
	$err_field = array();
	
	if (!empty($_POST))
	{
		$data['name']       = isset($_POST['name']) ? FormFilter($_POST['name']) : '';
		$data['email']      = isset($_POST['email']) ? FormFilter($_POST['email']) : '';
		$data['city']       = isset($_POST['city']) ? FormFilter($_POST['city']) : '';
		$data['country']    = isset($_POST['country']) ? FormFilter($_POST['country']) : '';
		$data['phone']      = isset($_POST['phone']) ? FormFilter($_POST['phone']) : '';
		$data['best_times'] = isset($_POST['best_times']) ? FormFilter($_POST['best_times']) : '';
		$data['interest']   = isset($_POST['interest']) ? FormFilter($_POST['interest']) : '';
		$data['marital']    = isset($_POST['marital']) ? FormFilter($_POST['marital']) : '';
		$data['main_thing'] = isset($_POST['main_thing']) ? FormFilter($_POST['main_thing']) : '';
		$data['about_me']   = isset($_POST['about_me']) ? FormFilter($_POST['about_me']) : '';
		
		if (!strlen($data['name'])) {
			$err .= $lang['request_call']['name'] . '<br>';
			$err_field['name'] = 1;
		}
		
		if (!strlen($data['email'])) {
			$err .= $lang['request_call']['email'] . '<br>';
			$err_field['email'] = 1;
		} elseif ($err_email = EmailFilter($data['email'])) {
			$err .= $err_email . '<br>';
			$err_field['email'] = 1;
		}
		
		if (!strlen($data['city'])) {
			$err .= $lang['request_call']['city'] . '<br>';
			$err_field['city'] = 1;
		}
		
		if (!strlen($data['country'])) {
			$err .= $lang['request_call']['country'] . '<br>';
			$err_field['country'] = 1;
		}
		
		if (!strlen($data['phone'])) {
			$err .= $lang['request_call']['phone'] . '<br>';
			$err_field['phone'] = 1;
		}
		
		if (!strlen($data['best_times'])) {
			$err .= $lang['request_call']['best_times'] . '<br>';
			$err_field['best_times'] = 1;
		}
		
		if (!strlen($data['interest'])) {
			$err .= $lang['request_call']['interest'] . '<br>';
			$err_field['interest'] = 1;
		}
		
		if (!strlen($data['marital'])) {
			$err .= $lang['request_call']['marital'] . '<br>';
			$err_field['marital'] = 1;
		}
		
		/*
		if (!strlen($data['main_thing'])) {
			$err .= $lang['request_call']['main_thing'] . '<br>';
			$err_field['main_thing'] = 1;
		}
		
		if (!strlen($data['about_me'])) {
			$err .= $lang['request_call']['about_me'] . '<br>';
			$err_field['about_me'] = 1;
		}
		*/
		
		if ($err) {
			$err = $lang['err']['invalid_fields'] . '<br><br>' . trim($err, '<br>');
		} else {
			$mail_err = RequestCall($data);
			if ($mail_err) {
				echo '<script>location.href="./request_call_back.php?msg=error";</script>';
			} else {
				echo '<script>location.href="./request_call_back.php?msg=thanks";</script>';
			}
			exit;
		}
	}
	
	if ($msg == 'error') {
		$err = $lang['request_call']['error'];
	}
	
	$form['err'] = $err;
	$form['err_field'] = $err_field;
	
	$smarty->assign('form', $form);
	$smarty->assign('data', $data);
	
	if ($msg == 'thanks') {
		$smarty->display(TrimSlash($config['index_theme_path']).'/request_call_back_thanks.tpl');
	} else {
		$smarty->display(TrimSlash($config['index_theme_path']).'/request_call_back.tpl');
	}
	
	exit;
}

function RequestCall($data)
{
	global $config, $dbconn;
	
	/**
	 * notification emails
	 **/
	
	// language
	$site_lang = $config['default_lang'];

	// include language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	/**
	 * email to admin
	 **/
	
	switch ($data['interest']) {
		case 'Being':
			$data['interest'] = 'Being A Member Of Thai Lady Date Finder';
		break;
		case 'Coming':
			$data['interest'] = 'Coming To Bangkok And Meeting Eligible, Prescreened Thai Ladies Through Thai Lady Dating Events';
		break;
		case 'Both':
			$data['interest'] = 'Both Programs';
		break;
	}
	
	// subject
	$subject	= $lang_mail['request_call_back_admin']['subject'];
	
	// sender
	$email_from	= html_entity_decode($data['email']);
	$name_from	= html_entity_decode($data['name']);
	
	// recipient
	if (REDIRECT_ADMIN_EMAIL && !IS_LIVE_SERVER) {
		$email_to = REDIRECT_ADMIN_EMAIL_TO;
	} else {
		$email_to = $config['site_email'];
	}
	
	$err = SendMail($site_lang, $email_to, $email_from, $subject, $data,
				'mail_request_call_back_admin', null, '', $name_from, 'request_call_back_admin');
	
	return $err;
}

?>