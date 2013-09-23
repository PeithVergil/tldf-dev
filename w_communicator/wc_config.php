<?php
// Constants for W_Communicator

// MMS - MY MESSAGE SEND

// MMS TABLE
define( "USERS_MESSAGES_TABLE",     $config["table_prefix"]."wm_mms_users_messages");
define( "USER_CONTACT_LIST_TABLE",  $config["table_prefix"]."wm_mms_contact_list");
define( "USERS_ONLINE_TABLE",       $config["table_prefix"]."wm_mms_online_users");
define( "BLOCK_POP_UP_MESSAGES_ON_SITE_TABLE",     $config["table_prefix"]."wm_mms_block_pop_up_messages");
define( "LAST_IM_CHEK_POP_UP_MESSAGES_TABLE",      $config["table_prefix"]."wm_mms_last_im_check_pop_up_messages");
define( "LAST_ALERTED_IDS_TABLE",   $config["table_prefix"]."wm_mms_last_alerted_ids");

// NEWS TABLE
define( "ENABLED_LID_TABLE",     $config["table_prefix"]."wm_news_enabled_lid");
define( "NEWS_STATE_TABLE",      $config["table_prefix"]."wm_news_user_news_state");
define( "LETTERS_STATE_TABLE",   $config["table_prefix"]."wm_news_user_letters_state");
define( "PERFECT_MATCH_TABLE",   $config["table_prefix"]."wm_news_perfect_match_alerted");
define( "PROFILE_VISITED_TABLE", $config["table_prefix"]."wm_news_profile_visited");


define( "DATING_WCOMMUNICATOR_HEADER", "<br>\nDating Communicator Page: Started");

define( "WN_FILE_MAIN",               $config["server"].$config["site_root"]."/w_communicator/communicator.php");
define( "WN_FILE_BROWSER_AUTH_ERROR", $config["server"].$config["site_root"]."/w_communicator/browser_auth_error.html");

define( "NEVER_WERE_ONLINE", -999999);    // Никогда не был в онлайне
define( "SYSTEM_USER",       -2);         // Пользователь система

define( "MAX_NEWS_TYPE_LEN", 500);
define( "MAX_NEWS_NAME_LEN", 500);
define( "MAX_NEWS_TEXT_LEN", 1000);


?>