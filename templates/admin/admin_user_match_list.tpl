{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name} | {$lang.subsection.search_result}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.user_matches}</div>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	{if $form.err}
		<tr>
			<td>
				<div class="error_msg">{$form.err}</div>
			</td>
		</tr>
	{/if}
	{if $empty == 1}
		<tr>
			<td>
				<div class="error_msg">{$header_s.empty_result}</div>
			</td>
		</tr>
	{/if}
	{if $search_res}
		{strip}
		<tr>
			<td style="padding-bottom:8px;">
				<table width="100%" cellpadding="0" cellspacing="0">
					<tr>
						{*<!--<td width="50%" align="left"><div class="text">{$section.search_result}: <font class="text_head">{$form.pages_count} {$lang.pages}</font></div></td>-->*}
						<td align="center"><input type="button" class="button" onclick="javascript: document.location.href='{$back_link}';" value="{$button.back_to_search_form}"></td>
					</tr>
				</table>
			</td>
		</tr>
		{*<!--{if $form.view eq 'gallery'}-->*}
		<tr>
			<!-- begin results list -->
			<td valign="top">
				{foreach key=key item=item from=$search_res}
				<div style="width:138px; float: left; margin:3px; background-color:#eeeeee;">
					<table border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td valign="top">
								<div style="margin: 10px">
									<div style="margin-top: 2px">
										<a target="_blank" href="{$item.profile_link}"><img src="{$item.icon_path}" class="icon" alt="{$item.name}" /></a>
									</div>
									<div style="margin-top: 2px">
										<a target="_blank" href="{$item.profile_link}"><b>{$item.name}</b></a>
									</div>
									<div style="margin-top: 2px">
										{if $base_lang.city[$item.id_city]}{$base_lang.city[$item.id_city]}, {/if}{if $base_lang.region[$item.id_region]}{$base_lang.region[$item.id_region]}, {/if}{$base_lang.country[$item.id_country]}
									</div>
									<div style="margin-top: 2px">
										<b>{$item.age} {$lang.home_page.ans}</b>
									</div>
									<div style="margin-top: 2px">
										{$item.photo_count} {$lang.users.upload_1}
									</div>
									<div style="margin-top: 2px"><b>{$item.group}</b></div>
									<div style="margin-top: 2px">{$lang.users.gender_search}&nbsp;{$item.gender_search} {$lang.users.from} {$item.age_min} {$lang.users.to} {$item.age_max}</div>
								</div>
							</td>
						</tr>
					</table>
				</div>
				{/foreach}
				<div style="clear:both"></div>
			</td>
		</tr>
		{/strip}
		{*<!-- {/if} -->*}
		{*<!--{if $form.view neq 'gallery'}
		<tr>
			<td>
				{foreach key=key item=item from=$search_res name=s}
				<div style="margin:0px" {if $item.is_platinum_verified}class="plat_user"{/if}>
					<table border="0" cellspacing="0" cellpadding="0" width="100%">
						<tr>
							<td width="15" class="text" align="center" valign="middle">{$item.number}</td>
							<td style="padding-left: 5px;" valign="top">
								<table cellpadding="0" cellspacing="0" width="100%" border="0">
									<tr>
										<td width="{$icon_width+15}" valign="top"><a target="_blank" href="{$item.profile_link}"><img src="{$item.icon_path}" class="icon" alt=""></a></td>
										<td style="padding-left: 5px;" valign="top">
											<table cellpadding="0" cellspacing="0" width="100%" border="0">
												<tr>
													<td valign="top" width="95">
														<table cellpadding="0" cellspacing="0" width="100%" border="0">
															<tr>
																<td><a target="_blank" href="{$item.profile_link}"><b>{$item.name}</b></a></td>
															</tr>
															{if $base_lang.city[$item.id_city] || $base_lang.region[$item.id_region] || $base_lang.country[$item.id_country]}
															<tr>
																<td style="padding-top: 2px;">{if $base_lang.city[$item.id_city]}{$base_lang.city[$item.id_city]}, {/if}{if $base_lang.region[$item.id_region]}{$base_lang.region[$item.id_region]}, {/if}{$base_lang.country[$item.id_country]}</td>
															</tr>
															{/if}
															<tr>
																<td style="padding-top: 2px;"><font class="text_head">{$item.age} {$lang.home_page.ans}</font></td>
															</tr>
															<tr>
																<td style="padding-top: 2px;"><font class="text_hidden">{$item.photo_count} {$lang.users.upload_1}</font></td>
															</tr>
														</table>
													</td>
													<td valign="top" width="280" style="padding-left: 10px;">
														<table cellpadding="0" cellspacing="0" width="100%" border="0">
															<tr>
																<td>
																	{if $form.show_users_group_str eq '1'}<font class="text_head">{$item.group}</font>{/if}
																	&nbsp;&nbsp;&nbsp;
																	{if $item.status eq Online}
																		<span class="icon_on">Online</span>
																	{else}
																		<span class="icon_off">Offline</span>
																	{/if}
																</td>
															</tr>
															<tr>
																<td style="padding-top: 20px;"><font class="text_hidden">{$lang.users.gender_search}&nbsp;{$item.gender_search} {$lang.users.from} {$item.age_min} {$lang.users.to} {$item.age_max}</font></td>
															</tr>
														</table>
													</td>
													<td valign="top" width="40" align="right" style="padding-left:10px;">
														{if $item.connected_status == CS_CONNECTED}
															<img src="{$site_root}{$template_root}/images/connections_icon.gif" alt="{$lang.search.added_to_hotlist}">
														{elseif $item.hotlisted}
															<img src="{$site_root}{$template_root}/images/hotlist_icon.gif" alt="{$lang.search.added_to_hotlist}">
														{else}
															&nbsp;
														{/if}
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div>
				{if !$smarty.foreach.s.last}
				<div class="delimiter"></div>
				{/if}
			{/foreach}
			<td>
		</tr>
		{/if}
		-->*}
		<tr>
			<td style="padding-bottom:8px;">
				<table width="100%" cellpadding="0" cellspacing="0">
					<tr>
						{*<!--<td width="50%" align="left"><div class="text">{$section.search_result}: <font class="text_head">{$form.pages_count} {$lang.pages}</font></div></td>-->*}
						<td align="center"><input type="button" class="button" onclick="javascript: document.location.href='{$back_link}';" value="{$button.back_to_search_form}"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<div style="margin-left: 0px; padding-top: 15px;">
				{foreach item=item from=$links}
					<span style="padding-right: 15px;"><a href="{$item.link}" {if $item.selected eq '1'} class="text_head"{/if}>{$item.name}</a></span>
				{/foreach}
				</div>
			</td>
		</tr>
		<!-- end results list -->
	{else}
		<tr>
			<td align="center" style="padding-top:15px;">
				<div class="error_msg">{$lang.err.select_user}</div>
				<br /><br />
				<input type="button" class="button" onclick="javascript: document.location.href='{$back_link}';" value="{$button.back}">
			</td>
		</tr>
	{/if}
</table>
{include file="$admingentemplates/admin_bottom.tpl"}