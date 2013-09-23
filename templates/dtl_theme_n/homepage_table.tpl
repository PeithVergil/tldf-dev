{include file="$gentemplates/index_top.tpl" show_news=1}
{strip}
{if ($org_home[3] == 'true' || $org_home[4] == 'true' || $org_home[5] == 'true' || $org_home[6] == 'true') || !($use_pilot_module_organizer eq 1 && $hide eq 1)}
	{if $org_home[3] == 'true' || !($use_pilot_module_organizer eq 1 && $hide eq 1) }
		{*
			<div class="text" style="padding-bottom: 12px;">
				<div align="justify">{$header.toptext}</div>
			</div>
		*}
		{if $form.err}
			<div class="main-box">
				<div class="error_msg"><p>{$form.err}</p></div>
			</div>
		{/if}
		<!-- main section -->
		<div class="toc page-simple-small">
			<div class="main-fixed-3 tcxf-ch-la">
				<div>
					<div><!-- user slide area -->&nbsp;</div>
				</div>
				<div>
					<div class="hdr2">{$lang.section.profile}</div>
					<div class="tcxf-ch-la">
						<div style="width:37%;">
							<div class="profile-photo" style="float:left;">
								{if $page.icon_path}<a href="myprofile.php"><img src="{$page.icon_path}" class="big_icon" alt=""></a>{/if}
								<p align="center" style="margin:7px 0px 2px 0px;"><b>{$page.complete}</b>% {$lang.users.complete}</p>
								<p align="center" style="margin:0px;"><a href="myprofile.php">{$button.add_more_info}</a></p>
							</div>
						</div>
						<div style="width:63%; overflow:hidden;">
							{*
								<ul class="none-bullet">
									<li>{$header.member_since}:&nbsp;&nbsp;<b>{$page.date_registration}</b></li>
									<li>{$header.last_connection}:&nbsp;&nbsp;<b>{$page.last_login}</b></li>
								</ul>
							*}
							<table class="home-links-tbl" cellpadding="0" cellspacing="0">
								<tr>
									<td><b>{$page.user_group}</b></td>
									<td>
										{if $auth.is_trial || $auth.is_regular}&nbsp; | &nbsp;{/if}
									</td>
									<td>
										{* next higher level only *}
										{if $auth.is_trial}
											<a href="payment.php?sel=buy_connection">{$lang.button.upgrade_membership}</a>
										{elseif $auth.is_regular}
											{if !$data.platinum_paid}
												<a href="platinum_match.php">{$lang.button.upgrade_membership}</a>
											{/if}
										{/if}
									</td>
								</tr>
								<tr><td colspan="3" style="height:5px;"></td></tr>
								<tr>
									<td><b>{$auth.login}</b></td>
									<td>&nbsp; | &nbsp;</td>
									<td><a href="myprofile.php">{$button.gr_profile}</a></td>
								</tr>
								<tr>
									<td colspan="3">
										{if $base_lang.city[$page.id_city]}
											{$base_lang.city[$page.id_city]}, {/if}
										{if $base_lang.region[$page.id_region]}
											{$base_lang.region[$page.id_region]}, {/if}
										{$base_lang.country[$page.id_country]}
									</td>
								</tr>
								<tr>
									<td colspan="3" class="text_head">
										{$page.age} {$lang.home_page.ans}
									</td>
								</tr>
								<tr><td colspan="3" style="height:5px;"></td></tr>
								<tr>
									<td><b>{$page.photo_count}</b> {$lang.users.upload_1}</td>
									<td>&nbsp; | &nbsp;</td>
									<td><a href="{$site_root}/myprofile.php?sel=4">{$button.add_more_photo}</a></td>
								</tr>
								<tr>
									<td><b>{$page.video_count}</b> {$lang.users.upload_3}</td>
									<td>&nbsp; | &nbsp;</td>
									<td><a href="{$site_root}/myprofile.php?sel=4&amp;sub=10">{$button.add_more_video}</a></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div>
					<div class="box-frame">
						<div class="hdr2">{$lang.top_menu.my_account}</div>
						<table class="home-links-tbl" cellpadding="0" cellspacing="0" style="padding-top:10px;">
							<tr>
								<td>{$lang.account.my_group}: <b>{$page.user_group_2}</b></td>
								<td>&nbsp; | &nbsp;</td>
								<td nowrap>
									{* KEEP IN SYNC WITH ACCOUNT_TABLE.TPL : START *}
									{if $auth.is_trial}
										{if ! $data.offline_paysystem_regular_upgrade_send}
											<a href="payment.php?sel=buy_connection">{$lang.button.get_more_connections}</a><br />
										{/if}
										{if !$data.platinum_paid}
											<a href="platinum_match.php">{$header.apply_for_platinum}</a>
										{/if}
									{elseif $auth.is_regular}
										{if !$data.platinum_paid}
											<a href="platinum_match.php">{$header.apply_for_platinum}</a>
										{/if}
									{/if}
									{* KEEP IN SYNC WITH ACCOUNT_TABLE.TPL : END *}
								</td>
							</tr>
							<tr>
								<td>
									{$lang.account.account_period_rest}:&nbsp;</span>
									{* KEEP IN SYNC WITH ACCOUNT_TABLE.TPL : START *}
									{if $active_user_info.unlimited}
										<b>{$lang.account.n_a}</b>
									{elseif $active_user_info.recurring}
										<b>{$lang.account.n_a}</b>
									{elseif $active_user_info.days_remain < 0}
										{*<!-- <b class="txtred">{$lang.account.expired}</b> -->*}
										<b class="txtred">0</b>
									{elseif $active_user_info.days_remain < 8}
										<b class="txtred">{$active_user_info.days_remain}</b>
									{else}
										<b>{$active_user_info.days_remain}</b>
									{/if}
									{* KEEP IN SYNC WITH ACCOUNT_TABLE.TPL : END *}
								</td>
								<td>&nbsp; | &nbsp;</td>
								<td>
									{* KEEP IN SYNC WITH ACCOUNT_TABLE.TPL : START *}
									{if ! $active_user_info.recurring}
										{if $auth.gender == GENDER_FEMALE}
											{if !in_array($auth.id_group, array(MM_PLATINUM_LADY_FIRST_INS_ID,MM_PLATINUM_LADY_SECOND_INS_ID,MM_PLATINUM_LADY_ID))}
												<a href="payment.php?sel=buy_connection">{$header.more_days}</a>
											{/if}
										{/if}
									{/if}
									{* KEEP IN SYNC WITH ACCOUNT_TABLE.TPL : END *}
								</td>
							</tr>
							<tr>
								<td>{$header.account_credit}:&nbsp;<b>{$account.account}</b>{*<!--&nbsp;{$account.units}-->*}</td>
								<td>&nbsp; | &nbsp;</td>
								<td>
									{* KEEP IN SYNC WITH ACCOUNT_TABLE.TPL : START *}
									{if $auth.gender == GENDER_MALE}
										<a href="payment.php?sel=buy_connection">{$header.account_buy}</a>
									{else}
										{if $auth.is_trial && ! $data.offline_paysystem_regular_upgrade_send}
											<a href="payment.php?sel=buy_connection">{$lang.button.get_more_connections}</a>
										{/if}
									{/if}
									{* KEEP IN SYNC WITH ACCOUNT_TABLE.TPL : END *}
								</td>
							</tr>
							<tr><td style="height:10px;"></td></tr>
							<tr>
								<td align="right"><a href="account.php">{$header.my_account}</a></td>
								<td>&nbsp; | &nbsp;</td>
								<td><a href="account.php?sel=passw">{$lang.account.change_password}</a></td>
							</tr>
						</table>
						{*
							<p><a href="payment.php">{$header.account_upgrade}</a></p>
							&nbsp;&nbsp;|&nbsp;&nbsp;
							<a href="account.php#alerts">{$header.account_alert}</a>
							&nbsp;&nbsp;|&nbsp;&nbsp;
							<a href="account.php#news">{$header.account_news}</a>
						*}
					</div>
				</div>
			</div>
		</div>
		<!-- /main section -->
	{/if}
	<div class="bottom-section">
		<div class="toc main-fixed-3 tcxf-ch-la">
			<div>
				<div>
					{if $org_home[7] == 'true' || !($use_pilot_module_organizer eq 1 && $hide eq 1)}
						<div class="box-frame">
							<table cellpadding="0" cellspacing="0" width="100%" border="0">
								<tr>
									<td valign="top">
										<div class="hdr2">{$lang.home_page.header_search}</div>
										<form action="quick_search.php" method="post" name="search_form" id="search_form" style="margin: 0px">
											<input type="hidden" name="sel" value="search" />
											<input type="hidden" name="flag_country" value="0" />
											<input type="hidden" name="search_type" id="search_type" {if $form.search_type == 1}value="1"{else}value="2"{/if} />
											<table cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td valign="top">
														<table cellpadding="0" cellspacing="0">
															<tr>
																<td style="padding:5px 0px 0px 0px;" class="text_head"> {$lang.home_page.im} : {if $data.gender_1 eq 1}{$gender[0].name}{elseif $data.gender_1 eq 2}{$gender[1].name}{/if}
																	<input type="hidden" name="gender_1" value="{$data.gender_1}" />
																</td>
															</tr>
															<tr>
																<td style="padding:10px 0px 3px 0px;" class="text_head">
																	{$lang.home_page.seeking_a} : &nbsp;
																	{if QUICK_SEARCH_GENDER}
																		<select name="gender_2" class="index_select" style="width:115px;">
																			{foreach item=item from=$gender}
																				<option value="{$item.id}" {if $item.sel_search}selected="selected"{/if}>{$item.name_search}</option>
																			{/foreach}
																		</select>
																	{else}
																		{if $data.gender_2 eq 1}{$gender[0].name}{elseif $data.gender_2 eq 2}{$gender[1].name}{/if}
																		<input type="hidden" name="gender_2" value="{$data.gender_2}" />
																	{/if}
																</td>
															</tr>
															{if QUICK_SEARCH_COUPLE}
																<tr>
																	<td>
																		<table cellpadding="0" cellspacing="0">
																			<tr>
																				<td>
																					<input type="radio" name="couple_2" value="0" {if !$data.couple_2}checked{/if}>
																				</td>
																				<td style="padding-right:15px;">&nbsp;{$lang.users.single}</td>
																				<td>
																					<input type="radio" name="couple_2" value="1" {if $data.couple_2}checked{/if}>
																				</td>
																				<td style="padding-right:15px;">&nbsp;{$lang.users.couple}</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															{/if}
															{if QUICK_SEARCH_RELATIONSHIP}
																<tr>
																	<td style="padding: 15px 0px 3px 0px;" class="text_head">{$lang.home_page.looking_for}</td>
																</tr>
																<tr>
																	<td>
																		<select name="relation[]" style="width: 150px;" multiple size="4" class="index_select">
																			<option value="" {if $data.arr_relationship eq '0' || !$data.arr_relationship}selected{/if}>{$lang.home_page.select_default}</option>
																			{section name=s loop=$relation}
																			<option value="{$relation[s].id}" {if $relation[s].sel eq 1}selected{/if}>{$relation[s].name}</option>
																			{/section}
																		</select>
																	</td>
																</tr>
															{else}
																<tr>
																	<td><input type="hidden" name="relation[]" value="2" /></td>
																</tr>
															{/if}
															<tr>
																<td style="padding:10px 0px 3px 0px;" class="text_head">{$lang.home_page.between_the_ages_of}</td>
															</tr>
															<tr>
																<td>
																	<table cellpadding="0" cellspacing="0">
																		<tr>
																			<td>
																				<select name="age_min" class="index_select">
																					{foreach item=item from=$age_min}
																						<option value="{$item}" {if $item == $form.age_min}selected="selected"{/if}>{$item}</option>
																					{/foreach}
																				</select>
																			</td>
																			<td style="padding:0px 5px" class="text_head">&nbsp;{$lang.home_page.and}&nbsp;</td>
																			<td>
																				<select name="age_max" class="index_select">
																					{foreach item=item from=$age_max}
																						<option value="{$item}" {if $item == $form.age_max}selected="selected"{/if}>{$item}</option>
																					{/foreach}
																				</select>
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>
															{if QUICK_SEARCH_COUNTRY}
																<tr>
																	<td style="padding: 15px 0px 3px 0px;" class="text_head">{$lang.home_page.country}</td>
																</tr>
																<tr>
																	<td>
																		<div id="country_div" style="margin-top:0;">
																			<select name="country" style="width:150px;" onchange="SelectRegion('hp', this.value, document.getElementById('region_div'), document.getElementById('city_div'));" class="index_select">
																				<option value="0">{$lang.home_page.select_default}</option>
																				{section name=s loop=$countries}
																				<option value="{$countries[s].id}" {if $countries[s].sel}selected{/if}>{$countries[s].name}</option>
																				{/section}
																			</select>
																		</div>
																	</td>
																</tr>
															{/if}
															{if QUICK_SEARCH_REGION}
																<tr>
																	<td style="padding: 5px 0px 3px 0px;" class="text_head">{$lang.home_page.region}</td>
																</tr>
																<tr>
																	<td>
																		<div id="region_div" style="margin-top:0;">
																			<select name="region" style="width:150px;" class="index_select" onchange="javascript: SelectCity('hp', this.value, document.getElementById('city_div'));">
																				<option value="0">{$lang.home_page.select_default}</option>
																				{foreach item=item from=$regions}
																				<option value="{$item.id}" {if $item.sel eq 1}selected{/if}>{$item.name}
																				{/foreach}
																			</select>
																		</div>
																	</td>
																</tr>
															{/if}
															{if QUICK_SEARCH_CITY}
																<tr>
																	<td style="padding: 5px 0px 3px 0px;" class="text_head">{$lang.home_page.city}</td>
																</tr>
																<tr>
																	<td>
																		<div id="city_div" style="margin-top:0;">
																			<select name="city" style="width:150px;" class="index_select">
																				<option value="0">{$lang.home_page.select_default}</option>
																				{foreach item=item from=$cities}
																				<option value="{$item.id}" {if $item.sel eq 1}selected{/if}>{$item.name}
																				{/foreach}
																			</select>
																		</div>
																	</td>
																</tr>
															{/if}
															{if QUICK_SEARCH_DISTANCE}
																<tr>
																	<td style="padding: 15px 0px 0px 0px;">
																		<table cellpadding="0" cellspacing="0">
																			<tr>
																				<td class="text_head">{$lang.home_page.zipcode}</td>
																				<td style="padding-left: 3px;">
																					<input type="text" name="zipcode" id="zipcode" maxlength="{$form.zip_count}" style="width:60px;" onblur="ZipCodeCheck(this.value);">
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
																<tr>
																	<td style="padding: 15px 0px 3px 0px;">
																		<table cellpadding="0" cellspacing="0">
																			<tr>
																				<td class="text_head">{$lang.home_page.within}</td>
																				<td style="padding-left: 5px;">
																					<input type="checkbox" id="within" name="within" value="1" {if $form.search_type eq 2}disabled{/if} onclick="javascript: if (document.getElementById('distance').disabled) document.getElementById('distance').disabled = false; else document.getElementById('distance').disabled = true;">
																				</td>
																				<td style="padding-left: 5px;">
																					<select id="distance" name="distance" disabled class="index_select">
																						{section name=d loop=$distances}
																						<option value="{$distances[d].id}">{$distances[d].name} {$distances[d].type}</option>
																						{/section}
																					</select>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															{/if}
															<tr>
																<td style="padding:8px 0px 0px 0px;">
																	<table cellpadding="0" cellspacing="0">
																		{if QUICK_SEARCH_WITH_PHOTO_SEARCH}
																			<tr>
																				<td><input type="checkbox" name="foto_only" value="1" checked="checked" /></td>
																				<td class="text_head">&nbsp;{$lang.home_page.foto}</td>
																			</tr>
																		{/if}
																		<tr>
																			<td><input type="checkbox" name="online_only" value="1" /></td>
																			<td class="text_head">&nbsp;{$lang.home_page.online_now}</td>
																		</tr>
																	</table>
																</td>
															</tr>
														</table>
													</td>
													<td valign="bottom" align="right">
														<p class="basic-btn_next" style="margin:0px;">
															<span>
															<input type="button" value="{$lang.button.search}" onclick="document.search_form.submit();">
															</span><b>&nbsp;</b>
														</p>
													</td>
												</tr>
											</table>
										</form>
									</td>
								</tr>
							</table>
							<div class="h-line"></div>
							<div>
								<form action="quick_search.php" method="get" name="search_form_6">
									<input type="hidden" name="sel" value="search_fname">
									<p style="margin-bottom:5px;" class="text_head">{$lang.search.search_6_name}</p>
									<div class="tcxf-ch-la">
										<input type="text" name="fname" style="width:128px;" maxlength="50">
										<span class="basic-btn_next"><span><input type="button" onclick="document.search_form_6.submit();" value="{$button.search}"></span><b>&nbsp;</b></span>
									</div>
								</form>
							</div>
							<div class="h-line"></div>
							<div>
								<form action="quick_search.php" method="get" id="search_form_4" name="search_form_4">
									<input type="hidden" name="sel" value="search_keyword">
									<p style="margin-bottom:5px;" class="text_head">{$lang.search.search_4_name}</p>
									<div class="tcxf-ch-la">
										<input type="text" name="word" style="width:128px;" maxlength="50" />
										<span class="basic-btn_next"><span><input type="button" onclick="document.search_form_4.submit();" value="{$button.search}"></span><b>&nbsp;</b></span>
									</div>
								</form>
							</div>
							<div class="h-line"></div>
							<p align="center" style="margin-bottom:0px;">
								{if $smarty.session.permissions.advanced_search}
									<a class="menu_block_3_link" href="{$site_root}/advanced_search.php">{$lang.section.a_search}</a>
								{/if}
							</p>
						</div>
					{/if}
				</div>
			</div>
			<div>
				{if $org_home[4] == 'true' || !($use_pilot_module_organizer eq 1 && $hide eq 1)}
					<!-- mix and mingle section -->
					<div class="mix-and-mingle">
						<div class="hdr2">{$lang.top_menu.mix_and_mingle}</div>
						<div class="tcxf-ch-la chw-50-50">
							<div>
								<p class="text_head">{$header.mix_mingle_header_4}:</p>
								<p style="white-space:nowrap;"><a href="{$hotlist.my_connections_link}">{$header.mix_mingle_text_4_1}:&nbsp;<span>{$hotlist.my_connections_count}</span></a></p>
								<p style="white-space:nowrap;"><a href="{$hotlist.my_hotlist_link}">{$header.mix_mingle_text_4_2}:&nbsp;<span>{$hotlist.my_hotlist_count}</span></a></p>
							</div>
							<div>
								<div style="padding-left:12px;">
									<p class="text_head">{$header.mix_mingle_header_5}:</p>
									<p style="white-space:nowrap;"><a href="{$site_root}/mailbox.php">{$header.mix_mingle_text_5_1}:&nbsp;<span>{$active_user_info.emailed_me_new_count}</span></a></p>
									<p style="white-space:nowrap;"><a href="ecards.php">{$header.mix_mingle_text_5_2}</a></p>
								</div>
							</div>
						</div>
						<div class="h-line"></div>
						<div class="tcxf-ch-la chw-50-50">
							<div>
								<p class="text_head">{$header.mix_mingle_header_1}:</p>
								<p style="white-space:nowrap;"><a href="{$hotlist.visit_me_link}">{$header.mix_mingle_text_1_1}:&nbsp;<span>{$hotlist.visit_me_count}</span></a></p>
								<p style="white-space:nowrap;"><a href="{$hotlist.kiss_me_link}">{$header.mix_mingle_text_1_2}:&nbsp;<span>{$hotlist.kiss_me_count}</span></a></p>
								<p style="white-space:nowrap;"><a href="{$hotlist.ecard_me_link}">{$header.mix_mingle_text_1_3}:&nbsp;<span>{$hotlist.ecard_me_count}</span></a></p>
								<p style="white-space:nowrap;"><a href="{$hotlist.emailed_me_link}">{$header.mix_mingle_text_1_4}:&nbsp;<span>{$hotlist.emailed_me_count}</span></a></p>
								<p style="white-space:nowrap;"><a href="{$hotlist.connect_me_link}">{$header.mix_mingle_text_1_5}:&nbsp;<span>{$hotlist.connect_me_count}</span></a></p>
								<p style="white-space:nowrap;"><a href="{$hotlist.theirhotlist_link}">{$header.mix_mingle_text_1_6}:&nbsp;<span>{$hotlist.their_hotlist_count}</span></a></p>
							</div>
							<div>
								<div style="padding-left:12px;">
									<p class="text_head">{$header.mix_mingle_header_2}:</p>
									<p style="white-space:nowrap;"><a href="{$hotlist.visit_them_link}">{$header.mix_mingle_text_2_1}:&nbsp;<span>{$hotlist.visit_them_count}</span></a></p>
									<p style="white-space:nowrap;"><a href="{$hotlist.kiss_them_link}">{$header.mix_mingle_text_2_2}:&nbsp;<span>{$hotlist.kiss_them_count}</span></a></p>
									<p style="white-space:nowrap;"><a href="{$hotlist.ecard_them_link}">{$header.mix_mingle_text_2_3}:&nbsp;<span>{$hotlist.ecard_them_count}</span></a></p>
									<p style="white-space:nowrap;"><a href="{$hotlist.emailed_them_link}">{$header.mix_mingle_text_2_4}:&nbsp;<span>{$hotlist.emailed_them_count}</span></a></p>
									<p style="white-space:nowrap;"><a href="{$hotlist.connect_them_link}">{$header.mix_mingle_text_2_5}:&nbsp;<span>{$hotlist.connect_them_count}</span></a></p>
									{*
										{if $user_refer_frends}
											<p style="white-space:nowrap;"><a href="{$hotlist.referred_link}">{$header.mix_mingle_text_2_6}:&nbsp;<span>{$hotlist.referred_count}</span></a></p>
										{/if}
									*}
								</div>
							</div>
						</div>
						<div class="h-line"></div>
						<div class="tcxf-ch-la chw-50-50">
							{*
								<div>
									<p class="text_head">{$header.mix_mingle_header_3}:</p>
									<p style="white-space:nowrap;"><a href="{$hotlist.meetthem_link}">{$header.mix_mingle_text_3_1}:&nbsp;<span>{$hotlist.meet_them_count}</span></a></p>
									<p style="white-space:nowrap;"><a href="{$hotlist.meetme_link}">{$header.mix_mingle_text_3_2}:&nbsp;<span>{$hotlist.meet_me_count}</span></a></p>
									<p style="white-space:nowrap;"><a href="{$hotlist.perfect_link}">{$header.mix_mingle_text_3_3}:&nbsp;{$hotlist.match_count}</a></p>
								</div>
							*}
							<div>
								<br />
								<p style="white-space:nowrap;"><a href="{$site_root}/blacklist.php">{$lang.top_menu.my_blacklist}</a></p>
								<p style="white-space:nowrap;"><a href="{$site_root}/report_a_violation.php">{$lang.top_menu.report_a_violation}</a></p>
							</div>
						</div>
					</div>
					<!-- /mix and mingle section -->
				{/if}
			</div>
			<div>
				{if $org_home[7] == 'true' || !($use_pilot_module_organizer eq 1 && $hide eq 1)}
					<div class="box-frame">
						<div class="hdr2">{$lang.users.privacy_settings}</div>
						{* online privacy *}
						<p>
							<span class="text_head">{$lang.users.privacy_online_title}:</span><br />
							<input type="checkbox" {if $page.hide_online==1}checked="checked"{/if} disabled="disabled" style="position:relative; top:2px;" />
							{$lang.users.privacy_online_hide}
						</p>
						{* privacy female *}
						<p>
							<span class="text_head">{$lang.users.pri_female}: </span><br />
							{if $page.visible_lady==0}
								<input type="checkbox" checked="checked" disabled="disabled" style="position:relative; top:2px;" />
								{$lang.users.pri_lady_none}
							{/if}
							{if $page.visible_lady==1}
								<input type="checkbox" checked="checked" disabled="disabled" style="position:relative; top:2px;" />
								{$lang.users.pri_lady_all}
							{/if}
							{if $page.visible_lady==2}
								{if $page.vis_lady_1==1}
									<input type="checkbox" checked="checked" disabled="disabled" style="position:relative; top:2px;" />
									{$lang.users.pri_lady_trial}<br>
								{/if}
								{if $page.vis_lady_2==1}
									<input type="checkbox" checked="checked" disabled="disabled" style="position:relative; top:2px;" />
									{$lang.users.pri_lady_regular}<br>
								{/if}
								{if $page.vis_lady_3==1}
									<input type="checkbox" checked="checked" disabled="disabled" style="position:relative; top:2px;" />
									{$lang.users.pri_lady_platinum}
								{/if}
							{/if}
						</p>
						{* privacy male *}
						<p>
							<span class="text_head">{$lang.users.pri_male}:</span><br />
							{if $page.visible_guy==0}
								<input type="checkbox" checked="checked" disabled="disabled" style="position:relative; top:2px;" />
								{$lang.users.pri_guy_none}
							{/if}
							{if $page.visible_guy==1}
								<input type="checkbox" checked="checked" disabled="disabled" style="position:relative; top:2px;" />
								{$lang.users.pri_guy_all}
							{/if}
							{if $page.visible_guy==2}
								{if $page.vis_guy_1==1}
									<input type="checkbox" checked="checked" disabled="disabled" style="position:relative; top:2px;" />
									{$lang.users.pri_guy_trial}<br>
								{/if}
								{if $page.vis_guy_2==1}
									<input type="checkbox" checked="checked" disabled="disabled" style="position:relative; top:2px;" />
									{$lang.users.pri_guy_regular}<br>
								{/if}
								{if $page.vis_guy_3==1}
									<input type="checkbox" checked="checked" disabled="disabled" style="position:relative; top:2px;" />
									{$lang.users.pri_guy_platinum}<br>
								{/if}
								{if $page.vis_guy_4==1}
									<input type="checkbox" checked="checked" disabled="disabled" style="position:relative; top:2px;" />
									{$lang.users.pri_guy_elite}
								{/if}
							{/if}
						</p>
						{* promotion *}
						<p>
							<span class="text_head">{$lang.users.pri_promo}:</span><br />
							{if $page.promotion_1==1}
								<input type="checkbox" checked="checked" disabled="disabled" style="position:relative; top:2px;" />
								{$lang.users.prom_no}<br>
							{/if}
							{if $page.promotion_2==1}
								<input type="checkbox" checked="checked" disabled="disabled" style="position:relative; top:2px;" />
								{$lang.users.prom_within}<br>
							{/if}
							{if $page.promotion_3==1}
								<input type="checkbox" checked="checked" disabled="disabled" style="position:relative; top:2px;" />
								{$lang.users.prom_pros}
							{/if}
						</p>
						<p align="center" style="margin-bottom:0px;">
							<a href="myprofile.php?sel=edit_1&amp;reqfrom=homepage#div_PrivacySettings">{$lang.settings.change_privacy}</a>
						</p>
					</div>
				{/if}
				<div>&nbsp;</div>
				<div class="box-frame">
					<div class="hdr2">{$lang.users.gift_shop}</div>
					<p class="text_head">{$lang.users.gift_shop_slogan}</p>
					<p align="center" style="margin-bottom:0px;">
						<a href="giftshop.php">{$lang.users.buy_gift}</a>
					</p>
				</div>
			</div>
		</div>
	</div>
	{*
		<!-- clubs section and perfect match section -->
		{if $org_home[6] == 'true' || !($use_pilot_module_organizer == 1 && $hide == 1)}
			{if $clubs != 'empty'}
				<tr>
					<td><div class="hline_grey" style="margin:15px 0px 15px 0px;"></div></td>
				</tr>
				<tr>
					<td valign="top" class="gradient_top">
						<div class="content">
							<div class="header">{$lang.club.club_menu_1}</div>
							<div style="padding-top:10px;">
								<table cellpadding="0" cellspacing="0" width="100%">
									<tr>
										{foreach key=key item=item from=$clubs name=visited}
											<td valign="top">
												<div style="margin-top:0px"><a href="{$item.link}"><img src="{$item.icon_path}" class="icon" alt=""></a></div>
												<div style="margin-top:7px"><a href="{$item.link}" style="white-space:nowrap;"><b>{$item.club_name}</b></a></div>
												<div style="margin-top:3px"><span class="text_head">{$item.category}</span></div>
											</td>
											<td width="23">&nbsp;</td>
										{/foreach}
									</tr>
								</table>
							</div>
							<div style="padding: 15px 10px 15px 10px"> <a href="club.php">{$lang.home_page.show_more_clubs}</a> </div>
						</div>
					</td>
				</tr>
			{/if}
			<tr>
				<td><div class="hline_grey" style="margin:15px 0px 15px 0px;"></div></td>
			</tr>
			<tr>
				<td valign="top" class="gradient_top">
					<div class="header">{$lang.subsection.perfect_match}</div>
					<div style="padding-top:10px;">
						<table cellpadding="0" cellspacing="0" border="0">
							<tr>
								{foreach key=key item=item from=$visited name=visited}
									<td valign="top">
										<div style="margin-top:0px"><a href="{$item.link}"><img src="{$item.icon_path}" class="icon" alt=""></a></div>
										<div style="margin-top:7px"><a href="{$item.link}" style="white-space:nowrap;"><b>{$item.name}</b></a></div>
										<div style="margin-top:2px; white-space:nowrap;" class="text">
											{if $base_lang.city[$item.id_city]}
												{$base_lang.city[$item.id_city]},&nbsp;
											{/if}
											{if $base_lang.region[$item.id_region]}
												{$base_lang.region[$item.id_region]},&nbsp;
											{/if}
											{$base_lang.country[$item.id_country]}
										</div>
										<div style="margin-top:2px; white-space:nowrap;" class="text_head">{$item.age} {$lang.home_page.ans}</div>
										<div style="margin-top:2px; white-space:nowrap;" class="text_hidden">{$item.photo_count} {$lang.users.upload_1}</div>
									</td>
									<td width="23">&nbsp;</td>
								{/foreach}
							</tr>
						</table>
					</div>
					<div align="right" style="padding:10px 10px 15px 10px"><a href="{$hotlist.perfect_link}">{$header.view_matches}</a></div>
				</td>
			</tr>
		{/if}
	*}
	<!-- Clubs and perfect match section -->
{/if}
<div class="toc">
	{if $org_home[8] == 'true' || !($use_pilot_module_organizer == 1 && $hide == 1)}
		{*
			<!-- HOROSCOPE DISABLED -->
			{if $form.use_horoscope}
				<div style="padding-top: 12px;" class="home_horoscope">
					<div class="content">
						<div class="header">{$lang.home_page.view_horoscope}</div>
						<div style="padding: 0px 10px 10px 15px">
							<table cellpadding="0" cellspacing="0" border="0" width=100%>
								{section name=s loop=$horoscope}
									{if $smarty.section.s.index is div by 2}
										<tr>
									{/if}
									<td class="text" width=50% style="padding-top: 5px;">
										<a href="{$horoscope[s].sign_link}">{$horoscope[s].sign_name}</a> {if $horoscope[s].my_sign}<sup>{$lang.horoscope.your}</sup>{/if}
									</td>
									{if $smarty.section.s.index_next is div by 2 || $smarty.section.s.last}
										</tr>
									{/if}
								{/section}
							</table>
						</div>
					</div>
				</div>
			{/if}
			<!-- NEWS DISABLED -->
			{if $news ne 'empty'}
				<div style="margin-top: 12px;" class="content">
					<div class="header">{$lang.section.news}</div>
					<div style="padding-bottom: 15px;">
						{foreach item=item name=s from=$news}
							<div style="padding: 10px 12px 0px 12px;">
								<a href="{$item.link_read}">{$item.text}</a>
							</div>
						{/foreach}
					</div>
					<div align="right" style="padding: 12px 12px;">
						<a href="news.php">{$lang.home_page.view_more}</a>
					</div>
				</div>
			{/if}
		*}
	{/if}
	{if ($org_home[0] == 'false' && $org_home[1] == 'false' && $org_home[2] == 'false') || ($org_home[3] == 'false' && $org_home[4] == 'false' && $org_home[5] == 'false' && $org_home[6] == 'false') || !($use_pilot_module_organizer eq 1 && $hide eq 1)}
		<div>&nbsp;</div>
	{/if}
</div>
{/strip}
<!-- end main cell -->
<script type="text/javascript">
{literal}
function ZipCodeCheck(zip_value)
{
	if (zip_value == '') {
		document.getElementById('within').disabled = false;
		document.getElementById('search_type').value = 1;
	} else {
		document.getElementById('search_type').value = 2;
		document.getElementById('within').disabled = true;
	}
	return;
}
{/literal}
</script>
{include file="$gentemplates/index_bottom.tpl"}