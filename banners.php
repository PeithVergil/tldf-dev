<?php
/**
* Banners
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
include './include/class.lang.php';

if ($config['user_banners_feature'] == 0) {
	header('location: homepage.php');
	exit;
}

define('DEF_BANNER_NAME', '');
define('DEF_BANNER_IMG_PATH', '');
define('DEF_BANNER_URL', '');
define('DEF_BANNER_ALT_TEXT', '');

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
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

// dispatcher
switch ($sel) {
	case "delete": DeleteBanner(); break;
	case "edit":   EditBanner(); break;
	case "add":    AddBanner(); break;
	case "save":   SaveBanner(); break;

	case "statistics": Statistics(intval($_REQUEST["id"])); break;
	
	case "activate_form": ActivateForm();break;
	case "activate": Activate();break;
	default: ListBanners();
}

function ListBanners($err='')
{
	global $smarty, $dbconn, $config, $user, $lang;

	$file_name = "banners.php";

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$strSQL = "select a.*, c.size_x, c.size_y, c.able_place 
	from ".BANNERS_TABLE." a
	LEFT JOIN ".BANNERS_SIZES_TABLE." c ON c.id=a.size_id
	where id_user='".$user[ AUTH_ID_USER ]."'  ORDER BY FIELD(a.payment_status , 'topay', 'toaprove', 'payed') , id DESC";
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
		
		$banner["delete_link"] = "banners.php?sel=delete&id=".$banner["id"];
		$banner["activate_link"] = "banners.php?sel=activate_form&id=".$banner["id"];
		
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
	
	$smarty->assign('error',$err);
	$smarty->assign('file_name',$file_name);
	$smarty->assign('lang',$lang);
	$smarty->assign('all_banners',$all_banners);
	$smarty->display(TrimSlash($config["index_theme_path"])."/banners_table.tpl");
	exit;
}

function AddBanner($err='')
{
	global $smarty, $dbconn, $config, $lang, $user;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$form["hiddens"]  = "<input type='hidden' name='save' value='new'>";
	$form["hiddens"] .= "<input type='hidden' name='sel' value='save'>";

	if (isset($_REQUEST["edit"])) 
		$edit_type = $_REQUEST["edit"];
	else{
		$edit_type = "image";
	}

	$banner["name"]           = $_POST["banner_name"];
	$banner["img_file_path"]  = $_POST["img_file_path"];
	$banner["short_img_file_path"]  = $banner["img_file_path"];
	$banner["banner_url"]     = $_POST["banner_url"];
	$banner["status"]         = 1;
	$banner["size_id"]        = $_POST["place_size_select"]?$_POST["place_size_select"]:1;
	$banner["html_code"]      = $_POST["banner_html_code"];
	$banner["link_type"] = $_POST["link_type"];

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

	$banner["myprofile_link"] = $config["server"].$config["site_root"]."/viewprofile.php?id=".$user[ AUTH_ID_USER ];

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

	//{$one_banner.size_x}x{$one_banner.size_y} ({if $one_banner.place eq 0}{$lang.banners.position_left}{else}{$lang.banners.position_bottom}{/if})

	$form["error"] = $err;
	
	$smarty->assign('is_add_mode',"1");
	$smarty->assign('edit_type',$edit_type);
	$smarty->assign('lang',$lang);
	$smarty->assign('form',$form);
	$smarty->assign('one_banner',$banner);
	$smarty->assign('posible_sizes',$sizes);
	$smarty->display(TrimSlash($config["index_theme_path"])."/banners_edit.tpl");
	exit;
}

function SaveBanner()
{
	global $dbconn, $config, $lang, $IMG_TYPE_ARRAY, $IMG_EXT_ARRAY, $user;
	
	$edit_type = isset($_REQUEST["edit"]) ? $_REQUEST["edit"] : "image";

	if (isset($_POST["banner_name"])) $banner_name = $_POST["banner_name"]; else $banner_name = "No name";

	$edit_type_id = 0;
	if ($edit_type=="html")
	{ // Gets html
		$edit_type_id = 1;
		if ($_POST["banner_html_code"] != '') $banner_html_code = addslashes($_POST["banner_html_code"]);
		else AddBanner($lang["banners"]["empty_html_code"]);
	}
	else
	{
		// If banner type =  image
		if (isset($_FILES["file"])&&isset($_FILES["file"]["tmp_name"])&&($_FILES["file"]["tmp_name"]!=""))
		{  // Upload file mode
			$file_type = $_FILES["file"]["type"];
			$file_name = $_FILES["file"]["name"];
			$temp_file = $_FILES["file"]["tmp_name"];

			$ex_arr = explode(".",$file_name);
			$extension = strtolower($ex_arr[count($ex_arr)-1]);
			
			if ( (!in_array($file_type, $IMG_TYPE_ARRAY)) || (!in_array($extension, $IMG_EXT_ARRAY))) {
				die("Logic error. Incorrect file type. It should be an image.");
			}
			
			// RS: we have a logical error here
			// $file_img_url is stored in the banners table and $id is the primary key of the
			// inserted record. $id is unset at this moment.
			// to fix this, either lock the banners table and calculate the new PK, or
			// first insert the record, then get the PK and get the file name, then do
			// file operations and then update the record in the banners table
			$f_short_name = "banner".$id."_".date("ymdhis").".".$extension;
			$new_file_name = $config["site_path"]."/uploades/banners/".$f_short_name;
			$res = copy($temp_file, $new_file_name);
			
			if (!$res) {
				die("Logic error. Can`t copy file.");
			}
			
			$file_img_url = $config["server"].$config["site_root"]."/uploades/banners/".$f_short_name;
		}
		else
		{
			// Get exists url mode
			if (isset($_POST["img_file_path"])&&($_POST["img_file_path"]!="")) {
				$file_img_url = $_POST["img_file_path"];
			} else {
				// Invalid parameters - no source image - sets default
				AddBanner($lang["banners"]["empty_img_url"]);
			}
		}
		
		if (isset($_POST["banner_url"])) $banner_url = $_POST["banner_url"];
		else  $banner_url = DEF_BANNER_URL;
		if (isset($_POST["alt_text"])) $alt_text = $_POST["alt_text"];
		else  $alt_text = DEF_BANNER_ALT_TEXT;

		$stop_after_views="-1";
		$stop_after_hits="-1";
	
		$stop_after_date="0000-00-00";
		$open_in_new_window = 1;

	}
	
	if (isset($_POST["place_size_select"])) {
		$place_and_size = $_POST["place_size_select"];
	}
	
	if ($edit_type == "html")
	{
		// inserts banner properties
		$strSQL = "insert into ".BANNERS_TABLE." set name ='".$banner_name."', html_code='".$banner_html_code.
		"', status='0', size_id ='".$place_and_size."', banner_type ='".$edit_type_id."', id_user='".$user[ AUTH_ID_USER ]."', id_group_for='-1'";
	}
	else
	{
		// inserts banner properties
		$strSQL = "insert into ".BANNERS_TABLE." set name ='".$banner_name."', img_file_path ='".$file_img_url."', 
		status='0', size_id ='".$place_and_size."', banner_url ='".$banner_url."', alt_text ='".$alt_text."', 
		stop_after_views ='".$stop_after_views ."', stop_after_hits ='".$stop_after_hits ."', stop_after_date ='".$stop_after_date."',
		open_in_new_window ='".$open_in_new_window ."', banner_type ='".$edit_type_id."', id_user='".$user[ AUTH_ID_USER ]."', id_group_for='-1'";
	}
	
	$res = $dbconn->Execute($strSQL);
	$id = $dbconn->Insert_ID();
	
	if ($edit_type!="html")
	{ // Format html code by properties
		form_html_code($id);

	}

	header("location: ".$config["server"].$config["site_root"]."/banners.php");
}

function form_html_code($id)
{
	global $dbconn, $config;
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


function DeleteBanner()
{
	global $dbconn, $config, $user;
	$id = intval($_REQUEST["id"]);

	$strSQL = " SELECT id, img_file_path FROM ".BANNERS_TABLE." WHERE id='".$id."' AND id_user=".$user[ AUTH_ID_USER ];
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

function ActivateForm($err='')
{
	global $dbconn, $smarty, $user, $config, $lang;

	$file_name = "banners.php";
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$id_banner = intval($_GET["id"]);
	
	$settings = GetSiteSettings(array('site_unit_costunit','banner_period_amount','banner_period'));
	
	$strSQL = "SELECT account_curr FROM ".BILLING_USER_ACCOUNT_TABLE." WHERE id_user='".$user[ AUTH_ID_USER ]."'";
	$account = $dbconn->GetOne($strSQL)?$dbconn->GetOne($strSQL):0;
	
	
	$strSQL = "SELECT resolved_places FROM ".BANNERS_TABLE." WHERE id='".$id_banner."' AND id_user='".$user[ AUTH_ID_USER ]."'";
	$resolved_places_str = $dbconn->GetOne($strSQL);

	if ($resolved_places_str == '') ListBanners($lang["err"]["error"]);
	
	$strSQL = "SELECT id, description, price FROM ".BANNERS_AREA_TABLE." WHERE id IN (".$resolved_places_str.")";
	$rs = $dbconn->Execute($strSQL);
	while (!$rs->EOF) {
		$areas[] = $rs->GetRowAssoc(false);
		$rs->MoveNext();
	}
	
	$form["hiddens"] = "<input type='hidden' name='sel' value='activate' />";	
	$form["hiddens"] .= "<input type='hidden' id='id_account' name='account' value='".$account."' />";
	$form["hiddens"] .= "<input type='hidden' name='id' value='".$id_banner."' />";
	$form["account_link"] = "payment.php?sel=update_account";	
	$form["error"] = $err;	
	
	$smarty->assign("form",$form);
	$smarty->assign("curr_count",$account);
	$smarty->assign("file_name",$file_name);
	$smarty->assign("areas",$areas);
	$smarty->assign("settings",$settings);
	$smarty->display(TrimSlash($config["index_theme_path"])."/banners_activate.tpl");
	exit;
}

function Activate()
{
	global $dbconn, $user, $lang;
	
	$id_banner = intval($_POST["id"]);
	$area = $_POST["area"];
	$settings = GetSiteSettings(array('banner_period_amount','banner_period','site_unit_costunit'));
	switch ($settings["banner_period"]){
		case "day": $period = "DAY"; break;
		case "month": $period = "MONTH"; break;
		case "year": $period = "YEAR"; break;
		default: $period = "DAY";
	}
	
	$strSQL = "SELECT account_curr FROM ".BILLING_USER_ACCOUNT_TABLE." WHERE id_user='".$user[ AUTH_ID_USER ]."'";
	$account = $dbconn->GetOne($strSQL);
	$area_str = addslashes(implode(",",$area));
	$strSQL = "SELECT SUM(price) FROM ".BANNERS_AREA_TABLE." WHERE id IN (".$area_str.")";
	$summ = $dbconn->GetOne($strSQL);
	if ($summ > $account){
		ActivateForm($lang["banners"]["no_match_money"]);
	}
	
	$strSQL = "SELECT resolved_places FROM ".BANNERS_TABLE." WHERE id='".$id_banner."' AND id_user='".$user[ AUTH_ID_USER ]."'";
	$res_places_str = $dbconn->GetOne($strSQL);
	$res_places_arr = explode(",",$res_places_str);
	$deduction = 0;
	foreach ($area as $value){
		$rs = $dbconn->Execute("SELECT id, price FROM ".BANNERS_AREA_TABLE." WHERE id='".$value."'");
		$id_area = $rs->fields[0];
		$price = $rs->fields[1];
		if (in_array($value,$res_places_arr) && $id_area){
			$strSQL = "DELETE FROM ".BANNERS_BELONGS_AREA_TABLE." WHERE banner_id='".$id_banner."' and area_id='".$id_area."'";
			$dbconn->Execute($strSQL);
			$strSQL = "INSERT INTO ".BANNERS_BELONGS_AREA_TABLE." SET banner_id='".$id_banner."', area_id='".$id_area."'";
			$dbconn->Execute($strSQL);
			$deduction += $price;
		}
	}
	$strSQL = "UPDATE ".BILLING_USER_ACCOUNT_TABLE." SET account_curr = account_curr - ".$deduction." WHERE id_user='".$user[ AUTH_ID_USER ]."'";
	$dbconn->Execute($strSQL);
	
	$strSQL = 'INSERT INTO '.BILLING_ENTRY_TABLE.' SET id_user='.$user[ AUTH_ID_USER ].', amount=-'.$deduction.', currency="'.$settings['site_unit_costunit'].'" , id_group='.PG_SINGLE_CREDIT_POINTS.', id_product=0, entry_type="banners", date_entry=NOW()';
	$dbconn->Execute($strSQL);
	
	$strSQL = "UPDATE ".BANNERS_TABLE." 
				SET status='1', stop_after_date = NOW() + INTERVAL ".intval($settings['banner_period_amount'])." ".$period.", payment_status='payed'
				WHERE id_user='".$user[ AUTH_ID_USER ]."' AND id='".$id_banner."'";
	$dbconn->Execute($strSQL);
	
	header("location: banners.php");
	exit;
}
?>