{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.listform}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.events}</div>
{if $form.err}
{else}
<table>
	<tr height="40">
		<td><input type="button" value="{$header.add}" class="button" onclick="javascript: location.href='{$add_link}'"></td>
		<td><input type="button" value="{$header.event_types}" class="button" onclick="javascript: location.href='{$types_link}'"></td>
	</tr>
</table>
<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
	{if $links}
	<tr bgcolor="#ffffff">
		<td height="20" align="left"  class="main_content_text" colspan="9">{$links}</td>
	</tr>
	{/if}
	{if $event}
		<tr class="table_header">
			<td class="main_header_text" align="center" width="5%">{$header.number}</td>
			<td class="main_header_text" align="center" width="10%">{$header.event_creator}</td>
			<td class="main_header_text" align="center" width="10%">{$header.type}</td>
			<td class="main_header_text" align="center" width="20%">{$header.event_name}</td>
			<td class="main_header_text" align="center" width="10%">{$header.event_date_begin}</td>
			<td class="main_header_text" align="center" width="10%">{$header.event_date_end}</td>
			<td class="main_header_text" align="center" width="5%">{$header.event_periodicity}</td>
			<td class="main_header_text" align="center" width="10%">{$header.event_users}</td>
			<td class="main_header_text" align="center" width="10%">&nbsp;</td>
        </tr>
		{section name=e loop=$event}
		<tr>
			<td class="main_content_text" align="center" bgcolor="#FFFFFF">{$event[e].number}</td>
			<td class="main_content_text" align="center" bgcolor="#FFFFFF">{if $event[e].admin_creator}{$event[e].event_creator}{else}<a href="{$event[e].creator_profile_link}">{$event[e].event_creator}</a>{/if}</td>
			<td class="main_content_text" align="center" bgcolor="#FFFFFF">{$event[e].type}</td>
			<td class="main_content_text" align="left" bgcolor="#FFFFFF">{if $event[e].admin_creator}<a href="{$event[e].edit_link}">{$event[e].name}</a>{else}{$event[e].name}{/if}</td>
			<td class="main_content_text" align="center" bgcolor="#FFFFFF">{$event[e].date_begin}</td>
			<td class="main_content_text" align="center" bgcolor="#FFFFFF">{$event[e].date_end}</td>
			<td class="main_content_text" align="center" bgcolor="#FFFFFF">{$event[e].periodicity} {if $event[e].date_die}{$header.until} {$event[e].date_die}{/if}</td>
			<td class="main_content_text" align="center" bgcolor="#FFFFFF">{$event[e].num_users} {if $event[e].admin_creator}<a href="#" onclick="javascript: location.href='{$event[e].users_link}'">{$user_list}</a>{/if}</td>
			{if $event[e].admin_creator}
			<td align="center" bgcolor="#FFFFFF"><input type="button" value="{$button.delete}" class="button" onclick="{literal}javascript: if(confirm('Delete this event?')){location.href={/literal}'{$event[e].delete_link}'{literal}}{/literal}"></td>
			{else}
			<td align="center" bgcolor="#FFFFFF"><input type="button" value="{$lang.users.comunicate}" class="button" onclick="javascript:window.open('{$event[e].comunicate_link}','comunicate', 'height=800, resizable=yes, scrollbars=yes,width=600, menubar=no,status=no');"></td>
			{/if}
		</tr>
		{/section}
	{else}
		<tr height="40">
			<td class="main_error_text" align="left" colspan="9" bgcolor="#FFFFFF">{$header.empty}</td>
		</tr>
	{/if}
	{if $links}
	<tr bgcolor="#ffffff">
		<td height="20" align="left"  class="main_content_text" colspan="9">{$links}</td>
	</tr>
	{/if}
</table>
<table>
	<tr height="40">
		<td><input type="button" value="{$header.add}" class="button" onclick="javascript: location.href='{$add_link}'"></td>
		<td><input type="button" value="{$header.event_types}" class="button" onclick="javascript: location.href='{$types_link}'"></td>
	</tr>
</table>
{/if}
{include file="$admingentemplates/admin_bottom.tpl"}