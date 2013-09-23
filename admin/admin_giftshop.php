<?php
/**
*
* @author Katya Kashkova<katya@pilotgroup.net>, Pilot Group <http://www.pilotgroup.net/>
* @date  28.07.2005 1:12
* @author Ralf Strehle<ralf.strehle@yahoo.de>
* @date 11/12/2010
*
**/

require_once '../include/config.php';
require_once '../common.php';
require_once 'config_admin.php';
require_once 'functions_auth.php';
require_once 'functions_admin.php';
include_once '../include/class.phpmailer.php';
include_once '../include/functions_mail.php';
require_once 'class.catalog.php';
require_once 'class.basket.php';

error_reporting(E_ALL);

$auth = auth_user();
login_check($auth);
$mode = IsFileAllowed($auth[ AUTH_ID_USER ], GetRightModulePath(__FILE__), 'giftshop');

$catalog = new Catalog($config, $dbconn, $smarty, $lang);

$sel = !empty($_GET['sel']) ? $_GET['sel'] : (!empty($_POST['sel']) ? $_POST['sel'] : '');

if ($mode == 1) {
	switch ($sel) {
		case 'catalog': CatalogCategoryList(); break;
		case 'catedit': CatalogCategoryForm('edit'); break;
		case 'catadd': CatalogCategoryForm('add'); break;
		case 'catupdate': CatalogCategoryUpdate(); break;
		case 'catinsert': CatalogCategoryInsert(); break;
		case 'catdel': CatalogCategoryDelete(); break;
		
		case 'items': CatalogItemsList(); break;
		case 'itemsedit': CatalogItemsForm('edit'); break;
		case 'itemsadd': CatalogItemsForm('add'); break;
		case 'itemsinsert': CatalogItemsInsert(); break;
		case 'itemsupdate': CatalogItemsUpdate(); break;
		case 'itemsdel': CatalogItemsDelete(); break;
		
		case 'orders': OrdersList(); break;
/*		case 'orders_status': OrdersChange(); break; */	// approve billing request instead
		case 'orders_procured': OrdersProcured(); break;
		case 'orders_shipped': OrdersShipped(); break;
		case 'orders_delivery': OrdersDelivery(); break;
		case 'orders_delete': OrdersDelete(); break;
		default: CatalogCategoryList();
	}
}

exit;

//====================
// CATEGORIES
//====================

function CatalogCategoryList()
{
	global $smarty, $config, $config_admin, $page, $lang, $catalog;
	
	AdminMainMenu($lang['giftshop']);
	
	$page = !empty($_GET['page']) ? $_GET['page'] : (!empty($_POST['page']) ? $_POST['page'] : null);
	
	if (empty($page)) {
		$page = 1;
	}
	
	$data = $catalog->category_list($page, $config_admin['giftshop_numpage']);
	
	if (!empty($data)) {
		$num_records = $catalog->category_count();
		$smarty->assign('links', GetLinkStr($num_records, $page, 'admin_giftshop.php?', $config_admin['giftshop_numpage']));
	}
	
	$form['page'] = $page;
	
	$smarty->assign('form', $form);
	$smarty->assign('data', $data);
	
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_giftshop_category_table.tpl');
	exit;
}

function CatalogCategoryForm($par, $data=array(), $err='')
{
	global $smarty, $config, $lang, $catalog;

	AdminMainMenu($lang['giftshop']);
	
	$page = !empty($_GET['page']) ? (int) $_GET['page'] : (!empty($_POST['page']) ? (int) $_POST['page'] : null);
	
	if (empty($page)) {
		$page = 1;
	}

	$id = !empty($_GET['id']) ? (int) $_GET['id'] : (!empty($_POST['id']) ? (int) $_POST['id'] : null);
	
	// invalid function call
	if ($par == 'edit' && empty($id)) {
		CatalogCategoryList();
		exit;
	}
	
	// sorter initialization
	$max_sorter = $catalog->category_count();
	
	if ($par == 'add') {
		$max_sorter++;
	}
	
	$sorter = array();
	
	for ($i = 1; $i <= $max_sorter; $i++) {
		$sorter[$i]['sel'] = 0;
	}
	
	// form initialization
	if ($err) {
		$form['err'] = $err;
		if ($par == 'edit') {
			$temp = $catalog->category_item($id);
			$data['icon_path'] = $temp['icon_path'];
			unset($temp);
		}
		$sorter[ $data['sorter'] ]['sel'] = 1;
	} elseif ($par == 'edit') {
		$data = $catalog->category_item($id);
		$sorter[ $data['sorter'] ]['sel'] = 1;
	} else {
		$data = array();
		$sorter[ $max_sorter ]['sel'] = 1;
	}
	
	$form['par'] = $par;
	$form['page'] = $page;
	
	$smarty->assign('form', $form);
	$smarty->assign('data', $data);
	$smarty->assign('sorter', $sorter);
	
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_giftshop_category_form.tpl');
	exit;
}

function CatalogCategoryInsert()
{
	global $lang, $catalog;
	
	// check post
	if (empty($_POST['e'])) {
		CatalogCategoryList();
		exit;
	}
	
	// populate data
	$data = array();
	$data['name']		= strip_tags(trim($_POST['name']));
	$data['comment']	= strip_tags(trim($_POST['comment']));
	$data['status']		= empty($_POST['status']) ? '0' : '1';
	$data['sorter']		= intval($_POST['sorter']);
	$data['icon']		= $_FILES['icon'];
	
	// validate data
	if (!$data['name']) {
		CatalogCategoryForm('add', $data, $lang['err']['invalid_fields'].$lang['giftshop']['name']);
		exit;
	}
	
	// insert record
	$err = $catalog->category_insert($data);
	
	if ($err){
		CatalogCategoryForm('add', $data, $err);
		exit;
	}
	
	CatalogCategoryList();
	exit;
}

function CatalogCategoryUpdate()
{
	global $lang, $catalog;

	// invalid post
	if (empty($_POST['id']) || empty($_POST['e'])) {
		CatalogItemsList();
		exit;
	}
	
	$id = (int) $_POST['id'];
	
	// handle delete picture
	if (!empty($_POST['picdel'])) {
		$catalog->category_picture_delete($id);
		CatalogCategoryForm('edit');
		exit;
	}
	
	// populate data
	$data = array();
	$data['id'] = $id;
	$data['name'] = strip_tags(trim($_POST['name']));
	$data['comment'] = strip_tags(trim($_POST['comment']));
	$data['status'] = empty($_POST['status']) ? '0' : '1';
	$data['sorter'] = intval($_POST['sorter']);
	$data['icon'] = $_FILES['icon'];
	
	// validate data
	if (!$data['name']) {
		CatalogCategoryForm('edit', $data, $lang['err']['invalid_fields'].$lang['giftshop']['name']);
		return;
	}
	
	// update record
	$err = $catalog->category_update($data);
	
	if ($err) {
		CatalogCategoryForm('edit', $data, $err);
		exit;
	}
	
	CatalogCategoryList();
	exit;
}

function CatalogCategoryDelete()
{
	global $catalog;
	
	$id = !empty($_GET['id']) ? $_GET['id'] : (!empty($_POST['id']) ? $_POST['id'] : null);
	
	if (!empty($id)) {
		$catalog->category_delete($id);
	}
	
	header('location: admin_giftshop.php');
	exit;
}

//====================
// ITEMS
//====================

function CatalogItemsList()
{
	global $smarty, $config, $config_admin, $page, $lang, $catalog;

	AdminMainMenu($lang['giftshop']);

	$id_category = !empty($_GET['id_category']) ? $_GET['id_category'] : (!empty($_POST['id_category']) ? $_POST['id_category'] : '');
	
	// invalid function call
	if (empty($id_category)) {
		CatalogItemsList();
		return;
	}
	
	// page
	$page = !empty($_GET['page']) ? $_GET['page'] : (!empty($_POST['page']) ? $_POST['page'] : '');
	
	if (empty($page)) {
		$page = 1;
	}
	
	// data
	$data = $catalog->items_list($id_category, $page, $config_admin['giftshop_numpage']);
	
	if (!empty($data)) {
		$num_records = $catalog->items_count($id_category);
		$param = 'admin_giftshop.php?sel=items&amp;id_category='.$id_category.'&amp;';
		$smarty->assign('links', GetLinkStr($num_records, $page, $param, $config_admin['giftshop_numpage']));
	}
	
	$form['page'] = $page;
	$form['curency'] = GetSiteSettings('site_unit_costunit');

	$smarty->assign('form', $form);
	$smarty->assign('data', $data);
	$smarty->assign('categories', $catalog->category_all_list());
	$smarty->assign('parent', $catalog->category_item($id_category));
	
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_giftshop_items_table.tpl');
	exit;
}

function CatalogItemsForm($par, $data=array(), $err='')
{
	global $smarty, $config, $lang, $catalog;
	
	AdminMainMenu($lang['giftshop']);
	
	$page = !empty($_GET['page']) ? (int) $_GET['page'] : (!empty($_POST['page']) ? (int) $_POST['page'] : null);
	
	if (empty($page)) {
		$page = 1;
	}
	
	$id = !empty($_GET['id']) ? (int) $_GET['id'] : (!empty($_POST['id']) ? (int) $_POST['id'] : null);
	$id_category = !empty($_GET['id_category']) ? (int) $_GET['id_category'] : (!empty($_POST['id_category']) ? (int) $_POST['id_category'] : null);
	
	// invalid function call
	if ($par == 'edit' && empty($id)) {
		CatalogItemsList();
		exit;
	}
	
	if ($par == 'add' && empty($id_category)) {
		CatalogItemsList();
		exit;
	}
	
	// lookup category from item
	if ($id && empty($id_category)) {
		$temp = $catalog->items_item($id);
		$id_category = $temp['id_category'];
		unset($temp);
	}
	
	// sorter initialization
	$max_sorter = $catalog->items_count($id_category);
	
	if ($par == 'add') {
		$max_sorter++;
	}
	
	$sorter = array();
	
	for ($i = 1; $i <= $max_sorter; $i++) {
		$sorter[$i]['sel'] = 0;
	}
	
	// form initialization
	if ($err)
	{
		// dont show err if gallery pic was uploaded successfully
		if ($err != 'imgupload') {
			$form['err'] = $err;
		}
		
		if ($par == 'edit') {
			if ($data) {
				$temp = $catalog->items_item($id);
				$data['icon_path'] = $temp['icon_path'];
				$data['gallery'] =  $temp['gallery'];
				unset($temp);
			} else {
				// for error during gallery upload
				$data = $catalog->items_item($id);
			}
		}
		
		$sorter[ $data['sorter'] ]['sel'] = 1;
	}
	elseif ($par == 'edit')
	{
		$data = $catalog->items_item($id);
		$sorter[ $data['sorter'] ]['sel'] = 1;
	}
	else
	{
		$data = array();
		$data['id_category'] = $id_category;
		$sorter[ $max_sorter ]['sel'] = 1;
	}
	
	if (empty($data['price'])) {
		$data['price'] = 0;
	}
	
	$data['price'] = sprintf('%01.2f', $data['price']);

	$form['par'] = $par;
	$form['page'] = $page;
	$form['curency'] = GetSiteSettings('site_unit_costunit');

	$smarty->assign('form', $form);
	$smarty->assign('data', $data);
	$smarty->assign('sorter', $sorter);
	$smarty->assign('categories', $catalog->category_all_list());

	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_giftshop_items_form.tpl');
	exit;
}

function CatalogItemsInsert()
{
	global $lang, $catalog;
	
	// invalid post
	if (empty($_POST['e'])) {
		CatalogItemsList();
		exit;
	}
	
	// category changed
	if ($_POST['cat'] != $_POST['id_category']) {
		$_POST['page'] = 1;
	}
	
	// populate data
	$data = array();
	$data['name'] = strip_tags(trim($_POST['name']));
	$data['comment'] = strip_tags(trim($_POST['comment']));
	$data['status'] = empty($_POST['status']) ? '0' : '1';
	$data['promote'] = empty($_POST['promote']) ? '0' : '1';
	$data['price'] = (float) $_POST['price'];
	$data['id_category'] = (int) $_POST['id_category'];
	$data['sorter'] = (int) $_POST['sorter'];
	$data['icon'] = $_FILES['icon'];
	
	// validate data
	if (!$data['name']) {
		CatalogItemsForm('add', $data, $lang['err']['invalid_fields'].$lang['giftshop']['name']);
		exit;
	}
	
	if (!$data['price']) {
		CatalogItemsForm('add', $data, $lang['err']['invalid_fields'].$lang['giftshop']['price']);
		exit;
	}
	
	// insert record
	$err = $catalog->items_insert($data);
	
	if (!empty($err)) {
		CatalogItemsForm('add', $data, $err);
		exit;
	}
	
	header('Location: admin_giftshop.php?sel=items&id_category='.$data['id_category'].'&page='.$_POST['page']);
	exit;
}

function CatalogItemsUpdate()
{
	global $lang, $catalog;
	
	// invalid post
	if (empty($_POST['id']) || empty($_POST['e'])) {
		CatalogItemsList();
		exit;
	}
	
	$id = (int) $_POST['id'];
	
	// category changed
	if ($_POST['cat'] != $_POST['id_category']) {
		$_POST['page'] = 1;
	}
	
	// handle delete picture
	if (!empty($_POST['picdel'])) {
		if (!empty($_POST['picdelid'])) {
			$catalog->items_gallery_delete($_POST['picdelid']);
		} else {
			$catalog->items_picture_delete($id);
		}
		CatalogItemsForm('edit');
		exit;
	}
	
	// handle gallery upload
	if (!empty($_POST['imgupload'])) {
		$ret = $catalog->items_gallery_insert($_FILES['images'], $id);
		if (!$ret) {
			$ret = 'imgupload';
		}
		CatalogItemsForm('edit', array(), $ret);
		exit;
	}
	
	// populate data
	$data = array();
	$data['id']				= $id;
	$data['name']			= strip_tags(trim($_POST['name']));
	$data['comment']		= strip_tags(trim($_POST['comment']));
	$data['status']			= empty($_POST['status']) ? '0' : '1';
	$data['promote']		= empty($_POST['promote']) ? '0' : '1';
	$data['price']			= (float) $_POST['price'];
	$data['id_category']	= (int) $_POST['id_category'];
	$data['sorter']			= (int) $_POST['sorter'];
	$data['icon']			= $_FILES['icon'];
	
	// validate data
	if (!$data['name']) {
		CatalogItemsForm('edit', $data, $lang['err']['invalid_fields'].$lang['giftshop']['name']);
		exit;
	}
	
	if (!$data['price']) {
		CatalogItemsForm('add', $data, $lang['err']['invalid_fields'].$lang['giftshop']['price']);
		exit;
	}
	
	// update record
	$err = $catalog->items_update($data);
	
	if (!empty($err)) {
		CatalogItemsForm('edit', $data, $err);
		exit;
	}
	
	header('Location: admin_giftshop.php?sel=items&id_category='.$data['id_category'].'&page='.$_POST['page']);
	exit;
}

function CatalogItemsDelete()
{
	global $catalog;

	if (empty($_GET['id'])) {
		CatalogItemsList();
		exit;
	}
	
	$id = (int) $_GET['id'];
	
	if (empty($_GET['id_category'])) {
		$temp = $catalog->items_item($id);
		$_GET['id_category'] = $temp['id_category'];
		unset($temp);
	}
	
	$catalog->items_delete($id);
	
	CatalogItemsList();
	exit;
}

function OrdersList()
{
	global $smarty, $dbconn, $config, $config_admin, $page, $lang, $catalog;

	AdminMainMenu($lang['giftshop']);

	$form['curency'] = GetSiteSettings('site_unit_costunit');

	$page = !empty($_GET['page']) ? (int) $_GET['page'] : (!empty($_POST['page']) ? (int) $_POST['page'] : 1);
	
	if (empty($page)) {
		$page = 1;
	}
	
	$num_records = (int) $dbconn->getOne('SELECT COUNT(id) FROM '.GIFTSHOP_ORDERS);
	
	$lim_min = ($page - 1) * (int) $config_admin['news_numpage'];
	$lim_max = (int) $config_admin['news_numpage'];
	
	$rs = $dbconn->Execute(
		'SELECT id, DATE_FORMAT(date_order, "'.$config['date_format'].'") AS date_order,
				id_user_from, id_user_to, paid_status, procured_status, shipped_status, delivery_status, comment
		   FROM '.GIFTSHOP_ORDERS.'
	   ORDER BY id DESC
		  LIMIT '.$lim_min.', '.$lim_max);
	
	$i = 0;
	$orders = array();
	
	if ($rs->RowCount() > 0) {
		while(!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$orders[$i] = $row;
			$orders[$i]['number'] = $i+1;
			$orders[$i]['user_from'] = GetUserInfo($row['id_user_from']);
			$orders[$i]['user_to'] = GetUserInfo($row['id_user_to']);
			$orders[$i]['paid_status'] = $row['paid_status'] ? '+' : '-';
			$orders[$i]['procured_status'] = $row['procured_status'] ? '+' : '-';
			$orders[$i]['shipped_status'] = $row['shipped_status'] ? '+' : '-';
			$orders[$i]['delivery_status'] = $row['delivery_status'] ? '+' : '-';
			$orders[$i]['order'] = '';
			$orders[$i]['total'] = 0;

			$rs_sub = $dbconn->Execute('SELECT id_item, currency, quantity FROM '.GIFTSHOP_ORDERS_ITEMS.' WHERE id_order = ?', array($row['id']));
			
			$j = 1;
			
			while (!$rs_sub->EOF) {
				$row_sub = $rs_sub->GetRowAssoc(false);
				$temp = $catalog->items_item($row_sub['id_item']);
				$orders[$i]['order'] .= $j.'. '.$temp['name'].' (<b>'.$row_sub['quantity'].'</b> x <b>'.sprintf('%01.2f', $temp['price']).'</b> '.$form['curency'].')<br>';
				$orders[$i]['total'] += $row_sub['currency'] * $row_sub['quantity'];
				$rs_sub->MoveNext();
				$j++;
			}
			
			if ($orders[$i]['comment']) {
				$orders[$i]['order'] .= '<br><i>'.$orders[$i]['comment'].'</i>';
			}
			$orders[$i]['total'] = number_format($orders[$i]['total'], 2);
			$rs->MoveNext();
			$i++;
		}
	}
	
	$smarty->assign('links', GetLinkStr($num_records, $page, 'admin_giftshop.php?', $config_admin['giftshop_numpage']));
	
	$form['page'] = $page;
	$form['file_name'] = 'admin_giftshop.php';
	
	$smarty->assign('form', $form);
	$smarty->assign('items', $orders);
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_giftshop_orders_table.tpl');
	exit;
}

function GetUserInfo($id_user)
{
	global $dbconn, $user;

	$rs = $dbconn->Execute('SELECT id, login, sname, fname, gender, date_birthday FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
	$user = $rs->GetRowAssoc(false);
	
	$user['name'] = $user['fname'].' '.$user['sname'];
	$user['age'] = AgeFromBDate($user['date_birthday']);
	
	return $user;
}

// DEACTIVATED. APPROVE BILLING REQUEST INSTEAD
/*
function OrdersChange()
{
	global $smarty, $dbconn, $config, $config_admin, $page, $lang, $catalog;

	$id = !empty($_GET['id']) ? $_GET['id'] : (!empty($_POST['id']) ? $_POST['id'] : '');
	$page = !empty($_GET['page']) ? $_GET['page'] : (!empty($_POST['page']) ? $_POST['page'] : '');
	
	if ($id) {
		$dbconn->Execute('UPDATE '.GIFTSHOP_ORDERS.' SET paid_status = "1" WHERE id = ?', array($id));
	}
	
	header('location: admin_giftshop.php?sel=orders&page='.$page);
	exit;
}
*/

function OrdersProcured()
{
	global $dbconn, $page;
	
	$id = !empty($_GET['id']) ? $_GET['id'] : (!empty($_POST['id']) ? $_POST['id'] : '');
	$page = !empty($_GET['page']) ? $_GET['page'] : (!empty($_POST['page']) ? $_POST['page'] : '1');
	
	if ($id) {
		$dbconn->Execute('UPDATE '.GIFTSHOP_ORDERS.' SET procured_status = "1" WHERE id = ?', array($id));
		OrderStatusChange_User_Messages($id, 'procured');
	}
	
	header('location: admin_giftshop.php?sel=orders&page='.$page);
	exit;
}

function OrdersShipped()
{
	global $dbconn, $page;
	
	$id = !empty($_GET['id']) ? $_GET['id'] : (!empty($_POST['id']) ? $_POST['id'] : '');
	$page = !empty($_GET['page']) ? $_GET['page'] : (!empty($_POST['page']) ? $_POST['page'] : '1');
	
	if ($id) {
		$dbconn->Execute('UPDATE '.GIFTSHOP_ORDERS.' SET shipped_status = "1" WHERE id = ?', array($id));
		OrderStatusChange_User_Messages($id, 'shipped');
	}
	
	header('location: admin_giftshop.php?sel=orders&page='.$page);
	exit;
}

function OrdersDelivery()
{
	global $dbconn, $page;
	
	$id = !empty($_GET['id']) ? $_GET['id'] : (!empty($_POST['id']) ? $_POST['id'] : '');
	$page = !empty($_GET['page']) ? $_GET['page'] : (!empty($_POST['page']) ? $_POST['page'] : '1');
	
	if ($id) {
		$dbconn->Execute('UPDATE '.GIFTSHOP_ORDERS.' SET delivery_status = "1" WHERE id = ?', array($id));
		OrderStatusChange_User_Messages($id, 'delivery');
	}
	
	header('location: admin_giftshop.php?sel=orders&page='.$page);
	exit;
}

function OrdersDelete()
{
	global $dbconn, $config, $smarty, $catalog;

	$id = !empty($_GET['id']) ? $_GET['id'] : (!empty($_POST['id']) ? $_POST['id'] : '');
	$page = !empty($_GET['page']) ? $_GET['page'] : (!empty($_POST['page']) ? $_POST['page'] : '1');
	
	if ($id) {
#		$dbconn->Execute('DELETE FROM '.GIFTSHOP_ORDERS_ITEMS.' WHERE id_order = ?', array($id));
#		$dbconn->Execute('DELETE FROM '.GIFTSHOP_ORDERS.' WHERE id = ?', array($id));
		$user = array();
		$orders = new Orders($user, $config, $dbconn, $smarty, $catalog);
		$orders->DeleteOrder($id);
	}
	
	header('location: admin_giftshop.php?sel=orders&page='.$page);
	exit;
}

function OrderStatusChange_User_Messages($id_order, $status)
{
	global $config, $dbconn;
	
	// sender and recipient user data
	$strSQL =
		'SELECT o.id_user_from, o.id_user_to,
				u1.login AS login_from, u1.fname AS fname_from, u1.sname AS sname_from, u1.email AS email_from,
				u1.gender AS gender_from, u1.site_language AS site_language_from,
				u2.login AS login_to, u2.fname AS fname_to, u2.sname AS sname_to, u2.email AS email_to,
				u2.gender AS gender_to, u2.site_language AS site_language_to
		   FROM '.GIFTSHOP_ORDERS.' o
	  LEFT JOIN '.USERS_TABLE.' u1 ON u1.id = o.id_user_from
	  LEFT JOIN '.USERS_TABLE.' u2 ON u2.id = o.id_user_to
		  WHERE o.id = ?';
	$rs = $dbconn->Execute($strSQL, array($id_order));
	$row = $rs->GetRowAssoc(false);
	
	//-------------------
	// message to sender
	//-------------------
	
	// site language
	$site_lang = $row['site_language_from'] ? $row['site_language_from'] : $config['default_lang'];
	
	// include mail language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	// content
	$content_array = array();
	
	$content_array['urls']	= GetUserEmailLinks();
	
	$content_array['login'] = stripslashes($row['login_from']);
	$content_array['fname'] = stripslashes($row['fname_from']);
	$content_array['sname'] = stripslashes($row['sname_from']);
	
	// gender specific langage items
	$suffix = ($row['gender_from'] == GENDER_MALE) ? '_e' : '_t';
	
	// subject
	$subject = $lang_mail['mystore_order_status_sender'.$suffix]['subject'][$status];
	
	// message
	$content_array['message'] = $lang_mail['mystore_order_status_sender'.$suffix]['message'][$status];
	$content_array['message'] = str_replace('[FNAME_TO]', $row['fname_to'], $content_array['message']);
	$content_array['message'] = str_replace('[LOGIN_FROM]', $row['login_from'], $content_array['message']);
	
	$name_to = trim($row['fname_from'].' '.$row['sname_from']);
	
	// send external message
	$mail_err = SendMail($site_lang, $row['email_from'], $config['site_email'], $subject, $content_array,
		'mail_noti_simple_generic_user', null, $name_to, '', 'mystore_order_status_sender', $row['gender_from']);
	
	// create internal message
	$body = $lang_mail['generic'.$suffix]['hello'].' '.$content_array['fname'].',<br><br>';
	$body.= $content_array['message'].'<br><br>';
	$body.= $lang_mail['generic'.$suffix]['admin_regards'];
	
	$dbconn->Execute(
		'INSERT INTO '.MAILBOX_TABLE.' (
			id_from, id_to, subject, body, was_read, deleted_to, deleted_from, date_creation
		) VALUES (?, ?, ?, ?, "0", "0", "0", NOW())',
		array(ID_ADMIN, $row['id_user_from'], $subject, $body));
	
	//----------------------
	// message to recipient
	//----------------------
	
	// site language
	$site_lang = $row['site_language_to'] ? $row['site_language_to'] : $config['default_lang'];
	
	// include mail language file
	$lang_file = $dbconn->GetOne('SELECT lang_file FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($site_lang));
	$lang_mail = array();
	include $config['path_lang'].'mail/'.$lang_file;
	
	// content array
	$content_array			= array();
	
	$content_array['urls']	= GetUserEmailLinks();
	
	$content_array['login'] = stripslashes($row['login_to']);
	$content_array['fname'] = stripslashes($row['fname_to']);
	$content_array['sname'] = stripslashes($row['sname_to']);
	
	// gender specific langage items
	$suffix = ($row['gender_to'] == GENDER_MALE) ? '_e' : '_t';
	
	// subject
	$subject = $lang_mail['mystore_order_status_recipient'.$suffix]['subject'][$status];
	
	// message
	$content_array['message'] = $lang_mail['mystore_order_status_recipient'.$suffix]['message'][$status];
	$content_array['message'] = str_replace('[FNAME_FROM]', $row['fname_from'], $content_array['message']);
	$content_array['message'] = str_replace('[LOGIN_TO]', $row['login_to'], $content_array['message']);
	
	$name_to = trim($row['fname_to'].' '.$row['sname_to']);
	
	// send external message
	$mail_err = SendMail($site_lang, $row['email_to'], $config['site_email'], $subject, $content_array,
		'mail_noti_simple_generic_user', null, $name_to, '', 'mystore_order_status_recipient', $row['gender_to']);
	
	// create internal message
	$body = $lang_mail['generic'.$suffix]['hello'].' '.$content_array['fname'].',<br><br>';
	$body.= $content_array['message'].'<br><br>';
	$body.= $lang_mail['generic'.$suffix]['admin_regards'];
	
	$dbconn->Execute(
		'INSERT INTO '.MAILBOX_TABLE.' (
			id_from, id_to, subject, body, was_read, deleted_to, deleted_from, date_creation
		) VALUES (?, ?, ?, ?, "0", "0", "0", NOW())',
		array(ID_ADMIN, $row['id_user_to'], $subject, $body));
	
	return $mail_err;
}

?>