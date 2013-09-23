<?php
/**
* Displays Addition information (Testimonials, General conditions, Press...)
*
* @package DatingPro
* @subpackage User Mode
**/

include './include/config.php';
include './common.php';
include './include/config_index.php';
include './include/functions_auth.php';
include './include/functions_index.php';
include './include/class.lang.php';

// authentication
$user = auth_index_user();

if (empty($user) || $user == 'err' || empty($user[ AUTH_ID_USER ])) {
	header('location: '.$config['site_root'].'/index.php');
	exit;
}

// check guest
// (public access)

// check group, period, expiration
RefreshAccount();

// check status
// (public access)

// check permissions
// (public access)

// alerts and statistics
if (!$user[ AUTH_GUEST ]) {
	GetAlertsMessage();
	SetModuleStatistic(GetRightModulePath(__FILE__));
}

// active menu item
$smarty->assign('sub_menu_num', '');

// user selection
$sel = isset($_POST['sel']) ? intval($_POST['sel']) : intval($_GET['sel']);

Banners(GetRightModulePath(__FILE__));
IndexHomePage();
GetActiveUserInfo($user);

$rs = $dbconn->Execute(
	'SELECT b.name as name_lang, a.name, b.content as content_lang, a.content, a.description, a.keywords, a.title
	   FROM '.INFO_CONTENT_TABLE.' a
  LEFT JOIN '.INFO_LANG_CONTENT_TABLE.' b ON a.id = b.id_info AND b.id_lang = '.$config['default_lang'].' AND b.table_key = 1
      WHERE a.id = ?',
	  array($sel));

$row = $rs->GetRowAssoc(false);

$data['name'] = $row['name_lang'] ? stripslashes($row['name_lang']) : stripslashes($row['name']);
$data['content'] = $row['content_lang'] ? stripslashes($row['content_lang']) : stripslashes($row['content']);

$smarty->assign('data', $data);

$lang['description'] = stripslashes($row['description']);
$lang['keywords'] = stripslashes($row['keywords']);
$lang['main_title'] = stripslashes($row['title']);

$smarty->assign('lang', $lang);
$smarty->assign('section', $lang['subsection']);
$smarty->assign('header', $lang['homepage']);
$smarty->display(TrimSlash($config['index_theme_path']).'/info_table.tpl');

exit;

?>