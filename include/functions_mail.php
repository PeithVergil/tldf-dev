<?php

/**
* Mail Functions (send e-mail and make e-mail content)
*
* @package DatingPro
* @subpackage Include files
* @modified Ralf Strehle
**/

function SendMail($id_lang, $email_to, $email_from, $subject, $content_array, $content_file, $embedded='',
	$name_to='', $name_from='', $lang_var='', $gender='')
{
	global $lang, $config, $dbconn, $smarty;
	
	$err = '';
	
	$server['url']		= $config['server'];
	$server['root']		= $config['server'].$config['site_root'];
	$server['img_root']	= $config['server'].$config['site_root'].$config['index_theme_path'].'/images';
	
	$content_array['server'] = $server;
	
	$rs = $dbconn->Execute('SELECT lang_file, code, charset FROM '.LANGUAGE_TABLE.' WHERE id = ?', array($id_lang));
	$lang_file = $rs->fields[0];
	$code = $rs->fields[1];
	$charset = $rs->fields[2];
	$rs->free();
	
	$PHPmailer = new PHPMailer();
	
	$PHPmailer->CharSet = $charset;
	$PHPmailer->From = $email_from;
	$PHPmailer->FromName = $name_from ? $name_from : $email_from;
	$PHPmailer->Subject = $subject;
	$PHPmailer->AddAddress($email_to, $name_to);
	$PHPmailer->IsHTML(true);
	
	// make content
	$lang_mail = array();
	
	include $config['path_lang'].'mail/'.$lang_file;
	
	if ($gender == 1) {
		$smarty->assign('mail_main', $lang_mail[ $lang_var.'_e' ]);
		$smarty->assign('mail_generic', $lang_mail['generic_e']);
	} elseif ($gender == 2) {
		$smarty->assign('mail_main', $lang_mail[ $lang_var.'_t' ]);
		$smarty->assign('mail_generic', $lang_mail['generic_t']);
	} else {
		$smarty->assign('mail_main', $lang_mail[ $lang_var ]);
		$smarty->assign('mail_generic', $lang_mail['generic_e']);
	}
	
	$smarty->assign('data', $content_array);
	$smarty->assign('gender', $gender);
	$smarty->assign('mail_lang', $code);
	$smarty->assign('mail_charset', $charset);
	$smarty->assign('header', $lang_mail); // old emails
	$smarty->assign('mail_css', $smarty->fetch(TrimSlash($config['index_theme_path']).'/css/mail.css'));
	
	$content = $smarty->fetch(TrimSlash($config['index_theme_path']).'/'.$content_file.'.tpl');
	$content = str_replace('[site]', $config['server'].$config['site_root'], $content);
	$content = str_replace('[site_name]', $lang['site_name'], $content);
	
	$PHPmailer->Body = $content;
	
	// embed images
	if (is_array($embedded) && count($embedded['id']) > 0) {
		for ($i = 0; $i < count($embedded['id']); $i++) {
			$PHPmailer->AddEmbeddedImage($embedded['image_path'][$i], 'agent'.$embedded['id'][$i], $embedded['image_name'][$i], 'base64', $embedded['image_type'][$i]);
		}
	}
	
	if ($content != '')
	{
		if (isset($config['dumpmail']) && (int)$config['dumpmail'] == 1)
		{
			switch ($gender) {
				case GENDER_MALE: $suffix = '_e'; break;
				case GENDER_FEMALE: $suffix = '_t'; break;
				default: $suffix = '';
			}
			$fd = fopen(dirname(__FILE__).'/@email-'.(string)(10000*microtime(true)).'-'.$lang_var.$suffix.'.html', 'wb');
			fwrite($fd, $content);
			fwrite($fd, 'from: '.$email_from.'<br>');
			fwrite($fd, 'to: '.$email_to.'<br>');
			fclose($fd);
		}
		elseif (!$PHPmailer->Send())
		{
			$err = $lang['err']['mail_error'].' ('.$PHPmailer->ErrorInfo.')';
		}
	}
	
	sleep(1);
	
	$PHPmailer->ClearAddresses();
	$PHPmailer->ClearAttachments();
	
	return $err;
}


function GetUserEmailLinks()
{
	global $config;
	
	$urls = array();
	
	$root = $config['server'].$config['site_root'];
	
	$urls['request_info']		= $root.'/request_info.php';
	$urls['contact']			= $root.'/contact.php';
	$urls['login']				= $root.'/index.php?sel=login';
	$urls['registration']		= $root.'/index.php';
	$urls['platinum']			= $root.'/platinum_match.php';
	$urls['dating_events']		= $root.'/dating_events.php';
	$urls['terminate']			= $root.'/account.php';
	$urls['help']				= $root.'/help.php';
	$urls['help_trial_member']	= $root.'/help.php?sel=list_item&id=42';
	$urls['help_reg_member']	= $root.'/help.php?sel=list_item&id=42';
	$urls['help_plat_member']	= $root.'/help.php?sel=list_item&id=42';
	$urls['help_tld_event']		= $root.'/help.php?sel=list_item&id=42';
	$urls['help_newsletter']	= $root.'/help.php?sel=list_item&id=42';
	
	return $urls;
}
?>