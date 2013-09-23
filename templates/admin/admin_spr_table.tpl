{include file="$admingentemplates/admin_top.tpl"}

<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.list}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.ref_description}</div>

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
					{if $links}
					<tr bgcolor="#ffffff">
						<td height="20"  colspan=4 align="left"  class="main_content_text" >{$links}</td>
					</tr>
					{/if}
							<tr class="table_header">
								<td class="main_header_text" align="center" width="10">{$header.number}</td>
								<td class="main_header_text" align="center" >{$header.name}</td>
								<td class="main_header_text" align="center" width="50">{$header.status}</td>
								<td class="main_header_text" align="center" width="100">&nbsp;</td>
							</tr>
							{if $references}
							{section name=spr loop=$references}
							<tr bgcolor="#FFFFFF">
								<td class="main_content_text" align="center">{$references[spr].number}</td>
								<td class="main_content_text" align="center"><a href="{$references[spr].editlink}">{$references[spr].name}</a></td>
								<td class="main_content_text" align="center">{$references[spr].status}</td>
								<td class="main_content_text" align="center"><input type="button" value="{$header.edit_option}" class="button" onclick="javascript: location.href='{$references[spr].editoptionlink}'"></td>
							</tr>
							{/section}
							{else}
							<tr height="40">
								<td class="main_error_text" align="left" colspan="4" bgcolor="#FFFFFF">{$header.empty}</td>
							</tr>
							{/if}
					{if $links}
					<tr bgcolor="#ffffff">
						<td height="20"  colspan=4 align="left"  class="main_content_text" >{$links}</td>
					</tr>
					{/if}
						</form>
					</table>
			<table><tr height="40">
			<td><input type="button" value="{$header.add}" class="button" onclick="javascript: location.href='{$add_link}'"></td>
			</tr></table>
{include file="$admingentemplates/admin_bottom.tpl"}