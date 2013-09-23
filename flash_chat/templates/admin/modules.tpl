{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.list}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.modules}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.chat_module}</div>
{if $modules}
<form name="modules" id="modules" action="modules.php" method="post" style="margin: 0px;">
<input type="hidden" name="sel" value="save">
<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="400">
<tr class="table_header">
	<td class="main_header_text" align="center">{$header.modules_title.num}</td>
	<td class="main_header_text" align="center">{$header.modules_title.name}</td>
	<td class="main_header_text" align="center">{$header.modules_title.status}</td>
	<td class="main_header_text" align="center">&nbsp;</td>
</tr>
{foreach item=item from=$modules}
<tr bgcolor="#FFFFFF">
	<td class="main_content_text" align="center">{$item.num}</td>
	<td class="main_content_text" align="center">{$item.title}</td>
	<td class="main_content_text" align="center"><input type="checkbox" name="status[]" value="{$item.id}" {if $item.status eq 1} checked {/if}></td>
	<td class="main_content_text" align="center"><input type="button" onclick="document.location.href='modules.php?sel=edit&id_module={$item.id}'" value="{$lang.button.edit}"></td>
</tr>
{/foreach}
<tr bgcolor="#FFFFFF">
	<td colspan="2"></td>
	<td align="center"><input type="submit" value="{$lang.button.save}"></td>
	<td>&nbsp;</td>
</tr>
</table>
</form>
{else}
	<font class="main_error_text">{$header.err_no_modules_found}</font>
{/if}
<br><br>
<p align="right" class="main_header_text">&copy;<a href="http://tufat.com/" target="_blank">TUFaT.com</a> </p>
{include file="$admingentemplates/admin_bottom.tpl"}