<?php

/**
* KCAPTCHA for include
*
* @package DatingPro
* @subpackage Include files
**/
error_reporting(E_ERROR);
session_start();
include('kcaptcha.php');
// deprecated in php 5.3, removed from php 5.4, and not needed anyway, but we DO NEED session_start() above!
#session_register("captcha_keystring");
$captcha = new KCAPTCHA();

?>