<?php

/**
* Newsletters module functions
*
* @package DatingPro
* @subpackage Include files
**/

include_once 'functions_common.php';
include_once 'class.ads_catalog.php';

function UseModuleClassified(){
	global $dbconn, $smarty, $lang, $config;
	$base_use	= GetSiteSettings("use_pilot_module_classified");
	if(!$base_use){
		return false;
	}
}

function EditCatForm($id_cat=0, $id_curr){
	global $objCat, $smarty, $lang, $config;

	if ($id_cat>0) {
		$cat=$objCat->getCatObject($id_cat, OT_CATEGORY);
	}
	
	$cats=$objCat->getCatalog();

	$form['hiddens']='<input type="hidden" name="id_cat" value="'.$id_cat.'">
					<input type="hidden" name="id_curr" value="'.$id_curr.'">';
	
	//Links
	$form['action']='./admin_classified.php?sel=save_cat';
	
	//Vars
	$smarty->assign('cat', $cat);
	$smarty->assign('cats', $cats);
	$smarty->assign('id_curr', $id_curr);
	$smarty->assign('object_type', OT_CATEGORY);
	$smarty->assign('form', $form);
	$smarty->assign('header', $lang['catalog']);
	$smarty->assign('button', $lang['button']);
	return $smarty->fetch(TrimSlash($config['admin_theme_path']).'/ajax_catalog.tpl');
}

function EditObjectForm($id_object, $id_root, $get_post=false){
	global $objCat, $smarty, $lang, $config, $objModules;

	if ($id_object>0){
		$object_data=$objCat->getCatObject($id_object, OT_OBJECT);
		$object_data['body']=bbdecode($object_data['body']);
	}
	else $object_data=false;
	
	$cats=$objCat->getCatalog(ROOT_CAT);
	unset($cats[ROOT_CAT]);
	
	$form['hiddens']='<input type="hidden" name="id_object" value="'.$id_object.'">';
	
	//Get form
	$ado_form=$objCat->getCatForm($id_root, 'add_object', $id_object, $get_post);

	//Links
	$form['action']='./admin_classified.php?sel=save_object';
	
	//Vars
	$smarty->assign('object', $object_data);
	$smarty->assign('cats', $cats);
	$smarty->assign('id_object', $id_object);
	$smarty->assign('id_root', $id_root);
	$smarty->assign('object_type', OT_OBJECT);
	$smarty->assign('fields', $ado_form['fields']);
	$smarty->assign('js_code', $ado_form['js']);
	$smarty->assign('form', $form);
	$smarty->assign('header', $lang['catalog']);
	$smarty->assign('button', $lang['button']);
	$smarty->assign("user_template_root", $config["index_theme_path"]);
	return $smarty->fetch(TrimSlash($config['admin_theme_path']).'/ajax_catalog.tpl');
}

function getUsersData($id_users){
	global $dbconn, $config, $lang, $user;

	if (is_array($id_users)) $where_str=" WHERE a.id IN (".implode(', ', $id_users).")";
		else $where_str=" WHERE a.id=".intval($id_users);
		
	$settings=GetSiteSettings(array("icon_male_default", "icon_female_default", "icons_folder"));
	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];

	$strSQL="SELECT a.id, a.fname, a.sname, a.login, a.icon_path, a.icon_path_temp, a.gender, a.root_user, a.guest_user, a.date_birthday, a.id_country, a.id_city, a.id_region, 
				c.name AS country_name, r.name AS region_name, ci.name AS city_name, !ISNULL(h.id) AS is_friend, !ISNULL(e.id) AS is_enemy
			FROM ".USERS_TABLE." a
			LEFT JOIN ".COUNTRY_SPR_TABLE." c ON a.id_country=c.id
			LEFT JOIN ".REGION_SPR_TABLE." r ON a.id_region=r.id
			LEFT JOIN ".CITY_SPR_TABLE." ci ON a.id_city=ci.id 
			LEFT JOIN ".HOTLIST_TABLE." h ON h.id_user=".intval($user[ AUTH_ID_USER ])." AND h.id_friend=a.id
			LEFT JOIN ".BLACKLIST_TABLE." e ON e.id_user=".intval($user[ AUTH_ID_USER ])." AND e.id_enemy=a.id
			".$where_str." ORDER BY a.id";
	$rs=$dbconn->Execute($strSQL);

	$users=array();
	while (!$rs->EOF) {
		$row=$rs->GetRowAssoc(false);
		$id_user=intval($row['id']);
		$users[$id_user]['id']=$id_user;
		$users[$id_user]['fname']=$row['fname'];
		$users[$id_user]['sname']=$row['sname'];
		$users[$id_user]['login']=$row['login'];
		$users[$id_user]['root_user']=$row['root_user'];
		$users[$id_user]['guest_user']=$row['guest_user'];
		$users[$id_user]['age']=AgeFromBDate($row["date_birthday"]);
		
		$icon_path=($row['icon_path']!='')?$row['icon_path']:$row['icon_path_temp'];
		if ($icon_path=='' || !file_exists($config['site_path'].$settings['icons_folder'].'/'.$icon_path)){
			$icon_path=$default_photos[$row['gender']];
		}
		$users[$id_user]['icon_path']=$config['server'].$config['site_root'].$settings['icons_folder'].'/'.$icon_path;

		//Friend/Enemy
		$users[$id_user]['is_friend']=intval($row['is_friend']);
		$users[$id_user]['is_enemy']=intval($row['is_enemy']);
		
		//City and etc...
		$users[$id_user]['country']=$row['country_name'];
		$users[$id_user]['region']=$row['region_name'];
		$users[$id_user]['city']=$row['city_name'];

		//Online status
		$sub_strSQL = "SELECT count(*) FROM ".ACTIVE_SESSIONS_TABLE." WHERE id_user='".$id_user."' ";
		$sub_rs = $dbconn->Execute($sub_strSQL);
		$users[$id_user]["status"] = intval($sub_rs->fields[0])?$lang["status"]["on"]:$lang["status"]["off"];
				
		//User group
		$sub_strSQL = "SELECT a.name FROM ".USER_GROUP_TABLE." b 
					LEFT JOIN ".GROUPS_TABLE." a ON a.id=b.id_group WHERE b.id_user='".$id_user."'";
		$sub_rs = $dbconn->Execute($sub_strSQL);
		$groups_arr = array();
		while(!$sub_rs->EOF){
			array_push($groups_arr, $sub_rs->fields[0]);
			$sub_rs->MoveNext();
		}
		if(is_array($groups_arr) && count($groups_arr)>0) $users[$id_user]["group"] = implode(",", $groups_arr);
		
		$rs->MoveNext();
	}
	
	if (is_array($id_users)) return $users; else return $users[$id_users];
}

function getClassifiedsCount($id_cat=ROOT_CAT){
	global $dbconn, $objCat;
	
	$subcats=$objCat->getCatalog($id_cat);
	$all_cats[0]=$id_cat;
	foreach ($subcats as $id_cat=>$cat_data){
		if ($cat_data['obj_count']>0) $all_cats[]=$id_cat;
	}
	
	$strSQL="SELECT COUNT(o.id) FROM ".ADS_CATALOG_TABLE." c
			LEFT JOIN ".ADS_OBJECTS_TABLE." o ON c.id_obj=o.id
			WHERE c.id_cat IN (".implode(', ', $all_cats).") AND c.object_type=".OT_OBJECT." AND c.status=".OS_SHOW;
	$rs=$dbconn->Execute($strSQL);
	
	return intval($rs->fields[0]);
}

function getClassifieds($id_cat=ROOT_CAT, $ids_only=false, $limit_str=''){
	global $dbconn, $objCat, $config, $lang, $user;

	$subcats=$objCat->getCatalog($id_cat);
	$all_cats[0]=$id_cat;
	foreach ($subcats as $id_cat=>$cat_data){
		if ($cat_data['obj_count']>0) $all_cats[]=$id_cat;
	}
	
	$strSQL="SELECT o.id, o.id_user FROM ".ADS_CATALOG_TABLE." c
			LEFT JOIN ".ADS_OBJECTS_TABLE." o ON c.id_obj=o.id
			WHERE c.id_cat IN (".implode(', ', $all_cats).") AND c.object_type=".OT_OBJECT." AND c.status=".OS_SHOW." 
			ORDER BY o.date_created ".$limit_str;
	$rs=$dbconn->Execute($strSQL);
	$ids_cl=array();
	$ids_users=array();
	while (!$rs->EOF) {
		$ids_cl[]=intval($rs->fields[0]);
		$ids_users[]=intval($rs->fields[1]);
		$rs->MoveNext();
	}
	
	if (count($ids_cl)==0) return array();
	if ($ids_only) return $ids_cl;
	
	$classifieds=$objCat->getCatObject($ids_cl, OT_OBJECT);
	$users=getUsersData($ids_users);
	
	foreach ($classifieds as $id=>$data){
		if ($user[ AUTH_ID_USER ] == $data['id_user']) $classifieds[$id]['owner']=true;
		$classifieds[$id]['user']=$users[$data['id_user']];
	}
	return $classifieds;
}

function getClassifiedsByIds($ids){
	global $dbconn, $objCat, $config, $lang, $user;

	if (is_array($ids) && count($ids)==0) return array();
	if (!is_object($objCat)){
		$objCat=new Catalog($dbconn, $config, $lang);;
	}
	
	$strSQL="SELECT o.id, o.id_user FROM ".ADS_CATALOG_TABLE." c
			LEFT JOIN ".ADS_OBJECTS_TABLE." o ON c.id_obj=o.id
			WHERE o.id IN (".implode(', ', $ids).") AND c.object_type=".OT_OBJECT." AND c.status=".OS_SHOW." 
			ORDER BY o.date_created";
	$rs=$dbconn->Execute($strSQL);
	$ids_cl=array();
	$ids_users=array();
	while (!$rs->EOF) {
		$ids_cl[]=intval($rs->fields[0]);
		$ids_users[]=intval($rs->fields[1]);
		$rs->MoveNext();
	}
	
	if (count($ids_cl)==0) return array();
	
	$classifieds=$objCat->getCatObject($ids_cl, OT_OBJECT);
	$users=getUsersData($ids_users);
	
	foreach ($classifieds as $id=>$data){
		if ($user[ AUTH_ID_USER ] == $data['id_user']) $classifieds[$id]['owner']=true;
		$classifieds[$id]['user']=$users[$data['id_user']];
	}
	return $classifieds;
}

function getUserClassifieds($id_user, $ids_only=false){
	global $dbconn, $objCat, $config, $lang;

	if (!is_object($objCat)){
		$objCat=new Catalog($dbconn, $config, $lang);;
	}
	
	$strSQL="SELECT o.id FROM ".ADS_CATALOG_TABLE." c
			LEFT JOIN ".ADS_OBJECTS_TABLE." o ON c.id_obj=o.id
			WHERE c.object_type=".OT_OBJECT." AND o.id_user=".$id_user." ORDER BY o.date_created";
	$rs=$dbconn->Execute($strSQL);
	$ids_cl=array();
	while (!$rs->EOF) {
		$ids_cl[]=intval($rs->fields[0]);
		$rs->MoveNext();
	}
	
	if ($ids_only) return $ids_cl;
	
	$classifieds=$objCat->getCatObject($ids_cl, OT_OBJECT);

	return $classifieds;
}
?>