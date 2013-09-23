{include file="$admingentemplates/admin_top.tpl"}
{strip}
<form action="{$form.action}" method="post" name="login_form" onsubmit="return CheckValid(this);">
	{$form.hiddens}
	<table border="0" cellspacing="1" cellpadding="5" align="center">
		<tr bgcolor="#ffffff">
			<td></td>
			<td colspan="2">
				<font class="red_header">{$header.razdel_name}</font><font class="red_sub_header">&nbsp;|&nbsp;{$header.editform}</font>
			</td>
		</tr>
		<tr bgcolor="#ffffff">
			<td class="main_header_text">{$header.login}:&nbsp;</td>
			<td colspan="2" class="main_content_text" align="center"><input type="text" name="login_lg" value="{$data.login}" size="30"></td>
		</tr>
		<tr bgcolor="#ffffff">
			<td class="main_header_text">{$header.pass}:&nbsp;</td>
			<td colspan="2" class="main_content_text" align="center"><input type="password" name="pass_lg" value="{$data.pass}" size="30"></td>
		</tr>
		<tr bgcolor="#ffffff">
			<td></td>
			<td><input type="submit" value="{$button.login}" class="button"></td>
			<td align="right"><a href="..">Goto Site</a></td>
		</tr>
	</table>
</form>
{/strip}
{literal}
<script language="JavaScript" type="text/javascript">
document.login_form.login_lg.focus();

function CheckValid(form)
{
	if (form.login_lg.value == "") {
		alert({/literal}'{$err.invalid_login}'{literal});
		form.login_lg.focus();
		return false;
	}
	if (form.pass_lg.value == "") {
		alert({/literal}'{$err.invalid_passw}'{literal});
		form.pass_lg.focus();
		return false;
	}
	return true;
}
</script>
{/literal}
{include file="$admingentemplates/admin_bottom.tpl"}