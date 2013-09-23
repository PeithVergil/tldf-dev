<?php

/**
* Functions for users authorization
*
* @package DatingPro
* @subpackage Include files
**/

// code formatting and additions by ralf strehle for meetmenow

define('AUTH_ID_USER', 0);
define('AUTH_FNAME', 1);
define('AUTH_SNAME', 2);
define('AUTH_GUEST', 3);
define('AUTH_ROOT', 4);
define('AUTH_LOGIN', 5);
define('AUTH_GENDER', 6);
define('AUTH_TYPE', 7);
define('AUTH_STATUS', 8);
define('AUTH_EMAIL', 9);
define('AUTH_SITE_LANGUAGE', 10);
define('AUTH_APPLICATION_SUBMITTED', 11);
define('AUTH_MASTER_LOGIN', 12);
define('AUTH_PLATINUM_APPLIED', 13);
define('AUTH_DATE_BIRTHDAY', 14);
define('AUTH_ID_COUNTRY', 15);
define('AUTH_CITY', 16);
define('AUTH_PHONE', 17);
define('AUTH_ZIPCODE', 18);
define('AUTH_ID_GROUP', 19);
define('AUTH_ID_SOLVE360', 20);
define('AUTH_IS_APPLICANT', 40);
define('AUTH_IS_TRIAL', 41);
define('AUTH_IS_REGULAR', 42);
define('AUTH_IS_PLATINUM', 43);
define('AUTH_IS_ELITE', 44);
define('AUTH_IS_TRIAL_INACTIVE', 45);
define('AUTH_IS_REGULAR_INACTIVE', 46);
define('AUTH_IS_PLATINUM_INACTIVE', 47);
define('AUTH_IS_ELITE_INACTIVE', 48);
define('AUTH_IS_INACTIVE', 49);

global $functions_auth_php_included;

if (!isset($functions_auth_php_included))
{
	$functions_auth_php_included = 'included';
	
	/**
	 * central authorization function in user area
	 **/
	
	function auth_index_user($ajax = false)
	{
		$sess_id = session_id();
		
		if (!$sess_id) {
			$sess_id = $PHPSESSID;
		}
		
		$login = isset($_POST['login_lg']) ? trim($_POST['login_lg']) : '';
		$pass = isset($_POST['pass_lg']) ? trim($_POST['pass_lg']) : '';
		
		if ($ajax == false) {
			// destroy all timed out sessions
			sess_clear(TIMEOUT_SECONDS);
		}
		
		// get id_user and type from session table
		// $sess_type (0 = normal login, 1 = master login)
		list($check_id, $sess_type) = sess_read($sess_id);
		
		if (empty($check_id))
		{
			// user not logged in, not even the guest user.
			if (isset($_POST['login_lg']) || isset($_GET['login_id']) && isset($_GET['token']))
			{
				// let's try to log in
				if (isset($_POST['login_lg']))
				{
					// manual login (hard to imagine how this can happen, but let's do it)
					$auth = auth_user_read(0, $login, $pass);
					
					if (!is_array($auth) || empty($auth[ AUTH_ID_USER ])) {
						// login error, create guest record
						$auth_guest = auth_guest_read();
						if (is_array($auth_guest) && !empty($auth_guest[ AUTH_ID_USER ])) {
							sess_write($sess_id, $auth_guest[ AUTH_ID_USER ]);
						}
						return 'err';
					}
				}
				else
				{
					// token login
					$auth = auth_user_read(3);
					
					if (!is_array($auth) || empty($auth[ AUTH_ID_USER ])) {
						// login error, create guest record
						$auth_guest = auth_guest_read();
						if (is_array($auth_guest) && !empty($auth_guest[ AUTH_ID_USER ])) {
							sess_write($sess_id, $auth_guest[ AUTH_ID_USER ]);
						}
						return $auth;
					}
				}
				
				// remove chat invites
				RemoveChatInvites($auth[ AUTH_ID_USER ]);
				
				// inform all friends what we signed in
				SetAlertsMessage($auth[ AUTH_ID_USER ]);
				
				// update login statistics
				SetLoginStatistic($auth[ AUTH_ID_USER ]);
				
				// store user in session table
				sess_write($sess_id, $auth[ AUTH_ID_USER ]);
				
				return $auth;
			}
			else
			{
				// no login attempt, so get guest user data from user table
				$auth = auth_guest_read();
				
				if (!is_array($auth) || empty($auth[ AUTH_ID_USER ])) {
					// guest record not found in user table
					return 'err';
				}
				
				// create guest record in session table
				sess_write($sess_id, $auth[ AUTH_ID_USER ]);
				
				return $auth;
			}
		}
		else
		{
			// get user data from user table, could also be guest user
			$auth = auth_user_read(1, '', '', $check_id, $sess_type);
			
			if (!is_array($auth) || empty($auth[ AUTH_ID_USER ])) {
				// not found in user table
				return 'err';
			}
			
			if ($auth[ AUTH_GUEST ] && (isset($_POST['login_lg']) || isset($_GET['login_id']) && isset($_GET['token'])))
			{
				// user was registered as guest, we try to log in him now
				if (isset($_POST['login_lg']))
				{
					// manual login
					$auth = auth_user_read(0, $login, $pass);
					
					if (!is_array($auth) || empty($auth[ AUTH_ID_USER ])) {
						// match once again with admin password
						$auth = auth_user_read(2, $login, $pass);
						
						if (!is_array($auth) || empty($auth[ AUTH_ID_USER ])) {
							// login error
							return 'err';
						}
					}
				}
				else
				{
					// token login
					$auth = auth_user_read(3);
					
					if (!is_array($auth) || empty($auth[ AUTH_ID_USER ])) {
						// login error
						return $auth;
					}
				}
				
				if ($auth[ AUTH_MASTER_LOGIN ])
				{
					/* echo "<script>alert('Master login..');</script>";*/
					
					// store user in session table
					sess_write($sess_id, $auth[ AUTH_ID_USER ], 1);
				}
				else
				{
					/* echo "<script>alert('User login..');</script>"; */
					
					// remove chat invites
					RemoveChatInvites($auth[ AUTH_ID_USER ]);
					
					// inform all friends what we signed in
					SetAlertsMessage($auth[ AUTH_ID_USER ]);
					
					// update login statistics
					SetLoginStatistic($auth[ AUTH_ID_USER ]);
					
					// store user in session table
					sess_write($sess_id, $auth[ AUTH_ID_USER ], 0);
				}
				
				return $auth;
			}
			
			if (empty($auth[ AUTH_MASTER_LOGIN ]) && $ajax == false) {
				// user already registered in session table
				// update date in session table and date_last_seen in user table
				auth_user_update_date($sess_id, $auth[ AUTH_ID_USER ]);
			}
			
			return $auth;
		}
	}
	
	/**
	 * central authorization function in admin area
	 **/
	
	function auth_user($ajax = false)
	{
		// get sessionid
		$sess_id = session_id();
		
		if (!$sess_id) {
			$sess_id = $PHPSESSID;
		}
		
		// prepare login and password
		$login = (isset($_POST['login_lg'])) ? trim($_POST['login_lg']) : '';
		$pass = (isset($_POST['pass_lg'])) ? trim($_POST['pass_lg']) : '';
		
		// remove timed out session records from session table, timeout = 60 minutes
		if ($ajax == false) {
			sess_clear(TIMEOUT_SECONDS);
		}
		
		// get id_user from session table
		list($check_id, $sess_type) = sess_read($sess_id);
		
		if (empty($check_id))
		{
			if (isset($_POST['login_lg']))
			{
				// perform login
				$auth = auth_user_read(0, $login, $pass);
				
				if (!is_array($auth) || empty($auth[ AUTH_ID_USER ])) {
					// login error
					return 'err';
				}
				
				if (! $auth[ AUTH_ROOT ]) {
					// simple users can't login in admin area
					return 'err';
				}
				
				// store id_user in session table
				sess_write($sess_id, $auth[ AUTH_ID_USER ]);
				
				return $auth;
			}
			else
			{
				// no login attempt
				return 'err';
			}
		}
		else
		{
			// get admin data based on session record
			$auth = auth_user_read(1, '', '', $check_id, '');
			
			if (!is_array($auth) || empty($auth[ AUTH_ID_USER ])) {
				// not found in user table
				return 'err';
			}
			
			if (isset($_POST['login_lg']) && $auth[ AUTH_GUEST ])
			{
				$auth = auth_user_read(0, $login, $pass);
				
				if (!is_array($auth) || empty($auth[ AUTH_ID_USER ])) {
					// login error
					return 'err';
				}
				
				if (! $auth[ AUTH_ROOT ]) {
					// simple users can't login in admin area
					return 'err';
				}
				
				// store id_user in session table
				sess_write($sess_id, $auth[ AUTH_ID_USER ]);
				
				return $auth;
			}
			
			if (! $auth[ AUTH_ROOT ]) {
				// simple users can't login in admin area
				return 'err';
			}
			
			if ($ajax == false) {
				// update date in session table and date_last_seen in user table
				auth_user_update_date($sess_id, $auth[ AUTH_ID_USER ]);
			}
			
			return $auth;
		}
	}
	
	/**
	 * get selected user data
	 * type = 0 is login
	 * type = 1 is read user data based on id_user in session table
	 * type = 2 is login with user login and admin password (master login)
	 **/
	
	function auth_user_read($type, $auth_login = null, $auth_pass = null, $id = null, $master_login = null)
	{
		global $dbconn;
		
		switch ($type)
		{
			case 0:
			
				// login with login and password
				$master_login = 0;
				
				$strSQL =
					'SELECT id, fname, sname, guest_user, root_user, login, gender, status, email, site_language,
							date_birthday, id_country, mm_city, mm_contact_phone_number, zipcode,
							mm_application_submit, mm_platinum_applied, id_solve360
					   FROM '.USERS_TABLE.'
					  WHERE login = ? AND password = ?';
				
				$rs = $dbconn->Execute($strSQL, array($auth_login, md5($auth_pass)));
				
			break;
			
			case 1:
			
				// user already logged in, master_login flag is passed in parameter
				$strSQL =
					'SELECT id, fname, sname, guest_user, root_user, login, gender, status, email, site_language,
							date_birthday, id_country, mm_city, mm_contact_phone_number, zipcode,
							mm_application_submit, mm_platinum_applied, id_solve360
					   FROM '.USERS_TABLE.'
					  WHERE id = ?';
				
				$rs = $dbconn->Execute($strSQL, array($id));
				
			break;
			
			case 2:
			
				// login with user login and admin password
				$master_login = 1;
				
				$strSQL =
					'SELECT id, fname, sname, guest_user, root_user, login, gender, status, email, site_language,
							date_birthday, id_country, mm_city, mm_contact_phone_number, zipcode,
							mm_application_submit, mm_platinum_applied, id_solve360
					   FROM '.USERS_TABLE.'
					  WHERE login = ? AND ? = (SELECT password FROM '.USERS_TABLE.' WHERE root_user = "1")';
				
				$rs = $dbconn->Execute($strSQL, array($auth_login, md5($auth_pass)));
				
			break;
			
			case 3:
				
				// login with token
				$master_login = 0;
				
				// override $type to trigger code which runs only once after login
				$type = 0;
				
				$rst = $dbconn->Execute(
					'SELECT id, id_user, DATEDIFF(NOW(), created)
					   FROM '.USER_TOKEN_TABLE.'
					  WHERE id_user = ? AND token = ?',
					  array($_GET['login_id'], $_GET['token']));
				if ($rst->fields[0] > 0) {
					if ($rst->fields[2] > 30) {
						// token is expired
						$dbconn->Execute('DELETE FROM '.USER_TOKEN_TABLE.' WHERE id = ?', array($rst->fields[0]));
						return 'err_token_expired';
					} else {
						// success
						## $dbconn->Execute('DELETE FROM '.USER_TOKEN_TABLE.' WHERE id = ?', array($rst->fields[0]));
						$strSQL =
							'SELECT id, fname, sname, guest_user, root_user, login, gender, status,
									email, site_language, date_birthday, id_country, mm_city,
									mm_contact_phone_number, zipcode, mm_application_submit, mm_platinum_applied,
									id_solve360
							   FROM '.USERS_TABLE.'
							  WHERE id = ?';
						$rs = $dbconn->Execute($strSQL, array($rst->fields[1]));
					}
				} else {
					// token not found
					return 'err_token_invalid';
				}
				
			break;
			
			default:
			
				return 'err';
			
			break;
		}
		
		if ($rs->EOF) {
			return 'err';
		}
		
		$row = $rs->GetRowAssoc(false);
		
		$id_group = (int) $dbconn->getOne('SELECT id_group FROM '.USER_GROUP_TABLE.' WHERE id_user = ?', array($row['id']));
		
		$user = array(
			(int) $row['id'],								// 0
				  $row['fname'],							// 1
				  $row['sname'],							// 2
			(int) $row['guest_user'],						// 3
			(int) $row['root_user'],						// 4
				  $row['login'],							// 5
			(int) $row['gender'],							// 6
				  $type,									// 7
			(int) $row['status'],							// 8
				  $row['email'],							// 9
				  $row['site_language'],					// 10
				  !empty($row['mm_application_submit']),	// 11
				  $master_login,							// 12
				  !empty($row['mm_platinum_applied']),		// 13
				  $row['date_birthday'],					// 14
				  $row['id_country'],						// 15
				  $row['mm_city'],							// 16
				  $row['mm_contact_phone_number'],			// 17
				  $row['zipcode'],							// 18
				  $id_group,								// 19
				  $row['id_solve360']						// 20
		);
		
		// type needed for payments
		// if user login firstly(with login pass) we dont remove points from his account and only refresh date of account
		// else we remove points spended during period between last and present refreshs
		
		$_SESSION['permissions'] = meetme_GetPermissions($user); // $user call by reference
		
		AssignUserToSmarty($user);
		
		return $user;
	}
	
	/**
	 * get guest data
	 **/
	
	function auth_guest_read()
	{
		global $dbconn, $smarty;
		
		$rs = $dbconn->Execute(
			'SELECT id, fname, sname, guest_user, root_user, login, gender, status, email, site_language
			   FROM '.USERS_TABLE.'
			  WHERE guest_user = "1"');
		
		if ($rs->RowCount() == 0) {
			return '';
		}
		
		$row = $rs->GetRowAssoc(false);
		
		$guest = array(
			(int) $row['id'],				// 0
				  $row['fname'],			// 1
				  $row['sname'],			// 2
			(int) $row['guest_user'],		// 3
			(int) $row['root_user'],		// 4
				  $row['login'],			// 5
			(int) $row['gender'],			// 6
				  1,						// 7
			(int) $row['status'],			// 8
				  $row['email'],			// 9
				  $row['site_language']		// 10
		);
		
		$guest[ AUTH_ID_GROUP ] = MM_GUEST_GROUP_ID;
		
		$_SESSION['permissions'] = meetme_GetPermissions($guest); // $guest call by reference
		
		$smarty->assign('auth', array(
			'id_user'				=> $guest[ AUTH_ID_USER ],
			'fname'					=> $guest[ AUTH_FNAME ],
			'sname'					=> $guest[ AUTH_SNAME ],
			'guest'					=> $guest[ AUTH_GUEST ],
			'root'					=> $guest[ AUTH_ROOT ],
			'login'					=> $guest[ AUTH_LOGIN ],
			'gender'				=> $guest[ AUTH_GENDER ],
			'type'					=> $guest[ AUTH_TYPE ],
			'status'				=> $guest[ AUTH_STATUS ],
			'email'					=> $guest[ AUTH_EMAIL ],
			'site_language'			=> $guest[ AUTH_SITE_LANGUAGE ],
			'id_group'				=> $guest[ AUTH_ID_GROUP ],
			'is_applicant'			=> 0,
			'is_trial'				=> 0,
			'is_regular'			=> 0,
			'is_platinum'			=> 0,
			'is_elite'				=> 0,
			'is_trial_inactive'		=> 0,
			'is_regular_inactive'	=> 0,
			'is_platinum_inactive'	=> 0,
			'is_elite_inactive'		=> 0,
			'is_inactive'			=> 0
		));
		
		return $guest;
	}
	
	/**
	 * refresh session date and date_last_seen
	 **/
	
	function auth_user_update_date($sess_id, $id_user)
	{
		global $dbconn;
		
		// update session table
		$ip_address = $_SERVER['REMOTE_ADDR'];
		
		$file = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : $_SERVER['SCRIPT_NAME'];
		
		# improvements by ralf:
		# - update guest record same as user record
		# - update ip address also for user record
		# - we will have multiple guest records now which can be used for monitoring the site
		# - timed out guest records will be automatically remove by sess_clear()
		
		$dbconn->Execute(
			'UPDATE '.ACTIVE_SESSIONS_TABLE.' SET update_date = NOW(), ip_address = ?, file = ? WHERE session = ? AND id_user = ?',
			array($ip_address, $file, $sess_id, $id_user));
		
		// update user record
		$dbconn->Execute('UPDATE '.USERS_TABLE.' SET date_last_seen = NOW() WHERE id = ?', array($id_user));
		
		// we do not update Solve360 as we don't want to bombard their web service
		
		return true;
	}
	
	/**
	 * read id_user from sessions table according to session id
	 */
	
	function sess_read($sess_id)
	{
		global $dbconn;
		
		$rs = $dbconn->Execute('SELECT id_user, type FROM '.ACTIVE_SESSIONS_TABLE.' WHERE session = ?', array($sess_id));
		
		$id_user	= !empty($rs->fields[0]) ? $rs->fields[0] : '';
		$type		= !empty($rs->fields[1]) ? 1 : 0;
		
		return array($id_user, $type);
	}
	
	/**
	 * check whether login as user or master login
	 **/
	
	function sess_type_read($sess_id)
	{
		global $dbconn;
		
		$session_type = $dbconn->getOne('SELECT type FROM '.ACTIVE_SESSIONS_TABLE.' WHERE session = ?', array($sess_id));
		
		return !empty($session_type) ? 1 : '';
	}
	
	/**
	 * write session record
	 **/
	
	function sess_write($sess_id, $id_user, $master=0)
	{
		global $dbconn, $config;
		
		$ip_address = $_SERVER['REMOTE_ADDR'];
		
		$file = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : $_SERVER['SCRIPT_NAME'];
		
		// delete all session records with the same session_id, thus also destroying the old guest session record
		// we keep multiple guest session records
		// we could also try to update an existing record
		//
		$dbconn->Execute('DELETE FROM '.ACTIVE_SESSIONS_TABLE.' WHERE session = ?', array($sess_id));
		
		// insert session record
		$dbconn->Execute(
			'INSERT INTO '.ACTIVE_SESSIONS_TABLE.' SET id_user = ?, session = ?, ip_address = ?, file = ?, update_date = NOW(), type = ?',
				array($id_user, $sess_id, $ip_address, $file, (string)$master));
		
		if (!$master) {
			// update date_last_seen
			$dbconn->Execute('UPDATE '.USERS_TABLE.' SET date_last_seen = NOW() WHERE id = ?', array($id_user));
			
			// UPDATE CONTACT IN SOLVE360
			if (SOLVE360_CONNECTION) {
				require_once $config['site_path'].'/include/Solve360Service.php';
				$solve360Service = new Solve360Service(SOLVE360_USER, SOLVE360_TOKEN);
				
				$solve360 = array();
				require $config['site_path'].'/include/Solve360CustomFields.php';
				
				$contactData = array(
					$solve360['Last Seen TLDF'] => date('Y-m-d H:i:s'),					// date/time
				);
				
				$rs = $dbconn->Execute('SELECT id_solve360, login FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
				$id_solve360 = $rs->fields[0];
				$login = $rs->fields[1];
				$rs->Free();
				
				if (!empty($id_solve360)) {
					$contact = $solve360Service->editContact($id_solve360, $contactData);
					#var_dump($contact); exit;
					if (isset($contact->errors)) {
						$subject = 'Error while updating Last Seen TLDF after login';
						solve360_api_error($contact, $subject, $login);
					}
				}
				// maybe add contact if not found
			}
		}
		
		return true;
	}
	
	/**
	 * delete specified session record
	 * used in /admin/index.php when admin signs out or switches to user mode
	 **/
	
	function sess_delete($sess_id)
	{
		global $dbconn;
		
		$dbconn->Execute('DELETE FROM '.ACTIVE_SESSIONS_TABLE.' WHERE session = ?', array($sess_id));
		
		return true;
	}
	
	/**
	 * delete timed out session records
	 **/
	
	function sess_clear($lifetime)
	{
		global $dbconn;
		$dbconn->Execute('DELETE FROM '.ACTIVE_SESSIONS_TABLE.' WHERE UNIX_TIMESTAMP(update_date) < UNIX_TIMESTAMP(NOW()) - '.$lifetime);
		return true;
	}
	
	/**
	 * check for logged in admin and display permission error if admin is not signed in
	 **/
	
	function login_check($auth)
	{
		if (empty($auth) || $auth == 'err' || empty($auth[ AUTH_ID_USER ]) || ! $auth[ AUTH_ROOT ])
		{
			if (isset($_REQUEST['ajax']))
			{
				echo '&nbsp;<script type="text/javascript">alert("your session has been expired!");</script>';
				echo '&nbsp;<script type="text/javascript">location.href="index.php";</script>';
				exit;
			}
			else
			{
				PermissionError('Permission denied.');
			}
		}
		
		return;
	}
	
	/**
	 * remove chat invites
	 **/
	
	function RemoveChatInvites($id_user)
	{
		global $dbconn;
		$dbconn->Execute('DELETE FROM chat_request_log WHERE from_id = ? OR to_id = ?', array($id_user, $id_user));
	}
	
	/**
	 * send online notice to all connected users
	 **/
	
	function SetAlertsMessage($id_user)
	{
		global $dbconn;
		
		$rs = $dbconn->Execute(
			'SELECT a.id_user
			   FROM '.CONNECTIONS_TABLE.' a
		 INNER JOIN '.ACTIVE_SESSIONS_TABLE.' b ON b.id_user = a.id_user
			  WHERE a.id_friend = ? AND a.status = "1"
			UNION
			 SELECT a.id_friend
			   FROM '.CONNECTIONS_TABLE.' a
		 INNER JOIN '.ACTIVE_SESSIONS_TABLE.' b ON b.id_user = a.id_friend
			  WHERE a.id_user = ? AND a.status = "1"',
			  array($id_user, $id_user));
		
		while (!$rs->EOF) {
			$dbconn->Execute('INSERT INTO '.ONLINE_NOTICE_TABLE.' SET id_to = ?, id_from = ?, type = "1", readed = "0"', array($rs->fields[0], $id_user));
			$rs->MoveNext();
		}
		
		return;
	}
	
	/**
	 * update login count
	 **/
	
	function SetLoginStatistic($id_user)
	{
		global $dbconn, $config;
		
		// increase login count
		$dbconn->Execute('UPDATE '.USERS_TABLE.' SET login_count = login_count + 1 WHERE id = ?', array($id_user));
		
		// SOLVE360
		if (SOLVE360_CONNECTION) {
			require_once $config['site_path'].'/include/Solve360Service.php';
			$solve360Service = new Solve360Service(SOLVE360_USER, SOLVE360_TOKEN);
			
			$solve360 = array();
			require $config['site_path'].'/include/Solve360CustomFields.php';
			
			$rs = $dbconn->Execute('SELECT login_count, id_solve360, login FROM '.USERS_TABLE.' WHERE id = ?', array($id_user));
			$login_count	= (int) $rs->fields[0];
			$id_solve360	= $rs->fields[1];
			$login			= $rs->fields[2];
			$rs->Free();
			
			$contactData = array(
				$solve360['TLDF Login Count'] => $login_count,
			);
			
			if (!empty($id_solve360)) {
				$contact = $solve360Service->editContact($id_solve360, $contactData);
				#var_dump($contact); exit;
				if (isset($contact->errors)) {
					$subject = 'Error while updating TLDF Login Count';
					solve360_api_error($contact, $subject, $login);
				}
			}
			// maybe add contact 
		}
		
		
		// GA_TRACKING
		$_SESSION['ga_event_code'] = 'login';
		
		// log GA visitorId
		if (isset($_COOKIE['__utma'])) {
			$items = explode('.', $_COOKIE['__utma']);
			if (isset($items[1])) {
				$visitorId = $items[1];
				$rs = $dbconn->Execute('SELECT visitorId FROM '.GA_VISITORS_TABLE.' WHERE id_user = ? ORDER BY id DESC', array($id_user));
				if ($rs->EOF || $rs->fields[0] != $visitorId) {
					$dbconn->Execute('INSERT INTO '.GA_VISITORS_TABLE.' SET id_user = ?, visitorId = ?, visitorIdDateTime = NOW()', array($id_user, $visitorId));
				}
			}
		}
		
		return;
	}
	
	/**
	 * check for installation folder and displays error message if found
	 **/
	
	function CheckInstallFolder()
	{
		global $config, $smarty;
		
		$dh = opendir($config['site_path']);
		
		while (($entry = readdir($dh)) !== false)
		{
			if (is_dir($config['site_path'].'/'.$entry) && strstr($entry, 'install'))
			{
				$smarty->assign('folder', $entry);
				$smarty->display(TrimSlash($config['index_theme_path']).'/install_folder_error.tpl');
				exit;
			}
		}
		
		closedir($dh);
		
		return;
	}
	
	function AssignUserToSmarty($user)
	{
		global $smarty;
		
		$smarty->assign('auth', array(
			'id_user'				=> $user[ AUTH_ID_USER ],
			'fname'					=> $user[ AUTH_FNAME ],
			'sname'					=> $user[ AUTH_SNAME ],
			'guest'					=> $user[ AUTH_GUEST ],
			'root'					=> $user[ AUTH_ROOT ],
			'login'					=> $user[ AUTH_LOGIN ],
			'gender'				=> $user[ AUTH_GENDER ],
			'type'					=> $user[ AUTH_TYPE ],
			'status'				=> $user[ AUTH_STATUS ],
			'email'					=> $user[ AUTH_EMAIL ],
			'site_language'			=> $user[ AUTH_SITE_LANGUAGE ],
			'application_submitted'	=> $user[ AUTH_APPLICATION_SUBMITTED ],
			'master_login'			=> $user[ AUTH_MASTER_LOGIN ],
			'platinum_applied'		=> $user[ AUTH_PLATINUM_APPLIED ],
			'date_birthday'			=> $user[ AUTH_DATE_BIRTHDAY ],
			'id_country'			=> $user[ AUTH_ID_COUNTRY ],
			'city'					=> $user[ AUTH_CITY ],
			'phone'					=> $user[ AUTH_PHONE ],
			'zipcode'				=> $user[ AUTH_ZIPCODE ],
			'id_group'				=> $user[ AUTH_ID_GROUP ],
			'is_applicant'			=> $user[ AUTH_IS_APPLICANT ],
			'is_trial'				=> $user[ AUTH_IS_TRIAL ],
			'is_regular'			=> $user[ AUTH_IS_REGULAR ],
			'is_platinum'			=> $user[ AUTH_IS_PLATINUM ],
			'is_elite'				=> $user[ AUTH_IS_ELITE ],
			'is_trial_inactive'		=> $user[ AUTH_IS_TRIAL_INACTIVE ],
			'is_regular_inactive'	=> $user[ AUTH_IS_REGULAR_INACTIVE ],
			'is_platinum_inactive'	=> $user[ AUTH_IS_PLATINUM_INACTIVE ],
			'is_elite_inactive'		=> $user[ AUTH_IS_ELITE_INACTIVE ],
			'is_inactive'			=> $user[ AUTH_IS_INACTIVE ],
			'id_solve360'			=> $user[ AUTH_ID_SOLVE360 ]
		));
		
	}
	
	/**
	 * TLDF
	 * get permissions
	 **/
	
	function meetme_GetPermissions(&$user)
	{
		global $dbconn;
		
		$user[ AUTH_IS_APPLICANT ]			= 0;
		$user[ AUTH_IS_TRIAL ]				= 0;
		$user[ AUTH_IS_REGULAR ]			= 0;
		$user[ AUTH_IS_PLATINUM ]			= 0;
		$user[ AUTH_IS_ELITE ]				= 0;
		$user[ AUTH_IS_TRIAL_INACTIVE]		= 0;
		$user[ AUTH_IS_REGULAR_INACTIVE ]	= 0;
		$user[ AUTH_IS_PLATINUM_INACTIVE ]	= 0;
		$user[ AUTH_IS_ELITE_INACTIVE ]		= 0;
		
		$id_group = $user[ AUTH_ID_GROUP ];
		$status = $user[ AUTH_STATUS ];
		
		if ($id_group != MM_GUEST_GROUP_ID) {
			if ($id_group == MM_SIGNUP_GUY_ID || $id_group == MM_SIGNUP_LADY_ID) {
				$user[ AUTH_IS_APPLICANT ] = 1;
			} elseif ($status == 0) {
				// status == 0
				if ($id_group == MM_REGULAR_GUY_ID || $id_group == MM_REGULAR_LADY_ID || $id_group == MM_TRIAL_GUY_ID || $id_group == MM_TRIAL_LADY_ID) {
					$user[ AUTH_IS_APPLICANT ] = 1;
				}
			} else {
				// status == 1
				if ($id_group == MM_TRIAL_GUY_ID || $id_group == MM_TRIAL_LADY_ID) {
					$user[ AUTH_IS_TRIAL ] = 1;
				} elseif ($id_group == MM_REGULAR_GUY_ID || $id_group == MM_REGULAR_LADY_ID) {
					$user[ AUTH_IS_REGULAR ] = 1;
				} elseif ($id_group == MM_PLATINUM_GUY_ID || $id_group == MM_PLATINUM_LADY_ID) {
					$user[ AUTH_IS_PLATINUM ] = 1;
				} elseif ($id_group == MM_ELITE_GUY_ID || $id_group == MM_ELITE_LADY_ID) {
					$user[ AUTH_IS_ELITE ] = 1;
				} elseif ($id_group == MM_INACT_TRIAL_GUY_ID || $id_group == MM_INACT_TRIAL_LADY_ID) {
					$user[ AUTH_IS_TRIAL_INACTIVE ] = 1;
				} elseif ($id_group == MM_INACT_REGULAR_GUY_ID || $id_group == MM_INACT_REGULAR_LADY_ID) {
					$user[ AUTH_IS_REGULAR_INACTIVE ] = 1;
				} elseif ($id_group == MM_INACT_PLATINUM_GUY_ID || $id_group == MM_INACT_PLATINUM_LADY_ID) {
					$user[ AUTH_IS_PLATINUM_INACTIVE ] = 1;
				} elseif ($id_group == MM_INACT_ELITE_GUY_ID || $id_group == MM_INACT_ELITE_LADY_ID) {
					$user[ AUTH_IS_ELITE_INACTIVE ] = 1;
				}
			}
		}
		
		$user[ AUTH_IS_INACTIVE ] = (int) ($user[ AUTH_IS_REGULAR_INACTIVE ] || $user[ AUTH_IS_PLATINUM_INACTIVE ] || $user[ AUTH_IS_ELITE_INACTIVE ]);
		
		$rs = $dbconn->Execute(
			'SELECT DISTINCT m.name, m.id
			   FROM '.MODULES_TABLE.' m
		 INNER JOIN '.GROUP_MODULE_TABLE.' gm ON m.id = gm.id_module
			  WHERE gm.id_group = ?
		   ORDER BY m.id',
			  array($id_group));
		
		$permissions = array();
		
		while (!$rs->EOF) {
			$permissions[ $rs->fields[0] ] = (int) $rs->fields[ 1 ];
			$rs->MoveNext();
		}
		
		return $permissions;
	}
	
	/**
	 * TLDF
	 * check if user is applicant
	 **/
	
	function is_applicant($id_user)
	{
		global $dbconn;
		
		$strSQL =
			'SELECT 1
			   FROM '.USER_GROUP_TABLE.' ug
		 INNER JOIN '.USERS_TABLE.' u ON u.id = ug.id_user
			  WHERE ug.id_user = ? 
				AND (ug.id_group IN ('.MM_SIGNUP_GUY_ID.','.MM_SIGNUP_LADY_ID.') 
				      OR
					 ug.id_group IN ('.MM_REGULAR_GUY_ID.','.MM_REGULAR_LADY_ID.','.MM_TRIAL_GUY_ID.','.MM_TRIAL_LADY_ID.') AND u.status = "0")';
		
		$found = $dbconn->getOne($strSQL, array($id_user));
		
		return (!empty($found) ? 1 : 0);
	}
	
	/**
	 * TLDF
	 * check if user is trial
	 **/
	
	function is_trial($id_user)
	{
		global $dbconn;
		
		$strSQL =
			'SELECT 1
			   FROM '.USER_GROUP_TABLE.' ug
		 INNER JOIN '.USERS_TABLE.' u ON u.id = ug.id_user
			  WHERE ug.id_user = ? 
				AND ug.id_group IN ('.MM_TRIAL_GUY_ID.','.MM_TRIAL_LADY_ID.') AND u.status = "1"';
		
		$found = $dbconn->getOne($strSQL, array($id_user));
		
		return (!empty($found) ? 1 : 0);
	}
	
	/**
	 * TLDF
	 * check if user is regular
	 **/
	
	function is_regular($id_user)
	{
		global $dbconn;
		
		$strSQL =
			'SELECT 1
			   FROM '.USER_GROUP_TABLE.' ug
		 INNER JOIN '.USERS_TABLE.' u ON u.id = ug.id_user
			  WHERE ug.id_user = ? 
				AND ug.id_group IN ('.MM_REGULAR_GUY_ID.','.MM_REGULAR_LADY_ID.') AND u.status = "1"';
		
		$found = $dbconn->getOne($strSQL, array($id_user));
		
		return (!empty($found) ? 1 : 0);
	}
	
	/**
	 * TLDF
	 * check if user is platinum
	 **/
	
	function is_platinum($id_user)
	{
		global $dbconn;
		
		$strSQL =
			'SELECT 1
			   FROM '.USER_GROUP_TABLE.' ug
		 INNER JOIN '.USERS_TABLE.' u ON u.id = ug.id_user
			  WHERE ug.id_user = ?
				AND ug.id_group IN ('.MM_PLATINUM_GUY_ID.','.MM_PLATINUM_LADY_ID.') AND u.status = "1"';
		
		$found = $dbconn->getOne($strSQL, array($id_user));
		
		return (!empty($found) ? 1 : 0);
	}
	
	/**
	 * TLDF
	 * check if user is elite
	 **/
	
	function is_elite($id_user)
	{
		global $dbconn;
		
		$strSQL =
			'SELECT 1
			   FROM '.USER_GROUP_TABLE.' ug
		 INNER JOIN '.USERS_TABLE.' u ON u.id = ug.id_user
			  WHERE ug.id_user = ?
				AND ug.id_group IN ('.MM_ELITE_GUY_ID.','.MM_ELITE_LADY_ID.') AND u.status = "1"';
		
		$found = $dbconn->getOne($strSQL, array($id_user));
		
		return (!empty($found) ? 1 : 0);
	}
	
	/**
	 * TLDF
	 * check if user is trial on hold
	 **/
	
	function is_trial_inactive($id_user)
	{
		global $dbconn;
		
		$strSQL =
			'SELECT 1
			   FROM '.USER_GROUP_TABLE.' ug
		 INNER JOIN '.USERS_TABLE.' u ON u.id = ug.id_user
			  WHERE ug.id_user = ? 
				AND ug.id_group IN ('.MM_INACT_TRIAL_GUY_ID.','.MM_INACT_TRIAL_LADY_ID.') AND u.status = "1"';
		
		$found = $dbconn->getOne($strSQL, array($id_user));
		
		return (!empty($found) ? 1 : 0);
	}
	
	/**
	 * TLDF
	 * check if user is regular on hold
	 **/
	
	function is_regular_inactive($id_user)
	{
		global $dbconn;
		
		$strSQL =
			'SELECT 1
			   FROM '.USER_GROUP_TABLE.' ug
		 INNER JOIN '.USERS_TABLE.' u ON u.id = ug.id_user
			  WHERE ug.id_user = ?
				AND ug.id_group IN ('.MM_INACT_REGULAR_GUY_ID.','.MM_INACT_REGULAR_LADY_ID.') AND u.status = "1"';
		
		$found = $dbconn->getOne($strSQL, array($id_user));
		
		return (!empty($found) ? 1 : 0);
	}
	
	/**
	 * TLDF
	 * check if user is platinum on hold
	 **/
	
	function is_platinum_inactive($id_user)
	{
		global $dbconn;
		
		$strSQL =
			'SELECT 1
			   FROM '.USER_GROUP_TABLE.' ug
		 INNER JOIN '.USERS_TABLE.' u ON u.id = ug.id_user
			  WHERE ug.id_user = ?
				AND ug.id_group IN ('.MM_INACT_PLATINUM_GUY_ID.','.MM_INACT_PLATINUM_LADY_ID.') AND u.status = "1"';
		
		$found = $dbconn->getOne($strSQL, array($id_user));
		
		return (!empty($found) ? 1 : 0);
	}
	
	/**
	 * TLDF
	 * check if user is elite on hold
	 **/
	
	function is_elite_inactive($id_user)
	{
		global $dbconn;
		
		$strSQL =
			'SELECT 1
			   FROM '.USER_GROUP_TABLE.' ug
		 INNER JOIN '.USERS_TABLE.' u ON u.id = ug.id_user
			  WHERE ug.id_user = ?
				AND ug.id_group IN ('.MM_INACT_ELITE_GUY_ID.','.MM_INACT_ELITE_LADY_ID.') AND u.status = "1"';
		
		$found = $dbconn->getOne($strSQL, array($id_user));
		
		return (!empty($found) ? 1 : 0);
	}
	
	/**
	 * TLDF
	 * check if user is on hold
	 **/
	
	function is_freeze($id_user)
	{
		global $dbconn;
		
		$strSQL =
			'SELECT 1
			   FROM '.USER_GROUP_TABLE.' ug
		 INNER JOIN '.USERS_TABLE.' u ON u.id = ug.id_user
			  WHERE ug.id_user = ?
				AND ug.id_group IN ('.MM_INACT_REGULAR_GUY_ID.','.MM_INACT_REGULAR_LADY_ID.','.
					MM_INACT_PLATINUM_GUY_ID.','.MM_INACT_PLATINUM_LADY_ID.','.MM_INACT_ELITE_GUY_ID.') AND u.status = "1"';
		
		$found = $dbconn->getOne($strSQL, array($id_user));
		
		return (!empty($found) ? 1 : 0);
	}
}

//VM creating log file
function LogUserActivity($LogFile, $Activity)
{
	$UserIp  = $_SERVER['REMOTE_ADDR'];
	$TimeRef = date('d-m-Y H:i T');
	$Handle  = fopen($LogFile, 'a');
	
	$Data = $UserIp.'|'.$TimeRef.'|'.$Activity.'~';
	fwrite($Handle, $Data);
	fclose($Handle);
}

function ReadUserActivity($LogFile)
{
	GLOBAL $log;
	$LogFile = file_get_contents($LogFile);
	
	$ExplodedLogFile = explode("~", $LogFile);
	$ArrayNum = count($ExplodedLogFile);
	$i = 0;
	
	while ( $i <= $ArrayNum ) {
		$log[$i] = explode("|", $ExplodedLogFile[$i]);
		$i++;
	}
}

?>