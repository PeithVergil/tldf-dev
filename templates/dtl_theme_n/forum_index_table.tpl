{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
	<table width="100%" border="0" cellpadding="0" cellspacing="1">
	{if $error}
	<tr>
		<td><div class="error_msg">{$error}</div></td>
	</tr>
	{/if}
	<tr>
		<td valign=top>
			<div style="margin: 0px; padding-bottom: 10px;">
			{include file="$gentemplates/forum_top.tpl"}
			</div>
			<div>
			{if $par eq 'index'}
			<table cellpadding="0" cellspacing="2" width="100%" border="0">
				<tr>
					<td height="27" width="15" align="center" style="text-transform: uppercase; padding-left: 13px; padding-right: 13px; background-color: #{$css_color.home_search};">&nbsp;</td>
					<td valign="middle" align="center" style="text-transform: uppercase; padding-left: 13px; padding-right: 13px; background-color: #{$css_color.home_search};"><b>{$lang.forum.categories}</b></td>
					<td width="1%" valign="middle" align="center" style="text-transform: uppercase; padding-left: 13px; padding-right: 13px; background-color: #{$css_color.home_search};"><b>{$lang.forum.topics}</b></td>
					<td width="1%" valign="middle" align="center" style="text-transform: uppercase; padding-left: 13px; padding-right: 13px; background-color: #{$css_color.home_search};"><b>{$lang.forum.posts}</b></td>
				</tr>
				{section name=f loop=$forum}
				<tr>
					<td height="27" align="center">
						{if $forum[f].new_posts eq 0 || $guest eq 1}
						<img src="{$site_root}{$template_root}/images/forum_read_posts.gif" border="0" alt="{$lang.forum.no_unread_posts}" vspace="0" hspace="0">
						{else}
						<img src="{$site_root}{$template_root}/images/forum_unread_posts.gif" border="0" alt="{$lang.forum.unread_posts}" vspace="0" hspace="0">
						{/if}
					</td>
					<td style="padding-left: 5px;"><a href="{$file_name}?sel=category&amp;id_category={$forum[f].id}">
					{if !($forum[f].new_posts eq 0  || $guest eq 1)}<b>{/if}
					{$forum[f].category}
					{if !($forum[f].new_posts eq 0  || $guest eq 1)}</b>{/if}
					</a></td>
					<td align="center">{$forum[f].total_subcategories}</td>
					<td align="center">{$forum[f].total_posts}</td>
				</tr>
				{/section}
			</table>
			{elseif $par eq 'help'}
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td class="header" style="padding-bottom: 10px;">{$lang.forum.forum_help}</td>
			</tr>
			<tr>
				<td>{$lang.forum.help_text}</td>
			</tr>
			</table>
			{elseif $par eq 'category'}
			{strip}
			{include file="$gentemplates/forum_category_table.tpl"}
			{/strip}
			{elseif $par eq 'new_subcategory' || $par eq 'new_post' || $par eq 'quote' || $par eq 'edit_post'}
			{include file="$gentemplates/forum_edit_form.tpl"}
			{elseif $par eq 'subcategory'}
			{strip}
			{include file="$gentemplates/forum_subcategory_table.tpl"}
			{/strip}
			{/if}
			</div>
		</td>
	</tr>
	</table>
	<!-- end main cell -->
</td>
{include file="$gentemplates/index_bottom.tpl"}