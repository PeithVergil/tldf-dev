<?php
/**
* Request Info Page
*
* @package DatingPro
* @subpackage User Mode
**/

include './include/config.php';
include './common.php';
include './include/config_index.php';
include './include/functions_auth.php';
include './include/functions_index.php';
include './include/class.lang.php';
include './include/class.phpmailer.php';
include './include/functions_mail.php';

// authentication
$user = auth_index_user();

if (empty($user) || $user == 'err' || empty($user[ AUTH_ID_USER ])) {
	header('location: '.$config['site_root'].'/index.php');
	exit;
}

// check guest
// (public access)

// check group, period, expiration
RefreshAccount();

// check status
// (public access)

// check permissions
// (public access)

// alerts and statistics
if (!$user[ AUTH_GUEST ]) {
	GetAlertsMessage();
	SetModuleStatistic(GetRightModulePath(__FILE__));
}

// active menu item
$smarty->assign('sub_menu_num', '');

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

// dispatcher
switch ($sel) {
	default:
		RequestInfoTable();
	break;
}

exit;


function RequestInfoTable($err = '')
{
	global $config, $smarty, $user;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$file_name = 'request_info.php';
	
	/*
	$msg = isset($_REQUEST['msg']) ? $_REQUEST['msg'] : 0;
	
	if ($msg == 0)
	{
		$data = $_POST;
		
		if($data)
		{
			$name = isset($data['name']) ? FormFilter($data['name']) : '';
			$phone = isset($data['phone']) ? FormFilter($data['phone']) : '';
			$email = isset($data['email']) ? FormFilter($data['email']) : '';
			$address = isset($data['address']) ? FormFilter($data['address']) : '';
			
			if (!strlen($name))
			{
				$err .= $lang['request_info']['name'] . ', ';
				$err_field['name'] = 1;
			}
			if (!strlen($phone))
			{
				$err .= $lang['request_info']['phone'] . ', ';
				$err_field['phone'] = 1;
			}
			if (!strlen($email))
			{
				$err .= $lang['request_info']['email'] . ', ';
				$err_field['email'] = 1;
			}
			if (!strlen($address))
			{
				$err .= $lang['request_info']['address'] . ', ';
				$err_field['address'] = 1;
			}
			
			// email validation
			if ($err .= EmailFilter($email))
			{
				$err_field['email'] = 1;
			}
			
			if ($err)
			{
				$smarty->assign('err_field', $err_field);
				$err = $lang['err']['invalid_fields'] . '<br/><br/>' . trim($err, ', ');
				
				$form['name'] = $data['name'];
				$form['phone'] = $data['phone'];
				$form['email'] = $data['email'];
				$form['address'] = $data['address'];
			}
			else
			{
				if(RequestSend())
				{
					echo '<script>location.href="./request_info.php?msg=2";</script>';
				}
				else
				{
					echo '<script>location.href="./request_info.php?msg=1";</script>';
				}
			}
		}
	}
	else
	{
		if($msg==1)
		{
			$form['res'] = $lang['request_info']['success'];
			$form['success']=1;
		}
		if($msg==2)
		{
			$form['res'] = $lang['request_info']['error'];
		}
	}
	*/
	
	$smarty->assign('user_gender', $user[ AUTH_GENDER ]);
	$form['err'] = $err;
	$form['action'] = $file_name;
	$smarty->assign('form', $form);
	$smarty->display(TrimSlash($config['index_theme_path']).'/request_info_table.tpl');
	exit;
}

/*
function RequestSend()
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user, $lang_mail;
	
	$content_array = $_POST;
	
	$rs = $dbconn->Execute('SELECT lang_file, code, charset FROM '.LANGUAGE_TABLE.' WHERE id='.$config['default_lang']);
	include $config['path_lang'].'mail/'.$rs->fields[0];
	$lang_mail_code = $rs->fields[1];
	$charset_mail = $rs->fields[2];
	$rs->free();
	
	$subject = $lang_mail['request_info_pack_admin']['subject'];
	
	$mail_err = SendMail($config['site_email'], $config['site_email'], $subject, $content_array, 'mail_request_info_pack_admin', null,
		'', '', 'request_info_pack_admin');
	
	return $mail_err;
}
*/
?>