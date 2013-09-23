<?php
/**
* Take a tour information page
*
* @package DatingPro
* @subpackage User Mode
**/

include './include/config.php';
include './common.php';
include './include/config_index.php';
include './include/functions_auth.php';
include './include/functions_index.php';
include './include/class.lang.php';

// authentication
$user = auth_index_user();

if (empty($user) || $user == 'err' || empty($user[ AUTH_ID_USER ])) {
	header('location: '.$config['site_root'].'/index.php');
	exit;
}

// check guest
// (public access)

// check group, period, expiration
RefreshAccount();

// check status
// (public access)

// check permissions
// (public access)

// alerts and statistics
if (!$user[ AUTH_GUEST ]) {
	GetAlertsMessage();
	SetModuleStatistic(GetRightModulePath(__FILE__));
}

// active menu item
$smarty->assign('sub_menu_num', '');

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// dispatcher
switch ($sel) {
	default:
		TaketourTable($id);
	break;
}

exit;


function TaketourTable($id = "")
{
	global $lang, $config, $smarty, $dbconn;
	
	IndexHomePage();

	$file_name = "taketour.php";

	////// settings
	$rs = $dbconn->Execute("select name, value from ".SETTINGS_TABLE." where name in ('taketour_folder')");
	while(!$rs->EOF){
		$settings[$rs->fields[0]] = $rs->fields[1];
		$rs->MoveNext();
	}
	if(!$id)
	$strSQL = "select id, comment, status, file_path, file_type, sorter from ".TAKE_TOUR_TABLE." where status='1' order by sorter";
	else
	$strSQL = "select id, comment, status, file_path, file_type, sorter from ".TAKE_TOUR_TABLE." where id='".$id."' and status='1'";
	$rs = $dbconn->Execute($strSQL);
	$row = $rs->GetRowAssoc(false);
	if(!$id)	 $id = $row["id"];
	$data["comment"] = stripslashes(nl2br($row["comment"]));
	$data["status"] = $row["status"];
	$data["sorter"] = $row["sorter"];
	$file_path = $config["site_path"].$settings["taketour_folder"]."/".$row["file_path"];
	if(strlen($row["file_path"])>0 && file_exists($file_path)){
		$data["file_type"] = $row["file_type"]?$row["file_type"]:"p";
		$data["file_path"] = $config["site_root"].$settings["taketour_folder"]."/".$row["file_path"];
	}else{
		$data["file_path"] = "";
	}
	//// links
	$strSQL = "select id  from ".TAKE_TOUR_TABLE." where sorter>'".$data["sorter"]."' and status='1' order by sorter";
	$rs = $dbconn->Execute($strSQL);
	if($rs->fields[0]>0)
	$data["next_link"] = $file_name."?id=".$rs->fields[0];
	else
	$data["next_link"] = "";
	$strSQL = "select id  from ".TAKE_TOUR_TABLE." where sorter<'".$data["sorter"]."' and status='1' order by sorter desc";
	$rs = $dbconn->Execute($strSQL);
	if($rs->fields[0]>0)
	$data["prev_link"] = $file_name."?id=".$rs->fields[0];
	else
	$data["prev_link"] = "";

	$smarty->assign("header", $lang["taketour"]);
	$smarty->assign("data", $data);
	$smarty->display(TrimSlash($config["index_theme_path"])."/taketour_table.tpl");
	exit;
}
?>