<?php
include "../include/config.php";
include_once "../common.php";
include "../include/config_admin.php";
include "../include/functions_auth.php";
include "../include/functions_admin.php";

$auth = auth_user();
login_check($auth);
IsFileAllowed($auth[0], GetRightModulePath(__FILE__));

if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
else
	$file_name = "admin_shoutbox.php";
$smarty->assign('file_name',$file_name);

$sel = $_REQUEST["sel"];
switch ($sel)
{
	case 'save_limit': SaveLimit(); break;
	case 'save_text': SaveText(); break;
	case 'delete': DeleteMessage(); break;
	case 'edit': EditMessage(); break;
	case 'reset_statistic': ResetStat(); break;
	default: ListMessages();
}

function ListMessages($err='')
{
	global $smarty, $dbconn, $config, $lang, $file_name;

	AdminMainMenu($lang["shoutbox"]);

	$limit = GetSiteSettings('shout_messages_limit');

	$strSQL = "SELECT st.id, st.user_id, st.text, st.date_add, st.status, ut.login FROM ".SHOUTS_TABLE." st, ".USERS_TABLE." ut
				WHERE st.user_id=ut.id";
	$rs = $dbconn->Execute($strSQL);
	$i=0;
	while (!$rs->EOF)
	{
		$row = $rs->GetRowAssoc(false);
		$shoutbox[$i]["id"] = $row["id"];
		$shoutbox[$i]["login"] = $row["login"];
		$shoutbox[$i]["text"] = $row["text"];
		$shoutbox[$i]["date_add"] = $row["date_add"];
		$shoutbox[$i]["status"] = $row["status"];
		$shoutbox[$i]["profile_link"] = "admin_users.php?sel=edit&id=".$row["user_id"];
		$shoutbox[$i]["delete_link"] = "?sel=del&id=".$row["id"];
		$rs->MoveNext();
		$i++;
	}

	$strSQL = "SELECT ut.id, ut.login FROM ".SHOUTS_USER_STAT_TABLE." st, ".USERS_TABLE." ut
				WHERE st.id_user=ut.id ORDER BY st.count_mess desc limit 0,1";
	$rs = $dbconn->Execute($strSQL);
	$stat['ma_profile_link'] = "admin_users.php?sel=edit&id=".$rs->fields[0];
	$stat['ma_login'] = $rs->fields[1];
	$strSQL="SELECT SUM(count_mess) FROM ".SHOUTS_USER_STAT_TABLE;
	$stat["total_mess"] = intval($dbconn->GetOne($strSQL));
	$stat["reset_link"] = "?sel=reset_statistic";
	$form['hiddens'] = "<input type='hidden' name='sel' value='save_limit' />";
	$form["error"] = $err;

	$smarty->assign('form',$form);
	$smarty->assign('limit',$limit);
	$smarty->assign('shoutbox',$shoutbox);
	$smarty->assign('stat',$stat);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_shoutbox_table.tpl");
	exit;
}

function SaveLimit(){
	global $dbconn, $lang;

	$limit = intval($_POST["limit"]);

	if ($limit == 0) ListMessages($lang["shoutbox"]["limit_err"]);
	$strSQL = " UPDATE ".SETTINGS_TABLE." SET value='".$limit."' WHERE name='shout_messages_limit' ";
	$dbconn->Execute($strSQL);
	ListMessages();
	return;
}

function SaveText(){
	global $dbconn, $lang;

	$id = intval($_POST["id"]);
	$text = strip_tags(addslashes($_POST["text"]));
	$status = isset($_POST["status"]) ? intval($_POST["status"]) : 0;
	
	if ($id == 0)
	{
		echo "<font class='error_msg'>* id is missing</font><br />";
		die();
	}

	$strSQL = "UPDATE ".SHOUTS_TABLE." SET text='".$text."', status='".$status."' WHERE id='".$id."'";
	$dbconn->Execute($strSQL);
	if ($dbconn->ErrorNo==0)
		echo "<font class='error_msg'>* ".$lang["shoutbox"]["saved"]."</font><br />";
	else
		echo "<font class='error_msg'>* Error</font><br />";
	exit;
}

function DeleteMessage(){
	global $dbconn, $lang;
	$id = intval($_POST["id"]);

	if ($id == 0) exit;

	$strSQL = "DELETE FROM ".SHOUTS_TABLE." WHERE id='".$id."'";
	$dbconn->Execute($strSQL);
	exit;
}

function ResetStat(){
	global $dbconn, $file_name;

	$strSQL = "DELETE FROM ".SHOUTS_USER_STAT_TABLE;
	$dbconn->Execute($strSQL);
	header("location:".$file_name);
}
?>