<?php

/**
* Solve360 integration
*
* @package DatingPro
* @subpackage Admin Mode
**/

/*
	new column id_solve360 in table user
	
	following events trigger API calls to add or update Solve360 contacts:
	
	load all contacts from TLDF users (OK)
	add contact after registration (OK)
	update contact after user updates his/her profile (OK)
	update TLDF Login Count after sign in (OK)
	update Last Seen TLDF after sign in (OK)
	update TLDF Status after user submits application and becomes Trial (OK)
	set TLDF Trial Start date after user becomes Trial (OK)
	update TLDF Status when admin changes user status via checkbox in user list (OK)
	update contact when admin updates a profile (OK)
	update contact and TLDF Platinum Form when user submits the platinum application form (OK)
	set TLDF Platinum Paid date when user pays with PayPal (OK)
	set TLDF Platinum Paid date when admin approves offline payment (OK)
	set TLDF Platinum Paid date when user pays with credit points (not in use)
	update Platinum Verified and assign Platinum group when admin approves platinum (OK)
	update contact and TLDF Express Interest date when user submits the TLDE Express Interest form (OK)
	update Current Group and TLDF Membership Ends when user pays with PayPal (OK)
	update Current Group and TLDF Membership Ends when admin approves offline payment (OK)
	update Current Group and TLDF Membership Ends when user pays with credit points (not in use)
	update Current Group and TLDF Membership Ends when admin directly assigns a group in Billing (OK)
	update Current Group after expiration (not in use)
	update TLDF Membership Ends when admin adds days in Billing (OK)
	update TLDF Membership Ends when paypal subscription is cancelled (not in use)
	update TLDF Membership Ends when paypal subscription ends (not in use)
	
	idea:
	reduce code by creating a wrapper function with the following signature
	
	Solve360($action, contactData, $error_message, $login, $id_solve360 = null)
	
	$action can be 'add' or 'edit'
	$contactDate is array with plain text keys; array needs to be transformed to use internal Solve360 field names
	$error_message is subject line in error email
	$login is username for error email
	$id_solve360 is needed for 'edit' action
*/

require_once '../include/config.php';
require_once '../common.php';
require_once '../include/config_admin.php';
require_once '../include/functions_auth.php';
require_once '../include/functions_admin.php';

$auth = auth_user();

login_check($auth);

//IsFileAllowed($auth[0], GetRightModulePath(__FILE__), 'export');

$sel = isset($_REQUEST['sel']) ? $_REQUEST['sel']: '';

switch ($sel) {
	case 'load_contacts':
		LoadContacts();
	break;
	case 'load_contacts_run':
		LoadContactsRun();
	break;
	case 'export_members':
	default:
		ExportMembers();
	break;
}

exit;

/**
 * LoadContacts
 */

function LoadContacts($err = '')
{
	global $smarty, $config, $lang;
	
	AdminMainMenu($lang['admin_menu']['load_contacts']);
	
	$debug = isset($_REQUEST['debug']) ? true : false;
	
	$smarty->assign('debug', $debug);
	
	$form['err'] = $err;
	
	$smarty->assign('form', $form);
	$smarty->assign('button', $lang['button']);
	$smarty->assign('header', $lang['solve360']);
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_solve360_load_contacts.tpl');
	exit;
}

/**
 * LoadContactsRun
 */

function LoadContactsRun()
{
	global $dbconn, $config;
	
	$offline_test = false;
	$limit = 0;				// 0 = all
	
	$show_progress = (isset($_GET['show_progress']) && $_GET['show_progress'] == '1');
	
	require_once 'include/logger.class.php';
	
	$solve360_log = new logger('../export/', 'solve360_load_contacts', 'Loading Contacts');
	
	require_once $config['site_path'].'/include/Solve360Service.php';
	$solve360Service = new Solve360Service(SOLVE360_USER, SOLVE360_TOKEN);
	
	$solve360 = array();
	require '../include/Solve360CustomFields.php';
	
	// 1h, should be good for 3600 contacts
	@set_time_limit(3600);
	
	if ($show_progress) {
		// flush and close all open buffers
		while (ob_get_level() > 0) {
			ob_end_flush();
		}

		// start output buffering
		ob_start();

		// output buffering stuff, does not work though, maybe because of mod_security
		echo '<html><head></head><body>';

		/*
		echo 'zlib.output_compression='.ini_get('zlib.output_compression').'<br>';
		echo 'output_buffering='.ini_get('output_buffering').'<br>';
		echo 'implicit_flush='.ini_get('implicit_flush').'<br>';

		//@apache_setenv('no-gzip', 1);
		@ini_set('zlib.output_compression', '0');
		@ini_set('output_buffering', '0');
		@ini_set('implicit_flush', '1');

		echo 'zlib.output_compression='.ini_get('zlib.output_compression').'<br>';
		echo 'output_buffering='.ini_get('output_buffering').'<br>';
		echo 'implicit_flush='.ini_get('implicit_flush').'<br>';
		*/
	}
	
	// query
	$rs = $dbconn->Execute(
		'SELECT u.*,
				country.name AS country, region.name AS region, city.name AS city, nationality.name AS nationality,
				lang1.name AS language_1, lang2.name AS language_2, lang3.name AS language_3,
				weight.name AS weight, height.name AS height,
				site_lang.name AS site_language, marital.name AS marital_status,
				level_english.name AS level_of_english, emp_status.name AS employment_status,
				g.id AS id_group, g.name AS group_name, bup.date_begin, bup.date_end
		   FROM '.USERS_TABLE.' u
	  LEFT JOIN '.COUNTRY_SPR_TABLE.' country ON country.id = u.id_country
	  LEFT JOIN '.REGION_SPR_TABLE.' region ON region.id = u.id_region
	  LEFT JOIN '.CITY_SPR_TABLE.' city ON city.id = u.id_city
	  LEFT JOIN '.NATION_SPR_TABLE.' nationality ON nationality.id = u.id_nationality
	  LEFT JOIN '.LANGUAGE_SPR_TABLE.' lang1 ON lang1.id = u.id_language_1
	  LEFT JOIN '.LANGUAGE_SPR_TABLE.' lang2 ON lang2.id = u.id_language_2
	  LEFT JOIN '.LANGUAGE_SPR_TABLE.' lang3 ON lang3.id = u.id_language_3
	  LEFT JOIN '.WEIGHT_SPR_TABLE.' weight ON weight.id = u.id_weight
	  LEFT JOIN '.HEIGHT_SPR_TABLE.' height ON height.id = u.id_height
	  LEFT JOIN '.LANGUAGE_TABLE.' site_lang ON site_lang.id = u.site_language
	  LEFT JOIN '.MARITAL_STATUS_SPR_TABLE.' marital ON marital.id = u.mm_marital_status
	  LEFT JOIN '.LEVEL_ENGLISH_SPR_TABLE.' level_english ON level_english.id = u.mm_level_of_english
	  LEFT JOIN '.EMPLOYMENT_STATUS_SPR_TABLE.' emp_status ON emp_status.id = u.mm_employment_status
	  LEFT JOIN '.USER_GROUP_TABLE.' ug ON ug.id_user = u.id
	  LEFT JOIN '.GROUPS_TABLE.' g ON g.id = ug.id_group
	  LEFT JOIN '.BILLING_USER_PERIOD_TABLE.' bup ON bup.id_user = u.id
		  WHERE root_user = "0" AND guest_user = "0"
	   ORDER BY id');
	
	// write data rows
	$i = 1;
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		
		$categories		= array();
		$categories[]	= SOLVE360_TAG_TLDF;
		
		// gender tag
		if ($row['gender'] == GENDER_MALE) {
			$categories[] = SOLVE360_TAG_GUY;
		} else {
			$categories[] = SOLVE360_TAG_LADY;
		}
		
		// membership group tag
		switch ($row['id_group']) {
			case MM_SIGNUP_GUY_ID:
				$categories[] = SOLVE360_TAG_SIGN_UP_GUY;
			break;
			case MM_SIGNUP_LADY_ID:
				$categories[] = SOLVE360_TAG_SIGN_UP_LADY;
			break;
			case MM_TRIAL_GUY_ID:
				$categories[] = SOLVE360_TAG_TRIAL_GUY;
			break;
			case MM_TRIAL_LADY_ID:
				$categories[] = SOLVE360_TAG_TRIAL_LADY;
			break;
			case MM_REGULAR_GUY_ID:
				$categories[] = SOLVE360_TAG_REGULAR_GUY;
			break;
			case MM_REGULAR_LADY_ID:
				$categories[] = SOLVE360_TAG_REGULAR_LADY;
			break;
			case MM_PLATINUM_GUY_ID:
				$categories[] = SOLVE360_TAG_PLATINUM_GUY;
			break;
			case MM_PLATINUM_LADY_ID:
				$categories[] = SOLVE360_TAG_PLATINUM_LADY;
			break;
		}
		
		// platinum applied tag
		if ($row['mm_platinum_paid'] && !$row['platinum_verified']) {
			$categories[] = SOLVE360_TAG_PLATINUM_APPLIED;
		}
		
		$contactData = array(
			$solve360['TLDF ID Number']			=> $row['id'],
			'firstname'							=> trim(stripslashes($row['fname'])),
			'lastname'							=> trim(stripslashes($row['sname'])),
			$solve360['TLDF Status']			=> ($row['status'] ? 'Good' : 'Inactive'),
			$solve360['Platinum Verified']		=> ($row['platinum_verified'] ? 'Yes' : 'No'),
			$solve360['TLDF Confirmed']			=> ($row['confirm'] ? 'Yes' : 'No'),
			$solve360['TLDF Login']				=> stripslashes($row['login']),
			$solve360['Gender']					=> ($row['gender'] == GENDER_MALE ? 'Guy' : 'Lady'),
			'personalemail'						=> stripslashes($row['email']),
			$solve360['Country']				=> stripslashes($row['country']),					// lookup
			$solve360['Region']					=> stripslashes($row['region']),					// lookup
			$solve360['Nationality']			=> stripslashes($row['nationality']),				// lookup
			$solve360['Language 1']				=> stripslashes($row['language_1']),				// lookup
			$solve360['Birthday']				=> substr($row['date_birthday'], 0, 10),
			$solve360['Last Seen TLDF']			=> $row['date_last_seen'],
			$solve360['Registration Date']		=> $row['date_birthday'],
			$solve360['TLDF Login Count']		=> (int) $row['login_count'],
			$solve360['Nick Name']				=> stripslashes($row['mm_nickname']),
			$solve360['National ID Number']		=> stripslashes($row['mm_id_number']),
			$solve360['ID Type']				=> stripslashes($row['mm_id_type']),
			'homephone'							=> stripslashes($row['mm_contact_phone_number']),
			'cellularphone'						=> stripslashes($row['mm_contact_mobile_number']),
			$solve360['Marital Status']			=> stripslashes($row['marital_status']),			// lookup
			$solve360['Place Of Birth']			=> stripslashes($row['mm_place_of_birth']),
			$solve360['City']					=> stripslashes($row['mm_city']),
			$solve360['Home Address 1']			=> stripslashes($row['mm_address_1']),
			$solve360['Home Address 2']			=> stripslashes($row['mm_address_2']),
			$solve360['Home Address 3']			=> stripslashes($row['mm_address_3']),
			$solve360['Level Of English']		=> stripslashes($row['level_of_english']),			// lookup
			$solve360['Employer Name']			=> stripslashes($row['mm_employer_name']),
			'jobtitle'							=> stripslashes($row['mm_job_position']),
			'businessaddress'					=> stripslashes($row['mm_work_address']),
			'businessphonedirect'				=> stripslashes($row['mm_work_phone_number']),
			$solve360['Current Group']			=> stripslashes($row['group_name']),				// lookup
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
		
		// possibly empty date/time fields
		if ($row['mm_platinum_submit']) {
			$contactData[ $solve360['Platinum Form'] ] = $row['mm_platinum_submit'];
		}
		if ($row['mm_platinum_paid']) {
			$contactData[ $solve360['Platinum Paid'] ] = $row['mm_platinum_paid'];
		}
		if ($row['tlde_express_interest_submit']) {
			$contactData[ $solve360['TLDE Express Interest'] ] = $row['tlde_express_interest_submit'];
		}
		if ($row['date_begin']) {
			$contactData[ $solve360['TLDF Trial Start Date'] ] = $row['date_begin'];
		}
		if ($row['date_end']) {
			$contactData[ $solve360['TLDF Membership Ends'] ] = $row['date_end'];
		}
		
		if (!$offline_test) {
			$contact = $solve360Service->addContact($contactData);
		}
		
		if (isset($contact->errors)) {
			if ($show_progress) {
				echo '<div style="color:red">record='.$i.' ID='.$row['id'].' Login='.$row['login'].' ERROR!<br>'.$contact->errors->asXml().'</div>';
			}
			$solve360_log->log('record='.$i.' ID='.$row['id'].' Login='.$row['login'].' ERROR: '.$contact->errors->asXml());
		} else {
			if ($offline_test) {
				$id_solve360 = '1000'.$row['id'];
			} else {
				$id_solve360 = $contact->item->id;
				$dbconn->Execute('UPDATE '.USERS_TABLE.' SET id_solve360 = ? WHERE id = ?', array($id_solve360, $row['id']));
			}
			if ($show_progress) {
				echo '<div>record='.$i.' ID='.$row['id'].' Login='.$row['login'].' added with Contact ID='.$id_solve360.'</div>';
			}
			$solve360_log->log('record='.$i.' ID='.$row['id'].' Login='.$row['login'].' added with Contact ID='.$id_solve360);
		}
		
		if ($show_progress) {
			// complex buffer fill for gzip
			echo '<!--';
			for ($j = 0; $j < 2500; $j++) {
				echo md5((string)$j);
			}
			echo '-->';

			@ob_flush();
			flush();
		}
		
		if (!empty($limit) && $i >= $limit) break;
		
		$rs->MoveNext();
		$i++;
	}
	
	if ($show_progress) {
		echo '<br>DONE !';
	}
	$solve360_log->log('DONE !');
}

/**
 * ExportMembers
 */

function ExportMembers($err = '')
{
	global $smarty, $dbconn, $config, $lang;
	
	AdminMainMenu($lang['admin_menu']['export_members']);
	
	$debug = isset($_REQUEST['debug']) ? true : false;
	
	$smarty->assign('debug', $debug);
	
	$filename = 'users.csv';
	
	$smarty->assign('filename', $filename);
	
	// open export file
	$handle = fopen('../export/'.$filename, 'w');
	
	// write header row
	$line = '"TLDF ID Number",';		// id
	$line.= '"First Name",';			// fname
	$line.= '"Last Name",';				// sname
#	$line.= '"patr",';					// seems to be not in use
	$line.= '"TLDF Status",';			// status
	$line.= '"Platinum Verified",';		// platinum_verified
	$line.= '"TLDF Confirmed",';		// confirm					NEW
	$line.= '"visible",';
	$line.= '"TLDF Login",';			// login
#	$line.= '"password",';				// no need to export
	$line.= '"Gender",';				// gender
	$line.= '"couple",';
	$line.= '"couple_user",';
	$line.= '"Personal Email",';		// email
#	$line.= '"id_c",';					// seems to be not in use
	$line.= '"Country",';				// country
	$line.= '"Region",';				// region
	$line.= '"city_from_id",';			// use mm_city instead
	$line.= '"zipcode",';
	$line.= '"Nationality",';			// nationality
	$line.= '"Language 1",';			// language_1
	$line.= '"Language 2",';			// language_2
	$line.= '"Language 3",';			// language_3
	$line.= '"weight",';
	$line.= '"height",';
	$line.= '"comment",';
	$line.= '"headline",';
	$line.= '"about_me",';
	$line.= '"what_i_do",';
	$line.= '"my_idea",';
	$line.= '"hoping_to_find",';
	$line.= '"icon_path",';
	$line.= '"icon_path_temp",';
	$line.= '"Birthday",';				// date_birthday
	$line.= '"Last Seen TLDF",';		// date_last_seen
	$line.= '"Registration Date",';		// date_registration
#	$line.= '"root_user",';				// no need to export
#	$line.= '"guest_user",';			// no need to export
	$line.= '"TLDF Login Count",';		// login_count
	$line.= '"big_icon_path",';
	$line.= '"site_language",';
	$line.= '"date_topsearched",';
	$line.= '"phone",';					// for VoIP, not used by TLDF
	$line.= '"Nick Name",';				// mm_nickname
	$line.= '"National ID Number",';	// mm_id_number
	$line.= '"ID Type",';				// mm_id_type
	$line.= '"Home Phone",';			// mm_contact_phone_number
	$line.= '"Mobile Phone",';			// mm_contact_mobile_number
	$line.= '"Marital Status",';		// mm_marital_status
	$line.= '"Place Of Birth",';		// mm_place_of_birth
	$line.= '"City",';					// mm_city
	$line.= '"Home Address 1",';		// mm_address_1
	$line.= '"Home Address 2",';		// mm_address_2
	$line.= '"Home Address 3",';		// mm_address_3
	$line.= '"Level Of English",';		// mm_level_of_english
	$line.= '"mm_employment_status",';
	$line.= '"mm_business_name",';
	$line.= '"Employer Name",';			// mm_employer_name
	$line.= '"Job Title",';				// mm_job_position
	$line.= '"Business Address",';		// mm_work_address
	$line.= '"Work Phone",';			// mm_work_phone_number
	$line.= '"mm_ref_1_first_name",';
	$line.= '"mm_ref_1_last_name",';
	$line.= '"mm_ref_1_relationship",';
	$line.= '"mm_ref_1_phone_number",';
	$line.= '"mm_ref_2_first_name",';
	$line.= '"mm_ref_2_last_name",';
	$line.= '"mm_ref_2_relationship",';
	$line.= '"mm_ref_2_phone_number",';
	$line.= '"mm_application_submit",';
	$line.= '"Platinum Form",';					// mm_platinum_submit		NEW
	$line.= '"mm_platinum_submit_comment",';
	$line.= '"mm_best_call_time_weekdays",';
	$line.= '"mm_best_call_time_saturdays",';
	$line.= '"mm_best_call_time_sundays",';
	$line.= '"Platinum Paid",';					// mm_platinum_paid
	$line.= '"mm_platinum_applied",';
	$line.= '"chk_background",';
	$line.= '"chk_marital_status",';
	$line.= '"chk_work_history",';
	$line.= '"chk_interview_photo",';
	$line.= '"chk_date",';
	$line.= '"chk_staff",';
	$line.= '"chk_comment",';
	$line.= '"videoplay",';
	$line.= '"TLDE Express Interest",';			// tlde_express_interest_submit
	$line.= '"Current Group",';					// group
	$line.= '"TLDF Trial Start Date",';			// date_begin
	$line.= '"TLDF Membership Ends"'."\n";		// date_end
	
	fwrite($handle, $line);
	
	// query
	$rs = $dbconn->Execute(
		'SELECT u.*,
				country.name AS country, region.name AS region, city.name AS city, nationality.name AS nationality,
				lang1.name AS language_1, lang2.name AS language_2, lang3.name AS language_3,
				weight.name AS weight, height.name AS height,
				site_lang.name AS site_language, marital.name AS mm_marital_status,
				level_english.name AS mm_level_of_english, emp_status.name AS mm_employment_status,
				g.name AS group_name, bup.date_begin, bup.date_end
		   FROM '.USERS_TABLE.' u
	  LEFT JOIN '.COUNTRY_SPR_TABLE.' country ON country.id = u.id_country
	  LEFT JOIN '.REGION_SPR_TABLE.' region ON region.id = u.id_region
	  LEFT JOIN '.CITY_SPR_TABLE.' city ON city.id = u.id_city
	  LEFT JOIN '.NATION_SPR_TABLE.' nationality ON nationality.id = u.id_nationality
	  LEFT JOIN '.LANGUAGE_SPR_TABLE.' lang1 ON lang1.id = u.id_language_1
	  LEFT JOIN '.LANGUAGE_SPR_TABLE.' lang2 ON lang2.id = u.id_language_2
	  LEFT JOIN '.LANGUAGE_SPR_TABLE.' lang3 ON lang3.id = u.id_language_3
	  LEFT JOIN '.WEIGHT_SPR_TABLE.' weight ON weight.id = u.id_weight
	  LEFT JOIN '.HEIGHT_SPR_TABLE.' height ON height.id = u.id_height
	  LEFT JOIN '.LANGUAGE_TABLE.' site_lang ON site_lang.id = u.site_language
	  LEFT JOIN '.MARITAL_STATUS_SPR_TABLE.' marital ON marital.id = u.mm_marital_status
	  LEFT JOIN '.LEVEL_ENGLISH_SPR_TABLE.' level_english ON level_english.id = u.mm_level_of_english
	  LEFT JOIN '.EMPLOYMENT_STATUS_SPR_TABLE.' emp_status ON emp_status.id = u.mm_employment_status
	  LEFT JOIN '.USER_GROUP_TABLE.' ug ON ug.id_user = u.id
	  LEFT JOIN '.GROUPS_TABLE.' g ON g.id = ug.id_group
	  LEFT JOIN '.BILLING_USER_PERIOD_TABLE.' bup ON bup.id_user = u.id
		  WHERE root_user = "0" AND guest_user = "0"
	   ORDER BY id');
	
	// write data rows
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$line = '"'.$row['id'].'",';
		$line.= '"'.stripslashes($row['fname']).'",';
		$line.= '"'.stripslashes($row['sname']).'",';
#		$line.= '"'.stripslashes($row['patr']).'",';
		if ($row['status']) {
			$line.= '"Good",';
		} else {
			$line.= '"Inactive",';
		}
		if ($row['platinum_verified']) {
			$line.= '"Yes",';
		} else {
			$line.= '"No",';
		}
		if ($row['confirm']) {
			$line.= '"Yes",';
		} else {
			$line.= '"No",';
		}
		$line.= '"'.$row['visible'].'",';
		$line.= '"'.stripslashes($row['login']).'",';
#		$line.= '"'.stripslashes($row['password']).'",';
		if ($row['gender'] == GENDER_MALE) {
			$line .= '"Guy",';
		} else {
			$line .= '"Lady",';
		}
		$line.= '"'.$row['couple'].'",';
		$line.= '"'.$row['couple_user'].'",';
		$line.= '"'.stripslashes($row['email']).'",';
#		$line.= '"'.$row['id_c'].'",';
#		$line.= '"'.$row['id_country'].'",';
		$line.= '"'.$row['country'].'",';
#		$line.= '"'.$row['id_region'].'",';
		$line.= '"'.$row['region'].'",';
#		$line.= '"'.$row['id_city'].'",';
		$line.= '"'.$row['city'].'",';
		$line.= '"'.stripslashes($row['zipcode']).'",';
#		$line.= '"'.$row['id_nationality'].'",';
		$line.= '"'.$row['nationality'].'",';
#		$line.= '"'.$row['id_language_1'].'",';
		$line.= '"'.$row['language_1'].'",';
#		$line.= '"'.$row['id_language_2'].'",';
		$line.= '"'.$row['language_2'].'",';
#		$line.= '"'.$row['id_language_3'].'",';
		$line.= '"'.$row['language_3'].'",';
#		$line.= '"'.$row['id_weight'].'",';
		$line.= '"'.$row['weight'].'",';
#		$line.= '"'.$row['id_height'].'",';
		$line.= '"'.$row['height'].'",';
		$line.= '"'.stripslashes($row['comment']).'",';
		$line.= '"'.stripslashes($row['headline']).'",';
		$line.= '"'.stripslashes($row['about_me']).'",';
		$line.= '"'.stripslashes($row['what_i_do']).'",';
		$line.= '"'.stripslashes($row['my_idea']).'",';
		$line.= '"'.stripslashes($row['hoping_to_find']).'",';
		$line.= '"'.stripslashes($row['icon_path']).'",';
		$line.= '"'.stripslashes($row['icon_path_temp']).'",';
		$line.= '"'.substr($row['date_birthday'], 0, 10).'",';
		$line.= '"'.$row['date_last_seen'].'",';
		$line.= '"'.$row['date_registration'].'",';
#		$line.= '"'.$row['root_user'].'",';
#		$line.= '"'.$row['guest_user'].'",';
		$line.= '"'.$row['login_count'].'",';
		$line.= '"'.stripslashes($row['big_icon_path']).'",';
		$line.= '"'.$row['site_language'].'",';								// from language table
		$line.= '"'.$row['date_topsearched'].'",';
		$line.= '"'.stripslashes($row['phone']).'",';
		$line.= '"'.stripslashes($row['mm_nickname']).'",';
		$line.= '"'.stripslashes($row['mm_id_number']).'",';
		$line.= '"'.stripslashes($row['mm_id_type']).'",';
		$line.= '"'.stripslashes($row['mm_contact_phone_number']).'",';
		$line.= '"'.stripslashes($row['mm_contact_mobile_number']).'",';
		$line.= '"'.$row['mm_marital_status'].'",';							// from marital_status_spr table
		$line.= '"'.stripslashes($row['mm_place_of_birth']).'",';
		$line.= '"'.stripslashes($row['mm_city']).'",';
		$line.= '"'.stripslashes($row['mm_address_1']).'",';
		$line.= '"'.stripslashes($row['mm_address_2']).'",';
		$line.= '"'.stripslashes($row['mm_address_3']).'",';
		$line.= '"'.$row['mm_level_of_english'].'",';						// from level_english_spr table
		$line.= '"'.$row['mm_employment_status'].'",';						// from employment_status_spr table
		$line.= '"'.stripslashes($row['mm_business_name']).'",';
		$line.= '"'.stripslashes($row['mm_employer_name']).'",';
		$line.= '"'.stripslashes($row['mm_job_position']).'",';
		$line.= '"'.stripslashes($row['mm_work_address']).'",';
		$line.= '"'.stripslashes($row['mm_work_phone_number']).'",';
		$line.= '"'.stripslashes($row['mm_ref_1_first_name']).'",';
		$line.= '"'.stripslashes($row['mm_ref_1_last_name']).'",';
		$line.= '"'.stripslashes($row['mm_ref_1_relationship']).'",';
		$line.= '"'.stripslashes($row['mm_ref_1_phone_number']).'",';
		$line.= '"'.stripslashes($row['mm_ref_2_first_name']).'",';
		$line.= '"'.stripslashes($row['mm_ref_2_last_name']).'",';
		$line.= '"'.stripslashes($row['mm_ref_2_relationship']).'",';
		$line.= '"'.stripslashes($row['mm_ref_2_phone_number']).'",';
		$line.= '"'.$row['mm_application_submit'].'",';
		$line.= '"'.$row['mm_platinum_submit'].'",';
		$line.= '"'.stripslashes($row['mm_platinum_submit_comment']).'",';
		$line.= '"'.stripslashes($row['mm_best_call_time_weekdays']).'",';
		$line.= '"'.stripslashes($row['mm_best_call_time_saturdays']).'",';
		$line.= '"'.stripslashes($row['mm_best_call_time_sundays']).'",';
		$line.= '"'.$row['mm_platinum_paid'].'",';
		$line.= '"'.$row['mm_platinum_applied'].'",';
		$line.= '"'.$row['chk_background'].'",';
		$line.= '"'.$row['chk_marital_status'].'",';
		$line.= '"'.$row['chk_work_history'].'",';
		$line.= '"'.$row['chk_interview_photo'].'",';
		$line.= '"'.$row['chk_date'].'",';
		$line.= '"'.stripslashes($row['chk_staff']).'",';
		$line.= '"'.stripslashes($row['chk_comment']).'",';
		$line.= '"'.$row['videoplay'].'",';
		$line.= '"'.$row['tlde_express_interest_submit'].'",';
		$line.= '"'.$row['group_name'].'",';
		$line.= '"'.$row['date_begin'].'",';
		$line.= '"'.$row['date_end'].'"';
		$line.= "\n";
		
		fwrite($handle, $line);
		
		$rs->MoveNext();
	}
	
	fclose($handle);
	
	$form['err'] = $err;
	
	$smarty->assign('form', $form);
	$smarty->assign('button', $lang['button']);
	$smarty->assign('header', $lang['solve360']);
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_solve360_export.tpl');
	exit;
}

?>