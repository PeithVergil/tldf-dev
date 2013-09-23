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
	case 'save': saveSettings();break;
	default:listSettings();
}

function listSettings($err=''){
	global $lang, $smarty, $dbconn, $config, $VoIp;
	
	if(isset($_SERVER["PHP_SELF"]))
		$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
		$file_name = "admin_voip_settings.php";

	AdminMainMenu($lang["voip"]);
	
	$form["error"] = $err;
	$voip_settings = $VoIp->GetAdminSettings();
	$balance = $VoIp->GetMemberBalanceForUser(true,2);
	
	if ($VoIp->IsCurrenciesSame($balance["jajah_currency_name"])){
		if ($voip_settings["voip_currency_rate"] != 1)
			$VoIp->SetAdminSettings($voip_settings["voip_admin"], $voip_settings["voip_admin_pass"], $voip_settings["voip_accountid"], $voip_settings["voip_admin_percent"],1);
		$voip_settings["voip_currency_rate"] = 1;
	}else{
		$voip_settings["edit_rate"] = 1;
	}
	$smarty->assign('settings',$voip_settings);
	$smarty->assign("balance",$balance);
	
	if (!$form["error"])
		$form["error"] = $VoIp->GetErrorMsg();
		
	$form["hiddens"] = "<input type='hidden' name='sel' value='save' />";
	$form["hiddens"] .= "<input type='hidden' name='jajah_currency_name' value='".$balance["currency_name"]."' />";
	
	$smarty->assign("form",$form);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_voip_settings_form.tpl");
	exit;
}

function saveSettings(){
	global $dbconn, $VoIp, $lang, $dbconn, $config, $auth;
	
	if(isset($_SERVER["PHP_SELF"]))
		$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
		$file_name = "admin_voip_settings.php";

	AdminMainMenu($lang["settings"]);
	$VoIp->SetAdminSettings($_POST["admin_name"], $_POST["admin_password"], $_POST["account_id"], $_POST["admin_percent"], $_POST["curr_rate"]);
	$err = $VoIp->GetErrorMsg();
	listSettings($err);
}
?>