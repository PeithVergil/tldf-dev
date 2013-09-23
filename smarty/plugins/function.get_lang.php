<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {get_lang} function plugin
 *
 * Type:     function<br>
 * Name:     Get Lang<br>
 * Date:     March 2, 2006<br>
 * Purpose:  Take languave specific texts from database to display
 * @link     To be attached with osdate package and topied to Smarty/plugins directory
 * @author   Vijay Nair <vijay@nairvijay.com>
 * @version  1.0
 * @param    Text_to_check Text for Language Check
 * @return   string
 */

function smarty_function_get_lang($params, &$smarty )
{  global $osDB, $config;
   $mainkey = $params['mainkey'];
   $subkey = $params['subkey'];
   $escape = $params['escape'];
   $optlang= $smarty->session->opt_lang;
   if ($subkey != '') {
	   $y = $osDB->getOne('select descr from ! where lang=? and mainkey= ? and subkey=?', array(LANGUAGE_TABLE, $optlang, $mainkey, $subkey));
   } else {
	   $y = $osDB->getOne('select descr from ! where lang=? and mainkey= ? ', array(LANGUAGE_TABLE, $optlang, $mainkey));
   }
   if (!$y) {
	   if ($subkey != '') {
		   $y = $osDB->getOne('select descr from ! where lang=? and mainkey= ? and subkey=?', array(LANGUAGE_TABLE, 'english', $mainkey, $subkey));
	   } else {
		   $y = $osDB->getOne('select descr from ! where lang=? and mainkey= ? ', array(LANGUAGE_TABLE, 'english', $mainkey));
	   }
   }
	$y = str_replace('SITENAME', SITENAME, $y);
	$y = str_replace('DATE_FORMAT', DATE_FORMAT, $y);
	$y = str_replace('DATE_TIME_FORMAT', DATE_TIME_FORMAT, $y);
	$y = str_replace('DISPLAY_DATE_FORMAT', DISPLAY_DATE_FORMAT, $y);
	$y = str_replace('#TNSIZE#', $config['upload_snap_tnsize'], $y);
	$y = str_replace('#upload_snap_maxsize#', $config['upload_snap_maxsize'], $y);
   if ($escape == 'url') {
		// return urlencode(html_entity_decode($y));
		return urlencode($y);
   } else {
		// return html_entity_decode($y);
		return $y;
   }
}

/* vim: set expandtab: */

?>
