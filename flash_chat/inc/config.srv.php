<?php
include substr(dirname(__FILE__), 0, -15)."/include/config.php";

$GLOBALS['fc_config']['db'] = array(
	'host' => $config["dbhost"],
	'user' => $config["dbuname"],
	'pass' => $config["dbpass"],
	'base' => $config["dbname"],
	'pref' => $config["table_prefix"].'fc_',
);

?>