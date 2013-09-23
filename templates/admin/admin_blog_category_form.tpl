{include file="$admingentemplates/admin_top.tpl"}
{strip}
<font class=red_header>{$lang.blog.admin.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$lang.blog.admin.categories_edit}</font><br><br>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{if $form.par eq 'edit'}{$help.blog_categories_edit}{else}{$help.blog_categories_add}{/if}</div>
<div>
<form method="post" action="{$form.action}" id="category_form" enctype="multipart/form-data">
<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
	<input type="hidden" name="par" value="{$form.par}">
	{if $form.par eq 'edit'}
	<input type="hidden" name="id" value="{$data.id}">
	{/if}
	<tr bgcolor="#ffffff">
		<td align="left" width="200" class="main_header_text">{$lang.blog.admin.category_name} <font class=main_error_text>*</font>:&nbsp;</td>
		<td align="left"><input type="text" name="category_name" value="{$data.category_name}" style="width: 195px;"></td>
	</tr>
	<tr bgcolor="#ffffff">
		<td colspan="2">
		<table cellpadding="3" cellspacing="0">
			<tr height="40">
				<td>{if $form.par eq 'edit'}<input type="submit" value="{$button.save}" class="button">{else}<input type="submit" value="{$button.add}" class="button">{/if}</td>
				<td><input type="button" value="{$button.back}" class="button" onclick="document.location.href='{$form.back}'"></td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</form>
</div>
{/strip}
{include file="$admingentemplates/admin_bottom.tpl"}