{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
	<!-- begin main cell -->
	<table width="100%" border="0" cellpadding="0" cellspacing="1">
	<tr>
		<td valign="top">
			<div class="header" style="margin: 0px; height: 25px;"><div style="padding: 5px 0px">{$lang.section.club_photos} {$lang.club.in_category} {$club_name}</div></div>
		</td>
	</tr>
	<tr>
		<td valign="top" style="padding: 5px 0px 10px 0px;">
			<table cellpadding="0" cellspacing="0">
			<tr>
				<td width="16"><img src="{$site_root}{$template_root}/images/btn_back.gif" hspace="0" vspace="0" border="0" alt=""></td>
				<td style="padding-left: 2px;"><a href="club.php">{$lang.club.back_to_all_club}</a></td>
			</tr>
			</table>
		</td>
	</tr>
	{if $form.err}
	<tr>
		<td><div class="error_msg">{$form.err}</div></td>
	</tr>
	{/if}
	<tr>
		<td valign="top" class="text">
			<div class="content" style=" margin: 0px;">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td valign="top" style="padding-left: 15px; padding-top: 15px;"><a href="{$back_link}">{$lang.club.back_to_club}</a></td>
			</tr>
			<tr>
				<td valign="top" style="padding-left: 15px; padding-top: 15px;">
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
					{if $par eq 'photos'}
						{section name=u loop=$club_photos}
						{if $smarty.section.u.index is div by 4}<tr>{/if}
						<td style="padding-bottom: 15px;">
						<div>{if $club_photos[u].view_link}<a href="#" onclick="javascript: window.open('{$club_photos[u].view_link}','photo_view','menubar=0, resizable=1, scrollbars=0,status=0,toolbar=0, width=800,height=600');return false;">{/if}<img src="{$club_photos[u].upload_thumb_path}" class="icon" alt="">{if $club_photos[u].view_link}</a>{/if}</div>
						{if $user_is_leader eq 1}<div style="padding-top: 3px;"><input type="button" value="{$button.delete}" onclick="document.location.href='{$club_photos[u].del_link}';"></div>{/if}
						</td>
						{if $smarty.section.u.index_next is div by 4 || $smarty.section.u.last}</tr>{/if}
						{/section}
					{else}
						{section name=n loop=$club_news}
						<tr>
							<td style="padding-top: 5px;" class="text_head">{$club_news[n].news_name}</td>
						</tr>
						<tr>
							<td><font class="text_hidden">{$club_news[n].creation_date}</font></td>
						</tr>
						<tr>
							<td><font class="text">{$club_news[n].news_text}</font></td>
						</tr>
						<tr>
							<td><div style="height: 1px; margin: 5px 10px 5px 0px;" class="delimiter"></div></td>
						</tr>
						{/section}
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