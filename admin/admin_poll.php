<?php
/**
*
*   @author Katya Kashkova<katya@pilotgroup.net>, Pilot Group <http://www.pilotgroup.net/>
*   @date   08/07/2004
*
**/
include "../include/config.php";
include_once "../common.php";
include "../include/config_admin.php";
include "../include/functions_auth.php";
include "../include/functions_admin.php";

$auth = auth_user();
login_check($auth);
$mode = IsFileAllowed($auth[0], GetRightModulePath(__FILE__), "poll");

if ($mode == 1) {
	// FULL
	ShowPollAdminPanels();
} elseif($mode == 2) {
	// DEMO
	ShowPollAdminPanels();
}

////////////// list function /////////////////////////////////////////////////////////////  
function ShowPollAdminPanels($err="")
{
    global $smarty, $dbconn, $config, $config_admin, $page, $lang;
    
    $file_name = (isset($_SERVER["PHP_SELF"]))?AfterLastSlash($_SERVER["PHP_SELF"]):"admin_poll.php"; 

    AdminMainMenu($lang["poll"]);

    global $poll_module_path;
    $poll_module_path = $config["site_path"]."/poll";
    require $poll_module_path."/admin/common.inc.php";
    require $poll_module_path."/admin/admin.php";
    require $poll_module_path."/admin/admin_edit.php";
    require $poll_module_path."/admin/admin_stats.php";   
    require $poll_module_path."/admin/admin_comment.php";
    require $poll_module_path."/include/class_input2db.php";
    require $poll_module_path."/admin/admin_settings.php";
    require $poll_module_path."/admin/admin_templates.php";
    require $poll_module_path."/include/class_poll.php";
    require $poll_module_path."/include/class_pollcomment.php";   
    require $poll_module_path."/admin/admin_preview.php";
    require $poll_module_path."/admin/admin_tpl_new.php";

    
    no_cache_header();
    
    $smarty->assign("poll_admin_menu", "poll menu");
    
    if (isset($_REQUEST["sel"])) $sel=$_REQUEST["sel"];
                          else   $sel='';
    //$sel = "";
    //$_REQUEST["action"]="new";
    //$_REQUEST["poll_id"]=4;
    //$_REQUEST["action"]="extend";                         
    switch ($sel) 
           {
                case "templates":
                    admin_poll_templates();
                    break;
                case "settings":
                    admin_poll_settings();
                    break;
                case "edit":
                    admin_poll_edit();
                    break; 
                case "stats":
                    admin_poll_stats();
                    break;
                case "comment":
                    admin_poll_comment();
                    break;
                default:
                    admin_poll_index();
                    break;
           }
    
    /// form 
    if(!$err){
      $name = "";
    }
    $form["err"] = $err;
    
    $smarty->assign("form", $form);
    $smarty->display(TrimSlash($config["admin_theme_path"])."/admin_poll_table.tpl");
    exit;
}

?> 