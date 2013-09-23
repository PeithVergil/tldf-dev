{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$lang.addition.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.cat_list}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.area_advice_category}</div>
	<table>
		<tr height=40>
			<td><input type="button" value="{$header.cat_add}" class="button" onclick="javascript: location.href='{$form.add_link}'"></td>
		</tr>
	</table>
	<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
		<tr class="table_header">
			<td class="main_header_text" align="center" width="5%" >{$header.number}</td>
			<td class="main_header_text" align="center" width="20%" >{$header.category}</td>
			<td class="main_header_text" align="center" width="55%" >{$header.cat_descr}</td>
			<td class="main_header_text" align="center" width="10%" >&nbsp;</td>
			<td class="main_header_text" align="center" width="10%" >&nbsp;</td>
		</tr>
		{if $cat_list}
		{section name=c loop=$cat_list}
			<tr bgcolor="#FFFFFF">
				<td class="main_content_text" align="center" valign="top">{$smarty.section.c.iteration}</td>
				<td class="main_content_text" align="center" valign="top"><a href="{$cat_list[c].item_link}">{$cat_list[c].name}</a></td>
				<td class="main_content_text" align="left"   valign="top">{$cat_list[c].descr}</td>
				<td class="main_content_text" align="center" valign="top"><input type="button" value="{$lang.button.edit}" class="button" onclick="javascript: location.href='{$cat_list[c].edit_link}'"></td>
				<td class="main_content_text" align="center" valign="top"><input type="button" value="{$lang.button.delete}" class="button" onclick="javascript: location.href='{$cat_list[c].del_link}'"></td>
			</tr>
		{/section}
		{else}
		<tr height="40">
			<td class="main_error_text" align="left" colspan="5" bgcolor="#FFFFFF">{$header.empty}</td>
		</tr>
		{/if}
	</table>
{include file="$admingentemplates/admin_bottom.tpl"}