<?php
/**
* Used in AJAX-requests
*
*
* @package DatingPro
* @subpackage User Mode
**/

include './include/config.php';
include './common.php';

header('Content-type: text/html; charset='.$charset);

if (!isset($_GET['sel']) || !isset($_GET['sec'])) {
	exit;
}

$sec = $_GET['sec'];
$sel = $_GET['sel'];

switch ($sec) {
	case 'hp':
		$default_option = $lang['home_page']['select_default'];
		$select_country_name = 'country';
		$select_region_name = 'region';
		$select_city_name = 'city';
		$select_style = 'style="width:150px" class="index_select" ';
	break;
	case 'qs':
		$default_option = $lang['home_page']['select_default'];
		$select_country_name = 'country';
		$select_region_name = 'region';
		$select_city_name = 'city';
		$select_style = 'style="width:150px" class="index_select" ';
	break;
	case 'as':
		$default_option = $lang['button']['all'];
		$select_country_name = 'id_country';
		$select_region_name = 'id_region';
		$select_city_name = 'id_city';
		$select_style = 'style="width:150px" class="index_select" ';
	break;
	case 'mp':
		$default_option = $lang['home_page']['select_default'];
		$select_country_name = 'id_country';
		$select_region_name = 'id_region';
		$select_city_name = 'id_city';
		$select_style = 'style="width:150px" class="index_select" ';
	break;
	case 'rp':
		$default_option = $lang['home_page']['select_default'];
		$select_country_name = 'id_country';
		$select_region_name = 'id_region';
		$select_city_name = 'id_city';
		$select_style = 'style="width:150px" ';
	break;
	default:
		exit;
}

switch ($sel) {
	case 'country':
		$rs = $dbconn->Execute('SELECT * FROM '.COUNTRY_SPR_TABLE.' ORDER BY name');

		echo '<select name="'.$select_country_name.'" '.$select_style.' onchange="SelectRegion(\''.$sec.'\', this.value, document.getElementById(\'region_div\'), document.getElementById(\'city_div\'));">';
		echo '<option value="0">'.$default_option.'</option>';
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			echo '<option value="'.$row['id'].'">'.stripslashes(htmlspecialchars($row['name'])).'</option>';
			$rs->MoveNext();
		}
		echo '</select>';
	break;
	case 'region':
		$rs = $dbconn->Execute('SELECT id, name FROM '.REGION_SPR_TABLE.' WHERE id_country = ? ORDER BY name', array((int)$_GET['id_country']));

		echo '<select name="'.$select_region_name.'" '.$select_style.' onchange="SelectCity(\''.$sec.'\', this.value, document.getElementById(\'city_div\'));">';
		echo '<option value="0">'.$default_option.'</option>';
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			echo '<option value="'.$row['id'].'">'.stripslashes(htmlspecialchars($row['name'])).'</option>';
			$rs->MoveNext();
		}
		echo '</select>';
	break;
	case 'city':
		$rs = $dbconn->Execute('SELECT DISTINCT id, name FROM '.CITY_SPR_TABLE.' WHERE id_region = "'.intval($_GET['id_region']).'" GROUP BY id ORDER BY name');
		echo '<select name="'.$select_city_name.'" '.$select_style.'>';
		echo '<option value="0">'.$default_option.'</option>';
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			echo '<option value="'.$row['id'].'">'.stripslashes(htmlspecialchars($row['name'])).'</option>';
			$rs->MoveNext();
		}
		echo '</select>';
	break;
	case 'login':
		$rs = $dbconn->Execute('SELECT COUNT(id) FROM '.USERS_TABLE.' WHERE login = "'.strip_tags(trim(strval($_GET['login']))).'"');
		if ($rs->fields[0]) {
			echo $lang['err']['exists_login'];
		}
	break;
	default:
		exit;
}

?>