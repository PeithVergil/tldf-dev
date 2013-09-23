{include file="$admingentemplates/admin_top.tpl"}
	<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.edit_type}</font><br><br><br>
	{if $form.err}{else}
	{if $err}<font class=main_error_text>*{$err}</font><br><br>{/if}
	<table border=0 cellspacing=1 cellpadding=5 width="100%">
                <form method="POST" action="{$form.action}" name="eventform">
                {$form.hiddens}
				<tr bgcolor="#ffffff">
					<td align="right" class="main_header_text" width="160px">{$header.type}<font class=main_error_text>*</font>:&nbsp;</td>
					<td class="main_content_text" align="left"><input type="text" name="name" value="{$data.name}" size=30 style="width: 195"></td>
				</tr>
				<tr bgcolor="#ffffff"><td colspan="2">
				<table>
					<tr height="40">
					{if $form.par eq "edit"}
					<td><input type="button" value="{$button.save}" class="button" onclick="javascript:document.eventform.sel.value='savetype';document.eventform.submit()"></td>
					<td><input type="button" value="{$button.delete}" class="button" onclick="javascript: if(confirm('{$header.confirm}')){literal}{location.href={/literal}'{$form.delete_link}'{literal}}{/literal}"></td>
					{else}
					<td><input type="button" value="{$button.add}" class="button" onclick="javascript:document.eventform.sel.value='addtype';document.eventform.submit()"></td>
					{/if}
					<td><input type="button" value="{$button.back}" class="button" onclick="javascript: location.href='{$form.back}'"></td>
					</tr>
				</table>
				</form>
				</td></tr>
    </table>
    {/if}
{include file="$admingentemplates/admin_bottom.tpl"}