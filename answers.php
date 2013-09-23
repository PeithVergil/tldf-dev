<?php
/**
* Questions and Answers
*
* @package DatingPro
* @subpackage User Mode
**/

include './include/config.php';
include './common.php';
include './include/config_index.php';
include './include/config_admin.php';
include './include/functions_auth.php';
include './include/functions_index.php';
include './include/class.lang.php';
include './include/class.answ_catalog.php';

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

$file_name = isset($_SERVER["PHP_SELF"]) ? AfterLastSlash($_SERVER["PHP_SELF"]) : $file_name = "admin_answers.php";

$smarty->assign('file_name', $file_name);

$config['answers_on_page'] = $config_index['questions_numpage'];

$Catalog = new Catalog($dbconn, $config, $lang);

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

// dispatcher
switch ($sel) {
	case 'ask': askQTable();break;
	case 'answer': answerQTable();break;
	case 'get_cats': getCats();break;
	case 'add_q': addQ();break;
	case 'my_q': myQTable();break;
	case 'my_a': myATable();break;
	case 'get_answers': getAnswers();break;
	case 'make_best': makeBest();break;
	case 'get_q': getQuestions();break;
	case 'add_answer':addAnswer(); break;
	case 'search':searchTable(); break;
	case 'get_searched':getSearched(); break;	
	case 'get_experts':getExperts(); break;
	case 'get_exp_answers':getExpertsAnswers(); break;
	case 'experts':ExpertsTable(); break;
	default: qHomePage();break;
}

exit;


function qHomePage()
{
	global $smarty, $config, $Catalog, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$smarty->assign("qa_info", $Catalog->getStat($id_user));
	
	$form["hiddens"] = "<input type='hidden' name='sel' value='ask' />";
	
	$from = 0;
	$count = 5;
	
	$questions = $Catalog->getItems($from, $count, -1, 0, 0, 0, $id_user);
	
	$experts = $Catalog->getExperts($from, $count);
	
	$smarty->assign("experts", $experts);
	$smarty->assign("questions", $questions);
	$smarty->assign("form", $form);
	$smarty->display(TrimSlash($config["index_theme_path"])."/answ_hp.tpl");
	exit;
}

function askQTable($err='')
{
	global $smarty, $config, $Catalog, $user;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$categories = $Catalog->getCategories(0,0,0,1);
	
	if (isset($_POST['cats'])){
		$smarty->assign('categories1', $Catalog->getCategories($_POST['cats']));
	}
	
	$form['hiddens'] = '<input type="hidden" name="sel" value="add_q" />';
	$smarty->assign('categories',$categories);
	
	$smarty->assign('data',$_POST);
	$smarty->assign('form',$form);
	$smarty->assign('err',$err);
	$smarty->display(TrimSlash($config["index_theme_path"])."/answ_ask.tpl");
	exit;
}

function getCats()
{
	global $Catalog;
	
	$categories = $Catalog->getCategories(intval($_REQUEST['id_parent']));
	$str = '<select id="categs'.(intval($_REQUEST['parent_level'])+1).'" name="cats'.(intval($_REQUEST['parent_level'])+1).'" size="10"  style="width:250px;">';
	foreach ($categories as $item){
		$str .= '<option value='.$item['id'].'>'.$item['name'].'</option>';
	}
	$str .= '</select>';
	echo $str;
	exit;
}

function addQ()
{
	global $Catalog, $user;
	
	if ($err = BadWordsCont(strip_tags(addslashes($_POST["question"]." ".$_POST["details"])), 12)) {
		askQTable($err);
	}
	if (!$Catalog->addItem($_REQUEST['question'],$_REQUEST['details'], $_REQUEST['cats1'], $user[ AUTH_ID_USER ])) {
		askQTable($Catalog->getErrorMsg());
	} else {
		echo '<script type="text/javascript">location.href="answers.php?sel=my_q&page=1"</script>'; 
	}
}

function myQTable()
{
	global $Catalog, $user, $smarty, $config, $config_index;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	if (isset($_REQUEST['page'])){
		$_SESSION['page'] = intval($_REQUEST['page']);
	}

	if (isset($_SESSION['page']) && intval($_SESSION['page']) > 1)
		$page = $_SESSION['page'];
	else
		$page = 1;
	
	$form['page'] = $page;
	
	$from = $config_index["questions_numpage"]*($page-1);
	$count = $config_index["questions_numpage"];
	
	switch ($_REQUEST['filter']){
		case 'closed':
			$_SESSION['closed_sort'] = 1;
			unset($_SESSION['open_sort']);
		break;
		case 'open':
			$_SESSION['open_sort'] = 1;
			unset($_SESSION['closed_sort']);
		break;
	}
	
	$form['open_sort'] = $_SESSION['open_sort'];
	$form['closed_sort'] = $_SESSION['closed_sort'];
	
	$questions = $Catalog->getItems($from, $count, -1, $id_user, $form['open_sort'], $form['closed_sort']);
	
	$count_records = $Catalog->getCountItems(-1, $id_user, $form['open_sort'], $form['closed_sort']);
	
	$link_arr = getAnswLinkArray($count_records, $page, $param, $count);
	
	$smarty->assign('form', $form);
	$smarty->assign('count_records', $count_records);
	$smarty->assign('link_arr', $link_arr);
	$smarty->assign('questions', $questions);

	$smarty->display(TrimSlash($config["index_theme_path"])."/answ_my_q.tpl");
	exit;
}

function getAnswers()
{
	global $dbconn, $Catalog, $smarty, $config_index, $user, $config;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$id_parent = intval($_REQUEST['id_parent']);
	
	if (isset($_REQUEST['ans_page'.$id_parent])){
		$_SESSION['ans_page'.$id_parent] = intval($_REQUEST['ans_page'.$id_parent]);
	}	
	
	if (isset($_SESSION['ans_page'.$id_parent]) && intval($_SESSION['ans_page'.$id_parent]) > 1)
		$page = $_SESSION['ans_page'.$id_parent];
	else
		$page = 1;
	
	$strSQL = "SELECT id FROM ".ANSW_QUESTIONS_TABLE." WHERE id='".$id_parent."' AND id_owner='".$id_user."'";
	$is_owner = $dbconn->GetOne($strSQL);
	if ($is_owner > 0){
		$form['owner'] = 1;
	}
	
	$strSQL = "SELECT id FROM ".ANSW_ANSWERS_TABLE." WHERE id_parent='".$id_parent."' AND is_best='1' ";
	$form['id_best'] = $dbconn->GetOne($strSQL);
	
	$form['page'] = $page;
	$form['id_parent'] = $id_parent;
	
	$from = $config_index["questions_numpage"]*($page-1);
	$count = $config_index["questions_numpage"];
	
	$answers = $Catalog->getComments($id_parent, $from, $count, $_REQUEST['fs_k']);
	
	$count_records = $Catalog->getCountComments($id_parent);
	
	$link_arr = getAnswLinkArray($count_records, $page, $param, $count);
	
	$smarty->assign('form',$form);
	$smarty->assign('count_records', $count_records);
	$smarty->assign('link_arr_answ', $link_arr);
	$smarty->assign('answers', $answers);

	echo $smarty->fetch(TrimSlash($config["index_theme_path"])."/answ_answ_pattern.tpl");
	exit;
}

function makeBest()
{
	global $Catalog;
	$Catalog->makeBestComment($_REQUEST['id'],$_REQUEST['id_parent']);
	exit;
}

function myATable()
{
	global $Catalog, $smarty, $config, $user, $config_index;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	if (isset($_REQUEST['page'])){
		$_SESSION['page'] = intval($_REQUEST['page']);
	}
	if (isset($_SESSION['page']) && intval($_SESSION['page']) > 1)
		$page = $_SESSION['page'];
	else
		$page = 1;
	$form['page'] = $page;
	$from = $config_index["questions_numpage"]*($page-1);
	$count = $config_index["questions_numpage"];
	
	switch ($_REQUEST['filter']){
		case 'all':
			unset($_SESSION['best_answ']);
		break;
		case 'best_answ':
			$_SESSION['best_answ'] = 1;
		break;
	}
	$form['best_filter'] = $_SESSION['best_answ'];
	$answers = $Catalog->getCommentsByOwner($id_user, $from, $count, $form['best_filter']);

	$count_records = $Catalog->getCountCommentsByOwner($id_user, $form['best_filter']);
	
	$link_arr = getAnswLinkArray($count_records, $page, $param, $count);
	$smarty->assign('link_arr', $link_arr);
	$smarty->assign('form', $form);
	$smarty->assign('answers',$answers);
	$smarty->display(TrimSlash($config["index_theme_path"])."/answ_my_a.tpl");
	exit;
}

function answerQTable()
{
	global $smarty, $config, $config_index, $Catalog, $user;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$page = 1;
	
	$form['page'] = $page;
	$from = $config_index["questions_numpage"]*($page-1);
	$count = $config_index["questions_numpage"];
	
	$categories = $Catalog->getCategories(0,0,0,1);
	if (isset($_SESSION['q_id_parent']))
		$id_parent = $_SESSION['q_id_parent'];
	else
		$id_parent = -1;
		
	$id_owner = 0;
	$open_filter = $_SESSION['q_open_filter'];
	$closed_filter = $_SESSION['q_closed_filter'];
	$not_id_owner = $user[ AUTH_ID_USER ];
	$questions = $Catalog->getItems($from, $count, $id_parent, $id_owner, $open_filter, $closed_filter, $not_id_owner);
	
	$count_records = $Catalog->getCountItems($id_parent, $id_owner, $open_filter, $closed_filter, $not_id_owner);
	
	$link_arr = getAnswLinkArray($count_records, $page, $param, $count);
	
	$smarty->assign('form', $form);
	$smarty->assign('count_records', $count_records);
	$smarty->assign('link_arr', $link_arr);
	$smarty->assign('categories', $categories);
	$smarty->assign('questions', $questions);
	$smarty->display(TrimSlash($config["index_theme_path"])."/answ_answer.tpl");
	exit;
}

function getQuestions()
{
	global $smarty, $config, $config_index, $Catalog, $user;
	
	$id_user = $user[ AUTH_ID_USER ];
	
	if (isset($_REQUEST['page'])){
		$page = intval($_REQUEST['page']);
	}else
		$page = 1;
	
	$form['page'] = $page;
	$from = $config_index["questions_numpage"]*($page-1);
	$count = $config_index["questions_numpage"];
	
	$id_parent = intval($_REQUEST['id_parent']);
	
	switch ($_REQUEST['filter']){
		case 'opened':
			$open_filter = 1;
			$cosed_filter = 0;
		break;
		case 'closed':
			$open_filter = 0;
			$cosed_filter = 1;
		break;
		case 'all':
			$open_filter = 0;
			$cosed_filter = 0;
		break;
	}
	$id_owner = 0;
	$questions = $Catalog->getItems($from, $count, $id_parent, $id_owner, $open_filter, $cosed_filter, $id_user);
	$count_records = $Catalog->getCountItems($id_parent, $id_owner, $open_filter, $cosed_filter, $id_user);
	
	$link_arr = getAnswLinkArray($count_records, $page, $param, $count);
	
	$smarty->assign('user', $user);
	$smarty->assign('form', $form);
	$smarty->assign('count_records', $count_records);
	$smarty->assign('link_arr', $link_arr);
	$smarty->assign('questions', $questions);
	echo $smarty->fetch(TrimSlash($config["index_theme_path"])."/answ_quest_pattern.tpl");
	exit;
}

function addAnswer()
{
	global $Catalog, $lang, $user, $dbconn;
	
	ini_set("dispay_errors",1);
	
	$id_user = $user[ AUTH_ID_USER ];
	
	$id_parent = $_REQUEST['id_parent'];
	$text = $_REQUEST['value'];
	
	$strSQL = "SELECT id FROM ".ANSW_QUESTIONS_TABLE." WHERE id='".$id_parent."' AND (id_owner='".$id_user."' OR is_open='0')";
	$id_q = $dbconn->getOne($strSQL);
	if ($id_q > 0) {
		echo msgTemplate($lang['answers']['answer_on_own_q']);
		exit;
	}
	
	if ($Catalog->addComment($id_parent, $id_user, $text)) {
		echo msgTemplate($lang['answers']['answer_added'], 1);
	} else {
		echo msgTemplate($Catalog->getErrorMsg());
	}
	exit;
}

function searchTable()
{
	global $smarty, $config, $user, $Catalog;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$categories = $Catalog->getCategories(0,0,0,1);
	
	$smarty->assign("categories", $categories);
	$smarty->display(TrimSlash($config["index_theme_path"])."/answ_search.tpl");
	exit;
}

function getSearched()
{
	global $smarty, $config, $Catalog, $config_index, $user;
	
	if (isset($_REQUEST['page'])){
		$page = intval($_REQUEST['page']);
	}else
		$page = 1;
	
	$form['page'] = $page;
	$from = $config_index["questions_numpage"]*($page-1);
	$count = $config_index["questions_numpage"];
	
	
	$id_parent = intval($_REQUEST['id_parent']);
	
	$questions = $Catalog->getSearched($from, $count, $_REQUEST['keyword'], $_REQUEST['filter'], $id_parent);
	
	$count_records = $Catalog->getSearchedCount($_REQUEST['keyword'], $_REQUEST['filter'], $id_parent);
	
	$link_arr = getAnswLinkArray($count_records, $page, $param, $count);
	
	$smarty->assign('user', $user);
	$smarty->assign('form', $form);
	$smarty->assign('count_records', $count_records);
	$smarty->assign('link_arr', $link_arr);
	$smarty->assign('questions', $questions);
	echo $smarty->fetch(TrimSlash($config["index_theme_path"])."/answ_quest_pattern.tpl");
	exit; 
}

function getExperts()
{
	global $smarty, $config, $config_index, $Catalog;
	
	if (isset($_REQUEST['page'])){
		$page = intval($_REQUEST['page']);
	}else
		$page = 1;
	
	$form['page'] = $page;
	$from = $config_index["questions_numpage"]*($page-1);
	$form['count_on_page'] = $config_index["questions_numpage"];
	
	$experts = $Catalog->getExperts($from, $form['count_on_page']);
	$count_records = $Catalog->getCountExperts();
	$link_arr = getAnswLinkArray($count_records, $page, $param, $form['count_on_page']);

	$smarty->assign("link_arr", $link_arr);
	$smarty->assign("form", $form);
	$smarty->assign("experts", $experts);
	echo $smarty->fetch(TrimSlash($config["index_theme_path"])."/answ_experts_pattern.tpl");
	exit;
}

function getExpertsAnswers()
{
	global $smarty, $config, $Catalog, $config_index;
	
	$id_user = intval($_REQUEST['id_user']);
	
	if (isset($_REQUEST['exp_ans_page'.$id_user])){
		$_SESSION['exp_ans_page'.$id_user] = intval($_REQUEST['exp_ans_page'.$id_user]);
	}	
	
	if (isset($_SESSION['exp_ans_page'.$id_user]) && intval($_SESSION['exp_ans_page'.$id_user]) > 1)
		$page = $_SESSION['exp_ans_page'.$id_user];
	else
		$page = 1;
	
	$form['page'] = $page;
	$form['id_user'] = $id_user;
	$from = $config_index["questions_numpage"]*($page-1);
	$count = $config_index["questions_numpage"];
	
	$exp_answers = $Catalog->getExpertAnswers($from, $count, $id_user);
	$count_records = $Catalog->getCountExpertAnswers($id_user);
	$link_arr = getAnswLinkArray($count_records, $page, $param, $count);
	$smarty->assign("exp_answers", $exp_answers);
	$smarty->assign("exp_link_arr", $link_arr);
	$smarty->assign("form", $form);
	echo $smarty->fetch(TrimSlash($config["index_theme_path"])."/answ_exp_answ_pattern.tpl");
	exit;
}

function ExpertsTable()
{
	global $smarty, $config, $user, $Catalog, $config_index;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	if (isset($_REQUEST['page'])){
		$page = intval($_REQUEST['page']);
	}else
		$page = 1;
	
	$form['page'] = $page;
	$from = $config_index["questions_numpage"]*($page-1);
	$count = $config_index["questions_numpage"];
	
	$experts = $Catalog->getExperts($from, $count);
	$count_records = $Catalog->getCountExperts();
	$link_arr = getAnswLinkArray($count_records, $page, $param, $count);
	
	$smarty->assign("link_arr", $link_arr);
	$smarty->assign("form", $form);
	$smarty->assign("experts", $experts);
	$smarty->display(TrimSlash($config["index_theme_path"])."/answ_experts.tpl");
	exit;
}

function msgTemplate($msg,$code=0)
{
	return "<div id='tmp_msg' >".$msg."</div><div id='tmp_code' >".$code."</div>";
}

function getAnswLinkArray($num_records, $page, $param, $max_record, $dop_param="")
{
	/// settings
	$dop_param["page_var_name"] = (isset($dop_param["page_var_name"]) && strlen($dop_param["page_var_name"]))?$dop_param["page_var_name"]:"page";
	$dop_param["left_arrow_name"] = (isset($dop_param["left_arrow_name"]) && strlen($dop_param["left_arrow_name"]))?$dop_param["left_arrow_name"]:"...";
	$dop_param["right_arrow_name"] = (isset($dop_param["right_arrow_name"]) && strlen($dop_param["right_arrow_name"]))?$dop_param["right_arrow_name"]:"...";

	$num_page = ceil($num_records/$max_record);
	if($num_page<2){
		return array();
	}
	$p_page_count = 10;
	$p_page = floor(($page-1)/$p_page_count);
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