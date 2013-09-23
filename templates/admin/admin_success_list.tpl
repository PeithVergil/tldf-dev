{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$lang.addition.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.success_list}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.area_stories}</div>

<table>
	<tr height="40">
		<td><input type="button" value="{$header.add}" class="button" onclick="javascript: location.href='{$add_link}'"></td>
	</tr>
</table>

<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
	{if $links}
	<tr bgcolor="#ffffff">
		<td height="20"  colspan=8 align="left"  class="main_content_text" >{$links}</td>
	</tr>
	{/if}
	<tr class="table_header">
		<td class="main_header_text" align="center" width="5%" >{$header.number}</td>
		<td class="main_header_text" align="center" width="10%" >{$header.image}</td>
		<td class="main_header_text" align="center" width="15%" >{$header.story_title}</td>
		<td class="main_header_text" align="center" width="10%" >{$header.couple_name}</td>
		<td class="main_header_text" align="center" width="40%" >{$header.description}</td>
		<td class="main_header_text" align="center" width="10%" >{$header.date}</td>
		<td class="main_header_text" align="center" width="5%" >&nbsp;</td>
		<td class="main_header_text" align="center" width="5%" >&nbsp;</td>
	</tr>
{if $story}
	{section name=s loop=$story}
	<tr>
		<td class="main_content_text" align="center" bgcolor="#FFFFFF" valign="top">{$story[s].number}</td>
		<td class="main_content_text" align="left" bgcolor="#FFFFFF" valign="top">{$story[s].image_path}</td>
		<td class="main_content_text" align="center" bgcolor="#FFFFFF" valign="top">{$story[s].story_title}</td>
		<td class="main_content_text" align="center" bgcolor="#FFFFFF" valign="top">{$story[s].couple_name}</td>
		<td class="main_content_text" align="left" bgcolor="#FFFFFF" valign="top">{$story[s].description}</td>
		<td class="main_content_text" align="center" bgcolor="#FFFFFF" valign="top">{$story[s].story_date}</td>
		<td class="main_content_text" align="center" bgcolor="#FFFFFF" valign="top"><input type="button" value="{$button.edit}" class="button" onclick="{literal}javascript: location.href={/literal}'{$story[s].edit_link}'"></td>
		<td class="main_content_text" align="center" bgcolor="#FFFFFF" valign="top"><input type="button" value="{$button.delete}" class="button" onclick="{literal}javascript: if(confirm('Delete this story?')) {location.href={/literal}'{$story[s].delete_link}'{literal}}{/literal}"></td>
	</tr>
	{/section}
{elseif $empty_row eq 1}
	<tr height="40">
		<td class="main_error_text" align="center" colspan="8" bgcolor="#FFFFFF">{$header.empty}</td>
	</tr>
{/if}
	{if $links}
	<tr bgcolor="#ffffff">
		<td height="20"  colspan=8 align="left"  class="main_content_text" >{$links}</td>
	</tr>
	{/if}
</table>
{include file="$admingentemplates/admin_bottom.tpl"}
