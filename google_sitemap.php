<?php

$include = 1;
include("map.php");
ini_set("display_errors", "0");
$to_google_map = array();
$i = 0;
$to_google_map["link"]= array();
foreach ($map_links as $val_arr) {
	if ($val_arr["is_active"] == 1) {
		if ($val_arr["link"] == $config["site_root"]."/index.php") {
			$to_google_map["priority"][$i] = 1;
		}
		if (isset($to_google_map["link"]) && is_array($to_google_map["link"]) && !(in_array(htmlspecialchars($config["server"].$val_arr["link"]), $to_google_map["link"]))) {
			$to_google_map["link"][$i] = htmlspecialchars($config["server"].$val_arr["link"]);
			$i++;
		}
	}
}

$output = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
$output .= "\t<urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\"\r\n
	xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\r\n
	xsi:schemaLocation=\"http://www.google.com/schemas/sitemap/0.84\r\n
	http://www.google.com/schemas/sitemap/0.84/sitemap.xsd\">\r\n";

foreach ($to_google_map["link"] as $key=>$val) {
	$output .= "\t\t<url>\r\n";
	$output .= "\t\t\t<loc>".$val."</loc>\r\n";
	if (isset($to_google_map["priority"][$key])) {
		$output .= "\t\t\t<priority>".$to_google_map["priority"][$key]."</priority>\r\n";
	}
	$output .= "\t\t</url>\r\n";
}
$output .= "\t</urlset>";

header ("Content-Type: text/xml; charset=utf-8");
echo $output;

?>