<?php
/**
 *
 *  MySQL      -> class_mysql.php
 *  PostgreSQL -> class_pgsql.php
 *
 */
global $POLLDB;
global $POLLTBL;
global $GUEST_USER_ID;

$POLLDB["dbName"] = $config["dbname"];
$POLLDB["host"]   = $config["dbhost"];
$POLLDB["user"]   = $config["dbuname"];
$POLLDB["pass"]   = $config["dbpass"];
$POLLDB["class"]  = "class_mysql.php";

$GUEST_USER_ID = 2;

/* tables  */

$POLLTBL["poll_config"]  = $config["table_prefix"]."poll_config";
$POLLTBL["poll_index"]   = $config["table_prefix"]."poll_index";
$POLLTBL["poll_data"]    = $config["table_prefix"]."poll_data";
$POLLTBL["poll_ip"]      = $config["table_prefix"]."poll_ip";
$POLLTBL["poll_uname"]   = $config["table_prefix"]."poll_user_name";
$POLLTBL["poll_log"]     = $config["table_prefix"]."poll_log";
$POLLTBL["poll_comment"] = $config["table_prefix"]."poll_comment";
$POLLTBL["poll_tpl"]     = $config["table_prefix"]."poll_templates";
$POLLTBL["poll_tplset"]  = $config["table_prefix"]."poll_templateset";

?>