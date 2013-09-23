<?php
/**
* Administrator-user communication interface
*
* @package DatingPro
* @subpackage Admin Mode
**/
include '../include/config.php';
include '../common.php';
include '../include/config_admin.php';
include '../include/functions_auth.php';
include '../include/functions_admin.php';
include '../include/class.phpmailer.php';
include '../include/functions_mail.php';

$auth = auth_user();

login_check($auth);

IsFileAllowed($auth[0], GetRightModulePath(__FILE__), 'comunicate');

$sel = isset($_REQUEST['sel']) ? $_REQUEST['sel'] : '';

$id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : "";
if (intval($id) < 1) {
	$err = $lang["err"]["empty_user"];
} else {
	$err = "";
}

switch ($sel) {
	case "send": ComunicateSend(); break;
	default: ComunicateForm($id, $err);
}

exit;


function ComunicateForm($id, $err = '', $to = '', $message = '')
{
	global $smarty, $dbconn, $config, $lang;
	
	$file_name = 'admin_comunicate.php';
	
	if ($err) {
		$data['to'] = $to;
		$data['message'] = $message;
	}
	
	AdminMainMenu($lang['comunicate'], '1');
	
	$rs = $dbconn->Execute('SELECT login FROM '.USERS_TABLE.' WHERE id = ?', array($id));
	
	if ($rs->EOF) {
		$err = $lang['err']['empty_user'];
	} else {
		$data['to'] = $rs->fields[0];
	}
	
	$form['hiddens'] = '<input type="hidden" name="id" value="'.$id.'">';
	$form['action'] = $file_name;
	$form['err'] = $err;
	
	$smarty->assign('form', $form);
	$smarty->assign('data', $data);
	$smarty->assign('button', $lang['button']);
	$smarty->assign('header', $lang['comunicate']);
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_comunicate_form.tpl');
	
	exit;
}

function ComunicateSend()
{
	global $dbconn, $lang, $config, $auth;
	
	$spars = array();
	
	$to			= addslashes($_POST['to']);
	$id			= isset($_POST['id']) ? $_POST['id'] : '';
	$message	= FormFilter($_POST['message']);
	$spars		= isset($_POST['spars']) ? $_POST['spars'] : '';
	
	if (!strlen($to) || !intval($id)) {
		$err = $lang['err']['empty_user'];
		ComunicateForm($id, $err, $to, $message);
	}
	
	if (!strlen($message)) {
		$err = $lang['err']['empty_message'];
		ComunicateForm($id, $err, $to, $message);
	}
	
	$cont_arr			= array();
	$cont_arr['urls']	= GetUserEmailLinks();
	
	$rs = $dbconn->Execute('SELECT fname, sname, email, gender, site_language FROM '.USERS_TABLE.' WHERE id = ?', array($id));
	$cont_arr['fname']	= stripslashes($rs->fields[0]);
	$cont_arr['sname']	= stripslashes($rs->fields[1]);
	$user_email			= stripslashes($rs->fields[2]);
	$gender				= $rs->fields[3];
	$site_lang			= $rs->fields[4];
	$rs->Free();
	
	// include mail language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	// suffix
	$suffix = ($gender == GENDER_MALE) ? '_e' : '_t';
	
	// send internal message
	if (isset($spars[0]) && intval($spars[0]) == 1) {
		$subject = $lang_mail['admin_communicate'.$suffix]['subject_internal'];
		
		$body = $lang_mail['generic'.$suffix]['hello'].' '.$cont_arr['fname'].',<br><br>';
		$body.= nl2br($message).'<br><br>';
		$body.= $lang_mail['generic'.$suffix]['admin_regards'];
		
		$dbconn->Execute(
			'INSERT INTO '.MAILBOX_TABLE.' SET
					id_to = ?, id_from = ?, subject = ?, body = ?, was_read = "0", deleted_to = "0", deleted_from = "0", date_creation = NOW()',
				array($id, $auth[0], $subject, $body));
		
		SendNotice($dbconn->Insert_ID(), $id);
	}
	
	// send external email
	if (isset($spars[1]) && intval($spars[1]) == 1) {
		$subject = $lang_mail['admin_communicate'.$suffix]['subject'];
		$name_to = $cont_arr['fname'].' '.$cont_arr['sname'];
		$cont_arr['message'] = nl2br($message);
		if ($user_email) {
			SendMail($site_lang, $user_email, $config['site_email'], $subject, $cont_arr, 'mail_noti_simple_generic_user', null,
				$name_to, '', 'admin_communicate', $gender);
		}
	}
	
	// send  message in account alerts
	if (isset($spars[2]) && intval($spars[2]) == 1) {
		$subject = $lang_mail['admin_communicate'.$suffix]['subject_alert'];
		$dbconn->Execute(
			'INSERT INTO '.ACCOUNT_ALERTS_TABLE.' SET id_user = ?, date_add = NOW(), subject = ?, body = ?',
				array($id, $subject, nl2br($message)));
	}
	
	echo '<script>window.close(); opener.focus();</script>';
	exit;
}

function SendNotice($id_letter, $id_recipient)
{
	global $config, $dbconn, $auth;
	
	// check subscription
	$strSQL = 'SELECT id_user FROM '.SUBSCRIBE_USER_TABLE.' WHERE type = "s" AND id_subscribe = "4" AND id_user = ?';
	$rs = $dbconn->Execute($strSQL, array($id_recipient));
	if (empty($rs->fields[0])) {
		return;
	}
	$rs->free();
	
	// settings
	$settings = GetSiteSettings(array('icon_male_default', 'icon_female_default', 'icons_default',
					'icons_folder', 'index_theme_path'));
	
	// default icons
	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];
	
	// login token
	$token = CreateToken($id_recipient);
	
	// populate content array
	$content = array();
	
	// message data
	$rs = $dbconn->Execute('SELECT subject FROM '.MAILBOX_TABLE.' WHERE id = ?', array($id_letter));
	$content['subject']		= stripslashes($rs->fields[0]);
	$rs->free();
	
	// date and links
	$content['date']		= date('d-m-Y H:i');
	$content['link_read']	= $config['server'].$config['site_root'].'/mailbox.php?sel=inbox&amp;login_id='.$id_recipient.'&amp;token='.$token;
	$content['urls']		= GetUserEmailLinks();
	
	// sender data
	$rs = $dbconn->Execute(
		'SELECT id, login, fname, sname, email, icon_path
		   FROM '.USERS_TABLE.'
		  WHERE status = "1" AND id = ?',
		array($auth[0]));
	$row = $rs->GetRowAssoc(false);
	$rs->free();
	
	$content['from_login']	= $row['login'];
	$content['from_fname']	= stripslashes($row['fname']);
	$content['from_sname']	= stripslashes($row['sname']);
	
	// user icon
	$icon_path = !empty($row['icon_path']) ? 'big_'.$row['icon_path'] : $default_photos[$row['gender']];
	
	# test cid:agent
	#
#	$content['from_icon']	= 'cid:agent'.$config['server'].$config['site_root'].$settings['icons_folder'].'/'.$icon_path;
	$content['from_icon']	= $config['server'].$config['site_root'].$settings['icons_folder'].'/'.$icon_path;
	
#	$attaches['id'][0] = $config['server'].$config['site_root'].$settings['icons_folder'].'/'.$icon_path;
#	$attaches['image_path'][0] = $config['site_path'].$settings['icons_folder'].'/'.$icon_path;
#	$attaches['image_name'][0] = '';
#	$attaches['image_type'][0] = 'application/octet-stream';
	
	$attaches = null;
	
	unset($row);
	
	// recipient data
	$rs = $dbconn->Execute(
		'SELECT login, fname, sname, email, gender, site_language FROM '.USERS_TABLE.' WHERE id = ?',
		array($id_recipient));
	
	$content['login']		= $rs->fields[0];
	$content['fname']		= stripslashes($rs->fields[1]);
	$content['sname']		= stripslashes($rs->fields[2]);
	$content['email']		= stripslashes($rs->fields[3]);
	$content['gender']		= $rs->fields[4];
	$site_lang				= $rs->fields[5];
	
	$rs->free();
	
	// include mail language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	// suffix
	$suffix = ($content['gender'] == GENDER_MALE) ? '_e' : '_t';
	
	// subject
	$subject = $lang_mail['mailbox_subscribe'.$suffix]['subject'];
	
	// recipient
	$name_to = trim($content['fname'].' '.$content['sname']);
	
	SendMail($site_lang, $content['email'], $config['site_email'], $subject, $content, 'mail_mailbox_subscribe_user',
		$attaches, $name_to, '', 'mailbox_subscribe', $content['gender']);
	
	return;
}

?>