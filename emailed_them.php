<?php
/**
* I Emailed them users listing
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
// (public access)

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
	case 'addhotlist':
		$res = AddToHotList();
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			header('Location: emailed_them.php?par=send');
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
			header('Location: emailed_them.php?par=send');
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
			header('Location: emailed_them.php?par=send');
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
			header('Location: emailed_them.php?par=send');
			exit;
		} else {
			$_GET['par'] = 'send';
			SearchTable($res['err']);
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
	
	$file_name = 'emailed_them.php';
	
	if (isset($_SESSION['err'])) {
		$err = $_SESSION['err'];
		unset($_SESSION['err']);
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
		
		if (EMAILED_THEM_PRIVACY) {
			$privacy_where = sql_privacy_where($id_group);
		}
		
		if (EMAILED_THEM_VISIBLE) {
			$visible_where = 'AND u.visible = "1"';
		}
		
		$strSQL =
			'SELECT u.id, u.icon_path, s.id_user AS session, up.hide_online
			   FROM '.USERS_TABLE.' u
		 INNER JOIN '.MAILBOX_TABLE.' l ON l.id_to = u.id
		  LEFT JOIN '.ACTIVE_SESSIONS_TABLE.' s ON s.id_user = u.id
		  LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id
			  WHERE l.id_from = ? AND l.deleted_from = "0"
				AND u.status = "1" AND u.root_user = "0" AND u.guest_user = "0" '.$visible_where.' '.$privacy_where.'
		   GROUP BY u.id
		   ORDER BY MAX(l.id) DESC';
		
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
		
		$strSQL =
			'SELECT u.id, u.fname, u.phone, SUBSTRING(u.comment, 1, 165) AS comment, u.gender,
					u.date_birthday, u.id_country, u.id_city, u.id_region, u.icon_path, u.platinum_verified, u.mm_platinum_applied,
					DATE_FORMAT(u.date_last_seen, "'.$config['date_format'].'") AS date_last_login,
					x.gender AS gender_search, x.age_max, x.age_min, up.hide_online
			   FROM '.USERS_TABLE.' u
		 INNER JOIN '.MAILBOX_TABLE.' l ON l.id_to = u.id AND l.id_from = ? AND l.deleted_to = "0"
		  LEFT JOIN '.USER_MATCH_TABLE.' x ON x.id_user = u.id
		  LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id
		 INNER JOIN '.USER_GROUP_TABLE.' ug ON u.id = ug.id_user
		 INNER JOIN '.GROUPS_TABLE.' g ON ug.id_group = g.id
			  WHERE u.id IN (' . implode(',', $id_arr) . ')
		   GROUP BY u.id
		   ORDER BY MAX(l.id) DESC
			  LIMIT '.$limit_offset.', '.$limit_length;
		
		$paramsSQL = array($id_user);
		
		if ($debug) echo '$strSQL='.$strSQL.'<br>'.print_r($paramsSQL, true).'<br>';
		
		$rs = $dbconn->Execute($strSQL, $paramsSQL);
		
		$search = array();
		$_LANG_NEED_ID = array();
		$i = 0;
		$search_type = 'et';
		
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
			
			// check hotlist
			$search[$i]['hotlisted'] = isInMyHotlist($id_user, $row['id']);
			
			// check blacklist
			$search[$i]['blacklisted'] = isInMyBlacklist($id_user, $row['id']);
			
			// check connected
			$search[$i]['connected_status'] = getConnectedStatus($id_user, $row['id']);
			
			// links
			$search[$i]['profile_link']		= 'viewprofile.php?id='.$row['id'].'&amp;search_type='.$search_type;
			$search[$i]['email_link']		= 'mailbox.php?sel=fs&amp;id='.$row['id'].'&amp;search_type='.$search_type;
			$search[$i]['sendfriend_link']	= 'send_friend.php?sel=send&amp;id_user='.$row['id'];
			$search[$i]['gift_link']		= 'giftshop.php?sel=users_add&amp;id_user='.$row['id'];
			$search[$i]['ecard_link']		= 'ecards.php?id_user_to='.$row['id'].'&amp;fixuser=Y';
			
			// kiss link
			if ($settings['use_kiss_types']) {
				$search[$i]['kiss_link'] = 'send_kiss.php?sel=send&amp;id_user='.$row['id'];
			} else {
				$search[$i]['kiss_link'] = $file_name.'?sel=kiss&amp;id='.$row['id'];
			}
			
			// hotlist link
			if ($search[$i]['hotlisted'] == 0 && $search[$i]['blacklisted'] == 0) {
				if ($settings['use_friend_types']) {
					$search[$i]['add_hotlist_link'] = 'hotlist.php?sel=addform&amp;id='.$row['id'];
				} else {
					$search[$i]['add_hotlist_link'] = $file_name.'?sel=addhotlist&amp;id='.$row['id'];
				}
			}
			
			// blacklist link
			if ($search[$i]['hotlisted'] == 0 && $search[$i]['connected_status'] != CS_CONNECTED && $search[$i]['blacklisted'] == 0) {
				$search[$i]['add_blacklist_link'] = $file_name.'?sel=addblacklist&amp;id='.$row['id'];
			}
			
			// connections link
			if ($search[$i]['blacklisted'] == 0 && $search[$i]['connected_status'] == CS_NOTHING) {
				if ($settings['use_friend_types']) {
					$search[$i]['add_connection_link'] = 'connections.php?sel=addform&amp;id='.$row['id'];
				} else {
					$search[$i]['add_connection_link'] = $file_name.'?sel=addconnection&amp;id='.$row['id'];
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
		$param = $file_name.'?filter='.$filter.'&amp;view='.$view.'&amp;';
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
	$smarty->assign('header', $lang['homepage']);
	$smarty->assign('header_s', $lang['matches']);
	$smarty->display(TrimSlash($config['index_theme_path']).'/emailed_them.tpl');
	exit;
}
?>