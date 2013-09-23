{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.list}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.ignores}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.chat_ignoring}</div>
<form name="ignorelist" id="ignorelist" action="ignorelist.php" method="post">
	<input type="hidden" id="sort" name="sort" value="none">
</form>
{if $error}
<font color="red">{$error}</font><br><br>
{/if}
{if $notice}
<font color="green">{$notice}</font><br><br>
{/if}
{if $ignores}
<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
<tr class="table_header">
	<th class="main_header_text" align="center"><a href="javascript:my_getbyid('sort').value = 'created'; my_getbyid('ignorelist').submit()">{$header.created}</a></th>
	<th class="main_header_text" align="center"><a href="javascript:my_getbyid('sort').value = 'user'; my_getbyid('ignorelist').submit()">{$header.user}</a></th>
	<th class="main_header_text" align="center"><a href="javascript:my_getbyid('sort').value = 'iuser'; my_getbyid('ignorelist').submit()">{$header.ignored_user}</a></th>
	<th class="main_header_text" align="center"><a href="javascript:my_getbyid('sort').value = 'iuserid'; my_getbyid('ignorelist').submit()">{$header.remove_ignore}</a></th>
</tr>
{foreach from=$ignores item=ignore}
<tr bgcolor="#FFFFFF">
	<td class="main_content_text" align=center>{$ignore.created}</td>
	<td class="main_content_text" align=center>{$ignore.user}</a></td>
	<td class="main_content_text" align=center>{$ignore.iuser}</a></td>
	<td class="main_content_text" align=center><a href="ignorelist.php?unignoreid={$ignore.iuserid}">{$ignore.iuserid}</a></td>
</tr>
{/foreach}
</table>
{else}
	<font class="main_error_text">{$header.err_no_ignores_found}</font>
{/if}
<br><br>
<p align="right" class="main_header_text">&copy;<a href="http://tufat.com/" target="_blank">TUFaT.com</a> </p>
{include file="$admingentemplates/admin_bottom.tpl"}