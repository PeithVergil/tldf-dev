{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
	<!-- begin main cell -->
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top" class="header" style="padding: 5px 0px 10px 0px;">{$lang.section.clubs}</td>
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
	{/if}
	<tr>
		<td valign="top" class="text">
			<div class="content" style=" margin: 0px;">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td class="header" style="padding: 10px 0px 0px 12px;" valign="top">{if $par eq 'my'}{$auth.login}&nbsp;{$lang.section.clubs}{elseif $par eq 'search'}{$lang.club.search_result}{else}{$clubs[0].category}{/if}</td>
			</tr>
			<tr>
				<td class="header" style="padding: 10px 0px 0px 12px;" valign="top">
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td><img src="{$site_root}{$template_root}/images/create_my_club.gif"></td>
						<td style="padding-left: 2px;"><a href="club.php?sel=create" {if $form.club_page eq '3'}class="text"{/if}>{$header_s.club_menu_3}</a></td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td><div style="height: 1px; margin: 10px 12px 15px 12px;" class="delimiter"></div></td>
			</tr>
			<tr>
				<td valign="top" style="padding-left: 15px; padding-bottom: 10px;">
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
					{section name=s loop=$clubs}
					<tr>{strip}
						<td width="{$form.icon_max_width+10}" valign="top"><a href="{$clubs[s].link}"><img src="{$clubs[s].icon_path}" class="icon" alt=""></a></td>
						<td style="padding-left: 10px;" valign="top" width="170">
							<div style="margin-top: 2px"><span class="text_head"><a href="{$clubs[s].link}">{$clubs[s].club_name}</a></span>&nbsp;&nbsp;&nbsp;<font class="hidden">{if $clubs[s].is_open eq 1}{$lang.club.public}{else}{$lang.club.private}{/if} {$lang.section.club}</font></div>
							<div style="margin-top: 2px"><font class="text">{$lang.club.club_category}:</font>&nbsp;<font class="text_head">{$clubs[s].category}</font></div>
							<div style="margin-top: 20px">
								{if $clubs[s].user_in_club eq 0}
									<input type="button" onclick="document.location.href='{$clubs[s].join_link}';" value="{$lang.club.join_club}">
								{else}<font style="color: #{$css_color.header};"><b>{$lang.club.joined}</b></font>
								{/if}
							</div>
							{if $clubs[s].user_in_club ne 0 && $clubs[s].user_is_leader ne '1'}
								<div style="margin-top: 20px"><a href="{$clubs[s].leave_link}">{$lang.club.leave_club}</a></div>
							{/if}
						</td>
						<td valign="top" width="90"><font style="font-family: Tahoma; font-size: 10px; color:#7f7f7f; text-transform: lowercase; padding-left: 10px; padding-right: 15px;">{$clubs[s].members_count}&nbsp;{$lang.club.members}</font></td>
						<td valign="top"><font style="font-family: Tahoma; font-size: 10px; color:#7f7f7f; padding-left: 10px;">{$clubs[s].description}</font></td>
			{/strip}</tr>
					{if !$smarty.section.s.last}
					<tr>
						<td colspan="4"><div style="height: 1px; margin: 20px 12px 15px 0px;" class="delimiter"></div></td>
					</tr>
					{/if}
					{/section}
					</table>
				</td>
			</tr>
			<tr>
				<td height="10"></td>
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