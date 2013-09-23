<?php
/**
 * ----------------------------------------------
 * this code is optional
 * Important! You have to include it 
 * before your html code
 * ----------------------------------------------
 */

$cookie_expire = 96; // hours

$action = (isset($_GET['action'])) ? $_GET['action'] : '';
$action = (isset($_POST['action'])) ? $_POST['action'] : $action;
$poll_ident = (isset($_GET['poll_ident'])) ? $_GET['poll_ident'] : '';
$poll_ident = (isset($_POST['poll_ident'])) ? $_POST['poll_ident'] : $poll_ident;

if ($action=="vote" && (isset($_POST['option_id']) || isset($_GET['option_id']))) {
    $cookie_name = "AdvancedPoll".$poll_ident;
    if (!isset($_COOKIE[$cookie_name])) {
        $endtime = time()+3600*$cookie_expire;
        setcookie($cookie_name, "1", $endtime);
    }
}

?>