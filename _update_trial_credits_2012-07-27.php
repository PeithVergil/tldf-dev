<?php

/**
* Access denied page for users
*
* @package DatingPro
* @subpackage User Mode
**/

exit;

include './include/config.php';
include './common.php';
include './include/config_index.php';
include './include/functions_auth.php';
include './include/functions_index.php';

// loop through all Trial members and give them 20 credit points
$rs = $dbconn->Execute('SELECT id_user FROM user_group WHERE id_group IN (14,15) ORDER BY id_user');

while (!$rs->EOF) {
	$id_user = $rs->fields[0];
	$found = $dbconn->GetOne('SELECT 1 FROM billing_user_account WHERE id_user = ?', array($id_user));
	if ($found) {
		echo 'Adding 20 credit points to '.$id_user.' with UPDATE<br>';
		$dbconn->Execute('UPDATE billing_user_account SET account_curr = account_curr + 20, date_refresh = NOW() WHERE id_user = ?', array($id_user));
	} else {
		echo 'Giving 20 credit points to '.$id_user.' with INSERT<br>';
		$dbconn->Execute('INSERT INTO billing_user_account SET id_user = ?, account_curr = 20, date_refresh = NOW()', array($id_user));
	}
	echo 'Creating a billing_entry record<hr>';
	$dbconn->Execute(
		'INSERT INTO billing_entry SET
			id_user = ?, amount = 20, currency = "USD", id_group = -9, id_product = 0,
			entry_type = "admin", txn_type = "site_credits", date_entry = NOW()',
		array($id_user));
	$rs->MoveNext();
}

exit;

?>