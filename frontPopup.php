<?php

include './include/config.php';
include './common.php';
include './include/config_index.php';
include './include/functions_auth.php';
include './include/functions_index.php';
include './include/class.lang.php';				// registration
include './include/class.percent.php';			// registration
include './include/class.ip_info.php';			// registration
include './include/class.phpmailer.php';		// registration
include './include/functions_mail.php';			// registration
include './include/functions_users.php';		// registration
include './include/functions_newsletter.php';	// registration
include './include/functions_affiliate.php';	// registration

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

IndexHomePage();

$id = $_REQUEST['id'];

if (!empty($_POST)) {
	$err = SavePersonalRegistrationForm();
	
	if ($err) {
		$_SESSION['error'] = $err;
		$_SESSION['data']  = $_POST;
		header('Location: frontPopup.php?id='.$id);
		exit;
	}
	
	// $_POST contains sanitized form input
	$data = $_POST;
	
	// submit to newsletter service
	if ($data['gender'] == GENDER_FEMALE) {
		//Creating array for GetResponse (Lady user)
		$resData['webform_id']			= '38741';
		$resData['name']				= html_entity_decode($data['fname']);
		$resData['custom_FirstName']	= html_entity_decode($data['fname']);
		$resData['custom_LastName']		= html_entity_decode($data['sname']);
		$resData['email']				= html_entity_decode($data['email']);
	} else {
		//Creating array for GetResponse (Guy user)
		$resData['webform_id']			= '41168';
		$resData['name']				= html_entity_decode($data['fname']);
		//$resData['custom_FirstName']	= html_entity_decode($data['fname']);
		$resData['custom_LastName']		= html_entity_decode($data['sname']);
		$resData['email']				= html_entity_decode($data['email']);
	}
	
	submitToGetResponse($resData);
	
	echo '<script>top.location.href="./myprofile.php";</script>';
	exit;
}

//RS signup form start
$err = '';

if (isset($_SESSION['error'])) {
	$err		= $_SESSION['error'];
	$err_field	= $_SESSION['err_field'];
	$_POST		= $_SESSION['data'];
	unset($_SESSION['error']);
	unset($_SESSION['err_field']);
	unset($_SESSION['data']);
	$smarty->assign('err_field', $err_field);
}

$form = PersonalRegistrationForm();

$form['err'] = $err;
//RS signup form end

$form['register_link'] = './index.php';
$form['login_link'] = './index.php?sel=login';
$form['lost_passw_link'] = './lost_pass.php';
$form['user_id'] = $id;

$smarty->assign('form', $form);

// Landing Featured users

$settings = GetSiteSettings(array('icons_folder', 'icon_male_default', 'icon_female_default'));

$strSQL = 'SELECT big_icon_path, gender, fname, sname, gender, id_nationality, couple FROM  '.USERS_TABLE.' WHERE id = ?';

$rs = $dbconn->query($strSQL, array($id));

$icon_path = $rs->fields[0];
$data['gender'] = $rs->fields[1];
$data['fname'] = $rs->fields[2];
$data['sname'] = $rs->fields[3];
$data['gender'] =$rs->fields[4];
$id_nationality =$rs->fields[5];
$data['couple'] =$rs->fields[6];
// multi-lang tables
	$multi_lang = new MultiLang();
	$field_name = $multi_lang->DefaultFieldName();
if (strlen(trim($id_nationality))>0 && trim($id_nationality) != '0')
	{
		$strSQL_nation =
			"Select b.".$field_name." as nation
			   from ".NATION_SPR_TABLE." a
		  left join ".REFERENCE_LANG_TABLE." b on b.id_reference = a.id and b.table_key = '".$multi_lang->TableKey(NATION_SPR_TABLE)."'
			  where a.id in (".$id_nationality.")
		   order by nation";
		$nation_arr = array();
		$rs = $dbconn->Execute($strSQL_nation);
		while (!$rs->EOF) {
			$row = $rs->GetRowAssoc(false);
			array_push($nation_arr, $row['nation']);
			$rs->MoveNext();
		}
		$data['nationality_match'] = implode(',<br>', $nation_arr);
	}
	elseif (trim($id_nationality) == '0')
	{
		$data['nationality_match'] = $lang['button']['not_important'];
	}

//big_thumb_839_961d5559.jpg  -miss

$icon_path = str_replace('big_thumb_', '', $icon_path); //omiting out the big_thum_ from file name to link original image file

if (!empty($icon_path) && file_exists($config['site_path'].$settings['icons_folder'].'/'.$icon_path)) {
	$icon_path = $config['site_root'].$settings['icons_folder'].'/'.$icon_path;
} else {
	if ($data['gender'] == 1) {
		$icon_path = $config['site_root'].$settings['icons_folder'].'/'.$settings['icon_male_default'];
	} elseif ($data['gender'] == 2) {
		$icon_path = $config['site_root'].$settings['icons_folder'].'/'.$settings['icon_female_default'];
	}
}
$smarty->assign('data1', $data);
$smarty->assign('icon_path', $icon_path);
$smarty->display(TrimSlash($config['index_theme_path']).'/frontPopup.tpl');

