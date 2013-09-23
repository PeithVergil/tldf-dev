<?php

/**
* Calendar-functions for events module and blog module
*
* @package DatingPro
* @subpackage Include files
**/

function GetEvent($date, $id_country = 0, $id_region = 0, $id_city = 0, $type = 0, $my_events = false){
	global $lang, $config, $config_index, $smarty, $dbconn, $user;

	$filter = $table = "";
	if ($id_country)$filter .= " AND a.id_country = ".$id_country;
	if ($id_region)	$filter .= " AND a.id_region = ".$id_region;
	if ($id_city)	$filter .= " AND a.id_city = ".$id_city;
	if ($type)		$filter .= " AND a.type = ".$type;

	if ($my_events) {
		$table = ", ".EVENT_USERS_TABLE." b ";
		$filter .= " AND a.id = b.id_event AND b.id_user = ".$user[ AUTH_ID_USER ];
	}

	$strSQL = "SELECT a.id, a.event_name, a.id_creator FROM ".EVENT_DESCR_TABLE." a".$table."
		WHERE (	(a.periodicity = 'none' AND DATE_FORMAT(a.start_date,'%Y-%m-%d') = DATE_FORMAT('".$date."','%Y-%m-%d'))
			OR (a.periodicity = 'daily' AND (DATE_FORMAT('".$date."','%Y-%m-%d') <= a.die_date OR a.die_date = '0000-00-00') AND DATE_FORMAT(a.start_date,'%Y-%m-%d') <= DATE_FORMAT('".$date."','%Y-%m-%d'))
			OR (a.periodicity = 'weekly' AND DAYOFWEEK(a.start_date) = DAYOFWEEK('".$date."') AND (DATE_FORMAT('".$date."','%Y-%m-%d') <= a.die_date OR a.die_date = '0000-00-00') AND DATE_FORMAT(a.start_date,'%Y-%m-%d') <= DATE_FORMAT('".$date."','%Y-%m-%d'))
			OR (a.periodicity = 'monthly' AND DAYOFMONTH(a.start_date) = DAYOFMONTH('".$date."') AND (DATE_FORMAT('".$date."','%Y-%m-%d') <= a.die_date OR a.die_date = '0000-00-00') AND DATE_FORMAT(a.start_date,'%Y-%m-%d') <= DATE_FORMAT('".$date."','%Y-%m-%d'))
			OR (a.periodicity = 'yearly' AND MONTH(a.start_date) = MONTH('".$date."') AND DAYOFMONTH(a.start_date) = DAYOFMONTH('".$date."') AND (DATE_FORMAT('".$date."','%Y-%m-%d') <= a.die_date OR a.die_date = '0000-00-00') AND DATE_FORMAT(a.start_date,'%Y-%m-%d') <= DATE_FORMAT('".$date."','%Y-%m-%d'))	)".$filter;
	$rs = $dbconn->Execute($strSQL);
	$num_records = $rs->RowCount();
	if($num_records>0){
		$event["event_count"] = $num_records;
		$i = 0;
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$event["event"][$i]["id_event"] = $row["id"];
			if (strlen($row["event_name"])<10) {
				$event["event"][$i]["name"] = stripslashes($row["event_name"]);
			} else {
				$event["event"][$i]["name"] = stripslashes(substr($row["event_name"],0,10)."...");
			}
			$event["event"][$i]["full_name"] = stripslashes($row["event_name"]);
			$i++;
			$rs->MoveNext();
		}
		return $event;
	} else {
		return '';
	}
}

function GetBlogPost($date)
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;

	$strSQL = " SELECT DISTINCT post.id, post.title
				FROM ".BLOG_POST_TABLE." post, ".BLOG_PROFILE_TABLE." profile
				WHERE profile.id=post.id_profile AND profile.id_user='".$user[ AUTH_ID_USER ]."' AND DATE_FORMAT(post.creation_date, '%Y-%m-%d')=DATE_FORMAT('".$date."','%Y-%m-%d')
				GROUP BY post.id
				LIMIT 0,3 ";
	$rs = $dbconn->Execute($strSQL);

	if($rs->RowCount()>0){
		$i = 0;
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$blog_post[$i]["id_post"] = $row["id"];
			$blog_post[$i]["post_link"] = "blog.php?sel=view_comments&id_post=".$blog_post[$i]["id_post"];
			if (strlen($row["title"])<10) {
				$blog_post[$i]["title"] = stripslashes($row["title"]);
			} else {
				$blog_post[$i]["title"] = stripslashes(substr($row["title"],0,10)."...");
			}
			$i++;
			$rs->MoveNext();
		}
		return $blog_post;
	} else {
		return '';
	}
}


?>