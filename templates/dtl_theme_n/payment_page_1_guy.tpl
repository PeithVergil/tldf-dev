{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc">
	{if !$auth.is_applicant && !$pay_platinum && !$buy_connection}
		<p class="_extra-back"><a href="account.php">{$lang.account.back_to_my_account_page}</a></p>
	{/if}
	<div class="tcxf-ch-la page-simple upgrade-member">
		<div>
			{if !$buy_connection}
				<div class="callchat_icons">
					<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/telephone.png" alt="Call Me" title="Call Me"></a>
					<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/Chat.png" alt="Chat With Me" title="Chat With Me" ></a>
				</div>
			{/if}
			{*
				{if $auth.id_group == $smarty.const.MM_SIGNUP_GUY_ID}
					<div class="flag"> <img src="{$site_root}{$template_root}/images/UnitedKingdomFlag.png" alt="" /> </div>
					<div class="player">
						<script type="text/javascript">
						var playerhost = (("https:" == document.location.protocol) ? "https://meetmenowbangkok.s3.amazonaws.com/ezs3js/secure/" : "http://meetmenowbangkok.s3.amazonaws.com/ezs3js/player/");
						document.write(unescape("%3Cscript src='" + playerhost + "mp3/A0532003-C094-2FDC-E6C34FB5682C9248.js' type='text/javascript'%3E%3C/script%3E"));
						</script>
					</div>
				{elseif $auth.id_group == $smarty.const.MM_TRIAL_GUY_ID}
					<div class="flag"> <img src="{$site_root}{$template_root}/images/UnitedKingdomFlag.png" alt="" /> </div>
					<div class="player">
						<script type="text/javascript">
						var playerhost = (("https:" == document.location.protocol) ? "https://meetmenowbangkok.s3.amazonaws.com/ezs3js/secure/" : "http://meetmenowbangkok.s3.amazonaws.com/ezs3js/player/");
						document.write(unescape("%3Cscript src='" + playerhost + "mp3/DF9604BE-ECF9-F588-97AA310B32C09510.js' type='text/javascript'%3E%3C/script%3E"));
						</script>
					</div>
				{elseif $auth.id_group == $smarty.const.MM_SIGNUP_LADY_ID}
					<div class="flag"> <img src="{$site_root}{$template_root}/images/ThailandFlag.png" alt="" /> </div>
					<div class="player">
						<script type="text/javascript">
						var playerhost = (("https:" == document.location.protocol) ? "https://meetmenowbangkok.s3.amazonaws.com/ezs3js/secure/" : "http://meetmenowbangkok.s3.amazonaws.com/ezs3js/player/");
						document.write(unescape("%3Cscript src='" + playerhost + "mp3/521F6133-C567-B7D2-07E48400A8A93ED1.js' type='text/javascript'%3E%3C/script%3E"));
						</script>
					</div>
					<div class="clear"></div>
				{elseif $auth.id_group == $smarty.const.MM_TRIAL_LADY_ID}
					<div class="flag"> <img src="{$site_root}{$template_root}/images/ThailandFlag.png" alt="" /> </div>
					<div class="player">
						<script type="text/javascript">
						var playerhost = (("https:" == document.location.protocol) ? "https://meetmenowbangkok.s3.amazonaws.com/ezs3js/secure/" : "http://meetmenowbangkok.s3.amazonaws.com/ezs3js/player/");
						document.write(unescape("%3Cscript src='" + playerhost + "mp3/D60CAB26-A980-4870-52F0D7E1E2487A97.js'' type='text/javascript'%3E%3C/script%3E"));
						</script>
					</div>
				{else}
					<div class="flag"> <img src="{$site_root}{$template_root}/images/ThailandFlag.png" alt="" /> </div>
					<div class="player">
						<script type="text/javascript">
						var playerhost = (("https:" == document.location.protocol) ? "https://meetmenowbangkok.s3.amazonaws.com/ezs3js/secure/" : "http://meetmenowbangkok.s3.amazonaws.com/ezs3js/player/");
						document.write(unescape("%3Cscript src='" + playerhost + "mp3/521DF658-F0E6-4B06-843F14379B26F655.js' type='text/javascript'%3E%3C/script%3E"));
						</script>
					</div>
				{/if}
			*}
		</div>
		<div>
			{if $buy_connection}
				{*
					BUY CONNECTIONS FOR GUYS AND LADIES
				*}
				<div class="toc">
					<div class="hdr2" title="">
						{$header.$usr_gender.buy_connections}
					</div>
					<div class="det-14-2">
						{$header.$usr_gender.buycon_top_text}
					</div>
					{if $usr_gender eq 'guy'}
						<div class="payment-box">
							<table cellpadding="0" cellspacing="0" width="100%">
								<tr class="headrow hdr3">
									<td class="title-row" valign="bottom">
										<div class="hdr4">{$header.$usr_gender.buycon_point_head1}<br /><br /></div>
									</td>
									<td valign="middle">
										{$header.$usr_gender.buycon_head1}
									</td>
									<td valign="top">&nbsp;</td>
									<td valign="middle">
										{$header.$usr_gender.buycon_head2}
									</td>
									<td valign="top">&nbsp;</td>
									<td valign="middle">
										{$header.$usr_gender.buycon_head3}
									</td>
									<td valign="top">&nbsp;</td>
									<td valign="middle">
										{$header.$usr_gender.buycon_head4}
									</td>
								</tr>
								<tr class="altrow">
									<td><div class="hdr4">{$header.$usr_gender.buycon_point1}</div></td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
									<td>&nbsp;</td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
									<td>&nbsp;</td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
									<td>&nbsp;</td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
								</tr>
								<tr class="transrow">
									<td><div class="hdr4">{$header.$usr_gender.buycon_point2}</div></td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
									<td>&nbsp;</td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
									<td>&nbsp;</td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
									<td>&nbsp;</td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
								</tr>
								<tr class="altrow">
									<td><div class="hdr4">{$header.$usr_gender.buycon_point3}</div></td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
									<td>&nbsp;</td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
									<td>&nbsp;</td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
									<td>&nbsp;</td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
								</tr>
								<tr class="transrow">
									<td><div class="hdr4">{$header.$usr_gender.buycon_point4}</div></td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
									<td>&nbsp;</td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
									<td>&nbsp;</td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
									<td>&nbsp;</td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
								</tr>
								<tr class="altrow">
									<td><div class="hdr4">{$header.$usr_gender.buycon_point5}</div></td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
									<td>&nbsp;</td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
									<td>&nbsp;</td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
									<td>&nbsp;</td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
								</tr>
								<tr class="transrow">
									<td><div class="hdr4">{$header.$usr_gender.buycon_point6}</div></td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
									<td>&nbsp;</td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
									<td>&nbsp;</td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
									<td>&nbsp;</td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
								</tr>
								<tr class="altrow">
									<td><div class="hdr4">{$header.$usr_gender.buycon_point7}</div></td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
									<td>&nbsp;</td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
									<td>&nbsp;</td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
									<td>&nbsp;</td>
									<td><span>{$header.$usr_gender.buycon_free}</span></td>
								</tr>
								<tr class="transrow">
									<td colspan="3" class="title-row"><div class="hdr4">{$header.$usr_gender.buycon_point_head2}</div></td>
								</tr>
								<tr class="altrow">
									<td><div class="hdr4">{$header.$usr_gender.buycon_point8}</div></td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
								</tr>
								<tr class="transrow">
									<td><div class="hdr4">{$header.$usr_gender.buycon_point9}</div></td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
								</tr>
								<tr class="altrow">
									<td><div class="hdr4">{$header.$usr_gender.buycon_point10}</div></td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
								</tr>
							</table>
						</div>
					{else}
						<div class="payment-box small">
							<table cellpadding="0" cellspacing="0" width="100%">
								<tr class="headrow hdr3">
									<td class="title-row" valign="bottom">
										<div class="hdr4">{$header.$usr_gender.buycon_point_head1}<br /><br /></div>
									</td>
									<td valign="top" colspan="5">
										{$header.$usr_gender.buycon_head_main}
									</td>
								</tr>
								<tr class="transrow">
									<td class="title-row"><div class="hdr4">&nbsp;</div></td>
									<td><div class="hdr4 btn-yellow">{$header.$usr_gender.buycon_head1}</div></td>
									<td>&nbsp;</td>
									<td><div class="hdr4 btn-yellow">{$header.$usr_gender.buycon_head2}</div></td>
									<td>&nbsp;</td>
									<td><div class="hdr4 btn-yellow">{$header.$usr_gender.buycon_head3}</div></td>
								</tr>
								<tr class="altrow">
									<td><div class="hdr4">{$header.$usr_gender.buycon_point1}</div></td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
								</tr>
								<tr class="transrow">
									<td><div class="hdr4">{$header.$usr_gender.buycon_point2}</div></td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
								</tr>
								<tr class="altrow">
									<td><div class="hdr4">{$header.$usr_gender.buycon_point3}</div></td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
								</tr>
								<tr class="transrow">
									<td><div class="hdr4">{$header.$usr_gender.buycon_point4}</div></td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
								</tr>
								<tr class="altrow">
									<td><div class="hdr4">{$header.$usr_gender.buycon_point5}</div></td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
								</tr>
								<tr class="transrow">
									<td><div class="hdr4">{$header.$usr_gender.buycon_point6}</div></td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
								</tr>
								<tr class="altrow">
									<td><div class="hdr4">{$header.$usr_gender.buycon_point7}</div></td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
								</tr>
								<tr class="transrow">
									<td><div class="hdr4">{$header.$usr_gender.buycon_point8}</div></td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
								</tr>
								<tr class="altrow">
									<td><div class="hdr4">{$header.$usr_gender.buycon_point9}</div></td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
								</tr>
								<tr class="transrow">
									<td><div class="hdr4">{$header.$usr_gender.buycon_point10}</div></td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
								</tr>
								<tr class="altrow">
									<td><div class="hdr4">{$header.$usr_gender.buycon_point11}</div></td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td><span class="tick-green">&nbsp;</span></td>
								</tr>
							</table>
						</div>
					{/if}
				</div>
			{elseif $new_page2}
				{*
					NOT IN USE ???
				*}
				<div class="hdr2e" title="{$form.header}" style="padding:0px 0px 20px 15px;">
					{$header.available_options}:
				</div>
				<div style="padding-top:10px;">
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td valign="top">
								<div class="box1">
									<div align="center" class="hdr3">Regular Membership</div>
									<div align="center" class="txtbrown font16">$19.97 / Month</div>
									<div align="center" style="padding:10px 0px;">
										<img src="{$site_root}{$template_root}/images/paypal_check_out.png" alt="Paypal" title="Paypal" />
									</div>
									<div align="center">
										<a href="#"><span class="txtbrown font16">Get It Now</span></a>
									</div>
									<div>
										<p>Connect & contact with as many members as you want to.</p>
										<p>No additional fees to email!</p>
										<p>No additional fees to chat!</p>
										<p>No additional fees to webcam!</p>
										<p>One simple monthly payment.</p>
										<p>Cancel anytime.</p>
										<p>Get contacts that stay yours.</p>
										<p>Privacy settings. Be seen by those you want to see you.</p>
									</div>
									<div align="center" style="padding:8px 0px;">
										<img src="{$site_root}{$template_root}/images/paypal_check_out.png" alt="Paypal" title="Paypal" />
									</div>
									<div align="center">
										<p><a href="#">Learn More About Platinum Membership</a></p>
										<p><a href="#">Learn More About Platinum Plus Membership</a></p>
									</div>
								</div>
							</td>
							<td valign="top">
								<div class="box2">
									<div align="center" class="hdr3">Platinum Membership</div>
									<div align="center" class="txtbrown font16">$29.97 / Month</div>
									<div align="center" style="padding:10px 0px;">
										<img src="{$site_root}{$template_root}/images/paypal_check_out.png" alt="Paypal" title="Paypal" />
									</div>
									<div align="center">
										<a href="#"><span class="txtbrown font16">Get It Now</span></a>
									</div>
									<div>
										<p>All the great features of Regular Membership</p>
										<p align="center"><b>Plus</b></p>
										<p>Fast track your success by having our team manage your profile.</p>
										<p>Find the best ladies first with our Early Bird notification of prospects matching your specifications.</p>
										<p>Attract more qualified ladies with our personal introduction and promotion to qualified prospects service.</p>
										<p>Phone service & support.</p>
									</div>
									<div align="center" style="padding:8px 0px;">
										<img src="{$site_root}{$template_root}/images/paypal_check_out.png" alt="Paypal" title="Paypal" />
									</div>
									<div align="center">
										<p><a href="#">Learn More About Regular Membership</a></p>
										<p><a href="#">Learn More About Platinum Plus Membership</a></p>
									</div>
								</div>
							</td>
							<td valign="top">
								<div class="box3">
									<div align="center" class="hdr3">Platinum Plus Membership</div>
									<div align="center" class="txtbrown font16">$5991 / 12 Months Unlimited Personal Introductions & Dates</div>
									<div align="center" style="padding:12px 0px 6px 0px;">
										<a href="#"><img src="{$site_root}{$template_root}/images/express_interest_btn.png" alt="Express Interest" title="Express Interest" /></a>
									</div>
									<div>
										<p>Meet Gorgeous & Qualified Ladies In Person On Your Own Thai Lady Dating Events™ Tour</p>
										<p>Unlimited Dating Introductions With As Many Ladies As You Wish For Your Entire 12 Month Membership Program!</p>
										<p>Comfort & Security All The Way With Your Own Driver & Limo For All Dating Events & Airport Transfers.</p>
										<p>Privileged & Personal Matching Services From Your Own Dating Concierge. We’ll Personally Match You With Ladies We Know & Vouch For.</p>
										<p>Our "No Scams" Crazy Money Back Guarantee! Your Date Is As Described In Her Profile, Legitimate & Eligible For A Relationship With You, Or Your Money Back & Your Travel Expenses Refunded! Period.</p>
										<p>Places Are Limited. Unlimited Exclusive Dates With The Finest Ladies For A Full 12 Months & Managed For You.</p>
										<p>Easy Payment of 3 x $1997</p>
									</div>
									<div align="center" style="padding:8px 0px;">
										<a href="#"><img src="{$site_root}{$template_root}/images/express_interest_btn.png" alt="Express Interest" title="Express Interest" /></a>
									</div>
								</div>
							</td>
						</tr>
					</table>
				</div>
				<div>&nbsp;</div>
			{else}
				{*
					SOCIAL NETWORKING GROUP / PERIOD SELECTION
				*}
				{if $selected_group}
					<div class="hdr2e" title="{$form.header}">
						{$header.available_options}:
					</div>
				{/if}
				<div class="pay-box">
					<b>&nbsp;</b><b>&nbsp;</b>
					<div>
						<table border="0" cellpadding="0" cellspacing="1" class="w80">
							{if $form.err}
								<tr>
									<td><div class="error_msg">{$form.err}</div></td>
								</tr>
							{/if}
							{*
								<!-- GROUP SELECTION: NOT NEEDED FOR TLDF -->
								<tr>
									<td valign="top" style="padding-top: 10px">
										<div class="header">{$lang.payment.groupselect}</div>
										<table border="0" cellpadding="0" cellspacing="0">
											{if $form.use_credits_for_membership_payment}
												<tr valign="middle">
													<td height="25" class="text">{$header.account_count}:&nbsp;&nbsp;&nbsp;</td>
													<td class="text"><b>{$data.count} &nbsp;{$data.account_currency}</b></td>
												</tr>
											{/if}
											<tr valign="middle">
												<td height="25" class="text">{$header.present_groups}:&nbsp;&nbsp;&nbsp;</td>
												<td class="text"><b>{$data.present_group}</b></td>
											</tr>
											<tr valign="middle">
												<td height="25" class="text">{$header.avialable_groups}:&nbsp;&nbsp;&nbsp;</td>
												<td class="text">
													<form name="change_group" action="payment.php" method="get">
														<input type="hidden" name="sel" value="group">
														<select name="group" style="width:150px" onchange="javascript: document.forms.change_group.submit();">
															<option value="">{$lang.payment.groupselect}</option>
															{foreach item=item from=$groups}
																<option value="{$item.id}"{if $item.sel} selected="selected"{/if}>{$item.name}</option>
															{/foreach}
														</select>
													</form>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td height="1">
										<div style="height: 1px; margin: 10px 0px" class="delimiter"></div>
									</td>
								</tr>
							*}
							{if $selected_group}
								<tr>
									<td valign="top">
										{*
											<div class="title2">{$lang.users.payment_module_user}</div>
										*}
										<div class="txtblue txtbig"{if $offline_payment_pending} style="float:right; width:520px;" {/if}>
											{if $auth.is_applicant}
												<div style="float:right; width:200px; padding-top:5px;">
													<div class="{if $steps.application_fee}step_btn_comp{else}step_btn_4{/if}">
														<label title="à¹€à¸¥à¸·à¸­à¸�à¸Šà¹ˆà¸­à¸‡à¸—à¸²à¸‡à¸�à¸²à¸£à¸Šà¸³à¸£à¸°à¹€à¸‡à¸´à¸™">
															<span class="txtwhite" style="padding-top:9px; height:69px;"><b>{$button.pay_application_fee}</b></span>
														</label>
													</div>
												</div>
												<div style="float:right; padding-right:15px;">
													{$lang.payment.applicant_instructions}
												</div>
											{elseif $auth.is_trial}
												{$lang.payment.free_trial_instructions}
											{elseif $auth.is_regular}
												{$lang.payment.regular_instructions}
											{elseif $auth.is_regular_inactive}
												{$lang.payment.regular_inactive_instructions}
											{elseif $auth.is_platinum}
												{$lang.payment.platinum_instructions}
											{elseif $auth.is_platinum_inactive}
												{$lang.payment.platinum_inactive_instructions}
											{elseif $auth.is_elite}
												{$lang.payment.elite_instructions}
											{elseif $auth.is_elite_inactive}
												{$lang.payment.elite_inactive_instructions}
											{/if}
										</div>
										{if $offline_payment_pending}
											<div>
												{$lang.account.additional_info_label}:<br />
												<span style="font-weight:bold; color:red;" class="text">
													{if $offline_payment_pending == 'atm_payment'}
														{$lang.account.atm_payment_pending}
													{elseif $offline_payment_pending == 'wire_transfer'}
														{$lang.account.wire_transfer_pending}
													{elseif $offline_payment_pending == 'bank_cheque'}
														{$lang.account.bank_cheque_pending}
													{/if}
												</span>
											</div>
										{/if}
										<div class="clear"></div>
										<div style="height:5px"></div>
										<div class="box_info">
											<div>
												<form name="select_period" action="payment.php" method="get">
													<input type="hidden" name="sel" value="save_1">
													{if $periods}
														<table border="0" cellspacing="8" cellpadding="0">
															{if $auth.is_applicant}
																<tr>
																	<td valign="top"><input type="radio" name="period_id" value="{$trial_id_period}" /></td>
																	<td width="330">
																		<div class="text_head">FREE Trial Membership</div>
																		{$lang.payment.free_trial_instructions}
																	</td>
																	<td width="100" class="text_head" align="right">
																	</td>
																</tr>
																<tr><td colspan="3"><hr class="line-norm" /></td></tr>
															{/if}
															{foreach item=item from=$periods}
																{if $item.amount == 1}
																	<tr>
																		<td>&nbsp;</td>
																		<td style="font-weight:bold;">
																			<div class="hdr2" {if !$item.recurring}style="margin-bottom:5px;"{/if}>
																				{if $item.recurring}Recurring{else}Non Recurring{/if} Options:
																			</div>
																		</td>
																	</tr>
																{/if}
																<tr>
																	<td valign="top"><input type="radio" name="period_id" value="{$item.id}" /></td>
																	<td width="330" class="text_head" valign="middle">
																		{* <!--{$item.amount} {$item.period} {if $item.name == 'Regular Guy' || $item.name == 'Regular Lady'} Regular {else} {$item.name} {/if} {if $item.recurring}Recurring{else}Non Recurring{/if} Membership:--> *}
																		{$item.amount} {$item.period} Membership:
																		{if $item.recurring && $item.amount == 1}
																			<div class="det-14">{$lang.payment.recuring_best_deal}</div>
																		{/if}
																		{*
																			{if $item.amount == 1 && ($item.name == 'Regular Guy' || $item.name == 'Regular Lady')}
																				<div style="font-weight:normal">{$lang.payment.regular_instructions}</div>
																			{/if}
																			{if $auth.is_applicant && $item.recurring && $item.trial_amount == 1}
																				<div style="font-weight:normal"> + Get the 1st month FREE<br/>(Best Deal - Cancel any Time)</div>
																			{/if}
																		*}
																	</td>
																	<td width="100" class="text_head" align="right" valign="middle">
																		{if $item.cost_2}
																			{$item.cost_2_formatted} {$data.account_currency_2}
																		{else}
																			{$item.cost_formatted} {$data.account_currency}
																		{/if}
																	</td>
																	{if $item.amount == 1 && $item.recurring}
																		<td valign="middle"><img src="{$site_root}{$template_root}/images/best_deal.png" title="Best Deal"></td>
																	{/if}
																</tr>
																{if $item.amount == 1 && $item.recurring}
																	<tr><td colspan="3"><hr class="line-norm" /></td></tr>
																{/if}
															{/foreach}
															{*
																<!-- old precondition for platinum upgrade -->
																{if $auth.is_applicant || $auth.is_trial || $auth.is_regular}
															*}
															{if !$auth.is_platinum}
																<tr><td colspan="3"><hr class="line-norm" /></td></tr>
																<tr>
																	<td valign="top"><a href="platinum_match.php"><img src="{$site_root}{$template_root}/images/radio_disabled.png"></a></td>
																	<td width="330">
																		<strong class="hd14">Platinum Membership</strong>
																		<div class="det-14">{$lang.payment.extra_platinum_upgrade_instructions}</div>
																	</td>
																	<td width="100" class="text_head" align="center">
																		<a href="platinum_match.php" class="app-l">Apply Now</a>
																		{*
																			<!-- old precondition for platinum upgrade -->
																			{if $auth.is_trial || $auth.is_regular}
																				<a href="platinum_match.php">Apply Now</a>
																			{else}
																				By Application Inside Member's Area
																			{/if}
																		*}
																	</td>
																	<td valign="middle"><img src="{$site_root}{$template_root}/images/become_platinum.png" title="Become Platinum Verified!"></td>
																</tr>
																{*
																	<!-- old precondition for platinum upgrade -->
																	{if $auth.is_applicant || $auth.is_trial || $auth.is_regular}
																*}
																<tr><td colspan="3"><hr class="line-norm" /></td></tr>
																<tr>
																	<td valign="top"><a href="events_booking.php"><img src="{$site_root}{$template_root}/images/radio_disabled.png"></a></td>
																	<td width="330">
																		<strong class="hd14">Dating Events</strong>
																		<div class="det-14">
																			{if $auth.gender == GENDER_MALE}
																				{$lang.payment.extra_dating_events_instructions_men|escape}
																			{else}
																				{$lang.payment.extra_dating_events_instructions_lady|escape}
																			{/if}
																		</div>
																	</td>
																	<td width="100" class="text_head" align="center">
																		<a href="events_booking.php" class="app-l">Apply Now</a>
																		{*
																			<!-- old precondition for platinum upgrade -->
																			{if $auth.is_trial || $auth.is_regular}
																				<a href="events_booking.php">Apply Now</a>
																			{else}
																				By Application Inside Member's Area
																			{/if}
																		*}
																	</td>
																	<td valign="middle"><img src="{$site_root}{$template_root}/images/meet_ladies.png" title="Meet Our Finest Ladies In Person"></td>
																</tr>
															{/if}
															{*
																<!-- precondition for platinum upgrade -->
																{/if}
															*}
															<tr>
																<td></td>
																<td colspan="2" align="center" style="padding-top:40px;">
																	<input type="image" src="{$site_root}{$template_root}/images/btn_shopping_cart.png" title="Add to Shopping Cart" />
																</td>
															</tr>
														</table>
													{/if}
												</form>
											</div>
											<div style="float:left; width:250px;">
												<div style="padding:5px;">
													<div align="center">
														<table cellpadding="0" cellspacing="0">
															<tr>
																<td>
																	<img src="{$site_root}{$template_root}/images/we-accept.png" title="We Accept">
																</td>
																<td>&nbsp;&nbsp;
																	<!-- PayPal Logo -->
																	{literal}
																	<script type="text/javascript">
																	function openBpWindow() {
																		window.open('https://www.paypal-apac.com/buyer-protection.html?size=180x113&url=' + escape(window.location.hostname) + '&page=' + escape(window.location.pathname),'olcwhatispaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=400, height=260');
																	};
																	//document.write("<a href=\"javascript:openBpWindow()\"><img  src=\"https://www.paypal-apac.com/images/logos/PayPal-Buyer-Protection-Logo-180p-x-113p.gif\" border=\"0\" alt=\"Acceptance Mark\"><\/a>");
																	document.write("<a href=\"javascript:openBpWindow()\"><img src=\"http://thailadydatefinder.com/templates/dtl_theme_n/images/paypal_buyer_protection.png\" border=\"0\"  width=\"65\" height=\"90\" alt=\"Acceptance Mark\"><\/a>");
																	document.write("<img src=\"https://www.paypal-apac.com/tracking/?size=180x113&url=" + escape(window.location.hostname) + "&page=" + escape(window.location.pathname) + "\" border=\"0\" alt=\"\" width=\"1\" height=\"1\">");
																	</script>
																	{/literal}
																	<!-- PayPal Logo -->                                            
																</td>
															</tr>
														</table>
													</div>
												</div>
											</div>
											<div class="clear"></div>
											{* <!--
											<div style="margin: 20px 0px 5px 0px;" class="text">{$header.group_description}:</div>
												<table border="0" cellspacing="0" cellpadding="0">
													{foreach item=item from=$description}
														<tr>
															<td height="30" width="30%" class="text_head">{$item.name}</td>
															<td height="30" class="text">{$item.descr}</td>
														</tr>
														<tr>
															<td height="1" colspan="2">
																<div style="height: 1px;" class="delimiter"></div>
															</td>
														</tr>
													{/foreach}
													{foreach item=item from=$add_descr}
														<tr>
															<td height="30" width="30%"><font class="text_head">{$item.name}:</font>&nbsp; <font class="text">{$item.count}&nbsp;{$item.add_name}</font> </td>
															<td class="text">{$item.descr}</td>
														</tr>
														<tr>
															<td height="1" colspan="2">
																<div style="height: 1px;" class="delimiter"></div>
															</td>
														</tr>
													{/foreach}
												</table>
											</div>
											--> *}
										</div>
									</td>
								</tr>
							{/if}
						</table>
					</div>
				</div>
			{/if}
		</div>
	</div>
</div>
{if $buy_connection}
<div class="bottom-section">
	<div class="toc" style="width:986px;">
		<div class="tcxf-ch-la buy-conn-btm {if $usr_gender eq 'lady'}lady-cls{/if}">
			<div style="width:30px;">&nbsp;</div>
			<div style="width:190px;">
				<img src="{$site_root}{$template_root}/images/paypal-buyer-protection.png" alt="" />
				{if $usr_gender eq 'guy'}
					<br /><img src="{$site_root}{$template_root}/images/paypal-2.png" width="180" alt="" />
				{/if}
			</div>
			{if $usr_gender eq 'guy'}
				<div style="width:120px;">
					&nbsp;
				</div>
				<div style="width:164px;">
					<div class="box-frame2">
						<div class="box-in" align="center">
							<p class="head">{$header.$usr_gender.buycon_bot_head1}</p>
							<p>Add&nbsp; <input type="text" id="txt_buy_points" name="txt_buy_points" style="width:50px;" />Points</p>
							<p>{$header.$usr_gender.buycon_bot_head1_1}</p>
							<p><i>{$header.$usr_gender.buycon_bot_instruct}</i></p>
						</div>
						<div align="center">
							<div class="btn-new"><a href="javascript: void(0);" onclick="buy_points_submit();"><span>{$header.$usr_gender.buycon_add_to_cart}</span></a></div>
						</div>
					</div>
				</div>
				<div style="width:160px;">
					<div class="box-frame2">
						<div class="box-in" align="center">
							<p class="head">{$header.$usr_gender.buycon_bot_head2}</p>
							<p>{$header.$usr_gender.buycon_bot_head2_1}</p>
							<p><i>{$header.$usr_gender.buycon_bot_instruct}</i></p>
						</div>
						<div align="center">
							<div class="btn-new">
								{*<!--<a href="payment.php?sel=update_account&cre_pack=bronze"><span>{$header.$usr_gender.buycon_add_to_cart}</span></a>-->*}
								<a href="payment.php?sel=credit_pack&amp;pack=bronze"><span>{$header.$usr_gender.buycon_add_to_cart}</span></a>
							</div>
						</div>
					</div>
				</div>
				<div style="width:160px;">
					<div class="box-frame2">
						<div class="box-in" align="center">
							<p class="head">{$header.$usr_gender.buycon_bot_head3}</p>
							<p>{$header.$usr_gender.buycon_bot_head3_1}</p>
							<p><i>{$header.$usr_gender.buycon_bot_instruct}</i></p>
						</div>
						<div align="center">
							<div class="btn-new">
								{*<!--<a href="payment.php?sel=update_account&cre_pack=silver"><span>{$header.$usr_gender.buycon_add_to_cart}</span></a>-->*}
								<a href="payment.php?sel=credit_pack&pack=silver"><span>{$header.$usr_gender.buycon_add_to_cart}</span></a>
							</div>
						</div>
					</div>
				</div>
				<div style="width:160px;">
					<div class="box-frame2">
						<div class="box-in" align="center">
							<p class="head">{$header.$usr_gender.buycon_bot_head4}</p>
							<p>{$header.$usr_gender.buycon_bot_head4_1}</p>
							<p><i>{$header.$usr_gender.buycon_bot_instruct}</i></p>
						</div>
						<div align="center">
							<div class="btn-new">
								{*<!--<a href="payment.php?sel=update_account&cre_pack=gold"><span>{$header.$usr_gender.buycon_add_to_cart}</span></a>-->*}
								<a href="payment.php?sel=credit_pack&pack=gold"><span>{$header.$usr_gender.buycon_add_to_cart}</span></a>
							</div>
						</div>
					</div>
				</div>
			{elseif $usr_gender eq 'lady'}
				<div style="width:280px;" align="center">
					<div style="padding-top:55px;">
						<img src="{$site_root}{$template_root}/images/paypal-2.png" width="180" alt="" />
					</div>
				</div>
				<div style="width:162px;">
					<div class="box-frame2">
						<div class="box-in" align="center">
							<p class="head">{$header.$usr_gender.buycon_bot_head1}</p>
							<p>{$header.$usr_gender.buycon_bot_head1_1}</p>
							<p>{$header.$usr_gender.buycon_bot_instruct}</p>
						</div>
						<div align="center">
							<div class="btn-new"><a href="payment.php?sel=save_1&period_id=7&x=98&y=26"><span>{$header.$usr_gender.buycon_add_to_cart}</span></a></div>
						</div>
					</div>
				</div>
				<div style="width:162px;">
					<div class="box-frame2">
						<div class="box-in" align="center">
							<p class="head">{$header.$usr_gender.buycon_bot_head2}</p>
							<p>{$header.$usr_gender.buycon_bot_head2_1}</p>
							<p>{$header.$usr_gender.buycon_bot_instruct}</p>
						</div>
						<div align="center">
							<div class="btn-new"><a href="payment.php?sel=save_1&period_id=8&x=104&y=28"><span>{$header.$usr_gender.buycon_add_to_cart}</span></a></div>
						</div>
					</div>
				</div>
				<div style="width:162px;">
					<div class="box-frame2">
						<div class="box-in" align="center">
							<p class="head">{$header.$usr_gender.buycon_bot_head3}</p>
							<p>{$header.$usr_gender.buycon_bot_head3_1}</p>
							<p>{$header.$usr_gender.buycon_bot_instruct}</p>
						</div>
						<div align="center">
							<div class="btn-new"><a href="payment.php?sel=save_1&period_id=9&x=111&y=17"><span>{$header.$usr_gender.buycon_add_to_cart}</span></a></div>
						</div>
					</div>
				</div>
			{/if}
		</div>
		<!-- bottom box -->
		{if $usr_gender eq 'lady'}
			<div class="toc" style="width:986px;">
				<div class="box-yellow" style="margin:10px 0px 0px 30px;">
					<div class="tcxf-ch-la chw-50-50">
						<div class="hdr3">
							{$header.$usr_gender.buycon_pop_head1}<br />
							<img src="{$site_root}{$template_root}/images/logo-kasikorn.png" alt="Kasikorn Bank" title="Kasikorn Bank"><br />
							{$header.$usr_gender.buycon_pop_text1}
						</div>
						<div class="hdr3">
							{$header.$usr_gender.buycon_pop_head2}<br />
							<div style="padding-top:15px;">{$header.$usr_gender.buycon_pop_text2}</div>
						</div>
					</div>
				</div>
			</div>
		{/if}
	</div>
</div>
<script type="text/javascript">
{literal}
function buy_points_submit() {
	val = document.getElementById('txt_buy_points').value;
	if (val > 0) {
		window.location.href = "payment.php?sel=update_account&cre_pack=custom&amt=" + val;
	} else {
		jAlert('Please Fill Valid Amount');
		return false;
	}
}
{/literal}
</script>
{/if}
{/strip}
{include file="$gentemplates/index_bottom.tpl"}