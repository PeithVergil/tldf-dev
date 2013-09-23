<?php

/**
*
*	dating version OCT_2008_01
*
**/


include "./include/config.php";
include "./common.php";
include "./include/config_index.php";
include "./include/functions_auth.php";
include "./include/functions_index.php";
include "./include/class.phpmailer.php";
include "./include/functions_mail.php";

// authentication
$user = auth_index_user();

if (!$user[ AUTH_ID_USER ]) {
	header("location: ".$config["site_root"]."/index.php");
	exit;
}

// check group, period, expiration
RefreshAccount();

// check status
if (!$user[ AUTH_STATUS ]) {
	AlertPage(GetRightModulePath(__FILE__));
}

// check ins messages if user not guest
if (!$user[ AUTH_GUEST ]) {
	GetAlertsMessage();
	SetModuleStatistic(GetRightModulePath(__FILE__));
}

$smarty->assign('sub_menu_num', '4');

$sel = $_POST['sel'] ? $_POST['sel'] : $_GET['sel'];

switch ($sel) {
	case 'import':
		ImportContacts();
	break;
	case 'send':
		InviteFriends();
	break;
	default:
		InviteFriendsForm();
	break;
}

exit;


function InviteFriendsForm($err="")
{
	global $lang, $config, $smarty, $user;
	
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$file_name = 'invite_friends.php';

	$site_name = $config["server"].$config["site_root"];

	$use_refer_friend_feature = GetSiteSettings("use_refer_friend_feature");
	if ($use_refer_friend_feature && !$user[ AUTH_GUEST ]) {
		$settings = GetSiteSettings(array('refer_friend_price','site_unit_costunit'));
		$search = array('[n]','[currency]');
		$repl = array($settings['refer_friend_price'],$settings['site_unit_costunit']);
		$form["comment"] = str_replace($search, $repl, $lang["refer_a_friend"]["comment_inv_friend"]);
		$code = GetUserReferCode($user[ AUTH_ID_USER ]);
		if (!$code) $code = substr(md5(time()),1,12);
		$form["body"] = str_replace("[link]", $site_name."/tell_friend.php?sel=from_refer&code=".$code, $lang["refer_a_friend"]["body"]);
		$form["hidden"] = "<input type='hidden' name='refer_code' value='".$code."' />";
	}else{
		$form["body"] = str_replace("[link]", $site_name, $lang["invite_friends"]["mail_content"]);
	}

	$form["subj"] = str_replace("[site]", $site_name, $lang["invite_friends"]["mail_subject"]);

	$form["action"] = $file_name;
	$form["hidden"] .= "<input type=hidden name=sel value=import>";

	$smarty->assign("invite_message", $err);

	$smarty->assign("form", $form);
	$smarty->assign("section", $lang["section"]);
	$smarty->assign("header", $lang["invite_friends"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/invite_friends_form.tpl");
	exit;
}

function ImportContacts()
{
	global $lang, $config, $smarty, $user, $cookiepath;
	
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$file_name = "invite_friends.php";
	
//	if ($err = EmailFilter($_POST["i_email"])) {
//		InviteFriendsForm($err);
//		return;
//	}
	$i_email = $_POST["i_email"];
	$i_email_pwd = $_POST["i_email_pwd"];

	$services = array("aol","msn","yahoo","gmail","hotmail");
	if (!in_array($_POST["i_service"], $services)) {
		$err = $lang["invite_friends"]["wrong_service"];
		InviteFriendsForm($err);
		return;
	}
	$i_service = $_POST["i_service"];

	$subj = stripslashes($_POST["subj"]);
	$body = stripslashes($_POST["body"]);
	$refer_code = stripslashes($_POST["refer_code"]);

	$_SESSION["email"] = $i_email;
	$_SESSION["subj"] = $subj;
	$_SESSION["body"] = $body;
	$_SESSION["refer_code"] = $refer_code;

	$cookiepath=$config["site_path"]."/include/importers/cookie/";

	require "./include/importers/".$i_service."importer.php";

	$i_contacts = import_contacts($i_email,$i_email_pwd);
	if(!is_array($i_contacts)){
		$err = $lang["invite_friends"]["failed_import"];
		InviteFriendsForm($err);
		return;
	}

	$contacts = array();
	$c = 0;
	foreach($i_contacts as $contact){
		$contacts[$c]["name"] = htmlspecialchars(@$contact[0],ENT_QUOTES);
		$contacts[$c]["email"] = htmlspecialchars(@$contact[1],ENT_QUOTES);
		$c++;
	}

	$form["action"] = $file_name;
	$form["hidden"] = "<input type=hidden name=sel value=send>";

	$smarty->assign("contacts", $contacts);
	$smarty->assign("count_contacts", count($contacts));
	$smarty->assign("form", $form);
	$smarty->assign("section", $lang["section"]);
	$smarty->assign("header", $lang["invite_friends"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/invite_friends_table.tpl");
	exit;
}

function InviteFriends()
{
	global $lang, $config, $dbconn, $user;
	
	$site_lang = $config["default_lang"];

	$rs = $dbconn->Execute("select fname, sname from ".USERS_TABLE." where id = ".$user[ AUTH_ID_USER ]);
	$name = stripslashes($rs->fields[0]." ".$rs->fields[1]);
	
	$cont_arr["name"] = $name;
	$cont_arr["email"] = $_SESSION["email"];
	$cont_arr["subject"] = $_SESSION["subj"];
	$cont_arr["content"] = $_SESSION["body"];
	$cont_arr["content"] = $_SESSION["body"];
	
	$refer_code = $_SESSION["refer_code"];
	
	unset($_SESSION["email"]);
	unset($_SESSION["subj"]);
	unset($_SESSION["body"]);
	unset($_SESSION["refer_code"]);
	
	$to_emails = $_POST["emails"];
	$counter = 0;

	$use_refer_friend_feature = GetSiteSettings("use_refer_friend_feature") && !$user[ AUTH_GUEST ] && $refer_code;
	if ($use_refer_friend_feature) {
		$strSQL = "SELECT code FROM ".USER_REFER_CODE_TABLE." WHERE id_user='".$user[ AUTH_ID_USER ]."'";
		$code = $dbconn->GetOne($strSQL);
		if (!$code) {
			$strSQL = "INSERT INTO ".USER_REFER_CODE_TABLE." SET id_user='".$user[ AUTH_ID_USER ]."', code='".addslashes($refer_code)."'";
			$dbconn->Execute($strSQL);
			if ($dbconn->ErrorNo()) {
				InviteFriendsForm($lang["err"]["error"]);
			}
		}
	}
	
	foreach ($to_emails as $to_email) {
		if ($use_refer_friend_feature) {
			$email = $dbconn->GetOne("SELECT email FROM ".USERS_TABLE." WHERE email='".$to_email."'");
			if (!$email){
				$mailer_err = SendMail($site_lang, $to_email, $cont_arr["email"], $cont_arr["subject"], $cont_arr,
					"mail_tell_a_friend", null, '', $cont_arr['name'], 'tell_a_friend');
			}
		} else {
			$mailer_err = SendMail($site_lang, $to_email, $cont_arr["email"], $cont_arr["subject"], $cont_arr,
				"mail_tell_a_friend", null, '', $cont_arr['name'], 'tell_a_friend');
		}
		if (!$mailer_err) {
			$counter++;
		}
	}

	if ($use_refer_friend_feature){
		$settings = GetSiteSettings(array('refer_friend_price','site_unit_costunit'));
		$acc_link = "<a href='".$config["server"].$config["site_root"]."/account.php'>".$lang["section"]["account"]."</a>";
		$search = array('[n_friends]', '[n_money]', '[curr]', '[my_acc]');
		$repl = array($counter, $settings['refer_friend_price'], $settings['site_unit_costunit'], $acc_link);
		$err = str_replace($search,$repl,$lang["err"]["refer_email_was_sent"]);
	} else {
		$err = $counter." ".$lang["invite_friends"]["mails_sent"];
	}
	InviteFriendsForm($err);
	return;
}
?>