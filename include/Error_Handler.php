<?PHP

/**
* Error handler class
*
* @package DatingPro
* @subpackage Include files
**/

if (!defined('ERROR_HANDLER_CONFIG')) {
	die('abort: ERROR_HANDLER_CONFIG not available in file ' . __FILE__);
}


class Error_Handler
{
	private $_error_handler_mode;
	
	public function __construct($mode = ERROR_HANDLER_MODE_DISPLAY)
	{
		$this->setMode($mode);
	}
	
	protected function setMode($mode)
	{
		switch ($mode) {
			case ERROR_HANDLER_MODE_LOG:
				$this->_error_handler_mode = $mode;
				$this->_setHandler('errorHandlerLog');
			break;
			case ERROR_HANDLER_MODE_DISPLAY:
				$this->_error_handler_mode = $mode;
				$this->_setHandler('errorHandlerDisplay');
			break;
			case ERROR_HANDLER_MODE_DATABASE:
				trigger_error('ERROR_HANDLER_MODE_DATABASE is not implemented.', E_USER_WARNING);
			break;
			default:
				trigger_error('Unknown Error Handler Mode', E_USER_WARNING);
			break;
		}
	}
	
	public function errorHandlerDisplay($type, $msg, $file, $line, $context)
	{
		switch ($type)
		{
			case E_USER_ERROR:
				echo '<hr>';
				echo 'ERROR!<br>At line '.$line.' of file '.$file.'.<br>Message:<br><b>'.$msg.'</b><br>';
				echo '<font color="red"><i>Script terminated!!!</i></font><br>';
				echo 'Context:<pre>';
				print_r($context);
				echo '</pre><hr>';
				die();
			break;

			case E_USER_WARNING:
				echo '<hr>';
				echo '<font color="blue">WARNING!</font>';
				echo 'At line '.$line.' of file '.$file.'.<br>';
				echo '<font color="blue">Message:</font> <b><font color="red">'.$msg.'</font></b><br>';
				echo '<font color="blue">Context:</font><pre>';
				print_r($context);
				echo '</pre><hr>';
			break;

			case E_USER_NOTICE:
				echo '<font color="gray">NOTICE! </font><b>'.$msg.'</b><br>';
			break;

			default:
			break;
		}
	}
	
	public function errorHandlerLog($type, $msg, $file, $line, $context)
	{
		switch($type)
		{
			case E_USER_ERROR:
				error_log('<hr>', 3, 'log.html');
				error_log('ERROR!<br>At line '.$line.' of file '.$file.'.<br>Message:<br><b>'.$msg.'</b><br>', 3, 'log.html');
				error_log('<font color="red"><i>Script terminated!!!</i></font><br>', 3, 'log.html');
				error_log('Context:<pre>'.print_r($context, true).'</pre><hr>', 3, 'log.html');
				die();
			break;

			case E_USER_WARNING:
				error_log('<hr>', 3, 'log.html');
				error_log('<font color="blue">WARNING!</font>', 3, 'log.html');
				error_log('At line '.$line.' of file '.$file.'.<br>', 3, 'log.html');
				error_log('<font color="blue">Message:</font> <b><font color="red">'.$msg.'</font></b><br>', 3, 'log.html');
				error_log('<font color="blue">Context:</font><pre>'.print_r($context, true).'</pre><hr>', 3, 'log.html');
			break;

			case E_USER_NOTICE:
				error_log('<font color="gray">NOTICE! </font><b>'.$msg.'</b><br>', 3, 'log.html');
			break;

			default:
			break;
		}
	}
	
	public function errorHandlerDatabase($type, $msg, $file, $line, $context)
	{
	}
	
	private function _setHandler($callback_func)
	{
		if (!isset($GLOBALS['_ERROR_HANDLER_METHOD']) || $GLOBALS['_ERROR_HANDLER_METHOD'] != $callback_func) {
			$GLOBALS['_ERROR_HANDLER_OBJECT'] = &$this;
			$GLOBALS['_ERROR_HANDLER_METHOD'] = $callback_func;
			function error_handler_passthru($type, $msg, $file, $line, $context) {
				$GLOBALS['_ERROR_HANDLER_OBJECT']->$GLOBALS['_ERROR_HANDLER_METHOD']($type, $msg, $file, $line, $context);
			}
			set_error_handler('error_handler_passthru');
		}
	}
	
	private function _print_r($content)
	{
		return '_print_r() not implemented!!!';
	}
}

?>