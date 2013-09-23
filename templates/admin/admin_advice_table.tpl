{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$lang.addition.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.adv_list}&nbsp;|&nbsp;{$form.category}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.area_advice}</div>
	<table>
		<tr height=40>
			<td><input type="button" value="{$lang.button.back}" class="button" onclick="javascript: location='{$form.back_link}'"></td>
			<td><input type="button" value="{$header.adv_add}" class="button" onclick="javascript: location.href='{$form.add_link}'"></td>
		</tr>
	</table>
	<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
		<tr class="table_header">
			<td class="main_header_text" align="center" width="5%" >{$header.number}</td>
			<td class="main_header_text" align="middle" width="30%" >{$header.advice}</td>
			<td class="main_header_text" align="middle" width="45%" >{$header.adv_descr}</td>
			<td class="main_header_text" align="middle" width="10%" >&nbsp;</td>
			<td class="main_header_text" align="middle" width="10%" >&nbsp;</td>
		</tr>
		{if $adv_list}
		{section name=a loop=$adv_list}
			<tr bgcolor="#FFFFFF">
				<td class="main_content_text" align="center" valign="top">{$smarty.section.a.iteration}</td>
				<td class="main_content_text" align="left"   valign="top">{$adv_list[a].title}</td>
				<td class="main_content_text" align="left"   valign="top">{$adv_list[a].body}</td>
				<td class="main_content_text" align="center" valign="top"><input type="button" value="{$lang.button.edit}" class="button" onclick="document.location.href='{$adv_list[a].edit_link}'"></td>
				<td class="main_content_text" align="center" valign="top"><input type="button" value="{$lang.button.delete}" class="button" onclick="document.location.href='{$adv_list[a].del_link}'"></td>
			</tr>
		{/section}
		{else}
		<tr height="40">
			<td class="main_error_text" align="center" colspan="5" bgcolor="#FFFFFF">{$header.empty}</td>
		</tr>
		{/if}
	</table>
{include file="$admingentemplates/admin_bottom.tpl"}