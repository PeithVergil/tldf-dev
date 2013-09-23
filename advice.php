<?php
/**
* Dating Advices
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
include './include/class.help_info.php';

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

$local_config['category_table'] = ADVICES_CATEGORIES_TABLE;
$local_config['category_table_key'] = 2;
$local_config['item_table'] = ADVICES_TABLE;
$local_config['item_table_key'] = 3;

$file_name = 'advice.php';

$helpinfo = new HelpInfo($config, $dbconn, $file_name, $local_config);

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

// dispatcher
switch ($sel) {
	case 'list_item':
		AdviceTable();
	break;
	default:
		CategoryAdviceTable();
	break;
}

exit;


function CategoryAdviceTable()
{
	global $config, $smarty, $user, $helpinfo;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$smarty->assign('categories', $helpinfo->GetCategoriesList(0));
	$smarty->display(TrimSlash($config['index_theme_path']).'/advice_categories_table.tpl');
	exit;
}

function AdviceTable()
{
	global $config, $smarty, $user, $helpinfo;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$id = isset($_REQUEST["id"]) && intval($_REQUEST["id"]) > 0 ? intval($_REQUEST["id"]) : null;
	
	if ($id == null) {
		CategoryAdviceTable();
		return;
	}
	
	$smarty->assign("category", $helpinfo->GetCategoryInfo($id));
	$smarty->assign("advices", $helpinfo->GetItemsList($id, 0));
	$smarty->display(TrimSlash($config["index_theme_path"])."/advice_table.tpl");
	exit;
}

?>