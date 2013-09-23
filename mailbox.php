<?php
/**
* User mailbox management file (folders listing, messages viewing, writing and sending messages)
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
include './include/class.percent.php';
include './include/class.phpmailer.php';
include './include/functions_mail.php';

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
// (we do not check for status, as applicants are having access to the mailbox)

// check permissions
IsFileAllowed(GetRightModulePath(__FILE__));

// alerts and statistics
if (!$user[ AUTH_GUEST ]) {
	GetAlertsMessage();
	SetModuleStatistic(GetRightModulePath(__FILE__));
}

// active menu item
if ($user[ AUTH_IS_APPLICANT ]) {
	$smarty->assign('sub_menu_num', '5');
} else {
	$smarty->assign('sub_menu_num', '');
}

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

// limited access for applicants
if ($user[ AUTH_IS_APPLICANT ]) {
	if ($sel == 'write' || $sel == 'reply' || $sel == 'hotlist' || $sel == 'connections' || $sel == 'send' || $sel == 'resend' || $sel == 'fs' || $sel == 'addconnection') {
		mail_inbox($lang['error']['access_denied_applicant']);
		exit;
	}
}

// limited access for trial users and inactive users
// addconnections gets a special treatment in function AddToConnections() in functions_index.php
// 'fs', 'write' and 'reply' get a special treatment in function mail_write()
// RS: not needed in new membership model, but still interesting
/*
if ($sel == 'hotlist' || $sel == 'connections' || $sel == 'send' || $sel == 'resend') {
	if ($user[ AUTH_IS_TRIAL ]) {
		mail_inbox($lang['error']['access_denied_trial']);
		exit;
	}
	if ($user[ AUTH_IS_INACTIVE ]) {
		mail_inbox($lang['error']['access_denied_inactive']);
		exit;
	}
}
*/

// dispatcher
switch ($sel) {
	case 'inbox': mail_inbox(); break;
	case 'outbox': mail_outbox(); break;
	case 'history': mail_history(); break;
	case 'write': mail_write(); break;
	case 'reply': mail_write('reply'); break;
	case 'delto': delete_msg('to'); break;
	case 'delfrom': delete_msg('from'); break;
	case 'hotlist': mail_hotlist(); break;
	case 'connections': mail_connections(); break;
	case 'send': mail_send(); break;
	case 'resend': mail_resend(); break;
	case 'viewto': mail_view('to'); break;
	case 'viewfrom': mail_view('from'); break;
	case 'addblacklist': mail_ignore('add'); break;
	case 'delblacklist': mail_ignore('del'); break;
	case 'fs': mail_write('from_search'); break;
	case 'addhotlist':
		$result = AddToHotList();
		if (isset($_GET['id_msg'])) $_GET['id'] = $_GET['id_msg'];
		if (!isset($_GET['ms'])) $_GET['ms'] = '';
		switch ($_GET['ms']) {
			case 'to': mail_view('to', $result['err']); break;
			case 'from': mail_view('from', $result['err']); break;
			case 'history': mail_history($result['err']); break;
			default: mail_inbox($result['err']);
		}
	break;
	case 'addconnection':
		$result = AddToConnections();
		if (isset($_GET['id_msg'])) $_GET['id'] = $_GET['id_msg'];
		if (!isset($_GET['ms'])) $_GET['ms'] = '';
		switch ($_GET['ms']) {
			case 'to': mail_view('to', $result['err']); break;
			case 'from': mail_view('from', $result['err']); break;
			case 'history': mail_history($result['err']); break;
			// doesn't make sense to show compose form when users are not connected
			## case 'write': mail_write('addconnection', $result['err']); break;
			case 'write': mail_inbox($result['err']); break;
			case 'reply': mail_view('to', $result['err']); break;
			default: mail_inbox($result['err']);
		}
	break;
	case 'save_period': savePeriod(); break;
	default: 	mail_inbox(); break;
}

exit;


function mail_inbox($err = '')
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$page = isset($_REQUEST['page']) && $_REQUEST['page'] > 0 ? (int) $_REQUEST['page'] : 1;
	$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
	
	$form['sort_link_1'] = 'mailbox.php?sort=1&amp;sel=inbox';
	$form['sort_link_2'] = 'mailbox.php?sort=2&amp;sel=inbox';
	$form['sort_link_3'] = 'mailbox.php?sort=3&amp;sel=inbox';
	
	switch($sort)
	{
		case '1': $order_string = ' ORDER BY u.fname'; break;
		case '2': $order_string = ' ORDER BY m.subject'; break;
		case '3': $order_string = ' ORDER BY m.id desc'; break;
		default: $order_string = ' ORDER BY m.id desc';
	}
	
	$rs = $dbconn->Execute('SELECT COUNT(id) FROM '.MAILBOX_TABLE.' WHERE id_to = ? AND deleted_to = "0" AND was_read = "0"', array($id_user));
	$form['inbox_new'] = (int) $rs->fields[0];
	
	$rs = $dbconn->Execute('SELECT COUNT(id) FROM '.MAILBOX_TABLE.' WHERE id_to = ? AND deleted_to = "0"', array($id_user));
	$form['inbox_all'] = (int) $rs->fields[0];
	
	$rs = $dbconn->Execute('SELECT COUNT(id) FROM '.MAILBOX_TABLE.' WHERE id_from = ? AND deleted_from = "0"', array($id_user));
	$form['outbox_all'] = (int) $rs->fields[0];
	
	$num_records = $form['inbox_all'];
	
	// paging
	$lim_min = ($page-1) * $config_index['message_numpage'];
	$lim_max = $config_index['message_numpage'];
	$limit_str = ' LIMIT '.$lim_min.', '.$lim_max;
	
	if ($num_records > 0)
	{
		$strSQL =
			'SELECT m.id, m.id_from, m.subject, DATE_FORMAT(m.date_creation,"'.$config['date_format'].'") AS date_creation, m.was_read,
					UNIX_TIMESTAMP(m.date_creation) as date_stamp, u.login, u.fname
			   FROM '.MAILBOX_TABLE.' m
		  LEFT JOIN '.USERS_TABLE.' u ON u.id = m.id_from
			  WHERE m.id_to = ? AND m.deleted_to = "0"'
					.$order_string.' '.$limit_str;
		
		$rs = $dbconn->Execute($strSQL, array($id_user));
		
		$maillist = array();
		$i = 0;
		
		while (!$rs->EOF)
		{
			$row = $rs->GetRowAssoc(false);
			
			$maillist[$i]['id'] = $row['id'];
			$maillist[$i]['from'] = $row['fname'];
			
			if (strlen(utf8_decode($row['subject'])) > 120 ) {
				$maillist[$i]['subject'] = utf8_substr(strip_tags($row['subject']), 0, 120).'...';
			} else {
				$maillist[$i]['subject'] = $row['subject'];
			}
			
			$maillist[$i]['date'] = $row['date_creation'];
			$maillist[$i]['time'] = date('H:i', $row['date_stamp']);
			$maillist[$i]['new'] = $row['was_read'] ? 0 : 1;
			
			$rs_attach = $dbconn->Execute('SELECT COUNT(id) FROM '.MAILBOX_ATTACHES_TABLE.' WHERE id_mail = ?', array($maillist[$i]['id']));
			
			$maillist[$i]['attach'] = ($rs_attach->fields[0] > 0) ? 1 : 0;
			
			$rs->MoveNext();
			$i++;
		}
		
		$param = 'mailbox.php?sel=inbox&amp;sort='.$sort.'&amp;';
		$smarty->assign('links', GetLinkArray($num_records, $page, $param, $config_index['message_numpage']));
		
		$smarty->assign('maillist', $maillist);
	}
	
	$form['err'] = $err;
	$smarty->assign('header', $lang['mailbox']);
	
	if (isset($_REQUEST['from']) && $_REQUEST['from'] == 'organizer') {
		$form['back_link'] = 'organizer.php';
	}
	
	$smarty->assign('form', $form);
	$smarty->assign('user_gender', $user[ AUTH_GENDER ]);
	
	// prepare "delete older than ..." form
	$rs = $dbconn->Execute('SELECT id_user, status, amount, period FROM '.USER_MAILBOX_SETTINGS.' WHERE id_user = ?', array($id_user));
	
	$smarty->assign('delete_after_form', $rs->GetRowAssoc(false));
	
	// display template
	$smarty->display(TrimSlash($config['index_theme_path']).'/mailbox_inbox.tpl');
	exit;
}


function mail_outbox($err = '')
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$page = isset($_REQUEST['page']) && $_REQUEST['page'] > 0 ? (int) $_REQUEST['page'] : 1;
	$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
	
	$form['sort_link_1'] = 'mailbox.php?sort=1&amp;sel=outbox';
	$form['sort_link_2'] = 'mailbox.php?sort=2&amp;sel=outbox';
	$form['sort_link_3'] = 'mailbox.php?sort=3&amp;sel=outbox';
	$form['sort_link_4'] = 'mailbox.php?sort=4&amp;sel=outbox';
	
	switch ($sort)
	{
		case '1': $order_string = ' ORDER BY u.fname'; break;
		case '2': $order_string = ' ORDER BY m.subject'; break;
		case '3': $order_string = ' ORDER BY date_creation DESC'; break;
		case '4': $order_string = ' ORDER BY was_read'; break;
		default: $order_string = ' ORDER BY m.id DESC';
	}
	
	$rs = $dbconn->Execute('SELECT COUNT(id) FROM '.MAILBOX_TABLE.' WHERE id_to = ? AND deleted_to = "0" AND was_read = "0"', array($id_user));
	$form['inbox_new'] = (int) $rs->fields[0];
	
	$rs = $dbconn->Execute('SELECT COUNT(id) FROM '.MAILBOX_TABLE.' WHERE id_to = ? AND deleted_to = "0"', array($id_user));
	$form['inbox_all'] = (int) $rs->fields[0];
	
	$rs = $dbconn->Execute('SELECT COUNT(id) FROM '.MAILBOX_TABLE.' WHERE id_from = ? AND deleted_from = "0"', array($id_user));
	$form['outbox_all'] = (int) $rs->fields[0];
	
	$num_records = $form['outbox_all'];
	
	$lim_min = ($page-1) * $config_index['message_numpage'];
	$lim_max = $config_index['message_numpage'];
	$limit_str = ' LIMIT '.$lim_min.', '.$lim_max;
	
	if ($num_records > 0)
	{
		$strSQL =
			'SELECT m.id, m.id_from, m.subject, DATE_FORMAT(m.date_creation,"'.$config['date_format'].'") AS date_creation, m.was_read,
					u.login, u.fname, m.deleted_to, UNIX_TIMESTAMP(m.date_creation) AS date_stamp
			   FROM '.MAILBOX_TABLE.' m
		  LEFT JOIN '.USERS_TABLE.' u ON u.id = m.id_to
			  WHERE m.id_from = ? AND m.deleted_from = "0"'.
					$order_string.' '.$limit_str;
		
		$rs = $dbconn->Execute($strSQL, array($id_user));
		
		$maillist = array();
		$i = 0;
		
		while (!$rs->EOF)
		{
			$row = $rs->GetRowAssoc(false);
			
			$maillist[$i]['id'] = $row['id'];
			$maillist[$i]['from'] = $row['fname'];
			
			if (strlen(utf8_decode($row['subject'])) > 120)
			{
				$maillist[$i]['subject'] = utf8_substr(strip_tags($row['subject']), 0, 120).'...';
			}
			else
			{
				$maillist[$i]['subject'] = $row['subject'];
			}
			
			$maillist[$i]['date'] = $row['date_creation'];
			$maillist[$i]['time'] = date('H:i', $row['date_stamp']);
			$maillist[$i]['was_read'] = $row['was_read'];
			$maillist[$i]['not_read'] = $row['was_read']?$lang['mailbox']['yes']:$lang['mailbox']['no'];
			
			if ($row['was_read'] == '0' && $row['deleted_to'] == '1') {
				$maillist[$i]['not_read'] = $lang['mailbox']['del'];
			}
			
			$rs->MoveNext();
			$i++;
		}
		
		$param = 'mailbox.php?sel=outbox&amp;sort='.$sort.'&amp;';
		$smarty->assign('links', GetLinkArray($num_records, $page, $param, $config_index['message_numpage']));
		
		$smarty->assign('maillist', $maillist);
	}
	
	$form['err'] = $err;
	
	$smarty->assign('header', $lang['mailbox']);
	
	if (isset($_REQUEST['from']) && $_REQUEST['from'] == 'organizer') {
		$form['back_link'] = 'organizer.php';
	}
	
	$smarty->assign('form', $form);
	$smarty->assign('user_gender', $user[ AUTH_GENDER ]);
	
	$smarty->display(TrimSlash($config['index_theme_path']).'/mailbox_outbox.tpl');
	exit;
}


function mail_history($err = '')
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$percent = new Percent($id_user);
	
	$id_correspondent = isset($_GET['id']) ? (int) $_GET['id'] : 0;
	
	$page = isset($_REQUEST['page']) && (int) $_REQUEST['page'] > 0 ? (int) $_REQUEST['page'] : 1;
	
	// not in use
	#$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
	
	$rs = $dbconn->Execute('SELECT COUNT(id) FROM '.MAILBOX_TABLE.' WHERE id_to = ? AND deleted_to = "0" AND was_read = "0"', array($id_user));
	$form['inbox_new'] = (int) $rs->fields[0];
	
	$rs = $dbconn->Execute('SELECT COUNT(id) FROM '.MAILBOX_TABLE.' WHERE id_to = ? AND deleted_to = "0"', array($id_user));
	$form['inbox_all'] = (int) $rs->fields[0];
	
	$rs = $dbconn->Execute('SELECT COUNT(id) FROM '.MAILBOX_TABLE.' WHERE id_from = ? AND deleted_from = "0"', array($id_user));
	$form['outbox_all'] = (int) $rs->fields[0];
	
	$strSQL =
		'SELECT COUNT(id)
		   FROM '.MAILBOX_TABLE.'
		  WHERE id_to IN ("'.$id_user.'", "'.$id_correspondent.'")
		    AND id_from IN ("'.$id_user.'", "'.$id_correspondent.'")
			AND deleted_to = "0"
	   ORDER BY id DESC';
	
	$rs = $dbconn->Execute($strSQL);
	
	$num_records = (int) $rs->fields[0];
	
	$lim_min = ($page-1) * $config_index['message_numpage'];
	$lim_max = $config_index['message_numpage'];
	$limit_str = ' LIMIT '.$lim_min.', '.$lim_max;
	
	// messages
	if ($num_records > 0)
	{
		$strSQL =
			'SELECT m.id, m.subject, m.body, DATE_FORMAT(m.date_creation, "'.$config['date_format'].' - %H:%i") AS date_creation, u.login, u.fname
			   FROM '.MAILBOX_TABLE.' m
		  LEFT JOIN '.USERS_TABLE.' u ON u.id = m.id_from
			  WHERE m.id_to IN ("'.$id_user.'", "'.$id_correspondent.'")
			    AND m.id_from IN ("'.$id_user.'", "'.$id_correspondent.'")
				AND m.deleted_to = "0"
		   ORDER BY m.id DESC '
					.$limit_str;
		
		$rs = $dbconn->Execute($strSQL);
		
		$history = array();
		$i = 0;
		
		while(!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$history[$i]['subject_msg'] = $row['subject'];
			$history[$i]['text_msg'] = nl2br($row['body']);
			$history[$i]['date_msg'] = $row['date_creation'];
			$history[$i]['name_msg'] = $row['fname'];
			$history[$i]['reply_link'] = './mailbox.php?sel=reply&amp;id='.$row['id'];
			$history[$i]['resend_link'] = './mailbox.php?sel=resend&amp;id='.$row['id'];
			$rs->MoveNext();
			$i++;
		}
		
		$param = 'mailbox.php?sel=history&amp;id='.$id_correspondent.'&amp;';
		$smarty->assign('links', GetLinkArray($num_records, $page, $param, $config_index['message_numpage']));
	}
	
	// settings
	$settings = GetSiteSettings(array('icon_male_default', 'icon_female_default', 'icons_folder', 'show_users_group_str', 'use_friend_types'));
	
	// default icons
	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];
	
	// correspondent info
	$strSQL =
		'SELECT a.id, a.login, a.fname, a.date_birthday, a.big_icon_path, c.name AS country_name, d.name AS city_name, r.name AS region_name,
				a.gender, sess.session, hot.id_friend, ig.id_enemy,
				k.gender AS gender_search, k.age_max, k.age_min, a.root_user
		   FROM '.USERS_TABLE.' a
	  LEFT JOIN '.COUNTRY_SPR_TABLE.' c ON c.id = a.id_country
	  LEFT JOIN '.CITY_SPR_TABLE.' d ON d.id = a.id_city
	  LEFT JOIN '.ACTIVE_SESSIONS_TABLE.' sess ON a.id = sess.id_user
	  LEFT JOIN '.HOTLIST_TABLE.' hot ON hot.id_user = ? AND hot.id_friend = a.id
	  LEFT JOIN '.BLACKLIST_TABLE.' ig ON ig.id_user = ? AND ig.id_enemy = a.id
	  LEFT JOIN '.USER_MATCH_TABLE.' k ON k.id_user = a.id
	  LEFT JOIN '.REGION_SPR_TABLE.' r ON r.id = a.id_region
		  WHERE a.id = ?';
	
	$rs = $dbconn->Execute($strSQL, array($id_user, $id_user, $id_correspondent));
	$row = $rs->GetRowAssoc(false);
	
	$data['id']				= $row['id'];
	$data['name']			= stripslashes($row['fname']);
	$data['gender']			= (int) $row['gender'];
	$data['status']			= $row['session'] ? $lang['status']['on'] : $lang['status']['off'];
	$data['root_user']		= $row['root_user'];
	$data['age']			= AgeFromBDate($row['date_birthday']);
	$data['age_max']		= $row['age_max'];
	$data['age_min']		= $row['age_min'];
	$data['gender_search']	= !empty($row['gender_search']) ? $lang['gender_search'][$row['gender_search']] : '';
	$data['country']		= stripslashes($row['country_name']);
	$data['region']			= stripslashes($row['region_name']);
	$data['city']			= stripslashes($row['city_name']);
	$data['completion']		= $percent->GetAllPercentForUser($row['id']);
	
	// get correspondent's groups
	$sub_strSQL =
		'SELECT a.name
		   FROM '.USER_GROUP_TABLE.' b
	  LEFT JOIN '.GROUPS_TABLE.' a ON a.id = b.id_group
		  WHERE b.id_user = ?';
	
	$sub_rs = $dbconn->Execute($sub_strSQL, array($id_correspondent));
	
	$groups_arr = array();
	
	while (!$sub_rs->EOF) {
		$groups_arr[] = $sub_rs->fields[0];
		$sub_rs->MoveNext();
	}
	
	if (!empty($groups_arr)) {
		$data['group'] = implode(',', $groups_arr);
	}
	
	// get correspondent's icon
	$big_icon_path = $row['big_icon_path'] ? $row['big_icon_path'] : (!empty($row['gender']) ? $default_photos[$row['gender']] : '');
	
	if ($big_icon_path && file_exists($config['site_path'].$settings['icons_folder'].'/'.$big_icon_path)) {
		$data['big_icon_path'] = $config['site_root'].$settings['icons_folder'].'/'.$big_icon_path;
	}
	
	// correspondent's photo count
	$strSQL =
		'SELECT COUNT(DISTINCT upload_path)
		   FROM '.USER_UPLOAD_TABLE.'
		  WHERE id_user = ? AND upload_type = "f" AND status = "1" AND allow in ("1", "2")';
	
	$rs_photo = $dbconn->Execute($strSQL, array($id_correspondent));
	
	$data['photo_count'] = (int) $rs_photo->fields[0];
	if (!empty($data['big_icon_path'])) {
		$data['photo_count']++;
	}
	
	// links
	$data['profile_link'] = './viewprofile.php?id='.$row['id'];
	
	// hot list, connections and black list
	$data['hotlisted'] = empty($row['id_friend']) ? 0 : 1;
	$data['blacklisted'] = empty($row['id_enemy']) ? 0 : 1;
	
	// check connection status
	$data['connected_status'] = getConnectedStatus($row['id'], $user[ AUTH_ID_USER ]);
	
	// add to hotlist link
	if ($data['hotlisted'] == 0 && $data['blacklisted'] == 0) {
		if ($settings['use_friend_types']) {
			$data['add_hotlist_link'] = './hotlist.php?sel=addform&amp;id='.$row['id'];
		} else {
			$data['add_hotlist_link'] = './mailbox.php?sel=addhotlist&amp;id='.$row['id'].'&amp;ms=history';
		}
	}
	
	// add to blacklist link
	if ($data['hotlisted'] == 0 && $data['connected_status'] != CS_CONNECTED && $data['blacklisted'] == 0) {
		$data['add_blacklist_link'] = 'mailbox.php?sel=addblacklist&amp;id='.$row['id'].'&amp;ms=history';
	}
	
	// add to connections link
	if ($data['connected_status'] == CS_NOTHING && $data['blacklisted'] == 0) {
		if ($settings['use_friend_types']) {
			$data['add_connection_link'] = './connections.php?sel=addform&amp;id='.$row['id'];
		} else {
			$data['add_connection_link'] = './mailbox.php?sel=addconnection&amp;id='.$row['id'].'&amp;ms=history';
		}
	}
	
	$form['show_users_group_str'] = $settings['show_users_group_str'];
	$form['use_friend_types'] = $settings['use_friend_types'];
	
	$form['err'] = $err;
	
	$smarty->assign('header', $lang['mailbox']);
	$smarty->assign('history', $history);
	$smarty->assign('form', $form);
	$smarty->assign('data', $data);
	
	$smarty->display(TrimSlash($config['index_theme_path']).'/mailbox_history.tpl');
	exit;
}


function mail_write($par = '', $err = '', $to = '', $subject = '', $body = '', $temp_attach_id = '')
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	// check permission
	if (empty($_SESSION['permissions']['email_compose'])) {
		AlertPage('email_compose');
		exit;
	}
	
	$id_user = $user[ AUTH_ID_USER ];
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	if (MM_CHECK_EMAIL_LIMIT) {
		if (CheckMailCountPermission() == false) {
			mail_inbox($lang['err']['emails_limit']);
			return;
		}
	}
	
	$settings = GetSiteSettings(array('attaches_folder'));
	
	$form['par'] = $par;
	$form['err'] = $err;
	
	$data = array();
	
	if ($temp_attach_id != '')
	{
		$rs = $dbconn->Execute(
			'SELECT id, attach_name, attach_file_type
			   FROM '.MAILBOX_ATTACHES_TABLE.'
			  WHERE id_mail_temp = ? AND id_user = ?',
			array($temp_attach_id, $id_user));
		
		$data['attaches'] = array();
		$i = 0;
		
		while (!$rs->EOF) {
			$data['attaches'][$i]['id'] = $rs->fields[0];
			$data['attaches'][$i]['name'] = $rs->fields[1];
			$data['attaches'][$i]['file_type'] = $rs->fields[2];
			$data['attaches'][$i]['path'] = $config['server'].$config['site_root'].$settings['attaches_folder'].'/'.$rs->fields[1];
			$rs->MoveNext();
			$i++;
		}
		
		$form['temp_attach_id'] = $temp_attach_id;
	}
	
	if (!empty($_REQUEST['search_type'])) {
		$form['back_link_profile'] = 1;
		$form['back_link_list'] = getBacklink($_REQUEST['search_type']);
	}
			
	switch ($par)
	{
		case 'err':
		
			$data['to'] = $to;
			$data['to_fname'] = $dbconn->getOne('SELECT fname FROM '.USERS_TABLE.' WHERE id = ?', array($to));
			$data['subject'] = $subject;
			$data['body'] = $body;
			
			if (!empty($_POST['from_search'])) {
				$form['back_link_profile'] = 1;
			}
		
		break;
	
		case 'ok':
	
			if (!empty($_POST['from_search'])) {
				$data['to'] = $to;
				$data['to_fname'] = $dbconn->getOne('SELECT fname FROM '.USERS_TABLE.' WHERE id = ?', array($to));
				$form['back_link_profile'] = 1;
			}
			
		break;
	
		case 'reply':
			
			$reply_id = (int) $_REQUEST['id'];
			
			$strSQL =
				'SELECT u.id, u.login, u.fname
				   FROM '.MAILBOX_TABLE.' m
			  LEFT JOIN '.USERS_TABLE.' u ON u.id = m.id_from
				  WHERE m.id_to = ? AND m.id = ?';
			
			$rs = $dbconn->Execute($strSQL, array($id_user, $reply_id));
			$row = $rs->GetRowAssoc(false);
			
			$data['to'] = $row['id'];
			$data['to_fname'] = $row['fname'];
			$data['subject'] = $subject;
			$data['body'] = $body;
			
			$recipient_id = (int) $row['id'];
			
			if (getConnectedStatus($id_user, $recipient_id) != CS_CONNECTED) {
				$form['err'] = $lang['err']['email_not_connected'].'<br><br>';
				if (!empty($_SESSION['permissions']['connection_invite'])) {
					$querystring = 'id='.$recipient_id.'&ms=reply&id_msg='.$reply_id;
					$form['err'] .= str_replace('#QUERYSTRING#', $querystring, $lang['err']['email_not_connected_connect_now']);
					if ($user[ AUTH_IS_PLATINUM ] && PLATINUM_GETS_UNLIMITED_CONNECTIONS) {
						$form['err'] .= $lang['err']['message_write_not_connected_platinum'];
					} elseif ($user[ AUTH_IS_ELITE ] && ELITE_GETS_UNLIMITED_CONNECTIONS) {
						$err .= $lang['err']['message_write_not_connected_elite'];
					}
				} else {
					// trial and inactive do not have any permission to write messages and can also be intercepted before the dispatcher,
					// but here we can show better messages
					if ($user[ AUTH_IS_TRIAL ]) {
						$form['err'] .= $lang['err']['connection_invite_permission_denied_trial'];
					} elseif ($user[ AUTH_IS_TRIAL_INACTIVE ]) {
						$err .= $lang['err']['connection_invite_permission_denied_trial_inactive'];
					} elseif ($user[ AUTH_IS_REGULAR ]) {
						$form['err'] .= $lang['err']['connection_invite_permission_denied_regular'];
					} elseif ($user[ AUTH_IS_REGULAR_INACTIVE ]) {
						$form['err'] .= $lang['err']['connection_permission_denied_regular_inactive'];
					} elseif ($user[ AUTH_IS_PLATINUM_INACTIVE ]) {
						$form['err'] .= $lang['err']['connection_invite_permission_denied_platinum_inactive'];
					} elseif ($user[ AUTH_IS_ELITE_INACTIVE ]) {
						$form['err'] .= $lang['err']['connection_invite_permission_denied_elite_inactive'];
					}
				}
				mail_inbox($form['err']);
				exit;
			}
			
			$form['reply_id'] = $reply_id;
			
			unset($rs, $row);
			
		break;
		
		case 'from_search':
		
			$recipient_id = (int) $_GET['id'];
			$data['to'] = $recipient_id;
			$data['to_fname'] = $dbconn->getOne('SELECT fname FROM '.USERS_TABLE.' WHERE id = ?', array($recipient_id));
			$form['back_link_profile'] = 1;
			
			if (getConnectedStatus($id_user, $recipient_id) != CS_CONNECTED) {
				$form['err'] = $lang['err']['email_not_connected'].'<br><br>';
				if (!empty($_SESSION['permissions']['connection_invite'])) {
					$querystring = 'id='.$recipient_id.'&ms=write&search_type='.$_REQUEST['search_type'];
					$form['err'] .= str_replace('#QUERYSTRING#', $querystring, $lang['err']['email_not_connected_connect_now']);
					if ($user[ AUTH_IS_PLATINUM ] && PLATINUM_GETS_UNLIMITED_CONNECTIONS) {
						$form['err'] .= $lang['err']['message_write_not_connected_platinum'];
					} elseif ($user[ AUTH_IS_ELITE ] && ELITE_GETS_UNLIMITED_CONNECTIONS) {
						$err .= $lang['err']['message_write_not_connected_elite'];
					}
				} else {
					// trial and inactive do not have any permission to write messages and can also be intercepted before the dispatcher,
					// but here we can show better messages
					if ($user[ AUTH_IS_TRIAL ]) {
						$form['err'] .= $lang['err']['connection_invite_permission_denied_trial'];
					} elseif ($user[ AUTH_IS_TRIAL_INACTIVE ]) {
						$err .= $lang['err']['connection_invite_permission_denied_trial_inactive'];
					} elseif ($user[ AUTH_IS_REGULAR ]) {
						$form['err'] .= $lang['err']['connection_invite_permission_denied_regular'];
					} elseif ($user[ AUTH_IS_REGULAR_INACTIVE ]) {
						$form['err'] .= $lang['err']['connection_permission_denied_regular_inactive'];
					} elseif ($user[ AUTH_IS_PLATINUM_INACTIVE ]) {
						$form['err'] .= $lang['err']['connection_invite_permission_denied_platinum_inactive'];
					} elseif ($user[ AUTH_IS_ELITE_INACTIVE ]) {
						$form['err'] .= $lang['err']['connection_invite_permission_denied_elite_inactive'];
					}
				}
				mail_inbox($form['err']);
				exit;
			}
			
		break;
		
		case 'addconnection':
		
			$recipient_id = (int) $_GET['id'];
			$data['to'] = $recipient_id;
			$data['to_fname'] = $dbconn->getOne('SELECT fname FROM '.USERS_TABLE.' WHERE id = ?', array($recipient_id));
			$form['back_link_profile'] = 1;
		
		break;
		
		default:
		
			// normal message writing starting with an empty message
			//
			/*VP allowing all users to go to compose page
			if (getConnectedStatus($id_user, $recipient_id) != CS_CONNECTED)
			{
				$form['err'] = $lang['err']['email_not_connected'].'<br><br>';
				if (!empty($_SESSION['permissions']['connection_invite']))
				{
					$querystring = 'id='.$recipient_id.'&ms=write&search_type='.$_REQUEST['search_type'];
					$form['err'] .= str_replace('#QUERYSTRING#', $querystring, $lang['err']['email_not_connected_connect_now']);
					if ($user[ AUTH_IS_PLATINUM ] && PLATINUM_GETS_UNLIMITED_CONNECTIONS)
						$form['err'] .= $lang['err']['message_write_not_connected_platinum'];
					} elseif ($user[ AUTH_IS_ELITE ] && ELITE_GETS_UNLIMITED_CONNECTIONS) {
						$err .= $lang['err']['message_write_not_connected_elite'];
					}
				}
				else
				{
					// trial and inactive do not have any permission to write messages and can also be intercepted before the dispatcher,
					// but here we can show better messages
					if ($user[ AUTH_IS_TRIAL ]) {
						$form['err'] .= $lang['err']['connection_invite_permission_denied_trial'];
					} elseif ($user[ AUTH_IS_TRIAL_INACTIVE ]) {
						$err .= $lang['err']['connection_invite_permission_denied_trial_inactive'];
					} elseif ($user[ AUTH_IS_REGULAR ]) {
						$form['err'] .= $lang['err']['connection_invite_permission_denied_regular'];
					} elseif ($user[ AUTH_IS_REGULAR_INACTIVE ]) {
						$form['err'] .= $lang['err']['connection_permission_denied_regular_inactive'];
					} elseif ($user[ AUTH_IS_PLATINUM_INACTIVE ]) {
						$form['err'] .= $lang['err']['connection_invite_permission_denied_platinum_inactive'];
					} elseif ($user[ AUTH_IS_ELITE_INACTIVE ]) {
						$form['err'] .= $lang['err']['connection_invite_permission_denied_elite_inactive'];
					}
				}
				mail_inbox($form['err']);
				exit;
			}
			*/
			
		break;
	}
	
	/*
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';
	$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
	if( (strval($page) == '') || (strval($page) == '0')){ $page = 1;}else{	$page = (int) $page;}
	*/
	
	$rs = $dbconn->Execute('SELECT COUNT(id) FROM '.MAILBOX_TABLE.' WHERE id_to = ? AND deleted_to = "0" AND was_read = "0"', array($id_user));
	$form['inbox_new'] = (int) $rs->fields[0];
	
	$rs = $dbconn->Execute('SELECT COUNT(id) FROM '.MAILBOX_TABLE.' WHERE id_to = ? AND deleted_to = "0"', array($id_user));
	$form['inbox_all'] = (int) $rs->fields[0];
	
	$rs = $dbconn->Execute('SELECT COUNT(id) FROM '.MAILBOX_TABLE.' WHERE id_from = ? AND deleted_from = "0"', array($id_user));
	$form['outbox_all'] = (int) $rs->fields[0];
	
	// display original mail if reply
	if (isset($reply_id))
	{
		$strSQL =
			'SELECT m.id, u.login, u.fname, m.subject, m.body, DATE_FORMAT(m.date_creation, "'.$config['date_format'].' - %H:%i") AS date_creation
			   FROM '.MAILBOX_TABLE.' m
			   LEFT JOIN '.USERS_TABLE.' u ON u.id = m.id_from
			  WHERE m.id_to = ? AND m.id = ?';
		
		$rs = $dbconn->Execute($strSQL, array($id_user, $reply_id));
		$row = $rs->GetRowAssoc(false);
		
		$data['name_msg_last'] = $row['fname'];
		$data['subject_msg_last'] = $row['subject'];
		$data['text_msg_last'] = nl2br($row['body']);
		$data['date_msg_last'] = $row['date_creation'];
		
		$data['reply'] = $data['name_msg_last'] ? 1 : 0;
		
		if ($data['reply'] && $data['subject'] == '') {
			$data['subject'] = 'Re: '.$data['subject_msg_last'];
		}
	}
	
	$smarty->assign('header', $lang['mailbox']);
	$smarty->assign('form', $form);
	$smarty->assign('data', $data);
	
	$smarty->display(TrimSlash($config['index_theme_path']).'/mailbox_write.tpl');
	exit;
}


function delete_msg($par)
{
	global $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	if (!empty($_POST['delete']) && is_array($_POST['delete']))
	{
		// delete multiple
		foreach($_POST['delete'] as $k => $v) {
			$val_array[$k] = (int) $v;
		}
		
		$val_str = '("'.implode('","', $val_array).'")';
		
		if ($par == 'from') {
			$rs = $dbconn->Execute('UPDATE '.MAILBOX_TABLE.' SET deleted_from = "1" WHERE id_from = ? AND id IN '.$val_str, array($id_user));
		} elseif ($par == 'to') {
			$rs = $dbconn->Execute('UPDATE '.MAILBOX_TABLE.' SET deleted_to = "1" WHERE id_to = ? AND id IN '.$val_str, array($id_user));
		}
	}
	elseif (!empty($_GET['id']))
	{
		// delete one
		$id = (int) $_GET['id'];
		
		if ($par == 'from') {
			$rs = $dbconn->Execute('UPDATE '.MAILBOX_TABLE.' SET deleted_from = "1" WHERE id_from = ? AND id = ?', array($id_user, $id));
		} elseif ($par == 'to') {
			$rs = $dbconn->Execute('UPDATE '.MAILBOX_TABLE.' SET deleted_to = "1" WHERE id_to = ? AND id = ?', array($id_user, $id));
		}
	}
	
	if ($par == 'from') {
		mail_outbox();
	} else {
		mail_inbox();
	}
	
	return;
}


function mail_hotlist()
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	// check permission
	if (empty($_SESSION['permissions']['email_compose'])) {
		AlertPage('email_compose');
		exit;
	}
	
	IndexHomePage();
	
	$id_user = (int) $user[ AUTH_ID_USER ];
	
	$strSQL =
		'SELECT DISTINCT u.id, u.login, u.fname, u.date_birthday, c.name as country
		   FROM '.HOTLIST_TABLE.' h
		  INNER JOIN '.USERS_TABLE.' u ON h.id_friend = u.id
		   LEFT JOIN '.COUNTRY_SPR_TABLE.' c ON c.id = u.id_country
		  WHERE h.id_user = ?';
	
	$rs = $dbconn->Execute($strSQL, array($id_user));
	
	$i = 0;
	$hotlist = array();
	
	while (!$rs->EOF)
	{
		$row = $rs->GetRowAssoc(false);
		$hotlist[$i]['id'] = $row['id'];
		$hotlist[$i]['friend'] = $row['fname'];
		$hotlist[$i]['age'] = AgeFromBDate($row['date_birthday']);
		$hotlist[$i]['country'] = stripslashes($row['country']);
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('header', $lang['mailbox']);
	$smarty->assign('hotlist', $hotlist);
	$smarty->display(TrimSlash($config['index_theme_path']).'/mailbox_friendlist.tpl');
	exit;
}


function mail_connections()
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	// check permission
	if (empty($_SESSION['permissions']['email_compose'])) {
		AlertPage('email_compose');
		exit;
	}
	
	IndexHomePage();
	
	$id_user = (int) $user[ AUTH_ID_USER ];
	
	$strSQL =
			'SELECT DISTINCT u.id, u.login, u.fname, u.date_birthday, c.name AS country
			   FROM '.CONNECTIONS_TABLE.' m
		 INNER JOIN '.USERS_TABLE.' u ON u.id = m.id_friend
		  LEFT JOIN '.COUNTRY_SPR_TABLE.' c ON c.id = u.id_country
			  WHERE m.id_user = ? AND m.status = "1" AND u.status = "1"
			  UNION
			 SELECT DISTINCT u.id, u.login, u.fname, u.date_birthday, c.name AS country
			   FROM '.CONNECTIONS_TABLE.' m
		 INNER JOIN '.USERS_TABLE.' u ON u.id = m.id_user
		  LEFT JOIN '.COUNTRY_SPR_TABLE.' c ON c.id = u.id_country
			  WHERE m.id_friend = ? AND m.status = "1" AND u.status = "1"
			  ORDER BY login';
	$rs = $dbconn->Execute($strSQL, array($id_user, $id_user));
	
	$i = 0;
	$connections = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$connections[$i]['id'] = $row['id'];
		$connections[$i]['friend'] = $row['fname'];
		$connections[$i]['age'] = AgeFromBDate($row['date_birthday']);
		$connections[$i]['country'] = stripslashes($row['country']);
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('header', $lang['mailbox']);
	$smarty->assign('connections', $connections);
	$smarty->display(TrimSlash($config['index_theme_path']).'/mailbox_connectionslist.tpl');
	exit;
}


function mail_send()
{
	global $lang, $config, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	// check permission
	if (empty($_SESSION['permissions']['email_compose'])) {
		AlertPage('email_compose');
		exit;
	}
	
	// check message count
	if (MM_CHECK_EMAIL_LIMIT && CheckMailCountPermission() == false) {
		mail_inbox($lang['err']['emails_limit']);
		return;
	}
	
	// settings
	$settings = GetSiteSettings(array('attaches_folder', 'mail_attaches_limit'));
	
	// form input
	$to				= FormFilter($_POST['to']);
	$subject		= FormFilter($_POST['subject']);
	$body			= FormFilter($_POST['body']);
	$temp_attach_id	= isset($_POST['temp_attach_id']) ? (int) $_POST['temp_attach_id'] : 0;
	
	$par = (isset($_REQUEST['par']) && $_REQUEST['par'] == 'reply') ? 'reply' : 'err';
	$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
	
	// attachment handling
	if ($act == 'upload_attach')
	{
		$upload = $_FILES['attach'];
		
		// check attachment limit
		if ($settings['mail_attaches_limit'] > 0 && $temp_attach_id > 0) {
			$rs = $dbconn->Execute('SELECT COUNT(id) FROM '.MAILBOX_ATTACHES_TABLE.' WHERE id_mail_temp = ?', array($temp_attach_id));
			
			if ($rs->fields[0] >= $settings['mail_attaches_limit']) {
				mail_write($par, $lang['err']['you_cant_attach_more_files'], $to, $subject, $body, $temp_attach_id);
				return ;
			}
		}
		
		// check upload success
		if (!is_uploaded_file($upload['tmp_name'])) {
			mail_write($par, $lang['err']['upload_err'], $to, $subject, $body, $temp_attach_id);
			return ;
		}
		
		// check mime type and extension
		$files_types = array('image/jpeg', 'image/pjpeg', 'image/gif', 'image/tiff', 'image/png', 'image/x-png', 'application/msexcel',
		'application/msword', 'application/pdf', 'application/rtf', 'text/plain', 'application/vnd.ms-excel');
		
		$files_extensions = array('jpeg', 'jpg', 'gif', 'tiff', 'png', 'xls', 'doc', 'rtf', 'txt', 'pdf');
		
		$file_name_arr = explode('.', $upload['name']);
		$file_ext = strtolower($file_name_arr[count($file_name_arr)-1]);
		
		if ( !in_array($upload['type'], $files_types) || !in_array($file_ext, $files_extensions) ) {
			mail_write($par, $lang['err']['wrong_file_type_for_attach'], $to, $subject, $body, $temp_attach_id);
			return ;
		}
		
		// rename file
		$new_file_name = $id_user.'_'.substr(md5(microtime().getmypid()), 0, 8).'.'.$file_ext;
		$upload_path = $config['site_path'].$settings['attaches_folder'].'/'.$new_file_name;
		
		if (copy($upload['tmp_name'], $upload_path))
		{
			if (isset($_REQUEST['temp_attach_id']))
			{
				$strSQL = 'INSERT INTO '.MAILBOX_ATTACHES_TABLE.' SET id_mail_temp = ?, attach_name = ?, attach_file_type = ?, id_user = ?';
				$dbconn->Execute($strSQL, array($temp_attach_id, $new_file_name, $upload['type'], $id_user));
			}
			else
			{
				$rs = $dbconn->Execute('SELECT MAX(id_mail_temp) FROM '.MAILBOX_ATTACHES_TABLE);
				$temp_attach_id = $rs->fields[0] + 1;
				
				$strSQL = 'INSERT INTO '.MAILBOX_ATTACHES_TABLE.' SET id_mail_temp = ?, attach_name = ?, attach_file_type = ?, id_user = ?';
				$dbconn->Execute($strSQL, array($temp_attach_id, $new_file_name, $upload['type'], $id_user));
			}
			
			unlink($upload['tmp_name']);
		}
		else
		{
			mail_write($par, $lang['err']['upload_err'], $to, $subject, $body, $temp_attach_id);
			return;
		}
		
		mail_write($par, $lang['err']['attach_was_upload_succsessfully'], $to, $subject, $body, $temp_attach_id);
		return;
	}
	elseif ($act == 'delete_attach')
	{
		$id_attach = isset($_REQUEST['id_attach']) ? (int) $_REQUEST['id_attach'] : 0;
		
		$rs = $dbconn->Execute(
			'SELECT id, attach_name, id_mail_temp FROM '.MAILBOX_ATTACHES_TABLE.' WHERE id_user = ? AND id = ?',
			array($id_user, $id_attach));
		
		if ($rs->fields[0] > 0)
		{
			$attach_name = $rs->fields[1];
			$temp_attach_id = $rs->fields[2];
			
			$dbconn->Execute('DELETE FROM '.MAILBOX_ATTACHES_TABLE.' WHERE id_user = ? AND id = ?', array($id_user, $id_attach));
			
			unlink($config['site_path'].$settings['attaches_folder'].'/'.$attach_name);
			mail_write($par, $lang['err']['attach_was_deleted'], $to, $subject, $body, $temp_attach_id);
			return;
		}
		else
		{
			mail_inbox();
			return;
		}
	}
	
	// input validation
	if (strlen($to) == 0) {
		mail_write('err', $lang['err']['invalid_to'], $to, $subject, $body);
	}
	
	$to_arr = explode(';', $to);
	
	if (count($to_arr) == 0) {
		mail_write('err', $lang['err']['invalid_to'], $to, $subject, $body);
	}
	
	if (strlen($subject) == 0) {
		mail_write('err', $lang['err']['empty_subject'], $to, $subject, $body);
	}
	
	if (strlen($body) == 0) {
		mail_write('err', $lang['err']['empty_body'], $to, $subject, $body);
	}
	
	$err = BadWordsCont($_POST['subject'].' '.$_POST['body'], 1);
	
	if ($err) {
		mail_write('err', $err, $to, $subject, $body);
	}
	
	if (check_filter($_POST['subject'].' '.$_POST['body'])) {
		mail_write('err', $lang['err']['info_finding_1'], $to, $subject, $body);
	}
	
	// prepare recipients array
	$recipients = array();
	
	for ($i = 0; $i < count($to_arr); $i++)
	{
		$strSQL =
			'SELECT a.id, ig.id_enemy, a.root_user, a.fname
			   FROM '.USERS_TABLE.' a
		  LEFT JOIN '.BLACKLIST_TABLE.' ig ON ig.id_user = a.id AND ig.id_enemy = ?
			  WHERE a.id = ?';
		
		$rs = $dbconn->Execute($strSQL, array($id_user, $to_arr[$i]));
		
		//$recipients[$i]['login'] = $to_arr[$i];
		$recipients[$i]['id'] = (int) $rs->fields[0];
		$recipients[$i]['ignore'] = (int) $rs->fields[1];
		$recipients[$i]['root_user'] = (int) $rs->fields[2];
		$recipients[$i]['login'] = $rs->fields[3];
	}
	
	// check validity of recipients
	$err = '';
	$connection_error = false;
	
	foreach ($recipients as $recipient)
	{
		if ($recipient['root_user'])
		{
			if ($err) $err .= '<br>';
			$str_err = str_replace('[link]', '<a href="./contact.php">', $lang['err']['email_admin_err']);
			$str_err = str_replace('[/link]', '</a>', $str_err);
			$err .= $recipient['login'].': '.$str_err;
		}
		elseif (!$recipient['id'])
		{
			if ($err) $err .= '<br>';
			$err .= $recipient['login'].': '.$lang['err']['invalid_to'];
		}
		elseif ($recipient['ignore'])
		{
			if ($err) $err .= '<br>';
			$err .= $recipient['login'].': '.$lang['err']['email_black_list_err'];
		}
		elseif (getConnectedStatus($id_user, $recipient['id']) != CS_CONNECTED)
		{
			if ($err) $err .= '<br>';
			$err .= $recipient['login'].': '.$lang['err']['email_not_connected'];
			$connection_error = true;
		}
	}
	
	if ($err)
	{
		if ($connection_error) {
			if (!empty($_SESSION['permissions']['connection_invite'])) {
				$querystring = 'id='.$recipient['id'].'&ms=write';
				$err .= '<br><br>'.str_replace('#QUERYSTRING#', $querystring, $lang['err']['email_not_connected_connect_now']);
				if ($user[ AUTH_IS_PLATINUM ] && PLATINUM_GETS_UNLIMITED_CONNECTIONS) {
					$err .= $lang['err']['message_write_not_connected_platinum'];
				} elseif ($user[ AUTH_IS_ELITE ] && ELITE_GETS_UNLIMITED_CONNECTIONS) {
					$err .= $lang['err']['message_write_not_connected_elite'];
				}
			} else {
				// trial and inactive do not have any permission to write messages and can also be intercepted before the dispatcher,
				// but here we can show better messages
				if ($user[ AUTH_IS_TRIAL ]) {
					$err .= $lang['err']['connection_invite_permission_denied_trial'];
				} elseif ($user[ AUTH_IS_TRIAL_INACTIVE ]) {
					$err .= $lang['err']['connection_invite_permission_denied_trial_inactive'];
				} elseif ($user[ AUTH_IS_REGULAR ]) {
					$err .= $lang['err']['connection_invite_permission_denied_regular'];
				} elseif ($user[ AUTH_IS_REGULAR_INACTIVE ]) {
					$err .= $lang['err']['connection_permission_denied_regular_inactive'];
				} elseif ($user[ AUTH_IS_PLATINUM_INACTIVE ]) {
					$err .= $lang['err']['connection_invite_permission_denied_platinum_inactive'];
				} elseif ($user[ AUTH_IS_ELITE_INACTIVE ]) {
					$err .= $lang['err']['connection_invite_permission_denied_elite_inactive'];
				}
			}
		}
		$err .= '<br><br>'.$lang['err']['no_mesages_sent'];
		mail_write('err', $err, $to, $subject, $body, $temp_attach_id);
		return;
	}
	
	// send messages
	foreach ($recipients as $recipient)
	{
		// store message
		$dbconn->Execute(
			'INSERT INTO '.MAILBOX_TABLE.' SET
					id_to = ?, id_from = ?, subject = ?, body = ?, was_read = "0", deleted_to = "0", deleted_from = "0", date_creation = NOW()',
			array($recipient['id'], $id_user, $subject, nl2br($body)));
		
		$mail_id = $dbconn->Insert_ID();
		
		// GA_TRACKING
		if ($par == 'reply') {
			$_SESSION['ga_event_code'] = 'emailreplied';
		} else {
			$_SESSION['ga_event_code'] = 'emailsent';
		}
		
		// set mail kill date for sender
		$rs = $dbconn->Execute('SELECT status, amount, period FROM '.USER_MAILBOX_SETTINGS.' WHERE id_user = ?', array($id_user));
		$row = $rs->GetRowAssoc(false);
		if ($row['status']) {
			$dbconn->Execute(
				'UPDATE '.MAILBOX_TABLE.'
					SET kill_date_from = (NOW() + INTERVAL '.$row['amount'].' '.$row['period'].')
				  WHERE id = ?',
				array($mail_id));
		}
		unset($row);
		$rs->free();
		
		// set mail kill date for recipient
		$rs = $dbconn->Execute('SELECT status, amount, period FROM '.USER_MAILBOX_SETTINGS.' WHERE id_user = ?', array($recipient['id']));
		$row = $rs->GetRowAssoc(false);
		if ($row['status']) {
			$dbconn->Execute(
				'UPDATE '.MAILBOX_TABLE.'
					SET kill_date_to = (NOW() + INTERVAL '.$row['amount'].' '.$row['period'].')
				  WHERE id = ?',
				array($mail_id));
		}
		unset($row);
		$rs->free();
		
		// send notification email
		SendNotice($mail_id, $recipient['id']);
		
		$dbconn->Execute(
			'UPDATE '.MAILBOX_ATTACHES_TABLE.' SET id_mail = ? WHERE id_mail_temp = ? AND id_user = ?',
			array($mail_id, $temp_attach_id, $id_user));
	}
	
	mail_write('ok', $lang['err']['email_was_sent'], $to);
}


function mail_view($par, $err = '')
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	// message id
	$id = (int) $_GET['id'];
	
	$data['id_mail'] = $id;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$percent = new Percent($id_user);
	
	$smarty->assign('par', $par);
	
	if ($par == 'to') {
		// messages FOR me
		$id_str = ' AND id_to = ?';
	} else {
		// messages FROM me
		$id_str = ' AND id_from = ?';
	}
	
	// message
	$strSQL =
		'SELECT subject, body, id_from, id_to, DATE_FORMAT(date_creation, "'.$config['date_format'].' - %H:%i") AS date_creation
		   FROM '.MAILBOX_TABLE.'
		  WHERE id = ? ' . $id_str;
	
	$rs = $dbconn->Execute($strSQL, array($id, $id_user));
	$row = $rs->GetRowAssoc(false);
	
	$data['subject_msg'] = stripslashes($row['subject']);
	$data['text_msg'] = stripslashes($row['body']);
	$data['date_msg'] = $row['date_creation'];
	
	if ($par == 'to')
	{
		// messages for me
		$id_correspondent = $row['id_from'];
		
		// ecard check
		$rs = $dbconn->Execute('SELECT id FROM '.ECARDS_ORDERS_TABLE.' WHERE id_mail = ? AND status = "approved"', array($id));
		
		if (!$rs->EOF && $rs->fields[0] > 0)
		{
			$dbconn->Execute('UPDATE '.ECARDS_ORDERS_TABLE.' SET status = "readed" WHERE id = ?', array($rs->fields[0]));
			
			// send external email to inform about read ecard
			SendNotification($id_correspondent, $id_user, 'ecard_viewed');
			
			// send internal message
			$rs = $dbconn->Execute('SELECT site_language, gender FROM '.USERS_TABLE.' WHERE id = ?', array($id_correspondent));
			$site_language = $rs->fields[0];
			$gender = $rs->fields[1];
			$rs->free();
			
			// include language file
			$rs = $dbconn->Execute('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_language));
			$lang_mail = array();
			include $config['path_lang'].'mail/'.$rs->fields[0];
			$rs->free();
			
			// gender suffix
			$suffix = ($gender == GENDER_MALE) ? '_e' : '_t';
			
			$subject = str_replace('[SENDER_NAME]', $user[ AUTH_FNAME ], $lang_mail['ecard_viewed'.$suffix]['subject']);
			$message = str_replace('[SENDER_NAME]', $user[ AUTH_FNAME ], $lang_mail['ecard_viewed'.$suffix]['message']);
			
			$dbconn->Execute(
				'INSERT INTO '.MAILBOX_TABLE.' SET id_from = ?, id_to = ?, subject = ?, body = ?, date_creation = NOW(), was_read = "0"',
				array(ID_ADMIN, $id_correspondent, $subject, $message));
		}
	}
	else
	{
		// messages from me
		$id_correspondent = $row['id_to'];
		$data['name_msg'] = $user[ AUTH_LOGIN ];
	}
	
	$settings = GetSiteSettings(array('icon_male_default', 'icon_female_default', 'icons_folder',
		'show_users_group_str', 'use_friend_types','attaches_folder'));
	
	$rs = $dbconn->Execute('SELECT attach_name, attach_file_type FROM '.MAILBOX_ATTACHES_TABLE.' WHERE id_mail = ?', array($id));
	$i = 0;
	
	while(!$rs->EOF) {
		$data['attaches'][$i]['name'] = $rs->fields[0];
		$data['attaches'][$i]['file_type'] = $rs->fields[1];
		$data['attaches'][$i]['path'] = $config['server'].$config['site_root'].$settings['attaches_folder'].'/'.$rs->fields[0];
		$rs->MoveNext();
		$i++;
	}
	
	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];
	
	// correspondent info
	$strSQL =
		'SELECT a.id, a.login, a.fname, a.gender, a.date_birthday, a.big_icon_path, a.root_user,
				c.name AS country_name, d.name AS city_name, r.name AS region_name,
				sess.session, hot.id_friend, ig.id_enemy,
				k.gender AS gender_search, k.age_max, k.age_min
		   FROM '.USERS_TABLE.' a
	  LEFT JOIN '.COUNTRY_SPR_TABLE.' c ON c.id = a.id_country
	  LEFT JOIN '.CITY_SPR_TABLE.' d ON d.id = a.id_city
	  LEFT JOIN '.REGION_SPR_TABLE.' r ON r.id = a.id_region
	  LEFT JOIN '.ACTIVE_SESSIONS_TABLE.' sess ON a.id = sess.id_user
	  LEFT JOIN '.HOTLIST_TABLE.' hot ON hot.id_user = ? AND hot.id_friend = a.id
	  LEFT JOIN '.BLACKLIST_TABLE.' ig ON ig.id_user = ? AND ig.id_enemy = a.id
	  LEFT JOIN '.USER_MATCH_TABLE.' k ON k.id_user = a.id
		  WHERE a.id = ?';
	
	$rs = $dbconn->Execute($strSQL, array($id_user, $id_user, $id_correspondent));
	$row = $rs->GetRowAssoc(false);
	
	$data['id']			= $row['id'];
	$data['login']		= stripslashes($row['login']);
	$data['name']		= stripslashes($row['fname']);
	$data['gender']		= (int) $row['gender'];
	$data['status']		= $row['session'] ? $lang['status']['on'] : $lang['status']['off'];
	$data['root_user']	= $row['root_user'];
	
	if (!$data['root_user']) {
		$data['age']			= AgeFromBDate($row['date_birthday']);
		$data['age_max']		= $row['age_max'];
		$data['age_min']		= $row['age_min'];
		$data['gender_search']	= $lang['gender_search'][$row['gender_search']];
		$data['country']		= stripslashes($row['country_name']);
		$data['region']			= stripslashes($row['region_name']);
		$data['city']			= stripslashes($row['city_name']);
		$data['completion']		= $percent->GetAllPercentForUser($row['id']);
	}
	
	// get groups
	$sub_rs = $dbconn->Execute(
		'SELECT a.name
		   FROM '.USER_GROUP_TABLE.' b
	  LEFT JOIN '.GROUPS_TABLE.' a ON a.id = b.id_group
		  WHERE b.id_user = ?',
		  array($row['id']));
	
	$groups_arr = array();
	
	while (!$sub_rs->EOF) {
		$groups_arr[] = $sub_rs->fields[0];
		$sub_rs->MoveNext();
	}
	
	if (!empty($groups_arr)) {
		$data['group'] = implode(',', $groups_arr);
	}
	
	$big_icon_path = $row['big_icon_path'] ? $row['big_icon_path'] : $default_photos[$row['gender']];
	
	if ($big_icon_path && file_exists($config['site_path'].$settings['icons_folder'].'/'.$big_icon_path)) {
		$data['big_icon_path'] = $config['site_root'].$settings['icons_folder'].'/'.$big_icon_path;
	}
	
	$img_icon = $row['big_icon_path'] ? 1 : 0;
	
	$rs_photo = $dbconn->Execute(
		'SELECT COUNT(DISTINCT upload_path)
		   FROM '.USER_UPLOAD_TABLE.'
		  WHERE id_user = ? AND upload_type = "f" AND status = "1" AND allow IN ("1", "2")',
		  array($row['id']));
	
	$data['photo_count'] = $rs_photo->fields[0] + $img_icon;
	
	if ($par == 'to') {
		// messages for me
		$data['name_msg'] = $data['name'];
	}
	
	// hot list, connections and black list
	$data['hotlisted'] = empty($row['id_friend']) ? 0 : 1;
	$data['blacklisted'] = empty($row['id_enemy']) ? 0 : 1;
	
	// check connection status
	$data['connected_status'] = getConnectedStatus($row['id'], $user[ AUTH_ID_USER ]);
	
	// add to hotlist link
	if ($data['hotlisted'] == 0 && $data['blacklisted'] == 0) {
		if ($settings['use_friend_types']) {
			$data['add_hotlist_link'] = './hotlist.php?sel=addform&amp;id='.$row['id'];
		} else {
			$data['add_hotlist_link'] = './mailbox.php?sel=addhotlist&amp;id='.$row['id'].'&amp;id_msg='.$id.'&amp;ms='.$par;
		}
	}
	
	// add to blacklist link
	if ($data['hotlisted'] == 0 && $data['connected_status'] != CS_CONNECTED && $data['blacklisted'] == 0) {
		$data['add_blacklist_link'] = './mailbox.php?sel=addblacklist&amp;id='.$id.'&amp;id_user='.$row['id'].'&amp;ms='.$par;
	}
	
	// add to connections link
	if ($data['connected_status'] == CS_NOTHING && $data['blacklisted'] == 0) {
		if ($settings['use_friend_types']) {
			$data['add_connection_link'] = './connections.php?sel=addform&amp;id='.$row['id'];
		} else {
			$data['add_connection_link'] = './mailbox.php?sel=addconnection&amp;id='.$row['id'].'&amp;id_msg='.$id.'&amp;ms='.$par;
		}
	}
	
	// previous msg
	$strSQL =
		'SELECT m.id, u.login, u.fname, m.subject, m.body, DATE_FORMAT(m.date_creation, "'.$config['date_format'].' - %H:%i") AS date_creation
		   FROM '.MAILBOX_TABLE.' m
	  LEFT JOIN '.USERS_TABLE.' u on u.id = m.id_from
		  WHERE m.id_to IN ("'.$id_user.'", "'.$id_correspondent.'")
		    AND m.id_from IN ("'.$id_user.'", "'.$id_correspondent.'")
			AND m.id < ? AND m.deleted_to = "0"
	   ORDER BY m.id desc';
	
	$rs = $dbconn->Execute($strSQL, array($id));
	$row = $rs->GetRowAssoc(false);
	
	$data['name_msg_last'] = stripslashes($row['fname']);
	$data['subject_msg_last'] = stripslashes($row['subject']);
	$data['text_msg_last'] = stripslashes($row['body']);
	$data['date_msg_last'] = $row['date_creation'];
	
	if ($par == 'to') {
		$rs = $dbconn->Execute('SELECT was_read FROM '.MAILBOX_TABLE.' WHERE id = ? AND id_to = ?', array($id, $id_user));
		if ($rs->fields[0] != '1') {
			// mark msg as read
			$dbconn->Execute('UPDATE '.MAILBOX_TABLE.' SET was_read = "1" WHERE id = ? AND id_to = ?', array($id, $id_user));
			// GA_TRACKING
			$_SESSION['ga_event_code'] = 'emailopened';
		}
	}
	
	// mail counts
	$rs = $dbconn->Execute('SELECT COUNT(*) FROM '.MAILBOX_TABLE.' WHERE id_to = ? AND deleted_to = "0" AND was_read = "0"', array($id_user));
	$form['inbox_new'] = (int) $rs->fields[0];
	
	$rs = $dbconn->Execute('SELECT COUNT(*) FROM '.MAILBOX_TABLE.' WHERE id_to = ? AND deleted_to = "0"', array($id_user));
	$form['inbox_all'] = (int) $rs->fields[0];
	
	$rs = $dbconn->Execute('SELECT COUNT(*) FROM '.MAILBOX_TABLE.' WHERE id_from = ? AND deleted_from = "0"', array($id_user));
	$form['outbox_all'] = (int) $rs->fields[0];
	
	$form['show_users_group_str'] = $settings['show_users_group_str'];
	$form['use_friend_types'] = $settings['use_friend_types'];
	
	$form['err'] = $err;
	
	$smarty->assign('header', $lang['mailbox']);
	$smarty->assign('form', $form);
	$smarty->assign('data', $data);
	
	$smarty->display(TrimSlash($config['index_theme_path']).'/mailbox_view.tpl');
	exit;
}


function mail_ignore($par)
{
	global $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$ms = isset($_GET['ms']) ? $_GET['ms'] : '';
	
	if ($ms == 'to' || $ms == 'from') {
		$id_from = (int) $_GET['id_user'];
	} elseif ($ms == 'history') {
		$id_from = (int) $_GET['id'];
	} else {
		mail_inbox();
	}
	
	if (!empty($id_from))
	{
		$rs = $dbconn->Execute('DELETE FROM '.BLACKLIST_TABLE.' WHERE id_user = ? AND id_enemy = ?', array($id_user, $id_from));
		
		if ($par == 'add')
		{
			$rs = $dbconn->Execute('SELECT root_user FROM '.USERS_TABLE.' WHERE id = ?', array($id_from));
			if ($rs->fields[0] != '1') {
				$rs = $dbconn->Execute('INSERT INTO '.BLACKLIST_TABLE.' SET id_user = ?, id_enemy = ?', array($id_user, $id_from));
			}
		}
	}
	
	switch ($ms)
	{
		case 'to': mail_view('to'); break;
		case 'from': mail_view('from'); break;
		case 'history': mail_history(); break;
		default: mail_inbox();
	}
	
	return;
}


function mail_resend()
{
	global $lang, $dbconn, $user;
	
	// check permission
	if (empty($_SESSION['permissions']['email_compose'])) {
		AlertPage('email_compose');
		exit;
	}
	
	// check message count
	if (MM_CHECK_EMAIL_LIMIT && CheckMailCountPermission() == false) {
		mail_inbox($lang['err']['emails_limit']);
		return;
	}
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
	
	if ($id)
	{
		$rs = $dbconn->Execute('SELECT id_to, id_from, subject, body FROM '.MAILBOX_TABLE.' WHERE id = ? AND id_from = ?', array($id, $id_user));
		$row = $rs->GetRowAssoc(false);
		
		$id_to		= (int) $row['id_to'];
		#$id_from	= (int) $row['id_from'];
		$subject	= stripslashes($row['subject']);
		$body		= stripslashes($row['body']);
		
		$rs = $dbconn->Execute('SELECT COUNT(*) FROM '.BLACKLIST_TABLE.' WHERE id_user = ? AND id_enemy = ?', array($id_to, $id_user));
		$ignore = (int) $rs->fields[1];
		
		if ($id_to && $ignore == 0) {
			$dbconn->Execute(
				'INSERT INTO '.MAILBOX_TABLE.' SET
						id_to = ?, id_from = ?, subject = ?, body = ?, was_read = "0", deleted_to = "0", deleted_from = "0", date_creation = NOW()',
				 array($id_to, $id_user, $subject, $body));
			$err = $lang['err']['mailbox_success_resend'];
		}
	}
	
	if (!$err) {
		$err = $lang['err']['mailbox_failed_resend'];
	}
	
	mail_outbox($err);
	
	return;
}


function SendNotice($id_letter, $id_recipient)
{
	global $config, $smarty, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
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
	$content['subject']			= stripslashes($rs->fields[0]);
	$rs->free();
	
	// date and links
	$content['date']			= date('d-m-Y H:i');
	$content['link_read']		= $config['server'].$config['site_root'].'/mailbox.php?sel=inbox&amp;login_id='.$id_recipient.'&amp;token='.$token;
	$content['link_viewprofile']= $config['server'].$config['site_root'].'/viewprofile.php?id='.$id_user.'&amp;login_id='.$id_recipient.'&amp;token='.$token;
	$content['urls']			= GetUserEmailLinks();
	
	// sender data
	$rs = $dbconn->Execute(
		'SELECT id, login, fname, sname, email, date_birthday, icon_path, gender, id_country, id_city, id_region
		   FROM '.USERS_TABLE.'
		  WHERE status = "1" AND id = ?',
		array($id_user));
	$row = $rs->GetRowAssoc(false);
	$rs->free();
	
	$content['from_login']		= $row['login'];
	$content['from_fname']		= stripslashes($row['fname']);
	$content['from_sname']		= stripslashes($row['sname']);
	$content['from_id_country'] = $row['id_country'];
	$content['from_id_region']	= $row['id_region'];
	$content['from_id_city']	= $row['id_city'];
	$content['from_age']		= AgeFromBDate($row['date_birthday']);
	
	// base lang
	$_LANG_NEED_ID				= array();
	$_LANG_NEED_ID['country'][]	= (int) $row['id_country'];
	$_LANG_NEED_ID['region'][]	= (int) $row['id_region'];
	$_LANG_NEED_ID['city'][]	= (int) $row['id_city'];
	
	$smarty->assign('base_lang', GetBaseLang($_LANG_NEED_ID));
	
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
	$strSQL = 'SELECT login, fname, sname, email, gender, site_language FROM '.USERS_TABLE.' WHERE id = ?';
	$rs = $dbconn->Execute($strSQL, array($id_recipient));
	$row = $rs->GetRowAssoc(false);
	$rs->free();
	
	$content['login']		= $row['login'];
	$content['fname']		= stripslashes($row['fname']);
	$content['sname']		= stripslashes($row['sname']);
	$content['email']		= $row['email'];
	$content['gender']		= $row['gender'];
	
	// language
	$site_lang				= $row['site_language'];
	
	unset($row);
	
	// include language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	// gender suffix
	$suffix = ($content['gender'] == GENDER_MALE) ? '_e' : '_t';
	
	// subject
	$subject = $lang_mail['mailbox_subscribe'.$suffix]['subject'];
	
	// recipient
	$name_to = trim($content['fname'].' '.$content['sname']);
	
	SendMail($site_lang, $content['email'], $config['site_email'], $subject, $content,
			'mail_mailbox_subscribe_user', $attaches, $name_to, '', 'mailbox_subscribe', $content['gender']);
	
	return;
}


function CheckMailCountPermission()
{
	global $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$strSQL =
		' SELECT gp.permission_count
			FROM '.GROUPS_PERMISSIONS_TABLE.' gp
	  INNER JOIN '.PERMISSIONS_TABLE.' p ON gp.id_permission = p.id
	  INNER JOIN '.USER_GROUP_TABLE.' ug ON gp.id_group = ug.id_group
		   WHERE ug.id_user = ? AND p.permission_name = "emails"';
	
	$rs = $dbconn->Execute($strSQL, array($id_user));
	
	$limit = (int) $rs->fields[0];
	
	if ($limit <= 0) return true;
	
	$rs = $dbconn->Execute('SELECT COUNT(id) FROM '.MAILBOX_TABLE.' WHERE id_from = ?', array($id_user));
	
	$count = (int) $rs->fields[0];
	
	return ($count < $limit);
}


function savePeriod()
{
	global $user, $dbconn, $lang;
	
	$id_user = (int) $user[ AUTH_ID_USER ];
	
	if (isset($_REQUEST['delete_after_form_status']))
	{
		$amount = (int) $_REQUEST['delete_after_form_amount'];
		
		if ($amount < 1) {
			mail_inbox($lang['mailbox']['wrong_amount']);
		}
		
		switch ($_REQUEST['delete_after_form_period'])
		{
			case 'day': $period = 'day'; break;
			case 'month': $period = 'month'; break;
			case 'year': $period = 'year'; break;
			default: $period = 'month';
		}
		
		$found = $dbconn->GetOne('SELECT id_user FROM '.USER_MAILBOX_SETTINGS.' WHERE id_user = ?', array($id_user));
		
		if (!empty($found))
		{
			$dbconn->Execute('UPDATE '.USER_MAILBOX_SETTINGS.' SET status = "1", amount = ?, period = ? WHERE id_user = ?', array($amount, $period, $id_user));
		}
		else
		{
			$dbconn->Execute('INSERT INTO '.USER_MAILBOX_SETTINGS.' SET id_user = ?, status = "1", amount = ?, period = ?', array($id_user, $amount, $period));
		}
		
		$dbconn->Execute('UPDATE '.MAILBOX_TABLE.' SET kill_date_from = (date_creation + INTERVAL '.$amount.' '.$period.') WHERE id_from = ?', array($id_user));
		$dbconn->Execute('UPDATE '.MAILBOX_TABLE.' SET kill_date_to = (date_creation + INTERVAL '.$amount.' '.$period.') WHERE id_to = ?', array($id_user));
		
		cleanMailbox();
	}
	else
	{
		$dbconn->Execute('UPDATE '.USER_MAILBOX_SETTINGS.' SET status = "0" WHERE id_user = ?', array($id_user));
		$dbconn->Execute('UPDATE '.MAILBOX_TABLE.' SET kill_date_from = NULL WHERE id_from = ?', array($id_user));
		$dbconn->Execute('UPDATE '.MAILBOX_TABLE.' SET kill_date_to = NULL WHERE id_to = ?', array($id_user));
	}
	
	mail_inbox();
}
?>