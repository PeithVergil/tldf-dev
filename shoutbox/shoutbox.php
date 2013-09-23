<?php

/**
* Shoutbox draw functions
*
*
* @package DatingPro
* @subpackage Shoutbox module
**/

function GetNewSize($image_file,$maxx,$maxy)
{
	@$size = GetImageSize($image_file);
	if ($size)
	{
		$x = $size[0]; $y = $size[1];
		$newx = $x; $newy = $y;
		if (($x > $maxx)&&($y <= $maxy)
		|| ($x > $maxx)&&($y > $maxy)&&
		(abs($x-$maxx)>abs($y-$maxy)))
		{
			$newx = $maxx;
			$newy = round($newx*$y/$x);
		}
		elseif (($y > $maxy)&&($x <= $maxx)
		|| ($y > $maxy)&&($x > $maxx)&&
		(abs($y-$maxy)>abs($x-$maxx)))
		{
			$newy = $maxy;
			$newx = round($newy*$x/$y);
		}
		return array($newx, $newy);
	}
	return array($maxx, $maxy);
}

global $config, $lang, $dbconn, $user;
include "sh_config.php";

$site_root  = $config["site_root"];
$site_path  = $config["site_path"];
$BGColor    = "0x".$config["color"]["shoutbox_color"];
$Title      = $lang["ShoutBox"]["Title"];
$SndBLabel  = $lang["ShoutBox"]["Shout"];

if (isset($_GET["id"]) && isset($_SESSION["shoutbox_color_".intval($_GET["id"])])) {
	$path_1 = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : "";
	$path_2 = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "";
	$path_3 = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : "";
	if ($path_1 != '' || $path_2 != '' || $path_3 != '') {
		if (strstr($path_1, "viewprofile.php") != "" || strstr($path_2, "viewprofile.php") != "" || strstr($path_3, "viewprofile.php") != "") {
			$BGColor    = "0x".$_SESSION["shoutbox_color_".intval($_GET["id"])];
		}
	}
} else {
	$path_1 = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : "";
	$path_2 = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "";
	$path_3 = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : "";
	if ($path_1 != '' || $path_2 != '' || $path_3 != '') {
		if ( (strstr($path_1, "myprofile.php") != "" || strstr($path_2, "myprofile.php") != "" || strstr($path_3, "myprofile.php") != "" || strstr($path_1, "homepage.php") != "" || strstr($path_2, "homepage.php") != "" || strstr($path_3, "homepage.php") != "") && isset($_SESSION["shoutbox_color_my"])) {
			$BGColor    = "0x".$_SESSION["shoutbox_color_my"];
		}
	}

}
$settings = GetSiteSettings(array("thumb_max_width","thumb_max_height","icons_folder","photos_folder","photos_default"));

$user_id = $user[ AUTH_ID_USER ];
$usr_group = $dbconn->GetOne('SELECT id_group FROM '.USER_GROUP_TABLE.' WHERE id_user='.$user_id);
$usr_gender = $dbconn->GetOne('SELECT gender FROM '.USERS_TABLE.' WHERE id='.$user_id);

$where_str="";

switch($usr_group)
{
	case MM_TRIAL_GUY_ID:
			$where_str=" AND up.vis_guy_1='1' ";
			break;
	case MM_TRIAL_LADY_ID:
			$where_str=" AND up.vis_lady_1='1' ";
			break;
	case MM_REGULAR_LADY_ID:
			$where_str=" AND up.vis_lady_2='1' ";
			break;
	case MM_PLATINUM_LADY_ID:
			$where_str=" AND up.vis_lady_3='1' ";
			break;
	case MM_REGULAR_GUY_ID:
			$where_str=" AND up.vis_guy_2='1' ";
			break;
	case MM_PLATINUM_GUY_ID:
			$where_str=" AND up.vis_guy_3='1' ";
			break;
	case MM_ELITE_GUY_ID:
			$where_str=" AND up.vis_guy_4='1' ";
			break;
	default:
			$where_str="";
}

$strSQL = "SELECT a.id, a.user_id, a.text, a.date_add, b.login, b.icon_path
			FROM ".SHOUTS_TABLE." a
			LEFT JOIN ".USERS_TABLE." AS b ON b.id=a.user_id
			LEFT JOIN ".USER_PRIVACY_SETTINGS." AS up ON up.id_user=a.user_id
			WHERE a.user_id=b.id AND a.status ='1' ".$where_str." OR up.id_user=$user_id ORDER BY a.date_add DESC LIMIT 1";

$rs = $dbconn->Execute($strSQL);
if ($rs===false)
{
	print("<br>\nError: Database error.<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
	exit(0);
}
if ($rs->EOF)
{
	$DefUName   = $lang["ShoutBox"]["DefUser"];
	$DefMessText= $lang["ShoutBox"]["DefMessText"];
	$DefPhotoUrl= $site_root.$settings["photos_folder"]."/".$settings["photos_default"];
	$DefPhotoPath= $site_path.$settings["photos_folder"]."/".$settings["photos_default"];
}
else
{
	$row = $rs->GetRowAssoc(false);
	$DefUName   = stripslashes($row["login"]);
	$DefMessText= urlencode(stripslashes($row["text"]));
	if ($row["icon_path"]=="") $DefPhotoUrl= $site_root.$settings["photos_folder"]."/".$settings["photos_default"];
	else $DefPhotoUrl= $site_root.$settings["icons_folder"]."/".$row["icon_path"];
}
$photo_size = GetNewSize($DefPhotoPath,$settings["thumb_max_width"],$settings["thumb_max_height"]);

$PhotoW=$photo_size[0];
$PhotoH=$photo_size[1];

$params = "site_mode=1&amp;orig_site=".$site_root."/shoutbox&amp;BGColor=".$BGColor."&amp;Title=".$Title."&amp;SndBLabel=".$SndBLabel."&amp;DefUName=".$DefUName."&amp;DefMessText=".$DefMessText."&amp;DefPhotoUrl=".$DefPhotoUrl."&amp;PhotoW=".$PhotoW."&amp;PhotoH=".$PhotoH;
echo "\n".'<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="122" height="270" name="flash_shoutbox_obj" id="flash_shoutbox_obj" align="middle">'."\n";
echo '<param name="allowScriptAccess" value="sameDomain" />'."\n";
echo '<param name="FlashVars" value="'.$params.'"/>'."\n";
echo '<param name="movie"   value="'.$site_root.'/shoutbox/shoutbox.swf" />'."\n";
echo '<param name="quality" value="high" />'."\n";
echo '<param name="wmode" value="transparent" />'."\n";
echo '<embed wmode="transparent" src="'.$site_root.'/shoutbox/shoutbox.swf" FlashVars="'.$params.'" swLiveConnect="true" quality="high" width="122" height="270" name="flash_shoutbox_emb" id="flash_shoutbox_emb" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />'."\n";
echo '</object>'."\n";
?>