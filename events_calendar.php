<?php
/**
* Event Calendar
*
* @package DatingPro
* @subpackage User Mode
**/

include './include/config.php';
include './common.php';
include './include/config_index.php';
include './include/functions_auth.php';
include './include/functions_index.php';
include './include/functions_calendar.php';
include './include/class.lang.php';

// active menu item
$smarty->assign('sub_menu_num', '');

if (!isset($_REQUEST['act'])) {
	// authentication
	$user = auth_index_user();
	
	if (empty($user) || $user == 'err' || empty($user[ AUTH_ID_USER ])) {
		header('location: '.$config['site_root'].'/index.php');
		exit;
	}
	
	// check guest
	// (public access)
	
	// check group, period, expiration
	RefreshAccount();
	
	// check status
	// (public access)
	
	// check permissions
	// (public access)
	
	// alerts and statistics
	if (!$user[ AUTH_GUEST ]) {
		GetAlertsMessage();
		SetModuleStatistic(GetRightModulePath(__FILE__));
	}
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$file_name = (isset($_SERVER["PHP_SELF"]))?AfterLastSlash($_SERVER["PHP_SELF"]):"events.php";
	
	$smarty->assign("add_event_link","./events.php?sel=create");
}

$id_country = isset($_REQUEST["id_country"]) ? $_REQUEST["id_country"] : 0;
$id_region = isset($_REQUEST["id_region"]) ? $_REQUEST["id_region"] : 0;
$id_city = isset($_REQUEST["id_city"]) ? $_REQUEST["id_city"] : 0;
$type = isset($_REQUEST["type"]) ? $_REQUEST["type"] : 0;

$my_events = isset($_REQUEST["act"]) ? false : true;

$smarty->assign("id_country", $id_country);
$smarty->assign("id_region", $id_region);
$smarty->assign("id_city", $id_city);
$smarty->assign("type", $type);

$actions = array(
	"show_period"	=> isset($_REQUEST["show_period"]) ? $_REQUEST["show_period"] : "month",
	"move"			=> isset($_REQUEST["move"]) ? $_REQUEST["move"] : "this",
	"year"			=> isset($_REQUEST["year"]) ? $_REQUEST["year"] : date("Y"),
	"month"			=> isset($_REQUEST["month"]) ? $_REQUEST["month"] : date("n"),
	"week"			=> isset($_REQUEST["week"]) ? $_REQUEST["week"] : date("W"),
	"day"			=> isset($_REQUEST["day"]) ? $_REQUEST["day"] : date("j")
);

if ( $actions["move"] != "this" ) {
	$parts = split("_", $actions["move"]);
	if ( $parts[1] == "month" ) {
		switch ( $parts[0] ) {
			case "back": {
				if ( $actions["month"] == 1 ) {
					$actions["year"] = $actions["year"] - 1;
					$actions["month"] = 12;
				} else {
					$actions["month"] = $actions["month"] - 1;
				}
			}
			break;

			case "next": {
				if ( $actions["month"] == 12 ) {
					$actions["year"] = $actions["year"] + 1;
					$actions["month"] = 1;
				} else {
					$actions["month"] = $actions["month"] + 1;
				}
			}
			break;

			default: {
			}
			break;
		}
	} elseif ( $parts[1] == "day" ) {
		switch ( $parts[0] ) {
			case "back": {
				$selected_day = adodb_getdate( adodb_mktime( 0, 0, 1, $actions["month"], $actions["day"], $actions["year"] ) - 86400 );
				$actions["day"] = $selected_day["mday"];
				$actions["month"] = $selected_day["mon"];
				$actions["year"] = $selected_day["year"];
			}
			break;

			case "next": {
				$selected_day = adodb_getdate( adodb_mktime( 0, 0, 1, $actions["month"], $actions["day"], $actions["year"] ) + 86400 );
				$actions["day"] = $selected_day["mday"];
				$actions["month"] = $selected_day["mon"];
				$actions["year"] = $selected_day["year"];
			}
			break;

			default: {
			}
			break;
		}
	}
}

switch ( $actions["show_period"] ) {
	//calendar month
	case "month": {
		$current_day = adodb_getdate();
		$amount_days = adodb_date( "t", adodb_mktime(0, 0, 0, $actions["month"], $actions["day"], $actions["year"]) );
		$selected_month = adodb_getdate( adodb_mktime(0, 0, 0, $actions["month"], 1, $actions["year"]) );
		$first_day = adodb_getdate( adodb_mktime(0, 0, 0, $actions["month"], 1, $actions["year"]) );
		$calendar = array();
		$week = array(	0 => "false",
						1 => "false",
						2 => "false",
						3 => "false",
						4 => "false",
						5 => "false",
						6 => "false");

		$events = GetEvent($first_day["year"]."-".$first_day["mon"]."-".$first_day["mday"], $id_country, $id_region, $id_city, $type, $my_events);
		$week[$first_day["wday"]] = array(	"mday" => $first_day["mday"],
											"wday" => $first_day["wday"],
											"mon" => $first_day["mon"],
											"year" => $first_day["year"],
											"event" => isset($events["event"])?$events["event"]:"",
											"event_count" => isset($events["event_count"])?$events["event_count"]:"" );
		if ( $current_day["mday"] == $first_day["mday"] &&
			 $current_day["mon"] == $first_day["mon"] &&
			 $current_day["year"] == $first_day["year"] )
		{
			$week[$first_day["wday"]]["current_day"] = "true";
		} else {
			$week[$first_day["wday"]]["current_day"] = "false";
		}
		for ( $days_cnt = 2; $days_cnt <= $amount_days; $days_cnt++ ) {
			$this_day = adodb_getdate( adodb_mktime(0, 0, 0, $first_day["mon"], $days_cnt, $first_day["year"]) );

			if ( $this_day["wday"] == 1 ) {
				$calendar[] = $week;
				$week = array(
								0 => "false",
								1 => "false",
								2 => "false",
								3 => "false",
								4 => "false",
								5 => "false",
								6 => "false");
			}
			$events = GetEvent($this_day["year"]."-".$this_day["mon"]."-".$this_day["mday"], $id_country, $id_region, $id_city, $type, $my_events);
			$week[$this_day["wday"]] = array(	"mday" => $this_day["mday"],
												"wday" => $this_day["wday"],
												"mon" => $this_day["mon"],
												"year" => $this_day["year"],
												"event" => isset($events["event"])?$events["event"]:"",
												"event_count" => isset($events["event_count"])?$events["event_count"]:"" );
			if ( $current_day["mday"] == $this_day["mday"] &&
				 $current_day["mon"] == $this_day["mon"] &&
				 $current_day["year"] == $this_day["year"] )
			{
				$week[$this_day["wday"]]["current_day"] = "true";
			} else {
				$week[$this_day["wday"]]["current_day"] = "false";
			}
		}
		$calendar[] = $week;
		$smarty->assign("current_month", $calendar);
		$smarty->assign("current_day", $current_day);
		$smarty->assign("selected_month", $selected_month);
		$smarty->assign("event_path", "./events.php");
		$smarty->assign("st", $lang["calendar"]);
		$smarty->assign("header", $lang["events"]);
		$smarty->assign("section", $lang["section"]);
		//$smarty->assign("show_period", $actions["show_period"] );
	}
	break;
	default: {
	}
	break;
}

if ( !isset($_REQUEST["act"]) )
	$smarty->display(TrimSlash($config["index_theme_path"])."/events_calendar.tpl");
else
	$smarty->display(TrimSlash($config["index_theme_path"])."/events_calendar_small.tpl");
?>