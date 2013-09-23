<?php

/**
* paypal payment system functions
*
* @package DatingPro
* @subpackage Payment systems files
* $adv_arr array for additional parameters
* $adv_arr["force_disable_recurring"] - force disabling recurring
**/

// enable offline testing with {site}/include/payment_request.php?offline=xxx with xxx being the transaction id
//
define('ALLOW_OFFLINE_TEST', false);

// send payment request to paypal
//
function MakePayment($id_req, $id_user_verify)
{
	global $dbconn, $config, $user;
	
	$error_redirect = '<script>location.href="./account.php"</script>';
	
	// check request id
	if (intval($id_req) <= 0) {
		echo $error_redirect;
		exit;
	}
	
	// get and check seller_id
	$rs = $dbconn->Execute('SELECT seller_id, sandbox, debug FROM '.BILLING_SYS_.'paypal');
	$row = $rs->GetRowAssoc(false);
	
	if (empty($row['seller_id'])) {
		echo $error_redirect;
		exit;
	}
	
	$seller_id = trim(stripslashes($row['seller_id']));
	$sandbox = ($row['sandbox'] == 1);
	$debug = ($row['debug'] == 1);
	
	unset($rs, $row);
	
	/**
	 *create paypal object
	 **/
	
	$payGear = new Payment_Engine(PAYMENT_ENGINE_SEND, $debug, false, $sandbox);
	$PaySystem = $payGear->factory('paypal');
	
	/**
	 * get and check billing request record data
	 **/
	
	$rs = $dbconn->Execute(
		'SELECT id, id_user, amount, currency, id_group, id_product, product_name, recurring
		   FROM '.BILLING_REQUESTS_TABLE.'
		  WHERE id = ?',
		  array($id_req));
	
	$row = $rs->GetRowAssoc(false);
	
	if (empty($row['id'])) {
		echo $error_redirect;
		exit;
	}
	
	if (empty($row['id_user']) || $row['id_user'] != $id_user_verify) {
		echo $error_redirect;
		exit;
	}
	
	$amount			= $row['amount'];
	$currency		= $row['currency'];
	$id_group		= $row['id_group'];
	$id_product		= $row['id_product'];
	$product_name	= $row['product_name'];
	$recurring		= $row['recurring'];
	
	unset($rs, $row);
	
	/**
	 * modify error redirection
	 **/
	
	if ($id_product == MM_PLATINUM_GUY_PERIOD_ID || $id_product == MM_PLATINUM_LADY_PERIOD_ID)
	{
		// platinum.php no longer in use
		#$error_redirect = '<script>location.href="./platinum.php?from=pay_platinum"</script>';
		$error_redirect = '<script>location.href="./account.php"</script>';
	}
	elseif ($id_group == PG_SINGLE_CREDIT_POINTS)
	{
		$error_redirect = '<script>location.href="./account.php"</script>';
	}
	elseif ($id_group == PG_ECARD)
	{
		$error_redirect = '<script>location.href="./account.php"</script>';
	}
	elseif ($id_group == PG_MY_STORE)
	{
		// MyStore: go to MyStore start page
		$error_redirect = '<script>location.href="./giftshop.php"</script>';
	}
	elseif ($id_group == PG_CREDIT_POINTS_PACK)
	{
		$error_redirect = '<script>location.href="./account.php"</script>';
	}
	else
	{
		// keep as is
	}
	
	/**
	 * set notify url
	 **/
	
	$notify_url = $config['server'].$config['site_root'].'/include/payment_request.php?sel=paypal';
	
	/**
	 * set return and cancel url depending on product
	 **/
	
	if ($id_group == PG_SINGLE_CREDIT_POINTS)
	{
		// buy credits: go to account page
		$return_url			= $config['server'].$config['site_root'].'/account.php?from=payment&id='.$id_req;
		$cancel_return_url	= $config['server'].$config['site_root'].'/account.php?from=payment&cancel=1';
	}
	elseif ($id_group == PG_ECARD)
	{
		// ecard: go to account page
		$return_url			= $config['server'].$config['site_root'].'/account.php?from=payment&id='.$id_req;
		$cancel_return_url	= $config['server'].$config['site_root'].'/account.php?from=payment&cancel=1';
	}
	elseif ($id_group == PG_MY_STORE)
	{
		// MyStore: go to order page
		$return_url			= $config['server'].$config['site_root'].'/giftshop.php?sel=view_order&order='.$id_product.'&from=payment';
		$cancel_return_url	= $config['server'].$config['site_root'].'/giftshop.php?sel=view_order&order='.$id_product.'&from=payment&cancel=1';
	}
	elseif ($id_group == PG_CREDIT_POINTS_PACK)
	{
		// buy credits: go to account page
		$return_url			= $config['server'].$config['site_root'].'/account.php?from=payment&id='.$id_req;
		$cancel_return_url	= $config['server'].$config['site_root'].'/account.php?from=payment&cancel=1';
	}
	elseif ($id_product == MM_PLATINUM_GUY_PERIOD_ID || $id_product == MM_PLATINUM_LADY_PERIOD_ID)
	{
		// go to account page, we check for platinum payment there
		$return_url			= $config['server'].$config['site_root'].'/account.php?from=payment&id='.$id_req;
		$cancel_return_url	= $config['server'].$config['site_root'].'/account.php?from=payment&cancel=1';
	}
	else
	{
		// membership payment: go to account page
		$return_url			= $config['server'].$config['site_root'].'/account.php?from=payment&id='.$id_req;
		$cancel_return_url	= $config['server'].$config['site_root'].'/account.php?from=payment&cancel=1';
	}
	
	/**
	 * assemble data array for Data Kipper
	 **/
	
	if ($recurring == 1)
	{
		/**
		 * recurring payment request
		 **/
		
		$rs = $dbconn->Execute(
			'SELECT id, amount, period, trial_amount, trial_period, trial_cost, trial_cost_2
			   FROM '.GROUP_PERIOD_TABLE.'
			  WHERE id = ?',
			  array($id_product));
		
		$row = $rs->GetRowAssoc(false);
		
		if (empty($row['id'])) {
			echo $error_redirect;
			exit;
		}
		
		// regular period
		$period_count_recurring = $row['amount'];
		
		switch ($row['period']) {
			case 'day':		$period_type_recurring = 'D'; break;
			case 'week':	$period_type_recurring = 'W'; break;
			case 'month':	$period_type_recurring = 'M'; break;
			case 'year':	$period_type_recurring = 'Y'; break;
			default:		$period_type_recurring = '';
		}
		
		// trial period
		if ($row['trial_amount'] && $row['trial_period'])
		{
			$trial_period_count_recurring = $row['trial_amount'];
			
			switch ($row['trial_period']) {
				case 'day':		$trial_period_type_recurring = 'D'; break;
				case 'week':	$trial_period_type_recurring = 'W'; break;
				case 'month':	$trial_period_type_recurring = 'M'; break;
				case 'year':	$trial_period_type_recurring = 'Y'; break;
				default:		$trial_period_type_recurring = '';
			}
		}
		else
		{
			$trial_period_count_recurring = 0;
			$trial_period_type_recurring = '';
		}
		
		$trial_amount_recurring = $row['trial_cost_2'] ? $row['trial_cost_2'] : $row['trial_cost'];
		
		unset($rs, $row);
		
		// construct parameter array for Data Kipper
		$data = array(
			'order_id'				=> $id_req,												// custom
			'seller_id'				=> $seller_id,											// business
			'test_mode'				=> '0',													// test_ipn
			'return_method'			=> '2',													// rm
			'notify_url'			=> $notify_url,											// notify_url
			'return_url'			=> $return_url,											// return
			'cancel_return_url'		=> $cancel_return_url,									// cancel_return
			'type'					=> '_xclick-subscriptions',								// cmd
			'product_name'			=> $product_name,										// item_name
			'currency'				=> $currency,											// currency_code
			'amount_recurring'		=> $amount,												// a3
			'period_count_recurring'=> $period_count_recurring,								// p3
			'period_type_recurring'	=> $period_type_recurring,								// t3
			'use_recurring'			=> '1',													// src
			'use_note'				=> '1'													// no_note
		);
		
		// special for trial period
		if ($trial_period_count_recurring && $trial_period_type_recurring) {
			$data['trial_amount_recurring']			= $trial_amount_recurring;				// a1
			$data['trial_period_count_recurring']	= $trial_period_count_recurring;		// p1
			$data['trial_period_type_recurring']	= $trial_period_type_recurring;			// t1
		}
	}
	else
	{
		/**
		 * block-of-time payment request
		 **/
		
		$data = array(
			'order_id'				=> $id_req,												// custom
			'seller_id'				=> $seller_id,											// business
			'test_mode'				=> '0',													// test_ipn
			'return_method'			=> '2',													// rm
			'notify_url'			=> $notify_url,											// notify_url
			'return_url'			=> $return_url,											// return
			'cancel_return_url'		=> $cancel_return_url,									// cancel_return
			'type'					=> '_xclick',											// cmd
			'product_name'			=> $product_name,										// item_name
			'currency'				=> $currency,											// currency
			'amount'				=> $amount,												// amount
		);
	}
	
	$PaySystem->setOptions($data);		// Payment_Data_Kipper: move $data to protected $_options array
	$PaySystem->doPayment();			// classes\paypal.php
}

// get paysystem request values. use in include/payment_request.php
// callback to process payment success or failure
// note: as we only use paypal for now, we just pass thru the txn_type. as soon as we need another
//       payment gateway, we need to map txn_type to something like subscr_type and define a
//       mapping for every payment gateway

function RequestPayment($fd)
{
	fwrite($fd, "### function entry: RequestPayment\n");
	
	$payGear = new Payment_Engine(PAYMENT_ENGINE_RECEIVE, false, false, false);
	
	fwrite($fd, "Payment_Engine instance created\n");
	
	$PaySystem = $payGear->factory('paypal');
	
	fwrite($fd, "PayPal instance created with factory method\n");
	
	$amount		= (isset($_REQUEST['mc_gross']) ? $_REQUEST['mc_gross'] : '');
	$currency	= (isset($_REQUEST['mc_currency']) ? $_REQUEST['mc_currency'] : '');
	$quantity	= (isset($_REQUEST['quantity']) ? $_REQUEST['quantity'] : '');
	$date		= (isset($_REQUEST['payment_date']) ? date('Y-m-d H:i:s', strtotime($_REQUEST['payment_date'])) : '');
	$id_req		= (isset($_REQUEST['custom']) ? $_REQUEST['custom'] : '');
	$txn_type	= (isset($_REQUEST['txn_type']) ? $_REQUEST['txn_type'] : '');
	
	$status		= ($PaySystem->checkPayment() ? 1 : 0);
	
	$data = array(
		'amount'		=> $amount,		// mc_gross
		'currency'		=> $currency,	// mc_currency
		'quantity'		=> $quantity,	// quantity
		'date'			=> $date,		// payment_date
		'status'		=> $status,		// payment_status Completed
		'id_req'		=> $id_req,		// custom
		'txn_type'		=> $txn_type,	// txt_type
	);
	
	fwrite($fd, "\$_REQUEST mapped to data array\n");
	
	// offline test
	if (ALLOW_OFFLINE_TEST && isset($_GET['offline'])) {
		/*
		$data = array(
			'amount'	=> '14.49',
			'currency'	=> 'USD',
			'quantity'	=> 1,
			'date'		=> date('Y-m-d H:i:s', time()),
			'status'	=> 1,
			'id_req'	=> intval($_GET['offline']),
			'txn_type'	=> ''
		);
		$data = array(
			'amount'	=> '14.49',
			'currency'	=> 'USD',
			'quantity'	=> 1,
			'date'		=> date('Y-m-d H:i:s', time()),
			'status'	=> 0,
			'id_req'	=> intval($_GET['offline']),
			'txn_type'	=> 'subscr_signup',
		);
		*/
	}
	
	fwrite($fd, "### function exit: RequestPayment\n");
	
	return $data;
}

//get paysystem settings. use in admin/admin_payment.php

function getBillingData($billing_system)
{
	global $dbconn, $smarty, $lang;
	
	$rs = $dbconn->Execute(
		'SELECT p.used, bs.seller_id, bs.recurring, bs.sandbox, bs.debug
		   FROM '.BILLING_PAYSYSTEMS_TABLE.' p, '.BILLING_SYS_.$billing_system.' bs 
		  WHERE p.template_name = ?',
		array($billing_system));
	
	$data['use'] = $rs->fields[0];
	$data['value'] = $rs->fields[1];
	$data['recurring'] = intval($rs->fields[2]);
	$data['sandbox'] = intval($rs->fields[3]);
	$data['debug'] = intval($rs->fields[4]);
	
	$smarty->assign('header', $lang['pays']);
	$smarty->assign('data', $data);
	
	$data['table_options'] = $smarty->fetch(SYSTEMS_DIR.'templates/'.$billing_system.'.tpl');
	
	return $data;
}

// set(change) paysystem settings. use in admin/admin_payment.php

function setBillingData($billing_system, $__POST)
{
	global $dbconn, $lang;
	
	$value = trim(strval($__POST['value']));
	
	$use = isset($__POST['use']) ? intval($__POST['use']) : 0;
	//RS: TLDF does not need the recurring flag, because there are recurring and non-recurring periods
	//    to avoid problems, we set recurring=1
	$recurring = isset($__POST['recurring']) ? intval($__POST['recurring']) : 1;
	$sandbox = isset($__POST['sandbox']) ? intval($__POST['sandbox']) : 0;
	$debug = isset($__POST['debug']) ? intval($__POST['debug']) : 0;
	
	$err = 0;
	
	if ($use && !$value) {
		$err = $lang['err']['invalid_fields'] . '<br>' . $lang['pays']['paypal_seller_id'];
	} else {
		$dbconn->Execute('UPDATE '.BILLING_PAYSYSTEMS_TABLE.' SET used = ? WHERE template_name = ?', array((string)$use, $billing_system));
		$dbconn->Execute('UPDATE '.BILLING_SYS_.$billing_system.' SET seller_id = ?, recurring = ?, sandbox = ?, debug = ?',
			array($value, (string)$recurring, (string)$sandbox, (string)$debug));
	}
	
	return $err;
}

?>