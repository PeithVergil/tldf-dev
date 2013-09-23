{include file="$admingentemplates/admin_top.tpl"}
{strip}
<font class=red_header>{$header.name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.categories_edit}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.cards_categories_edit}</div>
<div>
<form method="post" action="{$form.action}" enctype="multipart/form-data">
<input type="hidden" name="par" value="{$form.par}">
{if $form.par eq 'edit'}
<input type="hidden" name="id" value="{$data.id}">
{/if}
<table class="table_main" cellspacing=1 cellpadding=5 width="100%">
	<tr bgcolor="#ffffff">
		<td width="200" class="main_header_text">{$header.category_name} <font class=main_error_text>*</font>:&nbsp;</td>
		<td><input name="category_name" value="{$data.category_name}" style="width: 195px;"></td>
	</tr>
	<tr bgcolor="#ffffff">
		<td class="main_header_text" valign="top">{$header.category_descr} <font class=main_error_text>*</font>:&nbsp;</td>
		<td><textarea name="category_descr" rows="7" style="width: 250px;">{$data.category_descr}</textarea></td>
	</tr>
	<tr bgcolor="#ffffff">
		<td class="main_header_text" valign="top">{$header.category_image}:&nbsp;</td>
		<td>
			<div>{if $data.category_image}<img src="{$data.category_image}" height="100" width="100">{else}{$header.no_image}{/if}</div>
			<div style="padding-top: 5px;"><input type="file" name="category_image"></div>
		</td>
	</tr>
	<tr bgcolor="#ffffff">
		<td>&nbsp;</td>
		<td>
			{if $form.par eq 'edit'}<input type="submit" value="{$button.save}" class="button">{else}<input type="submit" value="{$button.add}" class="button">{/if}&nbsp;&nbsp;
			<input type="button" value="{$button.back}" class="button" onclick="document.location.href='{$form.back}'">
		</td>
	</tr>
</table>
</form>
</div>
{/strip}
{include file="$admingentemplates/admin_bottom.tpl"}