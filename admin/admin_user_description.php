<?php

/**
* Site "User description" reference management.
*
* @package DatingPro
* @subpackage Admin Mode
**/

include "../include/config.php";
include_once "../common.php";
include "../include/config_admin.php";
include "../include/functions_auth.php";
include "../include/functions_admin.php";
include "../include/class.lang.php";
include "../include/class.percent.php";

$auth = auth_user();
login_check($auth);
IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "users");

$sel = isset($_REQUEST['sel']) ? $_REQUEST['sel'] : '';

switch ($sel) {
	case "change": ChangeDescr(); break;
	default: ListDescr();
}

exit;


function ListDescr($err="")
{
	global $smarty, $dbconn, $config, $lang;

	$file_name = "admin_user_description.php";

	AdminMainMenu($lang["users"], "1");

	$id = intval($_REQUEST["id"]);

	if ($id) {
		$rs = $dbconn->Execute("Select id_spr, id_value from ".DESCR_SPR_USER_TABLE." where id_user='".$id."'");
		while(!$rs->EOF){
			$id_spr = $rs->fields[0];
			$id_value = $rs->fields[1];
			if (!isset($sess_info[$id_spr])) {
				$sess_info[$id_spr] = array();
			}
			$sess_info[$id_spr][count($sess_info[$id_spr])+1] = $id_value;
			$rs->MoveNext();
		}
	}

	if ($id > 0) {
		$strSQL = "select concat(fname, ' ', sname, ' (',login,')'), root_user from ".USERS_TABLE." where id='".$id."'";
		$rs = $dbconn->Execute($strSQL);
		$data["username"] = $rs->fields[0];
		$data["id"] = $id;
		$data["root"] = $rs->fields[1];
	} else {
		$data["username"] = $lang["users"]["default_user"];
	}
	
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	$table_key = $multi_lang->TableKey(DESCR_SPR_TABLE);
	$table_key_val = $multi_lang->TableKey(DESCR_SPR_VALUE_TABLE);

	$strSQL = "select distinct a.id, b.".$field_name." as name, a.type from ".DESCR_SPR_TABLE." a left join ".REFERENCE_LANG_TABLE." b on b.table_key='".$table_key."' and b.id_reference=a.id order by a.sorter ";
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$info = array();
	while (!$rs->EOF) {
		$info[$i]["id"] = $rs->fields[0];
		$info[$i]["name"] = $rs->fields[1];
		$info[$i]["type"] = $rs->fields[2];

		$strSQL_opt = "select distinct a.id, b.".$field_name." as name from ".DESCR_SPR_VALUE_TABLE." a left join ".REFERENCE_LANG_TABLE." b on b.table_key='".$table_key_val."' and b.id_reference=a.id where a.id_spr='".$rs->fields[0]."' order by name ";
		$rs_opt = $dbconn->Execute($strSQL_opt);
		$j = 0;
		while (!$rs_opt ->EOF) {
			$info[$i]["opt_value"][$j] = $rs_opt->fields[0];
			$info[$i]["opt_name"][$j] = $rs_opt->fields[1];
			if (is_array($sess_info[$rs->fields[0]]) && in_array(0, $sess_info[$rs->fields[0]])) {
				$info[$i]["sel_all"] = "1";
			} else {
				if (is_array($sess_info[$rs->fields[0]]) && in_array($rs_opt->fields[0], $sess_info[$rs->fields[0]])) {
					$info[$i]["opt_sel"][$j] = $rs_opt->fields[0];
				} else {
					$info[$i]["opt_sel"][$j] = 0;
				}
			}
			$rs_opt->MoveNext();
			$j++;
		}
		$rs->MoveNext();
		$i++;
	}

	$form["action"] = $file_name;

	$form["err"] = $err;

	$smarty->assign("data", $data);
	$smarty->assign("info", $info);
	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["users"]);
	$smarty->assign("button", $lang["button"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_user_descr_form.tpl");
	exit;
}
////////////////////////////////////////////////////////////////////////////////////////////////
function ChangeDescr()
{
	global $dbconn, $config;

	$file_name = "admin_user_description.php";

	$id = intval($_POST["id"]);
	$info = $_POST["info"];
	$spr = $_POST["spr"];

	/////////////////////////////////////////////////////////////////////////// save personal info
	if(intval($id) && is_array($spr)){
		$dbconn->Execute("Delete from ".DESCR_SPR_USER_TABLE." where id_user='".$id."'");
		for($i=0;$i<count($spr);$i++){
			for($j=0;$j<count($info[$i]);$j++){
				$dbconn->Execute("Insert into ".DESCR_SPR_USER_TABLE." (id_user, id_spr, id_value) values ('".$id."', '".$spr[$i]."', '".$info[$i][$j]."')");
			}
		}
	}
	$profile_percent = new Percent($id);
	$profile_percent->UpdateSection2Percent();

	echo "<form name=hide action=".$file_name." method=post>";
	echo "<input type=hidden name=id value=".$id.">";
	echo "</form>";
	echo "<script>document.hide.submit();</script>";
	exit;
}
?>