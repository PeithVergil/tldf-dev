<?php
/**
 * Manage users account page (change profile info or password, activate/deactivate profile, view billing info)
 *
 * @package DatingPro
 * @subpackage User Mode
 **/

require_once './include/config.php';
require_once './common.php';
require_once './include/config_index.php';
require_once './include/functions_auth.php';
require_once './include/functions_index.php';
require_once './include/class.lang.php';
require_once './include/class.phpmailer.php';
require_once './include/functions_mail.php';
require_once './include/functions_newsletter.php';
require_once './include/functions_forum.php';
require_once './include/functions_mm.php';

// authentication
$user = auth_index_user();

if (empty($user) || $user == 'err' || empty($user[ AUTH_ID_USER ])) {
	header('location: '.$config['site_root'].'/index.php');
	exit;
}

// check guest
if ($user[ AUTH_GUEST ]) {
	AlertPage(GetRightModulePath(__FILE__));
}

// check group, period, expiration
RefreshAccount();

// check status
// (moved to dispatcher, as it depends on the user action)

// check permissions
// (public access)

// alerts and statistics
if (!$user[ AUTH_GUEST ]) {
	GetAlertsMessage();
	SetModuleStatistic(GetRightModulePath(__FILE__));
}

// active menu item
$smarty->assign('sub_menu_num', '');

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

// dispatcher
switch ($sel) {
	case 'bl':
		// no access from signup sandbox
		if (!$user[ AUTH_STATUS ]) {
			AlertPage(GetRightModulePath(__FILE__));
			exit;
		}
		if ($user[ AUTH_IS_APPLICANT ]) {
			echo '<script>location.href="./account.php";</script>';
			exit;
		}
		DeleteFromBlackList(intval($_GET['id']));
	break;
		
	case 'passw':
		ChangePasswForm();
	break;
		
	case 'passw_change':
		ChangePassw();
	break;
		
	case 'subscr':
		// no access from signup sandbox
		if (!$user[ AUTH_STATUS ]) {
			AlertPage(GetRightModulePath(__FILE__));
			return;
		}
		if ($user[ AUTH_IS_APPLICANT ]) {
			echo '<script>location.href="./account.php";</script>';
			exit;
		}
		ChangeSubscribe();
	break;
		
	case 'alerts':
		// no access from signup sandbox
		if (!$user[ AUTH_STATUS ]) {
			AlertPage(GetRightModulePath(__FILE__));
			return;
		}
		if ($user[ AUTH_IS_APPLICANT ]) {
			echo '<script>location.href="./account.php";</script>';
			exit;
		}
		ChangeAlerts();
	break;
		
	case 'visible_change':
		// no access from signup sandbox
		if (!$user[ AUTH_STATUS ]) {
			AlertPage(GetRightModulePath(__FILE__));
			return;
		}
		if ($user[ AUTH_IS_APPLICANT ]) {
			echo '<script>location.href="./account.php";</script>';
			exit;
		}
		ChangeVisible();
	break;
		
	case 'delete':
		DeleteProfile();
	break;
		
	default:
		AccountPage();
	break;
}

exit;


function AccountPage($err='')
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	if (isset($_POST['err'])) {
		$err .= '<br /><br />'.$_POST['err'];
	}
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage($lang['account']);
	GetActiveUserInfo($user);
	
	$id_user = $user[ AUTH_ID_USER ];
	
	// settings
	$settings = GetSiteSettings(array('site_unit_costunit', 'icon_male_default', 'icon_female_default', 
		'icons_folder', 'photo_max_count', 'audio_max_count', 'video_max_count', 'free_site', 
		'use_hide_profile_feature', 'use_refer_friend_feature','refer_friend_price', 'must_approve_payment_before_verify'));
	
	$data['account_currency'] = $settings['site_unit_costunit'];
	$data['use_hide_profile_feature'] = $settings['use_hide_profile_feature'];
	
	// check offline payment pending
	
	/**
	 * check for Platinum Matching offline payment
	 **/
	
	if ($user[ AUTH_IS_TRIAL ] || $user[ AUTH_IS_REGULAR ])
	{
		$id_group = ($user[ AUTH_GENDER ] == GENDER_MALE) ? MM_PLATINUM_GUY_ID : MM_PLATINUM_LADY_ID;
		
		$strSQL =
			'SELECT paysystem
			   FROM '.BILLING_REQUESTS_TABLE.'
			  WHERE id_user = ? AND id_group = ? AND status = "send" AND paysystem IN ("atm_payment", "wire_transfer", "bank_cheque")';
		
		$data['offline_paysystem_platinum_send'] = $dbconn->getOne($strSQL, array($id_user, $id_group));
		
		$strSQL =
			'SELECT paysystem
			   FROM '.BILLING_REQUESTS_TABLE.'
			  WHERE id_user = ? AND id_group = ? AND status = "approve" AND paysystem IN ("atm_payment", "wire_transfer", "bank_cheque")';
		
		$data['offline_paysystem_platinum_approve'] = $dbconn->getOne($strSQL, array($id_user, $id_group));
	}
	
	/**
	 * check for Regular membership offline payment (Ladies only)
	 * can be either upgrade from Trial of buying more days when Regular or Platinum
	 **/
	
	if ($user[ AUTH_GENDER ] == GENDER_FEMALE)
	{
		$strSQL =
			'SELECT paysystem
			   FROM '.BILLING_REQUESTS_TABLE.'
			  WHERE id_user = ? AND id_group = ? AND status = "send" AND paysystem IN ("atm_payment", "wire_transfer", "bank_cheque")';
		
		$paysystem = $dbconn->getOne($strSQL, array($id_user, MM_REGULAR_LADY_ID));
		
		if ($user[ AUTH_IS_TRIAL ]) {
			$data['offline_paysystem_regular_upgrade_send'] = $paysystem;
		} else {
			$data['offline_paysystem_buy_days_send'] = $paysystem;
		}
	}
	
	/**
	 * check for Credit Points offline payment (Guys only)
	 **/
	
	if ($user[ AUTH_GENDER ] == GENDER_MALE)
	{
		$strSQL =
			'SELECT paysystem
			   FROM '.BILLING_REQUESTS_TABLE.'
			  WHERE id_user = ? AND id_group IN ('.PG_SINGLE_CREDIT_POINTS.','.PG_CREDIT_POINTS_PACK.')
			    AND status = "send" AND paysystem IN ("atm_payment", "wire_transfer", "bank_cheque")';
		
		$paysystem = $dbconn->getOne($strSQL, array($id_user));
		
		$data['offline_paysystem_credit_points_send'] = $paysystem;
	}
	
	// check for platinum paid
	//$data['platinum_paid'] = CheckIsPlatinumPaid();
	
	
	$data['platinum_paid'] = false;
	$data['onInstallments'] = false;
	
	$installment = getInstallmentCnt();
	switch ($installment) {
		case "paid":
			$data['platinum_paid'] = true;
			$data['allPaid'] = true;
		break;
		case 1:
			$data['installment'] = "Pay in Installments";
			$data['onInstallments'] = true;
		break;
		case 2:
			$data['installment'] = "Pay 2nd Installment";
			$data['onInstallments'] = true;
		break;
		case 3:
			$data['installment'] = "Pay 3rd Installment";
			$data['onInstallments'] = true;
		break;
	}
	
	if (isset($_GET['from']) && $_GET['from'] == 'check_platinum_payment' && $data['platinum_paid']) {
		$err .= $lang['account']['platinum_paid_info_text'];
	}
	
	// RS: check payment cancel and payment success when returning from payment gateway or after submitting an offline payment
	// ecards and mystore return to their own pages
	if (isset($_GET['from']) && $_GET['from'] == 'payment')
	{
		if (!empty($_GET['cancel']))
		{
			$err .= $lang['account']['online_payment_cancel_msg'];
		}
		elseif (!empty($_GET['id']))
		{
			// filter by id_user so a user can only look up a request id which belong to him
			$rs = $dbconn->execute(
				'SELECT paysystem, status, id_group, id_product
				   FROM '.BILLING_REQUESTS_TABLE.'
				  WHERE id_user = ? AND id = ?',
				array($id_user, $_GET['id']));
			$row = $rs->GetRowAssoc(false);
			
			if (!empty($row['paysystem']))
			{
				if ($row['status'] == 'send' && stripos('atm_payment,wire_transfer,bank_cheque', $row['paysystem']) !== false)
				{
					// ofline payment is pending
					switch ($row['id_group']) {
						case PG_SINGLE_CREDIT_POINTS:
							$err .= $lang['err']['credit_points_payment_success_offline'][$row['paysystem']];
							$err .= '<br><br>'.$lang['err']['credit_points_offline_payment_wait_for_confirmation'];
						break;
						case PG_CREDIT_POINTS_PACK:
							$err .= $lang['err']['credit_pack_payment_success_offline'][$row['paysystem']];
							$err .= '<br><br>'.$lang['err']['credit_pack_offline_payment_wait_for_confirmation'];
						break;
						default:
							if ($row['id_group'] > 0) {
								if ($row['id_product'] == MM_PLATINUM_GUY_PERIOD_ID || $row['id_product'] == MM_PLATINUM_LADY_PERIOD_ID) {
									$err .= $lang['err']['platinum_payment_success_offline'][$row['paysystem']];
									if ($settings['must_approve_payment_before_verify'] == '1') {
										// show stopper
										$err .= '<br><br>'.$lang['err']['offline_payment_wait_for_platinum_submit'];
									} else {
										$err .= '<br><br>'.$lang['err']['offline_payment_continue_platinum_application'];
									}
								} else {
									$err .= $lang['err']['membership_payment_success_offline'][$row['paysystem']];
									$err .= '<br><br>'.$lang['err']['membership_offline_payment_wait_for_confirmation'];
								}
							}
						break;
					}
				}
				elseif (($row['status'] == 'approve' || $row['status'] == 'subscr_signup') && $row['paysystem'] == 'paypal')
				{
					// paypal success
					switch ($row['id_group']) {
						case PG_SINGLE_CREDIT_POINTS:
							$err .= $lang['err']['credit_points_payment_success'];
						break;
						case PG_CREDIT_POINTS_PACK:
							$err .= $lang['err']['credit_pack_payment_success'];
						break;
						default:
							if ($row['id_group'] > 0) {
								if ($row['id_product'] == MM_PLATINUM_GUY_PERIOD_ID || $row['id_product'] == MM_PLATINUM_LADY_PERIOD_ID) {
									$err .= $lang['err']['platinum_payment_success'];
								} else {
									$err .= $lang['err']['membership_payment_success'];
								}
							}
						break;
					}
				}
				elseif ($row['status'] != 'approve')
				{
					// request found but not a success
					switch ($row['id_group']) {
						case PG_SINGLE_CREDIT_POINTS:
							$err .= $lang['err']['credit_points_payment_failure'];
						break;
						case PG_CREDIT_POINTS_PACK:
							$err .= $lang['err']['credit_pack_payment_failure'];
						break;
						default:
							if ($row['id_group'] > 0) {
								if ($row['id_product'] == MM_PLATINUM_GUY_PERIOD_ID || $row['id_product'] == MM_PLATINUM_LADY_PERIOD_ID) {
									$err .= $lang['err']['platinum_payment_failure'];
								} else {
									$err .= $lang['err']['membership_payment_failure'];
								}
							}
						break;
					}
				}
				// else do not show a message
				// this includes approved offline payments, as offline payments are not approved immediately
			}
		}
	}
	
	if ($err) {
		$form['err'] = $err;
	}
	
	// login, email, visible
	$rs = $dbconn->Execute('SELECT login, email, visible FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
	$row = $rs->GetRowAssoc(false);
	
	$data['login'] = $row['login'];
	$data['email'] = $row['email'];
	$status = intval($row['visible']) ? 'hide' : 'public';
	
	unset($rs, $row);
	
	$data['info_switch'] = $lang['account']['info_'.$status];
	$data['info_switch_comment'] = $lang['account']['info_'.$status.'_comment'];
	
	
	$data['user_group'] = $dbconn->getOne('SELECT id_group FROM '.USER_GROUP_TABLE.' WHERE id_user = ?', array($id_user));
	
	$platinum_array = array(
						MM_PLATINUM_LADY_FIRST_INS_ID,
						MM_PLATINUM_LADY_SECOND_INS_ID,
						MM_PLATINUM_LADY_ID);
	
	if (in_array($data['user_group'], $platinum_array)) {
		$data['count'] = 'n/a';
		$data['account_currency'] = '';
	} else {
		$data['count'] = $dbconn->GetOne('SELECT account_curr FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($id_user));
		$data['count'] = number_format(round($data['count'], 2), 2);
	}
	
	// current groups
	$strSQL =
		'SELECT a.name, b.id_group
		   FROM '.USER_GROUP_TABLE.' b
	  LEFT JOIN '.GROUPS_TABLE.' a ON a.id = b.id_group
		  WHERE b.id_user = ?';
	$rs = $dbconn->Execute($strSQL, array($id_user));
	
	$groups_arr = array();
	$id_groups_arr = array();
	
	while (!$rs->EOF) {
		$groups_arr[] = $rs->fields[0];
		$id_groups_arr[] = $rs->fields[1];
		$rs->MoveNext();
	}
	
	unset($rs);
	
	if (is_array($groups_arr) && count($groups_arr) > 0)
	{
		$data['groups'] = implode('<br>', $groups_arr);
		$data['id_groups'] = implode('<br>', $id_groups_arr);
		
		if ($data['id_groups'] == MM_REGULAR_GUY_ID || $data['id_groups'] == MM_REGULAR_LADY_ID
		|| $data['id_groups'] == MM_TRIAL_GUY_ID || $data['id_groups'] == MM_TRIAL_LADY_ID)
		{
			//VP fetching Platinum Applied status
			$date_apply = $dbconn->GetOne('SELECT mm_platinum_applied FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
			$is_platinum_applied = !empty($date_apply);
			$data['is_platinum_applied'] = $is_platinum_applied;
			
			if (!$user[ AUTH_STATUS ] && !$is_platinum_applied)
			{
				$data['groups'] .= ' ('. $lang['users']['pending'].')';
			}
			elseif ($is_platinum_applied)
			{
				$data['groups'] .= ' ('. $lang['users']['platinum_applied'].')';
			}
		}
	}
	
	$alert_expiration = 7; // days
	$dbconn->Execute('DELETE FROM '.ACCOUNT_ALERTS_TABLE.' WHERE YEAR(date_read) > 0 AND DATEDIFF(NOW(), date_read) > ?', array($alert_expiration));
	
	$strSQL =
		'SELECT id, subject, body, DATE_FORMAT(date_add, "'.$config['date_format'].'") AS date_add
		   FROM '.ACCOUNT_ALERTS_TABLE.'
		  WHERE id_user = ? AND (YEAR(date_read) = 0 OR DATEDIFF(NOW(), date_read) <= ?)
	   ORDER BY id DESC';
	$rs = $dbconn->Execute($strSQL, array($id_user, $alert_expiration));
	
	$alerts_arr = array();
	$alerts = array();
	$i = 0;
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$alerts[$i]['text'] = stripslashes($row['body']);
		$alerts[$i]['type'] = stripslashes($row['subject']);
		$alerts[$i]['date'] = $row['date_add'];
		$alerts_arr[] = $row['id'];
		$rs->MoveNext();
		$i++;
	}
	
	$rs->free();
	
	if (count($alerts_arr) > 0) {
		$dbconn->Execute('UPDATE '.ACCOUNT_ALERTS_TABLE.' SET date_read = NOW() WHERE id IN ('.implode(',', $alerts_arr).') AND YEAR(date_read) = 0');
	}
	
	$smarty->assign('alerts', $alerts);
	
	// email subscribes
	$rs = $dbconn->Execute(
		'SELECT DISTINCT a.id, b.id_user AS sel 
		   FROM '.SUBSCRIBE_SISTEM_TABLE.' a 
	  LEFT JOIN '.SUBSCRIBE_USER_TABLE.' b ON b.id_user = ? AND b.id_subscribe = a.id AND b.type = "s"  
		  WHERE a.status = "1"',
		array($id_user));
	
	$my_alerts_email = array();
	$ie = 0;
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$my_alerts_email[$ie]['id'] = $row['id'];
		$my_alerts_email[$ie]['name'] = $lang['subcribe']['alert_'.$row['id']];
		$my_alerts_email[$ie]['check'] = $row['sel'] ? '1' : '0';
		$ie++;
		$rs->MoveNext();
	}
	
	$rs->free();
	
	$smarty->assign('my_alerts_email', $my_alerts_email);
	
	// admin subscribes / function from functions_newsletter.php
	$smarty->assign('adm_subscr', GetSiteSubscribeForUser($id_user));
	
	$form['free_site'] = ($settings['free_site']) ? true : false;
	
	if ($settings['use_refer_friend_feature'])
	{
		$code = GetUserReferCode($id_user);
		
		if ($code)
		{
			$count_referred = GetCountReferredFriends($id_user);
			
			$count_friend_link = '<a href="'.$config['server'].$config['site_root'].'/quick_search.php?sel=search_referred">'.$count_referred.'</a>';
			
			$count_money = $dbconn->GetOne('SELECT SUM(amount) FROM '.BILLING_ENTRY_TABLE.' WHERE entry_type = "refer_friend" AND id_user = ?', array($id_user));
			$count_money = round($count_money, 2);
			
			$search = array('[count_friend_link]','[n_money]','[curr]');
			$repl = array($count_friend_link,$count_money,$settings['site_unit_costunit']);
			$refer_comment = str_replace($search, $repl, $lang['refer_friend']['account_positive_comment']);
		}
		else
		{
			/*VP
			$b_link = '<a href="'.$config['server'].$config['site_root'].'/tell_friend.php">';
			
			$search = array('[link]','[/link]','[n_money]','[curr]');
			$repl = array($b_link, '</a>', $settings['refer_friend_price'],$settings['site_unit_costunit']);
			$refer_comment = str_replace($search, $repl, $lang['refer_friend']['account_negotive_comment']);
			*/
		}
		
		$smarty->assign('refer_comment', $refer_comment);
	}
	
	#print_r($data);
	$smarty->assign('form', $form);
	$smarty->assign('data', $data);
	$smarty->assign('section', $lang['section']);
	$smarty->display(TrimSlash($config['index_theme_path']).'/account_table.tpl');
	exit;
}

function ChangePasswForm($err='')
{
	global $lang, $config, $smarty, $user;
	
	$from = isset($_POST['from']) ? $_POST['from'] : (isset($_GET['from']) ? $_GET['from'] : '');
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage($lang['account']);
	GetActiveUserInfo($user);
	
	$form['err'] = $err;
	$form['from'] = $from;
	
	$smarty->assign('form', $form);
	$smarty->display(TrimSlash($config['index_theme_path']).'/account_passw_form.tpl');
	
	exit;
}

function ChangePassw()
{
	global $lang, $config, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$from = isset($_POST['from']) ? $_POST['from'] : (isset($_GET['from']) ? $_GET['from'] : '');
	
	$oldpassw	= $_POST['oldpassw'];
	$passw		= $_POST['passw'];
	$repassw	= $_POST['repassw'];
	
	if (!strlen($oldpassw)) {
		ChangePasswForm($lang['err']['empty_oldpass']);
		return;
	}
	
	if (!strlen($passw) || !strlen($repassw)) {
		ChangePasswForm($lang['err']['empty_pass']);
		return;
	}
	
	if (strval($passw) != strval($repassw)) {
		ChangePasswForm($lang['err']['pass_eq_repass']);
		return;
	}
	
	$err = PasswFilter($passw);
	
	if ($err) {
		ChangePasswForm($err); 
		return;
	}
	
	$strSQL = 'SELECT login, email, fname, sname, site_language FROM '.USERS_TABLE.' WHERE password = ? AND id = ?';
	
	$rs = $dbconn->Execute($strSQL, array(md5($oldpassw), $id_user));
	$row = $rs->GetRowAssoc(false);
	
	$data['login']		= stripslashes($row['login']);
	$data['email']		= stripslashes($row['email']);
	$data['fname']		= stripslashes($row['fname']);
	$data['sname']		= stripslashes($row['sname']);
	$data['site_lang']	= stripslashes($row['site_language']);
	
	unset($rs, $row);
	
	if (strlen($data['login']) == 0) {
		ChangePasswForm($lang['err']['oldpassw_bad']);
		return;
	}
	
	if ($data['login'] == strval($passw)) {
		ChangePasswForm($lang['err']['pass_eq_log']);
		return;
	}
	
	$dbconn->Execute('UPDATE '.USERS_TABLE.' SET password = ? WHERE id = ?', array(md5($passw), $id_user));
	
	/**
	 * send email and message to user
	 **/
	
	// language
	$site_lang = $data['site_lang'];
	
	// include language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	$data['adminemail'] = $dbconn->GetOne('SELECT email FROM '.USERS_TABLE.' WHERE root_user = "1"');
	$data['new_pass']	= $passw;
	$data['urls']		= GetUserEmailLinks();
	
	$suffix = ($user[ AUTH_GENDER ] == GENDER_MALE) ? '_e' : '_t';
	
	// subject
	$subject	= $lang_mail['password_changed'.$suffix]['subject'];
	
	// recipient
	$name_to	= trim($data['fname'].' '.$data['sname']);
	
	SendMail($site_lang, $data['email'], $config['site_email'], $subject, $data, 'mail_password_changed_user', null,
		$name_to, '', 'password_changed', $user[ AUTH_GENDER ]);
	
	// assemble body
	$body = $lang_mail['generic'.$suffix]['hello'].' '.$data['fname'].',<br><br>';
	$body.= $lang_mail['password_changed'.$suffix]['message'].'<br><br>';
	$body.= $lang_mail['generic'.$suffix]['admin_regards'];
	
	$strSQL =
		'INSERT INTO '.MAILBOX_TABLE.' SET
			id_to = ?, id_from = ?, subject = ?, body = ?, was_read = "0", deleted_to = "0", deleted_from = "0", date_creation = NOW()';
	$dbconn->Execute($strSQL, array($id_user, ID_ADMIN, $subject, $body));
	
	if ($from == 'myprofile') {
		// return to signup sandbox
		header('location: myprofile.php?pwd=true');
		exit;
	}
	
	AccountPage($lang['err']['pass_saved']);
	
	return;
}


function ChangeSubscribe()
{
	global $user;
	
	$a_subscr = $_POST['a_subscr'];
	
	UpdateSubscribeListForUser($a_subscr, $user[ AUTH_ID_USER ]);
	AccountPage();
	return;
}


function ChangeAlerts()
{
	global $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$my_alerts_email = isset($_POST['my_alerts_email']) ? $_POST['my_alerts_email'] : array();
	
	$dbconn->Execute('DELETE FROM '.SUBSCRIBE_USER_TABLE.' WHERE type = "s" AND id_user = ?', array($id_user));
	
	if (is_array($my_alerts_email) && count($my_alerts_email) > 0) {
		foreach ($my_alerts_email as $v) {
			$dbconn->Execute('INSERT INTO '.SUBSCRIBE_USER_TABLE.' SET id_subscribe = ?, id_user = ?, type = "s"', array($v, $id_user));
		}
	}
	
	AccountPage();
	return;
}


function ChangeVisible()
{
	global $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$dbconn->Execute('UPDATE '.USERS_TABLE.' SET visible = IF(visible="1", "0", "1") WHERE id=? AND root_user="0" AND guest_user="0"', array($id_user));
	
	header('location: account.php');
	exit;
}


function DeleteProfile()
{
	global $config, $dbconn, $user;

	$id_user = $user[ AUTH_ID_USER ];

	$rs = $dbconn->Execute('SELECT name, value FROM '.SETTINGS_TABLE.' WHERE name IN ("icons_folder", "photos_folder", "audio_folder", "video_folder")');
	
	while (!$rs->EOF) {
		$settings[$rs->fields[0]] = $rs->fields[1];
		$rs->MoveNext();
	}
	
	// delete files
	$rs_upl = $dbconn->Execute('SELECT upload_path, upload_type FROM '.USER_UPLOAD_TABLE.' WHERE id_user = ?', array($id_user));
	
	while (!$rs_upl->EOF)
	{
		if ($rs_upl->fields[1] == 'f') {
			$file_folder = $settings['photos_folder'];
		} elseif ($rs_upl->fields[1] == 'a') {
			$file_folder = $settings['audio_folder'];
		} else {
			$file_folder = $settings['video_folder'];
		}
		
		if (strlen($rs_upl->fields[0]) > 0)
		{
			$old_file = $config['site_path'].$file_folder.'/'.$rs_upl->fields[0];
			$old_file_thumb = $config['site_path'].$file_folder.'/thumb_'.$rs_upl->fields[0];
			
			if (file_exists($old_file)) {
				unlink($old_file);
			}
			
			if (file_exists($old_file_thumb)) {
				unlink($old_file_thumb);
			}
		}
		
		$rs_upl->MoveNext();
	}
	
	$dbconn->Execute('DELETE FROM '.USER_UPLOAD_TABLE.' WHERE id_user = ?', array($id_user));
	
	$rs_upl = $dbconn->Execute('SELECT icon_path, icon_path_temp FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
	$icon_path = $rs_upl->fields[0] ? $rs_upl->fields[0] : $rs_upl->fields[1];
	$icon_path_thumb = 'thumb_'.$icon_path;
	
	if (strlen($icon_path) > 0)
	{
		$old_file = $config['site_path'].$settings['icons_folder'].'/'.$icon_path;
		
		if (file_exists($old_file)) {
			unlink($old_file);
		}
		
		$old_file_thumb = $config['site_path'].$settings['icons_folder'].'/'.$icon_path_thumb;
		
		if (file_exists($old_file_thumb)) {
			unlink($old_file_thumb);
		}
	}
	
	//VP
	//adding entry in log table 'user_terminated'
	//print_r($user);
	
	$t_id_group = $dbconn->GetOne('SELECT id_group FROM '.USER_GROUP_TABLE.' WHERE id_user = ?', array($id_user));
	
	$strSQL =
		'SELECT fname, sname, status, gender, date_birthday, email, mm_contact_phone_number, mm_contact_mobile_number,
				mm_application_submit, mm_platinum_applied, login_count
		   FROM '.USERS_TABLE.'
		  WHERE id = ?';
	$rs = $dbconn->Execute($strSQL, array($id_user));
	
	$t_id_user				= $id_user;
	$t_fname				= $rs->fields[0];
	$t_sname				= $rs->fields[1];
	$t_status				= $rs->fields[2];
	$t_gender				= $rs->fields[3];
	$t_date_birthday		= $rs->fields[4];
	$t_email				= $rs->fields[5];
	$t_phone_number			= $rs->fields[6];
	$t_mobile_number		= $rs->fields[7];
	$t_date_registration	= $rs->fields[8];
	$t_date_platinum_applied= $rs->fields[9];
	$t_login_count			= $rs->fields[10];
	$t_comment				= '';
	
	$strSQL = " INSERT INTO ".USER_TERMINATED_TABLE." (id_user, id_group, fname, sname, status, gender, date_birthday, email, phone_number, mobile_number, date_registration, date_platinum_applied, date_termination, login_count, comment)
				VALUES (
						'$t_id_user',
						'$t_id_group',
						'$t_fname',
						'$t_sname',
						'$t_status',
						'$t_gender',
						'$t_date_birthday',
						'$t_email',
						'$t_phone_number',
						'$t_mobile_number',
						'$t_date_registration',
						'$t_date_platinum_applied',
						NOW(),
						'$t_login_count',
						'$t_comment'
					)";
	$dbconn->Execute($strSQL);
	
	$debug = false;
	
	if (!$debug)
	{
		$dbconn->Execute('DELETE FROM '.USERS_TABLE.' WHERE id = ? AND root_user = "0" AND guest_user = "0"', array($id_user));
		$dbconn->Execute('DELETE FROM '.DESCR_SPR_USER_TABLE.' WHERE id_user = ?', array($id_user));
		$dbconn->Execute('DELETE FROM '.DESCR_SPR_MATCH_TABLE.' WHERE id_user = ?', array($id_user));
		$dbconn->Execute('DELETE FROM '.INTERESTS_SPR_USER_TABLE.' WHERE id_user = ?', array($id_user));
		$dbconn->Execute('DELETE FROM '.INTERESTS_SPR_MATCH_TABLE.' WHERE id_user = ?', array($id_user));
		$dbconn->Execute('DELETE FROM '.USER_MATCH_TABLE.' WHERE id_user = ?', array($id_user));
		$dbconn->Execute('DELETE FROM '.USER_GROUP_TABLE.' WHERE id_user = ?', array($id_user));
		$dbconn->Execute('DELETE FROM '.PERSON_SPR_USER_TABLE.' WHERE id_user = ?', array($id_user));
		$dbconn->Execute('DELETE FROM '.PERSON_SPR_MATCH_TABLE.' WHERE id_user = ?', array($id_user));
		$dbconn->Execute('DELETE FROM '.PORTRAIT_SPR_USER_TABLE.' WHERE id_user = ?', array($id_user));
		$dbconn->Execute('DELETE FROM '.PORTRAIT_SPR_MATCH_TABLE.' WHERE id_user = ?', array($id_user));
		$dbconn->Execute('DELETE FROM '.USER_PROFILE_TABLE.' WHERE id_user = ?', array($id_user));
		$dbconn->Execute('DELETE FROM '.USER_TOPTEN_TABLE.' WHERE id_user = ?', array($id_user));
		$dbconn->Execute('DELETE FROM '.ACTIVE_SESSIONS_TABLE.' WHERE id_user = ? AND session = ?', array($id_user, session_id()));
		
		// alerts what user is online mustnt apear when he is ofline
		$dbconn->Execute('DELETE FROM '.ONLINE_NOTICE_TABLE.' WHERE id_from = ? AND type = "1"', array($id_user));
		
		// if forum module installed
		if ($config['use_pilot_module_forum']) {
			DeleteUserFromForum($id_user);
		}
		
		// if events module installed
		if ($config['use_pilot_module_events']) {
			include_once('include/functions_events.php');
			DeleteUserFromEvents($id_user);
		}
		
		// if flash IM installed
		if ($config['use_pilot_module_im']) {
			include_once('w_communicator/wc_config.php');
			include_once('w_communicator/wc_functions.php');
			delete_site_user($id_user);
		}
		
		SetNewsletterUserUnactive($id_user);
	}
	echo '<script>document.location.href="'.$config['site_root'].'/index.php"</script>';
	return;
}
?>