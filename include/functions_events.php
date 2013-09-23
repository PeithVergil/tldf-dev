<?php
/**
*
*	events functions
*
**/

function EventsMain(){
	global $lang, $config, $config_index, $smarty, $dbconn, $user, $page;

	if ($config["use_pilot_module_events"]=='1'){
		$smarty->assign("calendar_link","./events_calendar.php");
		$today = date("Y-m-d");
		$events = array();
		$rs = $dbconn->Execute("select id, event_name, event_place,
			if(periodicity = 'none', DATE_FORMAT(start_date,'".$config["date_format"]." %H:%i'), DATE_FORMAT(ADDDATE(start_date,INTERVAL (TO_DAYS('".$today."') - TO_DAYS(start_date)) DAY),'".$config["date_format"]." %H:%i')) as date_start,
			if(periodicity = 'none', DATE_FORMAT(end_date,'".$config["date_format"]." %H:%i'), DATE_FORMAT(ADDDATE(end_date,INTERVAL (TO_DAYS('".$today."') - TO_DAYS(start_date)) DAY),'".$config["date_format"]." %H:%i')) as date_end,
			event_contain FROM ".EVENT_DESCR_TABLE."
			WHERE (periodicity = 'none' AND DATE_FORMAT(start_date,'%Y-%m-%d') = '".$today."')
			OR (periodicity = 'daily' AND ('".$today."' <= die_date OR die_date = '0000-00-00') AND DATE_FORMAT(start_date,'%Y-%m-%d') <= '".$today."')
			OR (periodicity = 'weekly' AND DAYOFWEEK(start_date) = DAYOFWEEK('".$today."') AND ('".$today."' <= die_date OR die_date = '0000-00-00') AND DATE_FORMAT(start_date,'%Y-%m-%d') <= '".$today."')
			OR (periodicity = 'monthly' AND DAYOFMONTH(start_date) = DAYOFMONTH('".$today."') AND ('".$today."' <= die_date OR die_date = '0000-00-00') AND DATE_FORMAT(start_date,'%Y-%m-%d') <= '".$today."')
			OR (periodicity = 'yearly' AND MONTH(start_date) = MONTH('".$today."') AND DAYOFMONTH(start_date) = DAYOFMONTH('".$today."') AND ('".$today."' <= die_date OR die_date = '0000-00-00') AND DATE_FORMAT(start_date,'%Y-%m-%d') <= '".$today."')
			limit 0,5");

		if($rs->RowCount()>0){
			$i = 0;
			while(!$rs->EOF){
				$row = $rs->GetRowAssoc(false);
				if (strlen(utf8_decode($row["event_name"]))<75) {
					$events[$i]["today_name"] = stripslashes($row["event_name"]);
				} else {
					$events[$i]["today_name"] = stripslashes(utf8_substr($row["event_name"],0,75)."...");
				}

				if (strlen(utf8_decode($row["event_place"]))<75) {
					$events[$i]["event_place"] = stripslashes($row["event_place"]);
				} else {
					$events[$i]["event_place"] = stripslashes(utf8_substr($row["event_place"],0,75)."...");
				}
				$events[$i]["start_date"] = $row["date_start"];
				$events[$i]["finish_date"] = $row["date_end"];

				$events[$i]["id_event"] = $row["id"];
				$events[$i]["today_link"] = "./events.php?sel=today#".$events[$i]["id_event"];
				$rs2 = $dbconn->Execute("select id_user FROM ".EVENT_USERS_TABLE." WHERE id_event='".$events[$i]["id_event"]."' AND id_user='".$user[ AUTH_ID_USER ]."'");
				if (count($rs2->fields[0])>0) {
					$events[$i]["joined"] = true;
					$events[$i]["leave_link"] = "./events.php?sel=today#".$events[$i]["id_event"];
				}else{
					$events[$i]["join_link"] = "./events.php?sel=today#".$events[$i]["id_event"];
				}
				$rs->MoveNext();
				$i++;
			}
			$link_more = "./events.php?sel=today";
			$smarty->assign("link_more", $link_more);
		}
		return $events;
	}else{
		return false;
	}
}

function DeleteUserFromEvents($id_user) {
	global $config, $dbconn;

	$dbconn->Execute("delete from ".EVENT_DESCR_TABLE." where id_creator='".$id_user."'");
	$dbconn->Execute("delete from ".EVENT_USERS_TABLE." where id_user='".$id_user."'");
	$dbconn->Execute("delete from ".EVENT_UPLOADS_TABLE." where id_user='".$id_user."'");
	$dbconn->Execute("delete from ".EVENT_COMMENTS_TABLE." where id_user='".$id_user."'");
	$dbconn->Execute("delete from ".EVENT_INVITES_TABLE." where id_user='".$id_user."' or id_inviter='".$id_user."'");

	return;
}
?>