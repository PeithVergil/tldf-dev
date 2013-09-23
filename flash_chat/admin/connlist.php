<?php

require_once('init.php');

$GLOBALS['fc_config']['cms']->LoginCheck(__FILE__);
AdminMainMenu($lang["chats"]);

if(!isset($_REQUEST['sort']) || isset($_REQUEST['clear'])) $_REQUEST['sort'] = 'none';

$stmt = new Statement("SELECT * FROM {$GLOBALS['fc_config']['db']['pref']}connections");
$rs = $stmt->process();

$connections = array();
if($rs->hasNext()) {
	while($rec = $rs->next()) {
		$temp_connection = array();
		$temp_connection['updated'] = $rec['updated'];//substr($rec['updated'], 8, 2) . ':' . substr($rec['updated'], 10, 2);
		$temp_connection['created'] = $rec['created'];//substr($rec['created'], 8, 2) . ':' . substr($rec['created'], 10, 2);

		$temp_connection['id'] = $rec['id'];
		$temp_connection['userid'] = $rec['userid'];
		if(isset($rec['userid'])){
			$user = ChatServer::getUser($rec['userid']);
			$temp_connection['login'] = $user['login'];
		}
		$temp_connection['roomid'] = $rec['roomid'];
		$temp_connection['state'] = $rec['state'];
		$temp_connection['color'] = $rec['color'];
		$temp_connection['start'] = $rec['start'];
		$temp_connection['lang'] = $rec['lang'];
		$temp_connection['ip'] = $rec['ip'];
		$temp_connection['tzoffset'] = $rec['tzoffset'];
		$temp_connection['host'] = @gethostbyaddr($rec['ip']);

		array_push($connections, $temp_connection);
	}
}

if ($_REQUEST['sort'] != 'none') {
	sort_table($_REQUEST['sort'], $connections);
}

//Assign Smarty variables and load the admin template
$smarty->assign('connections',$connections);
$smarty->display('connlist.tpl');

?>