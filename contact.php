<?php
/**
* Contact Us form and sending (interface for sending feedbacks to site administration)
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
	case 'contact':	 ContactSend(); break;
	case 'ajaxsend': ContactSend('ajax'); break;
	default:		 ListTableContact();
}

exit;


function ListTableContact($err = '', $err_field = array(), $data = array(), $type = '')
{
	global $lang, $config, $config_index, $smarty, $user;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$form['err'] = $err;
	$form['err_field'] = $err_field;
	$form['kcaptcha'] = $config['site_root'].$config_index['kcaptcha'];
	
	$smarty->assign('form', $form);
	$smarty->assign('data', $data);
	$smarty->assign('section', $lang['subsection']);
	$smarty->assign('header', $lang['contact_us']);
	
	if ($type == 'ajax') {
		$smarty->display(TrimSlash($config['index_theme_path']).'/inline_contact.tpl');
	} else {
		$smarty->display(TrimSlash($config['index_theme_path']).'/contact_table.tpl');
	}
	exit;
}

function ContactSend($type = '')
{
	global $lang, $config;
	
	$data = array();
	
	$data['fname']		= FormFilter($_POST['fname']);
	$data['email']		= FormFilter($_POST['email']);
	$data['subject']	= FormFilter($_POST['subject']);
	$data['message']	= FormFilter($_POST['message']);
	
	/* Ajax test
	$msg  = 'fname: '.$data['fname'].'<br>';
	$msg .= 'email: '.$data['email'].'<br>';
	$msg .= 'subject: '.$data['subject'].'<br>';
	$msg .= 'message: '.$data['message'].'<br>';
	
	ListTableContact($msg, array(), $data, $type);
	return;
	*/
	
	$err = array();
	$err_field = array();
	
	if (!strlen($data['fname'])) {
		$err[] = $lang['contact_us']['name'];
		$err_field['fname'] = 1;
	}
	
	if (!strlen($data['email'])) {
		$err[] = $lang['contact_us']['email'];
		$err_field['email'] = 1;
	} else {
		$email_err = EmailFilter($data['email']);
		if ($email_err) {
			$err[] = $email_err;
			$err_field['email'] = 1;
		}
	}
	
	if (!strlen($data['subject'])) {
		$err[] = $lang['contact_us']['subject'];
		$err_field['subject'] = 1;
	}
	
	if (!strlen($data['message'])){
		$err[] = $lang['contact_us']['message'];
		$err_field['message'] = 1;
	}
	
	if ($type != 'ajax') {
		if (!(isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] ==  $_POST['keystring'])) {
			$err[] = $lang['err']['invalid_security_code'];
			$err_field['captcha'] = 1;
		}
		unset($_SESSION['captcha_keystring']);
	}
	
	if (!empty($err)) {
		$error = $lang['err']['invalid_fields'].'<br><br>'.implode('<br>', $err);
		ListTableContact($error, $err_field, $data, $type);
		return;
	}
	
	/**
	 * notification emails
	 **/
	
	/**
	 * email to admin
	 **/
	
	// language
	$site_lang = $config['default_lang'];
	
	// subject
	$subject	= html_entity_decode($data['subject']);
	
	// sender
	$email_from	= html_entity_decode($data['email']);
	$name_from	= html_entity_decode($data['fname']);
	
	// recipient
	if (REDIRECT_ADMIN_EMAIL && !IS_LIVE_SERVER) {
		$email_to = REDIRECT_ADMIN_EMAIL_TO;
	} else {
		$email_to = $config['site_email'];
	}
	
	$error = SendMail($site_lang, $email_to, $email_from, $subject, $data, 'mail_contact_us_admin',
		null, '', $name_from, 'contact_us_admin');
	
	if (!$error) {
		$error = $lang['err']['contact_send'];
		$data  = array();
	}
	
	ListTableContact($error, array(), $data, $type);
	return;
}

?>