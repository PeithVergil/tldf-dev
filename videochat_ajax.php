<?php

include './include/config.php';
include './common.php';
include './include/config_index.php';
include './include/functions_auth.php';
include './include/functions_index.php';
include './include/class.lang.php';

// authentication, true for ajax script
$user = auth_index_user(true);

if (empty($user) || $user == 'err' || empty($user[ AUTH_ID_USER ])) {
	echo 'authentication error';
	exit;
}

// check group, period, expiration
# try without in ajax script. RefreshAccount resets $user[ AUTH_ID_GROUP ] and $_SESSION['permissions'] but we don't need it here
# RefreshAccount();

$id_user = $user[ AUTH_ID_USER ];

// handle GET request

if (isset($_GET['cmd'])) {
	switch ($_GET['cmd']) {
		case 'check_status':
			$id = (int)$_GET['id'];
			$rs = $dbconn->Execute('SELECT status FROM chat_request_log WHERE id = ?', array($id));
			if ($rs->RowCount() == 0) {
				echo 'no_request';
			} else {
				echo $rs->fields[0];
			}
			exit;
		
		case 'check_invite':
			$rs = $dbconn->Execute(
				'SELECT l.id, l.request_type, u.fname, u.sname, u.login
				   FROM chat_request_log l
			  LEFT JOIN user u ON u.id = l.from_id
			 WHERE l.to_id = ? AND l.status = "Waiting"',
			 array($id_user));
			if ($rs->RowCount() == 0) {
				echo '{"id" : -1}';
			} else {
				$row = $rs->GetRowAssoc(false);
				$row['id'] = (int)$row['id'];
				echo json_encode($row);
			}
			exit;
	}
}

// handle POST request

if (isset($_POST['cmd'])) {
	switch ($_POST['cmd']) {
		case 'invite':
			$to_id = (int)$_POST['to_id'];
			$type = $_POST['type'];
			$rs = $dbconn->Execute(
				'SELECT id
				   FROM chat_request_log
				  WHERE (to_id = ? AND from_id <> ? OR from_id = ? AND to_id <> ?)
				    AND status = "Waiting"',
				array($to_id, $id_user, $to_id, $id_user));
			if ($rs->RowCount() > 0) {
				echo 'occupied';
				exit;
			}
			$rs = $dbconn->Execute(
				'SELECT id
				   FROM chat_request_log
				  WHERE (from_id = ? AND to_id = ? OR from_id = ? AND to_id = ?)
				    AND status = "Waiting"
				  ORDER BY id',
				array($id_user, $to_id, $to_id, $id_user));
			if ($rs->RowCount() == 0) {
				$dbconn->Execute(
					'INSERT INTO chat_request_log SET from_id = ?, to_id = ?, request_type = ?, status = "Waiting"',
					array($id_user, $to_id, $type));
				$request_id = $dbconn->Insert_ID();
			} else {
				$request_id = $rs->fields[0];
			}
			echo $request_id;
			exit;
		
		case 'accept':
			$id = (int)$_POST['id'];
			$rs = $dbconn->Execute('UPDATE chat_request_log SET status = "Accepted" WHERE id = ? AND status = "Waiting"', array($id));
			exit;
		
		case 'deny':
			$id = (int)$_POST['id'];
			$rs = $dbconn->Execute('UPDATE chat_request_log SET status = "Denied" WHERE id = ? AND status = "Waiting"', array($id));
			exit;
		
		case 'cancel':
			$id = (int)$_POST['id'];
			$rs = $dbconn->Execute('UPDATE chat_request_log SET status = "Cancelled" WHERE id = ? AND status = "Waiting"', array($id));
			exit;
	}
}

?>