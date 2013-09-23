<?php
	/**VP Cron Jobs Reminder unsubscription page **/
	
	include "./include/config.php";
	include "./common.php";
	include "./include/functions_common.php";
	include $config['path_lang']."/mail/".$lang_file;
	
	$error_msg="";
	if ($_REQUEST['Submit'] == 'Yes')
	{
		$strcode = $_REQUEST['hid_uid'];
		if ($strcode != "")
		{
			$user_id = substr($strcode, 5, -5);
			//echo "User id: ".$user_id;
			
			if ($user_id && $user_id > 0)
			{
				//check the user in database.
				unset($check_exist);
				$check_exist = $dbconn->getOne("SELECT id FROM ".USERS_TABLE." WHERE id=".$user_id);
				
				if (!empty($check_exist))
				{
					unset($check_exist);
					$strSQL = 'SELECT id FROM '.USER_PRIVACY_SETTINGS.' WHERE id_user='.$user_id;
					$check_exist = $dbconn->getOne($strSQL);
					
					if (!empty($check_exist))
					{
						$strSQL = "UPDATE ".USER_PRIVACY_SETTINGS." SET is_rem_block='1' WHERE id_user='".$user_id."' ";
					}
					else
					{
						$strSQL = " INSERT INTO ".USER_PRIVACY_SETTINGS." (id_user, is_rem_block)
									VALUES ('".$user_id."', '1') ";
					}
					
					if($dbconn->Execute($strSQL))
					{
						header('Location: reminder_block.php?success=yes');
						exit();
					}
					else
					{
						$error_msg="Error:1 ";
					}
				}
				else
				{
					$error_msg="Error:2 ";
				}
			}
			else
			{
				$error_msg="Error:3 ";
			}
		}
		else
		{
			$error_msg="Error:4 ";
		}
		if($error_msg!="")
		{
			$error_msg.="Please contact site Administrator.";
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ThaiLadyDateFinder</title>
<link href="<?php echo $config['index_theme_path']; ?>/css/tldf_style.css?v=0001" rel="stylesheet" type="text/css" media="screen" />
</head>
<body>
<div align="center">
	<table cellpadding="0" cellspacing="20">
		<tr><td>&nbsp;</td></tr>
		<?php
			if($_REQUEST['success']=='yes')
			{
			?>
				<tr>
					<td class="txtgreen">Reminder mails successfully blocked for you.</td>
				</tr>
				<tr>
					<td align="center">
						<input type="button" class="btn_blue" style="width:60px;" value="Close" onclick="javascript:window.close();" />
					</td>
				</tr>
			<?php
			}
			else
			{
			?>
				<?php if($error_msg != '') { ?>
				<tr>
					<td colspan="2" class="txtred"><?php echo $error_msg; ?></td>
				</tr>
				<?php } ?>
				<tr>
					<td colspan="2">
						<?php
							echo ($lang_mail['generic_e']['unsubscribe_confirm']);
						?>
					</td>
				</tr>
				<tr>
					<td align="right" width="50%">
						<form method="post" action="" >
							<input type="hidden" name="hid_uid" value="<?php echo $_REQUEST['uid']; ?>" />
							<input type="submit" name="Submit" class="btn_blue" style="width:60px;" value="Yes" />
						</form>
					</td>
					<td align="left">
						<input type="button" class="btn_blue" style="width:60px;" value="Close" onclick="javascript:window.close();" />
					</td>
				</tr>
			<?php
			}
			?>
	</table>
</div>
</body>
</html>