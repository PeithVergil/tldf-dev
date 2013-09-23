<?php
/**
* Lost password form and send lost password
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

if (!$user[ AUTH_GUEST]) {
	header('location: '.$config['server'].$config['site_root'].'/homepage.php');
	exit;
}

// check guest
// (public access)

// check group, period, expiration
// (not needed on Lost Password page)

// check status
// (public access)

// check permissions
// (public access)

// active menu item
$smarty->assign('sub_menu_num', '');

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

// dispatcher
switch ($sel) {
	case 'send':
		SendPassw();
	break;
	default:
		LostPassForm();
	break;
}

exit;


function SendPassw()
{
	global $lang, $lang_mail, $config, $dbconn;
	
	$data = array();
	
	$data['email'] = isset($_POST['email']) ? FormFilter($_POST['email']) : '';
	
	if (!strlen($data['email'])) {
		LostPassForm($lang['err']['invalid_lost_pass_email']);
		return;
	}
	
	$rs = $dbconn->Execute(
		'SELECT id, login, fname, sname, gender, site_language
		   FROM '.USERS_TABLE.'
		  WHERE email = ? AND root_user = "0"',
		array($data['email']));
	
	$row = $rs->GetRowAssoc(false);
	$count = $rs->RowCount();
	
	if ($count != 1){
		LostPassForm($lang['err']['invalid_lost_pass_email']);
		return;
	}
	
	$id_user			= $row['id'];
	
	$data['login']		= stripslashes($row['login']);
	$data['fname']		= stripslashes($row['fname']);
	$data['sname']		= stripslashes($row['sname']);
	$data['gender']		= $row['gender'];
	$data['site_lang']	= $row['site_language'];
	$data['new_pass']	= substr(md5(time()), 0, 6);
	
	$dbconn->Execute('UPDATE '.USERS_TABLE.' SET password = ? WHERE id = ?', array(md5($data['new_pass']), $id_user));
	
	/**
	 * send registration email
	 **/
	
	// language
	$site_lang = $data['site_lang'];
	
	// include language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	$data['urls']		= GetUserEmailLinks();

	$rs = $dbconn->Execute('SELECT email FROM '.USERS_TABLE.' WHERE root_user = "1"');
	$data['adminemail']	= $rs->fields[0];
	$rs->free();
	
	// gender suffix
	$suffix = ($data['gender'] == GENDER_MALE) ? '_e' : '_t';
	
	// subject
	$subject	= $lang_mail['password_changed'.$suffix]['subject'];
	
	// recipient
	$email_to	= html_entity_decode($data['email']);
	$name_to	= html_entity_decode(trim($data['fname'].' '.$data['sname']));
	
	SendMail($site_lang, $email_to, $config['site_email'], $subject, $data,
		'mail_password_changed_user', null, $name_to, '', 'password_changed', $data['gender']);
	
	// assemble body
	$body = $lang_mail['generic'.$suffix]['hello'].' '.$data['fname'].',<br><br>';
	$body.= $lang_mail['password_changed'.$suffix]['message'].'<br><br>';
	$body.= $lang_mail['generic'.$suffix]['admin_regards'];
	
	$dbconn->Execute(
		'INSERT INTO '.MAILBOX_TABLE.' SET
			id_to = ?, id_from = ?, subject = ?, body = ?, was_read = "0", deleted_to = "0", deleted_from = "0", date_creation = NOW()',
			array($id_user, ID_ADMIN, $subject, $body));
	
	LostPassForm($lang['err']['success_lost_pass_email']);
	return;
}


function LostPassForm($err = '')
{
	global $smarty, $lang, $config;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	
	$form['site_email'] = GetSiteSettings('site_email');
	$form['err']		= $err;
	
	$smarty->assign('header', $lang['lost_pass']);
	$smarty->assign('form', $form);
	$smarty->assign('alt', $lang['alt']);
	$smarty->display(TrimSlash($config['index_theme_path']).'/lost_pass_page.tpl');
	exit;
}

?>