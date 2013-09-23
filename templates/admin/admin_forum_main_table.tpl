{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$lang.forum.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$lang.forum.administration}</font><br><br>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.forum_administration}</div>
<div>
{strip}
<table width="100%" border="0" cellpadding="0" cellspacing="1">
<tr>
	<td valign=top>
		<div style="margin: 0px; padding-bottom: 10px; padding-left: 13px;">
		{include file="$admingentemplates/admin_forum_top.tpl"}
		</div>
		<div>
		{if $par eq 'index'}
		<table cellpadding="0" cellspacing="2" width="100%" border="0">
			<tr>
				<td height="27"  valign="middle" align="left" style="text-transform: uppercase; padding-left: 13px; padding-right: 13px; background-color: #{$css_color.home_search};"><b>{$lang.forum.categories}</b></td>
				<td width="50" valign="middle" align="center" style="text-transform: uppercase; padding-left: 13px; padding-right: 13px; background-color: #{$css_color.home_search};"><b>{$lang.forum.edit}</b></td>
				<td width="50" valign="middle" align="center" style="text-transform: uppercase; padding-left: 13px; padding-right: 13px; background-color: #{$css_color.home_search};"><b>{$lang.forum.delete}</b></td>
				<td width="60" valign="middle" align="center" style="text-transform: uppercase; padding-left: 13px; padding-right: 13px; background-color: #{$css_color.home_search};"><b>{$lang.forum.actions}</b></td>
				<td width="1%" valign="middle" align="center" style="text-transform: uppercase; padding-left: 13px; padding-right: 13px; background-color: #{$css_color.home_search};"><b>{$lang.forum.topics}</b></td>
				<td width="1%" valign="middle" align="center" style="text-transform: uppercase; padding-left: 13px; padding-right: 13px; background-color: #{$css_color.home_search};"><b>{$lang.forum.posts}</b></td>
			</tr>
			{section name=f loop=$forum}
			<tr>
				<td height="27" class="main_content_text" align="left" style="padding-left: 13px; padding-right: 13px; padding-top: 5px; "><a href="{$file_name}?sel=category&amp;id_category={$forum[f].id}">{$forum[f].category}</a></td>
				<td align="center" style="padding-left: 13px; padding-right: 13px; padding-top: 5px;"><input type="button" onclick="document.location.href='{$file_name}?sel=edit_category&id_category={$forum[f].id}';" value="{$lang.button.edit}"></td>
				<td align="center" style="padding-left: 13px; padding-right: 13px; padding-top: 5px;"><input type="button" onclick="{literal}if (confirm('{/literal}{$lang.forum.del_confirm}{literal}')) {document.location.href='admin_forum.php?sel=del_category&id_category={/literal}{$forum[f].id}{literal}';}{/literal}" value="{$lang.button.delete}"></td>
				<td align="center" class="main_content_text" style="padding-left: 13px; padding-right: 13px; padding-top: 5px;"><a href="#" onclick="document.location.href='{$file_name}?sel=move_up&id_category={$forum[f].id}'; return false;">Move Up</a><br><a href="#" onclick="document.location.href='{$file_name}?sel=move_down&id_category={$forum[f].id}'; return false;">Move Down</a></td>
				<td align="center" class="main_content_text" style="padding-top: 5px;">{$forum[f].total_subcategories}</td>
				<td align="center" class="main_content_text" style="padding-top: 5px;">{$forum[f].total_posts}</td>
			</tr>
			{/section}
		</table>
		<table cellpadding="0" cellspacing="2" width="100%" border="0">
			<tr>
				<td><input type="button" onclick="document.location.href='admin_forum.php?sel=add_category';" value="{$lang.forum.add_category}"></td>
			</tr>
		</table>
		{elseif $par eq 'category'}
		{include file="$admingentemplates/admin_forum_category_table.tpl"}
		{elseif $par eq 'new_subcategory' || $par eq 'new_post' || $par eq 'quote' || $par eq 'edit_post'}
		{include file="$admingentemplates/admin_forum_edit_form.tpl"}
		{elseif $par eq 'subcategory'}
		{include file="$admingentemplates/admin_forum_subcategory_table.tpl"}
		{elseif $par eq 'edit_subcategory'}
		{include file="$admingentemplates/admin_forum_edit_subcategory_table.tpl"}
		{elseif $par eq 'new_category' || $par eq 'edit_category'}
		{include file="$admingentemplates/admin_forum_edit_category_table.tpl"}
		{/if}
		</div>
	</td>
</tr>
</table>
<!-- end main cell -->
{/strip}
</div>
{include file="$admingentemplates/admin_bottom.tpl"}