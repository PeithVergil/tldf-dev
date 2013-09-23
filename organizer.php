<?php
/**
* Organizer Module
*
* @package DatingPro
* @subpackage User Mode
**/

include './include/config.php';
include './common.php';
include './include/config_index.php';
include './include/functions_auth.php';
include './include/functions_index.php';
include './include/functions_users.php';
include './include/class.lang.php';
include './include/class.percent.php';
include './include/class.images.php';
include_once './include/functions_calendar.php';

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

// user selection
$sel = $_POST['sel'] ? $_POST['sel'] : $_GET['sel'];

// dispatcher
switch ($sel) {
	case 'save_profile':	SaveUserSiteProfile();		break;
	case 'save_action':		SaveUserAction();			break;
	case 'del_action':		DelUserAction();			break;
	case 'del_profile':		DelUserSiteProfile();		break;
	case 'bookmarks':		BookmarksPage();			break;
	case 'save_bookmark':	SaveUserBookmark();			break;
	case 'del_bookmark':	DelUserBookmark();			break;
	case 'billing':			BillingPage();				break;
	case 'homepage_management': HomePageManagement();	break;
	case 'ajax_req':		AJAXResponce();				break;
	case 'ajax_calendar':	OrganizerCalendar();		break;
	case 'date':			OrganizerMain('', 'calendar_action');	break;
	case 'save_home_colors': SaveHomeColors();		break;
	default:				OrganizerMain();			break;
}

exit;


function OrganizerMain($error=null, $section='', $data=null)
{
	global $lang, $config, $smarty, $user;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$settings = GetSiteSettings(array('show_users_connection_str','show_users_comments','show_users_group_str'));
	
	$form["show_users_connection_str"] = $settings["show_users_connection_str"];
	$form["show_users_comments"] = $settings["show_users_comments"];
	$form["show_users_group_str"] = $settings["show_users_group_str"];
	
	if ($error) {
		$form["err"] = $error;
	}
	if ($data) {
		$smarty->assign("data", $data);
	}
	
	$form['section'] = $section;
	
	$smarty->assign("header", $lang["homepage"]);
	$smarty->assign("site_profiles", GetUserSiteProfiles());
	
	ob_start();
	$_REQUEST["act"] = "ajax";
	OrganizerCalendar();
	$calendar_out_put = ob_get_contents();
	ob_end_clean();
	$smarty->assign("calendar_out_put", $calendar_out_put);
	
	if ($section == 'calendar_action') {
		$form["back_link"] = "organizer.php";
		
		// day select
		$data['day'] = $data['day'] ? $data['day'] : intval($_GET['day']);
		if (intval($data["day"]) == 0) {
			$data["day"] = date("d");
		}
		$day = array();
		for ($i = 0; $i < 31; $i++) {
			$day[$i]["value"] = sprintf("%02d", $i+1);
			if (intval($data["day"]) == $i + 1) {
				$day[$i]["sel"] = 1;
			} else {
				$day[$i]["sel"] = 0;
			}
		}
		$form['day'] = $data['day'];
		$smarty->assign("day", $day);
		
		// month select
		$data['month'] = $data['month'] ? $data['month'] : intval($_GET['month']);
		if (intval($data["month"]) == 0) {
			$data["month"] = date("m");
		}
		$month = array();
		for ($i = 0; $i < 12; $i++) {
			$month[$i]["value"] = $i+1;
			$month[$i]["name"] = $lang["month"][$i+1];
			if (intval($data["month"]) == $i + 1) {
				$month[$i]["sel"] = 1;
			} else {
				$month[$i]["sel"] = 0;
			}
		}
		$form['month'] = $data['month'];
		$smarty->assign("month", $month);
		
		// year select
		$data['year'] = $data['year'] ? $data['year'] : intval($_GET['year']);
		if (intval($data["year"]) == 0) {
			$data["year"] = date("Y");
		}
		$year = array();
		for ($i = 0; $i < 3; $i++) {
			$y = intval(date("Y"))+1-$i;
			$year[$i]["value"] = $y;
			if (intval($data["year"]) == $y) {
				$year[$i]["sel"] = 1;
			} else {
				$year[$i]["sel"] = 0;
			}
		}
		$form['year'] = $data['year'];
		$smarty->assign("year", $year);
		
		// hour
		$hour = array();
		for ($i = 0; $i < 24; $i++) {
			$hour[$i]["value"] = sprintf("%02d",$i);
			if ($data["hour"] == $i) {
				$hour[$i]["sel"] = 1;
			} else {
				$hour[$i]["sel"] = 0;
			}
		}
		$smarty->assign("hour", $hour);
		
		// minute
		$min = array();
		for ($i = 0; $i < 60; $i++) {
			$min[$i]["value"] = sprintf("%02d", $i);
			if ($data["min"] == $i) {
				$min[$i]["sel"] = 1;
			} else {
				$min[$i]["sel"] = 0;
			}
		}
		$smarty->assign("min", $min);
		$smarty->assign("user_actions", OrganizerUserCalendar($form['month'], $form['year'], $form['day']));
	}

	$smarty->assign("user_stat", GetUserStatistic());
	$smarty->assign("form", $form);
	$smarty->display(TrimSlash($config["index_theme_path"])."/organizer_main_table.tpl");
	exit;
}

function BookmarksPage($error=null, $section='', $data=null)
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$file_name = "organizer.php";

	$settings = GetSiteSettings(array('show_users_connection_str','show_users_comments','show_users_group_str'));

	if ($error){
		$form["err"] = $error;
	}
	if ($data){
		$smarty->assign("data", $data);
	}

	$form["show_users_connection_str"] = $settings["show_users_connection_str"];
	$form["show_users_comments"] = $settings["show_users_comments"];
	$form["show_users_group_str"] = $settings["show_users_group_str"];

	$page = $_GET["page"] ? $_GET["page"] : $_POST["page"];
	
	if (strval($page) == "" || strval($page) == "0") {
		$page = 1;
	}

	$strSQL = "SELECT COUNT(id) FROM ".ORG_USER_BOOKMARKS_TABLE." WHERE id_user='".$user[ AUTH_ID_USER ]."'";
	$rs = $dbconn->Execute($strSQL);
	$num_records = $rs->fields[0];
	
	$lim_min = ($page - 1) * $config_index["search_numpage"];
	$lim_max = $config_index["search_numpage"];
	$limit_str = " LIMIT ".$lim_min.", ".$lim_max;
	
	$strSQL =
		"SELECT DISTINCT id, link, descr
		   FROM ".ORG_USER_BOOKMARKS_TABLE."
		  WHERE id_user = '".$user[ AUTH_ID_USER ]."'
	   GROUP BY id
	   ORDER BY id ".$limit_str ;
	$rs = $dbconn->Execute($strSQL);
	
	if ($rs->fields[0] > 0) {
		$i = 0;
		$_arr = array();
		while (!$rs->EOF) {
			$_arr[$i]['id'] = $rs->fields[0];
			$_arr[$i]['link'] = stripslashes($rs->fields[1]);
			$_arr[$i]['descr'] = stripslashes($rs->fields[2]);
			$rs->MoveNext();
			$i++;
		}
		$param = $file_name."?sel=bookmarks&";
		$smarty->assign("links", GetLinkArray($num_records, $page, $param, $config_index["search_numpage"]));
		
		$smarty->assign("user_bookmarks", $_arr);
	}
	
	$form['section'] = $section;
	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["homepage"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/organizer_bookmarks_table.tpl");
	exit;
}

function SaveUserBookmark()
{
	global $lang, $dbconn, $user;
	
	$org_bookmark_limit = GetSiteSettings('org_bookmark_limit');
	$data = $_POST;
	
	if (!isset($data['bookmark_url']) || strip_tags(trim($data['bookmark_url'])) == '') {
		$error = $lang['organizer']['empty_bookmark_url'];
	}
	if (!isset($data['bookmark_descr']) || strip_tags(trim($data['bookmark_descr'])) == '') {
		$error = $lang['organizer']['empty_bookmark_descr'];
	}
	
	if ($error) {
		BookmarksPage($error, 2, $data);
	} else {
		$strSQL = "SELECT COUNT(id) FROM ".ORG_USER_BOOKMARKS_TABLE." WHERE id_user='".$user[ AUTH_ID_USER ]."'";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0] >= $org_bookmark_limit) {
			$error = $lang['organizer']['user_bookmark_limit'];
		} else {
			$data['bookmark_url'] = preg_replace("/http:\/\//is", "", $data['bookmark_url']);
			$rs = $dbconn->Execute(
				"INSERT INTO ".ORG_USER_BOOKMARKS_TABLE."
					(link, descr, id_user)
						VALUES
					('".addslashes(strip_tags(trim($data['bookmark_url'])))."', '".addslashes(strip_tags(trim($data['bookmark_descr'])))."', '".$user[ AUTH_ID_USER ]."') ");
			$error = $lang['organizer']['user_bookmark_added'];
		}
		BookmarksPage($error);
	}
	return;
}


function SaveUserSiteProfile()
{
	global $lang, $dbconn, $user;
	
	$org_profile_limit = GetSiteSettings('org_profile_limit');
	$data = $_POST;
	
	if (!isset($data['profile_url']) || strip_tags(trim($data['profile_url'])) == '') {
		$error = $lang['organizer']['empty_profile_url'];
	}
	if (!isset($data['profile_descr']) || strip_tags(trim($data['profile_descr'])) == '') {
		$error = $lang['organizer']['empty_profile_descr'];
	}
	
	if ($error) {
		OrganizerMain($error, 1, $data);
	} else {
		$strSQL = "SELECT COUNT(id) FROM ".ORG_USER_SITE_PROFILES_TABLE." WHERE id_user='".$user[ AUTH_ID_USER ]."'";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0] >= $org_profile_limit) {
			$error = $lang['organizer']['user_profile_limit'];
		} else {
			$data['profile_url'] = preg_replace("/http:\/\//is", "", $data['profile_url']);
			$rs = $dbconn->Execute(
				"INSERT INTO ".ORG_USER_SITE_PROFILES_TABLE."
					(link, descr, id_user)
				VALUES
					('".addslashes(strip_tags(trim($data['profile_url'])))."', '".addslashes(strip_tags(trim($data['profile_descr'])))."', '".$user[ AUTH_ID_USER ]."') ");
			$error = $lang['organizer']['user_profile_added'];
		}
		OrganizerMain($error);
	}
	return;
}

function DelUserSiteProfile()
{
	global $lang, $dbconn, $user;
	
	$dbconn->Execute(
		"DELETE FROM ".ORG_USER_SITE_PROFILES_TABLE."
		  WHERE id_user='".$user[ AUTH_ID_USER ]."' AND id='".intval($_GET['id'])."'");
	$error = $lang['organizer']['user_profile_deleted'];
	OrganizerMain($error);
}


function GetUserSiteProfiles($user_id=null)
{
	global $dbconn, $user;
	
	if (!$user_id) {
		$user_id = $user[ AUTH_ID_USER ];
	}
	
	$strSQL =
		"SELECT DISTINCT id, link, descr
		   FROM ".ORG_USER_SITE_PROFILES_TABLE."
		  WHERE id_user='".$user_id."'
	   GROUP BY id
	   ORDER BY id";
	$rs = $dbconn->Execute($strSQL);
	
	if ($rs->fields[0] > 0) {
		$i = 0;
		$_arr = array();
		while (!$rs->EOF) {
			$_arr[$i]['id'] = $rs->fields[0];
			$_arr[$i]['id_user'] = $user_id;
			$_arr[$i]['link'] = stripslashes($rs->fields[1]);
			$_arr[$i]['descr'] = stripslashes($rs->fields[2]);
			$rs->MoveNext();
			$i++;
		}
		return $_arr;
	}
	
	return;
}

function DelUserBookmark()
{
	global $dbconn, $user;
	
	if (isset($_POST['bookmark']) && is_array($_POST['bookmark']) && sizeof($_POST['bookmark'])>0) {
		$dbconn->Execute(" DELETE FROM ".ORG_USER_BOOKMARKS_TABLE." WHERE id IN (".implode(',', $_POST['bookmark']).") AND id_user='".$user[ AUTH_ID_USER ]."' ");
	}
	BookmarksPage();
}

function BillingPage($error = '')
{
	global $lang, $smarty, $dbconn, $user, $config;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$settings = GetSiteSettings(array('show_users_connection_str','show_users_comments','show_users_group_str', 'site_unit_costunit'));

	if ($error){
		$form["err"] = $error;
	}

	$data = array();
	
	// user_account
	$data['cur'] = $settings['site_unit_costunit'];
	$strSQL = "SELECT account_curr, DATE_FORMAT(date_refresh, '".$config["date_format"]."') FROM ".BILLING_USER_ACCOUNT_TABLE." WHERE id_user='".$user[ AUTH_ID_USER ]."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] > 0) {
		$data['account'] = round($rs->fields[0], 2);
		$data['date_refresh'] = $rs->fields[1];
	}
	$form["show_users_connection_str"] = $settings["show_users_connection_str"];
	$form["show_users_comments"] = $settings["show_users_comments"];
	$form["show_users_group_str"] = $settings["show_users_group_str"];

	// user_period
	$strSQL = " SELECT bup.id, UNIX_TIMESTAMP(bup.date_begin), UNIX_TIMESTAMP(bup.date_end), g.name
				FROM ".BILLING_USER_PERIOD_TABLE." bup
				LEFT JOIN ".GROUP_PERIOD_TABLE." gp ON gp.id=bup.id_group_period
				LEFT JOIN ".GROUPS_TABLE." g ON g.id=gp.id_group
				WHERE bup.id_user='".$user[ AUTH_ID_USER ]."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] > 0) {
		$data['user_group_name'] = stripslashes($rs->fields[3]);
		$data['user_days_left'] = round(($rs->fields[2] - time())/(60*60*24));
	} else {
		$data['free_group'] = true;
	}
	
	//	send requests
	$strSQL = " SELECT DISTINCT br.id, br.amount AS count_curr, br.currency, bp.name,
								gp.amount, gp.period, g.name, br.status, br.paysystem,
								DATE_FORMAT(br.date_send, '".$config["date_format"]."'),
								br.id_product
				FROM ".BILLING_REQUESTS_TABLE." br
				LEFT JOIN ".BILLING_PAYSYSTEMS_TABLE." bp ON bp.template_name=br.paysystem
				LEFT JOIN ".GROUP_PERIOD_TABLE." gp ON gp.id=br.id_product
				LEFT JOIN ".GROUPS_TABLE." g ON g.id=gp.id_group
				WHERE br.id_user='".$user[ AUTH_ID_USER ]."'
				GROUP BY br.id
				ORDER BY br.id DESC";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] > 0) {
		$i = 0;
		$_arr = array();
		while (!$rs->EOF) {
			$_arr[$i]['id'] = $rs->fields[0];
			$_arr[$i]['count_curr'] = round($rs->fields[1], 2);
			$_arr[$i]['currency'] = $rs->fields[2];
			if ($rs->fields[8] !='user_account') {
				$_arr[$i]['paysystem_name'] = stripslashes($rs->fields[3]);
			}
			$_arr[$i]['group_amount'] = $rs->fields[4];
			$_arr[$i]['group_period'] = $lang["organizer"][$rs->fields[5]];
			$_arr[$i]['group_name'] = stripslashes($rs->fields[6]);
			$_arr[$i]['status'] = $lang["organizer"][$rs->fields[7]];
			$_arr[$i]['date_send'] = $rs->fields[9];
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign('sendreq', $_arr);
	}

	$strSQL = "	SELECT a.amount AS currency, a.cost AS site_unit, a.currency AS currency_type, DATE_FORMAT(a.date_entry, '".$config["date_format"]."') as date_entry,
							a.entry_type, c.name as groupname, b.amount, b.period, a.id_product, b.id as gp_period_id
					FROM ".BILLING_ENTRY_TABLE." a
					LEFT JOIN ".GROUP_PERIOD_TABLE." b ON b.id=a.id_product
					LEFT JOIN ".GROUPS_TABLE." c ON c.id=b.id_group
					WHERE a.id_user='".$user[ AUTH_ID_USER ]."' ORDER BY a.id DESC ";
	$rs = $dbconn->Execute($strSQL);
	$data["membership_payments"] = 0;
	$data["account_payments"] = 0;
	if ($rs->RowCount() > 0){
		$i = 0;
		$entry = array();
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$entry[$i]["site_unit"] = $row["site_unit"];
			$entry[$i]["currency"] = $row["currency"];
			$entry[$i]["date_entry"] = $row["date_entry"];
			$entry[$i]["groupname"] = $row["groupname"];
			$entry[$i]["count"] = $row["amount"];
			if ($row["id_product"] != '9999') {
				if ($row["entry_type"] !='admin') {
					$data["membership_payments"] = $data["membership_payments"] + $entry[$i]["currency"];
				}
				if ($row["gp_period_id"] != '') {
					$entry[$i]["pay_type"] = $entry[$i]["count"]."&nbsp;".$lang["pays"]["periods"][$row["period"]]."&nbsp;".$entry[$i]["groupname"];
				} else {
					if ($row["gp_period_id"] != 0) {
						$entry[$i]["pay_type"] = $lang["pays"]["for_group_membership"];
					} else {
						$entry[$i]["pay_type"] = $lang["pays"]["site_payment"];
					}
				}
			} else {
				if ($row["entry_type"] !='admin') {
					$data["account_payments"] = $data["account_payments"] + $entry[$i]["currency"];
				}
				$entry[$i]["pay_type"] = $lang["pays"]["account_charging"];
			}
			$entry[$i]["type"] = isset($lang["pays"]["entry_types"][$row["entry_type"]]) ? $lang["pays"]["entry_types"][$row["entry_type"]]: $row["entry_type"];
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign("entry", $entry);
	}

	$data["total_payments"] = $data["membership_payments"] + $data["account_payments"];

	$smarty->assign('data', $data);
	$smarty->assign("form", $form);
	$smarty->assign("script", 'calculator');
	$smarty->assign("header", $lang["homepage"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/organizer_billing_table.tpl");
	exit;

}

function HomePageManagement($error='')
{
	global $lang, $smarty, $dbconn, $user, $config, $theme_ident;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$type = isset($_REQUEST["type"]) ? $_REQUEST["type"] : "layout";

	switch ($type) {
		case "page_styles":

			$strSQL = " SELECT id, link_color, header_color, home_area_color, shoutbox_color,
							menu_back_1_color, menu_back_2_color, menu_back_3_color, menu_back_4_color,
							menu_font_1_color, menu_font_2_color, menu_font_3_color, menu_font_4_color,
							content_color, search_color, main_text_color, text_hidden, big_bg_color, bg_picture_path
						FROM ".ORG_USER_LAYOUTS_TABLE." WHERE id_user='".$user[ AUTH_ID_USER ]."' ";
			$rs = $dbconn->Execute($strSQL);
			$data["link_color"] = ($rs->fields[1] != '') ? $rs->fields[1] : $config["color"]["link"];
			$data["header_color"] = ($rs->fields[2] != '') ? $rs->fields[2] : $config["color"]["header"];
			$data["home_area_color"] = ($rs->fields[3] != '') ? $rs->fields[3] : $config["color"]["home_menu"];
			$data["shoutbox_color"] = ($rs->fields[4] != '') ? $rs->fields[4] : $config["color"]["shoutbox_color"];
			$data["menu_back_1_color"] = ($rs->fields[5] != '') ? $rs->fields[5] : $config["color"]["menu_block_1"];
			$data["menu_back_2_color"] = ($rs->fields[6] != '') ? $rs->fields[6] : $config["color"]["menu_block_2"];
			$data["menu_back_3_color"] = ($rs->fields[7] != '') ? $rs->fields[7] : $config["color"]["menu_block_3"];
			$data["menu_back_4_color"] = ($rs->fields[8] != '') ? $rs->fields[8] : $config["color"]["menu_block_4"];
			$data["menu_font_1_color"] = ($rs->fields[9] != '') ? $rs->fields[9] : $config["color"]["menu_link_1"];
			$data["menu_font_2_color"] = ($rs->fields[10] != '') ? $rs->fields[10] : $config["color"]["menu_link_2"];
			$data["menu_font_3_color"] = ($rs->fields[11] != '') ? $rs->fields[11] : $config["color"]["menu_link_3"];
			$data["menu_font_4_color"] = ($rs->fields[12] != '') ? $rs->fields[12] : $config["color"]["menu_link_4"];
			$data["content_color"] = ($rs->fields[13] != '') ? $rs->fields[13] : $config["color"]["content"];
			$data["search_color"] = ($rs->fields[14] != '') ? $rs->fields[14] : $config["color"]["home_search"];
			$data["main_text_color"] = ($rs->fields[15] != '') ? $rs->fields[15] : $config["color"]["main_text_color"];
			$data["text_hidden"] = ($rs->fields[16] != '') ? $rs->fields[16] : $config["color"]["text_hidden"];
			$data["big_bg_color"] = ($rs->fields[17] != '') ? $rs->fields[17] : $config["color"]["bg_color"];
			$data["bg_picture_path"] = ($rs->fields[18] != '') ? $rs->fields[18] : "";
			if ($data["bg_picture_path"] != "") {
				$settings = GetSiteSettings( array("photos_folder"));
				$data["bg_picture_path"] = $config['server'].$config['site_root'].$settings["photos_folder"]."/".$data["bg_picture_path"];
			}
			break;
		case "my_images":
			break;
		default:
			$type = "layout";
			$strSQL = "SELECT id, area_1, area_2, area_3, area_4, area_5, area_6, area_7, area_8, area_9, area_10, area_11, area_12
						FROM ".ORG_USER_HOME_OPTIONS_TABLE."
						WHERE id_user='".$user[ AUTH_ID_USER ]."' ";
			$rs = $dbconn->Execute($strSQL);
			if ($rs->fields[0]>0) {
				$data[0] = ($rs->fields[1] == 1)? 'true': 'false';
				$data[1] = ($rs->fields[2] == 1)? 'true': 'false';
				$data[2] = ($rs->fields[3] == 1)? 'true': 'false';
				$data[3] = ($rs->fields[4] == 1)? 'true': 'false';
				$data[4] = ($rs->fields[5] == 1)? 'true': 'false';
				$data[5] = ($rs->fields[6] == 1)? 'true': 'false';
				$data[6] = ($rs->fields[7] == 1)? 'true': 'false';
				$data[7] = ($rs->fields[8] == 1)? 'true': 'false';
				$data[8] = ($rs->fields[9] == 1)? 'true': 'false';
				$data[9] = ($rs->fields[10] == 1)? 'true': 'false';
				$data[10] = ($rs->fields[11] == 1)? 'true': 'false';
				$data[11] = ($rs->fields[12] == 1)? 'true': 'false';
			} else {
				$data = array('true','true','true','true','true','true','true','true','true','true','true','true');
			}

			if (isset($theme_ident) && $theme_ident == 'matrimony'){
				$disabled_data_ids = array(0,1,0,0,0,0,0,1,0,0,0,0);
			} else {
				$disabled_data_ids = array(0,0,0,0,0,0,0,0,0,1,1,1);
			}

			$smarty->assign('disabled_data_ids',$disabled_data_ids);
			break;
	}

	$smarty->assign("type", $type);

	$settings = GetSiteSettings(array('show_users_connection_str','show_users_comments','show_users_group_str','use_shoutbox_feature'));

	if ($error){
		$form["err"] = $error;
	}

	$form["show_users_connection_str"] = $settings["show_users_connection_str"];
	$form["show_users_comments"] = $settings["show_users_comments"];
	$form["show_users_group_str"] = $settings["show_users_group_str"];

	$form["use_shoutbox_feature"] = $settings["use_shoutbox_feature"];

	$smarty->assign("data", $data);
	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["homepage"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/organizer_home_management_table.tpl");
	exit;
}

function AJAXResponce()
{
	global $smarty, $dbconn, $user, $config;
	
	$data = explode(',', $_GET['statuses']);
	$str = "";
	$strSQL = " SELECT id FROM ".ORG_USER_HOME_OPTIONS_TABLE." WHERE id_user='".$user[ AUTH_ID_USER ]."' ";
	$rs = $dbconn->Execute($strSQL);
	
	if ($rs->fields[0] < 1) {
		$dbconn->Execute("INSERT INTO ".ORG_USER_HOME_OPTIONS_TABLE." (id_user) VALUES ('".$user[ AUTH_ID_USER ]."') ");
	}
	
	foreach ($data as $key => $value) {
		$str .= " area_".($key+1);
		if ($value == 'true') {
			$str .= "='1'";
		} elseif($value == 'false') {
			$str .= "='0'";
		} else {
			$str .= "= area_".($key+1);
		}
		if (($key+1) != sizeof($data)) {
			$str .= ",";
		}
	}
	$strSQL = "UPDATE ".ORG_USER_HOME_OPTIONS_TABLE." SET ".$str." WHERE id_user='".$user[ AUTH_ID_USER ]."'";
	$dbconn->Execute($strSQL);
	$smarty->assign("data", $data);

	$form = GetSiteSettings(array('use_shoutbox_feature'));
	$smarty->assign("form", $form);

	$output = $smarty->fetch(TrimSlash($config["index_theme_path"])."/organizer_home_small.tpl");
	print_r($output);
}


function OrganizerCalendar()
{
	global $lang, $smarty, $config;
	
	$actions = array(
	"show_period"	=> isset($_REQUEST["show_period"]) ? $_REQUEST["show_period"] : "month",
	"move"			=> isset($_REQUEST["move"]) ? $_REQUEST["move"] : "this",
	"year"			=> isset($_REQUEST["year"]) ? $_REQUEST["year"] : date("Y"),
	"month"			=> isset($_REQUEST["month"]) ? $_REQUEST["month"] : date("n"),
	"week"			=> isset($_REQUEST["week"]) ? $_REQUEST["week"] : date("W"),
	"day"			=> isset($_REQUEST["day"]) ? $_REQUEST["day"] : date("j")
	);
	if ($actions["move"] != "this") {
		$parts = split("_", $actions["move"]);
		if ($parts[1] == "month") {
			switch ($parts[0]) {
				case "back": {
					if ($actions["month"] == 1) {
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
				default:
				break;
			}
		} elseif ($parts[1] == "day") {
			switch ($parts[0]) {
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
				default:
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
			$week = array(
			0 => "false",
			1 => "false",
			2 => "false",
			3 => "false",
			4 => "false",
			5 => "false",
			6 => "false"
			);

			$org_actions = GetOrganizerAction($first_day["year"], $first_day["mon"], $first_day["mday"]);
			$week[$first_day["wday"]] = array(	"mday" => $first_day["mday"],
			"wday" => $first_day["wday"],
			"mon" => $first_day["mon"],
			"year" => $first_day["year"],
			"count_org_actions" => $org_actions['count'],
			"org_actions" => $org_actions['arr']
			);
			if ($current_day["mday"] == $first_day["mday"] &&
			$current_day["mon"] == $first_day["mon"] &&
			$current_day["year"] == $first_day["year"])
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
					6 => "false"
					);
				}
				$org_actions = GetOrganizerAction($this_day["year"], $this_day["mon"], $this_day["mday"]);
				$week[$this_day["wday"]] = array(	"mday" => $this_day["mday"],
				"wday" => $this_day["wday"],
				"mon" => $this_day["mon"],
				"year" => $this_day["year"],
				"count_org_actions" => $org_actions['count'],
				"org_actions" => $org_actions['arr']
				);
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
			$smarty->assign("section",$lang["section"]);
		}
		break;
		default: {
		}
		break;
	}
	$smarty->display(TrimSlash($config["index_theme_path"])."/organizer_calendar_small.tpl");
	return;
}

function GetOrganizerAction($year, $month, $day)
{
	global $dbconn, $user;

	$date_1 = mktime(0, 0, 0, $month, $day, $year);
	$date_2 = mktime(23, 59, 59, $month, $day, $year);

	$strSQL = " SELECT COUNT(id)
				FROM ".ORG_USER_CALENDAR_ACTIONS_TABLE."
				WHERE id_user='".$user[ AUTH_ID_USER ]."' AND (UNIX_TIMESTAMP(action_date)>='".$date_1."' AND UNIX_TIMESTAMP(action_date)<='".$date_2."')
				";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>0) {
		$org_actions = array();
		$org_actions['count'] = $rs->fields[0];
		$strSQL = " SELECT DISTINCT id, action_name
					FROM ".ORG_USER_CALENDAR_ACTIONS_TABLE."
					WHERE id_user='".$user[ AUTH_ID_USER ]."' AND (UNIX_TIMESTAMP(action_date)>='".$date_1."' AND UNIX_TIMESTAMP(action_date)<='".$date_2."')
					GROUP BY id ORDER BY action_date ASC LIMIT 0,3 ";
		$rs = $dbconn->Execute($strSQL);
		$i = 0;
		while(!$rs->EOF){
			$org_actions['arr'][$i]["id"] = $rs->fields[0];
			if ( strlen(utf8_decode($rs->fields[1])) < 10) {
				$org_actions['arr'][$i]["name"] = stripslashes($rs->fields[1]);
			} else {
				$org_actions['arr'][$i]["name"] = stripslashes(utf8_substr($rs->fields[1] , 0 , 10)."...");
			}
			$i++;
			$rs->MoveNext();
		}
		return $org_actions;
	} else {
		return;
	}
}

function OrganizerUserCalendar($month, $year, $day)
{
	global $smarty, $dbconn, $user, $config, $config_index;

	$page = $_GET["page"]?$_GET["page"]:$_POST["page"];
	
	if ((strval($page) == "") || (strval($page) == "0")) {
		$page = 1;
	}

	$date_1 = mktime(0, 0, 0, $month, $day, $year);
	$date_2 = mktime(23, 59, 59, $month, $day, $year);

	$strSQL = " SELECT COUNT(id)
				FROM ".ORG_USER_CALENDAR_ACTIONS_TABLE."
				WHERE id_user='".$user[ AUTH_ID_USER ]."' AND (UNIX_TIMESTAMP(action_date)>='".$date_1."' AND UNIX_TIMESTAMP(action_date)<='".$date_2."')
				";
	$rs = $dbconn->Execute($strSQL);
	$num_records = $rs->fields[0];
	if ($num_records > 0) {
		$lim_min = ($page - 1) * $config_index["search_numpage"];
		$lim_max = $config_index["search_numpage"];
		$limit_str = " LIMIT ".$lim_min.", ".$lim_max;
		$strSQL = " SELECT id, DATE_FORMAT(action_date, '".$config["date_format"]."') as format, UNIX_TIMESTAMP(action_date) as stamp, action_name, action_contain
					FROM ".ORG_USER_CALENDAR_ACTIONS_TABLE."
					WHERE id_user='".$user[ AUTH_ID_USER ]."' AND (UNIX_TIMESTAMP(action_date)>='".$date_1."' AND UNIX_TIMESTAMP(action_date)<='".$date_2."')
					".$limit_str;
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0] > 0) {
			$i = 0;
			$_arr = array();
			while (!$rs->EOF) {
				$_arr[$i]['id'] = $rs->fields[0];
				$_arr[$i]['date'] = $rs->fields[1]." ".date('H:i', $rs->fields[2]);
				$_arr[$i]['name'] = stripslashes($rs->fields[3]);
				$_arr[$i]['contain'] = stripslashes($rs->fields[4]);
				if (intval($_GET['id_action']) == $_arr[$i]['id']) {
					$_arr[$i]['sel'] = 1;
				}
				$rs->MoveNext();
				$i++;
			}
			$param = "organizer.php?sel=bookmarks&";
			$smarty->assign("links", GetLinkArray($num_records, $page, $param, $config_index["search_numpage"]));
		}
	} else {
		$_arr = 'empty';
	}
	return $_arr;
}

function SaveUserAction()
{
	global $lang, $dbconn, $user;
	
	$data = $_POST;
	
	if (!isset($data['action_name']) || strip_tags(trim($data['action_name'])) == '') {
		$error = $lang['organizer']['empty_action_name'];
	}
	if (!isset($data['action_descr']) || strip_tags(trim($data['action_descr'])) == '') {
		$error = $lang['organizer']['empty_action_descr'];
	}

	$date = intval($data['year']).sprintf("%02d",intval($data['month'])).sprintf("%02d",intval($data['day'])).sprintf("%02d",intval($data['hour'])).sprintf("%02d",intval($data['min']))."00";

	if (checkdate(sprintf("%02d",intval($data['month'])), sprintf("%02d",intval($data['day'])),intval($data['year'])) == false) {
		$error = $lang['organizer']['wrong_date'];
	}
	
	if ($error) {
		OrganizerMain($error, 'calendar_action', $data);
	} else {
		$dbconn->Execute(" INSERT INTO ".ORG_USER_CALENDAR_ACTIONS_TABLE."
						(id_user, action_date, action_name, action_contain)
							VALUES
						('".$user[ AUTH_ID_USER ]."', '".$date."', '".addslashes(strip_tags(trim($data['action_name'])))."', '".addslashes(strip_tags(trim($data['action_descr'])))."') ");
		$error = $lang['organizer']['user_action_added'];
		echo "<script>document.location.href='organizer.php?sel=date&year=".intval($data['year'])."&month=".intval($data['month'])."&day=".intval($data['day'])."';</script>";
	}
	return;
}

function DelUserAction()
{
	global $dbconn, $user;

	if (isset($_POST['del_id']) && is_array($_POST['del_id']) && sizeof($_POST['del_id'])>0) {
		$_str = implode(",", $_POST['del_id']);
		$dbconn->Execute(" DELETE FROM ".ORG_USER_CALENDAR_ACTIONS_TABLE." WHERE id_user='".$user[ AUTH_ID_USER ]."' AND id IN (".$_str.") ");
	}
	echo "<script>document.location.href='organizer.php?sel=date&year=".intval($_POST['year'])."&month=".intval($_POST['month'])."&day=".intval($_POST['day'])."';</script>";
	return;
}


function GetUserStatistic()
{
	global $dbconn, $user;
	
	$suffix = "from=organizer";
	
	$_arr = array();
	
	$rs = $dbconn->Execute("Select count(id) from ".MAILBOX_TABLE." where id_to='".$user[ AUTH_ID_USER ]."' and  deleted_to='0' ");
	$_arr[0]["name"] = "emailed_me_count";
	$_arr[0]["value"] = intval($rs->fields[0]);
	$_arr[0]["link"] = 'mailbox.php?sel=inbox&'.$suffix;

	$rs = $dbconn->Execute("Select count(id) from ".MAILBOX_TABLE." where id_to='".$user[ AUTH_ID_USER ]."' and was_read='0' and deleted_to='0' ");
	$_arr[1]["name"] = "emailed_me_new_count";
	$_arr[1]["value"] = intval($rs->fields[0]);
	$_arr[1]["link"] = 'mailbox.php?sel=inbox&'.$suffix;

	$rs = $dbconn->Execute("Select count(id) from ".MAILBOX_TABLE." where id_from='".$user[ AUTH_ID_USER ]."' ");
	$_arr[2]["name"] = "emailed_them_count";
	$_arr[2]["value"]= intval($rs->fields[0]);
	$_arr[2]["link"] = 'mailbox.php?sel=outbox&'.$suffix;

	$rs = $dbconn->Execute("Select count(distinct a.id_from) from ".KISSLIST_TABLE." as a left join ".USERS_TABLE." as b on b.id=a.id_to where a.id_from!='".$user[ AUTH_ID_USER ]."' and a.id_to ='".$user[ AUTH_ID_USER ]."' and b.status='1' and b.guest_user='0'");
	$_arr[3]["name"] = "kiss_me_count";
	$_arr[3]["value"]= intval($rs->fields[0]);
	$_arr[3]["link"] = 'kisses.php?sel=me&'.$suffix;

	$rs = $dbconn->Execute("Select count(distinct a.id_to) from ".KISSLIST_TABLE." as a left join ".USERS_TABLE." as b on b.id=a.id_to where a.id_from='".$user[ AUTH_ID_USER ]."' and a.id_to !='".$user[ AUTH_ID_USER ]."' and b.status='1' and b.guest_user='0'");
	$_arr[4]["name"] = "kiss_them_count";
	$_arr[4]["value"]= intval($rs->fields[0]);
	$_arr[4]["link"] = 'kisses.php?sel=i&'.$suffix;

	$rs = $dbconn->Execute("Select count(distinct id_visiter) from ".PROFILE_VISIT_TABLE." left join ".USERS_TABLE." on id=id_visiter  where id_visiter!='".$user[ AUTH_ID_USER ]."' and id_user='".$user[ AUTH_ID_USER ]."' and status='1' and visible='1' and guest_user='0'");
	$_arr[5]["name"] = "visit_count";
	$_arr[5]["value"]= intval($rs->fields[0]);
	$_arr[5]["link"] = 'visit_my_page.php?'.$suffix;

	$meet_them_arr = GetWantToMeetThemList($user[ AUTH_ID_USER ], false);
	$_arr[6]["name"] = "meet_them_count";
	$_arr[6]["value"]= sizeof($meet_them_arr["id_arr"]);
	$_arr[6]["link"] = 'meet_them.php?'.$suffix;

	$meet_me_arr = GetWantToMeetMeList($user[ AUTH_ID_USER ], true);
	$_arr[7]["name"] = "meet_me_count";
	$_arr[7]["value"]= sizeof($meet_me_arr["id_arr"]);
	$_arr[7]["link"] = 'meet_me.php?'.$suffix;
	
	return $_arr;
}

function SaveHomeColors()
{
	global $dbconn, $user, $config;
	
	$err = "";
	
	$data['link_color'] = isset($_POST['data']['link_color']) && (preg_match('/^(?:(?:[a-f\d]{3}){1,2})$/i', $_POST['data']['link_color'])) ? $_POST['data']['link_color'] : $config["color"]["link"];
	$data['header_color'] = isset($_POST['data']['header_color']) && (preg_match('/^(?:(?:[a-f\d]{3}){1,2})$/i', $_POST['data']['header_color'])) ? $_POST['data']['header_color'] : $config["color"]["header"];
	$data['home_area_color'] = isset($_POST['data']['home_area_color']) && (preg_match('/^(?:(?:[a-f\d]{3}){1,2})$/i', $_POST['data']['home_area_color'])) ? $_POST['data']['home_area_color'] : $config["color"]["home_menu"];
	$data['shoutbox_color'] = isset($_POST['data']['shoutbox_color']) && (preg_match('/^(?:(?:[a-f\d]{3}){1,2})$/i', $_POST['data']['shoutbox_color'])) ? $_POST['data']['shoutbox_color'] : $config["color"]["shoutbox_color"];

	$data['menu_back_1_color'] = isset($_POST['data']['menu_back_1_color']) && (preg_match('/^(?:(?:[a-f\d]{3}){1,2})$/i', $_POST['data']['menu_back_1_color'])) ? $_POST['data']['menu_back_1_color'] : $config["color"]["menu_block_1"];
	$data['menu_back_2_color'] = isset($_POST['data']['menu_back_2_color']) && (preg_match('/^(?:(?:[a-f\d]{3}){1,2})$/i', $_POST['data']['menu_back_2_color'])) ? $_POST['data']['menu_back_2_color'] : $config["color"]["menu_block_2"];
	$data['menu_back_3_color'] = isset($_POST['data']['menu_back_3_color']) && (preg_match('/^(?:(?:[a-f\d]{3}){1,2})$/i', $_POST['data']['menu_back_3_color'])) ? $_POST['data']['menu_back_3_color'] : $config["color"]["menu_block_3"];
	$data['menu_back_4_color'] = isset($_POST['data']['menu_back_4_color']) && (preg_match('/^(?:(?:[a-f\d]{3}){1,2})$/i', $_POST['data']['menu_back_4_color'])) ? $_POST['data']['menu_back_4_color'] : $config["color"]["menu_block_4"];

	$data['menu_font_1_color'] = isset($_POST['data']['menu_font_1_color']) && (preg_match('/^(?:(?:[a-f\d]{3}){1,2})$/i', $_POST['data']['menu_font_1_color'])) ? $_POST['data']['menu_font_1_color'] : $config["color"]["menu_link_1"];
	$data['menu_font_2_color'] = isset($_POST['data']['menu_font_2_color']) && (preg_match('/^(?:(?:[a-f\d]{3}){1,2})$/i', $_POST['data']['menu_font_2_color'])) ? $_POST['data']['menu_font_2_color'] : $config["color"]["menu_link_2"];
	$data['menu_font_3_color'] = isset($_POST['data']['menu_font_3_color']) && (preg_match('/^(?:(?:[a-f\d]{3}){1,2})$/i', $_POST['data']['menu_font_3_color'])) ? $_POST['data']['menu_font_3_color'] : $config["color"]["menu_link_3"];
	$data['menu_font_4_color'] = isset($_POST['data']['menu_font_4_color']) && (preg_match('/^(?:(?:[a-f\d]{3}){1,2})$/i', $_POST['data']['menu_font_4_color'])) ? $_POST['data']['menu_font_4_color'] : $config["color"]["menu_link_4"];

	$data['content_color'] = isset($_POST['data']['content_color']) && (preg_match('/^(?:(?:[a-f\d]{3}){1,2})$/i', $_POST['data']['content_color'])) ? $_POST['data']['content_color'] : $config["color"]["content"];
	$data['search_color'] = isset($_POST['data']['search_color']) && (preg_match('/^(?:(?:[a-f\d]{3}){1,2})$/i', $_POST['data']['search_color'])) ? $_POST['data']['search_color'] : $config["color"]["home_search"];
	$data['main_text_color'] = isset($_POST['data']['main_text_color']) && (preg_match('/^(?:(?:[a-f\d]{3}){1,2})$/i', $_POST['data']['main_text_color'])) ? $_POST['data']['main_text_color'] : $config["color"]["main_text_color"];
	$data['text_hidden'] = isset($_POST['data']['text_hidden']) && (preg_match('/^(?:(?:[a-f\d]{3}){1,2})$/i', $_POST['data']['text_hidden'])) ? $_POST['data']['text_hidden'] : $config["color"]["text_hidden"];

	$settings = GetSiteSettings(array("photos_folder", "photo_max_width", "photo_max_height", "photo_max_size"));
	
	switch ($_POST['bg_type']) {
		case "color":
			$data['big_bg_color'] = isset($_POST['data']['big_bg_color']) && (preg_match('/^(?:(?:[a-f\d]{3}){1,2})$/i', $_POST['data']['big_bg_color'])) ? $_POST['data']['big_bg_color'] : $config["color"]["bg_color"];
			$strSQL = " SELECT id, bg_picture_path FROM ".ORG_USER_LAYOUTS_TABLE." WHERE id_user='".$user[ AUTH_ID_USER ]."' ";
			$rs = $dbconn->Execute($strSQL);
			if ($rs->fields[0] > 0) {
				$old_file = $config['site_path'].$settings["photos_folder"]."/".$rs->fields[1];
				if(file_exists($old_file)){
					@unlink($old_file);
				}
				$strSQL = "UPDATE ".ORG_USER_LAYOUTS_TABLE." SET bg_picture_path='' WHERE id_user='".$user[ AUTH_ID_USER ]."' ";
				$dbconn->Execute($strSQL);
			}
			break;
		case "image":
			$data['big_bg_color'] = '';
			if (isset($_FILES["bg_picture_path"])) {
				$upload = $_FILES["bg_picture_path"];
				$err = UploadBgImage($settings, $upload);
			}
			break;
	}

	$strSQL = " SELECT id FROM ".ORG_USER_LAYOUTS_TABLE." WHERE id_user='".$user[ AUTH_ID_USER ]."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] > 0) {
		$strSQL = " UPDATE ".ORG_USER_LAYOUTS_TABLE."
					SET link_color='".$data['link_color']."', header_color='".$data['header_color']."', home_area_color='".$data['home_area_color']."', shoutbox_color='".$data['shoutbox_color']."',
							menu_back_1_color='".$data['menu_back_1_color']."', menu_back_2_color='".$data['menu_back_2_color']."', menu_back_3_color='".$data['menu_back_3_color']."', menu_back_4_color='".$data['menu_back_4_color']."',
							menu_font_1_color='".$data['menu_font_1_color']."', menu_font_2_color='".$data['menu_font_2_color']."', menu_font_3_color='".$data['menu_font_3_color']."', menu_font_4_color='".$data['menu_font_4_color']."',
							content_color='".$data['content_color']."', search_color='".$data['search_color']."', main_text_color='".$data['main_text_color']."', text_hidden='".$data['text_hidden']."', big_bg_color='".$data['big_bg_color']."'
					WHERE id_user='".$user[ AUTH_ID_USER ]."'
				";
	} else {
		$strSQL = " INSERT INTO ".ORG_USER_LAYOUTS_TABLE." (id_user, link_color, header_color, home_area_color, shoutbox_color,
							menu_back_1_color, menu_back_2_color, menu_back_3_color, menu_back_4_color,
							menu_font_1_color, menu_font_2_color, menu_font_3_color, menu_font_4_color,
							content_color, search_color, main_text_color, text_hidden, big_bg_color)
					VALUES ('".$user[ AUTH_ID_USER ]."', '".$data['link_color']."', '".$data['header_color']."', '".$data['home_area_color']."', '".$data['shoutbox_color']."'
							, '".$data['menu_back_1_color']."', '".$data['menu_back_2_color']."', '".$data['menu_back_3_color']."', '".$data['menu_back_4_color']."'
							, '".$data['menu_font_1_color']."', '".$data['menu_font_2_color']."', '".$data['menu_font_3_color']."', '".$data['menu_font_4_color']."'
							, '".$data['content_color']."', '".$data['search_color']."', '".$data['main_text_color']."', '".$data['text_hidden']."', '".$data['big_bg_color']."')
							";
	}
	$dbconn->Execute($strSQL);
	$_REQUEST["type"] = 'page_styles';
	HomePageManagement($err);
	return;
}

function UploadBgImage($settings, $upload)
{
	global $lang, $dbconn, $user, $config;
	
	$IMG_TYPE_ARRAY = array("image/jpeg", "image/pjpeg", "image/gif", "image/tiff", "image/png", "image/x-png" );
	$IMG_EXT_ARRAY = array("jpeg", "jpg", "gif", "tiff", "png" );
	
	if (!is_uploaded_file($upload['tmp_name'])) {
		return $lang['err']['upload_err'];
	}
	
	$err = '';
	
	$upload_info = getimagesize($upload['tmp_name']);
	if ($upload_info[0] > $settings['photo_max_width']) {
		if (!isset($err)) {
			$err = $lang['err']['upload_err'].': <br>';
		} else {
			$err .= '<br>';
		}
		$err .= str_replace('#WIDTH#', $settings['photo_max_width'], $lang['err']['invalid_photo_width']);
	}
	if ($upload_info[1] > $settings['photo_max_height']) {
		if (!isset($err)) {
			$err = $lang['err']['upload_err'].': <br>';
		} else {
			$err .= '<br>';
		}
		$err .= str_replace('#HEIGHT#', $settings['photo_max_height'], $lang['err']['invalid_photo_height']);
	}
	
	$filename_arr = explode('.', $upload['name']);
	$nr = count($filename_arr);
	$ext = strtolower($filename_arr[$nr-1]);
	
	if (!in_array($upload['type'], $IMG_TYPE_ARRAY) || !in_array($ext, $IMG_EXT_ARRAY)) {
		if (!isset($err)) {
			$err = $lang['err']['upload_err'].': <br>';
		} else {
			$err .= '<br>';
		}
		$err .= $lang['err']['invalid_photo_type'] . implode(', ', $IMG_TYPE_ARRAY);
	}
	
	if ($upload['size'] > getFileSizeFromString($settings['photo_max_size'])) {
		if (!isset($err)) {
			$err = $lang['err']['upload_err'].': <br>';
		} else {
			$err .= '<br>';
		}
		$err .= str_replace('#SIZE#', $this->settings['photo_max_size'], $lang['err']['invalid_photo_size']);
	}
	
	if (isset($err) && $err){
		return $err;
	}
	
	$ex_arr = explode(".",$upload["name"]);
	$extension = $ex_arr[count($ex_arr)-1];
	$new_file_name = $user[ AUTH_ID_USER ]."_".substr(md5(microtime().getmypid()), 0, 8).".".$extension;
	
	$upload_path = $config['site_path'].$settings["photos_folder"]."/".$new_file_name;

	if (copy($upload['tmp_name'], $upload_path)) {
		unlink($upload['tmp_name']);
		$rs = $dbconn->Execute('SELECT id, bg_picture_path FROM '.ORG_USER_LAYOUTS_TABLE.' WHERE id_user = ?', array($user[ AUTH_ID_USER ]));
		if ($rs->fields[0] > 0) {
			$old_file = $config['site_path'].$settings['photos_folder'].'/'.$rs->fields[1];
			if (file_exists($old_file)) {
				@unlink($old_file);
			}
			$strSQL = "UPDATE ".ORG_USER_LAYOUTS_TABLE." SET bg_picture_path='".$new_file_name."' WHERE id_user='".$user[ AUTH_ID_USER ]."' ";
		} else {
			$strSQL = "INSERT INTO ".ORG_USER_LAYOUTS_TABLE." (bg_picture_path, id_user) VALUES ('".$new_file_name."','".$user[ AUTH_ID_USER ]."') ";
		}
		$dbconn->Execute($strSQL);
		return '';
	}
	
	return $lang['err']['upload_err'];
}

?>