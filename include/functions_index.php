<?php

/**
 * Functions for users area, filter functions
 *
 * @package DatingPro
 * @subpackage Include files
 **/

include_once 'functions_common.php';

/**
 * 1) reset user to default group when his group does not exist
 * 2) reset user to default group when no valid period record exists
 * 3) check expiration and resets group when expired
 * 4) reset $user[ AUTH_ID_GROUP ] when necessary
 * 5) reset $_SESSION['permissions'] when necessary
 * 6) update newsletter mailing list
 **/

//--------------------
// RefreshAccount
//--------------------

function RefreshAccount()
{
	global $dbconn, $config, $user;
	
	// check valid member
	if (empty($user) || $user == 'err' || !$user[ AUTH_ID_USER ]) {
		return;
	}
	
	// $_SESSION['permissions'] has already been set during authentication
	// we refresh only when we need to reset the group
	
	if ($user[ AUTH_ROOT ]) {
		//$user[ AUTH_ID_GROUP ] = MM_ADMIN_GROUP_ID;
		return;
	}
	if ($user[ AUTH_GUEST ]) {
		return;
	}
	
	$id_user = $user[ AUTH_ID_USER ];
	$id_group = $user[ AUTH_ID_GROUP ];
	
	// get group type of current user
	$rs = $dbconn->Execute('SELECT type FROM '.GROUPS_TABLE.' WHERE id = ?', array($id_group));
	
	if ($rs->EOF) {
		// move user to default group
		$strSQL = 'SELECT id, name FROM '.GROUPS_TABLE.' WHERE type = "d"';
		if ($config['use_gender_membership']) {
			$strSQL .= ' AND gender = "'.$user[ AUTH_GENDER ].'"';
		}
		$rs = $dbconn->Execute($strSQL);
		$new_id_group = $rs->fields[0];
		$new_group_name = $rs->fields[1];
		$rs->Free();
		
		$dbconn->Execute('INSERT INTO '.USER_GROUP_TABLE.' SET id_user = ?, id_group = ?', array($id_user, $new_id_group));
		$user[ AUTH_ID_GROUP ] = $new_id_group;
		$_SESSION['permissions'] = meetme_GetPermissions($user); // $user call by reference
		AssignUserToSmarty($user);
		
		// SOLVE360
		if (SOLVE360_CONNECTION) {
			require_once $config['site_path'].'/include/Solve360Service.php';
			$solve360Service = new Solve360Service(SOLVE360_USER, SOLVE360_TOKEN);
			
			$solve360 = array();
			require $config['site_path'].'/include/Solve360CustomFields.php';
			
			$contactData = array(
				$solve360['Current Group'] => $new_group_name,
			);
			
			if ($user[ AUTH_ID_SOLVE360 ]) {
				$contact = $solve360Service->editContact($user[ AUTH_ID_SOLVE360 ], $contactData);
				#var_dump($contact); exit;
				if (isset($contact->errors)) {
					$subject = 'Error while updating Current Group after invalid group check';
					solve360_api_error($contact, $subject, $user[ AUTH_LOGIN ]);
				}
			}
			// maybe add contact if not found
		}
		
		return;
	}
	
	$group_type = $rs->fields[0];
	$rs->Free();
	
	// d=default/signup, f=normal/pay, b=hold, t=trial, r=admin, g=guest, m=moderator
	
	if ($group_type == 'f') {
		// check valid period definition
		// we only need to do this once after the login
		if ($user[ AUTH_TYPE ] == 0) {
			//RS 2012-11-23: check for cost <> 0 removed, as Regular Guys do not have a cost
			$rs = $dbconn->Execute('SELECT id FROM '.GROUP_PERIOD_TABLE.' WHERE status <> "0" AND id_group = ?', array($id_group));
			if ($rs->EOF) {
				// move user to default group
				$strSQL = 'SELECT id, name FROM '.GROUPS_TABLE.' WHERE type = "d"';
				if ($config['use_gender_membership']) {
					$strSQL .= ' AND gender = "'.$user[ AUTH_GENDER ].'"';
				}
				$rs = $dbconn->Execute($strSQL);
				$new_id_group = $rs->fields[0];
				$new_group_name = $rs->fields[1];
				$rs->Free();
				
				$dbconn->Execute('UPDATE '.USER_GROUP_TABLE.' SET id_group = ? WHERE id_user = ?', array($new_id_group, $id_user));
				$user[ AUTH_ID_GROUP ] = $new_id_group;
				$_SESSION['permissions'] = meetme_GetPermissions($user); // $user call by reference
				AssignUserToSmarty($user);
				
				// SOLVE360
				if (SOLVE360_CONNECTION) {
					require_once $config['site_path'].'/include/Solve360Service.php';
					$solve360Service = new Solve360Service(SOLVE360_USER, SOLVE360_TOKEN);
					
					$solve360 = array();
					require $config['site_path'].'/include/Solve360CustomFields.php';
					
					$contactData = array(
						$solve360['Current Group'] => $new_group_name,
					);
					
					if ($user[ AUTH_ID_SOLVE360 ]) {
						$contact = $solve360Service->editContact($user[ AUTH_ID_SOLVE360 ], $contactData);
						if (isset($contact->errors)) {
							$subject = 'Error while updating Current Group after invalid period check';
							solve360_api_error($contact, $subject, $user[ AUTH_LOGIN ]);
						}
					}
					// maybe add contact
				}
				
				return;
			}
		}
	}
	
	if ($group_type == 'f' || $group_type == 't') {
		// check expiration
		$days_remain = GetRemainDaysInAccount($id_user);
		if ($days_remain == NULL || $days_remain < 0) {
			$new_id_group = ResetUserGroup($id_user, $id_group);
			if ($new_id_group) {
				$user[ AUTH_ID_GROUP ] = $new_id_group;
				$_SESSION['permissions'] = meetme_GetPermissions($user); // $user call by reference
				AssignUserToSmarty($user);
			}
		}
	}
	
	// RS: this is just a stub. Let's run it only after the login for better performance.
	if ($user[ AUTH_TYPE ] == 0) {
		require_once dirname(__FILE__).'/functions_newsletter.php';
		UpdateUserDatingMailingList($id_user);
	}
	
	return;
}

//--------------------
// IndexHomePage
//--------------------

function IndexHomePage($header = '', $get_logo_setup = false)
{
	global $lang, $config, $smarty, $dbconn, $user, $bottom_menu_info;
	
	// additional footer links for info content
	$rs = $dbconn->Execute(
		'SELECT a.id, b.name as name_lang, a.name 
		   FROM '.INFO_CONTENT_TABLE.' a 
	  LEFT JOIN '.INFO_LANG_CONTENT_TABLE.' b ON a.id = b.id_info AND b.id_lang = '.$config['default_lang'].' AND b.table_key = 1 
		  WHERE a.id <> 1 AND a.status = "1"
	   ORDER BY a.sorter');
	
	$bottom_menu_info = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$bottom_menu_info[$row['id']]['link'] = '/info.php?sel='.$row['id'];
		$bottom_menu_info[$row['id']]['name'] = $row['name_lang'] ? stripslashes($row['name_lang']) : stripslashes($row['name']);
		$rs->MoveNext();
	}
	
	$smarty->assign('bottom_menu_info', $bottom_menu_info);
	
	// language items
	$smarty->assign('lang', $lang);
	$smarty->assign('header', $header);
	$smarty->assign('button', $lang['button']);
	
	// logo setup
	// logo currently only used only in viewprofile_print.tpl, which is not in use,
	// and if we should ever use it then it will probably be a custom design
	
	if ($get_logo_setup) {
		$logo_setup = GetSiteSettings(array(
			'site_top_logotype', 'site_logotype_format', 'site_banner', 'site_banner_format',
			'site_logotype_width', 'site_logotype_height', 'site_banner_width', 'site_banner_height',
			'site_banner_color', 'use_shoutbox_feature'));
		
		if (isset($logo_setup['site_banner_color']) && $logo_setup['site_banner_color'] != '' && strlen($logo_setup['site_banner_color']) == 6) {
			$logo_setup['site_banner_color'] = $logo_setup['site_banner_color'];
		} else {
			$logo_setup['site_banner_color'] = $config['color']['content'];
		}
		
		$smarty->assign('logo_setup', $logo_setup);
	}
	
	if (!$user[ AUTH_ID_USER ] || $user[ AUTH_GUEST ]) {
		$smarty->assign('registered', 0);
	} else {
		$smarty->assign('registered', 1);
	}
	
	// paths to theme files
	$smarty->assign('template_root', $config['index_theme_path']);
	
	// freeze status
	// @todo: redundancy with functions_auth !!!
	$id_group = $user[ AUTH_ID_GROUP ];
	
	$isFreeze = (
		$id_group == MM_INACT_REGULAR_GUY_ID ||
		$id_group == MM_INACT_REGULAR_LADY_ID ||
		$id_group == MM_INACT_PLATINUM_GUY_ID ||
		$id_group == MM_INACT_PLATINUM_LADY_ID ||
		$id_group == MM_INACT_ELITE_GUY_ID
	);
	
	$smarty->assign('isFreeze', $isFreeze);
	
	// how it works
	$smarty->assign('howWorks', GetHowItWorks());
	
	return;
}

//--------------------
// GetLinkArray
//--------------------

function GetLinkArray($num_records, $page, $param, $max_record, $dop_param='')
{
	// settings
	$dop_param['page_var_name'] = (isset($dop_param['page_var_name']) && strlen($dop_param['page_var_name'])) ? $dop_param['page_var_name'] : 'page';
	$dop_param['left_arrow_name'] = (isset($dop_param['left_arrow_name']) && strlen($dop_param['left_arrow_name'])) ? $dop_param['left_arrow_name'] : '...';
	$dop_param['right_arrow_name'] = (isset($dop_param['right_arrow_name']) && strlen($dop_param['right_arrow_name'])) ? $dop_param['right_arrow_name'] : '...';
	
	$num_page = ceil($num_records/$max_record);
	
	if ($num_page < 2) {
		return array();
	}
	
	$p_page_count = 10;
	$p_page = floor(($page - 1) / $p_page_count);
	
	$j = 0;
	$ret_links = array();
	
	if ($p_page > 0) {
		$ret_links[$j]['name'] = $dop_param['left_arrow_name'];
		$ret_links[$j]['link'] = $param.''.$param['page_var_name'].'='.($p_page * $p_page_count);
		$ret_links[$j]['selected'] = 0;
		$j++;
	}
	
	//	for($i=($p_page*$p_page_count+1);$i<$num_page;$i++){
	
	$top_limit = ((($p_page + 1) * $p_page_count + 1) <= $num_page) ? (($p_page + 1) * $p_page_count + 1) : $num_page + 1;
	
	for ($i = ($p_page*$p_page_count+1); $i < $top_limit; $i++) {
		$ret_links[$j]['name'] = $i;
		$ret_links[$j]['link'] = $param.''.$dop_param['page_var_name'].'='.$i;
		$ret_links[$j]['selected'] = ($i == $page) ? 1 : 0;
		$j++;
	}
	
	if ((($p_page+1)*$p_page_count) < $num_page) {
		$ret_links[$j]['name'] = $dop_param['right_arrow_name'];
		$ret_links[$j]['link'] = $param.''.$dop_param['page_var_name'].'='.(($p_page + 1) * $p_page_count + 1);
		$ret_links[$j]['selected'] = 0;
		$j++;
	}
	
	return $ret_links;
}

//---------------------------------------
//VP (Return Records Per Page Link Array)
//---------------------------------------

function GetRPPageLinkArray($numpage, $param)
{
	$rpp_links = array();
	
	$rpp_links[0]['name'] = '10';
	$rpp_links[1]['name'] = '20';
	$rpp_links[2]['name'] = '50';
	$rpp_links[3]['name'] = '100';
	
	$rpp_links[0]['link'] = $param.'&amp;pprec=10';
	$rpp_links[1]['link'] = $param.'&amp;pprec=20';
	$rpp_links[2]['link'] = $param.'&amp;pprec=50';
	$rpp_links[3]['link'] = $param.'&amp;pprec=100';
	
	$rpp_links[0]['selected'] = ($numpage == 10) ? 1 : 0;
	$rpp_links[1]['selected'] = ($numpage == 20) ? 1 : 0;
	$rpp_links[2]['selected'] = ($numpage == 50) ? 1 : 0;
	$rpp_links[3]['selected'] = ($numpage == 100) ? 1 : 0;
	
	return $rpp_links;
}

function Banners($file)
{
	global $smarty, $dbconn, $user;
	
	// Seaching for area id
	$rs = $dbconn->Execute('SELECT id FROM '.BANNERS_AREA_TABLE.' WHERE file_path = ?', array($file));
	
	if (($rs === false) || ($rs->EOF)) return;
	
	$row = $rs->GetRowAssoc(false);
	
	$current_area_id = $row['id'];
	
	if ($current_area_id <= 0) return ;
	
	// Get able banners
	$rs = $dbconn->Execute('SELECT banner_id FROM '.BANNERS_BELONGS_AREA_TABLE.' WHERE area_id = ?', array($current_area_id));
	
	if (($rs === false) || ($rs->EOF)) return;
	
	$banners_id_list = '';
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$banners_id_list .= $row['banner_id'];
		$banners_id_list .= ', ';
		$rs->MoveNext();
	}
	
	$banners_id_list = substr($banners_id_list, 0, strlen($banners_id_list)-2);
	
	// Get rotate banners settings
	$rotate_left_flag = 0;
	$rotate_bottom_flag = 0;
	$rotate_left_time = 2500;
	$rotate_bottom_time = 2500;
	
	$rs = $dbconn->Execute('SELECT position, rotate_flag, rotate_time FROM '.BANNERS_ROTATE_TABLE);
	
	while (!$rs->EOF)
	{
		$row = $rs->GetRowAssoc(false);
		
		if ($row['position'] == 0) {
			$rotate_left_flag = $row['rotate_flag'];
			$rotate_left_time = $row['rotate_time'];
		}
		
		if ($row['position'] == 1) {
			$rotate_bottom_flag = $row['rotate_flag'];
			$rotate_bottom_time = $row['rotate_time'];
		}
		
		$rs->MoveNext();
	}
	
	$banners_html= array();
	
	if ($rotate_left_flag) {
		$banners_html['left'] = "\n<script type=\"text/javascript\">\n";
		$banners_html['left'].= "var rotate_left_banner_timer = setInterval(RotateBannersLeft, 0);\n";
		$banners_html['left'].= "var rotate_left_banner_id=0;\n";
		$banners_html['left'].= "function RotateBannersLeft()\n";
		$banners_html['left'].= "{\n";
		$banners_html['left'].= "clearInterval(rotate_left_banner_timer);\n";
	}
	
	if ($rotate_bottom_flag) {
		$banners_html['bottom'] = "\n<script type=\"text/javascript\">\n";
		$banners_html['bottom'].= "var rotate_bottom_banner_timer = setInterval(RotateBannersBottom, 0);\n";
		$banners_html['bottom'].= "var rotate_bottom_banner_id=0;\n";
		$banners_html['bottom'].= "function RotateBannersBottom()\n";
		$banners_html['bottom'].= "{\n";
		$banners_html['bottom'].= "clearInterval(rotate_bottom_banner_timer);\n";
	}
	
	// Get banners
	$left_banners_count = 0;
	$bottom_banners_count = 0;
	$left_divs = '';
	$bottom_divs = '';
	
	$current_time_formated=date('Y-m-d', time());
	
	$group = $dbconn->GetOne('SELECT id_group FROM '.USER_GROUP_TABLE.' WHERE id_user = ?', array($user[ AUTH_ID_USER ]));
	
	$strSQL =
		'SELECT a.*, c.size_x, c.size_y, c.able_place
		   FROM '.BANNERS_TABLE.' a
	 INNER JOIN '.BANNERS_SIZES_TABLE.' c ON a.size_id = c.id
		  WHERE a.id IN ('.$banners_id_list.')
		    AND (a.stop_after_date > "'.$current_time_formated.'" OR a.stop_after_date = "0000-00-00")
			AND (id_group_for REGEXP "^'.$group.'$" OR id_group_for REGEXP "^'.$group.'," OR id_group_for REGEXP ",'.$group.'," OR id_group_for REGEXP ",'.$group.'$" OR id_group_for="-1") ';
	
	$rs = $dbconn->Execute($strSQL);
	
	while (!$rs->EOF)
	{
		$row = $rs->GetRowAssoc(false);
		
		$banner['id']				= $row['id'];
		$banner['html_code']		= StripJS(stripslashes($row['html_code']));
		$banner['place']			= $row['able_place'];
		$banner['status']			= $row['status'];
		$banner['stop_after_views']	= $row['stop_after_views'];
		$banner['stop_after_hits']	= $row['stop_after_hits'];
		
		if ($banner['status'] == 1)
		{
			$not_stoped = 1;
			
			if ($banner['stop_after_views'] != -1)
			{
				if ($banner['stop_after_views'] <= 0)
				{
					$not_stoped = 0;
				}
				else
				{
					// decrement stop after views
					$banner['stop_after_views']--;
					
					$dbconn->Execute(
						'UPDATE '.BANNERS_TABLE.' SET stop_after_views = ? WHERE id = ?', array($banner['stop_after_views'], $banner['id']));
				}
			}
			
			if (($not_stoped) && ($banner['stop_after_hits'] != -1))
			{
				if ($banner['stop_after_hits'] <= 0) {
					$not_stoped = 0;
				}
			}
			
			if ($not_stoped == 1)
			{
				// Show banner
				// Save views number to global statistics
				
				$glob_stat_rs = $dbconn->Execute(
					'SELECT id, views FROM '.BANNERS_GLOBAL_STATISTICS.' WHERE banner_id = ? AND date = NOW()', array($banner['id']));
				
				if ($glob_stat_rs->RowCount() > 0) {
					$glob_stat = $glob_stat_rs->getRowAssoc(false);
					$glob_stat['views']++;
					$dbconn->Execute('UPDATE '.BANNERS_GLOBAL_STATISTICS.' SET views = ? WHERE id = ?', array($glob_stat['views'], $glob_stat['id']));
				} else {
					$dbconn->Execute('INSERT INTO '.BANNERS_GLOBAL_STATISTICS.' SET views = 1, banner_id = ?, date = NOW()', array($banner['id']));
				}
				
				if ($banner['place'] == 0) // Left
				{
					if ($rotate_left_flag) {
						$left_divs .= "\n".'<DIV align="center" name="left_banners_div'.$left_banners_count.'" id="left_banners_div'.$left_banners_count.'" style="visibility:hidden; position: absolute; left:0; top:0;">';
						$left_divs .= "\n".$banner['html_code'];
						$left_divs .= "\n</DIV>\n";
						
						$banners_html['left'] .= 'if (document.getElementById("left_banners_div'.$left_banners_count.'")) document.getElementById("left_banners_div'.$left_banners_count.'").style.visibility="hidden";'."\n";
						$banners_html['left'] .= 'if (document.getElementById("left_banners_div'.$left_banners_count.'")) document.getElementById("left_banners_div'.$left_banners_count.'").style.position="absolute";'."\n";
					} else {
						$banners_html['left'] .= $banner['html_code'];
					}
					
					$left_banners_count++;
				}
				else
				{
					if ($rotate_bottom_flag)
					{
						$bottom_divs .= "\n".'<DIV align=center name="bottom_banners_div'.$bottom_banners_count.'" id="bottom_banners_div'.$bottom_banners_count.'" style="visibility:hidden; position: absolute; left:0; top:0;">';
						$bottom_divs .= "\n".$banner["html_code"];
						$bottom_divs .= "\n</DIV>\n";
						
						$banners_html['bottom'] .= 'if (document.getElementById("bottom_banners_div'.$bottom_banners_count.'")) document.getElementById("bottom_banners_div'.$bottom_banners_count.'").style.visibility="hidden";'."\n";
						$banners_html['bottom'] .= 'if (document.getElementById("bottom_banners_div'.$bottom_banners_count.'")) document.getElementById("bottom_banners_div'.$bottom_banners_count.'").style.position="absolute";'."\n";;
					}
					else
					{
						$banners_html['bottom'] .= $banner['html_code'];
					}
					
					$bottom_banners_count++;
				}
			}
		}
		$rs->MoveNext();
	}
	
	if ($rotate_left_flag)
	{
		if ($left_banners_count)
		{
			$banners_html['left'] .= "if (document.getElementById('left_banners_div'+rotate_left_banner_id)) document.getElementById('left_banners_div'+rotate_left_banner_id).style.visibility=\"\";\n";
			$banners_html['left'] .= "if (document.getElementById('left_banners_div'+rotate_left_banner_id)) document.getElementById('left_banners_div'+rotate_left_banner_id).style.position=\"\";\n";
			$banners_html['left'] .= "rotate_left_banner_id++;\n";
			$banners_html['left'] .= "if (rotate_left_banner_id>".($left_banners_count-1).") rotate_left_banner_id=0;\n";
			$banners_html['left'] .= "rotate_left_banner_timer = setInterval(RotateBannersLeft, $rotate_left_time);\n";
			$banners_html['left'] .= "};\n";
			$banners_html['left'] .= "</script>\n";
			$banners_html['left'] .= $left_divs;
		}
		else
		{
			$banners_html['left'] .= "};\n";
			$banners_html['left'] .= "</script>\n";
		}
	}
	
	if ($rotate_bottom_flag)
	{
		if ($bottom_banners_count)
		{
			$banners_html['bottom'] .= "if (document.getElementById('bottom_banners_div'+rotate_bottom_banner_id)) document.getElementById('bottom_banners_div'+rotate_bottom_banner_id).style.visibility=\"\";\n";
			$banners_html['bottom'] .= "if (document.getElementById('bottom_banners_div'+rotate_bottom_banner_id)) document.getElementById('bottom_banners_div'+rotate_bottom_banner_id).style.position=\"\";\n";
			$banners_html['bottom'] .= "rotate_bottom_banner_id++;\n";
			$banners_html['bottom'] .= "if (rotate_bottom_banner_id>".($bottom_banners_count-1).") rotate_bottom_banner_id=0;\n";
			$banners_html['bottom'] .= "rotate_bottom_banner_timer = setInterval(RotateBannersBottom, $rotate_bottom_time);\n";
			$banners_html['bottom'] .= "};\n";
			$banners_html['bottom'] .= "</script>\n";
			$banners_html['bottom'] .= $bottom_divs;
		}
		else
		{
			$banners_html['bottom'] .= "};\n";
			$banners_html['bottom'] .= "</script>\n";
		}
	}
	
	$smarty->assign('banner', $banners_html);
	return;
}


function GetRightModulePath($file)
{
	global $config;
	
	// does the same thing as: return '/'.basename($file);
	
	$file_name = substr($file, strlen($config['site_path']));
	$file_name = str_replace("\\", '/', $file_name);
	if (substr($file_name, 0, 1) != '/') {
		$file_name = '/'.$file_name;
	}
	return $file_name;
}


function FormatDate($date, $format)
{
	$year	= intval(substr($date, 0, 4));
	$month	= intval(substr($date, 5, 2));
	$day	= intval(substr($date, 8, 2));
	$hour	= intval(substr($date, 11, 2));
	$minute	= intval(substr($date, 14, 2));
	$sec	= intval(substr($date, 17, 2));
	return date($format, mktime($hour, $minute, $sec, $month, $day, $year));
}


function n2br($str)
{
	return eregi_replace("\n", '<br>', $str);
}


function GetPermissionsUser($id_user)
{
	global $dbconn;
	
	$strSQL =
		'SELECT DISTINCT a.id_module
		   FROM '.GROUP_MODULE_TABLE.' a
	 INNER JOIN '.USER_GROUP_TABLE.' b ON b.id_group = a.id_group
		  WHERE b.id_user = ?';
	
	$rs = $dbconn->Execute($strSQL, array($id_user));
	
	$module = array();
	
	while (!$rs->EOF){
		$module[] = $rs->fields[0];
		$rs->MoveNext();
	}
	
	return $module;
}

//VP Send Site Activity Notification to users reg email
function SendNotification($noti_to, $noti_from, $noti_type, $extra = null)
{
	global $lang_mail, $config, $dbconn;
	
	$debug = false;
	
	$noti_type = strtolower($noti_type);
	
	// GA_TRACKING
	switch ($noti_type) {
		case 'hotlisted':
			$_SESSION['ga_event_code'] = 'addtohotlist';
		break;
		case 'kissed':
			$_SESSION['ga_event_code'] = 'sentkiss';
		break;
		case 'invited':
			$_SESSION['ga_event_code'] = 'connectinvite';
		break;
		case 'accepted':
			$_SESSION['ga_event_code'] = 'inviteaccepted';
		break;
	}
	
	if ($debug) print $noti_from.'|'.$noti_to.'|'.$noti_type.'<br>';
	
	if ($noti_type == 'profile_viewed') {
		// check subscription
		$strSQL = 'SELECT id_user FROM '.SUBSCRIBE_USER_TABLE.' WHERE type = "s" AND id_subscribe = "3" AND id_user = ?';
		$rs = $dbconn->Execute($strSQL, array($noti_to));
		if (empty($rs->fields[0])) {
			return;
		}
		$rs->free();
	}
	
	$content_array = array();
	
	$content_array['urls']		= GetUserEmailLinks();
	
	// recipient data
	$rs = $dbconn->Execute('SELECT login, fname, sname, icon_path, email, gender, site_language FROM '.USERS_TABLE.' WHERE id = ?', array($noti_to));
	$row = $rs->getRowAssoc(false);
	$content_array['login']		= stripslashes($row['login']);
	$content_array['fname']  	= stripslashes($row['fname']);
	$content_array['sname']  	= stripslashes($row['sname']);
	$content_array['icon']		= $config['server'].$config['site_root'].'/uploades/icons/'.$row['icon_path'];
	$email_to					= stripslashes($row['email']);
	$gender						= $row['gender'];
	$site_lang					= $row['site_language'];
	unset($row);
	$rs->free();
	
	if ($debug) print $gender.'|'.$site_lang.'<br>';
	
	// include mail language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	// login token
	$token = CreateToken($noti_to);
	
	// sender data
	$rs = $dbconn->Execute('SELECT fname, icon_path FROM '.USERS_TABLE.' WHERE id = ?', array($noti_from));
	$row = $rs->getRowAssoc(false);
	$content_array['from_id']		= $noti_from;
	$content_array['from_link']		= $config['server'].$config['site_root'].'/viewprofile.php?id='.$noti_from.'&amp;login_id='.$noti_to.'&amp;token='.$token;
	$content_array['from_fname']	= stripslashes($row['fname']);
	$content_array['from_icon']		= $config['server'].$config['site_root'].'/uploades/icons/big_'.$row['icon_path'];
	unset($row);
	$rs->free();
	
	// language suffix
	$suffix = ($gender == GENDER_MALE) ? '_e' : '_t';
	
	if ($debug) print $noti_type.$suffix.'<br>';
	
	// subject
	$subject = $lang_mail[$noti_type.$suffix]['subject'];
	$subject = str_replace('[SENDER_NAME]', $content_array['from_fname'], $subject);
	$subject = str_replace('[DATE]', date('m/d/Y'), $subject);
	
	// subject used inside message
	$content_array['subject_2'] = $lang_mail[$noti_type.$suffix]['subject_2'];
	$content_array['subject_2'] = str_replace('[SENDER_NAME]', $content_array['from_fname'], $content_array['subject_2']);
	
	// message
	$content_array['message'] = $lang_mail[$noti_type.$suffix]['message'];
	$content_array['message'] = str_replace('[SENDER_NAME]', $content_array['from_fname'], $content_array['message']);
	
	// message_sub
	$content_array['message_sub'] = $lang_mail[$noti_type.$suffix]['message_sub'];
	
	if ($noti_type == 'ecard_received') {
		$read_link = $config['server'].$config['site_root'].'/mailbox.php?sel=viewto&amp;id='.$extra.'&amp;login_id='.$noti_to.'&amp;token='.$token;
		$content_array['message_sub'] = str_replace('[READ_LINK]', $read_link, $content_array['message_sub']);
	}
	
	if ($debug) print_r($content_array);
	
	// recipient
	$name_to = trim($content_array['fname'].' '.$content_array['sname']);
	
	SendMail($site_lang, $email_to, $config['site_email'], $subject, $content_array,
		'mail_noti_generic_user', null, $name_to, '', $noti_type, $gender);
	
	if ($debug) exit;
}

function IsFileAllowed($file)
{
	global $dbconn;
	
	//RS: not needed any longer, we can check $_SESSION['permissions']
	##$modules = GetPermissionsUser($user[ AUTH_ID_USER ]);
	
	$id_module = $dbconn->GetOne('SELECT id_module FROM '.MODULE_FILE_TABLE.' WHERE file = ?', array($file));
	
	##if (!empty($modules) && in_array($id_module, $modules)) {
	if (!empty($_SESSION['permissions']) && in_array($id_module, $_SESSION['permissions'])) {
		return 1;
	}
	
	AlertPage($file);
	exit;
}

// used in alert.php to show which groups are having the permissions to access a feature
function GroupListForModule($id_module)
{
	global $dbconn, $config, $user;
	
	if ($config['use_gender_membership'])
	{
		$strSQL =
			'SELECT DISTINCT a.id_group
			   FROM '.GROUP_MODULE_TABLE.' a
		 INNER JOIN '.GROUPS_TABLE.' g ON g.id = a.id_group
			  WHERE a.id_module = ? AND g.is_gender_group = "1" AND g.gender = ?';
		$rs = $dbconn->Execute($strSQL, array($id_module, $user[ AUTH_GENDER ]));
	}
	else
	{
		$strSQL =
			'SELECT DISTINCT a.id_group
			   FROM '.GROUP_MODULE_TABLE.' a
		 INNER JOIN '.GROUPS_TABLE.' g ON g.id = a.id_group
			  WHERE a.id_module = ? AND g.is_gender_group = "0"';
		$rs = $dbconn->Execute($strSQL, array($id_module));
	}
	
	$group_arr = array();
	
	while (!$rs->EOF) {
		$group_arr[] = $rs->fields[0];
		$rs->MoveNext();
	}
	
	return $group_arr;
}

// Get Remaining Days In User's Account
function GetRemainDaysInAccount($id_user)
{
	global $dbconn;
	
	$strSQL = 'SELECT DATEDIFF(date_end, NOW()) FROM '.BILLING_USER_PERIOD_TABLE.' WHERE id_user = ?';
	$days_remain = $dbconn->getOne($strSQL, array($id_user));
	return $days_remain;
}

// Reset user group
// RS: as per 2012/08 there is no need any longer to reset a user or move him to another group,
// the user just expires and is having 0 days left

function ResetUserGroup($id_user, $id_group)
{
	global $dbconn, $config;
	
	if (RESET_USER_GROUP_ACTION == 'STAY_IN_GROUP')
	{
		return $id_group;
	}
	
	if (RESET_USER_GROUP_ACTION == 'INSTALLMENTS')
	{
		if ($id_group == MM_PLATINUM_LADY_FIRST_INS_ID || $id_group == MM_PLATINUM_LADY_SECOND_INS_ID) {			
			//Her Platinum Date has expired so change her back to Platinum Pending
			$dbconn->Execute('UPDATE '. USER_GROUP_TABLE .' SET id_group = ? WHERE id_user = ?', array(MM_PLATINUM_LADY_PENDING_ID, $id_user));
			$id_group_new = MM_PLATINUM_LADY_PENDING_ID;
		} else {
			return $id_group;
		}
	}
	elseif (RESET_USER_GROUP_ACTION == 'ON_HOLD')
	{
		// old membership model with inactive groups
		switch ($id_group) {
			case MM_TRIAL_GUY_ID:		$id_group_new = MM_INACT_TRIAL_GUY_ID; break;
			case MM_TRIAL_LADY_ID:		$id_group_new = MM_INACT_TRIAL_LADY_ID; break;
			case MM_REGULAR_GUY_ID:		$id_group_new = MM_INACT_REGULAR_GUY_ID; break;
			case MM_REGULAR_LADY_ID:	$id_group_new = MM_INACT_REGULAR_LADY_ID; break;
			case MM_PLATINUM_GUY_ID:	$id_group_new = MM_INACT_PLATINUM_GUY_ID; break;
			case MM_PLATINUM_LADY_ID:	$id_group_new = MM_INACT_PLATINUM_LADY_ID; break;
			case MM_ELITE_GUY_ID:		$id_group_new = MM_INACT_ELITE_GUY_ID; break;
			default:					$id_group_new = $id_group;
		}
		
		if ($id_group != $id_group_new) {
			$dbconn->Execute('UPDATE '.USER_GROUP_TABLE.' SET id_group = ? WHERE id_user = ?', array($id_group_new, $id_user));
		}
		### do not delete this record, we need it to see in which group the user was
		###
		### $dbconn->Execute('DELETE FROM '.BILLING_USER_PERIOD_TABLE.' WHERE id_user = ?', array($id_user));
	}
	elseif (RESET_USER_GROUP_ACTION == 'DOWNGRADE')
	{
		// discarded by Owen, a user should never be downgraded
		
		switch ($id_group)
		{
			case MM_REGULAR_GUY_ID:
			
				// regular guys never expire
				$id_group_new = MM_REGULAR_GUY_ID;
			
			break;
			
			case MM_REGULAR_LADY_ID:
				
				// move to Trial Lady and reset expiration date
				$strSQL = 'UPDATE '.USER_GROUP_TABLE.' SET id_group = ? WHERE id_user = ?';
				$dbconn->Execute($strSQL, array(MM_TRIAL_LADY_ID, $id_user));
				
				$strSQL = 'UPDATE '.BILLING_USER_PERIOD_TABLE.' SET id_group_period = ?, date_end = ? WHERE id_user = ?';
				$dbconn->Execute($strSQL, array(MM_TRIAL_LADY_PERIOD_ID, UNLIMITED_DATE_END, $id_user));
				
				$id_group_new = MM_TRIAL_LADY_ID;
				$date_end_new = UNLIMITED_DATE_END;
				
			break;
			
			case MM_PLATINUM_GUY_ID:
				
				// check if user was ever Regular before aka bought credit points
				$strSQL = 'SELECT id FROM '.BILLING_ENTRY_TABLE.' WHERE id_user = ? AND id_group IN (!, !)';
				$check = $dbconn->getOne($strSQL, array($id_user, PG_SINGLE_CREDIT_POINTS, PG_CREDIT_POINTS_PACK));
				
				if (empty($check))
				{
					// move to Trial Guy and reset expiration date
					$strSQL = 'UPDATE '.USER_GROUP_TABLE.' SET id_group = ? WHERE id_user = ?';
					$dbconn->Execute($strSQL, array(MM_TRIAL_GUY_ID, $id_user));
					
					$strSQL = 'UPDATE '.BILLING_USER_PERIOD_TABLE.' SET id_group_period = ?, date_end = ? WHERE id_user = ?';
					$dbconn->Execute($strSQL, array(MM_TRIAL_GUY_PERIOD_ID, UNLIMITED_DATE_END, $id_user));
					
					$id_group_new = MM_TRIAL_GUY_ID;
					$date_end_new = UNLIMITED_DATE_END;
				}
				else
				{
					// move to Regular Guy and reset expiration date
					$strSQL = 'UPDATE '.USER_GROUP_TABLE.' SET id_group = ? WHERE id_user = ?';
					$dbconn->Execute($strSQL, array(MM_REGULAR_GUY_ID, $id_user));
					
					$strSQL = 'UPDATE '.BILLING_USER_PERIOD_TABLE.' SET id_group_period = ?, date_end = ? WHERE id_user = ?';
					$dbconn->Execute($strSQL, array(MM_REGULAR_GUY_PERIOD_ID, UNLIMITED_DATE_END, $id_user));
					
					$id_group_new = MM_REGULAR_GUY_ID;
					$date_end_new = UNLIMITED_DATE_END;
				}
				
			break;
			
			case MM_PLATINUM_LADY_ID:
				
				// move to Trial Lady and reset expiration date
				$strSQL = 'UPDATE '.USER_GROUP_TABLE.' SET id_group = ? WHERE id_user = ?';
				$dbconn->Execute($strSQL, array(MM_TRIAL_LADY_ID, $id_user));
				
				$strSQL = 'UPDATE '.BILLING_USER_PERIOD_TABLE.' SET id_group_period = ?, date_end = ? WHERE id_user = ?';
				$dbconn->Execute($strSQL, array(MM_TRIAL_LADY_PERIOD_ID, UNLIMITED_DATE_END, $id_user));
				
				$id_group_new = MM_TRIAL_LADY_ID;
				$date_end_new = UNLIMITED_DATE_END;
				
			break;
		}
	}
	
	// SOLVE360
	if ($id_group != $id_group_new)
	{
		if (SOLVE360_CONNECTION) {
			require_once $config['site_path'].'/include/Solve360Service.php';
			$solve360Service = new Solve360Service(SOLVE360_USER, SOLVE360_TOKEN);
			
			$solve360 = array();
			require $config['site_path'].'/include/Solve360CustomFields.php';
			
			$new_group_name = $dbconn->getOne('SELECT name FROM '.GROUPS_TABLE.' WHERE id = ?', array($id_group_new));
			
			$contactData = array(
				$solve360['Current Group'] => $new_group_name,
			);
			
			if (isset($date_end_new)) {
				$contactData[ $solve360['TLDF Membership Ends'] ] = $date_end_new;
			}
			
			$rs = $dbconn->Execute('SELECT id_solve360, login FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
			$id_solve360 = $rs->fields[0];
			$login = $rs->fields[1];
			$rs->Free();
			
			if (!empty($id_solve360)) {
				$contact = $solve360Service->editContact($id_solve360, $contactData);
				if (isset($contact->errors)) {
					$subject = 'Error while downgrading Current Group after expiration';
					solve360_api_error($contact, $subject, $login);
				}
			}
			// maybe add contact
		}
	}
	
	return $id_group_new;
}

function AlertPage($file = '')
{
	global $dbconn, $config;
	
	setViewHistory($file);
	
	$id_module = $dbconn->GetOne('SELECT id_module FROM '.MODULE_FILE_TABLE.' WHERE file = ?', array($file));
	
	// header("location: ".$config["server"].$config["site_root"]."/alert.php?id_module=".$id_module."&err=1");
	
	echo
		'<script>
		if (opener) {
			opener.location.href="'.$config['server'].$config['site_root'].'/alert.php?id_module='.$id_module.'";
			window.close();
			opener.focus();
		} else {
			location.href="'.$config['server'].$config['site_root'].'/alert.php?id_module='.$id_module.'";
		}
		</script>';
	
	exit;
}


// get notifications when friends go to online
//
function GetAlertsMessage()
{
	global $dbconn, $user;
	
	## $script_str = '';
	
	$rs = $dbconn->Execute(
		'SELECT a.id, a.id_from, b.login
		   FROM '.ONLINE_NOTICE_TABLE.' a
	  LEFT JOIN '.USERS_TABLE.' b ON b.id = a.id_from
		  WHERE a.id_to = ? AND a.readed = "0" AND a.type = "1"',
		  array($user[ AUTH_ID_USER ]));
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		
		// $dbconn->Execute("update ".ONLINE_NOTICE_TABLE." set readed='1' where id='".$row["id"]."' ");
		
		$dbconn->Execute('DELETE FROM '.ONLINE_NOTICE_TABLE.' WHERE id = ?', array($row['id']));
		# disabled as per Owen's email of 2012-03-02
		# $script_str .= "alert('".str_replace('[user]', $row['login'], $lang['confirm']['online_notice'])."');\n";
		$rs->MoveNext();
	}
	/*
	if (strlen($script_str)) {
		$smarty->assign('online_notice', '<script>'.$script_str.'</script>');
	}
	*/
	
	return;
}

function SetModuleStatistic($file_name)
{
	global $dbconn, $user;
	
	$rs = $dbconn->Execute('SELECT id_module FROM '.MODULE_FILE_TABLE.' WHERE file = ?', array($file_name));
	$id_module = $rs->fields[0];
	
	if ($id_module == null) {
		return;
	}
	
	$rs->free();
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$rs = $dbconn->Execute('SELECT COUNT(*) FROM '.MODULE_STATISTIC_TABLE.' WHERE id_module = ? AND id_user = ?', array($id_module, $id_user));
	
	if (empty($rs->fields[0]))
	{
		$dbconn->Execute('INSERT INTO '.MODULE_STATISTIC_TABLE.' SET id_module = ?, id_user = ?, date_visit = NOW(), count = "1"',
			array($id_module, $id_user));
	}
	else
	{
		$dbconn->Execute('UPDATE '.MODULE_STATISTIC_TABLE.' SET count = count + 1, date_visit = NOW() WHERE id_module = ? AND id_user = ?',
			array($id_module, $id_user));
	}
	return;
}


function Replace_Chat_Tags($text)
{
	global $config, $config_index;
	
	// firstly replace simply tags in message
	//
	if (strlen($text) > 0)
	{
		for ($i = 0; $i < count($config_index['simple_tags']); $i++) {
			$text = str_replace($config_index['simple_tags'][$i]['base'], $config_index['simple_tags'][$i]['site'], $text);
		}
		
		// than replace reg exp tags
		for ($i = 0; $i < count($config_index['reg_tags']); $i++) {
			$text_arr = array();
			while (eregi($config_index['reg_tags'][$i+1]['reg'], $text, $text_arr)) {
				$str_1 = str_replace('[value]', $text_arr[1], $config_index['reg_tags'][$i+1]['base']);
				$str_2 = str_replace('[value]', $text_arr[1], $config_index['reg_tags'][$i+1]['site']);
				$text  = str_replace($str_1, $str_2, $text);
			}
		}
		
		// replace smiles
		$path = $config['server'].$config['site_root'].$config['index_theme_path'].'/icons/';
		
		$text_arr = array();
		
		while (eregi($config_index['reg_icon']['reg'], $text, $text_arr)) {
			$str_1 = str_replace('[value]', $text_arr[1], $config_index['reg_icon']['base']);
			$str_2 = str_replace('[value]', $path.$text_arr[1].'.gif', $config_index['reg_icon']['site']);
			$text  = str_replace($str_1, $str_2, $text);
		}
	}
	
	return $text;
}


function GallaryFormatImageTag($file_path, $width='', $height='', $trans='')
{
	global $config;
	
	if (file_exists($config['site_path'].$file_path))
	{
		$file_info = GetImageSize($config['site_path'].$file_path);
		$file_width = $file_info[0];
		$file_height = $file_info[1];
		
		if ((int)$trans == 0)
		{
			if ($file_width > $width) {
				$file_height = round($file_height * $width / $file_width);
				$file_width = $width;
			}
			
			if ($file_height > $height) {
				$file_width = round($file_width * $height / $file_height);
				$file_height = $height;
			}
		}
		return '<img src="'.$config['server'].$config['site_root'].$file_path.'" border="0" width="'.$file_width.'" height="'.$file_height.'">';
	}
	else
	{
		return '';
	}
}


function GetCountryList($sel_country = 0)
{
	global $dbconn;
	
	$sel_country = (int) $sel_country;
	
	$rs = $dbconn->Execute('SELECT DISTINCT id, name FROM '.COUNTRY_SPR_TABLE.' ORDER BY name');
	
	$i = 0;
	$spr_arr = array();
	
	while(!$rs->EOF){
		$row = $rs->GetRowAssoc(false);
		$spr_arr[$i]['id'] = $row['id'];
		$spr_arr[$i]['name'] = $row['name'];
		$spr_arr[$i]['sel'] = ($sel_country == $row['id'] ? 1 : 0);
		$rs->MoveNext();
		$i++;
	}
	return $spr_arr;
}


function GetActiveUserInfo($user)
{
	global $dbconn, $lang, $smarty;
	
	// GA_TRACKING
	ga_dequeue_event();
	
	$id_user = $user[ AUTH_ID_USER];
	
	// new messages (also IM, though we don't use the IM in TLDF)
	//
	if ($id_user && ! $user[ AUTH_GUEST ] && ! $user[ AUTH_ROOT ])
	{
		$rs = $dbconn->Execute('SELECT COUNT(id) FROM '.MAILBOX_TABLE.' WHERE id_to = ? AND was_read = "0" AND deleted_to = "0"', array($id_user));
		$active_user_info['emailed_me_new_count'] = $rs->fields[0];
		unset($rs);
		
		// TLDF uses neither userplane nor the built-in flash messenger
		$active_user_info['show_messages'] = 0;
		
		#$use_pilot_module_webmessenger = GetSiteSettings('use_pilot_module_webmessenger');
		#
		#if (empty($use_pilot_module_webmessenger)) {
		#	$rs = $dbconn->Execute('SELECT COUNT(id) FROM '.$config['table_prefix'].'wm_mms_users_messages WHERE to_id = ? AND mess_type=1', array($id_user));
		#	$active_user_info['im_me_new_count'] = $rs->fields[0];
		#	$rs->free();
		#	
		#	$active_user_info['im_link'] = $config['site_root'].'/w_communicator/flash_im.php';
		#	$active_user_info['show_messages'] = $active_user_info['im_me_new_count'] ? 1 : 0;
		#}
	}
	else
	{
		$active_user_info['emailed_me_new_count'] = 0;
		$active_user_info['show_messages'] = 0;
	}
	
	// additional status data for the active user
	//
	$strSQL =
		'SELECT DATEDIFF(a.date_end, NOW()) AS days_remain,
				DATE_FORMAT(a.date_begin, "%m/%d/%Y") AS date_begin,
				DATE_FORMAT(a.date_end, "%m/%d/%Y") AS date_end,
				a.date_end AS date_end_raw,
				b.recurring,
				c.name
		   FROM '.BILLING_USER_PERIOD_TABLE.' AS a
	  LEFT JOIN '.GROUP_PERIOD_TABLE.' AS b ON a.id_group_period = b.id
	  LEFT JOIN '.GROUPS_TABLE.' AS c ON b.id_group = c.id
		  WHERE a.id_user = ?';
	
	$rs = $dbconn->Execute($strSQL, array($id_user));
	$row = $rs->GetRowAssoc(false);
	
	$active_user_info['days_remain']	= (int) $row['days_remain'];
	$active_user_info['recurring']		= 0;
	$active_user_info['canceled']		= 0;
	$active_user_info['unlimited']		= 0;
	
	if ($active_user_info['days_remain'] >= 0) {
		$active_user_info['recurring'] = (int) $row['recurring'];
		if (substr($row['date_end_raw'], 0, 10) != substr(UNLIMITED_DATE_END, 0, 10)) {
			$active_user_info['date_end'] = $row['date_end'];
			if ($active_user_info['recurring']) {
				$active_user_info['canceled'] = 1;
			}
		} elseif (!$active_user_info['recurring']) {
			$active_user_info['unlimited'] = 1;
		}
	}
	
	if ($active_user_info['recurring'] && ! $active_user_info['canceled']) {
		$active_user_info['period'] = $row['date_begin'].' '.$lang['account']['until_canceled'];
	} elseif ($active_user_info['unlimited']) {
		$active_user_info['period'] = $row['date_begin'].' - '.$lang['account']['unlimited'];
	} else {
		$active_user_info['period'] = $row['date_begin'].' - '.$row['date_end'];
	}
	
	$active_user_info['last_active_group'] = $row['name'];
	
	unset($row);
	$rs->Free();
	
	// membership group name
	$id_group = $user[ AUTH_ID_GROUP ];
	
	$rs = $dbconn->Execute('SELECT name FROM '.GROUPS_TABLE.' WHERE id = ?', array($id_group));
	$active_user_info['user_group'] = $rs->fields[0];
	$rs->Free();
	
	// Trial and Regular with status = 0 are pending
	if ($id_group == MM_TRIAL_GUY_ID || $id_group == MM_TRIAL_LADY_ID || $id_group == MM_REGULAR_GUY_ID || $id_group == MM_REGULAR_LADY_ID) {
		if (!$user[ AUTH_STATUS ]) {
			$active_user_info['user_group'] .= ' ('. $lang['users']['pending'].')';
		}
	}
	// Ralf to Narendra:
	// old platinum applied code replaced with new code
	
	// Check for Applied for Platinum
	if ($id_group == MM_PLATINUM_GUY_APPLIED_ID || $id_group == MM_PLATINUM_LADY_APPLIED_ID) {
		$active_user_info['platinum_applied'] = true;
	} else if ($id_group == MM_PLATINUM_LADY_SECOND_INS_ID || $id_group == MM_PLATINUM_LADY_FIRST_INS_ID){
		$active_user_info['user_group'] = 'Platinum Lady';
	}
	
	// GA_TRACKING
	// gender for Google Analytics
	$_SESSION['ga_gender'] = ga_gender($user[ AUTH_GENDER ]);
	
	// group name for Google Analytics
	$_SESSION['ga_member_status'] = ga_member_status($id_group);
	
	$smarty->assign('active_user_info', $active_user_info);
	
	return $active_user_info;
}

function GetUserAccountDays($id_user)
{
	global $dbconn;
	
	$strSQL =
		'SELECT DATEDIFF(a.date_end, NOW()) AS days_remain, a.date_end, b.recurring
		   FROM '.BILLING_USER_PERIOD_TABLE.' a
	  LEFT JOIN '.GROUP_PERIOD_TABLE.' AS b ON a.id_group_period = b.id
		   WHERE a.id_user = ?';
	$rs = $dbconn->Execute($strSQL, array($id_user));
	$row = $rs->GetRowAssoc(false);
	
	// 0 days means unlimited here, but it also means that the user did not buy membership
	// time and thus needs to pay with credit points for connections
	
	if (substr($row['date_end'], 0, 10) == substr(UNLIMITED_DATE_END, 0, 10) && !$row['recurring']) {
		return 0;
	}
	
	$days_remain = (int) $row['days_remain'];
	
	if ($days_remain < 0) {
		return 0;
	}
	
	return $days_remain;
}


// this function is used in files: advanced_search.php, blog.php. hotlist.php, kises.php
// meet_me.php, meet_them.php, perfect_match.php,  quick_search.php, visit_my_page.php, viewprifile.php
// preveusly this function was defined in all that files
//
function AddToHotList()
{
	global $lang, $dbconn, $user;
	
	$add_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
	$type_id = isset($_GET['type']) ? (int) $_GET['type'] : 0;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	if ($add_id && $id_user && ! $user[ AUTH_GUEST ])
	{
		$rs = $dbconn->Execute('SELECT id FROM '.HOTLIST_TABLE.' WHERE id_user = ? AND id_friend = ?', array($id_user, $add_id));
		
		if (!empty($rs->fields[0])) {
			$dbconn->Execute('UPDATE '.HOTLIST_TABLE.' SET friend_type = ? WHERE id_user = ? AND id_friend = ?', array($type_id, $id_user, $add_id));
		} else {
			$dbconn->Execute('INSERT INTO '.HOTLIST_TABLE.' SET id_user = ?, id_friend = ?, friend_type = ?', array($id_user, $add_id, $type_id));
		}
		
		//VP send notification on users reg email
		SendNotification($add_id, $id_user, 'HOTLISTED'); // to, from, type
	}
	
	$subject = $lang['add_to_hotlist']['alert_subject'];
	
	if ($type_id)
	{
		$ml = new MultiLang();
		
		$strSQL =
			'SELECT b.'.$ml->DefaultFieldName().' AS friend_type
			   FROM '.HOTLIST_TABLE.' a
		  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.id_reference = a.friend_type AND b.table_key = "'.$ml->TableKey(HOTLIST_SPR_TABLE).'"
			  WHERE a.id_user = ? AND a.id_friend = ?';
		
		$rs = $dbconn->Execute($strSQL, array($id_user, $add_id));
		
		$body = $lang['add_to_hotlist']['alert_message_1'].' '.stripslashes($rs->fields[0]).'.';
	}
	else
	{
		$body = $lang['add_to_hotlist']['alert_message_1a'];
	}
	
	$body .= $lang['add_to_hotlist']['alert_message_2'].'<br><br>';
	
	$strSQL =
		'INSERT INTO '.MAILBOX_TABLE.' (id_from, id_to, subject, body, date_creation, was_read, deleted_from, deleted_to)
			   VALUES (?, ?, ?, ?, NOW(), "0", "0", "0")';
	
	$rs = $dbconn->Execute($strSQL, array($id_user, $add_id, $subject, $body));
	$return_param['err'] = $lang['err']['user_was_added_hotlist'];
	return $return_param;
}

//VP this function is used for to check that is user blacklisted.
function isMeBlackListed($id_user1)
{
	global $dbconn, $user;
	
	if (empty($id_user1)) return false;
	
	$check = $dbconn->GetOne('SELECT COUNT(*) FROM '.BLACKLIST_TABLE.' WHERE id_user = ? AND id_enemy = ?', array($id_user1, $user[ AUTH_ID_USER ]));
	
	return !empty($check);
}

function AddToConnections()
{
	global $lang, $dbconn, $user;
	
	$add_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
	
	$id_user = $user[ AUTH_ID_USER ];
	$gender  = $user[ AUTH_GENDER ];
	
	if ($add_id <= 0 || $id_user <= 0 || $user[ AUTH_GUEST ]) {
		$return_param['err'] = '';
		return $return_param;
	}
	
	$connected_status = getConnectedStatus($id_user, $add_id);
	
	// checking already connected
	if ($connected_status == CS_CONNECTED) {
		if ($gender == GENDER_MALE) {
			$return_param['err'] = $lang['err']['connection_invite_already_connected_with_lady'];
		} else {
			$return_param['err'] = $lang['err']['connection_invite_already_connected_with_guyy'];
		}
		return $return_param;
	}
	
	// checking invitation sent
	if ($connected_status == CS_SENT) {
		if ($gender == GENDER_MALE) {
			$return_param['err'] = $lang['err']['connection_invite_already_sent_to_lady'];
		} else {
			$return_param['err'] = $lang['err']['connection_invite_already_sent_to_guy'];
		}
		return $return_param;
	}
	
	// checking invitation received
	if ($connected_status == CS_RECEIVED) {
		if ($gender == GENDER_MALE) {
			$return_param['err'] = $lang['err']['connection_invite_already_received_from_lady'];
		} else {
			$return_param['err'] = $lang['err']['connection_invite_already_received_from_guy'];
		}
		return $return_param;
	}
	
	// checking permissions
	if (empty($_SESSION['permissions']['connection_invite'])) {
		if ($user[ AUTH_IS_TRIAL ]) {
			$return_param['err'] = $lang['err']['connection_invite_permission_denied_trial'];
		} elseif ($user[ AUTH_IS_TRIAL_INACTIVE ]) {
			$return_param['err'] = $lang['err']['connection_invite_permission_denied_trial_inactive'];
		} elseif ($user[ AUTH_IS_REGULAR ]) {
			$return_param['err'] = $lang['err']['connection_invite_permission_denied_regular'];
		} elseif ($user[ AUTH_IS_REGULAR_INACTIVE ]) {
			$return_param['err'] = $lang['err']['connection_permission_denied_regular_inactive'];
		} elseif ($user[ AUTH_IS_PLATINUM_INACTIVE ]) {
			$return_param['err'] = $lang['err']['connection_invite_permission_denied_platinum_inactive'];
		} elseif ($user[ AUTH_IS_ELITE_INACTIVE ]) {
			$return_param['err'] = $lang['err']['connection_invite_permission_denied_elite_inactive'];
		}
		return $return_param;
	}
	
	//VP checking whether I am black listed or not
	if (isMeBlackListed($add_id)) {
		$return_param['err'] = $lang['err']['invite_fail_user_blacklisted_you'];
		return $return_param;
	}
	
	// Regualar Ladies and Platinum Ladies with an expiration date in the future which is not 2037-12-31 can always connect
	// for all others we need to check points
	
	$credit_points = GetCreditPoints($id_user);
	$account_days  = GetUserAccountDays($id_user);
	
	$freeConnectionsArr = array(
		MM_PLATINUM_LADY_ID,
		MM_PLATINUM_LADY_FIRST_INS_ID,
		MM_PLATINUM_LADY_SECOND_INS_ID
	);
	
	$ok = false;
	
	if (in_array($user[ AUTH_ID_GROUP ], $freeConnectionsArr)) {
		// platinum ladies get free connections
		$ok = true;
	} elseif ($user[ AUTH_ID_GROUP ] == MM_REGULAR_LADY_ID) {
		// regular ladies can only connect when they have days or points
		if ($account_days > 0 || $credit_points >= POINT_USER_CONNECTION_INVITE) {
			$ok = true;
		}
	} else {
		// trial ladies and all guys can only connect when they have points
		if ($credit_points >= POINT_USER_CONNECTION_INVITE) {
			$ok = true;
		}
	}
	
	if (!$ok) {
		if ($gender == GENDER_MALE) {
			$return_param['err'] = $lang['err']['connection_invite_insufficient_points_guy'];
		} else {
			$return_param['err'] = $lang['err']['connection_invite_insufficient_points_lady'];
		}
		return $return_param;
	}
	
	// insert invitation
	$dbconn->Execute('INSERT INTO '.CONNECTIONS_TABLE.' SET id_user = ?, id_friend = ?, friend_type = 0', array($id_user, $add_id));
	
	// send message
	$subject = $lang['add_to_connections']['alert_subject'];
	
	$body = $lang['add_to_connections']['alert_message_1a'];
	
	$body.= $lang['add_to_connections']['alert_message_2'].'<br><br>';
	
	$strSQL =
		'INSERT INTO '.MAILBOX_TABLE.' SET
				id_from = ?, id_to = ?, subject = ?, body = ?, date_creation = NOW(), was_read = "0", deleted_from = "0", deleted_to = "0"';
	
	$dbconn->Execute($strSQL, array($id_user, $add_id, $subject, $body));
	
	//VP send notification on users reg email
	SendNotification($add_id, $id_user, 'INVITED'); // to, from, type
	
	$return_param['err'] = $lang['err']['user_was_added_connections'];
	
	return $return_param;
}


//SH2
function SendEcardTo()
{
	global $lang, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	$view_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
	
	//checking whether I am black listed or not
	if (isMeBlackListed($view_id)) {
		$return_param['err'] = $lang['err']['ecard_sending_fail_user_blacklisted_you'];
	}
	return $return_param;
}

//SH2
function SendGiftTo()
{
	global $lang, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	$view_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
	
	//checking whether I am black listed or not
	if (isMeBlackListed($view_id)) {
		$return_param['err'] = $lang['err']['gift_sending_fail_user_blacklisted_you'];
	}
	return $return_param;
}

//SH2
function ViewUserProfile()
{
	global $lang, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	$view_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
	
	//checking whether I am black listed or not
	if (isMeBlackListed($view_id)) {
		$return_param['err'] = $lang['err']['browse_fail_user_blacklisted_you'];
	}
	return $return_param;
}
// this function is used to get the current credit points in user's account
//
function GetCreditPoints($id_user)
{
	global $dbconn;
	
	$rs = $dbconn->Execute('SELECT account_curr FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($id_user));
	$credits = round($rs->fields[0], 2);
	
	return $credits;
}

// this function is used to deduct credit points from user's account
//
function DeductCreditPoints($id_user, $deduction, $txn_type)
{
	global $dbconn;
	
	// settings
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2'));
	
	$deduction = (float) $deduction;
	
	$strSQL = 'UPDATE '.BILLING_USER_ACCOUNT_TABLE.' SET account_curr = account_curr - '.$deduction.' WHERE id_user = ?';
	$dbconn->Execute($strSQL, array($id_user));
	
	if ($txn_type == 'con_invite')
	{
		$id_group = PG_CONNECTION_INVITE; // -4
	}
	elseif ($txn_type == 'con_accept')
	{
		$id_group = PG_CONNECTION_ACCEPT; // -5
	}
	else
	{
		$id_group = PG_SINGLE_CREDIT_POINTS; // -1
	}
	
	$strSQL =
		'INSERT INTO '.BILLING_ENTRY_TABLE.' SET
			id_user = ?, amount = -'.$deduction.', currency = ?, id_group = ?, id_product = 0,
			entry_type = "user_account", txn_type = ?, date_entry = NOW()';
	$dbconn->Execute($strSQL, array($id_user, $settings['site_unit_costunit'], $id_group, $txn_type));
}

// this function is used in files: advanced_search.php, blog.php, kisses.php, meet_me.php,
// meet_them.php, quick_search.php, perfect_match.php, viewproifile.php, visit_my_page.php,
//
function AddToBlackList()
{
	global $lang, $dbconn, $user;
	
	$add_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	if ($add_id && $id_user && ! $user[ AUTH_GUEST ])
	{
		$rs = $dbconn->Execute('SELECT id FROM '.BLACKLIST_TABLE.' WHERE id_user = ? AND id_enemy = ?', array($id_user, $add_id));
		
		if (empty($rs->fields[0])) {
			// GA_TRACKING
			$_SESSION['ga_event_code'] = 'addtoblacklist';
			$dbconn->Execute('INSERT INTO '.BLACKLIST_TABLE.' SET id_user = ?, id_enemy = ?', array($id_user, $add_id));
		}
		
		//add to contactlict w_comm with banned status
		include_once 'w_communicator/wc_config.php';
		
		$dbconn->Execute('DELETE FROM '.USER_CONTACT_LIST_TABLE.' WHERE user_id = ? AND view_user_id = ?', array($id_user, $add_id));
		$dbconn->Execute('INSERT INTO '.USER_CONTACT_LIST_TABLE.' SET user_id = ?, view_user_id = ?, ban_status = "1"', array($id_user, $add_id));
		
		//ignore in flashchat
		$dbconn->Execute('DELETE FROM '.F_CHAT_IGNORS_TABLE.' WHERE userid = ? AND ignoreduserid = ?', array($id_user, $add_id));
		$dbconn->Execute('INSERT INTO '.F_CHAT_IGNORS_TABLE.' SET created = NOW(), userid = ?, ignoreduserid = ?', array($id_user, $add_id));
	}
	
	$return_param['err'] = $lang['err']['user_was_added_blacklist'];
	
	return $return_param;
}


/// this function is used in files: advanced_search.php, blog.php. hotlist.php, kises.php
/// meet_me.php, meet_them.php, perfect_match.php, quick_search.php, visit_my_page.php, viewprifile.php
//
function SendKiss()
{
	global $lang, $dbconn, $user;
	
	$kiss_id = intval($_GET['id']);
	
	$id_user = $user[ AUTH_ID_USER ];
	
	if ($kiss_id && $id_user)
	{
		$you_banned = $dbconn->GetOne('SELECT id FROM '.BLACKLIST_TABLE.' WHERE id_user = ? AND id_enemy = ?', array($kiss_id, $id_user));
		
		if (!$you_banned)
		{
			$dbconn->Execute('INSERT INTO '.KISSLIST_TABLE.' SET id_to = ?, id_from = ?, kiss_date = NOW()', array($kiss_id, $id_user));
			
			$err = $lang['err']['kiss_was_send'];
			
			//VP send notification on users reg email
			SendNotification($kiss_id, $id_user, 'KISSED'); // to, from, type
		}
		else
		{
			$err = $lang['err']['cant_send_kiss'];
		}
	}
	
	$return_param['err'] = $err;
	return $return_param;
}

function getBacklink($search_type = '')
{
	switch ($search_type) {
		case 'a':			$back_link = 'advanced_search.php?sel=search&amp;par=back'; break;
		case 'b':			$back_link = 'blacklist.php?par=back'; break;
		case 'c':			$back_link = 'connections.php?par=back'; break;
		case 'ci':			$back_link = 'connections.php?sel=inbox&amp;par=back'; break;
		case 'co':			$back_link = 'connections.php?sel=outbox&amp;par=back'; break;
		case 'em':			$back_link = 'emailed_me.php?par=back'; break;
		case 'et':			$back_link = 'emailed_them.php?par=back'; break;
		case 'ecm':			$back_link = 'ecards_me.php?par=back'; break;
		case 'ect':			$back_link = 'ecards_them.php?par=back'; break;
		case 'h':			$back_link = 'hotlist.php?par=back'; break;
		// RS:
		// kisses is special because it handles "they kissed me" and "I kissed them" with the same script.
		// sel=i or sel=me is stored in the session and restored when kisses.php?par=back is loaded
		// we can simplify this by splitting kisses.php into kisses_me.php and kisses_them.php to use the same standards as in the other
		// mix and mingle lists, and can then use sync with a file compare tool
#		case 'k':			$back_link = 'kisses.php?par=back'; break;
		case 'ki':			$back_link = 'kisses.php?sel=i&amp;par=back'; break;
		case 'kme':			$back_link = 'kisses.php?sel=me&amp;par=back'; break;
		case 'mm':			$back_link = 'meet_me.php?sel=search&amp;par=back'; break;
		case 'mt':			$back_link = 'meet_them.php?sel=search&amp;par=back'; break;
		case 'p':			$back_link = 'perfect_match.php?sel=search&amp;par=back'; break;
		case 'q':			$back_link = 'quick_search.php?sel=search&amp;par=back'; break;
		case 'q_new':		$back_link = 'quick_search.php?sel=search_new&amp;par=back'; break;
		case 'q_h':			$back_link = 'quick_search.php?sel=search_h&amp;par=back'; break;
		case 'q_on':		$back_link = 'quick_search.php?sel=search_on&amp;par=back'; break;
		case 'q_name':		$back_link = 'quick_search.php?sel=search_name&amp;par=back'; break;
		case 'q_fname':		$back_link = 'quick_search.php?sel=search_fname&amp;par=back'; break;
		case 'q_bd':		$back_link = 'quick_search.php?sel=search_bd&amp;par=back'; break;
		case 'q_keyword':	$back_link = 'quick_search.php?sel=search_keyword&amp;par=back'; break;
		case 'q_tag':		$back_link = 'quick_search.php?sel=search_tag&amp;par=back'; break;
		case 'q_top':		$back_link = 'quick_search.php?sel=search_top&amp;par=back'; break;
		case 'vm':			$back_link = 'visit_my_page.php?par=back'; break;
		case 'vt':			$back_link = 'visit_their_page.php?par=back'; break;
		default:			$back_link = 'quick_search.php?sel=search&amp;par=back'; break;
	}
	return $back_link;
}


function MakeCoupleAction()
{
	global $lang, $dbconn, $user;
	
	$add_id = (isset($_GET["id"]) && intval($_GET["id"]) > 0) ? intval($_GET["id"]) : null;
	
	if ( $add_id != null && $user[ AUTH_ID_USER ] && ! $user[ AUTH_GUEST ]) {
		$strSQL = 'SELECT id FROM '.USERS_TABLE.' WHERE id = ? AND status = "1" AND root_user = "0" AND guest_user = "0"';
		$rs = $dbconn->Execute($strSQL, array($add_id));
		if ( $rs->fields[0]>0 ) {
			$body = $lang["users"]["couple_accept_message"];
			$body .= "<br><br><a href='myprofile.php?sel=couple&id=".$user[ AUTH_ID_USER ]."'>";
			$body .= $lang["users"]["couple_accept_link"];
			$body .= "</a><br><br>";
			
			$strSQL =
				'INSERT INTO '.MAILBOX_TABLE.' SET
						id_from = ?, id_to = ?, subject = ?, body = ?, date_creation = NOW(), was_read = "0", deleted_from = "0", deleted_to = "0"';
			$dbconn->Execute($strSQL, array($user[ AUTH_ID_USER ], $add_id, $lang['users']['couple_accept_subject'], $body));
			
			$dbconn->Execute('UPDATE '.USERS_TABLE.' SET couple_user = ? WHERE id = ?', array($add_id, $user[ AUTH_ID_USER ]));
		}
	}
	$return_param["err"] = $lang["err"]["your_proposal_was_sent"];
	$return_param["par"] = $_GET["par"];		///for quick search and advanced search, blog
	$return_param["ms"] = $_GET["ms"];		///for quick search and advanced search, blog
	return $return_param;
}


function CityInRadius($id_city, $id_distance)
{
	global $dbconn;
	
	$city_array = array();
	
	$rs = $dbconn->Execute('SELECT name, type FROM '.DISTANCE_SPR_TABLE.' WHERE id = ?', array($id_distance));
	$radius = ($rs->fields[1] == 'mile') ? $rs->fields[0] : $rs->fields[0] * 0.6213712;
	
	$rs = $dbconn->Execute('SELECT lon, lat FROM '.CITY_SPR_TABLE.' WHERE id = ?', array($id_city));
	if ($rs->RowCount() > 0) {
		$lon = $rs->fields[0];
		$lat = $rs->fields[1];
		$rs = $dbconn->Execute("SELECT id FROM ".CITY_SPR_TABLE."
			WHERE (POW((69.1*(lon-\"$lon\")*cos($lat/57.3)),\"2\")+POW((69.1*(lat-\"$lat\")),\"2\"))<($radius*$radius)");
		while (!$rs->EOF) {
			$city_array[] = $rs->fields[0];
			$rs->MoveNext();
		}
	}
	
	return $city_array;
}


function VotingTable($id, $search_type="")
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	$set_arr = array("icon_male_default", "icon_female_default", "icons_folder");
	$settings = GetSiteSettings($set_arr);
	
	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];
	
	$rate = $dbconn->Execute('SELECT AVG(estimation), COUNT(estimation) FROM '.USER_RATING_TABLE.' WHERE id_user = ?', array($id));
	$data["current_rating"] = $rating = round($rate->fields[0],2);
	$data["all_vote"] = $rate->fields[1];
	
	$rate_bar = "";
	for ($c=0;$c<=10;$c++) {
		if (($c-$rating) >= 0 && ($c-$rating) >= 1) {
			$rate_bar .= "<img src='".$config["site_root"].$config["index_theme_path"]."/images/empty.gif' height='10' width='10' style='border: 1px #6E6E6E solid;' alt=''>&nbsp;";
		} elseif (($c-$rating) > 0 && ($c-$rating) < 1) {
			$rate_bar .= "<img src='".$config["site_root"].$config["index_theme_path"]."/images/bar.gif' height='10' width='5' style='border-top: 1px #6E6E6E solid; border-left: 1px #6E6E6E solid; border-bottom: 1px #6E6E6E solid;' alt=''><img src='".$config["site_root"].$config["index_theme_path"]."/images/empty.gif' height='10' width='5' style='border-top: 1px #6E6E6E solid; border-right: 1px #6E6E6E solid; border-bottom: 1px #6E6E6E solid;' alt=''>&nbsp;";
		} elseif (($c-$rating) < 0 && (($c-$rating) <= -1)) {
			$rate_bar .= "<img src='".$config["site_root"].$config["index_theme_path"]."/images_path/bar.gif' height='10' width='10' style='border: 1px #6E6E6E solid;' alt=''>&nbsp;";
		}
	}
	$data["current_rating_bar"] = $rate_bar;
	$rate = $dbconn->Execute('SELECT estimation FROM '.USER_RATING_TABLE.' WHERE id_user = ? AND id_voter = ?', array($id, $user[ AUTH_ID_USER ]));
	$data["your_vote"] = ($rate->fields[0]) ? $rate->fields[0] : '0';
	$data["allow_rate"] = true;
	
	if ( ($id == $user[ AUTH_ID_USER ]) || ($rate->fields[0]) ) {
		$data["allow_rate"] = false;
	}
	
	$rs = $dbconn->Execute('SELECT COUNT(*) FROM '.USER_COMMENT_TABLE.' WHERE id_user = ?', array($id));
	$num_records = $rs->fields[0];
	
	$page = (isset($_REQUEST["page"]) && intval($_REQUEST["page"]) > 0) ? intval($_REQUEST["page"]) : 1;
	
	$lim_min = ($page-1)*6;
	$lim_max = 6;
	$limit_str = " LIMIT ".$lim_min.", ".$lim_max;
	
	$strSQL = "select a.id, a.message, DATE_FORMAT(a.comment_date,'".$config["date_format"]."') as date, a.id_voter, b.login, b.gender, b.date_birthday, b.icon_path, c.name as country, d.name as city, r.name as region, e.id_user as session
			from ".USER_COMMENT_TABLE." a, ".USERS_TABLE." b
			left join ".COUNTRY_SPR_TABLE." c on c.id=b.id_country
			left join ".CITY_SPR_TABLE." d on d.id=b.id_city
			left join ".ACTIVE_SESSIONS_TABLE." e on b.id=e.id_user
			left join ".REGION_SPR_TABLE." r on r.id=b.id_region
			where a.id_user = ? and a.id_voter=b.id group by a.id order by a.id desc
			".$limit_str;
	$rs = $dbconn->Execute($strSQL, array($id));
	$data["comments"] = array();
	$i = 0;
	while(!$rs->EOF){
		$row = $rs->GetRowAssoc(false);
		$data["comments"][$i]["message"] = stripslashes(nl2br($row["message"]));
		$data["comments"][$i]["delete_link"] = ($row["id_voter"] == $user[ AUTH_ID_USER ]) ? "./viewprofile.php?id=".$id."&search_type=".$search_type."&sel=delcomment&cid=".$row["id"] : "";
		$data["comments"][$i]["date"] = $row["date"];
		$data["comments"][$i]["name"] = $row["login"];
		$data["comments"][$i]["profile_link"] = ($row["id_voter"] != $user[ AUTH_ID_USER ]) ? "./viewprofile.php?id=".$row["id_voter"] : "";
		$data["comments"][$i]["status"] = $row["session"]?$lang["status"]["on"]:$lang["status"]["off"];
		$data["comments"][$i]["age"] = AgeFromBDate($row["date_birthday"]);
		$data["comments"][$i]["country"] = stripslashes($row["country"]);
		$data["comments"][$i]["region"] = stripslashes($row["region"]);
		$data["comments"][$i]["city"] = stripslashes($row["city"]);
		$icon_path = $row["icon_path"]?$row["icon_path"]:$default_photos[$row["gender"]];
		if($icon_path && file_exists($config["site_path"].$settings["icons_folder"]."/".$icon_path))
		$data["comments"][$i]["icon_path"] = $config["site_root"].$settings["icons_folder"]."/".$icon_path;
		$i++;
		$rs->MoveNext();
	}
	$param = "viewprofile.php?id=".$id."&sel=5&";
	$smarty->assign("links", GetLinkArray($num_records,$page,$param,6));
	$smarty->assign("data_10", $data);
	return;
}


function RateProfile($id,$estimation)
{
	global $dbconn, $user;
	
	$rs = $dbconn->Execute("SELECT COUNT(id_user) from ".USER_RATING_TABLE." where id_voter = ? and id_user = ?", array($user[ AUTH_ID_USER ], $id));
	if (!$rs->fields[0]) {
		$rs = $dbconn->Execute("INSERT INTO ".USER_RATING_TABLE." SET id_user = ?, id_voter = ?, estimation = ?, voting_date = NOW()",
			array($id, $user[ AUTH_ID_USER ], $estimation));
	}
	
	$rs = $dbconn->Execute("SELECT AVG(estimation) FROM ".USER_RATING_TABLE." WHERE id_user = ?", array($id));
	$avg = $rs->fields[0];
	
	//check if user in rating
	$rs = $dbconn->Execute("SELECT id FROM ".USER_TOPTEN_TABLE." WHERE id_user = ?", array($id));
	if ($rs->fields[0]>0) {
		## $top_id = $rs->fields[0];
		//update user average estimation
		$dbconn->Execute("UPDATE ".USER_TOPTEN_TABLE." SET rating = ? WHERE id_user = ?", array($avg, $id));
	} else {
		//rebuild top list
		$rs = $dbconn->Execute("SELECT MIN(rating) FROM ".USER_TOPTEN_TABLE);
		$min = $rs->fields[0];
		if ($avg > $min) {
			$rs = $dbconn->Execute("SELECT gender FROM ".USERS_TABLE." WHERE id = ?", array($id));
			$type = $rs->fields[0];
			
			$dbconn->Execute("INSERT INTO ".USER_TOPTEN_TABLE." SET id_user = ?, type_user = ?, rating = ?", array($id, $type, $avg));
			
			$rs = $dbconn->Execute("SELECT COUNT(*) FROM ".USER_TOPTEN_TABLE);
			if ($rs->fields[0] > 100) {
				$dbconn->Execute("DELETE FROM ".USER_TOPTEN_TABLE." WHERE rating = ?", array($min));
			}
		}
	}
	//	Top10Update();
	return;
}


/*
function Top10Update(){
global $lang, $config, $config_index, $smarty, $dbconn, $user;

$rs = $dbconn->Execute("select a.id as id_user, avg(b.estimation) as rating, count(b.estimation) as votes, a.gender from ".USERS_TABLE." a, ".USER_RATING_TABLE." b
where a.id = b.id_user and a.status='1' and a.root_user='0' and a.guest_user='0'
group by b.id_user order by rating desc, votes desc limit 0,10");
if ($rs->RowCount()) {
$dbconn->Execute("delete from ".USER_TOPTEN_TABLE);
$i = 10;
while(!$rs->EOF){
$row = $rs->GetRowAssoc(false);
$rs_topten = $dbconn->Execute("insert into ".USER_TOPTEN_TABLE." (id_user, type_user, rating) values ('".$row["id_user"]."', ".$row["gender"].", '".$i."')");
$i--;
$rs->MoveNext();
}
}

return;
}
*/

function PostComment($id, $message)
{
	global $lang, $dbconn, $user;
	
	$message = stripn(FormFilter($message));
	
	$err = BadWordsCont($message, 5);
	if ($err) {
		return $err;
	}
	
	if (check_filter($message)) {
		return $lang['err']['info_finding_1'];
	}
	
	if (!strlen($message)) {
		$err = $lang['err']['empty_comment'];
		return $err;
	}
	
	$message = AddSmiles($message);
	
	$strSQL = 'INSERT INTO '.USER_COMMENT_TABLE.' SET id_user = ?, id_voter = ?, message = ?, comment_date = NOW()';
	$dbconn->Execute($strSQL, array($id, $user[ AUTH_ID_USER ], $message));
	return '';
}

function DeleteComment($cid)
{
	global $dbconn, $user;
	$dbconn->Execute('DELETE FROM '.USER_COMMENT_TABLE.' WHERE id = ? AND id_voter = ?', array($cid, $user[ AUTH_ID_USER ]));
	return '';
}

function AddSmiles($text)
{
	global $config;
	foreach ($config['smiles'] as $smiles) {
		$text = preg_replace('/'.$smiles['preg'].'/i', '<img src="'.$config['site_root'].$config['index_theme_path'].'/emoticons/'.$smiles['file'].'" alt="">', $text);
	}
	return $text;
}

function GetCountReferredFriends($id_user)
{
	global $dbconn;
	return $dbconn->GetOne('SELECT COUNT(DISTINCT id_user) FROM '.USER_REFER_TABLE.' WHERE id_refer = ?', array(intval($id_user)));
}


function GetUserReferCode($id_user)
{
	global $dbconn;
	$id_user = intval($id_user);
	if ($id_user == 0) {
		return false;
	}
	return $dbconn->GetOne('SELECT code FROM '.USER_REFER_CODE_TABLE.' WHERE id_user = ?', array($id_user));
}

function GetLangs()
{
	global $dbconn;
	
	$rs = $dbconn->Execute('SELECT id, code, name FROM '.LANGUAGE_TABLE.' WHERE visible = "1"');
	
	$i = 0;
	$lang_link = array();
	
	while (!$rs->EOF)
	{
		$lang_link[$i]['code'] = $rs->fields[1];
		$lang_link[$i]['name'] = ucfirst($rs->fields[2]);
		$query = $_SERVER['QUERY_STRING'];
		
		if ($query)
		{
			$query_arr = array();
			$new_query_arr = array();
			$query_arr = explode('&', $query);
			foreach ($query_arr as $v) {
				$s = explode('=', $v);
				if (trim($s[0] != 'language_code')) {
					$new_query_arr[] = $v;
				}
			}
			$query = implode('&', $new_query_arr);
		}
		if (strlen($query) > 0) {
			$query .= '&';
		}
		$lang_link[$i]['link'] = '?'.$query.'language_code='.$rs->fields[0];
		$rs->MoveNext();
		$i++;
	}
	return $lang_link;
}

function StripJS($str)
{
	$str = preg_replace("/<script(.*?)\>(.*?)\<\/script\>/is", '', $str);
	return $str;
}

function setViewHistory($file='')
{
	global $config;
	
	if ($file && empty($_SESSION['return_to_view']['type']))
	{
		$_SESSION['return_to_view']['type'] = 'after_payment';
		
		if ($_SERVER['QUERY_STRING']) {
			$_SERVER['QUERY_STRING'] = '?'.$_SERVER['QUERY_STRING'];
		}
		
		$_SESSION['return_to_view']['return_url'] = $config['server'].$config['site_root'].$file.$_SERVER['QUERY_STRING'];
		
		return;
	}
	
	$_SESSION['return_to_view']['_get'] = $_GET;
	$_SESSION['return_to_view']['get_str'] = $_SERVER['QUERY_STRING'];
}

function lastViewed()
{
	global $smarty, $dbconn, $config, $user;
	
	$clickable = $user[ AUTH_GUEST ] ? 0 : 1;
	
	if (!empty($_SESSION['return_to_view']['type']))
	{
		switch ($_SESSION['return_to_view']['type'])
		{
			case 'viewprofile':
				$id = isset($_SESSION['return_to_view']['_get']['id']) ? (int) $_SESSION['return_to_view']['_get']['id'] : 0;
				if ($id < 1) return false;
				$settings['icons_folder'] = GetSiteSettings('icons_folder');
				
				$strSQL =
					'SELECT ut.login, ut.icon_path, ut.big_icon_path, ut.date_birthday, ct.name as country, COUNT(upt.id) as photo_count
					   FROM '.USERS_TABLE.' ut
				  LEFT JOIN '.COUNTRY_SPR_TABLE.' ct ON ut.id_country = ct.id
				  LEFT JOIN '.USER_UPLOAD_TABLE.' upt ON ut.id = upt.id_user
					  WHERE ut.id = ? AND ut.status = "1" AND ut.root_user = "0"
					    AND ut.guest_user = "0" AND ut.icon_path != "" AND upt.upload_type = "f" AND upt.status = "1"
				   GROUP BY upt.id_user';
				
				$rs = $dbconn->Execute($strSQL, array($id));
				$row = $rs->GetRowAssoc(false);
				$row['img_path'] = $config['site_path'].$settings['icons_folder'].'/'.$row['big_icon_path'];
				$row['img_url'] = $config['server'].$config['site_root'].$settings['icons_folder'].'/'.$row['big_icon_path'];
				if (!file_exists($row['img_path'])) return false;
				$row['age'] = AgeFromBDate($row['date_birthday']);
				$row['type'] = $_SESSION['return_to_view']['type'];
				if ($clickable)
				{
					$row['view_link'] = 'viewprofile.php?'.$_SESSION['return_to_view']['get_str'].'&clear_return_toview=1';
				}
				$smarty->assign('viewed_info', $row);
			break;
			
			case 'gallary':
				$id = isset($_SESSION['return_to_view']['_get']['id']) ? (int) $_SESSION['return_to_view']['_get']['id'] : 0;
				if ($id < 1) return false;
				$settings['photos_folder'] = GetSiteSettings('photos_folder');
				
				$strSQL =
					'SELECT upload_path, user_comment
					   FROM '.USER_UPLOAD_TABLE.'
					  WHERE id = ? AND status = "1" AND allow = "1" AND is_adult != "1" AND upload_type = "f"';
					
				$rs = $dbconn->Execute($strSQL, array($id));
				$row = $rs->GetRowAssoc(false);
				$row['img_path'] = $config['site_path'].$settings['photos_folder'].'/thumb_'.$row['upload_path'];
				$row['img_url'] = $config['server'].$config['site_root'].$settings['photos_folder'].'/thumb_'.$row['upload_path'];
				if (!file_exists($row['img_path'])) return false;
				if ($clickable){
					$row['view_link'] = '#';
					$row['onclick'] =
						"window.open('gallary.php?".$_SESSION['return_to_view']['get_str']."&clear_return_toview=1', 'view', 'height=400, resizable=yes, scrollbars=yes, width=400, menubar=no,status=no, left=200, top=20');
						location.href='gallary.php';";
				}
				$smarty->assign('viewed_info', $row);
			break;
		}
	}
}

function redirectToViewed()
{
	global $user;
	
	if (!empty($_SESSION['return_to_view']['type']))
	{
		$return_to_view = $_SESSION['return_to_view'];
		
		unset($_SESSION['return_to_view']);
		
		switch ($return_to_view['type'])
		{
			case 'viewprofile':
			
				if (!empty($return_to_view['_get']['id']) && $user[ AUTH_ID_USER ] != $return_to_view['_get']['id'])
				{
					echo "<script type='text/javascript'>location.href='viewprofile.php?".$return_to_view['get_str']."&clear_return_toview=1'; </script>";
					exit;
				}
				else
				{
					echo "<script type='text/javascript'>location.href='homepage.php';</script>";
					exit;
				}
			
			break;
			
			case 'gallary':
			
				echo "<script type='text/javascript'>
				window.open('gallary.php?".$return_to_view['get_str']."&clear_return_toview=1', 'view', 'height=400, resizable=yes, scrollbars=yes, width=400, menubar=no,status=no, left=200, top=20');
				location.href='gallary.php'</script>";
				exit;
			
			break;
			
			case 'after_payment':
				
				echo "<script type='text/javascript'>location.href='".$return_to_view['return_url']."'</script>";
				exit;
				
			break;
		}
	}
}

function cleanMailbox()
{
	global $dbconn, $config;
	
	$dbconn->Execute('UPDATE '.MAILBOX_TABLE.' SET deleted_from = "1" WHERE UNIX_TIMESTAMP(kill_date_from) < '.time());
	$dbconn->Execute('UPDATE '.MAILBOX_TABLE.' SET deleted_to = "1" WHERE UNIX_TIMESTAMP(kill_date_to) < '.time());
		
	$last_mailbox_cleaning = intval(GetSiteSettings('last_mailbox_cleaning'));
	
	if ($last_mailbox_cleaning < time()-1*24*60*60 || 1)
	{
		$dbconn->Execute('UPDATE '.SETTINGS_TABLE.' SET value = "'.time().'" WHERE name = "last_mailbox_cleaning"');
		
		//delete attachements
		$attaches_folder = GetSiteSettings('attaches_folder');
		
		$strSQL =
			'SELECT b.id, b.attach_name
			   FROM '.MAILBOX_TABLE.' a
		 INNER JOIN '.MAILBOX_ATTACHES_TABLE.' b ON a.id = b.id_mail
			  WHERE a.deleted_from = "1" AND a.deleted_to = "1"';
		
		$rs = $dbconn->Execute($strSQL);
		
		$attach_ids = array();
		
		while (!$rs->EOF) {
			$attach_ids[] = $rs->fields[0];
			$attach_name = $rs->fields[1];
			$attach_path = $config['site_path'].$config['site_root'].$attaches_folder.'/'.$attach_name;
			if (file_exists($attach_path)) unlink($attach_path);
			$rs->MoveNext();
		}
		
		//delete attaches records
		if (!empty($attach_ids))
		{
			$attach_ids_str = implode(',', $attach_ids);
			$dbconn->Execute('DELETE FROM '.MAILBOX_ATTACHES_TABLE.' WHERE id IN ('.$attach_ids_str.')');
		}
		
		// delete mails
		$dbconn->Execute('DELETE FROM '.MAILBOX_TABLE.' WHERE deleted_from = "1" AND deleted_to = "1"');
	}
	else
	{
		return;
	}
}

//Start RASHMI
function sendnotmail($to, $subject, $txt)
{
	global $lang;
	
	$PHPmailer = new PHPMailer();
	
	$PHPmailer->CharSet = "UTF-8";
	
	$PHPmailer->From = $lang["err"]["from_notification_email"];
	$PHPmailer->FromName = $lang["err"]["from_notification_text"];
	$PHPmailer->AddAddress($to);
	$PHPmailer->IsHTML(true);
	
	$PHPmailer->Subject = $subject;
	$PHPmailer->Body = $txt;
	
	if (!$PHPmailer->Send())
	{
		$err = $lang['err']['mail_error'].' ('.$PHPmailer->ErrorInfo.')';
	}
	
	//sleep(1);
	$PHPmailer->ClearAddresses();
	$PHPmailer->ClearAttachments();
	return $err;
}

function sendEcardmail($email, $login, $sender_id, $frmname)
{
	// this one is not using the email standard and needs to be converted
	
	global $lang, $dbconn, $user, $config;
	$site_url = $config["server"].$config["site_root"];
	
	$rs = $dbconn->Execute("SELECT * FROM ".USERS_TABLE." WHERE email = ?", array($email));
	$i = 0;
	$spr_arr = array();
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$to[0]["email"] = $row["email"];
		$to= $to[0]["email"]; 
		$fname[0]["fname"] = $row["login"];
		$to_photo[0]["big_icon_path"]=  $row["big_icon_path"];
		$uname[0]["login"]=  $row["login"];
		$rs->MoveNext();
		$i++;
	}
	
	$rs = $dbconn->Execute('SELECT * FROM '.USERS_TABLE.' WHERE id = ?', array($user[ AUTH_ID_USER ]));
	$i=0;
	$spr_arr = array();
	while (!$rs->EOF) {
		$row2 = $rs->GetRowAssoc(false);
		$User_profile_id[0]["id"]		= $row2["id"];
		$sender_email[0]["email"]		= $row2["email"];
		$fname_user[0]["login"]			= $row2["login"]; 
		$fname_user						= $fname_user[0]["login"];
		$from_photo[0]["big_icon_path"]	= $row2["big_icon_path"];    
		$rs->MoveNext();
		$i++;
	}
	
	$subject = $lang["err"]["email_mail_subject"];
	
	$header = file_get_contents('./notification_html/header.html');
	$header_image = $config['index_theme_path'].'/images/top-img.jpg';
	$header = str_replace("[[HEADER_IMAGE]]",  $header_image  ,  $header); 
	
	$fname_user= str_replace("//", " ", $fname_user);
	
	$body = file_get_contents('./notification_html/send_ecard_body.html');
	$body = str_replace("[[ECARD_FIRSTNAME]]",       ucfirst($fname[0]["fname"]) ,  $body);
	$body = str_replace("[[ECARD_SENDER_FIRSTNAME]]",  ucfirst($fname_user) ,  $body);
	$body = str_replace("[[TO_CARD_PHOTO]]",        ($site_url.'/uploades/icons/'.$to_photo[0]["big_icon_path"]) ,  $body);
	$body = str_replace("[[FROM_CARD_PHOTO]]",      ($site_url.'/uploades/icons/'.$from_photo[0]["big_icon_path"]) ,  $body);
	$body = str_replace("[[USER_NAME]]",            ($uname[0]["login"]) ,  $body);
	$body = str_replace("[[LINK_REDIRECT]]" ,   $site_url.'/viewprofile.php?id='.$User_profile_id[0]["id"],  $body);
	$body = str_replace("[[LINK]]" ,   $site_url.'/viewprofile.php?<br>id='.$User_profile_id[0]["id"],  $body);
	$body = str_replace("[[FROM]]" ,    'Nathamon' ,  $body);
	
	$url_registration		= $site_url.'/index.php';
	$url_platinum			= $site_url.'/platinum_match.php';
	$url_dating_events		= $site_url.'/dating_events.php';
	$url_member4			= $site_url.'';
	$url_help				= $site_url.'/help.php';
	$url_help_trial_member	= $site_url.'/help.php?sel=list_item&id=42';
	$url_help_reg_member	= $site_url.'/help.php?sel=list_item&id=42';
	$url_help_plat_member	= $site_url.'/help.php?sel=list_item&id=42';
	$url_help_tld_event		= $site_url.'/help.php?sel=list_item&id=42';
	$url_help_newsletter	= $site_url.'/help.php?sel=list_item&id=42';
	$event_url				= $site_url.'/request_info.php';
	$url_member_contact		= $site_url.'/contact.php';
	$url_login				= $site_url.'/index.php?sel=login';
	
	$login_image			= $config['index_theme_path'].'/images/login.jpg';
	
	$footer					= file_get_contents('./notification_html/footer.html');
	$footer					= str_replace("[[USER_NAME]]",        $uname[0]["login"], $footer);
	$footer					= str_replace("[[LOGIN_IMAGE]]",      $login_image, $footer);
	$footer					= str_replace("[[SITE_LOGIN]]",       $url_login, $footer);
	$footer					= str_replace("[[URL_TRIAL_MEMBER]]", $url_help_trial_member, $footer);
	$footer					= str_replace("[[REGULARL_MEMBER]]",  $url_help_reg_member, $footer);
	$footer					= str_replace("[[PLATENIUM_MEMBER]]", $url_help_plat_member, $footer);
	$footer					= str_replace("[[PLATENIUM_PLUS]]",   $url_help_tld_event, $footer);
	$footer					= str_replace("[[NEW_LETTER]]",       $url_help_newsletter, $footer);
	$footer					= str_replace("[[FAQ]]",              $url_help, $footer);
	$footer					= str_replace("[[CONTACT_US]]",       $url_member_contact, $footer);
	$footer					= str_replace("[[HEADER_IMAGE]]",     $header_image, $footer);
	$footer					= str_replace("[[THAI_LADY_EVENT]]",  $event_url, $footer);
	
	$body = $header . $body . $footer;
	
	sendnotmail($to, $subject, $body);
}

function visitedMeCount($id_user, $id_group)
{
	global $dbconn;
	$privacy_join = $privacy_where = $visible_where = '';
	if (VISITED_ME_PRIVACY) {
		$privacy_join = 'LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id';
		$privacy_where = sql_privacy_where($id_group);
	}
	if (VISITED_ME_VISIBLE) {
		$visible_where = 'AND u.visible = "1"';
	}
	$strSQL =
		'SELECT COUNT(DISTINCT u.id)
		   FROM '.USERS_TABLE.' u
	 INNER JOIN '.PROFILE_VISIT_TABLE.' l ON l.id_visiter = u.id
				'.$privacy_join.'
		  WHERE l.id_user = ? AND l.id_visiter <> ?
			AND u.status = "1" AND u.root_user = "0" AND u.guest_user = "0" '.$visible_where.' '.$privacy_where;
	$num_records = (int) $dbconn->getOne($strSQL, array($id_user, $id_user));
	return $num_records;
}

function visitedThemCount($id_user, $id_group)
{
	global $dbconn;
	$privacy_join = $privacy_where = $visible_where = '';
	if (VISITED_THEM_PRIVACY) {
		$privacy_join = 'LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id';
		$privacy_where = sql_privacy_where($id_group);
	}
	if (VISITED_THEM_VISIBLE) {
		$visible_where = 'AND u.visible = "1"';
	}
	$strSQL =
		'SELECT COUNT(DISTINCT u.id)
		   FROM '.USERS_TABLE.' u
	 INNER JOIN '.PROFILE_VISIT_TABLE.' l ON l.id_user = u.id
				'.$privacy_join.'
		  WHERE l.id_visiter = ? AND l.id_user <> ?
			AND u.status = "1" AND u.root_user = "0" AND u.guest_user = "0" '.$visible_where.' '.$privacy_where;
	$num_records = (int) $dbconn->getOne($strSQL, array($id_user, $id_user));
	return $num_records;
}

function kissedMeCount($id_user, $id_group)
{
	global $dbconn;
	$privacy_join = $privacy_where = $visible_where = '';
	if (KISSED_ME_PRIVACY) {
		$privacy_join = 'LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id';
		$privacy_where = sql_privacy_where($id_group);
	}
	if (KISSED_ME_VISIBLE) {
		$visible_where = 'AND u.visible = "1"';
	}
	$strSQL =
		'SELECT COUNT(DISTINCT u.id)
		   FROM '.USERS_TABLE.' u
	 INNER JOIN '.KISSLIST_TABLE.' l ON l.id_from = u.id
				'.$privacy_join.'
		  WHERE l.id_to = ?
			AND u.status = "1" '.$visible_where.' '.$privacy_where;
	$num_records = (int) $dbconn->getOne($strSQL, array($id_user));
	return $num_records;
}

function kissedThemCount($id_user, $id_group)
{
	global $dbconn;
	$privacy_join = $privacy_where = $visible_where = '';
	if (KISSED_THEM_PRIVACY) {
		$privacy_join = 'LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id';
		$privacy_where = sql_privacy_where($id_group);
	}
	if (KISSED_THEM_VISIBLE) {
		$visible_where = 'AND u.visible = "1"';
	}
	$strSQL =
		'SELECT COUNT(DISTINCT u.id)
		   FROM '.USERS_TABLE.' u
	 INNER JOIN '.KISSLIST_TABLE.' l ON l.id_to = u.id
				'.$privacy_join.'
		  WHERE l.id_from = ?
			AND u.status = "1" '.$visible_where.' '.$privacy_where;
	$num_records = (int) $dbconn->getOne($strSQL, array($id_user));
	return $num_records;
}

function emailedMeCount($id_user, $id_group)
{
	global $dbconn;
	$privacy_join = $privacy_where = $visible_where = '';
	if (EMAILED_ME_PRIVACY) {
		$privacy_join = 'LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id';
		$privacy_where = sql_privacy_where($id_group);
	}
	if (EMAILED_ME_VISIBLE) {
		$visible_where = 'AND u.visible = "1"';
	}
	$strSQL =
		'SELECT COUNT(DISTINCT u.id)
		   FROM '.USERS_TABLE.' u
	 INNER JOIN '.MAILBOX_TABLE.' l ON l.id_from = u.id
				'.$privacy_join.'
		  WHERE l.id_to = ? AND l.deleted_to = "0"
			AND u.status = "1" AND u.root_user = "0" AND u.guest_user = "0" '.$visible_where.' '.$privacy_where;
	$num_records = (int) $dbconn->getOne($strSQL, array($id_user));
	return $num_records;
}

function emailedThemCount($id_user, $id_group)
{
	global $dbconn;
	$privacy_join = $privacy_where = $visible_where = '';
	if (EMAILED_THEM_PRIVACY) {
		$privacy_join = 'LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id';
		$privacy_where = sql_privacy_where($id_group);
	}
	if (EMAILED_THEM_VISIBLE) {
		$visible_where = 'AND u.visible = "1"';
	}
	$strSQL =
		'SELECT COUNT(DISTINCT u.id)
		   FROM '.USERS_TABLE.' u
	 INNER JOIN '.MAILBOX_TABLE.' l ON l.id_to = u.id
				'.$privacy_join.'
		  WHERE l.id_from = ? AND l.deleted_to = "0"
			AND u.status = "1" AND u.root_user = "0" AND u.guest_user = "0" '.$visible_where.' '.$privacy_where;
	$num_records = (int) $dbconn->getOne($strSQL, array($id_user));
	return $num_records;
}

function ecardsMeCount($id_user, $id_group)
{
	global $dbconn;
	$privacy_join = $privacy_where = $visible_where = '';
	if (ECARDS_ME_PRIVACY) {
		$privacy_join = 'LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id';
		$privacy_where = sql_privacy_where($id_group);
	}
	if (ECARDS_ME_VISIBLE) {
		$visible_where = 'AND u.visible = "1"';
	}
	$strSQL =
		'SELECT COUNT(DISTINCT(u.id))
		   FROM '.USERS_TABLE.' u
	 INNER JOIN '.ECARDS_ORDERS_TABLE.' l ON l.id_user = u.id
				'.$privacy_join.'
		  WHERE l.id_user_to = ?
			AND l.status IN ("approved", "readed")
			AND u.status = "1" AND u.root_user = "0" AND u.guest_user = "0" '.$visible_where.' '.$privacy_where;
	$num_records = (int) $dbconn->getOne($strSQL, array($id_user));
	return $num_records;
}

function ecardsThemCount($id_user, $id_group)
{
	global $dbconn;
	$privacy_join = $privacy_where = $visible_where = '';
	if (ECARDS_THEM_PRIVACY) {
		$privacy_join = 'LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id';
		$privacy_where = sql_privacy_where($id_group);
	}
	if (ECARDS_THEM_VISIBLE) {
		$visible_where = 'AND u.visible = "1"';
	}
	$strSQL =
		'SELECT COUNT(DISTINCT(u.id))
		   FROM '.USERS_TABLE.' u
	 INNER JOIN '.ECARDS_ORDERS_TABLE.' l ON l.id_user_to = u.id
				'.$privacy_join.'
		  WHERE l.id_user = ?
			AND l.status IN ("approved", "readed")
			AND u.status = "1" AND u.root_user = "0" AND u.guest_user = "0" '.$visible_where.' '.$privacy_where;
	$num_records = (int) $dbconn->getOne($strSQL, array($id_user));
	return $num_records;
}

function invitedMeConnectCount($id_user, $id_group)
{
	global $dbconn;
	$privacy_join = $privacy_where = $visible_where = '';
	if (INVITED_ME_CONNECT_PRIVACY) {
		$privacy_join = 'LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id';
		$privacy_where = sql_privacy_where($id_group);
	}
	if (INVITED_ME_CONNECT_VISIBLE) {
		$visible_where = 'AND u.visible = "1"';
	}
	$strSQL =
		'SELECT COUNT(*)
		   FROM '.USERS_TABLE.' u
	 INNER JOIN '.CONNECTIONS_TABLE.' l ON l.id_user = u.id
				'.$privacy_join.'
		  WHERE l.id_friend = ? AND l.status = "0"
			AND u.status = "1" AND u.root_user = "0" AND u.guest_user = "0" '.$visible_where.' '.$privacy_where;
	$num_records = (int) $dbconn->getOne($strSQL, array($id_user));
	return $num_records;
}

function invitedThemConnectCount($id_user, $id_group)
{
	global $dbconn;
	$privacy_join = $privacy_where = $visible_where = '';
	if (INVITED_THEM_CONNECT_PRIVACY) {
		$privacy_join = 'LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id';
		$privacy_where = sql_privacy_where($id_group);
	}
	if (INVITED_THEM_CONNECT_VISIBLE) {
		$visible_where = 'AND u.visible = "1"';
	}
	$strSQL =
		'SELECT COUNT(*)
		   FROM '.USERS_TABLE.' u
	 INNER JOIN '.CONNECTIONS_TABLE.' l ON l.id_friend = u.id
				'.$privacy_join.'
		  WHERE l.id_user = ? AND l.status = "0"
			AND u.status = "1" AND u.root_user = "0" AND u.guest_user = "0" '.$visible_where.' '.$privacy_where;
	$num_records = (int) $dbconn->getOne($strSQL, array($id_user));
	return $num_records;
}

function connectedCount($id_user)
{
	global $dbconn;
	$count1 = (int) $dbconn->getOne(
		'SELECT COUNT(*)
		   FROM '.CONNECTIONS_TABLE.' a
	 INNER JOIN '.USERS_TABLE.' b ON b.id = a.id_friend
		  WHERE a.id_user = ? AND a.status = "1" AND b.status = "1"',
		array($id_user));
	
	$count2 = (int) $dbconn->getOne(
		'SELECT COUNT(*)
		   FROM '.CONNECTIONS_TABLE.' a
	 INNER JOIN '.USERS_TABLE.' b ON b.id = a.id_user
		  WHERE a.id_friend = ? AND a.status = "1" AND b.status = "1"',
		array($id_user));
	
	$total = $count1 + $count2;
	return $total;
}

function hotlistCount($id_user, $id_group)
{
	global $dbconn;
	$privacy_join = $privacy_where = $visible_where = '';
	if (HOTLIST_PRIVACY) {
		$privacy_join = 'LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id';
		$privacy_where = sql_privacy_where($id_group);
	}
	if (HOTLIST_VISIBLE) {
		$visible_where = 'AND u.visible = "1"';
	}
	$strSQL =
		'SELECT COUNT(DISTINCT(u.id))
		   FROM '.USERS_TABLE.' u
	 INNER JOIN '.HOTLIST_TABLE.' l ON l.id_friend = u.id
				'.$privacy_join.'
		  WHERE l.id_user = ?
		    AND u.status = "1" '.$visible_where.' '.$privacy_where;
	$num_records = (int) $dbconn->getOne($strSQL, array($id_user));
	return $num_records;
}

function theirHotlistCount($id_user, $id_group)
{
	global $dbconn;
	$privacy_join = $privacy_where = $visible_where = '';
	if (HOTLIST_PRIVACY) {
		$privacy_join = 'LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id';
		$privacy_where = sql_privacy_where($id_group);
	}
	if (HOTLIST_VISIBLE) {
		$visible_where = 'AND u.visible = "1"';
	}
	
	$strSQL =
		'SELECT COUNT(DISTINCT(u.id))
		   FROM '.USERS_TABLE.' u
	 INNER JOIN '.HOTLIST_TABLE.' h ON h.id_user = u.id
				'.$privacy_join.'
		  WHERE h.id_friend = ?
			AND u.status = "1" '.$visible_where.' '.$privacy_where;
	
	$num_records = (int) $dbconn->getOne($strSQL, array($id_user));
	return $num_records;
}

function blacklistCount($id_user, $id_group)
{
	global $dbconn;
	$privacy_join = $privacy_where = $visible_where = '';
	if (BLACKLIST_PRIVACY) {
		$privacy_join = 'LEFT JOIN '.USER_PRIVACY_SETTINGS.' up ON up.id_user = u.id';
		$privacy_where = sql_privacy_where($id_group);
	}
	if (BLACKLIST_VISIBLE) {
		$visible_where = 'AND u.visible = "1"';
	}
	$strSQL =
		'SELECT COUNT(DISTINCT(u.id))
		   FROM '.USERS_TABLE.' u
	 INNER JOIN '.BLACKLIST_TABLE.' l ON l.id_enemy = u.id
				'.$privacy_join.'
		  WHERE l.id_user = ?
			AND u.status = "1" '.$visible_where.' '.$privacy_where;
	$num_records = (int) $dbconn->getOne($strSQL, array($id_user));
	return $num_records;
}

function getUserGroups($id_user, $platinum_applied = false)
{
	global $dbconn, $lang;
	$groups = '';
	$strSQL =
		'SELECT a.id, a.name
		   FROM '.USER_GROUP_TABLE.' b
	 INNER JOIN '.GROUPS_TABLE.' a ON a.id = b.id_group
		  WHERE b.id_user = ?';
	$rs = $dbconn->Execute($strSQL, array($id_user));
	while (!$rs->EOF) {
		if ($groups) {
			$groups .= ',';
		}
		// Ralf to Narendra:
		// installment groups need to be displayes as "Platinum Lady"
		
		// if user is On Hold show them as Inactive
		switch ($rs->fields[0]) {
			case MM_INACT_TRIAL_GUY_ID:
				$groups .= $lang['users']['list_inact_trial_guy'];
			break;
			case MM_INACT_TRIAL_LADY_ID:
				$groups .= $lang['users']['list_inact_trial_lady'];
			break;
			case MM_INACT_REGULAR_GUY_ID:
				$groups .= $lang['users']['list_inact_reg_guy'];
			break;
			case MM_INACT_REGULAR_LADY_ID:
				$groups .= $lang['users']['list_inact_reg_lady'];
			break;
			case MM_INACT_PLATINUM_GUY_ID:
				$groups .= $lang['users']['list_inact_plat_guy'];
			break;
			case MM_INACT_PLATINUM_LADY_ID:
				$groups .= $lang['users']['list_inact_plat_lady'];
			break;
			case MM_INACT_ELITE_GUY_ID:
				$groups .= $lang['users']['list_inact_elite_guy'];
			break;
			case MM_INACT_ELITE_LADY_ID:
				$groups .= $lang['users']['list_inact_elite_lady'];
			break;
			case MM_PLATINUM_LADY_FIRST_INS_ID:
			case MM_PLATINUM_LADY_SECOND_INS_ID:
				$groups .= 'Platinum Lady';
			break;
			
			default:
				$groups .= $rs->fields[1];
		}
		$rs->MoveNext();
	}
	return $groups;
}

function getUserIsOnline($id_user)
{
	global $dbconn;
	$check = $dbconn->getOne('SELECT 1 FROM '.ACTIVE_SESSIONS_TABLE.' WHERE id_user = ?', array($id_user));
	return (empty($check) ? 0 : 1);
}

function isInMyHotlist($id_me, $id_other)
{
	global $dbconn;
	$check = $dbconn->getOne('SELECT 1 FROM '.HOTLIST_TABLE.' WHERE id_user = ? AND id_friend = ?', array($id_me, $id_other));
	return (empty($check) ? 0 : 1);
}

function isInMyBlacklist($id_me, $id_other)
{
	global $dbconn;
	$check = $dbconn->getOne('SELECT 1 FROM '.BLACKLIST_TABLE.' WHERE id_user = ? AND id_enemy = ?', array($id_me, $id_other));
	return (empty($check) ? 0 : 1);
}

function isConnected($id_user1, $id_user2)
{
	global $dbconn;
	if (empty($id_user1) || empty($id_user2)) return false;
	$check = $dbconn->GetOne(
		'SELECT 1
		   FROM '.CONNECTIONS_TABLE.'
		  WHERE ((id_user = ? AND id_friend = ?) OR (id_user = ? AND id_friend = ?)) AND status = "1"',
		array($id_user1, $id_user2, $id_user2, $id_user1));
	return !empty($check);
}

function isConnectedOrInvited($id_me, $id_other)
{
	global $dbconn;
	$check = $dbconn->getOne(
		'SELECT 1
		   FROM '.CONNECTIONS_TABLE.'
		  WHERE id_user = ? AND id_friend = ? OR id_user = ? AND id_friend = ?',
		array($id_me, $id_other, $id_other, $id_me));
	return (empty($check) ? 0 : 1);
}

function isConnectionInvitationSent($id_user1, $id_user2)
{
	global $dbconn;
	if (empty($id_user1) || empty($id_user2)) return false;
	$check = $dbconn->GetOne(
		'SELECT 1 FROM '.CONNECTIONS_TABLE.' WHERE id_user = ? AND id_friend = ? AND status = "0"',
		array($id_user1, $id_user2));
	return !empty($check);
}

function isConnectionInvitationReceived($id_user1, $id_user2)
{
	global $dbconn;
	if (empty($id_user1) || empty($id_user2)) return false;
	$check = $dbconn->GetOne(
		'SELECT 1 FROM '.CONNECTIONS_TABLE.' WHERE id_user = ? AND id_friend = ? AND status = "0"',
		array($id_user2, $id_user1));
	return !empty($check);
}

function getConnectedStatus($id_me, $id_you)
{
	global $dbconn;
	if (empty($id_me) || empty($id_you)) {
		return CS_NOTHING;
	}
	$rs = $dbconn->Execute(
		'SELECT id_user, id_friend, status
		   FROM '.CONNECTIONS_TABLE.'
		  WHERE id_user = ? AND id_friend = ? OR id_user = ? AND id_friend = ?
	   ORDER BY status DESC',
		array($id_me, $id_you, $id_you, $id_me));
	if ($rs->EOF) {
		return CS_NOTHING;
	}
	$row = $rs->GetRowAssoc(false);
	if ($row['status'] == '1') {
		return CS_CONNECTED;
	}
	if ($row['id_user'] == $id_me && $row['id_friend'] == $id_you) {
		return CS_SENT;
	}
	return CS_RECEIVED;
}

function publicPhotoCountAlbums($id_user)
{
	global $dbconn;
	$num_records = (int) $dbconn->getOne(
		'SELECT COUNT(DISTINCT upload_path)
		   FROM '.USER_UPLOAD_TABLE.'
		  WHERE id_user = ? AND upload_type="f" AND status="1" AND allow IN ("1", "2")',
		array($id_user));
	return $num_records;
}

function publicAudioCountAlbums($id_user)
{
	global $dbconn;
	$num_records = (int) $dbconn->getOne(
		'SELECT COUNT(DISTINCT upload_path)
		   FROM '.USER_UPLOAD_TABLE.'
		  WHERE id_user = ? AND upload_type="a" AND status="1" AND allow IN ("1", "2")',
		array($id_user));
	return $num_records;
}

function publicVideoCountAlbums($id_user)
{
	global $dbconn;
	$num_records = (int) $dbconn->getOne(
		'SELECT COUNT(DISTINCT upload_path)
		   FROM '.USER_UPLOAD_TABLE.'
		  WHERE id_user = ? AND upload_type="v" AND status="1" AND allow IN ("1", "2")',
		array($id_user));
	return $num_records;
}

function html_country_select($id_country)
{
	global $dbconn;
	$rs = $dbconn->Execute('SELECT id, name FROM '.COUNTRY_SPR_TABLE.' ORDER BY name');
	$i = 0;
	$arr = array();
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$arr[$i]['id'] = $row['id'];
		$arr[$i]['name'] = stripslashes($row['name']);
		if ($row['id'] == $id_country) {
			$arr[$i]['sel'] = 1;
		}
		$rs->MoveNext();
		$i++;
	}
	return $arr;
}

function html_region_select($id_country, $id_region)
{
	global $dbconn;
	$rs = $dbconn->Execute('SELECT id, name FROM '.REGION_SPR_TABLE.' WHERE id_country = ? ORDER BY name', array($id_country));
	$i = 0;
	$arr = array();
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$arr[$i]['id'] = $row['id'];
		$arr[$i]['name'] = stripslashes($row['name']);
		if ($row['id'] == $id_region) {
			$arr[$i]['sel'] = 1;
		}
		$rs->MoveNext();
		$i++;
	}
	return $arr;
}

function html_city_select($id_region, $id_city)
{
	global $dbconn;
	$rs = $dbconn->Execute('SELECT id, name FROM '.CITY_SPR_TABLE.' WHERE id_region = ? ORDER BY name', array($id_region));
	$i = 0;
	$arr = array();
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$arr[$i]['id'] = $row['id'];
		$arr[$i]['name'] = stripslashes($row['name']);
		if ($row['id'] == $id_city) {
			$arr[$i]['sel'] = 1;
		}
		$rs->MoveNext();
		$i++;
	}
	return $arr;
}

function html_distance_select($within = null, $distance = null)
{
	global $dbconn, $lang;
	$rs = $dbconn->Execute('SELECT id, name, type FROM '.DISTANCE_SPR_TABLE.' ORDER BY id, name DESC');
	$i = 0;
	$arr = array();
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$arr[$i]['id'] = $row['id'];
		$arr[$i]['name'] = $row['name'];
		$arr[$i]['type'] = ($row['type'] == 'mile') ? $lang['distance']['mile'] : $lang['distance']['km'];
		if ($within == '1') {
			if ($arr[$i]['id'] == $distance) {
				$arr[$i]['sel'] = 1;
			}
		}
		$rs->MoveNext();
		$i++;
	}
	return $arr;
}

function html_relationship_select($arr_relationship)
{
	global $dbconn;
	
	$ml = new MultiLang();
	
	$strSQL =
		'SELECT a.id, b.'.$ml->DefaultFieldName().' AS name
		   FROM '.RELATION_SPR_TABLE.' a
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' b ON b.table_key = "'.$ml->TableKey(RELATION_SPR_TABLE).'" AND b.id_reference = a.id
		  ORDER BY a.sorter';
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$arr = array();
	
	while (!$rs ->EOF) {
		$row = $rs->GetRowAssoc(false);
		$arr[$i]['id'] = $row['id'];
		$arr[$i]['name'] = $row['name'];
		if (is_array($arr_relationship) && in_array($row['id'], $arr_relationship)) {
			$arr[$i]['sel'] = 1;
		} else {
			$arr[$i]['sel'] = 0;
		}
		$rs->MoveNext();
		$i++;
	}
	
	return $arr;
}

function html_tag_select($file_name)
{
	global $dbconn;
	$rs = $dbconn->Execute('SELECT tag, COUNT(id) AS tag_count FROM '.TAGS_TABLE.' GROUP BY tag ORDER BY tag');
	$i = 0;
	$arr = array();
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$arr[$i]['tag'] = stripslashes($row['tag']);
		$arr[$i]['count'] = (int) $row['tag_count'];
		$arr[$i]['size'] = floor($arr[$i]['count'] / 5) + 9;
		$arr[$i]['searchlink'] = $file_name.'?sel=search_tag&amp;tag='.$arr[$i]['tag'];
		$rs->MoveNext();
		$i++;
	}
	return $arr;
}

function sql_privacy_where($id_group)
{
	switch ($id_group) {
		case MM_TRIAL_GUY_ID:
			return ' AND (up.visible_guy = "1" OR up.vis_guy_1 = "1") ';
		case MM_REGULAR_GUY_ID:
			return ' AND (up.visible_guy = "1" OR up.vis_guy_2 = "1") ';
		case MM_PLATINUM_GUY_ID:
			return ' AND (up.visible_guy = "1" OR up.vis_guy_3 = "1") ';
		case MM_ELITE_GUY_ID:
			return ' AND (up.visible_guy = "1" OR up.vis_guy_4 = "1") ';
		case MM_TRIAL_LADY_ID:
			return ' AND (up.visible_lady = "1" OR up.vis_lady_1 = "1") ';
		case MM_REGULAR_LADY_ID:
			return ' AND (up.visible_lady = "1" OR up.vis_lady_2 = "1") ';
		case MM_PLATINUM_LADY_ID:
			return ' AND (up.visible_lady = "1" OR up.vis_lady_3 = "1") ';
		default:
			return '';
	}
}

function GetHowItWorks()
{
	global $dbconn, $smarty, $user;

	$strSQL  = 'SELECT id, title, title_t, description, description_t, video, video_t FROM '.HOW_WORKS_INFO_TABLE.' WHERE status="1" ORDER BY sorter ASC';
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$works = array();
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$works[$i]['id']			= $row['id'];
		$works[$i]['title']			= stripslashes($row['title']);
		$works[$i]['title_t']		= stripslashes($row['title_t']);
		$works[$i]['description']	= stripslashes($row['description']);
		$works[$i]['description_t']	= stripslashes($row['description_t']);
		//$works[$i]['video']		= stripslashes($row['video']);
		$works[$i]['video']			= strlen($row['video']) ? 1 : 0;
		$works[$i]['video_t']		= strlen($row['video_t']) ? 1 : 0;
		$rs->MoveNext();
		$i++;
	}
	$how_works_lang = intval($user[AUTH_GENDER]);
	$smarty->assign('how_works_lang', $how_works_lang);
	return $works;
}

function GetHowItWorksVideo($id)
{
	global $dbconn, $smarty, $user;

	$rs = $dbconn->Execute('SELECT title, title_t, video, video_t FROM '.HOW_WORKS_INFO_TABLE.' WHERE status="1" AND id = ?', array($id));
	$row = $rs->GetRowAssoc(false);
	$data = array();
	$data['title']		= stripslashes($row['title']);
	$data['title_t']	= stripslashes($row['title_t']);
	$data['video']		= stripslashes($row['video']);
	$data['video_t']	= stripslashes($row['video_t']);
	
	$how_works_lang = intval($user[AUTH_GENDER]);
	$smarty->assign('how_works_lang', $how_works_lang);
	return $data;
}

// RS: splitting phone number into contry-code, area-code and number
function split_phone($phone)
{
	// replace '(' with white space for parsing area codes like (123)
	$phone = trim(str_replace('(', ' ', $phone));
	// remove leading '+' if needed
	if (substr($phone, 0, 1) == '+') {
		$phone = trim(substr($phone, 1));
	}
	// split by delimiter
	$j = 0;
	$in_digits = false;
	$country = '+';
	$area = $number = '';
	for ($i = 0; $i < strlen($phone); $i++) {
		$ch = substr($phone, $i, 1);
		if (strpos('0123456789', $ch) !== false || $j >= 2) {
			if ($j == 0) {
				$country .= $ch;
			} elseif ($j == 1) {
				$area .= $ch;
			} else {
				$number .= $ch;
			}
			$in_digits = true;
		} else {
			if ($in_digits) {
				$j++;
			}
			$in_digits = false;
		}
	}
	// fill empty area code and number
	if (!strlen($area) && !strlen($phone)) {
		$split = $country;
		$country = substr($split, 0, 3);
		$area = substr($split, 3, 4);
		$number = substr($split, 7);
	} elseif (!strlen($number)) {
		$split = $area;
		$area = substr($split, 0, 4);
		$phone = substr($split, 4);
	}
	// correct too many digits in country code (max 4 digits plus leading +)
	if (strlen($country) > 5) {
		$split = $country;
		$country = substr($split, 0, 5);
		$area = substr($split, 5) . $area;
	}
	// correct too many digits in area code (max 4 digits)
	if (strlen($area) > 4) {
		$split = $area;
		$area = substr($split, 0, 4);
		$number = substr($split, 4) . $number;
	}
	return array($country, $area, $number);
}

// RS: added for testing purposes
function strip_magic_quotes_gpc()
{
	if (get_magic_quotes_gpc()) {
		function array_stripslashes(&$item, $key) {
			$item = stripslashes($item);
		}
		if (!empty($_GET)) {
			array_walk_recursive($_GET, 'array_stripslashes');
		}
		if (!empty($_POST)) {
			array_walk_recursive($_POST, 'array_stripslashes');
		}
		if (!empty($_REQUEST)) {
			array_walk_recursive($_POST, 'array_stripslashes');
		}
	}
}

// RS: moved from functions_user.php
function GetDaySelect($day_active)
{
	$day = array();
	for ($i = 0; $i < 31; $i++) {
		$day[$i]['value'] = $day[$i]['name'] = $i+1;
		if (intval($day_active) == $i+1) {
			$day[$i]['sel'] = 1;
		} else {
			$day[$i]['sel'] = 0;
		}
	}
	return $day;
}


// RS: moved from functions_user.php
function GetMonthSelect($month_active)
{
	global $lang;
	$month = array();
	for ($i = 0; $i < 12; $i++) {
		$month[$i]['value'] = $i+1;
		$month[$i]['name'] = $lang['month'][$i+1];
		if (intval($month_active) == $i+1) {
			$month[$i]['sel'] = 1;
		} else {
			$month[$i]['sel'] = 0;
		}
	}
	return $month;
}

// RS: moved from functions_user.php
function GetYearSelect($year_active, $year_limit, $year_count)
{
	$year = array();
	for ($i = 0; $i < $year_limit; $i++) {
		$y = $year_count - $i;
		$year[$i]['value'] = $year[$i]['name'] = $y;
		if (intval($year_active) == $y) {
			$year[$i]['sel'] = 1;
		} else {
			$year[$i]['sel'] = 0;
		}
	}
	return $year;
}

// RS:
function prepSmartyDate($day, $month, $year, $settings = null)
{
	global $smarty, $config;
	
	// get settings only if we do not already have them
	if (empty($settings) || empty($settings['min_age_limit']) || empty($settings['max_age_limit'])) {
		$settings = GetSiteSettings(array('min_age_limit', 'max_age_limit'));
	}
	
	$daySelect = GetDaySelect($day);
	$monthSelect = GetMonthSelect($month);
	$yearSelect = GetYearSelect($year, ($settings['max_age_limit'] - $settings['min_age_limit']), (int) date('Y') - (int) $settings['min_age_limit']);
	
	$date_parts = explode('%', $config['date_format']);
	
	for ($i = 1; $i < count($date_parts); $i++)
	{
		switch ($date_parts[$i][0]) {
			case 'm':
			case 'c':
				$smarty->assign('date_part'.$i, $monthSelect);
				$smarty->assign('date_part'.$i.'_name', 'month');
				$smarty->assign('date_part'.$i.'_default', 'MMM');
			break;
			
			case 'd':
			case 'e':
				$smarty->assign('date_part'.$i, $daySelect);
				$smarty->assign('date_part'.$i.'_name', 'day');
				$smarty->assign('date_part'.$i.'_default', 'DD');
			break;
			
			case 'Y':
			case 'y':
				$smarty->assign('date_part'.$i, $yearSelect);
				$smarty->assign('date_part'.$i.'_name', 'year');
				$smarty->assign('date_part'.$i.'_default', 'YYYY');
			break;
		}
	}
}

// RS:
function formatDateSql($date)
{
	global $dbconn, $config;
	$rs = $dbconn->Execute('SELECT DATE_FORMAT("'.$date.'", "'.$config['date_format'].'")');
	return $rs->fields[0]; 
}

// moved from payment.php
function getProductName($id_period)
{
	global $dbconn, $lang, $user;
	
	$strSQL =
		'SELECT a.amount, a.period, a.recurring, a.trial_amount, b.name AS groupname 
		   FROM '.GROUP_PERIOD_TABLE.' a
	  LEFT JOIN '.GROUPS_TABLE.' b ON a.id_group = b.id 
		  WHERE a.id = ?';
	
	$rs = $dbconn->Execute($strSQL, array($id_period));
	$row = $rs->GetRowAssoc(false);
	
	if ($id_period == MM_PLATINUM_LADY_PERIOD_ID || $id_period == MM_PLATINUM_GUY_PERIOD_ID)
	{
		$product_name = $row['amount'].' ';
		if ($row['amount'] == 1) {
			$product_name .= $lang['pays']['periods_singular'][ $row['period'] ];
		} else {
			$product_name .= $lang['pays']['periods_plural'][ $row['period'] ];
		}
		$product_name .= ' '.$lang['payment']['platinum_product_name'];
	}
	else
	{
		if ($row['amount'] == 0) {
			$product_name = 'Unlimited';
		} else {
			$product_name = $row['amount'].' ';
			if ($row['amount'] == 1) {
				$product_name .= $lang['pays']['periods_singular'][ $row['period'] ];
			} else {
				$product_name .= $lang['pays']['periods_plural'][ $row['period'] ];
			}
		}
		$use_recurring = $dbconn->GetOne('SELECT COUNT(*) FROM '.GROUP_PERIOD_TABLE.' WHERE recurring = "1" AND status = "1"');
		if (!empty($use_recurring)) {
			if ($row['recurring']) {
				$product_name .=  ' Recurring';
			} else {
				$product_name .=  ' Non-Recurring';
			}
		}
		$product_name .= ' '.$row['groupname'].' Membership';
		if ($user[ AUTH_IS_APPLICANT ] && $row['recurring'] && $row['trial_amount'] > 0) {
			$product_name .= ' (1st Month FREE)';
		}
	}
	return $product_name;
}

?>