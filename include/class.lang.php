<?php

/**
* Functions for multi-language references
*
* @package DatingPro
* @subpackage Include files
**/

class MultiLang
{
	var $TABLE_KEY_ARR = array(
		1=> DESCR_SPR_TABLE,
		2=> DESCR_SPR_VALUE_TABLE,
		3=> INTERESTS_SPR_TABLE,
		4=> LANGUAGE_SPR_TABLE,
		5=> NATION_SPR_TABLE,
		6=> PERSON_SPR_TABLE,
		7=> PERSON_SPR_VALUE_TABLE,
		8=> PORTRAIT_SPR_TABLE,
		9=> PORTRAIT_SPR_VALUE_TABLE,
		12=> WEIGHT_SPR_TABLE,
		13=> HEIGHT_SPR_TABLE,
		14=> RELATION_SPR_TABLE,
		15=> KISSLIST_SPR_TABLE,
		16=> HOTLIST_SPR_TABLE,
		22=> GALLERY_CATEGORIES_TABLE
	);
	var $settings_table = SETTINGS_TABLE;
	var $classified_used;

	function MultiLang()
	{
		global $dbconn;
		$this->dbconn = $dbconn;
		$this->classified_used = GetSiteSettings('use_pilot_module_classified');
		if (isset($this->classified_used) && $this->classified_used == 1) {
			$this->TABLE_KEY_ARR[17] = ADO_FIELDS_TABLE;
			$this->TABLE_KEY_ARR[18] = ADO_SECTIONS_TABLE;
			$this->TABLE_KEY_ARR[19] = ADO_VALUES_TABLE;
			$this->TABLE_KEY_ARR[20] = ADO_DS_TABLE;
			$this->TABLE_KEY_ARR[21] = ADS_CATEGORIES_TABLE;
		}
		return;
	}

	function TableKey($table_name, $num='')
	{
		if ($num > 0) {
			$num--;
			$ret_arr = array_keys($this->TABLE_KEY_ARR, $table_name);
			if (is_array($ret_arr)) {
				return $ret_arr[$num];
			} else {
				return $ret_arr[0];
			}
		} else {
			return array_search($table_name, $this->TABLE_KEY_ARR);
		}
	}

	function TableName($table_key)
	{
		return $this->TABLE_KEY_ARR[$table_key];
	}

	function ValuesIdArray($id_spr, $table_key)
	{
		$rs = $this->dbconn->Execute('SELECT id FROM '.$this->TableName($table_key).' WHERE id_spr = "'.$id_spr.'"');
		$id_arr = array();
		while (!$rs->EOF) {
			$id_arr[] = $rs->fields[0];
			$rs->MoveNext();
		}
		return $id_arr;
	}

	function DefaultFieldName()
	{
		global $config;
		return 'lang_'.$config['default_lang'];
	}
	
	function DiffFieldName($lang)
	{
		return 'lang_'.$lang;
	}
	
	function DeleteRefName($id, $table_key)
	{
		$this->dbconn->Execute('DELETE FROM '.REFERENCE_LANG_TABLE.' WHERE table_key = "'.$table_key.'" AND id_reference = "'.$id.'"');
	}
	
	function DeleteRefNames($id_arr, $table_key)
	{
		if (is_array($id_arr)) {
			$id_str = implode(',', $id_arr);
			$strSQL = 'DELETE FROM '.REFERENCE_LANG_TABLE.' WHERE table_key = "'.$table_key.'" AND id_reference in ('.$id_str.')';
			$this->dbconn->Execute($strSQL);
		}
	}
	
	function SelectDefaultLangList($table_key, $where_str='', $order_str='')
	{
		$field_name = $this->DefaultFieldName();
		
		if ($where_str != '') {
			$where_str = ' AND '.$where_str;
		}
		if ($order_str == '') {
			$order_str = ' ORDER BY id';
		}
		
		$rs = $this->dbconn->Execute(
			'SELECT id, '.$field_name.', id_reference
			   FROM '.REFERENCE_LANG_TABLE.'
			  WHERE table_key = "'.$table_key.'" '.$where_str.$order_str);
		$i = 0;
		$spr_arr = array();
		while (!$rs->EOF) {
			$spr_arr[$i]['id'] = $rs->fields[0];
			$spr_arr[$i]['name'] = $rs->fields[1];
			$spr_arr[$i]['id_ref'] = $rs->fields[2];
			$rs->MoveNext();
			$i++;
		}
		return $spr_arr;
	}

	function SelectDiffLangList($table_key, $lang, $where_str='', $order_str='')
	{
		$field_name = $this->DiffFieldName($lang);
		
		if ($where_str != '') {
			$where_str = ' AND '.$where_str;
		}
		if ($order_str == '') {
			$order_str = ' ORDER BY id';
		}
		
		$rs = $this->dbconn->Execute(
			'SELECT id, '.$field_name.', id_reference
			   FROM '.REFERENCE_LANG_TABLE.'
			  WHERE table_key = "'.$table_key.'" '.$where_str.$order_str);
		$i = 0;
		$spr_arr = array();
		while (!$rs->EOF) {
			$spr_arr[$i]['id'] = $rs->fields[0];
			$spr_arr[$i]['name'] = $rs->fields[1];
			$spr_arr[$i]['id_ref'] = $rs->fields[2];
			$rs->MoveNext();
			$i++;
		}
		return $spr_arr;
	}
	
	function SelectDefaultLangName($table_key, $id_ref)
	{
		$field_name = $this->DefaultFieldName();
		$rs = $this->dbconn->Execute('SELECT id, '.$field_name.' FROM '.REFERENCE_LANG_TABLE.' WHERE table_key = "'.$table_key.'" AND id_reference = "'.$id_ref.'"');
		$spr_arr = array();
		$spr_arr['id'] = $rs->fields[0];
		$spr_arr['name'] = $rs->fields[1];
		return $spr_arr;
	}
	
	function SelectDiffLangName($table_key, $id_ref, $lang)
	{
		$field_name = $this->DiffFieldName($lang);
		$rs = $this->dbconn->Execute('SELECT id, '.$field_name.' FROM '.REFERENCE_LANG_TABLE.' WHERE table_key = "'.$table_key.'" AND id_reference = "'.$id_ref.'"');
		$spr_arr = array();
		$spr_arr['id'] = $rs->fields[0];
		$spr_arr['name'] = $rs->fields[1];
		return $spr_arr;
	}
	
	function FirstLangInsert($table_key, $id_reference, $name)
	{
		$str_f = 'table_key, id_reference';
		$str_v = '"'.$table_key.'", "'.$id_reference.'"';
		$rs = $this->dbconn->Execute('SHOW FIELDS FROM '.REFERENCE_LANG_TABLE.'');
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			if (strpos($row['field'], 'lang') !== false) {
				$str_f .= ', '.$row['field'];
				$str_v .= ', "'.$name.'"';
			}
			$rs->MoveNext();
		}
		$strSQL = 'INSERT INTO '.REFERENCE_LANG_TABLE.' ('.$str_f.') VALUES ('.$str_v.')';
		$this->dbconn->Execute($strSQL);
	}
	
	function SaveNames($table_key, $lang_code, $names)
	{
		foreach ($names as $id => $name) {
			$strSQL =
				'UPDATE '.REFERENCE_LANG_TABLE.'
				    SET '.$this->DiffFieldName($lang_code).' = "'.$name.'"
				  WHERE table_key = "'.$table_key.'" AND id = "'.$id.'"';
			$this->dbconn->Execute($strSQL);
		}
		return;
	}
	
	function SaveDefaultNames($table_key, $names)
	{
		$this->SaveNames($table_key, $this->DefaultFieldName(), $names);
	}
	
	function SaveDefaultRefNames($table_key, $name, $id_ref)
	{
		$strSQL =
			'UPDATE '.REFERENCE_LANG_TABLE.'
				SET '.$this->DefaultFieldName().' = "'.$name.'"
			  WHERE table_key = "'.$table_key.'" AND id_reference = "'.$id_ref.'"';
		$this->dbconn->Execute($strSQL);
	}
	
	function GetMLIdByRef($table_key, $id_ref)
	{
		$rs = $this->dbconn->Execute('SELECT id FROM '.REFERENCE_LANG_TABLE.' WHERE table_key = "'.$table_key.'" AND id_reference = "'.$id_ref.'"');
		return $rs->fields[0];
	}
}

?>