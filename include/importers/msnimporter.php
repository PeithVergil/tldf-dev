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
$ch=curl_init();
curl_setopt($ch,CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1) Gecko/20061010 Firefox/2.0");
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_COOKIEJAR,$cookiepath);
curl_setopt($ch,CURLOPT_COOKIEFILE,$cookiepath);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($ch,CURLOPT_HEADER,$header);
curl_setopt($ch,CURLOPT_FOLLOWLOCATION,$follow);
if($post){curl_setopt($ch, CURLOPT_POST,1); curl_setopt($ch,CURLOPT_POSTFIELDS,$post);}
$returned=curl_exec($ch);
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
if(!strpos($email,"@")){$email.="@msn.com";}
$emailtmp=$email;

/*Get email domain*/
preg_match('/.*\@([^\@]*)/',$email,$getdomain);
$getdomain=strtolower(trim(@$getdomain[1]));

$xreturn=curlsetopt("http://login.live.com/login.srf?id=2&svc=mail&cbid=24325&msppjph=1&tw=0&fs=1&fsa=1&fsat=1296000&lc=1033","PPStateXfer=1",1,0);

/*Get Form Hidden Inputs*/
$inputs=conv_hiddens($xreturn);

/*Get Form POST action page*/
preg_match('/<form[^>]+action\="([^"]*)"[^>]*>/',$xreturn,$getlink);

$xreturn=curlsetopt(@$getlink[1],"login=".urlencode($email)."&passwd=".urlencode($password)."&LoginOptions=2".conv_hiddens2txt($inputs),1,0);

preg_match('/content="0;\sURL\=([^"]*)"/',$xreturn,$geturl);
if(@$geturl[1]) $xreturn=curlsetopt(@$geturl[1],0,1);

preg_match('/window\.location\.replace\("([^"]*)"\)\;/',$xreturn,$getlink);

if(@$getlink[1])
$xreturn=curlsetopt(@$getlink[1],"",1,0,1);
else
$xreturn=curlsetopt("http://mail.live.com/default.aspx?wa=wsignin1.0","",1,0,1);

preg_match('/^(http:\/\/[^\.]*w\.[^\.]*\.mail\.live\.com\/)mail\/.*/mi',$curlstatus['url'],$checklinklive);
preg_match('/Set-Cookie:\smt=([^;\s]*);/',$xreturn,$getmt);

if(@$checklinklive[1]){
/*If MSN Live*/

$xreturn=curlsetopt(@$checklinklive[1]."mail/options.aspx?subsection=26","",1,0,1);
$inputs=conv_hiddens($xreturn);

$xreturn=curlsetopt(@$checklinklive[1]."mail/options.aspx?subsection=26","mt=".urlencode(@$getmt[1])."&ctl02%24ExportButton=Export+contacts".conv_hiddens2txt($inputs),1,0);

$xreturn.='
';
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
}
if(substr($record[1],$i,1)=="\""&&substr($record[1],$i,2)=="\"\""){$skip=1;}else{$skip=0;}

if($stat==0&&(substr($record[1],$i,1)==","||substr($record[1],$i,1)==";"||substr($record[1],$i,1)=="")){
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

$getary=array("Title","First Name","Middle Name","Last Name","E-mail Address");
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

$getnamea=@$contact[@$returnary[1]];
$getnameb=@$contact[@$returnary[2]];
$getnamec=@$contact[@$returnary[3]];

$email=@$contact[@$returnary[4]];

$getname=@$contact[@$returnary[0]];

if($getnamec||$getnamea||$getnameb){
$getname=null;
if($getnamec){$getname.=$getnamec;}
if($getnamea){if($getname==null){$getname.=$getnamea;}else{$getname.=", ".$getnamea;}}
if($getnameb){if($getname==null){$getname.=$getnameb;}else{$getname.=", ".$getnameb;}}
}

$contact=array($getname,$email);

/*Filter out blank email and invalid email address !important*/
if(@$contact[1]&&validateemail(@$contact[1])){array_push($tmp,$contact);}}
$contactemails=$tmp;

unlinkcookie();
return $contactemails;
}


//if Account is not MSN Accounts
unlinkcookie();
return false;
}


function isexist($ary,$dt){
foreach($ary as $scont){if(@$scont[1]==$dt){return true;}}
return false;
}


function conv_hiddens($html){
preg_match_all('|<input[^>]+type="hidden"[^>]+name\="([^"]+)"[^>]+value\="([^"]*)"[^>]*>|',$html,$getinputs,PREG_SET_ORDER);
return $getinputs;
}


function conv_hiddens2txt($getinputs){
$ac=null;
foreach($getinputs as $eachinput){if(@$eachinput[2]) $ac.="&".urlencode(html_entity_decode(@$eachinput[1]))."=".urlencode(html_entity_decode(@$eachinput[2]));}
return $ac;
}
?>