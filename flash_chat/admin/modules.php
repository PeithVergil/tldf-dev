<?php

require_once('init.php');

include_once "../../include/class.object2xml.php";
include_once "../../include/class.xmlparser.php";


$GLOBALS['fc_config']['cms']->LoginCheck(__FILE__);
AdminMainMenu($lang["chats"]);

$sel = isset($_REQUEST["sel"]) ? $_REQUEST["sel"] : "";

$modules = array();

$modules[1]['name'] = 'banner';
$modules[1]['config_name'] = 'banners';

switch($sel){
	case "save":		ChangeStatus(); break;
	case "edit":		EditModule();	break;
	case "edit_banner":	EditBanner();	break;
	case "save_banner":	SaveBanner();	break;
	case "delete_banner":	DeleteBanner();	break;
	default: 			ModulesTable();	break;
}



function ModulesTable() {
	global $lang, $dbconn, $smarty, $modules;

	$strSQL = " SELECT DISTINCT module_id, module_status, id
			FROM {$GLOBALS['fc_config']['db']['pref']}modules
			GROUP BY id ORDER BY id";
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	while(!$rs->EOF){
		$modules[$rs->fields[0]]['status'] = $rs->fields[1];
		$modules[$rs->fields[0]]['title'] = $lang["chats"]["module_name"][$modules[$rs->fields[0]]['name']];
		$modules[$rs->fields[0]]['id'] = $rs->fields[2];
		$modules[$rs->fields[0]]['num'] = $i+1;
		$rs->MoveNext();
		$i++;
	}

	$smarty->assign('modules', $modules);
	$smarty->display('modules.tpl');
}

function ChangeStatus() {
	global $dbconn, $smarty, $modules;

	$strSQL = " UPDATE {$GLOBALS['fc_config']['db']['pref']}modules SET module_status='0' ";
	$dbconn->Execute($strSQL);

	$status_str = '';
	if (isset($_REQUEST['status'])) {
		$status_str = implode(',', $_REQUEST['status']);
	}
	
	if ($status_str != '') {
		$strSQL = " UPDATE {$GLOBALS['fc_config']['db']['pref']}modules SET module_status='1' WHERE id IN (".$status_str.") ";
		$dbconn->Execute($strSQL);
	}
	ModulesTable();
	return;
}

function EditModule($id_module='') {
	global $lang, $dbconn, $smarty, $modules, $config;

	$id_module = isset($_REQUEST['id_module']) ? intval($_REQUEST['id_module']) :  $id_module;

	$strSQL = " SELECT module_id FROM {$GLOBALS['fc_config']['db']['pref']}modules WHERE id='".$id_module."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]<1) {
		ModulesTable();
	} elseif ($rs->fields[0] == 1) {
		$banners = GetBannerArr();
		$smarty->assign('banners', $banners);
		$smarty->display('modules_banner_form.tpl');
	}
	exit;
}

function EditBanner($data=null , $err='') {
	global $lang, $dbconn, $smarty, $modules, $config;
	if ($err != '') {
		$smarty->assign('error', $lang['chats']['errors']['wrong_banner_file']);
	}
	$num_banner = isset($_REQUEST['num_banner']) ? intval($_REQUEST['num_banner']) : 0;
	if ($num_banner == 0) {
		if ($data) {
			$banner = $data;
		} else {
			$banner['num'] = 0;
		}
		$smarty->assign('banner', $banner);
		$smarty->display('modules_banner_edit_form.tpl');
	} else {
		$banners = GetBannerArr();
		if (is_array($banners[$num_banner-1]) && sizeof($banners[$num_banner-1])>0) {
			$smarty->assign('banner', $banners[$num_banner-1]);
			$smarty->display('modules_banner_edit_form.tpl');
		} else {
			ModulesTable();
		}
	}
	exit;
}

function GetBannerArr() {
	global $lang, $dbconn, $smarty, $modules, $config;
	$xml_parser = new SimpleXmlParser($config['site_path']."/flash_chat/modules/banner/config.xml");
	$xml_root = $xml_parser->getRoot();
	foreach ($xml_root->children[0]->children as $node) {
		switch ($node->tag) {
			case "time":
				$data['time'] = $node->value;
				break;
		}
	}

	$xml_parser = new SimpleXmlParser($config['site_path']."/flash_chat/modules/banner/banners.xml");
	$xml_root = $xml_parser->getRoot();
	$banners = array(); $i = 0;
	foreach ($xml_root->children as $node) {
		switch ($node->tag) {
			case "banner":
				$banners[$i]['src'] = $node->attrs['src'];
				$banners[$i]['ext'] = substr($banners[$i]['src'], strlen($banners[$i]['src'])-3, 3);
				$banners[$i]['fading'] = $node->attrs['fading'];
				foreach ($node->children as $node_2){
					switch ($node_2->tag) {
						case "url":
							$banners[$i]['url'] = $node_2->value;
							break;
						case "target":
							$banners[$i]['target'] = $node_2->value;
							break;
					}
				}
				$banners[$i]['num'] = $i+1;
				$i++;
				break;
		}
	}
	return $banners;
}

function SaveBanner() {
	global $config;
	if ($_REQUEST['num'] == 0 ) {
		if (isset($_FILES['banner_file']) && $_FILES['banner_file']['name']!='' && $_FILES['banner_file']['size']!=0 && CheckFileType($_FILES['banner_file']['name']) == 'ok' && is_uploaded_file($_FILES['banner_file']['tmp_name'])) {
			$ext = substr($_FILES['banner_file']['name'], strlen($_FILES['banner_file']['name'])-3, 3);
			$upload_name = md5(time()).".".$ext;
			$upload_path = $config['site_path'].'/flash_chat/modules/banner/banners/'.$upload_name;
			if (copy($_FILES['banner_file']['tmp_name'], $upload_path)){
				$file_path = $config['site_path']."/flash_chat/modules/banner/banners.xml";

				$xml_strings = array();
				$new_xml_strings = array();

				$attrs = array();
				$attrs['src'] = "banners/".$upload_name;
				$attrs['fading'] = ($_REQUEST['fading'] == 1) ? "true" : "false";

				$new_xml_strings[] = new XmlNode('url','', null, htmlspecialchars(trim($_REQUEST['url'])));
				$new_xml_strings[] = new XmlNode('target','', null, htmlspecialchars(trim($_REQUEST['target'])));
				$new_xml_strings[] = new XmlNode('langs','', null, '');
				$new_xml_strings[] = new XmlNode('rooms','', null, '');
				$new_xml_strings[] = new XmlNode('skins','', null, '');

				$xml_strings = new XmlNode('banner', $attrs, $new_xml_strings);

				$xml_parser = new SimpleXmlParser( $file_path );
				$xml_root = $xml_parser->getRoot();

				for ( $i = 0; $i <= $xml_root->childrenCount; $i++ ) {
					if ($i == $xml_root->childrenCount) {
						$xml_root->children[$i] = $xml_strings;
					}
				}
				$xml_root->childrenCount = $i;

				$obj_saver = new Object2Xml();
				$obj_saver->Save( $xml_root, $file_path );

				unset( $new_xml_strings, $xml_strings, $xml_parser, $xml_root, $obj_saver );
				EditModule(1);
			} else {
				EditBanner($_REQUEST, 'upload_err');
			}
		} else {
			EditBanner($_REQUEST, 'wrong_file');
		}
	} else {
		if (isset($_FILES['banner_file']) && $_FILES['banner_file']['name']!='' && $_FILES['banner_file']['size']!=0 && CheckFileType($_FILES['banner_file']['name']) == 'ok' && is_uploaded_file($_FILES['banner_file']['tmp_name'])) {
			$ext = substr($_FILES['banner_file']['name'], strlen($_FILES['banner_file']['name'])-3, 3);
			$upload_name = md5(time()).".".$ext;
			$upload_path = $config['site_path'].'/flash_chat/modules/banner/banners/'.$upload_name;
			if (copy($_FILES['banner_file']['tmp_name'], $upload_path)){
				$upload = true;
			}
		}

		$file_path = $config['site_path']."/flash_chat/modules/banner/banners.xml";
		$xml_parser = new SimpleXmlParser( $file_path );
		$xml_root = $xml_parser->getRoot();

		for ( $i = 0; $i <= $xml_root->childrenCount; $i++ ) {
			if ($i == ($_REQUEST['num']-1)) {
				$was['attrs']['src'] = $xml_root->children[$i]->attrs['src'];
			}
		}

		$new_xml_strings = array();

		$attrs = array();
		if (isset($upload) && $upload == true) {
			$attrs['src'] = "banners/".$upload_name;
		} else {
			$attrs['src'] = $was['attrs']['src'];
		}

		$attrs['fading'] = isset($_REQUEST['fading']) && ($_REQUEST['fading'] == 1) ? "true" : "false";

		$new_xml_strings[] = new XmlNode('url','', null, htmlspecialchars(trim($_REQUEST['url'])));
		$new_xml_strings[] = new XmlNode('target','', null, htmlspecialchars(trim($_REQUEST['target'])));
		$new_xml_strings[] = new XmlNode('langs','', null, '');
		$new_xml_strings[] = new XmlNode('rooms','', null, '');
		$new_xml_strings[] = new XmlNode('skins','', null, '');

		$xml_strings = new XmlNode('banner', $attrs, $new_xml_strings);

		$xml_parser = new SimpleXmlParser( $file_path );
		$xml_root = $xml_parser->getRoot();

		for ( $i = 0; $i <= $xml_root->childrenCount; $i++ ) {
			if ($i == ($_REQUEST['num']-1)) {
				$xml_root->children[$i] = $xml_strings;
			}
		}

		$obj_saver = new Object2Xml();
		$obj_saver->Save( $xml_root, $file_path );

		unset( $new_xml_strings, $xml_strings, $xml_parser, $xml_root, $obj_saver );
		EditModule(1);
	}
}

function DeleteBanner() {
	global $config;
	if (intval($_REQUEST['num_banner']) > 0 ) {
		$num = intval($_REQUEST['num_banner']);
		$file_path = $config['site_path']."/flash_chat/modules/banner/banners.xml";

		$xml_parser = new SimpleXmlParser( $file_path );
		$xml_root = $xml_parser->getRoot();

		for ( $i = 0; $i <= $xml_root->childrenCount; $i++ ) {
			if ($i == ($num-1)) {
				unset($xml_root->children[$i]);
				$del = true;
			}
		}
		if ($del == true) {
			$xml_root->childrenCount = $i-1;
		}

		$obj_saver = new Object2Xml();
		$obj_saver->Save( $xml_root, $file_path );

	}
	EditModule(1);
}

function CheckFileType($file_name) {
	$ext_array = array("jpg", "swf");
	$ext = strtolower(substr($file_name, strlen($file_name)-3, 3));
	if (in_array($ext, $ext_array)) {
		return 'ok';
	} else {
		return false;
	}
}

function GetItemAttr($file_name) {

	$xml_parser = new SimpleXmlParser($file_name);
	$xml_root = $xml_parser->getRoot();

	$_array = array();

	foreach ( $xml_root->children as $node) {
		switch($node->tag) {
			case "banner":
				array_push($_array, $node->attrs);
				break;
		}
	}
	return $_array;
}

?>