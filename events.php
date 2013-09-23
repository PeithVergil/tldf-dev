<?php
/**
* Events module
*
* @package DatingPro
* @subpackage User Mode
**/

include './include/config.php';
include './common.php';
include './include/config_index.php';
include './include/config_admin.php';
include './include/functions_auth.php';
include './include/functions_index.php';
include './include/functions_events.php';
include './include/class.lang.php';
include './include/class.percent.php';
include './include/class.images.php';

// authentication
$user = auth_index_user();

if (empty($user) || $user == 'err' || empty($user[ AUTH_ID_USER ])) {
	header('location: '.$config['site_root'].'/index.php');
	exit;
}

// check guest
// (handled by permissions)

// check group, period, expiration
RefreshAccount();

// check status
if (!$user[ AUTH_STATUS ]) {
	AlertPage(GetRightModulePath(__FILE__));
}

// check permissions
IsFileAllowed(GetRightModulePath(__FILE__));

// alerts and statistics
if (!$user[ AUTH_GUEST ]) {
	GetAlertsMessage();
	SetModuleStatistic(GetRightModulePath(__FILE__));
}

// active menu item
$smarty->assign('sub_menu_num', '');

unset($_SESSION['return_to_view']);

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

// dispatcher
switch ($sel) {
	case "search":			EventsTable(); break;
	case "today":			EventsTable(); break;
	case "join":			JoinEvent(); break;
	case "leave":			LeaveEvent(); break;
	case "date":			DateEvent(); break;
	case "event":			EventTable(); break;
	case "create":			CreateForm(); break;
	case "add":				AddEvent(); break;
	case "invite":			InviteUser(); break;
	case "upload_image":	UploadImage(); break;
	case "upload_view":		UploadView(); break;
	case "upload_del":		UploadDelete(); break;
	case "add_comment":		AddEventComment(); break;
	case "delete_comment":	DeleteEventComment(); break;
	case "more_comments":	ListMore('comments'); break;
	case "more_users":		ListMore('users'); break;
	case "more_photos":		ListMore('photos'); break;
	case "delete_event":	DeleteEvent(); break;
	default: 				EventsTable();
}

function EventsTable($today="")
{
	global $lang, $config, $smarty, $dbconn, $user, $file_name;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$file_name = "events.php";

	$today = $today ? $today : date("Y-m-d");

	$filter = "";
	if (isset($_REQUEST["id_country"]) && $_REQUEST["id_country"]) {
		$filter .= " AND a.id_country = ".$_REQUEST["id_country"];
		$form["id_country"] = $_REQUEST["id_country"];
	}
	if (isset($_REQUEST["id_region"]) && $_REQUEST["id_region"]) {
		$filter .= " AND a.id_region = ".$_REQUEST["id_region"];
		$form["id_region"] = $_REQUEST["id_region"];
	}
	if (isset($_REQUEST["id_city"]) && $_REQUEST["id_city"]) {
		$filter .= " AND a.id_city = ".$_REQUEST["id_city"];
		$form["id_city"] = $_REQUEST["id_city"];
	}
	if (isset($_REQUEST["type"]) && $_REQUEST["type"]) {
		$filter .= " AND a.type = ".$_REQUEST["type"];
		$form["type"] = $_REQUEST["type"];
	}

	$strSQL = "select a.id, a.event_name, a.event_place, b.name as type, c.name as country, d.name as region, e.name as city,
			if(a.periodicity = 'none', DATE_FORMAT(a.start_date,'".$config["date_format"]." %H:%i'), DATE_FORMAT(ADDDATE(a.start_date,INTERVAL (TO_DAYS('".$today."') - TO_DAYS(a.start_date)) DAY),'".$config["date_format"]." %H:%i')) as date_start,
			if(a.periodicity = 'none', DATE_FORMAT(a.end_date,'".$config["date_format"]." %H:%i'), DATE_FORMAT(ADDDATE(a.end_date,INTERVAL (TO_DAYS('".$today."') - TO_DAYS(a.start_date)) DAY),'".$config["date_format"]." %H:%i')) as date_end,
			if(a.die_date = '0000-00-00','',DATE_FORMAT(a.die_date,'".$config["date_format"]."')) as date_die,
			a.periodicity FROM ".EVENT_DESCR_TABLE." a
			LEFT JOIN ".EVENT_TYPES_TABLE." b ON b.id = a.type
			LEFT JOIN ".COUNTRY_SPR_TABLE." c ON c.id = a.id_country
			LEFT JOIN ".REGION_SPR_TABLE." d ON d.id = a.id_region
			LEFT JOIN ".CITY_SPR_TABLE." e ON e.id = a.id_city
			WHERE ( (a.periodicity = 'none' AND DATE_FORMAT(a.start_date,'%Y-%m-%d') = DATE_FORMAT('".$today."','%Y-%m-%d'))
			OR (a.periodicity = 'daily' AND (DATE_FORMAT('".$today."','%Y-%m-%d') <= a.die_date OR a.die_date = '0000-00-00') AND DATE_FORMAT(a.start_date,'%Y-%m-%d') <= DATE_FORMAT('".$today."','%Y-%m-%d'))
			OR (a.periodicity = 'weekly' AND DAYOFWEEK(a.start_date) = DAYOFWEEK('".$today."') AND (DATE_FORMAT('".$today."','%Y-%m-%d') <= a.die_date OR a.die_date = '0000-00-00') AND DATE_FORMAT(a.start_date,'%Y-%m-%d') <= DATE_FORMAT('".$today."','%Y-%m-%d'))
			OR (a.periodicity = 'monthly' AND DAYOFMONTH(a.start_date) = DAYOFMONTH('".$today."') AND (DATE_FORMAT('".$today."','%Y-%m-%d') <= a.die_date OR a.die_date = '0000-00-00') AND DATE_FORMAT(a.start_date,'%Y-%m-%d') <= DATE_FORMAT('".$today."','%Y-%m-%d'))
			OR (a.periodicity = 'yearly' AND MONTH(a.start_date) = MONTH('".$today."') AND DAYOFMONTH(a.start_date) = DAYOFMONTH('".$today."') AND (DATE_FORMAT('".$today."','%Y-%m-%d') <= a.die_date OR a.die_date = '0000-00-00') AND DATE_FORMAT(a.start_date,'%Y-%m-%d') <= DATE_FORMAT('".$today."','%Y-%m-%d')) )".$filter;

	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$event = array();
	if ($rs->RowCount() > 0) {
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$event[$i]["id_event"] = $row["id"];
			$event[$i]["name"] = stripslashes($row["event_name"]);
			$event[$i]["type"] = stripslashes($row["type"]);
			$event[$i]["location"] = stripslashes($row["country"].", ".$row["region"].", ".$row["city"]);
			$event[$i]["place"] = stripslashes($row["event_place"]);
			$event[$i]["date_begin"] = $row["date_start"];
			$event[$i]["date_end"] = $row["date_end"];
			$event[$i]["periodicity"] = $lang["events"]["event_period"][$row["periodicity"]];
			if ($row["periodicity"] != "none" && $row["date_die"])
				$event[$i]["date_die"] = $row["date_die"];
			$rs2 = $dbconn->Execute("select id_user FROM ".EVENT_USERS_TABLE." WHERE id_event='".$event[$i]["id_event"]."' AND id_user='".$user[ AUTH_ID_USER ]."'");
			if (count($rs2->fields[0])>0) {
				$event[$i]["joined"] = true;
				$event[$i]["leave_link"] = "./events.php?sel=leave&id_event=".$event[$i]["id_event"];
			}else{
				$event[$i]["join_link"] = "./events.php?sel=join&id_event=".$event[$i]["id_event"];
			}
			$rs2 = $dbconn->Execute("SELECT count(id_user) FROM ".EVENT_USERS_TABLE." WHERE id_event='".$event[$i]["id_event"]."'");
			$event[$i]["num_users"] = $rs2->fields[0]?$rs2->fields[0]:0;
			$rs->MoveNext();
			$i++;
		}
	}
	$smarty->assign("event_path", "./events.php");
	$smarty->assign("calendar_link","./events_calendar.php");
	$smarty->assign("events", $event);
	if ($event[0]["date_begin"]) {
		$smarty->assign("date", substr($event[0]["date_begin"],0,10));
	} else {
		$smarty->assign("date", $lang["homepage"]["visit_my_page_1"]);
	}

	$strSQL = "SELECT DISTINCT id, name FROM ".COUNTRY_SPR_TABLE." GROUP BY id ORDER BY name ";
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$spr_arr = array();
	while(!$rs->EOF){
		$row = $rs->GetRowAssoc(false);
		$spr_arr[$i]["id"] = $row["id"];
		$spr_arr[$i]["name"] = stripslashes($row["name"]);
		if ($form["id_country"]==$spr_arr[$i]["id"]){
			$spr_arr[$i]["sel"] = 1;
		}
		$rs->MoveNext();
		$i++;
	}
	$form["countries"] = $spr_arr;

	if (isset($form["id_country"]) && $form["id_country"]){
		$strSQL = "SELECT DISTINCT id, name FROM ".REGION_SPR_TABLE." WHERE id_country='".$form["id_country"]."' ORDER BY name";
		$rs = $dbconn->Execute($strSQL);
		$i = 0;
		$spr_arr = array();
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$spr_arr[$i]["id"] = $row["id"];
			$spr_arr[$i]["name"] = stripslashes($row["name"]);
			if (isset($form["id_region"]) && $form["id_region"] == $spr_arr[$i]["id"])
				$spr_arr[$i]["sel"] = 1;
			$rs->MoveNext();
			$i++;
		}
		$form["regions"] = $spr_arr;
	}

	if (isset($form["id_region"]) && $form["id_region"]){
		$strSQL = "SELECT DISTINCT id, name FROM ".CITY_SPR_TABLE." WHERE id_region='".$form["id_region"]."' ORDER BY id";
		$rs = $dbconn->Execute($strSQL);
		$i = 0;
		$spr_arr = array();
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$spr_arr[$i]["id"] = $row["id"];
			$spr_arr[$i]["name"] = stripslashes($row["name"]);
			if (isset($form["id_city"]) && $form["id_city"] == $spr_arr[$i]["id"])
				$spr_arr[$i]["sel"] = 1;
			$rs->MoveNext();
			$i++;
		}
		$form["cities"] = $spr_arr;
	}

	$strSQL = "SELECT DISTINCT id, name FROM ".EVENT_TYPES_TABLE." GROUP BY id ORDER BY id ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>0){
		$i = 0;
		$type_arr = array();
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$type_arr[$i]["id"] = $row["id"];
			if (isset($form["type"]) && $type_arr[$i]["id"]==$form["type"]){
				$type_arr[$i]["sel"] = 1;
			}
			$type_arr[$i]["name"] = stripslashes($row["name"]);
			$rs->MoveNext();
			$i++;
		}
		$form["types"] = $type_arr;
	}

    ob_start();
    $_REQUEST["act"] = "ajax";
    require "events_calendar.php";
    $calendar_out_put = ob_get_contents();
    ob_end_clean();
    $smarty->assign("calendar_out_put", $calendar_out_put);

	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["events"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/events_table.tpl");
}

function JoinEvent()
{
	global $dbconn, $user;
	
	$id_event = $_POST["id_event"]?$_POST["id_event"]:$_GET["id_event"];
	$rs = $dbconn->Execute("SELECT id_user FROM ".EVENT_USERS_TABLE." WHERE id_event='".$id_event."' AND id_user='".$user[ AUTH_ID_USER ]."'");
	if (count($rs->fields[0]) <= 0) {
		$dbconn->Execute("INSERT INTO ".EVENT_USERS_TABLE." (id_event, id_user) VALUES ('".$id_event."','".$user[ AUTH_ID_USER ]."') ");
		EventTable('you_joined_to_event', $id_event);
	} else {
		EventTable('you_are_joined_event', $id_event);
	}
}

function LeaveEvent()
{
	global $dbconn, $user;
	
	$id_event = $_POST["id_event"]?$_POST["id_event"]:$_GET["id_event"];

	$rs = $dbconn->Execute(" SELECT id_creator FROM ".EVENT_DESCR_TABLE." WHERE id='".$id_event."' ");
	if ($rs->fields[0] == $user[ AUTH_ID_USER ]) {
		EventTable('creator_cant_leave', $id_event);
	}
	$dbconn->Execute("DELETE FROM ".EVENT_USERS_TABLE." WHERE id_event='".$id_event."' AND id_user='".$user[ AUTH_ID_USER ]."' ");

	$rs = $dbconn->Execute("SELECT start_date FROM ".EVENT_DESCR_TABLE." WHERE id='".$id_event."'");
	$today = substr($rs->fields[0],0,10);
	EventsTable($today);
}

function DateEvent()
{
	$year = $_POST["year"]?$_POST["year"]:$_GET["year"];
	$month = $_POST["month"]?$_POST["month"]:$_GET["month"];
	$day = $_POST["day"]?$_POST["day"]:$_GET["day"];
	$today = $year."-".$month."-".$day;
	EventsTable($today);
}

function EventTable($err='', $id_event='', $show_upload_form='', $show_comment_form='', $data='')
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	if ($id_event !='') {
		$event_id = intval($id_event);
	} else {
		$event_id = intval($_REQUEST["id_event"]);
	}

	$file_name = "events.php";

	$settings = GetSiteSettings(array('icon_male_default','icon_female_default','show_users_connection_str','show_users_comments','show_users_group_str','photos_default','events_uploads_folder','thumb_max_width','icons_folder','photos_folder'));

	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];

	$strSQL = "select a.id, a.event_name, a.event_place, b.name as type, c.name as country, d.name as region, e.name as city,
			DATE_FORMAT(a.start_date,'".$config["date_format"]." %H:%i') as date_start,
			DATE_FORMAT(a.end_date,'".$config["date_format"]." %H:%i') as date_end,
			if(a.die_date = '0000-00-00','',DATE_FORMAT(a.die_date,'".$config["date_format"]."')) as date_die,
			a.periodicity, a.event_contain, f.login as event_creator, f.icon_path as creator_icon_path, f.gender, f.date_birthday, a.id_creator as creator_id,
			a.can_invite, a.can_post_images, a.flyer FROM ".EVENT_DESCR_TABLE." a
			LEFT JOIN ".EVENT_TYPES_TABLE." b ON b.id = a.type
			LEFT JOIN ".COUNTRY_SPR_TABLE." c ON c.id = a.id_country
			LEFT JOIN ".REGION_SPR_TABLE." d ON d.id = a.id_region
			LEFT JOIN ".CITY_SPR_TABLE." e ON e.id = a.id_city
			LEFT JOIN ".USERS_TABLE." f ON f.id = a.id_creator
			WHERE a.id = '".$event_id."'";

	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>0){
		$row = $rs->GetRowAssoc(false);
		$event["id"] = $row["id"];
		$event["event_name"] = stripslashes($row["event_name"]);
		$event["type"] = stripslashes($row["type"]);
		$event["location"] = stripslashes($row["country"].", ".$row["region"].", ".$row["city"]);
		$event["place"] = stripslashes($row["event_place"]);
		$event["can_invite"] = $row["can_invite"];
		$event["can_post_images"] = $row["can_post_images"];
		$event["description"] = stripslashes($row["event_contain"]);
		$event["date_begin"] = $row["date_start"];
		$event["date_end"] = $row["date_end"];
		$event["periodicity"] = $lang["events"]["event_period"][$row["periodicity"]];
		if ($row["periodicity"] != "none" && $row["date_die"])
			$event["date_die"] = $row["date_die"];
		if($row["flyer"] && file_exists($config["site_path"].$settings["events_uploads_folder"]."/".$event["id"]."_".$row["flyer"])){
			$event["flyer"] = $config["site_root"].$settings["events_uploads_folder"]."/".$event["id"]."_".$row["flyer"];
		} else {
			$event["flyer"] = '';
		}
		$event["event_creator"] = stripslashes($row["event_creator"]);
		$event["creator_id"] = $row["creator_id"];
		$creator_icon_path = $row["creator_icon_path"]?$row["creator_icon_path"]:$default_photos[$row["gender"]];

		if($creator_icon_path && file_exists($config["site_path"].$settings["icons_folder"]."/".$creator_icon_path)){
			$event["creator_icon"] = $config["site_root"].$settings["icons_folder"]."/".$creator_icon_path;
		} else {
			$event["creator_icon"] = $config["server"].$config["site_root"].$settings["photos_folder"]."/".$settings["photos_default"];
		}

		$event["event_creator_age"] = AgeFromBDate($row["date_birthday"]);
		$event["event_creator_country"] = stripslashes($row["country"]);
		$event["event_creator_region"] = stripslashes($row["region"]);
		$event["event_creator_city"] = stripslashes($row["city"]);
		$_LANG_NEED_ID["country"][] = intval($row["id_country"]);
		$_LANG_NEED_ID["region"][] = intval($row["id_region"]);
		$_LANG_NEED_ID["city"][] = intval($row["id_city"]);
		$event["creator_profile_link"] = "./viewprofile.php?id=".$event["creator_id"];

		$event["leave_link"] = $file_name."?sel=leave&id_event=".$event["id"];
		$event["join_link"] = $file_name."?sel=join&id_event=".$event["id"];
		$event["invite_link"] = "hotlist.php?sel=invite&id_event=".$event["id"];
		$event["upload_link"] = $file_name."?sel=upload_image&id_event=".$event["id"];
		$event["delete_link"] = $file_name."?sel=delete_event&id_event=".$event["id"];
		$event["add_comment_link"] = $file_name."?sel=add_comment&id_event=".$event["id"];

		if ($event["creator_id"] == $user[ AUTH_ID_USER ]) {
			$event["user_is_creator"] = 1;
			$event["user_join_event"] = 1;
		} else {
			$event["user_is_creator"] = 0;
			$strSQL = "SELECT COUNT(*) FROM ".EVENT_USERS_TABLE." WHERE id_event='".$event["id"]."' AND id_user='".$user[ AUTH_ID_USER ]."'";
			$rs_c = $dbconn->Execute($strSQL);
			if ($rs_c->fields[0]>0){
				$event["user_join_event"] = 1;
			} else {
				$event["user_join_event"] = 0;
			}
		}
		$strSQL = "SELECT COUNT(*) FROM ".EVENT_USERS_TABLE." WHERE id_event='".$event["id"]."'";
		$rs_c = $dbconn->Execute($strSQL);
		$event["members_count"] = $rs_c->fields[0];

		$strSQL = "	SELECT DISTINCT a.id_user, b.login, b.icon_path, b.gender, b.date_birthday, b.id_country, b.id_city, b.id_region
					FROM ".EVENT_USERS_TABLE." a
					LEFT JOIN ".USERS_TABLE." b ON b.id=a.id_user
					WHERE a.id_event='".$event_id."' AND a.id_user!='".$event["creator_id"]."' AND a.id_user!='".$user[ AUTH_ID_USER ]."'
					GROUP BY a.id_user LIMIT 0,5 ";
		$rs_users = $dbconn->Execute($strSQL);
		if ($rs_users->fields[0] > 0) {
			$i = 0;
			$event_users = array();
			$_LANG_NEED_ID = array();
			
			while (!$rs_users->EOF) {
				$row_users = $rs_users->GetRowAssoc(false);
				$event_users[$i]["id_user"] = $row_users["id_user"];
				$event_users[$i]["login"] = stripslashes($row_users["login"]);
				$event_users[$i]["age"] = AgeFromBDate($row_users["date_birthday"]);
				$event_users[$i]["id_country"] = intval($row_users["id_country"]);
				$event_users[$i]["id_region"] = intval($row_users["id_region"]);
				$event_users[$i]["id_city"] = intval($row_users["id_city"]);
				$_LANG_NEED_ID["country"][] = intval($row_users["id_country"]);
				$_LANG_NEED_ID["region"][] = intval($row_users["id_region"]);
				$_LANG_NEED_ID["city"][] = intval($row_users["id_city"]);

				$icon_path = $row_users["icon_path"]?$row_users["icon_path"]:$default_photos[$row_users["gender"]];
				if($icon_path && file_exists($config["site_path"].$settings["icons_folder"]."/".$icon_path)){
					$event_users[$i]["icon"] = $config["server"].$config["site_root"].$settings["icons_folder"]."/".$icon_path;
				} else {
					$event_users[$i]["icon"] = $config["server"].$config["site_root"].$settings["photos_folder"]."/".$settings["photos_default"];
				}
				$event_users[$i]["profile_link"] = "./viewprofile.php?id=".$event_users[$i]["id_user"];
				$rs_users->MoveNext();
				$i++;
			}
			$smarty->assign("event_users_num", sizeof($event_users));
			$smarty->assign("event_users", $event_users);
		}
		$strSQL = "SELECT COUNT(*) FROM ".EVENT_USERS_TABLE." WHERE id_event='".$event_id."' AND id_user!='".$event["creator_id"]."' AND id_user!='".$user[ AUTH_ID_USER ]."' " ;
		$rs_c = $dbconn->Execute($strSQL);
		$event["members_count_2"] = $rs_c->fields[0];
		if ($event["members_count_2"]>sizeof($event_users)){
			$event["link_more_users"] = $file_name."?sel=more_users&id_event=".$event["id"];
		}

		$strSQL = "SELECT COUNT(*) FROM ".EVENT_UPLOADS_TABLE." WHERE id_event='".$event["id"]."'";
		$rs_p = $dbconn->Execute($strSQL);
		$event["photos_count"] = $rs_p->fields[0];

		$strSQL = "	SELECT DISTINCT a.id, b.login, a.upload_path, a.comment, a.id_user
					FROM ".EVENT_UPLOADS_TABLE." a
					LEFT JOIN ".USERS_TABLE." b ON b.id=a.id_user
					WHERE a.status='1' AND a.id_event='".$event_id."'
					GROUP BY a.id ORDER BY a.id DESC LIMIT 0,5 ";
		$rs_photos = $dbconn->Execute($strSQL);
		if ($rs_photos->fields[0]>0){
			$i = 0;
			$event_photos = array();
			while(!$rs_photos->EOF){
				$row_photos = $rs_photos->GetRowAssoc(false);
				$event_photos[$i]["id"] = $row_photos["id"];
				$event_photos[$i]["id_user"] = $row_photos["id_user"];
				$event_photos[$i]["login"] = stripslashes($row_photos["login"]);
				$event_photos[$i]["comment"] = stripslashes($row_photos["comment"]);
				$event_photos[$i]["user_upload"] = ($event_photos[$i]["id_user"] == $user[ AUTH_ID_USER ]) ? 1 : 0;
				$icon_path = $row_photos["upload_path"];
				if($icon_path && file_exists($config["site_path"].$settings["events_uploads_folder"]."/".$icon_path)){
					$event_photos[$i]["upload_path"] = $config["server"].$config["site_root"].$settings["events_uploads_folder"]."/".$icon_path;
					$event_photos[$i]["upload_thumb_path"] = $config["server"].$config["site_root"].$settings["events_uploads_folder"]."/thumb_".$icon_path;
					$event_photos[$i]["view_link"] = $file_name."?sel=upload_view&id_file=".$event_photos[$i]["id"];
					$event_photos[$i]["del_link"] = $file_name."?sel=upload_del&id_file=".$event_photos[$i]["id"];
				} else {
					$event_photos[$i]["upload_path"] = $config["server"].$config["site_root"].$settings["events_uploads_folder"]."/".$settings["photos_default"];
					$event_photos[$i]["upload_thumb_path"] = $config["server"].$config["site_root"].$settings["events_uploads_folder"]."/".$settings["photos_default"];
				}
				$rs_photos->MoveNext();
				$i++;
			}
			$smarty->assign("event_photos_num", sizeof($event_photos));
			$smarty->assign("event_photos", $event_photos);
		}
		if ($event["photos_count"]>sizeof($event_photos)){
			$event["link_more_photos"] = $file_name."?sel=more_photos&id_event=".$event["id"];
		}

		$strSQL = "SELECT COUNT(*) FROM ".EVENT_COMMENTS_TABLE." WHERE id_event='".$event["id"]."' ";
		$rs_comm = $dbconn->Execute($strSQL);
		$event["comments_count"] = $rs_comm->fields[0];
		$strSQL = "	select distinct a.id, a.comment, a.type, DATE_FORMAT(a.creation_date,'".$config["date_format"]."') as date, a.id_user, b.login, b.gender, b.date_birthday, b.icon_path, c.name as country, d.name as city, r.name as region, e.id_user as session
					from ".EVENT_COMMENTS_TABLE." a, ".USERS_TABLE." b
					left join ".COUNTRY_SPR_TABLE." c on c.id=b.id_country
					left join ".CITY_SPR_TABLE." d on d.id=b.id_city
					left join ".ACTIVE_SESSIONS_TABLE." e on b.id=e.id_user
					left join ".REGION_SPR_TABLE." r on r.id=b.id_region
					where a.id_event='".$event["id"]."' and a.id_user=b.id group by a.id order by a.creation_date desc limit 0,5";
		$rs_comm = $dbconn->Execute($strSQL);
		if ($rs_comm->fields[0]>0){
			$i = 0;
			$event_comm = array();
			while(!$rs_comm->EOF){
				$row_comm = $rs_comm->GetRowAssoc(false);
				$event_comm[$i]["comment"] = stripslashes(nl2br($row_comm["comment"]));
				$event_comm[$i]["type"] = $lang["events"][$row_comm["type"]];
				$event_comm[$i]["delete_link"] = ($row_comm["id_user"] == $user[ AUTH_ID_USER ]) ? "./events.php?sel=delete_comment&id=".$row_comm["id"] : "";
				$event_comm[$i]["date"] = $row_comm["date"];
				$event_comm[$i]["name"] = $row_comm["login"];
				$event_comm[$i]["profile_link"] = ($row_comm["id_user"] != $user[ AUTH_ID_USER ]) ? "./viewprofile.php?id=".$row_comm["id_user"] : "";
				$event_comm[$i]["status"] = $row_comm["session"]?$lang["status"]["on"]:$lang["status"]["off"];
				$event_comm[$i]["age"] = AgeFromBDate($row_comm["date_birthday"]);
				$event_comm[$i]["country"] = stripslashes($row_comm["country"]);
				$event_comm[$i]["region"] = stripslashes($row_comm["region"]);
				$event_comm[$i]["city"] = stripslashes($row_comm["city"]);

				$icon_path = $row_comm["icon_path"]?$row_comm["icon_path"]:$default_photos[$row_users["gender"]];

				if($icon_path && file_exists($config["site_path"].$settings["icons_folder"]."/".$icon_path)){
					$event_comm[$i]["icon_path"] = $config["site_root"].$settings["icons_folder"]."/".$icon_path;
				} else {
					$event_comm[$i]["icon_path"] = $config["server"].$config["site_root"].$settings["photos_folder"]."/".$settings["photos_default"];
				}

				$i++;
				$rs_comm->MoveNext();
			}
			$smarty->assign("event_comments_num", sizeof($event_comm));
			$smarty->assign("event_comments", $event_comm);
		}
		if ($event["comments_count"]>sizeof($event_comm)){
			$event["link_more_comments"] = $file_name."?sel=more_comments&id_event=".$event["id"];
		}

		$smarty->assign("base_lang", GetBaseLang($_LANG_NEED_ID));
		$smarty->assign("event", $event);
	} else {
		EventsTable();
		exit;
	}
	if ($err != ''){
		$form["err"] = $lang["err"][$err];
	}
	if ($event["user_join_event"] == 1){
		$form["event_page"] = 1;
	} else {
		$form["event_page"] = 2;
	}

	$form["guest_user"] = $user[ AUTH_GUEST ];
	$form["show_users_connection_str"] = $settings["show_users_connection_str"];
	$form["show_users_comments"] = $settings["show_users_comments"];
	$form["show_users_group_str"] = $settings["show_users_group_str"];
	$form["icon_max_width"] = $settings["thumb_max_width"];

	if (isset($data) && is_array($data)){
		$smarty->assign("data", $data);
	}
	if ($show_upload_form == '1'){
		$smarty->assign("show_upload_form", 1);
	}
	$smarty->assign("calendar_link","./events_calendar.php");
	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["events"]);
	$smarty->assign("smiles", $config["smiles"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/events_view_table.tpl");
	exit;
}

function CreateForm($err='', $data='')
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	#$settings = GetSiteSettings(array('icon_male_default','icon_female_default','icons_folder','show_users_connection_str','show_users_comments','show_users_group_str'));
	
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
	//// month select
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
	//// year select
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
	//// month select
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
	//// year select
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
	//// month select
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
	//// year select
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

	$data["periodicity"] = isset($data["periodicity"]) ? $data["periodicity"] : "none";

	$form["guest_user"] = $user[ AUTH_GUEST ];
	$form["kcaptcha"] = $config["site_root"].$config_index["kcaptcha"];

	if ($err !=''){
		$form["err"] = $lang["err"][$err];
	}
	$smarty->assign("form", $form);
	$smarty->assign("data", $data);

	$smarty->assign("calendar_link","./events_calendar.php");
	$smarty->assign("header", $lang["events"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/events_create_form.tpl");
	exit;
}

function AddEvent()
{
	global $lang, $config, $dbconn, $user, $IMG_TYPE_ARRAY, $IMG_EXT_ARRAY;
	
	$data = $_POST;

	if (strip_tags(trim($data["event_name"])) == '' || intval($data["type"]) == 0 || intval($data["country"]) == 0 || intval($data["region"]) == 0 || intval($data["city"]) == 0 || strip_tags(trim($data["event_place"])) == '') {
		$err = 'empty_fields';
		CreateForm($err, $data);
		return;
	}

	if (check_filter(strip_tags($data["event_name"]))) {
		CreateForm("info_finding_1", $data);
		return;
	}
	if (check_filter(strip_tags($data["event_place"]))) {
		CreateForm("info_finding_1", $data);
		return;
	}
	if (check_filter(strip_tags(htmlspecialchars($data["description"])))) {
		CreateForm("info_finding_1", $data);
		return;
	}

	if (checkdate($data["s_month"], $data["s_day"], $data["s_year"])){
		$start_date = $data["s_year"]."-".$data["s_month"]."-".$data["s_day"]." ".$data["s_hour"].":".$data["s_min"];
	} else {
		$err = $lang["err"]["invalid_date"];
		CreateForm($err, $data);
		return;
	}
	if (checkdate($data["f_month"], $data["f_day"], $data["f_year"])){
		$finish_date = $data["f_year"]."-".$data["f_month"]."-".$data["f_day"]." ".$data["f_hour"].":".$data["f_min"];
	} else {
		$err = $lang["err"]["invalid_date"];
		CreateForm($err, $data);
		return;
	}
	if ($data["d_month"] && $data["d_day"] && $data["d_year"]){
		if (checkdate($data["d_month"], $data["d_day"], $data["d_year"])){
			$die_date = $data["d_year"]."-".$data["d_month"]."-".$data["d_day"];
		} else {
			$err = $lang["err"]["invalid_date"];
			CreateForm($err, $data);
			return;
		}
	}
	if(!(isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] ==  $_POST['keystring'])) {
		$err = 'invalid_security_code';
		CreateForm($err, $data);
		unset($_SESSION['captcha_keystring']);
		return;
	}
	unset($_SESSION['captcha_keystring']);

	$flyer_file = $_FILES["flyer"];
	
	if(is_uploaded_file($flyer_file["tmp_name"])){
		$filename_arr = explode(".", $flyer_file["name"]);
		$nr = count($filename_arr);
		$ext = strtolower($filename_arr[$nr-1]);
		if (!in_array($flyer_file["type"], $IMG_TYPE_ARRAY) || !in_array($ext, $IMG_EXT_ARRAY)) {
			CreateForm('invalid_image_type', $data);
			return;
		}
	} else $flyer_file["name"] = '';

	$strSQL = " INSERT INTO ".EVENT_DESCR_TABLE."
					(type, id_creator, id_country, id_region, id_city, creation_date, start_date, end_date, periodicity, die_date, event_name, event_place, event_contain, can_invite, can_post_images, flyer)
				VALUES
					('".intval($data["type"])."', '".$user[ AUTH_ID_USER ]."', '".intval($data["country"])."', '".intval($data["region"])."', '".intval($data["city"])."',
					now(), '".$start_date."', '".$finish_date."', '".$data["periodicity"]."', '".$die_date."',
					'".addslashes(strip_tags(trim($data["event_name"])))."', '".addslashes(strip_tags(trim($data["event_place"])))."', '".addslashes(htmlspecialchars(strip_tags($data["description"])))."',
					'".intval($data["members_can_invite"])."', '".intval($data["members_can_post_images"])."', '".$flyer_file["name"]."')
				";
	$rs = $dbconn->Execute($strSQL);
	$rs = $dbconn->Execute("SELECT MAX(id) FROM ".EVENT_DESCR_TABLE." WHERE id_creator='".$user[ AUTH_ID_USER ]."' ");
	$id_event = $rs->fields[0];
	$dbconn->Execute("INSERT INTO ".EVENT_USERS_TABLE." (id_user, id_event) VALUES ('".$user[ AUTH_ID_USER ]."','".$id_event."') ");

	if(is_uploaded_file($flyer_file["tmp_name"])){
		$folder = GetSiteSettings("events_uploads_folder");
		move_uploaded_file($flyer_file["tmp_name"], $config["site_path"].$folder."/".$id_event."_".$flyer_file["name"]);
	}

	header("location: ./events_calendar.php");
	exit;
}

function InviteUser()
{
	global $lang, $dbconn, $user;
	
	$id_event = intval($_REQUEST["id_event"]);
	$id_user = intval($_REQUEST["id_user"]);

	if ($id_event<1 || $id_user<1){
		EventsTable();
		exit;
	}
	$strSQL = " SELECT id FROM ".EVENT_USERS_TABLE." WHERE id_user='".$id_user."' AND id_event='".$id_event."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]) {
		EventTable('user_already_event_member', $id_event);
		exit;
	}
	$strSQL = " SELECT id_creator, can_invite FROM ".EVENT_DESCR_TABLE." WHERE id='".$id_event."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] != $user[ AUTH_ID_USER ]) {//not event creator
		if ($rs->fields[1] != '1') {//only creator can invite other users
			EventTable('you_cant_invite', $id_event);
			exit;
		} else {
			$strSQL = " SELECT COUNT(*) FROM ".EVENT_USERS_TABLE." WHERE id_user='".$user[ AUTH_ID_USER ]."' ";
			$rs = $dbconn->Execute($strSQL);
			if (!$rs->fields[0]) {//current user isn't join event
				EventTable('you_cant_invite', $id_event);
				exit;
			}
		}
	}

	$rs = $dbconn->Execute(" INSERT INTO ".EVENT_INVITES_TABLE." (id_inviter, id_user, id_event) VALUES ('".$user[ AUTH_ID_USER ]."', '".$id_user."', '".$id_event."') ");

	$body = $lang["events"]["invite_mail_body"];

	$strSQL = " SELECT login FROM ".USERS_TABLE." WHERE id='".$id_user."' ";
	$rs = $dbconn->Execute($strSQL);
	$body = str_replace("[user_login]", stripslashes($rs->fields[0]), $body);
	$sub = str_replace("[user_login]", stripslashes($user[ AUTH_LOGIN ]), $lang["events"]["invite_mail_sub"]);

	$strSQL = " SELECT event_name, event_contain FROM ".EVENT_DESCR_TABLE." WHERE id='".$id_event."' ";
	$rs = $dbconn->Execute($strSQL);
	$body = str_replace("[event_name]", stripslashes($rs->fields[0]), $body);
	$body = str_replace("[event_description]", stripslashes($rs->fields[1]), $body);
	$body .= "<br><a href='events.php?sel=join&id_event=".$id_event."'>".$lang["events"]["invite_mail_join_link"]."</a><br>".$sub;

	$strSQL = " INSERT INTO ".MAILBOX_TABLE." (id_from, id_to, subject, body, date_creation, was_read, deleted_from, deleted_to)
				VALUES ('".$user[ AUTH_ID_USER ]."', '".$id_user."', '".$lang["events"]["invite_mail_subject"]."', '".addslashes($body)."', now(),'0','0','0') ";
	$rs = $dbconn->Execute($strSQL);
	EventTable('user_was_invited', $id_event);
	exit;
}

function UploadImage()
{
	global $dbconn, $user;
	
	$id_event = intval($_REQUEST["id_event"]);
	if ($id_event<1){
		EventsTable();
		exit;
	}
	if (check_filter($_REQUEST["comment_to_upload"])) {
		EventTable("info_finding_1", $id_event, '1','', $_REQUEST);
		return;
	}
	$strSQL = " SELECT id_creator, can_post_images FROM ".EVENT_DESCR_TABLE." WHERE id='".$id_event."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] != $user[ AUTH_ID_USER ]) {//not event creator
		if ($rs->fields[1] != '1') {//only creator can post images
			EventTable('you_cant_post_images_to_event', $id_event);
			exit;
		} else {
			$strSQL = " SELECT COUNT(*) FROM ".EVENT_USERS_TABLE." WHERE id_user='".$user[ AUTH_ID_USER ]."' ";
			$rs = $dbconn->Execute($strSQL);
			if (!$rs->fields[0]) {//user isn't join event
				EventTable('you_cant_post_images_to_event', $id_event);
				exit;
			}
		}
	}
	$upload = $_FILES["upload"];

	$err_upload = UploadEventImages($upload, $user[ AUTH_ID_USER ], $id_event, strip_tags(htmlspecialchars($_REQUEST["comment_to_upload"])));
	if ($err_upload){
		$err = 'upload_err';
	} else {
		$err = 'event_photo_uploaded';
	}
	EventTable($err, $id_event);
	exit;
}

function UploadEventImages($upload, $id_user, $id_event, $comment)
{
	global $dbconn;
	
	$images_obj = new Images($dbconn);
	
	$folder		= $images_obj->settings['events_uploads_folder'];
	$max_width	= $images_obj->settings['photo_max_width'];
	$max_height	= $images_obj->settings['photo_max_height'];
	$max_size	= getFileSizeFromString($images_obj->settings['photo_max_size']);
	$err_type	= $images_obj->lang['err']['invalid_photo_type'] . implode(', ', $images_obj->IMG_TYPE_ARRAY);
	$err_size	= str_replace('#SIZE#', $images_obj->settings['photo_max_size'], $images_obj->lang['err']['invalid_photo_size']);
	$err_width	= str_replace('#WIDTH#', $max_width, $images_obj->lang["err"]["invalid_photo_width"]);
	$err_height	= str_replace('#HEIGHT#', $max_height, $images_obj->lang["err"]["invalid_photo_height"]);
	
	if (!is_uploaded_file($upload['tmp_name'])){
		return $images_obj->lang['err']['upload_err'];
	}
	
	if ($images_obj->safe_mode_used) {
		$new_temp_path = $images_obj->GetTempUploadFile($upload['name']);
		if (move_uploaded_file($upload['tmp_name'], $new_temp_path)) {
			$upload['tmp_name'] = $new_temp_path;
		} else {
			return $images_obj->lang['err']['upload_err'];
		}
	}
	
	// if we using picture resize: traing to resize picture
	if ($images_obj->settings['use_image_resize']) {
		$images_obj->ReSizeImage($upload['tmp_name'], $max_width, $max_height);
		$upload['size'] = filesize($upload['tmp_name']);
	}
	
	// get width/height and size info and check on errors
	$err = '';
	$upload_info = GetImageSize($upload["tmp_name"]);
	if ($upload_info[0] > $max_width) {
		if (!$err) {
			$err = $images_obj->lang["err"]["upload_err"].": <br>";
		} else {
			$err .= "<br>";
		}
		$err .= $err_width;
	}
	if ($upload_info[1] > $max_height) {
		if (!$err) {
			$err = $images_obj->lang["err"]["upload_err"].": <br>";
		} else {
			$err .= "<br>";
		}
		$err .= $err_height;
	}
	
	$filename_arr = explode(".", $upload["name"]);
	$nr = count($filename_arr);
	$ext = strtolower($filename_arr[$nr-1]);
	
	if (!in_array($upload['type'], $images_obj->IMG_TYPE_ARRAY) || !in_array($ext, $images_obj->IMG_EXT_ARRAY)) {
		if (!$err) {
			$err = $images_obj->lang["err"]["upload_err"].": <br>";
		} else {
			$err .= "<br>";
		}
		$err .= $err_type;
	}
	if ($upload["size"] > $max_size) {
		if (!$err) {
			$err = $images_obj->lang["err"]["upload_err"].": <br>";
		} else {
			$err .= "<br>";
		}
		$err .= $err_size;
	}
	
	// return errrors if it was found
	if (isset($err) && $err) {
		return $err;
	}

	// rename file
	$new_file_name = $images_obj->GetNewFileName($upload["name"], $id_user);
	$upload_path = $images_obj->site_path.$folder."/".$new_file_name;
	
	if (copy($upload['tmp_name'], $upload_path)) {
		///create thumb if gd used
		if ($images_obj->gd_used) {
			$thumb_upload_path = $images_obj->site_path.$folder."/thumb_".$new_file_name;
			copy($upload["tmp_name"], $thumb_upload_path);
			if (file_exists($thumb_upload_path)) {
				if ($upload_info[0] <= $images_obj->settings["thumb_max_width"] && $upload_info[1] <= $images_obj->settings["thumb_max_height"]) {
					$images_obj->ReSizeWithoutCropImage($thumb_upload_path, $images_obj->settings["thumb_max_width"], $images_obj->settings["thumb_max_height"], 1);
				} else {
					$images_obj->ReSizeImage($thumb_upload_path, $images_obj->settings["thumb_max_width"], $images_obj->settings["thumb_max_height"], 1);
				}
			}
		}
		unlink($upload['tmp_name']);
		$strSQL =
			'INSERT INTO '.EVENT_UPLOADS_TABLE.' (id_event, id_user, upload_path, upload_type, file_type, status, comment)
				  VALUES ("'.$id_event.'", "'.$id_user.'", "'.$new_file_name.'", "f", "'.$upload['type'].'", "1", "'.addslashes($comment).'")';
		$images_obj->dbconn->Execute($strSQL);
		return '';
	}
	
	return $images_obj->lang['err']['upload_err'];
}

function UploadView()
{
	global $smarty, $dbconn, $config, $lang;
	
	IndexHomePage();
	$id_file = intval($_REQUEST["id_file"]);
	$rs = $dbconn->Execute("	SELECT a.upload_path, b.login, a.comment
								FROM ".EVENT_UPLOADS_TABLE." a
								LEFT JOIN ".USERS_TABLE." b ON a.id_user = b.id
								WHERE a.id='".$id_file."'");
	$data["comment"] = stripslashes($rs->fields[2]);
	$data["login"] = stripslashes($rs->fields[1]);
	$data["file_name"] = $rs->fields[0];
	$folder = GetSiteSettings("events_uploads_folder");
	$data["file_path"] = $config["server"].$config["site_root"]."/".$folder."/".$data["file_name"];
	$smarty->assign("data", $data);
	$smarty->assign("button", $lang["button"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/events_photos_view.tpl");
	exit;
}

function UploadDelete()
{
	global $config, $dbconn, $user;
	
	$id_file = intval($_REQUEST["id_file"]);
	if ($id_file<1){
		EventsTable();
		exit;
	}
	$strSQL = " SELECT id_event, upload_path, id_user FROM ".EVENT_UPLOADS_TABLE." WHERE id='".$id_file."' ";
	$rs = $dbconn->Execute($strSQL);
	$id_event = $rs->fields[0];
	if ($id_event<1){
		EventsTable();
		exit;
	}
	$upload_path = $rs->fields[1];
	$upload_id_user = $rs->fields[2];

	$strSQL = " SELECT id_creator FROM ".EVENT_DESCR_TABLE." WHERE id='".$id_event."' ";
	$rs = $dbconn->Execute($strSQL);
	if ( ($rs->fields[0] != $user[ AUTH_ID_USER ]) && ($upload_id_user != $user[ AUTH_ID_USER ]) ) {
		EventTable('you_cant_del_uploads', $id_event);
		return;
	}
	$folder = GetSiteSettings("events_uploads_folder");
	unlink($config["site_path"].$folder."/".$upload_path);
	unlink($config["site_path"].$folder."/thumb_".$upload_path);
	$dbconn->Execute(" DELETE FROM ".EVENT_UPLOADS_TABLE." WHERE id='".$id_file."' ");
	EventTable('event_upload_deleted', $id_event);
	exit;
}

function AddEventComment()
{
	global $dbconn, $user;
	
	$id_event = intval($_REQUEST["id_event"]);
	if ($id_event<1){
		EventsTable();
		exit;
	}
	if (check_filter($_REQUEST["comment_to_event"])) {
		EventTable("info_finding_1", $id_event, '1','', $_REQUEST);
		return;
	}
	$strSQL = " SELECT COUNT(*) FROM ".EVENT_USERS_TABLE." WHERE id_user='".$user[ AUTH_ID_USER ]."' ";
	$rs = $dbconn->Execute($strSQL);
	if (!$rs->fields[0]) {//user can't post comment
		EventTable('you_cant_post_comment_to_event', $id_event);
		exit;
	}

	$rs = $dbconn->Execute("insert into ".EVENT_COMMENTS_TABLE." (id_event, id_user, comment, type, creation_date)
		values ('".$id_event."','".$user[ AUTH_ID_USER ]."','".addslashes(AddSmiles(strip_tags(htmlspecialchars($_REQUEST["comment_to_event"]))))."', '".$_REQUEST["comment_type"]."', '".date("Y-m-d H:i:s")."')");

	EventTable('event_comment_added', $id_event);
	exit;
}

function DeleteEventComment()
{
	global $dbconn, $user;
	
	$id_comment = intval($_REQUEST["id"]);
	if ($id_comment<1){
		EventsTable();
		exit;
	}
	$strSQL = " SELECT id_event, id_user FROM ".EVENT_COMMENTS_TABLE." WHERE id='".$id_comment."' ";
	$rs = $dbconn->Execute($strSQL);
	$id_event = $rs->fields[0];
	if ($id_event<1){
		EventsTable();
		exit;
	}
	$event_id_user = $rs->fields[1];

	if ( $event_id_user != $user[ AUTH_ID_USER ] ) {
		EventTable('you_cant_del_comment', $id_event);
		return;
	}

	$dbconn->Execute(" DELETE FROM ".EVENT_COMMENTS_TABLE." WHERE id='".$id_comment."' ");
	EventTable('event_comment_deleted', $id_event);
	exit;
}

function ListMore($par, $err = '')
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;

	$id_event = intval($_REQUEST["id_event"]);
	if ($id_event<1){
		EventsTable();
		exit;
	}
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$file_name = "events.php";

	$settings = GetSiteSettings(array('icon_male_default','icon_female_default','icons_folder','show_users_connection_str','show_users_comments','show_users_group_str','events_uploads_folder','thumb_max_width'));
	
	$percent = new Percent($user[ AUTH_ID_USER ]);

	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];

	$form["guest_user"] = $user[ AUTH_GUEST ];
	$form["show_users_connection_str"] = $settings["show_users_connection_str"];
	$form["show_users_comments"] = $settings["show_users_comments"];
	$form["show_users_group_str"] = $settings["show_users_group_str"];

	$page = $_GET["page"]?$_GET["page"]:$_POST["page"];
	if( (strval($page) == "") || (strval($page) == "0")){ $page = 1;}else{	$page=intval($page);}

	$strSQL = " SELECT event_name, id_creator FROM ".EVENT_DESCR_TABLE." WHERE id='".$id_event."' ";
	$rs = $dbconn->Execute($strSQL);
	$smarty->assign('event_name', stripslashes($rs->fields[0]));
	$smarty->assign('id_event', $id_event);
	$smarty->assign('back_link', $file_name."?sel=event&id_event=".$id_event);
	$id_creator = $rs->fields[1];
	$smarty->assign('par', $par);
	switch ($par) {
		case "users":
				$strSQL = "SELECT COUNT(*) FROM ".EVENT_USERS_TABLE." WHERE id_event='".$id_event."' AND id_user!='".$user[ AUTH_ID_USER ]."' " ;
				$rs = $dbconn->Execute($strSQL);
				$num_records = $rs->fields[0];
				// page
				$lim_min = ($page-1)*$config_index["search_numpage"];
				$lim_max = $config_index["search_numpage"];
				$limit_str = " LIMIT ".$lim_min.", ".$lim_max;
				if($num_records>0){
					$strSQL = "	SELECT DISTINCT a.id_user, b.login, b.icon_path, b.gender,
									b.date_birthday, b.id_country, b.id_city, b.id_region,
									SUBSTRING(b.comment,1,165) as comment, DATE_FORMAT(b.date_last_seen,'".$config["date_format"]."') as date_last_login
								FROM ".EVENT_USERS_TABLE." a
								LEFT JOIN ".USERS_TABLE." b ON b.id=a.id_user
								WHERE a.id_event='".$id_event."' AND a.id_user!='".$user[ AUTH_ID_USER ]."'
								GROUP BY a.id_user ORDER BY a.id
								".$limit_str;
					$rs_users = $dbconn->Execute($strSQL);
					$i = 0;
					$search = array();
					$_LANG_NEED_ID = array();
					while(!$rs_users->EOF){
						$row = $rs_users->GetRowAssoc(false);
						$search[$i]["id"] = $row["id_user"];
						if ($id_creator == $row["id_user"]){
							$search[$i]["event_creator"] = 1;
						}
						$search[$i]["name"] = $row["login"];
						$search[$i]["number"] = ($page-1)*$config_index["search_numpage"]+($i+1);
						$search[$i]["age"] = AgeFromBDate($row["date_birthday"]);
						$search[$i]["id_country"] = intval($row["id_country"]);
						$search[$i]["id_region"] = intval($row["id_region"]);
						$search[$i]["id_city"] = intval($row["id_city"]);

						$_LANG_NEED_ID["country"][] = intval($row["id_country"]);
						$_LANG_NEED_ID["region"][] = intval($row["id_region"]);
						$_LANG_NEED_ID["city"][] = intval($row["id_city"]);

						$icon_path = $row["icon_path"]?$row["icon_path"]:$default_photos[$row["gender"]];
						$icon_image= (strlen($row["icon_path"]))?1:0;
						if($icon_path && file_exists($config["site_path"].$settings["icons_folder"]."/".$icon_path))
							$search[$i]["icon_path"] = $config["site_root"].$settings["icons_folder"]."/".$icon_path;

						$strSQL = "SELECT COUNT(*) FROM ".USER_UPLOAD_TABLE." WHERE id_user='".$row["id_user"]."' AND upload_type='f' AND status='1' AND allow in ('1', '2')";
						$rs_sub = $dbconn->Execute($strSQL);
						$search[$i]["photo_count"] = intval($rs_sub->fields[0])+$icon_image;

						$search[$i]["profile_link"] = "./viewprofile.php?id=".$row["id_user"];

						$search[$i]["completion"] = $percent->GetAllPercentForUser($row["id_user"]);
						$search[$i]["annonce"] = stripslashes($row["comment"]);
						$search[$i]["last_login"] = $row["date_last_login"];

						/// get groups
						$sub_strSQL = "Select a.name from ".USER_GROUP_TABLE." b left join ".GROUPS_TABLE." a on a.id=b.id_group where b.id_user='".$row["id_user"]."'";
						$sub_rs = $dbconn->Execute($sub_strSQL);
						$groups_arr = array();
						while(!$sub_rs->EOF){
							array_push($groups_arr, $sub_rs->fields[0]);
							$sub_rs->MoveNext();
						}
						if(is_array($groups_arr) && count($groups_arr)>0)
							$search[$i]["group"] = implode(",", $groups_arr);

						/// get status
						$sub_rs = $dbconn->Execute('SELECT COUNT(*) FROM '.ACTIVE_SESSIONS_TABLE.' WHERE id_user = ?', array($row['id_user']));
						$search[$i]['status'] = intval($sub_rs->fields[0]) ? $lang['status']['on'] : $lang['status']['off'];

						/// get user search params
						$sub_strSQL = "SELECT gender as gender_search, age_max, age_min FROM ".USER_MATCH_TABLE." WHERE id_user='".$row["id_user"]."' ";
						$sub_rs = $dbconn->Execute($sub_strSQL);
						$sub_row = $sub_rs->GetRowAssoc(false);
						$search[$i]["age_max"] = $sub_row["age_max"];
						$search[$i]["age_min"] = $sub_row["age_min"];
						$search[$i]["id_gender_search"] = $lang["gender_search"][$sub_row["gender_search"]];

						//links
						$search[$i]["email_link"] = "./mailbox.php?sel=fs&id=".$row["id_user"];
						$search[$i]["im_link"] = $config["use_pilot_module_webmessenger"] ? "./webmessenger/wm.php?strDestinationUserID=".$row["id_user"] : "./w_communicator/flash_im.php?send_user=".$row["id_user"];
						$rs_users->MoveNext();
						$i++;
					}
					$param = $file_name."?sel=more_users&id_event=".$id_event."&";
					$smarty->assign("links", GetLinkArray($num_records,$page,$param,$config_index["search_numpage"]));
					$smarty->assign("search_res", $search);
					if ($err !=''){
						$form["err"] = $lang["err"][$err];
					}
					$smarty->assign("base_lang", GetBaseLang($_LANG_NEED_ID));
					$smarty->assign("form", $form);
					$smarty->assign("header", $lang["events"]);
					$smarty->display(TrimSlash($config["index_theme_path"])."/events_users_table.tpl");
				} else {
					EventTable('',$id_event);
				}
			break;
		case "photos":
				if ($id_creator == $user[ AUTH_ID_USER ]){
					$smarty->assign("user_is_creator", 1);
				}
				$strSQL = "SELECT COUNT(*) FROM ".EVENT_UPLOADS_TABLE." WHERE id_event='".$id_event."' AND status='1'" ;
				$rs = $dbconn->Execute($strSQL);
				$num_records = $rs->fields[0];
				// page
				$lim_min = ($page-1)*$config_index["search_gallery_numpage"];
				$lim_max = $config_index["search_gallery_numpage"];
				$limit_str = " LIMIT ".$lim_min.", ".$lim_max;
				if($num_records>0){
					$strSQL = "	SELECT DISTINCT a.id, b.login, a.upload_path, a.comment, a.id_user
								FROM ".EVENT_UPLOADS_TABLE." a
								LEFT JOIN ".USERS_TABLE." b ON b.id=a.id_user
								WHERE a.status='1' AND a.id_event='".$id_event."'
								GROUP BY a.id ORDER BY a.id DESC
								".$limit_str;
					$rs_photos = $dbconn->Execute($strSQL);
					$i = 0;
					$event_photos = array();
					while(!$rs_photos->EOF){
						$row_photos = $rs_photos->GetRowAssoc(false);
						$event_photos[$i]["id"] = $row_photos["id"];
						$event_photos[$i]["id_user"] = $row_photos["id_user"];
						$event_photos[$i]["login"] = stripslashes($row_photos["login"]);
						$event_photos[$i]["comment"] = stripslashes($row_photos["comment"]);
						$event_photos[$i]["user_upload"] = ($event_photos[$i]["id_user"] == $user[ AUTH_ID_USER ]) ? 1 : 0;
						$file_path = $row_photos["upload_path"];
						if($file_path && file_exists($config["site_path"].$settings["events_uploads_folder"]."/".$file_path)){
							$event_photos[$i]["upload_path"] = $config["server"].$config["site_root"].$settings["events_uploads_folder"]."/".$file_path;
							$event_photos[$i]["upload_thumb_path"] = $config["server"].$config["site_root"].$settings["events_uploads_folder"]."/thumb_".$file_path;
							$event_photos[$i]["view_link"] = $file_name."?sel=upload_view&id_file=".$event_photos[$i]["id"];
							$event_photos[$i]["del_link"] = $file_name."?sel=upload_del&id_file=".$event_photos[$i]["id"];
						} else {
							$event_photos[$i]["upload_path"] = $config["server"].$config["site_root"].$settings["events_uploads_folder"]."/".$settings["photos_default"];
							$event_photos[$i]["upload_thumb_path"] = $config["server"].$config["site_root"].$settings["events_uploads_folder"]."/".$settings["photos_default"];
						}
						$rs_photos->MoveNext();
						$i++;
					}
					$param = $file_name."?sel=more_photos&id_event=".$id_event."&";
					$smarty->assign("links", GetLinkArray($num_records,$page,$param,$config_index["search_gallery_numpage"]));
					$smarty->assign("event_photos", $event_photos);
					if ($err !=''){
						$form["err"] = $lang["err"][$err];
					}
					$smarty->assign("form", $form);
					$smarty->assign("header", $lang["events"]);
					$smarty->display(TrimSlash($config["index_theme_path"])."/events_photos_table.tpl");
				} else {
					EventTable('',$id_event);
				}
			break;
		case "comments":
				$strSQL = "SELECT COUNT(*) FROM ".EVENT_COMMENTS_TABLE." WHERE id_event='".$id_event."'";
				$rs = $dbconn->Execute($strSQL);
				$num_records = $rs->fields[0];
				// page
				$lim_min = ($page-1)*$config_index["search_numpage"];
				$lim_max = $config_index["search_numpage"];
				$limit_str = " LIMIT ".$lim_min.", ".$lim_max;
				if($num_records>0){
					$strSQL = "	select distinct a.id, a.comment, a.type, DATE_FORMAT(a.creation_date,'".$config["date_format"]."') as date, a.id_user, b.login, b.gender, b.date_birthday, b.icon_path, c.name as country, d.name as city, r.name as region, e.id_user as session
								from ".EVENT_COMMENTS_TABLE." a, ".USERS_TABLE." b
								left join ".COUNTRY_SPR_TABLE." c on c.id=b.id_country
								left join ".CITY_SPR_TABLE." d on d.id=b.id_city
								left join ".ACTIVE_SESSIONS_TABLE." e on b.id=e.id_user
								left join ".REGION_SPR_TABLE." r on r.id=b.id_region
								where a.id_event='".$id_event."' and a.id_user=b.id
								group by a.id order by a.creation_date desc
								".$limit_str;
					$rs_comm = $dbconn->Execute($strSQL);
					$i = 0;
					$event_comm = array();
					while(!$rs_comm->EOF){
						$row_comm = $rs_comm->GetRowAssoc(false);
						$event_comm[$i]["comment"] = stripslashes(nl2br($row_comm["comment"]));
						$event_comm[$i]["type"] = $lang["events"][$row_comm["type"]];
						$event_comm[$i]["delete_link"] = ($row_comm["id_user"] == $user[ AUTH_ID_USER ]) ? "./events.php?sel=delete_comment&id=".$row_comm["id"] : "";
						$event_comm[$i]["date"] = $row_comm["date"];
						$event_comm[$i]["name"] = $row_comm["login"];
						$event_comm[$i]["profile_link"] = ($row_comm["id_user"] != $user[ AUTH_ID_USER ]) ? "./viewprofile.php?id=".$row_comm["id_user"] : "";
						$event_comm[$i]["status"] = $row_comm["session"]?$lang["status"]["on"]:$lang["status"]["off"];
						$event_comm[$i]["age"] = AgeFromBDate($row_comm["date_birthday"]);
						$event_comm[$i]["country"] = stripslashes($row_comm["country"]);
						$event_comm[$i]["region"] = stripslashes($row_comm["region"]);
						$event_comm[$i]["city"] = stripslashes($row_comm["city"]);
						$icon_path = $row_comm["icon_path"]?$row_comm["icon_path"]:$default_photos[$row["gender"]];
						if($icon_path && file_exists($config["site_path"].$settings["icons_folder"]."/".$icon_path))
							$event_comm[$i]["icon_path"] = $config["site_root"].$settings["icons_folder"]."/".$icon_path;
						$i++;
						$rs_comm->MoveNext();
					}
					$param = $file_name."?sel=more_comments&id_event=".$id_event."&";
					$smarty->assign("links", GetLinkArray($num_records,$page,$param,$config_index["search_numpage"]));
					$smarty->assign("event_comments", $event_comm);
					if ($err !=''){
						$form["err"] = $lang["err"][$err];
					}
					$smarty->assign("form", $form);
					$smarty->assign("header", $lang["events"]);
					$smarty->display(TrimSlash($config["index_theme_path"])."/events_comments_table.tpl");
				} else {
					EventTable('',$id_event);
				}
			break;
		default: EventsTable(); break;
	}
	exit;
}

function DeleteEvent()
{
	global $config, $dbconn, $user;
	
	$id_event = intval($_GET['id_event']);
	$strSQL = " SELECT id, flyer FROM ".EVENT_DESCR_TABLE." WHERE id='".$id_event."' AND id_creator='".$user[ AUTH_ID_USER ]."' ";
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
	header("location: ./events_calendar.php");
	exit;
}
?>