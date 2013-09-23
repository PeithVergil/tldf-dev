<?php
function toLog($log_str){
	$filename = 'test.log';
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