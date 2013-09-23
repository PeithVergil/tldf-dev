<?php
//==============================================================================
// Открыть домашнюю страницу пользователя
function OpenPageHome($user_name, $password)
{
  print(" <BODY onLoad='Redirect()'>                                                 \n");
  print(" <form name='RForm' method='post' action='../index.php'>                    \n");
  print("   <input name='login_lg' type='hidden'>                                    \n");
  print("   <input name='pass_lg'  type='hidden'>                                    \n");
  print(" </form>                                                                    \n");
  print(" </BODY>                                                                    \n");
  print("                                                                            \n");
  print(" <SCRIPT language='JavaScript'>                                             \n");
  print(" <!--                                                                       \n");
  print(" var v_login_lg = '';                                                       \n");
  print(" var v_pass_lg  = '';                                                       \n");
  print("                                                                            \n");
  print(" function Redirect()                                                        \n");
  print(" {                                                                          \n");
  print("   v_login_lg = '$user_name';                                               \n");
  print("   v_pass_lg  = '$password';                                                \n");
  print("   RForm.login_lg.value  = v_login_lg;                                      \n");
  print("   RForm.pass_lg.value   = v_pass_lg;                                       \n");
  print("   RForm.submit();                                                          \n");
  print(" }                                                                          \n");
  print(" function GetURLParam(param_name)                                           \n");
  print(" {                                                                          \n");
  print("   url = ''+document.location;                                              \n");
  print("   param_b = url.indexOf(param_name);                                       \n");
  print("   if (param_b==-1) return '';                                              \n");
  print("   param_b = param_b + param_name.length+1;                                 \n");
  print("   param_s = url.substring(param_b);                                        \n");
  print("   param_e = param_s.indexOf('%26');                                        \n");
  print("   if (param_e!=-1)                                                         \n");
  print("      {                                                                     \n");
  print("          param_s = param_s.substr(0,param_e);                              \n");
  print("    }                                                                       \n");
  print("    else                                                                    \n");
  print("      {                                                                     \n");
  print("        param_e = param_s.indexOf('&');                                     \n");
  print("        if (param_e!=-1) param_s = param_s.substr(0,param_e);               \n");
  print("    }                                                                       \n");
  print("   return param_s;                                                          \n");
  print(" }                                                                          \n");
  print(" //-->                                                                      \n");
  print(" </SCRIPT>                                                                  \n");
  print("                                                                            \n");
};
//==============================================================================

?>