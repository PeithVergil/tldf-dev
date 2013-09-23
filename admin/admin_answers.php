<?php

include '../include/config.php';
include_once '../common.php';
include '../include/config_admin.php';
include '../include/functions_auth.php';
include '../include/functions_admin.php';
include '../include/class.answ_catalog.php';

$auth = auth_user();
login_check($auth);
IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "addition");

if(isset($_SERVER["PHP_SELF"]))
	$file_name = AfterLastSlash($_SERVER["PHP_SELF"]);
else
	$file_name = "admin_answers.php";
$smarty->assign('file_name', $file_name);

$Catalog = new Catalog($dbconn, $config, $lang);

$sel = isset($_REQUEST["sel"]) ? $_REQUEST["sel"] : "";
switch ($sel){
	case 'cats_request': returnCategories(); break; 
	case 'add_cat': addCategory(); break;
	case 'edit_cat': editCategory(); break;
	case 'del_cat': delCategory(); break;
	case 'add_q': addQuestion(); break;
	case 'get_questions':getQuestions(); break;
	case 'saveQ':saveQuestion(); break;
	case 'delQ':delQuestion(); break;
	case 'add_answer':addAnswer(); break;
	case 'get_answers':getAnswers(); break;
	case 'del_answer':deleteAnswer(); break;
	case 'make_best':makeBest(); break;
	default: listCategories(); break;
}

function listCategories(){
	global $lang, $smarty, $config, $Catalog;

	AdminMainMenu($lang["addition"]);
	
	$Catalog->getCategories(0);
	
	
	$smarty->assign('categories',$Catalog->getCategories(0));
	
	$smarty->assign('level', 0);
	$smarty->assign('max_level', 1);
	
	$smarty->display(TrimSlash($config["admin_theme_path"])."/admin_answers_table.tpl");
	exit;
}

function returnCategories(){
	global $smarty, $Catalog, $config;
	
	$id_parent = intval($_REQUEST['id_parent']);
	if ($id_parent <= 0) return false;
	
	$smarty->assign('categories',$Catalog->getCategories($id_parent));
	
	$level = count($Catalog->getAllParents($id_parent))+1;
	$smarty->assign('level', $level);
	echo $smarty->fetch(TrimSlash($config["admin_theme_path"])."/admin_answers_cats_pattern.tpl");
	exit;
}

function addCategory(){
	global $Catalog, $lang;
	
	$id_parent = $_REQUEST['id_parent'];
	$name = strip_tags(addslashes($_REQUEST['value']));
	$new_id = $Catalog->addCategory($id_parent, $name, 1);
	if (!$new_id){
		echo msgTemplate($Catalog->getErrorMsg());
	}else{
		echo msgTemplate($lang["answers"]["saved"],$new_id);
	}
	exit;
}

function editCategory(){
	global $Catalog, $lang;
	
	$id = intval($_REQUEST['id']);
	$name = strip_tags(addslashes($_REQUEST['value']));
	
	if ($Catalog->updateCategory($id, $name)){
		echo msgTemplate($lang["answers"]["saved"],1);
	}else{
		echo msgTemplate($Catalog->getErrorMsg());
	}
	exit;
}

function delCategory(){
	global $Catalog, $lang;
	
	$id = intval($_REQUEST['id']);
	if ($Catalog->deleteCategory($id)){
		echo msgTemplate($lang["answers"]["deleted"],1);
	}else{
		echo msgTemplate($Catalog->getErrorMsg());
	}
	exit;
}

function addQuestion(){
	global $Catalog, $lang, $auth;
	
	if ($Catalog->addItem($_REQUEST['value'], $_REQUEST['details'], $_REQUEST['id_parent'], $auth[0])){
		echo msgTemplate($lang["answers"]["q_added"],1);
	}else{
		echo msgTemplate($Catalog->getErrorMsg());
	}
	exit;
}

function getQuestions(){
	global $Catalog, $lang, $smarty, $config_admin, $auth, $config;
	
	if (isset($_REQUEST['change_filter']) && $_REQUEST['change_filter'] == 1){
		unset($_SESSION['page']);
		unset($_REQUEST['page']);
	}
		
	
	if (isset($_REQUEST['page'])){
		$_SESSION['page'] = intval($_REQUEST['page']);
	}	
	
	if (isset($_REQUEST['view_all'])){
		unset($_REQUEST['id_parent']);
		unset($_REQUEST['open_sort']);
		unset($_REQUEST['closed_sort']);
		unset($_REQUEST['yours_sort']);
		unset($_SESSION['id_parent']);
		unset($_SESSION['open_sort']);
		unset($_SESSION['closed_sort']);
		unset($_SESSION['yours_sort']);
		$_SESSION['view_all'] = $_REQUEST['view_all'];
	}
	
	
	if (isset($_REQUEST['id_parent'])){
		$_SESSION['id_parent'] = $_REQUEST['id_parent'];
		$path = $Catalog->getAllPath($_SESSION['id_parent']);
		unset($_SESSION['view_all']);
	}else
		$_SESSION['id_parent'] = -1;
	
	if (isset($_REQUEST['open_sort'])){
		$_SESSION['open_sort'] = $_REQUEST['open_sort'];
		unset($_REQUEST['view_all']);
	}
	if (isset($_REQUEST['closed_sort'])){
		$_SESSION['closed_sort'] = $_REQUEST['closed_sort'];
		unset($_REQUEST['view_all']);
	}
	if (isset($_REQUEST['yours_sort'])){
		$_SESSION['yours_sort'] = $_REQUEST['yours_sort'];
		unset($_REQUEST['view_all']);
	}
	
	$id_parent = $_SESSION['id_parent'];
	$open_sort = $_SESSION['open_sort'];
	$closed_sort = $_SESSION['closed_sort'];
	$yours_sort = $_SESSION['yours_sort'];
	
	if (isset($_SESSION['page']) && intval($_SESSION['page']) > 1)
		$page = $_SESSION['page'];
	else
		$page = 1;
	
	$form['page'] = $page;
		
	if ($_SESSION['yours_sort'] == 1) $id_owner=$auth[0];
	
	switch ($_SESSION['view_all']){
		case 'opened': $open_sort = 1;break;
		case 'closed': $closed_sort = 1;break;
		case 'yours': $id_owner = $auth[0];break;
	}
	
	$form['view_all'] = $_SESSION['view_all'];
	$form['yours'] = $id_owner;
	$form['opened'] = $open_sort;
	$form['closed'] = $closed_sort;
	$from = $config_admin["questions_numpage"]*($page-1);
	$count = $config_admin["questions_numpage"];
	
	$questions = $Catalog->getItems($from, $count, $id_parent, $id_owner, $open_sort, $closed_sort);
	
	$count_records = $Catalog->getCountItems($id_parent, $id_owner, $open_sort, $closed_sort);
	
	$link_arr = getAnswLinkArray($count_records,$page,$param,$count);
	
	$smarty->assign('auth', $auth);
	$smarty->assign('path', $path);
	$smarty->assign('count_records', $count_records);
	$smarty->assign('link_arr', $link_arr);
	$smarty->assign('questions', $questions);

	
	$smarty->assign('form',$form);
	echo $smarty->fetch(TrimSlash($config["admin_theme_path"])."/admin_answers_quest_pattern.tpl");
	exit;
}

function saveQuestion(){
	global $Catalog, $lang;
	
	if ($Catalog->updateItem($_REQUEST['id'], $_REQUEST['value'], $_REQUEST['details'])){
		echo msgTemplate($lang['answers']['q_updated'],1);
	}else{
		echo msgTemplate($Catalog->getErrorMsg());
	}
	exit;
}

function delQuestion(){
	global $Catalog, $lang;
	
	$id = $_REQUEST['id'];
	
	if ($Catalog->deleteItem($id)){
		echo msgTemplate($lang['answers']['q_deleted'],1);
	}else{
		echo msgTemplate($Catalog->getErrorMsg());
	}
	exit;
}

function addAnswer(){
	global $Catalog, $lang, $auth, $dbconn;
	ini_set("dispay_errors",1);
	$id_parent = $_REQUEST['id_parent'];
	$text = $_REQUEST['value'];
	
	//RS: we had $user instead of $auth but this does not make sense
	$strSQL = "SELECT id FROM ".ANSW_QUESTIONS_TABLE." WHERE id='".$id_parent."' AND (id_owner='".$auth[0]."' OR is_open='0') ";
	$id_q = $dbconn->getOne($strSQL);
	if ($id_q > 0){
		echo msgTemplate($lang['answers']['answer_on_own_q']);
		exit;
	}
	
	if ($Catalog->addComment($id_parent, $auth[0], $text)){
		echo msgTemplate($lang['answers']['answer_added'],1);
	}else{
		echo msgTemplate($Catalog->getErrorMsg());
	}
	exit;
}

function getAnswers(){
	global $dbconn, $Catalog, $lang, $smarty, $config_admin, $auth, $config;
	
	$id_parent = intval($_REQUEST['id_parent']);
	
	if (isset($_REQUEST['ans_page'.$id_parent])){
		$_SESSION['ans_page'.$id_parent] = intval($_REQUEST['ans_page'.$id_parent]);
	}	
	
	if (isset($_SESSION['ans_page'.$id_parent]) && intval($_SESSION['ans_page'.$id_parent]) > 1)
		$page = $_SESSION['ans_page'.$id_parent];
	else
		$page = 1;
	
	$strSQL = "SELECT id FROM ".ANSW_QUESTIONS_TABLE." WHERE id='".$id_parent."' AND id_owner='".$auth[0]."'";
	$is_owner = $dbconn->GetOne($strSQL);
	if ($is_owner > 0){
		$form['owner'] = 1;
	}
	
	$strSQL = "SELECT id FROM ".ANSW_ANSWERS_TABLE." WHERE id_parent='".$id_parent."' AND is_best='1' ";
	$form['id_best'] = $dbconn->GetOne($strSQL);
	
	$form['page'] = $page;
	$form['id_parent'] = $id_parent;
	
	$from = $config_admin["questions_numpage"]*($page-1);
	$count = $config_admin["questions_numpage"];
	
	$answers = $Catalog->getComments($id_parent, $from, $count);
	
	$count_records = $Catalog->getCountComments($id_parent);
	
	$link_arr = getAnswLinkArray($count_records,$page,$param,$count);
	
	$smarty->assign('form',$form);
	$smarty->assign('auth', $auth);
	$smarty->assign('count_records', $count_records);
	$smarty->assign('link_arr_answ', $link_arr);
	$smarty->assign('answers', $answers);

	echo $smarty->fetch(TrimSlash($config["admin_theme_path"])."/admin_answers_answ_pattern.tpl");
	exit;
}

function deleteAnswer(){
	global $Catalog;
	$Catalog->deleteComment($_REQUEST['id'], $_REQUEST['id_parent']);
	exit;
}

function makeBest(){
	global $Catalog;
	$Catalog->makeBestComment($_REQUEST['id'],$_REQUEST['id_parent']);
	exit;
}

function msgTemplate($msg,$code=0){
	return "<div id='tmp_msg' >".$msg."</div><div id='tmp_code' >".$code."</div>";
}

function getAnswLinkArray($num_records, $page, $param, $max_record, $dop_param=""){
	global $lang;
	/// settings
	$dop_param["page_var_name"] = (isset($dop_param["page_var_name"]) && strlen($dop_param["page_var_name"]))?$dop_param["page_var_name"]:"page";
	$dop_param["left_arrow_name"] = (isset($dop_param["left_arrow_name"]) && strlen($dop_param["left_arrow_name"]))?$dop_param["left_arrow_name"]:"...";
	$dop_param["right_arrow_name"] = (isset($dop_param["right_arrow_name"]) && strlen($dop_param["right_arrow_name"]))?$dop_param["right_arrow_name"]:"...";

	$num_page = ceil($num_records/$max_record);  ////////// ���������� ������� (�� $max_record ������� �� ��������)
	if($num_page<2){
		return array();
	}
	$p_page_count = 10; /// ���������� ������ �� ��������
	$p_page = floor(($page-1)/$p_page_count);    ////// �������� �������
	$j = 0;

	if($p_page>0){
		$ret_links[$j]["name"] = $dop_param["left_arrow_name"];
		$ret_links[$j]["link"] = $param."".$param["page_var_name"]."=".($p_page*$p_page_count);
		$ret_links[$j]["page"] = $p_page*$p_page_count;
		$ret_links[$j]["selected"] = 0;
		$j++;
	}
	//	for($i=($p_page*$p_page_count+1);$i<$num_page;$i++){
	$top_limit = ((($p_page+1)*$p_page_count+1)<=$num_page)?(($p_page+1)*$p_page_count+1):$num_page+1;
	for($i=($p_page*$p_page_count+1);$i<$top_limit;$i++){
		$ret_links[$j]["name"] = $i;
		$ret_links[$j]["link"] = $param."".$dop_param["page_var_name"]."=".$i;
		$ret_links[$j]["page"] = $i;
		$ret_links[$j]["selected"] = ($i == $page)?1:0;
		$j++;
	}
	if( (($p_page+1)*$p_page_count) < $num_page){
		$ret_links[$j]["name"] = $dop_param["right_arrow_name"];
		$ret_links[$j]["link"] = $param."".$dop_param["page_var_name"]."=".(($p_page+1)*$p_page_count+1);
		$ret_links[$j]["page"] = (($p_page+1)*$p_page_count+1);
		$ret_links[$j]["selected"] =0;
		$j++;
	}
	return $ret_links;

}
?>