<form method="post" action="admin_forum.php" style="margin: 0px; padding: 0px;">
<input type="hidden" name="sel" id="sel" value="save_edited_subcategory">
<input type="hidden" name="subcategory_id" id="subcategory_id" value="{$data.subcategory_id}">
<input type="hidden" name="id_user" id="id_user" value="{$data.id_user}">
<input type="hidden" name="created_date" id="created_date" value="{$data.created_date}">
<input type="hidden" name="category_id" id="category_id" value="{$data.category_id}">
<table cellpadding="5" cellspacing="0">
<tr>
	<td class="main_header_text">{$lang.forum.topic_title}:&nbsp;</td>
	<td class="main_content_text"><input type="text" name="subcategory_name" value="{$data.subcategory_name}"></td>
</tr>
<tr>
	<td colspan="2">
	<input type="submit" value="{$lang.button.save}">&nbsp;
	<input type="button" value="{$lang.button.back}" onclick="document.location.href='{$file_name}?sel=category&id_category={$data.category_id}';">
	</td>
</tr>
</table>
</form>