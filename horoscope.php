<?php
/**
* Horoscope page
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
include './include/class.percent.php';
include './include/class.news.php';

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
	case 'view':	HoroscopeSign(); break;
	case 'match':	HoroscopeMatch(); break;
	default: 	HoroscopePage();
}

exit;


function HoroscopePage()
{
	global $lang, $config, $smarty, $dbconn, $user;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	if ($user[ AUTH_ID_USER ]) {
		$rs = $dbconn->Execute("select DATE_FORMAT(date_birthday,'%m'), DATE_FORMAT(date_birthday,'%d') from ".USERS_TABLE." where id='".$user[ AUTH_ID_USER ]."'");
		$birth_month = $rs->fields[0];
		$birth_day = $rs->fields[1];

		$rs = $dbconn->Execute("select id from ".HOROSCOPE_SIGNS_TABLE." where DATE_FORMAT(date_start,'%m')=".$birth_month." and DATE_FORMAT(date_start,'%d')<=".$birth_day);
		if ($rs->fields[0]) {
			$sign = $rs->fields[0];
		} else {
			$rs = $dbconn->Execute("select id from ".HOROSCOPE_SIGNS_TABLE." where DATE_FORMAT(date_end,'%m')=".$birth_month." and DATE_FORMAT(date_end,'%d')>=".$birth_day);
			$sign = $rs->fields[0];
		}
	}

	$rs = $dbconn->Execute("select id, name from ".HOROSCOPE_SIGNS_TABLE);
	$i = 0;
	$signs = array();
	while(!$rs->EOF){
		$row = $rs->GetRowAssoc(false);
		$signs[$i]["sign"] = $row["name"];
		$signs[$i]["sign_name"] = $lang["horoscope"][$row["name"]]["name"];
		$signs[$i]["sign_link"] = "./horoscope.php?sel=view&sign=".$row["name"];
		if ((isset($sign))&&($sign == $row["id"])) $signs[$i]["my_sign"] = 1;
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign("signs", $signs);

	$form["page"] = "main";

	$smarty->assign("form", $form);
	$smarty->assign("section", $lang["subsection"]);
	$smarty->assign("header", $lang["horoscope"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/horoscope_table.tpl");
	exit;
}

function HoroscopeSign()
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$file_name = "horoscope.php";

	$sign_arr = array("aries","taurus","gemini","cancer","leo","virgo","libra","scorpio","sagittarius","capricorn","aquarius","pisces");

	$sign_info = array();
	if (!isset($_REQUEST["sign"])||(!$_REQUEST["sign"])) {
		HoroscopePage();
		return;
	} else {
		$sign = $_REQUEST["sign"];
		if (!in_array($sign,$sign_arr)) {
			HoroscopePage();
			return;
		}
		$strSQL = "select id, name, DATE_FORMAT(date_start,'%d/%m') as start_date, DATE_FORMAT(date_end,'%d/%m') as end_date from ".HOROSCOPE_SIGNS_TABLE." where name='".$sign."'";
		$rs = $dbconn->Execute($strSQL);
		if (($rs->EOF)||($rs===false)) {
			HoroscopePage();
			return;
		} else {
			$row = $rs->GetRowAssoc(false);
			$sign_info = $row;
		}
	}

	$sign_info["sign"] = $sign_info["name"];
	$sign_info["sign_dates"] = $sign_info["start_date"]." - ".$sign_info["end_date"];
	$sign_info["sign_name"] = $lang["horoscope"][$sign_info["name"]]["name"];
	$sign_info["love_text"] = $lang["horoscope"][$sign_info["name"]]["love_text"];
	load_weekly_horoscope($config_index["horoscope_feed"], $sign_info);

	$smarty->assign("sign_info", $sign_info);

	$form["page"] = "view";
	$form["horoscope_link"] = "./".$file_name;

	if (isset($_REQUEST["search_type"]) && $_REQUEST["id"]) {
		$form["back_link"] = "viewprofile.php?id=".intval($_REQUEST["id"])."&search_type=".$_REQUEST["search_type"];
	}
	$smarty->assign("form", $form);
	$smarty->assign("section", $lang["subsection"]);
	$smarty->assign("header", $lang["horoscope"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/horoscope_table.tpl");
	exit;
}

function load_weekly_horoscope($url,&$sign_info)
{
	$rss_array = array();
	$rss_array = rss2array($url);

	if ($url == 'http://feeds.astrology.com/weeklyromantic') {
		$arr_1 = array();
		$arr_2 = array();
		$xml_parser = xml_parser_create();
		xml_parse_into_struct($xml_parser, trim($rss_array["items"][$sign_info["id"]-1]["description"]), $arr_1, $arr_2);

		$sign_info["weekly_text"] = nl2br(trim(strip_tags($arr_1[0]["value"])));
	} else {
		$sign_info["weekly_text"] = nl2br(trim(strip_tags($rss_array["items"][$sign_info["id"]]["description"])));

		$sign_info["weekly_horo_text"] = $rss_array["items"][0]["description"];
		$sign_info["weekly_horo_title"] = $rss_array["items"][0]["title"];
		$sign_info["weekly_horo_link"] = $rss_array["items"][0]["link"];
	}

	return;
}

?>