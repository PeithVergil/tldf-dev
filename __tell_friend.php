<?php
/**
* Tell friend about site
*
*
* @package DatingPro
* @subpackage User Mode
**/
include './include/config.php';
include './common.php';
include './include/config_index.php';
include './include/functions_auth.php';
include './include/functions_index.php';
include './include/class.phpmailer.php';
include './include/functions_mail.php';
include './include/class.lang.php';

$user = auth_index_user();

if (!$user[ AUTH_ID_USER ]) {
	header('location: '.$config['site_root'].'/index.php');
	exit;
}

RefreshAccount();

$smarty->assign('sub_menu_num', '9');

// check ins messages if user not guest
if (!$user[ AUTH_GUEST ]) {
	GetAlertsMessage();
}

$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

switch ($sel) {
	case 'send':		SendFriend(); break;
	case 'from_refer':	SetReferFlag(); break;
	default:			SendFriendForm();
}

function SendFriendForm($err='', $data=array())
{
	global $lang, $config, $config_index, $smarty, $user;
	
	$file_name = 'tell_friend.php';
	
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$site_name = $config['server'].$config['site_root'];
	
	$use_refer_friend_feature = GetSiteSettings('use_refer_friend_feature') && !$user[ AUTH_GUEST ];
	
	if ($use_refer_friend_feature) {
		$settings = GetSiteSettings(array('refer_friend_price','site_unit_costunit'));
		$search = array('[n]','[currency]');
		$repl = array($settings['refer_friend_price'],$settings['site_unit_costunit']);
		$form['comment'] = str_replace($search, $repl, $lang['refer_a_friend']['comment']);
		$code = GetUserReferCode($user[ AUTH_ID_USER ]);
		if (!$code) $code = substr(md5(time()),1,12);
		$form['body'] = !empty($data['body']) ? $data['body'] : str_replace('[link]', $site_name.'/tell_friend.php?sel=from_refer&code='.$code, $lang['refer_a_friend']['body']);
		$form['hidden'] = '<input type="hidden" name="refer_code" value="'.$code.'" />';
	} else {
		$form['comment'] = $lang['tell_a_friend']['comment'];
		$form['body'] = !empty($data['body']) ? $data['body'] : str_replace('[link]', $site_name, $lang['tell_a_friend']['body']);
		$form['hidden'] = '';
	}
	
	$form['top_header'] = $lang['tell_a_friend']['top_header'];
	$form['name'] = !empty($data['name']) ? $data['name'] : '';
	$form['email'] = !empty($data['email']) ? $data['email'] : '';
	$form['to'] = !empty($data['to']) ? $data['to'] : '';
	$form['subject'] = !empty($data['subject']) ? $data['subject'] : str_replace('[site]', $site_name, $lang['tell_a_friend']['subj']);
	$form['action'] = $file_name;
	$form['err'] = $err;
	$form['hidden'] .= '<input type=hidden name=sel value=send />';
	$form['kcaptcha'] = $config['site_root'].$config_index['kcaptcha'];
	
	$smarty->assign('form', $form);
	$smarty->assign('section', $lang['subsection']);
	$smarty->assign('header', $lang['contact_us']);
	$smarty->display(TrimSlash($config['index_theme_path']).'/tell_a_friend_form.tpl');
	exit;
}

function SendFriend()
{
	global $lang, $config, $dbconn, $user;
	
	$name = $data['name'] = FormFilter($_POST['name']);
	$email = $data['email'] = FormFilter($_POST['email']);
	$to = $data['to'] = FormFilter($_POST['to']);
	$subject = $data['subject'] = FormFilter($_POST['subject']);
	$body = $data['body'] = trim(stripslashes($_POST['body']));
	
	$err = '';
	
	if (strlen($name) == 0) {
		if ($err) $err .= '<br>';
		$err .= '* '.$lang['err']['invalid_name_field'];
	}
	
	if (strlen($email) == 0) {
		if ($err) $err .= '<br>';
		$err .= '* '.$lang['err']['invalid_email_field'];
	}
	
	$email_err = EmailFilter($email);
	
	if ($email_err) {
		if ($err) $err .= '<br>';
		$err .= '* '.$lang['err']['invalid_email_field'].' '.$email_err;
	}
	
	if (strlen($to) == 0) {
		if ($err) $err .= '<br>';
		$err .= '* '.$lang['err']['invalid_to_field'];
	}
	
	$email_err = EmailFilter($to);
	
	if ($email_err) {
		if ($err) $err .= '<br>';
		$err .= '* '.$lang['err']['invalid_to_field'].' '.$email_err;
	}
	
	if (strlen($subject) == 0) {
		if ($err) $err .= '<br>';
		$err .= '* '.$lang['err']['invalid_subject_field'];
	}
	
	if (strlen($body) == 0){
		if ($err) $err .= '<br>';
		$err .= '* '.$lang['err']['invalid_message_field'];
	}
	
	if ($err) {
		SendFriendForm($err, $data);
		return;
	}
	
	$to_arr = array();
	$to_arr = explode(';', $to);
	$count = count($to_arr)<=5?count($to_arr):5;
	if (!$count) {
		$err = $lang['err']['invalid_to_field'];
		SendFriendForm($err, $data);
		return;
	}
	
	if (!(isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] ==  $_POST['keystring'])) {
		$err = $lang['err']['invalid_security_code'];
		SendFriendForm($err, $data);
		return;
	}
	unset($_SESSION['captcha_keystring']);
	
	$site_lang = $config['default_lang'];
	
	$cont_arr['name'] = $name;
	$cont_arr['email'] = $email;
	$cont_arr['content'] = $body;
	
	$mailer_err = $err = '';
	$use_refer_friend_feature = GetSiteSettings('use_refer_friend_feature') && !$user[ AUTH_GUEST ] && $_POST['refer_code'];
	if ($use_refer_friend_feature) {
		$code = GetUserReferCode($user[ AUTH_ID_USER ]);
		if (!$code) {
			$strSQL = 'INSERT INTO '.USER_REFER_CODE_TABLE.' SET id_user="'.$user[ AUTH_ID_USER ].'", code="'.addslashes($_POST['refer_code']).'"';
			$dbconn->Execute($strSQL);
			if ($dbconn->ErrorNo()) {
				SendFriendForm($lang['err']['error'], $data);
			}
		}
	}
	$n = 0;
	for ($i = 0; $i < $count; $i++) {
		if ($use_refer_friend_feature) {
			$strSQL = 'SELECT email FROM '.USERS_TABLE.' WHERE email="'.$to_arr[$i].'"';
			$email = $dbconn->GetOne($strSQL);
			if (!$email) {
				$mailer_err = SendMail($site_lang, $to_arr[$i], $cont_arr['email'], $subject, $cont_arr, 'mail_tell_a_friend', '', '', $cont_arr['name']);
			}
		} else {
			$mailer_err = SendMail($site_lang, $to_arr[$i], $cont_arr['email'], $subject, $cont_arr, 'mail_tell_a_friend', '', '', $cont_arr['name']);
		}
		if ($mailer_err) {
			$err = $mailer_err;
		} else {
			$n++;
		}
	}
	if (!$err) {
		if ($use_refer_friend_feature) {
			$settings = GetSiteSettings(array('refer_friend_price','site_unit_costunit'));
			$acc_link = '<a href="'.$config['server'].$config['site_root'].'/account.php" >'.$lang['section']['account'].'</a>';
			$search = array('[n_friends]','[n_money]','[curr]','[my_acc]');
			$repl = array($n, $settings['refer_friend_price'],$settings['site_unit_costunit'],$acc_link);
			$err = str_replace($search,$repl,$lang['err']['refer_email_was_sent']);
		} else {
			$err = $lang['err']['email_was_sent'];
		}
	}
	SendFriendForm($err, $data);
	return;
}

function SetReferFlag()
{
	global $config;
	setcookie('refer_friend_code',$_GET['code'],time()+60*60*24*30);
	if ($_GET['id_profile']) {
		header('location:'.$config['server'].$config['site_root'].'/viewprofile.php?id='.$_GET['id_profile']);
		exit();
	}
	header('location:'.$config['server'].$config['site_root']);
}
?>