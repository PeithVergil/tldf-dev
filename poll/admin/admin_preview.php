<?php
/**
 * ----------------------------------------------
 * URL: http://www.pilotgroup.net
 * ----------------------------------------------
 */

function admin_preview($poll_tplset_name, $tpl_type)
{
  global $poll_module_path;
  global $POLL_CLASS, $POLLTBL, $auth, $pollvars, $lang_poll, $weekday_poll, $months_poll;
  $include_path = $poll_module_path."/admin/include";
  
  $POLL_CLASS["preview"] = new pollcomment();
  $POLL_CLASS["template"] = new poll_template();
  $POLL_CLASS["template"]->set_rootdir($poll_module_path."/admin/templates");

  $preview_poll_id = $POLL_CLASS["preview"]->get_latest_poll_id();
  $POLL_CLASS["preview"]->include_path = $include_path;
  $POLL_CLASS["preview"]->set_template_set("$poll_tplset_name");
  $POLL_CLASS["preview"]->form_forward = "#";
  
  switch ($tpl_type) 
  {
      case "result":
          $preview = $POLL_CLASS["preview"]->view_poll_result($preview_poll_id);
          break;
  
      case "comment":
          $preview = $POLL_CLASS["preview"]->poll_form($preview_poll_id);
          break;
  
      default:
          $preview = $POLL_CLASS["preview"]->display_poll($preview_poll_id);
  }
  
  $preview = str_replace("<form method=\"post\"", "<form method=\"post\" onsubmit=\"return false;\"",$preview);
  $preview = str_replace("javascript:void(","#",$preview);
  $POLL_CLASS["template"]->set_templatefiles(array("admin_preview" => "admin_preview.html"));
  $admin_preview = $POLL_CLASS["template"]->pre_parse("admin_preview");
  eval("echo \"$admin_preview\";");
}
?>