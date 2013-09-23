<?php

include "../config.php";
include_once "../../common.php";
include "../class.voip.php";

$VoIp = new DatingVoIp($dbconn, $config, -1);

$call_info = $VoIp->GetCallInfoByCallId($_REQUEST["CallId"]);
if (!$call_info){
	$_REQUEST["error_msg"] = $VoIp->dvErrorMsg();
	toLog($_REQUEST);
	exit;
}
$VoIp->id_user = $call_info["id_user"];


// inserts stat
echo "<?xml version='1.0' encoding='utf-8'?>";
$res = $VoIp->InsertStatistic($_REQUEST["CallId"],$_REQUEST["Status"],$_REQUEST["CallDuration"],$_REQUEST["CallCost"],$_REQUEST["CurrencyID"]);
if (!$res){
	$_REQUEST["error_msg"] = $VoIp->dvErrorMsg();
	toLog($_REQUEST);
	echo "<status>0</status>";
	exit;
}else 
	echo "<status>1</status>";
	
//get percent money from user
$VoIp->SubtrakFromUserAccount((floatval($_REQUEST["CallCost"])/$VoIp->currency_rate)*$VoIp->admin_percent/100,'voip_percent_for_admin');


function toLog($log_str){
	$filename = 'call_error.log';
	if (is_array($log_str)){
		$str = "date ".date('r')."\n";
		foreach($log_str as $key=>$value){
			$str.= $key."=>".$value."\n";	
		}
		$str.="\n\n";
	}else{
		$str = "date ".date('r')."\n";
		$str.= $log_str;
		$str.="\n\n";
	}
	
	if (is_writable($filename)) {
	    if (!$handle = fopen($filename, 'a')) {
	         //echo "�� ���� ������� ���� ($filename)";
	         //exit;
	    }
	
	    if (fwrite($handle, $str) === FALSE) {
	        //echo "�� ���� ���������� ������ � ���� ($filename)";
	        //exit;
	    }
	    
	    //echo "���! �������� ($somecontent) � ���� ($filename)";
	    
	    fclose($handle);
	
	} else {
	   	//echo "���� $filename ���������� ��� ������";
	}
}
?>