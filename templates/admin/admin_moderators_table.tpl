{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.list}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.moderators_list}</div>
<div>
	<form method="post" action="admin_moderators.php" id="main_form" style="margin: 0px; padding: 0px;">
	<input type="hidden" name="sel" id="sel" value="">
	<input type="hidden" name="page" id="page" value="{$page}">
	<table class="main_table" id="main_groups_div_1" border="0">
		{if $user}
		{if $links}
		<tr bgcolor="#ffffff">
			<td colspan="5" height="20"  align="left" class="main_content_text" >{$links}</td>
		</tr>
		{/if}
		<tr>
            <td class="main_header_table" width="25">{$header.number}</td>
            <td class="main_header_table">{$header.login}</td>
            <td class="main_header_table">{$header.email}</td>
            <td class="main_header_table" width="50">{$header.status}</td>
            <td class="main_header_table" width="100">{$header.date_last_seen}</td>
            <td class="main_header_table" width="40">&nbsp;</td>
        </tr>
	  	{foreach item=item from=$user}
        <tr>
            <td class="main_content_table">{$item.number}</td>
            <td class="main_content_table"><a href="admin_moderators.php?sel=edit&id={$item.id}">{$item.login}</a></td>
            <td class="main_content_table">{$item.email}</td>
            <td class="main_content_table">
            	<input type="checkbox" name="status[]" value="{$item.id}" {if $item.status eq '1'} checked {/if}>
				<input type="hidden" name="id_user[]" value="{$item.id}">
            </td>
            <td class="main_content_table">{$item.date_last_seen}</td>
            <td class="main_content_table"><a href="admin_moderators.php?sel=delete&id={$item.id}">{$header.delete}</a></td>
        </tr>
		{/foreach}
		<tr>
			<td colspan="3" class="main_content_text">&nbsp;</td>
			<td class="main_content_text" colspan="2" style="padding-top: 5px;"><input type="button" value="{$header.refresh_status}" class="button" onclick="document.getElementById('sel').value='status'; document.getElementById('main_form').submit();"></td>
		</tr>
		{if $links}
		<tr bgcolor="#ffffff">
			<td colspan="5" height="20"  align="left" class="main_content_text" >{$links}</td>
		</tr>
		{/if}
		{else}
        <tr height="40">
            <td class="main_error_text" align="left" colspan="5" bgcolor="#FFFFFF">{$header.empty}</td>
        </tr>
		{/if}
	</table>
	<table id="main_groups_div_2">
		<tr height="40">
			<td><input type="button" value="{$header.add_moderator}" class="button" onclick="javascript: location.href='{$form.add_link}'"></td>
		</tr>
	</table>
	</form>
</div>
{include file="$admingentemplates/admin_bottom.tpl"}