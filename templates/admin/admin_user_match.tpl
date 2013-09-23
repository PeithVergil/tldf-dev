{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font>
<!--
<font class=red_sub_header>&nbsp;|&nbsp;{$header.user_matches}</font>
-->
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.user_matches}</div>
<form action="{$form.action}" method="get" name="search_form">
	{$form.hiddens}
	<table border='0' class='table_main' cellspacing='1' cellpadding='5' width='100%' >
		<!-- search form -->
		<tr>
			{strip}
			{if $form}
				<td>
					<!--{*
					{if $id_err == 1}
						<div class="error_msg">{$lang.err.no_searchname}</div>
					{/if}
					{if $id_err == 2}
						<div class="error_msg">{$lang.err.exist_searchname}</div>
					 {/if}
					 *}-->
					{if $err}
						<div class="error_msg">{$err}</div>
					{/if}
					<div class="quick_search">
						<table cellspacing="0" cellpadding="0" border="0" width="100%">
							<tr>
								<td colspan="4" style="padding-top:5px;">{$lang.advanced_search.help_tip.main_text}</td>
							</tr>
							<tr>
								<td colspan="2" class="text_head" style="padding:10px 0px;">
									{if $active_user.icon_path}
										<img src="../uploades/icons/{$active_user.icon_path}" alt="{$active_user.fname}" align="left" style="padding-right:10px;" >
									{/if}
									{$active_user.fname} {$active_user.sname}<br />
									{$active_user.age} {$lang.home_page.ans}
								</td>
							</tr>
							<tr>
								<td class="text_head" style="padding:5px 0px;">
									{$lang.home_page.im} : {if $active_user.gender eq 1}{$gender[0].name_search}{elseif $active_user.gender eq 2}{$gender[1].name_search}{/if}
									<input type="hidden" name="gender_1" value="{$data.gender_1}" />
								</td>
							</tr>
							<tr>
								<td width="25%" valign="top">
									<table cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td  width="25%" class="text_head">{$lang.home_page.seeking_a}</td>
										</tr>
										<tr>
											<td style="padding-top:3px;">
												{*<!--
												{if $active_user.gender eq 1}
													{$gender[1].name_search}
												{else}
													{$gender[0].name_search}
												{/if}
												-->*}
												<select class="index_select" style="width:130px;" name="gender_2">
													{section name=s loop=$gender}
													<option value="{$gender[s].id}" {if $gender[s].id neq $active_user.gender}selected{/if}>{$gender[s].name_search}</option>
													{/section}
												</select>
											</td>
										</tr>
										<tr>
											<td><input type="hidden" name="relation[]" value="2" /></td>
										</tr>
									</table>
								</td>
								<td width="25%" valign="top">
									<table cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td class="text_head">{$lang.home_page.between_the_ages_of}</td>
										</tr>
										<tr>
											<td style="padding-top:3px;">
												<table cellpadding="0" cellspacing="0">
													<tr>
														<td>
															<select class="index_select" style="width: 50px" name="age_min">
																{section name=a loop=$age_min}
																<option value="{$age_min[a]}"  {if $data.age_min eq $age_min[a]}selected{/if}>{$age_min[a]}</option>
																{/section}
															</select>
														</td>
														<td class="text"> &nbsp;&nbsp;{$lang.home_page.and}&nbsp;&nbsp; </td>
														<td>
															<select class="index_select" style="width: 50px" name="age_max">
																{section name=a loop=$age_max}
																<option value="{$age_max[a]}" {if $data.age_max eq $age_max[a]}selected{/if}>{$age_max[a]}</option>
																{/section}
															</select>
														</td>
												</table>
											</td>
										</tr>
									</table>
								</td>
								<td width="25%" valign="top" style="padding-top:10px;">
									<table cellpadding="0" cellspacing="0">
										<tr>
											<td>
												<table cellpadding="0" cellspacing="3">
													<tr>
														<td>
															<input type="checkbox" name="foto_only" value="1" {if $data.foto_only eq 1}checked{/if}>
														</td>
														<td class="text_head">{$header_s.foto}</td>
													</tr>
												</table>
											</td>
											<td>&nbsp;</td>
											<td>
												<table cellpadding="0" cellspacing="3">
													<tr>
														<td>
															<input type="checkbox" name="online_only" value="1" {if $data.online_only eq 1}checked{/if}>
														</td>
														<td class="text_head">{$header_s.online}</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
								<td width="25%" valign="top">&nbsp;</td>
							</tr>
						</table>
						<table cellpadding="0" cellspacing="0" width="100%" style="margin: 12px 0px;">
							<tr>
								<td valign="top" height="1" bgcolor="#{$css_color.home_search}"></td>
							</tr>
						</table>
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<td width="50%" valign="top" style="padding-top: 5px;">
									<table cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td class="text_head">{$header_perfect.weight}</td>
										</tr>
										<tr>
											<td style="padding-top: 2px;">
												<select name="id_weight"  style="width: 200px" class="index_select">
													<option value="0">{$lang.home_page.any_weight}</option>
													{section name=s loop=$weight}
													<option value="{$weight[s].id}" {if $weight[s].sel}selected{/if}>{$weight[s].value}</option>
													{/section}
												</select>
											</td>
										</tr>
									</table>
								</td>
								<td width="50%" valign="top" style="padding-top: 5px;">
									<table cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td class="text_head">{$header_perfect.height}</td>
										</tr>
										<tr>
											<td style="padding-top: 2px;">
												<select name="id_height"  style="width: 200px" class="index_select">
													<option value="0">{$lang.home_page.any_height}</option>
													{section name=s loop=$height}
													<option value="{$height[s].id}" {if $height[s].sel}selected{/if}>{$height[s].value}</option>
													{/section}
												</select>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td width="50%" valign="top" style="padding-top: 10px;">
									<table cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td class="text_head">{$header_perfect.nationality}</td>
										</tr>
										<tr>
											<td style="padding-top: 2px;">
												<select class="index_select" name="id_nation[]" multiple style="width: 200px;" size="5">
												<option value="0" {if $default.id_nation}selected{/if}>{$button.all}</option>
												{section name=s loop=$nation_match}
												<option value="{$nation_match[s].id}" {if $nation_match[s].sel}selected{/if}>{$nation_match[s].value}</option>
												{/section}
												</select>
											</td>
										</tr>
									</table>
								</td>
								<td width="50%" style="padding-top: 10px;">
									<table cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td class="text_head">{$header_perfect.language}</td>
										</tr>
										<tr>
											<td style="padding-top: 2px;">
												<select class="index_select" name="id_lang[]" multiple style="width:200px;" size="5">
												<option value="0" {if $default.id_lang}selected{/if}>{$button.all}</option>
												{section name=s loop=$lang_sel_match}
												<option value="{$lang_sel_match[s].id}"{if $lang_sel_match[s].sel} selected{/if}>{$lang_sel_match[s].value}</option>
												{/section}
												</select>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<!--\\\height\weight\nationality\lang-->
						<table cellpadding="0" cellspacing="0" width="100%" style="margin: 12px 0px 5px 0px;">
							<tr>
								<td valign="top" height="1" bgcolor="#{$css_color.home_search}"></td>
							</tr>
						</table>
						<!-- references -->
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							{section name=f loop=$info}
							<tr>
								<td width="25%" valign="top" style="padding-top: 10px;">
									<table cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td class="text_head">{$info[f].name_1}
												<input type=hidden name="spr[{$info[f].num_1}]" value="{$info[f].id_1}">
											</td>
										</tr>
										<tr>
											<td style="padding-top: 2px;">
												<select class="index_select" id="info{$info[f].num_1}" name="info[{$info[f].num_1}][]" multiple style="width:167px;" size="5">
												<option value="0" {if $info[f].sel_all_1}selected{/if}>{$button.all}</option>
												{html_options values=$info[f].opt_value_1 selected=$info[f].opt_sel_1 output=$info[f].opt_name_1}
												</select>
											</td>
										</tr>
									</table>
								</td>
								<td width="25%" valign="top" style="padding-top: 10px;">
									<table cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td class="text_head">
												{if $info[f].name_2}{$info[f].name_2}
												<input type=hidden name="spr[{$info[f].num_2}]" value="{$info[f].id_2}">
												{/if}
											</td>
										</tr>
										<tr>
											<td style="padding-top: 2px;">
												{if $info[f].name_2}
												<select class="index_select" id="info{$info[f].num_2}" name="info[{$info[f].num_2}][]" multiple  style="width:167px;" size="5">
												<option value="0" {if $info[f].sel_all_2}selected{/if}>{$button.all}</option>
												{html_options values=$info[f].opt_value_2 selected=$info[f].opt_sel_2 output=$info[f].opt_name_2}
												</select>
												{/if}
											</td>
										</tr>
									</table>
								</td>
								<td width="25%" valign="top" style="padding-top: 10px;">
									<table cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td class="text_head">
												{if $info[f].name_3}{$info[f].name_3}
												<input type=hidden name="spr[{$info[f].num_3}]" value="{$info[f].id_3}">
												{/if}
											</td>
										</tr>
										<tr>
											<td style="padding-top: 2px;">
												{if $info[f].name_3}
												<select class="index_select" id="info{$info[f].num_3}" name="info[{$info[f].num_3}][]" multiple  style="width:167px;" size="5">
												<option value="0" {if $info[f].sel_all_3}selected{/if}>{$button.all}</option>
												{html_options values=$info[f].opt_value_3 selected=$info[f].opt_sel_3 output=$info[f].opt_name_3}
												</select>
												{/if}
											</td>
										</tr>
									</table>
								</td>
								<td width="25%" valign="top" style="padding-top: 10px;">
									<table cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td class="text_head">
												{if $info[f].name_4}{$info[f].name_4}
												<input type=hidden name="spr[{$info[f].num_4}]" value="{$info[f].id_4}">
												{/if}
											</td>
										</tr>
										<tr>
											<td style="padding-top: 2px;">
												{if $info[f].name_4}
												<select class="index_select" id="info{$info[f].num_4}" name="info[{$info[f].num_4}][]" multiple  style="width:167px;" size="5">
												<option value="0" {if $info[f].sel_all_4}selected{/if}>{$button.all}</option>
												{html_options values=$info[f].opt_value_4 selected=$info[f].opt_sel_4 output=$info[f].opt_name_4}
												</select>
												{/if}
											</td>
										</tr>
									</table>
								</td>
							</tr>
							{/section}
							<!--//refeneces-->
							<!-- bottom -->
							<tr>
								<td align="center" colspan="4" style="padding-top:15px;">
									<input type="button" class="button" onclick="javascript: document.search_form.submit();" value="{$button.search}">
								</td>
							</tr>
							<!--// bottom -->
						</table>
					</div>
				</td>
			{else}
				<td align="center" style="padding-top:15px;">
					<div class="error_msg">{$lang.err.select_user}</div>
					<br /><br />
					<input type="button" class="button" onclick="javascript: document.location.href='{$back_link}';" value="{$button.back}">
				</td>
			{/if}
			{/strip}
		</tr>
		<!-- /search form -->
	</table>
</form>
{include file="$admingentemplates/admin_bottom.tpl"}