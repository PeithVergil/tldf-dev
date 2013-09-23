<?php
/**
* Blog Calendar
*
* @package DatingPro
* @subpackage User Mode
**/

include './include/config.php';
include './common.php';
include './include/config_index.php';
include './include/functions_auth.php';
include './include/functions_index.php';
include './include/class.lang.php';
include './include/functions_calendar.php';

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

// active menu item
$smarty->assign('sub_menu_num', '');

global $lang, $config, $config_index, $smarty, $dbconn, $user;

Banners(GetRightModulePath(__FILE__));
IndexHomePage();
GetActiveUserInfo($user);

$file_name = 'blog_calendar.php';

$actions = array(
	'show_period'	=> isset($_REQUEST['show_period']) ? $_REQUEST['show_period'] : 'month',
	'move'			=> isset($_REQUEST['move']) ? $_REQUEST['move'] : 'this',
	'year'			=> isset($_REQUEST['year']) ? $_REQUEST['year'] : date('Y'),
	'month'			=> isset($_REQUEST['month']) ? $_REQUEST['month'] : date('n'),
	'week'			=> isset($_REQUEST['week']) ? $_REQUEST['week'] : date('W'),
	'day'			=> isset($_REQUEST['day']) ? $_REQUEST['day'] : date('j')
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


		$week[$first_day["wday"]] = array(	"mday" => $first_day["mday"],
											"wday" => $first_day["wday"],
											"mon" => $first_day["mon"],
											"year" => $first_day["year"],
											"blog" => GetBlogPost($first_day["year"]."-".$first_day["mon"]."-".$first_day["mday"]) );
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
			$week[$this_day["wday"]] = array(	"mday" => $this_day["mday"],
												"wday" => $this_day["wday"],
												"mon" => $this_day["mon"],
												"year" => $this_day["year"],
												"blog" => GetBlogPost($this_day["year"]."-".$this_day["mon"]."-".$this_day["mday"]) );
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
		$smarty->assign("blog_path", "./blog.php");
		$smarty->assign("section",$lang["section"]);

		$form["blog_page"] = 2;
		$form["guest_user"] = $user[ AUTH_GUEST ];

		$smarty->assign("header_s", $lang["blog"]);
		$smarty->assign("form", $form);
		$smarty->display(TrimSlash($config["index_theme_path"])."/blog_calendar.tpl");
	}
	break;
	default: {
	}
	break;
}
?>