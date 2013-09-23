{include file="$admingentemplates/admin_top.tpl"}

<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.editform}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.ref_kiss_types_edit}</div>

				<table border=0 cellspacing=1 cellpadding=5 width="100%">
                <form method="post" action="{$form.action}"  enctype="multipart/form-data" name="types_form">
                {$form.hiddens}
					<tr bgcolor="#FFFFFF">
                        <td align="right" width="15%" class="main_header_text">{$header.name}:&nbsp;</td>
                        <td class="main_content_text" align="left" ><input type="text" name="name" value="{$data.name}" size=30></td>
                        <td class="main_header_text" align="left" width="70%">&nbsp;</td>
					</tr>
					<tr bgcolor="#ffffff">
			<td align="right" width="15%" class="main_header_text">{$header.icon}:&nbsp;</td>
			<td align="left" class="main_content_text"><input type="file" name="image_path" size=18></td>
                        <td class="main_header_text" align="left" width="70%">{if $data.image_path}<img src="{$data.image_path}">{/if}&nbsp;</td>
					</tr>
					<tr bgcolor="#FFFFFF">
                        <td align="right" width="15%" class="main_header_text">{$header.sorter}:&nbsp;</td>
                        <td class="main_content_text" align="left">
                        <select name="sorter">
						{section name=s loop=$sorter}
							<option value="{$smarty.section.s.index_next}" {if $sorter[s].sel}selected{/if}>{$smarty.section.s.index_next}</option>
						{/section}
						</select>
						</td>
                         <td class="main_header_text" align="left" width="70%">&nbsp;</td>
                   </tr>
                    </form>
            </table>
			<table><tr height="40">
			{if $form.par eq "edit"}
			<td><input type="button" value="{$button.save}" class="button" onclick="javascript:document.types_form.submit();"></td>
			<td><input type="button" value="{$button.delete}" class="button" onclick="{literal}javascript: if(confirm({/literal}'{$form.confirm}'{literal})){location.href={/literal}'{$form.delete}'{literal}}{/literal}"></td>
			{else}
			<td><input type="button" value="{$button.add}" class="button" onclick="javascript:document.types_form.submit();"></td>
			{/if}
			<td><input type="button" value="{$button.back}" class="button" onclick="javascript: location.href='{$form.back}'"></td>
			</tr></table>

{include file="$admingentemplates/admin_bottom.tpl"}