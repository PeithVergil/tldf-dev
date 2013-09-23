<?php
/**
* Platinum Match
*
* @package DatingPro
* @subpackage User Mode
**/

include './include/config.php';
include './common.php';
include './include/config_index.php';
include './include/config_admin.php';
include './include/functions_auth.php';
include './include/functions_index.php';
include './include/class.lang.php';
include './include/class.percent.php';
include './include/class.images.php';
include './include/functions_users.php';
include './include/class.phpmailer.php';
include './include/functions_mail.php';
include './include/functions_mm.php';

// authentication
$user = auth_index_user();

if (empty($user) || $user == 'err' || empty($user[ AUTH_ID_USER ])) {
	header('location: '.$config['site_root'].'/index.php');
	exit;
}

// check guest
// (handled by permissions)

// check group, period, expiration
RefreshAccount();

// check status
// (public access)

// check permissions
IsFileAllowed(GetRightModulePath(__FILE__));

// alerts and statistics
if (!$user[ AUTH_GUEST ]) {
	GetAlertsMessage();
	SetModuleStatistic(GetRightModulePath(__FILE__));
}

// active menu item
$smarty->assign('sub_menu_num', '5');

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

// dispatcher
switch ($sel) {
	case 'ajaxsend':
		PlatinumMatchSubmit('ajax');
	break;
	
	default:
		PlatinumMatch();
	break;
}

exit;


function PlatinumMatch($err = '', $err_field = array(), $data = array(), $type = '')
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	// settings
	## $settings = GetSiteSettings(array('min_age_limit', 'max_age_limit', 'zip_letters', 'zip_count', 'date_format'));
	
	if ($type != 'ajax') {
		if (!$user[ AUTH_GUEST ]) {
			GetAlertsMessage();
		}
		Banners(GetRightModulePath(__FILE__));
		IndexHomePage();
	}
	
	GetActiveUserInfo($user);
	
	$usr_gender = ($user[AUTH_GENDER] == GENDER_FEMALE) ? 'lady' : 'guy';
	$smarty->assign('usr_gender', $usr_gender);
	
	if ($type != 'ajax') {
		$platinum_price = $dbconn->getOne('SELECT cost FROM '.GROUP_PERIOD_TABLE.' WHERE id = ?', array(MM_PLATINUM_GUY_PERIOD_ID));
		$form['platinum_price'] = round($platinum_price, 2);
	}
	
	// default data
	if (empty($data) && !$user[ AUTH_GUEST] ) {
		
		// data from global $user array
		$data['fname']		= isset($user[ AUTH_FNAME ])		? $user[ AUTH_FNAME ] : '';
		$data['sname']		= isset($user[ AUTH_SNAME ])		? $user[ AUTH_SNAME ] : '';
		$data['city']		= isset($user[ AUTH_CITY ])			? $user[ AUTH_CITY ] : '';
		$data['id_country'] = isset($user[ AUTH_ID_COUNTRY ])	? $user[ AUTH_ID_COUNTRY ] : '';
		$data['phone']		= isset($user[ AUTH_PHONE ])		? $user[ AUTH_PHONE ] : '';
		$data['email']		= isset($user[ AUTH_EMAIL ])		? $user[ AUTH_EMAIL ] : '';
		// additional data from user table
		$rs = $dbconn->Execute('SELECT mm_platinum_submit, mm_first_installment_date, mm_second_installment_date FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
		$row = $rs->GetRowAssoc(false);
		$data['mm_platinum_submit'] = $row['mm_platinum_submit'];
		if ($data['mm_platinum_submit']) {
			$smarty->assign('is_submit', true);
		}
		
		//Added for installments By Narendra
		if (!empty($row['mm_first_installment_date']) && !empty($row['mm_second_installment_date'])) {
			$installment_lang = $lang["platinum_match"]["lady"]["feature_head_2_3_installment"];
			$disable_platinum = true;
		} elseif (!empty($row['mm_first_installment_date']) && empty($row['mm_second_installment_date'])) {
			$installment_lang = $lang["platinum_match"]["lady"]["feature_head_2_2_installment"];
			$disable_platinum = true;
		} elseif (empty($row['mm_first_installment_date']) && empty($row['mm_second_installment_date'])) {
			$installment_lang = $lang["platinum_match"]["lady"]["feature_head_2_1_installment"];
			$disable_platinum = false;
		}
		
		$smarty->assign('installment_cnt', $installment_lang);
		$smarty->assign('disable_platinum', $disable_platinum);
		unset($row);
		$rs->free();
	}
	
	// country select
	$rs = $dbconn->Execute('SELECT id, name AS value FROM '.COUNTRY_SPR_TABLE.' ORDER BY name');
	$i = 0;
	$country_arr = array();
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$country_arr[$i] = $row;
		// exclude USA and Australia, they are the first items in the select and we handle this in the template
		if (isset($data['id_country']) && $data['id_country'] != 251 && $data['id_country'] != 14 && $data['id_country'] == $row['id']) {
			$country_arr[$i]['sel'] = 1;
		}
		$rs->MoveNext();
		$i++;
	}
	$smarty->assign('country_arr', $country_arr);
	
	$form['err'] = $err;
	$form['err_field'] = $err_field;
	
	$smarty->assign('data', $data);
	$smarty->assign('form', $form);
	$smarty->assign('header', $lang['profile']);
	
	if ($type == 'ajax') {
		$smarty->display(TrimSlash($config['index_theme_path']).'/inline_platinum_match.tpl');
	} else {
		$smarty->display(TrimSlash($config['index_theme_path']).'/platinum_match.tpl');
	}
}

function PlatinumMatchSubmit($type = '')
{
	global $lang, $config, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$data = array();
	
	$data['fname']		= FormFilter($_POST['fname']);
	$data['sname']		= FormFilter($_POST['sname']);
	$data['city']		= FormFilter($_POST['city']);
	$data['id_country']	= FormFilter($_POST['id_country']);
	$data['phone']		= FormFilter($_POST['phone']);
	$data['email']		= FormFilter($_POST['email']);
	$data['calltime']	= FormFilter($_POST['calltime']);
	$data['comments']	= FormFilter($_POST['comments']);
	
	$usr_gender = ($user[AUTH_GENDER] == GENDER_FEMALE) ? 'lady' : 'guy';
	
	// Ajax test
	/*
	$msg  = 'fname: '.$data['fname'].'<br>';
	$msg .= 'sname: '.$data['sname'].'<br>';
	$msg .= 'city: '.$data['city'].'<br>';
	$msg .= 'id_country: '.$data['id_country'].'<br>';
	$msg .= 'phone: '.$data['phone'].'<br>';
	$msg .= 'email: '.$data['email'].'<br>';
	$msg .= 'calltime: '.$data['calltime'].'<br>';
	$msg .= 'comments: '.$data['comments'].'<br>';
	PlatinumMatch($msg);
	return;
	*/
	// Ajax test end
	
	$err  = array();
	$err_field = array();
	
	if (!strlen($data['fname'])) {
		$err[] = $lang['platinum_match'][$usr_gender]['fname'];
		$err_field['fname'] = 1;
	}
	
	if (!strlen($data['sname'])) {
		$err[] = $lang['platinum_match'][$usr_gender]['sname'];
		$err_field['sname'] = 1;
	}
	
	if (!strlen($data['city'])) {
		$err[] = $lang['platinum_match'][$usr_gender]['city'];
		$err_field['city'] = 1;
	}
	
	if (!strlen($data['id_country'])) {
		$err[] = $lang['platinum_match'][$usr_gender]['id_country'];
		$err_field['id_country'] = 1;
	}
	
	if (!strlen($data['phone'])) {
		$err[] = $lang['platinum_match'][$usr_gender]['phone'];
		$err_field['phone'] = 1;
	}
	
	if (!strlen($data['email'])) {
		$err[] = $lang['platinum_match'][$usr_gender]['email'];
		$err_field['email'] = 1;
	} else {
		$email_err = EmailFilter($data['email']);
		if ($email_err) {
			$err[] = $email_err;
			$err_field['email'] = 1;
		}
	}
	
	if (!strlen($data['calltime'])){
		$err[] = $lang['platinum_match'][$usr_gender]['calltime'];
		$err_field['calltime'] = 1;
	}
	
	if (!empty($err)) {
		$error = $lang['err']['invalid_fields'].'<br>'.implode(', ', $err);
		PlatinumMatch($error, $err_field, $data, $type);
		return;
	}
	
	/**
	 * update user data
	 **/
	
	// don't update best_call_time fields because there is only one input field
	/*
		mm_best_call_time_weekdays	= "' . $mm_best_call_time_weekdays . '",
		mm_best_call_time_saturdays	= "' . $mm_best_call_time_saturdays . '",
		mm_best_call_time_sundays	= "' . $mm_best_call_time_sundays . '",
	*/
	
	$strSQL =
		'UPDATE '.USERS_TABLE.'
			SET fname = ?, sname = ?, email = ?, mm_city = ?, id_country = ?,
				mm_contact_phone_number = ?, mm_platinum_submit = NOW()
		  WHERE id = ?';
	
	$dbconn->Execute($strSQL, array(
		$data['fname'], $data['sname'], $data['email'], $data['city'],
		$data['id_country'], $data['phone'], $id_user
	));
	
	// additional content
	$data['country'] = $dbconn->GetOne('SELECT name FROM '.COUNTRY_SPR_TABLE.' WHERE id = ?', array($data['id_country']));
	
	// update global $user array
	$user[ AUTH_FNAME ]			= $data['fname'];
	$user[ AUTH_SNAME ]			= $data['sname'];
	$user[ AUTH_EMAIL ]			= $data['email'];
	$user[ AUTH_CITY ]			= $data['city'];
	$user[ AUTH_ID_COUNTRY ]	= $data['id_country'];
	$user[ AUTH_PHONE ]			= $data['phone'];
	
	// UPDATE SOLVE360 CONTACT
	if (SOLVE360_CONNECTION) {
		require_once $config['site_path'].'/include/Solve360Service.php';
		$solve360Service = new Solve360Service(SOLVE360_USER, SOLVE360_TOKEN);
		
		$solve360 = array();
		require $config['site_path'].'/include/Solve360CustomFields.php';
		
		$contactData = array(
			'firstname'								=> $data['fname'],
			'lastname'								=> $data['sname'],
			'personalemail'							=> $data['email'],
			$solve360['Country']					=> $data['country'],
			$solve360['Last Seen TLDF']				=> date('Y-m-d H:i:s'),
			'homephone'								=> $data['phone'],
			$solve360['City']						=> $data['city'],
			$solve360['Platinum Form']				=> date('Y-m-d H:i:s'),
		);
		
		if (!empty($user[ AUTH_ID_SOLVE360 ])) {
			$contact = $solve360Service->editContact($user[ AUTH_ID_SOLVE360 ], $contactData);
			#var_dump($contact); exit;
			if (isset($contact->errors)) {
				$subject = 'Error while updating contact after user submits Platinum Application Form';
				solve360_api_error($contact, $subject, $user[ AUTH_LOGIN ]);
			}
		}
		// maybe add contact if not found
	}
	
	/**
	 * notification emails
	 **/
	
	// language
	$site_lang = $config['default_lang'];

	// include language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	$data['urls']	= GetUserEmailLinks();
	
	/**
	 * email to user
	 **/
	
	// gender suffix
	$suffix = ($user[ AUTH_GENDER ] == GENDER_MALE) ? '_e' : '_t';
	
	// subject
	$subject = $lang_mail['platinum_match_application'.$suffix]['subject'];
	
	// message
	$data['message'] = $lang_mail['platinum_match_application'.$suffix]['message'];
	
	// recipient
	$email_to	= html_entity_decode($data['email']);
	$name_to	= html_entity_decode(trim($data['fname'].' '.$data['sname']));
	
	$err = SendMail($site_lang, $email_to, $config['site_email'], $subject, $data,
		'mail_noti_simple_generic_user', null, $name_to, '', 'platinum_match_application', $user[ AUTH_GENDER ]);
	
	if ($err){
		PlatinumMatch($err, array(), $data, $type);
		return;
	}
	
	/**
	 * email to admin
	 **/
	
	// subject
	$subject = $lang_mail['platinum_match_application_admin']['subject'];
	
	// sender
	$email_from	= html_entity_decode($data['email']);
	$name_from	= html_entity_decode(trim($data['fname'].' '.$data['sname']));
	
	// recipient
	if (REDIRECT_ADMIN_EMAIL && !IS_LIVE_SERVER) {
		$email_to = REDIRECT_ADMIN_EMAIL_TO;
	} else {
		$email_to = ADMIN_EMAIL_PLAT_APPLIED;
	}
	
	$err = SendMail($site_lang, $email_to, $email_from, $subject, $data,
		'mail_platinum_match_application_admin', null, '', $name_from, 'platinum_match_application_admin');
	
	if ($err){
		PlatinumMatch($err, array(), $data, $type);
		return;
	}
	
	// GA_TRACKING
	$_SESSION['ga_event_code'] = 'platinummatching';
	
	PlatinumMatch($lang['err']['platinum_send'], array(), array(), $type);
	return;
}
?>