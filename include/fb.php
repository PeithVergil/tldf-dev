<?php

require 'facebook/facebook.php';

$facebook = new Facebook(array(
  'appId'  => '298096403662644',
  'secret' => 'b8d008257aee496c9e2352c36f51a43c'
));

$params = array(
'display' => 'popup',
  'scope' => 'email ,user_birthday',
  'redirect_uri' => 'http://www.dev.thailadydatefinder.com/index.php?sel=fb_login'
);

// See if there is a user from a cookie

$token = $facebook->getAccessToken();

$fbuser = $facebook->getUser();


$login_url  = $facebook->getLoginUrl($params);


$logout_url = $facebook->getLogoutUrl(array(
		'next'	=> 'http://www.dev.thailadydatefinder.com/index.php?sel=logoff',
		'access_token'=>$token
		));


if ($fbuser) {
  	
	try {
    // Proceed knowing you have a logged in user who's authenticated.
    	$user_profile = $facebook->api('/me');
		
  	} 
  	
	catch (FacebookApiException $e) {
    	$fbuser = null;
  	}
}

?>