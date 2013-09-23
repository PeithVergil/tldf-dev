<?php
ini_set("url_rewriter.tags","");
ini_set('session.use_trans_sid', false);
include "../include/config.php";

include "wc_config.php";
include "wc_common.php";
include "wc_functions.php";
include "wc_home_page.php";
include "wc_site_login.php";
include "wc_mail_open.php";
include "wc_profile_open.php";


  // Глобальные константы
  $DEF_CODE_STRING_SEQUENCE = "kJXAeKFKoeswQfhfuvlZfGZYiqncTupC4gKbOVVg4mJNm3y2lBvBOaTlb51uagNv9kCys8yNvTUbyQUKGXxWDu0EwbPsrg2EQqNgS2CaDPhR3NJ9gimaBWam5PgRANc0SaE29rD";
  $g_user_name     = "";

  if (!isset($_GET["part"]))
     {
        print(DATING_WCOMMUNICATOR_HEADER);
        print("<br>\nError: part not setted."."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
        return;
     }
     else
     {
        $part = $_GET["part"];
        if ($part=="message")
           {
             require("messages.php");
           }
           else
        if ($part=="news")
           {
             require("news.php");
           }
           else
           {
             print(DATING_WCOMMUNICATOR_HEADER);
             print("<br>\nError: part is unknown."."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
             return;
           }
     }
?>
