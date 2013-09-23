{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$lang.forum.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$lang.forum.banned_users}</font><br><br>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.banned_users}</div>
<div>
{strip}
<table width="100%" border="0" cellpadding="0" cellspacing="1">
<tr>
	<td valign=top>
		<div style="margin: 0px; padding-bottom: 10px; padding-left: 13px;">
		</div>
		<div>
		{if $bans eq 'empty'}
		{$lang.forum.no_banned_users}
		{else}
			<form method="post" action="admin_forum.php" style="margin: 0px; padding: 0px;">
			<input type="hidden" name="sel" value="unban">
    		<table border=0 class="table_main" cellspacing=1 cellpadding=5>
			{if $links}
			<tr bgcolor="#ffffff">
				<td height="20" colspan=4 align="left"  class="main_content_text" >{$links}</td>
			</tr>
			{/if}
           	<tr class="table_header">
           		<td class="main_header_link" align="center" width="15">{$lang.forum.num}</td>
                <td class="main_header_link" align="center" width="150">{$lang.forum.login}</td>
                <td class="main_header_link" align="center" width="80">{$lang.forum.date}</td>
                <td class="main_header_link" align="center">&nbsp;</td>
          	</tr>
			{foreach item=item from=$bans}
            <tr bgcolor="#FFFFFF">
            	<td class="main_content_text" align="center">{$item.num}</td>
                <td class="main_content_text" align="center"><a href="admin_users.php?sel=edit&id={$item.id_user}" target="_blank">{$item.login}</a></td>
                <td class="main_content_text" align="center">{$item.b_date}</td>
                <td class="main_content_text" align="center"><input type="checkbox" value="{$item.id}" name="id[]"></td>
			</tr>
			{/foreach}
			<tr>
				<td colspan="3">&nbsp;</td>
				<td><input type="submit" value="{$lang.forum.unban}"></td>
			</tr>
			</table>
			</form>
		{/if}
		</div>
	</td>
</tr>
</table>
<!-- end main cell -->
{/strip}
</div>
{include file="$admingentemplates/admin_bottom.tpl"}