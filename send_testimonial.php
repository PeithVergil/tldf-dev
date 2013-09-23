<?php
/**
* Send Testimonial
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
		SendTestimonialTable();
	break;
}

exit;


function SendTestimonialTable($err = '')
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
		$data['testimonial']	= isset($_POST['testimonial']) ? FormFilter($_POST['testimonial']) : '';
		
		if (!strlen($data['name'])) {
			$err .= $lang['send_testimonial']['name'] . '<br>';
			$err_field['name'] = 1;
		}
		
		if (!strlen($data['email'])) {
			$err .= $lang['send_testimonial']['email'] . '<br>';
			$err_field['email'] = 1;
		} elseif ($err_email = EmailFilter($data['email'])) {
			$err .= $err_email . '<br>';
			$err_field['email'] = 1;
		}
		
		if (!strlen($data['testimonial'])) {
			$err .= $lang['send_testimonial']['testimonial'] . '<br>';
			$err_field['testimonial'] = 1;
		}
		
		if ($err) {
			$err = $lang['err']['invalid_fields'] . '<br/><br/>' . trim($err, ', ');
		} else {
			if (SendTestimonial($data)) {
				echo '<script>location.href="./send_testimonial.php?msg=thanks";</script>';
			} else {
				echo '<script>location.href="./send_testimonial.php?msg=error";</script>';
			}
			exit;
		}
	}
	
	if ($msg == 'thanks') {
		$form['res'] = 1;
		$err = $lang['send_testimonial']['thanks'];
	} elseif ($msg == 'error') {
		$err = $lang['send_testimonial']['error'];
	}
	
	$form['err'] = $err;
	$form['err_field'] = $err_field;
	
	$smarty->assign('form', $form);
	$smarty->assign('data', $data);
	$smarty->display(TrimSlash($config['index_theme_path']).'/send_testimonial.tpl');
	
	exit;
}

function SendTestimonial($data)
{
	global $config, $dbconn;
	
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
	
	$data['date'] = date('Y-M-d');
	
	// subject
	$subject	= $lang_mail['send_testimonial_admin']['subject'];
	
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
				'mail_send_testimonial_admin', null, '', $name_from, 'send_testimonial_admin');
	
	return $err;
}

?>