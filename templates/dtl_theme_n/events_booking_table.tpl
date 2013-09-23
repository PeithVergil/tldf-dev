{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple">
{if $form.err}
	<div class="error_msg">{$form.err}</div>
{/if}
{if $form.res}
	<div class="error_msg">{$form.err}</div>
{/if}
<!-- begin main cell -->
<div class="upgrade-member tcxf-ch-la">
<div>
	<div class="callchat_icons">
		<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/telephone.png" alt="Call Me" title="Call Me"></a> <a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/Chat.png" alt="Chat With Me" title="Chat With Me"></a>
	</div>
	{if $auth.id_group == MM_PLATINUM_GUY_ID}
		<div class="flag">
			<img src="{$site_root}{$template_root}/images/UnitedKingdomFlag.png" alt="">
		</div>
		<div class="player">
			<script type="text/javascript">
			var playerhost = (("https:" == document.location.protocol) ? "https://www.ezs3.com/secure/" : "http://www.ezs3.com/players/");
			document.write(unescape("%3Cscript src='" + playerhost + "mp3/meetmenowbangkok/08609B8F-EB97-EDD1-648BFD4A797F433A.js' type='text/javascript'%3E%3C/script%3E"));
			</script>
		</div>
		<div class="clear"></div>
	{/if}
	{if $auth.id_group == MM_PLATINUM_LADY_ID}
		<div class="flag">
			<img src="{$site_root}{$template_root}/images/ThailandFlag.png" alt="" align="left">
		</div>
		<div class="player">
			<script type="text/javascript">
			var playerhost = (("https:" == document.location.protocol) ? "https://meetmenowbangkok.s3.amazonaws.com/ezs3js/secure/" : "http://meetmenowbangkok.s3.amazonaws.com/ezs3js/player/");
			document.write(unescape("%3Cscript src='" + playerhost + "mp3/521F6133-C567-B7D2-07E48400A8A93ED1.js' type='text/javascript'%3E%3C/script%3E"));
			</script>
		</div>
		<div class="clear"></div>
	{/if}
</div>
<div>
<div class="_pleft20">
	<div class="hdr2x">{$lang.section.events_booking}</div>
	{if !$form.success}
		<div>
			<div>
				<div>
					<div class="pay-box">
						<b></b><b></b>
						<div>
							<form name="frm_events_booking" method="post" action="{$form.action}" style="margin:0px; padding: 20px;">
								<input type="hidden" name="sel" value="send_request" />
								<div class="text" style="line-height:20px;">
									<p style="font-weight:bold; padding: 0; margin: 0" class="txtpurple" > "Yes! I'm Interested In Coming To Bangkok And Finding Someone Absolutely Sensational Through My Own Personalized Thai Lady Dating Events&trade; Program. Here's The Time I Want To Be There And Here's The Ladies I Would Like To Meet." </p>
								</div>
								{if $list}
								<div style="padding-top:10px;">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tr>
											<td valign="top" width="230">I Expect To Be In Bangkok Between:&nbsp;&nbsp;</td>
											<td>
												<table cellpadding="0" cellspacing="0">
													<tr>
														<td align="left" style="padding-right:5px;">
															<label title="From Date"> {if $err_field.date_from}<font class="error">{/if}
																{$lang.events_booking.date_from}&nbsp;*
																{if $err_field.date_from}</font>{/if} </label>
															<br />
															<input type="text" id="date_from" name="date_from" class="date-pick" maxlength="20" style="width:90px;" value="{$form.date_from}" />
														</td>
														<td align="left">
															<label title="To Date"> {if $err_field.date_to}<font class="error">{/if}
																{$lang.events_booking.date_to}&nbsp;*
																{if $err_field.date_to}</font>{/if} </label>
															<br />
															<input type="text" id="date_to" name="date_to" class="date-pick" maxlength="20" style="width:90px;" value="{$form.date_to}" />
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</div>
								<div style="padding-top:10px;">
									<label title="The Ladies I Want To Meet With Are"> {if $err_field.want_to_meet}<font class="error">{/if}
										{$lang.events_booking.want_to_meet}&nbsp;*:
										{if $err_field.want_to_meet}</font>{/if}
									</label>
								</div>
								<div style="padding-top:10px;">
									<table cellpadding="0" cellspacing="0">
										{section name=u loop=$list}
										<tr>
											<td valign="middle">
												<input type="checkbox" name="lady_{$list[u].number}" value="1" {if $lady_stat[u].is_check}checked="checked"{/if} >
												&nbsp;&nbsp;
												<input type="hidden" name="hid_{$list[u].number}" value="{$list[u].id}">
												<input type="hidden" name="hnick_{$list[u].number}" value="{$list[u].nickname}">
												<input type="hidden" name="hage_{$list[u].number}" value="{$list[u].age}">
											</td>
											<td valign="middle" style="padding:1px 0px;"> <a href="{$list[u].profile_link}"><img src="{$list[u].icon_path}" class="icon" width="20" height="24" alt=""></a> &nbsp;&nbsp; </td>
											<td valign="middle"><b><a href="{$list[u].profile_link}" >{$list[u].nickname}</a></b>&nbsp;&nbsp;</td>
											<td valign="middle" style="font-size:90%">{$list[u].age} {$lang.home_page.ans}</td>
										</tr>
										{/section}
									</table>
									<input type="hidden" name="list_length" value="{$list|@count}" />
								</div>
								<div style="padding-top:10px;">
									<table cellpadding="0" cellspacing="0">
										<tr>
											<td valign="top" style="padding-top:3px;">
												<input type="checkbox" name="other_ladies" value="1" {if $form.other_ladies}checked="checked"{/if} style="margin-right:5px;" />
											</td>
											<td valign="top">
												<label title="I Also Want To Meet Additional Ladies That You Know Match My Preferences"> <b>I Also Want To Meet Additional Ladies That You Know Match My Preferences.</b> </label>
												&nbsp;&nbsp;&nbsp;
												(subject to my approval, of course)
											</td>
										</tr>
										<tr>
											<td colspan="2" style="padding-top:10px; padding-bottom:3px;">
												<label title="">Here's What I Would Like To Know or Discuss Further About This Program</label>
											</td>
										</tr>
										<tr>
											<td colspan="2">
												<textarea name="like_to_know" style="width:420px; height:70px;" >{$form.like_to_know}</textarea>
											</td>
										</tr>
										<tr>
											<td colspan="2" style="padding:10px 0px;">
												<table width="100%" cellpadding="0" cellspacing="0">
													<tr>
														<td>
															<label title="The Best Number To Call Me On Is"> {if $err_field.best_number}<font class="error">{/if}
																{$lang.events_booking.best_number}&nbsp;*:
																{if $err_field.best_number}</font>{/if} </label>
															<br />
															<input type="text" name="best_number" value="{$form.best_number}" maxlength="30" style="width:175px;" />
														</td>
														<td align="right"> </td>
													</tr>
													<tr>
														<td style="padding-top:10px;">
															<label title="The Best Time To Call Me Is"> {if $err_field.best_time}<font class="error">{/if}
																{$lang.events_booking.best_time}&nbsp;*:
																{if $err_field.best_time}</font>{/if} </label>
															<br>
															<input type="text" name="best_time" value="{$form.best_time}" maxlength="30" style="width:175px;" />
														</td>
														<td align="right" style="padding-top:10px;"> </td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td valign="top" style="padding-top:3px;">
												<input type="checkbox" name="send_info" value="1" {if $form.send_info}checked="checked"{/if} style="margin-right:5px;" />
											</td>
											<td>
												<label title="Send Information About Thai Lady Dating Events To My Address On File"> <b>Send Information About Thai Lady Dating Events To My Address On File.</b> </label>
											</td>
										</tr>
										<tr>
											<td colspan="2" style="padding-top:15px;">
												<div class="content_7 pointer" onclick="submitEventsForm();"> <span><b class="normal-btn">{$lang.button.send_interest_form}</b></span> </div>
											</td>
										</tr>
										<tr>
											<td colspan="2" style="padding-top:20px;">
												<div class="stop_icon justify">{$lang.index_page.note}</div>
											</td>
										</tr>
									</table>
								</div>
								{else}
									<div style="padding-top:40px;"> <font class="error">{$lang.events_booking.hotlist_list_empty}</font>
										<div style="font-size:13px;text-align:justify; line-height:18px; padding:40px 0px;"> {$lang.events_booking.intro_text} </div>
									</div>
								{/if}
							</form>
						</div>
					</div>
					<div style="padding:12px;" class="justify"> {$lang.events_booking.submit_instruction} </div>
				</div>
				<div>
					<div class="txtpurple" style="padding:10px 0px;"><b>{$lang.events_booking.intro_text}</b></div>
					<div class="speech">{$lang.events_booking.instructions}</div>
					<div align="center" style="padding:20px 0px 5px 0px;">
						<p><img src="{$site_root}{$template_root}/images/watch_presentation.png" alt="Watch the Dating Presentation" /></p>
						<p align="center" style="padding-top:20px;"> <a href="dating_events.php" title="Thai Lady Dating Events&trade;"><b>Watch The Presentation To Learn More About ThaiLadyDatingEvents&trade;</b></a> <img src="{$site_root}{$template_root}/images/pointer.png" alt="" /> </p>
					</div>
					<div align="center" style="margin-left:290px;">
						<p class="basic-btn_here">
							<b></b><span>
							<input type="button" onclick="window.location.href='{$site_root}/request_info.php'" value="{$lang.dating_events.request_info_pack}" />
							</span>
						</p>
					</div>
				</div>
				<!-- end main cell -->
			</div>
		{/if}
	</div>
</div>
{/strip}
{literal}
<script type="text/javascript">
function submitEventsForm() {
	document.frm_events_booking.submit();
}
</script>
<script type="text/javascript">
	$(function(){
		$("#date_from, #date_to").datepicker({
			beforeShow: customRange,
			numberOfMonths: 1,
			stepMonths: 1,
			//showOn: "button",
			//buttonImage: "/assets/images/icons/date.png",
			//buttonText: "select start date",
			dateFormat: 'M d, yy'
			//minDate:1
			//maxDate:'+12M'
		});
		$('.date-pick').datepicker({
			onSelect: function(dateText) {
				// compare the selected date and today
			}
		});
	});
	
	function customRange(input) {
		strMinDate = (input.id == "date_to" ? $("#date_from").datepicker("getDate") : 1);
		strMaxDate = (input.id == "date_from" ? $("#date_to").datepicker("getDate") : '+12M');
			
		return { minDate: strMinDate, maxDate: strMaxDate };
	}
</script>
{/literal}
{include file="$gentemplates/index_bottom.tpl"}