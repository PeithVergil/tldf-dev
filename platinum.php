<?php
/**
* Apply For Platinum Verification
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
// (public access)

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
	case 'save':
		SaveProfile();
	break;
	
	case 'apply_now':
		ApplyNow();
	break;
	
	default:
		ViewPlatinumForm();
	break;
}

exit;


function ViewPlatinumForm($err='',$data='')
{
	global $lang, $config, $smarty, $dbconn, $user, $sel;
	
	// settings
	$settings = GetSiteSettings(array('icon_male_default', 'icon_female_default', 'icons_folder', 'min_age_limit', 'max_age_limit', 'zip_letters', 'zip_count', 'date_format'));
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$file_name = 'platinum.php';
	
	$is_submit = CheckIsPlatinumSubmit();	// functions_mm
	$is_paid = CheckIsPlatinumPaid();		// functions_mm
	$is_applied = CheckIsPlatinumApply();	// functions_mm
	
	$smarty->assign('is_submit', $is_submit);
	$smarty->assign('is_paid', $is_paid);
	$smarty->assign('is_applied', $is_applied);
	
	$usr_gender = intval($user[ AUTH_GENDER ]) == GENDER_MALE ? 'guy' : 'lady';
	$smarty->assign('usr_gender', $usr_gender);
	
	if (isset($_GET['from']))
	{
		if ($_GET['from'] == 'pay_platinum')
		{
			$err = GetPaymentSuccessMessage();
			if ($err != 'payment_failure') {
				echo '<script>location.href="./platinum.php?sel=apply_now";</script>';
				exit;
			}
		}
		elseif ($_GET['from'] == 'check_platinum_payment')
		{
			$err = GetPaymentCheckMessage();
		}
	}
	
	if ($data)
	{
		// country select
		$rs = $dbconn->Execute('SELECT id, name AS value FROM '.COUNTRY_SPR_TABLE.' ORDER BY name');
		$i = 0;
		$country_arr = array();
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$country_arr[$i] = $row;
			$country_arr[$i]['sel'] = (intval($data['id_country']) == $row['id']) ? 1 : 0;
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign('country', $country_arr);
		
		// region select
		if (isset($data['id_country'])) {
			$rs = $dbconn->Execute('SELECT id, name AS value FROM '.REGION_SPR_TABLE.' WHERE id_country = ? ORDER BY name', array($data['id_country']));
			$i = 0;
			$region_arr = array();
			while (!$rs->EOF) {
				$row = $rs->GetRowAssoc(false);
				$region_arr[$i] = $row;
				$region_arr[$i]['sel'] = (intval($data['id_region']) == $row['id']) ? 1 : 0;
				$rs->MoveNext();
				$i++;
			}
			$smarty->assign('region', $region_arr);
		}
		
		// city select
		if (isset($data['id_region'])) {
			$strSQL = 'SELECT id, name AS value FROM '.CITY_SPR_TABLE.' WHERE id_region = ? AND id_country = ? GROUP BY id ORDER BY name';
			$rs = $dbconn->Execute($strSQL, array($data['id_region'], $data['id_country']));
			$i = 0;
			$city_arr = array();
			while (!$rs->EOF) {
				$row = $rs->GetRowAssoc(false);
				$city_arr[$i] = $row;
				//$city_arr[$i]['sel'] = (intval($data['id_city']) == $row['id']) ? 1 : 0;
				$rs->MoveNext();
				$i++;
			}
			$smarty->assign('city', $city_arr);
		}
		
		//birthday
		$day = GetDaySelect($data['b_day']);
		$month = GetMonthSelect($data['b_month']);
		$year = GetYearSelect($data['b_year'], ($settings['max_age_limit']-$settings['min_age_limit']), (intval(date('Y'))-$settings['min_age_limit']));
	
		$date_parts = explode('%', $settings['date_format']);
		
		for ($i = 1; $i < count($date_parts); $i++)
		{
			switch ($date_parts[$i][0])
			{
				case 'm':
				case 'c':
					$smarty->assign('date_part'.$i, $month);
					$smarty->assign('date_part'.$i.'_name', 'month');
					$smarty->assign('date_part'.$i.'_default', 'MMM');
				break;
				
				case 'd':
				case 'e':
					$smarty->assign('date_part'.$i, $day);
					$smarty->assign('date_part'.$i.'_name', 'day');
					$smarty->assign('date_part'.$i.'_default', 'DD');
				break;
				
				case 'Y':
				case 'y':
					$smarty->assign('date_part'.$i, $year);
					$smarty->assign('date_part'.$i.'_name', 'year');
					$smarty->assign('date_part'.$i.'_default', 'YYYY');
				break;
			}
		}
		$smarty->assign('data', $data);
	}
	else
	{
		$form = PersonalForm();
		
		if (!$err) {
			if ($is_applied) {
				$err = $lang['err']['applied_for_platinum'];
			} elseif (!$is_submit) {
				//VP $err = $lang['err']['missing_platinum_information'];
			} elseif (!$is_paid) {
				$err = $lang['err']['missing_platinum_fee'];
			} else {
				$err = $lang['err']['apply_for_platinum'];
			}
		}
	}
	
	if ($_GET['sel'] == 'congrat') {
		$congrat = true;
	}
	
	$form['err'] = $err;
	$form['action'] = $file_name;
	$smarty->assign('sel', $sel);
	$smarty->assign('congrat', $congrat);
	$smarty->assign('form', $form);
	$smarty->assign('header', $lang['profile']);
	$smarty->display(TrimSlash($config['index_theme_path']).'/platinum_table.tpl');
}

function GetPaymentSuccessMessage()
{
	global $dbconn, $lang, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$settings = GetSiteSettings(array('must_approve_payment_before_verify'));
	
	$err = '';
	
	// check for confirmed payment (online or offline)
	$date_paid = $dbconn->GetOne('SELECT mm_platinum_paid FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
	
	if (!empty($date_paid)) {
		return $lang['err']['platinum_payment_success'];
	}
	
	// check for unconfirmed offline payment
	$id_group = ($user[ AUTH_GENDER ] == GENDER_MALE) ? MM_PLATINUM_GUY_ID : MM_PLATINUM_LADY_ID;
	
	$strSQL =
		'SELECT paysystem
		   FROM '.BILLING_REQUESTS_TABLE.'
		  WHERE id_user = ? AND id_group = ?
			AND status = "send"
			AND paysystem IN ("atm_payment", "wire_transfer", "bank_cheque")';
	
	$paysystem = $dbconn->getOne($strSQL, array($id_user, $id_group));
	
	if (!empty($paysystem))
	{
		$err = $lang['err']['platinum_payment_success_offline'][$paysystem];
		if ($settings['must_approve_payment_before_verify'] == '1') {
			// show stopper
			$err.= '<br><br>'.$lang['err']['offline_payment_wait_for_platinum_submit'];
		} else {
			$err.= '<br><br>'.$lang['err']['offline_payment_continue_platinum_application'];
		}
	}
	else
	{
		//VP $err = $lang['err']['platinum_payment_failure'];
		$err = 'payment_failure';
	}
	
	return $err;
}


function GetPaymentCheckMessage()
{
	global $dbconn, $lang, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$settings = GetSiteSettings(array('must_approve_payment_before_verify'));
	
	$err = '';
	
	$rs = $dbconn->Execute('SELECT mm_platinum_paid, mm_platinum_applied FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
	$row = $rs->GetRowAssoc(false);
	
	if (!empty($row['mm_platinum_paid']) && !empty($row['mm_platinum_applied'])) {
		return $lang['err']['platinum_already_paid_and_submitted'];
	}
	
	if (!empty($row['mm_platinum_paid'])) {
		return $lang['err']['platinum_already_paid'];
	}
	
	// check for offline payment
	//
	$id_group = ($user[ AUTH_GENDER ] == GENDER_MALE) ? MM_PLATINUM_GUY_ID : MM_PLATINUM_LADY_ID;
	
	$strSQL =
		'SELECT paysystem
		   FROM '.BILLING_REQUESTS_TABLE.'
		  WHERE id_user = ? AND id_group = ?
			AND status = "send"
			AND paysystem IN ("atm_payment", "wire_transfer", "bank_cheque")';
	
	$paysystem = $dbconn->getOne($strSQL, array($id_user, $id_group));
	
	if (!empty($paysystem))
	{
		if (!empty($row['mm_platinum_applied']))
		{
			$err = $lang['err']['platinum_already_paid_and_submitted'][$paysystem];
			$err.= '<br><br>'.$lang['err']['offline_payment_wait_for_platinum_verification'];
		}
		else
		{
			$err = $lang['err']['platinum_already_paid_offline'][$paysystem];
			
			if ($settings['must_approve_payment_before_verify'] == '1') {
				// show stopper
				$err .= '<br><br>'.$lang['err']['offline_payment_wait_for_platinum_submit'];
			} else {
				$err .= '<br><br>'.$lang['err']['offline_payment_continue_platinum_application'];
			}
		}
	}
	
	return $err;
}

function SaveProfile()
{
	global $lang, $smarty, $dbconn, $user;
	
	$debug = false;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$data = $_POST;
	
	$check_confirm					= isset($data['check_confirm']) ? FormFilter($data['check_confirm']) : '';
	$mm_best_call_time_weekdays		= isset($data['mm_best_call_time_weekdays']) ? FormFilter($data['mm_best_call_time_weekdays']) : '';
	$mm_best_call_time_saturdays	= isset($data['mm_best_call_time_saturdays']) ? FormFilter($data['mm_best_call_time_saturdays']) : '';
	$mm_best_call_time_sundays		= isset($data['mm_best_call_time_sundays']) ? FormFilter($data['mm_best_call_time_sundays']) : '';
	
	// not used in this function
	#$fname							= isset($data['fname']) ? FormFilter($data['fname']) : '';
	#$sname							= isset($data['sname']) ? FormFilter($data['sname']) : '';
	$email							= isset($data['email']) ? FormFilter($data['email']) : '';
	$b_year							= intval($data['b_year']);
	$b_month						= intval($data['b_month']);
	$b_day							= intval($data['b_day']);
	$mm_place_of_birth				= isset($data['mm_place_of_birth']) ? FormFilter($data['mm_place_of_birth']) : '';
	$mm_id_number					= isset($data['mm_id_number']) ? FormFilter($data['mm_id_number']) : '';
	$mm_id_type						= isset($data['mm_id_type']) ? FormFilter($data['mm_id_type']) : '';
	$mm_contact_phone_number		= isset($data['mm_contact_phone_number']) ? FormFilter($data['mm_contact_phone_number']) : '';
	$mm_contact_mobile_number		= isset($data['mm_contact_mobile_number']) ? FormFilter($data['mm_contact_mobile_number']) : '';
	$id_country						= isset($data['id_country']) ? intval($data['id_country']) : 0;
	$id_region						= isset($data['id_region']) ? intval($data['id_region']) : 0;
	//$id_city						= isset($data['id_city']) ? intval($data['id_city']) : 0;
	$mm_city						= isset($data['mm_city']) ? FormFilter($data['mm_city']) : '';
	$zipcode						= isset($data['zipcode']) ? FormFilter($data['zipcode']) : '';
	$mm_address_1					= isset($data['mm_address_1']) ? FormFilter($data['mm_address_1']) : '';
	$mm_address_2					= isset($data['mm_address_2']) ? FormFilter($data['mm_address_2']) : '';
	$mm_address_3					= isset($data['mm_address_3']) ? FormFilter($data['mm_address_3']) : '';
	$mm_platinum_submit_comment		= isset($data['mm_platinum_submit_comment']) ? FormFilter($data['mm_platinum_submit_comment']) : '';
		
	//----------------
	// validity check
	//----------------
	
	$err = '';
	$err_field = array();
	
	if (!strlen($check_confirm)) {
		$err .= $lang['apply_platinum']['err_check_confirm'] . ', ';
		$err_field['check_confirm'] = 1;
	}
	
	if (!strlen($mm_best_call_time_weekdays)) {
		$err .= $lang['apply_platinum']['best_call_time_weekdays'] . ', ';
		$err_field['mm_best_call_time_weekdays'] = 1;
	}
	
	if (!strlen($mm_best_call_time_saturdays)) {
		$err .= $lang['apply_platinum']['best_call_time_saturdays'] . ', ';
		$err_field['mm_best_call_time_saturdays'] = 1;
	}
	
	if (!strlen($mm_best_call_time_sundays)) {
		$err .= $lang['apply_platinum']['best_call_time_sundays'] . ', ';
		$err_field['mm_best_call_time_sundays'] = 1;
	}
	
	if (!strlen($email)) {
		$err .= $lang['users']['email'] . ', ';
		$err_field['email'] = 1;
	} else {
		// email validation
		if (EmailFilter($email)) {
			$err .= EmailFilter($email);
			$err_field['email'] = 1;
		}
	}
	
	if (!strlen($mm_place_of_birth)) {
		$err .= $lang['users']['mm_place_of_birth'] . ', ';
		$err_field['mm_place_of_birth'] = 1;
	}
	
	if (!strlen($mm_id_number)) {
		$err .= $lang['users']['mm_id_number'] . ', ';
		$err_field['mm_id_number'] = 1;
	}
	
	if (!strlen($mm_id_type)) {
		$err .= $lang['users']['mm_id_type'] . ', ';
		$err_field['mm_id_type'] = 1;
	}
	
	if (!strlen($mm_contact_phone_number)) {
		$err .= $lang['users']['mm_contact_phone_number'] . ', ';
		$err_field['mm_contact_phone_number'] = 1;
	}
	
	if (!strlen($mm_contact_mobile_number)) {
		$err .= $lang['users']['mm_contact_mobile_number'] . ', ';
		$err_field['mm_contact_mobile_number'] = 1;
	}
	
	if (!$id_country) {
		$err .= $lang['users']['country'] . ', ';
		$err_field['id_country'] = 1;
	}
	
	/*
	if (!$id_region) {
		$err .= $lang['users']['region'] . ', ';
		$err_field['id_region'] = 1;
	}
	
	if (!$id_city) {
		$err .= $lang['users']['city'] . ', ';
		$err_field['id_city'] = 1;
	}
	*/
	
	if (!strlen($mm_city)) {
		$err .= $lang['users']['city'] . ', ';
		$err_field['mm_city'] = 1;
	}
	
	if (!strlen($zipcode)) {
		$err .= $lang['users']['zipcode'] . ', ';
		$err_field['zipcode'] = 1;
	}
	
	if (!strlen($mm_address_1)) {
		$err .= $lang['users']['mm_address_1'] . ', ';
		$err_field['mm_address_1'] = 1;
	}
	
	// birthdate not valid
	if (checkdate($b_month, $b_day, $b_year)) {
		$date_birthday = sprintf('%04d-%02d-%02d', $b_year, $b_month, $b_day);
	} else {
		$err .= $lang['err']['invalid_date'] . ', ';
		$err_field['date_birthday'] = 1;
	}
		
	if ($err) {
		$smarty->assign('err_field', $err_field);
		$err = $lang['err']['invalid_fields'] . '<br/><br/>' . trim($err, ', ');
		ViewPlatinumForm($err, $data);
		exit;
	}
	
	// check zipcode
	$rs = $dbconn->Execute('SELECT name, value FROM '.SETTINGS_TABLE.' WHERE name IN ("zip_letters", "zip_count")');
	
	while (!$rs->EOF) {
		$zip_settings[$rs->fields[0]] = $rs->fields[1];
		$rs->MoveNext();
	}
	
	if ($zipcode != '') {
		if (!$zip_settings['zip_letters']) {
			$zipcode = intval(substr($zipcode, 0, $zip_settings['zip_count']));
		} else {
			$zipcode = substr($zipcode, 0, $zip_settings['zip_count']);
		}
	}
	
	// UPDATE INFO
	$strSQL = 'UPDATE '.USERS_TABLE.' SET 
		email						= "' . $email . '",
		date_birthday				= "' . $date_birthday . '",
		mm_place_of_birth			= "' . $mm_place_of_birth . '",
		mm_id_number				= "' . $mm_id_number . '",
		mm_id_type					= "' . $mm_id_type . '",
		mm_contact_phone_number		= "' . $mm_contact_phone_number . '",
		mm_contact_mobile_number	= "' . $mm_contact_mobile_number . '",
		id_country					=  ' . $id_country . ',
		id_region					=  ' . $id_region . ',
		mm_city						= "' . $mm_city . '",
		zipcode						= "' . $zipcode . '",
		mm_address_1				= "' . $mm_address_1 . '",
		mm_address_2				= "' . $mm_address_2 . '",
		mm_address_3				= "' . $mm_address_3 . '",
		mm_platinum_submit_comment	= "' . $mm_platinum_submit_comment . '",
		mm_best_call_time_weekdays	= "' . $mm_best_call_time_weekdays . '",
		mm_best_call_time_saturdays	= "' . $mm_best_call_time_saturdays . '",
		mm_best_call_time_sundays	= "' . $mm_best_call_time_sundays . '",
		mm_platinum_submit			= NOW()
		WHERE id = ' . $id_user . ' AND root_user = "0"';
	
	if ($debug) echo $strSQL;
	$dbconn->Execute($strSQL);
	
	if ($err) {
		ViewPlatinumForm($err, $data);
		exit;
	}
	
	/*echo '<script>location.href="./payment.php?sel=pay_platinum";</script>';*/
	echo '<script>location.href="./platinum.php?sel=congrat";</script>';
	exit;
}

function ApplyNow()
{
	global $config, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$is_submit = CheckIsPlatinumSubmit();	// functions_mm
	$is_paid = CheckIsPlatinumPaid();		// functions_mm
	$is_applied = CheckIsPlatinumApply();	// functions_mm
	
	//Form is Submitted and Paid but member did not yet Apply
	if ($is_submit && $is_paid && !$is_applied)
	{
		// UPDATE Applied Column
		$res = $dbconn->Execute('UPDATE '.USERS_TABLE.' SET mm_platinum_applied = NOW() WHERE id = ? AND root_user = "0"', array($id_user));
		
		if ($res) {
			//-------------
			// send emails
			//-------------
			
			$strSQL =
				'SELECT login, fname, sname, email, mm_id_number, mm_id_type, site_language
						mm_contact_phone_number, mm_contact_mobile_number
				   FROM '.USERS_TABLE.'
				  WHERE id = ?';
			$rs = $dbconn->Execute($strSQL, array($id_user));
			$row = $rs->GetRowAssoc(false);
			
			$content				= array();
			$content['login']		= stripslashes($row['login']);
			$content['fname']		= stripslashes($row['fname']);
			$content['sname']		= stripslashes($row['sname']);
			$content['email']		= stripslashes($row['email']);
			$content['id_num']		= stripslashes($row['mm_id_number']);
			$content['id_type']		= stripslashes($row['mm_id_type']);
			$content['phone']		= stripslashes($row['mm_contact_phone_number']);
			$content['mobile']		= stripslashes($row['mm_contact_mobile_number']);
			$content['comments']	= stripslashes($row['mm_platinum_submit_comment']);
			
			// language
			$site_lang = $config['default_lang'];
		
			// include language file
			$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
			$lang_mail = array();
			include $config['path_lang'].'mail/'.$lang_file;
			
			//------------------------------------
			// send platinum applied email to user
			//------------------------------------
			
			$content['urls']		= GetUserEmailLinks();
			
			// gender suffix
			$suffix = ($user[ AUTH_GENDER ] == GENDER_MALE) ? '_e' : '_t';
			
			// subject
			$subject = $lang_mail['apply_platinum'.$suffix]['subject'];
			
			// recipient
			$email_to	= html_entity_decode($content['email']);
			$name_to	= html_entity_decode(trim($content['fname'].' '.$content['sname']));
			
			SendMail($site_lang, $email_to, $config['site_email'], $subject, $content,
				'mail_platinum_applied_user', null, $name_to, '', 'platinum_applied_user', $user[ AUTH_GENDER ]);
			
			//-------------------------------------
			// send platinum applied email to admin
			//-------------------------------------
			
			// subject
			$subject = $lang_mail['apply_platinum_admin']['subject'];
			
			// sender
			$email_from	= html_entity_decode($content['email']);
			$name_from	= html_entity_decode(trim($content['fname'].' '.$content['sname']));
			
			// recipient
			if (REDIRECT_ADMIN_EMAIL && !IS_LIVE_SERVER) {
				$email_to = REDIRECT_ADMIN_EMAIL_TO;
			} else {
				$email_to = ADMIN_EMAIL_PLAT_APPLIED;
			}
			
			SendMail($site_lang, $email_to, $email_from, $subject, $content,
				'mail_platinum_applied_admin', null, '', $name_from, 'platinum_applied_admin');
		}
	}
	
	ViewPlatinumForm();
	return;
}

?>