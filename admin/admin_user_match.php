<?php

/**
* Site user administration (search user matches)
*
* @package DatingPro
* @subpackage Admin Mode
**/

include "../include/config.php";
include_once "../common.php";
include "../include/config_admin.php";
include "../include/functions_auth.php";
include "../include/functions_admin.php";
include "../include/class.phpmailer.php";
include "../include/functions_mail.php";
include "../include/functions_newsletter.php";
include "../include/class.lang.php";
include "../include/class.percent.php";

include "../include/config_index.php";
include "../include/functions_users.php";
include '../include/functions_mm.php';

$auth = auth_user();
login_check($auth);
//IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "users");

$sel = isset($_REQUEST['sel']) ? $_REQUEST['sel'] : '';

$config_admin['gender_membership'] = GetSiteSettings('use_gender_membership');

switch($sel)
{
	case "search":	SearchList(); break;
	default:		SearchForm();
}

function SearchForm($par="", $id_err="")
{
	global $smarty, $dbconn, $config, $lang, $sel;
	
	$file_name = "admin_user_match.php";
	
	AdminMainMenu($lang["user_match"]);
	
	$page = (isset($_REQUEST['page']) && intval($_REQUEST['page'])>0) ? intval($_REQUEST['page']) : 1;
	
	/*
	$letter = (isset($_REQUEST["letter"]) && intval($_REQUEST["letter"])>0) ? intval($_REQUEST["letter"]) : "*";
	$sorter = (isset($_REQUEST['sorter']) && intval($_REQUEST['sorter'])>0) ? intval($_REQUEST['sorter']) : 5;
	$s_type = (isset($_REQUEST['s_type']) && intval($_REQUEST['s_type'])>0) ? intval($_REQUEST['s_type']) : 1;
	$order = (isset($_REQUEST['order']) && intval($_REQUEST['order'])>0) ? intval($_REQUEST['order']) : 1;
	
	$search = (isset($_REQUEST['search'])) ? strval($_REQUEST['search']) : "";
	$s_stat = (isset($_REQUEST['s_stat'])) ? strval($_REQUEST['s_stat']) : "";
	$group = (isset($_REQUEST['group'])) ? strval($_REQUEST['group']) : "";
	$s_gender = (isset($_REQUEST['s_gender'])) ? strval($_REQUEST['s_gender']) : "";
	
	$sel = isset($_REQUEST["sel"]) ? $_REQUEST["sel"] : "";
	$pre_sel = isset($_REQUEST["pre_sel"]) ? $_REQUEST["pre_sel"] : $sel;
	$s_pending = isset($_REQUEST["s_pending"]) ? $_REQUEST["s_pending"] : "";
	$plat_applied = isset($_REQUEST["plat_applied"]) ? $_REQUEST["plat_applied"] : "";
	
	$search_str = "";
	*/
	
	$id_user = isset($_REQUEST["uid"]) ? $_REQUEST["uid"] : "";
	
	if($id_user)
	{
		$strSQL = "SELECT fname, sname, mm_nickname, gender, icon_path, mm_marital_status, date_birthday FROM ".USERS_TABLE." WHERE id='".$id_user."'";
		$rs = $dbconn->Execute($strSQL);
		$row = $rs->GetRowAssoc(false);
		
		$active_user['fname']			 = stripslashes($row['fname']);
		$active_user['sname']			 = stripslashes($row['sname']);
		$active_user['mm_nickname']		 = stripslashes($row['mm_nickname']);
		$active_user['gender']			 = $row['gender'];
		$active_user['icon_path']		 = $row['icon_path'];
		$active_user['mm_marital_status']= $row['mm_marital_status'];
		$active_user['age']				 = AgeFromBDate($row["date_birthday"]);
		
		$smarty->assign("active_user", $active_user);
		
		$settings = GetSiteSettings(array('max_age_limit', 'min_age_limit', 'icons_folder', 'zip_letters', 'zip_count',"use_shoutbox_feature"));
	
		unset($_SESSION["id_arr"]);
		unset($_SESSION["search_pars"]);
	
		/*
		if ($customsearch_type=='save')
		{
			$data = $_GET;
			$par = "country";
		}
		*/
		
		$strSQL = "select max(date_birthday), min(date_birthday) from ".USERS_TABLE."";
		$rs = $dbconn->Execute($strSQL);
		$max_age = AgeFromBDate($rs->fields[1]);
		$max_age = min($max_age, $settings["max_age_limit"]);
		$min_age = AgeFromBDate($rs->fields[0]);
		$min_age = max($min_age, $settings["min_age_limit"]);
		$max_age_arr = range(intval($max_age), intval($min_age));
		$smarty->assign("age_max", $max_age_arr);
		$min_age_arr = range(intval($min_age), intval($max_age));
		$smarty->assign("age_min", $min_age_arr);
	
		
		$default = array();
		$smarty->assign("default", $default);
		
		// gender select
		if (!isset($data["gender_2"])) {
			$data["gender_2"] = 1;
		}
		
		$gender_arr = array();
		$gender_arr[0]["id"] = '1';
		$gender_arr[0]["name_search"] = $lang["gender_search"]["1"];
		$gender_arr[0]["sel_search"] = intval($data["gender_2"]) == 1 ? 1 : 0;
		$gender_arr[1]["id"] = '2';
		$gender_arr[1]["name_search"] = $lang["gender_search"]["2"];
		$gender_arr[1]["sel_search"] = intval($data["gender_2"]) == 2 ? 1 : 0;
		
		$smarty->assign("gender", $gender_arr);
		
		///////////////////////////////////////////// info from db
		$strSQL = "Select  id_country, id_nationality, id_language, id_height, id_weight, age_min, age_max from ".USER_MATCH_TABLE." where id_user='".$id_user."'";
		$rs = $dbconn->Execute($strSQL);
		$row = $rs->GetRowAssoc(false);
		$data["id_country"]	= explode(",",$row["id_country"]);		// array
		$data["id_nation"]	= explode(",",$row["id_nationality"]);		// array
		$data["id_lang"]	= explode(",",$row["id_language"]);		// array
		$data["id_weight"]	= intval($row["id_weight"]);
		$data["id_height"]	= intval($row["id_height"]);
		$data["age_min"]	= intval($row["age_min"]);
		$data["age_max"]	= intval($row["age_max"]);
		
		$multi_lang = new MultiLang();
		$field_name = $multi_lang->DefaultFieldName();
		
		///// weight select
		$strSQL =
			"select distinct a.id, b.".$field_name." as name
			   from ".WEIGHT_SPR_TABLE." a
		  left join ".REFERENCE_LANG_TABLE." b on b.table_key='".$multi_lang->TableKey(WEIGHT_SPR_TABLE)."' and b.id_reference=a.id
		   order by a.sorter";
		$rs = $dbconn->Execute($strSQL);
		$i = 0;
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
		$strSQL =
			"select distinct a.id, b.".$field_name." as name
			   from ".HEIGHT_SPR_TABLE." a
		  left join ".REFERENCE_LANG_TABLE." b on b.table_key='".$multi_lang->TableKey(HEIGHT_SPR_TABLE)."' and b.id_reference=a.id
		   order by a.sorter";
		$rs = $dbconn->Execute($strSQL);
		$i = 0;
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
		
		////  country select
		$strSQL = "select distinct id, name from ".COUNTRY_SPR_TABLE." order by name ";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0]>0) {
			$i = 0;
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
	
		////  nationality select
		$strSQL =
			"select distinct a.id, b.".$field_name." as name
			   from ".NATION_SPR_TABLE." a
		  left join ".REFERENCE_LANG_TABLE." b on b.table_key='".$multi_lang->TableKey(NATION_SPR_TABLE)."' and b.id_reference=a.id
		   order by name";
		$rs = $dbconn->Execute($strSQL);
		$i = 0;
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
		$smarty->assign("nation_match", $n_arr);
	
		////  language select
		$strSQL =
			"select distinct a.id, b.".$field_name." as name
			   from ".LANGUAGE_SPR_TABLE." a
		  left join ".REFERENCE_LANG_TABLE." b on b.table_key='".$multi_lang->TableKey(LANGUAGE_SPR_TABLE)."' and b.id_reference=a.id
		   order by name";
		$rs = $dbconn->Execute($strSQL);
		$i = 0;
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
		$smarty->assign("lang_sel_match", $lang_sel);
		
		//VP My Partner Criteria Information
		$rs = $dbconn->Execute("Select id_spr, id_value from ".DESCR_SPR_USER_TABLE." where id_user='".$id_user."'");
		while(!$rs->EOF) {
			$id_spr = $rs->fields[0];
			$id_value = $rs->fields[1];
			if (!isset($sess_info[$id_spr])) {
				$sess_info[$id_spr] = array();
			}
			$sess_info[$id_spr][count($sess_info[$id_spr])+1] = $id_value;
			$rs->MoveNext();
		}
		
		// descr selects
		//
		$table_key = $multi_lang->TableKey(DESCR_SPR_TABLE);
		$table_key_val = $multi_lang->TableKey(DESCR_SPR_VALUE_TABLE);
	
		$strSQL = "SELECT DISTINCT a.id, b.".$field_name." as name from ".DESCR_SPR_TABLE." a left join ".REFERENCE_LANG_TABLE." b on b.table_key='".$table_key."' and b.id_reference=a.id order by a.sorter ";
		$rs = $dbconn->Execute($strSQL);
		$i = 0;
		while(!$rs->EOF)
		{
			$row = intval($i%4)+1;
	
			$in = intval($i/4);
			$info[$in]["id_".$row] = $rs->fields[0];
			$info[$in]["name_".$row] = $rs->fields[1];
			$info[$in]["num_".$row] = $i;
	
			$strSQL_opt = "SELECT DISTINCT a.id, b.".$field_name." as name from ".DESCR_SPR_VALUE_TABLE." a left join ".REFERENCE_LANG_TABLE." b on b.table_key='".$table_key_val."' and b.id_reference=a.id where a.id_spr='".$rs->fields[0]."' order by name ";
	
			$rs_opt = $dbconn->Execute($strSQL_opt);
			$j = 0;
			while(!$rs_opt ->EOF)
			{
				$info[$in]["opt_value_".$row][$j] = $rs_opt->fields[0];
				$info[$in]["opt_name_".$row][$j] = $rs_opt->fields[1];
				
				if( (isset($sess_info[$rs->fields[0]]) && is_array($sess_info[$rs->fields[0]]) && in_array(0, $sess_info[$rs->fields[0]])) || (!isset($sess_info[$rs->fields[0]])) || (!is_array($sess_info[$rs->fields[0]])) )
				{
					$info[$in]["sel_all_".$row] = "1";
				}
				else
				{
					if(isset($sess_info[$rs->fields[0]]) && is_array($sess_info[$rs->fields[0]]) && in_array($rs_opt->fields[0], $sess_info[$rs->fields[0]]))
					{
						$info[$in]["opt_sel_".$row][$j] = $rs_opt->fields[0];
					}
					else
					{
						$info[$in]["opt_sel_".$row][$j] = 0;
					}
				}
				$rs_opt->MoveNext();
				$j++;
			}
			$rs->MoveNext();
			$i++;
		}
		
		// distance select
		//
		/*$strSQL = "SELECT id, name, type FROM ".DISTANCE_SPR_TABLE." order by type, name desc";
		$rs = $dbconn->Execute($strSQL);
		$i=0;
		while(!$rs->EOF)
		{
			$row = $rs->GetRowAssoc(false);
			$distances_arr[$i]["id"] = $row["id"];
			$distances_arr[$i]["name"] = $row["name"];
			$distances_arr[$i]["type"] = ($row["type"] == "mile") ? $lang["distance"]["mile"] : $lang["distance"]["km"];
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign("distances", $distances_arr);*/
	
		// relationships select
		//
		/*
		$strSQL =
			"select distinct a.id, b.".$field_name." as name
			   from ".RELATION_SPR_TABLE." a
		  left join ".REFERENCE_LANG_TABLE." b on b.table_key='".$multi_lang->TableKey(RELATION_SPR_TABLE)."' and b.id_reference=a.id
		   order by a.sorter";
		$rs = $dbconn->Execute($strSQL);
		$i = 0;
		while(!$rs ->EOF) {
			$relation_arr["opt_value"][$i] = $rs->fields[0];
			$relation_arr["opt_name"][$i] = $rs->fields[1];
			if(isset($data["relation"]) && is_array($data["relation"]) && in_array($rs->fields[0], $data["relation"]))
			{
				$relation_arr["opt_sel"][$i] = 1;
			}
			else
			{
				$relation_arr["opt_sel"][$i] = 0;
			}
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign("relation", $relation_arr);*/
	
		$form["action"] = $file_name;
		$form["hiddens"]  = "<input type=hidden name=sel value=search>";
		$form["hiddens"] .= "<input type=hidden name=uid value=$id_user>";
		$smarty->assign("form", $form);
		
		$smarty->assign("header", $lang["user_match"]);
		$smarty->assign("button", $lang["button"]);
		
		$smarty->assign("id_err", $id_err);
		$smarty->assign("info", $info);
		$smarty->assign("section", $lang["subsection"]);
		$smarty->assign("data", $data);
		$smarty->assign("header_s", $lang["search"]);
		$smarty->assign("header_perfect", $lang["users"]);
		
		$smarty->assign("sel", $sel);
		$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_user_match.tpl");
		exit;
	}
	else
	{
		$smarty->assign("sel", $sel);
		$smarty->assign("back_link", 'admin_users.php');
		$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_user_match.tpl");
		exit;
	}
}

function SearchList($err="")
{
	global $smarty, $dbconn, $config, $lang, $config_index;
	
	//if(isset($_SERVER["PHP_SELF"]))
	
	$file_name = (isset($_SERVER["PHP_SELF"]))?AfterLastSlash($_SERVER["PHP_SELF"]):"admin_user_match.php";
	AdminMainMenu($lang["user_match"]);
	
	$page = (isset($_REQUEST['page']) && intval($_REQUEST['page'])>0) ? intval($_REQUEST['page']) : 1;
	/*
	$letter = (isset($_REQUEST["letter"]) && intval($_REQUEST["letter"])>0) ? intval($_REQUEST["letter"]) : "*";
	$sorter = (isset($_REQUEST['sorter']) && intval($_REQUEST['sorter'])>0) ? intval($_REQUEST['sorter']) : 5;
	$s_type = (isset($_REQUEST['s_type']) && intval($_REQUEST['s_type'])>0) ? intval($_REQUEST['s_type']) : 1;
	$order = (isset($_REQUEST['order']) && intval($_REQUEST['order'])>0) ? intval($_REQUEST['order']) : 1;
	
	$search = (isset($_REQUEST['search'])) ? strval($_REQUEST['search']) : "";
	$s_stat = (isset($_REQUEST['s_stat'])) ? strval($_REQUEST['s_stat']) : "";
	$group = (isset($_REQUEST['group'])) ? strval($_REQUEST['group']) : "";
	$s_gender = (isset($_REQUEST['s_gender'])) ? strval($_REQUEST['s_gender']) : "";
	
	$sel = isset($_REQUEST["sel"]) ? $_REQUEST["sel"] : "";
	$pre_sel = isset($_REQUEST["pre_sel"]) ? $_REQUEST["pre_sel"] : $sel;
	$s_pending = isset($_REQUEST["s_pending"]) ? $_REQUEST["s_pending"] : "";
	$plat_applied = isset($_REQUEST["plat_applied"]) ? $_REQUEST["plat_applied"] : "";
	
	$search_str = "";
	*/
	
	$id_user = (isset($_REQUEST["uid"])) ? intval($_REQUEST["uid"]) : "";
	if(!$id_user)
	{
		$id_user = isset($_POST["uid"]) ? $_POST["uid"] : "";
	}
	
	if($id_user)
	{
		$id_arr = array();

		$page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";
		$filter = isset($_REQUEST["filter"]) ? $_REQUEST["filter"] : "";
		$view = isset($_REQUEST["view"]) && $_REQUEST["view"] == "gallery" ? "gallery" : "list";
	
		switch($filter)
		{
			case "all": $id_arr = isset($_SESSION["id_arr"])?$_SESSION["id_arr"]:array(); break;
			case "photo": $id_arr = isset($_SESSION["with_arr"])?$_SESSION["with_arr"]:array(); break;
			case "online": $id_arr = isset($_SESSION["online_arr"])?$_SESSION["online_arr"]:array(); break;
			default:
				$id_arr = isset($_SESSION["id_arr"])?$_SESSION["id_arr"]:array();
				$filter = 'all';
				break;
		}
		$use_session = 0;
	
		if(intval($page)>0 && is_array($id_arr) && count($id_arr)>0 )
		{
			$use_session = 1;		//// use a session id array
		}
		if( (strval($page) == "") || (strval($page) == "0"))
		{
			$page = 1;
		}
		else
		{
			$page=intval($page);
		}
			
		if(!isset($_GET["par"])) unset($_SESSION["search_page"]);
		if((strval($page) == "") || (strval($page) == "0"))
		{
			if (isset($_SESSION["search_page"]))
			$page = $_SESSION["search_page"];
			else
			$page = 1;
		}
		else
		{
			$page = intval($page);
			$_SESSION["search_page"] = $page;
		}
		
		//////// if isnt set post vars echo err
		
		if($use_session == 0)
		{
			$spr = isset($_REQUEST["spr"])?$_REQUEST["spr"]:array();
			$info =  isset($_REQUEST["info"])?$_REQUEST["info"]:array();
			$id_lang = isset($_REQUEST["id_lang"])?$_REQUEST["id_lang"]:0;
			$id_country = isset($_REQUEST["id_country"])?intval($_REQUEST["id_country"]):0;
			$id_region = isset($_REQUEST["id_region"])?intval($_REQUEST["id_region"]):0;
			$id_city = isset($_REQUEST["id_city"])?intval($_REQUEST["id_city"]):0;
			$id_nation = isset($_REQUEST["id_nation"])?$_REQUEST["id_nation"]:0;
			$id_weight = isset($_REQUEST["id_weight"])?intval($_REQUEST["id_weight"]):0;
			$id_height = isset($_REQUEST["id_height"])?intval($_REQUEST["id_height"]):0;
			$gender_2 = isset($_REQUEST["gender_2"])?intval($_REQUEST["gender_2"]):0;
			$couple_2 = isset($_REQUEST["couple_2"])?$_REQUEST["couple_2"]:0;
			$age_max = isset($_REQUEST["age_max"])?intval($_REQUEST["age_max"]):0;
			$age_min = isset($_REQUEST["age_min"])?intval($_REQUEST["age_min"]):0;
			$within = isset($_REQUEST["within"])?intval($_REQUEST["within"]):0;
			$distance = isset($_REQUEST["distance"])?intval($_REQUEST["distance"]):0;
			$relation = isset($_REQUEST["relation"])?$_REQUEST["relation"]:array();
			$foto = isset($_REQUEST["foto_only"])?intval($_REQUEST["foto_only"]):0;
			$online = isset($_REQUEST["online_only"])?intval($_REQUEST["online_only"]):0;
		}
		
		if(isset($_GET["par"]) && ($_GET["par"]=="back" || $_GET["par"]=="send"))
		{
			// if user clicked on back to list button
			//
			if(isset($_SESSION["id_arr"]))
			{
				$id_arr = $_SESSION["id_arr"];
				$use_session = 1;		//// use a session id array
			}
			else
			{
				$spr = $_SESSION["search_pars"]["spr"];					//// get new data from db
				$info = $_SESSION["search_pars"]["info"];
				$id_lang = $_SESSION["search_pars"]["id_lang"];
				$id_country = $_SESSION["search_pars"]["id_country"];
				$id_region = $_SESSION["search_pars"]["id_region"];
				$id_city = $_SESSION["search_pars"]["id_city"];
				$id_nation = $_SESSION["search_pars"]["id_nation"];
				$id_weight = $_SESSION["id_weight"];
				$id_height = $_SESSION["id_height"];
				$gender_2 = $_SESSION["search_pars"]["gender_2"];
				$couple_2 = $_SESSION["search_pars"]["couple_2"];
				$age_max = $_SESSION["search_pars"]["age_max"];
				$age_min = $_SESSION["search_pars"]["age_min"];
				$within = $_SESSION["search_pars"]["within"];
				$distance = $_SESSION["search_pars"]["distance"];
				$relation = $_SESSION["search_pars"]["relation"];
				$foto = $_SESSION["search_pars"]["foto"];
				$online = $_SESSION["search_pars"]["online"];
				$use_session = 0;		//// use a session data array
			}
		}
		
		//vp adding privacy condition
		$usr_group = $dbconn->GetOne('SELECT id_group FROM '.USER_GROUP_TABLE.' WHERE id_user='.$id_user);
		$where_str_pp="";
		
		switch($usr_group)
		{
			case MM_TRIAL_GUY_ID:
					$where_str_pp=" AND up.vis_guy_1='1' ";
					break;
			case MM_TRIAL_LADY_ID:
					$where_str_pp=" AND up.vis_lady_1='1' ";
					break;
			case MM_REGULAR_LADY_ID:
					$where_str_pp=" AND up.vis_lady_2='1' ";
					break;
			case MM_PLATINUM_LADY_ID:
					$where_str_pp=" AND up.vis_lady_3='1' ";
					break;
			case MM_REGULAR_GUY_ID:
					$where_str_pp=" AND up.vis_guy_2='1' ";
					break;
			case MM_PLATINUM_GUY_ID:
					$where_str_pp=" AND up.vis_guy_3='1' ";
					break;
			case MM_ELITE_GUY_ID:
					$where_str_pp=" AND up.vis_guy_4='1' ";
					break;
			default:
					$where_str_pp="";
		}
		
		// settings
		//
		$settings = GetSiteSettings(array('icon_male_default', 'icon_female_default', 'icons_folder', 'show_users_connection_str',
			'show_users_comments', 'show_users_group_str', 'use_kiss_types', 'thumb_max_width', 'use_friend_types'));
		
		$smarty->assign("icon_width", $settings["thumb_max_width"]);
	
		// coupled
		//
		$is_coupled = $dbconn->getOne('SELECT couple FROM '.USERS_TABLE.' WHERE id = "'.$id_user.'"');
		
		$smarty->assign('is_coupled', $is_coupled);
		
		if($use_session == 0)
		{
			unset($_SESSION["search_pars"]);	// unset session data
	
			//// search parametrs and id array put in sessoin			/// set new session data
			$_SESSION["search_pars"]["spr"] = $spr;
			$_SESSION["search_pars"]["info"] = $info;
			$_SESSION["search_pars"]["id_lang"] = $id_lang;
			$_SESSION["search_pars"]["id_country"] = $id_country;
			$_SESSION["search_pars"]["id_region"] = $id_region;
			$_SESSION["search_pars"]["id_city"] = $id_city;
			$_SESSION["search_pars"]["id_nation"] = $id_nation;
			$_SESSION["search_pars"]["id_weight"] = $id_weight;
			$_SESSION["search_pars"]["id_height"] = $id_height;
			$_SESSION["search_pars"]["gender_2"] = $gender_2;
			$_SESSION["search_pars"]["couple_2"] = $couple_2;
			$_SESSION["search_pars"]["age_max"] = $age_max;
			$_SESSION["search_pars"]["age_min"] = $age_min;
			$_SESSION["search_pars"]["within"] = $within;
			$_SESSION["search_pars"]["distance"] = $distance;
			$_SESSION["search_pars"]["relation"] = $relation;
			$_SESSION["search_pars"]["foto"] = $foto;
			$_SESSION["search_pars"]["online"] = $online;
	
			unset($_SESSION["id_arr"], $_SESSION["with_arr"], $_SESSION["without_arr"], $_SESSION["online_arr"], $_SESSION["offline_arr"]);	// unset session data
	
			////// get a descr user  array
			$descr_user_array = array();
			$descr_error = 0;
	
			$id_arr = array();
			foreach($spr as $key=>$id_spr)
			{
				$cr = $dbconn->Execute("Select count(*) from ".DESCR_SPR_VALUE_TABLE." where id_spr = '".$id_spr."'");
				
				if(is_array($info[$key]) && !in_array("0", $info[$key]) && $cr->fields[0] >count($info[$key]) && count($info[$key])>0)
				{
					$info_string = implode(" ,", $info[$key]);
	
					if(is_array($descr_user_array) && count($descr_user_array)>0)
					{
						$user_string = " and id_user in (".implode(" ,", $descr_user_array).")";
					}
					$str_sql_descr = "select distinct id_user from ".DESCR_SPR_USER_TABLE."  where id_spr='".$id_spr."' and id_value in (".$info_string.") ".$user_string."";
					$descr_rs = $dbconn->Execute($str_sql_descr);
	
					unset($descr_user_array);
					$descr_user_array = array();
	
					while(!$descr_rs->EOF)
					{
						array_push($descr_user_array, $descr_rs->fields[0]);
						$descr_rs->MoveNext();
					}
					if(count($descr_user_array) == 0)
					{
						$descr_error = 1;
						$_SESSION["id_arr"] = $id_arr;
						break;
					}
				}
			}
	
			if( ($within == 1) && (!intval($id_city)) )
			{
				$err = $lang["distance"]["specify_city"];
				$descr_error = 1;
			}
	
			if ($descr_error != 1)
			{
				$select_clause = "SELECT DISTINCT a.id, a.icon_path, e.id_user AS session";
				
				$from_clause = " FROM ".USER_MATCH_TABLE." b, ".USERS_TABLE." a ";
				
				$join_clause = " left join ".USER_GROUP_TABLE." g on a.id = g.id_user";
				$join_clause.= " left join ".GROUPS_TABLE." s on g.id_group = s.id";
				$join_clause.= " LEFT JOIN ".ACTIVE_SESSIONS_TABLE." e ON a.id = e.id_user";
				$join_clause.= " left join ".USER_PRIVACY_SETTINGS." up on up.id_user = a.id";
				
				$group_clause = "";
				
				//VP Order Clause
				$order_clause = ' ORDER BY s.sort asc, a.mm_platinum_applied DESC, a.date_registration DESC';
				
				$where = array();
				if (intval($id_country)) {
					array_push($where, " a.id_country='".$id_country."'");
				}
				if (intval($id_region)) {
					array_push($where, " a.id_region='".$id_region."'");
				}
				if (intval($id_city)) {
					array_push($where, " a.id_city='".$id_city."'");
				}
	
				if($within == 1)
				{
					$city_arr = CityInRadius($id_city,$distance);
					if (count($city_arr))
					{
						$where = array();
						array_push($where, " a.id_city in (".join(",", $city_arr).") ");
					}
				}
	
				//vp
				$relation_str=false;
				
				if (count($relation)) {
					$relation_arr = array();
					foreach($relation as $value) {
						if (intval($value)) {
							array_push($relation_arr, " ".$value." in (b.id_relationship) ");
						} else {
							$relation_str = " b.id_relationship<>'' and b.id_relationship is not null ";
							break;
						}
					}
					if (!$relation_str) {
						array_push($relation_arr, " '0' in (b.id_relationship) ");
						$relation_str = implode(" or ", $relation_arr);
					}
					array_push($where, " (".$relation_str.") ");
				}
	
				if(intval($id_weight))
				array_push($where, " a.id_weight='".$id_weight."'");
				if(intval($id_height))
				array_push($where, " a.id_height='".$id_height."'");
	
				$cr = $dbconn->Execute("Select count(*) from ".NATION_SPR_TABLE."");
				if(is_array($id_nation) && count($id_nation)>0 && !in_array("0", $id_nation)  && $cr->fields[0] >count($id_nation))
				{
					$id_nation_str = implode(" ,", $id_nation);
					array_push($where, " a.id_nationality in (".$id_nation_str.")");
				}
	
				$cr = $dbconn->Execute("Select count(*) from ".LANGUAGE_SPR_TABLE."");
				if(is_array($id_lang) && count($id_lang)>0 && !in_array("0", $id_lang)  && $cr->fields[0] >count($id_lang))
				{
					$id_lang_str = implode(" ,", $id_lang);
					array_push($where, " (a.id_language_1 in (".$id_lang_str.") or a.id_language_2 in (".$id_lang_str.") or a.id_language_3 in (".$id_lang_str."))");
				}
				if(intval($age_min))
				array_push($where, " STRCMP(date_format(a.date_birthday, '%Y%m%d'), date_format('".DateFromAge($age_min-1)."', '%Y%m%d')) <=0");
				if(intval($age_max))
				array_push($where, " STRCMP(date_format(a.date_birthday, '%Y%m%d'), date_format('".DateFromAge($age_max+1)."', '%Y%m%d')) >= 0");
				if(intval($gender_2))
				array_push($where, " a.gender='".$gender_2."'");
				if(isset($couple_2))
				array_push($where, " a.couple='".$couple_2."'");
	
				if($foto == 1)
				array_push($where, " a.icon_path <> ''");
	
				if ($online == 1)
				{
					//VP online privacy settings check added
					$strSQL =
						'SELECT DISTINCT a.id
						   FROM '.USERS_TABLE.' a
					 INNER JOIN '.ACTIVE_SESSIONS_TABLE.' e ON a.id = e.id_user
					 INNER JOIN '.USER_PRIVACY_SETTINGS.' p ON a.id = p.id_user
						  WHERE a.status = "1" AND a.visible = "1" AND a.root_user = "0"
							AND a.guest_user = "0" AND p.hide_online = "0" AND a.id <> ?';
					
					$rs = $dbconn->Execute($strSQL, array($id_user));
					$ids = array();
					while(!$rs->EOF)
					{
						$row = $rs->GetRowAssoc(false);
						$ids[] = $row["id"];
						$rs->MoveNext();
					}
					if (!count($ids)) $ids[0] = '0';
					array_push($where, " a.id in (".implode(" ,", $ids).")");
				}
	
				array_push($where, " b.id_user=a.id");
				array_push($where, " a.root_user = '0'");		//// not admin
				array_push($where, " a.guest_user = '0'");		//// not guest
				array_push($where, " a.id != '".$id_user."'");	//// not self
				array_push($where, " a.status='1'");			//// active user
				array_push($where, " a.visible='1'");			//// visible user
				
				if(is_array($descr_user_array) && count($descr_user_array)>0)
				{
					array_push($where, " a.id in (".implode(" ,", $descr_user_array).")");
				}
	
				$where_clause = implode(" and", $where);
				if(strlen($where_clause)>0)
				$where_clause = " where ".$where_clause;
				
				$strSQL = $select_clause.$from_clause.$join_clause.$where_clause.$group_clause.$where_str_pp.$order_clause;
				//VP echo $strSQL;
				$rs = $dbconn->Execute($strSQL);
				$i = 0;
				while(!$rs->EOF)
				{
					$row = $rs->GetRowAssoc(false);
					$_SESSION["id_arr"][$i] = $row["id"];
					if(strlen($row["icon_path"]))
					{
						$_SESSION["with_arr"][$i] = $row["id"];
					}
					else
					{
						$_SESSION["without_arr"][$i] = $row["id"];
					}
					if(intval($row["session"]))
					{
						$_SESSION["online_arr"][$i] = $row["id"];
					}
					else
					{
						$_SESSION["offline_arr"][$i] = $row["id"];
					}
					$i++;
					$rs->MoveNext();
				}
			}
		}
		switch($filter)
		{
			case "all": $id_arr = isset($_SESSION["id_arr"])?$_SESSION["id_arr"]:array(); break;
			case "photo": $id_arr = isset($_SESSION["with_arr"])?$_SESSION["with_arr"]:array(); break;
			case "online": $id_arr = isset($_SESSION["online_arr"])?$_SESSION["online_arr"]:array(); break;
			default: $id_arr = isset($_SESSION["id_arr"])?$_SESSION["id_arr"]:array();
		}
		$num_records = count($id_arr);
	
		/// pages
		$default_photos['1'] = $settings['icon_male_default'];
		$default_photos['2'] = $settings['icon_female_default'];
		$user_str = "";
		$numpage = ($view == "gallery")?$config_index["search_gallery_numpage"]:$config_index["search_numpage"];
		$pages_arr = array();
		$lim_min = ($page-1)*$numpage;
		$lim_max = min($numpage, ($num_records-$lim_min) );
		$pages_arr = array_slice($id_arr, $lim_min, $lim_max);
		$user_str = implode(", ", $pages_arr);
		
		if (strlen($user_str) > 0) {
			$strSQL =
				'SELECT DISTINCT a.id, a.login, a.phone, SUBSTRING(a.comment, 1, 165) AS comment, a.gender,
						a.date_birthday, a.id_country, a.id_city, a.id_region,
						DATE_FORMAT(a.date_last_seen, "'.$config['date_format'].'") AS date_last_login, a.icon_path
				   FROM '.USERS_TABLE.' a
			  LEFT JOIN '.USER_GROUP_TABLE.' g ON a.id = g.id_user
			  LEFT JOIN '.GROUPS_TABLE.' s ON g.id_group = s.id
				  WHERE a.id IN ('.$user_str.')
			   ORDER BY s.sort ASC, a.mm_platinum_applied DESC, a.date_registration DESC';
		} else {
			$strSQL = '';
		}
		
		if ($num_records > 0 && strlen($strSQL) > 0)
		{
			$rs = $dbconn->Execute($strSQL);
			$i = 0;
			$search = array();
			$_LANG_NEED_ID = array();
			
			while (!$rs->EOF) {
				$row = $rs->GetRowAssoc(false);
				$search[$i]['id'] = $row['id'];
				$search[$i]['name'] = $row['login'];
				$search[$i]['gender'] = $row['gender'];
				$search[$i]['phone'] = $row['phone'];
				$search[$i]['number'] = ($page-1)*$config_index['search_numpage']+($i+1);
				$search[$i]['age'] = AgeFromBDate($row['date_birthday']);
				$search[$i]['id_country'] = intval($row['id_country']);
				$search[$i]['id_region'] = intval($row['id_region']);
				$search[$i]['id_city'] = intval($row['id_city']);
				
				$_LANG_NEED_ID['country'][] = intval($row['id_country']);
				$_LANG_NEED_ID['region'][] = intval($row['id_region']);
				$_LANG_NEED_ID['city'][] = intval($row['id_city']);
				
				$search[$i]['profile_link'] = '../viewprofile.php?id='.$row['id'].'&amp;search_type=a&amp;page='.$page;
	
				$icon_path = $row["icon_path"] ? $row["icon_path"] : $default_photos[$row["gender"]];
				if ($icon_path && file_exists($config["site_path"].$settings["icons_folder"]."/".$icon_path)) {
					$search[$i]["icon_path"] = $config["site_root"].$settings["icons_folder"]."/".$icon_path;
				}
				$img_icon = $row["icon_path"]?1:0;
	
				$strSQL = "select count(distinct f.upload_path) as photo_count  from ".USER_UPLOAD_TABLE." f where f.id_user = ".$row["id"]." and f.upload_type='f' and f.status='1' and f.allow in ('1', '2')";
				$rs_photo = $dbconn->Execute($strSQL);
				$search[$i]["photo_count"] = $rs_photo->fields[0] + $img_icon;
	
				if ($view != "gallery")
				{
					$search[$i]["annonce"] = stripslashes($row["comment"]);
					//$search[$i]["completion"] = $percent->GetAllPercentForUser($row["id"]);
					$search[$i]["last_login"] = $row["date_last_login"];
	
					// get groups
					//
					$sub_strSQL =
						'SELECT a.name, b.id_group
						   FROM '.USER_GROUP_TABLE.' b
					  LEFT JOIN '.GROUPS_TABLE.' a ON a.id = b.id_group
						  WHERE b.id_user = ?';
					
					$sub_rs = $dbconn->Execute($sub_strSQL, array($row['id']));
					
					$groups_arr = array();
					$id_groups_arr = array();
					
					while (!$sub_rs->EOF)
					{
						$groups_arr[] = $sub_rs->fields[0];
						$id_groups_arr[] = $sub_rs->fields[1];
						$sub_rs->MoveNext();
					}
					
					if (is_array($groups_arr) && !empty($groups_arr))
					{
						$search[$i]['group'] = implode(', ', $groups_arr);
						$search[$i]['id_group'] = implode('<br>', $id_groups_arr);
						
						if ($search[$i]['id_group'] == MM_REGULAR_GUY_ID || $search[$i]['id_group'] == MM_REGULAR_LADY_ID
						|| $search[$i]['id_group'] == MM_TRIAL_GUY_ID || $search[$i]['id_group'] == MM_TRIAL_LADY_ID)
						{
							//VP fetching Platinum Applied status
							$date_apply = $dbconn->GetOne('SELECT mm_platinum_applied FROM '.USERS_TABLE.' WHERE id = ?', array($row['id']));
							$is_platinum_applied = !empty($date_apply);
							$search[$i]['is_platinum_applied'] = $is_platinum_applied;
							
							if ($is_platinum_applied) {
								$search[$i]['group'] .= ' ('. $lang['users']['platinum_applied'].')';
							}
						}
						
						//VP is user is Freeze show them as regular or platinum
						$verified = $dbconn->GetOne('SELECT platinum_verified FROM '.USERS_TABLE.' WHERE id = ?', array($row['id']));
						$is_platinum_verified = !empty($verified);
						$search[$i]['is_platinum_verified'] = $is_platinum_verified;
						
						switch ($search[$i]['id_group'])
						{
							case MM_INACT_REGULAR_GUY_ID: $search[$i]['group'] = $lang['users']['list_inact_reg_guy']; break;
							case MM_INACT_REGULAR_LADY_ID: $search[$i]['group'] = $lang['users']['list_inact_reg_lady']; break;
							case MM_INACT_PLATINUM_GUY_ID: $search[$i]['group'] = $lang['users']['list_inact_plat_guy']; break;
							case MM_INACT_PLATINUM_LADY_ID: $search[$i]['group'] = $lang['users']['list_inact_plat_lady']; break;
							case MM_INACT_ELITE_GUY_ID: $search[$i]['group'] = $lang['users']['list_inact_elite_guy']; break;
						}
					}
					
					// get status
					$sub_rs = $dbconn->Execute('SELECT COUNT(*) AS id_friend FROM '.ACTIVE_SESSIONS_TABLE.' WHERE id_user = ?', array($row["id"]));
					//VP online privacy settings check added
					//$search[$i]["status"] = intval($sub_rs->fields[0])?$lang["status"]["on"]:$lang["status"]["off"];
					
					if ($sub_rs->fields[0])
					{
						$count = $dbconn->getOne('SELECT COUNT(id) FROM '.USER_PRIVACY_SETTINGS.' WHERE hide_online = "1" AND id_user = ?', array($row['id']));
						$search[$i]['status'] = empty($count) ? $lang['status']['on'] : $lang['status']['off'];
					}
					else
					{
						$search[$i]["status"] = $lang["status"]["off"];	
					}
	
					// get user search params
					$sub_rs = $dbconn->Execute('SELECT gender as gender_search, age_max, age_min FROM '.USER_MATCH_TABLE.' WHERE id_user = ?', array($row['id']));
					$sub_row = $sub_rs->GetRowAssoc(false);
					$search[$i]["age_max"] = $sub_row["age_max"];
					$search[$i]["age_min"] = $sub_row["age_min"];
					$search[$i]["gender_search"] = $lang["gender_search"][$sub_row["gender_search"]];
	
					// links
					$search[$i]['email_link'] = 'mailbox.php?sel=fs&amp;id='.$row['id'].'&amp;search_type=a';
					$search[$i]['sendfriend_link'] = './send_friend.php?sel=send&amp;id_user='.$row['id'];
					$search[$i]['kiss_link'] = $settings['use_kiss_types'] ? './send_kiss.php?sel=send&amp;id_user='.$row['id'] : $file_name.'?sel=kiss&amp;id='.$row['id'].'&amp;page='.$page;
					$search[$i]['gift_link'] = './giftshop.php?sel=users_add&amp;id_user='.$row['id'];
					$search[$i]['ecard_link'] = './ecards.php?id_user_to='.$row['id'].'&amp;fixuser=Y';
	
					// check hotlisted
					$sub_rs = $dbconn->Execute(
						'SELECT COUNT(*)
						   FROM '.HOTLIST_TABLE.'
						  WHERE id_friend = "'.$row['id'].'" AND id_user = "'.$id_user.'"');
					
					$search[$i]['hotlisted'] = !empty($sub_rs->fields[0]) ? 1 : 0;
	
					// check blacklisted
					$sub_rs = $dbconn->Execute(
						'SELECT COUNT(*)
						   FROM '.BLACKLIST_TABLE.'
						  WHERE id_enemy = "'.$row['id'].'" AND id_user = "'.$id_user.'"');
					
					$search[$i]['blacklisted'] = !empty($sub_rs->fields[0]) ? 1 : 0;
					
					// check connection status
					$search[$i]['connected_status'] = getConnectedStatus($row['id'], $id_user);
					
					// add hotlist link
					if ($search[$i]['hotlisted'] == 0 && $search[$i]['blacklisted'] == 0) {
						if ($settings['use_friend_types']) {
							$search[$i]['add_hotlist_link'] = './hotlist.php?sel=addform&amp;id='.$row['id'];
						} else {
							$search[$i]['add_hotlist_link'] = './advanced_search.php?sel=addhotlist&amp;id='.$row['id'].'&amp;page='.$page;
						}
					}
						
					// add blacklist link
					if ($search[$i]['hotlisted'] == 0 && $search[$i]['connected_status'] != CS_CONNECTED && $search[$i]['blacklisted'] == 0) {
						$search[$i]['add_blacklist_link'] = './advanced_search.php?sel=addblacklist&amp;id='.$row['id'].'&amp;page='.$page;
					}
					
					// add connection link
					if ($search[$i]['connected_status'] == CS_NOTHING && $search[$i]['blacklisted'] == 0) {
						if ($settings['use_friend_types']) {
							$search[$i]['add_connection_link'] = './connections.php?sel=addform&amp;id='.$row['id'];
						} else {
							$search[$i]['add_connection_link'] = './advanced_search.php?sel=addconnection&amp;id='.$row['id'].'&amp;page='.$page;
						}
					}
					
					if ($is_coupled != 1) {
						$search[$i]['be_couple_link'] = './'.$file_name.'?sel=be_couple&amp;id='.$row['id'].'&amp;page='.$page;
					}
					
					if ($config['voipcall_feature']) {
						$search[$i]['call_link'] = './voip_call.php?sel=rate&amp;id_user='.$search[$i]['id'];
					}
				}
	
				$rs->MoveNext();
				$i++;
			}
			
			//$param = $file_name."?sel=search&amp;filter=".$filter."&amp;view=".$view."&amp;";
			//$form["pages_count"] = ceil($num_records/$numpage);
			//$smarty->assign("links", GetLinkArray($num_records,$page,$param,$numpage));
			$smarty->assign("search_res", $search);
		}
		else
		{
			$smarty->assign("empty", "1");
		}
		$form["err"] = $err;
		$form["guest_user"] = $user[ AUTH_GUEST ];
		$form["online_count"] = isset($_SESSION["online_arr"])?count($_SESSION["online_arr"]):0;
		$form["offline_count"] = isset($_SESSION["offline_arr"])?count($_SESSION["offline_arr"]):0;
		$form["with_count"] = isset($_SESSION["with_arr"])?count($_SESSION["with_arr"]):0;
		$form["without_count"] = isset($_SESSION["without_arr"])?count($_SESSION["without_arr"]):0;
	
		$form["view_online_link"] = "./".$file_name."?sel=search&amp;page=1&amp;filter=online&amp;view=".$view;
		$form["view_photo_link"] = "./".$file_name."?sel=search&amp;page=1&amp;filter=photo&amp;view=".$view;
		$form["view_all_link"] = "./".$file_name."?sel=search&amp;page=1&amp;filter=all&amp;view=".$view;
		$form["view_gallery_link"] = "./".$file_name."?sel=search&amp;page=1&amp;filter=".$filter."&amp;view=gallery";
		$form["view_list_link"] = "./".$file_name."?sel=search&amp;page=1&amp;filter=".$filter;
	
		$form["show_users_connection_str"] = $settings["show_users_connection_str"];
		$form["show_users_comments"] = $settings["show_users_comments"];
		$form["show_users_group_str"] = $settings["show_users_group_str"];
		$form["use_kiss_types"] = $settings["use_kiss_types"];
		$form["use_friend_types"] = $settings["use_friend_types"];
	
		$form["filter"] = $filter;
		$form["view"] = $view;
		
		$form["action"] = $file_name;
		$form["hiddens"]  = "<input type=hidden name=sel value=search>";
		$form["hiddens"] .= "<input type=hidden name=uid value=$id_user>";
		
		$smarty->assign("form", $form);
	
		if (isset($_LANG_NEED_ID) && count($_LANG_NEED_ID))
		{
			$smarty->assign("base_lang", GetBaseLang($_LANG_NEED_ID));
		}
		$smarty->assign("section", $lang["subsection"]);
		$smarty->assign("header", $lang["user_match"]);
		$smarty->assign("header_s", $lang["search"]);
		$back_link = "admin_user_match.php?uid=".$id_user;
		$smarty->assign("back_link", $back_link);
	}
	else
	{
		$smarty->assign("back_link", 'admin_users.php');
	}
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_user_match_list.tpl");
	exit;
}

?>