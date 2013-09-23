<?php

/**
* Generates administrator menu.
*
* @package DatingPro
* @subpackage Admin Mode
**/

include '../include/config.php';
include_once '../common.php';
include '../include/config_admin.php';
include '../include/functions_admin.php';
include '../include/class.xmlparser.php';

AdminMainMenu();

function AdminXMLMenu($root)
{
	global $config, $lang, $item_uid;
	
	$disabled_subitem = array();
	
	if (GetSiteSettings('user_banners_feature') == 0) {
		$disabled_subitem = array('user_banners', 'banners_settings');
	}
	
	$menu = array();
	
	foreach ($root->children as $cnt => $node) {
		$item_uid ++;
		if (isset($node->tag)) {
			switch ($node->tag) {
				case 'section':
					$menu[$cnt] = array();
					$menu[$cnt][0] = $lang['admin_menu'][$node->attrs['name']];
					$menu[$cnt][1] = '';
					$menu[$cnt][2] = array();
					if ($node->childrenCount > 0) {
						$menu[$cnt][2] = AdminXMLMenu($node);
					}
				break;
				case 'item':
					$menu[$cnt] = array();
					$menu[$cnt][0] = array();
					$menu[$cnt][0][0] = $lang['admin_menu'][$node->attrs['name']];
					$menu[$cnt][0][1] = $config['site_root'].$config['admin_theme_path'].$node->attrs['img'];
					$menu[$cnt][0][2] = $config['site_root'].$node->attrs['href'];
					$menu[$cnt][0][3] = 'item'.$item_uid;
					$menu[$cnt][1] = array();
					if ($node->childrenCount > 0) {
						$menu[$cnt][1] = AdminXMLMenu($node);
					}
				break;
				case 'subitem':
					if (!in_array($node->attrs['name'], $disabled_subitem)) {
						$menu[$cnt] = array();
						$menu[$cnt][0] = isset($lang['admin_menu'][$node->attrs['name']]) ? $lang['admin_menu'][$node->attrs['name']] : '';
						$menu[$cnt][1] = $config['site_root'].$config['admin_theme_path'].$node->attrs['img'];
						$menu[$cnt][2] = $config['site_root'].$node->attrs['href'];
						if (isset($node->attrs['onclick']) && $node->attrs['onclick'] != '') {
							$node->attrs['onclick'] = str_replace('[href]', $menu[$cnt][2], $node->attrs['onclick']);
						} else {
							$node->attrs['onclick'] = '';
						}
						$menu[$cnt][3] = $node->attrs['onclick'];
					}
				break;
			}
		}
	}
	
	return $menu;
}

function AdminJSMenu($menu_arr, $keys = '')
{
	$menu_str = '';
	foreach ($menu_arr as $key => $value) {
		if (is_array($menu_arr[$key])) {
			$menu_str .= 'menuElements'.$keys.'['.$key.'] = new Array();';
			$menu_str .= AdminJSMenu($menu_arr[$key], $keys.'['.$key.']');
		} else {
			$menu_str .= 'menuElements'.$keys.'['.$key.'] = "'.$value.'";';
		}
	}
	return $menu_str;
}

if (isset($_GET['js'])) {

	$xml_parser = new SimpleXmlParser($config['site_path'].$config_admin['admin_menu']);
	$xml_root = $xml_parser->getRoot();
	$menu_arr = array();
	$menu_arr = AdminXMLMenu($xml_root);
	$menu_str = AdminJSMenu($menu_arr);
	unset($xml_parser, $xml_root);
	
	$smarty->assign('menuElements', $menu_str);
	$smarty->display(TrimSlash($config['admin_theme_path']).'/js/menu.js');
}

if (isset($_GET['css'])) {
	header('Content-type: text/css');
	$smarty->display(TrimSlash($config['admin_theme_path']).'/css/menu.css');
}

?>