<?php
/**
* User connections page (listing, deleting)
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
if (!$user[ AUTH_STATUS ]) {
	AlertPage(GetRightModulePath(__FILE__));
}

// check permissions
IsFileAllowed(GetRightModulePath(__FILE__));

// alerts and statistics
if (!$user[ AUTH_GUEST ]) {
	GetAlertsMessage();
	SetModuleStatistic(GetRightModulePath(__FILE__));
}

// active menu item
$smarty->assign('sub_menu_num', '');

// user selection
$sel = isset($_REQUEST['sel']) ? $_REQUEST['sel'] : '';
$par = isset($_REQUEST['par']) ? $_REQUEST['par'] : '';

// limited access for trial users and inactive users
if ($sel == 'addform' || $sel == 'addsave' || $sel == 'addlist')
{
	if ($user[ AUTH_IS_TRIAL ]) {
		$_GET['par'] = 'send';
		SearchTable($lang['error']['access_denied_trial'], $par);
		exit;
	}
	if ($user[ AUTH_IS_INACTIVE ]) {
		$_GET['par'] = 'send';
		SearchTable($lang['error']['access_denied_inactive'], $par);
		exit;
	}
}

// dispatcher
switch ($sel) {
	case 'addform':
		AddToConnectionsForm();
	break;
	
	case 'addsave':
		$res = AddToConnections();
		AddToConnectionsForm($res['err']);
	break;
	
	case 'addlist':
		$res = AddToConnections();
		$sel = $_GET['par'];
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			header('Location: connections.php?sel='.$sel.'&par=send');
			exit;
		} else {
			$_GET['par'] = 'send';
			SearchTable($res['err'], $sel);
		}
	break;
	
	case 'addhotlist':
		$res = AddToHotList();
		$sel = $_GET['par'];
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			header('Location: connections.php?sel='.$sel.'&par=send');
			exit;
		} else {
			$_GET['par'] = 'send';
			SearchTable($res['err'], $sel);
		}
	break;
	
	case 'addblacklist':
		$res = AddToBlackList();
		$sel = $_GET['par'];
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			header('Location: connections.php?sel='.$sel.'&par=send');
			exit;
		} else {
			$_GET['par'] = 'send';
			SearchTable($res['err'], $sel);
		}
	break;
	
	case 'kiss':
		$res = SendKiss();
		$sel = $_GET['par'];
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			header('Location: connections.php?sel='.$sel.'&par=send');
			exit;
		} else {
			$_GET['par'] = 'send';
			SearchTable($res['err'], $sel);
		}
	break;
	
	case 'accept':
		$res = AcceptConnection();
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			header('Location: connections.php?sel=inbox');
			exit;
		} else {
			SearchTable($res['err'], 'inbox');
		}
	break;
	
	case 'del':
		$msg = DeleteFromList($par);
		$sel = $_GET['par'];
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $msg;
			header('Location: connections.php?sel='.$sel);
			exit;
		} else {
			SearchTable($msg, $sel);
		}
	break;
	
	case 'inbox':
		SearchTable('', 'inbox');
	break;
	
	case 'outbox':
		SearchTable('', 'outbox');
	break;
	
	default:
		SearchTable();
	break;
}

exit;


function SearchTable($err = '', $sel = '')
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;
	
	$debug = false;
	
	if ($debug) echo '<font color="red">';
	
	$id_user = $user[ AUTH_ID_USER ];
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$file_name = 'connections.php';
	
	if (isset($_SESSION['err'])) {
		$err = $_SESSION['err'];
		unset($_SESSION['err']);
	}
	
	if (MM_ENABLE_FRIENDS_FRIENDLIST)
	{
		if (isset($_GET['par']) && ($_GET['par'] == 'back' || $_GET['par'] == 'send') && isset($_SESSION['search_pars']['friend_id']))
		{
			// return from other page
			$id_user = $_SESSION['search_pars']['friend_id'];
			$form['friend_login'] = $_SESSION['search_pars']['friend_login'];
		}
		elseif (isset($_GET['id_user']))
		{
			// other user's list, check if he is in our connections list
			$rs = $dbconn->Execute(
				'SELECT u.login 
				   FROM '.USERS._TABLE.' u
			 INNER JOIN '.CONNECTIONS_TABLE.' l ON l.id_friend = u.id
				  WHERE l.id_user = ? AND l.id_friend = ?',
				array($id_user, (int)$_GET['id_user']));
			
			if ($rs->RowCount()) {
				unset($_SESSION['search_pars']);
				$id_user = $_SESSION['search_pars']['friend_id'] = (int)$_GET['id_user'];
				$form['friend_login'] = $_SESSION['search_pars']['friend_login'] = $rs->fields[0];
			}
		}
		else
		{
			// own list
			unset($_SESSION['search_pars']);
		}
	}
	
#	$percent = new Percent($config, $dbconn, $id_user);
	
	// settings
	$settings = GetSiteSettings(array(
		'icon_male_default',
		'icon_female_default',
		'icons_folder',
		'thumb_max_width',
		'show_users_connection_str',
		'show_users_comments',
		'show_users_group_str',
		'use_kiss_types',
		'use_friend_types',
		'use_pilot_module_giftshop',
	));
	
	$smarty->assign('icon_width', $settings['thumb_max_width']);
	
	// default icons
	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];
	
	// filter and view
	$filter	= isset($_REQUEST['filter']) ? $_REQUEST['filter'] : '';
	$view	= isset($_REQUEST['view']) && $_REQUEST['view'] == 'gallery' ? 'gallery' : 'list';
	
	if ($debug) echo '$filter='.$filter.'<br>$view='.$view.'<br>';
	
	// par
	// possible values: back, send
	$par = isset($_GET['par']) ? trim($_GET['par']) : '';
	
	if ($debug) echo '$par='.$par.'<br>';
	
	// page to display
	if ($par != 'back' && $par != 'send') {
		unset($_SESSION['search_page']);
	}
	if (isset($_REQUEST['page'])) {
		$_SESSION['search_page'] = (int) $_REQUEST['page'];
	}
	if (empty($_SESSION['search_page'])) {
		$_SESSION['search_page'] = 1;
	}
	
	if ($debug) echo '$_SESSION[\'search_page\']='.$_SESSION['search_page'].'<br>';
	
	//VP storing records per page value in session
	if (!empty($_GET['pprec'])) {
		$_SESSION['per_page_rec'] = (int) $_GET['pprec'];
	}
	if (empty($_SESSION['per_page_rec'])) {
		$_SESSION['per_page_rec'] = ($view == 'gallery') ? (int) $config_index['search_gallery_numpage'] : (int) $config_index['search_numpage'];
	}
	
	if ($debug) echo '$_SESSION[\'per_page_rec\']='.$_SESSION['per_page_rec'].'<br>';
	
	// add to club & events
	$id_club = isset($_REQUEST['id_club']) ? (int) $_REQUEST['id_club'] : 0;
	$id_event = isset($_REQUEST['id_event']) ? (int) $_REQUEST['id_event'] : 0;
	
	// check if we can use id array in session
	$use_session = 0;
	if (!empty($_SESSION['id_arr']) && is_array($_SESSION['id_arr'])) {
		if (isset($_GET['page']) && $_GET['page'] > 0) {
			$use_session = 1;
		}
		if (isset($_GET['pprec']) && $_GET['pprec'] > 0) {
			$use_session = 1;
		}
		if ($par == 'back' || $par == 'send') {
			$use_session = 1;
		}
	}
	
	if ($debug) echo '$use_session='.$use_session.'<br>';
	
	/*
	if ($par == 'back' || $par == 'send') {
		// restore old sel
		$sel = $_SESSION['sel'];
	} else {
		// store sel in session
		$_SESSION['sel'] = $sel;
	}
	*/
	
	if ($debug) echo '$sel='.$sel.'<br>';
	
	$where_clause = '';
	
	if (MM_ENABLE_FRIENDS_FRIENDLIST && $id_user != $user[ AUTH_ID_USER ]) {
		$where_clause .= ' AND u.visible = "1"';
	}
	
	// get fresh search results
	if ($use_session == 0)
	{
		$_SESSION['id_arr']			= array();
		$_SESSION['with_arr']		= array();
		$_SESSION['without_arr']	= array();
		$_SESSION['online_arr']		= array();
		$_SESSION['offline_arr']	= array();
		
		if ($sel == 'inbox')
		{
			$strSQL =
				'SELECT u.id, u.icon_path, s.id_user AS session, up.hide_online
				   FROM '.USERS_TABLE.' u
			 INNER JOIN '.CONNECTIONS_TABLE.' l ON l.id_user = u.id
			  LEFT JOIN '.ACTIVE_SESSIONS_TABLE.' s ON s.id_user = u.id
			  LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id
			 INNER JOIN '.USER_GROUP_TABLE.' ug ON u.id = ug.id_user
			 INNER JOIN '.GROUPS_TABLE.' g ON ug.id_group = g.id
				  WHERE l.id_friend = ? AND l.status = "0"
					AND u.status = "1" '.$where_clause.'
			   GROUP BY u.id
			   ORDER BY g.sort ASC, u.mm_platinum_applied DESC, u.id DESC';
			$paramsSQL = array($id_user);
			$smarty->assign('type', '1');
		}
		elseif ($sel == 'outbox')
		{
			$strSQL =
				'SELECT u.id, u.icon_path, s.id_user AS session, up.hide_online, g.sort, u.mm_platinum_applied
				   FROM '.USERS_TABLE.' u
			 INNER JOIN '.CONNECTIONS_TABLE.' l ON l.id_friend = u.id
			  LEFT JOIN '.ACTIVE_SESSIONS_TABLE.' s ON s.id_user = u.id
			  LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id
			 INNER JOIN '.USER_GROUP_TABLE.' ug ON u.id = ug.id_user
			 INNER JOIN '.GROUPS_TABLE.' g ON ug.id_group = g.id
				  WHERE l.id_user = ? AND l.status = "0"
					AND u.status = "1" '.$where_clause.'
			   GROUP BY u.id
			   ORDER BY g.sort ASC, u.mm_platinum_applied DESC, u.id DESC';
			$paramsSQL = array($id_user);
			$smarty->assign('type', '2');
		}
		else
		{
			$strSQL =
				'SELECT u.id, u.icon_path, s.id_user AS session, up.hide_online,
						g.sort, u.mm_platinum_applied
				   FROM '.USERS_TABLE.' u
			 INNER JOIN '.CONNECTIONS_TABLE.' l ON l.id_user = u.id
			  LEFT JOIN '.ACTIVE_SESSIONS_TABLE.' s ON s.id_user = u.id
			  LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id
			 INNER JOIN '.USER_GROUP_TABLE.' ug ON u.id = ug.id_user
			 INNER JOIN '.GROUPS_TABLE.' g ON ug.id_group = g.id
				  WHERE l.id_friend = ? AND l.status = "1"
					AND u.status = "1" '.$where_clause.'
				  GROUP BY u.id
				  UNION
				 SELECT u.id, u.icon_path, s.id_user AS session, up.hide_online,
						g.sort, u.mm_platinum_applied
				   FROM '.USERS_TABLE.' u
			 INNER JOIN '.CONNECTIONS_TABLE.' l ON l.id_friend = u.id
			  LEFT JOIN '.ACTIVE_SESSIONS_TABLE.' s ON s.id_user = u.id
			  LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id
			 INNER JOIN '.USER_GROUP_TABLE.' ug ON u.id = ug.id_user
			 INNER JOIN '.GROUPS_TABLE.' g ON ug.id_group = g.id
				  WHERE l.id_user = ? AND l.status = "1"
					AND u.status = "1" '.$where_clause.'
			   GROUP BY u.id
			   ORDER BY sort ASC, mm_platinum_applied DESC, id DESC';
			$paramsSQL = array($id_user, $id_user);
		}
		
		if ($debug) echo '$strSQL='.$strSQL.'<br>'.print_r($paramsSQL, true).'<br>';
		
		$rs = $dbconn->Execute($strSQL, $paramsSQL);
		
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			
			if ($debug) echo '<hr>'.print_r($row, true);
			
			$_SESSION['id_arr'][] = $row['id'];
			if (strlen($row['icon_path'])) {
				$_SESSION['with_arr'][] = $row['id'];
			} else {
				$_SESSION['without_arr'][] = $row['id'];
			}
			// RS: ignore hide_online when users are connected or are having a pending invitation
			## if (intval($row['session']) && !$row['hide_online']) {
			if (intval($row['session'])) {
				$_SESSION['online_arr'][] = $row['id'];
			} else {
				$_SESSION['offline_arr'][] = $row['id'];
			}
			$rs->MoveNext();
		}
	}
	
	if ($debug) echo '<hr>';
	
	// apply filter
	switch ($filter) {
		case 'all':
			$id_arr = isset($_SESSION['id_arr']) ? $_SESSION['id_arr'] : array();
		break;
		case 'photo':
			$id_arr = isset($_SESSION['with_arr']) ? $_SESSION['with_arr'] : array();
		break;
		case 'online':
			$id_arr = isset($_SESSION['online_arr']) ? $_SESSION['online_arr'] : array();
		break;
		default:
			$id_arr = isset($_SESSION['id_arr']) ? $_SESSION['id_arr'] : array();
			$filter = 'all';
		break;
	}
	
	if ($debug) echo 'before limit: $id_arr='.print_r($id_arr, true).'<br>';
	
	$num_records = count($id_arr);
	
	if ($debug) echo '$num_records='.$num_records.'<br>';
	
	if ($num_records > 0)
	{
		$limit_offset = ($_SESSION['search_page'] - 1) * $_SESSION['per_page_rec'];
		$limit_length = $_SESSION['per_page_rec'];
		
		if ($sel == 'inbox')
		{
			$strSQL =
				'SELECT u.id, u.fname, u.phone, SUBSTRING(u.comment, 1, 165) AS comment, u.gender,
						u.date_birthday, u.id_country, u.id_city, u.id_region, u.icon_path, u.platinum_verified, u.mm_platinum_applied,
						DATE_FORMAT(u.date_last_seen, "'.$config['date_format'].'") AS date_last_login,
						x.gender AS gender_search, x.age_max, x.age_min, up.hide_online
				   FROM '.USERS_TABLE.' u
			  LEFT JOIN '.USER_MATCH_TABLE.' x ON x.id_user = u.id
			  LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id
			 INNER JOIN '.USER_GROUP_TABLE.' ug ON u.id = ug.id_user
			 INNER JOIN '.GROUPS_TABLE.' g ON ug.id_group = g.id
				  WHERE u.id IN (' . implode(',', $id_arr) . ')
			   GROUP BY u.id
			   ORDER BY g.sort ASC, u.mm_platinum_applied DESC, u.id DESC
				  LIMIT '.$limit_offset.', '.$limit_length;
			$smarty->assign('type', '1');
		}
		elseif ($sel == 'outbox')
		{
			$strSQL =
				'SELECT u.id, u.fname, u.phone, SUBSTRING(u.comment, 1, 165) AS comment, u.gender,
						u.date_birthday, u.id_country, u.id_city, u.id_region, u.icon_path, u.platinum_verified, u.mm_platinum_applied,
						DATE_FORMAT(u.date_last_seen, "'.$config['date_format'].'") AS date_last_login,
						x.gender AS gender_search, x.age_max, x.age_min, up.hide_online, g.sort
				   FROM '.USERS_TABLE.' u
			  LEFT JOIN '.USER_MATCH_TABLE.' x ON x.id_user = u.id
			  LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id
			 INNER JOIN '.USER_GROUP_TABLE.' ug ON u.id = ug.id_user
			 INNER JOIN '.GROUPS_TABLE.' g ON ug.id_group = g.id
				  WHERE u.id IN (' . implode(',', $id_arr) . ')
			   GROUP BY u.id
			   ORDER BY g.sort ASC, u.mm_platinum_applied DESC, u.id DESC
				  LIMIT '.$limit_offset.', '.$limit_length;
			$smarty->assign('type', '2');
		}
		else
		{
			// order by fields need to be listed in SELECT clause !
			$strSQL =
				'SELECT u.id, u.fname, u.phone, SUBSTRING(u.comment, 1, 165) AS comment, u.gender,
						u.date_birthday, u.id_country, u.id_city, u.id_region, u.icon_path, u.platinum_verified, u.mm_platinum_applied,
						DATE_FORMAT(u.date_last_seen, "'.$config['date_format'].'") AS date_last_login,
						x.gender AS gender_search, x.age_max, x.age_min, up.hide_online, g.sort
				   FROM '.USERS_TABLE.' u
			  LEFT JOIN '.USER_MATCH_TABLE.' x ON x.id_user = u.id
			  LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id
			 INNER JOIN '.USER_GROUP_TABLE.' ug ON u.id = ug.id_user
			 INNER JOIN '.GROUPS_TABLE.' g ON ug.id_group = g.id
				  WHERE u.id IN (' . implode(',', $id_arr) . ')
			   GROUP BY u.id
						UNION
				 SELECT u.id, u.fname, u.phone, SUBSTRING(u.comment, 1, 165) AS comment, u.gender,
						u.date_birthday, u.id_country, u.id_city, u.id_region, u.icon_path, u.platinum_verified, u.mm_platinum_applied,
						DATE_FORMAT(u.date_last_seen, "'.$config['date_format'].'") AS date_last_login,
						x.gender AS gender_search, x.age_max, x.age_min, up.hide_online, g.sort
				   FROM '.USERS_TABLE.' u
			  LEFT JOIN '.USER_MATCH_TABLE.' x ON x.id_user = u.id
			  LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id
			 INNER JOIN '.USER_GROUP_TABLE.' ug ON u.id = ug.id_user
			 INNER JOIN '.GROUPS_TABLE.' g ON ug.id_group = g.id
				  WHERE u.id IN (' . implode(',', $id_arr) . ')
			   GROUP BY u.id
			   ORDER BY sort ASC, mm_platinum_applied DESC, id DESC
				  LIMIT '.$limit_offset.', '.$limit_length;
		}
		
		$rs = $dbconn->Execute($strSQL);
		
		$search = array();
		$_LANG_NEED_ID = array();
		$i = 0;
		
		if ($sel == 'inbox') {
			$search_type = 'ci';
		} elseif ($sel == 'outbox') {
			$search_type = 'co';
		} else {
			$search_type = 'c';
		}
		
		while (!$rs->EOF)
		{
			$row = $rs->GetRowAssoc(false);
			
			if ($debug) echo '<hr>'.print_r($row, true);
			
			$search[$i]['id']			= (int) $row['id'];
			$search[$i]['number']		= ($_SESSION['search_page'] - 1) * $_SESSION['per_page_rec'] + $i + 1;
			$search[$i]['name']			= stripslashes($row['fname']);
			$search[$i]['gender']		= (int) $row['gender'];
#			$search[$i]['phone']		= stripslashes($row['phone']);
			$search[$i]['annonce']		= stripslashes($row['comment']);
			$search[$i]['age']			= AgeFromBDate($row['date_birthday']);
			$search[$i]['id_country']	= (int) $row['id_country'];
			$search[$i]['id_region']	= (int) $row['id_region'];
			$search[$i]['id_city']		= (int) $row['id_city'];
#			$search[$i]['completion']	= $percent->GetAllPercentForUser($row['id']);
			$search[$i]['last_login']	= $row['date_last_login'];
			$search[$i]['gender_search']= $lang['gender_search'][$row['gender_search']];
			$search[$i]['age_max']		= (int) $row['age_max'];
			$search[$i]['age_min']		= (int) $row['age_min'];
			if ($settings['use_friend_types']) {
				$search[$i]['friend_type']	= stripslashes($row['friend_type']);
			}
			
			//SH is_verified
			$search[$i]['is_verified'] = !empty($row['platinum_verified']);
			
			// get groups
			$search[$i]['group'] = getUserGroups($row['id'], $row['mm_platinum_applied']);
			
			// get online status
			// RS: ignore hide_online when users are connected or are having a pending invitation
			## $search[$i]['status'] = (getUserIsOnline($row['id']) && !$row['hide_online']) ? $lang['status']['on'] : $lang['status']['off'];
			$search[$i]['status'] = getUserIsOnline($row['id']) ? $lang['status']['on'] : $lang['status']['off'];
			
			// language
			$_LANG_NEED_ID['country'][]	= (int) $row['id_country'];
			$_LANG_NEED_ID['region'][]	= (int) $row['id_region'];
			$_LANG_NEED_ID['city'][]	= (int) $row['id_city'];
			
			if ($row['id'] == $user[ AUTH_ID_USER ])
			{
				$search[$i]['profile_link'] = 'myprofile.php';
			}
			else
			{
				// check hotlist
				$search[$i]['hotlisted'] = isInMyHotlist($user[ AUTH_ID_USER ], $row['id']);
				
				// check blacklist
				if (MM_ENABLE_FRIENDS_FRIENDLIST && $id_user != $user[ AUTH_ID_USER ]) {
					$search[$i]['blacklisted'] = isInMyBlacklist($user[ AUTH_ID_USER ], $row['id']);
				} else {
					$search[$i]['blacklisted'] = 0;
				}
				
				// check connected
				$search[$i]['connected_status'] = getConnectedStatus($id_user, $row['id']);
				
				// links
				$search[$i]['profile_link']			= 'viewprofile.php?id='.$row['id'].'&amp;search_type='.$search_type;
				$search[$i]['email_link']			= 'mailbox.php?sel=fs&amp;id='.$row['id'].'&amp;search_type='.$search_type;
				$search[$i]['sendfriend_link']		= 'send_friend.php?sel=send&amp;id_user='.$row['id'];
				$search[$i]['gift_link']			= 'giftshop.php?sel=users_add&amp;id_user='.$row['id'];
				$search[$i]['ecard_link']			= 'ecards.php?id_user_to='.$row['id'].'&amp;fixuser=Y';
				$search[$i]['del_connection_link']	= 'connections.php?sel=del&amp;par='.$sel.'&amp;id='.$row['id'];
				$search[$i]['accept_link']			= 'connections.php?sel=accept&amp;id='.$row['id'];
				
				// kiss link
				if ($settings['use_kiss_types']) {
					$search[$i]['kiss_link'] = 'send_kiss.php?sel=send&amp;id_user='.$row['id'].'&amp;par='.$sel;
				} else {
					$search[$i]['kiss_link'] = $file_name.'?sel=kiss&amp;id='.$row['id'].'&amp;par='.$sel;
				}
				
				if (MM_ENABLE_FRIENDS_FRIENDLIST) {
					// look at connections's connected list
					$search[$i]['connection_link'] = $file_name.'?id_user='.$row['id'];
				}
				
				// invite to club
				if ($id_club > 0) {
					$search[$i]['invite_club_link'] = 'club.php?sel=invite&amp;id_club='.$id_club.'&amp;id_user='.$row['id'];
					$rs_invited = $dbconn->Execute('SELECT id FROM '.CLUB_USERS_TABLE.' WHERE id_user = ? AND id_club = ?', array($row['id'], $id_club));
					$search[$i]['in_club'] = !empty($rs_invited->fields[0]) ? 1 : 0;
				}
				
				// invite to event
				if ($id_event > 0) {
					$search[$i]['invite_event_link'] = 'events.php?sel=invite&amp;id_event='.$id_event.'&amp;id_user='.$row['id'];
					$rs_invited = $dbconn->Execute('SELECT 1 FROM '.EVENT_USERS_TABLE.' WHERE id_user = ? AND id_event = ?', array($row['id'], $id_event));
					$search[$i]['in_event'] = !empty($rs_invited->fields[0]) ? 1 : 0;
				}
				
				// hotlist link
				if ($search[$i]['hotlisted'] == 0 && $search[$i]['blacklisted'] == 0) {
					if ($settings['use_friend_types']) {
						$search[$i]['add_hotlist_link'] = 'hotlist.php?sel=addform&amp;id='.$row['id'].'&amp;par='.$sel;
					} else {
						$search[$i]['add_hotlist_link'] = $file_name.'?sel=addhotlist&amp;id='.$row['id'].'&amp;par='.$sel;
					}
				}
				
				// blacklist link
				if (MM_ENABLE_FRIENDS_FRIENDLIST && $id_user != $user[ AUTH_ID_USER ]) {
					if ($search[$i]['hotlisted'] == 0 && $search[$i]['connected_status'] != CS_CONNECTED && $search[$i]['blacklisted'] == 0) {
						$search[$i]['add_blacklist_link'] = $file_name.'?sel=addblacklist&amp;id='.$row['id'].'&amp;par='.$sel;
					}
				}
				
				// connections link
				if (MM_ENABLE_FRIENDS_FRIENDLIST && $id_user != $user[ AUTH_ID_USER ]) {
					if ($search[$i]['blacklisted'] == 0 && $search[$i]['connected_status'] == CS_NOTHING) {
						if ($settings['use_friend_types']) {
							$search[$i]['add_connection_link'] = 'connections.php?sel=addform&amp;id='.$row['id'].'&amp;par='.$sel;
						} else {
							$search[$i]['add_connection_link'] = $file_name.'?sel=addlist&amp;id='.$row['id'].'&amp;par='.$sel;
						}
					}
				}
			}
			
			// icon path
			$icon_path = $row['icon_path'] ? $row['icon_path'] : $default_photos[$row['gender']];
			
			if ($icon_path && file_exists($config['site_path'].$settings['icons_folder'].'/'.$icon_path)) {
				$search[$i]['icon_path'] = $config['site_root'].$settings['icons_folder'].'/'.$icon_path;
				//SH big icon image fetched
				$search[$i]['big_icon_path'] = $config['site_root'].$settings['icons_folder'].'/big_'.$icon_path;
			}
			
			// photo count
			$search[$i]['photo_count'] = publicPhotoCountAlbums($row['id']);
			if ($row['icon_path']) {
				$search[$i]['photo_count']++;
			}
			
			// voip link
			if ($config['voipcall_feature']) {
				$search[$i]['call_link'] = 'voip_call.php?sel=rate&amp;id_user='.$row['id']; 
			}
			
			$rs->MoveNext();
			$i++;
		}
		
		$smarty->assign('search_res', $search);
		
		// page count
		$form['pages_count'] = ceil($num_records / $_SESSION['per_page_rec']);
		
		// paging links
		if ($id_club > 0) {
			$param = $file_name.'?sel=invite&amp;id_club='.$id_club.'&amp;';
		} else {
			$param = $file_name.'?';
			if ($sel) {
				$param .= 'sel='.$sel.'&amp;';
			}
			$param .= 'filter='.$filter.'&amp;view='.$view.'&amp;';
		}
		// it's not allowed to look at a connection's connection list
		if (MM_ENABLE_FRIENDS_FRIENDLIST && $id_user != $user[ AUTH_ID_USER ]) {
			$param .= 'id_user='.$id_user.'&amp;';
		}
		$smarty->assign('links', GetLinkArray($num_records, $_SESSION['search_page'], $param, $_SESSION['per_page_rec']));
		
		// records per page links
		$smarty->assign('rpp_links', GetRPPageLinkArray($_SESSION['per_page_rec'], $param));
	}
	else
	{
		$smarty->assign('empty', '1');
	}
	
	if ($debug) echo '</font>';
	
	$form['err']						= $err;
	$form['sel']						= $sel;
	$form['user']						= $user[ AUTH_ID_USER ];
	$form['guest_user']					= $user[ AUTH_GUEST ];
	
	$form['online_count']				= isset($_SESSION['online_arr']) ? count($_SESSION['online_arr']) : 0;
	$form['offline_count']				= isset($_SESSION['offline_arr']) ? count($_SESSION['offline_arr']) : 0;
	$form['with_count']					= isset($_SESSION['with_arr']) ? count($_SESSION['with_arr']) : 0;
	$form['without_count']				= isset($_SESSION['without_arr']) ? count($_SESSION['without_arr']) : 0;
	
	$form['view_online_link']			= $file_name.'?sel='.$sel.'&amp;page=1&amp;filter=online&amp;view='.$view;
	$form['view_photo_link']			= $file_name.'?sel='.$sel.'&amp;page=1&amp;filter=photo&amp;view='.$view;
	$form['view_all_link']				= $file_name.'?sel='.$sel.'&amp;page=1&amp;filter=all&amp;view='.$view;
	$form['view_gallery_link']			= $file_name.'?sel='.$sel.'&amp;page=1&amp;filter='.$filter.'&amp;view=gallery';
	$form['view_list_link']				= $file_name.'?sel='.$sel.'&amp;page=1&amp;filter='.$filter.'&amp;view=list';
	
	// confirmed connections count
	$count1 = $dbconn->getOne(
		'SELECT COUNT(*)
		   FROM '.CONNECTIONS_TABLE.' a
	 INNER JOIN '.USERS_TABLE.' b ON b.id = a.id_friend
		  WHERE a.id_user = ? AND a.status = "1" AND b.status = "1"' . $where_clause,
		  array($id_user));
	
	$count2 = $dbconn->getOne(
		'SELECT COUNT(*)
		   FROM '.CONNECTIONS_TABLE.' a
	 INNER JOIN '.USERS_TABLE.' b ON b.id = a.id_user
		  WHERE a.id_friend = ? AND a.status = "1" AND b.status = "1"' . $where_clause,
		  array($id_user));
	
	$form['confirmed_all'] = $count1 + $count2;
	
	// incoming invites
	$form['inbox_all'] = $dbconn->getOne(
		'SELECT COUNT(*)
		   FROM '.CONNECTIONS_TABLE.' a
	 INNER JOIN '.USERS_TABLE.' b ON b.id = a.id_user
		  WHERE a.id_friend = ? AND a.status = "0" AND b.status = "1"' . $where_clause,
		  array($id_user));
	
	// outgoing invites
	$form['outbox_all'] = $dbconn->getOne(
		'SELECT COUNT(*)
		   FROM '.CONNECTIONS_TABLE.' a
	 INNER JOIN '.USERS_TABLE.' b ON b.id = a.id_friend
		  WHERE a.id_user = ? AND a.status = "0" AND b.status = "1"' . $where_clause,
		  array($id_user));
	
	$form['show_users_connection_str']	= $settings['show_users_connection_str'];
	$form['show_users_comments']		= $settings['show_users_comments'];
	$form['show_users_group_str']		= $settings['show_users_group_str'];
	$form['use_kiss_types']				= $settings['use_kiss_types'];
	$form['use_friend_types']			= $settings['use_friend_types'];
	$form['use_pilot_module_giftshop']	= $settings['use_pilot_module_giftshop'];
	
	$form['filter']						= $filter;
	$form['view']						= $view;
	
	$smarty->assign('form', $form);
	$smarty->assign('user_gender', $user[ AUTH_GENDER ]);
	
	if (!empty($_LANG_NEED_ID)) {
		$smarty->assign('base_lang', GetBaseLang($_LANG_NEED_ID));
	}
	
	$smarty->assign('section', $lang['subsection']);
	$smarty->assign('header', $lang['relations']);
	$smarty->display(TrimSlash($config['index_theme_path']).'/connections_table.tpl');
	exit;
}


function AcceptConnection()
{
	global $lang, $dbconn, $user;
	
	$debug = false;
	
	$invitor_id_user = isset($_GET['id']) ? (int) $_GET['id'] : 0;
	
	$id_user	= $user[ AUTH_ID_USER ];
	$id_group	= $user[ AUTH_ID_GROUP ];
	
	// check permissions
	if (empty($_SESSION['permissions']['connection_accept'])) {
		if ($user[ AUTH_IS_REGULAR_INACTIVE ]) {
			SearchTable($lang['err']['connection_permission_denied_regular_inactive'], 'inbox');
			exit;
		} else {
			SearchTable($lang['err']['accept_connections_permission_denied'], 'inbox');
			exit;
		}
	}
	
	if (MM_CHECK_CONNECTION_LIMIT)
	{
		$limit = $dbconn->GetOne(
			'SELECT permission_count
			   FROM '.GROUPS_PERMISSIONS_TABLE.'
			  WHERE id_group = ? AND id_permission = 8',
			array($id_group));
		
		if ($limit == null || $limit <= 0) {
			if ($user[ AUTH_IS_REGULAR_INACTIVE ]) {
				SearchTable($lang['err']['connection_permission_denied_regular_inactive'], 'inbox');
				exit;
			} else {
				SearchTable($lang['err']['accept_connections_permission_denied'], 'inbox');
				exit;
			}
		}
		
		if ($debug) echo 'limit='.$limit.'<br/>';
		
		// get period id and period begin
		$rs = $dbconn->Execute('SELECT UNIX_TIMESTAMP(date_begin) FROM '.BILLING_USER_PERIOD_TABLE.' WHERE id_user = ?', array($id_user));
		$start_timestamp = (int) $rs->fields[0];
		if ($debug) echo 'membership period start='.strftime('%Y-%m-%d %H:%M:%S', $start_timestamp).'<br/>';
		unset($rs);
		
		// calculate the seconds per month
		$period_seconds = 30*24*60*60;
		
		// calculate start and end timestamps
		while ($start_timestamp < time() - $period_seconds) {
			$start_timestamp += $period_seconds;
			if ($debug) echo 'new period start='.strftime('%Y-%m-%d %H:%M:%S', $start_timestamp).'<br/>';
		}
		
		if ($debug) echo 'final period start='.strftime('%Y-%m-%d %H:%M:%S', $start_timestamp).'<br/>';
		
		$end_timestamp = $start_timestamp + $period_seconds;
		
		if ($debug) echo 'final period end='.strftime('%Y-%m-%d %H:%M:%S', $end_timestamp).'<br/>';
		
		// get number of connection which have been accepted between $start_timestamp and $end_timestamp
		$count = $dbconn->GetOne(
			'SELECT COUNT(*)
			   FROM '.CONNECTIONS_TABLE.'
			  WHERE (id_user = ? OR id_friend = ?) AND status = "1"
				AND UNIX_TIMESTAMP(datetime_accepted) BETWEEN '.$start_timestamp.' AND '.$end_timestamp,
			array($id_user, $id_user));
		
		if ($debug) echo 'count='.$count.'<br/>';
		
		if ($count >= $limit)
		{
			$isTrialUser = ($id_group == MM_TRIAL_GUY_ID || $id_group == MM_TRIAL_LADY_ID);
			
			if ($isTrialUser)
			{
				//counting total number accepted connections
				$count_total = $dbconn->GetOne(
					'SELECT COUNT(*) FROM '.CONNECTIONS_TABLE.' WHERE (id_user = ? OR id_friend = ?) AND status = "1"',
					array($id_user, $id_user));
				
				if ($count_total >= MM_MAX_CONNECTION_LIMIT_FOR_TRIAL) {
					$error = $lang['err']['total_accept_connections_count_exceeded'];
					$error = str_replace('#MAX#', MM_MAX_CONNECTION_LIMIT_FOR_TRIAL, $error);
					SearchTable($error, 'inbox');
					exit;
				}
			}
			
			$error = $lang['err']['accept_connections_count_exceeded'];
			$error = str_replace('#MAX#', $limit, $error);
			$error = str_replace('#END#', strftime('%m/%d/%Y %H:%M', $end_timestamp), $error);
			SearchTable($error, 'inbox');
			exit;
		}
	}
	
	if ($invitor_id_user && $id_user)
	{
		$freeConnectionsArr = array(
			MM_PLATINUM_LADY_ID,
			MM_PLATINUM_LADY_FIRST_INS_ID,
			MM_PLATINUM_LADY_SECOND_INS_ID
		);

		// check current user
		$credit_points = GetCreditPoints($id_user);
		$account_days  = GetUserAccountDays($id_user);
		
		$ok = false;
		$deduct_points = false;

		if (in_array($id_group, $freeConnectionsArr)) {
			// platinum ladies get free connections
			$ok = true;
		} elseif ($id_group == MM_REGULAR_LADY_ID) {
			// regular ladies can only connect when they have days or points
			if ($account_days > 0) {
				$ok = true;
			} elseif ($credit_points >= POINT_USER_CONNECTION_INVITE) {
				$ok = true;
				$deduct_points = true;
			}
		} else {
			// trial ladies and all guys can only connect when they have points
			if ($credit_points >= POINT_USER_CONNECTION_INVITE) {
				$ok = true;
				$deduct_points = true;
			}
		}

		if (!$ok) {
			if ($user[ AUTH_GENDER ] == GENDER_MALE) {
				$return_param['err'] = $lang['err']['connection_accept_insufficient_points_guy'];
			} else {
				$return_param['err'] = $lang['err']['connection_accept_insufficient_points_lady'];
			}
			return $return_param;
		}
		
		// check invitor
		$invitor_id_group = $dbconn->getOne(
			'SELECT id_group FROM '.USER_GROUP_TABLE.' WHERE id_user = ?',
			array($invitor_id_user));
		
		$invitor_credit_points = GetCreditPoints($invitor_id_user);
		$invitor_account_days  = GetUserAccountDays($invitor_id_user);
		
		$ok = false;
		$invitor_deduct_points = false;

		if (in_array($invitor_id_group, $freeConnectionsArr)) {
			// platinum ladies get free connections
			$ok = true;
		} elseif ($invitor_id_group == MM_REGULAR_LADY_ID) {
			// regular ladies can only connect when they have days or points
			if ($invitor_account_days > 0) {
				$ok = true;
			} elseif ($invitor_credit_points >= POINT_USER_CONNECTION_INVITE) {
				$ok = true;
				$invitor_deduct_points = true;
			}
		} else {
			// trial ladies and all guys can only connect when they have points
			if ($invitor_credit_points >= POINT_USER_CONNECTION_INVITE) {
				$ok = true;
				$invitor_deduct_points = true;
			}
		}

		if (!$ok) {
			// send notification mail to invitor
			SendNotification($invitor_id_user, $id_user, 'WANT_To_ACCEPT'); // to, from, type
			
			$return_param['err'] = $lang['err']['connection_accept_insufficient_points_invitor'];
			return $return_param;
		}
		
		// all checks passed
		$dbconn->Execute(
			'UPDATE '.CONNECTIONS_TABLE.'
				SET status = "1", datetime_accepted = NOW()
			  WHERE id_user = ? AND id_friend = ?',
			array($invitor_id_user, $id_user));

		// user having insufficient days in account => deduct credit points
		if ($deduct_points) {
			DeductCreditPoints($id_user, POINT_USER_CONNECTION_ACCEPT, 'con_accept');
		}

		// invitor having insufficient days in account => deduct credit points
		if ($invitor_deduct_points) {
			DeductCreditPoints($invitor_id_user, POINT_USER_CONNECTION_INVITE, 'con_invite');
		}

		// send notification on users reg email
		SendNotification($invitor_id_user, $id_user, 'ACCEPTED'); // to, from, type

		$return_param['err'] = $lang['err']['user_was_accepted_connections'];
		return $return_param;
	}
	
	$return_param['err'] = '';
	return $return_param;
}


//VP function to check : is connection added in current month
function isConnectionRecentlyAdded($del_id)
{
	global $dbconn, $user;
	
	$debug = false;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	// get period id and period begin
	$rs = $dbconn->Execute('SELECT UNIX_TIMESTAMP(date_begin) FROM '.BILLING_USER_PERIOD_TABLE.' WHERE id_user = ?', array($id_user));
	$start_timestamp = (int) $rs->fields[0];
	
	if ($debug) echo 'membership period start='.strftime('%Y-%m-%d %H:%M:%S', $start_timestamp).'<br/>';
	
	unset($rs);
	
	// calculate the seconds per month
	$period_seconds = 30*24*60*60;
	
	// calculate start and end timestamps
	while ($start_timestamp < time() - $period_seconds) {
		$start_timestamp += $period_seconds;
		if ($debug) echo 'new period start='.strftime('%Y-%m-%d %H:%M:%S', $start_timestamp).'<br/>';
	}
	
	if ($debug) echo 'final period start='.strftime('%Y-%m-%d %H:%M:%S', $start_timestamp).'<br/>';
	
	$end_timestamp = $start_timestamp + $period_seconds;
	
	if ($debug) echo 'final period end='.strftime('%Y-%m-%d %H:%M:%S', $end_timestamp).'<br/>';
	
	// is user accepted between $start_timestamp and $end_timestamp
	$check = $dbconn->GetOne(
		'SELECT id
		   FROM '.CONNECTIONS_TABLE.'
		  WHERE id_user = ? AND id_friend = ? AND status = "1"
			AND UNIX_TIMESTAMP(datetime_accepted) BETWEEN '.$start_timestamp.' AND '.$end_timestamp,
				array($del_id, $id_user));
	
	if ($debug) echo 'check='.$check.'<br/>';
	
	return !empty($check);
}
			
function DeleteFromList($par)
{
	global $lang, $dbconn, $user;
	
	$delete_id	= isset($_GET['id']) ? (int) $_GET['id'] : 0;
	
	$id_user	= $user[ AUTH_ID_USER ];
	
	if ($delete_id && $id_user && $par != 'outbox')
	{
		$dbconn->Execute(
			'DELETE FROM '.CONNECTIONS_TABLE.' WHERE (id_user = ? AND id_friend = ?) OR (id_user = ? AND id_friend = ?)',
			array($id_user, $delete_id, $delete_id, $id_user));

		switch ($par) {
			case 'inbox': $msg = $lang['err']['user_was_deleted_connections_inbox']; break;
			case 'outbox': $msg = ''; break; // it's not allowed to withdraw invites
			case '' : $msg = $lang['err']['user_was_deleted_connections']; break;
			default: $msg = '';
		}
	}
	
	#SearchTable('', $par);
	#exit;
	return $msg;
}


function AddToConnectionsForm($err = '')
{
	global $lang, $config, $smarty, $dbconn;
	
	$id_user = isset($_GET['id']) ? (int) $_GET['id'] : 0;
	
	IndexHomePage();
	
	// multi-language tables
	$multi_lang = new MultiLang();
	
	// get types
	$strSQL =
		'SELECT DISTINCT a.id, b.'.$multi_lang->DefaultFieldName().' AS name, a.sorter
		   FROM '.HOTLIST_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.id_reference = a.id AND b.table_key = "'.$multi_lang->TableKey(HOTLIST_SPR_TABLE).'"
	   ORDER BY a.sorter';
	
	$rs = $dbconn->Execute($strSQL);
	
	if ($rs->RowCount()) {
		$types = array();
		$i = 0;
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$types[$i]['id'] = $row['id'];
			$types[$i]['name'] = stripslashes($row['name']);
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign('types', $types);
	}
	
	$form['err'] = $err;
	$form['id_user'] = $id_user;
	
	$smarty->assign('form', $form);
	$smarty->assign('header', $lang['add_to_connections']);
	
	$smarty->display(TrimSlash($config['index_theme_path']).'/connections_form.tpl');
	exit;
}
?>