<?php

/**
* Cron file. New users on site subscribe.
*
* @package DatingPro
* @subpackage Admin Mode
**/

include "../include/config.php";
include_once "../common.php";
include "../include/config_admin.php";
include "../include/config_index.php";
include "../include/functions_admin.php";
include "../include/class.phpmailer.php";
include "../include/functions_mail.php";

//// select from db all users who want to recieve news from site
$settings = GetSiteSettings(array("icon_male_default", "icon_female_default", 'icons_default', 'icons_folder', 'index_theme_path', 'site_email'));

//Get default icons
$default_photos['1'] = $settings['icon_male_default'];
$default_photos['2'] = $settings['icon_female_default'];

$id_user = array();
$i = 0;

$strSQL =
	"SELECT a.id_user, b.login, b.fname, b.gender, b.email, b.site_language
	   FROM  ".SUBSCRIBE_USER_TABLE." a, ".USERS_TABLE." b
	  WHERE a.type='s' AND a.id_subscribe='1' AND b.id=a.id_user
   ORDER BY b.site_language";

$rs = $dbconn->Execute($strSQL);

while (!$rs->EOF) {
	$row = $rs->GetRowAssoc(false);
	$id_user[$i]["id"] = $row["id_user"];
	$id_user[$i]["login"] = $row["login"];
	$id_user[$i]["fname"] = $row["fname"];
	$id_user[$i]["gender"] = $row["gender"];
	$id_user[$i]["email"] = $row["email"];
	$id_user[$i]["site_lang"] = $row["site_language"];
	$rs->MoveNext();
	$i++;
}

$users = array();
$_LANG_NEED_ID = array();
$i = 0;

if (count($id_user) > 0) {
	$rs = $dbconn->Execute("select UNIX_TIMESTAMP(date_last_send) from ".SUBSCRIBE_SISTEM_TABLE." where id='1'");
	$last_send = $rs->fields[0];
	$rs = $dbconn->Execute("update ".SUBSCRIBE_SISTEM_TABLE." set date_last_send='".date("Y-m-d H:i:s")."' where id='1'");
	
	$strSQL =
		"SELECT id, login, date_birthday, icon_path, gender, id_country, id_city, id_region, headline,
				DATE_FORMAT(date_registration,'".$config["date_format"]."') AS date_reg
		   FROM ".USERS_TABLE."
		  WHERE status = '1' AND visible = '1' AND UNIX_TIMESTAMP(date_registration) > '".$last_send."'
			AND root_user = '0' AND guest_user = '0'";
	$rs = $dbconn->Execute($strSQL);
	
	$attaches = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$users[$i]["id"]		= $row["id"];
		$users[$i]["login"]		= $row["login"];
		$users[$i]["text"]		= $row["headline"];
		$users[$i]["date"]		= $row["date_reg"];
		
		$users[$i]["id_city"]	= $row["id_city"];
		$users[$i]["id_region"]	= $row["id_region"];
		$users[$i]["id_country"]= $row["id_country"];
		$users[$i]["age"]		= AgeFromBDate($row["date_birthday"]);
		
		//User icon
		if (isset($row["icon_path"]) && $row["icon_path"]!="") {
			$icon_path = $row["icon_path"];
		} else {
			$icon_path = $default_photos[$row['gender']];
		}
		
		$users[$i]['icon']			= "cid:agent".$config["server"].$config["site_root"].$settings['icons_folder']."/".$icon_path;
		$attaches["id"][$i]			= $config["server"].$config["site_root"].$settings['icons_folder']."/".$icon_path;
		$attaches["image_path"][$i]	= $config["site_path"].$settings['icons_folder']."/".$icon_path;
		$attaches["image_name"][$i]	= "";
		$attaches["image_type"][$i]	= "application/octet-stream";
		
		//Base lang
		$_LANG_NEED_ID["country"][]	= intval($row["id_country"]);
		$_LANG_NEED_ID["region"][]	= intval($row["id_region"]);
		$_LANG_NEED_ID["city"][]	= intval($row["id_city"]);
		
		//Links
		$users[$i]["link_read"] = $config["server"].$config["site_root"]."/viewprofile.php?id=".$row["id"];
		
		$rs->MoveNext();
		$i++;
	}
}

$log_str = "";

if (count($users) > 0) {
	$smarty->assign('users', $users);
	$smarty->assign('base_lang', GetBaseLang($_LANG_NEED_ID));
	
	// language
	$site_lang = $config['default_lang'];
	
	// include mail language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	for ($i = 0; $i < count($id_user); $i++) {
		$site_lang_user = $id_user[$i]['site_lang'];
		
		if ($site_lang != $site_lang_user) {
			$site_lang = $site_lang_user;
			
			// include mail language file
			$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
			$lang_mail = array();
			include $config['path_lang'].'mail/'.$lang_file;
		}
		
		$smarty->assign("header", $lang_mail);
		
		$data["content"] = $smarty->fetch(TrimSlash($config["index_theme_path"])."/mail_cron_new_users_details.tpl");
		
		$subject = ($id_user[$i]['gender'] == GENDER_MALE) ? $lang_mail["cron_new_users_e"]["subject"] : $lang_mail["cron_new_users_t"]["subject"];
		$subject = str_replace("[date]", date("d/m/Y"), $subject);
		
		$data['urls']		= GetUserEmailLinks();
		
		SendMail($site_lang, $id_user[$i]['email'], $config['site_email'], $subject, $data, 'mail_cron_new_users', $attaches,
			$id_user[$i]['fname'], '', 'cron_new_users', $id_user[$i]['gender']);
		
		$log_str .= date("Y-m-d H:i:s")." <".$id_user[$i]["email"]."> ".$id_user[$i]["login"]." - new users registration subscribe was send\n";
	}
	
	$log_file = $config["site_path"]."/include/newsletter_log.txt";
	
	if (file_exists($log_file)) {
		$f = fopen($log_file, "a+");
		fwrite($f, $log_str);
		fclose($f);
	}
}
?>