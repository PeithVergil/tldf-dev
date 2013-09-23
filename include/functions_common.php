<?php

function GetSiteSettings($set_arr = '')
{
	global $dbconn;
	
	$settings = array();
	
	if (is_array($set_arr) && !empty($set_arr))
	{
		foreach ($set_arr as $key => $set_name) {
			$set_arr[$key] = '"'.$set_name.'"';
		}
		$sett_string = implode(', ', $set_arr);
		$rs = $dbconn->Execute('SELECT name, value FROM '.SETTINGS_TABLE.' WHERE name IN ('.$sett_string.')');
		while (!$rs->EOF) {
			$settings[$rs->fields[0]] = $rs->fields[1];
			$rs->MoveNext();
		}
	}
	elseif (strlen($set_arr) > 0)
	{
		$rs = $dbconn->Execute('SELECT value FROM '.SETTINGS_TABLE.' WHERE name = "'.strval($set_arr).'"');
		$settings = $rs->fields[0];
	}
	elseif (strval($set_arr) == '')
	{
		$rs = $dbconn->Execute('SELECT name, value FROM '.SETTINGS_TABLE.' ORDER BY id');
		while (!$rs->EOF) {
			$settings[$rs->fields[0]] = $rs->fields[1];
			$rs->MoveNext();
		}
	}
	
	return $settings;
}

function GetBaseLang($_LANG_NEED_ID)
{
	global $dbconn;
	
	$ret_arr = array();
	
	if (isset($_LANG_NEED_ID['country']) && count($_LANG_NEED_ID['country']) > 0) {
		$rs = $dbconn->Execute('SELECT DISTINCT id, name FROM '.COUNTRY_SPR_TABLE.' WHERE id IN ('.implode(',', $_LANG_NEED_ID['country']).')');
		while (!$rs->EOF) {
			$ret_arr['country'][ $rs->fields[0] ] = $rs->fields[1];
			$rs->MoveNext();
		}
	}
	
	if (isset($_LANG_NEED_ID['language']) && count($_LANG_NEED_ID['language']) > 0) {
		$rs = $dbconn->Execute('SELECT DISTINCT id, name FROM '.LANGUAGE_SPR_TABLE.' WHERE id IN ('.implode(',', $_LANG_NEED_ID['language']).')');
		while (!$rs->EOF) {
			$ret_arr['language'][ $rs->fields[0] ] = $rs->fields[1];
			$rs->MoveNext();
		}
	}
	
	if (isset($_LANG_NEED_ID['nationality']) && count($_LANG_NEED_ID['nationality']) > 0) {
		$rs = $dbconn->Execute('SELECT DISTINCT id, name FROM '.NATION_SPR_TABLE.' WHERE id IN ('.implode(',', $_LANG_NEED_ID['nationality']).')');
		while (!$rs->EOF) {
			$ret_arr['nationality'][ $rs->fields[0] ] = $rs->fields[1];
			$rs->MoveNext();
		}
	}
	
	if (isset($_LANG_NEED_ID['city']) && count($_LANG_NEED_ID['city']) > 0) {
		$rs = $dbconn->Execute('SELECT DISTINCT id, name FROM '.CITY_SPR_TABLE.' WHERE id IN ('.implode(',', $_LANG_NEED_ID['city']).')');
		while (!$rs->EOF) {
			$ret_arr['city'][ $rs->fields[0] ] = $rs->fields[1];
			$rs->MoveNext();
		}
	}
	
	if (isset($_LANG_NEED_ID['region']) && count($_LANG_NEED_ID['region']) > 0) {
		$rs = $dbconn->Execute('SELECT DISTINCT id, name FROM '.REGION_SPR_TABLE.' WHERE id IN ('.implode(',', $_LANG_NEED_ID['region']).')');
		while (!$rs->EOF) {
			$ret_arr['region'][ $rs->fields[0] ] = $rs->fields[1];
			$rs->MoveNext();
		}
	}
	
	return $ret_arr;
}

// Filter functions
//
function Rep_Slashes($str)
{
	$str = stripslashes($str);	// only necessary when magic quotes GPC are on
	$str = str_replace('"', '&quot;', $str);
	$str = str_replace("'", '&#039;', $str);
	$str = str_replace(">", '&gt;', $str);
	$str = str_replace("<", '&lt;', $str);
	return $str;
}

function LoginFilter($str)
{
	global $lang;
	
	if (strlen($str) < 5 || strlen($str) > 20) {
		return $lang['err']['login_length'];
	}
	
	/*if (!eregi("^[0-9a-z_\sA-Z]*$", $str)) {
		return $lang['err']['login_cont'];
	}*/
	return '';
}

function EmailFilter($str)
{
	global $lang, $check_email_domain;
	
	if (strlen($str) > 0)
	{
		//VP special chracters check in email
		if (!eregi("^[_a-z0-9-]+([+\.][_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $str)) {
			return $lang['err']['email_bad'];
		}
		//check domain
		/*
		if (!isset($check_email_domain) || $check_email_domain) {
			$tmp = array_reverse(explode('@', $str));
			$long_ip = ip2long(gethostbyname($tmp[0]));
			if ($long_ip == false || $long_ip == -1) {
				return $lang['err']['email_bad'];
			}
		}
		*/
	}
	return '';
}

function PhoneFilter($str)
{
	global $lang;
	
	if (strlen($str) > 0) {
		if (!preg_match("/^\d{10,15}(x\d{1,5})?$/", $str)) {
			return $lang['err']['phone_bad'];
		}
	}
	return '';
}

function PasswFilter($str)
{
	global $lang;
	
	if (strlen($str) < 6 || strlen($str) > 20) {
		return $lang['err']['pass_length'];
	}
	//VP some special symbols added
	//if (!eregi("^[0-9a-z_]*$", $str))
	if (!eregi("^[a-zA-Z0-9!@#$%^&*()\-_+{}<>\/]*$", $str)) {
		return $lang['err']['pass_cont'];
	}
	return '';
}

function FormFilter($str)
{
	return Rep_Slashes(strip_tags(trim(strval($str))));
}

function FormHMTLFilter($str)
{
	return strip_tags(strval($str), '<p><a><img><b><i><u>');
}

function stripn($str)
{
	return eregi_replace("\r", '', $str);
}

// Slash function
//

function AfterLastSlash($str)
{
	$arr = explode('/', $str);
	return $arr[count($arr)-1];
}

function DelFirstSlash($str)
{
	$str = strval($str);
	if ($str[0] == '/') {
		return substr($str, 1);
	}
	return $str;
}

function DelLastSlash($str)
{
	$str = strval($str);
	if ($str[strlen($str)-1] == '/') {
		return substr($str,0, -1);
	}
	return $str;
}

function TrimSlash($str)
{
	return DelFirstSlash(DelLastSlash(strval($str)));
}

// Date & Age functions
//

function AgeFromBDate($date)
{
	// date in Y-m-d h:i:s format
	
	$year = intval(substr($date, 0, 4));
	$month = intval(substr($date, 5, 2));
	$day = intval(substr($date, 8, 2));
	
	$n_year = date('Y');
	$n_month = date('m');
	$n_day = date('d');
	
	// simplified by ralf
	/*
	if ($month == $n_month) {
		if ($day > $n_day) {
			$age = floor(($n_year - $year) + ($n_month - $month - 1) / 12);
		} else {
			$age = floor(($n_year - $year) + ($n_month - $month) / 12);
		}
	} else {
		$age = floor(($n_year - $year) + ($n_month - $month) / 12);
	}
	*/
	
	if ($month < $n_month || $month == $n_month && $day <= $n_day) {
		$age = $n_year - $year;
	} elseif ($n_year > $year) {
		$age = $n_year - $year - 1;
	} else {
		$age = 0;
	}
	return $age;
}

function DateFromAge($age)
{
	// date in Y-m-d h:i:s format
	$n_year = date('Y');
	$year = $n_year - intval($age);
	return strval($year).'-01-01 00:00:00';
}

function bbcode($text)
{
	global $lang, $config;
	
	$smile_path = $config['server'].$config['site_root'].$config['index_theme_path'].'/images/smiles/';
	$text = preg_replace("/\s\:\)\s/is", '<img src="'.$smile_path.'1.gif" hspace="0" vspace="0" border="0">', $text);
	$text = preg_replace("/\s\;\)\s/is", '<img src="'.$smile_path.'2.gif" hspace="0" vspace="0" border="0">', $text);
	$text = preg_replace("/\s\:D\s/is", '<img src="'.$smile_path.'3.gif" hspace="0" vspace="0" border="0">', $text);
	$text = preg_replace("/\s8\)\s/is", '<img src="'.$smile_path.'4.gif" hspace="0" vspace="0" border="0">', $text);
	$text = preg_replace("/\s\:\]\s/is", '<img src="'.$smile_path.'5.gif" hspace="0" vspace="0" border="0">', $text);
	$text = preg_replace("/\s\:O\s/is", '<img src="'.$smile_path.'6.gif" hspace="0" vspace="0" border="0">', $text);
	$text = preg_replace("/\s\:\/\s/is", '<img src="'.$smile_path.'7.gif" hspace="0" vspace="0" border="0">', $text);
	$text = preg_replace("/\s\:\(\s/is", '<img src="'.$smile_path.'8.gif" hspace="0" vspace="0" border="0">', $text);
	$text = preg_replace("/\s\;\(\s/is", '<img src="'.$smile_path.'9.gif" hspace="0" vspace="0" border="0">', $text);
	$text = preg_replace("/\sO\_O\s/is", '<img src="'.$smile_path.'10.gif" hspace="0" vspace="0" border="0">', $text);
	
	$text = trim($text);
	$text = ereg_replace("\n", '<br>', $text);
	$text = preg_replace("/\[quote\](.+?)\[\/quote\]/is", '<div style="background-color: #edeef0;"><b>'.$lang['forum']['quote'].":</b><br><i>\\1</i></div>", $text);
	$text = preg_replace("/\[quote=([^<]+?)\](.+?)\[\/quote\]{1}/is", '<div style="background-color: #edeef0; padding: 3px; margin: 0px;">'."<b>\\1 ".$lang['forum']['said'].":</b><br><i>\\2</i></div>", $text);
	$text = preg_replace("/\[b\](.+?)\[\/b\]/is", "<b>\\1</b>", $text);
	$text = preg_replace("/\[i\](.+?)\[\/i\]/is", "<i>\\1</i>", $text);
	$text = preg_replace("/\[u\](.+?)\[\/u\]/is", "<u>\\1</u>", $text);
	$text = preg_replace("/\[img\](.+?)\[\/img\]{1}/is", "<img src='\\1'>", $text);
	$text = preg_replace("/\[img=(.+?)\salign=(.+?)\shspace=(.+?)\svspace=(.+?)\](.+?)\[\/img\]{1}/is", "<a href='\\1' target='_blank'><img src='\\5' align='\\2' class='icon' alt='' hspace='\\3' vspace='\\4'></a>", $text);
	$text = preg_replace("/\[email\](.+?)\[\/email\]{1}/is", "<a href='mailto:\\1'>\\1</a>", $text);
	$text = preg_replace("/\[url=([^<]+?)\](.+?)\[\/url\]{1}/is", "<a href='\\1' target='_blank'>\\2</a>", $text);
	$text = preg_replace("/\[url\](.+?)\[\/url\]{1}/is", "<a href='\\1' target='_blank'>\\1</a>", $text);
	return $text;
}

function bbdecode($text)
{
	global $config;
	
	$smile_path = $config['server'].$config['site_root'].$config['index_theme_path'].'/images/smiles/';
	$smile_slashed_path = addcslashes($smile_path, '/');
	
	$text = ereg_replace("\<br\>", "\n", $text);
	
	$text = preg_replace("/\<b\>(.+?)\<\/b\>/is", "[b]\\1[/b]", $text);
	$text = preg_replace("/\<i\>(.+?)\<\/i\>/is", "[i]\\1[/i]", $text);
	$text = preg_replace("/\<u\>(.+?)\<\/u\>/is", "[u]\\1[/u]", $text);
	
	$text = preg_replace("/\<a\shref=\'(.+?)\'\starget=\'\_blank\'\>\<img\ssrc=\'(.+?)\'\salign=\'(.+?)\'\sclass=\'icon\'\salt=\'\'\shspace=\'(.+?)\'\svspace=\'(.+?)\'\>\<\/a\>{1}/is", "[img=\\1 align=\\3 hspace=\\4 vspace=\\5]\\2[/img]", $text);
	$text = preg_replace("/\<a\shref=\'(.+?)\'\starget=\'\_blank\'\>(.+?)\<\/a\>{1}/is", "[url=\\1]\\2[/url]", $text);
	
	$text = preg_replace("/\<img\ssrc=\'".$smile_slashed_path."1.gif\'\shspace=0\svspace=0\sborder=0\>{1}/is", " :) ", $text);
	$text = preg_replace("/\<img\ssrc=\'".$smile_slashed_path."2.gif\'\shspace=0\svspace=0\sborder=0\>{1}/is", " ;) ", $text);
	$text = preg_replace("/\<img\ssrc=\'".$smile_slashed_path."3.gif\'\shspace=0\svspace=0\sborder=0\>{1}/is", " :D ", $text);
	$text = preg_replace("/\<img\ssrc=\'".$smile_slashed_path."4.gif\'\shspace=0\svspace=0\sborder=0\>{1}/is", " 8) ", $text);
	$text = preg_replace("/\<img\ssrc=\'".$smile_slashed_path."5.gif\'\shspace=0\svspace=0\sborder=0\>{1}/is", " :] ", $text);
	$text = preg_replace("/\<img\ssrc=\'".$smile_slashed_path."6.gif\'\shspace=0\svspace=0\sborder=0\>{1}/is", " :O ", $text);
	$text = preg_replace("/\<img\ssrc=\'".$smile_slashed_path."7.gif\'\shspace=0\svspace=0\sborder=0\>{1}/is", " :/ ", $text);
	$text = preg_replace("/\<img\ssrc=\'".$smile_slashed_path."8.gif\'\shspace=0\svspace=0\sborder=0\>{1}/is", " :( ", $text);
	$text = preg_replace("/\<img\ssrc=\'".$smile_slashed_path."9.gif\'\shspace=0\svspace=0\sborder=0\>{1}/is", " ;( ", $text);
	$text = preg_replace("/\<img\ssrc=\'".$smile_slashed_path."10.gif\'\shspace=0\svspace=0\sborder=0\>{1}/is", " O_O ", $text);
	
	$text = preg_replace("/\<img\ssrc=\'(.+?)\'\>{1}/is", "[img]\\1[/img]", $text);
	$text = preg_replace('/\<a\shref=\'(.+?)\'\starget=\'_blank\'\>(.+?)\<\/a\>{1}/is', '[url=\\1]\\2[/url]', $text);
	
	return $text;
}

function utf8_substr($str='', $from, $len)
{
	return mb_substr($str, $from, $len);
}

function convertSecsToDate($secs_ost)
{
	$year = 60*60*24*30*12;
	$month = 60*60*24*30;
	$day = 60*60*24;
	$hour = 60*60;
	$min = 60;
	$sec = 1;
	
	$years = intval($secs_ost/$year);
	$secs_ost = $secs_ost - $years * $year;
	
	$months = intval($secs_ost / $month);
	$secs_ost = $secs_ost - $months * $month;
	
	$days = intval($secs_ost/$day);
	$secs_ost = $secs_ost-$days*$day;
	
	$hours = intval($secs_ost/$hour);
	$secs_ost = $secs_ost-$hours*$hour;
	
	$mins = intval($secs_ost/$min);
	$secs_ost = $secs_ost-$mins*$min;
	
	$secs = $secs_ost;
	
	if ($years) $arr['years'] = $years;
	if ($months) $arr['months'] = $months;
	if ($days) $arr['days'] = $days;
	if ($hours) $arr['hours'] = $hours;
	if ($mins) $arr['min'] = $mins;
	if ($secs) $arr['sec'] = $secs;
	$str = '';
	foreach ($arr as $key => $val) {
		$str .= $val.$key;
	}
	return $str;
}

function GetTempUploadFile($file_name)
{
	global $config;
	
	$path_to_image = '';
	
	$matches = array();
	
	$forbidden_chars = strtr("$/\\:*?&quot;'&lt;&gt;|`", array('&amp;' => '&', '&quot;' => '"', '&lt;' => '<', '&gt;' => '>'));
	
	if (get_magic_quotes_gpc()) {
		$file_name = stripslashes($file_name);
	}
	
	$picture_name = strtr($file_name, $forbidden_chars, str_repeat('_', strlen("$/\\:*?&quot;'&lt;&gt;|`")));
	
	if (!preg_match("/(.+)\.(.*?)\Z/", $picture_name, $matches)) {
		$matches[1] = 'invalid_fname';
		$matches[2] = 'xxx';
	}
	
	$prefix = 'mHTTP_temp_';
	$suffix = $matches[2];
	
	do {
		$seed = substr(md5(microtime().getmypid()), 0, 8);
		$path_to_image = $config['file_temp_path'].'/'. $prefix . $seed . '.' . $suffix;
	} while (file_exists($path_to_image));
	
	return $path_to_image;
}

function getFileSizeFromString($s)
{
	$unit = strtoupper(substr($s, -1));
	$multiplier = ($unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1)));
	return ($multiplier * (int) $s);
}

function BadWordsCont($text, $area)
{
	global $dbconn, $config, $lang, $user;
	
	$err = '';
	$bw_array = array();
	$text_array = array();
	
	$text = trim(strtolower($text));
	
	$settings = GetSiteSettings(array('badwords_file_path', 'badwords_file_name'));
	
	$file_path = DelLastSlash($config['site_path']).'/'.TrimSlash($settings['badwords_file_path']).'/'.TrimSlash($settings['badwords_file_name']);
	
	if (file_exists($file_path) && is_readable($file_path) && strlen($text) > 0)
	{
		$bw_file = strtolower(implode('', file($file_path)));
		$bw_file = explode(',', $bw_file);
		
		foreach ($bw_file as $v) {
			if (strlen(trim($v)) > 0) {
				$pos = eregi("(^| |[[:punct:]])".trim($v)."($| |[[:punct:]])", $text);
				if (intval($pos) != 0) {
					// find
					$err = ($area == 8 || $area == 7) ? 1 : $lang['err']['badword_finding_'.$area];
					break;
				}
			}
		}
	}
	else
	{
		$err = '';
	}
	
	if (!$user[ AUTH_ROOT ] && !$user[ AUTH_GUEST ]) {
		if (strlen($err) > 0) {
			$dbconn->Execute(
				'INSERT INTO '.BADWORDS_TABLE.' SET id_user = ?, date_alert = NOW(), area = ?, msg_text = ?',
				array($user[ AUTH_ID_USER ], (string)$area, $text));
		}
	}
	
	return $err;
}

function check_filter($letter)
{
	if (icq_filter($letter)) return true;
	if (email_filter($letter)) return true;
	if (check_aol($letter)) return true;
	if (check_aim($letter)) return true;
	if (check_msn($letter)) return true;
	if (check_yahoo($letter)) return true;
	
	return false;
}


function icq_filter($letter)
{
	$s = 0;
	
	$preg = "/i[\d_\s=-]?c[\d_\s=-]?q\s(?:\s|\w|\S)*:?\n?\s\d+/is";
	
	if (preg_match($preg, $letter)) {
		// $letter = preg_replace($preg,"", $letter);
		$s = 1;
	}
	
	$preg = "/I[\d_\w]+C[\d_\w]+Q[\d_\w]+\s*:?\n?\s\d+/";
	
	if (preg_match($preg, $letter)) {
		// $letter = preg_replace($preg,"", $letter);
		$s = 1;
	}
	
	return $s;
}


function email_filter($letter)
{
	$s = 0;
	
	if (preg_match("/^(?:\s|\w|\S)+\@+(?:\s|\w)+\.+(?:\s|\w)/", $letter)) {
		$s = 1;
	}
	
	// already commented in original code
	/*
	if (preg_match("/\sat\s/i",$letter)) {
	$letter = preg_replace("/\w+\sat\s\w+\sdot\s\w+/is","", $letter);
	$s = 1;
	}
	if (preg_match("/\sat\s/i",$letter)) {
	$letter = preg_replace("/\w+\sat\s[\w\.]+/is","", $letter);
	$s = 1;
	}
	*/
	
	return $s;
}


function aim_filter($letter)
{
	$s = 0;
	$preg = "/a[\d_\s=-]?i[\d_\s=-]?m\s*:\n?\s\w+/is";
	
	if (preg_match($preg, $letter)) {
		// $letter = preg_replace($preg,"", $letter);
		$s = 1;
	}
	
	$preg = "/A[\d_\w]+I[\d_\w]+M[\d_\w]+\s*:\n?\s\w+/";
	
	if (preg_match($preg, $letter)) {
		// $letter = preg_replace($preg,"", $letter);
		$s = 1;
	}
	
	return $s;
}


function aol_filter($letter)
{
	$s = 0;
	$preg = "/a[\d_\s=-]?o[\d_\s=-]?l\s*:\n?\s\w+/is";
	
	if (preg_match($preg, $letter)) {
		// $letter = preg_replace($preg,"", $letter);
		$s = 1;
	}
	
	$preg = "/A[\d_\w]+O[\d_\w]+L[\d_\w]+\s*:\n?\s\w+/";
	
	if (preg_match($preg, $letter)) {
		// $letter = preg_replace($preg,"", $letter);
		$s = 1;
	}
	
	return $s;
}


function check_aol($letter)
{
	$preg = "/a[\d_\s=-]?o[\d_\s=-]?l\s*:\n?\s\w+/is";
	
	if (preg_match($preg, $letter)) {
		return true;
	}
	
	return false;
}


function check_aim($letter)
{
	$preg = "/a[\d_\s=-]?i[\d_\s=-]?m\s*:\s\w+/i";
	
	if (preg_match($preg, $letter)) {
		return true;
	}
	
	return false;
}


function check_msn($letter)
{
	$preg = "/m[\d_\s=-]?s[\d_\s=-]?n\s*:\s\w+/i";
	
	if (preg_match($preg, $letter)) {
		return true;
	}
	
	return false;
}


function check_yahoo($letter)
{
	$preg = "/yahoo[\d_\s=-]?messenger[\d_\s=-]?\s*\s\w+/i";
	
	if (preg_match($preg, $letter)) {
		return true;
	}
	
	return false;
}


function CreateToken($id_user)
{
	global $dbconn;
	
	$puddle = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$len = strlen($puddle);
	$token = '';
	
	for ($i = 0; $i < 16; $i++) {
		$pos = mt_rand(0, $len - 1);
		$token .= substr($puddle, $pos, 1);
	}
	
	$dbconn->Execute('INSERT INTO '.USER_TOKEN_TABLE.' SET id_user = ?, token = ?, created = NOW()', array($id_user, $token));
	
	return $token;
}

// RS: google analytics helper functions
function ga_gender($gender)
{
	return ($gender == GENDER_MALE) ? 'male' : 'female';
}

function ga_member_status($id_group)
{
	switch ($id_group) {
		case MM_SIGNUP_GUY_ID:
		case MM_SIGNUP_LADY_ID:
			return 'Signup';
		break;
		case MM_TRIAL_GUY_ID:
		case MM_TRIAL_LADY_ID:
			return 'Trial';
		break;
		case MM_REGULAR_GUY_ID:
		case MM_REGULAR_LADY_ID:
			return 'Regular';
		break;
		case MM_PLATINUM_GUY_ID:
		case MM_PLATINUM_LADY_ID:
			return 'Platinum';
		break;
		default:
			return 'Guest';
		break;
	}
}

function ga_enqueue_event($id_user, $event_code)
{
	global $dbconn;
	$dbconn->Execute('INSERT INTO '.GA_EVENTS_TABLE.' SET id_user = ?, event_code = ?, date_add = NOW()', array($id_user, $event_code));
}

function ga_dequeue_event()
{
	global $dbconn, $user;
	
	if (!empty($_SESSION['ga_event_code'])) {
		return;
	}
	
	if (empty($user) || $user == 'err' || !$user[ AUTH_ID_USER ] || $user[ AUTH_GUEST ] || $user[ AUTH_ROOT ]) {
		return;
	}
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$rs = $dbconn->Execute('SELECT id, event_code FROM '.GA_EVENTS_TABLE.' WHERE id_user = ? ORDER BY date_add', array($id_user));
	
	if (!$rs->EOF) {
		$_SESSION['ga_event_code'] = stripslashes($rs->fields[1]);
		$dbconn->Execute('DELETE FROM '.GA_EVENTS_TABLE.' WHERE id = ?', array($rs->fields[0]));
	}
}

function solve360_api_error($contact, $subject, $login)
{
	global $config;
	
	if (REDIRECT_ADMIN_EMAIL && !IS_LIVE_SERVER) {
		$email_to = REDIRECT_ADMIN_EMAIL_TO;
	} else {
		$email_to = $config['site_email'];
	}
	$subject = 'SOLVE360: '.$subject;
	$body = 'login='.$login.' Error: ' . $contact->errors->asXml();
	mail($email_to, $subject, $body);
}

?>