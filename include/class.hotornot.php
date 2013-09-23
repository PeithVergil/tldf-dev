<?php

class HotOrNot{
	
	var $dbconn;
	var $config;
	var $user;
	var $file_name = 'hot_or_not.php';
	var $hot_list_limit = 20;
	var $preview_limit = 4;
	var $top_amount_limit = 4;
	var $rated_ids = array();//array with ids of rated users by this user
	var $rated_ids_str = '';
	
	function HotOrNot($dbconn,$config,$user){
		$this->dbconn = $dbconn;
		$this->config = $config;
		$sett = $this->_getSiteSettings(array("icons_folder","icons_default","photos_folder","use_kiss_types","use_friend_types"));
		$this->config["icons_folder"] = $sett["icons_folder"];
		$this->config["icons_default"] = $sett["icons_default"];
		$this->config["photos_folder"] = $sett["photos_folder"];
		$this->config["use_kiss_types"] = $sett["use_kiss_types"];
		$this->config["use_friend_types"] = $sett["use_friend_types"];
		$this->user = $user;
		
		$this->rated_ids = $this->_getRatedIdsByThisUser();
		$this->rated_ids_str = implode(",", $this->rated_ids);
		if ($this->rated_ids_str == '') $this->rated_ids_str = 0;
	}
	
	function getPrevewPhotos($from=1, $from_id=false, $count=false){
		
		$from = intval($from);
		$from_id = intval($from_id);
		$count = intval($count);
		if ($from_id) $where_add = " AND id<='".$from_id."' ";
		if (!$count) $count = $this->preview_limit;
		
		$strSQL = "	SELECT couple FROM ".USERS_TABLE." WHERE id='".$this->user[0]."' ";
		$is_coupled = $this->dbconn->GetOne($strSQL);
			
		$strSQL = "SELECT id, icon_path
					FROM ".USERS_TABLE."
					WHERE id!='".$this->user[0]."' AND id not in (".$this->rated_ids_str.") AND icon_path!='' AND big_icon_path!='' AND status='1' AND confirm='1' AND visible='1' ".$where_add." ORDER BY id DESC  LIMIT ".$from.", ".$count." ";
		$i=0;
		$rs = $this->dbconn->Execute($strSQL);
		while (!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$upload_url = $this->config["server"].$this->config["site_root"].$this->config["icons_folder"]."/".$row["icon_path"];
			$upload_path = $this->config["site_path"].$this->config["icons_folder"]."/".$row["icon_path"];
			if (file_exists($upload_path)){
				$uploads[$i] = $row;
				$uploads[$i]["upload_url"] = $upload_url;
				$uploads[$i]["array_index"] = $i;
				$i++;
			}
			$rs->MoveNext();
		}
		return $uploads;
	}
	
	function getUserInfo($id=0){
		
		$strSQL = "	SELECT couple FROM ".USERS_TABLE." WHERE id='".$this->user[0]."' ";
		$is_coupled = $this->dbconn->GetOne($strSQL);
		
		$id = intval($id);
		$where_str = '';
		$limit_str = '';
		if ($id < 1) $limit_str = " ORDER BY id DESC LIMIT 0,1 ";
		else $where_str = " id='".$id."' AND ";
		
		$strSQL = "SELECT id as id_user, login, icon_path, big_icon_path, comment, date_birthday
					FROM ".USERS_TABLE." 
					WHERE ".$where_str." id!='".$this->user[0]."' AND id not in (".$this->rated_ids_str.") AND icon_path!='' AND big_icon_path!='' AND status='1' AND confirm='1' AND visible='1' ".$limit_str;
		$rs = $this->dbconn->Execute($strSQL);
		if (!$rs->RowCount()) return false;
		$row = $rs->GetRowAssoc(false);
		$row["icon_path"] = str_replace("thumb_","",$row["icon_path"]);
		$upload_url = $this->config["server"].$this->config["site_root"].$this->config["icons_folder"]."/".$row["icon_path"];
		$upload_path = $this->config["site_path"].$this->config["icons_folder"]."/".$row["icon_path"];
		if (file_exists($upload_path)){
			$user_info = $row;
			$user_info["years"] = $this->_ageFromBDate($row["date_birthday"]);
			$user_info["upload_url"] = $upload_url;
			$user_info["small_icon_url"] = $this->config["server"].$this->config["site_root"].$this->config["icons_folder"]."/thumb_".$row["icon_path"];
			$upl_sett = getimagesize($upload_path);
			$user_info["width"] = $upl_sett[0];
			
			$user_info["view_link"] = "./viewprofile.php?id=".$row["id_user"];
			$user_info["email_link"] = "./mailbox.php?sel=fs&id=".$row["id_user"];
			$user_info["kiss_link"] = $this->config["use_kiss_types"] ? "./send_kiss.php?sel=send&id_user=".$row["id_user"] : $this->file_name."?sel=kiss&id=".$row["id_user"];
			$user_info["gift_link"] = "./giftshop.php?sel=users_add&id_user=".$row["id_user"];
			
			//// get add_friend link
			$sub_strSQL = "SELECT id FROM ".HOTLIST_TABLE." WHERE id_friend='".$row["id_user"]."' and id_user='".$this->user[0]."'";
			$user_info["hotlisted"] = intval($this->dbconn->GetOne($sub_strSQL))?1:0;
			//// get blacklist_link link
			$sub_strSQL = "SELECT id FROM ".BLACKLIST_TABLE." WHERE id_enemy='".$row["id_user"]."' and id_user='".$this->user[0]."'";
			$user_info["blacklisted"] = intval($this->dbconn->GetOne($sub_strSQL))?1:0;
			
			if(!$user_info["hotlisted"] && !$user_info["blacklisted"]) {
				$user_info["addfriend_link"] = $this->config["use_friend_types"] ? "./hotlist.php?sel=addform&id=".$row["id_user"] : "./".$this->file_name."?sel=addlist&id=".$row["id_user"];
				$user_info["blacklist_link"] = "./".$this->file_name."?sel=blacklist&id=".$row["id_user"];
			}
			
			$user_info["sendfriend_link"] = "./send_friend.php?sel=send&id_user=".$row["id_user"];
			
			if (!$is_coupled) {
				$user_info["be_couple_link"] = "./".$this->file_name."?sel=be_couple&id=".$row["id_user"];
			}
			if ($this->config["voipcall_feature"]){
				$user_info["call_link"] = "./voip_call.php?sel=rate&id_user=".$row["id_user"]; 
			}
			$user_info["hotlist"] = $this->_getUserHotList($row["id_user"]);
			$user_info["photos"] = $this->_getUserPhotos($row["id_user"]);
			$user_info["photos_count"] = count($user_info["photos"]);
		}
		return $user_info;
	}
	
	function getRatedUserInfo($id){
		$id = intval($id);
		if ($id<1) return false;
		
		$strSQL = "SELECT b.id, b.big_icon_path, b.login, b.date_birthday, a.estimation FROM ".USER_RATING_TABLE." a
					LEFT JOIN ".USERS_TABLE." b on a.id_user=b.id
					WHERE a.id_user=b.id AND a.id_user='".$id."' AND a.id_voter='".$this->user[0]."'";
		$rs = $this->dbconn->Execute($strSQL);
		$rate_info = $rs->GetRowAssoc(false);
		$rate_info["view_link"] = "viewprofile.php?id=".$rate_info["id"];
		$rate_info["icon_url"] = $this->config["server"].$this->config["site_root"].$this->config["icons_folder"]."/".$rate_info["big_icon_path"];
		$rate_info["years"] = $this->_ageFromBDate($rate_info["date_birthday"]);
		
		$strSQL = "SELECT count(id_user) as votes_count, AVG(estimation) as avg_estim FROM ".USER_RATING_TABLE." WHERE id_user='".$id."'";
		$rs = $this->dbconn->Execute($strSQL);
		$rate_info["votes_count"] = $rs->fields[0];
		$rate_info["avg_estim"] = round($rs->fields[1],1);
		
		return $rate_info;
	}
	
	function getTops(){
		$strSQL = "SELECT utt.id_user, utt.rating, ut.login, ut.icon_path
					FROM ".USER_TOPTEN_TABLE." utt
					LEFT JOIN ".USERS_TABLE." ut ON ut.id=utt.id_user
					WHERE ut.icon_path!='' AND status='1' AND visible='1'
					ORDER BY RAND() DESC
					LIMIT 0,".$this->top_amount_limit;
		$rs = $this->dbconn->Execute($strSQL);
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$row['icon_path'] = $this->config["server"].$this->config["site_root"].$this->config["icons_folder"].'/'.$row['icon_path'];
			$row['view_link'] = $this->config["server"].$this->config["site_root"]."/viewprofile.php?id=".$row["id_user"];
			$top[] = $row;
			$rs->MoveNext();
		}
		return $top;
	}
	
	function getUserStats($id){
		$id = intval($id);
		if (!$id) return false;
		
		$strSQL = "SELECT rating FROM ".USER_TOPTEN_TABLE." WHERE id_user='".$id."'";
		$user_stats['rating'] = $this->dbconn->GetOne($strSQL);
		
		if (!$user_stats['rating']) return false;
		
		$strSQL = "SELECT COUNT(estimation) as count_estimation , estimation FROM ".USER_RATING_TABLE." 
					WHERE id_user='".$id."'
					GROUP BY estimation";
		$rs = $this->dbconn->Execute($strSQL);
		$stats = array_fill(1,10,0);
		
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$stats[$row['estimation']] = $row['count_estimation'];
			$rs->MoveNext();
		}
		
		$max_stat_value = max($stats);
		$adv_stats = array();
		foreach ($stats as $key=>$value){
			$adv_stats[$key]['value'] = $value;
			if ($adv_stats[$key]['value'] > 0)
				$adv_stats[$key]['height_ratio'] = $adv_stats[$key]['value']/$max_stat_value;
			else
				$adv_stats[$key]['height_ratio'] = 0;
		}
		$user_stats['stats'] = $adv_stats;
		
		$strSQL  = "SELECT COUNT(id_voter) FROM ".USER_RATING_TABLE." WHERE id_user='".$id."'";		
		$user_stats['voters_count'] = $this->dbconn->GetOne($strSQL);
		
		$strSQL = "SELECT big_icon_path FROM ".USERS_TABLE." WHERE id='".$id."'";
		$big_icon_path = $this->dbconn->GetOne($strSQL);
		$this->config['site_path'].$this->config["icons_folder"]."/".$big_icon_path;
		if (!$big_icon_path || !file_exists($this->config['site_path'].$this->config["icons_folder"]."/".$big_icon_path))
			$big_icon_path = $this->config['icons_default'];
		$user_stats['big_icon_url'] = $this->config['server'].$this->config['site_root'].$this->config["icons_folder"].'/'.$big_icon_path;
		
		return $user_stats;
	}

	function _getRatedIdsByThisUser(){
		$strSQL = "SELECT id_user FROM ".USER_RATING_TABLE." WHERE id_voter='".$this->user[0]."'";
		$rs = $this->dbconn->Execute($strSQL);
		while (!$rs->EOF){
			$ids[] = $rs->fields[0];
			$rs->MoveNext();
		}
		return $ids;
	}
	
	function _getUserHotList($id){
		$id = intval($id);
		if ($id<1) return false;
		
		$strSQL = "SELECT a.id_friend, b.icon_path, b.login FROM ".HOTLIST_TABLE." a
					LEFT JOIN ".USERS_TABLE." b ON a.id_friend = b.id
					WHERE a.id_user = ".$id." AND b.icon_path != '' ORDER BY a.id DESC LIMIT 0,".$this->hot_list_limit;
		$rs = $this->dbconn->Execute($strSQL);
		$i=0;
		while (!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$icon_url = $this->config["server"].$this->config["site_root"].$this->config["icons_folder"]."/".$row["icon_path"];
			$icon_path = $this->config["site_path"].$this->config["icons_folder"]."/".$row["icon_path"];
			if (file_exists($icon_path)){
				$hotlist[$i] = $row;
				$hotlist[$i]["icon_url"] = $icon_url;
				$hotlist[$i]["view_link"] = "viewprofile.php?id=".$hotlist[$i]["id_friend"];
				$i++;
			}
			$rs->MoveNext();
		}
		return $hotlist;
	}
	
	function _getUserPhotos($id){
		$id = intval($id);
		if ($id<1) return false;
		
		$strSQL = "SELECT id, upload_path 
					FROM ".USER_UPLOAD_TABLE." 
					WHERE id_user='".$id."' AND upload_type='f' AND allow='1' AND status='1'";
		$rs = $this->dbconn->Execute($strSQL);
		$i=0;
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$photo_url = $this->config["server"].$this->config["site_root"].$this->config["photos_folder"]."/".$row["upload_path"];
			$photo_path = $this->config["site_path"].$this->config["photos_folder"]."/".$row["upload_path"];
			if (file_exists($photo_path)){
				$uploads[$i] = $row;
				$uploads[$i]["photo"] = $photo_url;
				$uploads[$i]["small_photo"] = $this->config["server"].$this->config["site_root"].$this->config["photos_folder"]."/thumb_".$row["upload_path"];
				$uploads[$i]["array_index"] = $i;
				$i++;
			}
			$rs->MoveNext();
		}
		return $uploads;
	}
	
	function _getSiteSettings($set_arr=""){
	// array
		if($set_arr != ""  &&  is_array($set_arr) && count($set_arr)>0 ){
			foreach($set_arr as $key => $set_name){
				$set_arr[$key] = "'".$set_name."'";
			}
			$sett_string = implode(", ", $set_arr);
			$str_sql = "Select value, name from ".SETTINGS_TABLE." where name in (".$sett_string.")";
			$rs = $this->dbconn->Execute($str_sql);
			while(!$rs->EOF){
				$row = $rs->GetRowAssoc(false);
				$settings[$row["name"]] = $row["value"];
				$rs->MoveNext();
			}
		}elseif(strlen($set_arr)>0){
			$str_sql = "Select value, name from ".SETTINGS_TABLE." where name = '".strval($set_arr)."'";
			$rs = $this->dbconn->Execute($str_sql);
			$row = $rs->GetRowAssoc(false);
			$settings = $row["value"];
		}elseif(strval($set_arr)==""){
			$str_sql = "Select value, name from ".SETTINGS_TABLE." order by id";
			$rs = $this->dbconn->Execute($str_sql);
			while(!$rs->EOF){
				$row = $rs->GetRowAssoc(false);
				$settings[$row["name"]] = $row["value"];
				$rs->MoveNext();
			}
		}
		return $settings;
	}

	function _ageFromBDate($date){
		///// date in Y-m-d h:i:s format
		$year = intval(substr($date,0,4));
		$month = intval(substr($date,5,2));
		$day = intval(substr($date,8,2));
		$n_year = date("Y");
		$n_month = date("m");
		$n_day = date("d");
		if ($month==$n_month) {
			if ($day>$n_day) {
				$new_age = floor(($n_year - $year)+($n_month - $month-1)/12);
			} else {
				$new_age = floor(($n_year - $year) + ($n_month - $month)/12);
			}
		} else {
			$new_age = floor(($n_year - $year) + ($n_month - $month)/12);
		}
		return $new_age;
	}
}

?>