<?php
include_once('init.php');

$GLOBALS['fc_config']['cms']->LoginCheck(__FILE__);
AdminMainMenu($lang["chats"]);

$sel = isset($_REQUEST["sel"]) ? $_REQUEST["sel"] : "";
if($sel == 'install_form'){
	//get bases
	$learnfiles = array();
	$rootdir = '../bot/programe/aiml';
	$dir=opendir ($rootdir);
	while ($file = readdir($dir)){
		if (substr($file,strpos($file,"."))==".aiml"){
			$learnfiles[] = basename($file, '.aiml');
		}
	}

	closedir($dir);
	$smarty->assign('learnfiles', $learnfiles);
	$smarty->assign('form_type', '2');
}elseif($sel == 'install'){
	$errmsg = processUpdate();
	if($errmsg){
		$smarty->assign('errmsg', $errmsg);
		$smarty->assign('form_type', '2');
	}else{
		$GLOBALS['fc_config']['cms']->UpdateSettings("enable_bots", "1");
		flush();
		$smarty->assign('form_type', '3');
	}
}else{
	if(!isset($_REQUEST['sort']) || isset($_REQUEST['clear'])) $_REQUEST['sort'] = 'none';

	$bots = array();
	$botnames = array();

	if($GLOBALS['fc_config']['enableBots']){
		if(isset($_GET['id']))	{
			$user = ChatServer::getUser($_GET['id']);
			$userId = $GLOBALS['fc_config']['bot']->logout($user['login']);
			$GLOBALS['fc_config']['bot']->disconnectUser2Bot($userId);
		}

		$bots  = $GLOBALS['fc_config']['bot']->getBots();

		while (list($key, $val) = each($bots))	{
			$botnames[$val['login']]['id']    = $key;
			$botnames[$val['login']]['login'] = $val['login'];
		}
	}

	if ($_REQUEST['sort'] != 'none') {
		ksort( $botnames );
	}
	$smarty->assign('form_type', '1');
}

//Assign Smarty variables and load the botlist template
$smarty->assign('enableBots', $GLOBALS['fc_config']['enableBots']);
$smarty->assign('botnames', $botnames);
$smarty->display('botlist.tpl');


function processUpdate(){
	$ret = array();
	foreach( $_POST as $k => $v )	{
		if( substr($k,0,4 ) != "fld_" ) continue;
		$fld = substr($k,4);
		$ret[] = $fld;
	}
	$ret[] = 'std-65percent';
	$ret[] = 'std-pickup';
	$ret = array_unique($ret);

	if( sizeof($ret) == 0 ){
		echo '<script language="JavaScript" type="text/javascript">location.href = "botlist.php";	</script>';
		exit;
	}

	session_name('flashchat_bot');
	session_start();
	$_SESSION['files'] = $ret;
	$_SESSION['url']   = $_SERVER['SCRIPT_NAME'];

	include_once '../inc/config.srv.php';

	//Create DB tables
	$tables = array(
			"drop" => "DROP TABLE IF EXISTS {$GLOBALS['fc_config']['db']['pref']}bot , {$GLOBALS['fc_config']['db']['pref']}bots , {$GLOBALS['fc_config']['db']['pref']}conversationlog , {$GLOBALS['fc_config']['db']['pref']}dstore , {$GLOBALS['fc_config']['db']['pref']}gmcache , {$GLOBALS['fc_config']['db']['pref']}gossip , {$GLOBALS['fc_config']['db']['pref']}patterns , {$GLOBALS['fc_config']['db']['pref']}templates , {$GLOBALS['fc_config']['db']['pref']}thatindex , {$GLOBALS['fc_config']['db']['pref']}thatstack ;",
			"bot"  => "CREATE TABLE {$GLOBALS['fc_config']['db']['pref']}bot (  id int(11) NOT NULL auto_increment,  bot tinyint(4) NOT NULL default '0',  name varchar(255) NOT NULL default '',  value text NOT NULL,  PRIMARY KEY  (id),  KEY botname (bot,name)) TYPE=MyISAM;",
			"bots" => "CREATE TABLE {$GLOBALS['fc_config']['db']['pref']}bots ( id tinyint(3) unsigned NOT NULL auto_increment,  botname varchar(255) NOT NULL default '',  PRIMARY KEY  (botname),  KEY id (id)) TYPE=MyISAM;",
			"conversationlog" => "CREATE TABLE {$GLOBALS['fc_config']['db']['pref']}conversationlog (  bot tinyint(3) unsigned NOT NULL default '0',  id int(11) NOT NULL auto_increment,  input text,  response text,  uid varchar(255) default NULL,  enteredtime timestamp(14) NOT NULL,  PRIMARY KEY  (id),  KEY botid (bot)) TYPE=MyISAM;",
			"dstore"    => "CREATE TABLE {$GLOBALS['fc_config']['db']['pref']}dstore (  uid varchar(255) default NULL,  name text,  value text,  enteredtime timestamp(14) NOT NULL,  id int(11) NOT NULL auto_increment,  PRIMARY KEY  (id),  KEY nameidx (name(40))) TYPE=MyISAM;",
			"gmcache"   => "CREATE TABLE {$GLOBALS['fc_config']['db']['pref']}gmcache (  id int(11) NOT NULL auto_increment,  bot tinyint(3) unsigned NOT NULL default '0',  template int(11) NOT NULL default '0',  inputstarvals text,  thatstarvals text,  topicstarvals text,  patternmatched text,  inputmatched text,  combined text NOT NULL,  PRIMARY KEY  (id),  KEY combined (bot,combined(255))) TYPE=MyISAM;",
			"gossip"    => "CREATE TABLE {$GLOBALS['fc_config']['db']['pref']}gossip (  bot tinyint(3) unsigned NOT NULL default '0',  gossip text,  id int(11) NOT NULL auto_increment,  PRIMARY KEY  (id),  KEY botidx (bot)) TYPE=MyISAM;",
			"patterns"  => "CREATE TABLE {$GLOBALS['fc_config']['db']['pref']}patterns (  bot tinyint(3) unsigned NOT NULL default '0',  id int(11) NOT NULL auto_increment,  word varchar(255) default NULL,  ordera tinyint(4) NOT NULL default '0',  parent int(11) NOT NULL default '0',  isend tinyint(4) NOT NULL default '0',  PRIMARY KEY  (id),  KEY wordparent (parent,word),  KEY botid (bot)) TYPE=MyISAM;",
			"templates" => "CREATE TABLE {$GLOBALS['fc_config']['db']['pref']}templates (  bot tinyint(3) unsigned NOT NULL default '0',  id int(11) NOT NULL default '0',  template text NOT NULL,  pattern varchar(255) default NULL,  that varchar(255) default NULL,  topic varchar(255) default NULL,  PRIMARY KEY  (id),  KEY bot (id)) TYPE=MyISAM;",
			"thatindex" => "CREATE TABLE {$GLOBALS['fc_config']['db']['pref']}thatindex (  uid varchar(255) default NULL,  enteredtime timestamp(14) NOT NULL,  id int(11) NOT NULL auto_increment,  PRIMARY KEY  (id)) TYPE=MyISAM;",
			"thatstack" => "CREATE TABLE {$GLOBALS['fc_config']['db']['pref']}thatstack (  thatid int(11) NOT NULL default '0',  id int(11) NOT NULL auto_increment,  value varchar(255) default NULL,  enteredtime timestamp(14) NOT NULL,  PRIMARY KEY  (id)) TYPE=MyISAM;",
			//users to do
		);

	$dbname = $GLOBALS['fc_config']['db']['base'];
	$dbuser = $GLOBALS['fc_config']['db']['user'];
	$dbpass = $GLOBALS['fc_config']['db']['pass'];
	$dbhost = $GLOBALS['fc_config']['db']['host'];
	$dbpref = $GLOBALS['fc_config']['db']['pref'];

	$errmsg = '';

	$errmsg = connectToDB($dbname, $dbuser, $dbpass, $dbhost, $dbpref);
	if($errMsg != '') return $errmsg;

	foreach($tables as $k=>$str){
		if(@mysql_query($str) === false){
			return "<b>Could not create DB table '{$dbpref}$k' </b><br>" . mysql_error();
		}
	}
	return '';
}
function connectToDB($dbname='', $dbuser='', $dbpass='', $dbhost='', &$dbpref){
	if( $dbname == '' ){
		include_once '../inc/config.srv.php';
		$dbhost = $GLOBALS['fc_config']['db']['host'];
		$dbuser = $GLOBALS['fc_config']['db']['user'];
		$dbpass = $GLOBALS['fc_config']['db']['pass'];
		$dbname = $GLOBALS['fc_config']['db']['base'];
		$dbpref = $GLOBALS['fc_config']['db']['pref'];
	}

	if($conn = @mysql_pconnect($dbhost, $dbuser, $dbpass)){
		if(! mysql_select_db($dbname, $conn)){
			return "<b>Could not select '$dbname' database - please make sure this database exists</b><br>" . mysql_error();
		}
	}else{
		return '<b>Could not connect to MySQL database - please check database settings</b><br>' . mysql_error();
	}
	return '';

}

?>