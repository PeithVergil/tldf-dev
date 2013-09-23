<?php
include_once "functions_common.php";

//File types
define('FT_IMAGE', 1);
define('FT_AUDIO', 2);
define('FT_VIDEO', 3);
define('FT_FLASH', 4);
define('FT_OTHER', 5);

//Upload types
define('UT_ADO_IMAGES', 2);
define('UT_ADO_AUDIO', 3);
define('UT_ADO_VIDEO', 4);

if (!isset($settings)) {
	$settings = array();
}

$settings += GetSiteSettings(array('photos_folder', 'audio_folder', 'video_folder'));

//Supported upload types
$upload_config['valid_types'][FT_IMAGE] = array("image/jpeg", "image/pjpeg", "image/gif", "image/tiff", "image/png", "image/x-png");
$upload_config['valid_types'][FT_AUDIO] = array("audio/mpeg", "audio/wav", "audio/midi", "audio/mid", "application/octet-stream");
$upload_config['valid_types'][FT_VIDEO] = array("video/mpeg", "video/avi", "video/x-ms-asf", "video/x-ms-wmv", "video/x-msvideo");
$upload_config['valid_types'][FT_FLASH] = array("application/x-shockwave-flash");

$upload_config['upload_cfg'][UT_ADO_IMAGES] = array('upload_dir'=>$settings['photos_folder'], 'file_max_size'=>0);
$upload_config['upload_cfg'][UT_ADO_VIDEO] = array('upload_dir'=>$settings['video_folder'], 'file_max_size'=>0);
$upload_config['upload_cfg'][UT_ADO_AUDIO] = array('upload_dir'=>$settings['audio_folder'], 'file_max_size'=>0);
?>