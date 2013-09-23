<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {html_yes_no} function plugin
 *
 * Type:     function<br>
 * Name:     html_yes_no<br>
 * Date:     March 1, 2004<br>
 * Purpose:  Prints set of radio buttons side by side, essentially with value of<br>
 *                       1/0 [ true/false ], meant for Yes/No style options
 * Input:
 *         - name = name of radio set
 *                        - yes_label = Text for "yes" option [default: Yes]
 *                        - no_label = Text for "no" option [default: No]
 *                        - value = Currently selected radio button
 *                        - default = default value, "yes" if missing [optional]
 *
 * Examples:<br>
 * <pre>
 * {html_yes_no name="is_visible" yes_label="Show this" no_label="Hide this" value=$is_hidden}
 * </pre>
 * @author Mark Hewitt <mark@formfunction.co.za>
 * @version  0.1
 * @param array
 * @param Smarty
 * @return string|null
 */
function smarty_function_html_yes_no($params, &$smarty)
{
	if (!isset($params['value']) && is_numeric($params['value'])) {
		$params['value'] = ( isset($params['default']) && $params['default']='no' ? 0 : 1 );
	}
	
	// detemrine CHECK state of the individual RADIO elements
	$yes_state = (isset($params['value']) && $params['value'] ? 'checked="checked"' : '');
	$no_state = (isset($params['value']) && !$params['value'] ? 'checked="checked"' : '');
	
	// were labels given or must they default in?
	if (!isset($params['yes_label'])) $params['yes_label'] = get_lang('yes');
	if (!isset($params['no_label'])) $params['no_label'] = get_lang('no');
	
	// generate two radio buttons, first Yes button, the next to it separated by spaces
	// the No button
	
	$content = '<input type="radio" name="'.$params['name'].'" value="1" '.$yes_state.' />';
	$content.= '&nbsp;'.$params['yes_label'];
	$content.= '&nbsp;&nbsp;';
	$content.= '<input type="radio" name="'.$params['name'].'" value="0" '.$no_state.' />';
	$content.= '&nbsp;'.$params['no_label'];
	
	return $content;
}
?>
