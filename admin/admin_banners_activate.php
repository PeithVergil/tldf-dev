<?php
/**
* Banners activation
*
* @package DatingPro
* @subpackage Admin Mode
**/
include "../include/config.php";
include_once "../common.php";

global $dbconn, $config;

if ((isset($_REQUEST["id"]))&&($_REQUEST["id"]!="")) $id = $_REQUEST["id"];
else die("Logic error: banner id is not setted.");

$strSQL = "select a.banner_url, a.stop_after_hits ".
"from ".BANNERS_TABLE." a ".
"where a.id=$id";
$rs = $dbconn->Execute($strSQL);

if ($rs->EOF)
{
	die("Logic error. Can`t find banner by id = $id");
};
$row = $rs->GetRowAssoc(false);
$url = $row["banner_url"];
$stop_after_hits = $row["stop_after_hits"];
if ($stop_after_hits>0) $stop_after_hits--;

// decrement stop after views

$strSQL =  "update ".BANNERS_TABLE." set stop_after_hits='".$stop_after_hits."' ".
"where id='".$id."'";
$rs = $dbconn->Execute($strSQL);

$strSQL = "SELECT id, hits FROM ".BANNERS_GLOBAL_STATISTICS." ".
"WHERE banner_id='$id' AND date=NOW()";
$glob_stat_rs = $dbconn->Execute($strSQL);
if ($glob_stat_rs->RowCount() > 0) {
	$glob_stat = $glob_stat_rs->getRowAssoc( false );

	$strSQL = "UPDATE ".BANNERS_GLOBAL_STATISTICS." SET ".
	"hits='".++$glob_stat["hits"]."' ".
	"WHERE id='".$glob_stat["id"]."'";
} else {
	$strSQL = "INSERT INTO ".BANNERS_GLOBAL_STATISTICS." SET ".
	"hits='1', banner_id='$id', date=NOW()";
}
$dbconn->Execute($strSQL);

echo "<script>location.href='".$url."'</script>";
?>
