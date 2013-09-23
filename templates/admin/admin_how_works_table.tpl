{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.list}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.area_how_works}</div>
<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
	{if $links}
	<tr bgcolor="#ffffff">
		<td height="20"  colspan=6 align="left"  class="main_content_text" >{$links}</td>
	</tr>
	{/if}
	<tr class="table_header">
		<td class="main_header_text" align="center" colspan=2 width="80">{$header.number}</td>
		<td class="main_header_text" align="center">{$header.title}</td>
		<td class="main_header_text" align="center">{$header.title_t}</td>
		<td class="main_header_text" align="center" width="70">{$header.status}</td>
		<td class="main_header_text" align="center" width="100">&nbsp;</td>
	</tr>
	{if $how_works}
	{section name=spr loop=$how_works}
	<tr bgcolor="#ffffff">
		<td class="main_content_text" align="center" width="70">
			<table>
				<tr>
					<td align="center">
						<input type="button" value="{$header.up}" class="button" onclick="javascript: location.href='{$how_works[spr].uplink}'">
					</td>
				</tr>
				<tr>
					<td align="center">
						<input type="button" value="{$header.down}" class="button" onclick="javascript: location.href='{$how_works[spr].downlink}'">
					</td>
				</tr>
			</table>
		</td>
		<td class="main_content_text" align="center" width="10"><a href="{$how_works[spr].editlink}">{$how_works[spr].number}</a></td>
		<td class="main_content_text" align="left" style="padding:4"><a href="{$how_works[spr].editlink}">{$how_works[spr].title}</a></td>
		<td class="main_content_text" align="left" style="padding:4"><a href="{$how_works[spr].editlink}">{$how_works[spr].title_t}</a></td>
		<td class="main_content_text" align="center">{$how_works[spr].status}</td>
		<td class="main_content_text" align="center">
			<input type="button" value="{$button.delete}" class="button" onclick="{literal}javascript: if(confirm({/literal}'{$form.confirm}'{literal})){location.href={/literal}'{$how_works[spr].deletelink}'{literal}}{/literal}">
		</td>
	</tr>
	{/section}
	{else}
	<tr height="40" bgcolor="#ffffff">
		<td class="main_error_text" align="left" colspan="5">{$header.empty}</td>
	</tr>
	{/if}
	{if $links}
	<tr bgcolor="#ffffff">
		<td height="20"  colspan=6 align="left"  class="main_content_text" >{$links}</td>
	</tr>
	{/if}
</table>
<table>
	<tr height="40">
		<td>
			<input type="button" value="{$header.add}" class="button" onclick="javascript: location.href='{$add_link}'">
		</td>
	</tr>
</table>
{include file="$admingentemplates/admin_bottom.tpl"}