<?php

/**
* Used in AJAX-requests.
*
* @package DatingPro
* @subpackage Admin Mode
**/

include '../include/config.php';
include_once '../common.php';

header('Content-type: text/html; charset='.$charset);

if (!isset($_GET['sel'])) {
	exit;
}

$sel = $_GET['sel'];

switch ($sel) {
	case 'region':
		$rs = $dbconn->Execute('SELECT * FROM '.REGION_SPR_TABLE.' WHERE id_country="'.intval($_GET['id_country']).'" ORDER BY name');

		echo '<select name="region" id="region" style="width: 195px;" onchange="SelectCityAdmin(this.value, city_div);">';
		echo '<option value="0">------------------</option>';
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			echo '<option value="'.$row['id'].'">'.stripslashes(htmlspecialchars($row['name'])).'</option>';
			$rs->MoveNext();
		}
		echo '</select>';
	break;
	case 'city':
		$rs = $dbconn->Execute('SELECT DISTINCT id, name FROM '.CITY_SPR_TABLE.' WHERE id_region="'.intval($_GET['id_region']).'" GROUP BY id ORDER BY name');

		echo '<select name="city" id="city" style="width: 195px">';
		echo '<option value="0">------------------</option>';
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			echo '<option value="'.$row['id'].'">'.stripslashes(htmlspecialchars($row['name'])).'</option>';
			$rs->MoveNext();
		}
		echo '</select>';
	break;
	default:
		exit;
}

?>