<?php
/**
* Report A Bug
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
		ReportABugTable();
	break;
}

exit;


function ReportABugTable($err = '')
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
		$data['name']			= isset($_POST['name']) ? FormFilter($_POST['name']) : '';
		$data['email']			= isset($_POST['email']) ? FormFilter($_POST['email']) : '';
		$data['phone']			= isset($_POST['phone']) ? FormFilter($_POST['phone']) : '';
		$data['description']	= isset($_POST['description']) ? FormFilter($_POST['description']) : '';
		
		if (!strlen($data['name'])) {
			$err .= $lang['report_a_bug']['name'] . '<br>';
			$err_field['name'] = 1;
		}
		
		if (!strlen($data['email'])) {
			$err .= $lang['report_a_bug']['email'] . '<br>';
			$err_field['email'] = 1;
		} elseif ($err_email = EmailFilter($data['email'])) {
			$err .= $err_email . '<br>';
			$err_field['email'] = 1;
		}
		
		/*
		if (!strlen($data['phone'])) {
			$err .= $lang['report_a_bug']['phone'] . '<br>';
			$err_field['phone'] = 1;
		}
		*/
		
		if (!strlen($data['description'])) {
			$err .= $lang['report_a_bug']['description'] . '<br>';
			$err_field['description'] = 1;
		}
		
		if ($err) {
			$err = $lang['err']['invalid_fields'] . '<br><br>' . trim($err, '<br>');
		} else {
			$mail_err = ReportABug($data);
			if ($mail_err) {
				echo '<script>location.href="./report_a_bug.php?msg=error";</script>';
			} else {
				echo '<script>location.href="./report_a_bug.php?msg=thanks";</script>';
			}
			exit;
		}
	}
	
	if ($msg == 'error') {
		$err = $lang['report_a_bug']['error'];
	}
	
	$form['err'] = $err;
	$form['err_field'] = $err_field;
	
	$smarty->assign('form', $form);
	$smarty->assign('data', $data);
	
	if ($msg == 'thanks') {
		$smarty->display(TrimSlash($config['index_theme_path']).'/report_a_bug_thanks.tpl');
	} else {
		$smarty->display(TrimSlash($config['index_theme_path']).'/report_a_bug.tpl');
	}
	
	exit;
}

function ReportABug($data)
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
	
	// subject
	$subject	= $lang_mail['report_bug_admin']['subject'];
	
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
				'mail_report_bug_admin', null, '', $name_from, 'report_bug_admin');
	
	return $err;
}

?>