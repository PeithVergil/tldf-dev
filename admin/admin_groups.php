<?php

/**
* Site group management(groups, groups permissions, group users).
*
* @package DatingPro
* @subpackage Admin Mode
**/

require_once '../include/config.php';
require_once '../common.php';
require_once '../include/config_admin.php';
require_once '../include/functions_auth.php';
require_once '../include/functions_admin.php';

$auth = auth_user();
login_check($auth);

IsFileAllowed($auth[0], GetRightModulePath(__FILE__), 'groups');

$sel = isset($_REQUEST['sel']) ? $_REQUEST['sel'] : '';

switch($sel)
{
	case 'add': AddGroup(); break;
	case 'edit': EditForm('edit'); break;
	case 'change': ChangeGroup(); break;
	case 'del': DelGroup(); break;
	case 'free': DelGroups(); break;
	case 'perm': PermForm(); break;
	case 'groupsperm': GroupPermissions(); break;
	case 'permchange': PermChange(); break;
	case 'user': UserForm(); break;
	case 'groupuser': UserChange(); break;
	default: ListGroup();
}

function ListGroup($err = '')
{
	global $smarty, $dbconn, $config, $config_admin, $lang, $file_name;
	
	unset($_SESSION['perm']);
	unset($_SESSION['add_perm']);
	unset($_SESSION['add_perm_count']);
	
	$file_name = 'admin_groups.php';
	
	AdminMainMenu($lang['groups']);
	
	$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
	
	$rs = $dbconn->Execute('SELECT COUNT(id) FROM '.GROUPS_TABLE);
	$num_records = $rs->fields[0];
	
	$lim_min = ($page-1) * $config_admin['group_numpage'];
	$lim_max = $config_admin['group_numpage'];
	$limit_str = ' LIMIT '.$lim_min.', '.$lim_max;
	
	$rs = $dbconn->Execute(
		'SELECT id, name, type, is_gender_group
		   FROM '.GROUPS_TABLE.'
		  WHERE type != "r"
	   ORDER BY name '.$limit_str);
	
	$i = 0;
	$group_arr = array();
	
	if ($rs->RowCount() > 0) {
		while (!$rs->EOF) {
			$group_arr[$i]['id'] = $rs->fields[0];
			$group_arr[$i]['type'] = $rs->fields[2];
			$group_arr[$i]['name'] = strval($rs->fields[1]);
			$group_arr[$i]['type_name'] = $lang['groups']['types'][strval($rs->fields[2])];
			if ($group_arr[$i]['type'] == "m") {
				$group_arr[$i]['editlink'] = 'admin_moderators.php?sel=list';
			} else {
				$group_arr[$i]['editlink'] = $file_name.'?sel=edit&amp;id='.$rs->fields[0];
			}

			$group_arr[$i]['userlink'] = './admin_users.php?group='.$rs->fields[0];
			$group_arr[$i]['is_gender_group'] = $rs->fields[3];
			$rs->MoveNext();
			$i++;
		}
		$param = $file_name.'?';
		$smarty->assign('links', GetLinkStr($num_records, $page, $param, $config_admin['group_numpage']));
		$smarty->assign('group_arr', $group_arr);
	} else {
		$smarty->assign('empty_row', '1');
	}
	
	$rs = $dbconn->Execute(
		'SELECT a.id_user FROM '.USER_GROUP_TABLE.' a
	 INNER JOIN '.GROUPS_TABLE.' b ON a.id_group = b.id
		  WHERE b.type = "f"');
	$form['payed_count'] = $rs->RowCount();
	$settings = GetSiteSettings(array('free_site'));
	$form['free_site'] = $settings['free_site'];
	$form['use_gender_membership'] = $config['use_gender_membership'];
	
	$form['err'] = $err;
	$smarty->assign('form', $form);
	
	$smarty->assign('del_groups_link', $file_name.'?sel=free');
	$smarty->assign('add_link', $file_name.'?sel=add&amp;page='.$page);
	$smarty->assign('header', $lang['groups']);
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_groups_table.tpl');
	exit;
}

function GroupPermissions($err = '')
{
	global $smarty, $dbconn, $config, $lang;
	
	$file_name = 'admin_groups.php';
	
	AdminMainMenu($lang['groups']);
	
	$rs = $dbconn->Execute('SELECT id_group, id_module FROM '.GROUP_MODULE_TABLE.' ORDER BY id_group ASC');
	
	$perm_eg = array();
	$perm_el = array();
	$perm_egh = array();
	$perm_elh = array();
	$perm_pg = array();
	$perm_pl = array();
	$perm_pgh = array();
	$perm_plh = array();
	$perm_rg = array();
	$perm_rl = array();
	$perm_rgh = array();
	$perm_rlh = array();
	$perm_tg = array();
	$perm_tl = array();
	$perm_tgh = array();
	$perm_tlh = array();
	$perm_sg = array();
	$perm_sl = array();
	
	while (!$rs->EOF) {
		$idGroup = $rs->fields[0];
		switch ($idGroup) {
			// Elite
			case MM_ELITE_GUY_ID:
				array_push($perm_eg, $rs->fields[1]);
			break;
			case MM_ELITE_LADY_ID:
				array_push($perm_el, $rs->fields[1]);
			break;
			case MM_INACT_ELITE_GUY_ID:
				array_push($perm_egh, $rs->fields[1]);
			break;
			case MM_INACT_ELITE_LADY_ID:
				array_push($perm_elh, $rs->fields[1]);
			break;
			
			// Platinum
			case MM_PLATINUM_GUY_ID:
				array_push($perm_pg, $rs->fields[1]);
			break;
			case MM_PLATINUM_LADY_ID:
				array_push($perm_pl, $rs->fields[1]);
			break;
			case MM_INACT_PLATINUM_GUY_ID:
				array_push($perm_pgh, $rs->fields[1]);
			break;
			case MM_INACT_PLATINUM_LADY_ID:
				array_push($perm_plh, $rs->fields[1]);
			break;
			
			// Regular
			case MM_REGULAR_GUY_ID:
				array_push($perm_rg, $rs->fields[1]);
			break;
			case MM_REGULAR_LADY_ID:
				array_push($perm_rl, $rs->fields[1]);
			break;
			case MM_INACT_REGULAR_GUY_ID:
				array_push($perm_rgh, $rs->fields[1]);
			break;
			case MM_INACT_REGULAR_LADY_ID:
				array_push($perm_rlh, $rs->fields[1]);
			break;
			
			// Trial
			case MM_TRIAL_GUY_ID:
				array_push($perm_tg, $rs->fields[1]);
			break;
			case MM_TRIAL_LADY_ID:
				array_push($perm_tl, $rs->fields[1]);
			break;
			case MM_INACT_TRIAL_GUY_ID:
				array_push($perm_tgh, $rs->fields[1]);
			break;
			case MM_INACT_TRIAL_LADY_ID:
				array_push($perm_tlh, $rs->fields[1]);
			break;
			
			// Signup
			case MM_SIGNUP_GUY_ID:
				array_push($perm_sg, $rs->fields[1]);
			break;
			case MM_SIGNUP_LADY_ID:
				array_push($perm_sl, $rs->fields[1]);
			break;
		}
		$rs->MoveNext();
	}
	
	$rs = $dbconn->Execute(
		'SELECT id, name
		   FROM '.MODULES_TABLE.'
		  WHERE id IN (SELECT DISTINCT(id_module) FROM '.GROUP_MODULE_TABLE.' WHERE id_group NOT IN (1,2,3,4,9))
	   ORDER BY name');
	
	$i = 0;
	$perm = array();
	
	while (!$rs->EOF) {
		$perm[$i]['id'] = $rs->fields[0];
		$perm[$i]['name'] = isset($lang['modules'][$rs->fields[1]]['name']) ? $lang['modules'][$rs->fields[1]]['name'] : $rs->fields[1];
		
		// &radic; : tick mark sign
		$allow = '&radic;';
		
		$perm[$i]['eg']['active'] = in_array($perm[$i]['id'],$perm_eg) ? $allow : '';
		$perm[$i]['el']['active'] = in_array($perm[$i]['id'],$perm_el) ? $allow : '';
		$perm[$i]['egh']['active'] = in_array($perm[$i]['id'],$perm_egh) ? $allow : '';
		$perm[$i]['elh']['active'] = in_array($perm[$i]['id'],$perm_elh) ? $allow : '';
		
		$perm[$i]['pg']['active'] = in_array($perm[$i]['id'],$perm_pg) ? $allow : '';
		$perm[$i]['pl']['active'] = in_array($perm[$i]['id'],$perm_pl) ? $allow : '';
		$perm[$i]['pgh']['active'] = in_array($perm[$i]['id'],$perm_pgh) ? $allow : '';
		$perm[$i]['plh']['active'] = in_array($perm[$i]['id'],$perm_plh) ? $allow : '';
		
		$perm[$i]['rg']['active'] = in_array($perm[$i]['id'],$perm_rg) ? $allow : '';
		$perm[$i]['rl']['active'] = in_array($perm[$i]['id'],$perm_rl) ? $allow : '';
		$perm[$i]['rgh']['active'] = in_array($perm[$i]['id'],$perm_rgh) ? $allow : '';
		$perm[$i]['rlh']['active'] = in_array($perm[$i]['id'],$perm_rlh) ? $allow : '';
		
		$perm[$i]['tg']['active'] = in_array($perm[$i]['id'],$perm_tg) ? $allow : '';
		$perm[$i]['tl']['active'] = in_array($perm[$i]['id'],$perm_tl) ? $allow : '';
		$perm[$i]['tgh']['active'] = in_array($perm[$i]['id'],$perm_tgh) ? $allow : '';
		$perm[$i]['tlh']['active'] = in_array($perm[$i]['id'],$perm_tlh) ? $allow : '';
		
		$perm[$i]['sg']['active'] = in_array($perm[$i]['id'],$perm_sg) ? $allow : '';
		$perm[$i]['sl']['active'] = in_array($perm[$i]['id'],$perm_sl) ? $allow : '';
		
		$rs->MoveNext();
		$i++;
	}
	
	/////////////////////////////////////////////
	// Limit Permission settings
	/////////////////////////////////////////////
	
	$rs = $dbconn->Execute('SELECT id_group, id_permission, permission_count FROM '.GROUPS_PERMISSIONS_TABLE.' ORDER BY id_group ASC');
	
	$add_perm_eg = array();
	$add_perm_el = array();
	$add_perm_egh = array();
	$add_perm_elh = array();
	$add_perm_pg = array();
	$add_perm_pl = array();
	$add_perm_pgh = array();
	$add_perm_plh = array();
	$add_perm_rg = array();
	$add_perm_rl = array();
	$add_perm_rgh = array();
	$add_perm_rlh = array();
	$add_perm_tg = array();
	$add_perm_tl = array();
	$add_perm_tgh = array();
	$add_perm_tlh = array();
	$add_perm_sg = array();
	$add_perm_sl = array();
	
	$perm_count = array();
	
	while (!$rs->EOF) {
		$idGroup = $rs->fields[0];
		$idPerm	 = $rs->fields[1];
		$pcount	 = $rs->fields[2];
		
		$perm_count[$idGroup][$idPerm] = $pcount;
		
		switch ($idGroup) {
			// Elite
			case MM_ELITE_GUY_ID:
				array_push($add_perm_eg, $rs->fields[1]);
			break;
			case MM_ELITE_LADY_ID:
				array_push($add_perm_el, $rs->fields[1]);
			break;
			case MM_INACT_ELITE_GUY_ID:
				array_push($add_perm_egh, $rs->fields[1]);
			break;
			case MM_INACT_ELITE_LADY_ID:
				array_push($add_perm_elh, $rs->fields[1]);
			break;
			
			// Platinum
			case MM_PLATINUM_GUY_ID:
				array_push($add_perm_pg, $rs->fields[1]);
			break;
			case MM_PLATINUM_LADY_ID:
				array_push($add_perm_pl, $rs->fields[1]);
			break;
			case MM_INACT_PLATINUM_GUY_ID:
				array_push($add_perm_pgh, $rs->fields[1]);
			break;
			case MM_INACT_PLATINUM_LADY_ID:
				array_push($add_perm_plh, $rs->fields[1]);
			break;
			
			// Regular
			case MM_REGULAR_GUY_ID:
				array_push($add_perm_rg, $rs->fields[1]);
			break;
			case MM_REGULAR_LADY_ID:
				array_push($add_perm_rl, $rs->fields[1]);
			break;
			case MM_INACT_REGULAR_GUY_ID:
				array_push($add_perm_rgh, $rs->fields[1]);
			break;
			case MM_INACT_REGULAR_LADY_ID:
				array_push($add_perm_rlh, $rs->fields[1]);
			break;
			
			// Trial
			case MM_TRIAL_GUY_ID:
				array_push($add_perm_tg, $rs->fields[1]);
			break;
			case MM_TRIAL_LADY_ID:
				array_push($add_perm_tl, $rs->fields[1]);
			break;
			case MM_INACT_TRIAL_GUY_ID:
				array_push($add_perm_tgh, $rs->fields[1]);
			break;
			case MM_INACT_TRIAL_LADY_ID:
				array_push($add_perm_tlh, $rs->fields[1]);
			break;
			
			// Signup
			case MM_SIGNUP_GUY_ID:
				array_push($add_perm_sg, $rs->fields[1]);
			break;
			case MM_SIGNUP_LADY_ID:
				array_push($add_perm_sl, $rs->fields[1]);
			break;
		}
		$rs->MoveNext();
	}
	
	$rs = $dbconn->Execute(
		'SELECT id, permission_name
		   FROM '.PERMISSIONS_TABLE.'
		  WHERE id IN (SELECT DISTINCT(id_permission) FROM '.GROUPS_PERMISSIONS_TABLE.' WHERE id_group NOT IN (1,2,3,4,9))
	   ORDER BY permission_name');
	
	$i = 0;
	$add_perm = array();
	
	while (!$rs->EOF) {
		$addPermId				= $rs->fields[0];
		$add_perm[$i]['id']		= $rs->fields[0];
		$add_perm[$i]['name']	= $lang['groups']['permissions_name'][$rs->fields[1]];
		
		$add_perm[$i]['eg']['value']	= in_array($addPermId, $add_perm_eg)	? $perm_count[MM_ELITE_GUY_ID][$addPermId] : '';
		$add_perm[$i]['el']['value']	= in_array($addPermId, $add_perm_el)	? $perm_count[MM_ELITE_LADY_ID][$addPermId] : '';
		$add_perm[$i]['egh']['value']	= in_array($addPermId, $add_perm_egh)	? $perm_count[MM_INACT_ELITE_GUY_ID][$addPermId] : '';
		$add_perm[$i]['elh']['value']	= in_array($addPermId, $add_perm_elh)	? $perm_count[MM_INACT_ELITE_LADY_ID][$addPermId] : '';
		
		$add_perm[$i]['pg']['value']	= in_array($addPermId, $add_perm_pg)	? $perm_count[MM_PLATINUM_GUY_ID][$addPermId] : '';
		$add_perm[$i]['pl']['value']	= in_array($addPermId, $add_perm_pl)	? $perm_count[MM_PLATINUM_LADY_ID][$addPermId] : '';
		$add_perm[$i]['pgh']['value']	= in_array($addPermId, $add_perm_pgh)	? $perm_count[MM_INACT_PLATINUM_GUY_ID][$addPermId] : '';
		$add_perm[$i]['plh']['value']	= in_array($addPermId, $add_perm_plh)	? $perm_count[MM_INACT_PLATINUM_LADY_ID][$addPermId] : '';
		
		$add_perm[$i]['rg']['value']	= in_array($addPermId, $add_perm_rg)	? $perm_count[MM_REGULAR_GUY_ID][$addPermId] : '';
		$add_perm[$i]['rl']['value']	= in_array($addPermId, $add_perm_rl)	? $perm_count[MM_REGULAR_LADY_ID][$addPermId] : '';
		$add_perm[$i]['rgh']['value']	= in_array($addPermId, $add_perm_rgh)	? $perm_count[MM_INACT_REGULAR_GUY_ID][$addPermId] : '';
		$add_perm[$i]['rlh']['value']	= in_array($addPermId, $add_perm_rlh)	? $perm_count[MM_INACT_REGULAR_LADY_ID][$addPermId] : '';
		
		$add_perm[$i]['tg']['value']	= in_array($addPermId, $add_perm_tg)	? $perm_count[MM_TRIAL_GUY_ID][$addPermId] : '';
		$add_perm[$i]['tl']['value']	= in_array($addPermId, $add_perm_tl)	? $perm_count[MM_TRIAL_LADY_ID][$addPermId] : '';
		$add_perm[$i]['tgh']['value']	= in_array($addPermId, $add_perm_tgh)	? $perm_count[MM_INACT_TRIAL_GUY_ID][$addPermId] : '';
		$add_perm[$i]['tlh']['value']	= in_array($addPermId, $add_perm_tlh)	? $perm_count[MM_INACT_TRIAL_LADY_ID][$addPermId] : '';
		
		$add_perm[$i]['sg']['value']	= in_array($addPermId, $add_perm_sg)	? $perm_count[MM_SIGNUP_GUY_ID][$addPermId] : '';
		$add_perm[$i]['sl']['value']	= in_array($addPermId, $add_perm_sl)	? $perm_count[MM_SIGNUP_LADY_ID][$addPermId] : '';
		
		$rs->MoveNext();
		$i++;
	}
	
	/////////////////////////////////////////////
	// More Available Modules
	/////////////////////////////////////////////
	
	$rs = $dbconn->Execute(
		'SELECT name
		   FROM '.MODULES_TABLE.'
		  WHERE id NOT IN (SELECT DISTINCT(id_module) FROM '.GROUP_MODULE_TABLE.' WHERE id_group NOT IN (1,2,3,4,9))
	   ORDER BY name');
	
	$more_modules = '';
	while (!$rs->EOF) {
		$module_name = isset($lang['modules'][$rs->fields[0]]['name']) ? $lang['modules'][$rs->fields[0]]['name'] : $rs->fields[0];
		$pos = strpos($module_name, 'Admin');
		if ($pos === false) {
			if ($more_modules != '') {
				$more_modules = $more_modules.'<br>';
			}
			$more_modules = $more_modules.$module_name;
		}
		$rs->MoveNext();
	}
	
	$form['action'] = $file_name;
	$form['err'] = $err;
	
	$smarty->assign('form', $form);
	$smarty->assign('perm', $perm);
	$smarty->assign('add_perm', $add_perm);
	$smarty->assign('more_modules', $more_modules);
	$smarty->assign('header', $lang['groups']);
	$smarty->assign('button', $lang['button']);
	
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_groups_perm.tpl');
	exit;
}

function EditForm($par, $err = '')
{
	global $smarty, $dbconn, $config, $lang;
	
	$file_name = 'admin_groups.php';
	
	AdminMainMenu($lang['groups']);
	
	$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
	
	if ($err) {
		$form['err'] = $err;
		$data['name'] = FormFilter($_POST['name']);
		$data['add_default'] = $_POST['add_default'];
	}
	
	if ($par != 'add')
	{
		global $id;
		
		if (!$id) $id = $_GET['id'];
		
		$rs = $dbconn->Execute('SELECT type FROM '.GROUPS_TABLE.' WHERE id = ?', array($id));
		$data['type'] = $rs->fields[0];
		
		if ($data['type'] == 'd') {
			$data['add_default'] = 'd';
		} elseif ($data['type'] == 't'){
			$data['add_default_disable'] = 1;
		}
		
		if (!$err)
		{
			if (!$id) {
				ListGroup();
				return;
			}
			
			$rs = $dbconn->Execute('SELECT name, addable FROM '.GROUPS_TABLE.' WHERE id = ?', array($id));
			$row = $rs->GetRowAssoc(false);
			
			$data['name'] = FormFilter($row['name']);
			
			$strSQL =
				'SELECT a.id, a.name
				   FROM '.MODULES_TABLE.' a
			 INNER JOIN '.GROUP_MODULE_TABLE.' b ON a.id = b.id_module
				  WHERE b.id_group = ?
			   ORDER BY a.name';
			
			$rs = $dbconn->Execute($strSQL, array($id));
			
			if ($rs->RowCount() > 0) {
				$i = $j = 0;
				$data['modulestr'] = '';
				while (!$rs->EOF) {
					$data['modulestr'] .= '<input type="hidden" name="perm['.$i.']" value="'.$rs->fields[0].'" />';
					$data['modulestr'] .= $lang['modules'][$rs->fields[1]]['name'].'<br>';
					$rs->MoveNext();
					$i++;
				}
				//$data['modulestr'] = substr($data['modulestr'], 0, strlen($data['modulestr'])-4);
			}
			
			$strSQL =
				'SELECT DISTINCT gp.id, gp.permission_count, p.permission_name, gp.id_permission
				   FROM '.GROUPS_PERMISSIONS_TABLE.' gp
			  LEFT JOIN '.PERMISSIONS_TABLE.' p ON p.id = gp.id_permission
				  WHERE gp.id_group = ?
			   GROUP BY gp.id
			   ORDER BY gp.id';
			$rs = $dbconn->Execute($strSQL, array($id));
			
			if ($rs->RowCount() > 0) {
				$i = 0;
				$data['add_perm_str'] = '';
				while (!$rs->EOF) {
					$data['add_perm_str'] .= '<input type="hidden" name="add_perm['.$i.']" value="'.$rs->fields[3].'" />';
					$data['add_perm_str'] .= '<input type="hidden" name="add_perm_count['.$i.']" value="'.$rs->fields[1].'">';
					$data['add_perm_str'] .= $lang['groups']['permissions_name'][$rs->fields[2]].':&nbsp;'.$rs->fields[1].'&nbsp;'.$lang['groups']['permissions_value_name'][$rs->fields[2]].'<br>';
					$rs->MoveNext();
					$i++;
				}
			}
		}
		
		$form['hiddens']  = '<input type="hidden" name="sel" value="change" />';
		$form['hiddens'] .= '<input type="hidden" name="e" value="1" />';
		$form['hiddens'] .= '<input type="hidden" name="page" value="'.$page.'" />';
		$form['hiddens'] .= '<input type="hidden" name="id" value="'.$id.'" />';
		$strSQL = 'SELECT COUNT(*) FROM '.GROUPS_TABLE.' WHERE type = "d" AND id != "'.$id.'"';
	} else {
		$id = '';
		$form['hiddens']  = '<input type="hidden" name="sel" value="add" />';
		$form['hiddens'] .= '<input type="hidden" name="e" value="1" />';
		$form['hiddens'] .= '<input type="hidden" name="page" value="'.$page.'" />';
		$strSQL = 'SELECT COUNT(*) FROM '.GROUPS_TABLE.' WHERE type = "d"';
	}
	
	$rs = $dbconn->Execute($strSQL);
	
	// disabled by ralf
	// reason: checkbox for default group was disabled, thus posting no value and the default group
	// was automatically changed into a paid group, which was simply wrong.
	/*
	if ($rs->fields[0] > 0){
		$data['add_default_disable'] = '1';
	}
	*/
	$sess_perm = array();

	if (isset($_SESSION['perm'])) {
		if ($_SESSION['perm']) {
			$sess_perm = $_SESSION['perm'];
		}
		$data['modulestr'] = '';
		$perm_id = implode(',', $sess_perm);
		$rs = $dbconn->Execute('SELECT id, name FROM '.MODULES_TABLE.' WHERE id IN ('.$perm_id.') ORDER BY name');
		if ($rs->RowCount() > 0) {
			$i = $j = 0;
			while (!$rs->EOF) {
				$data['modulestr'] .= '<input type="hidden" name="perm['.$i.']" value="'.$rs->fields[0].'" />';
				$data['modulestr'] .= $lang['modules'][$rs->fields[1]]['name'].'<br>';
				$rs->MoveNext();
				$i++;
			}
			//$data['modulestr'] = substr($data['modulestr'], 0, strlen($data['modulestr'])-4);
		}
	}
	
	$sess_add_perm = array();
	
	if (isset($_SESSION['add_perm'])) {
		if ($_SESSION['add_perm']) {
			$sess_add_perm = $_SESSION['add_perm'];
		}
		
		$data['add_perm_str'] = '';
		$add_perm_id = implode(',', $sess_add_perm);
		
		$strSQL =
			'SELECT DISTINCT gp.id, gp.permission_count, p.permission_name, gp.id_permission
			   FROM '.GROUPS_PERMISSIONS_TABLE.' gp
		  LEFT JOIN '.PERMISSIONS_TABLE.' p ON p.id = gp.id_permission
			  WHERE gp.id IN ('.$add_perm_id.')
			  GROUP BY gp.id
			  ORDER BY gp.id';
		$rs = $dbconn->Execute($strSQL);
		if ($rs->RowCount() > 0) {
			$i = 0;
			while (!$rs->EOF) {
				$data['add_perm_str'] .= '<input type="hidden" name="add_perm['.$i.']" value="'.$rs->fields[3].'" />';
				$data['add_perm_str'] .= '<input type="hidden" name="add_perm_count['.$i.']" value="'.$rs->fields[1].'" />';
				$data['add_perm_str'] .= $lang['groups']['permissions_name'][$rs->fields[2]].':&nbsp;'.$rs->fields[1].'&nbsp;'.$lang['groups']['permissions_value_name'][$rs->fields[2]].'<br>';
				$rs->MoveNext();
				$i++;
			}
		}
	}

	$data['permlink']	= $file_name.'?sel=perm&amp;id='.$id;
	$form['delete']		= $file_name.'?sel=del&amp;id='.$id.'&amp;page='.$page;
	$form['back']		= $file_name.'?page='.$page;
	$form['action']		= $file_name;
	$form['par']		= $par;
	$form['confirm']	= $lang['confirm']['groups'];
	
	$form['use_gender_membership'] = $config['use_gender_membership'];

	$smarty->assign('err', $lang['err']);
	$smarty->assign('data', $data);
	$smarty->assign('form', $form);
	$smarty->assign('header', $lang['groups']);
	$smarty->assign('button', $lang['button']);

	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_groups_form.tpl');
	exit;
}

function AddGroup()
{
	global $dbconn, $lang, $e;
	
	$name = isset($_POST['name']) ? FormFilter($_POST['name']) : '';
	$perm = isset($_POST['perm']) ? $_POST['perm'] : '';
	
	if (!isset($perm)) {
		$perm = $_SESSION['perm'];
	}
	
	if (strlen($name) < 1) {
		$str_invl = '<br>'.$lang['groups']['name'];
		$err = isset($e) ? $lang['err']['invalid_fields'].$str_invl : '';
		EditForm('add', $err);
		return;
	}
	
	$add_default = isset($_POST['add_default']) ? $_POST['add_default'] : '';
	
	if (intval($add_default) == 1) {
		$def_type = '"d"';
	} else {
		$def_type = '"f"';
	}
	
	$ins_group = $dbconn->Execute('INSERT INTO '.GROUPS_TABLE.' (name, type) VALUES ("'.$name.'", '.$def_type.')');
	
	if ($ins_group) {
		if ($def_type == '"f"') {
			$dbconn->Execute('UPDATE '.SETTINGS_TABLE.' SET value = "0" WHERE name = "free_site"');
		}
		$rs = $dbconn->Execute('SELECT MAX(id) FROM '.GROUPS_TABLE);
		$id = $rs->fields[0];
		for ($i = 0; $i <= count($perm); $i++) {
			if (isset($perm[$i]) && $perm[$i] != 0) {
				$dbconn->Execute('INSERT INTO '.GROUP_MODULE_TABLE.' (id_group, id_module) VALUES ("'.$id.'", "'.$perm[$i].'")');
			}
		}
		$add_perm = isset($_POST['add_perm']) ? $_POST['add_perm'] : '';
		$add_perm_count = isset($_POST['add_perm_count']) ? $_POST['add_perm_count'] : '';
		if (sizeof($add_perm) > 0) {
			for ($i = 0; $i <= sizeof($add_perm); $i++) {
				if ((isset($add_perm_count[$i])) && ($add_perm_count[$i]>0)) {
					$dbconn->Execute(
						'INSERT INTO '.GROUPS_PERMISSIONS_TABLE.' (id_group, id_permission, permission_count)
							VALUES ("'.$id.'", "'.$add_perm[$i].'", "'.$add_perm_count[$i].'")');
				}
			}
		}
	}
	unset($_SESSION['perm']);
	unset($_SESSION['add_perm']);
	unset($_SESSION['add_perm_count']);
	ListGroup();
	return;
}

function ChangeGroup()
{
	global $dbconn, $lang, $e;

	$id = intval($_POST['id']);
	
	if (!$id) {
		ListGroup();
		return;
	}
	
	$name = FormFilter($_POST['name']);
	
	if (isset($_POST['perm'])) {
		$perm = $_POST['perm'];
	} elseif (isset($_SESSION['perm'])) {
		$perm = $_SESSION['perm'];
	} else {
		$perm = array();
	}
	
	if (isset($_POST['add_perm'])) {
		$add_perm = $_POST['add_perm'];
	} elseif (isset($_SESSION['add_perm'])) {
		$add_perm = $_SESSION['add_perm'];
	} else {
		$add_perm = array();
	}
	
	if (isset($_POST['add_perm_count'])) {
		$add_perm_count = $_POST['add_perm_count'];
	} elseif (isset($_SESSION['add_perm_count'])) {
		$add_perm_count = $_SESSION['add_perm_count'];
	} else {
		$add_perm_count = array();
	}
	
	$add_default = isset($_POST['add_default']) ? $_POST['add_default'] : 0;
	
	if (intval($add_default) == 1) {
		$def_type = 'd';
		$addable = '0';
	} else {
		$def_type = 'f';
		$addable = isset($_POST['addable']) ? $_POST['addable'] : '0';
	}

	$rs = $dbconn->Execute('SELECT type FROM '.GROUPS_TABLE.' WHERE id = ?', array($id));
	$type = $rs->fields[0];
	
	if ($type == 'r') {
		unset($_SESSION['perm']);
		unset($_SESSION['add_perm']);
		unset($_SESSION['add_perm_count']);
		ListGroup();
		return;
	}
	
	if ($type == 't' || $type == 'b'){
		$def_type = $type;
	}
	
	if ($type != 'g') {
		if (strlen($name) < 1) {
			$str_invl = '<br>'.$lang['groups']['name'];
			if ($e) {
				$err = $lang['err']['invalid_fields'].$str_invl;
			}
			EditForm('edit', $err);
		}
		$rs = $dbconn->Execute('SELECT COUNT(*) FROM '.GROUPS_TABLE.' WHERE name = ? AND id <> ?', array($name, $id));
		if ($rs->fields[0] > 0) {
			if ($e) {
				$err = $lang['err']['exists_group'];
			}
			EditForm('edit', $err);
			return;
		}
		
		$dbconn->Execute('UPDATE '.GROUPS_TABLE.' SET name = "'.$name.'", type = "'.$def_type.'" WHERE id = "'.$id.'"');
	}
	
	$dbconn->Execute('DELETE FROM '.GROUP_MODULE_TABLE.' WHERE id_group = ?', array($id));
	
	for ($i = 0; $i < count($perm); $i++) {
		if ($perm[$i] != 0) {
			$dbconn->Execute('INSERT INTO '.GROUP_MODULE_TABLE.' (id_group, id_module) VALUES ("'.$id.'", "'.$perm[$i].'")');
		}
	}
	
	$dbconn->Execute('DELETE FROM '.GROUPS_PERMISSIONS_TABLE.' WHERE id_group = ?', array($id));
	
	if (count($add_perm) > 0) {
		foreach ($add_perm as $key => $value) {
			if ($add_perm_count[$key] > 0) {
				$dbconn->Execute(
					'INSERT INTO '.GROUPS_PERMISSIONS_TABLE.' (id_group, id_permission, permission_count)
						VALUES ("'.$id.'", "'.$add_perm[$key].'", "'.$add_perm_count[$key].'")');
			}
		}
	}
	
	unset($_SESSION['perm']);
	unset($_SESSION['add_perm']);
	unset($_SESSION['add_perm_count']);
	
	ListGroup();
	return;
}

function DelGroup($id = '')
{
	global $dbconn;
	
	if ($id == '') {
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
	}
	
	if ($id < 1) {
		ListGroup();
		return;
	}
	
	unset($_SESSION['perm']);
	unset($_SESSION['add_perm']);
	unset($_SESSION['add_perm_count']);

	$dbconn->Execute('DELETE FROM '.GROUPS_TABLE.' WHERE id = ?', array($id));
	
	$rs = $dbconn->Execute('SELECT id FROM '.GROUP_PERIOD_TABLE.' WHERE id_group = ?', array($id));
	
	while (!$rs->EOF) {
		$dbconn->Execute('DELETE FROM '.BILLING_USER_PERIOD_TABLE.' WHERE id_group_period = ?', array($rs->fields[0]));
		$rs->MoveNext();
	}
	
	$dbconn->Execute('DELETE FROM '.GROUP_PERIOD_TABLE.' WHERE id_group = ?', array($id));
	$dbconn->Execute('DELETE FROM '.BILLING_REQUESTS_TABLE.' WHERE id_group = ?', array($id));
	$dbconn->Execute('DELETE FROM '.BILLING_ENTRY_TABLE.' WHERE id_group = ?', array($id));
	$dbconn->Execute('DELETE FROM '.GROUP_MODULE_TABLE.' WHERE id_group = ?', array($id));
	
	$rs = $dbconn->Execute('SELECT id FROM '.GROUPS_TABLE.' WHERE type = "d"');
	$default_group = $rs->fields[0];
	
	$rs = $dbconn->Execute('SELECT id FROM '.USER_GROUP_TABLE.' WHERE id_group = ?', array($id));
	
	while (!$rs->EOF) {
		$dbconn->Execute('UPDATE '.USER_GROUP_TABLE.' SET id_group = "'.$default_group.'" WHERE id = ?', array($rs->fields[0]));
		$rs->MoveNext();
	}
	
	$rs = $dbconn->Execute('SELECT id FROM '.GROUPS_TABLE.' WHERE type = "f"');
	
	if (!$rs->RowCount()) {
		$dbconn->Execute('UPDATE '.SETTINGS_TABLE.' SET value = "0" WHERE name = "show_users_group_str"');
		$dbconn->Execute('UPDATE '.SETTINGS_TABLE.' SET value = "1" WHERE name = "free_site"');
	}
	
	$dbconn->Execute('DELETE FROM '.GROUPS_PERMISSIONS_TABLE.' WHERE id_group = ?', array($id));
	
	ListGroup();
	return;
}

function DelGroups()
{
	#global $dbconn;
	
	// feature disabled for TLDF
	ListGroup('This feature has been disabled for ThaiLadyDateFinder');
	return;
	
	/*
	$rs = $dbconn->Execute('SELECT id FROM '.GROUPS_TABLE.' WHERE type = "f" AND is_gender_group != "1"');
	while (!$rs->EOF) {
		DelGroup($rs->fields[0]);
		$rs->MoveNext();
	}
	
	ListGroup();
	return;
	*/
}

function PermForm($err = '')
{
	global $smarty, $dbconn, $config, $lang;
	
	$file_name = 'admin_groups.php';
	
	AdminMainMenu($lang['groups'], '1');
	$id = intval($_GET['id']);
	
	$sess_perm = array();
	$sess_add_perm = array();
	$sess_add_perm_count = array();
	
	if (isset($_SESSION['perm'])) {
		$sess_perm = $_SESSION['perm'];
	} else {
		if ($id) {
			$rs = $dbconn->Execute('SELECT id_module FROM '.GROUP_MODULE_TABLE.' WHERE id_group = "'.$id.'"');
			$i = 0;
			while (!$rs->EOF) {
				$sess_perm[$i] = $rs->fields[0];
				$rs->MoveNext();
				$i++;
			}
		}
	}
	
	if (isset($_SESSION['add_perm'])) {
		$sess_add_perm = $_SESSION['add_perm'];
		$sess_add_perm_count = $_SESSION['add_perm_count'];
	} else {
		if ($id) {
			$strSQL = 
				'SELECT id_permission, permission_count
				   FROM '.GROUPS_PERMISSIONS_TABLE.'
				  WHERE id_group = "'.$id.'"
				  ORDER BY id_permission';
#				  GROUP BY id
#				  ORDER BY id ';
			$rs = $dbconn->Execute($strSQL);
			$i = 0;
			while (!$rs->EOF) {
				$sess_add_perm[$i] = $rs->fields[0];
				$sess_add_perm_count[$i] = $rs->fields[1];
				$rs->MoveNext();
				$i++;
			}
		}
	}
	
	if ($id > 0) {
		$rs = $dbconn->Execute('SELECT name FROM '.GROUPS_TABLE.' WHERE id = "'.$id.'"');
		$data['groupname'] = $rs->fields[0];
	} else {
		$data['groupname'] = $lang['groups']['default_group'];
	}
	
	$rs = $dbconn->Execute('SELECT id, name FROM '.MODULES_TABLE.' ORDER BY name');
	
	$i = 0;
	$perm = array();
	
	while (!$rs->EOF) {
		$perm[$i]['id'] = $rs->fields[0];
		$perm[$i]['name'] = isset($lang['modules'][$rs->fields[1]]['name']) ? $lang['modules'][$rs->fields[1]]['name'] : $rs->fields[1];
		$perm[$i]['comment'] = $lang['modules'][$rs->fields[1]]['comment'];
		$perm[$i]['checked'] = in_array($perm[$i]['id'],$sess_perm) ? '1' : '0';
		$rs->MoveNext();
		$i++;
	}
	
	$strSQL =
		'SELECT DISTINCT p.id, p.permission_name, gp.id, gp.permission_count
		   FROM '.PERMISSIONS_TABLE.' p
	  LEFT JOIN '.GROUPS_PERMISSIONS_TABLE.' gp ON (gp.id_permission = p.id AND gp.id_group = "'.$id.'")
		  GROUP BY p.id
		  ORDER BY p.id';
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$add_perm = array();
	while (!$rs->EOF) {
		$add_perm[$i]['id'] = $rs->fields[0];
		$add_perm[$i]['name'] = $lang['groups']['permissions_name'][$rs->fields[1]];
		$add_perm[$i]['value_name'] = $lang['groups']['permissions_value_name'][$rs->fields[1]];
		$add_perm[$i]['comment'] = $lang['groups']['permissions_comment'][$rs->fields[1]];
		if ($rs->fields[2] != null) {
			$add_perm[$i]['active'] = 1;
			$add_perm[$i]['value'] = $rs->fields[3];
		} else {
			$add_perm[$i]['active'] = 0;
			$add_perm[$i]['value'] = 0;
		}
		$rs->MoveNext();
		$i++;
	}
	
	$form['action'] = $file_name;
	$form['err'] = $err;
	
	$smarty->assign('data', $data);
	$smarty->assign('perm', $perm);
	$smarty->assign('add_perm', $add_perm);
	$smarty->assign('form', $form);
	$smarty->assign('header', $lang['groups']);
	$smarty->assign('button', $lang['button']);
	
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_groups_perm_form.tpl');
	exit;
}

function PermChange()
{
	unset($_SESSION['perm']);
	unset($_SESSION['add_perm']);
	unset($_SESSION['add_perm_count']);
	
	$perm = $_POST['perm'];
	$add_perm = $_POST['add_perm'];
	$add_perm_count = $_POST['count_add_perm'];
	
	$_SESSION['perm'] = $perm;
	$_SESSION['add_perm'] = $add_perm;
	$_SESSION['add_perm_count'] = $add_perm_count;
	
	echo '<script>window.close();window.opener.focus();</script>';
	exit;
}

function UserForm($err = '')
{
	global $smarty, $dbconn, $config, $page, $lang;
	
	$file_name = 'admin_groups.php';
	
	AdminMainMenu($lang['groups']);
	
	$page = $_POST['page'] ? $_POST['page'] : $_GET['page'];
	if (!intval($page)) {
		$page = 1;
	}
	
	$id = intval($_GET['id']) ? intval($_GET['id']) : intval($_POST['id']);
	
	$search = strval(strip_tags($_POST['search']));
	$s_type = intval($_POST['s_type']);
	
	if (!$search) {
		$search = $lang['groups']['initial_word'];
	}
	
	// search
	if (strval($search)) {
		$search = strip_tags($search);
		switch ($s_type) {
			case '1': $search_str = ' login LIKE "'.$search.'%"'; break;
			case '2': $search_str = ' fname LIKE "%'.$search.'%"'; break;
			case '3': $search_str = ' sname LIKE "%'.$search.'%"'; break;
			case '4': $search_str = ' email LIKE "%'.$search.'%"'; break;
			default: $search_str = ' login LIKE "'.$search.'%"';
		}
	}
	
	$search_str .= ' AND root_user != "1" AND guest_user != "1"';
	
	$search_hiddens  = '<input type="hidden" name="id" value="'.$id.'" />';
	$search_hiddens .= '<input type="hidden" name="page" value="'.$page.'" />';
	$search_hiddens .= '<input type="hidden" name="IncSUsers" value="" />';
	$search_hiddens .= '<input type="hidden" name="sel" value="user" />';
	
	// search form
	$types = array();
	for ($i = 0; $i < 4; $i++) {
		if ($s_type == ($i+1)) {
			$types[$i]['sel'] = '1';
		}
		$types[$i]['value'] = $lang['users']['type_'.($i+1)];
	}
	
	$smarty->assign('types', $types);
	$smarty->assign('search', $search);
	$smarty->assign('search_hiddens', $search_hiddens);
	
	// select group type
	$rs = $dbconn->Execute('SELECT type FROM '.GROUPS_TABLE.' WHERE id = "'.$id.'"');
	if ($rs->fields[0] == 'r' || $rs->fields[0] == 'g') {
		$smarty->assign('root', '1');
	}
	
	// group name
	$rs = $dbconn->Execute('SELECT name FROM '.GROUPS_TABLE.' WHERE id = "'.$id.'"');
	$form['groupname'] = $rs->fields[0];
	
	// group users (who are in group)
	// if something coming from search form
	if ($_POST['search'] && $_POST['IncSUsers']) {
		$strSQL =
			'SELECT a.id, CONCAT(a.sname, " ", a.fname, " (", a.login, ")") AS username
			   FROM '.USERS_TABLE.' a
			  WHERE a.id IN ('.substr($_POST['IncSUsers'], 0, -2).')
			  GROUP BY a.id
			  ORDER BY username';
	} else {
		$strSQL =
			'SELECT a.id, CONCAT(a.sname, " ", a.fname, " (", a.login, ")") AS username
			   FROM '.USERS_TABLE.' a
		 INNER JOIN '.USER_GROUP_TABLE.' b ON b.id_user = a.id
			  WHERE b.id_group = "'.$id.'"
			  GROUP BY a.id
			  ORDER BY username';
	}
	
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$gusers_arr = array();
	while (!$rs->EOF) {
		$gusers_arr[$i]['value'] = $rs->fields[0];
		$gusers_arr[$i]['name'] = $rs->fields[1];
		$all_str .= $rs->fields[0].', ';
		$rs->MoveNext();
		$i++;
	}
	
	// make a list of old users
	$strSQL =
		'SELECT a.id
		   FROM '.USERS_TABLE.' a
	 INNER JOIN '.USER_GROUP_TABLE.' b ON b.id_user = a.id
		  WHERE b.id_group = "'.$id.'"';
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$goldusers_arr = array();
	while (!$rs->EOF) {
		$goldusers_arr[$i]['value'] = $rs->fields[0];
		$rs->MoveNext();
		$i++;
	}
	
	if (strval($all_str) == '') {
		$in_str = '';
	} else {
		$in_str = ' AND a.id NOT IN ('.substr($all_str, 0, -2).')';
	}
	
	// all users (who are not in group and content search str)
	$strSQL =
		'SELECT a.id, CONCAT(a.sname, " ", a.fname, " (", a.login, ")") AS username
		   FROM '.USERS_TABLE.' a
		  WHERE '.$search_str.' '.$in_str.'
		  GROUP BY a.id';
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$allusers_arr = array();
	while (!$rs->EOF) {
		$allusers_arr[$i]['value'] = $rs->fields[0];
		$allusers_arr[$i]['name'] = $rs->fields[1];
		$rs->MoveNext();
		$i++;
	}
	
	$form['action'] = $file_name;
	$form['err'] = $err;
	
	$form['hiddens']  = '<input type="hidden" name="id" value="'.$id.'" />';
	$form['hiddens'] .= '<input type="hidden" name="sel" value="groupuser" />';
	$form['hiddens'] .= '<input type="hidden" name="page" value="'.$page.'" />';
	$form['back'] = $file_name.'?page='.$page;
	
	$smarty->assign('allusers_arr', $allusers_arr);
	$smarty->assign('gusers_arr', $gusers_arr);
	$smarty->assign('goldusers_arr', $goldusers_arr);
	
	$smarty->assign('form', $form);
	$smarty->assign('header', $lang['groups']);
	$smarty->assign('button', $lang['button']);
	
	$smarty->display(TrimSlash($config['admin_theme_path']).'/admin_groups_users_form.tpl');
	exit;
}

function UserChange()
{
	global $dbconn;
	
	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : null;
	
	if (!$id) {
		ListGroup();
		return;
	}
	
	$delete_arr = array();
	$values_arr = array();
	$prevUsers = is_array($_POST['prevusers']) ? array_unique($_POST['prevusers']) : '';
	$IncUsers = substr($_POST['IncUsers'], 0, -1);
	$nextUsers = explode(',', $IncUsers);
	$nextUsers = is_array($nextUsers) ? array_unique($nextUsers) : '';
	
	if (!is_array($prevUsers)) $prevUsers = array();
	if (!is_array($nextUsers)) $nextUsers = array();
	
	if (is_array($nextUsers) && count($nextUsers) == 0 && is_array($prevUsers) && count($prevUsers) == 0) {
		ListGroup();
		return;
	}
	
	// root and guest user
	$root_arr = array();
	$rs = $dbconn->Execute('SELECT id FROM '.USERS_TABLE.' WHERE root_user = "1" OR guest_user = "1"');
	while (!$rs->EOF) {
		array_push($root_arr, $rs->fields[0]);
		$rs->MoveNext();
	}
	
	// default groups
	$d_arr = array();
	$rs = $dbconn->Execute('SELECT id FROM '.GROUPS_TABLE.' WHERE type = "d"');
	while (!$rs->EOF) {
		array_push($d_arr, $rs->fields[0]);
		$rs->MoveNext();
	}
	
	for ($i = 0; $i < count($prevUsers); $i++) {
		// if element not in array (old user not in new list) delete him from table
		if (!in_array($prevUsers[$i], $nextUsers) && !in_array($prevUsers[$i],$root_arr) && $prevUsers[$i] != 0) {
			for ($j = 0; $j < count($d_arr); $j++) {
				array_push($values_arr, ' ("'.$prevUsers[$i].'", "'.$d_arr[$j].'")');	 //// add in "d" groups
			}
			array_push($delete_arr, $prevUsers[$i]);
		}
	}
	
	for ($i = 0; $i < count($nextUsers); $i++) {
		// if element not in array (new user not in old list) add him into table
		if (!in_array($nextUsers[$i], $prevUsers) && !in_array($nextUsers[$i],$root_arr) && $nextUsers[$i] != 0) {
			array_push($delete_arr, $nextUsers[$i]);
			array_push($values_arr, ' ("'.$nextUsers[$i].'", "'.$id.'")');
			$dbconn->Execute('INSERT INTO '.USER_GROUP_TABLE.' (id_user, id_group) VALUES ("'.$nextUsers[$i].'", "'.$id.'")');
		}
	}
	
	$values_str = implode(', ', $values_arr);
	$delete_str = implode(', ', $delete_arr);
	
	if (strlen($delete_str) > 0) {
		$dbconn->Execute('DELETE FROM '.USER_GROUP_TABLE.' WHERE id_user IN ('.$delete_str.')');
	}
	
	if (strlen($values_str) > 0) {
		$dbconn->Execute('INSERT INTO '.USER_GROUP_TABLE.' (id_user, id_group) VALUES '.$values_str);
	}
	
	ListGroup();
	return;
}

?>