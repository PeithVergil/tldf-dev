<?php
/**
* Send Feedback
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
		SendFeedbackTable();
	break;
}

exit;


function SendFeedbackTable($err = '')
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
		$data['question_1']	= isset($_POST['question_1']) ? FormFilter($_POST['question_1']) : '';
		$data['question_2']	= isset($_POST['question_2']) ? FormFilter($_POST['question_2']) : '';
		$data['question_3']	= isset($_POST['question_3']) ? FormFilter($_POST['question_3']) : '';
		$data['question_4']	= isset($_POST['question_4']) ? FormFilter($_POST['question_4']) : '';
		$data['question_5']	= isset($_POST['question_5']) ? FormFilter($_POST['question_5']) : '';
		$data['comments']   = isset($_POST['comments']) ? FormFilter($_POST['comments']) : '';
		
		if (!strlen($data['name'])) {
			$err .= $lang['send_feedback']['name'] . '<br>';
			$err_field['name'] = 1;
		}
		
		if (!strlen($data['email'])) {
			$err .= $lang['send_feedback']['email'] . '<br>';
			$err_field['email'] = 1;
		} elseif ($err_email = EmailFilter($data['email'])) {
			$err .= $err_email . '<br>';
			$err_field['email'] = 1;
		}
		
		if (!strlen($data['question_1'])) {
			$err .= $lang['send_feedback']['question_1'] . '<br>';
			$err_field['question_1'] = 1;
		}
		
		if (!strlen($data['question_2'])) {
			$err .= $lang['send_feedback']['question_2'] . '<br>';
			$err_field['question_2'] = 1;
		}
		
		if (!strlen($data['question_3'])) {
			$err .= $lang['send_feedback']['question_3'] . '<br>';
			$err_field['question_3'] = 1;
		}
		
		if (!strlen($data['question_4'])) {
			$err .= $lang['send_feedback']['question_4'] . '<br>';
			$err_field['question_4'] = 1;
		}
		
		if (!strlen($data['question_5'])) {
			$err .= $lang['send_feedback']['question_5'] . '<br>';
			$err_field['question_5'] = 1;
		}
		
		if (!strlen($data['comments'])) {
			$err .= $lang['send_feedback']['comments'] . '<br>';
			$err_field['comments'] = 1;
		}
		
		if ($err) {
			$err = $lang['err']['invalid_fields'] . '<br><br>' . trim($err, '<br>');
		} else {
			$mail_err = SendFeedback($data);
			if ($mail_err) {
				echo '<script>location.href="./send_feedback.php?msg=error";</script>';
			} else {
				echo '<script>location.href="./send_feedback.php?msg=thanks";</script>';
			}
			exit;
		}
	}
	
	if ($msg == 'thanks') {
		$form['res'] = 1;
		$err = $lang['send_feedback']['thanks'];
	} elseif ($msg == 'error') {
		$err = $lang['send_feedback']['error'];
	}
	
	$form['err'] = $err;
	$form['err_field'] = $err_field;
	
	$smarty->assign('form', $form);
	$smarty->assign('data', $data);
	$smarty->display(TrimSlash($config['index_theme_path']).'/send_feedback.tpl');
	
	exit;
}

function SendFeedback($data)
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
	$subject	= $lang_mail['send_feedback_admin']['subject'];
	
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
				'mail_send_feedback_admin', null, '', $name_from, 'send_feedback_admin');
	
	return $err;
}

?>