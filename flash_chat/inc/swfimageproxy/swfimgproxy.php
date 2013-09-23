<?php
//*************************************************
//
// swfImageProxy v0.4
// 
// Author: Mario Klingemann <mario@quasimondo.com>
// http://www.quasimondo.com
//
// Description: This script allows you to download
// various image formats from any URL into swf files.
// This allows you to circumvent the swf internal
// sandbox which limits dowloads to the domain from
// which the swf was launched and you can access
// formats that are not directly supported by the
// Flash player as the will be converted to either
// JPEG or SWF.
// 
// Currently the following formats are supported:
// JPEG, progressive JPEG, GIF, PNG, SWF
//
// Basic Usage: swfimgproxy.php?url=[url of the image]
// 
// Optional Arguments:
//
// &quality=[0-100] optional JPEG quality if no png2swf 
// support is used.
//
// &framerate=[0-99] optional framerate for animated GIFs
//
//
// You will need the following libraries on your
// server or compiled into PHP
//
// GD support for PHP. IF you want GIF support you
// will need a minimum version 2.0.28 
//
// png2swf which is part of the free SWF Tools package
// by Rainer Böhme and Matthias Kramm and is available
// on http://www.quiss.org/swftools
// For this purpose I have copied the png2swf file directly
// into the same folder as swfimgproxy.php. 
//
// ImageMagick (http://www.imagemagick.org) is needed 
// if you want to convert animated GIFs into swfs.
// 
// Known Bugs:
// 
// - Animated GIFs that use a reduced canvas on successive frames
// will be jumping around. 
//
// - Variable delays in animated GIFs are ignored.
// 
//
// History:
//
// v0.1 first release
// v0.2 fixed bug with saving transparent PNGs from GD
// v0.3 fixed bug with JPEG quality always 0 when using GD-only
// v0.4 added support for animated gifs by using ImageMagick
//
//*************************************************

require("swfimgproxy.inc.php");

$PATH_TO_IMGPROXY=""; 

$USE_JPEGTRAN=false;  // set this to false if you do not have access to jpegtran
$PATH_TO_JPEGTRAN="";

$USE_PNG2SWF=false; // set this to false if you do not have access to png2swf
$PATH_TO_PNG2SWF="./";

$USE_IMAGICK=false; // set this to false if you do not have access to Imagemagick
$PATH_TO_IMAGICK="";

if (!isset($_GET["url"])){
	returnError("No URL supplied");
}
if (strToLower(substr($_GET["url"],0,7))!="http://"){
	returnError("Wrong URL supplied");
}
if (!isset($_GET["quality"])){
	$jpegQuality=75;
} else {
	$jpegQuality=$_GET["quality"];
}
if (!isset($_GET["framerate"])){
	$framerate=31;
} else {
	$framerate=$_GET["framerate"];
}


$img_url=@parse_url($_GET["url"]);
				
$file = new GetWebObject($img_url["host"], 80, $img_url["path"],true);
if (!$file->fetch()){
	returnError($file->get_error());	
}
$head = $file->get_header();
$imgFormat=$head["content-type"];

switch ($imgFormat){
	case "image/jpeg":
		$imgname=createTempImgfile($file->get_content());
		if (!$USE_JPEGTRAN){
			$im = @imageCreateFromJPEG ($imgname);
			returnJPEG($im,$jpegQuality);
		} else {
			exec ($PATH_TO_JPEGTRAN."jpegtran -o ".$imgname." > ".$imgname.".jpg");
			if (!file_exists($imgname.".jpg")){
				unlink($imgname);
				returnError("Could not create JPEG");
			}
			header("Content-type: img/jpeg");
			$fp=fopen($imgname.".jpg","rb"); 
			fpassthru($fp); 
			fclose($fp);
			unlink($imgname);
			unlink($imgname.".jpg");
		}
	break;
	
	case "image/png":
		$imgname=@createTempImgfile($file->get_content());
		if(!function_exists('imagecreatefrompng')){ 
                	returnError('Your version of GD does not have support for the PNG image format.'); 
		}
		$im = @imageCreateFromPNG ($imgname);
		imagealphablending($im, false); 
		imagesavealpha($im,true);
		if ($USE_PNG2SWF && $im){
			imageinterlace ( $im, 0);
			imagePNG($im,$imgname);
			imagedestroy($im);	
			exec ($PATH_TO_PNG2SWF."png2swf -o ".$imgname.".swf ".$imgname);
			if (!file_exists($imgname.".swf")){
				unlink($imgname);
				returnError("Could not create SWF FROM PNG");
			}
			header("Content-type: application/x-shockwave-flash");
			$fp=fopen($imgname.".swf","rb"); 
			fpassthru($fp); 
			fclose($fp);
			unlink($imgname);
			unlink($imgname.".swf");
		} else {
			returnJPEG($im,$jpegQuality);
		}
	break;
	
	case "image/gif":
		$imgname=createTempImgfile($file->get_content());
		$gifFrames=0;
		if ($USE_IMAGICK && $USE_PNG2SWF){
			exec($PATH_TO_IMAGICK."identify ".$imgname." > ".$imgname.".txt");
			
			$lines=file($imgname.".txt");
			unlink($imgname.".txt");
			$gifFrames=count($lines);
		}	
		if ($gifFrames==0){
			if(!function_exists('imagecreatefromgif')){ 
	                	returnError('Your version of GD does not have support for the GIF image format.'); 
			}
			$im = @imageCreateFromGIF($imgname);
			imagealphablending($im, false); 
			imagesavealpha($im,true);
			if ($USE_PNG2SWF && $im){
				imageinterlace ( $im, 0);
				imagePNG($im,$imgname);
				imagedestroy($im);	
				exec ($PATH_TO_PNG2SWF."png2swf -o ".$imgname.".swf ".$imgname);
				if (!file_exists($imgname.".swf")){
					unlink($imgname);
					returnError("Could not create SWF FROM GIF");
				}
				header("Content-type: application/x-shockwave-flash");
				$fp=fopen($imgname.".swf","rb"); 
				fpassthru($fp); 
				fclose($fp);
				unlink($imgname);
				unlink($imgname.".swf");
			} else {
				returnJPEG($im,$jpegQuality);
			}
		} else {
			
			exec($PATH_TO_IMAGICK.'convert '.$imgname.' '.$imgname.'%03d.png');
			
			$inputpngs="";
			
			for ($i=0;$i<$gifFrames;$i++){
				$inputpngs.=$imgname.sprintf("%03d",$i).".png ";	
			}
			
			exec ($PATH_TO_PNG2SWF."png2swf -r ".$framerate." -o ".$imgname.".swf ".$inputpngs);
			if (!file_exists($imgname.".swf")){
				unlink($imgname);
				for ($i=0;$i<$gifFrames;$i++){
					unlink($imgname.sprintf("%03d",$i).".png");	
				}
				returnError("Could not create SWF FROM GIF");
			}
			header("Content-type: application/x-shockwave-flash");
			$fp=fopen($imgname.".swf","rb"); 
			fpassthru($fp); 
			fclose($fp);
			for ($i=0;$i<$gifFrames;$i++){
				unlink($imgname.sprintf("%03d",$i).".png");	
			}
			unlink($imgname);
			unlink($imgname.".swf");
		}
	break;
	
	case "application/x-shockwave-flash":
		header("Content-type: application/x-shockwave-flash");
		echo $file->get_content();
	break;
	
	default:
		returnError("Unknown image format: ".$imgFormat);
	break;
}
?>