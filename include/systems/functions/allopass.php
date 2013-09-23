<?php

/**
* allopass payment system functions
*
* @package DatingPro
* @subpackage Payment systems files
**/

//redirect on this place after user click button "Pay". use in payment.php
function ShowCodePage($id_trunzaction, $id_period) {
	global $smarty, $dbconn, $config;

	$form["DATAS"]["mobile"] = $id_trunzaction.'/mobile';
	$form["DATAS"]["credit"] = $id_trunzaction.'/credit';

	$strSQL = " SELECT mobile_doc_id, credit_doc_id FROM ".BILLING_PERIODS_ALLOPASS_TABLE." WHERE id_group_period='".intval($_REQUEST["period_id"])."' ";
	$rs = $dbconn->Execute($strSQL);
	$row = $rs->GetRowAssoc(false);
	list($SITE_ID,$DOC_ID,$AUTH) = explode('/', $row["mobile_doc_id"]);
	$form["DOC_ID"]["mobile"] = $DOC_ID;
	list($SITE_ID,$DOC_ID,$AUTH) = explode('/', $row["credit_doc_id"]);
	$form["DOC_ID"]["credit"] = $DOC_ID;

	$rs_sys = $dbconn->Execute("SELECT seller_id FROM ".BILLING_SYS_."allopass");
	$row = $rs_sys->GetRowAssoc(false);
	$form["SITE_ID"] = $row["seller_id"];

	$form["flags"] = array('fr','be','ch','lu','de','uk','ca','au','nl','es','at','it','ie','hk','nz','se','no','pl','us');
	$smarty->assign("form", $form);

	$smarty->display(TrimSlash($config["index_theme_path"])."/payment_page_3.tpl");
	exit;
}

//get paysystem request values. use in include/payment_request.php
function RequestPayment($billing_system, $__REQUEST) {
	global $dbconn, $config;

	$DATA = explode('/', $__REQUEST["DATAS"]);
	$strSQL = "SELECT id, amount, id_product from ".BILLING_REQUESTS_TABLE." where id='".intval($DATA[0])."'";
	$rs = $dbconn->Execute($strSQL);
	$row = $rs->GetRowAssoc(false);
	$data["id_req"] = $row["id"];
	$data["amount"] = $row['amount'];

	$strSQL = "SELECT ".$DATA[1]."_doc_id as allopass_doc_id FROM ".BILLING_PERIODS_ALLOPASS_TABLE." WHERE id_group_period='".intval($row["id_product"])."'";
	$rs = $dbconn->Execute($strSQL);
	$row = $rs->GetRowAssoc(false);
	$DOC_ID = $row["allopass_doc_id"];

	$RECALL = $__REQUEST["RECALL"];
	if (trim($RECALL)=="") exit;
	// $RECALL contains the access code
	$RECALL = urlencode($RECALL);
	// $AUTH must contain document's ID
	$AUTH = urlencode($DOC_ID);
	// sending of the request to the Allopass server
	// in the $r[0] variable there will be the server's reply
	// in the $r[1] variable there will be "ABOCB"
	$r=@file("http://www.allopass.com/check/vf.php4?CODE=$RECALL&AUTH=$AUTH");
	// the server reply is tested
	$data["status"] = 1;
	if (ereg("ERR",$r[0]) || ereg("NOK",$r[0])) {
		// The server has replied ERR or NOK: access is therefore denied
		$data["status"] = 0;
	}

	$pay_data = array(
		'count'		=> $data["amount"],
		'currency'	=> "USD",
		'date'		=> date("Y-m-d H:i:s"),
		'status'	=> $data["status"],
		'id_req'	=> $data["id_req"],
		'quantity'	=> 1
	);
	return $pay_data;
}

//get paysystem settings. use in admin/admin_pays.php
function getBillingData($billing_system) {
	global $dbconn, $smarty, $lang;
		$rs = $dbconn->Execute("Select p.used, bs.seller_id from ".BILLING_PAYSYSTEMS_TABLE." p, ".BILLING_SYS_.$billing_system." bs where p.template_name='".$billing_system."'");
		$row = $rs->GetRowAssoc(false);
		$data["use"] = $row["used"];
		$data["seller_id"] = $row["seller_id"];

		$smarty->assign("header", $lang["pays"]);
		$smarty->assign("data", $data);

		$strSQL = " SELECT DISTINCT gpt.id, gpt.period, gpt.amount, gpt.cost, bpat.id as allopass_group_id, bpat.mobile_doc_id, bpat.credit_doc_id, gt.name
					FROM ".GROUP_PERIOD_TABLE." gpt
					LEFT JOIN ".BILLING_PERIODS_ALLOPASS_TABLE." bpat ON bpat.id_group_period=gpt.id
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
				$group_periods[$i]["allopass_doc_id_mobile"] = $row["mobile_doc_id"];
				$group_periods[$i]["allopass_doc_id_credit"] = $row["credit_doc_id"];
				$group_periods[$i]["allopass_group_id"] = $row["allopass_group_id"];
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
	if (isset($__POST["groups_periods"]) && $__POST["groups_periods"] == "1"){
		$allopass_group_doc_id_mobile = $__POST["allopass_group_doc_id_mobile"];
		$allopass_group_doc_id_credit = $__POST["allopass_group_doc_id_credit"];
		$groups_periods_id = $__POST["groups_periods_id"];
		$allopass_group_id = $__POST["allopass_group_id"];
		foreach ($groups_periods_id as $key =>$group_period_id){
			$strSQL = " UPDATE ".BILLING_PERIODS_ALLOPASS_TABLE."
				SET mobile_doc_id='".stripslashes(trim($allopass_group_doc_id_mobile[$key]))."', credit_doc_id='".stripslashes(trim($allopass_group_doc_id_credit[$key]))."'
				WHERE id='".stripslashes(trim($allopass_group_id[$key]))."' AND id_group_period=".stripslashes(trim($group_period_id))." ";
			$dbconn->Execute($strSQL);
		}
		return;
	} else {
		$seller_id = strval($__POST["seller_id"]);
		$use = isset($__POST["use"]) ? intval($__POST["use"]) : 0;

		$err = 0;
		if(!$seller_id && $use != 0 ){
			$err = $lang["err"]["invalid_fields"];
			$err .= "<br>".$lang["pays"]["allopass_seller_id"];
		} else {
			$strSQL = "Update ".BILLING_PAYSYSTEMS_TABLE." set used='".$use."' where template_name='".$billing_system."'";
			$dbconn->Execute($strSQL);
			$strSQL = "Update ".BILLING_SYS_.$billing_system." set seller_id='".$seller_id."'";
			$dbconn->Execute($strSQL);
		}
		return $err;
	}
}

?>