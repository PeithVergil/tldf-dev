<?php

/**
* Newsletters module functions
*
* @package DatingPro
* @subpackage Include files
**/

if(!function_exists("GetSiteSettings")){
	include_once dirname(__FILE__)."/functions_index.php";
}

function UseModuleNewsletter(){
	global $dbconn, $smarty, $lang, $config;
	$base_use	= GetSiteSettings("use_pilot_module_newsletter");
	if($base_use){
		if(!strlen(NEWSLETTER_ATTACH) || !strlen(NEWSLETTER_CLIENTS_LISTS) || !strlen(NEWSLETTER_FORM) || !strlen(NEWSLETTER_LIST) || !strlen(NEWSLETTER_SETTINGS) || !strlen(NEWSLETTER_TEMPLATES)){
			return false;
		}
		return true;
	}else{
		return false;
	}
}

function GetSiteSubscribeForUser($id_user=0){
	global $dbconn, $smarty, $lang, $config;
	$use_newsletter = UseModuleNewsletter();
	if(!$use_newsletter){
		return "";
	}

	return array();
}

function CreateDatingNewsletterUser($id_user){
	global $dbconn, $smarty, $lang, $config;
	$use_newsletter = UseModuleNewsletter();
	if(!$use_newsletter){
		return "";
	}
	return false;
}

function GetNewsletterIdByDatingId($id_user){
	global $dbconn, $smarty, $lang, $config;
	$use_newsletter = UseModuleNewsletter();
	if(!$use_newsletter){
		return "";
	}
	return false;
}

function UpdateSubscribeListForUser($subscr_arr, $id_user){
	///$subscr_arr mailing list id's for which  user want to subscribe
	global $dbconn, $smarty, $lang, $config;
	$use_newsletter = UseModuleNewsletter();
	if(!$use_newsletter){
		return "";
	}
	return;
}
function UpdateUserDatingMailingList($id_user){
	global $dbconn, $smarty, $lang, $config;
	$use_newsletter = UseModuleNewsletter();
	if(!$use_newsletter){
		return "";
	}
	return;
}
function SetNewsletterUserUnactive($id_user){
	global $dbconn, $smarty, $lang, $config;
	$use_newsletter = UseModuleNewsletter();
	if(!$use_newsletter){
		return "";
	}
	return;
}

function UpdateNewsletterUserData($id_user, $fname, $sname, $email){
	global $dbconn, $smarty, $lang, $config;
	$use_newsletter = UseModuleNewsletter();
	if(!$use_newsletter){
		return "";
	}
	return;
}

?>