<?php

class CityCatalog
{
	var $config;
	var $dbconn;
	var $lang;
	var $smarty;
	var $CATEGORY_TABLE = LOCATION_CATALOG_CATEGORY;
	var $ITEM_TABLE = LOCATION_CATALOG_ITEMS;
	var $ITEM_IMAGES_TABLE = LOCATION_CATALOG_ITEM_IMAGES;
	var $ITEM_VIDEO_TABLE = LOCATION_CATALOG_ITEM_VIDEOS;
	var $ITEM_COMMENTS_TABLE = LOCATION_CATALOG_ITEM_COMMENTS;
	var $SETTINGS_TABLE = SETTINGS_TABLE;
	var $category_numpage = 30;
	var $IMG_TYPE_ARRAY;			// = array("image/jpeg", "image/pjpeg", "image/gif", "image/bmp", "image/tiff", "image/png", "image/x-png");
	var $IMG_EXT_ARRAY;				// = array("jpeg", "jpg", "gif", "bmp", "wbmp", "tiff", "png");
	
	
	function CityCatalog($config, $dbconn, $smarty, $lang='')
	{
		global $IMG_TYPE_ARRAY, $IMG_EXT_ARRAY, $VIDEO_TYPE_ARRAY, $VIDEO_EXT_ARRAY;
		
		$this->config = $config;
		$this->dbconn = $dbconn;
		$this->lang = $lang;
		$this->smarty = $smarty;
		
		// import settings from config_admin.php
		$this->IMG_TYPE_ARRAY = $IMG_TYPE_ARRAY;
		$this->IMG_EXT_ARRAY = $IMG_EXT_ARRAY;
		$this->VIDEO_TYPE_ARRAY = $VIDEO_TYPE_ARRAY;
		$this->VIDEO_EXT_ARRAY = $VIDEO_EXT_ARRAY;
	}
	
	
	//--------------------
	// category functions
	//--------------------
	
	function category_list($page=1, $numpage="", $only_active=0, $usr_where_str="")
	{
		$folder = GetSiteSettings('location_folder');
		
		if (!$numpage) {
			$numpage = $this->category_numpage;
		}
		
		$lim_min = ($page - 1) * $numpage;
		$lim_max = $numpage;
		$limit_str = ' LIMIT '.$lim_min.', '.$lim_max;
		
		if ($only_active == 1) {
			$where_str = ' WHERE status="1"';
		} else {
			$where_str = ' WHERE 1';
		}
		
		if (strlen($usr_where_str)) {
			$where_str .= ' AND '.$usr_where_str;
		}
		
		$strSQL = 'SELECT DISTINCT id, name, comment, status, sorter, icon_path FROM ' . $this->CATEGORY_TABLE . $where_str . ' ORDER BY sorter ' . $limit_str;
		$rs = $this->dbconn->Execute($strSQL);
		
		$i = 0;
		$spr_arr = array();
		
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$spr_arr[$i]['number'] = ($page - 1) * $numpage + ($i + 1);
			$spr_arr[$i]['id'] = $row['id'];
			$spr_arr[$i]['name'] = stripslashes($row['name']);
			$spr_arr[$i]['comment'] = (strlen(utf8_decode($row['comment'])) > 200) ? utf8_substr(stripslashes($row['comment']), 0, 200) . '...' : stripslashes($row['comment']);
			$spr_arr[$i]['comment_all'] = nl2br(stripslashes($row['comment']));
			$spr_arr[$i]['status'] = $row['status'] ? '+' : '-';
			
			if ($row['icon_path'] && file_exists($this->config['site_path'].$folder.'/'.$row['icon_path'])) {
				$spr_arr[$i]['icon_path'] = $this->config['site_root'].$folder.'/'.$row['icon_path'];
			}
			
			if ($row['icon_path'] && file_exists($this->config['site_path'].$folder.'/thumb_'.$row['icon_path'])) {
				$spr_arr[$i]['thumb_icon_path'] = $this->config['site_root'].$folder.'/thumb_'.$row['icon_path'];
			}
			
			$rs->MoveNext();
			$i++;
		}
		
		return $spr_arr;
	}
	
	
	function category_all_list($only_active=0)
	{
		$where = '';
		
		if ($only_active == 1) {
			$where = ' WHERE status="1"';
		}
		
		$strSQL = 'SELECT id, name, comment, status, sorter, icon_path FROM ' . $this->CATEGORY_TABLE . $where . ' ORDER BY sorter';
		$rs = $this->dbconn->Execute($strSQL);
		
		$i = 0;
		$spr_arr = array();
		
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$spr_arr[$i]['number'] = $i+1;
			$spr_arr[$i]['id'] = $row['id'];
			$spr_arr[$i]['name'] = stripslashes($row['name']);
			$rs->MoveNext();
			$i++;
		}
		
		return $spr_arr;
	}
	
	
	function category_count()
	{
		$cnt = $this->dbconn->getOne('SELECT COUNT(*) FROM '.$this->CATEGORY_TABLE);
		
		return intval($cnt);
	}
	
	
	function category_item($id)
	{
		// settings
		$folder = GetSiteSettings('location_folder');
		
		$strSQL = "SELECT id, name, comment, status, sorter, icon_path FROM ".$this->CATEGORY_TABLE." WHERE id='".$id."'";
		$rs = $this->dbconn->Execute($strSQL);
		
		$row = $rs->GetRowAssoc(false);
		
		$spr_arr = array();
		$spr_arr['id'] = $row['id'];
		$spr_arr['name'] = stripslashes($row['name']);
		$spr_arr['comment'] = stripslashes($row['comment']);
		$spr_arr['sorter'] = $row['sorter'];
		$spr_arr['status'] = $row['status'];
		
		if ($row['icon_path'] && file_exists($this->config['site_path'].$folder.'/'.$row['icon_path'])) {
			$spr_arr['icon_path'] = $this->config['site_root'].$folder.'/'.$row['icon_path'];
		} else {
			$spr_arr['icon_path'] = '';
		}
		
		if ($row['icon_path'] && file_exists($this->config['site_path'].$folder.'/thumb_'.$row['icon_path'])) {
			$spr_arr['thumb_icon_path'] = $this->config['site_root'].$folder.'/thumb_'.$row['icon_path'];
		} else {
			$spr_arr['thumb_icon_path'] = '';
		}
		
		return $spr_arr;
	}
	
	
	function category_add($data)
	{
		// icon
		$icon_path = '';
		
		if (is_uploaded_file($data['icon_path']['tmp_name'])) {
			$ret = $this->upload_picture($data['icon_path'], 'cat', true);
			
			if ($ret['err']) {
				return $ret['err'];
			}
			
			if ($ret['file_name']) {
				$icon_path = $ret['file_name'];
			}
		}
		
		// insert
		$strSQL = 'INSERT INTO '.$this->CATEGORY_TABLE.' (name, comment, status, icon_path) VALUES ("'.addslashes($data['name']).'", "'.addslashes($data['comment']).'", "'.$data['status'].'", "'.$icon_path.'")';
		$this->dbconn->Execute($strSQL);
		
		// sorter
		$rs = $this->dbconn->Execute('SELECT MAX(id) FROM '.$this->CATEGORY_TABLE);
		$rs_os = $this->dbconn->Execute('SELECT MAX(sorter)+1 FROM '.$this->CATEGORY_TABLE);
		
		$this->CategorySorter($data['sorter'], $rs_os->fields[0], $rs->fields[0]);
		
		return;
	}
	
	
	function category_change($data)
	{
		// icon
		$strSQL = 'SELECT icon_path FROM '.$this->CATEGORY_TABLE.' WHERE id = "'.$data['id'].'"';
		$rs = $this->dbconn->Execute($strSQL);
		
		$old_icon = $rs->fields[0];
		
		if (is_uploaded_file($data['icon_path']['tmp_name'])) {
			$ret = $this->upload_picture($data['icon_path'], 'cat', true);
			
			if ($ret['err']) {
				return $ret['err'];
			}
			
			if ($ret['file_name']) {
				$icon_path = $ret['file_name'];
				$this->delete_picture($old_icon);
			} else {
				$icon_path = $old_icon;
			}
		} else {
			$icon_path = $old_icon;
		}
		
		// sorter
		$old_sorter = $this->dbconn->getOne('SELECT sorter FROM '.$this->CATEGORY_TABLE.' WHERE id = "'.$data['id'].'"');
		$this->CategorySorter($data['sorter'], $old_sorter, $data['id']);
		
		// update
		$strSQL = 'UPDATE '.$this->CATEGORY_TABLE.' SET name="'.addslashes($data['name']).'", comment="'.addslashes($data['comment']).'", status="'.$data['status'].'", icon_path="'.$icon_path.'" where id="'.$data['id'].'" ';
		$this->dbconn->Execute($strSQL);
		
		return;
	}
	
	
	function category_picture_delete($id)
	{
		$old_icon = $this->dbconn->GetOne('SELECT icon_path FROM '.$this->CATEGORY_TABLE.' WHERE id = "'.$id.'"');
		
		$this->delete_picture($old_icon);
		
		$this->dbconn->Execute('UPDATE '.$this->CATEGORY_TABLE.' SET icon_path="" WHERE id="'.$id.'"');
		
		return;
	}
	
	
	function category_delete($id)
	{
		$this->category_picture_delete($id);
		
		$this->dbconn->Execute('DELETE FROM '.$this->CATEGORY_TABLE.' WHERE id="'.$id.'"');
		
		return;
	}
	
	
	function category_items($id, $only_active=0)
	{
		$where_str .= ' WHERE id_category="'.$id.'"';
		
		if ($only_active == 1) {
			$where_str = ' AND status="1"';
		}
		
		$strSQL = 'SELECT id, name, status, sorter, price FROM ' . $this->ITEM_TABLE . $where_str . ' ORDER BY sorter';
		$rs = $this->dbconn->Execute($strSQL);
		
		$i = 0;
		$spr_arr = array();
		
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$spr_arr[$i]['number'] = $i+1;
			$spr_arr[$i]['id'] = $row['id'];
			$spr_arr[$i]['name'] = stripslashes($row['name']);
			$spr_arr[$i]['price'] = sprintf('%01.2f', $row['price']);
			$spr_arr[$i]['status'] = $row['status'] ? '+' : '-';
			$rs->MoveNext();
			$i++;
		}
		
		return $spr_arr;
	}
	
	//----------------
	// item functions
	//----------------
	
	function items_list($id, $page=1, $numpage='', $only_active=0, $usr_where_str='')
	{
		// settings
		$folder = GetSiteSettings('location_folder');
		
		if (!$numpage) {
			$numpage = $this->category_numpage;
		}
		
		$lim_min = ($page - 1) * $numpage;
		$lim_max = $numpage;
		$limit_str = ' LIMIT '.$lim_min.', '.$lim_max;
		
		$where_str = ' WHERE id_category="'.$id.'"';
		
		if ($only_active == 1) {
			$where_str .= ' AND status="1"';
		}
		
		if (strlen($usr_where_str)) {
			$where_str .= ' AND '.$usr_where_str;
		}
		
		$strSQL = 'SELECT id, name, comment, status, sorter, icon_path, price FROM ' . $this->ITEM_TABLE . $where_str . ' ORDER BY sorter ' . $limit_str;
		$rs = $this->dbconn->Execute($strSQL);
		
		$i = 0;
		$spr_arr = array();
		
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$spr_arr[$i]['number'] = ($page-1) * $numpage + ($i+1);
			$spr_arr[$i]['id'] = $row['id'];
			$spr_arr[$i]['name'] = stripslashes($row['name']);
			$spr_arr[$i]['price'] = sprintf('%01.2f', $row['price']);
			$spr_arr[$i]['comment'] = (strlen(utf8_decode($row['comment']))>300) ? utf8_substr(stripslashes($row['comment']), 0, 300).'...' : stripslashes($row['comment']);
			$spr_arr[$i]['comment_all'] = nl2br(stripslashes($row['comment']));
			$spr_arr[$i]['status'] = $row['status'] ? '+' : '-';
			
			if ($row['icon_path'] && file_exists($this->config['site_path'].$folder.'/thumb_'.$row['icon_path'])) {
				$spr_arr[$i]['thumb_icon_path'] = $this->config['site_root'].$folder.'/thumb_'.$row['icon_path'];
			}
			
			if ($row['icon_path'] && file_exists($this->config['site_path'].$folder.'/'.$row['icon_path'])) {
				$spr_arr[$i]['icon_path'] = $this->config['site_root'].$folder.'/'.$row['icon_path'];
			}
			
			$rs->MoveNext();
			$i++;
		}
		
		return $spr_arr;
	}
	
	
	function items_count($id, $only_active=0)
	{
		$where_str = ' WHERE id_category="'.$id.'"';
		
		if ($only_active == 1) {
			$where_str .= ' AND status="1"';
		}
		
		$cnt = $this->dbconn->getOne('SELECT COUNT(*) FROM '.$this->ITEM_TABLE.$where_str);
		
		return intval($cnt);
	}
	
	
	function items_item($id)
	{
		// settings
		$folder = GetSiteSettings('location_folder');
		
		// read data
		$strSQL = "SELECT id, name, comment, status, sorter, icon_path, video_path, price, id_category FROM ".$this->ITEM_TABLE." WHERE id='".$id."'";
		$rs = $this->dbconn->Execute($strSQL);
		
		$row = $rs->GetRowAssoc(false);
		
		$spr_arr['id'] = $row['id'];
		$spr_arr['name'] = stripslashes($row['name']);
		$spr_arr['comment'] = nl2br(stripslashes($row['comment']));
		$spr_arr['sorter'] = $row['sorter'];
		$spr_arr['status'] = $row['status'];
		$spr_arr['price'] = sprintf('%01.2f', $row['price']);
		$spr_arr['id_category'] = $row['id_category'];
		
		// icon
		if ($row['icon_path'] && file_exists($this->config['site_path'].$folder.'/'.$row['icon_path'])) {
			$spr_arr['icon_path'] = $this->config['site_root'].$folder.'/'.$row['icon_path'];
		} else {
			$spr_arr['icon_path'] = '';
		}
		
		// thumbnail
		if ($row['icon_path'] && file_exists($this->config['site_path'].$folder.'/thumb_'.$row['icon_path'])) {
			$spr_arr['thumb_icon_path'] = $this->config['site_root'].$folder.'/thumb_'.$row['icon_path'];
		} else {
			$spr_arr['thumb_icon_path'] = '';
		}
		
		// video
		if ($row['video_path'] && file_exists($this->config['site_path'].$folder.'/'.$row['video_path'])) {
			$spr_arr['video_path'] = $this->config['site_root'].$folder.'/'.$row['video_path'];
			$spr_arr['video_name'] = $row['video_path'];
		} else {
			$spr_arr['video_path'] = $spr_arr['video_name'] = '';
		}
		
		// image gallery
		$spr_arr['gallery'] = $this->items_gallery_list($id);
		
		return $spr_arr;
	}
	
	
	function items_add($data)
	{
		// icon
		$icon_path = '';
		
		if (is_uploaded_file($data['icon_path']['tmp_name'])) {
			$ret = $this->upload_picture($data['icon_path'], 'item', true);
			
			if ($ret['err']) {
				return $ret['err'];
			}
			
			if ($ret['file_name']) {
				$icon_path = $ret['file_name'];
			}
		}
		
		// video
		$video_path = '';
		
		if (is_uploaded_file($data['video_path']['tmp_name'])) {
			$ret = $this->upload_video($data['video_path'], 'item');
			
			if ($ret['err']) {
				return $ret['err'];
			}
			
			if ($ret['file_name']) {
				$video_path = $ret['file_name'];
			}
		}
		
		// insert
		$strSQL = 'INSERT INTO '.$this->ITEM_TABLE.' (name, comment, status, icon_path, video_path, price, id_category) VALUES ("'.addslashes($data['name']).'", "'.addslashes($data['comment']).'", "'.$data['status'].'", "'.$icon_path.'", "'.$video_path.'", "'.$data['price'].'", "'.$data['id_category'].'")'; 
		$this->dbconn->Execute($strSQL);
		
		// sorter
		$rs = $this->dbconn->Execute('SELECT MAX(id) FROM '.$this->ITEM_TABLE);
		$rs_os = $this->dbconn->Execute('SELECT MAX(sorter)+1 FROM '.$this->ITEM_TABLE);
		$this->ItemSorter($data['sorter'], $rs_os->fields[0], $rs->fields[0], $data['id_category']);
		
		return;
	}
	
	//--------------------
	// update location
	//--------------------
	
	function items_change($data)
	{
		// prepare icon upload
		$old_icon = $this->dbconn->GetOne('SELECT icon_path FROM '.$this->ITEM_TABLE.' WHERE id = "'.$data['id'].'"');
		
		if (is_uploaded_file($data['icon_path']['tmp_name'])) {
			$ret = $this->upload_picture($data['icon_path'], 'item', true);
			
			if ($ret['err']) {
				return $ret['err'];
			}
			
			if ($ret['file_name']) {
				// delete old icon before uploading new one
				$this->delete_picture($old_icon);
				$icon_path = $ret['file_name'];
			} else {
				$icon_path = $old_icon;
			}
		} else {
			$icon_path = $old_icon;
		}
		
		// prepare video upload
		$old_video = $this->dbconn->getOne('SELECT video_path FROM '.$this->ITEM_TABLE.' WHERE id = "'.$data['id'].'"');
		
		if (is_uploaded_file($data['video_path']['tmp_name'])) {
			$ret = $this->upload_video($data['video_path'], 'item');
			
			if ($ret['err']) {
				return $ret['err'];
			}
			
			if ($ret['file_name']) {
				$this->delete_video($old_video);
				$video_path = $ret['file_name'];
			} else {
				$video_path = $old_video;
			}
		} else {
			$video_path = $old_video;
		}
		
		// sorter
		$old_sorter = $this->dbconn->getOne('SELECT sorter FROM '.$this->ITEM_TABLE.' WHERE id = "'.$data['id'].'"');
		$this->ItemSorter($data['sorter'], $old_sorter, $data['id'], $data['id_category']);
		
		// perform update
		$strSQL =
			'UPDATE '.$this->ITEM_TABLE.
			' SET name="'.addslashes($data['name']).'",
				comment="'.addslashes($data['comment']).'",
				status="'.$data['status'].'",
				price="'.$data['price'].'",
				id_category="'.$data['id_category'].'",
				icon_path="'.$icon_path.'",
				video_path="'.$video_path.'"
			WHERE id="'.$data['id'].'"';
		#echo $strSQL; exit;
		$this->dbconn->Execute($strSQL);
		
		return;
	}
	
	
	function items_picture_delete($id)
	{
		$old_icon = $this->dbconn->GetOne('SELECT icon_path FROM '.$this->ITEM_TABLE.' WHERE id = "'.$id.'"');
		
		$this->delete_picture($old_icon);
		
		$this->dbconn->Execute('UPDATE '.$this->ITEM_TABLE.' SET icon_path="" WHERE id="'.$id.'"');
		
		return;
	}
	
	
	function items_video_delete($id)
	{
		$old_video = $this->dbconn->GetOne('SELECT video_path FROM '.$this->ITEM_TABLE.' WHERE id = "'.$id.'"');
		
		$this->delete_video($old_video);
		
		$this->dbconn->Execute('UPDATE '.$this->ITEM_TABLE.' SET video_path="" WHERE id="'.$id.'"');
		
		return;
	}
	
	
	function items_delete($id)
	{
		$this->items_picture_delete($id);
		$this->items_video_delete($id);
		
		$this->dbconn->Execute('DELETE FROM '.$this->ITEM_TABLE.' WHERE id="'.$id.'"');
		
		return;
	}
	
	
	function items_gallery_list($id)
	{
		// settings
		$folder = GetSiteSettings('location_folder');
		
		$strSQL = "SELECT id, image_path FROM ".$this->ITEM_IMAGES_TABLE." WHERE id_location = ? ORDER BY id";
		$rs = $this->dbconn->Execute($strSQL, array($id));
		
		$i = 0;
		$gallary_arr = array();
		
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$gallary_arr[$i]["number"] = $i+1;
			$gallary_arr[$i]["id"] = $row["id"];
			
			#if ($row["image_path"] && file_exists($this->config["site_path"].$folder."/thumb_".$row["image_path"])) {
				$gallary_arr[$i]["thumb_image_path"] = $this->config["site_root"].$folder."/thumb_".$row["image_path"];
			#}
			
			#if ($row["image_path"] && file_exists($this->config["site_path"].$folder."/".$row["image_path"])) {
				$gallary_arr[$i]["image_path"] = $this->config["site_root"].$folder."/".$row["image_path"];
			#}
			
			$rs->MoveNext();
			$i++;
		}
		
		return $gallary_arr;
	}
	
	
	function items_gallery_count($id)
	{
		$cnt = $this->dbconn->getOne('SELECT COUNT(*) FROM '.$this->ITEM_IMAGES_TABLE.' WHERE id_location="'.$id.'"');
		
		return intval($cnt);
	}
	
	
	function items_gallery_image($id_image)
	{
		// settings
		$folder = GetSiteSettings('location_folder');
		
		$image_file = $this->dbconn->Execute('SELECT image_path FROM '.$this->ITEM_IMAGES_TABLE.' WHERE id="'.$id_image.'"');
		
		if ($image_file && file_exists($this->config['site_path'].$folder.'/'.$image_file)) {
			$image_path = $this->config['site_root'].$folder.'/'.$image_file;
		}
		
		return $image_path;
	}
	
	
	function items_gallery_add($image_src, $id_location)
	{
		if (is_uploaded_file($image_src['tmp_name'])) {
			$ret = $this->upload_picture($image_src, 'img', true);
			
			if ($ret['err']) {
				return $ret['err'];
			}
			
			$strSQL = 'INSERT INTO '.$this->ITEM_IMAGES_TABLE.' (id_location , image_path) VALUES ("'.$id_location.'", "'.$ret['file_name'].'")';
			$this->dbconn->Execute($strSQL);
			return $this->dbconn->ErrorMsg();
		}
		
		return '';
	}
	
	
	function items_gallery_delete($id_image)
	{
		$old_icon = $this->dbconn->getOne('SELECT image_path FROM '.$this->ITEM_IMAGES_TABLE.' WHERE id = "'.$id_image.'"');
		
		$this->delete_picture($old_icon);
		
		$this->dbconn->Execute('DELETE FROM '.$this->ITEM_IMAGES_TABLE.' WHERE id="'.$id_image.'"');
		
		return;
	}
	

	//-------------------
	// upload picture
	//-------------------
	
	function upload_picture($icon, $type, $create_thumb)
	{
		// settings
		$settings = GetSiteSettings(array('location_folder', 'location_max_size', 'location_max_height', 
			'location_max_width', 'thumb_max_width', 'thumb_max_height'));
		
		// icon
		$path_to_image = $this->GetTempUploadFile($icon['name']);
		
		$new_file_name = '';
		$err = '';
		
		if (is_uploaded_file($icon['tmp_name']) && move_uploaded_file($icon['tmp_name'], $path_to_image)) {
			$icon['tmp_name'] = $path_to_image;
			
			$this->ReSizeImage($icon['tmp_name'], $settings['location_max_width'], $settings['location_max_height'], 0);
			$icon['size'] = filesize($icon['tmp_name']);
			
			$icon_info = GetImageSize($icon['tmp_name']);
			
			$filename_arr = explode('.', $icon['name']);
			$ext = strtolower($filename_arr[count($filename_arr) - 1]);
			
			if (!in_array($icon['type'], $this->IMG_TYPE_ARRAY) || !in_array($ext, $this->IMG_EXT_ARRAY))
			{
				$err = $this->lang['err']['invalid_photo_type'] . implode(', ', $this->IMG_TYPE_ARRAY);
			}
			elseif ($icon['size'] > getFileSizeFromString($settings['location_max_size']))
			{
				$err = str_replace('#SIZE#', $settings['location_max_size'], $this->lang['err']['invalid_photo_size']);
			}
			elseif ($icon_info[0] > $settings['location_max_width'])
			{
				$err = str_replace('#WIDTH#', $settings['location_max_width'], $this->lang['err']['invalid_photo_width']);
			}
			elseif ($icon_info[1] > $settings['location_max_height'])
			{
				$err = str_replace('#HEIGHT#', $this->settings['location_max_height'], $this->lang['err']['invalid_photo_height']);
			}
			else
			{
				$new_file_name = $type.'_'.date('ymdhis').'.'.$ext;
				$new_file_path = $this->config['site_path'].$settings['location_folder'].'/'.$new_file_name;
				
				if (is_dir($this->config['site_path'].$settings['location_folder']) && is_writeable($this->config['site_path'].$settings['location_folder']))
				{
					if (copy($icon['tmp_name'], $new_file_path)) {
						if ($create_thumb) {
							// create thumb
							$this->ReSizeImage($icon['tmp_name'], $settings['thumb_max_width'], $settings['thumb_max_height'], 1);
							$new_thumb_name = 'thumb_'.$new_file_name;
							copy($icon['tmp_name'], $this->config['site_path'].$settings['location_folder'].'/'.$new_thumb_name);
						}
						unlink($icon['tmp_name']);
					}
				}
				else
				{
					unlink($icon['tmp_name']);
					$err = $this->lang['err']['not_writeable_file'];
				}
			}
		}
		
		$arr['file_name'] = $new_file_name;
		$arr['err'] = $err;
		
		return $arr;
	}
	
	
	//-----------------
	// upload video
	//-----------------
	
	function upload_video($video, $type)
	{
		// settings
		$settings = GetSiteSettings(array('location_folder', 'video_max_size', 'use_ffmpeg', 'path_to_ffmpeg', 'flv_output_dimension', 
			'flv_output_audio_sampling_rate', 'flv_output_audio_bit_rate', 'flv_output_foto_dimension'));
		
		$folder = $settings['location_folder'];
		
	    $path_to_video = GetTempUploadFile($video['name']);
		
		$new_file_name = '';
		$err = '';
		
		if (is_uploaded_file($video['tmp_name']) && move_uploaded_file($video['tmp_name'], $path_to_video))
		{
			$video['tmp_name'] = $path_to_video;
			$video['size'] = filesize($video['tmp_name']);
			$filename_arr = explode('.', $video['name']);
			$nr = count($filename_arr);
			$ext = strtolower($filename_arr[$nr - 1]);
			
			if ((!in_array($video['type'], $this->VIDEO_TYPE_ARRAY)) || (!in_array($ext, $this->VIDEO_EXT_ARRAY)))
			{
				$err = $this->lang['err']['invalid_video_type']."/".$video['type']."/".$ext;
			}
			elseif ($video['size'] > getFileSizeFromString($settings['video_max_size']))
			{
				$err = str_replace('#SIZE#', $settings['video_max_size'], $this->lang['err']['invalid_video_size']);
			}
			else
			{
				$ex_arr = explode('.', $video['name']);
				$extension = $ex_arr[count($ex_arr) - 1];
				
				$new_file_name = $type.'_'.date('ymdhis').'.'.$extension;
				$new_file_path = $this->config['site_path'].$folder.'/'.$new_file_name;
				
				if (is_dir($this->config['site_path'].$folder) && is_writeable($this->config['site_path'].$folder))
				{
					if (copy($video['tmp_name'], $new_file_path))
					{
						unlink($video['tmp_name']);
						@chmod($new_file_name, 0755);
						
						if ($settings['use_ffmpeg'] == 1) {
							$new_file_name_arr = explode('.', $new_file_name);
							$flv_name = $new_file_name_arr[0].'.flv';
							$flv_path = $this->config['site_path'].$folder.'/'.$flv_name;
							$res = array();
							// create .flv file
							@exec($settings['path_to_ffmpeg'].' -y -i '.$new_file_path.' -s '.$settings['flv_output_dimension'].' -acodec mp3 -ar '.$settings['flv_output_audio_sampling_rate'].' -ab '.$settings['flv_output_audio_bit_rate'].' '.$flv_path, $res);
							// create .jpg thumbnail
							@exec($settings['path_to_ffmpeg'].' -i '.$new_file_path.' -an -ss 00:00:00 -t 00:00:01 -r 1 -y -s '.$settings['flv_output_foto_dimension'].' '.$this->config['site_path'].$folder.'/'.$new_file_name_arr[0].'%d.jpg ', $res);
						}
					}
				}
				else
				{
					unlink($video['tmp_name']);
					$err = $this->lang['err']['not_writeable_file'];
				}
			}
		}
		
		$arr['file_name'] = $new_file_name;
		$arr['err'] = $err;
		
		return $arr;
		
		/* Bala's old routine
		
		$path_to_video = $this->GetTempUploadFile($video["name"]);
		
		if (is_uploaded_file($video["tmp_name"]) && move_uploaded_file($video["tmp_name"],$path_to_video))
		{
			$video["tmp_name"] = $path_to_video;
			$video["size"] = filesize($video["tmp_name"]);
			$video_info = GetImageSize($video["tmp_name"]);
			$filename_arr = explode(".", $video["name"]);
			$nr = count($filename_arr);
			$ext = strtolower($filename_arr[$nr-1]);
			$ex_arr = explode(".",$video["name"]);
			$extension = $ex_arr[count($ex_arr)-1];
			$new_file_name = $type."_".date("ymdhis").".".$extension;
			$video_file =$this->config["site_path"].$settings["location_folder"]."/".$new_file_name;
			
			if (is_dir($this->config["site_path"].$settings["location_folder"]) && is_writeable($this->config["site_path"].$settings["location_folder"]))
			{
				if (copy($video["tmp_name"], $video_file))
				{
					unlink($video["tmp_name"]);
				}
			}
			else
			{
				$new_file_name = "";
				unlink($video["tmp_name"]);
				$err = $this->lang["err"]["not_writeable_file"];
			}
		}
		*/
	}
	
	
	//-----------------
	// delete picture file
	//-----------------
	
	function delete_picture($file_name)
	{
		// settings
		$folder = GetSiteSettings('location_folder');
		
		$icon_file = $this->config['site_path'].$folder.'/'.$file_name;
		$thumb_icon_file = $this->config['site_path'].$folder.'/thumb_'.$file_name;
		
		if (file_exists($icon_file) && strlen($file_name) > 0) {
			unlink($icon_file);
		}
		
		if (file_exists($thumb_icon_file) && strlen($file_name) > 0) {
			unlink($thumb_icon_file);
		}
		
		return;
	}
	
	//-----------------
	// delete video file
	//-----------------
	
	function delete_video($file_name)
	{
		// settings
		$folder = GetSiteSettings('location_folder');
		
		$video_file = $this->config['site_path'].$folder.'/'.$file_name;
		
		if (file_exists($video_file) && strlen($file_name) > 0) {
			unlink($video_file);
		}
		
		return;
	}
	
	
	function ReSizeImage($path, $width_to, $height_to, $thumb=0)
	{
		// settings
		$use_image_resize = GetSiteSettings('use_image_resize');
		
		if (file_exists($path) && extension_loaded('gd'))
		{
			$image_info = GetImageSize($path);
			$image_width = $image_info[0];
			$image_height = $image_info[1];
			$image_type = $image_info[2];
			
			if ($image_width > $width_to || $image_height > $height_to)
			{
				if ($use_image_resize) {
					$st = $this->ReSizeAction($path, $image_type, $image_width, $image_height, $width_to, $height_to, $thumb);
				} else {
					$st = true;
				}
				
				if ($st) {
					return true;
				} else {
					return false;
				}
			}
			else
			{
				return true;
			}
		}
		else
		{
			return true;
		}
	}
	
	
	function ReSizeAction($path, $type, $image_width, $image_height, $width_to, $height_to, $thumb=0)
	{
		switch($type)
		{
			case "1" :
				$srcImage = @ImageCreateFromGif($path);
			break;
				
			case "2" :
				$srcImage = @imagecreatefromjpeg($path);
			break;
				
			case "3" :
				$srcImage = @imagecreatefrompng($path);
			break;
				
			case "6" :
				$srcImage = @imagecreatefromwbmp($path);
			break;
		}
				
		if ($srcImage)
		{
			if ($thumb == 1)
			{
				$srcWidth = ImageSX($srcImage);
				$srcHeight = ImageSY($srcImage);
				
				if ($image_width > $width_to) {
					$image_height = round($image_height*$width_to/$image_width);
					$image_width = $width_to;
				}
				
				if ($image_height > $height_to) {
					$image_width = round($image_width*$height_to/$image_height);
					$image_height = $height_to;
				}
				
				$destImage = @imagecreatetruecolor($image_width, $image_height);
				imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $image_width, $image_height, $srcWidth, $srcHeight);
			}
			else
			{
				$srcWidth = ImageSX($srcImage);
				$srcHeight = ImageSY($srcImage);
				$k_1 = $srcWidth/$width_to;
				$k_2 = $srcHeight/$height_to;
				
				if ($k_1 < $k_2)
				{	
					// $k_1->1
					$resized_image_width = $width_to;
					$resized_image_height = round($srcHeight/$k_1);
					$src_x = 0;
					$src_y = round($k_1*abs($resized_image_height - $height_to)/2);
					$sample_image_width = round($k_1*$width_to);
					$sample_image_height = round($k_1*$height_to);
				}
				elseif ($k_1 >= $k_2)
				{
					// $k_2->1
					$resized_image_height = $height_to;
					$resized_image_width = round($srcWidth/$k_2);
					$src_x = round($k_2*abs($resized_image_width - $width_to)/2);
					$src_y = 0;
					$sample_image_width = round($k_2*$width_to);
					$sample_image_height = round($k_2*$height_to);
				}
				
				$destImage = @imagecreatetruecolor($width_to, $height_to);
				$bg_color = imagecolorallocate($destImage, 255, 255, 255);
				imagefilledrectangle($destImage, 0, 0, $width_to, $height_to, $bg_color);
				imagecopyresampled($destImage, $srcImage, 0, 0, $src_x, $src_y, $width_to, $height_to, $sample_image_width, $sample_image_height);
			}
			
			switch($type)
			{
				case "1" :
					ImageGif($destImage, $path);
				break;
					
				case "2" :
					ImageJpeg($destImage, $path);
				break;
					
				case "3" :
					ImagePng($destImage, $path);
				break;
					
				case "6" :
					ImageWbmp($destImage, $path);
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
	
	
	function GetTempUploadFile($file_name)
	{
		$path_to_image = "";
		
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
		
		$prefix = "mHTTP_temp_";
		$suffix = $matches[2];
		
		do {
			$seed = substr(md5(microtime().getmypid()), 0, 8);
			$path_to_image = $this->config["file_temp_path"]."/". $prefix . $seed . '.' . $suffix;
		}
		while (file_exists($path_to_image));
		
		return $path_to_image;
	}
	
	
	function CategorySorter($sorter, $old_sorter, $id="")
	{
		if (!$id) {
			$strSQL = "select id, sorter from ".$this->CATEGORY_TABLE." where sorter >= '".$old_sorter."' order by sorter";
			$rs = $this->dbconn->Execute($strSQL);
			
			while (!$rs->EOF) {
				$rs_up = $this->dbconn->Execute("update ".$this->CATEGORY_TABLE." set sorter = '".($rs->fields[1]-1)."' where id ='".$rs->fields[0]."' ");
				$rs->MoveNext();
			}
			return;
		}
		
		// sorter
		if ($old_sorter < $sorter)
		{
			$strSQL = "select id, sorter from ".$this->CATEGORY_TABLE." where sorter >= '".$old_sorter."' and sorter <= '".$sorter."' order by sorter";
			$rs = $this->dbconn->Execute($strSQL);
			
			while (!$rs->EOF) {
				$rs_up = $this->dbconn->Execute("update ".$this->CATEGORY_TABLE." set sorter = '".($rs->fields[1]-1)."' where id ='".$rs->fields[0]."' ");
				$rs->MoveNext();
			}
			
			// add sorter
			$rs_up = $this->dbconn->Execute("update ".$this->CATEGORY_TABLE." set sorter = '".$sorter."' where id ='".$id."' ");
		}
		elseif ($old_sorter>$sorter)
		{
			$strSQL = "select id, sorter from ".$this->CATEGORY_TABLE." where sorter <= '".$old_sorter."' and sorter >= '".$sorter."' order by sorter";
			$rs = $this->dbconn->Execute($strSQL);
			
			while (!$rs->EOF) {
				$rs_up = $this->dbconn->Execute("update ".$this->CATEGORY_TABLE." set sorter = '".($rs->fields[1]+1)."' where id ='".$rs->fields[0]."' ");
				$rs->MoveNext();
			}
			
			// add sorter
			$rs_up = $this->dbconn->Execute("update ".$this->CATEGORY_TABLE." set sorter = '".$sorter."' where id ='".$id."' ");
		}
		else
		{
			$rs_up = $this->dbconn->Execute("update ".$this->CATEGORY_TABLE." set sorter = '".$sorter."' where id ='".$id."' ");
		}
		return;
	}
	
	
	function ItemSorter($sorter, $old_sorter, $id="", $id_category="")
	{
		if (!$id_category && $id) {
			$strSQL = "select id_category from ".$this->ITEM_TABLE." where id= '".$id."' ";
			$rs = $this->dbconn->Execute($strSQL);
			$id_category=intval($rs->fields[0]);
		}
		
		if (!$id && $id_category) {
			$strSQL = "select id, sorter from ".$this->ITEM_TABLE." where sorter >= '".$old_sorter."' and id_category='".$id_category."' order by sorter";
			$rs = $this->dbconn->Execute($strSQL);
			
			while (!$rs->EOF) {
				$rs_up = $this->dbconn->Execute("update ".$this->ITEM_TABLE." set sorter = '".($rs->fields[1]-1)."' where id ='".$rs->fields[0]."' ");
				$rs->MoveNext();
			}
			return;
		}
		
		// sorter
		if ($old_sorter < $sorter) {
			$strSQL = "select id, sorter from ".$this->ITEM_TABLE." where sorter >= '".$old_sorter."' and sorter <= '".$sorter."' and id_category='".$id_category."' order by sorter";
			$rs = $this->dbconn->Execute($strSQL);
			
			while (!$rs->EOF) {
				$rs_up = $this->dbconn->Execute("update ".$this->ITEM_TABLE." set sorter = '".($rs->fields[1]-1)."' where id ='".$rs->fields[0]."' ");
				$rs->MoveNext();
			}
			
			// add sorter
			$rs_up = $this->dbconn->Execute("update ".$this->ITEM_TABLE." set sorter = '".$sorter."' where id ='".$id."' ");
		}
		elseif ($old_sorter > $sorter)
		{
			$strSQL = "select id, sorter from ".$this->ITEM_TABLE." where sorter <= '".$old_sorter."' and sorter >= '".$sorter."' and id_category='".$id_category."' order by sorter";
			$rs = $this->dbconn->Execute($strSQL);
			
			while (!$rs->EOF) {
				$rs_up = $this->dbconn->Execute("update ".$this->ITEM_TABLE." set sorter = '".($rs->fields[1]+1)."' where id ='".$rs->fields[0]."' ");
				$rs->MoveNext();
			}
			
			// add sorter
			$rs_up = $this->dbconn->Execute("update ".$this->ITEM_TABLE." set sorter = '".$sorter."' where id ='".$id."' ");
		}
		else
		{
			$rs_up = $this->dbconn->Execute("update ".$this->ITEM_TABLE." set sorter = '".$sorter."' where id ='".$id."' ");
		}
		return;
	}
	
	
	function GetCategoryName($id)
	{
		$name = $this->dbconn->getOne('SELECT name FROM '.$this->CATEGORY_TABLE.' WHERE id="'.$id.'"');
		
		return stripslashes($name);
	}
}

?>