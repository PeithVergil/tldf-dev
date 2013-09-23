<?php
/**
* User payment page (groups selection, groups descriptions, sends payment data to a payment system)
*
* @package DatingPro
* @subpackage User Mode
**/

include './include/config.php';
include './common.php';
include './include/config_index.php';
include './include/config_admin.php';
include './include/functions_auth.php';
include './include/functions_index.php';
include './include/class.lang.php';
include './include/class.phpmailer.php';
include './include/functions_mail.php';

// authentication
$user = auth_index_user();

if (empty($user) || $user == 'err' || empty($user[ AUTH_ID_USER ])) {
	header('location: '.$config['site_root'].'/index.php');
	exit;
}

// check guest and admin
if ($user[ AUTH_GUEST ] || $user[ AUTH_ROOT ]) {
	AlertPage(GetRightModulePath(__FILE__));
}

// check group, period, expiration
RefreshAccount();

// check status
// (signup users with status=0 must be able to use payment.php for getting trial membership)

// check permissions
// (no permissions defined, but guest and admin are blocked, see above)

// alerts and statistics
if (!$user[ AUTH_GUEST ]) {
	GetAlertsMessage();
	SetModuleStatistic(GetRightModulePath(__FILE__));
}

// active menu item
$smarty->assign('sub_menu_num', '4');

$id_user = $user[ AUTH_ID_USER ];

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

/**
 * special treatment for Upgrade to Platinum
 **/

//Added by Narendra or statement
if ($sel == 'pay_platinum' || $sel == 'pay_installments' || isset($_REQUEST['period_id']) && ($_REQUEST['period_id'] == MM_PLATINUM_GUY_PERIOD_ID || $_REQUEST['period_id'] == MM_PLATINUM_LADY_PERIOD_ID))
{
	// check for already paid or submitted for platinum application
	$rs = $dbconn->Execute('SELECT mm_platinum_paid, mm_platinum_applied, mm_first_installment_date, mm_second_installment_date FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
	$row = $rs->GetRowAssoc(false);
	
	if (!empty($row['mm_platinum_paid'])) {
		header('location: account.php?from=check_platinum_payment');
		exit;
	}
	
	// check for pending offline payment
	$id_group = ($user[ AUTH_GENDER ] == GENDER_MALE) ? MM_PLATINUM_GUY_ID : MM_PLATINUM_LADY_ID;
	
	$strSQL =
		'SELECT paysystem
		   FROM '.BILLING_REQUESTS_TABLE.'
		  WHERE id_user = ? AND id_group = ? AND status = "send"
			AND paysystem IN ("atm_payment", "wire_transfer", "bank_cheque")';
	
	$paysystem = $dbconn->getOne($strSQL, array($id_user, $id_group));
	
	if (!empty($paysystem)) {
		header('location: account.php?from=check_platinum_payment');
		exit;
	}
	
	// $_GET['group'] is only used on payment_page_1
	// $_GET['group'] = $id_group;
	
	//Old code
	//$smarty->assign('pay_installments', 1);
	//$_GET['period_id'] = ($user[ AUTH_GENDER ] == GENDER_MALE) ? MM_PLATINUM_GUY_PERIOD_ID : MM_PLATINUM_LADY_PERIOD_ID; 
	
	//Changed By Narendra for Installments
	//----------------  Start --------------
	if ($sel == 'pay_platinum')
	{
		$_GET['period_id'] = ($user[ AUTH_GENDER ] == GENDER_MALE) ? MM_PLATINUM_GUY_PERIOD_ID : MM_PLATINUM_LADY_PERIOD_ID;
	}
	elseif ($sel == 'pay_installments')
	{
		$smarty->assign('pay_installments', 1);
		
		if (!empty($row['mm_first_installment_date']) && !empty($row['mm_second_installment_date'])) {
			$_GET['period_id'] = MM_PLATINUM_LADY_THIRD_INS_PERIOD_ID;
		} elseif (!empty($row['mm_first_installment_date']) && empty($row['mm_second_installment_date'])) {
			$_GET['period_id'] = MM_PLATINUM_LADY_SECOND_INS_PERIOD_ID;
		} elseif (empty($row['mm_first_installment_date']) && empty($row['mm_second_installment_date'])) {
			$_GET['period_id'] = MM_PLATINUM_LADY_FIRST_INS_PERIOD_ID;
		}
	}
	unset($rs, $row);
	//----------------- End ---------------
	//Changed By Narendra for Installments
	
	//echo $_GET['period_id']; die;
	
	$smarty->assign('pay_platinum', 1);
	$smarty->assign('sub_menu_num', '5');
}

// dispatcher
switch ($sel) {
	case 'group':							// prepare + display payment_page_1.tpl (select membership group)
		if (USER_CAN_SELECT_MEMBERSHIP_GROUP) {
			PaymentPage1();					// submit with GET to save_1
		} else {
			PaymentPageNew('', 1);
		}
	break;
	
	case 'save_1':							// display Payment Page 2 based on selection from Payment Page 1
		PaymentPage2();
	break;
		
	case 'pay_platinum':					// display Payment Page 2 with Platinum Upgrade
		PaymentPage2();
	break;
	
	case 'save_2':							// insert transaction record in BILLING_REQUESTS_TABLE
		SavePage2();						// include /include/systems/functions/'.$paysys.'.php' and call MakePayment() to start payment transaction
	break;									// return script: account.php, notify script: /include/payment_request.php?sel=PAYMENT_SYSTEM
	
	case 'credit_pack':						// prepare and display payment_page_2_cp_pack.tpl
		CreditPackPage();					// form is submitted to save_credit_pack, pack_wire_transfer or pack_bank_cheque
	break;
	
	case 'save_credit_pack':				// process buy credit pack, insert transaction record in BILLING_REQUESTS_TABLE
		SaveCreditPackPage();				// include /include/systems/functions/'.$paysys.'.php'
	break;									// call MakePayment() to start payment transaction, with account.php as callback script
	
	case 'update_account':					// prepare + display payment_page_2_cp_custom.tpl (add credits)
		UpdateAccountPage();				// form is submited to save_account, account_wire_transfer or account_bank_cheque
	break;
	
	case 'save_account':					// process add single credits, insert transaction record in BILLING_REQUESTS_TABLE
		SaveAccountPage();					// include /include/systems/functions/'.$paysys.'.php'
	break;									// call MakePayment() to start payment transaction, with account.php as callback script
	
	case 'atm_payment':						// process atm payment form, validate data and process payment similar to save_2
		ATM_Payment();						// insert record in BILLING_REQUESTS_TABLE with status=send
	break;									// admin needs to approve payment, and only then the send request and user record are updated
	
	case 'account_atm_payment':				// process wire transfer form, validate data and process payment similar to save_2
		Account_ATM_Payment();				// insert record in BILLING_REQUESTS_TABLE with status=send
	break;									// admin needs to approve payment, and only then the credit points are added
	
	case 'wire_transfer':					// process wire transfer form, validate data and process payment similar to save_2
		Wire_Transfer();					// insert record in BILLING_REQUESTS_TABLE with status=send
	break;									// admin needs to approve payment, and only then the send request and user record are updated
	
	case 'pack_wire_transfer':				// process wire transfer form, validate data and process payment similar to save_2
		Pack_Wire_Transfer();				// insert record in BILLING_REQUESTS_TABLE with status=send
	break;									// admin needs to approve payment, and only then the credit points are added
											
	case 'account_wire_transfer':			// process wire transfer form, validate data and process payment similar to save_2
		Account_Wire_Transfer();			// insert record in BILLING_REQUESTS_TABLE with status=send
	break;									// admin needs to approve payment, and only then the credit points are added
	
	case 'bank_cheque':						// process bank cheque form, validate data and process payment similar to save_2
		Bank_Cheque();						// insert record in BILLING_REQUESTS_TABLE with status=send
	break;									// admin needs to approve payment, and only then the send request and user record are updated
	
	case 'pack_bank_cheque':				// process bank cheque form, validate data and process payment similar to save_2
		Pack_Bank_Cheque();					// insert record in BILLING_REQUESTS_TABLE with status=send
	break;									// admin needs to approve payment, and only then the send request and the credit points are added
	
	case 'account_bank_cheque':				// process bank cheque form, validate data and process payment similar to save_2
		Account_Bank_Cheque();				// insert record in BILLING_REQUESTS_TABLE with status=send
	break;									// admin needs to approve payment, and only then the send request and the credit points are added
	
	case 'service':							// prepare + display payment_service_lift_up_page.tpl
		ServicePage();						// also handlers for processing the liftup
	break;									// user profile is lifted up in search results
	
	case 'buy_connection':
		PaymentPageNew('', 1);
	break;
	
	case 'newpage2':
		PaymentPageNew('', 2);
	break;
	
	case 'pay_installments':
		PaymentPage2();
	break;
	
	default:
		if (USER_CAN_SELECT_MEMBERSHIP_GROUP) {
			PaymentPage1();
		} else {
			PaymentPageNew('', 1);
		}
	break;
}

exit;


function PaymentPageNew($err = '', $pId = 1)
{
	global $lang, $config, $smarty, $user;
	
	if (in_array($user[ AUTH_ID_GROUP ], array(MM_PLATINUM_LADY_SECOND_INS_ID, MM_PLATINUM_LADY_FIRST_INS_ID))) {
		header('Location: platinum_match.php');
		exit;
	}
	
	// settings
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2', 'use_credits_for_membership_payment'));
	
	$form['use_credits_for_membership_payment'] = $settings['use_credits_for_membership_payment'];
	
	$data['account_currency'] = $settings['site_unit_costunit'];
	$data['account_currency_2'] = $settings['site_unit_costunit_2'];
		
	// handle external messages and errors
	if ($user[ AUTH_IS_APPLICANT ] && isset($_GET['missing']) && $_GET['missing'] == '1') {
		$err = $lang['err']['application_payment_missing'];
	}
	
	if ($err) {
		$form['err'] = $err;
	}
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$form['task'] = 4;
	$form['err'] = $err;
	
	if ($pId == 1) {
		$smarty->assign('buy_connection', true);
	} else {
		$smarty->assign('new_page2', true);
	}
	
	$usr_gender = intval($user[ AUTH_GENDER ]) == GENDER_FEMALE ? 'lady' : 'guy';
	$smarty->assign('usr_gender', $usr_gender);
	
	$smarty->assign('form', $form);
	$smarty->assign('data', $data);
	$smarty->assign('header', $lang['payment']);
	
	if ($user[ AUTH_GENDER ] == GENDER_FEMALE) {
		$smarty->display(TrimSlash($config['index_theme_path']).'/payment_page_1.tpl');
	} else {
		$smarty->display(TrimSlash($config['index_theme_path']).'/payment_page_1_guy.tpl');
	}
	
	exit;
}

function PaymentPage1($err = '')
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	// settings
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2', 'use_credits_for_membership_payment'));
	
	$form['use_credits_for_membership_payment'] = $settings['use_credits_for_membership_payment'];
	
	$data['account_currency'] = $settings['site_unit_costunit'];
	$data['account_currency_2'] = $settings['site_unit_costunit_2'];
		
	$id_user = $user[ AUTH_ID_USER ];
	
	// handle external messages and errors
	if ($user[ AUTH_IS_APPLICANT ] && isset($_GET['missing']) && $_GET['missing'] == '1') {
		$err = $lang['err']['application_payment_missing'];
	}
	
	if ($err) {
		$form['err'] = $err;
	}
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	if ($settings['use_credits_for_membership_payment']) {
		$data['count'] = $dbconn->getOne('SELECT account_curr FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($id_user));
		$data['count'] = number_format(round($data['count'], 2), 2);
	}
	
	$selected_group = isset($_POST['group']) ? intval($_POST['group']) : (isset($_GET['group']) ? intval($_GET['group']) : 0);
	
	$strSQL =
		'SELECT a.id, a.name
		   FROM '.GROUPS_TABLE.' a
	 INNER JOIN '.USER_GROUP_TABLE.' b ON a.id = b.id_group
		  WHERE b.id_user = ?';
		
	$rs = $dbconn->Execute($strSQL, array($id_user));
	$row = $rs->GetRowAssoc(false);
	
	$data['present_group'] = $row['name'];
	$data['selected_name'] = $row['name'];
	
	// group options
	$strWhere = ($config['use_gender_membership']) ? ' AND a.gender = "'.$user[ AUTH_GENDER ].'"' : '';
	
	$strSQL = 
		'SELECT DISTINCT a.id, a.name
		   FROM '.GROUPS_TABLE.' a 
	 INNER JOIN '.GROUP_PERIOD_TABLE.' b ON a.id = b.id_group
		  WHERE a.is_gender_group = "'.$config['use_gender_membership'].'"  
			AND a.type = "f" AND b.status = "1" '.$strWhere.'
	   ORDER BY a.id';
	
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$groups = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$groups[$i]['name'] = $row['name'];
		$groups[$i]['id'] = $row['id'];
		$groups[$i]['sel'] = ($row['id'] == $selected_group) ? '1' : '';
		
		if ($row['id'] == $selected_group) {
			$data['selected_name'] = $row['name'];
		}
		
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('groups', $groups);
	
	if ($selected_group)
	{
		$smarty->assign('selected_group', $selected_group);
		
		// modules
		$strSQL = 
			'SELECT a.name
			   FROM '.MODULES_TABLE.' a
		 INNER JOIN '.GROUP_MODULE_TABLE.' b ON a.id = b.id_module
			  WHERE b.id_group = ?
		   ORDER BY name';
		
		$rs = $dbconn->Execute($strSQL, array($selected_group));
		
		$i = 0;
		$description = array();
		
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$description[$i]['name'] = isset($lang['modules'][$row['name']]['name']) ? $lang['modules'][$row['name']]['name'] : $row['name'];
			$description[$i]['descr'] = isset($lang['modules'][$row['name']]['comment']) ? $lang['modules'][$row['name']]['comment'] : '';
			$rs->MoveNext();
			$i++;
		}
		
		$smarty->assign('description', $description);
		
		unset($rs, $row);
		
		// periods
		$strSQL =
			'SELECT gp.id, gp.amount, gp.period, gp.cost, gp.cost_2, gp.trial_amount, gp.recurring,
					g.name
			   FROM '.GROUP_PERIOD_TABLE.' gp
		 INNER JOIN '.GROUPS_TABLE.' g ON g.id = gp.id_group
			  WHERE gp.id_group = ? AND gp.status = "1" AND gp.upgrade = "0"
		   ORDER BY gp.recurring DESC, gp.amount';
		
		$rs = $dbconn->Execute($strSQL, array($selected_group));
		
		$i = 0;
		$periods = array();
		
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$periods[$i]['id'] = $row['id'];
			$periods[$i]['amount'] = $row['amount'];
			$periods[$i]['period'] = ($row['amount'] == 1) ? $lang['pays']['periods_singular'][$row['period']] : $lang['pays']['periods_plural'][$row['period']];
			$periods[$i]['name'] = $row['name'];
			$periods[$i]['recurring'] = $row['recurring'];
			$periods[$i]['cost'] = $row['cost'];
			$periods[$i]['cost_formatted'] = sprintf('%01.2f', $row['cost']);
			$periods[$i]['cost_2'] = $row['cost_2'];
			$periods[$i]['cost_2_formatted'] = sprintf('%01.2f', $row['cost_2']);
			$periods[$i]['trial_amount'] = $row['trial_amount'];
			$rs->MoveNext();
			$i++;
		}
		
		$smarty->assign('periods', $periods);
		
		unset($rs, $row);
		
		// permissions
		$strSQL =
			'SELECT DISTINCT gp.id, gp.permission_count, p.permission_name
			   FROM '.GROUPS_PERMISSIONS_TABLE.' gp
		  LEFT JOIN '.PERMISSIONS_TABLE.' p ON gp.id_permission = p.id
			  WHERE gp.id_group = ?
		   GROUP BY gp.id
		   ORDER BY gp.id';
			
		$rs = $dbconn->Execute($strSQL, array($selected_group));
		
		$i = 0;
		$add_descr = array();
		
		while (!$rs->EOF) {
			$add_descr[$i]['name'] = $lang['groups']['permissions_name'][$rs->fields[2]];
			$add_descr[$i]['descr'] = $lang['groups']['permissions_comment'][$rs->fields[2]];
			$add_descr[$i]['count'] = $rs->fields[1];
			$add_descr[$i]['add_name'] = $lang['groups']['permissions_value_name'][$rs->fields[2]];
			$rs->MoveNext();
			$i++;
		}
		
		$smarty->assign('add_descr', $add_descr);
		
		unset($rs, $row);
	}
	
	if ($user[ AUTH_IS_APPLICANT ]) {
		$trial_id_period = ($user[ AUTH_GENDER ] == GENDER_MALE) ? MM_TRIAL_GUY_PERIOD_ID : MM_TRIAL_LADY_PERIOD_ID;
		$smarty->assign('trial_id_period', $trial_id_period);
	}
	
	$form['task'] = 4;
	$form['err'] = $err;
	
	$usr_gender = intval($user[ AUTH_GENDER ]) == GENDER_FEMALE ? 'lady' : 'guy';
	$smarty->assign('usr_gender', $usr_gender);
	
	$smarty->assign('form', $form);
	$smarty->assign('data', $data);
	$smarty->assign('header', $lang['payment']);
	
	if ($user[ AUTH_GENDER ] == GENDER_FEMALE) {
		$smarty->display(TrimSlash($config['index_theme_path']).'/payment_page_1.tpl');
	} else {
		$smarty->display(TrimSlash($config['index_theme_path']).'/payment_page_1_guy.tpl');
	}
	
	exit;
}


function PaymentPage2($err = '', $paysystem = 'online_payment')
{
	global $lang, $config, $config_admin, $smarty, $dbconn, $user;
	
	// check for admin, guest or invalid id_user
	if (!$user[ AUTH_ID_USER ] || $user[ AUTH_GUEST ] || $user[ AUTH_ROOT ]) {
		header('location: '.$config['site_root'].'/index.php');
		exit;
	}
	
	if (empty($_GET['period_id'])) {
		// start from scratch
		if (USER_CAN_SELECT_MEMBERSHIP_GROUP) {
			header('location: '.$config['site_root'].'/payment.php');
		} else {
			header('location: '.$config['site_root'].'/payment.php?sel=buy_connection');
		}
		exit;
	}
	
	$id_user = (int) $user[ AUTH_ID_USER ];
	$id_period = (int) $_GET['period_id'];
	
	//echo $id_period; die;
	
	// following periods can't be bought
	if ($id_period == MM_TRIAL_GUY_PERIOD_ID || $id_period == MM_TRIAL_LADY_PERIOD_ID || $id_period == MM_REGULAR_GUY_PERIOD_ID) {
		// start from scratch
		if (USER_CAN_SELECT_MEMBERSHIP_GROUP) {
			header('location: '.$config['site_root'].'/payment.php');
		} else {
			header('location: '.$config['site_root'].'/payment.php?sel=buy_connection');
		}
		exit;
	}
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	// settings
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2', 'use_credits_for_membership_payment'));
	
	$form['use_credits_for_membership_payment'] = (int) $settings['use_credits_for_membership_payment'];
	
	// data
	$data = $_POST;
	
	$data['account_currency'] = $settings['site_unit_costunit'];
	$data['account_currency_2'] = $settings['site_unit_costunit_2'];
	$data['gender'] = $user[ AUTH_GENDER ];
	
	// get credit count
	if ($settings['use_credits_for_membership_payment']) {
		$data['count'] = $dbconn->getOne('SELECT account_curr FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($id_user));
		$data['count'] = round($data['count'], 2);
		$data['count_formatted'] = number_format($data['count'], 2);
		
		// handling two currencies for the credits is not supported yet
		$data['count_2'] = 0;
		$data['count_2_formatted'] = number_format($data['count_2'], 2);
	}
	
	// get level name, id, cost, period and amount
	$strSQL =
		'SELECT a.id, a.name, b.amount, b.period, b.cost, b.cost_2, b.recurring, b.trial_amount
		   FROM '.GROUPS_TABLE.' a
	 INNER JOIN '.GROUP_PERIOD_TABLE.' b ON a.id = b.id_group 
		  WHERE b.id = ? AND b.status = "1" AND a.gender = ?';
	
	$rs = $dbconn->Execute($strSQL, array($id_period, (string)$user[ AUTH_GENDER ]));
	$row = $rs->GetRowAssoc(false);
	
	if (empty($row['id'])) {
		// start from scratch
		if (USER_CAN_SELECT_MEMBERSHIP_GROUP) {
			header('location: '.$config['site_root'].'/payment.php');
		} else {
			header('location: '.$config['site_root'].'/payment.php?sel=buy_connection');
		}
		exit;
	}
	
	$data['chosen_group_id'] = $row['id'];
	$data['chosen_group'] = $row['name'] ? $row['name'] : '';
	$data['chosen_period_id'] = $id_period;
	$data['chosen_amount'] = $row['amount'];
	$data['chosen_period'] = ($row['amount'] == 1) ? $lang['pays']['periods_singular'][$row['period']] : $lang['pays']['periods_plural'][$row['period']];
	$data['chosen_period_text'] = $row['amount'].' '.$data['chosen_period'];
	$data['chosen_recurring'] = (int) $row['recurring'];
	$data['chosen_trial_amount'] = $row['trial_amount'];
	
	$data['chosen_cost'] = $row['cost'];
	$data['chosen_cost_formatted'] = sprintf('%02.2f', $data['chosen_cost']);
	
	$data['chosen_cost_2'] = $row['cost_2'];
	$data['chosen_cost_2_formatted'] = sprintf('%02.2f', $data['chosen_cost_2']);
	
	if ($settings['use_credits_for_membership_payment']) {
		$data['chosen_forpay'] = $row['cost'] - $data['count'];
		$data['chosen_forpay_2'] = $row['cost_2'] - $data['count_2'];
	} else {
		$data['chosen_forpay'] = $row['cost'];
		$data['chosen_forpay_2'] = $row['cost_2'];
	}
	
	if ($data['chosen_forpay'] <= 0) {
		// if credits account is higher than group costs, make upgrade without payment process
		$data['chosen_forpay'] = $data['chosen_forpay_2'] = 0;
	}
	
	$data['chosen_forpay_formatted'] = sprintf('%02.2f', $data['chosen_forpay']);
	$data['chosen_forpay_2_formatted'] = sprintf('%02.2f', $data['chosen_forpay_2']);
	
	// current and new expiration date
	if (!$user[ AUTH_IS_APPLICANT ] && ! $data['chosen_recurring'])
	{
		$period_days = $row['amount'] * $config_admin['pay_period'][ $row['period'] ];
		
		if ($user[ AUTH_IS_TRIAL ]) {
			$data['new_expiry_date'] = strftime('%B %d, %Y', time() + $period_days * 24*60*60);
		} else {
			$expiry_timestamp = $dbconn->GetOne('SELECT UNIX_TIMESTAMP(date_end) FROM '.BILLING_USER_PERIOD_TABLE.' WHERE id_user = ?', array($id_user));
			$data['expiry_date'] = strftime('%B %d, %Y', $expiry_timestamp);
			if ($expiry_timestamp < time()) {
				$expiry_timestamp = time();
				$data['expired'] = true;
			}
			$data['new_expiry_date'] = strftime('%B %d, %Y', $expiry_timestamp + $period_days * 24*60*60);
		}
	}
	
	unset($rs, $row);
	
	// error message
	if ($err) {
		$form['err'] = $err;
	}
	
	// get active payment services
	$rs = $dbconn->Execute('SELECT name, template_name FROM '.BILLING_PAYSYSTEMS_TABLE.' WHERE used = "1"');
	
	$i = 0;
	$paysys = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$paysys[$i]['name'] = $row['name'];
		$paysys[$i]['template_name'] = $row['template_name'];
		$rs->MoveNext();
		$i++;
	}
	
	unset($rs, $row);
	
	$usr_gender = intval($user[ AUTH_GENDER ]) == GENDER_FEMALE ? 'lady' : 'guy';
	$smarty->assign('usr_gender', $usr_gender);
	
	// pass data to template
	$smarty->assign('paysys', $paysys);
	$smarty->assign('paysystem', $paysystem);
	$smarty->assign('form', $form);
	$smarty->assign('data', $data);
	$smarty->assign('err', $lang['err']);
	
	// display template
	$smarty->display(TrimSlash($config['index_theme_path']).'/payment_page_2.tpl');
	exit;
}


function CreditPackPage($err = '', $paysystem = 'online_payment')
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	// check for admin, guest or invalid id_user
	if (!$user[ AUTH_ID_USER ] || $user[ AUTH_GUEST ] || $user[ AUTH_ROOT ]) {
		header('location: '.$config['site_root'].'/index.php');
		exit;
	}
	
	if (empty($_GET['pack'])) {
		// start from scratch
		header('location: '.$config['site_root'].'/payment.php?sel=buy_connection');
		exit;
	}
	
	if ($user[ AUTH_GENDER ] != GENDER_MALE) {
		// start from scratch
		header('location: '.$config['site_root'].'/payment.php?sel=buy_connection');
		exit;
	}
	
	switch ($_GET['pack']) {
		case 'bronze': $id_pack = 1; break;
		case 'silver': $id_pack = 2; break;
		case 'gold': $id_pack = 3; break;
		default: $id_pack = 1; break;
	}
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	// settings
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2'));
	
	// data
	$data = $_POST;
	
	$data['pack'] = $_GET['pack'];
	
	$data['account_currency'] = $settings['site_unit_costunit'];
	
	// only for guys in USD
	#$data['account_currency_2'] = $settings['site_unit_costunit_2'];
	
	$data['gender'] = $user[ AUTH_GENDER ];
	
	// get pack name, credit points and cost
	$strSQL = 'SELECT id, name, points, cost FROM '.CREDIT_POINT_PACKS_TABLE.' WHERE id = ?';
	
	$rs = $dbconn->Execute($strSQL, array($id_pack));
	$row = $rs->GetRowAssoc(false);
	
	if (empty($row['id'])) {
		// start from scratch
		header('location: '.$config['site_root'].'/payment.php?sel=buy_connection');
		exit;
	}
	
	$data['pack_id'] = $row['id'];
	$data['pack_name'] = $lang['payment']['guy']['buycon_bot_head'][ $row['id'] ];
	$data['pack_points'] = $row['points'];
	$data['pack_cost'] = $row['cost'];
	$data['pack_off'] = 100 - round($row['cost'] / $row['points'] * 100);
	$data['pack_save'] = $row['points'] - $row['cost'];
	$data['pack_cost_formatted'] = sprintf('%02.2f', $data['pack_cost']);
	
	// only for guys in USD
	#$data['chosen_cost_2'] = $row['cost_2'];
	#$data['chosen_cost_2_formatted'] = sprintf('%02.2f', $data['chosen_cost_2']);
	
	unset($rs, $row);
	
	$form = array();
	
	// error message
	if ($err) {
		$form['err'] = $err;
	}
	
	// get active payment services
	$rs = $dbconn->Execute('SELECT name, template_name FROM '.BILLING_PAYSYSTEMS_TABLE.' WHERE used = "1"');
	
	$i = 0;
	$paysys = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$paysys[$i]['name'] = $row['name'];
		$paysys[$i]['template_name'] = $row['template_name'];
		$rs->MoveNext();
		$i++;
	}
	
	unset($rs, $row);
	
	#$usr_gender = intval($user[ AUTH_GENDER ]) == GENDER_FEMALE ? 'lady' : 'guy';
	#$smarty->assign('usr_gender', $usr_gender);
	
	// pass data to template
	$smarty->assign('paysys', $paysys);
	$smarty->assign('paysystem', $paysystem);
	$smarty->assign('form', $form);
	$smarty->assign('data', $data);
	$smarty->assign('err', $lang['err']);
	
	// display template
	$smarty->display(TrimSlash($config['index_theme_path']).'/payment_page_2_cp_pack.tpl');
	exit;
}


function ATM_Payment()
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	// id_user
	$id_user = $user[ AUTH_ID_USER ];
	
	// settings
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2', 'use_credits_for_membership_payment'));
	
	$err = '';
	
	// check checkboxes
	if (empty($_POST['atm_cb_1']) || empty($_POST['atm_cb_2']) || empty($_POST['atm_cb_3']) || empty($_POST['atm_cb_4'])) {
		$err .= '* '.$lang['err']['atm_checkboxes'].'<br />';
	}
	
	// evaluate post data
	$id_group = (int) $_POST['group_id'];
	$id_period = (int) $_POST['period_id'];
	$atm_payamount = (float) str_replace('$', '', FormFilter($_POST['atm_payamount']));
	$atm_year = (int) $_POST['atm_Year'];
	$atm_month = (int) $_POST['atm_Month'];
	$atm_day = (int) $_POST['atm_Day'];
	$atm_hour = (int) $_POST['atm_Hour'];
	$atm_minute = (int) $_POST['atm_Minute'];
	
	// check date
	if (checkdate($atm_month, $atm_day, $atm_year) && $atm_hour >= 0 && $atm_hour <= 23 && $atm_minute >= 0 && $atm_minute <= 59) {
		$atm_datetime = sprintf("%04d-%02d-%02d %02d:%02d:00", $atm_year, $atm_month, $atm_day, $atm_hour, $atm_minute);
		$smarty->assign('atm_datetime', $atm_datetime);
	} else {
		$err .= '* '.$lang['err']['atm_datetime'].'<br />';
	}
	
	// get product data
	$rs = $dbconn->Execute('SELECT cost, cost_2 FROM '.GROUP_PERIOD_TABLE.' WHERE id = ?', array($id_period));
	$row = $rs->GetRowAssoc(false);
	
	$cost = $row['cost'];
	$cost_2 = $row['cost_2'];
	
	unset($rs, $row);
	
	$product_name = getProductName($id_period);
	
	$currency = $cost_2 ? $settings['site_unit_costunit_2'] : $settings['site_unit_costunit'];
	
	// calculate forpay
	if ($settings['use_credits_for_membership_payment']) {
		$credits = $dbconn->getOne('SELECT account_curr FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($id_user));
		$credits = round($credits, 2);
		// handling two currencies for site credits is not supported yet
		$credits_2 = 0;
		$forpay = $cost_2 ? $cost_2 - $credits_2 : $cost - $credits;
		if ($forpay < 0) {
			$forpay = 0;
		}
	} else {
		$forpay = $cost_2 ? $cost_2 : $cost;
	}
	
	// check payamount
	if (empty($_POST['atm_payamount'])) {
		$err .= '* '.$lang['err']['atm_amount_missing'].'<br />';
	} elseif ($forpay != $atm_payamount) {
		$err .= '* '.$lang['err']['atm_amount_incorrect'].'<br />';
	}
	
	// re-display payment form on error
	if (isset($err) && $err) {
		$_GET['period_id'] = $id_period;
		PaymentPage2($err, 'atm_payment');
		exit;
	}
	
	// create billing request record with status=send
	$dbconn->Execute(
		'INSERT INTO '.BILLING_REQUESTS_TABLE.' SET
			id_user = ?, amount = ?, currency = ?, cost = ?, cost_2 = ?, id_group = ?, id_product = ?, date_send = ?,
			status = "send", paysystem = "atm_payment", recurring = "0", product_name = ?',
		array($id_user, $atm_payamount, $currency, $cost, $cost_2, $id_group, $id_period, $atm_datetime, $product_name));
	
	$id = $dbconn->Insert_ID();
	
	// send message to user and admin
	OfflinePaymentSent_User_Message('atm_payment', $currency, $atm_payamount, $id_period); //$id_period updated by Narendra
	OfflinePaymentSent_Admin_Message('atm_payment', $currency, $atm_payamount);
	
	// redirect to appropriate page with success message trigger
	if ($id_period == MM_PLATINUM_GUY_PERIOD_ID || $id_period == MM_PLATINUM_LADY_PERIOD_ID) {
		echo '<script>location.href="'.$config['site_root'].'/account.php?from=payment&id='.$id.'"</script>';
		exit;
	}
	
	echo '<script>location.href="'.$config['site_root'].'/account.php?from=payment&id='.$id.'"</script>';
	exit;
}

function Account_ATM_Payment()
{
	global $lang, $smarty, $dbconn, $user;
	
	// id_user
	$id_user = $user[ AUTH_ID_USER ];
	
	// settings
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2'));
	
	$err = '';
	
	// check amount
	$amount = (float) FormFilter($_POST['account_to_add']);
	
	if ($amount <= 0){
		$err .= '* '.$lang['err']['update_account_err'].'<br />';
	}
	
	// check checkboxes
	if (empty($_POST['atm_cb_1']) || empty($_POST['atm_cb_2']) || empty($_POST['atm_cb_3']) || empty($_POST['atm_cb_4'])) {
		$err .= '* '.$lang['err']['atm_checkboxes'].'<br />';
	}
	
	// evaluate post data
	$atm_payamount = (float) str_replace('$', '', FormFilter($_POST['atm_payamount']));
	$atm_year = (int) $_POST['atm_Year'];
	$atm_month = (int) $_POST['atm_Month'];
	$atm_day = (int) $_POST['atm_Day'];
	$atm_hour = (int) $_POST['atm_Hour'];
	$atm_minute = (int) $_POST['atm_Minute'];
	
	// check date
	if (checkdate($atm_month, $atm_day, $atm_year) && $atm_hour >= 0 && $atm_hour <= 23 && $atm_minute >= 0 && $atm_minute <= 59) {
		$atm_datetime = sprintf("%04d-%02d-%02d %02d:%02d:00", $atm_year, $atm_month, $atm_day, $atm_hour, $atm_minute);
		$smarty->assign('atm_datetime', $atm_datetime);
	} else {
		$err .= '* '.$lang['err']['atm_datetime'].'<br />';
	}
	
	// check payamount
	if (empty($_POST['atm_payamount'])) {
		$err .= '* '.$lang['err']['atm_amount_missing'].'<br />';
	} elseif ($amount != $atm_payamount) {
		$err .= '* '.$lang['err']['atm_amount_incorrect'].'<br />';
	}
	
	// re-display payment form on error
	if (isset($err) && $err) {
		UpdateAccountPage($err, 'atm_payment');
		exit;
	}
	
	// only USD for now
	$amount_2 = 0;
	
	// only USD for now
	$currency = $settings['site_unit_costunit'];
	
	# THB as second currency
	# $currency = $cost_2 ? $settings['site_unit_costunit_2'] : $settings['site_unit_costunit'];
	
	// get product_name
	$product_name = $lang['payment']['update_account_page']['to_paysystem'];
	
	// create billing request record with status=send
	$dbconn->Execute(
		'INSERT INTO '.BILLING_REQUESTS_TABLE.' SET
			id_user = ?, amount = ?, currency = ?, cost = ?, cost_2 = ?, id_group = ?, id_product = "0", date_send = ?,
			status = "send", paysystem = "atm_payment", recurring = "0", product_name = ?',
		array($id_user, $atm_payamount, $currency, $amount, $amount_2, PG_SINGLE_CREDIT_POINTS, $atm_datetime, $product_name));
	
	$id = $dbconn->Insert_ID();
	
	// send message to user and admin
	OfflinePaymentSent_User_Message('atm_payment', $currency, $atm_payamount);
	OfflinePaymentSent_Admin_Message('atm_payment', $currency, $atm_payamount);
	
	// redirect to account page
	header('location: account.php?from=payment&id='.$id);
	exit;
}


function Wire_Transfer()
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	// id_user
	$id_user = $user[ AUTH_ID_USER ];
	
	// settings
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2', 'use_credits_for_membership_payment'));
	
	$err = '';
	
	// check checkboxes
	if (empty($_POST['wire_cb_1']) || empty($_POST['wire_cb_2']) || empty($_POST['wire_cb_3'])) {
		$err .= '* '.$lang['err']['wire_checkboxes'].'<br />';
	}
	
	// evaluate post data
	$id_group = (int) $_POST['group_id'];
	$id_period = (int) $_POST['period_id'];
	$wire_payamount = (float) str_replace('$', '', FormFilter($_POST['wire_payamount']));
	$wire_year = (int) $_POST['wire_Year'];
	$wire_month = (int) $_POST['wire_Month'];
	$wire_day = (int) $_POST['wire_Day'];
	$wire_hour = (int) $_POST['wire_Hour'];
	$wire_minute = (int) $_POST['wire_Minute'];
	
	// check date
	if (checkdate($wire_month, $wire_day, $wire_year) && $wire_hour >= 0 && $wire_hour <= 23 && $wire_minute >= 0 && $wire_minute <= 59) {
		$wire_datetime = sprintf("%04d-%02d-%02d %02d:%02d:00", $wire_year, $wire_month, $wire_day, $wire_hour, $wire_minute);
		$smarty->assign('wire_datetime', $wire_datetime);
	} else {
		$err .= '* '.$lang['err']['wire_datetime'].'<br />';
	}
	
	// get product data
	$rs = $dbconn->Execute('SELECT cost, cost_2 FROM '.GROUP_PERIOD_TABLE.' WHERE id = ?', array($id_period));
	$row = $rs->GetRowAssoc(false);
	
	$cost = $row['cost'];
	$cost_2 = $row['cost_2'];
	
	unset($rs, $row);
	
	$product_name = getProductName($id_period);
	
	$currency = $cost_2 ? $settings['site_unit_costunit_2'] : $settings['site_unit_costunit'];
	
	// calculate forpay
	if ($settings['use_credits_for_membership_payment']) {
		$credits = $dbconn->getOne('SELECT account_curr FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($id_user));
		$credits = round($credits, 2);
		// handling two currencies for the credits is not supported yet
		$credits_2 = 0;
		$forpay = $cost_2 ? $cost_2 - $credits_2 : $cost - $credits;
		if ($forpay < 0) {
			$forpay = 0;
		}
	} else {
		$forpay = $cost_2 ? $cost_2 : $cost;
	}
	
	// check payamount
	if (empty($_POST['wire_payamount'])) {
		$err .= '* '.$lang['err']['wire_amount_missing'].'<br />';
	} elseif ($forpay != $wire_payamount) {
		$err .= '* '.$lang['err']['wire_amount_incorrect'].'<br />';
	}
	
	// check transfer no.
	if (empty($_POST['wire_transfer_no'])) {
		$err .= '* '.$lang['err']['wire_transfer_no'].'<br />';
	}
	
	// re-display payment form on error
	if (isset($err) && $err) {
		$_GET['period_id'] = $id_period;
		PaymentPage2($err, 'wire_transfer');
		exit;
	}
	
	// get info
	$info = 'No. '.trim($_POST['wire_transfer_no']);
	
	// create billing request record with status=send
	$dbconn->Execute(
		'INSERT INTO '.BILLING_REQUESTS_TABLE.' SET
			id_user = ?, amount = ?, currency = ?, cost = ?, cost_2 = ?, id_group = ?, id_product = ?, date_send = ?,
			status = "send", paysystem = "wire_transfer", recurring = "0", product_name = ?, info = ?',
		array($id_user, $wire_payamount, $currency, $cost, $cost_2, $id_group, $id_period, $wire_datetime, $product_name, $info));
	
	$id = $dbconn->Insert_ID();
	
	// send message to user and admin
	OfflinePaymentSent_User_Message('wire_transfer', $currency, $wire_payamount, $id_period);
	OfflinePaymentSent_Admin_Message('wire_transfer', $currency, $wire_payamount);
	
	// redirect to appropriate page with success message trigger
	if ($id_period == MM_PLATINUM_GUY_PERIOD_ID || $id_period == MM_PLATINUM_LADY_PERIOD_ID) {
		echo '<script>location.href="'.$config['site_root'].'/account.php?from=payment&id='.$id.'"</script>';
		exit;
	}
	
	echo '<script>location.href="'.$config['site_root'].'/account.php?from=payment&id='.$id.'"</script>';
	exit;
}


function Pack_Wire_Transfer()
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	// id_user
	$id_user = $user[ AUTH_ID_USER ];
	
	// settings
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2'));
	
	$err = '';
	
	// check checkboxes
	if (empty($_POST['wire_cb_1']) || empty($_POST['wire_cb_2']) || empty($_POST['wire_cb_3'])) {
		$err .= '* '.$lang['err']['wire_checkboxes'].'<br />';
	}
	
	// evaluate post data
	$id_pack = (int) $_POST['pack_id'];
	
	$wire_payamount = (float) str_replace('$', '', FormFilter($_POST['wire_payamount']));
	
	$wire_year = (int) $_POST['wire_Year'];
	$wire_month = (int) $_POST['wire_Month'];
	$wire_day = (int) $_POST['wire_Day'];
	$wire_hour = (int) $_POST['wire_Hour'];
	$wire_minute = (int) $_POST['wire_Minute'];
	
	// check date
	if (checkdate($wire_month, $wire_day, $wire_year) && $wire_hour >= 0 && $wire_hour <= 23 && $wire_minute >= 0 && $wire_minute <= 59) {
		$wire_datetime = sprintf("%04d-%02d-%02d %02d:%02d:00", $wire_year, $wire_month, $wire_day, $wire_hour, $wire_minute);
		$smarty->assign('wire_datetime', $wire_datetime);
	} else {
		$err .= '* '.$lang['err']['wire_datetime'].'<br />';
	}
	
	// get product data
	$rs = $dbconn->Execute('SELECT name, points, cost FROM '.CREDIT_POINT_PACKS_TABLE.' WHERE id = ?', array($id_pack));
	$row = $rs->GetRowAssoc(false);
	
	$product_name = $row['name'].' Pack ('.$row['points'].' Credit Points)';
	$cost = $row['cost'];
	$cost_2 = 0;
	
	unset($rs, $row);
	
	$currency = $cost_2 ? $settings['site_unit_costunit_2'] : $settings['site_unit_costunit'];
	
	// calculate forpay
	$forpay = $cost_2 ? $cost_2 : $cost;
	
	// check payamount
	if (empty($_POST['wire_payamount'])) {
		$err .= '* '.$lang['err']['wire_amount_missing'].'<br />';
	} elseif ($forpay != $wire_payamount) {
		$err .= '* '.$lang['err']['wire_amount_incorrect'].'<br />';
	}
	
	// check transfer no.
	if (empty($_POST['wire_transfer_no'])) {
		$err .= '* '.$lang['err']['wire_transfer_no'].'<br />';
	}
	
	// re-display payment form on error
	if ($err) {
		$pack = $dbconn->GetOne('SELECT name FROM '.CREDIT_POINT_PACKS_TABLE.' WHERE id = ?', array($id_pack));
		$_GET['pack'] = strtolower($pack);
		CreditPackPage($err, 'wire_transfer');
		exit;
	}
	
	// get info
	$info = 'No. '.trim($_POST['wire_transfer_no']);
	
	// create billing request record with status=send
	$dbconn->Execute(
		'INSERT INTO '.BILLING_REQUESTS_TABLE.' SET
			id_user = ?, amount = ?, currency = ?, cost = ?, cost_2 = ?, id_group = ?, id_product = ?, date_send = ?,
			status = "send", paysystem = "wire_transfer", recurring = "0", product_name = ?, info = ?',
		array($id_user, $wire_payamount, $currency, $cost, $cost_2, PG_CREDIT_POINTS_PACK, $id_pack, $wire_datetime, $product_name, $info));
	
	$id = $dbconn->Insert_ID();
	
	// send message to user and admin
	OfflinePaymentSent_User_Message('wire_transfer', $currency, $wire_payamount);
	OfflinePaymentSent_Admin_Message('wire_transfer', $currency, $wire_payamount);
	
	// redirect to appropriate page with success message trigger
	echo '<script>location.href="'.$config['site_root'].'/account.php?from=payment&id='.$id.'"</script>';
	exit;
}


function Account_Wire_Transfer()
{
	global $lang, $smarty, $dbconn, $user;
	
	// id_user
	$id_user = $user[ AUTH_ID_USER ];
	
	// settings
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2'));
	
	$err = '';
	
	// check amount
	$amount = (float) FormFilter($_POST['account_to_add']);
	
	if ($amount <= 0){
		$err .= '* '.$lang['err']['update_account_err'].'<br />';
	}
	
	// check checkboxes
	if (empty($_POST['wire_cb_1']) || empty($_POST['wire_cb_2']) || empty($_POST['wire_cb_3'])) {
		$err .= '* '.$lang['err']['wire_checkboxes'].'<br />';
	}
	
	// evaluate post data
	$wire_payamount = (float) str_replace('$', '', FormFilter($_POST['wire_payamount']));
	$wire_year = (int) $_POST['wire_Year'];
	$wire_month = (int) $_POST['wire_Month'];
	$wire_day = (int) $_POST['wire_Day'];
	$wire_hour = (int) $_POST['wire_Hour'];
	$wire_minute = (int) $_POST['wire_Minute'];
	
	// check date
	if (checkdate($wire_month, $wire_day, $wire_year) && $wire_hour >= 0 && $wire_hour <= 23 && $wire_minute >= 0 && $wire_minute <= 59) {
		$wire_datetime = sprintf("%04d-%02d-%02d %02d:%02d:00", $wire_year, $wire_month, $wire_day, $wire_hour, $wire_minute);
		$smarty->assign('wire_datetime', $wire_datetime);
	} else {
		$err .= '* '.$lang['err']['wire_datetime'].'<br />';
	}
	
	// check payamount
	if (empty($_POST['wire_payamount'])) {
		$err .= '* '.$lang['err']['wire_amount_missing'].'<br />';
	} elseif ($amount != $wire_payamount) {
		$err .= '* '.$lang['err']['wire_amount_incorrect'].'<br />';
	}
	
	// check transfer no.
	if (empty($_POST['wire_transfer_no'])) {
		$err .= '* '.$lang['err']['wire_transfer_no'].'<br />';
	}
	
	// re-display payment form on error
	if (isset($err) && $err) {
		UpdateAccountPage($err, 'wire_transfer');
		exit;
	}
	
	// only USD for now
	$amount_2 = 0;
	
	// only USD for now
	$currency = $settings['site_unit_costunit'];
	
	# THB as second currency
	# $currency = $cost_2 ? $settings['site_unit_costunit_2'] : $settings['site_unit_costunit'];
	
	// get product_name
	$product_name = $lang['payment']['update_account_page']['to_paysystem'];
	
	// additional info
	$info = 'No. '.trim($_POST['wire_transfer_no']);
	
	// create billing request record with status=send
	$dbconn->Execute(
		'INSERT INTO '.BILLING_REQUESTS_TABLE.' SET
			id_user = ?, amount = ?, currency = ?, cost = ?, cost_2 = ?, id_group = ?, id_product = "0", date_send = ?,
			status = "send", paysystem = "wire_transfer", recurring = "0", product_name = ?, info = ?',
		array($id_user, $wire_payamount, $currency, $amount, $amount_2, PG_SINGLE_CREDIT_POINTS, $wire_datetime, $product_name, $info));
	
	$id = $dbconn->Insert_ID();

	// send message to user and admin
	OfflinePaymentSent_User_Message('wire_transfer', $currency, $wire_payamount);
	OfflinePaymentSent_Admin_Message('wire_transfer', $currency, $wire_payamount);
	
	// redirect to account page
	header('location: account.php?from=payment&id='.$id);
	exit;
}


function Bank_Cheque()
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	$err = '';
	
	// id_user
	$id_user = $user[ AUTH_ID_USER ];
	
	// settings
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2', 'use_credits_for_membership_payment'));
	
	// check checkboxes
	if (empty($_POST['cheque_cb_1']) || empty($_POST['cheque_cb_2']) || empty($_POST['cheque_cb_3'])) {
		$err .= '* '.$lang['err']['cheque_checkboxes'].'<br />';
	}
	
	// evaluate post data
	$id_group = (int) $_POST['group_id'];
	$id_period = (int) $_POST['period_id'];
	$cheque_payamount = (float) str_replace('$', '', FormFilter($_POST['cheque_payamount']));
	$cheque_year = (int) $_POST['cheque_Year'];
	$cheque_month = (int) $_POST['cheque_Month'];
	$cheque_day = (int) $_POST['cheque_Day'];
	$cheque_hour = (int) $_POST['cheque_Hour'];
	$cheque_minute = (int) $_POST['cheque_Minute'];
	
	// check date
	if (checkdate($cheque_month, $cheque_day, $cheque_year) && $cheque_hour >= 0 && $cheque_hour <= 23 && $cheque_minute >= 0 && $cheque_minute <= 59) {
		$cheque_datetime = sprintf("%04d-%02d-%02d %02d:%02d:00", $cheque_year, $cheque_month, $cheque_day, $cheque_hour, $cheque_minute);
		$smarty->assign('cheque_datetime', $cheque_datetime);
	} else {
		$err .= '* '.$lang['err']['cheque_datetime'].'<br />';
	}
	
	// get product data
	$rs = $dbconn->Execute('SELECT cost, cost_2 FROM '.GROUP_PERIOD_TABLE.' WHERE id = ?', array($id_period));
	$row = $rs->GetRowAssoc(false);
	
	$cost = $row['cost'];
	$cost_2 = $row['cost_2'];
	
	unset($rs, $row);
	
	$product_name = getProductName($id_period);
	
	$currency = $cost_2 ? $settings['site_unit_costunit_2'] : $settings['site_unit_costunit'];
	
	// calculate forpay
	if ($settings['use_credits_for_membership_payment']) {
		$credits = $dbconn->getOne('SELECT account_curr FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($id_user));
		$credits = round($credits, 2);
		// handling two currencies for the credits is not supported yet
		$credits_2 = 0;
		$forpay = $cost_2 ? $cost_2 - $credits_2 : $cost - $credits;
		if ($forpay < 0) {
			$forpay = 0;
		}
	} else {
		$forpay = $cost_2 ? $cost_2 : $cost;
	}
	
	// check payamount
	if (empty($_POST['cheque_payamount'])) {
		$err .= '* '.$lang['err']['cheque_amount_missing'].'<br />';
	} elseif ($forpay != $cheque_payamount) {
		$err .= '* '.$lang['err']['cheque_amount_incorrect'].'<br />';
	}
	
	// check bank name
	if (empty($_POST['cheque_bank_name'])) {
		$err .= '* '.$lang['err']['cheque_bank_name'].'<br />';
	}
	
	// re-display payment form on error
	if (isset($err) && $err) {
		$_GET['period_id'] = $id_period;
		PaymentPage2($err, 'bank_cheque');
		exit;
	}
	
	// get info
	$info = trim($_POST['cheque_bank_name']);
	
	// create billing request record with status=send
	$dbconn->Execute(
		'INSERT INTO '.BILLING_REQUESTS_TABLE.' SET
			id_user = ?, amount = ?, currency = ?, cost = ?, cost_2 = ?, id_group = ?, id_product = ?, date_send = ?,
			status = "send", paysystem = "bank_cheque", recurring = "0", product_name = ?, info = ?',
		array($id_user, $cheque_payamount, $currency, $cost, $cost_2, $id_group, $id_period, $cheque_datetime, $product_name, $info));
	
	$id = $dbconn->Insert_ID();

	// send message to user and admin
	OfflinePaymentSent_User_Message('bank_cheque', $currency, $cheque_payamount);
	OfflinePaymentSent_Admin_Message('bank_cheque', $currency, $cheque_payamount);
	
	// redirect to appropriate page with success message trigger
	if ($id_period == MM_PLATINUM_GUY_PERIOD_ID || $id_period == MM_PLATINUM_LADY_PERIOD_ID) {
		echo '<script>location.href="'.$config['site_root'].'/account.php?from=payment&id='.$id.'"</script>';
		exit;
	}
	
	echo '<script>location.href="'.$config['site_root'].'/account.php?from=payment&id='.$id.'"</script>';
	exit;
}


function Pack_Bank_Cheque()
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	$err = '';
	
	// id_user
	$id_user = $user[ AUTH_ID_USER ];
	
	// settings
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2'));
	
	// check checkboxes
	if (empty($_POST['cheque_cb_1']) || empty($_POST['cheque_cb_2']) || empty($_POST['cheque_cb_3'])) {
		$err .= '* '.$lang['err']['cheque_checkboxes'].'<br />';
	}
	
	// evaluate post data
	$id_pack = (int) $_POST['pack_id'];
	
	$cheque_payamount = (float) str_replace('$', '', FormFilter($_POST['cheque_payamount']));
	
	$cheque_year = (int) $_POST['cheque_Year'];
	$cheque_month = (int) $_POST['cheque_Month'];
	$cheque_day = (int) $_POST['cheque_Day'];
	$cheque_hour = (int) $_POST['cheque_Hour'];
	$cheque_minute = (int) $_POST['cheque_Minute'];
	
	// check date
	if (checkdate($cheque_month, $cheque_day, $cheque_year) && $cheque_hour >= 0 && $cheque_hour <= 23 && $cheque_minute >= 0 && $cheque_minute <= 59) {
		$cheque_datetime = sprintf("%04d-%02d-%02d %02d:%02d:00", $cheque_year, $cheque_month, $cheque_day, $cheque_hour, $cheque_minute);
		$smarty->assign('cheque_datetime', $cheque_datetime);
	} else {
		$err .= '* '.$lang['err']['cheque_datetime'].'<br />';
	}
	
	// get product data
	$rs = $dbconn->Execute('SELECT name, points, cost FROM '.CREDIT_POINT_PACKS_TABLE.' WHERE id = ?', array($id_pack));
	$row = $rs->GetRowAssoc(false);
	
	$product_name = $row['name'].' Pack ('.$row['points'].' Credit Points)';
	$cost = $row['cost'];
	$cost_2 = 0;
	
	unset($rs, $row);
	
	$currency = $cost_2 ? $settings['site_unit_costunit_2'] : $settings['site_unit_costunit'];
	
	// calculate forpay
	$forpay = $cost_2 ? $cost_2 : $cost;
	
	// check payamount
	if (empty($_POST['cheque_payamount'])) {
		$err .= '* '.$lang['err']['cheque_amount_missing'].'<br />';
	} elseif ($forpay != $cheque_payamount) {
		$err .= '* '.$lang['err']['cheque_amount_incorrect'].'<br />';
	}
	
	// check bank name
	if (empty($_POST['cheque_bank_name'])) {
		$err .= '* '.$lang['err']['cheque_bank_name'].'<br />';
	}
	
	// re-display payment form on error
	if (isset($err) && $err) {
		$pack = $dbconn->GetOne('SELECT name FROM '.CREDIT_POINT_PACKS_TABLE.' WHERE id = ?', array($id_pack));
		$_GET['pack'] = strtolower($pack);
		CreditPackPage($err, 'bank_cheque');
		exit;
	}
	
	// get info
	$info = trim($_POST['cheque_bank_name']);
	
	// create billing request record with status=send
	$dbconn->Execute(
		'INSERT INTO '.BILLING_REQUESTS_TABLE.' SET
			id_user = ?, amount = ?, currency = ?, cost = ?, cost_2 = ?, id_group = ?, id_product = ?, date_send = ?,
			status = "send", paysystem = "bank_cheque", recurring = "0", product_name = ?, info = ?',
		array($id_user, $cheque_payamount, $currency, $cost, $cost_2, PG_CREDIT_POINTS_PACK, $id_pack, $cheque_datetime, $product_name, $info));
	
	$id = $dbconn->Insert_ID();
	
	// send message to user and admin
	OfflinePaymentSent_User_Message('bank_cheque', $currency, $cheque_payamount);
	OfflinePaymentSent_Admin_Message('bank_cheque', $currency, $cheque_payamount);
	
	// redirect to appropriate page with success message trigger
	echo '<script>location.href="'.$config['site_root'].'/account.php?from=payment&'.$id.'"</script>';
	exit;
}


function Account_Bank_Cheque()
{
	global $lang, $smarty, $dbconn, $user;
	
	// id_user
	$id_user = $user[ AUTH_ID_USER ];
	
	// settings
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2'));
	
	$err = '';
	
	// check amount
	$amount = (float) FormFilter($_POST['account_to_add']);
	
	if ($amount <= 0){
		$err .= '* '.$lang['err']['update_account_err'].'<br />';
	}
	
	// check checkboxes
	if (empty($_POST['cheque_cb_1']) || empty($_POST['cheque_cb_2']) || empty($_POST['cheque_cb_3'])) {
		$err .= '* '.$lang['err']['cheque_checkboxes'].'<br />';
	}
	
	// evaluate post data
	$cheque_payamount = (float) str_replace('$', '', FormFilter($_POST['cheque_payamount']));
	$cheque_year = (int) $_POST['cheque_Year'];
	$cheque_month = (int) $_POST['cheque_Month'];
	$cheque_day = (int) $_POST['cheque_Day'];
	$cheque_hour = (int) $_POST['cheque_Hour'];
	$cheque_minute = (int) $_POST['cheque_Minute'];
	
	// check date
	if (checkdate($cheque_month, $cheque_day, $cheque_year) && $cheque_hour >= 0 && $cheque_hour <= 23 && $cheque_minute >= 0 && $cheque_minute <= 59) {
		$cheque_datetime = sprintf("%04d-%02d-%02d %02d:%02d:00", $cheque_year, $cheque_month, $cheque_day, $cheque_hour, $cheque_minute);
		$smarty->assign('cheque_datetime', $cheque_datetime);
	} else {
		$err .= '* '.$lang['err']['cheque_datetime'].'<br />';
	}
	
	// check payamount
	if (empty($_POST['cheque_payamount'])) {
		$err .= '* '.$lang['err']['cheque_amount_missing'].'<br />';
	} elseif ($amount != $cheque_payamount) {
		$err .= '* '.$lang['err']['cheque_amount_incorrect'].'<br />';
	}
	
	// check bank name
	if (empty($_POST['cheque_bank_name'])) {
		$err .= '* '.$lang['err']['cheque_bank_name'].'<br />';
	}
	
	// re-display payment form on error
	if (isset($err) && $err) {
		UpdateAccountPage($err, 'bank_cheque');
		exit;
	}
	
	// only USD for now
	$amount_2 = 0;
	
	// only USD for now
	$currency = $settings['site_unit_costunit'];
	
	# THB as second currency
	# $currency = $cost_2 ? $settings['site_unit_costunit_2'] : $settings['site_unit_costunit'];
	
	// get product_name
	$product_name = $lang['payment']['update_account_page']['to_paysystem'];
	
	// additional info
	$info = trim($_POST['cheque_bank_name']);
	
	// create billing request record with status=send
	$dbconn->Execute(
		'INSERT INTO '.BILLING_REQUESTS_TABLE.' SET
			id_user = ?, amount = ?, currency = ?, cost = ?, cost_2 = ?, id_group = ?, id_product = "0", date_send = ?,
			status = "send", paysystem = "bank_cheque", recurring = "0", product_name = ?, info = ?',
		array($id_user, $cheque_payamount, $currency, $amount, $amount_2, PG_SINGLE_CREDIT_POINTS, $cheque_datetime, $product_name, $info));
	
	$id = $dbconn->Insert_ID();

	// send message to user and admin
	OfflinePaymentSent_User_Message('bank_cheque', $currency, $cheque_payamount);
	OfflinePaymentSent_Admin_Message('bank_cheque', $currency, $cheque_payamount);
	
	// redirect to account page
	header('location: account.php?from=payment&id='.$id);
	exit;
}


function SavePage2()
{
	global $config, $dbconn, $user;
	
	// id_user
	$id_user = $user[ AUTH_ID_USER ];
	
	// settings
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2', 'use_credits_for_membership_payment'));
	
	// evaluate and sanitize post data. we need to use $_REQUEST here, because we link to here from the Registration Homepage
	$id_period = (int) $_REQUEST['period_id'];					// period (group specific)
	$forpay = floatval(FormFilter($_REQUEST['forpay']));		// amount to pay, might be less than cost if part can be paid with credits
	$paysys = trim($_REQUEST['paysys']);						// payment system
	
	// get product data
	$rs = $dbconn->Execute('SELECT id_group, cost, cost_2, recurring FROM '.GROUP_PERIOD_TABLE.' WHERE id = ? AND status = "1"', array($id_period));
	$row = $rs->GetRowAssoc(false);
	
	if (empty($row['id_group'])) {
		// invalid period id, start from scratch
		if (USER_CAN_SELECT_MEMBERSHIP_GROUP) {
			header('location: '.$config['site_root'].'/payment.php');
		} else {
			header('location: '.$config['site_root'].'/payment.php?sel=buy_connection');
		}
		exit;
	}
	
	$id_group = (int) $row['id_group'];
	$cost = $row['cost'];
	$cost_2 = $row['cost_2'];
	$recurring = $row['recurring'];
	
	unset($rs, $row);
	
	$product_name = getProductName($id_period);
	
	// determine currency
	$currency = $cost_2 ? $settings['site_unit_costunit_2'] : $settings['site_unit_costunit'];
	
	// create billing request record
	if ($paysys == 'ccbill')
	{
		// ccbill special: ccbill_cost is used as id_product
		$ccbill_cost = $dbconn->getOne('SELECT cost FROM '.GROUP_PERIOD_TABLE.' WHERE id = ?', array($id_period));
		
		$dbconn->Execute(
			'INSERT INTO '.BILLING_REQUESTS_TABLE.' SET
				id_user = ?, amount = ?, currency = ?, cost = ?, cost_2 = ?, id_group = ?, id_product = ?, date_send = NOW(),
				status = "send", paysystem = "ccbill", recurring = "0", product_name = ?',
			array($id_user, $forpay, $currency, $cost, $cost_2, $id_group, $ccbill_cost, $product_name));
	}
	elseif ($paysys == 'user_account')
	{
		// user pays with credits
		$dbconn->Execute(
			'INSERT INTO '.BILLING_REQUESTS_TABLE.' SET
				id_user = ?, amount = ?, currency = ?, cost = ?, cost_2 = ?, id_group = ?, id_product = ?, date_send = NOW(),
				status = "send", paysystem = "user_account", recurring = "0", product_name = ?',
			array($id_user, $cost, $currency, $cost, $cost_2, $id_group, $id_period, $product_name));
	}
	else
	{
		// regular payment with payment service
		$send_status = $recurring ? 'subscr_send' : 'send';
		
		$dbconn->Execute(
			'INSERT INTO '.BILLING_REQUESTS_TABLE.' SET
				id_user = ?, amount = ?, currency = ?, cost = ?, cost_2 = ?, id_group = ?, id_product = ?, date_send = NOW(),
				status = ?, paysystem = ?, recurring = ?, product_name = ?',
			array($id_user, $forpay, $currency, $cost, $cost_2, $id_group, $id_period, $send_status, $paysys, $recurring, $product_name));
	}
	
	// transaction id
	$id_trunzaction = $dbconn->Insert_ID();
	
	// start payment process
	switch ($paysys)
	{
		case 'user_account':
			
			// pay with credits
			MakeInPayment($id_trunzaction, $id_user);
			
		break;
			
		case 'allopass':
			
			// allopass special
			include_once './include/systems/functions/allopass.php';
			
			Banners(GetRightModulePath(__FILE__));
			IndexHomePage();
			GetActiveUserInfo($user);
			ShowCodePage($id_trunzaction, $id_period);
			
		break;
			
		default:
			
			// normal online payment
			include_once './include/systems/functions/'.$paysys.'.php';
			MakePayment($id_trunzaction, $id_user);
			
		break;
	}
	
	return;
}


function SaveCreditPackPage()
{
	global $dbconn, $user;
	
	// id_user
	$id_user = $user[ AUTH_ID_USER ];
	
	// settings
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2'));
	
	// evaluate and sanitize post data. we need to use $_REQUEST here, because we link to here from the Registration Homepage
	$id_pack = (int) $_REQUEST['pack_id'];						// pack id
	$forpay = floatval(FormFilter($_REQUEST['forpay']));		// amount to pay
	$paysys = trim($_REQUEST['paysys']);						// payment system
	
	// get product data
	$rs = $dbconn->Execute('SELECT name, points, cost FROM '.CREDIT_POINT_PACKS_TABLE.' WHERE id = ?', array($id_pack));
	$row = $rs->GetRowAssoc(false);
	
	$product_name = $row['name'].' Pack ('.$row['points'].' Credit Points)';
	$cost = $row['cost'];
	$cost_2 = 0;
	
	unset($rs, $row);
	
	// determine currency
	$currency = $cost_2 ? $settings['site_unit_costunit_2'] : $settings['site_unit_costunit'];
	
	// create billing request record
	if ($paysys == 'ccbill')
	{
		// ccbill special: ccbill_cost is used as id_product
		$ccbill_cost = $cost;
		
		$dbconn->Execute(
			'INSERT INTO '.BILLING_REQUESTS_TABLE.' SET
				id_user = ?, amount = ?, currency = ?, cost = ?, cost_2 = ?, id_group = ?, id_product = ?, date_send = NOW(),
				status = "send", paysystem = "ccbill", recurring = "0", product_name = ?',
			array($id_user, $forpay, $currency, $cost, $cost_2, PG_CREDIT_POINTS_PACK, $ccbill_cost, $product_name));
	}
	else
	{
		// regular payment with payment service
		$dbconn->Execute(
			'INSERT INTO '.BILLING_REQUESTS_TABLE.' SET
				id_user = ?, amount = ?, currency = ?, cost = ?, cost_2 = ?, id_group = ?, id_product = ?, date_send = NOW(),
				status = "send", paysystem = ?, recurring = "0", product_name = ?',
			array($id_user, $forpay, $currency, $cost, $cost_2, PG_CREDIT_POINTS_PACK, $id_pack, $paysys, $product_name));
	}
	
	// transaction id
	$id_trunzaction = $dbconn->Insert_ID();
	
	// start payment process
	switch ($paysys)
	{
		case 'allopass':
			
			// allopass special
			include_once './include/systems/functions/allopass.php';
			
			Banners(GetRightModulePath(__FILE__));
			IndexHomePage();
			GetActiveUserInfo($user);
			ShowCodePage($id_trunzaction, $id_pack);
			
		break;
			
		default:
			
			// regular payment
			include_once './include/systems/functions/'.$paysys.'.php';
			MakePayment($id_trunzaction, $id_user);
			
		break;
	}
	
	return;
}


function UpdateAccountPage($err = '', $paysystem = 'online_payment')
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	if ($user[ AUTH_IS_APPLICANT ]) {
		header('Location: '.$config['site_root'].'/payment.php');
		exit;
	}
	
	$form = array();
	
	if ($err != '') {
		$form['err'] = $err;
	}
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2', 'use_pilot_module_smspayments'));
	
	$data = $_POST;
	
	$data['account_currency'] = $settings['site_unit_costunit'];
	$data['account_currency_2'] = $settings['site_unit_costunit_2'];
	$data['gender'] = $user[ AUTH_GENDER ];
	
	$data['count'] = $dbconn->GetOne('SELECT account_curr FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($id_user));
	//$data['count'] = number_format(round($data['count'], 2), 2);
	
	$rs = $dbconn->Execute(
		'SELECT id, name, template_name
		   FROM '.BILLING_PAYSYSTEMS_TABLE.'
		  WHERE used = "1" AND template_name != "ccbill" AND template_name != "allopass"');
	
	$paysys = array();
	$i = 0;
	
	while (!$rs->EOF) {
		$paysys[$i]['id'] = $rs->fields[0];
		$paysys[$i]['name'] = $rs->fields[1];
		$paysys[$i]['template_name'] = $rs->fields[2];
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('paysys', $paysys);
	
	// sms payment option
	if (isset($settings['use_pilot_module_smspayments']) && $settings['use_pilot_module_smspayments'] == 1) {
		$rs = $dbconn->Execute('SELECT COUNT(id) FROM '.SMS_SYSTEMS_TABLE.' WHERE used = "1"');
		if ($rs->fields[0] > 0) {
			$smarty->assign('smssystems', 'yes');
		} else {
			$smarty->assign('smssystems', 'empty');
		}
	} else {
		$smarty->assign('smssystems', 'empty');
	}
	
	$usr_gender = intval($user[ AUTH_GENDER ]) == GENDER_FEMALE ? 'lady' : 'guy';
	$smarty->assign('usr_gender', $usr_gender);
	
	$cre_pack = isset($_POST['cre_pack']) ? $_POST['cre_pack'] : (isset($_GET['cre_pack']) ? $_GET['cre_pack'] : '');
	if($cre_pack != '')
	{
		if($cre_pack == 'custom')
		{
			// check amount
			$data['account_to_add'] = (float) FormFilter($_REQUEST['amt']);
		}
	}
	
	$smarty->assign('form', $form);
	$smarty->assign('cre_pack', $cre_pack);
	$smarty->assign('data', $data);
	$smarty->assign('paysystem', $paysystem);
	$smarty->assign('header', $lang['payment']);
	$smarty->display(TrimSlash($config['index_theme_path']).'/payment_page_2_cp_custom.tpl');
	exit;
}


function SaveAccountPage()
{
	global $lang, $config, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	if ($user[ AUTH_IS_APPLICANT ]) {
		header('Location: '.$config['site_root'].'/payment.php');
		exit;
	}
	
	// read request
	$paysys = isset($_POST['paysys']) ? $_POST['paysys'] : null;
	$amount = isset($_POST['account_to_add']) ? floatval($_POST['account_to_add']) : 0;
	
	// check amount
	if ($amount <= 0){
		UpdateAccountPage($lang['err']['update_account_err']);
		return;
	}
	
	// check payment system
	$rs = $dbconn->Execute('SELECT id, template_name FROM '.BILLING_PAYSYSTEMS_TABLE.' WHERE used = "1" AND template_name = ?', array($paysys));
	
	if (empty($rs->fields[0])){
		UpdateAccountPage($lang['err']['update_account_paysys_err']);
		return;
	}
	
	unset($rs);
	
	// only USD for now
	$amount_2 = 0;
	
	// only USD for now
	$currency = GetSiteSettings('site_unit_costunit');
	
	# THB as second currency
	# $currency = $cost_2 ? $settings['site_unit_costunit_2'] : $settings['site_unit_costunit'];
	
	// get fractional unit (NOT NEEDED)
	## $fractional_unit = $dbconn->getOne('SELECT fractional_unit FROM '.UNITS_TABLE.' WHERE abbr = ?', array($currency));
	
	// get product name
	$product_name = $lang['payment']['update_account_page']['to_paysystem'];
	
	// write billing request record
	$dbconn->Execute(
		'INSERT INTO '.BILLING_REQUESTS_TABLE.' SET
			id_user = ?, amount = ?, currency = ?, cost = ?, cost_2 = ?, id_group = ?, id_product = "0", date_send = NOW(),
			status = "send", paysystem = ?, recurring = "0", product_name = ?',
		array($id_user, $amount, $currency, $amount, $amount_2, PG_SINGLE_CREDIT_POINTS, $paysys, $product_name));
	
	$id_trunzaction = $dbconn->Insert_ID();
	
	// make payment
	include_once './include/systems/functions/'.$paysys.'.php';
	
	MakePayment($id_trunzaction, $id_user);
	
	return;
}

/*
	level upgrade with credit points
*/

function MakeInPayment($id_trunzaction, $id_user)
{
	global $dbconn, $user, $config;
	
	// check for valid transaction id
	$id_trunzaction = intval($id_trunzaction);
	
	if ($id_trunzaction <= 0) {
		return 0;
	}
	
	// data array to store approved data
	$data = array();
	
	$data['date'] = date('Y-m-d H:i:s');
	
	// get transaction record data
	$rs = $dbconn->Execute(
		'SELECT id, id_user, amount, currency, id_group, id_product, date_send, status, paysystem, product_name
		   FROM '.BILLING_REQUESTS_TABLE.'
		  WHERE id = ?',
		  array($id_trunzaction));
	
	$row = $rs->GetRowAssoc(false);
	
	// check for existing billing request record
	if (empty($row['id'])) {
		echo '<script>location.href="./account.php"</script>';
		exit;
	}
	
	// check for correct id_user
	if (empty($row['id_user']) || $row['id_user'] != $id_user) {
		echo '<script>location.href="./account.php"</script>';
		exit;
	}
	
	// check for correct status
	if ($row['status'] != 'send') {
		echo '<script>location.href="./account.php"</script>';
		exit;
	}
	
	// check for correct payment system
	if (empty($row['paysystem']) || $row['paysystem'] != 'user_account') {
		echo '<script>location.href="./account.php"</script>';
		exit;
	}
	
	$data['id_user']		= $row['id_user'];
	$data['amount']			= $row['amount'];
	$data['currency']		= $row['currency'];
	$data['id_group']		= $row['id_group'];
	$data['id_period']		= $row['id_product'];
	$data['paysystem']		= $row['paysystem'];
	$data['product_name']	= $row['product_name'];
	
	unset($rs, $row);
	
	// check for payment success
	$user_account = $dbconn->getOne('SELECT account_curr FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($data['id_user']));
	
	if ($user_account < $data['amount']) {
		// payment failed
		$dbconn->Execute('UPDATE '.BILLING_REQUESTS_TABLE.' SET status = "fail" WHERE id = ?', array($id_trunzaction));
		echo '<script>location.href="./account.php"</script>';
		exit;
	}
	
	// payment success
	$dbconn->Execute('UPDATE '.BILLING_REQUESTS_TABLE.' SET status = "approve" WHERE id = ?', array($id_trunzaction));
	
	// add billing entry
	$data['cost'] = $dbconn->GetOne('SELECT cost FROM '.GROUP_PERIOD_TABLE.' WHERE id = ?', array($data['id_period']));
	
	$dbconn->Execute(
		'INSERT INTO '.BILLING_ENTRY_TABLE.' SET
			id_user = ?, amount = ?, currency = ?, cost = ?, id_group = ?,
			id_product = ?, date_entry = ?, entry_type = ?, txn_type = "site_credits", product_name = ?',
		array($data['id_user'], $data['amount'], $data['currency'], $data['cost'], $data['id_group'],
			$data['id_period'], $data['date'], $data['paysystem'], $data['product_name']));
	
	// apply new membership level
	if ($data['id_period'] == MM_PLATINUM_GUY_PERIOD_ID || $data['id_period'] == MM_PLATINUM_LADY_PERIOD_ID)
	{
		// level update is delayed until admin approves the application
		$dbconn->execute('UPDATE '.USERS_TABLE.' SET mm_platinum_paid = NOW() WHERE id = ?', array($data['id_user']));
		
		if (PLATINUM_PAYMENT_TRIGGERS_PLATINUM_APPLIED) {
			$dbconn->execute('UPDATE '.USERS_TABLE.' SET mm_platinum_applied = NOW() WHERE id = ?', array($data['id_user']));
		}
		
		// UPDATE SOLVE360 CONTACT
		if (SOLVE360_CONNECTION) {
			require_once $config['site_path'].'/include/Solve360Service.php';
			$solve360Service = new Solve360Service(SOLVE360_USER, SOLVE360_TOKEN);
			
			$solve360 = array();
			require $config['site_path'].'/include/Solve360CustomFields.php';
			
			$contactData = array(
				$solve360['Platinum Paid'] => date('Y-m-d H:i:s'),
				// Add categories
				'categories' => array(
					'add' => array('category' => array(SOLVE360_TAG_PLATINUM_APPLIED))
				),
			);
			
			$rs = $dbconn->Execute('SELECT id_solve360, login FROM '.USERS_TABLE.' WHERE id = ?', array($data['id_user']));
			$id_solve360 = $rs->fields[0];
			$login = $rs->fields[1];
			$rs->Free();
			
			if (!empty($id_solve360)) {
				$contact = $solve360Service->editContact($id_solve360, $contactData);
				#var_dump($contact); exit;
				if (isset($contact->errors)) {
					$subject = 'Error while updating contact after platinum payment with credits';
					solve360_api_error($contact, $subject, $login);
				}
			}
			// maybe add contact if not found
		}
	}
	else
	{
		// normal membership upgrade or renewal
		$rv = AssignUserGroup($data['id_user'], $data['id_period']);
		if (! $rv) {
			echo '<script>location.href="./account.php"</script>';
			exit;
		}
	}
	
	// update user account
	$user_account = $user_account - $data['amount'];
	
	$dbconn->Execute(
		'UPDATE '.BILLING_USER_ACCOUNT_TABLE.' SET
				account_curr = "'.$user_account.'",
				date_refresh = NOW()
		  WHERE id_user = ?',
		  array($data['id_user']));
	
	if ($_SESSION['return_to_view']['return_url'] != '') {
		redirectToViewed();
		exit;
	}
	
	// redirect to appropriate page with success message trigger
	if ($data['id_period'] == MM_PLATINUM_GUY_PERIOD_ID || $data['id_period'] == MM_PLATINUM_LADY_PERIOD_ID) {
		header('Location: account.php?from=payment&id='.$id_trunzaction);
		exit;
	}
	
	header('Location: account.php?from=payment&id='.$id_trunzaction);
	exit;
}


function ServicePage()
{
	global $config, $smarty, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	if ($user[ AUTH_IS_APPLICANT ]) {
		header('Location: '.$config['site_root'].'/payment.php');
		exit;
	}
	
	$form = array();
	
	if (isset($_REQUEST['service']))
	{
		$form['cur'] = GetSiteSettings('site_unit_costunit');
		
		switch ($_REQUEST['service'])
		{
			case 'lift_up':
			
				$file_name = 'payment.php';
				
				Banners(GetRightModulePath(__FILE__));
				IndexHomePage();
				GetActiveUserInfo($user);
				
				$rs = $dbconn->Execute(
					'SELECT settings_value
					   FROM '.PAYMENT_SERVICES_SETTINGS_TABLE.'
					  WHERE service_name = "lift_up" AND settings_name = "price"');
				
				$form['price'] = floatval($rs->fields[0]);
				
				$rs = $dbconn->Execute('SELECT account_curr FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($id_user));
				
				$form['user_account'] = floatval($rs->fields[0]);
				
				if ($form['user_account'] >= $form['price']) {
					$form['service_available'] = 1;
				} else {
					$form['service_available'] = 0;
				}
				
				$form['page'] = 'list';
				$smarty->assign('form', $form);
				$smarty->display(TrimSlash($config['index_theme_path']).'/payment_service_lift_up_page.tpl');
				
			break;
				
			case 'lift_up_act':
			
				$rs = $dbconn->Execute(
					'SELECT settings_value
					   FROM '.PAYMENT_SERVICES_SETTINGS_TABLE.'
					  WHERE service_name = "lift_up" AND settings_name = "price"');
				
				$form['price'] = floatval($rs->fields[0]);
				
				$rs = $dbconn->Execute('SELECT account_curr FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($id_user));
				
				$form['user_account'] = floatval($rs->fields[0]);
				
				if ($form['user_account'] >= $form['price'])
				{
					$dbconn->Execute('UPDATE '.USERS_TABLE.' SET date_topsearched = NOW() WHERE id = ?', array($id_user));
					
					$new_curr = $form['user_account'] - $form['price'];
					
					$dbconn->Execute('UPDATE '.BILLING_USER_ACCOUNT_TABLE.' SET account_curr = ? WHERE id_user = ?', array($new_curr, $id_user));
					
					$strSQL =
						'INSERT INTO '.BILLING_ENTRY_TABLE.' SET
							id_user = ?, amount = ?, date_entry = NOW(), entry_type = "lift_up", currency = ?, id_group = ?';
					
					$dbconn->Execute($strSQL, array($id_user, - $form['price'], $form['cur'], PG_SINGLE_CREDIT_POINTS));
					
					header('location: '.$config['site_root'].'/payment.php?sel=service&service=lift_up_done');
					exit;
				}
				else
				{
					header('location: '.$config['site_root'].'/payment.php?sel=update_account');
					exit;
				}
				
			break;
				
			case 'lift_up_done':
				
				$file_name = 'payment.php';
				
				Banners(GetRightModulePath(__FILE__));
				IndexHomePage();
				GetActiveUserInfo($user);
				
				$form['page'] = 'done';
				
				$smarty->assign('form', $form);
				$smarty->display(TrimSlash($config['index_theme_path']).'/payment_service_lift_up_page.tpl');
				
			break;
				
			default:
				
				header('location: '.$config['site_root'].'/homepage.php');
				exit;
				
			break;
		}
	}
	else
	{
		header('location: '.$config['site_root'].'/homepage.php');
		exit;
	}
}

function OfflinePaymentSent_User_Message($paysystem, $currency, $payamount, $prod_id = NULL)
{
	global $config, $dbconn, $user;
	
	// content array
	$content				= array();
	
	$content['login']		= $user[ AUTH_LOGIN ];
	$content['fname']		= $user[ AUTH_FNAME ];
	$content['sname']		= $user[ AUTH_SNAME ];
	
	$content['urls']		= GetUserEmailLinks();
	
	$content['paysystem']	= $paysystem;
	
	// language
	$site_lang = !empty($user[ AUTH_SITE_LANGUAGE ]) ? $user[ AUTH_SITE_LANGUAGE ] : $config['default_lang'];
	
	// include language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	// gender suffix
	$suffix = ($user[ AUTH_GENDER ] == GENDER_MALE) ? '_e' : '_t';
	
	//Send payment record mail to lady if the payment is 9,997 /offline trasfers
	
	if (!empty($prod_id) && $prod_id == MM_PLATINUM_LADY_PERIOD_ID && $currency == 'THB') {
		if ($paysystem == 'atm_payment') {
			$subject = $lang_mail['cron_record_atm_pay_t']['subject'];
			$content['message'] = $lang_mail['cron_record_atm_pay_t']['message'];
		}
		if ($paysystem == 'wire_transfer'){
			$subject = $lang_mail['cron_record_wire_pay_t']['subject'];
			$content['message'] = $lang_mail['cron_record_wire_pay_t']['message'];
		}
	} else {
		// subject
		$subject = $lang_mail['offline_payment_sent'.$suffix]['subject'];
		
		// message
		$content['message'] = $lang_mail['offline_payment_sent'.$suffix]['message'][$paysystem];
		$content['message'] = str_replace('[userpayment]', $currency.'&nbsp;'.$payamount, $content['message']);
	}
	
	// recipient name
	$name_to = ($user[ AUTH_FNAME ].' '.$user[ AUTH_SNAME ]);
	
	// send external message
	SendMail($site_lang, $user[ AUTH_EMAIL ], $config['site_email'], $subject, $content,
		'mail_noti_simple_generic_user', null, $name_to, '', 'offline_payment_sent', $user[ AUTH_GENDER ]);
	
	// internal message
	$body = $lang_mail['generic'.$suffix]['hello'].' '.$user[ AUTH_FNAME ].',<br><br>';
	$body.= $content['message'].'<br><br>';
	$body.= $lang_mail['generic'.$suffix]['admin_regards'];
	
	$dbconn->Execute(
		'INSERT INTO '.MAILBOX_TABLE.' SET
			id_to = ?, id_from = ?, subject = ?, body = ?, was_read = "0", deleted_to = "0", deleted_from = "0", date_creation = NOW()',
		array($user[ AUTH_ID_USER ], ID_ADMIN, $subject, $body));
	
	return;
}

function OfflinePaymentSent_Admin_Message($paysystem, $currency, $payamount)
{
	global $config, $dbconn, $user;
	
	// content array
	$content				= array();
	
	$content['login']	 	= $user[ AUTH_LOGIN ];
	$content['fname']		= $user[ AUTH_FNAME ];
	$content['sname']		= $user[ AUTH_SNAME ];
	$content['email']		= $user[ AUTH_EMAIL ];
	
	$content['paysystem']	= $paysystem;
	
	// language
	$site_lang = $config['default_lang'];
	
	// include language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	// subject
	$subject = str_replace('[login]', $content['login'], $lang_mail['offline_payment_sent_admin']['subject']);
	
	// message
	$content['message'] = $lang_mail['offline_payment_sent_admin']['message'][$paysystem];
	$content['message'] = str_replace('[userpayment]', $currency.'&nbsp;'.$payamount, $content['message']);
	
	// recipient
	if (REDIRECT_ADMIN_EMAIL && !IS_LIVE_SERVER) {
		$email_to = REDIRECT_ADMIN_EMAIL_TO;
	} else {
		$email_to = $config['site_email'];
	}
	
	// send external message
	SendMail($site_lang, $email_to, $config['site_email'], $subject, $content,
		'mail_offline_payment_sent_admin', null, '', '', 'offline_payment_sent_admin');
	
	return;
}


?>