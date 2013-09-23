<?php
//==============================================================================
if (!function_exists("DateFromAge"))
   {
     function AgeFromBDate($date){
        ///// date in Y-m-d h:i:s format
        $year = intval(substr($date,0,4));
        $month = intval(substr($date,5,2));

        $n_year = date("Y");
        $n_month = date("m");

        return floor(($n_year - $year) + ($n_month - $month)/12);
     }
     function DateFromAge($age){
             ///// date in Y-m-d h:i:s format
             $n_year = date("Y");
             $year = $n_year - intval($age);

             return strval($year)."-01-01 00:00:00";
     }
     function GetNewsReadLink($id_news){
       global $dbconn, $config, $config_index;
       include "../include/config_index.php";

       $strSQL  = "select id from ".NEWS_TABLE." where status='1' order by date_ts desc, id desc";
       $rs = $dbconn->Execute($strSQL);
       $i = 0;
       $links = array();
       while(!$rs->EOF){
         $row = $rs->GetRowAssoc(false);
         $page = floor($i/$config_index["news_numpage"])+1;
         $links[$row["id"]] = $config["server"].$config["site_root"]."/news.php?page=".$page."#".$row["id"];
         $rs->MoveNext();
         $i++;
       }
       return $links[$id_news];
     }

   }
//==============================================================================


//==============================================================================
function get_user_session_name()
{
  global $dbconn;
  @session_start();
  $sess_id = session_id();
  if ((!$sess_id)&&(isset($PHPSESSID))) $sess_id = $PHPSESSID;
  $strSQL = "Select login from ".ACTIVE_SESSIONS_TABLE." a, ".USERS_TABLE." b where a.session = '".$sess_id."' and a.id_user=b.id";
  $rs = $dbconn->Execute($strSQL);
  if ($rs->RowCount())
     {
       return $rs->fields[0];
     }
     else
     {
       return "";
     }
}
//==============================================================================

//==============================================================================
function test_user_name_and_password($user, $password)
{
  global $dbconn;
  if ($user=="guest") return FALSE;
  $md5_password = md5($password);
  $strSQL = "select * from ".USERS_TABLE." where login='$user' and password='$md5_password' and status='1'";
  $rs = $dbconn->Execute($strSQL);
  if (($rs===false)||($rs->EOF))
     {
       return false;
     }
     else
     {
       return true;
     }
  return FALSE;
}
//==============================================================================

//==============================================================================
function test_login()
{
  if (isset($_GET["auth_session"])&&($_GET["auth_session"]==1))
     {
       $user_name = get_user_session_name();
       if (($user_name=="")||($user_name=="guest"))
          {
            print("<br>\nError: Login failed.");
            return -1;
          }
          else
          {
            global $g_user_name;
            $g_user_name=$user_name;
            //
            $user_name_shifr = shifr_string($user_name);
            print("<br>\nUser name: $user_name_shifr");
            return 0;
          };

     }
  if (!isset($_GET["username"]))
     {
        print("<br>\nError: Login failed.");
        return -1;
     }
  if (!isset($_GET["password"]))
     {
        print("<br>\nError: Login failed.");
        return -1;
     }
  $user_name = $_GET["username"];
  $password  = $_GET["password"];

  $user_name_orig = de_shifr_string($user_name);
  $password_orig  = de_shifr_string($password);

  $is_correct_logon = test_user_name_and_password($user_name_orig, $password_orig);
  if ($is_correct_logon==FALSE)
     {
        print("<br>\nError: Login failed.");
        return -1;
     }

  $user_name = shifr_string($user_name_orig);
  $password  = shifr_string($password_orig);

  print("<br>\nUser name: $user_name");
  print("<br>\nPassword: $password");
  global $g_user_name;
  $g_user_name = $user_name_orig;
  return 0;
}
//==============================================================================

//==============================================================================
function test_connection()
{
  $login_res = test_login();
  if ($login_res) return;

  print("<br>\nCorrect_connection: true");
  print("<br>\nRemote_ip: $_SERVER[REMOTE_ADDR]");
}
//==============================================================================

//==============================================================================
function del_string_spaces(&$text_inn)
{
  $text_sp  = $text_inn;
  $len = strlen($text_inn);

  do
     {
       $len_last = $len;
       $text_sp = str_replace("  ", " ", $text_sp);
       $len = strlen($text_sp);
     } while ($len!=$len_last);
  $text_inn = $text_sp;
  return 0;
}
//==============================================================================

//==============================================================================
function CorrectNewsText(&$text)
{
  //$text = strip_tags($text);
  $text = Html2text($text);
  $text_inr = str_replace("\r", " ", $text);
  $text_inn = str_replace("\n", " ", $text_inr);
  del_string_spaces($text_inn);
  $text=$text_inn;
  return 0;
}
//==============================================================================

//==============================================================================
function CorrectNewsLenText(&$text, $len)
{
  $text = substr($text, 0, $len);
  return 0;
}
//==============================================================================

//==============================================================================
// ������� ������ ������������� ����������� �������� �������� � �������
function enable_login()
{
  $login_res = test_login();
  if ($login_res) return;
  mt_srand(doubleval(microtime())*100000000);
  $unic_login_id = mt_rand(0,1000000);
  global $g_user_name;
  add_lid($g_user_name, $unic_login_id);

  print("<br>\nLogin mode: enabled");
  print("<br>\nUnic login id: $unic_login_id");
};
//==============================================================================

function open_browser_login(&$out_user_name, &$out_password)
{
  if (!isset($_GET["loginid"]))
     {
        print("<script>location.href='".WN_FILE_BROWSER_AUTH_ERROR."'</script>");
        return -1;
     }
  if (!isset($_GET["memberid"]))
     {
        print("<script>location.href='".WN_FILE_BROWSER_AUTH_ERROR."'</script>");
        return -1;
     }

  $login_id     = $_GET["loginid"];
  $member_id    = $_GET["memberid"];

  $user_name = "";
  $user_pass_md5 = "";
  $is_legal_login_id = get_name_by_lid($login_id, $user_name, $user_pass_md5);
  if (!$is_legal_login_id)
     {
        print("<script>location.href='".WN_FILE_BROWSER_AUTH_ERROR."'</script>");
        return -1;
     }
  //delete_lid($login_id);

  $orig_user_name_pass = de_shifr_string($member_id, $login_id);
  $delim = strpos($orig_user_name_pass, "|");
  if ($delim==0)
     {
        print("<script>location.href='".WN_FILE_BROWSER_AUTH_ERROR."'</script>");
        return -1;
     };
  $orig_name = substr($orig_user_name_pass, 0, $delim);
  $orig_pass = substr($orig_user_name_pass, $delim+1);
  $orig_pass_md5 = md5($orig_pass);
  if ((strtolower($orig_name) != strtolower($user_name))||($orig_pass_md5!=$user_pass_md5))
     {
        print("<script>location.href='".WN_FILE_BROWSER_AUTH_ERROR."'</script>");
        return -1;
     }
  $out_user_name = $orig_name;
  $out_password  = $orig_pass;
  return 0;
}

//==============================================================================
// ������� �������� � �������
function open_letter()
{
  $user_name=""; $password="";
  $login_res = open_browser_login($user_name, $password);
  if ($login_res!=0) return -1;
  if (!isset($_GET["letterid"]))
     {
        print("<script>location.href='".WN_FILE_BROWSER_AUTH_ERROR."'</script>");
        return -1;
     }
  $letter_id  = $_GET["letterid"];
  OpenMail($user_name, $password, $letter_id);
};
//==============================================================================

//==============================================================================
// ������� ������� ������������
function open_user_profile()
{
  $already_logined=-1;
  if (isset($_GET["auth_session"])&&($_GET["auth_session"]==1))
     {
       // ����������� �� ������
       $user_name = get_user_session_name(); $password="";
       if (($user_name=="")||($user_name=="guest"))
          {
            print("<script>location.href='".WN_FILE_BROWSER_AUTH_ERROR."'</script>");
            return -1;
          }
       $already_logined=1;
     }
     else
     {  // ����������� � ��������� ����� ������������
         $user_name=""; $password="";
         $login_res = open_browser_login($user_name, $password);
         if ($login_res!=0) return -1;
         $already_logined=0;
     };
  $user_id=-1;
  if ((isset($_GET["userid"]))&&($_GET["userid"]!="")) $user_id  = $_GET["userid"];
     else
  if ((isset($_GET["profile_user_name"]))&&($_GET["profile_user_name"]!=""))
     {
       $prof_user_name = $_GET["profile_user_name"];
       $user_id = get_user_id($prof_user_name);
     }
  if ($user_id==-1)
     {
        print("<script>location.href='".WN_FILE_BROWSER_AUTH_ERROR."'</script>");
        return -1;
     }
     else
     {
        OpenProfile($user_name, $password, $already_logined, $user_id);
        return 0;
     };
};
//==============================================================================

//==============================================================================
// ������� �������� �������� �����
function open_home_page()
{
  $user_name=""; $password="";
  $login_res = open_browser_login($user_name, $password);
  if ($login_res!=0) return -1;
  OpenPageHome($user_name, $password);
};
//==============================================================================

//==============================================================================
// �������� �� ����������� ������ ������ ��� ������������ � ������
function get_name_by_lid($lid, &$user_name, &$pass_md5)
{
  if ($lid==0) return FALSE;
  global $dbconn;
  clean_lid();
  $strSQL = "select user_id from ".ENABLED_LID_TABLE." where lid='$lid'";
  $rs = $dbconn->Execute($strSQL);
  if (($rs===false)||($rs->EOF))
     {
       return false;
     }
  $row = $rs->GetRowAssoc(false);
  $user_id = $row["user_id"];
  $user_name = get_user_login($user_id);
  $pass_md5  = get_user_passord_md5($user_id);
  if ($user_name=="") return false;
  return TRUE;
}
//==============================================================================

//==============================================================================
// ������ ������� lid
function clean_lid()
{
  global $dbconn;
  // ���������� ������ ������...
  $date = date('Y-m-d H:i:s',mktime(date("h")-1));
  $strSQL = "DELETE from ".ENABLED_LID_TABLE." where date_add<'$date'";
  $rs = $dbconn->Execute($strSQL);
  if ($rs===false)
     { // ������
       print("<br>\nError: Database error"."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
       exit();
     }
  return 0;
}
//==============================================================================

//==============================================================================
// �������� id ������������ �� ��� ������
function get_user_id($user_name)
{
  global $dbconn;
  $strSQL = "select id from ".USERS_TABLE." where login='$user_name'";
  $rs = $dbconn->Execute($strSQL);
  if (($rs===false)||($rs->EOF))
     { // ���� ������ ������������
       print("<br>\nError: get_user_id('$user_name') - user not exists."."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
       exit();
     }
  $row = $rs->GetRowAssoc(false);
  $id = $row["id"];
  return $id;
}
//==============================================================================

//==============================================================================
// �������� ����� ������������
function get_user_login($user_id)
{
  global $dbconn;
  $strSQL = "select login from ".USERS_TABLE." where id='$user_id'";
  $rs = $dbconn->Execute($strSQL);
  if (($rs===false)||($rs->EOF))
     { // ���� ������ ������������
       print("<br>\nError: Database error 24"."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
       exit();
     }
  $row = $rs->GetRowAssoc(false);
  $name = $row["login"];
  return $name;
}
//==============================================================================

//==============================================================================
// �������� ��� ������������
function get_user_name($user_id)
{
  return get_user_login($user_id); // �� �������� ������� �������� ��� ������������
/*
  global $dbconn;
  $strSQL = "select fname, sname from ".USERS_TABLE." where id='$user_id'";
  $rs = $dbconn->Execute($strSQL);
  if (($rs===false)||($rs->EOF))
     { // ���� ������ ������������
       print("<br>\nError: Database error 24"."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
       exit();
     }
  $row = $rs->GetRowAssoc(false);
  $name = $row["fname"]." ".$row["sname"];

  return $name;
*/
}
//==============================================================================

//==============================================================================
// �������� ������� ������������
function get_user_age($user_id)
{
  global $dbconn;
  $strSQL = "select date_birthday from ".USERS_TABLE." where id='$user_id'";
  $rs = $dbconn->Execute($strSQL);
  if (($rs===false)||($rs->EOF))
     { // ���� ������ ������������
       print("<br>\nError: Database error 28"."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
       exit();
     }
  $row = $rs->GetRowAssoc(false);
  $dt1  = $row["date_birthday"];
  $age  = AgeFromBDate($dt1);
  return $age;
}
//==============================================================================

//==============================================================================
function get_user_info($user_id)
{
  global $dbconn, $config, $win_lang;
  $config["default_lang"]=1;
  // ----------------------- view profile.php
  $old_display_errors = ini_get("display_errors");
  ini_set("display_errors", "1");
  // default multilanguage field for select
  $multi_lang = new MultiLang();
  $field_name = $multi_lang->DefaultFieldName();
  $strSQL =
	"Select a.login, a.sname, a.fname, b.name as country, c.name as city, r.name as region,
			d.".$field_name." as nation, e.".$field_name." as lang1, f.".$field_name." as lang2,
			g.".$field_name." as lang3, DATE_FORMAT(a.date_birthday,'%m-%d-%Y')  as date_birthday,
			a.date_birthday as birthday, i.".$field_name." as gender, h.session,
			hot.id_friend as hotlist, a.email, a.zipcode, a.headline
	   from ".USERS_TABLE." a
  left join ".COUNTRY_SPR_TABLE." b on b.id=a.id_country
  left join ".CITY_SPR_TABLE." c on c.id=a.id_city
  left join ".REFERENCE_LANG_TABLE." d on d.id_reference=a.id_nationality and d.table_key='".$multi_lang->TableKey(NATION_SPR_TABLE)."'
  left join ".REFERENCE_LANG_TABLE." e on e.id_reference=a.id_language_1 and e.table_key='".$multi_lang->TableKey(LANGUAGE_SPR_TABLE)."'
  left join ".REFERENCE_LANG_TABLE." f on f.id_reference=a.id_language_2 and f.table_key='".$multi_lang->TableKey(LANGUAGE_SPR_TABLE)."'
  left join ".REFERENCE_LANG_TABLE." g on g.id_reference=a.id_language_3 and g.table_key='".$multi_lang->TableKey(LANGUAGE_SPR_TABLE)."'
  left join ".REFERENCE_LANG_TABLE." i on i.id_reference=a.gender and i.table_key='".$multi_lang->TableKey(USER_TYPES_SPR_TABLE, 1)."'
  left join ".ACTIVE_SESSIONS_TABLE." h on h.id_user=a.id
  left join ".HOTLIST_TABLE." hot on hot.id_user='".$user_id."' and hot.id_friend = a.id
  left join ".REGION_SPR_TABLE." r on r.id=a.id_region
	  where a.id='".$user_id."'";
  $rs = $dbconn->Execute($strSQL);

  $row = $rs->GetRowAssoc(false);
  $data["country"] = stripslashes($row["country"]);
  $data["region"] = stripslashes($row["region"]);
  $data["city"] = stripslashes($row["city"]);
  $data["gender"] = $row["gender"];
  $data["nationality"] = stripslashes($row["nation"]);
  $data["language1"] = $row["lang1"];
  $data["language2"] = $row["lang2"];
  $data["language3"] = $row["lang3"];
  $data["languages"] = $data["language1"];
  if(strlen($data["languages"])>0 && strlen($data["language2"])>0) $data["languages"] .= ",";
  $data["languages"] .= $data["language2"];
  if(strlen($data["languages"])>0 && strlen($data["language3"])>0) $data["languages"] .= ",";
  $data["languages"] .= $data["language3"];

  // �������� �������������� ��������
  $strSQL = "Select m.".$field_name." as weight, k.".$field_name." as height
  from ".USERS_TABLE." a
  left join ".REFERENCE_LANG_TABLE." m on m.id_reference=a.id_weight and m.table_key='".$multi_lang->TableKey(WEIGHT_SPR_TABLE)."'
  left join ".REFERENCE_LANG_TABLE." k on k.id_reference=a.id_height and k.table_key='".$multi_lang->TableKey(HEIGHT_SPR_TABLE)."'
  where a.id='".$user_id."'";
  $rs = $dbconn->Execute($strSQL);
  $row = $rs->GetRowAssoc(false);
  $info["weight"] = $row["weight"];
  $info["height"] = $row["height"];

  $strSQL = "Select  b.id as id_spr, a.id_value, d.".$field_name." as sprname, c.".$field_name." as value, b.type
  from  ".DESCR_SPR_TABLE."  b
    left join ".DESCR_SPR_USER_TABLE." a on a.id_user='".$user_id."'  and b.id=a.id_spr
    left join ".REFERENCE_LANG_TABLE." d on d.id_reference=b.id  and d.table_key='".$multi_lang->TableKey(DESCR_SPR_TABLE)."'
    left join ".REFERENCE_LANG_TABLE." c on c.id_reference=a.id_value  and c.table_key='".$multi_lang->TableKey(DESCR_SPR_VALUE_TABLE)."'
    where length(b.name)>0
    order by b.sorter, c.id";
  $rs = $dbconn->Execute($strSQL);
  $i=0;
  while (!$rs->EOF)
        {
          $row = $rs->GetRowAssoc(false);
          if (isset($row["id_spr"])) $id_spr = $row["id_spr"];
                                else $id_spr ="";
          $id_value = $row["id_value"];
          if ( isset($spr_id) && isset($spr_id[$i])&& $id_spr != $spr_id[$i])  $i++;
          $spr_id[$i] = $id_spr;
          $info["info"][$i]["spr"] = $row["sprname"];
          $rs->MoveNext();
        }
  ini_set("display_errors", $old_display_errors);
  // ----------------------- ������� �� view profile.php
  // ��������� ���������� ������
  $res_info = "";
  if ($data["country"]     !="")  $res_info .= "  ".$win_lang["Country"].": " .strtolower($data["country"]);
  if ($data["region"]      !="")  $res_info .= "  ".$win_lang["Region"].": "  .strtolower($data["region"]);
  if ($data["city"]        !="")  $res_info .= "  ".$win_lang["City"].": "    .strtolower($data["city"]);
  if ($data["gender"]      !="")  $res_info .= "  ".$win_lang["Gender"].": "  .$win_lang[strtolower($data["gender"])];
  if ($data["nationality"] !="")  $res_info .= "  ".$win_lang["Nationality"].": "  .strtolower($data["nationality"]);
  if ($data["languages"]   !="")  $res_info .= "  ".$win_lang["Languages"].": "  .strtolower($data["languages"]);

  return $res_info;
}
//==============================================================================

//==============================================================================
// �������� md5 ��� �� ������ ������������
function get_user_passord_md5($user_id)
{
  global $dbconn;
  $strSQL = "select password from ".USERS_TABLE." where id='$user_id'";
  $rs = $dbconn->Execute($strSQL);
  if (($rs===false)||($rs->EOF))
     { // ���� ������ ������������
       print("<br>\nError: Database error 17"."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
       exit();
     }
  $row = $rs->GetRowAssoc(false);
  $pass = $row["password"];
  return $pass;
}
//==============================================================================

//==============================================================================
// ��������� ��������� ������������ - �� �����?
function test_user_site_state($user_id)
{
  global $dbconn;
  $strSQL = "select MAX(a.update_date) AS update_date from ".ACTIVE_SESSIONS_TABLE." a where a.id_user='$user_id'";

  $rs = $dbconn->Execute($strSQL);
  if (($rs===false)||($rs->EOF))
     { // ���� ������ ������������
       return -1;
     }
  $row = $rs->GetRowAssoc(false);
  if ($row["update_date"])
     { // ���� ����� �� ��������
       $update_date = strtotime($row["update_date"]);
       $now_date = time();
       $time_period = $update_date-$now_date+60*10; // ��������� ��� �� ����� 10 ����� �����
       if ($time_period<0) return -1;
                      else return $time_period;
     }
  return -1;
}
//==============================================================================

//==============================================================================
// ��������� ��������� ������������, ������, ������, �� �����
function test_user_online_state($user_id)
{
  global $dbconn;
  $strSQL = "select a.online_until from ".USERS_ONLINE_TABLE." a where a.user_id='$user_id'";
  $rs = $dbconn->Execute($strSQL);
  if (($rs===false)||($rs->EOF))
     { // ���� ������ ������������
       $site_state = test_user_site_state($user_id);
       if ($site_state>0) return  2;
                     else return NEVER_WERE_ONLINE;
     }
  $row = $rs->GetRowAssoc(false);
  $online_until = strtotime($row["online_until"]);
  $now_date = time();
  $time_period = $online_until-$now_date;
  if ($time_period<0)
     {
        $site_state = test_user_site_state($user_id);
        if ($site_state>0) return 2;
                     else  return  $time_period;
     }
  return  1;
}
//==============================================================================

//==============================================================================
// �������� ���������� ����� ������ ��� ��������� ������������
function add_lid($user_name, $lid)
{
  global $dbconn;
  clean_lid();
  $user_id = get_user_id($user_name);
  $date    = date('Y-m-d H:i:s',mktime());
  $strSQL = "INSERT INTO ".ENABLED_LID_TABLE." (user_id, lid, date_add) VALUES ('$user_id', '$lid', '$date')";
  $rs = $dbconn->Execute($strSQL);
  if ($rs===false)
     { // ������
       print("<br>\nError: Database error 15"."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
       exit();
     }
  return 0;
}
//==============================================================================

//==============================================================================
function delete_lid($lid)
{
	global $dbconn;
	clean_lid();
	$dbconn-> Execute("DELETE FROM ".ENABLED_LID_TABLE." WHERE lid='$lid'");
	return 0;
}
//==============================================================================

//==============================================================================
function get_user_lang()
{
  global $dbconn;
  $rs_smarty=$dbconn->Execute("select value from ".SETTINGS_TABLE." where name='default_lang'");
  if ($rs_smarty->fields[0])
       $config["default_lang"] = $rs_smarty->fields[0];
     else
       $config["default_lang"] = "1";

  @session_start();
  if (isset($_GET["language_code"]) && strlen($_GET["language_code"])>0)
     {
       $_SESSION["language_cd"] = $_GET["language_code"];     /// put in session
     }
  if (isset($_SESSION["language_cd"]) && strval($_SESSION["language_cd"])!="")
     {  /// from session
        $lang_code = $_SESSION["language_cd"];
     }
     else
     {
        $lang_code = $config["default_lang"];
     }

  $strSQL = "select name from ".LANGUAGE_TABLE." where id='".$lang_code."'";
  $rs = $dbconn->Execute($strSQL);
  $row = $rs->GetRowAssoc(false);
  if (isset($row["name"])&&($row["name"]!="")) return $row["name"];
               else return "english";
};
//==============================================================================

//==============================================================================
function delete_site_user($user_id)
{
  global $dbconn;
  $dbconn->Execute("delete from ".ENABLED_LID_TABLE." where user_id='".$user_id."'");
  $dbconn->Execute("delete from ".USERS_MESSAGES_TABLE." where from_id='".$user_id."' or to_id='".$user_id."'");
  $dbconn->Execute("delete from ".USER_CONTACT_LIST_TABLE." where user_id='".$user_id."' or view_user_id='".$user_id."'");
  $dbconn->Execute("delete from ".USERS_ONLINE_TABLE." where user_id='".$user_id."'");
  $dbconn->Execute("delete from ".LAST_ALERTED_IDS_TABLE." where user_id='".$user_id."'");
  $dbconn->Execute("delete from ".NEWS_STATE_TABLE." where user_id='".$user_id."'");
  $dbconn->Execute("delete from ".PERFECT_MATCH_TABLE." where user_id='".$user_id."' or perfect_id='".$user_id."'");
  $dbconn->Execute("delete from ".PROFILE_VISITED_TABLE." where id_user='".$user_id."' or id_visiter='".$user_id."'");
  return 0;
};
//==============================================================================

global $DEF_CODE_STRING_SEQUENCE;
$DEF_CODE_STRING_SEQUENCE = "kJXAeKFKoeswQfhfuvlZfGZYiqncTupC4gKbOVVg4mJNm3y2lBvBOaTlb51uagNv9kCys8yNvTUbyQUKGXxWDu0EwbPsrg2EQqNgS2CaDPhR3NJ9gimaBWam5PgRANc0SaE29rD";
//==============================================================================
function shifr_string($original, $code_shift=0)
{
  global $DEF_CODE_STRING_SEQUENCE;
  $rnd_seq = $DEF_CODE_STRING_SEQUENCE;
  $codec_symbols="";
  $len = strlen($original);
  $rnd_seq_len = strlen($rnd_seq);


  $i_size = $len/2;
  for ($i=0;$i<$i_size;$i++)
      {
         $a = $original[$i];
         $original[$i]=$original[$len-$i-1];
         $original[$len-$i-1]=$a;
      }
  $begin_shift = ($len+$code_shift)%$rnd_seq_len;
  $j=$begin_shift;
  for ($i=0;$i<$len;$i++)
      {
         $a  = $original[$i];
         $b  = $rnd_seq[$j];
         $c  = $a^$b;
         $c1 = (int) (ord($c)%16);
         $c2 = (int) (ord($c)/16);
         if ($c1>9) $c1=$c1-10+ord('A');
              else $c1=$c1+ord('0');
         if ($c2>9) $c2=$c2-10+ord('A');
              else $c2=$c2+ord('0');

         $codec_symbols=$codec_symbols.chr($c2);
         $codec_symbols=$codec_symbols.chr($c1);

         $j++;
         if ($j>$rnd_seq_len) $j=0;
      };
  return $codec_symbols;
}
//==============================================================================

//==============================================================================
function de_shifr_string($original, $code_shift=0)
{
  global $DEF_CODE_STRING_SEQUENCE;
  $rnd_seq = $DEF_CODE_STRING_SEQUENCE;
  $codec_symbols="";
  $len = strlen($original);
  $rnd_seq_len = strlen($rnd_seq);

  // ����������� �� ���������� ������������������
  $begin_shift = ((int)($len/2+$code_shift))%$rnd_seq_len;
  $j=$begin_shift;
  for ($i=0;$i<$len;$i+=2)
      {
         if (($i+1)>=$len) return "";
         $c1 = $original[$i+1];
         $c2 = $original[$i];
         if ($c1>'9') $c1=ord($c1)+10-ord('A');
                 else $c1=ord($c1)-ord('0');
         if ($c2>'9') $c2=ord($c2)+10-ord('A');
                 else $c2=ord($c2)-ord('0');
         $c = $c2*16+$c1;
         $b = $rnd_seq[$j];
         $a = chr($c)^$b;
         $codec_symbols=$codec_symbols.$a;
         $j++;
         if ($j>$rnd_seq_len) $j=0;
      }
  //
  $i_size = $len/4;
  for ($i=0;$i<$i_size;$i++)
      {
         $a = $codec_symbols[$i];
         $codec_symbols[$i]=$codec_symbols[$len/2-$i-1];
         $codec_symbols[$len/2-$i-1]=$a;
      }
  return $codec_symbols;
}
//==============================================================================

//==============================================================================
function Html2text($document)
{ // by SiMM, &#xHHHH; addition by John Profic
  // $document should contain an HTML document.
  // This will remove HTML tags, javascript sections
  // and white space. It will also convert some
  // common HTML entities to their text equivalent.
  $search = array ('@<script[^>]*?>.*?</script>@si', // Strip out javascript
                   '@<[\/\!]*?[^<>]*?>@si',          // Strip out HTML tags
                   '@([\r\n])[\s]+@',                // Strip out white space
                   '@&(quot|#34);@i',                // Replace HTML entities
                   '@&(amp|#38);@i',
                   '@&(lt|#60);@i',
                   '@&(gt|#62);@i',
                   '@&(nbsp|#160);@i',
                   '@&(iexcl|#161);@i',
                   '@&(cent|#162);@i',
                   '@&(pound|#163);@i',
                   '@&(copy|#169);@i');                    // evaluate as php

  $replace = array ('',
                   '',
                   '\1',
                   '"',
                   '&',
                   '<',
                   '>',
                   ' ',
                   chr(161),
                   chr(162),
                   chr(163),
                   chr(169));

  $text = preg_replace($search, $replace, $document);
  return $text;
}
//==============================================================================
?>