<?php
/**
 * ----------------------------------------------
 * URL: http://www.pilotgroup.net
 * ----------------------------------------------
 */

function admin_poll_stats()
{
  global $poll_module_path;
  global $config, $smarty;
  global $POLL_CLASS, $POLLTBL, $auth, $pollvars, $lang_poll, $weekday_poll, $months_poll;
  $smarty->assign("poll_admin_panel", "stats");
    
  if (isset($_REQUEST["poll_id"])) $poll_id = $_REQUEST["poll_id"];
  if (!isset($poll_id) || !is_valid_poll_id($poll_id)) 
     {
       $redirect = $config["server"].$config["site_root"]."\admin\admin_poll.php";
       header ("Location: $redirect");
       exit();
     }
     
      
  if ((isset($_REQUEST["action"]))&&($_REQUEST["action"]!=null)) $action = $_REQUEST["poll_id"];
                                                           else  $action = "";

                                                           
  if ($action=="reset" and isset($poll_id)) 
     {
      $POLL_CLASS["db"]->query("DELETE FROM $POLLTBL[poll_log] where (poll_id = '$poll_id')");
     }
  
  $row = $POLL_CLASS["db"]->fetch_array($POLL_CLASS["db"]->query("SELECT * FROM $POLLTBL[poll_index] WHERE (poll_id = '$poll_id')"));
  $logging = $row["logging"];
  $smarty->assign("logging",  $logging);
  $POLL_CLASS["db"]->free_result($POLL_CLASS["db"]->result);
  $time_offset = $pollvars["time_offset"]*3600;
  $poll_sum = $POLL_CLASS["db"]->fetch_array($POLL_CLASS["db"]->query("SELECT SUM(votes) AS total FROM $POLLTBL[poll_data] WHERE (poll_id = '$poll_id')"));
  $POLL_CLASS["db"]->free_result($POLL_CLASS["db"]->result);
  list($wday,$mday,$month,$year,$hour,$minutes) = split("( )",date("w j n Y H i",$row['timestamp']+$time_offset));
  $newdate = "$weekday_poll[$wday], $mday ".$months_poll[$month-1]." $year $hour:$minutes";
  $smarty->assign("newdate",  $newdate); 

  $hours = (int) ((time()-$row['timestamp']+$time_offset)/3600);
  $days = (int) ($hours/24);
  $remain = $hours%24;
  $question = $row['question'];
  $poll_sum_total = $poll_sum["total"];
  $smarty->assign("days"         ,  $days);
  $smarty->assign("remain"       ,  $remain);
  $smarty->assign("poll_question",  $question); 
  $smarty->assign("poll_sum_total", $poll_sum_total);

  
  $result = $POLL_CLASS["db"]->query("select * from $POLLTBL[poll_data] where (poll_id = '$poll_id') order by option_id asc");
  $all_votes = array();
  while ($one_vote = $POLL_CLASS["db"]->fetch_array($result)) 
        {
          $one_vote["percent"] = ($poll_sum['total'] == 0) ? "0%" : sprintf("%.2f",($one_vote['votes']*100/$poll_sum['total']))."%";
          $one_vote["perday"]  = ($days>0) ? sprintf("%.1f",($one_vote["votes"]/$days)) : $one_vote["votes"];
          $one_vote["logging"] = 0; 
          if ($logging == 1) 
             {
              $log_result = $POLL_CLASS["db"]->query("select a.*, b.login as uname from $POLLTBL[poll_log] a left join  ".USERS_TABLE." b on b.id=a.user_id where (poll_id = '$poll_id' and option_id = '$one_vote[option_id]')");
              $row = $POLL_CLASS["db"]->num_rows($log_result);
              if ($row != 0) 
                 {
                  $one_vote["logging"]=1; 
                  $i=0;
                  while ($log_data = $POLL_CLASS["db"]->fetch_array($log_result)) 
                        {
                         $one_vote["log_data"][$i]=$log_data; 
                         $one_vote["log_data"][$i]["log_date"] = date("j-M-Y H:i",$log_data['timestamp']+$time_offset);
                         $i++;
                        }
                }
             }
          $all_votes[]=$one_vote;   
        }
  $smarty->assign("all_votes",  $all_votes); 
  $smarty->assign("lang_poll",  $lang_poll); 
  $smarty->assign("pollvars",   $pollvars);
  $smarty->assign("poll_id",    $poll_id);  
}
?>