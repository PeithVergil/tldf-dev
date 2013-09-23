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
if(!strpos($email,"@")){$emailtmp=$email."@aol.com";}else{$emailtmp=$email;}

/*Get email domain*/
//preg_match('/.*\@([^\@]*)/',$email,$getdomain);
//$getdomain=strtolower(trim(@$getdomain[1]));

$xreturn=curlsetopt("https://my.screenname.aol.com/_cqr/login/login.psp?sitedomain=sns.webmail.aol.com&lang=en&locale=us&authLev=0&siteState=ver%3a3|rt%3aSTANDARD|ac%3aWS|at%3aSNS|ld%3awebmail.aol.com|uv%3aAOL|lc%3aen-us|mt%3aAOL|snt%3aScreenName&offerId=webmail-en-us&seamless=novl",0,1,0);

/*Get Form Hidden Inputs*/
$inputs=conv_hiddens($xreturn);

/*Get Form POST action page*/
/*Note that the link returned is a relative link not absolute link*/
preg_match('/<form[^>]+action\="([^"]*)"[^>]*>/',$xreturn,$getlink);

preg_match('/<input type="hidden" name="usrd" value="([^"]*)">/',$xreturn,$getusr);

$xreturn=curlsetopt(@$getlink[1],"sitedomain=sns.webmail.aol.com&siteId=&lang=en&locale=us&authLev=0&siteState=ver%253A3%257Crt%253ASTANDARD%257Cac%253AWS%257Cat%253ASNS%257Cld%253Awebmail.aol.com%257Cuv%253AAOL%257Clc%253Aen-us%257Cmt%253AAOL%257Csnt%253AScreenName&isSiteStateEncoded=true&mcState=initialized&uitype=std&use_aam=0&_sns_fg_color_=&_sns_err_color_=&_sns_link_color_=&_sns_width_=&_sns_height_=&_sns_bg_color_=&offerId=webmail-en-us&seamless=novl&regPromoCode=&idType=SN&usrd=".@$getusr[1]."&loginId=".urlencode($email)."&password=".urlencode($password),1,0);

preg_match('/&mcAuth=([^\'&]*)[&|\']/',$xreturn,$getmcauth);

$xreturn=curlsetopt("http://webmail.aol.com/_cqr/LoginSuccess.aspx?sitedomain=sns.webmail.aol.com&authLev=2&siteState=&lang=en&locale=us&uitype=std&mcAuth=".@$getmcauth[1],0,1,0,1);

preg_match('/var gSuccessPath = "\/([^\/"]*)\/[^"]*"/',$xreturn,$getversion);
preg_match('/&uid:([^&\;]*)[&|\;]/',$xreturn,$getuid);

preg_match('/var gHostCheckPath = "\/([^"&]*)[^"]*"/',$xreturn,$gethostcheck);

if(@$getuid[1]){

$xreturn=curlsetopt("http://webmail.aol.com/".@$getversion[1]."/aim/en-us/Default.aspx?rp=Lite%2fToday.aspx",0,1,0);
$xreturn=curlsetopt("http://webmail.aol.com/".@$getversion[1]."/aim/en-us/Lite/ContactList.aspx?folder=Inbox&showUserFolders=False",0,1,0);

preg_match('|<input type=hidden name="user" value="([^"]*)">|',$xreturn,$getuserid);

$xreturn=curlsetopt("http://webmail.aol.com/".@$getversion[1]."/aim/en-us/Lite/addresslist-print.aspx?command=all&sort=FirstLastNick&sortDir=Ascending&nameFormat=FirstLastNick&user=".urlencode(@$getuserid[1]),0,1,0);

/*Get the Mapping of the contact table cells*/
$fpos=strpos($xreturn,"<tr><td colspan=\"4\"><span class=\"fullName\">");
$spos=strrpos($xreturn,"<tr><td colspan=\"4\"><hr class=\"contactSeparator\"></td></tr>");

/*Focus on the important area*/
$xreturn=substr($xreturn,$fpos,$spos-$fpos+59);

$contactemails=explode("<tr><td colspan=\"4\"><hr class=\"contactSeparator\"></td></tr>",$xreturn);

$tmp=array();
foreach($contactemails as $contact){

preg_match('/<span class="fullName">([^<>]*)\s<i>([^<>]*)<\/i><\/span>/',$contact,$getnames);
$getname=html_entity_decode(trim(@$getnames[1]." ".@$getnames[2]));

if (!$getnames) {
preg_match('/<span class="fullName">([^<>]*)\s<\/span>/',$contact,$getnames);
$getname=html_entity_decode(@$getnames[1]);
}

preg_match('/<span>Screen Name:<\/span> <span>([^<>]*)<\/span>/',$contact,$getsname);
$getsname=html_entity_decode(@$getsname[1]);

preg_match('/<span>Email 1:<\/span> <span>([^<>]*)<\/span>/',$contact,$getemaila);
$getemaila=html_entity_decode(@$getemaila[1]);

preg_match('/<span>Email 2:<\/span> <span>([^<>]*)<\/span>/',$contact,$getemailb);
$getemailb=html_entity_decode(@$getemailb[1]);

$email=null;
if($getemailb){$email=$getemailb;}
if($getemaila){$email=$getemaila;}
if($getsname){
if (!$getname) $getname=$getsname;
if (!$email) $email=$getsname;
}

if ($email && !validateemail($email)) $email=$email."@aol.com";

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