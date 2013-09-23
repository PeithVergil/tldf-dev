<?php

/**
* Flash-used files
*
*
* @package DatingPro
* @subpackage Shoutbox module
**/

include "../include/config.php";
include "sh_config.php";
include "sh_common.php";
include "../include/functions_common.php";

$config["recive_last_messages_number"] = GetSiteSettings('shout_messages_limit');

if (isset($_GET["act"])) $act = $_GET["act"];
else  $act = "";
if ($act=="recv_shouts")
{
	print(SHOUTBOX_HEADER);
	recv_shouts();
}
else
if ($act=="open_profile")
{
	open_profile();
}
else
if ($act=="send_shout")
{
	print(SHOUTBOX_HEADER);
	send_shout();
}
else
{
	print(SHOUTBOX_HEADER);
	print("<br>\nError: Unkonwn act.<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
}


// Get current userid by session
function get_user_session_id()
{
	global $dbconn;
	@session_start();
	$sess_id = session_id();
	if ((!$sess_id)&&(isset($PHPSESSID))) $sess_id = $PHPSESSID;
	$strSQL = "SELECT u.id FROM ".ACTIVE_SESSIONS_TABLE." s, ".USERS_TABLE." u WHERE s.session = '".$sess_id."' AND s.id_user = u.id";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->RowCount())
	{
		return $rs->fields[0];
	}
	else
	{
		return 0;
	}
}

// Recive user login
function get_user_login($user_id)
{
	global $dbconn;
	$strSQL = "select login from ".USERS_TABLE." where id='$user_id'";
	$rs = $dbconn->Execute($strSQL);
	if (($rs===false)||($rs->EOF))
	{
		return null;
	}
	$row = $rs->GetRowAssoc(false);
	$name = $row["login"];
	return $name;
}

function get_user_id($user_name)
{
	global $dbconn;
	$strSQL = "select id from ".USERS_TABLE." where login='$user_name'";
	$rs = $dbconn->Execute($strSQL);
	if (($rs===false)||($rs->EOF))
	{
		print("<br>\nError: get_user_id('$user_name') - user not exists."."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
		exit();
	}
	$row = $rs->GetRowAssoc(false);
	$id = $row["id"];
	return $id;
}

// Recive user photo url
function get_user_icon_url($user_id)
{
	global $dbconn, $config;
	$strSQL = "select a.icon_path from ".USERS_TABLE." a where a.id = '$user_id'";
	$rs = $dbconn->Execute($strSQL);
	if (($rs==false)||($rs->EOF))
	{
		return "";
	}
	$row = $rs->GetRowAssoc(false);
	if ($row["icon_path"]=="")
	{
		return $config["server"].$config["site_root"]."/uploades/icons/default_1.gif";
	}
	$full_url = $config["server"].$config["site_root"]."/uploades/icons/".$row["icon_path"];
	return $full_url;
}

// Recive Shouts list in text format
function recv_shouts()
{
	global $dbconn, $config;
	$user_id = get_user_session_id();
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
	
	$strSQL = "SELECT a.id, a.user_id, a.text, a.date_add
				FROM ".SHOUTS_TABLE." a
				LEFT JOIN ".USER_PRIVACY_SETTINGS." AS up ON up.id_user=a.user_id
				WHERE a.status ='1' ".$where_str." OR up.id_user=$user_id ORDER BY a.date_add DESC LIMIT ".$config["recive_last_messages_number"];
	//echo $strSQL."<br><br>";
	$rs = $dbconn->Execute($strSQL);
	if ($rs===false)
	{
		print("<br>\nError: Database error.<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
	}
	$shouts_count=0;
	while (!$rs->EOF)
	{
		$i=$shouts_count;
		$row = $rs->GetRowAssoc(false);
		$user_id   = $row["user_id"];
		$user_name = get_user_login($user_id);
		if (!$user_name)
		{
			$rs->MoveNext();
			continue;
		}
		print("<br>\nId".$i.": ".$row["id"]);
		print("<br>\nUserName".$i.": ".$user_name);
		print("<br>\nUserPhotoUrl".$i.": ".get_user_icon_url($user_id));
		print("<br>\nMessage".$i.": ".stripslashes($row["text"]));
		print("<br>\nPost_date".$i.": ".strtotime($row["date_add"]));
		$rs->MoveNext();
		$shouts_count++;
	}
	print("<br>\nMessages count: ".$shouts_count);
	print("<br>\nServer time: ".time());
	return 0;
}

//  Return true - if email exists
/*function EmailFilter($str)
{
	if (strlen($str)>0)
	if (eregi("^.+@.+\\..+$", $str)) return 1;
	return 0;
}*/

//  Return true - if message contains badword
function BadWordsFilter($str)
{
	global $dbconn, $config;

	$bw_array = array();
	$text = trim(strtolower($str));

	$rs = $dbconn->Execute("select name, value from ".SETTINGS_TABLE." where name in ('badwords_file_path', 'badwords_file_name')");
	while(!$rs->EOF)
	{
		$settings[$rs->fields[0]] = $rs->fields[1];
		$rs->MoveNext();
	}
	$file_path = $config["site_path"].$settings["badwords_file_path"]."/".$settings["badwords_file_name"];
	if(file_exists($file_path) && is_readable($file_path) && strlen($text)>0)
	{
		$bw_file = strtolower(implode("", file($file_path)));
		$bw_file = explode(",", $bw_file);
		foreach($bw_file as $k => $v)
		{
			if(strlen(trim($v))>0)
			{
				$pos = eregi("(^| |[[:punct:]])".trim($v)."($| |[[:punct:]])", $text);
				if(intval($pos) != 0) return 1; /// find
			}
		}
	}
	return 0;
}

// Send shouts
function send_shout()
{
	global $lang;
	if ((!isset($_REQUEST["shout_text"]))||($_REQUEST["shout_text"]==""))
	{
		print("<br>\nError: Unkonwn  shout_text.<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
		return 0;
	}
	if (EmailFilter($_REQUEST["shout_text"]))
	{
		print("<br>\nError: Message should exclude emails");
		return 0;
	};
	if (BadWordsFilter($_REQUEST["shout_text"]))
	{
		print("<br>\nError: Message should exclude bad words");
		return 0;
	};

	$shout_text = addslashes($_REQUEST["shout_text"]);
	global $dbconn, $config;
	$user_id = get_user_session_id();
	if (($user_id==0)||($user_id==2))
	{
		print("<br>\nError: Login failed.");
		return -1;
	}
	else
	{
		$strSQL = "SELECT a.id from ".SHOUTS_TABLE." a ORDER BY a.date_add DESC LIMIT ".$config["recive_last_messages_number"].", 1";
		$rs = $dbconn->Execute($strSQL);
		if ($rs===false)
		{
			print("<br>\nError: Database error.<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
			return -1;
		}
		if (!$rs->EOF)
		{
			$row = $rs->GetRowAssoc(false);
			$last_id = $row["id"];
			$strSQL = "DELETE FROM ".SHOUTS_TABLE." where id<$last_id";
			$rs = $dbconn->Execute($strSQL);
			if ($rs===false)
			{
				print("<br>\nError: Database error.<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
				return -1;
			}

		}

		$post_date = date('Y-m-d H:i:s');
		//VP limit one shout per user
		$strSQL = "SELECT id FROM ".SHOUTS_TABLE." WHERE user_id='".$user_id."'";
		$id_shout = $dbconn->GetOne($strSQL);
		if($id_shout)
		{
			$strSQL = "UPDATE ".SHOUTS_TABLE." SET text='".$shout_text."', date_add='".$post_date."', status='0' WHERE id='".$id_shout."'";
		}
		else
		{
			$strSQL = "INSERT INTO ".SHOUTS_TABLE." (user_id, text, date_add, status) VALUES ($user_id, '$shout_text', '$post_date', '0')";
		}
		$rs = $dbconn->Execute($strSQL);
		if ($rs===false)
		{
			print("<br>\nError: Database error.<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
			return -1;
		}
		
		$strSQL = "SELECT id FROM ".SHOUTS_USER_STAT_TABLE." WHERE id_user='".$user_id."'";
		$id_stat = $dbconn->GetOne($strSQL);
		if ($id_stat)
		{
			$strSQL = "UPDATE ".SHOUTS_USER_STAT_TABLE." SET id_user='".$user_id."', count_mess=count_mess, date_last_mess=NOW() WHERE id='".$id_stat."'";
		}
		else
		{
			$strSQL = "INSERT INTO ".SHOUTS_USER_STAT_TABLE." (id_user, count_mess, date_last_mess) VALUES ('".$user_id."', '1', NOW())";
		}
		$rs = $dbconn->Execute($strSQL);
		if ($rs===false)
		{
			print("<br>\nError: Database error.<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
			return -1;
		}

		print("<br>\nSuccessfuly sended: true");
		recv_shouts();
	};
	return 0;
}

function open_profile()
{
	global $config;
	if ((!isset($_REQUEST["name"]))||($_REQUEST["name"]==""))
	{
		print("<br>\nError: Unkonwn  name.<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
		return 0;
	}
	$user_id=get_user_id($_REQUEST["name"]);
	echo "<script>location.href='".$config["site_root"]."/viewprofile.php?id=".$user_id."'</script>";
	return 0;
}
?>