{include file="$admingentemplates/admin_top.tpl"}

<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.list}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.ref_distances}</div>

			<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
							<tr class="table_header">
								<td class="main_header_text" align="center" width="20">{$header.number}</td>
								<td class="main_header_text" align="center">{$header.name}</td>
							</tr>
						{if $types}
						{section name=spr loop=$types}
							<tr bgcolor="#FFFFFF">
								<td class="main_content_text" align="center" width="20">{$types[spr].number}</td>
								<td class="main_content_text" align="center"><a href="{$types[spr].editlink}">{$types[spr].name} {$types[spr].type}</a></td>
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