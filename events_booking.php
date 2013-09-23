<?php
/**
* Events Booking Page
*
* @package DatingPro
* @subpackage User Mode
**/

include './include/config.php';
include './common.php';
include './include/config_index.php';
include './include/functions_auth.php';
include './include/functions_index.php';
include './include/class.lang.php';
include './include/class.phpmailer.php';
include './include/functions_mail.php';

// authentication
$user = auth_index_user();

if (empty($user) || $user == 'err' || empty($user[ AUTH_ID_USER ])) {
	header('location: '.$config['site_root'].'/index.php');
	exit;
}

// check guest
// (public access)

// check group, period, expiration
RefreshAccount();

// check status
// (public access)

// check permissions
// (public access)

// alerts and statistics
if (!$user[ AUTH_GUEST ]) {
	GetAlertsMessage();
	SetModuleStatistic(GetRightModulePath(__FILE__));
}

// active menu item
$smarty->assign('sub_menu_num', '');

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

// dispatcher
switch ($sel) {
	//case 'send_request':
	//	EventsBookingTable();
	//break;
	
	default:
		EventsBookingTable();
	break;
}

exit;


function EventsBookingTable($err="")
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	// settings
	$settings = GetSiteSettings(array('icon_male_default', 'icon_female_default', 'icons_folder',
		'min_age_limit', 'max_age_limit', 'zip_letters', 'zip_count', 'date_format'));
	
	$id_user = $user[ AUTH_ID_USER ];
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$file_name = "events_booking.php";
	
	unset($_SESSION["id_arr"]);

	$_SESSION["id_arr"] = array();

	$strSQL = "Select distinct a.id_friend from ".HOTLIST_TABLE." a where a.id_user='".$id_user."' GROUP BY a.id_friend order by a.id desc";
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$_SESSION["id_arr"][$i] = $row["id_friend"];
		$rs->MoveNext();
		$i++;
	}
	
	$num_records = count($_SESSION["id_arr"]);
	
	if ($num_records > 0) {
		$where_clause = "WHERE a.id in (".implode(",", $_SESSION["id_arr"]).")";
	} else {
		$where_clause = "WHERE a.id in (0)";
	}
	
	if ($id_user != $user[ AUTH_ID_USER ]) {
		$where_clause .= "and a.visible='1'";
	}
	
	$multi_lang = new MultiLang();
	
	$strSQL =
		"SELECT DISTINCT a.id, a.login, a.mm_nickname, a.fname, a.sname, a.gender, a.phone, a.date_birthday, c.name as country,
				a.id_region, a.id_city, a.icon_path
		  FROM ".USERS_TABLE." a, ".COUNTRY_SPR_TABLE." c, ".HOTLIST_TABLE." b
	 LEFT JOIN ".REFERENCE_LANG_TABLE." r on r.id_reference = b.friend_type and r.table_key = '".$multi_lang->TableKey(HOTLIST_SPR_TABLE)."'
				".$where_clause." AND a.gender <> ".$user[ AUTH_GENDER ]." AND b.id_friend=a.id AND a.id_country=c.id
	  GROUP BY a.id
	  ORDER BY b.id DESC";
	$rs = $dbconn->Execute($strSQL);
	//echo $strSQL;
	
	$list = array();
	$i = 0;
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$list[$i]["id"] = $row["id"];
		$list[$i]["login"] = $row["login"];
		$list[$i]["nickname"] = $row["mm_nickname"];
		$list[$i]["fname"] = $row["fname"];
		$list[$i]["sname"] = $row["sname"];
		$list[$i]["gender"] = $row["gender"];
		$list[$i]["phone"] = $row["phone"];
		$list[$i]["age"] = AgeFromBDate($row["date_birthday"]);
		$list[$i]["id_city"] = intval($row["id_city"]);
		$list[$i]["id_region"] = intval($row["id_region"]);
		$list[$i]["country"] = $row["country"];
		$list[$i]["profile_link"] = "viewprofile.php"."?id=".$row["id"];
		$list[$i]["number"] = $i;
		
		$icon_path = $row["icon_path"] ? $row["icon_path"] : $default_photos[$row["gender"]];
		if ($icon_path && file_exists($config["site_path"].$settings["icons_folder"]."/".$icon_path)) {
			$list[$i]["icon_path"] = $config["site_root"].$settings["icons_folder"]."/".$icon_path;
		}
		$img_icon = $row["icon_path"] ? 1 : 0;
		
		$rs->MoveNext();
		$i++;
	}
	
	$msg = isset($_REQUEST['msg']) ? $_REQUEST['msg'] : 0;
	$ladies_arr = array();
	
	if ($msg == 0)
	{
		$data = $_POST;
		
		if ($data)
		{
			$date_from		= isset($data['date_from']) ? FormFilter($data['date_from']) : '';
			$date_to		= isset($data['date_to']) ? FormFilter($data['date_to']) : '';
			
			$other_ladies	= isset($data['other_ladies']) ? intval($data['other_ladies']) : 0;
			$like_to_know	= isset($data['like_to_know']) ? FormFilter($data['like_to_know']) : '';
			$best_number	= isset($data['best_number']) ? FormFilter($data['best_number']) : '';
			$best_time		= isset($data['best_time']) ? FormFilter($data['best_time']) : '';
			$send_info		= isset($data['send_info']) ? intval($data['send_info']) : '';
			
			$list_length	= isset($data['list_length']) ? intval($data['list_length']) : 0;
			
			if ($list_length > 0)
			{
				for ($i = 0; $i < $list_length; $i++) {
					$is_selected = isset($data['lady_'.$i]) ? intval($data['lady_'.$i]) : 0;
					if ($is_selected) {
						$lady_id	= isset($data['hid_'.$i]) ? intval($data['hid_'.$i]) : 0;
						$lady_nick	= isset($data['hnick_'.$i]) ? FormFilter($data['hnick_'.$i]) : '';
						$lady_age	= isset($data['hage_'.$i]) ? intval($data['hage_'.$i]) : 0;
						
						//putting value in array
						$ladies_arr[$i]['id'] = $lady_id;
						$ladies_arr[$i]['nick'] = $lady_nick;
						$ladies_arr[$i]['age'] = $lady_age;
					}
				}
				
				if (count($ladies_arr) == 0) {
					$err .= $lang['events_booking']['want_to_meet'] . ', ';
					$err_field['want_to_meet'] = 1;
				}
			}
			
			if (!strlen($date_from)) {
				$err .= $lang['events_booking']['date_from'] . ', ';
				$err_field['date_from'] = 1;
			}
			if (!strlen($date_to)) {
				$err .= $lang['events_booking']['date_to'] . ', ';
				$err_field['date_to'] = 1;
			}
			if (!strlen($best_number)) {
				$err .= $lang['events_booking']['best_number'] . ', ';
				$err_field['best_number'] = 1;
			}
			if (!strlen($best_time)) {
				$err .= $lang['events_booking']['best_time'] . ', ';
				$err_field['best_time'] = 1;
			}
			
			if ($err) {
				$smarty->assign('err_field', $err_field);
				$err = $lang['err']['invalid_fields'] . '<br/><br/>' . trim($err, ', ');
				
				$form['date_from']		= $date_from;
				$form['date_to']		= $date_to;
				$form['other_ladies']	= $other_ladies;
				$form['like_to_know']	= $like_to_know;
				$form['best_number']	= $best_number;
				$form['best_time']		= $best_time;
				$form['send_info']		= $send_info;
				
				$lady_stat = array();
				for ($i = 0; $i < $list_length; $i++) {
					//$form['lady_'.$i] = isset($data['lady_'.$i]) ? intval($data['lady_'.$i]) : 0;
					$lady_stat[$i]['is_check'] = isset($data['lady_'.$i]) ? intval($data['lady_'.$i]) : 0;
				}
				$smarty->assign('lady_stat', $lady_stat);
			}
			else
			{
				if (RequestSend($ladies_arr)) {
					echo '<script>location.href="./events_booking.php?msg=2";</script>';
				} else {
					echo '<script>location.href="./events_booking.php?msg=1";</script>';
				}
			}
		}
	}
	else
	{
		if ($msg == 1) {
			$form['res'] = $lang["events_booking"]["success"];
			$form['success']=1;
		}
		if ($msg == 2) {
			$form['res'] = $lang["events_booking"]["error"];
		}
	}
	
	$form['err'] = $err;
	$form['action'] = $file_name;
	$smarty->assign('form', $form);
	$smarty->assign('list', $list);
	$smarty->assign('ladies_arr', $ladies_arr);
	$smarty->display(TrimSlash($config["index_theme_path"])."/events_booking_table.tpl");
	exit;
}

function RequestSend($ladies_arr='')
{
	global $config, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	// language
	$site_lang = $config['default_lang'];
	
	// include mail language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	// content array
	$content_array				= $_POST;
	$content_array['urls']		= GetUserEmailLinks();
	
	$rs = $dbconn->Execute(
		'SELECT fname, sname, login, email, mm_nickname, mm_contact_phone_number
		   FROM '.USERS_TABLE.'
		  WHERE id = ?',
		array($id_user));
	$row = $rs->GetRowAssoc(false);
	
	$content_array['login']		= $row['login'];
	$content_array['email']		= $row['email'];
	$content_array['nick']		= $row['mm_nickname'];
	$content_array['fname']		= $row['fname'];
	$content_array['sname']		= $row['sname'];
	$content_array['contact']	= $row['mm_contact_phone_number'];
	$content_array['ladies_arr']= $ladies_arr;
	
	// subject
	$subject = $lang_mail['events_booking_admin']['subject'];
	
	$mail_err = SendMail($site_lang, $config['site_email'], $content_array['email'], $subject, $content_array,
		'mail_events_booking_admin', null, '', $content_array['fname'], 'events_booking_admin');
	
	return $mail_err;
}

?>