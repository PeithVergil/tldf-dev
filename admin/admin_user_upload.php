<?php

/**
* User uploaded files administration.
*
* @package DatingPro
* @subpackage Admin Mode
**/

include "../include/config.php";
include_once "../common.php";
include "../include/config_admin.php";
include "../include/class.images.php";
include "../include/functions_auth.php";
include "../include/functions_admin.php";
include "../include/functions_users.php";

$auth = auth_user();
login_check($auth);
IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "users");

$sel = isset($_REQUEST['sel']) ? $_REQUEST['sel'] : '';
$type_upload = isset($_REQUEST['type_upload']) ? intval($_REQUEST['type_upload']) : 1;
$id_file = isset($_REQUEST['id_file']) ? $_REQUEST['id_file'] : '';
$id_user = isset($_REQUEST["id"]) ? intval($_REQUEST["id"]) : "";
$icon = isset($_REQUEST["icon"]) ? $_REQUEST["icon"] : null;

switch ($sel) {
	case "change": ChangeUpload($id_user); break;
	case "view": ViewUpload($id_file); break;
	case "delete": DeleteUpload($id_file, $id_user, $type_upload, $icon); break;
	default: ListUpload("", $id_user, $type_upload);
}

function ListUpload($err = "", $id="", $type_upload="")
{
	global $smarty, $dbconn, $config, $lang;
	
	if ($id == "") {
		echo "<script language='JavaScript' type='text/javascript'>window.close();</script>";
		exit;
	}

	$file_name = "admin_user_upload.php";
	
	$images_obj = new Images($dbconn);

	AdminMainMenu($lang["users"], "1");

	if($err){
		$form["err"] = $err;
	}

	switch ($type_upload) {
		case 1:
			$upload_type = "f";
			break;
		case 2:
			$upload_type = "a";
			break;
		case 3:
			$upload_type = "v";
			break;
		default:
			$upload_type = "f";
			$type_upload = 1;
			break;
	}
	
	switch ($type_upload) {
		case 1:
			$settings = GetSiteSettings(array('icon_male_default', 'icon_female_default', 'photo_max_width', 'photo_max_height', 'photo_max_size',
				'icon_max_width', 'icon_max_height', 'icon_max_size', 'icons_folder', 'photos_folder', 'photos_default', 'use_image_resize'));
			if ($settings["use_image_resize"]) {
				$data["icon_comment"] = str_replace("[size]", $settings["icon_max_size"], $lang["confirm"]["icon_upload_resize"]);
			} else {
				$data["icon_comment"] = str_replace("[size]", $settings["icon_max_size"], $lang["confirm"]["icon_upload"]);
				$data["icon_comment"] = str_replace("[width]", $settings["icon_max_width"], $data["icon_comment"]);
				$data["icon_comment"] = str_replace("[height]", $settings["icon_max_height"], $data["icon_comment"]);
			}
			$default_photos['1'] = $settings['icon_male_default'];
			$default_photos['2'] = $settings['icon_female_default'];
			
			$rs = $dbconn->Execute("SELECT icon_path, icon_path_temp, gender FROM ".USERS_TABLE." WHERE id = '".$id."'");
			$file = $rs->fields[0] ? $rs->fields[0] : ($rs->fields[1] ? $rs->fields[1] : $default_photos[$rs->fields[2]]);
			$path = $config["site_path"].$settings["icons_folder"]."/".$file;
			
			if (file_exists($path) && strlen($file) > 0) {
				$icon["file"] = "<img src=\"".$config["server"].$config["site_root"].$settings["icons_folder"]."/".$file."\" border=0>";
			}
			if (strlen($rs->fields[0]) > 0) {
				$icon["delete_link"] = "./".$file_name."?sel=delete&id=".$id."&id_file=&type_upload=1&icon=1";
			}
			$smarty->assign("icon", $icon);

			if ($settings["use_image_resize"]) {
				$data["comment"] = str_replace("[size]", $settings["photo_max_size"], $lang["confirm"]["photo_upload_resize"]);
			} else {
				$data["comment"] = str_replace("[size]", $settings["photo_max_size"], $lang["confirm"]["photo_upload"]);
				$data["comment"] = str_replace("[width]", $settings["photo_max_width"], $data["comment"]);
				$data["comment"] = str_replace("[height]", $settings["photo_max_height"], $data["comment"]);
			}
			$folder = $config["server"].$config["site_root"].$settings['photos_folder'];
		break;
		case 2:
			$settings = GetSiteSettings(array('audio_max_size', 'audio_folder'));
			$data['comment'] = str_replace('[size]', $settings['audio_max_size'], $lang['confirm']['audio_upload']);
			$folder = $config['server'].$config['site_root'].$settings['audio_folder'];
		break;
		case 3:
			$settings = GetSiteSettings(array('video_max_size', 'video_folder'));
			$data['comment'] = str_replace('[size]', $settings['video_max_size'], $lang['confirm']['video_upload']);
			$folder = $config['server'].$config['site_root'].$settings['video_folder'];
		break;
	}
	$i = 0;
	$upload = array();
	$rs = $dbconn->Execute("SELECT id, upload_path, allow, user_comment
							FROM ".USER_UPLOAD_TABLE."
							WHERE id_user='".$id."' AND upload_type='".$upload_type."'");
	if ($rs->fields[0] > 0) {
		while(!$rs->EOF){
			$upload[$i]["id"] = $rs->fields[0];
			if ($type_upload == 1) {
				$upload[$i]["file_path"] = $folder."/thumb_".$rs->fields[1];
			} else {
				$upload[$i]["file_path"] = $rs->fields[1];
			}
			$upload[$i]["allow"] = $rs->fields[2];
			$upload[$i]["user_comment"] = stripslashes($rs->fields[3]);
			$upload[$i]["del_link"] =  "./".$file_name."?sel=delete&id=".$id."&id_file=".$upload[$i]["id"]."&type_upload=".$type_upload;
			$rs->MoveNext();
			$i++;
		}
	} else {
		$upload = 'empty';
	}

	$strSQL = " SELECT CONCAT(fname, ' ', sname, ' (',login,')'), root_user FROM ".USERS_TABLE." WHERE id='".$id."'";
	$rs = $dbconn->Execute($strSQL);
	$data["username"] = $rs->fields[0];
	$data["id"] = $id;
	$data["root"] = $rs->fields[1];
	$form["action"] = $file_name;

	if (isset($upload)) {
		$smarty->assign("upload", $upload);
	}
	$smarty->assign("data", $data);
	$smarty->assign("settings", $settings);
	$smarty->assign("type_upload", $type_upload);
	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["users"]);
	$smarty->assign("button", $lang["button"]);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_user_uploades_form.tpl");
	exit;
}

function ChangeUpload($id = '')
{
	global $dbconn, $IMG_TYPE_ARRAY, $AUDIO_TYPE_ARRAY, $VIDEO_TYPE_ARRAY;
	
	$err = '';
	
	//print_r($_REQUEST);
	//print_r($_FILES);
	//exit;
	
	$upload_type = isset($_REQUEST['upload_type']) ? intval($_REQUEST['upload_type']): '';
	
	if ($id == '') {
		echo "<script type='text/javascript'>window.close();</script>";
		exit;
	}
	
	if ($upload_type == '') {
		ListUpload('', $id);
		return;
	}
	
	// all are arrays
	$upload_allow	= isset($_POST['upload_allow']) ? $_POST['upload_allow'] : '';
	$id_files		= isset($_POST['id_files']) ? $_POST['id_files']: '';
	$user_comments	= isset($_POST['user_comments']) ? $_POST['user_comments']: '';
	
	switch ($upload_type) {
		case '1':
			if (isset($_FILES['icon']) && $_FILES['icon']['name'] != '') {
				#$icon = $_FILES['icon'];
				#$err = SaveUploadForm($icon, 'icon', '', '', '', 1, $id); //upload, upload_type, upload_allow, id_file, user_comment, admin_mode, id_user_admin_mode
				$images_obj = new Images($dbconn);
				$err = $images_obj->UploadIcon('icon', $id, 1);
				$data = $images_obj->data;
			}
			$type = 'f';
		break;
		case '2':
			$type = 'a';
		break;
		case '3':
			$type = 'v';
		break;
	}
	
	if ($id_files) {
		foreach ($id_files as $key => $dummy) {
			$upload = $_FILES['upload'.$key];
			if (is_uploaded_file($upload['tmp_name']))
			{
				//$err .= SaveUploadForm($upload, $type, $upload_allow[$key], $id_files[$key], $user_comments[$key], 1, $id);
				$images_obj = new Images($dbconn);
				switch ($type) {
					case 'a':
						$err = $images_obj->UploadAudio('upload'.$key, $id, 1, $key);
					break;
					case 'v':
						$err = $images_obj->UploadVideo('upload'.$key, $id, 1, $key);
					break;
					case 'f':
					default:
						$err = $images_obj->UploadPhoto('upload'.$key, $id, 1, $key);
					break;
				}
			}
			else
			{
				$dbconn->Execute(
					'UPDATE '.USER_UPLOAD_TABLE.'
						SET allow = "'.$upload_allow[$key].'", user_comment = "'.addslashes($user_comments[$key]).'"
					  WHERE id = "'.$id_files[$key].'" AND id_user = "'.$id.'"');
			}
		}
	}
	
	if (substr($err, 0, 2) == 'OK') {
		$err = substr($err, 3);
	}
	
	ListUpload($err, $id, $upload_type);
	return;
}

function UploadInput($type, $id="", $file="", $icon="", $gender="")
{
	global $config, $lang, $dbconn;
	
	$s_name = "admin_user_upload.php";

	if($type == 1 && $icon ==1){		/// icon
		$rs=$dbconn->Execute("select value, name from ".SETTINGS_TABLE." where name in ('icons_folder', 'icons_default', 'icon_male_default', 'icon_female_default')");
		while(!$rs->EOF){
			$upload[$rs->fields[1]] = $rs->fields[0];
			$rs->MoveNext();
		}
		$path = $config["site_path"].$upload["icons_folder"]."/".$file;
		if(file_exists($path) && strlen($file)>0){
			$input_str = "<img src=\"".$config["server"].$config["site_root"].$upload["icons_folder"]."/".$file."\" border=0 width='100'>";
		}else{
			if($gender == 1)
			$input_str = "<img src=\"".$config["server"].$config["site_root"].$upload["icons_folder"]."/".$upload["icon_male_default"]."\" border=0 width='100'>";
			else
			$input_str = "<img src=\"".$config["server"].$config["site_root"].$upload["icons_folder"]."/".$upload["icon_female_default"]."\" border=0 width='100'>";
		}
	}elseif($type == 1 && $icon !=1){
		$rs=$dbconn->Execute("select value, name from ".SETTINGS_TABLE." where name in ('photos_folder', 'photos_default')");
		while(!$rs->EOF){
			$upload[$rs->fields[1]] = $rs->fields[0];
			$rs->MoveNext();
		}
		$path = $config["site_path"].$upload["photos_folder"]."/".$file;
		if(file_exists($path) && strlen($file)>0){
			$input_str = "<img src=\"".$config["site_root"].$upload["photos_folder"]."/".$file."\" border=0 width='100'>";
		}else{
			$input_str = "<img src=\"".$config["site_root"].$upload["photos_folder"]."/".$upload["photos_default"]."\" border=0 width='100'>";
		}
	}elseif($type == 2){
		$rs=$dbconn->Execute("select value, name from ".SETTINGS_TABLE." where name in ('audio_folder', 'audio_default')");
		while(!$rs->EOF){
			$upload[$rs->fields[1]] = $rs->fields[0];
			$rs->MoveNext();
		}
		$path = $config["site_path"].$upload["audio_folder"]."/".$file;
		if(file_exists($path) && strlen($file)>0){
			$input_str = "[<font style=\"cursor:hand\" onclick=\"Javascript:window.open('".$s_name."?sel=view&id_file=".$id."', 'upload_view', 'height=750, resizable=yes, scrollbars=yes,width=750, menubar=no,status=no');\">".$lang["users"]["audio_link"]."</font>]";
		}else{
			$input_str = $lang["users"]["audio_default"];
		}
	}elseif($type == 3){
		$rs=$dbconn->Execute("select value, name from ".SETTINGS_TABLE." where name in ('video_folder', 'video_default')");
		while(!$rs->EOF){
			$upload[$rs->fields[1]] = $rs->fields[0];
			$rs->MoveNext();
		}
		$path = $config["site_path"].$upload["video_folder"]."/".$file;
		if(file_exists($path) && strlen($file)>0){
			$input_str = "[<font style=\"cursor:hand\" onclick=\"Javascript:window.open('".$s_name."?sel=view&id_file=".$id."', 'upload_view', 'height=750, resizable=yes, scrollbars=yes,width=750, menubar=no,status=no');\">".$lang["users"]["video_link"]."</font>]";
		}else{
			$input_str = $lang["users"]["video_default"];
		}
	}
	return $input_str;
}

function ViewUpload($id_file){
	global $smarty, $dbconn, $config, $lang;
	AdminMainMenu($lang["users"], "1");

	$settings = GetSiteSettings(array('photos_folder','audio_folder','video_folder'));
	$rs = $dbconn->Execute(" 	SELECT a.upload_path, b.login, a.upload_type, a.id_user
								FROM ".USER_UPLOAD_TABLE." a LEFT JOIN ".USERS_TABLE." b on a.id_user = b.id
								WHERE a.id='".$id_file."'");

	$data["file_name"] = $rs->fields[0];
	$data["username"] = $rs->fields[1];

	$upload_type = $rs->fields[2];
	$data["id_user"] = $rs->fields[3];
	$data["button_type"] = 1;
	switch($upload_type){
		case "f":
			$folder = $settings['photos_folder'];
			$data["type"] = 1;
			break;
		case "a":
			$folder = $settings['audio_folder'];
			$data["type"] = 2;
			break;
		case "v":
			$folder = $settings['video_folder'];
			$data["type"] = 3;
			break;
	}
	$data["file_path"] = $config["server"].$config["site_root"].$folder."/".$data["file_name"];
	$smarty->assign("data", $data);
	$smarty->assign("upload_type", $upload_type);
	$smarty->assign("header", $lang["users"]);
	$smarty->assign("button", $lang["button"]);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_user_view_upload.tpl");
	exit;
}

function DeleteUpload($id_file, $id_user="", $type_upload, $icon=null)
{
	global $dbconn, $config;
	
	$settings = GetSiteSettings(array('icons_folder', 'photos_folder', 'audio_folder', 'video_folder'));

	if($icon){
		$folder = $settings["icons_folder"];
		$rs_upl = $dbconn->Execute(" SELECT icon_path, icon_path_temp FROM ".USERS_TABLE." WHERE id='".$id_user."'");
		if(strlen($rs_upl->fields[0])>0){
			$file_name = substr($rs_upl->fields[0], strlen("thumb_"));
			$old_file =$config["site_path"].$folder."/".$file_name;
			$thumb_old_file =$config["site_path"].$folder."/thumb_".$file_name;
			$main_thumb_old_file =$config["site_path"].$folder."/main_thumb_".$file_name;
			$big_thumb_old_file =$config["site_path"].$folder."/big_thumb_".$file_name;
			if(file_exists($old_file))	unlink($old_file);
			if(file_exists($thumb_old_file))	unlink($thumb_old_file);
			if(file_exists($main_thumb_old_file))	unlink($main_thumb_old_file);
			if(file_exists($big_thumb_old_file))	unlink($big_thumb_old_file);
			$dbconn->Execute("UPDATE ".USERS_TABLE." SET icon_path='' WHERE id='".$id_user."' ");
		}
		if (strlen($rs_upl->fields[1])>0) {
			$file_name = substr($rs_upl->fields[1], strlen("thumb_"));
			$old_file =$config["site_path"].$folder."/".$file_name;
			$thumb_old_file =$config["site_path"].$folder."/thumb_".$file_name;
			$main_thumb_old_file =$config["site_path"].$folder."/main_thumb_".$file_name;
			$big_thumb_old_file =$config["site_path"].$folder."/big_thumb_".$file_name;
			if(file_exists($old_file))	unlink($old_file);
			if(file_exists($thumb_old_file))	unlink($thumb_old_file);
			if(file_exists($main_thumb_old_file))	unlink($main_thumb_old_file);
			if(file_exists($big_thumb_old_file))	unlink($big_thumb_old_file);
			$dbconn->Execute("UPDATE ".USERS_TABLE." SET icon_path_temp='' WHERE id='".$id_user."' ");
		}
	} else {
		switch($type_upload){
			case "1": $folder = $settings["photos_folder"]; $thumb = true; break;
			case "2": $folder = $settings["audio_folder"]; $thumb = false; break;
			case "3": $folder = $settings["video_folder"]; $thumb = false; break;
			default: $folder = $settings["photos_folder"]; $thumb = true;
		}
		$rs_upl=$dbconn->Execute("SELECT upload_path FROM ".USER_UPLOAD_TABLE." WHERE id='".$id_file."' AND id_user= '".$id_user."'");
		if(strlen($rs_upl->fields[0])>0){
			$old_file = $config["site_path"].$folder."/".$rs_upl->fields[0];
			if(file_exists($old_file))	unlink($old_file);
			if ($thumb == true) {
				$thumb_file = $config["site_path"].$folder."/thumb_".$rs_upl->fields[0];
				if(file_exists($thumb_file))	unlink($thumb_file);
			}
			$dbconn->Execute("DELETE FROM ".USER_UPLOAD_TABLE." WHERE id='".$id_file."' AND id_user= '".$id_user."'");
		}
	}
	ListUpload("", $id_user, $type_upload);
	return;
}

?>