<?php

include_once('init.php');

$GLOBALS['fc_config']['cms']->LoginCheck(__FILE__);

AdminMainMenu($lang["chats"]);

$delete_pref = "delete";
$status_pref = "status";
$ids_pref = "ids";

if(isset($_POST['sel'])) {
	if($_POST['sel'] == "submit_all") {
		foreach ($_POST[$ids_pref] as $row => $id) {
			if (isset($_POST[$delete_pref][$row])) {
				// delete row
				$query = "DELETE FROM {$GLOBALS['fc_config']['db']['pref']}moderators WHERE id=".$id;
				$stmt1 = new Statement($query);
				$rs1 = $stmt1->process();
			} else {
				// update row
				if (isset($_POST[$status_pref][$row])) {
					$new_status = '1';
				} else {
					$new_status = '0';
				}
				if ($id == '1') $new_status = '1';
				$query = "UPDATE {$GLOBALS['fc_config']['db']['pref']}moderators set status='".$new_status."' WHERE id=".$id;
				$stmt = new Statement($query);
				$rs = $stmt->process();
			}
		}
	}
	if($_POST['sel'] == "search") {
		if (isset($_POST["search"])) {
			$search = $_POST["search"];
			$s_type = $_POST["s_type"];
			if(strval($search)){
				$search = strip_tags($search);
				switch($s_type){
					case "1": $search_str=" and login like '%".$search."%'"; break;
					case "2": $search_str=" and fname like '%".$search."%'"; break;
					case "3": $search_str=" and sname like '%".$search."%'"; break;
					case "4": $search_str=" and email like '%".$search."%'"; break;
				}
			}
			$smarty->assign("search", $search);
			if($search){
				$search_select = array();
				$stmt = new Statement("select id, login, fname, sname from ".USERS_TABLE." where root_user != '1' and guest_user != '1' ".$search_str." order by id");
				if($rs = $stmt->process()) {
					$row_nr = 0;
					while($rec = $rs->next()) {
						$search_select[$row_nr]["id"] = $rec["id"];
						$search_select[$row_nr]["name"] = stripslashes($rec["fname"]." ".$rec["sname"]." (".$rec["login"].")");
						$row_nr++;
					}
					$smarty->assign("search_select", $search_select);
				}
			}

		}
	}
	if($_POST['sel'] == "add") {
		if (isset($_POST["search_select"])) {
			$id = $_POST["search_select"];

			$stmt = new Statement("SELECT id FROM {$GLOBALS['fc_config']['db']['pref']}moderators WHERE userid=".$id);
			if(!($rs = $stmt->process()) || !($rec = $rs->next())) {
				$query = "INSERT INTO {$GLOBALS['fc_config']['db']['pref']}moderators (created, userid, status) values (NOW(), '".$id."', '1')";
				$stmt1 = new Statement($query);
				$rs1 = $stmt1->process();
			}
		}
	}
}

for($i=0;$i<4;$i++){
	if(isset($s_type) && $s_type==($i+1))$types[$i]["sel"]="1";
	$types[$i]["value"]=$lang["users"]["type_".($i+1)];
}
$smarty->assign("types", $types);

$stmt = new Statement("SELECT a.id, a.status, DATE_FORMAT(a.created,'".$config["date_format"]."') as date, b.fname, b.sname, b.login, b.root_user FROM {$GLOBALS['fc_config']['db']['pref']}moderators a, ".USERS_TABLE." b WHERE a.userid=b.id ORDER BY b.login, b.fname, b.sname");
$rs = $stmt->process();

$mods = array();
$row_nr = 1;
while($rec = $rs->next()) {
	$temp_mod 			= array();
	$temp_mod['id']			= $rec['id'];
	$temp_mod['row_nr']		= $row_nr;
	$temp_mod['login']		= $rec['login'];
	$temp_mod['name']		= stripslashes($rec['fname']." ".$rec['sname']);
	$temp_mod['status']		= $rec['status'];
	$temp_mod['root_user']		= $rec['root_user'];
	$temp_mod['date']		= $rec['date'];
	$temp_mod['id_name']		= $ids_pref.'['.$row_nr.']';
	$temp_mod['status_name']	= $status_pref.'['.$row_nr.']';
	$temp_mod['delete_name']	= $delete_pref.'['.$row_nr.']';

	$row_nr++;
	array_push($mods, $temp_mod);
}

$form["action"] = "./modlist.php";

//Assign Smarty variables and load the admin template
$smarty->assign('form',$form);
$smarty->assign('mods',$mods);
$smarty->display('modlist.tpl');

?>