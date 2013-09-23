<?php

/**
* Site "User personality" reference management.
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

switch($sel){
	case "change": ChangePersonality(); break;
	default: ListPersonality();
}

exit;

function ListPersonality($err="")
{
	global $smarty, $dbconn, $config, $lang;

	$file_name = "admin_user_personality.php";

	AdminMainMenu($lang["users"], "1");
	$id = intval($_REQUEST["id"]);
	if($id>0){
		$strSQL = "select concat(fname, ' ', sname, ' (',login,')'), root_user from ".USERS_TABLE." where id='".$id."'";
		$rs = $dbconn->Execute($strSQL);
		$data["username"] = $rs->fields[0];
		$data["id"] = $id;
		$data["root"] = $rs->fields[1];
	}else{
		$data["username"] = $lang["users"]["default_user"];
	}

	/////////////// personal info from db
	if($id){
		$rs = $dbconn->Execute("Select id_spr, id_value from ".PERSON_SPR_USER_TABLE." where id_user='".$id."'");
		while(!$rs->EOF){
			$id_spr = $rs->fields[0];
			$id_value = $rs->fields[1];
			$sess_person[$id_spr] = array();
			$sess_person[$id_spr][count($sess_person[$id_spr])+1] = $id_value;
			$rs->MoveNext();
		}
	}
	
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	// personal selects
	$table_key = $multi_lang->TableKey(PERSON_SPR_TABLE);
	$table_key_val = $multi_lang->TableKey(PERSON_SPR_VALUE_TABLE);

	$strSQL = "select distinct a.id, b.".$field_name." as name from ".PERSON_SPR_TABLE." a left join ".REFERENCE_LANG_TABLE." b on b.table_key='".$table_key."' and b.id_reference=a.id";
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	while(!$rs->EOF){
		$person[$i]["id"] = $rs->fields[0];
		$person[$i]["name"] = $rs->fields[1];

		$strSQL_opt = "select distinct a.id, b.".$field_name." as name from ".PERSON_SPR_VALUE_TABLE." a left join ".REFERENCE_LANG_TABLE." b on b.table_key='".$table_key_val."' and b.id_reference=a.id where a.id_spr='".$rs->fields[0]."' order by name ";

		$rs_opt = $dbconn->Execute($strSQL_opt);
		$j = 0;
		while(!$rs_opt->EOF){
			$person[$i]["opt_value"][$j] = $rs_opt->fields[0];
			$person[$i]["opt_name"][$j] = $rs_opt->fields[1];
			if(isset($sess_person[$rs->fields[0]]) && is_array($sess_person[$rs->fields[0]]) && in_array(0, $sess_person[$rs->fields[0]])){
				$person[$i]["sel_all"] = "1";
			}else{
				if(isset($sess_person[$rs->fields[0]]) && is_array($sess_person[$rs->fields[0]]) && in_array($rs_opt->fields[0], $sess_person[$rs->fields[0]])){
					$person[$i]["opt_sel"][$j] = $rs_opt->fields[0];
				}else{
					$person[$i]["opt_sel"][$j] = 0;
				}
			}
			$rs_opt->MoveNext();
			$j++;
		}
		$rs->MoveNext();
		$i++;
	}
	///////////////////// portrait info from db
	if($id){
		$rs = $dbconn->Execute("Select id_spr, id_value from ".PORTRAIT_SPR_USER_TABLE." where id_user='".$id."'");
		while(!$rs->EOF){
			$id_spr = $rs->fields[0];
			$id_value = $rs->fields[1];
			$sess_portrait[$id_spr] = array();
			$sess_portrait[$id_spr][count($sess_portrait[$id_spr])+1] = $id_value;
			$rs->MoveNext();
		}
	}
	
	// portrait selects
	$table_key = $multi_lang->TableKey(PORTRAIT_SPR_TABLE);
	$table_key_val = $multi_lang->TableKey(PORTRAIT_SPR_VALUE_TABLE);

	$strSQL = "select distinct a.id, b.".$field_name." as name from ".PORTRAIT_SPR_TABLE." a left join ".REFERENCE_LANG_TABLE." b on b.table_key='".$table_key."' and b.id_reference=a.id";
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	while(!$rs->EOF){
		if($i%2 == 0)
		$row = 1;
		else
		$row = 2;
		$in = intval($i/2);
		$portrait[$in]["id_".$row] = $rs->fields[0];
		$portrait[$in]["name_".$row] = $rs->fields[1];
		$portrait[$in]["num_".$row] = $i;

		$strSQL_opt = "select distinct a.id, b.".$field_name." as name from ".PORTRAIT_SPR_VALUE_TABLE." a left join ".REFERENCE_LANG_TABLE." b on b.table_key='".$table_key_val."' and b.id_reference=a.id where a.id_spr='".$rs->fields[0]."' order by name ";

		$rs_opt = $dbconn->Execute($strSQL_opt);
		$j = 0;
		while(!$rs_opt->EOF){
			$portrait[$in]["opt_value_".$row][$j] = $rs_opt->fields[0];
			$portrait[$in]["opt_name_".$row][$j] = $rs_opt->fields[1];
			if(isset($sess_portrait[$rs->fields[0]]) && is_array($sess_portrait[$rs->fields[0]]) && in_array(0, $sess_portrait[$rs->fields[0]])){
				$portrait[$in]["sel_all_".$row] = "1";
			}else{
				if(isset($sess_portrait[$rs->fields[0]]) && is_array($sess_portrait[$rs->fields[0]]) && in_array($rs_opt->fields[0], $sess_portrait[$rs->fields[0]])){
					$portrait[$in]["opt_sel_".$row][$j] = $rs_opt->fields[0];
				}else{
					$portrait[$in]["opt_sel_".$row][$j] = 0;
				}
			}
			$rs_opt->MoveNext();
			$j++;
		}
		$rs->MoveNext();
		$i++;
	}
	////////////////////// interests info from bd
	if($id){
		$rs = $dbconn->Execute("Select id_spr, id_value from ".INTERESTS_SPR_USER_TABLE." where id_user='".$id."'");
		while(!$rs->EOF){
			$id_spr = $rs->fields[0];
			$id_value = $rs->fields[1];
			$sess_interests[$id_spr] = $id_value;
			$rs->MoveNext();
		}
	}
	
	// interests selects
	$table_key = $multi_lang->TableKey(INTERESTS_SPR_TABLE);
	$strSQL = "select distinct a.id, b.".$field_name." as name from ".INTERESTS_SPR_TABLE." a left join ".REFERENCE_LANG_TABLE." b on b.table_key='".$table_key."' and b.id_reference=a.id";
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	while(!$rs->EOF){
		if($i%3 == 0)
		$row = 1;
		elseif($i%3 == 1)
		$row = 2;
		else
		$row = 3;
		$in = intval($i/3);

		$interests[$in]["id_".$row] = $rs->fields[0];
		$interests[$in]["name_".$row] = $rs->fields[1];
		$interests[$in]["num_".$row] = $i;
		$interests[$in]["sel_".$row] = isset($sess_interests[$rs->fields[0]]) ? $sess_interests[$rs->fields[0]] : 0;
		$rs->MoveNext();
		$i++;
	}



	$form["action"] = $file_name;
	$form["err"] = $err;

	$smarty->assign("data", $data);
	$smarty->assign("personal", $person);
	$smarty->assign("portrait", $portrait);
	$smarty->assign("interests", $interests);
	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["users"]);
	$smarty->assign("button", $lang["button"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_user_personal_form.tpl");
	exit;
}

function ChangePersonality()
{
	global $dbconn, $config;

	$file_name = "admin_user_personality.php";

	$id = intval($_POST["id"]);
	$p_spr = $_POST["p_spr"];
	$port_spr = $_POST["port_spr"];
	$int_spr = $_POST["int_spr"];
	$personal = $_POST["personal"];
	$portrait = $_POST["portrait"];
	$interests = isset($_POST["interests"]) ? $_POST["interests"] : array();

	$profile_percent = new Percent($id);
	
	// save personal info
	if(intval($id) && is_array($p_spr)){
		$dbconn->Execute("Delete from ".PERSON_SPR_USER_TABLE." where id_user='".$id."'");
		for($i=0;$i<count($p_spr);$i++){
			for($j=0;$j<count($personal[$i]);$j++){
				$dbconn->Execute("Insert into ".PERSON_SPR_USER_TABLE." (id_user, id_spr, id_value) values ('".$id."', '".$p_spr[$i]."', '".$personal[$i][$j]."')");
			}
		}
	}
	$profile_percent->UpdateSection3Percent();
	/////////////////////////////////////////////////////////////////////////// save portrait info
	if(intval($id) && is_array($port_spr)){
		$dbconn->Execute("Delete from ".PORTRAIT_SPR_USER_TABLE." where id_user='".$id."'");
		for($i=0;$i<count($port_spr);$i++){
			for($j=0;$j<count($portrait[$i]);$j++){
				$dbconn->Execute("Insert into ".PORTRAIT_SPR_USER_TABLE." (id_user, id_spr, id_value) values ('".$id."', '".$port_spr[$i]."', '".$portrait[$i][$j]."')");
			}
		}
	}
	$profile_percent->UpdateSection4Percent();
	/////////////////////////////////////////////////////////////////////////// save portrait info
	if(intval($id) && is_array($int_spr)){
		$dbconn->Execute("Delete from ".INTERESTS_SPR_USER_TABLE." where id_user='".$id."'");
		for($i=0;$i<count($int_spr);$i++){
			if (isset($interests[$i])) {
				$dbconn->Execute("Insert into ".INTERESTS_SPR_USER_TABLE." (id_user, id_spr, id_value) values ('".$id."', '".$int_spr[$i]."', '".$interests[$i]."')");
			}
		}
	}
	$profile_percent->UpdateSection5Percent();

	/*VP commented
	echo "<form name=hide action=".$file_name." method=post>";
	echo "<input type=hidden name=id value=".$id.">";
	echo "</form>";
	echo "<script>document.hide.submit();</script>";
	*/
	ListPersonality('Record saved sussfully.');
	exit;
}
?>