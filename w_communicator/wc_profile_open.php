<?php

//==============================================================================
// Открыть страницу с нужным профайлом
function OpenProfile($user_name, $password, $already_logined, $user_id)
{
  global $config;
  if ($already_logined==0) SiteLogin($user_name, $password);
  $url = "../viewprofile.php?id=$user_id&search_type=p";
  print("<script>location.href='".$url."'</script>");
};
//==============================================================================

?>
