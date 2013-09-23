<?php
/**
* Registration confirmation page
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

// active menu item
$smarty->assign('sub_menu_num', '');

Banners(GetRightModulePath(__FILE__));
IndexHomePage();
GetActiveUserInfo($user);

$id_user = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

$err = '';

if (!$id_user)
{
	$err = $lang['confirm']['error_not_user'];
}
else
{
	$rs = $dbconn->Execute('SELECT confirm, id_solve360, login FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
	
	if (!($rs->RecordCount()))
	{
		// invalid confirmation code
		$err = $lang['confirm']['error_not_user'];
	}
	elseif ($rs->fields[0])
	{
		// already confirmed
		$err = $lang['confirm']['error_confirm_user'];
		$form['confirmed'] = true;
		if ($user[ AUTH_ID_USER ] && !$user[ AUTH_GUEST ]) {
			header('Location: ./myprofile.php?sel=confirm_email');
			exit;
		}
	}
	else
	{
		// confirmation success
		$dbconn->Execute('UPDATE '.USERS_TABLE.' SET confirm = "1" WHERE id = ?', array($id_user));
		$err = $lang['confirm']['success_confirm_user_logged_out'];
		
		// SOLVE360
		if (SOLVE360_CONNECTION) {
			require_once $config['site_path'].'/include/Solve360Service.php';
			$solve360Service = new Solve360Service(SOLVE360_USER, SOLVE360_TOKEN);

			$solve360 = array();
			require $config['site_path'].'/include/Solve360CustomFields.php';

			$contactData = array(
				$solve360['TLDF Confirmed'] => 'Yes',
			);

			$id_solve360 = $rs->fields[1];
			$login = $rs->fields[2];
			
			if (!empty($id_solve360)) {
				$contact = $solve360Service->editContact($id_solve360, $contactData);
				#var_dump($contact); exit;
				if (isset($contact->errors)) {
					$subject = 'Error while updating TLDF profile confirmation';
					solve360_api_error($contact, $subject, $login);
				}
			}
			// maybe add contact if not found
		}
		
		// GA_TRACKING
		$_SESSION['ga_event_code'] = 'emailconfirmed';
		
		if ($user[ AUTH_ID_USER ] && !$user[ AUTH_GUEST ]) {
			header('Location: ./myprofile.php?sel_act=congrat');
			exit;
		}
	}
}

$form['err'] = $err;

$smarty->assign('form', $form);
$smarty->assign('header', $lang['confirm']);

$smarty->display(TrimSlash($config['index_theme_path']).'/confirmation_table.tpl');
exit;
?>