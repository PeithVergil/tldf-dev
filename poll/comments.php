<?php

include "../include/config.php";
include "../include/config_index.php";
include "../common.php";
include "../include/functions_auth.php";
include "../include/functions_index.php";


global $_SERVER, $_GET, $_POST;
$include_path = dirname(__FILE__);
if ($include_path == "/") 
{
    $include_path = ".";
}

$id = (isset($_POST['id'])) ? intval($_POST['id']) : "";
if ($id == "") 
{
  if (isset($_GET['id'])) 
  {
    $id = intval($_GET['id']);
  }
}

$template_set = (isset($_POST['template_set'])) ? trim($_POST['template_set']) : "";
if ($template_set == "") 
{
  if (isset($_GET['template_set'])) 
  {
    $template_set = trim($_GET['template_set']);
  }
}

$action = (isset($_POST['action'])) ? trim($_POST['action']) : "";
if ($action == "") 
{
  if (isset($_GET['action'])) 
  {
    $action = trim($_GET['action']);
  }
}

require $include_path."/include/config.inc.php";
require $include_path."/include/$POLLDB[class]";
require $include_path."/include/class_poll.php";
require $include_path."/include/class_pollcomment.php";
global $POLL_CLASS;
$POLL_CLASS["db"] = new polldb_sql;
$POLL_CLASS["db"]->connect();

$my_comment = new pollcomment();

if (!empty($template_set)) 
{
    $my_comment->set_template_set("$template_set");
}
if (empty($id)) 
{
    echo $my_comment->print_message("Poll ID <b>".$id."</b> does not exist or is disabled!");
} elseif ($my_comment->is_comment_allowed($id)) {
    if ($action == "add") {
        $poll_input = array("message");
        for($i=0;$i<sizeof($poll_input);$i++) 
        {
            if (isset($_POST[$poll_input[$i]])) 
            {     
                $_POST[$poll_input[$i]] = trim($_POST[$poll_input[$i]]);    
            } else 
            {
                $_POST[$poll_input[$i]] = '';
            }
        }
        $user = auth_index_user();  
        $user_id  = $user[ AUTH_ID_USER ];

        $_POST['user_id'] = $user_id;
        
        if (empty($_POST['message'])) 
        {
            echo $my_comment->print_message("You forgot to fill in the message field!<br><a href=\"javascript:history.back()\">Go back</a>");
        }
        else 
        {
            $my_comment->add_comment($id);
            echo $my_comment->print_message("Your message has been sent!",1);
        }
    } else {
        echo $my_comment->poll_form($id);
    }
}

?>