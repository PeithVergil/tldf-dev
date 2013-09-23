<?php
include "nusoap/nusoap.php";

class Voip extends soapclient{
	
	/**
	 * object of database access
	 *
	 * @var object
	 */
	var $dbconn;
	
	/**
	 * array of config elements
	 *
	 * @var array
	 */
	var $config;
	
	/**
	 * The business account's administrator user name
	 *
	 * @var string
	 */
	var $admin;	
	
	/**
	 * The business account administrator password	
	 *
	 * @var string
	 */
	var $adminpassword;
	
	/**
	 * account id of admin account
	 *
	 * @var integer
	 */
	var $accountid;
	
	/**
	 * The limit to be set for the user 
	 * The currency of this limit will	be the same as 
	 * the currency	that was set for the business account
	 *
	 * @var float
	 */
	
	var $limit;		
	
	/**
	 * array of provided jajah services
	 *
	 * @var array
	 */
	var $services = array('BusinessRegistration1',
							'ChangeLimitToUser',
							'ResellerCall',
							'GetRate',
							'GetMemberBalance',
							'UpdateMember1');
	
	/**
	 * number of error
	 *
	 * @var int
	 */
	var $error_no = 0;
	
	/**
	 * error messages array
	 *
	 * @var array
	 */
	var $error_msg_arr = array(	1=>'wrong_service_name',
								2=>'xml_file_missing',
								3=>'xml_file_empty',
								4=>'empty_service_adminname',
								5=>'empty_service_adminpass',
								6=>'empty_xmls_path',
								7=>'zero_min_account_balance',
								8=>'empty_email',
								9=>'wrong_email_format',
								10=>'empty_phone',
								11=>'wrong_phone_format',
								12=>'zero_limit',
/*reserved for nusoap errors*/	13=>'',
								14=>'clt_id_account_miss',
								15=>'clt_zero_new_limit',
								16=>'empty_service_response',
								17=>'rc_empty_payingusername',
								18=>'rc_empty_payingpassword',
								19=>'from_to_numbers_same',
								20=>'cant_connect_to_service',
								21=>'tonumbers_not_string_or_array',
								22=>'dublicate_tonumbers',
								23=>'empty_accountid',
								24=>'gmb_empty_username',
								25=>'gmb_empty_password',
								26=>'um_empty_username',
								27=>'um_empty_password',
								28=>'um_nothing_to_update',
								29=>'clt_empty_membername');
	
	
	/**
	 * answer from soap. Details in api docs
	 *
	 * @var string
	 */
	var $service_error_code;
	
	/**
	 * array with errors of BusinessRegistration1 service
	 * @var array
	 */
	var $BusinessRegistration1_errors = array(  -1=>'User name is empty',
												-2=>'Email is empty',
												-3=>'All three source numbers are empty',
												-4=>'Registration process failed',
												-5=>'User exists',
												-6=>'User has not confirmed email address',
												-8=>'Unsecured connection',
												-9=>'Administrator username/password not valid',
												-10=>'Customer does not exist',
												-11=>'IP not authorized');
		
	/**
	 * array with errors of ChangeLimitToUser service
	 * @var array
	 */
	var $ChangeLimitToUser_errors = array(  -1=>'Admin username or password are empty',
											-2=>'User\'s email empty',
											-4=>'Internal error',
											-8=>'Unsecured connection',
											-9=>'Unable to log admin',
											-10=>'Unable to get Business Account',
											-11=>'IP Address not authenticated',
											-12=>'Unable to log user',
											-13=>'Account ID does not match the business account specified by adminUserName/adminPassword parameters.');
											
	/**
	 * array with errors of ResellerCall service
	 * @var array
	 */
	var $ResellerCall_errors = array(   -1=>'Unauthorized API user. apiUserName/apiPassword does not match an existing Jajah account, or the user is not registered for the Reseller API package.',
										-2=>'Paying user not found. payingUserName/payingPassword does not match a Jajah account.',
										-3=>'IP address not authorized',
										-4=>'FromNumber is empty',
										-5=>'ToNumbers is empty',
										-6=>'One or more of the destination numbers is not in the right format.',
										-7=>'Call could not be connected – internal error',
										-8=>'Connection not secure',
										-9=>'The user credit specified by payingUserName/payingPassword is too low. Call will not be connected.',
										-11=>'Source number and one of the destination numbers are identical',
										-12=>'Invalid destination number',
										-13=>'Invalid source number',
										-14=>'Number of concurrent calls limit has been reached. By default the number of concurrent calls that can be invoked by the reseller is 30.');
										
	var $GetRate_errors = array(-1=>'Member does not exists',
								-2=>'User name or password are empty',
								-3=>'Source number is empty',
								-4=>'Invalid destination number',
								-5=>'ToNumbers (destination) is empty',
								-6=>'Member credit is too low to initiate the service, the member should proceed add call credits',
								-7=>'Source and one of the destination numbers are identical',
								-8=>'Unsecured connection',
								-9=>'Invalid source number',
								-10=>'The FromNumber is not registered under this user',
								-11=>'Internal Error');

	var $GetMemberBalance_errors = array(	-1=>'User name and password not valid',
											-2=>'User name or password is empty',
											-4=>'Internal Error',
											-8=>'Unsecured connection');
											
											
	var $UpdateMember1_errors = array(	-1=>'User name and password not valid',
										-2=>'User name or password is empty',
										-4=>'Internal Error',
										-5=>'Update email failed',
										-6=>'Maximum number of phone number changes is reached Number change is not allowed Contact Jajah Support',
										-7=>'Update password failed',
										-8=>'Unsecured connection',
										-9=>'New Landline is not valid',
										-10=>'New Office is not valid',
										-11=>'New Mobile is not valid',
										-18=>'Password is not in the correct format. Must be between 5 and 20 characters.');
	/**
	 * path to xml directory
	 * with xml templates files for service
	 *
	 * @var 
	 */
	
	var $xmls_path;
	
	/**
	 * minimal account balance when user can call
	 *
	 * @var float
	 */
	var $min_account_balance;

	/**
	 * constructor
	 *
	 * @param object $dbconn
	 * @param array $config
	 * @return Voip
	 */
	
	/**
	 * email pattern for check
	 *
	 * @var string
	 */
	//var $email_pattern = '/^([\w]+)(([-\.][\w]+)?)*@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/';
	var $email_pattern = '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';
	
	/**
	 * phone pattern for check
	 *
	 * @var string
	 */
	var $phone_pattern = '/^\d{10,15}(x\d{1,5})?$/';
	
	/**
	 * service 
	 *
	 * @var string
	 */
	var $service;
	
	/**
	 * private mode for calling
	 * Unknown or SourceNumberIsVisible or 
	 * DestinationNumberIsVisible or BothNumbersAreVisible or 
	 * BothNumbersAreInvisible
	 *
	 * @var string
	 */
	var $private_mode = 'BothNumbersAreInvisible';
	
	/**
	 * A URL to which
	 * Jajah dispatches the
	 * call status
	 *
	 * @var string
	 */
	var $callbackstr = '/include/voip/call_status.php';
	
	/**
	 * (Output) A call
	 * identifier generated
	 * by Jajah. The value
	 * is valid only if the
	 * call request was
	 * successful.
	 * On input, the value
	 * can be null.
	 *
	 * @var string
	 */
	var $RC_call_id;
	
	/**
	 * The rate calculated,
	 * converted using the
	 * currency ID set for the
	 * user
	 *
	 * @var double
	 */
	var $GR_rate;
	
	/**
	 * Is the call free?
	 * The call can be free
	 * for the user according
	 * to Jajah free calls
	 * rules.
	 *
	 * @var boolean
	 */
	var $GR_isFree;
	
	/**
	 * The currency ID of the
	 * user
	 * The rate is converted
	 * to that currency ID
	 *
	 * @var integer
	 */
	
	var $GR_currencyId;

	/**
	 * array currencyId=>abbr from dating
	 *
	 * @var array
	 */
	var $GR_currencyCodes = array(	1=>'EUR',
									2=>'USD',
									3=>'JPY',
									4=>'GBP',
									5=>'Chinese Yuan',
									6=>'Indian Rupee',
									7=>'Mexican Peso',
									8=>'Brazilian Real',
									9=>'Philippine Peso',
									10=>'Israeli Shekel',
									11=>'CAD',
									12=>'AUD',
									13=>'Hong Kong Dollar');
	
	/**
	 * If the call is free,
	 * indicates the amount
	 * of the weekly free
	 * minutes left for the
	 * user
	 *
	 * @var string
	 */
	var $GR_freeMinutesLeft;

	/**
	 * user balance
	 *
	 * @var double
	 */
	var $GMB_balance;
	
	/**
	 * id currency of user balance
	 *
	 * @var unknown_type
	 */
	var $GMB_currencyId;
	/**
	 * The service on which
	 * the rate calculation is
	 * required.
	 * 
	 * Valid Values:
	 * 1 – regular call
	 * 3 – SMS
	 * 6 – Scheduled
	 * 7 – Conference
	 * 12 – Scheduled
	 * Conference
	 * 13 – scheduled SMS
	 *
	 * @var unknown_type
	 */
	var $service_type = 1;
	
	/**
	 * xml wich sent to server
	 *
	 * @var unknown_type
	 */
	var $xml;
	
	var $status_codes_arr = array(	1=>'Your phone answered',
									2=>'Destination phone answered',
									3=>'Your phone busy',
									4=>'Destination phone busy',
									5=>'Your phone did not answer',
									6=>'Destination phone did not answer',
									7=>'Your phone canceled',
									8=>'Congestion',
									9=>'Hung-up (call terminated successfully)');
									
	function Voip($dbconn, $config){
		
		$this->dbconn = $dbconn;
		$this->config = $config;
		
		$settings = $this->_getSiteSettings(array('voip_admin', 'voip_admin_pass', 'voip_accountid', 'voip_xmls_path', 'voip_min_account_balance'));
		$settings["voip_min_account_balance"] = floatval($settings["voip_min_account_balance"]);
		
		if ($settings["voip_admin"] == ''){
			$this->error_no = 4;
		}else
			$this->admin = $settings["voip_admin"];
		
		if ($settings["voip_admin_pass"] == ''){
			$this->error_no = 5;
		}else
			$this->adminpassword = $settings["voip_admin_pass"];
		
		$settings["voip_accountid"] = intval($settings["voip_accountid"]);
		if (!$settings["voip_accountid"]){
			$this->error_no = 23;
		}else 
			$this->accountid = $settings["voip_accountid"];
			
		if ($settings["voip_xmls_path"] == ''){
			$this->error_no = 6;
			return false;
		}else
			$this->xmls_path = $settings["voip_xmls_path"];
		
//		if ($settings["voip_min_account_balance"] == 0){
//			$this->error_no = 7;
//			return false;
//		}else
			$this->min_account_balance = $settings["voip_min_account_balance"];

	}
	
	/**
	 * prepare xml for regiration user on jajah
	 * get xml template fill it and return xml string
	 * @param string $admin
	 * @param string $adminpassword
	 * @param string $username
	 * @param string $email
	 * @param string $password
	 * @param string $mobile
	 * @param float $limit
	 * @param string $firstname
	 * @param string $lastname
	 * @param string $landline
	 * @param string $office
	 * @return string
	 */
	function BusinessRegistration1($username, $email, $password, $mobile, $default_limit='', $firstname='', $lastname='', $landline='', $office=''){
		
		$this->service = 'BusinessRegistration1';
		
		$this->_isEmailFormat($email);
		if ($this->error_no) return false;
		
		$this->_isVoipPhoneFormat($mobile);
		if ($this->error_no) return false;
		
		$default_limit = floatval($default_limit);
//		if ($default_limit == 0){
//			$this->error_no = 12;
//			return false;
//		}
		
		if (!$xml_tpl = $this->_getServiceXmlTemplate()) return false;
		
		$search_arr = array('[username]',
							'[email]',
							'[password]',
							'[mobile]',
							'[limit]',
							'[firstname]',
							'[lastname]',
							'[landline]',
							'[office]',
							'[admin]',
							'[adminpassword]');
							
		$replace_arr = array($username,
							$email,
							$password,
							$mobile,
							$default_limit,
							$firstname,
							$lastname,
							$landline,
							$office,
							$this->admin,
							$this->adminpassword);
							
		$this->xml = str_replace($search_arr, $replace_arr, $xml_tpl);
		
		$soap_service = 'https://secure.jajah.com/api/RegisterService.asmx';
		$soap_action = 'http://www.jajah.com/BusinessRegistration1';
		$this->_sendRequest($this->xml, $soap_service, $soap_action);
		if (!$this->_parseServiceAnswer()){
			return false;
		}
		return $this->service_error_code;
	}
	
	/**
	 * changes money limit for call 
	 * must called before phone call
	 *
	 * @param string $memberemail
	 * @param float $newlimit
	 * @return string
	 */
	
	function ChangeLimitToUser($member_name, $newlimit){
		
		$this->service = 'ChangeLimitToUser';
		
		$newlimit = floatval($newlimit);
		
		if ($this->accountid == ''){
			$this->error_no = 14;
			return false;
		}
		
		if ($member_name == ''){
			$this->error_no = 29;
			return false;
		}
		
		if ($newlimit == 0){
			$this->error_no = 15;
			return false;
		}
		
		if (!$xml_tpl = $this->_getServiceXmlTemplate()) return false;
		
		$search_arr = array('[adminusername]',
							'[adminpassword]',
							'[accountid]',
							'[membername]',
							'[newlimit]');
		$replace_arr = array($this->admin,
								$this->adminpassword,
								$this->accountid,
								$member_name,
								$newlimit);
		$this->xml = str_replace($search_arr,$replace_arr,$xml_tpl);
		
		$soap_service = 'https://secure.jajah.com/api/BusinessAccountService.asmx';
		$soap_action = 'http://www.jajah.com/ChangeLimitToUser';
		
		$this->_sendRequest($this->xml, $soap_service, $soap_action);
		if (!$this->_parseServiceAnswer()){
			return false;
		}
		return $this->service_error_code;
	}
	
	/**
	 * The Call API provides the following features:
	 * • Connecting two or more telephone numbers into a call.
	 * • Billing the call on a specified account.
	 * • Determining the callback URL for call status capturing.
	 * • Obtaining a Jajah Call ID for later queries on specific calls.
	 *
	 * @param string $payingusername
	 * @param string $payingpassword
	 * @param string $fromnumber
	 * @param string $tonumbers
	 * @param string $audiofile
	 * @param string $affiliateid
	 * @return string
	 */
	
	function ResellerCall($payingusername,$payingpassword,$fromnumber,$tonumbers,$audiofile='',$affiliateid=''){
		
		$this->service = 'ResellerCall';
		if ($payingusername == ''){
			$this->error_no = 17;
			return false;
		}
		if ($payingpassword == ''){
			$this->error_no = 18;
			return false;
		}
		
		$this->_checkFromToNumbers($fromnumber,$tonumbers);
		if ($this->error_no) return false;
		
		$tonumbers_str = $this->_getTonumbersXmlFormat($tonumbers);
		
		$callbackurl = $this->config["server"].$this->config["site_root"].$this->callbackstr;			
		
		if (!$xml_tpl = $this->_getServiceXmlTemplate()) return false;
		
		$search_arr = array('[apiusername]',
							'[apipassword]',
							'[payingusername]',
							'[payingpassword]',
							'[fromnumber]',
							'[tonumbers]',
							'[privatemode]',
							'[affiliateid]',
							'[callbackurl]',
							'[audiofile]');
		$replace_arr = array($this->admin,
								$this->adminpassword,
								$payingusername,
								$payingpassword,
								$fromnumber,
								$tonumbers_str,
								$this->private_mode,
								$affiliateid,
								$callbackurl);
		$this->xml = str_replace($search_arr,$replace_arr,$xml_tpl);
		
		$soap_service = 'https://secure.jajah.com/api/CallService.asmx';
		$soap_action = 'http://www.jajah.com/ResellerCall';
		
		$this->_sendRequest($this->xml, $soap_service, $soap_action);
		
		if (!$this->_parseServiceAnswer()){
			return false;
		}
		if (intval($this->service_error_code)>0)
			return $this->RC_call_id;
		else 
			return $this->service_error_code;
	}
	
	function GetRate($username,$password,$source,$destinations){
		
		$this->service = 'GetRate';
		
		if (!$this->_checkFromToNumbers($source,$destinations)) return false;
		
		$tonumbers_str = $this->_getTonumbersXmlFormat($destinations);
		
		if (!$xml_tpl = $this->_getServiceXmlTemplate()) return false;
		
		$search_arr = array('[username]',
							'[password]',
							'[source]',
							'[destinations]',
							'[servicetype]');
		$replace_arr = array($username,
								$password,
								$source,
								$tonumbers_str,
								$this->service_type);
		$this->xml = str_replace($search_arr,$replace_arr,$xml_tpl);
		
		$soap_service = 'https://secure.jajah.com/api/MemberServices.asmx';
		$soap_action = 'http://www.jajah.com/GetRate';
		
		$this->_sendRequest($this->xml, $soap_service, $soap_action);
		if (!$this->_parseServiceAnswer()){
			return false;
		}
		if (intval($this->service_error_code) > 0)
			return array(	"rate"=>$this->GR_rate, 
							"isFree"=>$this->GR_isFree,
							"currencyId"=>$this->GR_currencyId,
							"freeMinutesLeft"=>$this->GR_freeMinutesLeft,
							"currencyName"=>$this->_getCurrencyName($this->GR_currencyId));
		else 
			return $this->service_error_code;
	}
	
	function GetMemberBalance($username,$password){
		
		$this->service = 'GetMemberBalance';
		
		if ($username == ''){
			$this->error_no = 24;
			return false;
		}
		if ($password == ''){
			$this->error_no = 25;
			return false;
		}
		
		if (!$xml_tpl = $this->_getServiceXmlTemplate()) return false;
		
		$search_arr = array('[username]',
							'[password]');
		$replace_arr = array($username,
								$password);
		$this->xml = str_replace($search_arr,$replace_arr,$xml_tpl);
		
		$soap_service = 'https://secure.jajah.com/api/MemberServices.asmx';
		$soap_action = 'http://www.jajah.com/GetMemberBalance';
		
		$this->_sendRequest($this->xml, $soap_service, $soap_action);
		if (!$this->_parseServiceAnswer()){
			return false;
		}
		if (intval($this->service_error_code) > 0)
			return array(	"balance"=>$this->GMB_balance, 
							"currencyId"=>$this->GMB_currencyId);
		else 
			return $this->service_error_code;
	}
	
	function UpdateMember1($username, $password, $newmobilenumber='', $newemail='', $newfirstname='', $newlastname='', $newpassword='', $newlandline='', $newofficenumber=''){
		
		$this->service = 'UpdateMember1';
		
		if ($newmobilenumber=='' && $newemail=='' && $newfirstname=='' && $newlastname=='' && $newpassword=='' && $newlandline=='' && $newofficenumber==''){
			$this->error_no = 28;
			return false;
		}
		
		if ($username == ''){
			$this->error_no = 26;
			return false;
		}
		if ($password == ''){
			$this->error_no = 27;
			return false;
		}
		
		$updatemobilenumber = $updateemail = $updatefirstname = $updatelastname = $updatepassword = $updatelandline = $updateofficenumber = 'false';
		
		if ($newmobilenumber != ''){
			if (!$this->_isVoipPhoneFormat($newmobilenumber))
				return false;
			else 
				$updatemobilenumber = 'true';
		}
		
		if ($newemail != ''){
			if (!$this->_isEmailFormat($newemail))
				return false;
			else 
				$updateemail = 'true';
		}
		
		if ($newfirstname != ''){
			$updatefirstname = 'true';
		}
		
		if ($newlastname != ''){
			$updatelastname = 'true';
		}
		
		if ($newpassword != ''){
			$updatepassword = 'true';
		}
		
		if ($newlandline != ''){
			if (!$this->_isVoipPhoneFormat($newlandline))
				return false;
			else 
				$updatelandline = 'true';
		}
		
		if ($newofficenumber != ''){
			if (!$this->_isVoipPhoneFormat($newmobilenumber))
				return false;
			else 
				$updateofficenumber = 'true';
		}
		
		if (!$xml_tpl = $this->_getServiceXmlTemplate()) return false;
		
		$search_arr = array('[username]',
							'[password]',
							'[updateemail]',
							'[newemail]',
							'[updatefirstname]',
							'[newfirstname]',
							'[updatelastname]',
							'[newlastname]',
							'[updatepassword]',
							'[newpassword]',
							'[updatelandline]',
							'[newlandline]',
							'[updateofficenumber]',
							'[newofficenumber]',
							'[updatemobilenumber]',
							'[newmobilenumber]');
		$replace_arr = array(	$username,
								$password,
								$updateemail,
								$newemail,
								$updatefirstname,
								$newfirstname,
								$updatelastname,
								$newlastname,
								$updatepassword,
								$newpassword,
								$updatelandline,
								$newlandline,
								$updateofficenumber,
								$newofficenumber,
								$updatemobilenumber,
								$newmobilenumber);
		$this->xml = str_replace($search_arr,$replace_arr,$xml_tpl);
		
		$soap_service = 'https://secure.jajah.com/api/MemberServices.asmx';
		$soap_action = 'http://www.jajah.com/UpdateMember1';
		
		$this->_sendRequest($this->xml, $soap_service, $soap_action);
		if (!$this->_parseServiceAnswer()){
			return false;
		}
		if (intval($this->service_error_code) > 0)
			return true;
		else 
			return $this->service_error_code;
	}

	function GetStatusMess($status_code){
		return $this->status_codes_arr[$status_code];
	}
	
	/**
	 * return error number
	 *
	 * @return int
	 */
	function errorNo(){
		return $this->error_no;
	}
	
	/**
	 * return error message by number error or false if nuber == 0
	 *
	 * @param int $no
	 * @return string
	 */
	function errorMsg($no=''){
		if (intval($no) > 0) $this->error_no = $no;
		if ($this->error_no > 0)
			return $this->error_msg_arr[$this->error_no];
		else 
			return false;
	}
	
	function serviceErrorNo(){
		return $this->service_error_code;
	}
	

	function serviceErrorMsg($no=0){
		if (!$no) $no = $this->service_error_code;
		$error_arr='';
		$$error_arr = $this->service.'_errors';
		$tmp = $this->$$error_arr;
		return $tmp[$no];
	}
	
	/**
	 * internal class function gets site esttings from database
	 *
	 * @param array $set_arr
	 * @return unknown
	 */
	function _getSiteSettings($set_arr=""){
		// array
		if($set_arr != ""  &&  is_array($set_arr) && count($set_arr)>0 ){
			foreach($set_arr as $key => $set_name){
				$set_arr[$key] = "'".$set_name."'";
			}
			$sett_string = implode(", ", $set_arr);
			$str_sql = "Select value, name from ".SETTINGS_TABLE." where name in (".$sett_string.")";
			$rs = $this->dbconn->Execute($str_sql);
			while(!$rs->EOF){
				$row = $rs->GetRowAssoc(false);
				$settings[$row["name"]] = $row["value"];
				$rs->MoveNext();
			}
		}elseif(strlen($set_arr)>0){
			$str_sql = "Select value, name from ".SETTINGS_TABLE." where name = '".strval($set_arr)."'";
			$rs = $this->dbconn->Execute($str_sql);
			$row = $rs->GetRowAssoc(false);
			$settings = $row["value"];
		}elseif(strval($set_arr)==""){
			$str_sql = "Select value, name from ".SETTINGS_TABLE." order by id";
			$rs = $this->dbconn->Execute($str_sql);
			while(!$rs->EOF){
				$row = $rs->GetRowAssoc(false);
				$settings[$row["name"]] = $row["value"];
				$rs->MoveNext();
			}
		}
		return $settings;
	}
	
	/**
	 * read xml template from file
	 *
	 * @param string $service
	 * @return string
	 */
	function _getServiceXmlTemplate(){
		if(!in_array($this->service,$this->services)){
			$this->error_no = 1;
			return false;
		}
		if (!file_exists($this->config["site_path"].$this->xmls_path."/".$this->service.".xml")){
			$this->error_no = 2;
			return false;
		}
		$xml_tpl = file_get_contents($this->config["site_path"].$this->xmls_path."/".$this->service.".xml");
		if ($xml_tpl == ''){
			$this->error_no = 3;
			return false;
		}
		return $xml_tpl;
	}
	
	/**
	 * check string by given pattern
	 *
	 * @param string $email
	 * @return boolean
	 */
	
	function _isEmailFormat($email){
		if ($email == ''){
			$this->error_no = 8;
			return false;
		}
		if (!preg_match($this->email_pattern, $email)){
			$this->error_no = 9;
			return false;
		}
		return true;
	}
	
	/**
	 * check string by given pattern
	 *
	 * @param string $phone
	 * @return boolean
	 */
	
	function _isVoipPhoneFormat($phone){
		if ($phone == ''){
			$this->error_no = 10;
			return false;
		}
		if (!preg_match($this->phone_pattern, $phone)){
			$this->error_no = 11;
			return false;
		}
		return true;
	}
	
	function _checkToNumbers($tonumbers){
		if (!is_string($tonumbers) && !is_array($tonumbers)){
			$this->error_no = 21;
			return false;	
		}
		if (is_string($tonumbers)){
			if ($this->_isVoipPhoneFormat($tonumbers)) return true;
			else return false;
		}
		if (is_array($tonumbers)){
			if ($tonumbers != array_unique($tonumbers)){
				$this->error_no = 22;
				return false;
			}
			foreach ($tonumbers as $value){
				if (!$this->_isVoipPhoneFormat($value)) return false;
			}
		}
		return true;
	}
	
	function _checkFromToNumbers($fromnumber,$tonumbers){
		if(!$this->_isVoipPhoneFormat($fromnumber)) return false;
		if(!$this->_checkToNumbers($tonumbers)) return false;
		if(is_string($tonumbers) && $fromnumber == $tonumbers){
			$this->error_no = 19;
			return false;
		}
		
		if(is_array($tonumbers)){
			foreach ($tonumbers as $value){
				if ($fromnumber == $value){
					$this->error_no = 19;
					return false;
				}
			}
		}
		return true;
	}
	
	function _getTonumbersXmlFormat($tonumbers){
		$str = '';
		if (is_array($tonumbers)){
			foreach ($tonumbers as $value){
				$str .= "<string>".$value."</string>";
			}
		}
		if (is_string($tonumbers)){
			$str = "<string>".$tonumbers."</string>";
		}
		return $str;
	}
	
	/**
	 * sends given xml by SOAP
	 *
	 * @param string $xml
	 */
	
	function _sendRequest($xml, $soap_service, $soap_action){
		$this->endpoint = $soap_service;
		$this->debugLevel = 0;
		$this->send($this->serializeEnvelope($xml),$soap_action);
		if ($this->responseData == ''){
			$this->error_no = 16;
			return false;
		}
		if ($this->fault){
			$this->error_no = 13;
			$this->error_msg_arr[13] = $this->error_str;
			return false;
		}
		return true;
	}
	
	function _parseServiceAnswer(){
		if ($this->faultstring != ''){
			$this->error_no = 13;
			$this->error_msg_arr[13] = $this->faultstring;
			return false;
		}
		//error code
		$mathes = array();
		preg_match('/(?U)<'.$this->service.'Result>(.*)<\/'.$this->service.'Result>/',$this->responseData, $mathes);
		$this->service_error_code = $mathes[1];
		if ($this->service_error_code == ''){
			$this->error_no = 20;
			return false;
		}
		if (intval($this->service_error_code) > 0){
			switch ($this->service){
				case 'ResellerCall':
					//callid from jajah
					$mathes = array();
					preg_match('/(?U)<CallId>(.*)<\/CallId>/',$this->responseData, $mathes);
					$this->RC_call_id = $mathes[1];
					return true;
				break;
				case 'GetRate':
					//getrate answer from jajah
					$mathes = array();
					preg_match('/(?U)<rate>(.*)<\/rate>/',$this->responseData, $mathes);
					$this->GR_rate = $mathes[1];
					
					$mathes = array();
					preg_match('/(?U)<isFree>(.*)<\/isFree>/',$this->responseData, $mathes);
					$this->GR_isFree = $mathes[1];
					
					$mathes = array();
					preg_match('/(?U)<currencyId>(.*)<\/currencyId>/',$this->responseData, $mathes);
					$this->GR_currencyId = $mathes[1];
					
					$mathes = array();
					preg_match('/(?U)<freeMinutesLeft>(.*)<\/freeMinutesLeft>/',$this->responseData, $mathes);
					$this->GR_freeMinutesLeft = $mathes[1];
					return true;
				break;
				case 'GetMemberBalance':
					$mathes = array();
					preg_match('/(?U)<balance>(.*)<\/balance>/',$this->responseData, $mathes);
					$this->GMB_balance = $mathes[1];
					
					$mathes = array();
					preg_match('/(?U)<currencyId>(.*)<\/currencyId>/',$this->responseData, $mathes);
					$this->GMB_currencyId = $mathes[1];
					return true;
				break;
				default: return true;
			}
		}else return true;
	}
	
	function _getCurrencyName($curr_id){
		return $this->GR_currencyCodes[$curr_id];
	}
	
}

class DatingVoIp extends Voip {
	
	/**
	 * datingpro user id
	 *
	 * @var integer
	 */
	var $id_user;
	
	/**
	 * first name of dating user;
	 *
	 * @var string
	 */
	var $first_name;
	
	/**
	 * last name of dating user
	 *
	 * @var string
	 */
	var $last_name;
	
	/**
	 * email of dating user
	 *
	 * @var string
	 */
	var $email;
	
	/**
	 * phone of dating user
	 *
	 * @var string
	 */
	var $phone;
	
	/**
	 * money of user
	 *
	 * @var float
	 */
	var $account;
	
	
	/**
	 * 	The user name to be registered
	 * with Jajah
	 *
	 * @var string
	 */
	var $voip_login;
	
	/**
	 * email of jajah user
	 *
	 * @var string
	 */
	var $voip_email;
	
	/**	 
	 * The user's password
	 * The Password length should be
	 * between 5 to 20 characters
	 *
	 * @var string
	 */
	var $voip_password;
	
	/**
	 * number of error
	 *
	 * @var integer
	 */
	var $dv_error_no;
	
	/**
	 * error msg
	 *
	 * @var string
	 */
	var $dv_error_msg;
	
	/**
	 * error array
	 *
	 * @var array
	 */
	var $dv_error_arr = array(  1=>'user_not_exist_or_guest',
								2=>'user_already_exist',
								3=>'source_phone_missing',
								4=>'zero_account',
								5=>'empty_adminname',
								6=>'empty_adminpassword',
								7=>'wrong_percent',
								8=>'cant_find_callinfo_by_callid',
								9=>'IS_iduser_less_than_1',
								10=>'IS_empty_callid',
								11=>'IS_empty_callstatus',
								12=>'CR_emty_touser',
								13=>'GDSFU_empty_iduser',
								14=>'wrong_rate',
								15=>'wrong_accunt_id',
								16=>'zero_amount',
								17=>'wrong_amount',
								18=>'empty_user_id');
	
	/**
	 * income percent for admin
	 *
	 * @var integer
	 */
	var $admin_percent;
	
	/**
	 * id user whom call
	 *
	 * @var integer
	 */
	var $to_id_user;
	
	/**
	 * array with info about call
	 * id_user, to_id_user, to_number, percent
	 * 
	 *
	 * @var array
	 */
	var $call_info;
	
	/**
	 * currency on site
	 *
	 * @var string
	 */
	
	var $site_currency;
	
	/**
	 * rate for currency. if 
	 * currency on site differ ftom 
	 * jajh account
	 *
	 * @var unknown_type
	 */
	var $currency_rate;
	
	function DatingVoIp($dbconn,$config,$id_user){
		
		$this->Voip($dbconn,$config);
		
		$settings = $this->_getSiteSettings(array('voip_admin_percent','site_unit_costunit', 'voip_currency_rate'));
		$this->admin_percent = $settings["voip_admin_percent"];
		$this->site_currency = $settings["site_unit_costunit"];
		$this->currency_rate = $settings["voip_currency_rate"];
		
		if ($this->error_no){
			return $this->errorMsg();
		}
		$id_user = intval($id_user);
		if ($id_user > 0){
			$strSQL = "SELECT ut.id, ut.fname, ut.sname, ut.email, ut.phone, bua.account_curr, uv.voip_login, uv.voip_email, uv.voip_password
						FROM ".USERS_TABLE." ut
						LEFT JOIN ".BILLING_USER_ACCOUNT_TABLE." bua on ut.id=bua.id_user
						LEFT JOIN ".USER_VOIP_TABLE." uv on ut.id=uv.id_user
						WHERE ut.id='".$id_user."' and ut.guest_user='0'";
			$rs = $this->dbconn->Execute($strSQL);
			$row = $rs->GetRowAssoc(false);
			if (!$row){
				$this->dv_error_no = 1;
				return false;
			}
			$this->id_user = $row["id"];
			$this->first_name = $row["fname"];
			$this->last_name = $row["sname"];
			$this->email = $row["email"];
			$this->voip_email = $row["voip_email"];
			$this->phone = $row["phone"];
			$this->account = floatval($row["account_curr"]);
			$this->voip_login = $row["voip_login"];
			$this->voip_password = $row["voip_password"];
		}
		
		
	
	}

	function CallRequest($touser){
		if (!$this->_registerIfNotExist()) return false;
		
		if (intval($touser) < 1){
			$this->dv_error_no = 12;
			return false;
		}
		$this->to_id_user = $touser;
		
		$tonumber = $this->GetPhoneById($this->to_id_user);
		
		if (!$this->_isVoipPhoneFormat($tonumber)) return false;
		
		$res = $this->ResellerCall($this->voip_login,$this->voip_password, $this->phone, $tonumber);
		
		if (intval($this->RC_call_id) == 0 || $res == false){
			return $this->_getReturn($res);
		}
		
		$strSQL = "UPDATE ".USER_VOIP_TABLE." SET call_id='".$this->RC_call_id."', percent='".$this->admin_percent."', from_number='".$this->phone."',  to_id_user='".$touser."', to_number='".$tonumber."'  WHERE id_user='".$this->id_user."'";
		$this->dbconn->Execute($strSQL);
		
		return true;
	}
	
	function GetRateForUser($touser){
		
		$tonumber = $this->GetPhoneById($touser);
	
		if (!$this->_registerIfNotExist()) return false;
		
		$res = $this->GetRate($this->voip_login,$this->voip_password,$this->phone,$tonumber);
		if (!is_array($res)){
			return $this->_getReturn($res);
		}else{
			if (!$this->IsCurrenciesSame($res["currencyName"])){
				$res["currencyName"] = $this->site_currency;
				$res["rate"] = $res["rate"]/$this->currency_rate;
			}
			$res["rate"] = round($res["rate"]+$res["rate"]*$this->admin_percent/100,2);
			return $res;
		}
	}
	
	function GetMemberBalanceForUser($is_admin=false,$round=false){
		if ($is_admin)
			$res = $this->GetMemberBalance($this->admin,$this->adminpassword);
		else{
			if (!$this->_registerIfNotExist()) return false;
			$res = $this->GetMemberBalance($this->voip_login,$this->voip_password);
		}
		if (!is_array($res)){
			return $this->_getReturn($res);
		}else{
			$res["jajah_currency_name"] = $res["currency_name"] = $this->_getCurrencyName($res["currencyId"]);
			
			if (!$this->IsCurrenciesSame($res["currency_name"])){
				$res["currency_name"] = $this->site_currency;
				$res["balance"] = $res["balance"]/$this->currency_rate;
			}
			if ($round){
				$res["balance"] = round($res["balance"],$round);
			}
			return $res;
		}
			
	}
	
	function GetAdminSettings(){
		return array("voip_admin"=>$this->admin, "voip_admin_pass"=>$this->adminpassword, "voip_accountid"=>$this->accountid, "voip_admin_percent"=>$this->admin_percent, "voip_currency_rate"=>$this->currency_rate, "site_currency"=>$this->site_currency);
	}
	
	function SetAdminSettings($admin, $password, $account_id, $percent, $curr_rate){
		if ($admin == ''){
			$this->dv_error_no = 5;
			return false;
		}
		$admin = addslashes($admin);
		if ($password == ''){
			$this->dv_error_no = 6;
			return false;
		}
		$password = addslashes($password);
		
		$account_id = intval($account_id);
		if ($account_id == 0){
			$this->dv_error_no = 15;
			return false;
		}
		
		$percent = intval($percent);
		if ($percent < 1){
			$this->dv_error_no = 7;
			return false;
		}
		
		$curr_rate = round(floatval($curr_rate),3);
		if ($curr_rate > 0){
			$change_rate = true;
		}else {
			$curr_rate = 1;
		}
			
		$res = $this->GetMemberBalance($admin,$password);
		if (!is_array($res)){
			return false;
		}
		
		$this->admin = $admin;
		$this->adminpassword = $password;
		$this->accountid = $account_id;
		$this->admin_percent = $percent;
		
		
		if (in_array($this->error_no,array(4,5,23))) $this->error_no=0;
		
		if ($this->IsCurrenciesSame($res["currency_name"])){
			$curr_rate = 1;
		}
		$this->currency_rate = $curr_rate;
		
		$strSQL = "UPDATE ".SETTINGS_TABLE." SET value = '".$admin."' WHERE name='voip_admin'";
		$this->dbconn->Execute($strSQL);
		$strSQL = "UPDATE ".SETTINGS_TABLE." SET value = '".$password."' WHERE name='voip_admin_pass'";
		$this->dbconn->Execute($strSQL);
		$strSQL = "UPDATE ".SETTINGS_TABLE." SET value = '".$account_id."' WHERE name='voip_accountid'";
		$this->dbconn->Execute($strSQL);
		$strSQL = "UPDATE ".SETTINGS_TABLE." SET value = '".$percent."' WHERE name='voip_admin_percent'";
		$this->dbconn->Execute($strSQL);
		$strSQL = "UPDATE ".SETTINGS_TABLE." SET value = '".$curr_rate."' WHERE name='voip_currency_rate'";
		$this->dbconn->Execute($strSQL);
		return true;
	}
	
	function GetErrorMsg(){
		if ($this->serviceErrorMsg() || $this->errorMsg() || $this->dvErrorMsg()){
			global $lang;
			$err = $this->serviceErrorMsg();
			if ($this->error_no == 13)
				$err .=" ".$this->errorMsg();
			else 
				$err .=" ".$lang["voip"]["err"][$this->errorMsg()];
			$err .=" ".$lang["voip"]["err"][$this->dvErrorMsg()];
			return $err;
		}else 
			return false;
	}
	
	function GetCallInfoByCallId($callId){
		$strSQL = "SELECT id_user, to_id_user, from_number, to_number, percent FROM ".USER_VOIP_TABLE." WHERE call_id='".addslashes($callId)."'";
		$rs = $this->dbconn->Execute($strSQL);
		$row = $rs->GetRowAssoc(false);
		if (!$row){
			$this->dv_error_no = 8;
			return false;
		}else{
			$this->call_info = $row;
			return $row;
		}
	}
	
	function InsertStatistic($call_id, $call_status, $duration, $cost, $curr_id){
		
		if ($this->id_user < 1){
			$this->dv_error_no = 9;
			return false;
		}
		
		if ($call_id == ''){
			$this->dv_error_no = 10;
			return false;
		}
			
		if ($call_status == ''){
			$this->dv_error_no = 11;
			return false;
		}
		
		$call_id = addslashes($call_id);
		$call_status = addslashes($call_status);
		$duration = addslashes($duration);
		$cost = addslashes($cost);
		$curr_id = addslashes($curr_id);
		
		$strSQL = "INSERT INTO ".VOIP_STAT_TABLE." 
					(id_user, call_id, call_status, duration, cost, curr_id, curr_name, percent, from_number, to_number, to_id_user) 
					VALUES('".$this->id_user."', '".$call_id."', '".$call_status."', '".$duration."', '".$cost."', '".$curr_id."', '".$this->_getCurrencyName($curr_id)."', '".$this->call_info["percent"]."', '".$this->call_info["from_number"]."', '".$this->call_info["to_number"]."', '".$this->call_info["to_id_user"]."')";
		$this->dbconn->Execute($strSQL);
		return true;
	}
	
	function GetCommonStatisticByUser($id_user){
		$info["id_user"] = $id_user;
		
		$info["login"] = $this->GetLoginById($id_user);
		
		$strSQL = "SELECT SUM(duration) FROM ".VOIP_STAT_TABLE." WHERE id_user='".$id_user."'";
		$info["duration"] = $this->dbconn->GetOne($strSQL);
		
		$strSQL = "SELECT DISTINCT(curr_name) FROM ".VOIP_STAT_TABLE." WHERE id_user='".$id_user."' AND curr_name!=''";
		$rs = $this->dbconn->Execute($strSQL);
		while (!$rs->EOF) {
			$curr_array[] = $rs->fields[0];
			$rs->MoveNext();
		}
		$i=0;
		foreach ($curr_array as $curr_name){
			$cost[$i]["curr_name"] = $curr_name;
			$strSQL = "SELECT SUM(cost) FROM ".VOIP_STAT_TABLE." WHERE id_user='".$id_user."' and curr_name='".$curr_name."'";
			$cost[$i]["curr_value"] = round($this->dbconn->GetOne($strSQL),2);
			$i++;
		}
		$info["cost"] = $cost;
		
		$strSQL = "SELECT MAX(date) FROM ".VOIP_STAT_TABLE." WHERE id_user='".$id_user."'";
		$info["last_date"] = $this->dbconn->GetOne($strSQL);
		
		return $info;
	}
	
	function GetDetailedStatisticForUser($id_user){
		
		if (intval($id_user) < 1){
			$this->dv_error_no = 13;
			return false;
		}
		
		$strSQL = "SELECT DISTINCT(call_id) FROM ".VOIP_STAT_TABLE." WHERE id_user='".$id_user."' ORDER BY date DESC";
		$rs = $this->dbconn->Execute($strSQL);
		$i=0;
		while (!$rs->EOF){
			$info[$i]["call_id"] = $rs->fields[0];
			
			$strSQL = "SELECT SUM(duration), SUM(cost) FROM ".VOIP_STAT_TABLE." WHERE id_user='".$id_user."' AND call_id='".$info[$i]["call_id"]."' AND (duration != 0 OR cost != 0)";
			$rs1 = $this->dbconn->Execute($strSQL);
			$info[$i]["duration"] = $rs1->fields[0];
			$info[$i]["cost"] = $rs1->fields[1];
			
			$strSQL = "SELECT curr_name, date, percent, from_number, to_number, to_id_user FROM ".VOIP_STAT_TABLE." WHERE id_user='".$id_user."' AND call_id='".$info[$i]["call_id"]."' ORDER BY curr_name DESC";
			$rs2 = $this->dbconn->Execute($strSQL);
			$info[$i]["curr_name"] = $rs2->fields[0];
			$info[$i]["date"] = $rs2->fields[1];
			$info[$i]["percent"] = $rs2->fields[2];
			$info[$i]["cost_for_user"] = round($info[$i]["cost"]+$info[$i]["cost"]*$info[$i]["percent"]/100,2);
			$info[$i]["cost"] = round($info[$i]["cost"],2);
			$info[$i]["from_number"] = $rs2->fields[3];
			$info[$i]["to_number"] = $rs2->fields[4];
			$info[$i]["to_id_user"] = $rs2->fields[5];
			$info[$i]["to_user"] = $this->GetLoginById($info[$i]["to_id_user"]);
			$info[$i]["call_link"] = $this->config["server"].$this->config["site_root"]."/voip_call.php?sel=rate&id_user=".$info[$i]["to_id_user"];
			
			$rs->MoveNext();
			$i++;
		}
		return $info;
	}
	
	function GetAllVoipUsers($who_calls = false){
		if ($who_calls)
			$strSQL = "SELECT DISTINCT(id_user) FROM ".USER_VOIP_TABLE." WHERE call_id!=''";
		else 
			$strSQL = "SELECT DISTINCT(id_user) FROM ".USER_VOIP_TABLE;
		$rs = $this->dbconn->Execute($strSQL);
		while (!$rs->EOF) {
			$id_user_arr[] = $rs->fields[0];
			$rs->MoveNext();
		}
		return $id_user_arr;
	}
	
	function IsCurrenciesSame($jajah_curr){
		if (strtoupper($jajah_curr) == strtoupper($this->site_currency))
			return true;
		else 
			return false;
	}
	
	function GetLoginById($id){
		return $this->dbconn->GetOne("SELECT login FROM ".USERS_TABLE." WHERE id='".intval($id)."'");
	}
	
	function GetDatingUserBalance($id_user){
		$balance["balance"] = floatval($this->dbconn->GetOne("SELECT account_curr FROM ".BILLING_USER_ACCOUNT_TABLE." WHERE id_user='".intval($id_user)."'"));
		$balance["currency_name"] = $this->site_currency;
		return $balance;
	}
	
	function AddCallCreditForUser($amount){
		
		if (!$this->_registerIfNotExist()) return false;
		
		$amount = floatval($amount);
		if ($amount <= 0){
			$this->dv_error_no = 16;
			return false;
		}
		$old_limit_arr = $this->GetMemberBalance($this->voip_login,$this->voip_password);
		if (!is_array($old_limit_arr)){
			return false;
		}
		
		$strSQL = "SELECT last_limit FROM ".USER_VOIP_TABLE." WHERE id_user='".$this->id_user."'";
		$last_limit = floatval($this->dbconn->GetOne($strSQL));

		$newlimit = $last_limit + $amount*$this->currency_rate;
		
		$res = $this->ChangeLimitToUser($this->voip_login,$newlimit);
		
		//while ($old_limit_arr === $this->GetMemberBalance($this->voip_login,$this->voip_password));
		
		if ($res > 0){
			$strSQL = "UPDATE ".USER_VOIP_TABLE." SET last_limit='".$newlimit."' WHERE id_user='".$this->id_user."'";
			$this->dbconn->Execute($strSQL);
			$this->SubtrakFromUserAccount($amount, 'add_call_credit');
		}
		
		return $this->_getReturn($res);
	}
	
	function UpdateDatingMemberPhone($new_mobile_number){
		$res = $this->UpdateMember1($this->voip_login, $this->voip_password, $new_mobile_number);
		return $this->_getReturn($res);
	}
	
	function SubtrakFromUserAccount($amount, $type){
		$amount = floatval($amount);
		$type = strip_tags(addslashes($type));
		if ($amount <= 0){
			$this->dv_error_no = 17;
			return false;
		}
		
		$strSQL = "UPDATE ".BILLING_USER_ACCOUNT_TABLE." SET account_curr=account_curr-".$amount." WHERE id_user='".$this->id_user."'";
		$this->dbconn->Execute($strSQL);
		
		$strSQL = "INSERT INTO ".BILLING_ENTRY_TABLE." (id_user, amount, currency, id_group, date_entry, entry_type) VALUES('".$this->id_user."','-".round($amount,2)."', '".$this->site_currency."', '-1', NOW(), '".$type."')";
		$this->dbconn->Execute($strSQL);
		return true;
	}
	
	function GetDatingUserInfo($id){
		$id = intval($id);
		if ($id < 1){
			$this->dv_error_no = 18;
			return false;
		}
		
		$settings = $this->_getSiteSettings(array("icon_male_default", "icon_female_default","icons_folder"));
		$default_photos['1'] = $settings['icon_male_default'];
		$default_photos['2'] = $settings['icon_female_default'];
		
		$strSQL = "SELECT login, date_birthday, icon_path, gender FROM ".USERS_TABLE." WHERE id='".$id."'";
		$rs = $this->dbconn->Execute($strSQL);
		$row = $rs->GetRowAssoc(false);
		
		$icon_path = $row["icon_path"]?$row["icon_path"]:$default_photos[$row["gender"]];
		if($icon_path && file_exists($this->config["site_path"].$settings["icons_folder"]."/".$icon_path))
			$row["icon_path"] = $this->config["site_root"].$settings["icons_folder"]."/".$icon_path;
		else 
			unset($row["icon_path"]);
			
		$row["age"] = $this->_ageFromBDate($row["date_birthday"]);
		
		$row["profile_link"] = "viewprofile.php?id=".$id;
		return $row;
	}
	
	function _getNewJajahName(){
		return substr(md5(rand(0,1000)),0,15);
	}
	
	function _getNewJajahPassword(){
		return substr(md5(rand(0,1000)),0,10);
	}
	
	function _registerNewUser(){
		
		if ($this->voip_login){
			$this->dv_error_no = 2;
			return false;
		}
		
		$this->voip_login = $this->_getNewJajahName();
		$this->voip_password = $this->_getNewJajahPassword();
		
		if ($this->phone == ''){
			$this->dv_error_no = 3;
			return false;
		}
		$this->email = substr(md5(rand(0,1000)),0,6).$this->email;//users must not know their jajah accout details
		$result = $this->BusinessRegistration1($this->voip_login, $this->email, $this->voip_password, $this->phone, $this->min_account_balance, $this->first_name, $this->last_name);
		
		if (intval($result) > 0){
			$strSQL = "INSERT INTO ".USER_VOIP_TABLE." (id_user, voip_login, voip_email, voip_password) 
						VALUES('".$this->id_user."', '".$this->voip_login."', '".$this->email."', '".$this->voip_password."')";
			$this->dbconn->Execute($strSQL);			
		}
		
		return $this->_getReturn($result);
	}
	
	function _getReturn($result){
		if ($result == false || intval($result) < 1){
			return false;
		}elseif (intval($result) >= 1){
			return true;
		}
	}
	
	function GetPhoneById($id){
		return $this->dbconn->GetOne("SELECT phone FROM ".USERS_TABLE." WHERE id='".intval($id)."'");
	}
	
	function _registerIfNotExist(){
		if (!$this->voip_login){
			$res = $this->_registerNewUser();
			if ($res != true){
				return $res;
			}
		}
		return true;
	}
	
	function GetLastStatusCodeByRcId($rc_id){
		$rc_id = intval($rc_id);
		$strSQL = "SELECT call_status FROM ".VOIP_STAT_TABLE." WHERE call_id='".$rc_id."' ORDER BY date DESC";
		return $this->dbconn->GetOne($strSQL);
	}
	
	function dvErrorMsg(){
		if ($this->dv_error_no == 0) return false;
		return $this->dv_error_arr[$this->dv_error_no]; 
	}
	
	function _ageFromBDate($date){
	///// date in Y-m-d h:i:s format
	$year = intval(substr($date,0,4));
	$month = intval(substr($date,5,2));
	$day = intval(substr($date,8,2));
	$n_year = date("Y");
	$n_month = date("m");
	$n_day = date("d");
	if ($month==$n_month) {
		if ($day>$n_day) {
			$new_age = floor(($n_year - $year)+($n_month - $month-1)/12);
		} else {
			$new_age = floor(($n_year - $year) + ($n_month - $month)/12);
		}
	} else {
		$new_age = floor(($n_year - $year) + ($n_month - $month)/12);
	}
	return $new_age;
}
}

?>