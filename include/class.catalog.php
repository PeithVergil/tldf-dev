<?php

class Catalog
{
	var $config;
	var $dbconn;
	var $lang;
	var $smarty;
	var $CATEGORY_TABLE = GIFTSHOP_CATALOG_CATEGORY;
	var $ITEM_TABLE = GIFTSHOP_CATALOG_ITEM;
	var $ITEM_IMAGES_TABLE = GIFTSHOP_CATALOG_ITEM_IMAGES;
	var $ITEM_COMMENTS_TABLE = GIFTSHOP_CATALOG_ITEM_COMMENTS;
	var $SETTINGS_TABLE = SETTINGS_TABLE;
	var $category_numpage = 30;
	var $IMG_TYPE_ARRAY = array('image/jpeg', 'image/pjpeg', 'image/gif', 'image/bmp', 'image/tiff', 'image/png', 'image/x-png');
	var $IMG_EXT_ARRAY = array('jpeg', 'jpg', 'gif', 'bmp', 'wbmp', 'tiff', 'png');

	function Catalog($config, $dbconn, $smarty, $lang='')
	{
		$this->config = $config;
		$this->dbconn = $dbconn;
		$this->lang = $lang;
		$this->smarty = $smarty;
	}
	
	//====================
	// CATEGORY FUNCTIONS
	//====================
	
	function category_list($page=1, $numpage='', $only_active=0, $usr_where_str='')
	{
		// settings
		$giftshop_folder = GetSiteSettings('giftshop_folder');
		
		// paging
		if (!$numpage) {
			$numpage = $this->category_numpage;
		}
		
		$lim_min = ($page-1) * $numpage;
		$lim_max = $numpage;
		$limit_str = ' LIMIT '.$lim_min.', '.$lim_max;
		
		// where
		$where_str = '';
		
		if ($only_active == 1) {
			$where_str .= 'status="1"';
		}
		
		if (strlen($usr_where_str)) {
			if ($where_str) {
				$where_str .= ' AND ';
			}
			$where_str .= $usr_where_str;
		}
		
		if ($where_str) {
			$where_str = ' WHERE '.$where_str;
		}
		
		// select
		$strSQL = 'SELECT DISTINCT id, name, comment, status, sorter, icon_path FROM '.$this->CATEGORY_TABLE.' '.$where_str.' ORDER BY sorter '.$limit_str;
		$rs = $this->dbconn->Execute($strSQL);
		
		// build result array
		$i = 0;
		$data = array();
		
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			
			$data[$i]['number'] = ($page-1) * $numpage + ($i+1);
			$data[$i]['id'] = $row['id'];
			$data[$i]['name'] = stripslashes($row['name']);
			$data[$i]['comment'] = (strlen(utf8_decode($row['comment'])) > 300) ? utf8_substr(stripslashes($row['comment']), 0, 300).'...' : stripslashes($row['comment']);
			$data[$i]['comment'] = nl2br($data[$i]['comment']);
			$data[$i]['comment_all'] = nl2br(stripslashes($row['comment']));
			$data[$i]['status'] = $row['status'] ? '+' : '-';
			
			if ($row['icon_path'] && file_exists($this->config['site_path'].$giftshop_folder.'/'.$row['icon_path'])) {
				$data[$i]['icon_path'] = $this->config['site_root'].$giftshop_folder.'/'.$row['icon_path'];
			}
			
			if ($row['icon_path'] && file_exists($this->config['site_path'].$giftshop_folder.'/thumb_'.$row['icon_path'])) {
				$data[$i]['thumb_icon_path'] = $this->config['site_root'].$giftshop_folder.'/thumb_'.$row['icon_path'];
			}
			
			$rs->MoveNext();
			$i++;
		}
		
		return $data;
	}

	function category_all_list($only_active = 0)
	{
		// where
		$where_str = '';
		
		if ($only_active == 1) {
			$where_str = ' WHERE status="1" ';
		}
		
		// select
		$strSQL = 'SELECT DISTINCT id, name, comment, status, sorter, icon_path FROM '.$this->CATEGORY_TABLE.' '.$where_str.' ORDER BY sorter';
		$rs = $this->dbconn->Execute($strSQL);
		
		// build result array
		$i = 0;
		$data = array();
		
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$data[$i]['number'] = $i+1;
			$data[$i]['id'] = $row['id'];
			$data[$i]['name'] = stripslashes($row['name']);
			$rs->MoveNext();
			$i++;
		}
		
		return $data;
	}

	function category_count()
	{
		$cnt = (int) $this->dbconn->getOne('SELECT COUNT(*) FROM '.$this->CATEGORY_TABLE);
		return $cnt;
	}

	function category_item($id)
	{
		// settings
		$giftshop_folder = GetSiteSettings('giftshop_folder');

		$strSQL = 'SELECT id, name, comment, status, sorter, icon_path FROM '.$this->CATEGORY_TABLE.' WHERE id = ?';
		$rs = $this->dbconn->Execute($strSQL, array($id));
		$row = $rs->GetRowAssoc(false);
		
		$item = array();
		$item['id'] = $row['id'];
		$item['name'] = stripslashes($row['name']);
		$item['comment'] = stripslashes($row['comment']);
		$item['sorter'] = $row['sorter'];
		$item['status'] = $row['status'];
		
		if ($row['icon_path'] && file_exists($this->config['site_path'].$giftshop_folder.'/'.$row['icon_path'])) {
			$item['icon_path'] = $this->config['site_root'].$giftshop_folder.'/'.$row['icon_path'];
		} else {
			$item['icon_path'] = '';
		}
		
		return $item;
	}

	function category_insert($data)
	{
		// icon
		$icon_path = '';
		
		if (is_uploaded_file($data['icon']['tmp_name'])) {
			$ret = $this->upload_picture($data['icon'], 'cat', true);
			if ($ret['err']) {
				return $ret['err'];
			}
			if ($ret['file_name']) {
				$icon_path = $ret['file_name'];
			}
		}
		
		// insert record
		$strSQL = 'INSERT INTO '.$this->CATEGORY_TABLE.' (name, comment, status, icon_path) VALUES (?, ?, ?, ?)';
		$this->dbconn->Execute($strSQL, array($data['name'], $data['comment'], $data['status'], $icon_path));
		
		// sorter
		$old_sorter = $this->dbconn->getOne('SELECT MAX(sorter)+1 FROM '.$this->CATEGORY_TABLE);
		$id = $this->dbconn->Insert_ID();
		$this->CategorySorter($data['sorter'], $old_sorter, $id);
		
		return;
	}

	function category_update($data)
	{
		// icon
		$icon_path = $this->dbconn->getOne('SELECT icon_path FROM '.$this->CATEGORY_TABLE.' WHERE id=?', array($data['id']));
		
		if (is_uploaded_file($data['icon']['tmp_name'])) {
			$ret = $this->upload_picture($data['icon'], 'cat', true);
			if ($ret['err']) {
				return $ret['err'];
			}
			if ($ret['file_name']) {
				$this->delete_picture($icon_path);
				$icon_path = $ret['file_name'];
			}
		}
		
		// sorter
		$old_sorter = $this->dbconn->getOne('SELECT sorter FROM '.$this->CATEGORY_TABLE.' WHERE id=?', array($data['id']));
		$this->CategorySorter($data['sorter'], $old_sorter, $data['id']);
		
		// update record
		$strSQL = 'UPDATE '.$this->CATEGORY_TABLE.' SET name=?, comment=?, status=?, icon_path=? WHERE id=?';
		$this->dbconn->Execute($strSQL, array($data['name'], $data['comment'], $data['status'], $icon_path, $data['id']));
		return;
	}

	function category_delete($id)
	{
		$this->category_picture_delete($id);
		$this->dbconn->Execute('DELETE FROM '.$this->CATEGORY_TABLE.' WHERE id=?', array($id));
		return;
	}
	
	function category_picture_delete($id)
	{
		// delete file
		$old_icon = $this->dbconn->getOne('SELECT icon_path FROM '.$this->CATEGORY_TABLE.' WHERE id=?', array($id));
		$this->delete_picture($old_icon);
		
		// update record
		$this->dbconn->Execute('UPDATE '.$this->CATEGORY_TABLE.' SET icon_path = "" WHERE id=?', array($id));
		return;
	}
	
	function category_items($id, $only_active=0)
	{
		$where_str = '';
		
		if ($only_active == 1) {
			$where_str .= ' AND status="1" ';
		}
		
		$strSQL = 'SELECT id, name, price, promote, status, sorter FROM '.$this->ITEM_TABLE.' WHERE id_category=? '.$where_str.' ORDER BY sorter';
		$rs = $this->dbconn->Execute($strSQL, array($id));
		
		$i = 0;
		$data = array();
		
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$data[$i]['number'] = $i+1;
			$data[$i]['id'] = $row['id'];
			$data[$i]['name'] = stripslashes($row['name']);
			$data[$i]['price'] = sprintf('%01.2f', $row['price']);
			$data[$i]['promote'] = $row['promote'] ? '+' : '-';
			$data[$i]['status'] = $row['status'] ? '+' : '-';
			$rs->MoveNext();
			$i++;
		}
		
		return $data;
	}

	function CategorySorter($sorter, $old_sorter, $id='')
	{
		// item deleted
		if (!$id) {
			$strSQL = 'SELECT id FROM '.$this->CATEGORY_TABLE.' WHERE sorter >= ? ORDER BY sorter';
			$rs = $this->dbconn->Execute($strSQL, array($old_sorter));
			while (!$rs->EOF) {
				$this->dbconn->Execute('UPDATE '.$this->CATEGORY_TABLE.' SET sorter = sorter - 1 WHERE id=?', array($rs->fields[0]));
				$rs->MoveNext();
			}
			return;
		}
		
		if ($old_sorter < $sorter)
		{
			// record moved up. move all records in between the old and new position one position down.
			$strSQL = 'SELECT id FROM '.$this->CATEGORY_TABLE.' WHERE sorter BETWEEN ? AND ? ORDER BY sorter';
			$rs = $this->dbconn->Execute($strSQL, array($old_sorter, $sorter));
			while (!$rs->EOF) {
				$this->dbconn->Execute('UPDATE '.$this->CATEGORY_TABLE.' SET sorter = sorter - 1 WHERE id=?', array($rs->fields[0]));
				$rs->MoveNext();
			}
		}
		elseif ($old_sorter > $sorter)
		{
			// record moved down. move all records in between the old and new position one position up.
			$strSQL = 'SELECT id FROM '.$this->CATEGORY_TABLE.' WHERE sorter BETWEEN ? AND ? ORDER BY sorter';
			$rs = $this->dbconn->Execute($strSQL, array($sorter, $old_sorter));
			while (!$rs->EOF) {
				$this->dbconn->Execute('UPDATE '.$this->CATEGORY_TABLE.' SET sorter = sorter + 1 WHERE id=?', array($rs->fields[0]));
				$rs->MoveNext();
			}
		}
		
		// now set the position of the moved record itself
		$this->dbconn->Execute('UPDATE '.$this->CATEGORY_TABLE.' SET sorter=? WHERE id=?', array($sorter, $id));
		return;
	}

	function GetCategoryName($id)
	{
		$name = $this->dbconn->getOne('SELECT name FROM '.$this->CATEGORY_TABLE.' WHERE id=?', array($id));
		return stripslashes($name);
	}
	
	//====================
	// ITEM FUNCTIONS
	//====================
	
	function items_list($id, $page=1, $numpage='', $only_active=0, $usr_where_str='')
	{
		// paging
		if (!$numpage) {
			$numpage = $this->category_numpage;
		}
		
		$lim_min = ($page-1) * $numpage;
		$lim_max = $numpage;
		$limit_str = ' LIMIT '.$lim_min.', '.$lim_max;
		
		// settings
		$giftshop_folder = GetSiteSettings('giftshop_folder');
		
		// where
		$where_str = '';
		
		if ($only_active == 1) {
			$where_str .= ' AND status="1"';
		}
		
		if (strlen($usr_where_str)) {
			$where_str .= ' AND '.$usr_where_str;
		}
		
		// select
		$strSQL = 'SELECT * FROM '.$this->ITEM_TABLE.' WHERE id_category=? '.$where_str.' ORDER BY sorter '.$limit_str;
		$rs = $this->dbconn->Execute($strSQL, array($id));
		
		// build result array
		$i = 0;
		$data = array();
		
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$data[$i]['number'] = ($page-1) * $numpage + ($i+1);
			$data[$i]['id'] = $row['id'];
			$data[$i]['name'] = stripslashes($row['name']);
			$data[$i]['price'] = sprintf('%01.2f', $row['price']);
			$data[$i]['comment'] = (strlen(utf8_decode($row['comment'])) > 300) ? utf8_substr(stripslashes($row['comment']), 0, 300).'&hellip;' : stripslashes($row['comment']);
			$data[$i]['comment'] = nl2br($data[$i]['comment']);
			$data[$i]['comment_all'] = nl2br(stripslashes($row['comment']));
			$data[$i]['promote'] = $row['promote'] ? '+' : '-';
			$data[$i]['status'] = $row['status'] ? '+' : '-';
			
			if ($row['icon_path'] && file_exists($this->config['site_path'].$giftshop_folder.'/thumb_'.$row['icon_path'])) {
				$data[$i]['thumb_icon_path'] = $this->config['site_root'].$giftshop_folder.'/thumb_'.$row['icon_path'];
			}
			
			if ($row['icon_path'] && file_exists($this->config['site_path'].$giftshop_folder.'/'.$row['icon_path'])) {
				$data[$i]['icon_path'] = $this->config['site_root'].$giftshop_folder.'/'.$row['icon_path'];
			}
			
			$rs->MoveNext();
			$i++;
		}
		
		return $data;
	}

	function items_count($id, $only_active=0)
	{
		if ($only_active == 1) {
			$where_str = ' AND status="1" ';
		} else {
			$where_str = '';
		}
		
		$cnt = (int) $this->dbconn->getOne('SELECT COUNT(*) FROM '.$this->ITEM_TABLE.' WHERE id_category=? '.$where_str, array($id));
		return $cnt;
	}

	function items_item($id)
	{
		// settings
		$giftshop_folder = GetSiteSettings('giftshop_folder');
		
		// select
		$strSQL = 'SELECT * FROM '.$this->ITEM_TABLE.' WHERE id=?';
		$rs = $this->dbconn->Execute($strSQL, array($id));
		$row = $rs->GetRowAssoc(false);
		
		// build item
		$item = array();
		$item['id'] = $row['id'];
		$item['name'] = stripslashes($row['name']);
		$item['comment'] = stripslashes($row['comment']);
		$item['sorter'] = $row['sorter'];
		$item['status'] = $row['status'];
		$item['price'] = sprintf('%01.2f', $row['price']);
		$item['id_category'] = $row['id_category'];
		$item['promote'] = $row['promote'];
		
		if ($row['icon_path'] && file_exists($this->config['site_path'].$giftshop_folder.'/'.$row['icon_path'])) {
			$item['icon_path'] = $this->config['site_root'].$giftshop_folder.'/'.$row['icon_path'];
		}
		
		if ($row['icon_path'] && file_exists($this->config['site_path'].$giftshop_folder.'/thumb_'.$row['icon_path'])) {
			$item['thumb_icon_path'] = $this->config['site_root'].$giftshop_folder.'/thumb_'.$row['icon_path'];
		}
		
		$item['gallery'] = $this->items_gallery_list($id);
		
		return $item;
	}
	
	function items_insert($data)
	{
		// icon
		$icon_path = '';
		
		if (is_uploaded_file($data['icon']['tmp_name'])) {
			$ret = $this->upload_picture($data['icon'], 'item', true);
			if ($ret['err']) {
				return $ret['err'];
			}
			if ($ret['file_name']) {
				$icon_path = $ret['file_name'];
			}
		}
		
		// insert
		$strSQL = 'INSERT INTO '.$this->ITEM_TABLE.' (name, comment, status, icon_path, price, promote, id_category) VALUES (?, ?, ?, ?, ?, ?, ?)';
		$this->dbconn->Execute($strSQL, array($data['name'], $data['comment'], $data['status'], $icon_path, $data['price'], $data['promote'], $data['id_category']));
		
		// sorter
		$id = $this->dbconn->Insert_ID();
		#$id = (int) $this->dbconn->getOne('SELECT MAX(id) FROM '.$this->ITEM_TABLE.' WHERE id_category=?', array($data['id_category']));
		$old_sorter = (int) $this->dbconn->getOne('SELECT MAX(sorter)+1 FROM '.$this->ITEM_TABLE.' WHERE id_category=?', array($data['id_category']));
		if (empty($old_sorter)) {
			$old_sorter = 1;
		}
		$this->ItemSorter($data['sorter'], $old_sorter, $id, $data['id_category']);
		
		return;
	}

	function items_update($data)
	{
		// icon
		$icon_path = $this->dbconn->getOne('SELECT icon_path FROM '.$this->ITEM_TABLE.' WHERE id=?', array($data['id']));
		
		if (is_uploaded_file($data['icon']['tmp_name'])) {
			$ret = $this->upload_picture($data['icon'], 'item', true);
			if ($ret['err']) {
				return $ret['err'];
			}
			if ($ret['file_name']) {
				if ($icon_path) {
					$this->delete_picture($icon_path);
				}
				$icon_path = $ret['file_name'];
			}
		}
		
		// sorter
		$old_category = $this->dbconn->getOne('SELECT id_category FROM '.$this->ITEM_TABLE.' WHERE id=?', array($data['id']));
		if ($old_category == $data['id_category']) {
			$old_sorter = $this->dbconn->getOne('SELECT sorter FROM '.$this->ITEM_TABLE.' WHERE id=?', array($data['id']));
			$this->ItemSorter($data['sorter'], $old_sorter, $data['id'], $data['id_category']);
		} else {
			// remove from old category
			$old_sorter = $this->dbconn->getOne('SELECT sorter FROM '.$this->ITEM_TABLE.' WHERE id=?', array($data['id']));
			$this->ItemSorter(null, $old_sorter, null, $old_category);
			// insert into new category. we move it to the end of the list, disregarding the user input
			$sorter = (int) $this->dbconn->getOne('SELECT MAX(sorter)+1 FROM '.$this->ITEM_TABLE.' WHERE id_category=?', array($data['id_category']));
			if (empty($sorter)) {
				$sorter = 1;
			}
			$this->ItemSorter($sorter, $sorter, $data['id'], $data['id_category']);
		}
		
		// update
		$strSQL = 'UPDATE '.$this->ITEM_TABLE.' SET name=?, comment=?, status=?, price=?, promote=?, id_category=?, icon_path=? WHERE id=?';
		$this->dbconn->Execute($strSQL, array($data['name'], $data['comment'], $data['status'], $data['price'], $data['promote'], $data['id_category'], $icon_path, $data['id']));
		return;
	}
	
	function items_delete($id)
	{
		$this->items_picture_delete($id);
		$this->dbconn->Execute('DELETE FROM '.$this->ITEM_TABLE.' WHERE id=?', array($id));
		return;
	}
	
	function items_picture_delete($id)
	{
		// delete file
		$old_icon = $this->dbconn->getOne('SELECT icon_path FROM '.$this->ITEM_TABLE.' WHERE id=?', array($id));
		$this->delete_picture($old_icon);
		
		// update record
		$this->dbconn->Execute('UPDATE '.$this->ITEM_TABLE.' SET icon_path = "" WHERE id=?', array($id));
		return;
	}
	
	function items_gallery_list($id)
	{
		// settings
		$giftshop_folder = GetSiteSettings('giftshop_folder');

		$rs = $this->dbconn->Execute('SELECT id, image_path FROM '.$this->ITEM_IMAGES_TABLE.' WHERE id_item=? ORDER BY id', array($id));
		
		$i = 0;
		$data = array();
		
		while (!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$data[$i]['number'] = $i+1;
			$data[$i]['id'] = (int) $row['id'];
			
			if ($row['image_path'] && file_exists($this->config['site_path'].$giftshop_folder.'/thumb_'.$row['image_path'])) {
				$data[$i]['thumb_image_path'] = $this->config['site_root'].$giftshop_folder.'/thumb_'.$row['image_path'];
			}
			
			if ($row['image_path'] && file_exists($this->config['site_path'].$giftshop_folder.'/'.$row['image_path'])) {
				$data[$i]['image_path'] = $this->config['site_root'].$giftshop_folder.'/'.$row['image_path'];
			}
			
			$rs->MoveNext();
			$i++;
		}
		
		return $data;
	}
	
	function items_gallery_count($id)
	{
		$cnt = (int) $this->dbconn->getOne('SELECT COUNT(*) FROM '.$this->ITEM_IMAGES_TABLE.' WHERE id_item=?', array($id));
		return $cnt;
	}
	
	function items_gallery_image($id_image)
	{
		$giftshop_folder = GetSiteSettings('giftshop_folder');
		
		$image_path = $this->dbconn->getOne('SELECT image_path FROM '.$this->ITEM_IMAGES_TABLE.' WHERE id=?', array($id_image));
		
		if ($image_path && file_exists($this->config['site_path'].$giftshop_folder.'/'.$image_path)) {
			$full_image_path = $this->config['site_root'].$giftshop_folder.'/'.$image_path;
		}
		
		return $full_image_path;
	}

	function items_gallery_insert($image_src, $id_item)
	{
		if (is_uploaded_file($image_src['tmp_name'])) {
			$ret = $this->upload_picture($image_src, 'img', true);
			if ($ret['err']) {
				return $ret['err'];
			}
			$this->dbconn->Execute('INSERT INTO '.$this->ITEM_IMAGES_TABLE.' (id_item , image_path) VALUES (?, ?)', array($id_item, $ret['file_name']));
			return $this->dbconn->ErrorMsg();
		}
		
		return '';
	}
	
	function items_gallery_delete($id_image)
	{
		$old_icon = $this->dbconn->getOne('SELECT image_path FROM '.$this->ITEM_IMAGES_TABLE.' WHERE id=?', array($id_image));
		$this->delete_picture($old_icon);
		$this->dbconn->Execute('DELETE FROM '.$this->ITEM_IMAGES_TABLE.' WHERE id=?', array($id_image));
		return;
	}
	
	function ItemSorter($sorter, $old_sorter, $id='', $id_category='')
	{
		if (!$id_category && $id) {
			$id_category = (int) $this->dbconn->getOne('SELECT id_category FROM '.$this->ITEM_TABLE.' WHERE id=?', array($id));
		}
		
		// item deleted
		if (!$id && $id_category) {
			$strSQL = 'SELECT id FROM '.$this->ITEM_TABLE.' WHERE sorter >= ? AND id_category=? ORDER BY sorter';
			$rs = $this->dbconn->Execute($strSQL, array($old_sorter, $id_category));
			while (!$rs->EOF) {
				$this->dbconn->Execute('UPDATE '.$this->ITEM_TABLE.' SET sorter = sorter - 1 WHERE id=?', array($rs->fields[0]));
				$rs->MoveNext();
			}
			return;
		}
		
		if ($old_sorter < $sorter)
		{
			// record moved up. move all records in between the old and new position one position down.
			$strSQL = 'SELECT id FROM '.$this->ITEM_TABLE.' WHERE id_category=? AND sorter BETWEEN ? AND ? ORDER BY sorter';
			$rs = $this->dbconn->Execute($strSQL, array($id_category, $old_sorter, $sorter));
			while (!$rs->EOF) {
				$this->dbconn->Execute('UPDATE '.$this->ITEM_TABLE.' SET sorter = sorter - 1 WHERE id=?', array($rs->fields[0]));
				$rs->MoveNext();
			}
		}
		elseif ($old_sorter > $sorter)
		{
			// record moved down. move all records in between the old and new position one position up.
			$strSQL = 'SELECT id FROM '.$this->ITEM_TABLE.' WHERE id_category=? AND sorter BETWEEN ? AND ? ORDER BY sorter';
			$rs = $this->dbconn->Execute($strSQL, array($id_category, $sorter, $old_sorter));
			while (!$rs->EOF) {
				$this->dbconn->Execute('UPDATE '.$this->ITEM_TABLE.' SET sorter = sorter + 1 WHERE id=?', array($rs->fields[0]));
				$rs->MoveNext();
			}
		}
		
		// now set the position of the moved record itself
		$this->dbconn->Execute('UPDATE '.$this->ITEM_TABLE.' SET sorter = ? WHERE id=?', array($sorter, $id));
		return;
	}
	
	//====================
	// PICTURE FUNCTIONS
	//====================
	
	function upload_picture($icon, $type='cat', $create_thumb=false)
	{
		// settings
		$settings = GetSiteSettings(array('giftshop_folder', 'giftshop_max_size', 'giftshop_max_height', 'giftshop_max_width', 'thumb_max_width', 'thumb_max_height'));
		
		// icon
		$path_to_image = $this->GetTempUploadFile($icon['name']);
		
		$err = '';
		
		if (is_uploaded_file($icon['tmp_name']) && move_uploaded_file($icon['tmp_name'], $path_to_image))
		{
			$icon['tmp_name'] = $path_to_image;
			
			$this->ReSizeImage($icon['tmp_name'], $settings['giftshop_max_width'], $settings['giftshop_max_height'], 0);
			
			$icon['size'] = filesize($icon['tmp_name']);
			
			$icon_info = GetImageSize($icon['tmp_name']);
			
			$filename_arr = explode('.', $icon['name']);
			$ext = strtolower($filename_arr[count($filename_arr) - 1]);
			
			if (!in_array($icon['type'], $this->IMG_TYPE_ARRAY) || !in_array($ext, $this->IMG_EXT_ARRAY))
			{
				$err = $this->lang['err']['invalid_photo_type'] . implode(', ', $this->IMG_TYPE_ARRAY);
			}
			elseif ($icon['size'] > getFileSizeFromString($settings['giftshop_max_size']))
			{
				$err = str_replace('#SIZE#', $settings['giftshop_max_size'], $this->lang['err']['invalid_photo_size']);
			}
			elseif ($icon_info[0] > $settings['giftshop_max_width'])
			{
				$err = str_replace('#WIDTH#', $settings['giftshop_max_width'], $this->lang['err']['invalid_photo_width']);
			}
			elseif ($icon_info[1] > $settings['giftshop_max_height'])
			{
				$err = str_replace('#HEIGHT#', $settings['giftshop_max_height'], $this->lang['err']['invalid_photo_height']);
			}
			else
			{
				$new_file_name = $type.'_'.date('ymdhis').'.'.$ext;
				$icon_file = $this->config['site_path'].$settings['giftshop_folder'].'/'.$new_file_name;
				if (is_dir($this->config['site_path'].$settings['giftshop_folder']) && is_writeable($this->config['site_path'].$settings['giftshop_folder'])) {
					if (copy($icon['tmp_name'], $icon_file)) {
						if ($create_thumb) {
							// create thumb
							$this->ReSizeImage($icon['tmp_name'], $settings['thumb_max_width'], $settings['thumb_max_height'], 1);
							$new_thumb_name = 'thumb_'.$new_file_name;
							copy($icon['tmp_name'], $this->config['site_path'].$settings['giftshop_folder'].'/'.$new_thumb_name);
						}
						unlink($icon['tmp_name']);
					}
				} else {
					unlink($icon['tmp_name']);
					$err = $this->lang['err']['not_writeable_file'];
				}
			}
		}
		
		$arr['file_name'] = isset($new_file_name) ? $new_file_name : '';
		$arr['err'] = $err;
		return $arr;
	}
	
	function delete_picture($file_name)
	{
		if (!$file_name) {
			return;
		}
		
		$giftshop_folder = GetSiteSettings('giftshop_folder');
		
		$icon_file = $this->config['site_path'].$giftshop_folder.'/'.$file_name;
		$thumb_icon_file = $this->config['site_path'].$giftshop_folder.'/thumb_'.$file_name;
		
		if (file_exists($icon_file)) {
			unlink($icon_file);
		}
		
		if (file_exists($thumb_icon_file)) {
			unlink($thumb_icon_file);
		}
		
		return;
	}

	function ReSizeImage($path, $width_to, $height_to, $thumb = 0)
	{
		$use_image_resize = GetSiteSettings('use_image_resize');
		
		if ($use_image_resize && file_exists($path) && extension_loaded('gd')) {
			$image_info = GetImageSize($path);
			$image_width = $image_info[0];
			$image_height = $image_info[1];
			$image_type = $image_info[2];
			if ($image_width > $width_to || $image_height > $height_to) {
				$st = $this->ReSizeAction($path, $image_type, $image_width, $image_height, $width_to, $height_to, $thumb);
				if ($st) {
					return true;
				} else {
					return false;
				}
			}
		}
		
		return true;
	}

	function ReSizeAction($path, $type, $image_width, $image_height, $width_to, $height_to, $thumb=0)
	{
		switch ($type) {
			case '1': $srcImage = @ImageCreateFromGif($path); break;
			case '2': $srcImage = @imagecreatefromjpeg($path); break;
			case '3': $srcImage = @imagecreatefrompng($path); break;
			case '6': $srcImage = @imagecreatefromwbmp($path); break;
		}
				
		if ($srcImage) {
			if ($thumb == 1) {
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
				$destImage = @imagecreatetruecolor($image_width, $image_height);
				imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $image_width, $image_height, $srcWidth, $srcHeight);
			} else {
				$srcWidth = ImageSX($srcImage);
				$srcHeight = ImageSY($srcImage);
				$k_1 = $srcWidth / $width_to;
				$k_2 = $srcHeight / $height_to;
				
				if ($k_1 < $k_2){
					$resized_image_width = $width_to;
					$resized_image_height = round($srcHeight / $k_1);
					$src_x = 0;
					$src_y = round($k_1 * abs($resized_image_height - $height_to) / 2);
					$sample_image_width = round($k_1 * $width_to);
					$sample_image_height = round($k_1 * $height_to);
				} else {
					$resized_image_height = $height_to;
					$resized_image_width = round($srcWidth / $k_2);
					$src_x = round($k_2 * abs($resized_image_width - $width_to) / 2);
					$src_y = 0;
					$sample_image_width = round($k_2 * $width_to);
					$sample_image_height = round($k_2 * $height_to);
				}
				
				$destImage = @imagecreatetruecolor($width_to, $height_to);
				$bg_color = imagecolorallocate($destImage, 255, 255, 255);
				imagefilledrectangle($destImage, 0, 0, $width_to, $height_to, $bg_color);
				imagecopyresampled( $destImage, $srcImage, 0, 0, $src_x, $src_y, $width_to, $height_to, $sample_image_width, $sample_image_height );
			}

			switch ($type) {
				case '1': ImageGif($destImage, $path); break;
				case '2': ImageJpeg($destImage, $path); break;
				case '3': ImagePng($destImage, $path); break;
				case '6': ImageWbmp($destImage, $path); break;
			}

			ImageDestroy($srcImage);
			ImageDestroy($destImage);
			return true;
		} else {
			return false;
		}
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
			$path_to_image = $this->config['file_temp_path'].'/'. $prefix . $seed . '.' . $suffix;
		} while (file_exists($path_to_image));

		return $path_to_image;
	}
}

?>