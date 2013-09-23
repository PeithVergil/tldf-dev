<?php
define('ROOT_CAT', 1);

//Ado attributes definitions

//Object types
define('OT_CATEGORY', 1);
define('OT_OBJECT', 2);

//Object status
define('OS_HIDE', 1);
define('OS_SHOW', 2);

//Catalog movements
define('MT_UP', 1);
define('MT_DOWN', 2);

//Errors
define('ERR_UNKNOWN_OBJECT_TYPE', 'Unnknown Object Type');

include_once "class.ado.php";
include_once "class.lang.php";
include_once "class.uploads.php";
include_once "functions_common.php";

class Catalog{
	var $dbconn;
	var $config;
	var $objUpload;
	var $last_error;
	var $lang;
	var $ado;
	var $KEY_CATEGORIES;
	
	function Catalog(&$dbconn, &$config, &$lang){
		$this->config = &$config;
		$this->dbconn = &$dbconn;
		$objUpload=new Uploads($config['site_path'], $config['server'].$config['site_root']);
		$this->objUpload=&$objUpload;
		$this->ado=new Ado($dbconn, $config, $lang, $objUpload);
		$this->lang=$lang;
		$this->last_error='';
		$multilang=new MultiLang();
		$this->KEY_CATEGORIES=$multilang->TableKey(ADS_CATEGORIES_TABLE);
		$this->config+=GetSiteSettings(array('default_icon_audio', 'default_icon_video', 'icons_folder'));
	}

	function addCatObject($id_root, $object_type, $object_data){
		switch ($object_type){
			case OT_CATEGORY:
				$object_data['status']=OS_SHOW;
				$new_id=$this->createCategory($object_data);
				break;
			case OT_OBJECT:
				$new_id=$this->createObject($id_root, $object_data);
				break;
		}
		if (intval($new_id)==0) return 0;

		if ($object_type==OT_CATEGORY){
			$strSQL="SELECT MAX(sorter) FROM ".ADS_CATALOG_TABLE." WHERE object_type=".$object_type." AND id_cat=".$id_root;
			$rs=$this->dbconn->Execute($strSQL);
			$sorter=intval($rs->fields[0])+1;
		}
		else $sorter=0;
		$strSQL="INSERT INTO ".ADS_CATALOG_TABLE."(id_obj, object_type, status, id_cat, sorter) VALUES(".$new_id.", ".$object_type.", '".intval($object_data['status'])."', ".$id_root.", ".$sorter.")";
		$this->dbconn->Execute($strSQL);
		return $new_id;
	}
	
	function saveCatObject($id_object, $id_root, $object_type, $object_data){
		switch ($object_type){
			case OT_CATEGORY:
				$object_data['status']=OS_EXIST;
				$result=$this->saveCategory($id_object, $object_data);
				break;
			case OT_OBJECT:
				$result=$this->saveObject($id_object, $id_root, $object_data);
				break;
		}
		if (intval($result)==0) return false;
		
		if (isset($object_data['status'])) $status_str=", status='".intval($object_data['status'])."' ";
		$strSQL="UPDATE ".ADS_CATALOG_TABLE." SET id_cat=".$id_root.$status_str." WHERE id_obj=".$id_object." AND object_type=".$object_type;
		$this->dbconn->Execute($strSQL);
		return true;
	}

	function moveCatObject($id_obj, $object_type, $move_type){
		$strSQL="SELECT sorter, id_cat FROM ".ADS_CATALOG_TABLE." WHERE id_obj=".$id_obj." AND object_type=".$object_type;
		$rs=$this->dbconn->Execute($strSQL);
		$sorter=intval($rs->fields[0]);
		$id_cat=intval($rs->fields[1]);

		switch ($move_type){
			case MT_UP:
				//If item has min sorter then break
				if ($sorter==1) break;
				
				//Else change item sorter position
				$strSQL="SELECT id_obj, sorter FROM ".ADS_CATALOG_TABLE." 
						WHERE sorter<".$sorter." AND object_type=".$object_type." AND id_cat=".$id_cat." ORDER BY sorter DESC LIMIT 0,1";
				$rs=$this->dbconn->Execute($strSQL);
				$id_ex=$rs->fields[0];
				$sorter_ex=intval($rs->fields[1]);
				$strSQL="UPDATE ".ADS_CATALOG_TABLE." SET sorter=".$sorter_ex." WHERE id_obj=".$id_obj." AND object_type=".$object_type;
				$this->dbconn->Execute($strSQL);
				$strSQL="UPDATE ".ADS_CATALOG_TABLE." SET sorter=".$sorter." WHERE id_obj=".$id_ex." AND object_type=".$object_type;				
				$this->dbconn->Execute($strSQL);
				break;
			case MT_DOWN:
				//If item has max sorter then break
				$strSQL="SELECT id_obj, sorter FROM ".ADS_CATALOG_TABLE." 
						WHERE sorter>".$sorter." AND object_type=".$object_type." AND id_cat=".$id_cat." ORDER BY sorter LIMIT 0,1";
				$rs=$this->dbconn->Execute($strSQL);
				$id_ex=$rs->fields[0];
				$sorter_ex=intval($rs->fields[1]);
				if (intval($id_ex)==0) break;
				
				//Else change item sorter position
				$strSQL="UPDATE ".ADS_CATALOG_TABLE." SET sorter=".$sorter_ex." WHERE id_obj=".$id_obj." AND object_type=".$object_type;
				$this->dbconn->Execute($strSQL);
				$strSQL="UPDATE ".ADS_CATALOG_TABLE." SET sorter=".$sorter." WHERE id_obj=".$id_ex." AND object_type=".$object_type;
				$this->dbconn->Execute($strSQL);
				break;
		}
	}
	
	function delCatObject($id_object, $object_type){
		$strSQL="SELECT id_cat FROM ".ADS_CATALOG_TABLE." WHERE id_obj=".$id_object." AND object_type=".$object_type;
		$rs=$this->dbconn->Execute($strSQL);
		$id_root=intval($rs->fields[0]);
		
		switch ($object_type){
			case OT_CATEGORY:
				$this->deleteBranch($id_object);
				break;
			case OT_OBJECT:
				$this->deleteObject($id_object);
				break;
		}
		
		$strSQL="DELETE FROM ".ADS_CATALOG_TABLE." WHERE id_obj=".$id_object." AND object_type=".$object_type;
		$this->dbconn->Execute($strSQL);

		//Refresh sorter
		if ($object_type==OT_CATEGORY){
			$strSQL="SELECT id FROM ".ADS_CATALOG_TABLE." WHERE id_cat=".$id_root." AND object_type=".OT_CATEGORY." ORDER BY sorter";
			$rs=$this->dbconn->Execute($strSQL);
			$sorter=1;
			while (!$rs->EOF) {
				$id=$rs->fields[0];
				$strSQL="UPDATE ".ADS_CATALOG_TABLE." SET sorter=".$sorter." WHERE id=".$id;
				$this->dbconn->Execute($strSQL);
				$rs->MoveNext();
				$sorter++;
			}
		}
	}
	
	function getCatObject($id_objects, $object_type, $strict_empty=false){
		if (is_array($id_objects) && count($id_objects)==0) return array();
		if (!is_array($id_objects)){
			$single=true;
			$id_res=$id_objects;
			$id_objects=array(0=>$id_objects);
		}
		else $single=false;
		
		switch ($object_type){
			case OT_CATEGORY:
				$res=$this->getCategory($id_objects, $strict_empty);
				break;
			case OT_OBJECT:
				$res=$this->getObjects($id_objects);
				break;
		}
		
		//Add catalog data
		$id_cats=array();
		$strSQL="SELECT c.id_cat, c.status, c.id_obj, c.sorter, cat.name, r.lang_".$this->config['default_lang']." AS name_lang
				FROM ".ADS_CATALOG_TABLE." c
				LEFT JOIN ".ADS_CATEGORIES_TABLE." cat ON c.id_cat=cat.id
				LEFT JOIN ".REFERENCE_LANG_TABLE." r ON c.id_cat=r.id_reference AND r.table_key=".$this->KEY_CATEGORIES."
				WHERE c.id_obj IN (".implode(', ', $id_objects).") AND c.object_type=".$object_type." ORDER BY FIELD(c.id_obj, ".implode(', ', $id_objects).")";
		$rs=$this->dbconn->Execute($strSQL);
		while (!$rs->EOF) {
			$row=$rs->GetRowAssoc(false);
			$id_parent=intval($row['id_cat']);
			$id_object=intval($row['id_obj']);
			$res[$id_object]['id_parent']=$id_parent;
			if ($id_parent!=0) $res[$id_object]['cat_name']=$row['name_lang']?$row['name_lang']:$row['name'];
				else $res[$id_object]['cat_name']=null;
			$res[$id_object]['status']=intval($row['status']);
			$res[$id_object]['sorter']=intval($row['sorter']);
			$rs->MoveNext();
		}
		if ($single) return $res[$id_res]; else return $res;
	}
	
	function getCatObjects($id_root='', $object_type, $ids_only=false, $strict_empty=false, &$total_count, $lim_min='', $lim_max=''){
		//Get total count
		$strSQL="SELECT COUNT(id_obj) FROM ".ADS_CATALOG_TABLE." WHERE object_type=".$object_type;
		if ($id_root!='') $strSQL.=" AND id_cat=".$id_root;
		if ($strict_empty==true) $strSQL.=" AND status!='".OS_ABSENT."'";		
		$rs=$this->dbconn->Execute($strSQL);
		$total_count=$rs->fields[0];
		
		//Get ids
		$strSQL="SELECT id_obj FROM ".ADS_CATALOG_TABLE." WHERE object_type=".$object_type;
		if ($id_root!='') $strSQL.=" AND id_cat=".$id_root;
		if ($strict_empty==true) $strSQL.=" AND status!='".OS_ABSENT."'";
		$strSQL.=" ORDER BY sorter";
		if (strval($lim_min)!='') $strSQL.=" LIMIT ".$lim_min.", ".$lim_max;
		
		$rs=$this->dbconn->Execute($strSQL);
		if ($rs->RowCount()==0) {
			return array();
		}
		
		$id_objects=array();
		while (!$rs->EOF) {
			$id_objects[]=intval($rs->fields[0]);
			$rs->MoveNext();
		}
		if ($ids_only) return $id_objects;
		$res=$this->getCatObject($id_objects, $object_type);
		
		return $res;
	}
	
	function createCategory($cat_data){
		//Create category
		$lang=new MultiLang();
		$strSQL="INSERT INTO ".ADS_CATEGORIES_TABLE."(name) VALUES('".$cat_data['name']."')";
		$this->dbconn->Execute($strSQL);
		$new_id=$this->dbconn->Insert_ID();
		$lang->FirstLangInsert($this->KEY_CATEGORIES, $new_id, $cat_data['name']);
		
		$total_count=0;
		$const_attrs=$this->ado->getAllAttributes(&$total_count, " a.is_const='Y'", '', true);
		$this->ado->updateForm($new_id, $const_attrs);
		return $new_id;
	}
	
	function saveCategory($id_cat, $cat_data){
		//Save category
		$lang=new MultiLang();
		$strSQL="UPDATE ".ADS_CATEGORIES_TABLE." SET name='".$cat_data['name']."', image='".$image_name."' WHERE id=".$id_cat;
		$this->dbconn->Execute($strSQL);
		$lang->SaveDefaultRefNames($this->KEY_CATEGORIES, $cat_data['name'], $id_cat);
		return true;
	}
	
	function getCategory($id_cats, $strict_empty=true){
		if (is_array($id_cats)) {
			$ids_str=implode(', ', $id_cats);
			$where_str=' AND c.id_obj IN ('.$ids_str.')';
		}
		else {
			$ids_str=intval($id_cats);
			$where_str.=' AND c.id_obj='.$ids_str;
		}
		$strict_empty_str=" AND obj.status!='".OS_HIDE."'";

		$strSQL="SELECT c.id_obj, r.lang_".$this->config['default_lang']." as name, c.id_cat as id_cat, COUNT(obj.id) as obj_count
				FROM ".ADS_CATALOG_TABLE." c 
				LEFT JOIN ".ADS_CATEGORIES_TABLE." cat ON c.id_obj=cat.id
				LEFT JOIN ".REFERENCE_LANG_TABLE." r ON c.id_obj=r.id_reference AND table_key=".$this->KEY_CATEGORIES."
				LEFT JOIN ".ADS_CATALOG_TABLE." obj ON c.id_obj=obj.id_cat AND obj.object_type=".OT_OBJECT.$strict_empty_str."
				WHERE c.object_type=".OT_CATEGORY." ".$where_str." GROUP BY c.id_obj ORDER BY FIELD(c.id_obj, ".$ids_str.")";
		$rs=$this->dbconn->Execute($strSQL);
		if ($rs->RowCount()==0) return array();

		$cats=array();
		while (!$rs->EOF) {
			$row=$rs->GetRowAssoc(false);
			$id=intval($row['id_obj']);
			$cats[$id]['id']=$id;
			$cats[$id]['name']=$row['name'];
			$cats[$id]['obj_count']=intval($row['obj_count']);
			$rs->MoveNext();
		}
		
		if (is_array($id_cats)) return $cats; else return $cats[$id_cats];
	}

	function deleteCategory($id_cats){
		if (is_array($id_cats)) $where_str=' IN ('.implode(', ', $id_cats).')';
			else $where_str.='='.intval($id_cats);
			
		$strSQL="DELETE FROM ".ADS_CATEGORIES_TABLE." WHERE id".$where_str;
		$this->dbconn->Execute($strSQL);
		$strSQL="DELETE FROM ".REFERENCE_LANG_TABLE." WHERE table_key=".$this->KEY_CATEGORIES." AND id_reference".$where_str;
		$this->dbconn->Execute($strSQL);
		$this->ado->delForm($id_cats);
	}
	
	function getCatForm($id_cat, $form_name, $id_object=null, $get_post=false){
		$id_fields=$this->ado->getFormAttributes($id_cat, true);
		
		if ($get_post) {
			$posted_attrs=$this->ado->last_posted;
			if (!is_array($posted_attrs)){
				$this->ado->getAttrsFromPost($_POST, $id_cat, $posted_attrs);
			}
		}
		else $posted_attrs=null;
		
		$fields=$this->ado->generateAttrsHTML($id_fields, $id_object, $posted_attrs);
		$regs=$this->ado->getPatterns();

		foreach ($fields as $index=>$field){
			if (isset($field['setup']['reg_exp'])) {
				$id_reg=$fields[$index]['setup']['reg_exp'];
				$fields[$index]['setup']['reg_exp']=$regs[$id_reg]['pattern'];
			}
		}
		$js_code=$this->ado->generateJSCode($fields, $form_name);
		return array('fields'=>$fields, 'js'=>$js_code);
	}
	
	function traceRoot($id_object, $object_type){
		$result=array();
		$result[0]=$this->getCatObject($id_object, $object_type);
		if ($id_object==ROOT_CAT && $object_type==OT_CATEGORY) return $result;
		$id_cat=$result[0]['id_parent'];

		while ($id_cat!=ROOT_CAT && $id_cat!=0){
			$strSQL="SELECT id_cat FROM ".ADS_CATALOG_TABLE." WHERE id_obj=".$id_cat." AND object_type=".OT_CATEGORY;
			$rs=$this->dbconn->Execute($strSQL);
			$result[]=$this->getCategory($id_cat);
			$id_cat=intval($rs->fields[0]);
		}
		$result[]=$this->getCategory(ROOT_CAT);
		$result=array_reverse($result);
		return $result;
	}
	
	function deleteBranch($id_root){
		$all_ids=array();
		$all_ids[0]=$id_root;

		$childs=array(0=>$id_root);
		while(count($childs)!=0){
			$strSQL="SELECT id_obj FROM ".ADS_CATALOG_TABLE." WHERE id_cat IN (".implode(', ', $childs).") AND object_type=".OT_CATEGORY;
			$rs=$this->dbconn->Execute($strSQL);
			$childs=array();
			while (!$rs->EOF) {
				$childs[]=intval($rs->fields[0]);
				$rs->MoveNext();
			}
			if (count($childs)>0){
				$all_ids=array_merge($all_ids, $childs);
			}
		}
		
		$strSQL="SELECT id_obj FROM ".ADS_CATALOG_TABLE." WHERE id_cat IN (".implode(',', $all_ids).") AND object_type=".OT_OBJECT;
		$rs=$this->dbconn->Execute($strSQL);
		$objects=array();
		while (!$rs->EOF) {
			$objects[]=intval($rs->fields[0]);
			$rs->MoveNext();
		}
		
		if (count($all_ids)>0){
			$strSQL="DELETE FROM ".ADS_CATALOG_TABLE." WHERE id_obj IN (".implode(',', $all_ids).") AND object_type=".OT_CATEGORY;
			$this->dbconn->Execute($strSQL);
			$this->deleteCategory($all_ids);
		}
		if (count($objects)>0){
			$strSQL="DELETE FROM ".ADS_CATALOG_TABLE." WHERE id_obj IN (".implode(',', $objects).") AND object_type=".OT_OBJECT;
			$this->dbconn->Execute($strSQL);
			$this->deleteObject($objects);
		}
	}

	function createObject($id_cat, $object_data){
		//Get ado data from post
		$attrs=array();
		$res=$this->ado->getAttrsFromPost($object_data, $id_cat, $attrs, true);

		if (isset($res['error'])) {
			$this->setError($res);
			return 0;
		}
	
		//Create catalog object
		$strSQL="INSERT INTO ".ADS_OBJECTS_TABLE."(id_user, date_created, name, body)
				VALUES(".$object_data['id_user'].", NOW(), '".$object_data['name']."', '".$object_data['body']."')";
		$this->dbconn->Execute($strSQL);
		$new_id=$this->dbconn->Insert_ID();

		//Save new ado object
		$this->ado->saveObjectData($new_id, $attrs);
		return $new_id;
	}

	function saveObject($id_object, $id_cat, $object_data){
		//Get ado data from post
		$attrs=array();
		$res=$this->ado->getAttrsFromPost($object_data, $id_cat, $attrs, true);

		if (isset($res['error'])) {
			$this->setError($res);
			return 0;
		}

		//Update catalog object
		$strSQL="UPDATE ".ADS_OBJECTS_TABLE." SET name='".$object_data['name']."', body='".$object_data['body']."' WHERE id=".$id_object;
		$this->dbconn->Execute($strSQL);
		
		//Update ado object
		$this->ado->saveObjectData($id_object, $attrs);
		return true;
	}
	
	function getObjects($id_objects){
		//Get objects
		$ids_str=implode(', ', $id_objects);
		$strSQL="SELECT o.id, o.id_user, DATE_FORMAT(o.date_created,'".$this->config["date_format"]."') AS date_posted, o.name, o.body, c.id_cat
				FROM ".ADS_OBJECTS_TABLE." o
				LEFT JOIN ".ADS_CATALOG_TABLE." c ON o.id=c.id_obj AND object_type=".OT_OBJECT."
				WHERE o.id IN (".$ids_str.") ORDER BY FIELD(o.id, ".$ids_str.")";
		$rs=$this->dbconn->Execute($strSQL);
		
		$objects=array();
		while (!$rs->EOF) {
			$row=$rs->getRowAssoc(false);
			$id_object=intval($row['id']);
			
			$objects[$id_object]['id']=$id_object;
			$objects[$id_object]['id_user']=intval($row['id_user']);
			$objects[$id_object]['date_created']=$row['date_posted'];
			$objects[$id_object]['name']=$row['name'];
			$objects[$id_object]['body']=$row['body'];
						
			//Add ADO data
			$id_form=intval($row['id_cat']);
			$attrs_ids=$this->ado->getFormAttributes($id_form, true, 'AND s.control_type!="password"');
			$object_data=$this->ado->getObjectAttribute($id_object, $attrs_ids, true);
			$attrs=$this->ado->getSectionAttribute($attrs_ids);

			if (is_array($object_data) && count($object_data)>0){
				foreach ($attrs_ids as $id_attr){
					if ($attrs[$id_attr]['control_type']=='file'){
						if (!isset($object_data[$id_attr]) || !file_exists($object_data[$id_attr]['path'])) continue;
						$type=$attrs[$id_attr]['setup']['f_type'];
						
						switch ($type){
							case 'image':
								$upload_cfg=$this->objUpload->getUploadConfig(UT_ADO_IMAGES);
								$temp['icon']=$upload_cfg['href'].'/thumb_'.basename($object_data[$id_attr]['href']);
								break;
							case 'audio':
								$temp['icon']=$this->config['server'].$this->config['site_root'].$this->config['icons_folder'].'/'.$this->config['default_icon_audio'];
								break;
							case 'video':
								$temp['icon']=$this->config['server'].$this->config['site_root'].$this->config['icons_folder'].'/'.$this->config['default_icon_video'];
								break;
						}
						$objects[$id_object][$type][$id_attr]['field']=$attrs[$id_attr]['field_name'];
						$objects[$id_object][$type][$id_attr]['href']=$object_data[$id_attr]['href'];
						$objects[$id_object][$type][$id_attr]+=$temp;
					}
					else {
						$objects[$id_object]['ado'][]=array(
							'field'=>$attrs[$id_attr]['field_name'],
							'value'=>$object_data[$id_attr],
							'type'=>$attrs[$id_attr]['control_type']
						);
					}
				}
				
				$objects[$id_object]['image_count']=count($objects[$id_object]['image']);
				$objects[$id_object]['audio_count']=count($objects[$id_object]['audio']);
				$objects[$id_object]['video_count']=count($objects[$id_object]['video']);
			}
			$rs->MoveNext();
		}

		return $objects;
	}
	
	function deleteObject($id_objects){
		if (is_array($id_objects)) $where_str=" id IN (".implode(', ', $id_objects).")";
			else $where_str=" id=".$id_objects;
			
		$strSQL="DELETE FROM ".ADS_OBJECTS_TABLE." WHERE ".$where_str;
		$this->dbconn->Execute($strSQL);
		
		$this->ado->delObjectAttributes($id_objects);
	}
	
	function setError($error_data){
		$this->last_error=$error_data;
	}
	
	function getError(){
		return $this->last_error;
	}
	
	function getCatalog($id_root=ROOT_CAT, $strict_empty=false, $make_plain=true){
		global $level;
		$level=1;
		$tree=$this->getCatalogTree($id_root, $strict_empty);
		if (!$make_plain) return $tree;
		$plain_tree=$this->makePlain($tree);
		return $plain_tree;
	}
	
	function getCatalogTree($id_root, $strict_empty=false){
		global $level;
		$strsQL="SELECT id_obj FROM ".ADS_CATALOG_TABLE." WHERE id_cat=".$id_root." AND object_type=".OT_CATEGORY." ORDER BY sorter";
		$rs=$this->dbconn->Execute($strsQL);
		$level++;
		$childs=array();
		while (!$rs->EOF) {
			$id_object=intval($rs->fields[0]);
			$childs[]=$id_object;
			$rs->MoveNext();
		}
		
		if (count($childs)>0){
			for ($i=0;$i<count($childs); $i++){
				$child=$this->getCatalogTree($childs[$i], $strict_empty);
				if ($child!=false) $b_childs[$childs[$i]]=$child;
			}
			$level-=1;
			$cat_data=$this->getCatObject($id_root, OT_CATEGORY, $strict_empty);
			$cat_data['level']=$level;
			if ($cat_data['obj_count']!=0 && count($b_childs)==0 && $strict_empty) return $cat_data;
			if ($strict_empty && count($b_childs)==0) return false;
			$cat_data['tree_count']=$cat_data['obj_count'];
			foreach ($b_childs as $id_child=>$child){
				$cat_data['tree_count']+=$child['data']['tree_count'];
			}
			return array('data'=>$cat_data, 'childs'=>$b_childs);
		}
		else {
			$level-=1;
			$cat_data=$this->getCatObject($id_root, OT_CATEGORY, $strict_empty);
			if ($strict_empty && $cat_data['obj_count']==0) return false;
			$cat_data['level']=$level;
			$cat_data['tree_count']=$cat_data['obj_count'];
			return array('data'=>$cat_data);
		}
	}
	
	function makePlain($tree_data){
		if (isset($tree_data['childs'])){
			$res[$tree_data['data']['id']]=$tree_data['data'];
			foreach ($tree_data['childs'] as $id_child=>$child_data)
				$res+=$this->makePlain($child_data);
		}
		else $res[$tree_data['data']['id']]=$tree_data['data'];
		return $res;
	}
}
?>