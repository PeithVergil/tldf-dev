<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty upper case first modifier plugin
 *
 * Type:     modifier<br>
 * Name:     ucfirst<br>
 * Purpose:  convert first letter of string to uppercase
 * @link http://smarty.php.net/manual/en/language.modifier.upper.php
 * @author   Down Home Consulting www.downhomeconsulting.com
 * @param string
 * @return string
 */
function smarty_modifier_ucfirst($string)
{
    return ucfirst($string);
}

?>
