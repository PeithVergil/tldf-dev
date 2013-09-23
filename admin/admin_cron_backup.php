<?php
/**
* Cron file. Backups database.
*
* @package DatingPro
* @subpackage Admin Mode
**/
include "../include/config.php";
include_once "../common.php";
include "../include/config_admin.php";
include "../include/config_index.php";
include "../include/class.dumper.php";

error_reporting(E_ALL && ~E_NOTICE);

// from dumper php

define('PATH', $config["site_path"].'/backup/');
define('URL',  $config["server"].$config["site_root"].'/backup/');
define('TIME_LIMIT', 600);
define('LIMIT', 1);
define('DBHOST', $config["dbhost"]);
define('DBNAMES', $config["dbname"]);

// write settings to file
define("SC", 0);

$is_safe_mode = ini_get('safe_mode') == '1' ? 1 : 0;
if (!$is_safe_mode) set_time_limit(TIME_LIMIT);

@mysql_connect($config["dbhost"], $config["dbuname"], $config["dbpass"]);

if (!file_exists(PATH) && !$is_safe_mode) {
	mkdir(PATH, 0777) || die("Error on creating directory ".PATH);
	@chmod(PATH, 0777);
}

$SK = new dumper($config, $lang);
$SK->backup(false);
mysql_close();

?>
