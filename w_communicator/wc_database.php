<?php
//include "../include/config.php"; 
//include "wc_config.php";
//include "wc_common.php";

//==============================================================================
// Проверка создана ли табилца lid - если нет - то создаем
function init_lid()
{
  global $dbconn;
  $strSQL = "CREATE TABLE IF NOT EXISTS ".ENABLED_LID_TABLE."
                (
                  id       int(11)  NOT NULL auto_increment,
                  user_id  int(11)  NOT NULL DEFAULT '0' ,
                  lid      int(11)  NOT NULL DEFAULT '0' ,
                  date_add datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ,
                  PRIMARY KEY (id)
                )";
  $rs = $dbconn-> Execute($strSQL);
  if ($rs===false)
     { // Ошибка
       print("<br>\nError: Database error 14"."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
       exit();
     }
  return 0;
}
//==============================================================================

//==============================================================================
// Инициализация таблицы сообщений пользователя
function init_users_messages_table()
{
  global $dbconn;
  $strSQL = "CREATE TABLE IF NOT EXISTS ".USERS_MESSAGES_TABLE."
                (
                  id               int(11)      NOT NULL auto_increment,
                  from_id          int(11)      NOT NULL DEFAULT '0',
                  to_id            int(11)      NOT NULL DEFAULT '0',
                  sess_id          int(11)      NOT NULL default '0',
                  mess_type        int(11)      NOT NULL DEFAULT '0',
                  mess_text        text         NOT NULL DEFAULT '',
                  post_date        datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
                  codepage         int(11)      NOT NULL default '0',
                  sended_to_client int(11)      NOT NULL DEFAULT '0',
                  PRIMARY KEY (id),
                  KEY `from_id`   (`from_id`),
                  KEY `to_id`     (`to_id`),
                  KEY `post_date` (`post_date`)
                )";
  $rs = $dbconn-> Execute($strSQL);
  if ($rs===false)
     { // Ошибка
       print("<br>\nError: Database error 59"."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
       exit();
     }
  return 0;
}
//==============================================================================

//==============================================================================
// Инициализация таблицы сообщений пользователя
function init_user_contact_list_table()
{
  global $dbconn;
  $strSQL = "CREATE TABLE IF NOT EXISTS ".USER_CONTACT_LIST_TABLE."
                (
                  id               int(11)      NOT NULL auto_increment,
                  user_id          int(11)      NOT NULL DEFAULT '0',
                  view_user_id     int(11)      NOT NULL default '0',
                  view_user_status int(11)      NOT NULL default '0',
                  ban_status       int(11)      NOT NULL default '0',
                  PRIMARY KEY (id),
                  KEY `user_id` (`user_id`),
                  KEY `view_user_id` (`view_user_id`)
                )";
  $rs = $dbconn-> Execute($strSQL);
  if ($rs===false)
     { // Ошибка
       print("<br>\nError: Database error 152"."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
       exit();
     }
  return 0;
}
//==============================================================================

//==============================================================================
// Инициализация таблицы сообщений пользователя
function init_users_online_table()
{
  global $dbconn;
  $strSQL = "CREATE TABLE IF NOT EXISTS ".USERS_ONLINE_TABLE."
                (
                  id               int(11)      NOT NULL auto_increment,
                  user_id          int(11)      NOT NULL DEFAULT '0',
                  online_until     datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
                  PRIMARY KEY (id),
                  KEY `user_id` (`user_id`)
                )";
  $rs = $dbconn-> Execute($strSQL);
  if ($rs===false)
     { // Ошибка
       print("<br>\nError: Database error 153"."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
       exit();
     }
  return 0;
}
//==============================================================================

//==============================================================================
// Инициализация таблицы номеров последних alerted сообщений
function init_last_alerted_ids_table()
{
  global $dbconn;
  $strSQL = "CREATE TABLE IF NOT EXISTS ".LAST_ALERTED_IDS_TABLE."
                (
                  id               int(11)      NOT NULL auto_increment,
                  user_id          int(11)      NOT NULL DEFAULT '0',
                  last_alerted_txt_ids int(11)      NOT NULL DEFAULT '0',
                  PRIMARY KEY (id),
                  KEY `user_id` (`user_id`)
                )";
  $rs = $dbconn-> Execute($strSQL);
  if ($rs===false)
     { // Ошибка
       print("<br>\nError: Database error 154"."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
       exit();
     }
  return 0;
}
//==============================================================================

//------

//==============================================================================
// Инициализация таблички состояния новостей
function init_news()
{
  global $dbconn;
  $strSQL = "CREATE TABLE IF NOT EXISTS ".NEWS_STATE_TABLE."
                (
                  id             int(11)  NOT NULL auto_increment,
                  user_id        int(11)  NOT NULL DEFAULT '0' ,
                  last_news_date int(11)  NOT NULL DEFAULT '0',
                  PRIMARY KEY (id)
                )";
  $rs = $dbconn-> Execute($strSQL);
  if ($rs===false)
     { // Ошибка
       print("<br>\nError: Database error 19"."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
       exit();
     }
  return 0;
}
//==============================================================================

//==============================================================================
// Инициализация таблички состояния писем
function init_letters()
{
  global $dbconn;
  $strSQL = "CREATE TABLE IF NOT EXISTS ".LETTERS_STATE_TABLE."
                (
                  letters_alerted int(11)  NOT NULL DEFAULT '0' ,
                  PRIMARY KEY (letters_alerted)
                )";
  $rs = $dbconn-> Execute($strSQL);
  if ($rs===false)
     { // Ошибка
       print("<br>\nError: Database error 22"."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
       exit();
     }
  return 0;
}
//==============================================================================

//==============================================================================
// Инициализация таблички просмотренных perfect match ползователей
function init_perfect_match()
{
  global $dbconn;
  $strSQL = "CREATE TABLE IF NOT EXISTS ".PERFECT_MATCH_TABLE."
                (
                  user_id        int(11)  NOT NULL DEFAULT '0' ,
                  perfect_id     int(11)  NOT NULL DEFAULT '0',
                  KEY `user_id` (`user_id`)
                )";
  $rs = $dbconn-> Execute($strSQL);
  if ($rs===false)
     { // Ошибка
       print("<br>\nError: Database error 22"."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
       exit();
     }
}
//==============================================================================

//==============================================================================
// Инициализация таблички просмотренных perfect match ползователей
function init_profile_visited()
{
  global $dbconn;

  $strSQL = "CREATE TABLE IF NOT EXISTS ".PROFILE_VISITED_TABLE."
                (
                    id_user              int(11)  NOT NULL DEFAULT '0' ,
                    id_visiter           int(11)  NOT NULL DEFAULT '0' ,
                    count_of_visits      int(11)  NOT NULL DEFAULT '0' 
                )";
  $rs = $dbconn-> Execute($strSQL);
  if ($rs===false)
     { // Ошибка
       print("<br>\nError: Database error 29"."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
       exit();
     }
}
//==============================================================================

//==============================================================================
// Демонстрационные контакты и сообщения
function init_demo()
{
  global $dbconn;

  $dbconn-> Execute("INSERT INTO ".USER_CONTACT_LIST_TABLE." VALUES (1, 3, 6, 0, 0)");
  $dbconn-> Execute("INSERT INTO ".USER_CONTACT_LIST_TABLE." VALUES (2, 10, 6, 0, 0)");
  $dbconn-> Execute("INSERT INTO ".USER_CONTACT_LIST_TABLE." VALUES (3, 7, 6, 0, 0)");

  $dbconn-> Execute("INSERT INTO ".LAST_ALERTED_IDS_TABLE." VALUES (1, 3, 4)");
  $dbconn-> Execute("INSERT INTO ".LAST_ALERTED_IDS_TABLE." VALUES (2, 6, 12)");
  $dbconn-> Execute("INSERT INTO ".LAST_ALERTED_IDS_TABLE." VALUES (3, 10, 5)");

  $dbconn-> Execute("INSERT INTO ".USERS_ONLINE_TABLE." VALUES (1, 3, '0000-00-00 00:00:00')");
  $dbconn-> Execute("INSERT INTO ".USERS_ONLINE_TABLE." VALUES (2, 10, '0000-00-00 00:00:00')");
  $dbconn-> Execute("INSERT INTO ".USERS_ONLINE_TABLE." VALUES (3, 7, '0000-00-00 00:00:00')");

  $dbconn-> Execute("INSERT INTO ".USERS_MESSAGES_TABLE." (from_id, to_id, mess_type, mess_text, post_date) VALUES (3, 6, 1, 'Hi) Please answer me. Contact me via Live chat for support!', '2007-04-11 15:39:55')");
  $dbconn-> Execute("INSERT INTO ".USERS_MESSAGES_TABLE." (from_id, to_id, mess_type, mess_text, post_date) VALUES (10, 6, 1, 'Hi you are albanian?\rI like you!!!', '2007-04-11 15:55:14')");
  $dbconn-> Execute("INSERT INTO ".USERS_MESSAGES_TABLE." (from_id, to_id, mess_type, mess_text, post_date) VALUES (7, 6, 1, 'Hello! How is life?', '2007-04-12 13:51:25')");
}
//==============================================================================

?>