<?php
/**
*
*
*
*   @author Yan Abdullaev<yan@pilotgroup.net>, Pilot Group <http://www.pilotgroup.net/>
*   @date   11.10.2007
*
*
*	Access data object class
*
**/

//Form constructor
define('ERR_ATTR_NOT_MATCH_PATTERN', 'Attribute not match associated pattern');
define('ERR_ATTR_NOT_SPECIFIED', 'Attribute value must be specified');
define('ERR_PASSES_NOT_EQUAL', 'Password and re-password must be equal');
define('ERR_INVALID_ATTR_ID', 'Invalid attribute id');

//Multilang reference offset
define('REFERENCE_OFFSET', 30);

//Attributes list separated by ',' without 'All' value
define('ADO_NO_ALL', '');

//Page types uses to display attributes different on different pages
define('PT_DEFAULT', 1);

//Form view section
define('FV_SEARCH_RESULTS', 1);

include_once "class.lang.php";
include_once "config_admin.php";
include_once "class.uploads.php";

class Ado{
	//ADODB Connection
	var $dbconn;

	//Config array
	var $config;

	//Upload object
	var $objUpload;

	//Language strings array
	var $lang;

	//Fiedls datasources
	var $ds;

	//Last posted attributes
	var $last_posted;

	//Multilang table keys
	var $KEY_FIELDS;
	var $KEY_SECTIONS;
	var $KEY_VALUES;
	var $KEY_DATASOURCES;

	function Ado(&$dbconn, &$config, $lang='', $objUpload=''){
		$this->dbconn=&$dbconn;
		$this->config=&$config;
		$this->lang=$lang;
		$this->ds=array();
		$this->last_posted=null;
		if ($objUpload=='') $this->objUpload=new Uploads($config['site_path'], $config['server'].$config['site_root']);
			else $this->objUpload=$objUpload;
		$multilang=new MultiLang();
		$this->KEY_FIELDS=$multilang->TableKey(ADO_FIELDS_TABLE);
		$this->KEY_SECTIONS=$multilang->TableKey(ADO_SECTIONS_TABLE);
		$this->KEY_VALUES=$multilang->TableKey(ADO_VALUES_TABLE);
		$this->KEY_DATASOURCES=$multilang->TableKey(ADO_DS_TABLE);
	}

	function saveDataSource($id_ds, $ds_name, $values, $new=false){
		$lang=new MultiLang();

		if ($new) {
			$strSQL="SELECT MAX(id_reference) as max_ds_id FROM ".REFERENCE_LANG_TABLE." WHERE table_key=".$this->KEY_DATASOURCES;
			$rs=$this->dbconn->Execute($strSQL);
			$id_ds=intval($rs->fields[0])+1;

			//If new datasource id intersects with other references we create first reference with id=reference offset
			if ($id_ds<REFERENCE_OFFSET) $id_ds=REFERENCE_OFFSET;
			$lang->FirstLangInsert($this->KEY_DATASOURCES, $id_ds, $ds_name);
		}

		if (count($values)>0) {
			$strSQL="SELECT id_reference FROM ".REFERENCE_LANG_TABLE." WHERE table_key=".$id_ds;
			$rs=$this->dbconn->Execute($strSQL);
			$ref_ids=array();
			while (!$rs->EOF) {
				$ref_ids[]=$rs->fields[0];
				$rs->MoveNext();
			}

			$new_set=array();
			foreach ($values as $value){
				$value['name']=$this->FormFilter($value['name'], true);
				$new_set[]=intval($value['id']);
				if (in_array($value['id'], $ref_ids)){
					$strSQL="UPDATE ".REFERENCE_LANG_TABLE." SET lang_".$this->config['default_lang']."='".$value['name']."'
							WHERE id_reference=".$value['id']." AND table_key=".$id_ds;
					$this->dbconn->Execute($strSQL);
				}
				else $lang->FirstLangInsert($id_ds, $value['id'], $value['name']);
			}

			//Delete all unreferenced values
			if (count($new_set)>0) {
				$strSQL="DELETE FROM ".REFERENCE_LANG_TABLE." WHERE id_reference NOT IN(".implode(', ', $new_set).") AND table_key=".$id_ds;
				$this->dbconn->Execute($strSQL);
			}
		}
		return $id_ds;
	}

	function getAllDataSources(){
		if (count($this->ds)>0) return $this->ds;
		$lang=new MultiLang();

		$add_ds=$lang->SelectDefaultLangList($this->KEY_DATASOURCES);

		foreach ($add_ds as $ds){
			$this->ds[]=array('id'=>$ds['id_ref'], 'name'=>$ds['name'], 'type'=>'control');
		}
		return $this->ds;
	}

	function getDataSource($id_ds){
		$ds=$this->getAllDataSources();
		foreach ($ds as $datasource){
			if ($datasource['id']==$id_ds) return $datasource;
		}
	}

	function getDataSourceValues($ds_id){
		$id_lang=$this->config['default_lang'];
		$strSQL="SELECT id_reference, lang_".$id_lang." FROM ".REFERENCE_LANG_TABLE." WHERE table_key=".$ds_id;
		$rs=$this->dbconn->Execute($strSQL);
		$values=array();
		$i=0;
		while (!$rs->EOF) {
			$values[$i]['id']=$rs->fields[0];
			$values[$i]['name']=stripslashes($rs->fields[1]);
			$rs->MoveNext();
			$i++;
		}
		return $values;
	}

	function deleteDataSource($id_ds){
		$strSQL="DELETE FROM ".REFERENCE_LANG_TABLE." WHERE table_key=".$id_ds;
		$this->dbconn->Execute($strSQL);
		$strSQL="DELETE FROM ".REFERENCE_LANG_TABLE." WHERE table_key=".$this->KEY_DATASOURCES." AND id_reference=".$id_ds;
		$this->dbconn->Execute($strSQL);
	}

	function deleteDsValue($id_ds, $id_values){
		if (is_array($id_values)) $where_str=" AND id_reference IN (".implode(', ', $id_values).")";
			else $where_str=" AND id_reference=".intval($id_values);
		$strSQL="DELETE FROM ".REFERENCE_LANG_TABLE." WHERE table_key=".$id_ds.$where_str;
		$this->dbconn->Execute($strSQL);
	}

	function addDsValue($id_ds, $value, $id_ref=''){
		$lang=new MultiLang();

		if ($id_ref==''){
			$strSQL="SELECT MAX(id_reference) as max_id FROM ".REFERENCE_LANG_TABLE." WHERE table_key=".$id_ds;
			$rs=$this->dbconn->Execute($strSQL);
			$id_ref=intval($rs->fields[0])+1;
		}
		$lang->FirstLangInsert($id_ds, $id_ref, $this->FormFilter($value, true));
	}

	function getFormAttributes($id_form, $ids_only=false, $filter_sql=''){
		$strSQL="SELECT b.id_attribute, b.attr_order FROM ".ADO_FORMS." b
				LEFT JOIN ".ADO_ATTRIBUTES." s ON s.id=b.id_attribute
				WHERE b.id=".$id_form." AND ISNULL(b.id_attribute)=0 ".$filter_sql."
				ORDER BY b.attr_order";
		$rs=$this->dbconn->Execute($strSQL);
		if ($rs->RowCount()==0) return array();

		$attrs=array();
		$attr_ids=array();
		$i=0;

		while (!$rs->EOF) {
			$row=$rs->GetRowAssoc(false);

			if ($ids_only==false) {
				$attrs[$i]['attr_order']=$row['attr_order'];
				$attrs[$i]['id']=intval($row['id_attribute']);
			}
			$attr_ids[]=intval($row['id_attribute']);
			$i++;
			$rs->MoveNext();
		}

		if ($ids_only) {
			return $attr_ids;
		}
		else {
			$attrs_data=$this->getSectionAttribute($attr_ids);
			foreach ($attrs as $index=>$attr){
				$attrs[$index]=array_merge($attrs[$index], $attrs_data[$attr['id']]);
			}
			return $attrs;
		}
	}

	function delForm($id_forms){
		if (is_array($id_forms)) $where_str=" id IN (".implode(', ', $id_forms).")";
			else $where_str=" id=".$id_forms;
		$strSQL="DELETE FROM ".ADO_FORMS." WHERE ".$where_str;
		$this->dbconn->Execute($strSQL);
	}

	function updateForm($id_form, $attrs){
		$strSQL="DELETE FROM ".ADO_FORMS." WHERE id=".$id_form;
		$this->dbconn->Execute($strSQL);
		foreach ($attrs as $order=>$id_attr){
			$strSQL="INSERT INTO ".ADO_FORMS."(id, id_attribute, attr_order) VALUES(".$id_form.", ".$id_attr.", ".$order.")";
			$this->dbconn->Execute($strSQL);
		}
	}

	function getSection($id_section){
		$lang=new MultiLang();

		$strSQL="SELECT id, name, is_const FROM ".ADO_SECTIONS." WHERE id=".$id_section;
		$rs=$this->dbconn->Execute($strSQL);

		$section=array();
		$row=$rs->GetRowAssoc(false);
		$section['id']=$row['id'];
		$temp=$lang->SelectDefaultLangName($this->KEY_SECTIONS, $id_section);
		$section['name']=$temp['name'];
		$section['is_const']=$row['is_const'];

		return $section;
	}

	function getSections(){
		$strSQL="SELECT id FROM ".ADO_SECTIONS." ORDER BY id";
		$rs=$this->dbconn->Execute($strSQL);

		$sections=array();
		$i=0;
		while (!$rs->EOF){
			$sections[$i]=$this->getSection($rs->fields[0]);
			$i++;
			$rs->MoveNext();
		}
		return $sections;
	}

	function getSectionAttribute($id_attrs){
		if (is_array($id_attrs)){
			if (count($id_attrs)==0) return array();
			$attr_str='a.id IN ('.implode(', ', $id_attrs).')';
		}
		else {
			if (intval($id_attrs)==0) return ERR_INVALID_ATTR_ID;
			$attr_str='a.id='.$id_attrs;
		}

		$strSQL="SELECT a.id, a.id_section, a.mandatory, a.control_type, a.control_setup, a.is_const FROM ".ADO_ATTRIBUTES." a
				WHERE ".$attr_str;
		
		$lang = new MultiLang();

		$rs=$this->dbconn->Execute($strSQL);

		$sec_attrs=array();
		while (!$rs->EOF){
			$attr=array();
			$row=$rs->GetRowAssoc(false);

			$attr['id']=intval($row['id']);
			$attr['id_section']=$row['id_section'];
			$attr['mandatory']=$row['mandatory'];
			$attr['control_type']=$row['control_type'];
			$attr['is_const']=$row['is_const'];
			$attr['setup']=unserialize(stripslashes($row['control_setup']));
			$temp=$lang->SelectDefaultLangName($this->KEY_FIELDS, $row['id']);
			$attr['field_name']=$temp['name'];

			switch ($attr['control_type']){
				case 'text':
								$temp_value=$lang->SelectDefaultLangName($this->KEY_VALUES, $attr['id']);
								$attr['setup']['def_value']=$temp_value['name'];
								break;
				case 'select':
								if (defined('ADO_NO_ALL')){
									//Attributes without 'All' value
									$no_all_attrs=explode(',', ADO_NO_ALL);
									if (!in_array($attr['id'], $no_all_attrs)) $attr['setup']['has_all']=true;
										else $attr['setup']['has_all']=false;
								}
								else $attr['setup']['has_all']=true;
								$attr['setup']['values']=$lang->SelectDefaultLangList($attr['setup']['datasource'], '', ' ORDER BY id');
								foreach ($attr['setup']['values'] as $index=>$value){
									$attr['setup']['values'][$index]['id']=$attr['setup']['values'][$index]['id_ref'];
									unset($attr['setup']['values'][$index]['id_ref']);
								}
								break;
				case 'textarea':
								$temp_value=$lang->SelectDefaultLangName($this->KEY_VALUES, $attr['id']);
								$attr['setup']['def_value']=$temp_value['name'];
								break;
			}
			$sec_attrs[$attr['id']]=$attr;
			$rs->MoveNext();
		}
		if (is_array($id_attrs)==false) return $attr; else return $sec_attrs;
	}

	function getSectionAttributes($id_section, &$total_count, $id_form='', $filter_sql='', $ids_only=false){
		$filter_str='';

		//Set id_form to form identifier to exclude section attributes, which already specified form has
		if ($id_form!='') {
			$form_attrs=$this->getFormAttributes($id_form);
			if (count($form_attrs)>0 && $form_attrs!=false) {
				$filter_str=' AND a.id NOT IN ( ';
				$len=count($form_attrs);
				for ($i=0; $i<$len;$i++){
					$filter_str.=$form_attrs[$i]['id'];
					if ($i!=($len-1)) $filter_str.=', ';
				}
				$filter_str.=')';
			}
		}

		//Get total attibute count
		$strSQL="SELECT COUNT(*) as count FROM ".ADO_ATTRIBUTES." a
				WHERE a.id_section=".$id_section.$filter_str.$filter_sql;
		$rs=$this->dbconn->Execute($strSQL);
		$total_count=$rs->fields[0];

		//Get section attribute
		$strSQL="SELECT a.id FROM ".ADO_ATTRIBUTES." a
				WHERE a.id_section=".$id_section.$filter_str.$filter_sql;
		$rs=$this->dbconn->Execute($strSQL);

		$attrs=array();
		while (!$rs->EOF) {
			$attrs[]=intval($rs->fields[0]);
			$rs->MoveNext();
		}

		if ($ids_only) return $attrs;
		if (count($attrs)>0) {
			$attrs_data=$this->getSectionAttribute($attrs);
		}
		foreach ($attrs as $index=>$id_attr){
			$attrs[$index]=array_merge($attrs[$index], $attrs_data[$id_attr]);
		}
		return $attrs;
	}

	function getAllAttributes(&$total_count, $filter_str='', $order_by='', $ids_only=false){
		$strSQL="";

		if ($filter_str!='') $filter_str=" WHERE ".$filter_str;
		if ($order_by=='') $order_by="ORDER BY a.id";

		//Get total attibute count
		$strSQL="SELECT COUNT(*) as count FROM ".ADO_ATTRIBUTES." a ".$filter_str.$order_by;
		$rs=$this->dbconn->Execute($strSQL);
		$total_count=$rs->fields[0];

		//Get section attribute
		$strSQL="SELECT a.id FROM ".ADO_ATTRIBUTES." a ".$filter_str.$order_by;
		$rs=$this->dbconn->Execute($strSQL);
		$attrs=array();
		while (!$rs->EOF) {
			$attrs[]=intval($rs->fields[0]);
			$rs->MoveNext();
		}

		//If only attribute IDs needed
		if ($ids_only) return $attrs;

		//Otherwise retrieve data
		$attrs_data=$this->getSectionAttribute($attrs);
		foreach ($attrs as $index=>$id_attr){
			$attrs[$index]=array_merge($attrs[$index], $attrs_data[$id_attr]);
		}
		return $attrs;
	}

	function addNewSection($section_data){
		$lang=new MultiLang();
		$new_sec_id=0;

		$strSQL="INSERT INTO ".ADO_SECTIONS."(name) VALUES('".addslashes($section_data['name'])."')";
		$this->dbconn->Execute($strSQL);
		$new_sec_id=$this->dbconn->Insert_ID();
		$lang->FirstLangInsert($this->KEY_SECTIONS, $new_sec_id, $section_data['name']);
		return $new_sec_id;
	}

	function delSection($id_section){
		if ($this->checkIfSectionIsConst($id_section)) return false;

		$lang=new MultiLang();

		$lang->DeleteRefName($id_section, $this->KEY_SECTIONS);
		$total_count=0;
		$attrs=$this->getSectionAttributes($id_section, $total_count);
		foreach ($attrs as $attr) $this->delSectionAttribute($attr['id']);
		$strSQL="DELETE FROM ".ADO_SECTIONS." WHERE id=".$id_section;
		$this->dbconn->Execute($strSQL);

		return true;
	}

	function addSectionAttribute($id_section, $attr_data){
		$lang=new MultiLang();

		$attr_data['field_name']=$this->FormFilter($attr_data['field_name'], true);

		//Add attribute
		$strSQL="INSERT INTO ".ADO_ATTRIBUTES."(id_section, mandatory, control_type) VALUES(".$id_section.", '".$attr_data['mandatory']."', '".$attr_data['control_type']."')";
		$this->dbconn->Execute($strSQL);
		$new_id=$this->dbconn->Insert_ID();
		$lang->FirstLangInsert($this->KEY_FIELDS, $new_id, $attr_data['field_name']);

		switch ($attr_data['control_type']){
			case 'text':
			case 'textarea':
							//id_reference = new attribute id
							$def_value=$attr_data['setup']['def_value'];
							$def_value=$this->FormFilter($def_value, true);
							$lang->FirstLangInsert($this->KEY_VALUES, $new_id, $def_value);
							$attr_data['setup']['def_value']=$new_id;
							break;
			case 'select':
							//Table key = new attribute id, id_reference = select id
							if ($attr_data['setup']['datasource']==-1) $new=true;
								else $new=false;
							$attr_data['setup']['datasource']=$this->saveDataSource($attr_data['setup']['datasource'], $attr_data['field_name'], $attr_data['setup']['values'], $new);
							unset($attr_data['setup']['values']);
							break;
		}
		$strSQL="UPDATE ".ADO_ATTRIBUTES." SET control_setup='".addslashes(serialize($attr_data['setup']))."' WHERE id=".$new_id;
		$this->dbconn->Execute($strSQL);
		return $new_id;
	}

	function updateSectionAttribute($id_attr, $attr_data){
		$lang=new MultiLang();

		//Get previous setup
		$strSQL="SELECT control_type FROM ".ADO_ATTRIBUTES." WHERE id=".$id_attr;
		$rs=$this->dbconn->Execute($strSQL);
		$last_type=$rs->fields[0];

		$attr_data['field_name']=$this->FormFilter($attr_data['field_name'], true);

		//Add attribute
		$strSQL="UPDATE ".ADO_ATTRIBUTES." SET mandatory='".$attr_data['mandatory']."', control_type='".$attr_data['control_type']."' WHERE id=".$id_attr;
		$this->dbconn->Execute($strSQL);
		$lang->SaveDefaultRefNames($this->KEY_FIELDS, $attr_data['field_name'],$id_attr);

		switch ($attr_data['control_type']){
			case 'text':
			case 'textarea':
							//id_reference = new attribute id
							$def_value=$attr_data['setup']['def_value'];
							$def_value=$this->FormFilter($def_value, true);
							if ($last_type=='select'){
								$lang->FirstLangInsert($this->KEY_VALUES, $id_attr, $def_value);
							}
							else $lang->SaveDefaultRefNames($this->KEY_VALUES, $def_value, $id_attr);
							$attr_data['setup']['def_value']=$new_id;
							break;
			case 'select':
							if ($last_type!='select') {
								$lang->DeleteRefName($id_attr, $this->KEY_VALUES);
							}
							if ($attr_data['setup']['datasource']==-1) $new=true;
								else $new=false;
							$attr_data['setup']['datasource']=$this->saveDataSource($attr_data['setup']['datasource'], $attr_data['field_name'], $attr_data['setup']['values'], $new);
							unset($attr_data['setup']['values']);
							break;
		}
		$strSQL="UPDATE ".ADO_ATTRIBUTES." SET control_setup='".addslashes(serialize($attr_data['setup']))."' WHERE id=".$id_attr;
		$this->dbconn->Execute($strSQL);
		return $new_id;
	}

	function delSectionAttribute($id_attr){
		if ($this->checkIfAttrIsConst($id_attr)) return false;

		$lang=new MultiLang();

		$attr_data=$this->getSectionAttribute($id_attr);
		switch ($attr_data['control_type']){
			case 'text':
			case 'textarea':
							$lang->DeleteRefName($id_attr, $this->KEY_VALUES);
							break;
		}
		$lang->DeleteRefName($id_attr, $this->KEY_FIELDS);
		$strSQL="DELETE FROM ".ADO_ATTRIBUTES." WHERE id=".$id_attr;
		$this->dbconn->Execute($strSQL);
		$strSQL="DELETE FROM ".ADO_FORMS." WHERE id_attribute=".$id_attr;
		$this->dbconn->Execute($strSQL);
		$strSQL="DELETE FROM ".ADO_OBJECTS." WHERE id_attr=".$id_attr;
		$this->dbconn->Execute($strSQL);
		return true;
	}

	function getPatterns(){
		$strSQL="SELECT id, name, pattern FROM ".ADO_PATTERNS;
		$rs=$this->dbconn->Execute($strSQL);

		$regs=array();
		$i=0;

		while (!$rs->EOF) {
			$row=$rs->GetRowAssoc(false);

			$id_reg=$row['id'];
			$regs[$id_reg]['id']=$row['id'];
			$regs[$id_reg]['name']=$row['name'];
			$regs[$id_reg]['pattern']=$row['pattern'];

			$i++;
			$rs->MoveNext();
		}
		return $regs;
	}

	function addNewPattern($regexp_data){
		$strSQL="INSERT INTO ".ADO_PATTERNS."(name, pattern) VALUES('".addslashes($regexp_data['name'])."', '".addslashes($regexp_data['reg_exp'])."')";
		$this->dbconn->Execute($strSQL);
		$new_id=$this->dbconn->Insert_ID();
		return $new_id;
	}

	function delPattern($regexp_id){
		$strSQL="DELETE FROM ".ADO_PATTERNS." WHERE id=".$regexp_id;
		$this->dbconn->Execute($strSQL);
	}

	function updatePatterns($regs){
		foreach ($regs as $reg_exp){
			$strSQL="UPDATE ".ADO_PATTERNS." SET pattern='".addslashes($reg_exp['reg_exp'])."' WHERE id=".$reg_exp['id'];
			$this->dbconn->Execute($strSQL);
		}
	}

	function checkIfSectionIsConst($id_section){
		$strSQL="SELECT is_const FROM ".SECTIONS_TABLE." WHERE id=".$id_section;
		$rs=$this->dbconn->Execute($strSQL);
		$is_const=($rs->fields[0]=='Y')?true:false;

		return $is_const;
	}

	function checkIfAttrIsConst($id_attr){
		$strSQL="SELECT is_const FROM ".SECTION_ATTRS_TABLE." WHERE id=".$id_attr;
		$rs=$this->dbconn->Execute($strSQL);
		$is_const=($rs->fields[0]=='Y')?true:false;

		return $is_const;
	}

	function getObjectAttribute($id_object, $id_attrs, $convert_to_str=false){
		$lang=new MultiLang();

		if ((is_array($id_attrs) && count($id_attrs)==0) || (!is_array($id_attrs) && (intval($id_attrs)==0))) return false;
		$attr_str=(is_array($id_attrs))?'a.id IN ('.implode(', ', $id_attrs).')': "a.id=".$id_attrs;
		$strSQL="SELECT a.id, a.control_type, a.control_setup, b.value FROM ".ADO_ATTRIBUTES." a
			LEFT JOIN ".ADO_OBJECTS." b ON a.id=b.id_attr
			WHERE ".$attr_str." AND b.id_object=".$id_object;
		$rs=$this->dbconn->Execute($strSQL);

		if ($rs->RowCount()==0) return false;
		$object_attrs=array();
		while (!$rs->EOF){
			$row=$rs->GetRowAssoc(false);
			$id_attr=intval($row['id']);
			$control_setup=unserialize(stripslashes($row['control_setup']));

			switch ($row['control_type']){
				case 'checkbox':
				case 'text':
				case 'textarea':
				case 'password':
								if ($convert_to_str) $temp=str_replace("\n", '<br>', $row['value']);
									else $temp=$row['value'];
								break;
				case 'select':
								$table_key=intval($control_setup['datasource']);
								if (strpos($row['value'], ',')) $temp=explode(',', $row['value']); else $temp=$row['value'];
								if (is_array($temp)){
									unset($res);
									foreach ($temp as $index=>$id_ref){
										if ($id_ref==-1) {
											$res=-1;
											break;
										}
										if ($convert_to_str) {
											$temp_lang=$lang->SelectDefaultLangName($table_key, $id_ref);
											$res[$id_ref]=$temp_lang['name'];
										}
										else $res[$id_ref]=$id_ref;
									}
									if ($convert_to_str) {
										if ($res!=-1) $res=implode(', ', $res); else $res=$this->lang['common']['all'];
									}
									$temp=$res;
								}
								else {
									if ($convert_to_str){
										if ($row['value']==-1) $temp=$this->lang['common']['all'];
										else {
											$temp_lang=$lang->SelectDefaultLangName($table_key, intval($row['value']));
											$temp=$temp_lang['name'];
										}
									}
									else {
										$temp=intval($row["value"]);
									}
								}
								break;
				case 'date':
								$temp=unserialize($row['value']);
								if ($convert_to_str) {
									if (isset($this->config['date_format']) && $this->config['date_format']!=''){
										$date_format=str_replace('%', '', $this->config['date_format']);
										$temp=date($date_format, mktime(0, 0, 0, $temp['month'], $temp['day'], $temp['year']));
									}
									else $temp=$temp['month'].'-'.$temp['day'].'-'.$temp['year'];
								}
								break;
				case 'file':
								$temp=array();
								switch ($control_setup['f_type']){
									case 'image':
										$upload_type=UT_ADO_IMAGES;
										$file_type=FT_IMAGE;
										break;
									case 'video':
										$upload_type=UT_ADO_VIDEO;
										$file_type=FT_VIDEO;
										break;
									case 'audio':
										$upload_type=UT_ADO_AUDIO;
										$file_type=FT_AUDIO;
										break;
									default:
										$upload_type=UT_ADO_IMAGES;
										$file_type=FT_IMAGE;
								}
								$upload_cfg=$this->objUpload->getUploadConfig($upload_type);
								$temp['path']=$upload_cfg['site_path'].'/'.$row['value'];
								if ($row['value']!='') $temp['href']=$upload_cfg['href'].'/'.$row['value'];
								break;
				case 'text_range':
								$temp=unserialize($row['value']);
								if ($temp['from']=='' || $temp['to']=='') $temp=null;
									elseif ($convert_to_str) $temp=$this->lang['forms']['from'].' '.$temp['from'].' '.$this->lang['forms']['to'].' '.$temp['to'];
								break;
				case 'place':
								$temp=unserialize($row['value']);
								if (intval($temp['country'])==0) {
									$temp=null;
									break;
								}
								if ($convert_to_str) {
									$_LANG_NEED_ID=array();
									if (isset($temp['country'])) $_LANG_NEED_ID['country'][]=$temp['country'];
									if (isset($temp['region'])) $_LANG_NEED_ID['region'][]=$temp['region'];
									if (isset($temp['city'])) $_LANG_NEED_ID['city'][]=$temp['city'];
									$values=$this->getBaseLang($_LANG_NEED_ID);
									$strLocation='';

									if (@intval($temp['country'])!=0) $strLocation=$values['country'][$temp['country']];
									if (@intval($temp['region'])!=0) $strLocation.=', '.$values['region'][$temp['region']];
									if (@intval($temp['city'])!=0) $strLocation.=', '.$values['city'][$temp['city']];
									$temp=$strLocation;
								}
								break;
			}
			if ($temp!==null) $object_attrs[$id_attr]=$temp;
			$rs->MoveNext();
		}
		if (is_array($id_attrs)==false) return $temp; else return $object_attrs;
	}

	function getAllObjectData($id_object, $convert_to_str=false){
		$strSQL="SELECT id_attr FROM ".ADO_OBJECTS." WHERE id_object=".$id_object;
		$rs=$this->dbconn->Execute($strSQL);
		if ($rs->RowCount()==0) return array();
		$attr_ids=array();
		while (!$rs->EOF) {
			$attr_ids[]=intval($rs->fields[0]);
			$rs->MoveNext();
		}
		$object_data=$this->getObjectAttribute($id_object, $attr_ids, $convert_to_str);
		return $object_data;
	}

	function saveObjectAttribute($id_object, $id_attr, $new_value){
		$attr=$this->getSectionAttribute($id_attr);

		$exist=false;
		//Check if attribute already exist in user data
		$strSQL="SELECT value FROM ".ADO_OBJECTS." WHERE id_object=".$id_object." AND id_attr=".$id_attr;
		$rs=$this->dbconn->Execute($strSQL);
		if ($rs->RowCount()>0) $exist=true;

		switch ($attr['control_type']){
			case 'checkbox':
			case 'text':
			case 'textarea':
							$new_value=trim($new_value);
							if ($new_value==''){
								$strSQL="DELETE FROM ".ADO_OBJECTS." WHERE id_object=".$id_object." AND id_attr=".$id_attr;
								$this->dbconn->Execute($strSQL);
							}
							else {
								if ($exist) $strSQL="UPDATE ".ADO_OBJECTS." SET value='".$new_value."' WHERE id_object=".$id_object." AND id_attr=".$id_attr;
									else $strSQL="INSERT INTO ".ADO_OBJECTS."(id_object, id_attr, value) VALUES(".$id_object.", ".$id_attr.", '".$new_value."')";
							}
							break;
			case 'password':
							if ($exist) $strSQL="UPDATE ".ADO_OBJECTS." SET value='".addslashes(md5($new_value))."' WHERE id_object=".$id_object." AND id_attr=".$id_attr;
								else $strSQL="INSERT INTO ".ADO_OBJECTS."(id_object, id_attr, value) VALUES(".$id_object.", ".$id_attr.", '".addslashes(md5($new_value))."')";
							break;
			case 'select':
							if (is_array($new_value)) $new_value=implode(',', $new_value);
							if ($exist) $strSQL="UPDATE ".ADO_OBJECTS." SET value='".$new_value."' WHERE id_object=".$id_object." AND id_attr=".$id_attr;
								else $strSQL="INSERT INTO ".ADO_OBJECTS."(id_object, id_attr, value) VALUES(".$id_object.", ".$id_attr.", '".$new_value."')";
							break;
			case 'place':
							if ($new_value['country']==0){
								$strSQL="DELETE FROM ".ADO_OBJECTS." WHERE id_object=".$id_object." AND id_attr=".$id_attr;
								$this->dbconn->Execute($strSQL);
							}
							else {
								$str_arr=serialize($new_value);
								if ($exist) $strSQL="UPDATE ".ADO_OBJECTS." SET value='".$str_arr."' WHERE id_object=".$id_object." AND id_attr=".$id_attr;
									else $strSQL="INSERT INTO ".ADO_OBJECTS."(id_object, id_attr, value) VALUES(".$id_object.", ".$id_attr.", '".$str_arr."')";
							}
							break;
			case 'date':
							$str_arr=serialize($new_value);
							if ($exist) $strSQL="UPDATE ".ADO_OBJECTS." SET value='".$str_arr."' WHERE id_object=".$id_object." AND id_attr=".$id_attr;
								else $strSQL="INSERT INTO ".ADO_OBJECTS."(id_object, id_attr, value) VALUES(".$id_object.", ".$id_attr.", '".$str_arr."')";
							break;
			case 'file':
							if (intval($new_value)==-1){
								//Delete file
								switch ($attr['setup']['f_type']){
									case 'image':
										$upload_type=UT_ADO_IMAGES;
										$file_type=FT_IMAGE;
										break;
									case 'video':
										$upload_type=UT_ADO_VIDEO;
										$file_type=FT_VIDEO;
										break;
									case 'audio':
										$upload_type=UT_ADO_AUDIO;
										$file_type=FT_AUDIO;
										break;
									default:
										$upload_type=UT_ADO_IMAGES;
										$file_type=FT_IMAGE;
								}
								$upload_cfg=$this->objUpload->getUploadConfig($upload_type);
								$oldfile=$rs->fields[0];
								unlink($upload_cfg['site_path'].'/'.$oldfile);
								$strSQL="DELETE FROM ".ADO_OBJECTS." WHERE id_attr=".$id_attr." AND id_object=".$id_object;
							}
							else {
								if ($exist) {
									$oldfile=$rs->fields[0];
									$filename=$this->uploadFile($attr, $new_value, $oldfile);
									$strSQL="UPDATE ".ADO_OBJECTS." SET value='".addslashes($filename)."' WHERE id_object=".$id_object." AND id_attr=".$id_attr;
								}
								else {
									$filename=$this->uploadFile($attr, $new_value);
									$strSQL="INSERT INTO ".ADO_OBJECTS."(id_object, id_attr, value) VALUES(".$id_object.", ".$id_attr.", '".addslashes($filename)."')";
								}
							}
							break;
			case 'text_range':
							$str_arr=serialize($new_value);
							if ($exist) $strSQL="UPDATE ".ADO_OBJECTS." SET value='".$str_arr."' WHERE id_object=".$id_object." AND id_attr=".$id_attr;
								else $strSQL="INSERT INTO ".ADO_OBJECTS."(id_object, id_attr, value) VALUES(".$id_object.", ".$id_attr.", '".$str_arr."')";
							break;
		}
		$this->dbconn->Execute($strSQL);
	}

	function saveObjectData($id_object, $attrs){
		foreach ($attrs as $id_attr=>$value){
			$this->saveObjectAttribute($id_object, $id_attr, $value);
		}
		return '';
	}

	function delObjectAttributes($id_objects){
		if (is_array($id_objects)) $where_str=" a.id_object IN (".implode(', ', $id_objects).")";
			else $where_str="a.id_object=".$id_objects;
		//Delete all uploaded files
		$strSQL="SELECT a.id_object, a.id_attr FROM ".ADO_OBJECTS." a
				LEFT JOIN ".ADO_ATTRIBUTES." b ON a.id_attr=b.id
				WHERE ".$where_str." AND b.control_type='file'";
		$rs=$this->dbconn->Execute($strSQL);
		while (!$rs->EOF) {
			$id_object=intval($rs->fields[0]);
			$id_attr=intval($rs->fields[1]);
			$file=$this->getObjectAttribute($id_object, $id_attr);
			if (file_exists($file['path'])) unlink($file['path']);
			$rs->MoveNext();
		}

		//Delete all object attributes
		if (is_array($id_objects)) $del_str=" id_object IN (".implode(', ', $id_objects).")";
			else $del_str=" id_object=".$id_objects;
		$strSQL="DELETE FROM ".ADO_OBJECTS." WHERE ".$del_str;
		$this->dbconn->Execute($strSQL);
	}

	function initPlaceCtrl($id_attrs, $id_object='', $object_values=null){
		$strSQL="SELECT id, name FROM ".COUNTRY_SPR_TABLE." ORDER BY name";
		$rs=$this->dbconn->Execute($strSQL);
		$countries=array();
		$i=0;
		while (!$rs->EOF) {
			$countries[$i]['id']=intval($rs->fields[0]);
			$countries[$i]['name']=$rs->fields[1];
			$i++;
			$rs->MoveNext();
		}

		$result['countries']=$countries;
		//If specified it gets user place date
		if ($id_object!='' || $object_values!=''){
			if ($id_object!='' && $object_values==null) $object_data=$this->getObjectAttribute($id_object, $id_attrs); else $object_data=$object_values;
			foreach ($id_attrs as $id_attr){
				if (intval($object_data[$id_attr]['country'])<=0) continue;

				//Select appropriate region list
				$regions=array();
				$i=0;
				$strSQL="SELECT id, name FROM ".REGION_SPR_TABLE." WHERE id_country=".$object_data[$id_attr]['country']." ORDER BY name";
				$rs=$this->dbconn->Execute($strSQL);
				while (!$rs->EOF) {
					$regions[$i]['id']=intval($rs->fields[0]);
					$regions[$i]['name']=$rs->fields[1];
					$i++;
					$rs->MoveNext();
				}

				$result[$id_attr]['region']=$regions;
				//Select appropriate city list
				if (intval($object_data[$id_attr]['region'])<=0) continue;
				$cities=array();
				$i=0;
				$strSQL=" SELECT id, name FROM ".CITY_SPR_TABLE." WHERE id_country=".$object_data[$id_attr]['country']." AND id_region=".$object_data[$id_attr]['region']." GROUP BY id ORDER BY name";
				$rs=$this->dbconn->Execute($strSQL);
				while (!$rs->EOF) {
					$cities[$i]['id']=intval($rs->fields[0]);
					$cities[$i]['name']=$rs->fields[1];
					$i++;
					$rs->MoveNext();
				}
				$result[$id_attr]['city']=$cities;
			}
		}
		return $result;
	}

	function attrSearch($id_attr, $value, $id_object=''){
		$strSQL="SELECT a.id_object FROM ".ADO_OBJECTS." a
				WHERE a.id_attr=".$id_attr." AND a.value='".$value."'";
		if ($id_object!='') $strSQL.=' AND a.id_object='.$id_object;
		$rs=$this->dbconn->Execute($strSQL);
		if ($rs->RowCount()==0) return false;
		$objects=array();
		$i=0;
		while (!$rs->EOF) {
			$objects[]=intval($rs->fields[0]);
			$rs->MoveNext();
		}
		return $objects;
	}

	function searchLike($value){
		$strSQL="SELECT o.id_object FROM ".ADO_ATTRIBUTES." a
				LEFT JOIN ".ADO_OBJECTS." o ON a.id=o.id_attr
				WHERE a.control_type IN ('text', 'textarea') AND o.value LIKE '%".$value."%' GROUP BY o.id_object";
		$rs=$this->dbconn->Execute($strSQL);
		$objects=array();
		while (!$rs->EOF) {
			$objects[]=intval($rs->fields[0]);
			$rs->MoveNext();
		}
		return $objects;
	}

	function uploadFile($attr, $file, $oldfile=''){
		switch ($attr['setup']['f_type']){
			case 'image':
				$upload_type=UT_ADO_IMAGES;
				$file_type=FT_IMAGE;
				break;
			case 'video':
				$upload_type=UT_ADO_VIDEO;
				$file_type=FT_VIDEO;
				break;
			case 'audio':
				$upload_type=UT_ADO_AUDIO;
				$file_type=FT_AUDIO;
				break;
			default:
				$upload_type=UT_ADO_IMAGES;
				$file_type=FT_IMAGE;
		}
		$upload_cfg=$this->objUpload->getUploadConfig($upload_type);
		if ($oldfile!='') unlink($upload_cfg['site_path'].'/'.$oldfile);
		$new_file_name=$this->objUpload->uploadFile($file, $file_type, $upload_type);
		return $new_file_name;
	}

	function checkFile($id_attr, $file){
		if ($file['name']=='') return '';
		$attr=$this->getSectionAttribute($id_attr);
		switch ($attr['setup']['f_type']){
			case 'image':
				$upload_type=UT_ADO_IMAGES;
				$file_type=FT_IMAGE;
				break;
			case 'video':
				$upload_type=UT_ADO_VIDEO;
				$file_type=FT_VIDEO;
				break;
			case 'audio':
				$upload_type=UT_ADO_AUDIO;
				$file_type=FT_AUDIO;
				break;
			default:
				$upload_type=UT_ADO_IMAGES;
				$file_type=FT_IMAGE;
			break;
		}
		$upload_cfg=$this->objUpload->getUploadConfig($upload_type);
		$this->objUpload->config['upload_cfg'][$upload_type]['file_max_size']=1024*(intval($attr['setup']['max_size']));
		$res=$this->objUpload->checkFile($file, $file_type, $upload_type);
		if ($res['error']!=''){
			switch ($res['error']){
				case ERR_FILE_EXCEED_MAX:
					return array('error'=>ERR_FILE_EXCEED_MAX, 'id_attr'=>$id_attr, 'field_name'=>$attr['field_name'], 'max_size'=>$attr['setup']['max_size']);
					break;
				case ERR_FILE_INVALID_TYPE:
					return array('error'=>ERR_FILE_INVALID_TYPE, 'id_attr'=>$id_attr, 'field_name'=>$attr['field_name']);
					break;
			}
		}
		return true;
	}

	function toDate($id_user, $id_date_attr){
		$value=$this->getUserAttribute($id_user);
		$date=$value['year'].'-'.$value['month'].'-'.$value['day'];
		return $date;
	}

	function getAttrsFromPost($post_data, $id_form, &$attrs, $force_passwords=false){
		$attrs=array();

		foreach ($post_data as $key=>$value){
			//File deletion
			$arr=array();
			if (preg_match('/^delfile_(\d*)/', $key, $arr)!=0){
				$id_file=$arr[1];
				$attrs[$id_file]=-1;
			}

			//Get posted attributes
			$arr=array();
			if (preg_match('/^attr_(\d*)/', $key, $arr)==0) continue;
			$id_attr=$arr[1];
			$sec_attrs[$id_attr]=$this->getSectionAttribute($id_attr);

			//Shade text values
			if (!is_array($value)) $value=$this->FormFilter($value);

			//Check if password needs refresh
			if ($sec_attrs[$id_attr]['control_type']=='password' && $force_passwords!=true){
				if (!isset($post_data['pass_'.$id_attr])) continue;
			}

			unset($post_data[$key]);
			if (is_array($value) && $sec_attrs[$id_attr]['control_type']=='select'){
				$temp=array();
				foreach ($value as $i=>$v) {
					if ($v==-1) {
						$temp=-1;
						break;
					}
					$temp[$v]=$v;
				}
				$post_data[$id_attr]=$temp;
			}
			else $post_data[$id_attr]=$value;
			$attrs[$id_attr]=$post_data[$id_attr];
		}

		//Checkbox & selects control, if attribute not exist in _POST data, but exist in Form attributes it sets to 0
		$form_fields=$this->getFormAttributes($id_form);

		foreach ($form_fields as $form_field){
			$id_attr=$form_field['id'];

			if ($form_field['control_type']=='checkbox'){
				//check if this field presence in filled attributes
				$keys=array_keys($attrs);
				if (!in_array($id_attr, $keys)) $attrs[$id_attr]=0;
				$sec_attrs[$id_attr]=$this->getSectionAttribute($id_attr);
			}

			if ($form_field['control_type']=='select'){
				//check if this field presence in filled attributes
				$keys=array_keys($attrs);
				if (!in_array($id_attr, $keys)) $attrs[$id_attr]='';
				$sec_attrs[$id_attr]=$this->getSectionAttribute($id_attr);
			}
		}

		//Save posted attrs
		$this->last_posted=$attrs;

		//File control
		foreach ($_FILES as $key=>$file){
			$arr=array();
			if (preg_match('/^attr_(\d*)/', $key, $arr)==0) continue;
			if ($file['name']=='') continue;
			$id_attr=$arr[1];
			$attrs[$id_attr]=$file;
			$err=$this->checkFile($id_attr, $file, $lang);
			$sec_attrs[$id_attr]=$this->getSectionAttribute($id_attr);
			if ($err!==true) return $err;
		}

		//Get attibutes patterns
		$regs=$this->getPatterns();
		foreach ($sec_attrs as $index=>$field){
			if (isset($field['setup']['reg_exp'])) {
				$id_reg=$sec_attrs[$index]['setup']['reg_exp'];
				$sec_attrs[$index]['setup']['reg_exp']=$regs[$id_reg]['reg_exp'];
			}
		}

		//Check user data
		$err='';
		$valid=true;
		foreach ($attrs as $id_attr=>$value){
			if ($sec_attrs[$id_attr]['mandatory']=='Y'){
				if ($value=='' || !isset($value)){
					return array('error'=>ERR_ATTR_NOT_SPECIFIED, 'id_attr'=>$id_attr, 'field_name'=>$sec_attrs[$id_attr]['field_name']);
				}
			}
			if (isset($sec_attrs[$id_attr]['setup']['reg_exp'])){
				$pattern=$sec_attrs[$id_attr]['setup']['reg_exp'];
				if (preg_match($pattern, $value)==0){
					return array('error'=>ERR_ATTR_NOT_MATCH_PATTERN, 'id_attr'=>$id_attr, 'field_name'=>$sec_attrs[$id_attr]['field_name']);
				}
			}

			//Check pass equal repass
			if ($sec_attrs[$id_attr]['control_type']=='password'){
				if (isset($post_data['pass_'.$id_attr])){
					$pass=strval($attrs[$id_attr]);
					$repass=strval($post_data['repass_'.$id_attr]);
					if ($pass!=$repass) return array('error'=>ERR_PASSES_NOT_EQUAL, 'id_attr'=>$id_attr, 'field_name'=>$sec_attrs[$id_attr]['field_name']);
				}
			}
		}
		return true;
	}

	function generateAttrsHTML($id_attrs, $id_object=null, $object_data=null, $id_page=PT_DEFAULT){
		//Convert to array if provided single attribute id
		if (!is_array($id_attrs)) $id_attrs=array(0=>$id_attrs);
		if (count($id_attrs)==0) return array();

		//If not specified get user data
		if ($id_object!=null && $object_data==null) $object_data=$this->getObjectAttribute($id_object, $id_attrs);
		if ($id_object!=null) $curr_object_data=$object_data=$this->getObjectAttribute($id_object, $id_attrs);

		$attr_data=$this->getSectionAttribute($id_attrs);

		//Prepare place controls
		$pctrl_ids=array();
		foreach ($attr_data as $id=>$attr){
			if ($attr['control_type']=='place') $pctrl_ids[]=$id;
		}
		if (count($pctrl_ids)>0) $pctrl=$this->initPlaceCtrl($pctrl_ids, $id_object, $object_data);

		//Determine styles
		$place_style='style="width: 305px;"';
		$text_style='style="width: 300px;"';
		$textarea_style='style="width: 300px; height:200px"';
		$select_style='style="width: 305px;"';
		$date_month_style='style="width: 39px;"';
		$date_day_style='style="width: 39px;"';
		$date_year_style='style="width: 70px;"';
		$range_style='style="width: 90px;"';
		switch ($id_page){
			default:
				break;
		}

		//Create HTML
		$res=array();

		foreach ($id_attrs as $order=>$id){
			$attr=$attr_data[$id];
			$type=$attr['control_type'];
			$html='';
			$object_value=(isset($object_data[$id]) && $type!='file')?$object_data[$id]:null;
			if ($type=='file' && isset($curr_object_data[$id])) $object_value=$curr_object_data[$id];

			if (isset($object_value) && !is_array($object_value)) $object_value=htmlspecialchars($object_value);

			switch ($type){
				case 'checkbox':
					$checked=($object_value==1 || ($attr['setup']['def_value']==1 && $object_value===null))?'checked':'';
					$html='<input type="checkbox" name="attr_'.$id.'" value="1" '.$checked.'>';
					break;
				case 'text':
					$text_value=($object_value!=null)?$object_value:$attr['setup']['def_value'];
					$html='<input type="text" name="attr_'.$id.'" value="'.$text_value.'" maxlength="'.$attr['setup']['max_len'].'" '.$text_style.'>';
					break;
				case 'select':
					$sel_type=$attr['setup']['select_type'];
					$values=$attr['setup']['values'];
					if ($sel_type=='dropdown'){
						$html='<select name="attr_'.$id.'" '.$select_style.'>';
						if ($attr['setup']['has_all']){
							$sel=($object_value==-1)?'selected':'';
							$html.='<option value="-1" '.$sel.'>'.$this->lang['common']['all'].'</option>';
						}
						foreach ($values as $index=>$vdata){
							$sel=($object_value==$vdata['id'])?'selected':'';
							$html.='<option value="'.$vdata['id'].'" '.$sel.'> '.$vdata['name'].'</option>';
						}
						$html.='</select>';
					}
					if ($sel_type=='listbox'){
						$mult=$attr['setup']['is_mult']?'multiple':'';
						$html='<select name="attr_'.$id.'[]" size="'.$attr['setup']['vsize'].'" '.$mult.' '.$select_style.'>';
						if ($attr['setup']['has_all']){
							$sel=($object_value==-1)?'selected':'';
							$html.='<option value="-1" '.$sel.'>'.$this->lang['common']['all'].'</option>';
						}
						foreach ($values as $index=>$vdata){
							if (is_array($object_value)){
								$sel=(isset($object_value[$vdata['id']]))?'selected':'';
							}
							else $sel=($object_value==$vdata['id'])?'selected':'';
							$html.='<option value="'.$vdata['id'].'" '.$sel.'> '.$vdata['name'].'</option>';
						}
						$html.='</select>';
					}
					if ($sel_type=='boxes'){
						if ($attr['setup']['has_all']){
							$sel=($object_value==-1)?'checked':'';
							$html.='<input type="checkbox" name="attr_'.$id.'[]" value="-1" '.$sel.'> '.$this->lang['common']['all'].'<br>';
						}
						foreach ($values as $index=>$vdata){
							if (is_array($object_value)){
								$sel=(isset($object_value[$vdata['id']]))?'checked':'';
							}
							else $sel=($object_value==$vdata['id'])?'checked':'';
							$sel=(isset($object_value[$vdata['id']]))?'checked':'';
							$html.='<input type="checkbox" name="attr_'.$id.'[]" value="'.$vdata['id'].'" '.$sel.'> '.$vdata['name'].'<br>';
						}
					}
					if ($sel_type=='radio'){
						if ($attr['setup']['has_all']){
							$sel=($object_value==-1 || $object_value==null)?'checked':'';
							$html.='<input type="radio" name="attr_'.$id.'" value="-1" '.$sel.'> '.$this->lang['common']['all'].'<br>';
						}
						foreach ($values as $index=>$vdata){
							$sel=($object_value==$vdata['id'])?'checked':'';
							$html.='<input type="radio" name="attr_'.$id.'" value="'.$vdata['id'].'" '.$sel.'> '.$vdata['name'].'<br>';
						}
					}
					break;
				case 'textarea':
					$text_value=($object_value!=null)?$object_value:$attr['setup']['def_value'];
					$html='<textarea name="attr_'.$id.'" '.$textarea_style.'>'.$text_value.'</textarea>';
					break;
				case 'password':
					$html='<input type="password" name="attr_'.$id.'" '.$text_style.'>';
					break;
				case 'file':
					$html['code']='<input type="file" name="attr_'.$id.'">';
					$html['href']=$object_value['href'];
					break;
				case 'date':
					//Month
					$html='	<select name="attr_'.$id.'[month]" '.$date_month_style.'>';
					for($x=1; $x<13; $x++){
						$sel=($object_value['month']==$x)?'selected':'';
						$html.='<option value="'.$x.'" '.$sel.'>'.$x.'</option>';
					}
					$html.='</select>';

					//Day
					$html.=' <select name="attr_'.$id.'[day]" '.$date_day_style.'>';
					for($x=1; $x<32; $x++){
						$sel=($object_value['day']==$x)?'selected':'';
						$html.='<option value="'.$x.'" '.$sel.'>'.$x.'</option>';
					}
					$html.='</select>';

					//Year
					$min_year=$attr['setup']['min_age'];
					$max_year=$attr['setup']['max_age'];
					$html.=' <select name="attr_'.$id.'[year]" '.$date_year_style.'>';
					for($x=$max_year; $x>($min_year-1); $x--){
						$sel=($object_value['year']==$x)?'selected':'';
						$html.='<option value="'.$x.'" '.$sel.'>'.$x.'</option>';
					}
					$html.='</select>';
					break;
				case 'text_range':
					$from_value=strval($object_value['from']);
					$to_value=strval($object_value['to']);
					$html=$this->lang['forms']['from'].' <input type="text" name="attr_'.$id.'[from]" value="'.$from_value.'" '.$range_style.'> '.
					      $this->lang['forms']['to'].' <input type="text" name="attr_'.$id.'[to]" value="'.$to_value.'" '.$range_style.'>';
					break;
				case 'place':
					//Country
					$temp_html='<div id="country_div_'.$id.'">';
					$temp_html.='<select name="attr_'.$id.'[country]" onchange="AdoSelectRegion('.$id.', '.$id_page.', this.value, \'region_div_'.$id.'\', \'city_div_'.$id.'\');" '.$place_style.'>';
					$temp_html.='<option value="0">'.$this->lang['common']['select_default'].'</option>';
					foreach ($pctrl['countries'] as $country){
						$sel=($object_value['country']==$country['id'])?'selected':'';
						$temp_html.='<option value="'.$country['id'].'" '.$sel.'>'.$country['name'].'</option>';
					}
					$temp_html.='</select></div>';
					$html['country']=$temp_html;

					//Region
					$temp_html='<div id="region_div_'.$id.'">';
					if (isset($pctrl[$id]['region'])){
						$temp_html.='<select name="attr_'.$id.'[region]" onchange="AdoSelectCity('.$id.', '.$id_page.', this.value, \'city_div_'.$id.'\');" '.$place_style.'>';
						$temp_html.='<option value="0">'.$this->lang['common']['select_default'].'</option>';
						foreach ($pctrl[$id]['region'] as $region){
							$sel=($region['id']==$object_value['region'])?'selected':'';
							$temp_html.='<option value="'.$region['id'].'" '.$sel.'>'.$region['name'].'</option>';
						}
					}
					$temp_html.='</select></div>';
					$html['region']=$temp_html;

					//City
					$temp_html='<div id="city_div_'.$id.'">';
					if (isset($pctrl[$id]['city'])){
						$temp_html.='<select name="attr_'.$id.'[city]" '.$place_style.'>';
						$temp_html.='<option value="0">'.$this->lang['common']['select_default'].'</option>';
						foreach ($pctrl[$id]['city'] as $city){
							$sel=($city['id']==$object_value['city'])?'selected':'';
							$temp_html.='<option value="'.$city['id'].'" '.$sel.'>'.$city['name'].'</option>';
						}
					}
					$temp_html.='</select></div>';
					$html['city']=$temp_html;
					break;
			}
			$res[$order]=$attr;
			$res[$order]['html']=$html;
		}
		return $res;
	}

	function generateJSCode($attrs, $js_form_name){
		$output='
			<script language="JavaScript" type="text/javascript" src="'.$this->config['server'].$this->config['site_root'].'/templates/admin/js/forms.js"></script>
			<script language="JavaScript" type="text/javascript">
			var err_empty=\''.$this->lang['err']['err_field_empty'].'\';
			var err_invalid=\''.$this->lang['err']['err_field_invalid'].'\';
			var err_pwd_equal=\''.$this->lang['err']['err_passes_not_equal'].'\';

			var regs=new Array(1);
			var mands=new Array(1);
			var fields=new Array(1);
			var pass=new Array(1);
			var places=new Array(1);
			var files=new Array(1);

			//All fields names
			//All passwords
			//Associated patterns
			//Mandatory fields
			';
		foreach ($attrs as $attr){
			$output.='fields["'.$attr['id'].'"]="'.$attr['field_name'].'";';
			if ($attr['control_type']=='password') $output.='pass["'.$attr['id'].'"]=1;';
			if (isset($attr['setup']['reg_exp'])) $output.='regs["'.$attr['id'].'"]='.$attr['setup']['reg_exp'].';';
			if ($attr['mandatory']=='Y') $output.='mands["'.$attr['id'].'"]=1;';
			if ($attr['control_type']=='place') $output.='places["'.$attr['id'].'"]=1;';
			if ($attr['control_type']=='file' && $attr['html']['href']!='') $output.='files["'.$attr['id'].'"]=1;';
		}

		$output.='
		function check_empty(id_attr, field){
			//Checkbox sets control
			if(field.type=="checkbox"){
				var form=field.form;
				var len=form.length;
				var count=0;
				var checked=false;
				for(var i=0; i<len; i++){
					if(form.elements[i].name.indexOf("attr_"+id_attr)!=-1){
						if(form.elements[i].checked) checked=true;
						count++;
						if(checked==true) break;
					}
				}

				if(count>1 && checked==false){
					error = err_empty.replace(/\[field\]/, fields[id_attr]);
					return error;
				} else return "";
			}

			//Files
			if(files[id_attr]!=null){
				if(files[id_attr]==1) return "";
			}

			//Other cases
			if(field.value==""){
				error = err_empty.replace(/\[field\]/, fields[id_attr]);
				return error;
			} else return "";
		}

		function check_pattern(id_attr, field){
			var pattern=regs[id_attr];
			if (field.value && pattern.test(field.value) == false)	{
				error = err_invalid.replace(/\[field\]/, fields[id_attr]);
				return error;
			} else return "";
		}

		function check_pwd_equal(id_attr, field){
			var rePwd=document.getElementById("attr_re_"+id_attr);
			if(field.value!=rePwd.value) return err_pwd_equal;
			return "";
		}

		function checkForm(){
			var form=document.'.$js_form_name.';
			var len=form.elements.length;

			//Check if all fields are filled coorectly (not empty and valid to patterns)
			for(var i=0;i<len;i++){
				if(!(/attr_/).test(form.elements[i].name)) continue;
				var pos=form.elements[i].name.indexOf("[");
				if(pos<0) pos=form.elements[i].name.length;
				var id_attr=form.elements[i].name.substring(5, pos);

				if(pass[id_attr]!=null){
					var refresh=document.getElementById("pass_"+id_attr);
					if(refresh!=null) {
						if(refresh.checked==false) continue;
					}
				}
				if(mands[id_attr]!=null) {
					var err=check_empty(id_attr, form.elements[i]);
					if(err!="") {
						window.alert(err);
						form.elements[i].focus();
						return false;
					}
				}
				if(regs[id_attr]!=null) {
					var err=check_pattern(id_attr, form.elements[i]);
					if(err!="") {
						window.alert(err);
						form.elements[i].focus();
						return false;
					}
				}
				if(pass[id_attr]!=null){
					var err=check_pwd_equal(id_attr, form.elements[i]);
					if(err!="") {
						window.alert(err);
						form.elements[i].focus();
						return false;
					}
				}
			}

			return true;
		}

		var site_path="'.$this->config['server'].$this->config['site_root'].'/";
		</script>';
		return $output;
	}

	function getPossibleAssFields($control_type, $ass_type='value', $id_attr=''){
		$av_types=array();
		switch ($control_type){
			case 'checkbox':
							$av_types[]='"checkbox"';
							break;
			case 'text':
							if ($ass_type=='value') $av_types[]='"text"';
								else $av_types[]='"date"';
							break;
			case 'select':
							$av_types[]='"select"';
							break;
			case 'date':	$av_types[]='"date"';
							break;
			case 'text_range':
							if ($ass_type=='value') $av_types[]='"text"';
								else $av_types[]='"date"';
							break;
			case 'place':
							$av_types[]='"place"';
							break;
			default: return false;
		}

		$strSQL="SELECT id FROM ".ADO_ATTRIBUTES." WHERE control_type IN (".implode(', ', $av_types).") ";
		if ($id_attr!='') $strSQL.=" AND id !=".$id_attr;
		$rs=$this->dbconn->Execute($strSQL);
		if ($rs->RowCount()==0) return false;

		$attr_ids=array();
		while (!$rs->EOF) {
			$attr_ids[]=$rs->fields[0];
			$rs->MoveNext();
		}
		return $attr_ids;
	}

	function toAge($date){
		if ($date['year']==0) return 'N/A';
		$cur_date=getdate();
		$age=$cur_date['year']-$date['year'];
		if (intval($cur_date['mon']-$date['month'])>0 || (intval($cur_date['mon']-$date['month'])==0 && intval($cur_date['mday']-$date['day'])>=0))
			$age++;
		return $age;
	}

	function getBaseLang($_LANG_NEED_ID){
		global $dbconn, $lang, $config, $config_index;
		if(isset($_LANG_NEED_ID["country"]) && count($_LANG_NEED_ID["country"])>0){
			$strSQL = "SELECT DISTINCT id, name FROM ".COUNTRY_SPR_TABLE." WHERE id IN (".implode(", ", $_LANG_NEED_ID["country"]).")";
			$rs = $dbconn->Execute($strSQL);
			$i=0;
			while(!$rs->EOF){
				$row = $rs->GetRowAssoc(false);
				$ret_arr["country"][$row["id"]] = $row["name"];
				$rs->MoveNext();
				$i++;
			}
		}

		if(isset($_LANG_NEED_ID["city"]) && count($_LANG_NEED_ID["city"])>0){
			$strSQL = "	SELECT DISTINCT id, name FROM ".CITY_SPR_TABLE." WHERE id IN (".implode(", ", $_LANG_NEED_ID["city"]).")";
			$rs = $dbconn->Execute($strSQL);
			$i=0;
			while(!$rs->EOF){
				$row = $rs->GetRowAssoc(false);
				$ret_arr["city"][$row["id"]] = $row["name"];
				$rs->MoveNext();
				$i++;
			}
		}
		if(isset($_LANG_NEED_ID["region"]) && count($_LANG_NEED_ID["region"])>0){
			$strSQL = "SELECT DISTINCT id, name FROM ".REGION_SPR_TABLE." WHERE id IN (".implode(", ", $_LANG_NEED_ID["region"]).")";
			$rs = $dbconn->Execute($strSQL);
			$i=0;
			while(!$rs->EOF){
				$row = $rs->GetRowAssoc(false);
				$ret_arr["region"][$row["id"]] = $row["name"];
				$rs->MoveNext();
				$i++;
			}
		}
		return $ret_arr;
	}

	function FormFilter($str, $escape=false){
		$str=strip_tags(trim(strval($str)));
		if ($escape){
			$str = stripslashes($str);
			$str = str_replace("\"", "&quot;", $str);
			$str = str_replace("'", "&#039;", $str);
		}
		else $str=addslashes($str);
		return $str;
	}
}
?>