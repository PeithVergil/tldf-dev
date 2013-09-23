<?php

/**
* Reorganize Group Ids
*
* @package TLDF
* @author Ralf Strehle (ralf.strehle@yahoo.de)
**/

exit;

include '../include/config.php';
include '../common.php';
include '../include/config_admin.php';
include '../include/functions_auth.php';
include '../include/functions_admin.php';
include '../include/config_index.php';

$auth = auth_index_user();
login_check($auth);

if (empty($_GET['from']) || (int)$_GET['from'] < 3 || empty($_GET['to']) || (int)$_GET['from'] < 3) {
	echo 'use ?from=ID_FROM&to=ID_TO to move a group<br><br>';
	echo 'ID_FROM and ID_TO must be group ids >= 3<br><br>';
	list_groups();
	exit;
}

define('FROM_GROUP', (int)$_GET['from']);
define('TO_GROUP', (int)$_GET['to']);

echo 'move group id '.FROM_GROUP.' to '.TO_GROUP.'<br><br>';

$check_from = $dbconn->getOne('SELECT 1 FROM '.GROUPS_TABLE.' WHERE id = '.FROM_GROUP);
if (empty($check_from)) {
	echo 'group id from='.FROM_GROUP.' not found in table '.GROUPS_TABLE;
	exit;
}

$check_to = $dbconn->getOne('SELECT 1 FROM '.GROUPS_TABLE.' WHERE id = '.TO_GROUP);
if (!empty($check_to)) {
	echo 'group id to='.TO_GROUP.' does already exist in table '.GROUPS_TABLE;
	exit;
}

$dbconn->execute('UPDATE '.GROUPS_TABLE.' SET id = '.TO_GROUP.' WHERE id = '.FROM_GROUP);
$count = $dbconn->getOne('SELECT ROW_COUNT()');
echo "id updated in table ".GROUPS_TABLE." (".$count." records)<br>";

$dbconn->execute('UPDATE '.GROUP_PERIOD_TABLE.' SET id_group = '.TO_GROUP.' WHERE id_group = '.FROM_GROUP);
$count = $dbconn->getOne('SELECT ROW_COUNT()');
echo "id_group updated in table ".GROUP_PERIOD_TABLE." (".$count." records)<br>";

$dbconn->execute('UPDATE '.GROUPS_PERMISSIONS_TABLE.' SET id_group = '.TO_GROUP.' WHERE id_group = '.FROM_GROUP);
$count = $dbconn->getOne('SELECT ROW_COUNT()');
echo "id_group updated in table ".GROUPS_PERMISSIONS_TABLE." (".$count." records)<br>";

$dbconn->execute('UPDATE '.GROUP_MODULE_TABLE.' SET id_group = '.TO_GROUP.' WHERE id_group = '.FROM_GROUP);
$count = $dbconn->getOne('SELECT ROW_COUNT()');
echo "id_group updated in table ".GROUP_MODULE_TABLE." (".$count." records)<br>";

$dbconn->execute('UPDATE '.USER_GROUP_TABLE.' SET id_group = '.TO_GROUP.' WHERE id_group = '.FROM_GROUP);
$count = $dbconn->getOne('SELECT ROW_COUNT()');
echo "id_group updated in table ".USER_GROUP_TABLE." (".$count." records)<br>";

$dbconn->execute('UPDATE '.BILLING_REQUESTS_TABLE.' SET id_group = '.TO_GROUP.' WHERE id_group = '.FROM_GROUP);
$count = $dbconn->getOne('SELECT ROW_COUNT()');
echo "id_group updated in table ".BILLING_REQUESTS_TABLE." (".$count." records)<br>";

$dbconn->execute('UPDATE '.BILLING_ENTRY_TABLE.' SET id_group = '.TO_GROUP.' WHERE id_group = '.FROM_GROUP);
$count = $dbconn->getOne('SELECT ROW_COUNT()');
echo "id_group updated in table ".BILLING_ENTRY_TABLE." (".$count." records)<br>";

$hasTable = $dbconn->execute('SHOW TABLES LIKE "'.NEWSLETTER_LIST.'"');
if (!empty($hasTable)) {
	$dbconn->execute('UPDATE '.NEWSLETTER_LIST.' SET site_group = '.TO_GROUP.' WHERE site_group = '.FROM_GROUP);
	$count = $dbconn->getOne('SELECT ROW_COUNT()');
	echo "site_group updated in table ".NEWSLETTER_LIST." (".$count." records)<br>";
}

$hasTable = $dbconn->execute('SHOW TABLES LIKE "'.UP_MESSENGER_GROUPS_AVDISABLED_TABLE.'"');
if (!empty($hasTable)) {
	$dbconn->execute('UPDATE '.UP_MESSENGER_GROUPS_AVDISABLED_TABLE.' SET id_group = '.TO_GROUP.' WHERE id_group = '.FROM_GROUP);
	$count = $dbconn->getOne('SELECT ROW_COUNT()');
	echo "id_group updated in table ".UP_MESSENGER_GROUPS_AVDISABLED_TABLE." (".$count." records)<br>";
}

echo '<br>Program finished with success.<br><br>';
list_groups();
echo "<br><a href='index.php'>Return to admin panel</a>";

exit;

function list_groups()
{
	global $dbconn;
	
	echo 'available groups:<br><br>';
	$rs = $dbconn->execute('SELECT id, name FROM '.GROUPS_TABLE.' ORDER BY id');
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		echo $row['id'].'='.$row['name'].'<br>';
		$rs->MoveNext();
	}
}

?>