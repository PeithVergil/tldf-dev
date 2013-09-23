{if $forum.empty eq 1}
<span style="padding-left: 13px;">{$lang.forum.no_topics_found}</span>
{else}
<table cellpadding="0" cellspacing="2" width="100%" border="0">
	<tr>
		<td height="27" valign="middle" align="center" style="text-transform: uppercase; padding-left: 13px; padding-right: 13px; background-color: #{$css_color.home_search};"><b>{$lang.forum.topics}</b></td>
		<td width="50" valign="middle" align="center" style="text-transform: uppercase; padding-left: 13px; padding-right: 13px; background-color: #{$css_color.home_search};"><b>{$lang.forum.edit}</b></td>
		<td width="50" valign="middle" align="center" style="text-transform: uppercase; padding-left: 13px; padding-right: 13px; background-color: #{$css_color.home_search};"><b>{$lang.forum.delete}</b></td>
		<td width="1%" valign="middle" align="center" style="text-transform: uppercase; padding-left: 13px; padding-right: 13px; background-color: #{$css_color.home_search};"><b>{$lang.forum.author}</b></td>
		<td width="1%" valign="middle" align="center" style="text-transform: uppercase; padding-left: 13px; padding-right: 13px; background-color: #{$css_color.home_search};"><b>{$lang.forum.last_post_date}</b></td>
		<td width="1%" valign="middle" align="center" style="text-transform: uppercase; padding-left: 13px; padding-right: 13px; background-color: #{$css_color.home_search};"><b>{$lang.forum.posts}</b></td>
	</tr>
	{section name=f loop=$forum}
	<tr>
		<td height="27" align="left" style="padding-left: 13px;"><a href="{$file_name}?sel=subcategory&amp;id_subcategory={$forum[f].id}">{$forum[f].subcategory}</a></td>
		<td align="center" style="padding-left: 13px; padding-right: 13px; padding-top: 5px;"><input type="button" onclick="document.location.href='{$file_name}?sel=edit_subcategory&id_subcategory={$forum[f].id}';" value="{$lang.button.edit}"></td>
		<td align="center" style="padding-left: 13px; padding-right: 13px; padding-top: 5px;"><input type="button" onclick="{literal}if (confirm('{/literal}{$lang.forum.del_confirm}{literal}')) {document.location.href='admin_forum.php?sel=del_subcategory&id_subcategory={/literal}{$forum[f].id}{literal}';}{/literal}" value="{$lang.button.delete}"></td>
		<td align="center" nowrap style="padding-left: 13px; padding-right: 13px;">{if $forum[f].poster_profile eq 1}<a href='admin_users.php?sel=edit&id={$forum[f].poster_id}' target="_blank">{/if}{$forum[f].poster_login}{if $forum[f].poster_profile eq 1}</a>{/if}</td>
		<td align="center" nowrap style="padding-left: 13px; padding-right: 13px;">{$forum[f].date}</td>
		<td align="center" nowrap>{$forum[f].total_posts}</td>
	</tr>
	{/section}
</table>
{/if}