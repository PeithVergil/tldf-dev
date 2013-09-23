<?php
/**
*
*   @author Pilot Group <http://www.pilotgroup.net/>
*   @date
*
**/
include "../include/config.php";
include_once "../common.php";
include "../include/config_admin.php";
include "../include/functions_auth.php";
include "../include/functions_admin.php";

$auth = auth_user();
login_check($auth);
$mode = IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "events");

$sel = $_GET["sel"]?$_GET["sel"]:$_POST["sel"];

if ($mode == 1) {
	switch ($sel) {
		case "add": EditEvent($par="add"); break;
		case "save": EditEvent(); break;
		case "del": DeleteEvent(); break;
		case "edit": EditEvent($par="edit"); break;
		case "types": ListEventTypes(); break;
		case "addtype": EditEventType($par="add"); break;
		case "savetype": EditEventType(); break;
		case "deltype": DeleteEventType(); break;
		case "edittype": EditEventType($par="edit"); break;
		case "user": UserForm(); break;
		case "eventuser": UserChange(); break;
		default: ListEvents();
	}
} elseif ($mode == 2) {
	ListEvents($lang["err"]["demo_ristriction_err"]);
}

function ListEvents($err="")
{
	global $smarty, $dbconn, $config, $config_admin, $page, $lang, $auth;

	if(isset($_SERVER["PHP_SELF"]))
		$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
		$file_name = "admin_events.php";

	AdminMainMenu();
	$page = $_GET["page"]?$_GET["page"]:$_POST["page"];
	if( (strval($page) == "") || (strval($page) == "0")){ $page = 1; }else{	$page=intval($page);}

	///events per page
	$strSQL = "SELECT count(*) FROM ".EVENT_DESCR_TABLE;
	$rs = $dbconn->Execute($strSQL);
	$num_records = $rs->fields[0];

	$lim_min = ($page-1)*$config_admin["users_numpage"];
	$lim_max = $config_admin["users_numpage"];
	$limit_str = "limit ".$lim_min.", ".$lim_max;
	$smarty->assign("page", $page);

	$param = $file_name."?";
	$smarty->assign("links", GetLinkStr($num_records,$page,$param,$config_admin["users_numpage"]));

	$strSQL = "select a.id, a.event_name, b.name as type,
		DATE_FORMAT(a.start_date,'".$config["date_format"]." %H:%i') as start_date,
		DATE_FORMAT(a.end_date,'".$config["date_format"]." %H:%i') as end_date,
		if(a.die_date = '0000-00-00','',DATE_FORMAT(a.die_date,'".$config["date_format"]."')) as die_date,
		a.periodicity, f.login as event_creator, a.id_creator
		FROM ".EVENT_DESCR_TABLE." a
		LEFT JOIN ".EVENT_TYPES_TABLE." b ON b.id = a.type
		LEFT JOIN ".USERS_TABLE." f ON f.id = a.id_creator
		ORDER BY f.login ".$limit_str;

	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$event = array();
	if (!$rs->EOF) {
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$event[$i]["number"] = $i+1;
			$event[$i]["id"] = $row["id"];
			$event[$i]["admin_creator"] = $row["id_creator"] == $auth[0] ? true : false;
			if (strlen(utf8_decode($row["event_name"]))<100) {
				$event[$i]["name"] = stripslashes($row["event_name"]);
			} else {
				$event[$i]["name"] = stripslashes(utf8_substr($row["event_name"],0,100)."...");
			}
			$event[$i]["type"] = stripslashes($row["type"]);
			$event[$i]["event_creator"] = stripslashes($row["event_creator"]);
			$event[$i]["creator_profile_link"] = "./admin_users.php?sel=edit&id=".$row["id_creator"];
			$event[$i]["date_begin"] = $row["start_date"];
			$event[$i]["date_end"] = $row["end_date"];
			$event[$i]["periodicity"] = $lang["events"]["event_period"][$row["periodicity"]];
			if ($row["periodicity"] != "none" && $row["die_date"])
				$event[$i]["date_die"] = $row["die_date"];

			$event[$i]["edit_link"] = $file_name."?sel=edit&id=".$row["id"];
			$event[$i]["delete_link"] = $file_name."?sel=del&id=".$row["id"];
			$rs2 = $dbconn->Execute("SELECT count(id_user) FROM ".EVENT_USERS_TABLE." WHERE id_event='".$event[$i]["id"]."'");
			$event[$i]["num_users"] = $rs2->fields[0]?$rs2->fields[0]:0;
			$event[$i]["users_link"] = $file_name."?sel=user&id_event=".$row["id"];
			$event[$i]["comunicate_link"] = "./admin_comunicate.php?id=".$row["id_creator"];
			$rs->MoveNext();
			$i++;
		}
	}

	$smarty->assign("event", $event);

	$smarty->assign("add_link", $file_name."?sel=add");
	$smarty->assign("types_link", $file_name."?sel=types");
	$form["err"] = $err;
	$smarty->assign("form", $form);
	// not assigned
	#$smarty->assign("data", $data);
	$smarty->assign("user_list", $lang["groups"]["user_list"]);
	$smarty->assign("header", $lang["events"]);
	$smarty->assign("button", $lang["button"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_event_table.tpl");
	exit;
}

function EditEvent($par)
{
	global $smarty, $dbconn, $config, $page, $lang;
	if(isset($_SERVER["PHP_SELF"]))
		$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
		$file_name = "admin_events.php";

	AdminMainMenu();
	$page = $_GET["page"]?$_GET["page"]:$_POST["page"];
	if( (strval($page) == "") || (strval($page) == "0")){ $page = 1; }else{	$page=intval($page);}
	$form["action"] = $file_name;
	$par = $par ? $par : "edit";
	$form["par"] = $par;
	$form["back"] = $file_name;
	$form["hiddens"] = "<input type=hidden name=sel value=>";
	$form["hiddens"] .= "<input type=hidden name=id_event value='".$_GET["id"]."'>";
	$form["delete_link"] = $file_name."?sel=del&id=".$_GET["id"];
	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["events"]);
	$smarty->assign("button", $lang["button"]);

	$events_uploads_folder = GetSiteSettings('events_uploads_folder');

	if ($_POST["sel"]=='add') {
		AddEvent($par='add');
	} elseif ($_POST["sel"]=='save') {
		AddEvent($par='save');
	} else {
		if ($_GET["id"]) {
			$strSQL = "select id, event_name, event_place, type, id_country, id_region, id_city,
					DATE_FORMAT(start_date,'%Y')  as s_year, DATE_FORMAT(start_date,'%m')  as s_month, DATE_FORMAT(start_date,'%d')  as s_day, DATE_FORMAT(start_date,'%H')  as s_hour, DATE_FORMAT(start_date,'%i')  as s_min,
					DATE_FORMAT(end_date,'%Y')  as f_year, DATE_FORMAT(end_date,'%m')  as f_month, DATE_FORMAT(end_date,'%d')  as f_day, DATE_FORMAT(end_date,'%H')  as f_hour, DATE_FORMAT(end_date,'%i')  as f_min,
					DATE_FORMAT(die_date,'%Y')  as d_year, DATE_FORMAT(die_date,'%m')  as d_month, DATE_FORMAT(die_date,'%d')  as d_day,
					periodicity, event_contain, can_invite, can_post_images, flyer FROM ".EVENT_DESCR_TABLE."
					WHERE id = '".intval($_GET["id"])."'";
			$rs = $dbconn->Execute($strSQL);
			$row = $rs->GetRowAssoc(false);
			$data["id"] = $row["id"];
			$data["name"] = stripslashes($row["event_name"]);
			$data["place"] = stripslashes($row["event_place"]);
			$data["type"] = $row["type"];
			$data["country"] = $row["id_country"];
			$data["region"] = $row["id_region"];
			$data["city"] = $row["id_city"];
			$data["s_year"] = $row["s_year"];
			$data["s_month"] = $row["s_month"];
			$data["s_day"] = $row["s_day"];
			$data["s_hour"] = $row["s_hour"];
			$data["s_min"] = $row["s_min"];
			$data["f_year"] = $row["f_year"];
			$data["f_month"] = $row["f_month"];
			$data["f_day"] = $row["f_day"];
			$data["f_hour"] = $row["f_hour"];
			$data["f_min"] = $row["f_min"];
			$data["contain"] = stripslashes($row["event_contain"]);
			$data["periodicity"] = $row["periodicity"];
			$data["d_year"] = $row["d_year"];
			$data["d_month"] = $row["d_month"];
			$data["members_can_invite"] = $row["can_invite"];
			$data["members_can_post_images"] = $row["can_post_images"];
			if($row["flyer"] && file_exists($config["site_path"].$events_uploads_folder."/".$row["id"]."_".$row["flyer"])){
				$data["flyer"] = $config["site_root"].$events_uploads_folder."/".$row["id"]."_".$row["flyer"];
			} else {
				$data["flyer"] = '';
			}
		} else {
			$data["periodicity"] = "none";
		}
		//finish
		
		$strSQL = "SELECT DISTINCT id, name FROM ".COUNTRY_SPR_TABLE." GROUP BY id ORDER BY name ";
		$rs = $dbconn->Execute($strSQL);
		$i = 0;
		$spr_arr = array();
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$spr_arr[$i]["id"] = $row["id"];
			$spr_arr[$i]["name"] = stripslashes($row["name"]);
			if ($data["country"]==$spr_arr[$i]["id"]){
				$spr_arr[$i]["sel"] = 1;
			}
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign("countries", $spr_arr);

		if ($data["country"]){
			$strSQL = "SELECT DISTINCT id, name FROM ".REGION_SPR_TABLE." WHERE id_country='".$data["country"]."' ORDER BY name";
			$rs = $dbconn->Execute($strSQL);
			$i = 0;
			$spr_arr = array();
			while(!$rs->EOF){
				$row = $rs->GetRowAssoc(false);
				$spr_arr[$i]["id"] = $row["id"];
				$spr_arr[$i]["name"] = stripslashes($row["name"]);
				if ($data["region"] == $spr_arr[$i]["id"])
					$spr_arr[$i]["sel"] = 1;
				$rs->MoveNext();
				$i++;
			}
			$smarty->assign("regions", $spr_arr);
		}
		if ($data["region"]){
			$strSQL = "SELECT DISTINCT id, name FROM ".CITY_SPR_TABLE." WHERE id_region='".$data["region"]."' ORDER BY id";
			$rs = $dbconn->Execute($strSQL);
			$i = 0;
			$spr_arr = array();
			while(!$rs->EOF){
				$row = $rs->GetRowAssoc(false);
				$spr_arr[$i]["id"] = $row["id"];
				$spr_arr[$i]["name"] = stripslashes($row["name"]);
				if ($data["city"] == $spr_arr[$i]["id"])
					$spr_arr[$i]["sel"] = 1;
				$rs->MoveNext();
				$i++;
			}
			$smarty->assign("cities", $spr_arr);
		}

		$strSQL = "SELECT DISTINCT id, name FROM ".EVENT_TYPES_TABLE." GROUP BY id ORDER BY name ";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0]>0){
			$i = 0;
			$type_arr = array();
			while(!$rs->EOF){
				$row = $rs->GetRowAssoc(false);
				$type_arr[$i]["id"] = $row["id"];
				if ($type_arr[$i]["id"]==$data["type"]){
					$type_arr[$i]["sel"] = 1;
				}
				$type_arr[$i]["name"] = stripslashes($row["name"]);
				$rs->MoveNext();
				$i++;
			}
			$smarty->assign("types", $type_arr);
		}

		//// day select
		if(intval($data["s_day"])==0) $data["s_day"] = date("d");
		$s_day = array();
		for($i=0;$i<31;$i++){
			$s_day[$i]["value"] = sprintf("%02d",$i+1);
			if(intval($data["s_day"]) == $i+1)
				$s_day[$i]["sel"] = 1;
			else
				$s_day[$i]["sel"] = 0;
		}
		$smarty->assign("s_day", $s_day);

		////  month select
		if(intval($data["s_month"])==0) $data["s_month"] = date("m");
		$s_month = array();
		for($i=0;$i<12;$i++){
			$s_month[$i]["value"] = $i+1;
			$s_month[$i]["name"] = $lang["month"][$i+1];
			if(intval($data["s_month"]) == $i+1)
				$s_month[$i]["sel"] = 1;
			else
				$s_month[$i]["sel"] = 0;
		}
		$smarty->assign("s_month", $s_month);
		////year select
		if(intval($data["s_year"])==0) $data["s_year"] = date("Y");
		$s_year = array();
		for($i=0;$i<3;$i++){
			$y = intval(date("Y"))+1-$i;
			$s_year[$i]["value"] = $y;
			if(intval($data["s_year"]) == $y)
				$s_year[$i]["sel"] = 1;
			else
				$s_year[$i]["sel"] = 0;
		}
		$smarty->assign("s_year", $s_year);

		//hour
		$s_hour = array();
		for($i=0;$i<24;$i++){
			$s_hour[$i]["value"] = sprintf("%02d",$i);
			if($data["s_hour"] == $i)
				$s_hour[$i]["sel"] = 1;
			else
				$s_hour[$i]["sel"] = 0;
		}
		$smarty->assign("s_hour", $s_hour);

		//minute
		$s_min = array();
		for($i=0;$i<60;$i++){
			$s_min[$i]["value"] = sprintf("%02d",$i);
			if($data["s_min"] == $i)
				$s_min[$i]["sel"] = 1;
			else
				$s_min[$i]["sel"] = 0;
		}
		$smarty->assign("s_min", $s_min);

		//finish
		/// day select
		if(intval($data["f_day"])==0) $data["f_day"] = date("d");
		$f_day = array();
		for($i=0;$i<31;$i++){
			$f_day[$i]["value"] = sprintf("%02d",$i+1);
			if(intval($data["f_day"]) == $i+1)
				$f_day[$i]["sel"] = 1;
			else
				$f_day[$i]["sel"] = 0;
		}
		$smarty->assign("f_day", $f_day);
		////  month select
		if(intval($data["f_month"])==0) $data["f_month"] = date("m");
		$f_month = array();
		for($i=0;$i<12;$i++){
			$f_month[$i]["value"] = $i+1;
			$f_month[$i]["name"] = $lang["month"][$i+1];
			if(intval($data["f_month"]) == $i+1)
				$f_month[$i]["sel"] = 1;
			else
				$f_month[$i]["sel"] = 0;
		}
		$smarty->assign("f_month", $f_month);
		////year select
		if(intval($data["f_year"])==0) $data["f_year"] = date("Y");
		$f_year = array();
		for($i=0;$i<3;$i++){
			$y = intval(date("Y"))+1-$i;
			$f_year[$i]["value"] = $y;
			if(intval($data["f_year"]) == $y)
				$f_year[$i]["sel"] = 1;
			else
				$f_year[$i]["sel"] = 0;
		}
		$smarty->assign("f_year", $f_year);

		//hour
		$f_hour = array();
		for($i=0;$i<24;$i++){
			$f_hour[$i]["value"] = sprintf("%02d",$i);
			if($data["f_hour"] == $i)
				$f_hour[$i]["sel"] = 1;
			else
				$f_hour[$i]["sel"] = 0;
		}
		$smarty->assign("f_hour", $f_hour);
		//minute
		$f_min = array();
		for($i=0;$i<60;$i++){
			$f_min[$i]["value"] = sprintf("%02d",$i);
			if($data["f_min"] == $i)
				$f_min[$i]["sel"] = 1;
			else
				$f_min[$i]["sel"] = 0;
		}
		$smarty->assign("f_min", $f_min);

		//finish
		/// day select
		$d_day = array();
		for($i=0;$i<31;$i++){
			$d_day[$i]["value"] = sprintf("%02d",$i+1);
			if(intval($data["d_day"]) == $i+1)
				$d_day[$i]["sel"] = 1;
			else
				$d_day[$i]["sel"] = 0;
		}
		$smarty->assign("d_day", $d_day);
		////  month select
		$d_month = array();
		for($i=0;$i<12;$i++){
			$d_month[$i]["value"] = $i+1;
			$d_month[$i]["name"] = $lang["month"][$i+1];
			if(intval($data["d_month"]) == $i+1)
				$d_month[$i]["sel"] = 1;
			else
				$d_month[$i]["sel"] = 0;
		}
		$smarty->assign("d_month", $d_month);
		////year select
		$d_year = array();
		for($i=0;$i<3;$i++){
			$y = intval(date("Y"))+1-$i;
			$d_year[$i]["value"] = $y;
			if(intval($data["d_year"]) == $y)
				$d_year[$i]["sel"] = 1;
			else
				$d_year[$i]["sel"] = 0;
		}
		$smarty->assign("d_year", $d_year);

		$smarty->assign("data", $data);
		$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_event_form.tpl");
		exit;
	}
}

function AddEvent($par)
{
	global $smarty, $dbconn, $config, $lang, $auth, $IMG_TYPE_ARRAY, $IMG_EXT_ARRAY;
	
	$start_year = intval($_POST["s_year"]);
	$start_month = intval($_POST["s_month"]);
	$start_day = intval($_POST["s_day"]);
	$start_hour = $_POST["s_hour"];
	$start_min = $_POST["s_min"];
	$finish_year = intval($_POST["f_year"]);
	$finish_month = intval($_POST["f_month"]);
	$finish_day = intval($_POST["f_day"]);
	$finish_hour = $_POST["f_hour"];
	$finish_min = $_POST["f_min"];
	$name = $_POST["name"];
	$type = $_POST["type"];
	$country = $_POST["country"];
	$region = $_POST["region"];
	$city = $_POST["city"];
	$place = $_POST["place"];
	$contain = $_POST["contain"];
	$periodicity = $_POST["periodicity"];
	$die_year = intval($_POST["d_year"]);
	$die_month = intval($_POST["d_month"]);
	$die_day = intval($_POST["d_day"]);
	$members_can_invite = intval($_POST["members_can_invite"]);
	$members_can_post_images = intval($_POST["members_can_post_images"]);
	$flyer_file = $_FILES["flyer"];

	/// check start and finish date
	if (checkdate($start_month, $start_day, $start_year)){
		$start_date = $start_year."-".$start_month."-".$start_day." ".$start_hour.":".$start_min;
	} else {
		$err = $lang["err"]["invalid_date"];
	}
	if (checkdate($finish_month, $finish_day, $finish_year)){
		$finish_date = $finish_year."-".$finish_month."-".$finish_day." ".$finish_hour.":".$finish_min;
	} else {
		$err = $lang["err"]["invalid_date"];
	}
	/// check empty values
	if (strip_tags(trim($name)) == '' || intval($type) == 0 || intval($country) == 0 || intval($region) == 0 || intval($city) == 0 || strip_tags(trim($place)) == '') {
		$err = $lang["err"]["invalid_fields"];
	}
	if ($die_month && $die_day && $die_year){
		if (checkdate($die_month, $die_day, $die_year)){
			$die_date = $die_year."-".$die_month."-".$die_day;
		} else {
			$err = $lang["err"]["invalid_date"];
		}
	}

	if (is_uploaded_file($flyer_file['tmp_name'])) {
		$filename_arr = explode(".", $flyer_file["name"]);
		$nr = count($filename_arr);
		$ext = strtolower($filename_arr[$nr-1]);
		if (!in_array($flyer_file["type"], $IMG_TYPE_ARRAY) || !in_array($ext, $IMG_EXT_ARRAY)) {
			$err = $lang["err"]["invalid_image_type"] . implode(', ', $IMG_TYPE_ARRAY);
		}
	} else {
		$flyer_file["name"] = '';
	}

	$data["name"] = $name;
	$data["type"] = $type;
	$data["country"] = $country;
	$data["region"] = $region;
	$data["city"] = $city;
	$data["contain"] = $contain;
	$data["place"] = $place;
	$data["s_year"] = $start_year;
	$data["s_month"] = $start_month;
	$data["s_day"] = $start_day;
	$data["s_hour"] = $start_hour;
	$data["s_min"] = $start_min;
	$data["f_year"] = $finish_year;
	$data["f_month"] = $finish_month;
	$data["f_day"] = $finish_day;
	$data["f_hour"] = $finish_hour;
	$data["f_min"] = $finish_min;
	$data["start_date"] = $start_date;
	$data["finish_date"] = $finish_date;
	$data["periodicity"] = $periodicity;
	$data["d_year"] = $die_year;
	$data["d_month"] = $die_month;
	$data["d_day"] = $die_day;
	$data["die_date"] = $die_date;
	$data["members_can_invite"] = $members_can_invite;
	$data["members_can_post_images"] = $members_can_post_images;

	$strSQL = "SELECT DISTINCT id, name FROM ".COUNTRY_SPR_TABLE." GROUP BY id ORDER BY name ";
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$spr_arr = array();
	while(!$rs->EOF){
		$row = $rs->GetRowAssoc(false);
		$spr_arr[$i]["id"] = $row["id"];
		$spr_arr[$i]["name"] = stripslashes($row["name"]);
		if ($data["country"]==$spr_arr[$i]["id"]){
			$spr_arr[$i]["sel"] = 1;
		}
		$rs->MoveNext();
		$i++;
	}
	$smarty->assign("countries", $spr_arr);

	if ($data["country"]){
		$strSQL = "SELECT DISTINCT id, name FROM ".REGION_SPR_TABLE." WHERE id_country='".$data["country"]."' ORDER BY name";
		$rs = $dbconn->Execute($strSQL);
		$i = 0;
		$spr_arr = array();
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$spr_arr[$i]["id"] = $row["id"];
			$spr_arr[$i]["name"] = stripslashes($row["name"]);
			if ($data["region"] == $spr_arr[$i]["id"])
				$spr_arr[$i]["sel"] = 1;
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign("regions", $spr_arr);
	}
	if ($data["region"]){
		$strSQL = "SELECT DISTINCT id, name FROM ".CITY_SPR_TABLE." WHERE id_region='".$data["region"]."' ORDER BY id";
		$rs = $dbconn->Execute($strSQL);
		$i = 0;
		$spr_arr = array();
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$spr_arr[$i]["id"] = $row["id"];
			$spr_arr[$i]["name"] = stripslashes($row["name"]);
			if ($data["city"] == $spr_arr[$i]["id"])
				$spr_arr[$i]["sel"] = 1;
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign("cities", $spr_arr);
	}

	$strSQL = "SELECT DISTINCT id, name FROM ".EVENT_TYPES_TABLE." GROUP BY id ORDER BY name ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>0){
		$i = 0;
		$type_arr = array();
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$type_arr[$i]["id"] = $row["id"];
			if ($type_arr[$i]["id"]==$data["type"]){
				$type_arr[$i]["sel"] = 1;
			}
			$type_arr[$i]["name"] = stripslashes($row["name"]);
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign("types", $type_arr);
	}

	//start
	/// day select
	if(intval($data["s_day"])==0) $data["s_day"] = date("d");
	$s_day = array();
	for($i=0;$i<31;$i++){
		$s_day[$i]["value"] = sprintf("%02d",$i+1);
		if(intval($data["s_day"]) == $i+1)
			$s_day[$i]["sel"] = 1;
		else
			$s_day[$i]["sel"] = 0;
	}
	$smarty->assign("s_day", $s_day);
	////  month select
	if(intval($data["s_month"])==0) $data["s_month"] = date("m");
	$s_month = array();
	for($i=0;$i<12;$i++){
		$s_month[$i]["value"] = $i+1;
		$s_month[$i]["name"] = $lang["month"][$i+1];
		if(intval($data["s_month"]) == $i+1)
			$s_month[$i]["sel"] = 1;
		else
			$s_month[$i]["sel"] = 0;
	}
	$smarty->assign("s_month", $s_month);
	////year select
	if(intval($data["s_year"])==0) $data["s_year"] = date("Y");
	$s_year = array();
	for($i=0;$i<3;$i++){
		$y = intval(date("Y"))+1-$i;
		$s_year[$i]["value"] = $y;
		if(intval($data["s_year"]) == $y)
			$s_year[$i]["sel"] = 1;
		else
			$s_year[$i]["sel"] = 0;
	}
	$smarty->assign("s_year", $s_year);
	//hour
	$s_hour = array();
	for($i=0;$i<24;$i++){
		$s_hour[$i]["value"] = sprintf("%02d",$i);
		if($data["s_hour"] == $i)
			$s_hour[$i]["sel"] = 1;
		else
			$s_hour[$i]["sel"] = 0;
	}
	$smarty->assign("s_hour", $s_hour);
	//minute
	$s_min = array();
	for($i=0;$i<60;$i++){
		$s_min[$i]["value"] = sprintf("%02d",$i);
		if($data["s_min"] == $i)
			$s_min[$i]["sel"] = 1;
		else
			$s_min[$i]["sel"] = 0;
	}
	$smarty->assign("s_min", $s_min);

	//finish
	/// day select
	if(intval($data["f_day"])==0) $data["f_day"] = date("d");
	$f_day = array();
	for($i=0;$i<31;$i++){
		$f_day[$i]["value"] =sprintf("%02d",$i+1);
		if(intval($data["f_day"]) == $i+1)
			$f_day[$i]["sel"] = 1;
		else
			$f_day[$i]["sel"] = 0;
	}
	$smarty->assign("f_day", $f_day);
	////  month select
	if(intval($data["f_month"])==0) $data["f_month"] = date("m");
	$f_month = array();
	for($i=0;$i<12;$i++){
		$f_month[$i]["value"] = $i+1;
		$f_month[$i]["name"] = $lang["month"][$i+1];
		if(intval($data["f_month"]) == $i+1)
			$f_month[$i]["sel"] = 1;
		else
			$f_month[$i]["sel"] = 0;
	}
	$smarty->assign("f_month", $f_month);
	////year select
	if(intval($data["f_year"])==0) $data["f_year"] = date("Y");
	$f_year = array();
	for($i=0;$i<3;$i++){
		$y = intval(date("Y"))+1-$i;
		$f_year[$i]["value"] = $y;
		if(intval($data["f_year"]) == $y)
			$f_year[$i]["sel"] = 1;
		else
			$f_year[$i]["sel"] = 0;
	}
	$smarty->assign("f_year", $f_year);
	//hour
	$f_hour = array();
	for($i=0;$i<24;$i++){
		$f_hour[$i]["value"] = sprintf("%02d",$i);
		if($data["f_hour"] == $i)
			$f_hour[$i]["sel"] = 1;
		else
			$f_hour[$i]["sel"] = 0;
	}
	$smarty->assign("f_hour", $f_hour);
	//minute
	$f_min = array();
	for($i=0;$i<60;$i++){
		$f_min[$i]["value"] = sprintf("%02d",$i);
		if($data["f_min"] == $i)
			$f_min[$i]["sel"] = 1;
		else
			$f_min[$i]["sel"] = 0;
	}
	$smarty->assign("f_min", $f_min);

	//finish
	/// day select
	$d_day = array();
	for($i=0;$i<31;$i++){
		$d_day[$i]["value"] =sprintf("%02d",$i+1);
		if(intval($data["d_day"]) == $i+1)
			$d_day[$i]["sel"] = 1;
		else
			$d_day[$i]["sel"] = 0;
	}
	$smarty->assign("d_day", $d_day);
	////  month select
	$d_month = array();
	for($i=0;$i<12;$i++){
		$d_month[$i]["value"] = $i+1;
		$d_month[$i]["name"] = $lang["month"][$i+1];
		if(intval($data["d_month"]) == $i+1)
			$d_month[$i]["sel"] = 1;
		else
			$d_month[$i]["sel"] = 0;
	}
	$smarty->assign("d_month", $d_month);
	////year select
	$d_year = array();
	for($i=0;$i<3;$i++){
		$y = intval(date("Y"))+1-$i;
		$d_year[$i]["value"] = $y;
		if(intval($data["d_year"]) == $y)
			$d_year[$i]["sel"] = 1;
		else
			$d_year[$i]["sel"] = 0;
	}
	$smarty->assign("d_year", $d_year);

	if ($err) {
		$smarty->assign("err",$err);
		$smarty->assign("data", $data);
		$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_event_form.tpl");
	} else {
		if ($par=='add'){
			$strSQL = "INSERT INTO ".EVENT_DESCR_TABLE."
						(type, id_creator, id_country, id_region, id_city, creation_date, start_date, end_date, periodicity, die_date, event_name, event_place, event_contain, can_invite, can_post_images)
					VALUES
						('".intval($data["type"])."', '".$auth[0]."', '".intval($data["country"])."', '".intval($data["region"])."', '".intval($data["city"])."',
						now(), '".$data["start_date"]."', '".$data["finish_date"]."', '".$data["periodicity"]."', '".$data["die_date"]."',
						'".addslashes(strip_tags(trim($data["name"])))."', '".addslashes(strip_tags(trim($data["place"])))."', '".addslashes(htmlspecialchars(strip_tags($data["contain"])))."',
						'".intval($data["members_can_invite"])."', '".intval($data["members_can_post_images"])."')";
			$dbconn->Execute($strSQL);
			$rs = $dbconn->Execute("SELECT MAX(id) FROM ".EVENT_DESCR_TABLE." WHERE id_creator='".$auth[0]."' ");
			$id_event = $rs->fields[0];
			$dbconn->Execute("INSERT INTO ".EVENT_USERS_TABLE." (id_user, id_event) VALUES ('".$auth[0]."','".$id_event."') ");
		} elseif ($par=='save'){
			$strSQL = "UPDATE ".EVENT_DESCR_TABLE."
					SET type='".intval($data["type"])."', id_country='".intval($data["country"])."', id_region='".intval($data["region"])."', id_city='".intval($data["city"])."',
					start_date='".$data["start_date"]."', end_date='".$data["finish_date"]."', periodicity='".$data["periodicity"]."', die_date='".$data["die_date"]."',
					event_name='".addslashes(strip_tags(trim($data["name"])))."', event_place='".addslashes(strip_tags(trim($data["place"])))."', event_contain='".addslashes(htmlspecialchars(strip_tags($data["contain"])))."',
					can_invite='".intval($data["members_can_invite"])."', can_post_images='".intval($data["members_can_post_images"])."'
					WHERE id='".intval($_POST["id_event"])."' ";
			$dbconn->Execute($strSQL);
			$id_event = intval($_POST["id_event"]);
		}
		if(is_uploaded_file($flyer_file["tmp_name"])){
			$folder = GetSiteSettings("events_uploads_folder");
			move_uploaded_file($flyer_file["tmp_name"], $config["site_path"].$folder."/".$id_event."_".$flyer_file["name"]);
			$strSQL = "UPDATE ".EVENT_DESCR_TABLE."	SET flyer='".$flyer_file["name"]."'	WHERE id='".$id_event."' ";
			$dbconn->Execute($strSQL);
		}

		ListEvents();
	}
}

function DeleteEvent()
{
	global $dbconn, $config, $auth;
	$id_event = $_GET["id"]?$_GET["id"]:$_POST["id"];
	$strSQL = " SELECT id, flyer FROM ".EVENT_DESCR_TABLE." WHERE id='".$id_event."' AND id_creator='".$auth[0]."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>0) {
		$folder = GetSiteSettings('events_uploads_folder');
		if ($rs->fields[1]) {
			unlink($config['site_path'].$folder."/".$id_event."_".$rs->fields[1]);
		}
		$dbconn->Execute("DELETE FROM ".EVENT_DESCR_TABLE." WHERE id='".$id_event."' ");
		$dbconn->Execute("DELETE FROM ".EVENT_USERS_TABLE." WHERE id_event='".$id_event."' ");
		$dbconn->Execute("DELETE FROM ".EVENT_INVITES_TABLE." WHERE id_event='".$id_event."' ");
		$rs = $dbconn->Execute(" SELECT DISTINCT id, upload_path FROM ".EVENT_UPLOADS_TABLE." WHERE id_event='".$id_event."' GROUP BY id ");
		if ($rs->fields[0]>0) {
			while(!$rs->EOF){
				unlink($config['site_path'].$folder."/".$rs->fields[1]);
				unlink($config['site_path'].$folder."/thumb_".$rs->fields[1]);
				$rs->MoveNext();
			}
		}
		$dbconn->Execute("DELETE FROM ".EVENT_UPLOADS_TABLE." WHERE id_event='".$id_event."' ");
	}
	ListEvents();
	exit;
}

function UserForm($err="")
{
	global $smarty, $dbconn, $config, $page, $lang;

	if(isset($_SERVER["PHP_SELF"]))
		$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
		$file_name = "admin_events.php";

	AdminMainMenu();

	$page = $_POST["page"]?$_POST["page"]:$_GET["page"];
	if(!intval($page)) $page=1;

	$id_event = intval($_GET["id_event"])?intval($_GET["id_event"]):intval($_POST["id_event"]);
	$search = strval(strip_tags($_POST["search"]));
	$s_type = intval($_POST["s_type"]);
	if(!$search) $search = $lang["groups"]["initial_word"];
	///////// search
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
	$search_str .= " and root_user != '1' and guest_user != '1'";
	$search_hiddens = "<input type=hidden name=id_event value=".$id_event.">";
	$search_hiddens .= "<input type=hidden name=page value=".$page.">";
	$search_hiddens .= "<input type=hidden name=IncSUsers value=\"\">";
	$search_hiddens .= "<input type=hidden name=sel value=user>";
	///////// search form
	$types = array();
	for($i=0;$i<4;$i++){
		if($s_type==($i+1))$types[$i]["sel"]="1";
		$types[$i]["value"]=$lang["users"]["type_".($i+1)];
	}
	$smarty->assign("types", $types);
	$smarty->assign("search", $search);
	$smarty->assign("search_hiddens", $search_hiddens);

	////////// event name
	$rs = $dbconn->Execute("select event_name from ".EVENT_DESCR_TABLE." where id='".$id_event."'");
	$form["eventname"] = $rs->fields[0];

	/////////////// event users (who is joined our event)
	//// if something coming from search form
	if($_POST["search"] && $_POST["IncSUsers"]){
		$strSQL = "select a.id, CONCAT(a.sname,' ',a.fname, ' (', a.login,')') as username from ".USERS_TABLE." a where  a.id in (".substr($_POST["IncSUsers"],0,-2).") group by a.id order by username";
	}else{
		$strSQL = "select a.id, CONCAT(a.sname,' ',a.fname, ' (', a.login,')') as username from ".USERS_TABLE." a, ".EVENT_USERS_TABLE." b where b.id_user=a.id and b.id_event='".$id_event."' group by a.id order by username";
	}
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$gusers_arr = array();
	while(!$rs->EOF){
		$gusers_arr[$i]["value"] = $rs->fields[0];
		$gusers_arr[$i]["name"] = $rs->fields[1];
		$all_str .= $rs->fields[0].", ";
		$rs->MoveNext();
		$i++;
	}
	//////////////// make a list of old users
	$strSQL = "select a.id from ".USERS_TABLE." a, ".EVENT_USERS_TABLE." b where b.id_user=a.id and b.id_event='".$id_event."'";
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$goldusers_arr = array();
	while (!$rs->EOF) {
		$goldusers_arr[$i]["value"] = $rs->fields[0];
		$rs->MoveNext();
		$i++;
	}

	if(strval($all_str) == ""){
		$in_str = "";
	}else{
		$in_str = " and a.id not in (".substr($all_str,0,-2).")";
	}
	/////////////// all users (whos not in event and content search str)
	$strSQL = "select a.id, CONCAT(a.sname,' ',a.fname, ' (', a.login,')') as username from ".USERS_TABLE." a where ".$search_str." ".$in_str." group by a.id";
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$allusers_arr = array();
	while (!$rs->EOF) {
		$allusers_arr[$i]["value"] = $rs->fields[0];
		$allusers_arr[$i]["name"] = $rs->fields[1];
		$rs->MoveNext();
		$i++;
	}

	$form["action"] = $file_name;
	$form["err"] = $err;

	$form["hiddens"] = "<input type=hidden name=id_event value=".$id_event.">";
	$form["hiddens"] .= "<input type=hidden name=sel value=eventuser>";
	$form["hiddens"] .= "<input type=hidden name=page value=".$page.">";
	$form["back"] = $file_name."?page=".$page;

	$smarty->assign("allusers_arr", $allusers_arr);
	$smarty->assign("gusers_arr", $gusers_arr);
	$smarty->assign("goldusers_arr", $goldusers_arr);

	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["events"]);
	$smarty->assign("button", $lang["button"]);
	$smarty->assign("header_group", $lang["groups"]);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_events_users_form.tpl");
	exit;
}

function UserChange()
{
	global $dbconn;
	
	$id_event = $_POST["id_event"]?$_POST["id_event"]:$_GET["id_event"];

	if(!$id_event){ ListEvents(); return;}

	$delete_arr = array();
	$values_arr = array();
	$prevUsers = is_array($_POST["prevusers"])?array_unique($_POST["prevusers"]):"";
	$IncUsers =  substr($_POST["IncUsers"],0,-1);
	$nextUsers = explode(",", $IncUsers);
	$nextUsers =  is_array($nextUsers)?array_unique($nextUsers):"";

	if(!is_array($prevUsers)) $prevUsers = array();
	if(!is_array($nextUsers)) $nextUsers = array();
	if(is_array($nextUsers) && count($nextUsers)==0 && is_array($prevUsers) && count($prevUsers)==0){
		ListEvents(); return;
	}

	for($i=0; $i<count($prevUsers); $i++){
		if(!in_array($prevUsers[$i], $nextUsers) && $prevUsers[$i]!=0){	/// if element not in array (old user not in new list) delete him from table
			array_push($delete_arr, $prevUsers[$i]);
		}
	}
	for($i=0; $i<count($nextUsers); $i++){
		if(!in_array($nextUsers[$i], $prevUsers) && $nextUsers[$i]!=0){	/// if element not in array (new user not in old list) add him into table
			array_push($delete_arr, $nextUsers[$i]);
			array_push($values_arr, " ( '".$nextUsers[$i]."', '".$id_event."')");
			$rs = $dbconn->Execute("insert into  ".EVENT_USERS_TABLE." (id_user, id_event) values ( '".$nextUsers[$i]."', '".$id_event."')");
		}
	}
	$values_str = implode(", ", $values_arr);
	$delete_str = implode(", ", $delete_arr);
	$delete_arr = explode(", ",$delete_str);

	if(count($delete_arr)>0) {
		$i=0;
		while ($i<count($delete_arr)) {
			$dbconn->Execute("delete from ".EVENT_USERS_TABLE." where id_event='".$id_event."' AND id_user='".$delete_arr[$i]."' ");
			$i++;
		}

	}
	if(strlen($values_str)>0)
		$rs = $dbconn->Execute("insert into  ".EVENT_USERS_TABLE." (id_user, id_event) values ".$values_str);

	ListEvents(); return;
}

function ListEventTypes($err="")
{
	global $smarty, $dbconn, $config, $config_admin, $page, $lang;

	if(isset($_SERVER["PHP_SELF"]))
		$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
		$file_name = "admin_events.php";

	AdminMainMenu();
	$page = $_GET["page"]?$_GET["page"]:$_POST["page"];
	if( (strval($page) == "") || (strval($page) == "0")){ $page = 1; }else{	$page=intval($page);}

	///types per page
	$strSQL = "SELECT count(*) FROM ".EVENT_TYPES_TABLE;
	$rs = $dbconn->Execute($strSQL);
	$num_records = $rs->fields[0];

	$lim_min = ($page-1)*$config_admin["users_numpage"];
	$lim_max = $config_admin["users_numpage"];
	$limit_str = "limit ".$lim_min.", ".$lim_max;
	$smarty->assign("page", $page);

	$param = $file_name."?";
	$smarty->assign("links", GetLinkStr($num_records,$page,$param,$config_admin["users_numpage"]));

	$strSQL = "select DISTINCT id, name FROM ".EVENT_TYPES_TABLE." ORDER BY name ".$limit_str;

	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$types = array();
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$types[$i]["number"] = $i+1;
		$types[$i]["id"] = $row["id"];
		$types[$i]["name"] = stripslashes($row["name"]);
		$types[$i]["editlink"] = $file_name."?sel=edittype&id=".$row["id"];
		$types[$i]["deletelink"] = $file_name."?sel=deltype&id=".$row["id"];
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign("types", $types);

	$smarty->assign("add_link", $file_name."?sel=addtype");
	$smarty->assign("back_link", $file_name);
	$form["err"] = $err;
	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["events"]);
	$smarty->assign("button", $lang["button"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_event_types_table.tpl");
	exit;
}

function EditEventType($par)
{
	global $smarty, $dbconn, $config, $page, $lang;
	
	if(isset($_SERVER["PHP_SELF"]))
		$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
		$file_name = "admin_events.php";

	AdminMainMenu();
	$page = $_GET["page"]?$_GET["page"]:$_POST["page"];
	if( (strval($page) == "") || (strval($page) == "0")){ $page = 1; }else{	$page=intval($page);}
	$form["action"] = $file_name;
	$par = $par ? $par : "edit";
	$form["par"] = $par;
	$form["back"] = $file_name."?sel=types";
	$form["hiddens"] = "<input type=hidden name=sel value=>";
	$form["hiddens"] .= "<input type=hidden name=id_type value='".$_GET["id"]."'>";
	$form["delete_link"] = $file_name."?sel=deltype&id=".$_GET["id"];
	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["events"]);
	$smarty->assign("button", $lang["button"]);

	if ($_POST["sel"]=='addtype') {
		AddEventType($par='add');
	} elseif ($_POST["sel"]=='savetype') {
		AddEventType($par='save');
	} else {
		if ($_GET["id"]) {
			$strSQL = "select id, name FROM ".EVENT_TYPES_TABLE." WHERE id = '".intval($_GET["id"])."'";
			$rs = $dbconn->Execute($strSQL);
			$row = $rs->GetRowAssoc(false);
			$data["id"] = $row["id"];
			$data["name"] = stripslashes($row["name"]);
		}
		$smarty->assign("data", $data);
		$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_event_types_form.tpl");
		exit;
	}
}

function AddEventType($par)
{
	global $smarty, $dbconn, $config, $lang;
	
	$name = $_POST["name"];

	/// check empty values
	if (strip_tags(trim($name)) == '') {
		$err = $lang["err"]["invalid_fields"];
	}

	$strSQL = "SELECT id FROM ".EVENT_TYPES_TABLE." WHERE name = '".addslashes(strip_tags(trim($name)))."'";
	$rs = $dbconn->Execute($strSQL);
	if($rs->fields[0]){
		$err = $lang["err"]["event_type_exists"];
	}

	$data["name"] = $name;

	if ($err) {
		$smarty->assign("err",$err);
		$smarty->assign("data", $data);
		$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_event_types_form.tpl");
	} else {
		if ($par=='add'){
			$strSQL = "INSERT INTO ".EVENT_TYPES_TABLE." (name)	VALUES ('".addslashes(strip_tags(trim($data["name"])))."')";
			$dbconn->Execute($strSQL);
		} elseif ($par=='save'){
			$strSQL = "UPDATE ".EVENT_TYPES_TABLE." SET name='".addslashes(strip_tags(trim($data["name"])))."' WHERE id='".intval($_POST["id_type"])."' ";
			$dbconn->Execute($strSQL);
		}

		ListEventTypes();
	}
}

function DeleteEventType()
{
	global $dbconn;
	
	$id_type = $_GET["id"]?$_GET["id"]:$_POST["id"];
	
	$dbconn->Execute("UPDATE ".EVENT_TYPES_TABLE." SET type='0' WHERE type='".$id_type."' ");
	$dbconn->Execute("DELETE FROM ".EVENT_TYPES_TABLE." WHERE id='".$id_type."' ");
	
	ListEventTypes();
	exit;
}
?>