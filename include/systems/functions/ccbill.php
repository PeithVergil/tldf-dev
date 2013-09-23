<?php

/**
* ccbill payment system functions
*
* @package DatingPro
* @subpackage Payment systems files
* $adv_arr array for additional parameters
**/

//redirect on this place after user click button "Pay". use in payment.php
function MakePayment($billing_system, $amount, $currency, $id_trunzaction, $product_name, $adv_arr='') {
	global $dbconn, $config;

		$strSQL = " SELECT ccbill_sub_id FROM ".BILLING_PERIODS_CCBILL_TABLE." WHERE id_group_period='".intval($_REQUEST["period_id"])."' ";
		$rs = $dbconn->Execute($strSQL);
		$subscription_type_id = $rs->fields[0];

		$rs_sys = $dbconn->Execute("SELECT seller_id, seller_sub_id, form_name, lang as language FROM ".BILLING_SYS_.$billing_system);
		$row = $rs_sys->GetRowAssoc(false);
		$payGear = new Payment_Engine(PAYMENT_ENGINE_SEND, false, false);
		$PaySystem = &$payGear->factory($billing_system);
		$pay_data = array(
			'seller_id'     => $row["seller_id"],
			'seller_sub_id'	=> $row["seller_sub_id"],
			'form_name'		=> $row["form_name"],
			'language'		=> $row["language"],
			'allowed_types' => $subscription_type_id,
			'order_id' 		=> $id_trunzaction,
			'subscription_type_id' => $subscription_type_id
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

//get paysystem settings. use in admin/admin_pays.php
function getBillingData($billing_system) {
	global $dbconn, $smarty, $lang;
		$rs = $dbconn->Execute("Select p.used, bs.seller_id, bs.seller_sub_id, bs.form_name, bs.lang as language  from ".BILLING_PAYSYSTEMS_TABLE." p, ".BILLING_SYS_.$billing_system." bs where p.template_name='".$billing_system."'");
		$row = $rs->GetRowAssoc(false);
		$data["use"] = $row["used"];
		$data["seller_id"] = $row["seller_id"];
		$data["seller_sub_id"] = $row["seller_sub_id"];
		$data["form_name"] = $row["form_name"];
		$data["language"] = $row["language"];

		$smarty->assign("header", $lang["pays"]);
		$smarty->assign("data", $data);

		$strSQL = " SELECT DISTINCT gpt.id, gpt.period, gpt.amount, gpt.cost, bpct.id as ccbill_group_id, bpct.ccbill_sub_id, gt.name
					FROM ".GROUP_PERIOD_TABLE." gpt
					LEFT JOIN ".BILLING_PERIODS_CCBILL_TABLE." bpct ON bpct.id_group_period=gpt.id
					LEFT JOIN ".GROUPS_TABLE." gt ON gt.id=gpt.id_group
					WHERE gpt.status='1' GROUP BY gpt.id";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0]>0) {
			$i = 0;
			$currency = GetSiteSettings("site_unit_costunit");
			while(!$rs->EOF){
				$row = $rs->GetRowAssoc(false);
				$group_periods[$i]["id"] = $row["id"];
				$group_periods[$i]["period"] = $lang["pays"]["periods"]["".$row["period"].""];
				$group_periods[$i]["amount"] = $row["amount"];
				$group_periods[$i]["cost"] = $row["cost"];
				$group_periods[$i]["ccbill_sub_id"] = $row["ccbill_sub_id"];
				$group_periods[$i]["ccbill_group_id"] = $row["ccbill_group_id"];
				$group_periods[$i]["group_name"] = $row["name"];
				$group_periods[$i]["currency"] = $currency;
				$rs->MoveNext();
				$i++;
			}
			$smarty->assign("group_periods", $group_periods);
		}
		$data["table_options"] = $smarty->fetch(SYSTEMS_DIR."templates/".$billing_system.".tpl");
		return $data;
}

//set(change) paysystem settings. use in admin/admin_payment.php
function setBillingData($billing_system, $__POST) {
	global $dbconn, $lang;
	if (isset($__POST["groups_periods"]) && ($__POST["groups_periods"] == "1")){
		$ccbill_group_sub_id = $__POST["ccbill_group_sub_id"];
		$groups_periods_id = $__POST["groups_periods_id"];
		$ccbill_group_id = $__POST["ccbill_group_id"];
		foreach ($groups_periods_id as $key =>$group_period_id){
			$strSQL = " UPDATE ".BILLING_PERIODS_CCBILL_TABLE." SET ccbill_sub_id='".stripslashes(trim($ccbill_group_sub_id[$key]))."' WHERE id='".stripslashes(trim($ccbill_group_id[$key]))."' AND id_group_period=".stripslashes(trim($group_period_id))." ";
			$dbconn->Execute($strSQL);
		}
		return;
	} else {
		$seller_id = strval($__POST["seller_id"]);
		$use = isset($__POST["use"]) ? intval($__POST["use"]) : 0;
		$seller_sub_id = strval($__POST["seller_sub_id"]);
		$form_name = strval($__POST["form_name"]);
		$language = strval($__POST["language"]);

		$err = 0;
		if ($use!=0 && (!$seller_id || !$seller_sub_id || !$form_name)){
			$err = $lang["err"]["invalid_fields"];
			if(!$seller_id)
				$err .= "<br>".$lang["pays"]["ccbill_seller_id"];
			if(!$seller_sub_id)
				$err .= "<br>".$lang["pays"]["ccbill_seller_sub_id"];
			if(!$form_name)
				$err .= "<br>".$lang["pays"]["ccbill_form_name"];
		} else {
			$strSQL = "Update ".BILLING_PAYSYSTEMS_TABLE." set used='".$use."' where template_name='".$billing_system."'";
			$dbconn->Execute($strSQL);
			$strSQL = "Update ".BILLING_SYS_.$billing_system." set seller_id='".$seller_id."', seller_sub_id='".$seller_sub_id."', form_name='".$form_name."',
				lang='".$language."' ";
			$dbconn->Execute($strSQL);
		}
		return $err;
	}
}

?>