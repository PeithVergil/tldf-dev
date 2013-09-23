<?php

/**
* Barclays payment system functions
*
* @package DatingPro
* @subpackage Payment systems files
* 
* $adv_arr array for additional parameters
**/

//redirect on this place after user click button "Pay". use in payment.php
function MakePayment($billing_system, $amount, $currency, $id_trunzaction, $product_name, $adv_arr='') {
	global $dbconn, $config;
		$rs_sys = $dbconn->Execute("SELECT seller_id, secret_word FROM ".BILLING_SYS_.$billing_system);
		$payGear = new Payment_Engine(PAYMENT_ENGINE_SEND, false, false);
		$PaySystem = &$payGear->factory($billing_system);

		switch($currency){
			case "USD":     $curr = "840"; break;
			case "EUR":     $curr = "978"; break;
			case "GBP":     $curr = "826"; break;
			case "CAD":     $curr = "124"; break;
			case "JPY":     $curr = "392"; break;
			case "AUD":     $curr = "036"; break;
		}

		$server = "secure2.epdq.co.uk";
		$url = "/cgi-bin/CcxBarclaysEpdqEncTool.e";
		$params = "clientid=".$rs_sys->fields[0];
		$params .= "&password=".$rs_sys->fields[1];
		$params .= "&oid=".$id_trunzaction;
		$params .= "&chargetype=Auth";
		$params .= "&currencycode=".$curr;
		$params .= "&total=".$amount;
		$response = pullpage( $server,$url,$params );
		$response_lines = explode("\n",$response);
		$response_line_count = count($response_lines);
		for ($i=0; $i<$response_line_count; $i++){
		    if (preg_match('/epdqdata/',$response_lines[$i])){
	        	$strEPDQ = $response_lines[$i];
		    }
		}
		$tmp = explode("\"",$strEPDQ);
		$strEPDQ = $tmp[1];

		$pay_data = array(
			'epdqdata' 		=> $strEPDQ,
			'seller_id'     => $rs_sys->fields[0],
			'secret_word'   => $rs_sys->fields[1],
			'amount'        => $amount,
			'order_id'		=> $id_trunzaction,
			'return_url'    => $config["server"].$config["site_root"]."/include/payment_request.php?sel=barclays",
			'type'          => "Auth",
			'product_name'  => $product_name,
			'currency'		=> $curr
		);
		$PaySystem->setOptions($pay_data);
		$PaySystem->doPayment();
}

//get paysystem request values. use in include/payment_request.php
function RequestPayment($billing_system, $__REQUEST) {
	$payGear = new Payment_Engine(PAYMENT_ENGINE_RECEIVE, false, false);
	$PaySystem = &$payGear->factory($billing_system);
	$pay_data = array(
		'count'		=> $__REQUEST[$PaySystem->_arrayMap['amount']],
		'currency'	=> "",
		'date'		=> date("Y-m-d H:i:s", strtotime($__REQUEST[$PaySystem->_arrayMap['datetime']])),
		'status'	=> ($PaySystem->checkPayment())?1:0,
		'id_req'	=> $__REQUEST[$PaySystem->_arrayMap['order_id']],
		'quantity'	=> 1
	);
	return $pay_data;
}

//get paysystem settings. use in admin/admin_payment.php
function getBillingData($billing_system) {
	global $dbconn, $smarty, $lang;
		$rs = $dbconn->Execute("Select p.used, bs.seller_id, bs.secret_word from ".BILLING_PAYSYSTEMS_TABLE." p, ".BILLING_SYS_.$billing_system." bs where p.template_name='".$billing_system."'");
		$data["use"] = $rs->fields[0];
		$data["value"] = $rs->fields[1];
		$data["password"] = $rs->fields[2];
		$smarty->assign("header", $lang["pays"]);
		$smarty->assign("data", $data);
		$data["table_options"] = $smarty->fetch(SYSTEMS_DIR."templates/".$billing_system.".tpl");
		return $data;
}

//set(change) paysystem settings. use in admin/admin_payment.php
function setBillingData($billing_system, $__POST) {
	global $dbconn, $lang;
		$value = strval($__POST["value"]);
		$password = strval($__POST["password"]);
		$use = isset($__POST["use"]) ? intval($__POST["use"]) : 0;
		$err = 0;
		if( $use!=0 && (!$value || !$password) ){
			$err = $lang["err"]["invalid_fields"];
			if(!$value) $err .= "<br>".$lang["pays"]["barclays_seller_id"];
			if(!$password) $err .= "<br>".$lang["pays"]["barclays_password"];
		} else {
			$strSQL = "Update ".BILLING_PAYSYSTEMS_TABLE." set used='".$use."' where template_name='".$billing_system."'";
			$dbconn->Execute($strSQL);
			$strSQL = "Update ".BILLING_SYS_.$billing_system." set seller_id='".$value."', secret_word='".$password."'";
			$dbconn->Execute($strSQL);
		}
		return $err;
}

function pullpage( $host, $usepath, $postdata = "" ) {
	$fp = fsockopen( $host, 80, &$errno, &$errstr, 60 );
	if( !$fp ) {
		print "$errstr ($errno)<br>\n";
	} else {
		fputs( $fp, "POST $usepath HTTP/1.0\n");
		$strlength = strlen( $postdata );
		fputs( $fp, "Content-type: application/x-www-form-urlencoded\n" );
		fputs( $fp, "Content-length: ".$strlength."\n\n" );
		fputs( $fp, $postdata."\n\n" );
		$output = "";
		while( !feof( $fp ) ) {
			$output .= fgets( $fp, 1024);
		}
		#close the socket connection
		fclose( $fp);
	}
	return $output;
}

?>