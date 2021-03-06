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
curl_setopt($ch,CURLOPT_USERAGENT, "XGContacts Importer v2.0");
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
if(!strpos($email,"@")){$email.="@yahoo.com";}
$emailtmp=$email;

/*Get email domain*/
preg_match('/.*\@([^\@]*)/',$email,$getdomain);
$getdomain=strtolower(trim(@$getdomain[1]));

$xreturn=curlsetopt("https://login.yahoo.com/config/login?","login=".urlencode($email)."&passwd=".urlencode($password)."&.done=http%3a//mail.yahoo.com",1,0);

$xreturn=curlsetopt("https://login.yahoo.com/config/verify?.done=http%3a//mail.yahoo.com",0,1,0);

$xreturn=curlsetopt("http://address.yahoo.com/yab/us?A=B",0,1,0);

if(!strncmp(@$curlstatus['url'],"http://address.yahoo.com/yab/us?",32)){

$inputs=conv_hiddens($xreturn);

$xreturn=curlsetopt("http://address.yahoo.com/index.php","submit[action_export_yahoo]=Export+Now".conv_hiddens2txt($inputs),1,0);

/*Match the first eight fields and get six fields*/
preg_match_all('|"([^"]*)","([^"]*)","([^"]*)","([^"]*)","([^"]*)","[^"]*","[^"]*","([^"]*)"[^\n]*\n|',$xreturn,$contactemails,PREG_SET_ORDER);

/*Cancel out the first line (CSV Header)*/
array_shift($contactemails);

$tmp=array();
foreach($contactemails as $contact){

$getnamea=@$contact[1];
$getnameb=@$contact[2];
$getnamec=@$contact[3];

$getemaila=@$contact[6];
$getemailb=@$contact[5];

$getname=@$contact[4];

if($getnamec||$getnamea||$getnameb){
$getname=null;
if($getnamec){$getname.=$getnamec;}
if($getnamea){if($getname==null){$getname.=$getnamea;}else{$getname.=", ".$getnamea;}}
if($getnameb){if($getname==null){$getname.=$getnameb;}else{$getname.=", ".$getnameb;}}
}

$email=null;
if($getemaila){$email=$getemaila."@".$getdomain;}
if($getemailb){$email=$getemailb;}

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
?>