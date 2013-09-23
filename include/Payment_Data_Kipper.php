<?php

/**
* Class for saving data which is necessary for different payment
* systems and implementation of standard operations with them. Also it contains
* information about the operating modes of the class functioning.
*
* @package DatingPro
* @subpackage Include files
**/

if (!defined('PAYMENT_CONFIG')) {
	die('abort: PAYMENT_CONFIG not available in file ' . __FILE__);
}

abstract class Payment_Data_Kipper
{
	/**
	*	Debug mode
	*	when Debug==true, the request is not sent but printed to the screen
	*
	*	@access protected
	*	@type bool
	*/
	protected $_debug = false;
	
	/**
	*	Remove empty options
	*
	*	@access protected
	*	@type bool
	*/
	protected $_remove_empty_options = true;
	
	/**
	*	Mode of class operation PAYMENT_ENGINE_SEND or PAYMENT_ENGINE_RECEIVE
	*
	*	@access protected
	*	@type int
	*/
	protected $_mode = PAYMENT_ENGINE_SEND;
	
	/**
	*	The way the data is sent
	*
	*	@access protected
	*	@type string
	*/
	protected $_method = 'POST';
	
	/**
	*	The URL to the payment system site
	*
	*	@access protected
	*	@type string
	*/
	protected $_url = '';
	
	/**
	*	sandbox mode
	*
	*	@access protected
	*	@type bool
	*/
	protected $_sandbox = false;
	
	/**
	*	Array of the data with parameters
	*
	*	@access protected
	*	@type array
	*/
	protected $_options = array();
	
	/**
	*	The list of obligatory parameters
	*
	*	@access protected
	*	@type array
	*/
	protected $_required = array();
	
	/**
	*	The list of optional parameters
	*
	*	@access protected
	*	@type array
	*/
	protected $_optional = array();
	
	
	
	/**
	*	Standard constructor
	*/
	public function __construct()
	{
	}
	
	/**
	*	Sets the parameters obligatory.
	*
	*	@access protected
	*	@param ... Parameters enumeration
	*/
	protected function makeRequired()
	{
		foreach (func_get_args() as $field) {
			$this->_required[$field] = null;
		}
	}
	
	/**
	*	Sets the parameters optional.
	*
	*	@access protected
	*	@param ... Parameters enumeration
	*/
	protected function makeOptional()
	{
		foreach (func_get_args() as $field) {
			$this->_optional[$field] = null;
		}
	}
	
	/**
	*	Adds obligatory parameter.
	*
	*	@access protected
	*	@param string $field Name of the parameter
	*	@param string $value Value of the parameter
	*/
	protected function makeRequiredField($field, $value = null)
	{
		$this->_required[$field] = $value;
	}
	
	/**
	*	Adds optional parameter.
	*
	*	@access protected
	*	@param string $field Name of the parameter
	*	@param string $value Value of the parameter
	*/
	protected function makeOptionalField($field, $value = null)
	{
		$this->_optional[$field] = $value;
	}
	
	/**
	*	Combines obligatory and optional parameters into one array.
	*
	*	@access protected
	*/
	protected function makeFields()
	{
		foreach ($this->_required as $field => $value) {
			$this->_options[$field] = $value;
		}
		
		foreach ($this->_optional as $field => $value) {
			$this->_options[$field] = $value;
		}
	}
	
	/**
	*	Move payment request data to _options array.
	*	It is necessary for the parameter to already exist in the _options array
	*
	*	@acces public
	*	@param array $where assotiative array 'key' => 'value'
	*/
	public function setOptions($where)
	{
		foreach ($this->_options as $key => $field)
		{
			if (isset($where[$key]))
			{
				$this->_options[$key] = $where[$key];
			} 
			elseif ($this->_remove_empty_options)
			{
				unset($this->_options[$key]);
			}
		}
	}
	
	/**
	*	Data verification for existance.
	*
	*	@access protected
	*	@return bool true if all the obligatory fields are set, otherwise false will be returned
	*/
	protected function verifyData()
	{
		foreach ($this->_required as $field => $value)  {
			if (is_null($this->_options[$field])) {
				return false;
			}
		}
		return true;
	}
	
	/**
	*    Method used for sending data to the server of Payment System.
	*        According to the type of Payment System there is either redirect to the site of Payment System(POST),
	*        or the URL is put in the header.
	*
	*    @access public
	*    @return mixed When all the parameters were set correctly, nothing will be returned, there will be redirect.
	*        Otherwise notification will be generated and false will be returned
	*/
	public function doPayment()
	{
		if ($this->_debug) trigger_error('method entry point : ' . get_class($this). ' -> doPayment()' , E_USER_NOTICE);
		
		if ($this->_mode == PAYMENT_ENGINE_SEND)
		{
			if ($this->verifyData())
			{
				$this->formMessage();
				
				if ($this->_debug) trigger_error('method exit point (data verified and sent) : ' . get_class($this). ' -> doPayment().' , E_USER_NOTICE);
				exit;
			}
			else
			{
				if ($this->_debug) trigger_error('data verification with Payment_Data_Kipper->verifyDate() failed.', E_USER_WARNING);
			}
		}
		else
		{
			if ($this->_debug) trigger_error('- wrong mode = mode must be PAYMENT_ENGINE_SEND', E_USER_ERROR);
		}
		
		if ($this->_debug) trigger_error('method exit point : ' . get_class($this). ' -> doPayment().' , E_USER_NOTICE);
		
		return false;
	}
	
	/**
	*	Creation of a form or a string of a request by some definite method
	*		depending on the settings of a definite class of a payment system.
	*
	*	@access protected
	*	@return bool
	*/
	protected function formMessage()
	{
		if ($this->_method === 'GET')
		{
			$redirect = 'Location: ' . $this->_url . '?';
			
			foreach ($this->_options as $key => $value) {
				$redirect .= $this->_arrayMap[$key] . '=' . $value . '&';
			}
			
			if ($this->_debug) {
				print $redirect;
			} else {
				header($redirect);
			}
		}
		elseif ($this->_method === 'POST')
		{
			$retHTML = '<html><body onload="document.send_form.submit();">';
			$retHTML.= '<form method="post" name="send_form" action="'.$this->_url.'">';
			
			foreach ($this->_options as $key => $value) {
				$retHTML .= '<input type="hidden" name="'.$this->_arrayMap[$key].'" value="'.$value.'" />';
			}
			
			$retHTML .= '</form></body></html>';
			
			if ($this->_debug) {
				print htmlspecialchars($retHTML);
			} else {
				print $retHTML;
			}
		}
		else
		{
			return false;
		}
	}
	
	/**
	*	Check whether the parameter is obligatory or not
	*
	*	@access protected
	*	@param string $field the name of the parameter used for checking
	*/
	//RS: not in use
	protected function isRequired($field)
	{
		return (isset($this->_required[$field]));
	}
	
	/**
	*	Returns the value of the parameter by key.
	*
	*	@access public
	*	@param string $field
	*	@return mixed returns the value of string if the field exists, otherwise
	*		false will be returned
	*/
	//RS: not in use
	public function getField($field)
	{
		if (array_key_exists($field, $this->_options)) {
			return $this->_options[$field];
		}
		return false;
	}
	
	/**
	*	Setup of a definite value to a definite  parameter. It is necessary
	*		for the parameter to already exist.
	*
	*	@access public
	*	@return bool will return true if the field exists, otherwise false will be returned
	*/
	//RS: not in use
	public function set($field, $value)
	{
		if (!array_key_exists($field, $this->_options)) {
			return false;
		}
		$this->_options[$field] = $value;
		return true;
	}
}

?>