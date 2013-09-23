<?php

/**
* Users uploads administration (approve of uploaded photos, galleries categories management).
*
* @package DatingPro
* @subpackage Admin Mode
**/


include '../include/config.php';
include_once '../common.php';
include '../include/config_admin.php';
include '../include/functions_auth.php';
include '../include/functions_admin.php';
include '../include/class.images.php';
include '../include/class.phpmailer.php';
include '../include/functions_mail.php';
include '../include/class.lang.php';

$auth = auth_user();
login_check($auth);
IsFileAllowed($auth[0], GetRightModulePath(__FILE__), 'uploads');

$sel		= isset($_REQUEST['sel']) ? $_REQUEST['sel'] : '';
$id_file	= isset($_REQUEST['id_file']) ? intval($_REQUEST['id_file']) : '';

switch ($sel) {
	case 'change': ChangeUpload(); break;
	case 'view': ViewUpload($id_file); break;
	case 'delete': DeleteUpload(); break;
	case 'adult': ChangeUpload("adult");
	default: ListUpload(); break;
}

function ListUpload($err = '', $type_upload = '')
{
	global $smarty, $dbconn, $config, $config_admin, $lang;
	
	$file_name = 'admin_upload.php';
	
	$type_upload	= isset($_REQUEST['type_upload']) ? intval($_REQUEST['type_upload']) : 1;
	$page			= isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
	
	AdminMainMenu($lang['uploads']);
	
	$data['upload_1_count'] = $dbconn->GetOne(
		'SELECT COUNT(a.id)
		   FROM '.USER_UPLOAD_TABLE.' a
	  LEFT JOIN '.USERS_TABLE.' b ON b.id = a.id_user
		  WHERE a.status = "0" AND a.upload_type = "f"');

	$data['upload_2_count'] = $dbconn->GetOne(
		'SELECT COUNT(a.id)
		   FROM '.USER_UPLOAD_TABLE.' a
	  LEFT JOIN '.USERS_TABLE.' b ON b.id = a.id_user
		  WHERE a.status = "0" AND a.upload_type = "a"');
	
	$data['upload_3_count'] = $dbconn->GetOne(
		'SELECT COUNT(a.id)
		   FROM '.USER_UPLOAD_TABLE.' a
	  LEFT JOIN '.USERS_TABLE.' b ON b.id = a.id_user
		  WHERE a.status = "0" AND a.upload_type = "v"');
	
	$data['upload_5_count'] = $dbconn->GetOne('SELECT COUNT(id) FROM '.USERS_TABLE.' WHERE icon_path_temp <> ""');
	
	switch ($type_upload)
	{
		case '1':
			// photo
			$strSQL =
				'SELECT DISTINCT a.id, a.upload_path, a.id_user, b.login
				   FROM '.USER_UPLOAD_TABLE.' a
			  LEFT JOIN '.USERS_TABLE.' b ON b.id = a.id_user
				  WHERE a.status = "0" AND a.upload_type = "f"
			   ORDER BY a.id DESC';
			$num_records = $data['upload_1_count'];
			$type_view = 1;
		break;
		
		case '2':
			// audio
			$strSQL =
				'SELECT DISTINCT a.id, a.upload_path, a.id_user, b.login
				   FROM '.USER_UPLOAD_TABLE.' a
			  LEFT JOIN '.USERS_TABLE.' b ON b.id = a.id_user
				  WHERE a.status = "0" AND a.upload_type = "a"
			   ORDER BY a.id DESC';
			$num_records = $data['upload_2_count'];
			$type_view = 2;
		break;
		
		case '3':
			// video
			$strSQL =
				'SELECT DISTINCT a.id, a.upload_path, a.id_user, b.login
				   FROM '.USER_UPLOAD_TABLE.' a
			  LEFT JOIN '.USERS_TABLE.' b ON b.id = a.id_user
				  WHERE a.status = "0" AND a.upload_type = "v"
			   ORDER BY a.id DESC';
			$num_records = $data['upload_3_count'];
			$type_view = 3;
		break;
		
		case '5':
			// profile icon
			$strSQL =
				'SELECT DISTINCT id, icon_path_temp, id as id_user, login
				   FROM '.USERS_TABLE.'
				  WHERE icon_path_temp <> ""
			   ORDER BY id DESC';
			$num_records = $data['upload_5_count'];
			$type_view = 1;
		break;
		
		default:
			// photo
			$strSQL =
				'SELECT DISTINCT a.id, a.upload_path, a.id_user, b.login
				   FROM '.USER_UPLOAD_TABLE.' a
			  LEFT JOIN '.USERS_TABLE.' b ON b.id = a.id_user
				  WHERE a.status = "0" AND a.upload_type = "f"
			   ORDER BY a.id DESC';
			$num_records = $data['upload_1_count'];
			$type_view = 1;
		break;
	}
	
	$lim_min = ($page - 1) * $config_admin['upload_numpage'];
	$lim_max = $config_admin['upload_numpage'];
	
	$smarty->assign('page', $page);
	
	$rs = $dbconn->Execute($strSQL.' LIMIT '.$lim_min.', '.$lim_max);
	
	$i = 0;
	$db_upload = array();
	
	while (!$rs->EOF) {
		$db_upload[$i]['id'] = $rs->fields[0];
		$db_upload[$i]['file_path'] = $rs->fields[1];
		if ($type_upload == 4) {
			$db_upload[$i]['file'] = UploadInput('4', $db_upload[$i]['file_path']);
		} elseif ($type_upload == 5) {
			$db_upload[$i]['file'] = UploadInput('5', $db_upload[$i]['file_path']);
		} else {
			$db_upload[$i]['file'] = UploadInput($type_view, $db_upload[$i]['file_path']);
		}
		$db_upload[$i]['id_user'] = $rs->fields[2];
		$db_upload[$i]['username'] = $rs->fields[3];
		$db_upload[$i]['userlink'] = 'admin_users.php?sel=edit&id='.$db_upload[$i]['id_user'];
		$rs->MoveNext();
		$i++;
	}
	
	$param = $file_name.'?type_upload='.$type_upload.'&';
	$smarty->assign('links', GetLinkStr($num_records, $page, $param, $config_admin['upload_numpage']));
	
	if (isset($db_upload)) {
		$smarty->assign('upload', $db_upload);
	}
	
	$form['action'] = $file_name;
	$form['err'] = $err;
	
	$smarty->assign('data', $data);
	$smarty->assign('type_upload', $type_upload);
	$smarty->assign('form', $form);
	$smarty->assign('header', $lang['uploads']);
	$smarty->assign('button', $lang['button']);

	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_upload_form.tpl');
	exit;
}

function ChangeUpload($par = '')
{
	global $dbconn, $config_admin;
	
	$err = '';
	
	$upload_type = isset($_REQUEST['upload_type']) && intval($_REQUEST['upload_type']) > 0 ? intval($_POST['upload_type']): null;
	
	if (!$upload_type) {
		ListUpload();
		return;
	}
	
	if ($par == 'adult') {
		$activate = isset($_POST['adult']) ? $_POST['adult']: '';
		$add_to_str = ', is_adult = "1" ';
	} else {
		$activate = isset($_POST['activate']) ? $_POST['activate']: '';
		$add_to_str = '';
	}
	
	$id_files = $_POST['id_files'];
	
	for ($i = 0; $i < $config_admin['upload_numpage']; $i++) {
		if (isset($activate[$i]) && intval($activate[$i]) == 1 && intval($id_files[$i])) {
			switch ($upload_type) {
				case '1':
					$dbconn->Execute('UPDATE '.USER_UPLOAD_TABLE.' SET status = "1" '.$add_to_str.' WHERE id = "'.$id_files[$i].'"');
					SendApproveMail($upload_type, $id_files[$i]);
				break;
				case '2':
					$dbconn->Execute('UPDATE '.USER_UPLOAD_TABLE.' SET status = "1" '.$add_to_str.' WHERE id = "'.$id_files[$i].'"');
					SendApproveMail($upload_type, $id_files[$i]);
				break;
				case '3':
					$dbconn->Execute('UPDATE '.USER_UPLOAD_TABLE.' SET status = "1" '.$add_to_str.' WHERE id = "'.$id_files[$i].'"');
					SendApproveMail($upload_type, $id_files[$i]);
				break;
				case '5':
					$dbconn->Execute('UPDATE '.USERS_TABLE.' SET icon_path = icon_path_temp, icon_path_temp = "" WHERE id = "'.$id_files[$i].'"');
					SendApproveMail($upload_type, $id_files[$i]);
				break;
			}
		}
	}
	
	ListUpload($err, $upload_type);
	return;
}

function DeleteUpload()
{
	global $dbconn, $config, $config_admin;
	
	$err = '';
	$file_name = 'admin_upload.php';
	
	$upload_type = intval($_POST['upload_type']);
	
	if (!$upload_type) {
		ListUpload();
		return;
	}
	
	$delete = isset($_POST['delete']) ? $_POST['delete'] : '';
	$id_files = isset($_POST['id_files']) ? $_POST['id_files'] : '';
	
	$rs = $dbconn->Execute('SELECT name, value FROM '.SETTINGS_TABLE.' WHERE name IN ("icons_folder", "photos_folder", "audio_folder", "video_folder")');
	
	while (!$rs->EOF) {
		$settings[$rs->fields[0]] = $rs->fields[1];
		$rs->MoveNext();
	}
	
	switch ($upload_type) {
		case '1': $folder = $settings['photos_folder']; break;
		case '2': $folder = $settings['audio_folder']; break;
		case '3': $folder = $settings['video_folder']; break;
		case '5': $folder = $settings['icons_folder']; break;
		default: $folder = $settings['photos_folder'];
	}

	for ($i = 0; $i < $config_admin['upload_numpage']; $i++) {
		if (isset($delete[$i]) && intval($delete[$i]) == 1 && intval($id_files[$i])) {
			switch ($upload_type) {
				case '1':
					$rs_upl = $dbconn->Execute('SELECT upload_path FROM '.USER_UPLOAD_TABLE.' WHERE id = "'.$id_files[$i].'"');
					if (strlen($rs_upl->fields[0]) > 0) {
						$old_file = $config['site_path'].$folder.'/'.$rs_upl->fields[0];
						$thumb_old_file = $config['site_path'].$folder.'/thumb_'.$rs_upl->fields[0];
						if (file_exists($old_file))	{
							unlink($old_file);
						}
						if (file_exists($thumb_old_file)) {
							unlink($thumb_old_file);
						}
						$dbconn->Execute('DELETE FROM '.USER_UPLOAD_TABLE.' WHERE id = "'.$id_files[$i].'"');
					}
				break;
				case '2':
					$rs_upl = $dbconn->Execute('SELECT upload_path FROM '.USER_UPLOAD_TABLE.' WHERE id = "'.$id_files[$i].'"');
					if (strlen($rs_upl->fields[0]) > 0) {
						$old_file = $config['site_path'].$folder.'/'.$rs_upl->fields[0];
						if (file_exists($old_file))	{
							unlink($old_file);
						}
						$dbconn->Execute('DELETE FROM '.USER_UPLOAD_TABLE.' WHERE id = "'.$id_files[$i].'"');
					}
				break;
				case '3':
					$rs_upl = $dbconn->Execute('SELECT upload_path FROM '.USER_UPLOAD_TABLE.' WHERE id = "'.$id_files[$i].'"');
					if (strlen($rs_upl->fields[0]) > 0) {
						$old_file = $config['site_path'].$folder.'/'.$rs_upl->fields[0];
						if (file_exists($old_file))	{
							unlink($old_file);
						}
						$dbconn->Execute('DELETE FROM '.USER_UPLOAD_TABLE.' WHERE id = "'.$id_files[$i].'"');
					}
				break;
				case '5':
					$rs_upl = $dbconn->Execute('SELECT icon_path_temp FROM '.USERS_TABLE.' WHERE id = "'.$id_files[$i].'"');
					if (strlen($rs_upl->fields[0]) > 0) {
						$file_name = substr($rs_upl->fields[0], strlen('thumb_'));
						$old_file = $config['site_path'].$folder.'/'.$file_name;
						$thumb_old_file = $config['site_path'].$folder.'/thumb_'.$file_name;
						$main_thumb_old_file = $config['site_path'].$folder.'/main_thumb_'.$file_name;
						$big_thumb_old_file = $config['site_path'].$folder.'/big_thumb_'.$file_name;
						if (file_exists($old_file)) {
							unlink($old_file);
						}
						if (file_exists($thumb_old_file)) {
							unlink($thumb_old_file);
						}
						if (file_exists($main_thumb_old_file)) {
							unlink($main_thumb_old_file);
						}
						if (file_exists($big_thumb_old_file)) {
							unlink($big_thumb_old_file);
						}
						$dbconn->Execute('UPDATE '.USERS_TABLE.' SET icon_path = "", icon_path_temp = "" WHERE id = "'.$id_files[$i].'"');
					}
				break;
			}
		}
	}
	
	ListUpload($err, $upload_type);
	return;
}

function UploadInput($type, $file='')
{
	global $config, $lang, $dbconn;
	
	$s_name = 'admin_upload.php';
	
	$images_obj = new Images($dbconn);
	
	$id = $dbconn->GetOne('SELECT id FROM '.USER_UPLOAD_TABLE.' WHERE upload_path = "'.$file.'"');
	
	if ($type == 1)
	{
		// photos
		$rs = $dbconn->Execute('SELECT value, name FROM '.SETTINGS_TABLE.' WHERE name IN ("photos_folder", "photos_default")');
		while (!$rs->EOF) {
			$upload[$rs->fields[1]] = $rs->fields[0];
			$rs->MoveNext();
		}
		$path = $config['site_path'].$upload['photos_folder'].'/'.$file;
		$thumb_path = $config['site_path'].$upload['photos_folder'].'/thumb_'.$file;
		$input_str = '<img src="'.$config['site_root'].$upload['photos_folder'].'/'.$upload['photos_default'].'" border="0" />';
		if (file_exists($thumb_path) && strlen($file) > 0) {
			$input_str = '<font style="cursor:pointer;" onclick="javascript:window.open(\''.$s_name.'?sel=view&id_file='.$id.'\', \'upload_view\', \'height=750,resizable=yes,scrollbars=yes,width=750,menubar=no,status=no\'); return false;"><img src="'.$config['site_root'].$upload['photos_folder'].'/thumb_'.$file.'" border="1" bordercolor="1" /></font>';
		}
		if (file_exists($path) && !file_exists($thumb_path) && strlen($file) > 0) {
			$input_str = '<font style="cursor:pointer;" onclick="javascript:window.open(\''.$s_name.'?sel=view&id_file='.$id.'\', \'upload_view\', \'height=750,resizable=yes,scrollbars=yes,width=750,menubar=no,status=no\'); return false;"><img src="'.$config['site_root'].$upload['photos_folder'].'/'.$file.'\' border="1" bordercolor="1" '.$images_obj->GetResizeParametrsStr($path).' /></font>';
		}
	}
	elseif ($type == 2)
	{
		// audio
		$rs = $dbconn->Execute('SELECT value, name FROM '.SETTINGS_TABLE.' WHERE name IN ("audio_folder", "audio_default")');
		while (!$rs->EOF) {
			$upload[$rs->fields[1]] = $rs->fields[0];
			$rs->MoveNext();
		}
		$path = $config['site_path'].$upload['audio_folder'].'/'.$file;
		if (file_exists($path) && strlen($file) > 0) {
			$input_str = '[<font style="cursor:pointer;" onclick="Javascript:window.open(\''.$s_name.'?sel=view&id_file='.$id.'\', \'upload_view\', \'height=750,resizable=yes,scrollbars=yes,width=750,menubar=no,status=no\'); return false;">'.$lang['users']['audio_link'].'</font>]';
		} else {
			$input_str = $lang['users']['audio_default'];
		}
	}
	elseif ($type == 3)
	{
		// video
		$rs = $dbconn->Execute('SELECT value, name FROM '.SETTINGS_TABLE.' WHERE name IN ("video_folder", "video_default")');
		while (!$rs->EOF) {
			$upload[$rs->fields[1]] = $rs->fields[0];
			$rs->MoveNext();
		}
		$path = $config['site_path'].$upload['video_folder'].'/'.$file;
		if (file_exists($path) && strlen($file) > 0) {
			#$input_str = '[<font style="cursor:pointer;" onclick="javascript:window.open(\''.$s_name.'?sel=view&id_file='.$id.'\', \'upload_view\', \'height=750,resizable=yes,scrollbars=yes,width=750,menubar=no,status=no\'); return false;">'.$lang['users']['video_link'].'</font>]';
			$input_str = '[<a class="video_colorbox" href="'.$s_name.'?sel=view&id_file='.$id.'">'.$lang['users']['video_link'].'</a>]';
		} else {
			$input_str = $lang['users']['video_default'];
		}
	}
	elseif ($type == 5)
	{
		$rs = $dbconn->Execute('SELECT value, name FROM '.SETTINGS_TABLE.' WHERE name = "icons_folder"');
		while (!$rs->EOF) {
			$upload[$rs->fields[1]] = $rs->fields[0];
			$rs->MoveNext();
		}
		$path = $config['site_path'].$upload['icons_folder'].'/'.$file;
		if (file_exists($path) && strlen($file) > 0) {
			$input_str = '<img src="'.$config['site_root'].$upload['icons_folder'].'/'.$file.'" border="1" bordercolor="1" />';
		} else {
			$input_str = '';
		}
	}
	
	return $input_str;
}

function ViewUpload($id_file)
{
	global $smarty, $dbconn, $config, $lang;
	
	// 2nd parameter sets smarty var $light to show a reduced admin_top.tpl
	AdminMainMenu($lang['uploads'], '1');
	
	$videoplay	= isset($_POST['videoplay']) ? trim($_POST['videoplay']) : '';
	
	if ($videoplay) {
		$_SESSION['videoplay'] = $videoplay;
	}
	if (empty($_SESSION['videoplay'])) {
		$_SESSION['videoplay'] = 'RTMP';
	}
	
	$data['videoplay'] = $_SESSION['videoplay'];
	
	$rs = $dbconn->Execute(
		'SELECT a.upload_path, b.login, a.user_comment, a.upload_type
		   FROM '.USER_UPLOAD_TABLE.' a
	  LEFT JOIN '.USERS_TABLE.' b ON a.id_user = b.id
		  WHERE a.id = ?',
		array($id_file));
	$row = $rs->GetRowAssoc(false);
	
	$data['file_name']		= $row['upload_path'];
	$data['username']		= $row['login'];
	$data['user_comment']	= stripslashes($row['user_comment']);
	$data['upload_type']	= $row['upload_type'];
	
	unset($row, $rs);
	
	switch ($data['upload_type'])
	{
		case 'f':
			$folder = $dbconn->GetOne('SELECT value FROM '.SETTINGS_TABLE.' WHERE name = "photos_folder"');
			$data['file_path'] = $config['server'].$config['site_root'].$folder.'/'.$data['file_name'];
			$data['file_name'] = $data['file_path'];
		break;
		
		case 'a':
			$folder = $dbconn->GetOne('SELECT value FROM '.SETTINGS_TABLE.' WHERE name = "audio_folder"');
			$data['file_path'] = $config['server'].$config['site_root'].$folder.'/'.$data['file_name'];
			$data['file_name'] = $data['file_path'];
		break;
		
		case 'v':
			$settings = GetSiteSettings(array('video_folder', 'flv_player_width', 'flv_player_height'));
			
			$smarty->assign('settings', $settings);
			
			$file_name_arr = explode('.', $data['file_name']);
			
			if (file_exists($config['site_path'].$settings['video_folder'].'/'.$file_name_arr[0].'-out.mp4')) {
				$data['file_name'] = $file_name_arr[0].'-out.mp4';
			}
			
			$data['file_path'] = $config['server'].$config['site_root'].$settings['video_folder'].'/'.$data['file_name'];
			
			if (file_exists($config['site_path'].$settings['video_folder'].'/'.$file_name_arr[0].'1.jpg')) {
				$data['image_path'] = $config['server'].$config['site_root'].$settings['video_folder'].'/'.$file_name_arr[0].'1.jpg';
			} else {
				$data['image_path'] = '';
			}
			
			require_once '../include/class.browser_detection.php';
			
			$browser = strtolower(Browser_Detection::get_browser($_SERVER['HTTP_USER_AGENT']));
			
			// firefox and opera do not support H.264, IE9 does not play converted mp4 which are played by chrome and safari
			if (strpos($browser, 'firefox') !== false || strpos($browser, 'opera') !== false) { // || strpos($browser, 'internet explorer') !== false) {
				$data['html5_video'] = false;
			} else {
				$data['html5_video'] = true;
			}
			
			$smarty->assign('superlight', 1);
		break;
	}
	
	$smarty->assign('data', $data);
	$smarty->assign('header', $lang['users']);
	$smarty->assign('button', $lang['button']);

	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_user_view_upload.tpl');
	exit;
}

function SendApproveMail($type_upload, $id_upload)
{
	global $dbconn, $config;
	
	switch ($type_upload) {
		case '1':
		case '2':
		case '3':
			$id_user = $dbconn->GetOne('SELECT id_user FROM '.USER_UPLOAD_TABLE.' WHERE id = ?', array($id_upload));
		break;
		case '5':
			$id_user = $id_upload;
		break;
	}
	
	$content_array				= array();
	$content_array['urls']		= GetUserEmailLinks();
	
	$rs = $dbconn->Execute(
		'SELECT fname, sname, email, login, gender, site_language
		   FROM '.USERS_TABLE.'
		  WHERE id = ?',
		array($id_user));
	$row = $rs->GetRowAssoc(false);
	
	$content_array['fname']		= stripslashes($row['fname']);
	$content_array['sname'] 	= stripslashes($row['sname']);
	$content_array['email'] 	= stripslashes($row['email']);
	$content_array['login'] 	= stripslashes($row['login']);
	$content_array['gender']	= $row['gender'];
	
	$site_lang					= $row['site_language'];
	
	unset($row);
	$rs->free();
	
	// include mail language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	// language suffix
	$suffix = ($content_array['gender'] == GENDER_MALE) ? '_e' : '_t';
	
	// subject
	$subject = $lang_mail['approve_upload'.$suffix]['subject'];
	
	// message
	$content_array['message'] = $lang_mail['approve_upload'.$suffix]['message'];
	
	// recipient
	$name_to = trim($content_array['fname'].' '.$content_array['sname']);
	
	SendMail($site_lang, $content_array['email'], $config['site_email'], $subject, $content_array,
		'mail_noti_simple_generic_user', null, $name_to, '', 'approve_upload', $content_array['gender']);
	
	return;
}

?>