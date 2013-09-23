<?php
			$this->sendBack(new Message('lout', null, null, (isset($msg))?$msg:'login'));
			$this->sendToAll(new Message('rmu', $this->userid));
		
			ChatServer::logout();
			$this->userid = null;
			$ar = $this->getAvailableRoom($GLOBALS['fc_config']['defaultRoom']);
			$this->roomid = $ar['id'];
			$this->room_is_permanent = $ar['ispermanent'] != '';
			
			$this->save();
?>