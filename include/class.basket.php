<?php

class Orders
{
	var $config;
	var $dbconn;
	var $catalog;
	var $smarty;
	var $user;
	var $BASKET_TABLE = GIFTSHOP_BASKET;
	var $ORDERS_TABLE = GIFTSHOP_ORDERS;
	var $ORDERS_ITEMS_TABLE = GIFTSHOP_ORDERS_ITEMS;
	var $ITEM_TABLE = GIFTSHOP_CATALOG_ITEM;
	var $BILLING_REQUESTS_TABLE = BILLING_REQUESTS_TABLE;
	var $USERS_TABLE = USERS_TABLE;

	function Orders($user, $config, $dbconn, $smarty, $catalog)
	{
		$this->user = $user;
		$this->config = $config;
		$this->dbconn = $dbconn;
		$this->catalog = $catalog;
		$this->smarty = $smarty;
	}

	function GetBasket()
	{
		$id_user = $this->user[ AUTH_ID_USER ];
		
		$rs = $this->dbconn->Execute('SELECT id, id_item, quantity FROM '.$this->BASKET_TABLE.' WHERE id_user=? ORDER BY id_item', array($id_user));
		$i = 0;
		$data = array();
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$data[$i]['id'] = (int) $row['id'];
			$data[$i]['id_item'] = (int) $row['id_item'];
			$data[$i]['quantity'] = (int) $row['quantity'];
			$data[$i]['shop'] = $this->catalog->items_item($row['id_item']);
			$data[$i]['sum'] = sprintf('%01.2f', $data[$i]['shop']['price'] * $data[$i]['quantity']);
			$rs->MoveNext();
			$i++;
		}
		return $data;
	}
	
	function GetBasketSum()
	{
		$id_user = $this->user[ AUTH_ID_USER ];
		
		$strSQL =
			'SELECT SUM(a.quantity * b.price) AS total_amount, SUM(a.quantity) AS total_quantity, COUNT(*) AS count
			   FROM '.$this->BASKET_TABLE.' a
			  INNER JOIN '.$this->ITEM_TABLE.' b ON a.id_item=b.id
			  WHERE a.id_user=?';
		$rs = $this->dbconn->Execute($strSQL, array($id_user));
		$row = $rs->GetRowAssoc(false);
		$basket_info['total_amount_format'] = sprintf('%01.2f', $row['total_amount']);
		$basket_info['total_quantity'] = (int) $row['total_quantity'];
		$basket_info['count'] = (int) $row['count'];
		return $basket_info;
	}
	
	function GetBasketCount()
	{
		$id_user = $this->user[ AUTH_ID_USER ];
		
		return (int) $this->dbconn->getOne('SELECT COUNT(*) FROM '.$this->BASKET_TABLE.' WHERE id_user=?', array($id_user));
	}
	
	function SaveBasket($basket_info)
	{
		$id_user = $this->user[ AUTH_ID_USER ];
		
		$this->ClearBasket(false);

		if (!empty($basket_info) && is_array($basket_info)) {
			foreach ($basket_info as $id_shop => $quantity) {
				if (intval($id_shop) && intval($quantity)) {
					$strSQL = 'INSERT INTO '.$this->BASKET_TABLE.' (id_user, id_item, quantity) VALUES (?, ?, ?)';
					$this->dbconn->Execute($strSQL, array($id_user, $id_shop, $quantity));
				}
			}
		}
		
		return;
	}
	
	function ClearBasket($clear_order=false)
	{
		$id_user = $this->user[ AUTH_ID_USER ];
		
		$this->dbconn->Execute('DELETE FROM '.$this->BASKET_TABLE.' WHERE id_user=?', array($id_user));
		
		// we keep $_SESSION['basket_user'] and $_SESSION['basket_comment']
		if ($clear_order) {
			unset($_SESSION['basket_order']);
		}
		
		return;
	}
	
	function AddToBasket($id_item)
	{
		if (intval($id_item)) {
			$check = $this->dbconn->getOne('SELECT id FROM '.$this->BASKET_TABLE.' WHERE id_item=? AND id_user=?', array($id_item, $this->user[0]));
			if (!empty($check)) {
				$strSQL = 'UPDATE '.$this->BASKET_TABLE.' SET quantity = quantity + 1 WHERE id_item=? AND id_user=?';
				$this->dbconn->Execute($strSQL, array($id_item, $this->user[0]));
			} else {
				$strSQL = 'INSERT INTO '.$this->BASKET_TABLE.' (id_user, id_item, quantity) VALUES (?, ?, "1")';
				$this->dbconn->Execute($strSQL, array($this->user[0], $id_item));
			}
		}
		return;
	}
	
	// ORDERS
	
	function GetOrdersSum()
	{
		$id_user = $this->user[ AUTH_ID_USER ];
		
		$order_info['orders'] = 0;
		$order_info['total_amount'] = 0;
		$order_info['total_quantity'] = 0;
		
		$rs = $this->dbconn->Execute('SELECT id FROM '.$this->ORDERS_TABLE.' WHERE id_user_from=?', array($id_user));
		
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$order_info['orders']++;
			$strSQL =
				'SELECT SUM(quantity * currency) AS total_amount, SUM(quantity) AS total_quantity
				   FROM '.$this->ORDERS_ITEMS_TABLE.'
				  WHERE id_order=?';
			$sub_rs = $this->dbconn->Execute($strSQL, array($row['id']));
			$order_info['total_amount'] += $sub_rs->fields[0];
			$order_info['total_quantity'] += (int) $sub_rs->fields[1];
			$rs->MoveNext();
		}
		$order_info['total_amount_format'] = sprintf('%01.2f', $order_info['total_amount']);
		return $order_info;
	}
	
	function GetOrders()
	{
		$id_user = $this->user[ AUTH_ID_USER ];
		
		$rs = $this->dbconn->Execute('SELECT id FROM '.$this->ORDERS_TABLE.' WHERE id_user_from = ? ORDER BY id', array($id_user));
		$i = 0;
		$order_data = array();
		
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			$order_data[$i]['order_info'] = $this->GetOrderInfo($row['id']);
			
			$strSQL =
				'SELECT SUM(quantity * currency) AS total_amount, SUM(quantity) AS total_quantity
				   FROM '.$this->ORDERS_ITEMS_TABLE.'
				  WHERE id_order = ?';
			$sub_rs = $this->dbconn->Execute($strSQL, array($row['id']));
			$sub_row = $sub_rs->GetRowAssoc(false);
			$order_data[$i]['total_amount'] = $sub_row['total_amount'];
			$order_data[$i]['total_amount_format'] = number_format($sub_row['total_amount'], 2);
			$order_data[$i]['total_quantity'] = $sub_row['total_quantity'];
			$rs->MoveNext();
			$i++;
		}

		return $order_data;
	}

	function GetOrder($order_id)
	{
		$order = $this->GetOrderInfo($order_id);
		$strSQL =
			'SELECT SUM(quantity * currency) AS total_amount, SUM(quantity) AS total_quantity
			   FROM '.$this->ORDERS_ITEMS_TABLE.'
			  WHERE id_order=?';
		$rs = $this->dbconn->Execute($strSQL, array($order_id));
		$row = $rs->GetRowAssoc(false);
		$order['total_amount'] = $row['total_amount'];
		$order['total_amount_format'] = sprintf('%01.2f', $row['total_amount']);
		$order['total_quantity'] = $row['total_quantity'];
		return $order;
		// summ -> total_amount
		// coll -> total_quantity
		// all_sum -> total_amount_format
		// order_info -> items
	}
	
	function GetOrderInfo($order_id)
	{
		$rs = $this->dbconn->Execute(
			'SELECT o.*, u.login AS login_to, u.fname AS fname_to
			   FROM '.$this->ORDERS_TABLE.' o
		 INNER JOIN '.$this->USERS_TABLE.' u ON o.id_user_to = u.id
			  WHERE o.id = ?',
			  array($order_id));
		$order = $rs->GetRowAssoc(false);
		
		$order['offline_payment_pending'] = $this->OfflinePaymentPending($order_id);
		
		$rs = $this->dbconn->Execute('SELECT id_item, currency, quantity FROM '.$this->ORDERS_ITEMS_TABLE.' WHERE id_order = ?', array($order_id));
		$i = 0;
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$order['items'][$i] = $row;
			$order['items'][$i]['shop'] = $this->catalog->items_item($row['id_item']);
			$order['items'][$i]['currency'] = number_format($row['currency'], 2);
			$order['items'][$i]['sum'] = number_format($row['currency'] * $row['quantity'], 2);
			$rs->MoveNext();
			$i++;
		}
		
		return $order;
	}
	
	function CreateOrder()
	{
		if (empty($_SESSION['basket_user'])) {
			return false;
		}
		
		$comment = $this->qstr($_SESSION['basket_comment']); // usually not necessary, adodb takes care of this
		
		if (empty($_SESSION['basket_order'])) {
			// insert order record
			$strSQL =
				'INSERT INTO '.$this->ORDERS_TABLE.' (id_user_from, id_user_to, comment, date_order, paid_status, delivery_status)
				 VALUES (?, ?, ?, NOW(), "0", "0")';
			$this->dbconn->Execute($strSQL, array($this->user[0], $_SESSION['basket_user'], $comment));
			$order_id = $this->dbconn->Insert_ID();
		} else {
			// update order record
			$order_id = $_SESSION['basket_order'];
			$strSQL = 'UPDATE '.$this->ORDERS_TABLE.' SET id_user_from=?, id_user_to=?, comment=?, date_order=NOW() WHERE id=?';
			$this->dbconn->Execute($strSQL, array($this->user[0], $_SESSION['basket_user'], $comment, $order_id));
			// delete order items
			$strSQL = 'DELETE FROM '.$this->ORDERS_ITEMS_TABLE.' WHERE id_order=?';
			$this->dbconn->Execute($strSQL, array($order_id));
		}
		
		if (empty($order_id)) {
			return false;
		}
		
		$basket_data = $this->GetBasket();
		
		foreach($basket_data as $item){
			$strSQL = 'INSERT INTO '.$this->ORDERS_ITEMS_TABLE.' (id_order, id_item, quantity, currency) VALUES (?, ?, ?, ?)';
			$this->dbconn->Execute($strSQL, array($order_id, $item['id_item'], $item['quantity'], $item['shop']['price']));
		}
		
		$this->ClearBasket(true);
		
		unset($_SESSION['basket_user']);
		unset($_SESSION['basket_comment']);
		
		return $order_id;
	}
	
	function EditOrder($id)
	{
		$this->ClearBasket(true);
		
		$order = $this->GetOrderInfo($id);
		
		if ($order['paid_status'] != '0') {
			return false;
		}
		
		if ($order['delivery_status'] != '0') {
			return false;
		}
		
		// $this->user[0] = $order['id_user_from'];
		
		$_SESSION['basket_order'] = $order['id'];
		$_SESSION['basket_user'] = $order['id_user_to'];
		$_SESSION['basket_comment'] = stripslashes($order['comment']);
		
		foreach ($order['items'] as $item) {
			$sql = 'INSERT INTO '.$this->BASKET_TABLE.' (id_user, id_item, quantity) VALUES (?, ?, ?)';
			$this->dbconn->Execute($sql, array($this->user[0], $item['id_item'], $item['quantity']));
		}
		
		return true;
	}
	
	function DeleteOrder($id)
	{
		global $dbconn;
		
		$dbconn->Execute('DELETE FROM '.GIFTSHOP_ORDERS_ITEMS.' WHERE id_order=?', array($id));
		$dbconn->Execute('DELETE FROM '.GIFTSHOP_ORDERS.' WHERE id=?', array($id));
	}
	
	function qstr($string)
	{
		$string = stripslashes($string);
		$string = str_replace('"', "&quot;", $string);
		$string = str_replace("'", "&#039;", $string);
		return $string;
	}
	
	function BestSellers($id_category = null, $limit = 5)
	{
		$data = array();
		
		if ($id_category) {
			$strSQL =
				'SELECT o.id_item, COUNT(o.id_item) AS cn
				   FROM '.$this->ORDERS_ITEMS_TABLE.' o
				  INNER JOIN '.$this->ITEM_TABLE.' i ON o.id_item = i.id
				  WHERE i.id_category=? AND i.status="1"
				  GROUP BY o.id_item
				  ORDER BY cn DESC
				  LIMIT 0,'.$limit;
			$rs = $this->dbconn->Execute($strSQL, array($id_category));
		} else {
			$strSQL =
				'SELECT o.id_item, COUNT(o.id_item) AS cn
				   FROM '.$this->ORDERS_ITEMS_TABLE.' o
				  INNER JOIN '.$this->ITEM_TABLE.' i ON o.id_item = i.id
				  WHERE i.status="1"
				  GROUP BY o.id_item
				  ORDER BY cn DESC
				  LIMIT 0,'.$limit;
			$rs = $this->dbconn->Execute($strSQL);
		}
		
		while (!$rs->EOF) {
			$data[] = $this->catalog->items_item($rs->fields[0]);
			$rs->MoveNext();
		}
		/*
		$rs = $this->dbconn->Execute('SELECT id FROM '.$this->ITEM_TABLE.' WHERE id_category=? AND status="1"', array($id_category));
		while (!$rs->EOF) {
			$ids[] = $rs->fields[0];
			$rs->MoveNext();
		}
		$data = array();
		if (!empty($ids)) {
		 	$strSQL =
				'SELECT id_item, COUNT(id_item) AS cn
				   FROM '.$this->ORDERS_ITEMS_TABLE.'
				  WHERE id_item IN ('.implode(',', $ids).')
				  GROUP BY id_item
				  ORDER BY cn DESC
				  LIMIT 0,'.$limit;
			$rs = $this->dbconn->Execute($strSQL);
			while (!$rs->EOF) {
				$data[] = $this->catalog->items_item($rs->fields[0]);
				$rs->MoveNext();
			}
		}
		*/
		return $data;
	}
	
	function PromotedItems($id_category = null, $limit = 5)
	{
		$data = array();
		
		if ($id_category) {
			$strSQL =
				'SELECT id
				   FROM '.$this->ITEM_TABLE.'
				  WHERE id_category=? AND status="1" AND promote="1"
				  ORDER BY id_category, id
				  LIMIT 0,'.$limit;
			$rs = $this->dbconn->Execute($strSQL, array($id_category));
		} else {
			$strSQL =
				'SELECT id
				   FROM '.$this->ITEM_TABLE.'
				  WHERE status="1" AND promote="1"
				  ORDER BY id_category, id
				  LIMIT 0,'.$limit;
			$rs = $this->dbconn->Execute($strSQL);
		}
		
		while (!$rs->EOF) {
			$data[] = $this->catalog->items_item($rs->fields[0]);
			$rs->MoveNext();
		}
		
		return $data;
	}
	
	function SameOrdersItems($id_item, $limit = 5)
	{
		$rs = $this->dbconn->Execute('SELECT id_order FROM '.$this->ORDERS_ITEMS_TABLE.' WHERE id_item=?', array($id_item));
		while (!$rs->EOF) {
			$ids[] = $rs->fields[0];
			$rs->MoveNext();
		}
		$data = array();
		if (!empty($ids)) {
			$strSQL =
				'SELECT id_item, COUNT(id_item) AS cn
				   FROM '.$this->ORDERS_ITEMS_TABLE.'
				  WHERE id_order IN ('.implode(',', $ids).')
				  GROUP BY id_item
				  ORDER BY cn DESC
				  LIMIT 0,'.$limit ;
			$rs = $this->dbconn->Execute($strSQL);
			while (!$rs->EOF) {
				$data[] = $this->catalog->items_item($rs->fields[0]);
				$rs->MoveNext();
			}
		}
		return $data;
	}
	
	function OfflinePaymentPending($id_order)
	{
		$id_user = $this->user[AUTH_ID_USER];
		
		$paysystem = $this->dbconn->GetOne(
			'SELECT paysystem
			   FROM '.$this->BILLING_REQUESTS_TABLE.'
			  WHERE id_user = ? AND id_group = ? AND id_product = ? AND status = "send"
			    AND paysystem IN ("atm_payment", "wire_transfer", "bank_cheque")',
				array($id_user, PG_MY_STORE, $id_order));
		
		return $paysystem;
	}
}

?>