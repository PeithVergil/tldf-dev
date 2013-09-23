{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.list}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.connections}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.chat_connections}</div>
<form name="connlist" id="connlist" action="connlist.php" method="post">
	<input type="hidden" id="sort" name="sort" value="none">
</form>
{if $connections}
<table border=0 class="table_main" cellspacing=1 cellpadding=3 width="100%">
<tr class="table_header">
	<th class="main_header_text" align="center"><a href="javascript:my_getbyid('sort').value = 'id'; my_getbyid('connlist').submit()">{$header.id}</a></th>
	<th class="main_header_text" align="center"><a href="javascript:my_getbyid('sort').value = 'updated'; my_getbyid('connlist').submit()">{$header.updated}</a></th>
	<th class="main_header_text" align="center"><a href="javascript:my_getbyid('sort').value = 'created'; my_getbyid('connlist').submit()">{$header.created}</a></th>
	<th class="main_header_text" align="center"><a href="javascript:my_getbyid('sort').value = 'login'; my_getbyid('connlist').submit()">{$header.user}</a></th>
	<th class="main_header_text" align="center"><a href="javascript:my_getbyid('sort').value = 'roomid'; my_getbyid('connlist').submit()">{$header.roomid}</a></th>
	<th class="main_header_text" align="center"><a href="javascript:my_getbyid('sort').value = 'state'; my_getbyid('connlist').submit()">{$header.state}</a></th>
	<th class="main_header_text" align="center"><a href="javascript:my_getbyid('sort').value = 'color'; my_getbyid('connlist').submit()">{$header.color}</a></th>
	<th class="main_header_text" align="center"><a href="javascript:my_getbyid('sort').value = 'start'; my_getbyid('connlist').submit()">{$header.start}</a></th>
	<th class="main_header_text" align="center"><a href="javascript:my_getbyid('sort').value = 'lang'; my_getbyid('connlist').submit()">{$header.lang}</a></th>
	<th class="main_header_text" align="center"><a href="javascript:my_getbyid('sort').value = 'ip'; my_getbyid('connlist').submit()">{$header.ip}</a></th>
	<th class="main_header_text" align="center"><a href="javascript:my_getbyid('sort').value = 'tzoffset'; my_getbyid('connlist').submit()">{$header.tzoffset}</a></th>
	<th class="main_header_text" align="center"><a href="javascript:my_getbyid('sort').value = 'host'; my_getbyid('connlist').submit()">{$header.host}</a></th>
</tr>
{foreach from=$connections item=connection}
<tr bgcolor="#FFFFFF">
	<td class="main_content_text">{$connection.id}</td>
	<td class="main_content_text" align=center>{$connection.updated}</td>
	<td class="main_content_text" align=center>{$connection.created}</td>
	<td class="main_content_text" align=center>
	{if $connection.userid}
		{$connection.login}
	{else}
		-
	{/if}
	</td>
	<td class="main_content_text" align=center>{$connection.roomid}</td>
	<td class="main_content_text" align=center>{$connection.state}</td>
	<td class="main_content_text">{$connection.color}</td>
	<td class="main_content_text">{$connection.start}</td>
	<td class="main_content_text" align=center>{$connection.lang}</td>
	<td class="main_content_text">{$connection.ip}</td>
	<td class="main_content_text" align=center>{$connection.tzoffset}</td>
	<td class="main_content_text" align=center>{$connection.host}</td>
</tr>
{/foreach}
</table>
{else}
	<font class="main_error_text">{$header.err_no_connections_found}</font>
{/if}
<br><br>
<p align="right" class="main_header_text">&copy;<a href="http://tufat.com/" target="_blank">TUFaT.com</a> </p>
{include file="$admingentemplates/admin_bottom.tpl"}