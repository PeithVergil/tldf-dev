{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
	<!-- begin main cell -->
	<table width="100%" border="0" cellpadding="0" cellspacing="1">
	<tr valign=top><td valign=top>
			<div class="header">{$lang.section.perfect_match}</div>
			<div class="sep"></div>
	</td></tr>
	<tr><td class="text">{$header_s.toptext_perfect}<br>&nbsp;</td></tr>
	{if $form.err}
    	<tr><td><div class="error_msg">{$form.err}</div></td></tr>
	{/if}
	{if $empty eq 1}
		<tr><td<div class="error_msg">{$header_s.empty_result}</div></td></tr>
	{elseif $empty eq 2}
		<tr><td><div class="error_msg">{$form.not_fill}&nbsp;<a href="{$form.perfect_link}"><b>{$header_s.perfect_match}</b></a></div></td></tr>
	{/if}

	{if $empty ne 1 && $empty ne 2}
	<tr><td>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
		<td><div class="text">{$section.search_result}: <font class="text_head">{$form.pages_count} {$lang.pages}</font></div></td>
		<td align="right">{strip}
			<div style="margin-top: 2px; margin-bottom: 2px">&nbsp;
				<a href="{$form.view_online_link}">{$lang.search.view_online} ({$form.online_count})</a>&nbsp;&nbsp;|&nbsp;&nbsp;
				<a href="{$form.view_photo_link}">{$lang.search.view_photo} ({$form.with_count})</a>&nbsp;&nbsp;|&nbsp;&nbsp;
				<a href="{$form.view_all_link}">{$lang.search.view_all} ({math equation='x+y' x=$form.online_count y=$form.offline_count})</a>&nbsp;&nbsp;|&nbsp;&nbsp;
				{if $form.view eq 'gallery'}
				<a href="{$form.view_list_link}">{$lang.search.view_list}</a>
				{else}
				<a href="{$form.view_gallery_link}">{$lang.search.view_gallery}</a>
				{/if}
			</div>{/strip}
		</td>
		</tr>
		</table>
	</td></tr>

	<tr valign=top>
		<!-- begin results list -->
		<td valign=top>
			<div style="height: 10px; margin: 0px"><img src="{$site_root}{$template_root}/images/empty.gif" height="5px" alt=""></div>
			{if $form.view eq 'gallery'}
			{foreach key=key item=item from=$search_res}
			<div class="content" style="width: 120px; float: left; margin: 5px">
				<table><tr><td height="210px" valign="top">
				<div style="margin: 10px">
					<div style="margin-top: 2px">
						<a href="{$item.profile_link}"><img src="{$item.icon_path}" class="icon" alt=""></a>
					</div>
					<div style="margin-top: 2px">
						<a href="{$item.profile_link}"><b>{$item.name}</b></a>
					</div>
					<div style="margin-top: 2px">
						<font class="text">{if $base_lang.city[$item.id_city]}{$base_lang.city[$item.id_city]}, {/if}{if $base_lang.region[$item.id_region]}{$base_lang.region[$item.id_region]}, {/if}{$base_lang.country[$item.id_country]}</font>
					</div>
					<div style="margin-top: 2px">
						<font class="text"><b>{$item.age} {$lang.home_page.ans}</b></font>
					</div>
					<div style="margin-top: 2px">
						<font class="text_hidden">{$item.photo_count} {$lang.users.upload_1}</font>
					</div>
				</div>
				</td></tr></table>
			</div>
			{/foreach}
			<div style="clear:both"></div>
			{else}
			{foreach key=key item=item from=$search_res name=s}
			<div style="margin: 0px">
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td width="15" class="text" align="center" valign="middle">{$item.number}</td>
					<td style="padding-left: 5px;" valign="top">
						<table cellpadding="0" cellspacing="0" width="100%" border="0">
						<tr>
							<td width="{$icon_width+15}" valign="top"><a href="{$item.profile_link}"><img src="{$item.icon_path}" class="icon" alt=""></a></td>
							<td style="padding-left: 5px;" valign="top">
								<table cellpadding="0" cellspacing="0" width="100%" border="0">
								<tr>
									<td valign="top" width="95">
										<table cellpadding="0" cellspacing="0" width="100%" border="0">
										<tr>
											<td><a href="{$item.profile_link}"><b>{$item.name}</b></a></td>
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
									<td valign="top" width="170" style="padding-left: 10px;">
										<table cellpadding="0" cellspacing="0" width="100%" border="0">
										<tr>
											<td>
												{if $form.show_users_group_str eq '1'}<font class="text_head">{$item.group}</font>&nbsp;&nbsp;&nbsp;{/if}
												<font class="{if $item.status eq $lang.status.on}link{else}text{/if}">{$item.status}</font>
											</td>
										</tr>
										<tr>
											<td style="padding-top: 20px;"><font class="text_hidden">{$lang.users.gender_search}&nbsp;{$item.gender_search} {$lang.users.from} {$item.age_min} {$lang.users.to} {$item.age_max}</font></td>
										</tr>
										{*<!--
										<tr>
											<td style="padding-top: 2px;"><font class="text">{$lang.homepage.completion}:&nbsp;{$item.completion}%</font></td>
										</tr>
										-->*}
										<tr>
											<td style="padding-top: 2px;"><font class="text">{$lang.matches.concurrence}:&nbsp;{$item.percent}%</font></td>
										</tr>
										</table>
									</td>
									<td valign="top" width="180" align="left" style="padding-left: 10px;">
									{if $form.show_users_comments eq '1'}
										<font class="text_hidden" style="font-size: 10px">{$item.annonce}</font>
									{/if}
									</td>
									<td valign="top" width="40" align="right" style="padding-left: 10px;">
									{if $item.hotlisted}
										<img src="{$site_root}{$template_root}/images/hotlist_icon.gif" alt="{$lang.search.added_to_hotlist}">
									{else}&nbsp;{/if}
									</td>
								</tr>
								<tr>
									<td colspan="4" align="top" style="padding-top:15px;">
										{include file="$gentemplates/user_links.tpl"}
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
			<div style="height: 1px; margin: 15px 0px 15px 0px;" class="delimiter"></div>
			{/if}
			{/foreach}
			{/if}
			<div style="margin-left: 0px; padding-top: 15px;">
			{foreach item=item from=$links}
				<span style="padding-right: 15px;"><a href="{$item.link}" {if $item.selected eq '1'} class="text_head"{/if}>{$item.name}</a></span>
			{/foreach}
			</div>
		</td>
		<!-- end results list -->
	</tr>
	{/if}
	</table>
	<!-- end main cell -->
</td>
{include file="$gentemplates/index_bottom.tpl"}