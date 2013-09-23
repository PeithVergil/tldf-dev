<?php
/**
*
* @author Katya Kashkova<katya@pilotgroup.net>, Pilot Group <http://www.pilotgroup.net/>
* @date 28.07.2005 1:12
*
* @author Bala
* @author Ralf Strehle
* @date 04/08/2009
*
**/

include_once '../include/config.php';
include_once '../common.php';
include_once 'config_admin.php';
include_once 'functions_auth.php';
include_once 'functions_admin.php';
include_once 'class.citycatalog.php';

$auth = auth_user();
login_check($auth);
$mode = IsFileAllowed($auth[0], GetRightModulePath(__FILE__), 'location');

$catalog = new CityCatalog($config, $dbconn, $smarty, $lang);

$file_name = isset($_SERVER['PHP_SELF']) ? AfterLastSlash($_SERVER['PHP_SELF']) : 'admin_citylocation.php';

$sel = isset($_GET['sel']) ? $_GET['sel'] : (isset($_POST['sel']) ? $_POST['sel'] : '');

if ($mode == 1)
{
	switch ($sel)
	{
		case 'catalog': CatalogCategoryList(); break;
		case 'catedit': CatalogCategoryForm('edit'); break;
		case 'catadd': CatalogCategoryAdd(); break;
		case 'catchange': CatalogCategoryChange(); break;
		case 'catdel': CatalogCategoryDelete(); break;
		case 'items': CatalogItemsList(); break;
		case 'itemsedit': CatalogItemsForm('edit'); break;
		case 'itemsadd': CatalogItemsAdd(); break;
		case 'itemschange': CatalogItemsChange(); break;
		case 'itemsdel': CatalogItemsDelete(); break;
		case 'upload_view': CatalogItemsUploadView();break;
		case 'orders': OrdersList(); break;
		case 'orders_status': OrdersChange(); break;
		case 'orders_delete': OrdersDelete(); break;
		case 'orders_delivery': OrdersDelivery(); break;
		default: CatalogCategoryList();
	}
}

exit;

//----------------------
// ITEMS
//----------------------

function CatalogCategoryList()
{
	global $smarty, $config, $config_admin, $page, $lang, $catalog, $file_name;
	
	AdminMainMenu($lang['location']);
	
	$page = isset($_GET['page']) ? intval($_GET['page']) : (isset($_POST['page']) ? intval($_POST['page']) : 0);
	
	if (!$page)
	{
		$page = 1;
	}
	
	$category_arr = $catalog->category_list($page, $config_admin['location_numpage']);
	
	if (!empty($category_arr))
	{
		$num_records = $catalog->category_count();
		$param = $file_name.'?';
		$smarty->assign('links', GetLinkStr($num_records, $page, $param, $config_admin['location_numpage']));
	}
	
	$form['page'] = $page;
	$form['file_name'] = $file_name;
	$form['add_link'] = './'.$file_name.'?sel=catadd&amp;page='.$page;
	
	$smarty->assign('category', $category_arr);
	$smarty->assign('header', $lang['location']);
	$smarty->assign('form', $form);
	$smarty->assign('button', $lang['button']);
	
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_location_category_table.tpl');
	exit;
}

function CatalogCategoryForm($par, $err='')
{
	global $smarty, $config, $lang, $catalog, $file_name;
	
	AdminMainMenu($lang['location']);
	
	// id
	$id = isset($_POST['id']) ? intval($_POST['id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);
	
	// check edit
	if ($par == 'edit' && !$id)
	{
		CatalogCategoryList();
		return;
	}
	
	// page
	$page = isset($_POST['page']) ? intval($_POST['page']) : (isset($_GET['page']) ? intval($_GET['page']) : 0);
	
	if (!$page) {
		$page = 1;
	}
	
	// sorter
	$ref_count = $catalog->category_count();
	
	$sorter_arr = array();
	
	for ($i = 0; $i < $ref_count; $i++) {
		$sorter_arr[$i]['sel'] = 0;
	}
	
	// fill data
	if ($err)
	{
		// postback
		$form['err'] = $err;
		
		$data = $_POST;
		
		if ($par == 'edit') {
			$temp_data = $catalog->category_item($id);
			$data['icon_path'] = $temp_data['icon_path'];
			unset($temp_data);
		}
		
		$sorter_arr[$_POST['sorter'] - 1]['sel'] = '1';
	}
	else
	{
		// no postback
		if ($par == 'edit') {
			// fresh edit
			$data = $catalog->category_item($id);
			$sorter_arr[$data['sorter'] - 1]['sel'] = '1';
		} else {
			// fresh add
			$sorter_arr[$ref_count]['sel'] = '1';
		}
	}
	
	// hidden form fields
	if ($par == 'edit') {
		$form['hiddens'] = '<input type="hidden" name="sel" value="catchange" />';
		$form['hiddens'].= '<input type="hidden" name="id" value="'.$id.'" />';
		$form['hiddens'].= '<input type="hidden" name="picdel" value="0" />';
	} else {
		$form['hiddens'] = '<input type="hidden" name="sel" value="catadd" />';
	}
	
	$form['hiddens'] .= '<input type="hidden" name="page" value="'.$page.'" />';
	
	$form['delete']		= $file_name.'?sel=catdel&amp;id='.$id.'&amp;page='.$page;
	$form['back']		= $file_name.'?page='.$page;
	$form['action']		= $file_name;
	$form['par']		= $par;
	$form['confirm']	= $lang['confirm']['location_category'];
	
	if (isset($data)) {
		$smarty->assign('data', $data);
	}
	
	$smarty->assign('sorter', $sorter_arr);
	$smarty->assign('form', $form);
	$smarty->assign('header', $lang['location']);
	$smarty->assign('button', $lang['button']);
	
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_location_category_form.tpl');
	exit;
}


function CatalogCategoryAdd()
{
	global $lang, $catalog;
	
	// check post
	if (empty($_POST['e']))
	{
		CatalogCategoryForm('add');
		return;
	}
	
	// read post and prepare data
	$data = $_POST;
	
	$data['comment'] = strip_tags(strval(trim($data['comment'])));
	$data['name'] = strip_tags(strval(trim($data['name'])));
	$data['sorter'] = isset($data['sorter']) ? intval($data['sorter']) : 0;
	$data['status'] = isset($data['status']) ? intval($data['status']) : 0;
	
	$data['icon_path'] = $_FILES['icon'];
	
	// validate data
	if (!$data['name'])
	{
		CatalogCategoryForm('add', $lang['err']['invalid_fields'].$lang['location']['name']);
		return;
	}
	
	// perform insert
	$err = $catalog->category_add($data);
	
	if ($err) {
		CatalogCategoryForm('add', $err);
		return;
	}
	
	CatalogCategoryList();
	return;
}


function CatalogCategoryChange()
{
	global $lang, $catalog;
	
	// check post
	if (empty($_POST['e']))
	{
		CatalogItemsForm('edit');
		return;
	}
	
	// check id
	$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
	
	if (empty($id))
	{
		CatalogCategoryList();
		return;
	}
	
	// delete picture
	if ($_POST['picdel'] == '1')
	{
		$catalog->category_picture_delete($id);
		CatalogCategoryForm('edit');
		return;
	}
	
	// read post and prepare data
	$data = $_POST;
	
	$data['id'] = $id;
	$data['comment'] = strip_tags(strval(trim($data['comment'])));
	$data['name'] = strip_tags(strval(trim($data['name'])));
	$data['sorter'] = intval($data['sorter']);
	$data['status'] = intval($data['status']);
	$data['icon_path'] = $_FILES['icon'];
	
	// validate data
	if (!$data['name'])
	{
		CatalogCategoryForm('edit', $lang['err']['invalid_fields'].$lang['location']['name']);
		return;
	}
	
	// perform update
	$err = $catalog->category_change($data);
	
	if ($err) {
		CatalogCategoryForm('edit', $err);
		return;
	}
	
	if ($_POST['upload'] == '1') {
		CatalogCategoryForm('edit');
		return;
	}
	
	CatalogCategoryList();
	return;
}


function CatalogCategoryDelete()
{
	global $catalog;
	
	$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);
	
	if (!$id) {
		CatalogCategoryList();
		return;
	}
	
	$catalog->category_delete($id);
	
	CatalogCategoryList();
	
	return;
}


//----------------------
// ITEMS
//----------------------

function CatalogItemsList()
{
	global $smarty, $config, $config_admin, $page, $lang, $catalog, $file_name;
	
	AdminMainMenu($lang['location']);
	
	// category
	$id_category = isset($_GET['id_category']) ? intval($_GET['id_category']) : (isset($_POST['id_category']) ? intval($_POST['id_category']) : 0);
		
	if (!$id_category)
	{
		CatalogCategoryList();
		return;
	}
	
	// page
	$page = isset($_GET['page']) ? intval($_GET['page']) : (isset($_POST['page']) ? intval($_POST['page']) : 0);
	
	if (!$page)
	{
		$page = 1;
	}
	
	// item list
	$items = $catalog->items_list($id_category, $page, $config_admin['location_numpage']);
	
	if (!empty($items))
	{
		$num_records = $catalog->items_count($id_category);
		$param = $file_name.'?sel=items&amp;id_category='.$id_category.'&amp;';
		$smarty->assign('links', GetLinkStr($num_records, $page, $param, $config_admin['location_numpage']));
	}
	
	$form['page'] = $page;
	$form['file_name'] = $file_name;
	$form['add_link'] = './'.$file_name.'?sel=itemsadd&amp;id_category='.$id_category.'&amp;page='.$page;
	$form['curency'] = GetSiteSettings('site_unit_costunit');
	
	$smarty->assign('items', $items);
	$smarty->assign('header', $lang['location']);
	$smarty->assign('form', $form);
	$smarty->assign('category', $catalog->category_all_list());
	$smarty->assign('parent', $catalog->category_item($id_category));
	$smarty->assign('button', $lang['button']);
	
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_location_items_table.tpl');
	exit;
}


function CatalogItemsForm($par, $err='')
{
	global $smarty, $config, $lang, $catalog, $file_name;
	
	AdminMainMenu($lang['location']);
	
	// settings
	$settings = GetSiteSettings(array('location_folder', 'video_max_size'));
	
	$folder = $settings['location_folder'];
	
	// id and category
	$id = isset($_POST['id']) && $_POST['id'] ? intval($_POST['id']) : (isset($_GET['id']) && $_GET['id'] ? intval($_GET['id']) : 0);
	
	$id_category = isset($_POST['id_category']) ? intval($_POST['id_category']) : (isset($_GET['id_category']) ? intval($_GET['id_category']) : 0);
	
	if ($id && !$id_category) {
		$temp_arr = $catalog->items_item($id);
		$id_category = $temp_arr['id_category'];
		unset($temp_arr);
	}
	
	// check edit
	if ($par == 'edit' && !$id) {
		CatalogItemsList();
		return;
	}
	
	// check add
	if ($par == 'add' && !$id_category) {
		CatalogItemsList();
		return;
	}
	
	// page
	$page = isset($_POST['page']) ? intval($_POST['page']) : (isset($_GET['page']) ? intval($_GET['page']) : 0);
	
	if (!$page) {
		$page = 1;
	}
	
	// sorter
	$ref_count = $catalog->items_count($id_category);
	
	$sorter_arr = array();
	
	for ($i = 0; $i < $ref_count; $i++) {
		$sorter_arr[$i]['sel'] = 0;
	}
	
	// fill data
	if ($err || !empty($_POST['cat']))
	{
		// postback
		$data = $_POST;
		
		if ($par == 'edit') {
			$temp_data = $catalog->items_item($id);
			$data['icon_path'] = $temp_data['icon_path'];
			$data['thumb_icon_path'] = $temp_data['thumb_icon_path'];
			$data['video_path'] = $temp_data['video_path'];
			$data['gallery'] = $temp_data['gallery'];
			unset($temp_data);
		}
		
		$sorter_arr[$_POST['sorter'] - 1]['sel'] = '1';
		
		// dont show err if gallery pic was uploaded
		if (isset($err) && $err != 'imgupload') {
			$form['err'] = $err;
		}
	}
	else
	{
		// no postback
		if ($par == 'edit') {
			// fresh edit
			$data = $catalog->items_item($id);
			$sorter_arr[$data['sorter']-1]['sel'] = '1';			
		} else {
			// fresh add
			$data['id_category'] = $id_category;
			$sorter_arr[$ref_count]['sel'] = '1';
		}
	}
	
	if (!isset($data['price'])) {
		$data['price'] = 0;
	}
	
	$data['price'] = sprintf('%01.2f', $data['price']);
	
	$data['video_image_path'] = $config['server'].$config['site_root'].$folder.'/default_video_icon.gif';
	$data['video_comment'] = str_replace('[size]', $settings['video_max_size'], $lang['confirm']['video_upload']);
	
	// form parameters
	if ($par == 'edit') {
		$form['hiddens'] = '<input type="hidden" name="sel" value="itemschange">';
		$form['hiddens'].= '<input type="hidden" name="id" value="'.$id.'">';
		$form['hiddens'].= '<input type="hidden" name="picdel" value="0">';
		$form['hiddens'].= '<input type="hidden" name="picdelid" value="0">';
		$form['hiddens'].= '<input type="hidden" name="viddel" value="0">';
		$form['hiddens'].= '<input type="hidden" name="viddelid" value="0">';
		$form['hiddens'].= '<input type="hidden" name="imgupload" value="0">';
	} else {
		$form['hiddens'] = '<input type="hidden" name="sel" value="itemsadd">';
	}
	
	$form['hiddens'] .= '<input type="hidden" name="page" value="'.$page.'" />';
		
	$form['delete']		= $file_name.'?sel=itemsdel&amp;id='.$id.'&amp;page='.$page;
	$form['back']		= $file_name.'?sel=items&amp;id_category='.$id_category.'&amp;page='.$page;
	$form['action']		= $file_name;
	$form['par']		= $par;
	$form['confirm']	= $lang['confirm']['location_item'];
	$form['curency']	= GetSiteSettings('site_unit_costunit');
	
	$smarty->assign('data', $data);
	$smarty->assign('sorter', $sorter_arr);
	$smarty->assign('category', $catalog->category_all_list());
	$smarty->assign('form', $form);
	$smarty->assign('header', $lang['location']);
	$smarty->assign('button', $lang['button']);
	
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_location_items_form.tpl');
	exit;
}


function CatalogItemsAdd()
{
	global $lang, $catalog;
	
	// check post
	if (empty($_POST['e']) || !empty($_POST['cat'])) {
		CatalogItemsForm('add');
		return;
	}
	
	// read post and prepare data
	$data = $_POST;
	
	$data['comment'] = Rep_Slashes(strip_tags(strval(trim($data['comment']))));
	$data['name'] = strip_tags(strval(trim($data['name'])));
	$data['sorter'] = isset($data['sorter']) ? intval($data['sorter']) : 0;
	$data['status'] = isset($data['status']) ? intval($data['status']) : 0;
	
	$data['icon_path'] = $_FILES['icon'];
	$data['video_path'] = $_FILES['video'];
	
	// validate data
	if (empty($data['name'])) {
		CatalogItemsForm('add', $lang['err']['invalid_fields'].$lang['location']['name']);
		return;
	}
	
	// perform update
	$err = $catalog->items_add($data);
	
	if ($err) {
		CatalogItemsForm('add', $err);
		return;
	}
	
	CatalogItemsList();
	return;
}


function CatalogItemsChange()
{
	global $lang, $catalog;
	
	// check post
	if (empty($_POST['e']) || !empty($_POST['cat'])) {
		CatalogItemsForm('edit');
		return;
	}
	
	// check id
	$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
	
	if (empty($id)) {
		CatalogItemsList();
		return;
	}
	
	// delete primary image
	if ($_POST['picdel'] == '1' && $_POST['picdelid'] == '0') {
		$catalog->items_picture_delete($id);
		CatalogItemsForm('edit');
		return;
	}
	
	// delete primary video
	if ($_POST['viddel'] == '1' && $_POST['viddelid'] == '0') {
		$catalog->items_video_delete($id);
		CatalogItemsForm('edit');
		return;
	}
	
	// delete gallery image
	if ($_POST['picdel'] == '1' && $_POST['picdelid'] != '0') {
		$catalog->items_gallery_delete(intval($_POST['picdelid']));
		CatalogItemsForm('edit');
		return;
	}
	
	// gallery image upload
	if ($_POST['imgupload'] == '1') {
		$err = $catalog->items_gallery_add($_FILES['images'], $id);
		if (!$err) {
			$err = 'imgupload';
		}
		CatalogItemsForm('edit', $err);
		return;
	}
	
	// read post and prepare data
	$data = $_POST;
	
	$data['id']			= $id;
	$data['comment']	= strip_tags(strval(trim($data['comment'])));
	$data['name']		= strip_tags(strval(trim($data['name'])));
	$data['sorter']		= isset($data['sorter']) ? intval($data['sorter']) : 0;
	$data['status']		= isset($data['status']) ? intval($data['status']) : 0;
	
	$data['icon_path']	= $_FILES['icon'];
	$data['video_path']	= $_FILES['video'];
	
	// validate data
	if (empty($data['name'])) {
		CatalogItemsForm('edit', $lang['err']['invalid_fields'].$lang['location']['name']);
		return;
	}
	
	// perform update
	$err = $catalog->items_change($data);
	
	if ($err) {
		CatalogItemsForm('edit', $err);
		return;
	}
	
	if ($_POST['upload'] == '1') {
		CatalogItemsForm('edit');
		return;
	}
	
	CatalogItemsList();
	return;
}


function CatalogItemsDelete()
{
	global $catalog;

	$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);
	
	if (!$id) {
		CatalogItemsList();
		return;
	}
	
	$catalog->items_delete($id);
	
	CatalogItemsList();
	
	return;
}


function CatalogItemsUploadView()
{
	global $smarty, $config;
	
	$id_file = trim($_GET['id_file']);
	
	$folder = GetSiteSettings('location_folder');
	
	$flv_name = explode('.', $id_file);
	
	$is_flv = 0;
	
	if (GetSiteSettings('use_ffmpeg') == 1) {
		if (file_exists($config['site_path'].$folder.'/'.$flv_name[0].'.flv')) {
			$is_flv = 1;
			$smarty->assign('is_flv', 1);
		}
	}
	
	if ($is_flv) {
		$data['file_path'] = $config['server'].$config['site_root'].$folder.'/'.$flv_name[0].'.flv';
		$data['image_path'] = $config['server'].$config['site_root'].$folder.'/'.$flv_name[0].'1.jpg';
	} else {
		$data['file_path'] = $config['server'].$config['site_root'].$folder.'/'.$id_file;
	}
	
	$smarty->assign('data', $data);
	
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_location_items_upload_view.tpl');
}


//----------------------
// ORDERS
//----------------------

function OrdersList()
{
	global $smarty, $dbconn, $config, $config_admin, $page, $lang, $catalog, $file_name;
	
	AdminMainMenu($lang['location']);
	
	// page
	$page = isset($_GET['page']) ? intval($_GET['page']) : isset($_POST['page']) ? intval($_POST['page']) : 0;
	
	if (!$page) {
		$page = 1;
	}
	
	$form['curency'] = $dbconn->GetOne('SELECT value FROM '.SETTINGS_TABLE.' WHERE name="site_unit_costunit"');
	
	$num_records = $dbconn->GetOne('SELECT COUNT(id) FROM '.LOCATION_ORDERS);
	
	$lim_min = ($page - 1) * $config_admin['news_numpage'];
	$lim_max = $config_admin['news_numpage'];
	$limit_str = ' LIMIT '.$lim_min.', '.$lim_max;
	
	$strSQL = 'SELECT id, DATE_FORMAT(date_order, "'.$config['date_format'].'") AS date_order, 
					  id_user_from, id_user_to, paid_status, comment, delivery_status
				 FROM '.LOCATION_ORDERS.'
			 ORDER BY id DESC '.$limit_str;
	$rs = $dbconn->Execute($strSQL);
	
	$i = 0;
	$orders = array();
	
	if ($rs->RowCount() > 0)
	{
		while (!$rs->EOF)
		{
			$row = $rs->GetRowAssoc(false);
			
			$orders[$i] = $row;
			$orders[$i]['number'] = $i+1;
			$orders[$i]['user_from'] = GetUserInfo($row['id_user_from']);
			$orders[$i]['user_to'] = GetUserInfo($row['id_user_to']);
			$orders[$i]['status'] = $row['paid_status'] ? '+' : '-';
			$orders[$i]['delivery_status'] = $row['delivery_status'] ? '+' : '-';
			$orders[$i]['order'] = '';
			$orders[$i]['total'] = 0;
			
			$sub_strSQL = 'SELECT id_item, currency, quantity FROM '.LOCATION_ORDERS_ITEMS.' WHERE id_order="'.$row['id'].'"';
			$rs_sub = $dbconn->Execute($sub_strSQL);
			
			$j = 1;
			
			while (!$rs_sub->EOF) {
				$row_sub = $rs_sub->GetRowAssoc(false);
				$temp = $catalog->items_item($row_sub['id_item']);
				$orders[$i]['order'] .= $j.'. '.$temp['name'].' (<b>'.$row_sub['quantity'].'</b> x <b>'.sprintf('%01.2f', $temp['price']).'</b> '.$form['curency'].')<br/>';
				$orders[$i]['total'] += $row_sub['currency'] * $row_sub['quantity'];
				$rs_sub->MoveNext();
				$j++;
			}
			
			$orders[$i]['order'] .= '<br><i>'.$orders[$i]['comment'].'</i>';
			$orders[$i]['total'] = sprintf('%01.2f', $orders[$i]['total']);
			$rs->MoveNext();
			$i++;
		}
	}
	
	$param = $file_name.'?';
	$smarty->assign('links', GetLinkStr($num_records, $page, $param, $config_admin['location_numpage']));
	
	$form['page'] = $page;
	$form['file_name'] = $file_name;
	
	$smarty->assign('items', $orders);
	$smarty->assign('header', $lang['location']);
	$smarty->assign('form', $form);
	$smarty->assign('button', $lang['button']);
	
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_location_orders_table.tpl');
	exit;
}


function OrdersChange()
{
	global $dbconn;
	
	$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);
	
	if ($id)
	{
		$dbconn->Execute('UPDATE '.LOCATION_ORDERS.' SET paid_status="1" WHERE id='.$id);
	}
	
	OrdersList();
	
	return;
}


function OrdersDelivery()
{
	global $dbconn, $lang;
	
	$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);
	
	if ($id)
	{
		$dbconn->Execute('UPDATE '.LOCATION_ORDERS.' SET delivery_status="1" WHERE id='.$id);
		
		$strSQL = 'SELECT go.id_user_from, go.id_user_to, u.login AS login_from, u2.login AS login_to
					FROM '.LOCATION_ORDERS.' go
					LEFT JOIN '.USERS_TABLE.' u ON u.id=go.id_user_from
					LEFT JOIN '.USERS_TABLE.' u2 ON u2.id=go.id_user_to
					WHERE go.id='.$id;
		
		$rs = $dbconn->Execute($strSQL);
		
		$data['id_user_from'] = $rs->fields[0];
		$data['id_user_to'] = $rs->fields[1];
		$data['login_from'] = $rs->fields[2];
		$data['login_to'] = $rs->fields[3];
		
		$msg['subj'] = addslashes($lang['location']['your_gift_was_delivered']);
		$msg['body'] = addslashes($lang['location']['mail']['hello'].$data['login_from'].$lang['location']['mail']['gift_delivered'].$data['login_to']);
		
		$strSQL = 'INSERT INTO '.MAILBOX_TABLE.' (id_from, id_to, subject, body, date_creation, was_read, deleted_from, deleted_to)
					VALUES ("1", "'.$data['id_user_from'].'", "'.$msg['subj'].'", "'.$msg['body'].'", now(), "0", "0", "0")';
		$dbconn->Execute($strSQL);
	}
	
	OrdersList();
	
	return;
}


function OrdersDelete()
{
	global $dbconn;
	
	$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);
	
	if ($id) {
		$dbconn->Execute('DELETE FROM '.LOCATION_ORDERS_ITEMS.' WHERE id_order='.$id);
		$dbconn->Execute('DELETE FROM '.LOCATION_ORDERS.' WHERE id='.$id);
	}
	
	OrdersList();
	
	return;
}

//----------------------
// USER INFO
//----------------------

function GetUserInfo($id_user)
{
	global $dbconn;
	
	$strSQL = 'SELECT a.id, a.login, a.sname, a.fname, a.gender, a.date_birthday FROM '.USERS_TABLE.' a WHERE a.id="'.$id_user.'"';
	$rs = $dbconn->Execute($strSQL);
	$row = $rs->GetRowAssoc(false);
	
	$row['name'] = $row['fname'].' '.$row['sname'];
	$row['age'] = AgeFromBDate($row['date_birthday']);
	
	return $row;
}

?>