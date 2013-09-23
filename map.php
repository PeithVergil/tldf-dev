<?php
/**
* Site map
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
include './include/functions_affiliate.php';

// authentication
$user = auth_index_user();

// check guest
// (public access)

// check group, period, expiration
RefreshAccount();

// check status
// (public access)

// check permissions
// (public access)

// active menu item
$smarty->assign('sub_menu_num', '');

Banners(GetRightModulePath(__FILE__));
IndexHomePage();
GetActiveUserInfo($user);

$settings = GetSiteSettings(array('use_horoscope_feature', 'use_success_stories'));

// create array of links by the following scheme
// array_element("name", "link", "level", "is_folder", "is_active");
$map_links = Array();

//-------------------
// Landing Page and General Public Pages
//-------------------

// index
$element_arr = array(
	'name'		=> $lang['top']['index'],
	'link'		=> $config['site_root'].'/index.php',
	'level'		=> 1,
	'is_folder'	=> 0,
	'is_active'	=> 1
);
array_push($map_links, $element_arr);

// register
$element_arr = array(
	'name'		=> $lang['top']['register'],
	'link'		=> $config['site_root'].'/index.php',
	'level'		=> 2,
	'is_folder'	=> 0,
	'is_active'	=> 1
);
array_push($map_links, $element_arr);

/*
// Step 1: Personal Info
## $element_arr = array("name"=>$lang["registration"]["step"]." 1: ".$lang["subsection"]["personal_info"], "link"=>$config["site_root"]."/registration.php?sel=1", "level"=>3, "is_folder"=>0, "is_active"=>1);
## array_push($map_links, $element_arr);
// Step 2: Upload Photo
## $element_arr = array("name"=>$lang["registration"]["step"]." 2: ".$lang["subsection"]["upload"], "link"=>$config["site_root"]."/registration.php?sel=2", "level"=>3, "is_folder"=>0, "is_active"=>0);
## array_push($map_links, $element_arr);
// Step 3: My Description
## $element_arr = array("name"=>$lang["registration"]["step"]." 3: ".$lang["subsection"]["description"], "link"=>$config["site_root"]."/registration.php?sel=3", "level"=>3, "is_folder"=>0, "is_active"=>0);
## array_push($map_links, $element_arr);
// Step 4: My Notice
## $element_arr = array("name"=>$lang["registration"]["step"]." 4: ".$lang["subsection"]["notice"], "link"=>$config["site_root"]."/registration.php?sel=4", "level"=>3, "is_folder"=>0, "is_active"=>0);
## array_push($map_links, $element_arr);
// Step 5: My Personality
## $element_arr = array("name"=>$lang["registration"]["step"]." 5: ".$lang["subsection"]["personal"], "link"=>$config["site_root"]."/registration.php?sel=5", "level"=>3, "is_folder"=>0, "is_active"=>0);
## array_push($map_links, $element_arr);
// Step 6: My Portreit
## $element_arr = array("name"=>$lang["registration"]["step"]." 6: ".$lang["subsection"]["portreit"], "link"=>$config["site_root"]."/registration.php?sel=6", "level"=>3, "is_folder"=>0, "is_active"=>0);
## array_push($map_links, $element_arr);
// Step 7: My Intetersts
## $element_arr = array("name"=>$lang["registration"]["step"]." 7: ".$lang["subsection"]["interest"], "link"=>$config["site_root"]."/registration.php?sel=7", "level"=>3, "is_folder"=>0, "is_active"=>0);
## array_push($map_links, $element_arr);
// Step 8: My Criteria
## $element_arr = array("name"=>$lang["registration"]["step"]." 8: ".$lang["subsection"]["criteria"], "link"=>$config["site_root"]."/registration.php?sel=8", "level"=>3, "is_folder"=>0, "is_active"=>0);
## array_push($map_links, $element_arr);
// Step 9: Her/His Interests
## $element_arr = array("name"=>$lang["registration"]["step"]." 9: ".$lang["subsection"]["match_interest"], "link"=>$config["site_root"]."/registration.php?sel=9", "level"=>3, "is_folder"=>0, "is_active"=>0);
## array_push($map_links, $element_arr);
*/

// member login
$element_arr = array(
	'name'		=> $lang['top']['login'],
	'link'		=> $config['site_root'].'/index.php?sel=login',
	'level'		=> 2,
	'is_folder'	=> 0,
	'is_active'	=> 1
);
array_push($map_links, $element_arr);

// Dating Advice
## $element_arr = array("name"=>$lang["bottom"]["advice"], "link"=>$config["site_root"]."/advice.php", "level"=>2, "is_folder"=>0, "is_active"=>1);
## array_push($map_links, $element_arr);

// Info pages
foreach ($bottom_menu_info as $item) {
	$element_arr = array(
		'name'		=> $item['name'],
		'link'		=> $config['site_root'].$item["link"],
		'level'		=> 2,
		'is_folder'	=> 0,
		'is_active'	=> 1
	);
	array_push($map_links, $element_arr);
}

// tell a friend
## $element_arr = array("name"=>$lang["tell_a_friend"]["top_header"], "link"=>$config["site_root"]."/tell_friend.php", "level"=>2, "is_folder"=>0, "is_active"=>1);
## array_push($map_links, $element_arr);

// News
## $element_arr = array("name"=>$lang["bottom"]["news"], "link"=>$config["site_root"]."/news.php", "level"=>1, "is_folder"=>0, "is_active"=>1);
## array_push($map_links, $element_arr);

// Take A Tour
## $element_arr = array("name"=>$lang["bottom"]["take_tour"], "link"=>$config["site_root"]."/taketour.php", "level"=>1, "is_folder"=>0, "is_active"=>1);
## array_push($map_links, $element_arr);

// Success Stories
## if ($settings["use_success_stories"]) {
##	$element_arr = array("name"=>$lang["bottom"]["stories"], "link"=>$config["site_root"]."/success_stories.php", "level"=>2, "is_folder"=>0, "is_active"=>1);
##	array_push($map_links, $element_arr);
## }

// Frequently Asked Questions
$element_arr = array(
	'name'		=> $lang['section']['faq'],
	'link'		=> $config['site_root'].'/help.php',
	'level'		=> 2,
	'is_folder'	=> 0,
	'is_active'	=> 1
);
array_push($map_links, $element_arr);

// Report A Bug
$element_arr = array(
	'name'		=> $lang['section']['report_a_bug'],
	'link'		=> $config['site_root'].'/report_a_bug.php',
	'level'		=> 2,
	'is_folder'	=> 0,
	'is_active'	=> 1
);
array_push($map_links, $element_arr);

// Request a Call Back
$element_arr = array(
	'name'		=> $lang['section']['request_call_back'],
	'link'		=> $config['site_root'].'/request_call_back.php',
	'level'		=> 2,
	'is_folder'	=> 0,
	'is_active'	=> 1
);
array_push($map_links, $element_arr);

// Express Interest
## $element_arr = array("name"=>$lang["section"]["express_interest"], "link"=>$config["site_root"]."/express_interest.php", "level"=>2, "is_folder"=>0, "is_active"=>1);
## array_push($map_links, $element_arr);

// Send Feedback
$element_arr = array(
	'name'		=> $lang['bottom']['send_feedback'],
	'link'		=> $config['site_root'].'/send_feedback.php',
	'level'		=> 2,
	'is_folder'	=> 0,
	'is_active'	=> 1
);
array_push($map_links, $element_arr);

// Contact Us
$element_arr = array(
	'name'		=> $lang['bottom']['contact'],
	'link'		=> $config['site_root'].'/contact.php',
	'level'		=> 2,
	'is_folder'	=> 0,
	'is_active'	=> 1
);
array_push($map_links, $element_arr);

// Site Map
$element_arr = array(
	'name'		=> $lang['bottom']['map'],
	'link'		=> $config['site_root'].'/map.php',
	'level'		=> 2,
	'is_folder'	=> 0,
	'is_active'	=> 1
);
array_push($map_links, $element_arr);

//--------------------
// Footer Buttons
//--------------------
/*VP
// The "Meet Me Now Bangkok" Story
## $element_arr = array("name"=>$lang["section"]["meet_me_story"], "link"=>$config["site_root"]."/info.php?sel=5", "level"=>2, "is_folder"=>0, "is_active"=>1);
## array_push($map_links, $element_arr);

// Who Relies on "Thai Lady Date Finder"
## $element_arr = array("name"=>$lang["section"]["who_relies"], "link"=>$config["site_root"]."/info.php?sel=6", "level"=>2, "is_folder"=>0, "is_active"=>1);
## array_push($map_links, $element_arr);

// What to Expect From Our System
## $element_arr = array("name"=>$lang["section"]["what_to_expect"], "link"=>$config["site_root"]."/info.php?sel=7", "level"=>2, "is_folder"=>0, "is_active"=>1);
## array_push($map_links, $element_arr);

// The Nasty Truth About Internet Dating
## $element_arr = array("name"=>$lang["section"]["dating_truth"], "link"=>$config["site_root"]."/info.php?sel=8", "level"=>2, "is_folder"=>0, "is_active"=>1);
## array_push($map_links, $element_arr);

// Get the FREE Report
## $element_arr = array("name"=>$lang["section"]["free_report"], "link"=>$config["site_root"]."/free_report.php", "level"=>2, "is_folder"=>0, "is_active"=>1);
## array_push($map_links, $element_arr);
*/

// LogOff
$element_arr = array(
	'name'		=> $lang['logoff'],
	'link'		=> $config['site_root'].'/index.php?sel=logoff',
	'level'		=> 1,
	'is_folder'	=> 0,
	'is_active'	=> 1
);
array_push($map_links, $element_arr);	

//--------------------
// My Profile
//--------------------

// home
$element_arr = array(
	'name'		=> $lang['section']['homepage'],
	'link'		=> $config['site_root']."/homepage.php",
	'level'		=> 2,
	'is_folder'	=> 0,
	'is_active'	=> 1
);
array_push($map_links, $element_arr);

////////////////////
// Members Activity
////////////////////
$element_arr = array("name"=>$lang["homepage"]["mix_mingle_header_1"], "link"=>"", "level"=>3, "is_folder"=>0, "is_active"=>0);
array_push($map_links, $element_arr);

// They Visited My Page
$element_arr = array("name"=>$lang["homepage"]["mix_mingle_text_1_1"], "link"=>$config["site_root"]."/visit_my_page.php", "level"=>4, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// They Sent Me A Kiss
$element_arr = array("name"=>$lang["homepage"]["mix_mingle_text_1_2"], "link"=>$config["site_root"]."/kisses.php?sel=me", "level"=>4, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// They Sent Me An eCard
$element_arr = array("name"=>$lang["homepage"]["mix_mingle_text_1_3"], "link"=>$config["site_root"]."/ecards_me.php", "level"=>4, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// They Emailed Me
$element_arr = array("name"=>$lang["homepage"]["mix_mingle_text_1_4"], "link"=>$config["site_root"]."/emailed_me.php", "level"=>4, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// They Invited Me To Connect
$element_arr = array("name"=>$lang["homepage"]["mix_mingle_text_1_5"], "link"=>$config["site_root"]."/connections.php?sel=inbox", "level"=>4, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

//////////////
// My Activity
//////////////
$element_arr = array("name"=>$lang["homepage"]["mix_mingle_header_2"], "link"=>"", "level"=>3, "is_folder"=>0, "is_active"=>0);
array_push($map_links, $element_arr);

// I Visited Their Page
$element_arr = array("name"=>$lang["homepage"]["mix_mingle_text_2_1"], "link"=>$config["site_root"]."/visit_their_page.php", "level"=>4, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// I Sent Them A Kiss
$element_arr = array("name"=>$lang["homepage"]["mix_mingle_text_2_2"], "link"=>$config["site_root"]."/kisses.php?sel=i", "level"=>4, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// I Sent Them An eCard
$element_arr = array("name"=>$lang["homepage"]["mix_mingle_text_2_3"], "link"=>$config["site_root"]."/ecards_them.php", "level"=>4, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// I Emailed Them
$element_arr = array("name"=>$lang["homepage"]["mix_mingle_text_2_4"], "link"=>$config["site_root"]."/emailed_them.php", "level"=>4, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

//  I Sent Them A Kiss
$element_arr = array("name"=>$lang["homepage"]["mix_mingle_text_2_5"], "link"=>$config["site_root"]."/connections.php?sel=outbox", "level"=>4, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

//////////
// Matches
//////////
$element_arr = array("name"=>$lang["homepage"]["mix_mingle_header_3"], "link"=>"", "level"=>3, "is_folder"=>0, "is_active"=>0);
array_push($map_links, $element_arr);

// They Match My Criteria
$element_arr = array("name"=>$lang["homepage"]["mix_mingle_text_3_1"], "link"=>$config["site_root"]."/meet_them.php", "level"=>4, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// I Match Their Criteria
$element_arr = array("name"=>$lang["homepage"]["mix_mingle_text_3_2"], "link"=>$config["site_root"]."/meet_me.php", "level"=>4, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// horoscope
## if ($settings["use_horoscope_feature"]) {
##	$element_arr = array("name"=>$lang["section"]["horoscope"], "link"=>$config["site_root"]."/horoscope.php", "level"=>3, "is_folder"=>0, "is_active"=>1);
##	array_push($map_links, $element_arr);
## }

//-----------
// My Profile
//-----------
$element_arr = array("name"=>$lang["section"]["profile"], "link"=>$config["site_root"]."/myprofile.php", "level"=>2, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// General Description
$element_arr = array("name"=>$lang["section"]["description"], "link"=>$config["site_root"]."/myprofile.php?sel=1", "level"=>3, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// My Portreit
$element_arr = array("name"=>$lang["section"]["portrait"], "link"=>$config["site_root"]."/myprofile.php?sel=2", "level"=>3, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// My Criteria
$element_arr = array("name"=>$lang["section"]["criteria"], "link"=>$config["site_root"]."/myprofile.php?sel=3", "level"=>3, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// Multimedia Album
$element_arr = array("name"=>$lang["section"]["photo_album"], "link"=>$config["site_root"]."/myprofile.php?sel=4", "level"=>3, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// Icon
$element_arr = array("name"=>$lang["section"]["icon"], "link"=>$config["site_root"]."/myprofile.php?sel=4&amp;sub=7", "level"=>4, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// Photo Gallery
$element_arr = array("name"=>$lang["section"]["photo_gallary"], "link"=>$config["site_root"]."/myprofile.php?sel=4&amp;sub=8", "level"=>4, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// Audio
## $element_arr = array("name"=>$lang["section"]["audio_gallary"], "link"=>$config["site_root"]."/myprofile.php?sel=4&amp;sub=9", "level"=>4, "is_folder"=>0, "is_active"=>1);
## array_push($map_links, $element_arr);

// Video Gallery
$element_arr = array("name"=>$lang["section"]["video_gallary"], "link"=>$config["site_root"]."/myprofile.php?sel=4&amp;sub=10", "level"=>4, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// Tags
## $element_arr = array("name"=>$lang["section"]["tags"], "link"=>$config["site_root"]."/myprofile.php?sel=6", "level"=>3, "is_folder"=>0, "is_active"=>1);
## array_push($map_links, $element_arr);

//-----------
// My Account
//-----------

$element_arr = array("name"=>$lang["top_menu"]["my_account"], "link"=>$config["site_root"]."/account.php", "level"=>2, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

//  My registration Information...
## $element_arr = array("name"=>$lang["account"]["subheader_info"], "link"=>"", "level"=>2, "is_folder"=>1, "is_active"=>0);
## array_push($map_links, $element_arr);

// Change Password
$element_arr = array("name"=>$lang["account"]["subheader_changepass"], "link"=>$config["site_root"]."/account.php?sel=passw", "level"=>3, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

//  Status Of My Account...
## $element_arr = array("name"=>$lang["account"]["subheader_account"], "link"=>"", "level"=>2, "is_folder"=>0, "is_active"=>0);
## array_push($map_links, $element_arr);

//  Modify Group
## $element_arr = array("name"=>$lang["account"]["group_modify"], "link"=>$config["site_root"]."/payment.php", "level"=>3, "is_folder"=>0, "is_active"=>1);
## array_push($map_links, $element_arr);

/* VP 
// My Alerts
## $element_arr = array("name"=>$lang["account"]["subheader_myalerts"], "link"=>"", "level"=>2, "is_folder"=>0, "is_active"=>0);
## array_push($map_links, $element_arr);

// Subscriber
## $element_arr = array("name"=>$lang["account"]["subheader_alerts"], "link"=>"", "level"=>2, "is_folder"=>0, "is_active"=>0);
## array_push($map_links, $element_arr);

// System Messages
## $element_arr = array("name"=>$lang["account"]["subheader_subscribe"], "link"=>"", "level"=>2, "is_folder"=>0, "is_active"=>0);
## array_push($map_links, $element_arr);
*/

// My Alerts
$element_arr = array(
	'name'		=> $lang['account']['subheader_myalerts'],
	'link'		=> $config['site_root'].'/account.php',
	'level'		=> 3,
	'is_folder'	=> 0,
	'is_active'	=> 1
);
array_push($map_links, $element_arr);

//--------------------
// My Multimedia Album
//--------------------
$element_arr = array(
	'name'		=> $lang['top_menu']['my_multimedia_album'],
	'link'		=> $config['site_root'].'/myprofile.php?sel=4',
	'level'		=> 2,
	'is_folder'	=> 0,
	'is_active'	=> 1,
);
array_push($map_links, $element_arr);


//-----------------
// My Photo Gallery
//-----------------
## $element_arr = array("name"=>$lang["top_menu"]["my_photo_gallery"], "link"=>$config["site_root"]."/myprofile.php?sel=4", "level"=>2, "is_folder"=>0, "is_active"=>1);
## array_push($map_links, $element_arr);

//-----------------
// My Video Gallery
//-----------------
## $element_arr = array("name"=>$lang["top_menu"]["my_video_gallery"], "link"=>$config["site_root"]."/myprofile.php?sel=4&amp;sub=10", "level"=>2, "is_folder"=>0, "is_active"=>1);
## array_push($map_links, $element_arr);

//--------------------
// My Blog
//--------------------

if (isset($config['use_pilot_module_blog']) && ($config['use_pilot_module_blog'] == 1)) {
	// My blog
	$element_arr = array('name'=>$lang['blog']['blog_menu_1'], 'link'=>$config['site_root'].'/blog.php', 'level'=>1, 'is_folder'=>1, 'is_active'=>1);
	array_push($map_links, $element_arr);
	//My blog
	$element_arr = array('name'=>$lang['blog']['blog_menu_1'], 'link'=>$config['site_root'].'/blog.php?sel=my_blog', 'level'=>2, 'is_folder'=>0, 'is_active'=>1);
	array_push($map_links, $element_arr);
	//Calendar
	$element_arr = array('name'=>$lang['blog']['blog_menu_2'], 'link'=>$config['site_root'].'/blog_calendar.php', 'level'=>2, 'is_folder'=>0, 'is_active'=>1);
	array_push($map_links, $element_arr);
	//Friends
	$element_arr = array('name'=>$lang['blog']['blog_menu_4'], 'link'=>$config['site_root'].'/blog.php?sel=friends', 'level'=>2, 'is_folder'=>0, 'is_active'=>1);
	array_push($map_links, $element_arr);
}

//--------------------
// My Communication
//--------------------

$element_arr = array("name"=>$lang["index_top_big_2"], "link"=>$config["site_root"]."/homepage.php", "level"=>1, "is_folder"=>0, "is_active"=>0);
array_push($map_links, $element_arr);

// My Connections
$element_arr = array("name"=>$lang["section"]["connections"], "link"=>$config["site_root"]."/connections.php", "level"=>2, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// Invites I've received
$element_arr = array("name"=>$lang["relations"]["connections_inbox"], "link"=>$config["site_root"]."/connections.php?sel=inbox", "level"=>3, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// Invites I've made
$element_arr = array("name"=>$lang["relations"]["connections_outbox"], "link"=>$config["site_root"]."/connections.php?sel=outbox", "level"=>3, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// Hot list
$element_arr = array("name"=>$lang["section"]["friends"], "link"=>$config["site_root"]."/hotlist.php", "level"=>2, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// Mailbox
$element_arr = array("name"=>$lang["section"]["email"], "link"=>$config["site_root"]."/mailbox.php", "level"=>2, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// Inbox
$element_arr = array("name"=>$lang["mailbox"]["inbox"], "link"=>$config["site_root"]."/mailbox.php?sel=inbox", "level"=>3, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// Outbox
$element_arr = array("name"=>$lang["mailbox"]["outbox"], "link"=>$config["site_root"]."/mailbox.php?sel=outbox", "level"=>3, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// Compose
$element_arr = array("name"=>$lang["mailbox"]["compose"], "link"=>$config["site_root"]."/mailbox.php?sel=write", "level"=>3, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// My Ecards
$element_arr = array("name"=>$lang["section"]["ecards"], "link"=>$config["site_root"]."/ecards.php", "level"=>2, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// My Shout Box
## $element_arr = array("name"=>$lang["section"]["shoutbox"], "link"=>$config["site_root"]."/shoutbox.php", "level"=>2, "is_folder"=>0, "is_active"=>1);
## array_push($map_links, $element_arr);

// Black List
$element_arr = array("name"=>$lang["section"]["black_list"], "link"=>$config["site_root"]."/blacklist.php", "level"=>2, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// Report a violaion
$element_arr = array("name"=>$lang["section"]["report_a_violation"], "link"=>$config["site_root"]."/report_a_violation.php", "level"=>2, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// chat
## if ($config["use_pilot_module_flashchat"] == 1) {
##	$element_arr = array("name"=>$lang["section"]["chat"], "link"=>$config["site_root"]."/flash_chat/flashchat.php", "level"=>2, "is_folder"=>0, "is_active"=>1, "is_new_window"=>1);
##	array_push($map_links, $element_arr);
## } elseif ($config["use_pilot_module_webchat"] == 1) {
##	$element_arr = array("name"=>$lang["section"]["chat"], "link"=>$config["site_root"]."/videochat/vchat.php", "level"=>2, "is_folder"=>0, "is_active"=>1, "is_new_window"=>1);
##	array_push($map_links, $element_arr);
## }

// IM
## $element_arr = array("name"=>$lang["section"]["im"], "link"=>$config["site_root"]."/w_communicator/flash_im.php", "level"=>2, "is_folder"=>0, "is_active"=>1, "is_new_window"=>1, "on_click" => "javascript: open_im_window(); return false;");
## array_push($map_links, $element_arr);

// Invite Friends
## if (isset($config["use_pilot_module_invitefriends"]) && $config["use_pilot_module_invitefriends"] == 1) {
##	$element_arr = array("name"=>$lang["section"]["invite_friends"], "link"=>$config["site_root"]."/invite_friends.php", "level"=>2, "is_folder"=>0, "is_active"=>1);
##	array_push($map_links, $element_arr);
## }

//--------------------
// My Searches
//--------------------

$element_arr = array("name"=>$lang["index_top_big_3"], "link"=>$config["site_root"]."/homepage.php", "level"=>1, "is_folder"=>0, "is_active"=>0);
array_push($map_links, $element_arr);

// Quick Search
$element_arr = array("name"=>$lang["section"]["q_search"], "link"=>$config["site_root"]."/quick_search.php", "level"=>2, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// Quick search : Search...
## $element_arr = array("name"=>$lang["search"]["search_1_name"], "link"=>$config["site_root"]."/quick_search.php", "level"=>2, "is_folder"=>1, "is_active"=>1);
## array_push($map_links, $element_arr);

// Quick search : Search by country
## $element_arr = array("name"=>$lang["search"]["country"]." ".$lang["search"]["search_1_name"], "link"=>$config["site_root"]."/quick_search.php", "level"=>3, "is_folder"=>0, "is_active"=>0);
## array_push($map_links, $element_arr);

// Quick search : Search by zip
## $element_arr = array("name"=>$lang["search"]["zipcode"]." ".$lang["search"]["search_1_name"], "link"=>$config["site_root"]."/quick_search.php", "level"=>3, "is_folder"=>0, "is_active"=>0);
## array_push($map_links, $element_arr);

// Quick search : By username
$element_arr = array("name"=>$lang["search"]["search_2_name"], "link"=>$config["site_root"]."/quick_search.php", "level"=>3, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// Quick search : Keyword search
$element_arr = array("name"=>$lang["search"]["search_4_name"], "link"=>$config["site_root"]."/quick_search.php", "level"=>3, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// Quick search : Direct search
$element_arr = array("name"=>$lang["search"]["search_3_name"], "link"=>$config["site_root"]."/quick_search.php", "level"=>3, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// Advanced Search
$element_arr = array("name"=>$lang["section"]["a_search"], "link"=>$config["site_root"]."/advanced_search.php", "level"=>2, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

//--------------------
// Classifieds
//--------------------

if (isset($config["use_pilot_module_classified"]) && ($config["use_pilot_module_classified"] == 1)) {
	// Classifieds Home
	$element_arr = array("name"=>$lang["modules"]["classified"]["name"], "link"=>$config["site_root"]."/classified.php", "level"=>1, "is_folder"=>1, "is_active"=>1);
	array_push($map_links, $element_arr);
	// Add New
	$element_arr = array("name"=>$lang["classified"]["post_ad"], "link"=>$config["site_root"]."/classified.php?sel=add", "level"=>2, "is_folder"=>0, "is_active"=>1);
	array_push($map_links, $element_arr);
	// All
	$element_arr = array("name"=>$lang["classified"]["all_ads"], "link"=>$config["site_root"]."/classified.php", "level"=>2, "is_folder"=>0, "is_active"=>1);
	array_push($map_links, $element_arr);
	// My Ads
	$element_arr = array("name"=>$lang["classified"]["my_ads"], "link"=>$config["site_root"]."/classified.php?sel=my_ads", "level"=>2, "is_folder"=>0, "is_active"=>1);
	array_push($map_links, $element_arr);
}

// Perfect Match
## $element_arr = array("name"=>$lang["section"]["perfect_match"], "link"=>$config["site_root"]."/perfect_match.php", "level"=>1, "is_folder"=>0, "is_active"=>1);
## array_push($map_links, $element_arr);

/*VP
if (isset($config["use_pilot_module_organizer"]) && ($config["use_pilot_module_organizer"] == 1)) {
	// Personal organizer
	$element_arr = array("name"=>$lang["organizer"]["page_title"], "link"=>$config["site_root"]."/organizer.php", "level"=>1, "is_folder"=>1, "is_active"=>1);
	array_push($map_links, $element_arr);
	//My bookmarks
	$element_arr = array("name"=>$lang["organizer"]["my_bookmarks"], "link"=>$config["site_root"]."/organizer.php?sel=bookmarks", "level"=>2, "is_folder"=>0, "is_active"=>1);
	array_push($map_links, $element_arr);
	if (isset($config["use_pilot_module_events"]) && $config["use_pilot_module_events"] == 1) {
		//My Events Statistics
		$element_arr = array("name"=>$lang["organizer"]["my_events_stats"], "link"=>$config["site_root"]."/organizer.php", "level"=>2, "is_folder"=>0, "is_active"=>1);
		array_push($map_links, $element_arr);
	}
	//Billing History
	$element_arr = array("name"=>$lang["organizer"]["billing_history"], "link"=>$config["site_root"]."/organizer.php?sel=billing", "level"=>2, "is_folder"=>0, "is_active"=>1);
	array_push($map_links, $element_arr);
	//Homepage management
	$element_arr = array("name"=>$lang["organizer"]["homepage_management"], "link"=>$config["site_root"]."/organizer.php?sel=homepage_management", "level"=>2, "is_folder"=>0, "is_active"=>1);
	array_push($map_links, $element_arr);
}
*/

// Forum Module
if (isset($config["use_pilot_module_forum"]) && $config["use_pilot_module_forum"] == 1) {
	$element_arr = array("name"=>$lang["section"]["forum"], "link"=>$config["site_root"]."/forum.php", "level"=>1, "is_folder"=>0, "is_active"=>1);
	array_push($map_links, $element_arr);
}

// Events Module
if (isset($config["use_pilot_module_events"]) && $config["use_pilot_module_events"] == 1) {
	$element_arr = array("name"=>$lang["section"]["events"], "link"=>$config["site_root"]."/events.php", "level"=>1, "is_folder"=>0, "is_active"=>1);
	array_push($map_links, $element_arr);
}

// Giftshop Module
if (isset($config["use_pilot_module_giftshop"]) && $config["use_pilot_module_giftshop"] == 1) {
	$element_arr = array("name"=>$lang["section"]["giftshop"], "link"=>$config["site_root"]."/giftshop.php", "level"=>1, "is_folder"=>0, "is_active"=>1);
	array_push($map_links, $element_arr);
}

// Club Module
if (isset($config["use_pilot_module_club"]) && $config["use_pilot_module_club"] == 1) {
	$element_arr = array("name"=>$lang["section"]["club"], "link"=>$config["site_root"]."/club.php", "level"=>1, "is_folder"=>0, "is_active"=>1);
	array_push($map_links, $element_arr);
}

//--------------------
// Thai Lady Dating Events
//--------------------

$element_arr = array("name"=>$lang["section"]["dating_events"], "link"=>$config["site_root"]."/myprofile.php", "level"=>1, "is_folder"=>0, "is_active"=>0);
array_push($map_links, $element_arr);

// Request Information Pack
$element_arr = array("name"=>$lang["section"]["request_info"], "link"=>$config["site_root"]."/request_info.php", "level"=>2, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// Thai Lady Dating Events™ Program
$element_arr = array("name"=>$lang["section"]["dating_events_program"], "link"=>$config["site_root"]."/dating_events.php", "level"=>2, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

// Book My Thai Lady Dating Events™ Program
$element_arr = array("name"=>$lang["section"]["events_booking"], "link"=>$config["site_root"]."/events_booking.php", "level"=>2, "is_folder"=>0, "is_active"=>1);
array_push($map_links, $element_arr);

//--------------------
// Multimedia Gallery
//--------------------

$element_arr = array(
	'name'		=> $lang['section']['gallary'],
	'link'		=> $config['site_root'].'/gallary.php',
	'level'		=> 1,
	'is_folder'	=> 0,
	'is_active'	=> 1
);
array_push($map_links, $element_arr);

AffiliateSiteMap($map_links, $element_arr);

$smarty->assign("map_links", $map_links);

if (!isset($include)) {
	$smarty->display(TrimSlash($config["index_theme_path"])."/map.tpl");
}

?>