<?php

/**
*	The class uses table Settings of base Dating Pro for definition of adjustments of a site.
*	It is supposed that it is in a folder./include together with other classes .
*	Proceeding from this fact ways for access to folders are formed .
*	Defines, whether library GDlib is installed on a server and according to this parameter displays, loads pictures,
*	creates thumbnails and make other transformations above images .
*	As entering parameters the reference to a connection to base and Lang a file is required.
*
* @package DatingPro
* @subpackage Include files
**/


class Images
{
	var $dbconn;
	var $settings;
	var $site_path;
	var $file_temp_path;
	var $waterlogo_path;
	var $IMG_TYPE_ARRAY;
	var $IMG_EXT_ARRAY;
	var $gd_used;
	var $safe_mode_used;
	var $err;
	var $data = array();
	var $upload = array();
	var $ext_array = array();
	var $type_array = array();
	var $max_size;
	var	$max_width;
	var $max_height;
	var $err_ext;
	var $err_type;
	var $err_size;
	var $err_width;
	var $err_height;
	
	function Images($dbconn)
	{
		$this->dbconn			= $dbconn;
		$this->settings			= GetSiteSettings(array('use_pilot_module_club', 'use_pilot_module_blog', 'use_image_resize', 'use_photo_logo',
									'icons_folder', 'icon_max_width', 'icon_max_height', 'icon_max_size', 'use_icon_approve',
									'photos_folder', 'photo_max_width', 'photo_max_height', 'photo_max_size', 'use_photo_approve',
									'club_uploads_folder', 'club_photo_max_width', 'club_photo_max_height', 'club_photo_max_size',
									'blog_folder', 'success_folder',
									'big_thumb_max_width', 'big_thumb_max_height', 'thumb_max_width', 'thumb_max_height'));
		$this->site_path		= dirname(__FILE__).'/..';
		$this->file_temp_path	= $this->site_path.'/templates_c';
		$this->waterlogo_path	= $this->site_path.'/uploades/photos/water_logo.png';
		$this->IMG_TYPE_ARRAY	= array('image/jpeg', 'image/pjpeg', 'image/gif', 'image/png', 'image/x-png');
		$this->IMG_EXT_ARRAY	= array('jpeg', 'jpg', 'gif', 'png');
		$this->gd_used			= extension_loaded('gd') ? 1 : 0;
		$this->safe_mode_used	= ini_get('safe_mode') ? 1 : 0;
	}
	
	
	function UploadIcon($upload_control, $id_user, $admin_mode=0)
	{
		global $IMG_EXT_ARRAY, $IMG_TYPE_ARRAY, $lang;
		
		if ($err = $this->checkPostMaxSize()) {
			return $err;
		}
		
		$this->upload		= $_FILES[ $upload_control ];
		
		$this->id_user		= $id_user;
		$this->settings		= GetSiteSettings(array('icons_folder', 'icon_max_width', 'icon_max_height', 'icon_max_size', 'use_icon_approve',
									'big_thumb_max_width', 'big_thumb_max_height', 'thumb_max_width', 'thumb_max_height',
									'use_image_resize', 'use_photo_logo'));
		$this->ext_array	= $IMG_EXT_ARRAY;
		$this->type_array	= $IMG_TYPE_ARRAY;
		$this->max_size		= getFileSizeFromString($this->settings['icon_max_size']);
		$this->max_width	= (int) $this->settings['icon_max_width'];
		$this->max_height	= (int) $this->settings['icon_max_height'];
		$this->err_ext		= $lang['err']['invalid_icon_ext'] . implode(', ', $this->ext_array);
		$this->err_type		= $lang['err']['invalid_icon_type'] . implode(', ', $this->type_array);
		$this->err_size		= str_replace('#SIZE#', $this->settings['icon_max_size'], $lang['err']['invalid_icon_size']);
		$this->err_width	= str_replace('#WIDTH#', $this->max_width, $lang['err']['invalid_icon_width']);
		$this->err_height	= str_replace('#HEIGHT#', $this->max_height, $lang['err']['invalid_icon_height']);
		
		// get form input
		$this->data					= array();
		$this->data['user_comment']	= isset($_POST['user_comment'])	? stripn(FormFilter($_POST['user_comment'])) : '';
		
		// check $_FILES error
		if ($err = $this->checkUploadError()) {
			return $err;
		}
		
		$folder				= $this->settings['icons_folder'];
		
		$use_approve		= ($admin_mode || MM_APPLICANT_DISABLE_ICON_APPROVE) ? 0 : (int) $this->settings['use_icon_approve'];
		
		// check form input
		$err = '';
		
		if ($temp = BadWordsCont($this->data['user_comment'], 6)) {
			$err = $temp;
		} elseif (check_filter($this->data['user_comment'])) {
			$err = $lang['err']['info_finding_1'];
		}
		
		if ($err) {
			return $err;
		}
		
		if ($this->upload['error'] == UPLOAD_ERR_NO_FILE) {
			// update headline only (not in use)
			# $this->dbconn->Execute('UPDATE '.USER_TABLE.' SET headline = ? WHERE id_user = ?', array($this->data['user_comment'], $id_user));
			# return 'OK_'.$lang['err']['icon_info_updated'];
		}
		
		// this was prepended to the error message
		// $lang['err']['upload_err']
		
		if (!is_uploaded_file($this->upload['tmp_name'])) {
			return $lang['err']['upload_err'];
		}
		
		// RS move file first, so we can resample if. we can change this when we switch
		// to PHP 5.4 and replace the 'rename' below with 'move_uploaded_file'
		#if ($this->safe_mode_used) {
			$new_temp_path = $this->GetTempUploadFile($this->upload['name']);
			if (move_uploaded_file($this->upload['tmp_name'], $new_temp_path)) {
				$this->upload['tmp_name'] = $new_temp_path;
			} else {
				return $lang['err']['move_uploaded_file'];
			}
		#}
		
		if ($err = $this->checkFileType()) {
			return $err;
		}
		
		// check file size
		if (!$this->settings['use_image_resize'] && $this->upload['size'] > $this->max_size) {
			return $this->err_size;
		}
		
		// resample picture when feature is active
		if ($this->settings['use_image_resize']) {
			//VP: $this->ReSizeImage($this->upload['tmp_name'], $this->max_width, $this->max_height);
			$this->ReSizeWithoutCropImage($this->upload['tmp_name'], $this->max_width, $this->max_height);
			$this->upload['size'] = filesize($this->upload['tmp_name']);
		}
		
		// check width and height
		if ($err = $this->checkWidthAndHeight()) {
			return $err;
		}
		
		// move to target folder
		$new_file_name = $this->GetNewFileName($this->upload['name'], $id_user);
		$upload_path = $this->site_path.$folder.'/'.$new_file_name;
		
		if (rename($this->upload['tmp_name'], $upload_path))
		{
			if ($this->gd_used)
			{
				$upload_info = GetImageSize($upload_path);
				
				// big thumb
				$big_thumb_upload_path = $this->site_path.$folder.'/big_thumb_'.$new_file_name;
				copy($upload_path, $big_thumb_upload_path);
				
				if (file_exists($big_thumb_upload_path)) {
					if (($upload_info[0] <= $this->settings['big_thumb_max_width']) && ($upload_info[1] <= $this->settings['big_thumb_max_height'])) {
						$this->ReSizeWithoutCropImage($big_thumb_upload_path, $this->settings['big_thumb_max_width'], $this->settings['big_thumb_max_height'], 1);
					} else {
						$this->ReSizeImage($big_thumb_upload_path, $this->settings['big_thumb_max_width'], $this->settings['big_thumb_max_height'], 1);
					}
				}
				
				// main thumb
				$main_thumb_upload_path = $this->site_path.$folder.'/main_thumb_'.$new_file_name;
				copy($upload_path, $main_thumb_upload_path);
				
				if (file_exists($main_thumb_upload_path)) {
					if (($upload_info[0] <= 56) && ($upload_info[1] <= 71)) {
						$this->ReSizeWithoutCropImage($main_thumb_upload_path, 56, 71, 1);
					} else {
						$this->ReSizeImage($main_thumb_upload_path, 56, 71, 1);
					}
				}
				
				// "normal" thumb
				$thumb_upload_path = $this->site_path.$folder.'/thumb_'.$new_file_name;
				copy($upload_path, $thumb_upload_path);
				
				if (file_exists($thumb_upload_path)) {
					if (($upload_info[0] <= $this->settings['thumb_max_width']) && ($upload_info[1] <= $this->settings['thumb_max_height'])) {
						$this->ReSizeWithoutCropImage($thumb_upload_path, $this->settings['thumb_max_width'], $this->settings['thumb_max_height'], 1);
					} else {
						$this->ReSizeImage($thumb_upload_path, $this->settings['thumb_max_width'], $this->settings['thumb_max_height'], 1);
					}
				}
			}
			
			// delete old files
			$this->DeleteUploadedFiles('icon', '', $id_user, $admin_mode);
			
			// database update
			if ($use_approve) {
				$strSQL =
					'UPDATE '.USERS_TABLE.' 
						SET icon_path = "", icon_path_temp = "thumb_'.$new_file_name.'", big_icon_path = "big_thumb_'.$new_file_name.'" 
					  WHERE id = ?';
				$this->dbconn->Execute($strSQL, array($id_user));
			} else {
				$strSQL =
					'UPDATE '.USERS_TABLE.' 
						SET icon_path = "thumb_'.$new_file_name.'", icon_path_temp = "", big_icon_path = "big_thumb_'.$new_file_name.'" 
					  WHERE id = ?';
				$this->dbconn->Execute($strSQL, array($id_user));
			}
			
			return 'OK_' . (is_applicant($id_user) ? $lang['msg']['icon_upload'] : $lang['err']['icon_saved']);
		}
		
		return $lang['err']['upload_err'];
	}
	
	
	function UploadPhoto($upload_control, $id_user, $admin_mode = 0, $index = null)
	{
		global $IMG_EXT_ARRAY, $IMG_TYPE_ARRAY, $lang;
		
		if ($err = $this->checkPostMaxSize()) {
			return $err;
		}
		
		// work around for flash upload when no file is selected
		if (empty($_FILES)) {
			$_FILES[ $upload_control ]['error'] = UPLOAD_ERR_NO_FILE;
		}
		
		$this->upload		= $_FILES[ $upload_control ];
		
		$this->id_user		= $id_user;
		$this->index		= $index;
		$this->settings		= GetSiteSettings(array('photos_folder', 'photo_max_width', 'photo_max_height', 'photo_max_size', 'use_photo_approve',
									'use_image_resize', 'use_photo_logo', 'thumb_max_width', 'thumb_max_height'));
		$this->ext_array	= $IMG_EXT_ARRAY;
		$this->type_array	= $IMG_TYPE_ARRAY;
		$this->max_size		= getFileSizeFromString($this->settings['photo_max_size']);
		$this->max_width	= (int) $this->settings['photo_max_width'];
		$this->max_height	= (int) $this->settings['photo_max_height'];
		$this->err_ext		= $lang['err']['invalid_photo_ext'] . implode(', ', $this->ext_array);
		$this->err_type		= $lang['err']['invalid_photo_type'] . implode(', ', $this->type_array);
		$this->err_size		= str_replace('#SIZE#', $this->settings['photo_max_size'], $lang['err']['invalid_photo_size']);
		$this->err_width	= str_replace('#WIDTH#', $this->max_width, $lang['err']['invalid_photo_width']);
		$this->err_height	= str_replace('#HEIGHT#', $this->max_height, $lang['err']['invalid_photo_height']);
		
		// get form input
		$this->getMultimediaFormInput();
		
		// check $_FILES error
		if ($err = $this->checkUploadError($this->data['id_file'])) {
			return $err;
		}
		
		$folder				= $this->settings['photos_folder'];
		
		$use_approve		= $admin_mode ? 0 : (int) $this->settings['use_photo_approve'];
		
		// this was prepended to the error message
		// $lang['err']['upload_err']
		
		// check form input
		if ($err = $this->checkMultimediaFormInput()) {
			return $err;
		}
		
		if ($this->upload['error'] == UPLOAD_ERR_NO_FILE) {
			$this->updateMultimediaRecord();
			return 'OK_' . $lang['err']['photo_info_updated'];
		}
		
		if (!is_uploaded_file($this->upload['tmp_name'])) {
			return $lang['err']['upload_err'];
		}
		
		// RS: move file first, so we can resample if. we can change this when we switch
		// to PHP 5.4 and replace the 'rename' below with 'move_uploaded_file'
		#if ($this->safe_mode_used) {
			$new_temp_path = $this->GetTempUploadFile($this->upload['name']);
			if (move_uploaded_file($this->upload['tmp_name'], $new_temp_path)) {
				$this->upload['tmp_name'] = $new_temp_path;
			} else {
				return $lang['err']['move_uploaded_file'];
			}
		#}
		
		if ($err = $this->checkFileType()) {
			return $err;
		}
		
		// check file size
		if (!$this->settings['use_image_resize'] && $this->upload['size'] > $this->max_size) {
			return $this->err_size;
		}
		
		// resample picture when feature is active
		if ($this->settings['use_image_resize']) {
			//VP: $this->ReSizeImage($this->upload['tmp_name'], $this->max_width, $this->max_height);
			$this->ReSizeWithoutCropImage($this->upload['tmp_name'], $this->max_width, $this->max_height);
			$this->upload['size'] = filesize($this->upload['tmp_name']);
		}
		
		// check width and height
		if ($err = $this->checkWidthAndHeight()) {
			return $err;
		}
		
		// move to target folder
		$new_file_name = $this->GetNewFileName($this->upload['name'], $id_user);
		$upload_path = $this->site_path.$folder.'/'.$new_file_name;
		
		if (rename($this->upload['tmp_name'], $upload_path))
		{
			if ($this->gd_used)
			{
				$upload_info = GetImageSize($upload_path);
				
				// "normal" thumb
				$thumb_upload_path = $this->site_path.$folder.'/thumb_'.$new_file_name;
				copy($upload_path, $thumb_upload_path);
				
				if (file_exists($thumb_upload_path)) {
					if (($upload_info[0] <= $this->settings['thumb_max_width']) && ($upload_info[1] <= $this->settings['thumb_max_height'])) {
						$this->ReSizeWithoutCropImage($thumb_upload_path, $this->settings['thumb_max_width'], $this->settings['thumb_max_height'], 1);
					} else {
						$this->ReSizeImage($thumb_upload_path, $this->settings['thumb_max_width'], $this->settings['thumb_max_height'], 1);
					}
				}
			}
			
			// delete old files
			if ($this->data['id_file']) {
				$this->DeleteUploadedFiles('f', $this->data['id_file'], $id_user, $admin_mode, false);
			}
			
			// database update
			$status = $use_approve ? '0' : '1';
			
			if ($admin_mode) {
				$strSQL =
					'UPDATE '.USER_UPLOAD_TABLE.'
						SET upload_path = ?, upload_type = "f", allow = ?, file_type = ?, status = ?, user_comment = ?
					  WHERE id_user = ? AND id = ?';
					$this->dbconn->Execute($strSQL, array(
						$new_file_name, (string)$this->data['upload_allow'], $this->upload['type'], (string)$status, $this->data['user_comment'],
						$id_user, $this->data['id_file']
					));
			} else {
				$this->updateMultimediaRecord($new_file_name, 'f', $status);
				/*
				$strSQL =
					'UPDATE '.USER_UPLOAD_TABLE.'
						SET upload_path = ?, upload_type = "f", allow = ?, file_type = ?, status = ?, user_comment = ?,
							is_gallary = ?, id_gallery = ?
					  WHERE id_user = ? AND id = ?';
				$this->dbconn->Execute($strSQL, array(
					$new_file_name, (string)$this->data['upload_allow'], $this->upload['type'], (string)$status, $this->data['user_comment'],
					(string)$this->data['is_gallary'], $this->data['id_gallery'], $id_user, $this->data['id_file']
				));
				*/
			}
			
			return 'OK_'.$lang['err']['photo_saved'];
		}
		
		return $lang['err']['upload_err'];
	}
	
	
	function UploadAudio($upload_control, $id_user, $admin_mode = 0, $index = null)
	{
		global $EMBEDDED_AUDIO_TYPE_ARRAY, $EMBEDDED_AUDIO_EXT_ARRAY, $AUDIO_TYPE_ARRAY, $AUDIO_EXT_ARRAY, $lang;
		
		if ($err = $this->checkPostMaxSize()) {
			return $err;
		}
		
		// work around for flash upload when no file is selected
		if (empty($_FILES)) {
			$_FILES[ $upload_control ]['error'] = UPLOAD_ERR_NO_FILE;
		}
		
		$this->upload		= $_FILES[ $upload_control ];
		
		$this->id_user		= $id_user;
		$this->index		= $index;
		$this->settings		= GetSiteSettings(array('audio_folder', 'use_embedded_audio', 'audio_max_size', 'use_audio_approve'));
		$this->ext_array	= $this->settings['use_embedded_audio'] ? $EMBEDDED_AUDIO_EXT_ARRAY : $AUDIO_EXT_ARRAY;
		$this->type_array	= $this->settings['use_embedded_audio'] ? $EMBEDDED_AUDIO_TYPE_ARRAY : $AUDIO_TYPE_ARRAY;
		$this->max_size		= getFileSizeFromString($this->settings['audio_max_size']);
		$this->err_ext		= $lang['err']['invalid_audio_ext'] . implode(', ', $this->ext_array);
		$this->err_type		= $lang['err']['invalid_audio_type'] . implode(', ', $this->type_array);
		$this->err_size		= str_replace('#SIZE#', $this->settings['audio_max_size'], $lang['err']['invalid_audio_size']);
		
		// get form input
		$this->getMultimediaFormInput();
		
		// check $_FILES error
		if ($err = $this->checkUploadError($this->data['id_file'])) {
			return $err;
		}
		
		$folder				= $this->settings['audio_folder'];
		
		$use_approve		= $admin_mode ? 0 : (int) $this->settings['use_audio_approve'];
		
		// this was prepended to the error message
		// $lang['err']['upload_err']
		
		// check form input
		if ($err = $this->checkMultimediaFormInput()) {
			return $err;
		}
		
		if ($this->upload['error'] == UPLOAD_ERR_NO_FILE) {
			$this->updateMultimediaRecord();
			return 'OK_' . $lang['err']['audio_info_updated'];
		}
		
		if (!is_uploaded_file($this->upload['tmp_name'])) {
			return $lang['err']['upload_err'];
		}
		
		if ($err = $this->checkFileType()) {
			return $err;
		}
		
		// check file size
		if ($this->upload['size'] > $this->max_size) {
			return $this->err_size;
		}
		
		// move to target folder
		$new_file_name = $this->GetNewFileName($this->upload['name'], $id_user);
		$upload_path = $this->site_path.$folder.'/'.$new_file_name;
		
		if (!move_uploaded_file($this->upload['tmp_name'], $upload_path)) {
			return $lang['err']['move_uploaded_file'];
		}
		
		@chmod($upload_path, 0644); // SN uses 755 but 644 is sufficient
		
		// delete old files
		if ($this->data['id_file']) {
			$this->DeleteUploadedFiles('a', $this->data['id_file'], $id_user, $admin_mode, false);
		}
		
		// database update
		$status = empty($use_approve) ? '1' : '0';
		
		$this->updateMultimediaRecord($new_file_name, 'a', $status);
		/*
		$strSQL =
			'UPDATE '.USER_UPLOAD_TABLE.' SET
					upload_path = ?, upload_type = "a", allow = ?, file_type = ?, status = ?, user_comment = ?,
					is_gallary = ?, id_gallery = ?
			  WHERE id_user = ? AND id = ?';
		$this->dbconn->Execute($strSQL, array(
			$new_file_name, (string)$this->data['upload_allow'], $this->upload['type'], (string)$status, $this->data['user_comment'],
			(string)$this->data['is_gallary'], $this->data['id_gallery'], $id_user, $this->data['id_file']
		));
		*/
		return 'OK_'.$lang['err']['audio_saved'];
	}
	
	
	function UploadVideo($upload_control, $id_user, $admin_mode = 0, $index = null)
	{
		global $VIDEO_TYPE_ARRAY, $VIDEO_EXT_ARRAY, $lang;
		
		if ($err = $this->checkPostMaxSize()) {
			return $err;
		}
		
		// work around for flash upload when no file is selected
		if (empty($_FILES)) {
			$_FILES[ $upload_control ]['error'] = UPLOAD_ERR_NO_FILE;
		}
		
		$this->upload		= $_FILES[ $upload_control ];
		
		$this->id_user		= $id_user;
		$this->index		= $index;
		$this->settings		= GetSiteSettings(array('video_folder', 'video_max_size', 'use_video_approve', 'video_max_count',
								'video_max_size', 'video_folder', 'use_ffmpeg', 'path_to_ffmpeg', 
								'flv_output_dimension', 'flv_output_preset', 'flv_output_profile', 'flv_output_fps', 'flv_output_gop',
								'flv_output_video_bit_rate', 'flv_output_audio_sampling_rate', 'flv_output_audio_bit_rate', 'flv_output_foto_dimension',
								'flv_grab_photo_at_second'));
		$this->ext_array	= $VIDEO_EXT_ARRAY;
		$this->type_array	= $VIDEO_TYPE_ARRAY;
		$this->max_size		= getFileSizeFromString($this->settings['video_max_size']);
		$this->err_ext		= $lang['err']['invalid_video_ext'] . implode(', ', $this->ext_array);
		$this->err_type		= $lang['err']['invalid_video_type'] . implode(', ', $this->type_array);
		$this->err_size		= str_replace('#SIZE#', $this->settings['video_max_size'], $lang['err']['invalid_video_size']);
		
		// get form input
		$this->getMultimediaFormInput();
		
		// check $_FILES error
		if ($err = $this->checkUploadError($this->data['id_file'])) {
			return $err;
		}
		
		$folder				= $this->settings['video_folder'];
		
		$use_approve		= $admin_mode ? 0 : (int) $this->settings['use_video_approve'];
		
		// this was prepended to the error message
		// $lang['err']['upload_err']
		
		// check form input
		if ($err = $this->checkMultimediaFormInput()) {
			return $err;
		}
		
		if ($this->upload['error'] == UPLOAD_ERR_NO_FILE) {
			$this->updateMultimediaRecord();
			return 'OK_' . $lang['err']['video_info_updated'];
		}
		
		if (!is_uploaded_file($this->upload['tmp_name'])) {
			return $lang['err']['upload_err'];
		}
		
		if ($err = $this->checkFileType()) {
			return $err;
		}
		
		// check file size
		if ($this->upload['size'] > $this->max_size) {
			return $this->err_size;
		}
		
		// move to target folder
		$new_file_name = $this->GetNewFileName($this->upload['name'], $id_user);
		$new_file_path = $this->site_path.$folder.'/'.$new_file_name;
		
		if (!move_uploaded_file($this->upload['tmp_name'], $new_file_path)) {
			return $lang['err']['move_uploaded_file'];
		}
		
		@chmod($new_file_path, 0644); // SN uses 755 but 644 is sufficient
		
		// delete old files
		if ($this->data['id_file']) {
			$this->DeleteUploadedFiles('v', $this->data['id_file'], $id_user, $admin_mode, false);
		}
		
		// convert to H.264 in MP4 container
		$new_file_name_arr = explode('.', $new_file_name);
		
		// $flv_name = $new_file_name_arr[0].'.flv';
		// $flv_path = $config['site_path'].$folder.'/'.$flv_name;
		$mp4_temp_name = $new_file_name_arr[0].'-temp.mp4';
		$mp4_temp_path = $this->site_path.$folder.'/'.$mp4_temp_name;
		$mp4_name = $new_file_name_arr[0].'-out.mp4';
		$mp4_path = $this->site_path.$folder.'/'.$mp4_name;
		
		// rs: always convert to standardized video quality
		// convert video to target format
		// rs original SN   : @exec($settings['path_to_ffmpeg'].' -y -i '.$new_file_path.' -s '.$settings['flv_output_dimension'].' -acodec mp3 -ar '.$settings['flv_output_audio_sampling_rate'].' -ab '.$settings['flv_output_audio_bit_rate'].' '.$flv_path, $res);
		// rs customized flv: @exec($settings['path_to_ffmpeg'].' -y -i '.$new_file_path.' -s '.$settings['flv_output_dimension'].' -ar '.$settings['flv_output_audio_sampling_rate'].' -ab '.$settings['flv_output_audio_bit_rate'].'k '.$flv_path, $res);
		// rs h264 for ffmpeg 0.6.5: @exec($settings['path_to_ffmpeg'].' -y -i '.$new_file_path.' -s '.$settings['flv_output_dimension'].' -vcodec libx264 -vpre medium -vpre baseline -acodec libfaac -ar '.$settings['flv_output_audio_sampling_rate'].' '$mp4_path, $res);
		// rs webm:    ffmpeg -i "$SOURCE" -vpre libvpx_vp8-360p -b 1700k -an -pass 1 -f webm -threads 0 /dev/null
		// rs webm: && ffmpeg -i "$SOURCE" -vpre libvpx_vp8-360p -b 1700k -pass 2 -acodec libvorbis -ab 128k -ar 44100 -threads 0 "$TARGET"
		// rs: and now the current command for H.264 conversion
		$ffmpeg = $this->settings['path_to_ffmpeg'].' -y -i '.$new_file_path.' -strict experimental -acodec aac -ac 2';
		$ffmpeg.= ' -ar '.$this->settings['flv_output_audio_sampling_rate'];
		$ffmpeg.= ' -ab '.$this->settings['flv_output_audio_bit_rate'];
		$ffmpeg.= ' -vcodec libx264 -s '.$this->settings['flv_output_dimension'];
		$ffmpeg.= ' -preset:v '.$this->settings['flv_output_preset'];
		$ffmpeg.= ' -profile:v '.$this->settings['flv_output_profile'];
		$ffmpeg.= ' -r '.$this->settings['flv_output_fps'];
		$ffmpeg.= ' -g '.$this->settings['flv_output_gop'];
		$ffmpeg.= ' -b:v '.$this->settings['flv_output_video_bit_rate'];
		$ffmpeg.= ' '.$mp4_temp_path;
		$res = array();
		@exec($ffmpeg, $res);
		
		// move MOOV atom to beginning of file for progressive download
		@exec('/usr/local/bin/qt-faststart '.$mp4_temp_path.' '.$mp4_path, $res);
		@unlink($mp4_temp_path);
		
		// extract thumbnail(s)
		// rs old SN: @exec($settings['path_to_ffmpeg'].' -y -i '.$new_file_path.' -an -ss 00:00:00 -t 00:00:01 -r 1 -s '.$settings['flv_output_foto_dimension'].' '.$config['site_path'].$folder.'/'.$new_file_name_arr[0].'%d.jpg', $res);
		// rs alternative: 1 or 2 sec after start with itsoffset
		// ffmpeg -y -itsoffset -1 -i test.mpg -vcodec mjpeg -vframes 1 -an -f image2 -s 320x240 test.jpg
		// doc suggests: -f image2 but some tutorial prefer -f rawvideo
		$thumb_path = $this->site_path.$folder.'/'.$new_file_name_arr[0].'1.jpg';
		@exec($this->settings['path_to_ffmpeg'].' -y -itsoffset -'.(int)$this->settings['flv_grab_photo_at_second'].' -i '.$new_file_path.' -vcodec mjpeg -vframes 1 -an -f image2 -s '.$this->settings['flv_output_foto_dimension'].' '.$thumb_path, $res);
		
		// database update
		$status = empty($use_approve) ? '1' : '0';
		
		$this->updateMultimediaRecord($new_file_name, 'v', $status);
		/*
		if ($this->data['id_file']) {
			$this->dbconn->Execute(
				'UPDATE '.USER_UPLOAD_TABLE.' SET
						upload_path = ?, upload_type = "a", allow = ?, file_type = ?, status = ?, user_comment = ?,
						is_gallary = ?, id_gallery = ?
				  WHERE id_user = ? AND id = ?',
				array($new_file_name, (string)$this->data['upload_allow'], $this->upload['type'], (string)$status, $this->data['user_comment'],
					(string)$this->data['is_gallary'], $this->data['id_gallery'], $id_user, $this->data['id_file']));
		} else {
			$this->dbconn->Execute(
				'INSERT INTO '.USER_UPLOAD_TABLE.' SET
						id_user = ?, upload_path = ?, upload_type = "v", allow = ?, file_type = ?, status = ?,
						user_comment = ?, id_album = ?, is_gallary = ?, id_gallery = ?',
				array($id_user, $new_file_name, (string)$this->data['upload_allow'], $this->upload['type'], (string)$status,
					$this->data['user_comment'], $this->data['id_album'], (string)$this->data['is_gallary'], $this->data['id_gallery']));
		}
		*/
		
		return 'OK_'.$lang['err']['video_saved'];
	}
	
	
	function getMultimediaFormInput()
	{
		$this->data					= array();
		
		if ($this->index === null) {
			$this->data['id_file']		= isset($_POST['id_file'])			? (int) $_POST['id_file'] : '';
			$this->data['id_album']		= isset($_POST['id_album'])			? (int) $_POST['id_album'] : '';
			$this->data['user_comment']	= isset($_POST['user_comment']) 	? stripn(FormFilter($_POST['user_comment'])) : '';
			$this->data['upload_allow']	= isset($_POST['upload_allow']) 	? $_POST['upload_allow'] : '';
			$this->data['is_gallary'] 	= empty($_POST['is_gallary'])		? '0' : '1';
			$this->data['id_gallery'] 	= isset($_POST['id_gallery'])		? (int) $_POST['id_gallery'] : '';
		} else {
			$id_files					= isset($_POST['id_files'])			? $_POST['id_files']: '';
			$id_albums					= isset($_POST['id_albums'])		? $_POST['id_albums']: '';
			$user_comments				= isset($_POST['user_comments'])	? $_POST['user_comments']: '';
			$upload_allows				= isset($_POST['upload_allow'])		? $_POST['upload_allow'] : '';
			
			$this->data['id_file']		= is_array($id_files)				? (int) $id_files[ $this->index ] : '';
			$this->data['id_album']		= is_array($id_albums)				? (int) $id_albums[ $this->index ] : '';
			$this->data['user_comment']	= is_array($user_comments)			? stripn(FormFilter($user_comments[ $this->index ])) : '';
			$this->data['upload_allow']	= is_array($upload_allows)			? $upload_allows[ $this->index ] : '';
			$this->data['is_gallary'] 	= '0';	// not in use on admin upload form
			$this->data['id_gallery'] 	= '';	// not in use on admin upload form
		}
	}
	
	function checkMultimediaFormInput()
	{
		global $lang;
		
		$err = '';
		
		if ($temp = BadWordsCont($this->data['user_comment'], 6)) {
			$err = $temp;
		} elseif (check_filter($this->data['user_comment'])) {
			$err = $lang['err']['info_finding_1'];
		}
		
		return $err;
	}
	
	
	function updateMultimediaRecord($new_file_name='', $upload_type='', $status='0')
	{
		if ($this->data['id_file'])
		{
			if ($new_file_name)
			{
				$this->dbconn->Execute(
					'UPDATE '.USER_UPLOAD_TABLE.' SET
							upload_path = ?, allow = ?, file_type = ?, status = ?, user_comment = ?,
							is_gallary = ?, id_gallery = ?
					  WHERE id_user = ? AND id = ?',
					array($new_file_name, (string)$this->data['upload_allow'], $this->upload['type'], (string)$status, $this->data['user_comment'],
						(string)$this->data['is_gallary'], $this->data['id_gallery'], $this->id_user, $this->data['id_file']));
			}
			else
			{
				$this->dbconn->Execute(
					'UPDATE '.USER_UPLOAD_TABLE.' 
						SET allow = ?, user_comment = ?, is_gallary = ?, id_gallery = ?
					  WHERE id_user = ? AND id = ?',
					array($this->data['upload_allow'], $this->data['user_comment'], (string)$this->data['is_gallary'],
						$this->data['id_gallery'], $this->id_user, $this->data['id_file']));
			}
		}
		else
		{
			$this->dbconn->Execute(
				'INSERT INTO '.USER_UPLOAD_TABLE.' SET
						id_user = ?, upload_path = ?, upload_type = ?, allow = ?, file_type = ?, status = ?,
						user_comment = ?, id_album = ?, is_gallary = ?, id_gallery = ?',
				array($this->id_user, $new_file_name, $upload_type, (string)$this->data['upload_allow'], $this->upload['type'], (string)$status,
					$this->data['user_comment'], $this->data['id_album'], (string)$this->data['is_gallary'], $this->data['id_gallery']));
		}
	}
	
	function checkPostMaxSize()
	{
		if (empty($_POST)) {
			// check content length vs. post_max_size
			$POST_MAX_SIZE = getFileSizeFromString(ini_get('post_max_size'));
			if ((int)$_SERVER['CONTENT_LENGTH'] > $POST_MAX_SIZE && $POST_MAX_SIZE > 0) {
				return 'The uploaded file is too big. Please try with a smaller file.';
			}
		}
		return '';
	}
	
	function checkUploadError($id = null)
	{
		global $lang;
		
		switch ($this->upload['error']) {
			case UPLOAD_ERR_INI_SIZE:
				return str_replace('#MAX#', ini_get('upload_max_filesize'), $lang['err']['UPLOAD_ERR_INI_SIZE']);
			break;
			case UPLOAD_ERR_FORM_SIZE:
				$MAX_FILE_SIZE = number_format($_POST['MAX_FILE_SIZE'] / (1024 * 1024), 1) . ' MB';
				return str_replace('#MAX_FILE_SIZE#', $MAX_FILE_SIZE, $lang['err']['UPLOAD_ERR_FORM_SIZE']);
			break;
			case UPLOAD_ERR_PARTIAL:
				return $lang['err']['UPLOAD_ERR_PARTIAL'];
			break;
			case UPLOAD_ERR_NO_FILE:
				if (empty($id)) return $lang['err']['UPLOAD_ERR_NO_FILE'];
			break;
			case UPLOAD_ERR_NO_TMP_DIR:
				return $lang['err']['UPLOAD_ERR_NO_TMP_DIR'];
			break;
			case UPLOAD_ERR_CANT_WRITE:
				return $lang['err']['UPLOAD_ERR_CANT_WRITE'];
			break;
			case UPLOAD_ERR_EXTENSION:
				return $lang['err']['UPLOAD_ERR_EXTENSION'];
			break;
		}
		return '';
	}
	
	function checkFileType()
	{
		// get extension
		$filename_arr = explode('.', $this->upload['name']);
		$ext = strtolower($filename_arr[count($filename_arr) - 1]);
		
		// check extension
		if (!in_array($ext, $this->ext_array)) {
			return str_replace('#EXT#', $ext, $this->err_ext);
		}
		
		// check mime type
		// flash uploads all files as application/octet-stream
		if (!isset($_GET['act']) || $_GET['act'] != 'flash') {
			if (!in_array($this->upload['type'], $this->type_array)) {
				return str_replace('#TYPE#', $this->upload['type'], $this->err_type);
			}
		}
		
		return '';
	}
	
	function checkWidthAndHeight()
	{
		$upload_info = GetImageSize($this->upload['tmp_name']);
		
		$err = '';
		
		if ($upload_info[0] > $this->max_width) {
			if ($err) $err .= '<br><br>';
			$err .= str_replace('#WIDTH#', $this->max_width, $this->err_width);
		}
		
		if ($upload_info[1] > $this->max_height) {
			if ($err) $err .= '<br><br>';
			$err .= str_replace('#HEIGHT#', $this->max_height, $this->err_height);
		}
		
		return $err;
	}
	
	// @todo: convert club and blog upload and dispose
	
	function UploadImages($upload, $id_user, $upload_type, $id_file='', $user_comment='', $upload_allow='', $admin_mode=0, $id_album='', $is_gallary='', $id_gallery='')
	{
		global $lang;
		
		// first check $_FILES error
		switch ($upload['error']) {
			case UPLOAD_ERR_INI_SIZE:
				return str_replace('#MAX#', ini_get('upload_max_filesize'), $lang['err']['UPLOAD_ERR_INI_SIZE']);
			break;
			case UPLOAD_ERR_FORM_SIZE:
				$MAX_FILE_SIZE = number_format($_POST['MAX_FILE_SIZE'] / (1024 * 1024), 1) . ' MB';
				return str_replace('#MAX_FILE_SIZE#', $MAX_FILE_SIZE, $lang['err']['UPLOAD_ERR_FORM_SIZE']);
			break;
			case UPLOAD_ERR_PARTIAL:
				return $lang['err']['UPLOAD_ERR_PARTIAL'];
			break;
			case UPLOAD_ERR_NO_FILE:
				if ($id_file == '') {
					return $lang['err']['UPLOAD_ERR_NO_FILE'];
				}
			break;
			case UPLOAD_ERR_NO_TMP_DIR:
				return $lang['err']['UPLOAD_ERR_NO_TMP_DIR'];
			break;
			case UPLOAD_ERR_CANT_WRITE:
				return $lang['err']['UPLOAD_ERR_CANT_WRITE'];
			break;
			case UPLOAD_ERR_EXTENSION:
				return $lang['err']['UPLOAD_ERR_EXTENSION'];
			break;
		}
		
		switch ($upload_type)
		{
			case 'icon':
				$folder			= $this->settings['icons_folder'];
				$max_width		= (int) $this->settings['icon_max_width'];
				$max_height		= (int) $this->settings['icon_max_height'];
				$max_size		= getFileSizeFromString($this->settings['icon_max_size']);
				$err_ext		= $lang['err']['invalid_icon_ext'] . implode(', ', $this->IMG_EXT_ARRAY);
				$err_type		= $lang['err']['invalid_icon_type'] . implode(', ', $this->IMG_TYPE_ARRAY);
				$err_size		= str_replace('#SIZE#', $this->settings['icon_max_size'], $lang['err']['invalid_icon_size']);
				$err_width		= str_replace('#WIDTH#', $this->settings['icon_max_width'], $lang['err']['invalid_icon_width']);
				$err_height		= str_replace('#HEIGHT#', $this->settings['icon_max_height'], $lang['err']['invalid_icon_height']);
				$use_approve	= (int) $this->settings['use_icon_approve'];
			break;
			
			case 'f':
				$folder			= $this->settings['photos_folder'];
				$max_width		= (int) $this->settings['photo_max_width'];
				$max_height		= (int) $this->settings['photo_max_height'];
				$max_size		= getFileSizeFromString($this->settings['photo_max_size']);
				$err_ext		= $lang['err']['invalid_photo_ext'] . implode(', ', $this->IMG_EXT_ARRAY);
				$err_type		= $lang['err']['invalid_photo_type'] . implode(', ', $this->IMG_TYPE_ARRAY);
				$err_size		= str_replace('#SIZE#', $this->settings['photo_max_size'], $lang['err']['invalid_photo_size']);
				$err_width		= str_replace('#WIDTH#', $max_width, $lang['err']['invalid_photo_width']);
				$err_height		= str_replace('#HEIGHT#', $max_height, $lang['err']['invalid_photo_height']);
				$use_approve	= (int) $this->settings['use_photo_approve'];
			break;
			
			case 'club':
				$folder			= $this->settings['club_uploads_folder'];
				$max_width		= (int) $this->settings['club_photo_max_width'];
				$max_height		= (int) $this->settings['club_photo_max_height'];
				$max_size		= getFileSizeFromString($this->settings['club_photo_max_size']);
				$err_ext		= $lang['err']['invalid_photo_ext'] . implode(', ', $this->IMG_EXT_ARRAY);
				$err_type		= $lang['err']['invalid_photo_type'] . implode(', ', $this->IMG_TYPE_ARRAY);
				$err_size		= str_replace('#SIZE#', $this->settings['club_photo_max_size'], $lang['err']['invalid_photo_size']);
				$err_width		= str_replace('#WIDTH#', $max_width, $lang['err']['invalid_photo_width']);
				$err_height		= str_replace('#HEIGHT#', $max_height, $lang['err']['invalid_photo_height']);
				$use_approve	= (int) $this->settings['use_photo_approve'];
				$up_type		= 'f'; // special for club
			break;
			
			case 'blog':
				$folder			= $this->settings['blog_folder'];
				$max_width		= (int) $this->settings['photo_max_width'];
				$max_height		= (int) $this->settings['photo_max_height'];
				$max_size		= getFileSizeFromString($this->settings['photo_max_size']);
				$err_ext		= $lang['err']['invalid_photo_ext'] . implode(', ', $this->IMG_EXT_ARRAY);
				$err_type		= $lang['err']['invalid_photo_type'] . implode(', ', $this->IMG_TYPE_ARRAY);
				$err_size		= str_replace('#SIZE#', $this->settings['photo_max_size'], $lang['err']['invalid_photo_size']);
				$err_width		= str_replace('#WIDTH#', $max_width, $lang['err']['invalid_photo_width']);
				$err_height		= str_replace('#HEIGHT#', $max_height, $lang['err']['invalid_photo_height']);
				$use_approve	= (int) $this->settings['use_photo_approve'];
				$up_type		= 'f'; // special for blog
			break;
		}
		
		if ($admin_mode == 1) {
			$use_approve = 0;
		}
		
		if (MM_APPLICANT_DISABLE_ICON_APPROVE == 1) {
			$use_approve = 0;
		}
		
		// this was prepended to the error message
		// $lang['err']['upload_err']
		
		if (!is_uploaded_file($upload['tmp_name'])) {
			return $lang['err']['upload_err'];
		}
		
		if ($this->safe_mode_used) {
			$new_temp_path = $this->GetTempUploadFile($upload['name']);
			if (move_uploaded_file($upload['tmp_name'], $new_temp_path)) {
				$upload['tmp_name'] = $new_temp_path;
			} else {
				return $lang['err']['move_uploaded_file'];
			}
		}
		
		// get extension
		$filename_arr = explode('.', $upload['name']);
		$ext = strtolower($filename_arr[count($filename_arr) - 1]);
		
		// check extension
		if (!in_array($ext, $this->IMG_EXT_ARRAY)) {
			return str_replace('#EXT#', $ext, $err_ext);
		}
		
		// check mime type
		// flash uploads all files as application/octet-stream
		if (!isset($_GET['act']) || $_GET['act'] != 'flash') {
			if (!in_array($upload['type'], $this->IMG_TYPE_ARRAY)) {
				return str_replace('#TYPE#', $upload['type'], $err_type);
			}
		}
		
		// check file size
		if (!$this->settings['use_image_resize'] && $upload['size'] > $max_size) {
			return $err_size;
		}
		
		$err = '';
		
		// if we using picture resize => resize picture
		if ($this->settings['use_image_resize']) {
			//VP
			//$this->ReSizeImage($upload['tmp_name'], $max_width, $max_height);
			$this->ReSizeWithoutCropImage($upload['tmp_name'], $max_width, $max_height);
			$upload['size'] = filesize($upload['tmp_name']);
		}
		
		// check width and height
		$upload_info = GetImageSize($upload['tmp_name']);
		
		if ($upload_info[0] > $max_width) {
			if ($err) $err .= '<br><br>';
			$err .= str_replace('#WIDTH#', $max_width, $err_width);
		}
		
		if ($upload_info[1] > $max_height) {
			if ($err) $err .= '<br><br>';
			$err .= str_replace('#HEIGHT#', $max_height, $err_height);
		}
		
		// return errors
		if ($err) {
			return $err;
		}
		
		// move to target folder
		$new_file_name = $this->GetNewFileName($upload['name'], $id_user);
		$upload_path = $this->site_path.$folder.'/'.$new_file_name;
		
		if (rename($upload['tmp_name'], $upload_path))
		{
			// create thumb if gd used
			if ($this->gd_used)
			{
				// addded by redesign
				if ($upload_type == 'icon')
				{
					// big thumb
					$big_thumb_upload_path = $this->site_path.$folder.'/big_thumb_'.$new_file_name;
					copy($upload_path, $big_thumb_upload_path);
					
					if (file_exists($big_thumb_upload_path)) {
						if (($upload_info[0] <= $this->settings['big_thumb_max_width']) && ($upload_info[1] <= $this->settings['big_thumb_max_height'])) {
							$this->ReSizeWithoutCropImage($big_thumb_upload_path, $this->settings['big_thumb_max_width'], $this->settings['big_thumb_max_height'], 1);
						} else {
							$this->ReSizeImage($big_thumb_upload_path, $this->settings['big_thumb_max_width'], $this->settings['big_thumb_max_height'], 1);
						}
					}
					
					// main thumb
					$main_thumb_upload_path = $this->site_path.$folder.'/main_thumb_'.$new_file_name;
					copy($upload_path, $main_thumb_upload_path);
					
					if (file_exists($main_thumb_upload_path)) {
						if (($upload_info[0] <= 56) && ($upload_info[1] <= 71)) {
							$this->ReSizeWithoutCropImage($main_thumb_upload_path, 56, 71, 1);
						} else {
							$this->ReSizeImage($main_thumb_upload_path, 56, 71, 1);
						}
					}
				}
				
				// "normal" thumb
				$thumb_upload_path = $this->site_path.$folder.'/thumb_'.$new_file_name;
				copy($upload_path, $thumb_upload_path);
				
				if (file_exists($thumb_upload_path)) {
					if (($upload_info[0] <= $this->settings['thumb_max_width']) && ($upload_info[1] <= $this->settings['thumb_max_height'])) {
						$this->ReSizeWithoutCropImage($thumb_upload_path, $this->settings['thumb_max_width'], $this->settings['thumb_max_height'], 1);
					} else {
						$this->ReSizeImage($thumb_upload_path, $this->settings['thumb_max_width'], $this->settings['thumb_max_height'], 1);
					}
				}
			}
			
			// delete old files if any
			$this->DeleteUploadedFiles($upload_type, $id_file, $id_user, $admin_mode);
			
			switch ($upload_type)
			{
				case 'icon':
					
					if ($use_approve) {
						$strSQL =
							'UPDATE '.USERS_TABLE.' 
								SET icon_path = "", icon_path_temp = "thumb_'.$new_file_name.'", big_icon_path = "big_thumb_'.$new_file_name.'" 
							  WHERE id = ?';
						$this->dbconn->Execute($strSQL, array($id_user));
					} else {
						$strSQL =
							'UPDATE '.USERS_TABLE.' 
								SET icon_path = "thumb_'.$new_file_name.'", icon_path_temp = "", big_icon_path = "big_thumb_'.$new_file_name.'" 
							  WHERE id = ?';
						$this->dbconn->Execute($strSQL, array($id_user));
					}
				break;
				
				case 'f':
					
					// insert entry into db
					$status = $use_approve ? '0' : '1';
					
					if ($admin_mode == 1) {
						$strSQL =
							'UPDATE '.USER_UPLOAD_TABLE.'
								SET upload_path = ?, upload_type = "f", allow = ?, file_type = ?, status = ?, user_comment = ?
							  WHERE id_user = ? AND id = ?';
							$this->dbconn->Execute($strSQL, array(
								$new_file_name, (string)$upload_allow, $upload['type'], (string)$status, $user_comment,
								$id_user, $id_file
							));
					} else {
						$strSQL =
							'INSERT INTO '.USER_UPLOAD_TABLE.'
								SET id_user = ?, upload_path = ?, upload_type = "f", allow = ?, file_type = ?,
									status = ?, user_comment = ?, id_album = ?, is_gallary = ?, id_gallery = ?';
						$this->dbconn->Execute($strSQL, array(
							$id_user, $new_file_name, (string)$upload_allow, $upload['type'],
							(string)$status, $user_comment, $id_album, (string)$is_gallary, $id_gallery
						));
					}
				
				break;
				
				case 'club':
					
					$status = '1';
					
					if ($id_file == 'change')
					{
						$rs = $this->dbconn->Execute('SELECT id FROM '.CLUB_UPLOADS_TABLE.' WHERE id_club = ? AND club_icon = "1"', array((string)$upload_allow));
						
						if ($rs->fields[0] > 0) {
							$strSQL =
								'UPDATE '.CLUB_UPLOADS_TABLE.'
									SET id_user = ?, upload_path = ?, upload_type = ?, file_type = ?, status = ?
								  WHERE id_club = ? AND club_icon = "1"';
							$this->dbconn->Execute($strSQL, array($id_user, $new_file_name, $up_type, $upload['type'], (string)$status, (string)$upload_allow));
						} else {
							$strSQL =
								'INSERT INTO '.CLUB_UPLOADS_TABLE.'
									SET id_club = ?, id_user = ?, upload_path = ?, upload_type = ?, file_type = ?, status = ?, club_icon = "1", comment = ?';
							$this->dbconn->Execute($strSQL, array((string)$upload_allow, $id_user, $new_file_name, $up_type, $upload['type'], (string)$status, $user_comment));
						}
					}
					else
					{
						$club_icon = $id_file;
						$strSQL =
							'INSERT INTO '.CLUB_UPLOADS_TABLE.'
								SET id_club = ?, id_user = ?, upload_path = ?, upload_type = ?, file_type = ?, status = ?, club_icon = ?, comment = ?';
						$this->dbconn->Execute($strSQL, array((string)$upload_allow, $id_user, $new_file_name, $up_type, $upload['type'], (string)$status, $club_icon, $user_comment));
					}
				
				break;
				
				case 'blog':
					
					$status = $use_approve ? '0' : '1';
					
					$strSQL = 'INSERT INTO '.BLOG_UPLOADS_TABLE.' SET id_post = ?, upload_path = ?, upload_type = ?, file_type = ?, status = ?';
					$this->dbconn->Execute($strSQL, array((string)$upload_allow, $new_file_name, $up_type, $upload['type'], (string)$status));
					
				break;
			}
			
			return 'OK';
		}
		
		return $lang['err']['upload_err'];
	}
	
	// @todo: convert and dispose
	
	function UploadSiteLogo($upload)
	{
		global $lang;
		
		$folder = $this->settings['photos_folder'];

		if (!is_uploaded_file($upload['tmp_name'])) {
			return $lang['err']['upload_err'];
		}

		if ($this->safe_mode_used) {
			$new_temp_path = $upload['name'];
			if (move_uploaded_file($upload['tmp_name'], $new_temp_path)){
				$upload['tmp_name'] = $new_temp_path;
			}
		}

		$upload['size'] = filesize($upload['tmp_name']);

		$filename_arr = explode('.', $upload['name']);
		$ext = strtolower($filename_arr[count($filename_arr) - 1]);
		
		$err = '';
		
		if (!in_array($upload['type'], $this->IMG_TYPE_ARRAY) || !in_array($ext, $this->IMG_EXT_ARRAY)) {
			if (!$err) {
				$err = $lang['err']['upload_err'].': <br>';
			} else {
				$err .= '<br>';
			}
			$err .= $err_type;
		}
		if ($err){
			return $err;
		}
		
		$new_file_name = $this->GetNewDefaultFileName($upload['name'], 'site_logo');
		$upload_path = $this->site_path.$folder.'/'.$new_file_name;
		
		if (rename($upload['tmp_name'], $upload_path)) {
			$this->FileToPNG($upload_path);
			return;
		}
		
		return $lang['err']['upload_err'];
	}
	
	
	// @todo: convert and dispose
	
	function UploadDefaultImages($upload, $upload_type, $gender)
	{
		global $lang;
		
		$max_width = $this->settings['thumb_max_width'];
		$max_height = $this->settings['thumb_max_height'];

		switch ($upload_type) {
			case 'icon':
				$folder			= $this->settings['icons_folder'];
				$max_size		= getFileSizeFromString($this->settings['icon_max_size']);
				$err_ext		= $lang['err']['invalid_icon_ext'] . implode(', ', $this->IMG_EXT_ARRAY);
				$err_type		= $lang['err']['invalid_icon_type'] . implode(', ', $this->IMG_TYPE_ARRAY);
				$err_size		= str_replace('#SIZE#', $this->settings['icon_max_size'], $lang['err']['invalid_icon_size']);
				$err_width		= str_replace('#WIDTH#', $max_width, $lang['err']['invalid_photo_width']);
				$err_height		= str_replace('#HEIGHT#', $max_height, $lang['err']['invalid_photo_height']);
			break;
			case 'f':
				$folder			= $this->settings['photos_folder'];
				$max_size		= getFileSizeFromString($this->settings['photo_max_size']);
				$err_ext		= $lang['err']['invalid_photo_ext'] . implode(', ', $this->IMG_EXT_ARRAY);
				$err_type		= $lang['err']['invalid_photo_type'] . implode(', ', $this->IMG_TYPE_ARRAY);
				$err_size		= str_replace('#SIZE#', $this->settings['photo_max_size'], $lang['err']['invalid_photo_size']);
				$err_width		= str_replace('#WIDTH#', $max_width, $lang['err']['invalid_photo_width']);
				$err_height		= str_replace('#HEIGHT#', $max_height, $lang['err']['invalid_photo_height']);
			break;
		}
		
		if(!is_uploaded_file($upload['tmp_name'])){
			return $lang['err']['upload_err'];
		}
		
		if ($this->safe_mode_used){
			$new_temp_path = $this->GetTempUploadFile($upload['name']);
			if (move_uploaded_file($upload['tmp_name'],$new_temp_path)) {
				$upload['tmp_name'] = $new_temp_path;
			}
		}
		
		// try to resize default image anyway
		if ($this->gd_used) {
			$this->ReSizeImage($upload['tmp_name'], $max_width, $max_height);
			$upload['size'] = filesize($upload['tmp_name']);
		}
		
		$err = '';
		
		// get width/height and size info and check on errors
		$upload_info = GetImageSize($upload['tmp_name']);
		
		if ($upload_info[0] > $max_width) {
			if (!$err) {
				$err = $lang['err']['upload_err'].': <br>';
			} else {
				$err .= '<br>';
			}
			$err .= $err_width.'width';
		}
		
		if ($upload_info[1] > $max_height) {
			if (!$err) {
				$err = $lang['err']['upload_err'].': <br>';
			} else {
				$err .= '<br>';
			}
			$err .= $err_height.'height';
		}
		
		$filename_arr = explode('.', $upload['name']);
		$nr = count($filename_arr);
		$ext = strtolower($filename_arr[$nr - 1]);
		
		if (!in_array($upload['type'], $this->IMG_TYPE_ARRAY) || !in_array($ext, $this->IMG_EXT_ARRAY)) {
			if (!$err) {
				$err = $lang['err']['upload_err'].': <br>';
			} else {
				$err .= '<br>';
			}
			$err .= str_replace('#EXT#', $ext, $err_ext);
		}
		
		if ($upload['size'] > $max_size) {
			if (!$err) {
				$err = $lang['err']['upload_err'].': <br>';
			} else {
				$err .= '<br>';
			}
			$err .= $err_size;
		}
		
		// return errrors if it was found
		if ($err) {
			return $err;
		}
		
		// rename file
		$new_file_name = $this->GetNewDefaultFileName($upload['name'], $upload_type, $gender);
		
		// get dist path for image
		$upload_path = $this->site_path.$folder.'/'.$new_file_name;
		
		if (rename($upload['tmp_name'], $upload_path)) {
			// create thumb if gd used
			
			if ($upload_type == 'f') {
				$strSQL = 'UPDATE '.SETTINGS_TABLE.' SET value = "'.$new_file_name.'" WHERE name = "photos_default"';
				$this->dbconn->Execute($strSQL);
			} elseif ($upload_type == 'icon') {
				$strSQL = 'UPDATE '.SETTINGS_TABLE.' SET value = "'.$new_file_name.'" WHERE name = "icon_'.$gender.'_default"';
				$this->dbconn->Execute($strSQL);
			}
		} else {
			return $lang['err']['upload_err'];
		}
	}
	
	
	function DeleteUploadedFiles($type_upload, $id_file='', $id_user='', $admin_mode=0, $delete_record=true)
	{
		global $config;
		
		switch ($type_upload)
		{
			case 'icon':
				
				$folder = $this->settings['icons_folder'];
				
				$rs = $this->dbconn->Execute('SELECT icon_path, icon_path_temp FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
				$file = strlen($rs->fields[0]) ? $rs->fields[0] : $rs->fields[1];
				
				if (strlen($file) > 0) {
					$old_file = str_replace('thumb_', '', $this->site_path.$folder.'/'.$file);
					if (file_exists($old_file)) {
						unlink($old_file);
					}
					
					$old_thumb_file = $this->site_path.$folder.'/'.$file;
					if (file_exists($old_thumb_file)) {
						unlink($old_thumb_file);
					}
					
					$old_main_file = $this->site_path.$folder.'/main_'.$file;
					if (file_exists($old_main_file)) {
						unlink($old_main_file);
					}
					
					$old_big_file = $this->site_path.$folder.'/big_'.$file;
					if (file_exists($old_big_file)) {
						unlink($old_big_file);
					}
					
					$this->dbconn->Execute('UPDATE '.USERS_TABLE.' SET icon_path = "", icon_path_temp = "" WHERE id = ?', array($id_user));
				}
				
				return;
			
			break;
			
			case 'f':
				
				$folder = $this->settings['photos_folder'];
				
				$id_album = '';
				
				$rs = $this->dbconn->Execute(
					'SELECT upload_path, id_album FROM '.USER_UPLOAD_TABLE.' WHERE id = ? AND id_user = ?', array($id_file, $id_user));
				
				$file = $rs->fields[0];
				
				if (strlen($file) > 0) {
					$id_album = $rs->fields[1];
					
					$old_file = $this->site_path.$folder.'/'.$file;
					@unlink($old_file);
					
					$old_thumb_file = $this->site_path.$folder.'/thumb_'.$file;
					@unlink($old_thumb_file);
					
					$old_edit_file = $this->site_path.$folder.'/edit_'.$file;
					@unlink($old_edit_file);
					
					if ($admin_mode) {
						$strSQL =
							'UPDATE '.USER_UPLOAD_TABLE.'
								SET is_gallary = "0", id_gallery = "0", gallery_rate = "0", is_adult = "0"
							  WHERE id = ? AND id_user = ?';
						$this->dbconn->Execute($strSQL, array($id_file, $id_user));
					} elseif ($delete_record) {
						$this->dbconn->Execute('DELETE FROM '.USER_UPLOAD_TABLE.' WHERE id = ? AND id_user = ?', array($id_file, $id_user));
					}
					
					if ($delete_record) {
						$this->dbconn->Execute('DELETE FROM '.GALLERY_RATING_TABLE.' WHERE id_upload = ?', array($id_file));
					}
				}
				
				return $id_album;
			
			break;
			
			case 'a':
				
				$folder = $this->settings['audio_folder'];
				
				$id_album = '';
				
				if ($admin_mode) {
					$rs = $this->dbconn->Execute('SELECT upload_path, id_album FROM '.USER_UPLOAD_TABLE.' WHERE id = ?', array($id_file));
				} else {
					$rs = $this->dbconn->Execute('SELECT upload_path, id_album FROM '.USER_UPLOAD_TABLE.' WHERE id = ? AND id_user = ?', array($id_file, $id_user));
				}
				
				$file = $rs->fields[0];
				
				if (strlen($file) > 0)
				{
					$id_album = $rs->fields[1];
					
					// delete originally uploaded file
					@unlink($this->site_path.$folder.'/'.$file);
					
					// delete converted flv and mp4 files
					$old_file_name_arr = explode('.', $file);
					@unlink($this->site_path.$folder.'/'.$old_file_name_arr[0].'.flv');
					@unlink($this->site_path.$folder.'/'.$old_file_name_arr[0].'-out.mp4');
					
					// delete jpg thumbs
					for ($i = 1; $i <= 9; $i++) {
						// the original ffmpeg command created more than one thumb for some odd reason
						@unlink($config['site_path'].$folder.'/'.$old_file_name_arr[0].$i.'.jpg');
					}
					
					if ($delete_record) {
						if ($admin_mode) {
							$this->dbconn->Execute('DELETE FROM '.USER_UPLOAD_TABLE.' WHERE id = ?', array($id_file));
						} else {
							$this->dbconn->Execute('DELETE FROM '.USER_UPLOAD_TABLE.' WHERE id = ? AND id_user = ?', array($id_file, $id_user));
						}
					}
				}
				
				return $id_album;
			
			break;
			
			case 'v':
				
				$folder = $this->settings['video_folder'];
				
				$id_album = '';
				
				if ($admin_mode) {
					$rs = $this->dbconn->Execute('SELECT upload_path, id_album FROM '.USER_UPLOAD_TABLE.' WHERE id = ?', array($id_file));
				} else {
					$rs = $this->dbconn->Execute('SELECT upload_path, id_album FROM '.USER_UPLOAD_TABLE.' WHERE id = ? AND id_user = ?', array($id_file, $id_user));
				}
				
				$file = $rs->fields[0];
				
				if (strlen($file) > 0)
				{
					$id_album = $rs->fields[1];
					
					@unlink($this->site_path.$folder.'/'.$file);
					
					if ($delete_record) {
						if ($admin_mode) {
							$this->dbconn->Execute('DELETE FROM '.USER_UPLOAD_TABLE.' WHERE id = ?', array($id_file));
						} else {
							$this->dbconn->Execute('DELETE FROM '.USER_UPLOAD_TABLE.' WHERE id = ? AND id_user = ?', array($id_file, $id_user));
						}
					}
				}
				
				return $id_album;
			
			break;
		}
	}
	
	
	function ReSizeImage($path, $width_to, $height_to, $thumb=0){

		if (file_exists($path) && $this->gd_used) {
			// if such image exists and gd lib is loaded
			$path_full = str_replace('thumb_', '', $path);
			// start added exotic
			$path_full = str_replace('big_', '', $path_full);
			$path_full = str_replace('main_', '', $path_full);
			// end added exotic
			$image_info = GetImageSize($path);
			$image_width = $image_info[0];
			$image_height = $image_info[1];
			$image_type = $image_info[2];
			if ($image_width > $width_to || $image_height > $height_to) {
				if ($this->settings['use_image_resize'] || $thumb) {
					$st = $this->ReSizeAction($path, $image_type, $image_width, $image_height, $width_to, $height_to);
					if ($this->settings['use_photo_logo'] && $thumb) {
						$this->mergePix($path_full, $this->waterlogo_path, $path_full, 3, 40);
					}
				} else {
					$st = true;
				}
				if ($st) {
					return true;
				} else {
					return false;
				}
			} else {
				if ($this->settings['use_photo_logo'] && $thumb) {
					$this->mergePix($path_full, $this->waterlogo_path, $path_full, 3, 40);
				}
				return true;
			}
		} else {
			return true;
		}
	}

	function ReSizeAction($path, $type, $image_width, $image_height, $width_to, $height_to)
	{
		switch ($type) {
			case '1': // GIF
				$srcImage = @ImageCreateFromGif($path);
			break;
			case '2': // JPG
				$srcImage = @imagecreatefromjpeg($path);
			break;
			case '3': // PNG
				$srcImage = @imagecreatefrompng($path);
			break;
			case '6': // WBMP
				$srcImage = @imagecreatefromwbmp($path);
			break;
		}
		
		if ($srcImage)
		{
			$srcWidth = ImageSX($srcImage);
			$srcHeight = ImageSY($srcImage);
			$k_1 = $srcWidth / $width_to;
			$k_2 = $srcHeight / $height_to;
			if ($k_1 < $k_2) {	// $k_1->1
				$resized_image_width = $width_to;
				$resized_image_height = round($srcHeight / $k_1);
				$src_x = 0;
				$src_y = round($k_1 * abs($resized_image_height - $height_to) / 2);
				$sample_image_width = round($k_1 * $width_to);
				$sample_image_height = round($k_1 * $height_to);
			} elseif ($k_1 >= $k_2) {	// $k_2->1
				$resized_image_height = $height_to;
				$resized_image_width = round($srcWidth / $k_2);
				$src_x = round($k_2 * abs($resized_image_width - $width_to) / 2);
				$src_y = 0;
				$sample_image_width = round($k_2 * $width_to);
				$sample_image_height = round($k_2 * $height_to);
			}
			
			$destImage = imagecreatetruecolor($width_to, $height_to);
			$bg_color = imagecolorallocate($destImage, 255, 255, 255);
			imagefilledrectangle($destImage, 0, 0, $width_to, $height_to, $bg_color);
			imagecopyresampled($destImage, $srcImage, 0, 0, $src_x, $src_y, $width_to, $height_to, $sample_image_width, $sample_image_height);

			switch ($type) {
				case '1': // GIF
					if (function_exists('imagegif')) {
						ImageGif($destImage, $path);
					} else {
						return false;
					}
				break;
				case '2': // JPG
					if (function_exists('imagejpeg')) {
						ImageJpeg($destImage, $path, 80);
					} else {
						return false;
					}
				break;
				case '3': // PNG
					if (function_exists('imagepng')) {
						ImagePng($destImage, $path, 8);
					} else {
						return false;
					}
				break;
				case '6': // WBMP
					if (function_exists('imagewbmp')) {
						ImageWbmp($destImage, $path);
					} else {
						return false;
					}
				break;
			}

			ImageDestroy($srcImage);
			ImageDestroy($destImage);
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function ReSizeWithoutCropImage($path, $width_to, $height_to, $thumb=0)
	{
		if (file_exists($path) && $this->gd_used){
			// if such image exists and gd lib is loaded
			$path_full = str_replace('thumb_', '', $path);
			$path_full = str_replace('big_', '', $path_full);
			$path_full = str_replace('main_', '', $path_full);
			$image_info = GetImageSize($path);
			$image_width = $image_info[0];
			$image_height = $image_info[1];
			$image_type = $image_info[2];
			if ($this->settings['use_image_resize'] || $thumb) {
				$success = $this->ReSizeWithoutCropAction($path, $image_type, $image_width, $image_height, $width_to, $height_to);
				if ($this->settings['use_photo_logo'] && $thumb) {
					$this->mergePix($path_full, $this->waterlogo_path, $path_full, 3, 40);
				}
			} else {
				$success = true;
			}
			return $success;
		} else {
			return true;
		}
	}

	function ReSizeWithoutCropAction($path, $type, $image_width, $image_height, $width_to, $height_to)
	{
		switch ($type) {
			case '1': // GIF
				$srcImage = @ImageCreateFromGif($path);
			break;
			case '2': // JPG
				$srcImage = @imagecreatefromjpeg($path);
			break;
			case '3': // PNG
				$srcImage = @imagecreatefrompng($path);
			break;
			case '6': // WBMP
				$srcImage = @imagecreatefromwbmp($path);
			break;
		}
		
		if ($srcImage)
		{
			$srcWidth = ImageSX($srcImage);
			$srcHeight = ImageSY($srcImage);
			if ($image_width > $width_to) {
				$image_height = round($image_height * $width_to / $image_width);
				$image_width = $width_to;
			}
			if ($image_height > $height_to) {
				$image_width = round($image_width * $height_to / $image_height);
				$image_height = $height_to;
			}
			
			$destImage = @imagecreatetruecolor($width_to, $height_to);
			// $destImage = @imagecreatetruecolor($image_width, $image_height);
			
			if ($image_width < $width_to){
				$x = round(($width_to - $image_width) / 2);
			} else {
				$x = 0 ;
			}
			
			if ($image_height < $height_to) {
				$y = round(($height_to - $image_height) / 2);
			} else {
				$y = 0;
			}
			
			$r = $g = $b = 255;
			$color = ImageColorAllocate($destImage, $r, $g, $b);
			imagefilledrectangle($destImage, 0, 0, $width_to, $height_to, $color);
			imagecopyresampled($destImage, $srcImage, $x, $y, 0, 0, $image_width, $image_height, $srcWidth, $srcHeight);
			// imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $image_width, $image_height, $srcWidth, $srcHeight);
			
			switch ($type) {
				case '1': // GIF
					if (function_exists('imagegif')) {
						ImageGif($destImage, $path);
					} else {
						return false;
					}
				break;
				case '2': // JPG
					if (function_exists('imagejpeg')) {
						ImageJpeg($destImage, $path, 80);
					} else {
						return false;
					}
				break;
				case '3': // PNG
					if (function_exists('imagepng')) {
						ImagePng($destImage, $path, 8);
					} else {
						return false;
					}
				break;
				case '6': // WBMP
					if (function_exists('imagewbmp')) {
						ImageWbmp($destImage, $path);
					} else {
						return false;
					}
				break;
			}
			
			ImageDestroy($srcImage);
			ImageDestroy($destImage);
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function GetResizeParametrs($path, $width_to='', $height_to='', $image_width='', $image_height='')
	{
		// used then gdlib not allowed on the server
		// return new parametrs of width and height for resizing pict
		if (!$width_to) {
			$width_to = $this->settings['thumb_max_width'];
		}
		if (!$height_to) {
			$height_to = $this->settings['thumb_max_height'];
		}
		if (!$image_width || !$image_height) {
			$image_info = GetImageSize($path);
			$image_width = $image_info[0];
			$image_height = $image_info[1];
		}
		if ($image_width>$width_to) {
			$image_height = round($image_height * $width_to / $image_width);
			$image_width = $width_to;
		}
		if ($image_height > $height_to) {
			$image_width = round($image_width * $height_to / $image_height);
			$image_height = $height_to;
		}
		
		$ret_arr['width'] = $image_width;
		$ret_arr['height'] = $image_height;
		
		return $ret_arr;
	}
	
	function GetResizeParametrsStr($path, $width_to='', $height_to='', $image_width='', $image_height='')
	{
		$param = $this->GetResizeParametrs($path, $width_to, $height_to, $image_width, $image_height);
		return ' width="'.$param['width'].'" height="'.$param['height'].'"';
	}
	
	function GetTempUploadFile($file_name)
	{
		$path_to_image = '';
		
		$matches = array();
		
		$forbidden_chars = strtr("$/\\:*?&quot;'&lt;&gt;|`", array('&amp;' => '&', '&quot;' => '"', '&lt;' => '<', '&gt;' => '>'));
		
		if (get_magic_quotes_gpc()) {
			$file_name = stripslashes($file_name);
		}
		
		$picture_name = strtr($file_name, $forbidden_chars, str_repeat('_', strlen("$/\\:*?&quot;'&lt;&gt;|`")));
		
		if (!preg_match("/(.+)\.(.*?)\Z/", $picture_name, $matches)) {
			$matches[1] = 'invalid_fname';
			$matches[2] = 'xxx';
		}
		
		$prefix = 'mHTTP_temp_';
		$suffix = $matches[2];
		
		do {
			$seed = substr(md5(microtime().getmypid()), 0, 8);
			$path_to_image = $this->file_temp_path.'/'. $prefix . $seed . '.' . $suffix;
		} while (file_exists($path_to_image));

		return $path_to_image;
	}
	
	function GetNewFileName($name, $user_id)
	{
		$ex_arr = explode('.', $name);
		$extension = $ex_arr[count($ex_arr) - 1];
		$new_file_name = $user_id.'_'.substr(md5(microtime().getmypid()), 0, 8).'.'.$extension;
		return $new_file_name;
	}
	
	function GetNewDefaultFileName($file_name, $upload_type, $gender)
	{
		switch ($upload_type) {
			case 'icon':
				$prefix = 'default_icon_'.$gender;
			break;
			case 'f':
				$prefix = 'default_photo';
			break;
			case 'site_logo':
				$prefix = 'water_logo';
			break;
		}
		$ex_arr = explode('.', $file_name);
		$extension = $ex_arr[count($ex_arr) - 1];
		$new_file_name = $prefix.'.'.$extension;
		return $new_file_name;
	}
	
	function mergePix($sourcefile, $insertfile, $targetfile, $pos,$transition)
	{
		$image_info = GetImageSize($insertfile);
		$image_type = $image_info[2];
		
		switch ($image_type) {
			case '1': // GIF
				$insertfile_id = @ImageCreateFromGif($insertfile);
			break;
			case '2': // JPG
				$insertfile_id = @imagecreatefromjpeg($insertfile);
			break;
			case '3': // PNG
				$insertfile_id = @imagecreatefrompng($insertfile);
			break;
			case '6': // WBMP
				$insertfile_id = @imagecreatefromwbmp($insertfile);
			break;
		}

		$image_info = GetImageSize($sourcefile);
		$image_type = $image_info[2];

		switch ($image_type) {
			case '1': // GIF
				$sourcefile_id = @ImageCreateFromGif($sourcefile);
			break;
			case '2': // JPG
				$sourcefile_id = @imagecreatefromjpeg($sourcefile);
			break;
			case '3': // PNG
				$sourcefile_id = @imagecreatefrompng($sourcefile);
			break;
			case '6': // WBMP
				$sourcefile_id = @imagecreatefromwbmp($sourcefile);
			break;
		}
		
		//Get the sizes of both pix
		$sourcefile_width = imageSX($sourcefile_id);
		$sourcefile_height = imageSY($sourcefile_id);
		$insertfile_width = imageSX($insertfile_id);
		$insertfile_height = imageSY($insertfile_id);
		
		//middle
		if ($pos == 0) {
			$dest_x = ($sourcefile_width / 2) - ($insertfile_width / 2);
			$dest_y = ($sourcefile_height / 2) - ($insertfile_height / 2);
		}
		//top left
		if ($pos == 1) {
			$dest_x = 0;
			$dest_y = 0;
		}
		//top right
		if ($pos == 2) {
			$dest_x = $sourcefile_width - $insertfile_width;
			$dest_y = 0;
		}
		//bottom right
		if ($pos == 3) {
			$dest_x = $sourcefile_width - $insertfile_width;
			$dest_y = $sourcefile_height - $insertfile_height;
		}
		//bottom left
		if ($pos == 4) {
			$dest_x = 0;
			$dest_y = $sourcefile_height - $insertfile_height;
		}
		//top middle
		if ($pos == 5) {
			$dest_x = ($sourcefile_width - $insertfile_width) / 2;
			$dest_y = 0;
		}
		//middle right
		if ($pos == 6) {
			$dest_x = $sourcefile_width - $insertfile_width;
			$dest_y = ($sourcefile_height / 2) - ($insertfile_height / 2);
		}
		//bottom middle
		if ($pos == 7) {
			$dest_x = ($sourcefile_width - $insertfile_width) / 2;
			$dest_y = $sourcefile_height - $insertfile_height;
		}
		//middle left
		if ($pos == 8) {
			$dest_x = 0;
			$dest_y = ($sourcefile_height / 2) - ($insertfile_height / 2);
		}
		
		//The main thing : merge the two pix
		imageCopyMerge($sourcefile_id, $insertfile_id, $dest_x, $dest_y, 0, 0, $insertfile_width, $insertfile_height, $transition);

		switch ($image_type) {
			case '1': // GIF
				if (function_exists('imagegif')) {
					ImageGif($sourcefile_id, $targetfile);
				} else {
					return false;
				}
			break;
			case '2': // JPG
				if (function_exists('imagejpeg')) {
					ImageJpeg($sourcefile_id, $targetfile, 80);
				} else {
					return false;
				}
			break;
			case '3': // PNG
				if (function_exists('imagepng')) {
					ImagePng($sourcefile_id, $targetfile, 8);
				} else {
					return false;
				}
				break;
			case '6': // WBMP
				if (function_exists('imagewbmp')) {
					ImageWbmp($sourcefile_id, $targetfile);
				} else {
					return false;
				}
				break;
		}
		
		ImageDestroy($sourcefile_id);
		ImageDestroy($insertfile_id);
	}
	
	function UploadSuccessImages($upload, $id_story, $num)
	{
		global $lang;
		
		$folder = $this->settings['success_folder'];
		
		$max_width	= $this->settings['photo_max_width'];
		$max_height	= $this->settings['photo_max_height'];
		$max_size	= getFileSizeFromString($this->settings['photo_max_size']);
		$err_type	= $lang['err']['invalid_photo_type'] . implode(', ', $this->IMG_TYPE_ARRAY);
		$err_size	= str_replace('#SIZE#', $this->settings['photo_max_size'], $lang['err']['invalid_photo_size']);
		$err_width	= str_replace('#WIDTH#', $max_width, $lang['err']['invalid_photo_width']);
		$err_height	= str_replace('#HEIGHT#', $max_height, $lang['err']['invalid_photo_height']);
		
		$err = '';
		
		if (!is_uploaded_file($upload['tmp_name'])) {
			return $lang['err']['upload_err'];
		}
		
		if ($this->safe_mode_used) {
			$new_temp_path = $this->GetTempUploadFile($upload['name']);
			if (move_uploaded_file($upload['tmp_name'], $new_temp_path)) {
				$upload['tmp_name'] = $new_temp_path;
			} else {
				return $lang['err']['upload_err'];
			}
		}
		
		// if we using picture resize: traing to resize picture
		if ($this->settings['use_image_resize']) {
			$this->ReSizeImage($upload['tmp_name'], $max_width, $max_height);
			$upload['size'] = filesize($upload['tmp_name']);
		}
		
		// get width/height and size info and check on errors
		$upload_info = GetImageSize($upload['tmp_name']);
		
		if ($upload_info[0] > $max_width) {
			if (!$err) {
				$err = $lang['err']['upload_err'].': <br>';
			} else {
				$err .= '<br>';
			}
			$err .= $err_width;
		}
		if ($upload_info[1] > $max_height) {
			if (!$err) {
				$err = $lang['err']['upload_err'].': <br>';
			} else {
				$err .= '<br>';
			}
			$err .= $err_height;
		}
		
		$filename_arr = explode('.', $upload['name']);
		$ext = strtolower($filename_arr[count($filename_arr) - 1]);
		
		if (!in_array($upload['type'], $this->IMG_TYPE_ARRAY) || !in_array($ext, $this->IMG_EXT_ARRAY)) {
			if (!isset($err)) {
				$err = $lang['err']['upload_err'].': <br>';
			} else {
				$err .= '<br>';
			}
			$err .= $err_type;
		}
		if ($upload['size'] > $max_size) {
			if (!$err) {
				$err = $lang['err']['upload_err'].': <br>';
			} else {
				$err .= '<br>';
			}
			$err .= $err_size;
		}
		
		// return errrors if it was found
		if ($err == '') {
			// rename file
			$new_file_name = $this->GetNewFileName($upload['name'], $id_story);
			// get dist path for image
			$upload_path = $this->site_path.$folder.'/'.$new_file_name;
			
			if (copy($upload['tmp_name'], $upload_path)){
				// create thumb if gd used
				if ($this->gd_used) {
					$thumb_upload_path = $this->site_path.$folder.'/thumb_'.$new_file_name;
					copy($upload['tmp_name'], $thumb_upload_path);
					if (file_exists($thumb_upload_path)) {
						if (($upload_info[0] <= $this->settings['thumb_max_width']) && ($upload_info[1] <= $this->settings['thumb_max_height'])) {
							$this->ReSizeWithoutCropImage($thumb_upload_path, $this->settings['thumb_max_width'], $this->settings['thumb_max_height'], 1);
						} else {
							$this->ReSizeImage($thumb_upload_path, $this->settings['thumb_max_width'], $this->settings['thumb_max_height'], 1);
						}
					}
				}
				unlink($upload['tmp_name']);
				$this->dbconn->Execute('UPDATE '.SUCCESS_STORIES_TABLE.' SET image_path_'.$num.' = "'.$new_file_name.'" WHERE id = "'.$id_story.'"');
			} else {
				$err = $lang['err']['upload_err'];
			}
		}
		return $err;
	}
	
	function FileToPNG($file_name)
	{
		$image_info = GetImageSize($file_name);
		$image_type = $image_info[2];
		
		switch ($image_type) {
			case '1': // GIF
				$new_image = @ImageCreateFromGif($file_name);
			break;
			case '2': // JPG
				$new_image = @imagecreatefromjpeg($file_name);
			break;
			case '3': // PNG
				$new_image = @imagecreatefrompng($file_name);
			break;
			case '6': // WBMP
				$new_image = @imagecreatefromwbmp($file_name);
			break;
		}
		
		$ex_arr = explode('.', $file_name);
		$extension = $ex_arr[count($ex_arr) - 1];
		$file_name = str_replace('.'.$extension, '.png', $file_name);
		
		if (function_exists('imagepng')) {
			ImagePng($new_image, $file_name);
		}
		ImageDestroy($new_image);
		return;
	}
}

?>