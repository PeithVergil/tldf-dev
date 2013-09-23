<?php
/**
* User hotlist page (listing, deleting)
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

// dispatcher
switch ($sel) {
	case 'addform':
		AddToHotListForm();
	break;
	
	case 'addsave':
		$res = AddToHotList();
		AddToHotListForm($res['err']);
	break;
	
	case 'addhotlist':
		$res = AddToHotList();
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			header('Location: hotlist.php?par=send');
			exit;
		} else {
			$_GET['par'] = 'send';
			SearchTable($res['err']);
		}
	break;
	
	case 'addblacklist':
		$res = AddToBlackList();
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			header('Location: hotlist.php?par=send');
			exit;
		} else {
			$_GET['par'] = 'send';
			SearchTable($res['err']);
		}
	break;
	
	case 'addconnection':
		$res = AddToConnections();
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			header('Location: hotlist.php?par=send');
			exit;
		} else {
			$_GET['par'] = 'send';
			SearchTable($res['err']);
		}
	break;
	
	case 'kiss':
		$res = SendKiss();
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			header('Location: hotlist.php?par=send');
			exit;
		} else {
			$_GET['par'] = 'send';
			SearchTable($res['err']);
		}
	break;
	
	case 'del':
		$msg = DeleteFromList();
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $msg;
			header('Location: hotlist.php');
			exit;
		} else {
			SearchTable($msg);
		}
	break;
	
	default:
		SearchTable();
	break;
}

exit;

function SearchTable($err = '')
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;
	
	$debug = false;
	
	if ($debug) echo '<font color="red">';
	
	$id_user = $user[ AUTH_ID_USER ];
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$file_name = 'hotlist.php';
	
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
			// other user's list, check if he is in our hot list
			$rs = $dbconn->Execute(
				'SELECT u.login 
				   FROM '.USERS._TABLE.' u
			 INNER JOIN '.HOTLIST_TABLE.' l ON l.id_friend = u.id
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
	
	// get fresh search results
	if ($use_session == 0)
	{
		$_SESSION['id_arr']			= array();
		$_SESSION['with_arr']		= array();
		$_SESSION['without_arr']	= array();
		$_SESSION['online_arr']		= array();
		$_SESSION['offline_arr']	= array();
		
		$id_group = $dbconn->GetOne('SELECT id_group FROM '.USER_GROUP_TABLE.' WHERE id_user = ?', array($id_user));
		
		$privacy_where = $visible_where = '';
		
		if (HOTLIST_PRIVACY) {
			$privacy_where = sql_privacy_where($id_group);
		}
		
		if (HOTLIST_VISIBLE) {
			$visible_where = 'AND u.visible = "1"';
		}
		
		$strSQL =
			'SELECT u.id, u.icon_path, s.id_user AS session, up.hide_online
			   FROM '.USERS_TABLE.' u
		 INNER JOIN '.HOTLIST_TABLE.' l ON l.id_friend = u.id
		  LEFT JOIN '.ACTIVE_SESSIONS_TABLE.' s ON s.id_user = u.id
		  LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id
		 INNER JOIN '.USER_GROUP_TABLE.' ug ON u.id = ug.id_user
		 INNER JOIN '.GROUPS_TABLE.' g ON ug.id_group = g.id
			  WHERE l.id_user = ?
				AND u.status = "1" '.$visible_where.' '.$privacy_where.'
		   GROUP BY u.id
		   ORDER BY g.sort ASC, u.mm_platinum_applied DESC, u.date_registration DESC';
		
		$paramsSQL = array($id_user);
		
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
			if (intval($row['session']) && !$row['hide_online']) {
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
		
		if ($settings['use_friend_types']) {
			// multi-language tables
			$multi_lang = new MultiLang();
			
			$friend_type_field = ', c.'.$multi_lang->DefaultFieldName().' AS friend_type';
			$friend_type_join =
				'INNER JOIN '.HOTLIST_TABLE.' l ON l.id_friend = u.id AND l.id_user = "'.$id_user.'"
				  LEFT JOIN '.REFERENCE_LANG_TABLE.' c ON c.id_reference = l.friend_type AND c.table_key = "'.$multi_lang->TableKey(HOTLIST_SPR_TABLE).'"';
		} else {
			$friend_type_field = '';
			$friend_type_join = '';
		}
		
		$strSQL =
			'SELECT u.id, u.fname, u.phone, SUBSTRING(u.comment, 1, 165) AS comment, u.gender,
					u.date_birthday, u.id_country, u.id_city, u.id_region, u.icon_path, u.platinum_verified, u.mm_platinum_applied,
					DATE_FORMAT(u.date_last_seen, "'.$config['date_format'].'") AS date_last_login,
					x.gender AS gender_search, x.age_max, x.age_min, up.hide_online'.$friend_type_field.'
			   FROM '.USERS_TABLE.' u
		  LEFT JOIN '.USER_MATCH_TABLE.' x ON x.id_user = u.id
		  LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id
		 INNER JOIN '.USER_GROUP_TABLE.' ug ON u.id = ug.id_user
		 INNER JOIN '.GROUPS_TABLE.' g ON ug.id_group = g.id
					'.$friend_type_join.'
			  WHERE u.id IN (' . implode(',', $id_arr) . ')
		   GROUP BY u.id
		   ORDER BY g.sort ASC, u.mm_platinum_applied DESC, u.date_registration DESC
			  LIMIT '.$limit_offset.', '.$limit_length;
		
		if ($debug) echo '$strSQL='.$strSQL.'<br>';
		
		$rs = $dbconn->Execute($strSQL);
		
		$search = array();
		$_LANG_NEED_ID = array();
		$i = 0;
		$search_type = 'h';
		
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
			$search[$i]['status'] = (getUserIsOnline($row['id']) && !$row['hide_online']) ? $lang['status']['on'] : $lang['status']['off'];
			
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
				if (MM_ENABLE_FRIENDS_FRIENDLIST && $id_user != $user[ AUTH_ID_USER ]) {
					$search[$i]['hotlisted'] = isInMyHotlist($user[ AUTH_ID_USER ], $row['id']);
				} else {
					$search[$i]['hotlisted'] = 1;
				}
				
				// check blacklist
				if (MM_ENABLE_FRIENDS_FRIENDLIST && $id_user != $user[ AUTH_ID_USER ]) {
					$search[$i]['blacklisted'] = isInMyBlacklist($user[ AUTH_ID_USER ], $row['id']);
				} else {
					$search[$i]['blacklisted'] = 0;
				}
				
				// check connected
				$search[$i]['connected_status'] = getConnectedStatus($id_user, $row['id']);
				
				// links
				$search[$i]['profile_link']		= 'viewprofile.php?id='.$row['id'].'&amp;search_type='.$search_type;
				$search[$i]['email_link']		= 'mailbox.php?sel=fs&amp;id='.$row['id'].'&amp;search_type='.$search_type;
				$search[$i]['sendfriend_link']	= 'send_friend.php?sel=send&amp;id_user='.$row['id'];
				$search[$i]['gift_link']		= 'giftshop.php?sel=users_add&amp;id_user='.$row['id'];
				$search[$i]['ecard_link']		= 'ecards.php?id_user_to='.$row['id'].'&amp;fixuser=Y';
				$search[$i]['del_hotlist_link']	= 'hotlist.php?sel=del&amp;id='.$row['id'];
				
				// kiss link
				if ($settings['use_kiss_types']) {
					$search[$i]['kiss_link'] = 'send_kiss.php?sel=send&amp;id_user='.$row['id'];
				} else {
					$search[$i]['kiss_link'] = $file_name.'?sel=kiss&amp;id='.$row['id'];
				}
				
				if (MM_ENABLE_FRIENDS_FRIENDLIST) {
					// look at friend's hotlist
					$search[$i]['hotlist_link'] = $file_name.'?id_user='.$row['id'];
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
				if (MM_ENABLE_FRIENDS_FRIENDLIST && $id_user != $user[ AUTH_ID_USER ]) {
					if ($search[$i]['hotlisted'] == 0 && $search[$i]['blacklisted'] == 0) {
						if ($settings['use_friend_types']) {
							$search[$i]['add_hotlist_link'] = 'hotlist.php?sel=addform&amp;id='.$row['id'];
						} else {
							$search[$i]['add_hotlist_link'] = $file_name.'?sel=addhotlist&amp;id='.$row['id'];
						}
					}
				}
				
				// blacklist link
				if (MM_ENABLE_FRIENDS_FRIENDLIST && $id_user != $user[ AUTH_ID_USER ]) {
					if ($search[$i]['hotlisted'] == 0 && $search[$i]['connected_status'] != CS_CONNECTED && $search[$i]['blacklisted'] == 0) {
						$search[$i]['add_blacklist_link'] = $file_name.'?sel=addblacklist&amp;id='.$row['id'];
					}
				}
				
				// connections link
				if ($search[$i]['blacklisted'] == 0 && $search[$i]['connected_status'] == CS_NOTHING) {
					if ($settings['use_friend_types']) {
						$search[$i]['add_connection_link'] = 'connections.php?sel=addform&amp;id='.$row['id'];
					} else {
						$search[$i]['add_connection_link'] = $file_name.'?sel=addconnection&amp;id='.$row['id'];
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
			$param = $file_name.'?filter='.$filter.'&amp;view='.$view.'&amp;';
		}
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
	$form['user']						= $user[ AUTH_ID_USER ];
	$form['guest_user']					= $user[ AUTH_GUEST ];
	
	$form['online_count']				= isset($_SESSION['online_arr']) ? count($_SESSION['online_arr']) : 0;
	$form['offline_count']				= isset($_SESSION['offline_arr']) ? count($_SESSION['offline_arr']) : 0;
	$form['with_count']					= isset($_SESSION['with_arr']) ? count($_SESSION['with_arr']) : 0;
	$form['without_count']				= isset($_SESSION['without_arr']) ? count($_SESSION['without_arr']) : 0;
	
	$form['view_online_link']			= $file_name.'?page=1&amp;filter=online&amp;view='.$view;
	$form['view_photo_link']			= $file_name.'?page=1&amp;filter=photo&amp;view='.$view;
	$form['view_all_link']				= $file_name.'?page=1&amp;filter=all&amp;view='.$view;
	$form['view_gallery_link']			= $file_name.'?page=1&amp;filter='.$filter.'&amp;view=gallery';
	$form['view_list_link']				= $file_name.'?page=1&amp;filter='.$filter.'&amp;view=list';
	
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
	$smarty->display(TrimSlash($config['index_theme_path']).'/hotlist_table.tpl');
	exit;
}


function DeleteFromList()
{
	global $lang, $dbconn, $user;
	
	$delete_id = isset($_GET['id']) ? (int) $_GET['id'] : null;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$msg = '';
	
	if ($delete_id && $id_user) {
		$dbconn->Execute('DELETE FROM '.HOTLIST_TABLE.' WHERE id_user = ? AND id_friend = ?', array($id_user, $delete_id));
		$msg = $lang['err']['user_was_del_hotlist'];
	}
	
	return $msg;
}


function AddToHotListForm($err = '')
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
	$smarty->assign('header', $lang['add_to_hotlist']);
	
	$smarty->display(TrimSlash($config['index_theme_path']).'/hotlist_form.tpl');
	exit;
}
?>