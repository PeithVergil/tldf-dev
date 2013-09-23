{include file="$admingentemplates/admin_top.tpl"}
	<font class=red_header>{$lang.addition.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.list}</font>
	<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.area_taketour}</div>

				<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
							{if $links}
							<tr bgcolor="#ffffff">
								<td height="20"  colspan=5 align="left"  class="main_content_text" >{$links}</td>
							</tr>
							{/if}
							<tr class="table_header">
								<td class="main_header_text" align="center" colspan=2 width="80">{$header.number}</td>
								<td class="main_header_text" align="center">{$header.comment}</td>
								<td class="main_header_text" align="center" width="70">{$header.status}</td>
								<td class="main_header_text" align="center" width="100">&nbsp;</td>
							</tr>
							{if $taketour}
							{section name=spr loop=$taketour}
							<tr bgcolor="#ffffff">
								<td class="main_content_text" align="center" width="70"><table>
								<tr><td align="center"><input type="button" value="{$header.up}" class="button" onclick="javascript: location.href='{$taketour[spr].uplink}'">
								</td></tr>
								<tr><td align="center"><input type="button" value="{$header.down}" class="button" onclick="javascript: location.href='{$taketour[spr].downlink}'">
								</td></tr>
								</table></td>
								<td class="main_content_text" align="center" width="10"><a href="{$taketour[spr].editlink}">{$taketour[spr].number}</a></td>
								<td class="main_content_text" align="left" style="padding:4"><a href="{$taketour[spr].editlink}">{$taketour[spr].comment}</a></td>
								<td class="main_content_text" align="center">{$taketour[spr].status}</td>
								<td class="main_content_text" align="center"><input type="button" value="{$button.delete}" class="button" onclick="{literal}javascript: if(confirm({/literal}'{$form.confirm}'{literal})){location.href={/literal}'{$taketour[spr].deletelink}'{literal}}{/literal}"></td>
							</tr>
							{/section}
							{else}
							<tr height="40" bgcolor="#ffffff">
								<td class="main_error_text" align="left" colspan="5">{$header.empty}</td>
							</tr>
							{/if}
							{if $links}
							<tr bgcolor="#ffffff">
								<td height="20"  colspan=5 align="left"  class="main_content_text" >{$links}</td>
							</tr>
							{/if}
					</table>

			<table><tr height="40">
			<td><input type="button" value="{$header.add}" class="button" onclick="javascript: location.href='{$add_link}'"></td>
			</tr></table>
{include file="$admingentemplates/admin_bottom.tpl"}