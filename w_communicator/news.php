<?php
include "../include/functions_users.php";
if (!class_exists('multilang')) include "../include/class.lang.php";

  if (!isset($_GET["mode"]))
     {
        print(DATING_WCOMMUNICATOR_HEADER);
        print("<br>\nError: mode not setted."."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
        return;
     }
     else
     {
        $mode = $_GET["mode"];
        if ($mode=="getnews")
           { 
             print(DATING_WCOMMUNICATOR_HEADER); 
             get_news();
           }  
           else
        if ($mode=="testconnection")
           {
             print(DATING_WCOMMUNICATOR_HEADER);
             test_connection();
           }
           else   
        if ($mode=="enablelogin")
           {
             print(DATING_WCOMMUNICATOR_HEADER);
             enable_login();
           }
           else   
        if ($mode=="openletter")
           {
             open_letter();
           }
           else
        if ($mode=="openuserprofile")
           {
             open_user_profile();
           }
           else           
        if ($mode=="openhomepage")
           {
             open_home_page();
           }
           else
           {
             print(DATING_WCOMMUNICATOR_HEADER);
             print("<br>\nError: Unknown mode."."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
             return;
           }       
   
     }
  return ;
                                        

function get_news()
{
  global $user_lang, $win_lang;
  $user_lang = $_REQUEST["wlang"];
  $user_lang = strtolower($user_lang);
  $user_lang = preg_replace("/[^a-zA-Z]/i", "" ,$user_lang);
  $lang_file_name = "lang/".$user_lang."_ansi.php";
  if (@file_exists($lang_file_name))
     {
        include "$lang_file_name";
     }
     else
     {
        include "lang/english_ansi.php";
     }

  $login_res = test_login();
  if ($login_res) return;
  global $mess_id;
  $mess_id=0;
  $hr = 0;
  if (isset($_GET["getsitenews"])&&($_GET["getsitenews"]=="1"))
     {
        $hr = check_news();
        if ($hr!=false)
           {
             echo "Error: Cant` get news popup.";
             return $hr;
           }
     };
 if (isset($_GET["getnewletters"])&&($_GET["getnewletters"]=="1"))
     {     
        $hr = check_letters();
        if ($hr!=false)
           {
             echo "Error: Cant` get letters popup.";
             return $hr;
           }
     }  
 if (isset($_GET["getperfectmatch"])&&($_GET["getperfectmatch"]=="1"))
     {     
        $hr = check_perfect_match();
        if ($hr!=false)
           {
             echo "Error: Cant` get perfect match popup.";
             return $hr;
           }
     }
  if (isset($_GET["getviewprofile"])&&($_GET["getviewprofile"]=="1"))
     {     
        $hr = check_profile_visited();
        if ($hr!=false)
           {
             echo "Error: Cant` get profile visited popup.";
             return $hr;
           }
     };   
/*
  print("<br>\nMessage Type$mess_id: Site News");
  print("<br>\nNews Type$mess_id: Alfa-dot registered");
  print("<br>\nNews Name$mess_id: PilotGroup hosting provided");
  print("<br>\nNews Text$mess_id: Lets test news now!");
  $news_id = "http://www.yandex.ru";
  print("<br>\nNews Url$mess_id: $news_id");
  $mess_id++;

  print("<br>\nMessage Type$mess_id: New letter");
  print("<br>\nNews Type$mess_id: New mail");
  print("<br>\nNews Name$mess_id: Adam Smitt wrote:");
  print("<br>\nNews Text$mess_id: Good weather now. Lets go for a walk...");
  print("<br>\nNews Url$mess_id: http://boss/analitic/messenger.php?mode=openletter&letterid=342");
  print("<br>\nLetter Number$mess_id: 342");
  $mess_id++;

  print("<br>\nMessage Type$mess_id: Readed letter");
  print("<br>\nLetter Number$mess_id: 342");
  $mess_id++;


  print("<br>\nMessage Type$mess_id: Registered user");
  print("<br>\nNews Type$mess_id: Match Perfect User Registered");
  print("<br>\nNews Name$mess_id: James Bond 24 years");
  print("<br>\nNews Text$mess_id: Status: Single. Intrests: Sport, Tv, Sex. About: I search a beautiful girl. Beautiful girl. beautiful girl. beautiful girl. beautiful girl. beautiful girl. beautiful girl. ");
  print("<br>\nNews Url$mess_id: http://boss/analitic/messenger.php?mode=openuserprofile&userid=562");
  print("<br>\nUser id$mess_id: 562");


  $mess_id++;

  print("<br>\nMessage Type$mess_id: Profile Showed");
  print("<br>\nNews Type$mess_id: You Profile was viewed by");
  print("<br>\nNews Name$mess_id: Kameren Dias 19 years");
  print("<br>\nNews Text$mess_id: Status: Single. Intrests: SEX, Money, Power. About: I am a bich, sheach a man with money.");
  print("<br>\nNews Url$mess_id: http://boss/analitic/messenger.php?mode=openuserprofile&userid=565");
  print("<br>\nUser id$mess_id: 565");
  $mess_id++;
*/
  print("<br>\nMessage Type$mess_id: Finish Message");
  print("<br>\nNews_finished: true");

  return ;
}
//==============================================================================

//==============================================================================
function check_news()
{
  global $g_user_name;
  global $dbconn;
  global $mess_id;
  global $win_lang;

  $user_id = get_user_id($g_user_name);
  
  $last_news_ts=0;
  $strSQL = "select last_news_date from ".NEWS_STATE_TABLE." where user_id = '$user_id'";
  $rs = $dbconn->Execute($strSQL);
  if (($rs===false)||($rs->EOF))
     { 
     }
     else 
     {
       $row = $rs->GetRowAssoc(false);     
       $last_news_ts = $row["last_news_date"];
     };  
  
  $strSQL = "select date_ts, channel_name, title, news_text, id  from ".NEWS_TABLE." where date_ts>'$last_news_ts' ORDER BY `date_ts` ASC";
  $rs = $dbconn->Execute($strSQL);
  if (($rs===false)||($rs->EOF))
     { 
       return false;
     }
  while (!$rs->EOF)  
    {
     $row = $rs->GetRowAssoc(false);     
     $news_date_ts = $row["date_ts"];
     $news_type = $win_lang["News"];
     $news_name = $row["title"];
     $news_text = $row["news_text"];
     $news_id   = $row["id"];     
     CorrectorNews($news_type, $news_name, $news_text);
     print("<br>\nMessage Type$mess_id: Site News");
     print("<br>\nNews Type$mess_id: $news_type");
     print("<br>\nNews Name$mess_id: $news_name");
     print("<br>\nNews Text$mess_id: $news_text");
     $news_id = GetNewsReadLink($news_id);
     print("<br>\nNews Url$mess_id: $news_id");
     $mess_id++;
     $rs->MoveNext();
     $last_news_ts=$news_date_ts;
    } 
  $strSQL = "DELETE FROM ".NEWS_STATE_TABLE." WHERE user_id = '$user_id'";
  $rs = $dbconn->Execute($strSQL);       
  $strSQL = "INSERT INTO ".NEWS_STATE_TABLE." (user_id, last_news_date) VALUES ('$user_id', '$last_news_ts')";
  $rs = $dbconn->Execute($strSQL);       
  return false;  
}
//==============================================================================

//==============================================================================
function check_letters()
{
  global $g_user_name;
  global $config, $dbconn;
  global $mess_id;
  global $win_lang;
  
  $user_id = get_user_id($g_user_name);
  $last_letters_date=0;
  $strSQL = "select letters_alerted from ".LETTERS_STATE_TABLE."";
  $rs = $dbconn->Execute($strSQL);
  $alerted_letters = "''";
  if (($rs===false)||($rs->EOF))
     { 
     }
     else 
     {
       $alerted_letters = "";
       while (!$rs->EOF)  
             {
                $row = $rs->GetRowAssoc(false);     
                $alert_id = $row["letters_alerted"];
                $alerted_letters = $alerted_letters."'".$alert_id."',";
                $rs->MoveNext();
             }
       $alerted_letters = substr($alerted_letters,0,-1);
     };  
  
  $strSQL = "select id from ".MAILBOX_TABLE." where id in ($alerted_letters) and id_to='$user_id' and was_read='1' and deleted_to='0' ORDER BY `date_creation` ASC";
  $rs = $dbconn->Execute($strSQL);
  if ($rs===false)
     { 
       return false;
     }
  if ($rs->EOF)
     {   
       $delete_alerted_letters = "''";
     }
     else
     {
        while (!$rs->EOF)  
          {
           $row = $rs->GetRowAssoc(false);     
           $letter_id = $row["id"];
           print("<br>\nMessage Type: Readed letter");
           print("<br>\nLetter Number: $letter_id");
           $delete_alerted_letters=$delete_alerted_letters."'".((string)$letter_id)."',";
           $rs->MoveNext();
          } 
        $delete_alerted_letters = substr($delete_alerted_letters,0,-1);  
     }     
  
  $strSQL = "DELETE FROM ".LETTERS_STATE_TABLE." where letters_alerted in ($delete_alerted_letters)";
  $rs = $dbconn->Execute($strSQL);
  
  $alert_letters_add = "";
  $strSQL = "select date_creation, body, subject, id_from, id from ".MAILBOX_TABLE." where id not in ($alerted_letters) and id_to='$user_id' and was_read='0' and deleted_to='0' ORDER BY `date_creation` ASC";
  $rs = $dbconn->Execute($strSQL);
  if ($rs===false)
     { 
       return false;
     }
  while (!$rs->EOF)  
    {
     $row = $rs->GetRowAssoc(false);  
     $letter_id  = $row["id"];
     $user_wrote = $row["id_from"];
     $user_name_wrote = get_user_name($user_wrote);
     $news_type = $user_name_wrote.$win_lang[" wrote:"];
     $news_name = $row["subject"];
     $news_text = $row["body"];
     CorrectorNews($news_type, $news_name, $news_text);
     print("<br>\nMessage Type$mess_id: New letter");
     print("<br>\nNews Type$mess_id: $news_type");
     print("<br>\nNews Name$mess_id: $news_name");
     print("<br>\nNews Text$mess_id: $news_text");
     $config["server"].$config["site_root"].
     $url = WN_FILE_MAIN."?part=news&mode=openletter&letterid=$letter_id";
     print("<br>\nNews Url$mess_id: $url");
     print("<br>\nLetter Number$mess_id: $letter_id");
     $mess_id++;
     $rs->MoveNext();
     
     $strSQL = "INSERT INTO ".LETTERS_STATE_TABLE." VALUES ('$letter_id')";
     $rs2 = $dbconn->Execute($strSQL);       
    } 
  return false;  
}
//==============================================================================

//==============================================================================
function check_perfect_match()
{
  global $g_user_name;
  global $dbconn;
  global $mess_id;
  global $win_lang;
  
  $user_id = get_user_id($g_user_name);
  
  $perfect_users = array();
  ini_set("display_errors", "0");
  $perfect_users = GetPerfectUsersList($user_id);
  ini_set("display_errors", "1");
  if (!isset($perfect_users["id_arr"])) $perfect_users["id_arr"]=array();
                                   else $perfect_users["id_arr"]=array_unique($perfect_users["id_arr"]);
  $perfect_users = $perfect_users["id_arr"];

  $alerted_perfect_match = array();
  $strSQL = "select perfect_id from ".PERFECT_MATCH_TABLE." where user_id='$user_id'";
  $rs = $dbconn->Execute($strSQL);
  if (($rs===false)||($rs->EOF))
     { 
     }
     else 
     {
        while (!$rs->EOF)  
          {
           $row = $rs->GetRowAssoc(false);  
           $user_view_id = $row["perfect_id"];
           $alerted_perfect_match[]=$user_view_id;
           $rs->MoveNext();
          }
     };  
  
  $res_perfect_users = array_diff($perfect_users, $alerted_perfect_match);
  foreach ($res_perfect_users as $usr_id) 
          {
            $user_name = get_user_name($usr_id);
            $user_age  = get_user_age($usr_id);
            $news_name = $user_name." ".$user_age.$win_lang[" years"];
            $user_info = get_user_info($usr_id);
            $url = WN_FILE_MAIN."?part=news&mode=openuserprofile&userid=$usr_id";
            
            print("<br>\nMessage Type$mess_id: Registered user");
            print("<br>\nNews Type$mess_id: ".$win_lang["Registered user"]);
            print("<br>\nNews Name$mess_id: $news_name");
            print("<br>\nNews Text$mess_id: $user_info");
            print("<br>\nNews Url$mess_id: $url");
            print("<br>\nUser id$mess_id: $usr_id");
            $mess_id++;
            
            $strSQL = "INSERT INTO ".PERFECT_MATCH_TABLE." (user_id, perfect_id) VALUES ('$user_id', '$usr_id')";
            $rs = $dbconn->Execute($strSQL);       
          }
  return false;
}
//==============================================================================

//==============================================================================
function check_profile_visited()
{
  global $g_user_name;
  global $dbconn;
  global $mess_id; 
  global $win_lang;

  $user_id = get_user_id($g_user_name);

  $profiles_visited = array();
  
  $strSQL = "Select id_visiter, count_of_visits from ".PROFILE_VISITED_TABLE."
             where id_user = $user_id";
  $rs = $dbconn->Execute($strSQL);
  if (($rs===false)||($rs->EOF))
     { 
     }
     else 
     {
       while (!$rs->EOF)  
          {
            $row = $rs->GetRowAssoc(false);
            $profiles_visited[] = $row;
            $rs->MoveNext();            
          }
     }     
  
  $profiles_visit = array();
  $strSQL = "Select a.id_visiter, a.count_of_visits 
             from ".PROFILE_VISIT_TABLE." a, ".USERS_TABLE." usrs 
             where a.id_user = $user_id and a.id_visiter !=$user_id 
             and usrs.id = a.id_visiter and a.id_visiter!=0 and usrs.status='1' and usrs.root_user='0' and usrs.guest_user='0'";
  $rs = $dbconn->Execute($strSQL);
  if (($rs===false)||($rs->EOF))
     { 
     }
     else 
     {
       while (!$rs->EOF)  
          {
            $row = $rs->GetRowAssoc(false);
            $profiles_visit[] = $row;
            $rs->MoveNext();            
          }
     }

  foreach ($profiles_visit as $view_profile) 
          {
             $new_visit=true;
             foreach ($profiles_visited as $viewed_profile)
                     {
                       if (($view_profile["id_visiter"]==$viewed_profile["id_visiter"])&&
                           ($view_profile["count_of_visits"]<=$viewed_profile["count_of_visits"]))
                           {
                             $new_visit=false;
                             break;
                           }
                     }
             if ($new_visit==true)
                {        
                   $usr_id = $view_profile["id_visiter"];
                   $visits = $view_profile["count_of_visits"];
                   
                   $user_name = get_user_name($usr_id);
                   $user_age  = get_user_age($usr_id);
                   $news_name = $user_name." ".$user_age.$win_lang[" years"];
                   $user_info = get_user_info($usr_id);
                   $url = WN_FILE_MAIN."?part=news&mode=openuserprofile&userid=$usr_id";
                   print("<br>\nMessage Type$mess_id: Profile Showed");
                   print("<br>\nNews Type$mess_id: ".$win_lang["Profile Showed"]);
                   print("<br>\nNews Name$mess_id: $news_name");
                   print("<br>\nNews Text$mess_id: $user_info");
                   print("<br>\nNews Url$mess_id: $url");
                   print("<br>\nUser id$mess_id: $usr_id");
                   $mess_id++;
        
                   $strSQL = "DELETE from ".PROFILE_VISITED_TABLE." where id_user='$user_id' and id_visiter='$usr_id'";
                   $rs2 = $dbconn->Execute($strSQL); 
                   
                   $strSQL = "INSERT INTO ".PROFILE_VISITED_TABLE." (id_user, id_visiter, count_of_visits) VALUES ('$user_id', '$usr_id', '$visits')";
                   $rs2 = $dbconn->Execute($strSQL);
                }
          };

     
};
//==============================================================================

//==============================================================================
function CorrectorNews(&$news_type, &$news_name, &$news_text)
{
  if ($news_type=="") $news_type="News";
  if ($news_name=="") $news_name="Some news";
  if ($news_text=="") $news_text="No text";
  
  CorrectNewsText   ($news_type);                     
  CorrectNewsLenText($news_type, MAX_NEWS_TYPE_LEN); 
  CorrectNewsText   ($news_name); 
  CorrectNewsLenText($news_name, MAX_NEWS_NAME_LEN); 
  CorrectNewsText   ($news_text); 
  CorrectNewsLenText($news_text, MAX_NEWS_TEXT_LEN); 
  return 0;
}

?>