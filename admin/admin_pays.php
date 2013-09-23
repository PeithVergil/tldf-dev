<?php

/**
 * Users billing accounts and site payment options administration (users accounts, groups periods, payment systems, site currency management).
 *
 * @package DatingPro
 * @subpackage Admin Mode
 *
 * reorganized by ralf.strehle@yahoo.de
 **/

include_once '../include/config.php';
include_once '../common.php';
include_once '../include/config_admin.php';
include_once '../include/functions_auth.php';
include_once '../include/functions_admin.php';
include_once '../include/class.phpmailer.php';
include_once '../include/functions_mail.php';
include_once '../include/functions_mm.php';

$auth = auth_user();
login_check($auth);
IsFileAllowed($auth[ AUTH_ID_USER ], GetRightModulePath(__FILE__), 'pays');

$sel	= isset($_REQUEST['sel']) ? $_REQUEST['sel'] : '';
$id		= (isset($_REQUEST['id']) && intval($_REQUEST['id'])) ? intval($_REQUEST['id']) : 0;
$delid	= (isset($_REQUEST['delid']) && intval($_REQUEST['delid'])) ? intval($_REQUEST['delid']) : 0;

switch ($sel) {
	case 'add': AddBilling($id); break;
	case 'user': UserListBilling($id); break;
	case 'delete': DeleteBillingRecord($delid, $id); break;
	case 'groups': GroupListBilling(); break;
	case 'speed': GroupsChange(); break;
	case 'settings': SettingsBilling(); break;
	case 'saveset': SaveSettingsBilling(); break;
	case 'service': ServicesManage(); break;
	case 'billing_requests': BillingRequestsTable(); break;
	case 'approve_request': ApproveRequest($id); break;
	default: ListBilling();
}

exit;


function BillingRequestsTable($err = '')
{
	global $smarty, $dbconn, $config, $config_admin, $lang;
	
	// settings
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2'));
	
	$form['costunits'] = $settings['site_unit_costunit'];
	$form['costunits_2'] = $settings['site_unit_costunit_2'];
	
	// menu
	AdminMainMenu($lang['pays']);
	
	// get parameters
	$page		= (isset($_REQUEST['page']) && (int)$_REQUEST['page'] > 0) ? (int)$_REQUEST['page'] : 1;
	$letter		= (isset($_REQUEST['letter']) && (int)$_REQUEST['letter'] > 0) ? (int)$_REQUEST['letter'] : '*';
	$sorter		= (isset($_REQUEST['sorter']) && (int)$_REQUEST['sorter'] > 0) ? (int)$_REQUEST['sorter'] : 1;
	$order		= (isset($_REQUEST['order']) && (int)$_REQUEST['order'] > 0) ? (int)$_REQUEST['order'] : 1;
	$search		= isset($_REQUEST['search']) ? strip_tags(trim($_REQUEST['search'])) : '';
	$search_type= (isset($_REQUEST['search_type']) && (int)$_REQUEST['search_type'] > 0) ? (int)$_REQUEST['search_type'] : 1;
	$err		= isset($_REQUEST['err']) ? trim($_REQUEST['err']) : '';
	$msg		= isset($_REQUEST['msg']) ? trim($_REQUEST['msg']) : '';
	$p1			= isset($_REQUEST['p1']) ? trim($_REQUEST['p1']) : '';
	
	// pass parameters back to template
	$form['sorter']	= $sorter;
	$form['order']	= $order;
	$form['search']	= $search;
	$form['letter']	= $letter;
	
	// build custom search options and pass to template
	$types = array();
	
	for ($i = 1; $i <= 6; $i++) {
		$types[$i]['sel'] = ($search_type == $i) ? 1 : 0;
		$types[$i]['value'] = $lang['pays']['billing_requests']['search_type_'.$i];
	}
	
	$smarty->assign('types', $types);
	
	// custom search
	$search_str = '';
	$letter_str = '';
	
	if ($search) {	
		switch ($search_type) {
			case 1: $search_str = ' u.login LIKE "%'.$search.'%"'; break;
			case 2: $search_str = ' u.fname LIKE "%'.$search.'%"'; break;
			case 3: $search_str = ' u.sname LIKE "%'.$search.'%"'; break;
			case 4: $search_str = ' b.amount = "'.$search.'"'; break;
			case 5: $search_str = ' b.paysystem = "'.$search.'"'; break;
			case 6: $search_str = ' b.status = "'.$search.'"'; break;
		}
	}
	
	// letter search
	$letter_str = strval($letter) == '*' ? '' : ' LOWER(SUBSTRING(u.login, 1, 1)) = "'.strtolower(chr($letter)).'"';
	
	// build sql where
	$sql_where = '';
	
	if ($search_str) {
		$sql_where .=' AND '.$search_str;
	}
	
	if ($letter_str) {
		$sql_where .= ' AND '.$letter_str;
	}
	
	// count
	$num_records = $dbconn->getOne(
		'SELECT COUNT(b.id)
		   FROM '.BILLING_REQUESTS_TABLE.' b
	  LEFT JOIN '.USERS_TABLE.' u ON u.id = b.id_user
		  WHERE 1 '.$sql_where);
	
	// sql limit
	$lim_min = ($page - 1) * $config_admin['pays_numpage'];
	$lim_max = $config_admin['pays_numpage'];
	
	$sql_limit = ' LIMIT '.$lim_min.', '.$lim_max;
	
	// sql order by
	switch ($order) {
		case 2:
			$order_type = 'ASC';
			$form['new_order'] = 1;
		break;
		case 1:
		default:
			$order_type = 'DESC';
			$form['new_order'] = 2;
		break;
	}
	
	switch ($sorter) {
		case 1:
			$sql_order_by = ' ORDER BY b.id '.$order_type;
		break;
		case 2:
			$sql_order_by = ' ORDER BY u.sname '.$order_type.', u.fname '.$order_type;
		break;
		case 3:
			$sql_order_by = ' ORDER BY u.login '.$order_type;
		break;
		case 4:
			$sql_order_by = ' ORDER BY b.id_group '.$order_type;
		break;
		case 5:
			$sql_order_by = ' ORDER BY b.amount '.$order_type;
		break;
		case 6:
			$sql_order_by = ' ORDER BY b.date_send '.$order_type;
		break;
		case 7:
			$sql_order_by = ' ORDER BY b.status '.$order_type;
		break;
		case 8:
			$sql_order_by = ' ORDER BY paysystem '.$order_type;
		break;
		case 9:
			$sql_order_by = ' ORDER BY ug.id_group '.$order_type;
		break;
	}
	
	// build sql select and execute
	$strSQL = 
		'SELECT DISTINCT b.id, b.id_user, b.amount, b.currency, b.id_group, b.id_product,
				DATE_FORMAT(b.date_send, "'.$config['date_format'].' %H:%i") AS date_send,
				b.status, b.paysystem, b.product_name, b.info,
				u.login, u.fname, u.sname, u.status AS user_status,
				ug.id_group AS old_id_group,
				og.name AS old_group_name,
				g.name AS new_group_name,
				p.amount AS new_period_amount, p.period AS new_period
		   FROM '.BILLING_REQUESTS_TABLE.' b
	  LEFT JOIN '.USERS_TABLE.' u ON u.id = b.id_user
	  LEFT JOIN '.GROUPS_TABLE.' g ON g.id = b.id_group
	  LEFT JOIN '.GROUP_PERIOD_TABLE.' p ON p.id = b.id_product
	  LEFT JOIN '.USER_GROUP_TABLE.' ug ON ug.id_user = b.id_user
	  LEFT JOIN '.GROUPS_TABLE.' og ON og.id = ug.id_group
		  WHERE 1' . $sql_where . $sql_order_by . $sql_limit;
	
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$data = array();
	
	if (!$rs->EOF) {
		// back querystring
		$back = '?sel=billing_requests&page='.$page;
		if ($letter) $back .= '&letter='.$letter;
		if ($search) $back .= '&search='.$search.'&search_type='.$search_type;
		if ($sorter) $back .= '&sorter='.$sorter.'&order='.$order;
	
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			
			$data[$i]['number']			= ($page - 1) * $config_admin['pays_numpage'] + $i + 1;
			$data[$i]['id']				= $row['id'];
			$data[$i]['id_user']		= $row['id_user'];
			$data[$i]['user_fullname']	= $row['sname'].', '.$row['fname'];
			$data[$i]['login']			= $row['login'];
			$data[$i]['amount']			= sprintf('%01.2f', $row['amount']);
			$data[$i]['currency']		= $row['currency'];
			$data[$i]['id_group']		= $row['id_group'];
			
			if ($row['status'] == 'send' || $row['status'] == 'subscr_send') {
				$data[$i]['old_group_name'] = $row['old_group_name'];
				if (($row['old_id_group'] == MM_REGULAR_GUY_ID || $row['old_id_group'] == MM_REGULAR_LADY_ID) && $row['user_status'] == '0') {
					$data[$i]['old_group_name'] .= '<br>(pending)';
				}
			}
			
			if ($row['id_group'] > 0) {
				$data[$i]['new_group_name'] = $row['new_group_name'];
				$data[$i]['new_period'] = $row['new_period'] ? $row['new_period_amount'].' '.$lang['pays']['periods'][$row['new_period']] : '';
			} elseif ($row['id_group'] == PG_SINGLE_CREDIT_POINTS) {
				$data[$i]['new_group_name'] = 'Credits';
			} elseif ($row['id_group'] == PG_ECARD) {
				$data[$i]['new_group_name'] = 'Ecard';
			} elseif ($row['id_group'] == PG_MY_STORE) {
				$data[$i]['new_group_name'] = 'Store';
				$data[$i]['new_period'] = $row['id_product'];
			} elseif ($row['id_group'] == PG_CREDIT_POINTS_PACK) {
				$data[$i]['new_group_name'] = stripslashes($row['product_name']);
			}
			
			$data[$i]['date_send']	= $row['date_send'];
			$data[$i]['status']		= $row['status'];
			$data[$i]['paysystem']	= $row['paysystem'];
			$data[$i]['info']		= $row['info'];
			
			// links
#			$data[$i]['edit_link'] = 'admin_pays.php?sel=user&amp;id='.$row['id'].'&amp;back='.urlencode($back);
			$data[$i]['comunicate_href'] = './admin_comunicate.php?id='.$row['id_user'];
			$data[$i]['approve_href'] = 'admin_pays.php?sel=approve_request&amp;id='.$row['id'].'&amp;back='.urlencode($back);
			
			$rs->MoveNext();
			$i++;
		}
		
		$smarty->assign('data', $data);
		
		// sort links
		$sort_link = 'admin_pays.php?sel=billing_requests';
		if ($letter) $sort_link .= '&amp;letter='.$letter;
		if ($search) $sort_link .= '&amp;search='.$search.'&amp;search_type='.$search_type;
		
		$smarty->assign('sort_link', $sort_link);
		
		// page links
		$param_page = 'admin_pays.php?sel=billing_requests';
		if ($letter) $param_page .= '&amp;letter='.$letter;
		if ($search) $param_page .= '&amp;search='.$search.'&amp;search_type='.$search_type;
		if ($sorter) $param_page .= '&amp;sorter='.$sorter.'&amp;order='.$order.'&amp;';
		
		$smarty->assign('page_links', GetLinkStr($num_records, $page, $param_page, $config_admin['pays_numpage']));
	}
	
	// letter links
	$param_letter = 'admin_pays.php?sel=billing_requests';
	if ($search) $param_letter .= '&amp;search='.$search.'&amp;search_type='.$search_type;
	if ($sorter) $param_letter .= '&amp;sorter='.$sorter.'&amp;order='.$order;
	$param_letter .= '&amp;letter=';
	
	$smarty->assign('letter_links', LetersLink_eng($param_letter, $letter));
	
	// error
	if ($err) {
		$err = $lang['pays']['err'][$err];
		if ($p1) {
			$err = str_replace('#P1#', $p1, $err);
		}
		$form['err'] = $err;
	}
	
	// message
	if ($msg) {
		$msg = $lang['pays']['msg'][$msg];
		if ($p1) {
			$msg = str_replace('#P1#', $p1, $msg);
		}
		$form['msg'] = $msg;
	}
	
	// prepare and display template
	$smarty->assign('header', $lang['pays']);
	$smarty->assign('form', $form);
	
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_billing_requests_table.tpl');
	
	exit;
}


//SH function to delete billing record 
function DeleteBillingRecord($rec_id, $id_user)
{
	global $dbconn;
	
	$rec_id  = (int) $rec_id;
	$id_user = (int) $id_user;
	
	// requests
	## $page = (isset($_REQUEST['page']) && intval($_REQUEST['page']) > 0) ? intval($_REQUEST['page']) : 1;
	$back_link = isset($_REQUEST['back']) ? $_REQUEST['back'] : '';
	
	// validate billing id
	if (empty($rec_id)) {
		//return;
	}
	
	// number of records
	$amount = $dbconn->getOne('SELECT amount FROM '.BILLING_ENTRY_TABLE.' WHERE id = ?', array($rec_id));
	
	$dbconn->Execute('DELETE FROM '.BILLING_ENTRY_TABLE.' WHERE id = ?', array($rec_id));
	
	$strSQL = 'UPDATE '.BILLING_USER_ACCOUNT_TABLE.' SET account_curr = account_curr - '.$amount.' WHERE id_user = ?';
	$dbconn->Execute($strSQL, array($id_user));
	
	//VM writing delete credit record entry in log file 'log_deleted_credits'
	$Activity = 'BId: '.$rec_id.', Amt: '.$amount.', Uid: '.$id_user;
	LogUserActivity(LOG_FILE_DELETED_CREDITS, $Activity);
	
	$redirect_url = 'admin_pays.php?sel=user&id='.$id_user.'&back='.$back_link;
	echo '<script>location.href="'.$redirect_url.'";</script>';
}


function ApproveRequest($id)
{
	global $dbconn, $config;
	
	// validate billing request id
	if (empty($id)) {
		BillingRequestsTable();
		return;
	}
	
	// back link
	$back_link = isset($_GET['back']) ? $_GET['back'] : '';
	
	// data array to store approved data
	$data = array();
	
	// get send request record data
	$rs = $dbconn->Execute(
		'SELECT id, id_user, amount, currency, cost, cost_2, id_group, id_product, date_send, status, paysystem, recurring, product_name
		   FROM '.BILLING_REQUESTS_TABLE.'
		  WHERE id = ?',
		  array($id));
	
	$row = $rs->GetRowAssoc(false);
	
	if (empty($row['id'])) {
		header('location: admin_pays.php'.$back_link.'&err=request_not_found&p1='.$row['id']);
		exit;
	}
	
	if ($row['status'] != 'send') {
		header('location: admin_pays.php'.$back_link.'&err=request_status_not_send&p1='.$row['id']);
		exit;
	}
	
	$data['id_user']		= $row['id_user'];
	$data['amount']			= $row['amount'];
	$data['currency']		= $row['currency'];
	$data['cost']			= $row['cost'];
	$data['cost_2']			= $row['cost_2'];
	$data['id_group']		= $row['id_group'];
	$data['id_product']		= $row['id_product'];
	$data['status']			= $row['status'];
	$data['paysystem']		= $row['paysystem'];
	$data['recurring']		= $row['recurring'];
	$data['product_name']	= stripslashes($row['product_name']);
	
	unset($rs, $row);
	
	// payment success
	$dbconn->Execute('UPDATE '.BILLING_REQUESTS_TABLE.' SET status = "approve" WHERE id = ?', array($id));
	
	// create billing entry record
	$dbconn->Execute(
		'INSERT INTO '.BILLING_ENTRY_TABLE.' SET
			id_user = ?, amount = ?, currency = ?, cost = ?, cost_2 = ?, id_group = ?,
			id_product = ?, date_entry = NOW(), entry_type = ?, txn_type = "admin_approve", product_name = ?',
		array($data['id_user'], $data['amount'], $data['currency'], $data['cost'], $data['cost_2'], $data['id_group'],
			$data['id_product'], $data['paysystem'], $data['product_name']));
	
	// perform action
	// (idea: integrate payment_request with MakeInPayment and this function by designing
	//        central functions to a) update account, b) send ecard, c) membership upgrade/renewal
	switch ($data['id_group'])
	{
		case PG_SINGLE_CREDIT_POINTS:
			
			$account_entry = $dbconn->getOne('SELECT id FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($data['id_user']));
			
			if (!empty($account_entry)) {
				$user_account = $dbconn->getOne('SELECT account_curr FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($data['id_user']));
				$user_account += $data['amount'];
				$dbconn->Execute(
					'UPDATE '.BILLING_USER_ACCOUNT_TABLE.' SET account_curr = ?, date_refresh = NOW() WHERE id_user = ?',
					array($user_account, $data['id_user']));
			} else {
				$dbconn->Execute(
					'INSERT INTO '.BILLING_USER_ACCOUNT_TABLE.' SET id_user = ?, account_curr = ?, date_refresh = NOW()',
					array($data['id_user'], $data['amount']));
			}
			
			// GA_TRACKING
			$gender = $dbconn->getOne('SELECT gender FROM '.USERS_TABLE.' WHERE id = ?', array($data['id_user']));
			$_SESSION['ga_gender'] = ga_gender($gender);
			
			$id_group = $dbconn->getOne('SELECT id_group FROM '.USER_GROUP_TABLE.' WHERE id_user = ?', array($data['id_user']));
			$_SESSION['ga_member_status'] = ga_member_status($id_group);
			
			$_SESSION['ga_event_code'] = 'customcreditpurchase';
			// GA_TRACKING END
			
			$msg = 'approved_credits';
			
			SendPaymentApproval($data['id_user'], $data['paysystem'], $data['currency'], $data['amount']);
			
			// promote trial guy to regular after buying custom points
			if (is_trial($data['id_user'])) {
				$gender = $dbconn->getOne('SELECT gender FROM '.USERS_TABLE.' WHERE id = ?', array($data['id_user']));
				if ($gender == GENDER_MALE) {
					AssignUserGroup($data['id_user'], MM_REGULAR_GUY_PERIOD_ID);
					// create billing entry record
					$dbconn->Execute(
						'INSERT INTO '.BILLING_ENTRY_TABLE.' SET
							id_user = ?, amount = "0", currency = ?, cost = "0", cost_2 = "0", id_group = ?, id_product = ?,
							date_entry = NOW(), entry_type = ?, txn_type = "admin_approve", product_name = "Regular Guy Membership"',
						array($data['id_user'], $data['currency'], MM_REGULAR_GUY_ID, MM_REGULAR_GUY_PERIOD_ID, $data['paysystem']));
				}
			}
			
		break;
		
		case PG_ECARD:
			
			SendEcard($data['id_product'], true);
			
			$msg = 'approved_ecard';
			
			SendPaymentApproval($data['id_user'], $data['paysystem'], $data['currency'], $data['amount']);
			
		break;
		
		case PG_MY_STORE:
		
			$dbconn->Execute('UPDATE '.GIFTSHOP_ORDERS.' SET paid_status = "1" WHERE id = ?', array($data['id_product']));
			
			$msg = 'approved_store';
			
			MyStore_Offline_Payment_Approval_User_Message($data['id_product'], $data['paysystem'], $data['currency'], $data['amount']);
			
		break;
		
		case PG_CREDIT_POINTS_PACK:
		
			$points = $dbconn->getOne('SELECT points FROM '.CREDIT_POINT_PACKS_TABLE.' WHERE id = ?', array($data['id_product']));
			$account_entry = $dbconn->getOne('SELECT id FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($data['id_user']));
			
			if (!empty($account_entry)) {
				$user_account = $dbconn->getOne('SELECT account_curr FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($data['id_user']));
				$user_account += $points;
				$dbconn->Execute(
					'UPDATE '.BILLING_USER_ACCOUNT_TABLE.' SET account_curr = ?, date_refresh = NOW() WHERE id_user = ?',
					array($user_account, $data['id_user']));
			} else {
				$dbconn->Execute(
					'INSERT INTO '.BILLING_USER_ACCOUNT_TABLE.' SET id_user = ?, account_curr = ?, date_refresh = NOW()',
					array($data['id_user'], $points));
			}
			
			// GA_TRACKING
			$gender = $dbconn->getOne('SELECT gender FROM '.USERS_TABLE.' WHERE id = ?', array($data['id_user']));
			$_SESSION['ga_gender'] = ga_gender($gender);
			
			$id_group = $dbconn->getOne('SELECT id_group FROM '.USER_GROUP_TABLE.' WHERE id_user = ?', array($data['id_user']));
			$_SESSION['ga_member_status'] = ga_member_status($id_group);
			
			switch ($data['id_product']) {
				case 1: $_SESSION['ga_event_code'] = 'bronzepackbuy'; break;
				case 2: $_SESSION['ga_event_code'] = 'silverpackbuy'; break;
				case 3: $_SESSION['ga_event_code'] = 'goldpackbuy'; break;
			}
			// GA_TRACKING END
			
			$msg = 'approved_credits';
			
			SendPaymentApproval($data['id_user'], $data['paysystem'], $data['currency'], $data['amount']);
			
			// promote trial guy to regular after buying a credit points pack
			if (is_trial($data['id_user'])) {
				$gender = $dbconn->getOne('SELECT gender FROM '.USERS_TABLE.' WHERE id = ?', array($data['id_user']));
				if ($gender == GENDER_MALE) {
					AssignUserGroup($data['id_user'], MM_REGULAR_GUY_PERIOD_ID);
					// create billing entry record
					$dbconn->Execute(
						'INSERT INTO '.BILLING_ENTRY_TABLE.' SET
							id_user = ?, amount = "0", currency = ?, cost = "0", cost_2 = "0", id_group = ?, id_product = ?,
							date_entry = NOW(), entry_type = ?, txn_type = "admin_approve", product_name = "Regular Guy Membership"',
						array($data['id_user'], $data['currency'], MM_REGULAR_GUY_ID, MM_REGULAR_GUY_PERIOD_ID, $data['paysystem']));
				}
			}
			
		break;
		
		default:
		
			$fd = fopen(dirname(__FILE__).'/changing_group.txt', 'wb');
			
			// membership upgrade or renewal incl. platinum matching
			
			//Added By Narendra ...Compiled constants into array for check 
			$platinum_trigger_arr = array(
				MM_PLATINUM_GUY_PERIOD_ID,
				MM_PLATINUM_LADY_PERIOD_ID,
				MM_PLATINUM_LADY_FIRST_INS_PERIOD_ID,
				MM_PLATINUM_LADY_SECOND_INS_PERIOD_ID,
				MM_PLATINUM_LADY_THIRD_INS_PERIOD_ID
			);
			
			if (in_array($data['id_product'], $platinum_trigger_arr))
			{
				// process Platinum payment
				// logic has been moved to AssignUserGroup
				
				$rv = AssignUserGroup($data['id_user'], $data['id_product'], $fd);
				
				if (!$rv) {
					header('location: admin_pays.php'.$back_link.'&err=membership_assignment_error');
					exit;
				}
				
				$is_platinum_verified = $dbconn->GetOne('SELECT platinum_verified FROM '.USERS_TABLE.' WHERE id = ?', array($data['id_user']));
				
				if ($is_platinum_verified) {
					$msg = 'approved';
				} else {
					$msg = 'platinum_approved_only';
				}
				
				fclose($fd);
				
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
							$subject = 'Error while updating contact after admin approves offline platinum payment';
							solve360_api_error($contact, $subject, $login);
						}
					}
					// maybe add contact if not found
				}
			}
			else
			{
				// normal membership upgrade or renewal
				$rv = AssignUserGroup($data['id_user'], $data['id_product']);
				if (!$rv) {
					header('location: admin_pays.php'.$back_link.'&err=membership_assignment_error');
					exit;
				}
				
				// GA_TRACKING
				if (isset($_SESSION['ga_event_code'])) {
					$gender = $dbconn->getOne('SELECT gender FROM '.USERS_TABLE.' WHERE id = ?', array($data['id_user']));
					$_SESSION['ga_gender'] = ga_gender($gender);
					
					$id_group = $dbconn->getOne('SELECT id_group FROM '.USER_GROUP_TABLE.' WHERE id_user = ?', array($data['id_user']));
					$_SESSION['ga_member_status'] = ga_member_status($id_group);
				}
				// GA_TRACKING END
				
				$msg = 'approved';
			}
			
			SendPaymentApproval($data['id_user'], $data['paysystem'], $data['currency'], $data['amount'], $data['id_product']);
			
		break;
	}
	
	header('location: admin_pays.php'.$back_link.'&msg='.$msg.'&p1='.$id);
	exit;
}


function ListBilling($err = '')
{
	global $smarty, $dbconn, $config, $config_admin, $lang;
	
	// settings
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2'));
	
	$form['costunits'] = $settings['site_unit_costunit'];
	$form['costunits_2'] = $settings['site_unit_costunit_2'];
	
	// menu
	AdminMainMenu($lang['pays']);
	
	// get parameters
	$page = (isset($_REQUEST['page']) && intval($_REQUEST['page']) > 0) ? intval($_REQUEST['page']) : 1;
	$letter = (isset($_REQUEST['letter']) && intval($_REQUEST['letter']) > 0) ? intval($_REQUEST['letter']) : '*';
	$sorter = (isset($_REQUEST['sorter']) && intval($_REQUEST['sorter']) > 0) ? intval($_REQUEST['sorter']) : 1;
	$order = (isset($_REQUEST['order']) && intval($_REQUEST['order']) > 0) ? intval($_REQUEST['order']) : 1;
	$search = isset($_REQUEST['search']) ? strip_tags(trim($_REQUEST['search'])) : '';
	$search_type = (isset($_REQUEST['search_type']) && intval($_REQUEST['search_type']) > 0) ? intval($_REQUEST['search_type']) : 1;
	
	// pass parameters back to template
	$form['sorter'] = $sorter;
	$form['order'] = $order;
	$form['search'] = $search;
	$form['letter'] = $letter;
	
	// build custom search options and pass to template
	$types = array();
	
	for ($i = 1; $i <= 4; $i++) {
		$types[$i]['sel'] = ($search_type == $i) ? 1 : 0;
		$types[$i]['value'] = $lang['users']['type_'.$i];
	}
	
	$smarty->assign('types', $types);
	
	// custom search
	$search_str = '';
	$letter_str = '';
	
	if ($search) {
		switch ($search_type) {
			case 1: $search_str = ' login LIKE "%'.$search.'%"'; break;
			case 2: $search_str = ' fname LIKE "%'.$search.'%"'; break;
			case 3: $search_str = ' sname LIKE "%'.$search.'%"'; break;
			case 4: $search_str = ' email LIKE "%'.$search.'%"'; break;
		}
	}
	
	// letter search
	$letter_str = strval($letter) == '*' ? '' : ' LOWER(SUBSTRING(login, 1, 1)) = "'.strtolower(chr($letter)).'"';
	
	// build sql where
	$sql_where = '';
	
	if ($search_str) {
		$sql_where .=' AND '.$search_str;
	}
	
	if ($letter_str) {
		$sql_where .= ' AND '.$letter_str;
	}
	
	// count
	$num_records = $dbconn->getOne('SELECT COUNT(id) FROM '.USERS_TABLE.' WHERE 1 '.$sql_where);
	
	// sql limit
	$lim_min = ($page - 1) * $config_admin['pays_numpage'];
	$lim_max = $config_admin['pays_numpage'];
	
	$sql_limit = ' LIMIT '.$lim_min.', '.$lim_max;
	
	// sql order by
	switch ($order) {
		case 2:
			$order_type = 'ASC';
			$form['new_order'] = 1;
		break;
		
		case 1:
		default:
			$order_type = 'DESC';
			$form['new_order'] = 2;
		break;
	}
	
	switch ($sorter) {
		case 1:
			$sql_order_by = ' ORDER BY u.id '.$order_type;
			break;
		case 2:
			$sql_order_by = ' ORDER BY gp.id_group '.$order_type;
			break;
		case 3:
			$sql_order_by = ' ORDER BY u.status '.$order_type;
			break;
		case 4:
			$sql_order_by = ' ORDER BY u.sname '.$order_type.', u.fname '.$order_type;
			break;
		case 5:
			$sql_order_by = ' ORDER BY a.account_curr '.$order_type;
			break;
		case 6:
			$sql_order_by = ' ORDER BY u.login '.$order_type;
			break;
		case 7:
			$sql_order_by = ' ORDER BY membership_payments '.$order_type;
			break;
	}
	
	// build sql select and execute
	$strSQL = 
		'SELECT DISTINCT
				u.id, u.fname, u.sname, u.status, u.login, u.root_user, u.guest_user,
				a.account_curr,
				DATE_FORMAT(up.date_begin, "'.$config['date_format'].'") as date_begin,
				DATE_FORMAT(up.date_end, "'.$config['date_format'].'") as date_end,
				gp.amount, gp.period, gp.id_group, gp.recurring, gp.upgrade,
				g.name as groupname,
				(SELECT SUM(amount) FROM '.BILLING_ENTRY_TABLE.' be1 WHERE be1.id_user = u.id AND be1.id_group > 0 AND be1.currency = "'.$settings['site_unit_costunit'].'") AS membership_payments,
				(SELECT SUM(amount) FROM '.BILLING_ENTRY_TABLE.' be2 WHERE be2.id_user = u.id AND be2.id_group > 0 AND be2.currency = "'.$settings['site_unit_costunit_2'].'") AS membership_payments_2
		   FROM '.USERS_TABLE.' u
	  LEFT JOIN '.BILLING_USER_ACCOUNT_TABLE.' a ON a.id_user = u.id
	  LEFT JOIN '.BILLING_USER_PERIOD_TABLE.' up ON up.id_user = u.id
	  LEFT JOIN '.GROUP_PERIOD_TABLE.' gp ON gp.id = up.id_group_period
	  LEFT JOIN '.GROUPS_TABLE.' g ON g.id = gp.id_group
		  WHERE	1' . $sql_where . $sql_order_by . $sql_limit;
	
	// echo nl2br($strSQL);
	
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$user = array();
	
	if ($rs->RowCount() > 0)
	{
		// back querystring
		$back = '?page='.$page;
		if ($letter) $back .= '&letter='.$letter;
		if ($search) $back .= '&search='.$search.'&search_type='.$search_type;
		if ($sorter) $back .= '&sorter='.$sorter.'&order='.$order;
		
		while (!$rs->EOF)
		{
			$row = $rs->GetRowAssoc(false);
			
			$user[$i]['number'] = ($page - 1) * $config_admin['pays_numpage'] + $i + 1;
			$user[$i]['id'] = $row['id'];
			$user[$i]['login'] = $row['login'];
			$user[$i]['name'] = ($row['root_user'] || $row['guest_user']) ? $row['fname'] : $row['sname'].', '.$row['fname'];
			$user[$i]['account'] = sprintf('%01.2f', $row['account_curr']);
			if ($row['status']) {
				if (!$row['root_user'] && !$row['guest_user']) {
					if ($row['amount'] == 0) {
						$user[$i]['period'] = 'Unlimited ';
					} else {
						$user[$i]['period'] = $row['amount'].' ';
						if ($row['amount'] == 1) {
							$user[$i]['period'] .= strtolower($lang['pays']['periods_singular'][ $row['period'] ]);
						} else {
							$user[$i]['period'] .= strtolower($lang['pays']['periods_plural'][ $row['period'] ]);
						}
					}
					$user[$i]['period'] .= ' '.$row['groupname'];
					if ($row['recurring']) {
						$user[$i]['period'] .= '<br>(recurring)';
					}
					if ($row['upgrade']) {
						$user[$i]['period'] .= '<br>(upgrade)';
					}
					$user[$i]['dates'] = ($row['date_begin'] && $row['date_end']) ? $row['date_begin'].' - '.$row['date_end'] : 'error';
				}
			} else {
				// signup
				$user[$i]['period'] = '(Signup)';
				$user[$i]['dates'] = '(Signup)';
			}
			
			$user[$i]['status'] = $row['status'];
			$user[$i]['system_user'] = $row['root_user'] || $row['guest_user'] ? 1 : 0;
			$user[$i]['membership_payments'] = sprintf('%01.2f', $row['membership_payments']);
			$user[$i]['membership_payments_2'] = sprintf('%01.2f', $row['membership_payments_2']);
			
			// groups
			$rs2 = $dbconn->Execute(
				'SELECT a.name FROM '.USER_GROUP_TABLE.' b LEFT JOIN '.GROUPS_TABLE.' a ON a.id = b.id_group WHERE b.id_user = ?',
				array($row['id']));
			
			$groups_arr = array();
			
			while (!$rs2->EOF) {
				$groups_arr[] = $rs2->fields[0];
				$rs2->MoveNext();
			}
			
			unset($rs2);
			
			if (!empty($groups_arr)) {
				$user[$i]['groups'] = implode('<br>', $groups_arr);
			}
			
			// links
			$user[$i]['edit_link'] = 'admin_pays.php?sel=user&amp;id='.$row['id'].'&amp;back='.urlencode($back);
			$user[$i]['comunicate'] = 'admin_comunicate.php?id='.$row['id'];
			
			$rs->MoveNext();
			$i++;
		}
		
		$smarty->assign('user', $user);
		
		// sort links
		$sort_link = 'admin_pays.php?';
		if ($letter) $sort_link .= '&amp;letter='.$letter;
		if ($search) $sort_link .= '&amp;search='.$search.'&amp;search_type='.$search_type;
		
		$smarty->assign('sort_link', $sort_link);
		
		// page links
		$param_page = 'admin_pays.php?';
		if ($letter) $param_page .= '&amp;letter='.$letter;
		if ($search) $param_page .= '&amp;search='.$search.'&amp;search_type='.$search_type;
		if ($sorter) $param_page .= '&amp;sorter='.$sorter.'&amp;order='.$order.'&amp;';
		
		$smarty->assign('page_links', GetLinkStr($num_records, $page, $param_page, $config_admin['pays_numpage']));
	}
	
	// letter links
	$param_letter = 'admin_pays.php?';
	if ($search) $param_letter .= '&amp;search='.$search.'&amp;search_type='.$search_type;
	if ($sorter) $param_letter .= '&amp;sorter='.$sorter.'&amp;order='.$order;
	$param_letter .= '&amp;letter=';
	
	$smarty->assign('letter_links', LetersLink_eng($param_letter, $letter));
	
	// error
	$form['err'] = $err;
	
	// prepare and display template
	$smarty->assign('header', $lang['pays']);
	$smarty->assign('form', $form);
	
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_billing_table.tpl');
	
	exit;
}


function UserListBilling($id_user, $err='')
{
	global $smarty, $dbconn, $config, $config_admin, $lang;
	
	// validate id_user
	if (!$id_user) {
		ListBilling();
		return;
	}
	
	$id_user = (int) $id_user;
	$data['user_group'] = $dbconn->getOne('SELECT id_group FROM '.USER_GROUP_TABLE.' WHERE id_user = ?', array($id_user));
	
	// menu
	AdminMainMenu($lang['pays']);
	
	// settings
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2'));
	
	$form['costunits'] = $settings['site_unit_costunit'];
	$form['costunits_2'] = $settings['site_unit_costunit_2'];
	
	// requests
	$page = (isset($_REQUEST['page']) && intval($_REQUEST['page']) > 0) ? intval($_REQUEST['page']) : 1;
	$back_link = isset($_REQUEST['back']) ? $_REQUEST['back'] : '';
	
	// user info
	$rs = $dbconn->Execute('SELECT root_user, guest_user, login, fname, sname, email, gender FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
	$row = $rs->GetRowAssoc(false);
	
	$data['root'] = $row['root_user'] ? $row['root_user'] : $row['guest_user'];
	$data['name'] = $row['fname'].' '.$row['sname'].' ('.$row['login'].')';
	$data['email'] = $row['email'];
	$data['gender'] = $row['gender'];
	
	unset($rs, $row);
	
	//assigning account status/ credit poits to n/a for temporary purpose. Narendra.
	$platinum_array = array(
						MM_PLATINUM_LADY_FIRST_INS_ID,
						MM_PLATINUM_LADY_SECOND_INS_ID,
						MM_PLATINUM_LADY_ID);
	
	// current account
	if (in_array($data['user_group'], $platinum_array )) {
		$data['account'] = 'n/a';
		$form['costunits'] = '';
	} else {
		$data['account'] = $dbconn->getOne('SELECT account_curr FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($id_user));
		$data['account'] = empty($data['account']) ? 0 : $data['account'];
		$data['account'] = number_format($data['account'], 2);
	}
	
	// filter
	$filter = '';
	
	if (isset($_GET['filter']) && $_GET['filter'] == 'referred') {
		$filter = ' AND entry_type = "refer_friend"';
	}
	
	// number of records
	$num_records = $dbconn->getOne('SELECT COUNT(id) FROM '.BILLING_ENTRY_TABLE.' WHERE id_user = ? ' . $filter, array($id_user));
	
	// sql limit
	$lim_min = ($page - 1) * $config_admin['pays_user_numpage'];
	$lim_max = $config_admin['pays_user_numpage'];
	
	$sql_limit = ' LIMIT '.$lim_min.', '.$lim_max;
	
	// select list data
	$strSQL =
		'SELECT a.id, a.amount AS billing_amount, a.cost, a.currency, a.id_group, a.id_product, a.txn_type,
				DATE_FORMAT(a.date_entry, "'.$config['date_format'].' %H:%i") AS date_entry, a.product_name,
				a.entry_type, c.name AS groupname, b.amount AS period_amount, b.period, b.recurring, b.upgrade, b.id AS gp_period_id,
				u.login AS invited_login, u.id AS invited_id
		   FROM '.BILLING_ENTRY_TABLE.' a
	  LEFT JOIN '.GROUP_PERIOD_TABLE.' b ON a.id_product = b.id
	  LEFT JOIN '.GROUPS_TABLE.' c ON b.id_group = c.id
	  LEFT JOIN '.USER_REFER_TABLE.' urt ON a.id = urt.id_entry
	  LEFT JOIN '.USERS_TABLE.' u ON urt.id_user = u.id
		  WHERE a.id_user = ? '.$filter.'
	   ORDER BY a.id ' . $sql_limit;
	
	$rs = $dbconn->Execute($strSQL, array($id_user));
	
	$i = 0;
	$entry = array();
	
	$data['membership_payments'] = 0;
	$data['membership_payments_user'] = 0;
	$data['membership_payments_admin'] = 0;
	
	$data['membership_payments_2'] = 0;
	$data['membership_payments_2_user'] = 0;
	$data['membership_payments_2_admin'] = 0;
	
	$data['account_payments'] = 0;
	$data['account_payments_user'] = 0;
	$data['account_payments_admin'] = 0;
	
	$data['account_payments_2'] = 0;
	$data['account_payments_2_user'] = 0;
	$data['account_payments_2_admin'] = 0;
	
	$data['ecards_payments'] = 0;
	$data['ecards_payments_user'] = 0;
	$data['ecards_payments_admin'] = 0;
	
	$data['ecards_payments_2'] = 0;
	$data['ecards_payments_2_user'] = 0;
	$data['ecards_payments_2_admin'] = 0;
	
	$data['store_payments'] = 0;
	$data['store_payments_user'] = 0;
	$data['store_payments_admin'] = 0;
	
	$data['store_payments_2'] = 0;
	$data['store_payments_2_user'] = 0;
	$data['store_payments_2_admin'] = 0;
	
	while (!$rs->EOF)
	{
		$row = $rs->GetRowAssoc(false);
		
		$entry[$i]['id']			 = $row['id'];
		$entry[$i]['billing_amount'] = number_format($row['billing_amount'], 2);
		$entry[$i]['cost']			 = number_format($row['cost'], 2);
		$entry[$i]['currency']		 = $row['currency'];
		$entry[$i]['date_entry']	 = $row['date_entry'];
		$entry[$i]['txn_type']		 = $row['txn_type'];
		
		if ($row['id_group'] == PG_SINGLE_CREDIT_POINTS) {
			$entry[$i]['del_entry_link'] = 'admin_pays.php?sel=delete&amp;id='.$id_user.'&amp;delid='.$row['id'].'&amp;back='.$back_link;
		}
		
		if ($row['id_group'] > 0)
		{
			if ($row['entry_type'] == 'admin')
			{
				if ($entry[$i]['currency'] == $settings['site_unit_costunit']) {
					$data['membership_payments_admin'] += $row['billing_amount'];
				} else {
					$data['membership_payments_2_admin'] += $row['billing_amount'];
				}
			}
			else
			{
				if ($entry[$i]['currency'] == $settings['site_unit_costunit']) {
					$data['membership_payments_user'] += $row['billing_amount'];
				} else {
					$data['membership_payments_2_user'] += $row['billing_amount'];
				}
			}
			
			if ($row['gp_period_id'] != '')
			{
				if ($row['period_amount'] == 0) {
					$entry[$i]['pay_type'] = 'Unlimited ';
				} else {
					$entry[$i]['pay_type'] = $row['period_amount'].'&nbsp;';
					if ($row['period_amount'] == 1) {
						$entry[$i]['pay_type'] .= strtolower($lang['pays']['periods_singular'][ $row['period'] ]);
					} else {
						$entry[$i]['pay_type'] .= strtolower($lang['pays']['periods_plural'][ $row['period'] ]);
					}
				}
				if ($row['gp_period_id'] == MM_PLATINUM_LADY_THIRD_INS_PERIOD_ID) {
					$entry[$i]['pay_type'] .= '&nbsp;'. 'Platinum Life (3rd Installment)';
				} else {
					$entry[$i]['pay_type'] .= '&nbsp;'.$row['groupname'];
				}
				
				if ($row['recurring']) {
					$entry[$i]['pay_type'] .= ' (recurring)';
				}
				if ($row['upgrade']) {
					$entry[$i]['pay_type'] .= ' (upgrade)';
				}
			}
			else
			{
				$entry[$i]['pay_type'] = $lang['pays']['for_group_membership'];
			}
		}
		elseif ($row['id_group'] == PG_SINGLE_CREDIT_POINTS || $row['id_group'] == PG_CREDIT_POINTS_PACK)
		{
			if ($row['entry_type'] == 'admin')
			{
				if ($entry[$i]['currency'] == $settings['site_unit_costunit']) {
					$data['account_payments_admin'] += $row['billing_amount'];
				} else {
					$data['account_payments_2_admin'] += $row['billing_amount'];
				}
			}
			else
			{
				if ($entry[$i]['currency'] == $settings['site_unit_costunit']) {
					$data['account_payments_user'] += $row['billing_amount'];
				} else {
					$data['account_payments_2_user'] += $row['billing_amount'];
				}
			}
			
			if ($row['id_group'] == PG_SINGLE_CREDIT_POINTS) {
				$entry[$i]['pay_type'] = $lang['pays']['account_charging'];
			} else {
				$entry[$i]['pay_type'] = $row['product_name'];
			}
		}
		elseif ($row['id_group'] == PG_ECARD) // -2
		{
			if ($row['entry_type'] == 'admin')
			{
				if ($entry[$i]['currency'] == $settings['site_unit_costunit']) {
					$data['ecards_payments_admin'] += $row['billing_amount'];
				} else {
					$data['ecards_payments_2_admin'] += $row['billing_amount'];
				}
			}
			else
			{
				if ($entry[$i]['currency'] == $settings['site_unit_costunit']) {
					$data['ecards_payments_user'] += $row['billing_amount'];
				} else {
					$data['ecards_payments_2_user'] += $row['billing_amount'];
				}
			}
			
			$entry[$i]['pay_type'] = 'Ecards';
		}
		elseif ($row['id_group'] == PG_MY_STORE) // -3
		{
			if ($row['entry_type'] == 'admin')
			{
				if ($entry[$i]['currency'] == $settings['site_unit_costunit']) {
					$data['store_payments_admin'] += $row['billing_amount'];
				} else {
					$data['store_payments_2_admin'] += $row['billing_amount'];
				}
			}
			else
			{
				if ($entry[$i]['currency'] == $settings['site_unit_costunit']) {
					$data['store_payments_user'] += $row['billing_amount'];
				} else {
					$data['store_payments_2_user'] += $row['billing_amount'];
				}
			}
			
			$entry[$i]['pay_type'] = 'My Store';
		}
		elseif ($row['id_group'] == PG_CONNECTION_INVITE) // -4
		{
			if ($row['entry_type'] == 'admin')
			{
				if ($entry[$i]['currency'] == $settings['site_unit_costunit']) {
					$data['account_payments_admin'] += $row['billing_amount'];
				} else {
					$data['account_payments_2_admin'] += $row['billing_amount'];
				}
			}
			else
			{
				if ($entry[$i]['currency'] == $settings['site_unit_costunit']) {
					$data['account_payments_user'] += $row['billing_amount'];
				} else {
					$data['account_payments_2_user'] += $row['billing_amount'];
				}
			}
			
			$entry[$i]['pay_type'] = $lang['pays']['connection_invite'];
		}
		elseif ($row['id_group'] == PG_CONNECTION_ACCEPT) // -5
		{
			if ($row['entry_type'] == 'admin')
			{
				if ($entry[$i]['currency'] == $settings['site_unit_costunit']) {
					$data['account_payments_admin'] += $row['billing_amount'];
				} else {
					$data['account_payments_2_admin'] += $row['billing_amount'];
				}
			}
			else
			{
				if ($entry[$i]['currency'] == $settings['site_unit_costunit']) {
					$data['account_payments_user'] += $row['billing_amount'];
				} else {
					$data['account_payments_2_user'] += $row['billing_amount'];
				}
			}
			
			$entry[$i]['pay_type'] = $lang['pays']['connection_accept'];
		}
		elseif ($row['id_group'] == PG_INITIAL_CREDIT_POINT_BONUS) // -9
		{
			if ($row['entry_type'] == 'admin')
			{
				if ($entry[$i]['currency'] == $settings['site_unit_costunit']) {
					$data['account_payments_admin'] += $row['billing_amount'];
				} else {
					$data['account_payments_2_admin'] += $row['billing_amount'];
				}
			}
			else
			{
				if ($entry[$i]['currency'] == $settings['site_unit_costunit']) {
					$data['account_payments_user'] += $row['billing_amount'];
				} else {
					$data['account_payments_2_user'] += $row['billing_amount'];
				}
			}
			
			$entry[$i]['pay_type'] = $lang['pays']['initial_bonus'];
		}
		
		$entry[$i]['type'] = $row['entry_type'];
		
		if ($entry[$i]['type'] == 'refer_friend') {
			$entry[$i]['invited_login'] = $row['invited_login'];
			$entry[$i]['invited_id'] = $row['invited_id'];
			$entry[$i]['invited_profile_link'] = $config['server'].$config['site_root'].'/admin/admin_users.php?sel=edit&amp;id='.$entry[$i]['invited_id'];
		}
		
		$entry[$i]['del_entry_link'] = $config['server'].$config['site_root'].'/admin/admin_pays.php?sel=delete&amp;delid='.$entry[$i]['id'].'&amp;id='.$id_user;
		
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('entry', $entry);
	
	// page links
	$param = 'admin_pays.php?sel=user&amp;id='.$id_user.'&amp;back='.$back_link.'&amp;';
	$smarty->assign('links', GetLinkStr($num_records, $page, $param, $config_admin['pays_user_numpage']));
	
	// totals
	$data['total_membership_payments'] = $data['membership_payments_user'] + $data['membership_payments_admin'];
	$data['total_membership_payments_2'] = $data['membership_payments_2_user'] + $data['membership_payments_2_admin'];
	
	$data['total_account_payments'] = $data['account_payments_user'] + $data['account_payments_admin'];
	$data['total_account_payments_2'] = $data['account_payments_2_user'] + $data['account_payments_2_admin'];
	
	$data['total_ecards_payments'] = $data['ecards_payments_user'] + $data['ecards_payments_admin'];
	$data['total_ecards_payments_2'] = $data['ecards_payments_2_user'] + $data['ecards_payments_2_admin'];
	
	$data['total_store_payments'] = $data['store_payments_user'] + $data['store_payments_admin'];
	$data['total_store_payments_2'] = $data['store_payments_2_user'] + $data['store_payments_2_admin'];
	
	$data['total_payments_user'] = $data['membership_payments_user'] + $data['account_payments_user'] + $data['ecards_payments_user'] + $data['store_payments_user'];
	$data['total_payments_2_user'] = $data['membership_payments_2_user'] + $data['account_payments_2_user'] + $data['ecards_payments_2_user'] + $data['store_payments_2_user'];
	
	$data['total_payments_admin'] = $data['membership_payments_admin'] + $data['account_payments_admin'] + $data['ecards_payments_admin'] + $data['store_payments_admin'];
	$data['total_payments_2_admin'] = $data['membership_payments_2_admin'] + $data['account_payments_2_admin'] + $data['ecards_payments_2_admin'] + $data['store_payments_2_admin'];
	
	$data['total_payments'] = $data['total_payments_user'] + $data['total_payments_admin'];
	$data['total_payments_2'] = $data['total_payments_2_user'] + $data['total_payments_2_admin'];
	
	// formatting totals
	$data['membership_payments_user'] = number_format($data['membership_payments_user'], 2);
	$data['membership_payments_2_user'] = number_format($data['membership_payments_2_user'], 2);
	$data['membership_payments_admin'] = number_format($data['membership_payments_admin'], 2);
	$data['membership_payments_2_admin'] = number_format($data['membership_payments_2_admin'], 2);
	
	$data['account_payments_user'] = number_format($data['account_payments_user'], 2);
	$data['account_payments_2_user'] = number_format($data['account_payments_2_user'], 2);
	$data['account_payments_admin'] = number_format($data['account_payments_admin'], 2);
	$data['account_payments_2_admin'] = number_format($data['account_payments_2_admin'], 2);
	
	$data['ecards_payments_user'] = number_format($data['ecards_payments_user'], 2);
	$data['ecards_payments_2_user'] = number_format($data['ecards_payments_2_user'], 2);
	$data['ecards_payments_admin'] = number_format($data['ecards_payments_admin'], 2);
	$data['ecards_payments_2_admin'] = number_format($data['ecards_payments_2_admin'], 2);
	
	$data['store_payments_user'] = number_format($data['store_payments_user'], 2);
	$data['store_payments_2_user'] = number_format($data['store_payments_2_user'], 2);
	$data['store_payments_admin'] = number_format($data['store_payments_admin'], 2);
	$data['store_payments_2_admin'] = number_format($data['store_payments_2_admin'], 2);
	
	$data['total_membership_payments'] = number_format($data['total_membership_payments'], 2);
	$data['total_membership_payments_2'] = number_format($data['total_membership_payments_2'], 2);
	$data['total_account_payments'] = number_format($data['total_account_payments'], 2);
	$data['total_account_payments_2'] = number_format($data['total_account_payments_2'], 2);
	$data['total_ecards_payments'] = number_format($data['total_ecards_payments'], 2);
	$data['total_ecards_payments_2'] = number_format($data['total_ecards_payments_2'], 2);
	$data['total_store_payments'] = number_format($data['total_store_payments'], 2);
	$data['total_store_payments_2'] = number_format($data['total_store_payments_2'], 2);
	
	$data['total_payments_user'] = number_format($data['total_payments_user'], 2);
	$data['total_payments_2_user'] = number_format($data['total_payments_2_user'], 2);
	$data['total_payments_admin'] = number_format($data['total_payments_admin'], 2);
	$data['total_payments_2_admin'] = number_format($data['total_payments_2_admin'], 2);
	
	$data['total_payments'] = number_format($data['total_payments'], 2);
	$data['total_payments_2'] = number_format($data['total_payments_2'], 2);
	
	// hidden form fields
	$form['hiddens'] = '<input type="hidden" name="sel" value="add">';
	$form['hiddens'].= '<input type="hidden" name="id" value="'.$id_user.'">';
	$form['hiddens'].= '<input type="hidden" name="page" value="'.$page.'">';
	$form['hiddens'].= '<input type="hidden" name="back" value="'.htmlentities($back_link).'">';
	
	// back link
	$form['back_link'] = htmlentities($back_link);
	
	// available groups
	$_str = $config['use_gender_membership'] ? ' AND b.gender = "'.$data['gender'].'"' : '';
	
	$strSQL =
		'SELECT a.id, a.cost, a.period, a.amount, a.recurring, a.upgrade, b.name
		   FROM '.GROUP_PERIOD_TABLE.' a
	  LEFT JOIN '.GROUPS_TABLE.' b ON a.id_group = b.id
		  WHERE a.status = "1" AND b.is_gender_group = "'.$config['use_gender_membership'].'" '.$_str.'
	   ORDER BY b.sort DESC, a.period, a.amount';
	
	$rs = $dbconn->Execute($strSQL);
	
	if (!$rs->EOF) {
		$i = 0;
		$periods = array();
		
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$periods[$i]['id'] = $row['id'];
			if ($row['amount'] == 0) {
				$periods[$i]['name'] = 'Unlimited';
			} else {
				$periods[$i]['name'] = $row['amount'].' ';
				if ($row['amount'] == 1) {
					$periods[$i]['name'] .= strtolower($lang['pays']['periods_singular'][ $row['period'] ]);
				} else {
					$periods[$i]['name'] .= strtolower($lang['pays']['periods_plural'][ $row['period'] ]);
				}
			}
			if($row['id'] == MM_PLATINUM_LADY_THIRD_INS_PERIOD_ID){
				$periods[$i]['name'] .= ' Platinum Life (3rd Installment)';
			}else{
				$periods[$i]['name'] .= ' '.$row['name'];
			}
			
			if ($row['recurring']) {
				$periods[$i]['name'] .= ' (recurring)';
			}
			if ($row['upgrade']) {
				$periods[$i]['name'] .= ' (upgrade)';
			}
			$rs->MoveNext();
			$i++;
		}
		
		$smarty->assign('periods', $periods);
	}
	
	//GETTING DAYS REMAINING FOR ADMIN PAGE
	$strSQL = 
		'SELECT DATEDIFF(date_end, NOW()) AS datediff, date_end, id_group  
		   FROM '.BILLING_USER_PERIOD_TABLE.' AS bupt
	 INNER JOIN '.USER_GROUP_TABLE.' AS ugt ON bupt.id_user = ugt.id_user
		  WHERE bupt.id_user = ?';
	$rs = $dbconn->Execute($strSQL, array($id_user));
	$i = 0;
	$row = $rs->GetRowAssoc(false);
	
	$days_remain = $row['datediff'];
	$user_group = $row['id_group'];
	$end_date = $row['date_end'];
	
	if ($end_date == UNLIMITED_DATE_END || $end_date == '') {
		$days_remain = 'n/a';
	}
	
	$smarty->assign('days_remain', $days_remain);
	//----
	
	$smarty->assign('data', $data);
	
	$form['err'] = $err;
	
	$smarty->assign('err', $lang['err']);
	$smarty->assign('form', $form);
	$smarty->assign('header', $lang['pays']);
	$smarty->assign('button', $lang['button']);
	
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_billing_user_table.tpl');
	
	exit;
}


function AddBilling($id_user)
{
	global $dbconn, $config, $lang;
	
	if (empty($id_user)) {
		ListBilling();
		return;
	}
	
	$id_user = (int) $id_user;
	
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2'));
	
	$add_type = (isset($_REQUEST['add_type']) && $_REQUEST['add_type'] == 'membership') ? 'membership' : $_REQUEST['add_type'];
	
	switch ($add_type)
	{
		case 'membership':
			
			$id_period = isset($_POST['period']) ? (int) $_POST['period'] : 0;
			
			if (!$id_period) {
				UserListBilling($id_user);
				return;
			}
			
			// get period data
			$strSQL =
				'SELECT a.id, a.id_group, a.amount, a.period, a.cost, a.cost_2, b.name AS group_name 
				   FROM '.GROUP_PERIOD_TABLE.' a
			  LEFT JOIN '.GROUPS_TABLE.' b ON a.id_group = b.id 
				  WHERE a.id = ?';
			
			$rs = $dbconn->Execute($strSQL, array($id_period));
			$row = $rs->GetRowAssoc(false);
			
			$id_group	= $row['id_group'];
			$cost		= $row['cost'];
			$cost_2		= $row['cost_2'];
			
			if ($cost_2 > 0) {
				$pay_amount = $cost_2;
				$pay_currency = $settings['site_unit_costunit_2'];
			} else {
				$pay_amount = $cost;
				$pay_currency = $settings['site_unit_costunit'];
			}
			
			$discount = 0;
			if (isset($_POST['discount']) && $_POST['discount'] != '') {
				$discount = $_POST['discount'];
				if (is_numeric($discount) && $discount <= $pay_amount) {
					$pay_amount -= $discount;
				}
			}
			
			$product_name = $row['amount'].' '.$lang['pays']['periods'][$row['period']].' '.$row['group_name'];
			
			unset($rs, $row);
			
			AssignUserGroup($id_user, $id_period);
			
			$dbconn->Execute(
				'INSERT INTO '.BILLING_ENTRY_TABLE.'
					SET id_user = ?, amount = ?, currency = ?, cost = ?, cost_2 = ?, id_group = ?,
						id_product = ?, date_entry = NOW(), entry_type = "admin", product_name = ?, discount = ?',
				array($id_user, $pay_amount, $pay_currency, $cost, $cost_2, $id_group, $id_period, $product_name, $discount));
			
		break;
		
		case 'move_user':
			
			$id_period = isset($_POST['period']) ? (int) $_POST['period'] : 0;
			
			AssignUserGroup($id_user, $id_period, null, true);
			
		break;
		
		case 'add_days':
			
			$days = isset($_REQUEST['days_to_add']) ? (int) $_REQUEST['days_to_add'] : 0;
			
			if ($days == 0) {
				UserListBilling($id_user, $lang['err']['add_days_zero_err']);
				return;
			}
			if ($days < -365) {
				UserListBilling($id_user, $lang['err']['add_days_too_small_err']);
				return;
			}
			if ($days > 365) {
				UserListBilling($id_user, $lang['err']['add_days_too_big_err']);
				return;
			}
			
			$date_end = $dbconn->GetOne('SELECT date_end FROM '.BILLING_USER_PERIOD_TABLE.' WHERE id_user = ?', array($id_user));
			
			if (substr($date_end, 0, 10) == substr(UNLIMITED_DATE_END, 0, 10)) {
				UserListBilling($id_user, $lang['err']['add_days_unlimited_err']);
				return;
			}
			
			$dbconn->Execute('UPDATE '.BILLING_USER_PERIOD_TABLE.' SET date_end = DATE_ADD(date_end, INTERVAL '.$days.' DAY) WHERE id_user = ?', array($id_user));
			
			$date_end = $dbconn->GetOne('SELECT date_end FROM '.BILLING_USER_PERIOD_TABLE.' WHERE id_user = ?', array($id_user));
			
			if (SOLVE360_CONNECTION) {
				require_once $config['site_path'].'/include/Solve360Service.php';
				$solve360Service = new Solve360Service(SOLVE360_USER, SOLVE360_TOKEN);
				
				$solve360 = array();
				require $config['site_path'].'/include/Solve360CustomFields.php';
				
				$contactData = array(
					$solve360['TLDF Membership Ends'] => $date_end,
				);
				
				$rs = $dbconn->Execute('SELECT id_solve360, login FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
				$id_solve360 = $rs->fields[0];
				$login = $rs->fields[1];
				$rs->Free();
				
				if (!empty($id_solve360)) {
					$contact = $solve360Service->editContact($id_solve360, $contactData);
					#var_dump($contact); exit;
					if (isset($contact->errors)) {
						$subject = 'Error while adding membership days in TLDF admin';
						solve360_api_error($contact, $subject, $login);
					}
				}
				// maybe add contact when not found
			}
			
			$msg = $lang['err']['add_days_success_msg'];
			$msg = str_replace('#DAYS#', $days, $msg);
			$msg = str_replace('#DATE_END#', $date_end, $msg);
			
			UserListBilling($id_user, $msg);
			return;
			
		break;
		
		case 'account':
		case 'del_credit':
			
			if ($add_type == 'del_credit')
			{
				$amount = isset($_REQUEST['account_to_delete']) ? floatval($_REQUEST['account_to_delete']) : 0;
				
				if ($amount <= 0) {
					UserListBilling($id_user, $lang['err']['delete_account_err']);
					return;
				}
				
				$amount = (int)('-'.$amount);
			}
			else
			{
				$amount = isset($_REQUEST['account_to_add']) ? floatval($_REQUEST['account_to_add']) : 0;
				
				if ($amount <= 0) {
					UserListBilling($id_user, $lang['err']['update_account_err']);
					return;
				}
			}
			
			$currency = GetSiteSettings('site_unit_costunit');
			
			// add billing entry record
			$dbconn->Execute(
				'INSERT INTO '.BILLING_ENTRY_TABLE.' SET
					id_user = ?, amount = ?, currency = ?, cost = ?, cost_2 = "0", id_group = ?, id_product = "0",
					date_entry = NOW(), entry_type = "admin", product_name = "credits"',
				array($id_user, $amount, $currency, $amount, PG_SINGLE_CREDIT_POINTS));
			
			// update user account
			$user_account = $dbconn->getOne('SELECT account_curr FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($id_user));
			
			if (empty($user_account)) {
				$user_account = 0;
			}
			
			$user_account += $amount;
			
			$exist = $dbconn->getOne('SELECT 1 FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($id_user));
			
			if (empty($exist)) {
				$dbconn->Execute(
					'INSERT INTO '.BILLING_USER_ACCOUNT_TABLE.' SET id_user = ?, account_curr = ?, date_refresh = NOW()',
					array($id_user, $user_account));
			} else {
				$dbconn->Execute(
					'UPDATE '.BILLING_USER_ACCOUNT_TABLE.' SET account_curr = ?, date_refresh = NOW() WHERE id_user = ?',
					array($user_account, $id_user));
			}
		
		break;
	}
	
	UserListBilling($id_user);
	
	return ;
}


function GroupListBilling($err = '')
{
	global $smarty, $dbconn, $config, $lang;
	
	AdminMainMenu($lang['pays']);
	
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2'));
	
	$form['costunits'] = $settings['site_unit_costunit'];
	$form['costunits_2'] = $settings['site_unit_costunit_2'];
	
	// get all groups and period definitions and assign to smarty array
	//
	$strSQL =
		'SELECT id, name
		   FROM '.GROUPS_TABLE.'
		  WHERE type IN ("f","t") AND is_gender_group = ?
	   ORDER BY sort ASC, gender DESC';
	
	$rs = $dbconn->Execute($strSQL, array($config['use_gender_membership']));
	
	$i = 0;
	$groups = array();
	
	while (!$rs->EOF)
	{
		$row = $rs->GetRowAssoc(false);
		
		$groups[$i]['id'] = $row['id'];
		$groups[$i]['name'] = $row['name'];
		
		$j = 0;
		
		$strSQL =
			'SELECT id, amount, period, cost, cost_2, recurring, upgrade, trial_amount, trial_period, trial_cost, trial_cost_2, status
			   FROM '.GROUP_PERIOD_TABLE.'
			  WHERE id_group = ?
		   ORDER BY upgrade DESC, recurring DESC, amount, trial_amount';
		
		$rs_p = $dbconn->Execute($strSQL, array($row['id']));
		
		while (!$rs_p->EOF)
		{
			$row_p = $rs_p->GetRowAssoc(false);
			
			$groups[$i]['period'][$j]['id'] = $row_p['id'];
			$groups[$i]['period'][$j]['amount'] = $row_p['amount'];
			$groups[$i]['period'][$j]['period'] = $lang['pays']['periods'][$row_p['period']];
			$groups[$i]['period'][$j]['cost'] = sprintf('%01.2f', $row_p['cost']);
			$groups[$i]['period'][$j]['cost_2'] = sprintf('%01.2f', $row_p['cost_2']);
			$groups[$i]['period'][$j]['recurring'] = $row_p['recurring'];
			$groups[$i]['period'][$j]['upgrade'] = $row_p['upgrade'];
			$groups[$i]['period'][$j]['status'] = $row_p['status'];
			
			if ($row_p['recurring'] == '1') {
				$groups[$i]['period'][$j]['trial_amount'] = $row_p['trial_amount'];
				$groups[$i]['period'][$j]['trial_period'] = $lang['pays']['periods'][$row_p['trial_period']];
				$groups[$i]['period'][$j]['trial_cost'] = sprintf('%01.2f', $row_p['trial_cost']);
				$groups[$i]['period'][$j]['trial_cost_2'] = sprintf('%01.2f', $row_p['trial_cost_2']);
			}
			
			$rs_p->MoveNext();
			$j++;
		}
		
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('groups', $groups);
	
	// get data for edit period and assign to smarty array
	//
	if (isset($_GET['cmd']) && $_GET['cmd'] == 'edit') {
		$id = (int) $_GET['id'];
		
		$rs = $dbconn->Execute('SELECT * FROM '.GROUP_PERIOD_TABLE.' WHERE id = ?', array($id));
		$row = $rs->GetRowAssoc(false);
		
		$smarty->assign('edit', $row);
		$smarty->assign('cmd', 'edit');
	}
	
	// prepare and display template
	//
	if ($err) {
		$form['err'] = $err;
	}
	
	$smarty->assign('form', $form);
	$smarty->assign('err', $lang['err']);
	$smarty->assign('header', $lang['pays']);
	$smarty->assign('button', $lang['button']);
	
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_billing_group_cost_table.tpl');
	
	exit;
}


function GroupsChange()
{
	global $smarty, $dbconn, $lang;
	
	$err = '';
	
	$cmd = isset($_REQUEST['cmd']) ? $_REQUEST['cmd'] : '';
	
	switch ($cmd)
	{
		case 'delete':
			
			$id = intval($_GET['id']);
			
			$exist = $dbconn->getOne('SELECT 1 FROM '.BILLING_USER_PERIOD_TABLE.' WHERE id_group_period = ?', array($id));
			
			if (!empty($exist)) {
				$err = 'There still exist records in the table "'.BILLING_USER_PERIOD_TABLE.'" with this period id. Please contact your database admin.';
			} else {
				$dbconn->Execute('DELETE FROM '.GROUP_PERIOD_TABLE.' WHERE id = ?', array($id));
				$err = 'The period definition has been deleted.';
			}
			
			$smarty->assign('cmd', '');
			
			GroupListBilling($err);
			
		break;
		
		case 'edit':
		
			$data['id']				= (int) $_POST['id'];
			
			$data['id_group']		= (int) $_POST['id_group'];
			$data['amount']			= (int) $_POST['amount'];
			$data['period']			= $_POST['period'];
			$data['cost']			= (float) $_POST['cost'];
			$data['cost_2']			= (float) $_POST['cost_2'];
			$data['recurring']		= !empty($_POST['recurring']) ? '1' : '0';
			$data['upgrade']		= !empty($_POST['upgrade']) ? '1' : '0';
			$data['trial_amount']	= (int) $_POST['trial_amount'];
			$data['trial_period']	= $_POST['trial_period'];
			$data['trial_cost']		= (float) $_POST['trial_cost'];
			$data['trial_cost_2']	= (float) $_POST['trial_cost_2'];
			$data['status']			= !empty($_POST['status']) ? '1' : '0';
			
			if ($data['id'] && $data['id_group'] && $data['period']) // $date['amount'] removed to allow unlimited periods
			{
				$sql =
					'SELECT 1 
					   FROM '.GROUP_PERIOD_TABLE.' 
					  WHERE id_group = ? AND period = ? AND amount = ? 
					    AND recurring = ? AND upgrade = ? AND trial_period = ? 
					    AND trial_amount = ? AND status = "1"
						AND id <> ?';
				
				$exist = $dbconn->getOne($sql, array($data['id_group'], $data['period'], $data['amount'], (string)$data['recurring'],
					(string)$data['upgrade'], $data['trial_period'], $data['trial_amount'], $data['id']));
				
				if (empty($exist))
				{
					$sql = 
						'UPDATE '.GROUP_PERIOD_TABLE.' SET
							id_group		= ?,
							amount			= ?,
							period			= ?,
							cost			= ?,
							cost_2			= ?,
							recurring		= ?,
							upgrade			= ?,
							trial_amount	= ?,
							trial_period	= ?,
							trial_cost		= ?,
							trial_cost_2	= ?,
							status			= ?
						WHERE id = ?';
					
					$dbconn->Execute($sql, array($data['id_group'], $data['amount'], $data['period'], $data['cost'], $data['cost_2'],
						(string)$data['recurring'], (string)$data['upgrade'], $data['trial_amount'], $data['trial_period'], $data['trial_cost'],
						$data['trial_cost_2'], (string)$data['status'], $data['id']));
					
					$err = 'The period definition has been updated.';
					
					$smarty->assign('cmd', '');
				}
				else
				{
					$err = 'A period with this cost and period lenght has already been defined.';
					
					$smarty->assign('edit', $data);
					$smarty->assign('cmd', 'edit');
				}
			}
			else
			{
				$err = 'Please provide at least a group, a period and a cost for the period definition.';
				
				$smarty->assign('edit', $data);
				$smarty->assign('cmd', 'edit');
			}
			
			GroupListBilling($err);
			
		break;
		
		case 'add':
			
			$data['id_group']		= (int) $_POST['id_group'];
			$data['amount']			= (int) $_POST['amount'];
			$data['period']			= $_POST['period'];
			$data['cost']			= (float) $_POST['cost'];
			$data['cost_2']			= (float) $_POST['cost_2'];
			$data['recurring']		= !empty($_POST['recurring']) ? '1' : '0';
			$data['upgrade']		= !empty($_POST['upgrade']) ? '1' : '0';
			$data['trial_amount']	= (int) $_POST['trial_amount'];
			$data['trial_period']	= $_POST['trial_period'];
			$data['trial_cost']		= (float) $_POST['trial_cost'];
			$data['trial_cost_2']	= (float) $_POST['trial_cost_2'];
			$data['status']			= !empty($_POST['status']) ? '1' : '0';
			
			if ($data['id_group'] && $data['period']) // $date['amount'] removed to allow Platinum Upgrade
			{
				$sql =
					'SELECT 1 
					   FROM '.GROUP_PERIOD_TABLE.' 
					  WHERE id_group = ? AND period = ? AND amount = ? 
					    AND recurring = ? AND upgrade = ? AND trial_period = ? 
					    AND trial_amount = ? AND status = "1"';
				
				$exist = $dbconn->getOne($sql, array($data['id_group'], $data['period'], $data['amount'], (string)$data['recurring'],
					(string)$data['upgrade'], $data['trial_period'], $data['trial_amount']));
				
				if (empty($exist))
				{
					$dbconn->Execute(
						'INSERT INTO '.GROUP_PERIOD_TABLE.' SET
							id_group = ?, amount = ?, period= ?, cost = ?, cost_2 = ?,
							recurring = ?, upgrade = ?, trial_amount = ?, trial_period = ?, trial_cost = ?,
							trial_cost_2 = ?, status = ?',
						array(
							$data['id_group'], $data['amount'], $data['period'], $data['cost'], $data['cost_2'],
							(string)$data['recurring'], (string)$data['upgrade'], $data['trial_amount'], $data['trial_period'], $data['trial_cost'],
							$data['trial_cost_2'], (string)$data['status'])
					);
					
					$id_period = $dbconn->Insert_ID();
					
					$dbconn->Execute('INSERT INTO '.BILLING_PERIODS_CCBILL_TABLE.' SET id_group_period = ?, ccbill_sub_id = "0000000000"', array($id_period));
					$dbconn->Execute('INSERT INTO '.BILLING_PERIODS_ALLOPASS_TABLE.' SET id_group_period = ?', array($id_period));
					
					$err = 'The period record has been inserted.<br /><br />' . $lang['pays']['ccbill_allopas_admin_note'];
					
					$smarty->assign('cmd', '');
				}
				else
				{
					$err = 'A period with this cost and period lenght has already been defined.';
					
					$smarty->assign('edit', $data);
					$smarty->assign('cmd', 'add');
				}
			}
			else
			{
				$err = 'Please provide at least a group, a period and a cost for the new period definition.';
				$smarty->assign('edit', $data);
				$smarty->assign('cmd', 'add');
			}
			
			GroupListBilling($err);
			
		break;
	}
	
	return;
}


function SettingsBilling($err = '')
{
	global $smarty, $dbconn, $config, $lang;
	
	$settype = isset($_REQUEST['settype']) ? $_REQUEST['settype'] : 'general';;
	
	AdminMainMenu($lang['pays']);
	
	$form = array();
	
	if ($err) {
		$form['err'] = $err;
	}
	
	$rs = $dbconn->Execute('SELECT template_name, name FROM '.BILLING_PAYSYSTEMS_TABLE);
	$i = 0;
	$paysystems = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$paysystems[$i]['value'] = $row['template_name'];
		$paysystems[$i]['name'] = $lang['pays']['option_'.$row['template_name']];
		$paysystems[$i]['name_orig'] = $row['name'];
		$i++;
		$rs->MoveNext();
	}
	
	$smarty->assign('paysystems', $paysystems);
	
	if ($settype == 'general')
	{
		$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2'));
		
		$data['site_unit_costunit'] = $settings['site_unit_costunit'];
		$data['site_unit_costunit_2'] = $settings['site_unit_costunit_2'];
		
		$smarty->assign('data', $data);
		
		$rs = $dbconn->Execute('SELECT abbr FROM '.UNITS_TABLE);
		
		$i = 0;
		$currency = array();
		
		while (!$rs->EOF) {
			$currency[$i]['value'] = $rs->fields[0];
			$rs->MoveNext();
			$i++;
		}
		
		$smarty->assign('currency', $currency);
	}
	else
	{
		include_once '../include/systems/functions/'.$settype.'.php';
		$data = getBillingData($settype);
		$smarty->assign('data', $data);
	}
	
	$smarty->assign('settype', $settype);
	$smarty->assign('header', $lang['pays']);
	$smarty->assign('button', $lang['button']);
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_billing_settings_table.tpl');
	exit;
}


function SaveSettingsBilling()
{
	global $dbconn, $config, $lang;
	
	$settype = isset($_REQUEST['settype']) ? $_REQUEST['settype'] : '';
	
	$settings = GetSiteSettings(array('use_pilot_module_affiliate', 'use_pilot_module_giftshop', 'site_unit_costunit', 'site_email'));
	
	if ($settype == 'general')
	{
		// main currency
		$site_unit_costunit = stripslashes(strval($_POST['currency']));
		
		if (!strlen($site_unit_costunit)) {
			$err = $lang['err']['invalid_fields'].'<br>'.$lang['pays']['currency'];
			SettingsBilling($err);
			return;
		}
		
		$dbconn->Execute('UPDATE '.SETTINGS_TABLE.' SET value = ? WHERE name = "site_unit_costunit"', array($site_unit_costunit));
		
		// currency 2
		$site_unit_costunit_2 = stripslashes(strval($_POST['currency_2']));
		
		$dbconn->Execute('UPDATE '.SETTINGS_TABLE.' SET value = ? WHERE name = "site_unit_costunit_2"', array($site_unit_costunit_2));
		
		// calculate new prices if exchange rate has been changed
		$curr_rate = round(1 / floatval($_POST['curr_rate']), 3);
		
		if (!$curr_rate) {
			SettingsBilling($lang['err']['small_curr_rate']);
		}
		
		if ($curr_rate != 1)
		{
			$dbconn->Execute('UPDATE '.BILLING_USER_ACCOUNT_TABLE.' SET account_curr = account_curr * '.$curr_rate.' WHERE account_curr > 0');
			$dbconn->Execute('UPDATE '.GROUP_PERIOD_TABLE.' SET cost = ROUND(cost * '.$curr_rate.', 2) WHERE cost > 0');
			$dbconn->Execute('UPDATE '.SETTINGS_TABLE.' SET value = ROUND(value * '.$curr_rate.', 2) WHERE name = "refer_friend_price"');
			$dbconn->Execute('UPDATE '.BANNERS_AREA_TABLE.' SET price = ROUND(price * '.$curr_rate.', 2)');
			
			if ($settings['use_pilot_module_affiliate']) {
				$dbconn->Execute('UPDATE '.AFF_USER_PRODUCT_SOLD_TABLE.' SET price = price * '.$curr_rate);
			}
			
			if ($settings['use_pilot_module_giftshop']) {
				$dbconn->Execute('UPDATE '.GIFTSHOP_CATALOG_ITEM.' SET price = price * '.$curr_rate);
			}
			
			$strSQL =
				'SELECT u.id, u.login, u.email, u.fname, u.sname, u.site_language, u.gender
				   FROM '.BILLING_USER_ACCOUNT_TABLE.' bua
			  LEFT JOIN '.USERS_TABLE.' u ON bua.id_user = u.id
				  WHERE bua.id_user = u.id AND bua.account_curr > 0';
			
			$rs = $dbconn->Execute($strSQL);
			
			if (!$rs->EOF)
			{
				while (!$rs->EOF)
				{
					$row = $rs->GetRowAssoc(false);
					
					$id_user = $row['id'];
					
					// language
					$site_lang = $row['site_language'];
					
					// include mail language file
					$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
					$lang_mail = array();
					include $config['path_lang'].'mail/'.$lang_file;
					
					// sufix
					$suffix = ($row['gender'] == GENDER_MALE) ? '_e' : '_t';
					
					// content
					$content_array = $row;
					
					$replace_from = array('[curr1]', '[rate]', '[curr2]');
					$replace_to = array($settings['site_unit_costunit'], $curr_rate, $site_unit_costunit);
					
					$content_array['message'] = str_replace($replace_from, $replace_to, $lang_mail['change_curr'.$suffix]['message']);
					$content_array['urls'] = GetUserEmailLinks();
					
					// subject
					$subject = $lang_mail['change_curr'.$suffix]['subject'];
					
					SendMail($site_lang, $content_array['email'], $config['site_email'], $subject, $content_array, 'mail_change_currency_user', null,
						$content_array['fname'], '', 'change_curr', $content_array['gender']);
					
					// internal message
					$subject = $lang['pays']['subj_exchange_rate'];
					
					$strSQL =
						'INSERT INTO '.MAILBOX_TABLE.' SET
							id_from = ?, id_to = ?, subject = ?, body = ?, date_creation = NOW(), was_read = "0", deleted_from = "0", deleted_to = "0"';
					$dbconn->Execute($strSQL, array(ID_ADMIN, $id_user, $subject, $content_array['message']));
					
					$rs->MoveNext();
				}
			}
			
			header('location: '.$config['server'].$config['site_root'].'/admin/admin_pays.php?sel=settings');
			exit;
		}
	}
	else
	{
		include_once '../include/systems/functions/'.$settype.'.php';
		
		$err = setBillingData($settype, $_POST);
		
		if ($err) {
			SettingsBilling($err);
			return;
		}
	}
	
	SettingsBilling();
	
	return;
}


function ServicesManage()
{
	$par = isset($_REQUEST['par']) ? $_REQUEST['par'] : 'list';
	$service = isset($_REQUEST['service']) ? addslashes($_REQUEST['service']) : 'lift_up';
	
	switch ($par)
	{
		case 'list':
			ListService($service);
			break;
		case 'save':
			SaveService($service);
			break;
	}
	
	exit;
}


function ListService($service)
{
	global $smarty, $dbconn, $lang, $config;
	
	AdminMainMenu($lang['pays']);
	
	$cur = GetSiteSettings('site_unit_costunit');
	
	$rs = $dbconn->Execute('SELECT id, settings_name, settings_value FROM '.PAYMENT_SERVICES_SETTINGS_TABLE.' WHERE service_name = ?', array($service));
	
	if ($rs->fields[0] > 0)
	{
		$i = 0;
		$settings = array();
		
		while (!$rs->EOF)
		{
			if (isset($lang['pays']['service_setting_name'][$service][$rs->fields[1]])) {
				$name = str_replace('[cur]', $cur, $lang['pays']['service_setting_name'][$service][$rs->fields[1]]);
			}
			$settings[$i]['name'] = $rs->fields[1];
			$settings[$i]['title'] = $name;
			$settings[$i]['value'] = stripslashes($rs->fields[2]);
			$rs->MoveNext();
			$i++;
		}
		
		$smarty->assign('settings', $settings);
	}
	
	$smarty->assign('service', $service);
	$smarty->assign('service_name', $lang['pays']['service'][$service]);
	$smarty->assign('header', $lang['pays']);
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_payment_service_list.tpl');
	
	exit;
}


function SaveService($service)
{
	global $dbconn;
	
	if (!empty($_REQUEST['settings'])) {
		foreach ($_REQUEST['settings'] as $key => $value) {
			$strSQL =
				'UPDATE '.PAYMENT_SERVICES_SETTINGS_TABLE.' SET
						settings_value = ?
				  WHERE service_name = ? AND settings_name = ?';
			$dbconn->Execute($strSQL, array($value, $service, $key));
		}
	}
	ListService($service);
}


function SendPaymentApproval($id_user, $paysystem, $currency, $payamount, $prod_id = NULL)
{
	global $config, $dbconn;
	
	// user data
	$rs = $dbconn->Execute('SELECT login, fname, sname, email, gender, site_language FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
	$row = $rs->GetRowAssoc(false);
	
	// language
	$site_lang = !empty($row['site_language']) ? $row['site_language'] : $config['default_lang'];
	
	// include mail language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	// content array
	$content_array				= array();
	$content_array['login'] 	= $row['login'];
	$content_array['fname'] 	= $row['fname'];
	$content_array['sname'] 	= $row['sname'];
	$content_array['email'] 	= $row['email'];
	$content_array['paysystem'] = $paysystem;
	$content_array['urls']		= GetUserEmailLinks();
	
	// gender specific langage items
	$suffix = ($row['gender'] == GENDER_MALE ? '_e' : '_t');
	
	//Send approval mail to lady if the payment is 9,997 /offline trasfers
	
	if (!empty($prod_id) && $prod_id == MM_PLATINUM_LADY_PERIOD_ID && $currency == 'THB') {
		if ($paysystem == 'atm_payment') {
			$subject = $lang_mail['cron_approve_atm_pay_t']['subject'];
			$content_array['message'] = $lang_mail['cron_approve_atm_pay_t']['message'];
		}
		if ($paysystem == 'wire_transfer'){
			$subject = $lang_mail['cron_approve_wire_pay_t']['subject'];
			$content_array['message'] = $lang_mail['cron_approve_wire_pay_t']['message'];
		}
	} else {
		// subject
		$subject = $lang_mail['offline_payment_approval'.$suffix]['subject'];
		
		// message
		$content_array['message'] = $lang_mail['offline_payment_approval'.$suffix]['message'][$paysystem];
		$content_array['message'] = str_replace('[userpayment]', $currency.'&nbsp;'.$payamount, $content_array['message']);
		$content_array['message'] = str_replace('[username]', $row['login'], $content_array['message']);
	}
	
	$name_to = trim($row['fname'].' '.$row['sname']);
	
	// send message
	$mail_err = SendMail($site_lang, $row['email'], $config['site_email'], $subject, $content_array,
		'mail_noti_simple_generic_user', null, $name_to, '', 'offline_payment_approval', $row['gender']);
	
	// internal message
	$body = $lang_mail['generic'.$suffix]['hello'].' '.$row['fname'].',<br><br>';
	$body.= $content_array['message'].'<br><br>';
	$body.= $lang_mail['generic'.$suffix]['admin_regards'];
	
	$dbconn->Execute(
		'INSERT INTO '.MAILBOX_TABLE.' SET
			id_to = ?, id_from = ?, subject = ?, body = ?, was_read = "0", deleted_to = "0", deleted_from = "0", date_creation = NOW()',
		array($id_user, ID_ADMIN, $subject, $body));
	
	return $mail_err;
}

function MyStore_Offline_Payment_Approval_User_Message($order_id, $paysystem, $currency, $amount)
{
	global $config, $dbconn;
	
	// collect data
	$rs = $dbconn->Execute(
		'SELECT o.id_user_from, o.id_user_to,
				u1.login AS login_from, u1.fname AS fname_from, u1.sname AS sname_from, u1.gender AS gender_from,
				u1.email AS email_from, u1.site_language AS site_language_from,
				u2.login AS login_to, u2.fname AS fname_to, u1.sname AS sname_to, u2.gender AS gender_to,
				u1.email AS email_to, u1.site_language AS site_language_to
		   FROM '.GIFTSHOP_ORDERS.' o
	 INNER JOIN '.USERS_TABLE.' u1 ON o.id_user_from = u1.id
	 INNER JOIN '.USERS_TABLE.' u2 ON o.id_user_to = u2.id
		  WHERE o.id = ?',
		  array($order_id));
	$row = $rs->GetRowAssoc(false);
	
	//------------------
	// external message
	//------------------
	
	// language
	$site_lang = !empty($row['site_language_from']) ? $row['site_language_from'] : $config['default_lang'];
	
	// include mail language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	// content
	$content_array				= array();
	$content_array['urls']		= GetUserEmailLinks();
#	$content_array['login']		= stripslashes($row['login_from']);
	$content_array['fname']		= stripslashes($row['fname_from']);
#	$content_array['sname']		= stripslashes($row['sname_from']);
#	$content_array['email']		= stripslashes($row['email_from']);
#	$content_array['paysystem']	= $paysystem;
	
	// gender suffix
	$suffix = ($row['gender_from'] == GENDER_MALE) ? '_e' : '_t';
	
	// subject
	$subject = $lang_mail['mystore_offline_payment_approval'.$suffix]['subject'];
	$subject = str_replace('[ORDER_ID]', $order_id, $subject);
	
	// message
	$amount = $currency.'&nbsp;'.$amount;
	
	$content_array['message'] = $lang_mail['mystore_offline_payment_approval'.$suffix]['message'][$paysystem];
	$content_array['message'] = str_replace('[ORDER_ID]', $order_id, $content_array['message']);
	$content_array['message'] = str_replace('[FNAME_TO]', $row['fname_to'], $content_array['message']);
	$content_array['message'] = str_replace('[AMOUNT]', $amount, $content_array['message']);
	$content_array['message'] = str_replace('[LOGIN_FROM]', $row['login_from'], $content_array['message']);
	
	$name_to = trim($row['fname_from'].' '.$row['sname_from']);
	
	// send message
	$mail_err = SendMail($site_lang, $row['email_from'], $config['site_email'], $subject, $content_array,
		'mail_noti_simple_generic_user', null, $name_to, '', 'mystore_offline_payment_approval', $row['gender_from']);
	
	// internal message
	$body = $lang_mail['generic'.$suffix]['hello'].' '.$row['fname_from'].',<br><br>';
	$body.= $content_array['message'].'<br><br>';
	$body.= $lang_mail['generic'.$suffix]['admin_regards'];
	
	$dbconn->Execute(
		'INSERT INTO '.MAILBOX_TABLE.' SET
			id_from = ?, id_to = ?, subject = ?, body = ?, was_read = "0", deleted_to = "0", deleted_from = "0", date_creation = NOW()',
		array(ID_ADMIN, $row['id_user_from'], $subject, $body));
	
	return $mail_err;
}

?>