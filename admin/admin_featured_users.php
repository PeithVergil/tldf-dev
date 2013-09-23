<?php
/**
* Site user administration (Featured Users list)
**/

include '../include/config.php';
include_once '../common.php';
include '../include/config_admin.php';
include '../include/functions_auth.php';
include '../include/functions_admin.php';
//include '../include/class.phpmailer.php';
//include '../include/functions_mail.php';
//include '../include/functions_newsletter.php';
include '../include/class.lang.php';
//include '../include/class.percent.php';

include '../include/config_index.php';
include '../include/functions_users.php';

$auth = auth_user();
login_check($auth);
//IsFileAllowed($auth[0], GetRightModulePath(__FILE__), 'users');

$sel = isset($_REQUEST['sel']) ? $_REQUEST['sel'] : '';

if (isset($_REQUEST['no_invite'])) unset($_SESSION['invite_users']);

switch ($sel) {
	case 'del': DeleteFromList(); break;
	default:	FeaturedUsersList();
}

exit;


function FeaturedUsersList($err='')
{
	global $smarty, $dbconn, $config, $config_admin, $lang, $sel;
	
	$file_name = 'admin_featured_users.php';
	
	AdminMainMenu($lang['users']);
	
	$sel = isset($_REQUEST['sel']) ? $_REQUEST['sel'] : '';
	
	$settings = GetSiteSettings(array('icon_male_default', 'icon_female_default'));
	
	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];
	
	$strSQL  =
		'SELECT DISTINCT u.id, u.login, u.fname, u.sname, u.gender, u.date_birthday, u.big_icon_path, u.id_country, u.what_i_do, u.icon_path,
				u.mm_platinum_applied, u.status, u.confirm, up.promotion_1, ug.id_group, g.name as user_group, c.name as country,
				up.featured_land, up.featured_home
		   FROM '.USERS_TABLE.' u
	  LEFT JOIN '.USER_GROUP_TABLE.' as ug ON u.id = ug.id_user
	  LEFT JOIN '.GROUPS_TABLE.' g ON ug.id_group = g.id
	  LEFT JOIN '.USER_PRIVACY_SETTINGS.' as up ON u.id = up.id_user
	  LEFT JOIN '.COUNTRY_SPR_TABLE.' as c ON c.id = u.id_country
		  WHERE up.featured_land = "1" OR up.featured_home = "1"
	   ORDER BY u.login;';
	
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$user = array();
	
	while (!$rs->EOF)
	{
		$row = $rs->GetRowAssoc(false);
		//$user[$i]['number'] 	= ($page-1)*$config_admin['users_numpage']+($i+1);
		$user[$i]['number']		= $i+1;
		$user[$i]['id']			= $row['id'];
		$user[$i]['name']		= stripslashes($row['fname'].' '.$row['sname']);
		$user[$i]['nick']		= $row['login'];
		$user[$i]['gender']		= $lang['gender'][$row['gender']];
		$user[$i]['age']		= AgeFromBDate($row['date_birthday']);
		
		$tick_mark = "";
		if ($row['status'] != '1') {
			$user[$i]['status'] = 'Disabled<br>(Status)';
		} elseif ($row['confirm'] != '1') {
			$user[$i]['status'] = 'Disabled<br>(Unconfirmed)';
		} elseif ($row['promotion_1'] == '1') {
			$user[$i]['status'] = 'Disabled<br>(Privacy)';
		} else {
			$user[$i]['status'] = "&radic;"; // &radic; : tick mark sign
		}
		
		if($row['featured_land']) {
			$user[$i]['featured_land'] = $user[$i]['status'];
		}
		
		if($row['featured_home']) {
			$user[$i]['featured_home'] = $user[$i]['status'];
		}
		
		$user[$i]['edit_link']	= 'admin_users.php?sel=edit&pre_sel=featured&id='.$row['id'];
		
		$icon_path 				= $row['icon_path'] ? $row['icon_path'] : $default_photos[$row['gender']];
		$user[$i]['icon_path'] 	= $config['server'].$config['site_root'].'/uploades/icons/'.$icon_path;
		
		$user[$i]['country']	= stripslashes($row['country']);
		$user[$i]['id_group']	= $row['id_group'];
		$user[$i]['user_group']	= stripslashes($row['user_group']);
		
		// Check for Applied for Platinum
		if ($row['id_group'] == MM_TRIAL_GUY_ID || $row['id_group'] == MM_TRIAL_LADY_ID || $row['id_group'] == MM_REGULAR_GUY_ID || $row['id_group'] == MM_REGULAR_LADY_ID) {
			if (!empty($row['mm_platinum_applied'])) {
				$user[$i]['user_group'] .= ' ('. $lang['users']['platinum_applied'].')';
			}
		}
		
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('user', $user);
	$smarty->assign('header', $lang['users']);
	$smarty->assign('sel', $sel);
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_featured_users_table.tpl');
	exit;
}

function DeleteFromList()
{
	global $dbconn;
	
	$id = intval($_GET['id']);
	
	if (!$id) {
		$err = 'Error in deletion';
		FeaturedUsersList($err);
		exit;
	}
	
	$dbconn->Execute('UPDATE '.USER_PRIVACY_SETTINGS.' SET featured_land = "0", featured_home = "0" WHERE id_user = ?', array($id));
	
	echo '<script>location.href="admin_featured_users.php"</script>';
	exit;
}

?>