<?php
/**
 * ----------------------------------------------
 * URL: http://www.pilotgroup.net
 * ----------------------------------------------
 */

function admin_poll_comment()
{
  if (isset($_REQUEST["poll_id"])) $poll_id = $_REQUEST["poll_id"];
  if (!isset($poll_id) || !is_valid_poll_id($poll_id)) 
     {
       $redirect = $config["server"].$config["site_root"]."\admin\admin_poll.php";
       header ("Location: $redirect");
       exit();
     } 
 if ((isset($_REQUEST["entry"])) && ($_REQUEST["entry"]!=null)) $entry = $_REQUEST["entry"];
                                                           else $entry = 0;    

 if (isset($_REQUEST["action"])) $action = $_REQUEST["action"];
                            else $action='';
 if (isset($_GET["mess_id"])) $mess_id=$_GET["mess_id"];
 
 global $poll_module_path;
 global $config, $smarty;
 global $POLL_CLASS, $POLLTBL, $auth, $pollvars, $lang_poll, $weekday_poll, $months_poll;
 $smarty->assign("poll_admin_panel", "comments");

 if ($action=="delete" and isset($mess_id) and isset($poll_id)) 
    {
     $POLL_CLASS["db"]->query("DELETE FROM $POLLTBL[poll_comment] where (com_id = '$mess_id' and poll_id='$poll_id')");
    }

  $record = $POLL_CLASS["db"]->fetch_array($POLL_CLASS["db"]->query("SELECT question FROM $POLLTBL[poll_index] WHERE (poll_id = '$poll_id')"));
  $poll_question = $record['question'];
  $smarty->assign("poll_question", $poll_question);
  
  $POLL_CLASS["db"]->free_result($POLL_CLASS["db"]->result);
  $time_offset = $pollvars["time_offset"]*3600;
  $POLL_CLASS["db"]->fetch_array($POLL_CLASS["db"]->query("select count(*) as total from $POLLTBL[poll_comment] WHERE (poll_id = '$poll_id')"));
  $total_commens = $POLL_CLASS["db"]->record['total'];
  $smarty->assign("total_commens", $total_commens);
  
  $next_page = $entry+$pollvars["entry_pp"];
  $prev_page = $entry-$pollvars["entry_pp"];
  $navigation ='';
  if ($prev_page >= 0)
     {
       $navigation = "<img src=\"$pollvars[base_gif]/back.gif\" width=\"16\" height=\"14\">&nbsp;<a href=\"$pollvars[SELF]?session=$auth[session]&uid=$auth[uid]&poll_id=$poll_id&entry=$prev_page\">$lang_poll[NavPrev]</a>\n";
     }
  if ($next_page < $total_commens) 
     {
       $navigation = $navigation. " &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$pollvars[SELF]?session=$auth[session]&uid=$auth[uid]&poll_id=$poll_id&entry=$next_page\">$lang_poll[NavNext]</a>&nbsp;<img src=\"$pollvars[base_gif]/next.gif\" width=\"16\" height=\"14\">\n";
     }

  $all_comments = array();
  $one_comment  = array();
   
  $results = $POLL_CLASS["db"]->query("select cmnt.*, datinguser.login from $POLLTBL[poll_comment] cmnt, ".USERS_TABLE." datinguser WHERE ((cmnt.poll_id = '$poll_id') and (cmnt.user_id = datinguser.id)) order by cmnt.com_id desc"); // order by com_id desc limit $entry, $pollvars[entry_pp]
  while ($one_comment = $POLL_CLASS["db"]->fetch_array($results)) 
        {
          $one_comment["date"]    = date("j-M-Y H:i",$one_comment['time']+$time_offset);
          $one_comment["message"] = nl2br(htmlspecialchars($one_comment['message']));
          
          if (eregi("Opera",$one_comment['browser'])) 
             {
                $one_comment["browser_ico"] = "$pollvars[base_gif]/opera.gif";
             } elseif (eregi("MSIE",$one_comment['browser'])) 
             {
                $one_comment["browser_ico"] = "$pollvars[base_gif]/msie.gif";
             } elseif (eregi("Mozilla",$one_comment['browser'])) 
             {
                $one_comment["browser_ico"] = "$pollvars[base_gif]/netscape.gif";
             } else 
             {
                $one_comment["browser_ico"] = "$pollvars[base_gif]/unknown.gif";
             }
          $all_comments[]=$one_comment;   
        }
  $smarty->assign("all_comments", $all_comments);
  $smarty->assign("lang_poll", $lang_poll);
  $smarty->assign("pollvars",   $pollvars);
  $smarty->assign("poll_id",    $poll_id);
  $smarty->assign("smarty_script",
 '      <script language="Javascript">
        function del_entry(entry) 
        {
          if (window.confirm("'.$lang_poll["ComDel"].'")) 
             {
              window.location.href = "http://"+window.location.host+window.location.pathname+"?sel=comment&action=delete&poll_id='.$poll_id.'&mess_id="+entry+"&no_cache="+Math.random();
             }
        }     
        // -->
        </script>');      
        
}
?>