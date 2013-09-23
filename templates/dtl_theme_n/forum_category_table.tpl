{if $forum.empty eq 1}
{$lang.forum.no_topics_found}
{else}
<table cellpadding="0" cellspacing="2" width="100%" border="0">
	<tr>
		<td height="27" width="15" align="center" style="text-transform: uppercase; padding-left: 13px; padding-right: 13px; background-color: #{$css_color.home_search};">&nbsp;</td>
		<td valign="middle" align="center" style="text-transform: uppercase; padding-left: 13px; padding-right: 13px; background-color: #{$css_color.home_search};"><b>{$lang.forum.topics}</b></td>
		<td width="1%" valign="middle" align="center" style="text-transform: uppercase; padding-left: 13px; padding-right: 13px; background-color: #{$css_color.home_search};"><b>{$lang.forum.author}</b></td>
		<td width="1%" valign="middle" align="center" style="text-transform: uppercase; padding-left: 13px; padding-right: 13px; background-color: #{$css_color.home_search};"><b>{$lang.forum.last_post_date}</b></td>
		<td width="1%" valign="middle" align="center" style="text-transform: uppercase; padding-left: 13px; padding-right: 13px; background-color: #{$css_color.home_search};"><b>{$lang.forum.posts}</b></td>
	</tr>
	{section name=f loop=$forum}
	<tr>
		<td height="27" align="center">
		{if $forum[f].new_posts eq 0  || $guest eq 1}
			<img src="{$site_root}{$template_root}/images/forum_read_posts.gif" border="0" alt="{$lang.forum.no_unread_posts}" vspace="0" hspace="0">
			{else}
			<img src="{$site_root}{$template_root}/images/forum_unread_posts.gif" border="0" alt="{$lang.forum.unread_posts}" vspace="0" hspace="0">
			{/if}
		</td>
		<td align="left" style="padding-left: 5px;">
			{if !($forum[f].new_posts eq 0  || $guest eq 1)}<b>{/if}
			<a href="{$file_name}?sel=subcategory&amp;id_subcategory={$forum[f].id}">{$forum[f].subcategory}</a>
			{if !($forum[f].new_posts eq 0  || $guest eq 1)}</b>{/if}
		</td>
		<td align="center" nowrap style="padding-left: 13px; padding-right: 13px;">{if $forum[f].poster_profile eq 1}<a href='viewprofile.php?id={$forum[f].poster_id}' target="_blank">{/if}{$forum[f].poster_login}{if $forum[f].poster_profile eq 1}</a>{/if}</td>
		<td align="center" nowrap style="padding-left: 13px; padding-right: 13px;">{$forum[f].date}</td>
		<td align="center" nowrap>{$forum[f].total_posts}</td>
	</tr>
	{/section}
</table>
{/if}