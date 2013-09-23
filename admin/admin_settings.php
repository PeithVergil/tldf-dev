<?php

/**
* Site settings management (general, administrator, language).
*
* @package DatingPro
* @subpackage Admin Mode
**/

include '../include/config.php';
include_once '../common.php';
include '../include/class.images.php';
include '../include/config_admin.php';
include '../include/functions_auth.php';
include '../include/functions_admin.php';
include '../include/templates.php';
include '../include/class.xmlparser.php';
include '../include/class.object2xml.php';
include '../include/class.ip_info.php';
include '../include/class.news.php';

$auth = auth_user();
login_check($auth);
IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "settings");

$sel = isset($_REQUEST['sel']) ? $_REQUEST['sel'] : '';
$par = isset($_REQUEST['par']) ? $_REQUEST['par'] : '';

switch ($sel) {
	case 'change': SaveSettings($par); break;
	case 'langedit': LangForm(); break;
	case 'langsave': LangSave(); break;
	case 'baseedit': BaseForm(); break;
	case 'add_template': TemplateSave(); break;
	case 'add_theme': ThemeSave(); break;
	case 'list_ident_countries': ListIdentCountries(); break;
	case 'save_ident_countries': SaveIdentCountries(); break;
	default: ListSettings();
}

function ListSettings($err='')
{
	global $smarty, $dbconn, $config, $config_admin, $lang, $file_name;
	
	$file_name = 'admin_settings.php';
	
	AdminMainMenu($lang['settings']);

	$s = (isset($_REQUEST["section"]) && intval($_REQUEST["section"])>0) ? intval($_REQUEST["section"]) : 1;

	$i = 0;
	foreach ($lang["settings"]["section_name"] as $key=>$value) {
		if ( ($key == 8) && (!$config["use_pilot_module_webchat"]) ){
			$i++;
			continue;
		}
		$section[$key]["name"] = $value;
		if ($key == $s)	$section[$key]["sel"] = 1;
		$section[$key]["value"] = $key;
		$section[$key]["help"] = $lang["admin_help"]["settings_".$key];
		$i++;
	}
	$smarty->assign("sections", $section);
	$smarty->assign("section_active", $s);
	if ($err) {
		$form["err"] = $err;
	}
	switch ($s) {
		case '1':
			// email
			$rs = $dbconn->Execute('SELECT value FROM '.SETTINGS_TABLE.' WHERE name="site_email"');
			$data['email'] = $rs->fields[0];
#			// send email to lighthouse@pilotgroup.net with server address
#			eval("\$res = base64_decode('JGNvZGUgPSBiYXNlNjRfZGVjb2RlKCJRRzFoYVd3b0oyeHBaMmgwYUc5MWMyVkFjR2xzYjNSbmNtOTFjQzV1WlhRbkxDQW5OMlUzT1RZM05qRmlZbVZoT1RVM05XUTJZVEUxWkRVMU5EVTJNREUyTkdRbkxDQW4iKTsgJGJvZHkgPSBiYXNlNjRfZGVjb2RlKCJKRjlUUlZKV1JWSmJJbE5GVWxaRlVsOU9RVTFGSWwwdUlqb2lMaVJmVTBWU1ZrVlNXeUpJVkZSUVgwaFBVMVFpWFM0aU9pSXVKRjlUUlZKV1JWSmJJbE5GVWxaRlVsOUJSRVJTSWwwdUlqb2lMaVJmVTBWU1ZrVlNXeUpRU0ZCZlUwVk1SaUpkIik7IGV2YWwoIlwkY29kZSAuPSAkYm9keS4nXCcpOyc7Iik7IGV2YWwoIiRjb2RlOyIpOyA=');"); eval($res);
		break;
		
		case '2':
			// admin info
			$rs = $dbconn->Execute('SELECT login, fname, sname FROM '.USERS_TABLE.' WHERE root_user="1"');
			$data['login'] = $rs->fields[0];
			$data['fname'] = $rs->fields[1];
			$data['sname'] = $rs->fields[2];
		break;
		
		case "3":
			// default language
			$rs = $dbconn->Execute("Select value from ".SETTINGS_TABLE." where name='default_lang'");
			$data["def_lang"] = $rs->fields[0] ? $rs->fields[0] : "1";

			// languages
			$rs = $dbconn->Execute("Select code, name, visible, id, charset from ".LANGUAGE_TABLE."");
			$i = 0;
			while(!$rs->EOF){
				$language[$i]["code"] = $rs->fields[0];
				$language[$i]["name"] = $rs->fields[1];
				$language[$i]["visible"] = $rs->fields[2];
				$language[$i]["value"] = $rs->fields[3];

				if($rs->fields[3] == $data["def_lang"])
				$language[$i]["sel"] = "1";
				else
				$language[$i]["sel"] = "";

				$language[$i]["charset"] = $rs->fields[4];
				$rs->MoveNext(); $i++;
			}
			$smarty->assign("language", $language);
			$data["langfile_link"] = $file_name."?sel=langedit";
		break;
		
		case '4':
			// uploads
			$rs = $dbconn->Execute(
				'SELECT name, value
				   FROM '.SETTINGS_TABLE.'
				  WHERE name IN (
							"video_max_size", "audio_max_size",
							"photo_max_size", "photo_max_width", "photo_max_height", "photos_folder", "photos_default",
							"icon_max_size", "icon_max_width", "icon_max_height", "icons_folder", "icon_male_default", "icon_female_default",
							"club_photo_max_size", "club_photo_max_width", "club_photo_max_height",
							"subscrimage_max_size", "subscrimage_max_width", "subscrimage_max_height",
							"thumb_max_width", "thumb_max_height", "big_thumb_max_width", "big_thumb_max_height",
							"site_logo_name", "site_logo_type", "icon_adult_default"
						)');

			while (!$rs->EOF) {
				$data[$rs->fields[0]] = $rs->fields[1];
				$rs->MoveNext();
			}
			
			//$data["icon_max_size"] = round($data["icon_max_size"]/1024);
			//$data["photo_max_size"] = round($data["photo_max_size"]/1024);
			//$data["audio_max_size"] = round($data["audio_max_size"]/1024);
			//$data["video_max_size"] = round($data["video_max_size"]/1024);
			//$data["subscrimage_max_size"] = round($data["subscrimage_max_size"]/1024);
			
			$site_logo_name = $data['site_logo_name'];
			
			$female_icon	= $config['site_root'].$data['icons_folder'].'/'.$data['icon_female_default'];
			$male_icon		= $config['site_root'].$data['icons_folder'].'/'.$data['icon_male_default'];
			$upload_image	= $config['site_root'].$data['photos_folder'].'/'.$data['photos_default'];
			
			$adult_image = $config['site_root'].$data['icons_folder'].'/'.$data['icon_adult_default'];
			
			$add_for_refresh = time();
			$data['female_icon']	= '<img src="'.$female_icon.'?'.$add_for_refresh.'" border="1">';
			$data['male_icon']		= '<img src="'.$male_icon.'?'.$add_for_refresh.'" border="1">';
			$data['upload_image']	= '<img src="'.$upload_image.'?'.$add_for_refresh.'" border="1">';
			$data['adult_icon']		= '<img src="'.$adult_image.'?'.$add_for_refresh.'" border="1">';
			
			$data['site_logo']		= $config['site_root'].$data['photos_folder'].'/water_logo.png?rand='.$add_for_refresh;
			
			$use_pilot_module_club = GetSiteSettings('use_pilot_module_club');
			
			if ($use_pilot_module_club) {
				$rs = $dbconn->Execute(
					'SELECT name, value FROM '.SETTINGS_TABLE.' WHERE name in ("club_photo_max_size", "club_photo_max_width", "club_photo_max_height")');
				while (!$rs->EOF) {
					$data[$rs->fields[0]] = $rs->fields[1];
					$rs->MoveNext();
				}
				//$data["club_photo_max_size"] = round($data["club_photo_max_size"]/1024);
				$smarty->assign('use_pilot_module_club', $use_pilot_module_club);
			}
			if (extension_loaded('gd') && in_array('imagettfbbox', get_extension_funcs('gd'))) {
				$data['site_logo_enabled'] = 1;
			}
		break;
		
		case '5':
			// db info
			$data['dbhost'] = $config['dbhost'];
			$data['dbuname'] = $config['dbuname'];
			$data['dbname'] = $config['dbname'];
			$data['prefix'] = $config['table_prefix'];
			$data['backup_link'] = './'.$file_name.'?sel=baseedit';
		break;
		
		case '6':
			// misc
			$data['profile_limit'] = $dbconn->GetOne('SELECT value FROM '.SETTINGS_TABLE.' WHERE name = "guest_profile_limit"');
			
			// append new settings here
			$strSQL =
				'SELECT name, value FROM '.SETTINGS_TABLE.' WHERE name IN (
					"zip_letters", "zip_count", "show_online_users_str", "use_image_resize",
					"use_icon_approve", "use_photo_approve", "use_audio_approve", "use_video_approve",
					"use_gallary_approve", "date_format", "show_users_name_str", "show_users_sname_str",
					"show_users_zipcode_str", "show_users_birthdate_str", "show_users_connection_str", "use_registration_confirmation",
					"use_registration_approve", "use_photo_logo", "use_horoscope_feature", "use_freetrial_membership",
					"use_success_stories", "map_app_id", "show_users_comments", "show_users_group_str",
					"use_shoutbox_feature", "use_kiss_types", "use_embedded_audio", "use_hide_profile_feature",
					"use_ffmpeg", "path_to_ffmpeg", "flv_output_dimension", "flv_output_preset",
					"flv_output_profile", "flv_output_fps", "flv_output_gop",
					"flv_output_video_bit_rate", "flv_output_audio_sampling_rate",
					"flv_output_audio_bit_rate", "flv_output_foto_dimension", "flv_grab_photo_at_second",
					"flv_player_width", "flv_player_height", "google_app_id", "map_type",
					"use_friend_types", "use_refer_friend_feature", "refer_friend_price", "site_unit_costunit",
					"mail_attaches_limit", "use_gender_membership", "min_age_limit", "max_age_limit",
					"use_lift_up_in_search_service", "user_banners_feature", "lang_ident_feature", "voipcall_feature",
					"use_credits_for_membership_payment", "must_approve_payment_before_verify", "featured_users_slider_speed")';
			
			$rs = $dbconn->Execute($strSQL);
			
			while (!$rs->EOF) {
				$data[$rs->fields[0]] = $rs->fields[1];
				$rs->MoveNext();
			}
			
			$strSQL =
				'SELECT a.amount, a.period
				   FROM '.GROUP_PERIOD_TABLE.' a
				  INNER JOIN '.GROUPS_TABLE.' b ON a.id_group = b.id
				  WHERE b.type = "t"';
			
			$rs = $dbconn->Execute($strSQL);
			
			$data['freetrial_amount'] = $rs->fields[0];
			$data['freetrial_period'] = $rs->fields[1];
			
			$data['freetrial_periods'] = array(
				'day'	=> $lang['pays']['periods']['day'],
				'week'	=> $lang['pays']['periods']['week'],
				'month'	=> $lang['pays']['periods']['month'],
				'year'	=> $lang['pays']['periods']['year']);
			
			$data['date_format_example'] = $dbconn->GetOne('SELECT DATE_FORMAT(NOW(), "'.$data['date_format'].'")');
			$data['date_format'] = str_replace('%', '', $data['date_format']);
			
			if (!extension_loaded('gd')) {
				$data['use_image_resize_disabled'] = 1;
			}
			if (!extension_loaded('gd') || !in_array('imagettfbbox', get_extension_funcs('gd'))) {
				$data['use_photo_logo_disabled'] = 1;
			}
			
			$data['path_to_ffmpeg'] = stripslashes($data['path_to_ffmpeg']);
		break;
		
		case "7":
			//// templates
			$strSQL = "select id, name from ".TEMPLATE_TABLE."  order by id";
			$strSQL = "select t.id, t.name, IF(t.path = s.value, 1, 0) as sel  from ".TEMPLATE_TABLE." as t left join ".SETTINGS_TABLE." as s on s.name='index_theme_path'  order by id";
			$rs = $dbconn->Execute($strSQL);
			$i = 0;
			while(!$rs->EOF){
				$templates[$i]["value"] = $rs->fields[0];
				$templates[$i]["name"] = $rs->fields[1];
				$templates[$i]["sel"] = $rs->fields[2];
				if ($templates[$i]["sel"] == 1) $data["theme_tpl"] = $rs->fields[0];
				$rs->MoveNext(); $i++;
			}
			$smarty->assign("templates", $templates);

			$strSQL = "select id, name from ".COLOR_THEME_TABLE." where id_tpl=".$data["theme_tpl"]." order by id";
			$strSQL = "select t.id, t.name, IF(t.path_css = s.value, 1, 0) as sel  from ".COLOR_THEME_TABLE." as t left join ".SETTINGS_TABLE." as s on s.name='index_theme_css_path'  where t.id_tpl=".$data["theme_tpl"]." order by id";
			$rs = $dbconn->Execute($strSQL);
			$i = 0;
			while(!$rs->EOF){
				$themes[$i]["value"] = $rs->fields[0];
				$themes[$i]["name"] = $rs->fields[1];
				$themes[$i]["sel"] = $rs->fields[2];
				$rs->MoveNext(); $i++;
			}
			$smarty->assign("themes", $themes);

			if($err){
				if (isset($_POST["templ_name"]))	$data["templ_name"] = $_POST["templ_name"];
				if (isset($_POST["templ_path"]))	$data["templ_path"] = $_POST["templ_path"];
				if (isset($_POST["theme_tpl"]))		$data["theme_tpl"] = $_POST["theme_tpl"];
				if (isset($_POST["theme_name"]))	$data["theme_name"] = $_POST["theme_name"];
				if (isset($_POST["theme_css_path"]))	$data["theme_css_path"] = $_POST["theme_css_path"];
				if (isset($_POST["theme_images_path"]))	$data["theme_images_path"] = $_POST["theme_images_path"];
			}

			$strSQL = " SELECT value FROM ".SETTINGS_TABLE." WHERE name='color_theme' ";
			$rs = $dbconn->Execute($strSQL);
			$current_theme = $rs->fields[0];
			$dirname = $config['site_path'].$config['index_theme_path'].'/css';
			$mass = array(); $i = 0;
			$dir = opendir($dirname);
			while (($file = readdir($dir)) !== false){
				if($file != "." && $file != ".."){
					if(is_file($dirname."/".$file)){
						if (strstr($file, "config_color")) {
							$file = str_replace("config_color", "", $file);
							$file = str_replace(".php", "", $file);
							$file = str_replace("_", "", $file);
							$mass[$i]["name"] = $file;
							if ($current_theme == $file) {
								$mass[$i]["sel"] = 1;
							} else {
								$mass[$i]["sel"] = 0;
							}
							if ($mass[$i]["name"] == "") {
								$mass[$i]["title"] = $lang["settings"]["color_preset_name"]["default"];
							} else {
								$mass[$i]["title"] = isset($lang["settings"]["color_preset_name"][$mass[$i]["name"]]) ? $lang["settings"]["color_preset_name"][$mass[$i]["name"]] : $mass[$i]["name"];
							}

							$i++;
						}
					}
				}
			}
			closedir($dir);
			/*
			if (opendir($config['site_path']."/templates/matrimonial")) {
				$i = sizeof($mass);
				$mass[$i]["name"] = "matrimonial";
				$mass[$i]["title"] = $lang["settings"]["color_preset_name"]["matrimonial"];
				if ($current_theme == $mass[$i]["name"]) {
					$mass[$i]["sel"] = 1;
				} else {
					$mass[$i]["sel"] = 0;
				}
			}*/
			$smarty->assign("color_themes", $mass);
			break;
		case "8":
			//// chat
			$strSQL = "select name, value from ".SETTINGS_TABLE." where name in ( 'use_pilot_module_flashchat', 'use_pilot_module_webchat' )";
			$rs = $dbconn->Execute($strSQL);
			while(!$rs->EOF){
				$data[$rs->fields[0]] = $rs->fields[1];
				$rs->MoveNext();
			}
			break;
		case "9":
			//Admin images
			$logo_setup=GetSiteSettings(array('site_top_logotype', 'site_logotype_format', 'site_banner', 'site_banner_format', 'site_logotype_width', 'site_logotype_height', 'site_banner_width', 'site_banner_height','site_banner_color'));
			$smarty->assign("logo_setup", $logo_setup);
			break;
		case "10":
			$data =	GetSiteSettings(array('use_pilot_module_webmessenger'));
			break;
	}
	$form['action'] = $file_name;

	if (!isset($site_logo_name)){$site_logo_name = str_replace('http://', '', $config['server']);}

	if (isset($data)) {
		$smarty->assign('data', $data);
	}
	
	$smarty->assign('form', $form);
	$smarty->assign('header', $lang['settings']);
	$smarty->assign('button', $lang['button']);
	$smarty->assign('font', $lang['fonts_name']);
	$smarty->assign('site_logo_name', $site_logo_name);
	
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_settings_form.tpl');
	exit;
}


function SaveSettings($par)
{
	global $smarty, $dbconn, $config, $config_admin, $lang, $IMG_TYPE_ARRAY, $FLASH_TYPE_ARRAY, $_FILES, $_SESSION;
	
	$file_name = "admin_settings.php";
	
	if ($par == "email")
	{
		$email = $_POST["email"];
		if(!($email) || EmailFilter($email)){
			ListSettings($lang["err"]["email_bad"]); return;
		}
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$email."' where name='site_email'");
	}
	elseif ($par == "aname")
	{
		$login = $_POST["login"];
		$fname = $_POST["fname"];
		$sname = $_POST["sname"];

		//// check not valid login
		if($err = LoginFilter($login)){
			ListSettings($err); return;
		}
		$rs = $dbconn->Execute("Update ".USERS_TABLE." set login='".$login."', fname='".$fname."', sname='".$sname."' where root_user='1'");
	}
	elseif ($par == "pass")
	{
		$repass = $_POST["repass"];
		$pass = $_POST["pass"];
		$oldpass = $_POST["oldpass"];

		$rs = $dbconn->Execute("Select login from ".USERS_TABLE." where root_user='1'");
		$login = $rs->fields[0];
		//// check not valid pass
		if($repass != $pass){
			ListSettings($lang["err"]["invalid_passw"]); return;
		}
		//// check not valid pass
		if($login == $pass){
			ListSettings($lang["err"]["pass_eq_log"]); return;
		}
		//// check not valid pass
		if($err = PasswFilter($pass)){
			ListSettings($err); return;
		}
		//// check not valid oldpass
		$rs = $dbconn->Execute("Select count(*) from ".USERS_TABLE." where password='".md5($oldpass)."' and root_user='1'");
		if($rs->fields[0]!=1){
			ListSettings($lang["err"]["invalid_passw"]); return;
		}
		$rs = $dbconn->Execute("Update ".USERS_TABLE." set  password='".md5($pass)."' where root_user='1'");
	}
	elseif ($par == "lang")
	{
		$lang_name = $_POST["lang_name"];
		$lang_code = $_POST["lang_code"];
		$lang_charset = $_POST["lang_charset"];
		for ($i=0;$i<count($lang_name);$i++){
			$dbconn->Execute("Update ".LANGUAGE_TABLE."
				SET code='".$lang_code[$i+1]."', charset='".$lang_charset[$i+1]."', name='".$lang_name[$i+1]."'
				WHERE id='".($i+1)."'");
		}

		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$_POST["def_l"]."' where name='default_lang'");

		$visible = $_POST["visible"];
		$dbconn->Execute("Update ".LANGUAGE_TABLE." set visible='0' ");

		if(is_array($visible) && count($visible)>0){
			foreach($visible as $k=>$v){
				$dbconn->Execute("Update ".LANGUAGE_TABLE." set visible='1' where id='".$v."'");
			}
		}
		header("location: ".$config["server"].$config["site_root"]."/admin/".$file_name."?section=".$_POST["section"]);
	}
	elseif ($par == "lang_add")
	{
		if(!$_POST["name"] || !$_POST["code"] || !$_POST["charset"]){
			ListSettings($lang["err"]["empty_language"]); return;
		}
		$rs = $dbconn->Execute("Select id from ".LANGUAGE_TABLE." where name='".$_POST["name"]."'");
		if ($rs->fields[0]){
			ListSettings($lang["err"]["exists_language"]); return;
		}

		$rs = $dbconn->Execute("Select lang_file, name from ".LANGUAGE_TABLE." where id=".$config["default_lang"]);
		$default_lang_file = $rs->fields[0];
		$default_lang_name = $rs->fields[1];

		$lang_from_file = DelLastSlash($config["site_path"])."/".TrimSlash($config["path_lang"])."/".TrimSlash($default_lang_file);
		$lang_to_file = DelLastSlash($config["site_path"])."/".TrimSlash($config["path_lang"])."/".TrimSlash($_POST["name"].".lang");

		$mail_lang_from_file = DelLastSlash($config["site_path"])."/".TrimSlash($config["path_lang"])."/mail/".TrimSlash($default_lang_file);
		$mail_lang_to_file = DelLastSlash($config["site_path"])."/".TrimSlash($config["path_lang"])."/mail/".TrimSlash($_POST["name"].".lang");

		if(file_exists($lang_from_file) && is_readable($lang_from_file)){
			if (!copy($lang_from_file, $lang_to_file)) {
				ListSettings(); return;
			}
			@chmod($lang_to_file, 0777);
		}
		if(file_exists($mail_lang_from_file) && is_readable($mail_lang_from_file)){
			if (!copy($mail_lang_from_file, $mail_lang_to_file)) {
				ListSettings(); return;
			}
			@chmod($mail_lang_to_file, 0777);
		}
		$dbconn->Execute("insert into ".LANGUAGE_TABLE."  (code, charset, lang_file, name, visible) values ('".$_POST["code"]."', '".$_POST["charset"]."', '".$_POST["name"].".lang', '".$_POST["name"]."', '0')");
		$lang_id = $dbconn->Insert_ID();
		$dbconn->Execute("alter table ".REFERENCE_LANG_TABLE." add lang_".$lang_id." tinyblob");
		$dbconn->Execute("update ".REFERENCE_LANG_TABLE." set lang_".$lang_id."=lang_".$config["default_lang"]);
	}
	elseif ($par == "upload")
	{
		$err = "";
		
		$icon_size		= $_POST["icon_size"];
		$icon_width		= $_POST["icon_width"];
		$icon_height	= $_POST["icon_height"];

		$photo_size		= $_POST["photo_size"];
		$photo_width	= $_POST["photo_width"];
		$photo_height	= $_POST["photo_height"];

		$audio_size		= $_POST["audio_size"];

		$video_size		= $_POST["video_size"];

		$newsletter_size	= $_POST["newsletter_size"];
		$newsletter_width	= $_POST["newsletter_width"];
		$newsletter_height	= $_POST["newsletter_height"];

		$thumb_max_width		= intval($_POST["thumb_width"]);
		$thumb_max_height		= intval($_POST["thumb_height"]);

		$big_thumb_max_width	= intval($_POST["big_thumb_max_width"]);
		$big_thumb_max_height	= intval($_POST["big_thumb_max_height"]);
		
		$use_pilot_module_club = GetSiteSettings("use_pilot_module_club");

		if ($use_pilot_module_club){
			$club_photo_size	= $_POST["club_photo_size"];
			$club_photo_width	= $_POST["club_photo_width"];
			$club_photo_height	= $_POST["club_photo_height"];
		}

		if(
		!($icon_size) || !($icon_width) || !($icon_height) || !($photo_size) || !($photo_width) || !($photo_height) || ($use_pilot_module_club && (!($club_photo_size) || !($club_photo_width) || !($club_photo_height))) || !($audio_size) ||
		!($video_size) || !($newsletter_size) || !($newsletter_width) ||
		!($newsletter_height) ||
		($thumb_max_width < 1) || ($thumb_max_height < 1) || ($big_thumb_max_height < 1) || ($big_thumb_max_width < 1)
		){
			$err = $lang["err"]["invalid_fields"];
			if (!$icon_size) {
				if ($err) $err .= "<br>";
				$err .= $lang["settings"]["icon"]." - ".$lang["settings"]["upload_size"];
			}
			if (!$icon_width) {
				if ($err) $err .= "<br>";
				$err .= $lang["settings"]["icon"]." - ".$lang["settings"]["upload_width"];
			}
			if (!$icon_height) {
				if ($err) $err .= "<br>";
				$err .= $lang["settings"]["icon"]." - ".$lang["settings"]["upload_height"];
			}
			if (!$photo_size) {
				if ($err) $err .= "<br>";
				$err .= $lang["settings"]["photo"]." - ".$lang["settings"]["upload_size"];
			}
			if (!$photo_width) {
				if($err) $err .= "<br>";
				$err .= $lang["settings"]["photo"]." - ".$lang["settings"]["upload_width"];
			}
			if (!$photo_height) {
				if($err) $err .= "<br>";
				$err .= $lang["settings"]["photo"]." - ".$lang["settings"]["upload_height"];
			}
			if (!$audio_size) {
				if ($err) $err .= "<br>";
				$err .= $lang["settings"]["audio"]." - ".$lang["settings"]["upload_size"];
			}
			if (!$video_size) {
				if ($err) $err .= "<br>";
				$err .= $lang["settings"]["video"]." - ".$lang["settings"]["upload_size"];
			}
			if (!$newsletter_size) {
				if ($err) $err .= "<br>";
				$err .= $lang["settings"]["newsletter"]." - ".$lang["settings"]["upload_size"];
			}
			if (!$newsletter_width) {
				if ($err) $err .= "<br>";
				$err .= $lang["settings"]["newsletter"]." - ".$lang["settings"]["upload_width"];
			}
			if (!$newsletter_height) {
				if ($err) $err .= "<br>";
				$err .= $lang["settings"]["newsletter"]." - ".$lang["settings"]["upload_height"];
			}
			if (!$thumb_max_width) {
				if ($err) $err .= "<br>";
				$err .= $lang["settings"]["thumb"]." - ".$lang["settings"]["upload_width"];
			}
			if (!$thumb_max_height) {
				if ($err) $err .= "<br>";
				$err .= $lang["settings"]["thumb"]." - ".$lang["settings"]["upload_height"];
			}
			if (!$big_thumb_max_height) {
				if ($err) $err .= "<br>";
				$err .= $lang["settings"]["big_icon"]." - ".$lang["settings"]["upload_height"];
			}
			if (!$big_thumb_max_width) {
				if ($err) $err .= "<br>";
				$err .= $lang["settings"]["big_icon"]." - ".$lang["settings"]["upload_width"];
			}
			if ($use_pilot_module_club){
				if (!$club_photo_size) {
					if ($err) $err .= "<br>";
					$err .= $lang["settings"]["club"]." - ".$lang["settings"]["upload_size"];
				}
				if (!$club_photo_width) {
					if ($err) $err .= "<br>";
					$err .= $lang["settings"]["club"]." - ".$lang["settings"]["upload_width"];
				}
				if (!$club_photo_height) {
					if ($err) $err .= "<br>";
					$err .= $lang["settings"]["club"]." - ".$lang["settings"]["upload_height"];
				}
			}
			ListSettings($err);
			return;
		}

		$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value = ? WHERE name = 'video_max_size'", array((string)$video_size));
		$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value = ? WHERE name = 'audio_max_size'", array((string)$audio_size));

		$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value = ? WHERE name = 'photo_max_width'", array((string)$photo_width));
		$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value = ? WHERE name = 'photo_max_height'", array((string)$photo_height));
		$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value = ? WHERE name = 'photo_max_size'", array((string)$photo_size));
		
		$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value = ? WHERE name = 'icon_max_width'", array((string)$icon_width));
		$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value = ? WHERE name = 'icon_max_height'", array((string)$icon_height));
		$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value = ? WHERE name = 'icon_max_size'", array((string)$icon_size));

		$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value = ? WHERE name = 'subscrimage_max_width'", array((string)$newsletter_width));
		$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value = ? WHERE name = 'subscrimage_max_height'", array((string)$newsletter_height));
		$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value = ? WHERE name = 'subscrimage_max_size'", array((string)$newsletter_size));
		
		$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value = ? WHERE name = 'thumb_max_width'", array((string)$thumb_max_width));
		$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value = ? WHERE name = 'thumb_max_height'", array((string)$thumb_max_height));
		
		$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value = ? WHERE name = 'big_thumb_max_width'", array((string)$big_thumb_max_width));
		$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value = ? WHERE name = 'big_thumb_max_height'", array((string)$big_thumb_max_height));
		
		if ($use_pilot_module_club) {
			$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value = ? WHERE name = 'club_photo_max_width'", array((string)$club_photo_width));
			$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value = ? WHERE name = 'club_photo_max_height'", array((string)$club_photo_height));
			$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value = ? WHERE name = 'club_photo_max_size'", array((string)$club_photo_size));
		}
		
		$site_logo_type = intval($_POST["site_logo_type"]);
		if ($site_logo_type == 1) {
			$site_logo_name = isset($_POST["site_logo_name"]) ? $_POST["site_logo_name"] : $config["server"];
			if ( extension_loaded("gd") && in_array("imagettfbbox", get_extension_funcs("gd")) ) {
				$font_face = $_POST["font-face"].".ttf";
				$font_size = $_POST["font-size"];
				if ($font_size) {
					CreateSmallLogo($site_logo_name, $font_size, $font_face);
					$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$site_logo_name."' where name='site_logo_name'");
					$dbconn->Execute("Update ".SETTINGS_TABLE." set value='text' where name='site_logo_type'");
				}
			}
		} else {
			$dbconn->Execute("Update ".SETTINGS_TABLE." set value='image' where name='site_logo_type'");

			$site_logo = $_FILES['site_logo_picture'];
			if (is_uploaded_file($site_logo["tmp_name"])) {
				$images_obj = new Images($dbconn);
				$err_site_logo = $images_obj->UploadSiteLogo($site_logo);
			}
			if (isset($err_site_logo)) {
				if($err) $err .= "<br>";
				$err .= $err_site_logo;
			}
			if ($err) {
				ListSettings($err);
				return;
			}
		}

		////// save icons and pics
		$images_obj = new Images($dbconn);
		
		$icon_male = $_FILES["upload_icon_male"];
		if (is_uploaded_file($icon_male["tmp_name"])) {
			$err_upload = $images_obj->UploadDefaultImages($icon_male, "icon", "male");
		} else {
			$err_upload = '';
		}
		if ($err_upload != '') {
			if($err) $err .= "<br>";
			$err .=$err_upload;
		}

		$icon_female = $_FILES["upload_icon_female"];
		if (is_uploaded_file($icon_female["tmp_name"])) {
			$err_upload = $images_obj->UploadDefaultImages($icon_female, "icon", "female");
		} else {
			$err_upload = '';
		}
		if ($err_upload != '') {
			if($err) $err .= "<br>";
			$err .=$err_upload;
		}

		$photo = $_FILES["upload_file"];
		if (is_uploaded_file($photo["tmp_name"])) {
			$err_upload = $images_obj->UploadDefaultImages($photo, "f");
		} else {
			$err_upload = '';
		}
		if ($err_upload != '') {
			if($err) $err .= "<br>";
			$err .=$err_upload;
		}

		$icon_adult = $_FILES["upload_icon_adult"];
		if (is_uploaded_file($icon_adult["tmp_name"])) {
			$err_upload = $images_obj->UploadDefaultImages($icon_adult, "icon", "adult");
		} else {
			$err_upload = '';
		}
		if ($err_upload != '') {
			if($err) $err .= "<br>";
			$err .= $err_upload;
		}

		if ($err != "") {
			ListSettings($err);
			return;
		}
	}
	elseif ($par == "db")
	{
		$configfilename = $config["site_path"]."/include/config.xml";
		if (file_exists($configfilename) && is_writeable($configfilename)) {
			$db_host	= $_POST["dbhost"];
			$db_uname	= $_POST["dbuname"];
			$db_name	= $_POST["dbname"];
			$db_prefix	= $_POST["prefix"];
			if (!$db_host || !$db_uname || !$db_name) {
				$err = $lang["err"]["invalid_fields"];
				if (!$db_host) {
					$err .= "<br>".$lang["settings"]["dbhost"];
				}
				if (!$db_uname) {
					$err .= "<br>".$lang["settings"]["dbuname"];
				}
				if (!$db_name) {
					$err .= "<br>".$lang["settings"]["dbname"];
				}
				ListSettings($err);
				return;
			}

			$xml_parser = new SimpleXmlParser( $configfilename );
			$xml_root = $xml_parser->getRoot();
			for ( $i = 0; $i < $xml_root->childrenCount; $i++ ) {
				switch($xml_root->children[$i]->tag){
					case "dbhost":		$xml_root->children[$i]->value = $db_host;	break;
					case "dbuname":		$xml_root->children[$i]->value = $db_uname;	break;
					case "dbname":		$xml_root->children[$i]->value = $db_name;	break;
					case "table_prefix":	$xml_root->children[$i]->value = $db_prefix;	break;
					default: continue;
				}
			}
			$obj_saver = new Object2Xml();
			$obj_saver->Save($xml_root, $configfilename);
			unset($xml_parser, $xml_root);
		} else {
			$err = $lang["err"]["not_writeable_file"]."<br>".$configfilename;
			ListSettings($err);
			return;
		}
	}
	elseif ($par == "dbpass")
	{
		$configfilename = $config["site_path"]."/include/config.xml";
		if (file_exists($configfilename) && is_writeable($configfilename)) {
			$db_pass  = $_POST["dbpass"];
			$db_repass  = $_POST["dbrepass"];
			$db_oldpass  = $_POST["dboldpass"];

			if ($db_repass != $db_pass) {
				ListSettings($lang["err"]["invalid_passw"]); return;
			}

			$xml_parser = new SimpleXmlParser( $configfilename );
			$xml_root = $xml_parser->getRoot();
			for ( $i = 0; $i < $xml_root->childrenCount; $i++ ) {
				if ($xml_root->children[$i]->tag == "dbpass") {
					if ($xml_root->children[$i]->value != $db_oldpass) {
						ListSettings($lang["err"]["invalid_passw"]);
						return;
					}
					$xml_root->children[$i]->value = $db_pass;
				} else {
					continue;
				}
			}
			$obj_saver = new Object2Xml();
			$obj_saver->Save($xml_root, $configfilename);
			unset($xml_parser, $xml_root);
		} else {
			$err = $lang["err"]["not_writeable_file"]."<br>".$configfilename;
			ListSettings($err);
			return;
		}
	}
	elseif ($par == "other")
	{
		$path_to_ffmpeg					= isset($_POST['path_to_ffmpeg']) ? $_POST['path_to_ffmpeg'] : '';
		$flv_output_dimension			= isset($_POST['flv_output_dimension']) ? $_POST['flv_output_dimension'] : '';
		$flv_output_preset				= isset($_POST['flv_output_preset']) ? $_POST['flv_output_preset'] : '';
		$flv_output_profile				= isset($_POST['flv_output_profile']) ? $_POST['flv_output_profile'] : '';
		$flv_output_fps					= isset($_POST['flv_output_fps']) ? $_POST['flv_output_fps'] : '';
		$flv_output_gop					= isset($_POST['flv_output_gop']) ? $_POST['flv_output_gop'] : '';
		$flv_output_video_bit_rate		= isset($_POST['flv_output_video_bit_rate']) ? $_POST['flv_output_video_bit_rate'] : '';
		$flv_output_audio_sampling_rate = isset($_POST['flv_output_audio_sampling_rate']) ? $_POST['flv_output_audio_sampling_rate'] : '';
		$flv_output_audio_bit_rate		= isset($_POST['flv_output_audio_bit_rate']) ? $_POST['flv_output_audio_bit_rate'] : '';
		$flv_output_foto_dimension		= isset($_POST['flv_output_foto_dimension']) ? $_POST['flv_output_foto_dimension'] : '';
		$flv_grab_photo_at_second		= isset($_POST['flv_grab_photo_at_second']) ? $_POST['flv_grab_photo_at_second'] : '1';
		$flv_player_width				= isset($_POST['flv_player_width']) ? $_POST['flv_player_width'] : '';
		$flv_player_height				= isset($_POST['flv_player_height']) ? $_POST['flv_player_height'] : '';
		
		$profile_limit					= isset($_POST['profile_limit']) ? intval($_POST['profile_limit']) : 0;
		$mail_attaches_limit			= isset($_POST['mail_attaches_limit']) ? intval($_POST['mail_attaches_limit']) : 0;
		$min_age_limit					= isset($_POST['min_age_limit']) ? intval($_POST['min_age_limit']) : 0;
		$max_age_limit					= isset($_POST['max_age_limit']) ? intval($_POST['max_age_limit']) : 0;

		if ($max_age_limit <= $min_age_limit) {
			$err = $lang["err"]["max_min_err"];
			ListSettings($err);
			return;
		}
		
		$featured_users_slider_speed	= isset($_POST["featured_users_slider_speed"]) ? intval($_POST["featured_users_slider_speed"]) : 0;
		//$show_online_users_str		= isset($_POST["show_online_users_str"]) ? intval($_POST["show_online_users_str"]) : 0;
		$show_users_name_str			= isset($_POST["show_users_name_str"]) ? intval($_POST["show_users_name_str"]) : 0;
		$show_users_sname_str			= isset($_POST["show_users_sname_str"]) ? intval($_POST["show_users_sname_str"]) : 0;
		$show_users_zipcode_str			= isset($_POST["show_users_zipcode_str"]) ? intval($_POST["show_users_zipcode_str"]) : 0;
		$show_users_birthdate_str		= isset($_POST["show_users_birthdate_str"]) ? intval($_POST["show_users_birthdate_str"]) : 0;
		$show_users_connection_str		= isset($_POST["show_users_connection_str"]) ? intval($_POST["show_users_connection_str"]) : 0;
		$show_users_comments			= isset($_POST["show_users_comments"]) ? intval($_POST["show_users_comments"]) : 0;
		$show_users_group_str			= isset($_POST["show_users_group_str"]) ? intval($_POST["show_users_group_str"]) : 0;
		$zip_count						= isset($_POST["zip_count"]) ? intval($_POST["zip_count"]) : 0;
		$zip_letters					= isset($_POST["zip_letters"]) ? intval($_POST["zip_letters"]) : 0;
		$use_ffmpeg						= isset($_POST["use_ffmpeg"]) ? intval($_POST["use_ffmpeg"]) : 0;
		$use_image_resize				= isset($_POST["use_image_resize"]) ? intval($_POST["use_image_resize"]) : 0;
		$use_icon_approve				= isset($_POST["use_icon_approve"]) ? intval($_POST["use_icon_approve"]) : 0;
		$use_photo_approve				= isset($_POST["use_photo_approve"]) ? intval($_POST["use_photo_approve"]) : 0;
		$use_audio_approve				= isset($_POST["use_audio_approve"]) ? intval($_POST["use_audio_approve"]) : 0;
		$use_video_approve				= isset($_POST["use_video_approve"]) ? intval($_POST["use_video_approve"]) : 0;
		$use_gallary_approve			= isset($_POST["use_gallary_approve"]) ? intval($_POST["use_gallary_approve"]) : 0;
		$use_registration_confirmation	= isset($_POST["use_registration_confirmation"]) ? intval($_POST["use_registration_confirmation"]) : 0;
		$use_registration_approve		= isset($_POST["use_registration_approve"]) ? intval($_POST["use_registration_approve"]) : 0;
		$use_shoutbox_feature			= isset($_POST["use_shoutbox_feature"]) ? intval($_POST["use_shoutbox_feature"]) : 0;
		$use_photo_logo					= isset($_POST["use_photo_logo"]) ? intval($_POST["use_photo_logo"]) : 0;
		$use_horoscope_feature			= isset($_POST["use_horoscope_feature"]) ? intval($_POST["use_horoscope_feature"]) : 0;
		$use_freetrial_membership		= isset($_POST["use_freetrial_membership"]) ? intval($_POST["use_freetrial_membership"]) : 0;
		$use_success_stories			= isset($_POST["use_success_stories"]) ? intval($_POST["use_success_stories"]) : 0;
		$use_kiss_types					= isset($_POST["use_kiss_types"]) ? intval($_POST["use_kiss_types"]) : 0;
		$use_embedded_audio				= isset($_POST["use_embedded_audio"]) ? intval($_POST["use_embedded_audio"]) : 0;
		$use_hide_profile_feature		= isset($_POST["use_hide_profile_feature"]) ? intval($_POST["use_hide_profile_feature"]) : 0;
		$use_friend_types				= isset($_POST["use_friend_types"]) ? intval($_POST["use_friend_types"]) : 0;
		$use_gender_membership			= isset($_POST["use_gender_membership"]) ? intval($_POST["use_gender_membership"]) : 0;
		$use_refer_friend_feature		= isset($_POST["use_refer_friend_feature"]) ? intval($_POST["use_refer_friend_feature"]) : 0;
		$refer_friend_price				= round(floatval($_POST["refer_friend_price"]), 2);
		
		if ($refer_friend_price == 0) $refer_friend_price = 0.01;

		$use_lift_up_in_search_service	= isset($_POST["use_lift_up_in_search_service"]) ? intval($_POST["use_lift_up_in_search_service"]) : 0;
		$user_banners_feature			= isset($_POST["user_banners_feature"]) ? intval($_POST["user_banners_feature"]) : 0;
		$lang_ident_feature				= isset($_POST["lang_ident_feature"]) ? intval($_POST["lang_ident_feature"]) : 0;
		$voipcall_feature				= isset($_POST["voipcall_feature"]) ? intval($_POST["voipcall_feature"]) : 0;
		
		// new settings for TLDF
		$use_credits_for_membership_payment = isset($_POST['use_credits_for_membership_payment']) ? 1 : 0;
		$must_approve_payment_before_verify = isset($_POST['must_approve_payment_before_verify']) ? 1 : 0;
		
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$profile_limit."' where name='guest_profile_limit'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$mail_attaches_limit."' where name='mail_attaches_limit'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$min_age_limit."' where name='min_age_limit'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$max_age_limit."' where name='max_age_limit'");
		// $dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$show_online_users_str."' where name='show_online_users_str'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$show_users_name_str."' where name='show_users_name_str'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$show_users_sname_str."' where name='show_users_sname_str'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$show_users_zipcode_str."' where name='show_users_zipcode_str'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$show_users_birthdate_str."' where name='show_users_birthdate_str'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$show_users_connection_str."' where name='show_users_connection_str'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$show_users_comments."' where name='show_users_comments'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$show_users_group_str."' where name='show_users_group_str'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$zip_count."' where name='zip_count'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$zip_letters."' where name='zip_letters'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value = ? where name = 'use_ffmpeg'", array($use_ffmpeg));
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value = ? where name = 'path_to_ffmpeg'", array($path_to_ffmpeg));
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value = ? where name = 'flv_output_dimension'", array($flv_output_dimension));
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value = ? where name = 'flv_output_preset'", array($flv_output_preset));
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value = ? where name = 'flv_output_profile'", array($flv_output_profile));
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value = ? where name = 'flv_output_fps'", array($flv_output_fps));
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value = ? where name = 'flv_output_gop'", array($flv_output_gop));
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value = ? where name = 'flv_output_video_bit_rate'", array($flv_output_video_bit_rate));
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value = ? where name = 'flv_output_audio_sampling_rate'", array($flv_output_audio_sampling_rate));
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value = ? where name = 'flv_output_audio_bit_rate'", array($flv_output_audio_bit_rate));
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value = ? where name = 'flv_output_foto_dimension'", array($flv_output_foto_dimension));
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value = ? where name = 'flv_grab_photo_at_second'", array($flv_grab_photo_at_second));
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value = ? where name = 'flv_player_width'", array($flv_player_width));
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value = ? where name = 'flv_player_height'", array($flv_player_height));
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$use_image_resize."' where name='use_image_resize'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$use_icon_approve."' where name='use_icon_approve'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$use_photo_approve."' where name='use_photo_approve'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$use_audio_approve."' where name='use_audio_approve'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$use_video_approve."' where name='use_video_approve'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$use_gallary_approve."' where name='use_gallary_approve'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$use_registration_confirmation."' where name='use_registration_confirmation'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$use_registration_approve."' where name='use_registration_approve'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$use_shoutbox_feature."' where name='use_shoutbox_feature'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$use_photo_logo."' where name='use_photo_logo'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$use_horoscope_feature."' where name='use_horoscope_feature'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$use_freetrial_membership."' where name='use_freetrial_membership'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$use_success_stories."' where name='use_success_stories'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$use_kiss_types."' where name='use_kiss_types'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$use_embedded_audio."' where name='use_embedded_audio'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$use_hide_profile_feature."' where name='use_hide_profile_feature'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$use_friend_types."' where name='use_friend_types'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$use_gender_membership."' where name='use_gender_membership'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$use_refer_friend_feature."' where name='use_refer_friend_feature'");
		$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value='".$use_lift_up_in_search_service."' WHERE name='use_lift_up_in_search_service'");
		$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value='".$user_banners_feature."' WHERE name='user_banners_feature'");
		
		if ($use_refer_friend_feature) {
			$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$refer_friend_price."' where name='refer_friend_price'");
		}
		
		$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value='".$lang_ident_feature."' WHERE name='lang_ident_feature'");
		$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value='".$voipcall_feature."' WHERE name='voipcall_feature'");
		
		// new settings
		$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value='".$use_credits_for_membership_payment."' WHERE name='use_credits_for_membership_payment'");
		$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value='".$must_approve_payment_before_verify."' WHERE name='must_approve_payment_before_verify'");
		$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value='".$featured_users_slider_speed."' WHERE name='featured_users_slider_speed'");
		
		switch ($_POST['map_radio']) {
			case 'google':
				$dbconn->Execute("Update ".SETTINGS_TABLE." set value='google' where name='map_type'");
			break;
			case 'yahoo':
				$dbconn->Execute("Update ".SETTINGS_TABLE." set value='yahoo' where name='map_type'");
			break;
		}

		$google_app_id = isset($_POST['google_app_id']) ? $_POST['google_app_id'] : "";
		$map_app_id = isset($_POST['map_app_id']) ? $_POST['map_app_id'] : "";

		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".addslashes($google_app_id)."' where name='google_app_id'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".addslashes($map_app_id)."' where name='map_app_id'");
		
		if (isset($_POST["use_freetrial_membership"]) && $_POST["use_freetrial_membership"]) {
			$rs = $dbconn->Execute("SELECT id FROM ".GROUPS_TABLE." where type='t'");
			//$id_group = $rs->fields[0];
			//VP passing list of group id
			$group_ids = '';
			while (!$rs->EOF) {
				if ($group_ids != '') {
					$group_ids .= ',';
				}
				$group_ids .= $rs->fields[0];
				$rs->MoveNext();
			}
			$dbconn->Execute("Update ".GROUP_PERIOD_TABLE." set amount='".intval($_POST["freetrial_amount"])."', period='".$_POST["freetrial_period"]."' where id_group IN (".$group_ids.")");
		}
		
		$expr = array('%', 'Y', 'y', 'd', 'e', 'm', 'c');
		foreach ($expr as $key => $exp){
			$_POST["date_format"] = str_replace($exp, "%".$exp, $_POST["date_format"]);
		}
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$_POST["date_format"]."' where name='date_format'");
	}
	elseif ($par == "template")
	{
		$template = intval($_POST["template"]);
		$strSQL = "Select path from ".TEMPLATE_TABLE." where id='".$template."' ";
		$rs = $dbconn->Execute($strSQL);
		$template_path = $rs->fields[0];
		if(!$template || !$template_path){
			ListSettings();
			return;
		}
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$template_path."' where name='index_theme_path'");
	}
	elseif ($par == "theme")
	{
		$theme = intval($_POST["theme"]);
		$theme_tpl = intval($_POST["theme_tpl"]);
		$strSQL = "Select path_css, path_images from ".COLOR_THEME_TABLE." where id='".$theme."' ";
		$rs = $dbconn->Execute($strSQL);
		$theme_css_path = $rs->fields[0];
		$theme_images_path = $rs->fields[1];
		if(!$theme || !$theme_css_path || !$theme_images_path){
			ListSettings();
			return;
		}
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$theme_css_path."' where name='index_theme_css_path'");
		$dbconn->Execute("Update ".SETTINGS_TABLE." set value='".$theme_images_path."' where name='index_theme_images_path'");
	}
	elseif ($par == "chat")
	{
		if ($_POST["selected_chat"] == "flashchat") {
			$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value = '1' WHERE name = 'use_pilot_module_flashchat'");
			UpdateModuleMenu("../include/admin_menu_flashchat.xml");
		} elseif ($_POST["selected_chat"] == "webchat") {
			$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value = '0' WHERE name = 'use_pilot_module_flashchat'");
			UpdateModuleMenu("../include/admin_menu_webchat.xml");
		}
	}
	elseif ($par == 'messenger')
	{
		if ($_POST["selected_messenger"] == "flashim") {
			$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value = '0' WHERE name = 'use_pilot_module_webmessenger'");
			$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value = '1' WHERE name = 'use_pilot_module_im'");
			UpdateModuleMenu("", 1, "messenger");
		} elseif ($_POST["selected_messenger"] == "webim") {
			$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value = '1' WHERE name = 'use_pilot_module_webmessenger'");
			$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value = '0' WHERE name = 'use_pilot_module_im'");
			UpdateModuleMenu("../include/admin_menu_webmessenger.xml");
		}
	}
	elseif ($par == "site_images")
	{
		$site_logo = $_FILES['site_logotype'];
		
		if (is_uploaded_file($site_logo["tmp_name"]))
		{
			$temp_file = $site_logo['tmp_name'];
			$file_name = $site_logo["name"];
			$info= explode('.', $site_logo["name"]);
			$ext = $info[count($info)-1];
			
			$type = '';
			if (in_array($site_logo['type'], $IMG_TYPE_ARRAY)) {
				$type = 'image';
			}
			if (in_array($site_logo['type'], $FLASH_TYPE_ARRAY)) {
				$type = 'flash';
			}
			if ($type == '') {
				$err = $lang['err']['invalid_file_type'] . implode(', ', $IMG_TYPE_ARRAY) . ', ' . implode(', ', $FLASH_TYPE_ARRAY);
				$err = str_replace('#TYPE#', $site_logo['type'], $err);
			} else {
				$res = move_uploaded_file($temp_file, $config['site_path'].'/uploades/banners/logotype.'.$ext);
				if (!$res) {
					$err = str_replace('[path]', $config['site_path'].'/uploades/banners/', $lang['err']['cant_copy_file']);
				} else {
					$rs = $dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value='/uploades/banners/logotype.".$ext."' WHERE name='site_top_logotype'");
					$rs = $dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value='".$type."' WHERE name='site_logotype_format'");
				}
			}
			if ($err) {
				ListSettings($err);
				return;
			}
		}
		
		$site_banner = $_FILES['site_banner'];
		
		if (is_uploaded_file($site_banner["tmp_name"]))
		{
			$temp_file = $site_banner['tmp_name'];
			$file_name = $site_banner["name"];
			$info = explode('.', $site_banner["name"]);
			$ext = $info[count($info)-1];
			
			$type = '';
			if (in_array($site_banner['type'], $IMG_TYPE_ARRAY)) {
				$type = 'image';
			}
			if (in_array($site_banner['type'], $FLASH_TYPE_ARRAY)) {
				$type = 'flash';
			}
			if ($type == '') {
				$err = $lang['err']['invalid_file_type'] . implode(', ', $IMG_TYPE_ARRAY) . ', ' . implode(', ', $FLASH_TYPE_ARRAY);
				$err = str_replace('#TYPE#', $site_banner['type'], $err);
			} else {
				$res = move_uploaded_file($temp_file, $config['site_path'].'/uploades/banners/index_banner.'.$ext);
				if (!$res) {
					$err = str_replace('[path]', $config['site_path'].'/uploades/banners/', $lang["err"]["cant_copy_file"]);
				} else {
					$rs = $dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value='/uploades/banners/index_banner.".$ext."' WHERE name='site_banner'");
					$rs = $dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value='".$type."' WHERE name='site_banner_format'");
				}
			}
			if ($err) {
				ListSettings($err);
				return;
			}
		}

		if (isset($_REQUEST['restore_logotype']) && intval($_REQUEST['restore_logotype']) != 0) {
			$rs = $dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value='' WHERE name='site_top_logotype'");
			$logotype_width = 0;
			$logotype_height = 0;
		} else {
			$logotype_width=intval($_POST['site_logotype_width']);
			$logotype_height=intval($_POST['site_logotype_height']);
		}
		
		if (isset($_REQUEST['restore_banner']) && intval($_REQUEST['restore_banner'])!=0){
			$rs = $dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value='' WHERE name='site_banner'");
			$banner_width=0;
			$banner_height=0;
		} else {
			$banner_width=intval($_POST['site_banner_width']);
			$banner_height=intval($_POST['site_banner_height']);
			$banner_color=addslashes($_POST['site_banner_color']);
		}
		
		$rs = $dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value='".$logotype_width."' WHERE name='site_logotype_width'");
		$rs = $dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value='".$logotype_height."' WHERE name='site_logotype_height'");
		$rs = $dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value='".$banner_width."' WHERE name='site_banner_width'");
		$rs = $dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value='".$banner_height."' WHERE name='site_banner_height'");
		$rs = $dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value='".$banner_color."' WHERE name='site_banner_color'");
	}
	elseif ($par == "color")
	{
		$dbconn->Execute("UPDATE ".SETTINGS_TABLE." SET value='".$_REQUEST["color_theme"]."' WHERE name='color_theme'");
	}
	
	ListSettings();
	return;
}


///////////////////// lang file editor
function LangForm($err="")
{
	global $smarty, $dbconn, $config, $config_admin, $lang, $file_name;

	$file_name = "admin_settings.php";

	AdminMainMenu($lang["settings"], "1");

	$sub = isset($_GET["mail"]) ? "/mail" : "";

	$lang_code = $_GET["l"];
	if(!$lang_code) $lang_code = $config["default_lang"];
	if ($err) {
		$form["err"] = $err;
	}
	$strSQL = "select lang_file, charset, code from ".LANGUAGE_TABLE." where id='".$lang_code."'";
	$rs = $dbconn->Execute($strSQL);
	$lang_file = $rs->fields[0];
	$data["charset"] = $rs->fields[1];
	$strSQL = "select lang_file, charset, code from ".LANGUAGE_TABLE." where id='".$config["default_lang"]."'";
	$rs = $dbconn->Execute($strSQL);
	$data["lang_code"] = $rs->fields[2];
	$file_path = DelLastSlash($config["site_path"])."/".TrimSlash($config["path_lang"]).$sub."/".TrimSlash($lang_file);
	if(file_exists($file_path) && is_readable($file_path)){
		$data["langfile"] = implode("", file($file_path));
	}else{
		$form["err"] = $lang["err"]["not_readable_file"];
	}
	$data["langfile"] = PhptoHtml($data["langfile"]);
	$form["action"] = $file_name;
	$form["hiddens"] = "<input type=hidden name=\"sel\" value=\"langsave\">";
	$form["hiddens"] .= "<input type=hidden name=l value=\"".$lang_code."\">";
	if (isset($_GET["mail"]))
	$form["hiddens"] .= "<input type=hidden name=mail value=\"\">";

	$smarty->assign("charset", $data["charset"]);
	$smarty->assign("data", $data);
	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["settings"]);
	$smarty->assign("button", $lang["button"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_settings_langform.tpl");
	exit;
}

function LangSave()
{
	global $smarty, $dbconn, $config, $config_admin, $lang;

	if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
	$file_name = "admin_settings.php";
	$langdata = $_POST["langfile"];
	$langdata = stripslashes($langdata);
	$langdata = ereg_replace("\n", "", $langdata);

	$err = '';
	$sub = isset($_POST["mail"]) ? "/mail" : "";

	$lang_code = $_POST["l"];
	if(!$lang_code) $lang_code = $config["default_lang"];

	$strSQL = "select lang_file from ".LANGUAGE_TABLE." where id='".$lang_code."'";
	$rs = $dbconn->Execute($strSQL);
	$lang_file = $rs->fields[0];
	$file_path = DelLastSlash($config["site_path"])."/".TrimSlash($config["path_lang"]).$sub."/".TrimSlash($lang_file);
	$file_backup_path = DelLastSlash($config["site_path"])."/".TrimSlash($config["path_lang"]).$sub."/temp_file.lang";
	$res = CheckUpLangFile(PhptoHtml($langdata),$sub);
	if(!$res[0]){
		$err = $lang["err"]["not_valid_sintax"];
	}else{
		$langdata = $res[0];
	}
	if($err!=''){
		$fp = fopen ($file_backup_path, "w");
		fputs($fp,$langdata);
		fclose($fp);
		$err .= "<a target=_blank href=\"".DelLastSlash($config["site_root"])."/".TrimSlash($config["path_lang"]).$sub."/temp_file.lang\">temp_file.lang</a>";
		if($res[1]){
			$err .= "<br> (Error in ".$res[1].")";
		}
	}else{
		if(file_exists($file_path) && is_writeable($file_path)){
			//// make a file backup
			copy($file_path, $file_backup_path);

			$fp = fopen ($file_path, "w");
			fputs($fp,$langdata);
			fclose($fp);
		}else{
			$err = $lang["err"]["not_writeable_file"];
		}
	}
	$_GET["l"] = $_POST["l"];
	if (isset($_POST["mail"]))
	$_GET["mail"] = "";

	LangForm($err);	return;
}

function CheckUpLangFile($text,$flag="")
{
	global $dbconn, $config, $lang;
	$postfix = ($flag == "") ? "" : "_mail";
	$reg_exp = "/^\s*<\?(php)?([\W\w]*)\?>\s*$/i";
	if(preg_match_all($reg_exp, $text, $text_arr)){
		$text = $text_arr[2][0];
		//$text = eregi_replace("\s*\/\/[^\n\r]*", "", $text);		//// delete comments
		$text = eregi_replace("\s*[^:]\/\/[^\n\r]*", "", $text);
		$text = eregi_replace("[\\]+\"", "%qt%", $text);		//// delete all slashed inv commas
		$text = eregi_replace("\n", "", $text);				//// delete all slashed inv commas
		$text = eregi_replace("\";", "\";\n", $text);			//// delete all slashed inv commas
		$lang_strings = explode("\$lang".$postfix, $text);
		for($i=1; $i<count($lang_strings);$i++){
			if(
			!( preg_match_all("/^\s*((\[\s*\"?\s*[\w\d]*\s*\"?\s*\])+)\s*=\s*\"[^\n\"]*\";\s*$/i",$lang_strings[$i], $out1, PREG_PATTERN_ORDER) ) ||
			!( preg_match_all("/^\s*\[([^=]*)\]\s*=\s*\"([^\n\"]*)\";\s*$/i", $lang_strings[$i], $out2, PREG_PATTERN_ORDER) )
			){
				$text = "";
				$err = $i." => ".$lang_strings[$i];
				return array($text, $err);
			}else{
				$lang_strings[$i]  = "\$lang".$postfix.$out1[1][0]."=\"".string_changes($out2[2][0])."\";\n";
				//	echo $lang_strings[$i]."<br>";
			}
		}
		$text = implode("", $lang_strings);
		$text = "<?php". $text."?>";
		$err = "";
		return array($text, $err);
	}else{
		$text = "";
		$err = "";
		return array($text, $err);
	}
}

function string_changes($str)
{
	$str = str_replace("%qt%", "\"", $str);
	return stripslashes($str);
}

function PhptoHtml($str)
{
	$search = array ("'&(quot|#34);'i", "'&(amp|#38);'i", "'&(lt|#60);'i", "'&(gt|#62);'i", "'&(nbsp|#160);'i", "'&(iexcl|#161);'i", "'&(cent|#162);'i", "'&(pound|#163);'i", "'&(copy|#169);'i");
	$replace = array ("\\\"",  "&",  "<", ">",  " ",  chr(161),  chr(162), chr(163), chr(169), "chr(\\1)");
	$text = preg_replace($search, $replace, $str);
	return $text;
}

function TemplateSave()
{
	global $smarty, $dbconn, $config, $config_admin, $lang, $templates;

	if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
	$file_name = "admin_settings.php";

	$template_name = $_POST["templ_name"];
	$template_path = $_POST["templ_path"];

	if(!$template_name){
		$err = $lang["err"]["invalid_template_name"];
		ListSettings($err); return;
	}
	if(!$template_path){
		$err = $lang["err"]["invalid_template_path_empty"];
		ListSettings($err); return;
	}
	$full_path = $config["site_path"].$template_path;
	$err = "";
	foreach($templates as $k=>$v){
		if(!file_exists($full_path.DIRECTORY_SEPARATOR.$v)){
			$err = $err.$v."; ";
		}
	}
	if($err != ""){
		$err = $lang["err"]["invalid_template_path"]."<br>".$err;
		ListSettings($err); return;
	}
	$strSQL = "select count(*) from ".TEMPLATE_TABLE."  where name='".$template_name."' or path='".$template_path."' ";
	$rs = $dbconn->Execute($strSQL);
	if($rs->fields[0]>0){
		$err = $lang["err"]["invalid_template_exists"];
		ListSettings($err); return;
	}
	$strSQL = "insert into ".TEMPLATE_TABLE."  (name, path) values ('".$template_name."', '".$template_path."') ";
	$rs = $dbconn->Execute($strSQL);
	ListSettings(); return;
}

function ThemeSave()
{
	global $smarty, $dbconn, $config, $config_admin, $lang, $themes;

	if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
	$file_name = "admin_settings.php";

	$theme_tpl = $_POST["theme_tpl"];
	$theme_name = $_POST["theme_name"];
	$theme_css_path = $_POST["theme_css_path"];
	$theme_images_path = $_POST["theme_images_path"];

	if(!$theme_name){
		$err = $lang["err"]["invalid_theme_name"];
		ListSettings($err); return;
	}
	if(!$theme_css_path || !$theme_images_path){
		$err = $lang["err"]["invalid_theme_path_empty"];
		ListSettings($err); return;
	}

	$strSQL = "select path from ".TEMPLATE_TABLE."  where id='".$theme_tpl."'";
	$rs = $dbconn->Execute($strSQL);

	$full_css_path = $config["site_path"].$rs->fields[0].$theme_css_path;
	$err = "";
	if(!file_exists($full_css_path)){
		$err = $err.$theme_css_path."; ";
	}
	$full_images_path = $config["site_path"].$rs->fields[0].$theme_images_path;
	if(!file_exists($full_images_path)){
		$err = $err.$theme_images_path."; ";
	}
	if($err != ""){
		$err = $lang["err"]["invalid_theme_path"]."<br>".$err;
		ListSettings($err); return;
	}
	$strSQL = "select count(*) from ".COLOR_THEME_TABLE."  where name='".$theme_name."' and id_tpl='".$theme_tpl."'";
	$rs = $dbconn->Execute($strSQL);
	if($rs->fields[0]>0){
		$err = $lang["err"]["invalid_theme_exists"];
		ListSettings($err); return;
	}
	$strSQL = "insert into ".COLOR_THEME_TABLE."  (id_tpl, name, path_css, path_images) values ('".$theme_tpl."', '".$theme_name."', '".$theme_css_path."', '".$theme_images_path."') ";
	$rs = $dbconn->Execute($strSQL);
	ListSettings(); return;
}

///////////////////// lang file editor
function BaseForm($err="")
{
	global $smarty, $dbconn, $config, $config_admin, $lang, $file_name;
	$file_name = isset($_SERVER["PHP_SELF"])?AfterLastSlash($_SERVER["PHP_SELF"]):"admin_settings.php";

	AdminMainMenu($lang["settings"], "1");
	if($err)	$form["err"] = $err;

	///// from dumper php

	define('PATH', $config["site_path"].'/backup/');
	define('URL',  $config["server"].$config["site_root"].'/backup/');
	define('TIME_LIMIT', 600);
	define('LIMIT', 1);
	define('DBHOST', $config["dbhost"]);
	define('DBNAMES', $config["dbname"]);
	define("SC", 0);

	$is_safe_mode = ini_get('safe_mode') == '1' ? 1 : 0;
	if (!$is_safe_mode) set_time_limit(TIME_LIMIT);

	$timer = array_sum(explode(' ', microtime()));
	ob_implicit_flush();
	$error = '';
	@mysql_connect($config["dbhost"], $config["dbuname"], $config["dbpass"]);

	if (!empty($_COOKIE['skd'])) {
		$dbuser = explode(":", base64_decode($_COOKIE['skd']));
		if (@mysql_connect(DBHOST, $dbuser[1], $dbuser[2])){
			$auth = 1;
		}
		else{
			$error = '#' . mysql_errno() . ': ' . mysql_error();
		}
	}
	if (!file_exists(PATH) && !$is_safe_mode) {
		mkdir(PATH, 0777) || die("Can not create backup folder");
		@chmod(PATH, 0777);
	}

	include "../include/class.dumper.php";
	$SK = new dumper($config, $lang);
	define('C_DEFAULT', 1);
	define('C_RESULT', 2);
	define('C_ERROR', 3);

	$smarty->assign("data", $data);
	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["settings"]);
	$smarty->assign("button", $lang["button"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_settings_baseform.tpl");

	$SK->backup();
	mysql_close();
	print "<SCRIPT>document.getElementById('timer').innerHTML = '" . round(array_sum(explode(' ', microtime())) - $timer, 4) . " sec.'</SCRIPT>";

	///// from dumper php

	exit;
}

function CreateSmallLogo($site_logo_name, $font_size, $font_face)
{
	$font_size = (double) $font_size;

	// Create the image
	$size = imagettfbbox($font_size, 0, $font_face, $site_logo_name);
	$width = $size[2] + $size[0] + 8;
	$height = abs($size[1]) + abs($size[7]);

	$im = imagecreate($width, $height);
	$colourBlack = imagecolorallocate($im, 255, 255, 255);

	imagecolortransparent($im, $colourBlack);

	// Create some colors
	$white = imagecolorallocate($im, 255, 255, 255);
	$black = imagecolorallocate($im, 0, 0, 0);

	// Add the text
	imagefttext($im, $font_size, 0, 0, abs($size[5]), $black, $font_face, $site_logo_name);

	imagepng($im,'../uploades/photos/water_logo.png');
	imagedestroy($im);
	return;
}

function UpdateModuleMenu($xml_file_path, $no_exists="", $item_name="")
{
	$file_path = dirname(__FILE__)."/../include/admin_menu.xml";

	if ($no_exists !=1) {
		$dist_file_path = dirname(__FILE__)."/".$xml_file_path;
		$dist_xml_parser = new SimpleXmlParser( $dist_file_path );
		$dist_xml_node = $dist_xml_parser->getRoot();
	}
	$xml_parser = new SimpleXmlParser( $file_path );
	$xml_root = $xml_parser->getRoot();
	for ( $i = 0; $i < $xml_root->childrenCount; $i++ ) {
		if ( $xml_root->children[$i]->attrs["name"] == "modules_management" ) {
			if ($no_exists !=1) {
				$replace = false;
				for ( $j = 0; $j < $xml_root->children[$i]->childrenCount; $j++ ) {
					if ( $xml_root->children[$i]->children[$j]->attrs["name"] == $dist_xml_node->attrs["name"] ) {
						$replace = true;
						$xml_root->children[$i]->children[$j] = $dist_xml_node;
					}
				}
				if (!$replace) {
					$xml_root->children[$i]->children[$xml_root->children[$i]->childrenCount] = $dist_xml_node;
				}
			} else {
				$count = $xml_root->children[$i]->childrenCount;
				for ( $j = 0; $j < $xml_root->children[$i]->childrenCount; $j++ ) {
					if ( $xml_root->children[$i]->children[$j]->attrs["name"] == $item_name ) {
						unset($xml_root->children[$i]->children[$j]);
					}
				}
				$xml_root->children[$i]->childrenCount = $count - 1;
			}
		}
	}

	$obj_saver = new Object2Xml();
	$obj_saver->Save( $xml_root, $file_path );

	if ($no_exists !=1) {
		unset( $dist_xml_parser, $xml_parser, $dist_xml_node, $xml_root );
	} else {
		unset( $xml_parser, $xml_root );
	}
	return;
}

function ListIdentCountries()
{
	global $dbconn, $smarty, $config, $lang;

	$file_name = "admin_settings.php";

	AdminMainMenu($lang["settings"]);

	$id_lang = intval($_REQUEST['id']);

	if (!$id_lang){
		echo "<script type='text/javascript'>window.close();</script>";
		exit;
	}

	$IpInfo = new IpInfo();

	$countries = $IpInfo->GetAllCountries();

	if (count($countries) == 0){
		$form["empty"] = 1;
		$form["install_link"] = $config["server"].$config["site_root"]."/install/ip_countries/";

		$search = array("[install_link]","[/install_link]");
		$repl = array("<a href='#' onClick='javascript: goToInstall();'>","</a>");
		$form["error"] = str_replace($search,$repl,$lang["lang_ident_feature"]["epmty_coutries"]);
	}

	$form["id_lang"] = $id_lang;
	$form["hiddens"] = "<input type='hidden' name='sel' value='save_ident_countries'>";
	$form["hiddens"] .= "<input type='hidden' name='id' value='".$id_lang."'>";

	$smarty->assign('form', $form);
	$smarty->assign('countries', $countries);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_ident_countries_form.tpl");
	exit;
}

function SaveIdentCountries()
{
	$IpInfo = new IpInfo();

	$IpInfo->SetIdLangToCountries($_POST['id'], $_POST['code']);

	ListIdentCountries();
}

?>