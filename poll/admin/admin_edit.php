<?php
/**
 * ----------------------------------------------
 * URL: http://www.pilotgroup.net
 * ----------------------------------------------
 */
global $poll_source_array;
$poll_source_array = array(
    "aqua","blue","brown","darkgreen","gold","green","grey","orange","pink","purple","red","yellow"
);

function add_options($poll_id)
{
    global $pollvars;
    global $POLL_CLASS, $POLLTBL;
    global $option_id, $color;
    
    // Получаем последий id
    $POLL_CLASS["db"]->free_result($POLL_CLASS["db"]->result);
    $data = $POLL_CLASS["db"]->fetch_array($POLL_CLASS["db"]->query("select max(option_id) as option_id from $POLLTBL[poll_data] where (poll_id = '$poll_id')"));
    $POLL_CLASS["db"]->free_result($POLL_CLASS["db"]->result);
    $last_id = $data["option_id"]+1;
    $end_id   = $last_id+$pollvars['def_options'];
    
    for ($i=$last_id; $i < $end_id; $i++) 
        {
          $id_name = "option_id".$i;
          if (isset($_REQUEST[$id_name])) $option_id = $_REQUEST[$id_name];
                                     else $option_id = '';
          $color_name = "color".$i;
          if (isset($_REQUEST[$color_name])) $color = $_REQUEST[$color_name];
                                        else $color = '';
          if (!empty($option_id)) 
             {
               $POLL_CLASS["db"]->query("INSERT INTO $POLLTBL[poll_data] (poll_id, option_id, option_text, color, votes) VALUES('$poll_id', '$i', '$option_id','$color',0)");
             }
        }
}

function save($poll_id) 
{
    global $POLL_CLASS, $POLLTBL;

    // Получаем последий id
    $POLL_CLASS["db"]->free_result($POLL_CLASS["db"]->result);
    $data = $POLL_CLASS["db"]->fetch_array($POLL_CLASS["db"]->query("select max(option_id) as option_id from $POLLTBL[poll_data] where (poll_id = '$poll_id')"));
    $POLL_CLASS["db"]->free_result($POLL_CLASS["db"]->result);
    $last_id = $data["option_id"]+1;
    
    $option_id = array();
    $votes     = array();
    $color     = array(); 
    
    // Получаем параметры
    for ($i=1; $i < $last_id; $i++) 
        {
          $id_name = "option_id".$i;
          if (isset($_REQUEST[$id_name]))    $option_id[$i] = $_REQUEST[$id_name];
          $votes_name = "votes".$i;
          if (isset($_REQUEST[$votes_name])) $votes[$i] = $_REQUEST[$votes_name];
          $color_name = "color".$i;
          if (isset($_REQUEST[$color_name])) $color[$i] = $_REQUEST[$color_name];
        }
    if (isset($_REQUEST["status"]))   $status = $_REQUEST["status"];
    if (isset($_REQUEST["logging"]))  $logging = $_REQUEST["logging"];
    if (isset($_REQUEST["question"])) $question = $_REQUEST["question"];
    if (isset($_REQUEST["exp_time"])) $exp_time = $_REQUEST["exp_time"];
    if (isset($_REQUEST["poll_for"])) $poll_for = $_REQUEST["poll_for"];
	if (isset($_REQUEST["expire"]))   $expire = $_REQUEST["expire"];    
    if (isset($_REQUEST["comments"])) $comments = $_REQUEST["comments"];    
    
    if (!isset($expire))   $expire=1;
    if (!isset($comments)) $comments=0;
    $exp_time=time()+$exp_time*86400;
    if (!empty($question)) 
    {
        $POLL_CLASS["db"]->query("UPDATE $POLLTBL[poll_index] set question='$question', status='$status', logging='$logging', exp_time='$exp_time', expire='$expire', poll_for='$poll_for', comments='$comments' where (poll_id = '$poll_id')");
        $POLL_CLASS["db"]->query("select max(option_id) as max_option from $POLLTBL[poll_data] where (poll_id = '$poll_id')");
        $data = $POLL_CLASS["db"]->fetch_array($POLL_CLASS["db"]->result);
        for($i=1; $i <= $data["max_option"]; $i++) 
        {
            if (!isset($option_id["$i"])) 
            {
                continue;
            }
            if (!empty($option_id[$i])) 
            {
                if (!eregi("^[0-9]+$", $votes[$i])) 
                {
                    $votes[$i] = 0;
                }
                $POLL_CLASS["db"]->query("UPDATE $POLLTBL[poll_data] set option_text='$option_id[$i]', color='$color[$i]', votes='$votes[$i]' where (poll_id = '$poll_id' and option_id = '$i')");
            } elseif (sizeof($option_id) > 2) 
            {
                $POLL_CLASS["db"]->query("DELETE FROM $POLLTBL[poll_data] where (poll_id = '$poll_id' and option_id = '$i')");
            }
        }
    }
}

function poll_extend($poll_id) 
{
    global $poll_module_path;
    global $config, $smarty;
    global $POLL_CLASS, $POLLTBL, $poll_source_array, $color_array_poll, $lang_poll, $pollvars, $auth;
    $smarty->assign("poll_admin_panel", "poll_extend");
    
    // Получаем вопрос
    $row = $POLL_CLASS["db"]->fetch_array($POLL_CLASS["db"]->query("SELECT question as question FROM $POLLTBL[poll_index] WHERE (poll_id = '$poll_id')"));
    $poll_question = $row['question'];
    $smarty->assign("poll_question", $poll_question);
    
    // Получаем последий id
    $POLL_CLASS["db"]->free_result($POLL_CLASS["db"]->result);
    $data = $POLL_CLASS["db"]->fetch_array($POLL_CLASS["db"]->query("select max(option_id) as option_id from $POLLTBL[poll_data] where (poll_id = '$poll_id')"));
    $POLL_CLASS["db"]->free_result($POLL_CLASS["db"]->result);

    $start_id = $data["option_id"]+1;
    $end_id   = $start_id+$pollvars['def_options'];
    for ($id=$start_id; $id<$end_id; $id++)
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
    
    $smarty->assign("lang_poll",  $lang_poll); 
    $smarty->assign("pollvars",   $pollvars); 
    $smarty->assign("poll_id",    $poll_id);    
    $smarty->assign("smarty_script",
 '      <script language="Javascript">
        '.create_javascript_colors_array().'
        function ChangeBar(sel,img) 
        {
          eval("document.bar"+img+".src="+sel+".src");
        }
        function ResetColors()
        {
          document.forms[0].reset();
          '.create_javascript_reset_colors_array($start_id, $end_id).'
        }
        function SubmitMyForm() 
        {
          document.forms[0].submit();
        }  
        // -->
        </script>');

}

function poll_edit($poll_id) 
{
    global $poll_module_path;
    global $config, $smarty;
    global $POLL_CLASS, $auth, $pollvars, $color_array_poll, $poll_source_array, $lang_poll, $POLLTBL;
    $smarty->assign("poll_admin_panel", "poll_edit");
    
    $row = $POLL_CLASS["db"]->fetch_array($POLL_CLASS["db"]->query("SELECT * FROM $POLLTBL[poll_index] WHERE (poll_id = '$poll_id')"));
    $poll_question = $row['question'];
    $smarty->assign("poll_question", $poll_question);
           
    $POLL_CLASS["db"]->free_result($POLL_CLASS["db"]->result);
    $POLL_CLASS["db"]->query("select * from $POLLTBL[poll_data] where (poll_id = '$poll_id') order by option_id asc");

    $poll_options = '';
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
    $poll_for = $row['poll_for'];
	$poll_expire = ($row['expire'] == 0) ? "checked" : "";
    $smarty->assign("poll_comments", $poll_comments);
	$smarty->assign("poll_for", $poll_for);
    $smarty->assign("poll_expire",   $poll_expire);
    $one_option  = array();
    $all_options = array();
    $i=1;
    $start_id=10000000;
    $end_id=1;
    while ($one_option = $POLL_CLASS["db"]->fetch_array($POLL_CLASS["db"]->result)) 
          {
            $i++;
            if ($one_option["option_id"]<$start_id) $start_id=$one_option["option_id"];
            if ($one_option["option_id"]>=$end_id)   $end_id=$one_option["option_id"]+1;
             
            $one_option["opt_color"] = array();
            for ($j=0; $j<sizeof($poll_source_array); $j++) 
                {
                  if ($one_option["color"] == $poll_source_array["$j"])  
                     {
                       $one_option["opt_color"][] = "<option value=\"$poll_source_array[$j]\" selected>$color_array_poll[$j]</option>\n";
                     }
                      else
                     {
                       $one_option["opt_color"][] = "<option value=\"$poll_source_array[$j]\">$color_array_poll[$j]</option>\n";
                     }
                }
            $one_option["color_file"] = $pollvars["base_gif"]."/".$one_option["color"].".gif";
            $all_options[]=$one_option;
          }
    $smarty->assign("all_options", $all_options);       
    $expiration = round (($row['exp_time']-time())/86400);
    if ($expiration<=0) $expiration = 0;
    $smarty->assign("expiration", $expiration); 
    $timestamp = '';
    $smarty->assign("lang_poll",  $lang_poll); 
    $smarty->assign("pollvars",   $pollvars); 
    $smarty->assign("poll_id",    $poll_id);

    $smarty->assign("smarty_script",
 '      <script language="Javascript">
        '.create_javascript_colors_array().'
        function ChangeBar(sel,img) 
        {
          eval("document.bar"+img+".src="+sel+".src");
        }
        
        function ResetPoll() 
        {
          for (i=4; i<document.forms[0].elements.length-6; i+=3) 
              {
                document.forms[0].elements[i].value = "0";
              }
        }
        function CheckDays() 
        {
          if (!(document.poll.exp_time.value >= 0)) 
             {
               alert("$lang[SetEmpty]");
               document.poll.exp_time.focus();
               return false;
             }
          return true;   
        }
        function SubmitMyForm() 
        {
         res = CheckDays();
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
          '.create_javascript_reset_colors_array($start_id, $end_id).'
        }        
        
        // -->
        </script>');
}
    
function is_valid_poll_id($poll_id) 
{
    global $POLL_CLASS, $POLLTBL;
    if ($poll_id>0) {
        $POLL_CLASS["db"]->fetch_array($POLL_CLASS["db"]->query("SELECT poll_id FROM $POLLTBL[poll_index] WHERE poll_id=$poll_id"));
        return ($POLL_CLASS["db"]->record['poll_id']) ? true : false;
    } else {
        return false;
    }
}

function admin_poll_edit()
{
  global $config;
  if (isset($_REQUEST["poll_id"])) $poll_id = $_REQUEST["poll_id"];
  if (!isset($poll_id) || !is_valid_poll_id($poll_id)) 
     {
       $redirect = $config["server"].$config["site_root"]."/admin/admin_poll.php";
       header ("Location: $redirect");
       exit();
     }
  if (!isset($_REQUEST["action"])) $action='';
                              else $action=$_REQUEST["action"];
    
  switch ($action) 
         {
            case "save":
                save($poll_id);
                poll_edit($poll_id);
                break;
        
            case "extend":
                poll_extend($poll_id);
                break;
        
            case "add":
                add_options($poll_id);
                poll_edit($poll_id);
                break;
        
            default:
                poll_edit($poll_id);
         }
}     
?>