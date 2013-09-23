<?php

require_once PAYMENT_DIR.'Payment_Data_Kipper.php';

/**
* paypal payment system class
*
* @package DatingPro
* @subpackage Payment systems files
**/

class Payment_paypal extends Payment_Data_Kipper
{
	/**
	* Array for mapping abstraction layer payment parameters to PayPal parameters
	*
	*  @access protected
	*  @type array
	*/
	protected $_arrayMap = array
	(
		'seller_id'						=> 'business',
		'amount'						=> 'amount',
		'currency'						=> 'currency_code',
		'order_id'						=> 'custom',
		'test_mode'						=> 'test_ipn',
		'return_method'					=> 'rm',
		'return_url'					=> 'return',
		'notify_url'					=> 'notify_url',
		'cancel_return_url'				=> 'cancel_return',
		'type'							=> 'cmd',
		'product_name'					=> 'item_name',
		'product_id'					=> 'item_number',
		'quantity'						=> 'quantity',
		'amount_recurring'				=> 'a3',
		'period_count_recurring'		=> 'p3',
		'period_type_recurring'			=> 't3',
		'trial_amount_recurring'		=> 'a1',
		'trial_period_count_recurring'	=> 'p1',
		'trial_period_type_recurring'	=> 't1',
		'use_recurring'					=> 'src',
		'use_note'						=> 'no_note',
		'txn_type'						=> 'txn_type',			//
		'amount_response'				=> 'mc_gross',			//
		'currency_response'				=> 'mc_currency',		//
		'payment_date'					=> 'payment_date',		//
	);
	
	
	/**
	*    Standard constructor. Nothing should be changed here.
	*
	*    @param int $mode Mode of class funtioning
	*    @param bool $debug Debug mode
	*/
	public function __construct($mode, $debug, $sandbox)
	{
		$this->_debug = $debug;
		
		if ($this->_debug) trigger_error('### constructor entry point: ' . get_class($this). ' -> __construct()' , E_USER_NOTICE);
		
		if ($mode == PAYMENT_ENGINE_SEND || $mode == PAYMENT_ENGINE_RECEIVE)
		{
			$this->_mode = $mode;
			if ($this->_debug) trigger_error('valid mode = ' . $this->_mode, E_USER_NOTICE);
		}
		else
		{
			if ($this->_debug) trigger_error('invalid mode = ' . $this->_mode . ' / PROGRAM ABORTS' , E_USER_ERROR);
			exit;
		}
		
		$this->_sandbox = $sandbox;
		
		$this->_initialization();
		
		$this->makeFields();
		
		if ($this->_debug) trigger_error('### constructor exit point : ' . get_class($this). ' -> __construct()' , E_USER_NOTICE);
	}
	
	
	/**
	*    Method of class parameters initialization. It is set while being created.
	*    It is divided into 3 parts: general, receive and send.
	*
	*    @access private
	*/
	private function _initialization()
	{
		if ($this->_debug) trigger_error('### method entry point: ' . get_class($this). ' -> _initialization()' , E_USER_NOTICE);
		
		$this->_method = 'POST';
		
		if ($this->_sandbox) {
			$this->_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		} else {
			$this->_url = 'https://www.paypal.com/cgi-bin/webscr';
		}
		
		if ($this->_mode == PAYMENT_ENGINE_SEND)
		{
			$this->makeRequiredField('return_method', '2');
			$this->makeRequired('type', 'seller_id', 'order_id', 'return_url', 'cancel_return_url', 'notify_url', 'product_name');
			$this->makeOptional('amount', 'currency', 'use_note');
			$this->makeOptional('use_recurring', 'amount_recurring', 'period_count_recurring', 'period_type_recurring');
			$this->makeOptional('trial_amount_recurring', 'trial_period_count_recurring', 'trial_period_type_recurring');
		}
		elseif ($this->_mode == PAYMENT_ENGINE_RECEIVE)
		{
			// do nothing
		}
		
		if ($this->_debug) trigger_error('### method exit point : ' . get_class($this). ' -> _initialization().' , E_USER_NOTICE);
	}
	
	
	/**
	*    Checks the incoming data to be set correctly in accord with the parameters set.
	*
	*    @access public
	*    @param ... Parameters enumeration. Transfer by link.
	*    @return bool true- in case all the parameters are set correctly and the payment
	*        was successfully processed, false - in case parameters are set incorrectly or payment failed.
	*/
	public function checkPayment()
	{
		if ($this->_debug) trigger_error('### method entry point : ' . get_class($this). ' -> checkPayment()' , E_USER_NOTICE);
		
		$retVal = true;
		$arg_list = func_get_args();
		
		if ($this->_mode == PAYMENT_ENGINE_RECEIVE)
		{
			if (PAYMENT_IGNORE_TEST_PAYMENT)
			{
				if (isset($_REQUEST[$this->_arrayMap['test_mode']]) && $_REQUEST[$this->_arrayMap['test_mode']] == '1')
				{
					if ($this->_debug) trigger_error('test mode payment has been ignored.', E_USER_NOTICE);
					
					$retVal = false;
				}
			}
			
			if (PAYMENT_LOG_PAYER_INFO)
			{
				$arg_list[0] = 'payer_id: ' . $_REQUEST['payer_id'] . ";\n";
				$arg_list[0].= 'address_street: ' . $_REQUEST['address_street'] . ";\n";
				$arg_list[0].= 'address_zip: ' . $_REQUEST['address_zip'] . ";\n";
				$arg_list[0].= 'first_name: ' . $_REQUEST['first_name'] . ";\n";
				$arg_list[0].= 'address_name: ' . $_REQUEST['address_name'] . ";\n";
				$arg_list[0].= 'address_country: ' . $_REQUEST['address_country'] . ";\n";
				$arg_list[0].= 'address_city: ' . $_REQUEST['address_city'] . ";\n";
				$arg_list[0].= 'payer_email: ' . $_REQUEST['payer_email'] . ";\n";
				$arg_list[0].= 'address_state: ' . $_REQUEST['address_state'] . ";\n";
				$arg_list[0].= 'payer_business_name: ' . $_REQUEST['payer_business_name'] . ";\n";
				$arg_list[0].= 'last_name: ' . $_REQUEST['last_name'] . ";";
			}
			
			if ($this->verifyData())
			{
				$arg_list[1] = $_REQUEST[$this->_arrayMap['order_id']];
				
				/*
				if ($_REQUEST['payer_status'] != 'verified') {
					if ($this->_debug) trigger_error('- payer status could not be verified by PayPal.', E_USER_NOTICE);
					$retVal = false;
				}
				if ($_REQUEST['address_status'] != 'confirmed') {
					if ($this->_debug) trigger_error('- address could not be confirmed by PayPal.', E_USER_NOTICE);
					$retVal = false;
				}
				*/
				
				/*
				Ralf:
				this is the if from Pilot Group. we do not check for received payments, but check for
				successful subscription transactions in /include/payment_request.php
				also, we ignore Pending payments, see below
				
				if ($_REQUEST['payment_status'] != 'Completed' 
				&& $_REQUEST['payment_status'] != 'Pending' 
				&& $_REQUEST['payment_status'] != 'subscr_payment')
				
				*/
				
				//RS 2012-04-19
				//only accept Completed payments. Pending payments need to be approved by the merchant and then paypal sends a Completed IPN
				#if (!isset($_REQUEST['payment_status']) || $_REQUEST['payment_status'] != 'Completed' && $_REQUEST['payment_status'] != 'Pending')
				
				if (!isset($_REQUEST['payment_status']) || $_REQUEST['payment_status'] != 'Completed')
				{
					if ($this->_debug) trigger_error('invalid payment status.', E_USER_NOTICE);
					
					$retVal = false;
				}
			}
			else
			{
				if ($this->_debug) trigger_error('data verification failed.', E_USER_ERROR);
				
				$retVal = false;
			}
		}
		else
		{
			trigger_error('wrong mode = PAYMENT_ENGINE_SEND in method checkPayment().', E_USER_ERROR);
			
			$retVal = false;
		}
		
		if ($this->_debug) trigger_error('### method exit point : ' . get_class($this). ' -> checkPayment().' , E_USER_NOTICE);
		
		return $retVal;
	}
}

?>