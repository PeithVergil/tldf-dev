<?php

//==============================================================================
// Открыть страницу с нужным письмом
function OpenMail($user_name, $password, $letter_id)
{
  global $config;
  SiteLogin($user_name, $password);
  $url = $config["server"].$config["site_root"]."/mailbox.php?sel=viewto&id=$letter_id";
  print("<script>location.href='".$url."'</script>");
};
//==============================================================================

?>