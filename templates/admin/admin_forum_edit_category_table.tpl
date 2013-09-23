<form method="post" action="admin_forum.php" style="margin: 0px; padding: 0px;">
{if $data.id_category>0}
<input type="hidden" name="sel" id="sel" value="save_edited_category">
<input type="hidden" name="category_id" id="category_id" value="{$data.id_category}">
{else}
<input type="hidden" name="sel" id="sel" value="save_new_category">
{/if}
<table cellpadding="5" cellspacing="0">
<tr>
	<td class="main_header_text">{$lang.forum.category_name}:&nbsp;</td>
	<td class="main_content_text"><input type="text" name="category_name" value="{$data.category_name}"></td>
</tr>
<tr>
	<td class="main_header_text">{$lang.forum.category_description}:&nbsp;</td>
	<td class="main_content_text"><textarea name="category_description" style="width: 250px; height: 60px;">{$data.category_description}</textarea></td>
</tr>
<tr>
	<td class="main_header_text">{$lang.forum.category_sorter}:&nbsp;</td>
	<td class="main_content_text">
	<select name="category_sorter" id="category_sorter">
		{foreach item=item from=$data.sorter}
		<option value="{$item.value}" {if $item.sel eq 1}selected{/if}>{$item.value}</option>
		{/foreach}
	</select>
	</td>
</tr>
<tr>
	<td colspan="2">
	<input type="submit" value="{$lang.button.save}">&nbsp;
	<input type="button" value="{$lang.button.back}" onclick="document.location.href='admin_forum.php';">
	</td>
</tr>
</table>
</form>