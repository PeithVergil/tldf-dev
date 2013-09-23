<?php

/**
* Include file, initiates adodb and smarty libraries.
* Set smarty path-and-environment variables.
* Set language and modules settings.
*
* @package DatingPro
* @subpackage User Mode
**/

//RS: Redirect email to admin. It's better to define a constant for this than to set site_email in the
//    settings table, because the setting is also used for sending emails. Depending on security settings
//    of the SMTP server, the SMTP server can block sending emails from an email address with a different
//    domain name than the site.
define('REDIRECT_ADMIN_EMAIL_TO', 'nchitrakar@alucio.com');
define('REDIRECT_ADMIN_EMAIL', true);

define('GOOGLE_TAG_MANAGER', false);

/*
echo '<table width="100%" height="100%" border="0">';
echo '<tr><td align="center" valign="center" style="color:red;">';
echo '<h1>';
echo '<p>We are just updating the system.</p>';
echo '<p>Please visit us a little bit later ...</p>';
echo '<p>Thank you</p>';
echo '</h1>';
echo '</td></tr></table>';
exit;
*/
// additional constants for Meet Me Now

define('TIMEOUT_SECONDS', 3600);

// genders
define('GENDER_MALE', 1);
define('GENDER_FEMALE', 2);

// connection status
define('CS_NOTHING', 0);
define('CS_CONNECTED', 1);
define('CS_SENT', 2);
define('CS_RECEIVED', 3);

// users
define('ID_ADMIN', 1);

// groups
define('MM_ADMIN_GROUP_ID', 1);
define('MM_GUEST_GROUP_ID', 2);
define('MM_SIGNUP_GUY_ID', 5);
define('MM_SIGNUP_LADY_ID', 6);
define('MM_REGULAR_GUY_ID', 7);
define('MM_REGULAR_LADY_ID', 8);
define('MM_MODERATORS', 9);					// not in use
define('MM_PLATINUM_GUY_ID', 10);
define('MM_PLATINUM_LADY_ID', 11);
define('MM_ELITE_GUY_ID', 12);
define('MM_ELITE_LADY_ID', 13);
define('MM_TRIAL_GUY_ID', 14);
define('MM_TRIAL_LADY_ID', 15);
define('MM_INACT_REGULAR_GUY_ID', 16);
define('MM_INACT_REGULAR_LADY_ID', 17);
define('MM_INACT_PLATINUM_GUY_ID', 18);
define('MM_INACT_PLATINUM_LADY_ID', 19);
define('MM_INACT_ELITE_GUY_ID', 20);
define('MM_INACT_ELITE_LADY_ID', 21);
define('MM_INACT_TRIAL_GUY_ID', 22);
define('MM_INACT_TRIAL_LADY_ID', 23);

//Added by Narendra
//----------------  Start  ------------------
define('MM_PLATINUM_LADY_APPLIED_ID', 26);
define('MM_PLATINUM_LADY_FIRST_INS_ID', 27);
define('MM_PLATINUM_LADY_SECOND_INS_ID', 28);
define('MM_PLATINUM_LADY_PENDING_ID', 29);
define('MM_PLATINUM_GUY_APPLIED_ID', 30);
//----------------  End    ------------------
//Added By Narendra

define('INVISIBLE_GROUPS', '1,2,3,4,5,6,9');

// period ids
define('MM_TRIAL_GUY_PERIOD_ID', 24);
define('MM_TRIAL_LADY_PERIOD_ID', 25);

//Added by Narendra
//----------------  Start  ------------------
define('MM_PLATINUM_LADY_FIRST_INS_PERIOD_ID', 27);
define('MM_PLATINUM_LADY_SECOND_INS_PERIOD_ID', 28);
define('MM_PLATINUM_LADY_THIRD_INS_PERIOD_ID', 29);
//----------------  End    ------------------
//Added By Narendra

define('MM_PLATINUM_GUY_PERIOD_ID', 16);	// Platinum Guy Period for Platinum Upgrade
define('MM_PLATINUM_LADY_PERIOD_ID', 22);	// Platinum Lady Period for Platinum Upgrade

#define('MM_REGULAR_GUY_PERIOD_ID', 5);		// Regular Guy Period used for Platinum Upgrade until approval (12 months)
define('MM_REGULAR_GUY_PERIOD_ID', 26);		// Regular Guy Period used for Platinum Upgrade until approval (0 days = unlimited)
define('MM_REGULAR_LADY_PERIOD_ID', 10);	// Regular Lady Period used for Platinum Upgrade until approval

define('PLATINUM_PAYMENT_TRIGGERS_PLATINUM_APPLIED', true);
define('PLATINUM_GETS_UNLIMITED_CONNECTIONS', false);
define('ELITE_GETS_UNLIMITED_CONNECTIONS', false);
define('RESET_USER_GROUP_ACTION', 'INSTALLMENTS');	// INSTALLMENTS, STAY_IN_GROUP, ON_HOLD, DOWNGRADE
define('USER_CAN_SELECT_MEMBERSHIP_GROUP', false);

// additional constants for cron jobs
define('CJ_REGISTER_COMPLETE_ID', 1);	//Registration Completion reminder to user (NOT IN USE ANY LONGER)
define('CJ_BECOME_PAID_MEMBER_ID', 2);	//Become paid member request to user
define('CJ_PLATINUM_VERIFY_ID', 3);		//Platinum Verification request to admin
define('CJ_ONE_WEEK_ID', 4);			//Only 7 Days Remain reminder to user
define('CJ_TWO_DAYS_ID', 5);			//Only 2 Days Remain reminder to user
define('CJ_LAST_DAY_ID', 6);			//Only 1 Day Remain reminder to user
define('CJ_ACC_EXPIRED_ID', 7);			//Account Has Expired mail to user
define('CJ_RE_JOIN_US_ID', 8);			//Re Join Us Request to user (NOT IN USE)
define('CJ_APPROVE_USER_ID', 9);		//Reminder to admin to approve user profile (NOT IN USE)
define('CJ_SIGNUP_0_2_ID', 10);			//Signup Completion Reminder to user after 24h
define('CJ_SIGNUP_0_3_ID', 11);			//Signup Completion Reminder to user after 72h
define('CJ_SIGNUP_0_4_ID', 12);			////Signup Completion Reminder to user after 168h (7 days)

// miscellaneous
define('MM_APPLICANT_DISABLE_ICON_APPROVE', 1);		// Admin does not need to approve icons uploaded by Applicants
define('MM_DEFAULT_RELATIONSHIP_ID', 2);			//
define('MM_ECARDS_FREE', 1);						// use free ecards. ecard prices still need to be set to 0 for free ecards.
define('MM_ECARDS_MUSIC', 0);						// Enable/disable ecard music feature.
define('MM_DISPLAY_PROFILE_COMPLETION', 0);
define('MM_ENABLE_IM', 1);
define('MM_ENABLE_COUPLES', 0);
define('MM_ENABLE_RATE', 0);
define('MM_ENABLE_SEND_TO_FRIEND', 0);
define('MM_ENABLE_FRIENDS_FRIENDLIST', 0);
define('MM_CHECK_CONNECTION_LIMIT', false);
define('MM_CHECK_EMAIL_LIMIT', false);
define('MM_MAX_CONNECTION_LIMIT_FOR_TRIAL', 4);

//VP Automatic Country and Nationality fill for lady users
define('USE_LADY_COUNTRY_FIX', 1);
define('THAILAND_COUNTRY_CODE', 236);
define('THAI_NATIONALITY_CODE', 45);

//VP
define('ADMIN_EMAIL_PLAT_APPLIED', 'platinumapplyguy@thailadydatefinder.com');
define('EMAIL_EXPRESS_INTEREST', 'apply@thailadydatingevents.com');
define('EMAIL_DATING_EVENTS_ADMIN', 'admin@thailadydatingevents.com');

//RS enable/disable quick search features
define('QUICK_SEARCH_GENDER', 0);
define('QUICK_SEARCH_COUPLE', 0);
define('QUICK_SEARCH_RELATIONSHIP', 0);
define('QUICK_SEARCH_COUNTRY', 0);
define('QUICK_SEARCH_REGION', 0);
define('QUICK_SEARCH_CITY', 0);
define('QUICK_SEARCH_DISTANCE', 0);
define('QUICK_SEARCH_TAGS', 0);
define('QUICK_SEARCH_AVAILABLE_AGE_RANGE', 0);
define('QUICK_SEARCH_WITH_PHOTO_SEARCH', 0);

//RS enable/disable advanced search features
define('ADVANCED_SEARCH_GENDER', 0);
define('ADVANCED_SEARCH_COUPLE', 0);
define('ADVANCED_SEARCH_RELATIONSHIP', 0);
define('ADVANCED_SEARCH_COUNTRY', 0);
define('ADVANCED_SEARCH_REGION', 0);
define('ADVANCED_SEARCH_CITY', 0);
define('ADVANCED_SEARCH_DISTANCE', 0);
define('ADVANCED_SEARCH_AVAILABLE_AGE_RANGE', 0);
define('ADVANCED_SEARCH_WITH_PHOTO_SEARCH', 0);

//RS privacy control
define('VISITED_ME_PRIVACY', 1);
define('VISITED_THEM_PRIVACY', 0);
define('KISSED_ME_PRIVACY', 0);
define('KISSED_THEM_PRIVACY', 0);
define('EMAILED_ME_PRIVACY', 0);
define('EMAILED_THEM_PRIVACY', 0);
define('ECARDS_ME_PRIVACY', 0);
define('ECARDS_THEM_PRIVACY', 0);
define('INVITED_ME_CONNECT_PRIVACY', 0);
define('INVITED_THEM_CONNECT_PRIVACY', 0);
define('HOTLIST_PRIVACY', 0);
define('BLACKLIST_PRIVACY', 0);

//RS visibility control
define('VISITED_ME_VISIBLE', 0);
define('VISITED_THEM_VISIBLE', 0);
define('KISSED_ME_VISIBLE', 0);
define('KISSED_THEM_VISIBLE', 0);
define('EMAILED_ME_VISIBLE', 0);
define('EMAILED_THEM_VISIBLE', 0);
define('ECARDS_ME_VISIBLE', 0);
define('ECARDS_THEM_VISIBLE', 0);
define('INVITED_ME_CONNECT_VISIBLE', 0);
define('INVITED_THEM_CONNECT_VISIBLE', 0);
define('HOTLIST_VISIBLE', 0);
define('BLACKLIST_VISIBLE', 0);

define('UNLIMITED_DATE_END', '2037-12-31 00:00:00');

define('USE_PROFILE_EDIT_IN_SIGNUP_SANDBOX', false);
define('USE_PAYMENT_IN_SIGNUP_SANDBOX', false);

//SH points system
define('POINT_USER_REGISTER', 20);
define('POINT_USER_CONNECTION_INVITE', 7);
define('POINT_USER_CONNECTION_ACCEPT', 7);

// RS product groups
define('PG_SINGLE_CREDIT_POINTS', -1);
define('PG_ECARD', -2);
define('PG_MY_STORE', -3);
define('PG_CONNECTION_INVITE', -4);
define('PG_CONNECTION_ACCEPT', -5);
define('PG_CREDIT_POINTS_PACK', -6);
define('PG_INITIAL_CREDIT_POINT_BONUS', -9);

//RS: video upload and play
define('VIDEO_PLAYER_RTMP', 'flowplayer_scripted');				// flowplayer_scripted, flowplayer_hardcoded
define('VIDEO_PLAYER_PROGRESSIVE_DOWNLOAD', 'mediaelement-js');	// mediaelement-js, HTML5_flowplayer_custom

define('RICH_TEXT_EDITOR', 'TINYMCE');	// SPAW-1, SPAW-2, TINYMCE

// RS: Redirect after a user action like adding to hot list, connection invite, permission denied when trying to write message etc.
// Thus, when the user reloads the page or returns to it (e.g. returning to a list after looking at a profile), the action is not
// invoked again.
define('REDIRECT_AFTER_ACTION', true);

if (strtolower($_SERVER['SERVER_NAME']) == 'www.thailadydatefinder.com')
{
	ini_set('display_errors', '0');
	error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT));
}
elseif (strtolower($_SERVER['SERVER_NAME']) == 'www.dev.thailadydatefinder.com')
{
	ini_set('display_errors', '0');
	error_reporting(E_ALL & ~(E_DEPRECATED | E_STRICT));
}
else
{
	ini_set('display_errors', '1');
	error_reporting(E_ALL & ~(E_DEPRECATED | E_STRICT));
}

// session start
if (!isset($_SESSION)) {
	if (isset($_POST['session_id'])) {
		session_id($_POST['session_id']);
	}
	@session_start();
}

// goto install if basic config data is missing
#if (!$config['server'] && !$config['dbname'] && !$config['dbhost'] && !$config['dbuname']) {
#	echo '<script>location.href="./install/index.php"</script>';
#	exit;
#}

// system evaluation
$config['system'] = (substr(php_uname(), 0, 7) == 'Windows') ? 'win' : 'unix';

// full site path
$dir = $config['site_path'];

if (strlen($dir) == 0) {
	$dir = dirname(__FILE__);
}

// temp path for file uploads
$config['file_temp_path'] = $config['site_path'].'/templates_c';

// php include path
switch ($config['system']) {
	case 'unix':	
		ini_set('include_path', '.:'.$dir.':'.$dir.'/include:'.$dir.'/adodb:'.$dir.'/smarty');
	break;
	case 'win':
		ini_set('include_path', '.;'.$dir.';'.$dir.'/include;'.$dir.'/adodb;'.$dir.'/smarty');
	break;
}

// adodb
include_once 'adodb/adodb-exceptions.inc.php';
include_once 'adodb/adodb.inc.php';
include_once 'adodb/tohtml.inc.php';

function PN_DBMsgError($db='', $prg='', $line=0, $message='Error accesing to the database')
{
	$lcmessage = $message . '<br />' .
	'Program: ' . $prg . ' - ' . 'Line N.: ' . $line . '<br />' .
	'Database: ' . $db->database . '<br> ';
	
	if ($db->ErrorNo() != 0) {
		$lcmessage .= 'Error (' . $db->ErrorNo() . ') : ' . $db->ErrorMsg() . '<br />';
	}
	
	die($lcmessage);
}

$dbconn = ADONewConnection($config['dbtype']);

GLOBAL $ADODB_FETCH_MODE;

$connectString = $config['dbtype'].':'.$config['dbuname'].':'.$config['dbpass'].'@'.$config['dbhost'].'/'.$config['dbname'];
$dbh = $dbconn->PConnect($config['dbhost'], ($config['dbuname']), ($config['dbpass']), $config['dbname']);

$ADODB_FETCH_MODE = ADODB_FETCH_NUM;

if ($dbh === false) {
	error_log('connect string: '.$connectString);
	error_log('error: ' . $dbconn->ErrorMsg());
	// show error and die
	PN_DBMsgError($dbconn, __FILE__ , __LINE__, 'Error connecting to db'.$config['dbname']);
}

// use utf-8 for database connection
$dbconn->Execute('SET NAMES "utf8" COLLATE "utf8_general_ci"');

// smarty
include_once 'smarty/Smarty.class.php';

if (!isset($smarty) || !is_object($smarty)) {
	$smarty = new Smarty;
}

$smarty->force_compile = false;
$smarty->template_dir = $dir; 						// removed for smarty 2.6.18: .'/templates';
$smarty->compile_dir = $dir.'/templates_c';
$smarty->plugins_dir = $dir.'/smarty/plugins';

if (!$smarty) {
	die('Smarty error');
}

// load settings
// removed, as we set them in IndexHomePage(...)
//	"site_top_logotype", "site_logotype_format", "site_banner", "site_banner_format", "site_logotype_width",
//	"site_logotype_height", "site_banner_width", "site_banner_height", "site_banner_color",
// removed, as we had no luck with userplane
//  "use_pilot_module_webmessenger"

try
{
	$rs = $dbconn->Execute(
		'SELECT name, value
		   FROM '.SETTINGS_TABLE.'
		  WHERE name LIKE "use_pilot_module_%" OR
				name IN ("admin_theme_path", "index_theme_path",
				"default_lang", "path_lang", "date_format", "use_success_stories", "use_horoscope_feature",
				"user_banners_feature", "voipcall_feature", "color_theme", "site_email", "use_gender_membership",
				"use_shoutbox_feature")');
}
catch (exception $e)
{
	adodb_backtrace($e->gettrace());
	echo '<pre>'.print_r($e, true).'</pre>';
}

while (!$rs->EOF) {
	$config[$rs->fields[0]] = $rs->fields[1];
	$smarty->assign($rs->fields[0], $rs->fields[1]);
	$rs->MoveNext();
}

$rs->free();

/*
#if (isset($config['use_pilot_module_webmessenger']) && $config['use_pilot_module_webmessenger'] == 1) {
#	$rs = $dbconn->Execute('SELECT name, value FROM '.UP_MESSENGER_SETTINGS_TABLE.' WHERE name IN ("presence_id", "account_password")');
#	while (!$rs->EOF) {
#		$config[$rs->fields[0]] = $rs->fields[1];
#		$rs->MoveNext();
#	}
#	$rs->free();
#	if (!isset($config['presence_id']) || !isset($config['account_password'])) {
#		$config['presence_id'] = '';
#		$config['account_password'] = '';
#	}
#}
*/

$config['admin_theme_path'] = '/templates/admin';
$config['index_theme_path'] = '/templates/dtl_theme_n';

if ($config['system'] == 'win') {
	$smarty->assign('gentemplates', 'file:'.$dir.$config['index_theme_path']);	
	$smarty->assign('admingentemplates', 'file:'.$dir.$config['admin_theme_path']);
} else {
	$smarty->assign('gentemplates', 'file:'.$config['site_path'].$config['index_theme_path']);
	$smarty->assign('admingentemplates', 'file:'.$config['site_path'].$config['admin_theme_path']);
}

// languages
if (!$config['default_lang']) {
	$config['default_lang'] = '1';
}

if (!$config['path_lang']) {
	$config['path_lang'] = '/languages';
}

if (isset($_GET['language_code']) && strlen($_GET['language_code']))
{
	setcookie('language_cd', $_GET['language_code'], time()+7200);
	$lang_code = $_GET['language_code'];
	$config['default_lang'] = $_GET['language_code'];
}
elseif (isset($_COOKIE['language_cd']) && strlen($_COOKIE['language_cd']))
{
	$lang_code = $_COOKIE['language_cd'];
	$config['default_lang'] = $_COOKIE['language_cd'];
}
else
{
	$lang_code = $config['default_lang'];
}

// load language interface
$rs = $dbconn->Execute('SELECT charset, lang_file, code FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($lang_code));
$charset = $rs->fields[0];
$lang_file = $rs->fields[1];
$lang_code = $rs->fields[2];
$rs->free();

if (substr($config['path_lang'], -1) != '/' && substr($config['path_lang'], -1) != "\\") {
	$config['path_lang'] = $config['path_lang'].'/';
}

if (substr($config['path_lang'], 0, 1) == '/' || substr($config['path_lang'], 0, 1) == "\\") {
	$config['path_lang'] = substr($config['path_lang'], 1);
}

if (substr($lang_file, 0, 1) == '/' || substr($lang_file, 0, 1) == "\\") {
	$lang_file = substr($lang_file, 1);
}

$lang = array();
include $config['path_lang'].$lang_file;

$smarty->assign('default_lang', $lang_code);
$smarty->assign('charset', $charset);
$smarty->assign('lang', $lang);
$smarty->assign('site_path', $config['site_path']);
$smarty->assign('site_root', $config['site_root']);
$smarty->assign('server', $config['server'].$config['site_root']);

if (isset($config['offline'])) {
	$smarty->assign('tldf_offline', $config['offline']);
}

//VP checking if it is live or test server
//alternative: strtolower($_SERVER['SERVER_NAME']) == 'www.thailadydatefinder.com' || strtolower($_SERVER['SERVER_NAME']) == 'thailadydatefinder.com'
$is_live_server = ($config['server'] == 'http://www.thailadydatefinder.com' || $config['server'] == 'http://thailadydatefinder.com') ? true : false;

// testing, be careful to comment before you commit
// $is_live_server = true;

define('IS_LIVE_SERVER', $is_live_server);

$is_dev_server = !$is_live_server;

// override for testing
if (isset($config['is_dev_server'])) {
	$is_dev_server = ($config['is_dev_server'] == 1);
}

define('IS_DEV_SERVER', $is_dev_server);

$is_localhost = (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == 'localhost');

define('IS_LOCALHOST', $is_localhost);

// performance log
if (isset($config['logger']) && (int)$config['logger'] == 1) {
	require_once 'include/logger.class.php';
	global $log;
	$log = new logger();
}

##global $_SERVER;

global $FILE_SELF;
$FILE_SELF = basename($_SERVER['PHP_SELF']);

// ms analysis
if ($config['use_pilot_module_msanalysis'] && !isset($msanalysis_ignore)) {
	require_once($config['site_path'].'/msanalysis/counter.php');
	require($config['site_path'].'/msanalysis/mstrack.php');
}

// dynamic css colors
$color = array();
include $dir.$config['index_theme_path'].'/css/config_color.php';

// only used in IndexHomePage(...) in functions_index.php for viewprofile_print.tpl
$config['color'] = $color;

// still in use in many template files
$smarty->assign('css_color', $color);

if (isset($theme_ident)) {
	$smarty->assign('theme_ident', $theme_ident);
}

// payment systems
include_once 'Payment_Config.php';

define('PAYMENT_DIR', $dir.'/include/');
define('SYSTEMS_DIR', $dir.'/include/systems/');

include_once 'Payment_Data_Kipper.php';
include_once 'Payment_Engine.php';

// additional code for Meet Me Now
$file_credit_log = $config['site_path'].'/admin/log/log_deleted_credits';
define('LOG_FILE_DELETED_CREDITS', $file_credit_log);

// SOLVE360
define('SOLVE360_USER', 'admin@meetmenowbangkok.com');
define('SOLVE360_TOKEN', '4bVa8e09G9fam1lbDcF4Hba1e8k621rai2oe35ra');

#define('SOLVE360_CONNECTION', IS_DEV_SERVER && !IS_LOCALHOST);
define('SOLVE360_CONNECTION', IS_LIVE_SERVER);

define('SOLVE360_OWNERSHIP_MMNB', 71383427);

define('SOLVE360_TAG_TLDF', 74873411);

define('SOLVE360_TAG_GUY', 71388596);
define('SOLVE360_TAG_LADY', 71391483);

define('SOLVE360_TAG_SIGN_UP_GUY', 71388597);
define('SOLVE360_TAG_SIGN_UP_LADY', 71391472);
define('SOLVE360_TAG_TRIAL_GUY', 74873899);
define('SOLVE360_TAG_TRIAL_LADY', 74873912);
define('SOLVE360_TAG_REGULAR_GUY', 74873878);
define('SOLVE360_TAG_REGULAR_LADY', 74873889);
define('SOLVE360_TAG_PLATINUM_GUY', 74873917);
define('SOLVE360_TAG_PLATINUM_LADY', 74873925);

define('SOLVE360_TAG_PLATINUM_APPLIED', 75217294);

?>