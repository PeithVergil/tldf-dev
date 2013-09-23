<?php

/**
* Cron file. Delete 'Send' payment > 7 days.
*
* @package DatingPro
* @subpackage Admin Mode
**/

include "../include/config.php";
include_once "../common.php";
include "../include/config_admin.php";
include "../include/config_index.php";
include "../include/functions_admin.php";


$strSQL = " DELETE FROM ".BILLING_REQUESTS_TABLE." WHERE status='send' AND date_send<(now()-INTERVAL 7 DAY) ";
$dbconn->Execute($strSQL);

?>