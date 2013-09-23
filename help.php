<?php
/**
* User FAQ
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

$local_config['category_table'] = HELP_CATEGORIES_TABLE;
$local_config['category_table_key'] = 4;
$local_config['item_table'] = HELP_QUESTIONS_TABLE;
$local_config['item_table_key'] = 5;
$local_config['use_status'] = '1';

$file_name = 'help.php';

$helpinfo = new HelpInfo($config, $dbconn, $file_name, $local_config);

// user selection
$sel = isset($_REQUEST['sel']) ? $_REQUEST['sel'] : '';

// dispatcher
switch ($sel) {
	case 'list_item':	FaqsTable(); break;
	default: 	TopicTable();
}

exit;


function TopicTable()
{
	global $config, $smarty, $user, $helpinfo;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$smarty->assign('topics', $helpinfo->GetCategoriesList(0, 1));
	$smarty->display(TrimSlash($config["index_theme_path"])."/faq_topics_table.tpl");
	exit;
}

function FaqsTable()
{
	global $config, $smarty, $user, $helpinfo;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$id = isset($_REQUEST["id"]) && intval($_REQUEST["id"]) > 0 ? intval($_REQUEST["id"]) : null;
	if ($id == null) {
		TopicTable();
		return;
	}
	$smarty->assign("category", $helpinfo->GetCategoryInfo($id));
	$smarty->assign("faqs", $helpinfo->GetItemsList($id, 0));
	$smarty->display(TrimSlash($config["index_theme_path"])."/faq_table.tpl");
	exit;
}

?>
