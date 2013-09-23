<?php

class Catalog {
	
	var $dbconn;
	var $config;
	var $lang;
	var $error_arr = array(1=>'addCategory_empty_name',
							2=>'updateCategory_empty_id',
							3=>'updateCategory_empty_name',
							4=>'deleteCategory_empty_id',
							5=>'addCategory_unknown_id_parent',
							6=>'addCategory_name_on_level_already_exist',
							7=>'updateCategory_unknown_id',
							8=>'addItem_unknown_id_parent',
							9=>'addItem_request_error',
							10=>'updateCategory_name_on_level_already_exist',
							11=>'addItem_empty_question_field',
							12=>'updateItem_unknown_id',
							13=>'updateItem_empty_text',
							14=>'updateItem_request_error',
							15=>'deleteItem_empty_id',
							16=>'deleteItem_request_error',
							17=>'addComment_empty_id_parent',
							18=>'addComment_empty_text',
							19=>'addComment_unknown_id_parent',
							20=>'addComment_empty_id_owner',
							21=>'addComment_unknown_id_owner',
							22=>'addComment_request_error');
	var $error_no = 0;
	
	function Catalog($dbconn,$config,$lang){
		$this->config = $config;
		$this->dbconn = $dbconn;
		$this->lang = $lang;
		list($this->config['icons_folder'],$this->config['icons_default']) = array_values(GetSiteSettings(array('icons_folder','icons_default')));
		
		define('CATEGORIES_TABLE', ANSW_CATEGORIES_TABLE);
		define('ITEMS_TABLE', ANSW_QUESTIONS_TABLE);
		define('COMMENTS_TABLE', ANSW_ANSWERS_TABLE);
	}
	
	function adodb_error_check($line){
		if($this->dbconn->ErrorNo()) return trigger_error("<br>Error on line: ".$line." ",E_USER_ERROR);	
	}
	
	function getCategories($id_parent, $from=0, $count=0, $with_subcats_only=0){
		$id_parent = intval($id_parent);
		$from = intval($from);
		$count = intval($count);
		$limit='';
		
		if ($count != 0)
			$limit = " LIMIT ".$from.",".$count;
		
		$strSQL = "SELECT id, id_parent, active, name, date_add FROM ".CATEGORIES_TABLE." WHERE id_parent='".$id_parent."' ORDER BY name ".$limit;
		$rs = $this->dbconn->Execute($strSQL);

		$this->adodb_error_check(__LINE__);
		unset($cats);
		while (!$rs->EOF){
			//id, id_parent, active, name, date_add
			$row = $rs->GetRowAssoc(false);
			if($with_subcats_only){
				$strSQL = "SELECT id FROM ".CATEGORIES_TABLE." WHERE id_parent='".$row['id']."'";
				if (!$this->dbconn->GetOne($strSQL)){
					$rs->MoveNext();
					continue;
				}
			}	
			$row['name'] = stripslashes($row['name']);
			$cats[] = $row;
			$rs->MoveNext();
		}
		return $cats;
	}
	
	function getAllCats(){
		$strSQL = "SELECT id, name, id_parent, active FROM ".CATEGORIES_TABLE;
		$rs = $this->dbconn->Execute($strSQL);
		$this->adodb_error_check(__LINE__);

		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$row['name'] = stripslashes($row['name']);
			$cats[] = $row;
			$rs->MoveNext();
		}
		return $cats;
	}
	
	function addCategory($id_parent, $name, $is_active=1){	

		$id_parent = intval($id_parent);
		$name = addslashes($name);
		$is_active = $is_active?1:0;
		
		if ($name == ''){
			$this->error_no = 1;
			return false;
		}
		
		if ($id_parent > 0){
			$strSQL = "SELECT id FROM ".CATEGORIES_TABLE." WHERE id='".$id_parent."' ";
			if (!$this->dbconn->GetOne($strSQL)){
				$this->error_no = 5;
				return false;
			}
		}
		
		$strSQL = "SELECT name FROM ".CATEGORIES_TABLE." WHERE name='".$name."' AND id_parent='".$id_parent."'";
		if ($this->dbconn->GetOne($strSQL)){
			$this->error_no = 6;
			return false;
		}
		
		$strSQL = "INSERT INTO ".CATEGORIES_TABLE." SET id_parent='".$id_parent."', active='".$is_active."', name='".$name."', date_add=NOW()";
		$this->dbconn->Execute($strSQL);
		$this->adodb_error_check(__LINE__);
		
		return $this->dbconn->Insert_ID();
	}
	
	function updateCategory($id, $name, $is_active=1){
		$id = intval($id);
		$name = addslashes($name);
		$is_active = $is_active?1:0;
		
		if ($id < 1){
			$this->error_no = 2;
			return false;
		}

		if ($name == ''){
			$this->error_no = 3;
			return false;
		}

		$strSQL = "SELECT id, id_parent FROM ".CATEGORIES_TABLE." WHERE id='".$id."'";
		$rs = $this->dbconn->Execute($strSQL);
		$id = $rs->fields[0];
		$id_parent = $rs->fields[1];
		if(!$id){
			$this->error_no = 7;
			return false;
		}
		
		$strSQL = "SELECT id FROM ".CATEGORIES_TABLE." WHERE name='".$name."' AND id_parent='".$id_parent."' AND id!='".$id."'";
		if ($this->dbconn->GetOne($strSQL)){
			$this->error_no = 10;
			return false;
		}
		
		
		$strSQL = "UPDATE ".CATEGORIES_TABLE." set active='".$is_active."', name='".$name."' where id='".$id."'";
		$this->dbconn->Execute($strSQL);
		$this->adodb_error_check(__LINE__);
		
		return true;
	}
	
	function deleteCategory($id){
		$id = intval($id);
		if (!$id){
			$this->error_no = 4;
			return false;
		}

		$strSQL = "DELETE FROM ".CATEGORIES_TABLE." WHERE id='".$id."'";
		$this->dbconn->Execute($strSQL);
		$this->adodb_error_check(__LINE__);
		
		//get all child ids array
		$id_arr[] = $id;
		while(true){
			$strSQL = "SELECT id FROM ".CATEGORIES_TABLE." WHERE id_parent IN (".implode(',',$id_arr).")";
			$rs = $this->dbconn->Execute($strSQL);
			$this->adodb_error_check(__LINE__);
			unset($id_arr);
			while (!$rs->EOF){
				$id_arr_all[] = $id_arr[] = $rs->fields[0];
				$rs->MoveNext();
			}
			if (!$id_arr){
				break;
			}
		}
		//delete all childs
		if (count($id_arr_all)){
			$strSQL = "DELETE FROM ".CATEGORIES_TABLE." WHERE id IN(".implode(',',$id_arr_all).")";
			$this->dbconn->Execute($strSQL);
			$this->adodb_error_check(__LINE__);
		
			$strSQL = "SELECT id FROM ".ITEMS_TABLE." WHERE id_parent in (".implode(',',$id_arr_all).")";
			$rs = $this->dbconn->Execute($strSQL);
			while (!$rs->EOF){
				$id_items_arr[] = $rs->fields[0];
				$rs->MoveNext();
			}
		
			$id_items_str = implode(',',$id_items_arr);
			
			//delete all childs items
			//дальше что то вроде.....
			$strSQL = "DELETE FROM ".ITEMS_TABLE." WHERE id in (".$id_items_str.")";
			$this->dbconn->Execute($strSQL);
			
			$strSQL = "DELETE FROM ".COMMENTS_TABLE." WHERE id_parent in (".$id_items_str.")";
			$this->dbconn->Execute($strSQL);
		}
		return true;
	}
	
	function getAllPath($id_parent){
		$id = intval($id_parent);
		if ($id == 0) return;
		while($id != 0){
			$strSQL = "select id_parent from ".CATEGORIES_TABLE." where id='".$id."'";
			$id_arr[]["id"] = $id = $this->dbconn->GetOne($strSQL);
		}
		$path_arr = array_reverse($id_arr);
		$i=0;
		foreach ($path_arr as $item){
			if ($item != "0"){
				$strSQL = "select name from ".CATEGORIES_TABLE." where id='".$item["id"]."'";
				$path_arr[$i]["name"] = stripslashes($this->dbconn->GetOne($strSQL));
			}
			$i++;
		}
		$strSQL = "select id, name from ".CATEGORIES_TABLE." where id='".$id_parent."'";
		$rs = $this->dbconn->Execute($strSQL);
		$this->adodb_error_check(__LINE__);
		$path_arr[$i]["id"] = $rs->fields[0];
		$path_arr[$i]["name"] = stripslashes($rs->fields[1]);
		return $path_arr;
	}
	
	function getAllParents($id){
		$id = intval($id);
		if ($id == 0) return;
		while($id != 0){
			$strSQL = "select id_parent from ".CATEGORIES_TABLE." where id='".$id."'";
			$parent_arr[]["id"] = $id = $this->dbconn->GetOne($strSQL);
		}
		$i=0;
		foreach ($parent_arr as $value){
			if ($value["id"] == '0') $parent_arr[$i]["name"] = "Root";
			else {
				$strSQL = "select name from ".CATEGORIES_TABLE." where id='".$value["id"]."'";
				$parent_arr[$i]["name"] = stripslashes($this->dbconn->GetOne($strSQL));
			}
			$i++;
		}
		return $id_arr; 
	}
	
	function getCatParams($id){
		$strSQL= "select id, name, id_parent, active from ".CATEGORIES_TABLE." where id='".$id."'";
		$rs = $this->dbconn->Execute($strSQL);
		$this->adodb_error_check(__LINE__);
		$row = $rs->GetRowAssoc(false);	
		$arr["id"] = $row["id"];
		$arr["name"] = stripslashes($row["name"]);
		$arr["id_parent"] = $row["id_parent"];
		$arr["active"] = $row["active"];
		return $arr;
	}
	
	function addItem($text, $details, $id_parent, $id_owner){
		$id_parent = intval($id_parent);
		$text = strip_tags(addslashes($text));
		$details = strip_tags(addslashes($details));
		
		if ($id_parent > 0){
			$strSQL = "SELECT id_parent FROM ".CATEGORIES_TABLE." WHERE id='".$id_parent."' ";
			$id_p = $this->dbconn->GetOne($strSQL);
			if (!$id_parent || $id_p == 0){
				$this->error_no = 8;
				return false;
			}
		}
		
		if ($text == ''){
			$this->error_no = 11;
			return false;
		}
		
		$strSQL = "INSERT INTO ".ITEMS_TABLE." SET id_parent='".$id_parent."', id_owner='".$id_owner."', text='".$text."', details='".$details."', date_open=NOW()";
		$this->dbconn->Execute($strSQL);
		if ($this->dbconn->ErrorNo()){
			$this->error_no = 9;
			return false;
		}else 
			return true;
	}
	
	function updateItem($id, $text, $details){
		$id = intval($id);
		$text = strip_tags(addslashes($text));
		$details = strip_tags(addslashes($details));
		
		if ($id < 1){
			$this->error_no = 12;
			return false;
		}
		
		if ($text == ''){
			$this->error_no = 13;
			return false;
		}
		
		$strSQL = "UPDATE ".ITEMS_TABLE." SET text='".$text."', details='".$details."' WHERE id='".$id."'";
		$this->dbconn->Execute($strSQL);
		if ($this->dbconn->ErrorNo()){
			$this->error_no = 14;
			return false;
		}else 
			return true;
	}
	
	function deleteItem($id){
		$id = intval($id);
		
		if ($id < 1){
			$this->error_no = 15;
			return false;
		}
		
		$strSQL = "DELETE FROM ".ITEMS_TABLE." WHERE id='".$id."'";
		$this->dbconn->Execute($strSQL);
		if ($this->dbconn->ErrorNo()){
			$this->error_no = 16;
			return false;
		}else 
			return true;
	}
	
	function getItems($from=0, $count=0, $id_parent=-1, $id_owner=0, $open_sort=0, $closed_sort=0, $not_id_owner=0){
		
		if ($id_parent != -1 || $id_owner || $open_sort || $closed_sort || $not_id_owner){
			$where = ' WHERE ';
			$where_arr_or = array();
			if ($id_parent != -1) $where_arr_and[] =" it.id_parent='".$id_parent."' ";
			if ($id_owner) $where_arr_and[] =" it.id_owner='".$id_owner."' ";
			if ($not_id_owner) $where_arr_and[] =" it.id_owner!='".$not_id_owner."' ";
			$where_str_and = implode('AND',$where_arr_and);
			
			if ($open_sort) $where_arr_or[] =" it.is_open='1' ";
			if ($closed_sort) $where_arr_or[] =" it.is_open='0' ";

			$where_str_or = implode('OR', $where_arr_or);
			
			if ($where_str_and != '' && $where_str_or != ''){
				$where .= $where_str_and." AND (".$where_str_or.") ";
			}elseif ($where_str_and != '' && $where_str_or == ''){
				$where .= $where_str_and;
			}elseif ($where_str_and == '' && $where_str_or != ''){
				$where .= $where_str_or;
			}
		}else 
			$where = '';
		
		if ($from || $count){
			$limit = ' LIMIT '.$from.','.$count;
		}else
			$limit = '';
			
		$strSQL = "SELECT it.id, it.id_parent, it.id_owner, it.text, it.details, it.is_open, it.date_open, (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(it.date_open)) AS life_time , (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(it.date_closed)) as closed_time, ut.login, DATE_FORMAT(ut.date_birthday,'%Y-%m-%d %h:%i:%s') AS date_birthday, ut.icon_path, ut.gender, cat.id_parent as cat_id_parent, cat.name as cat_name, COUNT(ct.id) as answers_count
					FROM ".ITEMS_TABLE." it
					LEFT JOIN ".USERS_TABLE." ut ON it.id_owner=ut.id
					LEFT JOIN ".COMMENTS_TABLE." ct ON it.id=ct.id_parent
					LEFT JOIN ".CATEGORIES_TABLE." cat ON it.id_parent = cat.id
					".$where." GROUP by it.id ORDER BY it.is_open ASC, it.date_open DESC, it.date_closed ASC  ".$limit;
		$rs = $this->dbconn->Execute($strSQL);
		
		$langdate = $this->lang["answers"]["date"];
		$date_search = array('years','months','days','hours','min','sec');
		$date_repl = array($langdate["years"],$langdate["months"],$langdate["days"],$langdate["hours"],$langdate["min"],$langdate["sec"]);
		while (!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			
			$row['life_time'] = str_replace($date_search,$date_repl,convertSecsToDate($row['life_time']));
			$row['age'] = AgeFromBDate($row['date_birthday']);
			$icon_path = $this->config['site_path'].$this->config['icons_folder'].'/'.$row['icon_path'];
			if($row['icon_path'] !='' && file_exists($icon_path)){
				$row['icon_url'] = $this->config['server'].$this->config['site_root'].$this->config['icons_folder'].'/'.$row['icon_path'];
			}else{
				$row['icon_url'] = $this->config['server'].$this->config['site_root'].$this->config['icons_folder'].'/'.'default_'.$row['gender'].'.gif';
			}
			
			if ($row['is_open'] == '0'){
				$row['closed_time'] = str_replace($date_search,$date_repl,convertSecsToDate($row['closed_time']));
				
				$strSQL = "SELECT text FROM ".COMMENTS_TABLE." WHERE id_parent='".$row['id']."' AND is_best='1'";
				$row["best_answer_text"] = stripslashes($this->dbconn->GetOne($strSQL));
			}
			$row['text'] = stripslashes($row['text']);
			$row['details'] = stripslashes($row['details']);
			
			$strSQL = "SELECT name FROM ".CATEGORIES_TABLE." WHERE id='".$row['cat_id_parent']."'";
			$main_cat_name = $this->dbconn->GetOne($strSQL);
			$row['path'] = $main_cat_name.'/'.$row['cat_name'];
			
			$items[] = $row;
			$rs->MoveNext();
		}
		return $items;
	}
	
	function getCountItems($id_parent=-1, $id_owner=0, $open_sort=0, $closed_sort=0, $not_id_owner=0){
		
		if ($id_parent != -1 || $id_owner != -1 || $open_sort || $closed_sort || $not_id_owner){
			$where = ' WHERE ';
			if ($id_parent != -1) $where_arr_and[] =" it.id_parent='".$id_parent."' ";
			if ($id_owner) $where_arr_and[] =" it.id_owner='".$id_owner."' ";
			if ($not_id_owner) $where_arr_and[] =" it.id_owner!='".$not_id_owner."' ";
			$where_str_and = implode('AND',$where_arr_and);
			
			if ($open_sort) $where_arr_or[] =" it.is_open='1' ";
			if ($closed_sort) $where_arr_or[] =" it.is_open='0' ";
			$where_str_or = implode('OR', $where_arr_or);
			
			if ($where_str_and != '' && $where_str_or != ''){
				$where .= $where_str_and." AND (".$where_str_or.") ";
			}elseif ($where_str_and != '' && $where_str_or == ''){
				$where .= $where_str_and;
			}elseif ($where_str_and == '' && $where_str_or != ''){
				$where .= $where_str_or;
			}
		}else
			$where = '';
			
		$strSQL = "SELECT COUNT(it.id) as count
					FROM ".ITEMS_TABLE." it
					".$where;
		return $this->dbconn->GetOne($strSQL);
	}
	
	function addComment($id_parent, $id_owner, $text){
		
		$id_parent = intval($id_parent);
		$id_owner = intval($id_owner);
		$text = strip_tags(addslashes($text));
		
		if($id_parent < 1){
			$this->error_no = 17;
			return false;
		}		
		if($id_owner < 1){
			$this->error_no = 20;
			return false;
		}
		if($text == ''){
			$this->error_no = 18;
			return false;
		}
		
		$strSQL = "SELECT id FROM ".ITEMS_TABLE." WHERE id='".$id_parent."'";
		if (!$this->dbconn->GetOne($strSQL)){
			$this->error_no = 19;
			return false;
		}
		$strSQL = "SELECT id FROM ".USERS_TABLE." WHERE id='".$id_owner."'";
		if (!$this->dbconn->GetOne($strSQL)){
			$this->error_no = 21;
			return false;
		}
		$strSQL = "INSERT INTO ".COMMENTS_TABLE." SET id_parent='".$id_parent."', id_owner='".$id_owner."', text='".$text."', date_add=NOW()";
		$this->dbconn->Execute($strSQL);
		if($this->dbconn->ErrorNo()){
			$this->error_no = 22;
			return false;
		}else 
			return true;
		
	}
	
	function getComments($id_parent, $from=0, $count=0, $alloc_keword=''){
		
		$id_parent = intval($id_parent);
		
		if ($from || $count){
			$limit = ' LIMIT '.$from.','.$count;
		}else
			$limit = '';
		
		$alloc_keword = strip_tags(addslashes($alloc_keword));
		
		if ($alloc_keword){
			$adds_select = " (CASE WHEN ct.text LIKE '%".$alloc_keword."%' THEN 1 END) as relevance, ";
			$adds_order_by = " relevance DESC, ";	
		}
		
		$strSQL = "SELECT ".$adds_select." ct.id, ct.id_parent, ct.id_owner, ct.text, ct.is_best, ct.date_add, (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(ct.date_add)) AS life_time, ut.login, DATE_FORMAT(ut.date_birthday,'%Y-%m-%d %h:%i:%s') AS date_birthday, ut.icon_path, ut.gender, ut.root_user
					FROM ".COMMENTS_TABLE." ct
					LEFT JOIN ".USERS_TABLE." ut ON ct.id_owner=ut.id
					WHERE ct.id_parent='".$id_parent."' ORDER BY ".$adds_order_by." ct.is_best DESC, ct.date_add DESC ".$limit;
		$rs = $this->dbconn->Execute($strSQL);
		
		$langdate = $this->lang["answers"]["date"];
		$date_search = array('years','months','days','hours','min','sec');
		$date_repl = array($langdate["years"],$langdate["months"],$langdate["days"],$langdate["hours"],$langdate["min"],$langdate["sec"]);
		while (!$rs->EOF){
			$row = $rs->GetRowAssoc(false);	
			$row['life_time'] = str_replace($date_search,$date_repl,convertSecsToDate($row['life_time']));
			$row['age'] = AgeFromBDate($row['date_birthday']);
			$icon_path = $this->config['site_path'].$this->config['icons_folder'].'/'.$row['icon_path'];
			if($row['icon_path'] !='' && file_exists($icon_path)){
				$row['icon_url'] = $this->config['server'].$this->config['site_root'].$this->config['icons_folder'].'/'.$row['icon_path'];
			}else{
				$row['icon_url'] = $this->config['server'].$this->config['site_root'].$this->config['icons_folder'].'/'.'default_'.$row['gender'].'.gif';
			}
			$row['text'] = stripslashes($row['text']);
			if ($alloc_keword != ''){
				$row['text'] = str_replace($alloc_keword, "<font class='alloc_answ_searched'>".$alloc_keword."</font>",$row['text']);
			}
			$items[] = $row;
			$rs->MoveNext();
		}
		return $items;
	}
	
	function getCountComments($id_parent){
			
		$strSQL = "SELECT COUNT(id)
					FROM ".COMMENTS_TABLE."
					WHERE id_parent='".$id_parent."' ";
		return $this->dbconn->GetOne($strSQL);
	}
	
	function deleteComment($id, $id_parent){
		$id = intval($id);
		$id_parent = intval($id_parent);
		
		$strSQL = "DELETE FROM ".COMMENTS_TABLE." WHERE id='".$id."' AND id_parent='".$id_parent."'";
		$this->dbconn->Execute($strSQL);
		return ;
	}
	
	function makeBestComment($id, $id_parent){
		
		$id = intval($id);
		$id_parent = intval($id_parent);
		
		$strSQL = "UPDATE ".COMMENTS_TABLE." SET is_best='1' WHERE id='".$id."' AND id_parent='".$id_parent."'";
		$this->dbconn->Execute($strSQL);
		
		$strSQL = "UPDATE ".ITEMS_TABLE." SET is_open='0', date_closed=NOW() WHERE id='".$id_parent."'";
		$this->dbconn->Execute($strSQL);
		return ;
	}
	
	function getCommentsByOwner($id_owner, $from=0, $count=0, $best_filter=0){
		$id_owner = intval($id_owner);
		
		if ($from || $count){
			$limit = ' LIMIT '.$from.','.$count;
		}else
			$limit = '';
	
		if($best_filter){
			$where_adds = " AND ct.is_best='1' ";
		}
	
		
		$strSQL = "SELECT ut.id as id_user, ut.login, ut.icon_path, DATE_FORMAT(ut.date_birthday,'%Y-%m-%d %h:%i:%s') AS date_birthday, ut.root_user, it.text as item_text, it.details as item_details, (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(it.date_open)) AS item_life_time, (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(it.date_closed)) AS item_closed_time, cat.name as cat_name, cat.id_parent as cat_id_parent, ct.text as comment_text, (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(ct.date_add)) AS comment_life_time, ct.is_best
					FROM ".COMMENTS_TABLE." ct
					LEFT JOIN ".ITEMS_TABLE." it ON ct.id_parent=it.id
					LEFT JOIN ".CATEGORIES_TABLE." cat ON it.id_parent=cat.id
					LEFT JOIN ".USERS_TABLE." ut ON it.id_owner=ut.id
					WHERE ct.id_owner='".$id_owner."' ".$where_adds."
					ORDER BY ct.is_best ASC, ct.date_add DESC
					".$limit."
					";
		
		$langdate = $this->lang["answers"]["date"];
		$date_search = array('years','months','days','hours','min','sec');
		$date_repl = array($langdate["years"],$langdate["months"],$langdate["days"],$langdate["hours"],$langdate["min"],$langdate["sec"]);
		$rs = $this->dbconn->Execute($strSQL);
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);

			$row['item_life_time'] = str_replace($date_search,$date_repl,convertSecsToDate($row['item_life_time']));
			$row['age'] = AgeFromBDate($row['date_birthday']);
			$icon_path = $this->config['site_path'].$this->config['icons_folder'].'/'.$row['icon_path'];
			if($row['icon_path'] !='' && file_exists($icon_path)){
				$row['icon_url'] = $this->config['server'].$this->config['site_root'].$this->config['icons_folder'].'/'.$row['icon_path'];
			}else{
				$row['icon_url'] = $this->config['server'].$this->config['site_root'].$this->config['icons_folder'].'/'.'default_'.$row['gender'].'.gif';
			}
			
			if ($row['is_open'] == '0'){
				$row['item_closed_time'] = str_replace($date_search,$date_repl,convertSecsToDate($row['item_closed_time']));
			}
			$row['item_text'] = stripslashes($row['item_text']);
			$row['item_details'] = stripslashes($row['item_details']);
			$row['comment_text'] = stripslashes($row['comment_text']);
			$row['comment_life_time'] = str_replace($date_search,$date_repl,convertSecsToDate($row['comment_life_time']));
			
			$strSQL = "SELECT name FROM ".CATEGORIES_TABLE." WHERE id='".$row['cat_id_parent']."'";
			$main_cat_name = $this->dbconn->GetOne($strSQL);
			$row['path'] = $main_cat_name.'/'.$row['cat_name'];
			
			
			$comments[] = $row;
			$rs->MoveNext();		
		}
		return $comments;
	}
	
	function getCountCommentsByOwner($id_owner, $best_filter){
		if($best_filter){
			$where_adds = " AND ct.is_best='1' ";
		}
		$strSQL = "SELECT COUNT(ct.id) FROM ".COMMENTS_TABLE." ct
					WHERE ct.id_owner='".$id_owner."' ".$where_adds."
					";
		return $this->dbconn->GetOne($strSQL);
	}
	
	function getSearched($from=0, $count=0, $keyword='', $filter='', $id_parent=0){
		
		$keyword = str_replace(" ","",addslashes($keyword));
		$id_parent = intval($id_parent);
		
		$where = $where_and_b = $where_and_e = '';
		$where_or_arr = array();
		
		
		if ($keyword != ''){
			$where_or_arr[] = "it.text LIKE '%".$keyword."%' OR it.details LIKE '%".$keyword."%'";
		}
		
		if ($filter != 'q' && $keyword != ''){
			$where_or_arr[] = " ct.text LIKE '%".$keyword."%' ";
		}
	
		$where_or_str = implode(' OR ',$where_or_arr);
		
		if ($id_parent > 0){
			$where_and_str = " it.id_parent='".$id_parent."' ";
		}
		
		if ($where_and_str != '' && $where_or_str != '')
			$where = "WHERE ".$where_and_str." AND ( ".$where_or_str." ) ";
		elseif ($where_and_str != '' && $where_or_str == '')
			$where = "WHERE ".$where_and_str;
		elseif ($where_and_str == '' && $where_or_str != '')
			$where = "WHERE ".$where_or_str;
		
		if ($from || $count){
			$limit = ' LIMIT '.$from.','.$count;
		}else
			$limit = '';
		
		$strSQL = "SELECT it.id, it.id_parent, it.id_owner, it.text, it.details, it.is_open, it.date_open, (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(it.date_open)) AS life_time , (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(it.date_closed)) as closed_time, ut.login, DATE_FORMAT(ut.date_birthday,'%Y-%m-%d %h:%i:%s') AS date_birthday, ut.icon_path, ut.gender, cat.id_parent as cat_id_parent, cat.name as cat_name, COUNT(ct.id) as answers_count
					FROM ".ITEMS_TABLE." it
					LEFT JOIN ".COMMENTS_TABLE." ct ON it.id=ct.id_parent
					LEFT JOIN ".USERS_TABLE." ut ON it.id_owner=ut.id
					LEFT JOIN ".CATEGORIES_TABLE." cat ON it.id_parent=cat.id
					".$where."
					GROUP BY it.id
					ORDER BY date_open DESC
					".$limit."
					";
		$rs = $this->dbconn->Execute($strSQL);
		$langdate = $this->lang["answers"]["date"];
		$date_search = array('years','months','days','hours','min','sec');
		$date_repl = array($langdate["years"],$langdate["months"],$langdate["days"],$langdate["hours"],$langdate["min"],$langdate["sec"]);
		while (!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			
			$row['text'] = str_replace($keyword, "<font class='alloc_answ_searched'>".$keyword."</font>",$row['text']);
			$row['details'] = str_replace($keyword, "<font class='alloc_answ_searched'>".$keyword."</font>",$row['details']);
			
			$row['life_time'] = str_replace($date_search,$date_repl,convertSecsToDate($row['life_time']));
			$row['age'] = AgeFromBDate($row['date_birthday']);
			$icon_path = $this->config['site_path'].$this->config['icons_folder'].'/'.$row['icon_path'];
			if($row['icon_path'] !='' && file_exists($icon_path)){
				$row['icon_url'] = $this->config['server'].$this->config['site_root'].$this->config['icons_folder'].'/'.$row['icon_path'];
			}else{
				$row['icon_url'] = $this->config['server'].$this->config['site_root'].$this->config['icons_folder'].'/'.'default_'.$row['gender'].'.gif';
			}
			
			if ($row['is_open'] == '0'){
				$row['closed_time'] = str_replace($date_search,$date_repl,convertSecsToDate($row['closed_time']));
				
				$strSQL = "SELECT text FROM ".COMMENTS_TABLE." WHERE id_parent='".$row['id']."' AND is_best='1'";
				$row["best_answer_text"] = stripslashes($this->dbconn->GetOne($strSQL));
			}
			$row['text'] = stripslashes($row['text']);
			$row['details'] = stripslashes($row['details']);
			
			$strSQL = "SELECT name FROM ".CATEGORIES_TABLE." WHERE id='".$row['cat_id_parent']."'";
			$main_cat_name = $this->dbconn->GetOne($strSQL);
			$row['path'] = $main_cat_name.'/'.$row['cat_name'];
			
			$items[] = $row;
			$rs->MoveNext();
		}
		return $items;
	}

	function getSearchedCount($keyword='', $filter='', $id_parent=0){
		
		$keyword = str_replace(" ","",addslashes($keyword));
		$id_parent = intval($id_parent);
		
		$where = $where_and_b = $where_and_e = '';
		$where_or_arr = array();
		
		
		if ($keyword != ''){
			$where_or_arr[] = "it.text LIKE '%".$keyword."%' OR it.details LIKE '%".$keyword."%'";
		}
		
		if ($filter != 'q' && $keyword != ''){
			$where_or_arr[] = " ct.text LIKE '%".$keyword."%' ";
		}
	
		if ($id_parent > 0){
			$where_and_str = " it.id_parent='".$id_parent."' ";
		}
		
		$where_or_str = implode(' OR ',$where_or_arr);
		
		if ($where_and_str != '' && $where_or_str != '')
			$where = "WHERE ".$where_and_str." AND ( ".$where_or_str." ) ";
		elseif ($where_and_str != '' && $where_or_str == '')
			$where = "WHERE ".$where_and_str;
		elseif ($where_and_str == '' && $where_or_str != '')
			$where = "WHERE ".$where_or_str;
		
		$strSQL = "SELECT COUNT(DISTINCT(it.id))
					FROM ".ITEMS_TABLE." it
					LEFT JOIN ".COMMENTS_TABLE." ct ON it.id=ct.id_parent
					".$where."
					";
		return $this->dbconn->GetOne($strSQL);
	}
	
	function getExperts($from=0, $count=0){
		
		if($from || $count){
			$limit = ' LIMIT '.$from.','.$count;
		}else
			$limit = '';
			
		$strSQL = "SELECT ut.id as id_user, ut.login, DATE_FORMAT(ut.date_birthday,'%Y-%m-%d %h:%i:%s') AS date_birthday, ut.icon_path, ut.gender, ut.root_user, COUNT(ct.id) as answers_count
					FROM ".COMMENTS_TABLE." ct			
					LEFT JOIN ".USERS_TABLE." ut ON ct.id_owner=ut.id
					WHERE ct.is_best='1'
					GROUP BY ut.id
					ORDER BY answers_count DESC
					".$limit."
					";
		$rs = $this->dbconn->Execute($strSQL);
		$i=1;
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			
			$row['place'] = $from?$from*$count+$i:$i;
			
			$row['age'] = AgeFromBDate($row['date_birthday']);
			$icon_path = $this->config['site_path'].$this->config['icons_folder'].'/'.$row['icon_path'];
			if($row['icon_path'] !='' && file_exists($icon_path)){
				$row['icon_url'] = $this->config['server'].$this->config['site_root'].$this->config['icons_folder'].'/'.$row['icon_path'];
			}else{
				$row['icon_url'] = $this->config['server'].$this->config['site_root'].$this->config['icons_folder'].'/'.'default_'.$row['gender'].'.gif';
			}
			
			$strSQL = "SELECT COUNT(ct.id) as answ_count, cat.name
						FROM ".COMMENTS_TABLE." ct
						LEFT JOIN ".ITEMS_TABLE." it ON ct.id_parent=it.id
						LEFT JOIN ".CATEGORIES_TABLE." cat ON it.id_parent=cat.id
						WHERE ct.id_owner='".$row['id_user']."' AND ct.is_best='1'
						GROUP BY it.id_parent
						ORDER BY answ_count DESC
						";
			$rs1 = $this->dbconn->Execute($strSQL);
			while(!$rs1->EOF){
				$row1 = $rs1->GetRowAssoc(false);
				$row["count_answ_in_cats"][] = $row1;
				$rs1->MoveNext();
			}
			$experts[] = $row;
			$rs->MoveNext();
			$i++;
		}
		return $experts;
	}
	
	function getCountExperts(){
		$strSQL = "SELECT COUNT(DISTINCT(ct.id_owner))
					FROM ".COMMENTS_TABLE." ct
					WHERE ct.is_best='1'
					";
		return $this->dbconn->GetOne($strSQL);
	}
	
	function getExpertAnswers($from=0, $count=0, $id_user){
		
		$id_user = intval($id_user);
		
		if($from || $count){
			$limit = ' LIMIT '.$from.','.$count;
		}else
			$limit = '';
			
		$strSQL = "SELECT ct.text as answer, it.text as question, cat.name as cat_name 
					FROM ".COMMENTS_TABLE." ct
					LEFT JOIN ".ITEMS_TABLE." it ON ct.id_parent=it.id
					LEFT JOIN ".CATEGORIES_TABLE." cat ON it.id_parent=cat.id
					WHERE ct.id_owner='".$id_user."' AND ct.is_best='1'
					ORDER BY ct.date_add DESC
					".$limit."
		";
		$rs = $this->dbconn->Execute($strSQL);
		while (!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$answers[] = $row;
			$rs->MoveNext();
		}
		return $answers;
	}
	
	function getCountExpertAnswers($id_user){
		$id_user = intval($id_user);
			
		$strSQL = "SELECT COUNT(ct.id)
					FROM ".COMMENTS_TABLE." ct
					WHERE ct.id_owner='".$id_user."' AND ct.is_best='1'
		";
		return $this->dbconn->GetOne($strSQL);
	}

	function getStat($id_user){
		
		$strSQL = "SELECT COUNT(id)
						FROM ".COMMENTS_TABLE."
						WHERE id_owner='".$id_user."'";
		$stat['count_all_answ'] = $this->dbconn->GetOne($strSQL);
		
		$strSQL = "SELECT COUNT(id)
						FROM ".COMMENTS_TABLE."
						WHERE id_owner='".$id_user."' AND is_best='1'";
		$stat['count_best_answ'] = $this->dbconn->GetOne($strSQL);
		
		$strSQL = "SELECT COUNT(id)
						FROM ".ITEMS_TABLE."
						WHERE id_owner='".$id_user."'";
		$stat['count_all_quest'] = $this->dbconn->GetOne($strSQL);
		
		$strSQL = "SELECT COUNT(id)
						FROM ".ITEMS_TABLE."
						WHERE id_owner='".$id_user."' AND is_open='1'";
		$stat['count_open_quest'] = $this->dbconn->GetOne($strSQL);
		
		$strSQL = "SELECT COUNT(id)
						FROM ".ITEMS_TABLE."
						WHERE id_owner='".$id_user."' AND is_open='0'";
		$stat['count_closed_quest'] = $this->dbconn->GetOne($strSQL);
		
		$strSQL = "SELECT count(id) as best_answ_count, id_owner
						FROM ".COMMENTS_TABLE."
						WHERE is_best='1'
						GROUP BY id_owner
						ORDER BY best_answ_count DESC
						";
		$rs = $this->dbconn->Execute($strSQL);
		$i = 0;
		while (!$rs->EOF){
			if ($i == 0) $i=1;
			if ($rs->fields[1] == $id_user) break;
			$rs->MoveNext();
			$i++;
		}
		if ($i > 0)
			$stat['experts_place'] = $i;
		else
			$stat['experts_place'] = $this->lang['answers']['no_place'];
		return $stat;
	}
	
	function getErrorMsg(){
		return $this->lang["answers"][$this->error_arr[$this->error_no]];
	}
}

?>