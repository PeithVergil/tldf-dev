<?php
require_once('init.php');

$GLOBALS['fc_config']['cms']->LoginCheck(__FILE__);
AdminMainMenu($lang["chats"]);

function str2date($str) {
	// MM/DD/YY
	$parts = split('/', $str);

	return "{$parts[2]}-{$parts[0]}-{$parts[1]}";
}
function str2timestamp($str)
{
	//YYYY-MM-DD hh:mm:ss
	return $parts = str_replace(array(' ',':','-'),'',$str);
}

$urs = $GLOBALS['fc_config']['cms']->getUsers();

$dispusers = array();
if ( is_array( $urs ) ) {
	while( $rec = next( $urs ) ) {
		$dispusers[$rec['id']] = $rec['login'];
	}
}
else {
	while($rec = $urs->next()) {
		$dispusers[$rec['id']] = $rec['login'];
	}
}
asort($dispusers);

// end fix

$stmt = new Statement("SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}rooms order by ispermanent");
$rrs = $stmt->process();

if(!isset($_REQUEST['roomid']) || isset($_REQUEST['clear'])) $_REQUEST['roomid'] = 0;
if(!isset($_REQUEST['userid']) || isset($_REQUEST['clear'])) $_REQUEST['userid'] = 0;
if(!isset($_REQUEST['from']) || isset($_REQUEST['clear'])) $_REQUEST['from'] = '';
if(!isset($_REQUEST['to']) || isset($_REQUEST['clear'])) $_REQUEST['to'] = '';
if(!isset($_REQUEST['days']) || isset($_REQUEST['clear'])) $_REQUEST['days'] = '';
if(!isset($_REQUEST['keyword']) || isset($_REQUEST['clear'])) $_REQUEST['keyword'] = '';
if(!isset($_REQUEST['sort']) || isset($_REQUEST['clear'])) $_REQUEST['sort'] = 'none';

$where = array('1=1');
if($_REQUEST['roomid']) $where[] = "(msgs.roomid='{$_REQUEST['roomid']}' OR msgs.toroomid='{$_REQUEST['roomid']}')";
if($_REQUEST['userid']) $where[] = "(msgs.userid='{$_REQUEST['userid']}' OR msgs.touserid='{$_REQUEST['userid']}')";
if($_REQUEST['days']) $where[] = "msgs.created >= DATE_SUB(NOW(),INTERVAL {$_REQUEST['days']} DAY)";
if($_REQUEST['from']) $where[] = "msgs.created >= '" . str2timestamp($_REQUEST['from']) . "'";
if($_REQUEST['to']) $where[] = "msgs.created <= '" . str2timestamp($_REQUEST['to']) . "'";
if($_REQUEST['keyword']) $where[] = "msgs.txt LIKE '%{$_REQUEST['keyword']}%'";

$qry = "SELECT msgs.*, DATE_FORMAT(DATE_ADD(msgs.created, INTERVAL {$GLOBALS['fc_config']['timeOffset']}/60 HOUR), '%b %e, %Y %T') AS sent, torooms.name AS toroom, fromrooms.name AS fromroom FROM ".
	   "{$GLOBALS['fc_config']['db']['pref']}messages AS msgs LEFT JOIN {$GLOBALS['fc_config']['db']['pref']}rooms AS fromrooms ON msgs.roomid=fromrooms.id ".
	   "LEFT JOIN {$GLOBALS['fc_config']['db']['pref']}rooms AS torooms ON msgs.toroomid=torooms.id ".
	   "WHERE command='msg' AND (msgs.touserid IS NOT NULL OR msgs.toroomid IS NOT NULL) AND ". join(' AND ', $where) .
	   " ORDER BY msgs.id";

$stmt = new Statement($qry);
$rs = $stmt->process();

$users = array();

function getUser($userid) {
	global $users, $manageUsers;

	if(!isset($users[$userid])) {
		$user = ChatServer::getUser($userid);
		if($manageUsers) {
			$users[$userid] = "<a href=\"user.php?id={$user['id']}\">{$user['login']}</a>";
		} else {
			$users[$userid] = $user['login'];
		}
	}

	return $users[$userid];
}

//Set variables for display in the query
$rooms = array();
while($rec = $rrs->next()) {
	if ($GLOBALS['fc_config']['cp_map']) {
		$utfConverter = new utf8($GLOBALS['fc_config']['cp_map']);
		$rec['name'] = $utfConverter->utf8ToStr($rec['name']);
	}
	$rooms[$rec['id']] = $rec['name'];
}

$messages = array();

while($rec = $rs->next()) {
	$temp_message 			= array();
	$temp_message['id'] 		= $rec['id'];
	$temp_message['user'] 		= getUser($rec['userid']);
	$temp_message['touser'] 	= getUser($rec['touserid']);
	$temp_message['sent'] 		= $rec['sent'];
	$temp_message['toroomid'] 	= $rec['toroomid'];
	if ($GLOBALS['fc_config']['cp_map']) {
		$utfConverter = new utf8($GLOBALS['fc_config']['cp_map']);
		$rec['toroom'] = $utfConverter->utf8ToStr($rec['toroom']);
	}
	$temp_message['toroom'] = $rec['toroom'];
	if ($GLOBALS['fc_config']['cp_map']) {
		$utfConverter = new utf8($GLOBALS['fc_config']['cp_map']);
		$rec['txt'] = $utfConverter->utf8ToStr($rec['txt']);
	}
	$temp_message['txt'] = $rec['txt'];
	array_push($messages, $temp_message);
}

// sort messages by col
if ($_REQUEST['sort'] != 'none') {
	sort_table($_REQUEST['sort'], $messages);
}

//Assign Smarty variables and load the admin template
$smarty->assign('users',$dispusers);
$smarty->assign('rooms',$rooms);
$smarty->assign('messages',$messages);
$smarty->display('msglist.tpl');
?>