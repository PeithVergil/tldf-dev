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

$user = auth_index_user();

RefreshAccount();

$id_user = $user[ AUTH_ID_USER ];

// check for guest and admin
if ($user[ AUTH_GUEST ] || $user[ AUTH_ROOT ]) {
	header('location: index.php');
	exit;
}

// get gender-specific regular group and period id
$id_group_regular = ($user[ AUTH_GENDER ] == GENDER_MALE) ? MM_REGULAR_GUY_ID : MM_REGULAR_LADY_ID;

$id_period_regular = $dbconn->GetOne(
	'SELECT id FROM '.GROUP_PERIOD_TABLE.' WHERE id_group='.$id_group_regular.' AND amount=1 AND period="month" AND recurring="0"');

// get days for date_begin
$days = isset($_GET['days']) && (int) $_GET['days'] > 0 ? (int) $_GET['days'] : 20;

// run queries to reset the user
$dbconn->execute(
	'UPDATE '.USERS_TABLE.' SET
		platinum_verified = "0",
		mm_platinum_submit = NULL,
		mm_platinum_paid = NULL,
		mm_platinum_applied = NULL,
		chk_background = "NA",
		chk_marital_status = "NA",
		chk_work_history = "NA",
		chk_interview_photo = "NA",
		chk_date = NULL,
		chk_staff = NULL,
		chk_comment = NULL
	 WHERE id = '.$id_user);

$dbconn->execute('UPDATE '.USER_GROUP_TABLE.' SET id_group = '.$id_group_regular.' WHERE id_user = '.$id_user);

$dbconn->execute('UPDATE '.BILLING_USER_PERIOD_TABLE.' SET id_group_period = '.$id_period_regular.', date_begin=DATE_ADD(NOW(), INTERVAL '.-$days.' DAY), date_end = DATE_ADD(NOW(), INTERVAL '.(30-$days).' DAY) WHERE id_user = '.$id_user);

$dbconn->execute('DELETE FROM '.BILLING_REQUESTS_TABLE.' WHERE id_user = '.$id_user);

$dbconn->execute('DELETE FROM '.BILLING_ENTRY_TABLE.' WHERE id_user = '.$id_user);

$dbconn->execute('DELETE FROM '.MAILBOX_TABLE.' WHERE id_from = '.$id_user.' OR id_to = '.$id_user);

header('location: account.php');
exit;

?>