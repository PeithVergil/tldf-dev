<?php

/**
* Shoutbox initial file
*
*
* @package DatingPro
* @subpackage Shoutbox module
**/

include_once "../adodb/tohtml.inc.php";
include_once "../adodb/adodb.inc.php";

function PN_DBMsgError($db='',$prg='',$line=0,$message='Error accesing to the database') {
    $lcmessage = $message . "<br>" .
                "Program: " . $prg . " - " . "Line N.: " . $line . "<br>" .
                "Database: " . $db->database . "<br> ";

    if ($db->ErrorNo()<>0) {
        $lcmessage .= "Error (" . $db->ErrorNo() . ") : " . $db->ErrorMsg() . "<br>";
    }
    die($lcmessage);
}

global $dbconn, $ADODB_FETCH_MODE;

$dbconn = ADONewConnection($config['dbtype']);

if ($config['dbtype'] == "ado_mssql") {
    if ($config['useoledb'] == 1) {
        $connectString = "SERVER=".$config['dbhost'].";DATABASE=".$config['dbname'].";";
        $dbh = $dbconn->Connect($connectString, $config["dbuname"], $config["dbpass"], "SQLOLEDB");
    } else {
        $connectString="PROVIDER=MSDASQL;DRIVER={SQL Server};"."SERVER=".$config['dbhost'].";DATABASE=".$config['dbname'].";id_user=".$config['dbuname'].";PWD=".$config['dbpass'].";";
        $dbh = $dbconn->Connect($connectString, "", "", "");
    }
} else {
    $connectString = $config['dbtype'].":".$config['dbuname'].":".$config['dbpass']."@".$config['dbhost']."/".$config['dbname'];
    $dbh = $dbconn->Connect($config['dbhost'],($config['dbuname']),($config['dbpass']),$config['dbname']);
}
$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
 // if we get an error, log it and die
if ($dbh === false) {
    error_log ("connect string: $connectString");
    error_log ("error: " . $dbconn->ErrorMsg());
// show error and die
    PN_DBMsgError($dbconn, __FILE__ , __LINE__, "Error connecting to db".$config['dbname']);

}
//utf-8
$dbconn->Execute("SET NAMES 'utf8' COLLATE 'utf8_general_ci'");

?>