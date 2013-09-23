<?php
// Типы сообщений
//   message["mess_type"] = 1 - Обычное сообщение пользователя
//   message["mess_type"] = 2 - Сабмит на обычное сообщение
//   message["mess_type"] = 3 - Cообщение о изменении состояния пользователя - OnLine OffLine
//   message["mess_type"] = 4 - Cообщение служебное - для прекращения ожидания новых сообщений
//   message["mess_type"] = 5 - Сообщение которое не нуждается в подтверждении получения
//   message["mess_type"] = 6 - Сообщение о выходе пользователя из программы

//   message["mess_type"] = 7 - Сообщение о необходимости протестировать соединение
//   message["mess_type"] = 8 - Ответ на сообщение 7
//   message["mess_type"] = 9 - Результат тестирования соединения

if (get_magic_quotes_gpc())
{ // Если включены magicquotes - выключаем
	foreach ($_GET as $key => $value)
	{
		$_GET[$key]=stripslashes($value);
	}
	foreach ($_POST as $key => $value)
	{
		$_POST[$key]=stripslashes($value);
	}
	foreach ($_REQUEST as $key => $value)
	{
		$_REQUEST[$key]=stripslashes($value);
	}

}

if (!isset($_GET["act"]))
{
	print(DATING_WCOMMUNICATOR_HEADER);
	print("<br>\nError: act not setted."."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
	return;
}
else
{
	$act = $_GET["act"];
	if ($act=="send")
	{
		print(DATING_WCOMMUNICATOR_HEADER);
		send_message();
	}
	else
	if ($act=="recv")
	{
		print(DATING_WCOMMUNICATOR_HEADER);
		recv_message();
	}
	else
	if ($act=="get_my_users_list")
	{
		print(DATING_WCOMMUNICATOR_HEADER);
		get_my_users_list();
	}
	else
	if ($act=="test_user_exists")
	{
		print(DATING_WCOMMUNICATOR_HEADER);
		test_user_exists();
	}
	else
	if ($act=="invite_user")
	{
		print(DATING_WCOMMUNICATOR_HEADER);
		invite_user();
	}
	else
	if ($act=="delete_user")
	{
		print(DATING_WCOMMUNICATOR_HEADER);
		delete_user();
	}
	else
	if ($act=="ban_user")
	{
		print(DATING_WCOMMUNICATOR_HEADER);
		ban_user();
	}
	else
	if ($act=="recive_photo_url")
	{
		print(DATING_WCOMMUNICATOR_HEADER);
		recive_photo_url();
	}
	else
	if ($act=="recive_lang")
	{
		print(DATING_WCOMMUNICATOR_HEADER);
		recive_lang();
	}
	else  //  ------ POP UP PART -------
	if ($act=="recv_alerts")
	{
		print(DATING_WCOMMUNICATOR_HEADER);
		recv_alerts();
	}
	else
	{
		print(DATING_WCOMMUNICATOR_HEADER);
		print("<br>\nError: Unknown act."."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
		return;
	}

}
return ;

//=============================== Функции ======================================

//==============================================================================
// Оставляем сообщение для пользователя
function send_message()
{
	global $g_user_name;
	// Для отправки сообщений - нужна авторизация
	$login_res = test_login();
	if ($login_res) return;
	if (!isset($_GET["mess_count"]))
	{
		print("<br>\nError: Unknown mess_count."."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
		exit();
	};
	$from_id = get_user_id($g_user_name);
	global $dbconn;

	$mess_count=$_GET["mess_count"];
	for ($i=0; $i<$mess_count; $i++)
	{  // Перебираем все сообщения
		if (isset($_REQUEST["submit_mode".$i]))
		{ // Режим потдверждения сообщения
			if (!isset($_REQUEST["mess_id".$i]))
			{
				print("<br>\nError: Unknown mess_id".$i."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
				exit();
			};

			$mess_id = $_REQUEST["mess_id".$i];
			// Получаем тип сообщения
			$strSQL = "select * from ".USERS_MESSAGES_TABLE." where id='$mess_id' and to_id='$from_id' ";
			$rs = $dbconn->Execute($strSQL);
			if (($rs!==false)&&(!$rs->EOF))
			{
				$row  = $rs->GetRowAssoc(false);
				if ($row["mess_type"]>1)
				{ // Сообщение о подтверждении получения сообщения
					// Сообщение о изменении статуса пользователя
					// Удаляем сообщение из базы
					$strSQL = "DELETE from ".USERS_MESSAGES_TABLE." where id='$mess_id'";
					$rs = $dbconn->Execute($strSQL);
				}
				if ($row["mess_type"]==1)
				{ // Обычное сообщение
					// Направляем его в обратную сторону - ставим тип подтверждение получения
					$strSQL = "update ".USERS_MESSAGES_TABLE." set sended_to_client=0, mess_type=2, from_id='".$row["to_id"]."', to_id='".$row["from_id"]."' where id='$mess_id'";
					$rs = $dbconn->Execute($strSQL);
				}
			};
			print("<br>\nMessage recived".$i.": ok");
			print("<br>\nMessage submited".$i.": ok");
		}
		else
		{
			if (!isset($_GET["mess_type".$i]))
			{
				print("<br>\nError: Unknown mess_type".$i."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
				exit();
			};
			if (!isset($_REQUEST["mess_text".$i]))
			{
				print("<br>\nError: Unknown mess_text.".$i."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
				exit();
			};
			$mess_type = (int)$_GET["mess_type".$i];
			$mess_text = $_REQUEST["mess_text".$i];
			$mess_text = BadWordsCheck($mess_text);
			$mess_text = addslashes($mess_text);

			if (($mess_type==6)||($mess_type==4))
			{ // Сообщение о закрытии программы
				// Cообщение служебное - для прекращения ожидания новых сообщений
				// Высылаются только самому себе
				$to_user = $g_user_name;
				$to_id   = $from_id;
			}
			else
			{
				if (!isset($_GET["to_user".$i]))
				{
					print("<br>\nError: Unknown to_user.".$i."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
					exit();
				};
				$to_user = $_GET["to_user".$i];
				$to_id   = get_user_id($to_user);
			};
			if (isset($_GET["sess_id".$i])) $sess_id = (int)$_GET["sess_id".$i];
			else  $sess_id = '';

			// Проверка - не забанен ли пользователь
			$strSQL = "select count(id) from ".USER_CONTACT_LIST_TABLE." where user_id='".$to_id."' and view_user_id='".$from_id."' and ban_status='1'";
			$rs = $dbconn->Execute($strSQL);
			if (($rs!==false)&&(!$rs->EOF)&&($rs->fields[0]>0))
			{ // А батенька то забанен :)
				print("<br>\nMessage recived".$i.": failed");
				print("<br>\nReason".$i.": Banned");
			}
			else
			{
				$post_date = date('Y-m-d H:i:s');
				$strSQL = "INSERT INTO ".USERS_MESSAGES_TABLE." (from_id, to_id, sess_id, mess_type, mess_text, post_date) VALUES ('$from_id', '$to_id', '$sess_id', '$mess_type', '$mess_text', '$post_date')";
				$rs = $dbconn->Execute($strSQL);
				if ($rs===false)
				{ // Ошибка вставки
					print("<br>\nError: Database error 52."."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
					return false;
				}
				$mess_id = $dbconn->Insert_ID();
				print("<br>\nMessage recived".$i.": ok");
				print("<br>\nMess_id".$i.": $mess_id");
				print("<br>\nMess_type".$i.": $mess_type");
				$send_text=stripslashes($mess_text);
				$send_text=str_replace("<br>\n", "<br>\n<br>\n", $send_text);
				print("<br>\nMess_text".$i.": ".$send_text);
			}
			if ($mess_type==6)
			{  // Переводим пользователя в состояние offline
				$cur_time=time();
				$end_time=$cur_time-1;
				$end_time_formated=date('Y-m-d H:i:s', $end_time);
				$rs=$dbconn->Replace(USERS_ONLINE_TABLE, array('user_id'=>"'".$from_id."'",'online_until'=>"'".$end_time_formated."'"), 'user_id');
			};
		}
	};
	return;
}
//==============================================================================

//==============================================================================
// Оправляем сообщение пользователя
function recv_message()
{
	global $dbconn;
	global $g_user_name;
	ini_set("max_execution_time", 250);
	$user_id = 0;
	$sess_id = "";
	$recvie_status_messages=0;
	$recvie_any_messages=0;
	$recvie_nowait=0;
	$messages_exists=0;
	//  Для получения сообщений - нужно залогиниться
	$login_res = test_login();
	if ($login_res) return;
	$user_id=get_user_id($g_user_name);
	if (isset($_GET["recvie_status_messages"])) $recvie_status_messages=(int)$_GET["recvie_status_messages"];
	if (isset($_GET["recvie_any_messages"])) $recvie_any_messages=(int)$_GET["recvie_any_messages"];
	if (isset($_GET["recvie_nowait"])) $messages_exists=(int)$_GET["recvie_nowait"];
	if (isset($_GET["long_alive"])) $long_alive=(int)$_GET["long_alive"];
	else  $long_alive=0;

	$cur_time = time();
	if ($long_alive==1)
	{
		$end_time = $cur_time+60;  // Не более трех минут
		$end_time_for_resend = 30; // Повторная передача сообщения после 30 секунд максимум
	}
	else
	{
		$end_time = $cur_time+3;
		$end_time_for_resend = $cur_time+2;
	}

	$conact_list = array();

	// Меняем статус пользователя на Online
	// до времени $end_time
	$end_time_formated=date('Y-m-d H:i:s', $end_time+30);
	$rs=$dbconn->Replace(USERS_ONLINE_TABLE, array('user_id'=>"'".$user_id ."'",'online_until'=>"'".$end_time_formated."'"), 'user_id');
	// Загружаем список пользователей из контакт листа
	$contact_users_status_size = 0;
	if ($recvie_status_messages==1)
	{
		$strSQL = "select a.view_user_id, a.view_user_status from ".USER_CONTACT_LIST_TABLE." a where a.user_id = '$user_id' and a.ban_status=0";
		$rs = $dbconn->Execute($strSQL);
		$contact_users_ids="";
		$contact_users_status=array();
		if (($rs!==false)&&(!$rs->EOF))
		{
			while (!$rs->EOF)
			{
				$row  = $rs->GetRowAssoc(false);
				$contact_users_ids.=$row["view_user_id"].",";
				if ($row["view_user_status"]>0) $contact_users_status[$row["view_user_id"]]=$row["view_user_status"];
				$rs->MoveNext();
			}
			// Срезам конец
			$contact_users_ids = substr($contact_users_ids,0,-1);
			$contact_users_status_size = count($contact_users_status);
		}
	}
	while (($cur_time<$end_time)&&($messages_exists==0))
	{
		$cur_time = time();
		if ($recvie_status_messages==1)
		{ // Генерируем сообщения статуса
			// Проверка пользователей на сайте
			$site_min_update_time=date('Y-m-d H:i:s', $cur_time-60*10); // 10 минутный интервал
			$strSQL = "select a.id_user from ".ACTIVE_SESSIONS_TABLE." a where a.id_user in (".$contact_users_ids.") and a.update_date > '$site_min_update_time' and a.ban_status=0";
			$rs = $dbconn->Execute($strSQL);
			$real_online_users=array();
			if (($rs!==false)&&(!$rs->EOF))
			{ // Есть пользователи onsite
				while (!$rs->EOF)
				{
					$row  = $rs->GetRowAssoc(false);
					$real_online_users[$row["id_user"]]=2;
					$rs->MoveNext();
				}
			}

			// Проверка пользователей на онлайн в программе
			$cur_time_formated = date('Y-m-d H:i:s', $cur_time);
			$strSQL = "select a.user_id from ".USERS_ONLINE_TABLE." a where a.user_id in (".$contact_users_ids.") and a.online_until > '$cur_time_formated'";
			$rs = $dbconn->Execute($strSQL);
			if (($rs!==false)&&(!$rs->EOF))
			{ // Есть пользователи online
				while (!$rs->EOF)
				{
					$row  = $rs->GetRowAssoc(false);
					$real_online_users[$row["user_id"]]=1;
					$rs->MoveNext();
				}
			}

			// Смотрим - есть ли пользователи которые ушли в онлайн - офлайн
			$diff  = array_intersect_assoc($real_online_users, $contact_users_status);
			$diff_size = count($diff);
			$real_online_users_size=count($real_online_users);
			if (($diff_size!=$contact_users_status_size)||($diff_size!=$real_online_users_size))
			{ // Есть отличия
				$diff2 = array_diff_assoc($real_online_users,      $diff);
				$diff3 = array_diff_assoc($contact_users_status,   $diff);
				foreach ($diff3 as $key=>$value)
				{
					$diff3[$key]=0;
				}
				$status_changed = $diff2+$diff3;
				// Формируем сообщения на изменения в контакт листе
				$post_date = date('Y-m-d H:i:s');
				foreach ($status_changed as $key=>$value)
				{
					$changed_user_id=$key;
					$changed_status=$value;
					if ($changed_status==1) $changed_mess_text="online";
					else
					if ($changed_status==2) $changed_mess_text="onsite";
					else  $changed_mess_text="offline";
					$rs=$dbconn->Replace(USERS_MESSAGES_TABLE, array('from_id'=>"'".$changed_user_id."'",
					'to_id'=>"'".$user_id."'",
					'mess_type'=>"'3'",
					'mess_text'=>"'".$changed_mess_text."'",
					'post_date'=>"'".$post_date."'"), array('from_id', 'to_id', 'mess_type'));
					if ($rs!==false)
					{ // Транзакция выполнена
						// Фиксируем contact list
						$rs=$dbconn->Replace(USER_CONTACT_LIST_TABLE, array('user_id'=>"'".$user_id."'",
						'view_user_id'=>"'".$changed_user_id."'",
						'view_user_status'=>"'".$changed_status."'"), array('user_id', 'view_user_id'));
						if ($rs===false)
						{
							print("<br>\nError: Database error 342"."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
							exit();
						}
					}
					else
					{
						print("<br>\nError: Database error 341"."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
						exit();
					}
				}
				$rs = $dbconn->Execute($strSQL);
				$messages_exists=1;
			}
		}
		if ($messages_exists==0)
		{ // Все еще нет новых сообщений
			if (($cur_time>$end_time_for_resend)||($recvie_any_messages))
			{  // Появился флаг - получить сообщения без фильтрации посланных
				$strSQL = "select count(id) from ".USERS_MESSAGES_TABLE." where to_id = '$user_id'";
			}
			else
			{  // Фильтруем уже посланные
				$strSQL = "select count(id) from ".USERS_MESSAGES_TABLE." where to_id = '$user_id' and sended_to_client=0";
			}
			$rs = $dbconn->Execute($strSQL);
			if (($rs!==false)&&(!$rs->EOF)&&($rs->fields[0]>0))
			{
				// Нашли запись
				$messages_exists=1;
			}
		}
		if ($messages_exists==0)
		{
			// Ждем секунду
			sleep(1);
		}
	}
	$messages = array();
	$terminate_flag=0;
	if ($messages_exists)
	{  // Сообщения существуют!
		$ids = 0;
		$strSQL = "select * from ".USERS_MESSAGES_TABLE." where to_id = '$user_id'";
		$rs = $dbconn->Execute($strSQL);
		if (($rs!==false)&&(!$rs->EOF))
		{
			// Нашли запись
			$marker_ids="";
			$delete_ids="";
			$total_messages=0;
			while ((!$rs->EOF)&&(($total_messages<50)||$long_alive))  // Высылаем не более 50 сообщений за раз
			{                                                   // Если клиент не долгих соединений
				$total_messages++;
				$mess  = $rs->GetRowAssoc(false);
				if (($mess["mess_type"]==4)||($mess["mess_type"]==5)||($mess["mess_type"]==6)||
				(($mess["mess_type"]==3)&&($recvie_status_messages!=1)) )
				{  // Сообщение выхода из цикла - удаляем его
					// Либо устаревшее сообщение статуса
					$delete_ids.=$mess["id"];
					$delete_ids.=",";
				}
				else
				{
					$marker_ids.=$mess["id"];
					$marker_ids.=",";
				}
				if (($mess["mess_type"]!=4)&&($mess["mess_type"]!=6)&& (!(($mess["mess_type"]==3)&&($recvie_status_messages!=1))) )
				{ // Не высылаем устаревшие сообщение статуса
					// Сообщение типа обновить канал, и сообщение выхода
					$messages[]=$mess;
				}
				if ($mess["mess_type"]==6) $terminate_flag=1;
				$ids++;
				$rs->MoveNext();
			}
			if ($marker_ids!="")
			{ // Увеличиваем счетчики на 1 - что сообщения были высланы клиенту
				$marker_ids = substr($marker_ids,0,-1);
				$strSQL = "update ".USERS_MESSAGES_TABLE." set sended_to_client=sended_to_client+1 where to_id = '$user_id' and id in (".$marker_ids.")";
				$rs = $dbconn->Execute($strSQL);
			}
			if ($delete_ids!="")
			{ // Сообщения просто удаляются из списка
				$delete_ids = substr($delete_ids,0,-1);
				$strSQL = "delete from ".USERS_MESSAGES_TABLE." where id in (".$delete_ids.")";
				$rs = $dbconn->Execute($strSQL);
			}
		}
	}

	// Выставляем время online
	$cur_time=time();
	if (!$terminate_flag)
	{ // Не принудительная остановка
		$end_time=$cur_time+30; // Минута/2 - чтобы снова попытаться забрать сообщение
	}
	else
	{ // Принудительная остановка
		// Выставляем время online - сейчас
		$end_time=$cur_time-1;
	}
	$end_time_formated=date('Y-m-d H:i:s', $end_time);
	$rs=$dbconn->Replace(USERS_ONLINE_TABLE, array('user_id'=>"'".$user_id ."'",'online_until'=>"'".$end_time_formated."'"), 'user_id');


	$size = count($messages);
	if ($size==0)
	{ // Вышли по тайм ауту
		print("<br>\nNew messages: 0");
	}
	else
	{  // Действительно есть сообщения
		print("<br>\nNew messages: $size");
		for ($i=0;$i<$size;$i++)
		{
			print("<br>\nMess_id".$i.": ".$messages[$i]["id"]);
			print("<br>\nMess_type".$i.": ".$messages[$i]["mess_type"]);
			$send_text=stripslashes($messages[$i]["mess_text"]);
			$send_text=str_replace("<br>\n", "<br>\n<br>\n", $send_text);
			print("<br>\nMess_text".$i.": ".$send_text);
			print("<br>\nSess_id".$i.": ".$messages[$i]["sess_id"]);
			print("<br>\nPost_date".$i.": ".strtotime($messages[$i]["post_date"]));
			print("<br>\nFrom_user".$i.": ".get_user_login($messages[$i]["from_id"]));
			print("<br>\nTo_user".$i.": ".$g_user_name);
		};
	}
	// Используется для упрощенного вычисления разницы во времени между сервером и клиентом
	// Для веб flash клиента
	print("<br>\nServer time: ".time());
	return;
}
//==============================================================================

//==============================================================================
// Получить контакт лист для определенного пользователя
function get_my_users_list()
{
	global $dbconn;
	$login_res = test_login();
	if ($login_res) return;
	global $g_user_name;
	$user_id=get_user_id($g_user_name);

	$strSQL = "select b.login as login, a.view_user_id from ".USER_CONTACT_LIST_TABLE." a, ".USERS_TABLE." b where a.user_id = '$user_id' and a.view_user_id=b.id and a.ban_status='0'";
	$rs = $dbconn->Execute($strSQL);
	$users_info = array();
	if (($rs!==false)&&(!$rs->EOF))
	{
		while (!$rs->EOF)
		{
			$row  = $rs->GetRowAssoc(false);
			$view_user_id = strval($row["view_user_id"]);
			
			//VP checking user in Connections list
			$strSQL = 'SELECT COUNT(*) FROM '.CONNECTIONS_TABLE.'
						WHERE id_friend = "'.$view_user_id.'" AND id_user = "'.$user_id.'" AND status = "1"
						OR id_user = "'.$view_user_id.'" AND id_friend = "'.$user_id.'" AND status = "1"';
			$is_connected = $dbconn->getOne($strSQL);
			if (empty($is_connected))
			{
				$_GET["delete_user_name"] = $row["login"];
				delete_user();
			}
			else
			{
				$user_info["name"]=$row["login"];
				$user_info["view_user_id"]=$row["view_user_id"];
				$user_info["state"]=test_user_online_state($user_info["view_user_id"]);
				$users_info[]=$user_info;
			}
			$rs->MoveNext();
		}
	}
	$size = count($users_info);
	print("<br>\nUsers_list_size: $size");
	if ($size)
	{
		for ($i=0;$i<$size;$i++)
		{
			$user_info = $users_info[$i];
			print("<br>\nUser_name".$i.": ".$user_info["name"]);
			$changed_status=0;
			if ($user_info["state"]<0)
			{
				print("<br>\nStatus".$i.": offline");
				print("<br>\nLast_visit".$i.": ".$user_info["state"]);
			}
			else
			if ($user_info["state"]==1)
			{
				print("<br>\nStatus".$i.": online");
				$changed_status=1;
			}
			else
			if ($user_info["state"]==2)
			{
				print("<br>\nStatus".$i.": onsite");
				$changed_status=2;
			}
			else
			{
				print("<br>\nError: Unknown status"."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
				exit();
			}
			// Фиксируем таблицу контакт листа
			$rs=$dbconn->Replace(USER_CONTACT_LIST_TABLE, array('user_id'=>"'".$user_id."'",
			'view_user_id'=>"'".$user_info["view_user_id"]."'",
			'view_user_status'=>"'".$changed_status."'"), array('user_id', 'view_user_id'));
		};
	};
	// Удалить все уведомления о изменении статуса
	$strSQL = "delete from ".USERS_MESSAGES_TABLE." where to_id = '".$user_id."' and mess_type= '3'";
	$rs = $dbconn->Execute($strSQL);
}
//==============================================================================

//==============================================================================
// Проверить на существование определенного пользователя
function test_user_exists()
{
	global $dbconn;
	global $g_user_name;
	$login_res = test_login();
	$user_id=get_user_id($g_user_name);
	
	if ($login_res) return;
	if ((!isset($_GET["test_user_name"]))||($_GET["test_user_name"]==""))
	{
		print("<br>\nError: Unknown test_user_name."."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
		return;
	};
	$user_login=$_GET["test_user_name"];
	
	$strSQL = "select a.login, a.id from ".USERS_TABLE." a where a.login = '$user_login' and a.status='1'";
	$rs = $dbconn->Execute($strSQL);
	if (($rs==false)||($rs->EOF))
	{
		print("<br>\nUser_is_exists: false");
		return ;
	}
	else
	{
		$view_user_id = strval($rs->fields[1]);
		//VP checking user in Connections list
		$strSQL = 'SELECT COUNT(*) FROM '.CONNECTIONS_TABLE.'
					WHERE id_friend = "'.$view_user_id.'" AND id_user = "'.$user_id.'" AND status = "1"
					OR id_user = "'.$view_user_id.'" AND id_friend = "'.$user_id.'" AND status = "1"';
		$is_connected = $dbconn->getOne($strSQL);
		if (empty($is_connected))
		{
			print("<br>\nUser_is_exists: false");
			return ;
		}
	}
	print("<br>\nUser_is_exists: true");
	return ;
}
//==============================================================================

//==============================================================================
// Добавить пользователя в свой контакт лист
function invite_user()
{
	global $dbconn;
	global $g_user_name;
	$login_res = test_login();
	$user_id=get_user_id($g_user_name);
	
	if ($login_res) return;
	if ((!isset($_GET["invite_user_name"]))||($_GET["invite_user_name"]==""))
	{
		print("<br>\nError: Unknown invite_user_name."."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
		return;
	};
	
	$invite_user_login=$_GET["invite_user_name"];
	$view_user_id = get_user_id($invite_user_login);
	if ($user_id==$view_user_id)
	{
		print("<br>\nError: Cant invite myself."."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
		return;
	}
	//VP checking user in Connections list
	$strSQL = 'SELECT COUNT(*) FROM '.CONNECTIONS_TABLE.'
				WHERE id_friend = "'.$view_user_id.'" AND id_user = "'.$user_id.'" AND status = "1"
				OR id_user = "'.$view_user_id.'" AND id_friend = "'.$user_id.'" AND status = "1"';
	$is_connected = $dbconn->getOne($strSQL);
	if (empty($is_connected))
	{
		print("<br>\nError: Not in Connections list."."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
		return;
	}
	else
	{
		$rs=$dbconn->Replace(USER_CONTACT_LIST_TABLE, array('user_id'=>"'".$user_id ."'",'view_user_id'=>"'".$view_user_id."'",'ban_status'=>"'0'"), array('user_id', 'view_user_id'));
		if ($rs==false)
		{
			print("<br>\nInvite_is: failed");
			return ;
		}
	}
	print("<br>\nInvite_is: ok");
	return ;
};
//==============================================================================

//==============================================================================
// Удалить пользователя из контакт листа
function delete_user()
{
	global $dbconn;
	$login_res = test_login();
	if ($login_res) return;
	if ((!isset($_GET["delete_user_name"]))||($_GET["delete_user_name"]==""))
	{
		print("<br>\nError: Unknown delete_user_name."."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
		return;
	};
	global $g_user_name;
	$user_id=get_user_id($g_user_name);
	$delete_user_login=$_GET["delete_user_name"];
	$view_user_id = get_user_id($delete_user_login);
	$strSQL = "DELETE FROM ".USER_CONTACT_LIST_TABLE."  WHERE user_id='".$user_id."' and view_user_id='".$view_user_id."'";
	$rs = $dbconn->Execute($strSQL);
	if ($rs==false)
	{
		print("<br>\nDelete_is: failed");
		return ;
	}
	print("<br>\nDelete_is: ok");
	return ;
};
//==============================================================================

//==============================================================================
function ban_user()
{
	global $dbconn;
	$login_res = test_login();
	if ($login_res) return;
	if ((!isset($_GET["ban_user_name"]))||($_GET["ban_user_name"]==""))
	{
		print("<br>\nError: Unknown ban_user_name."."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
		return;
	};
	global $g_user_name;
	$user_id=get_user_id($g_user_name);
	$ban_user_login=$_GET["ban_user_name"];
	$view_user_id = get_user_id($ban_user_login);
	$rs=$dbconn->Replace(USER_CONTACT_LIST_TABLE, array('user_id'=>"'".$user_id ."'",'view_user_id'=>"'".$view_user_id."'",'ban_status'=>"'1'"), array('user_id', 'view_user_id'));
	if ($rs==false)
	{
		print("<br>\nBan_is: failed");
		return ;
	}
	print("<br>\nBan_is: ok");
	return ;
}
//==============================================================================

//==============================================================================
function recive_photo_url()
{
	global $config, $dbconn;
	$login_res = test_login();
	if ($login_res) return;
	if ((!isset($_GET["photo_user_name"]))||($_GET["photo_user_name"]==""))
	{
		print("<br>\nError: Unknown photo_user_name."."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
		return;
	};
	global $g_user_name;
	$user_id=get_user_id($g_user_name);
	$photo_user_login=$_GET["photo_user_name"];
	$strSQL = "select a.icon_path from ".USERS_TABLE." a where a.login = '$photo_user_login'";
	$rs = $dbconn->Execute($strSQL);
	if (($rs==false)||($rs->EOF))

	{
		print("<br>\nImage_url: none");
		return ;
	}
	$row = $rs->GetRowAssoc(false);
	if ($row["icon_path"]=="")
	{
		print("<br>\nImage_url: none");
		return ;
	}
	$full_url = $config["server"].$config["site_root"]."/uploades/icons/".$row["icon_path"];
	print("<br>\nImage_url: ".$full_url);
	return ;
};
//==============================================================================

//==============================================================================
// Получить языковой файл
function recive_lang()
{
	print("<br>\nUser name: default");
	$user_lang = get_user_lang();
	//$user_lang="russian";
	if ((strpos($user_lang,"\\")!==false)||(strpos($user_lang,"/")!==false)) include "lang/english.php";
	else include "lang/".$user_lang.".php";
	foreach ($wc_lang as $key => $value)
	{
		echo "<br>\n".$key.": ".$value;
	};
	return 0;
};
//==============================================================================


//==============================================================================
// Получить количество непрочитанных сообщений для пользователя
function recv_unreaded_messages_count($user_id)
{
	global $dbconn;
	$strSQL = "select count(id) from ".USERS_MESSAGES_TABLE." where to_id='$user_id' and mess_type=1";
	$rs = $dbconn->Execute($strSQL);
	if (($rs===false)||($rs->EOF))
	{ // Нет такой строки
		return 0;
	}
	else
	{
		return $rs->fields[0];
	};
};
//==============================================================================

//==============================================================================
// Получить pop up flash сообщения
function recv_alerts()
{
	global $dbconn, $g_user_name;
	$login_res = test_login();
	if ($login_res) return;
	if ((!isset($_GET["last_alerted_text_id"]))||($_GET["last_alerted_text_id"]==""))
	{
		print("<br>\nError: Unknown last_alerted_text_id."."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
		return;
	};
	$last_alerted_text_mess_id= (int)$_GET["last_alerted_text_id"];

	$user_id=get_user_id($g_user_name);

	if ($last_alerted_text_mess_id<=0)
	{ // Получить id последнего выведенного через alert сообщения
		$last_alerted_text_mess_id=0;
		$strSQL = "SELECT last_alerted_txt_ids from ".LAST_ALERTED_IDS_TABLE."  WHERE user_id='".$user_id."'";
		$rs = $dbconn->Execute($strSQL);
		if (($rs==false)||($rs->EOF))
		{ // Нет такой строки
		}
		else
		{ // Получаем значение
			$row = $rs->GetRowAssoc(false);
			$last_alerted_text_mess_id = $row["last_alerted_txt_ids"];
		}
		print("<br>\nLast_alerted_text_mess: ".$last_alerted_text_mess_id);
	};

	ini_set("max_execution_time", 250);
	$new_messages_count=0;
	// Получаем текущий статус пользователя
	$cur_time_formated = date('Y-m-d H:i:s', time());
	$strSQL = "select a.user_id from ".USERS_ONLINE_TABLE." a where a.user_id = ".$user_id." and a.online_until > '$cur_time_formated'";
	$rs = $dbconn->Execute($strSQL);
	if (($rs!==false)&&(!$rs->EOF))
	{
		$cur_time = time();
		$end_time = $cur_time+1;           // Не более минуты
		// Всвязи с тем что одновременно браузер не может поддерживать открытыми более двуж открытых соединеней по загрузке страниц
		// Приходится отказываться от получения сообщений методом online - и переключиться в сторону опросника
		// Поэтому держимся в сооедениии одну секунду
		$recive_messages = 0;
		while ($cur_time<$end_time)
		{
			$cur_time = time();
			$cur_time_formated = date('Y-m-d H:i:s', $cur_time);
			$strSQL = "select a.user_id from ".USERS_ONLINE_TABLE." a where a.user_id = ".$user_id." and a.online_until > '$cur_time_formated'";
			$rs = $dbconn->Execute($strSQL);
			if (($rs!==false)&&(!$rs->EOF))
			{ // Пользователь все еще online
				$strSQL = "select * from ".USERS_MESSAGES_TABLE." where to_id = '$user_id' and mess_type in (4,6) and id>".$last_alerted_text_mess_id;
				$rs = $dbconn->Execute($strSQL);
				if ($rs===false)
				{ // Ошибка базы
					print("<br>\nError: Database error 158"."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
					return -1;
				}
				else
				{
					if (!$rs->EOF)
					{  // Есть сообщения 4,6 - прервать передачу
						$recive_messages = 1;
						break;
					};
				};

			}
			else
			{ // Пользователь перешел в состояние offline
				break;
			};
			sleep(1);
		};
		// Не выдаем сообщений пока пользователь online
		if ($recive_messages==0)
		{
			print("<br>\nNew messages: 0");
			print("<br>\nUser_mode: online");
			print("<br>\nServer time: ".time());
			print("<br>\nUnreaded messages count: ".recv_unreaded_messages_count($user_id));
			return 0;
		};
	};
	// Пользователь в offline
	$cur_time = time();
	$end_time = $cur_time+1;           // Не более минуты
	while (($cur_time<$end_time)&&($new_messages_count==0))
	{
		$cur_time = time();

		// Смотрим возможно за это время пользователь перешел в состояние online
		$user_status=0;
		$cur_time_formated = date('Y-m-d H:i:s', time());
		$strSQL = "select a.user_id from ".USERS_ONLINE_TABLE." a where a.user_id = ".$user_id." and a.online_until > '$cur_time_formated'";
		$rs_online = $dbconn->Execute($strSQL);
		if (($rs_online!==false)&&(!$rs_online->EOF))
		{
			$user_status=1;
		};

		if ($user_status)  $strSQL = "select * from ".USERS_MESSAGES_TABLE." where to_id = '$user_id' and mess_type in (4,6) and id>".$last_alerted_text_mess_id;
		else  $strSQL = "select * from ".USERS_MESSAGES_TABLE." where to_id = '$user_id' and mess_type in (1, 4,6) and id>".$last_alerted_text_mess_id;
		$rs = $dbconn->Execute($strSQL);
		if ($rs===false)
		{ // Ошибка базы
			print("<br>\nError: Database error 156"."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
			return -1;
		}
		else
		if (!$rs->EOF)
		{ // Существуют сообщения
			while (!$rs->EOF)
			{
				$mess  = $rs->GetRowAssoc(false);
				$i=$new_messages_count;
				print("<br>\nMess_id".$i.": ".$mess["id"]);
				print("<br>\nMess_type".$i.": ".$mess["mess_type"]);
				$send_text=stripslashes($mess["mess_text"]);
				$send_text=str_replace("<br>\n", "<br>\n<br>\n", $send_text);
				print("<br>\nMess_text".$i.": ".$send_text);
				print("<br>\nSess_id".$i.": ".$mess["sess_id"]);
				print("<br>\nPost_date".$i.": ".strtotime($mess["post_date"]));
				print("<br>\nFrom_user".$i.": ".get_user_login($mess["from_id"]));
				print("<br>\nTo_user".$i.": ".$g_user_name);
				$rs->MoveNext();
				$new_messages_count++;
				if ($mess["id"]>$last_alerted_text_mess_id) $last_alerted_text_mess_id=$mess["id"];
			}
		}
		if ($new_messages_count==0) sleep(1);
	};
	print("<br>\nNew messages: ".$new_messages_count);
	if ($new_messages_count)
	{ // Сдвигаем последнее alert сообщение
		$saved_last_alerted_text_mess_id=0;
		$strSQL = "SELECT last_alerted_txt_ids from ".LAST_ALERTED_IDS_TABLE."  WHERE user_id='".$user_id."'";
		$rs = $dbconn->Execute($strSQL);
		if (($rs===false)||($rs->EOF))
		{ // Нет такой строки
		}
		else
		{ // Получаем значение
			$row = $rs->GetRowAssoc(false);
			$saved_last_alerted_text_mess_id = $row["last_alerted_txt_ids"];
		};
		if ($saved_last_alerted_text_mess_id<$last_alerted_text_mess_id)
		{
			$rs = $dbconn->Replace(LAST_ALERTED_IDS_TABLE, array('user_id'=>$user_id, 'last_alerted_txt_ids'=>$last_alerted_text_mess_id), 'user_id');
			if ($rs===false)
			{
				print("<br>\nError: Database error 155"."<br>\nError file: ".__FILE__."<br>\nError file line: ".__LINE__);
				return -1;
			}
		}
	};
	// Используется для упрощенного вычисления разницы во времени между сервером и клиентом
	// Для веб flash клиента
	print("<br>\nServer time: ".time());
	print("<br>\nUnreaded messages count: ".recv_unreaded_messages_count($user_id));
	return 0;
};
//==============================================================================


function BadWordsCheck($text='') {
	global $dbconn, $config;

//	$bw_arr = array();
	$rs = $dbconn->Execute("SELECT name, value FROM ".SETTINGS_TABLE." WHERE name IN ('badwords_file_path', 'badwords_file_name')");
	while(!$rs->EOF){
		$row = $rs->GetRowAssoc(false);
		$settings[$row["name"]] = $row["value"];
		$rs->MoveNext();
	}

	$file_path = BWDelLastSlash($config["site_path"])."/".BWTrimSlash($settings["badwords_file_path"])."/".BWTrimSlash($settings["badwords_file_name"]);

	if ( file_exists($file_path) && is_readable($file_path) ) {
		$bw_file = strtolower(implode("", file($file_path)));
		$bw_file = explode(",", $bw_file);
		foreach($bw_file as $k => $v){
			$pos = eregi("(^| |[[:punct:]])".trim($v)."($| |[[:punct:]])", $text);
			if(intval($pos) != 0){ /// find
				$text = str_replace(trim($v),"#$%^&*",$text);
				break;
			}
		}
	}
	return $text;
}

function BWDelFirstSlash($str){
	$str = strval($str);
	if($str[0]=="/")
	return substr($str,1);
	else
	return $str;
}

function BWDelLastSlash($str){
	$str = strval($str);
	if($str[strlen($str)-1]=="/")
	return substr($str,0,-1);
	else
	return $str;
}

function BWTrimSlash($str){
	return BWDelFirstSlash(BWDelLastSlash(strval($str)));
}

?>