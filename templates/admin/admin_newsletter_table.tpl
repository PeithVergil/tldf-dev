{include file="$admingentemplates/admin_top.tpl"}
	{if $sistem}
	<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.sistemlist}</font>
	<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.newsletter}</div>
					<form method="post" action="{$form.action}"  enctype="multipart/form-data" name="sactivate">
					{$form.hiddens_sistem_active}
					<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
							<tr class="table_header">
								<td class="main_header_text" align="center" width="20">{$header.number}</td>
								<td class="main_header_text" align="center" >{$header.name_alert}</td>
								<td class="main_header_text" align="center" width="100">{$header.status}</td>
								<td class="main_header_text" align="center" width="100">&nbsp;</td>
							</tr>
							{section name=spr loop=$sistem}
							<tr bgcolor="#ffffff">
								<td class="main_content_text" align="center" >{$sistem[spr].number}</td>
								<td class="main_content_text" align="center" >{$sistem[spr].name}</td>
								<td class="main_content_text" align="center" ><input type="checkbox" name="active[{$smarty.section.spr.index}]" value="{$sistem[spr].id}" {if $sistem[spr].status}checked{/if}></td>
								<td class="main_content_text" align="center" ><input type="button" value="{$header.users}" class="button" onclick="javascript: location.href='{$sistem[spr].userlink}';"></td>
							</tr>
							{/section}
							<tr bgcolor="#ffffff" height="20">
								<td class="main_header_text" align="left" colspan="2">&nbsp;</td>
								<td align="center" width="100"><input type="button" value="{$button.activate}" class="button" onclick="javascript: document.sactivate.submit();"></td>
								<td class="main_header_text" align="left">&nbsp;</td>
							</tr>
					</table>
					</form>
<BR><BR>
{/if}
{include file="$admingentemplates/admin_bottom.tpl"}