<?php

/**
* Users subscribe management.
*
* @package DatingPro
* @subpackage Admin Mode
**/

include "../include/config.php";
include_once "../common.php";
include "../include/config_admin.php";
include "../include/functions_auth.php";
include "../include/functions_admin.php";

$auth = auth_user();
login_check($auth);
IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "newsletter");

$sel = isset($_REQUEST['sel']) ? $_REQUEST['sel']: "";
$type = isset($_REQUEST['type']) ? $_REQUEST['type']: "";

if($type== "s")
$par="sistem";
else
$par="admin";

switch($sel){
	case "sysedit": EditSysForm(); break;
	case "syschange": ChangeSysSubscribe(); break;
	case "sactive": ActiveSubscribe("sistem"); break;
	case "susers": UsersForm("sistem"); break;
	case "adduser": AddUserSubscribe(); break;
	case "deluser": DeleteUserSubscribe(); break;
	default: ListSubscribe();
}

function ListSubscribe($err=""){
	global $smarty, $dbconn, $config, $config_admin, $page, $lang;

	$file_name = (isset($_SERVER["PHP_SELF"]))?AfterLastSlash($_SERVER["PHP_SELF"]):"admin_newsletter.php";

	AdminMainMenu($lang["newsletter"]);
	$page = isset($_REQUEST['page']) ?  intval($_REQUEST['page']) : 1;

	$strSQL = "select id, status from ".SUBSCRIBE_SISTEM_TABLE." order by id";
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	if($rs->RowCount()>0){
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$sistem[$i]["number"] = $i+1;
			$sistem[$i]["id"] = $row["id"];
			$sistem[$i]["name"] = $lang["subcribe"]["alert_".$row["id"]];
			$sistem[$i]["editlink"] = $file_name."?sel=sysedit&page=".$page."&id_subscribe=".$row["id"];
			$sistem[$i]["status"] = $row["status"]?"+":"";
			$sistem[$i]["userlink"] = $file_name."?sel=susers&id_subscribe=".$rs->fields[0];
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign("sistem", $sistem);
		$form["hiddens_sistem_active"] = "<input type=hidden name=sel value=sactive>";
		$form["hiddens_sistem_active"] .= "<input type=hidden name=page value=\"".$page."\">";
	}
	///	form
	if(!$err){
		$name = "";
	}
	$form["err"] = $err;
	$form["action"] = $file_name;
	$form["confirm"] = $lang["confirm"]["newsletter"];

	$smarty->assign("add_link", $file_name."?sel=add&page=".$page);
	$smarty->assign("form", $form);
	$smarty->assign("button", $lang["button"]);
	$smarty->assign("header", $lang["newsletter"]);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_newsletter_table.tpl");
	exit;
}

/////////////////////////////////////////////////////////////// set status of selected subscribes equal 1 or 0
function ActiveSubscribe($par="system"){
	global $smarty, $dbconn, $config, $config_admin, $page, $lang;
	$active = array();

	$active = isset($_REQUEST['active']) ? $_REQUEST['active'] : "";

	if((is_array($active) && count($active)==0) || !is_array($active)){
		$dbconn->Execute("update ".SUBSCRIBE_SISTEM_TABLE." set status='0'");
	}elseif(is_array($active) && count($active)>0){
		$strSQL = "Select id, status from ".SUBSCRIBE_SISTEM_TABLE." order by id";
		$rs = $dbconn->Execute($strSQL);
		if($rs->RowCount()>0){
			$i = 0;
			while(!$rs->EOF){
				$row = $rs->GetRowAssoc(false);
				if(in_array($row["id"], $active) && $row["status"]==0)
				$dbconn->Execute("update ".SUBSCRIBE_SISTEM_TABLE." set status='1' where id='".$row["id"]."'");
				elseif(!in_array($row["id"], $active) && $row["status"]==1)
				$dbconn->Execute("update ".SUBSCRIBE_SISTEM_TABLE." set status='0' where id='".$row["id"]."'");
				$rs->MoveNext();
				$i++;
			}
		}
	}
	ListSubscribe(); return;
}

function UsersForm($par, $err=""){
	global $smarty, $dbconn, $config, $config_admin, $lang, $file_name;

	$type = "s";	$sel = "susers"; $table = SUBSCRIBE_SISTEM_TABLE;

	$id_subscribe = isset($_REQUEST["id_subscribe"]) ? intval($_REQUEST["id_subscribe"]) : null;
	$search = isset($_REQUEST["search"]) ? strval(strip_tags($_REQUEST["search"])) : null;
	$s_type = isset($_REQUEST["s_type"]) ? intval($_REQUEST["s_type"]) : 0;
	$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;

	$file_name = (isset($_SERVER["PHP_SELF"]))?AfterLastSlash($_SERVER["PHP_SELF"]):"admin_newsletter.php";

	AdminMainMenu($lang["newsletter"]);

	if(!$search) $search = $lang["newsletter"]["initial_word"];

	if(strval($search)){
		$search = strip_tags($search);
		switch($s_type){
			case "1": $search_str=" login like '".$search."%'"; break;
			case "2": $search_str=" fname like '%".$search."%'"; break;
			case "3": $search_str=" sname like '%".$search."%'"; break;
			case "4": $search_str=" email like '%".$search."%'"; break;
			default: $search_str=" login like '".$search."%'";
		}
	}
	$form["search_hiddens"] = "<input type=hidden name=id_subscribe value=".$id_subscribe.">";
	$form["search_hiddens"] .= "<input type=hidden name=page value=".$page.">";
	$form["search_hiddens"] .= "<input type=hidden name=type value=".$type.">";
	$form["search_hiddens"] .= "<input type=hidden name=sel value=".$sel.">";
	///////// search form
	for($i=0;$i<4;$i++){
		if($s_type==($i+1))$types[$i]["sel"]="1";
		$types[$i]["value"]=$lang["users"]["type_".($i+1)];
	}
	$smarty->assign("types", $types);
	$smarty->assign("search", $search);

	$_arr = array();
	$strSQL = "select id_user from ".SUBSCRIBE_USER_TABLE." where id_subscribe='".$id_subscribe."' and type='".$type."'";
	$rs = $dbconn->Execute($strSQL);
	$num_records = $rs->RowCount();
	$i = 0;
	while(!$rs->EOF){
		array_push($_arr, $rs->fields[0]);
		$rs->MoveNext();
		$i++;
	}
	if (sizeof($_arr)>0) {
		$all_str = implode(",", $_arr);
	} else {
		$all_str = "";
	}

	$lim_min = ($page-1)*$config_admin["subscribe_user_numpage"];
	$lim_max = $config_admin["subscribe_user_numpage"];
	$limit_str = " limit ".$lim_min.", ".$lim_max;
	$strSQL = " SELECT DISTINCT b.id, a.id as id_user, a.login, a.fname, a.sname, a.email
				FROM ".SUBSCRIBE_USER_TABLE." b
				LEFT JOIN ".USERS_TABLE." a ON a.id=b.id_user
				WHERE b.id_subscribe='".$id_subscribe."' AND b.type='".$type."'
				GROUP BY b.id ORDER BY a.login ".$limit_str;
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	if($rs->RowCount()>0){
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$subscr[$i]["number"] = ($page-1)*$config_admin["subscribe_user_numpage"]+($i+1);
			$subscr[$i]["id"] = $row["id_user"];
			$subscr[$i]["name"] = $row["fname"]." ".$row["sname"];
			$subscr[$i]["login"] = $row["login"];
			$subscr[$i]["email"] = $row["email"];
			$subscr[$i]["deletelink"] = $file_name."?sel=deluser&id_subscribe=".$id_subscribe."&type=".$type."&id=".$row["id_user"]."&s_type=".$s_type."&search=".$search;
			$rs->MoveNext();
			$i++;
		}
		$param = $file_name."?sel=".$sel."&id=".$id_subscribe."&par=".$par."&s_type=".$s_type."&search=".$search."&";
		$smarty->assign("links", GetLinkStr($num_records,$page,$param,$config_admin["subscribe_user_numpage"]));
		$smarty->assign("subscribers", $subscr);
	}

	if(isset($all_str) && strval($all_str) != ""){
		$in_str = " and a.id not in (".$all_str.")";
	}else{
		$in_str = "";
	}
	$strSQL = " SELECT a.id, CONCAT(a.sname,' ',a.fname, ' (', a.login,')') as username
				FROM ".USERS_TABLE." a
				where ".$search_str." ".$in_str." and root_user != '1' and guest_user != '1'";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>0) {
		$i = 0;
		while(!$rs->EOF){
			$users_arr[$i]["value"] = $rs->fields[0];
			$users_arr[$i]["name"] = $rs->fields[1];
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign("users_arr", $users_arr);
	}

	$form["add_hiddens"] = "<input type=hidden name=id_subscribe value=".$id_subscribe.">";
	$form["add_hiddens"] .= "<input type=hidden name=page value=".$page.">";
	$form["add_hiddens"] .= "<input type=hidden name=type value=".$type.">";
	$form["add_hiddens"] .= "<input type=hidden name=sel value=adduser>";
	$form["add_hiddens"] .= "<input type=hidden name=search value=".$search.">";
	$form["add_hiddens"] .= "<input type=hidden name=s_type value=".$s_type.">";
	$form["add_hiddens"] .= "<input type=hidden name=users_str value=\"\">";

	$form["action"] = $file_name;
	$form["err"] = $err;
	$form["confirm"] = $lang["confirm"]["newsletter_subscribers"];
	$form["back"] = $file_name;

	$rs=$dbconn->Execute("select description from ".$table." where id='".$id_subscribe."'");
	$form["subscribe_name"] = $rs->fields[0];

	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["newsletter"]);
	$smarty->assign("button", $lang["button"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_newsletter_users_table.tpl");
	exit;
}

/////////////////////////////////////////////////////////////// add users into subscribe userlist
function AddUserSubscribe(){
	global $smarty, $dbconn, $config, $config_admin, $lang, $file_name;

	$id_subscribe = isset($_REQUEST["id_subscribe"]) ? intval($_REQUEST["id_subscribe"]) : null;
	$type = isset($_REQUEST["type"]) ? strval($_REQUEST["type"]) : null;

	if(!$id_subscribe || !$type){	ListSubscribe(); return;}

	if($type== "s")
	$par="sistem";
	else
	$par="admin";

	$users_arr = array();
	$users_arr = explode(", ", $_POST["users_str"]);
	if(is_array($users_arr) && count($users_arr)>0){
		for($i=0;$i<count($users_arr);$i++){
			if(intval($users_arr[$i])){
				$dbconn->Execute("DELETE FROM ".SUBSCRIBE_USER_TABLE." WHERE id_subscribe='".$id_subscribe."' AND id_user='".$users_arr[$i]."' and type='".$type."'");
				$dbconn->Execute("INSERT INTO ".SUBSCRIBE_USER_TABLE." (id_subscribe, id_user, type) VALUES ('".$id_subscribe."', '".$users_arr[$i]."', '".$type."')");
			}
		}
	}
	UsersForm($par);
	return;
}

/////////////////////////////////////////////////////////////// delete users from subscribe userlist
function DeleteUserSubscribe(){
	global $smarty, $dbconn, $config, $config_admin, $lang, $file_name;

	$id = intval($_GET["id"]);
	$id_subscribe = intval($_GET["id_subscribe"]);
	$type = strval($_GET["type"]);

	if($type== "s")
	$par="sistem";
	else
	$par="admin";

	if(!$id_subscribe || !$type){	ListSubscribe(); return;}
	if(!$id){	UsersForm($par); return;}

	$dbconn->Execute("delete from ".SUBSCRIBE_USER_TABLE." where (id_subscribe='".$id_subscribe."' and id_user='".$id."' and type='".$type."') ");
	UsersForm($par);
	return;
}

?>