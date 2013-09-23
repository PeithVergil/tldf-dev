{include file="$gentemplates/index_top.tpl"}
{strip}
{if $congrat}
	<div>
		{literal}
		<script type="text/javascript">
			$(document).ready(function(){ create_popup(); });
		</script>
		{/literal}
		<div id="popupContact">
			<a id="popupContactClose" href="payment.php?sel=pay_platinum">x</a>
			<h1>{$lang.apply_platinum.$usr_gender.congrat_title}</h1>
			<p id="contactArea">{$lang.apply_platinum.$usr_gender.congrat_text}</p>
		</div>
		<div id="backgroundPopup"></div>
	</div>
{/if}
<div class="toc page-simple">
	<div class="upgrade-member tcxf-ch-la">
		<div>
			<div class="callchat_icons">
				<a href="{$site_root}/contact.php">
					<img src="{$site_root}{$template_root}/images/telephone.png" alt="Call Me" title="Call Me">
				</a>&nbsp;
				<a href="{$site_root}/contact.php">
					<img src="{$site_root}{$template_root}/images/Chat.png" alt="Chat With Me" title="Chat With Me">
				</a>
			</div>
			{*
				<!-- OLD VIDEO INSTRUCTIONS -->
				{if $auth.id_group == MM_REGULAR_GUY_ID}
					<div class="flag">
						<img src="{$site_root}{$template_root}/images/UnitedKingdomFlag.png" alt=""  >
					</div>
					<div class="player">
						<script type="text/javascript">
							var playerhost = (("https:" == document.location.protocol) ? "https://www.ezs3.com/secure/" : "http://www.ezs3.com/players/");
							document.write(unescape("%3Cscript src='" + playerhost + "mp3/meetmenowbangkok/08609B8F-EB97-EDD1-648BFD4A797F433A.js' type='text/javascript'%3E%3C/script%3E"));
						</script>
					</div>
					<div class="clear"></div>
				{/if}
			*}
			{if $auth.id_group == MM_REGULAR_LADY_ID}
				<div class="flag"><img src="{$site_root}{$template_root}/images/ThailandFlag.png" alt="" /></div>
				<div class="player">
					<script type="text/javascript">
					var playerhost = (("https:" == document.location.protocol) ? "https://meetmenowbangkok.s3.amazonaws.com/ezs3js/secure/" : "http://meetmenowbangkok.s3.amazonaws.com/ezs3js/player/");
					document.write(unescape("%3Cscript src='" + playerhost + "mp3/D60F1938-07A3-076F-FA91DEA7352822C4.js' type='text/javascript'%3E%3C/script%3E"));
					</script>
					{* <!--
						<embed src="http://ezs3.s3.amazonaws.com/player/mediaplayer46.swf" wmode="transparent" width="75" height="40" allowscriptaccess="always" flashvars="autostart=false&controlbar=bottom&type=sound&skin=http://ezs3.s3.amazonaws.com/player/skin3.swf&abouttext=About eZs3&aboutlink=http://www.ezs3.com&wmode=transparent&file=http://dqk3dcdad0x2.cloudfront.net/Become Platinum Verified2 (Thai).mp3&width=75&height=30&frontcolor=0xffffff&backcolor=0x000000&screencolor=000000&lightcolor=0x000099&bufferlength=3&volume=80&showicons=true" />
						<script type="text/javascript">
						var playerhost = (("https:" == document.location.protocol) ? "https://meetmenowbangkok.s3.amazonaws.com/ezs3js/secure/" : "http://meetmenowbangkok.s3.amazonaws.com/ezs3js/player/");
						document.write(unescape("%3Cscript src='" + playerhost + "mp3/52194CDD-095C-D045-115F3DCA1D505D5A.js' type='text/javascript'%3E%3C/script%3E"));
						</script>
					--> *}
				</div>
				<div class="clear"></div>
			{/if} 
		</div>
		<div>
			<div class="hdr2">
				{if $usr_gender == 'guy'}
					{$lang.section.apply_platinum}
				{else}
					{$lang.section.apply_platinum_t}
				{/if}
			</div>
			<!-- begin main cell -->
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="det-14-3 norm-form-table">
				{* <!--
				{if !$is_applied}
					<tr>
						<td colspan="2" align="center">
							<table cellpadding="0" cellspacing="0">
								<tr>
									<td style="padding:0px 7px 0px 3px;">
										<div class="menu_arrow" style="padding-top:25px; padding-bottom:25px;">
											<label title="Submit Information">
												<a href="platinum.php" class="menu_tab_link">Submit Information</a>
											</label>
										</div>
									</td>
									<td style="padding:0px 7px 0px 7px;">
										<div class="menu_arrow" style="padding-top:25px; padding-bottom:25px;">
											<label title="Payment Options">
												<a href="payment.php?sel=pay_platinum" class="menu_tab_link">Payment Options</a>
											</label>
										</div>
									</td>
									<td style="padding:0px 7px 0px 7px;">
										<div class="content_7" style="padding:15px 40px;">
											<a href="platinum.php?sel=apply_now" class="menu_tab_link">Apply Now</a>
										</div>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				{/if}
				--> *}
				{* <!--
				{if $form.err}
					<tr>
						<td colspan="2"><div class="error_msg">{$form.err}</div></td>
					</tr>
				{/if}
				--> *}
				<tr>
					{*
					{if $auth.id_group == MM_REGULAR_GUY_ID || $auth.id_group == MM_REGULAR_LADY_ID}
					*}
					<td valign="top" class="_pleft20">
						<div class="tcxf-ch-la chw-60-40">
							<div class="yellow_box_top">
								<div class="yellow_box_btm">
									<div class="yellow_box_mid" align="left">
										<form name="frm_platinum" method="post" action="{$form.action}" style="margin:0px; padding:0px;">
											<input type="hidden" name="sel" value="save" />
											<table width="100%" border="0" cellpadding="0" cellspacing="2">
												<tr>
													<td align="center" colspan="2" style="font-size:13px; padding-bottom:10px;"> {if $form.err} <span class="txtred" ><b>{$form.err}</b></span> {/if}
														{if $is_submit && !$is_paid}
															<p class="basic-btn_here">
																<b>&nbsp;</b><span>
																<input type="button" value="{$button.pay_platinum}" onclick="javascript:window.location.href='payment.php?sel=pay_platinum'" />
																</span>
															</p>
														{/if}
													</td>
												</tr>
												<tr>
													<!--  Best time -->
													<td colspan="2" class="txtblue bold" valign="top">
														<input type="checkbox" name="check_confirm" {if $is_submit}disabled="disabled" checked {/if} />
														<!--  YES! I'm serious about finding-->
														&nbsp;
														{$lang.apply_platinum.$usr_gender.check_confirm //edited by hachimae on 3/2/2012} 
														<br /><br />
														{$lang.apply_platinum.$usr_gender.best_times//edited by hachimae on 3/2/2012} 
													</td>
												</tr>
												<tr>
													<td class="txtblue bold">
														<!--  Weekdays -->
														<label title="{$lang.apply_platinum.best_call_time_weekdays}">
															{if $err_field.mm_best_call_time_weekdays}<font class="error">{/if}
															{$lang.apply_platinum.$usr_gender.best_call_time_weekdays //edited by hachimae on 3/2/2012}
															{if $err_field.mm_best_call_time_weekdays}</font>{/if}:
														</label>
													</td>
													<td>
														<input type="text" name="mm_best_call_time_weekdays" {if $is_submit}disabled="disabled"{/if} value="{$data.mm_best_call_time_weekdays}" maxlength="50" style="width:270px" />
													</td>
												</tr>
												<tr>
													<td class="txtblue bold">
														<!--  Saturdays -->
														<label title="{$lang.apply_platinum.best_call_time_saturdays}">
															{if $err_field.mm_best_call_time_saturdays}<font class="error">{/if}
															{$lang.apply_platinum.$usr_gender.best_call_time_saturdays //edited by hachimae on 3/2/2012}
															{if $err_field.mm_best_call_time_saturdays}</font>{/if}:
														</label>
													</td>
													<td>
														<input type="text" name="mm_best_call_time_saturdays" {if $is_submit}disabled="disabled"{/if} value="{$data.mm_best_call_time_saturdays}" maxlength="50" style="width:270px" />
													</td>
												</tr>
												<tr>
													<td class="txtblue bold">
														<!--  Sunday -->
														<label title="{$lang.apply_platinum.best_call_time_sundays}">
															{if $err_field.mm_best_call_time_sundays}<font class="error">{/if}
															{$lang.apply_platinum.$usr_gender.best_call_time_sundays //edited by hachimae on 3/2/2012}
															{if $err_field.mm_best_call_time_sundays}</font>{/if}:
														</label>
													</td>
													<td>
														<input type="text" name="mm_best_call_time_sundays" {if $is_submit}disabled="disabled"{/if} value="{$data.mm_best_call_time_sundays}" maxlength="50" style="width:270px" />
													</td>
												</tr>
												<tr><td height="30">&nbsp;</td></tr>
												<tr>
													<td width="220" class="txtblue bold">
														<label title="à¸Šà¸·à¹ˆà¸´à¸­">
															{if $err_field.fname}<font class="error">{/if}
															{$lang.users.fname}
															{if $err_field.fname}</font>{/if}&nbsp;:
														</label>
													</td>
													<td>
														<input type="text" name="fname" {if $is_submit}disabled="disabled"{/if} value="{$data.fname}" maxlength="50" style="width:270px" />
													</td>
												</tr>
												<tr>
													<td class="txtblue bold">
														<label title="à¸­à¸µà¹€à¸¡à¸¥à¹Œ">
															{if $err_field.sname}<font class="error">{/if}
															{$lang.users.sname}
															{if $err_field.sname}</font>{/if}&nbsp;:
														</label>
													</td>
													<td>
														<input type="text"  name="sname" {if $is_submit}disabled="disabled"{/if} value="{$data.sname}" maxlength="50" style="width:270px" />
													</td>
												</tr>
												<tr>
													<td class="txtblue bold">
														<label title="à¸­à¸µà¹€à¸¡à¸¥à¹Œ">
															{if $err_field.email}<font class="error">{/if}
															{$lang.users.email}
															{if $err_field.email}</font>{/if}&nbsp;:
														</label>
													</td>
													<td style="padding-bottom:4px;">
														<input type="text" name="email" {if $is_submit}disabled="disabled"{/if} value="{$data.email}" maxlength="100" style="width:270px" />
													</td>
												</tr>
												<tr>
													<td class="txtblue bold">
														<label title="à¸§à¸±à¸™ à¹€à¸”à¸·à¸­à¸™ à¸›à¸µ à¹€à¸�à¸´à¸”">
															{if $err_field.date_birthday}<font class="error">{/if}
															{$lang.users.date_birthday}
															{if $err_field.date_birthday}</font>{/if}
															&nbsp;<font class="mandatory">*</font>:
														</label>
													</td>
													<td>
														<select name="b_{$date_part1_name}" {if $is_submit}disabled="disabled"{/if}>
															{foreach item=item from=$date_part1}
																<option value="{$item.value}" {if $item.sel}selected="selected"{/if}>{$item.name}</option>
															{/foreach}
														</select>
														&nbsp;
														<select name="b_{$date_part2_name}" {if $is_submit}disabled="disabled"{/if}>
															{foreach item=item from=$date_part2}
																<option value="{$item.value}" {if $item.sel}selected="selected"{/if}>{$item.name}</option>
															{/foreach}
														</select>
														&nbsp;
														<select name="b_{$date_part3_name}" {if $is_submit}disabled="disabled"{/if}>
															{foreach item=item from=$date_part3}
																<option value="{$item.value}" {if $item.sel}selected="selected"{/if}>{$item.name}</option>
															{/foreach}
														</select>
													</td>
												</tr>
												<tr>
													<td class="txtblue bold">
														<label title="à¸ªà¸–à¸²à¸™à¸—à¸µà¹ˆà¹€à¸�à¸´à¸”">
															{if $err_field.mm_place_of_birth}<font class="error">{/if}
															{$lang.users.mm_place_of_birth}
															{if $err_field.mm_place_of_birth}</font>{/if}
															&nbsp;<font class="mandatory">*</font>:
														</label>
													</td>
													<td>
														<input type="text" name="mm_place_of_birth" {if $is_submit}disabled="disabled"{/if} value="{$data.mm_place_of_birth}" maxlength="50" style="width:270px" />
													</td>
												</tr>
												<tr>
													<td class="txtblue bold">
														<label title="social security number or ID number">
															{if $err_field.mm_id_number}<font class="error">{/if}
															{$lang.users.mm_id_number}
															{if $err_field.mm_id_number}</font>{/if}
															&nbsp;<font class="mandatory">*</font>:
														</label>
													</td>
													<td>
														<input type="text" name="mm_id_number" {if $is_submit}disabled="disabled"{/if} value="{$data.mm_id_number}" maxlength="50" style="width:270px" />
													</td>
												</tr>
												<tr>
													<td class="txtblue bold" valign="top">
														<label title="ID Type">
															{if $err_field.mm_id_type}<font class="error">{/if}
															{$lang.users.mm_id_type}
															{if $err_field.mm_id_type}</font>{/if}
															&nbsp;<font class="mandatory">*</font>:
														</label>
													</td>
													<td class="txtblue">
														<input type="text" name="mm_id_type" {if $is_submit}disabled="disabled"{/if} value="{$data.mm_id_type}" maxlength="50" style="width:270px" />
														<br /><span class="hint_text">( {$lang.users.mm_id_type_hint} )</span>
													</td>
												</tr>
												<tr>
													<td class="txtblue bold">
														<label title="à¸«à¸¡à¸²à¸¢à¹€à¸¥à¸‚à¹‚à¸—à¸£à¸¨à¸±à¸žà¸—à¹Œ">
															{if $err_field.mm_contact_phone_number}<font class="error">{/if}
															{$lang.users.mm_contact_phone_number}
															{if $err_field.mm_contact_phone_number}</font>{/if}
															&nbsp;<font class="mandatory">*</font>:
														</label>
													</td>
													<td class="txtblue bold">
														<input type="text" name="mm_contact_phone_number" {if $is_submit}disabled="disabled"{/if} value="{$data.mm_contact_phone_number}" maxlength="30" style="width:270px" />
														<br /><span class="hint_text">( {$lang.users.mm_phone_number_hint} )</span>
													</td>
												</tr>
												<tr>
													<td class="txtblue bold">
														<label title="à¸«à¸¡à¸²à¸¢à¹€à¸¥à¸‚à¹‚à¸—à¸£à¸¨à¸±à¸žà¸—à¹Œ">
															{if $err_field.mm_contact_mobile_number}<font class="error">{/if}
															{$lang.users.mm_contact_mobile_number}
															{if $err_field.mm_contact_mobile_number}</font>{/if}
															<font class="mandatory">&nbsp;*</font>:
														</label>
													</td>
													<td>
														<input type="text" name="mm_contact_mobile_number" {if $is_submit}disabled="disabled"{/if} value="{$data.mm_contact_mobile_number}" maxlength="30" style="width:270px" />
													</td>
												</tr>
												<tr>
													<td class="txtblue bold">
														<label title="à¸›à¸£à¸°à¹€à¸—à¸¨">
															{if $err_field.id_country}<font class="error">{/if}
															{$lang.users.country}
															{if $err_field.id_country}</font>{/if}
															&nbsp;<font class="mandatory">*</font>:
														</label>
													</td>
													<td>
														<select name="id_country" {if $is_submit}disabled="disabled"{/if} style="width:280px" onchange="SelectRegion('mp', this.value, document.getElementById('region_div'), document.getElementById('city_div'));">
															<option value="0">{$lang.home_page.select_default}</option>
															{foreach item=item from=$country}
																<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
															{/foreach}
														</select>
													</td>
												</tr>
												<tr>
													<td class="txtblue bold">
														{if $err_field.id_region}<font class="error">{/if}
														{$lang.users.region}
														{if $err_field.id_region}</font>{/if}
														{if $mandatory.id_region}&nbsp;<font class="mandatory">*</font>{/if}:
													</td>
													<td>
														<div id="region_div">
															{if isset($region)}
																<select name="id_region" {if $is_submit}disabled="disabled"{/if} style="width:280px">
																	<option value="0">{$lang.home_page.select_default}</option>
																	{foreach item=item from=$region}
																		<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
																	{/foreach}
																</select>
															{else}
																&nbsp;
															{/if}
														</div>
													</td>
												</tr>
												<tr>
													<td class="txtblue bold">
														<label title="à¸ˆà¸±à¸‡à¸«à¸§à¸±à¸”">
															{if $err_field.mm_city}<font class="error">{/if}
															{$lang.users.city}
															{if $err_field.mm_city}</font>{/if}
															&nbsp;<font class="mandatory">*</font>:
														</label>
													</td>
													<td>
														<input type="text" name="mm_city" {if $is_submit}disabled="disabled"{/if} value="{$data.mm_city}" maxlength="50" style="width:270px" />
													</td>
												</tr>
												<tr>
													<td class="txtblue bold">
														<label title="à¸£à¸«à¸±à¸ªà¹„à¸›à¸£à¸©à¸“à¸µà¸¢à¹Œ">
															{if $err_field.zipcode}<font class="error">{/if}
															{$lang.users.zipcode}
															{if $err_field.zipcode}</font>{/if}
															&nbsp;<font class="mandatory">*</font>:
														</label>
													</td>
													<td>
														<input type="text" name="zipcode" {if $is_submit}disabled="disabled"{/if} value="{$data.zipcode}" style="width:270px" maxlength="10" />
													</td>
												</tr>
												<tr>
													<td class="txtblue bold">
														<label title="à¸—à¸µà¹ˆà¸­à¸¢à¸¹à¹ˆà¸•à¸²à¸¡à¸šà¸±à¸•à¸£à¸›à¸£à¸°à¸Šà¸²à¸Šà¸™">
															{if $err_field.mm_address_1}<font class="error">{/if}
															{$lang.users.mm_address_1}
															{if $err_field.mm_address_1}</font>{/if}
															&nbsp;<font class="mandatory">*</font>:
														</label>
													</td>
													<td>
														<input type="text" name="mm_address_1" {if $is_submit}disabled="disabled"{/if} value="{$data.mm_address_1}" maxlength="100" style="width:270px" />
													</td>
												</tr>
												<tr>
													<td class="txtblue bold">
														<label title="à¸—à¸µà¹ˆà¸­à¸¢à¸¹à¹ˆà¸›à¸±à¸ˆà¸ˆà¸¸à¸šà¸±à¸™">
															{if $err_field.mm_address_2}<font class="error">{/if}
															{$lang.users.mm_address_2}
															{if $err_field.mm_address_2}</font>{/if}
															{if $mandatory.mm_address_2}&nbsp;<font class="mandatory">*</font>{/if}:
														</label>
													</td>
													<td>
														<input type="text" name="mm_address_2" {if $is_submit}disabled="disabled"{/if} value="{$data.mm_address_2}" maxlength="100" style="width:270px" />
													</td>
												</tr>
												<tr>
													<td class="txtblue bold">
														<label title="à¸—à¸µà¹ˆà¸­à¸¢à¸¹à¹ˆà¸›à¸±à¸ˆà¸ˆà¸¸à¸šà¸±à¸™">
															{if $err_field.mm_address_3}<font class="error">{/if}
															{$lang.users.mm_address_3}
															{if $err_field.mm_address_3}</font>{/if}
															{if $mandatory.mm_address_3}&nbsp;<font class="mandatory">*</font>{/if}:
														</label>
													</td>
													<td>
														<input type="text" name="mm_address_3" {if $is_submit}disabled="disabled"{/if} value="{$data.mm_address_3}" maxlength="100" style="width:270px" />
													</td>
												</tr>
												<tr>
													<td class="txtblue bold" valign="top">
														<label title="à¸—à¸µà¹ˆà¸­à¸¢à¸¹à¹ˆà¸›à¸±à¸ˆà¸ˆà¸¸à¸šà¸±à¸™">
															{if $err_field.mm_comments}<font class="error">{/if}
															{$lang.users.mm_platinum_submit_comment}
															{if $err_field.mm_platinum_submit_comment}</font>{/if}
															{if $mandatory.mm_platinum_submit_comment}&nbsp;<font class="mandatory">*</font>{/if}:
														</label>
													</td>
													<td>
														<textarea name="mm_platinum_submit_comment" {if $is_submit}disabled="disabled"{/if} maxlength="500" style="width:274px; height:120px;">{$data.mm_platinum_submit_comment}</textarea>
													</td>
												</tr>
												<tr>
													<td align="left" width="40%"> </td>
													<td>
														{if !$is_submit}
															<p class="basic-btn_here">
																<b>&nbsp;</b><span>
																<input type="submit" value="{$button.apply_platinum}" />
																</span>
															</p>
														{/if}
													</td>
												</tr>
											</table>
										</form>
									</div>
								</div>
							</div>
							<div>
								<div class="here-how">
									<p class="hdr3">{$lang.apply_platinum.$usr_gender.steps_to_apply}</p>
									<p>{$lang.apply_platinum.$usr_gender.step_to_apply_1}</p>
									<p>{$lang.apply_platinum.$usr_gender.step_to_apply_2}</p>
									<p>{$lang.apply_platinum.$usr_gender.step_to_apply_3}</p>
									<p>{$lang.apply_platinum.$usr_gender.step_to_apply_4}</p>
								</div>
								<div class="det-14 conget"> {$lang.apply_platinum.$usr_gender.instruction_8} </div>
							</div>
						</div>
						<br />
						<br />
						<br />
						<p>
							<span class="txtpurple txtbig"><b>{$lang.apply_platinum.$usr_gender.instruction_1}</b></span><br /><br />
							<b>{$lang.apply_platinum.$usr_gender.instruction_2}</b><br /><br />
							{$lang.apply_platinum.$usr_gender.instruction_3}<br />
							{$lang.apply_platinum.$usr_gender.instruction_4}<br /><br />
						</p>
						<p align="center"><img src="{$site_root}{$template_root}/images/platinum_bar.png" alt="Platinum Status Bar" /></p>
						{$lang.apply_platinum.$usr_gender.instruction_5}<br /><br /><br />
						{* <!--  Comment code by hachimae on 3/2/2012
							<div class="arrow_tick">
								<b>{$lang.apply_platinum.$usr_gender.benefit_heading}</b><br />
								<br />
								<ul>
									<li>{$lang.apply_platinum.$usr_gender.benefit_1}</li>
									<li>{$lang.apply_platinum.$usr_gender.benefit_2}</li>
									<li>{$lang.apply_platinum.$usr_gender.benefit_3}</li>
									<li>{$lang.apply_platinum.$usr_gender.benefit_4}</li>
									<li>{$lang.apply_platinum.$usr_gender.benefit_5}</li>
									<li>{$lang.apply_platinum.$usr_gender.benefit_6}</li>
									<li>{$lang.apply_platinum.$usr_gender.benefit_7}</li>
								</ul>
							</div>
							<p>
								<br />
								{$lang.apply_platinum.$usr_gender.instruction_6}<br /><br />
								{$lang.apply_platinum.$usr_gender.instruction_7}<br /><br />
							</p>
						--> *}
					</td>
					{* <!--
					{else}
						<td valign="top" class="text" style="padding-right:220px;">
							<div class="error_msg">{$lang.err.access_denied_2}</div>
							<div style="padding:20px 5px;">
								<p style="padding-bottom:10px;">{$lang.err.must_regular}</p>
							</div>
						</td>
					{/if}
					--> *}
				</tr>
			</table>
			<!-- end main cell -->
		</div>
	</div>
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}