{include file="$admingentemplates/admin_top.tpl"}
	<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.settings}</font><br /><br />
	<div class="help_text"><span class="help_title">{$lang.help}:</span>{$lang.voip.settings_help_1}http://jajah.com/business/users/registration/{$lang.voip.settings_help_2}http://jajah.com/business/users/registration/{$lang.voip.settings_help_3}</div>
	{if $form.error}<font class="error_msg">{$form.error}</font>{/if}
	<form action="admin_voip_settings.php" method="post" enctype="multipart/form-data" name="settings">
	{$form.hiddens}
	<table cellpadding="0" cellspacing="0" class="admin_edit_form">
	<tr>
		<td class="text_head">{$lang.voip.admin_name}:</td>
		<td><input type="text" name="admin_name" value="{$settings.voip_admin}" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td class="text_head">{$lang.voip.admin_password}:</td>
		<td><input type="password" name="admin_password" value="{$settings.voip_admin_pass}" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td class="text_head">{$lang.voip.account_id}:</td>
		<td><input type="text" name="account_id" value="{$settings.voip_accountid}" /></td>
		<td><i>&nbsp;&nbsp;* {$lang.voip.account_id_comment}</i></td>
	</tr>
	<tr>
		<td class="text_head">{$lang.voip.admin_percent}:</td>
		<td><input type="text" name="admin_percent" size="3" value="{$settings.voip_admin_percent}"/></td>
		<td><i>&nbsp;%&nbsp;&nbsp;* {$lang.voip.admin_percent_comment}</i></td>
	</tr>
	<tr>
		<td class="text_head">{$lang.voip.currency_rate}:</td>
		<td>1 {$settings.site_currency} = <input type="text" name="curr_rate" size="3" value="{$settings.voip_currency_rate}" {if !$settings.edit_rate}disabled="disabled"{/if}/></td>
		<td><i> {$balance.jajah_currency_name}&nbsp;&nbsp;* {$lang.voip.currency_rate_comment}</i></td>
	</tr>
	<tr>
		<td colspan="3">
			<input type="button" onclick="javascript: checkForm(document.settings);" value="{$lang.button.save}"/>
		</td>
	</tr>
	<tr>
		<td>{$lang.voip.admin_balance}:</td>
		<td colspan="2">{$balance.balance}&nbsp;{$balance.currency_name} <a target="_blank" href="http://jajah.com">{$lang.voip.add_founds}</a>&nbsp;&nbsp;* {$lang.voip.admin_balance_comment}</td>
	</tr>
	</table>
	</form>
<script type="text/javascript">
{literal}
function checkForm(form){
	if (form.admin_name.value == ''){
		alert('{/literal}{$lang.voip.err.empty_adminname}{literal}');
		return false;
	}
	if (form.admin_password.value == ''){
		alert('{/literal}{$lang.voip.err.empty_adminpassword}{literal}');
		return false;
	}
	if (form.account_id.value == ''){
		alert('{/literal}{$lang.voip.err.empty_accountid}{literal}');
		return false;
	}
	percent = parseInt(form.admin_percent.value);
	if (percent < 1){
		alert('{/literal}{$lang.voip.err.wrong_percent}{literal}');
		return false;
	}
	rate = Math.round(parseFloat(form.curr_rate.value)*1000)/1000;
	if (rate <= 0){
		alert('{/literal}{$lang.voip.err.wrong_rate}{literal}');
		return false;
	}
	form.submit();
}
{/literal}
</script>
{include file="$admingentemplates/admin_bottom.tpl"}