{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.list_option}: {$reference_name}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$top_help}</div>
<table cellSpacing="3" cellPadding="0">
<tr>
	{section name=s loop=$lang_link}
	<td align="center"><a href="#" onclick="javascript:window.open('{$lang_link[s].link}','lang_edit', 'height=600, resizable=yes, scrollbars=yes,width=800, menubar=no,status=no'); return false;" class=privacy_link>{$lang_link[s].name}</a></td>
	{if !$smarty.section.s.last}
	<td align="center" valign="middle" class="main_content_text">&nbsp;|&nbsp;</td>
	{/if}
	{/section}
</tr>
</table>
<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
<tr class="table_header">
	<td class="main_header_text" align="center" width="10">{$header.number}</td>
	<td class="main_header_text" align="center">{$header.option}</td>
	<td class="main_header_text" align="center" width="100">&nbsp;</td>
</tr>
{if $references}
{section name=spr loop=$references}
<tr bgcolor="#FFFFFF">
	<td class="main_content_text" align="center">{$references[spr].number}</td>
	<td class="main_content_text" align="center">{$references[spr].name}</td>
	<td class="main_content_text" align="center" width="100"><input type="button" value="{$button.delete}" class="button" onclick="javascript: location.href='{$references[spr].dellink}'"></td>
</tr>
{/section}
{else}
	<tr bgcolor="#FFFFFF" height="40">
	<td class="main_error_text" align="left" colspan="3" bgcolor="#FFFFFF">{$header.empty_option}</td>
</tr>
{/if}
</form>
</table>
<br>
<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
<form method="post" action="{$form.action}"  enctype="multipart/form-data" name=add_form>
{$form.hiddens}
<tr bgcolor="#FFFFFF">
	<td colspan=3 align="left" class="table_main"><font class="main_header_text">{$header.editform_opt}</font></td>
</tr>
{if $form.err}
<tr bgcolor="#FFFFFF">
	<td height="200%" colspan=3 align="left" class="main_error_text">{$form.err}</td>
</tr>
{/if}
<tr bgcolor="#FFFFFF">
	<td align="right" width="15%" class="main_header_text">{$header.new_option}:&nbsp;</td>
	<td class="main_content_text" align="left"><input type="text" name="name" value="{$name}" size=60></td>
	<td align="left" width="65%"><input type="button" value="{$header.add}" class="button" onclick="javascript: document.add_form.submit();"></td>
</tr>
</form>
</table><br>
<input type="button" value="{$header.back_to_ref}" class="button" onclick="javascript: location.href='{$back_link}'">
{include file="$admingentemplates/admin_bottom.tpl"}