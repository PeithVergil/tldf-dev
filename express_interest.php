<?php
/**
* Express Interest Page
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
if (!$user[ AUTH_GUEST]) {
	GetAlertsMessage();
	SetModuleStatistic(GetRightModulePath(__FILE__));
}

// active menu item
$smarty->assign('sub_menu_num', '9');

// user selection
$sel  = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');
$type = isset($_POST['type']) ? $_POST['type'] : (isset($_GET['type']) ? $_GET['type'] : '');

// dispatcher
switch ($sel) {
	case 'post':
		ExpressInterestSubmit($type);
	break;
	
	default:
		ExpressInterestTable('', array(), array(), $type);
	break;
}

exit;


function ExpressInterestTable($err = '', $err_field = array(), $data = array(), $type = '')
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	if ($type != 'ajax') {
		if (!$user[ AUTH_GUEST ]) {
			GetAlertsMessage();
		}
		Banners(GetRightModulePath(__FILE__));
		IndexHomePage();
	}

	GetActiveUserInfo($user);
	
	if ($type != 'ajax') {
		$msg = isset($_REQUEST['msg']) ? $_REQUEST['msg'] : false;
		if ($msg == 'error') {
			$err = $lang['express_interest']['error'];
		} elseif ($msg == 'congrat') {
			$form['congrat'] = true;
			$err = $lang['express_interest']['congrat_text'];
		}
	}
	
	// default data
	if (empty($data)) {
		if (!$user[ AUTH_GUEST]) {
			// data from global $user array
			$data['fname'] = isset($user[ AUTH_FNAME ]) ? $user[ AUTH_FNAME ] : '';
			$data['sname'] = isset($user[ AUTH_SNAME ]) ? $user[ AUTH_SNAME ] : '';
			$data['email'] = isset($user[ AUTH_EMAIL ]) ? $user[ AUTH_EMAIL ] : '';
			if (isset($user[ AUTH_DATE_BIRTHDAY ])) {
				$data['b_year'] = (int) substr($user[ AUTH_DATE_BIRTHDAY ], 0, 4);
				$data['b_month'] = (int) substr($user[ AUTH_DATE_BIRTHDAY ], 5, 2);
				$data['b_day'] = (int) substr($user[ AUTH_DATE_BIRTHDAY ], 8, 2);
			} else {
				$data['b_year'] = $data['b_month'] = $data['b_day'] = '';
			}
			list($data['home_phone_cc'], $data['home_phone_ac'], $data['home_phone_ph']) = split_phone($user[ AUTH_PHONE ]);
			$data['id_country'] = isset($user[ AUTH_ID_COUNTRY ]) ? $user[ AUTH_ID_COUNTRY ] : '';
			$data['city'] = isset($user[ AUTH_CITY ]) ? $user[ AUTH_CITY ] : '';
			$data['zip_code'] = isset($user[ AUTH_ZIPCODE ]) ? $user[ AUTH_ZIPCODE ] : '';
			// additional data from user table
			$rs = $dbconn->Execute(
				'SELECT mm_best_call_time_weekdays, mm_best_call_time_saturdays, mm_best_call_time_sundays,
						mm_place_of_birth, mm_id_number, mm_id_type, mm_contact_mobile_number,
						mm_address_1, mm_address_2, mm_address_3, tlde_express_interest_submit
				   FROM '.USERS_TABLE.'
				  WHERE id = ?',
				  array($id_user));
			$row = $rs->GetRowAssoc(false);
			$data['best_time_weekdays']		= stripslashes($row['mm_best_call_time_weekdays']);
			$data['best_time_saturdays']	= stripslashes($row['mm_best_call_time_saturdays']);
			$data['best_time_sundays']		= stripslashes($row['mm_best_call_time_sundays']);
			$data['place_of_birth']			= stripslashes($row['mm_place_of_birth']);
			$data['identification_number']	= stripslashes($row['mm_id_number']);
			$data['identification_type']	= stripslashes($row['mm_id_type']);
			$data['mobile_phone']			= stripslashes($row['mm_contact_mobile_number']);
			$data['address_1']				= stripslashes($row['mm_address_1']);
			$data['address_2']				= stripslashes($row['mm_address_2']);
			$data['address_3']				= stripslashes($row['mm_address_3']);
			$data['tlde_express_interest_submit'] = $row['tlde_express_interest_submit'];
			if ($data['tlde_express_interest_submit']) {
				$smarty->assign('is_submit', true);
			}
			unset($row);
			$rs->free();
		} else {
			$data['b_year'] = $data['b_month'] = $data['b_day'] = '';
		}
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
	
	// date of birth select
	prepSmartyDate($data['b_day'], $data['b_month'], $data['b_year']);
	
	$form['err'] = $err;
	$form['err_field'] = $err_field;
	
	$smarty->assign('data', $data);
	$smarty->assign('form', $form);
	$smarty->assign('mylang', $lang['express_interest']);
	
	if ($type == 'ajax') {
		$smarty->display(TrimSlash($config['index_theme_path']).'/inline_express_interest.tpl');
	} else {
		$smarty->display(TrimSlash($config['index_theme_path']).'/express_interest_table.tpl');
	}
	exit;
}

function ExpressInterestSubmit($type = '')
{
	global $lang, $config, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$data = array();
	
	$data['best_time_weekdays']		= isset($_POST['best_time_weekdays'])	? FormFilter($_POST['best_time_weekdays']) : '';
	$data['best_time_saturdays']	= isset($_POST['best_time_saturdays'])	? FormFilter($_POST['best_time_saturdays']) : '';
	$data['best_time_sundays']		= isset($_POST['best_time_sundays'])	? FormFilter($_POST['best_time_sundays']) : '';
	$data['fname']					= isset($_POST['fname'])				? FormFilter($_POST['fname']) : '';
	$data['sname']					= isset($_POST['sname'])				? FormFilter($_POST['sname']) : '';
	$data['email']					= isset($_POST['email'])				? FormFilter($_POST['email']) : '';
	$data['b_year']					= isset($_POST['b_year'])				? (int)$_POST['b_year'] : '';
	$data['b_month']				= isset($_POST['b_month'])				? (int)$_POST['b_month'] : '';
	$data['b_day']					= isset($_POST['b_day'])				? (int)$_POST['b_day'] : '';
	$data['place_of_birth']			= isset($_POST['place_of_birth'])		? FormFilter($_POST['place_of_birth']) : '';
	$data['identification_number']	= isset($_POST['identification_number'])? FormFilter($_POST['identification_number']) : '';
	$data['identification_type']	= isset($_POST['identification_type'])	? FormFilter($_POST['identification_type']) : '';
	$data['home_phone_cc']			= isset($_POST['home_phone_cc'])		? FormFilter($_POST['home_phone_cc']) : '';
	$data['home_phone_ac']			= isset($_POST['home_phone_ac'])		? FormFilter($_POST['home_phone_ac']) : '';
	$data['home_phone_ph']			= isset($_POST['home_phone_ph'])		? FormFilter($_POST['home_phone_ph']) : '';
	$data['mobile_phone']			= isset($_POST['mobile_phone'])			? FormFilter($_POST['mobile_phone']) : '';
	$data['id_country']				= isset($_POST['id_country'])			? FormFilter($_POST['id_country']) : '';
	$data['region']					= isset($_POST['region'])				? FormFilter($_POST['region']) : '';
	$data['city']					= isset($_POST['city'])					? FormFilter($_POST['city']) : '';
	$data['zip_code']				= isset($_POST['zip_code'])				? FormFilter($_POST['zip_code']) : '';
	$data['address_1']				= isset($_POST['address_1'])			? FormFilter($_POST['address_1']) : '';
	$data['address_2']				= isset($_POST['address_2'])			? FormFilter($_POST['address_2']) : '';
	$data['address_3']				= isset($_POST['address_3'])			? FormFilter($_POST['address_3']) : '';
	$data['comments']				= isset($_POST['comments'])				? FormFilter($_POST['comments']) : '';
	
	$err = '';
	$err_field = array();
	
	if (!strlen($data['best_time_weekdays'])) {
		$err .= $lang['express_interest']['best_time_weekdays'] . ', ';
		$err_field['best_time_weekdays'] = 1;
	}
	if (!strlen($data['best_time_saturdays'])) {
		$err .= $lang['express_interest']['best_time_saturdays'] . ', ';
		$err_field['best_time_saturdays'] = 1;
	}
	if (!strlen($data['best_time_sundays'])) {
		$err .= $lang['express_interest']['best_time_sundays'] . ', ';
		$err_field['best_time_sundays'] = 1;
	}
	if (!strlen($data['fname'])) {
		$err .= $lang['express_interest']['fname'] . ', ';
		$err_field['fname'] = 1;
	}
	if (!strlen($data['sname'])) {
		$err .= $lang['express_interest']['sname'] . ', ';
		$err_field['sname'] = 1;
	}
	if (!strlen($data['email'])) {
		$err .= $lang['express_interest']['email'] . ', ';
		$err_field['email'] = 1;
	} else {
		// email validation
		$email_err = EmailFilter($data['email']);
		if ($email_err) {
			$err .= $email_err;
			$err_field['email'] = 1;
		}
	}
	if (checkdate($data['b_month'], $data['b_day'], $data['b_year'])) {
		$data['date_birthday'] = sprintf('%04d-%02d-%02d', $data['b_year'], $data['b_month'], $data['b_day']);
	} else {
		$err .= $lang['express_interest']['date_birthday'] . ', ';
		$err_field['date_birthday'] = 1;
	}
	if (!strlen($data['place_of_birth'])) {
		$err .= $lang['express_interest']['place_of_birth'] . ', ';
		$err_field['place_of_birth'] = 1;
	}
	if (!strlen($data['identification_number'])) {
		$err .= $lang['express_interest']['identification_number'] . ', ';
		$err_field['identification_number'] = 1;
	}
	if (!strlen($data['identification_type'])) {
		$err .= $lang['express_interest']['identification_type'] . ', ';
		$err_field['identification_type'] = 1;
	}
	if (!strlen($data['home_phone_cc']) || !strlen($data['home_phone_ac']) || !strlen($data['home_phone_ph'])) {
		$err .= $lang['express_interest']['home_phone'] . ', ';
		$err_field['home_phone'] = 1;
	}
	if (!strlen($data['mobile_phone'])) {
		$err .= $lang['express_interest']['mobile_phone'] . ', ';
		$err_field['mobile_phone'] = 1;
	}
	if (!strlen($data['id_country'])) {
		$err .= $lang['express_interest']['id_country'] . ', ';
		$err_field['id_country'] = 1;
	}
	if (!strlen($data['region'])) {
		$err .= $lang['express_interest']['region'] . ', ';
		$err_field['region'] = 1;
	}
	if (!strlen($data['city'])) {
		$err .= $lang['express_interest']['city'] . ', ';
		$err_field['city'] = 1;
	}
	if (!strlen($data['zip_code'])) {
		$err .= $lang['express_interest']['zip_code'] . ', ';
		$err_field['zip_code'] = 1;
	}
	if (!strlen($data['address_1'])) {
		$err .= $lang['express_interest']['address_1'] . ', ';
		$err_field['address_1'] = 1;
	}
	
	if (!empty($err)) {
		$error = $lang['err']['invalid_fields'].'<br>'.trim($err, ', ');
		ExpressInterestTable($error, $err_field, $data, $type);
		return;
	}
	
	/**
	 * update user data
	 **/
	
	$data['home_phone'] = $data['home_phone_cc'].' ('.$data['home_phone_ac'].') '.$data['home_phone_ph'];
	
	// missing: id_region
	$strSQL =
		'UPDATE '.USERS_TABLE.'
			SET mm_best_call_time_weekdays = ?, mm_best_call_time_saturdays = ?, mm_best_call_time_sundays = ?,
				fname = ?, sname = ?, email = ?, date_birthday = ?, mm_place_of_birth = ?, mm_id_number = ?, mm_id_type = ?,
				mm_contact_phone_number = ?, mm_contact_mobile_number = ?, id_country = ?, mm_city = ?,
				zipcode = ?, mm_address_1 = ?, mm_address_2 = ?, mm_address_3 = ?, mm_platinum_submit_comment = ?,
				tlde_express_interest_submit = NOW()
		  WHERE id = ?';
	
	$dbconn->Execute($strSQL, array(
		$data['best_time_weekdays'], $data['best_time_saturdays'], $data['best_time_sundays'],
		$data['fname'], $data['sname'], $data['email'], $data['date_birthday'], $data['place_of_birth'],
		$data['identification_number'], $data['identification_type'],
		$data['home_phone'], $data['mobile_phone'], $data['id_country'], $data['city'],
		$data['zip_code'], $data['address_1'], $data['address_2'], $data['address_3'], $data['comments'],
		$id_user
	));
	
	// update global $user array
	$user[ AUTH_FNAME ]			= $data['fname'];
	$user[ AUTH_SNAME ]			= $data['sname'];
	$user[ AUTH_EMAIL ]			= $data['email'];
	$user[ AUTH_PHONE ]			= $data['home_phone'];
	$user[ AUTH_ID_COUNTRY ]	= $data['id_country'];
	$user[ AUTH_CITY ]			= $data['city'];
	$user[ AUTH_ZIPCODE ]		= $data['zip_code'];
	
	$data['country'] = $dbconn->GetOne('SELECT name FROM '.COUNTRY_SPR_TABLE.' WHERE id = ?', array($data['id_country']));
	
	// UPDATE SOLVE360 CONTACT
	if (SOLVE360_CONNECTION) {
		require_once $config['site_path'].'/include/Solve360Service.php';
		$solve360Service = new Solve360Service(SOLVE360_USER, SOLVE360_TOKEN);
		
		$solve360 = array();
		require $config['site_path'].'/include/Solve360CustomFields.php';
		
		$contactData = array(
			'firstname'							=> $data['fname'],
			'lastname'							=> $data['sname'],
			'personalemail'						=> $data['email'],
			$solve360['Country']				=> $data['country'],
			$solve360['Birthday']				=> substr($data['date_birthday'], 0, 10),
			$solve360['Last Seen TLDF']			=> date('Y-m-d H:i:s'),
			$solve360['National ID Number']		=> $data['identification_number'],
			$solve360['ID Type']				=> $data['identification_type'],
			'homephone'							=> $data['home_phone'],
			'cellularphone'						=> $data['mobile_phone'],
			$solve360['Place Of Birth']			=> $data['place_of_birth'],
			$solve360['City']					=> $data['city'],
			$solve360['Home Address 1']			=> $data['address_1'],
			$solve360['Home Address 2']			=> $data['address_2'],
			$solve360['Home Address 3']			=> $data['address_3'],
			$solve360['TLDE Express Interest']	=> date('Y-m-d H:i:s'),
		);
		
		if (!empty($user[ AUTH_ID_SOLVE360 ])) {
			$contact = $solve360Service->editContact($user[ AUTH_ID_SOLVE360 ], $contactData);
			#var_dump($contact); exit;
			if (isset($contact->errors)) {
				$subject = 'Error while updating contact after user submits TLDE Express Interest';
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
	
	// include mail language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	$data['date_birthday_formatted'] = formatDateSql($data['b_year'].'-'.$data['b_month'].'-'.$data['b_day']);
	
	/**
	 * email to admin
	 **/
	
	// subject
	$subject	= $lang_mail['express_interest_admin']['subject'];
	
	// sender
	$email_from	= html_entity_decode($data['email']);
	$name_from	= html_entity_decode(trim($data['fname'].' '.$data['sname']));
	
	// recipient
	if (REDIRECT_ADMIN_EMAIL && !IS_LIVE_SERVER) {
		$email_to = REDIRECT_ADMIN_EMAIL_TO;
	} else {
		$email_to = EMAIL_EXPRESS_INTEREST;
	}
	
	$error = SendMail($site_lang, $email_to, $email_from, $subject, $data,
				'mail_express_interest_admin', null, '', $name_from, 'express_interest_admin');
	
	if ($error){
		if ($type == 'ajax') {
			$error = $lang['express_interest']['error'];
			ExpressInterestTable($error, array(), $data, $type);
			return;
		} else {
			header('Location: ./express_interest.php?msg=error');
			exit;
		}
	}
	
	/**
	 * email to user
	 **/
	
	$data['urls']		= GetUserEmailLinks();
	$data['adminemail'] = EMAIL_EXPRESS_INTEREST;
	
	// language suffix
	$suffix = ($user[ AUTH_GUEST ] || $user[ AUTH_GENDER ] == GENDER_MALE) ? '_e' : '_t';
	
	// subject
	$subject	= $lang_mail['express_interest'.$suffix]['subject'];
	
	// recipient
	$email_to	= html_entity_decode($data['email']);
	$name_to	= html_entity_decode(trim($data['fname'].' '.$data['sname']));
	
	// gender
	$gender		= $user[ AUTH_GUEST ] ? GENDER_MALE : $user[ AUTH_GENDER ];
	
	$error = SendMail($site_lang, $email_to, $config['site_email'], $subject, $data,
				'mail_express_interest_user', null, $name_to, '', 'express_interest', $gender);
	
	if ($error){
		if ($type == 'ajax') {
			$error = $lang['express_interest']['error'];
			ExpressInterestTable($error, array(), $data, $type);
			return;
		} else {
			header('Location: ./express_interest.php?msg=error');
			exit;
		}
	}
	
	// emails were sent successfully
	if ($type == 'ajax') {
		$error = $lang['express_interest']['congrat_text'];
		$data = array();
		ExpressInterestTable($error, array(), $data, $type);
		return;
	} else {
		header('Location: ./express_interest.php?msg=congrat');
		exit;
	}
}

?>