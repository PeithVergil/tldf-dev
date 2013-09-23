<?php
/**
 * ----------------------------------------------
 * URL: http://www.pilotgroup.net
 * ----------------------------------------------
 */
global $config;
global $poll_module_path;
global $POLLDB;
global $POLLTBL;
global $_SERVER;
global $PHP_SELF;


require $poll_module_path."/include/config.inc.php";
require $poll_module_path."/include/$POLLDB[class]";
require $poll_module_path."/include/class_session.php";
require $poll_module_path."/include/class_template.php";

function no_cache_header() 
{
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
}

if (!isset($PHP_SELF))  $PHP_SELF = $_SERVER["PHP_SELF"];
    

global $POLL_CLASS;
global $pollvars;

$POLL_CLASS["db"] = new polldb_sql;
$POLL_CLASS["db"]->connect();
$pollvars = $POLL_CLASS["db"]->fetch_array($POLL_CLASS["db"]->query("SELECT * FROM $POLLTBL[poll_config]"));
$POLL_CLASS["db"]->free_result($POLL_CLASS["db"]->result);
$pollvars["SELF"] = basename($PHP_SELF);
$pollvars["base_gif"] = $config["server"].$config["site_root"].$pollvars["base_gif"];

unset($lang_poll);
if (file_exists($poll_module_path."/lang/$pollvars[lang]")) {
    include ($poll_module_path."/lang/$pollvars[lang]");
} else {
    include ($poll_module_path."/lang/english.php");
}

$POLL_CLASS["session"] = new poll_session();
$POLL_CLASS["session"]->db = $POLL_CLASS["db"];
?>