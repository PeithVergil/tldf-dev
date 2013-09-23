<?php

function DeleteUserFromForum($id)
{
	global $dbconn;
	
	$rs = $dbconn->Execute("SELECT id FROM ".USERS_TABLE." WHERE guest_user='1'");
	$guest_id = $rs->fields[0];
	
	$dbconn->Execute("DELETE FROM ".FORUM_VISITS_TABLE." WHERE id_user = ?", array($id));
	$dbconn->Execute("DELETE FROM ".FORUM_BANS_TABLE." WHERE id_user = ?", array($id));
	$dbconn->Execute("UPDATE ".FORUM_SUBCATEGORIES_TABLE." SET id_user = ? WHERE id_user = ?", array($guest_id, $id));
	$dbconn->Execute("UPDATE ".FORUM_MESSAGES_TABLE." SET id_user = ? WHERE id_user = ?", array($guest_id, $id));
	return;
}

?>