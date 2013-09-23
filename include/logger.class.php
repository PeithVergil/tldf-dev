<?php

/*
	NAME			: logger.class.php
	DESCRIPTION 	: simple textfile logger class with profiling/benchmark option
					  would be nice to store sums and counters in a database and calculate statistics on demand
	LICENSE			: GPL
	AUTHOR			: Ralf Strehle (ralf.strehle@yahoo.de)
	COPYRIGHT		: 2010, by author
	
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
	
	
	EXAMPLE 1 (default parameters)
	------------------------------
	
	require_once 'logger.class.php';
	$log = new logger();
	...
	$log->log('function abc START');
	...
	$log->log('function abc END');
	...
	
	
	EXAMPLE 2 (custom parameters and conditional logging)
	-----------------------------------------------------
	
	define('LOG', true);
	
	if (LOG) {
		require_once 'logger.class.php';
		$cmd = isset($_REQUEST['cmd']) ? $_REQUEST['cmd'] : 'index';
		$log = new logger('/log/', '@'.$cmd, $cmd);
	}
	...
	if (LOG) $log->log('function abc START');
	...
	if (LOG) $log->log('function abc END');
	...
*/

class logger
{
	var $fd = null;
	var $mt_start = array();
	var $item = '';
	
	public function __construct($path = '', $file = '', $item = '')
	{
		if ($file == '') {
			$file = '@'.basename($_SERVER['PHP_SELF'], '.php');
		}
		if ($item == '') {
			$item = $_SERVER['PHP_SELF'];
		}
		$this->fd = fopen($path.$file.'.txt', 'wb');
		$this->item = $item;
		$this->mt_start = explode(' ', microtime());
		$this->log('start of '.$item);
		register_shutdown_function(array($this, 'log'));
	}

	public function log($s = '', $bench = true)
	{
		if ($s == '') {
			$s = 'end of '.$this->item;
		}
		
		if ($bench) {
			fwrite($this->fd, number_format($this->benchmark(), 6).': '.$s."\n");
			if (substr($s, -3) == 'END') {
				fwrite($this->fd, str_repeat(' ', 10).str_repeat('-', 40)."\n");
			}
		} else {
			fwrite($this->fd, str_repeat(' ', 10).$s."\n");
		}
	}
	
	private function benchmark()
	{
		$mt_end = explode(' ', microtime());
		$bm = ($mt_end[1] - $this->mt_start[1] + $mt_end[0] - $this->mt_start[0]);
		return $bm;
	}
}

?>