<?php
/**
* Dating Events Page
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
include './include/class.help_info.php';

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
$smarty->assign('sub_menu_num', '6');

$local_config['category_table'] = HELP_CATEGORIES_TABLE;
$local_config['category_table_key'] = 4;
$local_config['item_table'] = HELP_QUESTIONS_TABLE;
$local_config['item_table_key'] = 5;
$local_config['use_status'] = '1';

$file_name = 'dating_events.php';

global $helpinfo;
$helpinfo = new HelpInfo($config, $dbconn, $file_name, $local_config);

define('TLDEVENTS_FAQ_CAT_ID', 46);

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

// dispatcher
switch ($sel) {
	case 'question': DatingQuestionForm(); break;
	case 'quesend':  DatingQuestionSend(); break;
	case 'comment':  VideoCommentForm(); break;
	case 'sendcomm': VideoCommentSend(); break;
	default: DatingEventsTable();
}

exit;


function DatingEventsTable($err = '')
{
	global $config, $smarty, $user, $helpinfo;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$form['err'] = $err;
	$form['express_interest'] = './express_interest.php';
	
	$smarty->assign('form', $form);
	$smarty->assign('datingfaqs', $helpinfo->GetItemsList(TLDEVENTS_FAQ_CAT_ID, 0));
	$smarty->display(TrimSlash($config['index_theme_path']).'/dating_events_table.tpl');
	exit;
}

function DatingQuestionForm($err = '', $data = array(), $err_field = array())
{
	global $config, $smarty, $user;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	if (empty($data) && !$user[ AUTH_GUEST] ) {
		$data['fname'] = isset($user[ AUTH_FNAME ]) ? $user[ AUTH_FNAME ] : '';
		$data['sname'] = isset($user[ AUTH_SNAME ]) ? $user[ AUTH_SNAME ] : '';
		$data['email'] = isset($user[ AUTH_EMAIL ]) ? $user[ AUTH_EMAIL ] : '';
	}
	
	$form['err'] = $err;
	
	$smarty->assign('form', $form);
	$smarty->assign('data', $data);
	$smarty->assign('err_field', $err_field);
	$smarty->display(TrimSlash($config['index_theme_path']).'/inline_ask_dating_question.tpl');
	exit;
}

function DatingQuestionSend()
{
	global $lang, $config, $dbconn, $user;
	
	$data = array();
	
	$data['fname']		= FormFilter($_POST['fname']);
	$data['sname']		= FormFilter($_POST['sname']);
	$data['email']		= FormFilter($_POST['email']);
	$data['question']	= FormFilter($_POST['question']);
	
	/* Ajax test
	$msg  = 'First Nname: '.$data['fname'].'<br>';
	$msg .= 'Last Name: '.$data['sname'].'<br>';
	$msg .= 'Email: '.$data['email'].'<br>';
	$msg .= 'Question: '.$data['question'].'<br>';
	
	DatingQuestionForm($msg);
	return;
	*/
	
	$err = array();
	$err_field = array();
	
	if (!strlen($data['fname'])) {
		$err[] = $lang['dating_events_faq']['fname'];
		$err_field['fname'] = 1;
	}
	
	if (!strlen($data['sname'])) {
		$err[] = $lang['dating_events_faq']['sname'];
		$err_field['sname'] = 1;
	}
	
	if (!strlen($data['email'])) {
		$err[] = $lang['dating_events_faq']['email'];
		$err_field['email'] = 1;
	} elseif ($email_err = EmailFilter($data['email'])) {
		$err[] = $email_err;
		$err_field['email'] = 1;
	}
	
	if (!strlen($data['question'])){
		$err[] = $lang['dating_events_faq']['question'];
		$err_field['question'] = 1;
	}
	
	if (!empty($err)) {
		$error = $lang['err']['invalid_fields'].'<br><br>'.implode('<br>', $err);
		DatingQuestionForm($error, $data, $err_field);
		return;
	}
	
	/**
	 * notification emails
	 **/
	
	$site_lang = $config['default_lang'];
	
	// include mail language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	/**
	 * email to admin
	 **/
	
	// subject
	$subject	= $lang_mail['dating_events_faq_admin']['subject'];
	
	// sender
	$email_from	= html_entity_decode($data['email']);
	$name_from	= html_entity_decode(trim($data['fname'].' '.$data['sname']));
	
	// recipient
	if (REDIRECT_ADMIN_EMAIL && !IS_LIVE_SERVER) {
		$email_to = REDIRECT_ADMIN_EMAIL_TO;
	} else {
		$email_to = EMAIL_DATING_EVENTS_ADMIN;
	}
	
	$mail_err = SendMail($site_lang, $email_to, $email_from, $subject, $data,
		'mail_dating_events_faq_admin', null, '', $name_from, 'dating_events_faq_admin');
	
	if (!$mail_err) {
		$error = $lang['err']['dating_events_faq_send'];
	} else {
		$error = $lang['err']['dating_events_faq_error'];
	}
	
	/**
	 * email to user
	 **/
	
	$data['adminemail'] = EMAIL_DATING_EVENTS_ADMIN;
	$data['urls']		= GetUserEmailLinks();
	
	// language_suffix
	$suffix = ($user[ AUTH_GUEST ] || $user[ AUTH_GENDER ] == GENDER_MALE) ? '_e' : '_t';
	
	// subject
	$subject	= $lang_mail['dating_events_faq'.$suffix]['subject'];
	
	// recipient
	$email_to	= html_entity_decode($data['email']);
	$name_to	= html_entity_decode(trim($data['fname'].' '.$data['sname']));
	
	// gender
	$gender		= $user[ AUTH_GUEST ] ? GENDER_MALE : $user[ AUTH_GENDER ];
	
	$mail_err = SendMail($site_lang, $email_to, $config['site_email'], $subject, $data,
		'mail_dating_events_faq_user', null, $name_to, '', 'dating_events_faq', $gender);
	
	if (!$mail_err) {
		$error = $lang['err']['dating_events_faq_send'];
	} else {
		$error = $lang['err']['dating_events_faq_error'];
	}
	
	if (!$mail_err) {
		$data = array();
	}
	
	// display form
	DatingQuestionForm($error, $data);
	return;
}

function VideoCommentForm($err='', $data=array(), $err_field=array())
{
	global $config, $smarty, $user;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$vid = isset($_POST['vid']) ? $_POST['vid'] : (isset($_GET['vid']) ? $_GET['vid'] : '');
	
	if (empty($data) && !$user[ AUTH_GUEST] ) {
		$data['fname'] = isset($user[ AUTH_FNAME ]) ? $user[ AUTH_FNAME ] : '';
		$data['sname'] = isset($user[ AUTH_SNAME ]) ? $user[ AUTH_SNAME ] : '';
		$data['email'] = isset($user[ AUTH_EMAIL ]) ? $user[ AUTH_EMAIL ] : '';
	}
	
	$form['vid'] = $vid;
	$form['err'] = $err;
	
	$smarty->assign('form', $form);
	$smarty->assign('data', $data);
	$smarty->assign('err_field', $err_field);
	$smarty->display(TrimSlash($config['index_theme_path']).'/inline_dating_video_comment.tpl');
	exit;
}

function VideoCommentSend()
{
	global $lang, $config, $dbconn, $user;
	
	$data = array();
	
	$data['videoid']	= FormFilter($_POST['videoid']);
	$data['fname']		= FormFilter($_POST['fname']);
	$data['sname']		= FormFilter($_POST['sname']);
	$data['email']		= FormFilter($_POST['email']);
	$data['comment']	= FormFilter($_POST['comment']);
	
	/* Ajax test
	$msg  = 'First Nname: '.$data['fname'].'<br>';
	$msg .= 'Last Name: '.$data['sname'].'<br>';
	$msg .= 'Email: '.$data['email'].'<br>';
	$msg .= 'Comment: '.$data['comment'].'<br>';
	
	VideoCommentForm($msg);
	return;
	*/
	
	$err = array();
	$err_field = array();
	
	if (!strlen($data['fname'])) {
		$err[] = $lang['dating_video_comment']['fname'];
		$err_field['fname'] = 1;
	}
	
	if (!strlen($data['sname'])) {
		$err[] = $lang['dating_video_comment']['sname'];
		$err_field['sname'] = 1;
	}
	
	if (!strlen($data['email'])) {
		$err[] = $lang['dating_video_comment']['email'];
		$err_field['email'] = 1;
	} elseif ($email_err = EmailFilter($data['email'])) {
		$err[] = $email_err;
		$err_field['email'] = 1;
	}
	
	if (!strlen($data['comment'])){
		$err[] = $lang['dating_video_comment']['comment'];
		$err_field['comment'] = 1;
	}
	
	if (!empty($err)) {
		$error = $lang['err']['invalid_fields'].'<br><br>'.implode('<br>', $err);
		VideoCommentForm($error, $data, $err_field);
		return;
	}
	
	switch ($data['videoid']) {
		case 1: $data['video'] = $lang['dating_video']['title1']; break;
		case 2: $data['video'] = $lang['dating_video']['title2']; break;
		case 3: $data['video'] = $lang['dating_video']['title3']; break;
	}
	
	/**
	 * notification emails
	 **/
	
	// language
	$site_lang = $config['default_lang'];
	
	// include mail language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	/**
	 * email to admin
	 **/
	
	// subject
	$subject	= $lang_mail['dating_video_comment_admin']['subject'];
	
	// sender
	$email_from	= html_entity_decode($data['email']);
	$name_from	= html_entity_decode(trim($data['fname'].' '.$data['sname']));
	
	// recipient
	if (REDIRECT_ADMIN_EMAIL && !IS_LIVE_SERVER) {
		$email_to = REDIRECT_ADMIN_EMAIL_TO;
	} else {
		$email_to = EMAIL_DATING_EVENTS_ADMIN;
	}
	
	$mail_err = SendMail($site_lang, $email_to, $email_from, $subject, $data,
		'mail_dating_video_comment_admin', null, '', $name_from, 'dating_video_comment_admin');
	
	if (!$mail_err) {
		$error = $lang['err']['dating_video_comment_send'];
	} else {
		$error = $lang['err']['dating_video_comment_error'];
	}
	
	/**
	 * email to user
	 **/
	
	$data['adminemail'] = EMAIL_DATING_EVENTS_ADMIN;
	$data['urls']		= GetUserEmailLinks();
	
	// language suffix
	$suffix		= ($user[ AUTH_GUEST ] || $user[ AUTH_GENDER ] == GENDER_MALE) ? '_e' : '_t';
	
	// subject
	$subject	= $lang_mail['dating_video_comment'.$suffix]['subject'];
	
	// recipient
	$email_to	= html_entity_decode($data['email']);
	$name_to	= html_entity_decode(trim($data['fname'].' '.$data['sname']));
	
	// gender
	$gender		= $user[ AUTH_GUEST ] ? GENDER_MALE : $user[ AUTH_GENDER ];
	
	$mail_err = SendMail($site_lang, $email_to, $config['site_email'], $subject, $data,
		'mail_dating_video_comment_user', null, $name_to, '', 'dating_video_comment', $gender);
	
	if (!$mail_err) {
		$error = $lang['err']['dating_video_comment_send'];
	} else {
		$error = $lang['err']['dating_video_comment_error'];
	}
	
	if (!$mail_err) {
		$data  = array();
	}
	
	VideoCommentForm($error, $data);
	return;
}

?>