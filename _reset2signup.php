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

// get gender-specific signup group
$id_group_signup = ($user[ AUTH_GENDER ] == GENDER_MALE) ? MM_SIGNUP_GUY_ID : MM_SIGNUP_LADY_ID;

// run queries to reset the user
$dbconn->execute(
	'UPDATE '.USERS_TABLE.' SET
		status = "0",
		platinum_verified = "0",
		mm_application_submit = NULL,
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

echo "data in table ".USERS_TABLE." reset<br>";

// unconfirm on demand
if (!empty($_GET['unconfirm'])) {
	$dbconn->execute('UPDATE '.USERS_TABLE.' SET confirm = "0" WHERE id = '.$id_user);
	echo "unconfirmed<br>";
}

$dbconn->execute('UPDATE '.USER_GROUP_TABLE.' SET id_group = '.$id_group_signup.' WHERE id_user = '.$id_user);
echo "group reset<br>";

$dbconn->execute('DELETE FROM '.BILLING_USER_PERIOD_TABLE.' WHERE id_user = '.$id_user);
echo "periods deleted<br>";

$dbconn->execute('DELETE FROM '.BILLING_REQUESTS_TABLE.' WHERE id_user = '.$id_user);
echo "billing requests deleted<br>";

$dbconn->execute('DELETE FROM '.BILLING_ENTRY_TABLE.' WHERE id_user = '.$id_user);
echo "billing entries deleted<br>";

$dbconn->execute('DELETE FROM '.MAILBOX_TABLE.' WHERE id_from = '.$id_user.' OR id_to = '.$id_user);
echo "mailbox deleted<br>";

echo "<a href='myprofile.php'>Return to profile</a>";

exit;

?>