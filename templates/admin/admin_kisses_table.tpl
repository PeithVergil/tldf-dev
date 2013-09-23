{include file="$admingentemplates/admin_top.tpl"}

<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.list}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.ref_kiss_types}</div>

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
								<td class="main_header_text" align="center" >{$header.name}</td>
							</tr>
						{if $types}
						{section name=spr loop=$types}
							<tr bgcolor="#FFFFFF">
								<td class="main_content_text" align="center" width="20">{$types[spr].number}</td>
								<td class="main_content_text" align="center">
									<table border=0 cellspacing=0 cellpadding=0><tr>
									{if $types[spr].image_path}<td><a href="{$types[spr].editlink}"><img src="{$types[spr].image_path}" border=0></a>&nbsp;&nbsp;</td>{/if}
									<td><a href="{$types[spr].editlink}">{$types[spr].name}</a></td>
									</tr></table>
								</td>
							</tr>
							{/section}
							{else}
							<tr height="40">
								<td class="main_error_text" align="left" colspan="2" bgcolor="#FFFFFF">{$header.empty}</td>
							</tr>
							{/if}
					</table>
			<table><tr height="40">
			<td><input type="button" value="{$button.add}" class="button" onclick="javascript: location.href='{$form.add_link}'"></td>
			</tr></table>
{include file="$admingentemplates/admin_bottom.tpl"}