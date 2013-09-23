<?php

// Smarty class is extended for use with osdate.
// stripslashes added for handling of magic quotes
//
// Vijay Nair	25 May 2006
//
// Ralf Strehle	29 May 2009
// - bugfix in handling of array as $tpl_var 


function array_stripslashes(&$item, $key)
{
	$item = stripslashes($item);
}
	
class osDate_Smarty extends Smarty{

	/**
	 * assigns values to template variables
	 *
	 * @param array|string $tpl_var the template variable name(s)
	 * @param mixed $value the value to assign
	 *
	 * MOD: process arrays with up to 3 dimensions (original smarty: only 2)
	 * MOD: all values are treated with stripslashes
	 */
	function assign($tpl_var, $value = null)
	{
		if (is_array($tpl_var))
		{
			array_walk_recursive($tpl_var, 'array_stripslashes');
			
			foreach ($tpl_var as $key => $val)
			{
				if ($key != '')
				{
					$this->_tpl_vars[$key] = $val;
				}
			}
		}
		else
		{
			if (is_array($value))
			{
				array_walk_recursive($value, 'array_stripslashes');
			}
			
			$this->_tpl_vars[$tpl_var] = $value;
		}
	}
	
	function assign_by_ref($tpl_var, &$value)
	{
		if ($tpl_var != '')
		{
			if (is_array($value))
			{
				array_walk_recursive($value, 'array_stripslashes');
			}
			
			$this->_tpl_vars[$tpl_var] = &$value;
		}
    }

	 /*
	function assign($tpl_var, $value = null)
	{
		if (is_array($tpl_var)) {
			foreach ($tpl_var as $key1 => $val1) {
				if ($key1) {
					if (!is_array($val1)) {
						$this->_tpl_vars[$key1] = stripslashes($val1);
					} else {
						foreach ($val1 as $key2 => $val2) {
							if (!is_array($val2))
								$val1[$key2] = stripslashes($val2);
							else
								$val1[$key2] = $val2;
						}
						$this->_tpl_vars[$key1] = $val1;
					}
				}
			}
		} else {
			if ($tpl_var != '') {
				if (!is_array($value)) {
					$this->_tpl_vars[$tpl_var] = stripslashes($value);
				} else {
					foreach ($value as $key1 => $val1) {
						if (!is_array($val1)) {
							$value[$key1] = stripslashes($val1);
						} else {
							foreach ($val1 as $key2 => $val2) {
								if (!is_array($val2))
									$val1[$key2] = stripslashes($val2);
								else
									$val1[$key2] = $val2;
							}
							$value[$key1] = $val1;
						}
					}
					$this->_tpl_vars[$tpl_var] = $value;
				}
			}
		}
	}
	*/
}