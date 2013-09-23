<?php

/**
* Functions for user mode
*
* @package DatingPro
* @subpackage Include files
*
**/

//----------------------------
// Personal Registration Form
//----------------------------

function PersonalRegistrationForm()
{
	global $smarty, $dbconn, $config, $config_index, $lang;

	// settings
	$settings = GetSiteSettings(array('icon_male_default', 'icon_female_default', 'icons_folder', 'min_age_limit', 
		'max_age_limit', 'zip_letters', 'zip_count', 'date_format','lang_ident_feature'));
	
	$form['zip_count'] = $settings['zip_count'];
	
	// hiddens
	$form['hiddens'] = '<input type="hidden" name="sel" value="save_1" />';
	$form['hiddens'].= '<input type="hidden" name="count" value="0" />';
	$form['hiddens'].= '<input type="hidden" name="e" value="1" />';
	
	// profile switchboard
	$use_field = array();
	$mandatory = array();
	
	include './customize/profile_switchboard.php';
	
	$smarty->assign('use_field', $use_field);
	$smarty->assign('mandatory', $mandatory);
	$smarty->assign('use_level', SB_REGISTRATION);
	
	// captcha
	$form['kcaptcha'] = $config['site_root'].$config_index['kcaptcha'];
	
	// multi-lang tables
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	$data = $_POST;
	
	/* do not initialize gender, user needs to make selection
	if (!isset($data['gender']) || !$data['gender']) {
		$data['gender'] = (isset($_GET['signup']) && $_GET['signup'] == 'f') ? 2 : 1;
	}
	*/
	
	if (empty($data['gender_search']) && !empty($data['gender'])) {
		$data['gender_search'] = ($data['gender'] == 1) ? 2 : 1;
	}
	
	if (!isset($data['site_language']) || !$data['site_language']) {
		$data['site_language'] = $config['default_lang'];
	}
	
	$data['relation'] = isset($_POST['relation']) ? $_POST['relation'] : array();
	
	// ralf: cannot see a risk if passwords are remembered
	// $data['pass'] = $data['repass'] = '';
	
	$smarty->assign('data', $data);

	// marital status select
	$strSQL =
		'SELECT a.id, b.'.$field_name.' AS value
		   FROM '.MARITAL_STATUS_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = 200 AND b.id_reference = a.id
	   ORDER BY a.sorter';
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$marital_status_arr = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$marital_status_arr[$i] = $row;
		$marital_status_arr[$i]['sel'] = (isset($data['mm_marital_status']) && (intval($data['mm_marital_status']) == $row['id'])) ? 1 : 0;
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('mm_marital_status', $marital_status_arr);
	
	// level of english select
	$strSQL =
		'SELECT a.id, b.'.$field_name.' AS value
		   FROM '.LEVEL_ENGLISH_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = 201 AND b.id_reference = a.id
	   ORDER BY a.sorter';
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$level_of_english_arr = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$level_of_english_arr[$i] = $row;
		$level_of_english_arr[$i]['sel'] = (isset($data['mm_level_of_english']) && (intval($data['mm_level_of_english']) == $row['id'])) ? 1 : 0;
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('mm_level_of_english', $level_of_english_arr);
	
	// employment status select
	$strSQL =
		'SELECT a.id, b.'.$field_name.' AS value
		   FROM '.EMPLOYMENT_STATUS_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = 202 AND b.id_reference = a.id
	   ORDER BY a.sorter';
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$employment_status_arr = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$employment_status_arr[$i] = $row;
		$employment_status_arr[$i]['sel'] = (isset($data['mm_employment_status']) && (intval($data['mm_employment_status']) == $row['id'])) ? 1 : 0;
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('mm_employment_status', $employment_status_arr);
	
	// birthday select
	$data['b_day']		= isset($data['b_day']) ? $data['b_day'] : 0;
	$data['b_month']	= isset($data['b_month']) ? $data['b_month'] : 0;
	$data['b_year']		= isset($data['b_year']) ? $data['b_year'] : 0;

	$day	= GetDaySelect($data['b_day']);
	$month	= GetMonthSelect($data['b_month']);
	$year	= GetYearSelect($data['b_year'], ($settings['max_age_limit']-$settings['min_age_limit']), (intval(date('Y'))-$settings['min_age_limit']));

	$date_parts = explode('%', $settings['date_format']);
	
	for ($i = 1; $i < count($date_parts); $i++) {
		switch ($date_parts[$i][0]) {
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
	
	// country select
	$rs = $dbconn->Execute('SELECT id, name AS value FROM '.COUNTRY_SPR_TABLE.' ORDER BY name');
	
	$i = 0;
	$country_arr = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$country_arr[$i] = $row;
		$country_arr[$i]['sel'] = (isset($data['id_country']) && intval($data['id_country']) == $row['id']) ? 1 : 0;
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
		$strSQL =
			'SELECT id, name AS value
			   FROM '.CITY_SPR_TABLE.'
			  WHERE id_country = ? AND id_region = ?
		   GROUP BY id
		   ORDER BY name';
		$rs = $dbconn->Execute($strSQL, array(intval($data['id_country']), intval($data['id_region'])));
		
		$i = 0;
		$city_arr = array();
		
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$city_arr[$i] = $row;
			$city_arr[$i]['sel'] = (intval($data['id_city']) == $row['id']) ? 1 : 0;
			$rs->MoveNext();
			$i++;
		}
		
		$smarty->assign('city', $city_arr);
	}
	
	// nationality select
	$strSQL =
		'SELECT a.id, b.'.$field_name.' AS value
		   FROM '.NATION_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = '.$multi_lang->TableKey(NATION_SPR_TABLE).' AND b.id_reference = a.id
	   ORDER BY value';
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$nation_arr = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$nation_arr[$i] = $row;
		$nation_arr[$i]['sel'] = (isset($data['id_nationality']) && (intval($data['id_nationality']) == $row['id'])) ? 1 : 0;
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('nation', $nation_arr);
	
	// language select
	$strSQL = 
		'SELECT a.id, b.'.$field_name.' AS value
		   FROM '.LANGUAGE_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = '.$multi_lang->TableKey(LANGUAGE_SPR_TABLE).' AND b.id_reference = a.id
	   ORDER BY value';
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$lang_sel = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$lang_sel[$i] = $row;
		$lang_sel[$i]['sel1'] = (isset($data['id_language_1']) && (intval($data['id_language_1']) == $row['id'])) ? 1 : 0;
		$lang_sel[$i]['sel2'] = (isset($data['id_language_2']) && (intval($data['id_language_2']) == $row['id'])) ? 1 : 0;
		$lang_sel[$i]['sel3'] = (isset($data['id_language_3']) && (intval($data['id_language_3']) == $row['id'])) ? 1 : 0;
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('lang_sel', $lang_sel);
	
	// gender select
	$gender_arr = array();
	$gender_arr[0]['id'] = '1';
	$gender_arr[0]['name'] = $lang['gender']['1'];
	$gender_arr[0]['name_search'] = $lang['gender_search']['1'];
	$gender_arr[0]['sel'] = isset($data['gender']) && intval($data['gender']) == 1 ? 1 : 0;
	$gender_arr[0]['sel_search'] = isset($data['gender_search']) && intval($data['gender_search']) == 1 ? 1 : 0;
	$gender_arr[1]['id'] = '2';
	$gender_arr[1]['name'] = $lang['gender']['2'];
	$gender_arr[1]['name_search'] = $lang['gender_search']['2'];
	$gender_arr[1]['sel'] = isset($data['gender']) && intval($data['gender']) == 2 ? 1 : 0;
	$gender_arr[1]['sel_search'] = isset($data['gender_search']) && intval($data['gender_search']) == 2 ? 1 : 0;
	
	$smarty->assign('gender', $gender_arr);
	
	// relationships select
	$strSQL =
		'SELECT a.id, b.'.$field_name.' AS name
		   FROM '.RELATION_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = "'.$multi_lang->TableKey(RELATION_SPR_TABLE).'" AND b.id_reference = a.id
	   ORDER BY a.sorter';
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$relation_arr = array();
	
	while (!$rs->EOF) {
		$relation_arr['opt_value'][$i] = $rs->fields[0];
		$relation_arr['opt_name'][$i] = $rs->fields[1];
		
		if (!empty($data['relation'])) {
			$relation_user_arr = $data['relation'];
		}
		
		if (isset($relation_user_arr) && is_array($relation_user_arr) && in_array(0, $relation_user_arr)) {
			$relation_arr['sel_all'] = '1';
		} else {
			if (isset($relation_user_arr) && is_array($relation_user_arr) && in_array($rs->fields[0], $relation_user_arr)) {
				$relation_arr['opt_sel'][$i] = $rs->fields[0];
			} else {
				$relation_arr['opt_sel'][$i] = 0;
			}
		}
		
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('relation', $relation_arr);
	$smarty->assign('relation_input_type', 'checkbox');
	
	// get site language from ip
	if (!isset($_POST['site_language']) && !isset($_COOKIE['language_cd']) && $settings['lang_ident_feature']) {
		$IpInfo = new IpInfo();
		$id_lang_by_ip = $IpInfo->id_lang;
		
		if ($id_lang_by_ip > 0) {
			$data['site_language'] = $id_lang_by_ip;
		}
	}
	
	// site language select
	$rs = $dbconn->Execute('SELECT id, name FROM '.LANGUAGE_TABLE.' WHERE visible = "1"');
	
	$site_langs = array();
	$i = 0;
	
	while (!$rs->EOF) {
		$site_langs[$i]['id'] = intval($rs->fields[0]);
		$site_langs[$i]['name'] = ucfirst($rs->fields[1]);
		$site_langs[$i]['sel'] = ($rs->fields[0] == $data['site_language']) ? 1 : 0;
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('site_langs', $site_langs);
	
	// age range selection
	$max_in = $settings['max_age_limit'];
	$min_in = $settings['min_age_limit'];
	
	$max_age_arr = range(intval($max_in), intval($min_in));
	$min_age_arr = range(intval($min_in), intval($max_in));
	
	$smarty->assign('age_max', $max_age_arr);
	$smarty->assign('age_min', $min_age_arr);
	
	if (isset($data['age_min']) && intval($data['age_min'])) {
		$smarty->assign('min_age_sel', $data['age_min']);
	}
	
	if (isset($data['age_max']) && intval($data['age_max'])) {
		$smarty->assign('max_age_sel', $data['age_max']);
	}
	
	// built subscribes
	$rs = $dbconn->Execute('SELECT a.id FROM '.SUBSCRIBE_SISTEM_TABLE.' a WHERE a.status = "1"');
	
	$i = 0;
	$s_subscr = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$s_subscr[$i]['id'] = $row['id'];
		$s_subscr[$i]['sel'] = (isset($data['s_subscr'][$i]) && $data['s_subscr'][$i] > 0) ? '1' : '0';
		$s_subscr[$i]['name'] = $lang['subcribe']['alert_'.$row['id']];
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('s_subscr', $s_subscr);

	// admin subscribes ... from functions_newsletter.php
	if (!function_exists('GetSiteSubscribeForUser')) {
		include_once dirname(__FILE__).'/functions_newsletter.php';
	}
	
	$adm_subscr = GetSiteSubscribeForUser();
	
	if (is_array($adm_subscr)) {
		foreach ($adm_subscr as $key => $subscr_entry) {
			$adm_subscr[$key]['sel'] = $data['a_subscr'][$key] > 0 ? '1' : '0';
		}
	}
	
	$smarty->assign('adm_subscr', $adm_subscr);
	
	return $form;
}

function IsRegistered($email){
	global $smarty, $dbconn, $config, $lang, $user; 
	
	$query = "SELECT * FROM ".USERS_TABLE." WHERE `email` = '".$email."'";
	
	
	$selectquery =$dbconn->Execute($query);
	$count = $selectquery->RecordCount();
	//print($count);
	if($count)
	return 1;
	else
	return 0;

}

//Generating Random password for FB user

function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}


function SaveFbRegistrationForm($fbdata = array())
{
	/**
	*
	*@author:SH2
	**/
	global $smarty, $dbconn, $config, $lang, $user;
	
	if(!IsRegistered($fbdata['email'])){
			
		$pass = SavePersonalRegistrationForm($fbdata);
		return $pass;
	}
	
	else{
	//login user
	
		return '';
	
	}	
}

function SavePersonalRegistrationForm($fbdata = array())
{
	global $smarty, $dbconn, $config, $lang, $user; // $check_email_domain;
	
	$debug = false;
	
	if(!empty($fbdata)){	
	//echo "Not empty";
	//print(print_r(htmlentities($fbdata)));
	
	}
	$data  =  $_POST;
	
	
	// login and password
	$data['login']				= isset($_POST['login']) ? FormFilter($_POST['login']):'';
	$data['pass']				= isset($_POST['pass']) ? strval($_POST['pass']): '';  
	
	$data['repass'] = $data['pass'];
	// NOT IN USE SH 2
	//	$data['repass']				= isset($fbdata) ? ' ' : strval($_POST['repass']);
	
	// personal info
	
	/** SH2 Not in use now.
	$data['fname']				= isset($_POST['fname'])				? FormFilter($_POST['fname']) : '';
	$data['sname']				= isset($_POST['sname'])				? FormFilter($_POST['sname']) : '';
	**/
	
	$name = explode(' ', $_POST['name']);
	
	$data['fname'] = $name[0];
	$data['sname'] = $name[1];
	
	$data['mm_nickname']		= isset($_POST['mm_nickname'])			? FormFilter($_POST['mm_nickname']) : '';
	$data['gender']				= isset($_POST['gender'])				? (int) $_POST['gender'] : 0;
	$data['mm_marital_status']	= isset($_POST['mm_marital_status'])	? (int) $_POST['mm_marital_status'] : 0;
	$data['b_year']				= isset($_POST['b_year']) ? (int) $_POST['b_year']: '';
	$data['b_month']			= isset($_POST['b_month']) ? (int) $_POST['b_month']: '';
	$data['b_day']				= isset($_POST['b_day']) ? (int) $_POST['b_day']: '';
	$data['mm_place_of_birth']	= isset($_POST['mm_place_of_birth'])	? FormFilter($_POST['mm_place_of_birth']) : '';
	$data['id_nationality']		= isset($_POST['id_nationality'])		? (int) $_POST['id_nationality'] : 0;
	$data['mm_id_number']		= isset($_POST['mm_id_number'])			? FormFilter($_POST['mm_id_number']) : '';
	
	// contact info
	$data['email']				= isset($_POST['email'])				? FormFilter($_POST['email']) : '';
	$data['reemail']			= isset($_POST['reemail'])				? FormFilter($_POST['reemail']) : '';
	$data['mm_contact_phone_number']	= isset($_POST['mm_contact_phone_number']) ? FormFilter($_POST['mm_contact_phone_number']) : '';
	$data['mm_contact_mobile_number']	= isset($_POST['mm_contact_mobile_number']) ? FormFilter($_POST['mm_contact_mobile_number']) : '';
	
	// voip
	$data['phone']				= isset($_POST['phone'])				? $_POST['phone'] : '';
	
	// couple
	$data['couple']				= isset($_POST['couple'])				? (int) $_POST['couple'] : 0;
	$data['couple_user']		= isset($_POST['couple_user'])			? (int) $_POST['couple_user'] : 0;
	$data['couple_login']		= isset($_POST['couple_login'])			? FormFilter($_POST['couple_login']) : '';
	
	// address info
	$data['id_country']			= isset($_POST['id_country'])			? (int) $_POST['id_country'] : 0;
	$data['id_region']			= isset($_POST['id_region'])			? (int) $_POST['id_region'] : 0;
	$data['id_city']			= isset($_POST['id_city'])				? (int) $_POST['id_city'] : 0;
	$data['mm_city']			= isset($_POST['mm_city'])				? FormFilter($_POST['mm_city']) : '';
	$data['zipcode']			= isset($_POST['zipcode'])				? FormFilter($_POST['zipcode']) : '';
	$data['mm_address_1']		= isset($_POST['mm_address_1'])			? FormFilter($_POST['mm_address_1']) : '';
	$data['mm_address_2']		= isset($_POST['mm_address_2'])			? FormFilter($_POST['mm_address_2']) : '';
	
	// language info
	$data['id_language_1']		= isset($_POST['id_language_1'])		? (int) $_POST['id_language_1'] : 0;
	$data['id_language_2']		= isset($_POST['id_language_2'])		? (int) $_POST['id_language_2'] : 0;
	$data['id_language_3']		= isset($_POST['id_language_3'])		? (int) $_POST['id_language_3'] : 0;
	$data['mm_level_of_english']= isset($_POST['mm_level_of_english'])	? (int) $_POST['mm_level_of_english'] : 0;
	$data['site_language']		= isset($_POST['site_language'])		? (int) $_POST['site_language'] : (int) $config['default_lang'];
	
	// search
	$data['gender_search']		= isset($_POST['gender_search'])		? (int) $_POST['gender_search'] : 0;
	$data['couple_search']		= isset($_POST['couple_search'])		? (int) $_POST['couple_search'] : 0;
	$data['age_min']			= isset($_POST['age_min'])				? (int) $_POST['age_min'] : 18;
	$data['age_max']			= isset($_POST['age_max'])				? (int) $_POST['age_max'] : 99;
	
	if (empty($_POST['relation'])) {
		$data['relation'] = MM_DEFAULT_RELATIONSHIP_ID;
	} elseif ($_POST['relation'][0] == '0') {
		$data['relation'] = '0';
	} else {
		$data['relation'] = implode(',', $data['relation']);
	}
	
	// employment info
	$data['mm_employment_status']	= isset($_POST['mm_employment_status']) ? (int) $_POST['mm_employment_status']: '';
	$data['mm_business_name']		= isset($_POST['mm_business_name']) ? FormFilter($_POST['mm_business_name']):'';
	$data['mm_employer_name']		= isset($_POST['mm_employer_name']) ? FormFilter($_POST['mm_employer_name']): '';
	$data['mm_job_position']		= isset($_POST['mm_job_position'])		? FormFilter($_POST['mm_job_position']) : '';
	$data['mm_work_address']		= isset($_POST['mm_work_address'])		? FormFilter($_POST['mm_work_address']) : '';
	$data['mm_work_phone_number']	= isset($_POST['mm_work_phone_number'])	? FormFilter($_POST['mm_work_phone_number']) : '';
	
	// reference 1
	$data['mm_ref_1_first_name']	= isset($_POST['mm_ref_1_first_name'])	? FormFilter($_POST['mm_ref_1_first_name']) : '';
	$data['mm_ref_1_last_name']		= isset($_POST['mm_ref_1_last_name'])	? FormFilter($_POST['mm_ref_1_last_name']) : '';
	$data['mm_ref_1_relationship']	= isset($_POST['mm_ref_1_relationship'])? FormFilter($_POST['mm_ref_1_relationship']) : '';
	$data['mm_ref_1_phone_number']	= isset($_POST['mm_ref_1_phone_number'])? FormFilter($_POST['mm_ref_1_phone_number']) : '';
	
	// reference 2
	$data['mm_ref_2_first_name']	= isset($_POST['mm_ref_2_first_name'])	? FormFilter($_POST['mm_ref_2_first_name']) : '';
	$data['mm_ref_2_last_name']		= isset($_POST['mm_ref_2_last_name'])	? FormFilter($_POST['mm_ref_2_last_name']) : '';
	$data['mm_ref_2_relationship']	= isset($_POST['mm_ref_2_relationship'])? FormFilter($_POST['mm_ref_2_relationship']) : '';
	$data['mm_ref_2_phone_number']	= isset($_POST['mm_ref_2_phone_number'])? FormFilter($_POST['mm_ref_2_phone_number']) : '';
	
	// headline
	$data['headline']				= isset($_POST['headline'])				? stripn(FormFilter($_POST['headline'])) : '';
	
	// height and weight
	$data['id_height']				= isset($_POST['id_height'])			? (int) $_POST['id_height'] : 0;
	$data['id_weight']				= isset($_POST['id_weight'])			? (int) $_POST['id_weight'] : 0;
	
	// terms of service
	$data['agreed']					= isset($fbdata) ? 1 : (int) $_POST['agreed'];
	
	// spam code
	//$keystring						= trim($data['keystring']);
	
	// dependent values
	if (!$data['gender_search'] && $data['gender']) {
		$data['gender_search'] = ($data['gender'] == GENDER_MALE) ? GENDER_FEMALE : GENDER_MALE;
	}
	
	// privacy settings
	$data['hide_online']			= isset($_POST['hide_online'])			? (int) $_POST['hide_online'] : 0;
	
	$default_lady_visible			= ($data['gender'] == GENDER_MALE) ? 1 : 0;
	$default_guy_visible			= ($data['gender'] == GENDER_FEMALE) ? 1 : 0;
	
	$data['visible_lady']			= isset($_POST['visible_lady'])			? (int) $_POST['visible_lady'] : $default_lady_visible;
	$data['visible_guy']			= isset($_POST['visible_guy'])			? (int) $_POST['visible_guy'] : $default_guy_visible;
	
	if ($data['visible_lady'] == 1) {
		$data['vis_lady_1']		= 1;
		$data['vis_lady_2']		= 1;
		$data['vis_lady_3']		= 1;
	} else {
		$data['vis_lady_1']		= isset($_POST['vis_lady_1']) ? (int) $_POST['vis_lady_1'] : 0;
		$data['vis_lady_2']		= isset($_POST['vis_lady_2']) ? (int) $_POST['vis_lady_2'] : 0;
		$data['vis_lady_3']		= isset($_POST['vis_lady_3']) ? (int) $_POST['vis_lady_3'] : 0;
	}
	
	if ($data['visible_guy'] == 1) {
		$data['vis_guy_1']		= 1;
		$data['vis_guy_2']		= 1;
		$data['vis_guy_3']		= 1;
		$data['vis_guy_4']		= 1;
	} else {
		$data['vis_guy_1']		= isset($_POST['vis_guy_1']) ? (int) $_POST['vis_guy_1'] : 0;
		$data['vis_guy_2']		= isset($_POST['vis_guy_2']) ? (int) $_POST['vis_guy_2'] : 0;
		$data['vis_guy_3']		= isset($_POST['vis_guy_3']) ? (int) $_POST['vis_guy_3'] : 0;
		$data['vis_guy_4']		= isset($_POST['vis_guy_4']) ? (int) $_POST['vis_guy_4'] : 0;
	}
	
	$data['promotion_1']		= isset($_POST['promotion_1']) ? (int) $_POST['promotion_1'] : 0;
	$data['promotion_2']		= isset($_POST['promotion_2']) ? (int) $_POST['promotion_2'] : 0;
	$data['promotion_3']		= isset($_POST['promotion_3']) ? (int) $_POST['promotion_3'] : 0;
	
	
	if(!empty($fbdata)){	//SH2 If user is from fb login.
		
		
		$data = $fbdata;
		//$debug = false;
		$confirm = 1;
		$status = 1;
		$data['fname'] = $fbdata['first_name'];
		$data['sname'] = $fbdata['last_name'];
		$data['email'] = $fbdata['email'];
		$data['reemail'] = $fbdata['email'];
		$data['login'] = $fbdata['username'];
		$data['pass'] = randomPassword();
		$data['repass'] = $data['pass'];
		$data['couple_user'] = 0;
		if($fbdata['gender'] =='male'){
			$data['gender'] = '1';
			$data['gender_search'] = '2';
		}else {$data['gender'] = '2';
			$data['gender_search'] = '1';
		}
		
		$bday = explode( '/', $fbdata['birthday'] );
		//$data['date_birthday'] = $bday[2]."-".$bday[0]."-".$bday[1];
		$data['b_year']				= (int) $bday[2];
		$data['b_month']			= (int) $bday[0];
		$data['b_day']				= (int)	$bday[1];
		$data['site_language']		= 1;
		$data['couple_login']       = 0;
		$data['couple']       		= 0;
		$data['headline']			='';
		$data['zipcode']			='';
	}
	
	
	
	
	// store sanitized input in $_POST for postback
	$_POST = $data;
	
	//----------------
	// validity check
	//----------------
	
	$mandatory = array();
	
	include './customize/profile_switchboard.php';
	
	// check mandatory fields
	
	$err		= '';
	$err_field	= array();
	$bullet		= '&#8226; ';
	
	if (!strlen($data['login'])) {
		$err .= $lang['users']['login'] . ', ';
		$err_field['login'] = 1;
	}
	
	if (!strlen($data['pass'])) {
		$err .= $lang['users']['pass'] . ', ';
		$err_field['pass'] = 1;
	}
	
// 	if (!strlen($data['repass'])) {
// 		$err .= $lang['users']['repass'] . ', ';
// 		$err_field['repass'] = 1;
// 	}
	
	if ($mandatory['fname'] & SB_REGISTRATION && !strlen($data['fname'])) {
		$err .= $lang['users']['fname'] . ', ';
		$err_field['fname'] = 1;
	}
	
// 	if ($mandatory['sname'] & SB_REGISTRATION && !strlen($data['sname'])) {
// 		$err .= $lang['users']['sname'] . ', ';
// 		$err_field['sname'] = 1;
// 	}
	
	if ($mandatory['mm_nickname'] & SB_REGISTRATION && !strlen($data['mm_nickname'])) {
		$err .= $lang['users']['mm_nickname'] . ', ';
		$err_field['mm_nickname'] = 1;
	}
	
	if ($mandatory['gender'] & SB_REGISTRATION && !$data['gender']) {
		$err .= $lang['users']['gender'] . ', ';
		$err_field['gender'] = 1;
	}
	
	if ($mandatory['mm_marital_status'] & SB_REGISTRATION && !$data['mm_marital_status']) {
		$err .= $lang['users']['mm_marital_status'] . ', ';
		$err_field['mm_marital_status'] = 1;
	}
	
	if ($mandatory['date_birthday'] & SB_REGISTRATION && (!$data['b_year'] || !$data['b_month'] || !$data['b_day'])) {
		$err .= $lang['users']['date_birthday'] . ', ';
		$err_field['date_birthday'] = 1;
	}
	
	if ($mandatory['mm_place_of_birth'] & SB_REGISTRATION && !strlen($data['mm_place_of_birth'])) {
		$err .= $lang['users']['mm_place_of_birth'] . ', ';
		$err_field['mm_place_of_birth'] = 1;
	}
	
	if ($mandatory['id_nationality'] & SB_REGISTRATION && !$data['id_nationality']) {
		$err .= $lang['users']['nationality'] . ', ';
		$err_field['id_nationality'] = 1;
	}
	
	if ($mandatory['mm_id_number'] & SB_REGISTRATION && $data['gender'] == GENDER_FEMALE && !strlen($data['mm_id_number'])) {
		$err .= $lang['users']['mm_id_number'] . ', ';
		$err_field['mm_id_number'] = 1;
	}
	
	if ($mandatory['email'] & SB_REGISTRATION && !strlen($data['email'])) {
		$err .= $lang['users']['email'] . ', ';
		$err_field['email'] = 1;
	}
	
// 	if ($mandatory['reemail'] & SB_REGISTRATION && !strlen($data['reemail'])) {
// 		$err .= $lang['users']['reemail'] . ', ';
// 		$err_field['reemail'] = 1;
// 	}
	
	if ($mandatory['mm_contact_phone_number'] & SB_REGISTRATION && !strlen($data['mm_contact_phone_number'])) {
		$err .= $lang['users']['mm_contact_phone_number'] . ', ';
		$err_field['mm_contact_phone_number'] = 1;
	}
	
	if ($mandatory['mm_contact_mobile_number'] & SB_REGISTRATION && !strlen($data['mm_contact_mobile_number'])) {
		$err .= $lang['users']['mm_contact_mobile_number'] . ', ';
		$err_field['mm_contact_mobile_number'] = 1;
	}
	
	if ($mandatory['id_country'] & SB_REGISTRATION && !$data['id_country']) {
		$err .= $lang['users']['country'] . ', ';
		$err_field['id_country'] = 1;
	}
	
	if ($mandatory['id_region'] & SB_REGISTRATION && !$data['id_region']) {
		$err .= $lang['users']['region'] . ', ';
		$err_field['id_region'] = 1;
	}
	
	if ($mandatory['id_city'] & SB_REGISTRATION && !$data['id_city']) {
		$err .= $lang['users']['city'] . ', ';
		$err_field['id_city'] = 1;
	}
	
	if ($mandatory['mm_city'] & SB_REGISTRATION && !strlen($data['mm_city'])) {
		$err .= $lang['users']['city'] . ', ';
		$err_field['mm_city'] = 1;
	}
	
	if ($mandatory['zipcode'] & SB_REGISTRATION && !strlen($data['zipcode'])) {
		$err .= $lang['users']['zipcode'] . ', ';
		$err_field['zipcode'] = 1;
	}
	
	if ($mandatory['mm_address_1'] & SB_REGISTRATION && !strlen($data['mm_address_1'])) {
		$err .= $lang['users']['mm_address_1'] . ', ';
		$err_field['mm_address_1'] = 1;
	}
	
	if ($mandatory['mm_address_2'] & SB_REGISTRATION && !strlen($data['mm_address_2'])) {
		$err .= $lang['users']['mm_address_2'] . ', ';
		$err_field['mm_address_2'] = 1;
	}
	
	if ($mandatory['id_language_1'] & SB_REGISTRATION && !$data['id_language_1']) {
		$err .= $lang['users']['language'] . ', ';
		$err_field['id_language_1'] = 1;
	}
	
	if ($mandatory['id_language_2'] & SB_REGISTRATION && !$data['id_language_2']) {
		$err .= $lang['users']['language'] . ', ';
		$err_field['id_language_2'] = 1;
	}
	
	if ($mandatory['id_language_3'] & SB_REGISTRATION && !$data['id_language_3']) {
		$err .= $lang['users']['language'] . ', ';
		$err_field['id_language_3'] = 1;
	}
	
	if ($mandatory['mm_level_of_english'] & SB_REGISTRATION && !$data['mm_level_of_english']) {
		$err .= $lang['users']['mm_level_of_english'] . ', ';
		$err_field['mm_level_of_english'] = 1;
	}
	
	if ($mandatory['site_language'] & SB_REGISTRATION && !$data['site_language']) {
		$err .= $lang['users']['site_language'] . ', ';
		$err_field['site_language'] = 1;
	}
	
	if ($mandatory['mm_employment_status'] & SB_REGISTRATION && !$data['mm_employment_status']) {
		$err .= $lang['users']['mm_employment_status'] . ', ';
		$err_field['mm_employment_status'] = 1;
	}
	
	if ($mandatory['mm_business_name'] & SB_REGISTRATION && $data['mm_employment_status'] == 2 && !strlen($data['mm_business_name'])) {
		$err .= $lang['users']['mm_business_name'] . ', ';
		$err_field['mm_business_name'] = 1;
	}
	
	if ($mandatory['mm_employer_name'] & SB_REGISTRATION && $data['mm_employment_status'] == 3 && !strlen($data['mm_employer_name'])) {
		$err .= $lang['users']['mm_employer_name'] . ', ';
		$err_field['mm_employer_name'] = 1;
	}
	
	if ($mandatory['mm_job_position'] & SB_REGISTRATION && $data['mm_employment_status'] != 1 && !strlen($data['mm_job_position'])) {
		$err .= $lang['users']['mm_job_position'] . ', ';
		$err_field['mm_job_position'] = 1;
	}
	
	if ($mandatory['mm_work_address'] & SB_REGISTRATION && $data['mm_employment_status'] != 1 && !strlen($data['mm_work_address'])) {
		$err .= $lang['users']['mm_work_address'] . ', ';
		$err_field['mm_work_address'] = 1;
	}
	
	if ($mandatory['mm_work_phone_number'] & SB_REGISTRATION && $data['mm_employment_status'] != 1 && !strlen($data['mm_work_phone_number'])) {
		$err .= $lang['users']['mm_work_phone_number'] . ', ';
		$err_field['mm_work_phone_number'] = 1;
	}
	
	if ($mandatory['mm_ref_1_first_name'] & SB_REGISTRATION && !strlen($data['mm_ref_1_first_name'])) {
		$err .= $lang['users']['mm_reference_1'].' '.$lang['users']['fname'] . ', ';
		$err_field['mm_ref_1_first_name'] = 1;
	}
	
	if ($mandatory['mm_ref_1_last_name'] & SB_REGISTRATION && !strlen($data['mm_ref_1_last_name'])) {
		$err .= $lang['users']['mm_reference_1'].' '.$lang['users']['sname'] . ', ';
		$err_field['mm_ref_1_last_name'] = 1;
	}
	
	if ($mandatory['mm_ref_1_relationship'] & SB_REGISTRATION && !strlen($data['mm_ref_1_relationship'])) {
		$err .= $lang['users']['mm_reference_1'].' '.$lang['users']['mm_reference_relationship'].', ';
		$err_field['mm_ref_1_relationship'] = 1;
	}
	
	if ($mandatory['mm_ref_1_phone_number'] & SB_REGISTRATION && !strlen($data['mm_ref_1_phone_number'])) {
		$err .= $lang['users']['mm_reference_1'].' '.$lang['users']['mm_reference_phone_number'].', ';
		$err_field['mm_ref_1_phone_number'] = 1;
	}
	
	if ($mandatory['mm_ref_2_first_name'] & SB_REGISTRATION && !strlen($data['mm_ref_2_first_name'])) {
		$err .= $lang['users']['mm_reference_2'].' '.$lang['users']['fname'] . ', ';
		$err_field['mm_ref_2_first_name'] = 1;
	}
	
	if ($mandatory['mm_ref_2_last_name'] & SB_REGISTRATION && !strlen($data['mm_ref_2_last_name'])) {
		$err .= $lang['users']['mm_reference_2'].' '.$lang['users']['sname'] . ', ';
		$err_field['mm_ref_2_last_name'] = 1;
	}
	
	if ($mandatory['mm_ref_2_relationship'] & SB_REGISTRATION && !strlen($data['mm_ref_2_relationship'])) {
		$err .= $lang['users']['mm_reference_2'].' '.$lang['users']['mm_reference_relationship'].', ';
		$err_field['mm_ref_2_relationship'] = 1;
	}
	
	if ($mandatory['mm_ref_2_phone_number'] & SB_REGISTRATION && !strlen($data['mm_ref_2_phone_number'])) {
		$err .= $lang['users']['mm_reference_2'].' '.$lang['users']['mm_reference_phone_number'].', ';
		$err_field['mm_ref_2_phone_number'] = 1;
	}
	
	if ($err) {
		$err = $bullet . $lang['err']['invalid_fields'] . '<br/><br/>' . trim($err, ', ');
	}
	
	// additional checks only after all mandatory fields are provided
	// disable this if statement when all checks should be made immediately
	if (!$err)
	{
		// login not valid
		if (strlen($data['login']))
		{
			$login_err = LoginFilter($data['login']);
			
			if ($login_err) {
				$err .= $bullet . $login_err;
				$err_field['login'] = 1;
			}
			
			// login already exists
			$count = $dbconn->getOne('SELECT COUNT(id) FROM '.USERS_TABLE.' WHERE login = ?', array($data['login']));
			
			if (!empty($count)) {
				if ($err) $err .= '<br><br>';
				$err .= $bullet . $lang['err']['exists_login'];
				$err_field['login'] = 1;
			}
		}
		
		// password not valid
		if (strlen($data['pass']))
		{
			$password_err = PasswFilter($data['pass']);
			
			if ($password_err) {
				if ($err) $err .= '<br><br>';
				$err .= $bullet . $password_err;
				$err_field['pass'] = 1;
			}
			
			if (strlen($data['repass']) && $data['repass'] != $data['pass']) {
				if ($err) $err .= '<br><br>';
				$err .= $bullet . $lang['err']['pass_eq_repass'];
				$err_field['pass'] = $err_field['repass'] = 1;
			}
			
			if (strlen($data['login']) && $data['login'] == $data['pass']) {
				if ($err) $err .= '<br><br>';
				$err .= $bullet . $lang['err']['pass_eq_log'];
				$err_field['login'] = $err_field['pass'] = $err_field['repass'] = 1;
			}
		}
			
		// birthdate not valid
		if ($data['b_year'] || $data['b_month'] || $data['b_day'])
		{
			if (checkdate($data['b_month'], $data['b_day'], $data['b_year']))
			{
				$data['date_birthday'] = sprintf('%04d-%02d-%02d', $data['b_year'], $data['b_month'], $data['b_day']);
			}
			else
			{
				if ($err) $err .= '<br><br>';
				$err .= $bullet . $lang['err']['invalid_birthdate'];
				$err_field['date_birthday'] = 1;
			}
		}
		
		// email not valid
		if (strlen($data['email']))
		{
			$email_err = EmailFilter($data['email']);
			
			if ($email_err) {
				if ($err) $err .= '<br><br>';
				$err .= $bullet . $email_err;
				$err_field['email'] = 1;
			}
			
			if (strlen($data['reemail']) && $data['reemail'] != $data['email']) {
				if ($err) $err .= '<br><br>';
				$err .= $bullet . $lang['err']['email_eq_log'];
				$err_field['email'] = $err_field['reemail'] = 1;
			}
			
			// email already exists
			unset($count);
			
			$count = $dbconn->getOne('SELECT COUNT(id) FROM '.USERS_TABLE.' WHERE email = ?', array($data['email']));
			
			if (!empty($count)) {
				if ($err) $err .= '<br><br>';
				$err .= $bullet . $lang['err']['exists_email'];
				$err_field['email'] = 1;
			}
		}
		
		if ($config['voipcall_feature'] == 1)
		{
			// voip phone not valid
			$phone_err = PhoneFilter($data['phone']);
			
			if ($phone_err) {
				if ($err) $err .= '<br><br>';
				$err .= $bullet . $phone_err;
				$err_field['phone'] = 1;
			}
			
			// voip phone already exists
			unset($count);
			
			$count = $dbconn->getOne('SELECT COUNT(id) FROM '.USERS_TABLE.' WHERE phone <> "" AND phone = ?', array($data['phone']));
			
			if (!empty($count)) {
				if ($err) $err .= '<br><br>';
				$err .= $bullet . $lang['err']['exists_phone'];
				$err_field['phone'] = 1;
			}
		}
		
		// couple login already exists
		if (!$data['couple_user'] && strlen($data['couple_login']))
		{
			$data['couple_user'] = $dbconn->getOne('SELECT id FROM '.USERS_TABLE.' WHERE login = ?', array($data['couple_login']));
			
			if (!empty($data['couple_user'])) {
				$couple_send = true;
			} else {
				if ($err) $err .= '<br><br>';
				$err .= $bullet . $lang['err']['wrong_couple_login'];
				$err_field['couple'] = 1;
			}
		}
		
		// check badwords and contacts in headline
		if (strlen($data['headline']))
		{
			$headline_err = BadWordsCont($data['headline'], 4);
			
			if ($headline_err) {
				if ($err) $err .= '<br><br>';
				$err .= $bullet . $headline_err;
				$err_field['headline'] = 1;
			}
			
			if (check_filter($data['headline'])) {
				if ($err) $err .= '<br><br>';
				$err .= $bullet . $lang['err']['info_finding_1'];
				$err_field['headline'] = 1;
			}
		}
		
		// check zipcode
		if (strlen($data['zipcode']))
		{
			$zip_settings = GetSiteSettings(array('zip_letters', 'zip_count'));
			
			if ($zip_settings['zip_letters']) {
				$data['zipcode'] = substr($data['zipcode'], 0, $zip_settings['zip_count']);
			} else {
				$data['zipcode'] = intval(substr($data['zipcode'], 0, $zip_settings['zip_count']));
			}
		}
	}
	
	
	unset($_SESSION['captcha_keystring']);
	
	if ($err) {
		$smarty->assign('err_field', $err_field);
		$_SESSION['err_field']  = $err_field;
		return $err;
	}
	
	$settings = GetSiteSettings(array('use_registration_confirmation', 'use_registration_approve',
					'use_refer_friend_feature', 'refer_friend_price', 'site_unit_costunit'));
	
	$confirm = $settings['use_registration_confirmation'] ? '0' : '1';
	$status  = $settings['use_registration_approve'] ? '0' : '1';
	
	//VP Automatic Country and Nationality fill for lady users
	if (USE_LADY_COUNTRY_FIX) {
		if ($data['gender'] == GENDER_FEMALE && $data['id_country'] == 0) {
			$data['id_country'] = THAILAND_COUNTRY_CODE;
		}
		if ($data['gender'] == GENDER_FEMALE && $data['id_nationality'] == 0) {
			$data['id_nationality'] = THAI_NATIONALITY_CODE;
		}
	}
		
		
	$strSQL = 
		'INSERT INTO '.USERS_TABLE.' SET
				fname = ?, sname = ?, status = ?, confirm = ?, gender = ?,
				couple = ?, couple_user = ?, login = ?, email = ?, id_country = ?,
				id_region = ?, id_city = ?, zipcode = ?, date_birthday = ?, date_last_seen = NOW(),
				date_registration = NOW(), password = ?, id_nationality = ?, id_language_1 = ?, id_language_2 = ?,
				id_language_3 = ?, headline = ?, site_language = ?, date_topsearched = NOW(), phone = ?,
				mm_nickname = ?, mm_id_number = ?, mm_contact_phone_number = ?, mm_contact_mobile_number = ?, mm_marital_status = ?,
				mm_place_of_birth = ?, mm_city = ?, mm_address_1 = ?, mm_address_2 = ?, mm_level_of_english = ?,
				mm_employment_status = ?, mm_business_name = ?, mm_employer_name = ?, mm_job_position = ?, mm_work_address = ?,
				mm_work_phone_number = ?, mm_ref_1_first_name = ?, mm_ref_1_last_name = ?, mm_ref_1_relationship = ?, mm_ref_1_phone_number = ?,
				mm_ref_2_first_name = ?, mm_ref_2_last_name = ?, mm_ref_2_relationship = ?, mm_ref_2_phone_number = ?,
				id_height = ?, id_weight = ?';
	
	if ($debug) echo $strSQL; 
	
	
	//SH2.
	if(!empty($fbdata)){ //user logs in through fb.
		$dbconn->Execute($strSQL, array(
		$data['fname'], $data['sname'], (string)$status, '1', (string)$data['gender'],
		'', '', $data['login'], $data['email'], '',
		'', '', '', $data['date_birthday'],
		md5($data['pass']), '', '', '',
		'', '', '', '',
		'', '', '', '', '',
		'', '', '', '', '',
		'', '', '', '', '',
		'', '', '', '', '',
		'', '', '', '',
		'', ''
	));
		
	}else{
	$dbconn->Execute($strSQL, array(
		$data['fname'], $data['sname'], (string)$status, (string)$confirm, (string)$data['gender'],
		(string)$data['couple'], $data['couple_user'], $data['login'], $data['email'], $data['id_country'],
		$data['id_region'], $data['id_city'], $data['zipcode'], $data['date_birthday'],
		md5($data['pass']), $data['id_nationality'], $data['id_language_1'], $data['id_language_2'],
		$data['id_language_3'], $data['headline'], $data['site_language'], $data['phone'],
		$data['mm_nickname'], $data['mm_id_number'], $data['mm_contact_phone_number'], $data['mm_contact_mobile_number'], $data['mm_marital_status'],
		$data['mm_place_of_birth'], $data['mm_city'], $data['mm_address_1'], $data['mm_address_2'], $data['mm_level_of_english'],
		$data['mm_employment_status'], $data['mm_business_name'], $data['mm_employer_name'], $data['mm_job_position'], $data['mm_work_address'],
		$data['mm_work_phone_number'], $data['mm_ref_1_first_name'], $data['mm_ref_1_last_name'], $data['mm_ref_1_relationship'], $data['mm_ref_1_phone_number'],
		$data['mm_ref_2_first_name'], $data['mm_ref_2_last_name'], $data['mm_ref_2_relationship'], $data['mm_ref_2_phone_number'],
		$data['id_height'], $data['id_weight']
	));
	}
	$id_user = $dbconn->Insert_ID();
	 
	// IF user Registers through FB Get his/her Profile Pic From Facebook. ***SH 2***
	if(! empty($fbdata)){
		
		//Query to store image location in Database.
		$query = "UPDATE `".USERS_TABLE."` SET `icon_path` = ?, `big_icon_path` = ? WHERE `id` = ? ";
		
		$fid = $data['login'] ;
		$img = file_get_contents('https://graph.facebook.com/'.$fid.'/picture?type=large');
		
		$img_small = file_get_contents('https://graph.facebook.com/'.$fid.'/picture?type=normal');
		
		$randm = randomPassword();
		
		$big_pic_name = 'big_thumb_'.$id_user.'_'.$randm.'.jpg' ;
		
		$small_pic_name = 'thumb_'.$id_user.'_'.$randm.'.jpg' ;
		
		$file = $config['site_path'].'/uploades/icons/'.$big_pic_name;
		$file1 = $config['site_path'].'/uploades/icons/'.$small_pic_name;
		
		file_put_contents($file, $img);
		file_put_contents($file1, $img_small);
		
		$dbconn->Execute($query, array($small_pic_name, $big_pic_name, $id_user));
	
	}
	//ENDIF    **SH 2**
	
	$_SESSION['language_cd'] = $data['site_language'];
	
	// ADD CONTACT TO SOLVE360
	if (SOLVE360_CONNECTION) {
		require_once $config['site_path'].'/include/Solve360Service.php';
		$solve360Service = new Solve360Service(SOLVE360_USER, SOLVE360_TOKEN);
		
		$country = $dbconn->GetOne('SELECT name FROM '.COUNTRY_SPR_TABLE.' WHERE id = ?', array($data['id_country']));
		$region = $dbconn->GetOne('SELECT name FROM '.REGION_SPR_TABLE.' WHERE id = ?', array($data['id_region']));
		$nationality = $dbconn->GetOne('SELECT name FROM '.NATION_SPR_TABLE.' WHERE id = ?', array($data['id_nationality']));
		$language_1 = $dbconn->GetOne('SELECT name FROM '.LANGUAGE_SPR_TABLE.' WHERE id = ?', array($data['id_language_1']));
		$marital_status = $dbconn->GetOne('SELECT name FROM '.MARITAL_STATUS_SPR_TABLE.' WHERE id = ?', array($data['mm_marital_status']));
		$level_of_english = $dbconn->GetOne('SELECT name FROM '.LEVEL_ENGLISH_SPR_TABLE.' WHERE id = ?', array($data['mm_level_of_english']));
		
		$solve360 = array();
		require $config['site_path'].'/include/Solve360CustomFields.php';
		
		$categories		= array();
		$categories[]	= SOLVE360_TAG_TLDF;
		
		if ($data['gender'] == GENDER_MALE) {
			$categories[] = SOLVE360_TAG_GUY;
		} else {
			$categories[] = SOLVE360_TAG_LADY;
		}
		
		// clarify if we need this
		// we would need to remove this category when the user becomes Trial
		if ($data['gender'] == GENDER_MALE) {
			$categories[] = SOLVE360_TAG_SIGN_UP_GUY;
		} else {
			$categories[] = SOLVE360_TAG_SIGN_UP_LADY;
		}
		
		$contactData = array(
			$solve360['TLDF ID Number']			=> $id_user,
			'firstname'							=> $data['fname'],
			'lastname'							=> $data['sname'],
			$solve360['TLDF Status']			=> ($status ? 'Good' : 'Inactive'),
		#	$solve360['Platinum Verified']		=> 'No',
			$solve360['TLDF Confirmed']			=> 'No',
			$solve360['TLDF Login']				=> $data['login'],
			$solve360['Gender']					=> ($data['gender'] == GENDER_MALE ? 'Guy' : 'Lady'),
			'personalemail'						=> $data['email'],
			$solve360['Country']				=> $country,						// lookup
			$solve360['Region']					=> $region,							// lookup
			$solve360['Nationality']			=> $nationality,					// lookup
			$solve360['Language 1']				=> $language_1,						// lookup
			$solve360['Birthday']				=> substr($data['date_birthday'], 0, 10),
			$solve360['Last Seen TLDF']			=> date('Y-m-d H:i:s'),
			$solve360['Registration Date']		=> date('Y-m-d H:i:s'),
			$solve360['TLDF Login Count']		=> 0,
			$solve360['Nick Name']				=> $data['mm_nickname'],
			$solve360['National ID Number']		=> $data['mm_id_number'],
			$solve360['ID Type']				=> '',
			'homephone'							=> $data['mm_contact_phone_number'],
			'cellularphone'						=> $data['mm_contact_mobile_number'],
			$solve360['Marital Status']			=> $marital_status,					// lookup
			$solve360['Place Of Birth']			=> $data['mm_place_of_birth'],
			$solve360['City']					=> $data['mm_city'],
			$solve360['Home Address 1']			=> $data['mm_address_1'],
			$solve360['Home Address 2']			=> $data['mm_address_2'],
			$solve360['Home Address 3']			=> '',
			$solve360['Level Of English']		=> $level_of_english,				// lookup
			$solve360['Employer Name']			=> $data['mm_employer_name'],
			'jobtitle'							=> $data['mm_job_position'],
			'businessaddress'					=> $data['mm_work_address'],
			'businessphonedirect'				=> $data['mm_work_phone_number'],
		#	$solve360['Platinum Form']			=> '',								// date/time
		#	$solve360['Platinum Paid']			=> '',								// date/time
		#	$solve360['TLDE Express Interest']	=> '',								// date/time
			$solve360['Current Group']			=> ($data['gender'] == GENDER_MALE ? 'Signup Guy' : 'Signup Lady'),
		#	$solve360['TLDF Trial Start Date']	=> '',								// date/time
			$solve360['TLDF Membership Ends']	=> UNLIMITED_DATE_END,				// date/time
			$solve360['Online Origin Of Lead']	=> 'TLDF',
			

			// OPTION Apply category tag(s) and set the owner for the contact to a group
			// You will find a list of IDs for your tags, groups and users in Workspace > My Account > API Reference

			// Specify a different ownership i.e. share the item
			'ownership'							=> SOLVE360_OWNERSHIP_MMNB,
			
			// Add categories
			'categories' => array(
				'add' => array('category' => $categories)
			),
		);

		$contact = $solve360Service->addContact($contactData);

		#var_dump($contact); exit;

		if (isset($contact->errors)) {
			#return $contact->errors->asXml();
			$subject = 'Error while adding TLDF contact after registration';
			solve360_api_error($contact, $subject, $data['login']);
		} else {
			$id_solve360 = $contact->item->id;
			$dbconn->Execute('UPDATE '.USERS_TABLE.' SET id_solve360 = ? WHERE id = ?', array($id_solve360, $id_user));
		}
	}
	
	// GA_TRACKING
	$_SESSION['ga_event_code'] = 'newsignup';
	
	// entry in match table
	if(!empty($fbdata)){ //user logs in through fb.
	$dbconn->Execute(
		'INSERT INTO '.USER_MATCH_TABLE.'
			SET id_user = ?, gender = ?, couple = ?, age_max = ?, age_min = ?, id_relationship = ?',
		array($id_user, (string)$data['gender_search'], '', '', '', ''));
	}
	
else{
	
	$dbconn->Execute(
		'INSERT INTO '.USER_MATCH_TABLE.'
			SET id_user = ?, gender = ?, couple = ?, age_max = ?, age_min = ?, id_relationship = ?',
		array($id_user, (string)$data['gender_search'], (string)$data['couple_search'], $data['age_max'], $data['age_min'], $data['relation']));
	}
	// entry in user_privacy_settings table
	
	if(!empty($fbdata)){ //user sign up through fb. skip privacy settings for now
	}
	else{
	$strSQL =
		'INSERT INTO '.USER_PRIVACY_SETTINGS.' SET
				id_user = ?, hide_online = ?, promotion_1 = ?, promotion_2 = ?, promotion_3 = ?,
				visible_lady = ?, visible_guy = ?, vis_lady_1 = ?, vis_lady_2 = ?, vis_lady_3 = ?,
				vis_guy_1 = ?, vis_guy_2 = ?, vis_guy_3 = ?, vis_guy_4 = ?';
	$dbconn->Execute($strSQL, array($id_user, (string)$data['hide_online'], (string)$data['promotion_1'], (string)$data['promotion_2'], (string)$data['promotion_3'],
				(string)$data['visible_lady'], (string)$data['visible_guy'], (string)$data['vis_lady_1'], (string)$data['vis_lady_2'], (string)$data['vis_lady_3'],
				(string)$data['vis_guy_1'], (string)$data['vis_guy_2'], (string)$data['vis_guy_3'], (string)$data['vis_guy_4']));
	}
	// entry in subscribe_user table for 'New email in my mailbox' (id = 4)
	$dbconn->Execute(
		'INSERT INTO '.SUBSCRIBE_USER_TABLE.' SET id_subscribe = "4", id_user = ?, type = "s"',
		array($id_user));
	
	// add user into 'default_add' groups (f.e 'free users') or 'freetrial_membership' group
#	$use_freetrial_membership = GetSiteSettings('use_freetrial_membership');
#	
#	if ($use_freetrial_membership)
#	{
#		$rs = $dbconn->Execute('SELECT id FROM '.GROUPS_TABLE.' WHERE type = "t"');
#		
#		while (!$rs->EOF) {
#			if (intval($rs->fields[0]) > 0) {
#				$rs1 = $dbconn->Execute('INSERT INTO '.USER_GROUP_TABLE.' SET id_user = ?, id_group = ?', array($id_user, intval($rs->fields[0])));
#			}
#			
#			$strSQL = 'SELECT id, cost, period, amount FROM '.GROUP_PERIOD_TABLE.' WHERE id_group = ?';
#			$rs1 = $dbconn->Execute($strSQL, array(intval($rs->fields[0])));
#			
#			$period = $rs1->fields[0];
#			$cost = $rs1->fields[1];
#			$day_period = $rs1->fields[3] * $config_admin['pay_period'][$rs1->fields[2]];	// period in days
#			$date_begin = date('Y-m-d H:i:s');
#			
#			$ts = time() + $day_period*24*60*60;
#			$date_end = date('Y-m-d H:i:s', $ts);
#			
#			$strSQL = 'INSERT INTO '.BILLING_USER_PERIOD_TABLE.' SET id_group_period = ?, date_begin = ?, date_end = ?, id_user = ?';
#			$dbconn->Execute($strSQL, array($period, $date_begin, $date_end, $id_user));
#			
#			$rs->MoveNext();
#		}
#	}
#	else
#	{
		// VP gender added as filter. Now type 'd' is used into two groups
		// RS we need to cast $gender as string here because of integers in enum (arghh)
		$rs = $dbconn->Execute('SELECT id FROM '.GROUPS_TABLE.' WHERE type = "d" AND gender = ?', array((string)$data['gender']));
		
		while (!$rs->EOF) {
			if ($rs->fields[0] > 0) {
				$dbconn->Execute('INSERT INTO '.USER_GROUP_TABLE.' SET id_user = ?, id_group = ?', array($id_user, $rs->fields[0]));
			}
			$rs->MoveNext();
		}
#	}
	
	// update session with new id (from functions_auth.php)
	sess_write(session_id(), $id_user);
	$user = auth_index_user();
	RefreshAccount();
	
	// update(create) user profile completed statistic
	$profile_percent = new Percent($id_user);
	$profile_percent->UpdateSection1Percent();
	
	// newsletter update
	if (!function_exists('UpdateNewsletterUserData')) {
		include_once dirname(__FILE__).'/functions_newsletter.php';
	}
	
	UpdateNewsletterUserData($id_user, $data['fname'], $data['sname'], $data['email']);
	
	// subscribes
	$s_subscr = isset($data['s_subscr']) ? $data['s_subscr'] : array();
	$a_subscr = isset($data['a_subscr']) ? $data['a_subscr'] : array();
	
	if (is_array($s_subscr) && count($s_subscr) > 0) {
		foreach ($s_subscr as $v) {
			$dbconn->Execute('INSERT INTO '.SUBSCRIBE_USER_TABLE.' SET id_subscribe = ?, id_user = ?, type = "s"', array($v, $id_user));
		}
	}
	
	if (!function_exists('UpdateSubscribeListForUser')) {
		include_once dirname(__FILE__).'/functions_newsletter.php';
	}
	
	UpdateSubscribeListForUser($a_subscr, $id_user);
	
	if (!function_exists('TypeAffiliatePayment')) {
		include_once dirname(__FILE__).'/functions_affiliate.php';
	}
	
	if (TypeAffiliatePayment() == '0') {
		AffiliatesRegistration();
	}
	
	// create login token for registration email
	$token = CreateToken($id_user);
	//echo $token;
	//---------------------------------
	// send registration email to user
	//---------------------------------
	
	// include mail language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($data['site_language']));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	// content array
	$content							= array();
	$content['fname']					= $data['fname'];
	$content['sname']					= $data['sname'];
	$content['date_birthday_formatted'] = formatDateSql($data['date_birthday']);
	$content['login']					= $data['login'];
	$content['pass']					= $data['pass'];
	$content['email']					= $data['email'];
	
	$content['urls']		= GetUserEmailLinks();
	
	#$urls['video_eng']		= $server['root'].'/video_player.php?vid=2A746A01-CB90-2F1D-6AB5C06DB7C95262';
	#$urls['video_thai']	= $server['root'].'/video_player.php?vid=2A7973DC-C133-0DE8-40EC3DD6D5E2D089';
	
	if ($settings['use_registration_confirmation']) {
		$content['confirm_link'] = $config['server'].$config['site_root'].'/confirm.php?id='.$id_user.'&login_id='.$id_user.'&token='.$token;
	}
	
	#$content['freecd_link']	= $config['server'].$config['site_root'].'/request_info.php?id='.$id_user;
	#$content['approve']		= $settings['use_registration_approve'] ? 1 : 0;
	
	// gender suffix
	$suffix = ($data['gender'] == GENDER_MALE) ? '_e' : '_t';
	
	// subject
	$subject = str_replace('[name]', $data['fname'], $lang_mail['registration'.$suffix]['subject']);
	
	// recipient
	$name_to = trim($data['fname'].' '.$data['sname']);
	
	// send external email
	SendMail($data['site_language'], $data['email'], $config['site_email'], $subject, $content,
		'mail_registration_user', null, $name_to, '', 'registration', $data['gender']);
	
	//---------------------------------
	// send registration email to admin
	//---------------------------------
	
	if ($settings['use_registration_approve'])
	{
		// language
		$site_lang = $config['default_lang'];
		
		// include mail language file
		$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
		$lang_mail = array();
		include $config['path_lang'].'mail/'.$lang_file;
		
		// subject
		$subject = str_replace('[login]', $data['login'], $lang_mail['registration_admin']['subject']);
		
		// recipient
		if (REDIRECT_ADMIN_EMAIL && !IS_LIVE_SERVER) {
			$email_to = REDIRECT_ADMIN_EMAIL_TO;
		} else {
			$email_to = $config['site_email'];
		}
		
		SendMail($site_lang, $email_to, $config['site_email'], $subject, $content,
			'mail_registration_admin', null, '', '', 'registration_admin');
	}
	
	// couples not in use
	if (false) {
		if (isset($couple_send) && $couple_send) {
			$body = $lang['users']['couple_accept_message'];
			$body.= '<br><br><a href="myprofile.php?sel=couple&id='.$id_user.'">';
			$body.= $lang['users']['couple_accept_link'];
			$body.= '</a><br><br>';
			$strSQL =
				'INSERT INTO '.MAILBOX_TABLE.' SET
						id_from = ?, id_to = ?, subject = ?, body = ?, date_creation = NOW(), was_read = "0", deleted_from = "0", deleted_to = "0"';
			$rs = $dbconn->Execute($strSQL, array($id_user, $data['couple_user'], $lang['users']['couple_accept_subject'], $body));
		}
	}
	
	// refer friend feature
	$refer_friend_price = floatval($settings['refer_friend_price']);
	
	if ($settings['use_refer_friend_feature'] && isset($_COOKIE['refer_friend_code']) && $_COOKIE['refer_friend_code'] && $refer_friend_price != 0)
	{
		$refer_friend_code = addslashes($_COOKIE['refer_friend_code']);
		
		$id_refer = $dbconn->GetOne('SELECT id_user FROM '.USER_REFER_CODE_TABLE.' WHERE code = ?', array($refer_friend_code));
		
		if ($id_refer) {
			$id_acc = $dbconn->GetOne('SELECT id FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($id_refer));
			
			if ($id_acc != '') {
				$strSQL =
					'UPDATE '.BILLING_USER_ACCOUNT_TABLE.' SET
							account_curr = account_curr + '.$settings['refer_friend_price'].', date_refresh = NOW()
					  WHERE id = ?';
				$dbconn->Execute($strSQL, array($id_acc));
			} else {
				$strSQL =
					'INSERT INTO '.BILLING_USER_ACCOUNT_TABLE.' SET
							id_user = ?, account_curr = account_curr + '.$settings['refer_friend_price'].', date_refresh = NOW()';
				$dbconn->Execute($strSQL, array($id_refer));
			}
			
			$strSQL =
				'INSERT INTO '.BILLING_ENTRY_TABLE.' SET
						id_user = ?, amount = ?, currency = ?, id_group = ?, date_entry = NOW(), entry_type = "refer_friend"';
			$dbconn->Execute($strSQL, array($id_refer, $refer_friend_price, $settings['site_unit_costunit'], PG_SINGLE_CREDIT_POINTS));
			
			$id_entry = $dbconn->Insert_ID();
			
			$refer_id_user = $dbconn->GetOne('SELECT id_user FROM '.USER_REFER_TABLE.' WHERE id_user = ?', array($id_user));
			
			if (empty($refer_id_user)) {
				$dbconn->Execute('INSERT INTO '.USER_REFER_TABLE.' SET id_user = ?, id_refer = ?, id_entry = ?',
					array($id_user, $id_refer, $id_entry));
			}
			
			setcookie('refer_friend_code', '', time()-3600);
		}
	}

	if($fbdata){
		return $data['pass'];
	}
	
	return '';
}

//---------------
// Personal Form
//---------------

function PersonalForm()
{
	global $smarty, $dbconn, $lang, $user, $reqfrom;
	
	$id = $user[ AUTH_ID_USER ];
	
	// settings
	$settings = GetSiteSettings(array('icon_male_default', 'icon_female_default', 'icons_folder', 'min_age_limit', 'max_age_limit', 'zip_letters', 'zip_count', 'date_format'));
	
	$form['zip_count']  = $settings['zip_count'];
	
	// hiddens
	$form['hiddens'] = '<input type="hidden" name="sel" value="save_1" />';
	$form['hiddens'].= '<input type="hidden" name="count" value="1" />';
	$form['hiddens'].= '<input type="hidden" name="e" value="1" />';
	$form['hiddens'].= '<input type="hidden" name="id" value="'.$id.'" />';
	$form['hiddens'].= '<input type="hidden" name="reqfrom" value="'.$reqfrom.'" />';
	
	// profile switchboard
	$use_field = array();
	$mandatory = array();
	
	include './customize/profile_switchboard.php';
	
	$smarty->assign('use_field', $use_field);
	$smarty->assign('mandatory', $mandatory);
	$smarty->assign('use_level', SB_EDIT);
	
	// multi-lang tables
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	$data = $_POST;
	
	if (!isset($data['count']) || $data['count'] != 1)
	{
		$strSQL =
			'SELECT u.login, u.fname, u.sname, u.status, u.email, u.date_birthday, u.gender, u.couple, u.couple_user, 
					u.id_country, u.id_city, u.id_region, u.zipcode, u.comment, u.headline, u.id_nationality, 
					u.id_language_1, u.id_language_2, u.id_language_3, u.id_weight, u.id_height, u.root_user, u.guest_user, 
					um.gender AS gender_search, um.couple AS couple_search, um.age_min, um.age_max, um.id_relationship, 
					u.site_language, u.phone, u.mm_nickname, u.mm_id_number, u.mm_id_type, u.mm_contact_phone_number,
					u.mm_contact_mobile_number, u.mm_marital_status,
					u.id_height,u.id_weight,
					u.mm_place_of_birth, u.mm_city, u.mm_address_1, u.mm_address_2, u.mm_address_3,
					u.mm_level_of_english, u.mm_employment_status, u.mm_business_name, u.mm_employer_name, 
					u.mm_job_position, u.mm_work_address, u.mm_work_phone_number,
					u.mm_ref_1_first_name, u.mm_ref_1_last_name, u.mm_ref_1_relationship, u.mm_ref_1_phone_number, 
					u.mm_ref_2_first_name, u.mm_ref_2_last_name, u.mm_ref_2_relationship, u.mm_ref_2_phone_number,
					u.mm_best_call_time_weekdays, u.mm_best_call_time_saturdays, u.mm_best_call_time_sundays, u.mm_platinum_submit_comment,
					u.about_me, u.what_i_do, u.my_idea, u.hoping_to_find,
					up.hide_online, up.promotion_1, up.promotion_2, up.promotion_3, up.visible_lady, up.visible_guy,
					up.vis_lady_1, up.vis_lady_2, up.vis_lady_3, up.vis_lady_4, up.vis_lady_5,
					up.vis_guy_1, up.vis_guy_2, up.vis_guy_3, up.vis_guy_4, up.vis_guy_5
			   FROM '.USERS_TABLE.' AS u
		  LEFT JOIN '.USER_MATCH_TABLE.' AS um ON um.id_user = u.id
		  LEFT JOIN '.USER_PRIVACY_SETTINGS.' AS up ON up.id_user = u.id
			  WHERE u.id = ?';
		
		$rs = $dbconn->Execute($strSQL, array($id));
		$row = $rs->GetRowAssoc(false);
		
		// login
		$data['login']			= $row['login'];
		// identity
		$data['mm_nickname']	= stripslashes($row['mm_nickname']);
		$data['fname']			= stripslashes($row['fname']);
		$data['sname']			= stripslashes($row['sname']);
		$data['mm_id_number']	= stripslashes($row['mm_id_number']);
		$data['mm_id_type']		= stripslashes($row['mm_id_type']);
		// contact info
		$data['email']						= $row['email'];
		$data['mm_contact_phone_number']	= stripslashes($row['mm_contact_phone_number']);
		$data['mm_contact_mobile_number']	= stripslashes($row['mm_contact_mobile_number']);
		$data['phone']						= $row['phone'];
		// personal info
		$data['gender']				= $row['gender'];
		$data['mm_marital_status']	= $row['mm_marital_status'];
		$data['b_year']				= intval(substr($row['date_birthday'], 0, 4));
		$data['b_month']			= intval(substr($row['date_birthday'], 5, 2));
		$data['b_day']				= intval(substr($row['date_birthday'], 8, 2));
		$data['mm_place_of_birth']	= stripslashes($row['mm_place_of_birth']);
		//Added Height and weight
		$data['id_height']			= $row['id_height'];
		$data['id_weight']			= $row['id_weight'];
		
		$data['id_nationality']		= $row['id_nationality'];
		$data['mm_best_call_time_weekdays']		= stripslashes($row['mm_best_call_time_weekdays']);
		$data['mm_best_call_time_saturdays']	= stripslashes($row['mm_best_call_time_saturdays']);
		$data['mm_best_call_time_sundays']		= stripslashes($row['mm_best_call_time_sundays']);
		$data['mm_platinum_submit_comment']		= stripslashes($row['mm_platinum_submit_comment']);
		// address info
		$data['id_country']			= $row['id_country'];
		$data['id_region']			= $row['id_region'];
		$data['id_city']			= $row['id_city'];
		$data['mm_city']			= stripslashes($row['mm_city']);
		$data['zipcode']			= stripslashes($row['zipcode']);
		$data['mm_address_1']		= stripslashes($row['mm_address_1']);
		$data['mm_address_2']		= stripslashes($row['mm_address_2']);
		$data['mm_address_3']		= stripslashes($row['mm_address_3']);
		// language info
		$data['id_language_1']			= $row['id_language_1'];
		$data['id_language_2']			= $row['id_language_2'];
		$data['id_language_3']			= $row['id_language_3'];
		$data['mm_level_of_english']	= $row['mm_level_of_english'];
		$data['site_language']			= $row['site_language'];
		// employment info
		$data['mm_employment_status']	= $row['mm_employment_status'];
		$data['mm_business_name']		= stripslashes($row['mm_business_name']);
		$data['mm_employer_name']		= stripslashes($row['mm_employer_name']);
		$data['mm_job_position']		= stripslashes($row['mm_job_position']);
		$data['mm_work_address']		= stripslashes($row['mm_work_address']);
		$data['mm_work_phone_number']	= stripslashes($row['mm_work_phone_number']);
		// references
		$data['mm_ref_1_first_name']	= stripslashes($row['mm_ref_1_first_name']);
		$data['mm_ref_1_last_name']		= stripslashes($row['mm_ref_1_last_name']);
		$data['mm_ref_1_relationship']	= stripslashes($row['mm_ref_1_relationship']);
		$data['mm_ref_1_phone_number']	= stripslashes($row['mm_ref_1_phone_number']);
		$data['mm_ref_2_first_name']	= stripslashes($row['mm_ref_2_first_name']);
		$data['mm_ref_2_last_name']		= stripslashes($row['mm_ref_2_last_name']);
		$data['mm_ref_2_relationship']	= stripslashes($row['mm_ref_2_relationship']);
		$data['mm_ref_2_phone_number']	= stripslashes($row['mm_ref_2_phone_number']);
		// couples
		$data['couple']					= $row['couple'];
		$data['couple_user']			= $row['couple_user'];
		$data['couple_login']			= '';
		
		if ($data['couple_user']) {
			$rs_couple = $dbconn->Execute('SELECT login, gender, date_birthday, couple_user FROM '.USERS_TABLE.' WHERE id = ?', array($data['couple_user']));
			$data['couple_login']	= $rs_couple->fields[0];
			$data['couple_link']	= 'viewprofile.php?id='.$data['couple_user'];
			$data['couple_gender']	= $lang['gender'][$rs_couple->fields[1]];
			$data['couple_age']		= AgeFromBDate($rs_couple->fields[2]);
			$data['couple_accept']	= $rs_couple->fields[3] == $id ? 1 : 0;
		}
		
		// headline
		$data['headline']				= stripslashes($row['headline']);
		
		// personal settings
		$data['hide_online']			= isset($row['hide_online']) ? intval($row['hide_online']) : 0;
		$data['visible_lady']			= isset($row['visible_lady']) ? intval($row['visible_lady']) : 1;
		
		$data['vis_lady_1']				= isset($row['vis_lady_1']) ? intval($row['vis_lady_1']) : 1;
		$data['vis_lady_2']				= isset($row['vis_lady_2']) ? intval($row['vis_lady_2']) : 1;
		$data['vis_lady_3']				= isset($row['vis_lady_3']) ? intval($row['vis_lady_3']) : 1;
		
		$data['visible_guy']			= isset($row['visible_guy']) ? intval($row['visible_guy']) : 1;
		
		$data['vis_guy_1']				= isset($row['vis_guy_1']) ? intval($row['vis_guy_1']) : 1;
		$data['vis_guy_2']				= isset($row['vis_guy_2']) ? intval($row['vis_guy_2']) : 1;
		$data['vis_guy_3']				= isset($row['vis_guy_3']) ? intval($row['vis_guy_3']) : 1;
		$data['vis_guy_4']				= isset($row['vis_guy_4']) ? intval($row['vis_guy_4']) : 1;
		
		$data['promotion_1']			= isset($row['promotion_1']) ? intval($row['promotion_1']) : 0;
		$data['promotion_2']			= isset($row['promotion_2']) ? intval($row['promotion_2']) : 0;
		$data['promotion_3']			= isset($row['promotion_3']) ? intval($row['promotion_3']) : 0;
		
		// biography
		$data['about_me']				= stripslashes($row['about_me']);
		$data['what_i_do']				= stripslashes($row['what_i_do']);
		$data['my_idea']				= stripslashes($row['my_idea']);
		$data['hoping_to_find']			= stripslashes($row['hoping_to_find']);
		
		// weight and height
		$data['id_weight']				= $row['id_weight'];
		$data['id_height']				= $row['id_height'];
		// root user
		$data['root']					= $row['root_user'] ? $row['root_user'] : $row['guest_user'];
		// search criteria
		$data['gender_search']			= intval($row['gender_search']);
		$data['couple_search']			= intval($row['couple_search']);
		$data['age_min']				= intval($row['age_min']);
		$data['age_max']				= intval($row['age_max']);
		$data['relation']				= $row['id_relationship'];
	}
	else
	{
		$data['relation']				= isset($_POST['relation']) ? $_POST['relation'] : array();
	}
	
	if (!$data['gender']) {
		$data['gender'] = 2;
	}
	
	if (!$data['gender_search']) {
		$data['gender_search'] = 1;
	}
	
	$smarty->assign('data', $data);
	
	$day	= GetDaySelect($data['b_day']);
	$month	= GetMonthSelect($data['b_month']);
	$year	= GetYearSelect($data['b_year'], ($settings['max_age_limit']-$settings['min_age_limit']), (intval(date('Y'))-$settings['min_age_limit']));
	
	$date_parts = explode('%', $settings['date_format']);
	
	for ($i = 1; $i < count($date_parts); $i++)
	{
		switch ($date_parts[$i][0]) {
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
	
	// marital status select
	$strSQL =
		'SELECT a.id, b.'.$field_name.' AS value
		   FROM '.MARITAL_STATUS_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = 200 AND b.id_reference = a.id
	   ORDER BY a.sorter';
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$marital_status_arr = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$marital_status_arr[$i] = $row;
		$marital_status_arr[$i]['sel'] = (isset($data['mm_marital_status']) && (intval($data['mm_marital_status']) == $row['id'])) ? 1 : 0;
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('mm_marital_status', $marital_status_arr);
	
	// level of english select
	$strSQL =
		'SELECT a.id, b.'.$field_name.' AS value
		   FROM '.LEVEL_ENGLISH_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = 201 AND b.id_reference = a.id
	   ORDER BY a.sorter';
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$level_of_english_arr = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$level_of_english_arr[$i] = $row;
		$level_of_english_arr[$i]['sel'] = (isset($data['mm_level_of_english']) && (intval($data['mm_level_of_english']) == $row['id'])) ? 1 : 0;
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('mm_level_of_english', $level_of_english_arr);
	
	// employment status select
	$strSQL =
		'SELECT a.id, b.'.$field_name.' AS value
		   FROM '.EMPLOYMENT_STATUS_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = 202 AND b.id_reference = a.id
	   ORDER BY a.sorter';
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$employment_status_arr = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$employment_status_arr[$i] = $row;
		$employment_status_arr[$i]['sel'] = (isset($data['mm_employment_status']) && (intval($data['mm_employment_status']) == $row['id'])) ? 1 : 0;
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('mm_employment_status', $employment_status_arr);
	
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
	if (isset($data['id_country']))
	{
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
	if (isset($data['id_region']))
	{
		$strSQL =
			'SELECT id, name AS value
			   FROM '.CITY_SPR_TABLE.'
			  WHERE id_region = ? AND id_country = ?
		   GROUP BY id
		   ORDER BY name';
		$rs = $dbconn->Execute($strSQL, array($data['id_region'], $data['id_country']));
		
		$i = 0;
		$city_arr = array();
		
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$city_arr[$i] = $row;
			$city_arr[$i]['sel'] = (intval($data['id_city']) == $row['id']) ? 1 : 0;
			$rs->MoveNext();
			$i++;
		}
		
		$smarty->assign('city', $city_arr);
	}
	
	// weight select
	$rs = $dbconn->Execute(
		'SELECT DISTINCT a.id, b.' . $field_name . ' AS value
		   FROM '.WEIGHT_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = "' . $multi_lang->TableKey(WEIGHT_SPR_TABLE) . '" AND b.id_reference = a.id
	   ORDER BY a.sorter');
	
	$i = 0;
	$weight_arr = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$weight_arr[$i] = $row;
		$weight_arr[$i]['sel'] = ($data['id_weight'] == $row['id']) ? 1 : 0;
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('weight', $weight_arr);
	
	// height select
	$strSQL =
		"SELECT DISTINCT a.id, b." . $field_name . " AS value
		  FROM ".HEIGHT_SPR_TABLE." a
	 LEFT JOIN ".REFERENCE_LANG_TABLE." b ON b.table_key='" . $multi_lang->TableKey(HEIGHT_SPR_TABLE) . "' AND b.id_reference=a.id
	  ORDER BY a.sorter";
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$height_arr = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$height_arr[$i] = $row;
		$height_arr[$i]['sel'] = ($data['id_height'] == $row['id']) ? 1 : 0;
		$rs->MoveNext();
		$i++;
	}

	$smarty->assign('height', $height_arr);
	
	// nationality select
	$strSQL =
		'SELECT a.id, b.'.$field_name.' AS value
		   FROM '.NATION_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = '.$multi_lang->TableKey(NATION_SPR_TABLE).' AND b.id_reference = a.id
	   ORDER BY value';
	
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$nation_arr = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$nation_arr[$i] = $row;
		$nation_arr[$i]['sel'] = (intval($data['id_nationality']) == $row['id']) ? 1 : 0;
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('nation', $nation_arr);
	
	// language select
	$strSQL =
		'SELECT a.id, b.'.$field_name.' AS value
		   FROM '.LANGUAGE_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = '.$multi_lang->TableKey(LANGUAGE_SPR_TABLE).' AND b.id_reference = a.id
	   ORDER BY value';
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$lang_sel = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$lang_sel[$i] = $row;
		$lang_sel[$i]['sel1'] = (intval($data['id_language_1']) == $row['id']) ? 1 : 0;
		$lang_sel[$i]['sel2'] = (intval($data['id_language_2']) == $row['id']) ? 1 : 0;
		$lang_sel[$i]['sel3'] = (intval($data['id_language_3']) == $row['id']) ? 1 : 0;
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('lang_sel', $lang_sel);

	// gender select
	$gender_arr = array();
	$gender_arr[0]['id'] = '1';
	$gender_arr[0]['name'] = $lang['gender']['1'];
	$gender_arr[0]['name_search'] = $lang['gender_search']['1'];
	$gender_arr[0]['sel'] = intval($data['gender']) == 1 ? 1 : 0;
	$gender_arr[0]['sel_search'] = intval($data['gender_search']) == 1 ? 1 : 0;
	$gender_arr[1]['id'] = '2';
	$gender_arr[1]['name'] = $lang['gender']['2'];
	$gender_arr[1]['name_search'] = $lang['gender_search']['2'];
	$gender_arr[1]['sel'] = intval($data['gender']) == 2 ? 1 : 0;
	$gender_arr[1]['sel_search'] = intval($data['gender_search']) == 2 ? 1 : 0;
	$smarty->assign('gender', $gender_arr);

	// relationships select
	$strSQL =
		'SELECT a.id, b.'.$field_name.' AS name
		   FROM '.RELATION_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = '.$multi_lang->TableKey(RELATION_SPR_TABLE).' AND b.id_reference = a.id
	   ORDER BY a.sorter';
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	
	while (!$rs ->EOF) {
		$relation_arr['opt_value'][$i] = $rs->fields[0];
		$relation_arr['opt_name'][$i] = $rs->fields[1];
		
		if (strlen($data['relation'])) {
			$relation_user_arr = explode(',', $data['relation']);
		}
		
		if (empty($relation_user_arr) || (is_array($relation_user_arr) && in_array(0, $relation_user_arr))) {
			$relation_arr['sel_all'] = '1';
		} else {
			if (is_array($relation_user_arr) && in_array($rs->fields[0], $relation_user_arr)) {
				$relation_arr['opt_sel'][$i] = $rs->fields[0];
			} else {
				$relation_arr['opt_sel'][$i] = 0;
			}
		}
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('relation', $relation_arr);
	
	$smarty->assign('relation_input_type', 'select');
	
	// site language select
	$rs = $dbconn->Execute('SELECT id, name FROM '.LANGUAGE_TABLE.' WHERE visible = "1"');
	
	$site_langs = array();
	$i = 0;
	
	while (!$rs->EOF) {
		$site_langs[$i]['id'] = intval($rs->fields[0]);
		$site_langs[$i]['name'] = ucfirst($rs->fields[1]);
		$site_langs[$i]['sel'] = ($rs->fields[0] == $data['site_language']) ? 1 : 0;
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('site_langs', $site_langs);

	// age range select
	$max_in = $settings['max_age_limit'];
	$min_in = $settings['min_age_limit'];
	$max_age_arr = range(intval($max_in), intval($min_in));
	$min_age_arr = range(intval($min_in), intval($max_in));

	if (intval($data['age_min'])) {
		$min_age_sel = $data['age_min'];
	}
		
	if (intval($data['age_max'])) {
		$max_age_sel = $data['age_max'];
	}
		
	$smarty->assign('age_max', $max_age_arr);
	$smarty->assign('age_min', $min_age_arr);
	$smarty->assign('min_age_sel', $min_age_sel);
	$smarty->assign('max_age_sel', $max_age_sel);
	
	return $form;
}


function SavePersonalForm($profile_percent)
{
	global $smarty, $dbconn, $config, $lang, $user; // $check_email_domain;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$data							= $_POST;
	
	// login
	$data['login']					= FormFilter($_POST['login']);
	
	// personal info
	$data['fname']					= isset($_POST['fname'])				? FormFilter($_POST['fname']) : '';
	$data['sname']					= isset($_POST['sname'])				? FormFilter($_POST['sname']) : '';
	$data['mm_nickname']			= isset($_POST['mm_nickname'])			? FormFilter($_POST['mm_nickname']) : '';
	$data['gender']					= isset($_POST['gender'])				? intval($_POST['gender']) : 0;
	$data['mm_marital_status']		= isset($_POST['mm_marital_status'])	? (int) $_POST['mm_marital_status'] : 0;
	$data['b_year']					= (int) $_POST['b_year'];
	$data['b_month']				= (int) $_POST['b_month'];
	$data['b_day']					= (int) $_POST['b_day'];
	$data['mm_place_of_birth']		= isset($_POST['mm_place_of_birth'])	? FormFilter($_POST['mm_place_of_birth']) : '';
	$data['id_nationality']			= isset($_POST['id_nationality'])		? (int) $_POST['id_nationality'] : 0;
	$data['mm_id_number']			= isset($_POST['mm_id_number'])			? FormFilter($_POST['mm_id_number']) : '';
	
	// contact info
	$data['email']						= isset($_POST['email'])					? FormFilter($_POST['email']) : '';
	$data['mm_contact_phone_number']	= isset($_POST['mm_contact_phone_number'])	? FormFilter($_POST['mm_contact_phone_number']) : '';
	$data['mm_contact_mobile_number']	= isset($_POST['mm_contact_mobile_number'])	? FormFilter($_POST['mm_contact_mobile_number']) : '';
	
	// voip
	$data['phone']					= isset($_POST['phone'])				? $_POST['phone'] : '';
	
	// couple
	$data['couple']					= isset($_POST['couple'])				? (int) $_POST['couple'] : 0;
	$data['couple_user']			= isset($_POST['couple_user'])			? (int) $_POST['couple_user'] : 0;
	$data['couple_login']			= isset($_POST['couple_login'])			? FormFilter($_POST['couple_login']) : '';
	
	// address info
	$data['id_country']				= isset($_POST['id_country'])			? (int) $_POST['id_country'] : 0;
	$data['id_region']				= isset($_POST['id_region'])			? (int) $_POST['id_region'] : 0;
	$data['id_city']				= isset($_POST['id_city'])				? (int) $_POST['id_city'] : 0;
	$data['mm_city']				= isset($_POST['mm_city'])				? FormFilter($_POST['mm_city']) : '';
	$data['zipcode']				= isset($_POST['zipcode'])				? FormFilter($_POST['zipcode']) : '';
	$data['mm_address_1']			= isset($_POST['mm_address_1'])			? FormFilter($_POST['mm_address_1']) : '';
	$data['mm_address_2']			= isset($_POST['mm_address_2'])			? FormFilter($_POST['mm_address_2']) : '';
	
	// language info
	$data['id_language_1']			= isset($_POST['id_language_1'])		? (int) $_POST['id_language_1'] : 0;
	$data['id_language_2']			= isset($_POST['id_language_2'])		? (int) $_POST['id_language_2'] : 0;
	$data['id_language_3']			= isset($_POST['id_language_3'])		? (int) $_POST['id_language_3'] : 0;
	$data['mm_level_of_english']	= isset($_POST['mm_level_of_english'])	? (int) $_POST['mm_level_of_english'] : 0;
	$data['site_language']			= isset($_POST['site_language'])		? (int) $_POST['site_language'] : $config['default_lang'];
	
	// search
	$data['gender_search']			= isset($_POST['gender_search'])		? (int) $_POST['gender_search'] : 0;
	$data['couple_search']			= isset($_POST['couple_search'])		? (int) $_POST['couple_search'] : 0;
	$data['age_min']				= isset($_POST['age_min'])				? (int) $_POST['age_min'] : 18;
	$data['age_max']				= isset($_POST['age_max'])				? (int) $_POST['age_max'] : 99;
	
	if (empty($_POST['relation'])) {
		$data['relation'] = MM_DEFAULT_RELATIONSHIP_ID;
	} elseif ($_POST['relation'][0] == '0') {
		$data['relation'] = '0';
	} else {
		$data['relation'] = implode(',', $_POST['relation']);
	}
	
	// employment info
	$data['mm_employment_status']	= isset($_POST['mm_employment_status'])	? (int) $_POST['mm_employment_status'] : 0;
	$data['mm_business_name']		= isset($_POST['mm_business_name'])		? FormFilter($_POST['mm_business_name']) : '';
	$data['mm_employer_name']		= isset($_POST['mm_employer_name'])		? FormFilter($_POST['mm_employer_name']) : '';
	$data['mm_job_position']		= isset($_POST['mm_job_position'])		? FormFilter($_POST['mm_job_position']) : '';
	$data['mm_work_address']		= isset($_POST['mm_work_address'])		? FormFilter($_POST['mm_work_address']) : '';
	$data['mm_work_phone_number']	= isset($_POST['mm_work_phone_number'])	? FormFilter($_POST['mm_work_phone_number']) : '';
	
	// reference 1
	$data['mm_ref_1_first_name']	= isset($_POST['mm_ref_1_first_name'])	? FormFilter($_POST['mm_ref_1_first_name']) : '';
	$data['mm_ref_1_last_name']		= isset($_POST['mm_ref_1_last_name'])	? FormFilter($_POST['mm_ref_1_last_name']) : '';
	$data['mm_ref_1_relationship']	= isset($_POST['mm_ref_1_relationship'])? FormFilter($_POST['mm_ref_1_relationship']) : '';
	$data['mm_ref_1_phone_number']	= isset($_POST['mm_ref_1_phone_number'])? FormFilter($_POST['mm_ref_1_phone_number']) : '';
	
	// reference 2
	$data['mm_ref_2_first_name']	= isset($_POST['mm_ref_2_first_name'])	? FormFilter($_POST['mm_ref_2_first_name']) : '';
	$data['mm_ref_2_last_name']		= isset($_POST['mm_ref_2_last_name'])	? FormFilter($_POST['mm_ref_2_last_name']) : '';
	$data['mm_ref_2_relationship']	= isset($_POST['mm_ref_2_relationship'])? FormFilter($_POST['mm_ref_2_relationship']) : '';
	$data['mm_ref_2_phone_number']	= isset($_POST['mm_ref_2_phone_number'])? FormFilter($_POST['mm_ref_2_phone_number']) : '';
	
	// height and weight
	$data['id_height']				= isset($_POST['id_height'])			? (int) $_POST['id_height'] : 0;
	$data['id_weight']				= isset($_POST['id_weight'])			? (int) $_POST['id_weight'] : 0;
	
	// headline
	$data['headline']				= isset($_POST['headline'])				? stripn(FormFilter($_POST['headline'])) : '';
	
	// email notification
	$data['use_notification']		= isset($_POST['use_notification'])		? (int) $_POST['use_notification'] : 0;
	
	// privacy settings
	$data['hide_online']			= isset($_POST['hide_online'])			? (int) $_POST['hide_online'] : 0;
	
	$default_lady_visible			= ($data['gender'] == 1) ? 1 : 0;
	$default_guy_visible			= ($data['gender'] == 2) ? 1 : 0;
	
	$data['visible_lady']			= isset($_POST['visible_lady'])			? (int) $_POST['visible_lady'] : $default_lady_visible;
	$data['visible_guy']			= isset($_POST['visible_guy'])			? (int) $_POST['visible_guy'] : $default_guy_visible;
	
	if ($data['visible_lady'] == 1) {
		$data['vis_lady_1']			= 1;
		$data['vis_lady_2']			= 1;
		$data['vis_lady_3']			= 1;
	} else {
		$data['vis_lady_1']			= isset($_POST['vis_lady_1']) ? (int) $_POST['vis_lady_1'] : 0;
		$data['vis_lady_2']			= isset($_POST['vis_lady_2']) ? (int) $_POST['vis_lady_2'] : 0;
		$data['vis_lady_3']			= isset($_POST['vis_lady_3']) ? (int) $_POST['vis_lady_3'] : 0;
	}
	
	if ($data['visible_guy'] == 1) {
		$data['vis_guy_1']			= 1;
		$data['vis_guy_2']			= 1;
		$data['vis_guy_3']			= 1;
		$data['vis_guy_4']			= 1;
	} else {
		$data['vis_guy_1']			= isset($_POST['vis_guy_1']) ? (int) $_POST['vis_guy_1'] : 0;
		$data['vis_guy_2']			= isset($_POST['vis_guy_2']) ? (int) $_POST['vis_guy_2'] : 0;
		$data['vis_guy_3']			= isset($_POST['vis_guy_3']) ? (int) $_POST['vis_guy_3'] : 0;
		$data['vis_guy_4']			= isset($_POST['vis_guy_4']) ? (int) $_POST['vis_guy_4'] : 0;
	}
	
	$data['promotion_1']			= isset($_POST['promotion_1']) ? (int) $_POST['promotion_1'] : 0;
	$data['promotion_2']			= isset($_POST['promotion_2']) ? (int) $_POST['promotion_2'] : 0;
	$data['promotion_3']			= isset($_POST['promotion_3']) ? (int) $_POST['promotion_3'] : 0;
	
	// biography
	$data['about_me']				= isset($_POST['about_me'])			? stripn(FormFilter($_POST['about_me'])) : '';
	$data['what_i_do']				= isset($_POST['what_i_do'])		? stripn(FormFilter($_POST['what_i_do'])) : '';
	$data['my_idea']				= isset($_POST['my_idea'])			? stripn(FormFilter($_POST['my_idea'])) : '';
	$data['hoping_to_find']			= isset($_POST['hoping_to_find'])	? stripn(FormFilter($_POST['hoping_to_find'])) : '';
	
	// dependent values
	if (!$data['gender_search'] && $data['gender']) {
		$data['gender_search'] = ($data['gender'] == GENDER_MALE) ? GENDER_FEMALE : GENDER_MALE;
	}
	
	// store sanitized input in $_POST for postback on error
	$_POST = $data;
	
	//----------------
	// validity check
	//----------------
	
	$mandatory = array();
	
	include './customize/profile_switchboard.php';
	
	// check mandatory fields
	
	$err		= '';
	$err_field	= array();
	$bullet		= '&#8226; ';
	
	$sb_mandatory = ($user[ AUTH_IS_APPLICANT ] ? SB_REGISTRATION : SB_EDIT);
	
	if (!strlen($data['login'])) {
		$err .= $lang['users']['nick'] . ', ';
		$err_field['login'] = 1;
	}
	
	if ($mandatory['fname'] & $sb_mandatory && !strlen($data['fname'])) {
		$err .= $lang['users']['fname'] . ', ';
		$err_field['fname'] = 1;
	}
	
	if ($mandatory['sname'] & $sb_mandatory && !strlen($data['sname'])) {
		$err .= $lang['users']['sname'] . ', ';
		$err_field['sname'] = 1;
	}
	
	if ($mandatory['mm_nickname'] & $sb_mandatory && !strlen($data['mm_nickname'])) {
		$err .= $lang['users']['mm_nickname'] . ', ';
		$err_field['mm_nickname'] = 1;
	}
	
	if ($mandatory['gender'] & $sb_mandatory && !$data['gender']) {
		$err .= $lang['users']['gender'] . ', ';
		$err_field['gender'] = 1;
	}
	
	if ($mandatory['mm_marital_status'] & $sb_mandatory && !$data['mm_marital_status']) {
		$err .= $lang['users']['mm_marital_status'] . ', ';
		$err_field['mm_marital_status'] = 1;
	}
	
	if ($mandatory['mm_place_of_birth'] & $sb_mandatory && !strlen($data['mm_place_of_birth'])) {
		$err .= $lang['users']['mm_place_of_birth'] . ', ';
		$err_field['mm_place_of_birth'] = 1;
	}
	
	if ($mandatory['id_nationality'] & $sb_mandatory && !$data['id_nationality']) {
		$err .= $lang['users']['nationality'] . ', ';
		$err_field['id_nationality'] = 1;
	}
	
	if ($mandatory['mm_id_number'] & $sb_mandatory && $data['gender'] == 2 && !strlen($data['mm_id_number'])) {
		$err .= $lang['users']['mm_id_number'] . ', ';
		$err_field['mm_id_number'] = 1;
	}
	
	if ($mandatory['email'] & $sb_mandatory && !strlen($data['email'])) {
		$err .= $lang['users']['email'] . ', ';
		$err_field['email'] = 1;
	}
	
	if ($mandatory['mm_contact_phone_number'] & $sb_mandatory && !strlen($data['mm_contact_phone_number'])) {
		$err .= $lang['users']['mm_contact_phone_number'] . ', ';
		$err_field['mm_contact_phone_number'] = 1;
	}
	
	if ($mandatory['mm_contact_mobile_number'] & $sb_mandatory && !strlen($data['mm_contact_mobile_number'])) {
		$err .= $lang['users']['mm_contact_mobile_number'] . ', ';
		$err_field['mm_contact_mobile_number'] = 1;
	}
	
	if ($mandatory['id_country'] & $sb_mandatory && !$data['id_country']) {
		$err .= $lang['users']['country'] . ', ';
		$err_field['id_country'] = 1;
	}
	
	if ($mandatory['id_region'] & $sb_mandatory && !$data['id_region']) {
		$err .= $lang['users']['region'] . ', ';
		$err_field['id_region'] = 1;
	}
	
	if ($mandatory['id_city'] & $sb_mandatory && !$data['id_city']) {
		$err .= $lang['users']['city'] . ', ';
		$err_field['id_city'] = 1;
	}
	
	if ($mandatory['mm_city'] & $sb_mandatory && !strlen($data['mm_city'])) {
		$err .= $lang['users']['city'] . ', ';
		$err_field['mm_city'] = 1;
	}
	
	if ($mandatory['zipcode'] & $sb_mandatory && !strlen($data['zipcode'])) {
		$err .= $lang['users']['zipcode'] . ', ';
		$err_field['zipcode'] = 1;
	}
	
	if ($mandatory['mm_address_1'] & $sb_mandatory && !strlen($data['mm_address_1'])) {
		$err .= $lang['users']['mm_address_1'] . ', ';
		$err_field['mm_address_1'] = 1;
	}
	
	if ($mandatory['mm_address_2'] & $sb_mandatory && !strlen($data['mm_address_2'])) {
		$err .= $lang['users']['mm_address_2'] . ', ';
		$err_field['mm_address_2'] = 1;
	}
	
	if ($mandatory['id_language_1'] & $sb_mandatory && !$data['id_language_1']) {
		$err .= $lang['users']['language'] . ', ';
		$err_field['id_language_1'] = 1;
	}
	
	if ($mandatory['id_language_2'] & $sb_mandatory && !$data['id_language_2']) {
		$err .= $lang['users']['language'] . ', ';
		$err_field['id_language_2'] = 1;
	}
	
	if ($mandatory['id_language_3'] & $sb_mandatory && !$data['id_language_3']) {
		$err .= $lang['users']['language'] . ', ';
		$err_field['id_language_3'] = 1;
	}
	
	if ($mandatory['mm_level_of_english'] & $sb_mandatory && !$data['mm_level_of_english']) {
		$err .= $lang['users']['mm_level_of_english'] . ', ';
		$err_field['mm_level_of_english'] = 1;
	}
	
	if ($mandatory['site_language'] & $sb_mandatory && !$data['site_language']) {
		$err .= $lang['users']['site_language'] . ', ';
		$err_field['site_language'] = 1;
	}
	
	if ($mandatory['mm_employment_status'] & $sb_mandatory && !$data['mm_employment_status']) {
		$err .= $lang['users']['mm_employment_status'] . ', ';
		$err_field['mm_employment_status'] = 1;
	}
	
	if ($mandatory['mm_business_name'] & $sb_mandatory && $data['mm_employment_status'] == 2 && !strlen($data['mm_business_name'])) {
		$err .= $lang['users']['mm_business_name'] . ', ';
		$err_field['mm_business_name'] = 1;
	}
	
	if ($mandatory['mm_employer_name'] & $sb_mandatory && $data['mm_employment_status'] == 3 && !strlen($data['mm_employer_name'])) {
		$err .= $lang['users']['mm_employer_name'] . ', ';
		$err_field['mm_employer_name'] = 1;
	}
	
	if ($mandatory['mm_job_position'] & $sb_mandatory && $data['mm_employment_status'] != 1 && !strlen($data['mm_job_position'])) {
		$err .= $lang['users']['mm_job_position'] . ', ';
		$err_field['mm_job_position'] = 1;
	}
	
	if ($mandatory['mm_work_address'] & $sb_mandatory && $data['mm_employment_status'] != 1 && !strlen($data['mm_work_address'])) {
		$err .= $lang['users']['mm_work_address'] . ', ';
		$err_field['mm_work_address'] = 1;
	}
	
	if ($mandatory['mm_work_phone_number'] & $sb_mandatory && $data['mm_employment_status'] != 1 && !strlen($data['mm_work_phone_number'])) {
		$err .= $lang['users']['mm_work_phone_number'] . ', ';
		$err_field['mm_work_phone_number'] = 1;
	}
	
	if ($mandatory['mm_ref_1_first_name'] & $sb_mandatory && !strlen($data['mm_ref_1_first_name'])) {
		$err .= $lang['users']['mm_reference_1'].' '.$lang['users']['fname'] . ', ';
		$err_field['mm_ref_1_first_name'] = 1;
	}
	
	if ($mandatory['mm_ref_1_last_name'] & $sb_mandatory && !strlen($data['mm_ref_1_last_name'])) {
		$err .= $lang['users']['mm_reference_1'].' '.$lang['users']['sname'] . ', ';
		$err_field['mm_ref_1_last_name'] = 1;
	}
	
	if ($mandatory['mm_ref_1_relationship'] & $sb_mandatory && !strlen($data['mm_ref_1_relationship'])) {
		$err .= $lang['users']['mm_reference_1'].' '.$lang['users']['mm_reference_relationship'].', ';
		$err_field['mm_ref_1_relationship'] = 1;
	}
	
	if ($mandatory['mm_ref_1_phone_number'] & $sb_mandatory && !strlen($data['mm_ref_1_phone_number'])) {
		$err .= $lang['users']['mm_reference_1'].' '.$lang['users']['mm_reference_phone_number'].', ';
		$err_field['mm_ref_1_phone_number'] = 1;
	}
	
	if ($mandatory['mm_ref_2_first_name'] & $sb_mandatory && !strlen($data['mm_ref_2_first_name'])) {
		$err .= $lang['users']['mm_reference_2'].' '.$lang['users']['fname'] . ', ';
		$err_field['mm_ref_2_first_name'] = 1;
	}
	
	if ($mandatory['mm_ref_2_last_name'] & $sb_mandatory && !strlen($data['mm_ref_2_last_name'])) {
		$err .= $lang['users']['mm_reference_2'].' '.$lang['users']['sname'] . ', ';
		$err_field['mm_ref_2_last_name'] = 1;
	}
	
	if ($mandatory['mm_ref_2_relationship'] & $sb_mandatory && !strlen($data['mm_ref_2_relationship'])) {
		$err .= $lang['users']['mm_reference_2'].' '.$lang['users']['mm_reference_relationship'].', ';
		$err_field['mm_ref_2_relationship'] = 1;
	}
	
	if ($mandatory['mm_ref_2_phone_number'] & $sb_mandatory && !strlen($data['mm_ref_2_phone_number'])) {
		$err .= $lang['users']['mm_reference_2'].' '.$lang['users']['mm_reference_phone_number'].', ';
		$err_field['mm_ref_2_phone_number'] = 1;
	}
		
	if ($err)
	{
		$err = $bullet . $lang['err']['invalid_fields'] . '<br/><br/>' . trim($err, ', ');
	}
	else
	{
		// login not valid
		$login_err = LoginFilter($data['login']);
		
		if ($login_err) {
			if ($err) $err .= '<br><br>';
			$err .= $bullet . $login_err;
			$err_field['login'] = 1;
		}
		
		// login already exists
		$check_exist = $dbconn->getOne('SELECT 1 FROM '.USERS_TABLE.' WHERE login = ? AND id <> ?', array($data['login'], $id_user));
		
		if (!empty($check_exist)) {
			if ($err) $err .= '<br><br>';
			$err .= $bullet . $lang['err']['exists_login'];
			$err_field['login'] = 1;
		}
		
		// email not valid
		$email_err = EmailFilter($data['email']);
		
		if ($email_err) {
			if ($err) $err .= '<br><br>';
			$err .= $bullet . $email_err;
			$err_field['email'] = 1;
		}
		
		// email already exists
		unset($check_exist);
		
		$check_exist = $dbconn->getOne('SELECT 1 FROM '.USERS_TABLE.' WHERE email = ? AND id <> ?', array($data['email'], $id_user));
		
		if (!empty($check_exist)) {
			if ($err) $err .= '<br><br>';
			$err .= $bullet . $lang['err']['exists_email'];
			$err_field['email'] = 1;
		}
		
		// voip phone not valid
		$phone_err = PhoneFilter($data['phone']);
		
		if ($phone_err) {
			if ($err) $err .= '<br><br>';
			$err .= $bullet . $phone_err;
			$err_field['phone'] = 1;
		}
		
		if ($config['voipcall_feature'] == 1)
		{
			// phone already exists
			unset($check_exist);
			
			$check_exist = $dbconn->getOne('SELECT 1 FROM '.USERS_TABLE.' WHERE phone <> "" AND phone = ? AND id <> ?', array($data['phone'], $id_user));
			
			if (!empty($check_exist)) {
				if ($err) $err .= '<br><br>';
				$err .= $bullet . $lang['err']['exists_phone'];
				$err_field['phone'] = 1;
			}
			
			// check phone update
			$phone_update = false;
			
			unset($check_exist);
			
			$check_exist = $dbconn->getOne('SELECT id FROM '.USERS_TABLE.' WHERE phone <> ? AND id = ?', array($data['phone'], $id_user));
			
			if (!empty($check_exist)) {
				$phone_update = true;
			}
		}
		
		// birthdate not valid
		if (checkdate($data['b_month'], $data['b_day'], $data['b_year'])) {
			$data['date_birthday'] = sprintf('%04d-%02d-%02d', $data['b_year'], $data['b_month'], $data['b_day']);
		} else {
			if ($err) $err .= '<br><br>';
			$err .= $bullet . $lang['err']['invalid_date'];
			$err_field['date_birthday'] = 1;
		}
		
		if (!$data['couple_user'] && !empty($data['couple_login'])) {
			// couple login already exists
			$data['couple_user'] = $dbconn->getOne('SELECT id FROM '.USERS_TABLE.' WHERE login = ? AND id <> ?', array($data['couple_login'], $id_user));
			if (!empty($data['couple_user'])) {
				$couple_send = true;
			} else {
				if ($err) $err .= '<br><br>';
				$err .= $bullet . $lang['err']['wrong_couple_login'];
				$err_field['couple'] = 1;
			}
		}
		
		// check badwords and contacts in headline
		$badwords_err = BadWordsCont($data['headline'], 4);
		
		if ($badwords_err) {
			if ($err) $err .= '<br><br>';
			$err .= $bullet . $badwords_err;
			$err_field['headline'] = 1;
		}
		
		if (check_filter($data['headline'])) {
			if ($err) $err .= '<br><br>';
			$err .= $bullet . $lang['err']['info_finding_1'];
			$err_field['headline'] = 1;
		}
		
		// check zipcode
		$rs = $dbconn->Execute('SELECT name, value FROM '.SETTINGS_TABLE.' WHERE name IN ("zip_letters", "zip_count")');
		
		while (!$rs->EOF) {
			$zip_settings[$rs->fields[0]] = $rs->fields[1];
			$rs->MoveNext();
		}
		
		if ($data['zipcode'] != '') {
			if (!$zip_settings['zip_letters']) {
				$data['zipcode'] = intval(substr($data['zipcode'], 0, $zip_settings['zip_count']));
			} else {
				$data['zipcode'] = substr($data['zipcode'], 0, $zip_settings['zip_count']);
			}
		}
	}
	
	if ($err) {
		$smarty->assign('err_field', $err_field);
		return $err;
	}
	
	// UPDATE USER
	
	// 5 fields per row
	$strSQL =
		'UPDATE '.USERS_TABLE.' SET
				fname = ?, sname = ?, gender = ?, couple = ?, couple_user = ?,
				login = ?, email = ?, id_country = ?, id_region = ?, id_city = ?,
				zipcode = ?, headline = ?, id_nationality = ?, id_language_1 = ?, id_language_2 = ?,
				id_language_3 = ?, date_birthday = ?, site_language = ?, phone = ?, mm_nickname = ?,
				mm_id_number = ?, mm_contact_phone_number = ?, mm_contact_mobile_number = ?, mm_marital_status = ?, mm_place_of_birth = ?,
				mm_city = ?, mm_address_1 = ?, mm_address_2 = ?, mm_level_of_english = ?, mm_employment_status = ?,
				mm_business_name = ?, mm_employer_name = ?, mm_job_position = ?, mm_work_address = ?, mm_work_phone_number = ?,
				mm_ref_1_first_name = ?, mm_ref_1_last_name = ?, mm_ref_1_relationship = ?, mm_ref_1_phone_number = ?, mm_ref_2_first_name = ?,
				mm_ref_2_last_name = ?, mm_ref_2_relationship = ?, mm_ref_2_phone_number = ?, about_me = ?, what_i_do = ?,
				my_idea = ?, hoping_to_find = ?, id_height = ?, id_weight = ?
		  WHERE id = ? AND root_user = "0"';
	
	$dbconn->Execute($strSQL, array(
		$data['fname'], $data['sname'], (string)$data['gender'], (string)$data['couple'], $data['couple_user'],
		$data['login'], $data['email'], $data['id_country'], $data['id_region'], $data['id_city'],
		$data['zipcode'], $data['headline'], $data['id_nationality'], $data['id_language_1'], $data['id_language_2'],
		$data['id_language_3'], $data['date_birthday'], $data['site_language'], $data['phone'], $data['mm_nickname'],
		$data['mm_id_number'], $data['mm_contact_phone_number'], $data['mm_contact_mobile_number'], $data['mm_marital_status'], $data['mm_place_of_birth'],
		$data['mm_city'], $data['mm_address_1'], $data['mm_address_2'], $data['mm_level_of_english'], $data['mm_employment_status'],
		$data['mm_business_name'], $data['mm_employer_name'], $data['mm_job_position'], $data['mm_work_address'], $data['mm_work_phone_number'],
		$data['mm_ref_1_first_name'], $data['mm_ref_1_last_name'], $data['mm_ref_1_relationship'], $data['mm_ref_1_phone_number'], $data['mm_ref_2_first_name'],
		$data['mm_ref_2_last_name'], $data['mm_ref_2_relationship'], $data['mm_ref_2_phone_number'], $data['about_me'], $data['what_i_do'],
		$data['my_idea'], $data['hoping_to_find'], $data['id_height'], $data['id_weight'],
		$id_user
	));
	
	// UPDATE PRIVACY SETTINGS
	$strSQL =
		'UPDATE '.USER_PRIVACY_SETTINGS.' SET
				hide_online = ?,
				promotion_1 = ?, promotion_2 = ?, promotion_3 = ?,
				visible_lady = ?, visible_guy = ?,
				vis_lady_1 = ?, vis_lady_2 = ?, vis_lady_3 = ?,
				vis_guy_1 = ?, vis_guy_2 = ?, vis_guy_3 = ?, vis_guy_4 = ?
		  WHERE id_user = ?';
	
	$dbconn->Execute($strSQL, array(
		(string)$data['hide_online'],
		(string)$data['promotion_1'], (string)$data['promotion_2'], (string)$data['promotion_3'],
		(string)$data['visible_lady'], (string)$data['visible_guy'],
		(string)$data['vis_lady_1'], (string)$data['vis_lady_2'], (string)$data['vis_lady_3'],
		(string)$data['vis_guy_1'], (string)$data['vis_guy_2'], (string)$data['vis_guy_3'], (string)$data['vis_guy_4'],
		$id_user));
	
	// UPDATE VOIP
	if (!intval($dbconn->ErrorNo()) && intval($config['voipcall_feature']) == 1 && $phone_update) {
		include './include/class.voip.php';
		$VoIp = new DatingVoIp($dbconn, $config, $id_user);
		if (!$VoIp->UpdateDatingMemberPhone($data['phone'])) {
			return $VoIp->GetErrorMsg();
		}
	}
	
	$_SESSION['language_cd'] = $data['site_language'];
	
	// UPDATE SOLVE360 CONTACT
	if (SOLVE360_CONNECTION) {
		require_once $config['site_path'].'/include/Solve360Service.php';
		$solve360Service = new Solve360Service(SOLVE360_USER, SOLVE360_TOKEN);
		
		$country = $dbconn->GetOne('SELECT name FROM '.COUNTRY_SPR_TABLE.' WHERE id = ?', array($data['id_country']));
		$region = $dbconn->GetOne('SELECT name FROM '.REGION_SPR_TABLE.' WHERE id = ?', array($data['id_region']));
		$nationality = $dbconn->GetOne('SELECT name FROM '.NATION_SPR_TABLE.' WHERE id = ?', array($data['id_nationality']));
		$language_1 = $dbconn->GetOne('SELECT name FROM '.LANGUAGE_SPR_TABLE.' WHERE id = ?', array($data['id_language_1']));
		$marital_status = $dbconn->GetOne('SELECT name FROM '.MARITAL_STATUS_SPR_TABLE.' WHERE id = ?', array($data['mm_marital_status']));
		$level_of_english = $dbconn->GetOne('SELECT name FROM '.LEVEL_ENGLISH_SPR_TABLE.' WHERE id = ?', array($data['mm_level_of_english']));
		
		$solve360 = array();
		require $config['site_path'].'/include/Solve360CustomFields.php';
		
		$contactData = array(
		#	$solve360['TLDF ID Number']			=> $id_user,							// immutable
			'firstname'							=> $data['fname'],
			'lastname'							=> $data['sname'],
		#	$solve360['TLDF Status']			=> ($status ? 'Good' : 'Inactive'),		// set by admin
		#	$solve360['Platinum Verified']		=> 'No',								// set by admin
		#	$solve360['TLDF Confirmed']			=> 'No',								// not changed here
			$solve360['TLDF Login']				=> $data['login'],
		#	$solve360['Gender']					=> ($data['gender'] == GENDER_MALE ? 'Guy' : 'Lady'),	// immutable
			'personalemail'						=> $data['email'],
			$solve360['Country']				=> $country,							// lookup
			$solve360['Region']					=> $region,								// lookup
			$solve360['Nationality']			=> $nationality,						// lookup
			$solve360['Language 1']				=> $language_1,							// lookup
			$solve360['Birthday']				=> substr($data['date_birthday'], 0, 10),
			$solve360['Last Seen TLDF']			=> date('Y-m-d H:i:s'),					// date/time
		#	$solve360['Registration Date']		=> date('Y-m-d H:i:s'),					// date/time, immutable
		#	$solve360['TLDF Login Count']		=> 0,									// not changed here
			$solve360['Nick Name']				=> $data['mm_nickname'],
			$solve360['National ID Number']		=> $data['mm_id_number'],
		#	$solve360['ID Type']				=> '',									// no input control
			'homephone'							=> $data['mm_contact_phone_number'],
			'cellularphone'						=> $data['mm_contact_mobile_number'],
			$solve360['Marital Status']			=> $marital_status,						// lookup
			$solve360['Place Of Birth']			=> $data['mm_place_of_birth'],
			$solve360['City']					=> $data['mm_city'],
			$solve360['Home Address 1']			=> $data['mm_address_1'],
			$solve360['Home Address 2']			=> $data['mm_address_2'],
		#	$solve360['Home Address 3']			=> '',									// no input control
			$solve360['Level Of English']		=> $level_of_english,					// lookup
			$solve360['Employer Name']			=> $data['mm_employer_name'],
			'jobtitle'							=> $data['mm_job_position'],
			'businessaddress'					=> $data['mm_work_address'],
			'businessphonedirect'				=> $data['mm_work_phone_number'],
		#	$solve360['Platinum Form']			=> '',									// date/time, not changed here
		#	$solve360['Platinum Paid']			=> '',									// date/time, not changed here
		#	$solve360['TLDE Express Interest']	=> '',									// date/time, not changed here
		#	$solve360['Current Group']			=> $group_name,							// not changed here
		#	$solve360['TLDF Trial Start Date']	=> '',									// date/time, not changed here
		#	$solve360['TLDF Membership Ends']	=> UNLIMITED_DATE_END,					// date/time, not changed here
		);
		
		$id_solve360 = $dbconn->GetOne('SELECT id_solve360 FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
		
		if (!empty($id_solve360)) {
			$contact = $solve360Service->editContact($id_solve360, $contactData);
			#var_dump($contact); exit;
			if (isset($contact->errors)) {
				$subject = 'Error while updating contact after user edits profile';
				solve360_api_error($contact, $subject, $data['login']);
			}
		}
		// maybe add contact if not found
	}
	
	// PROFILE UPDATE EMAIL TO USER
	if ($data['use_notification'] == 1)
	{
		// include mail language file
		$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($data['site_language']));
		$lang_mail = array();
		include $config['path_lang'].'mail/'.$lang_file;
		
		$content			= array();
		$content['fname']	= $data['fname'];
		$content['sname']	= $data['sname'];
		$content['login']	= $data['login'];
		$content['email']	= $data['email'];
		
		// gender suffix
		$suffix = ($data['gender'] == GENDER_MALE) ? '_e' : '_t';
		
		// subject
		$subject = $lang_mail['profile_update'.$suffix]['subject'];
		
		// recipient
		$name_to = trim($data['fname'].' '.$data['sname']);
		
		SendMail($data['site_language'], $data['email'], $config['site_email'], $subject, $content,
			'mail_profile_update_user', null, $name_to, '', 'profile_update', $data['gender']);
	}
	
	// UPDATE SEARCH PREFERENCES
	unset($check_exist);
	
	$check_exist = $dbconn->getOne('SELECT 1 FROM '.USER_MATCH_TABLE.' WHERE id_user = ?', array($id_user));
	
	if (!empty($check_exist)) {
		$dbconn->Execute(
			'UPDATE '.USER_MATCH_TABLE.'
				SET gender = ?, couple = ?, age_max = ?, age_min = ?, id_relationship = ?
			  WHERE id_user = ?',
			array((string)$data['gender_search'], (string)$data['couple_search'], $data['age_max'], $data['age_min'], $data['relation'], $id_user));
	} else {
		$dbconn->Execute(
			'INSERT INTO '.USER_MATCH_TABLE.'
				SET id_user = ?, gender = ?, couple = ?, age_max = ?, age_min = ?, id_relationship = ?',
			array($id_user, (string)$data['gender_search'], (string)$data['couple_search'], $data['age_max'], $data['age_min'], $data['relation']));
	}
	
	// COUPLE ACTION
	if (isset($couple_send) && $couple_send) {
		$body = $lang['users']['couple_accept_message'];
		$body.= '<br><br><a href="myprofile.php?sel=couple&id='.$id_user.'">';
		$body.= $lang['users']['couple_accept_link'];
		$body.= '</a><br><br>';
		
		$strSQL =
			'INSERT INTO '.MAILBOX_TABLE.'
				SET id_from = ?, id_to = ?, subject = ?, body = ?,
					date_creation = NOW(), was_read = "0", deleted_from = "0", deleted_to = "0"';
		$rs = $dbconn->Execute($strSQL, array($id_user, $data['couple_user'], $lang['users']['couple_accept_subject'], $body));
	}
	
	if (isset($data['couple_delete']) && $data['couple_delete'] == 1) {
		CoupleAction($data['couple_user'], 'delete');
	}
	
	// NEWSLETTER UPDATE
	if (!function_exists('UpdateNewsletterUserData')) {
		include_once dirname(__FILE__).'/functions_newsletter.php';
	}
	
	UpdateNewsletterUserData($id_user, $data['fname'], $data['sname'], $data['email']);
	
	// PROFILE COMPLETION UPDATE
	$profile_percent->UpdateSection1Percent();
	
	return '';
}

//------------------
// Description Form
//------------------

function DescriptionForm()
{
	global $smarty, $dbconn, $user;
	
	$id = $user[ AUTH_ID_USER ];
	
	// hiddens
	$form['hiddens'] = '<input type="hidden" name="sel" value="save_2">';
	$form['hiddens'].= '<input type="hidden" name="e" value="1">';
	$form['hiddens'].= '<input type="hidden" name="id" value="'.$id.'">';
	
	// multi-lang tables
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	$rs = $dbconn->Execute('SELECT id_weight, id_height, root_user, guest_user FROM '.USERS_TABLE.' WHERE id = ?', array($id));
	$row = $rs->GetRowAssoc(false);
	
	$data['id_weight'] = intval($row['id_weight']);
	$data['id_height'] = intval($row['id_height']);
	$data['root'] = $row['root_user'] ? $row['root_user'] : $row['guest_user'];
	
	$smarty->assign('data', $data);
	
	// weight select
	$strSQL =
		'SELECT DISTINCT a.id, b.'.$field_name.' AS value
		   FROM '.WEIGHT_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = "'.$multi_lang->TableKey(WEIGHT_SPR_TABLE).'" AND b.id_reference = a.id
	   ORDER BY a.sorter';
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$weight_arr = array();
	while (!$rs->EOF){
		$row = $rs->GetRowAssoc(false);
		$weight_arr[$i] = $row;
		$weight_arr[$i]['sel'] = (intval($data['id_weight']) == $row['id']) ? 1 : 0;
		$rs->MoveNext();
		$i++;
	}
	$smarty->assign('weight', $weight_arr);
	
	// height select
	$strSQL =
		'SELECT DISTINCT a.id, b.'.$field_name.' AS value
		   FROM '.HEIGHT_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = "'.$multi_lang->TableKey(HEIGHT_SPR_TABLE).'" AND b.id_reference = a.id
	   ORDER BY a.sorter';
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$height_arr = array();
	while (!$rs->EOF){
		$row = $rs->GetRowAssoc(false);
		$height_arr[$i] = $row;
		$height_arr[$i]['sel'] = (intval($data['id_height']) == $row['id']) ? 1 : 0;
		$rs->MoveNext();
		$i++;
	}
	$smarty->assign('height', $height_arr);
	
	// descr selects
	$sess_info = array();
	$rs = $dbconn->Execute('SELECT id_spr, id_value FROM '.DESCR_SPR_USER_TABLE.' WHERE id_user = ?', array($id));
	while (!$rs->EOF){
		$id_spr = $rs->fields[0];
		$id_value = $rs->fields[1];
		if (!isset($sess_info[$id_spr])) {
			$sess_info[$id_spr] = array();
		}
		$sess_info[$id_spr][count($sess_info[$id_spr])+1] = $id_value;
		$rs->MoveNext();
	}
	
	$table_key = $multi_lang->TableKey(DESCR_SPR_TABLE);
	$table_key_val = $multi_lang->TableKey(DESCR_SPR_VALUE_TABLE);
	
	$strSQL =
		'SELECT DISTINCT a.id, b.'.$field_name.' AS name, a.type
		   FROM '.DESCR_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = "'.$table_key.'" AND b.id_reference = a.id
	   ORDER BY a.sorter';
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	while (!$rs->EOF){
		$info[$i]['id'] = $rs->fields[0];
		$info[$i]['name'] = $rs->fields[1];
		$info[$i]['type'] = $rs->fields[2];
		$info[$i]['num'] = $i;
		
		$strSQL_opt =
			'SELECT DISTINCT a.id, b.'.$field_name.' AS name
			   FROM '.DESCR_SPR_VALUE_TABLE.' a
		  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = "'.$table_key_val.'" AND b.id_reference = a.id
			  WHERE a.id_spr = ?
		   ORDER BY name';
		
		$rs_opt = $dbconn->Execute($strSQL_opt, array($rs->fields[0]));
		$j = 0;
		$info = array();
		while (!$rs_opt ->EOF){
			$info[$i]['opt'][$j]['value'] = $rs_opt->fields[0];
			$info[$i]['opt'][$j]['name'] = $rs_opt->fields[1];
			if(isset($sess_info[$rs->fields[0]]) && is_array($sess_info[$rs->fields[0]]) && in_array(0, $sess_info[$rs->fields[0]])){
				$info[$i]['sel_all'] = '1';
			}else{
				$info[$i]['opt'][$j]['sel'] = (isset($sess_info[$rs->fields[0]]) && is_array($sess_info[$rs->fields[0]]) && in_array($rs_opt->fields[0], $sess_info[$rs->fields[0]]))?$rs_opt->fields[0]:0;
			}
			$rs_opt->MoveNext();	$j++;
		}
		$rs->MoveNext();	$i++;
	}
	$smarty->assign('info', $info);
	return $form;
}


function SaveDescriptionForm($profile_percent, $data, $spr, $info)
{
	global $dbconn, $user;
	
	$id_user = intval($user[ AUTH_ID_USER ]);
	
	$id_weight = intval($data['id_weight']);
	$id_height = intval($data['id_height']);
	
	$strSQL = 'UPDATE '.USERS_TABLE.' SET id_weight = ?, id_height = ? WHERE id = ? AND root_user = "0"';
	$dbconn->Execute($strSQL, array($id_weight, $id_height, $id_user));
	
	// save personal info
	if (intval($id_user) && is_array($spr))
	{
		$dbconn->Execute('DELETE FROM '.DESCR_SPR_USER_TABLE.' WHERE id_user = ?', array($id_user));
		
		for ($i = 0; $i < count($spr); $i++) {
			for ($j = 0; $j < count($info[$i]); $j++) {
				$id_spr = intval($spr[$i]);
				$id_value = intval($info[$i][$j]);
				
				$strSQL = 'INSERT INTO '.DESCR_SPR_USER_TABLE.' SET id_user = ?, id_spr = ?, id_value = ?';
				$dbconn->Execute($strSQL, array($id_user, $id_spr, $id_value));
			}
		}
	}
	
	$profile_percent->UpdateSection2Percent();
	
	return '';
}

//--------------
// Annonce Form
//--------------

function AnnonceForm()
{
	global $smarty, $dbconn, $user;
	
	$id = $user[ AUTH_ID_USER ];
	
	// hiddens
	$form['hiddens'] = '<input type="hidden" name="sel" value="save_3">';
	$form['hiddens'].= '<input type="hidden" name="e" value="1">';
	$form['hiddens'].= '<input type="hidden" name="id" value="'.$id.'">';
	
	$rs = $dbconn->Execute('SELECT root_user, guest_user, comment FROM '.USERS_TABLE.' WHERE id = ?', array($id));
	$row = $rs->GetRowAssoc(false);
	$data['root'] = $row['root_user'] ? $row['root_user'] : $row['guest_user'];
	$data['annonce'] = stripslashes($row['comment']);
	$smarty->assign('data', $data);
	
	return $form;
}

function SaveAnnonceForm($data)
{
	global $dbconn, $lang, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$annonce = stripn(FormFilter($data['annonce']));
	
	$err = BadWordsCont($annonce, 4);
	
	if ($err) {
		return $err;
	}
	
	if (check_filter($annonce)) {
		return $lang['err']['info_finding_1'];
	}
	
	$dbconn->Execute(
		'UPDATE '.USERS_TABLE.' SET comment = ? WHERE id = ? AND root_user = "0"',
		array($annonce, $id_user));
	
	return '';
}

//---------------------
// My Personality Form
//---------------------

function MyPersonalityForm()
{
	global $smarty, $dbconn, $user;
	
	$id = $user[ AUTH_ID_USER ];
	
	// hiddens
	$form['hiddens'] = '<input type="hidden" name="sel" value="save_4">';
	$form['hiddens'].= '<input type="hidden" name="e" value="1">';
	$form['hiddens'].= '<input type="hidden" name="id" value="'.$id.'">';
	
	// multi-lang tables
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	$rs = $dbconn->Execute('SELECT root_user, guest_user FROM '.USERS_TABLE.' WHERE id = ?', array($id));
	$row = $rs->GetRowAssoc(false);
	$data['root'] = $row['root_user']?$row['root_user']:$row['guest_user'];
	$smarty->assign('data', $data);
	
	$rs = $dbconn->Execute('SELECT id_spr, id_value FROM '.PERSON_SPR_USER_TABLE.' WHERE id_user = ?', array($id));
	while (!$rs->EOF) {
		$id_spr = $rs->fields[0];
		$id_value = $rs->fields[1];
		$sess_person[$id_spr] = array();
		$sess_person[$id_spr][count($sess_person[$id_spr])+1] = $id_value;
		$rs->MoveNext();
	}
	
	// personal selects
	$table_key = $multi_lang->TableKey(PERSON_SPR_TABLE);
	$table_key_val = $multi_lang->TableKey(PERSON_SPR_VALUE_TABLE);
	
	$strSQL =
		'SELECT DISTINCT a.id, b.'.$field_name.' AS name
		   FROM '.PERSON_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = "'.$table_key.'" AND b.id_reference = a.id';
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$person = array();
	while (!$rs->EOF) {
		$person[$i]['id'] = $rs->fields[0];
		$person[$i]['name'] = $rs->fields[1];
		$strSQL_opt =
			'SELECT DISTINCT a.id, b.'.$field_name.' AS name
			   FROM '.PERSON_SPR_VALUE_TABLE.' a
		  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = "'.$table_key_val.'" AND b.id_reference = a.id
			  WHERE a.id_spr = ?
		   ORDER BY name';
		$rs_opt = $dbconn->Execute($strSQL_opt, array($rs->fields[0]));
		$j = 0;
		while (!$rs_opt->EOF) {
			$person[$i]['opt'][$j]['value'] = $rs_opt->fields[0];
			$person[$i]['opt'][$j]['name'] = $rs_opt->fields[1];
			if (isset($sess_person[$rs->fields[0]]) && is_array($sess_person[$rs->fields[0]]) && in_array(0, $sess_person[$rs->fields[0]])){
				$person[$i]['sel_all'] = '1';
			} else {
				$person[$i]['opt'][$j]['sel'] = (isset($sess_person[$rs->fields[0]]) && is_array($sess_person[$rs->fields[0]]) && in_array($rs_opt->fields[0], $sess_person[$rs->fields[0]]))?$rs_opt->fields[0]:0;
			}
			$rs_opt->MoveNext(); $j++;
		}
		$rs->MoveNext(); $i++;
	}
	$smarty->assign('personal', $person);
	return $form;
}

function SaveMyPersonalityForm($profile_percent, $spr, $personal)
{
	global $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	if ($id_user && is_array($spr) && count($spr)) {
		$dbconn->Execute('DELETE FROM '.PERSON_SPR_USER_TABLE.' WHERE id_user = ?', array($id_user));
		for ($i = 0; $i < count($spr); $i++) {
			for ($j = 0; $j < count($personal[$i]); $j++) {
				$dbconn->Execute(
					'INSERT INTO '.PERSON_SPR_USER_TABLE.' SET id_user = ?, id_spr = ?, id_value = ?',
					 array($id_user, $spr[$i], $personal[$i][$j]));
			}
		}
	}
	
	$profile_percent->UpdateSection3Percent();
	return '';
}

//------------------
// My Portrait Form
//------------------

function MyPortraitForm()
{
	global $smarty, $dbconn, $user;
	
	$id = $user[ AUTH_ID_USER ];
	
	// hiddens
	$form['hiddens'] = '<input type="hidden" name="sel" value="save_5">';
	$form['hiddens'].= '<input type="hidden" name="e" value="1">';
	$form['hiddens'].= '<input type="hidden" name="id" value="'.$id.'">';
	
	// multi-lang tables
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	$rs = $dbconn->Execute('SELECT root_user, guest_user FROM '.USERS_TABLE.' WHERE id = ?', array($id));
	$row = $rs->GetRowAssoc(false);
	$data['root'] = $row['root_user'] ? $row['root_user'] : $row['guest_user'];
	$smarty->assign('data', $data);
	
	$rs = $dbconn->Execute('SELECT id_spr, id_value FROM '.PORTRAIT_SPR_USER_TABLE.' WHERE id_user = ?', array($id));
	while (!$rs->EOF) {
		$id_spr = $rs->fields[0];
		$id_value = $rs->fields[1];
		$sess_portrait[$id_spr] = array();
		$sess_portrait[$id_spr][count($sess_portrait[$id_spr])+1] = $id_value;
		$rs->MoveNext();
	}
	
	// portrait selects
	$table_key = $multi_lang->TableKey(PORTRAIT_SPR_TABLE);
	$table_key_val = $multi_lang->TableKey(PORTRAIT_SPR_VALUE_TABLE);
	
	$strSQL =
		'SELECT DISTINCT a.id, b.'.$field_name.' AS name
		   FROM '.PORTRAIT_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = "'.$table_key.'" AND b.id_reference = a.id';
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	while(!$rs->EOF){
		$portrait[$i]['id'] = $rs->fields[0];
		$portrait[$i]['name'] = $rs->fields[1];
		$portrait[$i]['num'] = $i;
		
		$strSQL_opt =
			'SELECT DISTINCT a.id, b.'.$field_name.' AS name
			   FROM '.PORTRAIT_SPR_VALUE_TABLE.' a
		  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = "'.$table_key_val.'" AND b.id_reference = a.id
			  WHERE a.id_spr = ?
		   ORDER BY name';
		
		$rs_opt = $dbconn->Execute($strSQL_opt, array($rs->fields[0]));
		$j = 0;
		$portrait = array();
		while (!$rs_opt->EOF) {
			$portrait[$i]['opt'][$j]['value'] = $rs_opt->fields[0];
			$portrait[$i]['opt'][$j]['name'] = $rs_opt->fields[1];
			if (isset($sess_portrait[$rs->fields[0]]) && is_array($sess_portrait[$rs->fields[0]]) && in_array(0, $sess_portrait[$rs->fields[0]])) {
				$portrait[$i]['sel_all'] = '1';
			} else {
				$portrait[$i]['opt'][$j]['sel'] = (isset($sess_portrait[$rs->fields[0]]) && is_array($sess_portrait[$rs->fields[0]]) && in_array($rs_opt->fields[0], $sess_portrait[$rs->fields[0]]))?$rs_opt->fields[0]:0;
			}
			$rs_opt->MoveNext();
			$j++;
		}
		$rs->MoveNext(); $i++;
	}
	$smarty->assign('portrait', $portrait);
	
	return $form;
}

function SaveMyPortraitForm($profile_percent, $spr, $portrait)
{
	global $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	if ($id_user && is_array($spr) && count($spr)) {
		$dbconn->Execute('DELETE FROM '.PORTRAIT_SPR_USER_TABLE.' WHERE id_user = ?', array($id_user));
		
		for ($i = 0; $i < count($spr); $i++) {
			for ($j = 0; $j < count($portrait[$i]); $j++) {
				$dbconn->Execute('INSERT INTO '.PORTRAIT_SPR_USER_TABLE.' SET id_user = ?, id_spr = ?, id_value = ?',
				array($id_user, $spr[$i], $portrait[$i][$j]));
			}
		}
	}
	
	$profile_percent->UpdateSection4Percent();
	return '';
}

//-------------------
// My Interests Form
//-------------------

function MyInterestsForm()
{
	global $smarty, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	// hiddens
	$form['hiddens'] = '<input type="hidden" name="sel" value="save_6">';
	$form['hiddens'].= '<input type="hidden" name="e" value="1">';
	$form['hiddens'].= '<input type="hidden" name="id" value="'.$id_user.'">';
	
	// multi-lang tables
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	$rs = $dbconn->Execute('SELECT root_user, guest_user FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
	$row = $rs->GetRowAssoc(false);
	$data['root'] = $row['root_user'] ? $row['root_user'] : $row['guest_user'];
	$smarty->assign('data', $data);
	
	// interests info from bd
	$rs = $dbconn->Execute('SELECT id_spr, id_value FROM '.INTERESTS_SPR_USER_TABLE.' WHERE id_user = ?', array($id_user));
	
	while (!$rs->EOF) {
		$id_spr = $rs->fields[0];
		$id_value = $rs->fields[1];
		$sess_interests[$id_spr] = $id_value;
		$rs->MoveNext();
	}
	
	// portrait selects
	$table_key = $multi_lang->TableKey(INTERESTS_SPR_TABLE);
	
	$strSQL =
		'SELECT DISTINCT a.id, b.'.$field_name.' AS name 
		   FROM '.INTERESTS_SPR_TABLE.' a 
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = '.$table_key.' AND b.id_reference = a.id
	   ORDER BY name';
	
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$interests = array();
	
	while (!$rs->EOF) {
		$interests[$i]['id'] = $rs->fields[0];
		$interests[$i]['name'] = $rs->fields[1];
		$interests[$i]['num'] = $i;
		$interests[$i]['sel'] = isset($sess_interests[$rs->fields[0]]) ? $sess_interests[$rs->fields[0]] : 0;
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('interests', $interests);
	return $form;
}

function SaveMyInterestsForm($profile_percent, $spr, $interests)
{
	global $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	if ($id_user && is_array($spr) && count($spr)) {
		$dbconn->Execute('DELETE FROM '.INTERESTS_SPR_USER_TABLE.' WHERE id_user = ?', array($id_user));
		for ($i = 0; $i < count($spr); $i++) {
			$id_spr = intval($spr[$i]);
			$id_value = isset($interests[$i]) ? intval($interests[$i]) : 0;
			$dbconn->Execute(
				'INSERT INTO '.INTERESTS_SPR_USER_TABLE.' SET id_user = ?, id_spr = ?, id_value = ?',
				array($id_user, $id_spr, $id_value));
		}
	}
	
	$profile_percent->UpdateSection5Percent();
	return '';
}

//------------------
// My Criteria Form
//------------------

function MyCriteriaForm()
{
	global $smarty, $dbconn, $user;
	
	$id = $user[ AUTH_ID_USER ];
	
	// hiddens
	$form['hiddens'] = '<input type="hidden" name="sel" value="save_7">';
	$form['hiddens'].= '<input type="hidden" name="e" value="1">';
	$form['hiddens'].= '<input type="hidden" name="id" value="'.$id.'">';
	
	// multi-lang tables
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	$rs = $dbconn->Execute('SELECT root_user, guest_user FROM '.USERS_TABLE.' WHERE id = ?', array($id));
	$row = $rs->GetRowAssoc(false);
	
	$data['root'] = $row['root_user'] ? $row['root_user'] : $row['guest_user'];
	
	$strSQL = 'SELECT id_country, id_nationality, id_language, id_height, id_weight from '.USER_MATCH_TABLE.' where id_user = ?';
	$rs = $dbconn->Execute($strSQL, array($id));
	$row = $rs->GetRowAssoc(false);
	
	$data['id_country'] = explode(',', $row['id_country']);		// array
	$data['id_nation'] = explode(',', $row['id_nationality']);		// array
	$data['id_lang'] = explode(',', $row['id_language']);		// array
	$data['id_weight'] = intval($row['id_weight']);
	$data['id_height'] = intval($row['id_height']);
	
	$smarty->assign('data', $data);
	
	$default = array();
	
	// country select
	$rs = $dbconn->Execute('SELECT DISTINCT id, name AS value FROM '.COUNTRY_SPR_TABLE.' ORDER BY name');
	
	$i = 0;
	$country_arr = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$country_arr[$i] = $row;
		$country_arr[$i]['sel'] = (is_array($data['id_country']) && in_array($row['id'], $data['id_country']) && !(in_array('0', $data['id_country'])) )?1:0;
		$rs->MoveNext();
		$i++;
	}
	
	if (in_array('0', $data['id_country'])) {
		$default['id_country'] = 1;
	}
	
	$smarty->assign('country_match', $country_arr);
	
	// nationality select
	$strSQL =
		'SELECT DISTINCT a.id, b.'.$field_name.' AS value
		   FROM '.NATION_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = "'.$multi_lang->TableKey(NATION_SPR_TABLE).'" AND b.id_reference = a.id
	   ORDER BY value';
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$n_arr = array();
	
	while(!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$n_arr[$i] = $row;
		$n_arr[$i]['sel'] = (is_array($data['id_nation']) && in_array($row['id'], $data['id_nation']) && !(in_array('0', $data['id_nation'])) )?1:0;
		$rs->MoveNext();
		$i++;
	}
	
	if (in_array('0', $data['id_nation'])) {
		$default['id_nation'] = 1;
	}
	
	$smarty->assign('nation_match', $n_arr);
	
	// language select
	$strSQL =
		'SELECT DISTINCT a.id, b.'.$field_name.' AS value
		   FROM '.LANGUAGE_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = "'.$multi_lang->TableKey(LANGUAGE_SPR_TABLE).'" AND b.id_reference = a.id
	   ORDER BY value';
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$lang_sel = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$lang_sel[$i] = $row;
		$lang_sel[$i]['sel'] = ( is_array($data['id_lang']) && in_array($row['id'], $data['id_lang']) && !(in_array('0', $data['id_lang'])) ) ? 1 : 0;
		$rs->MoveNext();
		$i++;
	}
	
	if (in_array('0', $data['id_lang'])) {
		$default['id_lang'] = 1;
	}
	
	$smarty->assign('lang_sel_match', $lang_sel);
	
	// weight select
	$strSQL =
		'SELECT DISTINCT a.id, b.'.$field_name.' AS value
		   FROM '.WEIGHT_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = "'.$multi_lang->TableKey(WEIGHT_SPR_TABLE).'" AND b.id_reference = a.id
	   ORDER BY a.sorter';
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$weight_arr = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$weight_arr[$i] = $row;
		$weight_arr[$i]['sel'] = (intval($data['id_weight']) == $row['id']) ? 1 : 0;
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('weight', $weight_arr);
	
	// height select
	$strSQL =
		'SELECT DISTINCT a.id, b.'.$field_name.' AS value
		   FROM '.HEIGHT_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = "'.$multi_lang->TableKey(HEIGHT_SPR_TABLE).'" AND b.id_reference = a.id
	   ORDER BY a.sorter ';
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$height_arr = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$height_arr[$i] = $row;
		$height_arr[$i]['sel'] = (intval($data['id_height']) == $row['id']) ? 1 : 0;
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('height', $height_arr);
	
	$rs = $dbconn->Execute('SELECT id_spr, id_value FROM '.DESCR_SPR_MATCH_TABLE.' WHERE id_user = ?', array($id));
	$sess_info = array();
	while (!$rs->EOF) {
		$id_spr = $rs->fields[0];
		$id_value = $rs->fields[1];
		if (!isset($sess_info[$id_spr])) {
			$sess_info[$id_spr] = array();
		}
		$sess_info[$id_spr][count($sess_info[$id_spr])+1] = $id_value;
		$rs->MoveNext();
	}
	
	// descr selects
	$strSQL =
		'SELECT DISTINCT a.id, b.'.$field_name.' AS name, a.type
		   FROM '.DESCR_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = "'.$multi_lang->TableKey(DESCR_SPR_TABLE).'" AND b.id_reference = a.id
	   ORDER BY a.sorter';
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$info = array();
	while (!$rs->EOF) {
		$info[$i]['id'] = $rs->fields[0];
		$info[$i]['name'] = $rs->fields[1];
		///// all selects is multiply
		$info[$i]['type'] = 2;
		$info[$i]['num'] = $i;
		$strSQL_opt =
			'SELECT DISTINCT a.id, b.'.$field_name.' AS name
			   FROM '.DESCR_SPR_VALUE_TABLE.' a
		  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = "'.$multi_lang->TableKey(DESCR_SPR_VALUE_TABLE).'" AND b.id_reference = a.id
			  WHERE a.id_spr = ?
		   ORDER BY name';
		$rs_opt = $dbconn->Execute($strSQL_opt, array($rs->fields[0]));
		$j = 0;
		while(!$rs_opt ->EOF){
			$info[$i]['opt'][$j]['value'] = $rs_opt->fields[0];
			$info[$i]['opt'][$j]['name'] = $rs_opt->fields[1];
			if(isset($sess_info[$rs->fields[0]]) && is_array($sess_info[$rs->fields[0]]) && in_array(0, $sess_info[$rs->fields[0]])){
				$info[$i]['sel_all'] = '1';
			}else{
				$info[$i]['opt'][$j]['sel'] = (isset($sess_info[$rs->fields[0]]) && is_array($sess_info[$rs->fields[0]]) && in_array($rs_opt->fields[0], $sess_info[$rs->fields[0]]))?$rs_opt->fields[0]:0;
			}
			$rs_opt->MoveNext(); $j++;
		}
		$rs->MoveNext();
		$i++;
	}
	$smarty->assign('info', $info);
	
	$smarty->assign('default', $default);
	return $form;
}

function SaveMyCriteriaForm($profile_percent, $data, $spr, $info)
{
	global $dbconn, $user;
	
	$id = $user[ AUTH_ID_USER ];
	
	$id_weight = isset($data['id_weight']) ? intval($data['id_weight']) : 0;
	$id_height = isset($data['id_height']) ? intval($data['id_height']) : 0;
	
	if (isset($data['id_country']) && is_array($data['id_country']) && count($data['id_country']) > 0) {
		$cr = $dbconn->Execute('SELECT COUNT(id) FROM '.COUNTRY_SPR_TABLE);
		$id_country = (count($data['id_country']) >= $cr->fields[0]) ? '0' : implode(',', $data['id_country']);
	} else {
		$id_country = '';
	}
	
	if (isset($data['id_nation']) && is_array($data['id_nation']) && count($data['id_nation']) > 0) {
		$cr = $dbconn->Execute('SELECT COUNT(id) FROM '.NATION_SPR_TABLE);
		$id_nation = (count($data['id_nation']) >= $cr->fields[0]) ? '0' : implode(',', $data['id_nation']);
	} else {
		$id_nation = '';
	}
	
	if (isset($data['id_lang']) && is_array($data['id_lang']) && count($data['id_lang']) > 0) {
		$cr = $dbconn->Execute('SELECT COUNT(id) FROM '.LANGUAGE_SPR_TABLE);
		$id_lang = (count($data['id_lang']) >= $cr->fields[0]) ? '0' : implode(',', $data['id_lang']);
	} else {
		$id_lang = '';
	}
	
	// save main info
	$dbconn->Execute(
		'UPDATE '.USER_MATCH_TABLE.' SET
				id_country = ?, id_nationality = ?, id_language = ?, id_weight = ?, id_height = ?
		  WHERE id_user = ?',
		array($id_country, $id_nation, $id_lang, $id_weight, $id_height, $id));
	
	// save personal info
	if (intval($id) && is_array($spr))
	{
		$dbconn->Execute('DELETE FROM '.DESCR_SPR_MATCH_TABLE.' WHERE id_user = ?', array($id));
		
		for ($i = 0; $i < count($spr); $i++)
		{
			$cr = $dbconn->Execute('SELECT COUNT(id) FROM '.DESCR_SPR_VALUE_TABLE.' WHERE id_spr = ?', array($spr[$i]));
			
			if (isset($info[$i]))
			{
				if (count($info[$i]) >= $cr->fields[0]) {
					$dbconn->Execute('INSERT INTO '.DESCR_SPR_MATCH_TABLE.' SET id_user = ?, id_spr = ?, id_value = "0"', array($id, $spr[$i]));
				} else {
					for ($j=0; $j<count($info[$i]); $j++) {
						$dbconn->Execute('INSERT INTO '.DESCR_SPR_MATCH_TABLE.' SET id_user = ?, id_spr = ?, id_value = ?',
							array($id, $spr[$i], $info[$i][$j]));
					}
				}
			}
		}
	}
	$profile_percent->UpdateSection6Percent();
	return '';
}

//--------------------
// His Interests Form
//--------------------

function HisInterestsForm()
{
	global $smarty, $dbconn, $user;
	
	$id = $user[ AUTH_ID_USER ];
	
	// hiddens
	$form['hiddens'] = '<input type="hidden" name="sel" value="save_8">';
	$form['hiddens'].= '<input type="hidden" name="e" value="1">';
	$form['hiddens'].= '<input type="hidden" name="id" value="'.$id.'">';
	
	// multi-lang tables
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	$rs = $dbconn->Execute('SELECT root_user, guest_user FROM '.USERS_TABLE.' WHERE id = ?', array($id));
	$row = $rs->GetRowAssoc(false);
	
	$data['root'] = $row['root_user'] ? $row['root_user'] : $row['guest_user'];
	$smarty->assign('data', $data);
	
	// interests info from db
	$rs = $dbconn->Execute('SELECT id_spr, id_value FROM '.INTERESTS_SPR_MATCH_TABLE.' WHERE id_user = ?', array($id));
	
	$sess_interests = array();
	
	while (!$rs->EOF) {
		$id_spr = $rs->fields[0];
		$id_value = $rs->fields[1];
		$sess_interests[$id_spr][$id_value] = 1;
		$rs->MoveNext();
	}
	
	// portrait selects
	$strSQL =
		'SELECT DISTINCT a.id, b.'.$field_name.' AS name
		   FROM '.INTERESTS_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = "'.$multi_lang->TableKey(INTERESTS_SPR_TABLE).'" AND b.id_reference = a.id
	   ORDER BY name';
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$interests = array();
	
	while (!$rs->EOF) {
		$interests[$i]['id'] = $rs->fields[0];
		$interests[$i]['name'] = $rs->fields[1];
		$interests[$i]['num'] = $i;
		$interests[$i]['sel_1'] = isset($sess_interests[$rs->fields[0]][1])?intval($sess_interests[$rs->fields[0]][1]):0;
		$interests[$i]['sel_2'] = isset($sess_interests[$rs->fields[0]][2])?intval($sess_interests[$rs->fields[0]][2]):0;
		$interests[$i]['sel_3'] = isset($sess_interests[$rs->fields[0]][3])?intval($sess_interests[$rs->fields[0]][3]):0;
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('interests', $interests);
	return $form;
}

function SaveHisInterestsForm($profile_percent, $spr, $interests)
{
	global $dbconn, $user, $profile_percent;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	if ($id_user && is_array($spr) && count($spr)) {
		$dbconn->Execute('DELETE FROM '.INTERESTS_SPR_MATCH_TABLE.' WHERE id_user = ?', array($id_user));
		for ($i = 0; $i < count($spr); $i++) {
			for ($j = 1; $j <= 3; $j++) {
				if (isset($interests[$i][$j]) && (int) $interests[$i][$j]) {
					$dbconn->Execute(
						'INSERT INTO '.INTERESTS_SPR_MATCH_TABLE.' SET id_user = ?, id_spr = ?, id_value = ?',
						array($id_user, $spr[$i], $j));
				}
			}
		}
	}
	
	$profile_percent->UpdateSection7Percent();
	return '';
}

//---------------
// Couple Action
//---------------

function CoupleAction($couple_user, $action)
{
	global $dbconn, $lang, $user;
	
	switch ($action)
	{
		case 'delete':
			$dbconn->Execute('UPDATE '.USERS_TABLE.' SET couple_user = "0" WHERE id = ?', array($user[ AUTH_ID_USER ]));
			$dbconn->Execute('UPDATE '.USERS_TABLE.' SET couple_user = "0" WHERE id = ?', array($couple_user));
		break;
		
		case 'accept':
			$rs = $dbconn->Execute('SELECT couple_user FROM '.USERS_TABLE.' WHERE id = ?', array($couple_user));
			if (!$rs->fields[0]) {
				return $lang['err']['couple_user_not_couple'];
			} elseif ($rs->fields[0] != $user[ AUTH_ID_USER ]) {
				return $lang['err']['couple_user_has_couple'];
			} else {
				$rs_couple = $dbconn->Execute('SELECT couple_user FROM '.USERS_TABLE.' WHERE id = ?', array($user[ AUTH_ID_USER ]));
				if ($rs_couple->fields[0]) {
					CoupleAction($rs_couple->fields[0], 'delete');
				}
				$dbconn->Execute('UPDATE '.USERS_TABLE.' SET couple = "1", couple_user = ? WHERE id = ?', array($couple_user, $user[ AUTH_ID_USER ]));
				$dbconn->Execute('UPDATE '.USERS_TABLE.' SET couple_user = ? WHERE id = ?', array($user[ AUTH_ID_USER ], $couple_user));
			}
			return $lang['users']['couple_accept_success'];
		break;
	}
	
	return;
}

//--------------
// Icon Upload and Delete
//--------------

function IconUpload($from)
{
	global $lang, $config, $smarty, $dbconn, $user, $IMG_EXT_ARRAY, $IMG_TYPE_ARRAY;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	// icon upload via registration.php is currently not in use on TLDF
	$file_name = ($from == 'registration') ? 'registration.php' : 'myprofile.php';
	
	$settings = GetSiteSettings(array('icon_max_width', 'icon_max_height', 'icon_max_size', 'icons_folder',
		'icon_male_default', 'icon_female_default', 'use_image_resize'));
	
	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];
	
	//RS: not in use. we could use it to allow more than one profile photo, so won't use album photos for this
	/*
	$rs = $dbconn->Execute(
		'SELECT id, upload_path, allow, user_comment
		   FROM '.USER_UPLOAD_TABLE.' 
		  WHERE id_user = ? AND upload_type = "f"
	   ORDER BY id',
		  array($id_user));
	
	$photos = array();
	$i = 0;
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$photos[$i]['id']			= $row['id'];
		$photos[$i]['file_path']	= $row['upload_path'];
		$photos[$i]['allow']		= $row['allow'];
		$photos[$i]['user_comment']	= stripslashes($row['user_comment']);
		$rs->MoveNext();
		$i++;
	}
	$data['photos'] = $photos;
	*/
	
	$data['max_file_size_string']	= $settings['icon_max_size'];
	$data['max_file_size_bytes']	= getFileSizeFromString($settings['icon_max_size']);
	$data['file_exts']				= '*.' . implode('; *.', $IMG_EXT_ARRAY);
	$data['file_types']				= implode('; ', $IMG_TYPE_ARRAY);
	
	if ($settings['use_image_resize']) {
		$data['icon_upload_comment'] = str_replace('[size]', $settings['icon_max_size'], $lang['confirm']['icon_upload_resize']);
	} else {
		$data['icon_upload_comment'] = str_replace('[size]', $settings['icon_max_size'], $lang['confirm']['icon_upload']);
		$data['icon_upload_comment'] = str_replace('[width]', $settings['icon_max_width'], $data['icon_upload_comment']);
		$data['icon_upload_comment'] = str_replace('[height]', $settings['icon_max_height'], $data['icon_upload_comment']);
	}
	
	$rs = $dbconn->Execute('SELECT big_icon_path, icon_path, icon_path_temp FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
	
	if (strlen($rs->fields[0])) {
		$file = $rs->fields[0];
	} elseif (strlen($rs->fields[1])) {
		$file = $rs->fields[1];
	} else {
		$file = $rs->fields[2];
	}
	
	$rs->Free();
	
	$path = $config['site_path'].$settings['icons_folder'].'/'.$file;
	
	if (strlen($file) && file_exists($path)) {
		$data['icon_del_link'] = $file_name.'?sel=delete_photo';
	} else {
		$file = $default_photos[$user[ AUTH_GENDER ]];
	}
	
	$data['icon_path'] = '.'.$settings['icons_folder'].'/'.$file;
	$data['gender'] = $user[ AUTH_GENDER ];
	
	$data['timestamp'] = time();
	$data['token'] = md5('unique_salt' . $data['timestamp']);
	
	$smarty->assign('data', $data);
	return;
}

function IconDelete($num)
{
	DeleteUploadedFiles('icon');
	EditProfile($num);
	return;
}

//--------------
// Multimedia File Upload
//--------------

// RS: completely replaced with methods from class.images.php
/*
function SaveUploadForm($upload, $upload_type='f', $upload_allow='', $id_file, $user_comment, $admin_mode=0, $id_user_admin_mode='', $id_album='', $is_gallary='', $id_gallery='')
{
	// only used in admin after refactoring of user front end
	
	global $smarty, $dbconn, $config, $config_admin, $lang, $user, $profile_percent, $config_index;
	global $IMG_TYPE_ARRAY, $IMG_EXT_ARRAY, $AUDIO_TYPE_ARRAY, $AUDIO_EXT_ARRAY;
	global $EMBEDDED_AUDIO_TYPE_ARRAY, $EMBEDDED_AUDIO_EXT_ARRAY, $VIDEO_TYPE_ARRAY, $VIDEO_EXT_ARRAY;
	
	#if (!isset($IMG_TYPE_ARRAY) ||  !isset($AUDIO_TYPE_ARRAY) || !isset($EMBEDDED_AUDIO_TYPE_ARRAY) || !isset($VIDEO_TYPE_ARRAY)) {
	#	require_once dirname(__FILE__).'/config_admin.php';
	#}
	
	$id_user = (int) $user[ AUTH_ID_USER ];
	
	if ($admin_mode == 1) {
		$id_user = $id_user_admin_mode;
	}
	
	// icon and photo upload use Images class
	if ($upload_type == 'icon') {
		$images_obj = new Images($dbconn);
		//$id_file = $upload_allow = '';
		//return $images_obj->UploadImages($upload, $id_user, $upload_type, $id_file, $user_comment, $upload_allow, $admin_mode);
		return $images_obj->UploadIcon('file_upload', $id_user);
	}
	
	if ($upload_type == 'f') {
		$images_obj = new Images($dbconn);
		//return $images_obj->UploadImages($upload, $id_user, $upload_type, $id_file, $user_comment, $upload_allow, $admin_mode, $id_album, $is_gallary, $id_gallery);
		return $images_obj->UploadPhoto('file_upload', $id_user);
	}
	
	$settings = GetSiteSettings(array('use_audio_approve', 'use_video_approve', 'audio_max_count', 'audio_max_size', 'audio_folder',
		'video_max_count', 'video_max_size', 'video_folder', 'use_embedded_audio', 'use_ffmpeg', 'path_to_ffmpeg', 
		'flv_output_dimension', 'flv_output_preset', 'flv_output_profile', 'flv_output_fps', 'flv_output_gop',
		'flv_output_video_bit_rate', 'flv_output_audio_sampling_rate', 'flv_output_audio_bit_rate', 'flv_output_foto_dimension',
		'flv_grab_photo_at_second'));
	
	switch ($upload_type)
	{
		case 'a':
			$folder			= $settings['audio_folder'];
			$type_array		= $settings['use_embedded_audio'] ? $EMBEDDED_AUDIO_TYPE_ARRAY : $AUDIO_TYPE_ARRAY;
			$ext_array		= $settings['use_embedded_audio'] ? $EMBEDDED_AUDIO_EXT_ARRAY : $AUDIO_EXT_ARRAY;
			$max_size		= getFileSizeFromString($settings['audio_max_size']);
			$err_type		= $lang['err']['invalid_audio_type'] . implode(', ', $type_array);
			$err_ext		= $lang['err']['invalid_audio_ext'] . implode(', ', $ext_array);
			$err_size		= str_replace('#SIZE#', $settings['audio_max_size'], $lang['err']['invalid_audio_size']);
			$use_approve	= $settings['use_audio_approve'];
		break;
		
		case 'v':
			$folder			= $settings['video_folder'];
			$type_array		= $VIDEO_TYPE_ARRAY;
			$ext_array		= $VIDEO_EXT_ARRAY;
			$max_size		= getFileSizeFromString($settings['video_max_size']);
			$err_type		= $lang['err']['invalid_video_type'] . implode(', ', $type_array);
			$err_ext		= $lang['err']['invalid_video_ext'] . implode(', ', $ext_array);
			$err_size		= str_replace('#SIZE#', $settings['video_max_size'], $lang['err']['invalid_video_size']);
			$use_approve	= $settings['use_video_approve'];
			#$use_ffmpeg	= $settings['use_ffmpeg'];
		break;
	}
	
	// no approval needed when admin uploads files
	if ($admin_mode == 1) {
		$use_approve = 0;
	}
	
	// first check $_FILES error
	switch ($upload['error']) {
		case UPLOAD_ERR_INI_SIZE:
			return str_replace('#MAX#', ini_get('upload_max_filesize'), $lang['err']['UPLOAD_ERR_INI_SIZE']);
		break;
		case UPLOAD_ERR_FORM_SIZE:
			$MAX_FILE_SIZE = number_format($_POST['MAX_FILE_SIZE'] / (1024 * 1024), 1) . ' MB';
			return str_replace('#MAX_FILE_SIZE#', $MAX_FILE_SIZE, $lang['err']['UPLOAD_ERR_FORM_SIZE']);
		break;
		case UPLOAD_ERR_PARTIAL:
			return $lang['err']['UPLOAD_ERR_PARTIAL'];
		break;
		case UPLOAD_ERR_NO_FILE:
			if ($id_file == '') {
				return $lang['err']['UPLOAD_ERR_NO_FILE'];
			}
		break;
		case UPLOAD_ERR_NO_TMP_DIR:
			return $lang['err']['UPLOAD_ERR_NO_TMP_DIR'];
		break;
		case UPLOAD_ERR_CANT_WRITE:
			return $lang['err']['UPLOAD_ERR_CANT_WRITE'];
		break;
		case UPLOAD_ERR_EXTENSION:
			return $lang['err']['UPLOAD_ERR_EXTENSION'];
		break;
	}
	
	if (!is_uploaded_file($upload['tmp_name'])) {
		return $lang['err']['upload_err'];
	}
	
	// for save mode restrict: move file to new temp folder
	$new_temp_path = GetTempUploadFile($upload['name']);
	if (move_uploaded_file($upload['tmp_name'], $new_temp_path)) {
		$upload['tmp_name'] = $new_temp_path;
	} else {
		return $lang['err']['move_uploaded_file'];
	}
	
	// get extension
	$filename_arr = explode('.', $upload['name']);
	$ext = array_pop($filename_arr);
	
	// check extension
	if (!in_array($ext, $ext_array)) {
		return str_replace('#EXT#', $ext, $err_ext);
	}
	
	// check mime type
	// flash uploads all files as application/octet-stream
	if (!isset($_GET['act']) || $_GET['act'] != 'flash') {
		if (!in_array($upload['type'], $type_array)) {
			return str_replace('#TYPE#', $upload['type'], $err_type);
		}
	}
	
	// check size
	if ($upload['size'] > $max_size) {
		return $err_size;
	}
	
	// move to target folder
	$new_file_name = GetNewFileName($upload['name'], $id_user);
	$new_file_path = $config['site_path'].$folder.'/'.$new_file_name;
	
	if (rename($upload['tmp_name'], $new_file_path))
	{
		@chmod($new_file_path, 0644); // SN uses 755 but 644 is sufficient
		
		// delete old upload and database record if any
		DeleteUploadedFiles($upload_type, $id_file, $admin_mode);
		
		if ($upload_type == 'v' && $settings['use_ffmpeg'] == 1)
		{
			$new_file_name_arr = explode('.', $new_file_name);
			
			// $flv_name = $new_file_name_arr[0].'.flv';
			// $flv_path = $config['site_path'].$folder.'/'.$flv_name;
			$mp4_temp_name = $new_file_name_arr[0].'-temp.mp4';
			$mp4_temp_path = $config['site_path'].$folder.'/'.$mp4_temp_name;
			$mp4_name = $new_file_name_arr[0].'-out.mp4';
			$mp4_path = $config['site_path'].$folder.'/'.$mp4_name;
			
			// rs: always convert to standardized video quality
			// convert video to target format
			// rs original SN   : @exec($settings['path_to_ffmpeg'].' -y -i '.$new_file_path.' -s '.$settings['flv_output_dimension'].' -acodec mp3 -ar '.$settings['flv_output_audio_sampling_rate'].' -ab '.$settings['flv_output_audio_bit_rate'].' '.$flv_path, $res);
			// rs customized flv: @exec($settings['path_to_ffmpeg'].' -y -i '.$new_file_path.' -s '.$settings['flv_output_dimension'].' -ar '.$settings['flv_output_audio_sampling_rate'].' -ab '.$settings['flv_output_audio_bit_rate'].'k '.$flv_path, $res);
			// rs h264 for ffmpeg 0.6.5: @exec($settings['path_to_ffmpeg'].' -y -i '.$new_file_path.' -s '.$settings['flv_output_dimension'].' -vcodec libx264 -vpre medium -vpre baseline -acodec libfaac -ar '.$settings['flv_output_audio_sampling_rate'].' '$mp4_path, $res);
			// rs webm:    ffmpeg -i "$SOURCE" -vpre libvpx_vp8-360p -b 1700k -an -pass 1 -f webm -threads 0 /dev/null
			// rs webm: && ffmpeg -i "$SOURCE" -vpre libvpx_vp8-360p -b 1700k -pass 2 -acodec libvorbis -ab 128k -ar 44100 -threads 0 "$TARGET"
			// rs: and now the current command for H.264 conversion
			$ffmpeg = $settings['path_to_ffmpeg'].' -y -i '.$new_file_path.' -strict experimental -acodec aac -ac 2';
			$ffmpeg.= ' -ar '.$settings['flv_output_audio_sampling_rate'];
			$ffmpeg.= ' -ab '.$settings['flv_output_audio_bit_rate'];
			$ffmpeg.= ' -vcodec libx264 -s '.$settings['flv_output_dimension'];
			$ffmpeg.= ' -preset:v '.$settings['flv_output_preset'];
			$ffmpeg.= ' -profile:v '.$settings['flv_output_profile'];
			$ffmpeg.= ' -r '.$settings['flv_output_fps'];
			$ffmpeg.= ' -g '.$settings['flv_output_gop'];
			$ffmpeg.= ' -b:v '.$settings['flv_output_video_bit_rate'];
			$ffmpeg.= ' '.$mp4_temp_path;
			@exec($ffmpeg, $res);
			
			// move MOOV atom to beginning of file
			@exec('/usr/local/bin/qt-faststart '.$mp4_temp_path.' '.$mp4_path, $res);
			@unlink($mp4_temp_path);
			
			// extract thumbnail(s)
			// rs old SN: @exec($settings['path_to_ffmpeg'].' -y -i '.$new_file_path.' -an -ss 00:00:00 -t 00:00:01 -r 1 -s '.$settings['flv_output_foto_dimension'].' '.$config['site_path'].$folder.'/'.$new_file_name_arr[0].'%d.jpg', $res);
			// rs alternative: 1 or 2 sec after start with itsoffset
			// ffmpeg -y -itsoffset -1 -i test.mpg -vcodec mjpeg -vframes 1 -an -f image2 -s 320x240 test.jpg
			// doc suggests: -f image2 but some tutorial prefer -f rawvideo
			$thumb_path = $config['site_path'].$folder.'/'.$new_file_name_arr[0].'1.jpg';
			@exec($settings['path_to_ffmpeg'].' -y -itsoffset -'.(int)$settings['flv_grab_photo_at_second'].' -i '.$new_file_path.' -vcodec mjpeg -vframes 1 -an -f image2 -s '.$settings['flv_output_foto_dimension'].' '.$thumb_path, $res);
		}
		
		// insert entry into db
		$status = empty($use_approve) ? '1' : '0';
		$is_gallary = empty($is_gallary) ? '0' : '1';
		
		$dbconn->Execute(
			'INSERT INTO '.USER_UPLOAD_TABLE.' SET
					id_user = ?, upload_path = ?, upload_type = ?, allow = ?, file_type = ?, status = ?,
					user_comment = ?, id_album = ?, is_gallary = ?, id_gallery = ?',
			array($id_user, $new_file_name, $upload_type, (string)$upload_allow, $upload['type'], (string)$status,
				$user_comment, $id_album, (string)$is_gallary, $id_gallery));
		
		return 'OK';
	}
	
	return  $lang['err']['upload_err'];
}
*/

 
function DeleteUploadedFiles($type_upload, $id_file='', $admin_mode='')
{
	global $dbconn, $config, $user;
	
	$id_user = (int) $user[ AUTH_ID_USER ];
	
	$settings = GetSiteSettings(array('icons_folder', 'photos_folder', 'audio_folder', 'video_folder'));
	
	switch ($type_upload)
	{
		case 'icon':
			$images_obj = new Images($dbconn);
			return $images_obj->DeleteUploadedFiles($type_upload, '', $id_user);
		break;
			
		case 'f':
			$images_obj = new Images($dbconn);
			return $images_obj->DeleteUploadedFiles($type_upload, $id_file, $id_user);
		break;
			
		case 'a':
			$folder = $settings['audio_folder'];
		break;
			
		case 'v':
			$folder = $settings['video_folder'];
		break;
			
		default:
			$folder = $settings['photos_folder'];
		break;
	}
	
	$id_album = '';
	
	if ($type_upload == 'a' || $type_upload == 'v')
	{
		if ($admin_mode != '') {
			$rs = $dbconn->Execute('SELECT upload_path, id_album FROM '.USER_UPLOAD_TABLE.' WHERE id = ?', array($id_file));
		} else {
			$rs = $dbconn->Execute('SELECT upload_path, id_album FROM '.USER_UPLOAD_TABLE.' WHERE id = ? AND id_user = ?', array($id_file, $id_user));
		}
		
		if (strlen($rs->fields[0]) > 0)
		{
			$id_album = $rs->fields[1];
			
			$old_file = $config['site_path'].$folder.'/'.$rs->fields[0];
			@unlink($old_file);
			
			// delete converted flv and mp4 files and jpg thumbs
			$old_file_name_arr = explode('.', $rs->fields[0]);
			@unlink($config['site_path'].$folder.'/'.$old_file_name_arr[0].'.flv');
			@unlink($config['site_path'].$folder.'/'.$old_file_name_arr[0].'-out.mp4');
			
			// jpg thumbs
			for ($i = 1; $i <= 9; $i++) {
				// the original ffmpeg command creates more than one thumb for some odd reason
				@unlink($config['site_path'].$folder.'/'.$old_file_name_arr[0].$i.'.jpg');
			}
			
			if ($admin_mode != '') {
				$dbconn->Execute('DELETE FROM '.USER_UPLOAD_TABLE.' WHERE id = ?', array($id_file));
			} else {
				$dbconn->Execute('DELETE FROM '.USER_UPLOAD_TABLE.' WHERE id = ? AND id_user = ?', array($id_file, $id_user));
			}
		}
	}
	
	return $id_album;
}

//---------------------- Select Functions ------------------------//

function GetNewFileName($name, $user_id)
{
	$ex_arr = explode('.', $name);
	$extension = $ex_arr[count($ex_arr)-1];
	$new_file_name = $user_id.'_'.substr(md5(microtime().getmypid()), 0, 8).'.'.$extension;
	return $new_file_name;
}

//---------------------- /Select Functions ------------------------//

function GetPerfectUsersList($user_id, $after_date=0, $limit=0)
{
	global $dbconn;
	
	// get our match requirements from user_match_table (basic)
	$strSQL =
		'SELECT gender, couple, age_min, age_max, id_weight, id_height,
				id_language, id_country, id_nationality, id_relationship
		   FROM '.USER_MATCH_TABLE.'
		  WHERE id_user = ?';
	$rs = $dbconn->Execute($strSQL, array($user_id));
	$row = $rs->GetRowAssoc(false);
	
	$gender = $row['gender'];
	$couple = $row['couple'];
	$age_min = $row['age_min'];
	$age_max = $row['age_max'];
	$id_weight = $row['id_weight'];
	$id_height = $row['id_height'];
	$id_lang_str = $row['id_language'];
	$id_country_str = $row['id_country'];
	$id_nation_str = $row['id_nationality'];
	$id_relation_str = $row['id_relationship'];

	$id_lang = explode(',', $row['id_language']);
	$id_country = explode(',', $row['id_country']);
	$id_nation = explode(',', $row['id_nationality']);
	$id_relation = explode(',', $row['id_relationship']);

	$rs = $dbconn->Execute('SELECT gender, couple FROM '.USERS_TABLE.' WHERE id = ?', array($user_id));
	$row = $rs->GetRowAssoc(false);
	
	$gender_2 = $row['gender']; // self gender
	$couple_2 = $row['couple']; // self single or couple
	
	// get match requirements from description table
	$strSQL =
		'SELECT a.id_spr, a.id_value, b.type
		   FROM '.DESCR_SPR_MATCH_TABLE.' a
	  LEFT JOIN '.DESCR_SPR_TABLE.' b ON b.id = a.id_spr
		  WHERE a.id_user = ?';
	$rs = $dbconn->Execute($strSQL, array($user_id));
	
	$i = 0;
	$info = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$id_spr = $row['id_spr'];
		$id_value = $row['id_value'];
		$info_type[$id_spr] = $row['type'];
		$info[$id_spr][] = $id_value;
		$rs->MoveNext();
	}
	
	// try to find what interests user like
	$rs = $dbconn->Execute('SELECT id_spr, id_value FROM '.INTERESTS_SPR_MATCH_TABLE.' WHERE id_user = ?', array($user_id));
	
	$i = 0;
	$interests = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$id_spr = $row['id_spr'];
		$id_value = $row['id_value'];
		$interests[$id_spr][] = $id_value;
		$rs->MoveNext();
	}
	
	// select users matches for main parametrs
	$where = array();
	$rank = 0;
	
	$select_clause = 'SELECT a.id';
	$from_clause = ' FROM '.USERS_TABLE.' a, '.USER_MATCH_TABLE.' um ';
	
	if (strlen($id_country_str) > 0 && count($id_country) > 0 && strval($id_country_str) != '0') {
		$where[] = ' a.id_country IN ('.$id_country_str.')';
		$rank++;
	}
	if (strlen($id_nation_str) > 0 && count($id_nation) > 0 && strval($id_nation_str) != '0') {
		$where[] = ' a.id_nationality IN ('.$id_nation_str.')';
		$rank++;
	}
	if (strlen($id_lang_str) > 0 && count($id_lang) > 0 && strval($id_lang_str) != '0') {
		$where[] = ' ( a.id_language_1 IN ('.$id_lang_str.') OR a.id_language_2 IN ('.$id_lang_str.') OR a.id_language_3 IN ('.$id_lang_str.') ) ';
		$rank++;
	}
	if (strlen($id_relation_str) > 0 && count($id_relation) > 0 && strval($id_relation_str) != '0') {
		$where[] = '  um.id_relationship IN ('.$id_relation_str.') ';
		$rank++;
	}
	if (intval($gender)) {
		$where[] = ' a.gender="'.$gender.'"';
		$rank++;
	}
	if (isset($couple)) {
		$where[] = ' a.couple="'.$couple.'"';
	}
	if (intval($id_weight)) {
		$where[] = ' a.id_weight="'.$id_weight.'"';
		$rank++;
	}
	if (intval($id_height)) {
		$where[] = ' a.id_height="'.$id_height.'"';
		$rank++;
	}
	if (intval($age_min)) {
		$where[] = ' STRCMP(date_format(a.date_birthday, "%Y%m%d"), date_format("'.DateFromAge($age_min-1).'", "%Y%m%d")) <= 0';
	}
	if (intval($age_max)) {
		$where[] = ' STRCMP(date_format(a.date_birthday, "%Y%m%d"), date_format("'.DateFromAge($age_max+1).'", "%Y%m%d")) >= 0';
	}
	if (intval($age_min) || intval($age_max)) {
		$rank++;
	}
	$where[] = ' a.root_user = "0"';			// not admin
	$where[] = ' a.guest_user = "0"';			// not guest
	$where[] = ' a.id != "'.$user_id.'"';		// not self
	$where[] = ' a.status = "1"';				// active user
	$where[] = ' a.visible = "1"';				// visible user
	$where[] = ' um.id_user = a.id';			//
	$where[] = ' um.gender = "'.$gender_2.'"';	// perfect match for person who we search is the same gender as we
	$where[] = ' um.couple = "'.$couple_2.'"';	// for couple or single
	
	if ($after_date) {
		$where[] = ' UNIX_TIMESTAMP(a.date_registration) > "'.$after_date.'"';
	}
	
	$where_clause = implode(' AND', $where);
	
	if (strlen($where_clause) > 0) {
		$where_clause = ' WHERE '.$where_clause;
	}
	
	$order_clause = ' ORDER BY a.date_registration DESC';
	
	if ($limit > 0) {
		$limit_str = ' LIMIT '.$limit;
		$order_clause = ' ORDER BY RAND()';
	} else {
		$limit_str = '';
	}
	
	$strSQL = $select_clause.$from_clause.$where_clause.$order_clause.$limit_str;
	
	$rs = $dbconn->Execute($strSQL);
	
	$temp_users_arr = array();
	$main_percent_arr = array();
	$required_id = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$required_id[] = $row['id'];
		$temp_users_arr[$row['id']] = $rank;
		$main_percent_arr[$row['id']] = $rank;
		$rs->MoveNext();
	}
	
	// rank on this step  ~ 50% concurrence

	if (is_array($required_id) && count($required_id) > 0) {
		$required_id_str = implode(', ', $required_id);
		/// update existing users ranks, add new users by description options
		$rank_descr = 0;
		$descr_percent_arr = array();
		if (is_array($info) && count($info) > 0) {
			foreach ($info as $key => $value) {
				$rank_descr++;
				// $value - array , '0' - all
				if (is_array($value) && !in_array('0', $value)) {
					// 0 - mean that doesnt matter for user what another user mark in this field
					// if select is multiply ($info_type[$key] == 2)  then mark '0' = 'all fields is suite for me' and such user is suite for us
					// if $info_type[$key] == 1 (select is not multiply) then '0' - mean 'nothing marked'
					$value_str = implode(',', $value);
					if (strlen(trim($value_str)) > 0) {
						$strSQL =
							'SELECT id_user
							   FROM '.DESCR_SPR_USER_TABLE.'
							  WHERE id_spr = ? AND id_value IN ('.$value_str.''.($info_type[$key] == 2 ? ', "0"' : '').')
							    AND id_user IN ('.$required_id_str.')';
						$rs = $dbconn->Execute($strSQL, array($key));
						while (!$rs->EOF) {
							$row = $rs->GetRowAssoc(false);
							$temp_users_arr[$row['id_user']] = intval($temp_users_arr[$row['id_user']])+1;
							$descr_percent_arr[$row['id_user']] = $rank_descr;
							$rs->MoveNext();
						}
					}
				} else {
					foreach ($temp_users_arr as $temp_id => $temp_rank) {
						$temp_users_arr[$temp_id] = $temp_rank+1;
						$descr_percent_arr[$temp_id] = (isset($descr_percent_arr[$temp_id])?$descr_percent_arr[$temp_id]:0)+1;
					}
				}
			}
		}
		// update existing users ranks, add new users by interests options
		$rank_int = 0;
		$int_percent_arr = array();
		if (is_array($interests) && count($interests) > 0) {
			foreach ($interests as $key => $value) {
				$rank_int++;
				// $value - scalar value - or empty or in (1,2,3); if empty - not include in query
				if (is_array($value) && !in_array('0', $value)) {
					$value_str = implode(',', $value);
					if (strlen(trim($value_str)) > 0) {
						$strSQL =
							'SELECT id_user
							   FROM '.INTERESTS_SPR_USER_TABLE.'
							  WHERE id_spr = ? AND id_value IN ('.$value_str.') AND id_user IN ('.$required_id_str.')';
						$rs = $dbconn->Execute($strSQL, array($key));
						while (!$rs->EOF) {
							$row = $rs->GetRowAssoc(false);
							$temp_users_arr[$row['id_user']] = intval($temp_users_arr[$row['id_user']])+1;
							$int_percent_arr[$row['id_user']] = $rank_int;
							$rs->MoveNext();
						}
					}
				} else {
					foreach ($temp_users_arr as $temp_id => $temp_rank) {
						$temp_users_arr[$temp_id] = $temp_rank+1;
						$int_percent_arr[$temp_id] = (isset($int_percent_arr[$temp_id])?$int_percent_arr[$temp_id]:0)+1;
					}
				}
			}
		}
	}
	$i = 0;
	$users = array();
	if (is_array($required_id) && count($required_id) > 0) {
		foreach ($required_id  as $temp_id) {
			$rank_percent = 0;
			if ($rank) {
				$rank_percent = $rank_percent + 0.5*($main_percent_arr[$temp_id]/$rank);
			}
			if ($rank_descr) {
				$rank_percent = $rank_percent + 0.3*((isset($descr_percent_arr[$temp_id])?$descr_percent_arr[$temp_id]:0)/$rank_descr);
			}
			if ($rank_int) {
				$rank_percent = $rank_percent + 0.2*((isset($int_percent_arr[$temp_id])?$int_percent_arr[$temp_id]:0)/$rank_int);
			}
			$rank_percent = round($rank_percent*100);
			if (intval($rank_percent) >= 50) {
				$users['id_arr'][$i] = $temp_id;
				$users['percent_arr'][$temp_id] = $rank_percent;
				$i++;
			}
		}
	}
	
	return $users;
}


function GetWantToMeetThemList($user_id, $only_full_concurr=false)
{
	global $dbconn;

	// get our match requirements from user_match_table (basic)
	$strSQL =
		'SELECT gender, couple, age_min, age_max, id_country, id_nationality, id_language, id_weight, id_height, id_relationship
		   FROM '.USER_MATCH_TABLE.'
		  WHERE id_user = ?';
	$rs = $dbconn->Execute($strSQL, array($user_id));
	$row = $rs->GetRowAssoc(false);
	$gender = $row['gender'];
	$couple = $row['couple'];
	$age_min = $row['age_min'];
	$age_max = $row['age_max'];
	$id_weight = $row['id_weight'];
	$id_height = $row['id_height'];

	$id_lang_str = $row['id_language'];
	$id_country_str = $row['id_country'];
	$id_nation_str = $row['id_nationality'];
	$id_relation_str = $row['id_relationship'];

	$id_lang = explode(',', $row['id_language']);
	$id_country = explode(',', $row['id_country']);
	$id_nation = explode(',', $row['id_nationality']);
	$id_relation = explode(',', $row['id_relationship']);
	
	$rs = $dbconn->Execute('SELECT gender, couple FROM '.USERS_TABLE.' WHERE id = ?', array($user_id));
	$row = $rs->GetRowAssoc(false);
	
	$gender_2 = $row['gender']; // self gender
	$couple_2 = $row['couple']; // self couple or single
	
	// get match requirements from description table
	$strSQL =
		'SELECT a.id_spr, a.id_value, b.type
		   FROM '.DESCR_SPR_MATCH_TABLE.' a
	  LEFT JOIN '.DESCR_SPR_TABLE.' b ON b.id = a.id_spr
		  WHERE a.id_user = ?';
	$rs = $dbconn->Execute($strSQL, array($user_id));
	
	$i = 0;
	$info = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$id_spr = $row['id_spr'];
		$id_value = $row['id_value'];
		$info_type[$id_spr] = $row['type'];
		$info[$id_spr][] = $id_value;
		$rs->MoveNext();
	}
	
	// select users matches for main parametrs
	$where = array();
	$rank = 0;

	$select_clause = 'SELECT a.id';
	$from_clause = ' FROM '.USERS_TABLE.' a, '.USER_MATCH_TABLE.' um ';
	
	if(strlen($id_country_str)>0 && count($id_country)>0 && strval($id_country_str)!='0'){
		array_push($where, ' a.id_country IN ('.$id_country_str.')');
		$rank++;
	}
	if(strlen($id_nation_str)>0 && count($id_nation)>0 && strval($id_nation_str)!='0'){
		array_push($where, ' a.id_nationality IN ('.$id_nation_str.')');
		$rank++;
	}
	if(strlen($id_lang_str)>0 && count($id_lang)>0 && strval($id_lang_str)!='0'){
		array_push($where, ' ( a.id_language_1 IN ('.$id_lang_str.') OR a.id_language_2 IN ('.$id_lang_str.') OR a.id_language_3 IN ('.$id_lang_str.') ) ');
		$rank++;
	}
	if(strlen($id_relation_str)>0 && count($id_relation)>0 && strval($id_relation_str)!='0'){
		array_push($where, '  um.id_relationship IN ('.$id_relation_str.') ');
		$rank++;
	}
	if(intval($gender)){
		array_push($where, ' a.gender="'.$gender.'"');
		$rank++;
	}
	if(isset($couple)){
		array_push($where, ' a.couple="'.$couple.'"');
	}
	if(intval($id_weight)){
		array_push($where, ' a.id_weight="'.$id_weight.'"');
		$rank++;
	}
	if(intval($id_height)){
		array_push($where, ' a.id_height="'.$id_height.'"');
		$rank++;
	}
	if(intval($age_min)){
		array_push($where, ' STRCMP(date_format(a.date_birthday, "%Y%m%d"), date_format("'.DateFromAge($age_min-1).'", "%Y%m%d")) <=0');
	}
	if (intval($age_max)) {
		array_push($where, ' STRCMP(date_format(a.date_birthday, "%Y%m%d"), date_format("'.DateFromAge($age_max+1).'", "%Y%m%d")) >= 0');
	}
	if (intval($age_min) || intval($age_max)) {
		$rank++;
	}
	array_push($where, ' a.root_user = "0"');		//// not admin
	array_push($where, ' a.guest_user = "0"');		//// not guest
	array_push($where, ' a.id != "'.$user_id.'"');		//// not self
	array_push($where, ' a.status="1"');			//// active user
	array_push($where, ' a.visible="1"');			//// visible user
	array_push($where, ' um.id_user=a.id');			////
	array_push($where, ' um.gender="'.$gender_2.'"');	//// perfect match for person who we search is the same gender as we
	array_push($where, ' um.couple="'.$couple_2.'"');	//// for couple or single

	$where_clause = implode(' AND', $where);
	if (strlen($where_clause) > 0) {
		$where_clause = ' WHERE '.$where_clause;
	}

	$order_clause = ' ORDER BY a.date_registration DESC';

	$strSQL = $select_clause.$from_clause.$where_clause.$order_clause;
	$rs = $dbconn->Execute($strSQL);
	
	$temp_users_arr = array();
	$main_percent_arr = array();
	$required_id = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$required_id[] = $row['id'];
		$temp_users_arr[$row['id']] = $rank;
		$main_percent_arr[$row['id']] = $rank;
		$rs->MoveNext();
	}
	
	// rank on this step  ~ 50% concurrence
	
	if (is_array($required_id) && count($required_id) > 0) {
		$required_id_str = implode(', ', $required_id);
		/// update existing users ranks, add new users by description options
		$rank_descr = 0;
		$descr_percent_arr = array();
		if (is_array($info) && count($info) > 0) {
			foreach ($info as $key => $value) {
				$rank_descr++;
				///// $value - array , '0' - all
				if (is_array($value) && !in_array('0', $value)) {
					/////// 0 - mean that doesnt matter for user what another user mark in this field
					/////// if select is multiply ($info_type[$key] == 2)  then mark '0' = 'all fields is suite for me' and such user is suite for us
					/////// if $info_type[$key] == 1 (select is not multiply) then '0' - mean 'nothing marked'
					$value_str = implode(',', $value);
					if (strlen(trim($value_str)) > 0) {
						$strSQL =
							'SELECT id_user
							   FROM '.DESCR_SPR_USER_TABLE.'
							  WHERE id_spr = ? AND id_value IN ('.$value_str.''.($info_type[$key] == 2 ? ',"0"' : '').')
							    AND id_user IN ('.$required_id_str.')';
						$rs = $dbconn->Execute($strSQL, array($key));
						while (!$rs->EOF) {
							$row = $rs->GetRowAssoc(false);
							$temp_users_arr[$row['id_user']] = intval($temp_users_arr[$row['id_user']]) + 1;
							$descr_percent_arr[$row['id_user']] = $rank_descr;
							$rs->MoveNext();
						}
					}
				} else {
					foreach ($temp_users_arr as $temp_id => $temp_rank) {
						$temp_users_arr[$temp_id] = $temp_rank + 1;
						$descr_percent_arr[$temp_id] = (isset($descr_percent_arr[$temp_id])?$descr_percent_arr[$temp_id]:0)+1;
					}
				}
			}
		}
	}
	$i = 0;
	$users = array();
	$profile_limit = $only_full_concurr?100:50;
	if (is_array($required_id) && count($required_id) > 0) {
		foreach ($required_id as $temp_id) {
			$rank_percent = 0;
			if ($rank) {
				$rank_percent = $rank_percent + 0.7*($main_percent_arr[$temp_id]/$rank);
			}
			if ($rank_descr) {
				$rank_percent = $rank_percent + 0.3*((isset($descr_percent_arr[$temp_id])?$descr_percent_arr[$temp_id]:0)/$rank_descr);
			}
			$rank_percent = round($rank_percent*100);
			if (intval($rank_percent) >= $profile_limit) {
				$users['id_arr'][$i] = $temp_id;
				$users['percent_arr'][$temp_id] = $rank_percent;
				$i++;
			}
		}
	}

	$users_data = $users;
	return $users_data;

}


function GetWantToMeetMeList($user_id, $only_full_concurr=false)
{
	global $dbconn;

	// get our match requirements from user_match_table (basic)
	$strSQL =
		'SELECT gender, couple, date_birthday, id_country, id_nationality,
				id_language_1, id_language_2, id_language_3, id_weight, id_height
		   FROM '.USERS_TABLE.'
		  WHERE id = ?';
	$rs = $dbconn->Execute($strSQL, array($user_id));
	$row = $rs->GetRowAssoc(false);
	
	$id_lang_1 = $row['id_language_1'];
	$id_lang_2 = $row['id_language_2'];
	$id_lang_3 = $row['id_language_3'];
	$id_country = $row['id_country'];
	$id_nation = $row['id_nationality'];
	$gender = $row['gender'];
	$couple = $row['couple'];
	$age = AgeFromBDate($row['date_birthday']);
	$id_weight = $row['id_weight'];
	$id_height = $row['id_height'];
	
	$rs = $dbconn->Execute(
		'SELECT gender, couple, id_relationship, age_min, age_max
		   FROM '.USER_MATCH_TABLE.'
		  WHERE id_user = ?',
		array($user_id));
	$row = $rs->GetRowAssoc(false);
	
	$gender_2	= $row['gender'];				/// self gender
	$couple_2	= $row['couple'];				/// self couple or single
	$age_min	= (int) $row['age_min'];
	$age_max	= (int) $row['age_min'];
	
	// not ised
	## $id_relation_str = $row['id_relationship'];
	## $id_relation = explode(',', $row['id_relationship']);	/// relationships
	
	$strSQL =
		'SELECT a.id_spr, a.id_value, b.type
		   FROM '.DESCR_SPR_USER_TABLE.' a
	  LEFT JOIN '.DESCR_SPR_TABLE.' b ON b.id = a.id_spr
		  WHERE a.id_user = ?';
	$rs = $dbconn->Execute($strSQL, array($user_id));
	
	$i = 0;
	$info = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$id_spr = $row['id_spr'];
		$id_value = $row['id_value'];
		$info_type[$id_spr] = $row['type'];
		$info[$id_spr][] = $id_value;
		$rs->MoveNext();
	}

	// select users matches for main parametrs
	$where = array();
	$rank = 0;

	$select_clause = 'SELECT a.id_user';
	$from_clause = ' FROM '.USER_MATCH_TABLE.' a, '.USERS_TABLE.' c ';
	
	if (isset($id_country) && intval($id_country)>0){
		$country_str = ','.$id_country.',';
		$where[] = ' ( CONCAT(",", a.id_country,",") LIKE "%'.$country_str.'%" OR CONCAT(",", a.id_country,",") LIKE "%,0,%" )';
		$rank++;
	}
	
	if (isset($id_nation) && intval($id_nation) > 0) {
		$nation_str = ','.$id_nation.',';
		$where[] = ' ( CONCAT(",", a.id_nationality,",") LIKE "%'.$nation_str.'%" OR CONCAT(",", a.id_nationality,",") LIKE "%,0,%")';
		$rank++;
	}
	
	if (intval($id_lang_1) || intval($id_lang_2) || intval($id_lang_3)) {
		$id_lang_1_str = intval($id_lang_1) ? ' CONCAT(",", a.id_language,",") LIKE "%'.$id_lang_1.'%" OR' : '';
		$id_lang_2_str = intval($id_lang_2) ? ' CONCAT(",", a.id_language,",") LIKE "%'.$id_lang_2.'%" OR' : '';
		$id_lang_3_str = intval($id_lang_3) ? ' CONCAT(",", a.id_language,",") LIKE "%'.$id_lang_3.'%" OR' : '';
		$where[] = ' ('.$id_lang_1_str.' '.$id_lang_2_str.' '.$id_lang_3_str.' CONCAT(",", a.id_language,",") LIKE "%,0,%")';
		$rank++;
	}
	
	if (isset($gender) && intval($gender)) {
		$where[] = ' a.gender="'.$gender.'"';
		$rank++;
	}
	
	if (isset($couple)) {
		$where[] = ' a.couple="'.$couple.'"';
	}
	
	if (isset($id_weight) && intval($id_weight)) {
		$where[] = ' a.id_weight in ('.$id_weight.', 0)';
		$rank++;
	}
	
	if (isset($id_height) && intval($id_height)) {
		$where[] = ' a.id_height in ('.$id_height.', 0)';
		$rank++;
	}
	
	if (isset($age_max) && intval($age_max)) {
		array_push($where, ' a.age_max >"'.$age.'" ');
		$rank++;
	}
	
	if (isset($age_min) && intval($age_min)) {
		array_push($where, ' a.age_min <"'.$age.'" ');
		$rank++;
	}

	$where[] = ' c.root_user = "0"';			// not admin
	$where[] = ' c.guest_user = "0"';			// not guest
	$where[] = ' c.id != "'.$user_id.'"';		// not self
	$where[] = ' c.status = "1"';				// active user
	$where[] = ' c.visible = "1"';				// visible user
	$where[] = ' c.id = a.id_user';				//
	$where[] = ' c.gender = "'.$gender_2.'"';	// perfect match for person who we search is the same gender as we
	$where[] = ' c.couple = "'.$couple_2.'"';	// for couple or single
	
	$where_clause = implode(' AND', $where);
	
	if (strlen($where_clause) > 0) {
		$where_clause = ' WHERE '.$where_clause;
	}
	
	$order_clause = ' ORDER BY c.date_registration DESC';
	
	$strSQL = $select_clause.$from_clause.$where_clause.$order_clause;
	$rs = $dbconn->Execute($strSQL);
	
	$temp_users_arr = array();
	$main_percent_arr = array();
	$required_id = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$required_id[] = $row['id_user'];
		$temp_users_arr[$row['id_user']] = $rank;
		$main_percent_arr[$row['id_user']] = $rank;
		$rs->MoveNext();
	}
	
	// rank on this step  ~ 50% concurrence
	
	if (is_array($required_id) && count($required_id) > 0) {
		$required_id_str = implode(', ', $required_id);
		// update existing users ranks, add new users by description options
		$rank_descr = 0;
		$descr_percent_arr = array();
		if (is_array($info) && count($info) > 0) {
			foreach ($info as $key => $value) {
				$rank_descr++;
				// $value - array , '0' - all
				if (is_array($value) && !in_array('0', $value)) {
					// 0 - mean that doesnt matter for user what another user mark in this field
					// if select is multiply ($info_type[$key] == 2)  then mark '0' = 'all fields is suite for me' and such user is suite for us
					// if $info_type[$key] == 1 (select is not multiply) then '0' - mean 'nothing marked'
					$value_str = implode(',', $value);
					if (strlen(trim($value_str)) > 0) {
						$strSQL =
							'SELECT id_user
							   FROM '.DESCR_SPR_MATCH_TABLE.'
							  WHERE id_spr = ? AND id_value IN ('.$value_str.''.($info_type[$key] == 2 ? ',"0"' : '').')
							    AND id_user IN ('.$required_id_str.')';
						$rs = $dbconn->Execute($strSQL, array($key));
						while (!$rs->EOF) {
							$row = $rs->GetRowAssoc(false);
							$temp_users_arr[$row['id_user']] = intval($temp_users_arr[$row['id_user']])+1;
							$descr_percent_arr[$row['id_user']] = $rank_descr;
							$rs->MoveNext();
						}
					}
				} else {
					foreach ($temp_users_arr as $temp_id=>$temp_rank) {
						$temp_users_arr[$temp_id] = $temp_rank+1;
						$descr_percent_arr[$temp_id] = (isset($descr_percent_arr[$temp_id])?$descr_percent_arr[$temp_id]:0)+1;
					}
				}
			}
		}
	}
	
	$i = 0;
	$users = array();
	
	$profile_limit = $only_full_concurr ? 100 : 50;
	
	if (is_array($required_id) && count($required_id) > 0) {
		foreach ($required_id as $temp_id) {
			$rank_percent = 0;
			if ($rank) {
				$rank_percent = $rank_percent + 0.7*($main_percent_arr[$temp_id]/$rank);
			}
			if ($rank_descr) {
				$rank_percent = $rank_percent + 0.3*((isset($descr_percent_arr[$temp_id])?$descr_percent_arr[$temp_id]:0)/$rank_descr);
			}
			$rank_percent = round($rank_percent * 100);
			if (intval($rank_percent) >= $profile_limit) {
				$users['id_arr'][$i] = $temp_id;
				$users['percent_arr'][$temp_id] = $rank_percent;
				$i++;
			}
		}
	}

	$users_data = $users;
	return $users_data;
}


function GetUserRatingPlace($user_id)
{
	global $dbconn;
	
	$rs = $dbconn->Execute('SELECT id FROM '.USER_TOPTEN_TABLE.' WHERE id_user = ?', array($user_id));
	
	if ($rs->fields[0] > 0) {
		$id = $rs->fields[0];
		
		$rs = $dbconn->Execute('SELECT DISTINCT id FROM '.USER_TOPTEN_TABLE.' GROUP BY id_user ORDER BY rating DESC');
		
		$i = 1;
		
		while (!$rs->EOF) {
			if ($rs->fields[0] == $id) {
				return $i;
				break;
			}
			$i++;
			$rs->MoveNext();
		}
		return null;
	}
	else
	{
		return null;
	}
}

// VP function to check the status of Registration steps completion
// check icon photo
//
function isPhotoUploaded($id_user)
{
	global $dbconn;
	$rs = $dbconn->Execute('SELECT icon_path FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
	return !empty($rs->fields[0]);
}


// check email confirmation status
//
function isEmailConfirmed($id_user)
{
	global $dbconn;
	$rs = $dbconn->Execute('SELECT confirm FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
	return ($rs->fields[0] == '1');
}

// checking profile data status
//
function isProfileCompleted($id_user)
{
	global $dbconn;
	
	$rs = $dbconn->Execute('SELECT * FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
	$data = $rs->GetRowAssoc(false);
	
	$mandatory = array();
	
	include './customize/profile_switchboard.php';
	
	if (!strlen($data['login'])) {
		return false;
	}
	
	if ($mandatory['fname'] & SB_EDIT && !strlen($data['fname'])) {
		return false;
	}
	
	if ($mandatory['sname'] & SB_EDIT && !strlen($data['sname'])) {
		return false;
	}
	
	if ($mandatory['mm_nickname'] & SB_EDIT && !strlen($data['mm_nickname'])) {
		return false;
	}
	
	if ($mandatory['gender'] & SB_EDIT && !$data['gender']) {
		return false;
	}
	
	if ($mandatory['mm_marital_status'] & SB_EDIT && !$data['mm_marital_status']) {
		return false;
	}
	
	if ($mandatory['mm_place_of_birth'] & SB_EDIT && !strlen($data['mm_place_of_birth'])) {
		return false;
	}
	
	if ($mandatory['id_nationality'] & SB_EDIT && !$data['id_nationality']) {
		return false;
	}
	
	if ($mandatory['mm_id_number'] & SB_EDIT && $data['gender'] == 2 && !strlen($data['mm_id_number'])) {
		return false;
	}
	
	if ($mandatory['email'] & SB_EDIT && !strlen($data['email'])) {
		return false;
	}
	
	if ($mandatory['mm_contact_phone_number'] & SB_EDIT && !strlen($data['mm_contact_phone_number'])) {
		return false;
	}
	
	if ($mandatory['mm_contact_mobile_number'] & SB_EDIT && !strlen($data['mm_contact_mobile_number'])) {
		return false;
	}
	
	if ($mandatory['id_country'] & SB_EDIT && !$data['id_country']) {
		return false;
	}
	
	if ($mandatory['id_region'] & SB_EDIT && !$data['id_region']) {
		return false;
	}
	
	if ($mandatory['id_city'] & SB_EDIT && !$data['id_city']) {
		return false;
	}
	
	if ($mandatory['mm_city'] & SB_EDIT && !strlen($data['mm_city'])) {
		return false;
	}
	
	if ($mandatory['zipcode'] & SB_EDIT && !strlen($data['zipcode'])) {
		return false;
	}
	
	if ($mandatory['mm_address_1'] & SB_EDIT && !strlen($data['mm_address_1'])) {
		return false;
	}
	
	if ($mandatory['mm_address_2'] & SB_EDIT && !strlen($data['mm_address_2'])) {
		return false;
	}
	
	if ($mandatory['id_language_1'] & SB_EDIT && !strlen($data['id_language_1'])) {
		return false;
	}
	
	if ($mandatory['id_language_2'] & SB_EDIT && !strlen($data['id_language_2'])) {
		return false;
	}
	
	if ($mandatory['id_language_3'] & SB_EDIT && !strlen($data['id_language_3'])) {
		return false;
	}
	
	if ($mandatory['mm_level_of_english'] & SB_EDIT && !$data['mm_level_of_english']) {
		return false;
	}
	
	if ($mandatory['site_language'] & SB_EDIT && !$data['site_language']) {
		return false;
	}
	
	if ($mandatory['mm_employment_status'] & SB_EDIT && !$data['mm_employment_status']) {
		return false;
	}
	
	if ($mandatory['mm_business_name'] & SB_EDIT && $data['mm_employment_status'] == 2 && !strlen($data['mm_business_name'])) {
		return false;
	}
	
	if ($mandatory['mm_employer_name'] & SB_EDIT && $data['mm_employment_status'] == 3 && !strlen($data['mm_employer_name'])) {
		return false;
	}
	
	if ($mandatory['mm_job_position'] & SB_EDIT && $data['mm_employment_status'] != 1 && !strlen($data['mm_job_position'])) {
		return false;
	}
	
	if ($mandatory['mm_work_address'] & SB_EDIT && $data['mm_employment_status'] != 1 && !strlen($data['mm_work_address'])) {
		return false;
	}
	
	if ($mandatory['mm_work_phone_number'] & SB_EDIT && $data['mm_employment_status'] != 1 && !strlen($data['mm_work_phone_number'])) {
		return false;
	}
	
	if ($mandatory['mm_ref_1_first_name'] & SB_EDIT && !strlen($data['mm_ref_1_first_name'])) {
		return false;
	}
	
	if ($mandatory['mm_ref_1_last_name'] & SB_EDIT && !strlen($data['mm_ref_1_last_name'])) {
		return false;
	}
	
	if ($mandatory['mm_ref_1_relationship'] & SB_EDIT && !strlen($data['mm_ref_1_relationship'])) {
		return false;
	}
	
	if ($mandatory['mm_ref_1_phone_number'] & SB_EDIT && !strlen($data['mm_ref_1_phone_number'])) {
		return false;
	}
	
	if ($mandatory['mm_ref_2_first_name'] & SB_EDIT && !strlen($data['mm_ref_2_first_name'])) {
		return false;
	}
	
	if ($mandatory['mm_ref_2_last_name'] & SB_EDIT && !strlen($data['mm_ref_2_last_name'])) {
		return false;
	}
	
	if ($mandatory['mm_ref_2_relationship'] & SB_EDIT && !strlen($data['mm_ref_2_relationship'])) {
		return false;
	}
	
	if ($mandatory['mm_ref_2_phone_number'] & SB_EDIT && !strlen($data['mm_ref_2_phone_number'])) {
		return false;
	}
	
	return true;	
}

//SH adding credit points to user account
function AddCreditPoints($id_user, $points, $id_group)
{
	global $dbconn;
	
	// settings
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2'));
	
	$check = $dbconn->getOne('SELECT id FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($id_user));
	
	if (!empty($check))
	{
		$strSQL = 'UPDATE '.BILLING_USER_ACCOUNT_TABLE.' SET account_curr = account_curr + '.$points.', date_refresh = NOW() WHERE id_user = ?';
		$dbconn->Execute($strSQL, array($id_user));
	}
	else
	{
		$strSQL = 'INSERT INTO '.BILLING_USER_ACCOUNT_TABLE.' SET id_user = ?, account_curr = ?, date_refresh = NOW()';
		$dbconn->Execute($strSQL, array($id_user, $points));
	}
	
	if ($id_group == '') {
		$id_group = PG_SINGLE_CREDIT_POINTS;
	}
	
	$product_name = '';
	if ($id_group == PG_INITIAL_CREDIT_POINT_BONUS) {
		$product_name = 'Initial Credit Points';
	}
	
	$strSQL =
		'INSERT INTO '.BILLING_ENTRY_TABLE.' SET
				id_user = ?, amount = ?, currency = ?, id_group = ?, id_product = 0, date_entry = NOW(),
				entry_type = "admin", txn_type = "site_credits", product_name = ?';
	$dbconn->Execute($strSQL, array($id_user, $points, $settings['site_unit_costunit'], $id_group, $product_name));
}

// VP checking application submit status
//
function isApplicationSubmit($id_user)
{
	global $dbconn;
	$rs = $dbconn->Execute('SELECT mm_application_submit FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
	return !empty($rs->fields[0]);
}

//VP submitting the form to GetResponse
function submitToGetResponse($postData)
{
	$remoteURL = 'http://www.getresponse.com/add_contact_webform.html';
	
	//traverse array and prepare data for posting (key1=value1)
	foreach ($postData as $key => $value) {
		$postItems[] = $key . '=' . $value;
	}
	
	//create the final string to be posted using implode()
	$postString = implode ('&', $postItems);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $remoteURL);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
	curl_exec($ch);
	//$output = curl_exec($ch); // get output
	//$info = curl_getinfo($ch);
	curl_close($ch);
	
	//return $output;
}

//VP submitting the form to AWeber
/*
function submitToAweber($postData)
{
	$remoteURL = 'http://www.aweber.com/scripts/addlead.pl';
	
	//traverse array and prepare data for posting (key1=value1)
	foreach ($postData as $key => $value) {
		$postItems[] = $key . '=' . $value;
	}
	
	//create the final string to be posted using implode()
	$postString = implode ('&', $postItems);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $remoteURL);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
	@curl_exec($ch);
	//$output = curl_exec($ch);
	//$info = curl_getinfo($ch);
	curl_close($ch);
	
	//return $output;
}
*/
?>