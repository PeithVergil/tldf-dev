<?php
/**
 * ----------------------------------------------
 * URL: http://www.pilotgroup.net
 * ----------------------------------------------
 */
    
function create_javascript_colors_array() 
{
    global $pollvars, $poll_source_array;
    $images_blank = array();
    foreach ($poll_source_array as $color)
            {
              $images_blank[] = $color." = new Image(); ".$color.".src = '".$pollvars["base_gif"]."/".$color.".gif';";
            };
    $images_blank[] = "blank = new Image(); blank.src = '".$pollvars["base_gif"]."/blank.gif';";            
    $java_script = implode("\r\n        ", $images_blank);       
    return $java_script;
};

function create_javascript_reset_colors_array($i_start, $i_end) 
{
    global $pollvars, $poll_source_array;
    $r_color = array();
    for ($i=$i_start;$i<$i_end;$i++)
        { 
          $r_color[$i] ='';
          $r_color[$i].='if (document.forms[0].color';
          $r_color[$i].=$i;
          $r_color[$i].=') {eval("';
          $r_color[$i].='document.bar';
          $r_color[$i].=$i;
          $r_color[$i].='.src=\'';
          $r_color[$i].=$pollvars["base_gif"];
          $r_color[$i].='/"+document.forms[0].color';
          $r_color[$i].=$i;
          $r_color[$i].='.value+".gif\';");};';
        };
    $java_script = implode("\r\n        ", $r_color);       
    return $java_script;
};

function admin_delete_poll() 
{
    if (isset($_REQUEST["poll_id"])) $poll_id = $_REQUEST["poll_id"];
    if (!isset($poll_id) || !is_valid_poll_id($poll_id)) 
     {
       // Не установлен номер для удаления
       return  ;
     }
    global $POLL_CLASS, $POLLTBL;
    $POLL_CLASS["db"]->query("DELETE FROM $POLLTBL[poll_data] where (poll_id = '$poll_id')");
    $POLL_CLASS["db"]->query("DELETE FROM $POLLTBL[poll_index] where (poll_id = '$poll_id')");
    $POLL_CLASS["db"]->query("DELETE FROM $POLLTBL[poll_log] where (poll_id = '$poll_id')");
    $POLL_CLASS["db"]->query("DELETE FROM $POLLTBL[poll_comment] where (poll_id = '$poll_id')");
}

function admin_poll_list() 
{
    global $poll_module_path;
    global $config, $smarty;
    global $POLL_CLASS, $POLLTBL, $auth, $pollvars, $lang_poll, $weekday_poll, $months_poll;
    $smarty->assign("poll_admin_panel", "index");
    
    $POLL_CLASS["db"]->fetch_array($POLL_CLASS["db"]->query("select count(*) as total from $POLLTBL[poll_index]"));
    $total = $POLL_CLASS["db"]->record['total'];
    $time_offset = $pollvars["time_offset"]*3600;
    list($wday,$mday,$month,$year,$hour,$minutes) = split("( )",date("w j n Y H i",time()+$time_offset));
    $newdate = "$weekday_poll[$wday], $mday ".$months_poll[$month-1]." $year $hour:$minutes";

    $results = $POLL_CLASS["db"]->query("select * from $POLLTBL[poll_index] order by poll_id desc");
    $all_polls = array();
    $one_poll  = array();
    while ($row = $POLL_CLASS["db"]->fetch_array($results)) 
          {
              $one_poll["poll_id"] = $row['poll_id'];
              $one_poll["question"] = $row['question'];
              $one_poll["date"] = date("j-M-Y",$row['timestamp']+$time_offset);
              if ($row['expire']==0) 
                 {
                  $one_poll["poll_expire"] = "0";                   
                  $one_poll["poll_expire_date"] = $lang_poll["IndexNever"];
                 }
                  else 
                 {
                  if (time()>$row['exp_time'])
                     {
                       $one_poll["poll_expire"] = "1";                   
                       $one_poll["poll_expire_date"] = $lang_poll["IndexExpire"];
                     }
                     else
                     {
                       $one_poll["poll_expire"] = "2";
                       $expr_date = date("j-M-Y",$row['exp_time']+$time_offset)." (".round(($row["exp_time"]-time())/86400).")";
                       $one_poll["poll_expire_date"] = $expr_date;
                     }
                 }
              $one_poll["days"]    = (int) ((time()-$row['timestamp']+$time_offset)/86400);
              $one_poll["status"]  = $row['status'];
              $one_poll["poll_for"] = $row['poll_for'];
			  $one_poll["logging"] = $row['logging'];
              $one_poll["comments"]= $row['comments'];
              if ($row['status'] == 1) 
                 {
                   $one_poll["image"] = $pollvars["base_gif"].'/folder.gif';
                   $one_poll["alt"]   = $lang_poll["EditOn"];
                 }
                 elseif
                 ($row['status'] == 2)
                 {
                   $one_poll["image"] = $pollvars["base_gif"].'/hidden.gif';
                   $one_poll["alt"]   = $lang_poll["EditHide"];
                 }
                 else
                 {
                   $one_poll["image"] = $pollvars["base_gif"].'/lock.gif';
                   $one_poll["alt"]   = $lang_poll["EditOff"];
                 }
              $one_poll["image2"] = ($row['logging']  == 1)  ? "$pollvars[base_gif]/log.gif" : "$pollvars[base_gif]/log_off.gif";
              $one_poll["image3"] = ($row['comments'] == 1)  ? "$pollvars[base_gif]/reply.gif" : "$pollvars[base_gif]/co_dis.gif";
              $one_poll["image4"] = ($row['status']   == 2)  ? "$pollvars[base_gif]/text_off.gif" : "$pollvars[base_gif]/text.gif";
              
              $all_polls[] = $one_poll;
          }
    $smarty->assign("all_polls", $all_polls);
    $smarty->assign("lang_poll", $lang_poll); 
    $smarty->assign("smarty_script", 
'    <script language="Javascript">
     <!--
     function del_entry(entry) 
     {
        if (window.confirm("'.$lang_poll["Confirm"].'")) 
           {
            window.location.href = "http://"+window.location.host+window.location.pathname+"?action=delete&poll_id="+entry+"&no_cache="+Math.random();
           }
     }
     // -->
     </script>');     
}

function admin_poll_new()
{
    global $poll_module_path;
    global $config, $smarty;
    global $POLL_CLASS, $auth, $pollvars, $color_array_poll, $poll_source_array, $lang_poll, $POLLTBL;
    $smarty->assign("poll_admin_panel", "poll_new");
    
    $row['status'] = 1;   // status  - enabled
    $row['logging'] = 0;  // logginf - on
    $row['comments'] = 0; // allo comments - off
    $row['expire'] = 0;   // expire - off
    $row['exp_time'] = 0; // expire time - ''
    
    $status_0 = ($row['status'] == 0) ? "selected" : "";
    $status_1 = ($row['status'] == 1) ? "selected" : "";
    $status_2 = ($row['status'] == 2) ? "selected" : "";
    $smarty->assign("status_0", $status_0);
    $smarty->assign("status_1", $status_1);
    $smarty->assign("status_2", $status_2);
    
    $logging_0 = ($row['logging'] == 0) ? "selected" : "";
    $logging_1 = ($row['logging'] == 1) ? "selected" : "";
    $smarty->assign("logging_0", $logging_0);
    $smarty->assign("logging_1", $logging_1);
    
    $poll_comments = ($row['comments'] == 1) ? "checked" : "";
    $poll_expire = ($row['expire'] == 0) ? "checked" : "";
    $smarty->assign("poll_comments", $poll_comments);
    $smarty->assign("poll_expire",   $poll_expire);
    
    $end_id = 1+$pollvars['def_options'];
    for ($id=1; $id<$end_id; $id++)
        {
           $ids[]=$id;
        }
    $opt_colors = array();
    for ($j=0; $j<sizeof($poll_source_array); $j++) 
        {
          $opt_colors[] = "<option value=\"$poll_source_array[$j]\">$color_array_poll[$j]</option>\n";
        }

    $smarty->assign("ids",        $ids);
    $smarty->assign("opt_colors",  $opt_colors);
  
    $expiration = round (($row['exp_time']-time())/86400);
    if ($expiration<=0) $expiration = 0;
    $smarty->assign("expiration", $expiration); 
    $timestamp = '';
    $smarty->assign("lang_poll",  $lang_poll); 
    $smarty->assign("pollvars",   $pollvars); 

    $smarty->assign("smarty_script",
 '      <script language="Javascript">
        '.create_javascript_colors_array().'
        function ChangeBar(sel,img) 
        {
          eval("document.bar"+img+".src="+sel+".src");
        }
        
        function trim(value) 
        {
         startpos=0;
         while ((value.charAt(startpos)==" ")&&(startpos<value.length)) 
               {
                 startpos++;
               }
         if (startpos==value.length) 
            {
              value="";
            }
             else
            {
              value=value.substring(startpos,value.length);
              endpos=(value.length)-1;
              while (value.charAt(endpos)==" ") 
                    {
                      endpos--;
                    }
              value=value.substring(0,endpos+1);
            }
         return(value);
        }
        function CheckForm() 
        {
         document.forms[0].question.value = trim(document.forms[0].question.value);
         document.forms[0].exp_time.value = trim(document.forms[0].exp_time.value);
         document.forms[0].elements[2].value = trim(document.forms[0].elements[2].value);
  
         if (document.forms[0].question.value == "") 
            {
             alert("'.$lang_poll["NewNoQue"].'!");
             document.forms[0].question.focus();
             return false;
            }
         if (document.forms[0].elements[2].value == "") 
            {
             alert("'.$lang_poll["NewNoOpt"].'!");
             document.forms[0].elements[2].focus();
             return false;
            }
         if (document.forms[0].expire.checked == false) 
            {
              if (!(document.forms[0].exp_time.value >= 0) || document.forms[0].exp_time.value == "") 
                 {
                   alert("'.$lang_poll["SetEmpty"].'");
                   document.forms[0].exp_time.focus();
                   return false;
                 }
            }
         return true;   
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
        function ResetColors()
        {
          document.forms[0].reset();
          '.create_javascript_reset_colors_array(1, $end_id).'
        }
        // -->
        </script>');
}

function admin_create_poll() 
{
    global $poll_module_path;
    global $config, $smarty; 
    global $POLL_CLASS, $POLLTBL, $pollvars;
    
    $option_id = array();
    $color     = array(); 
    
    // Получаем параметры
    $last_id = $pollvars['def_options'];
    for ($i=1; $i < $last_id; $i++) 
        {
          $id_name = "option_id".$i;
          if (isset($_REQUEST[$id_name]))    $option_id[$i] = $_REQUEST[$id_name];
          $color_name = "color".$i;
          if (isset($_REQUEST[$color_name])) $color[$i] = $_REQUEST[$color_name];
        }
    if (isset($_REQUEST["status"]))   $status = $_REQUEST["status"];
    if (isset($_REQUEST["logging"]))  $logging = $_REQUEST["logging"];
    if (isset($_REQUEST["question"])) $question = $_REQUEST["question"];
    if (isset($_REQUEST["exp_time"])) $exp_time = $_REQUEST["exp_time"];
	if (isset($_REQUEST["poll_for"]))   $poll_for = $_REQUEST["poll_for"];
	if (isset($_REQUEST["expire"]))   $expire = $_REQUEST["expire"];  
	if (isset($_REQUEST["comments"])) $comments = $_REQUEST["comments"];    

    
    $timestamp = time();
    if (!isset($expire))   $expire=1;
    if (!isset($comments)) $comments=0;
    if (!isset($exp_time)) $exp_time=$timestamp;
                      else $exp_time=$timestamp+$exp_time*86400;
    
    $POLL_CLASS["db"]->query("INSERT INTO $POLLTBL[poll_index] (question,timestamp,status,logging,exp_time,poll_for,expire,comments) VALUES ('$question','$timestamp','$status','$logging','$exp_time','$poll_for','$expire','$comments')");
    $sql_result = $POLL_CLASS["db"]->query("SELECT poll_id FROM $POLLTBL[poll_index] WHERE timestamp=$timestamp");
    $POLL_CLASS["db"]->fetch_array($sql_result);
    $poll_id = $POLL_CLASS["db"]->record['poll_id'];
    for($i=1; $i <= sizeof($option_id); $i++) 
    {
        $option_id[$i] = trim($option_id[$i]);
        if (!empty($option_id[$i])) 
           {
             $POLL_CLASS["db"]->query("INSERT INTO $POLLTBL[poll_data] (poll_id, option_id, option_text, color, votes) VALUES('$poll_id', '$i', '$option_id[$i]','$color[$i]',0)");
           }
    }
}

function admin_poll_index() 
{
  global $config;
  if (!isset($_REQUEST["action"])) $action='';
                              else $action=$_REQUEST["action"];
    
  switch ($action) 
         {
            case "delete":
                admin_delete_poll();
                admin_poll_list();
                break;
        
            case "new":
                admin_poll_new();
                break;
        
            case "create":
                admin_create_poll();
                admin_poll_list();
                break;
        
            default:
                admin_poll_list();
         }
}

?>