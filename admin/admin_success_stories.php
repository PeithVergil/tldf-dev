<?php

/**
* Success stories section management (editing, creating, deleting site success stories).
*
* @package DatingPro
* @subpackage Admin Mode
**/

include "../include/config.php";
include_once "../common.php";
include "../include/config_admin.php";
include "../include/functions_auth.php";
include "../include/functions_admin.php";
include "../include/class.images.php";

$auth = auth_user();
login_check($auth);
IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "success");

if(isset($_SERVER["PHP_SELF"]))
$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
else
$file_name = "admin_success_stories.php";

$sel = isset($_REQUEST["sel"]) ? $_REQUEST["sel"] : "";

switch($sel){
	case "edit": 		EditStory("edit"); break;
	case "add": 		EditStory("add"); break;
	case "add_story": 	AddStory(); break;
	case "save": 		SaveStory(); break;
	case "delete": 		DeleteStory(); break;
	default:			ListStories();
}

function ListStories(){
	global $smarty, $dbconn, $config, $config_admin, $lang, $file_name;

	AdminMainMenu($lang["success"]);

	$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;

	$photos_folder = GetSiteSettings('success_folder');

	$strSQL = "SELECT COUNT(id) FROM ".SUCCESS_STORIES_TABLE;
	$rs = $dbconn->Execute($strSQL);
	$num_records = $rs->fields[0];

	$lim_min = ($page-1)*$config_admin["stories_numpage"];
	$lim_max = $config_admin["stories_numpage"];
	$limit_str = " limit ".$lim_min.", ".$lim_max;

	$strSQL = "	SELECT id, couple_name, story_title, image_path_1, description, DATE_FORMAT(story_date,'".$config["date_format"]."') as story_date
				FROM ".SUCCESS_STORIES_TABLE."
				ORDER BY story_date ".$limit_str;
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	if($rs->RowCount()>0){
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$story[$i]["number"] = ($page-1)*$config_admin["stories_numpage"]+($i+1);
			$story[$i]["id"] = $row["id"];
			if (strlen(utf8_decode($row["couple_name"]))<50) {
				$story[$i]["couple_name"] = stripslashes($row["couple_name"]);
			} else {
				$story[$i]["couple_name"] = utf8_substr(stripslashes($row["couple_name"]),0,50)."...";
			}
			if (strlen(utf8_decode($row["story_title"]))<50) {
				$story[$i]["story_title"] = stripslashes($row["story_title"]);
			} else {
				$story[$i]["story_title"] = utf8_substr(stripslashes($row["story_title"]),0,50)."...";
			}
			if (($row["image_path_1"]!='0') && (file_exists($config["site_path"].$photos_folder."/".$row["image_path_1"]))) {
				$story[$i]["image_path"] ="<img src=".$config["site_root"].$photos_folder."/"."thumb_".$row["image_path_1"].">";
			} else {
				$story[$i]["image_path"] = $lang["success"]["no_image"];
			}
			if (strlen(utf8_decode($row["description"])<300)) {
				$story[$i]["description"] = stripslashes($row["description"]);
			} else {
				$story[$i]["description"] = utf8_substr(stripslashes($row["description"]),0,300)."...";
			}
			$story[$i]["story_date"] = $row["story_date"];
			$story[$i]["edit_link"] = $file_name."?sel=edit&id_story=".$row["id"];
			$story[$i]["delete_link"] = $file_name."?sel=delete&id_story=".$row["id"];
			$rs->MoveNext();
			$i++;
		}
		$param = $file_name."?";
		$smarty->assign("links", GetLinkStr($num_records,$page,$param,$config_admin["stories_numpage"]));
		$smarty->assign("story", $story);
		$smarty->assign("empty_row", "0");
	}else{
		$smarty->assign("empty_row", "1");
	}

	$smarty->assign("button", $lang["button"]);
	$smarty->assign("add_link", $file_name."?sel=add");

	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_success_list.tpl");
	exit;
}

function EditStory($par='', $err='', $id_story=null) {
	global $smarty, $dbconn, $config, $config_admin, $lang, $file_name;

	AdminMainMenu($lang["success"]);

	if ($err) $form["err"] = $err;

	$photos_folder = GetSiteSettings('success_folder');
	$id_story = isset($_REQUEST["id_story"]) ? intval($_REQUEST["id_story"]) : $id_story;

	$form["hiddens"] = "<input type=hidden name=sel value=>";
	if ($par=='edit') {
		$strSQL = "SELECT id, couple_name, story_title, image_path_1, image_path_2, image_path_3, description, DATE_FORMAT(story_date,'%Y')  as year, DATE_FORMAT(story_date,'%m')  as month, DATE_FORMAT(story_date,'%d')  as day
							FROM ".SUCCESS_STORIES_TABLE."
							WHERE id='".$id_story."' ";
		$rs = $dbconn->Execute($strSQL);
		if($rs->RowCount()>0){
			$row = $rs->GetRowAssoc(false);
			$story["id"] = $row["id"];
			$form["hiddens"] .= "<input type=hidden name=id_story value='".$story["id"]."'>";
			$story["couple_name"] = stripslashes($row["couple_name"]);
			$story["story_title"] = stripslashes($row["story_title"]);
			$story["description"] = stripslashes($row["description"]);
			if (($row["image_path_1"]!='0') && (file_exists($config["site_path"].$photos_folder."/".$row["image_path_1"]))) {
				$story["image_path_1"] ="<img src=".$config["site_root"].$photos_folder."/"."thumb_".$row["image_path_1"].">";
				$story["image_1_delete_link"] = $file_name."?sel=del_image_1&id_story=".$story["id"];
			} else {
				$story["image_path_1"] = $lang["success"]["no_image"];
			}
			if (($row["image_path_2"]!='0') && (file_exists($config["site_path"].$photos_folder."/".$row["image_path_2"]))) {
				$story["image_path_2"] ="<img src=".$config["site_root"].$photos_folder."/"."thumb_".$row["image_path_2"].">";
				$story["image_2_delete_link"] = $file_name."?sel=del_image_2&id_story=".$story["id"];
			} else {
				$story["image_path_2"] = $lang["success"]["no_image"];
			}
			if (($row["image_path_3"]!='0') && (file_exists($config["site_path"].$photos_folder."/".$row["image_path_3"]))) {
				$story["image_path_3"] ="<img src=".$config["site_root"].$photos_folder."/"."thumb_".$row["image_path_3"].">";
				$story["image_3_delete_link"] = $file_name."?sel=del_image_3&id_story=".$story["id"];
			} else {
				$story["image_path_3"] = $lang["success"]["no_image"];
			}
			$story["year"] = $row["year"];
			$story["month"] = $row["month"];
			$story["day"] = $row["day"];
		}
	} else {
		$story["image_path_1"] = $story["image_path_2"] = $story["image_path_3"] = $lang["success"]["no_image"];
	}
	$story["day"] = isset($story["day"]) && $story["day"] !=0 ? $story["day"] : date("d");
	for($i=0;$i<31;$i++){
		$s_day[$i]["value"] = $i+1;
		if(intval($story["day"]) == $i+1)
		$s_day[$i]["sel"] = 1;
		else
		$s_day[$i]["sel"] = 0;
	}
	$smarty->assign("s_day", $s_day);

	$story["month"] = isset($story["month"]) && $story["month"]!=0 ? $story["month"] : date("m");
	for($i=0;$i<12;$i++){
		$s_month[$i]["value"] = $i+1;
		$s_month[$i]["name"] = $lang["month"][$i+1];
		if(intval($story["month"]) == $i+1)
		$s_month[$i]["sel"] = 1;
		else
		$s_month[$i]["sel"] = 0;
	}
	$smarty->assign("s_month", $s_month);

	$story["year"] = isset($story["year"]) && $story["year"]!=0 ? $story["year"] : date("Y");
	for($i=0;$i<3;$i++){
		$y = intval(date("Y"))+1-$i;
		$s_year[$i]["value"] = $y;
		if(intval($story["year"]) == $y)
		$s_year[$i]["sel"] = 1;
		else
		$s_year[$i]["sel"] = 0;
	}
	$smarty->assign("s_year", $s_year);
	$form["par"] = $par;
	$form["action"] = $file_name;
	$form["back"] = $file_name;
	$smarty->assign("form", $form);

	$smarty->assign("button", $lang["button"]);
	$smarty->assign("story", $story);
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_success_form.tpl");
	exit;
}

function SaveStory(){
	global $smarty, $dbconn, $config, $config_admin, $lang, $file_name;
	$images_obj = new Images($dbconn);
	$photos_folder = GetSiteSettings('success_folder');

	$id_story = isset($_REQUEST["id_story"]) ? intval($_REQUEST["id_story"]) : null;
	if (isset($id_story)) {
		$couple_name = isset($_POST["couple_name"]) ? $_POST["couple_name"] : "";
		$story_title = isset($_POST["story_title"]) ? $_POST["story_title"] : "";
		$description = isset($_POST["description"]) ? $_POST["description"] : "";

		$s_year = $_POST["s_year"];
		$s_month = sprintf("%02d",$_POST["s_month"]);
		$s_day = sprintf("%02d",$_POST["s_day"]);

		$image_file_1 = $_FILES["image_path_1"];
		$image_file_2 = $_FILES["image_path_2"];
		$image_file_3 = $_FILES["image_path_3"];

		if (checkdate($s_month, $s_day, $s_year)){
			$s_date = $s_year."-".$s_month."-".$s_day;
		} else {
			$err = $lang["err"]["invalid_date"];
			EditStory("edit",$err,$id_story);
			exit;
		}
		/// check empty values
		if(!strlen($couple_name) || !strlen($story_title)){
			$err = $lang["err"]["invalid_fields"];
			EditStory("edit",$err,$id_story);
			exit;
		}
		//deleting files
		if (isset($_POST["delimage1"]) && $_POST["delimage1"]) {
			DeleteImage("1",$id_story,$config["site_path"].$photos_folder."/");
		}
		if (isset($_POST["delimage2"]) && $_POST["delimage2"]) {
			DeleteImage("2",$id_story,$config["site_path"].$photos_folder."/");
		}
		if (isset($_POST["delimage3"]) && $_POST["delimage3"]) {
			DeleteImage("3",$id_story,$config["site_path"].$photos_folder."/");
		}
		//upload photo
		if(is_uploaded_file($image_file_1["tmp_name"])){
			$err = $images_obj->UploadSuccessImages($image_file_1, $id_story,1);
			if(strlen($err)>0)	{EditStory("edit",$err,$id_story); return;}
		}
		if(is_uploaded_file($image_file_2["tmp_name"])){
			$err = $images_obj->UploadSuccessImages($image_file_2, $id_story,2);
			if(strlen($err)>0)	{EditStory("edit",$err,$id_story); return;}
		}
		if(is_uploaded_file($image_file_3["tmp_name"])){
			$err = $images_obj->UploadSuccessImages($image_file_3, $id_story,3);
			if(strlen($err)>0)	{EditStory("edit",$err,$id_story); return;}
		}


		$dbconn->Execute("UPDATE ".SUCCESS_STORIES_TABLE."
							SET couple_name='".addslashes($couple_name)."', story_title='".addslashes($story_title)."', description='".addslashes($description)."',	story_date='".$s_date."'
							WHERE id='".$id_story."'");
	}
	ListStories();
	exit;
}

function AddStory(){
	global $smarty, $dbconn, $config, $config_admin, $lang, $file_name;
	$images_obj = new Images($dbconn);

	$couple_name = $_POST["couple_name"];
	$story_title = $_POST["story_title"];
	$description = $_POST["description"];
	$s_year = $_POST["s_year"];
	$s_month = sprintf("%02d",$_POST["s_month"]);
	$s_day = sprintf("%02d",$_POST["s_day"]);

	$image_file_1 = $_FILES["image_path_1"];
	$image_file_2 = $_FILES["image_path_2"];
	$image_file_3 = $_FILES["image_path_3"];

	if (checkdate($s_month, $s_day, $s_year)){
		$s_date = $s_year."-".$s_month."-".$s_day;
	} else {
		$err = $lang["err"]["invalid_date"];
		EditStory("add",$err);
		exit;
	}
	/// check empty values
	if(!strlen($couple_name) || !strlen($story_title)){
		$err = $lang["err"]["invalid_fields"];
		EditStory("add",$err);
		exit;
	}

	$dbconn->Execute("	INSERT INTO ".SUCCESS_STORIES_TABLE." (couple_name, story_title, image_path_1, image_path_2, image_path_3, description, story_date)
						VALUES ('".addslashes($couple_name)."', '".addslashes($story_title)."', '0', '0', '0', '".addslashes($description)."', '".$s_date."') ");
	$id_story = $dbconn->Insert_ID();
	$image_path = array();
	if(is_uploaded_file($image_file_1["tmp_name"])){
		$err = $images_obj->UploadSuccessImages($image_file_1, $id_story,1);
		if(strlen($err)>0)	{EditStory("add",$err,$id_story); return;}
	} else {
		array_push($image_path, "image_path_1='0'");
	}

	if(is_uploaded_file($image_file_2["tmp_name"])){
		$err = $images_obj->UploadSuccessImages($image_file_2, $id_story,2);
		if(strlen($err)>0)	{EditStory("add",$err,$id_story); return;}
	} else {
		array_push($image_path, "image_path_2='0'");
	}

	if(is_uploaded_file($image_file_3["tmp_name"])){
		$err = $images_obj->UploadSuccessImages($image_file_3, $id_story,3);
		if(strlen($err)>0)	{EditStory("add",$err,$id_story); return;}
	} else {
		array_push($image_path, "image_path_3='0'");
	}

	if (sizeof($image_path)>0) {
		$image_str = implode(",", $image_path);
		$dbconn->Execute("	UPDATE ".SUCCESS_STORIES_TABLE." SET ".$image_path."
							WHERE id='".$id_story."' ") ;
	}
	ListStories();
	exit;
}

function DeleteImage($num, $id_story,$path){
	global $dbconn, $config;

	$rs = $dbconn->Execute("SELECT image_path_".$num." FROM ".SUCCESS_STORIES_TABLE." WHERE id='".$id_story."'");

	if (($rs->fields[0]!='0') && (file_exists($path.$rs->fields[0])))
	@unlink($path.$rs->fields[0]);

	$dbconn->Execute("UPDATE ".SUCCESS_STORIES_TABLE." set image_path_".$num."='0' WHERE id='".$id_story."'");
	return;
}

function DeleteStory(){
	global $smarty, $dbconn, $config, $config_admin, $lang, $file_name;

	$id_story = intval($_GET["id_story"]);

	$photos_folder = GetSiteSettings('success_folder');
	DeleteImage("1",$id_story,$config["site_path"].$photos_folder."/");
	DeleteImage("2",$id_story,$config["site_path"].$photos_folder."/");
	DeleteImage("3",$id_story,$config["site_path"].$photos_folder."/");

	$dbconn->Execute("DELETE FROM ".SUCCESS_STORIES_TABLE." WHERE id='".$id_story."'");

	ListStories();
	exit;
}

?>