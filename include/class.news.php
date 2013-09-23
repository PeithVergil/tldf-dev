<?php

/**
* News class (read RSS feeds, update site news)
*
* @package DatingPro
* @subpackage Include files
**/

include dirname(__FILE__)."/Snoopy.class.php";

global $rss2array_globals;
global $item_limit;
global $snoopy;

$snoopy = new Snoopy;
$snoopy->read_timeout = 5;

function rss2array($url, $news_limit=false)
{
	global $rss2array_globals;
	global $item_limit;
	global $snoopy;

	$rss2array_globals = array();
	$item_limit = $news_limit;

	if(preg_match("/^http:\/\/([^\/]+)(.*)$/", $url, $matches))
	{
		$snoopy->fetch($url);

		$xml = @$snoopy->results;
		$header = @$snoopy->response_code;
		$rss_timeout = @$snoopy->timed_out;
		$status = @$snoopy->status;
		$host = @$snoopy->host;

		if (!$rss_timeout)
		{
			if (preg_match("/200/",$header))
			{
				$xml_parser = xml_parser_create();

				xml_set_element_handler($xml_parser, "startElement", "endElement");

				xml_set_character_data_handler($xml_parser, "characterData");

				xml_parse($xml_parser, trim($xml), true) or $rss2array_globals['errors'][] = xml_error_string(xml_get_error_code($xml_parser)) . " at line " . xml_get_current_line_number($xml_parser);

				xml_parser_free($xml_parser);
			}
			else
			{
				$rss2array_globals['errors'][] = "Can't get feed: HTTP status code $status";
			}
		}
		else
		{
			$rss2array_globals['errors'][] = "Can't connect to $host";
		}
	}
	else
	{
		$rss2array_globals['errors'][] = "Invalid url: $url";
	}

	unset($rss2array_globals['channel_title']);
	unset($rss2array_globals['channel_link']);
	unset($rss2array_globals['channel_description']);

	unset($rss2array_globals['image_title']);
	unset($rss2array_globals['image_link']);
	unset($rss2array_globals['image_url']);
	unset($rss2array_globals['image_width']);
	unset($rss2array_globals['image_height']);

	unset($rss2array_globals['inside_rdf']);
	unset($rss2array_globals['inside_rss']);
	unset($rss2array_globals['inside_channel']);
	unset($rss2array_globals['inside_item']);
	unset($rss2array_globals['inside_image']);

	unset($rss2array_globals['current_tag']);
	unset($rss2array_globals['current_title']);
	unset($rss2array_globals['current_link']);
	unset($rss2array_globals['current_description']);
	unset($rss2array_globals['current_content']);
	unset($rss2array_globals['current_pubdate']);

	return $rss2array_globals;
}

function startElement($parser, $name, $attrs)
{
	global $rss2array_globals;
	global $item_limit;

	$rss2array_globals['current_tag'] = $name;

	if ($name == "RSS")
	{
		$rss2array_globals['inside_rss'] = true;
	}

	elseif ($name == "RDF:RDF")
	{
		$rss2array_globals['inside_rdf'] = true;
	}

	elseif ($name == "CHANNEL")
	{
		$rss2array_globals['inside_channel'] = true;
		$rss2array_globals['channel_title'] = "";

	}

	elseif (($rss2array_globals['inside_rss'] and $rss2array_globals['inside_channel']) or $rss2array_globals['inside_rdf'])
	{
		if ($name == "ITEM")
		{
			$rss2array_globals['inside_item'] = true;
		}

		elseif ($name == "IMAGE")
		{
			$rss2array_globals['inside_image'] = true;
		}
	}
}

function characterData($parser, $data)
{
	global $rss2array_globals;
	global $item_limit;

	if (isset($rss2array_globals['inside_item']) && $rss2array_globals['inside_item'])
	{
		switch($rss2array_globals['current_tag'])
		{
			case "TITLE":
				if (isset($rss2array_globals['current_title']))
					$rss2array_globals['current_title'] .= $data;
				else
					$rss2array_globals['current_title'] = $data;
				break;
			case "DESCRIPTION":
				if (isset($rss2array_globals['current_description']))
					$rss2array_globals['current_description'] .= $data;
				else
					$rss2array_globals['current_description'] = $data;
				break;
			case "CONTENT:ENCODED":
				if (isset($rss2array_globals['current_content']))
					$rss2array_globals['current_content'] .= $data;
				else
					$rss2array_globals['current_content'] = $data;
				break;
			case "LINK":
				if (isset($rss2array_globals['current_link']))
					$rss2array_globals['current_link'] .= $data;
				else
					$rss2array_globals['current_link'] = $data;
				break;
			case "PUBDATE":
				if (isset($rss2array_globals['current_pubdate']))
					$rss2array_globals['current_pubdate'] .= $data;
				else
					$rss2array_globals['current_pubdate'] = $data;
				break;
		}
	}
	elseif (isset($rss2array_globals['inside_image']) && $rss2array_globals['inside_image'])
	{
		switch($rss2array_globals['current_tag'])
		{
			case "TITLE":
				$rss2array_globals['image_title'] .= $data;
				break;
			case "LINK":
				$rss2array_globals['image_link'] .= $data;
				break;
			case "URL":
				$rss2array_globals['image_url'] .= $data;
				break;
			case "WIDTH":
				$rss2array_globals['image_width'] .= $data;
				break;
			case "HEIGHT":
				$rss2array_globals['image_height'] .= $data;
				break;
		}
	}
	elseif(isset($rss2array_globals['inside_channel']) && $rss2array_globals['inside_channel'])
	{
		switch($rss2array_globals['current_tag'])
		{
			case "TITLE":
				if (isset($rss2array_globals['channel_title']))
					$rss2array_globals['channel_title'] .= $data;
				else
					$rss2array_globals['channel_title'] = $data;
				break;
			case "DESCRIPTION":
				if (isset($rss2array_globals['channel_description']))
					$rss2array_globals['channel_description'] .= $data;
				else
					$rss2array_globals['channel_description'] = $data;
				break;
			case "LINK":
				if (isset($rss2array_globals['channel_link']))
					$rss2array_globals['channel_link'] .= $data;
				else
					$rss2array_globals['channel_link'] = $data;
				break;
		}
	}
}

function endElement($parser, $name)
{
	global $rss2array_globals;
	global $item_limit;

	if ($name == "ITEM")
	{
		$item_limit = intval($item_limit);
		if (!$item_limit || (count($rss2array_globals['items']) < $item_limit))
		{
			$rss2array_globals['items'][] =
			array(
				'title' => trim($rss2array_globals['current_title']),
				'link' => trim($rss2array_globals['current_link']),
				'description' => trim($rss2array_globals['current_description']),
				'content' => trim($rss2array_globals['current_content']),
				'pubdate' => trim($rss2array_globals['current_pubdate']),
				'unixtimestamp' => intval(strtotime(trim($rss2array_globals['current_pubdate'])))
			);
		}
		$rss2array_globals['current_title'] = "";
		$rss2array_globals['current_description'] = "";
		$rss2array_globals['current_content'] = "";
		$rss2array_globals['current_link'] = "";
		$rss2array_globals['current_pubdate'] = "";

		$rss2array_globals['inside_item'] = false;
	}
	elseif ($name == "RSS")
	{
		$rss2array_globals['inside_rss'] = false;
	}
	elseif ($name == "RDF:RDF")
	{
		$rss2array_globals['inside_rdf'] = false;
	}
	elseif ($name == "CHANNEL")
	{
		$rss2array_globals['channel'][] =
		array(
			'title' => trim($rss2array_globals['channel_title']),
			'link' => trim($rss2array_globals['channel_link']),
			'description' => trim($rss2array_globals['channel_description']),
		);
		$rss2array_globals['channel_title'] = "";
		$rss2array_globals['channel_description'] = "";
		$rss2array_globals['channel_link'] = "";

		$rss2array_globals['inside_channel'] = false;
	}
	elseif($name == "IMAGE")
	{
		$rss2array_globals['image'][] =
		array
		(
			'title' => trim($rss2array_globals['image_title']),
			'link' => trim($rss2array_globals['image_link']),
			'url' => trim($rss2array_globals['image_url']),
			'width' => trim($rss2array_globals['image_width']),
			'height' => trim($rss2array_globals['image_height'])
		);
		$rss2array_globals['image_title'] = "";
		$rss2array_globals['image_link'] = "";
		$rss2array_globals['image_url'] = "";
		$rss2array_globals['image_width'] = "";
		$rss2array_globals['image_height'] = "";

		$rss2array_globals['inside_image'] = false;
	}
}

function NewsUpdater($id=0, $status=1)
{
	global $dbconn, $config;
	$where_str = "";
	
	if($status)
	{
		$where_str .= " status='1' ";
	}
	
	if($id)
	{
		if(strlen($where_str)) $where_str .= " and ";
		$where_str .= "  id='".$id."' ";
	}
	
	if (strlen($where_str)) $where_str = " WHERE ".$where_str;
	$strSQL = "SELECT id, link, max_news FROM ".NEWS_FEEDS_TABLE.$where_str;
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	while (!$rs->EOF)
	{
		$row = $rs->GetRowAssoc(false);
		$rss_array[$i]["id"] = $row["id"];
		if(intval($row["max_news"])>0)
		$row["max_news"] = intval($row["max_news"]);
		else
		$row["max_news"] = false;

		$rss_array[$i]["content"] = rss2array($row["link"], $row["max_news"]);

		if (count($rss_array[$i]["content"]["items"])>0)
		{
			$str = "DELETE FROM ".NEWS_TABLE." WHERE id_channel='".$rss_array[$i]["id"]."'";
			$dbconn->Execute($str);
			foreach($rss_array[$i]["content"]["items"] as $rss_news)
			{
				$str = "INSERT INTO ".NEWS_TABLE." (date_add, news_text, status, title, date_ts, channel_name, channel_link, news_link, id_channel) values ('".date("Y-m-d h:i:s", $rss_news["unixtimestamp"])."', '".addslashes($rss_news["content"] ? $rss_news["content"] : $rss_news["description"])."', '1', '".addslashes($rss_news["title"])."', '".$rss_news["unixtimestamp"]."', '".addslashes($rss_array[$i]["content"]["channel"][0]["title"])."', '".$rss_array[$i]["content"]["channel"][0]["link"]."', '".$rss_news["link"]."', '".$rss_array[$i]["id"]."')";
				$dbconn->Execute($str);
			}
			$str = "UPDATE ".NEWS_FEEDS_TABLE." SET date_update=NOW() WHERE id='".$rss_array[$i]["id"]."' ";
			$dbconn->Execute($str);
		}

		$rs->MoveNext();
		$i++;
	}
}

function GetLastNews($max_count = false, $page=1)
{
	global $dbconn, $config;

	if ($max_count) {
		$lim_min = ($page-1)*$max_count;
		$lim_max = $max_count;
		$limit_str = " limit ".$lim_min.", ".$lim_max;
	}
	$strSQL  =
		"SELECT id, title, news_text, DATE_FORMAT(DATE_ADD, '".$config["date_format"]."') AS date_add,
				news_link, channel_name, channel_link
		   FROM ".NEWS_TABLE."
		  WHERE status = '1'
	   ORDER BY date_ts DESC, id DESC ".$limit_str;
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	while(!$rs->EOF)
	{
		$row = $rs->GetRowAssoc(false);
		$news[$i]  =  $row;
		$news[$i]["title"] = stripslashes($row["title"]);
		$DOM = stripslashes($row["news_text"]);
		
		$doc = new DOMDocument();
		@$doc->loadHTML($DOM);
		
		$img = $doc->getElementsByTagName('img')->item(0);
		$imgOfText = $img->getAttribute('src');
		
		$news[$i]["image"] = $imgOfText;
		
		$news1 = stripslashes($news[$i]["news_text"]);//$string1;

		
		$news[$i]["news_text"] = strip_tags($news1, '<img[^>]*>');
		
	
		
		$rs->MoveNext();
		$i++;
	}
	
	return $news;
}

function GetNewsReadLink($id_news)
{
	global $dbconn, $config, $config_index;

	$strSQL  = "SELECT id FROM ".NEWS_TABLE." WHERE status='1' ORDER BY date_ts DESC, id DESC";
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$links = array();
	while(!$rs->EOF)
	{
		$row = $rs->GetRowAssoc(false);
		$page = floor($i/$config_index["news_numpage"])+1;
		$links[$row["id"]] = $config["server"].$config["site_root"]."/news.php?page=".$page."#".$row["id"];
		$rs->MoveNext();
		$i++;
	}
	return $links[$id_news];
}

?>