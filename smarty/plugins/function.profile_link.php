<?php

/*
	NAME			: function.profile_link.php
	PARAMETERS		: userid, username
	DESCRIPTION		: smarty plugin to generate an anchor tag for displaying a profile
	INSTALLATION	  copy this file to /libs/Smarty/plugins
	USAGE			  usage in template file: {profile_link userid=### username=### class=###}
	AUTHOR			: Ralf Strehle (ralf dot strehle at yahoo dot de)
	COMPATIBILITY	: osDate 2.1.x
	COPYRIGHT		: Ralf Strehle IT Training, Consulting and Development (ralf.strehle@yahoo.de)
*/

function smarty_function_profile_link($params, &$smarty)
{
	global $config, $use_profilepopups;
	
	$ret = '<a href="' . DOC_ROOT;				// .DOC_ROOT; not good to add DOC_ROOT, because of admin area
	
	if (!empty($_SESSION['AdminId']))
	{
		$ret .= 'admin/';
	}
	
	if ($config['enable_mod_rewrite'] == 'Y')
	{
		$ret .= ($config['seo_username'] == 'Y') ? $params['username'].'.html' : $params['userid'].'.html';
	}
	else
	{
		$ret .= ($config['seo_username'] == 'Y') ? 'showprofile.php?username='.$params['username'] : 'showprofile.php?id='.$params['userid'];
	}
	
	$ret .= '"';
	
	if (!empty($params['class']))
	{
		$ret .= ' class="'.$params['class'].'"';
	}
	
	if ($use_profilepopups == 'Y')
	{
		$ret .= ' onclick="return popUpScrollWindow2(this.href,\'center\',800,600,\''.$params['userid'].'\');"';
	}
	
	$ret .= '>';
	
	return $ret;
}

?>
