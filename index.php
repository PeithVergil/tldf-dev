<?php
/**
* Index site page (site page data, login and logout functions)
*
* @package DatingPro
* @subpackage User Mode
**/

include './include/config.php';
include './common.php';
include './include/config_index.php';
include './include/config_admin.php';
include './include/functions_auth.php';
include './include/functions_index.php';
include './include/class.images.php';
include './include/class.news.php';
include './include/class.lang.php';				// registration
include './include/class.percent.php';			// registration
include './include/class.ip_info.php';			// registration
include './include/class.phpmailer.php';		// registration
include './include/functions_mail.php';			// registration
include './include/functions_users.php';		// registration
include './include/functions_newsletter.php';	// registration
include './include/functions_affiliate.php';	// registration
include_once'./include/fb.php';						//facebook login include



//session_destroy();
$debug = false;

CheckInstallFolder();

// authentication
$user = auth_index_user();

// check guest
// (public access)

// check group, period, expiration
RefreshAccount();

//SH2

	$smarty->assign('fb_login_url', $login_url);
	$smarty->assign('fb_logout_url', $logout_url);
	
	if($debug) echo $fbuser;	

// check status
// (public access)

// check permissions
// (public access)

$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

if ($sel != 'logoff')
{
	//RS: maybe move cookie login to functions_auth.php, to handle it the same way as token login
	if (isset($_COOKIE['dp_login']) && isset($_COOKIE['dp_pass']))
	{
		// cookie login
		$rs = $dbconn->Execute('SELECT id FROM '.USERS_TABLE.' WHERE login = ? AND password = ?', array($_COOKIE['dp_login'], $_COOKIE['dp_pass']));
		if ($rs->fields[0] > 0) {
			sess_write(session_id(), $rs->fields[0]);
			$user = auth_index_user();
		}
		$rs->free();
	}
	
	if ($debug) { print_r($user); echo '<br>'; }
	
	if (!headers_sent() && isset($_POST['remember_me'])) {
		// keep signed in for 10 days = 60 * 60 * 24 * 10 = 864000
		setcookie('dp_login', $_POST['login_lg'], time()+864000);
		setcookie('dp_pass', md5($_POST['pass_lg']), time()+864000);
	}
}
else
{
	// destroy cookies
	setcookie('dp_login', '', time()-7200);
	setcookie('dp_pass', '', time()-7200);
}

// registered user, redirect to requested page or homepage
//
if (!empty($user) && $user != 'err' && $user[ AUTH_ID_USER ] && !$user[ AUTH_GUEST ] && ($sel == '' || $sel == 'login'))
{
	if ($debug) echo 'registered user<br />';
	
	$_SESSION['language_cd'] = $user[ AUTH_SITE_LANGUAGE ] ? $user[ AUTH_SITE_LANGUAGE ] : $config['default_lang'];
	
	if (isset($_SESSION['return_to_view']['get_str']) && $_SESSION['return_to_view']['get_str'] != '' 
	|| isset($_SESSION['return_to_view']['type']) && $_SESSION['return_to_view']['type'] == 'other')
	{
		if ($debug) { print_r($_SESSION['return_to_view']); echo 'call redirectToViewed()<br />'; exit; }
		redirectToViewed();
		// exits when redirected
	}
	else
	{
		// display profile
		if (isset($_POST['pid']) && $_POST['pid'] != '') {
			header('Location: '.$config['site_root'].'/viewprofile.php?id='.$_POST['pid']);
			exit;
		}
		
		// redirect applicant to signup sandbox
		if ($user[ AUTH_IS_APPLICANT ]) {
			if ($debug) { echo 'applicant detected, redirect to myprofile.php<br />'; exit; }
			header('Location: '.$config['site_root'].'/myprofile.php');
			exit;
		}
		
		// display profile after login
		if ($debug) { echo 'non-applicant detected, redirect to homepage.php<br />'; exit; }
		header('Location: '.$config['site_root'].'/homepage.php');
		exit;
	}
}

$err = '';

if ($user == 'err') {
	$sel = 'login';
	$err = $lang['err']['login_bad'];
} elseif ($user == 'err_token_expired') {
	$sel = 'login';
	$err = 'The login token is expired.<br><br>Please sign in by providing username and password.';
} elseif ($user == 'err_token_invalid') {
	$sel = 'login';
	$err = 'The login token is invalid or expired.<br><br>Please sign in by providing username and password.';
}

// dispatcher
switch ($sel) {
	case 'logoff': LogoutUser(); break;	
	case 'login': LoginUser($err); break;	// Login Form
	case 'save_1': SaveProfile(); break;	// SavePersonalRegistrationForm
	case 'fb_login':FbLogin(); break;       //If user logs in through FB. SH2
	default: IndexPage(); break;
}

exit;

/**
 * SH 2  FB Login
 */
 
function FbLogin(){
global $fbuser, $login_url, $logout_url, $user_profile;
	
 if ($fbuser) {
	 
	 global $smarty;
	 
	  //echo $logout_url."<br>";
	  $smarty->assign('fbuser', $fbuser);
	 	if(!IsRegistered($user_profile['email'])){
		$pass = SaveFbRegistrationForm($user_profile);
		$_SESSION['fpass'] = $pass ;
		echo '<script>location.href="./myprofile.php";</script>';
	exit;
		//LogoutUser(1);
		/*echo"<script>alert('Congrats You have Created account with us, A mail is sent to you with Login Details.'); window.location = 'index.php?sel=login'; FB.logout(); </script>";*/

		}else{ 
		
		LogoutUser(1);
		echo"<script>alert('You have already account with us.Please login with the details sent to you in email.');window.location = 'index.php?sel=login';</script>";
	 	
		}

	}
 else {
	 
 	 header('location: index.php');
 } 

}

/**
 * logout
 **/
function LogoutUser( $flag = 0 ) //Flag indicates weather the user wants to redirect to index(0) or return to calling function.
{
	global $config, $dbconn, $user, $fbuser, $logout_url;
	
	
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$dbconn->Execute('DELETE FROM '.ACTIVE_SESSIONS_TABLE.' WHERE id_user = ? AND session = ?', array($id_user, session_id()));
	
	// do not show online alerts to other users if user is offline
	$dbconn->Execute('DELETE FROM '.ONLINE_NOTICE_TABLE.' WHERE id_from = ? AND type = "1"', array($id_user));
	
	// remove chat invites
	$dbconn->Execute('DELETE FROM chat_request_log WHERE from_id = ? OR to_id = ?', array($id_user, $id_user));
	
	// Start RASHMI
	setcookie('dp_login', '', time()-7200);
	setcookie('dp_pass', '', time()-7200);
	// start ralf
	// Unset all of the session variables.
	$_SESSION = array();
	// If it's desired to kill the session, also delete the session cookie.
	// Note: This will destroy the session, and not just the session data!
	if (ini_get('session.use_cookies')) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
	}
	// end ralf
	// Finally, destroy the session.
	@session_destroy();
	// End RASHMI
	
	if(!$flag){
		header('Location: index.php');
	exit;
	}
	else{
		return '';
	}
}

/**
 * display explicit login form
 **/
function LoginUser($err = '')
{
	global $smarty, $lang, $config, $user,$fbuser;
	
	
	if ($err) {
		$form['err'] = $err;
	}
	
	if (!is_array($user)) {
		$user = auth_guest_read();
	}
	
	IndexHomePage();
	GetActiveUserInfo($user);
	
	if (isset($_SESSION['return_to_view']['type']) && ($_SESSION['return_to_view']['type'] == 'viewprofile'
	|| $_SESSION['return_to_view']['type'] == 'gallary')) {
		lastViewed();
	}
	
	if ($user == 'err') {
		$form['login_err'] = '1';
	}
	
	//Start RASHMI
	$form['pid'] = isset($_GET['id']) ? $_GET['id'] : '';
	//End RASHMI
	if($fbuser){ echo"<script>FB.logout();</script>"; }
	$smarty->assign('header', $lang['home_page']);
	$smarty->assign('form', $form);
	$smarty->assign('alt', $lang['alt']);
	$smarty->assign('err', $lang['err']);
	$smarty->display(TrimSlash($config['index_theme_path']).'/index_login_form.tpl');
	exit;
}

/**
 * landing page
 **/
function IndexPage()
{
	global $smarty, $lang, $config, $dbconn, $user, $settings;
	
	$IpInfo = new IpInfo();
	
	if (!isset($_COOKIE['language_cd']) && GetSiteSettings('lang_ident_feature')) {
		$id_lang_by_ip = $IpInfo->id_lang;
		if ($id_lang_by_ip > 0 && $config['default_lang'] != $id_lang_by_ip) {
			header('location: index.php?language_code='.$id_lang_by_ip);
		}
	}
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	$smarty->assign('lang_link', GetLangs());
	
	$ip_info['country'] = $IpInfo->country;
	$ip_info['flag_path'] = $IpInfo->flag_path;
	$smarty->assign('ip_info', $ip_info);
	
	$settings = GetSiteSettings(array('max_age_limit', 'min_age_limit', 'icon_male_default', 'icon_female_default', 'icons_folder',
	'zip_count', 'thumb_max_width', 'thumb_max_height','use_ffmpeg', 'video_folder', 'video_default', 'date_format'));
	
	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];
	
	//SH signup form start
	if (isset($_SESSION['error'])) {
		$err		= $_SESSION['error'];
		$err_field	= $_SESSION['err_field'];
		$_POST		= $_SESSION['data'];
		unset($_SESSION['error']);
		unset($_SESSION['err_field']);
		unset($_SESSION['data']);
		$smarty->assign('err_field', $err_field);
	}
	
	$form = PersonalRegistrationForm();
	//SH signup form end
	
	if (!empty($err)) {
		$form['err'] = $err;
	}
	
	#$form['signup_action'] = './registration.php';
	$form['signup_action'] = './index.php';
	$form['search_action'] = './quick_search.php';
	$form['search_hiddens'] = '<input type="hidden" name="sel" value="search" />';
	$form['search_hiddens'] .= '<input type="hidden" name="flag_country" value="0" />';
	$form['register_link'] = './index.php';
	//$form['tell_friend_link'] = './tell_friend.php';
	$form['online_link'] = './quick_search.php?sel=search_on';
	$form['login_link'] = './index.php?sel=login';
	$form['lost_passw_link'] = './lost_pass.php';
	$form['search_type'] = 1;
	
	if ($user == 'err') {
		$form['login_err'] = '1';
	}
	
	$smarty->assign('header', $lang['home_page']);
	$smarty->assign('form', $form);
	$smarty->assign('alt', $lang['alt']);
	$smarty->assign('err', $lang['err']);
	
	$smarty->assign('settings', $settings);
	$smarty->assign('left_menu', $lang['section']);

	$news = GetLastNews(5);
	
	if (!empty($news) && is_array($news)) {
		foreach ($news as $key => $n)
		{
			$news[$key]['text'] = mb_substr(strip_tags($n['title']), 0, 50).'...';
			$news[$key]['news_small'] = mb_substr(($n['news_text']), 0, 250).'...';
			if (!strlen(utf8_decode($news[$key]['text'])))
			{
				$news[$key]['text'] = mb_substr(strip_tags($n['news_text']), 0, 450).'...';
			}
			$news[$key]['date'] = $n['date_add'];
			$news[$key]['link_read'] = GetNewsReadLink($n['id']);
		}
		$smarty->assign('news', $news);
	} else {
		$smarty->assign('news', 'empty');
	}
	
	// WordPress blog
	// $posts = GetBlogPosts(5);
	// $smarty->assign('posts', $posts);
	
	// Landing Featured users
	$strSQL =
		'SELECT u.id, u.fname, u.gender, u.date_birthday, u.big_icon_path, u.id_country, u.what_i_do, ug.id_group
		   FROM '.USERS_TABLE.' u
	 INNER JOIN '.USER_GROUP_TABLE.' ug ON u.id = ug.id_user
	 INNER JOIN '.USER_PRIVACY_SETTINGS.' up ON u.id = up.id_user
		  WHERE u.status = "1" AND u.confirm = "1" AND up.promotion_1 != "1" AND up.featured_land = "1"
	   GROUP BY u.id
	   ORDER BY RAND()
		  LIMIT 8;';
	
	$rs = $dbconn->Execute($strSQL);
	
	$i = 1;
	$feature_user = array();
	$_LANG_NEED_ID = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$feature_user[$i]['name']			= $row['fname'];
		$feature_user[$i]['gender']			= $row['gender'];
		$feature_user[$i]['age']			= AgeFromBDate($row['date_birthday']);
		$feature_user[$i]['icon_path']		= $row['big_icon_path'];
		$feature_user[$i]['profile_link']	= './viewprofile.php?id='.$row['id'];
		$feature_user[$i]['id_country']		= intval($row['id_country']);
		$feature_user[$i]['what_i_do']		= substr($row["what_i_do"],0,150);
		$feature_user[$i]['id_group']		= $row['id_group'];
		$feature_user[$i]['id']				= $row['id'];
		//$icon_path = str_replace('big_thumb_', '', $feature_user[$i]['icon_path']);
		$icon_path = $feature_user[$i]['icon_path'];
		
		if ($icon_path && file_exists($config['site_path'].$settings['icons_folder'].'/'.$icon_path)) {
			$feature_user[$i]['icon_path'] = $config['site_root'].$settings['icons_folder'].'/'.$icon_path;
		} else {
			$file = $default_photos[$feature_user[$i]['gender']];
			$feature_user[$i]['icon_path'] = '.'.$settings['icons_folder'].'/'.$file;
		}
		
		$_LANG_NEED_ID['country'][] = intval($feature_user[$i]['id_country']);
		
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('feature_user', $feature_user);
	
	if (isset($_LANG_NEED_ID)) {
		$smarty->assign('base_lang', GetBaseLang($_LANG_NEED_ID));
	}
	
	$smarty->assign('Version_B', false);
	$smarty->display(TrimSlash($config['index_theme_path']).'/index_home_page.tpl');
	exit;
}

function SaveProfile()
{
	//strip_magic_quotes_gpc();
	
	$err = SavePersonalRegistrationForm();
	
	if ($err) {
		$_SESSION['error'] = $err;
		$_SESSION['data']  = $_POST;
		header('Location: index.php');
		exit;
	}
	
	// $_POST contains sanitized form input
	$data = $_POST;
	
	// submit to newsletter service
	if ($data['gender'] == GENDER_FEMALE)
	{
		//Creating array for GetResponse (Lady user)
		$resData['webform_id']			= '38741';
		$resData['name']				= html_entity_decode($data['fname']);
		$resData['custom_FirstName']	= html_entity_decode($data['fname']);
		$resData['custom_LastName']		= html_entity_decode($data['sname']);
		$resData['email']				= html_entity_decode($data['email']);
	}
	else
	{
		//Creating array for GetResponse (Guy user)
		$resData['webform_id']			= '41168';
		$resData['name']				= html_entity_decode($data['fname']);
		//$resData['custom_FirstName']	= html_entity_decode($data['fname']);
		$resData['custom_LastName']		= html_entity_decode($data['sname']);
		$resData['email']				= html_entity_decode($data['email']);
	}

	//submitting form to GetResponse
	submitToGetResponse($resData);

	/* SH: echo '<script>location.href="./myprofile.php?vdo=y";</script>';
	// RS: headers already sent by curl in submitToGetResponse(...), so we can't use header('Location: ...') here
	//header('Location: myprofile.php');*/
	echo '<script>location.href="./myprofile.php";</script>';
	exit;
}

function GetLastUploades()
{
	global $smarty, $config, $dbconn;
	
	$upload_folder = GetSiteSettings('photos_folder');
	
	// multi-lang tables
	$multi_lang = new MultiLang();
	
	//get last photos
	$strSQL =
		'SELECT DISTINCT uu.id, uu.upload_path, gc.id, rl.'.$multi_lang->DefaultFieldName().' as name, ut.login, uu.id_user, uu.is_adult, ual.title
		   FROM '.USER_UPLOAD_TABLE.' uu
	  LEFT JOIN '.GALLERY_CATEGORIES_TABLE.' gc ON gc.id = uu.id_gallery
	  LEFT JOIN '.USERS_TABLE.' ut ON ut.id = uu.id_user
	  LEFT JOIN '.REFERENCE_LANG_TABLE.' rl ON gc.id=rl.id_reference AND rl.table_key="'.$multi_lang->TableKey(GALLERY_CATEGORIES_TABLE).'"
	  LEFT JOIN '.USER_ALBUMS.' ual ON ual.id = uu.id_album
		  WHERE uu.is_gallary="1" AND uu.status="1" AND uu.allow="1" AND is_adult!="1" AND uu.upload_type="f"
	   GROUP BY uu.id
	   ORDER BY uu.id DESC
		  LIMIT 0,6';
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$last_uploads = array();
	while (!$rs->EOF) {
		$last_uploads[$i]['id'] = $rs->fields[0];
		$last_uploads[$i]['is_adult'] = $rs->fields[6];
		$last_uploads[$i]['link_type'] = 3;
		$last_uploads[$i]['view_link'] = 'gallary.php?sel=view_upload&amp;upload_type=f&amp;id='.$last_uploads[$i]['id'];
		if (isset($rs->fields[1]) && $rs->fields[1] != '' && file_exists($config['site_path'].$upload_folder.'/thumb_'.$rs->fields[1])) {
			$last_uploads[$i]['upload_path'] = $config['server'].$config['site_root'].$upload_folder.'/thumb_'.$rs->fields[1];
		}
		$last_uploads[$i]['category_id'] = $rs->fields[2];
		$last_uploads[$i]['category_name'] = stripslashes($rs->fields[3]);
		$last_uploads[$i]['author_login'] = stripslashes($rs->fields[4]);
		$last_uploads[$i]['author_id'] = $rs->fields[5];
		
		$last_uploads[$i]['album_title'] = stripslashes($rs->fields[7]);
		if (strlen(utf8_decode($last_uploads[$i]['album_title']) > 15)) {
			$last_uploads[$i]['album_title'] = utf8_substr($last_uploads[$i]['album_title'], 0, 15);
		}
		$rs->MoveNext();
		$i++;
	}
	#print '<hr>last uploads'; print_r($last_uploads);
	$smarty->assign('last_uploads', $last_uploads);
	return;
}


function GetClubCategories($limit = 11)
{
	global $smarty, $dbconn;
	
	$strSQL = 'SELECT DISTINCT id, name FROM '.CLUB_CATEGORIES_TABLE.' GROUP BY id ORDER BY name LIMIT 0,'.$limit;
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] > 0){
		$i = 0;
		$cat_arr = array();
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$cat_arr[$i]['id'] = $row['id'];
			$cat_arr[$i]['name'] = htmlspecialchars(stripslashes($row['name']));
			$cat_arr[$i]['link'] = 'club.php?sel=list&amp;id_cat='.$cat_arr[$i]['id'];
			$rs->MoveNext();
			$i++;
		}
		#print '<hr>club categories'; print_r($cat_arr);
		$smarty->assign('club_categories', $cat_arr);
	}
	return;
}


function GetLastEvents($count=5)
{
	global $config, $dbconn;
	
	$today = date('Y-m-d');
	$events = array();
	
	$strSQL =
		'SELECT DISTINCT id, DATE_FORMAT(start_date, "'.$config['date_format'].'") AS s_date, event_name, event_place, event_contain,
				DATE_FORMAT(start_date,"%Y") AS date_y, DATE_FORMAT(start_date,"%m") AS date_m, DATE_FORMAT(start_date,"%d") AS date_d
		   FROM '.EVENT_DESCR_TABLE.'
		  WHERE (DATE_FORMAT(start_date,"%Y-%m-%d") <= "'.$today.'") AND ("'.$today.'" <= die_date OR die_date = "0000-00-00")
	   GROUP BY id
	   ORDER BY start_date DESC
	      LIMIT 0,'.$count;
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		if (strlen(utf8_decode($row['event_name'])) < 40) {
			$events[$i]['name'] = stripslashes($row['event_name']);
		} else {
			$events[$i]['name'] = stripslashes(utf8_substr($row['event_name'], 0, 40).'...');
		}
		if (strlen(utf8_decode($row['event_place'])) < 40) {
			$events[$i]['place'] = stripslashes($row['event_place']);
		} else {
			$events[$i]['place'] = stripslashes(utf8_substr($row['event_place'], 0, 40).'...');
		}
		if (strlen(utf8_decode($row['event_contain']) < 80)) {
			$events[$i]['contain'] = stripslashes($row['event_contain']);
		} else {
			$events[$i]['contain'] = stripslashes(utf8_substr($row['event_contain'], 0, 80).'...');
		}
		$events[$i]['start_date'] = $row['s_date'];
		
		$events[$i]['id_event'] = $row['id'];
		$events[$i]['link'] = './events.php?sel=date&amp;year='.$row['date_y'].'&amp;month='.$row['date_m'].'&amp;day='.$row['date_d'].'#'.$events[$i]['id_event'];
		$rs->MoveNext();
		$i++;
	}
	#print '<hr>events'; print_r($events);
	return $events;
}


function GetVideos()
{
	global $config, $smarty, $dbconn, $settings;
	
	$strSQL =
		'SELECT DISTINCT a.id, a.upload_path, a.user_comment, u.login, a.id_user, a.id_album
		   FROM '.USER_UPLOAD_TABLE.' a
	  LEFT JOIN '.USERS_TABLE.' u ON u.id = a.id_user
		  WHERE a.upload_type="v" AND a.allow="1" AND a.status="1"
		  LIMIT 0,3';
	$rs = $dbconn->Execute($strSQL);
	if (!$rs->EOF) {
		$video = array();
		$i = 0;
		while (!$rs->EOF) {
			$video[$i]['id'] = $rs->fields[0];
			$new_file_name_arr = explode('.', $rs->fields[1]);
			
			$video[$i]['image'] = $new_file_name_arr[0].'1.jpg';
			
			if (file_exists($config['site_path'].$settings['video_folder'].'/'.$video[$i]['image'])) {
				$video[$i]['image'] = $config['server'].$config['site_root'].$settings['video_folder'].'/'.$video[$i]['image'];
			} else {
				$video[$i]['image'] = $config['server'].$config['site_root'].$settings['video_folder'].'/'.$settings['video_default'];
			}
			
			if (strlen(utf8_decode($rs->fields[2])<18)) {
				$video[$i]['user_comment'] = stripslashes($rs->fields[2]);
			} else {
				$video[$i]['user_comment'] = stripslashes(utf8_substr($rs->fields[2], 0, 18).'...');
			}
			$video[$i]['login'] = stripslashes($rs->fields[3]);
			$video[$i]['id_user'] = stripslashes($rs->fields[4]);
			$video[$i]['link'] = 'viewprofile.php?id='.$video[$i]['id_user'].'&amp;view=video&amp;id_album='.intval($rs->fields[5]).'&amp;id_v='.$video[$i]['id'];
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign('video_path',$settings['video_folder']);
		#print '<hr>videos'; print_r($video);
		$smarty->assign('video', $video);
	}
	return;
}


function GetSuccessStories($limit = 6)
{
	global $dbconn, $config;
	
	$photos_folder = GetSiteSettings('success_folder');
	$strSQL =
		'SELECT id, couple_name, story_title, image_path_1, DATE_FORMAT(story_date,"'.$config['date_format'].'") AS story_date
		   FROM '.SUCCESS_STORIES_TABLE.'
		  WHERE image_path_1 != ""
	   ORDER BY story_date
		  LIMIT 0,'.$limit;
	$rs = $dbconn->Execute($strSQL);
	$story = array();
	$i = 0;
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$story[$i]['id'] = $row['id'];
		$story[$i]['couple_name'] = stripslashes($row['couple_name']);
		$story[$i]['story_title'] = utf8_substr(stripslashes($row['story_title']),0,12).'...';
		if (file_exists($config['site_path'].$photos_folder.'/'.$row['image_path_1'])) {
			$story[$i]['thumb_path'] = $config['site_root'].$photos_folder.'/thumb_'.$row['image_path_1'];
			$story[$i]['view_link'] = './success_stories.php';
		}
		$story[$i]['story_date'] = $row['story_date'];
		$rs->MoveNext();
		$i++;
	}
	#print '<hr>stories'; print_r($story);
	return $story;
}

?>