<?php
/**
* Ecards module
*
* @package DatingPro
* @subpackage User Mode
**/

include './include/config.php';
include './common.php';
include './include/config_index.php';
include './include/config_admin.php';
include './include/functions_auth.php';
include './include/functions_index.php';
include './include/class.lang.php';
include './include/class.phpmailer.php';
include './include/functions_mail.php';
include './include/functions_mm.php';

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
IsFileAllowed(GetRightModulePath(__FILE__));

// alerts and statistics
if (!$user[ AUTH_GUEST ]) {
	GetAlertsMessage();
	SetModuleStatistic(GetRightModulePath(__FILE__));
}

// active menu item
$smarty->assign('sub_menu_num', '');

// user selection
$sel = isset($_REQUEST['sel']) ? $_REQUEST['sel'] : '';

// limited access for trial users and inactive users is handled within the module.
// trial and inactive users can only send ecards with a predefined header and text
// so all features are available, but with limited editing capabilities

// dispatcher
switch ($sel) {
	case 'category': CategoryTable(); break;
	case 'cards': SubCategoryTable(); break;
	case 'card': CardTable(); break;
	case 'choose_music': MusicForm(); break;
	case 'save_card': SaveCard(); break;
	case 'preview': PreviewCard(); break;
	case 'select_user': SelectUserForm(); break;
	case 'users_autocomplete': UserAutocomplete(); break;
	case 'check_user': CheckUser(); break;
	case 'save_user': SaveUser(); break;
	case 'order_form': OrderForm(); break;
	case 'pay_from_account': PayFromAccount(); break;
	case 'pay_by_paysys': PayFromPaysystem(); break;
	case 'my_orders': OrdersList(); break;
	case 'order_delete': OrderDelete(); break;
	default: MainTable(); break;
}

exit;


function MainTable()
{
	global $config, $smarty, $dbconn, $user;
	
	
	
	$id_user_to = isset($_GET['id_user_to']) ? (int) $_GET['id_user_to'] : 0;
	$id_order = isset($_GET['id_order']) ? (int) $_GET['id_order'] : 0;
		
	// get id_user_to and user_to_fname
	//
	$data = array();
	
	if ($id_order) {
		$data = GetCardOrderData($id_order);
		if (empty($data)){
			header('Location: '.$config['server'].$config['site_root'].'/ecards.php');
			exit;
		}
	}
	
	if (empty($data['id_user_to']) && $id_user_to) {
		$user_to_fname = $dbconn->getOne('SELECT fname FROM '.USERS_TABLE.' WHERE id = ?', array($id_user_to));
		if (!empty($user_to_fname)) {
			$data['user_to_fname'] = $user_to_fname;
			$data['id_user_to'] = $id_user_to;
		}
	}
	
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$strSQL =
		'SELECT DISTINCT a.id, a.category_name, a.category_image, b.content_name, COUNT(c.id) AS cards_count
		   FROM '.ECARDS_CATEGORIES_TABLE.' a
	  LEFT JOIN '.ECARDS_LANG_CONTENT_TABLE.' b ON b.content_id = a.id AND b.content_type = "1" AND b.id_lang = "'.$config['default_lang'].'"
	  LEFT JOIN '.ECARDS_ITEMS_TABLE.' c ON c.id_category = a.id AND c.status = "1"
	   GROUP BY a.id
	   ORDER BY a.sorter';
		  
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$_arr = array();
	
	if ($rs->fields[0] > 0)
	{
		while (!$rs->EOF)
		{
			$row = $rs->GetRowAssoc(false);
			
			$_arr[$i]['id'] = $row['id'];
			  $_arr[$i]['name'] = $row['content_name'] ? stripslashes($row['content_name']) : stripslashes($row['category_name']);
			$_arr[$i]['cards_count'] = $row['cards_count'];
			
			if ($row['category_image'])
			{
				$_arr[$i]['card_image'] = $config['server'].$config['site_root'].'/uploades/ecards/'.stripslashes($row['category_image']);
			}
			else
			{
				$res = GetRandomImage('category', $_arr[$i]['id']);
				$_arr[$i]['card_image'] = $config['server'].$config['site_root'].'/uploades/ecards/'.$res;
			}
			
			$_arr[$i]['subcategories'] = GetAllSubCategoriesList($_arr[$i]['id']);
			
			$rs->MoveNext();
			$i++;
		}
	}
	
	unset($rs, $row);
	
	$form['categories_count'] = count($_arr);
	
	$num_cat = $form['categories_count'] % 3;
	
	if ($num_cat == 0)
	{
		$form['categories_1_limit'] = $form['categories_2_limit'] = intval($form['categories_count'] / 3);
	}
	elseif ($num_cat == 1)
	{
		$form['categories_1_limit'] = intval($form['categories_count'] / 3) + 1;
		$form['categories_2_limit'] = intval($form['categories_count'] / 3);
	}
	else
	{
		$form['categories_1_limit'] = $form['categories_2_limit'] = intval($form['categories_count'] / 3) + 1;
	}
	
	$form['categories_2_start'] = $form['categories_1_limit'];
	$form['categories_3_start'] = $form['categories_2_start'] + $form['categories_2_limit'];
	
	$smarty->assign('categories', $_arr);
	
	$strSQL =
		'SELECT a.id AS id_order, COUNT(a.id_item) AS card_count, b.card_image, b.card_name, c.content_name, b.id AS id_card
		   FROM '.ECARDS_ORDERS_TABLE.' a
	  LEFT JOIN '.ECARDS_ITEMS_TABLE.' b ON b.id = a.id_item AND b.status = "1"
	  LEFT JOIN '.ECARDS_LANG_CONTENT_TABLE.' c ON c.content_id = a.id_item AND c.content_type = "3" AND c.id_lang = "'.$config['default_lang'].'"
	   GROUP BY a.id_item
	   ORDER BY card_count DESC
	      LIMIT 0,6';
	
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$_arr = array();
	
	if ($rs->fields[0] > 0)
	{
		while (!$rs->EOF)
		{
			$row = $rs->GetRowAssoc(false);
			
			$_arr[$i]['id'] = $row['id_order'];
			$_arr[$i]['item_id'] = $row['id_card'];
			
			if ($row['card_image'] != '' && file_exists($config['site_path'].'/uploades/ecards/thumb_'.stripslashes($row['card_image']))) {
				$_arr[$i]['card_image_thumb'] = $config['server'].$config['site_root'].'/uploades/ecards/thumb_'.stripslashes($row['card_image']);
			}
			
			$_arr[$i]['card_name'] = $row['content_name'] ? stripslashes($row['content_name']) : stripslashes($row['card_name']);
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign('most_ordered', $_arr);
	}
	
	$smarty->assign('data', $data);
	$smarty->assign('form', $form);
	$smarty->display(TrimSlash($config['index_theme_path']).'/ecards_table.tpl');
	exit;
}


function UserAutocomplete()
{
	global $dbconn;
	
	if (empty($_GET['term'])) {
		return;
	}
	
	$search_str = $_GET['term'].'%';
	
	$strSQL =
		'SELECT id, fname
		   FROM '.USERS_TABLE.'
		  WHERE fname LIKE ? AND status = "1" AND visible = "1" AND root_user != "1" AND guest_user != "1"';
	$rs = $dbconn->Execute($strSQL, array($search_str));
	
	$i = 0;
	$users = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$users[$i]['id'] = $row['id'];
		$users[$i]['label'] = $row['fname'];
		$users[$i]['value'] = $row['fname'];
		if ($i > 15) {
			break;
		}
		$rs->MoveNext();
		$i++;
	}
	
	echo json_encode($users);
	return;
}

function CategoryTable()
{
	global $smarty, $config, $dbconn, $user;
	
	$id_category = isset($_GET['id_category']) && (int) $_GET['id_category'] ? (int) $_GET['id_category'] : null;
	$id_user_to = isset($_GET['id_user_to']) ? (int) $_GET['id_user_to'] : 0;
	$id_order = isset($_GET['id_order']) ? (int) $_GET['id_order'] : 0;
	
	$data = array();
	
	if ($id_order) {
		$data = GetCardOrderData($id_order);
		if (empty($data)) {
			header('Location: '.$config['server'].$config['site_root'].'/ecards.php');
			exit;
		}
	}
	
	if (empty($data['id_user_to']) && $id_user_to) {
		$user_to_fname = $dbconn->getOne('SELECT fname FROM '.USERS_TABLE.' WHERE id = ?', array($id_user_to));
		if (!empty($user_to_fname)) {
			$data['user_to_fname'] = $user_to_fname;
			$data['id_user_to'] = $id_user_to;
		}
	}
	
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$strSQL =
		'SELECT a.id, a.category_name, b.content_name, a.category_descr, b.content_body
		   FROM '.ECARDS_CATEGORIES_TABLE.' a
	  LEFT JOIN '.ECARDS_LANG_CONTENT_TABLE.' b ON b.content_id = a.id AND b.content_type = "1" AND b.id_lang = "'.$config['default_lang'].'"
		  WHERE a.id = ?';
	
	$rs = $dbconn->Execute($strSQL, array($id_category));
	
	if ($rs->fields[0] > 0) {
		$row = $rs->GetRowAssoc(false);
		$form['id_category'] = $row['id'];
		$form['category_name'] = $row['content_name'] ? stripslashes($row['content_name']) : stripslashes($row['category_name']);
		$form['category_descr'] = $row['content_body'] ? stripslashes($row['content_body']) : stripslashes($row['category_descr']);
	} else {
		header('Location: '.$config['server'].$config['site_root'].'/ecards.php');
		exit;
	}
	
	unset($rs, $row);
	
	$strSQL =
		'SELECT DISTINCT a.id, a.subcategory_name, a.subcategory_image, a.subcategory_descr, b.content_name, b.content_body, COUNT(c.id) AS cards_count
		   FROM '.ECARDS_SUBCATEGORIES_TABLE.' a
	  LEFT JOIN '.ECARDS_LANG_CONTENT_TABLE.' b ON b.content_id = a.id AND b.content_type = "2" AND b.id_lang = "'.$config['default_lang'].'"
	  LEFT JOIN '.ECARDS_ITEMS_TABLE.' c ON c.id_subcategory = a.id
		  WHERE a.id_category = ? AND c.status = "1"
	   GROUP BY a.id
	   ORDER BY a.sorter';
	
	$rs = $dbconn->Execute($strSQL, array($id_category));
	
	if ($rs->fields[0] > 0)
	{
		$i = 0; $_arr = array();
		
		while (!$rs->EOF)
		{
			$row = $rs->GetRowAssoc(false);
			
			$_arr[$i]['id'] = $row['id'];
			$_arr[$i]['name'] = $row['content_name'] ? stripslashes($row['content_name']) : stripslashes($row['subcategory_name']);
			$_arr[$i]['descr'] = $row['content_body'] ? stripslashes($row['content_body']) : stripslashes($row['subcategory_descr']);
			
			if ($row['subcategory_image'] != '' && file_exists($config['site_path'].'/uploades/ecards/'.$row['subcategory_image']))
			{
				$_arr[$i]['image'] = $config['server'].$config['site_root'].'/uploades/ecards/'.stripslashes($row['subcategory_image']);
			}
			else
			{
				$res = GetRandomImage('subcategory', $_arr[$i]['id']);
				$_arr[$i]['image'] = $config['server'].$config['site_root'].'/uploades/ecards/'.$res;
			}
			$_arr[$i]['cards_count'] = $row['cards_count'];
			$rs->MoveNext();
			$i++;
		}
		
		$smarty->assign('subcategories', $_arr);
		
		$form['subcategories_count'] = sizeof($_arr);
		$num_cat = $form['subcategories_count'] % 2;
		
		if ($num_cat == 0)
		{
			$form['categories_1_limit'] = $form['categories_2_limit'] = intval($form['subcategories_count']/2);
		}
		elseif ($num_cat == 1)
		{
			$form['categories_1_limit'] = intval($form['subcategories_count'] / 2) + 1;
			$form['categories_2_limit'] = intval($form['subcategories_count'] / 2);
		}
		$form['categories_2_start'] = $form['categories_1_limit'];
	}
	
	$smarty->assign('data', $data);
	$smarty->assign('form', $form);
	$smarty->display(TrimSlash($config['index_theme_path']).'/ecards_category_table.tpl');
	exit;
}


function SubCategoryTable()
{
	global $smarty, $config, $dbconn, $user;
	
	$id_category = isset($_GET['id_category']) && (int) $_GET['id_category'] ? (int) $_GET['id_category'] : null;
	$id_subcategory = isset($_GET['id_subcategory']) && (int) $_GET['id_subcategory'] ? (int) $_GET['id_subcategory'] : null;
	$id_user_to = isset($_GET['id_user_to']) ? (int) $_GET['id_user_to'] : 0;
	$id_order = isset($_GET['id_order']) ? (int) $_GET['id_order'] : 0;
	
	// get id_user_to and user_to_fname
	//
	$data = array();
	
	if ($id_order) {
		$data = GetCardOrderData($id_order);
		if (empty($data)) {
			header('Location: '.$config['server'].$config['site_root'].'/ecards.php');
			exit;
		}
	}
	
	if (empty($data['id_user_to']) && $id_user_to) {
		$user_to_fname = $dbconn->getOne('SELECT fname FROM '.USERS_TABLE.' WHERE id = ?', array($id_user_to));
		if (!empty($user_to_fname)) {
			$data['user_to_fname'] = $user_to_fname;
			$data['id_user_to'] = $id_user_to;
		}
	}
	
	$form['cur'] = GetSiteSettings('site_unit_costunit');
	
	$strSQL =
		'SELECT a.id, a.category_name, b.content_name
		   FROM '.ECARDS_CATEGORIES_TABLE.' a
	  LEFT JOIN '.ECARDS_LANG_CONTENT_TABLE.' b ON b.content_id = a.id AND b.content_type = "1" AND b.id_lang = "'.$config['default_lang'].'"
		  WHERE a.id = ?';
	
	$rs = $dbconn->Execute($strSQL, array($id_category));
	
	if ($rs->fields[0] > 0) {
		$row = $rs->GetRowAssoc(false);
		$form['id_category'] = $row['id'];
		$form['category_name'] = $row['content_name'] ? stripslashes($row['content_name']) : stripslashes($row['category_name']);
	} else {
		header('Location: '.$config['server'].$config['site_root'].'/ecards.php');
		exit;
	}
	
	unset($rs, $row);
	
	$strSQL =
		'SELECT a.id, a.subcategory_name, b.content_name, a.subcategory_descr, b.content_body
		   FROM '.ECARDS_SUBCATEGORIES_TABLE.' a
	  LEFT JOIN '.ECARDS_LANG_CONTENT_TABLE.' b ON b.content_id = a.id AND b.content_type = "2" AND b.id_lang = "'.$config['default_lang'].'"
		  WHERE a.id = ? AND a.id_category = ?';
	
	$rs = $dbconn->Execute($strSQL, array($id_subcategory, $id_category));
	
	if ($rs->fields[0] > 0) {
		$row = $rs->GetRowAssoc(false);
		$form['id_subcategory'] = $row['id'];
		$form['subcategory_name'] = $row['content_name'] ? stripslashes($row['content_name']) : stripslashes($row['subcategory_name']);
		$form['subcategory_descr'] = $row['content_body'] ? stripslashes($row['content_body']) : stripslashes($row['subcategory_descr']);
	} else {
		header('Location: '.$config['server'].$config['site_root'].'/ecards.php');
		exit;
	}
	
	$rs->Free();
	
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$cards_per_page = 9;
	$page = isset($_REQUEST['page']) && (int) $_REQUEST['page'] ? (int) $_REQUEST['page'] : 1;
	
	$rs = $dbconn->Execute(
		'SELECT COUNT(*)
		   FROM '.ECARDS_ITEMS_TABLE.'
		  WHERE id_category = ? AND id_subcategory = ? AND status = "1"',
		  array($id_category, $id_subcategory));
	
	$num_cards = $rs->fields[0];
	
	unset($rs);
	
	$lim_min = ($page - 1) * $cards_per_page;
	$lim_max = $cards_per_page;
	$limit_str = ' LIMIT '.$lim_min.', '.$lim_max;
	
	$strSQL =
		'SELECT DISTINCT a.id, a.card_name, a.card_image, b.content_name, a.card_price
		   FROM '.ECARDS_ITEMS_TABLE.' a
	  LEFT JOIN '.ECARDS_LANG_CONTENT_TABLE.' b ON b.content_id = a.id AND b.content_type = "3" AND b.id_lang = "'.$config['default_lang'].'"
		  WHERE a.status = "1" AND a.id_category = ? AND a.id_subcategory = ?
	   GROUP BY a.id
	   ORDER BY a.sorter '.$limit_str;
	
	$rs = $dbconn->Execute($strSQL, array($id_category, $id_subcategory));
	
	if ($rs->fields[0] > 0)
	{
		$i = 0;
		$_arr = array();
		
		while (!$rs->EOF)
		{
			$row = $rs->GetRowAssoc(false);
			
			$_arr[$i]['id'] = $row['id'];
			$_arr[$i]['name'] = $row['content_name'] ? stripslashes($row['content_name']) : stripslashes($row['card_name']);
			$_arr[$i]['name_unslashed'] = addslashes($_arr[$i]['name']);
			
			if ($row['card_image'] && file_exists($config['site_path'].'/uploades/ecards/thumb_'.stripslashes($row['card_image']))) {
				$_arr[$i]['card_image'] = $config['server'].$config['site_root'].'/uploades/ecards/thumb_'.stripslashes($row['card_image']);
			}
			
			if ($row['card_image'] && file_exists($config['site_path'].'/uploades/ecards/'.stripslashes($row['card_image']))) {
				$_arr[$i]['card_image_big'] = $config['server'].$config['site_root'].'/uploades/ecards/'.stripslashes($row['card_image']);
			}
			
			$_arr[$i]['card_price'] = sprintf('%01.2f', $row['card_price']);
			$_arr[$i]['card_price_raw'] = $row['card_price'];
			
			$rs->MoveNext();
			$i++;
		}
		
		$param = 'ecards.php?sel=cards&id_category='.$id_category.'&id_subcategory='.$id_subcategory.'&';
		
		$smarty->assign('links', GetLinkArray($num_cards, $page, $param, $cards_per_page));
		$smarty->assign('cards', $_arr);
	}
	
	$smarty->assign('data', $data);
	$smarty->assign('form', $form);
	$smarty->display(TrimSlash($config['index_theme_path']).'/ecards_cards_table.tpl');
	
	exit;
}


function CardTable($err = '', $input = array())
{
	global $smarty, $config, $dbconn, $lang, $user;
	
	$id_card = isset($_REQUEST['id_card']) && (int) $_REQUEST['id_card'] ? (int) $_REQUEST['id_card'] : null;
	$id_order = isset($_REQUEST['id_order']) && (int) $_REQUEST['id_order'] ? (int) $_REQUEST['id_order'] : null;
	$id_user_to = isset($_REQUEST['id_user_to']) && (int) $_REQUEST['id_user_to'] ? (int) $_REQUEST['id_user_to'] : null;
	
	if (empty($id_card) && empty($id_order)) {
		header('Location: '.$config['server'].$config['site_root'].'/ecards.php');
		exit;
	}
	
	$id_user = $user[ AUTH_ID_USER ];
	
	// check parameter $id_user_to, get $user_to_fname and update orders if $id_user_to is valid
	//
	if (!empty($id_user_to))
	{
		$user_to_fname = $dbconn->getOne('SELECT fname FROM '.USERS_TABLE.' WHERE id = ?', array($id_user_to));
		
		if (empty($user_to_fname))
		{
			$err = $lang['error']['there_is_no_user_with_such_fname'].'<br>';
		}
		elseif (! CheckPrivacy($id_user, $id_user_to))
		{
			$err = $lang['error']['privacy_violation'].'<br>';
		}
# delete
#		elseif (!empty($id_order))
#		{
#			$dbconn->execute('UPDATE '.ECARDS_ORDERS_TABLE.' SET id_user_to = "'.$id_user_to.'" WHERE id = "'.$id_order.'"');
#		}
	}
	
	// get order data incl. card data
	//
	$order = array();
	
	if (!empty($id_order)) {
		$order = GetCardOrderData($id_order);
		if (empty($order) || ! is_array($order)) {
			header('Location: '.$config['server'].$config['site_root'].'/ecards.php');
			exit;
		}
		if (empty($id_user_to)) {
			$id_user_to = $order['id_user_to'];
		}
	}
	
	$smarty->assign('connected_status', getConnectedStatus($id_user, $id_user_to));
	
	// get card data if id_card is explicitely passed to the script.
	// this will overwrite the old header and message with new defaults
	//
	$card = array();
	
	if (!empty($id_card)) {
		$card = GetCardData($id_card, $id_user_to);
		if (empty($card) || ! is_array($card)) {
			header('Location: '.$config['server'].$config['site_root'].'/ecards.php');
			exit;
		}
		if (empty($id_user_to) || empty($order['id_user_to']) || $id_user_to != $order['id_user_to']) {
			$card['card_header'] = $card['card_name'];
			$card['message'] = $card['default_message'];
		}
	}
	
	// merge order data with card data; card overwrites order
	//
	$data = array_merge($order, $card);
	
	if (empty($data)) {
		header('Location: '.$config['server'].$config['site_root'].'/ecards.php');
		exit;
	}
	
	// provide user_to data if order has not been stored yet
	//
#	if (empty($data['id_user_to']) && !empty($id_user_to) && !empty($user_to_fname)) {
	// overwrite user in order when new user has been selected
	if (!empty($id_user_to) && !empty($user_to_fname) && (empty($data['id_user_to']) || $data['id_user_to'] != $id_user_to)) {
		$data['id_user_to'] = $id_user_to;
		$data['user_to_fname'] = $user_to_fname;
		
		$rs = $dbconn->Execute(
			'SELECT login, fname, gender, date_birthday, email, site_language, icon_path FROM '.USERS_TABLE.' WHERE id = ?',
			array($id_user_to));
	
		$row = $rs->GetRowAssoc(false);
		
		$data['user_to_age'] = AgeFromBDate($row['date_birthday']);
		
		$settings = GetSiteSettings(array('icon_male_default', 'icon_female_default', 'icons_folder'));
		
		$default_photos['1'] = $settings['icon_male_default'];
		$default_photos['2'] = $settings['icon_female_default'];
		
		$icon_path = $row['icon_path'] ? $row['icon_path'] : $default_photos[$row['gender']];
		
		if ($icon_path && file_exists($config['site_path'].$settings['icons_folder'].'/'.$icon_path))
		{
			$data['user_to_icon_path'] = $config['site_root'].$settings['icons_folder'].'/'.$icon_path;
		}
	}
	
	// handle data validation error
	//
	if ($err)
	{
		$form['err'] = $err;
		
		if (!empty($input))
		{
			$data['card_header'] = $input['card_header'];
			$data['message'] = $input['message'];
			$data['id_song'] = $input['id_song'];
			
			if ($data['id_song']) {
				$data['song_name'] = $dbconn->getOne('SELECT song_name FROM '.ECARDS_SONGS_TABLE.' WHERE id = ?', array($data['id_song']));
			}
		}
	}
	
	$form['cur'] = GetSiteSettings('site_unit_costunit');
	
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$smarty->assign('data', $data);
	$smarty->assign('form', $form);
	
	$smarty->display(TrimSlash($config['index_theme_path']).'/ecards_card_edit_form.tpl');
	
	exit;
}


function SaveCard()
{
	global $config, $dbconn, $lang, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$id_card = isset($_POST['id_card']) ? (int) $_POST['id_card'] : 0;
	
	if (! $id_card) {
		header('Location: '.$config['server'].$config['site_root'].'/ecards.php');
		exit;
	}
	
	$id_user_to = isset($_POST['id_user_to']) ? (int) $_POST['id_user_to'] : 0;
	$id_order = isset($_POST['id_order']) ? (int) $_POST['id_order'] : 0;
	$id_song = isset($_POST['id_song']) ? (int) $_POST['id_song'] : 0;
	$fixuser = (isset($_POST['fixuser']) && $_POST['fixuser'] == 'Y') ? 'Y' : '';
	
	if (empty($_SESSION['permissions']['email_compose']) || empty($id_user_to) || getConnectedStatus($id_user, $id_user_to) != CS_CONNECTED)
	{
		$card = GetCardData($id_card, $id_user_to);
		$card_header = $card['card_name'];
		$message = $card['default_message'];
	}
	else
	{
		$card_header = (isset($_POST['card_header']) && trim($_POST['card_header']) != '') ? strip_tags($_POST['card_header']) : '';
		$message = (isset($_POST['message']) && $_POST['message'] != '') ? $_POST['message'] : '';
	}
	
	// validate user input
	//
	$err = '';
	
	if ($id_user_to <= 0) {
		$err .= 'Please select a recipient.<br />';
	} elseif (! CheckPrivacy($id_user, $id_user_to)) {
		$err .= $lang['error']['privacy_violation'];
	}
	
	if ($card_header == '') {
		$err .= 'Please enter a Header.<br />';
	}
	
	if ($message == '') {
		$err .= 'Please enter a Message.<br />';
	}
	
	// re-display page on input error
	//
	if ($err) {
		if (get_magic_quotes_gpc()) {
			$card_header = stripslashes($card_header);
			$message = stripslashes($message);
		}
		CardTable($err, array('id_user_to' => $id_user_to, 'card_header' => $card_header, 'message' => $message, 'id_song' => $id_song));
		exit;
	}
	
	$message = ecards_html_cleanup($message);
	
	if (!empty($id_order))
	{
		$strSQL = 'UPDATE '.ECARDS_ORDERS_TABLE.' SET id_item = ?, id_song = ?, id_user_to = ?, card_header = ?, message = ? WHERE id = ?';
		$dbconn->Execute($strSQL, array($id_card, $id_song, $id_user_to, $card_header, $message, $id_order));
	}
	else
	{
		$strSQL =
			'INSERT INTO '.ECARDS_ORDERS_TABLE.' SET
					id_user = ?, id_item = ?, id_song = ?, id_user_to = ?, card_header = ?, message = ?, status = "temped"';
		
		$dbconn->Execute($strSQL, array($id_user, $id_card, $id_song, $id_user_to, $card_header, $message));
		
		$id_order = $dbconn->Insert_ID();
	}
	
	$qs = 'sel=preview&id_order='.$id_order;
	
	if ($fixuser) $qs .= '&fixuser=Y';
	
	header('Location: '.$config['server'].$config['site_root'].'/ecards.php?'.$qs);
	exit;
}


function SelectUserForm()
{
	global $smarty, $config;
	
	IndexHomePage();
	
	$id_order = isset($_GET['id_order']) ? (int) $_GET['id_order'] : 0;
	$id_card = isset($_GET['id_card']) ? (int) $_GET['id_card'] : 0;
	
	$smarty->assign('id_order', $id_order);
	$smarty->assign('id_card', $id_card);
	
	$smarty->display(TrimSlash($config['index_theme_path']).'/ecards_user_select_form.tpl');
	
	exit;
}


function CheckUser()
{
	global $config, $dbconn, $lang, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$fname = isset($_GET['fname']) ? trim(strip_tags($_GET['fname'])) : '';
	$id_card = $_GET['id_card'];
	$id_order = isset($_GET['id_order']) ? $_GET['id_order'] : 0;
	
	if ($fname == '') {
		echo $lang['error']['there_is_no_user_with_such_fname'];
		exit;
	}
	
	$strSQL =
		'SELECT id, login, fname, mm_nickname, gender, date_birthday, icon_path, id_country, id_region, id_city
		   FROM '.USERS_TABLE.'
		  WHERE fname = ? AND id <> ? AND status = "1"
			AND root_user <> "1" AND guest_user <> "1"';
	
	$rs = $dbconn->Execute($strSQL, array($fname, $id_user));
		
	if (empty($rs->fields[0])) {
		echo $lang['error']['there_is_no_user_with_such_fname'];
		exit;
	}
	
	$row = $rs->GetRowAssoc(false);
	
	if (! CheckPrivacy($id_user, $row['id'])) {
		echo $lang['error']['privacy_violation'];
		exit;
	}
	
	$check_user['id'] = $row['id'];
	$check_user['name'] = stripslashes($row['fname']);
	$check_user['gender'] = $row['gender'];
	$check_user['age'] = AgeFromBDate($row['date_birthday']);
	$check_user['nickname'] = stripslashes($row['mm_nickname']);
	
	$check_user['id_country'] = $row['id_country'];
	$check_user['id_region'] = $row['id_region'];
	$check_user['id_city'] = $row['id_city'];
	
	$_LANG_NEED_ID = array();
	$_LANG_NEED_ID['country'][] = $check_user['id_country'];
	$_LANG_NEED_ID['region'][] = $check_user['id_region'];
	$_LANG_NEED_ID['city'][] = $check_user['id_city'];
			
	$settings = GetSiteSettings(array('icon_male_default', 'icon_female_default', 'icons_folder'));
	
	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];
	
	$icon_path = $row['icon_path'] ? $row['icon_path'] : $default_photos[$check_user['gender']];
	
	if ($icon_path && file_exists($config['site_path'].$settings['icons_folder'].'/'.$icon_path)) {
		$check_user['icon_path'] = $config['site_root'].$settings['icons_folder'].'/'.$icon_path;
	}
	
	$location = GetBaseLang($_LANG_NEED_ID);
			
	// build message
	// $card = GetCardData($id_card, $check_user['id']);
	// $message = $card['default_message'];
	
	// build html to display the found user in flybox
	$user_html = '<div style="width: 98%; padding: 10px; cursor: pointer;" onmouseover="this.style.backgroundColor=\'#cccccc\';" onmouseout="this.style.backgroundColor=\'#ffffff\';" ';
	
	if (getConnectedStatus($id_user, $check_user['id']) == CS_CONNECTED) {
		if ($id_order) {
			$user_html .= 'onclick="parent.window.location.href=\'ecards.php?sel=card&amp;id_card='.$id_card.'&amp;id_order='.$id_order.'&amp;id_user_to='.$check_user['id'].'\';">';
		} else {
			$user_html .= 'onclick="parent.window.location.href=\'ecards.php?sel=card&amp;id_card='.$id_card.'&amp;id_user_to='.$check_user['id'].'\';">';
		}
	} else {
		if ($id_order) {
			$user_html .= 'onclick="parent.window.location.href=\'ecards.php?sel=card&amp;id_card='.$id_card.'&amp;id_order='.$id_order.'&amp;id_user_to='.$check_user['id'].'\';">';
		} else {
			$user_html .= 'onclick="parent.window.location.href=\'ecards.php?sel=card&amp;id_card='.$id_card.'&amp;id_user_to='.$check_user['id'].'\';">';
		}
#		$user_html .= 'onclick="ChooseUserAct('.$check_user['id'].',\''.$check_user['name'].'\','.$check_user['age'].',\''.$check_user['icon_path'].'\',\''.$message.'\');">';
	}
	
	$user_html.= '<table cellpadding="0" cellspacing="0"><tr>';
	$user_html.= '<td valign="top"><img src="'.$check_user['icon_path'].'" class="icon"></td>';
	$user_html.= '<td valign="top" style="padding-left: 15px;"><div><b>'.$check_user['name'].'</b>, '.$check_user['age'].' '.$lang['home_page']['ans'].'</div>';
	
	if (!empty($location['city'][$check_user['id_city']]) 
	|| !empty($location['region'][$check_user['id_region']])
	|| !empty($location['country'][$check_user['id_country']]))
	{
		$user_html .= '<div style="padding-top: 10px;">';
		if (!empty($location['city'][$check_user['id_city']])) {
			$user_html .= $location['city'][$check_user['id_city']].', ';
		}
		if (!empty($location['region'][$check_user['id_region']])) {
			$user_html .= $location['region'][$check_user['id_region']].', ';
		}
		if (!empty($location['country'][$check_user['id_country']])) {
			$user_html .= $location['country'][$check_user['id_country']];
		}
		$user_html .= '</div>';
	}
	
	$user_html .= '</td></tr></table></div>';
	
	echo $user_html;
	exit;
}


function SaveUser()
{
	global $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$id_user_to = isset($_GET['id_user_to']) ? (int) $_GET['id_user_to'] : 0;
	$id_order = isset($_GET['id_order']) ? (int) $_GET['id_order'] : 0;
	$id_card = isset($_GET['id_card']) ? (int) $_GET['id_card'] : 0;
	
	if ($id_user_to > 0 && $id_order > 0)
	{
		// update recipient
		$dbconn->execute('UPDATE '.ECARDS_ORDERS_TABLE.' SET id_user_to = ? WHERE id = ?', array($id_user_to, $id_order));
		
		// update content if user is not allowed to edit the content
		if (empty($_SESSION['permissions']['email_compose']) || getConnectedStatus($id_user, $id_user_to) != CS_CONNECTED) {
			$card = GetCardData($id_card, $id_user_to);
			$dbconn->execute(
				'UPDATE '.ECARDS_ORDERS_TABLE.' SET card_header = ?, message = ? WHERE id = ?',
				array($card['card_name'], $card['default_message'], $id_order));
		}
	}
	
	exit;
}


function MusicForm()
{
	global $smarty, $config, $dbconn;
	
	IndexHomePage();
	
	$rs = $dbconn->Execute('SELECT id, song_name, song_file, status FROM '.ECARDS_SONGS_TABLE.' WHERE status = "1" ORDER BY id');
	
	if ($rs->fields[0] > 0)
	{
		$i = 0;
		$_arr = array();
		
		while (!$rs->EOF)
		{
			$row = $rs->GetRowAssoc(false);
			
			$_arr[$i]['id'] = $row['id'];
			$_arr[$i]['name'] = stripslashes($row['song_name']);
			$_arr[$i]['name_unslashed'] = addslashes($_arr[$i]['name']);
			
			if ($row['song_file'] && file_exists($config['site_path'].'/uploades/ecards/'.stripslashes($row['song_file']))) {
				$_arr[$i]['file'] = $config['server'].$config['site_root'].'/uploades/ecards/'.stripslashes($row['song_file']);
				$_arr[$i]['file_path'] = $config['site_path'].'/uploades/ecards/'.stripslashes($row['song_file']);
			}
			
			$_arr[$i]['status'] = (int) $row['status'];
			$rs->MoveNext();
			$i++;
		}
		
		$smarty->assign('songs', $_arr);
		$smarty->assign('music', 1);
		$smarty->display(TrimSlash($config['index_theme_path']).'/ecards_song_table.tpl');
	}
	
	exit;
}


function PreviewCard()
{
	global $smarty, $config, $user;
	
	$id_order = (isset($_GET['id_order']) && (int) $_GET['id_order']) ? (int) $_GET['id_order'] : null;
	
	if ($id_order == null) {
		header('Location: '.$config['server'].$config['site_root'].'/ecards.php');
		exit;
	}
	
	$data = GetCardOrderData($id_order);
	
	if (empty($data)) {
		header('Location: '.$config['server'].$config['site_root'].'/ecards.php');
		exit;
	}
	
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$form['cur'] = GetSiteSettings('site_unit_costunit');
	
	// tiny mce does not reset the <p> margin
	$data['message'] = str_replace('<p>', '<p style="margin:10px 0px;">', $data['message']);
	
	$smarty->assign('data', $data);
	$smarty->assign('form', $form);
	
	$smarty->display(TrimSlash($config['index_theme_path']).'/ecards_preview_order_table.tpl');
	
	exit;
}


function OrderForm()
{
	global $smarty, $config, $dbconn, $lang, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$id_order = (isset($_GET['id_order']) && (int) $_GET['id_order']) ? (int) $_GET['id_order'] : 0;
	
	if ($id_order <= 0) {
		header('Location: '.$config['server'].$config['site_root'].'/ecards.php');
		exit;
	}
	
	$order = GetCardOrderData($id_order);
	
	if (empty($order)) {
		header('Location: '.$config['server'].$config['site_root'].'/ecards.php');
		exit;
	}
	
	$id_user_to = $order['id_user_to'];
	
	if (! CheckPrivacy($id_user, $id_user_to)) {
		$err = $lang['error']['there_is_no_user_with_such_login'].'<br>';
		CardTable($err);
		exit;
	}
	
	IndexHomePage();
	GetActiveUserInfo($user);
	
	// send card if price is 0
	//
	if ($order['price_raw'] == 0) {
		header('location: '.$config['server'].$config['site_root'].'/ecards.php?sel=pay_from_account&id_order='.$id_order);
		exit;
	}
	
	$form['cur'] = GetSiteSettings('site_unit_costunit');
	
	// tiny mce does not reset the <p> margin
	$order['message'] = str_replace('<p>', '<p style="margin:10px 0px;">', $order['message']);
	
	$smarty->assign('order', $order);
	
	$rs = $dbconn->Execute('SELECT name, template_name FROM '.BILLING_PAYSYSTEMS_TABLE.' WHERE used = "1"');
	
	$i = 0;
	$paysys = array();
	
	while (!$rs->EOF) {
		$paysys[$i]['name'] = $rs->fields[0];
		$paysys[$i]['template_name'] = $rs->fields[1];
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('paysys', $paysys);
	
	$rs = $dbconn->Execute('SELECT account_curr FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($id_user));
	
	$form['count'] = round($rs->fields[0], 2);
	
	$smarty->assign('form', $form);
	$smarty->display(TrimSlash($config['index_theme_path']).'/ecards_order_form.tpl');
	
	exit;
}


function PayFromAccount()
{
	global $config, $dbconn, $user;
	
	$id_order = (isset($_GET['id_order']) && (int) $_GET['id_order']) ? (int) $_GET['id_order'] : null;
	
	if ($id_order == null) {
		header('Location: '.$config['server'].$config['site_root'].'/ecards.php');
		exit;
	}
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$settings = GetSiteSettings(array('site_unit_costunit'));
	
	$order = GetCardOrderData($id_order);
	
	if ($order['price_raw'] > 0 || MM_ECARDS_FREE == 0)
	{
		$rs = $dbconn->Execute('SELECT account_curr FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($id_user));
		
		$account_curr = round($rs->fields[0], 2);
		
		if ($account_curr < $order['price_raw']) {
			header('Location: '.$config['server'].$config['site_root'].'/ecards.php');
			exit;
		}
		
		$new_account_curr = round(($account_curr - $order['price_raw']), 2);
		
		$dbconn->Execute('UPDATE '.BILLING_USER_ACCOUNT_TABLE.' SET account_curr = ? WHERE id_user = ?', array($new_account_curr, $id_user));
		
		$strSQL =
			'INSERT INTO '.BILLING_ENTRY_TABLE.' SET
					id_user = ?, amount = ?, currency = ?, cost = ?, cost_2 = 0,
					id_group = '.PG_ECARD.', id_product = ?, date_entry = NOW(),
					entry_type = "ecard", txn_type = "account", product_name = "ecard no. '.$order['id_card'].'"';
		
		$dbconn->Execute($strSQL, array($id_user, $order['price_raw'], $settings['site_unit_costunit'], $order['price_raw'], $id_order));
	}
	
	SendEcard($id_order, true);
	
	header('Location: '.$config['server'].$config['site_root'].'/ecards.php?sel=my_orders&sent=1');
	exit;
}


function PayFromPaysystem()
{
	global $config, $dbconn, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$id_order = (isset($_GET['id_order']) && (int) $_GET['id_order']) ? (int) $_GET['id_order'] : null;
	
	if ($id_order == null) {
		header('Location: '.$config['server'].$config['site_root'].'/ecards.php');
		exit;
	}
	
	$currency = GetSiteSettings('site_unit_costunit');
	
	$paysys = $_GET['paysys'];
	
	$order = GetCardOrderData($id_order);
	
	if ($order['price_raw'] == 0) {
		header('Location: '.$config['server'].$config['site_root'].'/ecards.php');
		exit;
	}
	
	$strSQL =
		'INSERT INTO '.BILLING_REQUESTS_TABLE.' SET
				id_user = ?, amount = ?, currency = ?, cost = ?, cost_2 = 0,
				id_group = '.PG_ECARD.', id_product = ?, date_send = NOW(),
				status = "send", paysystem = ?, recurring = "0",
				product_name = "ecard no. '.$order['id_card'].'"';
	
	$dbconn->Execute($strSQL, array($id_user, $order['price_raw'], $currency, $order['price_raw'], $order['id_order'], $paysys));
	
	$id_trunzaction = $dbconn->Insert_ID();
	
	include_once './include/systems/functions/'.$paysys.'.php';
	
	MakePayment($id_trunzaction, $id_user);
	
	header('Location: '.$config['server'].$config['site_root'].'/ecards.php?sel=my_orders');
	exit;
}


function OrdersList()
{
	global $smarty, $config, $dbconn, $lang, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$id_user_to = isset($_GET['id_user_to']) ? (int) $_GET['id_user_to'] : 0;
	$fixuser = isset($_GET['fixuser']) && $_GET['fixuser'] == 'Y' ? 'Y' : '';
	$id_order = isset($_GET['id_order']) ? (int) $_GET['id_order'] : 0;
	
	// get id_user_to and user_to_fname
	//
	$data = array();
	
	if ($id_order) {
		$data = GetCardOrderData($id_order);
		if (empty($data)) {
			header('Location: '.$config['server'].$config['site_root'].'/ecards.php');
			exit;
		}
	}
	
	if (empty($data['id_user_to']) && $id_user_to) {
		$user_to_fname = $dbconn->getOne('SELECT fname FROM '.USERS_TABLE.' WHERE id = ?', array($id_user_to));
		if (!empty($user_to_fname)) {
			$data['user_to_fname'] = $user_to_fname;
			$data['id_user_to'] = $id_user_to;
		}
	}
	
	$orders_per_page = 10;
	
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$form['cur'] = GetSiteSettings('site_unit_costunit');
	
	$page = (isset($_REQUEST['page']) && (int) $_REQUEST['page']) ? (int) $_REQUEST['page'] : 1;
	
	$rs = $dbconn->Execute('SELECT COUNT(*) FROM '.ECARDS_ORDERS_TABLE.' WHERE id_user = ?', array($id_user));
	
	$num_orders = $rs->fields[0];
	
	$lim_min = ($page - 1) * $orders_per_page;
	$lim_max = $orders_per_page;
	$limit_str = ' LIMIT '.$lim_min.', '.$lim_max;
	
	$strSQL =
		'SELECT DISTINCT a.id, a.id_user, a.id_item, a.id_song, a.id_user_to, a.card_header, a.status, b.card_image, b.card_name, c.content_name,
				e.login, e.fname, b.card_price
		   FROM '.ECARDS_ORDERS_TABLE.' a
	  LEFT JOIN '.ECARDS_ITEMS_TABLE.' b ON b.id = a.id_item
      LEFT JOIN '.ECARDS_LANG_CONTENT_TABLE.' c ON c.content_id = a.id_item AND c.content_type = "3" AND c.id_lang = "'.$config['default_lang'].'"
	  LEFT JOIN '.USERS_TABLE.' e ON e.id = a.id_user_to
		  WHERE a.id_user = "'.$id_user.'"';
	
	if ($id_user_to && $fixuser == 'Y') {
		$strSQL .= ' AND a.id_user_to = "'.$id_user_to.'"';
	}
	
	$strSQL .= ' GROUP BY a.id ORDER BY a.id '.$limit_str;
	
	$rs = $dbconn->Execute($strSQL);
	
	if ($rs->fields[0] > 0)
	{
		$i = 0;
		$_arr = array();
		
		while (!$rs->EOF)
		{
			$row = $rs->GetRowAssoc(false);
			
			$_arr[$i]['id_order'] = $row['id'];
			$_arr[$i]['id_card'] = $row['id_item'];
			$_arr[$i]['id_song'] = $row['id_song'];
			$_arr[$i]['id_user_to'] = $row['id_user_to'];
			$_arr[$i]['card_header'] = stripslashes($row['card_header']);
			$_arr[$i]['status'] = $row['status'];
			
			if ($row['card_price'] == 0 && $_arr[$i]['status'] == 'approved')
			{
				$_arr[$i]['status_lang'] = $lang['cards']['admin']['status_value']['sent'];
			}
			elseif ($row['card_price'] == 0 && $_arr[$i]['status'] == 'temped')
			{
				$_arr[$i]['status_lang'] = $lang['cards']['admin']['status_value']['unsent'];
			}
			else
			{
				$_arr[$i]['status_lang'] = $lang['cards']['admin']['status_value'][$_arr[$i]['status']];
			}
			
			if ($row['card_image'] && file_exists($config['site_path'].'/uploades/ecards/'.stripslashes($row['card_image'])))
			{
				$_arr[$i]['card_image_big'] = $config['server'].$config['site_root'].'/uploades/ecards/'.stripslashes($row['card_image']);
			}
			
			if ($row['card_image'] && file_exists($config['site_path'].'/uploades/ecards/thumb_'.stripslashes($row['card_image'])))
			{
				$_arr[$i]['card_image_thumb'] = $config['server'].$config['site_root'].'/uploades/ecards/thumb_'.stripslashes($row['card_image']);
			}
			
			$_arr[$i]['name'] = $row['content_name'] ? stripslashes($row['content_name']) : stripslashes($row['card_name']);
			$_arr[$i]['name_unslashed'] = addslashes($_arr[$i]['name']);
			$_arr[$i]['user_to_fname'] = stripslashes($row['fname']);
			$_arr[$i]['card_price'] = sprintf('%01.2f', $row['card_price']);
			$_arr[$i]['card_price_raw'] = $row['card_price'];
			
			$rs->MoveNext();
			$i++;
		}
		
		$param = 'ecards.php?sel=my_orders&';
		$smarty->assign('links', GetLinkArray($num_orders, $page, $param, $orders_per_page));
		$smarty->assign('orders', $_arr);
	}
	
	if (isset($_GET['sent'])) {
		$smarty->assign('sent', 1);
	}
	
	if ($id_user_to && $fixuser == 'Y') {
		$form['user_to_fname'] = $dbconn->getOne('SELECT fname FROM '.USERS_TABLE.' WHERE id = ?', array($id_user_to));
	}
	
	$smarty->assign('form', $form);
	$smarty->assign('data', $data);
	$smarty->display(TrimSlash($config['index_theme_path']).'/ecards_orders_table.tpl');
	
	exit;
}


function OrderDelete()
{
	global $dbconn, $config;
	
	$id_order = !empty($_GET['id_order']) ? (int) $_GET['id_order'] : 0;
	$id_user_to = !empty($_GET['id_user_to']) ? (int) $_GET['id_user_to'] : 0;
	$fixuser = isset($_GET['fixuser']) && $_GET['fixuser'] == 'Y' ? 'Y' : '';
	
	if ($id_order) {
		$dbconn->execute('DELETE FROM '.ECARDS_ORDERS_TABLE.' WHERE id = ?', array($id_order));
	}
	
	$qs = 'sel=my_orders&deleted=1';
	
	if ($id_user_to) $qs .= '&id_user_to='.$id_user_to;
	if ($fixuser) $qs .= '&fixuser=Y';
	
	header('Location: '.$config['server'].$config['site_root'].'/ecards.php?'.$qs);
	exit;
}


function ecards_html_cleanup($document)
{
	$search = array (
		'@<script[^>]*?>.*?</script>@si',	// Strip out javascript
		'@<style[^>]*?>.*?</style>@siU',	// Strip style tags properly
		'@<![\s\S]*?--[ \t\n\r]*>@'			// Strip multi-line comments including CDATA
	);
	$document = preg_replace($search, '', $document);
	return $document;
}


function GetAllSubCategoriesList($id_category='', $id_subcategory='')
{
	global $config, $dbconn;

	$strSQL =
		'SELECT DISTINCT a.id, a.subcategory_name, b.content_name
		   FROM '.ECARDS_SUBCATEGORIES_TABLE.' a
	  LEFT JOIN '.ECARDS_LANG_CONTENT_TABLE.' b ON b.content_id = a.id AND b.content_type = "2" AND b.id_lang = "'.$config['default_lang'].'"
		  WHERE a.id_category = ?
	   GROUP BY a.id
	   ORDER BY a.sorter ';
		  
	$rs = $dbconn->Execute($strSQL, array($id_category));
	
	$i = 0;
	$_arr = array();
	
	while (!$rs->EOF)
	{
		$_arr[$i]['id'] = $rs->fields[0];
		$_arr[$i]['name'] = $rs->fields[2] ? stripslashes($rs->fields[2]) : stripslashes($rs->fields[1]);
		if (isset($id_subcategory) && $id_subcategory == $rs->fields[0]) {
			$_arr[$i]['sel'] = 1;
		}
		$rs->MoveNext();
		$i++;
	}
	
	return $_arr;
}


function GetRandomImage($type, $id)
{
	global $config, $dbconn;
	
	switch ($type)
	{
		case 'category' :
			$res = 'default_category_picture.gif';
			$strSQL = 'SELECT id, card_image FROM '.ECARDS_ITEMS_TABLE.' WHERE id_category = ? ORDER BY RAND() LIMIT 1';
			$rs = $dbconn->Execute($strSQL, array($id));
		break;
		case 'subcategory' :
			$res = 'default_subcategory_picture.gif';
			$strSQL = 'SELECT id, card_image FROM '.ECARDS_ITEMS_TABLE.' WHERE id_subcategory = ? ORDER BY RAND() LIMIT 1';
			$rs = $dbconn->Execute($strSQL, array($id));
		break;
	}
	
	if ($rs->fields[0] > 0 && file_exists($config['site_path'].'/uploades/ecards/thumb_'.stripslashes($rs->fields[1]))) {
		$res = 'thumb_'.stripslashes($rs->fields[1]);
	}
	
	return $res;
}


function CheckPrivacy($id_user, $id_user_to)
{
	global $dbconn;
	
	$strSQL =
		'SELECT a.id
		   FROM '.USERS_TABLE.' a
		   LEFT JOIN '.USER_PRIVACY_SETTINGS.' AS up ON up.id_user = a.id
		  WHERE a.id = "'.$id_user_to.'"';
	
	// adding privacy condition
	$usr_group = $dbconn->GetOne('SELECT id_group FROM '.USER_GROUP_TABLE.' WHERE id_user = ?', array($id_user));
	
	switch ($usr_group)
	{
		case MM_TRIAL_GUY_ID:
			$where_str_pp = ' AND up.vis_guy_1 = "1"';
		break;
			
		case MM_TRIAL_LADY_ID:
			$where_str_pp = ' AND up.vis_lady_1 = "1"';
		break;
		
		case MM_REGULAR_LADY_ID:
			$where_str_pp = ' AND up.vis_lady_2 = "1"';
		break;
		
		case MM_PLATINUM_LADY_ID:
			$where_str_pp = ' AND up.vis_lady_3 = "1"';
		break;
		
		case MM_REGULAR_GUY_ID:
			$where_str_pp = ' AND up.vis_guy_2 = "1"';
		break;
		
		case MM_PLATINUM_GUY_ID:
			$where_str_pp = ' AND up.vis_guy_3 = "1"';
		break;
		
		case MM_ELITE_GUY_ID:
			$where_str_pp = ' AND up.vis_guy_4 = "1"';
		break;
		
		default:
			$where_str_pp = '';
		break;
	}
	
	$strSQL .= $where_str_pp;
	
	$rs = $dbconn->Execute($strSQL);
	
	if (empty($rs->fields[0])) {
		return false;
	}
	
	return true;
}

?>