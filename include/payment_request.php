<?php

/**
* Requests data from payments systems
*
* @package DatingPro
* @subpackage Include files
*
* modified for comprehensive handling of recurring payments by ralf strehle (ralf.strehle@yahoo.de)
*
**/

include './config.php';
include '../common.php';
include './config_admin.php';
include './functions_auth.php';
include './functions_index.php';
include './functions_affiliate.php';
include './class.phpmailer.php';
include './functions_mail.php';
include './functions_mm.php';

$debug = true;

// dump request to file
if ($debug) {
	$mt = explode(' ', microtime());
	$fd = fopen(dirname(__FILE__).'/@profile-'.$mt[1].'-'.$mt[0].'.txt', 'wb');
} else {
	$fd = fopen(dirname(__FILE__).'/@profile.txt', 'wb');
}

fwrite($fd, "\$_REQUEST=\n");
fwrite($fd, print_r($_REQUEST, true));

// set $_GET['sel'] for barclays and allopass
if (isset($_REQUEST['oid']) && !isset($_GET['sel'])) {
	$_GET['sel'] = 'barclays';
}

if (isset($_REQUEST['DATAS']) && !isset($_GET['sel'])) {
	$_GET['sel'] = 'allopass';
}

// exit if no payment system detected
if (empty($_GET['sel'])) {
	fwrite($fd, "abort: parameter sel is empty\n");
	echo '<script>location.href="'.$config['site_root'].'/account.php"</script>';
	exit;
}

// call payment system specific function PaymentRequest to get response from specific payment gateway
$paysys = $_GET['sel'];

include_once './systems/functions/'.$paysys.'.php';

fwrite($fd, "/include/systems/functions/".$paysys.".php has been included\n");

$data = RequestPayment($fd);

// dump data to file
fwrite($fd, "\$data=\n");
fwrite($fd, print_r($data, true));

// update transaction record and perform post sales action
UpdateAccount($data['amount'], $data['currency'], $data['quantity'], $data['date'], $data['status'], $data['id_req'], $data['txn_type'], $paysys, $fd);

exit;


function UpdateAccount($amount, $currency, $quantity, $date, $status, $id_req, $txn_type, $paysys, $fd)
{
	// notes:
	// - amount is only compared with transaction record amount for non-ccbill payment systems
	// - quantity is not used at all
	// - status == 1 means payment success
	// - subscription transaction else than subsr_payment do not use payment_success and are handled in status == 0 branch
	// - this function is only called from payment_request.php and could be fully integrated
	//
	// ideas for improvement:
	// - compare quantity or do not pass as parameter
	
	global $dbconn, $config, $config_admin;
	
	fwrite($fd, "### function entry: UpdateAccount\n");
	
	// check for valid request id
	$id_req = intval($id_req);
	
	if ($id_req <= 0) {
		fwrite($fd, "abort: invalid request id\n");
		return 0;
	}
	
	fwrite($fd, "id_req=$id_req\n");
	
	// data array to store approved data
	$data = array();
	
	$data['date'] = $date;
	
	// get billing request record data
	$strSQL =
		'SELECT id, id_user, amount, currency, cost, cost_2, id_group, id_product, date_send, status,
				paysystem, recurring, product_name
		   FROM '.BILLING_REQUESTS_TABLE.'
		  WHERE id = ?';
	
	$rs = $dbconn->Execute($strSQL, array($id_req));
	$row = $rs->GetRowAssoc(false);
	
	fwrite($fd, BILLING_REQUESTS_TABLE." record=\n");
	fwrite($fd, print_r($row, true));
	
	// check for existing billing request record
	if (empty($row['id'])) {
		fwrite($fd, "abort: billing send request not found\n");
		return 0;
	}
	
	fwrite($fd, "billing send request found\n");
	
	// check for valid id_user
	// note: we do not compare with signed in id_user, because session could time out in between
	if (empty($row['id_user'])) {
		fwrite($fd, "abort: invalid userid in billing send request\n");
		return 0;
	}
	
	fwrite($fd, "user id found in billing send request: ".$row['id_user']."\n");
	
	// check for correct payment system
	if (empty($row['paysystem']) || $row['paysystem'] != $paysys) {
		fwrite($fd, "abort: invalid paysystem in billing send request or paysystem mismatch\n");
		return 0;
	}
	
	fwrite($fd, "matching payment system found in billing send request: ".$row['paysystem']."\n");
	
	// ralf: not sure what this is about. can the user change the currency within the payment gateway ???
	// replaced with currency match test
	# $data['currency'] = ($currency != '' ? $currency : $row['currency']);
	
	// check for correct currency
	if (empty($row['currency']) || $row['currency'] != $currency) {
		fwrite($fd, "abort: invalid currency in billing send request or currency mismatch\n");
		return 0;
	}
	
	fwrite($fd, "matching currency found in billing send request: ".$row['currency']."\n");
	
	$data['id_user'] = (int) $row['id_user'];
	$data['amount'] = $row['amount'];
	$data['currency'] = $row['currency'];
	$data['cost'] = $row['cost'];
	$data['cost_2'] = $row['cost_2'];
	$data['id_group'] = $row['id_group'];
	$data['id_product'] = $row['id_product'];
	$data['status'] = $row['status'];
	$data['paysystem'] = $row['paysystem'];
	$data['recurring'] = $row['recurring'];
	$data['product_name'] = $row['product_name'];
	
	unset($rs, $row);
	
	// check for payment success or subscription message
	// update billing_send_request
	// write billing_entry record on received payment
	if ($status == 1)
	{
		// payment success, also set when a recurring payment with subscr_payment is received
		// subscr_signup is not handled here, as no payment is received, see else branch of this if statement
		
		// check status of billing send request record
		if ($txn_type == 'subscr_payment')
		{
			// first payment usually arrives before signup success notification
			if ($data['status'] != 'subscr_signup' && $data['status'] != 'subscr_send') {
				fwrite($fd, "abort: recurring payment received, but invalid status \"".$data['status']."\" in billing send request record\n");
				return 0;
			}
		}
		elseif ($data['status'] != 'send')
		{
			fwrite($fd, "abort: non-recurring payment received, but invalid status in billing send request record\n");
			return 0;
		}
		
		// check for valid amount
		if ($paysys != 'ccbill') {
			if ($amount != $data['amount']) {
				fwrite($fd, $amount . " amount does not match " .$data['amount']. "\n" );	
				fwrite($fd, "abort: payment received, but amount does not match\n");
				return 0;
			}
		}
		
		// do not update the billing send request record for now when recurring payments are received
		// idea: count the number of payments and/or total amount received
		if ($txn_type != 'subscr_payment') {
			$dbconn->Execute('UPDATE '.BILLING_REQUESTS_TABLE.' SET status = "approve" WHERE id = ?', array($id_req));
			fwrite($fd, "status in billing send request record updated to 'approve'\n");
		}
		
		$strSQL =
			'INSERT INTO '.BILLING_ENTRY_TABLE.' SET
				id_user = ?, amount = ?, currency = ?, cost = ?, cost_2 = ?, id_group = ?, id_product = ?,
				date_entry = ?, entry_type = ?, txn_type = ?, product_name = ?';
		
		$param = array(
			'id_user'		=> $data['id_user'],
			'amount'		=> $data['amount'],
			'currency'		=> $data['currency'],
			'cost'			=> $data['cost'],
			'cost_2'		=> $data['cost_2'],
			'id_group'		=> $data['id_group'],
			'id_product'	=> $data['id_product'],
			'date'			=> $data['date'],
			'paysystem'		=> $data['paysystem'],
			'txn_type'		=> $txn_type,
			'product_name'	=> $data['product_name']
		);
		
		$dbconn->Execute($strSQL, $param);
		
		fwrite($fd, BILLING_ENTRY_TABLE." record inserted=\n");
		fwrite($fd, print_r($param, true));
	}
	else
	{
		// payment failure or subscription transaction else than subscr_payment
		
		switch ($txn_type)
		{
			case 'subscr_signup':
				
				fwrite($fd, "processing subscr_signup\n");
				
				if ($data['status'] != 'subscr_send') {
					fwrite($fd, "abort: status of transaction record is not subscr_send\n");
					return 0;
				}
				
				$dbconn->Execute('UPDATE '.BILLING_REQUESTS_TABLE.' SET status = "subscr_signup" WHERE id = ?', array($id_req));
				
				fwrite($fd, "status of transaction record was updated to subscr_signup\n");
				
			break;
			
			case 'subscr_cancel':
				
				fwrite($fd, "processing subscr_cancel\n");
				
				$dbconn->Execute('UPDATE '.BILLING_REQUESTS_TABLE.' SET status = "subscr_cancel" WHERE id = ?', array($id_req));
				
				fwrite($fd, "status of transaction record was updated to subscr_cancel\n");
				
			break;
			
			case 'subscr_modify':
				
				fwrite($fd, "processing subscr_modify\n");
				
				$dbconn->Execute('UPDATE '.BILLING_REQUESTS_TABLE.' SET status = "subscr_modify" WHERE id = ?', array($id_req));
				
				fwrite($fd, "status of transaction record was updated to subscr_modify\n");
				
			break;
			
			case 'subscr_eot':
				
				fwrite($fd, "processing subscr_eot\n");
				
				$dbconn->Execute('UPDATE '.BILLING_REQUESTS_TABLE.' SET status = "subscr_eot" WHERE id = ?', array($id_req));
				
				fwrite($fd, "status of transaction record was updated to subscr_eot\n");
				
			break;
			
			case 'subscr_failed':
				
				fwrite($fd, "processing subscr_failed\n");
				
				$dbconn->Execute('UPDATE '.BILLING_REQUESTS_TABLE.' SET status = "subscr_failed" WHERE id = ?', array($id_req));
				
				fwrite($fd, "status of transaction record was updated to subscr_failed\n");
				fwrite($fd, "abort: payment failure\n");
				return 0;
				
			break;
			
			default:
				
				fwrite($fd, "processing payment failure\n");
				
				$dbconn->Execute('UPDATE '.BILLING_REQUESTS_TABLE.' SET status = "fail" WHERE id = ?', array($id_req));
				
				fwrite($fd, "status of transaction record was updated to fail\n");
				fwrite($fd, "abort: payment failure\n");
				return 0;
				
			break;
		}
	}
	
	// perform action
	//
	switch ($data['id_group'])
	{
		case PG_SINGLE_CREDIT_POINTS:
			
			fwrite($fd, "action: money transfer to account\n");
			
			$account_entry = $dbconn->getOne('SELECT id FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($data['id_user']));
			
			if (!empty($account_entry))
			{
				$user_account = $dbconn->getOne('SELECT account_curr FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($data['id_user']));
				$user_account += $amount;
				$dbconn->Execute('UPDATE '.BILLING_USER_ACCOUNT_TABLE.' SET account_curr = ?, date_refresh = NOW() WHERE id_user = ?',
					array($user_account, $data['id_user']));
			}
			else
			{
				$dbconn->Execute('INSERT INTO '.BILLING_USER_ACCOUNT_TABLE.' SET id_user = ?, account_curr = ?, date_refresh = NOW()',
					array($data['id_user'], $data['amount']));
			}
			
			fwrite($fd, "account was updated\n");
			
			// GA_TRACKING
			ga_enqueue_event($data['id_user'], 'customcreditpurchase');
			
			// promote trial guy to regular
			if (is_trial($data['id_user'])) {
				$gender = $dbconn->getOne('SELECT gender FROM '.USERS_TABLE.' WHERE id = ?', array($data['id_user']));
				if ($gender == GENDER_MALE) {
					AssignUserGroup($data['id_user'], MM_REGULAR_GUY_PERIOD_ID, $fd);
				}
			}
			
		break;
		
		case PG_ECARD:
			
			fwrite($fd, "action: send ecard\n");
			
			SendEcard($data['id_product'], false);
			
			fwrite($fd, "ecard was sent\n");
			
		break;
		
		case PG_MY_STORE:
		
			fwrite($fd, "action: set paid_status of order to 1 and send emails to user and admin\n");
			
			$dbconn->Execute('UPDATE '.GIFTSHOP_ORDERS.' SET paid_status = "1" WHERE id = ?', array($data['id_product']));
			
			fwrite($fd, "paid_status was updated\n");
			
			MyStore_Online_Payment_User_Message($data['id_product'], $data['paysystem'], $data['currency'], $data['amount']);
			
			fwrite($fd, "email to user was sent\n");
			
			MyStore_Online_Payment_Admin_Message($data['id_product'], $data['paysystem'], $data['currency'], $data['amount']);
			
			fwrite($fd, "email to admin was sent\n");
			
		break;
		
		case PG_CREDIT_POINTS_PACK:
			
			fwrite($fd, "action: money transfer to account with credit points pack\n");
			
			$points = $dbconn->getOne('SELECT points FROM '.CREDIT_POINT_PACKS_TABLE.' WHERE id = ?', array($data['id_product']));
			
			fwrite($fd, "adding $points points\n");
			
			$account_entry = $dbconn->getOne('SELECT id FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($data['id_user']));
			
			if (!empty($account_entry))
			{
				$user_account = $dbconn->getOne('SELECT account_curr FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($data['id_user']));
				$user_account += $points;
				$dbconn->Execute('UPDATE '.BILLING_USER_ACCOUNT_TABLE.' SET account_curr = ?, date_refresh = NOW() WHERE id_user = ?', array($user_account, $data['id_user']));
			}
			else
			{
				$dbconn->Execute('INSERT INTO '.BILLING_USER_ACCOUNT_TABLE.' SET id_user = ?, account_curr = ?, date_refresh = NOW()',
					array($data['id_user'], $points));
			}
			
			fwrite($fd, "account was updated\n");
			
			// GA_TRACKING
			switch ($data['id_product']) {
				case 1: ga_enqueue_event($data['id_user'], 'bronzepackbuy'); break;
				case 2: ga_enqueue_event($data['id_user'], 'silverpackbuy'); break;
				case 3: ga_enqueue_event($data['id_user'], 'goldpackbuy'); break;
			}
			
			// promote trial guy to regular
			if (is_trial($data['id_user'])) {
				$gender = $dbconn->getOne('SELECT gender FROM '.USERS_TABLE.' WHERE id = ?', array($data['id_user']));
				if ($gender == GENDER_MALE) {
					AssignUserGroup($data['id_user'], MM_REGULAR_GUY_PERIOD_ID, $fd);
				}
			}
			
		break;
		
		default:
			
			// membership transaction
			//
			fwrite($fd, "action: membership upgrade or renewal\n");
			
			switch ($txn_type)
			{
				case 'subscr_failed':
					
					// we already catch this above
					
					fwrite($fd, "handle subscr_failed\n");
					fwrite($fd, "abort: payment failure\n");
					
					return 0;
					
				break;
				
				case 'subscr_payment':
					
					fwrite($fd, "handle subscr_payment\n");
					fwrite($fd, "do nothing, we already added a record to the billing_entry table\n");
					
				break;
				
				case 'subscr_modify':
					
					// we need to define what should happen here and create a user interface for subscription modifications
					// as an alternative, we can ask the user to first cancel an old subscription manually on the paypal site
					// and then to start a new subscription with the desired membership group
					
					fwrite($fd, "handle subscr_modify\n");
					fwrite($fd, "no handler defined, members are not supposed to modify their subscriptions\n");
					
				break;
				
				case 'subscr_cancel':
					
					// the user actively cancelled his membership
					// set level end date to last day of period
					// this is still simple, as we assume that the user is having only one record in BILLING_USER_PERIOD_TABLE at a time.
					// as soon as we allow stacked periods, we need to modify this.
					// we also could add more checks here, like testing if this is really a recurring payment, and that the
					// group is matching with the current group.
					
					fwrite($fd, "handle subscr_cancel\n");
					fwrite($fd, "calculating new date_end\n");
					
					// get date_begin as a timestamp. this is our first possible date_end
					$sql = 'SELECT UNIX_TIMESTAMP(date_begin) FROM '.BILLING_USER_PERIOD_TABLE.' WHERE id_user = ?';
					$date_end_timestamp = $dbconn->getOne($sql, array($data['id_user']));
					
					// check for valid record. if the user cancels while still in the signup sandbox, we won't have a
					// billing_user_period record, so we can skip this.
					//
					if (empty($date_end_timestamp))
					{
						fwrite($fd, "no billing_user_period record found for this user, must be a Signup user\n");
						fwrite($fd, "no need to adjust date_end\n");
					}
					else
					{
						// calculate the period_days
						$rs = $dbconn->Execute('SELECT amount, period FROM '.GROUP_PERIOD_TABLE.' WHERE id = ?', array($data['id_product']));
						$row = $rs->GetRowAssoc(false);
						
						$period_days = $row['amount'] * $config_admin['pay_period'][ $row['period'] ];
						
						fwrite($fd, "\$period_days=$period_days\n");
						
						unset($rs, $row);
						
						// date_begin is no longer updated when a user buys a period, so we need to get the date when he made the last payment
						$last_date_entry = $dbconn->GetOne(
							'SELECT date_entry
							   FROM '.BILLING_ENTRY_TABLE.'
							  WHERE id_user = ? AND txn_type = "subscr_payment"
						   ORDER BY date_entry DESC',
							  array($data['id_user']));
						
						fwrite($fd, "last recurring payment: $last_date_entry\n");
						
						$new_date_end_ts = strtotime($last_date_entry);
						
						// add period_days to new_date_end_ts, until we exceede the current time
						while ($new_date_end_ts < time()) {
							$new_date_end_ts += $period_days * 24*60*60;
						}
						
						$new_date_end = date('Y-m-d H:i:s', $new_date_end_ts);
						
						fwrite($fd, "calculated date_end is $new_date_end\n");
						
						$dbconn->Execute('UPDATE '.BILLING_USER_PERIOD_TABLE.' SET date_end = ? WHERE id_user = ?', array($new_date_end, $data['id_user']));
						
						fwrite($fd, "date_end in table ".BILLING_USER_PERIOD_TABLE." was updated\n");
						
						// UPDATE SOLVE360 CONTACT
						if (SOLVE360_CONNECTION) {
							require_once $config['site_path'].'/include/Solve360Service.php';
							$solve360Service = new Solve360Service(SOLVE360_USER, SOLVE360_TOKEN);
							
							$solve360 = array();
							require $config['site_path'].'/include/Solve360CustomFields.php';
							
							$contactData = array(
								$solve360['TLDF Membership Ends'] => $new_date_end,
							);
							
							$rs = $dbconn->Execute('SELECT id_solve360, login FROM '.USERS_TABLE.' WHERE id = ?', array($data['id_user']));
							$id_solve360 = $rs->fields[0];
							$login = $rs->fields[1];
							$rs->Free();
							
							if (!empty($id_solve360)) {
								$contact = $solve360Service->editContact($id_solve360, $contactData);
								#var_dump($contact); exit;
								if (isset($contact->errors)) {
									$subject = 'Error while updating expiration date after canceling paypal subscription';
									solve360_api_error($contact, $subject, $login);
								}
							}
							// maybe add contact if not found
						}
					}
					
				break;
				
				case 'subscr_eot':
					
					// payment gateway send end-of-term message
					// set level end to NOW() if current level end is in the future
					
					fwrite($fd, "handle subscr_eot\n");
					
					$dbconn->Execute('UPDATE '.BILLING_USER_PERIOD_TABLE.' SET date_end = NOW() WHERE id_user = ? AND date_end > NOW()', array($data['id_user']));
					
					fwrite($fd, "date_end in table billing_user_period was updated to NOW() if it was in the future\n");
					
					// UPDATE SOLVE360 CONTACT
					if (SOLVE360_CONNECTION) {
						require_once $config['site_path'].'/include/Solve360Service.php';
						$solve360Service = new Solve360Service(SOLVE360_USER, SOLVE360_TOKEN);
						
						$solve360 = array();
						require $config['site_path'].'/include/Solve360CustomFields.php';
						
						$contactData = array(
							$solve360['TLDF Membership Ends'] => date('Y-m-d H:i:s'),
						);
						
						$rs = $dbconn->Execute('SELECT id_solve360, login FROM '.USERS_TABLE.' WHERE id = ?', array($data['id_user']));
						$id_solve360 = $rs->fields[0];
						$login = $rs->fields[1];
						$rs->Free();
						
						if (!empty($id_solve360)) {
							$contact = $solve360Service->editContact($id_solve360, $contactData);
							#var_dump($contact); exit;
							if (isset($contact->errors)) {
								$subject = 'Error while updating expiration date after paypal subscription ends (EOT)';
								solve360_api_error($contact, $subject, $login);
							}
						}
						// maybe add contact if not found
					}
						
				break;
				
				case 'subscr_signup':
					
					// pass through to default handler
					
					fwrite($fd, "handle subscr_signup\n");
					
				default:
					
					fwrite($fd, "everything okay so far, we update the membership now\n");
					
					$platinum_trigger_arr = array(
						MM_PLATINUM_GUY_PERIOD_ID,
						MM_PLATINUM_LADY_PERIOD_ID,
						MM_PLATINUM_LADY_FIRST_INS_PERIOD_ID,
						MM_PLATINUM_LADY_SECOND_INS_PERIOD_ID,
						MM_PLATINUM_LADY_THIRD_INS_PERIOD_ID
					);
					
					if (in_array($data['id_product'], $platinum_trigger_arr)) {
						
						fwrite($fd, "platinum upgrade detected, checking if she has been verified platinum by admin\n");
						
						//All the cases will be handled by AssignUserGroup in functions_mm.php
						AssignUserGroup($data['id_user'], $data['id_product'], $fd);
						
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
									$subject = 'Error while updating contact after online platinum payment';
									solve360_api_error($contact, $subject, $login);
								}
							}
							// maybe add contact if not found
						}
						
					} else {
						
						fwrite($fd, "normal membership update or renewal\n");
						fwrite($fd, "calling AssignUserGroup(\$id_user=".$data['id_user'].", \$id_period=".$data['id_product'].")\n");
						
						$rv = AssignUserGroup($data['id_user'], $data['id_product'], $fd);
						
						if (!$rv){
							fwrite($fd, "abort: membership update/renewal failure\n");
							return 0;
						}
						
						fwrite($fd, "membership update/renewal was successful\n");
					}
				
				break;
			}
		
		break;
	}
	
	// affiliate check
	if (TypeAffiliatePayment() == '1') {
		fwrite($fd, "affiliat payment detected\n");
		AffiliatesPayment($status, $data['amount']);
		fwrite($fd, "affiliat payment was handled\n");
	}
	
	fwrite($fd, "### function exit: UpdateAccount with success\n");
	
	return 1;
}

?>