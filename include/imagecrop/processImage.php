<?php

/**
* Image crop functions
*
* @package DatingPro
* @subpackage Include files
**/

// required params: imageName

include dirname(__FILE__)."/../config.php";
include dirname(__FILE__)."/../../common.php";

header("Content-Type: text/plain");

$originalDirectory = dirname(__FILE__)."/../../uploades/photos/";
$activeDirectory = dirname(__FILE__)."/../../uploades/photos/thumb_";
$editDirectory = dirname(__FILE__)."/../../uploades/photos/edit_";
$imageName = $_REQUEST["imageName"];
$action = $_REQUEST["action"];

if(!file_exists($editDirectory.$imageName) && file_exists($originalDirectory.$imageName))
	copy($originalDirectory.$imageName, $editDirectory.$imageName);

if(empty($imageName) ||
	!file_exists($originalDirectory.$imageName) ||
	!file_exists($activeDirectory.$imageName) ||
	!file_exists($editDirectory.$imageName)) { echo "{imageFound:false}"; exit; }

$rs = $dbconn->Execute("select name, value from ".SETTINGS_TABLE." where name in ('thumb_max_width','thumb_max_height')");
while(!$rs->EOF){
	$settings[$rs->fields[0]] = $rs->fields[1];
	$rs->MoveNext();
}

switch($action){

	case "viewOriginal":
		copy($originalDirectory.$imageName, $editDirectory.$imageName);
		break;

	case "viewActive":
		copy($activeDirectory.$imageName, $editDirectory.$imageName);
		break;

	case "save":
		list($w, $h) = getimagesize($editDirectory.$imageName);
		if ($w > $settings["thumb_max_width"] || $h > $settings["thumb_max_height"]) { exit; }
		copy($editDirectory.$imageName, $activeDirectory.$imageName);
		break;

	case "resize": // additional required params: w, h
		$out_w = $_REQUEST["w"];
		$out_h = $_REQUEST["h"];
		if (!is_numeric($out_w) || $out_w < 1 || $out_w > 2000 || !is_numeric($out_h) || $out_h < 1 || $out_h > 2000) { exit; }
		list($in_w, $in_h) = getimagesize($editDirectory.$imageName);
		$in = ImageCreateFromType($editDirectory.$imageName);
		$out = imagecreatetruecolor($out_w, $out_h);
		imagecopyresampled($out, $in, 0, 0, 0, 0, $out_w, $out_h, $in_w, $in_h);
		ImageType($out, $editDirectory.$imageName);
		imagedestroy($in);
		imagedestroy($out);
		break;

	case "rotate": // additional required params: degrees (90, 180 or 270)
		$degrees = $_REQUEST["degrees"];
		if (($degrees != 90 && $degrees != 180 && $degrees != 270)) { exit; }
		$in = ImageCreateFromType($editDirectory.$imageName);
		if ($degrees == 180){
			$out = imagerotate($in, $degrees, 180);
		}else{ // 90 or 270
			$x = imagesx($in);
			$y = imagesy ($in);
			$max = max($x, $y);

			$square = imagecreatetruecolor($max, $max);
			imagecopy($square, $in, 0, 0, 0, 0, $x, $y);
			$square = imageRotate($square, $degrees, 0);

			$out = imagecreatetruecolor($y, $x);
			if ($degrees == 90) {
				imagecopy($out, $square, 0, 0, 0, $max - $x, $y, $x);
			} elseif ($degrees == 270) {
				imagecopy($out, $square, 0, 0, $max - $y, 0, $y, $x);
			}
			imagedestroy($square);
		}
		ImageType($out, $editDirectory.$imageName);
		imagedestroy($in);
		imagedestroy($out);
		break;

	case "crop": // additional required params: x, y, w, h
		$x = $_REQUEST["x"];
		$y = $_REQUEST["y"];
		$w = $_REQUEST["w"];
		$h = $_REQUEST["h"];
		if (!is_numeric($x) || !is_numeric($y) || !is_numeric($w) || !is_numeric($h)) { exit; }
		$in = ImageCreateFromType($editDirectory.$imageName);
		$out = imagecreatetruecolor($w, $h);
		imagecopyresampled($out, $in, 0, 0, $x, $y, $w, $h, $w, $h);
		ImageType($out, $editDirectory.$imageName);
		imagedestroy($in);
		imagedestroy($out);
		break;

}

list($w, $h) = getimagesize($editDirectory.$imageName);
echo '{imageFound:true,imageName:"'.$imageName.'",w:'.$w.',h:'.$h.',max_w:'.$settings["thumb_max_width"].',max_h:'.$settings["thumb_max_height"].'}';
exit;

function ImageCreateFromType($image_path) {
	$image_info = GetImageSize($image_path);
	$image_type = $image_info[2];
	switch($image_type){
		case "1" :
			$image_obj = @ImageCreateFromGif($image_path);
			break;	/// GIF
		case "2" :
			$image_obj = @imagecreatefromjpeg($image_path);
			break;	/// JPG
		case "3" :
			$image_obj = @imagecreatefrompng($image_path);
			break;	/// PNG
		case "6" :
			$image_obj = @imagecreatefromwbmp($image_path);
			break;	/// BMP
	}
	return $image_obj;
}

function ImageType($image_obj,$image_path) {
	$image_info = GetImageSize($image_path);
	$image_type = $image_info[2];
	switch($image_type){
		case "1" :
			ImageGif($image_obj, $image_path, 100);
			break;	/// GIF
		case "2" :
			ImageJpeg($image_obj, $image_path, 100);
			break;	/// JPG
		case "3" :
			ImagePng($image_obj, $image_path, 100);
			break;	/// PNG
		case "6" :
			ImageWbmp($image_obj, $image_path, 100);
			break;	/// BMP
	}
	return;
}
?>