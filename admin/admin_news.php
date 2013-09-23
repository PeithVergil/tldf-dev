<?php

/**
* Site news section management (news RSS feeds subscribe, create/edit/delete site news).
*
* @package DatingPro
* @subpackage Admin Mode
**/

include "../include/config.php";
include_once "../common.php";
include "../include/config_admin.php";
include "../include/functions_auth.php";
include "../include/functions_admin.php";
include "../include/class.news.php";

$auth = auth_user();
login_check($auth);
IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "news");

$sel = isset($_REQUEST["sel"]) ? $_REQUEST["sel"] : "";

switch($sel){
	case "add": AddNews(); break;
	case "edit": EditForm("edit"); break;
	case "change": ChangeNews(); break;
	case "del": DeleteNews(); break;
	case "add_f": AddFeed(); break;
	case "edit_f": EditFormFeed("edit"); break;
	case "change_f": ChangeFeed(); break;
	case "del_f": DeleteFeed(); break;
	case "upd_f": UpdateFeeds(); break;
	default: ListNews();
}

function ListNews($err="")
{
	global $smarty, $dbconn, $config, $config_admin, $page, $lang;

	$file_name = "admin_news.php";

	AdminMainMenu($lang["news"]);

	$page = (isset($_REQUEST["page"]) && intval($_REQUEST["page"])>0) ? intval($_REQUEST["page"]) : 1;

	$strSQL = "SELECT COUNT(id) FROM ".NEWS_TABLE." WHERE id_channel='0'";
	$rs = $dbconn->Execute($strSQL);
	$num_records = $rs->fields[0];

	$lim_min = ($page-1)*$config_admin["news_numpage"];
	$lim_max = $config_admin["news_numpage"];
	$limit_str = " limit ".$lim_min.", ".$lim_max;
	$strSQL = "SELECT id, DATE_FORMAT(date_add,'".$config["date_format"]."') as date_add,  news_text, status, title FROM ".NEWS_TABLE." WHERE id_channel='0' ORDER BY date_ts desc, id DESC ".$limit_str;
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$news = array();
	if ($rs->RowCount() > 0) {
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$news[$i]["number"] = ($page-1)*$config_admin["news_numpage"]+($i+1);
			$news[$i]["id"] = $row["id"];
			$news[$i]["title"] = strip_tags(stripslashes($row["title"]));
			$news[$i]["text"] = strip_tags(stripslashes($row["news_text"]));
			if (strlen(utf8_decode($news[$i]["text"]))>100) {
				$news[$i]["text"] = utf8_substr($news[$i]["text"], 0, 100)."...";
			}
			$news[$i]["date"] = $row["date_add"];
			$news[$i]["status"] = $row["status"]?"+":"";
			$news[$i]["deletelink"] = $file_name."?sel=del&id=".$rs->fields[0];
			$news[$i]["editlink"] = $file_name."?sel=edit&page=".$page."&id=".$rs->fields[0];
			$rs->MoveNext();
			$i++;
		}
		$param = $file_name."?";
		$smarty->assign("links", GetLinkStr($num_records,$page,$param,$config_admin["news_numpage"]));
		$smarty->assign("news", $news);
	}

	$strSQL = "select id, DATE_FORMAT(date_update,'".$config["date_format"]." %h:%i:%s')  as date_update,  link, status, max_news from ".NEWS_FEEDS_TABLE."  order by id  ";
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$feeds = array();
	if ($rs->RowCount() > 0) {
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$feeds[$i]["number"] = $i+1;
			$feeds[$i]["id"] = $row["id"];
			$feeds[$i]["link"] = $row["link"];
			$feeds[$i]["date"] = $row["date_update"];
			$feeds[$i]["status"] = $row["status"]?"+":"";
			$feeds[$i]["deletelink"] = $file_name."?sel=del_f&id=".$row["id"];
			$feeds[$i]["editlink"] = $file_name."?sel=edit_f&page=".$page."&id=".$row["id"];
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign("feeds", $feeds);
	}
	///	form
	$form["err"] = $err;
	$form["confirm"] = $lang["confirm"]["news"];

	$smarty->assign("add_link", $file_name."?sel=add&page=".$page);
	$smarty->assign("rss_add_link", $file_name."?sel=add_f&page=".$page);
	$smarty->assign("rss_update_link", $file_name."?sel=upd_f&page=".$page);
	$smarty->assign("form", $form);
	$smarty->assign("button", $lang["button"]);
	$smarty->assign("header", $lang["news"]);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_news_table.tpl");
	exit;
}

function EditForm($par, $err="")
				{
	global $smarty, $dbconn, $config, $lang;
	global $spaw_root, $spaw_dir, $spaw_base_url, $spaw_dropdown_data;

	$file_name = "admin_news.php";

	AdminMainMenu($lang["news"]);

	$page = (isset($_REQUEST["page"]) && intval($_REQUEST["page"])>0) ? intval($_REQUEST["page"]) : 1;

	if($err){
		$form["err"] = $err;
		$data["text"] = $_POST["text"];
		$data["title"] = $_POST["title"];
		$data["status"] = $_POST["status"];
		$data["n_day"] = $_POST["n_day"];
		$data["n_month"] = $_POST["n_month"];
		$data["n_year"] = $_POST["n_year"];
	}
	if($par != "add"){
		global $id;
		if(!$id) $id = $_GET["id"];

		if(!$err){
			if(!$id){ ListNews(); return;}
			$strSQL = "	SELECT id, DATE_FORMAT(date_add,'%Y') AS n_year, DATE_FORMAT(date_add,'%m') AS n_month,
						DATE_FORMAT(date_add,'%d') AS n_day, news_text, status, title
						FROM ".NEWS_TABLE." WHERE id='".$id."'";
			$rs = $dbconn->Execute($strSQL);
			$row = $rs->GetRowAssoc(false);
			$data["text"] = stripslashes($row["news_text"]);
			$data["title"] = stripslashes($row["title"]);
			$data["status"] = $row["status"];
			$data["n_day"] = $row["n_day"];
			$data["n_month"] = $row["n_month"];
			$data["n_year"] = $row["n_year"];
		}
		$form["hiddens"] = "<input type=hidden name=sel value=change>";
		$form["hiddens"] .= "<input type=hidden name=e value=1>";
		$form["hiddens"] .= "<input type=hidden name=page value=\"".$page."\">";
		$form["hiddens"] .= "<input type=hidden name=id value=\"".$id."\">";
		$form["delete"] = $file_name."?sel=del&id=".$id;
	}else{
		$data["status"] = 1;
		$data["text"] = "";
		$form["hiddens"] = "<input type=hidden name=sel value=add>";
		$form["hiddens"] .= "<input type=hidden name=e value=1>";
		$form["hiddens"] .= "<input type=hidden name=page value=\"".$page."\">";

	}
	
	if (RICH_TEXT_EDITOR == 'SPAW-1')
	{
		// include the control file
		$spaw_root = $config['site_path'].'/spaw/';
		include $spaw_root.'spaw_control.class.php';
		// pass $demo_array to the constructor
		$sw = new SPAW_Wysiwyg(
			'text',								/*name*/
			html_entity_decode($data['text']),	/*value*/
			'en',								/*language*/
			'full',								/*toolbar mode*/
			'default',							/*theme*/
			'500px',							/*width*/
			'500px',							/*height*/
			'',									/*stylesheet file*/
			$spaw_dropdown_data					/*dropdown data (is NULL)*/
		);
		$smarty->assign('editor', $sw->show());
	}
	elseif (RICH_TEXT_EDITOR == 'SPAW-2')
	{
		$spaw_root = $config['site_path'].'/spaw2/';
		include $spaw_root.'spaw_control.class.php';
		$sw = new SPAW_Wysiwyg(
			'text',								/*name*/
			html_entity_decode($data['text']),	/*value*/
			'en',								/*language*/
			'full',								/*toolbar mode*/
			'default',							/*theme*/
			'700px',							/*width*/
			'500px',							/*height*/
			'',									/*stylesheet file*/
			$spaw_dropdown_data					/*dropdown data (is NULL)*/
		);
		$smarty->assign('editor', $sw->getHTML());
	}
	
	//// day select
	$data["n_day"] = (isset($data["n_day"]) && intval($data["n_day"])>0) ? $data["n_day"] : date("d");
	$day = array();
	for ($i = 0; $i < 31; $i++) {
		$day[$i]["value"] = $i+1;
		if(intval($data["n_day"]) == $i+1)
		$day[$i]["sel"] = 1;
		else
		$day[$i]["sel"] = 0;
	}
	$smarty->assign("day", $day);

	////  month select
	$data["n_month"] = (isset($data["n_month"]) && intval($data["n_month"])>0) ? $data["n_month"] : date("m");
	$month = array();
	for ($i = 0; $i < 12; $i++) {
		$month[$i]["value"] = $i+1;
		$month[$i]["name"] = $lang["month"][$i+1];
		if(intval($data["n_month"]) == $i+1)
		$month[$i]["sel"] = 1;
		else
		$month[$i]["sel"] = 0;
	}
	$smarty->assign("month", $month);

	////  year select	(take there only free values of year prev, next and present )
	$data["n_year"] = (isset($data["n_year"]) && intval($data["n_year"])>0) ? $data["n_year"] : date("Y");
	$year = array();
	for ($i = 0; $i < 3; $i++) {
		$y = intval(date("Y"))+1-$i;
		$year[$i]["value"] = $y;
		if(intval($data["n_year"]) == $y)
		$year[$i]["sel"] = 1;
		else
		$year[$i]["sel"] = 0;
	}
	$smarty->assign("year", $year);
	
	$form["back"] = $file_name."?page=".$page;
	$form["action"] = $file_name;
	$form["par"] = $par;
	$form["confirm"] = $lang["confirm"]["news"];
	
	$smarty->assign("data", $data);
	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["news"]);
	$smarty->assign("err", $lang["err"]);
	$smarty->assign("button", $lang["button"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_news_form.tpl");
	exit;
}

function AddNews()
{
	global $dbconn, $lang, $e;
	
	$text = isset($_POST["text"]) ? $_POST["text"] : "";
	$title = isset($_POST["title"]) ? $_POST["title"] : "";
	$status = (isset($_POST["status"]) && intval($_POST["status"])>0) ? intval($_POST["status"]) : 0;
	
	$n_year = isset($_POST["n_year"]) ? $_POST["n_year"] : "";
	$n_month = isset($_POST["n_month"]) ? $_POST["n_month"] : "";
	$n_day = isset($_POST["n_day"]) ? $_POST["n_day"] : "";
	
	if (!strlen($text)) {
		if ($e) {
			$err = $lang["err"]["invalid_fields"];
			$err .= "<br>".$lang["news"]["text"];
		} else {
			$err = "";
		}
		EditForm("add", $err);
	}
	
	if (checkdate($n_month, $n_day, $n_year)) {
		$date_add = $n_year."-".sprintf("%02d",$n_month)."-".sprintf("%02d",$n_day);
		$timestamp = mktime(0, 0, 0, $n_month, $n_day, $n_year);
	} else {
		if ($e) {
			$err = $lang["err"]["invalid_date"];
		}
		EditForm("add", $err);
	}
	
	if (!$title) {
		$title = utf8_substr(strip_tags($text), 0, 100)."...";
	}
	$str = "INSERT INTO ".NEWS_TABLE." (date_add, news_text, status, title, date_ts, channel_name, channel_link, news_link, id_channel)
			VALUES ('".$date_add."', '".addslashes($text)."', '".$status."', '".addslashes($title)."', '".$timestamp."', '', '', '', '0')";
	$dbconn->Execute($str);
	ListNews();
	return;
}

function ChangeNews()
{
	global $dbconn, $lang, $e;

	$id = isset($_POST["id"]) ? intval($_POST["id"]): null;
	
	if (!$id) {
		ListNews();
		return;
	}
	
	$text = $_POST["text"];
	$title = $_POST["title"];
	$status = intval($_POST["status"]);
	$n_year = $_POST["n_year"];
	$n_month = $_POST["n_month"];
	$n_day = $_POST["n_day"];

	if (!strlen($text)) {
		if ($e) {
			$err = $lang["err"]["invalid_fields"];
			$err .= "<br>".$lang["news"]["text"];
		}
		EditForm("edit", $err);
	}
	
	if (checkdate($n_month, $n_day, $n_year)) {
		$date_add = $n_year."-".sprintf("%02d",$n_month)."-".sprintf("%02d",$n_day);
		$timestamp = mktime(0, 0, 0, $n_month, $n_day, $n_year);
	} else {
		if ($e) {
			$err = $lang["err"]["invalid_date"];
		}
		EditForm("edit", $err);
	}
	
	if (!$title) {
		$title = utf8_substr(strip_tags($text), 0, 100)."...";
	}
	
	$str = "UPDATE ".NEWS_TABLE." SET date_add = '".$date_add."', title='".addslashes(strip_tags($title))."', news_text = '".addslashes($text)."',
			status = '".$status."', date_ts='".$timestamp."' WHERE id='".$id."' ";
	$dbconn->Execute($str);

	ListNews();
	return;
}

function DeleteNews()
{
	global $dbconn;
	
	$id = isset($_REQUEST["id"]) ? intval($_REQUEST["id"]) : null;
	
	if (!$id) {
		ListNews();
		return;
	}
	
	$dbconn->Execute("delete from ".NEWS_TABLE." where id='".$id."'");
	ListNews();
	return;
}


function EditFormFeed($par, $err="")
{
	global $smarty, $dbconn, $config, $lang;

	$file_name = "admin_news.php";
	AdminMainMenu($lang["news"]);
	$page = (isset($_REQUEST["page"]) && intval($_REQUEST["page"])>0) ? intval($_REQUEST["page"]) : 1;
	
	if ($err) {
		$form["err"] = $err;
		$data = $_POST;
	}

	if ($par != "add") {
		global $id;
		if (!$id) {
			$id = $_GET["id"];
		}
		
		if (!$err) {
			if (!$id) {
				ListNews();
				return;
			
				}
			$strSQL = "select id, link, status, max_news from ".NEWS_FEEDS_TABLE." where id='".$id."'";
			$rs = $dbconn->Execute($strSQL);
			$row = $rs->GetRowAssoc(false);
			$data["link"] = $row["link"];
			$data["status"] = $row["status"];
			$data["max_news"] = $row["max_news"];
			if(!$data["max_news"])$data["all"] = 1;

		}

		$form["hiddens"] = "<input type=hidden name=sel value=change_f>";
		$form["hiddens"] .= "<input type=hidden name=e value=1>";
		$form["hiddens"] .= "<input type=hidden name=page value=\"".$page."\">";
		$form["hiddens"] .= "<input type=hidden name=id value=\"".$id."\">";
		$form["delete"] = $file_name."?sel=del_f&id=".$id;
	}else{
		$data["status"] = 1;
		$form["hiddens"] = "<input type=hidden name=sel value=add_f>";
		$form["hiddens"] .= "<input type=hidden name=e value=1>";
		$form["hiddens"] .= "<input type=hidden name=page value=\"".$page."\">";

	}
	$form["back"] = $file_name."?page=".$page;
	$form["action"] = $file_name;
	$form["par"] = $par;
	$form["confirm"] = $lang["confirm"]["news"];


	$smarty->assign("data", $data);
	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["news"]);
	$smarty->assign("err", $lang["err"]);
	$smarty->assign("button", $lang["button"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_news_feeds_form.tpl");
	exit;
}

function AddFeed()
{
	global $dbconn, $lang, $e;

	$link = isset($_POST["link"]) ? $_POST["link"] : "";
	$status = isset($_POST["status"]) ? intval($_POST["status"]) : 0;
	$max_news = isset($_POST["max_news"]) ? intval($_POST["max_news"]) : 0;

	if (!strlen($link)) {
		if ($e) {
			$err = $lang["err"]["invalid_fields"];
			$err .= "<br>".$lang["news"]["rss_link"];
		} else {
			$err = "";
		}
		EditFormFeed("add", $err);
	}
	$rss_array = array();
	$rss_array = rss2array($link);
	if (count($rss_array["errors"])) {
		if ($e) {
			$err = $lang["news"]["rss_link_error"];
		}
		EditFormFeed("add", $err);
	}
	// save feed
	$dbconn->Execute("insert into ".NEWS_FEEDS_TABLE." (date_update, link, max_news, status) values (now(), '".$link."', '".$max_news."', '".$status."')");
	ListNews(); return;
}

function ChangeFeed()
{
	global $dbconn, $lang, $e;
	
	$id = isset($_POST["id"]) ? $_POST["id"] : null;
	
	if (!$id) {
		ListNews();
		return;
	}
	
	$link = isset($_POST["link"]) ? $_POST["link"] : "";
	$status = isset($_POST["status"]) ? intval($_POST["status"]) : 0;
	$max_news = isset($_POST["max_news"]) ? intval($_POST["max_news"]) : 0;
	
	if (!strlen($link)) {
		if ($e) {
			$err = $lang["err"]["invalid_fields"];
			$err .= "<br>".$lang["news"]["rss_link"];
		}
		EditFormFeed("edit", $err);
	}
	$rss_array = array();
	$rss_array = rss2array($link);
	if (count($rss_array["errors"])) {
		if ($e) {
			$err = $lang["news"]["rss_link_error"];
		}
		EditFormFeed("add", $err);
	}

	$dbconn->Execute("UPDATE ".NEWS_FEEDS_TABLE."  SET link='".$link."', max_news='".$max_news."', status='".$status."' where id='".$id."' ");
	ListNews(); return;
}

function DeleteFeed()
{
	global $dbconn;

	$id = $_GET["id"];
	
	if (!$id) {
		ListNews();
		return;
	}
	
	$dbconn->Execute("delete from ".NEWS_FEEDS_TABLE." where id='".$id."'");
	$dbconn->Execute("delete from ".NEWS_TABLE." where id_channel='".$id."'");
	ListNews();
	return;
}

function UpdateFeeds($id = 0)
{
	NewsUpdater($id);
	ListNews();
	return;
}

?>