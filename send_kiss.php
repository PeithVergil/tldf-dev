<?php
/**
* User Send-Kiss file (kisses form, add to hot and black list)
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

// authentication
$user = auth_index_user();

if (empty($user) || $user == 'err' || empty($user[ AUTH_ID_USER ])) {
	header('location: '.$config['site_root'].'/index.php');
	exit;
}

// check guest
if ($user[ AUTH_GUEST ]) {
	AlertPage(GetRightModulePath(__FILE__));
}

// check group, period, expiration
RefreshAccount();

// check status
if (!$user[ AUTH_STATUS ]) {
	AlertPage(GetRightModulePath(__FILE__));
}

// check permissions
// (no permission defined)

// alerts and statistics
if (!$user[ AUTH_GUEST ]) {
	GetAlertsMessage();
	SetModuleStatistic(GetRightModulePath(__FILE__));
}

// active menu item
$smarty->assign('sub_menu_num', '');

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

// dispatcher
switch ($sel) {
	case 'send':
		SendKissForm();
	break;
	
	case 'sendto':
		SendKissTo();
	break;
	
	default:
		SendKissForm();
	break;
}

exit;


///  this function is used in files: advanced_search.php, blog.php. hotlist.php, kises.php
///  meet_me.php, meet_them.php, online.php, perfect_match.php,  quick_search.php, visit_my_page.php, viewprifile.php
///  preveusly this function was defined in all that files
function SendKissForm($err="")
{
	global $lang, $config, $smarty, $dbconn;

	$file_name = "send_kiss.php";
	
	$id_user = $_REQUEST["id_user"];

	IndexHomePage();

	$settings["kiss_folder"] = GetSiteSettings("kiss_folder");

	$ml = new MultiLang();
	
	$strSQL =
		"select distinct a.id, b.".$ml->DefaultFieldName()." as name, a.image_path, a.sorter
		   from ".KISSLIST_SPR_TABLE." a
	  left join ".REFERENCE_LANG_TABLE." b on b.table_key = '".$ml->TableKey(KISSLIST_SPR_TABLE)."' and b.id_reference = a.id
	   order by a.sorter";
	$rs = $dbconn->Execute($strSQL);

	if ($rs->RowCount() > 0) {
		$i = 0;
		$types = array();
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$types[$i]["id"] = $row["id"];
			$types[$i]["name"] = stripslashes($row["name"]);
			if ($row["image_path"] && file_exists($config["site_path"].$settings["kiss_folder"]."/".$row["image_path"]))
				$types[$i]["image_path"] = $config["site_root"].$settings["kiss_folder"]."/".$row["image_path"];
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign("types", $types);
	}

	$form["action"] = $file_name;
	$form["err"] = $err;
	$form["hidden"] = "<input type=hidden name=sel value=sendto>";
	$form["hidden"] .= "<input type=hidden name=id_user value=".$id_user.">";

	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["send_kiss"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/send_kiss_form.tpl");
	exit;
}

function SendKissTo()
{
	global $lang, $dbconn, $user;

	$id_user = intval($_POST["id_user"]);
	$kiss_type = $_POST["kiss_type"];

	if ($id_user && $user[ AUTH_ID_USER ] && intval($kiss_type)){
		$strSQL = "SELECT id FROM ".BLACKLIST_TABLE." WHERE id_user='".$id_user."' AND id_enemy='".$user[ AUTH_ID_USER ]."'";
		$you_banned = $dbconn->GetOne($strSQL);
		if (!$you_banned){
			$strSQL = "Insert into ".KISSLIST_TABLE." (id_to, id_from, kiss_type, kiss_date) values ( '".$id_user."', '".$user[ AUTH_ID_USER ]."', '".intval($kiss_type)."', now()) ";
			$dbconn->Execute($strSQL);
			$err = $lang["err"]["kiss_was_send"];
		}else
			$err = $lang["err"]["cant_send_kiss"];
	}

	SendKissForm($err);
	return;
}
?>