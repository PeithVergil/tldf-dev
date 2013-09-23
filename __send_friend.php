<?php
/**
* Send message to a friend interface and functions
*
* @package DatingPro
* @subpackage User Mode
**/
include "./include/config.php";
include "./common.php";
include "./include/config_index.php";
include "./include/functions_auth.php";
include "./include/functions_index.php";
include "./include/class.phpmailer.php";
include "./include/functions_mail.php";
include "./include/class.lang.php";

$user = auth_index_user();

if (!$user[ AUTH_ID_USER ]) {
	header("location: ".$config["site_root"]."/index.php");
	exit;
}

RefreshAccount();

$smarty->assign("sub_menu_num", '2');

if (!$user[ AUTH_STATUS ]) {
	AlertPage(GetRightModulePath(__FILE__));
}

// check ins messages if user not guest
if (!$user[ AUTH_GUEST]) {
	GetAlertsMessage();
}

$sel = isset($_POST["sel"]) ? $_POST["sel"] : (isset($_GET["sel"]) ? $_GET["sel"] : "");

switch ($sel) {
	case "send": 	SendFriendForm(); break;
	case "sendto": 	SendFriend(); break;
	default: 	SearchForm();
}

///  this function is used in files: advanced_search.php, blog.php. hotlist.php, kises.php
///  meet_me.php, meet_them.php, online.php, perfect_match.php,  quick_search.php, visit_my_page.php, viewprifile.php
///  preveusly this function was defined in all that files
function SendFriendForm($err = "")
{
	global $lang, $config, $smarty, $user;

	$file_name = "send_friend.php";
	
	$id_user = intval($_REQUEST["id_user"]);
	$site_name = $config["server"].$config["site_root"];

	IndexHomePage();
	$link = $site_name.$config["site_root"]."/viewprofile.php?id=".$id_user;

	$use_refer_friend_feature = GetSiteSettings("use_refer_friend_feature") && !$user[ AUTH_GUEST ];
	if ($use_refer_friend_feature){
		$settings = GetSiteSettings(array('refer_friend_price','site_unit_costunit'));
		$search = array('[n]','[currency]');
		$repl = array($settings['refer_friend_price'],$settings['site_unit_costunit']);
		$form["comment"] = str_replace($search, $repl, $lang["refer_a_friend"]["comment"]);
		$code = GetUserReferCode($user[ AUTH_ID_USER ]);
		if (!$code) $code = substr(md5(time()),1,12);
		$form["body"] = str_replace("[link]", $site_name."/tell_friend.php?sel=from_refer&code=".$code."&id_profile=".$id_user, $lang["refer_a_friend"]["body"]);
		$form["hidden"] = "<input type='hidden' name='refer_code' value='".$code."' />";
	}else{
		$form["body"] = str_replace("[link]", $link, $lang["send_to_friend"]["body"]);
		$form["comment"] = $lang["send_to_friend"]["comment"];
	}
	$form["top_header"] = $lang["send_to_friend"]["top_header"];
	$form["subject"] = str_replace("[site]", $site_name, $lang["send_to_friend"]["subj"]);
	$form["action"] = $file_name;
	$form["err"] = $err;
	$form["hidden"] .= "<input type=hidden name=sel value=sendto>";
	$form["hidden"] .= "<input type=hidden name=id_user value=".$id_user.">";

	$smarty->assign("form", $form);
	$smarty->assign("section", $lang["subsection"]);
	$smarty->assign("header", $lang["mailbox"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/send_to_friend_form.tpl");
	exit;
}

function SendFriend()
{
	global $lang, $config, $dbconn, $user;
	
	$err = "";

	$to = $_POST["to"];
	
	if (strlen(trim($to)) == 0) {
		$err = $lang["err"]["mail_error"];
	}
	$to_arr = array();
	$to_arr = explode(";", $to);
	$subj = stripslashes($_POST["subject"]);
	$body = $_POST["body"];
	
	$site_lang = $config['default_lang'];

	$rs = $dbconn->Execute("select sname, fname, email  from ".USERS_TABLE." where id='".$user[ AUTH_ID_USER ]."' ");
	$cont_arr["username"] = stripslashes($rs->fields[1]." ".$rs->fields[0]);
	$cont_arr["email"] = $rs->fields[2];
	$cont_arr["content"] = stripslashes($body);
	$count = (count($to_arr) <= 5) ? count($to_arr) : 5;
	
	if (!$count) {
		$err = $lang["err"]["mail_error"];
	}
	
	$use_refer_friend_feature = GetSiteSettings("use_refer_friend_feature") && !$user[ AUTH_GUEST ] && $_POST["refer_code"]!='';
	if ($use_refer_friend_feature) {
		$code = GetUserReferCode($user[ AUTH_ID_USER ]);
		if (!$code) {
			$strSQL = "INSERT INTO ".USER_REFER_CODE_TABLE." SET id_user='".$user[ AUTH_ID_USER ]."', code='".addslashes($_POST["refer_code"])."'";
			$dbconn->Execute($strSQL);
			if ($dbconn->ErrorNo()) {
				SendFriendForm($lang["err"]["error"]);
			}
		}
	}
	$n = 0;
	for ($i = 0; $i < $count; $i++) {
		$mailer_err = "";
		if (eregi("^.+@.+\\..+$", $to_arr[$i])) {
			if ($use_refer_friend_feature) {
				$strSQL = "SELECT email FROM ".USERS_TABLE." WHERE email='".$to_arr[$i]."' ";
				$email = $dbconn->GetOne($strSQL);
				if (!$email) {
					$mailer_err = SendMail($site_lang, $to_arr[$i], $cont_arr["email"], $subj, $cont_arr,
						"mail_mailbox_send_to_friend", "", "", $cont_arr["username"], 'send_to_friend');
				}
			} else {
				$mailer_err = SendMail($site_lang, $to_arr[$i], $cont_arr["email"], $subj, $cont_arr,
					"mail_mailbox_send_to_friend", "", "", $cont_arr["username"], 'send_to_friend');
			}
		}
		if ($mailer_err) {
			$err = $mailer_err;
		} else {
			$n++;
		}
	}
	
	if (!$err) {
		if ($use_refer_friend_feature) {
			$settings = GetSiteSettings(array('refer_friend_price','site_unit_costunit'));
			$acc_link = "<a href='#' onclick=\"javascript: parent.location.href='".$config["server"].$config["site_root"]."/account.php'; parent.GB_hide(); \" >".$lang["section"]["account"]."</a>";
			$search = array('[n_friends]','[n_money]','[curr]','[my_acc]');
			$repl = array($n, $settings['refer_friend_price'],$settings['site_unit_costunit'],$acc_link);
			$err = str_replace($search,$repl,$lang["err"]["refer_email_was_sent"]);
		} else {
			$err = $lang["err"]["email_was_sent"];
		}
	}
	SendFriendForm($err);
	return;
}
?>