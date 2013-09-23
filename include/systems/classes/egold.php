<?php

include_once PAYMENT_DIR."Payment_Data_Kipper.php";

/**
* e-gold payment system class
*
* @package DatingPro
* @subpackage Payment systems files
**/

class Payment_egold extends Payment_Data_Kipper {

    /**
    *	Array for setting Payment System parameters.
    *
    *       @access private
    *       @type array
    */
	var $_arrayMap = array(

	/*** MODIFIED CODE BEGIN ***/

	/* General Parameters: */

	"seller_id"		=> "PAYEE_ACCOUNT",

	"amount"		=> "PAYMENT_AMOUNT",

	"order_id"		=> "PAYMENT_ID",

	"seller_name"	=> "PAYEE_NAME",

	"return_good_method"	=> "PAYMENT_URL_METHOD",

	"return_bad_method"		=> "NOPAYMENT_URL_METHOD",

	"return_good_url"	=> "PAYMENT_URL",

	"return_bad_url"	=> "NOPAYMENT_URL",

	"currency"		=> "PAYMENT_UNITS",

	"metal_id"		=> "PAYMENT_METAL_ID",

	"information"	=>"BAGGAGE_FIELDS",

	"secret_word"	=> ""

	/*** MODIFIED CODE END ***/

	);


    /**
	*	Standard constructor. Nothing should be changed here.
	*
	*	@param int $mode Mode of class funtioning
	*	@param bool $debug Debug mode
	*/
	function Payment_egold($mode, $debug) {
		$this->_debug = $debug;
		if ($this->_debug) trigger_error("Докладывает -> " . get_class($this). " ! Конструктор()." , E_USER_NOTICE);

		if ( ($mode == PAYMENT_ENGINE_SEND) || ($mode == PAYMENT_ENGINE_RECEIVE) ) {
			$this->_mode = $mode;
			if ($this->_debug) trigger_error("- Установлен режим " . $this->_mode . " ." , E_USER_NOTICE);
		} else {
			if ($this->_debug) trigger_error("- Установлен НЕВЕРНЫЙ режим " . $this->_mode . " !!" , E_USER_ERROR);
			exit();
		}
		$this->_initialization();
		$this->makeFields();
		if ($this->_debug) trigger_error("Отработал <- " . get_class($this). " ! Конструктор()." , E_USER_NOTICE);
	}

    /**
	*	Method of class parameters initialization. It is set while being created.
	*		It is divided into 3 parts: general, receive and send.
	*
	*	@access private
	*/
	function _initialization() {
		if ($this->_debug) trigger_error("Докладывает -> " . get_class($this). " ! _initialization()" , E_USER_NOTICE);

		/*** MODIFIED CODE BEGIN ***/

		$this->_url = "https://www.e-gold.com/sci_asp/payments.asp";
		$this->_method = "POST";
		if ($this->_mode == PAYMENT_ENGINE_SEND) {
			$this->makeRequiredField('return_good_method', 'POST');
			$this->makeRequiredField('return_bad_method', 'POST');
			$this->makeRequired('seller_id', 'amount', 'order_id', 'seller_name', 'return_good_url', 'return_bad_url', 'currency', 'metal_id', 'information');
		}
		if ($this->_mode == PAYMENT_ENGINE_RECEIVE) {
			//$this->makeRequired('secret_word');
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
		if ($this->_debug) trigger_error("Докладывает -> " . get_class($this). " ! doPayment()" , E_USER_NOTICE);
		if ($this->_mode == PAYMENT_ENGINE_SEND) {
			if ($this->_verifyData()) {
				$this->formMessage();
				if ($this->_debug) trigger_error("Отработал <- " . get_class($this). " ! doPayment()." , E_USER_NOTICE);
				exit();
			} else {
				if ($this->_debug) trigger_error("- Параметры заданы некорректно.", E_USER_WARNING);
				if ($this->_debug) trigger_error("Отработал <- " . get_class($this). " ! doPayment()." , E_USER_NOTICE);
				return false;
			}
		} else {
			trigger_error("- В режиме PAYMENT_ENGINE_RECEIVE метод doPayment() не доступен.", E_USER_ERROR);
		}
		if ($this->_debug) trigger_error("Отработал <- " . get_class($this). " ! doPayment()." , E_USER_NOTICE);
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
		if ($this->_debug) trigger_error("Докладывает -> " . get_class($this). " ! checkPayment()" , E_USER_NOTICE);
		$retVal = true;
		$arg_list = func_get_args();
		if ($this->_mode == PAYMENT_ENGINE_RECEIVE) {
			if (PAYMENT_IGNORE_TEST_PAYMENT) {

				/*** MODIFIED CODE BEGIN ***/

				/*** MODIFIED CODE END ***/

			}
			if (PAYMENT_LOG_PAYER_INFO) {

				/*** MODIFIED CODE BEGIN ***/


				/*** MODIFIED CODE END ***/

			}
			if ($this->_verifyData()) {
				/*** MODIFIED CODE BEGIN ***/

				$arg_list[0] = $_REQUEST[$this->_arrayMap['order_id']];

				if ($_POST["ERROR"]) {
					if ($this->_debug) trigger_error("- Транзакция прошла НЕ успешно.", E_USER_NOTICE);
					$retVal = false;
				}

				/*** MODIFIED CODE END ***/

			} else {
				if ($this->_debug) trigger_error("- Не все обязательные параметры были заданы.", E_USER_ERROR);
				$retVal = false;
			}
		} else {
			trigger_error("- В режиме PAYMENT_ENGINE_SEND метод verifyIncoming() не доступен.", E_USER_ERROR);
			$retVal = false;
		}
		if ($this->_debug) trigger_error("Отработал <- " . get_class($this). " ! doPayment()." , E_USER_NOTICE);
		return $retVal;
	}

}	// end class Payment_template
?>