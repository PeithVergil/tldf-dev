<?php
/**
* Countries list editing
*
* @package DatingPro
* @subpackage Admin Mode
**/
include '../include/config.php';
include_once '../common.php';
include '../include/config_admin.php';
include '../include/functions_auth.php';
include '../include/functions_admin.php';
include '../include/class.lang.php';

$auth = auth_user();
login_check($auth);
IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "country");

$sel = isset($_REQUEST["sel"]) ? $_REQUEST["sel"] : "";
$id_country = isset($_REQUEST["id_country"]) ? $_REQUEST["id_country"] : "";
$id_region = isset($_REQUEST["id_region"]) ? $_REQUEST["id_region"] : "";

switch($sel){
	case "add": AddCountry(); break;
	case "del": DelCountry(); break;
	case "rename_country": renameCountry(); break;
	case "edit": ListRegion($id_country); break;
	case "addr": AddRegion($id_country); break;
	case "delr": DelRegion($id_country); break;
	case "rename_region": renameRegion(); break;
	case "editr": ListCity($id_country, $id_region); break;
	case "addc": AddCity($id_country, $id_region); break;
	case "delc": DelCity($id_country, $id_region); break;
	case "editc": EditFormCity($id_country, $id_region); break;
	case "changec": ChangeCity($id_country, $id_region); break;
	default: ListCountry();
}

function ListCountry($err="", $name=""){
	global $smarty, $dbconn, $config, $config_admin, $page, $lang;

	if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
	$file_name = "admin_countries.php";

	AdminMainMenu($lang["country"]);

	$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;

	$strSQL = " SELECT COUNT(id) FROM ".COUNTRY_SPR_TABLE." ";
	$rs = $dbconn->Execute($strSQL);
	$num_records = $rs->fields[0];

	$lim_min = ($page-1)*$config_admin["country_numpage"];
	$lim_max = $config_admin["country_numpage"];
	$limit_str = " limit ".$lim_min.", ".$lim_max;

	$strSQL = "select distinct id, name from ".COUNTRY_SPR_TABLE." order by name ".$limit_str;
	$rs = $dbconn->Execute($strSQL);

	$i = 0;
	$spr_arr = array();
	if($rs->RowCount()>0){
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$spr_arr[$i]["number"] = ($page-1)*$config_admin["country_numpage"]+($i+1);
			$spr_arr[$i]["id"] = $row["id"];
			$spr_arr[$i]["name"] = stripslashes($row["name"]);
			$spr_arr[$i]["deletelink"] = $file_name."?sel=del&page=".$page."&id=".$rs->fields[0];
			$spr_arr[$i]["editlink"] = $file_name."?sel=edit&id_country=".$rs->fields[0];
			$rs->MoveNext();
			$i++;
		}
		$param = $file_name."?";
		$smarty->assign("links", GetLinkStr($num_records,$page,$param,$config_admin["country_numpage"]));
		$smarty->assign("countries", $spr_arr);
	}

	if(!$err){
		$name = "";
	}

	$form["hiddens"] = "<input type=hidden name=sel value=add>";
	$form["hiddens"] .= "<input type=hidden name=page value=".$page.">";

	$form["action"] = $file_name;
	$form["err"] = $err;
	$form["confirm"] = $lang["confirm"]["countries"];
	$smarty->assign("name", $name);
	$smarty->assign("form", $form);
	$smarty->assign("button", $lang["button"]);
	
	$smarty->assign("header", $lang["country"]);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_country_table.tpl");
	exit;
}

function AddCountry()
{
	global $dbconn, $page, $lang;
	$name = isset($_REQUEST["name"]) ? $_REQUEST["name"] : "";
	$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;
	// not used
	#$id_country = isset($_REQUEST["id_country"]) ? intval($_REQUEST["id_country"]) : "";

	$strSQL = "SELECT COUNT(id) FROM ".COUNTRY_SPR_TABLE." WHERE name = '".Rep_Slashes($name)."'";
	$rs = $dbconn->Execute($strSQL);
	if($rs->fields[0]>0){
		$err = $lang["err"]["exists_country"];
		ListCountry($err, Rep_Slashes($name)); return;
	}

	if(strlen($name)>0){
		$strSQL = "insert into ".COUNTRY_SPR_TABLE." (name) values ('".Rep_Slashes($name)."')";
		$dbconn->Execute($strSQL);
	}

	ListCountry();
	return;
}

function DelCountry()
{
	global $dbconn;
	$id = isset($_REQUEST["id"]) ? intval($_REQUEST["id"]) : "";
	if($id == ""){ ListCountry(); return;}

	$strSQL = "DELETE FROM ".COUNTRY_SPR_TABLE." WHERE id='".$id."'";
	$dbconn->Execute($strSQL);

	$strSQL = "DELETE FROM ".REGION_SPR_TABLE." WHERE id_country='".$id."'";
	$dbconn->Execute($strSQL);

	$strSQL = "DELETE FROM ".CITY_SPR_TABLE." WHERE id_country='".$id."'";
	$dbconn->Execute($strSQL);

	ListCountry(); return;
}

function renameCountry(){
	global $dbconn, $lang;
	$id = intval($_REQUEST["id"]);
	$value = addslashes($_REQUEST["value"]);
	if (!$id)	return false;
	if ($value == ''){
		echo $lang["err"]["wrong_value"]."<input id='saved".$id."' type='hidden' value='0' />";
		return false;
	}
	
	$strSQL = "UPDATE ".COUNTRY_SPR_TABLE." SET name='".$value."' WHERE id='".$id."'";
	$dbconn->Execute($strSQL);
	if (!$dbconn->ErrorNo()) echo $lang["country"]["saved"]."<input id='saved".$id."' type='hidden' value='1' />";
	else echo $lang["country"]["system_error"]."<input id='saved".$id."' type='hidden' value='0' />";
}


function ListRegion($id_country, $err="", $name="")
{
	global $smarty, $dbconn, $config, $config_admin, $page, $lang;

	if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
	$file_name = "admin_countries.php";

	AdminMainMenu($lang["country"]);

	$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;

	$rs = $dbconn->Execute("Select name from ".COUNTRY_SPR_TABLE." where id = '".$id_country."'");
	$country_name = $rs->fields[0];

	$strSQL = "SELECT COUNT(id) FROM ".REGION_SPR_TABLE." where id_country='".$id_country."'";
	$rs = $dbconn->Execute($strSQL);
	$num_records = $rs->fields[0];

	$lim_min = ($page-1)*$config_admin["region_numpage"];
	$lim_max = $config_admin["region_numpage"];
	$limit_str = " limit ".$lim_min.", ".$lim_max;

	$strSQL = "SELECT DISTINCT id, name FROM ".REGION_SPR_TABLE." where id_country='".$id_country."' order by id ".$limit_str;
	$rs = $dbconn->Execute($strSQL);

	$i = 0;
	$spr_arr = array();
	if($rs->RowCount()>0){
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$spr_arr[$i]["number"] = ($page-1)*$config_admin["region_numpage"]+($i+1);
			$spr_arr[$i]["id"] = $row["id"];
			$spr_arr[$i]["name"] = stripslashes($row["name"]);
			$spr_arr[$i]["deletelink"] = $file_name."?sel=delr&page=".$page."&id_country=".$id_country."&id=".$rs->fields[0];
			$spr_arr[$i]["editlink"] = $file_name."?sel=editr&id_country=".$id_country."&id_region=".$rs->fields[0];
			$rs->MoveNext();
			$i++;
		}
		$param = $file_name."?sel=edit&id_country=".$id_country."&";
		$smarty->assign("links", GetLinkStr($num_records,$page,$param,$config_admin["region_numpage"]));
		$smarty->assign("regions", $spr_arr);
	}
	///	form
	if(!$err){
		$name = "";
	}

	$smarty->assign("country_name", $country_name);
	$smarty->assign("back_link", $file_name);

	$form["hiddens"] = "<input type=hidden name=sel value=addr>";
	$form["hiddens"] .= "<input type=hidden name=page value=".$page.">";
	$form["hiddens"] .= "<input type=hidden name=id_country value=".$id_country.">";

	$form["action"] = $file_name;
	$form["err"] = $err;
	$form["confirm"] = $lang["confirm"]["regions"];

	$smarty->assign("name", $name);

	$smarty->assign("form", $form);
	$smarty->assign("button", $lang["button"]);


	$smarty->assign("header", $lang["country"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_country_region_table.tpl");
	exit;
}

function AddRegion($id_country)
{
	global $dbconn, $page, $lang;
	$name = isset($_REQUEST["name"]) ? $_REQUEST["name"] : "";
	$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;
	
	$strSQL = "SELECT COUNT(id) FROM ".REGION_SPR_TABLE." WHERE id_country='".$id_country."' AND name = '".Rep_Slashes($name)."'";
	$rs = $dbconn->Execute($strSQL);
	if($rs->fields[0]>0){
		$err = $lang["err"]["exists_region"];
		ListRegion($id_country, $err, Rep_Slashes($name)); return;
	}
	if(strlen($name)>0){
		$strSQL = "INSERT INTO ".REGION_SPR_TABLE." (id_country, name) VALUES ('".$id_country."', '".Rep_Slashes($name)."')";
		$dbconn->Execute($strSQL);
	}
	ListRegion($id_country); return;
}

function DelRegion($id_country)
{
	global $dbconn;

	$id = isset($_REQUEST["id"]) ? intval($_REQUEST["id"]) : "";
	if($id == ""){ ListRegion($id_country); return;}

	$strSQL = "delete from ".REGION_SPR_TABLE."  where id='".$id."'";
	$dbconn->Execute($strSQL);

	$strSQL = "delete from ".CITY_SPR_TABLE."  where id_region='".$id."'";
	$dbconn->Execute($strSQL);

	ListRegion($id_country); return;
}

function renameRegion(){
	global $dbconn, $lang;
	$id = intval($_REQUEST["id"]);
	$value = addslashes($_REQUEST["value"]);
	if (!$id)	return false;
	if ($value == ''){
		echo $lang["err"]["wrong_value"]."<input id='saved".$id."' type='hidden' value='0' />";
		return false;
	}
	
	$strSQL = "UPDATE ".REGION_SPR_TABLE." SET name='".$value."' WHERE id='".$id."'";
	$dbconn->Execute($strSQL);
	if (!$dbconn->ErrorNo()) echo $lang["country"]["saved"]."<input id='saved".$id."' type='hidden' value='1' />";
	else echo $lang["country"]["system_error"]."<input id='saved".$id."' type='hidden' value='0' />";
}

function ListCity($id_country, $id_region, $err="", $name="")
{
	global $smarty, $dbconn, $config, $config_admin, $lang;

	if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
	$file_name = "admin_countries.php";

	AdminMainMenu($lang["country"]);

	$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;

	///// table
	if(!$id_country){ ListCountry(); return;}
	if(!$id_region){ ListRegion($id_country); return;}

	$rs = $dbconn->Execute("Select name from ".COUNTRY_SPR_TABLE." where id = '".$id_country."'");
	$country_name = $rs->fields[0];
	$rs = $dbconn->Execute("Select name from ".REGION_SPR_TABLE." where id = '".$id_region."'");
	$region_name = $rs->fields[0];

	$strSQL = "SELECT COUNT(id) FROM ".CITY_SPR_TABLE." where id_country='".$id_country."' and id_region='".$id_region."'";
	$rs = $dbconn->Execute($strSQL);
	$num_records = $rs->fields[0];

	$lim_min = ($page-1)*$config_admin["city_numpage"];
	$lim_max = $config_admin["city_numpage"];
	$limit_str = " limit ".$lim_min.", ".$lim_max;

	$strSQL = "select distinct id, name, zip_code from ".CITY_SPR_TABLE." where id_country='".$id_country."' and id_region='".$id_region."' order by id ".$limit_str;
	$rs = $dbconn->Execute($strSQL);

	$i = 0;
	$spr_arr = array();
	if($rs->RowCount()>0){
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$spr_arr[$i]["number"] = ($page-1)*$config_admin["city_numpage"]+($i+1);
			$spr_arr[$i]["id"] = $row["id"];
			$spr_arr[$i]["name"] = stripslashes($row["name"]);
			$spr_arr[$i]["zip_code"] = $row["zip_code"];
			$spr_arr[$i]["edit_link"] = $file_name."?sel=editc&page=".$page."&id=".$rs->fields[0]."&id_country=".$id_country."&id_region=".$id_region;
			$spr_arr[$i]["deletelink"] = $file_name."?sel=delc&page=".$page."&id=".$rs->fields[0]."&id_country=".$id_country."&id_region=".$id_region;
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign("cities", $spr_arr);
	}
	$param = $file_name."?sel=editr&id_country=".$id_country."&id_region=".$id_region."&";
	$smarty->assign("links", GetLinkStr($num_records,$page,$param,$config_admin["city_numpage"]));
	$smarty->assign("country_name", $country_name);
	$smarty->assign("region_name", $region_name);
	$smarty->assign("back_link", $file_name."?sel=edit&id_country=".$id_country);

	$smarty->assign("header", $lang["country"]);

	///	form
	if(!$err){
		$name = "";
	}

	$form["hiddens"] = "<input type=hidden name=sel value=addc>";
	$form["hiddens"] .= "<input type=hidden name=e value=1>";
	$form["hiddens"] .= "<input type=hidden name=page value=".$page.">";
	$form["hiddens"] .= "<input type=hidden name=id_country value=".$id_country.">";
	$form["hiddens"] .= "<input type=hidden name=id_region value=".$id_region.">";

	$form["action"] = $file_name;
	$form["err"] = $err;
	$form["confirm"] = $lang["confirm"]["cities"];

	$smarty->assign("name", $name);

	$smarty->assign("form", $form);
	$smarty->assign("button", $lang["button"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_country_region_form.tpl");
	exit;
}
function EditFormCity($id_country, $id_region, $err="")
				{
	global $smarty, $dbconn, $config, $lang;

	if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
	$file_name = "admin_countries.php";

	AdminMainMenu($lang["country"]);

	$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;

	if($err){
		$form["err"] = $err;
		$data["name"] = isset($_REQUEST["name"]) ? $_REQUEST["name"] : "";
		$data["zip_code"] = isset($_REQUEST["zip_code"]) ? $_REQUEST["zip_code"] : "";
	}
	$id = isset($_REQUEST["id"]) ? intval($_REQUEST["id"]) : null; 

	if(!$err){
		if(!$id){ ListCity($id_country, $id_region); return;}
		$strSQL = "select id, name, zip_code from ".CITY_SPR_TABLE." where id='".$id."'";
		$rs = $dbconn->Execute($strSQL);
		$row = $rs->GetRowAssoc(false);
		$data["name"] = $row["name"];
		$data["zip_code"] = $row["zip_code"];
	}

	$form["hiddens"] = "<input type=hidden name=sel value=changec>";
	$form["hiddens"] .= "<input type=hidden name=e value=1>";
	$form["hiddens"] .= "<input type=hidden name=page value=\"".$page."\">";
	$form["hiddens"] .= "<input type=hidden name=id_country value=\"".$id_country."\">";
	$form["hiddens"] .= "<input type=hidden name=id_region value=\"".$id_region."\">";
	$form["hiddens"] .= "<input type=hidden name=id value=\"".$id."\">";


	$form["back"] = $file_name."?sel=editr&id_country=".$id_country."&id_region=".$id_region."&page=".$page;
	$form["action"] = $file_name;

	$smarty->assign("data", $data);
	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["country"]);
	$smarty->assign("err", $lang["err"]);
	$smarty->assign("button", $lang["button"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_country_cities_form.tpl");
	exit;
}

function AddCity($id_country="", $id_region="")
{
	global $dbconn, $lang, $e;
	$name = isset($_POST["name"]) ? $_POST["name"] : "";
	$zip_code = isset($_POST["zip_code"]) ? $_POST["zip_code"] : "";
	$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;

	if(!$id_country){ListCountry(); return;}
	if(!$id_region){ListRegion($id_country); return;}

	$strSQL = "SELECT id FROM ".CITY_SPR_TABLE." WHERE id_country='".intval($id_country)."'";
	$rs = $dbconn->Execute($strSQL);
	$cities = array();
	while(!$rs->EOF){
		$cities[] = $rs->fields[0];
		$rs->MoveNext();
	}
	if(is_array($cities)) $cities_str = implode(", ", $cities);

	$strSQL = "SELECT COUNT(id) FROM ".CITY_SPR_TABLE." WHERE name = '".Rep_Slashes($name)."' and id_country='".$id_country."' and id_region='".$id_region."'";
	$rs= $dbconn->Execute($strSQL);
	if($rs->fields[0]>0){
		$err = $lang["err"]["exists_city"];
		ListCity($id_country, $id_region, $err); return;
	}

	$settings = GetSiteSettings(array('zip_letters', 'zip_count'));

	if(strlen($zip_code)>0){
		$zip_arr = array();
		$zip_arr = explode(";", $zip_code);
		for($i=0;$i<count($zip_arr);$i++){
			if(!$settings["zip_letters"]){
				$zip_arr[$i] = intval(substr($zip_arr[$i],0,$settings["zip_count"]));
			}else{
				$zip_arr[$i] = substr($zip_arr[$i],0,$settings["zip_count"]);
			}
		}
		$zip_unique_arr = array_unique($zip_arr);
		$zip_code = implode(";", $zip_unique_arr);
	}

	if(strlen($name)>0){
		$strSQL = "INSERT INTO ".CITY_SPR_TABLE." (id_country, name, zip_code, id_region) values ('".$id_country."', '".Rep_Slashes($name)."', '".$zip_code."', '".$id_region."')";
		$dbconn->Execute($strSQL);
	}

	ListCity($id_country, $id_region); return;
}
///////  change city //////////////////////////////////////////////////////////////
function ChangeCity($id_country="", $id_region="")
{
	global $dbconn, $lang, $e;
	$name = isset($_POST["name"]) ? $_POST["name"] : "";
	$id = isset($_REQUEST["id"]) ? intval($_REQUEST["id"]) : "";
	$zip_code = isset($_POST["zip_code"]) ? $_POST["zip_code"] : 0;
	$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;
	
	if(!$id_country){ListCountry(); return;}
	if(!$id_region){ListRegion($id_country); return;}

	if(strlen($zip_code)>0){
		$zip_arr = array();
		$zip_arr = explode(";", $zip_code);

		$settings = GetSiteSettings(array('zip_letters', 'zip_count'));

		for($i=0;$i<count($zip_arr);$i++){
			if(!$settings["zip_letters"]){
				$zip_arr[$i] = intval(substr($zip_arr[$i],0,$settings["zip_count"]));
			}else{
				$zip_arr[$i] = substr($zip_arr[$i],0,$settings["zip_count"]);
			}
		}
		$zip_unique_arr = array_unique($zip_arr);
		$zip_code = implode(";", $zip_unique_arr);
	}

	$strSQL = "SELECT COUNT(id) FROM ".CITY_SPR_TABLE." where name = '".Rep_Slashes($name)."' and id_country='".$id_country."' and id_country='".$id_region."' and id<>'".$id."'";
	$rs= $dbconn->Execute($strSQL);
	if($rs->fields[0]>0){
		$err = $lang["err"]["exists_city"];
		EditFormCity($id_country, $id_region, $err, Rep_Slashes($name), $zip_code);
		return;
	}

	if(strlen($name)>0){
		$strSQL = "Update ".CITY_SPR_TABLE." set id_country='".$id_country."', id_region='".$id_region."', name='".Rep_Slashes($name)."', zip_code='".$zip_code."' where id='".$id."'";
		$dbconn->Execute($strSQL);
	}

	ListCity($id_country, $id_region);
	return;
}

function DelCity($id_country="", $id_region="")
{
	global $dbconn, $e;
	$id = isset($_REQUEST["id"]) ? intval($_REQUEST["id"]) : "";

	if ($id == ""){ ListCity($id_country, $id_region); return;}

	$strSQL = "delete from ".CITY_SPR_TABLE."  where id='".$id."'";
	$dbconn->Execute($strSQL);

	ListCity($id_country, $id_region); return;
}

?>