<?php
/**
* returns RSS of selected user
*
* @package DatingPro
* @subpackage User Mode
**/
include "./include/config.php";
include "./common.php";
include "./include/functions_index.php";
include "./include/class.rss_genesis.php";

if ($_GET["user_name"])
	$user_name = $_GET["user_name"];
else
	exit;

$language_code = $_GET["language_code"] ? intval($_GET["language_code"]) : $config["default_lang"];


if (isset($_GET['rssversion'])) {
	$rss = new rssGenesis($config, $_GET['rssversion']);
} else {
	$rss = new rssGenesis($config);
}


// CHANNEL
$rss->setChannel (
	$config, // config
	str_replace("[user]", $user_name, $lang["rss"]["chanel_title"]), // Title
	$config["server"].$config["site_root"]."/viewprofile.php?login=".$user_name."&language_code=".$language_code, // Link
	$lang["rss"]["chanel_description"], // Description
	$lang_code, // Language
	$config["server"].$config["site_root"], // Copyright
	null, // Managing Editor
	null, // WebMaster
	null, // Rating
	"auto", // PubDate
	"auto", // Last Build Date
	$lang["rss"]["chanel_category"], // Category
	null, // Docs
	null, // Time to Live
	null, // Skip Days
	null // Skip Hours
);

$rs = $dbconn->Execute("select id from ".USERS_TABLE." where login = '".$user_name."'");
if ($rs->fields[0]) $user_id = $rs->fields[0];
else exit;

$settings = GetSiteSettings(array("icon_male_default","icon_female_default","icons_folder","photos_folder","audio_folder","video_folder"));

$default_photos['1'] = $settings['icon_male_default'];
$default_photos['2'] = $settings['icon_female_default'];


//// Uploads


/////////// icon
$rs = $dbconn->Execute("select icon_path, comment from ".USERS_TABLE." where id = '".$user_id."'");
$file_name = $rs->fields[0];
$file_path = $config["site_path"].$settings["icons_folder"]."/".$file_name;

if(file_exists($file_path) && strlen($file_name)>0) {
	$file_link = $config["server"].$config["site_root"].$settings["icons_folder"]."/".$file_name;
	$comment = nl2br(stripslashes($rs->fields[1]));
	$content = "<p><img src='".$file_link."' alt='' title='' hspace='10' vspace='0' border='0' align='left'/>".$comment."</p>";

	$rss->addItem 	(
		$lang["rss"]["icon_item_title"], // Title
		$config["server"].$config["site_root"]."/viewprofile.php?login=".$user_name."&amp;sel=4&amp;language_code=".$language_code, // Link
		$content, // Description
		"auto", //Publication Date
		$lang["rss"]["icon_item_category"], // Category
		$file_name //unique id for feed readers
	);
}

/////////// uploads
$i = 0;
$rs = $dbconn->Execute("Select upload_path, upload_type, user_comment from ".USER_UPLOAD_TABLE." where id_user='".$user_id."' and status='1' order by upload_type, id");
while(!$rs->EOF){
	$row = $rs->GetRowAssoc(false);
	$db_upload[$row["upload_type"]][$i]["user_comment"] = nl2br(stripslashes($row["user_comment"]));
	$db_upload[$row["upload_type"]][$i]["file_path"] = $row["upload_path"];
	$rs->MoveNext(); $i++;
}

/////////// photo
if(isset($db_upload['f']) && count($db_upload['f'])){
	foreach($db_upload['f'] as $photo){
		$file_name = $photo["file_path"];
		$file_path = $config["site_path"].$settings["photos_folder"]."/thumb_".$file_name;

		if(file_exists($file_path) && strlen($file_name)>0)  {
			$file_link = $config["server"].$config["site_root"].$settings["photos_folder"]."/thumb_".$file_name;
			$comment = $photo["user_comment"];
			$content = "<p><img src='".$file_link."' alt='' title='' hspace='10' vspace='0' border='0' align='left'/>".$comment."</p>";

			$rss->addItem 	(
				$lang["rss"]["photo_item_title"], // Title
				$config["server"].$config["site_root"]."/viewprofile.php?login=".$user_name."&amp;sel=4&amp;language_code=".$language_code, // Link
				$content, // Description
				"auto", //Publication Date
				$lang["rss"]["photo_item_category"], // Category
				$file_name //unique id for feed readers
			);
		}
	}
}

/////////// audio
if(isset($db_upload['a']) && count($db_upload['a'])){
	foreach($db_upload['a'] as $audio){
		$file_name = $audio["file_path"];
		$file_path = $config["site_path"].$settings["audio_folder"]."/".$file_name;

		if(file_exists($file_path) && strlen($file_name)>0){
			$comment = $audio["user_comment"];
			$content = "<p>".$comment."</p>";

			$rss->addItem 	(
				$lang["rss"]["audio_item_title"], // Title
				$config["server"].$config["site_root"]."/viewprofile.php?login=".$user_name."&amp;sel=4&amp;language_code=".$language_code, // Link
				$content, // Description
				"auto", //Publication Date
				$lang["rss"]["audio_item_category"], // Category
				$file_name //unique id for feed readers
			);
		}
	}
}

/////////// video
if(isset($db_upload['v']) && count($db_upload['v'])){
	foreach($db_upload['v'] as $video){
		$file_name = $video["file_path"];
		$file_path = $config["site_path"].$settings["video_folder"]."/".$file_name;

		if(file_exists($file_path) && strlen($file_name)>0){
			$comment = $video["user_comment"];
			$content = "<p>".$comment."</p>";

			$rss->addItem 	(
				$lang["rss"]["video_item_title"], // Title
				$config["server"].$config["site_root"]."/viewprofile.php?login=".$user_name."&amp;sel=4&amp;language_code=".$language_code, // Link
				$content, // Description
				"auto", //Publication Date
				$lang["rss"]["video_item_category"], // Category
				$file_name //unique id for feed readers
			);
		}
	}
}

//// Comments

$strSQL = "select a.id, a.message, DATE_FORMAT(a.comment_date,'".$config["date_format"]."') as date, a.id_voter, b.login, b.gender, b.date_birthday, b.icon_path, c.name as country, d.name as city, r.name as region
		from ".USER_COMMENT_TABLE." a, ".USERS_TABLE." b
		left join ".COUNTRY_SPR_TABLE." c on c.id=b.id_country
		left join ".CITY_SPR_TABLE." d on d.id=b.id_city
		left join ".REGION_SPR_TABLE." r on r.id=b.id_region
		where a.id_user='".$user_id."' and a.id_voter=b.id group by a.id order by a.id desc";
$rs = $dbconn->Execute($strSQL);
while(!$rs->EOF){
	$row = $rs->GetRowAssoc(false);

	$data["profile_link"] = $config["server"].$config["site_root"]."/viewprofile.php?id=".$row["id_voter"];
	$icon_path = $row["icon_path"]?$row["icon_path"]:$default_photos[$row["gender"]];
	if($icon_path && file_exists($config["site_path"].$settings["icons_folder"]."/".$icon_path))
		$data["icon"] = $config["server"].$config["site_root"].$settings["icons_folder"]."/".$icon_path;
	$data["name"] = $row["login"];
	$data["age"] = AgeFromBDate($row["date_birthday"]);
	$data["location"] = "";
	if ($row["city"]) $data["location"] .= stripslashes($row["city"]).", ";
	if ($row["region"]) $data["location"] .= stripslashes($row["region"]).", ";
	if ($row["country"]) $data["location"] .= stripslashes($row["country"]);

	$data["message"] = strip_tags(stripslashes(nl2br($row["message"])));
	$data["date"] = $row["date"];

	$content = "
		<table width='100%' border='0' cellpadding='5' cellspacing='0'>
		<tr valign='middle'>
			<td height='100px' width='80px' align='center' valign='top'><a href='".$data["profile_link"]."'><img src='".$data["icon"]."' alt=''></a></td>
			<td height='100px' width='100%' valign='top'>
					<div style='margin-top: 5px'>
						<a href='".$data["profile_link"]."'><b>".$data["name"]."</b></a>&nbsp;
						<b>".$data["age"]." ".$lang["home_page"]["ans"]."</b>&nbsp;<br>
						".$data["location"]."
					</div>
					<div style='margin-top: 10px'>
						".$data["message"]."&nbsp;
					</div>
					<div style='margin-top: 10px'>
						<i>".$data["date"]."</i>&nbsp;
					</div>
			</td>
		</tr>
		</table>
	";

	$rss->addItem 	(
		$lang["rss"]["comment_item_title"], // Title
		$config["server"].$config["site_root"]."/viewprofile.php?login=".$user_name."&amp;sel=5&amp;language_code=".$language_code, // Link
		$content, // Description
		"auto", //Publication Date
		$lang["rss"]["comment_item_category"], // Category
		"comment_".$row["id"] //unique id for feed readers
	);

	$rs->MoveNext();
}

//// Rating

$rs = $dbconn->Execute("select avg(estimation),count(estimation) from ".USER_RATING_TABLE." where id_user='".$user_id."'");
$data["current_rating"] = round($rs->fields[0],2);
$data["all_vote"] = $rs->fields[1];

$strSQL = "select a.estimation, DATE_FORMAT(a.voting_date,'".$config["date_format"]."') as date, a.id_user, a.id_voter, b.login, b.gender, b.date_birthday, b.icon_path, c.name as country, d.name as city, r.name as region
		from ".USER_RATING_TABLE." a, ".USERS_TABLE." b
		left join ".COUNTRY_SPR_TABLE." c on c.id=b.id_country
		left join ".CITY_SPR_TABLE." d on d.id=b.id_city
		left join ".REGION_SPR_TABLE." r on r.id=b.id_region
		where a.id_user='".$user_id."' and a.id_voter=b.id";
$rs = $dbconn->Execute($strSQL);
while(!$rs->EOF){
	$row = $rs->GetRowAssoc(false);

	$data["profile_link"] = $config["server"].$config["site_root"]."/viewprofile.php?id=".$row["id_voter"];
	$icon_path = $row["icon_path"]?$row["icon_path"]:$default_photos[$row["gender"]];
	if($icon_path && file_exists($config["site_path"].$settings["icons_folder"]."/".$icon_path))
		$data["icon"] = $config["server"].$config["site_root"].$settings["icons_folder"]."/".$icon_path;
	$data["name"] = $row["login"];
	$data["age"] = AgeFromBDate($row["date_birthday"]);
	$data["location"] = "";
	if ($row["city"]) $data["location"] .= stripslashes($row["city"]).", ";
	if ($row["region"]) $data["location"] .= stripslashes($row["region"]).", ";
	if ($row["country"]) $data["location"] .= stripslashes($row["country"]);

	$data["vote"] = $row["estimation"];
	$data["date"] = $row["date"];

	$content = "
		".$lang["profile"]["current_rating"].": <b>".$data["current_rating"]."</b><br>
		".$lang["profile"]["all_vote"].": <b>".$data["all_vote"]."</b><br>
		<br>
		<table width='100%' border='0' cellpadding='5' cellspacing='0'>
		<tr valign='middle'>
			<td height='100px' width='80px' align='center' valign='top'><a href='".$data["profile_link"]."'><img src='".$data["icon"]."' alt=''></a></td>
			<td height='100px' width='100%' valign='top'>
					<div style='margin-top: 5px'>
						<a href='".$data["profile_link"]."'><b>".$data["name"]."</b></a>&nbsp;
						<b>".$data["age"]." ".$lang["home_page"]["ans"]."</b>&nbsp;<br>
						".$data["location"]."
					</div>
					<div style='margin-top: 10px'>
						".$lang["profile"]["vote"].": <b>".$data["vote"]."</b>&nbsp;
					</div>
					<div style='margin-top: 10px'>
						<i>".$data["date"]."</i>&nbsp;
					</div>
			</td>
		</tr>
		</table>
	";

	$rss->addItem 	(
		$lang["rss"]["rating_item_title"], // Title
		$config["server"].$config["site_root"]."/viewprofile.php?login=".$user_name."&amp;sel=5&amp;language_code=".$language_code, // Link
		$content, // Description
		"auto", //Publication Date
		$lang["rss"]["rating_item_category"], // Category
		"rating_".$row["id_user"]."_".$row["id_voter"] //unique id for feed readers
	);

	$rs->MoveNext();
}

$rss->generateFeed ($config);

exit;

?>