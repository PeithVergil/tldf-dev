<?php
/**
* User homepage (information about user billing, profile, profile visits, user perfect matches, horoscope...)
*
* @package DatingPro
* @subpackage User Mode
**/

include './include/config.php';
include './common.php';
include './include/config_index.php';
include './include/functions_auth.php';
include './include/functions_index.php';
include './include/functions_users.php';
include './include/functions_mm.php';
include './include/class.lang.php';
include './include/class.news.php';
include './include/class.percent.php';
include './include/functions_poll.php';
if (file_exists('./poll/poll_cookie.php')) include './poll/poll_cookie.php';
include './include/functions_events.php';

// authentication
$user = auth_index_user();

if (empty($user) || $user == 'err' || empty($user[ AUTH_ID_USER ])) {
	header('Location: '.$config['site_root'].'/index.php');
	exit;
}

// special check for admin
if ($user[ AUTH_ROOT ]) {
	header('Location: '.$config['site_root'].'/admin/index.php');
	exit;
}

// check guest
if ($user[ AUTH_GUEST ]) {
	AlertPage(GetRightModulePath(__FILE__));
}

// check group, period, expiration
RefreshAccount();

// check status
// (every user can access his/her homepage)

// check permissions
// (every user can access his/her homepage)

// alerts and statistics
GetAlertsMessage();
SetModuleStatistic(GetRightModulePath(__FILE__));

// active menu item
$smarty->assign('sub_menu_num', '1');

lastViewed();

// unset the session ReturnToView
unset($_SESSION['return_to_view']);

HomePage();
exit;


function HomePage()
{
	global $settings, $lang, $config, $smarty, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$act = isset($_GET['act']) ? $_GET['act'] : '';
	
	$file_name = 'homepage.php';
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
#	$log->log('after GetActiveUserInfo()');
	
	$smarty->assign('file_name', $file_name);
	
	cleanMailbox();
#	$log->log('after cleanMailbox()');
	
	// settings
	$settings = GetSiteSettings(array('icons_folder', 'zip_count', 'max_age_limit', 'min_age_limit',
		'icon_male_default', 'icon_female_default', 'icons_folder', 'site_unit_costunit', 'use_horoscope_feature',
		'free_site', 'use_pilot_module_organizer', 'use_lift_up_in_search_service', 'featured_users_slider_speed'));
#	$log->log('after GetSiteSettings()');
	
	// icon
	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];
	
	$profile_percent = new Percent($id_user);
#	$log->log('after new Percent(...)');
	
	$strSQL =
		'SELECT id, login, gender, date_birthday, big_icon_path, id_country, id_city, id_region,
				DATE_FORMAT(date_registration, "'.$config['date_format'].'") AS date_registration,
				DATE_FORMAT(date_last_seen, "'.$config['date_format'].'") AS date_last_login
		   FROM '.USERS_TABLE.'
		  WHERE id = ?';
	$rs = $dbconn->Execute($strSQL, array($id_user));
#	$log->log('after getting basic user data()');
	
	$i = 0;
	$row = $rs->GetRowAssoc(false);

	$page['name']				= $row['login'];
	$page['age']				= AgeFromBDate($row['date_birthday']);
	$page['id_country']			= intval($row['id_country']);
	$page['id_region']			= intval($row['id_region']);
	$page['id_city']			= intval($row['id_city']);
	$page['date_registration']	= $row['date_registration'];
	$page['last_login']			= $row['date_last_login'];
	$page['complete']			= $profile_percent->GetAllPercent();
	
	// language
	$_LANG_NEED_ID				= array();
	$_LANG_NEED_ID['country'][]	= (int) $row['id_country'];
	$_LANG_NEED_ID['region'][]	= (int) $row['id_region'];
	$_LANG_NEED_ID['city'][]	= (int) $row['id_city'];
	
	// icon
	$icon_path = $row['big_icon_path'];
	if ($icon_path && file_exists($config['site_path'].$settings['icons_folder'].'/'.$icon_path)) {
		$page['icon_path'] = $config['site_root'].$settings['icons_folder'].'/'.$icon_path;
	}
	
	unset($row);
	
	// photo count
	$page['photo_count'] = publicPhotoCountAlbums($id_user);
	if ($icon_path) {
		$page['photo_count']++;
	}
#	$log->log('after photo count');
	
	// video count
	$page['video_count'] = publicVideoCountAlbums($id_user);
#	$log->log('after video count');
	
	//VP id_group and group name
	$strSQL =
		'SELECT ug.id_group, g.name
		   FROM '.USER_GROUP_TABLE.' ug
	 INNER JOIN '.GROUPS_TABLE.' g ON ug.id_group = g.id
		  WHERE id_user = ?';
	$rs = $dbconn->Execute($strSQL, array($id_user));
#	$log->log('after getting group info');
	
	$page['id_group']			= (int) $rs->fields[0];
	$page['user_group']			= stripslashes($rs->fields[1]);
	
	// removing Lady and Guy from user group
	$page['user_group_2'] = str_replace(' Lady', '', $page['user_group']);
	$page['user_group_2'] = str_replace(' Guy', '', $page['user_group_2']);
	
	$id_group = $page['id_group'];
	
	if (in_array($id_group, array(MM_PLATINUM_LADY_FIRST_INS_ID, MM_PLATINUM_LADY_SECOND_INS_ID))) {
		$page['user_group_2'] = 'Platinum';
	}
	
	// check for platinum paid
	$data['platinum_paid'] = CheckIsPlatinumPaid();
#	$log->log('after CheckIsPlatinumPaid()');
	
	//VP privacy settings
	$strSQL =
		'SELECT hide_online, promotion_1, promotion_2, promotion_3, visible_lady, visible_guy,
				vis_lady_1, vis_lady_2, vis_lady_3, vis_lady_4, vis_lady_5,
				vis_guy_1, vis_guy_2, vis_guy_3, vis_guy_4, vis_guy_5
		   FROM '.USER_PRIVACY_SETTINGS.'
		  WHERE id_user = ?';
	$rs = $dbconn->Execute($strSQL, array($id_user));
#	$log->log('after getting privacy settings');
	
	$page['hide_online']		= intval($rs->fields[0]);
	
	$page['promotion_1']		= intval($rs->fields[1]);
	$page['promotion_2']		= intval($rs->fields[2]);
	$page['promotion_3']		= intval($rs->fields[3]);
	
	$page['visible_lady']		= intval($rs->fields[4]);
	$page['visible_guy']		= intval($rs->fields[5]);
	
	$page['vis_lady_1']			= intval($rs->fields[6]);
	$page['vis_lady_2']			= intval($rs->fields[7]);
	$page['vis_lady_3']			= intval($rs->fields[8]);
	$page['vis_lady_4']			= intval($rs->fields[9]);
	$page['vis_lady_5']			= intval($rs->fields[10]);
	
	$page['vis_guy_1']			= intval($rs->fields[11]);
	$page['vis_guy_2']			= intval($rs->fields[12]);
	$page['vis_guy_3']			= intval($rs->fields[13]);
	$page['vis_guy_4']			= intval($rs->fields[14]);
	$page['vis_guy_5']			= intval($rs->fields[15]);
	
	// matches
	// RS: not in use on TLDF
	/*
	$strSQL =
		'SELECT u.id, u.login, u.gender, u.date_birthday, u.icon_path, u.id_country, u.id_city, u.id_region
		   FROM '.USERS_TABLE.' u
	 INNER JOIN '.USER_TOPTEN_TABLE.' t ON u.id = t.id_user
		  WHERE u.status = "1"
		  ORDER BY RAND()
		  LIMIT 1';
	$rs = $dbconn->Execute($strSQL);
	$row = $rs->GetRowAssoc(false);
	
	$top_users['name']		= $row['login'];
	$top_users['age']		= AgeFromBDate($row['date_birthday']);
	$top_users['link']		= './viewprofile.php?id='.$row['id'].'&sel=5';
	$top_users['country']	= $dbconn->GetOne('SELECT name FROM '.COUNTRY_SPR_TABLE.' WHERE id = ?', array($row['id_country']));
	$top_users['region']	= $dbconn->GetOne('SELECT name FROM '.REGION_SPR_TABLE.' WHERE id = ?', array($row['id_region']));
	$top_users['city']		= $dbconn->GetOne('SELECT name FROM '.CITY_SPR_TABLE.' WHERE id = ?', array($row['id_city']));
	
	$icon_path = $row['icon_path'] ? 'big_'.$row['icon_path'] : $default_photos[$row['gender']];
	if ($icon_path && file_exists($config['site_path'].$settings['icons_folder'].'/'.$icon_path)) {
		$top_users['icon_path'] = $config['site_root'].$settings['icons_folder'].'/'.$icon_path;
	}
	
	$top_users['photo_count'] = publicPhotoCountAlbums($row['id']);
	if ($icon_path) {
		$top_users['photo_count']++;
	}
	
	$_LANG_NEED_ID['country'][] = intval($row['id_country']);
	$_LANG_NEED_ID['region'][] = intval($row['id_region']);
	$_LANG_NEED_ID['city'][] = intval($row['id_city']);
	
	$smarty->assign('top_users', $top_users);
	*/
	
	// RS: NOT IN USE ON TLDF
	/*
	// if user"s perfect match is empty
	$profile_percent = new Percent($user[ AUTH_ID_USER ]);
	$descr_perc = $profile_percent->GetSectionPercent(6);
	$interests_perc = $profile_percent->GetSectionPercent(7);
	
	// $descr_perc ~ 70%, $interests_perc ~ 30%
	// this is avg percent if $summ_all< 75 user must fill his perfect mach section
	$summ_all = round($descr_perc * 0.7 + $interests_perc * 0.3);
	$hotlist['match_count'] = 0;
	
	if ($summ_all >= 75)
	{
		$match_arr = GetPerfectUsersList($id_user);
		$hotlist['match_count'] = isset($match_arr['id_arr']) ? count($match_arr['id_arr']) : 0;
		if (isset($match_arr['id_arr']) && is_array($match_arr['id_arr'])) {
			$user_arr['id_arr'] = (count($match_arr['id_arr']) < 4) ? $match_arr['id_arr'] : array_slice($match_arr['id_arr'], 0, 4);
			$user_str = implode(', ', $user_arr['id_arr']);
		} else {
			$user_str = '""';
		}
		
		$strSQL =
			'SELECT id, login, gender, date_birthday, icon_path, id_country, id_city, id_region
			   FROM '.USERS_TABLE.'
			  WHERE id IN ('.$user_str.')';
		$rs = $dbconn->Execute($strSQL);
		
		$i = 0;
		$visited = array();
		
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			
			$visited[$i]['name']		= $row['login'];
			$visited[$i]['age']			= AgeFromBDate($row['date_birthday']);
			$visited[$i]['id_country']	= intval($row['id_country']);
			$visited[$i]['id_region']	= intval($row['id_region']);
			$visited[$i]['id_city']		= intval($row['id_city']);
			$visited[$i]['link']		= './viewprofile.php?id='.$row['id'];
			
			$icon_path = $row['icon_path'] ? $row['icon_path'] : $default_photos[$row['gender']];
			if ($icon_path && file_exists($config['site_path'].$settings['icons_folder'].'/'.$icon_path)) {
				$visited[$i]['icon_path'] = $config['site_root'].$settings['icons_folder'].'/'.$icon_path;
			}
			
			$visited[$i]['photo_count'] = publicPhotoCountAlbums($row['id']);
			if ($icon_path) {
				$visited[$i]['photo_count']++;
			}
			
			$_LANG_NEED_ID['country'][] = intval($row['id_country']);
			$_LANG_NEED_ID['region'][] = intval($row['id_region']);
			$_LANG_NEED_ID['city'][] = intval($row['id_city']);
			
			$rs->MoveNext();
			$i++;
		}
		
		$smarty->assign('visited', $visited);
	}
	*/
	
	$account['units'] = $settings['site_unit_costunit'];
	
	$rs = $dbconn->Execute('SELECT account_curr FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($id_user));
	
	//added by Narendra
	if ($id_group == MM_PLATINUM_LADY_FIRST_INS_ID || $id_group == MM_PLATINUM_LADY_SECOND_INS_ID || $id_group == MM_PLATINUM_LADY_ID) {
		$account['account'] = 'n/a';
	} else {
		$account['account'] = round($rs->fields[0], 2); 
	}
	
#	$log->log('after getting credits');
	
	if (isset($settings['use_lift_up_in_search_service']) && $settings['use_lift_up_in_search_service'] == 1) {
		$account['lift_up_link'] = './payment.php?sel=service&service=lift_up';
	}
	
	$profile['perfect_match_link'] = './perfect_match.php';
	
	$hotlist['my_connections_link']	= $config['site_root'].'/connections.php';
	$hotlist['my_hotlist_link']		= $config['site_root'].'/hotlist.php';
	$hotlist['visit_me_link']		= $config['site_root'].'/visit_my_page.php';
	$hotlist['visit_them_link']		= $config['site_root'].'/visit_their_page.php';
	$hotlist['meetme_link']			= $config['site_root'].'/meet_me.php';
	$hotlist['theirhotlist_link']	= $config['site_root'].'/quick_search.php?sel=search_h';
	$hotlist['kiss_me_link']		= $config['site_root'].'/kisses.php?sel=me';
	$hotlist['emailed_me_link']		= $config['site_root'].'/emailed_me.php';
	$hotlist['perfect_link']		= $config['site_root'].'/perfect_match.php';
	$hotlist['meetthem_link']		= $config['site_root'].'/meet_them.php';
	$hotlist['kiss_them_link']		= $config['site_root'].'/kisses.php?sel=i';
	$hotlist['emailed_them_link']	= $config['site_root'].'/emailed_them.php';
	$hotlist['connect_me_link']		= $config['site_root'].'/connections.php?sel=inbox';
	$hotlist['connect_them_link']	= $config['site_root'].'/connections.php?sel=outbox';
	$hotlist['ecard_me_link']		= $config['site_root'].'/ecards_me.php';
	$hotlist['ecard_them_link']		= $config['site_root'].'/ecards_them.php';
	
	$hotlist['my_connections_count'] = connectedCount($id_user);
	$hotlist['my_hotlist_count'] = hotlistCount($id_user, $id_group);
	
	// mix and mingle count
	$hotlist['connect_me_count'] = invitedMeConnectCount($id_user, $id_group);
	$hotlist['connect_them_count'] = invitedThemConnectCount($id_user, $id_group);
	
	$hotlist['emailed_me_count'] = emailedMeCount($id_user, $id_group);
	$hotlist['emailed_them_count'] = emailedThemCount($id_user, $id_group);
	
	$hotlist['ecard_me_count'] = ecardsMeCount($id_user, $id_group);
	$hotlist['ecard_them_count'] = ecardsThemCount($id_user, $id_group);
	
	$hotlist['kiss_me_count'] = kissedMeCount($id_user, $id_group);
	$hotlist['kiss_them_count'] = kissedThemCount($id_user, $id_group);
	
	$hotlist['visit_me_count'] = visitedMeCount($id_user, $id_group);
	$hotlist['visit_them_count'] = visitedThemCount($id_user, $id_group);
	
	// they want to meet me
	$meet_me_arr = GetWantToMeetMeList($id_user, true);
	$hotlist['meet_me_count'] = isset($meet_me_arr['id_arr']) ? count($meet_me_arr['id_arr']) : 0;
	
	// I want to meet them
	$meet_them_arr = GetWantToMeetThemList($id_user, false);
	$hotlist['meet_them_count'] = isset($meet_them_arr['id_arr']) ? count($meet_them_arr['id_arr']) : 0;
	
	$hotlist['their_hotlist_count'] = theirHotlistCount($id_user, $id_group);
#	$log->log('after getting mix & mingle counts');
	
	$use_refer_friend_feature = GetSiteSettings('use_refer_friend_feature');
	
	if ($use_refer_friend_feature) {
		$hotlist['referred_link'] = $config['server'].$config['site_root'].'/quick_search.php?sel=search_referred';
		$hotlist['referred_count'] = GetCountReferredFriends($id_user);
		$smarty->assign('user_refer_frends',GetUserReferCode($id_user));
	}
	
	if (!$user[ AUTH_STATUS ]) {
		$form['err'] = $lang['home_page']['alert_header_status'];
		$rs = $dbconn->Execute('SELECT confirm FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
		if (!$rs->fields[0])
			$form['err'] .= '<br>'.$lang['home_page']['alert_header_confirm'];
	}

	$form['use_horoscope'] = ($settings['use_horoscope_feature']) ? true : false;
	
	// RS: run horoscope queries only when horoscope is in use
	if ($form['use_horoscope'])
	{
		$rs = $dbconn->Execute(
			'SELECT DATE_FORMAT(date_birthday, "%m"), DATE_FORMAT(date_birthday, "%d")
			   FROM '.USERS_TABLE.'
			  WHERE id = ?',
			array($id_user));
		$birth_month = $rs->fields[0];
		$birth_day = $rs->fields[1];
		
		$rs = $dbconn->Execute(
			'SELECT id
			   FROM '.HOROSCOPE_SIGNS_TABLE.'
			  WHERE DATE_FORMAT(date_start, "%m") = ? AND DATE_FORMAT(date_start, "%d") <= ?',
			array($birth_month, $birth_day));
		
		if ($rs->fields[0]) {
			$sign = $rs->fields[0];
		} else {
			$rs = $dbconn->Execute(
				'SELECT id
				   FROM '.HOROSCOPE_SIGNS_TABLE.'
				  WHERE DATE_FORMAT(date_end, "%m") = ? AND DATE_FORMAT(date_end, "%d") >= ?',
				array($birth_month, $birth_day));
			$sign = $rs->fields[0];
		}
		
		$rs = $dbconn->Execute('SELECT id, name FROM '.HOROSCOPE_SIGNS_TABLE);
		$horoscope = array();
		$i = 0;
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$horoscope[$i]['sign_name'] = $lang['horoscope'][$row['name']]['name'];
			$horoscope[$i]['sign_link'] = './horoscope.php?sel=view&sign='.$row['name'];
			if ($sign == $row['id']) {
				$horoscope[$i]['my_sign'] = 1;
			}
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign('horoscope', $horoscope);
	}
#	$log->log('after horoscope');
	
	$form['use_account'] = ($settings['free_site']) ? false : true;
	
	// country, region, city select for quick search
	if (QUICK_SEARCH_COUNTRY) {
		$smarty->assign('countries', html_country_select($page['id_country']));
	}
	
	if (QUICK_SEARCH_REGION) {
		$smarty->assign('regions', html_region_select($page['id_country'], $page['id_region']));
	}
	
	if (QUICK_SEARCH_CITY) {
		$smarty->assign('cities', html_city_select($page['id_region'], $page['id_city']));
	}
	
	// search preferences
	$strSQL =
		'SELECT a.gender, b.gender as gender_search, b.couple as couple_search, b.age_min, b.age_max, b.id_relationship
		   FROM '.USERS_TABLE.' a
	 INNER JOIN '.USER_MATCH_TABLE.' b ON b.id_user = a.id
		  WHERE a.id = ?';
	$rs = $dbconn->Execute($strSQL, array($id_user));
	$row = $rs->GetRowAssoc(false);
#	$log->log('after getting search preferences');
	
	$data['gender_1'] = $row['gender'];
	$data['gender_2'] = $row['gender_search'];
	$data['couple_2'] = $row['couple_search'];
	$data['age_min'] = $row['age_min'];
	$data['age_max'] = $row['age_max'];
	
	if (!empty($row['id_relationship'])) {
		$data['arr_relationship'] = explode(',', $row['id_relationship']);
	} else {
		$data['arr_relationship'] = 0;
	}
	
	unset($rs, $row);
	
	// gender selection for search
	$gender_arr						= array();
	
	$gender_arr[0]['id']			= '1';
	$gender_arr[0]['name']			= $lang['gender']['1'];
	$gender_arr[0]['name_search']	= $lang['gender_search']['1'];
	$gender_arr[0]['sel']			= intval($data['gender_1']) == 1 ? 1 : 0;
	$gender_arr[0]['sel_search']	= intval($data['gender_2']) == 1 ? 1 : 0;
	
	$gender_arr[1]['id']			= '2';
	$gender_arr[1]['name']			= $lang['gender']['2'];
	$gender_arr[1]['name_search']	= $lang['gender_search']['2'];
	$gender_arr[1]['sel']			= intval($data['gender_1']) == 2 ? 1 : 0;
	$gender_arr[1]['sel_search']	= intval($data['gender_2']) == 2 ? 1 : 0;
	
	$smarty->assign('gender', $gender_arr);
	
	// max. zip code length
	$form['zip_count'] = $settings['zip_count'];
	
	$max_age = (int) $settings['max_age_limit'];
	$min_age = (int) $settings['min_age_limit'];
	
	$smarty->assign('age_max', range($max_age, $min_age));
	$smarty->assign('age_min', range($min_age, $max_age));
	
	// relationships select
	if (QUICK_SEARCH_RELATIONSHIP) {
		$smarty->assign('relation', html_relationship_select($data['arr_relationship']));
	}
	
	// distance select
	if (QUICK_SEARCH_DISTANCE) {
		$smarty->assign('distances', html_distance_select());
	}
	
	if (isset($config['use_pilot_module_club']) && $config['use_pilot_module_club'] == 1) {
		GetUserClubs($id_user);
	}
	
	// RS: not in use on TLDF
	/*
	$place = GetUserRatingPlace($id_user);
	if ($place != null) {
		$form['place'] = $place;
	}
	*/
	
	if (isset($settings['use_pilot_module_organizer']) && $settings['use_pilot_module_organizer'] == 1)
	{
		$strSQL =
			'SELECT id, area_1, area_2, area_3, area_4, area_5, area_6, area_7, area_8, area_9
			   FROM '.ORG_USER_HOME_OPTIONS_TABLE.'
			  WHERE id_user = ?';
		$rs = $dbconn->Execute($strSQL, array($id_user));
		
		if ($rs->fields[0] > 0) {
			$org_home[0] = ($rs->fields[1] == 1) ? 'true': 'false';
			$org_home[1] = ($rs->fields[2] == 1) ? 'true': 'false';
			$org_home[2] = ($rs->fields[3] == 1) ? 'true': 'false';
			$org_home[3] = ($rs->fields[4] == 1) ? 'true': 'false';
			$org_home[4] = ($rs->fields[5] == 1) ? 'true': 'false';
			$org_home[5] = ($rs->fields[6] == 1) ? 'true': 'false';
			$org_home[6] = ($rs->fields[7] == 1) ? 'true': 'false';
			$org_home[7] = ($rs->fields[8] == 1) ? 'true': 'false';
			$org_home[8] = ($rs->fields[9] == 1) ? 'true': 'false';
		} else {
			//Clubs disabled
			//$org_home = array('true','true','true','true','true','true','true','true','true');
			$org_home = array('true','true','true','true','true','true','false','true','true');
		}
		
		$smarty->assign('org_home', $org_home);
		$smarty->assign('hide', 1);
	}
#	$log->log('after organizer');
	
	// Home Featured Users
	$slide_gender = $user[ AUTH_GENDER ] == GENDER_MALE ? GENDER_FEMALE : GENDER_MALE;
	
	$strSQL =
		'SELECT u.id, u.fname, u.gender, u.date_birthday, u.big_icon_path, u.id_country, u.what_i_do, ug.id_group
		   FROM '.USERS_TABLE.' u
	 INNER JOIN '.USER_GROUP_TABLE.' ug ON u.id = ug.id_user
	 INNER JOIN '.USER_PRIVACY_SETTINGS.' up ON u.id = up.id_user
		  WHERE u.gender = ?
			AND u.status = "1" AND u.confirm = "1" AND up.promotion_1 != "1" AND up.featured_home = "1"
		  GROUP BY u.id
		  ORDER BY RAND()
		  LIMIT 25;';
	
	$rs = $dbconn->Execute($strSQL, array((string)$slide_gender));
	
	$i = 1;
	$promo_user = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$promo_user[$i]['name']			= $row['fname'];
		$promo_user[$i]['gender']		= $row['gender'];
		$promo_user[$i]['age']			= AgeFromBDate($row['date_birthday']);
		$promo_user[$i]['icon_path']	= $row['big_icon_path'];
		$promo_user[$i]['profile_link']	= './viewprofile.php?id='.$row['id'];
		$promo_user[$i]['id_country']	= intval($row['id_country']);
		$promo_user[$i]['what_i_do']	= substr($row['what_i_do'], 0, 150);
		$promo_user[$i]['id_group']		= $row['id_group'];
		
		$icon_path = str_replace('big_thumb_', '', $promo_user[$i]['icon_path']);
		
		if ($icon_path && file_exists($config['site_path'].$settings['icons_folder'].'/'.$icon_path)) {
			$promo_user[$i]['icon_path'] = $config['site_root'].$settings['icons_folder'].'/'.$icon_path;
		} else {
			$file = $default_photos[$promo_user[$i]['gender']];
			$promo_user[$i]['icon_path'] = '.'.$settings['icons_folder'].'/'.$file;
		}
		
		$_LANG_NEED_ID['country'][] = intval($promo_user[$i]['id_country']);
		
		$rs->MoveNext();
		$i++;
	}
#	$log->log('after featured users');
	
	$smarty->assign('promo_user', $promo_user);
	$smarty->assign('users_slider_speed', $settings['featured_users_slider_speed']);
	
	// RS: not in use on TLDF
	/*
	if ($config['color_theme'] == 'niche') {
		GetLastUploades();
	}
	
	if ($config['color_theme'] == 'adult' || $config['color_theme'] == 'gay' || $config['color_theme'] == 'lesby' || $config['color_theme'] == 'matrimonial' || $config['color_theme'] == 'niche') {
		$smarty->assign('new_users', GetNewUsers());
	}
	*/
	
	//SH displaying welcome message
	if ($act == 'welcome') {
		$form['err'] = $lang['home_page']['welcome_text'];
	}
	
	$smarty->assign('base_lang', GetBaseLang($_LANG_NEED_ID));
	$smarty->assign('hotlist', $hotlist);
	// RS: news, poll and events not in use on TLDF
#	$smarty->assign('news', HomepageNews());
#	$log->log('after HomepageNews()');
#	$smarty->assign('poll_bar', PollBar());
#	$log->log('after PollBar()');
#	$smarty->assign('events', EventsMain());
#	$log->log('after EventsMain()');
	$smarty->assign('page', $page);
	$smarty->assign('account', $account);
	$smarty->assign('profile', $profile);
	$smarty->assign('form', $form);

	$smarty->assign('data', $data);
	$smarty->assign('section', $lang['section']);
	$smarty->assign('header', $lang['homepage']);
	
	if (isset($config['use_pilot_module_organizer']) && ($config['use_pilot_module_organizer'] == 1))
	{
		$strSQL =
			'SELECT id, home_area_color, menu_back_1_color, menu_back_2_color,
					menu_back_3_color, menu_back_4_color, menu_font_1_color, menu_font_2_color,
					menu_font_3_color, menu_font_4_color, link_color, header_color, content_color,
					search_color, shoutbox_color, main_text_color,
					big_bg_color, bg_picture_path
			   FROM '.ORG_USER_LAYOUTS_TABLE.'
			  WHERE id_user = ?';
		$rs = $dbconn->Execute($strSQL, array($id_user));
		
		if ($rs->fields[0] > 0) {
			$row = $rs->GetRowAssoc(false);
			if ($row['home_area_color'] != '') {
				$color['home_menu'] = $row['home_area_color'];
			}
			if ($row['shoutbox_color'] != '') {
				$color['shoutbox_color'] = $row['shoutbox_color'];
				$_SESSION['shoutbox_color_my'] = $row['shoutbox_color'];
			}
			if ($row['menu_back_1_color'] != '') {
				$color['menu_block_1'] = $row['menu_back_1_color'];
			}
			if ($row['menu_back_2_color'] != '') {
				$color['menu_block_2'] = $row['menu_back_2_color'];
			}
			if ($row['menu_back_3_color'] != '') {
				$color['menu_block_3'] = $row['menu_back_3_color'];
			}
			if ($row['menu_back_4_color'] != '') {
				$color['menu_block_4'] = $row['menu_back_4_color'];
			}
			if ($row['menu_font_1_color'] != '') {
				$color['menu_link_1'] = $row['menu_font_1_color'];
			}
			if ($row['menu_font_2_color'] != '') {
				$color['menu_link_2'] = $row['menu_font_2_color'];
			}
			if ($row['menu_font_3_color'] != '') {
				$color['menu_link_3'] = $row['menu_font_3_color'];
			}
			if ($row['menu_font_4_color'] != '') {
				$color['menu_link_4'] = $row['menu_font_4_color'];
			}
			if ($row['link_color'] != '') {
				$color['link'] = $row['link_color'];
			}
			if ($row['header_color'] != '') {
				$color['header'] = $row['header_color'];
			}
			if ($row['content_color'] != '') {
				$color['content'] = $row['content_color'];
			}
			if ($row['search_color'] != '') {
				$color['home_search'] = $row['search_color'];
			}
			if ($row['big_bg_color'] != '') {
				$color['bg_color'] = $row['big_bg_color'];
			}
			if ($row['main_text_color'] != '') {
				$color['main_text_color'] = $row['main_text_color'];
			}
			if ($row['bg_picture_path'] != '') {
				$settings = GetSiteSettings( array('photos_folder'));
				$color['bg_picture_path'] = $config['site_root'].$settings['photos_folder'].'/'.$row['bg_picture_path'];
			}
			$smarty->append('css_color', $color, true);
			$smarty->assign('customised', '1');
			$smarty->assign('id_customed', $user[ AUTH_ID_USER ]);
		}
		else
		{
			unset($_SESSION['shoutbox_color_my']);
		}
	}
	else
	{
		unset($_SESSION['shoutbox_color_my']);
	}
	
#	$log->log('after organizer 2');
	
	$smarty->display(TrimSlash($config['index_theme_path']).'/homepage_table.tpl');
	exit;
}

function HomepageNews()
{
	global $config, $config_index;
	
	if ($config['color_theme'] == 'niche') {
		$config_index['news_homepage_numpage'] = 3;
	}
	
	$news = GetLastNews($config_index['news_homepage_numpage']);
	
	if (is_array($news) && count($news) > 0) {
		foreach ($news as $key => $n){
			$news[$key]['text'] = strip_tags($n['title']);
			if(!strlen(utf8_decode($news[$key]['text'])))
			$news[$key]['text'] = utf8_substr(strip_tags($n['news_text']), 0, 100).'...';
			$news[$key]['date'] = $n['date_add'];
			$news[$key]['link_read'] = GetNewsReadLink($n['id']);
		}
		return $news;
	} else {
		return;
	}
}

function GetUserClubs($id_user)
{
	global $config, $smarty, $dbconn;
	
	$settings = GetSiteSettings(array('show_users_connection_str','show_users_comments','show_users_group_str','photos_default','club_uploads_folder','thumb_max_width'));
	
	$strSQL = 'SELECT DISTINCT id_club FROM '.CLUB_USERS_TABLE.' WHERE id_user = ? GROUP BY id_club LIMIT 0,4';
	$rs = $dbconn->Execute($strSQL, array($id_user));
	$num_records = $rs->RowCount();
	
	if ($num_records > 0)
	{
		$id_arr = array();
		
		while (!$rs->EOF) {
			$id_arr[] = $rs->fields[0];
			$rs->MoveNext();
		}
		
		$id_str = implode(',', $id_arr);
		
		$strSQL =
			'SELECT DISTINCT ct.id, ct.name as club_name, cct.name as category, cut.upload_path, ct.id_creator as leader_id, ct.is_open
			   FROM '.CLUB_TABLE.' ct
		  LEFT JOIN '.CLUB_CATEGORIES_TABLE.' cct ON cct.id = ct.id_category
		  LEFT JOIN '.CLUB_UPLOADS_TABLE.' cut ON (cut.id_club = ct.id AND cut.club_icon = "1" AND cut.status = "1" AND cut.upload_type = "f")
			  WHERE ct.id IN ('.$id_str.')
		   GROUP BY ct.id
		   ORDER BY ct.id DESC';
		$rs = $dbconn->Execute($strSQL);
		
		$i = 0;
		$clubs = array();
		
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$clubs[$i]['id']		= $row['id'];
			$clubs[$i]['club_name']	= stripslashes($row['club_name']);
			$clubs[$i]['category']	= stripslashes($row['category']);
			$clubs[$i]['is_open']	= $row['is_open'];
			
			if ($row['leader_id'] == $id_user){
				$clubs[$i]['user_is_leader'] = 1;
			}
			
			$icon_path = $row['upload_path'] ? $row['upload_path'] : $settings['photos_default'];
			
			if ($icon_path && file_exists($config['site_path'].$settings['club_uploads_folder'].'/thumb_'.$icon_path)) {
				$clubs[$i]['icon_path'] = $config['site_root'].$settings['club_uploads_folder'].'/thumb_'.$icon_path;
			} else {
				$clubs[$i]['icon_path'] = $config['site_root'].$settings['club_uploads_folder'].'/'.$settings['photos_default'];
			}
			
			$clubs[$i]['link'] = 'club.php?sel=club&id_club='.$clubs[$i]['id'];

			$strSQL = 'SELECT id FROM '.CLUB_USERS_TABLE.' WHERE id_club = ? AND id_user = ?' ;
			$rs_c = $dbconn->Execute($strSQL, array($clubs[$i]['id'], $id_user));
			
			$clubs[$i]['user_in_club'] = ($rs_c->fields[0] > 0) ? 1 : 0;
			
			$rs->MoveNext();
			$i++;
		}

		$smarty->assign('clubs', $clubs);
	}
	else
	{
		$smarty->assign('clubs', 'empty');
	}
	
	return;
}

function GetNewUsers($limit=10)
{
	global $dbconn, $settings, $config, $default_photos;
	
	$strSQL =
		'SELECT DISTINCT id, icon_path, login
		   FROM '.USERS_TABLE.'
		  WHERE status = "1" AND root_user = "0" AND guest_user = "0" AND icon_path != ""
	   ORDER BY date_registration DESC
		  LIMIT 0,'.$limit;
	$rs = $dbconn->Execute($strSQL);
	
	$new_users = array();
	$i = 0;
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$new_users[$i]['link']		= './viewprofile.php?id='.$row['id'];
		$new_users[$i]['login']		= $row['login'];
		
		$icon_path = $row['icon_path']?$row['icon_path']:$default_photos[$row['gender']];
		if ($icon_path && file_exists($config['site_path'].$settings['icons_folder'].'/'.$icon_path)) {
			$new_users[$i]['icon_path'] = $config['site_root'].$settings['icons_folder'].'/'.$icon_path;
		}
		
		$icon_image = (strlen($row['icon_path'])) ? 1 : 0;
		
		$strSQL = 'SELECT COUNT(*) FROM '.USER_UPLOAD_TABLE.' WHERE id_user = ? AND upload_type = "f" AND status = "1" AND allow IN ("1", "2")';
		$rs_sub = $dbconn->Execute($strSQL, array($row['id']));
		$new_users[$i]['photo_count'] = intval($rs_sub->fields[0]) + $icon_image;
		
		$rs->MoveNext();
		$i++;
	}
	
	return $new_users;
}

function GetLastUploades()
{
	global $smarty, $config, $dbconn;
	
	$upload_folder = GetSiteSettings('photos_folder');
	
	// multi-language tables
	$multi_lang = new MultiLang();
	
	$strSQL =
		'SELECT DISTINCT uu.id, uu.upload_path, gc.id, rl.'.$multi_lang->DefaultFieldName().' as name, ut.login, uu.id_user, uu.is_adult, ual.title
		   FROM '.USER_UPLOAD_TABLE.' uu
	  LEFT JOIN '.GALLERY_CATEGORIES_TABLE.' gc ON gc.id = uu.id_gallery
	  LEFT JOIN '.USERS_TABLE.' ut ON ut.id = uu.id_user
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' rl ON gc.id = rl.id_reference AND rl.table_key = "'.$multi_lang->TableKey(GALLERY_CATEGORIES_TABLE).'"
	  LEFT JOIN '.USER_ALBUMS.' ual ON ual.id = uu.id_album
		  WHERE uu.is_gallary = "1" AND uu.status = "1" AND uu.allow = "1" AND is_adult != "1" AND uu.upload_type = "f"
	   GROUP BY uu.id
	   ORDER BY uu.id DESC
		  LIMIT 0,6';
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$last_uploads = array();
	while (!$rs->EOF) {
		$last_uploads[$i]['id'] = $rs->fields[0];
		$last_uploads[$i]['link_type'] = 3;
		$last_uploads[$i]['view_link'] = 'gallary.php?sel=view_upload&upload_type=f&id='.$last_uploads[$i]['id'];
		if (isset($rs->fields[1]) && $rs->fields[1] != '' && file_exists($config['site_path'].$upload_folder.'/thumb_'.$rs->fields[1])) {
			$last_uploads[$i]['upload_path'] = $config['server'].$config['site_root'].$upload_folder.'/thumb_'.$rs->fields[1];
		}
		$last_uploads[$i]['category_id'] = $rs->fields[2];
		$last_uploads[$i]['category_name'] = stripslashes($rs->fields[3]);
		$last_uploads[$i]['author_login'] = stripslashes($rs->fields[4]);
		$last_uploads[$i]['author_id'] = $rs->fields[5];
		$last_uploads[$i]['is_adult'] = $rs->fields[6];
		$last_uploads[$i]['album_title'] = stripslashes($rs->fields[7]);
		if (strlen(utf8_decode($last_uploads[$i]['album_title'])>15)) {
			$last_uploads[$i]['album_title'] = utf8_substr($last_uploads[$i]['album_title'],0,15);
		}
		$rs->MoveNext();
		$i++;
	}
	$smarty->assign('last_uploads', $last_uploads);
	return ;
}

?>