{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$clubs[0].category}&nbsp;|&nbsp;{$header.club_list}</font><br><br>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.club_list}</div>
<div>
<table cellpadding="0" cellspacing="0">
<tr height="40">
	<td><input type="button" value="{$lang.button.back}" class="button" onclick="javascript: location.href='admin_club.php'"></td>
</tr>
</table>
<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
	{if $links}
	<tr bgcolor="#ffffff">
		<td height="20"  colspan=7 align="left"  class="main_content_text" >{$links}</td>
	</tr>
	{/if}
	<tr class="table_header">
		<td class="main_header_text" align="center" width="20">{$header.number}</td>
		<td class="main_header_text" align="center" width="100">{$header.club_leader}</td>
		<td class="main_header_text" align="center">{$header.club_name}</td>
		<td class="main_header_text" align="center" width="100">{$header.club_status}</td>
		<td class="main_header_text" align="center" width="100">{$header.club_members_count}</td>
		<td class="main_header_text" align="center" width="100">{if $clubs}<input type="button" value="{$lang.button.delete}"  {literal}onclick="javascript: if (window.confirm('{/literal}{$lang.users.del_confirm}{literal}'))  { document.clubs.sel.value='delete_clubs'; document.clubs.submit(); } else return false;"{/literal} />{/if}</td>
		<td class="main_header_text" align="center" width="100">&nbsp;</td>
	</tr>
	{if $clubs}
    <form action="{$form.action}" name="clubs" method="post">
    {$form.hidden}
	{section name=c loop=$clubs}
	<tr bgcolor="#FFFFFF">
		<td class="main_content_text" align="center">{$clubs[c].number}</td>
		<td class="main_content_text" align="center"><a href="{$clubs[c].leader_link}">{$clubs[c].leader_name}</a></td>
		<td class="main_content_text" align="center"><a href="{$clubs[c].club_link}">{$clubs[c].name}</a>{if $clubs[c].edit_link}&nbsp;&nbsp;<a href="{$clubs[c].edit_link}">{$lang.button.edit}</a>{/if}</td>
		<td class="main_content_text" align="center">{if $clubs[c].is_open eq 1}{$header.public}{else}{$header.private}{/if}</td>
		<td class="main_content_text" align="center">{$clubs[c].members_count}</td>
        <td class="main_content_text" align="center"><input type="checkbox" name="del_club[{$clubs[c].id}]" value="{$clubs[c].id}" /></td>
		<td class="main_content_text" align="center" width="100"><input type="button" value="{$lang.users.comunicate}" class="button" onclick="javascript:window.open('{$clubs[c].leader_comunicate}','comunicate', 'height=800, resizable=yes, scrollbars=yes,width=600, menubar=no,status=no');"></td>
	</tr>
	{/section}
    </form>
	{else}
	<tr height="40">
		<td class="main_error_text" align="left" colspan="7" bgcolor="#FFFFFF">{$header.empty_clubs}</td>
	</tr>
	{/if}
	{if $links}
	<tr bgcolor="#ffffff">
		<td height="20" colspan=7 align="left"  class="main_content_text" >{$links}</td>
	</tr>
	{/if}
</table>
</div>
{include file="$admingentemplates/admin_bottom.tpl"}