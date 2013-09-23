<?php
/**
* Report A Violation
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
		ReportViolationTable();
	break;
}

exit;


function ReportViolationTable($err = '')
{
	global $lang, $config, $smarty, $user;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$file_name = 'report_a_violation.php';
	
	$msg = isset($_REQUEST['msg']) ? $_REQUEST['msg'] : 0;
	
	if ($msg == 0)
	{
		$data = $_POST;
		
		if ($data)
		{
			$name        = isset($data['name']) ? FormFilter($data['name']) : '';
			$email       = isset($data['email']) ? FormFilter($data['email']) : '';
			$phone       = isset($data['phone']) ? FormFilter($data['phone']) : '';
			$description = isset($data['description']) ? FormFilter($data['description']) : '';
			
			if (!strlen($name)) {
				$err .= $lang['report_a_violation']['name'] . ', ';
				$err_field['name'] = 1;
			}
			
			if (!strlen($email)) {
				$err .= $lang['report_a_violation']['email'] . ', ';
				$err_field['email'] = 1;
			} else {
				// email validation
				if (EmailFilter($email)) {
					$err .= EmailFilter($email);
					$err_field['email'] = 1;
				}
			}
			/*
			if (!strlen($phone)) {
				$err .= $lang['report_a_violation']['phone'] . ', ';
				$err_field['phone'] = 1;
			}
			*/
			if (!strlen($description)) {
				$err .= $lang['report_a_violation']['description'] . ', ';
				$err_field['description'] = 1;
			}
			
			if ($err)
			{
				$smarty->assign('err_field', $err_field);
				$err = $lang['err']['invalid_fields'] . '<br/><br/>' . trim($err, ', ');
				
				$form['name']        = $name;
				$form['email']       = $email;
				$form['phone']       = $phone;
				$form['description'] = $description;
			}
			else
			{
				if (ReportViolation())
				{
					echo '<script>location.href="./report_a_violation.php?msg=2";</script>';
				}
				else
				{
					echo '<script>location.href="./report_a_violation.php?msg=1";</script>';
				}
			}
		}
	}
	
	if ($msg == 2) {
		$err = $lang['report_a_violation']['error'];
	}
	
	$form['err'] = $err;
	$form['action'] = $file_name;
	$smarty->assign('form', $form);
	
	if ($msg != 1) {
		$smarty->display(TrimSlash($config['index_theme_path']).'/report_a_violation.tpl');
	} else {
		$smarty->display(TrimSlash($config['index_theme_path']).'/report_a_violation_thanks.tpl');
	}
	
	exit;
}

function ReportViolation()
{
	global $config, $dbconn, $user;
	
	$data = $_POST;
	
	/**
	 * email notifications
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
	
	// subject
	$subject	= $lang_mail['report_violation_admin']['subject'];
	
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
				'mail_report_violation_admin', null, '', $name_from, 'report_violation_admin');
	
	/**
	 * email to user
	 **/
	
	// gender suffix
	$suffix = ($user[ AUTH_GENDER ] == GENDER_MALE) ? '_e' : '_t';
	
	// subject
	$subject	= $lang_mail['report_violation'.$suffix]['subject'];
	
	// recipient
	$email_to	= html_entity_decode($data['email']);
	$name_to	= html_entity_decode($data['name']);
	
	$err = SendMail($site_lang, $email_to, $config['site_email'], $subject, $data, 'mail_report_violation_user', null,
					$name_to, '', 'report_violation', $user[ AUTH_GENDER ]);
	
	return $err;
}

?>