<?php

/**
* Promotional Emails Management.
*
* @package DatingPro
* @subpackage Admin Mode
**/

include '../include/config.php';
include_once '../common.php';
include '../include/config_admin.php';
include '../include/functions_auth.php';
include '../include/functions_admin.php';

$auth = auth_user();
login_check($auth);

//IsFileAllowed($auth[0], GetRightModulePath(__FILE__), 'promotions');

$sel = isset($_REQUEST['sel']) ? $_REQUEST['sel']: "";
$type = isset($_REQUEST['type']) ? $_REQUEST['type']: "";

$par = ($type == 's') ? 'sistem' : 'admin';

//echo $sel;

switch($sel)
{
	case 'add_new':		CreateTemplate(); break;
	case 'save_temp':	SaveTemplate(); break;
	case 'edit_temp':	EditTemplate(); break;
	case 'del_temp':	DeleteTemplate(); break;
	default: ListPromotionEmails();
}

function ListPromotionEmails($err='')
{
	global $smarty, $dbconn, $config, $page, $lang;

	$file_name = "admin_promotions.php";

	AdminMainMenu($lang["promotions"]);
	
	$page = isset($_REQUEST['page']) ?  intval($_REQUEST['page']) : 1;

	$rs = $dbconn->Execute("SELECT id, title, header, body_text, footer_text, status FROM ".PROMOTION_TEMPLATES_TABLE." ORDER BY id DESC");
	$i = 0;
	$sistem = array();
	
	if ($rs->RowCount() > 0) {
		while(!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$sistem[$i]['number'] = $i + 1;
			$sistem[$i]['id'] = $row['id'];
			$sistem[$i]['title'] = stripslashes($row['title']);
			$sistem[$i]['sendlink'] = $file_name.'?sel=syssend&page='.$page.'&id_pro='.$row['id'];
			$sistem[$i]['status'] = $row['status'] ? '+' : '';
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign('sistem', $sistem);
	}
	
	$form['err'] = $err;
	$form['action'] = $file_name;
#	$form['confirm'] = $lang['confirm']['promotions'];

	$smarty->assign('create_link', $file_name.'?sel=add_new');
	$smarty->assign('form', $form);
	$smarty->assign('button', $lang['button']);
	$smarty->assign('header', $lang['promotions']);
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_promotions_table.tpl');
	exit;
}


function CreateTemplate($err='')
{
	global $smarty, $config, $lang, $file_name;
	global $spaw_root, $spaw_dir, $spaw_base_url, $spaw_dropdown_data;

	$file_name = 'admin_promotions.php';
	
	$form['add_hiddens'] = '<input type="hidden" name="sel" value="save_temp">';
	
	AdminMainMenu($lang['promotions']);
	
	$form['title']		= isset($_POST['title']) ? $_POST['title'] : '';
	$form['head']		= isset($_POST['head']) ? $_POST['head'] : '';
	$form['body_text']	= isset($_POST['body_text']) ? $_POST['body_text'] : '';
	$form['footer_text']= isset($_POST['footer_text']) ? $_POST['footer_text'] : '';
	
	$form['action'] = $file_name;
	$form['err'] = $err;
	$form['back'] = $file_name;

	//$rs=$dbconn->Execute("select name from ".$table." where id='".$id_subscribe."'");
	//$form["subscribe_name"] = $rs->fields[0];

	$smarty->assign('form', $form);
	$smarty->assign('header', $lang['promotions']);
	$smarty->assign('button', $lang['button']);
	
	if (RICH_TEXT_EDITOR == 'SPAW-1')
	{
		$spaw_root = $config['site_path'].'/spaw/';
		// include the control file
		include $spaw_root.'spaw_control.class.php';
		// pass $demo_array to the constructor
		$sw = new SPAW_Wysiwyg(
			'body_text',							/*name*/
			html_entity_decode($form['body_text']),	/*value*/
			'en',									/*language*/
			'half',									/*toolbar mode*/
			'default',								/*theme*/
			'445px',								/*width*/
			'350px',								/*height*/
			'',										/*stylesheet file*/
			$spaw_dropdown_data						/*dropdown data*/
		);
		$smarty->assign('editor', $sw->show());
	}
	elseif (RICH_TEXT_EDITOR == 'SPAW-2')
	{
		$spaw_root = $config['site_path'].'/spaw2/';
		include $spaw_root.'spaw_control.class.php';
		$sw = new SPAW_Wysiwyg(
			'body_text',							/*name*/
			html_entity_decode($form['body_text']),	/*value*/
			'en',									/*language*/
			'half',									/*toolbar mode*/
			'default',								/*theme*/
			'445px',								/*width*/
			'350px',								/*height*/
			'',										/*stylesheet file*/
			$spaw_dropdown_data						/*dropdown data (is NULL)*/
		);
		$smarty->assign('editor', $sw->getHTML());
	}
	
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_promotions_template.tpl');
	exit;
}

function EditTemplate($err="")
{
	global $smarty, $dbconn, $config, $lang, $file_name;
	global $spaw_root, $spaw_dir, $spaw_base_url, $spaw_dropdown_data;

	$file_name = 'admin_promotions.php';
	
	$tmpid	= isset($_REQUEST['tmpid']) ? $_REQUEST['tmpid'] : '';
	$tid	= isset($_POST['tid']) ? $_POST['tid'] : '';
	
	$form['add_hiddens'] = '<input type="hidden" name="sel" value="save_temp">';
	$strTid = $tmpid > 0 ? $tmpid : $tid;
	$form['add_hiddens'] .= '<input type="hidden" name="tid" value="'.$strTid.'">';
	
	AdminMainMenu($lang['promotions']);
	
	$form['action'] = $file_name;
	$form['err'] = $err;
	#$form['confirm'] = $lang['confirm']['promotions_subscribers'];
	$form['back'] = $file_name;
	$form['mode'] = 'edit';
	
	if ($tmpid > 0 || $tid > 0)
	{
		if ($tmpid > 0)
		{
			$rs = $dbconn->Execute('SELECT title, header, body_text, footer_text FROM '.PROMOTION_TEMPLATES_TABLE.' WHERE id = ?', array($tmpid));
			
			$form['title']		= stripslashes($rs->fields[0]);
			$form['head']		= stripslashes($rs->fields[1]);
			$form['body_text']	= stripslashes($rs->fields[2]);
			$form['footer_text']= stripslashes($rs->fields[3]);
		}
		elseif($tid > 0)
		{
			$form['title']		= isset($_POST['title']) ? $_POST['title'] : '';
			$form['head']		= isset($_POST['head']) ? $_POST['head'] : '';
			$form['body_text']	= isset($_POST['body_text']) ? $_POST['body_text'] : '';
			$form['footer_text']= isset($_POST['footer_text']) ? $_POST['footer_text'] : '';
		}
		
		$smarty->assign('form', $form);
		$smarty->assign('header', $lang['promotions']);
		$smarty->assign('button', $lang['button']);
		
		if (RICH_TEXT_EDITOR == 'SPAW-1')
		{
			$spaw_root = $config['site_path'].'/spaw/';
			// include the control file
			include $spaw_root.'spaw_control.class.php';
			// pass $demo_array to the constructor
			$sw = new SPAW_Wysiwyg(
				'body_text',							/*name*/
				html_entity_decode($form['body_text']),	/*value*/
				'en',									/*language*/
				'half',									/*toolbar mode*/
				'default',								/*theme*/
				'445px',								/*width*/
				'350px',								/*height*/
				'',										/*stylesheet file*/
				$spaw_dropdown_data						/*dropdown data*/
			);
			$smarty->assign('editor',$sw->show());
		}
		elseif (RICH_TEXT_EDITOR == 'SPAW-2')
		{
			$spaw_root = $config['site_path'].'/spaw2/';
			include $spaw_root.'spaw_control.class.php';
			$sw = new SPAW_Wysiwyg(
				'body_text',							/*name*/
				html_entity_decode($form['body_text']),	/*value*/
				'en',									/*language*/
				'half',									/*toolbar mode*/
				'default',								/*theme*/
				'445px',								/*width*/
				'350px',								/*height*/
				'',										/*stylesheet file*/
				$spaw_dropdown_data						/*dropdown data (is NULL)*/
			);
			$smarty->assign('editor', $sw->getHTML());
		}
		
		$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_promotions_template.tpl');
		exit;
	}
	else
	{
		$err = 'Invalid Template Id';
		ListPromotionEmails($err);
	}
}


function SaveTemplate()
{
	global $dbconn, $lang;
	
	$file_name = 'admin_promotions.php';

	$title		= isset($_POST['title']) ? $_POST['title'] : '';
	$head		= isset($_POST['head']) ? $_POST['head'] : '';
	$body_text	= isset($_POST['body_text']) ? $_POST['body_text'] : '';
	$footer_text= isset($_POST['footer_text']) ? $_POST['footer_text'] : '';
	
	$err = '';
	
	if (!strlen($title) || !strlen($body_text)) {
		$err = $lang['err']['invalid_fields'];
		if (!strlen($title)) {
			$err .= '<br>'.$lang['promotions']['title'];
		}
		if (!strlen($body_text)) {
			$err .= '<br>'.$lang['promotions']['body_text'];
		}
	}
	
	$tid = isset($_POST['tid']) ? $_POST['tid'] : '';
	
	if ($err == '')
	{
		if ($tid > 0) {
			$str =
				'UPDATE '.PROMOTION_TEMPLATES_TABLE.'
					SET title = "'.addslashes($title).'", body_text = "'.addslashes($body_text).'", footer_text = "'.addslashes($footer_text).'"
				  WHERE id = '.$tid;
		} else {
			$str =
				'INSERT INTO '.PROMOTION_TEMPLATES_TABLE.' (title, header , body_text , footer_text)
					VALUES ("'.addslashes($title).'", "'.$head.'", "'.addslashes($body_text).'", "'.addslashes($footer_text).'")';
		}
		
		$dbconn->Execute($str);
		
		header('Location: '.$file_name);
		#echo "<script>location.href='".$file_name."'</script>";
		exit;
	}
	else
	{
		if ($tid > 0) {
			EditTemplate($err);
		} else {
			CreateTemplate($err);
		}
	}
}


function DeleteTemplate()
{
	global $dbconn;
	
	$id = intval($_GET['id']);
	
	$err = '';
	
	if (!$id) {
		$err = 'Error in template deletion';
	} else {
		$dbconn->Execute('DELETE FROM '.PROMOTION_TEMPLATES_TABLE.' WHERE id = ?', array($id));
	}
	
	ListPromotionEmails($err);
	return;
}
?>