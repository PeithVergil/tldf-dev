{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.list}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$top_help}</div>
<table cellSpacing="3" cellPadding="0">
	<tr>
	{section name=s loop=$lang_link}
		<td align="center"><a href="#" onclick="javascript:window.open('{$lang_link[s].link}','lang_edit', 'height=600, resizable=yes, scrollbars=yes,width=800, menubar=no,status=no'); return false;" class=privacy_link>{$lang_link[s].name}</a></td>
		{if !$smarty.section.s.last}
		<td align="center" valign="middle" class="main_content_text">&nbsp;|&nbsp;</td>
		{/if}
	{/section}
	</tr>
</table>
<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
	<tr class="table_header">
		<td class="main_header_text" align="center" width="20">{$header.number}</td>
		<td class="main_header_text" align="center">{$header.name}</td>
		{if $form.options eq 1}<td class="main_header_text" align="center" width="80">{$header.option}</td>{/if}
	</tr>
	{if $types}
	{section name=spr loop=$types}
	<tr bgcolor="#FFFFFF">
		<td class="main_content_text" align="center" width="20">{$types[spr].number}</td>
		<td class="main_content_text" align="center"><a href="{$types[spr].editlink}">{$types[spr].name}</a></td>
		{if $form.options eq 1}<td class="main_content_text" align="center"><input type="button" value="{$header.edit_option}" class="button" onclick="javascript: document.location.href='{$types[spr].editoptionlink}'"></td>{/if}
	</tr>
	{/section}
	{if $links}
	<tr bgcolor="#ffffff">
		<td height="20"  colspan="{if $form.options eq 1}3{else}2{/if}" align="left"  class="main_content_text" >{$links}</td>
	</tr>
	{/if}
	{else}
	<tr height="40">
		<td class="main_error_text" align="left" colspan="{if $form.options eq 1}3{else}2{/if}" bgcolor="#FFFFFF">{$header.empty}</td>
	</tr>
	{/if}
</table>
<table>
	<tr height="40">
		<td><input type="button" value="{$button.add}" class="button" onclick="javascript: location.href='{$form.add_link}'"></td>
	</tr>
</table>
{include file="$admingentemplates/admin_bottom.tpl"}