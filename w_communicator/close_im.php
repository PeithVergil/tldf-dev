<?php
  $_GET["part"]="message";
  $_GET["act"]="send";
  $_GET["mess_count"]=1;
  $_GET["mess_type0"]=6;
  $_REQUEST["mess_text0"]="Close im session.";
  $_GET["auth_session"]=1;
  ob_start();
  include "communicator.php"; 
  ob_end_clean(); 
?>

<script type="text/javascript">
function close_browser_window()
{
  //alert("close");
  logouted=1;
  if ((window.ActiveXObject)||(window.opera))
     {
       window.opener = "_";
     }
     else
     {
       window.open('','_parent','');
     };
  window.close();

};
//close_browser_window();
</script>
