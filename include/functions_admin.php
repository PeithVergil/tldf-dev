<?php

/**
 * Include file
 * Functions for admin section
 *
 * @package DatingPro
 * @subpackage Include files
 **/

include_once('functions_common.php');

function AdminMainMenu($header = '', $light = '')
{
	global $lang, $config, $config_admin, $smarty, $dbconn;
	
	// logoff
	$logoff['link']		= $config['site_root'].$config_admin['logoff_path'];
	$logoff['touser']	= $config['site_root'].$config_admin['touser_path'];
	$logoff['value']	= $lang['logoff'];
	$smarty->assign('logoff', $logoff);
	
	// langs
	$rs = $dbconn->Execute('SELECT id, name, code FROM '.LANGUAGE_TABLE.' WHERE visible = "1"');
	
	$i = 0;
	$lang_link = array();
	
	while (!$rs->EOF) {
		$lang_link[$i]['code'] = $rs->fields[2];
		$lang_link[$i]['name'] = ucfirst($rs->fields[1]);
		$query = $_SERVER['QUERY_STRING'];
		if ($query) {
			$new_query_arr = array();
			$query_arr = explode('&', $query);
			foreach ($query_arr as $v) {
				$s = explode('=', $v);
				if (trim($s[0] != 'language_code')) {
					array_push($new_query_arr, $v);
				}
			}
			$query = implode('&', $new_query_arr);
		}
		if (strlen($query) > 0)	{
			$query .= '&';
		}
		$lang_link[$i]['link'] = '?'.$query.'language_code='.$rs->fields[0];
		$rs->MoveNext();
		$i++;
	}
	$smarty->assign('langs_link', $lang_link);

	$smarty->assign('help', $lang['admin_help']);
	$smarty->assign('header', $header);
	$smarty->assign('light', $light);
	if (isset($_SESSION['new_version'])) {
		$smarty->assign('new_version', $_SESSION['new_version']);
	}
	$smarty->assign('template_root', $config['admin_theme_path']);

	$smarty->assign('button', $lang['button']);
	$smarty->assign('user_template_root', $config['index_theme_path']);
	return;
}

/////////////////////////////////////////////////////////////////////////////////////

function GetLinkStr($num_records,$page,$param, $max_record, $var_page_name='page')
{
	global $lang;
	
	$num_page = ceil($num_records/$max_record);
	$p_page = floor(($page-1)/10);
	
	$link_str = '&nbsp;&nbsp; '.$lang['pages'].': &nbsp;';
	
	if ($p_page > 0) $link_str .= '<a href="'.$param.$var_page_name.'='.($p_page*10).'" class="page_link"><<</a>&nbsp;';
	
	if ((($p_page+1)*10) >= $num_page) {
		for ($i = $p_page*10+1; $i <= $num_page; $i++) {
			if ($i == $page) {
				$link_str .= '<b><font class="page_link">'.$i.'</font></b>&nbsp;';
			} else {
				$link_str .= '<a href="'.$param.$var_page_name.'='.$i.'" class="page_link">'.$i.'</a>&nbsp;';
			}
		}
	} else {
		for ($i = $p_page*10+1; $i <= ($p_page+1)*10; $i++) {
			if ($i == $page) {
				$link_str .= '<b><font class="page_link">'.$i.'</font></b>&nbsp;';
			} else {
				$link_str .= '<a href="'.$param.$var_page_name.'='.$i.'" class="page_link">'.$i.'</a>&nbsp;';
			}
		}
		$link_str .= '<a href="'.$param.$var_page_name.'='.(($p_page+1)*10+1).'" class="page_link">>></a>&nbsp;';
	}
	
	return $link_str;
}

///////////////////////////////////////////////////////////////////////////////////////

function LetersLink_rus($url_param, $active_leter){

	for($i=192;$i<=223;$i++){
		if($i == 218 || $i == 220 || $i == 201) continue;
		if($i == $active_leter){
			$leter_str .= "&nbsp;<b><font class=page_link>".chr($i)."</font></b>";
		}else{
			$leter_str .= "&nbsp;<a href=\"".$url_param."".$i."\">".chr($i)."</a>";
		}
		if($i == 197) { $leter_str .= "&nbsp;<a href=\"".$url_param."168\">".chr(168)."</a>";}
	}
	if($active_leter == "*"){
		$leter_str .= "&nbsp;&nbsp;&nbsp; &nbsp;<b><font class=page_link>".chr(192)."-".chr(223)."</font></b>&nbsp;";
	}else{
		$leter_str .= "&nbsp;&nbsp;&nbsp; &nbsp;<a href=\"".$url_param."*\">".chr(192)."-".chr(223)."</a>&nbsp;";
	}
	return $leter_str;
}

///////////////////////////////////////////////////////////////////////////////////////

function LetersLink_eng($url_param, $active_leter)
{
	$leter_str = '';
	
	for ($i = 65; $i <= 90; $i++) {
		if ($i == $active_leter) {
			$leter_str .= '&nbsp;<b><font class="page_link">'.chr($i).'</font></b>';
		} else {
			$leter_str .= '&nbsp;<a href="'.$url_param.''.$i.'" class="page_link">'.chr($i).'</a>';
		}
	}
	
	if ($active_leter == '*') {
		$leter_str .= '&nbsp;&nbsp;&nbsp;&nbsp;<b><font class="page_link">'.chr(65).'-'.chr(90).'</font></b>&nbsp;';
	} else {
		$leter_str .= '&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$url_param.'*" class="page_link">'.chr(65).'-'.chr(90).'</a>&nbsp;';
	}
	
	return $leter_str;
}

function GetPermissionsUser($id_user)
{
	global $dbconn;
	
	$module = array();
	
	//check moderator or not
	$strSQL =
		'SELECT g.type
		   FROM '.GROUPS_TABLE.' g
	 INNER JOIN '.USER_GROUP_TABLE.' ug ON g.id = ug.id_group
		  WHERE ug.id_user = ?';
	$rs = $dbconn->Execute($strSQL, array($id_user));
	if ($rs->fields[0] != 'm') {
		$strSQL =
			'SELECT DISTINCT a.id_module
			   FROM '.GROUP_MODULE_TABLE.' a
		 INNER JOIN '.USER_GROUP_TABLE.' b ON b.id_group = a.id_group
			  WHERE b.id_user = ?';
		$rs = $dbconn->Execute($strSQL, array($id_user));
		$i = 0;
		while(!$rs->EOF){
			$module[$i] = $rs->fields[0];
			$rs->MoveNext();
			$i++;
		}
	} else {
		$strSQL = 'SELECT DISTINCT id, id_user, id_module FROM '.GROUP_MODULE_USER_TABLE.' WHERE id_user = ? GROUP BY id ORDER BY id';
		$rs = $dbconn->Execute($strSQL, array($id_user));
		$i = 0;
		while(!$rs->EOF){
			$module[$i] = $rs->fields[2];
			$rs->MoveNext();
			$i++;
		}
	}
	return $module;
}

function IsFileAllowed($id_user, $file, $lang_type = '')
{
	global $dbconn, $config;
	
	$mod_arr = GetPermissionsUser($id_user);
	
	$rs = $dbconn->Execute('SELECT id_module FROM '.MODULE_FILE_TABLE.' WHERE file = ?', array($file));
	$id_module = $rs->fields[0];
	
	if (!empty($mod_arr) && in_array($id_module, $mod_arr)) {
		// full mode
		return '1';
	}
	
	header('location: '.$config['server'].$config['site_root'].'/admin/index.php?err=1&lang_type='.$lang_type);
	exit;
}

function GetRightModulePath($file)
{
	global $config;
	$file_name = substr($file, strlen($config["site_path"]));
	$file_name = str_replace("\\", "/", $file_name);
	if (substr($file_name, 0, 1) != "/") {
		$file_name = "/".$file_name;
	}
	return $file_name;
}

function AdminAllowedModule($id_user)
{
	global $dbconn;
	
	$mod_arr = GetPermissionsUser($id_user);
	
	if (empty($mod_arr) || !is_array($mod_arr)) {
		return '/admin/admin_users.php';
	}
	
	$mod_str = implode(',', $mod_arr);
	
	$rs = $dbconn->Execute('SELECT file FROM '.MODULE_FILE_TABLE.' WHERE file LIKE "%admin%" AND id_module IN ('.$mod_str.')');
	
	if ($rs->fields[0]) {
		return $rs->fields[0];
	} else {
		return '/admin/admin_users.php';
	}
}

function PermissionError($err = '')
{
	global $smarty, $config, $lang;
	
	$file_name = 'index.php';

	AdminMainMenu($lang['index'], '1');
	
	if (isset($_COOKIE['login'])){
		$data['login'] = $_COOKIE['login'];
		$data['pass'] = $_COOKIE['pass'];
	}
	
	if ($err){
		$form['err'] = $err;
		$data['login'] = isset($_POST['login']) ? trim($_POST['login']) : '';
	}
	
	$form['clearlink'] = $file_name.'?sel=clear';
	$form['savelink'] = $file_name.'?sel=save';
	$form['action'] = $file_name;
	if (isset($data)) {
		$smarty->assign('data', $data);
	}
	$smarty->assign('form', $form);
	$smarty->assign('page_type', 'login');
	$smarty->assign('header', $lang['index']);
	$smarty->assign('button', $lang['button']);
	
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_login_form.tpl');
	exit;
}

function FormatDate($date, $format)
{
	$year = intval(substr($date,0,4));
	$month = intval(substr($date,5,2));
	$day = intval(substr($date,8,2));
	$hour = intval(substr($date,11,2));
	$minute = intval(substr($date,14,2));
	$sec = intval(substr($date,17,2));
	$mtime = mktime($hour,$minute,$sec,$month,$day,$year);
	if ($year) {
		return date ($format, $mtime);
	} else {
		return date ($format);
	}
}

?>