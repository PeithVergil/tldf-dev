<?php

/**
* Using for database dumping
*
* @package DatingPro
* @subpackage Include files
**/

class dumper {
	function dumper($config, $lang) {
		if (file_exists(PATH . "dumper.cfg.php")) {
			include(PATH . "dumper.cfg.php");
		}
		else{
			$this->SET['last_action'] = 0;
			$this->SET['last_db_backup'] = '';
			$this->SET['tables'] = '';
			$this->SET['comp_method'] = 2;
			$this->SET['comp_level']  = 7;
			$this->SET['last_db_restore'] = '';
		}
		$this->config = $config;
		$this->lang = $lang;
		$this->tabs = 0;
		$this->records = 0;
		$this->size = 0;
		$this->comp = 0;
	}

	function backup($output = true) {
		set_error_handler("SKD_errorHandler");

		$this->SET['last_action']     = 0;
		$this->SET['last_db_backup']  = $this->config["dbname"];
		$this->SET['tables_exclude']  =  0;
		$this->SET['tables']          = '';
		$this->SET['comp_method']     =  0;
		$this->SET['comp_level']      =  0;
		$this->fn_save();

		$this->SET['tables']          = explode(",", $this->SET['tables']);
		if (!empty($_POST['tables'])) {
			foreach($this->SET['tables'] AS $table){
				$table = preg_replace("/[^\w*?^]/", "", $table);
				$pattern = array( "/\?/", "/\*/");
				$replace = array( ".", ".*?");
				$tbls[] = preg_replace($pattern, $replace, $table);
			}
		}
		else{
			$this->SET['tables_exclude'] = 1;
		}

		if ($this->SET['comp_level'] == 0) {
			$this->SET['comp_method'] = 0;
		}
		$db = $this->SET['last_db_backup'];

		if (!$db) {
			if ($output) print $this->tpl_l($this->lang["backup"]["err_empty_db"], C_ERROR);
			if ($output) print $this->tpl_enableBack();
			exit;
		}
		if ($output) print $this->tpl_l($this->lang["backup"]["connect_db"]." `{$db}`.");
		mysql_select_db($db) or trigger_error ($this->lang["backup"]["err_select_db"]."<BR>" . mysql_error(), E_USER_ERROR);
		$tables = array();
		$result = mysql_query("SHOW TABLES");
		$all = 0;
		while($row = mysql_fetch_array($result)) {
			$status = 0;
			if (!empty($tbls)) {
				foreach($tbls AS $table){
					$exclude = preg_match("/^\^/", $table) ? true : false;
					if (!$exclude) {
						if (preg_match("/^{$table}$/i", $row[0])) {
							$status = 1;
						}
						$all = 1;
					}
					if ($exclude && preg_match("/{$table}$/i", $row[0])) {
						$status = -1;
					}
				}
			}
			else {
				$status = 1;
			}
			if ($status >= $all) {
				$tables[] = $row[0];
			}
		}

		$tabs = count($tables);
		$result = mysql_query("SHOW TABLE STATUS");
		$tabinfo = array();
		$tabinfo[0] = 0;
		$info = '';
		while($item = mysql_fetch_assoc($result)){
			if(in_array($item['Name'], $tables)) {
				$item['Rows'] = empty($item['Rows']) ? 0 : $item['Rows'];
				$tabinfo[0] += $item['Rows'];
				$tabinfo[$item['Name']] = $item['Rows'];
				$this->size += $item['Data_length'];
				$tabsize[$item['Name']] = 1 + round(LIMIT * 1048576 / ($item['Avg_row_length'] + 1));
				if($item['Rows']) $info .= "|" . $item['Rows'];
			}
		}
		$show = 10 + $tabinfo[0] / 50;
		$info = $tabinfo[0] . $info;
		$name = $db . '_' . date("Y-m-d_H-i");
		$fp = $this->fn_open($name, "w");
		if ($output) print $this->tpl_l($this->lang["backup"]["create_file_db"]."<BR>\\n  -  {$this->filename}");
		$this->fn_write($fp, "#SKD101|{$db}|{$tabs}|" . date("Y.m.d H:i:s") ."|{$info}\n\n");
		$t=0;
		if ($output) print $this->tpl_l(str_repeat("-", 60));
		$result = mysql_query("SET SQL_QUOTE_SHOW_CREATE = 1");
		foreach ($tables AS $table){
			if ($output) print $this->tpl_l($this->lang["backup"]["process_table"]." `{$table}` [" . $this->fn_int($tabinfo[$table]) . "].");

			$result = mysql_query("SHOW CREATE TABLE {$table}");
			$tab = mysql_fetch_array($result);
			$tab = preg_replace('/(default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP|DEFAULT CHARSET=\w+|character set \w+|collate \w+)/i', '/*!40101 \\1 */', $tab);
			$this->fn_write($fp, "DROP TABLE IF EXISTS {$table};\n{$tab[1]};\n\n");
			$NumericColumn = array();
			$result = mysql_query("SHOW COLUMNS FROM {$table}");
			$field = 0;
			while($col = mysql_fetch_row($result)) {
				$NumericColumn[$field++] = preg_match("/^(\w*int|year)/", $col[1]) ? 1 : 0;
			}
			$fields = $field;
			$from = 0;
			$limit = $tabsize[$table];
			$limit2 = round($limit / 3);
			if ($tabinfo[$table] > 0) {
				if ($tabinfo[$table] > $limit2) {
					if ($output) print $this->tpl_s(0, $t / $tabinfo[0]);
				}
				$i = 0;
				$this->fn_write($fp, "INSERT INTO `{$table}` VALUES");
				while(($result = mysql_query("SELECT * FROM {$table} LIMIT {$from}, {$limit}")) && ($total = mysql_num_rows($result))){
					while($row = mysql_fetch_row($result)) {
						$i++;
						$t++;

						for($k = 0; $k < $fields; $k++){
							if ($NumericColumn[$k])
							$row[$k] = isset($row[$k]) ? $row[$k] : "NULL";
							else
							$row[$k] = isset($row[$k]) ? "'" . mysql_escape_string($row[$k]) . "'" : "NULL";
						}

						$this->fn_write($fp, ($i == 1 ? "" : ",") . "\n(" . implode(", ", $row) . ")");
						if ($i % $limit2 == 0)
						if ($output) print $this->tpl_s($i / $tabinfo[$table], $t / $tabinfo[0]);
					}
					mysql_free_result($result);
					if ($total < $limit) {
						break;
					}
					$from += $limit;
				}

				$this->fn_write($fp, ";\n\n");
				if ($output) print $this->tpl_s(1, $t / $tabinfo[0]);}
		}
		$this->tabs = $tabs;
		$this->records = $tabinfo[0];
		$this->comp = $this->SET['comp_method'] * 10 + $this->SET['comp_level'];
		if ($output) print $this->tpl_s(1, 1);
		if ($output) print $this->tpl_l(str_repeat("-", 60));
		$this->fn_close($fp);
		if ($output) print $this->tpl_l("`{$db}`: ".$this->lang["backup"]["copy_db_created"], C_RESULT);
		if ($output) print $this->tpl_l($this->lang["backup"]["db_size"].":       " . round($this->size / 1048576, 2) . " Mb", C_RESULT);
		$filesize = round(filesize(PATH . $this->filename) / 1048576, 2) . " Mb";
		if ($output) print $this->tpl_l($this->lang["backup"]["file_size"].": {$filesize}", C_RESULT);
		if ($output) print $this->tpl_l($this->lang["backup"]["processed_tables"].": {$tabs}", C_RESULT);
		if ($output) print $this->tpl_l($this->lang["backup"]["processed_strings"].":   " . $this->fn_int($tabinfo[0]), C_RESULT);
		if ($output) print "<SCRIPT>with (document.getElementById('save')) {style.display = ''; innerHTML = '".$this->lang["settings"]["get_file"]." ({$filesize})'; href = '" . URL . $this->filename . "'; }document.getElementById('back').disabled = 0;</SCRIPT>";
	}



	function db_select(){
		if (DBNAMES != '') {
			$items = explode(',', trim(DBNAMES));
			foreach($items AS $item){
				if (mysql_select_db($item)) {
					$tables = mysql_query("SHOW TABLES");
					if ($tables) {
						$tabs = mysql_num_rows($tables);
						$dbs[$item] = "{$item} ({$tabs})";
					}
				}
			}
		}
		else {
			$result = mysql_query("SHOW DATABASES");
			$dbs = array();
			while($item = mysql_fetch_array($result)){
				if (mysql_select_db($item[0])) {
					$tables = mysql_query("SHOW TABLES");
					if ($tables) {
						$tabs = mysql_num_rows($tables);
						$dbs[$item[0]] = "{$item[0]} ({$tabs})";
					}
				}
			}
		}
		return $dbs;
	}

	function file_select(){
		$files = array('');
		if (is_dir(PATH) && $handle = opendir(PATH)) {
			while (false !== ($file = readdir($handle))) {
				if (preg_match("/^.+?\.sql(\.(gz|bz2))?$/", $file)) {
					$files[$file] = $file;
				}
			}
			closedir($handle);
		}
		return $files;
	}

	function fn_open($name, $mode){
		if ($this->SET['comp_method'] == 2) {
			$this->filename = "{$name}.sql.bz2";
			return bzopen(PATH . $this->filename, "{$mode}b{$this->SET['comp_level']}");
		}
		elseif ($this->SET['comp_method'] == 1) {
			$this->filename = "{$name}.sql.gz";
			return gzopen(PATH . $this->filename, "{$mode}b{$this->SET['comp_level']}");
		}
		else{
			$this->filename = "{$name}.sql";
			return fopen(PATH . $this->filename, "{$mode}b");
		}
	}

	function fn_write($fp, $str){
		if ($this->SET['comp_method'] == 2) {
			bzwrite($fp, $str);
		}
		elseif ($this->SET['comp_method'] == 1) {
			gzwrite($fp, $str);
		}
		else{
			fwrite($fp, $str);
		}
	}

	function fn_read($fp){
		if ($this->SET['comp_method'] == 2) {
			return bzread($fp, 4096);
		}
		elseif ($this->SET['comp_method'] == 1) {
			return gzread($fp, 4096);
		}
		else{
			return fread($fp, 4096);
		}
	}

	function fn_read_str($fp){
		$string = '';
		$this->file_cache = ltrim($this->file_cache);
		$pos = strpos($this->file_cache, "\n", 0);
		if ($pos < 1) {
			while (!$string && ($str = $this->fn_read($fp))){
				$pos = strpos($str, "\n", 0);
				if ($pos === false) {
					$this->file_cache .= $str;
				}
				else{
					$string = $this->file_cache . substr($str, 0, $pos);
					$this->file_cache = substr($str, $pos + 1);
				}
			}
			if (!$str) {
				if ($this->file_cache) {
					$string = $this->file_cache;
					$this->file_cache = '';
					return trim($string);
				}
				return false;
			}
		}
		else {
			$string = substr($this->file_cache, 0, $pos);
			$this->file_cache = substr($this->file_cache, $pos + 1);
		}
		return trim($string);
	}

	function fn_close($fp){
		if ($this->SET['comp_method'] == 2) {
			bzclose($fp);
		}
		elseif ($this->SET['comp_method'] == 1) {
			gzclose($fp);
		}
		else{
			fclose($fp);
		}
		@chmod(PATH . $this->filename, 0666);
		$this->fn_index();
	}

	function fn_select($items, $selected){
		$select = '';
		foreach($items AS $key => $value){
			$select .= $key == $selected ? "<OPTION VALUE='{$key}' SELECTED>{$value}" : "<OPTION VALUE='{$key}'>{$value}";
		}
		return $select;
	}

	function fn_save(){
		if (SC) {
			$fp = fopen(PATH . "dumper.cfg.php", "wb");
			fwrite($fp, "<?php\n\$this->SET = " . $this->fn_arr2str($this->SET) . "\n?>");
			fclose($fp);
			@chmod(PATH . "dumper.cfg.php", 0666);
			$this->fn_index();
		}
	}

	function fn_index(){
		if (!file_exists(PATH . 'index.html')) {
			$fh = fopen(PATH . 'index.html', 'wb');
			fwrite($fh, $this->tpl_backup_index());
			fclose($fh);
			@chmod(PATH . 'index.html', 0666);
		}
	}

	function fn_int($num){
		return number_format($num, 0, ',', ' ');
	}

	function fn_arr2str($array) {
		$str = "array(\n";
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$str .= "'$key' => " . $this->fn_arr2str($value) . ",\n\n";
			}
			else {
				$str .= "'$key' => '" . str_replace("'", "\'", $value) . "',\n";
			}
		}
		return $str . ")";
	}


	function tpl_l($str, $color = C_DEFAULT){
		$str = preg_replace("/\s{2}/", " &nbsp;", $str);
		return "<SCRIPT>l('{$str}', $color);</SCRIPT>";
	}

	function tpl_enableBack(){
		return "<SCRIPT>document.getElementById('back').disabled = 0;</SCRIPT>";
	}

	function tpl_s($st, $so){
		$st = round($st * 100);
		$st = $st > 100 ? 100 : $st;
		$so = round($so * 100);
		$so = $so > 100 ? 100 : $so;
		return "<SCRIPT>s({$st},{$so});</SCRIPT>";
	}

	function tpl_backup_index(){
		return "<CENTER><H1>{$this->lang["backup"]["err_permission"]}</H1></CENTER>";
	}
	function SKD_errorHandler($errno, $errmsg, $filename, $linenum, $vars) {
		$dt = date("Y.m.d H:i:s");
		$errmsg = addslashes($errmsg);
		print $this->tpl_l("{$dt}<BR><B>".$this->lang["backup"]["error"]."</B>", C_ERROR);
		print $this->tpl_l("{$errmsg}", C_ERROR);
		print $this->tpl_enableBack();
		die();
	}
}

?>