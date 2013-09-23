<?php
/**
* Text Chat
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

// authentication
$user = auth_index_user();

if (empty($user) || $user == 'err' || empty($user[ AUTH_ID_USER ])) {
	header('location: '.$config['site_root'].'/index.php');
	exit;
}

// check guest
if ($user[ AUTH_GUEST ]) {
	AlertPage(GetRightModulePath(__FILE__));
}

// check group, period, expiration
RefreshAccount();

// check status
if (!$user[ AUTH_STATUS ]) {
	AlertPage(GetRightModulePath(__FILE__));
}

// check permissions
// (pending)

// alerts and statistics
// (not applicable in colorbox or popup)

// active menu item
// (not applicable in colorbox or popup)

#error_reporting(E_ALL);

$id_user = $user[ AUTH_ID_USER ];

// for setting GA session vars
GetActiveUserInfo($user);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$rs = $dbconn->Execute(
	'SELECT l.id AS log_id, l.from_id, l.to_id, l.status,
			u1.fname AS from_fname, u1.sname AS from_sname, u1.login AS from_login,
			u2.fname AS to_fname, u2.sname AS to_sname, u2.login AS to_login,
			u1.id_city AS from_id_city, u1.id_region AS from_id_region, u1.id_country AS from_id_country,
			u1.date_birthday AS from_date_birthday, u1.icon_path AS from_icon_path, u1.gender AS from_gender,
			u2.id_city AS to_id_city, u2.id_region AS to_id_region, u2.id_country AS to_id_country,
			u2.date_birthday AS to_date_birthday, u2.icon_path AS to_icon_path, u2.gender AS to_gender
	   FROM chat_request_log l
 INNER JOIN user u1 ON l.from_id = u1.id
 INNER JOIN user u2 ON l.to_id = u2.id
	  WHERE l.id = ?',
	array($id));

if ($rs->EOF) {
	echo '<center><h1>The Chat Request '.$id.' was not found.</h1><h1>Please try again ...</h1></center>';
	exit;
}

$row = $rs->GetRowAssoc(false);

#if ($row['status'] != 'Accepted') {
#	echo '<center><h1>The Chat Request '.$id.' was not accepted.</h1><h1>Please try again ...</h1></center>';
#	exit;
#}

$log_id				= $row['log_id'];
$from_id			= $row['from_id'];
$to_id				= $row['to_id'];

$connected_status = getConnectedStatus($from_id, $to_id);

if ($connected_status != CS_CONNECTED) {
	echo '<center><h1>You are not connected with the other user.</h1><h1>Please establish a Connection and then try again ...</h1></center>';
	exit;
}

$from_fname			= $row['from_fname'];
$from_login			= $row['from_login'];

$to_fname			= $row['to_fname'];
$to_login			= $row['to_login'];

$from_id_city		= $row['from_id_city'];
$from_id_region		= $row['from_id_region'];
$from_id_country	= $row['from_id_country'];
$from_date_birthday	= $row['from_date_birthday'];
$from_icon_path		= $row['from_icon_path'];
$from_gender		= $row['from_gender'];

$to_id_city			= $row['to_id_city'];
$to_id_region		= $row['to_id_region'];
$to_id_country		= $row['to_id_country'];
$to_date_birthday	= $row['to_date_birthday'];
$to_icon_path		= $row['to_icon_path'];
$to_gender			= $row['to_gender'];

# $trans_uname = md5($from_login.$log_id.$id_user);

if ($from_id == $id_user)
{
	$login					= $from_login;
	$fname					= $from_fname;
	$friend_login			= $to_login;
#	$rcv_uname				= md5($from_login.$log_id.$to_id);
	$friend_id_user			= $to_id;
	$friend_icon_path		= $to_icon_path;
	$friend_id_country		= $to_id_country;
	$friend_id_region		= $to_id_region;
	$friend_id_city			= $to_id_city;
	$friend_date_birthday	= $to_date_birthday;
	$friend_gender			= $to_gender; 
}
else
{
	$login					= $to_login;
	$fname					= $to_fname;
	$friend_login			= $from_login;
#	$rcv_uname				= md5($from_login.$log_id.$from_id);
	$friend_id_user			= $from_id;
	$friend_icon_path		= $from_icon_path;
	$friend_id_country		= $from_id_country;
	$friend_id_region		= $from_id_region;
	$friend_id_city			= $from_id_city;
	$friend_date_birthday	= $from_date_birthday;
	$friend_gender			= $from_gender; 
}

# NOT IN USE
#$connectionstr	= 'rtmp://184.173.9.143:1935/';
#$room			= md5($from_login.$log_id);
#$uid			= $log_id;
#$cb			= 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title></title>         
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--
Include CSS to eliminate any default margins/padding and set the height of the html element and 
the body element to 100%, because Firefox, or any Gecko based browser, interprets percentage as 
the percentage of the height of its parent container, which has to be set explicitly.  Initially, 
don't display flashContent div so it won't show if JavaScript disabled.
-->
<style type="text/css" media="screen"> 
html, body { height:100%; }
body { margin:0; padding:0; overflow:auto; text-align:center; background-color: #ffffff; }
#flashContent { display:none; }
</style>
<!-- Enable Browser History by replacing useBrowserHistory tokens with two hyphens -->
<!-- BEGIN Browser History required section -->
<link rel="stylesheet" type="text/css" href="<?php echo $config['site_root']; ?>/pvchat/history/history.css" />
<script type="text/javascript" src="<?php echo $config['site_root']; ?>/pvchat/history/history.js"></script>
<!-- END Browser History required section -->
<script type="text/javascript" src="<?php echo $config['site_root']; ?>/pvchat/swfobject.js"></script>
<?php if (isset($config['offline']) && $config['offline']) { ?>
	<script type="text/javascript" src="<?php echo $config['site_root'].$config['index_theme_path']; ?>/js/jquery-1.7.2.min.js"></script>
<?php } else { ?>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
<?php } ?>
<script type="text/javascript">
/* <![CDATA[ */
// For version detection, set to min. required Flash Player version, or 0 (or 0.0.0), for no version detection.
var swfVersionStr				= "11.1.0";
// To use express install, set to playerProductInstall.swf, otherwise the empty string.
var xiSwfUrlStr					= "<?php echo $config['site_root']; ?>/pvchat/playerProductInstall.swf";
var flashvars = {};
<?php if (IS_LIVE_SERVER) { ?>
//flashvars.address				= "50.116.69.235";						// TLDF
//flashvars.room				= "chat/<?php echo $log_id; ?>";		// TLDF
flashvars.address				= "54.251.116.204";						// singapore
flashvars.room					= "videochat/<?php echo $log_id; ?>";	// singapore
<?php } else { ?>
//flashvars.address				= "54.249.239.153";						// tokyo
flashvars.address				= "54.251.116.204";						// singapore
flashvars.room					= "videochat/<?php echo $log_id; ?>";
<?php } ?>
flashvars.ports					= "80,1935";
flashvars.mid					= "<?php echo md5($log_id.$login); ?>";
flashvars.pid					= "<?php echo md5($log_id.$friend_login); ?>";
flashvars.uname					= "<?php echo $fname; ?>";
flashvars.cameraWidth			= 320; // 320
flashvars.cameraHeight			= 240; // 240
flashvars.cameraFps				= 24; // 12
flashvars.cameraBandwidth		= 60000;
flashvars.cameraQuality			= 0;
flashvars.cameraKFrameInterval	= 72; // 48
//flashvars.useH264				= true;
flashvars.micEncodeQuality		= 3; 
flashvars.micEchoPath			= 128; 
flashvars.h264profile			= "baseline";
flashvars.h264level				= "3.1";

var params = {};
params.quality					= "high";
params.bgcolor					= "#ffffff";
params.allowscriptaccess		= "sameDomain";
params.allowfullscreen			= "true";

var attributes = {};
attributes.id					= "Chat";
attributes.name					= "Chat";
attributes.align				= "middle";

swfobject.embedSWF("<?php echo $config['site_root']; ?>/pvchat/PChat.swf", "chatContent", "325", "471", swfVersionStr, xiSwfUrlStr, flashvars, params, attributes);
// JavaScript enabled so display the flashContent div in case it is not replaced with a swf object.
swfobject.createCSS("#chatContent", "display:block;text-align:left;");
/* ]]> */
</script>
</head>
<body>
<?php if (IS_LIVE_SERVER && GOOGLE_TAG_MANAGER) { ?>
<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-JD9Z"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-JD9Z');</script>
<!-- End Google Tag Manager -->
<?php } ?>
<!-- Google Analytics Tracking -->
<div>
	<?php
	$_SESSION['ga_event_code'] = 'chatstarted';
	include $config['site_path'].'/include/tracking.php';
	?>
	<!--
	<input type="hidden" name="_track_gender" id="_track_gender" value="<?php #echo $_SESSION['ga_gender']; ?>" />
	<input type="hidden" name="_track_memberStatus" id="_track_memberStatus" value="<?php #echo $_SESSION['ga_member_status']; ?>" />
	<input type="hidden" name="_track_eventCode" id="_track_eventCode" value="chatstarted" />
	-->
</div>
<!-- Google Analytics Tracking -->
<!--
SWFObject's dynamic embed method replaces this alternative HTML content with Flash content when enough 
JavaScript and Flash plug-in support is available. The div is initially hidden so that it doesn't show
when JavaScript is disabled.
-->
<?php if (isset($_GET['banner']) && $_GET['banner'] == '1') { ?>
	<div><img src="<?php echo $config['site_root']; ?>/pvchat/banner.png" alt="" /></div>
<?php } ?>
<div id="chatContent">
	<script type="text/javascript">
	/* <![CDATA[ */
	document.write('<p>To view this page ensure that Adobe Flash Player version ' + swfVersionStr + ' or greater is installed.<\/p>');
	var pageHost = ((document.location.protocol == "https:") ? "https://" : "http://"); 
	document.write('<a href="http://www.adobe.com/go/getflashplayer"><img src="' + pageHost + 'www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /><\/a>');
	/* ]]> */
	</script>
</div>
<?php if (IS_LIVE_SERVER) { ?>
	<script type="text/javascript" src="<?php echo $config['site_root'].$config['index_theme_path']; ?>/js/google_analytics.js?v=0002"></script>
<?php } ?>
</body>
</html>
