{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.list}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.bans}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.chat_bans}</div>
{if $error}
	<font color="red">{$error}</font><br><br>
{/if}
{if $notice}
	<font color="green">{$notice}</font><br><br>
{/if}
<form name="banlist" id="banlist" action="banlist.php" method="post">
	<input type="hidden" id="sort" name="sort" value="none">
</form>
{if $bannedlist}
<table border=0 class="table_main" cellspacing=1 cellpadding=3 width="100%">
<tr>
	<th><a href="javascript:my_getbyid('sort').value = 'created'; my_getbyid('banlist').submit()">{$header.created}</a></th>
	<th><a href="javascript:my_getbyid('sort').value = 'user'; my_getbyid('banlist').submit()">{$header.user}</a></th>
	<th><a href="javascript:my_getbyid('sort').value = 'buser'; my_getbyid('banlist').submit()">{$header.banneduser}</a></th>
	<th><a href="javascript:my_getbyid('sort').value = 'roomid'; my_getbyid('banlist').submit()">{$header.roomid}</a></th>
	<th><a href="javascript:my_getbyid('sort').value = 'ip'; my_getbyid('banlist').submit()">{$header.ip}</a></th>
	<th><a href="javascript:my_getbyid('sort').value = 'banlevel'; my_getbyid('banlist').submit()">{$header.ban_level}</a></th>
	<th><a href="javascript:my_getbyid('sort').value = 'buser'; my_getbyid('banlist').submit()">{$header.remove_ban}</a></th>
</tr>
{foreach from=$bannedlist item=banned}
	<td>{$banned.created}</td>
	<td align=center>{$banned.user}</a></td>
	<td align=center>{$banned.buser}</a></td>
	<td align=center>{$banned.roomid}</td>
	<td>{$banned.ip}</td>
	<td><center>{$banned.banlevel}</center></td>
	<td align=center><a href="banlist.php?unbanid={$banned.banneduserid}">{$banned.banneduserid}</a></td>
</tr>
{/foreach}
</table>
{else}
	<font class="main_error_text">{$header.err_no_bans_found}</font>
{/if}
<br><br>
<p align="right" class="main_header_text">&copy;<a href="http://tufat.com/" target="_blank">TUFaT.com</a> </p>
{include file="$admingentemplates/admin_bottom.tpl"}