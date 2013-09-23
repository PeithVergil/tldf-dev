{include file="$admingentemplates/admin_top.tpl"}
<div>
	<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.services_management}&nbsp;|&nbsp;{$service_name}</font>
</div>
<div class="help_text">
	<span class="help_title">{$lang.help}:</span>{$help.lift_up_service}
</div>
<form method="POST" action="admin_pays.php" style="margin: 0px; padding: 0px;">
<input type="hidden" name="sel" value="service">
<input type="hidden" name="service" value="{$service}">
<input type="hidden" name="par" value="save">
<table cellpadding="5" cellspacing="1">
	{foreach item=item from=$settings}
	<tr>
		<td class="main_header_text">{$item.title}</td>
		<td><input type="text" value="{$item.value}" name="settings[{$item.name}]" size="10"></td>
	</tr>
	{/foreach}
	<tr>
		<td></td>
		<td>
			<input type="submit" value="{$lang.button.save}">&nbsp;&nbsp;&nbsp;
			<input type="button" value="{$lang.button.back}" class="button" onclick="javascript: location.href='admin_pays.php?sel=settings'">
		</td>
	</tr>
</table>
</form>
<br>
{include file="$admingentemplates/admin_bottom.tpl"}