<?php
/**
* Addition area content management (creating, deleting, editing)
*
* @package DatingPro
* @subpackage Admin Mode
**/
include '../include/config.php';
include_once '../common.php';
include '../include/config_admin.php';
include '../include/functions_admin.php';
include '../include/functions_auth.php';

$auth = auth_user();
login_check($auth);
IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "addition");

$sel = isset($_REQUEST["sel"]) ? $_REQUEST["sel"] : "";

switch ($sel) {
	case "add": EditPage("add");
	case "edit": EditPage("edit");
	case "save": SavePage();
	case "up": UpPage(intval($_GET["id"]));
	case "down": DownPage(intval($_GET["id"]));
	case "del": DeletePage();
	default: ListPages();
}

exit;


function ListPages($err="")
{
	global $smarty, $dbconn, $config, $lang;

	if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
	$file_name = "admin_addition_info.php";

	AdminMainMenu($lang["addition"]);

	$strSQL = "	SELECT a.id, a.name, a.content, a.status, b.name AS name_lang, b.content AS content_lang
				FROM ".INFO_CONTENT_TABLE." a
				LEFT JOIN ".INFO_LANG_CONTENT_TABLE." b ON a.id=b.id_info AND b.id_lang=".$config['default_lang']." AND b.table_key=1
				WHERE a.id != '1' ORDER BY sorter";
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$pages = array();
	if($rs->RowCount()>0) {
		while(!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$pages[$i]["number"] = $i+1;
			$pages[$i]["id"] = $row["id"];
			$pages[$i]["name"] = $row["name_lang"] ? stripslashes($row["name_lang"]) : stripslashes($row["name"]);
			$pages[$i]["content"] = $row["content_lang"] ? strip_tags(stripslashes($row["content_lang"])) : strip_tags(stripslashes($row["content"]));
			if (strlen(utf8_decode($pages[$i]["content"])) > 400) {
				$pages[$i]["content"] = utf8_substr($pages[$i]["content"], 0, 400)."...";
			}
			$pages[$i]["status"] = $row["status"]?"+":"";
			$pages[$i]["deletelink"] = $file_name."?sel=del&id=".$row["id"];
			$pages[$i]["editlink"] = $file_name."?sel=edit&id=".$row["id"];
			$pages[$i]["uplink"] = $file_name."?sel=up&id=".$row["id"];
			$pages[$i]["downlink"] = $file_name."?sel=down&id=".$row["id"];
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign("pages", $pages);
	}

	$form["err"] = $err;
	$form["confirm"] = $lang["confirm"]["section"];

	$smarty->assign("add_link", $file_name."?sel=add");
	$smarty->assign("form", $form);
	$smarty->assign("button", $lang["button"]);
	$smarty->assign("header", $lang["addition"]);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_addition_info_table.tpl");
	exit;
}

function EditPage($par, $err="")
				{
	global $smarty, $dbconn, $config, $lang, $spaw_root, $spaw_dir, $spaw_base_url;

	if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
	else
	$file_name = "admin_addition_info.php";

	AdminMainMenu($lang["addition"]);

	$form["hiddens"] = "<input type=hidden name=sel value=save>";
	$form["hiddens"] .= "<input type=hidden name=par value=".$par.">";

	if ($err != "") {
		$form["err"] = $err;
		$data["id"] = $_POST["id"];
		$data["name"] = $_POST["name"];
		$data["status"] = $_POST["status"];
		$data["content"] = $_POST["code"];
		$data["description"] = $_POST["description"];
		$data["keywords"] = $_POST["keywords"];
		$form["hiddens"] .= "<input type=hidden name=id value=".$data["id"].">";
	} else {
		if ($par == "edit") {
			$data["id"] = isset($_GET["id"]) ? intval($_GET["id"]) : 1;
			$strSQL="	SELECT a.status, a.description, a.keywords, a.name, a.content, b.name AS name_lang, b.content AS content_lang, a.title
						FROM ".INFO_CONTENT_TABLE." a
						LEFT JOIN ".INFO_LANG_CONTENT_TABLE." b ON a.id=b.id_info AND b.id_lang=".$config['default_lang']." AND b.table_key=1
						WHERE a.id=".$data["id"];
			$rs = $dbconn->Execute($strSQL);
			$row = $rs->GetRowAssoc(false);
			$data["name"] = $row["name_lang"] ? stripslashes($row["name_lang"]) : stripslashes($row["name"]);
			$data["content"] = $row["content_lang"] ? stripslashes($row["content_lang"]) : stripslashes($row["content"]);
			$data["status"] = $row["status"];
			$data["title"] = stripslashes($row["title"]);
			$data["description"] = stripslashes($row["description"]);
			$data["keywords"] = stripslashes($row["keywords"]);
			$form["hiddens"] .= "<input type=hidden name=id value=".$data["id"].">";
		} else {
			$data["id"] = 0;
			$data["name"] = "";
			$data["status"] = "";
			$data["content"] = "";
			$data["description"] = $lang["description"];
			$data["keywords"] = $lang["keywords"];
			$data["title"] = $lang["title"];
		}
	}

	if ($data["id"] == 1) {
		$form["hiddens"] .= "<input type=hidden name=name value=\"".$data["name"]."\">";
		$form["hiddens"] .= "<input type=hidden name=status value=0>";
	}

	$form["back"] = $file_name;
	$form["action"] = $file_name;
	$form["par"] = $par;

	$strSQL = "SELECT id, name FROM ".LANGUAGE_TABLE." ORDER BY id ";
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$lang_link_arr = array();
	while(!$rs->EOF){
		$row = $rs->GetRowAssoc(false);
		$lang_link_arr[$i]["id"] = $row["id"];
		$lang_link_arr[$i]["name"] = $row["name"];
		$lang_link_arr[$i]["link"] = "./admin_translate_info.php?table_key=1&id_lang=".$row["id"]."&id_info=".$data["id"];
		$rs->MoveNext();
		$i++;
	}
	$smarty->assign("lang_link", $lang_link_arr);

	if (RICH_TEXT_EDITOR == 'SPAW-1')
	{
		// set $spaw_root variable to the physical path were control resides
		// don't forget to modify other settings in config/spaw_control.config.php
		// namely $spaw_dir and $spaw_base_url most likely require your modification
		$spaw_root = $config['site_path'].'/spaw/';
		// include the control file
		include $spaw_root.'spaw_control.class.php';
		// pass $demo_array to the constructor
		$sw = new SPAW_Wysiwyg(
			'code',									/*name*/
			html_entity_decode($data['content']),	/*value*/
			'en',									/*language*/
			'full',									/*toolbar mode*/
			'default',								/*theme*/
			'100%',									/*width*/
			'100%',									/*height*/
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
			'code',									/*name*/
			html_entity_decode($data['content']),	/*value*/
			'en',									/*language*/
			'full',									/*toolbar mode*/
			'default',								/*theme*/
			'700px',								/*width*/
			'500px',								/*height*/
			'',										/*stylesheet file*/
			$spaw_dropdown_data						/*dropdown data (is NULL)*/
		);
		$smarty->assign('editor', $sw->getHTML());
	}

	$smarty->assign("data", $data);
	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["addition"]);
	$smarty->assign("err", $lang["err"]);
	$smarty->assign("button", $lang["button"]);

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_addition_info_form.tpl");
	exit;
}

function SavePage()
{
	global $dbconn, $config, $lang;

	$id_info = isset($_POST['id']) ? intval($_POST['id']) : 0;
	$name = isset($_POST["name"]) ? $_POST["name"] : "";
	$status = isset($_POST["status"]) ? intval($_POST["status"]) : 0;
	$content = isset($_POST["code"]) ? $_POST["code"] : "";
	$description = isset($_POST["description"]) ? $_POST["description"] : "";
	$keywords = isset($_POST["keywords"]) ? $_POST["keywords"] : "";
	$title = isset($_POST["title"]) ? $_POST["title"] : "";

	if($id_info!=1 && !strlen($name)){
		$err = $lang["err"]["invalid_fields"];
		$err .= "<br>".$lang["addition"]["name"];
		EditPage($_POST["par"], $err);
		return;
	}

	if (isset($_POST["par"]) && $_POST["par"] == "edit") {
		$dbconn->Execute("update ".INFO_CONTENT_TABLE." set name = '".addslashes($name)."', content = '".addslashes($content)."', title = '".addslashes($title)."', description = '".addslashes($description)."', keywords = '".addslashes($keywords)."', status = '".$status."' where id='".intval($id_info)."'");

		//Check if this page already exist
		$strSQL="SELECT * FROM ".INFO_LANG_CONTENT_TABLE." WHERE id_info=".$id_info." AND id_lang=".$config['default_lang']." AND table_key=1";
		$rs=$dbconn->Execute($strSQL);
		if($rs->RowCount()>0) $strSQL="UPDATE ".INFO_LANG_CONTENT_TABLE." SET name='".addslashes($name)."', content='".addslashes($content)."' WHERE id_info=".$id_info." AND id_lang=".$config['default_lang']." AND table_key=1";
		else $strSQL="INSERT INTO ".INFO_LANG_CONTENT_TABLE."(table_key, id_info, name, content, id_lang) VALUES(1, ".$id_info.", '".addslashes($name)."', '".addslashes($content)."', ".$config['default_lang'].")";
		$dbconn->Execute($strSQL);
	} else {
		$rs=$dbconn->Execute("select max(sorter) from ".INFO_CONTENT_TABLE);
		$sorter = intval($rs->fields[0])+1;
		$dbconn->Execute("insert into ".INFO_CONTENT_TABLE." (name, content, description, keywords, status, sorter, title) values ('".addslashes($name)."','".addslashes($content)."','".addslashes($description)."','".addslashes($keywords)."','".$status."','".$sorter."','".addslashes($title)."')");
		$id_info=$dbconn->Insert_ID();
		$strSQL="INSERT INTO ".INFO_LANG_CONTENT_TABLE."(table_key, id_info, name, content, id_lang) VALUES(1, ".$id_info.", '".addslashes($name)."', '".addslashes($content)."', ".$config['default_lang'].")";
		$dbconn->Execute($strSQL);
	}
	$_GET["id"] = $id_info;
	EditPage("edit");
	return;
}

function DeletePage()
{
	global $dbconn;
	
	$id = isset($_GET["id"]) ? intval($_GET["id"]) : 1;
	
	if ($id == 1) {
		ListPages();
		return;
	}
	
	$dbconn->Execute("delete from ".INFO_CONTENT_TABLE." where id = '".$id."'");
	$dbconn->Execute("delete from ".INFO_LANG_CONTENT_TABLE." where table_key=1 AND id_info = '".$id."'");

	ListPages();
	return;
}

function UpPage($id)
{
	global $dbconn;
	
	if(!$id) { ListPages(); return; }
	$rs = $dbconn->Execute("Select id, sorter from ".INFO_CONTENT_TABLE." where id='".$id."'");
	if ($rs->RowCount()>0){
		$id_page = $rs->fields[0];
		$sorter_page = $rs->fields[1];
	}
	$rs = $dbconn->Execute("Select id, sorter from ".INFO_CONTENT_TABLE." where sorter<'".$sorter_page."' and sorter>0 order by sorter desc");
	if ($rs->RowCount()>0){
		$id_old = $rs->fields[0];
		$sorter_old = $rs->fields[1];
	}
	if(intval($id_old)){
		$dbconn->Execute("Update ".INFO_CONTENT_TABLE." set sorter='".$sorter_page."' where id='".$id_old."'");
		$dbconn->Execute("Update ".INFO_CONTENT_TABLE." set sorter='".$sorter_old."' where id='".$id_page."'");
	}
	ListPages();
}
function DownPage($id)
{
	global $dbconn;
	if(!$id) { ListPages(); return; }
	$rs = $dbconn->Execute("Select id, sorter from ".INFO_CONTENT_TABLE." where id='".$id."'");
	if ($rs->RowCount()>0){
		$id_page = $rs->fields[0];
		$sorter_page = $rs->fields[1];
	}
	$rs = $dbconn->Execute("Select id, sorter from ".INFO_CONTENT_TABLE." where sorter>'".$sorter_page."' order by sorter");
	if ($rs->RowCount()>0){
		$id_old = $rs->fields[0];
		$sorter_old = $rs->fields[1];
	}
	if(intval($id_old)){
		$dbconn->Execute("Update ".INFO_CONTENT_TABLE." set sorter='".$sorter_page."' where id='".$id_old."'");
		$dbconn->Execute("Update ".INFO_CONTENT_TABLE." set sorter='".$sorter_old."' where id='".$id_page."'");
	}
	ListPages();
}

?>