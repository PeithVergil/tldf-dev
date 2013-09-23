{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font>
<font class=red_sub_header>&nbsp;|&nbsp;{$header.promo_mails}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.promotions}</div>
<table width="100%">
	<tr valign=top>
		<!-- user_select -->
		<td>
			<form name="add_form" action="{$form.action}" method="post">
				{$form.add_hiddens}
				<input type="hidden" name="title" value="{$form.title}" />
				<input type="hidden" name="head" value="{$form.head}" />
				<input type="hidden" name="body_text" value="{$form.body_text}" />
				<input type="hidden" name="footer_text" value="{$form.footer_text}" />
				<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
					{if $form.status eq '1'}
						<tr>
							<td bgcolor="#FFFFCC">
								<b>Sent On:</b> {$form.send_date}
							</td>
						</tr>
					{/if}
					<tr>
						<td class="main_header_text" >
							<b>SUBJECT:</b> {$form.title}
						</td>
					</tr>
					<tr>
						<td class="main_header_text" >
							Profile(s) to Promote:
							{if $form.mode neq 'view_promo'}
								<input type="button" value="{$button.add_more_profile}" class="button" onclick="javascript:window.open('{$form.promolink}', 'promo_profile', 'height=650, resizable=yes, scrollbars=yes,width=750, menubar=no,status=yes');" name=perm>
								<input type="hidden" name="promo_user_count" id="promo_user_count" value="{$promo_user_count}" />
							{/if}
							<div id="div_promo_users" style="padding-top:3px;">
								{section name=f loop=$promo_user}
									<p style="padding:0px; margin:0px;">
										{if $form.mode eq 'view_promo'}
											<a href="{$promo_user[f].profile_link}" target="_blank"><img src="{$promo_user[f].icon_path}" class="icon" alt="{$promo_user[f].name}" width="25"></a><span style="position:relative; top:-12px; font-weight:normal;">&nbsp;&nbsp; {$promo_user[f].name}</span>
										{else}
											<a href="{$promo_user[f].profile_link}" target="_blank"><img src="{$promo_user[f].icon_path}" class="icon" alt="{$promo_user[f].name}" width="25"></a>
											<span style="position:relative; top:-12px;">
												<input type=checkbox name="promo_user[{$smarty.section.f.index}]" value='{$promo_user[f].id}' {if $promo_user[f].sele eq 1}checked{/if}>
												{$promo_user[f].name}
											</span>
										{/if}
									</p>
								{/section}
							</div>
						</td>
					</tr>
					<tr>
						<td class="main_header_text" >
							User Groups:<br />
							{section name=f loop=$groups}
								<p style="padding:0px; margin:0px;">
									{if $form.mode eq 'view_promo'}
										{if $groups[f].sele eq 1}
											<span style="font-weight:normal;">&nbsp;&nbsp; {$groups[f].name} ({$groups[f].count})</span>
										{/if}
									{else}
										<input type=checkbox name="recipient_group[{$smarty.section.f.index}]" value='{$groups[f].id}' {if $groups[f].sele eq 1}checked{/if}>
										{$groups[f].name} ({$groups[f].count})
									{/if}
								</p>
							{/section}
						</td>
					</tr>
					<tr>
						<td class="main_header_text" >
							More Users: (Email Addresses)<br />
							{if $form.mode eq 'view_promo'}
								<p style="margin-top:2px; font-weight:normal;">&nbsp;&nbsp; {$form.recipient_email}</p>
							{else}
								<textarea name="recipient_email" style="width:325px; height:100px;" >{$form.recipient_email}</textarea>
							{/if}
						</td>
					</tr>
					<tr id="ProcessRow" style="display:none;">
						<td class="main_header_text" >
							<div align='center' style="font-size:18px; color:#333399;padding:10px">
								<img src='../templates/admin/images/processing.gif' /> <br><br>Sending mails.. Please wait..
							</div>
						</td>
					</tr>
					<tr>
						<td align="center" style="padding:5px;">
							{if $form.mode neq 'view_promo'}
								<input type="submit" value="{$header.save_promo}" class="button" />
							{else}
								{if $form.status neq '1'}
									<input type="button" value="{$header.send_promo}" id="btnSend" class="button" onclick="SendPromo();" />
									&nbsp;
								{/if}
							{/if}
							<input type="button" value="{$header.cancel}" class="button" onclick="javascript: location.href='{$form.back}'" />
						</td>
					</tr>
				</table>
			</form>
		</td>
		<!-- /user select -->
		<!-- main user row -->
		<td width="650">
			<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
				<tr>
					<td bgcolor="#eeeeee">
						<div><img src="../promo_images/header_1.jpg" alt=""></div>
						<div id="promo_template_wrap">
							<table cellpadding="5" cellspacing="0">
								<tr>
									<td>
										<table cellpadding="0" cellspacing="0">
											<tr>
												<td valign="top" style="padding-right:25px;">
													<img src="../promo_images/nathamon.png" alt="Nathamon">
												</td>
												<td valign="top">
													<div>Hello <b>User Name</b>,</div>
													<div id="spn_body_text">{$form.body_text}</div>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td>
										<!--<img src="../promo_images/sample_user_match.jpg" alt="">-->
										<div class="promo_mail">
											<!-- begin results list -->
											{if !$promo_user}
												Users Profile Here..
											{else}
												{section name=f loop=$promo_user}
													<table cellpadding="0" cellspacing="0" width="100%" border="0">
														<tr>
															<td width="{$icon_width+15}" valign="top"><a href="{$promo_user[f].profile_link}" target="_blank"><img src="{$promo_user[f].icon_path}" class="icon" alt="{$promo_user[f].name}" ></a></td>
															<td valign="top" width="150">
																<div><a href="{$promo_user[f].profile_link}" target="_blank"><b>{$promo_user[f].name}</b></a></div>
																{assign var=str_id_city value=$promo_user[f].id_city}
																{assign var=str_id_region value=$promo_user[f].id_region}
																{assign var=str_id_country value=$promo_user[f].id_country}
																<div style="padding-top:0px;">{if $base_lang.city[$str_id_city]}{$base_lang.city[$str_id_city]}, {/if}{if $base_lang.region[$str_id_region]}{$base_lang.region[$str_id_region]}, {/if}{$base_lang.country[$str_id_country]}</div>
																<div style="padding-top:0px;"><font class="text_head">{$promo_user[f].age} {$lang.home_page.ans}</font></div>
																<div style="padding-top:0px; font-size:90%">{$lang.users.gender_search}&nbsp;{$promo_user[f].gender_search} {$lang.users.from} {$promo_user[f].age_min} {$lang.users.to} {$promo_user[f].age_max}</div>
															</td>
															<td valign="top" style="padding-left: 10px;">
																<div>{$promo_user[f].about_me}..</div>
																<div align="right"><a href="{$promo_user[f].profile_link}" target="_blank"><b>{$lang.users.read_more}</b></a></div>
															</td>
														</tr>
													</table>
												{/section}
											{/if}
											<!-- end results list -->
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div id="spn_footer_text">{$form.footer_text}</div>
										<div>Kind Regards,<br /> The <b>ThaiLadyDateFinder&#8482;</b> Team</div>
									</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>
			</table>
		<td>
	</tr>
</table>
{literal}
<script>
/*
function SavePromo()
{
	document.add_form.submit();
}
*/

function SendPromo()
{
	document.getElementById('ProcessRow').style.display ='block';
	document.add_form.submit();
	//document.getElementById('btnSend').disabled;
	
}
</script>
{/literal}
{include file="$admingentemplates/admin_bottom.tpl"}