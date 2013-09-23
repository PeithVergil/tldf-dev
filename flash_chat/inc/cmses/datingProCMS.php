<?php
$dpro_root_path = realpath(dirname(__FILE__) . '/../../../') . '/';

include($dpro_root_path . "include/config.php");
include($dpro_root_path . "common.php");
include($dpro_root_path . "include/functions_auth.php");

class DatingProCMS {

	var $userid = null;

	var $loginStmt;
	var $getUserStmt;
	var $getUsersStmt;

	function DatingProCMS()  {

		$this->getUserStmt = new Statement("SELECT a.id, a.login, a.gender, b.status as root_user FROM ".USERS_TABLE." a LEFT JOIN ".F_CHAT_MODERATORS_TABLE." b ON b.userid=a.id WHERE a.id=? LIMIT 1");
		$this->loginStmt = new Statement("SELECT a.id, a.login, a.gender, b.status as root_user FROM ".USERS_TABLE." a LEFT JOIN ".F_CHAT_MODERATORS_TABLE." b ON b.userid=a.id WHERE a.login=? AND a.password=? LIMIT 1");
		$this->getUsersStmt = new Statement("SELECT a.id, a.login, a.gender, b.status as root_user FROM ".USERS_TABLE." a LEFT JOIN ".F_CHAT_MODERATORS_TABLE." b ON b.userid=a.id");

		$user = auth_index_user();
		if (!$user[ AUTH_GUEST ]) $this->userid = $user[ AUTH_ID_USER ];
		else $this->userid = null;
	}

	function isLoggedIn() {
		return $this->userid;
	}

	function login($login, $password) {

		global $dbconn;

		if ($this->userid) {
			// delete old session
			$rs = $dbconn->Execute('DELETE FROM '.ACTIVE_SESSIONS_TABLE.' WHERE id_user = ? AND session = ?', array($this->userid, session_id()));
			$rs = $dbconn->Execute('DELETE FROM '.ONLINE_NOTICE_TABLE.' WHERE id_from = ? and type = "1"', array($this->userid));
		}

		$this->userid = null;
		$_POST["login_lg"] = $login;
		$_POST["pass_lg"] = $password;


		$u = auth_user();
		if ($u[3] != 1) {
			$this->userid = $u[0];
		}
		else
		$this->userid = NULL;


		return $this->userid;
	}

	function logout(){
		$this->userid = null;

	}

	function getUser($userid) {
		$rv = NULL;

		$rs = $this->getUserStmt->process($userid);

		if ($rec = $rs->next()) {
			$rec["roles"] = $this->getRoles($rec["root_user"]);
			$rv = $rec;
		}

		return $rv;
	}

	function getUsers() {
		$rs = $this->getUsersStmt->process();
		$rv = array();
		while ($rec = $rs->next()) {
			$rec["roles"] = $this->getRoles($rec["root_user"]);
			$rv[] = $rec;
		}
		return $rv;
	}

	function getUserProfile($userid) {
		if($userid == SPY_USERID) return null;

		return $GLOBALS["config"]["site_root"] . (($userid == $this->userid) ? "/myprofile.php" : "/viewprofile.php?id=".$userid);
	}

	function getPermMode($file){
		$u = auth_user();
		if (!$u[8]) return 0;
		return $mode = FCIsFileAllowed($u[0], FCGetRightModulePath($file));
	}
	function PermErrPage($file_path){
		global $dbconn;
		$file = FCGetRightModulePath($file_path);
		$strSQL = "select id_module from ".MODULE_FILE_TABLE." where file='".$file."' ";
		$rs = $dbconn->Execute($strSQL);
		$id_module = $rs->fields[0];
		echo "<script>if(opener){ opener.location.href='../alert.php?id_module=".$id_module."&err=1'; window.close(); opener.focus();}</script>";
		exit;
	}
	function LoginCheck($file){
		$u = auth_user();
		$mode =FCIsFileAllowed($u[0], FCGetRightModulePath($file), "chats");
		if($mode==0){
			FCPermissionError();
			exit;
		}
		return;
	}

	function getRoles($group) {
		$rv = NULL;
		if ($group == 1) {
			$rv = ROLE_ADMIN;
		}
		elseif ($GLOBALS['fc_config']['liveSupportMode']) {
			$rv = ROLE_CUSTOMER;
		}
		else
		$rv = ROLE_USER;

		return $rv;
	}

	function userInRole($userid, $role) {
		if($user = $this->getUser($userid)) {
			return ($user['roles'] & $role) != 0;
		}
		return false;
	}

	function getGender($user) {
		// 'M' for Male, 'F' for Female, NULL for undefined
		$pr = $this->getUser($user);
		if ($pr["gender"] == '1') return 'M';
		elseif ($pr["gender"] == '2') return 'F';
		else return NULL;
	}
	function getSettings($settings_name){
		global $dbconn;
		$strSQL = "Select value from ".SETTINGS_TABLE." where name ='".$settings_name."' ";
		$rs = $dbconn->Execute($strSQL);
		return $rs->fields[0];
	}
	function UpdateSettings($settings_name, $settings_value){
		global $dbconn;
		$strSQL = "Update ".SETTINGS_TABLE." set value='".$settings_value."' where name ='".$settings_name."' ";
		$dbconn->Execute($strSQL);
		return;
	}

}
function FCPermissionError(){
	global $smarty, $dbconn, $config, $config_admin, $lang, $auth;
	header("location: ".$config["server"].$config["site_root"]."/admin/index.php?err=1");
	echo "<script>location.href='".$config["server"].$config["site_root"]."/admin/index.php?err=1';</script>";
	exit;
}
///////////////////////////////////////////////////////////////////////////////////////////
function FCIsFileAllowed($id_user, $file, $lang_type=""){
	global $dbconn, $config;
	$mod_arr = array();
	$mod_arr = FCGetPermissionsUser($id_user);
	$strSQL = "select id_module from ".MODULE_FILE_TABLE." where file='".$file."' ";
	$rs = $dbconn->Execute($strSQL);
	$id_module = $rs->fields[0];
	if(is_array($mod_arr["id"]) && in_array($id_module, $mod_arr["id"]) ){
		return "1";
	} else {
		return "0";
	}
}
///////////////////////////////////////////////////////////////////////////////////////////
function FCGetPermissionsUser($id_user){
	global $dbconn;
	$strSQL = "select distinct a.id_module from ".GROUP_MODULE_TABLE." a, ".USER_GROUP_TABLE." b
					where b.id_user='".$id_user."' and b.id_group=a.id_group ";
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	while(!$rs->EOF){
		$module["id"][$i] = $rs->fields[0];
		$rs->MoveNext();
		$i++;
	}
	return $module;
}

///////////////////////////////////////////////////////////////////////////////////////
function FCGetRightModulePath($file){
	global $config;
	$file_name = substr($file, strlen($config["site_path"]));
	$file_name = str_replace("\\", "/", $file_name);
	if(substr($file_name, 0, 1) != "/") $file_name = "/".$file_name;
	return $file_name;
}

function FCGetModulesArr(){
	global $dbconn;

	$_arr = array();
	$_arr['anchor'] = array();
	$_arr['path'] = array();
	$_arr['stretch'] = array();
	$_arr['float_x'] = array();
	$_arr['float_y'] = array();
	$_arr['float_w'] = array();
	$_arr['float_h'] = array();
	$strSQL = " SELECT module_id, anchor, module_path, stretch, float_x, float_y, float_w, float_h FROM {$GLOBALS['fc_config']['db']['pref']}modules WHERE module_status='1' ";
	$rs = $dbconn->Execute($strSQL);
	while(!$rs->EOF){
		array_push($_arr['anchor'], $rs->fields[1]);
		array_push($_arr['path'], $rs->fields[2]);
		array_push($_arr['stretch'], $rs->fields[3]);
		array_push($_arr['float_x'], $rs->fields[4]);
		array_push($_arr['float_y'], $rs->fields[5]);
		array_push($_arr['float_w'], $rs->fields[6]);
		array_push($_arr['float_h'], $rs->fields[7]);
		$rs->MoveNext();
	}
	$out_arr = array(
	'anchor' => implode(',', $_arr['anchor']),
	'path' => implode(',', $_arr['path']),
	'stretch' => implode(',', $_arr['stretch']),
	'float_x' => implode(',', $_arr['float_x']),
	'float_y' => implode(',', $_arr['float_y']),
	'float_w' => implode(',', $_arr['float_w']),
	'float_h' => implode(',', $_arr['float_h']),
	);
	return $out_arr;
}

$GLOBALS['fc_config']['db'] = array(
'host' => $config["dbhost"],
'user' => $config["dbuname"],
'pass' => $config["dbpass"],
'base' => $config["dbname"],
'pref' => $config["table_prefix"] . "fc_",
);

$GLOBALS['fc_config']['defaultTheme'] = $config["color"]["fc_theme"];
$GLOBALS['fc_config']['login']['theme'] = $config["color"]["fc_theme"];
require_once(INC_DIR . 'themes/'.$GLOBALS['fc_config']['defaultTheme'].'.php');//include only one theme - site color theme

$GLOBALS['fc_config']['cms'] = new DatingProCMS();
$GLOBALS['fc_config']['enableBots'] = $GLOBALS['fc_config']['cms']->getSettings("enable_bots");
$GLOBALS['fc_config']['module'] = FCGetModulesArr();

?>