{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.list}</font><br><br>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.club_categories_list}</div>
<div>
<table cellpadding="0" cellspacing="3">
<tr height="40">
	<td><input type="button" value="{$header.add}" class="button" onclick="javascript: location.href='{$form.add_link}'"></td>
	<td><input type="button" value="{$lang.club.club_menu_3}" class="button" onclick="javascript: location.href='{$form.add_club_link}'"></td>
	<td><input type="button" value="{$lang.club.club_menu_1}" class="button" onclick="javascript: location.href='{$form.my_clubs_link}'"></td>
</tr>
</table>
<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
	{if $links}
	<tr bgcolor="#ffffff">
		<td height="20"  colspan=4 align="left"  class="main_content_text" >{$links}</td>
	</tr>
	{/if}
	<tr class="table_header">
		<td class="main_header_text" align="center" width="20">{$header.number}</td>
		<td class="main_header_text" align="center">{$header.category}</td>
		<td class="main_header_text" align="center" width="100">{$header.category_clubs_count}</td>
		<td class="main_header_text" align="center" width="100">&nbsp;</td>
	</tr>
	{if $club_categories}
	{section name=spr loop=$club_categories}
	<tr bgcolor="#FFFFFF">
		<td class="main_content_text" align="center">{$club_categories[spr].number}</td>
		<td class="main_content_text" align="center"><a href="{$club_categories[spr].editlink}">{$club_categories[spr].name}</a></td>
		<td class="main_content_text" align="center">{if $club_categories[spr].clublink}<a href="{$club_categories[spr].clublink}">{/if}{$club_categories[spr].clubs_count}&nbsp;{$header.clubs_small}{if $club_categories[spr].clublink}</a>{/if}</td>
		<td class="main_content_text" align="center"><input type="button" value="{$button.delete}" class="button" onclick="javascript: if(confirm('{$form.confirm}')){literal}{{/literal}location.href='{$club_categories[spr].deletelink}'{literal}}{/literal}"></td>
	</tr>
	{/section}
	{else}
	<tr height="40">
		<td class="main_error_text" align="left" colspan="4" bgcolor="#FFFFFF">{$header.empty_categories}</td>
	</tr>
	{/if}
	{if $links}
	<tr bgcolor="#ffffff">
		<td height="20"  colspan=4 align="left"  class="main_content_text" >{$links}</td>
	</tr>
	{/if}
</table>
</div>
{include file="$admingentemplates/admin_bottom.tpl"}