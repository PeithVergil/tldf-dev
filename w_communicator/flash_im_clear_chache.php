<?php
  // Закрываем все текущие соединения чата
  $_GET["part"]="message";
  $_GET["act"]="send";
  $_GET["mess_count"]=1;
  $_GET["mess_type0"]=4;
  $_REQUEST["mess_text0"]="First restart chanel2.";
  $_GET["auth_session"]=1;
  ob_start();
  include "communicator.php"; 
  ob_end_clean(); 
?>
