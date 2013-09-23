<?php
/**
* Site forbidden words editing and statistics
*
* @package DatingPro
* @subpackage Admin Mode
**/

include '../include/config.php';
include_once '../common.php';
include '../include/config_admin.php';
include '../include/functions_auth.php';
include '../include/functions_admin.php';

$auth = auth_user();
login_check($auth);
IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "badwords");

$sel = isset($_REQUEST["sel"]) ? $_REQUEST["sel"] : "";
$area = isset($_REQUEST["area"]) ? intval($_REQUEST["area"]) : 1;

switch($sel){
	case "upload": UploadFile(); break;
	case "edit": EditFile(); break;
	case "delete": DeleteEntry(); break;
	case "fileform": ViewFile(); break;
	case "statistic": ViewStatistic(); break;
	default: ListViolocations($area);
}

function ListViolocations($area=""){
	global $smarty, $dbconn, $config, $config_admin, $page, $lang;

	if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
	$file_name = "admin_badwords.php";

	AdminMainMenu($lang["badwords"]);
	$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;
	$orderby = isset($_REQUEST["orderby"]) ? intval($_REQUEST["orderby"]) : 1;

	switch($orderby){
		case 1: $orderby_str = " ORDER BY username "; break;
		case 2: $orderby_str = " ORDER BY alert_data DESC "; break;
		default: $orderby_str = " ORDER BY alert_data DESC "; break;
	}

	$strSQL = " SELECT COUNT(id) FROM ".BADWORDS_TABLE." WHERE area='".intval($area)."'";
	$rs = $dbconn->Execute($strSQL);
	$num_records = $rs->fields[0];

	$lim_min = ($page-1)*$config_admin["badwords_numpage"];
	$lim_max = $config_admin["badwords_numpage"];
	$limit_str = " LIMIT ".$lim_min.", ".$lim_max;
	$strSQL = " SELECT a.id, a.id_user, DATE_FORMAT(a.date_alert,'".$config["date_format"]." %H:%i') as alert_data, b.login as username
				FROM ".BADWORDS_TABLE." a
				LEFT JOIN ".USERS_TABLE." b ON b.id=a.id_user
				WHERE a.area='".intval($area)."' ".$orderby_str." ".$limit_str;
	$rs = $dbconn->Execute($strSQL);

	$i = 0;
	if($rs->RowCount()>0){
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$vialocations[$i]["number"] = ($page-1)*$config_admin["badwords_numpage"]+($i+1);
			$vialocations[$i]["name"] = $row["username"];
			$vialocations[$i]["date"] = $row["alert_data"];
			$vialocations[$i]["communicate_link"] = "./admin_comunicate.php?id=".$row["id_user"];
			$vialocations[$i]["statistic_link"] = "./".$file_name."?sel=statistic&id=".$row["id_user"];
			$vialocations[$i]["delete_link"] = "./".$file_name."?sel=delete&area=".$area."&id=".$row["id"];
			$rs->MoveNext();
			$i++;
		}
		$param = $file_name."?area=".$area."&orderby=".$orderby;
		$smarty->assign("links", GetLinkStr($num_records,$page,$param,$config_admin["badwords_numpage"]));
		$smarty->assign("vialocations", $vialocations);
	}

	$form["hiddens"] = "<input type=hidden name=page value=".$page.">";
	$form["hiddens"] .= "<input type=hidden name=orderby value=".$orderby.">";

	$form["action"] = $file_name;
	$form["orderby_1"] = $file_name."?orderby=1&area=".$area;
	$form["orderby_2"] = $file_name."?orderby=2&area=".$area;
	$form["err"] = isset($err) ? $err : "";
	$data["area"] = $area;

	$smarty->assign("form", $form);
	$smarty->assign("data", $data);
	$smarty->assign("button", $lang["button"]);
	$smarty->assign("header", $lang["badwords"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_badwords_alerts_table.tpl");
	exit;
}

function UploadFile(){
	global $smarty, $dbconn, $config, $config_admin, $page, $lang, $_FILES;

	$upload=$_FILES["upload_file"];
	$type = intval($_POST["upload_type"]);
	if($type == 2){
		$param = "a+";
	}else{
		$param = "w";
	}
	$settings = GetSiteSettings(array('badwords_file_path', 'badwords_file_name'));

	$file_path = DelLastSlash($config["site_path"])."/".TrimSlash($settings["badwords_file_path"])."/".TrimSlash($settings["badwords_file_name"]);
	if(file_exists($file_path) && is_writeable($file_path)){
		if(is_uploaded_file($upload["tmp_name"])  && is_readable($upload["tmp_name"])){
			$temp_file = implode("", file($upload["tmp_name"]));
			unlink($upload["tmp_name"]);
			$fp = fopen ($file_path, $param);
			fputs($fp,$temp_file);
			fclose($fp);
			$err = "";
		}else{
			$err = $lang["err"]["not_readable_file"];
		}
	}else{
		$err = $lang["err"]["not_writeable_file"];
	}
	ViewFile($err);
	return;
}

function EditFile(){
	global $smarty, $dbconn, $config, $config_admin, $page, $lang;

	$bwdata = $_POST["content"];
	$bwdata = stripslashes($bwdata);
	$bwdata = ereg_replace("\n", "", $bwdata);

	$settings = GetSiteSettings(array('badwords_file_path', 'badwords_file_name'));

	$file_path = DelLastSlash($config["site_path"])."/".TrimSlash($settings["badwords_file_path"])."/".TrimSlash($settings["badwords_file_name"]);
	if(file_exists($file_path) && is_writeable($file_path)){
		$fp = fopen ($file_path, "w");
		fputs($fp,$bwdata);
		fclose($fp);
		$err = "";
	} else {
		$err = $lang["err"]["not_writeable_file"];
	}

	ViewFile($err);
	return;
}

function ViewFile($err=""){
	global $smarty, $dbconn, $config, $config_admin, $page, $lang;

	if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
	$file_name = "admin_badwords.php";

	AdminMainMenu($lang["badwords"]);

	$settings = GetSiteSettings(array('badwords_file_path', 'badwords_file_name'));

	$file_path = DelLastSlash($config["site_path"])."/".TrimSlash($settings["badwords_file_path"])."/".TrimSlash($settings["badwords_file_name"]);
	if(file_exists($file_path) && is_readable($file_path)){
		$data["content"] = implode("", file($file_path));
	}else{
		$form["err"] = $lang["err"]["not_readable_file"];
	}
	$form["action"] = $file_name;
	$form["hiddens_edit"] = "<input type=hidden name=sel value=edit>";
	$form["hiddens_upload"] = "<input type=hidden name=sel value=upload>";

	if (isset($data)) {
		$smarty->assign("data", $data);
	}
	$smarty->assign("form", $form);

	$smarty->assign("header", $lang["badwords"]);
	$smarty->assign("button", $lang["button"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_badwords_table.tpl");
	exit;
}

function DeleteEntry(){
	global $smarty, $dbconn, $config, $config_admin, $page, $lang;
	$area = isset($_REQUEST["area"]) ? intval($_REQUEST["area"]) : 1;
	$id = isset($_REQUEST["id"]) ? intval($_REQUEST["id"]) : 0;

	if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
	$file_name = "admin_badwords.php";

	if ($id>0) {
		$dbconn->Execute("Delete from ".BADWORDS_TABLE." where id='".$id."'");
	}
	ListViolocations($area);
	return;
}

function ViewStatistic(){
	global $smarty, $dbconn, $config, $config_admin, $page, $lang;

	if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
	$file_name = "admin_badwords.php";

	AdminMainMenu($lang["badwords"], 1);

	$id = isset($_REQUEST["id"]) ? intval($_REQUEST["id"]) : 0;

	if($id >0){
		$strSQL = "Select login from ".USERS_TABLE." where id='".$id."'";
		$rs = $dbconn->Execute($strSQL);
		$data["username"] = $rs->fields[0];
		$strSQL = "Select area, count(id) as coll from ".BADWORDS_TABLE." where id_user ='".$id."' group by area";
		$rs = $dbconn->Execute($strSQL);
		$all = 0;
		while(!$rs->EOF){
			$statistic[$rs->fields[0]]["coll"] = intval($rs->fields[1]);
			$all +=$rs->fields[1];
			$rs->MoveNext();
		}
		$statistic["all"] = $all;
	}
	$smarty->assign("statistic", $statistic);
	$smarty->assign("data", $data);
	$smarty->assign("button", $lang["button"]);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_badwords_statistic.tpl");
	exit;
}

?>