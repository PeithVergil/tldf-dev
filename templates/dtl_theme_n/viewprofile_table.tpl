{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple-small">
	{if $search_link}
		<div style="padding-bottom:20px;">
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td style="padding-right:10px;">
						{if $search_link.back_link}
							<span class="basic-btn_here"><b>&nbsp;</b><span><input type="button" onclick="window.location.href='{$search_link.back_link}';" value="{$button.back_to_list[$smarty.get.search_type]}"></span></span>
						{/if}
					</td>
					<td style="padding-right:10px;">
						{if $search_link.prev_link}
							<span class="basic-btn_back"><b>&nbsp;</b><span><input type="button" onclick="window.location.href='{$search_link.prev_link}';" value="{$button.gr_prev}" style="width:110px;"></span></span>
						{else}
							<span class="basic-btn_back" style="opacity:0.5"><b>&nbsp;</b><span><input type="button" value="{$button.gr_prev}" style="width:110px;"></span></span>
						{/if}
					</td>
					<td>
						{if $search_link.next_link}
							<span class="basic-btn_next"><span><input type="button" onclick="window.location.href='{$search_link.next_link}';" value="{$button.gr_next}" style="width:110px;"></span><b>&nbsp;</b></span>
						{else}
							<span class="basic-btn_next" style="opacity:0.5"><span><input type="button" value="{$button.gr_next}" style="width:110px;"></span><b>&nbsp;</b></span>
						{/if}
					</td>
				</tr>
			</table>
		</div>
	{/if}
	{if $form.err}
		<div class="error_msg">{$form.err}</div>
	{/if}
	<div class="main-fixed-2 tcxf-ch-la">
		<div>
			<div class="tcxf-ch-la">
				<div class="user-photo">
					<h3>{$data_1.fname}</h3>
					{if $data.icon_path}
						<img src="{$data.icon_path}" border="0" alt="{$data_1.fname}">
					{/if}
				</div>
				<div style="padding-left:10px;">
					<div class="hdr2" style="padding-bottom:7px;">{$lang.section.photos} ({$data.photo_count})</div>
					<div>
						<div id="colorbox-wrap">
							<ul class="colorbox_list">
								<li class="nailthumb-item"><a class="photo_slider_colorbox" href="{$data.full_icon_path}"><img src="{$data.full_icon_path}" /></a></li>
								{foreach key=key item=item from=$data.big_photo_path}
									<li class="nailthumb-item"><a class="photo_slider_colorbox" href="{$item.path}"><img src="{$item.path}" /></a></li>
								{/foreach}
							</ul>
							<div class="clear"></div>
						</div>
					</div>
					<div class="clear"></div>
					<div class="hdr2" style="padding-bottom:7px; margin-top:20px;">{$lang.section.videos} ({$data.video_count})</div>
					<div>
						<div id="colorbox-wrap-video">
							<ul class="video-slider">
								{foreach key=key item=item from=$data.video}
									<li><a class="video_slider_colorbox" href="{$item.view_link}"><img src="{$item.thumb_path}" /></a></li>
								{/foreach}
							</ul>
							<div class="clear"></div>
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
				<div class="user-status">
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td width="85" align="center">
								{if $data_1.status == 'Online'}
									<span class="online">Online</span>
								{else}
									<span class="offline">Offline</span>
								{/if}
							</td>
							<td>
								{*<!--{$lang.profile.user_group}:<br/>-->*} <b>{$data.group}</b>
							</td>
							<td width="115" align="center">
								<label title="{$lang.profile.chk_background_info}"> {$lang.profile.chk_background}:<br />
									{if $data.chk_background == 'VR'}<span class="verified">{$lang.profile.background_check.verified}</span>{/if}
									{if $data.chk_background == 'PE'}<span class="pending">{$lang.profile.background_check.pending}</span>{/if}
									{if $data.chk_background == 'NR'}<span class="not_requested">{$lang.profile.background_check.not_yet_requested}</span>{/if}
									{if $data.chk_background == 'NA'}<span class="not_applicable">{$lang.profile.background_check.not_applicable}</span>{/if}
								</label>
							</td>
							<td width="127" align="center">
								<label title="{$lang.profile.chk_marital_status_info}"> {$lang.profile.chk_marital_status}:<br />
									{if $data.chk_marital_status == 'VR'}<span class="verified">{$lang.profile.background_check.verified}</span>{/if}
									{if $data.chk_marital_status == 'PE'}<span class="pending">{$lang.profile.background_check.pending}</span>{/if}
									{if $data.chk_marital_status == 'NR'}<span class="not_requested">{$lang.profile.background_check.not_yet_requested}</span>{/if}
									{if $data.chk_marital_status == 'NA'}<span class="not_applicable">{$lang.profile.background_check.not_applicable}</span>{/if}
								</label>
							</td>
							<td width="122" align="center">
								<label title="{$lang.profile.chk_work_history_info}"> {$lang.profile.chk_work_history}:<br />
									{if $data.chk_work_history == 'VR'}<span class="verified">{$lang.profile.background_check.verified}</span>{/if}
									{if $data.chk_work_history == 'PE'}<span class="pending">{$lang.profile.background_check.pending}</span>{/if}
									{if $data.chk_work_history == 'NR'}<span class="not_requested">{$lang.profile.background_check.not_yet_requested}</span>{/if}
									{if $data.chk_work_history == 'NA'}<span class="not_applicable">{$lang.profile.background_check.not_applicable}</span>{/if}
								</label>
							</td>
							<td width="135" align="center">
								<label title="{$lang.profile.chk_interview_photo_info}"> {$lang.profile.chk_interview_photo}:<br />
									{if $data.chk_interview_photo == 'VR'}<span class="verified">{$lang.profile.background_check.verified}</span>{/if}
									{if $data.chk_interview_photo == 'PE'}<span class="pending">{$lang.profile.background_check.pending}</span>{/if}
									{if $data.chk_interview_photo == 'NR'}<span class="not_requested">{$lang.profile.background_check.not_yet_requested}</span>{/if}
									{if $data.chk_interview_photo == 'NA'}<span class="not_applicable">{$lang.profile.background_check.not_applicable}</span>{/if}
								</label>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div>
			<div class="box-frame" style="min-height:363px;">
				<div class="hdr2">{$lang.section.mix_and_mingle}</div>
				<br>
				{if $link_item.connected_status == CS_CONNECTED}
					<p style="margin:0px; padding:0px;">
						<img src="{$site_root}{$template_root}/images/connections_icon.png" alt="{$lang.search.added_to_hotlist}">
					</p>
					{if $link_item.del_connection_link}
						<p style="margin:0 0 15px; padding:0px;">
							<b><a href="javascript:void(0);" onclick="DeleteConnection('{$link_item.del_connection_link}');">{$lang.user_link.remove}</a></b>
						</p>
					{else}
						<br>
					{/if}
				{/if}
				{if $link_item.hotlisted}
					<p style="margin:0 0 10px; padding:0px;">
						<img src="{$site_root}{$template_root}/images/hotlist_icon.png" alt="{$lang.search.added_to_hotlist}">
					</p>
				{/if}
				<div id="profile-action">
					{include file="$gentemplates/user_links.tpl"}
				</div>
			</div>
		</div>
	</div>
</div>
<div class="bottom-section">
	<div class="toc">
		<div class="main-fixed-2 tcxf-ch-la">
			<div>
				<div class="tcxf-ch-la">
					<div class="box-frame" style="width:200px;">
						<div class="hdr2">{$lang.section.quick_stat}</div>
						<p class="text_head"><label title="{$lang.first_name_thai}">Name:</label> {$data_1.fname}</p>
						<p class="text_head"><label title="{$lang.age_thai}">Age:</label> {$data_1.age}</p>
						<p class="text_head"><label title="{$lang.marital_status_thai}">Status:</label> {if $data_1.couple}{$lang.users.couple}{else}{$lang.users.single}{/if}</p>
						{*Added BY Narendra*}
						<p class="text_head"><label title="{$lang.height}">Height:</label><br>{$data_2.height}</p>
						<p class="text_head"><label title="{$lang.weight}">Weight:</label><br>{$data_2.weight}</p>
						
						<p class="text_head"><label title="{$lang.nationality_thai}">Nationality:</label> {$data_1.nationality}</p>
						<p class="text_head"><label title="{$lang.country_thai}">Lives In:</label><br>{$data_1.lives_in}</p>
						<p class="text_head"><label title="{$lang.language_thai}">Speaks:</label><br>{$data_1.languages}</p>
						<p align="center" style="margin-top:80px; margin-bottom:0px;">
							<a class="inline_colorbox" href="#inline_facts">More Facts</a> &nbsp;|&nbsp; <a class="inline_colorbox" href="#inline_criteria">Partner Criteria</a>
						</p>
						<div style="display:none">
							<div id="inline_facts" class="inline_content">
								<div class="hdr2">{$lang.subsection.description}</div>
								<div class="sep"></div>
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td width="50%" style="padding-bottom: 5px;" class="txtblack">
											<font class="txtblue">{$lang.users.weight}:</font> {$data_2.weight}
										</td>
										<td width="50%" style="padding-bottom: 5px;" class="txtblack">
											<font class="txtblue">{$lang.users.height}:</font> {$data_2.height}
										</td>
									</tr>
									{section name=s loop=$data_2.info}
										{if $smarty.section.s.index is div by 2}
											<tr>
										{/if}
											<td width="50%" style="padding-bottom: 5px;" class="txtblack">
												<font class="txtblue">{$data_2.info[s].spr}:</font> {$data_2.info[s].value}
											</td>
										{if $smarty.section.s.index_next is div by 2 || $smarty.section.s.last}
											</tr>
										{/if}
									{/section}
								</table>
							</div>
						</div>
						<div style="display:none">
							<div id="inline_criteria" class="inline_content">
								<div class="hdr2">{$lang.subsection.criteria}</div>
								<div class="sep"></div>
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td valign="top" width="50%" style="padding-bottom: 5px;" class="txtblack">
											<font class="txtblue">{$lang.users.weight}:</font> {$data_7.weight}
										</td>
										<td valign="top" width="50%" style="padding-bottom: 5px;" class="txtblack">
											<font class="txtblue">{$lang.users.height}:</font> {$data_7.height}
										</td>
									</tr>
									<tr>
										<td width="50%" style="padding-bottom: 5px;" class="txtblack">
											<font class="txtblue">{$lang.users.country}:</font> {$data_7.country_match}
										</td>
										<td width="50%" style="padding-bottom: 5px;" class="txtblack">
											<font class="txtblue">{$lang.users.language}:</font> {$data_7.language}
										</td>
									</tr>
									<tr>
										<td width="50%" style="padding-bottom: 5px;" class="txtblack">
											<font class="txtblue">{$lang.users.nationality}:</font> {$data_7.nationality_match}
										</td>
									</tr>
									{section name=s loop=$data_7.info}
										{if $smarty.section.s.index_next is div by 2}
											<tr>
										{/if}
										<td width="50%" style="padding-bottom: 5px;" class="txtblack">
											<font class="txtblue">{$data_7.info[s].name}:</font> {$data_7.info[s].value}
										</td>
										{if $smarty.section.s.index is div by 2 || $smarty.section.s.last}
											</tr>
										{/if}
									{/section}
								</table>
							</div>
						</div>
					</div>
					<div class="profile-info-new">
						<div>
							<p class="title"><label title="{$lang.about_me_thai}">Some Things About Me:</label></p>
							<p>{if $data_1.about_me}{$data_1.about_me_short}...(<a class="inline_colorbox" href="#inline_profile-info">more</a>){/if}</p>
							<p class="title" style="padding-top:8px;"><label title="{$lang.what_i_do_thai}">What I do:</label></p>
							<p>{if $data_1.what_i_do}{$data_1.what_i_do_short}...(<a class="inline_colorbox" href="#inline_profile-info">more</a>){/if}</p>
							<p class="title" style="padding-top:8px;"><label title="{$lang.my_idea_thai}">My Idea of A Perfect Weekend:</label></p>
							<p>{if $data_1.my_idea}{$data_1.my_idea_short}...(<a class="inline_colorbox" href="#inline_profile-info">more</a>){/if}</p>
							<p class="title" style="padding-top:8px;"><label title="{$lang.hoping_to_find_thai}">What I'm Hoping To Find:</label></p>
							<p>{if $data_1.hoping_to_find}{$data_1.hoping_to_find_short}...(<a class="inline_colorbox" href="#inline_profile-info">more</a>){/if}</p>
						</div>
						<div style="display:none">
							<div id="inline_profile-info" class="inline_content">
								{* RS: jquery tooltips are not displayed in colorbox, even when we use onComplete event *}
								<p class="title" title="{$lang.about_me_thai}" style="cursor:help">Some Things About Me:</p>
								<p>{if $data_1.about_me}{$data_1.about_me}{/if}</p>
								<p class="title" style="padding-top:8px; cursor:help" title="{$lang.what_i_do_thai}">What I do:</p>
								<p>{if $data_1.what_i_do}{$data_1.what_i_do}{/if}</p>
								<p class="title" style="padding-top:8px; cursor:help" title="{$lang.my_idea_thai}">My Idea of A Perfect Weekend:</p>
								<p>{if $data_1.my_idea}{$data_1.my_idea}{/if}</p>
								<p class="title" style="padding-top:8px; cursor:help" title="{$lang.hoping_to_find_thai}">What I'm Hoping To Find:</p>
								<p>{if $data_1.hoping_to_find}{$data_1.hoping_to_find}{/if}</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div>
				<div class="box-frame" style="height:150px;">
					<div class="hdr2">{$lang.section.alerts} <span style="position:relative; top:-5px; font-size:22px">|</span> {$lang.section.privacy}</div>
					<p><input type="checkbox" name="" value="" /> <b>Alert Me When This Person<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Is Online</b></p>
					<p><input type="checkbox" name="" value="" /> <b>Always Invisible To This<br /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Person</b></p>
				</div>
				<div>&nbsp;</div>
				<div class="box-frame">
					<div class="hdr2">{$lang.users.gift_shop}</div>
					<p class="text_head">{$lang.users.gift_shop_slogan}</p>
					<p align="center" style="margin-bottom:0px;">
						<a href="giftshop.php?sel=users_add&id_user={$data_1.id}">{$lang.users.buy_gift}</a>
					</p>
				</div>
			</div>
		</div>
	</div>
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}