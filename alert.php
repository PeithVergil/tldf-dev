<?php
/**
* Access denied page for users
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

// authentication
$user = auth_index_user();

if (empty($user) || $user == 'err' || empty($user[ AUTH_ID_USER ])) {
	header('location: '.$config['site_root'].'/index.php');
	exit;
}

// check guest
// (public access)

// check group, period, expiration
RefreshAccount();

// check status
// (public access)

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
$id_module = isset($_GET['id_module']) ? (int) $_GET['id_module'] : null;

// dispatcher
switch ($sel) {
	default:
		AlertTable($id_module);
	break;
}

exit;


function AlertTable($id_module)
{
	global $smarty, $lang, $config, $dbconn, $user;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	lastViewed();
	
	if ($user[ AUTH_GUEST])
	{
		//Start RASHMI
		$aplink = '';
		//print '<pre>'; print_r($_SERVER); print '</pre>';
		if (isset($_SERVER['HTTP_REFERER'])) {
			if (strpos($_SERVER['HTTP_REFERER'], 'mailbox.php') !== false) {
				$prid = str_replace($config['server'].$config['site_root'].'/mailbox.php?', '', $_SERVER['HTTP_REFERER']);
				$prid = str_replace('=', '-', $prid);
				$prid = str_replace('&', '/', $prid);
				if ($prid != '') {
					$aplink = '&pid='.$prid;	
				}
			} else {
				$prid = str_replace($config['server'].$config['site_root'].'/viewprofile.php?id=', '', $_SERVER['HTTP_REFERER']);
				if ($prid != '') {
					$aplink = '&pid='.$prid;	
				}
			}
		}
		//End RASHMI
		
		$form['err'] = $lang['err']['access_denied_1'];
		
		#$form['register_link'] = './registration.php';
		$form['register_link'] = './index.php';
		
		//RASHMI
		$form['login_link'] = './index.php?sel=login'.$aplink;
		$form['table_alert_header'] = $lang['home_page']['alert_header_register'];
	}
	elseif (!$user[ AUTH_STATUS ])
	{
		$form['err'] = $lang['err']['access_denied_2'];
		$form['homepage_link'] = './homepage.php';
		$form['logoff_link'] = './index.php?sel=logoff';
		$form['table_alert_header'] = $lang['home_page']['alert_header_status'];
		
		$rs = $dbconn->Execute('SELECT confirm FROM '.USERS_TABLE.' WHERE id = ?', array($user[ AUTH_ID_USER]));
		
		if (!$rs->fields[0]) {
			$form['alert_header_confirm'] = $lang['home_page']['alert_header_confirm'];
		}
	}
	else
	{
		$form['err'] = $lang['err']['access_denied_2'];
		$form['table_alert_header'] = $lang['home_page']['alert_page'];
		$form['homepage_link'] = './homepage.php';
		$form['logoff_link'] = './index.php?sel=logoff';
		
		/*
		$type = 'buy';
		$form['table_alert_header'] = $lang['home_page']['alert_header_buy'];
		
		$groups = GroupListForModule($id_module);
		
		if (is_array($groups) && !empty($groups))
		{
			$groups_str = implode(',', $groups);
			
			$strSQL =
				'SELECT DISTINCT a.id, a.name
				   FROM '.GROUPS_TABLE.' a
			 INNER JOIN '.GROUP_PERIOD_TABLE.' b ON b.id_group=a.id
				  WHERE a.id IN ('.$groups_str.') AND b.status = "1"
			   GROUP BY a.id';
			
			$rs = $dbconn->Execute($strSQL);
			
			$i = 0;
			$group = array();
			
			while (!$rs->EOF) {
				$row = $rs->GetRowAssoc(false);
				$group[$i]['name'] = $row['name'];
				$group[$i]['credit_link'] = './payment.php?sel=save_1&group='.$row['id'];
				$rs->MoveNext();
				$i++;
			}
			
			$smarty->assign('group', $group);
		}
		*/
	}
	
	$smarty->assign('header', $lang['home_page']);
	$smarty->assign('form', $form);
	$smarty->display(TrimSlash($config['index_theme_path']).'/alert_table.tpl');
	exit;
}

?>