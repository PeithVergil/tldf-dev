<?php

/**
* webmoney payment system functions
*
* @package DatingPro
* @subpackage Payment systems files
* $adv_arr array for additional parameters
**/

//redirect on this place after user click button "Pay". use in payment.php
function MakePayment($billing_system, $amount, $currency, $id_trunzaction, $product_name, $adv_arr='') {
	global $dbconn, $config;
		$rs_sys = $dbconn->Execute("SELECT seller_id FROM ".BILLING_SYS_.$billing_system);
		$payGear = new Payment_Engine(PAYMENT_ENGINE_SEND, false, false);
		$PaySystem = &$payGear->factory($billing_system);
		$pay_data = array(
			'seller_id'     => $rs_sys->fields[0],
			'amount'        => $amount,
			'order_id'		=> $id_trunzaction,
//			'test_mode'		=> 0,
			'description'		=> $product_name,
			'return_url'		=> $config["server"].$config["site_root"]."/include/payment_request.php?sel=webmoney",
			'success_url'		=> $config["server"].$config["site_root"]."/account.php",
			'fail_url'		=> $config["server"].$config["site_root"]."/account.php",
			'success_url_method'		=> "2",
			'fail_url_method'		=> "2",
		);
		$PaySystem->setOptions($pay_data);
		$PaySystem->doPayment();
}

//get paysystem request values. use in include/payment_request.php
function RequestPayment($billing_system, $__REQUEST) {
	global $dbconn, $config;
	$rs_sys = $dbconn->Execute("SELECT seller_id, secret_key FROM ".BILLING_SYS_.$billing_system);
	$merchant_data["id_seller"] = $rs_sys->fields[0];
	$merchant_data["secret_key"] = $rs_sys->fields[1];
	$payGear = new Payment_Engine(PAYMENT_ENGINE_RECEIVE, false, false);
	$PaySystem = &$payGear->factory($billing_system);
	if ($__REQUEST[$PaySystem->_arrayMap['ret_prerequest']]) {
		$pay_data = array(
			'count'		=> $__REQUEST[$PaySystem->_arrayMap['amount']],
			'id_req'	=> $__REQUEST[$PaySystem->_arrayMap['order_id']],
			'seller_id'	=> $__REQUEST[$PaySystem->_arrayMap['seller_id']]
		);
		CheckPreOrder($pay_data);
	}
	$pay_data = array(
		'count'		=> $__REQUEST[$PaySystem->_arrayMap['amount']],
		'currency'	=> "USD",
		'date'		=> date("Y-m-d H:i:s"),
		'status'	=> ($PaySystem->checkPayment($merchant_data))?1:0,
		'id_req'	=> $__REQUEST[$PaySystem->_arrayMap['order_id']],
		'quantity'	=> 1
	);
	return $pay_data;
}

function CheckPreOrder($data){
	global $dbconn;
	$strSQL = "SELECT amount, status, paysystem from ".BILLING_REQUESTS_TABLE." where id='".$data["id_req"]."'";
	$rs = $dbconn->Execute($strSQL);
	$row = $rs->GetRowAssoc(false);

	$base_data["amount"] = $row["amount"];
	$base_data["status"] = $row["status"];
	$base_data["paysystem"] = $row["paysystem"];

	$rs = $dbconn->Execute("Select seller_id from ".BILLING_SYS_."webmoney");
	$base_data["seller_id"] = $rs->fields[0];

	if($base_data["paysystem"] == "webmoney" && $base_data["seller_id"] == $data["seller_id"] && $base_data["status"] == "send" && $base_data["count_curr"] == $data["amount"]){
		echo "YES";
	}else{
		$strSQL = "update ".BILLING_REQUESTS_TABLE." set status='fail' where id='".$data["id_req"]."'";
		$rs = $dbconn->Execute($strSQL);
		echo "ERROR";
	}
	exit();
}

//get paysystem settings. use in admin/admin_payment.php
function getBillingData($billing_system) {
	global $dbconn, $smarty, $lang;
		$rs = $dbconn->Execute("Select p.used, bs.seller_id, bs.secret_key from ".BILLING_PAYSYSTEMS_TABLE." p, ".BILLING_SYS_.$billing_system." bs where p.template_name='".$billing_system."'");
		$data["use"] = $rs->fields[0];
		$data["value"] = $rs->fields[1];
		$data["secret_key"] = $rs->fields[2];
		$smarty->assign("header", $lang["pays"]);
		$smarty->assign("data", $data);
		$data["table_options"] = $smarty->fetch(SYSTEMS_DIR."templates/".$billing_system.".tpl");
		return $data;
}

//set(change) paysystem settings. use in admin/admin_payment.php
function setBillingData($billing_system, $__POST) {
	global $dbconn, $lang;
		$secret_key = strval($__POST["secret_key"]);
		$value = strval($__POST["value"]);
		$use = isset($__POST["use"]) ? intval($__POST["use"]) : 0;
		$err = 0;
		if($use!=0 && !$value){
			$err = $lang["err"]["invalid_fields"];
			$err .= "<br>".$lang["pays"]["webmoney_seller_id"];
		} else {
			$strSQL = "Update ".BILLING_PAYSYSTEMS_TABLE." set used='".$use."' where template_name='".$billing_system."'";
			$dbconn->Execute($strSQL);
			$strSQL = "Update ".BILLING_SYS_.$billing_system." set seller_id='".$value."', secret_key='".$secret_key."'";
			$dbconn->Execute($strSQL);
		}
		return $err;
}

?>