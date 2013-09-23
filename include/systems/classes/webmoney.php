<?php

include_once PAYMENT_DIR."Payment_Data_Kipper.php";


/**
* webmoney payment system class
*
* @package DatingPro
* @subpackage Payment systems files
**/

class Payment_webmoney extends Payment_Data_Kipper {

	/**
	*	Array for setting Payment System parameters.
	*
	*	@access private
	*	@type array
	*/
	var $_arrayMap = array(

	/*** MODIFIED CODE BEGIN ***/

	/* General Parameters: */

	"seller_id"		=> "LMI_PAYEE_PURSE",

	"amount"		=> "LMI_PAYMENT_AMOUNT",

	"order_id"		=> "LMI_PAYMENT_NO",

	"test_mode"		=> "LMI_SIM_MODE",

	"description"		=> "LMI_PAYMENT_DESC",

	"return_url"	=> "LMI_RESULT_URL",
	"success_url"	=> "LMI_SUCCESS_URL",
	"fail_url"	=> "LMI_FAIL_URL",
	"success_url_method"	=> "LMI_SUCCESS_METHOD",
	"fail_url_method"	=> "LMI_FAIL_METHOD",


	"ret_prerequest"	=> "LMI_PREREQUEST",	/// предварительные данные о платеже
	"ret_test_mode"	=> "LMI_MODE",	///тестовый режм (0 - реальный, 1 - тестовый)
	"ret_wm_inv_no"	=> "LMI_SYS_INVS_NO",	//// уникальный номер с платежки
	"ret_wm_trans_no"	=> "LMI_SYS_TRANS_NO",	//// уникальный номер с платежки
	"ret_payer_id"	=> "LMI_PAYER_PURSE",	/// кошелек того кто платит
	"ret_payer_wm"	=> "LMI_PAYER_WM",	/// идентификатор того кто платит
	"ret_hash"	=> "LMI_HASH",	/// подпись для проверки целостности
	"ret_trans_date"	=> "LMI_SYS_TRANS_DATE",	/// дата транзакции
	"ret_secret_key"	=> "LMI_SECRET_KEY",	/// будет пустым

	/*** MODIFIED CODE END ***/

	);


	/**
	*	Standard constructor. Nothing should be changed here.
	*
	*	@param int $mode Mode of class funtioning
	*	@param bool $debug Debug mode
	*/
	function Payment_webmoney($mode, $debug) {
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

		$this->_url = "https://merchant.webmoney.ru/lmi/payment.asp";
		$this->_method = "POST";
		if ($this->_mode == PAYMENT_ENGINE_SEND) {

			$this->makeRequired("seller_id",
								"order_id",
								"amount",
//								"test_mode",
								"description",
								"return_url",
								"success_url",
								"success_url_method",
								"fail_url",
								"fail_url_method"
				);
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
	function checkPayment($merchant_data="") {
		if ($this->_debug) trigger_error("Докладывает -> " . get_class($this). " ! checkPayment()" , E_USER_NOTICE);
		$retVal = true;
		$arg_list = func_get_args();
		if ($this->_mode == PAYMENT_ENGINE_RECEIVE) {
			if (PAYMENT_IGNORE_TEST_PAYMENT) {

				/*** MODIFIED CODE BEGIN ***/

				if (isset($_REQUEST[$this->_arrayMap['test_mode']]) &&
				$_REQUEST[$this->_arrayMap['ret_test_mode']] == 1) {
					if ($this->_debug) trigger_error("- Оплата проходила в демонстрационном режиме.", E_USER_NOTICE);
					$retVal = false;
				}

				/*** MODIFIED CODE END ***/

			}
			if (PAYMENT_LOG_PAYER_INFO) {

				/*** MODIFIED CODE BEGIN ***/

				$arg_list[0] = "LMI_PAYEE_PURSE: " . $_REQUEST['LMI_PAYEE_PURSE'] . ";\n";
				$arg_list[0] .= "LMI_SYS_INVS_NO: " . $_REQUEST['LMI_SYS_INVS_NO'] . ";\n";
				$arg_list[0] .= "LMI_SYS_TRANS_NO: " . $_REQUEST['LMI_SYS_TRANS_NO'] . ";\n";
				$arg_list[0] .= "LMI_PAYER_PURSE: " . $_REQUEST['LMI_PAYER_PURSE'] . ";\n";
				$arg_list[0] .= "LMI_PAYER_WM: " . $_REQUEST['LMI_PAYER_WM'] . ";\n";

				/*** MODIFIED CODE END ***/

			}
			if ($this->_verifyData()) {
				/*** MODIFIED CODE BEGIN ***/

				$arg_list[1] = $_REQUEST[$this->_arrayMap['order_id']];

				$data['ret_hash'] = strtoupper($_REQUEST[$this->_arrayMap['ret_hash']]);

				$data['seller_id'] = $_REQUEST[$this->_arrayMap['seller_id']];
				$data['count'] = $_REQUEST[$this->_arrayMap['amount']];
				$data['id_req'] = $_REQUEST[$this->_arrayMap['order_id']];
				$data['ret_test_mode'] = $_REQUEST[$this->_arrayMap['ret_test_mode']];
				$data['ret_wm_inv_no'] = $_REQUEST[$this->_arrayMap['ret_wm_inv_no']];
				$data['ret_wm_trans_no'] = $_REQUEST[$this->_arrayMap['ret_wm_trans_no']];
				$data['ret_trans_date'] = $_REQUEST[$this->_arrayMap['ret_trans_date']];
				$data['ret_secret_key'] = $merchant_data["secret_key"];
				$data['ret_payer_id'] = $_REQUEST[$this->_arrayMap['ret_payer_id']];
				$data['ret_payer_wm'] = $_REQUEST[$this->_arrayMap['ret_payer_wm']];

				$data['get_hash'] = strtoupper(trim(md5($data['seller_id'].$data['count'].$data['id_req'].$data['ret_test_mode'].$data['ret_wm_inv_no'].$data['ret_wm_trans_no'].$data['ret_trans_date'].$data['ret_secret_key'].$data['ret_payer_id'].$data['ret_payer_wm'])));

				if ($data['get_hash'] != $data['ret_hash']) {
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