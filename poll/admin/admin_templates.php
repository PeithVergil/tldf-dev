<?php
/**
 * ----------------------------------------------
 * URL: http://www.pilotgroup.net
 * ----------------------------------------------
 */
 
function get_tplset($poll_tplset) 
{
    global $POLL_CLASS, $lang_poll, $POLLTBL;
    $POLL_CLASS["db"]->query("SELECT * FROM $POLLTBL[poll_tplset] ORDER BY tplset_id asc");
    $select_field ="";
    while ($row = $POLL_CLASS["db"]->fetch_array($POLL_CLASS["db"]->result)) 
          {
            if ($poll_tplset==$row["tplset_id"]) $select_field .= "<option value=\"$row[tplset_id]\" selected>$row[tplset_name]</option>\n";
                                           else $select_field .= "<option value=\"$row[tplset_id]\">$row[tplset_name]</option>\n";
          }
    return $select_field;
}

function is_valid_tplset_id($poll_tplset='') 
{
    global $POLL_CLASS, $POLLTBL;    
    $POLL_CLASS["db"]->fetch_array($POLL_CLASS["db"]->query("SELECT * FROM $POLLTBL[poll_tplset] where (tplset_id = '$poll_tplset')"));
    if (!$POLL_CLASS["db"]->record) {
        $POLL_CLASS["db"]->fetch_array($POLL_CLASS["db"]->query("SELECT * FROM $POLLTBL[poll_tplset] ORDER BY tplset_id asc"));
        return (!$POLL_CLASS["db"]->record) ? false : $POLL_CLASS["db"]->record['tplset_id'];
    }
    return $POLL_CLASS["db"]->record['tplset_id'];
}

function update_tpl($poll_tplset, $tpl) 
{
    global $POLL_CLASS, $POLLTBL; 
    if (is_array($tpl)) 
    {
        reset ($tpl);
        while (list($name, $value) = each($tpl)) 
        {
            if (!get_magic_quotes_gpc()) $value = addslashes($value);
            $POLL_CLASS["db"]->query("UPDATE $POLLTBL[poll_tpl] set template='$value' where (tplset_id = '$poll_tplset' and tpl_id='$name')");
        }
    }
}

function get_tplset_name($poll_tplset) 
{
    global $POLL_CLASS, $POLLTBL;
    $POLL_CLASS["db"]->fetch_array($POLL_CLASS["db"]->query("SELECT tplset_name FROM $POLLTBL[poll_tplset] where (tplset_id = '$poll_tplset')"));
    return (!$POLL_CLASS["db"]->record) ? false : $POLL_CLASS["db"]->record['tplset_name'];
}
  

function admin_poll_templates()
{
  global $poll_module_path;
  global $config, $smarty;
  global $POLL_CLASS, $POLLTBL, $auth, $pollvars, $lang_poll, $weekday_poll, $months_poll;
  $smarty->assign("poll_admin_panel", "templates");
  
  if ((isset($_REQUEST["tpl_act"]))&&($_REQUEST["tpl_act"]!=null)) $tpl_act = $_REQUEST["tpl_act"];
                                                             else  $tpl_act = "";  
  if ((isset($_REQUEST["poll_tplset"]))&&($_REQUEST["poll_tplset"]!=null)) $poll_tplset = $_REQUEST["poll_tplset"];
                                                                     else  $poll_tplset = "";                                                             
  if ((isset($_REQUEST["tpl_type"]))&&($_REQUEST["tpl_type"]!=null)) $tpl_type = $_REQUEST["tpl_type"];
                                                               else  $tpl_type = "";                                                             
                                                                                                                                          
  if ($tpl_act=="create")
     {    
        $poll_tplset = admin_tpl_create();
     }
                                                               
  $poll_tplset = is_valid_tplset_id($poll_tplset);
  $poll_tplset_name = get_tplset_name ($poll_tplset);

     
  if ($tpl_act=="preview")
     {
        admin_preview($poll_tplset_name, $tpl_type);
        exit; // Не запускаем SMARTY - Просто выходим...
     }
  if ($tpl_act=="save")
     {    
        $tpl = array(); // Выбираем все что на форме
        $tpl_prefix = "tpl_";
        foreach ($_REQUEST as $key => $arg)
          {
             if (eregi($tpl_prefix.'[0-9]+', $key))
                {
                   $tpl_key = substr($key,strlen("$tpl_prefix"), strlen($key));
                   $tpl[$tpl_key] = $arg;
                }
                
          }
        update_tpl($poll_tplset, $tpl);
     }       
  if ($tpl_act=="delete")
     {     
        $POLL_CLASS["db"]->query("DELETE FROM $POLLTBL[poll_tpl] where (tplset_id = '$poll_tplset')");
        $POLL_CLASS["db"]->query("DELETE FROM $POLLTBL[poll_tplset] where (tplset_id = '$poll_tplset')");
        $poll_tplset = 1;
        $poll_tplset = is_valid_tplset_id($poll_tplset);
        $poll_tplset_name = get_tplset_name ($poll_tplset);
        $tpl_act="";
     }
  if (!$poll_tplset) $tpl_act = "new";     

  if ($tpl_act=="new")
     {    
        admin_tpl_new();
     }
     else
     {
        $POLL_CLASS["db"]->query("SELECT * FROM $POLLTBL[poll_tpl] WHERE tplset_id='$poll_tplset'");
        while ($tpl = $POLL_CLASS["db"]->fetch_array($POLL_CLASS["db"]->result)) 
           {
              $poll_tpl[$tpl['title']] = htmlspecialchars($tpl['template']);
              $poll_tpl_id[$tpl['title']] = $tpl['tpl_id'];
           }
     }
     
  
  $select_field = get_tplset($poll_tplset);
 
  $smarty->assign("tpl_type",     $tpl_type);
  $smarty->assign("poll_tplset",  $poll_tplset);
  $smarty->assign("select_field", $select_field);
  $smarty->assign("poll_tpl",     $poll_tpl);
  $smarty->assign("poll_tpl_id",  $poll_tpl_id); 
  $smarty->assign("lang_poll",    $lang_poll); 
  $smarty->assign("pollvars",     $pollvars); 
  $smarty->assign("smarty_script", 
'    <script language="Javascript">
     <!--
     function ChangeBar(sel) 
        {
          location.href="'.$pollvars["SELF"].'?sel=templates&poll_tplset="+sel;
        }    
     function del_entry(entry) 
        {
          if (window.confirm("'.$lang_poll["Confirm"].'")) 
             {
               window.location.href = "http://"+window.location.host+window.location.pathname+"?sel=templates&tpl_act=delete&poll_tplset='.$poll_tplset.'&no_cache="+Math.random();
             }
        }
     function openWindow(theURL,winName,winWidth,winHeight,features) 
        {
          var w = (screen.width - winWidth)/2;
          var h = (screen.height - winHeight)/2 - 20;
          features = features+",width="+winWidth+",height="+winHeight+",top="+h+",left="+w;
          window.open(theURL,winName,features);
        }
     function SubmitMyForm() 
        {
          document.forms[0].submit();
        }                        
     function ResetMyForm() 
        {
         document.forms[0].reset();
        }             
     // -->
     </script>');  
};
?>