<?php
/**
* Succsess stories page
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

$file_name = 'success_stories.php';

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

// dispatcher
switch ($sel) {
	case "photo_view":
		UploadView();
	break;
	
	default:
		ListStories();
	break;
}

exit;


function ListStories()
{
	global $config, $config_index, $smarty, $dbconn, $user, $file_name;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$photos_folder = GetSiteSettings('success_folder');

	$page = isset($_REQUEST["page"])?$_REQUEST["page"]:"";
	if( (strval($page) == "") || (strval($page) == "0")){ $page = 1;}

	$strSQL = "SELECT count(*) FROM ".SUCCESS_STORIES_TABLE;
	$rs = $dbconn->Execute($strSQL);
	$num_records = $rs->fields[0];
	if ($num_records > $config_index["stories_numpage"]) {
		$param = $file_name."?";
		$smarty->assign("links", GetLinkArray($num_records,$page,$param,$config_index["stories_numpage"]));
	}
	$lim_min = ($page-1)*$config_index["stories_numpage"];
	$lim_max = $config_index["stories_numpage"];
	$limit_str = " limit ".$lim_min.", ".$lim_max;

	$strSQL = "SELECT id, couple_name, story_title, image_path_1, image_path_2, image_path_3, description, DATE_FORMAT(story_date,'".$config["date_format"]."') as story_date
						FROM ".SUCCESS_STORIES_TABLE."
						ORDER BY story_date ".$limit_str;
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$story = array();
	if($rs->RowCount()>0){
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$story[$i]["id"] = $row["id"];
			$story[$i]["couple_name"] = stripslashes($row["couple_name"]);
			$story[$i]["story_title"] = stripslashes($row["story_title"]);
			if (($row["image_path_1"]!='0') && (file_exists($config["site_path"].$photos_folder."/".$row["image_path_1"]))) {
				$story[$i]["thumb_path_1"] = $config["site_root"].$photos_folder."/thumb_".$row["image_path_1"];
				$story[$i]["view_link_1"] = "./".$file_name."?sel=photo_view&id_story=".$story[$i]["id"]."&id_photo=1";
			} else {
				$story[$i]["image_path_1"] = '';
			}
			if (($row["image_path_2"]!='0') && (file_exists($config["site_path"].$photos_folder."/".$row["image_path_2"]))) {
				$story[$i]["thumb_path_2"] = $config["site_root"].$photos_folder."/thumb_".$row["image_path_2"];
				$story[$i]["view_link_2"] = "./".$file_name."?sel=photo_view&id_story=".$story[$i]["id"]."&id_photo=2";
			} else {
				$story[$i]["image_path_2"] = '';
			}
			if (($row["image_path_3"]!='0') && (file_exists($config["site_path"].$photos_folder."/".$row["image_path_3"]))) {
				$story[$i]["thumb_path_3"] = $config["site_root"].$photos_folder."/thumb_".$row["image_path_3"];
				$story[$i]["view_link_3"] = "./".$file_name."?sel=photo_view&id_story=".$story[$i]["id"]."&id_photo=3";
			} else {
				$story[$i]["image_path_3"] = '';
			}
			$story[$i]["description"] = stripslashes(nl2br($row["description"]));
			$story[$i]["story_date"] = $row["story_date"];
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign("story", $story);
	}
	$smarty->display(TrimSlash($config["index_theme_path"])."/success_stories_list.tpl");
	exit;
}

function UploadView()
{
	global $smarty, $dbconn, $config;
	
	IndexHomePage();
	
	$folder = GetSiteSettings("success_folder");

	$id_story = intval($_GET["id_story"]);
	$id_photo = intval($_GET["id_photo"]);
	$rs = $dbconn->Execute(
		"SELECT image_path_".$id_photo."
		   FROM ".SUCCESS_STORIES_TABLE."
		  WHERE id='".$id_story."'");
	$data["file_name"] = $rs->fields[0];
	$data["upload_type"] = "f";
	$data["file_path"] = $config["server"].$config["site_root"].$folder."/".$data["file_name"];

	$smarty->assign("data", $data);
	$smarty->display(TrimSlash($config["index_theme_path"])."/myprofile_upload_view.tpl");
	exit;
}
?>