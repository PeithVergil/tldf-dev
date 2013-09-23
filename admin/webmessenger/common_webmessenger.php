<?php

$path = dirname(__FILE__)."/../..";
include $path."/include/config.php";
include $path."/common.php";
include $path."/include/config_admin.php";
include $path."/include/functions_auth.php";
include $path."/include/functions_admin.php";

/// new config descriptions
$config["admin_webmessenger_theme_path"] = $config["admin_theme_path"]."/webmessenger";
$smarty->assign("admin_webmessenger_gentemplates", "file:".$config["site_path"].$config["admin_webmessenger_theme_path"]);

$auth = auth_user();
login_check($auth);

$file_name = substr(dirname($_SERVER["PHP_SELF"]), strlen($config["site_root"]));
$file_name = str_replace("\\", "/", $file_name);
if(substr($file_name, 0, 1) != "/") $file_name = "/".$file_name;

$mode = IsFileAllowed($auth[0], $file_name, "messenger");

?>