<?php
/**
* Copy Groups
*
* @package TLDF
* @author Ralf Strehle (ralf.strehle@yahoo.de)
**/

include '../include/config.php';
include '../common.php';
include '../include/config_admin.php';
include '../include/functions_auth.php';
include '../include/functions_admin.php';
include '../include/config_index.php';

$auth = auth_index_user();
login_check($auth);

if (!isset($_GET['from']) || (int)$_GET['from'] < 3 || !isset($_GET['to']) || (int)$_GET['from'] < 3 || !isset($_GET['gender'])) {
	echo 'use ?from=ID_FROM&to=ID_TO&gender=GENDER to copy a group<br><br>';
	echo 'ID_FROM and ID_TO must be group ids >= 3<br>GENDER must be 1=male or 2=female<br><br>';
	list_groups();
	exit;
}

define('FROM_GROUP', (int)$_GET['from']);
define('TO_GROUP', (int)$_GET['to']);
define('GENDER', (int)$_GET['gender']);

echo 'copy group id '.FROM_GROUP.' to '.TO_GROUP.'<br><br>';

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

// GROUPS_TABLE
//
$sql =
	'INSERT INTO '.GROUPS_TABLE.'
		SELECT "'.TO_GROUP.'" AS id, name, type, addable, is_gender_group, "'.GENDER.'" AS gender
		  FROM '.GROUPS_TABLE.'
		 WHERE id='.FROM_GROUP;
$dbconn->execute($sql);
$count = $dbconn->getOne('SELECT ROW_COUNT()');
echo "group record inserted in table ".GROUPS_TABLE." (".$count." records)<br>";

// GROUP_PERIOD_TABLE
//
$sql =
	'INSERT INTO '.GROUP_PERIOD_TABLE.'
		SELECT NULL AS id, "'.TO_GROUP.'" AS id_group, amount, period, cost, cost_2, recurring, upgrade, trial_amount, trial_period, trial_cost, trial_cost_2, status
		  FROM '.GROUP_PERIOD_TABLE.'
		 WHERE id_group='.FROM_GROUP;
$dbconn->execute($sql);
$count = $dbconn->getOne('SELECT ROW_COUNT()');
echo "period records inserted in table ".GROUP_PERIOD_TABLE." (".$count." records)<br>";

// GROUPS_PERMISSIONS_TABLE
//
$sql =
	'INSERT INTO '.GROUPS_PERMISSIONS_TABLE.'
		SELECT NULL AS id, "'.TO_GROUP.'" AS id_group, id_permission, permission_count
		  FROM '.GROUPS_PERMISSIONS_TABLE.'
		 WHERE id_group='.FROM_GROUP;
$dbconn->execute($sql);
$count = $dbconn->getOne('SELECT ROW_COUNT()');
echo "permission records inserted in table ".GROUPS_PERMISSIONS_TABLE." (".$count." records)<br>";

// GROUP_MODULE_TABLE
//
$sql =
	'INSERT INTO '.GROUP_MODULE_TABLE.'
		SELECT NULL AS id, "'.TO_GROUP.'" AS id_group, id_module
		  FROM '.GROUP_MODULE_TABLE.'
		 WHERE id_group='.FROM_GROUP;
$dbconn->execute($sql);
$count = $dbconn->getOne('SELECT ROW_COUNT()');
echo "module reference records inserted in table ".GROUP_MODULE_TABLE." (".$count." records)<br>";

/*
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
*/

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