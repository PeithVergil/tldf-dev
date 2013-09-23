<?php

/**
* Get Image for crop
*
* @package DatingPro
* @subpackage Include files
**/

// required param: imageName

if(!isset($_GET["imageName"]) || empty($_GET["imageName"])) { exit; }

$editDirectory = dirname(__FILE__)."/../../uploades/photos/edit_";
$imageName = $_GET["imageName"];

$image_info = GetImageSize($editDirectory.$imageName);
$image_type = $image_info[2];

switch($image_type){
	case "1" :
		$output = @ImageCreateFromGif($editDirectory.$imageName);
		ImageGif( $output, "", 100 );
		break;	/// GIF
	case "2" :
		$output = @imagecreatefromjpeg($editDirectory.$imageName);
		ImageJpeg( $output, "", 100 );
		break;	/// JPG
	case "3" :
		$output = @imagecreatefrompng($editDirectory.$imageName);
		ImagePng( $output, "", 100 );
		break;	/// PNG
	case "6" :
		$output = @imagecreatefromwbmp($editDirectory.$imageName);
		ImageWbmp( $output, "", 100 );
		break;	/// BMP
}

imagedestroy($output);

?>