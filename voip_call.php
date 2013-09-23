<?php
/**
* Voice over IP
*
* @package DatingPro
* @subpackage User Mode
**/

include './include/config.php';
include './common.php';
if (!$config['voipcall_feature']) exit;
include './include/config_index.php';
include './include/functions_auth.php';
include './include/functions_index.php';
include './include/class.lang.php';
include './include/class.voip.php';

// authentication
$user = auth_index_user();

if (empty($user) || $user == 'err' || empty($user[ AUTH_ID_USER ])) {
	header('location: '.$config['site_root'].'/index.php');
	exit;
}

// check guest
if ($user[ AUTH_GUEST ]) {
	AlertPage(GetRightModulePath(__FILE__));
}

// check group, period, expiration
RefreshAccount();

// check status
if (!$user[ AUTH_STATUS ]) {
	AlertPage(GetRightModulePath(__FILE__));
}

// check permissions
IsFileAllowed(GetRightModulePath(__FILE__));

// alerts and statistics
if (!$user[ AUTH_GUEST ]) {
	GetAlertsMessage();
	SetModuleStatistic(GetRightModulePath(__FILE__));
}

// active menu item
$smarty->assign('sub_menu_num', '');

// global VoIp
$VoIp = new DatingVoIp($dbconn, $config, $user[ AUTH_ID_USER ]);

$file_name = 'voip_call.php';

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

// dispatcher
switch ($sel) {
	case 'rate':
		rateTable();
	break;
	
	case 'add_call_credits':
		addCallCredits();
	break;
	
	case 'call':
		CallToUser();
		// fall through in original code !!!
	case 'get_status':
		getStatusMess();
	break;
	
	default:
		listStatistic();
	break;
}

exit;


function listStatistic()
{
	global $VoIp, $user, $smarty, $config;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$smarty->assign('user_stat', $VoIp->GetDetailedStatisticForUser($user[ AUTH_ID_USER ]));
	
	$smarty->display(TrimSlash($config["index_theme_path"])."/voip_call_stat.tpl");
	exit;
}

function rateTable($error='')
{
	global $VoIp, $user, $smarty, $config, $lang, $file_name;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$from_number = $VoIp->GetPhoneById($user[ AUTH_ID_USER ]);
	if ($from_number){
		$smarty->assign("from_number",$from_number);
		
		$to_id_user = intval($_REQUEST["id_user"]);
		
		$rates["touser_name"] = $VoIp->GetLoginById($to_id_user);

		$balance = $VoIp->GetMemberBalanceForUser(false,2);
		
		if (!$balance) {
			$err = $VoIp->GetErrorMsg();
		} else {
			$rates["balance"] = $balance;
		}
		
		$rates["site_balance"] = $VoIp->GetDatingUserBalance($user[ AUTH_ID_USER ]);
		
		if ($rates["site_balance"]["balance"] <= 0) {
			$add_site_funds = str_replace('[add_funds_link]','payment.php?sel=update_account',$lang["voip"]["err"]["low_balance"]);
			$smarty->assign("add_site_funds",$add_site_funds);
		} else {
			$rates["site_balance"]["balance"] = round($rates["site_balance"]["balance"],2);
			$red_str = "?sel=rate&id_user=".$to_id_user;
			$add_form["hiddens"] = "<input type='hidden' name='sel' value='add_call_credits' />";
			$add_form["hiddens"] .= "<input type='hidden' name='id_user' value='".$to_id_user."' />";
			$add_form["hiddens"] .= "<input type='hidden' name='red_str' value='".$red_str."' />";
			$smarty->assign("add_form",$add_form);
		}
		
		$rate = $VoIp->GetRateForUser($to_id_user);
		
		if (!$rate) {
			$rate_err = $VoIp->GetErrorMsg();
		} else {
			$rates["rate"] = $rate;
		}
		
		$smarty->assign("rates",$rates);
		
		$smarty->assign('to_id_user',$to_id_user);
		$smarty->assign('file_name',$file_name);
		$smarty->assign('rate_err',$rate_err);
	} else {
		$error = $lang["voip"]["empty_from_number"];
	}
		
	$user_info = $VoIp->GetDatingUserInfo($to_id_user);
	$smarty->assign("user_info", $user_info);
	$smarty->assign('error',$error);
	$smarty->display(TrimSlash($config["index_theme_path"])."/voip_rate.tpl");
	exit;
}

function addCallCredits()
{
	global $lang, $user, $VoIp, $file_name;
	
	$add_credit = floatval($_REQUEST["funds"]);
	
	if ($add_credit == 0) {
		rateTable($lang["voip"]["err"]["zero_funds"]);
	}

	$site_balance = $VoIp->GetDatingUserBalance($user[ AUTH_ID_USER ]);	
	
	if ($add_credit > $site_balance["balance"]) {
		rateTable($lang["voip"]["err"]["too_big_funds"]);
	}
	
	if (!$VoIp->AddCallCreditForUser($add_credit)) {
		rateTable($lang["voip"]["err"]["cant_add_credits"]);
	}
	echo "<script type='text/javascript'>location.href='".$file_name.$_REQUEST['red_str']."';</script>";
	//header("location: ".$file_name.$_REQUEST['red_str']);
	//rateTable($lang["voip"]["call_caredits_added"]);
	
}

function CallToUser()
{
	global $VoIp, $lang;
	
	if (!$VoIp->CallRequest($_REQUEST["to_user"])) {
		$_SESSION["call_err"] = $VoIp->GetErrorMsg();
		echo "<script type='text/javascript'>document.getElementById('call_st_code').value = '-1';</script>";
		echo $_SESSION["call_err"];
	} else {
		unset($_SESSION["call_err"]);
	}
	echo $lang["voip"]["calling"]."<script type='text/javascript'>document.getElementById('rc_id').value = ".$VoIp->RC_call_id.";</script>";
	exit;
}

function getStatusMess()
{
	global $VoIp, $lang;
	
	$rc_id = $_REQUEST["rs_id"];
	$call_status = $VoIp->GetLastStatusCodeByRcId($rc_id);

	if ($mess = $VoIp->GetStatusMess($call_status)) {
		echo $mess;
	} elseif (!$_SESSION["call_err"]) {
		echo $lang["voip"]["calling"];
	} else {
		echo $_SESSION["call_err"];
		$call_status = -1;
	}

	if ($call_status != 0 && $call_status != 1 && $call_status != 2) {
		$call_status = -1;
	}
	
	echo "<script type='text/javascript'>document.getElementById('call_st_code').value = '".$call_status."';</script>";
	if ($call_status == -1) {
		echo "&nbsp;<a href='voip_call.php>".$lang["voip"]["statistic"]."</a>";
	}
	exit;
}

?>