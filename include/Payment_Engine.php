<?php

/**
*	Class used for data exchange with the payment system. It contains
*		an enterprise of classes. The principles of class functioning depend on the chosen mode of operation.
*
*	Every class created by means of Engine can be easily broaden. Only constructor of the class and class
*		$_arrayMap requires modification. It is desirable to make the names of the keys in the array
*		$_arrayMap identical, but this is not obligatory.
*
*	The places in the body of the class used for class modification are underlined
*		by the following comment: \*** MODIFIED CODE ***\. Also the folder 'systems' contains
*		file tmpl_system.php which stands for the template for the class of the payment system.
*
* @package DatingPro
* @subpackage Include files
**/


if (!defined('PAYMENT_CONFIG')) {
	die('abort: PAYMENT_CONFIG not available in file ' . __FILE__);
}

require_once PAYMENT_DIR.'Error_Handler.php';


class Payment_Engine extends Error_Handler
{
	// the private properties are needed for passing the parameters of the constructor to the factory method
	
	// int
	private $_payment_engine_mode;
	
	// bool
	private $_payment_engine_debug;
	
	// bool
	private $_payment_engine_sandbox;
	
	/**
	*	Standard constructor
	*
	*	@param int $mode Mode of class operation
	*	@param bool $debug Enabling of debug mode
	*	@param bool $log Enabling logging of the debug system
	*/
	public function __construct($mode = PAYMENT_ENGINE_SEND, $debug = false, $log = false, $sandbox = false)
	{
		if ($log) {
			parent::__construct(ERROR_HANDLER_MODE_LOG);
		} else {
			parent::__construct();
		}
		$this->_payment_engine_debug = $debug;
		$this->_payment_engine_sandbox = $sandbox;
		$this->_switchEngine($mode);
	}
	
	/**
	*	Enterprise of classes. Returns the class identical for a definite payment system
	*
	*	@access public
	*	@param string $type name of the class of the payment system
	*	@return class
	*/
	public function factory($type)
	{
		$class = 'Payment_'.$type;
		$file = SYSTEMS_DIR.'classes/'.$type.'.php';
		if (include_once($file)) {
			if ($this->_payment_engine_debug) trigger_error('file '.$file.' included sucessfully.', E_USER_NOTICE);
			if (class_exists($class)) {
				if ($this->_payment_engine_debug) trigger_error('class '.$class.' exists.', E_USER_NOTICE);
				$object = new $class($this->_payment_engine_mode, $this->_payment_engine_debug, $this->_payment_engine_sandbox);
				return $object;
			} else {
				trigger_error('ERROR: class '.$class.' not found.', E_USER_ERROR);
			}
		} else {
			trigger_error('ERROR: include file '.$file.' not found.', E_USER_ERROR);
		}
	}
	
	/**
	*	Switch of the mode of the sites
	*
	*	@access public
	*	@type const int
	*/
	private function _switchEngine($mode)
	{
		switch ($mode) {
			case PAYMENT_ENGINE_SEND:
				$this->_payment_engine_mode = $mode;
				if ($this->_payment_engine_debug) trigger_error('payment engine mode changed to PAYMENT_ENGINE_SEND.', E_USER_NOTICE);
			break;

			case PAYMENT_ENGINE_RECEIVE:
				$this->_payment_engine_mode = $mode;
				if ($this->_payment_engine_debug) trigger_error('payment engine mode changed to PAYMENT_ENGINE_RECEIVE.', E_USER_NOTICE);
			break;

			default:
				trigger_error('ERROR: unknown payment engine mode: '.$mode, E_USER_ERROR);
			break;
		}
	}
}

?>