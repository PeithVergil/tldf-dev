<?php
/**
* Cron file. Perfect match subscribe.
*
* @package DatingPro
* @subpackage Admin Mode
**/

include '../include/config.php';
include_once '../common.php';
include '../include/config_admin.php';
include '../include/config_index.php';
include '../include/functions_admin.php';
include '../include/class.phpmailer.php';
include '../include/functions_mail.php';
include '../include/functions_users.php';

//// select from db all users who want to recieve news from site
$settings = GetSiteSettings(array('icon_male_default', 'icon_female_default', 'icons_default', 'icons_folder', 'index_theme_path'));

//Get default icons
$default_photos['1'] = $settings['icon_male_default'];
$default_photos['2'] = $settings['icon_female_default'];

$strSQL = 'select b.id, b.login, b.fname, b.gender, b.email, b.site_language
	from '.SUBSCRIBE_USER_TABLE.' a, '.USERS_TABLE.' b, '.USER_MATCH_TABLE.' c
	where a.type="s" and a.id_subscribe="2" and b.id=a.id_user and c.id_user=b.id
	order by b.site_language';

$rs = $dbconn->Execute($strSQL);

$id_user = array();
$i = 0;

while (!$rs->EOF) {
	$row = $rs->GetRowAssoc(false);
	$id_user[$i]['id'] = $row['id'];
	$id_user[$i]['login'] = $row['login'];
	$id_user[$i]['fname'] = $row['fname'];
	$id_user[$i]['gender'] = $row['gender'];
	$id_user[$i]['email'] = $row['email'];
	$id_user[$i]['site_lang'] = $row['site_language'];
	$i++;

	$rs->MoveNext();
}

$log_str = "";

if(count($id_user)>0){
	$rs = $dbconn->Execute("select UNIX_TIMESTAMP(date_last_send) from ".SUBSCRIBE_SISTEM_TABLE." where id='2'");
	$last_send = $rs->fields[0];
	$rs = $dbconn->Execute("update ".SUBSCRIBE_SISTEM_TABLE." set date_last_send=now() where id='2'");
	
	// language
	$site_lang = $config["default_lang"];
	
	// include mail language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	for ($i = 0; $i < count($id_user); $i++) {
		$user_match = array();
		$id_user_match = array();
		$id_user_match = GetPerfectUsersList($id_user[$i]["id"], $last_send);
		$attaches=array();
		if (count($id_user_match)){
			$rs = $dbconn->Execute("select  a.id, a.login, a.date_birthday, a.icon_path, a.gender, a.id_country, a.id_city, a.id_region, a.headline, DATE_FORMAT(a.date_registration,'".$config["date_format"]."') as date_reg from ".USERS_TABLE." a  where a.id in (".implode(",", $id_user_match["id_arr"]).")");
			$j = 0;
			while (!$rs->EOF) {
				$row = $rs->GetRowAssoc(false);
				$user_match[$j]["id"] = $row["id"];
				$user_match[$j]["login"] = $row["login"];
				$user_match[$j]["text"] = stripslashes($row["headline"]);
				$user_match[$j]["date"] = $row["date_reg"];

				$user_match[$j]["id_city"] = $row["id_city"];
				$user_match[$j]["id_region"] = $row["id_region"];
				$user_match[$j]["id_country"] = $row["id_country"];
				$user_match[$j]["age"] = AgeFromBDate($row["date_birthday"]);

				//User icon
				if (isset($row["icon_path"]) && $row["icon_path"]!="") {
					$icon_path=$row["icon_path"];
				} else {
					$icon_path=$default_photos[$row['gender']];
				}
				$user_match[$j]['icon']="cid:agent".$config["server"].$config["site_root"].$settings['icons_folder']."/".$icon_path;
				$attaches["id"][$j]=$config["server"].$config["site_root"].$settings['icons_folder']."/".$icon_path;
				$attaches["image_path"][$j]=$config["site_path"].$settings['icons_folder']."/".$icon_path;
				$attaches["image_name"][$j]="";
				$attaches["image_type"][$j]="application/octet-stream";

				//Base lang
				$_LANG_NEED_ID["country"][] = intval($row["id_country"]);
				$_LANG_NEED_ID["region"][] = intval($row["id_region"]);
				$_LANG_NEED_ID["city"][] = intval($row["id_city"]);

				//Links
				$user_match[$j]["link_read"] = $config["server"].$config["site_root"]."/viewprofile.php?id=".$row["id"];
				$j++;
				$rs->MoveNext();
			}
		}

		if (count($user_match) > 0)
		{
			$smarty->assign('users', $user_match);
			$smarty->assign('base_lang', GetBaseLang($_LANG_NEED_ID));

			$site_lang_user = $id_user[$i]['site_lang'];
			
			if ($site_lang != $site_lang_user) {
				$site_lang = $site_lang_user;
				
				// include mail language file
				$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
				$lang_mail = array();
				include $config['path_lang'].'mail/'.$lang_file;
			}
			
			$smarty->assign('header', $lang_mail);

			$subject = ($id_user[$i]['gender'] == GENDER_MALE) ? $lang_mail['cron_perfect_e']['subject'] : $lang_mail['cron_perfect_t']['subject'];
			$subject = str_replace('[date]', date('d/m/Y'), $subject);
			
			$data['content'] = $smarty->fetch(TrimSlash($config['index_theme_path']).'/mail_cron_perfect_details.tpl');
			
			$data['urls']	= GetUserEmailLinks();
			
			$err = SendMail($site_lang, $id_user[$i]['email'], $config['site_email'], $subject, $data, 'mail_cron_perfect', $attaches,
				$id_user[$i]['fname'], '', 'cron_perfect', $id_user[$i]['gender']);
			
			if (!$err) {
				$log_str .= date('Y-m-d H:i:s').' <'.$id_user[$i]['email'].'> '.$id_user[$i]['login']." - new perfect match users registration subscribe was send\n";
			} else {
				$log_str .= date('Y-m-d H:i:s').' <'.$id_user[$i]['email'].'> '.$id_user[$i]['login'].' - '.$err." (perfect match users registration subscribe)\n";
			}
		}
	}
	
	$log_file = $config['site_path'].'/include/newsletter_log.txt';
	
	if (file_exists($log_file)) {
		$f = fopen($log_file, 'a+');
		fwrite($f, $log_str);
		fclose($f);
	}
}
?>