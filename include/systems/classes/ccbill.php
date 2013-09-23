<?php

include_once PAYMENT_DIR."Payment_Data_Kipper.php";

/**
* ccbill payment system class
*
* @package DatingPro
* @subpackage Payment systems files
**/

class Payment_ccbill extends Payment_Data_Kipper {

    /**
    *	Array for setting Payment System parameters.
    *
    *       @access private
    *       @type array
    */
	var $_arrayMap = array(

	/*** MODIFIED CODE BEGIN ***/

	/* General Parameters: */

	"seller_id"		=> "clientAccnum",

	"seller_sub_id"	=> "clientSubacc",

	"form_name"		=> "formName",

	"language"		=> "language",

	"allowed_types"	=> "allowedTypes",

	"subscription_type_id" => "subscriptionTypeId",

	"order_id" => "productDesc",

	"amount"	=>	"initialPrice"

	/*** MODIFIED CODE END ***/

	);


	/**
	*	Standard constructor. Nothing should be changed here.
	*
	*	@param int $mode Mode of class funtioning
	*	@param bool $debug Debug mode
	*/
	function Payment_ccbill($mode, $debug) {
		$this->_debug = $debug;
		if ($this->_debug) trigger_error("It reports -> " . get_class($this). " ! Designer()." , E_USER_NOTICE);

		if ( ($mode == PAYMENT_ENGINE_SEND) || ($mode == PAYMENT_ENGINE_RECEIVE) ) {
			$this->_mode = $mode;
			if ($this->_debug) trigger_error("- The regime is established " . $this->_mode . " ." , E_USER_NOTICE);
		} else {
			if ($this->_debug) trigger_error("- THE INCORRECT regime is established " . $this->_mode . " !!" , E_USER_ERROR);
			exit();
		}
		$this->_initialization();
		$this->makeFields();
		if ($this->_debug) trigger_error("It worked out <- " . get_class($this). " ! Designer()." , E_USER_NOTICE);
	}

    /**
	*	Method of class parameters initialization. It is set while being created.
	*		It is divided into 3 parts: general, receive and send.
	*
	*	@access private
	*/
	function _initialization() {
		if ($this->_debug) trigger_error("It reports -> " . get_class($this). " ! _initialization()" , E_USER_NOTICE);

		/*** MODIFIED CODE BEGIN ***/

		$this->_url = "https://bill.ccbill.com/jpost/signup.cgi";
		$this->_method = "GET";
		if ($this->_mode == PAYMENT_ENGINE_SEND) {
			$this->makeRequired('seller_id', 'seller_sub_id', 'form_name', 'language', 'allowed_types', 'subscription_type_id', 'order_id');
		}
		if ($this->_mode == PAYMENT_ENGINE_RECEIVE) {

		}

		/*** MODIFIED CODE END ***/

		if ($this->_debug) trigger_error("Отработал <- " . get_class($this). " ! _initialization()." , E_USER_NOTICE);
	}

	/**
	*	Method used for sending data to the server of Payment System.
	*		According to the type of Payment System there is either redirect to the site of Payment System(POST),
	*		or the URL is put in the heading.
	*
	*	@access public
	*	@return mixed In case when all the parameters were ser correctly, nothing will be returned,
	*		there will be redirect, otherwise notification will be generated and false will be returned
	*/
	function doPayment() {
		if ($this->_debug) trigger_error("It reports -> " . get_class($this). " ! doPayment()" , E_USER_NOTICE);
		if ($this->_mode == PAYMENT_ENGINE_SEND) {
			if ($this->_verifyData()) {
				$this->formMessage();
				if ($this->_debug) trigger_error("It worked out <- " . get_class($this). " ! doPayment()." , E_USER_NOTICE);
				exit();
			} else {
				if ($this->_debug) trigger_error("- The parameters are assigned incorrectly.", E_USER_WARNING);
				if ($this->_debug) trigger_error("It worked out <- " . get_class($this). " ! doPayment()." , E_USER_NOTICE);
				return false;
			}
		} else {
			trigger_error("- In the regime PAYMENT_ENGINE_RECEIVE the method doPayment() it is not accessible.", E_USER_ERROR);
		}
		if ($this->_debug) trigger_error("It worked out <- " . get_class($this). " ! doPayment()." , E_USER_NOTICE);
		return false;
	}

	/**
	*	Checks the incoming data to be set correctly in accord with
	*		the parameters set.
	*
	*	@access public
	*	@param ... Parameters enumeration. Transfer by link.
	*	@return bool true- in case all the parameters are set correctly and the payment
	*		was successfully processed, false - in case parameters are set incorrectly or payment failed.
	*/
	function checkPayment() {
		if ($this->_debug) trigger_error("It reports -> " . get_class($this). " ! checkPayment()" , E_USER_NOTICE);
		$retVal = true;
		$arg_list = func_get_args();
		if ($this->_mode == PAYMENT_ENGINE_RECEIVE) {
			if (PAYMENT_IGNORE_TEST_PAYMENT) {
				// !!! On ccbill there is no test mode
			}
			if (PAYMENT_LOG_PAYER_INFO) {

				/*** MODIFIED CODE BEGIN ***/
				// !!! User data do not come on ccbill
				/*** MODIFIED CODE END ***/

			}
			if ($this->_verifyData()) {
				/*** MODIFIED CODE BEGIN ***/

				if (!$_REQUEST["productDesc"]) {
					if ($this->_debug) trigger_error("- Транзакция прошла НЕ успешно.", E_USER_NOTICE);
					$retVal = false;
				}

				/*** MODIFIED CODE END ***/

			} else {
				if ($this->_debug) trigger_error("- Not all required parameters were assigned.", E_USER_ERROR);
				$retVal = false;
			}
		} else {
			trigger_error("- In the regime PAYMENT_ENGINE_SEND the method verifyIncoming() it is not accessible.", E_USER_ERROR);
			$retVal = false;
		}
		if ($this->_debug) trigger_error("It worked out <- " . get_class($this). " ! doPayment()." , E_USER_NOTICE);
		return $retVal;
	}

}	// end class Payment_template
?>