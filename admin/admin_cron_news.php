<?php

/**
* Cron file. Site news subscribe.
*
* @package DatingPro
* @subpackage Admin Mode
**/

error_reporting(~E_ALL);
include "../include/config.php";
include_once "../common.php";
include "../include/config_admin.php";
include "../include/config_index.php";
include "../include/functions_admin.php";
include "../include/class.phpmailer.php";
include "../include/functions_mail.php";
include "../include/class.news.php";

//// select from db all users who want to recieve news from site

$id_user = array();
$i = 0;

$rs = $dbconn->Execute(
	"SELECT a.id_user, b.login, b.fname, b.gender, b.email, b.site_language
	   FROM ".SUBSCRIBE_USER_TABLE." a
 INNER JOIN ".USERS_TABLE." b ON b.id = a.id_user
	  WHERE a.type = 's' AND a.id_subscribe = '5'
   ORDER BY b.site_language");

while (!$rs->EOF) {
	$row = $rs->GetRowAssoc(false);
	$id_user[$i]['id'] = $row['id_user'];
	$id_user[$i]['login'] = $row['login'];
	$id_user[$i]['fname'] = $row['fname'];
	$id_user[$i]['gender'] = $row['gender'];
	$id_user[$i]['email'] = $row['email'];
	$id_user[$i]['site_lang'] = $row['site_language'];
	$rs->MoveNext();
	$i++;
}

$news = array();
$i = 0;

if (count($id_user) > 0)
{
	$rs = $dbconn->Execute("SELECT UNIX_TIMESTAMP(date_last_send) from ".SUBSCRIBE_SISTEM_TABLE." where id='5'");
	$last_send = $rs->fields[0];
	$rs = $dbconn->Execute("SELECT id, DATE_FORMAT(date_add,'".$config["date_format"]."') as date_add, news_text from ".NEWS_TABLE." where status='1' and date_ts>'".$last_send."' order by date_ts desc, id desc");
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$news[$i]["id"] = $row["id"];
		$news[$i]["text"] = strip_tags($row["news_text"]);
		if (strlen(utf8_decode($news[$i]["text"])) > 200) {
			$news[$i]["text"] = utf8_substr($news[$i]["text"], 0, 200)."...";
		}
		$news[$i]["date"] = $row["date_add"];
		$news[$i]["link_read"] = GetNewsReadLink($row["id"]);
		$rs->MoveNext();
		$i++;
	}
	$rs = $dbconn->Execute("update ".SUBSCRIBE_SISTEM_TABLE." set date_last_send='".date("Y-m-d H:i:s")."' where id='5'");
}

$log_str = "";

if (!empty($news))
{
	$smarty->assign('news', $news);
	
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

		$data["content"] = $smarty->fetch(TrimSlash($config["index_theme_path"])."/mail_cron_news_details.tpl");
		
		$subject = $id_user[$i]['gender'] == GENDER_MALE ? $lang_mail['cron_news_e']['subject'] : $lang_mail['cron_news_t']['subject'];
		$subject = str_replace('[date]', date('d/m/Y'), $subject);
		
		$data['urls']	= GetUserEmailLinks();

		SendMail($site_lang, $id_user[$i]['email'], $config['site_email'], $subject, $data, 'mail_cron_news', null,
			$id_user[$i]['fname'], '', 'cron_news', $id_user[$i]['gender']);
		
		$log_str .= date("Y-m-d H:i:s")." <".$id_user[$i]["email"]."> ".$id_user[$i]["login"]." - news subscribe was send\n";
	}

	$log_file = $config["site_path"]."/include/newsletter_log.txt";
	
	if (file_exists($log_file)) {
		$f = fopen($log_file, "a+");
		fwrite($f, $log_str);
		fclose($f);
	}
}
?>