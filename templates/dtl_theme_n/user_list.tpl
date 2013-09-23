{strip}
{foreach key=key item=item from=$search_res name=s}
	<div class="{if $item.is_verified}plat_user{else}reg_user{/if}">
		<table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td width="25" valign="top"><b>{$item.number}</b></td>
				<td class="user-icon"><a href="{$item.profile_link}"><img src="{$item.big_icon_path}" class="big_icon" alt="{$item.name}"> </a></td>
				<td style="padding-left:10px;" valign="top">
					<div style="min-height:128px;">
						<table cellpadding="0" cellspacing="0" width="100%" border="0">
							<tr>
								<td align="left" valign="top" width="220" class="main-info-search">
									<div><a href="{$item.profile_link}"><b>{$item.name}</b></a></div>
									{if $base_lang.city[$item.id_city] || $base_lang.region[$item.id_region] || $base_lang.country[$item.id_country]}
										<div style="padding-top: 2px;">{if $base_lang.city[$item.id_city]}{$base_lang.city[$item.id_city]}, {/if}{if $base_lang.region[$item.id_region]}{$base_lang.region[$item.id_region]}, {/if}{$base_lang.country[$item.id_country]}</div>
									{/if}
									<div style="padding-top: 2px;"><font class="text_head">{$item.age} {$lang.home_page.ans}</font></div>
									<div style="padding-top: 2px;"><font class="text_hidden">{$item.photo_count} {$lang.users.upload_1}</font></div>
								</td>
								<td valign="top" width="250" style="padding-left: 10px;">
									<div>
										{if $form.show_users_group_str eq '1'}<font class="text_head">{$item.group}</font>{/if}
										{if $item.status eq Online} <span class="icon_on">Online</span> {else} <span class="icon_off">Offline</span> {/if}
									</div>
									<div style="padding-top: 20px;">
										<font class="text_hidden">{$lang.users.gender_search} {$item.gender_search} {$lang.users.from} {$item.age_min} {$lang.users.to} {$item.age_max}</font>
									</div>
									{* <!--
									<div style="padding-top: 2px;"><font class="text">{$lang.homepage.completion}: {$item.completion}%</font></div>
									--> *}
								</td>
								<td valign="top" align="right" style="padding-left: 10px;">
									{if $form.show_users_comments && $item.annonce}
										<div class="text_hidden" style="padding-bottom:7px; font-size: 10px">{$item.annonce}</div>
									{/if}
									{foreach item=k from=$item.kisses}
										<div class="text" style="padding-bottom:7px;">
											{if $k.image_path}
												<img src="{$k.image_path}" alt="">&nbsp;
											{/if}
											{if $k.name}<b>{$k.name}</b> <sup>{$k.date}</sup>{else}{$header.kiss_date}: {$k.date}{/if}
										</div>
									{/foreach}
									{if $item.friend_type}
										<font class="text_head">{$item.friend_type}</font>
									{/if}
									{if $item.connected_status == CS_CONNECTED}
										<p style="margin:0px; padding:0px;">
											<img src="{$site_root}{$template_root}/images/connections_icon.png" alt="{$lang.search.added_to_hotlist}">
										</p>
										{if $item.del_connection_link}
											<p style="margin:0 0 15px; padding:0px;">
												<b><a href="javascript:DeleteConnection('{$item.del_connection_link}');">{$lang.user_link.remove}</a></b>
											</p>
										{else}
											<br>
										{/if}
									{/if}
									{if $form.sel == 'inbox' && $form.friend_login == ''}
										<p style="margin:0 0 10px; padding:0px">
											<input type="button" class="btn-green" value="Accept" onclick="window.location.href='{$item.accept_link}'">
											&nbsp;<input type="button" class="btn-red" value="No, Thanks" onclick="DeleteInviteReceived('{$item.del_connection_link}');">
										</p>
									{elseif $form.sel == 'outbox'}
										<p style="margin:0 0 10px; padding:0px;">
											<input type="button" class="btn-orng" value="Connection Pending" style="cursor:default;" {* onclick="window.location.href='{$item.del_connection_link}'" *}>
										</p>
									{/if}
									{if $item.hotlisted}
										<p style="margin:0 0 10px; padding:0px;">
											<img src="{$site_root}{$template_root}/images/hotlist_icon.png" alt="{$lang.search.added_to_hotlist}">
										</p>
									{/if}
								</td>
							</tr>
						</table>
					</div>
					<div style="padding-top:15px;">
						{include file="$gentemplates/user_links.tpl"}
					</div>
				</td>
			</tr>
		</table>
	</div>
	{* <!--
	{if !$smarty.foreach.s.last}
		<div class="delimiter"></div>
	{/if}
	--> *}
{/foreach}
<script type="text/javascript">
{literal}
function DeleteConnection(delUrl)
{
	jConfirm(
		"{/literal}{$lang.confirm.delete_connection}{literal}",
		"{/literal}{$lang.confirm.delete_connection_title}{literal}",
		function(result) { if (result) DeleteConnectionRe(delUrl); }
	);
}

function DeleteConnectionRe(delUrl)
{
	jConfirm(
		"{/literal}<b>{$lang.confirm.delete_warning}</b><br><br>{$lang.confirm.delete_connection_re}{literal}",
		"{/literal}{$lang.confirm.delete_connection_title}{literal}",
		function(result) { if (result) window.location.href = delUrl; }
	);
}

function DeleteInviteReceived(delUrl)
{
	jConfirm(
		"{/literal}{$lang.confirm.delete_invite_receive}{literal}",
		"{/literal}{$lang.confirm.delete_invite_receive_title}{literal}",
		function(result) { if (result) window.location.href = delUrl; }
	);
}
{/literal}
</script>
{/strip}
