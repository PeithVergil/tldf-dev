<?php

/**
 * Site user administration (create new user,  deleting, activating/deactivating, manage existing users...)
 *
 * @package DatingPro
 * @subpackage Admin Mode
 **/

include_once '../include/config.php';
include_once '../common.php';
include_once '../include/config_admin.php';
include_once '../include/functions_auth.php';
include_once '../include/functions_admin.php';
include_once '../include/class.phpmailer.php';
include_once '../include/functions_mail.php';
include_once '../include/functions_newsletter.php';
include_once '../include/functions_forum.php';
include_once '../include/class.lang.php';
include_once '../include/class.percent.php';

include_once '../include/config_index.php';
include_once '../include/functions_users.php';
include_once '../include/functions_mm.php';

$auth = auth_user();
login_check($auth);
IsFileAllowed($auth[0], GetRightModulePath(__FILE__), 'users');

$sel = isset($_REQUEST['sel']) ? $_REQUEST['sel'] : '';

if (isset($_REQUEST['no_invite'])) {
	unset($_SESSION['invite_users']);
}

switch ($sel) {
	case 'add':
		AddUser();
	break;
	case 'edit':
		EditForm('edit');
	break;
	case 'count':
		EditForm('count');
	break;
	case 'change':
		ChangeUser();
	break;
	case 'del':
		DelUser($_POST['id'] ? (int)$_POST['id'] : (int)$_GET['id']);
		exit;
		ListUser();
	break;
	case 'delete':
		DelUsers();
	break;
	case 'active':
		UpdateStatus();
	break;
	case 'top':
		Top10Form();
	break;
	case 'terminated_guy':
		ListTerminatedUser(1);
	break;
	case 'terminated_lady':
		ListTerminatedUser(2);
	break;
	case 'invite_search':
		ListInvitingUsers();
	break;
	default:
		FilteredUserList($sel);
	break;
}

exit;


function FilteredUserList($sel)
{
	//$_REQUEST['order']=2;
	switch ($sel) {
		case 'signup_guy':
			$_REQUEST['group'] = MM_SIGNUP_GUY_ID;
		break;
		
		case 'signup_lady':
			$_REQUEST['group'] = MM_SIGNUP_LADY_ID;
		break;
		
		case 'trial_guy':
			$_REQUEST['group'] = MM_TRIAL_GUY_ID;
			$_REQUEST['plat_applied'] = '0';
		break;
		
		case 'trial_lady':
			$_REQUEST['group'] = MM_TRIAL_LADY_ID;
			$_REQUEST['plat_applied'] = '0';
		break;
		
		case 'reg_guy':
			$_REQUEST['group'] = MM_REGULAR_GUY_ID;
			$_REQUEST['plat_applied'] = '0';
		break;
		
		case 'reg_lady':
			$_REQUEST['group'] = MM_REGULAR_LADY_ID;
			$_REQUEST['plat_applied'] = '0';
		break;
		
		case 'applied_platinum_guy':
			$_REQUEST['group'] = MM_PLATINUM_GUY_APPLIED_ID;
			//RS: no longer needed, remove later
			//$_REQUEST['group'] = MM_REGULAR_GUY_ID . ',' . MM_TRIAL_GUY_ID;
			//$_REQUEST['plat_applied'] = '1';
		break;
		
		case 'applied_platinum_lady':
			$_REQUEST['group'] = MM_PLATINUM_LADY_APPLIED_ID;
			//RS: no longer needed, remove later
			//$_REQUEST['group'] = MM_REGULAR_LADY_ID . ',' . MM_TRIAL_LADY_ID;
			//$_REQUEST['plat_applied'] = '1';
		break;
		
		case 'plat_guy':
			$_REQUEST['group'] = MM_PLATINUM_GUY_ID;
		break;
		
		case 'plat_lady':
			$_REQUEST['group'] = MM_PLATINUM_LADY_ID;
		break;
		
		case 'elite_guy':
			$_REQUEST['group'] = MM_ELITE_GUY_ID;
		break;
		
		case 'first_installment':
			$_REQUEST['group'] = MM_PLATINUM_LADY_FIRST_INS_ID;
		break;
		
		case 'second_installment':
			$_REQUEST['group'] = MM_PLATINUM_LADY_SECOND_INS_ID;
		break;
		
		case 'platinum_pending':
			$_REQUEST['group'] = MM_PLATINUM_LADY_PENDING_ID;
		break;
		
		
		// below groups are not in use
		/*
		case 'pen_trial_guy':
			$_REQUEST['group'] = MM_TRIAL_GUY_ID;
			$_REQUEST['s_pending'] = '0';
		break;
		
		case 'pen_trial_lady':
			$_REQUEST['group'] = MM_TRIAL_LADY_ID;
			$_REQUEST['s_pending'] = '0';
		break;
		
		case 'pen_reg_guy':
			$_REQUEST['group'] = MM_REGULAR_GUY_ID;
			$_REQUEST['s_pending'] = '0';
		break;
		
		case 'pen_reg_lady':
			$_REQUEST['group'] = MM_REGULAR_LADY_ID;
			$_REQUEST['s_pending'] = '0';
		break;
		
		case 'inact_trial_guy':
			$_REQUEST['group'] = MM_INACT_TRIAL_GUY_ID;
		break;
		
		case 'inact_trial_lady':
			$_REQUEST['group'] = MM_INACT_TRIAL_LADY_ID;
		break;
		
		case 'inact_reg_guy':
			$_REQUEST['group'] = MM_INACT_REGULAR_GUY_ID;
		break;
		
		case 'inact_reg_lady':
			$_REQUEST['group'] = MM_INACT_REGULAR_LADY_ID;
		break;
		
		case 'inact_plat_guy':
			$_REQUEST['group'] = MM_INACT_PLATINUM_GUY_ID;
		break;
		
		case 'inact_plat_lady':
			$_REQUEST['group'] = MM_INACT_PLATINUM_LADY_ID;
		break;
		
		case 'inact_elite_guy':
			$_REQUEST['group'] = MM_INACT_ELITE_GUY_ID;
		break;
		*/
	}
	
	ListUser();
}

function ListUser($err = '')
{
	global $smarty, $dbconn, $config, $config_admin, $lang, $sel;
	
	$file_name = 'admin_users.php';
	
	AdminMainMenu($lang['users']);
	
	$page = (isset($_REQUEST['page']) && (int)$_REQUEST['page'] > 0) ? (int)$_REQUEST['page'] : 1;
	$letter = (isset($_REQUEST['letter']) && (int)$_REQUEST['letter'] > 0) ? (int)$_REQUEST['letter'] : '*';
	$sorter = (isset($_REQUEST['sorter']) && (int)$_REQUEST['sorter'] > 0) ? (int)$_REQUEST['sorter'] : 5;
	$s_type = (isset($_REQUEST['s_type']) && (int)$_REQUEST['s_type'] > 0) ? (int)$_REQUEST['s_type'] : 1;
	$order = (isset($_REQUEST['order']) && (int)$_REQUEST['order']) > 0 ? (int)$_REQUEST['order'] : 2;
	
	$search = (isset($_REQUEST['search'])) ? strval($_REQUEST['search']) : '';
	$s_stat = (isset($_REQUEST['s_stat'])) ? strval($_REQUEST['s_stat']) : '';
	$group = (isset($_REQUEST['group'])) ? strval($_REQUEST['group']) : '';
	$s_gender = (isset($_REQUEST['s_gender'])) ? strval($_REQUEST['s_gender']) : '';
	
	$sel = isset($_REQUEST['sel']) ? $_REQUEST['sel'] : '';
	$pre_sel = isset($_REQUEST['pre_sel']) ? $_REQUEST['pre_sel'] : $sel;
	$s_pending = isset($_REQUEST['s_pending']) ? $_REQUEST['s_pending'] : '';
	$plat_applied = isset($_REQUEST['plat_applied']) ? $_REQUEST['plat_applied'] : '';
	
	$search_str = '';
	
	if (strval($search)) {
		$search = strip_tags($search);
		switch ($s_type) {
			case 1:
				$search_str = ' AND a.login LIKE "%' . $search . '%"';
			break;
			case 2:
				$search_str = ' AND a.fname LIKE "%' . $search . '%"';
			break;
			case 3:
				$search_str = ' AND a.sname LIKE "%' . $search . '%"';
			break;
			case 4:
				$search_str = ' AND a.email LIKE "%' . $search . '%"';
			break;
		}
	}
	
	if (strval($s_stat)) {
		switch ($s_stat) {
			case 'online':
				$strSQL =
					'SELECT DISTINCT u.id
					   FROM '.USERS_TABLE.' u
				 INNER JOIN '.ACTIVE_SESSIONS_TABLE.' s ON u.id = s.id_user
					  WHERE u.root_user != "1" AND u.guest_user != "1"';
				$rs = $dbconn->Execute($strSQL);
				$user_arr = array();
				$i = 0;
				while (!$rs->EOF) {
					$user_arr[$i] = $rs->fields[0];
					$i++;
					$rs->MoveNext();
				}
				if (count($user_arr)) {
					$user_str = implode(',', $user_arr);
					$search_str .= ' AND a.id IN ('.$user_str.')';
				}
			break;
			case 'reg_today':
				$search_str .= ' AND (a.date_registration + INTERVAL 1 DAY) > NOW()';
			break;
			case 'reg_week':
				$search_str .= ' AND (a.date_registration + INTERVAL 7 DAY) > NOW()';
			break;
			case 'reg_month':
				$search_str .= ' AND (a.date_registration + INTERVAL 30 DAY) > NOW()';
			break;
			case 'chat':
				$rs = $dbconn->Execute('SELECT DISTINCT userid FROM '.F_CHAT_CONNECTIONS_TABLE.' WHERE userid > "0"');
				$user_arr = array();
				$i = 0;
				while (!$rs->EOF) {
					$row = $rs->GetRowAssoc(false);
					$user_arr[$i] = $row['userid'];
					$i++;
					$rs->MoveNext();
				}
				if (count($user_arr)) {
					$user_str = implode(',', $user_arr);
					$search_str .= ' AND a.id IN ('.$user_str.')';
				}
			break;
		}
	}
	$smarty->assign('s_stat', $s_stat);
	
	// groups search
	if (!empty($group)) {
		$rs = $dbconn->Execute('SELECT DISTINCT id_user FROM '.USER_GROUP_TABLE.' WHERE id_group IN ('.$group.')');
		$user_arr = array();
		while (!$rs->EOF) {
			$user_arr[] = $rs->fields[0];
			$rs->MoveNext();
		}
		if (count($user_arr)) {
			$user_str = implode(',', $user_arr);
			$search_str .= ' AND a.id IN ('.$user_str.')';
		} else {
			$search_str .= ' AND a.id IN (0)';
		}
	}
	
	$smarty->assign('group', $group);
	
	// gender search
	if ((int) $s_gender) {
		$strSQL = 'SELECT DISTINCT id FROM '.USERS_TABLE.' WHERE gender = "'.$s_gender.'"';
		$rs = $dbconn->Execute($strSQL);
		$user_arr = array();
		$i = 0;
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$user_arr[$i] = $row['id'];
			$i++;
			$rs->MoveNext();
		}
		if (count($user_arr)) {
			$user_str = implode(',', $user_arr);
			$search_str .= ' AND a.id IN ('.$user_str.')';
		} else {
			$search_str .= ' AND a.id IN (0)';
		}
	}
	
	$smarty->assign('gender', $s_gender);
	
	// group filter
	$strSQL =
		'SELECT DISTINCT a.id, a.name, COUNT(b.id) AS count
		   FROM '.GROUPS_TABLE.' a
	  LEFT JOIN '.USER_GROUP_TABLE.' b ON b.id_group = a.id
		  WHERE (a.is_gender_group = "'.$config['use_gender_membership'].'" OR a.type="d" OR a.type="g" OR a.type="r" OR a.type="m")
	   GROUP BY a.id
	   ORDER BY a.name';
	$rs = $dbconn->Execute($strSQL);
	
	$groups = array();
	$i = 0;
	while (!$rs->EOF) {
		$groups[$i]['id'] = $rs->fields[0];
		$groups[$i]['name'] = stripslashes($rs->fields[1]);
		$groups[$i]['count'] = $rs->fields[2];
		$i++;
		$rs->MoveNext();
	}
	
	$smarty->assign('groups', $groups);
	
	// search form
	$types = array();
	for ($i = 0; $i < 4; $i++) {
		if ($s_type == ($i + 1)) {
			$types[$i]['sel'] = '1';
		}
		$types[$i]['value'] = $lang['users']['type_' . ($i + 1)];
	}
	$smarty->assign('types', $types);
	$smarty->assign('search', $search);
	
	// letter filter
	if (strval($letter) != '*') {
		$letter_str = ' lower(substring(a.login,1,1)) = "'.strtolower(chr($letter)).'"';
	} else {
		$letter_str = '';
	}
	
	$smarty->assign('letter', $letter);
	
	$form['order'] = $order;
	
	if ((int) $sorter)
	{
		$sorter_str = ' order by';
		
		switch ($sorter) {
			case '1':
				$sorter_str.=' a.login';
			break;
			case '2':
				$sorter_str.=' a.fname';
			break;
			case '3':
				$sorter_str.=' a.gender';
			break;
			case '4':
				$sorter_str.=' a.date_birthday';
			break;
			case '5':
				$sorter_str.=' a.date_registration';
			break;
			case '6':
				$sorter_str.=' a.date_last_seen';
			break;
			case '7':
				$sorter_str.=' a.status';
			break;
			case '8':
				$sorter_str.=' a.login_count';
			break;
		}
		
		switch ($order) {
			case '1':
				$form['new_order'] = '2';
				$sorter_str .= ' ASC ';
			break;
			case '2':
				$form['new_order'] = '1';
				$sorter_str .= ' DESC ';
			break;
			default:
				$form['new_order'] = '2';
				$sorter_str .= ' ASC ';
			break;
		}
	}
	else
	{
		$sorter_str = '';
	}
	
	$smarty->assign('sorter', $sorter);
	
	$status_str = '';
	
	if ($s_pending != '') {
		$status_str = ' a.status = "'.$s_pending.'"';
	}
	
	if ($search_str)
	{
		$where_str = 'WHERE a.id > 0 ' . $search_str . ' ';
		
		if ($status_str) {
			$where_str .= ' AND '.$status_str.' ';
		}
		
		if ($plat_applied !== '') {
			if ($plat_applied > 0) {
				$where_str .= ' AND a.mm_platinum_applied IS NOT NULL ';
			} else {
				$where_str .= ' AND a.mm_platinum_applied IS NULL ';
			}
		}
		
		if ($letter_str) {
			$where_str .= ' AND '.$letter_str.' ';
		}
	}
	else
	{
		if ($letter_str) {
			$where_str = ' WHERE '.$letter_str.' ';
		} else {
			$where_str = '';
		}
	}
	
	$rs = $dbconn->Execute('SELECT COUNT(*) FROM '.USERS_TABLE.' a '.$where_str);
	$num_records = $rs->fields[0];
	
	// page
	$lim_min = ($page - 1) * $config_admin['users_numpage'];
	$lim_max = $config_admin['users_numpage'];
	$limit_str = ' LIMIT '.$lim_min.', '.$lim_max;
	$smarty->assign('page', $page);
	
	if (isset($_SESSION['id_club']) && $_SESSION['invite_users'])
	{
		$strSQL =
			'SELECT id_user
			   FROM '.CLUB_USERS_TABLE.'
			  WHERE id_club = ?
		   GROUP BY id_user';
		$rs = $dbconn->Execute($strSQL, array($_SESSION['id_club']));
		while (!$rs->EOF) {
			$users_not_invite[] = $rs->fields[0];
			$rs->MoveNext();
		}
		
		$strSQL =
			'SELECT id_user
			   FROM '.CLUB_INVITES_TABLE.'
			  WHERE id_club = ?
		   GROUP BY id_user';
		$rs = $dbconn->Execute($strSQL, array($_SESSION['id_club']));
		while (!$rs->EOF) {
			$users_not_invite[] = $rs->fields[0];
			$rs->MoveNext();
		}
		
		$users_not_invite = array_unique($users_not_invite);
	}
	
	// query
	$strSQL =
		'SELECT a.id, a.fname, a.sname, a.status, a.login, a.gender, a.email, a.date_birthday,
				DATE_FORMAT(a.date_last_seen, "' . $config['date_format'] . '") AS date_last_seen,
				DATE_FORMAT(a.date_registration, "' . $config['date_format'] . '") AS date_registration,
				a.login_count, a.root_user, a.guest_user
		   FROM '.USERS_TABLE.' a ' . $where_str . ' ' . $sorter_str . ' ' . $limit_str;
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$user = array();
	
	if (!$rs->EOF) {
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$user[$i]['number']				= ($page - 1) * $config_admin['users_numpage'] + ($i + 1);
			$user[$i]['id']					= $row['id'];
			$user[$i]['name']				= stripslashes($row['fname'] . ' ' . $row['sname']);
			$user[$i]['nick']				= $row['login'];
			$user[$i]['gender']				= $lang['gender'][$row['gender']];
			$user[$i]['status']				= (int) $row['status'];
			$user[$i]['email']				= $row['email'];
			$user[$i]['age']				= AgeFromBDate($row['date_birthday']);
			$user[$i]['date_rigistration']	= $row['date_registration'];
			$user[$i]['last_login']			= $row['date_last_seen'];
			$user[$i]['login_count']		= (int) $row['login_count'];
			$user[$i]['edit_link']			= $file_name . '?sel=edit&pre_sel=' . $pre_sel . '&page=' . $page . '&id=' . $row['id'] . '&letter=' . $letter . '&search=' . $search . '&s_type=' . $s_type . '&sorter=' . $sorter;
			$user[$i]['delete_link']		= $file_name . '?sel=del&page=' . $page . '&id=' . $row['id'] . '&letter=' . $letter . '&search=' . $search . '&s_type=' . $s_type . '&sorter=' . $sorter;
			$user[$i]['descr_link']			= './admin_user_description.php?id=' . $row['id'];
			$user[$i]['personal_link']		= './admin_user_personality.php?id=' . $row['id'];
			$user[$i]['upload_link']		= './admin_user_upload.php?id=' . $row['id'];
			$user[$i]['perfect_link']		= './admin_user_perfect.php?id=' . $row['id'];
			$user[$i]['comunicate']			= './admin_comunicate.php?id=' . $row['id'];
			$user[$i]['root_user']			= $row['root_user'] ? $row['root_user'] : $row['guest_user'];
			$user[$i]['confirm']			= addslashes($lang['confirm']['users']);
			$user[$i]['guest_user']			= $row['guest_user'];
			
			$strSQL = 'SELECT COUNT(id) FROM '.USER_REFER_TABLE.' WHERE id_refer = "' . $user[$i]['id'] . '"';
			$user[$i]['count_invited'] = $dbconn->GetOne($strSQL);
			if ($user[$i]['count_invited'] > 0) {
				$user[$i]['invited_link'] = $config['server'] . $config['site_root'] . '/admin/admin_pays.php?sel=user&filter=referred&id=' . $user[$i]['id'];
				$user[$i]['invited_link_name'] = str_replace('[n]', $user[$i]['count_invited'], $lang['refer_friend']['invited_link_name']);
			}
			
			//VP fetching id_group and use_active status
			$strSQL = 'SELECT id_group FROM ' . USER_GROUP_TABLE . ' WHERE id_user = "' . $user[$i]['id'] . '"';
			$user[$i]['id_group'] = $dbconn->GetOne($strSQL);
			if ($user[$i]['id_group'] > 0) {
				$user[$i]['use_active'] = ($user[$i]['id_group'] == MM_SIGNUP_GUY_ID || $user[$i]['id_group'] == MM_SIGNUP_GUY_ID) ? false : true;
			}
			if (isset($_SESSION['id_club']) && $_SESSION['invite_users'] && !in_array($user[$i]['id'], $users_not_invite) && !$user[$i]['guest_user']) {
				$user[$i]['invite_link'] = 'admin_club.php?sel=invite&id_user=' . $user[$i]['id'] . '&id_club=' . $_SESSION['id_club'];
			}
			$rs->MoveNext();
			$i++;
		}
		$param = $file_name . '?sel=' . $sel . '&letter=' . $letter . '&search=' . $search . '&s_type=' . $s_type . '&s_stat=' . $s_stat . '&sorter=' . $sorter . '&group=' . $group . '&order=' . $form['order'] . '&s_gender=' . $s_gender . '&';
		$smarty->assign('links', GetLinkStr($num_records, $page, $param, $config_admin['users_numpage']));
		$smarty->assign('user', $user);
	}
	
	// letter link
	$param_letter = $file_name . '?sel=' . $sel . '&sorter=' . $sorter . '&order=' . $form['order'] . '&letter=';
	$letter_links = LetersLink_eng($param_letter, $letter);
	$smarty->assign('letter_links', $letter_links);
	
	$form['hiddens'] = '<input type="hidden" name="sel" value="' . $sel . '">';
	$form['hiddens'].= '<input type="hidden" name="pre_sel" value="' . $pre_sel . '">';
	$form['hiddens'].= '<input type="hidden" name="page" value="' . $page . '">';
	$form['hiddens'].= '<input type="hidden" name="letter" value="' . $letter . '">';
	$form['hiddens'].= '<input type="hidden" name="search" value="' . $search . '">';
	$form['hiddens'].= '<input type="hidden" name="s_type" value="' . $s_type . '">';
	$form['hiddens'].= '<input type="hidden" name="s_stat" value="' . $s_stat . '">';
	$form['hiddens'].= '<input type="hidden" name="sorter" value="' . $sorter . '">';
	$form['hiddens'].= '<input type="hidden" name="group" value="' . $group . '">';
	$form['hiddens'].= '<input type="hidden" name="order" value="' . $order . '">';
	
	$form['action'] = $file_name;
	
	$form['err'] = $err;
	
	$smarty->assign('add_link', $file_name . '?sel=add&page=' . $page);
	$smarty->assign('topten_link', $file_name . '?sel=top');
	$smarty->assign('header', $lang['users']);
	$smarty->assign('button', $lang['button']);
	$smarty->assign('alerts', $lang['alerts']);
	$smarty->assign('form', $form);
	
	if (isset($_SESSION['id_club'])) {
		$smarty->assign('invite_users', $_SESSION['invite_users']);
	}
	
	$smarty->assign('sel', $sel);
	$smarty->display(TrimSlash($config['admin_theme_path']) . '/admin_user_table.tpl');
	exit;
}

function ListTerminatedUser($gen)
{
	global $smarty, $dbconn, $config, $config_admin, $lang, $sel;
	
	$file_name = 'admin_users.php';
	
	$err = '';
	
	AdminMainMenu($lang['users']);
	
	$page			= (isset($_REQUEST['page']) && (int) $_REQUEST['page']) > 0 ? (int) $_REQUEST['page'] : 1;
	$letter			= (isset($_REQUEST['letter']) && (int) $_REQUEST['letter'] > 0) ? (int) $_REQUEST['letter'] : '*';
	$sorter			= (isset($_REQUEST['sorter']) && (int) $_REQUEST['sorter'] > 0) ? (int) $_REQUEST['sorter'] : 5;
	$s_type			= (isset($_REQUEST['s_type']) && (int) $_REQUEST['s_type'] > 0) ? (int) $_REQUEST['s_type'] : 1;
	$order			= (isset($_REQUEST['order']) && (int) $_REQUEST['order'] > 0) ? (int) $_REQUEST['order'] : 1;
	$plat_applied	= (isset($_REQUEST['plat_applied']) && (int) $_REQUEST['plat_applied'] > 0) ? (int) $_REQUEST['plat_applied'] : 1;
	
	$search			= (isset($_REQUEST['search'])) ? strval($_REQUEST['search']) : '';
	$s_stat			= (isset($_REQUEST['s_stat'])) ? strval($_REQUEST['s_stat']) : '';
	$s_gender		= (isset($_REQUEST['s_gender'])) ? strval($_REQUEST['s_gender']) : '';
	$group			= (isset($_REQUEST['group'])) ? strval($_REQUEST['group']) : '';
	
	$sel = isset($_REQUEST['sel']) ? $_REQUEST['sel'] : '';
	$pre_sel = isset($_REQUEST['pre_sel']) ? $_REQUEST['pre_sel'] : $sel;
	
	$search_str = '';
	
	if (strval($search)) {
		$search = strip_tags($search);
		switch ($s_type) {
			case 1:
				$search_str = ' and a.fname like "%' . $search . '%"';
			break;
			case 2:
				$search_str = ' and a.fname like "%' . $search . '%"';
			break;
			case 3:
				$search_str = ' and a.sname like "%' . $search . '%"';
			break;
			case 4:
				$search_str = ' and a.email like "%' . $search . '%"';
			break;
		}
	}
	
	// search form
	$types = array();
	for ($i = 0; $i < 4; $i++) {
		if ($s_type == ($i + 1)) {
			$types[$i]['sel'] = '1';
		}
		$types[$i]['value'] = $lang['users']['type_' . ($i + 1)];
	}
	$smarty->assign('types', $types);
	$smarty->assign('search', $search);
	
	// letter
	if (strval($letter) != '*') {
		$letter_str = ' lower(substring(a.login,1,1)) = "' . strtolower(chr($letter)) . '"';
	} else {
		$letter_str = '';
	}
	
	$smarty->assign('letter', $letter);
	
	$form['order'] = $order;
	
	if ((int) $sorter) {
		$sorter_str = ' order by';
		
		switch ($sorter) {
			case '1':
				$sorter_str.=' a.fname';
			break;
			case '2':
				$sorter_str.=' a.id_group';
			break;
			case '3':
				$sorter_str.=' a.date_birthday';
			break;
			case '4':
				$sorter_str.=' a.email';
			break;
			case '5':
				$sorter_str.=' a.phone_number';
			break;
			case '6':
				$sorter_str.=' a.mobile_number';
			break;
			case '7':
				$sorter_str.=' a.date_registration';
			break;
			case '8':
				$sorter_str.=' a.date_termination';
			break;
			case '9':
				$sorter_str.=' a.login_count';
			break;
		}
		
		switch ($order) {
			case '1':
				$form['new_order'] = '2';
				$sorter_str .=' ASC ';
			break;
			case '2':
				$form['new_order'] = '1';
				$sorter_str .=' DESC ';
			break;
			default:
				$form['new_order'] = '2';
				$sorter_str .=' ASC ';
			break;
		}
	}
	else
	{
		$sorter_str = '';
	}
	
	$smarty->assign('sorter', $sorter);
	
	$status_str = '';
	
	if ($search_str)
	{
		$where_str = 'WHERE a.id > 0 ' . $search_str . ' ';
		
		if ($status_str) {
			$where_str .= ' AND ' . $status_str . ' ';
		}
		
		if ($plat_applied !== '') {
			if ($plat_applied > 0) {
				$where_str .= ' AND a.date_platinum_applied IS NOT NULL ';
			} else {
				$where_str .= ' AND a.date_platinum_applied IS NULL ';
			}
		}
		
		if ($letter_str) {
			$where_str .= ' AND ' . $letter_str . ' ';
		}
	}
	else
	{
		if ($letter_str) {
			$where_str = ' WHERE ' . $letter_str . ' ';
		} else {
			$where_str = '';
		}
	}
	
	if ($where_str == '') {
		$where_str = ' WHERE a.gender = ' . $gen . ' ';
	} else {
		$where_str .= ' AND a.gender = ' . $gen . ' ';
	}
	
	$strSQL = 'SELECT COUNT(*) FROM '.USER_TERMINATED_TABLE.' AS a ' . $where_str . ' ' . $sorter_str;
	$rs = $dbconn->Execute($strSQL);
	$num_records = $rs->fields[0];
	
	// page
	$lim_min = ($page - 1) * $config_admin['users_numpage'];
	$lim_max = $config_admin['users_numpage'];
	$limit_str = ' limit ' . $lim_min . ', ' . $lim_max;
	$smarty->assign('page', $page);
	
	// query
	$strSQL =
		'SELECT a.id, a.id_user, a.id_group, a.fname, a.sname, a.status, a.gender,
				a.date_birthday, a.email, a.phone_number, a.mobile_number, a.date_registration,
				a.date_platinum_applied, a.date_termination, a.login_count, a.comment
		   FROM '.USER_TERMINATED_TABLE.' AS a ' . $where_str . ' ' . $sorter_str . $limit_str;
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$user = array();
	
	if (!$rs->EOF) {
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$user[$i]['number']					= ($page - 1) * $config_admin['users_numpage'] + ($i + 1);
			$user[$i]['id']						= $row['id'];
			$user[$i]['id_user']				= $row['id_user'];
			$user[$i]['id_group']				= $row['id_group'];
			$user[$i]['name']					= stripslashes($row['fname'] . ' ' . $row['sname']);
			$user[$i]['status']					= $row['status'];
			$user[$i]['gender']					= $lang['gender'][$row['gender']];
			$user[$i]['age']					= AgeFromBDate($row['date_birthday']);
			$user[$i]['email']					= $row['email'];
			$user[$i]['phone_number']			= $row['phone_number'];
			$user[$i]['mobile_number']			= $row['mobile_number'];
			$user[$i]['date_rigistration']		= $row['date_registration'];
			$user[$i]['date_platinum_applied']	= $row['date_platinum_applied'];
			$user[$i]['date_termination']		= $row['date_termination'];
			$user[$i]['login_count']			= (int) $row['login_count'];
			$user[$i]['comment']				= (int) $row['comment'];
			
			$user[$i]['group_name'] = $dbconn->getOne('SELECT name FROM '.GROUPS_TABLE.' WHERE id = ?', array($row['id_group']));
			
			$rs->MoveNext();
			$i++;
		}
		
		$param = $file_name . '?sel=' . $sel . '&letter=' . $letter . '&search=' . $search . '&s_type=' . $s_type . '&s_stat=' . $s_stat . '&sorter=' . $sorter . '&group=' . $group . '&order=' . $form['order'] . '&s_gender=' . $s_gender . '&';
		$smarty->assign('links', GetLinkStr($num_records, $page, $param, $config_admin['users_numpage']));
		$smarty->assign('user', $user);
	}
	
	// letter link
	$param_letter = $file_name . '?sel=' . $sel . '&sorter=' . $sorter . '&order=' . $form['order'] . '&letter=';
	$letter_links = LetersLink_eng($param_letter, $letter);
	$smarty->assign('letter_links', $letter_links);
	
	$form['hiddens'] = '<input type=hidden name=sel value=' . $sel . '>';
	$form['hiddens'] .= '<input type=hidden name=pre_sel value=' . $pre_sel . '>';
	$form['hiddens'] .= '<input type=hidden name=page value=' . $page . '>';
	$form['hiddens'] .= '<input type=hidden name=letter value=' . $letter . '>';
	$form['hiddens'] .= '<input type=hidden name=search value=' . $search . '>';
	$form['hiddens'] .= '<input type=hidden name=s_type value=' . $s_type . '>';
	$form['hiddens'] .= '<input type=hidden name=s_stat value=' . $s_stat . '>';
	$form['hiddens'] .= '<input type=hidden name=sorter value=' . $sorter . '>';
	$form['hiddens'] .= '<input type=hidden name=group value=' . $group . '>';
	$form['hiddens'] .= '<input type=hidden name=order value=' . $order . '>';
	
	$form['action'] = $file_name;
	
	$form['err'] = $err;
	
	$smarty->assign('header', $lang['users']);
	$smarty->assign('button', $lang['button']);
	$smarty->assign('alerts', $lang['alerts']);
	$smarty->assign('form', $form);
	
	$smarty->assign('sel', $sel);
	$smarty->display(TrimSlash($config['admin_theme_path']) . '/admin_user_terminated.tpl');
	exit;
}


function EditForm($par = '', $err = '', $data = array())
{
	global $smarty, $dbconn, $config, $page, $lang;
	
	$file_name = 'admin_users.php';
	
	AdminMainMenu($lang['users']);
	
	if ($err) {
		$form['err'] = $err;
	}
	
	$rs = $dbconn->Execute(
		'SELECT name, value
		   FROM '.SETTINGS_TABLE.'
		  WHERE name IN ("min_age_limit", "max_age_limit")');
	
	while (!$rs->EOF) {
		$settings[$rs->fields[0]] = $rs->fields[1];
		$rs->MoveNext();
	}
	
	$page	= isset($_REQUEST['page']) ? (int) $_REQUEST['page'] : 1;
	$letter = (isset($_REQUEST['letter']) && (int) $_REQUEST['letter']) > 0 ? (int) $_REQUEST['letter'] : '*';
	$search = (isset($_REQUEST['search'])) ? strval($_REQUEST['search']) : '';
	$s_type = isset($_REQUEST['s_type']) ? (int) $_REQUEST['s_type'] : 1;
	$s_stat = isset($_REQUEST['s_stat']) ? $_REQUEST['s_stat'] : '';
	$sorter = isset($_REQUEST['sorter']) ? (int) $_REQUEST['sorter'] : 1;
	$pre_sel= isset($_REQUEST['pre_sel']) ? $_REQUEST['pre_sel'] : '';
	
	if ($par == 'edit' || $par == 'del')
	{
		$id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : '';
		
		$rs = $dbconn->Execute('SELECT root_user, guest_user FROM ' . USERS_TABLE . ' WHERE id = "' . $id . '"');
		$row = $rs->GetRowAssoc(false);
		$data['root'] = $row['root_user'] ? $row['root_user'] : $row['guest_user'];
		
		if (!$id) {
			ListUser();
			return;
		}
		
		if (!$err) {
			/**
			 * read user from database
			 **/
			//VP included magic array
			$use_field = array();
			$mandatory = array();
			include '../customize/profile_switchboard.php';
			$smarty->assign('use_field', $use_field);
			$smarty->assign('mandatory', $mandatory);
			
			$strSQL =
				'SELECT u.id_group, g.name
				   FROM '.USER_GROUP_TABLE.' AS u
			  LEFT JOIN '.GROUPS_TABLE.' AS g ON u.id_group = g.id
				  WHERE id_user = ?';
			$rs = $dbconn->Execute($strSQL, array($id));
			
			$group = strval($rs->fields[0]);
			$group_name = strval($rs->fields[1]);
			
			//$mandatory_level = ($group == MM_SIGNUP_GUY_ID || $group == MM_SIGNUP_LADY_ID) ? SB_EDIT : SB_EDIT;
			$mandatory_level = SB_REGISTRATION;
			//echo "(VIEW)Mandatory Level : ".$mandatory_level;
			$smarty->assign('mandatory_level', $mandatory_level);
			$use_active = ($group == MM_SIGNUP_GUY_ID || $group == MM_SIGNUP_LADY_ID) ? false : true;
			$smarty->assign('use_active', $use_active);
			
			$strSQL =
					'SELECT u.login, u.fname, u.sname, u.status, u.platinum_verified, u.email, u.date_birthday, u.gender, u.couple,
							u.couple_user, u.id_country, u.id_city, u.id_region, u.zipcode, u.comment, u.headline, u.big_icon_path,
							u.id_nationality, u.id_language_1, u.id_language_2, u.id_language_3, u.id_weight, u.id_height, u.root_user,
							u.guest_user, um.gender AS gender_search, um.couple AS couple_search, um.age_min, um.age_max,
							um.id_relationship, u.site_language, u.phone,
							u.mm_nickname, u.mm_id_number, u.mm_id_type, u.mm_contact_phone_number, u.mm_contact_mobile_number,
							u.mm_marital_status,u.mm_place_of_birth, u.mm_city, u.mm_address_1, u.mm_address_2, u.mm_address_3,
							u.mm_level_of_english, u.mm_employment_status, u.mm_business_name, u.mm_employer_name, 
							u.mm_job_position, u.mm_work_address, u.mm_work_phone_number,
							u.mm_ref_1_first_name, u.mm_ref_1_last_name, u.mm_ref_1_relationship, u.mm_ref_1_phone_number, 
							u.mm_ref_2_first_name, u.mm_ref_2_last_name, u.mm_ref_2_relationship, u.mm_ref_2_phone_number,
							u.about_me, u.what_i_do, u.my_idea, u.hoping_to_find, u.mm_platinum_applied,
							u.mm_best_call_time_weekdays, u.mm_best_call_time_saturdays, u.mm_best_call_time_sundays, u.mm_platinum_submit_comment,
							u.chk_background, u.chk_marital_status, u.chk_work_history, u.chk_interview_photo,
							u.chk_date, u.chk_staff, u.chk_comment,
							up.hide_online, up.promotion_1, up.promotion_2, up.promotion_3, up.featured_land, up.featured_home, up.visible_lady, up.visible_guy,
							up.vis_lady_1, up.vis_lady_2, up.vis_lady_3, up.vis_lady_4, up.vis_lady_5,
							up.vis_guy_1, up.vis_guy_2, up.vis_guy_3, up.vis_guy_4, up.vis_guy_5
					   FROM '.USERS_TABLE.' AS u
				  LEFT JOIN '.USER_MATCH_TABLE.' AS um ON um.id_user = u.id
				  LEFT JOIN '.USER_PRIVACY_SETTINGS.' AS up ON up.id_user = u.id
					  WHERE u.id = ?';
			
			$rs = $dbconn->Execute($strSQL, array($id));
			$row = $rs->GetRowAssoc(false);
			
			// login
			$data['id']				= $id;
			$data['login']			= $row['login'];
			$data['big_icon_path']	= $row['big_icon_path'];
			$data['status']			= $row['status'];
			
			if ($group == MM_TRIAL_GUY_ID || $group == MM_TRIAL_LADY_ID || $group == MM_REGULAR_GUY_ID || $group == MM_REGULAR_LADY_ID) {
				if ($row['status'] == '0') {
					$group_name .= ' (Pending)';
				}
			}
			
			//This is not needed there is a separate group for applied now
//			if (($group == MM_TRIAL_GUY_ID || $group == MM_REGULAR_GUY_ID)) {
//				$group_name = 'Platinum Applied Guy';
//			}
//			if (($group == MM_TRIAL_LADY_ID || $group == MM_REGULAR_LADY_ID)) {
//				$group_name = 'Platinum Applied Lady';
//			}
			if ($row['platinum_verified'] == "2") {
				$group_name .= " (Platinum Rejected)";
			}
			
			$data['group_name']					= $group_name;
			$data['group_id']					= $group;
			// background checks
			$data['chk_background']				= $row['chk_background'];
			$data['chk_marital_status']			= $row['chk_marital_status'];
			$data['chk_work_history']			= $row['chk_work_history'];
			$data['chk_interview_photo']		= $row['chk_interview_photo'];
			$data['chk_year']					= (int) substr($row['chk_date'], 0, 4);
			$data['chk_month']					= (int) substr($row['chk_date'], 5, 2);
			$data['chk_day']					= (int) substr($row['chk_date'], 8, 2);
			$data['chk_staff']					= stripslashes($row['chk_staff']);
			$data['chk_comment']				= $row['chk_comment'];
			$data['platinum_verified']			= $row['platinum_verified'];
			$data['is_applied']					= $row['mm_platinum_applied'];
			
			// personal info
			$data['fname']						= stripslashes($row['fname']);
			$data['sname']						= stripslashes($row['sname']);
			$data['mm_nickname']				= stripslashes($row['mm_nickname']);
			$data['gender']						= $row['gender'];
			$data['mm_marital_status']			= $row['mm_marital_status'];
			$data['b_year']						= intval(substr($row['date_birthday'], 0, 4));
			$data['b_month']					= intval(substr($row['date_birthday'], 5, 2));
			$data['b_day']						= intval(substr($row['date_birthday'], 8, 2));
			$data['mm_place_of_birth']			= stripslashes($row['mm_place_of_birth']);
			$data['id_nationality']				= $row['id_nationality'];
			$data['mm_id_number']				= stripslashes($row['mm_id_number']);
			$data['mm_id_type']					= stripslashes($row['mm_id_type']);
			$data['id_weight']					= $row['id_weight'];
			$data['id_height']					= $row['id_height'];
			$data['headline']					= stripslashes($row['headline']);
			
			// contact info
			$data['email']						= $row['email'];
			$data['mm_contact_phone_number']	= stripslashes($row['mm_contact_phone_number']);
			$data['mm_contact_mobile_number']	= stripslashes($row['mm_contact_mobile_number']);
			$data['phone']						= $row['phone'];
			
			// best call time
			$data['mm_best_call_time_weekdays']	= stripslashes($row['mm_best_call_time_weekdays']);
			$data['mm_best_call_time_saturdays']= stripslashes($row['mm_best_call_time_saturdays']);
			$data['mm_best_call_time_sundays']	= stripslashes($row['mm_best_call_time_sundays']);
			$data['mm_platinum_submit_comment']	= stripslashes($row['mm_platinum_submit_comment']);
			
			// search criteria
			$data['gender_search']				= (int) $row['gender_search'];
			$data['couple_search']				= (int) $row['couple_search'];
			$data['age_min']					= (int) $row['age_min'];
			$data['age_max']					= (int) $row['age_max'];
			
			// address info
			$data['id_country']					= $row['id_country'];
			$data['id_region']					= $row['id_region'];
			$data['id_city']					= $row['id_city'];
			$data['mm_city']					= stripslashes($row['mm_city']);
			$data['zipcode']					= stripslashes($row['zipcode']);
			$data['mm_address_1']				= stripslashes($row['mm_address_1']);
			$data['mm_address_2']				= stripslashes($row['mm_address_2']);
			$data['mm_address_3']				= stripslashes($row['mm_address_3']);
			
			// language info
			$data['site_language']				= $row['site_language'];
			$data['id_language_1']				= $row['id_language_1'];
			$data['id_language_2']				= $row['id_language_2'];
			$data['id_language_3']				= $row['id_language_3'];
			$data['mm_level_of_english']		= $row['mm_level_of_english'];
			
			// employment info
			$data['mm_employment_status']		= $row['mm_employment_status'];
			$data['mm_business_name']			= stripslashes($row['mm_business_name']);
			$data['mm_employer_name']			= stripslashes($row['mm_employer_name']);
			$data['mm_job_position']			= stripslashes($row['mm_job_position']);
			$data['mm_work_address']			= stripslashes($row['mm_work_address']);
			$data['mm_work_phone_number']		= stripslashes($row['mm_work_phone_number']);
			
			// references
			$data['mm_ref_1_first_name']		= stripslashes($row['mm_ref_1_first_name']);
			$data['mm_ref_1_last_name']			= stripslashes($row['mm_ref_1_last_name']);
			$data['mm_ref_1_relationship']		= stripslashes($row['mm_ref_1_relationship']);
			$data['mm_ref_1_phone_number']		= stripslashes($row['mm_ref_1_phone_number']);
			$data['mm_ref_2_first_name']		= stripslashes($row['mm_ref_2_first_name']);
			$data['mm_ref_2_last_name']			= stripslashes($row['mm_ref_2_last_name']);
			$data['mm_ref_2_relationship']		= stripslashes($row['mm_ref_2_relationship']);
			$data['mm_ref_2_phone_number']		= stripslashes($row['mm_ref_2_phone_number']);
			
			// platinum groups
			$data['platinumArr'] 				= array(
												MM_PLATINUM_LADY_ID,
												MM_PLATINUM_GUY_ID,
												MM_PLATINUM_LADY_PENDING_ID,
												MM_PLATINUM_LADY_SECOND_INS_ID,
												MM_PLATINUM_LADY_FIRST_INS_ID
											);
			
			// couples
			$data['couple']						= $row['couple'];
			$data['couple_user']				= $row['couple_user'];
			$data['couple_login']				= '';
			
			if ($data['couple_user']) {
				$strSQL = 'SELECT login, gender, date_birthday, couple_user FROM ' . USERS_TABLE . ' WHERE id=' . $data['couple_user'];
				$rs_couple = $dbconn->Execute($strSQL);
				$data['couple_login']	= $rs_couple->fields[0];
				$data['couple_link']	= 'viewprofile.php?id=' . $data['couple_user'];
				$data['couple_gender']	= $lang['gender'][$rs_couple->fields[1]];
				$data['couple_age']		= AgeFromBDate($rs_couple->fields[2]);
				$data['couple_accept']	= $rs_couple->fields[3] == $id ? 1 : 0;
			}
			
			// root user
			$data['root']			= $row['root_user'] ? $row['root_user'] : $row['guest_user'];
			$data['relation']		= $row['id_relationship'];
			$data['comment']		= $row['comment'];
			
			// privacy settings
			$data['hide_online']	= isset($row['hide_online']) ? (int) $row['hide_online'] : 0;
			$data['visible_lady']	= isset($row['visible_lady']) ? (int) $row['visible_lady'] : 1;
			
			$data['vis_lady_1']		= isset($row['vis_lady_1']) ? (int) $row['vis_lady_1'] : 1;
			$data['vis_lady_2']		= isset($row['vis_lady_2']) ? (int) $row['vis_lady_2'] : 1;
			$data['vis_lady_3']		= isset($row['vis_lady_3']) ? (int) $row['vis_lady_3'] : 1;
			
			$data['visible_guy']	= isset($row['visible_guy']) ? (int) $row['visible_guy'] : 1;
			
			$data['vis_guy_1']		= isset($row['vis_guy_1']) ? (int) $row['vis_guy_1'] : 1;
			$data['vis_guy_2']		= isset($row['vis_guy_2']) ? (int) $row['vis_guy_2'] : 1;
			$data['vis_guy_3']		= isset($row['vis_guy_3']) ? (int) $row['vis_guy_3'] : 1;
			$data['vis_guy_4']		= isset($row['vis_guy_4']) ? (int) $row['vis_guy_4'] : 1;
			
			$data['promotion_1']	= isset($row['promotion_1']) ? (int) $row['promotion_1'] : 0;
			$data['promotion_2']	= isset($row['promotion_2']) ? (int) $row['promotion_2'] : 0;
			$data['promotion_3']	= isset($row['promotion_3']) ? (int) $row['promotion_3'] : 0;
			$data['featured_land']	= isset($row['featured_land']) ? (int) $row['featured_land'] : 0;
			$data['featured_home']	= isset($row['featured_home']) ? (int) $row['featured_home'] : 0;
			
			// biography
			$data['about_me']		= stripslashes($row['about_me']);
			$data['what_i_do']		= stripslashes($row['what_i_do']);
			$data['my_idea']		= stripslashes($row['my_idea']);
			$data['hoping_to_find']	= stripslashes($row['hoping_to_find']);
		}
		
		$form['hiddens'] = '<input type="hidden" name="sel" value="change">';
		$form['hiddens'].= '<input type="hidden" name="count" value="0">';
		$form['hiddens'].= '<input type="hidden" name="e" value="1">';
		$form['hiddens'].= '<input type="hidden" name="zip" value="0">';
		$form['hiddens'].= '<input type="hidden" name="page" value="' . $page . '">';
		$form['hiddens'].= '<input type="hidden" name="letter" value="' . $letter . '">';
		$form['hiddens'].= '<input type="hidden" name="search" value="' . $search . '">';
		$form['hiddens'].= '<input type="hidden" name="s_type" value="' . $s_type . '">';
		$form['hiddens'].= '<input type="hidden" name="s_stat" value="' . $s_stat . '">';
		$form['hiddens'].= '<input type="hidden" name="sorter" value="' . $sorter . '">';
		$form['hiddens'].= '<input type="hidden" name="id" value="' . $id . '">';
	}
	else
	{
		// add user
		if (!$err) {
			$data['fname']			= '';
			$data['sname']			= '';
			$data['status']			= '1';
			$data['conf']			= '1';
			$data['site_language']	= $config['default_lang'];
		}
		
		$form['hiddens'] = '<input type="hidden" name="sel" value="add">';
		$form['hiddens'].= '<input type="hidden" name="count" value="0">';
		$form['hiddens'].= '<input type="hidden" name="id_count" value="0">';
		$form['hiddens'].= '<input type="hidden" name="zip" value="0">';
		$form['hiddens'].= '<input type="hidden" name="e" value="1">';
		$form['hiddens'].= '<input type="hidden" name="page" value="' . $page . '">';
		$form['hiddens'].= '<input type="hidden" name="letter" value="' . $letter . '">';
		$form['hiddens'].= '<input type="hidden" name="search" value="' . $search . '">';
		$form['hiddens'].= '<input type="hidden" name="s_type" value="' . $s_type . '">';
		$form['hiddens'].= '<input type="hidden" name="s_stat" value="' . $s_stat . '">';
		$form['hiddens'].= '<input type="hidden" name="sorter" value="' . $sorter . '">';
	}
	
	// if we try to find country acording zipcode
	$rs = $dbconn->Execute(
		'SELECT name, value
		   FROM '.SETTINGS_TABLE.'
		  WHERE name IN ("zip_letters", "zip_count")');
	
	while (!$rs->EOF) {
		$settings[$rs->fields[0]] = $rs->fields[1];
		$rs->MoveNext();
	}
	
	$form['zip_count'] = $settings['zip_count'];
	
	// chk day select
	$chk_day = array();
	for ($i = 0; $i < 31; $i++) {
		$chk_day[$i]['value'] = $i + 1;
		if (isset($data) && isset($data['chk_day']) && (int) $data['chk_day'] == $i + 1) {
			$chk_day[$i]['sel'] = 1;
		} else {
			$chk_day[$i]['sel'] = 0;
		}
	}
	$smarty->assign('chk_day', $chk_day);
	
	//  chk month select
	$chk_month = array();
	for ($i = 0; $i < 12; $i++) {
		$chk_month[$i]['value'] = $i + 1;
		$chk_month[$i]['name'] = $lang['month'][$i + 1];
		if (isset($data) && isset($data['chk_month']) && (int) $data['chk_month'] == $i + 1) {
			$chk_month[$i]['sel'] = 1;
		} else {
			$chk_month[$i]['sel'] = 0;
		}
	}
	$smarty->assign('chk_month', $chk_month);
	
	//  chk year select
	$chk_year_limit = 2;
	$chk_year = array();
	for ($i = 0; $i < $chk_year_limit; $i++) {
		$y = (int) date('Y') - $i;
		$chk_year[$i]['value'] = $y;
		if (isset($data) && isset($data['chk_year']) && (int) $data['chk_year'] == $y) {
			$chk_year[$i]['sel'] = 1;
		} else {
			$chk_year[$i]['sel'] = 0;
		}
	}
	$smarty->assign('chk_year', $chk_year);
	
	// birth day select
	$day = array();
	for ($i = 0; $i < 31; $i++) {
		$day[$i]['value'] = $i + 1;
		if (isset($data) && isset($data['b_day']) && (int) $data['b_day'] == $i + 1) {
			$day[$i]['sel'] = 1;
		} else {
			$day[$i]['sel'] = 0;
		}
	}
	$smarty->assign('day', $day);
	
	//  birth month select
	$month = array();
	for ($i = 0; $i < 12; $i++) {
		$month[$i]['value'] = $i + 1;
		$month[$i]['name'] = $lang['month'][$i + 1];
		if (isset($data) && isset($data['b_month']) && (int) $data['b_month'] == $i + 1) {
			$month[$i]['sel'] = 1;
		} else {
			$month[$i]['sel'] = 0;
		}
	}
	$smarty->assign('month', $month);
	
	//  birth year select
	$year_limit = $settings['max_age_limit'] - $settings['min_age_limit'];
	$year = array();
	for ($i = 0; $i < $year_limit; $i++) {
		$y = (int) date('Y') - $settings['min_age_limit'] - $i;
		$year[$i]['value'] = $y;
		if (isset($data) && isset($data['b_year']) && (int) $data['b_year'] == $y) {
			$year[$i]['sel'] = 1;
		} else {
			$year[$i]['sel'] = 0;
		}
	}
	$smarty->assign('year', $year);
	
	//  country select
	$rs = $dbconn->Execute('SELECT id, name FROM '.COUNTRY_SPR_TABLE.' ORDER BY name');
	$c_arr = array();
	$i = 0;
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$c_arr[$i]['id'] = $row['id'];
		$c_arr[$i]['value'] = $row['name'];
		if ($data['id_country'] == $row['id']) {
			$c_arr[$i]['sel'] = 1;
		} else {
			$c_arr[$i]['sel'] = 0;
		}
		$rs->MoveNext();
		$i++;
	}
	$smarty->assign('country', $c_arr);
	
	// region select
	if (isset($data['id_country'])) {
		$strSQL = 'SELECT id, name AS value FROM '.REGION_SPR_TABLE.' WHERE id_country = ' . $data['id_country'] . ' ORDER BY name';
		$rs = $dbconn->Execute($strSQL);
		
		$i = 0;
		$region_arr = array();
		
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$region_arr[$i] = $row;
			$region_arr[$i]['sel'] = ($data['id_region'] == $row['id']) ? 1 : 0;
			$rs->MoveNext();
			$i++;
		}
		
		$smarty->assign('region', $region_arr);
	}
	
	// city select
	if (isset($data['id_region'])) {
		$strSQL = 'SELECT id, name AS value FROM '.CITY_SPR_TABLE.' WHERE id_country=' . $data['id_country'] . ' AND id_region=' . $data['id_region'] . ' GROUP by id ORDER BY name';
		$rs = $dbconn->Execute($strSQL);
		
		$i = 0;
		$city_arr = array();
		
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$city_arr[$i] = $row;
			$city_arr[$i]['sel'] = ($data['id_city'] == $row['id']) ? 1 : 0;
			$rs->MoveNext();
			$i++;
		}
		
		$smarty->assign('city', $city_arr);
	}
	
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	// nationality select
	$rs = $dbconn->Execute(
			'SELECT a.id, b.' . $field_name . ' AS value
			   FROM '.NATION_SPR_TABLE.' a
		  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = ' . $multi_lang->TableKey(NATION_SPR_TABLE) . ' AND b.id_reference = a.id
		   ORDER BY value');
	
	$i = 0;
	$nation_arr = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$nation_arr[$i] = $row;
		$nation_arr[$i]['sel'] = ($data['id_nationality'] == $row['id']) ? 1 : 0;
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('nation', $nation_arr);
	
	// language select
	$strSQL =
		'SELECT a.id, b.' . $field_name . ' AS value 
		   FROM '.LANGUAGE_SPR_TABLE.' a 
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key=' . $multi_lang->TableKey(LANGUAGE_SPR_TABLE) . ' AND b.id_reference=a.id 
	   ORDER BY value';
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$lang_sel = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$lang_sel[$i] = $row;
		$lang_sel[$i]['sel1'] = (isset($data['id_language_1']) && ($data['id_language_1'] == $row['id'])) ? 1 : 0;
		$lang_sel[$i]['sel2'] = (isset($data['id_language_2']) && ($data['id_language_2'] == $row['id'])) ? 1 : 0;
		$lang_sel[$i]['sel3'] = (isset($data['id_language_3']) && ($data['id_language_3'] == $row['id'])) ? 1 : 0;
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('lang_sel', $lang_sel);
	
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
	
	$id = isset($id) ? $id : '';
	$data['infolink'] = $file_name . '?sel=info&id=' . $id;
	
	$max_in = $settings['max_age_limit'];
	$min_in = $settings['min_age_limit'];
	$max_age_arr = range(intval($max_in), intval($min_in));
	$min_age_arr = range(intval($min_in), intval($max_in));
	
	if (isset($data['age_min'])) {
		$min_age_sel = $data['age_min'];
		$smarty->assign('min_age_sel', $min_age_sel);
	}
	if (isset($data['age_max'])) {
		$max_age_sel = $data['age_max'];
		$smarty->assign('max_age_sel', $max_age_sel);
	}
	$smarty->assign('age_max', $max_age_arr);
	$smarty->assign('age_min', $min_age_arr);
	
	// gender select
	$gender_arr = array();
	
	$gender_arr[0]['id'] = '1';
	$gender_arr[0]['name'] = $lang['gender']['1'];
	$gender_arr[0]['name_search'] = $lang['gender_search']['1'];
	$gender_arr[0]['sel'] = (isset($data['gender']) && $data['gender'] == GENDER_MALE) ? 1 : 0;
	$gender_arr[0]['sel_search'] = (isset($data['gender_search']) && $data['gender_search'] == GENDER_MALE) ? 1 : 0;
	$gender_arr[1]['id'] = '2';
	$gender_arr[1]['name'] = $lang['gender']['2'];
	$gender_arr[1]['name_search'] = $lang['gender_search']['2'];
	$gender_arr[1]['sel'] = (isset($data['gender']) && $data['gender'] == GENDER_FEMALE) ? 1 : 0;
	$gender_arr[1]['sel_search'] = (isset($data['gender_search']) && $data['gender_search'] == GENDER_FEMALE) ? 1 : 0;
	
	$smarty->assign('gender', $gender_arr);
	
	// added: marital status
	$rs = $dbconn->Execute(
		'SELECT a.id, b.' . $field_name . ' AS value
		   FROM '.MARITAL_STATUS_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = 200 AND b.id_reference = a.id
	   ORDER BY a.sorter');
	
	$i = 0;
	$marital_status_arr = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$marital_status_arr[$i] = $row;
		$marital_status_arr[$i]['sel'] = (isset($data['mm_marital_status']) && ($data['mm_marital_status'] == $row['id'])) ? 1 : 0;
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('mm_marital_status', $marital_status_arr);
	
	// added: level of english
	$strSQL = 'SELECT a.id, b.' . $field_name . ' AS value FROM ' . LEVEL_ENGLISH_SPR_TABLE . ' a LEFT JOIN ' . REFERENCE_LANG_TABLE . ' b ON b.table_key=201 AND b.id_reference=a.id ORDER BY a.sorter';
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$level_of_english_arr = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$level_of_english_arr[$i] = $row;
		$level_of_english_arr[$i]['sel'] = (isset($data['mm_level_of_english']) && ($data['mm_level_of_english'] == $row['id'])) ? 1 : 0;
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('mm_level_of_english', $level_of_english_arr);
	
	// added: employment status
	$rs = $dbconn->Execute(
		'SELECT a.id, b.' . $field_name . ' AS value
		   FROM ' . EMPLOYMENT_STATUS_SPR_TABLE . ' a
	  LEFT JOIN ' . REFERENCE_LANG_TABLE . ' b ON b.table_key = 202 AND b.id_reference = a.id
	   ORDER BY a.sorter');
	
	$i = 0;
	$employment_status_arr = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$employment_status_arr[$i] = $row;
		$employment_status_arr[$i]['sel'] = (isset($data['mm_employment_status']) && ($data['mm_employment_status'] == $row['id'])) ? 1 : 0;
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('mm_employment_status', $employment_status_arr);
	
	//  relationships select
	$strSQL =
		'SELECT DISTINCT a.id, b.' . $field_name . ' AS name
		   FROM ' . RELATION_SPR_TABLE . ' a
	  LEFT JOIN ' . REFERENCE_LANG_TABLE . ' b ON b.table_key = "' . $multi_lang->TableKey(RELATION_SPR_TABLE) . '" AND b.id_reference = a.id
	   ORDER BY a.sorter';
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$relation_arr = array();
	
	while (!$rs->EOF) {
		$relation_arr['opt_value'][$i] = $rs->fields[0];
		$relation_arr['opt_name'][$i] = $rs->fields[1];
		if (isset($data['relation']) && strlen($data['relation'])) {
			$relation_user_arr = explode(',', $data['relation']);
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
	
	// site language select
	$rs = $dbconn->Execute('SELECT id, name FROM ' . LANGUAGE_TABLE . ' WHERE visible = "1"');
	$site_langs = array();
	$i = 0;
	while (!$rs->EOF) {
		$site_langs[$i]['id'] = (int) $rs->fields[0];
		$site_langs[$i]['name'] = ucfirst($rs->fields[1]);
		$site_langs[$i]['sel'] = ($rs->fields[0] == $data['site_language']) ? 1 : 0;
		$rs->MoveNext();
		$i++;
	}
	$smarty->assign('site_langs', $site_langs);
	
	$form['delete'] = $file_name . '?sel=del&id=' . $id . '&page=' . $page;
	
	if ($pre_sel == 'featured') {
		$form['back'] = 'admin_featured_users.php';
	} else {
		$form['back'] = $file_name . '?sel=' . $pre_sel . '&page=' . $page . '&letter=' . $letter . '&search=' . $search . '&s_type=' . $s_type . '&s_stat=' . $s_stat;
	}
	
	$form['action']	= $file_name . '?pre_sel=' . $pre_sel;
	$form['par']	= $par;
	$form['confirm']= $lang['confirm']['users'];
	
	$smarty->assign('data', $data);
	$smarty->assign('form', $form);
	$smarty->assign('header', $lang['users']);
	$smarty->assign('button', $lang['button']);
	$smarty->assign('alerts', $lang['alerts']);
	
	$smarty->display(TrimSlash($config['admin_theme_path']) . '/admin_user_form.tpl');
	exit;
}


function AddUser()
{
	global $dbconn, $config, $page, $lang, $e;
	
	$data					= array();
	
	$data['fname']			= isset($_POST['fname']) ? FormFilter($_POST['fname']) : '';
	$data['sname']			= isset($_POST['sname']) ? FormFilter($_POST['sname']) : '';
	$data['login']			= isset($_POST['login']) ? FormFilter($_POST['login']) : '';
	$data['status']			= isset($_POST['status']) ? (int) $_POST['status'] : 0;
	$data['email']			= isset($_POST['email']) ? FormFilter($_POST['email']) : '';
	$data['gender']			= isset($_POST['gender']) ? (int) $_POST['gender'] : 1;
	$data['id_country']		= isset($_POST['country']) ? (int) $_POST['country'] : 0;
	$data['id_region']		= isset($_POST['region']) ? (int) $_POST['region'] : 0;
	$data['id_city']		= isset($_POST['city']) ? (int) $_POST['city'] : 0;
	$data['b_year']			= isset($_POST['b_year']) ? (int) $_POST['b_year'] : 0;
	$data['b_month']		= isset($_POST['b_month']) ? (int) $_POST['b_month'] : 0;
	$data['b_day']			= isset($_POST['b_day']) ? (int) $_POST['b_day'] : 0;
	$data['zipcode']		= isset($_POST['zipcode']) ? FormFilter($_POST['zipcode']) : '';
	$data['id_nation']		= isset($_POST['id_nation']) ? (int) $_POST['id_nation'] : 0;
	$data['id_language_1']	= isset($_POST['id_language_1']) ? (int) $_POST['id_language_1'] : 0;
	$data['id_language_2']	= isset($_POST['id_language_2']) ? (int) $_POST['id_language_2'] : 0;
	$data['id_language_3']	= isset($_POST['id_language_3']) ? (int) $_POST['id_language_3'] : 0;
	$data['headline']		= isset($_POST['headline']) ? stripn(FormFilter($_POST['headline'])) : '';
	$data['comment']		= isset($_POST['comment']) ? FormFilter($_POST['comment']) : '';
	$data['pass']			= isset($_POST['pass']) ? strval($_POST['pass']) : '';
	$data['repass']			= isset($_POST['repass']) ? strval($_POST['repass']) : '';
	$data['conf']			= isset($_POST['conf']) ? (int) $_POST['conf'] : 0;
	$data['gender_search']	= isset($_POST['gender_search']) ? (int) $_POST['gender_search'] : 0;
	$data['age_min']		= isset($_POST['age_min']) ? (int) $_POST['age_min'] : 0;
	$data['age_max']		= isset($_POST['age_max']) ? (int) $_POST['age_max'] : 0;
	$data['id_weight']		= isset($_POST['weight']) ? (int) $_POST['weight'] : 0;
	$data['id_height']		= isset($_POST['height']) ? (int) $_POST['height'] : 0;
	$data['site_language']	= isset($_POST['site_language']) ? (int) $_POST['site_language'] : 0;
	
	if (!empty($_POST['relation']) && is_array($_POST['relation'])) {
		if ($_POST['relation'][0] == '0') {
			$_POST['relation'] = array();
			$_POST['relation'][0] = '0';
		}
		$data['relation'] = implode(',', $_POST['relation']);
	} else {
		$data['relation'] = MM_DEFAULT_RELATIONSHIP_ID;
	}
	
	if (!$data['gender_search']) {
		$data['gender_search'] = 1;
	}
	
	$page = isset($_POST['page']) ? (int) $_POST['page'] : 1;
	
#	$letter = (isset($_POST['letter']) && intval($_POST['letter'])>0) ? intval($_POST['letter']) : '*';
#	$sorter = isset($_POST['sorter']) ? intval($_POST['sorter']) : 1;
#	$search = isset($_POST['search']) ? $_POST['search'] : '';
#	$s_type = isset($_POST['s_type']) ? intval($_POST['s_type']) : 1;
#	$s_stat = isset($_POST['s_stat']) ? $_POST['s_stat'] : '';
	
	// check date
	if (checkdate($data['b_month'], $data['b_day'], $data['b_year'])) {
		$data['date_birthday'] = $data['b_year'] . '-' . sprintf('%02d', $data['b_month']) . '-' . sprintf('%02d', $data['b_day']);
	} else {
		$data['date_birthday'] = $data['b_year'] . '-' . sprintf('%02d', $data['b_month']) . '-' . sprintf('%02d', $data['b_day']);
		$err = $e ? $lang['err']['invalid_date'] : '';
		EditForm('add', $err, $data);
	}
	
	if (!strlen($data['login']) || !strlen($data['fname']) || !strlen($data['sname']) || !strlen($data['email']) || !strlen($data['pass'])) {
		if ($e) {
			$err = $lang['err']['invalid_fields'];
			if (!strlen($data['login'])) {
				$err .= '<br>' . $lang['users']['nick'];
			} elseif (!strlen($data['fname'])) {
				$err .= '<br>' . $lang['users']['fname'];
			} elseif (!strlen($data['sname'])) {
				$err .= '<br>' . $lang['users']['sname'];
			} elseif (!strlen($data['email'])) {
				$err .= '<br>' . $lang['users']['email'];
			} elseif (!strlen($data['pass'])) {
				$err .= '<br>' . $lang['users']['pass'];
			}
		}
		EditForm('add', $err, $data);
	}
	
	// check username
	$err = LoginFilter($data['login']);
	if ($err) {
		EditForm('add', $err, $data);
	}
	
	// check password
	if ($data['repass'] != $data['pass']) {
		$err = $lang['err']['invalid_passw'];
		EditForm('add', $err, $data);
	}
	if ($data['login'] == $data['pass']) {
		$err = $lang['err']['pass_eq_log'];
		EditForm('add', $err, $data);
	}
	$err = PasswFilter($data['pass']);
	if ($err) {
		EditForm('add', $err, $data);
	}
	
	// check email
	$err = EmailFilter($data['email']);
	if ($err) {
		EditForm('add', $err, $data);
	}
	
	// check login already used
	$rs = $dbconn->Execute('SELECT COUNT(*) FROM ' . USERS_TABLE . ' WHER login = ?', array($data['login']));
	if ($rs->fields[0] > 0) {
		$err = $lang['err']['exists_login'];
		EditForm('add', $err, $data);
	}
	
	// check email already used
	$rs = $dbconn->Execute('SELECT COUNT(*) FROM ' . USERS_TABLE . ' WHERE email = ?', array($data['email']));
	if ($rs->fields[0] > 0) {
		$err = $lang['err']['exists_email'];
		EditForm('add', $err, $data);
	}
	
	// check zipcode
	$rs = $dbconn->Execute('SELECT name, value FROM ' . SETTINGS_TABLE . ' WHERE name IN ("zip_letters", "zip_count")');
	while (!$rs->EOF) {
		$zip_settings[$rs->fields[0]] = $rs->fields[1];
		$rs->MoveNext();
	}
	
	if (!$zip_settings['zip_letters']) {
		$data['zipcode'] = intval(substr($data['zipcode'], 0, $zip_settings['zip_count']));
	} else {
		$data['zipcode'] = substr($data['zipcode'], 0, $zip_settings['zip_count']);
	}
	
	$strSQL =
		"INSERT INTO " . USERS_TABLE . " (
			fname, sname, status, gender, login,
			email, id_country, id_region, id_city, zipcode,
			date_birthday, date_last_seen, date_registration, password, comment,
			id_nationality, id_language_1, id_language_2, id_language_3, headline,
			id_weight, id_height, site_language
		) VALUES (
			'" . $data['fname'] . "', '" . $data['sname'] . "', '" . $data['status'] . "', '" . $data['gender'] . "', '" . $data['login'] . "',
			'" . $data['email'] . "', '" . $data['id_country'] . "', '" . $data['id_region'] . "', '" . $data['id_city'] . "', '" . $data['zipcode'] . "',
			'" . $data['date_birthday'] . "', NOW(), NOW(), '" . md5($data['pass']) . "', '" . $data['comment'] . "',
			'" . $data['id_nation'] . "', '" . $data['id_language_1'] . "', '" . $data['id_language_2'] . "', '" . $data['id_language_3'] . "', '" . $data['headline'] . "',
			'" . $data['id_weight'] . "', '" . $data['id_height'] . "', '" . $data['site_language'] . "'
		)";
	$dbconn->Execute($strSQL);
	
	$rs = $dbconn->Execute('SELECT MAX(id) FROM '.USERS_TABLE);
	$id = $rs->fields[0];
	
	// entry in match table
	$strSQL =
		'INSERT INTO ' . USER_MATCH_TABLE . " (id_user, gender, age_max, age_min, id_relationship)
			VALUES ('" . $id . "', '" . $data['gender_search'] . "','" . $data['age_max'] . "','" . $data['age_min'] . "', '" . $data['relation'] . "') ";
	$dbconn->Execute($strSQL);
	
	$profile_percent = new Percent($id);
	$profile_percent->UpdateSection1Percent();
	
	// add user into 'dafault_add' groups (f.e 'free users')
	$rs = $dbconn->Execute("SELECT id FROM ".GROUPS_TABLE." WHERE type = 'd'");
	while (!$rs->EOF) {
		if (intval($rs->fields[0]) > 0) {
			$dbconn->Execute("insert into ".USER_GROUP_TABLE." (id_user, id_group) values ('" . $id . "', '" . intval($rs->fields[0]) . "')");
		}
		$rs->MoveNext();
	}
	
	// newsletter update
	UpdateNewsletterUserData($id, $data['fname'], $data['sname'], $data['email']);
	
	// send confirmation email to user
	if ($data['conf']) {
		// include mail language file
		$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($data['site_language']));
		$lang_mail = array();
		include $config['path_lang'] . 'mail/' . $lang_file;
		
		$content			= array();
		$content['fname']	= $data['fname'];
		$content['sname']	= $data['sname'];
		$content['login']	= $data['login'];
		$content['pass']	= $data['pass'];
		$content['status']	= $data['status'];
		$content['email']	= $data['email'];
		$content['urls']	= GetUserEmailLinks();
		
		// language suffix
		$suffix = ($data['gender'] == GENDER_MALE) ? '_e' : '_t';
		
		// subject
		$subject = $lang_mail['admin_registration' . $suffix]['subject'];
		
		// recipient
		$name_to = trim($data['fname'] . ' ' . $data['sname']);
		
		SendMail($data['site_language'], $data['email'], $config['site_email'], $subject, $content,
			'mail_admin_registration_for_user', null, $name_to, '', 'admin_registration', $data['gender']);
	}
	
	ListUser();
	return;
}


function ChangeUser()
{
	global $smarty, $dbconn, $config, $page, $lang;
	
	$id = isset($_POST['id']) ? (int) $_POST['id'] : null;
	
	if (!$id) {
		ListUser();
		return;
	}
	
	$data							= $_POST;
	
	// login
	$data['login']					= isset($_POST['login']) ? FormFilter($_POST['login']) : '';
	$data['big_icon_path']			= isset($_POST['big_icon_path']) ? FormFilter($_POST['big_icon_path']) : '';
	$data['status']					= isset($_POST['status']) ? (int) $_POST['status'] : 0;
	$data['pass']					= isset($_POST['pass']) ? (string) $_POST['pass'] : '';
	$data['repass']					= isset($_POST['repass']) ? (string) $_POST['repass'] : '';
	$data['conf']					= isset($_POST['conf']) ? (int) $_POST['conf'] : 0;
	$data['refresh']				= isset($_POST['refresh']) ? (int) $_POST['refresh'] : 0;
	$data['group_name']				= isset($_POST['group_name']) ? FormFilter($_POST['group_name']) : '';
	
	// background checks
	$data['chk_background']			= isset($_POST['chk_background']) ? FormFilter($_POST['chk_background']) : 'NA';
	$data['chk_marital_status']		= isset($_POST['chk_marital_status']) ? FormFilter($_POST['chk_marital_status']) : 'NA';
	$data['chk_work_history']		= isset($_POST['chk_work_history']) ? FormFilter($_POST['chk_work_history']) : 'NA';
	$data['chk_interview_photo']	= isset($_POST['chk_interview_photo']) ? FormFilter($_POST['chk_interview_photo']) : 'NA';
	$data['chk_year']				= isset($_POST['chk_year']) ? (int) $_POST['chk_year'] : 0;
	$data['chk_month']				= isset($_POST['chk_month']) ? (int) $_POST['chk_month'] : 0;
	$data['chk_day']				= isset($_POST['chk_day']) ? (int) $_POST['chk_day'] : 0;
	$data['chk_staff']				= isset($_POST['chk_staff']) ? FormFilter($_POST['chk_staff']) : '';
	$data['chk_comment']			= isset($_POST['chk_comment']) ? FormFilter($_POST['chk_comment']) : '';
	$data['platinum_verified']		= isset($_POST['platinum_verified']) ? FormFilter($_POST['platinum_verified']) : '0';
	
	// personal info
	$data['fname']					= isset($_POST['fname']) ? FormFilter($_POST['fname']) : '';
	$data['sname']					= isset($_POST['sname']) ? FormFilter($_POST['sname']) : '';
	$data['mm_nickname']			= isset($_POST['mm_nickname']) ? FormFilter($_POST['mm_nickname']) : '';
	$data['gender']					= isset($_POST['gender']) ? (int) $_POST['gender'] : 1;
	$data['mm_marital_status']		= isset($_POST['mm_marital_status']) ? (int) $_POST['mm_marital_status'] : 1;
	$data['b_year']					= isset($_POST['b_year']) ? (int) $_POST['b_year'] : 0;
	$data['b_month']				= isset($_POST['b_month']) ? (int) $_POST['b_month'] : 0;
	$data['b_day']					= isset($_POST['b_day']) ? (int) $_POST['b_day'] : 0;
	$data['mm_place_of_birth']		= isset($_POST['mm_place_of_birth']) ? FormFilter($_POST['mm_place_of_birth']) : '';
	$data['id_nationality']			= isset($_POST['id_nationality']) ? (int) $_POST['id_nationality'] : 0;
	$data['mm_id_number']			= isset($_POST['mm_id_number']) ? FormFilter($_POST['mm_id_number']) : '';
	$data['mm_id_type']				= isset($_POST['mm_id_type']) ? FormFilter($_POST['mm_id_type']) : '';
	$data['id_weight']				= isset($_POST['id_weight']) ? (int) $_POST['id_weight'] : 0;
	$data['id_height']				= isset($_POST['id_height']) ? (int) $_POST['id_height'] : 0;
	$data['headline']				= isset($_POST['headline']) ? FormFilter($_POST['headline']) : '';
	
	// contact info
	$data['email']						= isset($_POST['email']) ? FormFilter($_POST['email']) : '';
	$data['mm_contact_phone_number']	= isset($_POST['mm_contact_phone_number']) ? FormFilter($_POST['mm_contact_phone_number']) : '';
	$data['mm_contact_mobile_number']	= isset($_POST['mm_contact_mobile_number']) ? FormFilter($_POST['mm_contact_mobile_number']) : '';
	$data['phone']						= isset($_POST['phone']) ? FormFilter($_POST['phone']) : '';
	
	// platinum application form
	$data['mm_best_call_time_weekdays']	= isset($_POST['mm_best_call_time_weekdays']) ? FormFilter($_POST['mm_best_call_time_weekdays']) : '';
	$data['mm_best_call_time_saturdays']= isset($_POST['mm_best_call_time_saturdays']) ? FormFilter($_POST['mm_best_call_time_saturdays']) : '';
	$data['mm_best_call_time_sundays']	= isset($_POST['mm_best_call_time_sundays']) ? FormFilter($_POST['mm_best_call_time_sundays']) : '';
	$data['platinum_submit_comment']	= isset($_POST['mm_platinum_submit_comment']) ? FormFilter($_POST['mm_platinum_submit_comment']) : '';
	
	// search criteria
	$data['gender_search']			= isset($_POST['gender_search']) ? (int) $_POST['gender_search'] : 1;
	$data['couple_search']			= isset($_POST['couple_search']) ? (int) $_POST['couple_search'] : 1;
	$data['age_min']				= isset($_POST['age_min']) ? (int) $_POST['age_min'] : 0;
	$data['age_max']				= isset($_POST['age_max']) ? (int) $_POST['age_max'] : 0;
	
	// address info
	$data['id_country']				= isset($_POST['id_country']) ? (int) $_POST['id_country'] : 0;
	$data['id_region']				= isset($_POST['id_region']) ? (int) $_POST['id_region'] : 0;
	$data['id_city']				= isset($_POST['id_city']) ? (int) $_POST['id_city'] : 0;
	$data['mm_city']				= isset($_POST['mm_city']) ? FormFilter($_POST['mm_city']) : '';
	$data['zipcode']				= isset($_POST['zipcode']) ? FormFilter($_POST['zipcode']) : '';
	$data['mm_address_1']			= isset($_POST['mm_address_1']) ? FormFilter($_POST['mm_address_1']) : '';
	$data['mm_address_2']			= isset($_POST['mm_address_2']) ? FormFilter($_POST['mm_address_2']) : '';
	$data['mm_address_3']			= isset($_POST['mm_address_3']) ? FormFilter($_POST['mm_address_3']) : '';
	
	// language info
	$data['site_language']			= isset($_POST['site_language']) ? (int) $_POST['site_language'] : 0;
	$data['id_language_1']			= isset($_POST['id_language_1']) ? (int) $_POST['id_language_1'] : 0;
	$data['id_language_2']			= isset($_POST['id_language_2']) ? (int) $_POST['id_language_2'] : 0;
	$data['id_language_3']			= isset($_POST['id_language_3']) ? (int) $_POST['id_language_3'] : 0;
	$data['mm_level_of_english']	= isset($_POST['mm_level_of_english']) ? (int) $_POST['mm_level_of_english'] : 0;
	
	// employment info
	$data['mm_employment_status']	= isset($_POST['mm_employment_status']) ? (int) $_POST['mm_employment_status'] : 0;
	$data['mm_business_name']		= isset($_POST['mm_business_name']) ? FormFilter($_POST['mm_business_name']) : '';
	$data['mm_employer_name']		= isset($_POST['mm_employer_name']) ? FormFilter($_POST['mm_employer_name']) : '';
	$data['mm_job_position']		= isset($_POST['mm_job_position']) ? FormFilter($_POST['mm_job_position']) : '';
	$data['mm_work_address']		= isset($_POST['mm_work_address']) ? FormFilter($_POST['mm_work_address']) : '';
	$data['mm_work_phone_number']	= isset($_POST['mm_work_phone_number']) ? FormFilter($_POST['mm_work_phone_number']) : '';
	
	// references
	$data['mm_ref_1_first_name']	= isset($_POST['mm_ref_1_first_name']) ? FormFilter($_POST['mm_ref_1_first_name']) : '';
	$data['mm_ref_1_last_name']		= isset($_POST['mm_ref_1_last_name']) ? FormFilter($_POST['mm_ref_1_last_name']) : '';
	$data['mm_ref_1_relationship']	= isset($_POST['mm_ref_1_relationship']) ? FormFilter($_POST['mm_ref_1_relationship']) : '';
	$data['mm_ref_1_phone_number']	= isset($_POST['mm_ref_1_phone_number']) ? FormFilter($_POST['mm_ref_1_phone_number']) : '';
	$data['mm_ref_2_first_name']	= isset($_POST['mm_ref_2_first_name']) ? FormFilter($_POST['mm_ref_2_first_name']) : '';
	$data['mm_ref_2_last_name']		= isset($_POST['mm_ref_2_last_name']) ? FormFilter($_POST['mm_ref_2_last_name']) : '';
	$data['mm_ref_2_relationship']	= isset($_POST['mm_ref_2_relationship']) ? FormFilter($_POST['mm_ref_2_relationship']) : '';
	$data['mm_ref_2_phone_number']	= isset($_POST['mm_ref_2_phone_number']) ? FormFilter($_POST['mm_ref_2_phone_number']) : '';
	
	// couples
	// not included
	
	// privacy settings
	$data['hide_online']	= isset($_POST['hide_online']) ? (int) $_POST['hide_online'] : 0;
	
	$data['visible_lady']	= isset($_POST['visible_lady']) ? (int) $_POST['visible_lady'] : 1;
	
	if ($data['visible_lady'] == 1) {
		$data['vis_lady_1']	= 1;
		$data['vis_lady_2']	= 1;
		$data['vis_lady_3']	= 1;
	} else {
		$data['vis_lady_1']	= isset($_POST['vis_lady_1']) ? (int) $_POST['vis_lady_1'] : 0;
		$data['vis_lady_2']	= isset($_POST['vis_lady_2']) ? (int) $_POST['vis_lady_2'] : 0;
		$data['vis_lady_3']	= isset($_POST['vis_lady_3']) ? (int) $_POST['vis_lady_3'] : 0;
	}
	
	$data['visible_guy']	= isset($_POST['visible_guy']) ? (int) $_POST['visible_guy'] : 1;
	
	if ($data['visible_guy'] == 1) {
		$data['vis_guy_1']	= 1;
		$data['vis_guy_2']	= 1;
		$data['vis_guy_3']	= 1;
		$data['vis_guy_4']	= 1;
	} else {
		$data['vis_guy_1']	= isset($_POST['vis_guy_1']) ? (int) $_POST['vis_guy_1'] : 0;
		$data['vis_guy_2']	= isset($_POST['vis_guy_2']) ? (int) $_POST['vis_guy_2'] : 0;
		$data['vis_guy_3']	= isset($_POST['vis_guy_3']) ? (int) $_POST['vis_guy_3'] : 0;
		$data['vis_guy_4']	= isset($_POST['vis_guy_4']) ? (int) $_POST['vis_guy_4'] : 0;
	}
	
	$data['promotion_1']	= isset($_POST['promotion_1']) ? (int) $_POST['promotion_1'] : 0;
	$data['promotion_2']	= isset($_POST['promotion_2']) ? (int) $_POST['promotion_2'] : 0;
	$data['promotion_3']	= isset($_POST['promotion_3']) ? (int) $_POST['promotion_3'] : 0;
	$data['featured_land']	= isset($_POST['featured_land']) ? (int) $_POST['featured_land'] : 0;
	$data['featured_home']	= isset($_POST['featured_home']) ? (int) $_POST['featured_home'] : 0;
	
	// biography
	$data['about_me']		= isset($_POST['about_me']) ? FormFilter($_POST['about_me']) : '';
	$data['what_i_do']		= isset($_POST['what_i_do']) ? FormFilter($_POST['what_i_do']) : '';
	$data['my_idea']		= isset($_POST['my_idea']) ? FormFilter($_POST['my_idea']) : '';
	$data['hoping_to_find']	= isset($_POST['hoping_to_find']) ? FormFilter($_POST['hoping_to_find']) : '';
	
	// root user
	$data['root']			= isset($_POST['root']) ? FormFilter($_POST['root']) : '';
	$data['relation']		= isset($_POST['relation']) ? FormFilter($_POST['relation']) : MM_DEFAULT_RELATIONSHIP_ID;
	$data['comment']		= isset($_POST['comment']) ? FormFilter($_POST['comment']) : '';
	
	$data['id_nation']		= isset($_POST['id_nation']) ? (int) $_POST['id_nation'] : 0;
	
	if (isset($_POST['relation']) && count($_POST['relation'])) {
		if ($_POST['relation'][0] == '0') {
			$_POST['relation'] = array();
			$_POST['relation'][0] = '0';
		}
		$data['relation'] = implode(',', $_POST['relation']);
	} else {
		$data['relation'] = MM_DEFAULT_RELATIONSHIP_ID;
	}
	
	$page	= isset($_POST['page']) ? intval($_POST['page']) : 1;
#	$letter = (isset($_POST['letter']) && intval($_POST['letter']) > 0) ? intval($_POST['letter']) : '*';
#	$sorter = isset($_POST['sorter']) ? intval($_POST['sorter']) : 1;
#	$search = isset($_POST['search']) ? intval($_POST['search']) : '';
#	$s_type = isset($_POST['s_type']) ? intval($_POST['s_type']) : 1;
#	$s_stat = isset($_POST['s_stat']) ? $_POST['s_stat'] : '';
#	$spr	= isset($_POST['spr']) ? $_POST['spr'] : '';
#	$info	= isset($_POST['info']) ? $_POST['info'] : '';
	
	//----------------
	// validity check
	//----------------
	
	//VP included magic array
	$use_field = array();
	$mandatory = array();
	include '../customize/profile_switchboard.php';
	$smarty->assign('use_field', $use_field);
	$smarty->assign('mandatory', $mandatory);
	
	$rs = $dbconn->Execute('SELECT id_group FROM ' . USER_GROUP_TABLE . ' WHERE id_user = ?', array($id));
	$current_id_group = strval($rs->fields[0]);
	
	// RS: we cannot enforce that us user will ever edit and save his profile data, so we can only check for
	// fields which are mandatory on the registration form
	// $mandatory_level = ($current_id_group == MM_SIGNUP_GUY_ID || $current_id_group == MM_SIGNUP_LADY_ID) ? SB_REGISTRATION : SB_EDIT;
	$mandatory_level = SB_REGISTRATION;
	//echo '(EDIT)Mandatory Level : '.$mandatory_level;
	
	$smarty->assign('use_level', SB_EDIT);
	$use_active = ($current_id_group == MM_SIGNUP_GUY_ID || $current_id_group == MM_SIGNUP_LADY_ID) ? false : true;
	$smarty->assign('use_active', $use_active);
	
	// check mandatory fields
	$err = '';
	$err_field = array();
	
	if (!strlen($data['login'])) {
		$err .= $lang['users']['login'] . ', ';
		$err_field['login'] = 1;
	}
	
	if ($data['refresh'] == 1 && !strlen($data['pass'])) {
		$err .= $lang['users']['pass'] . ', ';
		$err_field['pass'] = 1;
	}
	
	if ($data['refresh'] == 1 && !strlen($data['repass'])) {
		$err .= $lang['users']['repass'] . ', ';
		$err_field['repass'] = 1;
	}
	
	if ($mandatory['fname'] & $mandatory_level && !strlen($data['fname'])) {
		$err .= $lang['users']['fname'] . ', ';
		$err_field['fname'] = 1;
	}
	
	if ($mandatory['sname'] & $mandatory_level && !strlen($data['sname'])) {
		$err .= $lang['users']['sname'] . ', ';
		$err_field['sname'] = 1;
	}
	
	if ($mandatory['mm_nickname'] & $mandatory_level && !strlen($data['mm_nickname'])) {
		$err .= $lang['users']['mm_nickname'] . ', ';
		$err_field['mm_nickname'] = 1;
	}
	
	if ($mandatory['gender'] & $mandatory_level && !$data['gender']) {
		$err .= $lang['users']['gender'] . ', ';
		$err_field['gender'] = 1;
	}
	
	if ($mandatory['mm_marital_status'] & $mandatory_level && !$data['mm_marital_status']) {
		$err .= $lang['users']['mm_marital_status'] . ', ';
		$err_field['mm_marital_status'] = 1;
	}
	
	if (checkdate($data['b_month'], $data['b_day'], $data['b_year'])) {
		$data['date_birthday'] = $data['b_year'] . '-' . sprintf('%02d', $data['b_month']) . '-' . sprintf('%02d', $data['b_day']);
	}
	
	if ($mandatory['mm_place_of_birth'] & $mandatory_level && !strlen($data['mm_place_of_birth'])) {
		$err .= $lang['users']['mm_place_of_birth'] . ', ';
		$err_field['mm_place_of_birth'] = 1;
	}
	
	if ($mandatory['id_nationality'] & $mandatory_level && !$data['id_nationality']) {
		$err .= $lang['users']['nationality'] . ', ';
		$err_field['id_nationality'] = 1;
	}
	
	if ($mandatory['mm_id_number'] & $mandatory_level && $data['gender'] == 2 && !strlen($data['mm_id_number'])) {
		$err .= $lang['users']['mm_id_number'] . ', ';
		$err_field['mm_id_number'] = 1;
	}
	
	if ($mandatory['mm_id_number'] & $mandatory_level && $data['gender'] == 2 && !strlen($data['mm_id_type'])) {
		$err .= $lang['users']['mm_id_type'] . ', ';
		$err_field['mm_id_type'] = 1;
	}
	
	if ($mandatory['email'] & $mandatory_level && !strlen($data['email'])) {
		$err .= $lang['users']['email'] . ', ';
		$err_field['email'] = 1;
	}
	
	if ($mandatory['mm_contact_phone_number'] & $mandatory_level && !strlen($data['mm_contact_phone_number'])) {
		$err .= $lang['users']['mm_contact_phone_number'] . ', ';
		$err_field['mm_contact_phone_number'] = 1;
	}
	
	if ($mandatory['mm_contact_mobile_number'] & $mandatory_level && !strlen($data['mm_contact_mobile_number'])) {
		$err .= $lang['users']['mm_contact_mobile_number'] . ', ';
		$err_field['mm_contact_mobile_number'] = 1;
	}
	
	if ($mandatory['id_country'] & $mandatory_level && !$data['id_country']) {
		$err .= $lang['users']['country'] . ', ';
		$err_field['id_country'] = 1;
	}
	
	/*
	if ($mandatory['id_region'] & $mandatory_level && !$id_region) {
		$err .= $lang['users']['region'] . ', ';
		$err_field['id_region'] = 1;
	}
	*/
	
	if ($mandatory['id_city'] & $mandatory_level && !$data['id_city']) {
		$err .= $lang['users']['city'] . ', ';
		$err_field['id_city'] = 1;
	}
	
	if ($mandatory['mm_city'] & $mandatory_level && !strlen($data['mm_city'])) {
		$err .= $lang['users']['city'] . ', ';
		$err_field['mm_city'] = 1;
	}
	
	if ($mandatory['zipcode'] & $mandatory_level && !strlen($data['zipcode'])) {
		$err .= $lang['users']['zipcode'] . ', ';
		$err_field['zipcode'] = 1;
	}
	
	if ($mandatory['mm_address_1'] & $mandatory_level && !strlen($data['mm_address_1'])) {
		$err .= $lang['users']['mm_address_1'] . ', ';
		$err_field['mm_address_1'] = 1;
	}
	
	if ($mandatory['mm_address_2'] & $mandatory_level && !strlen($data['mm_address_2'])) {
		$err .= $lang['users']['mm_address_2'] . ', ';
		$err_field['mm_address_2'] = 1;
	}
	
	if ($mandatory['mm_address_3'] & $mandatory_level && !strlen($data['mm_address_3'])) {
		$err .= $lang['users']['mm_address_3'] . ', ';
		$err_field['mm_address_3'] = 1;
	}
	
	if ($mandatory['id_language_1'] & $mandatory_level && !$data['id_language_1']) {
		$err .= $lang['users']['language'] . ', ';
		$err_field['id_language_1'] = 1;
	}
	
	if ($mandatory['id_language_2'] & $mandatory_level && !$data['id_language_2']) {
		$err .= $lang['users']['language'] . ', ';
		$err_field['id_language_2'] = 1;
	}
	
	if ($mandatory['id_language_3'] & $mandatory_level && !$data['id_language_3']) {
		$err .= $lang['users']['language'] . ', ';
		$err_field['id_language_3'] = 1;
	}
	
	if ($mandatory['mm_level_of_english'] & $mandatory_level && !$data['mm_level_of_english']) {
		$err .= $lang['users']['mm_level_of_english'] . ', ';
		$err_field['mm_level_of_english'] = 1;
	}
	
	if ($mandatory['site_language'] & $mandatory_level && !$data['site_language']) {
		$err .= $lang['users']['site_language'] . ', ';
		$err_field['site_language'] = 1;
	}
	
	if ($mandatory['mm_employment_status'] & $mandatory_level && !$data['mm_employment_status']) {
		$err .= $lang['users']['mm_employment_status'] . ', ';
		$err_field['mm_employment_status'] = 1;
	}
	
	if ($mandatory['mm_business_name'] & $mandatory_level && $data['mm_employment_status'] == 2 && !strlen($data['mm_business_name'])) {
		$err .= $lang['users']['mm_business_name'] . ', ';
		$err_field['mm_business_name'] = 1;
	}
	
	if ($mandatory['mm_employer_name'] & $mandatory_level && $data['mm_employment_status'] == 3 && !strlen($data['mm_employer_name'])) {
		$err .= $lang['users']['mm_employer_name'] . ', ';
		$err_field['mm_employer_name'] = 1;
	}
	
	if ($mandatory['mm_job_position'] & $mandatory_level && $data['mm_employment_status'] != 1 && !strlen($data['mm_job_position'])) {
		$err .= $lang['users']['mm_job_position'] . ', ';
		$err_field['mm_job_position'] = 1;
	}
	
	if ($mandatory['mm_work_address'] & $mandatory_level && $data['mm_employment_status'] != 1 && !strlen($data['mm_work_address'])) {
		$err .= $lang['users']['mm_work_address'] . ', ';
		$err_field['mm_work_address'] = 1;
	}
	
	if ($mandatory['mm_work_phone_number'] & $mandatory_level && $data['mm_employment_status'] != 1 && !strlen($data['mm_work_phone_number'])) {
		$err .= $lang['users']['mm_work_phone_number'] . ', ';
		$err_field['mm_work_phone_number'] = 1;
	}
	
	if ($mandatory['mm_ref_1_first_name'] & $mandatory_level && !strlen($data['mm_ref_1_first_name'])) {
		$err .= $lang['users']['mm_reference_1'] . ' ' . $lang['users']['fname'] . ', ';
		$err_field['mm_ref_1_first_name'] = 1;
	}
	
	if ($mandatory['mm_ref_1_last_name'] & $mandatory_level && !strlen($data['mm_ref_1_last_name'])) {
		$err .= $lang['users']['mm_reference_1'] . ' ' . $lang['users']['sname'] . ', ';
		$err_field['mm_ref_1_last_name'] = 1;
	}
	
	if ($mandatory['mm_ref_1_relationship'] & $mandatory_level && !strlen($data['mm_ref_1_relationship'])) {
		$err .= $lang['users']['mm_reference_1'] . ' ' . $lang['users']['mm_reference_relationship'] . ', ';
		$err_field['mm_ref_1_relationship'] = 1;
	}
	
	if ($mandatory['mm_ref_1_phone_number'] & $mandatory_level && !strlen($data['mm_ref_1_phone_number'])) {
		$err .= $lang['users']['mm_reference_1'] . ' ' . $lang['users']['mm_reference_phone_number'] . ', ';
		$err_field['mm_ref_1_phone_number'] = 1;
	}
	
	if ($mandatory['mm_ref_2_first_name'] & $mandatory_level && !strlen($data['mm_ref_2_first_name'])) {
		$err .= $lang['users']['mm_reference_2'] . ' ' . $lang['users']['fname'] . ', ';
		$err_field['mm_ref_2_first_name'] = 1;
	}
	
	if ($mandatory['mm_ref_2_last_name'] & $mandatory_level && !strlen($data['mm_ref_2_last_name'])) {
		$err .= $lang['users']['mm_reference_2'] . ' ' . $lang['users']['sname'] . ', ';
		$err_field['mm_ref_2_last_name'] = 1;
	}
	
	if ($mandatory['mm_ref_2_relationship'] & $mandatory_level && !strlen($data['mm_ref_2_relationship'])) {
		$err .= $lang['users']['mm_reference_2'] . ' ' . $lang['users']['mm_reference_relationship'] . ', ';
		$err_field['mm_ref_2_relationship'] = 1;
	}
	
	if ($mandatory['mm_ref_2_phone_number'] & $mandatory_level && !strlen($data['mm_ref_2_phone_number'])) {
		$err .= $lang['users']['mm_reference_2'] . ' ' . $lang['users']['mm_reference_phone_number'] . ', ';
		$err_field['mm_ref_2_phone_number'] = 1;
	}
	
	if ($data['refresh'])
	{
		// check not valid pass
		if ($data['repass'] != $data['pass']) {
			$err = $lang['err']['pass_eq_repass'];
			EditForm('edit', $err, $data);
		}
		
		// check not valid pass
		if ($data['login'] == $data['pass']) {
			$err = $lang['err']['pass_eq_log'];
			EditForm('edit', $err, $data);
		}
		
		// check not valid pass
		$err = PasswFilter($data['pass']);
		if ($err) {
			EditForm('edit', $err, $data);
		}
		
		$pass_str = ', password = "' . md5($data['pass']) . '" ';
	}
	else
	{
		$pass_str = '';
	}
	
	if ($err) {
		$smarty->assign('err_field', $err_field);
		$err = $lang['err']['invalid_fields'] . '<br/><br/>' . trim($err, ', ');
		//return $err;
		EditForm('edit', $err, $data);
	}
	
	// login not valid
	$err = LoginFilter($data['login']);
	
	if ($err) {
		$err_field['login'] = 1;
		$smarty->assign('err_field', $err_field);
		//return $err;
		EditForm('edit', $err, $data);
	}
	
	// login already exists
	$check_exist = $dbconn->getOne('SELECT 1 FROM '.USERS_TABLE.' WHERE login = "' . $data['login'] . '" AND id <> ' . $id);
	
	if (!empty($check_exist)) {
		$err_field['login'] = 1;
		$smarty->assign('err_field', $err_field);
		//return $lang['err']['exists_login'];
		$err = $lang['err']['exists_login'];
		EditForm('edit', $err, $data);
	}
	
	// email not valid
	$err = EmailFilter($data['email']);
	
	if ($err) {
		$err_field['email'] = 1;
		$smarty->assign('err_field', $err_field);
		//return $err;
		EditForm('edit', $err, $data);
	}
	
	// email already exists
	$check_exist = $dbconn->getOne('SELECT 1 FROM ' . USERS_TABLE . ' WHERE email="' . $data['email'] . '" AND id<>' . $id);
	
	if (!empty($check_exist)) {
		$err_field['email'] = 1;
		$smarty->assign('err_field', $err_field);
		//return $lang['err']['exists_email'];
		$err = $lang['err']['exists_email'];
		EditForm('edit', $err, $data);
	}
	
	// voip phone not valid
	$err = PhoneFilter($data['phone']);
	
	if ($err) {
		$err_field['phone'] = 1;
		$smarty->assign('err_field', $err_field);
		//return $err;
		EditForm('edit', $err, $data);
	}
	
	if ($config['voipcall_feature'] == 1) {
		// phone already exists
		$check_exist = $dbconn->getOne('SELECT 1 FROM '.USERS_TABLE.' WHERE phone <> "" AND phone="' . $data['phone'] . '" AND id<>' . $id);
		
		if (!empty($check_exist)) {
			$err_field['phone'] = 1;
			$smarty->assign('err_field', $err_field);
			//return $lang['err']['exists_phone'];
			$err = $lang['err']['exists_phone'];
			EditForm('edit', $err, $data);
		}
		
		// check phone update
		$phone_update = false;
		
		$check_exist = $dbconn->getOne('SELECT id FROM ' . USERS_TABLE . ' WHERE phone<>"' . $data['phone'] . '" AND id=' . $id);
		
		if (!empty($check_exist)) {
			$phone_update = true;
		}
	}
	
	// check date not valid
	if (checkdate($data['chk_month'], $data['chk_day'], $data['chk_year'])) {
		$data['chk_date'] = sprintf('%04d-%02d-%02d', $data['chk_year'], $data['chk_month'], $data['chk_day']);
	} elseif ($data['chk_month'] == 0 && $data['chk_day'] == 0 && $data['chk_year'] == 0) {
		//no need to validate
		$data['chk_date'] = '';
	} else {
		$err_field['chk_date'] = 1;
		$smarty->assign('err_field', $err_field);
		$err = $lang['err']['invalid_date'];
		EditForm('edit', $err, $data);
	}
	
	// birthdate not valid
	if (checkdate($data['b_month'], $data['b_day'], $data['b_year'])) {
		$data['date_birthday'] = sprintf('%04d-%02d-%02d', $data['b_year'], $data['b_month'], $data['b_day']);
	} else {
		$err_field['date_birthday'] = 1;
		$smarty->assign('err_field', $err_field);
		//return $err = $lang['err']['invalid_date'];
		$err = $lang['err']['invalid_date'];
		EditForm('edit', $err, $data);
	}
	
	/*
	if (!$couple_user && !empty($couple_login)) {
		// couple login already exists
		$couple_user = $dbconn->getOne('SELECT id FROM '.USERS_TABLE.' WHERE login="'.$couple_login.'" AND id<>"'.$id);
		
		if (!empty($couple_user)) {
			$couple_send = true;
		} else {
			$err_field['couple'] = 1;
			$smarty->assign('err_field', $err_field);
			//return $lang['err']['wrong_couple_login'];
			$err = $lang['err']['wrong_couple_login'];
			EditForm('edit', $err, $data);
		}
	}
	*/
	
	/*
	// check badwords and contacts in headline
	if ($err = BadWordsCont($headline, 4)) {
		$err_field['headline'] = 1;
		$smarty->assign('err_field', $err_field);
		return $err;
	}
	
	if (check_filter($headline)) {
		$err_field['headline'] = 1;
		$smarty->assign('err_field', $err_field);
		return $lang['err']['info_finding_1'];
	}
	*/
	
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
	
	//VP fetching users existing Status from database
	$old_status = $dbconn->getOne('SELECT status FROM ' . USERS_TABLE . ' WHERE id = ?', array($id));
	
	//Removed platinum applied and add later if passed all the tests BY Narendra
	// UPDATE
	$strSQL =
		"UPDATE ".USERS_TABLE." SET
			login						= '" . $data['login'] . "',
			big_icon_path				= '" . $data['big_icon_path'] . "',
			status						= '" . $data['status'] . "',
			fname						= '" . $data['fname'] . "',
			sname						= '" . $data['sname'] . "',
			mm_nickname					= '" . $data['mm_nickname'] . "',
			gender						= '" . $data['gender'] . "',
			mm_marital_status			= '" . $data['mm_marital_status'] . "',
			date_birthday				= '" . $data['date_birthday'] . "',
			mm_place_of_birth			= '" . $data['mm_place_of_birth'] . "',
			id_nationality				= '" . $data['id_nationality'] . "',
			mm_id_number				= '" . $data['mm_id_number'] . "',
			mm_id_type					= '" . $data['mm_id_type'] . "',
			id_weight					= '" . $data['id_weight'] . "',
			id_height					= '" . $data['id_height'] . "',
			headline					= '" . $data['headline'] . "',
			email						= '" . $data['email'] . "',
			mm_contact_phone_number		= '" . $data['mm_contact_phone_number'] . "',
			mm_contact_mobile_number	= '" . $data['mm_contact_mobile_number'] . "',
			mm_best_call_time_weekdays	= '" . $data['mm_best_call_time_weekdays'] . "',
			mm_best_call_time_saturdays	= '" . $data['mm_best_call_time_saturdays'] . "',
			mm_best_call_time_sundays	= '" . $data['mm_best_call_time_sundays'] . "',
			mm_platinum_submit_comment	= '" . $data['platinum_submit_comment'] . "',
			phone						= '" . $data['phone'] . "',
			id_country					= '" . $data['id_country'] . "',
			id_region					= '" . $data['id_region'] . "',
			id_city						= '" . $data['id_city'] . "',
			mm_city						= '" . $data['mm_city'] . "',
			zipcode						= '" . $data['zipcode'] . "',
			mm_address_1				= '" . $data['mm_address_1'] . "',
			mm_address_2				= '" . $data['mm_address_2'] . "',
			mm_address_3				= '" . $data['mm_address_3'] . "',
			site_language				= '" . $data['site_language'] . "',
			id_language_1				= '" . $data['id_language_1'] . "',
			id_language_2				= '" . $data['id_language_2'] . "',
			id_language_3				= '" . $data['id_language_3'] . "',
			mm_level_of_english			= '" . $data['mm_level_of_english'] . "',
			mm_employment_status		= '" . $data['mm_employment_status'] . "',
			mm_business_name			= '" . $data['mm_business_name'] . "',
			mm_employer_name			= '" . $data['mm_employer_name'] . "',
			mm_job_position				= '" . $data['mm_job_position'] . "',
			mm_work_address				= '" . $data['mm_work_address'] . "',
			mm_work_phone_number		= '" . $data['mm_work_phone_number'] . "',
			mm_ref_1_first_name			= '" . $data['mm_ref_1_first_name'] . "',
			mm_ref_1_last_name			= '" . $data['mm_ref_1_last_name'] . "',
			mm_ref_1_relationship		= '" . $data['mm_ref_1_relationship'] . "',
			mm_ref_1_phone_number		= '" . $data['mm_ref_1_phone_number'] . "',
			mm_ref_2_first_name			= '" . $data['mm_ref_2_first_name'] . "',
			mm_ref_2_last_name			= '" . $data['mm_ref_2_last_name'] . "',
			mm_ref_2_relationship		= '" . $data['mm_ref_2_relationship'] . "',
			mm_ref_2_phone_number		= '" . $data['mm_ref_2_phone_number'] . "',
			about_me					= '" . $data['about_me'] . "',
			what_i_do					= '" . $data['what_i_do'] . "',
			my_idea						= '" . $data['my_idea'] . "',
			hoping_to_find				= '" . $data['hoping_to_find'] . "',
			comment						= '" . $data['comment'] . "',
			chk_background				= '" . $data['chk_background'] . "',
			chk_marital_status			= '" . $data['chk_marital_status'] . "',
			chk_work_history			= '" . $data['chk_work_history'] . "',
			chk_interview_photo			= '" . $data['chk_interview_photo'] . "',
			chk_date					= '" . $data['chk_date'] . "',
			chk_staff					= '" . $data['chk_staff'] . "',
			chk_comment					= '" . $data['chk_comment'] . "',
			id_height					= '" . $data['id_height'] . "',
			id_weight					= '" . $data['id_weight'] . "'
			" . $pass_str . "
	  WHERE id = ". $id ." AND root_user = '0'";
	
	//echo $strSQL;
	// Bugfixing for temporary cause. by NARENDRA.
	$dbconn->Execute($strSQL);
	
	$check_exist = $dbconn->getOne('SELECT id FROM '.USER_PRIVACY_SETTINGS.' WHERE id_user = ?', array($id));
	
	if (!empty($check_exist))
	{
		$strSQL =
			"UPDATE ".USER_PRIVACY_SETTINGS." SET
				hide_online		= '" . $data['hide_online'] . "',
				promotion_1		= '" . $data['promotion_1'] . "',
				promotion_2		= '" . $data['promotion_2'] . "',
				promotion_3		= '" . $data['promotion_3'] . "',
				featured_land	= '" . $data['featured_land'] . "',
				featured_home	= '" . $data['featured_home'] . "',
				visible_lady	= '" . $data['visible_lady'] . "',
				visible_guy		= '" . $data['visible_guy'] . "',
				vis_lady_1		= '" . $data['vis_lady_1'] . "',
				vis_lady_2		= '" . $data['vis_lady_2'] . "',
				vis_lady_3		= '" . $data['vis_lady_3'] . "',
				vis_guy_1		= '" . $data['vis_guy_1'] . "',
				vis_guy_2		= '" . $data['vis_guy_2'] . "',
				vis_guy_3		= '" . $data['vis_guy_3'] . "',
				vis_guy_4		= '" . $data['vis_guy_4'] . "'
			WHERE id_user = '" . $id . "' ";
	}
	else
	{
		$strSQL =
				"INSERT INTO " . USER_PRIVACY_SETTINGS . " (id_user, hide_online, promotion_1, promotion_2, promotion_3, featured_land, featured_home, visible_lady, visible_guy, vis_lady_1, vis_lady_2, vis_lady_3, vis_guy_1, vis_guy_2, vis_guy_3, vis_guy_4)
				VALUES ('" . $id . "', '" . $data['hide_online'] . "', '" . $data['promotion_1'] . "', '" . $data['promotion_2'] . "', '" . $data['promotion_3'] . "', '" . $data['featured_land'] . "', '" . $data['featured_home'] . "', '" . $data['visible_lady'] . "', '" . $data['visible_guy'] . "', '" . $data['vis_lady_1'] . "', '" . $data['vis_lady_2'] . "', '" . $data['vis_lady_3'] . "', '" . $data['vis_guy_1'] . "', '" . $data['vis_guy_2'] . "', '" . $data['vis_guy_3'] . "', '" . $data['vis_guy_4'] . "') ";
	}
	
	$dbconn->Execute($strSQL);
	
	$rs = $dbconn->Execute('SELECT 1 FROM '.USER_MATCH_TABLE.' WHERE id_user = ?', array($id));
	
	if (!$rs->EOF) {
		$dbconn->Execute(
			'UPDATE '.USER_MATCH_TABLE.'
				SET gender = ?, age_max = ?, age_min = ?, id_relationship = ?
			  WHERE id_user = ?',
			array((string) $data['gender_search'], $data['age_max'], $data['age_min'], $data['relation'], $id));
	} else {
		$dbconn->Execute(
			'INSERT INTO '.USER_MATCH_TABLE.'
				SET id_user = ?, gender = ?, age_max = ?, age_min = ?, id_relationship = ?',
			array($id, (string) $data['gender_search'], $data['age_max'], $data['age_min'], $data['relation']));
	}
	
	$profile_percent = new Percent($id);
	$profile_percent->UpdateSection1Percent();
	
	// newsletter update
	UpdateNewsletterUserData($id, $data['fname'], $data['sname'], $data['email']);
	
	// Move user to appropriate Platinum group when platinum_verified is checked and the user
	// does not already belong to a Platinum group
	$platinum_group_arr = array(
		MM_PLATINUM_LADY_SECOND_INS_ID,
		MM_PLATINUM_LADY_FIRST_INS_ID,
		MM_PLATINUM_GUY_ID,
		MM_PLATINUM_LADY_ID
	);
	
	if ($data['platinum_verified'] == '1')
	{ 
		// allow admin to kick any user to Platinum group
		// it's a bit dangerous but seems to be a requirement from the office
		//if (!in_array($current_id_group, $platinum_group_arr)) {
			$debug = false;
			
			// assign Platinum group
			
			//Added By Narendra -----start---------
			//changing code to update user groups as admin verifies that the user is platinum (installments or otherwise)
			
			$rs2 = $dbconn->Execute(
				'SELECT mm_first_installment_date,
						mm_second_installment_date, 
						platinum_verified,
						mm_platinum_applied,
						mm_platinum_paid,
						chk_background,
						chk_marital_status,
						chk_work_history,
						chk_interview_photo 
				   FROM '.USERS_TABLE.'
				  WHERE id = ?', array($id));
			
			$userDetails = $rs2->GetRowAssoc(false);
			
			// double-check radio controls
			$checkArrs = array(
				$userDetails['chk_background'],
				$userDetails['chk_marital_status'],
				$userDetails['chk_interview_photo'],
				$userDetails['chk_work_history'],
			);
			
			$verified = true;
			
			foreach ($checkArrs as $chk) {
				if ($chk != 'VR' && $chk != 'NA') {
					$verified = false;
					break;
				}
			}
			
			if ($verified) {
				//echo "verified <pre>"; print_r($data['platinum_verified']); echo "</pre>";die;
				$fd = fopen(dirname(__FILE__).'/adminApproval.txt', 'wb');
				
				$old_group_id = $dbconn->GetOne('SELECT id_group FROM '.USER_GROUP_TABLE.' WHERE id_user = ?', array($id));
				fwrite($fd, $old_group_id." is the old group id");
				
				$dbconn->Execute('UPDATE '.USERS_TABLE.' SET platinum_verified = "1" WHERE id = ?', array($id));
				
				// move to group and set date_end
				if (!empty($userDetails['mm_platinum_paid']) && !empty($userDetails['mm_first_installment_date']) && !empty($userDetails['mm_second_installment_date'])) {
					
					if ($data['gender'] == GENDER_FEMALE) {
						fwrite($fd, "She has paid 3rd installment");
						
						$id_group			= MM_PLATINUM_LADY_ID;
						$id_group_period	= MM_PLATINUM_LADY_THIRD_INS_PERIOD_ID;
						$date_end			= UNLIMITED_DATE_END;
					} else {
						fwrite($fd, "He has paid 3rd installment but there are no installments defined for guys !");
					}
					
				} elseif (!empty($userDetails['mm_platinum_paid'])) {
					
					if ($data['gender'] == GENDER_MALE) {
						fwrite($fd, "He has paid platinum matching");
						
						$id_group			= MM_PLATINUM_GUY_ID;
						$id_group_period	= MM_PLATINUM_GUY_PERIOD_ID;
					} else {
						fwrite($fd, "She has paid platinum lifetime");
						
						$id_group			= MM_PLATINUM_LADY_ID;
						$id_group_period	= MM_PLATINUM_LADY_PERIOD_ID;
						$date_end			= UNLIMITED_DATE_END;
					}
					
				} elseif (!empty($userDetails['mm_first_installment_date']) && !empty($userDetails['mm_second_installment_date'])) {
					
					if ($data['gender'] == GENDER_FEMALE) {
						fwrite($fd, "She has paid first and second installments");
						
						$id_group			= MM_PLATINUM_LADY_SECOND_INS_ID;
						$id_group_period	= MM_PLATINUM_LADY_SECOND_INS_PERIOD_ID;
						$days				= getTimePeriodInDays(MM_PLATINUM_LADY_FIRST_INS_PERIOD_ID) + getTimePeriodInDays(MM_PLATINUM_LADY_SECOND_INS_PERIOD_ID);
						$addDays			= "+" . $days . " days";
						$date_end			= date('Y-m-d H:i:s', strtotime($addDays));
					} else {
						fwrite($fd, "He has paid first and second installments but there are no installments defined for guys !");
					}
					
				} elseif (!empty($userDetails['mm_first_installment_date'])) {
					
					if ($data['gender'] == GENDER_FEMALE) {
						fwrite($fd, "She has paid only first installments");
						
						$id_group			= MM_PLATINUM_LADY_FIRST_INS_ID;
						$id_group_period	= MM_PLATINUM_LADY_FIRST_INS_PERIOD_ID;
						$addDays			= "+" . getTimePeriodInDays(MM_PLATINUM_LADY_FIRST_INS_PERIOD_ID) . " days";
						$date_end			= date('Y-m-d H:i:s', strtotime($addDays));
					} else {
						fwrite($fd, "He has paid only first installment but there are no installments defined for guys !");
					}
				}
				
				if (isset($id_group)) {
					$dbconn->Execute('UPDATE '.USER_GROUP_TABLE.' SET id_group = ? WHERE id_user = ?', array($id_group, $id));
					
					if (isset($date_end)) {
						$dbconn->Execute(
							'UPDATE '.BILLING_USER_PERIOD_TABLE.'
								SET id_group_period = ?,
									date_end = ?
							  WHERE id_user = ?',
							array($id_group_period, $date_end, $id)
						);
					} else {
						$dbconn->Execute(
							'UPDATE '.BILLING_USER_PERIOD_TABLE.'
								SET id_group_period = ?
							  WHERE id_user = ?',
							array($id_group_period, $id)
						);
					}
					
					//Logging Changes in user group
					$dbconn->Execute('INSERT INTO '. USER_GROUP_HISTORY_TABLE .' SET 
						`from` = ?, `to` = ?, `id_user` = ?, `staff` = ?, `comment` = ?, `date` = NOW()', 
						array($old_group_id, $id_group, $id, $data['chk_staff'], $data['chk_comment']));
				}
				
				if ($debug) echo 'assigned group '.$id_group.' to user '.$id.' in table '.USER_GROUP_TABLE.'<br/>';
				
				fclose($fd);
			} else {
				
				$err_field['not_verified'] = 1;
				$smarty->assign('err_field', $err_field);
				$err = $lang['err']['not_verified'];
				EditForm('edit', $err, $data);
			}
			
			// ----------------------  END ---------------------------
			
			if ($debug) exit;
			
			//Send external and internal mail
			SendPlatinumApprovalMail($id);
		//} 
	}
	elseif ($data['platinum_verified'] == '2')
	{
		//echo "Rejected <pre>"; print_r($data['platinum_verified']); echo "</pre>";die;
		$dbconn->Execute('UPDATE '.USERS_TABLE.' SET platinum_verified = "2" WHERE id = ?', array($id));
		
	}
	
	// include mail language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($data['site_language']));
	$lang_mail = array();
	include $config['path_lang'] . 'mail/' . $lang_file;
	
	// language suffix
	$suffix = ($data['gender'] == GENDER_MALE) ? '_e' : '_t';
	
	// send email to user when password was changed
	if ($data['conf']) {
		$content			= array();
		$content['fname']	= $data['fname'];
		$content['sname']	= $data['sname'];
		$content['login']	= $data['login'];
		$content['pass']	= $data['pass'];
		$content['status']	= $data['status'];
		$content['email']	= $data['email'];
		$content['urls']	= GetUserEmailLinks();
		
		// subject
		$subject = $lang_mail['admin_data_changed' . $suffix]['subject'];
		
		// recipient
		$name_to = trim($data['fname'] . ' ' . $data['sname']);
		
		SendMail($data['site_language'], $data['email'], $config['site_email'], $subject, $content,
			'mail_admin_data_changed_for_user', null, $name_to, '', 'admin_data_changed', $data['gender']);
	}
	
	// send email to user when status was changed
	if ($data['status'] != $old_status) {
		$content			= array();
		$content['fname']	= $data['fname'];
		$content['sname']	= $data['sname'];
		$content['login']	= $data['login'];
		$content['pass']	= $data['pass'];
		$content['status']	= $data['status'];
		$content['email']	= $data['email'];
		$content['gender']	= $data['gender'];
		$content['urls']	= GetUserEmailLinks();
		
		if ($data['status'] == 1) {
			$subject = $lang_mail['status_change_on' . $suffix]['subject'];
			$name_to = trim($data['fname'] . ' ' . $data['sname']);
			SendMail($data['site_language'], $data['email'], $config['site_email'], $subject, $content,
				'mail_status_change_user', null, $name_to, '', 'status_change', $data['gender']);
		} else {
			// account suspended email disabled
			## $subject = $lang_mail['status_change_off'.$suffix]['subject'];
			## $name_to = trim($data['fname'].' '.$sname);
			## SendMail($email, $config['site_email'], $subject, $content, 'mail_status_change_user', null,
			##	$name_to, '', 'status_change', $gender);
		}
	}
	
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
		$group_name = $dbconn->GetOne(
			'SELECT g.name
			   FROM '.GROUPS_TABLE.' g
		 INNER JOIN '.USER_GROUP_TABLE.' ug ON g.id = ug.id_group
			  WHERE ug.id_user = ?',
			array($id));
		
		$solve360 = array();
		require $config['site_path'].'/include/Solve360CustomFields.php';
		
		$contactData = array(
		#	$solve360['TLDF ID Number']			=> $id_user,							// immutable
			'firstname'							=> $data['fname'],
			'lastname'							=> $data['sname'],
			$solve360['TLDF Status']			=> ($data['status'] ? 'Good' : 'Inactive'),
			$solve360['Platinum Verified']		=> ($data['platinum_verified'] ? 'Yes' : 'No'), // set by admin
		#	$solve360['TLDF Confirmed']			=> 'No',								// not changed here
			$solve360['TLDF Login']				=> $data['login'],
			$solve360['Gender']					=> ($data['gender'] == GENDER_MALE ? 'Guy' : 'Lady'),
			'personalemail'						=> $data['email'],
			$solve360['Country']				=> $country,							// lookup
			$solve360['Region']					=> $region,								// lookup
			$solve360['Nationality']			=> $nationality,						// lookup
			$solve360['Language 1']				=> $language_1,							// lookup
			$solve360['Birthday']				=> substr($data['date_birthday'], 0, 10),
		#	$solve360['Last Seen TLDF']			=> date('Y-m-d H:i:s'),					// date/time
		#	$solve360['Registration Date']		=> date('Y-m-d H:i:s'),					// date/time, immutable
		#	$solve360['TLDF Login Count']		=> 0,									// not changed here
			$solve360['Nick Name']				=> $data['mm_nickname'],
			$solve360['National ID Number']		=> $data['mm_id_number'],
			$solve360['ID Type']				=> $data['mm_id_type'],
			'homephone'							=> $data['mm_contact_phone_number'],
			'cellularphone'						=> $data['mm_contact_mobile_number'],
			$solve360['Marital Status']			=> $marital_status,						// lookup
			$solve360['Place Of Birth']			=> $data['mm_place_of_birth'],
			$solve360['City']					=> $data['mm_city'],
			$solve360['Home Address 1']			=> $data['mm_address_1'],
			$solve360['Home Address 2']			=> $data['mm_address_2'],
			$solve360['Home Address 3']			=> $data['mm_address_3'],
			$solve360['Level Of English']		=> $level_of_english,					// lookup
			$solve360['Employer Name']			=> $data['mm_employer_name'],
			'jobtitle'							=> $data['mm_job_position'],
			'businessaddress'					=> $data['mm_work_address'],
			'businessphonedirect'				=> $data['mm_work_phone_number'],
		#	$solve360['Platinum Form']			=> '',									// date/time, not changed here
		#	$solve360['Platinum Paid']			=> '',									// date/time, not changed here
		#	$solve360['TLDE Express Interest']	=> '',									// date/time, not changed here
			$solve360['Current Group']			=> $group_name,							// possibly changed after platinum verification
		#	$solve360['TLDF Trial Start Date']	=> '',									// date/time, not changed here
		#	$solve360['TLDF Membership Ends']	=> UNLIMITED_DATE_END,					// date/time, not changed here
		);
		
		if ($data['platinum_verified'] && $current_id_group != MM_PLATINUM_GUY_ID && $current_id_group != MM_PLATINUM_LADY_ID) {
			// Remove categories
			$contactData['categories'] = array(
				'remove' => array('category' => array(SOLVE360_TAG_PLATINUM_APPLIED))
			);
		}
		
		$id_solve360 = $dbconn->GetOne('SELECT id_solve360 FROM '.USERS_TABLE.' WHERE id = ?', array($id));
		
		if (!empty($id_solve360)) {
			$contact = $solve360Service->editContact($id_solve360, $contactData);
			#var_dump($contact); exit;
			if (isset($contact->errors)) {
				$subject = 'Error while updating contact after admin edits profile';
				solve360_api_error($contact, $subject, $data['login']);
			}
		}
		// maybe add contact if not found
	}
	
	// redirect
	$presel = isset($_REQUEST['pre_sel']) ? FormFilter($_REQUEST['pre_sel']) : '';
	
	if ($presel == 'featured') {
		##echo '<script>document.location.href="admin_featured_users.php";</script>';
		header('Location: admin_featured_users.php');
		exit;
	} else {
		##echo '<script>document.location.href="'.$file_name.'?sel='.$presel.'";</script>';
		header('Location: admin_users.php?sel='.$presel);
		exit;
	}
}


function DelUser($id)
{
	global $dbconn, $config;
	
	if (!$id) {
		return;
	}
	
	$settings = GetSiteSettings(array('icons_folder', 'photos_folder', 'audio_folder', 'video_folder', 'attaches_folder'));
	
	// delete uploads
	$rs_upl = $dbconn->Execute('SELECT upload_path, upload_type, id FROM '.USER_UPLOAD_TABLE.' WHERE id_user = ?', array($id));
	
	while (!$rs_upl->EOF) {
		$dbconn->Execute('DELETE FROM '.GALLERY_RATING_TABLE.' WHERE id_upload = ?', array($rs_upl->fields[2]));
		if ($rs_upl->fields[1] == 'f') {
			$file_folder = $settings['photos_folder'];
		} elseif ($rs_upl->fields[1] == 'a') {
			$file_folder = $settings['audio_folder'];
		} else {
			$file_folder = $settings['video_folder'];
		}
		if (strlen($rs_upl->fields[0]) > 0) {
			$old_file = $config['site_path'] . $file_folder . '/' . $rs_upl->fields[0];
			$old_file_thumb = $config['site_path'] . $file_folder . '/thumb_' . $rs_upl->fields[0];
			if (file_exists($old_file)) {
				unlink($old_file);
			}
			if (file_exists($old_file_thumb)) {
				unlink($old_file_thumb);
			}
		}
		$rs_upl->MoveNext();
	}
	
	$dbconn->Execute('DELETE FROM '.USER_UPLOAD_TABLE.' WHERE id_user = ?', array($id));
	
	// delete user icons
	$rs_upl = $dbconn->Execute('SELECT big_icon_path, icon_path_temp FROM ' . USERS_TABLE . ' WHERE id = ?', array($id));
	$big_icon_path = $rs_upl->fields[0] ? $rs_upl->fields[0] : $rs_upl->fields[1];
	$file_name = substr($big_icon_path, strlen('thumb_'));
	
	if (strlen($big_icon_path) > 0) {
		$old_file = $config['site_path'] . $settings['icons_folder'] . '/' . $file_name;
		$thumb_old_file = $config['site_path'] . $settings['icons_folder'] . '/thumb_' . $file_name;
		$main_thumb_old_file = $config['site_path'] . $settings['icons_folder'] . '/main_thumb_' . $file_name;
		$big_thumb_old_file = $config['site_path'] . $settings['icons_folder'] . '/big_thumb_' . $file_name;
		
		if (file_exists($old_file)) {
			unlink($old_file);
		}
		if (file_exists($thumb_old_file)) {
			unlink($thumb_old_file);
		}
		if (file_exists($main_thumb_old_file)) {
			unlink($main_thumb_old_file);
		}
		if (file_exists($big_thumb_old_file)) {
			unlink($big_thumb_old_file);
		}
	}
	
	// delete user record
	$dbconn->Execute('DELETE FROM '.USERS_TABLE.' WHERE id = ? AND root_user = "0" AND guest_user = "0"', array($id));
	
	// delete attachments
	$rs = $dbconn->Execute('SELECT attach_name FROM '.MAILBOX_ATTACHES_TABLE.' WHERE id_user = ?', array($id));
	
	while (!$rs->EOF) {
		$attach = $config['site_path'] . $settings['attaches_folder'] . $rs->fields[0];
		if (file_exists($attach)) {
			unlink($attach);
		}
		$rs->MoveNext();
	}
	
	// delete all the other stuff
	$dbconn->Execute('DELETE FROM ' . ACCOUNT_ALERTS_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . BADWORDS_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . BILLING_ENTRY_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . BILLING_REQUESTS_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . BILLING_USER_ACCOUNT_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . BILLING_USER_PERIOD_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . BLACKLIST_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . BLACKLIST_TABLE . ' WHERE id_enemy = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . CONNECTIONS_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . CONNECTIONS_TABLE . ' WHERE id_friend = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . CRON_ACTION_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . DESCR_SPR_MATCH_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . DESCR_SPR_USER_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . GALLERY_RATING_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . HOTLIST_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . HOTLIST_TABLE . ' WHERE id_friend = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . INTERESTS_SPR_USER_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . INTERESTS_SPR_MATCH_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . KISSLIST_TABLE . ' WHERE id_from = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . KISSLIST_TABLE . ' WHERE id_to = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . MAILBOX_TABLE . ' WHERE id_from = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . MAILBOX_TABLE . ' WHERE id_to = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . MAILBOX_ATTACHES_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . ONLINE_NOTICE_TABLE . ' WHERE id_to = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . ONLINE_NOTICE_TABLE . ' WHERE id_from = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . PERSON_SPR_USER_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . PERSON_SPR_MATCH_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . PORTRAIT_SPR_USER_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . PORTRAIT_SPR_MATCH_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . PROFILE_VISIT_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . PROFILE_VISIT_TABLE . ' WHERE id_visiter = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . SAVESEARCH_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . SHOUTS_TABLE . ' WHERE user_id = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . SUBSCRIBE_USER_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . TAGS_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . TAGS_TABLE . ' WHERE id_creator = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . USER_ALBUMS . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . USER_COMMENT_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . USER_COMMENT_TABLE . ' WHERE id_voter = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . USER_GROUP_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . USER_MATCH_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . USER_PRIVACY_SETTINGS . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . USER_PROFILE_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . USER_RATING_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . USER_RATING_TABLE . ' WHERE id_voter = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . USER_REFER_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . USER_REFER_TABLE . ' WHERE id_refer = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . USER_REFER_CODE_TABLE . ' WHERE id_user = ?', array($id));
	$dbconn->Execute('DELETE FROM ' . USER_TOPTEN_TABLE . ' WHERE id_user = ?', array($id));
	
	// if forum module installed
	if ($config['use_pilot_module_forum']) {
		DeleteUserFromForum($id);
	}
	
	// if events module installed
	if ($config['use_pilot_module_events']) {
		include_once('../include/functions_events.php');
		DeleteUserFromEvents($id);
	}
	
	// if flash IM installed
	if ($config['use_pilot_module_im']) {
		include_once('../w_communicator/wc_config.php');
		include_once('../w_communicator/wc_functions.php');
		delete_site_user($id);
	}
	
	SetNewsletterUserUnactive($id);
	return;
}


function DelUsers()
{
	global $page;
	
	$file_name = 'admin_users.php';
	
	$page	= isset($_POST['page']) ? (int) $_POST['page'] : 1;
	$letter	= (isset($_POST['letter']) && (int) $_POST['letter'] > 0) ? (int) $_POST['letter'] : '*';
	$sorter	= isset($_POST['sorter']) ? (int) $_POST['sorter'] : 1;
	$search	= isset($_POST['search']) ? trim($_POST['search']) : '';
	$s_type	= isset($_POST['s_type']) ? (int) $_POST['s_type'] : 1;
	$s_stat	= isset($_POST['s_stat']) ? trim($_POST['s_stat']) : '';
	
	foreach ($_POST['delete'] as $val) {
		DelUser($val);
	}
	
	#ListUser();
	echo '<form name="hide" action="' . $file_name . '" method="get">';
	echo '<input type="hidden" name="page" value="' . $page . '">';
	echo '<input type="hidden" name="search" value="' . $search . '">';
	echo '<input type="hidden" name="s_type" value="' . $s_type . '">';
	echo '<input type="hidden" name="s_stat" value="' . $s_stat . '">';
	echo '<input type="hidden" name="sorter" value="' . $sorter . '">';
	echo '<input type="hidden" name="letter" value="' . $letter . '">';
	echo '</form>';
	echo '<script>document.hide.submit();</script>';
	exit;
}


function UpdateStatus()
{
	global $dbconn, $config, $config_admin;
	
	$file_name = 'admin_users.php';
	
	$page	= isset($_POST['page']) ? (int) $_POST['page'] : 1;
	$letter = (isset($_POST['letter']) && (int) $_POST['letter'] > 0) ? (int) $_POST['letter'] : '*';
	$sorter = isset($_POST['sorter']) ? (int) $_POST['sorter'] : 1;
	$search = isset($_POST['search']) ? $_POST['search'] : '';
	$s_type = isset($_POST['s_type']) ? (int) $_POST['s_type'] : 1;
	$s_stat = isset($_POST['s_stat']) ? $_POST['s_stat'] : '';
	$order	= isset($_POST['order']) ? (int) $_POST['order'] : 1;
	
	$pre_sel = isset($_REQUEST['pre_sel']) ? $_REQUEST['pre_sel'] : '';
	
	$hactive = $_POST['hactive'];										// array of all user ids
	$active = isset($_POST['active']) ? $_POST['active'] : array();		// array of checked status
	
	if (count($hactive) > $config_admin['users_numpage']) {
		$count = $config_admin['users_numpage'];
	} else {
		$count = count($hactive);
	}
	
	for ($i = 1; $i <= $count; $i++) {
		$index = ($page - 1) * $config_admin['users_numpage'] + $i;
		$strSQL =
			'SELECT status
			   FROM '.USERS_TABLE.'
			  WHERE id = "'.$hactive[ $index ].'" AND root_user = "0" AND guest_user = "0"';
		$rs = $dbconn->Execute($strSQL);
		
		$status[ $index ] = $rs->fields[0] ? $rs->fields[0] : '0';
		
		$active[ $index ] = isset($active[ $index ]) ? '1' : '0';
	}
	
	for ($i = 1; $i <= $count; $i++) {
		$index = ($page - 1) * $config_admin['users_numpage'] + $i;
		$strSQL =
			"UPDATE ".USERS_TABLE." SET
					status = '".intval($active[ $index ])."',
					confirm = IF(confirm = '0', '".$active[ $index ]."', '1')
			  WHERE id = '".$hactive[ $index ]."' AND root_user = '0' AND guest_user = '0'";
		$dbconn->Execute($strSQL);
	}
	
	// UPDATE TLDF STATUS IN SOLVE360
	if (SOLVE360_CONNECTION) {
		require_once $config['site_path'].'/include/Solve360Service.php';
		$solve360Service = new Solve360Service(SOLVE360_USER, SOLVE360_TOKEN);
		$solve360 = array();
		require $config['site_path'].'/include/Solve360CustomFields.php';
	}
	
	for ($i = 1; $i <= $count; $i++)
	{
		$index = ($page - 1) * $config_admin['users_numpage'] + $i;
		
		if ($status[ $index ] != $active[ $index ])
		{
			// send status change email
			$strSQL =
				'SELECT fname, sname, mm_nickname, gender, email, status, login, site_language, id_solve360
				   FROM '.USERS_TABLE.'
				  WHERE id = "'.$hactive[ $index ].'" AND root_user = "0" AND guest_user = "0"';
			$rs = $dbconn->Execute($strSQL);
			$row = $rs->GetRowAssoc(false);
			
			$site_language		= $row['site_language'];
			
			$content			= array();
			$content['fname']	= stripslashes($row['fname']);
			$content['sname']	= stripslashes($row['sname']);
			$content['nick']	= stripslashes($row['mm_nickname']);
			$content['gender']	= $row['gender'];
			$content['email']	= $row['email'];
			$content['status']	= $row['status'];
			$content['login']	= $row['login'];
			$content['urls']	= GetUserEmailLinks();
			
			// include mail language file
			$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_language));
			$lang_mail = array();
			include $config['path_lang'].'mail/'.$lang_file;
			
			// language suffix
			$suffix = ($content['gender'] == GENDER_MALE) ? '_e' : '_t';
			
			if ($content['status'] == 1) {
				$subject = $lang_mail['status_change_on'.$suffix]['subject'];
			} else {
				// suspended message deactivated
				// $subject = $lang_mail['status_change_off'.$suffix]['subject'];
			}
			
			if ($content['status'] == 1) {
				$name_to = trim($content['fname'].' '.$content['sname']);
				SendMail($site_language, $content['email'], $config['site_email'], $subject, $content,
					'mail_status_change_user', null, $name_to, '', 'status_change', $content['gender']);
			}
			
			// UPDATE TLDF STATUS IN SOLVE360
			if (SOLVE360_CONNECTION) {
				$contactData = array(
					$solve360['TLDF Status'] => $row['status'] ? 'Good' : 'Inactive',
				);
				
				if (!empty($row['id_solve360'])) {
					$contact = $solve360Service->editContact($row['id_solve360'], $contactData);
					#var_dump($contact); exit;
					if (isset($contact->errors)) {
						$subject = 'Error while updating TLDF Status in admin back end';
						solve360_api_error($contact, $subject, $row['login']);
					}
				}
				// maybe add contact if not found
			}
		}
	}
	
	echo '<script>window.location.href="'.$file_name.'?sel='.$pre_sel.'&page='.$page.'&search='.$search.'&s_type='.$s_type.'&s_stat='.$s_stat.'&sorter='.$sorter.'&letter='.$letter.'&order='.$order.'";</script>';
	exit;
}


function Top10Form($err = '')
{
	global $smarty, $dbconn, $config, $page, $lang;
	
	$file_name = 'admin_users.php';
	
	AdminMainMenu($lang['users']);
	
	$page = (isset($_REQUEST['page']) && (int) $_REQUEST['page'] > 0) ? (int) $_REQUEST['page'] : 1;
	
	$num_records = $dbconn->GetOne('SELECT COUNT(*) FROM ' . USER_TOPTEN_TABLE);
	
	$users_numpage = 25;
	$lim_min = ($page - 1) * $users_numpage;
	$lim_max = $users_numpage;
	$smarty->assign('page', $page);
	
	$param = $file_name.'?sel=top&';
	$smarty->assign('links', GetLinkStr($num_records, $page, $param, $users_numpage));
	
	$strSQL =
		"SELECT a.id, a.id_user, a.rating, CONCAT(u.fname, ' ', u.sname) as username, u.login
		   FROM ".USER_TOPTEN_TABLE." a
	  LEFT JOIN ".USERS_TABLE." u ON u.id = a.id_user
	   ORDER BY a.rating DESC
		  LIMIT ".$lim_min.", ".$lim_max;
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$users = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$users[$i]['name']		= $row['username'];
		$users[$i]['id']		= $row['id_user'];
		$users[$i]['rating']	= $row['rating'];
		$users[$i]['login']		= $row['login'];
		$users[$i]['place']		= ($i + 1) + ($page - 1) * $users_numpage;
		$rs_count = $dbconn->Execute('SELECT COUNT(*) FROM ' . USER_RATING_TABLE . ' WHERE id_user = ?', array($row['id_user']));
		$users[$i]['rated'] = $rs_count->fields[0];
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('users', $users);
	
	$form['err'] = $err;
	$smarty->assign('form', $form);
	$smarty->assign('button', $lang['button']);
	$smarty->display(TrimSlash($config['admin_theme_path']) . '/admin_users_topten.tpl');
	exit;
}


function InvitingUsers()
{
	$_SESSION['invite_users'] = 1;
	$_SESSION['id_club'] = $_REQUEST['id_club'];
	ListUser();
}

function SendPlatinumApprovalMail($id_user)
{
	global $config, $dbconn;
	
	$rs = $dbconn->Execute(
		'SELECT login, fname, sname, gender, email, site_language FROM '.USERS_TABLE.' WHERE id = ?',
		array($id_user));
	$row = $rs->GetRowAssoc(false);
	
	// content array
	$content			= array();
	$content['login']	= stripslashes($row['login']);
	$content['fname']	= stripslashes($row['fname']);
	$content['sname']	= stripslashes($row['sname']);
	$content['email']	= stripslashes($row['email']);
	$content['urls']	= GetUserEmailLinks();
	
	$gender				= $row['gender'];
	$site_language		= $row['site_language'];
	
	// include mail language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_language));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	// language suffix
	$suffix = ($gender == GENDER_MALE) ? '_e' : '_t';
	
	// subject
	$subject = $lang_mail['platinum_approved'.$suffix]['subject'];
	
	// recipient
	$name_to = trim($content['fname'].' '.$content['sname']);
	
	SendMail($site_language, $content['email'], $config['site_email'], $subject, $content,
		'mail_platinum_approved_user', null, $name_to, '', 'platinum_approved', $gender);
	
	// internal message
	$body = $lang_mail['generic'.$suffix]['hello'].' '.$content['fname'].',<br><br>';
	$body.= $lang_mail['platinum_approved'.$suffix]['message'].'<br/><br/>';
	$body.= $lang_mail['generic'.$suffix]['admin_regards'];
	
	$strSQL =
		'INSERT INTO '.MAILBOX_TABLE.'
			SET id_to = ?, id_from = ?, subject = ?, body = ?, was_read = "0", deleted_to = "0",
				deleted_from = "0", date_creation = NOW()';
	$dbconn->Execute($strSQL, array($id_user, ID_ADMIN, $subject, $body));
	
	return;
}

?>