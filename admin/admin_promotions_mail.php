<?php

/**
* Promotional Emails Management.
*
* @package DatingPro
* @subpackage Admin Mode
**/

include '../include/config.php';
include_once '../common.php';
include '../include/config_admin.php';
include '../include/functions_auth.php';
include '../include/functions_admin.php';
include '../include/class.phpmailer.php';
include '../include/functions_mail.php';

$auth = auth_user();
login_check($auth);

global $debug;
$debug = false;

//IsFileAllowed($auth[0], GetRightModulePath(__FILE__), 'promotions');

$sel = isset($_REQUEST['sel']) ? $_REQUEST['sel']: '';

/*
$type = isset($_REQUEST['type']) ? $_REQUEST['type']: '';
if ($type== 's')
	$par='sistem';
else
	$par='admin';
*/

switch ($sel) {
	case 'create_promo':	CreatePromo(); break;
	case 'view_promo':		BrowsePromo('send_promo'); break;
	case 'edit_promo':		BrowsePromo('save_promo'); break;
	case 'save_promo':		SavePromo(); break;
	case 'send_promo':		SendPromo(); break;
	case 'del_rec':			DeletePromo(); break;
	case 'refresh_user':	RefreshUser(); break;
	default: PromoList();
}

exit;


function PromoList($err = '')
{
	global $smarty, $dbconn, $config, $lang;
	
	$file_name = 'admin_promotions_mail.php';
	
	AdminMainMenu($lang['promotions']);
	//$page = isset($_REQUEST['page']) ?  intval($_REQUEST['page']) : 1;
	
	$rs = $dbconn->Execute(
		'SELECT id, title, header, body_text, footer_text, recipient_group, status
		   FROM '.PROMOTION_MAIL_TABLE.'
	   ORDER BY id DESC');
	
	$sistem = array();
	$i = 0;
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$sistem[$i]['number']	= $i + 1;
		$sistem[$i]['id']		= $row['id'];
		$sistem[$i]['title']	= stripslashes($row['title']);
		$sistem[$i]['header']	= $row['header'];
		$sistem[$i]['viewlink']	= $file_name.'?sel=view_promo&pid='.$row['id'];
		$sistem[$i]['editlink']	= $file_name.'?sel=edit_promo&pid='.$row['id'];
		$sistem[$i]['status']	= $row['status'];
		//$sistem[$i]['recipient_group'] = $row['recipient_group'];
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('sistem', $sistem);
	
	//	form
	$form['err'] = $err;
	$form['action'] = $file_name;
#	$form['confirm'] = $lang['confirm']['promotions'];
	
	$smarty->assign('form', $form);
	$smarty->assign('button', $lang['button']);
	$smarty->assign('header', $lang['promotions']);
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_promotions_mail.tpl');
	exit;
}

function CreatePromo($err='')
{
	global $smarty, $dbconn, $config, $page, $lang;

	$file_name = 'admin_promotions_mail.php';

	AdminMainMenu($lang['promotions']);
	
	$page	= isset($_REQUEST['page']) ?  intval($_REQUEST['page']) : 1;
	$tid	= isset($_REQUEST['tid']) ? $_REQUEST['tid'] : '';
	
	$form['add_hiddens']  = '<input type="hidden" name="sel" value="save_promo" />';
	$form['add_hiddens'] .= '<input type="hidden" name="tid" value="'.$tid.'" />';
	
	if ($tid > 0)
	{
		$strSQL =
			'SELECT DISTINCT a.id, a.name, COUNT(b.id) AS count
			   FROM '.GROUPS_TABLE.' a
		  LEFT JOIN '.USER_GROUP_TABLE.' b ON b.id_group = a.id
		   GROUP BY a.id
		   ORDER BY a.name';
		$rs = $dbconn->Execute($strSQL);
		
		$groups = array();
		$i = 0;
		
		while(!$rs->EOF) {
			$groups[$i]['id'] = $rs->fields[0];
			$groups[$i]['name'] = stripslashes($rs->fields[1]);
			$groups[$i]['count'] = $rs->fields[2];
			$rs->MoveNext();
			$i++;
		}
		
		$smarty->assign('groups', $groups);
		
		$rs = $dbconn->Execute(
			'SELECT title, header, body_text, footer_text
			   FROM '.PROMOTION_TEMPLATES_TABLE.'
			  WHERE id = ?',
			array($tid));
		
		$form['title']			= stripslashes($rs->fields[0]);
		$form['head']			= stripslashes($rs->fields[1]);
		$form['body_text']		= stripslashes($rs->fields[2]);
		$form['footer_text']	= stripslashes($rs->fields[3]);
		
		$form['err']		= $err;
		$form['action']		= $file_name;
		$form['confirm']	= $lang['confirm']['promotions'];
		$form['back']		= 'admin_promotions.php';
		$form['promolink']	= 'admin_promotions_users.php';
		
		$smarty->assign('form', $form);
		$smarty->assign('header', $lang['promotions']);
		$smarty->assign('button', $lang['button']);
		
		$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_promotions_send.tpl');
		exit;
	}
	else
	{
		$err = 'Invalid Template Id';
		PromoList($err);
	}
}

function BrowsePromo($mode, $err = '')
{
	global $smarty, $dbconn, $config, $page, $lang;

	$file_name = 'admin_promotions_mail.php';

	AdminMainMenu($lang['promotions']);
	
	$page	= isset($_REQUEST['page']) ?  intval($_REQUEST['page']) : 1;
	$pid	= isset($_REQUEST['pid']) ? $_REQUEST['pid'] : '';
	
	//$str_sel= isset($_REQUEST['sel']) ? $_REQUEST['sel'] : 'save_promo';
	
	$form['add_hiddens'] = '<input type="hidden" name="sel" value="'.$mode.'" />';
	$form['add_hiddens'].= '<input type="hidden" name="pid" id="pid" value="'.$pid.'" />';
	
	if ($pid > 0)
	{
		$strSQL =
			'SELECT title, header, body_text, footer_text, promo_user, recipient_group, recipient_email,
					status, send_date
			   FROM '.PROMOTION_MAIL_TABLE.'
			  WHERE id = ?';
		$rs = $dbconn->Execute($strSQL, array($pid));
		
		$form['title']			= stripslashes($rs->fields[0]);
		$form['head']			= stripslashes($rs->fields[1]);
		$form['body_text']		= stripslashes($rs->fields[2]);
		$form['footer_text']	= stripslashes($rs->fields[3]);
		$form['promo_user']		= $rs->fields[4];
		$form['recipient_group']= $rs->fields[5];
		$form['recipient_email']= $rs->fields[6];
		$form['status']			= $rs->fields[7];
		$form['send_date']		= $rs->fields[8];
		
		//if form is redirected after validation error
		$arrProUser = array();
		
		if ($err != '') {
			$arrProUser					= $_POST['promo_user'];
			//$arrProUser				= explode(',', $_POST['promo_user']);
			$form['recipient_group']	= ConvertCommaString($_POST['recipient_group']);
			$form['recipient_email']	= $_POST['recipient_email'];
		}
		
		$arrGroup   = explode(',', $form['recipient_group']);
		
		//echo $arrGroup;
		
		$strSQL =
			'SELECT DISTINCT a.id, a.name, COUNT(b.id) AS count
			   FROM '.GROUPS_TABLE.' a
		  LEFT JOIN '.USER_GROUP_TABLE.' b ON b.id_group = a.id
		   GROUP BY a.id
		   ORDER BY a.name';
		$rs = $dbconn->Execute($strSQL);
	
		$groups = array();
		$i = 0;
		
		while(!$rs->EOF)
		{
			$groups[$i]['id']	= $rs->fields[0];
			$groups[$i]['name'] = $rs->fields[1];
			$groups[$i]['count']= $rs->fields[2];
			
			$keyVal = $groups[$i]['id'];
			if (in_array($keyVal, $arrGroup)) {
				$groups[$i]['sele'] = 1;
			}
			
			$i++;
			$rs->MoveNext();
		}
		
		$smarty->assign('groups', $groups);
		
		#$smarty->assign('icon_width', $settings['thumb_max_width']);
		#$default_photos['1'] = $settings['icon_male_default'];
		#$default_photos['2'] = $settings['icon_female_default'];
		
		// Fetch Promo users
		$promo_user = array();
		
		if ($pid != '') {
			$promo_user = FetchPromoUsers($pid);
		}
		
		$arrLen = count($promo_user);
		
		for ($i = 0; $i < $arrLen; $i++) {
			if ($arrProUser) {
				if (!in_array($promo_user[$i]['id'], $arrProUser)) {
					$promo_user[$i]['sele'] = 0;
				}
			}
		}
		
		$smarty->assign('promo_user', $promo_user);
		$smarty->assign('promo_user_count', $i);
		
		$form['err']		= $err;
		$form['action']		= $file_name;
#		$form['confirm']	= $lang['confirm']['promotions'];
		$form['back']		= $file_name;
		$form['mode']		= ($mode == 'send_promo') ? 'view_promo' : $mode;
		//$form['promolink']	= $file_name.'?sel=add_user&id='.$pid;
		$form['promolink']	= 'admin_promotions_users.php';
		
		$smarty->assign('form', $form);
		$smarty->assign('header', $lang['promotions']);
		$smarty->assign('button', $lang['button']);
		
		$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_promotions_send.tpl');
		exit;
	}
	else
	{
		$err = 'Invalid promo mail id';
		PromoList($err);
	}
}

function FetchPromoUsers($pid = '', $user_str = '')
{
	global $smarty, $dbconn, $config, $lang;
	
	$settings = GetSiteSettings(array('icon_male_default', 'icon_female_default'));
	
	$default_photos = array();
	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];
	
	$promo_user = array();
	
	if ($pid != '') {
		$user_list	= $dbconn->GetOne('SELECT promo_user FROM '.PROMOTION_MAIL_TABLE.' WHERE id = ?', array($pid));
	} else {
		$user_list = $user_str;
	}
	
	if ($user_list != '')
	{
		$strSQL =
			'SELECT DISTINCT u.id, u.fname, u.sname, u.gender, u.icon_path, u.date_birthday,
					SUBSTRING(u.about_me, 1, 150),
					u.id_country, u.id_region, u.id_city, m.age_min, m.age_max, m.gender AS gender_search
			   FROM '.USERS_TABLE.' u
		  LEFT JOIN '.USER_MATCH_TABLE.' m ON m.id_user = u.id
			  WHERE id IN ('.$user_list.')';
		$rs = $dbconn->Execute($strSQL);
		
		$i = 0;
		
		while (!$rs->EOF) {
			$promo_user[$i]['id']			= $rs->fields[0];
			$promo_user[$i]['fname']		= stripslashes($rs->fields[1]);
			$promo_user[$i]['sname']		= stripslashes($rs->fields[2]);
			$promo_user[$i]['name']			= stripslashes($rs->fields[1].' '.$rs->fields[2]);
			$promo_user[$i]['gender']		= (int) $rs->fields[3];
			$promo_user[$i]['icon_path']	= $rs->fields[4];
			$promo_user[$i]['profile_link']	= $config['server'].$config['site_root'].'/viewprofile.php?id='.$rs->fields[0];
			$promo_user[$i]['age']			= AgeFromBDate($rs->fields[5]);
			$promo_user[$i]['about_me']		= stripslashes($rs->fields[6]);
			$promo_user[$i]['id_country']	= (int) $rs->fields[7];
			$promo_user[$i]['id_region']	= (int) $rs->fields[8];
			$promo_user[$i]['id_city']		= (int) $rs->fields[9];
			
			$_LANG_NEED_ID					= array();
			$_LANG_NEED_ID['country'][]		= (int) $rs->fields[7];
			$_LANG_NEED_ID['region'][]		= (int) $rs->fields[8];
			$_LANG_NEED_ID['city'][]		= (int) $rs->fields[9];
			
			$promo_user[$i]['age_min']		= (int) $rs->fields[10];
			$promo_user[$i]['age_max']		= (int) $rs->fields[11];
			$promo_user[$i]['gender_search']= $lang['gender_search'][(int) $rs->fields[12]];
			
			if (isset($_LANG_NEED_ID) && count($_LANG_NEED_ID)) {
				$smarty->assign('base_lang', GetBaseLang($_LANG_NEED_ID));
			}
			
			$promo_user[$i]['sele']			= 1;
			
			if ($promo_user[$i]['icon_path']) {
				$icon_path = $promo_user[$i]['icon_path'];
			} else {
				$icon_path = $default_photos[$promo_user[$i]['gender']];
			}
			$promo_user[$i]['icon_path'] = $config['server'].$config['site_root'].'/uploades/icons/'.$icon_path;
			
			$i++;
			$rs->MoveNext();
		}
	}
	
	return $promo_user;
}

function SavePromo()
{
	global $dbconn, $lang;
	
	$file_name = 'admin_promotions_mail.php';

	$pid			 = isset($_POST['pid']) ? $_POST['pid'] : '';
	$title			 = isset($_POST['title']) ? $_POST['title'] : '';
	$head			 = isset($_POST['head']) ? $_POST['head'] : '';
	$body_text		 = isset($_POST['body_text']) ? $_POST['body_text'] : '';
	$footer_text	 = isset($_POST['footer_text']) ? $_POST['footer_text'] : '';
	$promo_user		 = isset($_POST['promo_user']) ? $_POST['promo_user'] : '';
	$groups_arr		 = isset($_POST['recipient_group']) ? $_POST['recipient_group'] : '';
	$recipient_email = isset($_POST['recipient_email']) ? $_POST['recipient_email'] : '';
	#$status		 = isset($_POST['status']) ? $_POST['status'] : 0;
	
	$err = '';
	
	if (empty($promo_user)) {
		$err .= '<br>'.$lang['promotions']['promo_user'];
	}
	
	if (empty($groups_arr) && !strlen($recipient_email)) {
		//$err = $lang['err']['invalid_fields'];
		$err .= '<br>'.$lang['promotions']['group_or_email'];
	}
	
	if ($err) {
		$err = $lang['err']['invalid_fields'].$err;
	}
	
	$str_promo_user = '';
	
	if ($promo_user != '') {
		foreach ($promo_user as $usrs) {
			if ($usrs != 0) {
				if ($str_promo_user != '') {
					$str_promo_user .= ',';
				}
				$str_promo_user .= $usrs;
			}
		}
	}
	
	$recipient_group = '';
	
	if ($groups_arr != '') {
		foreach ($groups_arr as $grps) {
			if ($grps != 0) {
				if ($recipient_group != '') {
					$recipient_group .= ',';
				}
				$recipient_group .= $grps;
			}
		}
	}
	
	if ($err == '')
	{
		if ($pid > 0) {
			$str =
				'UPDATE '.PROMOTION_MAIL_TABLE.'
					SET promo_user = "'.$str_promo_user.'",
						recipient_group = "'.$recipient_group.'",
						recipient_email = "'.$recipient_email.'"
				  WHERE id = "'.$pid.'"';
		} else {
			$str =
			 'INSERT INTO '.PROMOTION_MAIL_TABLE.' (
				 title, header, body_text, footer_text, promo_user, recipient_group, recipient_email
			) VALUES (
				"'.addslashes($title).'", "'.$head.'", "'.addslashes($body_text).'", "'.addslashes($footer_text).'", "'.$str_promo_user.'", "'.$recipient_group.'", "'.$recipient_email.'"
			)';
		}
		
		$dbconn->Execute($str);
		
		echo '<script>location.href="'.$file_name.'"</script>';
		return;
	}
	else
	{
		if ($pid > 0) {
			BrowsePromo('save_promo', $err);
		} else {
			CreatePromo($err);
		}
	}
}


function RefreshUser()
{
	global $dbconn;
	
	$pid	 = isset($_POST['pid']) ? $_POST['pid'] : '';
	$userids = isset($_POST['userids']) ? $_POST['userids'] : '';
	
	/*
	if ($pid == 0) {
		echo '<font class="error_msg">* pid is missing</font><br />';
		die();
	}
	
	if ($userids == '') {
		echo '<font class="error_msg">* userids is missing</font><br />';
		die();
	}
	*/
	/*echo '<font class="error_msg">* IDS: $userids</font><br />';*/
	
	$promo_user = array();
	
	if ($pid == 'new_mail')
	{
		// Fetch Promo users by user id list
		$promo_user = FetchPromoUsers('', $userids);
	}
	else
	{
		$user_list	= $dbconn->GetOne('SELECT promo_user FROM '.PROMOTION_MAIL_TABLE.' WHERE id = ?', array($pid));
		
		if ($user_list) {
			$userids = $userids.','.$user_list;
		}
		
		//converting string to array, selecting unique vaules, re converting to string format
		$arrUser = explode(',', $userids);
		$userids = array_unique($arrUser);
		$userids = ConvertCommaString($userids);
		
		$dbconn->Execute('UPDATE '.PROMOTION_MAIL_TABLE.' SET promo_user = "'.$userids.'" WHERE id="'.$pid.'"');
		
		/*
		if ($dbconn->ErrorNo == 0) {
			$strMsg = '<font class="error_msg">* Success</font><br />';
		} else {
			$strMsg = '<font class="error_msg">* Error</font><br />';
		}
		echo $strMsg; exit;
		*/
		
		// Fetch Promo users by mail id
		$promo_user = FetchPromoUsers($pid);
	}
	
	$arrLen = count($promo_user);
	$HTMLS = '';
	
	if ($arrLen > 0) {
		for ($i = 0; $i < $arrLen; $i++) {
			$HTMLS .= '<p style="padding:0px; margin:0px;">';
			$HTMLS .= '<a href="'.$promo_user[$i]["profile_link"].'" target="_blank"><img src="'.$promo_user[$i]["icon_path"].'" class="icon" alt="'.$promo_user[$i]["name"].'" width="25"></a>';
			$HTMLS .= '<span style="position:relative; top:-12px;"><input type="checkbox" name="promo_user['.$i.']" value="'.$promo_user[$i]["id"].'" checked="checked" />'.$promo_user[$i]["name"].'</span>';
			$HTMLS .= '</p>';
		}
	} else {
		$HTMLS .= 'empty list..';	
	}
	
	echo $HTMLS;
	exit;
}

function SendPromo()
{
	global $smarty, $dbconn, $config, $lang, $debug;
	
	$file_name = 'admin_promotions_mail.php';
	
	$pid = isset($_REQUEST['pid']) ? (int) $_REQUEST['pid'] : 0;
	
	AdminMainMenu($lang['promotions']);
	
	$settings = GetSiteSettings(array('icon_male_default', 'icon_female_default'));
	
	$default_photos = array();
	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];
	
	//$form['add_hiddens'] = '<input type="hidden" name="sel" value="view_promo" />';
	
	if ($pid > 0)
	{
		$rs = $dbconn->Execute(
			'SELECT title, header, body_text, footer_text, promo_user, recipient_group, recipient_email, status
			   FROM '.PROMOTION_MAIL_TABLE.'
			  WHERE id = ?',
			array($pid));
		
		$data['title']			= stripslashes($rs->fields[0]);
		$data['head']			= stripslashes($rs->fields[1]);
		$data['body_text']		= stripslashes($rs->fields[2]);
		$data['footer_text']	= stripslashes($rs->fields[3]);
		$data['promo_user']		= $rs->fields[4];
		$data['recipient_group']= $rs->fields[5];
		$data['recipient_email']= $rs->fields[6];
		$data['status']			= $rs->fields[7];
		
		$rs->Free();
		
		// Promoted profiles
		$promo_user = array();
		$i = 0;
		
		if ($data['promo_user'] != '')
		{
			$strSQL =
				'SELECT DISTINCT u.id, u.fname, u.sname, u.gender, u.icon_path, u.date_birthday,
						substring(u.about_me, 1,150), u.id_country, u.id_region, u.id_city,
						m.age_min, m.age_max, m.gender as gender_search
				   FROM '.USERS_TABLE.' u
			  LEFT JOIN '.USER_MATCH_TABLE.' m ON m.id_user = u.id
				  WHERE id IN ('.$data['promo_user'].')';
			$rs = $dbconn->Execute($strSQL);
			
			while (!$rs->EOF) {
				$promo_user[$i]['id']			= $rs->fields[0];
				$promo_user[$i]['fname']		= stripslashes($rs->fields[1]);
				$promo_user[$i]['sname']		= stripslashes($rs->fields[2]);
				$promo_user[$i]['name']			= stripslashes($rs->fields[1].' '.$rs->fields[2]);
				$promo_user[$i]['gender']		= $rs->fields[3];
				$promo_user[$i]['icon_path']	= $rs->fields[4];
				$promo_user[$i]['profile_link']	= $config['server'].$config['site_root'].'/viewprofile.php?id='.$rs->fields[0];
				$promo_user[$i]['age']			= AgeFromBDate($rs->fields[5]);
				$promo_user[$i]['about_me']		= stripslashes($rs->fields[6]);
				$promo_user[$i]['id_country']	= (int) $rs->fields[7];
				$promo_user[$i]['id_region']	= (int) $rs->fields[8];
				$promo_user[$i]['id_city']		= (int) $rs->fields[9];
				
				$_LANG_NEED_ID					= array();
				$_LANG_NEED_ID['country'][]		= (int) $rs->fields[7];
				$_LANG_NEED_ID['region'][]		= (int) $rs->fields[8];
				$_LANG_NEED_ID['city'][]		= (int) $rs->fields[9];
				
				$promo_user[$i]['age_min']		= (int) $rs->fields[10];
				$promo_user[$i]['age_max']		= (int) $rs->fields[11];
				$promo_user[$i]['gender_search']= $lang['gender_search'][(int) $rs->fields[12]];
				
				if (isset($_LANG_NEED_ID) && count($_LANG_NEED_ID)) {
					$smarty->assign('base_lang', GetBaseLang($_LANG_NEED_ID));
				}
				
				$promo_user[$i]['sele']			= 1;
				
				if ($promo_user[$i]['icon_path']) {
					$icon_path = $promo_user[$i]['icon_path'];
				} else {
					$icon_path = $default_photos[$promo_user[$i]['gender']];
				}
				$promo_user[$i]['icon_path'] = $config['server'].$config['site_root'].'/uploades/icons/'.$icon_path;
				
				$i++;
				$rs->MoveNext();
			}
			$rs->Free();
		}
		
		$smarty->assign('promo_user', $promo_user);
		
		$server['url']		= $config['server'];
		$server['root']		= $config['server'].$config['site_root'];
		$server['img_root']	= $config['server'].$config['site_root'].'/promo_images';
		
		$data['server']		= $server;
		
		$data['urls']		= GetUserEmailLinks();
		
		$smarty->assign('data', $data);
		
		// selecting recipients
		$i = 0;
		$arrUser = array();
		
		// dead branch working with membership groups
		/*
		if ($data['recipient_group'])
		{
			$strSQL =
				'SELECT DISTINCT a.id, a.fname, a.sname, a.email, b.id_group
				   FROM '.USERS_TABLE.' a
			  LEFT JOIN '.USER_GROUP_TABLE.' b ON b.id_user = a.id
				  WHERE b.id_group IN ('.$data['recipient_group'].')';
			$rs = $dbconn->Execute($strSQL);
			
			while(!$rs->EOF) {
				$arrUser[$i]['id']			= $rs->fields[0];
				$arrUser[$i]['fname']		= $rs->fields[1];
				$arrUser[$i]['sname']		= $rs->fields[2];
				$arrUser[$i]['name']		= $rs->fields[1].' '.$rs->fields[2];
				$arrUser[$i]['email']		= $rs->fields[3];
				$arrUser[$i]['id_group']	= $rs->fields[4];
				$i++;
				$rs->MoveNext();
			}
		}
		*/
		
		$arrRecipient = explode(',', $data['recipient_email']);
		
		foreach ($arrRecipient as $email) {
			$arrUser[$i]['email'] = $email;
			$rs = $dbconn->Execute('SELECT fname, sname, site_language FROM '.USER_TABLE.' WHERE email = ?', array($email));
			$arrUser[$i]['fname'] = stripslashes($rs->fields[0]);
			$arrUser[$i]['sname'] = stripslashes($rs->fields[1]);
			$arrUser[$i]['site_language'] = (int) $rs->fields[2];
			$i++;
		}
		
		$email_from		= GetSiteSettings('site_email');
		$subject		= $data['title'];
		
		$k = 1;
		
		foreach ($arrUser as $user)
		{
			//Send Mail to user
			$smarty->assign('user', $user);
			
			// language
			$site_lang = $user['site_language'];
			
			//adding random delay every 20 emails
			if ($k > 20) {
				$k = 1;
				sleep(rand(2, 5));
			}
			
			if ($debug)
			{
				echo count($arrUser).'<br>';
				echo 'Email: '.$user['email'];
				$send_date = date('Y-m-d H:i:s', time());
				echo $send_date;
				$mail_template = $config['index_theme_path'].'/mail_admin_promotions.tpl';
				$smarty->display($mail_template);
				//exit;
			}
			else
			{
				$send_date = date('Y-m-d H:i:s');
				
				$strSQL = 'UPDATE '.PROMOTION_MAIL_TABLE.' SET status = "1", send_date = "'.$send_date.'" WHERE id="'.$pid.'"';
				$dbconn->Execute($strSQL);
				
				$name_to = $user['fname'].' '.$user['sname'];
				
				SendMail($site_lang, $user['email'], $email_from, $subject, $data,
					'mail_admin_promotions', null, $name_to, '');
			}
			
			$k++;
		}
		
		if (!$debug) {
			echo '<script>location.href="'.$file_name.'"</script>';
			return;
		}
	}
	else
	{
		$err = 'Invalid promo mail id';
		PromoList($err);
	}
}


function DeletePromo()
{
	global $dbconn;
	
	$id = intval($_GET['id']);
	
	$err = '';
	
	if (!$id) {
		$err = 'Error in promotion mail deletion';
	} else {
		$dbconn->Execute('DELETE FROM '.PROMOTION_MAIL_TABLE.' WHERE id = ?', array($id));
	}
	PromoList($err);
	return;
}

function ConvertCommaString($arr)
{
	$str = '';
	
	if (count($arr) > 0){
		foreach ($arr as $value) {
			if ($str != '') {
				$str .= ',';
			}
			$str .= $value;
		}
	}
	
	return $str;
}

?>