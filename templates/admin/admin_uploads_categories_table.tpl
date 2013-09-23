{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.categories_list}</font><br><br>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.uploads_gallery_categories}</div>
<div>
<table cellpadding="0" cellspacing="3">
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
		<td class="main_header_text" align="center">{$header.category}</td>
		<td>&nbsp;</td>
	</tr>
	{if $types}
	{section name=spr loop=$types}
	<tr bgcolor="#FFFFFF">
		<td class="main_content_text" align="center" width="20">{$types[spr].number}</td>
		<td class="main_content_text" align="center">{$types[spr].name}</td>
		<td class="main_content_text" width="100" align="center"><a href="{$types[spr].dellink}">{$button.delete}</a></td>
	</tr>
	{/section}
	{else}
	<tr height="40">
		<td class="main_error_text" align="left" colspan="2" bgcolor="#FFFFFF">{$header.empty}</td>
	</tr>
	{/if}
</table>
<table>
	<tr height="40">
		<td><input type="button" value="{$header.add_category}" class="button" onclick="javascript: location.href='{$form.add_link}'"></td>
	</tr>
</table>
</div>
{include file="$admingentemplates/admin_bottom.tpl"}