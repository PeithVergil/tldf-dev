<?php

/**
* Affiliate module functions
*
* @package DatingPro
* @subpackage Include files
**/

if(!function_exists("GetSiteSettings")){
	include_once dirname(__FILE__)."/functions_index.php";
}

function UseModuleAffiliate(){
	global $dbconn, $smarty, $lang, $config;
	$base_use	= GetSiteSettings("use_pilot_module_affiliate");
	if($base_use){
		if(!strlen(AFF_SETTINGS_TABLE) || !strlen(AFF_IP_TMP_TABLE) || !strlen(AFF_CLICK_COUNTER_TABLE) || !strlen(AFF_USER_TABLE) || !strlen(AFF_USER_PRODUCT_SOLD_TABLE) ){
			return false;
		}
		return true;
	}else{
		return false;
	}
}

function AffiliatesPayment($status, $count){
	global $dbconn, $smarty, $lang, $config;
	return "";
}

function AffiliatesRegistration(){
	global $dbconn, $smarty, $lang, $config;
	return "";
}

function TypeAffiliatePayment(){
	global $dbconn, $smarty, $lang, $config;
	return '-1';
}

function AffiliateSiteMap(&$map_links, &$element_arr){
	global $dbconn, $smarty, $lang, $config;
	return "";
}
?>