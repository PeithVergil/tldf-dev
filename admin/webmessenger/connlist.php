<?php
/**
*
*
*
*   @author Alexander Kiverin<aki@pilotgroup.net>, Pilot Group <http://www.pilotgroup.net/>
*   @date   11/11/2006
*
**/
include "./common_webmessenger.php";

AdminMainMenu($lang["messenger"]);

if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
else
	$file_name = "connlist.php";

$page = $_GET["page"]?$_GET["page"]:$_POST["page"];
if( ($page == "") || ($page == "0")) $page = 1;
$sorter = $_GET["sorter"]?$_GET["sorter"]:$_POST["sorter"];

///////// sorter
$sorter_str = " order by ";
switch($sorter){
	case "user":	$sorter_str.="user"; break;
	case "connect":	$sorter_str.="date_connected"; break;
	default:	$sorter_str.="user"; break;
}
$smarty->assign("sorter", $sorter);

$strSQL = "select count(*) from ".UP_MESSENGER_ACTIVE_USERS_TABLE;
$rs = $dbconn->Execute($strSQL);
$num_records = $rs->fields[0];

///////// page
$lim_min = ($page-1)*$config_admin["chat_numpage"];
$lim_max = $config_admin["chat_numpage"];
$limit_str = " limit ".$lim_min.", ".$lim_max;
$smarty->assign("page", $page);
		
///////// query
$query = "select b.login as user, DATE_FORMAT(a.lastTimeOnline,'".$config["date_format"]." %H:%i') as date_connected 
	from ".UP_MESSENGER_ACTIVE_USERS_TABLE." a
	left join ".USERS_TABLE." b on b.id = a.UserID".$sorter_str.$limit_str;
$rs = $dbconn->Execute($query);

$conns = array();
$i = 0;
while (!$rs->EOF) {
	$row = $rs->GetRowAssoc(false);
	$conns[$i]["number"] = ($page-1)*$config_admin["chat_numpage"]+($i+1);
	$conns[$i]["user"] = $row["user"];
	$conns[$i]["connected"] = $row["date_connected"];

	$i++;
	$rs->MoveNext();
}
$param = $file_name."?sorter=".$sorter."&";
$smarty->assign("links", GetLinkStr($num_records,$page,$param,$config_admin["chat_numpage"]));
$smarty->assign("conns",$conns);

$form["action"] = "./".$file_name;

$smarty->assign("form",$form);
$smarty->display(TrimSlash($config["admin_webmessenger_theme_path"])."/connlist.tpl");
exit;
?>