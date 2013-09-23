<?php

/**VP Cron Jobs page (it will excute according to Cron Job settings) **/
//
// $_REQUEST['view']	: 'table' or 'text' or '' // table and text are for testing; text produces live output and converts \n to <br>
// $_REQUEST['action']	: 'reset' or ''
//
// cron is executed every 12 hours (checked 2012-10-26)

include './include/config.php';
include './common.php';
include './include/functions_index.php';
include './include/class.phpmailer.php';
include './include/functions_mail.php';

$debug = false;

@set_time_limit(3600); // 1h, good for about 3600 emails

if ($config['server'] == 'http://www.thailadydatefinder.com')
{
	// don't use debug output on live site, because it creates noise in the cron email
	$debug = false;
	
	define('SEND_CRON_EMAIL',				true);
	define('MAX_EMAIL_SEND',				0);		// 0 = all
	define('BLOCK_USER_LIVE_EMAIL',			false);	// true = Sends cron mail to dummy email address
	define('SEND_TO_GUYS',					true);
	define('SEND_TO_LADIES',				true);
	
	// disable cron emails for testing purposes or "not in use"
	define('SEND_REGISTRATION_COMPLETE',	false);	// this is the old reminder !!!
	define('SEND_SIGNUP_0_2',				true);
	define('SEND_SIGNUP_0_3',				true);
	define('SEND_SIGNUP_0_4',				true);
	define('SEND_BECOME_PAID_MEMBER',		false); // needs text review !!!
	define('SEND_ONE_WEEK',					true);
	define('SEND_TWO_DAYS',					true);
	define('SEND_LAST_DAY',					true);
	define('SEND_ACC_EXPIRED',				true);
	define('SEND_RE_JOIN_US',				false);	// always false as inactive groups are no longer in use
	define('SEND_APPROVE_USER',				false);	// always false as new users are autoatically approved
	define('SEND_PLATINUM_VERIFY',			true);
}
elseif ($config['server'] == 'http://www.dev.thailadydatefinder.com')
{
	define('SEND_CRON_EMAIL',				false);
	define('MAX_EMAIL_SEND',				0);		// 0 = all
	define('BLOCK_USER_LIVE_EMAIL',			true);	// true = Sends cron mail to dummy email address
	define('SEND_TO_GUYS',					true);
	define('SEND_TO_LADIES',				true);
	
	// disable cron emails for testing purposes or "not in use"
	define('SEND_REGISTRATION_COMPLETE',	false);	// this is the old reminder !!!
	define('SEND_SIGNUP_0_2',				true);
	define('SEND_SIGNUP_0_3',				true);
	define('SEND_SIGNUP_0_4',				true);
	define('SEND_BECOME_PAID_MEMBER',		false); // needs text review !!!
	define('SEND_ONE_WEEK',					true);
	define('SEND_TWO_DAYS',					true);
	define('SEND_LAST_DAY',					true);
	define('SEND_ACC_EXPIRED',				true);
	define('SEND_RE_JOIN_US',				false);	// always false as inactive groups are no longer in use
	define('SEND_APPROVE_USER',				false);	// always false as new users are autoatically approved
	define('SEND_PLATINUM_VERIFY',			true);
}
else
{
	define('SEND_CRON_EMAIL',				false);
	define('MAX_EMAIL_SEND',				0);	// 0 = all
	define('BLOCK_USER_LIVE_EMAIL',			true);	// true = Sends cron mail to dummy email address
	define('SEND_TO_GUYS',					true);
	define('SEND_TO_LADIES',				true);
	
	// disable cron emails for testing purposes or "not in use"
	define('SEND_REGISTRATION_COMPLETE',	false);	// this is the old reminder !!!
	define('SEND_SIGNUP_0_2',				true);
	define('SEND_SIGNUP_0_3',				true);
	define('SEND_SIGNUP_0_4',				true);
	define('SEND_BECOME_PAID_MEMBER',		false); // needs text review !!!
	define('SEND_ONE_WEEK',					true);
	define('SEND_TWO_DAYS',					true);
	define('SEND_LAST_DAY',					true);
	define('SEND_ACC_EXPIRED',				true);
	define('SEND_RE_JOIN_US',				false);	// always false as inactive groups are no longer in use
	define('SEND_APPROVE_USER',				false);	// always false as new users are autoatically approved
	define('SEND_PLATINUM_VERIFY',			true);
}

$view = isset($_REQUEST['view']) ? $_REQUEST['view'] : '';

// Dummy email address to send users cron mails
#$DummyEmail = ($config['server'] == 'http://www.thailadydatefinder.com') ? 'thailadyfinder@gmail.com' : 'thailadyfindertest@gmail.com';
$DummyEmail = 'nchitrakar@alucio.com';

$settings = GetSiteSettings(array('use_registration_approve', 'use_registration_confirmation'));

// language
$site_lang = $config['default_lang'];

// include mail language file
$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
$lang_mail = array();
include $config['path_lang'].'mail/'.$lang_file;

//VP reset table for testing purpose
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'reset') {
	$dbconn->Execute('TRUNCATE TABLE '.CRON_ACTION_TABLE);
	if (isset($_REQUEST['view']) && $_REQUEST['view'] == 'table') {
		header('Location: cron_job.php?view=table');
		exit;
	} elseif (isset($_REQUEST['view']) && $_REQUEST['view'] == 'text') {
		header('Location: cron_job.php?view=text');
		exit;
	} else {
		header('Location: cron_job.php');
		exit;
	}
}

//Fetching site_activity mail
/*
$siteActSQL = 'SELECT * FROM '.SITE_ACTIVITY_MAIL_TABLE.'';
$rs1 = $dbconn->Execute($siteActSQL);
$i = 0;
$siteActMailArr = array();
while (!$rs1->EOF) {
	$siteActMailArr[$i] = $rs1->GetRowAssoc(false);
	$rs1 ->MoveNext();
	$i++;
}
$finalMailArr = array();
foreach ($siteActMailArr as $siteActMail) {
	$key = $siteActMail['subject'];
	$finalMailArr[$key] = $siteActMail;
}

//Fetching billing send request information  *Required for cron mails for 9997 bhatt paid Ladies
$startDay = date('Y-m-d 00:00:00',  time());
$endDay	  = date('Y-m-d 23:59:59', time());
//$billSQL =
//	"SELECT * FROM '.BILLING_REQUESTS_TABLE.' WHERE date_send BETWEEN '".$startDay."' AND '".$endDay."' AND id_product = 22 ";
//		
//	echo $billSQL; die;
*/

// Fetching All users
$strSQL =
	'SELECT DISTINCT u.id, u.login, u.fname, u.sname, u.status, u.gender, u.email, u.date_birthday, u.confirm,
			u.icon_path, u.mm_platinum_applied, u.date_registration,
			ug.id_group, g.name AS group_name,
			b.id_group_period, b.date_end, gp.id_group AS id_group_from_period, g2.name AS group_name_from_period,
			p.is_rem_block,
			UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(u.date_registration) AS reg_duration,
			UNIX_TIMESTAMP(b.date_end) - UNIX_TIMESTAMP(b.date_begin) AS all_res
	   FROM '.USERS_TABLE.' AS u
  LEFT JOIN '.USER_GROUP_TABLE.' AS ug ON ug.id_user = u.id
  LEFT JOIN '.GROUPS_TABLE.' AS g ON g.id = ug.id_group
  LEFT JOIN '.BILLING_USER_PERIOD_TABLE.' AS b ON b.id_user = u.id
  LEFT JOIN '.GROUP_PERIOD_TABLE.' AS gp ON gp.id = b.id_group_period
  LEFT JOIN '.GROUPS_TABLE.' AS g2 ON g2.id = gp.id_group
  LEFT JOIN '.USER_PRIVACY_SETTINGS.' AS p ON p.id_user = u.id
	  WHERE u.root_user = "0" AND u.guest_user = "0"
   ORDER BY u.id ASC';

$rs = $dbconn->Execute($strSQL);
$i = 0;
$all_users = array();

// RS: this can cause memory problems, it's better to process one user at a time directly
// without storing all users in an array first
while (!$rs->EOF) {
	$row = $rs->GetRowAssoc(false);
	if (SEND_TO_GUYS && $row['gender'] == '1' || SEND_TO_LADIES && $row['gender'] == '2') {
		$all_users[$i]['id']				= intval($row['id']);
		$all_users[$i]['login']				= stripslashes($row['login']);
		$all_users[$i]['fname']				= stripslashes($row['fname']);
		$all_users[$i]['sname']				= stripslashes($row['sname']);
		$all_users[$i]['status']			= $row['status'];
		$all_users[$i]['gender']			= intval($row['gender']);
		$all_users[$i]['date_birthday']		= $row['date_birthday'];
		$all_users[$i]['email']				= stripslashes($row['email']);
		$all_users[$i]['confirm']			= (int) $row['confirm'];
		$all_users[$i]['icon_path']			= $row['icon_path'];
		$all_users[$i]['date_registration']	= $row['date_registration'];
		$all_users[$i]['is_applied']		= $row['mm_platinum_applied'] ? 1 : 0;
		$all_users[$i]['id_group']			= intval($row['id_group']);
		$all_users[$i]['group_name']		= stripslashes($row['group_name']);
		$all_users[$i]['id_group_period']	= $row['id_group_period'];
		$all_users[$i]['id_group_from_period']		= $row['id_group_from_period'];
		$all_users[$i]['group_name_from_period']	= stripslashes($row['group_name_from_period']);
		$all_users[$i]['date_end']			= $row['date_end'];
		$all_users[$i]['is_rem_block']		= $row['is_rem_block'] ? $row['is_rem_block'] : '0';
		$all_users[$i]['reg_duration']		= $row['reg_duration'] ? intval($row['reg_duration']/3600) : '';	// hours
		$all_users[$i]['all_res']			= $row['all_res'] ? intval($row['all_res']/3600) : '';				// hours
		$all_users[$i]['now_res']			= GetRemainDaysInAccount(intval($row['id'])); // days, from functions_index.php
	}
	//echo $all_users[$i]['now_res'].'<br>';
	$rs->MoveNext();
	$i++;
}

unset($row);
$rs->free();

if ($view == 'table') {
	print_top_test(count($all_users));
} else {
	print_top_live();
}

$i = 0;

foreach ($all_users as $user)
{
	$mail_err		= false;
	$action			= '';
	$action_id		= 0;
	
	$data = array();
	
	$data['sender']		= '';	// 'Admin of '.$config['server'];
	$data['subject']	= '';
	$data['message']	= '';
	
	$data['id']						= $user['id']    ? $user['id']    : '';
	$data['login']					= $user['login'] ? $user['login'] : '';
	$data['fname']					= $user['fname'] ? $user['fname'] : '';
	$data['sname']					= $user['sname'] ? $user['sname'] : '';
	$data['email']					= $user['email'] ? $user['email'] : '';
	$data['date_registration']		= $user['date_registration'];
	$data['group_name']				= $user['group_name'];
	$data['id_group_period']		= $user['id_group_period'];
	$data['id_group_from_period']	= $user['id_group_from_period'];
	$data['group_name_from_period']	= $user['group_name_from_period'];
	$data['date_end']				= $user['date_end'];
	
	$data['date_birthday_formatted'] = $user['date_birthday'] ? formatDateSql($user['date_birthday']) : '';
	
	$data['full_name']	= trim($data['fname'].' '.$data['sname']);
	
	if (BLOCK_USER_LIVE_EMAIL) {
		$data['email'] = $DummyEmail;
	}
	
	$data['is_applied'] = $user['is_applied'];
	$data['confirm']	= $user['confirm'];
	$data['icon_path']	= $user['icon_path'];
	
	$unsubscribe_userid = rand(11111,99999).$user['id'].rand(11111,99999);
	$unsubscribe_url = $config['server'].'/reminder_block.php?uid='.$unsubscribe_userid;
	
	$data['unsubscribe_url'] = $unsubscribe_url;
	
	$data['urls'] = GetUserEmailLinks();
	
	$suffix = ($user['gender'] == GENDER_MALE) ? '_e' : '_t';
	
	if ($debug) print $data['login'].' | '.$user['gender'].' '.$suffix.'<br>';
	
	/**
	 * Remind Signup users to complete their registration, send every 7 days
	 * OLD EMAIL, NOT IN USE ANY LONGER !!!
	 **/
	if (SEND_REGISTRATION_COMPLETE)
	{
		if ($user['id_group'] == MM_SIGNUP_GUY_ID || $user['id_group'] == MM_SIGNUP_LADY_ID)
		{
			if ($user['reg_duration'] > 168 && $user['is_rem_block'] != '1')
			{
				list($cron_id, $cron_gap) = get_last_cron_action($user['id'], CJ_REGISTER_COMPLETE_ID);
				
				// send only once per week
				if ($cron_gap > 168)
				{
					// action
					$action_id	= CJ_REGISTER_COMPLETE_ID;
					$action		= 'registration completion reminder';
					
					// subject
					$data['subject'] = $lang_mail['cron_registration_complete'.$suffix]['subject'];
					
					// message
					$data['message'] = $lang_mail['cron_registration_complete'.$suffix]['message'];
					
					// email to user
					if (SEND_CRON_EMAIL) {
						$mail_err = SendMail($site_lang, $data['email'], $config['site_email'], $data['subject'],
							$data, 'mail_cron_registration_complete_user', null,
							$data['full_name'], $data['sender'], 'cron_registration_complete', $user['gender']);
					}
					
					// update cron action table
					if (!$mail_err) {
						if ($cron_id) {
							$dbconn->Execute('UPDATE '.CRON_ACTION_TABLE.' SET id_job = ?, job_time = NOW() WHERE id = ?', array($action_id, $cron_id));
						} else {
							$dbconn->Execute('INSERT INTO '.CRON_ACTION_TABLE.' SET id_job = ?, id_user = ?, job_time = NOW()', array($action_id, $user['id']));
						}
					}
					
					// print row
					if ($view == 'table') {
						print_row_test($action_id, $action, $data, $user, false);
					} else {
						print_row_live($action_id, $action, $data);
					}
				}
			}
		}
	}
	
	/**
	 * Reminders to Signup users to complete their registration
	 **/
	if ($user['id_group'] == MM_SIGNUP_GUY_ID || $user['id_group'] == MM_SIGNUP_LADY_ID)
	{
		/**
		 * 1st reminder to Signup users to complete their registration, send after 24 hours
		 **/
		if (SEND_SIGNUP_0_2 && $user['reg_duration'] > 24 && $user['is_rem_block'] != '1' && not_yet_sent($user['id'], CJ_SIGNUP_0_2_ID))
		{
			// action
			$action_id	= CJ_SIGNUP_0_2_ID;
			$action		= 'registration completion reminder 0.2';
			
			// create login token
			$token = CreateToken($user['id']);
			
			// additional data
			if ($settings['use_registration_confirmation'] && !$data['confirm']) {
				$data['confirm_link'] = $config['server'].$config['site_root'].'/confirm.php?id='.$user['id'].'&login_id='.$user['id'].'&token='.$token;
			} else {
				$data['login_link'] = $config['server'].$config['site_root'].'/index.php?sel=login&login_id='.$user['id'].'&token='.$token;
			}
			
			$data['pass'] = $lang_mail['registration_2'.$suffix]['password_lookup'];
			
			#RS: not in use any longer
			#$data['freecd_link'] = $config['server'].$config['site_root'].'/request_info.php?id='.$user['id'];
			#$data['approve'] = $settings['use_registration_approve'] ? 1 : 0;
			
			// subject
			$data['subject'] = str_replace('[name]', $user['fname'], $lang_mail['registration_2'.$suffix]['subject']);
			
			// message (only for log table)
			$data['message'] = '0.2'.strtoupper($suffix);
			
			// email to user
			if (SEND_CRON_EMAIL) {
				$mail_err = SendMail($site_lang, $data['email'], $config['site_email'], $data['subject'],
					$data, 'mail_registration_user', null,
					$data['full_name'], $data['sender'], 'registration_2', $user['gender']);
			}
			
			// update cron action table
			if (!$mail_err) {
				$dbconn->Execute('INSERT INTO '.CRON_ACTION_TABLE.' SET id_job = ?, id_user = ?, job_time = NOW()', array($action_id, $user['id']));
			}
			
			// print row
			if ($view == 'table') {
				print_row_test($action_id, $action, $data, $user, false);
			} else {
				print_row_live($action_id, $action, $data);
			}
		}
		/**
		 * 2nd reminder to Signup users to complete their registration, send after 72 hours
		 **/
		elseif (SEND_SIGNUP_0_3 && $user['reg_duration'] > 72 && $user['is_rem_block'] != '1' && not_yet_sent($user['id'], CJ_SIGNUP_0_3_ID))
		{
			// action
			$action_id	= CJ_SIGNUP_0_3_ID;
			$action		= 'registration completion reminder 0.3';
			
			// create login token
			$token = CreateToken($user['id']);
			
			// additional data
			if ($settings['use_registration_confirmation'] && !$data['confirm']) {
				$data['confirm_link'] = $config['server'].$config['site_root'].'/confirm.php?id='.$user['id'].'&login_id='.$user['id'].'&token='.$token;
			} else {
				$data['login_link'] = $config['server'].$config['site_root'].'/index.php?sel=login&login_id='.$user['id'].'&token='.$token;
			}
			
			$data['pass'] = $lang_mail['registration_3'.$suffix]['password_lookup'];
			
			#RS: not in use any longer
			#$data['freecd_link'] = $config['server'].$config['site_root'].'/request_info.php?id='.$user['id'];
			#$data['approve'] = $settings['use_registration_approve'] ? 1 : 0;
			
			// subject
			$data['subject'] = str_replace('[name]', $user['fname'], $lang_mail['registration_3'.$suffix]['subject']);
			
			// message (only for log table)
			$data['message'] = '0.3'.strtoupper($suffix);
			
			// email to user
			if (SEND_CRON_EMAIL) {
				$mail_err = SendMail($site_lang, $data['email'], $config['site_email'], $data['subject'],
					$data, 'mail_registration_user', null,
					$data['full_name'], $data['sender'], 'registration_3', $user['gender']);
			}
			
			// update cron action table
			if (!$mail_err) {
				$dbconn->Execute('INSERT INTO '.CRON_ACTION_TABLE.' SET id_job = ?, id_user = ?, job_time = NOW()', array($action_id, $user['id']));
			}
			
			// print row
			if ($view == 'table') {
				print_row_test($action_id, $action, $data, $user, false);
			} else {
				print_row_live($action_id, $action, $data);
			}
		}
		/**
		 * 3rd reminder to Signup users to complete their registration, send after 7 days = 168 hours
		 **/
		elseif (SEND_SIGNUP_0_4 && $user['reg_duration'] > 168 && $user['is_rem_block'] != '1' && not_yet_sent($user['id'], CJ_SIGNUP_0_4_ID))
		{
			// action
			$action_id	= CJ_SIGNUP_0_4_ID;
			$action		= 'registration completion reminder 0.4';
			
			// create login token
			$token = CreateToken($user['id']);
			
			// additional data
			if ($settings['use_registration_confirmation'] && !$data['confirm']) {
				$data['confirm_link'] = $config['server'].$config['site_root'].'/confirm.php?id='.$user['id'].'&login_id='.$user['id'].'&token='.$token;
			} else {
				$data['login_link'] = $config['server'].$config['site_root'].'/index.php?sel=login&login_id='.$user['id'].'&token='.$token;
			}
			
			$data['pass'] = $lang_mail['registration_4'.$suffix]['password_lookup'];
			
			#RS: not in use any longer
			#$data['freecd_link'] = $config['server'].$config['site_root'].'/request_info.php?id='.$user['id'];
			#$data['approve'] = $settings['use_registration_approve'] ? 1 : 0;
			
			// subject
			$data['subject'] = str_replace('[name]', $user['fname'], $lang_mail['registration_4'.$suffix]['subject']);
			
			// message (only for log table)
			$data['message'] = '0.4'.strtoupper($suffix);
			
			// email to user
			if (SEND_CRON_EMAIL) {
				$mail_err = SendMail($site_lang, $data['email'], $config['site_email'], $data['subject'],
					$data, 'mail_registration_user', null,
					$data['full_name'], $data['sender'], 'registration_4', $user['gender']);
			}
			
			// update cron action table
			if (!$mail_err) {
				$dbconn->Execute('INSERT INTO '.CRON_ACTION_TABLE.' SET id_job = ?, id_user = ?, job_time = NOW()', array($action_id, $user['id']));
			}
			
			// print row
			if ($view == 'table') {
				print_row_test($action_id, $action, $data, $user, false);
			} else {
				print_row_live($action_id, $action, $data);
			}
		}
	}
	
	/**
	 * Ask trial user to become regular
	 **/
	
	if (SEND_BECOME_PAID_MEMBER)
	{
		if ($user['id_group'] == MM_TRIAL_GUY_ID || $user['id_group'] == MM_TRIAL_LADY_ID)
		{
			if ($user['is_rem_block'] != '1')
			{
				list($cron_id, $cron_gap) = get_last_cron_action($user['id'], CJ_BECOME_PAID_MEMBER_ID);
				
				// send only once per week
				if ($cron_gap > 168)
				{
					// action
					$action_id	= CJ_BECOME_PAID_MEMBER_ID;
					$action		= 'become paid member';
					
					// subject
					$data['subject'] = $lang_mail['cron_become_paid'.$suffix]['subject'];
					
					// message
					$data['message'] = $lang_mail['cron_become_paid'.$suffix]['message'];
					
					// email to user
					if (SEND_CRON_EMAIL) {
						$mail_err = SendMail($site_lang, $data['email'], $config['site_email'], $data['subject'],
							$data, 'mail_cron_become_paid_user', null,
							$data['full_name'], $data['sender'], 'cron_become_paid', $user['gender']);
					}
					
					// update cron action table
					if (!$mail_err) {
						if ($cron_id) {
							$dbconn->Execute('UPDATE '.CRON_ACTION_TABLE.' SET id_job = ?, job_time = NOW() WHERE id = ?', array($action_id, $cron_id));
						} else {
							$dbconn->Execute('INSERT INTO '.CRON_ACTION_TABLE.' SET id_job = ?, id_user = ?, job_time = NOW()', array($action_id, $user['id']));
						}
					}
					
					// print row
					if ($view == 'table') {
						print_row_test($action_id, $action, $data, $user, false);
					} else {
						print_row_live($action_id, $action, $data);
					}
				}
			}
		}
	}
	
	/**
	 * Check remaining time of active users
	 * right now, only Regular and Installment Ladies can expire
	 **/
	
	if ($user['id_group'] == MM_REGULAR_LADY_ID
	||  $user['id_group'] == MM_PLATINUM_LADY_FIRST_INS_ID
	||  $user['id_group'] == MM_PLATINUM_LADY_SECOND_INS_ID)
	{
		if (SEND_ONE_WEEK)
		{
			// only one week remaining
			if ($user['now_res'] == 7)
			{
				list($cron_id, $cron_gap) = get_last_cron_action($user['id'], CJ_ONE_WEEK_ID);
				
				// do not send twice per day
				if ($cron_gap > 24)
				{
					// action
					$action_id	= CJ_ONE_WEEK_ID;
					$action		= 'only 7 days remain';
					
					// subject and message
					if ($user['id_group'] == MM_PLATINUM_LADY_FIRST_INS_ID) {
						$data['subject'] = str_replace("[x]", '7', $lang_mail['cron_2nd_ins_expires_t']['subject']);
						$data['message'] = str_replace("[x]", '7', $lang_mail['cron_2nd_ins_expires_t']['message']);
					} elseif ($user['id_group'] == MM_PLATINUM_LADY_SECOND_INS_ID) {
						$data['subject'] = str_replace("[x]", '7', $lang_mail['cron_3rd_ins_expires_t']['subject']);
						$data['message'] = str_replace("[x]", '7', $lang_mail['cron_3rd_ins_expires_t']['message']);
					} else {
						$data['subject'] = $lang_mail['cron_one_week'.$suffix]['subject'];
						$data['message'] = $lang_mail['cron_one_week'.$suffix]['message'];
					}
					
					// email to user
					if (SEND_CRON_EMAIL) {
						$mail_err = SendMail($site_lang, $data['email'], $config['site_email'], $data['subject'],
							$data, 'mail_cron_expires_soon_user', null,
							$data['full_name'], $data['sender'], 'cron_one_week', $user['gender']);
					}
					
					// update cron action table
					if (!$mail_err) {
						if ($cron_id) {
							$dbconn->Execute('UPDATE '.CRON_ACTION_TABLE.' SET id_job = ?, job_time = NOW() WHERE id = ?', array($action_id, $cron_id));
						} else {
							$dbconn->Execute('INSERT INTO '.CRON_ACTION_TABLE.' SET id_job = ?, id_user = ?, job_time = NOW()', array($action_id, $user['id']));
						}
					}
					
					// print row
					if ($view == 'table') {
						print_row_test($action_id, $action, $data, $user, false);
					} else {
						print_row_live($action_id, $action, $data);
					}
					
					// reset the reminder mail block check
					// RS: we might need to review this
					unset($check_exist);
					$check_exist = $dbconn->getOne('SELECT id FROM '.USER_PRIVACY_SETTINGS.' WHERE id_user = ?', array($user['id']));
					
					if (!empty($check_exist)) {
						$dbconn->Execute('UPDATE '.USER_PRIVACY_SETTINGS.' SET is_rem_block = "0" WHERE id_user = ?', array($user['id']));
					} else {
						$dbconn->Execute('INSERT INTO '.USER_PRIVACY_SETTINGS.' SET id_user = ?, is_rem_block = "0"', array($user['id']));
					}
				}
			}
		}
		
		if (SEND_TWO_DAYS)
		{
			// Two days left only
			if ($user['now_res'] == 2)
			{
				list($cron_id, $cron_gap) = get_last_cron_action($user['id'], CJ_TWO_DAYS_ID);
				
				// do not send twice per day
				if ($cron_gap > 24)
				{
					// action
					$action_id	= CJ_TWO_DAYS_ID;
					$action		= 'only 2 days remain';
					
					// subject and message
					if ($user['id_group'] == MM_PLATINUM_LADY_FIRST_INS_ID) {
						$data['subject'] = str_replace("[x]", '2', $lang_mail['cron_2nd_ins_expires_t']['subject']);
						$data['message'] = str_replace("[x]", '2', $lang_mail['cron_2nd_ins_expires_t']['message']);
					} elseif ($user['id_group'] == MM_PLATINUM_LADY_SECOND_INS_ID) {
						$data['subject'] = str_replace("[x]", '2', $lang_mail['cron_3rd_ins_expires_t']['subject']);
						$data['message'] = str_replace("[x]", '2', $lang_mail['cron_3rd_ins_expires_t']['message']);
					} else {
						$data['subject'] = $lang_mail['cron_two_days'.$suffix]['subject'];
						$data['message'] = $lang_mail['cron_two_days'.$suffix]['message'];
					}
					
					// email to user
					if (SEND_CRON_EMAIL) {
						$mail_err = SendMail($site_lang, $data['email'], $config['site_email'], $data['subject'],
							$data, 'mail_cron_expires_soon_user', null,
							$data['full_name'], $data['sender'], 'cron_two_days', $user['gender']);
					}
					
					// update cron action table
					if (!$mail_err) {
						if ($cron_id) {
							$dbconn->Execute('UPDATE '.CRON_ACTION_TABLE.' SET id_job = ?, job_time = NOW() WHERE id = ?', array($action_id, $cron_id));
						} else {
							$dbconn->Execute('INSERT INTO '.CRON_ACTION_TABLE.' SET id_job = ?, id_user = ?, job_time = NOW()', array($action_id, $user['id']));
						}
					}
					
					// print row
					if ($view == 'table') {
						print_row_test($action_id, $action, $data, $user, false);
					} else {
						print_row_live($action_id, $action, $data);
					}
				}
			}
		}
		
		if (SEND_LAST_DAY)
		{
			// Last day only
			if ($user['now_res'] == 1)
			{
				list($cron_id, $cron_gap) = get_last_cron_action($user['id'], CJ_LAST_DAY_ID);
				
				// do not send twice per day
				if ($cron_gap > 24)
				{
					// action
					$action_id	= CJ_LAST_DAY_ID;
					$action		= 'only 1 day remains';
					
					// subject and message
					if ($user['id_group'] == MM_PLATINUM_LADY_FIRST_INS_ID) {
						$data['subject'] = str_replace("[x]", '1', $lang_mail['cron_2nd_ins_expires_t']['subject']);
						$data['message'] = str_replace("[x]", '1', $lang_mail['cron_2nd_ins_expires_t']['message']);
					} elseif ($user['id_group'] == MM_PLATINUM_LADY_SECOND_INS_ID) {
						$data['subject'] = str_replace("[x]", '1', $lang_mail['cron_3rd_ins_expires_t']['subject']);
						$data['message'] = str_replace("[x]", '1', $lang_mail['cron_3rd_ins_expires_t']['message']);
					} else {
						$data['subject'] = $lang_mail['cron_last_day'.$suffix]['subject'];
						$data['message'] = $lang_mail['cron_last_day'.$suffix]['message'];
					}
					
					// email to user
					if (SEND_CRON_EMAIL) {
						$mail_err = SendMail($site_lang, $data['email'], $config['site_email'], $data['subject'],
							$data, 'mail_cron_expires_soon_user', null,
							$data['full_name'], $data['sender'], 'cron_last_day', $user['gender']);
					}
					
					// update cron action table
					if (!$mail_err) {
						if ($cron_id) {
							$dbconn->Execute('UPDATE '.CRON_ACTION_TABLE.' SET id_job = ?, job_time = NOW() WHERE id = ?', array($action_id, $cron_id));
						} else {
							$dbconn->Execute('INSERT INTO '.CRON_ACTION_TABLE.' SET id_job = ?, id_user = ?, job_time = NOW()', array($action_id, $user['id']));
						}
					}
					
					// print row
					if ($view == 'table') {
						print_row_test($action_id, $action, $data, $user, false);
					} else {
						print_row_live($action_id, $action, $data);
					}
				}
			}
		}
		
		if (SEND_ACC_EXPIRED)
		{
			//checking current users session in database
			/*
			$ses_res = $dbconn->Execute('SELECT COUNT(*) FROM '.ACTIVE_SESSIONS_TABLE.' WHERE id_user = ?', array($user['id']));
			$is_session = $ses_res->fields[0] ? $ses_res->fields[0] : false;
			*/
			
			//subscription time end
			if ($user['now_res'] == NULL || $user['now_res'] < 0)
			{
				// reset user (functions_index.php)
				$usr_id = $user['id'];
				$grp_id = $user['id_group'];
				
				//RS: In the new membership model as per 2012/08, nothing happens here, so the function
				//    just returns the current id_group. The account is of course still
				//    expired. Only Lady accounts can expire, as guys have unlimited
				//    membership time. We thus could check the id_group first or further
				//    simplify, but then need to make corrections if the membership model
				//    changes again.
				$new_id_group = ResetUserGroup($usr_id, $grp_id);
				
				if ($user['is_rem_block'] != '1')
				{
					list($cron_id, $cron_gap) = get_last_cron_action($user['id'], CJ_ACC_EXPIRED_ID);
					
					// send only once per week
					if ($cron_gap > 168)
					{
						// action
						$action_id	= CJ_ACC_EXPIRED_ID;
						$action		= 'account has expired';
						
						// subject
						$data['subject'] = $lang_mail['cron_expired'.$suffix]['subject'];
						
						// message
						$data['message'] = $lang_mail['cron_expired'.$suffix]['message'];
						
						// email to user
						if (SEND_CRON_EMAIL) {
							$mail_err = SendMail($site_lang, $data['email'], $config['site_email'], $data['subject'],
								$data, 'mail_cron_expired_user', null,
								$data['full_name'], $data['sender'], 'cron_expired', $user['gender']);
						}
						
						// update cron action table
						if (!$mail_err) {
							if ($cron_id) {
								$dbconn->Execute('UPDATE '.CRON_ACTION_TABLE.' SET id_job = ?, job_time = NOW() WHERE id = ?', array($action_id, $cron_id));
							} else {
								$dbconn->Execute('INSERT INTO '.CRON_ACTION_TABLE.' SET id_job = ?, id_user = ?, job_time = NOW()', array($action_id, $user['id']));
							}
						}
						
						// print row
						if ($view == 'table') {
							print_row_test($action_id, $action, $data, $user, false);
						} else {
							print_row_live($action_id, $action, $data);
						}
					}
				}
			}
		}
	} // user is Regular Lady or Installment Lady
	
	/**
	 * Ask Platinum Lady Pending to rejoin, send every 7 days
	 **/
	
	if (SEND_RE_JOIN_US)
	{
		if ($user['id_group'] == MM_PLATINUM_LADY_PENDING_ID )
		{
			if ($user['is_rem_block'] != '1')
			{
				list($cron_id, $cron_gap) = get_last_cron_action($user['id'], CJ_RE_JOIN_US_ID);
				
				// send only once per week
				if ($cron_gap > 168)
				{
					// action
					$action_id	= CJ_RE_JOIN_US_ID;
					$action		= 'rejoin us';
					
					// subject
					$data['subject'] = $lang_mail['cron_rejoin'.$suffix]['subject'];
					
					// message
					$data['message'] = $lang_mail['cron_rejoin'.$suffix]['message'];
					
					// email to user
					if (SEND_CRON_EMAIL) {
						$mail_err = SendMail($site_lang, $data['email'], $config['site_email'], $data['subject'],
							$data, 'mail_cron_rejoin_user', null,
							$data['full_name'], $data['sender'], 'cron_rejoin', $user['gender']);
					}
					
					// update cron action table
					if (!$mail_err) {
						if ($cron_id) {
							$dbconn->Execute('UPDATE '.CRON_ACTION_TABLE.' SET id_job = ?, job_time = NOW() WHERE id = ?', array($action_id, $cron_id));
						} else {
							$dbconn->Execute('INSERT INTO '.CRON_ACTION_TABLE.' SET id_job = ?, id_user = ?, job_time = NOW()', array($action_id, $user['id']));
						}
					}
					
					// print row
					if ($view == 'table') {
						print_row_test($action_id, $action, $data, false);
					} else {
						print_row_live($action_id, $action, $data);
					}
				}
			}
		}
	}
	
	/**
	 * admin reminder for pending users.
	 * admin needs to activate the user by setting status flag.
	 * not in use, as all users are automatically active
	 **/
	
	if (SEND_APPROVE_USER)
	{
		if ($user['id_group'] == MM_TRIAL_GUY_ID || $user['id_group'] == MM_TRIAL_LADY_ID)
		{
			if ($user['status'] == 0)
			{
				list($cron_id, $cron_gap) = get_last_cron_action($user['id'], CJ_APPROVE_USER_ID);
				
				// send once per day
				if ($cron_gap > 24)
				{
					// action
					$action_id	= CJ_APPROVE_USER_ID;
					$action		= 'user approval reminder TO ADMIN';
					
					// subject
					$data['subject'] = $lang_mail['cron_approve_user_admin']['subject'];
					
					// message
					$data['message'] = $lang_mail['cron_approve_user_admin']['message'];
					
					// recipient
					if (REDIRECT_ADMIN_EMAIL && !IS_LIVE_SERVER) {
						$email_to = REDIRECT_ADMIN_EMAIL_TO;
					} else {
						$email_to = $config['site_email'];
					}
					
					// email to admin
					if (SEND_CRON_EMAIL) {
						$mail_err = SendMail($site_lang, $email_to, $config['site_email'], $data['subject'],
							$data, 'mail_cron_approve_user_admin', null,
							'', $data['sender'], 'cron_approve_user_admin');
					}
					
					// update cron action table
					if (!$mail_err) {
						if ($cron_id) {
							$dbconn->Execute('UPDATE '.CRON_ACTION_TABLE.' SET id_job = ?, job_time = NOW() WHERE id = ?', array($action_id, $cron_id));
						} else {
							$dbconn->Execute('INSERT INTO '.CRON_ACTION_TABLE.' SET id_job = ?, id_user = ?, job_time = NOW()', array($action_id, $user['id']));
						}
					}
					
					// print row
					if ($view == 'table') {
						print_row_test($action_id, $action, $data, $user, true);
					} else {
						print_row_live($action_id, $action, $data);
					}
				}
			}
		}
	}
	
	/**
	 * remind admin to check Platinum Verification
	 **/
	
	if (SEND_PLATINUM_VERIFY)
	{
		if ($user['id_group'] == MM_REGULAR_GUY_ID || $user['id_group'] == MM_REGULAR_LADY_ID
		||  $user['id_group'] == MM_TRIAL_GUY_ID   || $user['id_group'] == MM_TRIAL_LADY_ID)
		{
			if ($user['is_applied'])
			{
				list($cron_id, $cron_gap) = get_last_cron_action($user['id'], CJ_PLATINUM_VERIFY_ID);
				
				// send every two days
				if ($cron_gap > 48)
				{
					// action
					$action_id	= CJ_PLATINUM_VERIFY_ID;
					$action		= 'platinum verification reminder TO ADMIN';
					
					// subject
					$data['subject'] = $lang_mail['cron_platinum_applied_admin']['subject'];
					
					// message
					$data['message'] = $lang_mail['cron_platinum_applied_admin']['message'];
					
					// recipient
					if (REDIRECT_ADMIN_EMAIL && !IS_LIVE_SERVER) {
						$email_to = REDIRECT_ADMIN_EMAIL_TO;
					} else {
						$email_to = $config['site_email'];
					}
					
					// email to admin
					if (SEND_CRON_EMAIL) {
						$mail_err = SendMail($site_lang, $email_to, $config['site_email'], $data['subject'],
							$data, 'mail_cron_platinum_applied_admin', null,
							'', $data['sender'], 'cron_platinum_applied_admin');
					}
					
					// update cron action table
					if (!$mail_err) {
						if ($cron_id) {
							$dbconn->Execute('UPDATE '.CRON_ACTION_TABLE.' SET id_job = ?, job_time = NOW() WHERE id = ?', array($action_id, $cron_id));
						} else {
							$dbconn->Execute('INSERT INTO '.CRON_ACTION_TABLE.' SET id_job = ?, id_user = ?, job_time = NOW()', array($action_id, $user['id']));
						}
					}
					
					// print row
					if ($view == 'table') {
						print_row_test($action_id, $action, $data, $user, true);
					} else {
						print_row_live($action_id, $action, $data);
					}
				}
			}
		}
	}
	
	if ($action_id > 0) {
		$i++;
	} else {
		if ($view == 'table') {
			print_row_test(0, '', $data, $user, false);
		}
	}
	
	if (MAX_EMAIL_SEND > 0 && $i >= MAX_EMAIL_SEND) {
		break;
	}
} // foreach

if ($view == 'table') {
	print_bottom_test();
} else {
	print_bottom_live();
}

exit;

function get_last_cron_action($id_user, $id_job)
{
	global $dbconn;
	
	$rs = $dbconn->Execute(
		'SELECT id, UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(job_time)
		   FROM '.CRON_ACTION_TABLE.'
		  WHERE id_user = ? AND id_job = ?',
		  array($id_user, $id_job));
	if ($rs->EOF) {
		$id		= 0;
		$gap	= 99999;
	} else {
		$id		= (int) $rs->fields[0];
		$gap	= (int) ($rs->fields[1] / 3600);
	}
	$rs->free();
	return array($id, $gap); //return array($id, 25);
}

function not_yet_sent($id_user, $id_job)
{
	global $dbconn;
	$rs = $dbconn->Execute('SELECT id FROM '.CRON_ACTION_TABLE.' WHERE id_user = ? AND id_job = ?', array($id_user, $id_job));
	return $rs->EOF;
}

function print_top_live()
{
	global $view;
	/*
	?>
	<div align="center">
		<table cellpadding="2" cellspacing="0" border="1">
			<tr><td>Login</td><td>Email</td><td>Cron Id</td><td>Action</td></tr>
	<?php
	*/
	$s = 'Cron Job Action Log '.date('Y-m-d h:i:s').":\n\n";
	if ($view == 'text') {
		$s = nl2br($s);
	}
	echo $s;
}

function print_row_live($action_id, $action, $data)
{
	global $view;
	
	if ($action_id > 0) {
		/*
		echo '<tr>';
		echo '<td>'.$data['login'].'</td>';
		echo '<td>'.$data['email'].'</td>';
		echo '<td>'.$action_id.'</td>';
		echo '<td>'.$action.'</td>';
		echo '</tr>';
		*/
		$s = 'Login / ID: '.$data['login'].' / '.$data['id']."\n";
		$s.= 'Email: '.$data['email']."\n";
		$s.= 'Registration: '.$data['date_registration']."\n";
		$s.= 'Group: '.$data['group_name']."\n";
		$s.= 'Period: ';
		if ($data['id_group_period']) {
			$s.= $data['id_group_period'].' => '.$data['id_group_from_period'].' => '.$data['group_name_from_period']."\n";
		} else {
			$s.= "none\n";
		}
		$s.= 'Expiration: ';
		if ($data['id_group_period']) {
			$s.= $data['date_end']."\n";
		} else {
			$s.= "n.a.\n";
		}
		$s.= 'Action: '.$action_id.' = '.$action."\n\n";
		if ($view == 'text') {
			$s = nl2br($s);
		}
		echo $s;
	}
}

function print_bottom_live()
{
	global $view;
	/*
	?>
		</table>
	</div>
	<?php
	*/
	$s = 'Cron Job End '.date('Y-m-d h:i:s');
	if ($view == 'text') {
		$s = nl2br($s);
	}
	echo $s;
}

function print_top_test($count)
{
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>ThaiLadyDateFinder Cron Jobs</title>
	</head>
	<body>
		<div style="padding:7px;" align="center">
			<span>Total Users: <?php echo $count; ?></span>
			<span style="padding-left:50px;"><a href="cron_job.php?action=reset&view=table">truncate cron_job_action table and run again (table view)</a></span>
			<span style="padding-left:50px;"><a href="cron_job.php?action=reset">truncate cron_job_action table and run again (text view)</a></span>
		</div>
		<?php
		if (MAX_EMAIL_SEND > 0) { ?>
			<div style="padding:7px; color:red; font-size:20px;" align="center">
				Stop After <?php echo MAX_EMAIL_SEND; ?> Emails
			</div>
			<?php
		}
		?>
		<div align="center">
			<table cellpadding="2" cellspacing="0" border="1">
				<tr>
					<td align="center">User Id</td>
					<td align="center">Login</td>
					<td align="center">Group Id</td>
					<td>Group Name</td>
					<td>Duration (Hours)</td>
					<td>Days Left</td>
					<td align="center">Block</td>
					<td>Action</td>
					<td>Email</td>
					<td>Subject + Message</td>
				</tr>
	<?php
}

function print_row_test($action_id, $action, $data, $user, $is_admin_reminder)
{
	global $lang_mail, $suffix;
	
	if ($action_id == 0)
	{
		$action = $data['email'] = $data['subject'] = $mail_msg = '&nbsp;';
	}
	else
	{
		if ($is_admin_reminder)
		{
			$mail_msg =
				$lang_mail['generic_e']['hello'].' '.$lang_mail['generic_e']['admin_name'].'<br><br>'.
				$data['subject'].'<br><br>'.
				$data['message'].'<br><br>'.
				$lang_mail['generic_e']['site_regards'];
		}
		else
		{
			$mail_msg = $lang_mail['generic'.$suffix]['hello'].' <b>'.$data['fname'].'</b>,<br><br>';
			$mail_msg.= $data['subject'].'<br><br>';
			$mail_msg.= $data['message'].'<br><br>';
			if ($action_id == CJ_REGISTER_COMPLETE_ID || $action_id == CJ_RE_JOIN_US_ID) {
				$mail_msg .= $lang_mail['generic'.$suffix]['unsubscribe'].'<br>';
				$mail_msg .= '<a target="_blank" href="'.$data['unsubscribe_url'].'">'.$data['unsubscribe_url'].'</a><br><br>';
			}
			$mail_msg .= $lang_mail['generic'.$suffix]['admin_regards'];
		}
	}
	
	$user['now_res'] = $user['now_res'] ? $user['now_res'] : '&nbsp;';
	$user['is_rem_block'] = $user['is_rem_block'] ? $user['is_rem_block'] : '&nbsp;';
	
	echo '<tr>';
	echo '<td align="center">'.$user['id'].'</td>';
	echo '<td align="center">'.$user['login'].'</td>';
	echo '<td align="center">'.$user['id_group'].'</td>';
	echo '<td>'.$user['group_name'].'</td>';
	echo '<td>'.$user['reg_duration'].'</td>';
	echo '<td>'.$user['now_res'].'</td>';
	echo '<td align="center">'.$user['is_rem_block'].'</td>';
	echo '<td>'.$action.'</td>';
	echo '<td>'.$data['email'].'</td>';
	echo '<td align="left">'.$data['subject'].($action_id ? '<hr>' : '').$mail_msg.'</td>';
	echo '</tr>';
}

function print_bottom_test()
{
	?>
			</table>
		</div>
	</body>
	</html>
	<?php
}
	
?>