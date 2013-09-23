{include file="$admingentemplates/admin_top.tpl"}

<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.editform}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{if $form.par eq "edit"}{$help.ref_distances_edit}{else}{$help.ref_distances_add}{/if}</div>

				<table border=0 cellspacing=1 cellpadding=5 width="100%">
                <form method="post" action="{$form.action}"  enctype="multipart/form-data" name="types_form">
                {$form.hiddens}
					<tr bgcolor="#FFFFFF">
                        <td align="right" width="15%" class="main_header_text">{$header.name}:&nbsp;</td>
                        <td class="main_content_text" align="left" ><input type="text" name="name" value="{$data.name}" size=10></td>
                        <td class="main_header_text" align="left" width="70%">&nbsp;</td>
					</tr>
					<tr bgcolor="#FFFFFF">
                        <td align="right" width="15%" class="main_header_text">{$header.type}:&nbsp;</td>
                        <td class="main_content_text" align="left">
                        <select name="type">
				<option value="mile" {if $data.type == "mile"}selected{/if}>{$header.mile}</option>
				<option value="km" {if $data.type == "km"}selected{/if}>{$header.km}</option>
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