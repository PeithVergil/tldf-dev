<?php
/**
 * ----------------------------------------------
 * URL: http://www.pilotgroup.net
 * ----------------------------------------------
 */
function strip_bad_chars($strg) 
{
    $bad_chars = array("/","\\","\"","'","?","#","+","~","<",">","*","|",":");
    for($i=0; $i<sizeof($bad_chars); $i++) {
        $strg = str_replace($bad_chars[$i],"",$strg);
    }
    return $strg;
}

function new_tplset($new_tplsetname) 
{
    global $POLL_CLASS, $POLLTBL;
    $now = date("Y-m-d H:i:s",time());
    $tpl_array = array("display_head","display_loop","display_foot","result_head","result_loop","result_foot","comment");
    $POLL_CLASS["db"]->query("INSERT INTO $POLLTBL[poll_tplset] (tplset_name,created) VALUES ('$new_tplsetname','$now')");
    $POLL_CLASS["db"]->fetch_array($POLL_CLASS["db"]->query("select max(tplset_id) as tplset_id from $POLLTBL[poll_tplset]"));
    $new_tpl_id = $POLL_CLASS["db"]->record["tplset_id"];
    for ($i=0; $i<sizeof($tpl_array); $i++) 
        {
          $POLL_CLASS["db"]->query("INSERT INTO $POLLTBL[poll_tpl] (tplset_id,title,template) VALUES ('$new_tpl_id','$tpl_array[$i]','')");
        }
    return $new_tpl_id;   
}

function admin_tpl_new()
{
  global $smarty;
  $smarty->assign("poll_admin_panel", "template_new");
  return 0;
}

function admin_tpl_create()
{
  if ((isset($_REQUEST["new_tplsetname"]))&&($_REQUEST["new_tplsetname"]!=null)) $new_tplsetname = $_REQUEST["new_tplsetname"];
                                                                           else  $new_tplsetname = ""; 
  $new_tplsetname = trim(strip_bad_chars($new_tplsetname));

  if (empty($new_tplsetname))
     {
        return 1; // Выводим первый попавшийся элемент
     }
     
  $poll_tplset = new_tplset($new_tplsetname);
  return $poll_tplset;
};
?>