<?php
/**
* Banners management (listing, deleting, creating, editing)
*
* @package DatingPro
* @subpackage Admin Mode
**/
include_once '../include/config.php';
include_once '../common.php';
include_once '../include/config_admin.php';
include_once '../include/functions_auth.php';
include_once '../include/functions_admin.php';
include_once '../include/class.phpmailer.php';
include_once '../include/functions_mail.php';

define( "DEF_BANNER_NAME", "");
define( "DEF_BANNER_IMG_PATH", "");
define( "DEF_BANNER_URL", "");
define( "DEF_BANNER_ALT_TEXT", "");

$auth = auth_user();
login_check($auth);
IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "banners");

if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
else
	$file_name = "admin_banners.php";
$smarty->assign("file_name", $file_name);

$sel = isset($_REQUEST["sel"]) ? $_REQUEST["sel"] : "";

switch($sel){
	case "delete": DeleteBanner(); break;
	case "edit":   EditBanner(); break;
	case "add":    AddBanner(); break;
	case "save":   SaveBanner(); break;
	case "save_rotate": SaveRotate();break;

	case "statistics": Statistics(intval($_REQUEST["id"])); break;
	case "user_banners": UserBanners(); break;
	case "get_resols": GetPages(); break;
	case "set_resols": SetPages(); break;
	case "settings": Settings(); break;
	case "save_settings": SaveSettings(); break;
	default: ListBanners();
}

function form_html_code($id)
{
	global $smarty, $dbconn, $config, $config_admin, $page, $lang;
	$strSQL = "select a.*, c.size_x, c.size_y ".
	"from ".BANNERS_TABLE." a, ".BANNERS_SIZES_TABLE." c ".
	"where a.size_id=c.id and a.id=$id";
	$rs = $dbconn->Execute($strSQL);

	if ($rs->EOF)
	{
		die("Logic error. Can`t find banner by id = $id");
	}
	$banner = array();
	$row = $rs->GetRowAssoc(false);

	$banner["id"]             = $row["id"];
	$banner["name"]           = $row["name"];
	$banner["img_file_path"]  = $row["img_file_path"];
	$banner["short_img_file_path"]  = $banner["img_file_path"];
	$banner["status"]         = $row["status"];
	$banner["html_code"]      = stripslashes($row["html_code"]);
	$banner["size_id"]        = $row["size_id"];
	$banner["size_x"]         = $row["size_x"];
	$banner["size_y"]         = $row["size_y"];
	//	$banner["place"]          = $row["place"];
	$banner["alt_text"]       = $row["alt_text"];
	$banner["stop_after_views"]   = $row["stop_after_views"];
	$banner["stop_after_hits"]    = $row["stop_after_hits"];
	$banner["open_in_new_window"] = $row["open_in_new_window"];

	if ($banner["stop_after_hits"]>0)
	{
		$banner["banner_url"] = $config["server"].$config["site_root"]."/admin/admin_banners_activate.php?id=".$banner["id"];
	}
	else
	{
		$banner["banner_url"] = $row["banner_url"];
	}
	$html_text='<a href="'.$banner["banner_url"].'"';
	if ($banner["open_in_new_window"]==1)
	{
		$html_text.=' target=_blank';
	}
	$html_text.=' >';
	$html_text.=' <img src="'.$banner["img_file_path"].'" width="'.$banner["size_x"].'"'.
	' height="'.$banner["size_y"].'" alt="'.$banner["alt_text"].'" border="0" vspace="5">';
	$html_text.=" </a>\n";
	$html_text = addslashes($html_text);
	// Writes banner properties
	$strSQL = "update ".BANNERS_TABLE." set html_code ='".addslashes($html_text)."' where id='".$id."'";
	$dbconn->Execute($strSQL);
	return 0;
}


function SaveBanner()
{
	global $dbconn, $config, $page, $IMG_TYPE_ARRAY, $IMG_EXT_ARRAY, $auth;
	$save = isset($_REQUEST["save"]) ? $_POST["save"] : "edit";
	$edit_type = isset($_REQUEST["edit"]) ? $_REQUEST["edit"] : "image";
	if ($save!="new")
	{
		if (isset($_REQUEST["id"]))  $id = $_REQUEST["id"];
		else
		{
			die("Input error. Unknown _REQUEST[id]");
		}
	}
	else
	{
		$id=0;
	}
	if (isset($_POST["banner_name"])) $banner_name = $_POST["banner_name"]; else $banner_name = "No name";
	$status = (isset($_POST["status"]) &&  $_POST["status"] == 'on') ? 1 : 0;

	$edit_type_id = 0;
	if ($edit_type=="html")
	{ // Gets html
		$edit_type_id = 1;
		if (isset($_POST["banner_html_code"])) $banner_html_code = addslashes($_POST["banner_html_code"]);
		else  $banner_html_code = "";
	}
	else
	{ // If banner type =  image
		if (isset($_FILES["file"])&&isset($_FILES["file"]["tmp_name"])&&($_FILES["file"]["tmp_name"]!=""))
		{  // Upload file mode
			$file_type = $_FILES["file"]["type"];
			$file_name = $_FILES["file"]["name"];
			$temp_file = $_FILES["file"]["tmp_name"];

			$ex_arr = explode(".",$file_name);
			$extension = strtolower($ex_arr[count($ex_arr)-1]);
			if ( (!in_array($file_type, $IMG_TYPE_ARRAY)) || (!in_array($extension, $IMG_EXT_ARRAY)) )
			{
				die("Logic error. Incorrect file type. It should be an image.");
			}
			$f_short_name = "banner".$id."_".date("ymdhis").".".$extension;
			$new_file_name = $config["site_path"]."/uploades/banners/".$f_short_name;
			$res = copy($temp_file, $new_file_name);
			if (!$res)
			{
				die("Logic error. Can`t copy file.");
			}
			$file_img_url = $config["server"].$config["site_root"]."/uploades/banners/".$f_short_name;
		}
		else
		{ // Get exists url mode
			if (isset($_POST["img_file_path"])&&($_POST["img_file_path"]!=""))
			{
				$file_img_url = $_POST["img_file_path"];
			}
			else
			{ // Invalid parameters - no source image - sets default
				$file_img_url = DEF_BANNER_IMG_PATH;
			}
		}
		if (isset($_POST["banner_url"])) $banner_url = $_POST["banner_url"];
		else  $banner_url = DEF_BANNER_URL;
		if (isset($_POST["alt_text"])) $alt_text = $_POST["alt_text"];
		else  $alt_text = DEF_BANNER_ALT_TEXT;
		if ((isset($_POST["stop_after_views"]))&&($_POST["stop_after_views_checked"]=="on"))
		{
			$stop_after_views=$_POST["stop_after_views"];
		}
		else
		{
			$stop_after_views="-1";
		}
		if ((isset($_POST["stop_after_hits"]))&&($_POST["stop_after_hits_checked"]=="on"))
		{
			$stop_after_hits=$_POST["stop_after_hits"];
		}
		else
		{
			$stop_after_hits="-1";
		}
		if ((isset($_POST["b_day"]))&&isset($_POST["b_month"])&&isset($_POST["b_year"])&&($_POST["stop_after_date_checked"]=="on"))
		{
			$stop_after_date=(int)$_POST["b_year"]."-".(int)$_POST["b_month"]."-".(int)$_POST["b_day"];
		}
		else
		{
			$stop_after_date="0000-00-00";
		}
		if ((isset($_POST["open_in_new_window"]))&&($_POST["open_in_new_window"]=="on")) $open_in_new_window = 1;
		else  $open_in_new_window = 0;

	}
	if (isset($_POST["place_size_select"])) $place_and_size = $_POST["place_size_select"];
	else  $place_and_size = 1;

	// Parsing areas
	$areas = array();
	foreach ($_POST as $one_post_key=>$one_post)
	{
		if (strncmp($one_post_key, "area_", strlen("area_"))==0)
		{
			if ($one_post=="on")
			{
				$area_id = substr($one_post_key, strlen("area_"));
				$areas[]=$area_id;
			};
		};
	};
	
	$groups_perm = addslashes(implode(",",$_POST["groups"]));
	
	if ($save=="new")
	{
		if ($edit_type=="html")
		{
			// inserts banner properties
			$strSQL = "insert into ".BANNERS_TABLE." set name ='".$banner_name."', html_code='".$banner_html_code.
			"', status='".$status."', size_id ='".$place_and_size."', banner_type ='".$edit_type_id."', id_user='".$auth[0]."', payment_status='payed', id_group_for='".$groups_perm."'";
		}
		else
		{
			// inserts banner properties
			$strSQL = "insert into ".BANNERS_TABLE." set name ='".$banner_name."', img_file_path ='".$file_img_url."', 
			status='".$status."', size_id ='".$place_and_size."', banner_url ='".$banner_url."', alt_text ='".$alt_text."', 
			stop_after_views ='".$stop_after_views ."', stop_after_hits ='".$stop_after_hits ."', stop_after_date ='".$stop_after_date."',
			open_in_new_window ='".$open_in_new_window ."', banner_type ='".$edit_type_id."', id_user='".$auth[0]."', payment_status='payed', id_group_for='".$groups_perm."'";
		}
		$res = $dbconn->Execute($strSQL);
		$id = $dbconn->Insert_ID();
		$_REQUEST["id"] = $id;
	}
	else
	{
		if ($edit_type=="html")
		{
			// inserts banner properties
			$strSQL = "update ".BANNERS_TABLE." set name ='".$banner_name."', html_code='".$banner_html_code.
			"', status='".$status."', size_id ='".$place_and_size."', banner_type ='".$edit_type_id."'".
			", id_group_for='".$groups_perm."' where id=".$id."";
		}
		else
		{
			// Writes banner properties
			$strSQL = "update ".BANNERS_TABLE." set name ='".$banner_name."', img_file_path ='".$file_img_url.
			"', status='".$status."', size_id ='".$place_and_size.
			"', banner_url ='".$banner_url."', alt_text ='".$alt_text.
			"', stop_after_views ='".$stop_after_views ."', stop_after_hits ='".$stop_after_hits .
			"', stop_after_date ='".$stop_after_date .
			"', open_in_new_window ='".$open_in_new_window ."', banner_type ='".$edit_type_id."'".
			", id_group_for='".$groups_perm."' where id=".$id."";
		}
		$res = $dbconn->Execute($strSQL);
	}
	if ($edit_type=="html")
	{

	}
	else
	{ // Format html code by properties
		form_html_code($id);
	}

	// Saveing banner areas
	// Delete old
	$strSQL = "delete from ".BANNERS_BELONGS_AREA_TABLE." where banner_id=$id";
	$res = $dbconn->Execute($strSQL);
	// Save new
	foreach ($areas as $area_id)
	{
		$strSQL = "insert into ".BANNERS_BELONGS_AREA_TABLE." set banner_id='".$id."', area_id='".$area_id."' ";
		$res = $dbconn->Execute($strSQL);
	}

	
	header("location: admin_banners.php");
}

function AddBanner()
{
	global $smarty, $dbconn, $config, $lang, $file_name;

	AdminMainMenu($lang["banners"]);

	$form["hiddens"]  = "<input type='hidden' name='save' value='new'>";
	$form["hiddens"] .= "<input type='hidden' name='sel' value='save'>";

	if (isset($_REQUEST["edit"])) $edit_type = $_REQUEST["edit"];
	else
	{
		$edit_type = "image";
	}

	$banner["name"]           = DEF_BANNER_NAME;
	$banner["img_file_path"]  = DEF_BANNER_IMG_PATH;
	$banner["short_img_file_path"]  = $banner["img_file_path"];
	$banner["banner_url"]     = DEF_BANNER_URL;
	$banner["status"]         = 1;
	$banner["size_id"]        = 1;
	$banner["html_code"]      = "";

	// Gets banner size
	$strSQL = "select a.* ".
	"from ".BANNERS_SIZES_TABLE." a where a.id='".$banner["size_id"]."'";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->EOF)
	{
		die("Logic error. No one banner sizes");
	}
	$row = $rs->GetRowAssoc(false);
	$banner["size_x"]         = $row["size_x"];
	$banner["size_y"]         = $row["size_y"];
	$banner["place"]          = $row["able_place"];

	$banner["alt_text"]       = DEF_BANNER_ALT_TEXT;
	$banner["stop_after_views"]   = -1;
	$banner["stop_after_hits"]    = -1;
	$banner["stop_after_date"]    = "0000-00-00";
	form_date_selection($banner["stop_after_date"]);

	$banner["open_in_new_window"] = 1;

	// Gets all areas
	$strSQL = "select a.id, a.file_path, a.description, a.left_place, a.bottom_place ".
	"from ".BANNERS_AREA_TABLE." a";
	$rs_area = $dbconn->Execute($strSQL);
	$areas   = array();
	$area  = array();
	$change_left_areas = "";
	$change_bottom_areas = "";
	while (!$rs_area->EOF)
	{
		$row = $rs_area->GetRowAssoc(false);
		$area["id"]          = $row["id"];
		$area["file_path"]   = $row["file_path"];
		$area["description"] = $row["description"];
		$area["checked"]     = 0;
		$area["left_place"]  = $row["left_place"];
		$area["bottom_place"]  = $row["bottom_place"];
		$area["enabled"]=0;
		if (($banner["place"]==0)&&($area["left_place"]==1))
		$area["enabled"] = 1;
		if (($banner["place"]==1)&&($area["bottom_place"]==1))
		$area["enabled"] = 1;
		$areas[$row["id"]]=$area;
		if ($area["left_place"]==0)
		{
			$change_left_areas.= "document.forms[0].area_".$area["id"].".disabled=1;\n";
			$change_left_areas.= "document.forms[0].area_".$area["id"].".checked=0;\n";

		}
		else
		{
			$change_left_areas.= "document.forms[0].area_".$area["id"].".disabled=0;\n";
		}
		if ($area["bottom_place"]==0)
		{
			$change_bottom_areas.= "document.forms[0].area_".$area["id"].".disabled=1;\n";
			$change_bottom_areas.= "document.forms[0].area_".$area["id"].".checked=0;\n";
		}
		else
		{
			$change_bottom_areas.= "document.forms[0].area_".$area["id"].".disabled=0;\n";
		}
		$rs_area->MoveNext();
	}

	// Resort areas
	$resort_areas=array();
	foreach ($areas as $one_area)
	{
		$resort_areas[]=$one_area;
	};
	$areas=$resort_areas;
	unset($resort_areas);

	// Split areas for tow parts
	$tow_areas = array();
	$areas_size = count($areas);
	for ($i=0;$i<$areas_size;$i+=2)
	{
		$tow_areas[$i][0]=$areas[$i];
		if (($i+1)<$areas_size)
		{
			$tow_areas[$i][1]=$areas[$i+1];
		}
		else
		{
			$tow_areas[$i][1]["id"]=-1;
		}
	};
	$banner["areas"]=$tow_areas;

	// Gets all posible banners sizes
	$strSQL = "select a.* ".
	"from ".BANNERS_SIZES_TABLE." a ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->EOF)
	{
		die("Logic error. No one banner sizes");
	}
	$sizes = array();
	$size_able_places=array();
	$change_bar ="";
	$size_id = 1;
	while (!$rs->EOF)
	{
		$row = $rs->GetRowAssoc(false);
		if ($edit_type=="html")
		{
			$sz = "";
		}
		else
		{
			$sz  = "$row[size_x]x$row[size_y]";
		}
		$size = "<option value=\"$row[id]\" ";
		if ($row["id"]==$banner["size_id"])
		{
			$size.="selected ";
		}
		$size.= ">$sz ";
		if ($row["able_place"]==0) $size.="left";
		else $size.="bottom";
		$size.= "</option>";
		$change_bar.=  "  if (sel==$row[id])\n".
		"      {\n".
		"        if (document.banner_img) document.banner_img.width  = $row[size_x];\n".
		"        if (document.banner_img) document.banner_img.height = $row[size_y];\n";
		if ($row["able_place"]==0) $change_bar.="        SetLeftAreas();\n";
		else  $change_bar.="        SetBottomAreas();\n";
		$change_bar.=   "      } ;\n";
		if ($edit_type=="html")
		{
			if (!in_array($row["able_place"], $size_able_places))
			{
				$size_able_places[]  =  $row["able_place"];
				$sizes[$size_id]=$size;
				$sizes_and_places[$row["able_place"]]=$size_id;
				$size_id++;
			}
			else
			{
				if ($row["id"]==$banner["size_id"])
				{
					$old_id = $sizes_and_places[$row["able_place"]];
					$sizes[$old_id]=$size;
				}
			}
		}
		else
		{
			$sizes[$size_id]=$size;
			$size_id++;
		}
		$rs->MoveNext();
	}

	$javascript="function ChangeBar(sel)\n".
	"{\n".
	$change_bar.
	"}\n".
	"function SetLeftAreas()\n".
	"{\n".
	$change_left_areas.
	"}\n".
	"function SetBottomAreas()\n".
	"{\n".
	$change_bottom_areas.
	"}\n";

	//{$one_banner.size_x}x{$one_banner.size_y} ({if $one_banner.place eq 0}{$lang.banners.position_left}{else}{$lang.banners.position_bottom}{/if})
	$strSQL = "SELECT id, name FROM ".GROUPS_TABLE." WHERE type NOT IN ('r', 'm')";
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$groups = array();
	while (!$rs->EOF){
		$row = $rs->GetRowAssoc(false);
		$groups[$i]["id"] = $row["id"];
		$groups[$i]["name"] = $row["name"];
		$rs->MoveNext();
		$i++;
	}
	$banner["groups"] = $groups;	
	
	$smarty->assign('is_add_mode',"1");
	$smarty->assign('edit_type',$edit_type);
	$smarty->assign('javascript',$javascript);
	$smarty->assign('FILE_SELF',$file_name);
	$smarty->assign('lang',$lang);
	$smarty->assign('form',$form);
	$smarty->assign('one_banner',$banner);
	$smarty->assign('posible_sizes',$sizes);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_banners_edit.tpl");
	exit;
}


function DeleteBanner()
{
	global $dbconn, $config;
	$id = intval($_REQUEST["id"]);

	$strSQL = " SELECT id, img_file_path FROM ".BANNERS_TABLE." WHERE id='".$id."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>0) {
		$file_name = substr($rs->fields[1], strlen($config["server"].$config["site_root"]));
		$file_name = str_replace("\\", "/", $file_name);
		if (file_exists($config["site_path"].$file_name)) {
			unlink($config["site_path"].$file_name);
		}
		$strSQL = "DELETE FROM ".BANNERS_TABLE." WHERE id=".$id."";
		$dbconn->Execute($strSQL);
		$strSQL = "DELETE FROM ".BANNERS_BELONGS_AREA_TABLE." WHERE banner_id=".$id."";
		$dbconn->Execute($strSQL);
	}
	ListBanners();
	return;
}

function form_date_selection($sel_date)
{
	global $smarty, $lang, $dbconn;
	if ($sel_date=="0000-00-00") $time_stamp=time();
	else  $time_stamp = strtotime($sel_date);
	$data["b_day"]   = date('d',$time_stamp);
	$data["b_month"] = date('m',$time_stamp);
	$data["b_year"]  = date('Y',$time_stamp);

	$rs = $dbconn->Execute("Select name, value from ".SETTINGS_TABLE." where name in ('min_age_limit', 'max_age_limit')");
	while (!$rs->EOF) {
		$settings[$rs->fields[0]] = $rs->fields[1];
		$rs->MoveNext();
	}

	//// day select
	$day = array();
	for($i=0;$i<31;$i++){
		$day[$i]["value"] = $i+1;
		if(intval($data["b_day"]) == $i+1)
		$day[$i]["sel"] = 1;
		else
		$day[$i]["sel"] = 0;
	}
	$smarty->assign("day", $day);

	////  month select
	$month = array();
	for($i=0;$i<12;$i++){
		$month[$i]["value"] = $i+1;
		$month[$i]["name"] = $lang["month"][$i+1];
		if(intval($data["b_month"]) == $i+1)
		$month[$i]["sel"] = 1;
		else
		$month[$i]["sel"] = 0;
	}
	$smarty->assign("month", $month);

	////  year select
	$j=0;
	$year = array();
	for($i=4;$i>-5;$i--){
		$y = intval(date("Y"))+$i;
		$year[$j]["value"] = $y;
		if(intval($data["b_year"]) == $y)
		$year[$j]["sel"] = 1;
		else
		$year[$j]["sel"] = 0;
		$j++;
	}
	$smarty->assign("year", $year);
	return;
}


function EditBanner()
{
	global $smarty, $dbconn, $config, $lang, $file_name;

	if (isset($_REQUEST["id"])) $id = $_REQUEST["id"];
	else
	{
		die("Error in edit banner, unknown id");
	}
	if (isset($_REQUEST["edit"])) $edit_type = $_REQUEST["edit"];
	else
	{ // try to set old.
		$strSQL = "select a.banner_type ".
		"from ".BANNERS_TABLE." a ".
		"where a.id=$id";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->EOF)
		{
			die("Logic error. Can`t find banner by id = $id");
		}
		$row = $rs->GetRowAssoc(false);
		if ($row["banner_type"]==0) $edit_type = "image";
		else $edit_type = "html";
	}

	AdminMainMenu($lang["banners"]);

	$form["hiddens"]  = "<input type='hidden' name='save' value='edit'>";
	$form["hiddens"] .= "<input type='hidden' name='sel'  value='save'>";
	$form["hiddens"] .= "<input type='hidden' name='id'   value='".$id."'>";

	if ($edit_type=="html")
	{
		$strSQL = "select a.*, c.able_place ".
		"from ".BANNERS_TABLE." a, ".BANNERS_SIZES_TABLE." c ".
		"where a.size_id=c.id and a.id=$id";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->EOF)
		{
			die("Logic error. Can`t find banner by id = $id");
		}
		$banner = array();
		$row = $rs->GetRowAssoc(false);
		$banner["id"]             = $row["id"];
		$banner["name"]           = $row["name"];
		$banner["status"]         = $row["status"];
		$banner["html_code"]      = stripslashes($row["html_code"]);
		$banner["place"]          = $row["able_place"];
		$banner["size_id"]        = $row["size_id"];
		$banner["id_group_for"] = $row["id_group_for"];
	}
	else
	{
		$strSQL = "select a.*, c.size_x, c.size_y, c.able_place ".
		"from ".BANNERS_TABLE." a, ".BANNERS_SIZES_TABLE." c ".
		"where a.size_id=c.id and a.id=$id";
		$rs = $dbconn->Execute($strSQL);

		if ($rs->EOF)
		{
			die("Logic error. Can`t find banner by id = $id");
		}
		$banner = array();
		$row = $rs->GetRowAssoc(false);
		$banner["id"]             = $row["id"];
		$banner["name"]           = $row["name"];
		$banner["img_file_path"]  = $row["img_file_path"];

		$banner["short_img_file_path"]  = $banner["img_file_path"];
		$banner["banner_url"]     = $row["banner_url"];
		$banner["status"]         = $row["status"];
		$banner["html_code"]      = stripslashes($row["html_code"]);
		$banner["size_id"]        = $row["size_id"];
		$banner["size_x"]         = $row["size_x"];
		$banner["size_y"]         = $row["size_y"];
		$banner["place"]          = $row["able_place"];
		$banner["alt_text"]       = $row["alt_text"];
		$banner["stop_after_views"]   = $row["stop_after_views"];
		$banner["stop_after_hits"]    = $row["stop_after_hits"];
		$banner["stop_after_date"]    = $row["stop_after_date"];
		$banner["open_in_new_window"] = $row["open_in_new_window"];
		$banner["id_group_for"] = $row["id_group_for"];
		form_date_selection($banner["stop_after_date"]);
	}

	// Gets all areas
	$strSQL = "select a.id, a.file_path, a.description, a.left_place, a.bottom_place ".
	"from ".BANNERS_AREA_TABLE." a";
	$rs_area = $dbconn->Execute($strSQL);

	$areas   = array();
	$area  = array();
	$change_left_areas = "";
	$change_bottom_areas = "";
	while (!$rs_area->EOF)
	{
		$row = $rs_area->GetRowAssoc(false);
		$area["id"]          = $row["id"];
		$area["file_path"]   = $row["file_path"];
		$area["description"] = $row["description"];
		$area["checked"]     = 0;
		$area["left_place"]  = $row["left_place"];
		$area["bottom_place"]  = $row["bottom_place"];
		$area["enabled"]=0;
		if (($banner["place"]==0)&&($area["left_place"]==1))
		$area["enabled"] = 1;
		if (($banner["place"]==1)&&($area["bottom_place"]==1))
		$area["enabled"] = 1;
		$areas[$row["id"]]=$area;
		if ($area["left_place"]==0)
		{
			$change_left_areas.= "document.forms[0].area_".$area["id"].".disabled=1;\n";
			$change_left_areas.= "document.forms[0].area_".$area["id"].".checked=0;\n";

		}
		else
		{
			$change_left_areas.= "document.forms[0].area_".$area["id"].".disabled=0;\n";
		}
		if ($area["bottom_place"]==0)
		{
			$change_bottom_areas.= "document.forms[0].area_".$area["id"].".disabled=1;\n";
			$change_bottom_areas.= "document.forms[0].area_".$area["id"].".checked=0;\n";
		}
		else
		{
			$change_bottom_areas.= "document.forms[0].area_".$area["id"].".disabled=0;\n";
		}
		$rs_area->MoveNext();
	}

	// Gets belogns area
	$strSQL = "select b.area_id ".
	"from ".BANNERS_BELONGS_AREA_TABLE." b ".
	"where b.banner_id=$banner[id]";
	$rs_area = $dbconn->Execute($strSQL);
	$area   = array();
	while (!$rs_area->EOF)
	{
		$row = $rs_area->GetRowAssoc(false);
		$area_id = $row["area_id"];
		if (($banner["place"]==0)&&($areas[$area_id]["left_place"]==1))
		$areas[$area_id]["checked"] = 1;
		if (($banner["place"]==1)&&($areas[$area_id]["bottom_place"]==1))
		$areas[$area_id]["checked"] = 1;
		$rs_area->MoveNext();
	}
	// Resort areas
	$resort_areas=array();
	foreach ($areas as $one_area)
	{
		$resort_areas[]=$one_area;
	};
	$areas=$resort_areas;
	unset($resort_areas);

	// Split areas for tow parts
	$tow_areas = array();
	$areas_size = count($areas);
	for ($i=0;$i<$areas_size;$i+=2)
	{
		$tow_areas[$i][0]=$areas[$i];
		if (($i+1)<$areas_size)
		{
			$tow_areas[$i][1]=$areas[$i+1];
		}
		else
		{
			$tow_areas[$i][1]["id"]=-1;
		}
	};

	$banner["areas"]=$tow_areas;
	// Gets all posible banners sizes
	$strSQL = "select a.* ".
	"from ".BANNERS_SIZES_TABLE." a ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->EOF)
	{
		die("Logic error. No one banner sizes");
	}
	$sizes = array();
	$size_able_places=array();
	$change_bar ="";
	$size_id = 1;
	while (!$rs->EOF)
	{
		$row = $rs->GetRowAssoc(false);
		if ($edit_type=="html")
		{
			$sz = "";
		}
		else
		{
			$sz  = "$row[size_x]x$row[size_y]";
		}
		$size = "<option value=\"$row[id]\" ";
		if ($row["id"]==$banner["size_id"])
		{
			$size.="selected ";
		}
		$size.= ">$sz ";
		if ($row["able_place"]==0) $size.="left";
		else $size.="bottom";
		$size.= "</option>";
		$change_bar.=  "  if (sel==$row[id])\n".
		"      {\n".
		"        if (document.banner_img) document.banner_img.width  = $row[size_x];\n".
		"        if (document.banner_img) document.banner_img.height = $row[size_y];\n";
		if ($row["able_place"]==0) $change_bar.="        SetLeftAreas();\n";
		else  $change_bar.="        SetBottomAreas();\n";
		$change_bar.=   "      } ;\n";
		if ($edit_type=="html")
		{
			if (!in_array($row["able_place"], $size_able_places))
			{
				$size_able_places[]  =  $row["able_place"];
				$sizes[$size_id]=$size;
				$sizes_and_places[$row["able_place"]]=$size_id;
				$size_id++;
			}
			else
			{
				if ($row["id"]==$banner["size_id"])
				{
					$old_id = $sizes_and_places[$row["able_place"]];
					$sizes[$old_id]=$size;
				}
			}
		}
		else
		{
			$sizes[$size_id]=$size;
			$size_id++;
		}
		$rs->MoveNext();
	}

	$javascript="function ChangeBar(sel)\n".
	"{\n".
	$change_bar.
	"}\n".
	"function SetLeftAreas()\n".
	"{\n".
	$change_left_areas.
	"}\n".
	"function SetBottomAreas()\n".
	"{\n".
	$change_bottom_areas.
	"}\n";

	//{$one_banner.size_x}x{$one_banner.size_y} ({if $one_banner.place eq 0}{$lang.banners.position_left}{else}{$lang.banners.position_bottom}{/if})
	
	$strSQL = "SELECT id, name FROM ".GROUPS_TABLE." WHERE type NOT IN ('r', 'm')";
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$groups = array();
	while (!$rs->EOF){
		$row = $rs->GetRowAssoc(false);
		$groups[$i]["id"] = $row["id"];
		$groups[$i]["name"] = $row["name"];
		if ($banner["id_group_for"] != ''){
			if ($banner["id_group_for"] == '-1')
				$groups[$i]["checked"] = "checked=checked";
			else{
				if (in_array($groups[$i]["id"],explode(",",$banner["id_group_for"]))){
					$groups[$i]["checked"] = "checked=checked";
				}
			}
		}
		$rs->MoveNext();
		$i++;
	}
	$banner["groups"] = $groups;	
	
	$smarty->assign('edit_type',$edit_type);
	$smarty->assign('javascript',$javascript);
	$smarty->assign('id',$id);
	$smarty->assign('FILE_SELF',$file_name);
	$smarty->assign('lang',$lang);
	$smarty->assign('form',$form);
	$smarty->assign('one_banner',$banner);
	$smarty->assign('posible_sizes',$sizes);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_banners_edit.tpl");
	exit;
}

function ListBanners()
{
	global $smarty, $dbconn, $config, $lang;

	AdminMainMenu($lang["banners"]);
	
	$strSQL = "select a.*, c.size_x, c.size_y, c.able_place ".
	"from ".BANNERS_SIZES_TABLE." c, ".BANNERS_TABLE." a
	LEFT JOIN ".USERS_TABLE." ut ON a.id_user=ut.id
	where a.size_id=c.id AND ut.root_user='1'";
	$rs = $dbconn->Execute($strSQL);
	$all_banners = array();
	while (!$rs->EOF)
	{
		
		$banner = array();
		$row = $rs->GetRowAssoc(false);
		$banner["id"]             = $row["id"];
		$banner["name"]           = $row["name"];
		$banner["banner_type"]         = $row["banner_type"];
		$banner["img_file_path"]  = $row["img_file_path"];
		$banner["banner_url"]     = $row["banner_url"];
		$banner["status"]         = $row["status"];
		$banner["html_code"]      = stripslashes($row["html_code"]);
		$banner["size_x"]         = $row["size_x"];
		$banner["size_y"]         = $row["size_y"];

		if ($row["size_x"]>150) $banner["show_size_x"]=150;
		else $banner["show_size_x"]=$row["size_x"];
		if ($row["size_y"]>150) $banner["show_size_y"]=150;
		else $banner["show_size_y"]=$row["size_y"];

		$banner["place"]          = $row["able_place"];
		$banner["alt_text"]       = $row["alt_text"];
		$banner["stop_after_views"]   = $row["stop_after_views"];
		$banner["stop_after_hits"]    = $row["stop_after_hits"];
		$banner["stop_after_date"]    = $row["stop_after_date"];
		if (($banner["stop_after_date"]!="0000-00-00")&&(strtotime($banner["stop_after_date"])<time())) $banner["stoped_by_date"]=1;
		else $banner["stoped_by_date"]=0;
		$banner["open_in_new_window"] = $row["open_in_new_window"];
		$banner["id_group_for"] = $row["id_group_for"];
		
		// Gets area
		$strSQL = "select a.file_path, a.description ".
		"from ".BANNERS_AREA_TABLE." a, ".BANNERS_BELONGS_AREA_TABLE." b ".
		"where a.id=b.area_id and b.banner_id=$banner[id]";
		$rs_area = $dbconn->Execute($strSQL);

		$area   = array();
		$areas  = array();
		while (!$rs_area->EOF)
		{
			$row = $rs_area->GetRowAssoc(false);
			$area["file_path"]   = $row["file_path"];
			$area["description"] = $row["description"];
			$areas[]=$area;
			$rs_area->MoveNext();
		}
		$banner["areas"]=$areas;
		
		if ($banner["id_group_for"] != ''){
			if ($banner["id_group_for"] == '-1')
				$banner["groups"] = "All";
			else{
				$strSQL = "SELECT name FROM ".GROUPS_TABLE." WHERE id IN (".$banner["id_group_for"].")";
				$rs_groups = $dbconn->Execute($strSQL);
				$groups = array();
				while (!$rs_groups->EOF){
					$groups[] = $rs_groups->fields[0];
					$rs_groups->MoveNext();
				}
				$banner["groups"] = implode("; ", $groups);
			}
		}
		
		$rs->MoveNext();
		$all_banners[]=$banner;
	}
	$strSQL = "select a.position, a.rotate_flag, a.rotate_time from ".BANNERS_ROTATE_TABLE." a";
	$rs = $dbconn->Execute($strSQL);
	while (!$rs->EOF)
	{
		$row = $rs->GetRowAssoc(false);
		if ($row["position"]==0) { $smarty->assign('rotate_left_flag', $row["rotate_flag"]); $smarty->assign('rotate_left_time', $row["rotate_time"]); }
		if ($row["position"]==1) { $smarty->assign('rotate_bottom_flag', $row["rotate_flag"]); $smarty->assign('rotate_bottom_time', $row["rotate_time"]); }
		$rs->MoveNext();
	};
	
	$smarty->assign('all_banners',$all_banners);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_banners_table.tpl");
	exit;
}
function SaveRotate()
{
	global $dbconn;
	if (isset($_POST["rotate_left_flag"])&&($_POST["rotate_left_flag"]=="on")) $rotate_left_flag = 1;
	else $rotate_left_flag = 0;
	if (isset($_POST["rotate_bottom_flag"])&&($_POST["rotate_bottom_flag"]=="on")) $rotate_bottom_flag = 1;
	else $rotate_bottom_flag = 0;
	$rotate_left_time = intval($_POST["rotate_left_time"]);
	$rotate_bottom_time = intval($_POST["rotate_bottom_time"]);
	$strSQL = "update ".BANNERS_ROTATE_TABLE." set rotate_flag ='".$rotate_left_flag."', rotate_time ='".$rotate_left_time."' where position='0'";
	$rs = $dbconn->Execute($strSQL);
	$strSQL = "update ".BANNERS_ROTATE_TABLE." set rotate_flag ='".$rotate_bottom_flag."', rotate_time ='".$rotate_bottom_time."' where position='1'";
	$rs = $dbconn->Execute($strSQL);
	ListBanners();
}


function Statistics($id)
{
	global $smarty, $dbconn, $config, $page, $lang, $file_name;

	if (!$id) {
		ListBanners();
	}

	AdminMainMenu($lang["banners"]);

	$page = (isset($_REQUEST["page"]) && !empty($_REQUEST["page"])) ? intval($_REQUEST["page"]) : 1;

	$rows_num_page = (isset($_REQUEST["rows_num_page"]) && !empty($_REQUEST["rows_num_page"])) ? intval($_REQUEST["rows_num_page"]) : 15;
	$smarty->assign("rows_num_page", $rows_num_page);

	$period_type = (isset($_REQUEST["period_type"]) && !empty($_REQUEST["period_type"])) ? $_REQUEST["period_type"] : "day";
	$order = (isset($_REQUEST["order"]) && !empty($_REQUEST["order"])) ? intval($_REQUEST["order"]) : 2;

	if (isset($_REQUEST["sorter"]) && !empty($_REQUEST["sorter"])) {
		$sorter = $_REQUEST["sorter"];
	} else {
		$sorter = $period_type;
	}

	switch ($order){
		case "1":
			$order_str = " ASC";
			$order_new = 2;
			$order_icon = "&darr;";
			break;
		default:
			$order_str = " DESC";
			$order_new = 1;
			$order_icon = "&uarr;";
			break;
	}
	$smarty->assign("order_icon", $order_icon);

	$sorter_str = "  ORDER BY ";
	switch($sorter) {
		case "day": $sorter_str.=" date $order_str"; break;
		case "week": $sorter_str.=" date $order_str, week $order_str"; break;
		case "month": $sorter_str.=" date $order_str, month $order_str"; break;
		case "year": $sorter_str.=" date $order_str, year $order_str"; break;
		case "hits": $sorter_str.=" hits $order_str"; break;
		case "views": $sorter_str.=" views $order_str"; break;
		case "ctr": $sorter_str.=" ctr $order_str"; break;
	}
	$smarty->assign("sorter", $sorter);
	$sort_order_link = "$file_name?sel=statistics&id=$id&order=$order_new&rows_num_page=$rows_num_page&period_type=$period_type&sorter=";

	$lim_min = ($page-1)*$rows_num_page;
	$lim_max = $rows_num_page;
	$limit_str = "LIMIT ".$lim_min.", ".$lim_max;

	if ($period_type == "day") {
		$strSQL = "SELECT COUNT(id) AS cnt FROM ".BANNERS_GLOBAL_STATISTICS." WHERE banner_id='$id'";
		$rs = $dbconn->Execute($strSQL);
		$num_records = $rs->fields[0];

		$strSQL = "SELECT hits, views, ROUND((hits/views)*100, 4) AS ctr, ".
				  "DATE_FORMAT(date, '".$config["date_format"]."') AS date_format ".
				  "FROM ".BANNERS_GLOBAL_STATISTICS." WHERE banner_id='$id' $sorter_str $limit_str";
	} elseif ($period_type == "week") {
		$strSQL = "SELECT COUNT(DISTINCT(week(date, 1))) AS week_cnt ".
				  "FROM ".BANNERS_GLOBAL_STATISTICS." WHERE banner_id='$id' ";
		$rs = $dbconn->Execute($strSQL);
		$num_records = $rs->fields[0];

		$strSQL = "SELECT SUM(hits) AS hits, SUM(views) AS views, ROUND((SUM(hits)/SUM(views))*100, 4) AS ctr, ".
				  "week(date, 1) AS week, month(date) AS month, year(date) as year ".
				  "FROM ".BANNERS_GLOBAL_STATISTICS." WHERE banner_id='$id' ".
				  "GROUP BY week, month, year $sorter_str $limit_str";
	} elseif ($period_type == "month") {
		$strSQL = "SELECT COUNT(DISTINCT(month(date))) AS month_cnt ".
				  "FROM ".BANNERS_GLOBAL_STATISTICS." WHERE banner_id='$id' ";
		$rs = $dbconn->Execute($strSQL);
		$num_records = $rs->fields[0];

		$strSQL = "SELECT SUM(hits) AS hits, SUM(views) AS views, ROUND((SUM(hits)/SUM(views))*100, 4) AS ctr, ".
				  "month(date) AS month, year(date) as year ".
				  "FROM ".BANNERS_GLOBAL_STATISTICS." WHERE banner_id='$id' ".
				  "GROUP BY month, year $sorter_str $limit_str";
	} elseif ($period_type == "year") {
		$strSQL = "SELECT COUNT(DISTINCT(year(date))) AS year_cnt ".
				  "FROM ".BANNERS_GLOBAL_STATISTICS." WHERE banner_id='$id' ";
		$rs = $dbconn->Execute($strSQL);
		$num_records = $rs->fields[0];

		$strSQL = "SELECT SUM(hits) AS hits, SUM(views) AS views, ROUND((SUM(hits)/SUM(views))*100, 4) AS ctr, ".
				  "year(date) as year ".
				  "FROM ".BANNERS_GLOBAL_STATISTICS." WHERE banner_id='$id' ".
				  "GROUP BY year $sorter_str $limit_str";
	}
	$rs = $dbconn->Execute($strSQL);
	$statistics = array();

	while (!$rs->EOF) {
		$statistics[] = $rs->GetRowAssoc( false );
		$rs->MoveNext();
	}
	$smarty->assign("statistics", $statistics);

	$smarty->assign("page", $page);
	$param = "$file_name?sel=statistics&id=$id&order=$order&rows_num_page=$rows_num_page&period_type=$period_type&sorter=".$sorter."&";

	$smarty->assign("links", GetLinkStr($num_records,$page,$param,$rows_num_page));

	/**
	 * Total statistics for all period of banners' existing
	 */
	$strSQL = "SELECT SUM(hits) AS hits, SUM(views) AS views, ROUND((SUM(hits)/SUM(views))*100, 4) AS ctr ".
			  "FROM ".BANNERS_GLOBAL_STATISTICS." WHERE banner_id='$id' ";
	$rs = $dbconn->Execute($strSQL);
	$total = $rs->getRowAssoc( false );
	$smarty->assign("total_stat", $total);

	$form["hiddens"] = array();
	$form["hiddens"][] = array("name" => "id",
								"value" => $id);
	$form["hiddens"][] = array("name" => "sel",
								"value" => "statistics");
	$form["hiddens"][] = array("name" => "sorter",
								"value" => $sorter);
	$form["hiddens"][] = array("name" => "order",
								"value" => $order);
	$form["hiddens"][] = array("name" => "period_type",
								"value" => $period_type);
	$smarty->assign("form", $form);

	/**
	 * Generate rows per page array
	 */
	$cnt = 15;
	$max = $cnt+50;
	$rows_per_page = array();
	for ($i=$cnt; $i<$max; $i+=10) {
		$rows_per_page[] = $i;
	}
	$smarty->assign('rows_per_page', $rows_per_page);

	$period_arr = array("day", "week", "month", "year");

	$smarty->assign("month_name", $lang["month"]);

	$smarty->assign("sort_order_link", $sort_order_link);
	/**
	 * Get Banners type
	 */
	$strSQL = "SELECT type FROM ".BANNERS_TABLE." WHERE id='$id'";
	$rs = $dbconn->Execute($strSQL);
	$banner_type = $rs->fields[0];

	$smarty->assign("type", $banner_type);

	$smarty->assign("period_arr", $period_arr);
	$smarty->assign("period_type", $period_type);
	$smarty->assign('id', $id);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_banners_statistics.tpl");
	exit;
}

function UserBanners()
{
	global $smarty, $dbconn, $config, $lang;

	AdminMainMenu($lang["banners"]);
	if ($_GET["sel"] == 'user_banners'){
		$users_join = "";
		$users_where = "  ";
	}
	$strSQL = "SELECT a.*, c.size_x, c.size_y, c.able_place, ut.id as id_user, ut.login
	FROM ".BANNERS_TABLE." a
	LEFT JOIN ".USERS_TABLE." ut ON a.id_user=ut.id
	LEFT JOIN ".BANNERS_SIZES_TABLE." c ON c.id=a.size_id	
	WHERE ut.root_user='0' ORDER BY FIELD(a.payment_status ,'toaprove', 'topay', 'payed'), id DESC ";
	$rs = $dbconn->Execute($strSQL);
	$all_banners = array();
	$banner = array();
	while (!$rs->EOF)
	{
		$row = $rs->GetRowAssoc(false);
		$banner["id"]             = $row["id"];
		$banner["name"]           = $row["name"];
		$banner["banner_type"]    = $row["banner_type"];
		$banner["img_file_path"]  = $row["img_file_path"];
		$banner["banner_url"]     = $row["banner_url"];
		$banner["status"]         = $row["status"];
		$banner["html_code"]      = stripslashes($row["html_code"]);
		$banner["size_x"]         = $row["size_x"];
		$banner["size_y"]         = $row["size_y"];
		$banner["stop_after_date"]= $row["stop_after_date"];

		if ($row["size_x"]>150) $banner["show_size_x"]=150;
		else $banner["show_size_x"]=$row["size_x"];
		if ($row["size_y"]>150) $banner["show_size_y"]=150;
		else $banner["show_size_y"]=$row["size_y"];

		$banner["place"]          = $row["able_place"];
		$banner["payment_status"] = $row["payment_status"];
		$banner["login"]		= $row["login"];
		$banner["profile_link"] = "admin_users.php?sel=edit&id=".$row["id_user"];
		
		if (($banner["stop_after_date"]!="0000-00-00")&&(strtotime($banner["stop_after_date"])<time())) $banner["stoped_by_date"]=1;
		else $banner["stoped_by_date"]=0;
		
		// Gets area
		$strSQL = "select a.file_path, a.description ".
		"from ".BANNERS_AREA_TABLE." a, ".BANNERS_BELONGS_AREA_TABLE." b ".
		"where a.id=b.area_id and b.banner_id=$banner[id]";
		$rs_area = $dbconn->Execute($strSQL);

		$area   = array();
		$areas  = array();
		while (!$rs_area->EOF)
		{
			$row = $rs_area->GetRowAssoc(false);
			$area["file_path"]   = $row["file_path"];
			$area["description"] = $row["description"];
			$areas[]=$area;
			$rs_area->MoveNext();
		}
		$banner["areas"]=$areas;
		$rs->MoveNext();
		$all_banners[]=$banner;
	}
	$strSQL = "select a.position, a.rotate_flag, a.rotate_time from ".BANNERS_ROTATE_TABLE." a";
	$rs = $dbconn->Execute($strSQL);
	while (!$rs->EOF)
	{
		$row = $rs->GetRowAssoc(false);
		if ($row["position"]==0) { $smarty->assign('rotate_left_flag', $row["rotate_flag"]); $smarty->assign('rotate_left_time', $row["rotate_time"]); }
		if ($row["position"]==1) { $smarty->assign('rotate_bottom_flag', $row["rotate_flag"]); $smarty->assign('rotate_bottom_time', $row["rotate_time"]); }
		$rs->MoveNext();
	};
	$smarty->assign('all_banners',$all_banners);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_banners_users_table.tpl");
	exit;
}

function GetPages($err='')
{
	global $dbconn, $smarty, $config, $lang;
	
	AdminMainMenu($lang["banners"]);
	
	$id_banner = intval($_GET["id"]);

	$strSQL = "SELECT bst.able_place FROM ".BANNERS_TABLE." bt 
				LEFT JOIN ".BANNERS_SIZES_TABLE." bst ON bt.size_id=bst.id
				WHERE bt.id='".$id_banner."' AND bt.payment_status='toaprove'";
	$able_place = $dbconn->GetOne($strSQL);
	
	if ($able_place == '') die("line: ".__LINE__);
	
	switch ($able_place){
		case '0': $able_place = "left_place";break;
		case '1': $able_place = "bottom_place";break;
	}
	
	$strSQL = "SELECT id, description FROM ".BANNERS_AREA_TABLE." WHERE ".$able_place."=1";
	$rs = $dbconn->Execute($strSQL);
	while (!$rs->EOF){
		$row = $rs->GetRowAssoc(false);
		$areas[] = $row;
		$rs->MoveNext();
	}
	
	$form["hiddens"] = "<input type='hidden' name='sel' value='set_resols' />";
	$form["hiddens"] .= "<input type='hidden' name='id' value='".$id_banner."' />";
	$form["error"] = $err;
	$smarty->assign('form',$form);
	$smarty->assign('areas',$areas);	
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_banners_resols_table.tpl");
	exit;
}

function SetPages()
{
	global $dbconn, $file_name, $lang, $config;
	
	$id_banner = intval($_POST["id"]);
	$area_str = addslashes(implode(",",$_POST["area"]));
	
	if ($area_str == ''){
		GetPages($lang["banners"]["blank_area"]);
		exit;
	}
	
	$strSQL = "SELECT bt.id FROM ".BANNERS_TABLE." bt, ".USERS_TABLE." ut
				WHERE bt.id_user=ut.id AND root_user='1' AND bt.id='".$id_banner."'";
	$admins_banner = $dbconn->GetOne($strSQL);
	if($admins_banner){
		GetPages($lang["banners"]["set_pages_admin"]);
		exit;
	}
	
	$strSQL = "UPDATE ".BANNERS_TABLE." set payment_status='topay', resolved_places='".$area_str."' WHERE id='".$id_banner."'";
	$dbconn->Execute($strSQL);
	
	///mail to user
	$strSQL = "SELECT ut.email, ut.fname, ut.sname, ut.site_language, bt.name as banner_name FROM ".BANNERS_TABLE." bt, ".USERS_TABLE." ut 
				WHERE bt.id='".$id_banner."' AND bt.id_user=ut.id AND ut.root_user='0' AND ut.guest_user='0'";
	$rs = $dbconn->Execute($strSQL);
	$user_info = $rs->GetRowAssoc(false);
	$banners_link = "<a href='".$config["server"].$config["site_root"]."/banners.php'>".$lang["section"]["banners"]."</a>";
	
	$site_email = GetSiteSettings('site_email');
	
	$site_lang = $user_info["site_language"];
	
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	$subj = $lang_mail["banner_aprove"]["subject"];
	
	$search = array('[username]', '[banner_name]', '[banner_page_link]');
	$repl = array($user_info["fname"]." ".$user_info["sname"], $user_info["banner_name"], $banners_link);
	$cont_arr["content"] = str_replace($search, $repl, $lang_mail["banner_aprove"]["content"]);
	
	SendMail($site_lang, $user_info["email"], $site_email, $subj, $cont_arr, "mail_banner_aproved");
	
	echo "<script type='text/javascript'>window.opener.document.location='".$file_name."?sel=user_banners' ;window.close();</script>";
}

function Settings($err=''){
	global $dbconn,$smarty, $config, $file_name, $lang;
	
	AdminMainMenu($lang["banners"]);
	
	$settings = GetSiteSettings(array('site_unit_costunit','banner_period_amount','banner_period'));
	
	$strSQL = "SELECT id, description, left_place, bottom_place, price FROM ".BANNERS_AREA_TABLE;
	$rs = $dbconn->Execute($strSQL);
	while (!$rs->EOF) {
		$areas[] = $rs->GetRowAssoc(false);
		$rs->MoveNext();
	}
	
	$form["hiddens"] = "<input type='hidden' name='sel' value='save_settings' />";
	$form["error"] = $err;
	
	$smarty->assign("areas",$areas);
	$smarty->assign("settings",$settings);
	$smarty->assign("file_name",$file_name);
	$smarty->assign("form",$form);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_banners_settings_table.tpl");
	exit;
}

function SaveSettings(){
	global $dbconn, $lang;
	
	$amount = intval($_POST["amount"]);
	$period = $_POST["period"];
	$prices = $_POST["price"];
	
	if ($amount == 0)
		Settings($lang["banners"]["zero_period_amount"]);
	
	if ($period != 'day' && $period != 'month' && $period != 'year' )
		Settings($lang["banners"]["wrong_period"]);
	
	$strSQL = "UPDATE ".SETTINGS_TABLE." SET value='".$amount."' WHERE name='banner_period_amount'";
	$dbconn->Execute($strSQL);
	$strSQL = "UPDATE ".SETTINGS_TABLE." SET value='".$period."' WHERE name='banner_period'";
	$dbconn->Execute($strSQL);
	
	foreach ($prices as $key => $value){
		$price = round(floatval($value),2);
		$strSQL = "UPDATE ".BANNERS_AREA_TABLE." SET price='".$price."' WHERE id='".$key."'";
		$dbconn->Execute($strSQL);
		if ($price == 0){
			$err = $lang["banners"]["err_zero_price"]; 
		}
	}
	Settings($err);
}
?>