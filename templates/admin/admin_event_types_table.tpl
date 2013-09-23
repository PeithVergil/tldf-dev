{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.event_types}</font><br><br><br>
{if $form.err}
{else}
<table>
	<tr height="40">
		<td><input type="button" value="{$button.back}" class="button" onclick="javascript: location.href='{$back_link}'"></td>
		<td><input type="button" value="{$header.add_type}" class="button" onclick="javascript: location.href='{$add_link}'"></td>
	</tr>
</table>
<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
	{if $links}
	<tr bgcolor="#ffffff">
		<td height="20"  colspan=3 align="left"  class="main_content_text" >{$links}</td>
	</tr>
	{/if}
	<tr class="table_header">
		<td class="main_header_text" align="center" width="20">{$header.number}</td>
		<td class="main_header_text" align="center">{$header.type}</td>
		<td class="main_header_text" align="center" width="100">&nbsp;</td>
	</tr>
	{if $types}
	{section name=spr loop=$types}
	<tr bgcolor="#FFFFFF">
		<td class="main_content_text" align="center">{$types[spr].number}</td>
		<td class="main_content_text" align="center"><a href="{$types[spr].editlink}">{$types[spr].name}</a></td>
		<td class="main_content_text" align="center"><input type="button" value="{$button.delete}" class="button" onclick="javascript: if(confirm('{$header.confirm}')){literal}{{/literal}location.href='{$types[spr].deletelink}'{literal}}{/literal}">
	</tr>
	{/section}
	{else}
	<tr height="40">
		<td class="main_error_text" align="left" colspan="3" bgcolor="#FFFFFF">{$header.empty_types}</td>
	</tr>
	{/if}
	{if $links}
	<tr bgcolor="#ffffff">
		<td height="20"  colspan=3 align="left"  class="main_content_text" >{$links}</td>
	</tr>
	{/if}
</table>
{/if}
{include file="$admingentemplates/admin_bottom.tpl"}