<?php

/**
* Class for help information pages (advice and FAQ)
*
* @package DatingPro
* @subpackage Include files
**/

class HelpInfo
{
	var $config;
	var $dbconn;
	var $file_name;
	var $local_config;

	function HelpInfo($config, $dbconn, $file_name, $local_config)
	{
		$this->config = $config;
		$this->dbconn = $dbconn;
		$this->file_name = $file_name;
		$this->local_config = $local_config;
		return;
	}

	function GetCategoriesList($strip=1, $enabled_only=0)
	{
		$str_add = "";
		if ($this->local_config["use_status"] == 1) {
			$str_add .= ", a.status";
		}
		$where_str = "";
		if ($enabled_only == 1) {
			$where_str .= "WHERE a.status='1'";
		}
		$strSQL = "	SELECT DISTINCT a.id, a.name, a.description, b.name AS name_lang, b.content AS content_lang".$str_add."
				FROM ".$this->local_config['category_table']." a
				LEFT JOIN ".INFO_LANG_CONTENT_TABLE." b ON a.id=b.id_info AND b.id_lang=".$this->config['default_lang']." AND b.table_key=".$this->local_config['category_table_key']."
				".$where_str."
				GROUP BY a.id ORDER BY a.id
			";
		$rs = $this->dbconn->Execute($strSQL);
		$i = 0;
		$cat_list = array();
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$cat_list[$i]["id"] = $row["id"];
			$cat_list[$i]["name"] = $row["name_lang"] ? stripslashes($row["name_lang"]):stripslashes($row["name"]);
			$description = $row["content_lang"] ? stripslashes($row["content_lang"]) : stripslashes($row["description"]);
			if (strlen(utf8_decode($description)) > 200 && $strip == 1) {
				$description = strip_tags($description);
				$cat_list[$i]["descr"] = nl2br(utf8_substr($description, 0, 200))." ...";
			} else {
				$cat_list[$i]["descr"] = nl2br($description);
			}
			$cat_list[$i]["item_link"] = "./".$this->file_name."?sel=list_item&id=".$row["id"];
			$cat_list[$i]["edit_link"] = "./".$this->file_name."?sel=edit_cat&id=".$row["id"];
			$cat_list[$i]["del_link"] = "./".$this->file_name."?sel=del_cat&id=".$row["id"];
			if ($this->local_config["use_status"] == 1) {
				$cat_list[$i]["status"] = $row["status"];
			}
			$rs->MoveNext();
			$i++;
		}
		return $cat_list;
	}

	function GetItemsList($cat_id, $strip=1)
	{
		$strSQL =
			"SELECT DISTINCT a.id, a.title, a.body, c.name AS name_lang, c.content AS content_lang
			   FROM ".$this->local_config['item_table']." a
		  LEFT JOIN ".INFO_LANG_CONTENT_TABLE." c ON a.id=c.id_info AND c.id_lang=".$this->config['default_lang']." AND c.table_key=".$this->local_config['item_table_key']."
			  WHERE a.id_category='".$cat_id."'
		   GROUP BY a.id
		   ORDER BY a.id";
		$rs = $this->dbconn->Execute($strSQL);
		$i = 0;
		$adv_list = array();
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$adv_list[$i]["id"] = $row["id"];
			$adv_list[$i]["title"] = $row["name_lang"] ? stripslashes($row["name_lang"]) : stripslashes($row["title"]);
			$body = $row["content_lang"] ? stripslashes($row["content_lang"]) : stripslashes($row["body"]);

			if(strlen(utf8_decode($body)) > 200 && $strip == 1) {
				$body = strip_tags($body);
				$adv_list[$i]["body"] = nl2br(utf8_substr($body, 0, 200))." ...";
			} else {
				$adv_list[$i]["body"] = nl2br($body);
			}
			$adv_list[$i]["edit_link"] = "./".$this->file_name."?sel=edit_item&id=".$row["id"];
			$adv_list[$i]["del_link"] = "./".$this->file_name."?sel=del_item&id=".$row["id"];
			$rs->MoveNext();
			$i++;
		}
		return $adv_list;
	}

	function GetLanguageLinks($key, $id_info)
	{
		$strSQL = "SELECT id, name FROM ".LANGUAGE_TABLE." ORDER BY id";
		$rs = $this->dbconn->Execute($strSQL);
		$i = 0;
		$lang_link_arr = array();
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$lang_link_arr[$i]["id"] = $row["id"];
			$lang_link_arr[$i]["name"] = $row["name"];
			$lang_link_arr[$i]["link"] = "./admin_translate_info.php?table_key=".$key."&id_lang=".$row["id"]."&id_info=".$id_info;
			$rs->MoveNext();
			$i++;
		}
		return $lang_link_arr;
	}

	function GetCategoryInfo($cat_id)
	{
		$rs = $this->dbconn->Execute("SELECT id, name FROM ".$this->local_config['category_table']." WHERE id='".$cat_id."' ");
		$form["category_id"] = $rs->fields[0];
		$form["category"] = stripslashes($rs->fields[1]);
		return $form;
	}

	function GetCategoryEditInfo($id = "")
	{
		$str_add = "";
		if ($this->local_config["use_status"] == 1) {
			$str_add .= ", a.status";
		}
		$strSQL = "SELECT a.id, a.name, a.description, b.name AS name_lang, b.content AS content_lang".$str_add."
				FROM ".$this->local_config['category_table']." a
				LEFT JOIN ".INFO_LANG_CONTENT_TABLE." b ON a.id=b.id_info AND b.id_lang=".$this->config['default_lang']." AND b.table_key=".$this->local_config['category_table_key']."
				WHERE a.id='".$id."'";
		$rs = $this->dbconn->Execute($strSQL);
		$row = $rs->GetRowAssoc(false);
		$form["name"] = $row["name_lang"] ? stripslashes($row["name_lang"]) : stripslashes($row["name"]);
		$form["descr"] = $row["content_lang"] ? stripslashes($row["content_lang"]) : stripslashes($row["description"]);
		$form["save_link"] = "./".$this->file_name."?sel=save_cat&id=".$row["id"];
		if ($this->local_config["use_status"] == 1) {
			$form["status"] = $row["status"];
		}
		return $form;
	}

	function GetItemEditInfo($id = "")
	{
		$strSQL="SELECT a.id, a.id_category, a.title, a.body, b.name AS category, c.name AS name_lang, c.content AS content_lang
				FROM ".$this->local_config['category_table']." b, ".$this->local_config['item_table']." a
				LEFT JOIN ".INFO_LANG_CONTENT_TABLE." c ON a.id=c.id_info AND c.id_lang=".$this->config['default_lang']." AND c.table_key=".$this->local_config['item_table_key']."
				WHERE a.id_category=b.id and a.id='".$id."'";
		$rs = $this->dbconn->Execute($strSQL);
		$row = $rs->GetRowAssoc(false);
		$form["category"] = stripslashes($row["category"]);
		$form["category_id"] = $row["id_category"];
		$form["title"] = $row['name_lang'] ? stripslashes($row["name_lang"]) : stripslashes($row["title"]);
		$form["body"] = $row['content_lang'] ? stripslashes($row["content_lang"]) : stripslashes($row["body"]);
		$form["save_link"] = "./".$this->file_name."?sel=save_item&id=".$row["id"];
		return $form;
	}

	function DeleteCategory($id = "")
	{
		$strSQL = "SELECT DISTINCT id FROM ".$this->local_config['item_table']." WHERE id_category='".$id."' GROUP BY id ";
		$rs = $this->dbconn->Execute($strSQL);
		if ($rs->fields[0] > 0) {
			while(!$rs->EOF){
				$this->DeleteItem($rs->fields[0]);
				$rs->MoveNext();
			}
		}
		$this->dbconn->Execute("DELETE FROM ".$this->local_config['category_table']." WHERE id='".$id."'");
		$this->dbconn->Execute("DELETE FROM ".INFO_LANG_CONTENT_TABLE." WHERE table_key=".$this->local_config['category_table_key']." AND id_info='".$id."'");
		return;
	}

	function DeleteItem($id = "")
	{
		$this->dbconn->Execute("DELETE FROM ".$this->local_config['item_table']." WHERE id='".$id."'");
		$this->dbconn->Execute("DELETE FROM ".INFO_LANG_CONTENT_TABLE." WHERE table_key=".$this->local_config['item_table_key']." AND id_info='".$id."'");
		return ;
	}

	function SaveCategory($type)
	{
		if ($type == "save") {
			$id = isset($_REQUEST['id']) && intval($_REQUEST['id']) > 0 ? intval($_REQUEST['id']) : null;
			$str_add = "";
			if ($this->local_config["use_status"] == 1) {
				$str_add .= ", status='".intval($_POST['status'])."'";
			}
			$strSQL = "UPDATE ".$this->local_config['category_table']." SET name='".addslashes($_POST["name"])."', description='".addslashes($_POST["code"])."'".$str_add." WHERE id='".$id."'";
			$this->dbconn->Execute($strSQL);
			$strSQL = "SELECT COUNT(*) FROM ".INFO_LANG_CONTENT_TABLE." WHERE id_info=".$id." AND id_lang=".$this->config['default_lang']." AND table_key=".$this->local_config['category_table_key']."";
			$rs = $this->dbconn->Execute($strSQL);

			if ($rs->fields[0] > 0) {
				$strSQL = "UPDATE ".INFO_LANG_CONTENT_TABLE." SET name='".addslashes($_POST['name'])."', content='".addslashes($_POST['code'])."' WHERE id_info=".$id." AND id_lang=".$this->config['default_lang']." AND table_key=".$this->local_config['category_table_key']."";
			} else {
				$strSQL = "INSERT INTO ".INFO_LANG_CONTENT_TABLE."(table_key, id_info, name, content, id_lang) VALUES(".$this->local_config['category_table_key'].", ".$id.", '".addslashes($_POST['name'])."', '".addslashes($_POST['code'])."', ".$this->config['default_lang'].")";
			}
			$this->dbconn->Execute($strSQL);
		} else {
			$str_add_1 = "";
			$str_add_2 = "";
			if ($this->local_config["use_status"] == 1) {
				$str_add_1 .= ", status";
				$str_add_2 .= ", '".intval($_POST['status'])."'";
			}
			$strSQL = "INSERT INTO ".$this->local_config['category_table']." (name, description".$str_add_1.") VALUES ('".addslashes($_POST["name"])."','".addslashes($_POST["code"])."'".$str_add_2.")";
			$this->dbconn->Execute($strSQL);
			$id = $this->dbconn->Insert_ID();
			$strSQL = "INSERT INTO ".INFO_LANG_CONTENT_TABLE."(table_key, id_info, name, content, id_lang) VALUES(".$this->local_config['category_table_key'].", ".$id.", '".addslashes($_POST['name'])."', '".addslashes($_POST['code'])."', ".$this->config['default_lang'].")";
			$this->dbconn->Execute($strSQL);
		}
		return;
	}

	function SaveItem($type)
	{
		if ($type == "save"){
			$id = isset($_REQUEST['id']) && intval($_REQUEST['id']) > 0 ? intval($_REQUEST['id']) : null;
			$this->dbconn->Execute("UPDATE ".$this->local_config['item_table']." SET id_category='".$_POST["category"]."', title='".addslashes($_POST["title"])."', body='".addslashes($_POST["code"])."' WHERE id='".$id."'");
			$cat_id = $_POST["category"];
			$strSQL="SELECT COUNT(*) FROM ".INFO_LANG_CONTENT_TABLE." WHERE id_info=".$id." AND id_lang=".$this->config['default_lang']." AND table_key=".$this->local_config['item_table_key']."";
			$rs = $this->dbconn->Execute($strSQL);

			if($rs->fields[0] > 0) {
				$strSQL = "UPDATE ".INFO_LANG_CONTENT_TABLE." SET name='".addslashes($_POST['title'])."', content='".addslashes($_POST['code'])."' WHERE id_info=".$id." AND id_lang=".$this->config['default_lang']." AND table_key=".$this->local_config['item_table_key']."";
			} else {
				$strSQL = "INSERT INTO ".INFO_LANG_CONTENT_TABLE."(table_key, id_info, name, content, id_lang) VALUES(".$this->local_config['item_table_key'].", ".$id.", '".addslashes($_POST['title'])."', '".addslashes($_POST['code'])."', ".$this->config['default_lang'].")";
			}
			$this->dbconn->Execute($strSQL);
		} else {
			$cat_id = isset($_REQUEST['id']) && intval($_REQUEST['id']) > 0 ? intval($_REQUEST['id']) : null;
			$this->dbconn->Execute("INSERT INTO ".$this->local_config['item_table']." (id_category, title, body) VALUES ('".$cat_id."', '".addslashes($_POST["title"])."','".addslashes($_POST["code"])."')");
			$id = $this->dbconn->Insert_ID();
			$strSQL = "INSERT INTO ".INFO_LANG_CONTENT_TABLE."(table_key, id_info, name, content, id_lang) VALUES(".$this->local_config['item_table_key'].", ".$id.", '".addslashes($_POST['title'])."', '".addslashes($_POST['code'])."', ".$this->config['default_lang'].")";
			$this->dbconn->Execute($strSQL);
		}
		return $cat_id;
	}
}

?>