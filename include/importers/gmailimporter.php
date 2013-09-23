<?php
set_time_limit(0);

function validateemail($email){
	if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {return false;}else{return true;}
}


function unlinkcookie(){
	global $cookiepath;
	/*Remove Cookie File After Session !important*/
	global $cookiepath; @unlink($cookiepath);
	return;
}


function curlsetopt($url,$post="",$follow=1,$debugmode=0,$header=0){
	global $curlstatus,$cookiepath;
	$follow = 0;
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_USERAGENT, "XGContacts Importer v2.0");
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_COOKIEJAR,$cookiepath);
	curl_setopt($ch,CURLOPT_COOKIEFILE,$cookiepath);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
	curl_setopt($ch,CURLOPT_HEADER,$header);
//	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,$follow);
	if($post){curl_setopt($ch, CURLOPT_POST,1); curl_setopt($ch,CURLOPT_POSTFIELDS,$post);}
	$returned=curl_redir_exec($ch);
//	$returned=curl_exec($ch);
	$curlstatus=curl_getinfo($ch);
	curl_close($ch);

	if($debugmode){echo "<br/>==========================================================================================<br/><b>Calling URL:</b> ".htmlspecialchars($url,ENT_QUOTES)."<br/><b>Cookie Path:</b> ".htmlspecialchars($cookiepath,ENT_QUOTES)."<br/>==========================================================================================<br/>".htmlspecialchars($returned,ENT_QUOTES)."<br/><br/>==========================================================================================<br/><br/>"; exit;}
	return $returned;
}

function import_contacts($email,$password){
	global $curlstatus,$cookiepath,$emailtmp;

	$ct=0;
	while(file_exists($cookiepath."xgcurlcookie".$ct.".xgc")===true){$ct++;}
	$cookiepath.="xgcurlcookie".$ct.".xgc";

	//Automatically inject @domain.com into email if @ is not detected
	if(!strpos($email,"@")){$email.="@gmail.com";}
	$emailtmp=$email;

	/*Get email domain*/
	preg_match('/.*\@([^\@]*)/',$email,$getdomain);
	$getdomain=strtolower(trim(@$getdomain[1]));

	$xreturn=curlsetopt("http://mail.google.com/mail/",0,1,0);

	/*Get Form Hidden Inputs*/
	$inputs=conv_hiddens($xreturn);

	/*Get Form POST action page*/
	/*Note that the link returned is a relative link not absolute link*/
	preg_match('/<form[^>]+action\="([^"]*)"[^>]*>/',$xreturn,$getlink);

	$xreturn=curlsetopt(@$getlink[1],"Email=".urlencode($email)."&Passwd=".urlencode($password).conv_hiddens2txt($inputs),1,0);

	/*Get Javascript Redirection Link !optional -not used*/
	preg_match('/url=\'([^\']*)\'/',$xreturn,$getlink);

	if(strncmp(@$curlstatus['url'],"https://www.google.com/accounts/ServiceLoginAuth?service=mail",61)){

		$xreturn=curlsetopt("http://mail.google.com/mail/?ui=2",0,1,0);

		$xreturn=curlsetopt("http://mail.google.com/mail/contacts/data/export?exportType=ALL&groupToExport=&out=OUTLOOK_CSV",0,1,0);

		/*Match new lines*/
		preg_match_all('|([^\n]*)\n|',$xreturn,$records,PREG_SET_ORDER);

		$tmp=array(); $newfields=array();
		foreach($records as $record){

			$currentrecord=count($newfields);
			$newfields[$currentrecord]=array();

			$stat=0; $i=0; $storetmp=null; $skip=0;
			while($i<=strlen($record[1])){

				if($skip==0){
					if(substr($record[1],$i,1)=="\""&&substr($record[1],$i,2)!="\"\""){
						if($stat==1){$stat=0;}else{$stat=1;}
					}elseif(substr($record[1],$i,1)=="\""&&substr($record[1],$i,2)=="\"\""){$skip=1;}else{$skip=0;}

					if($stat==0&&(substr($record[1],$i,1)==","||substr($record[1],$i,1)=="")){
						$storetmp=trim($storetmp);
						if(substr($storetmp,0,1)=="\""&&substr($storetmp,strlen($storetmp)-1,1)=="\""){
							$storetmp=substr($storetmp,1,strlen($storetmp)-2); //strip the limit quotes off
						}
						array_push($newfields[$currentrecord],$storetmp); $storetmp=null;
					}else{$storetmp.=substr($record[1],$i,1);}

				}else{$skip=0;}

				$i++;
			}
			//end while
		}
		//end foreach

		$getary=array("Name","E-mail Address");
		$returnary=array();

		$i=0;
		while($i<count($newfields[0])){

			$ib=0;
			while($ib<count($getary)){
				if($newfields[0][$i]==$getary[$ib]){$returnary[$ib]=$i;}
				$ib++;
			}

			$i++;
		}

		/*Cancel out the first line (CSV Header)*/
		array_shift($newfields);

		$tmp=array();
		foreach($newfields as $contact){

			$email=@$contact[@$returnary[1]];

			$getname=@$contact[@$returnary[0]];

			$contact=array($getname,$email);

			/*Filter out blank email and invalid email address !important*/
			if(@$contact[1]&&validateemail(@$contact[1])){array_push($tmp,$contact);}}
			$contactemails=$tmp;

			unlinkcookie();
			return $contactemails;
	}

	//if Account is not valid account
	unlinkcookie();
	return false;
}


function conv_hiddens($html){
	preg_match_all('|<input[^>]+type="hidden"[^>]+name\="([^"]+)"[^>]+value\="([^"]*)"[^>]*>|',$html,$getinputs,PREG_SET_ORDER);
	return $getinputs;
}


function conv_hiddens2txt($getinputs){
	$ac=null;
	foreach($getinputs as $eachinput){$ac.="&".urlencode(html_entity_decode(@$eachinput[1]))."=".urlencode(html_entity_decode(@$eachinput[2]));}
	return $ac;
}

function curl_redir_exec($ch,$debug="") // Right Function
{
	static $curl_loops = 0;
	static $curl_max_loops = 20;

	if ($curl_loops++ >= $curl_max_loops)
	{
		$curl_loops = 0;
		return FALSE;
	}
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$data = curl_exec($ch);
	$debbbb = $data;
	@list($header, $data) = explode("\n\n", $data, 2);
	@$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	if ($http_code == 301 || $http_code == 302) {
		$matches = array();
		preg_match('/Location:(.*?)\n/', $header, $matches);
		$url = @parse_url(trim(array_pop($matches)));
		//print_r($url);
		if (!$url)
		{
			//couldn't process the url to redirect to
			$curl_loops = 0;
			return $data;
		}
		$last_url = parse_url(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
		/*    if (!$url['scheme'])
		$url['scheme'] = $last_url['scheme'];
		if (!$url['host'])
		$url['host'] = $last_url['host'];
		if (!$url['path'])
		$url['path'] = $last_url['path'];*/
		@$new_url = $url['scheme'] . '://' . $url['host'] . $url['path'] . ($url['query']?'?'.$url['query']:'');
		curl_setopt($ch, CURLOPT_URL, $new_url);
		//    debug('Redirecting to', $new_url);

		return curl_redir_exec($ch);
	} else {
		$curl_loops=0;
		curl_setopt($ch, CURLOPT_HEADER, false);

		$data = curl_exec($ch);
		return $data;
	}
}
?>