<table cellpadding="0" cellspacing="0" width="100%" border="0">
	<tr>
		<td class="red_sub_header">{if $par eq 'index'}{$lang.forum.forum_home}{elseif $par eq 'category'}{$data.category_name}{elseif $par eq 'subcategory'}{$data.subcategory_name}{elseif $par eq 'edit_subcategory'}{$lang.forum.edit_subcategory}{/if}
		</td>
	</tr>
</table>
<table cellpadding="0" cellspacing="0" width="100%" border="0">
	<tr>
		<td valign="top" style="padding-top: 5px;" class="main_content_text">
		{if $par eq 'category'}
			<a href="{$file_name}?sel=new_subcategory&amp;id_category={$data.category_id}">{$lang.forum.new_topic}</a>&nbsp;&nbsp;&nbsp;&nbsp;
		{/if}
		{if $par eq 'subcategory'}
			<a href="{$file_name}?sel=new_post&amp;id_subcategory={$data.subcategory_id}">{$lang.forum.new_post}</a>&nbsp;&nbsp;&nbsp;&nbsp;
		{/if}
		{if $par ne 'index'}<a href="{$file_name}"><b>{$lang.forum.forum_home}</b></a>{else}{$lang.forum.total_categories}:&nbsp;{$data.total_categories}{/if}
		{if $par ne 'index' && $par ne 'help' && $data.category_name}<font style="font-family: Arial, Verdana, Times New Roman; font-size: 14px;">&#8594;</font>&nbsp;{if $par eq 'subcategory' || $par eq 'new_subcategory' || $par eq 'new_post'|| $par eq 'quote'}<a href="{$file_name}?sel=category&amp;id_category={$data.category_id}">{/if}<b>{$data.category_name}{if $par eq 'subcategory' || $par eq 'new_subcategory' || $par eq 'new_post' || $par eq 'quote'}</a>{/if}</b>{/if}{if $par eq 'new_subcategory'}<font style="font-family: Arial, Verdana, Times New Roman; font-size: 14px;">&#8594;</font>&nbsp;<b>{$lang.forum.new_topic}</b>{/if}
		{if $par eq 'new_post'}
			<font style="font-family: Arial, Verdana, Times New Roman; font-size: 14px;">&#8594;</font>&nbsp;<b>{$lang.forum.new_post}</b>
		{/if}
		{if $par eq 'quote'}
			<font style="font-family: Arial, Verdana, Times New Roman; font-size: 14px;">&#8594;</font>&nbsp;<b>{$lang.forum.new_quote}</b>
		{/if}
		</td>
	</tr>
</table>