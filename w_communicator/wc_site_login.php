<?php

if (!function_exists('auth_index_user')) {
	include '../include/functions_auth.php';
}

function SiteLogin($user_name, $password)
{
	@session_start();
	$_POST['login_lg'] = $user_name;
	$_POST['pass_lg'] = $password;  
	ini_set('display_errors', '1');
	$user = auth_index_user();
}

?>