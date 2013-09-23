<?php

class IpInfo
{
	var $dbconn;
	var $config;
	var $ip;
	var $ip_long;
	var $id_record;
	var $country;
	var $id_lang;
	var $iso_code;
	var $flag_path;


	function IpInfo($ip = '')
	{
		global $config, $dbconn;
		
		$this->dbconn = $dbconn;
		$this->config = $config;
		$this->ip = $ip ? $ip : $_SERVER['REMOTE_ADDR'];
		$this->ip_long = sprintf("%u", ip2long($this->ip));
		
		$strSQL = "SELECT id, code, name, id_lang FROM ".IP_COUNTRY_TABLE." WHERE ".$this->ip_long." BETWEEN f_long_ip AND s_long_ip";
		$rs = $this->dbconn->Execute($strSQL);
		if ($rs !== false){
			$row = $rs->GetRowAssoc(false);
			$this->id_record = $row["id"];
			$this->iso_code = strtolower($row["code"]);
			$this->country = $row["name"];
			$this->id_lang = $row["id_lang"];
			if (file_exists($this->config["site_path"].$this->config["index_theme_path"]."/images/flags/".$this->iso_code.".gif")){
				$this->flag_path = $this->config["site_root"].$this->config["index_theme_path"]."/images/flags/".$this->iso_code.".gif";
			}
		}
	}

	function GetAllCountries()
	{
		$strSQL = "SELECT DISTINCT(code) FROM ".IP_COUNTRY_TABLE." ORDER BY name";
		$rs = $this->dbconn->Execute($strSQL);
		$i=0;
		while (!$rs->EOF){
			$code = addslashes($rs->fields[0]);
			$strSQL = "SELECT name, code, id_lang FROM ".IP_COUNTRY_TABLE." WHERE code='".$code."'";
			$rs_inf = $this->dbconn->Execute($strSQL);
			$row_inf[$i] = $rs_inf->GetRowAssoc(false);
			$rs->MoveNext();$i++;
		}
		return $row_inf;
	}

	//$codes_arr - array of iso codes
	function SetIdLangToCountries($id_lang, $codes_arr)
	{
		$id_lang = intval($id_lang);

		foreach ($codes_arr as $key=>$value){
			$codes_arr[$key] = "'".addslashes($value)."'";
		}

		$codes_str = implode(",",$codes_arr);
		if (!$id_lang) return false;

		$strSQL = "UPDATE ".IP_COUNTRY_TABLE." SET id_lang='' WHERE id_lang='".$id_lang."' ";
		$this->dbconn->Execute($strSQL);

		$strSQL = "UPDATE ".IP_COUNTRY_TABLE." SET id_lang='".$id_lang."' WHERE code IN ( ".$codes_str." )";
		$this->dbconn->Execute($strSQL);

		if ($this->dbconn->ErrorNo() == 0) return true;
		else return false;
	}
}

?>