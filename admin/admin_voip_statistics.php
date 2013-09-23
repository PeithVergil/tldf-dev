<?php

include "../include/config.php";
include_once "../common.php";
include "../include/config_admin.php";
include "../include/functions_auth.php";
include "../include/functions_admin.php";
include "../include/templates.php";
include "../include/class.voip.php";

$auth = auth_user();
login_check($auth);
IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "settings");

$VoIp = new DatingVoIp($dbconn,$config,$auth[0]);

$sel = isset($_REQUEST['sel']) ? $_REQUEST['sel'] : "";
switch ($sel){
	case 'user_stat': UserStat();break;
	default:listStatistic();
}

function listStatistic(){
	global $config, $smarty, $VoIp, $lang;
	
	if(isset($_SERVER["PHP_SELF"]))
		$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
		$file_name = "admin_voip_statistics.php";
		
	AdminMainMenu($lang["voip"]);
	
	$ids = $VoIp->GetAllVoipUsers(true);
	$i=0;
	foreach ($ids as $id_user){
		$stat_arr[$i] = $VoIp->GetCommonStatisticByUser($id_user);
		$i++;
	}
	$smarty->assign('stat',$stat_arr);
	$smarty->assign('file_name',$file_name);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_voip_stat.tpl");
	exit;
}

function UserStat(){
	global $config, $smarty, $VoIp, $lang;

	$smarty->assign('user_stat',$VoIp->GetDetailedStatisticForUser($_POST["id_user"]));
	$smarty->assign('iter', intval($_POST["iter"]));
	$smarty->assign('id_user', intval($_POST["id_user"]));
	echo $smarty->fetch(TrimSlash($config["admin_theme_path"])."/admin_voip_user_stat_table.tpl");
	exit;
}
?>