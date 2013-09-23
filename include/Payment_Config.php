<?php

/**
* Payment config file
*
* @package DatingPro
* @subpackage Include files
**/

/**
*	Indicator of config inclusion
*/
define('PAYMENT_CONFIG', true);

/**
*  This mode uses testing payments.
*  In the mode of the real work the value PAYMENT_IGNORE_TEST_PAYMENT must be false.
*/
define('PAYMENT_IGNORE_TEST_PAYMENT', false);

/**
*	SEND mode expects that data will be send to the payment system server
*/
define('PAYMENT_ENGINE_SEND', 100);

/**
*	RECEIVE mode expects that class will recieve data from payment system server
*/
define('PAYMENT_ENGINE_RECEIVE', 200);


// }}} Payment config end

// {{{ Error handler config begin

/**
*	Indicator of config inclusion
*/
define('ERROR_HANDLER_CONFIG', false);

/**
*   Constants for setting Error_Handler class working mode
*/
define('ERROR_HANDLER_MODE_LOG', 1);		// логирование ошибок
define('ERROR_HANDLER_MODE_DISPLAY', 2);	// вывод ошибок на экран
define('ERROR_HANDLER_MODE_DATABASE', 3);	// запись ошибок в базу данных

// }}} Error handler config end

?>