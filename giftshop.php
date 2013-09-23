<?php
/**
* My Store
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
include './include/class.catalog.php';
include './include/class.basket.php';
include './include/class.phpmailer.php';
include './include/functions_mail.php';

if (isset($log)) $log->log('giftshop START');

define('GIFTSHOP_REDIRECT_BASKET_ADD', false);		// GET : reload adds item again (no alert, possible harm)
define('GIFTSHOP_REDIRECT_BASKET_REFRESH', false);	// POST: reload shows repeat action alert and saves basket again (no harm)
define('GIFTSHOP_REDIRECT_BASKET_CLEAR', false);	// POST: reload shows repeat action alert and tries to clear basket again (no harm)
define('GIFTSHOP_REDIRECT_USER_ADD', false);		// GET : reload saves recipient user again (no alert, no harm)
define('GIFTSHOP_REDIRECT_EDIT_ORDER', false);		// GET : reload copies order to basket again (no alert, no harm)
													//		 but I suggest to make a redirection here
define('GIFTSHOP_REDIRECT_CONFIRM_ORDER', true);	## POST: reload shows repeat action alert and jumps to giftshop index page (unintended effect)
													## 		 this happens because the cart has been emptied, so the order cannot be created again,
													## 		 and the order id is not stored in the session.
													## 		 we could program a work-around, but it is better here to redirect to the
													## 		 view order page and accept the performance loss
define('GIFTSHOP_REDIRECT_DELETE_ORDER', false);	// GET : reload tried to delete order again, and fails gracefully (no alert, no harm)

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

// catalog class
$catalog = new Catalog($config, $dbconn, $smarty, $lang);
$orders = new Orders($user, $config, $dbconn, $smarty, $catalog);

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

// dispatcher
switch ($sel) {
	case 'items': ViewItems(); break;
	case 'view': DetailedView(); break;
	case 'gallery': GalleryView(); break;
	case 'view_basket': BasketView(); break;
	case 'basket_add': BasketAdd(); break;
	case 'basket_refresh': BasketRefresh(); break;
	case 'basket_clear': BasketClear(); break;
	case 'users_form': UserSearchForm(); break;
	case 'users_autocomplete': UserAutocomplete(); break;
	case 'users_add': UserAdd(); break;
	case 'confirm_order': ConfirmOrder(); break;
	case 'payment_order': PaymentOrder(); break;
	case 'pay_from_account': PayFromAccount(); break;
	case 'pay_with_service': PayWithService(); break;
	case 'atm_payment': ATM_Payment(); break;
	case 'wire_transfer': Wire_Transfer(); break;
	case 'bank_cheque': Bank_Cheque(); break;
	case 'order_history': OrderHistory(); break;
	case 'view_order': ViewOrder(); break;
	case 'edit_order': EditOrder(); break;
	case 'delete_order': DeleteOrder(); break;
	case 'wishlist': Wishlist(); break;
	default: ViewCategory(); break;
}

exit;

function ViewCategory()
{
	global $smarty, $catalog, $orders;
	
	$smarty->assign('settings', GetSiteSettings(array('thumb_max_width', 'thumb_max_height')));
	$smarty->assign('categories', $catalog->category_list(1, $catalog->category_count(), 1));
	
	/*
	if (isset($_GET['new'])) {
		unset($_SESSION['basket_user']);
		unset($_SESSION['basket_comment']);
		unset($_SESSION['basket_order']);
	}
	*/
	
	// recipient data
	if (!empty($_SESSION['basket_user'])) {
		$smarty->assign('recipient_data', GetUserInfo($_SESSION['basket_user']));
	}
	
	// comment
	if (!empty($_SESSION['basket_comment'])) {
		$smarty->assign('giftshop_comment', $_SESSION['basket_comment']);
	}
	
	// last viewed data
	$last_viewed = ViewedItems();
	if (!empty($last_viewed) && is_array($last_viewed)) {
		$smarty->assign('last_viewed', $last_viewed);
	}
	
	// bestsellers data
	$bestsellers = $orders->BestSellers();
	if (!empty($bestsellers) && is_array($bestsellers)) {
		$smarty->assign('bestsellers', $bestsellers);
	}
	
	// promoted items data
	$promoted = $orders->PromotedItems();
	if (!empty($promoted) && is_array($promoted)) {
		$smarty->assign('promoted', $promoted);
	}
	
	// form data
	$form = array();
	$form['subpage'] = 'view_categories';
	
	GiftShopPage($form);
	exit;
}

function ViewItems()
{
	global $config_index, $smarty, $catalog, $orders;
	
	// overwrite for 3 items per column
	$config_index['search_numpage'] = 6;
	
	$page = !empty($_GET['page']) ? (int) $_GET['page'] : (!empty($_POST['page']) ? (int) $_POST['page'] : 1);
	
	if (empty($page)) {
		$page = 1;
	}
	
	$id_category = !empty($_GET['category']) ? (int) $_GET['category']: null;
	
	if (empty($id_category)) {
		ViewCategory();
		exit;
	}
	
	$smarty->assign('settings', GetSiteSettings(array('thumb_max_width', 'thumb_max_height')));
	$smarty->assign('category_name', $catalog->GetCategoryName($id_category));
	$smarty->assign('items_list', $catalog->items_list($id_category, $page, $config_index['search_numpage'], 1));
	
	// categories for sidebar
	$smarty->assign('categories', $catalog->category_all_list(1));
	
	// recipient data
	if (!empty($_SESSION['basket_user'])) {
		$smarty->assign('recipient_data', GetUserInfo($_SESSION['basket_user']));
	}
	
	// comment
	if (!empty($_SESSION['basket_comment'])) {
		$smarty->assign('giftshop_comment', $_SESSION['basket_comment']);
	}
	
	// last viewed data
	$last_viewed = ViewedItems();
	if (!empty($last_viewed) && is_array($last_viewed)) {
		$smarty->assign('last_viewed', $last_viewed);
	}
	
	// bestsellers data
	$bestsellers = $orders->BestSellers($id_category);
	if (!empty($bestsellers) && is_array($bestsellers)) {
		$smarty->assign('bestsellers', $bestsellers);
	}
	
	// form data
	$form = array();
	$form['subpage'] = 'view_items';
	
	$param = 'giftshop.php?sel=items&amp;category='.$id_category.'&amp;';
	$form['links'] = GetLinkArray($catalog->items_count($id_category, 1), $page, $param, $config_index['search_numpage']);
	
	GiftShopPage($form);
	exit;
}

function DetailedView()
{
	global $smarty, $catalog, $orders;
	
	// get item
	$id_item = !empty($_GET['item']) ? (int) $_GET['item'] : (!empty($_POST['item']) ? (int) $_POST['item'] : null);
	
	if (!$id_item) {
		ViewItems();
		return;
	}
	
	// get page
	$page = !empty($_GET['page']) ? (int) $_GET['page'] : (!empty($_POST['page']) ? (int) $_POST['page'] : 1);
	
	if (!$page) {
		$page = 1;
	}
	
	// add item to viewed items
	if (empty($_SESSION['viewed_items']) || is_array($_SESSION['viewed_items']) && !in_array($id_item, $_SESSION['viewed_items'])) {
		$_SESSION['viewed_items'][] = $id_item;
	}
	
	$smarty->assign('settings', GetSiteSettings(array('thumb_max_width', 'thumb_max_height')));
	
	// categories for sidebar
	$smarty->assign('categories', $catalog->category_all_list(1));
	
	// item data
	$item_data = $catalog->items_item($id_item);
	$smarty->assign('item_data', $item_data);
	
	// category
	$id_category = $item_data['id_category'];
	
	// recipient data
	if (!empty($_SESSION['basket_user'])) {
		$smarty->assign('recipient_data', GetUserInfo($_SESSION['basket_user']));
	}
	
	// comment
	if (!empty($_SESSION['basket_comment'])) {
		$smarty->assign('giftshop_comment', $_SESSION['basket_comment']);
	}
	
	// other items in same category
	$smarty->assign('items_list', $catalog->items_list($id_category, 1, $catalog->items_count($id_category, 1), 1));
	
	// last viewed items
	$last_viewed = ViewedItems();
	if (!empty($last_viewed) && is_array($last_viewed)) {
		$smarty->assign('last_viewed', $last_viewed);
	}
	
	// bestsellers
	$bestsellers = $orders->BestSellers($id_category);
	if (!empty($bestsellers) && is_array($bestsellers)) {
		$smarty->assign('bestsellers', $bestsellers);
	}
	
	// same items
	$same_items = $orders->SameOrdersItems($id_item);
	if (!empty($same_items) && is_array($same_items)) {
		$smarty->assign('same_items', $same_items);
	}
	
	// form data
	$form = array();
	$form['subpage'] = 'view_item';
	
	GiftShopPage($form);
	exit;
}

function ViewedItems($limit = 6)
{
	global $catalog;
	
	if (empty($_SESSION['viewed_items']) || !is_array($_SESSION['viewed_items'])) {
		return array();
	}
	
	$arr = array_unique(array_reverse($_SESSION['viewed_items']));
	$ret_arr = array();
	$i = 0;
	
	foreach ($arr as $view_id) {
		$ret_arr[] = $catalog->items_item($view_id);
		$i++;
		if ($i >= $limit) {
			break;
		}
	}
	
	return $ret_arr;
}

function GalleryView()
{
	global $lang, $config, $smarty, $catalog;
	
	$id = $_GET['id'];
	$id_image = $_GET['id_image'];
	
	$item = $catalog->items_item($id);
	$form['login'] = $item['name'];
	$form['image_path'] = '<img src="'.$catalog->items_gallery_image($id_image).'">';
	
	$smarty->assign('form', $form);
	$smarty->assign('header', $lang['giftshop']);
	
	$smarty->display(TrimSlash($config['index_theme_path']).'/gallary_view_table.tpl');
	exit;
}

//====================
// BASKET
//====================

function BasketAdd()
{
	global $orders;
	
	if (!empty($_GET['item'])) {
		$item = $_GET['item'];
		$orders->AddToBasket($item);
	}
	
	if (GIFTSHOP_REDIRECT_BASKET_ADD) {
		header('location: giftshop.php?sel=view_basket');
	} else {
		BasketView();
	}
	
	exit;
}

function BasketRefresh()
{
	global $orders;
	
	// $_POST['quantity'] contains an array
	
	if (!empty($_POST['quantity']) && is_array($_POST['quantity'])) {
		$orders->SaveBasket($_POST['quantity']);
	}
	
	if (!empty($_POST['comment'])) {
		$_SESSION['basket_comment'] = strip_tags($_POST['comment']);
	} else {
		unset($_SESSION['basket_comment']);
	}
	
	if (GIFTSHOP_REDIRECT_BASKET_REFRESH) {
		header('location: giftshop.php?sel=view_basket');
	} else {
		BasketView();
	}
	exit;
}

function BasketView($err = '')
{
	global $smarty, $orders;
	
	// basket data
	$basket_data['positions'] = $orders->GetBasket();
	
	$temp = $orders->GetBasketSum();
	$basket_data['total_amount_format'] = $temp['total_amount_format'];
	
	$smarty->assign('basket_data', $basket_data);
	
	// recipient data
	if (!empty($_SESSION['basket_user'])) {
		$smarty->assign('recipient_data', GetUserInfo($_SESSION['basket_user']));
	}
	
	// comment
	if (!empty($_SESSION['basket_comment'])) {
		$smarty->assign('giftshop_comment', $_SESSION['basket_comment']);
	}
	
	// form data
	$form = array();
	$form['subpage'] = 'view_basket';
	$form['err'] = $err;
	
	GiftShopPage($form);
	exit;
}

function BasketClear()
{
	global $orders;
	
	$orders->ClearBasket(true);
	
	if (GIFTSHOP_REDIRECT_BASKET_CLEAR) {
		header('location: giftshop.php');
	} else {
		ViewCategory();
	}
	
	exit;
}

function UserAdd()
{
	global $orders;
	
	if (empty($_GET['id_user']) || (int) $_GET['id_user'] <= 0) {
		header('location: index.php');
		exit;
	}
	
	$_SESSION['basket_user'] = (int) $_GET['id_user'];
	
	$cnt = $orders->GetBasketCount();
	
	if ($cnt) {
		if (GIFTSHOP_REDIRECT_USER_ADD) {
			header('location: giftshop.php?sel=view_basket');
		} else {
			BasketView();
		}
		exit;
	}
	
	if (GIFTSHOP_REDIRECT_USER_ADD) {
		header('location: giftshop.php');
	} else {
		ViewCategory();
	}
	exit;
}

function UserSearchForm()
{
	global $lang, $config, $dbconn, $smarty, $user;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$settings = GetSiteSettings(array('icon_male_default', 'icon_female_default', 'icons_folder'));
	
	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];
	
	$search_str = isset($_GET['search_str']) ? strip_tags($_GET['search_str']) : null;
	$form['search_str'] = $search_str;
	
	if (!empty($search_str)) {
		$strSQL =
			'SELECT id, login, fname, gender, date_birthday, icon_path, id_country, id_city, id_region
			   FROM '.USERS_TABLE.'
			  WHERE fname LIKE "'.$search_str.'%" AND status = "1" AND visible = "1" AND root_user <> "1" AND guest_user <> "1"';
		$rs = $dbconn->Execute($strSQL);
		
		$i = 0;
		$users = array();
		$_LANG_NEED_ID = array();
		
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$users[$i] = $row;
			$users[$i]['id'] = $row['id'];
			$users[$i]['login'] = $row['login'];
			$users[$i]['fname'] = stripslashes($row['fname']);
			$users[$i]['number'] = $i+1;
			$users[$i]['age'] = AgeFromBDate($row['date_birthday']);
			$users[$i]['id_country'] = intval($row['id_country']);
			$users[$i]['id_region'] = intval($row['id_region']);
			$users[$i]['id_city'] = intval($row['id_city']);
			
			$_LANG_NEED_ID['country'][] = intval($row['id_country']);
			$_LANG_NEED_ID['region'][] = intval($row['id_region']);
			$_LANG_NEED_ID['city'][] = intval($row['id_city']);
			
			$icon_path = $row['icon_path']?$row['icon_path']:$default_photos[$row['gender']];
			
			if ($icon_path && file_exists($config['site_path'].$settings['icons_folder'].'/'.$icon_path)) {
				$users[$i]['icon_path'] = $config['site_root'].$settings['icons_folder'].'/'.$icon_path;
			}
			
			$rs->MoveNext();
			$i++;
		}
		
		# $smarty->assign('base_lang', GetBaseLang($_LANG_NEED_ID));
		if (!empty($users)) {
			$smarty->assign('users', $users);
		}
	}
	
	$smarty->assign('section', $lang['subsection']);
	$smarty->assign('form', $form);
	$smarty->assign('header', $lang['giftshop']);
	
	$smarty->display(TrimSlash($config['index_theme_path']).'/giftshop_basket_user_table.tpl');
	exit;
}

function UserAutocomplete()
{
	global $dbconn;
	
	if (empty($_GET['term'])) {
		return;
	}
	
	$search_str = $_GET['term'];
	
	$strSQL =
		'SELECT id, login
		   FROM '.USERS_TABLE.'
		  WHERE login LIKE "'.$search_str.'%" AND status = "1" AND visible = "1" AND root_user <> "1" AND guest_user <> "1"';
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$users = array();
	
	while (!$rs->EOF) {
		$row = $rs->GetRowAssoc(false);
		$users[$i]['id'] = $row['id'];
		$users[$i]['label'] = $row['login'];
		$users[$i]['value'] = $row['login'];
		if ($i > 15) {
			break;
		}
		$rs->MoveNext();
		$i++;
	}
	
	echo json_encode($users);
	return;
}

function GetUserInfo($id_user)
{
	global $config, $dbconn;
	
	$settings = GetSiteSettings(array('icon_male_default', 'icon_female_default', 'icons_folder'));
	
	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];
	
	$strSQL =
		'SELECT a.id, a.login, a.fname, a.gender, a.date_birthday, a.icon_path, a.big_icon_path, b.name AS country, c.name AS city, d.name AS region
		   FROM '.USERS_TABLE.' a
	  LEFT JOIN '.COUNTRY_SPR_TABLE.' b ON b.id = a.id_country
	  LEFT JOIN '.CITY_SPR_TABLE.' c ON c.id = a.id_city
	  LEFT JOIN '.REGION_SPR_TABLE.' d ON d.id = a.id_region
		  WHERE a.id = ?';
	$rs = $dbconn->Execute($strSQL, array($id_user));
	$row = $rs->GetRowAssoc(false);
	
	$row['fname'] = stripslashes($row['fname']);
	$row['country'] = stripslashes($row['country']);
	$row['region'] = stripslashes($row['region']);
	$row['city'] = stripslashes($row['city']);
	$row['age'] = AgeFromBDate($row['date_birthday']);
	
	$icon_path = $row['big_icon_path'] ? $row['big_icon_path'] : $default_photos[ $row['gender'] ];        
	
	if ($icon_path && file_exists($config['site_path'].$settings['icons_folder'].'/'.$icon_path)) {
		$row['big_icon_path'] = $config['site_root'].$settings['icons_folder'].'/'.$icon_path;
	}
	
	return $row;
}

//====================
// ORDERS
//====================

function ConfirmOrder()
{
	global $orders;
	
	// recalc the basket with the current quantities, $_POST['quantity'] contains an array
	if (!empty($_POST['quantity']) && is_array($_POST['quantity'])) {
		$orders->SaveBasket($_POST['quantity']);
	}
	
	// save comment in session
	$_SESSION['basket_comment'] = strip_tags($_POST['comment']);
	
	$order_id = $orders->CreateOrder();
	
	// redirect to avoid re-post on reload
	if (GIFTSHOP_REDIRECT_CONFIRM_ORDER) {
		header('location: giftshop.php?sel=view_order&order='.$order_id);
	} else {
		$_GET['order'] = $order_id;
		ViewOrder();
	}
		
	exit;
}

function PaymentOrder()
{
	global $smarty, $dbconn, $user, $orders;
	
	$order_id = !empty($_GET['order']) ? (int) $_GET['order'] : null;
	
	if (empty($order_id)) {
		ViewCategory();
		exit;
	}
	
	$id_user = $user[ AUTH_ID_USER ];
	
	// order data
	$order_data = $orders->GetOrder($order_id);
	
	// recipient data
	$user_data = GetUserInfo($order_data['id_user_to']);
	$user_data['comment'] = $order_data['comment'];
	
	// account data
	$account_data = array();
	$account_data['account_curr'] = $dbconn->getOne('SELECT account_curr FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($id_user));
	$account_data['account_curr_format'] = number_format($account_data['account_curr'], 2);
	
	// available payment systems
	$rs = $dbconn->Execute('SELECT name, template_name FROM '.BILLING_PAYSYSTEMS_TABLE.' WHERE used = "1"');
	$i = 0;
	$paysys = array();
	while (!$rs->EOF){
		$paysys[$i]['name'] = $rs->fields[0];
		$paysys[$i]['template_name'] = $rs->fields[1];
		$rs->MoveNext();
		$i++;
	}
	
	// form data
	$form = array();
	$form['subpage'] = 'payment';
	
	$smarty->assign('order_data', $order_data);
	$smarty->assign('user_data', $user_data);
	$smarty->assign('account_data', $account_data);
	$smarty->assign('paysys', $paysys);
	
	GiftShopPage($form);
	exit;
}

function PayWithService()
{
	global $dbconn, $user, $orders;
	
	$id_user = (int) $user[ AUTH_ID_USER ];
	
	// settings
	$currency = GetSiteSettings('site_unit_costunit');
	
	// read post
	$paysys = !empty($_POST['paysys']) ? $_POST['paysys'] : null;
	$order_id = !empty($_POST['order_id']) ? (int) $_POST['order_id'] : null;
	
	if (empty($paysys) || empty($order_id)) {
		ViewCategory();
		exit;
	}
	
	// amount and product_name
	$order = $orders->GetOrder($order_id);
	$amount = round($order['total_amount'], 2);
	
	if (empty($amount)) {
		ViewCategory();
		exit;
	}
	
	$product_name = $order['total_amount_format'].' '.$currency.' My Store payment for order #'.$order_id;
	
	// write billing request record
	$strSQL =
		'INSERT INTO '.BILLING_REQUESTS_TABLE.' SET
			id_user = ?, amount = ?, currency = ?, cost = ?, id_group = ?, id_product = ?,
			date_send = NOW(), status = "send", paysystem = ?, recurring = "0", product_name = ?';
	
	$dbconn->Execute($strSQL, array($id_user, $amount, $currency, $amount, PG_MY_STORE, $order_id, $paysys, $product_name));
	
	$id_trunzaction = $dbconn->Insert_ID();
	
	// make payment
	include_once './include/systems/functions/'.$paysys.'.php';
	
	MakePayment($id_trunzaction, $id_user);
	
	exit;
}

function PayFromAccount()
{
	global $lang, $dbconn, $user, $orders;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	// settings
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2'));
	
	// only USD for now
	$currency = $settings['site_unit_costunit'];
	
	# THB as second currency
	# $currency = $cost_2 ? $settings['site_unit_costunit_2'] : $settings['site_unit_costunit'];
	
	$order_id = isset($_POST['order_id']) ? (int) $_POST['order_id'] : null;
	
	if (empty($order_id)) {
		ViewCategory();
		exit;
	}

	$order = $orders->GetOrder($order_id);
	$amount = round($order['total_amount'], 2);
	
	if (empty($amount)) {
		ViewCategory();
		exit;
	}
	
	$account_curr = $dbconn->getOne('SELECT account_curr FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($id_user));
	$account_curr = round($account_curr, 2);
	
	if ($account_curr < $amount) {
		ViewCategory();
		exit;
	}
	
	$account_curr = round($account_curr - $amount, 2);
	
	// update account credits
	$dbconn->Execute('UPDATE '.BILLING_USER_ACCOUNT_TABLE.' SET account_curr = ? WHERE id_user = ?', array($account_curr, $id_user));
	
	// mark order as paid
	$dbconn->Execute('UPDATE '.GIFTSHOP_ORDERS.' SET paid_status = "1" WHERE id = ?', array($order_id));
	
	// send message to user and admin
	MyStore_Credits_Payment_User_Message($order_id, $order['fname_to'], $currency, $amount);
	MyStore_Credits_Payment_Admin_Message($order_id, $order['login_to'], $currency, $amount);
	
	// note: do not write billing send request record or billing entry record, as no money has been received
	
	// display order
	$_GET['order'] = $order_id;
	$err = $lang['giftshop']['credits_payment_thanks_msg'];
	$err = str_replace('[AMOUNT]', $currency.'&nbsp;'.number_format($amount, 2), $err);
	$err = str_replace('[ACCOUNT_CURR]', $currency.'&nbsp;'.number_format($account_curr, 2), $err);
	ViewOrder($err);
	exit;
}

function ATM_payment()
{
	global $lang, $smarty, $dbconn, $user, $orders;
	
	// id_user
	$id_user = $user[ AUTH_ID_USER ];
	
	// settings
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2'));
	
	// only USD for now
	$currency = $settings['site_unit_costunit'];
	
	# THB as second currency
	# $currency = $cost_2 ? $settings['site_unit_costunit_2'] : $settings['site_unit_costunit'];
	
	// order id
	$order_id = !empty($_POST['order_id']) ? (int) $_POST['order_id'] : null;
	
	if (empty($order_id)) {
		ViewCategory();
		exit;
	}
	
	// amount and product_name
	$order = $orders->GetOrder($order_id);
	$amount = round($order['total_amount'], 2);
	
	if (empty($amount)) {
		ViewCategory();
		exit;
	}
	
	$product_name = $order['total_amount_format'].' '.$currency.' '.$lang['giftshop']['product_name'];
	
	$err = '';
	
	// check checkboxes
	if (empty($_POST['atm_cb_1']) || empty($_POST['atm_cb_2']) || empty($_POST['atm_cb_3']) || empty($_POST['atm_cb_4'])) {
		$err .= '* '.$lang['err']['atm_checkboxes'].'<br />';
	}
	
	// evaluate post data
	$atm_payamount = (float) str_replace('$', '', FormFilter($_POST['atm_payamount']));
	$atm_year = (int) $_POST['atm_Year'];
	$atm_month = (int) $_POST['atm_Month'];
	$atm_day = (int) $_POST['atm_Day'];
	$atm_hour = (int) $_POST['atm_Hour'];
	$atm_minute = (int) $_POST['atm_Minute'];
	
	// check date
	if (checkdate($atm_month, $atm_day, $atm_year) && $atm_hour >= 0 && $atm_hour <= 23 && $atm_minute >= 0 && $atm_minute <= 59) {
		$atm_datetime = sprintf("%04d-%02d-%02d %02d:%02d:00", $atm_year, $atm_month, $atm_day, $atm_hour, $atm_minute);
		$smarty->assign('atm_datetime', $atm_datetime);
	} else {
		$err .= '* '.$lang['err']['atm_datetime'].'<br />';
	}
	
	// check payamount
	if (empty($_POST['atm_payamount'])) {
		$err .= '* '.$lang['err']['atm_amount_missing'].'<br />';
	} elseif ($amount != $atm_payamount) {
		$err .= '* '.$lang['err']['atm_amount_incorrect'].'<br />';
	}
	
	// re-display payment form on error
	if (isset($err) && $err) {
		$_GET['order'] = $order_id;
		ViewOrder($err, 'atm_payment');
		exit;
	}
	
	// only USD for now
	$amount_2 = 0;
	
	// additional info
	// $info = '';
	
	// create billing request record with status=send
	$dbconn->Execute(
		'INSERT INTO '.BILLING_REQUESTS_TABLE.' (
			id_user, amount, currency, cost, cost_2, id_group, id_product, date_send, status, paysystem, recurring, product_name
		) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
		array($id_user, $atm_payamount, $currency, $amount, $amount_2, PG_MY_STORE, $order_id, $atm_datetime, "send", "atm_payment", "0", $product_name));
	
	## $id = $dbconn->Insert_ID();
	
	// send message to user and admin
	MyStore_Offline_Payment_User_Message($order_id, $order['fname_to'], 'atm_payment', $currency, $atm_payamount);
	MyStore_Offline_Payment_Admin_Message($order_id, $order['login_to'], 'atm_payment', $currency, $atm_payamount);
	
	// display order
	$_GET['order'] = $order_id;
	$err = $lang['giftshop']['atm_payment_thanks_msg'];
	$err = str_replace('[AMOUNT]', $currency.'&nbsp;'.number_format($atm_payamount, 2), $err);
	ViewOrder($err);
	exit;
}

function Wire_Transfer()
{
	global $lang, $smarty, $dbconn, $user, $orders;
	
	// id_user
	$id_user = $user[ AUTH_ID_USER ];
	
	// settings
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2'));
	
	// only USD for now
	$currency = $settings['site_unit_costunit'];
	
	# THB as second currency
	# $currency = $cost_2 ? $settings['site_unit_costunit_2'] : $settings['site_unit_costunit'];
	
	// order id
	$order_id = !empty($_POST['order_id']) ? (int) $_POST['order_id'] : null;
	
	if (empty($order_id)) {
		ViewCategory();
		exit;
	}
	
	// amount and product_name
	$order = $orders->GetOrder($order_id);
	$amount = round($order['total_amount'], 2);
	
	if (empty($amount)) {
		ViewCategory();
		exit;
	}
	
	$product_name = $order['total_amount_format'].' '.$currency.' '.$lang['giftshop']['product_name'];
	
	$err = '';
	
	// check checkboxes
	if (empty($_POST['wire_cb_1']) || empty($_POST['wire_cb_2']) || empty($_POST['wire_cb_3'])) {
		$err .= '* '.$lang['err']['wire_checkboxes'].'<br />';
	}
	
	// evaluate post data
	$wire_payamount = (float) str_replace('$', '', FormFilter($_POST['wire_payamount']));
	$wire_year = (int) $_POST['wire_Year'];
	$wire_month = (int) $_POST['wire_Month'];
	$wire_day = (int) $_POST['wire_Day'];
	$wire_hour = (int) $_POST['wire_Hour'];
	$wire_minute = (int) $_POST['wire_Minute'];
	
	// check date
	if (checkdate($wire_month, $wire_day, $wire_year) && $wire_hour >= 0 && $wire_hour <= 23 && $wire_minute >= 0 && $wire_minute <= 59) {
		$wire_datetime = sprintf("%04d-%02d-%02d %02d:%02d:00", $wire_year, $wire_month, $wire_day, $wire_hour, $wire_minute);
		$smarty->assign('wire_datetime', $wire_datetime);
	} else {
		$err .= '* '.$lang['err']['wire_datetime'].'<br />';
	}
	
	// check payamount
	if (empty($_POST['wire_payamount'])) {
		$err .= '* '.$lang['err']['wire_amount_missing'].'<br />';
	} elseif ($amount != $wire_payamount) {
		$err .= '* '.$lang['err']['wire_amount_incorrect'].'<br />';
	}
	
	// check transfer no.
	if (empty($_POST['wire_transfer_no'])) {
		$err .= '* '.$lang['err']['wire_transfer_no'].'<br />';
	}
	
	// re-display payment form on error
	if (isset($err) && $err) {
		$_GET['order'] = $order_id;
		ViewOrder($err, 'wire_transfer');
		exit;
	}
	
	// only USD for now
	$amount_2 = 0;
	
	// additional info
	$info = 'No. '.trim($_POST['wire_transfer_no']);
	
	// create billing request record with status=send
	$dbconn->Execute(
		'INSERT INTO '.BILLING_REQUESTS_TABLE.' (
			id_user, amount, currency, cost, cost_2, id_group, id_product, date_send, status, paysystem, recurring, product_name, info
		) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
		array($id_user, $wire_payamount, $currency, $amount, $amount_2, PG_MY_STORE, $order_id, $wire_datetime, "send", "wire_transfer", "0", $product_name, $info));
	
	## $id = $dbconn->Insert_ID();
	
	// send message to user and admin
	MyStore_Offline_Payment_User_Message($order_id, $order['fname_to'], 'wire_transfer', $currency, $wire_payamount);
	MyStore_Offline_Payment_Admin_Message($order_id, $order['login_to'], 'wire_transfer', $currency, $wire_payamount);
	
	// display order
	$_GET['order'] = $order_id;
	$err = $lang['giftshop']['wire_transfer_thanks_msg'];
	$err = str_replace('[AMOUNT]', $currency.'&nbsp;'.number_format($wire_payamount, 2), $err);
	ViewOrder($err);
	exit;
}

function Bank_Cheque()
{
	global $lang, $smarty, $dbconn, $user, $orders;
	
	// id_user
	$id_user = $user[ AUTH_ID_USER ];
	
	// settings
	$settings = GetSiteSettings(array('site_unit_costunit', 'site_unit_costunit_2'));
	
	// only USD for now
	$currency = $settings['site_unit_costunit'];
	
	# THB as second currency
	# $currency = $cost_2 ? $settings['site_unit_costunit_2'] : $settings['site_unit_costunit'];
	
	// order id
	$order_id = !empty($_POST['order_id']) ? (int) $_POST['order_id'] : null;
	
	if (empty($order_id)) {
		ViewCategory();
		exit;
	}
	
	// amount and product_name
	$order = $orders->GetOrder($order_id);
	$amount = round($order['total_amount'], 2);
	
	if (empty($amount)) {
		ViewCategory();
		exit;
	}
	
	$product_name = $order['total_amount_format'].' '.$currency.' '.$lang['giftshop']['product_name'];
	
	$err = '';
	
	// check checkboxes
	if (empty($_POST['cheque_cb_1']) || empty($_POST['cheque_cb_2']) || empty($_POST['cheque_cb_3'])) {
		$err .= '* '.$lang['err']['cheque_checkboxes'].'<br />';
	}
	
	// evaluate post data
	$cheque_payamount = (float) str_replace('$', '', FormFilter($_POST['cheque_payamount']));
	$cheque_year = (int) $_POST['cheque_Year'];
	$cheque_month = (int) $_POST['cheque_Month'];
	$cheque_day = (int) $_POST['cheque_Day'];
	$cheque_hour = (int) $_POST['cheque_Hour'];
	$cheque_minute = (int) $_POST['cheque_Minute'];
	
	// check date
	if (checkdate($cheque_month, $cheque_day, $cheque_year) && $cheque_hour >= 0 && $cheque_hour <= 23 && $cheque_minute >= 0 && $cheque_minute <= 59) {
		$cheque_datetime = sprintf("%04d-%02d-%02d %02d:%02d:00", $cheque_year, $cheque_month, $cheque_day, $cheque_hour, $cheque_minute);
		$smarty->assign('cheque_datetime', $cheque_datetime);
	} else {
		$err .= '* '.$lang['err']['cheque_datetime'].'<br />';
	}
	
	// check payamount
	if (empty($_POST['cheque_payamount'])) {
		$err .= '* '.$lang['err']['cheque_amount_missing'].'<br />';
	} elseif ($amount != $cheque_payamount) {
		$err .= '* '.$lang['err']['cheque_amount_incorrect'].'<br />';
	}
	
	// check bank name
	if (empty($_POST['cheque_bank_name'])) {
		$err .= '* '.$lang['err']['cheque_bank_name'].'<br />';
	}
	
	// re-display payment form on error
	if (isset($err) && $err) {
		$_GET['order'] = $order_id;
		ViewOrder($err, 'bank_cheque');
		exit;
	}
	
	// only USD for now
	$amount_2 = 0;
	
	// additional info
	$info = trim($_POST['cheque_bank_name']);
	
	// create billing request record with status=send
	$dbconn->Execute(
		'INSERT INTO '.BILLING_REQUESTS_TABLE.' (
			id_user, amount, currency, cost, cost_2, id_group, id_product, date_send, status, paysystem, recurring, product_name, info
		) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
		array($id_user, $cheque_payamount, $currency, $amount, $amount_2, PG_MY_STORE, $order_id, $cheque_datetime, "send", "bank_cheque", "0", $product_name, $info));
	
	## $id = $dbconn->Insert_ID();
	
	// send message to user and admin
	MyStore_Offline_Payment_User_Message($order_id, $order['fname_to'], 'bank_cheque', $currency, $cheque_payamount);
	MyStore_Offline_Payment_Admin_Message($order_id, $order['login_to'], 'bank_cheque', $currency, $cheque_payamount);
	
	// display order
	$_GET['order'] = $order_id;
	$err = $lang['giftshop']['bank_cheque_thanks_msg'];
	$err = str_replace('[AMOUNT]', $currency.'&nbsp;'.number_format($cheque_payamount, 2), $err);
	ViewOrder($err);
	exit;
}

function ViewOrder($err = '', $paysystem = 'online_payment')
{
	global $lang, $smarty, $dbconn, $user, $orders;
	
	$order_id = !empty($_GET['order']) ? (int) $_GET['order'] : null;
	
	if (empty($order_id)) {
		ViewCategory();
		exit;
	}
	
	$id_user = $user[ AUTH_ID_USER ];
	
	// order data
	$order_data = $orders->GetOrder($order_id);
	
	// check online payment success
	if (isset($_GET['from']) && $_GET['from'] == 'payment') {
		if ($order_data['paid_status'] == '1') {
			$err = $lang['giftshop']['online_payment_thanks_msg'];
		} elseif (isset($_GET['cancel']) && $_GET['cancel'] == '1') {
			$err = $lang['giftshop']['online_payment_cancel_msg'];
		}
	}
	
	// recipient info
	$recipient_data = GetUserInfo($order_data['id_user_to']);
	
	// comment
	$smarty->assign('giftshop_comment', $order_data['comment']);
	
	// account data
	$account_data = array();
	$account_data['account_curr'] = $dbconn->getOne('SELECT account_curr FROM '.BILLING_USER_ACCOUNT_TABLE.' WHERE id_user = ?', array($id_user));
	$account_data['account_curr_format'] = number_format($account_data['account_curr'], 2);
	
	// available payment systems
	$rs = $dbconn->Execute('SELECT name, template_name FROM '.BILLING_PAYSYSTEMS_TABLE.' WHERE used = "1"');
	$i = 0;
	$paysys = array();
	while (!$rs->EOF){
		$paysys[$i]['name'] = $rs->fields[0];
		$paysys[$i]['template_name'] = $rs->fields[1];
		$rs->MoveNext();
		$i++;
	}
	
	$smarty->assign('paysys', $paysys);
	
	// form data
	$form = array();
	$form['subpage'] = 'view_order';
	
	// payment error
	if ($err != '') {
		$form['err'] = $err;
	}
	
	$smarty->assign('order_data', $order_data);
	$smarty->assign('recipient_data', $recipient_data);
	$smarty->assign('account_data', $account_data);
	$smarty->assign('data', $_POST);	// initialize payment forms
	$smarty->assign('paysystem', $paysystem);
	
	GiftShopPage($form);
	exit;
}

function EditOrder()
{
	global $orders;

	$id = !empty($_GET['order']) ? $_GET['order'] : null;
	
	if (empty($id)) {
		if (GIFTSHOP_REDIRECT_EDIT_ORDER) {
			header('location: giftshop.php?sel=order_history');
		} else {
			OrderHistory();
		}
		exit;
	}
	
	$orders->EditOrder($id);
	
	if (GIFTSHOP_REDIRECT_EDIT_ORDER) {
		header('location: giftshop.php?sel=view_basket');
	} else {
		BasketView();
	}
	exit;
}

function DeleteOrder()
{
	global $orders;

	$id = !empty($_GET['order']) ? $_GET['order'] : (!empty($_POST['order']) ? $_POST['order'] : null);
	
	if ($id) {
		$orders->DeleteOrder($id);
	}
	
	if (GIFTSHOP_REDIRECT_DELETE_ORDER) {
		header('location: giftshop.php?sel=order_history');
	} else {
		OrderHistory();
	}
	exit;
}

function OrderHistory()
{
	global $smarty, $orders;
	
	$form = array();
	$form['subpage'] = 'order_history';
	
	$order_data = $orders->GetOrders();
	// $order_totals = $orders->GetOrdersSum();
	
	$smarty->assign('order_data', $order_data);
	// $smarty->assign('order_totals', $order_totals);
	
	GiftShopPage($form);
	exit;
}

function Wishlist($err = '')
{
	global $lang, $smarty, $user;
	
	$data = array();
	$form = array();
	
	if (!empty($_POST))
	{
		$data['product_name'] = isset($_POST['product_name']) ? FormFilter($_POST['product_name']) : '';
		$data['description'] = isset($_POST['description']) ? FormFilter($_POST['description']) : '';
		$data['notes'] = isset($_POST['notes']) ? FormFilter($_POST['notes']) : '';
		
		$err_field = array();
		
		if (!strlen($data['product_name'])) {
			$err .= $lang['giftshop_wishes']['product_name'] . ', ';
			$err_field['product_name'] = 1;
		}
		
		if (!strlen($data['description'])) {
			$err .= $lang['giftshop_wishes']['description'] . ', ';
			$err_field['description'] = 1;
		}
		
		#if (!strlen($data['notes'])) {
		#	$err .= $lang['giftshop_wishes']['notes'] . ', ';
		#	$err_field['notes'] = 1;
		#}
		
		if ($err) {
			$smarty->assign('err_field', $err_field);
			$err = $lang['err']['invalid_fields'] . '<br/><br/>' . trim($err, ', ');
		} else {
			$err1 = MyStore_Wishlist_Admin_Message($data);
			$err2 = MyStore_Wishlist_User_Message($data);
			if ($err1 || $err2) {
				// send error
				header('Location: giftshop.php?sel=wishlist&msg=2');
				exit;
			} else {
				$_SESSION['wishlist_data'] = $data;
				header('Location: giftshop.php?sel=wishlist&msg=1');
				exit;
			}
		}
	}
	
	// display form
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$msg = isset($_REQUEST['msg']) ? $_REQUEST['msg'] : 0;
	
	switch ($msg) {
		case 1:
			if (isset($_SESSION['wishlist_data'])) {
				$data = $_SESSION['wishlist_data'];
				// unset($_SESSION['wishlist_data']);
			}
			$form['subpage'] = 'wishlist_thanks';
		break;
		case 2:
			$err = $lang['giftshop_wishes']['send_error'];
			$form['subpage'] = 'wishlist';
		break;
		default:
			$form['subpage'] = 'wishlist';
		break;
	}
	
	$form['err'] = $err;
	
	$smarty->assign('form', $form);
	$smarty->assign('data', $data);
	
	GiftShopPage($form);
	exit;
}

//====================
// WRAPPER
//====================

function GiftShopPage($form)
{
	global $lang, $config, $smarty, $user, $catalog, $orders;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$id_category = !empty($_GET['category']) && (int) $_GET['category'] ? (int) $_GET['category'] : 1;
	
	if (empty($_GET['category'])) {
		$id_category = 1;
	}
	
	$smarty->assign('user_gender', $user[ AUTH_GENDER ]);
	$smarty->assign('basket_info', $orders->GetBasketSum());
	$smarty->assign('orders_info', $orders->GetOrdersSum());
	$smarty->assign('category_active', $catalog->category_item($id_category));
	
	$form['currency'] = GetSiteSettings('site_unit_costunit');
	
	$smarty->assign('form', $form);
	$smarty->assign('section', $lang['subsection']);	## most probably not needed
	
	$smarty->display(TrimSlash($config['index_theme_path']).'/giftshop_table.tpl');
}

//====================
// MAIL FUNCTIONS
//====================

function MyStore_Credits_Payment_User_Message($order_id, $fname_to, $currency, $amount)
{
	global $config, $dbconn, $user;
	
	//------------------
	// external message
	//------------------
	
	// language
	$site_lang = !empty($user[ AUTH_SITE_LANGUAGE ]) ? $user[ AUTH_SITE_LANGUAGE ] : $config['default_lang'];
	
	// include mail language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	// content array
	$content_array			= array();
	$content_array['urls']	= GetUserEmailLinks();
	$content_array['login']	= $user[ AUTH_LOGIN ];
	$content_array['fname']	= $user[ AUTH_FNAME ];
	$content_array['sname']	= $user[ AUTH_SNAME ];
	
	// gender suffix
	$suffix = ($user[ AUTH_GENDER ] == GENDER_MALE ? '_e' : '_t');
	
	// message
	$amount = $currency.'&nbsp;'.number_format($amount, 2);
	
	$content_array['message'] = $lang_mail['mystore_credits_payment'.$suffix]['message'];
	$content_array['message'] = str_replace('[ORDER_ID]', $order_id, $content_array['message']);
	$content_array['message'] = str_replace('[FNAME_TO]', $fname_to, $content_array['message']);
	$content_array['message'] = str_replace('[AMOUNT]', $amount, $content_array['message']);
	$content_array['message'] = str_replace('[LOGIN_FROM]', $user[ AUTH_LOGIN ], $content_array['message']);
	
	// subject
	$subject = str_replace('[ORDER_ID]', $order_id, $lang_mail['mystore_credits_payment'.$suffix]['subject']);
	
	$name_to = trim($user[ AUTH_FNAME ].' '.$user[ AUTH_SNAME ]);
	
	// send message
	$mail_err = SendMail($site_lang, $user[ AUTH_EMAIL ], $config['site_email'], $subject, $content_array,
		'mail_noti_simple_generic_user', null, $name_to, '', 'mystore_credits_payment', $user[ AUTH_GENDER ]);
	
	//------------------
	// internal message
	//------------------
	
	// assemble body
	$body = $lang_mail['generic'.$suffix]['hello'].' '.$user[ AUTH_FNAME ].',<br><br>';
	$body.= $content_array['message'].'<br><br>';
	$body.= $lang_mail['generic'.$suffix]['admin_regards'];
	
	// store message
	$dbconn->Execute(
		'INSERT INTO '.MAILBOX_TABLE.' SET
			id_from = ?, id_to = ?, subject = ?, body = ?, was_read = "0", deleted_to = "0", deleted_from = "0", date_creation = NOW()',
		array(ID_ADMIN, $user[ AUTH_ID_USER ], $subject, $body));
	
	return $mail_err;
}

function MyStore_Credits_Payment_Admin_Message($order_id, $login_to, $currency, $amount)
{
	global $config, $dbconn, $user;
	
	// language
	$site_lang = !empty($user[ AUTH_SITE_LANGUAGE ]) ? $user[ AUTH_SITE_LANGUAGE ] : $config['default_lang'];
	
	// include mail language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	// content
	$content				= array();
	
	$content['order_id']	= $order_id;
	$content['amount']		= $currency.'&nbsp;'.number_format($amount, 2);
	
	$content['login']		= $user[ AUTH_LOGIN ];
	$content['fname']		= $user[ AUTH_FNAME ];
	$content['sname']		= $user[ AUTH_SNAME ];
	$content['email']		= $user[ AUTH_EMAIL ];
	
	$rs = $dbconn->Execute('SELECT fname, sname, email FROM '.USERS_TABLE.' WHERE login = ?', array($login_to));
	
	$content['login_to']	= $login_to;
	$content['fname_to']	= stripslashes($rs->fields[0]);
	$content['sname_to']	= stripslashes($rs->fields[1]);
	$content['email_to']	= stripslashes($rs->fields[2]);
	
	$rs->free();
	
	// message
	$content['message'] = str_replace('[ORDER_ID]', $order_id, $lang_mail['mystore_credits_payment_admin']['message']);
	
	// subject
	$subject = str_replace('[ORDER_ID]', $order_id, $lang_mail['mystore_credits_payment_admin']['subject']);
	
	// recipient
	if (REDIRECT_ADMIN_EMAIL && !IS_LIVE_SERVER) {
		$email_to = REDIRECT_ADMIN_EMAIL_TO;
	} else {
		$email_to = $config['site_email'];
	}
	
	// send message
	$mail_err = SendMail($site_lang, $email_to, $config['site_email'], $subject, $content,
					'mail_mystore_credits_payment_admin', null, '', '', 'mystore_credits_payment_admin');
	
	return $mail_err;
}

function MyStore_Offline_Payment_User_Message($order_id, $fname_to, $paysystem, $currency, $amount)
{
	global $config, $dbconn, $user;
	
	//------------------
	// external message
	//------------------
	
	// language
	$site_lang = !empty($user[ AUTH_SITE_LANGUAGE ]) ? $user[ AUTH_SITE_LANGUAGE ] : $config['default_lang'];
	
	// include mail language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	// content array
	$content_array			= array();
	$content_array['urls']	= GetUserEmailLinks();
	$content_array['fname'] = $user[ AUTH_FNAME ];
	$content_array['sname'] = $user[ AUTH_SNAME ];
	
	// gender suffix
	$suffix = ($user[ AUTH_GENDER ] == GENDER_MALE) ? '_e' : '_t';
	
	// subject
	$subject = $lang_mail['mystore_offline_payment'.$suffix]['subject'];
	$subject = str_replace('[ORDER_ID]', $order_id, $subject);
	
	// message
	$amount = $currency.'&nbsp;'.number_format($amount, 2);
	
	$content_array['message'] = $lang_mail['mystore_offline_payment'.$suffix]['message'][$paysystem];
	$content_array['message'] = str_replace('[ORDER_ID]', $order_id, $content_array['message']);
	$content_array['message'] = str_replace('[FNAME_TO]', $fname_to, $content_array['message']);
	$content_array['message'] = str_replace('[AMOUNT]', $amount, $content_array['message']);
	$content_array['message'] = str_replace('[LOGIN_FROM]', $user[ AUTH_LOGIN ], $content_array['message']);
	
	$name_to = trim($user[ AUTH_FNAME ].' '.$user[ AUTH_SNAME ]);
	
	// send message
	$mail_err = SendMail($site_lang, $user[ AUTH_EMAIL ], $config['site_email'], $subject, $content_array,
		'mail_noti_simple_generic_user', null, $name_to, '', 'mystore_offline_payment', $user[ AUTH_GENDER ]);
	
	//------------------
	// internal message
	//------------------
	
	// assemble body
	$body = $lang_mail['generic'.$suffix]['hello'].' '.$user[ AUTH_FNAME ].',<br><br>';
	$body.= $content_array['message'].'<br><br>';
	$body.= $lang_mail['generic'.$suffix]['admin_regards'];
	
	// store message
	$dbconn->Execute(
		'INSERT INTO '.MAILBOX_TABLE.' (
			id_from, id_to, subject, body, was_read, deleted_to, deleted_from, date_creation
		) VALUES (?, ?, ?, ?, "0", "0", "0", NOW())',
		array(ID_ADMIN, $user[ AUTH_ID_USER ], $subject, $body));
	
	return $mail_err;
}


function MyStore_Offline_Payment_Admin_Message($order_id, $login_to, $paysystem, $currency, $amount)
{
	global $config, $dbconn, $user;
	
	// language
	$site_lang = $config['default_lang'];
	
	// include mail language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	// content
	$content				= array();
	
	$content['order_id']	= $order_id;
	$content['amount']		= $currency.'&nbsp;'.number_format($amount, 2);
	$content['paysystem']	= $paysystem;
	
	$content['login']		= $user[ AUTH_LOGIN ];
	$content['fname']		= $user[ AUTH_FNAME ];
	$content['sname']		= $user[ AUTH_SNAME ];
	$content['email']		= $user[ AUTH_EMAIL ];
	
	$rs = $dbconn->Execute('SELECT fname, sname, email FROM '.USERS_TABLE.' WHERE login = ?', array($login_to));
	
	$content['login_to']	= $login_to;
	$content['fname_to']	= stripslashes($rs->fields[0]);
	$content['sname_to']	= stripslashes($rs->fields[1]);
	$content['email_to']	= stripslashes($rs->fields[2]);
	
	$rs->free();
	
	// subject
	$subject = $lang_mail['mystore_offline_payment_admin']['subject'];
	$subject = str_replace('[ORDER_ID]', $order_id, $subject);
	
	// message
	$content['message'] = $lang_mail['mystore_offline_payment_admin']['message'][$paysystem];
	$content['message'] = str_replace('[ORDER_ID]', $order_id, $content['message']);
	
	// recipient
	if (REDIRECT_ADMIN_EMAIL && !IS_LIVE_SERVER) {
		$email_to = REDIRECT_ADMIN_EMAIL_TO;
	} else {
		$email_to = $config['site_email'];
	}
	
	// send message
	$mail_err = SendMail($site_lang, $email_to, $config['site_email'], $subject, $content,
					'mail_mystore_offline_payment_admin', null, '', '', 'mystore_offline_payment_admin');
	
	return $mail_err;
}


function MyStore_Wishlist_User_Message($data)
{
	global $config, $dbconn, $user;
	
	//------------------
	// external message
	//------------------
	
	// language
	$site_lang = !empty($user[ AUTH_SITE_LANGUAGE ]) ? $user[ AUTH_SITE_LANGUAGE ] : $config['default_lang'];
	
	// include mail language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	// content
	$content			= $data;
	
	$content['login']	= $user[ AUTH_LOGIN ];
	$content['fname']	= $user[ AUTH_FNAME ];
	$content['sname']	= $user[ AUTH_SNAME ];
	$content['email']	= $user[ AUTH_EMAIL ];
	
	// gender specific langage items
	$suffix = ($user[ AUTH_GENDER ] == GENDER_MALE ? '_e' : '_t');
	
	// subject
	$subject = $lang_mail['mystore_wish'.$suffix]['subject'];
	
	// recipient
	$name_to = trim($user[ AUTH_FNAME ].' '.$user[ AUTH_SNAME ]);
	
	// send message
	$mail_err = SendMail($site_lang, $user[ AUTH_EMAIL ], $config['site_email'], $subject, $content,
					'mail_mystore_wish_user', null, $name_to, '', 'mystore_wish', $user[ AUTH_GENDER ]);
	
	return $mail_err;
}


function MyStore_Wishlist_Admin_Message($data)
{
	global $config, $dbconn, $user;
	
	// language
	$site_lang = $config['default_lang'];
	
	// include mail language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	// content
	$content			= $data;
	
	$content['login']	= $user[ AUTH_LOGIN ];
	$content['fname']	= $user[ AUTH_FNAME ];
	$content['sname']	= $user[ AUTH_SNAME ];
	$content['email']	= $user[ AUTH_EMAIL ];
	
	// subject
	$subject	= str_replace('[login]', $content['login'], $lang_mail['mystore_wish_admin']['subject']);
	
	// sender
	$name_from = trim($user[ AUTH_FNAME ].' '.$user[ AUTH_SNAME ]);
	
	// recipient
	if (REDIRECT_ADMIN_EMAIL && !IS_LIVE_SERVER) {
		$email_to = REDIRECT_ADMIN_EMAIL_TO;
	} else {
		$email_to = $config['site_email'];
	}
	
	// send message
	$mail_err = SendMail($site_lang, $email_to, $user[ AUTH_EMAIL ], $subject, $content,
					'mail_mystore_wish_admin', null, '', $name_from, 'mystore_wish_admin');
	
	return $mail_err;
}

?>