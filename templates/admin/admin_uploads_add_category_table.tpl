{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{if $form.par eq 'add_category'}{$header.add_category}{elseif $form.par eq 'edit_category'}{$header.edit_category}{/if}</font><br><br>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{if $form.par eq 'edit_category'}{$help.uploads_save_category}{else}{$help.uploads_add_category}{/if}</div>
<div>
<form method="post" action="{$form.action}" name="category_form" id="category_form" enctype="multipart/form-data">
<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
	<input type="hidden" name="par" value="{$form.par}">
	{if $form.par eq 'edit_category'}
	<input type="hidden" name="id" value="{$data.id}">
	{/if}
	<tr bgcolor="#ffffff">
		<td align="left" width="200" class="main_header_text">{$header.category_name} <font class=main_error_text>*</font>:&nbsp;</td>
		<td align="left"><input type="text" name="category_name" value="{$data.category_name}" style="width: 195px;"></td>
	</tr>
	<tr bgcolor="#ffffff">
		<td colspan="2">
		<table cellpadding="3" cellspacing="0">
			<tr height="40">
				{if $form.par eq 'edit_category'}
				<td><input type="button" value="{$button.save}" class="button" onclick="javascript: document.category_form.submit()"></td>
				{else}
				<td><input type="button" value="{$button.add}" class="button" onclick="javascript: document.category_form.submit()"></td>
				{/if}
				<td><input type="button" value="{$button.back}" class="button" onclick="javascript: document.location.href='{$form.back}'"></td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</form>
</div>
{include file="$admingentemplates/admin_bottom.tpl"}