<?php
/**
* Advanced search by different criteria
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

// dispatcher
switch ($sel) {
	case 'search':
		SearchTable('', $sel);
	break;
	
	case 'save':
		CustomSearchSave();
	break;
	
	case 'load':
		CustomSearchLoad();
	break;
	
	case 'delete':
		CustomSearchDelete();
	break;
	
	//SH2
	
	case 'sendecard':
		$res = SendEcardTo();
		$sel = $_GET['par'];
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			if($_SESSION['err']) {
				header('Location: advanced_search.php?sel='.$sel.'&par=send');
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
				header('Location: advanced_search.php?sel='.$sel.'&par=send');
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
	
	//SH2
	case 'viewprofile':
		$res = ViewUserProfile();
		$sel = $_GET['par'];
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			if($_SESSION['err']) {
				header('Location: advanced_search.php?sel='.$sel.'&par=send');
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
	
	case 'addhotlist':
		$res = AddToHotList();
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			header('Location: advanced_search.php?sel=search&par=send');
			exit;
		} else {
			$_GET['par'] = 'send';
			SearchTable($res['err'], $sel);
		}
	break;
	
	case 'addblacklist':
		$res = AddToBlackList();
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			header('Location: advanced_search.php?sel=search&par=send');
			exit;
		} else {
			$_GET['par'] = 'send';
			SearchTable($res['err'], $sel);
		}
	break;
	
	case 'addconnection':
		$res = AddToConnections();
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			header('Location: advanced_search.php?sel=search&par=send');
			exit;
		} else {
			$_GET['par'] = 'send';
			SearchTable($res['err'], $sel);
		}
	break;
	
	case 'kiss':
		$res = SendKiss();
		if (REDIRECT_AFTER_ACTION) {
			$_SESSION['err'] = $res['err'];
			header('Location: advanced_search.php?sel=search&par=send');
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


function SearchForm($par = '', $customsearch_type = '', $id_err = '', $customsearch_load_id = '', $err = '')
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$file_name = 'advanced_search.php';
	
	$settings = GetSiteSettings(array(
		'max_age_limit',
		'min_age_limit',
		'zip_letters',
		'zip_count',
	));
	
	// multi-language tables
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	unset($_SESSION['id_arr'], $_SESSION['search_pars']);
	
	if ($customsearch_type == 'save') {
		$data = $_GET;
		$par = 'country';
	}
	
	$data['gender_1'] = $user[ AUTH_GENDER ];
	
	if ($customsearch_type == 'load')
	{
		// load from savesearch table
		$rs = $dbconn->Execute('SELECT * FROM '.SAVESEARCH_TABLE.' WHERE id = ?', array($customsearch_load_id));
		$row = $rs->GetRowAssoc(false);
		
		$data['search_name']	= stripslashes($row['name']);
		$data['gender_2']		= $row['gender'];
		$data['couple_2']		= $row['couple'];
		$data['age_max']		= (int) $row['age_max'];
		$data['age_min']		= (int) $row['age_min'];
		
		$data['id_weight']		= (int) $row['id_weight'];
		$data['id_height']		= (int) $row['id_height'];
		$data['id_country']		= (int) $row['id_country'];
		$data['id_region']		= (int) $row['id_region'];
		$data['id_city']		= (int) $row['id_city'];
		$data['within']			= (int) $row['id_within'];
		$data['distance']		= (int) $row['id_distance'];
		$data['foto_only']		= (int) $row['id_foto'];
		$data['online_only']	= (int) $row['id_online'];
		
		$relation	= $row['id_relationship'];
		if (strlen($relation)) {
			$data['relation'] = explode(',', $relation);
		}
		
		$nation = $row['id_nationality'];
		if (strlen($nation)) {
			$data['id_nation'] = explode(',', $nation);
		}
		
		$language = $row['id_language'];
		if (strlen($language)) {
			$data['id_lang'] = explode(',', $language);
		}
		
		// load from savesearch_descr table
		$i = 0;
		$rs = $dbconn->Execute('SELECT id_spr FROM '.SAVESEARCH_DESCR_TABLE.' WHERE id = ?', array($customsearch_load_id));
		while (!$rs->EOF) {
			$id_spr = $rs->fields[0];
			$data['spr'][$i] = $id_spr;
			$rs2 = $dbconn->Execute('SELECT id_info FROM '.SAVESEARCH_DESCR_TABLE.' WHERE id = ? AND id_spr = ?', array($customsearch_load_id, $id_spr));
			while (!$rs2->EOF) {
				$data['info'][$i][] = $rs2->fields[0];
				$rs2->MoveNext();
			}
			$rs->MoveNext();
			$i++;
		}
		$par = 'country';
	}
	
	// saved searches select
	$rs = $dbconn->Execute('SELECT id, name FROM '.SAVESEARCH_TABLE.' WHERE id_user = ?', array($id_user));
	$load_id = array();
	$load_name = array();
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$load_id[] = $row['id'];
		$load_name[] = stripslashes($row['name']);
		$rs->MoveNext();
	}
	$smarty->assign('load_id', $load_id);
	$smarty->assign('load_name', $load_name);
	
	//  country select
	if (ADVANCED_SEARCH_COUNTRY) {
		$smarty->assign('country_match', html_country_select($data['id_country']));
	}
	
	// region select
	if (ADVANCED_SEARCH_REGION) {
		$smarty->assign('region_match', html_region_select($data['id_country'], $data['id_region']));
	}
	
	// city select
	if (ADVANCED_SEARCH_CITY) {
		$smarty->assign('city_match', html_region_select($data['id_region'], $data['id_city']));
	}
	
	// distance select
	if (ADVANCED_SEARCH_DISTANCE) {
		$smarty->assign('distances', html_distance_select());
	}
	
	// relationship select ($field_name is global)
	if (ADVANCED_SEARCH_RELATIONSHIP) {
		$smarty->assign('relation', html_relationship_select($data['relation']));
	}
	
	$default = array();
	
	// nationality select
	$strSQL =
		'SELECT DISTINCT a.id, b.'.$field_name.' AS name
		   FROM '.NATION_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = "'.$multi_lang->TableKey(NATION_SPR_TABLE).'" AND b.id_reference = a.id
	   ORDER BY name';
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$n_arr = array();
	while(!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$n_arr[$i]['id'] = $row['id'];
		$n_arr[$i]['value'] = $row['name'];
		if (isset($data['id_nation']) && is_array($data['id_nation']) && in_array($row['id'], $data['id_nation'])) {
			$n_arr[$i]['sel'] = 1;
		} else {
			$n_arr[$i]['sel'] = 0;
		}
		$rs->MoveNext();
		$i++;
	}
	if (!isset($data['id_nation']) || !is_array($data['id_nation']) || $data['id_nation'][0] == 0) {
		$default['id_nation'] = 1;
	}
	$smarty->assign('nation_match', $n_arr);
	
	// language select
	$strSQL =
		'SELECT DISTINCT a.id, b.'.$field_name.' AS name
		   FROM '.LANGUAGE_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = "'.$multi_lang->TableKey(LANGUAGE_SPR_TABLE).'" AND b.id_reference = a.id
	   ORDER BY name';
	$rs = $dbconn->Execute($strSQL);
	$lang_sel = array();
	$i = 0;
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$lang_sel[$i]['id'] = $row['id'];
		$lang_sel[$i]['value'] = $row['name'];
		if (isset($data['id_lang']) && is_array($data['id_lang']) && in_array($row['id'], $data['id_lang'])) {
			$lang_sel[$i]['sel'] = 1;
		} else {
			$lang_sel[$i]['sel'] = 0;
		}
		$rs->MoveNext();
		$i++;
	}
	if (!isset($data['id_lang']) || !is_array($data['id_lang']) || $data['id_lang'][0] == 0) {
		$default['id_lang'] = 1;
	}
	$smarty->assign('lang_sel_match', $lang_sel);
	
	$smarty->assign('default', $default);
	
	//  weight select
	$strSQL =
		'SELECT DISTINCT a.id, b.'.$field_name.' AS name
		   FROM '.WEIGHT_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = "'.$multi_lang->TableKey(WEIGHT_SPR_TABLE).'" AND b.id_reference = a.id
	   ORDER BY a.sorter';
	$rs = $dbconn->Execute($strSQL);
	$weight_arr = array();
	$i = 0;
	while(!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$weight_arr[$i]['id'] = $row['id'];
		$weight_arr[$i]['value'] = $row['name'];
		if (isset($data['id_weight']) && $data['id_weight'] == $row['id']) {
			$weight_arr[$i]['sel'] = 1;
		} else {
			$weight_arr[$i]['sel'] = 0;
		}
		$rs->MoveNext();
		$i++;
	}
	$smarty->assign('weight', $weight_arr);
	
	// height select
	$strSQL =
		'SELECT DISTINCT a.id, b.'.$field_name.' AS name
		   FROM '.HEIGHT_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = "'.$multi_lang->TableKey(HEIGHT_SPR_TABLE).'" AND b.id_reference = a.id
	   ORDER BY a.sorter';
	$rs = $dbconn->Execute($strSQL);
	$height_arr = array();
	$i = 0;
	while(!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$height_arr[$i]['id'] = $row['id'];
		$height_arr[$i]['value'] = $row['name'];
		if (isset($data['id_height']) && $data['id_height'] == $row['id']) {
			$height_arr[$i]['sel'] = 1;
		} else {
			$height_arr[$i]['sel'] = 0;
		}
		$rs->MoveNext();
		$i++;
	}
	$smarty->assign('height', $height_arr);
	
	// gender select
	$gender_arr = array();
	
	$gender_arr[0]['id'] = '1';
	$gender_arr[0]['name'] = $lang['gender']['1'];
	$gender_arr[0]['name_search'] = $lang['gender_search']['1'];
@	$gender_arr[0]['sel_search'] = ($data['gender_2'] == 1) ? 1 : 0; //SH2
	
	$gender_arr[1]['id'] = '2';
	$gender_arr[1]['name'] = $lang['gender']['2'];
	$gender_arr[1]['name_search'] = $lang['gender_search']['2'];
@	$gender_arr[1]['sel_search'] = ($data['gender_2'] == 2) ? 1 : 0;  //SH2
	
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
	
	// descr info from db
	$sess_info = array();
	
	if ($par == 'country') {
		for ($i = 0; $i < count($data['spr']); $i++) {
			$cr = $dbconn->Execute('SELECT COUNT(*) FROM '.DESCR_SPR_VALUE_TABLE.' WHERE id_spr = ?', array($data['spr'][$i]));
			if ($cr->fields[0] > count($data['info'][$i])) {
				for ($j = 0; $j < count($data['info'][$i]); $j++) {
					$sess_info[$data['spr'][$i]][$j] = $data['info'][$i][$j];
				}
			}
		}
	}
	
	// descr selects
	$strSQL =
		'SELECT DISTINCT a.id, b.'.$field_name.' AS name
		   FROM '.DESCR_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = "'.$multi_lang->TableKey(DESCR_SPR_TABLE).'" AND b.id_reference = a.id
	   ORDER BY a.sorter';
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	while (!$rs->EOF) {
		$row = intval($i % 4) + 1;
		
		$in = intval($i / 4);
		$info[$in]['id_'.$row] = $rs->fields[0];
		$info[$in]['name_'.$row] = $rs->fields[1];
		$info[$in]['num_'.$row] = $i;
		
		$strSQL_opt =
			'SELECT DISTINCT a.id, b.'.$field_name.' AS name
			   FROM '.DESCR_SPR_VALUE_TABLE.' a
		  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = "'.$multi_lang->TableKey(DESCR_SPR_VALUE_TABLE).'" AND b.id_reference = a.id
			  WHERE a.id_spr = ?
		   ORDER BY name';
		
		$rs_opt = $dbconn->Execute($strSQL_opt, array($rs->fields[0]));
		$info = array();
		$j = 0;
		while (!$rs_opt ->EOF) {
			$info[$in]['opt_value_'.$row][$j] = $rs_opt->fields[0];
			$info[$in]['opt_name_'.$row][$j] = $rs_opt->fields[1];
			if ((isset($sess_info[$rs->fields[0]]) && is_array($sess_info[$rs->fields[0]]) && in_array(0, $sess_info[$rs->fields[0]]))
			|| !isset($sess_info[$rs->fields[0]]) || !is_array($sess_info[$rs->fields[0]]))
			{
				$info[$in]['sel_all_'.$row] = '1';
			}
			else
			{
				if (isset($sess_info[$rs->fields[0]]) && is_array($sess_info[$rs->fields[0]]) && in_array($rs_opt->fields[0], $sess_info[$rs->fields[0]])) {
					$info[$in]['opt_sel_'.$row][$j] = $rs_opt->fields[0];
				} else {
					$info[$in]['opt_sel_'.$row][$j] = 0;
				}
			}
			$rs_opt->MoveNext();
			$j++;
		}
		$rs->MoveNext();
		$i++;
	}
	
	$form['search_action'] = $file_name;
	
	$smarty->assign('id_err', $id_err);
	$smarty->assign('section', $lang['subsection']);
	$smarty->assign('err', $err);
	$smarty->assign('info', $info);
	$smarty->assign('form', $form);
	$smarty->assign('data', $data);
	$smarty->assign('header', $lang['homepage']);
	$smarty->assign('header_s', $lang['search']);
	$smarty->assign('header_perfect', $lang['users']);
	
	$smarty->display(TrimSlash($config['index_theme_path']).'/advanced_search_form.tpl');
	exit;
}


function SearchTable($err = '', $sel = '')
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;
	
	$debug = false;
	
	if ($debug) echo '<font color="red">';
	
	$id_user = $user[ AUTH_ID_USER ];
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$file_name = 'advanced_search.php';
	
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
		'use_shoutbox_feature',
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
	
	/*
	// RS: in use_session test below we originally tested $id_arr but this does not make sense to me
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
	*/
	
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
		// get search criteria from session or form input
		if (isset($_GET['par']) && ($_GET['par'] == 'back' || $_GET['par'] == 'send')) {
			$spr		= $_SESSION['search_pars']['spr'];
			$info		= $_SESSION['search_pars']['info'];
			$id_lang	= $_SESSION['search_pars']['id_lang'];
			$id_country	= $_SESSION['search_pars']['id_country'];
			$id_region	= $_SESSION['search_pars']['id_region'];
			$id_city	= $_SESSION['search_pars']['id_city'];
			$id_nation	= $_SESSION['search_pars']['id_nation'];
			$id_weight	= $_SESSION['id_weight'];
			$id_height	= $_SESSION['id_height'];
			$gender_2	= $_SESSION['search_pars']['gender_2'];
			$couple_2	= $_SESSION['search_pars']['couple_2'];
			$age_max	= $_SESSION['search_pars']['age_max'];
			$age_min	= $_SESSION['search_pars']['age_min'];
			$within		= $_SESSION['search_pars']['within'];
			$distance	= $_SESSION['search_pars']['distance'];
			$relation	= $_SESSION['search_pars']['relation'];
			$foto		= $_SESSION['search_pars']['foto'];
			$online		= $_SESSION['search_pars']['online'];
		} else {
			$spr		= isset($_REQUEST['spr'])			? $_REQUEST['spr']				: array();
			$info		= isset($_REQUEST['info'])			? $_REQUEST['info']				: array();
			$id_lang	= isset($_REQUEST['id_lang'])		? (int)$_REQUEST['id_lang']		: 0;
			$id_country	= isset($_REQUEST['id_country'])	? (int)$_REQUEST['id_country']	: 0;
			$id_region	= isset($_REQUEST['id_region'])		? (int)$_REQUEST['id_region']	: 0;
			$id_city	= isset($_REQUEST['id_city'])		? (int)$_REQUEST['id_city']		: 0;
			$id_nation	= isset($_REQUEST['id_nation'])		? (int)$_REQUEST['id_nation']	: 0;
			$id_weight	= isset($_REQUEST['id_weight'])		? (int)$_REQUEST['id_weight']	: 0;
			$id_height	= isset($_REQUEST['id_height'])		? (int)$_REQUEST['id_height']	: 0;
			$gender_2	= isset($_REQUEST['gender_2'])		? (int)$_REQUEST['gender_2']	: 0;
			$couple_2	= isset($_REQUEST['couple_2'])		? $_REQUEST['couple_2']			: 0;
			$age_max	= isset($_REQUEST['age_max'])		? (int)$_REQUEST['age_max']		: 0;
			$age_min	= isset($_REQUEST['age_min'])		? (int)$_REQUEST['age_min']		: 0;
			$within		= isset($_REQUEST['within'])		? (int)$_REQUEST['within']		: 0;
			$distance	= isset($_REQUEST['distance'])		? (int)$_REQUEST['distance']	: 0;
			$relation	= isset($_REQUEST['relation'])		? $_REQUEST['relation']			: array();
			$foto		= isset($_REQUEST['foto_only'])		? (int)$_REQUEST['foto_only']	: 0;
			$online		= isset($_REQUEST['online_only'])	? (int)$_REQUEST['online_only']	: 0;
		}
		
		// get fresh search results
		$_SESSION['id_arr']			= array();
		$_SESSION['with_arr']		= array();
		$_SESSION['without_arr']	= array();
		$_SESSION['online_arr']		= array();
		$_SESSION['offline_arr']	= array();
		$_SESSION['search_pars']	= array();
		
		// store search parameters in session
		$_SESSION['search_pars']['spr']			= $spr;
		$_SESSION['search_pars']['info']		= $info;
		$_SESSION['search_pars']['id_lang']		= $id_lang;
		$_SESSION['search_pars']['id_country']	= $id_country;
		$_SESSION['search_pars']['id_region']	= $id_region;
		$_SESSION['search_pars']['id_city']		= $id_city;
		$_SESSION['search_pars']['id_nation']	= $id_nation;
		$_SESSION['search_pars']['id_weight']	= $id_weight;
		$_SESSION['search_pars']['id_height']	= $id_height;
		$_SESSION['search_pars']['gender_2']	= $gender_2;
		$_SESSION['search_pars']['couple_2']	= $couple_2;
		$_SESSION['search_pars']['age_max']		= $age_max;
		$_SESSION['search_pars']['age_min']		= $age_min;
		$_SESSION['search_pars']['within']		= $within;
		$_SESSION['search_pars']['distance']	= $distance;
		$_SESSION['search_pars']['relation']	= $relation;
		$_SESSION['search_pars']['foto']		= $foto;
		$_SESSION['search_pars']['online']		= $online;
		
		// get array of user ids which match the description search
		$descr_user_array = array();
		$descr_user_string = '';
		$descr_error = 0;
		
		foreach ($spr as $key => $id_spr)
		{
			$options_count = $dbconn->GetOne('SELECT COUNT(*) FROM '.DESCR_SPR_VALUE_TABLE.' WHERE id_spr = ?', array($id_spr));
			
			if (!empty($info[$key]) && is_array($info[$key]) && !in_array('0', $info[$key]) && $options_count > count($info[$key]))
			{
				$info_string = implode(',', $info[$key]);
				
				// intersection of users who match this description test with all previous description tests
				if (is_array($descr_user_array) && !empty($descr_user_array)) {
					$descr_user_string = ' AND id_user IN ('.implode(',', $descr_user_array).')';
				}
				
				// ralf: try without id_spr in where clause, should be sufficient to test id_value
				$str_sql_descr =
					'SELECT DISTINCT id_user
					   FROM '.DESCR_SPR_USER_TABLE.'
					  WHERE id_spr = ? AND id_value IN ('.$info_string.') '.$descr_user_string;
				$descr_rs = $dbconn->Execute($str_sql_descr, array($id_spr));
				
				$descr_user_array = array();
				
				while (!$descr_rs->EOF) {
					$descr_user_array[] = $descr_rs->fields[0];
					$descr_rs->MoveNext();
				}
				
				// $descr_user_array now contains the ids of all users who match this description and all previously tested descriptions
				
				if (empty($descr_user_array)) {
					$descr_error = 1;
					$_SESSION['id_arr'] = array();
					break;
				}
			}
		}
		
		if ($within == 1 && !intval($id_city)) {
			$err = $lang['distance']['specify_city'];
			$descr_error = 1;
		}
		
		if ($descr_error == 0)
		{
			if ($online) {
				$online_join = 'INNER JOIN';
			} else {
				$online_join = 'LEFT JOIN';
			}
			
			$select_clause =
				'SELECT u.id, u.icon_path, s.id_user AS session, up.hide_online';
			
			$from_clause = '
				   FROM '.USERS_TABLE.' u
			 INNER JOIN '.USER_MATCH_TABLE.' m ON m.id_user = u.id
	   '.$online_join.' '.ACTIVE_SESSIONS_TABLE.' s ON u.id = s.id_user
			  LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id
			 INNER JOIN '.USER_GROUP_TABLE.' ug ON u.id = ug.id_user
			 INNER JOIN '.GROUPS_TABLE.' g ON ug.id_group = g.id';
			
			$group_clause = ' GROUP BY u.id';
			
			$order_clause = ' ORDER BY g.sort ASC, u.mm_platinum_applied DESC, u.id DESC';
			
			$where_clause = ' WHERE u.status = "1" AND u.visible = "1" AND u.root_user = "0" AND u.guest_user = "0" AND u.id <> "'.$id_user.'"';
			$where_clause.= '   AND u.id NOT IN (SELECT id_user FROM '.USER_GROUP_TABLE.' WHERE id_group IN ('.INVISIBLE_GROUPS.'))';
			
			// respect privacy
			$usr_group = $dbconn->GetOne('SELECT id_group FROM '.USER_GROUP_TABLE.' WHERE id_user = ?', array($id_user));
			$where_clause .= sql_privacy_where($usr_group);
			
			// online search
			if ($online) {
				$where_clause .= ' AND up.hide_online = "0" ';
			}
			
			// location search
			if ($within == 1) {
				$city_arr = CityInRadius($id_city, $distance);
				if (count($city_arr)) {
					$where_clause .= ' AND u.id_city IN ('.implode(',', $city_arr).')';
				}
			} else {
				if (intval($id_country)) {
					$where_clause .= ' AND u.id_country = "'.$id_country.'"';
				}
				if (intval($id_region)) {
					$where_clause .= ' AND u.id_region = "'.$id_region.'"';
				}
				if (intval($id_city)) {
					$where_clause .= ' AND u.id_city = "'.$id_city.'"';
				}
			}
			
			// relationship search
			// ralf: we don't use this, and I am afraid it is too simple, see quicksearch for alternative
			$relation_str = false;
			if (count($relation)) {
				$relation_arr = array();
				foreach ($relation as $value) {
					if (intval($value)) {
						$relation_arr[] = $value.' IN (m.id_relationship)';
					} else {
						$relation_str = ' m.id_relationship <> "" AND m.id_relationship IS NOT NULL ';
						break;
					}
				}
				if (!$relation_str) {
					$relation_arr[] = ' "0" IN (m.id_relationship) ';
					$relation_str = implode(' OR ', $relation_arr);
				}
				$where_clause .= ' AND ('.$relation_str.') ';
			}
			
			// weight search
			if (intval($id_weight)) {
				$where_clause .= ' AND u.id_weight = "'.$id_weight.'"';
			}
			
			// height search
			if (intval($id_height)) {
				$where_clause .= ' AND u.id_height = "'.$id_height.'"';
			}
			
			// nationality search
			$cr = $dbconn->Execute('SELECT COUNT(*) FROM '.NATION_SPR_TABLE);
			if (is_array($id_nation) && !empty($id_nation) && !in_array('0', $id_nation) && $cr->fields[0] > count($id_nation)) {
				$id_nation_str = implode(',', $id_nation);
				$where_clause .= ' AND u.id_nationality IN ('.$id_nation_str.')';
			}
			
			// language search
			$cr = $dbconn->Execute('SELECT COUNT(*) FROM '.LANGUAGE_SPR_TABLE);
			if (is_array($id_lang) && count($id_lang) > 0 && !in_array('0', $id_lang) && $cr->fields[0] > count($id_lang)) {
				$id_lang_str = implode(',', $id_lang);
				$where_clause .= ' AND (u.id_language_1 IN ('.$id_lang_str.') OR u.id_language_2 IN ('.$id_lang_str.') OR u.id_language_3 IN ('.$id_lang_str.'))';
			}
			
			// age range search
			if ($age_min > 0 && $age_max > 0) {
				$date_start = date('Y-m-d', strtotime('-' . ($age_max + 1) . ' year +1 day'));
				$date_end = date('Y-m-d', strtotime('-' . $age_min . ' year'));
				$where_clause .= ' AND DATE_FORMAT(u.date_birthday, "%Y-%m-%d") BETWEEN "'.$date_start.'" AND "'.$date_end.'"';
			}
			
			/*
			if ($age_min > 0) {
				$where_clause .= ' AND STRCMP(DATE_FORMAT(u.date_birthday, "%Y%m%d"), DATE_FORMAT("'.DateFromAge($age_min-1).'", "%Y%m%d")) <= 0';
			}
			if ($age_max > 0) {
				$where_clause .= ' AND STRCMP(DATE_FORMAT(u.date_birthday, "%Y%m%d"), DATE_FORMAT("'.DateFromAge($age_max+1).'", "%Y%m%d")) >= 0';
			}
			*/
			
			// gender search
			if (intval($gender_2)) {
				$where_clause .= ' AND u.gender = "'.$gender_2.'"';
			}
			
			// couple search
			if (isset($couple_2)) {
				$where_clause .= ' AND u.couple = "'.$couple_2.'"';
			}
			
			// has photo search
			if ($foto == 1) {
				$where_clause .= ' AND u.icon_path <> ""';
			}
			
			// description search
			if (is_array($descr_user_array) && !empty($descr_user_array)) {
				$where_clause .= ' AND u.id IN ('.implode(',', $descr_user_array).')';
			}
			
			// execute search
			$strSQL = $select_clause.$from_clause.$where_clause.$group_clause.$order_clause;
			
			if ($debug) echo $strSQL;
			
			$rs = $dbconn->Execute($strSQL);
			
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
		} // if ($descr_error == 0)
	} // if ($use_session == 0)
	
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
		  LEFT JOIN '.USER_MATCH_TABLE.' x ON x.id_user = u.id
		  LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id
		 INNER JOIN '.USER_GROUP_TABLE.' ug ON u.id = ug.id_user
		 INNER JOIN '.GROUPS_TABLE.' g ON ug.id_group = g.id
			  WHERE u.id IN (' . implode(',', $id_arr) . ')
		   GROUP BY u.id
		   ORDER BY g.sort ASC, u.mm_platinum_applied DESC, u.id DESC
			  LIMIT '.$limit_offset.', '.$limit_length;
		
		if ($debug) echo '$strSQL='.$strSQL.'<br>';
		
		$rs = $dbconn->Execute($strSQL);
		
		$search = array();
		$_LANG_NEED_ID = array();
		$i = 0;
		$search_type = 'a';
		
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
				
				//SH2
				$search[$i]['ecard_link']		= $file_name.'?sel=sendecard&amp;id='.$row['id'].'&amp;par='.$sel;
				
				
				
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
				
				// make couple link
				if (ADVANCED_SEARCH_COUPLE) {
					if ($is_coupled != 1) {
						$search[$i]['be_couple_link'] = $file_name.'?sel=be_couple&amp;id='.$row['id'];
					}
				}
				
				// voip link
				if ($config['voipcall_feature']) {
					$search[$i]['call_link'] = 'voip_call.php?sel=rate&amp;id_user='.$row['id'];
				}
			}
			
			$rs->MoveNext();
			$i++;
		}
		
		$smarty->assign('search_res', $search);
		
		// page count
		$form['pages_count'] = ceil($num_records / $_SESSION['per_page_rec']);
		
		// paging links
		$param = $file_name.'?sel=search&amp;filter='.$filter.'&amp;view='.$view.'&amp;';
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
	
	$form['view_online_link']			= $file_name.'?sel=search&amp;page=1&amp;filter=online&amp;view='.$view;
	$form['view_photo_link']			= $file_name.'?sel=search&amp;page=1&amp;filter=photo&amp;view='.$view;
	$form['view_all_link']				= $file_name.'?sel=search&amp;page=1&amp;filter=all&amp;view='.$view;
	$form['view_gallery_link']			= $file_name.'?sel=search&amp;page=1&amp;filter='.$filter.'&amp;view=gallery';
	$form['view_list_link']				= $file_name.'?sel=search&amp;page=1&amp;filter='.$filter.'&amp;view=list';
	
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
	$smarty->assign('header_s', $lang['search']);
	$smarty->display(TrimSlash($config['index_theme_path']).'/advanced_search_table.tpl');
	exit;
}


function CustomSearchSave()
{
	global $dbconn, $user;
	
	// check parameter
	if (empty($_GET['search_name'])) {
		$id_err = 1;
		SearchForm('', 'save', $id_err, '');
		exit;
	}
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$search_name = trim($_GET['search_name']);
	
	// check existing search
	$rs = $dbconn->Execute('SELECT name FROM '.SAVESEARCH_TABLE.' WHERE id_user = ? AND name LIKE "'.$search_name.'"', array($id_user));
	
	if ($rs->fields[0]) {
		###
		### ralf: this is wrong! we need to update the savesearch record!
		###
		$id_err = 2;
		SearchForm('', 'save', $id_err, '');
		exit;
	}
	
	$id_err = 0;
	
	// relation
	$rel = isset($_GET['relation']) ? $_GET['relation'] : array();
	
	if (!empty($rel)) {
		if ($rel[0] == '0') {
			$relation = '0';
		} else {
			$relation = implode(',', $rel);
		}
	} else {
		$relation = '';
	}
	
	// nation
	$nat = isset($_GET['id_nation']) ? $_GET['id_nation'] : array();
	
	if (!empty($nat)) {
		if ($nat[0] == '0') {
			$nation = '0';
		} else {
			$nation = implode(',', $nat);
		}
	} else {
		$nation = '';
	}
	
	// language
	$language = isset($_GET['id_lang']) ? $_GET['id_lang'] : array();
	
	if (!empty($language)) {
		if ($language[0] == '0') {
			$languageb = '0';
		} else {
			$languageb = implode(',', $language);
		}
	} else {
		$languageb = '';
	}
	
	$strSQL =
		'INSERT INTO '.SAVESEARCH_TABLE.' (
			name, id_user, gender, couple, age_max,
			age_min, id_relationship, id_weight, id_height, id_country,
			id_region, id_city, id_within, id_distance, id_nationality,
			id_language, id_foto, id_online
		) VALUES (
			"'.$_GET['search_name'].'",
			"'.$user[ AUTH_ID_USER ].'",
			"'.$_GET['gender_2'].'",
			"'.$_GET['couple_2'].'",
			"'.$_GET['age_max'].'",
			"'.$_GET['age_min'].'",
			"'.$relation.'",
			"'.$_GET['id_weight'].'",
			"'.$_GET['id_height'].'",
			"'.$_GET['id_country'].'",
			"'.$_GET['id_region'].'",
			"'.$_GET['id_city'].'",
			"'.(isset($_GET['within']) ? (int) $_GET['within'] : 0).'",
			"'.(isset($_GET['distance']) ? (int) $_GET['distance'] : 0).'",
			"'.$nation.'",
			"'.$languageb.'",
			"'.(isset($_GET['foto_only']) ? 1 : 0).'",
			"'.(isset($_GET['online_only']) ? 1 : 0).'"
		)';
	
	$dbconn->Execute($strSQL);
	
	$id_row = $dbconn->getOne('SELECT MAX(id) FROM '.SAVESEARCH_TABLE);
	
	for ($i = 0; $i < count($_GET['spr']); $i++) {
		for ($j = 0; $j < count($_GET['info'][$i]); $j++) {
			$id_spr = $_GET['spr'][$i];
			$id_info = $_GET['info'][$i][$j];
			$dbconn->Execute('INSERT INTO '.SAVESEARCH_DESCR_TABLE.' SET id = ?, id_spr = ?, id_info = ?', array($id_row, $id_spr, $id_info));
		}
	}
	
	SearchForm('', 'save', $id_err, '');
	exit;
}


function CustomSearchLoad()
{
	$load_id = isset($_GET['load_id']) ? $_GET['load_id'] : '';
	
	SearchForm('', 'load', '', $load_id);
	
	exit;
}


function CustomSearchDelete()
{
	global $dbconn;
	
	$load_id = isset($_GET['load_id']) ? (int)$_GET['load_id'] : 0;
	
	if ($load_id > 0) {
		$dbconn->Execute('DELETE FROM '.SAVESEARCH_TABLE.' WHERE id = ?', array($load_id));
		$dbconn->Execute('DELETE FROM '.SAVESEARCH_DESCR_TABLE.' WHERE id = ?', array($load_id));
	}
	
	SearchForm();
	
	exit;
}

?>