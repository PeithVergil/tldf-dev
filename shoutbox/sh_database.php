<?php

/**
* Shoutbox install file
*
*
* @package DatingPro
* @subpackage Shoutbox module
**/

function init_shoutbox_table() {
	global $dbconn;
	$strSQL = "DROP TABLE IF EXISTS ".SHOUTS_TABLE." ";
	$rs = $dbconn->Execute($strSQL);
	$strSQL = "CREATE TABLE IF NOT EXISTS ".SHOUTS_TABLE."
                (
                  id       int(11)  NOT NULL auto_increment,
                  user_id  int(11)  NOT NULL DEFAULT '0' ,
                  text     mediumblob,
                  date_add datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ,
                  PRIMARY KEY (id)
                )";
	$rs = $dbconn->Execute($strSQL);
	$strSQL = "DROP TABLE IF EXISTS ".SHOUTS_USER_STAT_TABLE." ";
	$rs = $dbconn->Execute($strSQL);
	$strSQL = "CREATE TABLE ".SHOUTS_USER_STAT_TABLE." (
							id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
							id_user INT( 11 ) NOT NULL ,
							count_mess INT( 11 ) NOT NULL DEFAULT '0',
							date_last_mess DATETIME NOT NULL ,
							INDEX ( id_user )
							)";
	$rs = $dbconn->Execute($strSQL);
	$rs = $dbconn-> Execute("INSERT INTO ".SHOUTS_TABLE." VALUES (4, 17, 'Hello all!', '2007-07-06 15:34:04') ");
	$rs = $dbconn-> Execute("INSERT INTO ".SHOUTS_TABLE." VALUES (5, 6, 'Welcome to our community!', '2007-07-06 15:35:55') ");
	$rs = $dbconn-> Execute("INSERT INTO ".SHOUTS_TABLE." VALUES (6, 20, 'I like it there!', '2007-07-06 17:36:04') ");
	$rs = $dbconn-> Execute("INSERT INTO ".SHOUTS_TABLE." VALUES (7, 15, 'Come to chat!', '2007-07-06 17:37:17') ");

	$rs = $dbconn-> Execute("INSERT INTO ".SHOUTS_USER_STAT_TABLE." VALUES (NULL, 17, count_mess+1, NOW())");
	$rs = $dbconn-> Execute("INSERT INTO ".SHOUTS_USER_STAT_TABLE." VALUES (NULL, 6, count_mess+1, NOW()) ");
	$rs = $dbconn-> Execute("INSERT INTO ".SHOUTS_USER_STAT_TABLE." VALUES (NULL, 20, count_mess+1, NOW()) ");
	$rs = $dbconn-> Execute("INSERT INTO ".SHOUTS_USER_STAT_TABLE." VALUES (NULL, 15, count_mess+1, NOW()) ");
	return 0;
}

?>