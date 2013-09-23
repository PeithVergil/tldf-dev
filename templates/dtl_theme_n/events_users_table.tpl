{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
	<!-- begin main cell -->
	<table width="100%" border="0" cellpadding="0" cellspacing="1">
	<tr>
		<td valign="top">
			<div class="header" style="margin: 0px; height: 25px;"><div style="padding: 5px 0px">{$header.event_users} {$event_name}</div></div>
		</td>
	</tr>
	<tr>
		<td valign="top" style="padding: 5px 0px 10px 0px;">
			<table cellpadding="0" cellspacing="0">
			<tr>
				<td width="16"><img src="{$site_root}{$template_root}/images/btn_back.gif" hspace="0" vspace="0" border="0" alt=""></td>
				<td style="padding-left: 2px;"><a href="events.php?sel=event&id_event={$id_event}">{$header.back_to_event}</a></td>
			</tr>
			</table>
		</td>
	</tr>
	{if $form.err}
	<tr>
		<td><div class="error_msg">{$form.err}</div></td></tr>
	{/if}
	<tr>
		<td valign="top" class="text">
			<div class="content" style=" margin: 0px;">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td valign="top" style="padding-left: 15px; padding-top: 15px;">
					<table width="100%" border="0" cellpadding="3" cellspacing="0">
					{foreach key=key item=item from=$search_res}
					<div style="margin: 0px">
					<table width="100%" border="0" cellpadding="5" cellspacing="0">
						<tr valign="middle">
							<td width="18px" class="text" align=center>{$item.number}</td>
							<td width="80px" class="text" align=center><a href="{$item.profile_link}"><img src="{$item.icon_path}" class="icon" alt=""></a></td>
							<td width="100%" class="text" valign="top">
									<div style="margin-top: 7px">
										<a href="{$item.profile_link}"><b>{$item.name}</b></a>&nbsp;&nbsp;&nbsp;
										{if $form.show_users_group_str eq '1'}<font class="text_head">{$item.group}</font>&nbsp;&nbsp;&nbsp;{/if}
										<font class="{if $item.status eq $lang.status.on}link{else}text{/if}_active">{$item.status}</font>&nbsp;
										{if $item.event_creator eq 1}&nbsp;&nbsp;<font class="text_head">{$header.event_creator}</font>{/if}
									</div>
									<div style="margin-top: 2px">
										<font class="text">{if $base_lang.city[$item.id_city]}{$base_lang.city[$item.id_city]}, {/if}{if $base_lang.region[$item.id_region]}{$base_lang.region[$item.id_region]}, {/if}{$base_lang.country[$item.id_country]}</font>
									</div>
									<div style="margin-top: 2px">
										<font class="text_head">{$item.age} {$lang.home_page.ans}</font>&nbsp;&nbsp;&nbsp;
										<font class="text">{$lang.users.gender_search}&nbsp;{$item.gender_search} {$lang.users.from} {$item.age_min} {$lang.users.to} {$item.age_max}</font>&nbsp;
									</div>
									<div style="margin-top: 2px">
										<font class="text_hidden">{$item.photo_count} {$lang.users.upload_1}</font>&nbsp;
									</div>
									<div style="margin-top: 2px">
										{*<!--
										<font class="text">{$lang.homepage.completion}:&nbsp;<img src="{$site_root}{$template_root}/images/bar.gif" height="10" width="{$item.completion}" alt="">&nbsp;{$item.completion}%</font>&nbsp;&nbsp;&nbsp;
										-->*}
										{if $form.show_users_connection_str eq '1'}<font class="text">{$lang.homepage.last_connection}: {$item.last_login}</font>{/if}
									</div>
									{if $form.show_users_comments eq '1'}
									<div style="margin-top: 2px">
										<font class="text_hidden" style="font-size:10px">{$item.annonce}</font>
									</div>
									{/if}
							</td>
						</tr>
						<tr valign="top">
							<td class="text" align=center>&nbsp;</td>
							<td colspan=2 class="content_active">
								<div style="margin-top: 2px; margin-bottom: 2px">&nbsp;
									{if $form.guest_user != 1}
									<a href="{$item.email_link}">{$button.gr_email}</a>&nbsp;&nbsp;&nbsp;
									<a href="#" onclick="javascript:window.open('{$item.im_link}', 'send{$item.id}', 'height=650, width=600, resizable=yes, scrollbars=no, menubar=no,status=no, left=200, top=20'); return false;">{$button.gr_im}</a>&nbsp;&nbsp;&nbsp;
									{/if}
								</div>
							</td>
						</tr>
					</table>
					</div>
					<div style="height: 10px; margin: 0px"><img src="{$site_root}{$template_root}/images/empty.gif" height="10px" alt=""></div>
					{/foreach}
					</table>
				</td>
			</tr>
			<tr>
				<td height="10">&nbsp;</td>
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