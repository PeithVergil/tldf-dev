<?php
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	config_admin.php	--  include file
//	configuration file
//	admin interface setings
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$config_admin["admin_menu"] = "/include/admin_menu.xml";

$config_admin["newsletter"][0] = "/admin/admin_newsletter.php";

$config_admin["logoff_path"] = "/admin/index.php?sel=logoff";
$config_admin["touser_path"] = "/admin/index.php?sel=usermode";

$config_admin["reference_numpage"] = "30";
$config_admin["users_numpage"] = "10";
$config_admin["group_numpage"] = "30";
$config_admin["news_numpage"] = "30";
$config_admin["country_numpage"] = "30";
$config_admin["region_numpage"] = "30";
$config_admin["city_numpage"] = "30";
$config_admin["upload_numpage"] = "10";
$config_admin["badwords_numpage"] = "30";
$config_admin["taketour_numpage"] = "8";
$config_admin["how_works_numpage"] = "8";
$config_admin["subscribe_numpage"] = "10";
$config_admin["subscribe_user_numpage"] = "30";
$config_admin["subscribe_archive_numpage"] = "30";
$config_admin["subscribe_image_numpage"] = "12";
$config_admin["pays_numpage"] = "30";
$config_admin["pays_user_numpage"] = "30";
$config_admin["addition_numpage"] = "30";
$config_admin["stories_numpage"] = "5";
$config_admin["chat_numpage"] = "20";
$config_admin["giftshop_numpage"] = "10";
$config_admin["location_numpage"] = "10";
$config_admin["club_numpage"] = "10";
$config_admin["questions_numpage"] = "5";

$IMG_TYPE_ARRAY = array("image/jpeg", "image/pjpeg", "image/gif", "image/png", "image/x-png"); // removed: image/tiff
$IMG_EXT_ARRAY = array("jpeg", "jpg", "gif", "png"); // removed: tiff

$AUDIO_TYPE_ARRAY = array("audio/mpeg", "audio/wav", "audio/midi", "audio/mid", "application/octet-stream", "audio/x-ms-wma");
$AUDIO_EXT_ARRAY = array("mp3", "mpg", "wav", "mid", "midi", "wma");

$EMBEDDED_AUDIO_TYPE_ARRAY = array("audio/mpeg");
$EMBEDDED_AUDIO_EXT_ARRAY = array("mp3");

$VIDEO_EXT_ARRAY = array(
	"3gp",		// 3GPP Multimedia
	"3gpp",		// 3GPP Multimedia
	"3g2",		// 3GPP2 Multimedia
	"3gpp2",	// 3GPP2 Multimedia
	"asf",		// advances system format
	"avi",		// audio video interleave
	"flv",		// flash video
	"moov",		// quicktime
	"mov",		// quicktime
	"mqv",		// quicktime
	"qt",		// quicktime
	"mp4",		// mp4
	"mp4v",		// mp4
	"mpg4",		// mp4
	"mpeg",		// mpeg
	"mpg",		// mpeg
	"mpe",		// mpeg
	"m1v",		// mpeg
	"m2v",		// mpeg
	"ogv",		// Ogg Vorbis
	"rm",		// real media
	"swf",		// shockwave flash
	"webm",		// webm
	"wm",		// windows media
	"wmv",		// windows media video
);

$VIDEO_TYPE_ARRAY = array(
	"video/3gpp",						// 3GP, 3GPP 
	"video/3gpp2",						// 3G2, 3GPP2
	"video/x-ms-asf",					// ASF
	"video/avi",						// AVI
	"video/msvideo",					// AVI
	"video/x-msvideo",					// AVI
	"application/x-troff-msvideo",		// AVI
	"application/octet-stream",			// FLV
	"flv-application/octet-stream",		// FLV
	"video/quicktime",					// MOOV, MOV, MQV, QT
	"video/mp4",						// MP4, MP4V, MPG4
	"video/mpeg",						// MPEG, MPG, MPE, M1V, M2V
	"video/ogg",						// OGV
	"application/vnd.rn-realmedia",		// RM
	"application/x-shockwave-flash",	// SWF
	"video/webm",						// WEBM
	"video/x-ms-wm",					// WM
	"video/x-ms-wmv",					// WMV
);

$FLASH_TYPE_ARRAY = array("application/x-shockwave-flash");
$FLASH_EXT_ARRAY = array("swf");

$config_admin["pay_period"]["day"] = 1;
$config_admin["pay_period"]["week"] = 7;
$config_admin["pay_period"]["month"] = 30;
$config_admin["pay_period"]["year"] = 365;

//////// part of lang file which shouldnt be translated
///// text edit tools
$lang["edit_tool"]["b"] = "b";
$lang["edit_tool"]["i"] = "i";
$lang["edit_tool"]["u"] = "u";
$lang["edit_tool"]["br"] = "br";
$lang["edit_tool"]["a"] = "a";
$lang["edit_tool"]["sup"] = "sup";
$lang["edit_tool"]["sub"] = "sub";
$lang["edit_tool"]["code"] = "code";

$lang["colors_name"]["select_fontcolor"] = "font-color";
$lang["colors_name"]["select_bgcolor"] = "bgcolor";
$lang["colors_name"]["black"] = "black";
$lang["colors_name"]["maroon"] = "maroon";
$lang["colors_name"]["brown"] = "brown";
$lang["colors_name"]["red"] = "red";
$lang["colors_name"]["tomato"] = "tomato";
$lang["colors_name"]["orange"] = "orange";
$lang["colors_name"]["gold"] = "gold";
$lang["colors_name"]["yellow"] = "yellow";
$lang["colors_name"]["goldenrod"] = "goldenrod";
$lang["colors_name"]["peru"] = "peru";
$lang["colors_name"]["olive"] = "olive";
$lang["colors_name"]["palegreen"] = "palegreen";
$lang["colors_name"]["lime"] = "lime";
$lang["colors_name"]["green"] = "green";
$lang["colors_name"]["darkgreen"] = "darkgreen";
$lang["colors_name"]["mediumseagreen"] = "mediumseagreen";
$lang["colors_name"]["lightseagreen"] = "lightseagreen";
$lang["colors_name"]["teal"] = "teal";
$lang["colors_name"]["lightsteelblue"] = "lightsteelblue";
$lang["colors_name"]["lightskyblue"] = "lightskyblue";
$lang["colors_name"]["mediumslateblue"] = "mediumslateblue";
$lang["colors_name"]["cyan"] = "cyan";
$lang["colors_name"]["blue"] = "blue";
$lang["colors_name"]["darkblue"] = "darkblue";
$lang["colors_name"]["indigo"] = "indigo";
$lang["colors_name"]["violet"] = "violet";
$lang["colors_name"]["orchid"] = "orchid";
$lang["colors_name"]["plum"] = "plum";
$lang["colors_name"]["thistle"] = "thistle";
$lang["colors_name"]["pink"] = "pink";
$lang["colors_name"]["hotpink"] = "hotpink";
$lang["colors_name"]["lightsalmon"] = "lightsalmon";
$lang["colors_name"]["tan"] = "tan";
$lang["colors_name"]["wheat"] = "wheat";
$lang["colors_name"]["oldlace"] = "oldlace";
$lang["colors_name"]["honeydew"] = "honeydew";
$lang["colors_name"]["aliceblue"] = "aliceblue";
$lang["colors_name"]["white"] = "white";

$lang["fonts_name"]["select_fontface"] = "font-face";
$lang["fonts_name"]["select_fontsize"] = "font-size";
$lang["fonts_name"]["arial"] = "Arial";
$lang["fonts_name"]["arial_black"] = "Arial Black";
$lang["fonts_name"]["arial_narrow"] = "Arial Narrow";
$lang["fonts_name"]["comic_sans_ms"] = "Comic Sans MS";
$lang["fonts_name"]["courier"] = "Courier";
$lang["fonts_name"]["courier_new"] = "Courier New";
$lang["fonts_name"]["lucida_console"] = "Lucida Console";
$lang["fonts_name"]["tahoma"] = "Tahoma";
$lang["fonts_name"]["verdana"] = "Verdana";

$config_admin["reg_dbhost"] = "config\[\"dbhost\"\] = \"([A-Za-z0-9_\.]*)\"";
$config_admin["reg_dbuname"] = "config\[\"dbuname\"\] = \"([A-Za-z0-9_\.]*)\"";
$config_admin["reg_dbpass"] = "config\[\"dbpass\"\] = \"([A-Za-z0-9_\.]*)\"";
$config_admin["reg_dbname"] = "config\[\"dbname\"\] = \"([A-Za-z0-9_\.]*)\"";
$config_admin["reg_dbprefix"] = "config\[\"table_prefix\"\] = \"([A-Za-z0-9_\.]*)\"";

$config_index["clubs_numpage"] = 5;
$config_index["club_uploads_numpage"] = 12;
$config_index["club_news_numpage"] = 10;
?>