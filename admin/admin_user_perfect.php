<?php

/**
* User perfect match criterias management.
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
	case "change": ChangePerfect(); break;
	case "country": ListPerfect("country"); break;
	default: ListPerfect();
}

exit;


function ListPerfect($par="", $err="")
				{
	global $smarty, $dbconn, $config, $lang;

	if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
	$file_name = "admin_user_perfect.php";

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

	///////////////////////////////////////////// info from db
	$strSQL = "Select  id_country, id_nationality, id_language, id_height, id_weight from ".USER_MATCH_TABLE." where id_user='".$id."'";
	$rs = $dbconn->Execute($strSQL);
	$row = $rs->GetRowAssoc(false);
	$data["id_country"] = explode(",",$row["id_country"]);		// array
	$data["id_nation"] = explode(",",$row["id_nationality"]);		// array
	$data["id_lang"] = explode(",",$row["id_language"]);		// array
	$data["id_weight"] = intval($row["id_weight"]);
	$data["id_height"] = intval($row["id_height"]);
	////  country select
	$strSQL = "select distinct id, name from ".COUNTRY_SPR_TABLE." order by name ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>0) {
		$i=0;
		$c_arr = array();
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$c_arr[$i]["id"] = $row["id"];
			$c_arr[$i]["value"] = stripslashes($row["name"]);
			if(is_array($data["id_country"]) && in_array($row["id"], $data["id_country"]) && !(in_array("0", $data["id_country"])) )
			$c_arr[$i]["sel"] = 1;
			else
			$c_arr[$i]["sel"] = 0;
			$rs->MoveNext();
			$i++;
		}
		if(in_array("0", $data["id_country"])) $default["id_country"] = 1;
		$smarty->assign("country", $c_arr);
	}

	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
	
	////  nationality select
	$strSQL = "select distinct a.id, b.".$field_name." as name from ".NATION_SPR_TABLE." a left join ".REFERENCE_LANG_TABLE." b on b.table_key='".$multi_lang->TableKey(NATION_SPR_TABLE)."' and b.id_reference=a.id order by name ";
	$rs = $dbconn->Execute($strSQL);
	$i=0;
	$n_arr = array();
	while(!$rs->EOF){
		$row = $rs->GetRowAssoc(false);
		$n_arr[$i]["id"] = $row["id"];
		$n_arr[$i]["value"] = $row["name"];
		if(is_array($data["id_nation"]) && in_array($row["id"], $data["id_nation"]) && !(in_array("0", $data["id_nation"])) )
		$n_arr[$i]["sel"] = 1;
		else
		$n_arr[$i]["sel"] = 0;
		$rs->MoveNext();
		$i++;
	}
	if(in_array("0", $data["id_nation"])) $default["id_nation"] = 1;
	$smarty->assign("nation", $n_arr);

	////  language select
	$strSQL = "select distinct a.id, b.".$field_name." as name from ".LANGUAGE_SPR_TABLE." a left join ".REFERENCE_LANG_TABLE." b on b.table_key='".$multi_lang->TableKey(LANGUAGE_SPR_TABLE)."' and b.id_reference=a.id order by name ";
	$rs = $dbconn->Execute($strSQL);
	$i=0;
	$lang_sel = array();
	while(!$rs->EOF){
		$row = $rs->GetRowAssoc(false);
		$lang_sel[$i]["id"] = $row["id"];
		$lang_sel[$i]["value"] = $row["name"];
		if(is_array($data["id_lang"]) && in_array($row["id"], $data["id_lang"]) && !(in_array("0", $data["id_lang"])) ){
			$lang_sel[$i]["sel"] = 1;
		}
		$rs->MoveNext();
		$i++;
	}
	if(in_array("0", $data["id_lang"])) $default["id_lang"] = 1;
	$smarty->assign("lang_sel", $lang_sel);
	///// weight select
	$strSQL = "select distinct a.id, b.".$field_name." as name from ".WEIGHT_SPR_TABLE." a left join ".REFERENCE_LANG_TABLE." b on b.table_key='".$multi_lang->TableKey(WEIGHT_SPR_TABLE)."' and b.id_reference=a.id order by a.sorter ";
	$rs = $dbconn->Execute($strSQL);
	$i=0;
	$weight_arr = array();
	while(!$rs->EOF){
		$row = $rs->GetRowAssoc(false);
		$weight_arr[$i]["id"] = $row["id"];
		$weight_arr[$i]["value"] = $row["name"];
		if($data["id_weight"] == $row["id"])
		$weight_arr[$i]["sel"] = 1;
		else
		$weight_arr[$i]["sel"] = 0;
		$rs->MoveNext();
		$i++;
	}
	$smarty->assign("weight", $weight_arr);
	///////// height select
	$strSQL = "select distinct a.id, b.".$field_name." as name from ".HEIGHT_SPR_TABLE." a left join ".REFERENCE_LANG_TABLE." b on b.table_key='".$multi_lang->TableKey(HEIGHT_SPR_TABLE)."' and b.id_reference=a.id order by a.sorter ";
	$rs = $dbconn->Execute($strSQL);
	$i=0;
	$height_arr = array();
	while(!$rs->EOF){
		$row = $rs->GetRowAssoc(false);
		$height_arr[$i]["id"] = $row["id"];
		$height_arr[$i]["value"] = $row["name"];
		if($data["id_height"] == $row["id"])
		$height_arr[$i]["sel"] = 1;
		else
		$height_arr[$i]["sel"] = 0;
		$rs->MoveNext();
		$i++;
	}
	$smarty->assign("height", $height_arr);

	if($id){
		$rs = $dbconn->Execute("Select id_spr, id_value from ".DESCR_SPR_MATCH_TABLE." where id_user='".$id."'");
		while(!$rs->EOF){
			$id_spr = $rs->fields[0];
			$id_value = $rs->fields[1];
			if(!isset($sess_info[$id_spr]))$sess_info[$id_spr] = array();
			$sess_info[$id_spr][count($sess_info[$id_spr])+1] = $id_value;
			$rs->MoveNext();
		}
	}

	$strSQL = "select distinct a.id, b.".$field_name." as name, a.type from ".DESCR_SPR_TABLE." a left join ".REFERENCE_LANG_TABLE." b on b.table_key='".$multi_lang->TableKey(DESCR_SPR_TABLE)."' and b.id_reference=a.id order by a.sorter ";
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	while(!$rs->EOF){
		$info[$i]["id"] = $rs->fields[0];
		$info[$i]["name"] = $rs->fields[1];
		///// all selects is multiply
		$info[$i]["num"] = $i;
		$strSQL_opt = "select distinct a.id, b.".$field_name." as name from ".DESCR_SPR_VALUE_TABLE." a left join ".REFERENCE_LANG_TABLE." b on b.table_key='".$multi_lang->TableKey(DESCR_SPR_VALUE_TABLE)."' and b.id_reference=a.id where a.id_spr='".$rs->fields[0]."' order by name ";
		$rs_opt = $dbconn->Execute($strSQL_opt);
		$j = 0;
		while(!$rs_opt ->EOF){
			$info[$i]["opt_value"][$j] = $rs_opt->fields[0];
			$info[$i]["opt_name"][$j] = $rs_opt->fields[1];
			if(isset($sess_info[$rs->fields[0]]) && is_array($sess_info[$rs->fields[0]]) && in_array(0, $sess_info[$rs->fields[0]])){
				$info[$i]["sel_all"] = "1";
			}else{
				if(isset($sess_info[$rs->fields[0]]) && is_array($sess_info[$rs->fields[0]]) && in_array($rs_opt->fields[0], $sess_info[$rs->fields[0]])){
					$info[$i]["opt_sel"][$j] = $rs_opt->fields[0];
				}else{
					$info[$i]["opt_sel"][$j] = 0;
				}
			}
			$rs_opt->MoveNext();
			$j++;
		}
		$rs->MoveNext();
		$i++;
	}

	////////////////////// interests info from db
	if($id){

		$rs = $dbconn->Execute("Select id_spr, id_value from ".INTERESTS_SPR_MATCH_TABLE." where id_user='".$id."'");
		while(!$rs->EOF){
			$id_spr = $rs->fields[0];
			$id_value = $rs->fields[1];
			$sess_interests[$id_spr][$id_value]=1;
			$rs->MoveNext();
		}
	}
	//////////////////// portrait selects
	$strSQL = "select distinct a.id, b.".$field_name." as name from ".INTERESTS_SPR_TABLE." a left join ".REFERENCE_LANG_TABLE." b on b.table_key='".$multi_lang->TableKey(INTERESTS_SPR_TABLE)."' and b.id_reference=a.id";
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
		$interests[$in]["sel_1_".$row] = isset($sess_interests[$rs->fields[0]][1]) ? intval($sess_interests[$rs->fields[0]][1]) : 0;
		$interests[$in]["sel_2_".$row] = isset($sess_interests[$rs->fields[0]][2]) ? intval($sess_interests[$rs->fields[0]][2]) : 0;
		$interests[$in]["sel_3_".$row] = isset($sess_interests[$rs->fields[0]][3]) ? intval($sess_interests[$rs->fields[0]][3]) : 0;
		$rs->MoveNext();
		$i++;
	}


	$form["action"] = $file_name;
	$form["err"] = $err;

	$smarty->assign("data", $data);
	$smarty->assign("info", $info);
	if (isset($default)) {
		$smarty->assign("default", $default);
	}
	$smarty->assign("interests", $interests);
	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["users"]);
	$smarty->assign("button", $lang["button"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_user_perfect_form.tpl");
	exit;
}


function ChangePerfect()
{
	global $dbconn, $config;

	$file_name = "admin_user_perfect.php";

	$id = intval($_POST["id"]);
	$id_weight = intval($_POST["id_weight"]);
	$id_height = intval($_POST["id_height"]);

	$info = isset($_POST["info"]) ? $_POST["info"]: "";
	$spr = isset($_POST["spr"]) ? $_POST["spr"]: "";

	$int_spr = isset($_POST["int_spr"]) ? $_POST["int_spr"]:"";
	$interests = isset($_POST["interests"]) ? $_POST["interests"] : array();

	$profile_percent = new Percent($id);
	
	// save main info
	if (isset($_POST["id_country"]) && is_array($_POST["id_country"]) && count($_POST["id_country"])>0) {
		$cr = $dbconn->Execute("select  count(*) from ".COUNTRY_SPR_TABLE."");
		if(count($_POST["id_country"])>= $cr->fields[0]){
			$id_country = "0";
		}else{
			$id_country = implode(",", $_POST["id_country"]);
		}
	}else{
		$id_country = "";
	}
	/////////////////////////////
	if(isset($_POST["id_nation"]) && is_array($_POST["id_nation"]) && count($_POST["id_nation"])>0){
		$cr = $dbconn->Execute("select  count(*) from ".NATION_SPR_TABLE."");
		if(count($_POST["id_nation"])>= $cr->fields[0]){
			$id_nation = "0";
		}else{
			$id_nation = implode(",", $_POST["id_nation"]);
		}
	}else{
		$id_nation = "";
	}
	/////////////////////////////
	if(isset($_POST["id_lang"]) && is_array($_POST["id_lang"]) && count($_POST["id_lang"])>0){
		$cr = $dbconn->Execute("select  count(*) from ".LANGUAGE_SPR_TABLE."");
		if(count($_POST["id_lang"])>= $cr->fields[0]){
			$id_lang = "0";
		}else{
			$id_lang = implode(",", $_POST["id_lang"]);
		}
	}else{
		$id_lang = "";
	}

	if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
	$file_name = "myprofile.php";

	/////////////////////////////////////////////////////////////////////////// save main info
	$dbconn->Execute("update ".USER_MATCH_TABLE." set   id_country='".$id_country."', id_nationality='".$id_nation."', id_language ='".$id_lang."', id_weight= '".$id_weight."', id_height='".$id_height."' where id_user='".$id."'");
	/////////////////////////////////////////////////////////////////////////// save personal info
	if(intval($id) && is_array($spr)){
		$dbconn->Execute("Delete from ".DESCR_SPR_MATCH_TABLE." where id_user='".$id."'");
		for($i=0;$i<count($spr);$i++){
			$cr = $dbconn->Execute("select  count(*) from ".DESCR_SPR_VALUE_TABLE." where id_spr='".$spr[$i]."'");
			if ( isset($info[$i]) && (count($info[$i]) >= $cr->fields[0])) {
				$dbconn->Execute("Insert into ".DESCR_SPR_MATCH_TABLE." (id_user, id_spr, id_value) values ('".$id."', '".$spr[$i]."', '0')");
			}elseif (isset($info[$i])) {
				for($j=0;$j<count($info[$i]);$j++){
					$dbconn->Execute("Insert into ".DESCR_SPR_MATCH_TABLE." (id_user, id_spr, id_value) values ('".$id."', '".$spr[$i]."', '".$info[$i][$j]."')");
				}
			}
		}
	}
	$profile_percent->UpdateSection6Percent();
	/////////////////////////////////////////////////////////////////////////// save interests info
	if(intval($id) && is_array($int_spr)){
		$dbconn->Execute("Delete from ".INTERESTS_SPR_MATCH_TABLE." where id_user='".$id."'");
		for($i=0;$i<count($int_spr);$i++){
			for($j=1; $j<=3;$j++){
				if(isset($interests[$i][$j]) && intval($interests[$i][$j]))$dbconn->Execute("Insert into ".INTERESTS_SPR_MATCH_TABLE." (id_user, id_spr, id_value) values ('".$id."', '".$int_spr[$i]."', '".$j."')");
			}
		}
	}
	$profile_percent->UpdateSection7Percent();

	echo "<form name=hide action=".$file_name." method=post>";
	echo "<input type=hidden name=id value=".$id.">";
	echo "</form>";
	echo "<script>document.hide.submit();</script>";
	exit;
}
?>