{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple" id="registration_edit">
	<div class="upgrade-member tcxf-ch-la">
		<div>
			<div class="callchat_icons">
				<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/telephone.png" alt="Call Me" title="Call Me" /></a> 
				<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/Chat.png" alt="Chat With Me" title="Chat With Me" /></a>
			</div>
			{*
				<!-- OLD VIDEO INSTRUCTIONS -->
				<div>
					<div class="flag">
						<img src="{$site_root}{$template_root}/images/UnitedKingdomFlag.png" alt="" />
					</div>
					<div class="player">
						<script type="text/javascript">
						var playerhost = (("https:" == document.location.protocol) ? "https://meetmenowbangkok.s3.amazonaws.com/ezs3js/secure/" : "http://meetmenowbangkok.s3.amazonaws.com/ezs3js/player/");
						document.write(unescape("%3Cscript src='" + playerhost + "mp3/A0497661-F895-F372-0B5A2BCCD3A683F5.js' type='text/javascript'%3E%3C/script%3E"));
						</script>
					</div>
					<div class="clear"></div>
					<div class="flag"> 
						<img src="{$site_root}{$template_root}/images/ThailandFlag.png" alt="" align="left" />
					</div>
					<div class="player">
						<script type="text/javascript">
						var playerhost = (("https:" == document.location.protocol) ? "https://meetmenowbangkok.s3.amazonaws.com/ezs3js/secure/" : "http://meetmenowbangkok.s3.amazonaws.com/ezs3js/player/");
						document.write(unescape("%3Cscript src='" + playerhost + "mp3/52194CDD-095C-D045-115F3DCA1D505D5A.js' type='text/javascript'%3E%3C/script%3E"));
						</script>
					</div>
					<div class="clear"></div>
				</div>
			*}
		</div>
		<div>
			{*
				<div class="title2">{$form.header}</div><div class="step_info">Step One &raquo; Create My Account</div>
			*}
			<div class="space"></div>
			{if $form.num != 2}
				{* dont need this form on upload page *}
				<form name="profile" method="post" action="{$form.action}" onsubmit="return CheckForm(this);">
					{* signup gender *}
					<input type="hidden" name="signup" value="{$smarty.request.signup}" />
					{$form.hiddens}
			{/if}
			<div>
				<div class="det-14">
					<b>
					{if $form.num == 1}
						{$lang.subsection.registration_text}
					{elseif $form.num == 2}
						{$lang.subsection.upload_text}
					{elseif $form.num == 3}
						{$lang.subsection.description_text}
					{elseif $form.num == 4}
						{$lang.subsection.notice_text}
					{elseif $form.num == 5}
						{$lang.subsection.personal_text}
					{elseif $form.num == 6}
						{$lang.subsection.portreit_text}
					{elseif $form.num == 7}
						{$lang.subsection.interest_text}
					{elseif $form.num == 8}
						{$lang.subsection.criteria_text}
					{elseif $form.num == 9}
						{$lang.subsection.match_interest_text}
					{/if}
					</b>
				</div>
			</div>
			{if $form.err}
				<div class="error_msg">{$form.err}</div>
			{/if}
			<div class="inside-regis">
				{if $form.num == 1}
					{* there seems to be a problem with the javascript error checking. we disabled this *}
					{* USERNAME AND PASSWORD *}
					<div class="box_info">
						<p class="hdr2">
							<label title="My Username And Password">{$lang.profile_head.login_password}</label>
						</p>
						<div class="box_inn">
							<table border="0" cellpadding="0" cellspacing="0">
								{* username *}
								<tr>
									<td class="col1 label">
										<label title="{$lang.username_thai}">
											{if $err_field.login}<span class="error">{/if}
											{$lang.users.login}
											{if $err_field.login}</span>{/if}
											<span class="mandatory">*</span>:
										</label>
									</td>
									<td>
										<input type="text" name="login" id="login" maxlength="40" value="{$data.login}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" onblur="if (CheckValue(this)) CheckLogin('mp', this.value, error_div);" />
										<label title="{$lang.application.confidential}">
											{$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
										</label>
										{* error message for login already in use *}
										<div id="error_div" class="error"></div>
									</td>
								</tr>
								{* password *}
								<tr>
									<td class="col1 label">
										<label title="{$lang.password_thai}">
											{if $err_field.pass}<span class="error">{/if}
											{$lang.users.pass}
											{if $err_field.pass}</span>{/if}
											<span class="mandatory">*</span>:
										</label>
									</td>
									<td>
										<input type="password" name="pass" maxlength="20" value="{$data.pass}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" onblur="CheckValue(this);" />
									</td>
								</tr>
								{* confirm password *}
								<tr>
									<td class="col1 label">
										<label title="{$lang.confirm_password_thai}">
											{if $err_field.repass}<span class="error">{/if}
											{$lang.users.repass}
											{if $err_field.repass}</span>{/if}
											<span class="mandatory">*</span>:
										</label>
									</td>
									<td>
										<input type="password" name="repass" maxlength="20" value="{$data.repass}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" onblur="CheckValue(this);" />
									</td>
								</tr>
							</table>
						</div>
					</div>
					{* PERSONAL INFO *}
					<div class="box_info">
						<div class="hdr2">
							<label title="{$lang.personal_info_thai}">{$lang.profile_head.personal_info}</label>
						</div>
						<div class="clear"></div>
						<div class="box_inn">
							<table border="0" cellpadding="0" cellspacing="0">
								{* first name *}
								{if $use_field.fname & SB_REGISTRATION}
									<tr>
										<td class="col1 label">
											<label title="{$lang.first_name_thai}">
												{if $err_field.fname}<span class="error">{/if}
												{$lang.users.fname}
												{if $err_field.fname}</span>{/if}
												{if $mandatory.fname & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
											</label>
										</td>
										<td>
											<input type="text" name="fname" maxlength="25" value="{$data.fname}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" onblur="CheckValue(this);" />
											<label title="{$lang.application.fname_public}">
												{$lang.public} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
											</label>
										</td>
									</tr>
								{/if}
								{* last name *}
								{if $use_field.sname & SB_REGISTRATION}
									<tr>
										<td class="col1 label">
											<label title="{$lang.last_name_thai}">
												{if $err_field.sname}<span class="error">{/if}
												{$lang.users.sname}
												{if $err_field.sname}</span>{/if}
												{if $mandatory.sname & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
											</label>
										</td>
										<td>
											<input type="text" name="sname" maxlength="25" value="{$data.sname}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" onblur="CheckValue(this);" />
											<label title="{$lang.application.confidential}">
												{$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
											</label>
										</td>
									</tr>
								{/if}
								{* added: nick name *}
								{if $use_field.mm_nickname & SB_REGISTRATION}
									<tr>
										<td class="col1 label">
											<label title="{$lang.nickname_thai}">
												{if $err_field.mm_nickname}<span class="error">{/if}
												{$lang.users.mm_nickname}
												{if $err_field.mm_nickname}</span>{/if}
												{if $mandatory.mm_nickname & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
											</label>
										</td>
										<td>
											<input type="text" name="mm_nickname" maxlength="25" value="{$data.mm_nickname}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
										</td>
									</tr>
								{/if}
								{* gender *}
								{if $use_field.gender & SB_REGISTRATION}
									<tr>
										<td class="col1 label" valign="top" style="padding-top:5px;">
											<label title="{$lang.gender_thai}">
												{if $err_field.gender}<span class="error">{/if}
												{$lang.users.gender}
												{if $err_field.gender}</span>{/if}
												{if $mandatory.gender & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
											</label>
										</td>
										<td>
											{foreach item=item from=$gender}
												<input type="radio" name="gender" value="{$item.id}" onclick="CheckGender(this)" {if $item.sel}checked="checked"{/if} />
												<span style="padding-right:0px;">
													<label title="{$lang.mm_gender[$item.id]}">{$item.name}</label>
												</span>
											{/foreach}
											<div class="txtred" style="padding-left:10px;font-size:12px">
												Please make sure that you are choosing a right Gender. You are not able to change this after Registration.
											</div>
										</td>
									</tr>
								{/if}
								{* couple *}
								{if $use_field.couple & SB_REGISTRATION}
									<tr>
										<td class="col1 label">
											{if $err_field.couple}<span class="error">{/if}
											{$lang.users.single_couple}
											{if $err_field.couple}</span>{/if}
											{if $mandatory.couple & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
										</td>
										<td>
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td>
														<input type="radio" onclick=showcouple(); name="couple" value="0" {if ! $data.couple}checked="checked"{/if} />
													</td>
													<td style="padding-right:15px;" class="txtblack">
														{$lang.users.single}
													</td>
													<td>
														<input type="radio" onclick=showcouple(); name="couple" value="1" {if $data.couple}checked="checked"{/if} />
													</td>
													<td style="padding-right:15px;" class="txtblack">
														{$lang.users.couple}
													</td>
													<td>
														<div id="couple_user_form" style="display:none; position:relative; top:0px">
															{if $data.couple_user}
																<input type="hidden" value="{$data.couple_user}" name="couple_user" />
																{$lang.users.couple_link}:<br>
																<a href="{$data.couple_link}" target="_blank"><b>{$data.couple_login}</b></a>
																{$data.couple_gender} {$data.couple_age} {$lang.home_page.ans}<br>
																<input type="checkbox" value="1" name="couple_delete" />
																{$lang.users.couple_delete}<br>
																{if ! $data.couple_accept}{$lang.users.couple_accept}{/if}
															{else}
																<table cellspacing="0" cellpadding="0" border="0">
																	<tr>
																		<td class="txtblack">{$lang.users.couple_login}:</td>
																		<td>
																			<input type="text" name="couple_login" maxlength="25" value="{$data.couple_login}" style="width: 150px" />
																		</td>
																		<td>
																			<a href="quick_search.php">{$lang.button.search}</a>
																		</td>
																	</tr>
																</table>
															{/if}
														</div>
													</td>
												</tr>
											</table>
										</td>
									</tr>
								{/if}
								{* added: marital status *}
								{if $use_field.mm_marital_status & SB_REGISTRATION}
									<tr>
										<td class="col1 label">
											<label title="{$lang.marital_status_thai}">
												{if $err_field.mm_marital_status}<span class="error">{/if}
												{$lang.users.mm_marital_status}
												{if $err_field.mm_marital_status}</span>{/if}
												{if $mandatory.mm_marital_status & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
											</label>
										</td>
										<td>
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													{foreach item=item from=$mm_marital_status}
														<td>
															<input type="radio" name="mm_marital_status" id="marital_status_{$item.id}" value="{$item.id}" {if $item.sel}checked="checked"{/if} />
														</td>
														<td style="padding-right:15px;" class="txtblack">
															<label for="marital_status_{$item.id}" title="{$lang.mm_marital_status[$item.id]}">{$item.value}</label>
														</td>
													{/foreach}
												</tr>
											</table>
										</td>
									</tr>
								{/if}
								{* date of birth *}
								{if $use_field.date_birthday & SB_REGISTRATION}
									<tr>
										<td class="col1 label">
											<label title="{$lang.birthday_thai}">
												{if $err_field.date_birthday}<span class="error">{/if}
												{$lang.users.date_birthday}
												{if $err_field.date_birthday}</span>{/if}
												{if $mandatory.date_birthday & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
											</label>
										</td>
										<td>
											<select name="b_{$date_part1_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:auto !important; margin-right:5px;">
												<option value="">{$date_part1_default}</option>
												{foreach item=item from=$date_part1}
													<option value="{$item.value}" {if $item.sel}selected="selected"{/if}>{$item.name}</option>
												{/foreach}
											</select>
											<select name="b_{$date_part2_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:auto !important; margin-right:5px;">
												<option value="">{$date_part2_default}</option>
												{foreach item=item from=$date_part2}
													<option value="{$item.value}" {if $item.sel}selected="selected"{/if}>{$item.name}</option>
												{/foreach}
											</select>
											<select name="b_{$date_part3_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:auto !important;">
												<option value="">{$date_part3_default}</option>
												{foreach item=item from=$date_part3}
													<option value="{$item.value}" {if $item.sel}selected="selected"{/if}>{$item.name}</option>
												{/foreach}
											</select>
										</td>
									</tr>
								{/if}
								{* added: place of birth *}
								{if $use_field.mm_place_of_birth & SB_REGISTRATION}
									<tr>
										<td class="col1 label">
											<label title="{$lang.place_of_birth_thai}">
												{if $err_field.mm_place_of_birth}<span class="error">{/if}
												{$lang.users.mm_place_of_birth}
												{if $err_field.mm_place_of_birth}</span>{/if}
												{if $mandatory.mm_place_of_birth & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
											</label>
										</td>
										<td><input type="text" name="mm_place_of_birth" maxlength="25" value="{$data.mm_place_of_birth}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" /></td>
									</tr>
								{/if}
								{* nationality *}
								{if $use_field.id_nationality & SB_REGISTRATION}
									<tr>
										<td class="col1 label">
											<label title="{$lang.nationality_thai}">
												{if $err_field.id_nationality}<span class="error">{/if}
												{$lang.users.nationality}
												{if $err_field.id_nationality}</span>{/if}
												{if $mandatory.id_nationality & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
											</label>
										</td>
										<td>
											<select name="id_nationality" {if $data.root == 1}disabled="disabled"{/if} style="width:200px">
												<option value="0">{$lang.home_page.select_default}</option>
												{foreach item=item from=$nation}
													<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
												{/foreach}
											</select>
										</td>
									</tr>
								{/if}
								{* added: identification number *}
								{if ($use_field.mm_id_number & SB_REGISTRATION) && $data.gender == 2}
									<tr>
										<td class="col1 label">
											<label title="ID Number">
												{if $err_field.mm_id_number}<span class="error">{/if}
												{$lang.users.mm_id_number}
												{if $err_field.mm_id_number}</span>{/if}
												{if $mandatory.mm_id_number & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
											</label>
										</td>
										<td>
											<input type="text" name="mm_id_number" maxlength="25" value="{$data.mm_id_number}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
											<label title="{$lang.application.confidential}">
												{$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
											</label>
										</td>
									</tr>
								{else}
									<tr>
										<td colspan="2">
											<input type="hidden" name="mm_id_number" value="{$data.mm_id_number}" />
										</td>
									</tr>
								{/if}
							</table>
						</div>
					</div>
					{* CONTACT INFO *}
					<div class="box_info">
						<div class="hdr2">
							<label title="{$lang.contact_info_thai}">{$lang.profile_head.contact_info}</label>
						</div>
						<div class="clear"></div>
						<div class="box_inn">
							<table border="0" cellpadding="0" cellspacing="0">
								{* email *}
								{if $use_field.email & SB_REGISTRATION}
									<tr>
										<td class="col1 label">
											<label title="{$lang.email_thai}">
												{if $err_field.email}<span class="error">{/if}
												{$lang.users.email}
												{if $err_field.email}</span>{/if}
												{if $mandatory.email & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
											</label>
										</td>
										<td>
											<input type="text" name="email" maxlength="50" value="{$data.email}" {if $data.root == 1}disabled="disabled"{/if} style="width:250px" onblur="CheckValue(this);" oncopy="return false" />
											<label title="{$lang.application.confidential}">
												{$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
											</label>
										</td>
									</tr>
								{/if}
								{* confirm email *}
								<tr>
									<td class="col1 label">
										<label title="{$lang.confirm_email_thai}">
											{if $err_field.reemail}<span class="error">{/if}
											{$lang.users.reemail}
											{if $err_field.reemail}</span>{/if}
											<span class="mandatory">*</span>:
										</label>
									</td>
									<td>
										<input type="text" name="reemail" maxlength="40" value="{$data.reemail}" {if $data.root == 1}disabled="disabled"{/if} style="width:250px" onblur="CheckValue(this);" oncopy="return false" ondrag="return false" ondrop="return false" onpaste="return false" autocomplete="off" />
										<label title="{$lang.application.confidential}">
											{$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
										</label>
									</td>
								</tr>
								{* added: contact phone number *}
								{if $use_field.mm_contact_phone_number & SB_REGISTRATION}
									<tr>
										<td class="col1 label">
											<label title="{$lang.contact_phone_thai}">
												{if $err_field.mm_contact_phone_number}<span class="error">{/if}
												{$lang.users.mm_contact_phone_number}
												{if $err_field.mm_contact_phone_number}</span>{/if}
												{if $mandatory.mm_contact_phone_number & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
											</label>
										</td>
										<td>
											<input type="text" name="mm_contact_phone_number" maxlength="30" value="{$data.mm_contact_phone_number}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
											<label title="{$lang.application.confidential}">
												{$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
											</label>
										</td>
									</tr>
								{/if}
								{* added: contact mobile number *}
								{if $use_field.mm_contact_mobile_number & SB_REGISTRATION}
									<tr>
										<td class="col1 label">
											<label title="{$lang.contact_mobile_thai}">
												{if $err_field.mm_contact_mobile_number}<span class="error">{/if}
												{$lang.users.mm_contact_mobile_number}
												{if $err_field.mm_contact_mobile_number}</span>{/if}
												{if $mandatory.mm_contact_mobile_number & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
											</label>
										</td>
										<td>
											<input type="text" name="mm_contact_mobile_number" maxlength="30" value="{$data.mm_contact_mobile_number}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
											<label title="{$lang.application.confidential}">
												{$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
											</label>
										</td>
									</tr>
								{/if}
								{* voip special *}
								{if $voipcall_feature == 1}
									{if $use_field.phone & SB_REGISTRATION}
										<tr>
											<td class="col1 label">
												{$lang.users.phone}:
											</td>
											<td>
												<input type="text" name="phone" maxlength="25" value="{$data.phone}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" onblur="CheckValue(this);" />
											</td>
										</tr>
										<tr>
											<td colspan="2">{$lang.users.phone_notice}</td>
										</tr>
									{/if}
								{/if}
							</table>
						</div>
					</div>
					{* LOOKING FOR *}
					{if ($use_field.gender_search & SB_REGISTRATION)
					|| ($use_field.age_min & SB_REGISTRATION)
					|| ($use_field.age_max & SB_REGISTRATION)
					|| ($use_field.couple_search & SB_REGISTRATION)
					|| ($use_field.relationship & SB_REGISTRATION)}
						<div class="box_info">
							<div class="hdr2">
								{if $data.gender == 2}
									<label title="{$lang.looking_for_man_thai}">{$lang.profile_head.looking_for_man}</label>
								{else}
									<label title="I'M Looking For">{$lang.profile_head.looking_for_woman}</label>
								{/if}
							</div>
							<div class="clear"></div>
							<div class="box_inn">
								<table border="0" cellpadding="0" cellspacing="0">
									{* search gender *}
									{if $use_field.gender_search & SB_REGISTRATION}
										<tr>
											<td class="col1 label">
												{if $err_field.gender_search}<span class="error">{/if}
												{$lang.users.gender}
												{if $err_field.gender_search}</span>{/if}:
											</td>
											<td>
												{foreach item=item from=$gender}
													<input type="radio" name="gender_search" value="{$item.id}" {if $item.sel_search}checked="checked"{/if} />
													<span style="padding-right:15px;" class="txtblack">{$item.name_search}</span>
												{/foreach}
											</td>
										</tr>
									{/if}
									{* search age range *}
									{if $use_field.age_min & SB_REGISTRATION || $use_field.age_max & SB_REGISTRATION}
										<tr>
											<td class="col1 label">
												<label title="{$lang.age_range_thai}">
												{if $err_field.age_min || $err_field.age_max}<span class="error">{/if}
												{$lang.users.age_range}
												{if $err_field.age_min || $err_field.age_max}</span>{/if}:
												</label>
											</td>
											<td>
												{if $use_field.age_min & SB_REGISTRATION}
													<span class="txtblack">{$lang.users.from_big}</span>
													<span style="padding-left:10px;">
														<select name="age_min" {if $data.root == 1}disabled="disabled"{/if}>
															{foreach item=item from=$age_min}
															<option value="{$item}" {if $min_age_sel == $item}selected="selected"{/if}>{$item}</option>
															{/foreach}
														</select>
													</span>
												{/if}
												{if $use_field.age_max & SB_REGISTRATION}
													<span class="txtblack" style="padding-left:10px;">{$lang.users.to_big}</span>
													<span style="padding-left:10px;">
														<select name="age_max" {if $data.root == 1}disabled="disabled"{/if}>
															{foreach item=item from=$age_max}
															<option value="{$item}" {if $max_age_sel == $item}selected="selected"{/if}>{$item}</option>
															{/foreach}
														</select>
													</span>
												{/if}
											</td>
										</tr>
									{/if}
									{* search single / couple *}
									{if $use_field.couple_search & SB_REGISTRATION}
										<tr>
											<td class="col1 label">
												{if $err_field.couple_search}<span class="error">{/if}
												{$lang.users.single_couple}
												{if $err_field.couple_search}</span>{/if}:
											</td>
											<td>
												<table border="0" cellpadding="0" cellspacing="0">
													<tr>
														<td>
															<input type="radio" name="couple_search" value="0" {if !$data.couple_search}checked="checked"{/if} />
														</td>
														<td class="txtblack" style="padding-right:15px;">
															{$lang.users.single}
														</td>
														<td>
															<input type="radio" name="couple_search" value="1" {if $data.couple_search}checked="checked"{/if} />
														</td>
														<td class="txtblack" style="padding-right:15px;">
															{$lang.users.couple}
														</td>
													</tr>
												</table>
											</td>
										</tr>
									{/if}
									{* relationship *}
									{if $use_field.id_relationship & SB_REGISTRATION}
										<tr>
											<td class="col1 label" valign="top">
												{if $err_field.id_relationship}<span class="error">{/if}
												{$lang.users.relationship}
												{if $err_field.id_relationship}</span>{/if}:
											</td>
											<td class="txtblack">
												{if $relation_input_type == "select"}
													<select name="relation[]" {if $data.root == 1}disabled="disabled"{/if} multiple style="width:150px; height:80px">
														<option value="0" {if $relation.sel_all}selected="selected"{/if}>{$button.all}</option>
														{html_options values=$relation.opt_value selected=$relation.opt_sel output=$relation.opt_name}
													</select>
												{else}
													<table cellpadding="0" cellspacing="0" border="0">
														<tr>
															<td>
																<input type="checkbox" name="relation[]" value="0" id="all" {if $relation.sel_all}checked="checked"{/if} />
															</td>
															<td class="txtblack">
																<label for="all">{$button.all}</label>
															</td>
														</tr>
														{section name=r loop=$relation.opt_value}
															{if $smarty.section.r.index is div by 5 && !$smarty.section.r.last}
																<tr>
															{/if}
															<td>
																<input type="checkbox" id="relation_{$smarty.section.r.index}" name="relation[]" value="{$relation.opt_value[r]}" {if $relation.opt_sel[r] == $relation.opt_value[r]}checked="checked"{/if} />
															</td>
															<td class="txtblack">
																<label for="relation_{$smarty.section.r.index}" style="margin-bottom:5px;">{$relation.opt_name[r]}</label>
															</td>
															{if $smarty.section.r.index_next is div by 5 || $smarty.section.r.last}
																</tr>
															{/if}
														{/section}
													</table>
												{/if}
											</td>
										</tr>
									{/if}
								</table>
							</div>
						</div>
					{/if}
					{* ADDRESS INFO *}
					{if $use_field.id_country & SB_REGISTRATION}
						<div class="box_info">
							<div class="hdr2">
								<label title="{$lang.address_info_thai}">{$lang.profile_head.address_info}</label>
								{* <span class="text_hidden">{$lang.registration.country_text}</span> *}
							</div>
							<div class="clear"></div>
							<div class="box_inn">
								<table border="0" cellpadding="0" cellspacing="0">
									{* country *}
									{if $use_field.id_country & SB_REGISTRATION}
										<tr>
											<td class="col1 label">
												<label title="{$lang.country_thai}">
													{if $err_field.id_country}<span class="error">{/if}
													{$lang.users.country}
													{if $err_field.id_country}</span>{/if}
													{if $mandatory.id_country & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
												</label>
											</td>
											<td>
												<select name="id_country" {if $data.root == 1}disabled="disabled"{/if} onchange="SelectRegion('rp', this.value, document.getElementById('region_div'), document.getElementById('city_div'));">
													<option value="0">{$lang.home_page.select_default}</option>
													{foreach item=item from=$country}
													<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
													{/foreach}
												</select>
											</td>
										</tr>
									{/if}
									{* region *}
									{if $use_field.id_region & SB_REGISTRATION}
										<tr>
											<td class="col1 label">
												{if $err_field.id_region}<span class="error">{/if}
												{$lang.users.region}
												{if $err_field.id_region}</span>{/if}
												{if $mandatory.id_region & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
											</td>
											<td>
												<div id="region_div">
													{if isset($region)}
														<select name="id_region" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" onchange="SelectCity('rp', this.value, document.getElementById('city_div'));">
															<option value="0">{$lang.home_page.select_default}</option>
															{foreach item=item from=$region}
															<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
															{/foreach}
														</select>
													{else}
													{/if}
												</div>
											</td>
										</tr>
									{/if}
									{* city *}
									{if $use_field.id_city & SB_REGISTRATION}
										<tr>
											<td class="col1 label">
												<label title="{$lang.city}">
													{if $err_field.id_city}<span class="error">{/if}
													{$lang.users.city}
													{if $err_field.id_city}</span>{/if}
													{if $mandatory.id_city & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
												</label>
											</td>
											<td>
												<div id="city_div">
													{if isset($city)}
														<select name="id_city" {if $data.root == 1}disabled="disabled"{/if} style="width: 150px">
															<option value="0">{$lang.home_page.select_default}</option>
															{foreach item=item from=$city}
															<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
															{/foreach}
														</select>
													{else}
													{/if}
												</div>
											</td>
										</tr>
									{/if}
									{* added: city *}
									{if $use_field.mm_city & SB_REGISTRATION}
										<tr>
											<td class="col1 label">
												<label title="{$lang.city}">
													{if $err_field.mm_city}<span class="error">{/if}
													{$lang.users.city}
													{if $err_field.mm_city}</span>{/if}
													{if $mandatory.mm_city & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
												</label>
											</td>
											<td>
												<input type="text" name="mm_city" maxlength="25" value="{$data.mm_city}" size="30" {if $data.root == 1}disabled="disabled"{/if} style="width: 150px" />
											</td>
										</tr>
									{/if}
									{* zipcode *}
									{if $use_field.zipcode & SB_REGISTRATION}
										<tr>
											<td class="col1 label">
												<label title="{$lang.zipcode_thai}">
													{if $err_field.zipcode}<span class="error">{/if}
													{$lang.users.zipcode}
													{if $err_field.zipcode}</span>{/if}
													{if $mandatory.zipcode & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
												</label>
											</td>
											<td>
												<input type="text" name="zipcode" maxlength="25" value="{$data.zipcode}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" maxlength="{$form.zip_count}" />
												<label title="{$lang.application.confidential}">
													{$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
												</label>
												{* <span class="text_hidden">{$lang.users.us_only}</span> *}
											</td>
										</tr>
									{/if}
									{* added: address line 1 *}
									{if $use_field.mm_address_1 & SB_REGISTRATION}
										<tr>
											<td class="col1 label">
												<label title="{$lang.address_line_1_thai}">
													{if $err_field.mm_address_1}<span class="error">{/if}
													{$lang.users.mm_address_1}
													{if $err_field.mm_address_1}</span>{/if}
													{if $mandatory.mm_address_1 & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
												</label>
											</td>
											<td>
												<input type="text" name="mm_address_1" maxlength="40" value="{$data.mm_address_1}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
												<label title="{$lang.application.confidential}">
													{$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
												</label>
											</td>
										</tr>
									{/if}
									{* added: address line 2 *}
									{if $use_field.mm_address_2 & SB_REGISTRATION}
										<tr>
											<td class="col1 label">
												<label title="{$lang.address_line_2_thai}">
													{if $err_field.mm_address_2}<span class="error">{/if}
													{$lang.users.mm_address_2}
													{if $err_field.mm_address_2}</span>{/if}
													{if $mandatory.mm_address_2 & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
												</label>
											</td>
											<td>
												<input type="text" name="mm_address_2" maxlength="40" value="{$data.mm_address_2}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
												<label title="{$lang.application.confidential}">
													{$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
												</label>
											</td>
										</tr>
									{/if}
								</table>
							</div>
						</div>
					{/if}
					{* LANGUAGE INFO *}
					{if $use_field.id_language_1 & SB_REGISTRATION}
						<div class="box_info">
							<div class="hdr2">
								<label title="{$lang.language_info_thai}">{$lang.profile_head.language_info}</label>
							</div>
							<div class="clear"></div>
							<div class="box_inn">
								<table border="0" cellpadding="0" cellspacing="0">
									{* language *}
									{if $use_field.id_language_1 & SB_REGISTRATION}
										<tr>
											<td class="col1 label">
												<label title="{$lang.language_thai}">
													{if $err_field.id_language_1 || $err_field.id_language_2 || $err_field.id_language_3}<span class="error">{/if}
													{$lang.users.language}
													{if $err_field.id_language_1 || $err_field.id_language_2 || $err_field.id_language_3}</span>{/if}
													{if $mandatory.id_language_1 & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
												</label>
											</td>
											<td>
												<select name="id_language_1" {if $data.root == 1}disabled="disabled"{/if} style="width:160px; margin-right:5px;">
													<option value="0">{$lang.home_page.select_default}</option>
													{foreach item=item from=$lang_sel}
													<option value="{$item.id}" {if $item.sel1}selected="selected"{/if}>{$item.value}</option>
													{/foreach}
												</select>
												<select name="id_language_2" {if $data.root == 1}disabled="disabled"{/if} style="width:160px; margin-right:5px;">
													<option value="0">{$lang.home_page.select_default}</option>
													{foreach item=item from=$lang_sel}
													<option value="{$item.id}" {if $item.sel2}selected="selected"{/if}>{$item.value}</option>
													{/foreach}
												</select>
												<select name="id_language_3" {if $data.root == 1}disabled="disabled"{/if} style="width:160px; margin-right:5px;">
													<option value="0">{$lang.home_page.select_default}</option>
													{foreach item=item from=$lang_sel}
													<option value="{$item.id}" {if $item.sel3}selected="selected"{/if}>{$item.value}</option>
													{/foreach}
												</select>
											</td>
										</tr>
									{/if}
									{* added: level of english *}
									{if $use_field.mm_level_of_english & SB_REGISTRATION}
										<tr>
											<td class="col1 label">
												<label title="{$lang.level_of_english_thai}">
												{if $err_field.mm_level_of_english}<span class="error">{/if}
												{$lang.users.mm_level_of_english}
												{if $err_field.mm_level_of_english}</span>{/if}
												{if $mandatory.mm_level_of_english & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
												</label>
											</td>
											<td>
												{foreach item=item from=$mm_level_of_english}
													<input type="radio" name="mm_level_of_english" id="level_english_{$item.id}" value="{$item.id}" {if $item.sel}checked="checked"{/if} style="vertical-align: top;" />
													<span style="margin-right:15px;" class="txtblack">
														<label for="level_english_{$item.id}" title="{$lang.mm_level_english[$item.value]}">{$item.value}</label>
													</span>
												{/foreach}
											</td>
										</tr>
									{/if}
									{* site language *}
									{if $use_field.site_language & SB_REGISTRATION}
										<tr>
											<td class="col1 label">
												{if $err_field.site_language}<span class="error">{/if}
												{$lang.users.site_language}
												{if $err_field.site_language}</span>{/if}
												{if $mandatory.site_language & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
											</td>
											<td>
												<select name="site_language" style="width:200px">
													{foreach from=$site_langs item=item}
													<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.name}</option>
													{/foreach}
												</select>
											</td>
										</tr>
									{else}
										<tr>
											<td colspan="2" style="padding: 0px;">
												<input type="hidden" name="site_language" value="{$data.site_language}">
											</td>
										</tr>
									{/if}
								</table>
							</div>
						</div>
					{/if}
					{* EMPLOYMENT INFO *}
					{if $use_field.mm_employment_status & SB_REGISTRATION}
						<div class="box_info">
							<div class="hdr2">
								<label title="{$lang.employment_info_thai}">{$lang.profile_head.employment_info}</label>
							</div>
							<div class="clear"></div>
							<div class="box_inn">
								<table border="0" cellpadding="0" cellspacing="0">
									{* added: employment status *}
									{if $use_field.mm_employment_status & SB_REGISTRATION}
										<tr>
											<td class="col1 label">
												<label title="Employment Status">
													{if $err_field.mm_employment_status || $err_field.mm_business_name || $err_field.mm_employer_name}<span class="error">{/if}
													{$lang.users.mm_employment_status}
													{if $err_field.mm_employment_status || $err_field.mm_business_name || $err_field.mm_employer_name}</span>{/if}
													{if $mandatory.mm_employment_status & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
												</label>
											</td>
											<td>
												<table border="0" cellpadding="0" cellspacing="0">
													{foreach item=item from=$mm_employment_status}
														<tr>
															<td>
																<input type="radio" name="mm_employment_status" value="{$item.id}" {if $item.sel}checked="checked"{/if} />
															</td>
															<td style="padding-right:15px;" class="txtblack">
																<label title="{$lang.mm_employment_status[$item.value]}">{$item.value}</label>
															</td>
															{if $item.id == 1}
																<td></td>
																<td></td>
															{elseif $item.id == 2}
																{if $use_field.mm_business_name & SB_REGISTRATION}
																	<td class="col1 label">
																		<label title="{$lang.business_name_thai}">
																			{if $err_field.mm_business_name}<span class="error">{/if}
																			{$lang.users.mm_business_name}
																			{if $err_field.mm_business_name}</span>{/if}:
																		</label>
																	</td>
																	<td>
																		<input type="text" name="mm_business_name" maxlength="40" value="{$data.mm_business_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
																	</td>
																{else}
																	<td></td>
																	<td></td>
																{/if}
															{elseif $item.id == 3}
																{if $use_field.mm_employer_name & SB_REGISTRATION}
																	<td class="col1 label">
																		<label title="{$lang.employer_name_thai}">
																			{if $err_field.mm_employer_name}<span class="error">{/if}
																			{$lang.users.mm_employer_name}
																			{if $err_field.mm_employer_name}</span>{/if}:
																		</label>
																	</td>
																	<td>
																		<input type="text" name="mm_employer_name" maxlength="25" value="{$data.mm_employer_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
																	</td>
																{else}
																	<td></td>
																	<td></td>
																{/if}
															{/if}
														</tr>
													{/foreach}
												</table>
											</td>
										</tr>
									{/if}
									{* added: job position *}
									{if $use_field.mm_job_position & SB_REGISTRATION}
										<tr>
											<td class="col1 label">
												<label title="{$lang.job_position_thai}">
													{if $err_field.mm_job_position}<span class="error">{/if}
													{$lang.users.mm_job_position}
													{if $err_field.mm_job_position}</span>{/if}
													{if $mandatory.mm_job_position & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
												</label>
											</td>
											<td>
												<input type="text" name="mm_job_position" maxlength="25" value="{$data.mm_job_position}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
											</td>
										</tr>
									{/if}
									{* added: work address *}
									{if $use_field.mm_work_address & SB_REGISTRATION}
										<tr>
											<td class="col1 label">
												<label title="{$lang.work_address_thai}">
													{if $err_field.mm_work_address}<span class="error">{/if}
													{$lang.users.mm_work_address}
													{if $err_field.mm_work_address}</span>{/if}
													{if $mandatory.mm_work_address & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
												</label>
											</td>
											<td>
												<input type="text" name="mm_work_address" maxlength="40" value="{$data.mm_work_address}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
												<label title="{$lang.application.confidential}">
													{$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
												</label>
											</td>
										</tr>
									{/if}
									{* added: work phone number *}
									{if $use_field.mm_work_phone_number & SB_REGISTRATION}
										<tr>
											<td class="col1 label">
												<label title="{$lang.work_phone_thai}">
													{if $err_field.mm_work_phone_number}<span class="error">{/if}
													{$lang.users.mm_work_phone_number}
													{if $err_field.mm_work_phone_number}</span>{/if}
													{if $mandatory.mm_work_phone_number & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
												</label>
											</td>
											<td>
												<input type="text" name="mm_work_phone_number" maxlength="25" value="{$data.mm_work_phone_number}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
												<label title="{$lang.application.confidential}">
													{$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
												</label>
											</td>
										</tr>
									{/if}
								</table>
							</div>
						</div>
					{/if}
					{* REFERENCE 1 *}
					{if $use_field.mm_ref_1_first_name & SB_REGISTRATION}
						<div class="box_info">
							<div class="hdr2">
								<label title="{$lang.reference_1_thai}">{$lang.profile_head.reference_1}</label>
							</div>
							<div class="clear"></div>
							<div class="box_inn">
								<table border="0" cellpadding="0" cellspacing="0">
									{* added: ref 1 first name *}
									{if $use_field.mm_ref_1_first_name & SB_REGISTRATION}
										<tr>
											<td class="col1 label">
												<label title="{$lang.first_name_thai}">
													{if $err_field.mm_ref_1_first_name}<span class="error">{/if}
													{$lang.users.fname}
													{if $err_field.mm_ref_1_first_name}</span>{/if}
													{if $mandatory.mm_ref_1_first_name & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
												</label>
											</td>
											<td>
												<input type="text" name="mm_ref_1_first_name" maxlength="25" value="{$data.mm_ref_1_first_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
												<label title="{$lang.application.confidential}">
													{$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
												</label>
											</td>
										</tr>
									{/if}
									{* added: ref 1 last name *}
									{if $use_field.mm_ref_1_last_name & SB_REGISTRATION}
										<tr>
											<td class="col1 label">
												<label title="{$lang.last_name_thai}">
													{if $err_field.mm_ref_1_last_name}<span class="error">{/if}
													{$lang.users.sname}
													{if $err_field.mm_ref_1_last_name}</span>{/if}
													{if $mandatory.mm_ref_1_last_name & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
												</label>
											</td>
											<td>
												<input type="text" name="mm_ref_1_last_name" maxlength="25" value="{$data.mm_ref_1_last_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
												<label title="{$lang.application.confidential}">
													{$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
												</label>
											</td>
										</tr>
									{/if}
									{* added: ref 1 relationship *}
									{if $use_field.mm_ref_1_relationship & SB_REGISTRATION}
										<tr>
											<td class="col1 label">
												<label title="{$lang.reference_relationship_thai}">
													{if $err_field.mm_ref_1_relationship}<span class="error">{/if}
													{$lang.users.mm_reference_relationship}
													{if $err_field.mm_ref_1_relationship}</span>{/if}
													{if $mandatory.mm_ref_1_relationship & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
												</label>
											</td>
											<td>
												<input type="text" name="mm_ref_1_relationship" maxlength="25" value="{$data.mm_ref_1_relationship}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
												<label title="{$lang.application.confidential}">
													{$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
												</label>
											</td>
										</tr>
									{/if}
									{* added: ref 1 phone number *}
									{if $use_field.mm_ref_1_phone_number & SB_REGISTRATION}
										<tr>
											<td class="col1 label">
												<label title="{$lang.reference_phone_thai}">
													{if $err_field.mm_ref_1_phone_number}<span class="error">{/if}
													{$lang.users.mm_reference_phone_number}
													{if $err_field.mm_ref_1_phone_number}</span>{/if}
													{if $mandatory.mm_ref_1_phone_number & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
												</label>
											</td>
											<td>
												<input type="text" name="mm_ref_1_phone_number" maxlength="25" value="{$data.mm_ref_1_phone_number}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
												<label title="{$lang.application.confidential}">
													{$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
												</label>
											</td>
										</tr>
									{/if}
								</table>
							</div>
						</div>
					{/if}
					{* REFERENCE 2 *}
					{if $use_field.mm_ref_1_first_name & SB_REGISTRATION}
						<div class="box_info">
							<div class="hdr2">
								<label title="{$lang.reference_2_thai}">{$lang.profile_head.reference_2}</label>
							</div>
							<div class="clear"></div>
							<div class="box_inn">
								<table border="0" cellpadding="0" cellspacing="0">
									{* added: ref 2 first name *}
									{if $use_field.mm_ref_2_first_name & SB_REGISTRATION}
										<tr>
											<td class="col1 label">
												<label title="{$lang.first_name_thai}">
													{if $err_field.mm_ref_2_first_name}<span class="error">{/if}
													{$lang.users.fname}
													{if $err_field.mm_ref_2_first_name}</span>{/if}
													{if $mandatory.mm_ref_2_first_name & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
												</label>
											</td>
											<td>
												<input type="text" name="mm_ref_2_first_name" maxlength="25" value="{$data.mm_ref_2_first_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
												<label title="{$lang.application.confidential}">
													{$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
												</label>
											</td>
										</tr>
									{/if}
									{* added: ref 2 last name *}
									{if $use_field.mm_ref_2_last_name & SB_REGISTRATION}
										<tr>
											<td class="col1 label">
												<label title="{$lang.last_name_thai}">
													{if $err_field.mm_ref_2_last_name}<span class="error">{/if}
													{$lang.users.sname}
													{if $err_field.mm_ref_2_last_name}</span>{/if}
													{if $mandatory.mm_ref_2_last_name & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
												</label>
											</td>
											<td>
												<input type="text" name="mm_ref_2_last_name" maxlength="25" value="{$data.mm_ref_2_last_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
												<label title="{$lang.application.confidential}">
													{$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
												</label>
											</td>
										</tr>
									{/if}
									{* added: ref 2 relationship *}
									{if $use_field.mm_ref_2_relationship & SB_REGISTRATION}
										<tr>
											<td class="col1 label">
												<label title="{$lang.reference_relationship_thai}">
													{if $err_field.mm_ref_2_relationship}<span class="error">{/if}
													{$lang.users.mm_reference_relationship}
													{if $err_field.mm_ref_2_relationship}</span>{/if}
													{if $mandatory.mm_ref_2_relationship & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
												</label>
											</td>
											<td>
												<input type="text" name="mm_ref_2_relationship" maxlength="25" value="{$data.mm_ref_2_relationship}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
												<label title="{$lang.application.confidential}">
													{$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
												</label>
											</td>
										</tr>
									{/if}
									{* added: ref 2 phone number *}
									{if $use_field.mm_ref_2_phone_number & SB_REGISTRATION}
										<tr>
											<td class="col1 label">
												<label title="{$lang.reference_phone_thai}">
													{if $err_field.mm_ref_2_phone_number}<span class="error">{/if}
													{$lang.users.mm_reference_phone_number}
													{if $err_field.mm_ref_2_phone_number}</span>{/if}
													{if $mandatory.mm_ref_2_phone_number & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
												</label>
											</td>
											<td>
												<input type="text" name="mm_ref_2_phone_number" maxlength="25" value="{$data.mm_ref_2_phone_number}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
												<label title="{$lang.application.confidential}">
													{$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
												</label>
											</td>
										</tr>
									{/if}
								</table>
							</div>
						</div>
					{/if}
					{* HEADLINE *}
					{if $use_field.headline & SB_REGISTRATION}
						<div class="box_info">
							<div class="hdr2">
								<label title="My Contact Info">{$lang.users.headline}</label>
							</div>
							<div class="clear"></div>
							<div class="box_inn">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td class="col1 label" valign="top">
											{if $err_field.headline}<span class="error">{/if}
											{$lang.users.headline}
											{if $err_field.headline}</span>{/if}
											{if $mandatory.headline & SB_REGISTRATION}<span class="mandatory">*</span>{/if}:
										</td>
										<td valign="top" height="60px">
											<textarea name="headline" rows="5" cols="80" style="width:400px; height:50px;" {if $data.root == 1}disabled="disabled"{/if}>{$data.headline}</textarea>
										</td>
									</tr>
								</table>
							</div>
						</div>
					{/if}
					<table border="0" cellpadding="0" cellspacing="0">
						{* terms of use *}
						<tr><td height="20"></td></tr>
						<tr>
							<td class="label" colspan="2">
								<input type="checkbox" name="agreed" value="1" {if $data.agreed}checked="checked"{/if} /> 
								<span class="txtblack">
									<label title="{$lang.terms_of_service_agree_thai}">
										{if $err_field.agreed}<span class="error">{/if}
										{$lang.registration.page_1_agreed_1}
										{if $err_field.agreed}</span>{/if}
										&nbsp;<a href="{$site_root}/info.php?sel=3" target=_blank>{$lang.registration.page_1_agreed_2}</a>
									</label>
								</span>
							</td>
						</tr>
						{* subscribes *}
						{if $use_field.subscribes & SB_REGISTRATION}
							<tr><td height="20"></td></tr>
							<tr>
								<td class="label" valign="top">{$lang.account.subheader_subscribe}:</td>
								<td class="label">
									{foreach item=item key=key from=$s_subscr}
										<div>
											<input type="checkbox" name="s_subscr[{$key}]" value="{$item.id}" {if $item.sel}checked="checked"{/if} />
											{$item.name}
										</div>
									{/foreach}
									{if $adm_subscr}
										<div style="height:10px"></div>
										{foreach item=item key=key from=$adm_subscr}
											<div>
												<input type="checkbox" name="a_subscr[{$key}]" value="{$item.id}" {if $item.sel}checked="checked"{/if} />
												{$item.name}
											</div>
										{/foreach}
									{/if}
								</td>
							</tr>
						{/if}
						{* spam code *}
						<tr><td height="10"></td></tr>
						<tr>
							<td class="label">
								{if $err_field.captcha}<span class="error">{/if}
								{$lang.contact_us.security_code}:
								{if $err_field.captcha}</span>{/if}
							</td>
							<td>
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><img src="{$form.kcaptcha}" alt="{$lang.contact_us.security_code}"></td>
										<td>&nbsp;<input type="text" class="txt_spam_code" name="keystring" /></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				{elseif $form.num == 3}
					<!-- my description begin -->
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="160" class="txtblue bold">
								{$lang.users.weight}:
							</td>
							<td>
								<select name="id_weight" {if $data.root == 1}disabled="disabled"{/if} style="width:200px">
									<option value="0">{$lang.home_page.select_default}</option>
									{foreach item=item from=$weight}
									<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
									{/foreach}
								</select>
							</td>
						</tr>
						<tr><td height="10"></td></tr>
						<tr>
							<td width="160" class="txtblue bold">
								{$lang.users.height}:
							</td>
							<td>
								<select name="id_height" {if $data.root eq 1}disabled="disabled"{/if} style="width:200px">
									<option value="0">{$lang.home_page.select_default}</option>
									{foreach item=item from=$height}
									<option value="{$item.id}" {if $item.sel}selected{/if}>{$item.value}</option>
									{/foreach}
								</select>
							</td>
						</tr>
						<tr><td height="10"></td></tr>
						{section name=f loop=$info}
							<tr>
								<td width="160" class="txtblue bold">
									{$info[f].name}:
									<input type="hidden" name="spr[{$info[f].num}]" value="{$info[f].id}" />
								</td>
								<td>
									<select id="info{$info[f].num}" name="info[{$info[f].num}][]" {if $info[f].type == 2}multiple{/if} style="width:150px; {if $info[f].type == 2}height: 80px;{/if}" {if $data.root==1}disabled="disabled"{/if}>
										{if $info[f].type == 1}
											<option value="">{$button.nothing}</option>
										{/if}
										{if $info[f].type == 2}
											<option value="0" {if $info[f].sel_all}selected="selected"{/if}>{$button.all}</option>
										{/if}
										{foreach item=item from=$info[f].opt}
											<option value="{$item.value}" {if $item.sel}selected="selected"{/if}>{$item.name}</option>
										{/foreach}
									</select>
								</td>
							</tr>
							<tr><td height="10"></td></tr>
						{/section}
					</table>
					<!-- my description end -->
				{elseif $form.num == 4}
					<!-- my notice begin -->
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr valign="top">
							<td width="160" class="txtblue bold" style="padding-top: 5px;">
								{$lang.subsection.notice_text}
							</td>
							<td width="10"></td>
							<td>
								<textarea name="annonce" rows="5" cols="80" {if $data.root == 1}disabled="disabled"{/if} class="hideborder" style="width:400px; height:150px">{$data.annonce}</textarea>
							</td>
						</tr>
					</table>
					<!-- my notice end -->
				{elseif $form.num == 5}
					<!-- my personality begin -->
					<table border="0" cellpadding="5" cellspacing="0">
						{section name=f loop=$personal}
							<tr>
								<td class="txtblue bold" style="padding: 5px;">
									{$personal[f].name}:
									<input type="hidden" name="p_spr[{$smarty.section.f.index}]" value="{$personal[f].id}" />
								</td>
								<td style="padding: 5px;">
									<select name="personal[{$smarty.section.f.index}][]" style="width:200px" {if $data.root == 1}disabled="disabled"{/if}>
										<option value="">{$button.nothing}</option>
										{foreach item=item from=$personal[f].opt}
										<option value="{$item.value}" {if $item.sel}selected="selected"{/if}>{$item.name}</option>
										{/foreach}
									</select>
								</td>
							</tr>
						{/section}
					</table>
					<br>
					<!-- my personality end -->
				{elseif $form.num == 6}
					<!-- my portrait begin -->
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						{section name=f loop=$portrait}
							<tr>
								<td width="120" class="txtblue bold">
									{$portrait[f].name}:
									<input type="hidden" name="port_spr[{$portrait[f].num}]" value="{$portrait[f].id}" />
								</td>
								<td>
									<select name="portrait[{$portrait[f].num}][]" style="width:200px"{if $data.root == 1} disabled="disabled"{/if} />
										<option value="">{$button.nothing}</option>
										{foreach item=item from=$portrait[f].opt}
										<option value="{$item.value}" {if $item.sel}selected="selected"{/if} />{$item.name}</option>
										{/foreach}
									</select>
								</td>
							</tr>
							<tr><td height="15"></td></tr>
						{/section}
					</table>
					<!-- my portrait end -->
				{elseif $form.num == 7}
					<!-- my interests begin -->
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						{section name=f loop=$interests}
							{if $smarty.section.f.index is div by 3}
								<tr>
							{/if}
									<td width="80" class="txtblue bold">
								{$interests[f].name}:
								<input type="hidden" name="int_spr[{$interests[f].num}]" value="{$interests[f].id}" />
							</td>
							<td>
								<table style="padding-top:8px;" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="radio" name="interests[{$interests[f].num}]" value="1" {if $interests[f].sel == 1}checked="checked"{/if} /></td>
										<td><input type="radio" name="interests[{$interests[f].num}]" value="2" {if $interests[f].sel == 2}checked="checked"{/if} /></td>
										<td><input type="radio" name="interests[{$interests[f].num}]" value="3" {if $interests[f].sel == 3}checked="checked"{/if} /></td>
									</tr>
								</table>
							</td>
							{if $smarty.section.f.index_next is div by 3 || $smarty.section.f.last}
								</tr>
								<tr><td height="15"></td></tr>
							{/if}
						{/section}
					</table>
					<!-- my interests end -->
				{elseif $form.num == 8}
					<!-- my criteria begin -->
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="160" class="txtblue bold">
								{$lang.users.weight}:
							</td>
							<td>
								<select name="id_weight" {if $data.root == 1}disabled="disabled"{/if} style="width:200px">
									<option value="0">{$button.all}</option>
									{foreach item=item from=$weight}
									<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
									{/foreach}
								</select>
							</td>
						</tr>
						<tr><td height="15"></td></tr>
						<tr>
							<td width="160" class="txtblue bold">
								{$lang.users.height}:
							</td>
							<td>
								<select name="id_height" {if $data.root == 1}disabled="disabled"{/if} style="width:200px">
									<option value="0">{$button.all}</option>
									{foreach item=item from=$height}
									<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
									{/foreach}
								</select>
							</td>
						</tr>
						<tr><td height="15"></td></tr>
						<tr>
							<td width="160" class="txtblue bold">
								{$lang.users.country}:
							</td>
							<td>
								<select name="id_country[]" {if $data.root == 1}disabled="disabled"{/if} style="width:150px; height:80px" multiple>
									<option value="0" {if $default.id_country == 1}selected="selected"{/if}>{$button.all}</option>
									{foreach item=item from=$country_match}
									<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
									{/foreach}
								</select>
							</td>
						</tr>
						<tr><td height="15"></td></tr>
						<tr>
							<td width="160" class="txtblue bold">
								{$lang.users.nationality}:
							</td>
							<td>
								<select name="id_nation[]" {if $data.root eq 1}disabled{/if} style="width:150px; height:80px" multiple>
									<option value="0" {if $default.id_nation == 1}selected="selected"{/if}>{$button.all}</option>
									{foreach item=item from=$nation_match}
									<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
									{/foreach}
								</select>
							</td>
						</tr>
						<tr><td height="15"></td></tr>
						<tr>
							<td width="160" class="txtblue bold">
								{$lang.users.language}:
							</td>
							<td>
								<select name="id_lang[]" {if $data.root eq 1}disabled{/if} style="width:150px; height:80px" multiple>
									<option value="0" {if $default.id_lang == 1}selected="selected"{/if}>{$button.all}</option>
									{foreach item=item from=$lang_sel_match}
									<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
									{/foreach}
								</select>
							</td>
						</tr>
						<tr><td height="15"></td></tr>
						{section name=f loop=$info}
							<tr>
								<td width="160" class="txtblue bold">
									{$info[f].name}:
									<input type="hidden" name="spr[{$info[f].num}]" value="{$info[f].id}" />
								</td>
								<td>
									<select id="info{$info[f].num}" name="info[{$info[f].num}][]" {if $info[f].type == 2}multiple{/if} style="width:150px; {if $info[f].type eq 2}height:80px;{/if}" {if $data.root eq 1}disabled{/if}>
										<option value="0"{if $info[f].sel_all} selected="selected"{/if}>{$button.all}</option>
										{foreach item=item from=$info[f].opt}
										<option value="{$item.value}" {if $item.sel}selected="selected"{/if}>{$item.name}</option>
										{/foreach}
									</select>
								</td>
							</tr>
							<tr><td height="15"></td></tr>
						{/section}
					</table>
					<!-- my criteria end -->
				{elseif $form.num == 9}
					<!-- desired partner interests begin -->
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						{section name=f loop=$interests}
							{if $smarty.section.f.index is div by 3}
								<tr>
							{/if}
								<td width="160" class="txtblue bold">
									{$interests[f].name}:
									<input type="hidden" name="int_spr[{$interests[f].num}]" value="{$interests[f].id}" />
								</td>
								<td>
									<input type="checkbox" name="interests[{$interests[f].num}][1]" value="1"{if $interests[f].sel_1 == 1} checked="checked"{/if} />
									<input type="checkbox" name="interests[{$interests[f].num}][2]" value="2"{if $interests[f].sel_2 == 1} checked="checked"{/if} />
									<input type="checkbox" name="interests[{$interests[f].num}][3]" value="3"{if $interests[f].sel_3 == 1} checked="checked"{/if} />
								</td>
							{if $smarty.section.f.index_next is div by 3 || $smarty.section.f.last}
								</tr>
								<tr>
									<td height="1" colspan="6">
										<div style="height: 1px; margin: 10px 0px" class="delimiter"></div>
									</td>
								</tr>
							{/if}
						{/section}
					</table>
					<!-- desired partner interests end -->
				{elseif $form.num == 2}
					<!-- photo upload begin -->
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td valign="top" style="padding-right:20px;">
								<img src="{if $data.icon_thumb_path}{$data.icon_thumb_path}{else}{$data.icon_path}{/if}" border="0" class="icon" alt="" />
							</td>
							<td align="left" width="90%">
								<form name="upload_icon" method="post" action="{$form.action}" enctype="multipart/form-data">
									<input type="hidden" name="sel" value="save_9" />
									<input type="hidden" name="upload_type" value="icon" />
									<div style="padding-bottom: 10px;" class="txtblack bold">
										{if $data.icon_del_link}{$lang.profile.change_icon}{else}{$lang.profile.add_new_icon}{/if}
									</div>
									<span class="txtblack">{$data.icon_comment}</span><br/>
									<input type="file" name="upload" {if $data.root == 1}disabled="disabled"{/if} />
									<div style="padding-top:5px">
										<div class="center">
											<div class="btnwrap" style="width:120px;">
												<span><span>
												<input type="button" class="btn_org" style="width:100px;" onclick="document.upload_icon.submit();" value="{$button.upload}" />
												</span></span>
											</div>
										</div>
										{if $data.icon_del_link}
											<div class="center">
												<div class="btnwrap" style="width:120px;">
													<span><span>
													<input type="button" class="btn_org" style="width:100px;" onclick="location.href='{$data.icon_del_link}';" value="{$button.delete}" />
													</span></span>
												</div>
											</div>
										{/if}
										<div class="center">
											<div class="btnwrap" style="width:100px;">
												<span><span>
												<input type="button" class="btn_org" style="width:80px;" onclick="document.location.href='{$form.menu}';" value="{$button.my_application_page}">
												</span></span>
											</div>
										</div>
									</div>
								</form>
							</td>
						</tr>
						{if $data.photo}
							<tr>
								<td height="40" colspan=2 class="txtblack" align=center>{$data.photo_comment}</td>
							</tr>
							{foreach key=key item=item from=$data.photo}
								<tr valign="top">
									<td width="80" valign="top">
										{if $item.view_link}<a href="#" onclick="window.open('{$item.view_link}','photo_view','menubar=0, resizable=1, scrollbars=0,status=0,toolbar=0, width=800,height=600');return false;">{/if}<img src="{if $item.thumb_file}{$item.thumb_file}{else}{$item.file}{/if}" border=0 alt="" class="icon" />{if $item.view_link}</a>{/if}
									</td>
									<td align="left" class="txtblack">
										<form action="{$form.action}" method="post" enctype="multipart/form-data" name="upload_photo_{$key}">
											<input type="hidden" name="sel" value="save_9" />
											<input type="hidden" name="id_file" value="{$item.id}" />
											<input type="hidden" name="upload_type" value="f" />
											<div style="padding-bottom: 10px;" class="txtblack bold">
												{if $item.del_link}{$lang.profile.change_photo}{else}{$lang.profile.add_new_photo}{/if}
											</div>
											<input type=file name="upload" {if $data.root eq 1}disabled{/if} />
											{$lang.users.allow_type}:
											<select name="upload_allow" {if $data.root == 1}disabled="disabled"{/if}>
												<option value="1"{if $item.allow == 1} selected="selected"{/if}>{$lang.users.allow_1}</option>
												<option value="2"{if $item.allow == 2} selected="selected"{/if}>{$lang.users.allow_2}</option>
												<option value="3"{if $item.allow == 3} selected="selected"{/if}>{$lang.users.allow_3}</option>
											</select>
											<div align="left" style="padding-top:10px">
												<textarea name="user_comment" cols="40" rows="2">{$item.user_comment}</textarea>
												{$lang.profile.comment_photo}
											</div>
											<div align="left" style="padding-top:5px">
												<div class="center">
													<div class="btnwrap" style="width:220px;">
														<span><span>
														<input type="button" class="btn_org" style="width:200px;" value="{$button.save}" onclick="document.upload_photo_{$key}.submit();" />
														</span></span>
													</div>
												</div>
												{if $item.del_link}
													<div class="center">
														<div class="btnwrap" style="width:220px;">
															<span><span>
															<input type="button" class="btn_org" style="width:200px;" onclick="location.href='{$item.del_link}';" value="{$button.delete}" />
															</span></span>
														</div>
													</div>
												{/if}
												<div class="center">
													<div class="btnwrap" style="width:100px;">
														<span><span>
														<input type="button" class="btn_org" style="width:80px;" onclick="document.location.href='{$form.menu}';" value="{$button.my_application_page}">
														</span></span>
													</div>
												</div>
											</div>
										</form>
									</td>
								</tr>
								<tr>
									<td height="1" colspan="2">
										<div style="height:1px; margin:10px 0px" class="delimiter"></div>
									</td>
								</tr>
							{/foreach}
						{/if}
					</table>
					<!-- photo upload end -->
				{/if}
				{if $form.num == 1}
					<div id="next_step" class="norm-box" style="margin:15px 0;">
						{* <p class="_acenter"><strong>Next Step &raquo; My Profile Details</strong></p> *}
						<p class="basic-btn_here _mleft30">
							<b>&nbsp;</b><span><input type="submit" value="{$button.create_account}" title="{$button.create_account}" /></span>
						</p>
					</div>
					<div class="clear"></div>
				{elseif $form.num != 2 && $form.num != 9}
					<div style="margin:0px; height:43px">
						<input type="submit" class="button" value="{$button.save}" />
					</div>
				{elseif $form.num == 9}
					<div style="margin:0px; height:43px">
						<input type="submit" class="button" value="{$button.gr_completer}" />
					</div>
				{/if}
				{if $form.num > 1 && $form.num < 9}
					<div style="margin:0px" align="center">
						x{$form.num}x
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr valign="middle">
								<td width="50%" align="left">
									{if $form.num != 1 && $form.num != 9}
										<a href="{$form.site_link}">{$button.gr_acceder}...</a>
									{/if}
								</td>
								<td width="50%" align="right">
									{if $form.num == 2}
										<a href="{$form.next_link}">{$button.next_step} &raquo;</a>
									{elseif $form.num != 1 && $form.num != 9}
										<a href="{$form.next_link}">{$button.skip_step} &raquo;</a>
									{/if}
								</td>
							</tr>
						</table>
					</div>
				{/if}
			</div>
			{if $form.num != 2}
				{* dont need this form on upload page *}
				</form>
			{/if}
		</div>
	</div>
</div>
{/strip}
<script type="text/javascript">
{literal}
function CharCountChecker(obj)
{
	if (obj.value.length >= 165) {
		return false;
	}
	return true;
}
function showcouple()
{
	couple = document.forms.profile.elements.couple;
	couple_show = true;
	if (couple) {
		for (i=0; i<couple.length; i++) {
			if ((couple[i].checked)&&(couple[i].value=="0")) {
				couple_show=false;
				break;
			}
		}
		if (couple_show) {
			document.getElementById('couple_user_form').style.display="block";
		} else {
			document.getElementById('couple_user_form').style.display="none";
		}
	}
	return false;
}
{/literal}
{if $form.num == 1 && $use_field.couple & SB_REGISTRATION}
showcouple();
{/if}
function CheckValue(obj)
{ldelim}
	return true;
	f = obj.form;
	
	if (obj.name=='login' && obj.value == '') {ldelim}
		alert("{$lang.err.invalid_login}");
		return;
	{rdelim}
	if (obj.name=='login' && (obj.value.length < 5 || obj.value.length > 20)) {ldelim}
		alert("{$lang.err.login_length}");
		return;
	{rdelim}
	if (obj.name=='pass' && obj.value == '') {ldelim}
		alert("{$lang.err.invalid_passw}");
		return;
	{rdelim}
	if (obj.name=='pass' && obj.value.length < 6) {ldelim}
		alert("{$lang.err.pass_length}");
		return;
	{rdelim}
	if (obj.name=='login' && obj.value == f.pass.value) {ldelim}
		alert("{$lang.err.pass_eq_log}");
		return;
	{rdelim}
	if (obj.name=='pass' && obj.value == f.login.value) {ldelim}
		alert("{$lang.err.pass_eq_log}");
		return;
	{rdelim}
	if (obj.name=='repass' && (bp.pass.value != obj.value)) {ldelim}
		alert("{$lang.err.pass_eq_repass}");
		return;
	{rdelim}
	if (obj.name=='sname' && obj.value == '') {ldelim}
		alert("{$lang.err.invalid_sname}");
		return;
	{rdelim}
	if (obj.name=='fname' && obj.value == '') {ldelim}
		alert("{$lang.err.invalid_name}");
		return;
	{rdelim}
	if (obj.name=='mm_nickname' && obj.value == '') {ldelim}
		alert("{$lang.err.invalid_mm_nickname}");
		return;
	{rdelim}
	if (obj.name=='email' && (obj.value == '' || (obj.value != '' && obj.value.search('^.+@.+\\..+$') == -1))) {ldelim}
		alert("{$lang.err.email_bad}");
		return;
	{rdelim}
{if $voipcall_feature}
	if (obj.name=='phone' && (obj.value != '' && obj.value.search(/^\d{ldelim}10,15{rdelim}(x\d{ldelim}1,5{rdelim})?$/) == -1)) {ldelim}
		alert("{$lang.err.phone_bad}");
		return;
	{rdelim}
{/if}
{rdelim}

function CheckForm(f)
{ldelim}
	return true;
	if (f.login.value == '') {ldelim}
		alert("{$lang.err.invalid_login}");
		f.login.focus()
		return false;
	{rdelim}
	if (f.login.value.length < 5 || f.login.value.length > 20) {ldelim}
		alert("{$lang.err.login_length}");
		f.login.focus();
		return false;
	{rdelim}
	if (f.pass.value == '') {ldelim}
		alert("{$lang.err.invalid_passw}");
		f.pass.focus()
		return false;
	{rdelim}
	if (f.pass.value.length < 6) {ldelim}
		alert("{$lang.err.pass_length}");
		f.pass.focus();
		return false;
	{rdelim}
	if (f.login.value == f.pass.value) {ldelim}
		alert("{$lang.err.pass_eq_log}");
		f.pass.focus();
		return false;
	{rdelim}
	if (f.pass.value != f.repass.value) {ldelim}
		alert("{$lang.err.pass_eq_repass}");
		f.repass.focus();
		return false;
	{rdelim}
	if (f.fname.value == '') {ldelim}
		alert("{$lang.err.invalid_name}");
		f.name.focus();
		return false;
	{rdelim}
	if (f.sname.value == '') {ldelim}
		alert("{$lang.err.invalid_sname}");
		f.sname.focus();
		return false;
	{rdelim}
	if (f.mm_nickname.value == '') {ldelim}
		alert("{$lang.err.invalid_mm_nickname}");
		f.mm_nickname.focus();
		return false;
	{rdelim}
	if (f.email.value == '' || f.email.value.search('^.+@.+\\..+$') == -1) {ldelim}
		alert("{$lang.err.email_bad}");
		f.email.focus();
		return false;
	{rdelim}
{if $voipcall_feature}
	if (f.phone.value != '' && f.phone.value.search(/^\d{ldelim}10,15{rdelim}(x\d{ldelim}1,5{rdelim})?$/) == -1) {ldelim}
		alert("{$lang.err.phone_bad}");
		f.phone.focus()
		return false;
	{rdelim}
{/if}
	if (bp.agreed.checked == false) {ldelim}
		alert("{$lang.err.term_agreed_err}");
		return false;
	}
	return true;
{rdelim}
function CheckGender(obj)
{ldelim}
	if (obj.value == "1") {ldelim}
		alert("{$lang.confirm.register_as_male}"+"\n"+"{$lang.confirm.unable_to_change}");
	{rdelim}
	else if (obj.value == "2") {ldelim}
		alert("{$lang.confirm.register_as_female}"+"\n"+"{$lang.confirm.unable_to_change}");
	{rdelim}
{rdelim}
</script>
{include file="$gentemplates/index_bottom.tpl"}