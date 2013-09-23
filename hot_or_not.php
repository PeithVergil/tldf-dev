<?php
/**
* Hot or not
*
* @package DatingPro
* @subpackage User Mode
**/

include './include/config.php';
include './common.php';
include './include/config_index.php';
include './include/functions_auth.php';
include './include/functions_index.php';
include './include/functions_users.php';
include './include/class.lang.php';
include './include/class.hotornot.php';

// authentication
$user = auth_index_user();

if (empty($user) || $user == 'err' || empty($user[ AUTH_ID_USER ])) {
	header('location: '.$config['site_root'].'/index.php');
	exit;
}

// check guest
// (handled by permissions)

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

// global HotOrNot
$HotOrNot = new HotOrNot($dbconn, $config, $user);

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

// dispatcher
switch ($sel) {
	case 'rate': rateUser(); break;
	case 'return_main_part': returnMainPart(); break;
	case 'get_addition_preview': getAdditionPreview(); break;
	case 'be_couple': 	$res = MakeCoupleAction(); listHotOrNot($res['err']); break;
	case 'return_tops': returnTops(); break;
	case 'return_stats': returnStats(); break;
	default: listHotOrNot(); break;
}

exit;


function listHotOrNot($err='')
{
	global $smarty, $config, $user, $HotOrNot, $lang;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$_arr = range(1,10);
	$smarty->assign("vote_arr", $_arr);
	
	$preview = $HotOrNot->getPrevewPhotos();
	$smarty->assign("preview",$preview);
	
	_getMainPartData();
	
	$tops = $HotOrNot->getTops();
	$smarty->assign('tops',$tops);
	
	$user_stats = $HotOrNot->getUserStats($user[ AUTH_ID_USER ]);
	if ($user_stats){
		$user_stats['votes_count_phrase'] = str_replace('[count]',$user_stats['voters_count'],$lang["hotornot"]["based_on"]);
		$smarty->assign('user_stats',$user_stats);
	}
	if ($err) $smarty->assign("err",$err);
	$smarty->display(TrimSlash($config["index_theme_path"])."/hot_or_not.tpl");
	exit;
}

function rateUser(){
	global $HotOrNot, $smarty, $config;
	
	$id = intval($_REQUEST["id_user"]);
	$estim = intval($_REQUEST["estim"]);
	
	if ($id < 1 || $estim < 1 || $estim > 10) return false;
	
	RateProfile($id, $estim);

	$smarty->assign('rated_userinfo',$HotOrNot->getRatedUserInfo($id));
	
	echo $smarty->fetch(TrimSlash($config["index_theme_path"])."/hot_or_not_postview.tpl");
	exit;
}

function returnMainPart(){
	global $smarty,$config;
	IndexHomePage();
	_getMainPartData($_REQUEST["id_user"]);
	
	echo $smarty->fetch(TrimSlash($config["index_theme_path"])."/hot_or_not_userinfo.tpl");
	echo "<script type='text/javascript'>i=1;</script>";
}

function _getMainPartData($id_user = false){
	global $smarty, $smarty, $HotOrNot, $lang;
	
	$user_info = $HotOrNot->getUserInfo($id_user);
	
	if (!$user_info) $smarty->assign("err", $lang["hotornot"]["end_reached"]);
	
	$smarty->assign("user_info",$user_info);

	$form["use_kiss_types"] = $HotOrNot->config["use_kiss_types"];
	$form["use_friend_types"] = $HotOrNot->config["use_friend_types"];
	$smarty->assign("form",$form);
}

function getAdditionPreview(){
	global $HotOrNot;
	
	$previews = $HotOrNot->getPrevewPhotos(1,$_REQUEST["from_id"], $_REQUEST["count"]);
	
	foreach ($previews as $key=>$value){
		echo "<input type='hidden' id='addition_preview_id_".$key."' value='".$value["id"]."' />";
		echo "<input type='hidden' id='addition_preview_upload_url_".$key."' value='".$value["upload_url"]."' />";
		echo "<input type='hidden' id='addition_preview_array_index_".$key."' value='".$value["array_index"]."' />";
	}
	$count_previews = count($previews);
	echo "<input type='hidden' id='count_added' value='".$count_previews."' />";
	
	exit;
}

function returnTops(){
	global $smarty, $HotOrNot, $config;
	$tops = $HotOrNot->getTops();
	$smarty->assign('tops',$tops);
	echo $smarty->fetch(TrimSlash($config["index_theme_path"])."/hot_or_not_tops.tpl");
	exit;
}

function returnStats(){
	global $smarty, $HotOrNot, $config, $lang, $user;
	$user_stats = $HotOrNot->getUserStats($user[ AUTH_ID_USER ]);
	if ($user_stats){
		$user_stats['votes_count_phrase'] = str_replace('[count]',$user_stats['voters_count'],$lang["hotornot"]["based_on"]);
		$smarty->assign('user_stats',$user_stats);
	}
	echo $smarty->fetch(TrimSlash($config["index_theme_path"])."/hot_or_not_stats.tpl");
	exit;
}
?>