<?php

require_once('init.php');

$GLOBALS['fc_config']['cms']->LoginCheck(__FILE__);
AdminMainMenu($lang["chats"]);

unset( $_SESSION['userid'] );

ChatServer::loadCMSclass();
$cms = $GLOBALS['fc_config']['cms'];
$cmsclass = strtolower(get_class($cms));
$manageUsers = ($cmsclass == 'defaultcms') || ($cmsclass == 'statelesscms'  && (! isset($cms->constArr)));

ChatServer::logout();

//Assign Smarty variables and load the admin template
$smarty->assign('manageUsers',$manageUsers);
$smarty->assign('installed',isInstalled());
$smarty->display('logout.tpl');

?>