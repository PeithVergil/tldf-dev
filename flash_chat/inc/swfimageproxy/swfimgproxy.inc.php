<?php

class GetWebObject
{
	var $host  = "";
	var $port  = "";
	var $path   = "";
	var $username  = "";
	var $password   = "";
	var $referer="";
	var $header = array();
	var $content = "";
	var $error="";
	
	function GetWebObject($host="", $port=80, $path="",$header_only=false)
	{
		$this->host = $host;
		$this->port = $port;
		$this->path = $path;
		$this->header_only = $header_only;
	}
	
	function fetch()
	{
		$fp = @fsockopen ($this->host, $this->port);
		
		if(!$fp){ 
			$this->error="Could not connect to host.";
			return false;
		}
		
		$header_done=false;
		
		$request = "GET ".$this->path." HTTP/1.1\r\n";
		$request .= "Referer: http://".$this->host.$this->path."\r\n";
		$request .= "Accept-Encoding: gzip, deflate\r\n";
		$request .= "User-Agent: swfImageProxy/0.1\r\n";
		$request .= "Host: ".$this->host."\r\n";
		$request .= "Connection: Close\r\n";
		
		$request .="\r\n";
		$return = '';
		
		fputs ($fp, $request);
		
		$line = fgets ($fp, 128);
		$this->header["status"] = $line;
		
		$break=false;
		while ((!feof($fp)) && (!$break))
		{
			if($header_done){
				if ($this->header["transfer-encoding"]=="chunked"){
					$chunkSize =hexdec(fgets($fp, 128));
					$line = fread ( $fp,$chunkSize  );
					$this->content .= $line;
					$garbage=fgets($fp, 2);
				} else {
					if (isset($this->header["content-length"])){
						$chunkSize=$this->header["content-length"];
					} else {
						$chunkSize=1024;
					}
					$line = fread ( $fp,$chunkSize  );
					$this->content .= $line;
				}
			} else {
				$line = fgets ($fp, 128);
				if($line == "\r\n"){ 
					$header_done=true;
					if ($this->header_only==true) $break=true;
				} else {
					$data = explode(": ",$line);
					$this->header[strtolower($data[0])] = trim($data[1]);
				}
			}
		}
		fclose ($fp);
		return true;
	}
	
	
	function get_header(){ 
		return($this->header);
	}
	
	function get_content(){ 
		if ($this->header_only){
			$this->header_only=false;
			if (!$this->fetch()){
			 	return false;	
			}	
		}
		return($this->content);
	}
	
	function get_error(){ 
		return($this->error);
	}
}

function parseURL($url){
	$url=trim($url);
	if (substr(strtolower($url),0,7)!="http://") $url="http://".$url;
	
	if ($currenturl!="http://"){
		$p_url=parse_url($url);
		
		$subpath=explode("/",$p_url["path"]);
		$last=count($subpath)-1;
		if (stristr($subpath[$last],".")!=false){
			$last--;
		}
		
		$baseURL="http://".$p_url["host"]."/";
		for ($i=1;$i<$last+1;$i++){
			$baseURL.=$subpath[$i]."/";
		}
		
		if (substr($baseURL,-2)=="//"){
			$baseURL=substr( $baseURL,0,strlen($baseURL)-1 );
		}
		
	}
}

function createTempImgfile($data){
	list($usec, $sec) = explode(" ",microtime()); 
	$imgname="p".trim($sec).trim(str_replace(".","",$usec));

	$fp=fopen($imgname,"w");
	fputs($fp,$data);
	fclose($fp);
	
	return $imgname;
}

function returnError($errorString=""){
	header("Content-type: application/x-shockwave-flash");
	$fp=fopen("404.swf","rb"); 
	fpassthru($fp); 
	fclose($fp);
	die();
}

function returnJPEG($im,$jpegQuality=75){
	global $imgname;
	if (!$im){
		unlink($imgname);
		returnError("GD Error: Could not create JPEG file");	
	}
	
	header("Content-type: img/jpeg");
	@imageinterlace ( $im, 0);
	@imagejpeg($im,"",$jpegQuality);
	@imagedestroy($im);
	unlink($imgname);
}

?>