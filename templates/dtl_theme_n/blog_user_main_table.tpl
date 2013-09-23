{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
	<!-- begin main cell -->
	<table width="100%" border="0" cellpadding="0" cellspacing="1">
	<tr>
		<td valign="top">
			<div class="header" style="margin: 0px; height: 25px;"><div style="padding: 5px 0px">{$lang.section.blog}</div></div>
		</td>
	</tr>
	{if $form.err}
	<tr>
		<td><div class="error_msg">{$form.err}</div></td></tr>
	{/if}
	<tr>
		<td valign="top" class="text">
			{include file="$gentemplates/blog_menu.tpl"}
			<div class="content_2" style=" margin: 0px; padding-bottom: 15px;">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td valign="top" style="padding-left: 15px; padding-top: 15px;">
					{if $form.blog_page ne '3'}
					<table border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td><font class="text_head">{if $form.blog_page eq '4'}{$lang.blog.posts_in_category} {$form.category_name}{else}{$blog_info.title}{/if}</font></td>
						{if $form.blog_page eq '4'}
						<td style="padding-left: 50px;"><a href="blog.php?sel=all_blogs">{$lang.blog.to_categories_list}</a></td>
						{/if}
					</tr>
					{if $form.is_user eq 1}
					<tr>
						<td style="padding-top: 10px;"><font class="text_head"><a href="blog.php?sel=post">{$lang.blog.blog_menu_3}</font></td>
					</tr>
					{/if}
					</table>
					{/if}
					<table cellpadding="0" cellspacing="0" width="100%" border="0">
					{if $blog_posts ne 'empty'}
						{section name=p loop=$blog_posts}
						{if ($blog_posts[p].show eq 0 && $blog_info.is_user eq 1) || ($blog_posts[p].show eq 1)}
						<tr>
							<td colspan="2"><div style="height: 1px; margin: 15px 15px 10px 0px;" class="delimiter"></div></td>
						</tr>
					</table>
					<table cellpadding="0" cellspacing="0" width="100%" border="0">
						{if $form.blog_page eq 3}
						<tr>
							<td valign="top" width="{$form.icon_max_width}" height="{$form.icon_max_height}"><a href="{$blog_posts[p].profile_link}" target="_blank"><img src="{$blog_posts[p].comment_icon}" class="icon" alt=""></a></td>
							<td valign="top" style="padding-left: 10px;">
								<table cellpadding="0" cellspacing="0">
								<tr>
									<td valign="top" style="padding-bottom: 5px;"><font class="text_hidden">{$lang.blog.posted_at}&nbsp;{$blog_posts[p].creation_date}&nbsp;&nbsp;{$blog_posts[p].creation_time}{if $blog_posts[p].is_hidden eq 1}&nbsp;&nbsp;&nbsp;{$lang.blog.hidden_post}{/if}</font></td>
								</tr>
								<tr>
									<td style="padding-bottom: 5px;">{$lang.blog.post_title}:&nbsp;&nbsp;&nbsp;<font class="text_head"><a href="{$blog_posts[p].comments_link}">{$blog_posts[p].title}</a></font></td>
								</tr>
								<tr>
									<td>{$lang.blog.post_author}:&nbsp;&nbsp;&nbsp;<font class="text_head"><a href="{$blog_posts[p].profile_link}" target="_blank">{$blog_posts[p].login}</a></font></td>
								</tr>
								</table>
							</td>
						</tr>
						{else}
						<tr>
							<td valign="top" style="padding-bottom: 10px;">
							{if $blog_posts[p].login}<div>{$lang.blog.post_author}:&nbsp;<a href="vieprofile.php?id={$blog_posts[p].id_user}">{$blog_posts[p].login}</a></div>{/if}
							<div><font class="text_hidden">{$lang.blog.posted_at}&nbsp;{$blog_posts[p].creation_date}&nbsp;&nbsp;{$blog_posts[p].creation_time}{if $blog_posts[p].is_hidden eq 1}&nbsp;&nbsp;&nbsp;{$lang.blog.hidden_post}{/if}</font></div></td>
							<td valign="top" align="right" style="padding: 0px 15px 10px 0px;">{if $blog_posts[p].is_user eq '1'}<a href="{$blog_posts[p].edit_link}">{$lang.blog.edit_post}</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="{$blog_posts[p].delete_link}">{$lang.blog.delete_post}</a>{/if}</td>
						</tr>
						{/if}
					</table>
					<table cellpadding="0" cellspacing="0" width="100%" border="0">
						<tr>
							<td colspan="2">
								<table cellpadding="0" cellspacing="0" width="100%">
									{if $form.blog_page ne 3}
									<tr>
										<td class="text_head" style="padding-bottom: 5px;">{$blog_posts[p].title}</td>
									</tr>
									{/if}
									<tr>
										<td class="text" style="line-height: 1.5; {if $form.blog_page eq 3} padding-top: 10px; {/if} " >{$blog_posts[p].body}</td>
									</tr>
									<tr>
										<td style="padding-top: 5px;">{if $blog_posts[p].comments_count}<a href="{$blog_posts[p].comments_link}">{$blog_posts[p].comments_count} {$lang.blog.comments}</a>{else}<font class="text_hidden">{$lang.blog.no_comment}</font>{/if}&nbsp;&nbsp;{if $blog_posts[p].can_comment eq '1' || $blog_posts[p].is_user eq 1}|&nbsp;&nbsp;<a href="{$blog_posts[p].add_comments_link}">{$lang.blog.add_comment}</a>{/if}</td>
									</tr>
								</table>
							</td>
						</tr>
						{/if}
						{/section}
					{else}
					<tr>
						<td style="padding-top: 10px;" class="error">{if $form.blog_page eq '3'}{$lang.blog.no_friends}{elseif $form.blog_page eq '4'}{$lang.blog.there_are_no_post}{else}{$lang.blog.you_have_no_post}{/if}</td>
					</tr>
					{/if}
					</table>
				</td>
			</tr>
			</table>
			</div>
			<div style="height: 10px; margin: 0px"><img src="{$site_root}{$template_root}/images/empty.gif" height="10px" alt=""></div>
			{if $links}
			<div style="margin: 0px"><div style="margin-left: 10px">
			{foreach item=item from=$links}
				<div class="page_div{if $item.selected eq '1'}_active{/if}">
					<div style="margin: 5px"><a href="{$item.link}" class="page_link{if $item.selected eq '1'}_active{/if}">{$item.name}</a></div>
				</div>
			{/foreach}
			</div></div>
			{/if}
		</td>
	</tr>
	</table>
	<!-- end main cell -->
</td>
{include file="$gentemplates/index_bottom.tpl"}