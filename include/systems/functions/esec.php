<?php

/**
* Esec payment system functions
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
			'return_url'    => $config["server"].$config["site_root"]."/include/payment_request.php?sel=esec&amount=<<payment_amount>>&payment_reference=<<payment_reference>>"
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
		'currency'	=> "USD",
		'date'		=> date("Y-m-d H:i:s"),
		'status'	=> ($PaySystem->checkPayment())?1:0,
		'id_req'	=> $__REQUEST[$PaySystem->_arrayMap['order_id']],
		'quantity'	=> 1
	);
	return $pay_data;
}

//get paysystem settings. use in admin/admin_payment.php
function getBillingData($billing_system) {
	global $dbconn, $smarty, $lang;
		$rs = $dbconn->Execute("Select p.used, bs.seller_id from ".BILLING_PAYSYSTEMS_TABLE." p, ".BILLING_SYS_.$billing_system." bs where p.template_name='".$billing_system."'");
		$data["use"] = $rs->fields[0];
		$data["value"] = $rs->fields[1];
		$smarty->assign("header", $lang["pays"]);
		$smarty->assign("data", $data);
		$data["table_options"] = $smarty->fetch(SYSTEMS_DIR."templates/".$billing_system.".tpl");
		return $data;
}

//set(change) paysystem settings. use in admin/admin_payment.php
function setBillingData($billing_system, $__POST) {
	global $dbconn, $lang;
		$value = strval($__POST["value"]);
		$use = isset($__POST["use"]) ? intval($__POST["use"]) : 0;
		$err = 0;
		if($use !=0 && !$value){
			$err = $lang["err"]["invalid_fields"];
			$err .= "<br>".$lang["pays"]["esec_seller_id"];
		} else {
			$strSQL = "Update ".BILLING_PAYSYSTEMS_TABLE." set used='".$use."' where template_name='".$billing_system."'";
			$dbconn->Execute($strSQL);
			$strSQL = "Update ".BILLING_SYS_.$billing_system." set seller_id='".$value."'";
			$dbconn->Execute($strSQL);
		}
		return $err;
}

?>