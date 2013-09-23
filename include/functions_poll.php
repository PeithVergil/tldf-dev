<?php
/**
*
*	dating version OCT_2008_01
*
*	poll functions
*
**/

if(!function_exists("GetSiteSettings"))
{
	include_once dirname(__FILE__)."/functions_index.php";
}

function UseModulePoll()
{
	global $dbconn, $smarty, $lang, $config;
	
	$base_use = GetSiteSettings('use_pilot_module_poll');
	
	if ($base_use) {
		return true;
	} else {
		return false;
	}
}

function PollBar()
{
	// RS: poll is a performance hog on homepage.php and has been disabled via use_pilot_module_poll in settings table
	
	// return array for smarty
	global $lang, $config, $config_index, $smarty, $dbconn, $user;
	
	if (!UseModulePoll()) {
		$smarty->assign("show_poll", "0");
		return "";
	}
	
	$smarty->assign("show_poll", "1");
	$poll_path = $config["site_path"]."/poll";
	
	require $poll_path."/include/config.inc.php";
	require $poll_path."/include/$POLLDB[class]";
	require $poll_path."/include/class_poll.php";
	
	global $POLL_CLASS;
	
	$POLL_CLASS["db"] = new polldb_sql;
	$POLL_CLASS["db"]->connect();
	
	$php_poll = new poll();
	
	$polltext ='';
	
	if ((isset($_REQUEST["poll_view_id"]))&&($_REQUEST["poll_view_id"])) {
		$random_id = $_REQUEST["poll_view_id"];
	} else {
		$random_id = $php_poll->get_random_poll_id($user[ AUTH_GENDER ]);
	}
	
	if ($random_id > 0) {
		$polltext.= $php_poll->poll_process($random_id);
	}
	
	return $polltext;
}
?>