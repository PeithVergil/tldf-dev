<?php

require_once('init.php');

$GLOBALS['fc_config']['cms']->LoginCheck(__FILE__);
AdminMainMenu($lang["chats"]);

if(!isset($_REQUEST['sort']) || isset($_REQUEST['clear'])) $_REQUEST['sort'] = 'none';

$req = array_merge($_GET, $_POST);

if(isset($_REQUEST['unignoreid'])) {
	$stmt = new Statement("DELETE FROM {$GLOBALS['fc_config']['db']['pref']}ignors WHERE ignoreduserid=?");
	$stmt->process($_REQUEST['unignoreid']);
	$notice = 'ignore removed';
	$smarty->assign('notice',$notice);
}

$stmt = new Statement("SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}ignors ORDER BY userid");
$rs = $stmt->process();

$ignores = array();

while($rec = $rs->next()) {
	$ignores_temp = array();

	$user  = ChatServer::getUser($rec['userid']);
	$iuser = ChatServer::getUser($rec['ignoreduserid']);

	$ignores_temp['user'] = $user['login'];
	$ignores_temp['userid'] = $rec['userid'];
	$ignores_temp['iuser'] = $iuser['login'];
	$ignores_temp['iuserid'] = $rec['ignoreduserid'];
	$ignores_temp['created'] = $rec['created'];

	array_push($ignores, $ignores_temp);
}

if ($_REQUEST['sort'] != 'none') {
	sort_table($_REQUEST['sort'], $ignores);
}

//Assign Smarty variables and load the admin template


$smarty->assign('ignores',$ignores);
$smarty->display('ignorelist.tpl');

?>