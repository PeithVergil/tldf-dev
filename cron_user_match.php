<?php
	/**
	* User perfect matches listing
	**/
	
	include './include/config.php';
	include './common.php';
	include './include/functions_index.php';
	include './include/functions_users.php';
	include './include/class.phpmailer.php';
	include './include/functions_mail.php';
	include './include/class.lang.php';
	include './include/class.percent.php';
	
	include $config['path_lang']."/mail/".$lang_file;
	
	$debug = false;
	if($_REQUEST['debug']) { $debug = $_REQUEST['debug']; }
	
	$user_limit = 4;
	
	$strUsers="";
	$strMatches="";
	$all_users = FetchUsersList($user_limit);
	//echo (count($all_users));
	
	$user = array();
	foreach($all_users as $usr)
	{
		//echo $usr["id"];
		$user["id"]    = $usr["id"] ? $usr["id"] : "";
		$user["login"] = $usr["login"] ? $usr["login"] : "";
		$user["fname"] = $usr["fname"] ? $usr["fname"] : "";
		$user["sname"] = $usr["sname"] ? $usr["sname"] : "";
		$user["email"] = $usr["email"] ? $usr["email"] : "";
		$user["gender"] = $usr["gender"] ? $usr["gender"] : "";
		
		$icon_path = $usr["icon_path"] ? $usr["icon_path"] : $default_photos[$row["gender"]];
		$user["icon_path"] = $config["server"].$config["site_root"]."/uploades/icons/".$icon_path;
		
		CronUserMatchTable();
		
		if($debug)
		{
			$strUsers .= "<tr><td align='center'>".$user['id']."</td><td valign='top'>".$user['fname']."<br>";
			$strUsers .= "<img src='".$user['icon_path']."' ></td>";
			$strUsers .= "<td>".$strMatches."</td></tr>";
		}
	}
	
	function FetchUsersList($limit=0)
	{
		global $dbconn;
		
		$str_limit = "";
		
		if ($limit > 0) {
			$str_limit = " ORDER BY RAND() LIMIT ".$limit;
		}
		
		/* Fetching All users */
		$strSQL = " SELECT DISTINCT u.id, u.login, u.fname, u.sname, u.icon_path, u.status, u.gender, u.email, u.mm_platinum_applied,
					g.id_group, n.name AS group_name, p.is_rem_block,
					UNIX_TIMESTAMP('".date('Y-m-d H:i:s')."')-UNIX_TIMESTAMP(u.date_registration) AS reg_duration,
					UNIX_TIMESTAMP(b.date_end) - UNIX_TIMESTAMP(b.date_begin) AS all_res,
					UNIX_TIMESTAMP(b.date_end) - UNIX_TIMESTAMP('".date("Y-m-d H:i:s")."') AS now_res
					FROM ".USERS_TABLE." AS u
					LEFT JOIN ".USER_GROUP_TABLE." AS g ON g.id_user = u.id
					LEFT JOIN ".GROUPS_TABLE." AS n ON n.id = g.id_group
					LEFT JOIN ".BILLING_USER_PERIOD_TABLE." AS b ON b.id_user = u.id
					LEFT JOIN ".USER_PRIVACY_SETTINGS." AS p ON p.id_user = u.id
					WHERE u.root_user='0' AND u.guest_user='0' AND u.status='1'".$str_limit;
		
		$rs = $dbconn->Execute($strSQL);
		$i = 0;
		$all_users = array();
		
		while(!$rs->EOF)
		{
			$row = $rs->GetRowAssoc(false);
			$all_users[$i]["id"] = intval($row["id"]);
			$all_users[$i]["login"] = $row["login"];
			$all_users[$i]["fname"] = $row["fname"];
			$all_users[$i]["sname"] = $row["sname"];
			$all_users[$i]["icon_path"] = $row["icon_path"];
			$all_users[$i]["status"] = $row["status"];
			$all_users[$i]["gender"] = intval($row["gender"]);
			$all_users[$i]["email"] = $row["email"];
			$all_users[$i]["is_applied"] = $row["mm_platinum_applied"] ? 1 : 0;
			$all_users[$i]["id_group"] = intval($row["id_group"]);
			$all_users[$i]["group_name"] = $row["group_name"];
			$all_users[$i]["is_rem_block"] = $row["is_rem_block"] ? $row["is_rem_block"] : "";
			$all_users[$i]["reg_duration"] = $row["reg_duration"] ? intval($row["reg_duration"]/3600) : "";
			$all_users[$i]["all_res"] = $row["all_res"] ? intval($row["all_res"]/3600) : "";
			$all_users[$i]["now_res"] = $row["now_res"] ? intval($row["now_res"]/3600) : "";
			
			$rs->MoveNext();
			$i++;
		}
		return $all_users;
	}
	
	function CronUserMatchTable($err="", $par="")
	{
		global $lang, $config, $config_index, $smarty, $dbconn, $user, $lang_mail, $debug, $strUsers, $strMatches;
		
		//For displaying template file.
		$smarty->assign('template_root', $config['index_theme_path']);
		
		$page = 1;
		$record_limit = 5;
		
		////////// settings
		$settings = GetSiteSettings(array('icon_male_default','icon_female_default','icons_folder','show_users_connection_str','show_users_comments','show_users_group_str','use_kiss_types','thumb_max_width', 'use_friend_types'));
		$smarty->assign("icon_width", $settings["thumb_max_width"]);
		$default_photos['1'] = $settings['icon_male_default'];
		$default_photos['2'] = $settings['icon_female_default'];
	
		$profile_percent = new Percent($user['id']);
		
		//////// if isnt set post vars echo err
		///// if user's perfect match is empty
		$descr_perc = $profile_percent->GetSectionPercent(6);
		$interests_perc = $profile_percent->GetSectionPercent(7);
	
		if ($use_session == 0)
		{
			unset($_SESSION["id_arr"], $_SESSION["with_arr"], $_SESSION["without_arr"], $_SESSION["online_arr"], $_SESSION["offline_arr"]);	// unset session data
			$match_arr = GetPerfectUsersList($user['id'], '', $record_limit);

			$in_str = (count($match_arr["id_arr"])>0)?implode(", ", $match_arr["id_arr"]):"0";

			$strSQL =
				'SELECT DISTINCT a.id, a.icon_path, e.id_user AS session
				   FROM '.USERS_TABLE.' a
			  LEFT JOIN '.ACTIVE_SESSIONS_TABLE.' e ON a.id = e.id_user
				  WHERE a.id IN ('.$in_str.')';
			$rs = $dbconn->Execute($strSQL);
			$i = 0;
			$perfect_match_type = 0;
			while(!$rs->EOF)
			{
				$row = $rs->GetRowAssoc(false);
				if($par != 'full' && intval($match_arr["percent_arr"][$row["id"]]) >= 98)
				{	////// profiles ~ match for 100%
					$perfect_match_type = 1;
					$_SESSION["id_arr"][$i] = $row["id"];
					$_SESSION["percent_arr"][$row["id"]] = "100";
					if(strlen($row["icon_path"]))
					{
						$_SESSION["with_arr"][$i] = $row["id"];
					}
					else
					{
						$_SESSION["without_arr"][$i] = $row["id"];
					}
					if(intval($row["session"]))
					{
						$_SESSION["online_arr"][$i] = $row["id"];
					}
					else
					{
						$_SESSION["offline_arr"][$i] = $row["id"];
					}
					$i++;
					$err = $lang["matches"]["only_full_match"];
				}
				elseif(intval($match_arr["percent_arr"][$row["id"]]) >= 50)
				{
					if($perfect_match_type == 1)	break;		//// we already have list of 100%-matches
					$perfect_match_type = 2;
					$_SESSION["id_arr"][$i] = $row["id"];
					$_SESSION["percent_arr"][$row["id"]] = $match_arr["percent_arr"][$row["id"]];
					if(strlen($row["icon_path"]))
					{
						$_SESSION["with_arr"][$i] = $row["id"];
					}
					else
					{
						$_SESSION["without_arr"][$i] = $row["id"];
					}
					if(intval($row["session"]))
					{
						$_SESSION["online_arr"][$i] = $row["id"];
					}
					else
					{
						$_SESSION["offline_arr"][$i] = $row["id"];
					}
					$i++;
				}
				else
				{
					break;				///// becouse sort by coll_spr desc
				}
				$rs->MoveNext();
			}
		}
		// problem: $id_arr is not set
		elseif($use_session != 0 && count($id_arr) > 0)
		{
			$perfect_match_type = 1;
		}
		if($perfect_match_type == 2)
		{
			if($par != 'full')
			{
				if(!$err)$err = $lang["err"]["no_matches_in_perfect"];
			}
		}
		elseif($perfect_match_type == 0)
		{
			$smarty->assign("empty", "1");
		}
		
		///// if count of matches ==0 then trying to find most close result
		$id_arr = isset($_SESSION["id_arr"]) ? $_SESSION["id_arr"] : array();
		
		if(is_array($_SESSION["percent_arr"]) && count($_SESSION["percent_arr"])>0)
		{
			arsort($_SESSION["percent_arr"]);
			$temp_id_arr = array();
			foreach($_SESSION["percent_arr"] as $temp_id => $temp_percent)
			{
				if(is_array($id_arr) && in_array($temp_id, $id_arr) && !in_array($temp_id, $temp_id_arr))
				{
					$temp_id_arr[] = $temp_id;
				}
			}
			unset($id_arr);
			$_SESSION["id_arr"] = $id_arr = $temp_id_arr;
		}

		$num_records = count($id_arr);

		if ($num_records > 0)
		{
			if ($id_arr)
			{
				$i = 0;
				$search = array();
				$_LANG_NEED_ID = array();
				
				foreach ($id_arr as $v)
				{
					$strSQL = "Select distinct a.id, a.login, a.phone, SUBSTRING(a.comment,1, 165) as comment, a.gender, a.date_birthday, a.id_country, a.id_city, a.id_region, DATE_FORMAT(a.date_last_seen,'".$config["date_format"]."')  as date_last_login, a.icon_path from ".USERS_TABLE." a where a.id = '".$v."' ";
					$rs = $dbconn->Execute($strSQL);
					$row = $rs->GetRowAssoc(false);
					$search[$i]["id"] = $row["id"];
					$search[$i]["number"] = ($page-1)*$config_index["search_numpage"]+($i+1);
					$search[$i]["name"] = $row["login"];
					$search[$i]["phone"] = $row["phone"];
					$search[$i]["age"] = AgeFromBDate($row["date_birthday"]);
					$search[$i]["id_country"] = intval($row["id_country"]);
					$search[$i]["id_region"] = intval($row["id_region"]);
					$search[$i]["id_city"] = intval($row["id_city"]);
					
					$_LANG_NEED_ID["country"][] = intval($row["id_country"]);
					$_LANG_NEED_ID["region"][] = intval($row["id_region"]);
					$_LANG_NEED_ID["city"][] = intval($row["id_city"]);

					$search[$i]["profile_link"] = "viewprofile.php?id=".$row["id"];
					$icon_path = $row["icon_path"]?$row["icon_path"]:$default_photos[$row["gender"]];
					if($icon_path && file_exists($config["site_path"].$settings["icons_folder"]."/".$icon_path))
						$search[$i]["icon_path"] = $config["site_root"].$settings["icons_folder"]."/".$icon_path;
					$img_icon = $row["icon_path"]?1:0;

					$strSQL = "select count(distinct f.upload_path) as photo_count  from ".USER_UPLOAD_TABLE." f where f.id_user = ".$row["id"]." and f.upload_type='f' and f.status='1' and f.allow in ('1', '2')";
					$rs_photo = $dbconn->Execute($strSQL);
					$search[$i]["photo_count"] = $rs_photo->fields[0] + $img_icon;
					
					//RS: if can most probably be removed, because $view is not set
					if ($view != "gallery")
					{
						$search[$i]["annonce"] = stripslashes($row["comment"]);
						$search[$i]["completion"] = $profile_percent->GetAllPercentForUser($row["id"]);
						$search[$i]["last_login"] = $row["date_last_login"];

						/// get groups
						$sub_strSQL = "Select a.name from ".USER_GROUP_TABLE." b left join ".GROUPS_TABLE." a on a.id=b.id_group where b.id_user='".$row["id"]."'";
						$sub_rs = $dbconn->Execute($sub_strSQL);
						$groups_arr = array();
						while(!$sub_rs->EOF)
						{
							array_push($groups_arr, $sub_rs->fields[0]);
							$sub_rs->MoveNext();
						}
						if(is_array($groups_arr) && count($groups_arr)>0)
							$search[$i]["group"] = implode(",", $groups_arr);

						/// get user search params
						$sub_strSQL = "SELECT gender as gender_search, age_max, age_min FROM ".USER_MATCH_TABLE." WHERE id_user='".$row["id"]."' ";
						$sub_rs = $dbconn->Execute($sub_strSQL);
						$sub_row = $sub_rs->GetRowAssoc(false);
						$search[$i]["age_max"] = $sub_row["age_max"];
						$search[$i]["age_min"] = $sub_row["age_min"];
						$search[$i]["gender_search"] = $lang["gender_search"][$sub_row["gender_search"]];
						$search[$i]["percent"] = intval($_SESSION["percent_arr"][$row["id"]]);
					}
					$i++;
				}
				//$smarty->assign("search_res", $search);
			}
			else
			{
				$smarty->assign("empty", "1");
			}
		}
		else
		{
			$smarty->assign("empty", "1");
		}
		
		$form["show_users_comments"] = $settings["show_users_comments"];
		$form["show_users_group_str"] = $settings["show_users_group_str"];
		
		if (isset($_LANG_NEED_ID) && count($_LANG_NEED_ID))
		{
			$smarty->assign("base_lang", GetBaseLang($_LANG_NEED_ID));
		}
		
		//$smarty->assign("form", $form);
		//$smarty->assign("user", $user);
		
		$server['url']		= $config['server'];
		$server['root']		= $config['server'].$config['site_root'];
		$server['img_root']	= $config['server'].$config['site_root'].$config['index_theme_path']."/images";
		$data['server']		= $server;
		
		$data['urls']	= GetUserEmailLinks();
		
		$data['user']		= $user;
		$data['form']		= $form;
		$data['search_res']	= $search;
		$smarty->assign("data", $data);
		
		if ($debug)
		{
			$strMatches = "<table cellpadding='0' cellspacing='0' border='1'>";
			$strMatches .= "<tr>";
			
			foreach ($search as $mat) {
				$strMatches .= "<td align='center' width='120'>".$mat['id']."<br>".$mat['name']."<br>";
				$strMatches .= "<img src='".$mat['icon_path']."' ></td>";
			}
			$strMatches .= "</tr></table>";
			
			if ($debug == 2) {
				$smarty->display(TrimSlash($config["index_theme_path"])."/cron_user_match.tpl");
				exit;
			}
		}
		else
		{
			//Send Mail to user
			$rs = $dbconn->Execute('SELECT lang_file, code, charset FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($config['default_lang']));
			include $config['path_lang'].'mail/'.$rs->fields[0];
			$lang_mail_code = $rs->fields[1];	// global
			$charset_mail = $rs->fields[2];		// global
			$rs->free();
			
			// Auto Response mail to User
			$subject = ($user['gender'] == GENDER_MALE) ? $lang_mail['cron_user_match_e']['subject'] : $lang_mail['cron_user_match_t']['subject'];
			
			//send mail
			//currently disabled
			//$mail_err = SendMail($user['email'], $config['site_email'], $subject, $data, 'mail_cron_user_match', null,
			//	$user['fname'], '', 'cron_user_match', $user['gender']);
			
			// For Testing
			/*
			if ($email_to == 'vimal_designing@yahoo.com')
			{
				$mail_err = SendMail($user['email'], $config['site_email'], $subject, $data, 'mail_cron_user_match', null,
					$user['fname'], '', 'cron_user_match', $user['gender']);
				echo "Mail sent to: ".$email_to."<br>";
			}
			*/
		}
	}
	
	if ($debug) {
		$testStrS	= "<table cellpadding='3' cellspacing='0' border='1'>";
		$testStrHdr	= "<tr><td>UserId</td><td>User</td><td>Matches</td></tr>";
		$testStrE	= "</table>";
		
		$testStrComp = $testStrS.$testStrHdr.$strUsers.$testStrE;
		echo $testStrComp;
	}
?>