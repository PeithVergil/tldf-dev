<?php

//-------------------------------------
// Meet Me Now
//-------------------------------------

function AssignUserGroup($id_user, $id_period, $fd = null, $force = false)
{
	global $dbconn, $config, $config_admin;
	
	$debug = false;
	
	#####
	if ($fd) fwrite($fd, "### function entry: AssignUserGroup\n");
	if ($fd) fwrite($fd, "### $id_period is the group id\n");
	#####
	
	/**
	 * BASIC USER DATA
	 **/
	
	/*
	$rs = $dbconn('SELECT id, gender FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
	$row = $rs->GetRowAssoc(false);
	
	if (empty($row['id'])) {
		if ($fd) fwrite($fd, "error: user with id=$id_user not found in table ".USERS_TABLE."\n");
		return 0;
	}
	
	$gender = (int) $row['gender'];
	
	unset($rs, $row);
	
	#####
	$msg = "\$id_user=$id_user;\$gender=$gender";
	if ($debug) echo $msg."<br/>";
	if ($fd) fwrite($fd, $msg."\n");
	#####
	*/
	
	/**
	 * BASIC PERIOD DATA
	 **/
	
	$rs = $dbconn->Execute('SELECT id_group, amount, period, recurring FROM '.GROUP_PERIOD_TABLE.' WHERE id = ?', array($id_period));
	$row = $rs->GetRowAssoc(false);
	if ($fd) fwrite($fd, 'SELECT id_group, amount, period, recurring FROM '.GROUP_PERIOD_TABLE.' WHERE id = ?');
	if ($fd) fwrite($fd, print_r($row,true));
	if (empty($row['id_group'])) {
		if ($fd) fwrite($fd, "error: id_period not found in table ".GROUP_PERIOD_TABLE."\n");
		return 0;
	}
	
	$id_group = $row['id_group'];
	$amount = $row['amount'];
	$period = $row['period'];
	$recurring = (int) $row['recurring'];
	
	unset($row);
	$rs->Free();
	
	#####
	$msg = "\$id_group=$id_group;\$amount=$amount;\$period=$period;\$recurring=$recurring";
	if ($debug) echo $msg."<br/>";
	if ($fd) fwrite($fd, $msg."\n");
	#####
	
	/**
	 * CALCULATE PERIOD DAYS
	 **/
	
	if ($recurring) {
		$period_days = 0;
	} else {
		$period_days = $amount * $config_admin['pay_period'][ $period ];
		
	}
	#####
	$msg = "calculated \$period_days=$period_days";
	if ($debug) echo $msg."<br/>";
	if ($fd) fwrite($fd, $msg."\n");
	#####
	
	/**
	 * GET CURRENT PERIOD DATA
	 **/
	
	$rs = $dbconn->Execute(
		'SELECT id, id_group_period, date_begin, date_end, UNIX_TIMESTAMP(date_end) AS ts_end
		   FROM '.BILLING_USER_PERIOD_TABLE.'
		  WHERE id_user = ?',
		  array($id_user));
	$row = $rs->GetRowAssoc(false);
	
	if (empty($row['id']))
	{
		// user is first time member
		
		#####
		$msg = "user is a first time member";
		if ($debug) echo $msg."<br/>";
		if ($fd) fwrite($fd, $msg."\n");
		#####
		
		$date_begin			= date('Y-m-d H:i:s');
		$date_end			= date('Y-m-d H:i:s');
		
		$old_date_end		= date('Y-m-d H:i:s');
		$old_date_end_ts	= time();
	}
	else
	{
		// user already had a membership
		
		#####
		$msg = "user already had a membership";
		if ($debug) echo $msg."<br/>";
		if ($fd) fwrite($fd, $msg."\n");
		#####
		
		// keep old date_begin
		## $date_begin		= date('Y-m-d H:i:s');
		$date_begin			= $row['date_begin'];
		$date_end			= $row['date_end'];
		
		$old_date_end		= $row['date_end'];
		$old_date_end_ts	= (int) $row['ts_end'];
	}
	
	/**
	 * ASSIGN GROUP
	 **/
	
	/* as we do not need multiple group assignments, we update the current group
	
	// delete old group assignment
	$dbconn->Execute('DELETE FROM '.USER_GROUP_TABLE.' WHERE id_user='.$id_user);
	
	// insert new group assignment
	$dbconn->Execute('INSERT INTO '.USER_GROUP_TABLE.' (id_user, id_group) VALUES ('.$id_user.', '.$id_group.')');
	*/
	
	$old_id_group = $dbconn->getOne('SELECT id_group FROM '.USER_GROUP_TABLE.' WHERE id_user = ?', array($id_user));
	
	if (empty($old_id_group))
	{
		$dbconn->Execute('INSERT INTO '.USER_GROUP_TABLE.' SET id_user = ?, id_group = ?', array($id_user, $id_group));
		
		#####
		$msg = "record was inserted into ".USER_GROUP_TABLE." with id_user=$id_user, id_group=$id_group";
		if ($debug) echo $msg."<br/>";
		if ($fd) fwrite($fd, $msg."\n");
		#####
		
		if (SOLVE360_CONNECTION) {
			require_once $config['site_path'].'/include/Solve360Service.php';
			$solve360Service = new Solve360Service(SOLVE360_USER, SOLVE360_TOKEN);
			
			$solve360 = array();
			require $config['site_path'].'/include/Solve360CustomFields.php';
			
			$new_group_name = $dbconn->getOne('SELECT name FROM '.GROUPS_TABLE.' WHERE id = ?', array($id_group));
			
			$contactData = array(
				$solve360['Current Group'] => $new_group_name,
			);
			
			$rs = $dbconn->Execute('SELECT id_solve360, login FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
			$id_solve360 = $rs->fields[0];
			$login = $rs->fields[1];
			$rs->Free();
			
			if ($id_solve360) {
				$contact = $solve360Service->editContact($id_solve360, $contactData);
				if (isset($contact->errors)) {
					$subject = 'Error while upgrading Current Group';
					solve360_api_error($contact, $subject, $login);
				}
			}
			// maybe add contact
		}
	}
	else
	{
		#####
		$msg = "old_id_group=$old_id_group";
		if ($debug) echo $msg."<br/>";
		if ($fd) fwrite($fd, $msg."\n");
		if ($fd) fwrite($fd, $id_group . "is the group I am tring to get in. \n");
		#####
		
		if ($force)
		{
			$update_group_flag = true;
			
			#####
			$msg = "Force group update, even when the new group is having a lower rank than the old group.";
			if ($debug) echo $msg."<br/>";
			if ($fd) fwrite($fd, $msg."\n");
			#####
			
			if (isset($_POST['add_days_move_user']) && $_POST['add_days_move_user'] == 'yes')
			{
				#####
				$msg = "Admin has chosen to add days so Days are added.";
				if ($debug) echo $msg."<br/>";
				if ($fd) fwrite($fd, $msg."\n");
				#####
				$add_days = true;
			}
			else
			{
				#####
				$msg = "Admin has chosen not to add days so Days are not added.";
				if ($debug) echo $msg."<br/>";
				if ($fd) fwrite($fd, $msg."\n");
				#####
				$add_days = false;
			}
			
			if (isset($_POST['newDate']) && isset($_POST['add_days_move_user']) && isset($_POST['reset_date']))
			{
				if ($_POST['add_days_move_user'] == 'no' && $_POST['reset_date'] == 'yes')
				{
					$rawDate = $_POST['newDate'];
					$rawDate = $rawDate .' 00:00:00';
					$date_end = $rawDate;
				}
			}
			
			switch ($id_group)
			{
				case MM_TRIAL_GUY_ID:
				case MM_REGULAR_GUY_ID:
				case MM_PLATINUM_GUY_ID:
					$date_end = UNLIMITED_DATE_END;
				break;

				case MM_TRIAL_LADY_ID:
					$date_end = UNLIMITED_DATE_END;
				break;

				case MM_REGULAR_LADY_ID:
					if ($add_days && $period_days > 0) {
						if (substr($old_date_end, 0, 10) == substr(UNLIMITED_DATE_END, 0, 10)) {
							$date_end = date('Y-m-d H:i:s', time() + $period_days*24*60*60);
						} elseif ($old_date_end_ts < time()) {
							$date_end = date('Y-m-d H:i:s', time() + $period_days*24*60*60);
						} else {
							$date_end = date('Y-m-d H:i:s', $old_date_end_ts + $period_days*24*60*60);
						}
					}
				break;

				case MM_PLATINUM_LADY_FIRST_INS_ID:
					if ($add_days && $period_days > 0) {
						$date_end = date('Y-m-d H:i:s', time() + $period_days*24*60*60);
					}
				break;

				case MM_PLATINUM_LADY_SECOND_INS_ID:
					if ($old_id_group == MM_PLATINUM_LADY_FIRST_INS_ID) {
						// special situation, we might need to add days to current expiration date
						if ($add_days && $period_days > 0) {
							if (substr($old_date_end, 0, 10) == substr(UNLIMITED_DATE_END, 0, 10)) {
								$date_end = date('Y-m-d H:i:s', time() + $period_days*24*60*60);
							} elseif ($old_date_end_ts < time()) {
								$date_end = date('Y-m-d H:i:s', time() + $period_days*24*60*60);
							} else {
								$date_end = date('Y-m-d H:i:s', $old_date_end_ts + $period_days*24*60*60);
							}
						}
					} else {
						if ($add_days && $period_days > 0) {
							$date_end = date('Y-m-d H:i:s', time() + $period_days*24*60*60);
						}
					}
				break;

				case MM_PLATINUM_LADY_ID:
					$date_end = UNLIMITED_DATE_END;
				break;
			}
		}
		else
		{
			// update only if new group is having a higher rank
			$update_group_flag = false;
			
			switch ($id_group)
			{
				case MM_TRIAL_GUY_ID:
					if ($old_id_group == MM_SIGNUP_GUY_ID) {
						$update_group_flag = true;
						$date_end = UNLIMITED_DATE_END;
					}
				break;
				
				case MM_REGULAR_GUY_ID:
					if ($old_id_group == MM_SIGNUP_GUY_ID || $old_id_group == MM_TRIAL_GUY_ID) {
						$update_group_flag = true;
						$date_end = UNLIMITED_DATE_END;
					}
				break;
				
				case MM_PLATINUM_GUY_ID:
					$userField = "mm_platinum_paid";
					if ($id_period == MM_PLATINUM_GUY_PERIOD_ID) {
						// platinum upgrade
						switch ($old_id_group) {
							case MM_PLATINUM_GUY_APPLIED_ID:
								// user accidentally buys platinum upgrade after buying platinum upgrade before and stays in platinum applied group
							break;
							case MM_PLATINUM_GUY_ID:
								// user accidentally buys platinum upgrade while being in platinum group and stays in platinum group
							break;
							default:
								// normal platinum upgrade 
								$id_group = MM_PLATINUM_GUY_APPLIED_ID;
								$update_group_flag = true;
							break;
						}
					} else {
						// platinum renewal, user stays in old group
					}
					// $date_end remains as is
				break;
				
				case MM_TRIAL_LADY_ID:
					if ($old_id_group == MM_SIGNUP_LADY_ID) {
						$update_group_flag = true;
						$date_end = UNLIMITED_DATE_END;
					}
				break;
				
				case MM_REGULAR_LADY_ID:
					switch ($old_id_group) {
						case MM_SIGNUP_LADY_ID:
						case MM_TRIAL_LADY_ID:
							$update_group_flag = true;
							if ($period_days > 0) {
								$date_end = date('Y-m-d H:i:s', time() + $period_days*24*60*60);
							}
						break;
						case MM_REGULAR_LADY_ID:
							// group stays the same, just extend date_end
							if ($period_days > 0) {
								if ($old_date_end_ts < time()) {
									$date_end = date('Y-m-d H:i:s', time() + $period_days*24*60*60);
								} else {
									$date_end = date('Y-m-d H:i:s', $old_date_end_ts + $period_days*24*60*60);
								}
							}
						break;
						case MM_PLATINUM_LADY_APPLIED_ID:
							// group stays the same, just extend date end
							// it's tricky and something which should never happen, need for discussion
							// user will loose this time when she is verified and won't get money back, as it
							// happens AFTER she bought the platinum upgrade
							if ($period_days > 0) {
								if (substr($old_date_end, 0, 10) == substr(UNLIMITED_DATE_END, 0, 10)) {
									$date_end = date('Y-m-d H:i:s', time() + $period_days*24*60*60);
								} elseif ($old_date_end_ts < time()) {
									$date_end = date('Y-m-d H:i:s', time() + $period_days*24*60*60);
								} else {
									$date_end = date('Y-m-d H:i:s', $old_date_end_ts + $period_days*24*60*60);
								}
							}
						break;
						case MM_PLATINUM_LADY_FIRST_INS_ID:
						case MM_PLATINUM_LADY_SECOND_INS_ID:
						case MM_PLATINUM_LADY_ID:
							// user is not allowed to buy Regular time while being Platinum
						break;
						case MM_PLATINUM_LADY_PENDING_ID:
							// group stays the same, just extend date end
							// user will loose this time when paying the next installment and won't get money back, unless
							// we calculate a discount
							if ($period_days > 0) {
								if (substr($old_date_end, 0, 10) == substr(UNLIMITED_DATE_END, 0, 10)) {
									$date_end = date('Y-m-d H:i:s', time() + $period_days*24*60*60);
								} elseif ($old_date_end_ts < time()) {
									$date_end = date('Y-m-d H:i:s', time() + $period_days*24*60*60);
								} else {
									$date_end = date('Y-m-d H:i:s', $old_date_end_ts + $period_days*24*60*60);
								}
							}
						break;
					}
				break;
				
				case MM_PLATINUM_LADY_FIRST_INS_ID:
					$userField = "mm_first_installment_date";
					// this must be 1st installment payment
					switch ($old_id_group) {
						case MM_PLATINUM_LADY_APPLIED_ID:
							// user accidentally buys platinum 1st installment after buying some other platinum upgrade before
							// stay in platinum applied group
							// $date_end remains as is
						break;
						case MM_PLATINUM_LADY_FIRST_INS_ID:
							// user accidentally buys 1st installment while being in 1st installment group
							// stay in 1st installment group
							// $date_end remains as is
						break;
						case MM_PLATINUM_LADY_SECOND_INS_ID:
							// user accidentally buys 1st installment while being in 2nd installment group
							// stay in 2nd installment group
							// $date_end remains as is
						break;
						case MM_PLATINUM_LADY_ID:
							// user accidentally buys 1st installment while being in platinum lifetime group
							// stays in platinum lifetime group
							// $date_end remains as is
						break;
						case MM_PLATINUM_LADY_PENDING_ID:
							// user accidentally buys 1st installment while being in platinum pending group
							// move to 1st installment group
							$update_group_flag = true;
							// add days to current date
							if ($period_days > 0) {
								$date_end = date('Y-m-d H:i:s', time() + $period_days*24*60*60);
							}
						break;
						default:
							// normal platinum upgrade
							$id_group = MM_PLATINUM_LADY_APPLIED_ID;
							$update_group_flag = true;
							// $date_end remains as is
						break;
					}
				break;
				
				case MM_PLATINUM_LADY_SECOND_INS_ID:
					$userField = "mm_second_installment_date";
					
					// this must be 2nd installment payment
					switch ($old_id_group) {
						case MM_PLATINUM_LADY_APPLIED_ID:
							// stay in platinum applied group
							// $date_end remains as is
						break;
						case MM_PLATINUM_LADY_FIRST_INS_ID:
							// move to 2nd installment group
							$update_group_flag = true;
							// add days
							if ($period_days > 0) {
								if (substr($old_date_end, 0, 10) == substr(UNLIMITED_DATE_END, 0, 10)) {
									$date_end = date('Y-m-d H:i:s', time() + $period_days*24*60*60);
								} elseif ($old_date_end_ts < time()) {
									$date_end = date('Y-m-d H:i:s', time() + $period_days*24*60*60);
								} else {
									$date_end = date('Y-m-d H:i:s', $old_date_end_ts + $period_days*24*60*60);
								}
							}
						break;
						case MM_PLATINUM_LADY_SECOND_INS_ID:
							// user accidentally buys 2nd installment while being in 2nd installment group
							// stay in 2nd installment group
							// $date_end remains as is
						break;
						case MM_PLATINUM_LADY_ID:
							// user accidentally buys 2nd installment while being in platinum lifetime group
							// stay in platinum lifetime group
							// $date_end remains as is
						break;
						case MM_PLATINUM_LADY_PENDING_ID:
							// move to 2nd installment group
							$update_group_flag = true;
							// add days to current date
							if ($period_days > 0) {
								$date_end = date('Y-m-d H:i:s', time() + $period_days*24*60*60);
							}
						break;
						default:
							// user accidentally buys 2nd installment without having bought 1st installment
							// move to platinum applied group
							$id_group = MM_PLATINUM_LADY_APPLIED_ID;
							$update_group_flag = true;
							// $date_end remains as is
						break;
					}
				break;
				
				case MM_PLATINUM_LADY_ID:
					$userField = "mm_platinum_paid";
					if ($id_period == MM_PLATINUM_LADY_PERIOD_ID) {
						// platinum upgrade to lifetime
						switch ($old_id_group) {
							case MM_PLATINUM_LADY_APPLIED_ID:
								// user accidentally buys platinum lifetime after buying some other platinum upgrade before
								// stay in platinum applied group
								// $date_end remains as is
							break;
							case MM_PLATINUM_LADY_FIRST_INS_ID:
								// user accidentally buys platinum lifetime while being in 1st installment group
								// move to platinum lifetime group
								$update_group_flag = true;
								$date_end = UNLIMITED_DATE_END;
							break;
							case MM_PLATINUM_LADY_SECOND_INS_ID:
								// user accidentally buys platinum lifetime while being in 2nd installment group
								// move to platinum lifetime group
								$update_group_flag = true;
								$date_end = UNLIMITED_DATE_END;
							break;
							case MM_PLATINUM_LADY_ID:
								// user accidentally buys platinum lifetime while being in platinum lifetime group
								// stay in platinum lifetime group
								// $date_end remains as is
							break;
							case MM_PLATINUM_LADY_PENDING_ID:
								// user accidentally buys platinum lifetime while being in platinum pending group
								// move to platinum lifetime group
								$update_group_flag = true;
								$date_end = UNLIMITED_DATE_END;
							break;
							default:
								// normal platinum upgrade 
								$id_group = MM_PLATINUM_LADY_APPLIED_ID;
								$update_group_flag = true;
								// $date_end remains as is
							break;
						}
					} else {
						// must be 3rd installment payment
						switch ($old_id_group) {
							case MM_PLATINUM_LADY_APPLIED_ID:
								// stay in platinum applied group
								// $date_end remains as is
							break;
							case MM_PLATINUM_LADY_FIRST_INS_ID:
								// user accidentally buys 3rd installment while being in 1st installment group
								// stay in 1st installment group
								// $date_end remains as is
							break;
							case MM_PLATINUM_LADY_SECOND_INS_ID:
								// move to platinum lifetime group
								$update_group_flag = true;
								$date_end = UNLIMITED_DATE_END;
							break;
							case MM_PLATINUM_LADY_ID:
								// user accidentally buys 3rd installment while being in platinum lifetime group
								// stay in platinum lifetime group
								// $date_end remains as is
							break;
							case MM_PLATINUM_LADY_PENDING_ID:
								// move to platinum lifetime group
								// @TODO: check if 1st and 2nd installments have been paid
								$update_group_flag = true;
								$date_end = UNLIMITED_DATE_END;
							break;
							default:
								// user accidentally buys 3rd installment without having bought 1st installment
								// move to platinum applied group
								$id_group = MM_PLATINUM_LADY_APPLIED_ID;
								$update_group_flag = true;
								// $date_end remains as is
							break;
						}
					}
				break;
			}
			
			// override for recurring payments
			if ($recurring) {
				$date_end = UNLIMITED_DATE_END;
			}
			
			if ($update_group_flag) {
				#####
				$msg = "New group is having a higher rank than old group.";
				if ($debug) echo $msg."<br/>";
				if ($fd) fwrite($fd, $msg."\n");
				#####
				
				
				
			} else {
				
				#####
				$msg = "New group is having a lower rank than old group.";
				if ($debug) echo $msg."<br/>";
				if ($fd) fwrite($fd, $msg."\n");
				#####
			}
		}
		
		//Updating date for installments
		if (!empty($userField)) {
			
			if (PLATINUM_PAYMENT_TRIGGERS_PLATINUM_APPLIED) {
				
				if ($fd) fwrite($fd, "Check if the user has already applied for platinum \n");
				$applied_before = $dbconn->getOne('SELECT mm_platinum_applied FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
				
				if (empty($applied_before)) {
					fwrite($fd, "mm_platinum_applied was updated in table user\n");
					$dbconn->Execute('UPDATE '.USERS_TABLE.' SET mm_platinum_applied = NOW() WHERE id = ?', array($id_user));
					
					// GA_TRACKING
					ga_enqueue_event($id_user, 'platinummatching');
				} else {
					
					if ($fd) fwrite($fd, "user already applied for platinum, mm_platinum_applied was NOT updated in table user\n");
				}
			}
			
			$dbconn->Execute('UPDATE '.USERS_TABLE.' SET '.$userField.' = NOW() WHERE id = ?', array($id_user));
			
			#####
			$msg = "record was updated in ".USERS_TABLE." for $userField=". date('Y-m-d H:i:s') ." with user id=$id_user";
			if ($debug) echo $msg."<br/>";
			if ($fd) fwrite($fd, $msg."\n");
			#####
		}
			
		if ($update_group_flag === true)
		{
			$comment = '';
			if (isset($_POST['comment']) && $_POST['comment'] != '') {
				$comment = FormFilter($_POST['comment']);
			}
			
			$staff = '';
			if (isset($_POST['staff']) && $_POST['staff'] != '') {
				$staff = FormFilter($_POST['staff']);
			}
			
			//Logging Changes in user group
			$dbconn->Execute('INSERT INTO '.USER_GROUP_HISTORY_TABLE.' SET 
				`from` = ?, `to` = ?, `id_user` = ?, `staff` = ?, `comment` = ?, `date` = NOW()', 
					array($old_id_group, $id_group, $id_user, $staff, $comment));
			
			$dbconn->Execute('UPDATE '.USER_GROUP_TABLE.' SET id_group = ? WHERE id_user = ?', array($id_group, $id_user));
			
			#####
			$msg = "record was updated in ".USER_GROUP_TABLE." for id_user=$id_user with id_group=$id_group";
			if ($debug) echo $msg."<br/>";
			if ($fd) fwrite($fd, $msg."\n");
			#####
			
			if (SOLVE360_CONNECTION) {
				require_once $config['site_path'].'/include/Solve360Service.php';
				$solve360Service = new Solve360Service(SOLVE360_USER, SOLVE360_TOKEN);
				
				$solve360 = array();
				require $config['site_path'].'/include/Solve360CustomFields.php';
				
				$new_group_name = $dbconn->getOne('SELECT name FROM '.GROUPS_TABLE.' WHERE id = ?', array($id_group));
				
				$contactData = array(
					$solve360['Current Group'] => $new_group_name,
				);
				
				$rs = $dbconn->Execute('SELECT id_solve360, login FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
				$id_solve360 = $rs->fields[0];
				$login = $rs->fields[1];
				$rs->Free();
				
				if ($id_solve360) {
					$contact = $solve360Service->editContact($id_solve360, $contactData);
					#var_dump($contact); exit;
					if (isset($contact->errors)) {
						$subject = 'Error while upgrading Current Group';
						solve360_api_error($contact, $subject, $login);
					}
				}
				// maybe add contact if not found
			}
		}
	}
	
	/**
	 * UPDATE BILLING_USER_PERIOD_TABLE
	 **/
	
	if (isset($date_end)) {
		// delete old period billing record(s)
		$dbconn->Execute('DELETE FROM '.BILLING_USER_PERIOD_TABLE.' WHERE id_user = ?', array($id_user));
		
		#####
		$msg = "record deleted in table ".BILLING_USER_PERIOD_TABLE." with id_user=$id_user";
		if ($debug) echo $msg."<br/>";
		if ($fd) fwrite($fd, $msg."\n");
		#####
		
		// insert new period billing record
		$dbconn->Execute('INSERT INTO '.BILLING_USER_PERIOD_TABLE.' SET id_user = ?, id_group_period = ?, date_begin = ?, date_end = ?',
			array($id_user, $id_period, $date_begin, $date_end));
		
		#####
		$msg = "record inserted in table ".BILLING_USER_PERIOD_TABLE." with id_user=$id_user, id_period=$id_period, date_begin=$date_begin, date_end=$date_end";
		if ($debug) echo $msg."<br/>";
		if ($fd) fwrite($fd, $msg."\n");
		#####
		
		if (SOLVE360_CONNECTION) {
			require_once $config['site_path'].'/include/Solve360Service.php';
			$solve360Service = new Solve360Service(SOLVE360_USER, SOLVE360_TOKEN);
			
			$solve360 = array();
			require $config['site_path'].'/include/Solve360CustomFields.php';
			
			$contactData = array(
				$solve360['TLDF Trial Start Date'] => $date_begin,
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
		
		// GA_TRACKING
		// let's try with checking the $period_days, we could also check the $id_period and define some more constants in common.php
		switch ($period_days) {
			case 30: ga_enqueue_event($id_user, 'month1'); break;
			case 90: ga_enqueue_event($id_user, 'month3'); break;
			case 180: ga_enqueue_event($id_user, 'month6'); break;
		}
	}
#	else
#	{
		#####
#		$msg = "date_end has not been calculated => no need to change anything in ".BILLING_USER_PERIOD_TABLE;
#		if ($debug) echo $msg."<br/>";
#		if ($fd) fwrite($fd, $msg."\n");
		#####
#	}
	
	if ($fd) fwrite($fd, "### function exit: AssignUserGroup\n");
	
	return 1;
}


function SendEcard($id_order, $has_session)
{
	global $lang, $config, $dbconn;
	
	// settings
	// get card order data
	$order = GetCardOrderData($id_order);
	
	// update card order status
	$dbconn->Execute('UPDATE '.ECARDS_ORDERS_TABLE.' SET status = "approved" WHERE id = ?', array($id_order));
	
	//---------------------------------
	// build and send internal message
	//---------------------------------
	
	$subject = str_replace('[subject]', $order['card_header'], $lang['cards']['cardmail_from_user']);
	$subject = str_replace('[user]', $order['user_from_fname'], $subject);
	
	$body = '<div>';
	$body.= '<div><table cellpadding="0" cellspacing="0" border="0"><tr>';
	$body.= '<td valign="top"><img src="'.$order['image'].'"></td>';
	$body.= '<td valign="top">';
	if ($order['message']) {
		$body.= '<div style="background-color: #ffffff; z-index: 10; padding: 10px; margin-left: 25px; border: 1px solid #cccccc;">'.$order['message'].'</div>';
	}
	$body.= '</td>';
	$body.= '</tr></table></div>';
	
	if ($order['id_song'] > 0) {
		$body .= '<div style="padding-top: 5px;">';
		$body .= '<span id="player1">';
		$body .= '<script type="text/javascript">';
		$body .= 'var fv = "file='.$order['song_url'].'&autostart=false&title='.$order['song_name'].'&lightcolor=0xD12627&repeat=true";';
		$body .= 'var FO = { movie:"'.$config['server'].$config['site_root'].'/include/mp3player/mp3player.swf",width:"300",height:"20",majorversion:"7",build:"0",bgcolor:"#FFFFFF", flashvars:fv };';
		$body .= 'UFO.create(FO, "player1");';
		$body .= '</script></span></div>';
	}
	
	$body .= '</div>';
	
	$strSQL = 'INSERT INTO '.MAILBOX_TABLE.' SET id_from = ?, id_to = ?, subject = ?, body = ?, date_creation = NOW(), was_read = "0"';
	$dbconn->Execute($strSQL, array($order['id_user'], $order['id_user_to'], $subject, $body));
	
	//---------------------------------
	// update card order table
	//---------------------------------
	
	$id_mail = $dbconn->Insert_ID();
	
	$dbconn->Execute('UPDATE '.ECARDS_ORDERS_TABLE.' SET id_mail = ? WHERE id = ?', array($id_mail, $id_order));
	
	//---------------------------------
	// build and send external message
	//---------------------------------
	
	SendNotification($order['id_user_to'], $order['id_user'], 'ecard_received', $id_mail);
	
	// GA_TRACKING
	if ($has_session) {
		$_SESSION['ga_event_code'] = 'ecardsent';
	} else {
		ga_push_event($order['id_user_to'], 'ecardsent');
	}
	
	return;
}


function GetCardData($id_card, $id_user_to)
{
	global $config, $dbconn, $user, $lang;
	
	$id_user = (int) $user[ AUTH_ID_USER ];
	
	$strSQL =
		'SELECT a.id, a.id_category, a.id_subcategory, a.card_name, a.card_message, a.card_image, a.card_price,
				b.subcategory_name,
				c.category_name,
				d.content_name AS card_name_translated,
				e.content_name AS subcategory_name_translated,
				f.content_name AS category_name_translated
		   FROM '.ECARDS_ITEMS_TABLE.' a
	  LEFT JOIN '.ECARDS_SUBCATEGORIES_TABLE.' b ON b.id = a.id_subcategory
	  LEFT JOIN '.ECARDS_CATEGORIES_TABLE.' c ON c.id = a.id_category
	  LEFT JOIN '.ECARDS_LANG_CONTENT_TABLE.' d ON d.content_id = a.id AND d.content_type = "3" AND d.id_lang = "'.$config['default_lang'].'"
	  LEFT JOIN '.ECARDS_LANG_CONTENT_TABLE.' e ON e.content_id = a.id_subcategory AND e.content_type = "2" AND e.id_lang = "'.$config['default_lang'].'"
	  LEFT JOIN '.ECARDS_LANG_CONTENT_TABLE.' f ON d.content_id = a.id_category AND f.content_type = "1" AND f.id_lang = "'.$config['default_lang'].'"
		  WHERE a.status = "1" AND a.id = ?';
	
	$rs = $dbconn->Execute($strSQL, array($id_card));
	
	if ($rs->fields[0] > 0)
	{
		$row = $rs->GetRowAssoc(false);
		
		$card['id_card'] = $row['id'];
		$card['id_subcategory'] = $row['id_subcategory'];
		$card['id_category'] = $row['id_category'];
		$card['card_name'] = $row['card_name_translated'] ? $row['card_name_translated'] : $row['card_name'];
		$card['subcategory_name'] = $row['subcategory_name_translated'] ? $row['subcategory_name_translated'] : $row['subcategory_name'];
		$card['category_name'] = $row['category_name_translated'] ? $row['category_name_translated'] : $row['category_name'];
		$card['price_raw'] = $row['card_price'];
		$card['price'] = sprintf('%01.2f', $row['card_price']);
		
		// build default message
		$name_from = $dbconn->GetOne('SELECT fname FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
		if ($id_user_to) {
			$name_to = $dbconn->GetOne('SELECT fname FROM '.USERS_TABLE.' WHERE id = ?', array($id_user_to));
		} else {
			$name_to = '......';
		}
		$card['default_message'] = $lang['cards'][ $row['card_message'] ];
		$card['default_message'] = str_replace('#NICKNAME_FROM#', $name_from, $card['default_message']);
		$card['default_message'] = str_replace('#NICKNAME_TO#', $name_to, $card['default_message']);
		
		if ($row['card_image'] && file_exists($config['site_path'].'/uploades/ecards/'.stripslashes($row['card_image']))) {
			$card['image'] = $config['server'].$config['site_root'].'/uploades/ecards/'.stripslashes($row['card_image']);
		} else {
			$card['image'] = '';
		}
		
		if ($row['card_image'] && file_exists($config['site_path'].'/uploades/ecards/thumb_'.stripslashes($row['card_image']))) {
			$card['thumb_image'] = $config['server'].$config['site_root'].'/uploades/ecards/thumb_'.stripslashes($row['card_image']);
		} else {
			$card['thumb_image'] = '';
		}
		
		return $card;
	}
	else
	{
		return null;
	}	
}


function GetCardOrderData($id_order)
{
	global $config, $dbconn, $user;
	
	$id_user = (int) $user[ AUTH_ID_USER ];
	$id_order = (int) $id_order;
	
	$order = array();
	
	$strSQL =
		'SELECT o.id_item, o.id_song, o.id_user, o.id_user_to, o.card_header, o.message, o.status,
				s.song_name, s.song_file,
				ut.login, ut.fname, ut.gender, ut.date_birthday, ut.email, ut.site_language, ut.icon_path,
				uf.fname AS user_from_fname
		   FROM '.ECARDS_ORDERS_TABLE.' o
	  LEFT JOIN '.ECARDS_SONGS_TABLE.' s ON s.id = o.id_song
	  LEFT JOIN '.USERS_TABLE.' ut ON ut.id = o.id_user_to AND ut.status = "1" AND ut.root_user <> "1" AND ut.guest_user <> "1"
	  LEFT JOIN '.USERS_TABLE.' uf ON uf.id = o.id_user
		  WHERE o.id = ? AND o.id_user = ?';
	
	$rs = $dbconn->Execute($strSQL, array($id_order, $id_user));
	
	if ($rs->fields[0] > 0)
	{
		$row = $rs->GetRowAssoc(false);
		
		$order['id_order'] = $id_order;
		$order['id_card'] = $row['id_item'];
		$order['id_song'] = $row['id_song'];
		$order['id_user'] = $row['id_user'];
		$order['id_user_to'] = $row['id_user_to'];
		$order['card_header'] = stripslashes($row['card_header']);
		$order['message'] = stripslashes($row['message']);
		$order['status'] = $row['status'];
		$order['song_name'] = stripslashes($row['song_name']);
		
		// song
		if ($row['song_file'] && file_exists($config['site_path'].'/uploades/ecards/'.stripslashes($row['song_file']))) {
			$order['song_url'] = $config['server'].$config['site_root'].'/uploades/ecards/'.stripslashes($row['song_file']);
			$order['song_path'] = $config['site_path'].'/uploades/ecards/'.stripslashes($row['song_file']);
		} else {
			$order['song_url'] = $order['song_path'] = '';
		}
		
		// user to
		$order['user_to_login'] = stripslashes($row['login']);
		
		if ($order['user_to_login']) {
			$order['user_to_fname'] = stripslashes($row['fname']);
			$order['user_to_gender'] = $row['gender'];
			$order['user_to_age'] = AgeFromBDate($row['date_birthday']);
			$order['user_to_email'] = stripslashes($row['email']);
			$order['user_to_site_language'] = !empty($row['site_language']) ? $row['site_language'] : $config['default_lang'];
			
			$settings = GetSiteSettings(array('icon_male_default', 'icon_female_default', 'icons_folder'));
			
			$default_photos['1'] = $settings['icon_male_default'];
			$default_photos['2'] = $settings['icon_female_default'];
			
			$icon_path = $row['icon_path'] ? $row['icon_path'] : $default_photos[$order['gender']];
#			$icon_image= strlen($row['icon_path']) ? 1 : 0;
			
			if ($icon_path && file_exists($config['site_path'].$settings['icons_folder'].'/'.$icon_path)) {
				$order['user_to_icon_path'] = $config['site_root'].$settings['icons_folder'].'/'.$icon_path;
			}
		}
		else
		{
			$order['id_user_to'] = 0;
		}
		
		// user from
		$order['user_from_fname'] = stripslashes($row['user_from_fname']);
		
		// card data
		$card = GetCardData($order['id_card'], $order['id_user_to']);
		
		if (empty($card)) {
			return null;
		}
		
		$data = array_merge($order, $card);
		
		if (empty($_SESSION['permissions']['email_compose']) || empty($order['id_user_to']) || getConnectedStatus($id_user, $order['id_user_to']) != CS_CONNECTED) {
			$card['card_header'] = $card['card_name'];
			$card['message'] = $card['default_message'];
		}
		
		return $data;
	}
	else
	{
		return null;
	}
}


function CheckIsPlatinumSubmit()
{
	global $dbconn, $user;
	$id_user = $user[ AUTH_ID_USER ];
	$date_submit = $dbconn->GetOne('SELECT mm_platinum_submit FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
	return !empty($date_submit);
}


function CheckIsPlatinumApply()
{
	global $dbconn, $user;
	$id_user = $user[ AUTH_ID_USER ];
	$date_apply = $dbconn->GetOne('SELECT mm_platinum_applied FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
	return !empty($date_apply);
}


function CheckIsPlatinumPaid()
{
	global $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	// check for confirmed payment (online or offline)
	$date_paid = $dbconn->GetOne('SELECT mm_platinum_paid FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
	
	if (!empty($date_paid)) {
		return true;
	}
	
	$settings = GetSiteSettings(array('must_approve_payment_before_verify'));
	
	if ($settings['must_approve_payment_before_verify'] == '1') {
		return false;
	}
	
	// check for unconfirmed offline payment
	$id_group = ($user[ AUTH_GENDER ] == GENDER_MALE) ? MM_PLATINUM_GUY_ID : MM_PLATINUM_LADY_ID;
	
	$strSQL =
		'SELECT paysystem
		   FROM '.BILLING_REQUESTS_TABLE.'
		  WHERE id_user = ? AND id_group = ?
			AND status = "send" AND paysystem IN ("atm_payment", "wire_transfer", "bank_cheque")';
	
	$paysystem = $dbconn->getOne($strSQL, array($id_user, $id_group));
	
	return !empty($paysystem);
}


function MyStore_Online_Payment_User_Message($order_id, $paysystem, $currency, $amount)
{
	global $config, $dbconn;
	
	// collect data
	$rs = $dbconn->Execute(
		'SELECT o.id_user_from, o.id_user_to,
				u1.login AS login_from, u1.fname AS fname_from, u1.sname AS sname_from,
				u1.email AS email_from, u1.site_language AS site_language_from, u1.gender AS gender_from,
				u2.login AS login_to, u2.fname AS fname_to, u1.sname AS sname_to,
				u1.email AS email_to, u1.site_language AS site_language_to, u2.gender AS gender_to
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
	
	// content array
	$content				= array();
	$content['urls']		= GetUserEmailLinks();
	$content['login']		= stripslashes($row['login_from']);
	$content['fname']		= stripslashes($row['fname_from']);
	$content['sname']		= stripslashes($row['sname_from']);
	$content['paysystem']	= $paysystem;
	
	// gender specific langage items
	$suffix = ($row['gender_from'] == GENDER_MALE) ? '_e' : '_t';
	
	// subject
	$subject = $lang_mail['mystore_online_payment'.$suffix]['subject'];
	$subject = str_replace('[ORDER_ID]', $order_id, $subject);
	
	// message
	$amount = $currency.'&nbsp;'.number_format($amount, 2);
	
	$content['message'] = $lang_mail['mystore_online_payment'.$suffix]['message'];
	$content['message'] = str_replace('[ORDER_ID]', $order_id, $content['message']);
	$content['message'] = str_replace('[FNAME_TO]', $row['fname_to'], $content['message']);
	$content['message'] = str_replace('[AMOUNT]', $amount, $content['message']);
	$content['message'] = str_replace('[LOGIN_FROM]', $row['login_from'], $content['message']);
	
	$name_to = trim($row['fname_from'].' '.$row['sname_from']);
	
	// send message
	$mail_err = SendMail($site_lang, $row['email_from'], $config['site_email'], $subject, $content,
			'mail_noti_simple_generic_user', null, $name_to, '', 'mystore_online_payment', $row['gender_from']);
	
	//------------------
	// internal message
	//------------------
	
	// assemble body
	$body = $lang_mail['generic'.$suffix]['hello'].' '.$row['fname_from'].',<br><br>';
	$body.= $content['message'].'<br><br>';
	$body.= $lang_mail['generic'.$suffix]['admin_regards'];
	
	// store message
	$dbconn->Execute(
		'INSERT INTO '.MAILBOX_TABLE.' SET
			id_from = ?, id_to = ?, subject = ?, body = ?, was_read = "0", deleted_to = "0", deleted_from = "0", date_creation = NOW()',
		array(ID_ADMIN, $row['id_user_from'], $subject, $body));
	
	return $mail_err;
}


function MyStore_Online_Payment_Admin_Message($order_id, $paysystem, $currency, $amount)
{
	global $config, $dbconn;
	
	// collect data
	$rs = $dbconn->Execute(
		'SELECT o.id_user_from, o.id_user_to,
				u1.login AS login_from, u1.fname AS fname_from, u1.sname AS sname_from,
				u1.email AS email_from, u1.site_language AS site_language_from,
				u2.login AS login_to, u2.fname AS fname_to, u1.sname AS sname_to,
				u1.email AS email_to, u1.site_language AS site_language_to
		   FROM '.GIFTSHOP_ORDERS.' o
	 INNER JOIN '.USERS_TABLE.' u1 ON o.id_user_from = u1.id
	 INNER JOIN '.USERS_TABLE.' u2 ON o.id_user_to = u2.id
		  WHERE o.id = ?',
		  array($order_id));
	$row = $rs->GetRowAssoc(false);
	
	// language
	$site_lang = $config['default_lang'];
	
	// include mail language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	// content
	$content				= array();
	
	$content['order_id']	= $order_id;
	$content['amount']		= $currency.'&nbsp;'.number_format($amount, 2);
	$content['paysystem']	= $paysystem;
	
	$content['login']		= stripslashes($row['login_from']);
	$content['fname']		= stripslashes($row['fname_from']);
	$content['sname']		= stripslashes($row['sname_from']);
	$content['email']		= stripslashes($row['email_from']);
	
	$content['login_to']	= stripslashes($row['login_to']);
	$content['fname_to']	= stripslashes($row['fname_to']);
	$content['sname_to']	= stripslashes($row['sname_to']);
	$content['email_to']	= stripslashes($row['email_to']);
	
	// subject
	$subject = $lang_mail['mystore_online_payment_admin']['subject'];
	$subject = str_replace('[ORDER_ID]', $order_id, $subject);
	
	// message
	$content['message'] = $lang_mail['mystore_online_payment_admin']['message'];
	$content['message'] = str_replace('[ORDER_ID]', $order_id, $content['message']);
	
	// recipient
	if (REDIRECT_ADMIN_EMAIL && !IS_LIVE_SERVER) {
		$email_to = REDIRECT_ADMIN_EMAIL_TO;
	} else {
		$email_to = $config['site_email'];
	}
	
	// send message
	$mail_err = SendMail($site_lang, $email_to, $config['site_email'], $subject, $content,
					'mail_mystore_online_payment_admin', null, '', '', 'mystore_online_payment_admin');
	
	return $mail_err;
}

function getTimePeriodInDays($period_id)
{
	global $dbconn;
	
	$dbconn->SetFetchMode(ADODB_FETCH_ASSOC);
	$periodDetailsRs = $dbconn->Execute('SELECT period,amount FROM '.GROUP_PERIOD_TABLE.' WHERE id = ?', array($period_id));
	$periodDetails = $periodDetailsRs->fields;
	
	switch($periodDetails['period']){
		case 'week' :
			$days =  $periodDetails['amount'] * 7;
			break;
		case 'month' : 			
			$days = $periodDetails['amount'] * 30;
			break;			
		case 'day' :
			$days = $periodDetails['amount'];
			break;
		case 'year' :
			$days = $periodDetails['amount'] * 365;
			break;	
	 }
	 return $days; 
}

function getInstallmentCnt()
{
	global $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	//Added for installments By Narendra
	$rs = $dbconn->Execute('SELECT mm_platinum_paid, mm_first_installment_date, mm_second_installment_date FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
	$row = $rs->GetRowAssoc(false);
	
	if (empty($row['mm_platinum_paid'])) {
		if (!empty($row['mm_first_installment_date']) && !empty($row['mm_second_installment_date'])) {
			return 3;
		} elseif (!empty($row['mm_first_installment_date']) && empty($row['mm_second_installment_date'])) {
			return 2;
		} elseif (empty($row['mm_first_installment_date']) && empty($row['mm_second_installment_date'])) {
			return 1;
		}
	} else {
		return 'paid';
	}
}

?>