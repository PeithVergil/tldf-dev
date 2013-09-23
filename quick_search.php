<?php
/**
* Quick search form, functions and results listings
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
// (handled by permissions)

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
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

// limited access for trial users and inactive users
// addconnections gets a special treatment in function AddToConnections() in functions_index.php
if ($sel == 'be_couple') {
	if ($user[ AUTH_IS_TRIAL ]) {
		$sel = $_GET['par'];
		$_GET['par'] = 'send';
		SearchTable($lang['error']['access_denied_trial'], $sel);
		exit;
	}
	if ($user[ AUTH_IS_INACTIVE ]) {
		$sel = $_GET['par'];
		$_GET['par'] = 'send';
		SearchTable($lang['error']['access_denied_inactive'], $sel);
		exit;
	}
}

// dispatcher
switch ($sel) {
	case 'search':
	case 'search_name':
	case 'search_fname':
	case 'search_h':
	case 'search_on':
	case 'search_bd':
	case 'search_new':
	case 'search_top':
	case 'search_keyword':
	case 'search_tag':
	case 'search_all':
	case 'search_referred':
		SearchTable('', $sel);
	break;
	
	//SH2
		case 'sendecard':
		$res = SendEcardTo();
		$sel = $_GET['par'];
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			if($_SESSION['err']) {
				header('Location:  quick_search.php?sel='.$sel.'&par=send');
			} else {
				$id = $_GET['id'];
				header('Location:ecards.php?id_user_to='.$id.'&amp;fixuser=Y');
			}
			exit;
		} else {
			$_GET['par'] = 'viewprofile';
			SearchTable($res['err'], $sel);
		}
	break;
	
	//SH2
		case 'sendgift':
		$res = SendGiftTo();
		$sel = $_GET['par'];
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			if($_SESSION['err']) {
				header('Location: quick_search.php?sel='.$sel.'&par=send');
			} else {
				$id = $_GET['id'];
				header('Location:giftshop.php?sel=users_add&amp;id_user='.$id.'');
			}
			exit;
		} else {
			$_GET['par'] = 'viewprofile';
			SearchTable($res['err'], $sel);
		}
	break;
	
	case 'viewprofile':
		$res = ViewUserProfile();
		$sel = $_GET['par'];
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			if($_SESSION['err']) {
				header('Location: quick_search.php?sel='.$sel.'&par=send');
			} else {
				$id = $_GET['id'];
				header('Location: viewprofile.php?id='.$id.'&par='.$sel.'');
			}
			exit;
		} else {
			$_GET['par'] = 'viewprofile';
			SearchTable($res['err'], $sel);
		}
	break;
	
	case 'kiss':
		$res = SendKiss();
		$sel = $_GET['par'];
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			header('Location: quick_search.php?sel='.$sel.'&par=send');
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
			header('Location: quick_search.php?sel='.$sel.'&par=send');
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
			header('Location: quick_search.php?sel='.$sel.'&par=send');
			exit;
		} else {
			$_GET['par'] = 'send';
			SearchTable($res['err'], $sel);
		}
	break;
	
	case 'addconnection':
		$res = AddToConnections();
		$sel = $_GET['par'];
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			header('Location: quick_search.php?sel='.$sel.'&par=send');
			exit;
		} else {
			$_GET['par'] = 'send';
			SearchTable($res['err'], $sel);
		}
	break;
	
	case 'be_couple':
		$res = MakeCoupleAction();
		$sel = $res['par'];
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			header('Location: quick_search.php?sel='.$sel.'&par=send');
			exit;
		} else {
			$_GET['par'] = 'send';
			SearchTable($res['err'], $sel);
		}
	break;
	
	default:
		SearchForm();
	break;
}

exit;

function SearchForm($err = '')
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$file_name = 'quick_search.php';
	
	$settings = GetSiteSettings(array(
		'max_age_limit',
		'min_age_limit',
		'zip_letters',
		'zip_count',
	));
	
	// search table
	$search_type = '1';
	
	$rs_user = $dbconn->Execute(
		'SELECT id_country, id_region, id_city FROM '.USERS_TABLE.' WHERE id = ?',
		array($id_user));
	$row_user = $rs_user->GetRowAssoc(false);
	
	// country select
	if (QUICK_SEARCH_COUNTRY) {
		$smarty->assign('countries', html_country_select($row_user['id_country']));
	}
	
	// region select
	if (QUICK_SEARCH_REGION) {
		$smarty->assign('regions', html_region_select($row_user['id_country'], $row_user['id_region']));
	}
	
	// city select
	if (QUICK_SEARCH_CITY) {
		$smarty->assign('cities', html_city_select($row_user['id_region'], $row_user['id_city']));
	}
	
	// distance select
	if (QUICK_SEARCH_DISTANCE) {
		$smarty->assign('distances', html_distance_select());
	}
	
	// tag select
	if (QUICK_SEARCH_TAGS) {
		$smarty->assign('tags', html_tag_select($file_name));
	}
	
	// max. zip code length
	$form['zip_count'] = $settings['zip_count'];
	
	// search preferences
	if (!$user[ AUTH_GUEST ]) {
		$strSQL =
			'SELECT u.id, u.gender, m.gender AS gender_search, m.couple AS couple_search,
					m.age_min, m.age_max, m.id_relationship
			   FROM '.USERS_TABLE.' u
		  LEFT JOIN '.USER_MATCH_TABLE.' m ON m.id_user = u.id
			  WHERE u.id = ?';
		$rs = $dbconn->Execute($strSQL, array($id_user));
		$row = $rs->GetRowAssoc(false);
		
		$data['gender_1'] = (int) $row['gender'];
		$data['gender_2'] = (int) $row['gender_search'];
		$data['couple_2'] = $row['couple_search'];
		$data['age_min'] = $row['age_min'];
		$data['age_max'] = $row['age_max'];
		if ($row['id_relationship'] != '' && $row['id_relationship'] != '0') {
			$data['arr_relationship'] = explode(',', $row['id_relationship']);
		} else {
			$data['arr_relationship'] = 0;
		}
		if (QUICK_SEARCH_RELATIONSHIP) {
			$smarty->assign('relation', html_relationship_select($data['arr_relationship']));
		}
	} else {
		$data['gender_1'] = GENDER_FEMALE;
		$data['gender_2'] = GENDER_MALE;
	}
	
	// gender select
	$gender_arr = array();
	
	$gender_arr[0]['id'] = '1';
	$gender_arr[0]['name'] = $lang['gender']['1'];
	$gender_arr[0]['name_search'] = $lang['gender_search']['1'];
	$gender_arr[0]['sel'] = ($data['gender_1'] == 1) ? 1 : 0;
	$gender_arr[0]['sel_search'] = ($data['gender_2'] == 1) ? 1 : 0;
	
	$gender_arr[1]['id'] = '2';
	$gender_arr[1]['name'] = $lang['gender']['2'];
	$gender_arr[1]['name_search'] = $lang['gender_search']['2'];
	$gender_arr[1]['sel'] = ($data['gender_1'] == 2) ? 1 : 0;
	$gender_arr[1]['sel_search'] = ($data['gender_2'] == 2) ? 1 : 0;
	
	$smarty->assign('gender', $gender_arr);
	
	// age range select
	if (QUICK_SEARCH_AVAILABLE_AGE_RANGE) {
		$rs = $dbconn->Execute('SELECT MAX(date_birthday), MIN(date_birthday) FROM '.USERS_TABLE.' WHERE YEAR(date_birthday) > 1900');
		$max_age = AgeFromBDate($rs->fields[1]);
		$max_age = (int) min($max_age, (int) $settings['max_age_limit']);
		$min_age = AgeFromBDate($rs->fields[0]);
		$min_age = (int) max($min_age, (int) $settings['min_age_limit']);
	} else {
		$max_age = (int) $settings['max_age_limit'];
		$min_age = (int) $settings['min_age_limit'];
	}
	
	$smarty->assign('age_max', range($max_age, $min_age));
	$smarty->assign('age_min', range($min_age, $max_age));
	
	// search action and type
	$form['search_type'] = $search_type;
	$form['search_action'] = $file_name;

	$form['search_3_birthday']	= $file_name.'?sel=search_bd';
	$form['search_3_online']	= $file_name.'?sel=search_on';
	$form['search_3_hotlist']	= $file_name.'?sel=search_h';
	$form['search_3_new']		= $file_name.'?sel=search_new';
	
	//RS not in use
	$smarty->assign('err', $err);
	$smarty->assign('form', $form);
	$smarty->assign('data', $data);
	$smarty->assign('header', $lang['homepage']);
	$smarty->assign('header_s', $lang['search']);
	
	$smarty->display(TrimSlash($config['index_theme_path']).'/quick_search_form.tpl');
	exit;
}


function SearchTable($err = '', $sel = '')
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;
	
	$debug = false;
	
	if ($debug) echo '<font color="red">';
	if ($debug) echo '$sel='.$sel.'<br>';
	
	$id_user = $user[ AUTH_ID_USER ];
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$file_name = 'quick_search.php';
	
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
		'max_age_limit',
		'min_age_limit',
		'zip_count',
		'zip_letters',
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
	
	// coupled
	$is_coupled = $dbconn->getOne('SELECT couple FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
	$smarty->assign('is_coupled', $is_coupled);
	
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
	
	if ($use_session == 0)
	{
		// search initialization
		$strSQL = '';
		$gender_1 = $gender_2 = $couple_2 = $age_min = $age_max = $country = $region = $city = $foto = $online = $within = $distance = 0;
		$zipcode = $nick = $fname = $word = $tag = '';
		
		// get fresh search results
		$_SESSION['id_arr']			= array();
		$_SESSION['with_arr']		= array();
		$_SESSION['without_arr']	= array();
		$_SESSION['online_arr']		= array();
		$_SESSION['offline_arr']	= array();
		$_SESSION['search_pars']	= array();
				
		//vp adding privacy condition
		$usr_group = $dbconn->GetOne('SELECT id_group FROM '.USER_GROUP_TABLE.' WHERE id_user = ?', array($id_user));
		$privacy_where = sql_privacy_where($usr_group);
		
		switch ($sel)
		{
			case 'search':
				
				if (isset($_GET['par']) && ($_GET['par'] == 'back' || $_GET['par'] == 'send')) {
					// user clicked on back to list button in profile details or returns from sending a message
					$gender_1	= $_SESSION['search_pars']['gender_1'];
					$gender_2	= $_SESSION['search_pars']['gender_2'];
					$couple_2	= $_SESSION['search_pars']['couple_2'];
					$age_min	= $_SESSION['search_pars']['age_min'];
					$age_max	= $_SESSION['search_pars']['age_max'];
					$country	= $_SESSION['search_pars']['country'];
					$region		= $_SESSION['search_pars']['region'];
					$city		= $_SESSION['search_pars']['city'];
					$zipcode	= $_SESSION['search_pars']['zipcode'];
					$foto		= $_SESSION['search_pars']['foto'];
					$online		= $_SESSION['search_pars']['online'];
					$within		= $_SESSION['search_pars']['within'];
					$distance	= $_SESSION['search_pars']['distance'];
					$relation	= $_SESSION['search_pars']['relation'];
				} else {
					$gender_1	= isset($_REQUEST['gender_1'])		? intval($_REQUEST['gender_1']) : 0;
					$gender_2	= isset($_REQUEST['gender_2'])		? intval($_REQUEST['gender_2']) : 0;
					$couple_2	= isset($_REQUEST['couple_2'])		? intval($_REQUEST['couple_2']) : 0;
					$age_min	= isset($_REQUEST['age_min'])		? intval($_REQUEST['age_min']) : 0;
					$age_max	= isset($_REQUEST['age_max'])		? intval($_REQUEST['age_max']) : 0;
					$country	= isset($_REQUEST['country'])		? intval($_REQUEST['country']) : 0;
					$region		= isset($_REQUEST['region'])		? intval($_REQUEST['region']) : 0;
					$city		= isset($_REQUEST['city'])			? intval($_REQUEST['city']) : 0;
					$zipcode	= isset($_REQUEST['zipcode'])		? FormFilter($_REQUEST['zipcode']) : '';
					$foto		= isset($_REQUEST['foto_only'])		? intval($_REQUEST['foto_only']) : 0;
					$online		= isset($_REQUEST['online_only'])	? intval($_REQUEST['online_only']) : 0;
					$within		= isset($_REQUEST['within'])		? intval($_REQUEST['within']) : 0;
					$distance	= isset($_REQUEST['distance'])		? intval($_REQUEST['distance']) : 0;
				}
				
				// RS: not sure what this is good for, we don't do this is advanced search
				if (!isset($_REQUEST['relation'])) {
					if (isset($_SESSION['search_pars']['relation'])) {
						$relation = $_SESSION['search_pars']['relation'];
					} else {
						$relation = '';
					}
				} else {
					$relation = $_REQUEST['relation'];
				}
				
				// convert relation from querystring to array
				if (isset($relation) && !is_array($relation) && strlen($relation) >= 3) {
					$relation = explode(',', $relation);
				}
				
				// store search parameters in session
				$_SESSION['search_pars']['gender_1']	= $gender_1;
				$_SESSION['search_pars']['gender_2']	= $gender_2;
				$_SESSION['search_pars']['couple_2']	= $couple_2;
				$_SESSION['search_pars']['age_min']		= $age_min;
				$_SESSION['search_pars']['age_max']		= $age_max;
				$_SESSION['search_pars']['country']		= $country;
				$_SESSION['search_pars']['region']		= $region;
				$_SESSION['search_pars']['city']		= $city;
				$_SESSION['search_pars']['zipcode']		= $zipcode;
				$_SESSION['search_pars']['foto']		= $foto;
				$_SESSION['search_pars']['online']		= $online;
				$_SESSION['search_pars']['within']		= $within;
				$_SESSION['search_pars']['distance']	= $distance;
				$_SESSION['search_pars']['relation']	= $relation;
				
				if (!isset($gender_1) && !isset($gender_2) && !isset($age_min) && !isset($age_max) && (!isset($country) || !isset($zipcode)))
				{
					$smarty->assign('empty', '1');
				}
				elseif ($within == 1 && !intval($city))
				{
					$err = $lang['distance']['specify_city'];
					$smarty->assign('empty', '1');
				}
				else
				{
					// build where clause
					$foto_where = $zip_where = $relation_where = $online_where = '';
					
					// photo search
					if ($foto == 1) {
						$foto_where = ' AND u.icon_path <> "" ';
					}
					
					// online search
					if ($online) {
						$online_join = 'INNER JOIN';
						$online_where = ' AND up.hide_online = "0" ';
					} else {
						$online_join = 'LEFT JOIN';
						$online_where = '';
					}
					
					// relationship search
					$relation_str = '';
					
					if ($relation) {
						if (is_array($relation)) {
							$_arr = array();
							foreach ($relation as $value) {
								if (trim($value) != '') {
									$_arr[] = ' ((m.id_relationship LIKE "'.$value.'") OR (m.id_relationship LIKE "'.$value.',%") OR (m.id_relationship LIKE "%,'.$value.',%") OR (m.id_relationship LIKE "%,'.$value.'")) ';
								}
							}
							if (!empty($_arr)) {
								$relation_where  = ' AND (';
								$relation_where .= implode(' OR ', $_arr);
								$relation_where .= ' OR ((m.id_relationship LIKE "0") OR (m.id_relationship LIKE "0,%") OR (m.id_relationship LIKE "%,0,%") OR (m.id_relationship LIKE "%,0")) ';
								$relation_where .= ') ';
								$relation_str = implode(',', $relation);
							}
						} elseif (intval($relation) > 0) {
							$relation_where = ' AND (';
							$relation_where.= '((m.id_relationship LIKE "'.$relation.'") OR (m.id_relationship LIKE "'.$relation.',%") OR (m.id_relationship LIKE "%,'.$relation.',%") OR (m.id_relationship LIKE "%,'.$relation.'"))';
							$relation_where.= ' OR ((m.id_relationship LIKE "0") OR (m.id_relationship LIKE "0,%") OR (m.id_relationship LIKE "%,0,%") OR (m.id_relationship LIKE "%,0"))';
							$relation_where.= ') ';
							$relation_str = $relation;
						}
					}
					$_SESSION['search_pars']['relation_str'] = $relation_str;
					
					// location search
					$zip_where = '';
					
					$search_type = isset($_GET['search_type']) ? $_GET['search_type'] : '1';
					
					if ($search_type == '1')
					{
						if ($country) {
							if ($within == 1) {
								$city_arr = CityInRadius($city, $distance);
								if (!empty($city_arr)) {
									$zip_where = ' AND u.id_city IN (' . implode(',', $city_arr) . ') ';
								}
							} else {
								$zip_where = ' AND u.id_country = '.$country;
								if ($region) {
									$zip_where .= ' AND u.id_region = '.$region;
								}
								if ($city) {
									$zip_where .= ' AND u.id_city = '.$city;
								}
							}
						}
					}
					elseif ($search_type == '2')
					{
						if ($zipcode)
						{
							$country_arr = array();
							$region_arr = array();
							$city_arr = array();
							
							// select id_ countries and cities which have dem zipcodes (if searched user live in city but didnt write his zipcode only city)
							$rs = $dbconn->Execute('SELECT id_country, id_region, id FROM '.CITY_SPR_TABLE.' WHERE zip_code LIKE "%'.addslashes($zipcode).'%"');
							
							while (!$rs->EOF) {
								$row = $rs->GetRowAssoc(false);
								$country_arr[] = $row['id_country'];
								$region_arr[] = $row['id_region'];
								$city_arr[] = $row['id'];
								$rs->MoveNext();
							}
							
							if (!in_array($country, $country_arr)) {
								$country_arr[] = $country;
							}
							if (!in_array($region, $region_arr)) {
								$region_arr[] = $region;
							}
							if (!in_array($city, $city_arr)) {
								$city_arr[] = $city;
							}
							
							if (!empty($country_arr)) {
								$country_str = ' u.id_country IN (' . implode(',', $country_arr) . ')';
							}
							if (!empty($region_arr)) {
								$region_str = ' u.id_region IN (' . implode(',', $region_arr) . ')';
							}
							if (!empty($city_arr)) {
								$city_str = ' u.id_city IN (' . implode(',', $city_arr) . ')';
							}
							
							if (!isset($country_str) && !isset($city_str) && !isset($region_str)) {
								$zip_where = ' AND u.zipcode LIKE "'.$zipcode.'" ';
							} else {
								$_arr = array();
								if ($city_str) {
									$_arr[] = $city_str;
								}
								if ($region_str) {
									$_arr[] = $region_str;
								}
								if ($country_str) {
									$_arr[] = $country_str;
								}
								$zip_where = ' AND ((' . implode(' AND ', $_arr) . ') OR u.zipcode LIKE "'.$zipcode.'") ';
							}
						}
					}
					
					// age range search
					$date_start = date('Y-m-d', strtotime('-' . ($age_max + 1) . ' year +1 day'));
					$date_end = date('Y-m-d', strtotime('-' . $age_min . ' year'));
					
					$strSQL =
						'SELECT u.id, u.icon_path, s.id_user AS session, up.hide_online
						   FROM '.USERS_TABLE.' u
					 INNER JOIN '.USER_MATCH_TABLE.' m ON m.id_user = u.id
			   '.$online_join.' '.ACTIVE_SESSIONS_TABLE.' s ON s.id_user = u.id
					  LEFT JOIN '.USER_PRIVACY_SETTINGS.' AS up ON up.id_user = u.id
					 INNER JOIN '.USER_GROUP_TABLE.' ug ON u.id = ug.id_user
					 INNER JOIN '.GROUPS_TABLE.' g ON ug.id_group = g.id
						  WHERE u.gender = "'.$gender_2.'" AND m.gender = "'.$gender_1.'" AND u.couple = "'.$couple_2.'"
							AND DATE_FORMAT(u.date_birthday, "%Y-%m-%d") BETWEEN "'.$date_start.'" AND "'.$date_end.'"
							AND u.id <> "'.$id_user.'" '.$foto_where.' '.$zip_where.' '.$relation_where.' '.$online_where.' '.$privacy_where.'
							AND u.status = "1" AND u.visible = "1" AND u.root_user = "0" AND u.guest_user = "0"
							AND u.id NOT IN (SELECT id_user FROM '.USER_GROUP_TABLE.' WHERE id_group IN ('.INVISIBLE_GROUPS.'))
						  GROUP BY u.id
						  ORDER BY g.sort ASC, u.mm_platinum_applied DESC, u.id DESC';

					if ($debug) echo '>search: $strSQL='.$strSQL.'<br>';
				}
			
			break;
			
			case 'search_name':
				
				$nick = isset($_REQUEST['nick']) ? FormFilter($_REQUEST['nick']) : '';
				
				if (isset($_GET['par']) && ($_GET['par'] == 'back' || $_GET['par'] == 'send')) {
					$nick = $_SESSION['search_pars']['nick'];
				}
				
				$search_gender = ($user[ AUTH_GENDER ] == GENDER_MALE) ? GENDER_FEMALE : GENDER_MALE;
				
				if (!isset($nick) || $nick == '') {
					$smarty->assign('empty', '1');
				}
				else
				{
#					unset($_SESSION['id_arr'], $_SESSION['search_pars']);
					
					// search parameters and id array put in session
					$_SESSION['search_pars']['nick'] = $nick;
					
					$strSQL =
						'SELECT u.id, u.icon_path, s.id_user AS session, up.hide_online
						   FROM '.USERS_TABLE.' u
					  LEFT JOIN '.ACTIVE_SESSIONS_TABLE.' s ON u.id = s.id_user
					  LEFT JOIN '.USER_PRIVACY_SETTINGS.' AS up ON up.id_user = u.id
					 INNER JOIN '.USER_GROUP_TABLE.' ug ON u.id = ug.id_user
					 INNER JOIN '.GROUPS_TABLE.' g ON ug.id_group = g.id
						  WHERE LOWER(u.login) LIKE "%'.strtolower($nick).'%" AND u.gender = "'.$search_gender.'"
							AND u.status = "1" AND u.visible = "1" AND u.root_user = "0" AND u.guest_user = "0" AND u.id <> "'.$id_user.'" '.$privacy_where.'
							AND u.id NOT IN (SELECT id_user FROM '.USER_GROUP_TABLE.' WHERE id_group IN ('.INVISIBLE_GROUPS.'))
						  GROUP BY u.id
						  ORDER BY g.sort ASC, u.mm_platinum_applied DESC, u.id DESC';
				}
				
				if ($debug) echo 'search_name: $strSQL='.$strSQL.'<br>';
				
			break;
			
			case 'search_fname':
				
				$fname = isset($_REQUEST['fname']) ? FormFilter($_REQUEST['fname']) : '';
				
				if (isset($_GET['par']) && ($_GET['par'] == 'back' || $_GET['par'] == 'send')) {
					$fname = $_SESSION['search_pars']['fname'];
				}
				
				$search_gender = ($user[ AUTH_GENDER ] == GENDER_MALE) ? GENDER_FEMALE : GENDER_MALE;
				
				if (!isset($fname) || $fname == '') {
					$smarty->assign('empty', '1');
				}
				else
				{
					// search parameters and id array put in session
					$_SESSION['search_pars']['fname'] = $fname;
					
					$strSQL =
						'SELECT u.id, u.icon_path, s.id_user AS session, up.hide_online
						   FROM '.USERS_TABLE.' u
					  LEFT JOIN '.ACTIVE_SESSIONS_TABLE.' s ON u.id = s.id_user
					  LEFT JOIN '.USER_PRIVACY_SETTINGS.' AS up ON up.id_user = u.id
					 INNER JOIN '.USER_GROUP_TABLE.' ug ON u.id = ug.id_user
					 INNER JOIN '.GROUPS_TABLE.' g ON ug.id_group = g.id
						  WHERE LOWER(u.fname) LIKE "%'.strtolower($fname).'%" AND u.gender = "'.$search_gender.'"
							AND u.status = "1" AND u.visible = "1" AND u.root_user = "0" AND u.guest_user = "0" AND u.id <> "'.$id_user.'" '.$privacy_where.'
							AND u.id NOT IN (SELECT id_user FROM '.USER_GROUP_TABLE.' WHERE id_group IN ('.INVISIBLE_GROUPS.'))
					   GROUP BY u.id
					   ORDER BY g.sort ASC, u.mm_platinum_applied DESC, u.id DESC';
				}
				
				if ($debug) echo 'search_fname: $strSQL='.$strSQL.'<br>';
				
			break;
			
			case 'search_on':
				
				$strSQL =
					'SELECT u.id, u.icon_path, s.id_user AS session, up.hide_online
					   FROM '.USERS_TABLE.' u
				 INNER JOIN '.ACTIVE_SESSIONS_TABLE.' s ON u.id = s.id_user
				  LEFT JOIN '.USER_PRIVACY_SETTINGS.' AS up ON up.id_user = u.id
				 INNER JOIN '.USER_GROUP_TABLE.' ug ON u.id = ug.id_user
				 INNER JOIN '.GROUPS_TABLE.' g ON ug.id_group = g.id
					  WHERE up.hide_online = "0"
						AND u.status = "1" AND u.visible = "1" AND u.root_user = "0" AND u.guest_user = "0" AND u.id <> "'.$id_user.'" '.$privacy_where.'
						AND u.id NOT IN (SELECT id_user FROM '.USER_GROUP_TABLE.' WHERE id_group IN ('.INVISIBLE_GROUPS.'))
					  GROUP BY u.id
					  ORDER BY g.sort ASC, u.mm_platinum_applied DESC, u.id DESC';
				
				if ($debug) echo 'search_on: $strSQL='.$strSQL.'<br>';
				
			break;
			
			case 'search_bd':
				
				$strSQL =
					'SELECT u.id, u.icon_path, s.id_user AS session, up.hide_online
					   FROM '.USERS_TABLE.' u
				  LEFT JOIN '.ACTIVE_SESSIONS_TABLE.' s ON u.id = s.id_user
				  LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id
				 INNER JOIN '.USER_GROUP_TABLE.' ug ON u.id = ug.id_user
				 INNER JOIN '.GROUPS_TABLE.' g ON ug.id_group = g.id
					  WHERE u.date_birthday LIKE "%-'.date('m').'-'.date('d').'%" 
						AND u.status = "1" AND u.visible = "1" AND u.root_user = "0" AND u.guest_user = "0" AND u.id <> "'.$id_user.'" '.$privacy_where.'
						AND u.id NOT IN (SELECT id_user FROM '.USER_GROUP_TABLE.' WHERE id_group IN ('.INVISIBLE_GROUPS.'))
					  GROUP BY u.id
					  ORDER BY g.sort ASC, u.mm_platinum_applied DESC, u.id DESC';
				
				if ($debug) echo 'search_bd: $strSQL='.$strSQL.'<br>';
				
			break;
			
			case 'search_new':
				
				$strSQL =
					'SELECT u.id, u.icon_path, s.id_user AS session, up.hide_online
					   FROM '.USERS_TABLE.' u
				  LEFT JOIN '.ACTIVE_SESSIONS_TABLE.' s ON u.id = s.id_user
				  LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id
				 INNER JOIN '.USER_GROUP_TABLE.' ug ON u.id = ug.id_user
				 INNER JOIN '.GROUPS_TABLE.' g ON ug.id_group = g.id
					  WHERE u.date_registration > (NOW() - INTERVAL 7 DAY) 
						AND u.status = "1" AND u.visible = "1" AND u.root_user = "0" AND u.guest_user = "0" AND u.id <> "'.$id_user.'" '.$privacy_where.'
						AND u.id NOT IN (SELECT id_user FROM '.USER_GROUP_TABLE.' WHERE id_group IN ('.INVISIBLE_GROUPS.'))
					  GROUP BY u.id
					  ORDER BY g.sort ASC, u.mm_platinum_applied DESC, u.id DESC';
				
				if ($debug) echo 'search_new: $strSQL='.$strSQL.'<br>';
				
			break;
			
			case 'search_h':
				
				$strSQL =
					'SELECT u.id, u.icon_path, s.id_user AS session, up.hide_online
					   FROM '.USERS_TABLE.' u
				 INNER JOIN '.HOTLIST_TABLE.' h ON u.id = h.id_user
				  LEFT JOIN '.ACTIVE_SESSIONS_TABLE.' s ON u.id = s.id_user
				  LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id
				 INNER JOIN '.USER_GROUP_TABLE.' ug ON u.id = ug.id_user
				 INNER JOIN '.GROUPS_TABLE.' g ON ug.id_group = g.id
					  WHERE h.id_friend = "'.$id_user.'" 
						AND u.status = "1" AND u.visible = "1" AND u.root_user = "0" AND u.guest_user = "0" AND u.id <> "'.$id_user.'" '.$privacy_where.'
						AND u.id NOT IN (SELECT id_user FROM '.USER_GROUP_TABLE.' WHERE id_group IN ('.INVISIBLE_GROUPS.'))
					  GROUP BY u.id
					  ORDER BY g.sort ASC, u.mm_platinum_applied DESC, u.id DESC';
				
				if ($debug) echo 'search_h: $strSQL='.$strSQL.'<br>';
				
			break;
			
			case 'search_top':
				
				$strSQL =
					'SELECT u.id, u.icon_path, s.id_user AS session, up.hide_online
					   FROM '.USERS_TABLE.' u
				  LEFT JOIN '.USER_TOPTEN_TABLE.' t ON u.id = t.id_user
				  LEFT JOIN '.ACTIVE_SESSIONS_TABLE.' s ON u.id = s.id_user
				  LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id
					  WHERE t.id > 0
						AND u.status = "1" AND u.visible = "1" AND u.root_user = "0" AND u.guest_user = "0" '.$privacy_where.'
						AND u.id NOT IN (SELECT id_user FROM '.USER_GROUP_TABLE.' WHERE id_group IN ('.INVISIBLE_GROUPS.'))
					  GROUP BY u.id
					  ORDER BY t.rating DESC';
				
				if ($debug) echo 'search_top: $strSQL='.$strSQL.'<br>';
				
			break;
			
			case 'search_keyword':
				
				$word = isset($_REQUEST['word']) ? FormFilter($_REQUEST['word']) : '';
				
				if (isset($_GET['par']) && ($_GET['par'] == 'back' || $_GET['par'] == 'send')) {
					$word = $_SESSION['search_pars']['word'];
				}
				
				if (!isset($word) || $word === '')
				{
					$smarty->assign('empty', '1');
				}
				else
				{
					// put search parameters and id array in session
					$_SESSION['search_pars']['word'] = $word;
					
					$id_arr = array();
					$or_substr = '';
					
					$rs = $dbconn->Execute('SELECT id FROM '.LANGUAGE_TABLE);
					$id_langs = array();
					
					while (!$rs->EOF) {
						$id_langs[] = 'a.lang_'.$rs->fields[0].' LIKE "%'.ucfirst(strtolower($word)).'%"';
						$rs->MoveNext();
					}
					
					$like_substr = join(' OR ', $id_langs);
					
					// personality
					$strSQL =
						'SELECT b.id_user AS id
						   FROM '.REFERENCE_LANG_TABLE.' a
					 INNER JOIN '.PERSON_SPR_USER_TABLE.' b ON a.id_reference = b.id_value
						  WHERE a.table_key = 7 AND  ('.$like_substr.')';
					$rs = $dbconn->Execute($strSQL);
					
					while (!$rs->EOF) {
						$id_arr[] = $rs->fields[0];
						$rs->MoveNext();
					}
					
					// portrait
					$strSQL =
						'SELECT b.id_user AS id
						   FROM '.REFERENCE_LANG_TABLE.' a
					 INNER JOIN '.PORTRAIT_SPR_USER_TABLE.' b ON a.id_reference = b.id_value
						  WHERE a.table_key = 9 AND ('.$like_substr.')';
					$rs = $dbconn->Execute($strSQL);
					
					while (!$rs->EOF) {
						$id_arr[] = $rs->fields[0];
						$rs->MoveNext();
					}
					
					// interests
					$strSQL =
						'SELECT b.id_user AS id
						   FROM '.REFERENCE_LANG_TABLE.' a
					 INNER JOIN '.INTERESTS_SPR_USER_TABLE.' b ON a.id_reference = b.id_spr
						  WHERE a.table_key = 3 AND b.id_value <> "" AND ('.$like_substr.')';
					$rs = $dbconn->Execute($strSQL);
					
					while (!$rs->EOF) {
						$id_arr[] = $rs->fields[0];
						$rs->MoveNext();
					}
					
					// join user ids
					if (!empty($id_arr)) {
						$or_substr = ' OR u.id IN (' . implode(',', $id_arr) . ') ';
					}
					
					$strSQL =
						'SELECT u.id, u.icon_path, s.id_user AS session, up.hide_online
						   FROM '.USERS_TABLE.' u
					  LEFT JOIN '.USER_UPLOAD_TABLE.' l ON u.id = l.id_user
					  LEFT JOIN '.ACTIVE_SESSIONS_TABLE.' s ON u.id = s.id_user
					  LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id
					 INNER JOIN '.USER_GROUP_TABLE.' ug ON u.id = ug.id_user
					 INNER JOIN '.GROUPS_TABLE.' g ON ug.id_group = g.id
						  WHERE (u.comment LIKE "%'.$word.'%" OR u.headline LIKE "%'.$word.'%" OR u.about_me LIKE "%'.$word.'%"
								 OR u.what_i_do LIKE "%'.$word.'%" OR u.my_idea LIKE "%'.$word.'%" OR u.hoping_to_find LIKE "%'.$word.'%" '.$or_substr.')
							AND u.status = "1" AND u.visible = "1" AND u.root_user = "0" AND u.guest_user = "0" AND u.id <> "'.$id_user.'" '.$privacy_where.'
							AND u.id NOT IN (SELECT id_user FROM '.USER_GROUP_TABLE.' WHERE id_group IN ('.INVISIBLE_GROUPS.'))
						  GROUP BY u.id
						  ORDER BY g.sort ASC, u.mm_platinum_applied DESC, u.id DESC';
				}
				
				if ($debug) echo 'search_keyword: $strSQL='.$strSQL.'<br>';
				
			break;
			
			case 'search_tag':
				
				if (isset($_GET['par']) && ($_GET['par'] == 'back' || $_GET['par'] == 'send')) {
					$tag = $_SESSION['search_pars']['tag'];
				} else {
					$tag = isset($_REQUEST['tag']) ? FormFilter($_REQUEST['tag']) : '';
				}
				
				if (trim($tag) == '')
				{
					$smarty->assign('empty', '1');
				}
				else
				{
					// put search parameters and id array in session
					$_SESSION['search_pars']['tag'] = $tag;
					
					$strSQL =
						'SELECT u.id, u.icon_path, s.id_user AS session, up.hide_online
						   FROM '.TAGS_TABLE.' t
					 INNER JOIN '.USERS_TABLE.' u ON t.id_user = u.id
					  LEFT JOIN '.ACTIVE_SESSIONS_TABLE.' s ON u.id = s.id_user
					  LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id
					 INNER JOIN '.USER_GROUP_TABLE.' ug ON u.id = ug.id_user
					 INNER JOIN '.GROUPS_TABLE.' g ON ug.id_group = g.id
						  WHERE t.tag = "'.trim($tag).'" 
							AND u.status = "1" AND u.visible = "1" AND u.root_user = "0" AND u.guest_user = "0" AND u.id <> "'.$id_user.'" '.$privacy_where.'
							AND u.id NOT IN (SELECT id_user FROM '.USER_GROUP_TABLE.' WHERE id_group IN ('.INVISIBLE_GROUPS.'))
						  GROUP BY u.id
						  ORDER BY g.sort ASC, u.mm_platinum_applied DESC, u.id DESC';
				}
				
				if ($debug) echo 'search_tag: $strSQL='.$strSQL.'<br>';
				
			break;
			
			case 'search_all':
			
				$strSQL =
					'SELECT u.id, u.icon_path, s.id_user AS session, up.hide_online
					   FROM '.USERS_TABLE.' u
				  LEFT JOIN '.ACTIVE_SESSIONS_TABLE.' s ON u.id = s.id_user
				  LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id
				 INNER JOIN '.USER_GROUP_TABLE.' ug ON u.id = ug.id_user
				 INNER JOIN '.GROUPS_TABLE.' g ON ug.id_group = g.id
					  WHERE u.status = "1" AND u.visible = "1" AND u.root_user = "0" AND u.guest_user = "0" AND u.id <> "'.$id_user.'" '.$privacy_where.'
						AND u.id NOT IN (SELECT id_user FROM '.USER_GROUP_TABLE.' WHERE id_group IN ('.INVISIBLE_GROUPS.'))
					  GROUP BY u.id
					  ORDER BY g.sort ASC, u.mm_platinum_applied DESC, u.id DESC';
							
				if ($debug) echo 'search_all: $strSQL='.$strSQL.'<br>';
				
			break;
			
			case 'search_referred':
			
				$strSQL =
					'SELECT u.id, u.icon_path, s.id_user AS session, up.hide_online
					   FROM '.USERS_TABLE.' u
				  LEFT JOIN '.ACTIVE_SESSIONS_TABLE.' s ON u.id = s.id_user
				  LEFT JOIN '.USER_REFER_TABLE.' r ON r.id_user = u.id
				  LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id
				 INNER JOIN '.USER_GROUP_TABLE.' ug ON u.id = ug.id_user
				 INNER JOIN '.GROUPS_TABLE.' g ON ug.id_group = g.id
					  WHERE r.id_refer = "'.$id_user.'"
						AND u.status = "1" AND u.visible = "1" AND u.root_user = "0" AND u.guest_user = "0" AND u.id <> "'.$id_user.'" '.$privacy_where.'
						AND u.id NOT IN (SELECT id_user FROM '.USER_GROUP_TABLE.' WHERE id_group IN ('.INVISIBLE_GROUPS.'))
					  GROUP BY u.id
					  ORDER BY g.sort ASC, u.mm_platinum_applied DESC, u.id DESC';
				
				if ($debug) echo 'search_referred: $strSQL='.$strSQL.'<br>';
				
			break;
		}
		
		// execute search
		if ($strSQL != '') {
			$rs = $dbconn->Execute($strSQL);
			
			if ($debug) echo '<hr>'.print_r($row, true);
			
			while (!$rs->EOF) {
				$row = $rs->GetRowAssoc(false);
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
	} // if ($use_session == 0)
	else
	{
		// user clicked on back to list button in profile details or returns from sending a message
		$gender_1		= isset($_SESSION['search_pars']['gender_1']) ? $_SESSION['search_pars']['gender_1'] : '';
		$gender_2		= isset($_SESSION['search_pars']['gender_2']) ? $_SESSION['search_pars']['gender_2'] : '';
		$couple_2		= isset($_SESSION['search_pars']['couple_2']) ? $_SESSION['search_pars']['couple_2'] : '';
		$age_min		= isset($_SESSION['search_pars']['age_min']) ? $_SESSION['search_pars']['age_min'] : '';
		$age_max		= isset($_SESSION['search_pars']['age_max']) ? $_SESSION['search_pars']['age_max'] : '';
		$country		= isset($_SESSION['search_pars']['country']) ? $_SESSION['search_pars']['country'] : '';
		$region			= isset($_SESSION['search_pars']['region']) ? $_SESSION['search_pars']['region'] : '';
		$city			= isset($_SESSION['search_pars']['city']) ? $_SESSION['search_pars']['city'] : '';
		$zipcode		= isset($_SESSION['search_pars']['zipcode']) ? $_SESSION['search_pars']['zipcode'] : '';
		$foto			= isset($_SESSION['search_pars']['foto']) ? $_SESSION['search_pars']['foto'] : '';
		$online			= isset($_SESSION['search_pars']['online']) ? $_SESSION['search_pars']['online'] : '';
		$within			= isset($_SESSION['search_pars']['within']) ? $_SESSION['search_pars']['within'] : '';
		$distance		= isset($_SESSION['search_pars']['distance']) ? $_SESSION['search_pars']['distance'] : '';
		$relation		= isset($_SESSION['search_pars']['relation']) ? $_SESSION['search_pars']['relation'] : '';
		$relation_str	= isset($_SESSION['search_pars']['relation_str']) ? $_SESSION['search_pars']['relation_str'] : '';
		$nick			= isset($_SESSION['search_pars']['nick']) ? $_SESSION['search_pars']['nick'] : '';
		$word			= isset($_SESSION['search_pars']['word']) ? $_SESSION['search_pars']['word'] : '';
		$tag			= isset($_SESSION['search_pars']['tag']) ? $_SESSION['search_pars']['tag'] : '';
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
		
		//VP Order Clause
		if ($sel != 'search_top') {
			$order_clause = ' ORDER BY g.sort ASC, u.mm_platinum_applied DESC, u.id DESC';
		} else {
			$order_clause = '';
		}
		
		// RS : we can use the same query for all searches. original queries which often do not use $_SESSION['id_arr']
		// are disabled by commenting. Using $_SESSION['id_arr'] can become uncomfortable when the database grows,
		// it might then be better to either run 2 queries with complete where clauses (one for counting and one for
		// fetching the records), or use SQL_CALC_FOUND_ROWS and one query only. but for now let's use $_SESSION['id_arr']
		// as this is the original intention on the Pilot Group application.
		
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
					'.$order_clause.'
			  LIMIT '.$limit_offset.', '.$limit_length;
		
		switch ($sel)
		{
			case 'search':
				// query moved up, before switch statement
				$search_type = 'q';
			break;
			
			case 'search_name':
				/*
				$strSQL =
					'SELECT distinct u.id, u.fname, u.platinum_verified, ug.id_group, u.mm_platinum_applied, u.phone, 
							u.gender, u.date_birthday, u.icon_path, u.id_country, u.id_city, u.id_region, SUBSTRING(u.comment, 1, 165) AS comment, 
							DATE_FORMAT(u.date_last_seen,"'.$config['date_format'].'") AS date_last_login
					   FROM '.USERS_TABLE.' u
				  LEFT JOIN '.USER_GROUP_TABLE.' ug on u.id=ug.id_user
				  LEFT JOIN '.GROUPS_TABLE.' g on ug.id_group=g.id
					  WHERE LOWER(u.login) LIKE "%'.strtolower($nick).'%" and u.status = "1" and u.visible = "1" and u.root_user = "0" 
						AND u.guest_user = "0" and u.id != "'.$id_user.'"
							'.$order_clause.'
					  LIMIT '.$limit_offset.', '.$limit_length;
				*/
				$search_type = 'q_name';
			break;
			
			case 'search_fname':
				$search_type = 'q_fname';
			break;
			
			case 'search_on':
				/*VP
				$strSQL =
					'SELECT distinct a.id, a.fname, a.platinum_verified, g.id_group, a.mm_platinum_applied, a.phone, a.gender, a.date_birthday,
							a.icon_path, a.id_country, a.id_city, a.id_region, SUBSTRING(a.comment,1, 165) as comment,
							DATE_FORMAT(a.date_last_seen,"'.$config['date_format'].'")  as date_last_login, ea.id_user as session
					   FROM '.USERS_TABLE.' a
				  LEFT JOIN '.USER_GROUP_TABLE.' g on a.id=g.id_user
				  LEFT JOIN '.GROUPS_TABLE.' s on g.id_group=s.id
				  LEFT JOIN '.ACTIVE_SESSIONS_TABLE.' ea on a.id=ea.id_user
					  WHERE a.status="1" and a.visible="1" and a.root_user = "0" and a.guest_user="0" and a.id != "'.$user[ AUTH_ID_USER ].'"
							'.$order_clause.'
					  LIMIT '.$limit_offset.', '.$limit_length;
				*/
				/* RS: this new query by VP is not correct, as it does not select all necessary fiels
				$strSQL = 
					'SELECT DISTINCT a.id, a.icon_path, e.id_user AS session
					FROM '.USERS_TABLE.' a
					INNER JOIN '.ACTIVE_SESSIONS_TABLE.' e ON a.id=e.id_user
					LEFT JOIN '.USER_PRIVACY_SETTINGS.' AS up ON up.id_user=a.id
					WHERE up.hide_online="0" AND a.status="1" AND a.visible="1" AND a.root_user="0" AND a.guest_user="0" AND a.id != '.$user[ AUTH_ID_USER ].'
					AND u.id NOT IN (SELECT id_user FROM '.USER_GROUP_TABLE.' WHERE id_group IN ('.INVISIBLE_GROUPS.'))
							'.$order_clause.'
					  LIMIT '.$limit_offset.', '.$limit_length;
					*/
				$search_type = 'q_on';
			break;
			
			case 'search_bd':
				/*
				$strSQL =
					'SELECT DISTINCT a.id, a.fname, a.platinum_verified, g.id_group, a.mm_platinum_applied, a.phone, a.gender, a.date_birthday,
							a.icon_path, a.id_country, a.id_city, a.id_region, SUBSTRING(a.comment,1, 165) as comment,
							DATE_FORMAT(a.date_last_seen,"'.$config['date_format'].'") AS date_last_login
					   FROM '.USERS_TABLE.' a
				  LEFT JOIN '.USER_GROUP_TABLE.' g on a.id=g.id_user
				  LEFT JOIN '.GROUPS_TABLE.' s on g.id_group=s.id
					  WHERE a.date_birthday like "%-'.date('m').'-'.date('d').'%"
						AND a.status="1" and a.visible="1" and a.root_user = "0" and a.guest_user="0" and a.id != "'.$id_user.'"
							'.$order_clause.'
					  LIMIT '.$limit_offset.', '.$limit_length;
				*/
				$search_type = 'q_bd';
			break;
			
			case 'search_new':
				/*
				$strSQL =
					'SELECT DISTINCT a.id, a.fname, a.platinum_verified, g.id_group, a.mm_platinum_applied, a.phone, a.gender, a.date_birthday,
							a.icon_path, a.id_country, a.id_city, a.id_region, SUBSTRING(a.comment,1, 165) as comment,
							DATE_FORMAT(a.date_last_seen,"'.$config['date_format'].'") as date_last_login
					   FROM '.USERS_TABLE.' a
				  LEFT JOIN '.USER_GROUP_TABLE.' g on a.id=g.id_user
				  LEFT JOIN '.GROUPS_TABLE.' s on g.id_group=a.id
					  WHERE a.date_registration > (now() - interval 7 day)
						AND a.status="1" and a.visible="1" and a.root_user = "0" and a.guest_user="0" and a.id != "'.$user[ AUTH_ID_USER ].'"
							'.$order_clause.'
					  LIMIT '.$limit_offset.', '.$limit_length;
				*/
				$search_type = 'q_new';
			break;
			
			case 'search_h':
				/*
				$strSQL =
					'SELECT DISTINCT a.id, a.fname, a.platinum_verified, g.id_group, a.mm_platinum_applied, a.phone, a.gender, a.date_birthday,
							a.icon_path, a.id_country, a.id_city, a.id_region, SUBSTRING(a.comment,1, 165) as comment,
							DATE_FORMAT(a.date_last_seen,"'.$config['date_format'].'") as date_last_login
					   FROM '.USERS_TABLE.' a
				  LEFT JOIN '.HOTLIST_TABLE.' h on h.id_user=a.id
				  LEFT JOIN '.USER_GROUP_TABLE.' g on a.id=g.id_user
				  LEFT JOIN '.GROUPS_TABLE.' s on g.id_group=s.id
					  WHERE h.id_friend="'.$id_user.'"
						AND a.status="1" and a.visible="1" and a.root_user = "0" and a.guest_user="0" and a.id != "'.$id_user.'"';
				*/
				$search_type = 'q_h';
			break;
			
			case 'search_top':
				/*
				$strSQL =
					'SELECT DISTINCT a.id, a.fname, a.platinum_verified, g.id_group, a.mm_platinum_applied, a.phone, a.gender, a.date_birthday,
							a.icon_path, a.id_country, a.id_city, a.id_region, SUBSTRING(a.comment,1, 165) as comment,
							DATE_FORMAT(a.date_last_seen,"'.$config['date_format'].'")  as date_last_login
					   FROM '.USERS_TABLE.' a
				  LEFT JOIN '.USER_TOPTEN_TABLE.' e on a.id=e.id_user
				  LEFT JOIN '.USER_GROUP_TABLE.' g on a.id=g.id_user
				  LEFT JOIN '.GROUPS_TABLE.' s on g.id_group=s.id
					  WHERE (e.id>0) AND a.status="1" and a.visible="1" and a.root_user = "0" and a.guest_user="0"
					  GROUP BY a.id
							'.$order_clause.'
					  LIMIT '.$limit_offset.', '.$limit_length;
				*/
				$search_type = 'q_top';
			break;
			
			case 'search_keyword':
				/*
				$strSQL =
					'SELECT DISTINCT a.id, a.fname, a.platinum_verified, g.id_group, a.mm_platinum_applied, a.phone, a.gender, a.date_birthday,
							a.icon_path, a.id_country, a.id_city, a.id_region, SUBSTRING(a.comment,1, 165) as comment,
							DATE_FORMAT(a.date_last_seen,"'.$config['date_format'].'")  as date_last_login
					   FROM '.USERS_TABLE.' a
				  LEFT JOIN '.USER_GROUP_TABLE.' g on a.id=g.id_user
				  LEFT JOIN '.GROUPS_TABLE.' s on g.id_group=s.id
					  WHERE a.id IN (' . implode(',', $id_arr) . ')
							'.$order_clause.'
					  LIMIT '.$limit_offset.', '.$limit_length;
				*/
				$search_type = 'q_keyword';
			break;
			
			case 'search_tag':
				/*
				$strSQL =
					'SELECT DISTINCT a.id, a.fname, a.platinum_verified, g.id_group, a.mm_platinum_applied, a.phone, a.gender, a.date_birthday,
							a.icon_path, a.id_country, a.id_city, a.id_region, SUBSTRING(a.comment,1, 165) as comment,
							DATE_FORMAT(a.date_last_seen,"'.$config['date_format'].'") as date_last_login
					   FROM '.USERS_TABLE.' a
				  LEFT JOIN '.USER_GROUP_TABLE.' g on a.id=g.id_user
				  LEFT JOIN '.GROUPS_TABLE.' s on g.id_group=s.id
					  WHERE a.id IN (' . implode(',', $id_arr) . ')
							'.$order_clause.'
					  LIMIT '.$limit_offset.', '.$limit_length;
				*/
				$search_type = 'q_tag';
			break;
			
			case 'search_all':
				/*
				$strSQL =
					'SELECT DISTINCT a.id, a.fname, a.platinum_verified, g.id_group, a.mm_platinum_applied, a.phone, a.gender, a.date_birthday,
							a.icon_path, a.id_country, a.id_city, a.id_region, SUBSTRING(a.comment,1, 165) as comment,
							DATE_FORMAT(a.date_last_seen,"'.$config['date_format'].'") as date_last_login
					   FROM '.USERS_TABLE.' a
				  LEFT JOIN '.USER_GROUP_TABLE.' g on a.id=g.id_user
				  LEFT JOIN '.GROUPS_TABLE.' s on g.id_group=s.id
					  WHERE a.status="1" and a.visible="1" and a.root_user = "0" and a.guest_user="0" and a.id != "'.$user[ AUTH_ID_USER ].'"
							'.$order_clause.'
					  LIMIT '.$limit_offset.', '.$limit_length;
				*/
				$search_type = 'q_all';
			break;
			
			case 'search_referred':
				/*
				$strSQL =
					'SELECT DISTINCT a.id, a.fname, a.platinum_verified, g.id_group, a.mm_platinum_applied, a.phone, a.gender, a.date_birthday,
							a.icon_path, a.id_country, a.id_city, a.id_region, SUBSTRING(a.comment,1, 165) as comment,
							DATE_FORMAT(a.date_last_seen,"'.$config['date_format'].'")  as date_last_login
					   FROM '.USERS_TABLE.' a
				  LEFT JOIN '.USER_GROUP_TABLE.' g on a.id=g.id_user
				  LEFT JOIN '.GROUPS_TABLE.' s on g.id_group=s.id
				  LEFT JOIN '.USER_REFER_TABLE.' urt ON urt.id_user=a.id
					  WHERE urt.id_user=a.id AND urt.id_refer="'.$user[ AUTH_ID_USER ].'"
							'.$order_clause.'
					  LIMIT '.$limit_offset.', '.$limit_length;
				*/
			break;
		}
		
		if ($debug) echo '$strSQL='.$strSQL.'<br>';
		
		$rs = $dbconn->Execute($strSQL);
		
		$search = array();
		$_LANG_NEED_ID = array();
		$i = 0;
		
		while (!$rs->EOF)
		{
			$row = $rs->GetRowAssoc(false);
			
			if ($debug) echo '<hr>'.print_r($row, true);
			
			$search[$i]['id']			= (int) $row['id'];
			$search[$i]['number']		= ($_SESSION['search_page'] - 1) * $_SESSION['per_page_rec'] + $i + 1;
			$search[$i]['name']			= stripslashes($row['fname']);
			$search[$i]['gender']		= (int) $row['gender'];
#			$search[$i]['phone']		= stripslashes($row['phone']);
			$search[$i]['age']			= AgeFromBDate($row['date_birthday']);
			$search[$i]['id_country']	= (int) $row['id_country'];
			$search[$i]['id_region']	= (int) $row['id_region'];
			$search[$i]['id_city']		= (int) $row['id_city'];
			$search[$i]['is_me']		= (isset($id_user) && $row['id'] == $id_user) ? 1 : 0;	// only possible in top users list
			
			//SH is_verified
			$search[$i]['is_verified'] = !empty($row['platinum_verified']);
			
			// get online status
			$search[$i]['status'] = (getUserIsOnline($row['id']) && !$row['hide_online']) ? $lang['status']['on'] : $lang['status']['off'];
			
			// language
			$_LANG_NEED_ID['country'][]	= (int) $row['id_country'];
			$_LANG_NEED_ID['region'][]	= (int) $row['id_region'];
			$_LANG_NEED_ID['city'][]	= (int) $row['id_city'];
			
			// icon path
			$icon_path = $row['icon_path'] ? $row['icon_path'] : $default_photos[$row['gender']];
			
			if ($icon_path && file_exists($config['site_path'].$settings['icons_folder'].'/'.$icon_path)) {
				$search[$i]['icon_path'] = $config['site_root'].$settings['icons_folder'].'/'.$icon_path;
				// SH big icon image fetched
				$search[$i]['big_icon_path'] = $config['site_root'].$settings['icons_folder'].'/big_'.$icon_path;
			}
			
			// photo count
			$search[$i]['photo_count'] = publicPhotoCountAlbums($row['id']);
			if ($row['icon_path']) {
				$search[$i]['photo_count']++;
			}
			
			// links
			$search[$i]['profile_link'] =	$file_name.'?sel=viewprofile&amp;id='.$row['id'].'&amp;par='.$sel;
			
			if ($view != 'gallery')
			{
#				$search[$i]['completion']	= $percent->GetAllPercentForUser($row['id']);
				$search[$i]['annonce']		= stripslashes($row['comment']);
				$search[$i]['last_login']	= $row['date_last_login'];
				
				// get groups
				$search[$i]['group'] = getUserGroups($row['id'], $row['mm_platinum_applied']);
				
				// get user search params
				$search[$i]['age_max']			= (int) $row['age_max'];
				$search[$i]['age_min']			= (int) $row['age_min'];
				$search[$i]['gender_search']	= $lang['gender_search'][$row['gender_search']];
				
				// check hotlist
				$search[$i]['hotlisted'] = isInMyHotlist($id_user, $row['id']);
				
				// check blacklist
				$search[$i]['blacklisted'] = isInMyBlacklist($id_user, $row['id']);
				
				// check connected
				$search[$i]['connected_status'] = getConnectedStatus($id_user, $row['id']);
				
				// links
				$search[$i]['email_link']		= 'mailbox.php?sel=fs&amp;id='.$row['id'].'&amp;search_type='.$search_type;
				$search[$i]['sendfriend_link']	= 'send_friend.php?sel=send&amp;id_user='.$row['id'];
				$search[$i]['gift_link']		= $file_name.'?sel=sendgift&amp;id='.$row['id'].'&amp;par='.$sel;
				
				
				
				//$search[$i]['ecard_link']		= 'ecards.php?id_user_to='.$row['id'].'&amp;fixuser=Y';
				$search[$i]['ecard_link']		= $file_name.'?sel=sendecard&amp;id='.$row['id'].'&amp;par='.$sel;
				
				// kiss link
				if ($settings['use_kiss_types']) {
					$search[$i]['kiss_link'] = 'send_kiss.php?sel=send&amp;id_user='.$row['id'];
				} else {
					$search[$i]['kiss_link'] = $file_name.'?sel=kiss&amp;id='.$row['id'].'&amp;par='.$sel;
				}
				
				// hotlist link
				if ($search[$i]['hotlisted'] == 0 && $search[$i]['blacklisted'] == 0) {
					if ($settings['use_friend_types']) {
						$search[$i]['add_hotlist_link'] = 'hotlist.php?sel=addform&amp;id='.$row['id'];
					} else {
						$search[$i]['add_hotlist_link'] = $file_name.'?sel=addhotlist&amp;id='.$row['id'].'&amp;par='.$sel;
					}
				}
				
				// blacklist link
				if ($search[$i]['hotlisted'] == 0 && $search[$i]['connected_status'] != CS_CONNECTED && $search[$i]['blacklisted'] == 0) {
					$search[$i]['add_blacklist_link'] = $file_name.'?sel=addblacklist&amp;id='.$row['id'].'&amp;par='.$sel;
				}
				
				// connections link
				if ($search[$i]['blacklisted'] == 0 && $search[$i]['connected_status'] == CS_NOTHING) {
					if ($settings['use_friend_types']) {
						$search[$i]['add_connection_link'] = 'connections.php?sel=addform&amp;id='.$row['id'];
					} else {
						$search[$i]['add_connection_link'] = $file_name.'?sel=addconnection&amp;id='.$row['id'].'&amp;par='.$sel;
					}
				}
				
				// make couple link
				if (QUICK_SEARCH_COUPLE) {
					if ($is_coupled != 1) {
						$search[$i]['be_couple_link'] = $file_name.'?sel=be_couple&amp;id='.$row['id'].'&amp;par='.$sel;
					}
				}
				
				// voip link
				if ($config['voipcall_feature']) {
					$search[$i]['call_link'] = 'voip_call.php?sel=rate&amp;id_user='.$search[$i]['id'];
				}
			}
			
			$rs->MoveNext();
			$i++;
		}
		
		$smarty->assign('search_res', $search);
		
		// page count
		$form['pages_count'] = ceil($num_records / $_SESSION['per_page_rec']);
		
		// paging links
		if (!isset($relation_str)) {
			$relation_str = '';
		}

		$param = $file_name.'?sel='.$sel.'&amp;gender_1='.$gender_1.'&amp;gender_2='.$gender_2.'&amp;couple_2='.$couple_2.
					'&amp;country='.$country.'&amp;region='.$region.'&amp;city='.$city.'&amp;age_min='.$age_min.'&amp;age_max='.$age_max.
					'&amp;foto_only='.$foto.'&amp;nick='.$nick.'&amp;filter='.$filter.'&amp;view='.$view.
					'&amp;word='.urlencode($word).'&amp;tag='.$tag.'&amp;zipcode='.$zipcode.'&amp;relation='.urlencode($relation_str).
					'&amp;within='.$within.'&amp;distance='.$distance.'&amp;';
		$smarty->assign('links', GetLinkArray($num_records, $_SESSION['search_page'], $param, $_SESSION['per_page_rec']));
		
		//VP records per page links
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
	
	$form['view_online_link']			= $file_name.'?sel='.$sel.'&amp;page=1&amp;gender_1='.$gender_1.'&amp;gender_2='.$gender_2.'&amp;couple_2='.$couple_2.'&amp;country='.$country.'&amp;region='.$region.'&amp;city='.$city.'&amp;age_min='.$age_min.'&amp;age_max='.$age_max.'&amp;foto_only='.$foto.'&amp;nick='.$nick.'&amp;filter=online&amp;view='.$view.'&amp;word='.urlencode($word).'&amp;tag='.$tag;
	$form['view_photo_link']			= $file_name.'?sel='.$sel.'&amp;page=1&amp;gender_1='.$gender_1.'&amp;gender_2='.$gender_2.'&amp;couple_2='.$couple_2.'&amp;country='.$country.'&amp;region='.$region.'&amp;city='.$city.'&amp;age_min='.$age_min.'&amp;age_max='.$age_max.'&amp;foto_only='.$foto.'&amp;nick='.$nick.'&amp;filter=photo&amp;view='.$view.'&amp;word='.urlencode($word).'&amp;tag='.$tag;
	$form['view_all_link']				= $file_name.'?sel='.$sel.'&amp;page=1&amp;gender_1='.$gender_1.'&amp;gender_2='.$gender_2.'&amp;couple_2='.$couple_2.'&amp;country='.$country.'&amp;region='.$region.'&amp;city='.$city.'&amp;age_min='.$age_min.'&amp;age_max='.$age_max.'&amp;foto_only='.$foto.'&amp;nick='.$nick.'&amp;filter=all&amp;view='.$view.'&amp;word='.urlencode($word).'&amp;tag='.$tag;
	$form['view_gallery_link']			= $file_name.'?sel='.$sel.'&amp;page=1&amp;gender_1='.$gender_1.'&amp;gender_2='.$gender_2.'&amp;couple_2='.$couple_2.'&amp;country='.$country.'&amp;region='.$region.'&amp;city='.$city.'&amp;age_min='.$age_min.'&amp;age_max='.$age_max.'&amp;foto_only='.$foto.'&amp;nick='.$nick.'&amp;filter='.$filter.'&amp;view=gallery&amp;word='.urlencode($word).'&amp;tag='.$tag;
	$form['view_list_link']				= $file_name.'?sel='.$sel.'&amp;page=1&amp;gender_1='.$gender_1.'&amp;gender_2='.$gender_2.'&amp;couple_2='.$couple_2.'&amp;country='.$country.'&amp;region='.$region.'&amp;city='.$city.'&amp;age_min='.$age_min.'&amp;age_max='.$age_max.'&amp;foto_only='.$foto.'&amp;nick='.$nick.'&amp;filter='.$filter.'&amp;word='.urlencode($word).'&amp;tag='.$tag;
	
	$form['show_users_connection_str']	= $settings['show_users_connection_str'];
	$form['show_users_comments']		= $settings['show_users_comments'];
	$form['show_users_group_str']		= $settings['show_users_group_str'];
	$form['use_kiss_types']				= $settings['use_kiss_types'];
	$form['use_friend_types']			= $settings['use_friend_types'];
	$form['use_pilot_module_giftshop']	= $settings['use_pilot_module_giftshop'];
	
	$form['filter']						= $filter;
	$form['view']						= $view;
	
	$smarty->assign('user_gender', $user[ AUTH_GENDER ]);
	
	// search table
	$search_type = '1';
	
	$rs_user = $dbconn->Execute(
		'SELECT id_country, id_region, id_city FROM '.USERS_TABLE.' WHERE id = ?',
		array($id_user));
	
	$row_user = $rs_user->GetRowAssoc(false);
	
	// search preferences
	$strSQL =
		'SELECT u.gender, m.gender AS gender_search, m.couple AS couple_search, m.age_min, m.age_max, m.id_relationship
		   FROM '.USERS_TABLE.' u
	  LEFT JOIN '.USER_MATCH_TABLE.' m ON m.id_user = u.id
		  WHERE u.id = ?';
	
	$rs = $dbconn->Execute($strSQL, array($id_user));
	$row = $rs->GetRowAssoc(false);
	
	$data['gender_1'] = $row['gender'];
	$data['gender_2'] = $row['gender_search'];
	$data['couple_2'] = $row['couple_search'];
	
	if (isset($age_min)) {
		$data['age_min'] = $age_min;
	} else {
		$data['age_min'] = $row['age_min'];
	}
	
	if (isset($age_max)) {
		$data['age_max'] = $age_max;
	} else {
		$data['age_max'] = $row['age_max'];
	}
	
	if ($row['id_relationship'] != '' && $row['id_relationship'] != '0') {
		$data['arr_relationship'] = explode(',', $row['id_relationship']);
	} else {
		$data['arr_relationship'] = 0;
	}
	
	if (isset($online)) {
		$data['online'] = $online;
	}
	
	// country select
	if (QUICK_SEARCH_COUNTRY) {
		$smarty->assign('countries', html_country_select($row_user['id_country']));
	}
	
	// region select
	if (QUICK_SEARCH_REGION) {
		$smarty->assign('regions', html_region_select($row_user['id_country'], $row_user['id_region']));
	}
	
	// city select
	if (QUICK_SEARCH_CITY) {
		$smarty->assign('cities', html_region_select($row_user['id_region'], $row_user['id_city']));
	}
	
	//  distance select
	if (QUICK_SEARCH_DISTANCE) {
		$smarty->assign('distances', html_distance_select($data['within'], $data['distance']));
	}
	
	// relationships select
	if (QUICK_SEARCH_RELATIONSHIP) {
		$smarty->assign('relation', html_relationship_select($data['arr_relationship']));
	}
	
	// max. zip code length
	$form['zip_count'] = $settings['zip_count'];
	
	// gender select
	$gender_arr = array();
	
	$gender_arr[0]['id'] = '1';
	$gender_arr[0]['name'] = $lang['gender']['1'];
	$gender_arr[0]['name_search'] = $lang['gender_search']['1'];
	$gender_arr[0]['sel'] = intval($data['gender_1']) == 1 ? 1 : 0;
	$gender_arr[0]['sel_search'] = intval($data['gender_2']) == 1 ? 1 : 0;
	
	$gender_arr[1]['id'] = '2';
	$gender_arr[1]['name'] = $lang['gender']['2'];
	$gender_arr[1]['name_search'] = $lang['gender_search']['2'];
	$gender_arr[1]['sel'] = intval($data['gender_1']) == 2 ? 1 : 0;
	$gender_arr[1]['sel_search'] = intval($data['gender_2']) == 2 ? 1 : 0;
	
	$smarty->assign('gender', $gender_arr);
	
	// age range select
	if (QUICK_SEARCH_AVAILABLE_AGE_RANGE) {
		$rs = $dbconn->Execute('SELECT MAX(date_birthday), MIN(date_birthday) FROM '.USERS_TABLE.' WHERE YEAR(date_birthday) > 1900');
		$max_age = AgeFromBDate($rs->fields[1]);
		$max_age = (int) min($max_age, (int) $settings['max_age_limit']);
		$min_age = AgeFromBDate($rs->fields[0]);
		$min_age = (int) max($min_age, (int) $settings['min_age_limit']);
	} else {
		$max_age = (int) $settings['max_age_limit'];
		$min_age = (int) $settings['min_age_limit'];
	}
		
	$smarty->assign('age_max', range($max_age, $min_age));
	$smarty->assign('age_min', range($min_age, $max_age));
	
	$form['search_type'] = $search_type;
	$form['search_action'] = $file_name;
	
	$smarty->assign('icon_width', $settings['thumb_max_width']);
	$smarty->assign('form', $form);
	$smarty->assign('data', $data);
	
	if (isset($_LANG_NEED_ID) && count($_LANG_NEED_ID)) {
		$smarty->assign('base_lang', GetBaseLang($_LANG_NEED_ID));
	}
	
	$smarty->assign('section', $lang['subsection']);
	$smarty->assign('header', $lang['homepage']);
	$smarty->assign('header_s', $lang['search']);
	$smarty->display(TrimSlash($config['index_theme_path']).'/quick_search_table.tpl');
	exit;
}

?>