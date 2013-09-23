<?php

/**
* Cron file. Friends' birthdays reminder.
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
include "../include/functions_users.php";

// settings
$settings = GetSiteSettings(array("icon_male_default", "icon_female_default", 'icons_default', 'icons_folder', 'index_theme_path'));

// default icons
$default_photos['1'] = $settings['icon_male_default'];
$default_photos['2'] = $settings['icon_female_default'];

$tomorrow = strftime("%d-%m", mktime(date("H"), date("i"), date("s"),date("m"), date("d")+1, date("Y")));

$id_user = array();

$i = 0;
$strSQL =
	"SELECT b.login, b.fname, b.gender, b.email, a.id_user, b.site_language
	   FROM ".SUBSCRIBE_USER_TABLE." a, ".USERS_TABLE." b
	  WHERE a.type='s' AND b.id=a.id_user AND a.id_subscribe='6'
   ORDER BY b.site_language";
$rs = $dbconn->Execute($strSQL);

while (!$rs->EOF){
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

if (count($id_user) > 0) {
	// language
	$site_lang = $config['default_lang'];
	
	// include mail language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	for ($i = 0; $i < count($id_user); $i++) {
		$j = 0;
		$users = array();

		$strSQL =
			"SELECT DISTINCT DATE_FORMAT(a.date_birthday, '%d-%m'), a.login , a.id, a.date_birthday as date_birth,
					a.icon_path, a.gender, a.id_country, a.id_city, a.id_region
    		   FROM ".USERS_TABLE." a
		  LEFT JOIN ".HOTLIST_TABLE." b on a.id = b.id_friend
		      WHERE b.id_user='".$id_user[$i]['id']."'";
		$rs = $dbconn->Execute($strSQL);
		$attaches=array();
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			if ($rs->fields[0] == $tomorrow){
				$users[$j]["login"] = $row['login'];
				$users[$j]["id_city"] = $row["id_city"];
				$users[$j]["id_region"] = $row["id_region"];
				$users[$j]["id_country"] = $row["id_country"];
				$users[$j]["age"] = AgeFromBDate($row["date_birth"]);

				//User icon
				if (isset($row["icon_path"]) && $row["icon_path"]!="") {
					$icon_path = $row["icon_path"];
				} else {
					$icon_path=$default_photos[$row['gender']];
				}
				$users[$j]['icon'] = "cid:agent".$config["server"].$config["site_root"].$settings['icons_folder']."/".$icon_path;
				$attaches["id"][$j] = $config["server"].$config["site_root"].$settings['icons_folder']."/".$icon_path;
				$attaches["image_path"][$j] = $config["site_path"].$settings['icons_folder']."/".$icon_path;
				$attaches["image_name"][$j] = "";
				$attaches["image_type"][$j] = "application/octet-stream";

				//Base lang
				$_LANG_NEED_ID["country"][] = intval($row["id_country"]);
				$_LANG_NEED_ID["region"][] = intval($row["id_region"]);
				$_LANG_NEED_ID["city"][] = intval($row["id_city"]);

				//Links
				$users[$j]["link_read"] = $config["server"].$config["site_root"]."/viewprofile.php?id=".$rs->fields[2];

				$j++;
			}
			$rs->MoveNext();
		}

		if (count($users) > 0) {
			$smarty->assign("users", $id_user[$i]);
			$smarty->assign("login", $rs->fields[1]);
			$smarty->assign("birthdays", $users);
			$smarty->assign("base_lang", GetBaseLang($_LANG_NEED_ID));

			$site_lang_user = $id_user[$i]['site_lang'];
			
			if ($site_lang != $site_lang_user) {
				$site_lang = $site_lang_user;
				
				// include mail language file
				$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
				$lang_mail = array();
				include $config['path_lang'].'mail/'.$lang_file;
			}
			
			$smarty->assign("header", $lang_mail);

			$subject = ($id_user[$i]['gender'] == GENDER_MALE) ? $lang_mail['cron_birthday_e']['subject'] : $lang_mail['cron_birthday_t']['subject'];
			$subject = str_replace('[date]', $tomorrow, $subject);
			
			$data['content'] = $smarty->fetch(TrimSlash($config['index_theme_path']).'/mail_cron_birthday_details.tpl');
			
			$data['urls']	= GetUserEmailLinks();
			
			SendMail($site_lang, $id_user[$i]['email'], $config['site_email'], $subject, $data, 'mail_cron_birthday', $attaches,
				$id_user[$i]['fname'], '', 'cron_birthday', $id_user[$i]['gender']);
			
			unset($users);
		}
	}
}

?>