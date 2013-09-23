<?php
/**
 * ----------------------------------------------
 * URL: http://www.pilotgroup.net
 * ----------------------------------------------
 */

function get_lang_list($dir) 
{
    $lang_poll_list = '';
    chdir("$dir");
    $hnd = opendir(".");
    while ($file = readdir($hnd)) {
        if(is_file($file)) {
            $lang_polllist[] = $file;
        }
    }
    closedir($hnd);
    if ($lang_polllist) {
        asort($lang_polllist);
        while (list ($key, $file) = each ($lang_polllist)) {
            if (ereg(".php|.php3",$file,$regs)) {
                $lang_poll_list .= "<option value=\"".$file."\">".str_replace("$regs[0]","","$file")."</option>\n";
            }
        }
    }
    return $lang_poll_list;
}

function addspecialchars($input='') 
{
    if(is_array($input)) {
        reset($input);
        while (list($var,$value) = each($input)) {
            $input[$var] = htmlspecialchars($value);
        }
        return $input;
    } else {
        return false;
    }
}

function admin_poll_settings()
{
  global $PHP_SELF;
  global $poll_module_path;
  global $config, $smarty;
  global $POLL_CLASS, $POLLTBL, $auth, $pollvars, $lang_poll, $weekday_poll, $months_poll;
  $smarty->assign("poll_admin_panel", "settings");

  if ((isset($_REQUEST["action"]))&&($_REQUEST["action"]!=null)) $action = $_REQUEST["action"];
                                                           else  $action = "";
  if ($action == "update") 
     {
      // Получаем параметры  
      if ((isset($_REQUEST["cfg_lang"]))&&($_REQUEST["cfg_lang"]!=null)) $cfg["lang"] = $_REQUEST["cfg_lang"];
                                                                   else  $cfg["lang"] = "";
      if ((isset($_REQUEST["cfg_check_ip"]))&&($_REQUEST["cfg_check_ip"]!=null)) $cfg["check_ip"] = 1;
                                                                           else  $cfg["check_ip"] = 0;                       
      if ((isset($_REQUEST["cfg_lock_timeout"]))&&($_REQUEST["cfg_lock_timeout"]!=null)) $cfg["lock_timeout"] = $_REQUEST["cfg_lock_timeout"];
                                                                                   else  $cfg["lock_timeout"] = "";                                                                                                                                              
      if ((isset($_REQUEST["cfg_check_user_name"]))&&($_REQUEST["cfg_check_user_name"]!=null)) $cfg["check_uname"] = 1;
                                                                                         else  $cfg["check_uname"] = 0;                                                                                                               
      if (!eregi(".php|.php3", $cfg["lang"])) 
      {
          $cfg["lang"] = "english.php";
      }
      $POLL_CLASS["db_input"] = new input2db();
      $result = $POLL_CLASS["db_input"]->update_db_row($POLLTBL["poll_config"],$cfg,"config_id",1);
      if ($result) 
      {
          $pollvars = $POLL_CLASS["db"]->fetch_array($POLL_CLASS["db"]->query("SELECT * FROM $POLLTBL[poll_config]"));
          $pollvars['SELF'] = basename($PHP_SELF);
          $POLL_CLASS["db"]->free_result($POLL_CLASS["db"]->result);
          unset($lang_poll);
          include ($poll_module_path."/lang/".$pollvars["lang"]);
      } 
     }
  $lang_polllist = get_lang_list($poll_module_path."/lang");
  $check_ip = ($pollvars["check_ip"] != 0) ? "checked" : "";
  $check_uname= ($pollvars["check_uname"] != 0) ? "checked" : "";
  
  $smarty->assign("lang_polllist",  $lang_polllist); 
  $smarty->assign("check_ip",  $check_ip); 
  $smarty->assign("check_uname",  $check_uname); 
  $smarty->assign("no_ip_check",  $no_ip_check); 
  
  $smarty->assign("lang_poll",  $lang_poll); 
  $smarty->assign("pollvars",   $pollvars);  
  $smarty->assign("smarty_script",
 '      <script language="Javascript">
        <!-- 
        function trim(value) 
        {
         startpos=0;
         while((value.charAt(startpos)==" ")&&(startpos<value.length)) 
         {
           startpos++;
         }
         if(startpos==value.length) 
         {
           value="";
         } else 
         {
           value=value.substring(startpos,value.length);
           endpos=(value.length)-1;
           while(value.charAt(endpos)==" ") 
           {
             endpos--;
           }
           value=value.substring(0,endpos+1);
         }
         return(value);
        }
        function CheckForm() 
        {
         var found = 0;
         for (i=0; i<document.forms[0].elements.length; i++) 
         {
           document.forms[0].elements[i].value = trim(document.forms[0].elements[i].value);
           if (document.forms[0].elements[i].value == "") 
           {
             alert("'.$lang_poll["SetEmpty"].'");
             document.forms[0].elements[i].focus();
             found=1;
             break;
           }
         }
         return (found == 1) ? false : true;
        }        
        function SubmitMyForm() 
        {
         res = CheckForm();
         if (res==true)
            {
              document.forms[0].submit();
            }
            else
            {
              // Doing nothing
            }
        }                        
        function ResetMyForm() 
        {
         document.forms[0].reset();
        }                        
        // -->        
        </script>');  
}
?>