<?php

/**
* Site Activity Emails Management.
*
* @package DatingPro
* @subpackage Admin Mode
**/

include '../include/config.php';
include_once '../common.php';
include '../include/config_admin.php';
include '../include/functions_auth.php';
include '../include/functions_admin.php';
include '../include/class.phpmailer.php';
include '../include/functions_mail.php';
include $config['path_lang'].'/mail/'.$lang_file;

$auth = auth_user();
login_check($auth);
//IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "site_activity_mail");
$debug = false;

$sel = isset($_REQUEST['sel']) ? $_REQUEST['sel']: '';

//echo $sel;

switch ($sel) {
	case 'view_mail': BrowseMail(); break;
	default: ActivityMailList();
}

exit;


function ActivityMailList($err = '')
{
	global $smarty, $dbconn, $config, $lang, $lang_mail;
	
	AdminMainMenu($lang['admin_menu']['activity_mails']);
	
	$debug = isset($_REQUEST['debug']) ? $_REQUEST['debug'] : false;
	
	//$debug = true;
	$smarty->assign('debug', $debug);

	$rs = $dbconn->Execute('SELECT * FROM '.SITE_ACTIVITY_MAIL_TABLE.' ORDER BY id');
	$i = 0;
	$data = array();
	
	if ($rs->RowCount() > 0)
	{
		while (!$rs->EOF)
		{
			$row = $rs->GetRowAssoc(false);
			$data[$i]['number']			= $i + 1;
			$data[$i]['id']				= $row['id'];
			$data[$i]['sequence']		= $row['sequence'];
			$data[$i]['mail_to']		= $row['mail_to'];
			$data[$i]['title']			= stripslashes($row['title']);
			$data[$i]['template_file']	= $row['template_file'];
			$data[$i]['multi_lang']		= $row['multi_lang'];
			$data[$i]['status']			= $row['status'];
			$data[$i]['viewlink']		= 'admin_site_activity_mail.php?sel=view_mail&mid='.$row['id'];
			
			// get subject
			$subject_var = $row['subject'];
			//echo "<pre>". var_dump($subject_var)."</pre>";
			if ($data[$i]['multi_lang']) {
				$subject_var .= '_e';
			}
			
			switch ($subject_var) {
				case 'mystore_order_status_sender_e':
				case 'mystore_order_status_recipient_e':
					$data[$i]['subject'] = $lang_mail[$subject_var]['subject']['shipped'];
				break;
				case 'cron_2nd_ins_expires_one_day_e':
					$data[$i]['subject'] = str_replace('[x]', '1', $lang_mail['cron_2nd_ins_expires_e']['subject']);
				break;
				case 'cron_2nd_ins_expires_two_days_e':
					$data[$i]['subject'] = str_replace('[x]', '2', $lang_mail['cron_2nd_ins_expires_e']['subject']);
				break;
				case 'cron_2nd_ins_expires_week_e':
					$data[$i]['subject'] = str_replace('[x]', '7', $lang_mail['cron_2nd_ins_expires_e']['subject']);
				break;
				case 'cron_3rd_ins_expires_week_e':
					$data[$i]['subject'] = str_replace('[x]', '7', $lang_mail['cron_3rd_ins_expires_e']['subject']);
					break;
				case 'cron_3rd_ins_expires_one_day_e':
					$data[$i]['subject'] = str_replace('[x]', '1', $lang_mail['cron_3rd_ins_expires_e']['subject']);
				break;
				case 'cron_3rd_ins_expires_two_days_e':
					$data[$i]['subject'] = str_replace('[x]', '2', $lang_mail['cron_3rd_ins_expires_e']['subject']);
				break;
				// better handle with default below
				// there also was some confusion with atm subject being used for wire payment
				// as far as I can say, this are dummy messages anyway because we need them only for Thai Ladies
				// please remove after inspection
				/*
				case 'cron_record_atm_pay_e':
					$data[$i]['subject'] = $lang_mail['cron_record_atm_pay_e']['subject'];
				break;
				case 'cron_approve_atm_pay_e':
					$data[$i]['subject'] = $lang_mail['cron_approve_atm_pay_e']['subject'];
				break;
				case 'cron_record_wire_pay_e':
					$data[$i]['subject'] = $lang_mail['cron_record_atm_pay_e']['subject'];
				break;
				case 'cron_approve_wire_pay_e':
					$data[$i]['subject'] = $lang_mail['cron_approve_atm_pay_e']['subject'];
				break;
				*/
				default:
					$data[$i]['subject'] = $lang_mail[$subject_var]['subject'];
				break;
			}
			
#			if ($data[$i]['subject'] == '') {
#				$data[$i]['subject'] = $lang_mail['generic_e']['default_subject'];
#			}
			
			$data[$i]['subject'] = str_replace('[login]', 'john123', $data[$i]['subject']);
			$data[$i]['subject'] = str_replace('[name]', 'John', $data[$i]['subject']);
			$data[$i]['subject'] = str_replace('[SENDER_NAME]', 'Panida', $data[$i]['subject']);
			$data[$i]['subject'] = str_replace('[ORDER_ID]', '123', $data[$i]['subject']);
			$data[$i]['subject'] = str_replace('[DATE]', date('m/d/Y'), $data[$i]['subject']);
			
			$rs->MoveNext();
			$i++;
		}
		
		$smarty->assign('sistem', $data);
	}
	
	$form['err'] = $err;
	
	$smarty->assign('form', $form);
	$smarty->assign('button', $lang['button']);
	$smarty->assign('header', $lang['activity_mails']);
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_site_activity_mail_list.tpl');
	exit;
}

function BrowseMail($err = '')
{
	global $smarty, $dbconn, $config, $lang, $lang_mail;
	
	$file_name = 'admin_site_activity_mail.php';
	
	AdminMainMenu($lang['admin_menu']['activity_mails']);
	
	$mid	= isset($_REQUEST['mid']) ? $_REQUEST['mid'] : '';
	$gender	= isset($_REQUEST['langid']) ? $_REQUEST['langid'] : GENDER_MALE;
	
	$form['add_hiddens']	= '<input type="hidden" name="mid" id="mid" value="'.$mid.'" />';
	$form['backlink']		= $file_name;
	
	if ($mid > 0)
	{
		$strSQL = 'SELECT mail_to, title, subject, template_file, multi_lang, single_tpl, status FROM '.SITE_ACTIVITY_MAIL_TABLE.' WHERE id = ?';
		$rs = $dbconn->Execute($strSQL, array($mid));
		$row = $rs->GetRowAssoc(false);
		
		$data['mail_to']		= $row['mail_to'];
		$data['title']			= stripslashes($row['title']);
		$data['template_file']	= stripslashes($row['template_file']);
		$data['multi_lang']		= $row['multi_lang'];
		$data['single_tpl']		= $row['single_tpl'];
		$data['status']			= $row['status'];
		
		$subject_var			= stripslashes($row['subject']);
		
		$server['url']			= $config['server'];
		$server['root']			= $config['server'].$config['site_root'];
		$server['img_root']		= $config['server'].$config['site_root'].$config['index_theme_path'].'/images';
		
		$data['server']			= $server;
		
		$data['urls']			= GetUserEmailLinks();
		
		$lore_ipsum = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent ac urna eros, in bibendum velit. Phasellus faucibus elementum sapien a pellentesque.\n\nNulla facilisi. Integer convallis magna sed turpis porta pellentesque imperdiet nisi hendrerit. Sed at erat ac enim rhoncus ultrices sit amet non lacus. Sed ante purus, dictum non posuere nec, scelerisque quis neque.\n\nNullam blandit, velit vitae scelerisque hendrerit, tellus justo tincidunt velit, eleifend pellentesque massa lacus ut tortor.";
		
		$data['question'] = $data['comment'] = $data['comments'] = $data['message'] = $data['description'] = $data['main_thing'] =
		$data['testimonial'] = $data['notes'] = $lore_ipsum;
		
		$rs = $dbconn->Execute('SELECT email FROM '.USERS_TABLE.' WHERE root_user = "1"');
		$data['adminemail']	= $rs->fields[0];
		$rs->free();
		
		// Assigning dummy values
		if ($gender == GENDER_MALE) {
			$data['nick']					= 'Johnny';
			$data['fname']					= 'John';
			$data['sname']					= 'Bravo';
			$data['name']					= 'John Bravo';
			$data['login']					= 'john123';
			$data['pass']					= 'password123';
			$data['email']					= 'johnbravo@testmail.com';
			$data['date_birthday_formatted']= '05/01/1960';
			$data['place_of_birth']			= 'Perth';
			$data['identification_number']	= $data['id_num']	= 'DL4568RR5768';
			$data['identification_type']	= $data['id_type']	= 'Driving Licence';
			$data['home_phone']				= $data['phone']	= '9856986523';
			$data['mobile_phone']			= $data['mobile']	= '1231231234';
			$data['city']					= 'Melbourne';
			$data['region']					= 'Victoria';
			$data['country']				= 'Australia';
			$data['zip_code']				= '3000';
			$data['address_1']				= '123, Beach Rd.';
			$data['calltime']				= $data['best_times'] = '6pm to 7pm Monday to Friday';
			$data['best_time_weekdays']		= '6pm to 7pm';
			$data['best_time_saturdays']	= '3pm to 5pm';
			$data['best_time_sundays']		= 'no';
			$data['interest']				= 'Coming To Bangkok And Meeting Eligible, Prescreened Thai Ladies Through Thai Lady Dating Events';
			$data['marital']				= 'Divorced';
#			$data['icon_path']				= 'dummy_thumb_guy.jpg';
			$data['icon']					= $config['server'].$config['site_root'].'/uploades/icons/dummy_thumb_guy.jpg';
			$data['from_id']				= 'xxx';
#			$data['from_icon']				= 'dummy_thumb_lady.jpg';
			$data['from_icon']				= $config['server'].$config['site_root'].'/uploades/icons/big_dummy_thumb_lady.jpg';
			$data['from_fname']				= 'Panida';
			$data['from_id_country']		= 236;
			$data['from_id_region']			= 3607;
			$data['from_id_city']			= 0;
			$data['from_age']				= '32';
			$data['sender_name']			= 'Panida';							// kissed, hotlisted, invited to connect
			$data['login_to']				= 'panida123';						// my store
			$data['fname_to']				= 'Panida';							// my store
			$data['sname_to']				= 'Butterfly';						// my store
			$data['email_to']				= 'panida.butterfly@testmail.com';	// my store
		} else {
			$data['nick']					= 'Panny';
			$data['fname']					= 'Panida';
			$data['sname']					= 'Butterfly';
			$data['name']					= 'Panida Butterfly';
			$data['login']					= 'panida123';
			$data['pass']					= 'password123';
			$data['email']					= 'panida.butterfly@testmail.com';
			$data['date_birthday_formatted']= '07/14/1980';
			$data['place_of_birth']			= 'Pak Kret';
			$data['identification_number']	= $data['id_num']	= '1122334455';
			$data['identification_type']	= $data['id_type']	= 'ID Card';
			$data['home_phone']				= $data['phone']	= '123123123';
			$data['mobile_phone']			= $data['mobile']	= '456456456';
			$data['city']					= 'Bangkok';
			$data['region']					= 'Krung Thep Maha Nakhon';
			$data['country']				= 'Thailand';
			$data['zip_code']				= '10170';
			$data['address_1']				= '456, Soi Sai Ngam';
			$data['calltime']				= $data['best_times'] = '4pm to 6pm';
			$data['best_time_weekdays']		= '4pm to 6pm';
			$data['best_time_saturdays']	= '2pm to 6pm';
			$data['best_time_sundays']		= 'no';
			$data['interest']				= 'Being A Member Of Thai Lady Date Finder';
			$data['marital']				= 'Single';
#			$data['icon_path']				= 'dummy_thumb_lady.jpg';
			$data['icon']					= $config['server'].$config['site_root'].'/uploades/icons/dummy_thumb_lady.jpg';
			$data['from_id']				= 'xxx';
#			$data['from_icon']				= 'dummy_thumb_guy.jpg';
			$data['from_icon']				= $config['server'].$config['site_root'].'/uploades/icons/big_dummy_thumb_guy.jpg';
			$data['from_fname']				= 'John';
			$data['from_id_country']		= 14;
			$data['from_id_region']			= 0;
			$data['from_id_city']			= 0;
			$data['from_age']				= '51';
			$data['sender_name']			= 'John';					// kissed, hotlisted, invited to connect
			$data['login_to']				= 'john123';				// my store
			$data['fname_to']				= 'John';					// my store
			$data['sname_to']				= 'Bravo';					// my store
			$data['email_to']				= 'johnbravo@testmail.com';	// my store
		}
		
		// base lang
		$_LANG_NEED_ID = array();
		$_LANG_NEED_ID['country'][]	= intval($data['from_id_country']);
		$_LANG_NEED_ID['region'][]	= intval($data['from_id_region']);
		$_LANG_NEED_ID['city'][]	= intval($data['from_id_city']);
		
		$smarty->assign('base_lang', GetBaseLang($_LANG_NEED_ID));
		
		$mail_template = $data['template_file'];
		
		if ($data['multi_lang']) {
			$suffix = ($gender == GENDER_MALE) ? '_e' : '_t';
		} else {
			$suffix = '';
		}
		
		switch ($mail_template)
		{
			case 'mail_registration_user':
				$data['confirm_link'] = $config['server'].$config['site_root'].'/confirm.php?id=xxx&login_id=xxx&token=abcdefghijklmnop';
				if ($subject_var == 'registration_2' || $subject_var == 'registration_3' || $subject_var == 'registration_4') {
					$data['pass'] = $lang_mail['registration_2'.$suffix]['password_lookup'];
				}
			break;
			
			case 'mail_application_submit_admin':
				$data['datetime'] = date('m/d/Y h:i:s');
			break;
			
			case 'mail_status_change_user':
				$data['status']	= 1;
			break;
			
			case 'mail_noti_generic_user':
				$data['from_link'] = $server['root'].'/viewprofile.php?id=xxx&amp;login_id=yyy&amp;token=abcdefghijklmnop';
				switch ($subject_var) {
					case 'hotlisted':
						$data['message']	= $lang_mail['hotlisted'.$suffix]['message'];
						$data['message_sub']= $lang_mail['hotlisted'.$suffix]['message_sub'];
						$data['subject_2']	= $lang_mail['hotlisted'.$suffix]['subject_2'];
					break;
					case 'kissed':
						$data['message']	= $lang_mail['kissed'.$suffix]['message'];
						$data['message_sub']= $lang_mail['kissed'.$suffix]['message_sub'];
						$data['subject_2']	= $lang_mail['kissed'.$suffix]['subject_2'];
					break;
					case 'invited':
						$data['message']	= $lang_mail['invited'.$suffix]['message'];
						$data['message_sub']= $lang_mail['invited'.$suffix]['message_sub'];
						$data['subject_2']	= $lang_mail['invited'.$suffix]['subject_2'];
					break;
					case 'want_to_accept':
						$data['message']	= $lang_mail['want_to_accept'.$suffix]['message'];
						$data['message_sub']= $lang_mail['want_to_accept'.$suffix]['message_sub'];
						$data['subject_2']	= $lang_mail['want_to_accept'.$suffix]['subject_2'];
					break;
					case 'accepted':
						$data['message']	= $lang_mail['accepted'.$suffix]['message'];
						$data['message_sub']= $lang_mail['accepted'.$suffix]['message_sub'];
						$data['subject_2']	= $lang_mail['accepted'.$suffix]['subject_2'];
					break;
					case 'profile_viewed':
						$data['message']	= $lang_mail['profile_viewed'.$suffix]['message'];
						$data['message_sub']= $lang_mail['profile_viewed'.$suffix]['message_sub'];
						$data['subject_2']	= $lang_mail['profile_viewed'.$suffix]['subject_2'];
					break;
					case 'ecard_received':
						$data['message']	= $lang_mail['ecard_received'.$suffix]['message'];
						$data['message_sub']= $lang_mail['ecard_received'.$suffix]['message_sub'];
						$data['message_sub']= str_replace('[READ_LINK]', $server['root'].'/mailbox.php?sel=viewto&amp;id=xxx&amp;login_id=yyy&amp;token=abcdefghijklmnop', $data['message_sub']);
						$data['subject_2']	= $lang_mail['ecard_received'.$suffix]['subject_2'];
					break;
					case 'ecard_viewed':
						$data['message']	= $lang_mail['ecard_viewed'.$suffix]['message'];
						$data['message_sub']= $lang_mail['ecard_viewed'.$suffix]['message_sub'];
						$data['subject_2']	= $lang_mail['ecard_viewed'.$suffix]['subject_2'];
					break;
				}
				$data['message'] = str_replace('[SENDER_NAME]', $data['sender_name'], $data['message']);
			break;
			
			case 'mail_noti_simple_generic_user':
				switch ($subject_var) {
					case 'offline_payment_sent':
						$data['message'] = $lang_mail['offline_payment_sent'.$suffix]['message']['atm_payment'];
						$data['message'] = str_replace('[userpayment]', '$99.00', $data['message']);
					break;
					case 'offline_payment_approval':
						$data['message'] = $lang_mail['offline_payment_approval'.$suffix]['message']['atm_payment'];
						$data['message'] = str_replace('[userpayment]', '$99.00', $data['message']);
					break;
					case 'platinum_match_application':
						$data['message'] = $lang_mail['platinum_match_application'.$suffix]['message'];
					break;
					case 'mystore_credits_payment':
						$data['message'] = $lang_mail['mystore_credits_payment'.$suffix]['message'];
					break;
					case 'mystore_offline_payment':
						$data['message'] = $lang_mail['mystore_offline_payment'.$suffix]['message']['wire_transfer'];
					break;
					case 'mystore_offline_payment_approval':
						$data['message'] = $lang_mail['mystore_offline_payment_approval'.$suffix]['message']['wire_transfer'];
					break;
					case 'mystore_online_payment':
						$data['message'] = $lang_mail['mystore_online_payment'.$suffix]['message'];
					break;
					case 'mystore_order_status_sender':
						$data['subject'] = $lang_mail['mystore_order_status_sender'.$suffix]['subject']['shipped'];
						$data['message'] = $lang_mail['mystore_order_status_sender'.$suffix]['message']['shipped'];
					break;
					case 'mystore_order_status_recipient':
						$data['subject'] = $lang_mail['mystore_order_status_recipient'.$suffix]['subject']['shipped'];
						$data['message'] = $lang_mail['mystore_order_status_recipient'.$suffix]['message']['shipped'];
					break;
					case 'approve_upload':
						$data['subject'] = $lang_mail['approve_upload'.$suffix]['subject'];
						$data['message'] = $lang_mail['approve_upload'.$suffix]['message'];
					break;
					case 'cron_record_atm_pay':
					$data['message'] = $lang_mail['cron_record_atm_pay_t']['message'];
					break;
					case 'cron_approve_atm_pay':
						$data['message'] = $lang_mail['cron_approve_atm_pay_t']['message'];
					break;
					case 'cron_record_wire_pay':
						$data['message'] = $lang_mail['cron_record_wire_pay_t']['message'];
					break;
					case 'cron_approve_wire_pay':
						$data['message'] = $lang_mail['cron_approve_wire_pay_t']['message'];
					break;
					case 'admin_communicate':
						$data['message'] = nl2br($lore_ipsum);
					break;
				}
			break;
			
			case 'mail_offline_payment_sent_admin':
				$data['message'] = $lang_mail['offline_payment_sent_admin']['message']['atm_payment'];
				$data['message'] = str_replace('[userpayment]', '$99.00', $data['message']);
			break;
			
			case 'mail_password_changed_user':
				$data['new_pass'] = 'password123';
			break;
			
			case 'mail_dating_video_comment_user':
			case 'mail_dating_video_comment_admin':
				$data['video'] = 'What\'s The Attraction Between Thai &amp; Western Couples?';
			break;
			
			case 'mail_express_interest_user':
				$data['adminemail'] = EMAIL_EXPRESS_INTEREST;
			break;
			
			case 'mail_contact_us_admin':
				$data['subject'] = 'This is my subject line ...';
			break;
			
			case 'mail_send_feedback_admin':
				$data['question_1'] = $data['question_2'] = $data['question_3'] = $data['question_4'] = 5;
				$data['question_5'] = 'Yes';
			break;
			
			case 'mail_send_testimonial_admin':
				$data['date'] = date('m/d/Y');
			break;
			
			case 'mail_mystore_wish_admin':
			case 'mail_mystore_wish_user':
				$data['product_name'] = 'Love Potion No. 9';
			break;
			
			case 'mail_mystore_credits_payment_admin':
				$data['message'] = str_replace('[ORDER_ID]', '123', $lang_mail['mystore_credits_payment_admin']['message']);
				$data['order_id'] = '123';
				$data['amount'] = '$99.99';
			break;
			
			case 'mail_mystore_offline_payment_admin':
				$data['message'] = str_replace('[ORDER_ID]', '123', $lang_mail['mystore_offline_payment_admin']['message']['atm_payment']);
				$data['order_id'] = '123';
				$data['amount'] = '$99.99';
			break;
			
			case 'mail_mystore_online_payment_admin':
				$data['message'] = str_replace('[ORDER_ID]', '123', $lang_mail['mystore_online_payment_admin']['message']);
				$data['order_id'] = '123';
				$data['amount'] = '$99.99';
			break;
			
			case 'mail_cron_registration_complete_user':
				$data['message'] = $lang_mail['cron_registration_complete'.$suffix]['message'];
				$unsubscribe_userid = rand(11111,99999).'xxx'.rand(11111,99999);
				$data['unsubscribe_url'] = $config['server'].'/reminder_block.php?uid='.$unsubscribe_userid;
			break;
			
			case 'mail_cron_rejoin_user':
				$data['message'] = $lang_mail['cron_rejoin'.$suffix]['message'];
				$unsubscribe_userid = rand(11111,99999).'xxx'.rand(11111,99999);
				$data['unsubscribe_url'] = $config['server'].'/reminder_block.php?uid='.$unsubscribe_userid;
			break;
			
			case 'mail_cron_expires_soon_user':
				
				switch ($subject_var) {
				
					case 'cron_one_week':
						$data['message'] = $lang_mail['cron_one_week'.$suffix]['message'];
					break;
					case 'cron_two_days':
						$data['message'] = $lang_mail['cron_two_days'.$suffix]['message'];
					break;
					case 'cron_last_day':
						$data['message'] = $lang_mail['cron_last_day'.$suffix]['message'];
					break;
					case 'cron_2nd_ins_expires_week':
						$data['message'] = str_replace("[x]", '7', $lang_mail['cron_2nd_ins_expires'.$suffix]['message']);
						$data['subject'] = str_replace("[x]", '7', $lang_mail['cron_2nd_ins_expires'.$suffix]['subject']);
					break;
					case 'cron_2nd_ins_expires_two_days':
						$data['message'] = str_replace("[x]", '2', $lang_mail['cron_2nd_ins_expires'.$suffix]['message']);
						$data['subject'] = str_replace("[x]", '2', $lang_mail['cron_2nd_ins_expires'.$suffix]['subject']);
					break;
					case 'cron_2nd_ins_expires_one_day':
						$data['message'] = str_replace("[x]", '1', $lang_mail['cron_2nd_ins_expires'.$suffix]['message']);
						$data['subject'] = str_replace("[x]", '1', $lang_mail['cron_2nd_ins_expires'.$suffix]['subject']);
						
					break;
					case 'cron_2nd_ins_expires_today':
						$data['message'] = str_replace("[x]", '1', $lang_mail['cron_2nd_ins_expires_today'.$suffix]['message']);
						$data['subject'] = str_replace("[x]", '1', $lang_mail['cron_2nd_ins_expires_today'.$suffix]['subject']);
						
					break;
					case 'cron_3rd_ins_expires_week':
						$data['message'] = str_replace("[x]", '7', $lang_mail['cron_3rd_ins_expires'.$suffix]['message']);
						$data['subject'] = str_replace("[x]", '7', $lang_mail['cron_3rd_ins_expires'.$suffix]['subject']);
					break;
					case 'cron_3rd_ins_expires_two_days':
						$data['message'] = str_replace("[x]", '2', $lang_mail['cron_3rd_ins_expires'.$suffix]['message']);
						$data['subject'] = str_replace("[x]", '2', $lang_mail['cron_3rd_ins_expires'.$suffix]['subject']);
					break;
					case 'cron_3rd_ins_expires_one_day':
						$data['message'] = str_replace("[x]", '1', $lang_mail['cron_3rd_ins_expires'.$suffix]['message']);
						$data['subject'] = str_replace("[x]", '1', $lang_mail['cron_3rd_ins_expires'.$suffix]['subject']);
						
					break;
					case 'cron_3rd_ins_expires_today':
						$data['message'] = str_replace("[x]", '1', $lang_mail['cron_3rd_ins_expires_today'.$suffix]['message']);
						$data['subject'] = str_replace("[x]", '1', $lang_mail['cron_3rd_ins_expires_today'.$suffix]['subject']);
						
					break;
				}
			break;
			
			case 'mail_cron_expired_user':
				$data['message'] = $lang_mail['cron_expired'.$suffix]['message'];
			break;
			
			case 'mail_mailbox_subscribe_user':
				if (stripos($data['title'], 'admin') !== false) {
					$data['from_fname'] = 'Admin';
					unset($data['from_id_country'], $data['from_id_region'], $data['from_id_city'], $data['from_age']);
				}
				$data['link_viewprofile'] = $server['root'].'/viewprofile.php?id=xxx&amp;login_id=yyy&amp;token=abcdefghijklmnop';
				$data['link_read'] = $server['root'].'/mailbox.php?sel=inbox&amp;login_id=xxx&amp;token=abcdefghijklmnop';
			break;
		}
		
		if ($data['multi_lang']) {
			if ($gender == GENDER_MALE) {
				if (!$data['single_tpl']) {
					$mail_template .= '_eng';
				}
				$subject_var .= '_e';
			} else {
				if (!$data['single_tpl']) {
					$mail_template .= '_thai';
				}
				$subject_var .= '_t';
			}
		}
		
		$smarty->assign('mail_template', $mail_template);
		
		if (!isset($data['subject'])) {
			$data['subject'] = $lang_mail[$subject_var]['subject'];
#			if ($data['subject'] == '') {
#				$data['subject'] = $lang_mail['generic_e']['default_subject'];
#			}
		}
		
		// replace in subject
		$data['subject'] = str_replace('[login]', $data['login'], $data['subject']);
		$data['subject'] = str_replace('[name]', $data['fname'], $data['subject']);
		$data['subject'] = str_replace('[SENDER_NAME]', $data['sender_name'], $data['subject']);
		$data['subject'] = str_replace('[ORDER_ID]', '123', $data['subject']);
		$data['subject'] = str_replace('[DATE]', date('m/d/Y'), $data['subject']);
		
		// replace in message
		if (isset($data['message'])) {
			$data['message'] = str_replace('[username]', $data['login'], $data['message']);
		}
		
		// correction for 'status_change'
		if ($subject_var == 'status_change_on_e' || $subject_var == 'status_change_off_e') {
			$subject_var = 'status_change_e';
		} elseif ($subject_var == 'status_change_on_t' || $subject_var == 'status_change_off_t') {
			$subject_var = 'status_change_t';
		}
		
		// print $subject_var;
		
		$smarty->assign('sistem', $data);
		$smarty->assign('form', $form);
		$smarty->assign('data', $data);
		$smarty->assign('gender', $gender);
		$smarty->assign('header_top', $lang['activity_mails']);
		$smarty->assign('header', $lang_mail); // old emails
		$smarty->assign('mail_main', $lang_mail[ $subject_var ]);
		
		if ($data['multi_lang']) {
			if ($gender == GENDER_MALE) {
				$smarty->assign('mail_generic', $lang_mail['generic_e']);
			} else {
				$smarty->assign('mail_generic', $lang_mail['generic_t']);
			}
		} else {
			$smarty->assign('mail_generic', $lang_mail['generic_e']);
		}
		$smarty->assign('mail_css', $smarty->fetch(TrimSlash($config['index_theme_path']).'/css/mail.css'));
		$smarty->assign('button', $lang['button']);
		
		$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_site_activity_mail.tpl');
		exit;
	}
	else
	{
		$err = 'Invalid Activity Mail id';
		ActivityMailList($err);
	}
}
?>