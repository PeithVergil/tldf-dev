<?php
/**
* User Shoutbox management
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
include './include/class.percent.php';

// authentication
$user = auth_index_user();

if (empty($user) || $user == 'err' || empty($user[ AUTH_ID_USER ])) {
	header('location: '.$config['site_root'].'/index.php');
	exit;
}

// check guest
// (handled by permissions)

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
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

// dispatcher
switch ($sel) {
	case 'edit':
		ViewShout();
	break;
	
	case 'save':
		UpdateShout();
	break;
	
	case 'del':
		DeleteFromList();
	break;
	
	default:
		ShoutListTable();
	break;
}

exit;


function ShoutListTable($err="")
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$id_user = $user[ AUTH_ID_USER ];
	$file_name = "shoutbox.php";
	$page = isset($_REQUEST["page"])?$_REQUEST["page"]:1;

	if(!isset($_GET["par"])) unset($_SESSION["search_page"]);
	if((strval($page) == "") || (strval($page) == "0"))
	{
		if (isset($_SESSION["search_page"]))
			$page = $_SESSION["search_page"];
		else
			$page = 1;
	}
	else
	{
		$page = intval($page);
		$_SESSION["search_page"] = $page;
	}

	////////// settings
	$settings = GetSiteSettings(array('icon_male_default', 'icon_female_default', 'icons_folder','show_users_connection_str','show_users_comments','show_users_group_str','use_kiss_types', 'thumb_max_width'));
	$smarty->assign("icon_width", $settings["thumb_max_width"]);

	unset($_SESSION["id_arr"]);
	$_SESSION["id_arr"] = array();

	$strSQL = "SELECT a.id, a.text, a.date_add, b.login, b.icon_path from ".SHOUTS_TABLE." a, ".USERS_TABLE." b WHERE a.user_id=b.id AND a.user_id=".$id_user." ORDER BY a.date_add DESC";
	//echo $strSQL;
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	while(!$rs->EOF)
	{
		$row = $rs->GetRowAssoc(false);
		$_SESSION["id_arr"][$i] = $row["id"];
		$rs->MoveNext();
		$i++;
	}
	$num_records = count($_SESSION["id_arr"]);
	
	///////// page
	$lim_min = ($page-1)*$config_index["search_numpage"];
	$lim_max = $config_index["search_numpage"];
	$limit_str = " limit ".$lim_min.", ".$lim_max;
	if($num_records>0)
	{
		$strSQL = "SELECT a.id, a.text, a.date_add, a.status, b.login, b.icon_path from ".SHOUTS_TABLE." a, ".USERS_TABLE." b WHERE a.user_id=b.id AND a.user_id=".$id_user." ORDER BY a.date_add DESC ".$limit_str;
		//echo $strSQL;
		$rs = $dbconn->Execute($strSQL);
		$i = 0;
		$search = array();
		while(!$rs->EOF)
		{
			$row = $rs->GetRowAssoc(false);

			$search[$i]["id"] = $row["id"];
			$search[$i]["number"] = ($page-1)*$config_index["search_numpage"]+($i+1);
			
			$search[$i]["text"] = stripslashes($row["text"]);
			$search[$i]["date_add"] = $row["date_add"];
			$search[$i]["status"] = $row["status"];
			
			//links
			$search[$i]["edit_link"] = "shoutbox.php?sel=edit&id=".$row["id"];
			$search[$i]["delete_id"] = $row["id"];
			
			$rs->MoveNext();
			$i++;
		}
		
		$param = $file_name."?";
		$form["pages_count"] = ceil($num_records/$config_index["search_numpage"]);
		$smarty->assign("links", GetLinkArray($num_records,$page,$param,$config_index["search_numpage"]));
		$smarty->assign("search_res", $search);
	}
	else
	{
		$smarty->assign("empty", "1");
	}
	
	$form["err"] = $err;

	$smarty->assign("form", $form);

	$smarty->assign("section", $lang["subsection"]);
	$smarty->assign("header_s", $lang["shoutbox"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/shoutbox_table.tpl");
	exit;
}

function ViewShout($err="",$data="")
{
	global $lang, $config, $smarty, $dbconn, $user;

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$view_id = intval($_GET["id"]);
	
	if (!$data)
	{
		if ($view_id > 0)
		{
			$strSQL = "SELECT a.id, a.text, a.date_add FROM ".SHOUTS_TABLE." a WHERE a.id=$view_id AND user_id=".$user[ AUTH_ID_USER ];
			//echo $strSQL;
			$rs = $dbconn->Execute($strSQL);
			$i = 0;
			while(!$rs->EOF) {
				$row = $rs->GetRowAssoc(false);
				
				$form["id"] = $row["id"];
				$form["text"] = stripslashes($row["text"]);
				$form["date_add"] = $row["date_add"];
				
				$rs->MoveNext();
			}
			
			if (!$form["id"]) {
				echo '<script>location.href="./shoutbox.php";</script>';
				exit;
			}
		}
		else
		{
			echo '<script>location.href="./shoutbox.php";</script>';
			exit;
		}
	}
	else
	{
		$form["id"] = $data["id"];
		$form["text"] = $data["text"];
	}
	
	$form['err'] = $err;

	$smarty->assign("form", $form);
	$smarty->assign("section", $lang["subsection"]);
	$smarty->assign("header_s", $lang["shoutbox"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/shout_edit_form.tpl");
	exit;
}

function UpdateShout()
{
	global $lang, $dbconn;

	$data = $_POST;
	
	$debug = false;
	
	//$text = addslashes(FormFilter($data["text"]));
	$text = addslashes($data["text"]);
	$id = $data["id"];
	
	$err="";
	if (!strlen($text))
	{
		$err .= $lang['err']['fill_shout'];
	}
	
	if(!$err)
	{
		$post_date = date('Y-m-d H:i:s');
		$strSQL = "UPDATE ".SHOUTS_TABLE." SET text='".$text."', date_add='".$post_date."', status='0' WHERE id='".$id."'";
		if ($debug) echo $strSQL;
		
		$dbconn->Execute($strSQL);
		
		echo '<script>location.href="./shoutbox.php";</script>';
		exit;
	}
	else
	{
		$data["id"] = $id;
		$data["text"] = $text;
		
		ViewShout($err,$data);
	}
}

function DeleteFromList()
{
	global $dbconn, $user;
	
	$user_id = $user[ AUTH_ID_USER ];
	
	$del_id = intval($_GET["id"]);
	
	if ($del_id > 0)
	{
		$strSQL = "DELETE FROM ".SHOUTS_TABLE." WHERE id=".$del_id;
		$rs = $dbconn->Execute($strSQL);
		
		$strSQL = "SELECT id FROM ".SHOUTS_USER_STAT_TABLE." WHERE id_user='".$user_id."'";
		$id_stat = $dbconn->GetOne($strSQL);
		if ($id_stat) {
			$strSQL = "UPDATE ".SHOUTS_USER_STAT_TABLE." SET count_mess=count_mess-1 WHERE id='".$id_stat."'";
			$rs = $dbconn->Execute($strSQL);
		}
	}
	
	if (!$err) {
		echo '<script>location.href="./shoutbox.php";</script>';
		exit;
	}
	
	ShoutListTable($err);
}
?>