<?php

/**
* Cron file. Update rss feed news on site.
*
* @package DatingPro
* @subpackage Admin Mode
**/

error_reporting(~E_ALL);
include "../include/config.php";
include_once "../common.php";
include "../include/config_admin.php";
include "../include/config_index.php";
include "../include/class.news.php";
NewsUpdater();
?>