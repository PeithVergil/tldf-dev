<?php

function WebmessengerWindow()
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;
	
	$to_user = $_GET['to_user'];
	$from_user = $user[ AUTH_ID_USER ];
	
	echo "<script type='text/javascript' src='".$config["site_root"]."/webmessenger/functions.php'></script>";
	echo "<script type='text/javascript'>up_launchIM('".$from_user."','".$to_user."');</script>";
	exit;
}

?>