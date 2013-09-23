<?php

/**
* Used for count percent of user's profile completion
*
* @package DatingPro
* @subpackage Include files
**/

class Percent
{
	var $dbconn;
	var $id_user;
	var $PROFILE_SECTION_ARR = array(
			1=> "Personal Info",
			2=> "My description",
			3=> "My personality",
			4=> "My portrait",
			5=> "My interests",
			6=> "Criteria",
			7=> "Criteria(interests)"
		);
	var $PERSENT_SECTION_ARR = array(
			1=> 20,
			2=> 25,
			3=> 5,
			4=> 5,
			5=> 10,
			6=> 25,
			7=> 10,
		);

	function Percent($id_user)
	{
		global $dbconn;
		$this->dbconn = $dbconn;
		$this->id_user = $id_user;
	}

	///// return percent of complited curent section
	/////(for geting general persent from all profile need to be * on $PERSENT_SECTION_ARR[$i])
	function CulcSection1Percent()
	{
		$strSQL = "select round( (
						if(length(login)>0,1,0) +
						if(length(fname)>0,1,0) +
						if(length(sname)>0,1,0) +
						if(gender>0,1,0) +
						if(length(email)>0,1,0) +
						if(id_country>0,1,0) +
						if(id_region>0,1,0) +
						if(id_city>0,1,0) +
						if(length(zipcode)>0,1,0) +
						if(id_nationality>0 and !ISNULL(id_nationality),1,0) +
						if((id_language_1>0 or id_language_2>0 or id_language_3>0) and !ISNULL(id_language_1 or id_language_2 or id_language_3),1,0) +
						if(length(headline)>0,1,0) +
						if(date_birthday!='0000-00-00 00:00:00',1,0)
						)*100/13) as all_score
						from ".USERS_TABLE." where id='".$this->id_user."'";
		$rs = $this->dbconn->Execute($strSQL);
		return intval($rs->fields[0]);
	}

	function CulcSection2Percent()
	{
		//// height and weight in this section now
		$fields_count = 2;
		$strSQL = "select (
						if(id_weight>0 and !ISNULL(id_weight),1,0) +
						if(id_height>0 and !ISNULL(id_height),1,0)
						) as all_score
						from ".USERS_TABLE." where id='".$this->id_user."'";
		$rs = $this->dbconn->Execute($strSQL);
		$value = intval($rs->fields[0]);

		$strSQL = "SELECT COUNT(id) FROM ".DESCR_SPR_TABLE;
		$rs = $this->dbconn->Execute($strSQL);
		$fields_count += intval($rs->fields[0]);
		
		if ($fields_count > 0) {
			$strSQL = "select s.id, if( !ISNULL(su.id_value) and max(su.id_value)>0, 1, 0) as value
						from ".DESCR_SPR_TABLE." as s
						left join ".DESCR_SPR_USER_TABLE." su on su.id_user='".$this->id_user."' and su.id_spr=s.id
						group by s.id";

			$rs = $this->dbconn->Execute($strSQL);
			while (!$rs->EOF) {
				$row = $rs->GetRowAssoc(false);
				$value += $row["value"];
				$rs->MoveNext();
			}
			return round($value * 100 / $fields_count);
		}
		else
		{
			return 100;
		}
	}

	function CulcSection3Percent()
	{
		$strSQL = "Select count(*) from ".PERSON_SPR_TABLE;
		$rs = $this->dbconn->Execute($strSQL);
		$fields_count = intval($rs->fields[0]);
		if ($fields_count > 0) {
			$value = 0;
			$strSQL = "select s.id, if( !ISNULL(su.id_value) and max(su.id_value)>0, 1, 0) as value
						from ".PERSON_SPR_TABLE." as s
						left join ".PERSON_SPR_USER_TABLE." su on su.id_user='".$this->id_user."' and su.id_spr=s.id
						group by s.id";
			$rs = $this->dbconn->Execute($strSQL);
			while(!$rs->EOF) {
				$row = $rs->GetRowAssoc(false);
				$value += $row["value"];
				$rs->MoveNext();
			}
			return round($value*100/$fields_count);
		}
		else
		{
			return 100;
		}
	}

	function CulcSection4Percent()
	{
		$strSQL = "Select count(*) from ".PORTRAIT_SPR_TABLE;
		$rs = $this->dbconn->Execute($strSQL);
		$fields_count = intval($rs->fields[0]);
		if ($fields_count > 0) {
			$value = 0;
			$strSQL = "select s.id, if( !ISNULL(su.id_value) and max(su.id_value)>0, 1, 0) as value
						from ".PORTRAIT_SPR_TABLE." as s
						left join ".PORTRAIT_SPR_USER_TABLE." su on su.id_user='".$this->id_user."' and su.id_spr=s.id
						group by s.id";
			$rs = $this->dbconn->Execute($strSQL);
			while (!$rs->EOF) {
				$row = $rs->GetRowAssoc(false);
				$value += $row["value"];
				$rs->MoveNext();
			}
			return round($value * 100 / $fields_count);
		}
		else
		{
			return 100;
		}
	}

	function CulcSection5Percent()
	{
		$strSQL = "Select count(*) from ".INTERESTS_SPR_TABLE;
		$rs = $this->dbconn->Execute($strSQL);
		$fields_count = intval($rs->fields[0]);
		if ($fields_count > 0) {
			$value = 0;
			$strSQL = "select s.id, if( !ISNULL(su.id_value) and max(su.id_value)>0, 1, 0) as value
						from ".INTERESTS_SPR_TABLE." as s
						left join ".INTERESTS_SPR_USER_TABLE." su on su.id_user='".$this->id_user."' and su.id_spr=s.id
						group by s.id";
			$rs = $this->dbconn->Execute($strSQL);
			while (!$rs->EOF) {
				$row = $rs->GetRowAssoc(false);
				$value += $row["value"];
				$rs->MoveNext();
			}
			return round($value*100/$fields_count);
		}
		else
		{
			return 100;
		}
	}

	function CulcSection6Percent()
	{
		///// first part
		$fields_count = 5;
		$value = 0;

		$strSQL = "select round(
						if(length(id_country)>0,1,0) +
						if(length(id_nationality)>0,1,0) +
						if(length(id_language)>0,1,0) +
						if(!ISNULL(id_weight),1,0) +
						if(!ISNULL(id_height),1,0)
						) as all_score
						from ".USER_MATCH_TABLE." where id_user='".$this->id_user."'";
		$rs = $this->dbconn->Execute($strSQL);
		$row = $rs->GetRowAssoc(false);
		$value += $row["all_score"];
		///// second part
		$strSQL = "SELECT COUNT(id) from ".DESCR_SPR_TABLE." ";
		$rs = $this->dbconn->Execute($strSQL);
		$fields_count += intval($rs->fields[0]);
		if ($fields_count > 0) {
			$strSQL = "select s.id, if( !ISNULL(su.id_value), 1, 0) as value
						from ".DESCR_SPR_TABLE." as s
						left join ".DESCR_SPR_MATCH_TABLE." su on su.id_user='".$this->id_user."' and su.id_spr=s.id
						group by s.id";
			$rs = $this->dbconn->Execute($strSQL);
			while (!$rs->EOF) {
				$row = $rs->GetRowAssoc(false);
				$value += $row["value"];
				$rs->MoveNext();
			}
		}
		return round($value*100/$fields_count);
	}

	function CulcSection7Percent()
	{
		$strSQL = "Select count(*) from ".INTERESTS_SPR_TABLE;
		$rs = $this->dbconn->Execute($strSQL);
		$fields_count = intval($rs->fields[0]);
		if ($fields_count > 0) {
			$value = 0;
			$strSQL = "select s.id, if( !ISNULL(su.id_value) and max(su.id_value)>0, 1, 0) as value
						from ".INTERESTS_SPR_TABLE." as s
						left join ".INTERESTS_SPR_MATCH_TABLE." su on su.id_user='".$this->id_user."' and su.id_spr=s.id
						group by s.id";
			$rs = $this->dbconn->Execute($strSQL);
			while (!$rs->EOF) {
				$row = $rs->GetRowAssoc(false);
				$value += $row["value"];
				$rs->MoveNext();
			}
			return round($value*100/$fields_count);
		}
		else
		{
			return 100;
		}
	}
////////////////////////////////////////
	function GetSectionPercent($section)
	{
		$strSQL = "Select section_".intval($section)." from ".USER_PROFILE_TABLE." where id_user='".$this->id_user."'";
		$rs = $this->dbconn->Execute($strSQL);
		return intval($rs->fields[0]);
	}
	
	function GetAllPercent()
	{
		$strSQL = "Select percent from ".USER_PROFILE_TABLE." where id_user='".$this->id_user."'";
		$rs = $this->dbconn->Execute($strSQL);
		return intval($rs->fields[0]);
	}
	
	function GetSectionPercentForUser($section, $user_id)
	{
		$strSQL = "Select section_".intval($section)." from ".USER_PROFILE_TABLE." where id_user='".$user_id."'";
		$rs = $this->dbconn->Execute($strSQL);
		return intval($rs->fields[0]);
	}
	
	function GetAllPercentForUser($user_id)
	{
		$strSQL = "Select percent from ".USER_PROFILE_TABLE." where id_user='".$user_id."'";
		$rs = $this->dbconn->Execute($strSQL);
		return intval($rs->fields[0]);
	}
	
	function SetSectionPercent($section, $value)
	{
		$strSQL = "Select count(*) from ".USER_PROFILE_TABLE." where id_user='".$this->id_user."'";
		$rs = $this->dbconn->Execute($strSQL);
		if ($rs->fields[0] == 0) {
			$strSQL = "insert into ".USER_PROFILE_TABLE." (id_user, section_1, section_2, section_3, section_4, section_5, section_6, section_7, percent) values ('".$this->id_user."', '0', '0', '0', '0', '0', '0', '0', '0')";
			$this->dbconn->Execute($strSQL);
		}
		$strSQL = "update ".USER_PROFILE_TABLE." set section_".$section." = '".intval($value)."' where id_user='".$this->id_user."'";
		$this->dbconn->Execute($strSQL);
		$this->RefreshAllPercent();
		return;
	}
	
	function RefreshAllPercent()
	{
		$s_arr = $this->PERSENT_SECTION_ARR;
		$str_arr = array();
		foreach ($s_arr as $section => $percent) {
			array_push($str_arr, "(section_".$section."*".$percent.")/100");
		}
		$str_section = implode(" + ", $str_arr);
		$strSQL = "update ".USER_PROFILE_TABLE." set percent = (".$str_section.") where id_user='".$this->id_user."'";
		$this->dbconn->Execute($strSQL);
		return;
	}

	//////////////////////
	function UpdateSection1Percent()
	{
		$value = $this->CulcSection1Percent();
		$this->SetSectionPercent(1, $value);
	}
	
	function UpdateSection2Percent()
	{
		$value = $this->CulcSection2Percent();
		$this->SetSectionPercent(2, $value);
	}
	
	function UpdateSection3Percent()
	{
		$value = $this->CulcSection3Percent();
		$this->SetSectionPercent(3, $value);
	}
	
	function UpdateSection4Percent()
	{
		$value = $this->CulcSection4Percent();
		$this->SetSectionPercent(4, $value);
	}
	
	function UpdateSection5Percent()
	{
		$value = $this->CulcSection5Percent();
		$this->SetSectionPercent(5, $value);
	}
	
	function UpdateSection6Percent()
	{
		$value = $this->CulcSection6Percent();
		$this->SetSectionPercent(6, $value);
	}
	
	function UpdateSection7Percent()
	{
		$value = $this->CulcSection7Percent();
		$this->SetSectionPercent(7, $value);
	}
}
?>