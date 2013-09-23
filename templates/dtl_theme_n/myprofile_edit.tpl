{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple">
	<div class="upgrade-member tcxf-ch-la">
		<div>
			<div class="callchat_icons2">
				<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/telephone.png" alt="Call Me" title="Call Me"></a> 
				<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/Chat.png" alt="Chat With Me" title="Chat With Me"></a>
			</div>
			{*
			<div class="flag">
				<img src="{$site_root}{$template_root}/images/UnitedKingdomFlag.png" alt="" />
			</div>
			<div class="player">
				{if $form.num == 11}
					<!-- PHOTO UPLOAD -->
					<script type="text/javascript">
					var playerhost = (("https:" == document.location.protocol) ? "https://meetmenowbangkok.s3.amazonaws.com/ezs3js/secure/" : "http://meetmenowbangkok.s3.amazonaws.com/ezs3js/player/");
					document.write(unescape("%3Cscript src='" + playerhost + "mp3/A04F21FA-F6C1-26F0-BF148594FF357612.js' type='text/javascript'%3E%3C/script%3E"));
					</script>
				{elseif $form.num == 10}
					<!-- CONFIRM EMAIL ADDRESS -->
					<script type="text/javascript">
					var playerhost = (("https:" == document.location.protocol) ? "https://meetmenowbangkok.s3.amazonaws.com/ezs3js/secure/" : "http://meetmenowbangkok.s3.amazonaws.com/ezs3js/player/");
					document.write(unescape("%3Cscript src='" + playerhost + "mp3/A050CD0A-FC86-33A7-3BD6FFF3F1E62D98.js' type='text/javascript'%3E%3C/script%3E"));
					</script>
				{/if}
			</div>
			*}
			{if $form.num == 2}
				<div style="padding-top:15px;">
					<span style="font-size:13px;">{$lang.my_fact_sheet.instructions}</span>
				</div>
				<div class="clear"></div>
			{/if}
			{* <!--
			<div id="left_section">
				<img src="{$site_root}{$template_root}/images/nathamon.png" alt="" class="nathamon_img" {if $form.num == 2} align="left" style="padding-right:30px;" {/if} >
				<div class="flag"> 
					<img src="{$site_root}{$template_root}/images/ThailandFlag.png" alt="" align="left">
				</div>
				<div class="player">
					{if $form.num == 11}
						<!-- PHOTO UPLOAD -->
						<script type="text/javascript">
						var playerhost = (("https:" == document.location.protocol) ? "https://meetmenowbangkok.s3.amazonaws.com/ezs3js/secure/" : "http://meetmenowbangkok.s3.amazonaws.com/ezs3js/player/");
						document.write(unescape("%3Cscript src='" + playerhost + "mp3/521C8592-DE87-8B3C-FA0D4DCD2665E92B.js' type='text/javascript'%3E%3C/script%3E"));
						</script>
					{elseif $form.num == 1}
						<!-- PROFILE EDIT -->
						<script type="text/javascript">
						//Deleted the audio, as it is wrong audio
						//var playerhost = (("https:" == document.location.protocol) ? "https://meetmenowbangkok.s3.amazonaws.com/ezs3js/secure/" : "http://meetmenowbangkok.s3.amazonaws.com/ezs3js/player/");
						//document.write(unescape("%3Cscript src='" + playerhost + "mp3/521C8592-DE87-8B3C-FA0D4DCD2665E92B.js' type='text/javascript'%3E%3C/script%3E"));
						</script>
					{elseif $form.num == 10}
						<!-- CONFIRM EMAIL ADDRESS -->
						<script type="text/javascript">
						var playerhost = (("https:" == document.location.protocol) ? "https://meetmenowbangkok.s3.amazonaws.com/ezs3js/secure/" :"http://meetmenowbangkok.s3.amazonaws.com/ezs3js/player/");
						document.write(unescape("%3Cscript src='" + playerhost + "mp3/521DF658-F0E6-4B06-843F14379B26F655.js' type='text/javascript'%3E%3C/script%3E"));
						</script>
					{/if}
				</div>
			</div>
			--> *}
		</div>
		<div>
			<div id="myprofile_edit" class="align_top">
				<div class="box_info">
					<div style="{if $form.num == 5 || $form.num == 6 || $form.num == 8}width:280px;{/if}">
						<div class="hdr2">
							<label title="{$form.tool_tip}">{$form.header}</label>
						</div>
					</div>
					<div class="box_inn">
						<div class="det-14-2">
							{if $form.num == 1}
								<label title="{$form.subheader_tool}">{$form.subheader}</label>
							{elseif $form.num == 11}
								<label title="{$lang.subsection.upload_text_thai}">{$lang.subsection.upload_text}</label>
							{/if}
						</div>
						{if $form.err}
							<div class="error_msg">{$form.err}</div>
						{/if}
						{if $form.num == 1}
							<form method="post" action="{$form.action}" name="profile" onsubmit="return CheckForm(this);">
								{$form.hiddens}
								{* USERNAME *}
								<div class="block_blue" style="padding-top:10px;">
									<div class="titlebg" style="margin-top: 0px;">
										<div class="top_showhide">
											<a href="#" onclick="$('#div_UserName').slideToggle('slow'); return false;">
											Click to Show/Hide</a>
										</div>
										<div>
											<label title="{$lang.mylogin_thai}">{$lang.profile_head.login}</label>
										</div>
										<div class="clear"></div>
									</div>
									<div id="div_UserName" class="disp_block" style="padding:5px;">
										<table border="0" cellpadding="0" cellspacing="0">
											{* username *}
											<tr>
												<td class="col1 label">
													<label title="{$lang.username_thai}">
														{if $err_field.login}<span class="error">{/if}
														{$lang.users.login}
														{if $err_field.login}</span>{/if}
														 &nbsp;<span class="mandatory">*</span>:
													</label>
												</td>
												<td>
													<input type="text" name="login" id="login" maxlength="40" value="{$data.login}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" onblur="CheckValue(this);" />
													<label title="{$lang.application.confidential}">
														{$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
													</label>
													{* error message for login already in use *}
													<div id="error_div" class="error"></div>
												</td>
											</tr>
										</table>
									</div>
								</div>
								{* PERSONAL INFO *}
								<br>
								<div class="block_blue">
									<div class="titlebg" style="margin-top: 0px;">
										<div class="top_showhide">
											<a href="#" onclick="$('#div_PersonalInfo').slideToggle('slow'); return false;">
											Click to Show/Hide</a>
										</div>
										<div>
											<label title="{$lang.personal_info_thai}">{$lang.profile_head.personal_info}</label>
										</div>
										<div class="clear"></div>
									</div>
									<div id="div_PersonalInfo" class="disp_block" style="padding:5px;">
										<table border="0" cellpadding="0" cellspacing="0">
											{* first name *}
											{if $use_field.fname & SB_EDIT}
												<tr>
													<td class="col1 label">
														<label title="{$lang.first_name_thai}">
															{if $err_field.fname}<span class="error">{/if}
															{$lang.users.fname}
															{if $err_field.fname}</span>{/if}
															{if $mandatory.fname & SB_EDIT} <span class="mandatory">*</span>{/if}:
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
											{if $use_field.sname & SB_EDIT}
												<tr>
													<td class="col1 label">
														<label title="{$lang.last_name_thai}">
															{if $err_field.sname}<span class="error">{/if}
															{$lang.users.sname}
															{if $err_field.sname}</span>{/if}
															{if $mandatory.sname & SB_EDIT} <span class="mandatory">*</span>{/if}:
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
											{if $use_field.mm_nickname & SB_EDIT}
												<tr>
													<td class="col1 label">
														<label title="{$lang.nickname_thai}">
															{if $err_field.mm_nickname}<span class="error">{/if}
															{$lang.users.mm_nickname}
															{if $err_field.mm_nickname}</span>{/if}
															{if $mandatory.mm_nickname & SB_EDIT} <span class="mandatory">*</span>{/if}:
														</label>
													</td>
													<td>
														<input type="text" name="mm_nickname" maxlength="25" value="{$data.mm_nickname}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
													</td>
												</tr>
											{/if}
											{* gender *}
											{if $use_field.gender & SB_EDIT}
												<tr>
													<td class="col1 label">
														<label title="{$lang.gender_thai}">
															{if $err_field.gender}<span class="error">{/if}
															{$lang.users.gender}
															{if $err_field.gender}</span>{/if}
															{if $mandatory.gender & SB_EDIT} <span class="mandatory">*</span>{/if}:
														</label>
													</td>
													<td>
														<table border="0" cellpadding="0" cellspacing="0">
															<tr>
																{foreach item=item from=$gender}
																	{if $item.sel}
																		<td>
																			<input type="radio" name="gender" id="gender_{$item.id}" value="{$item.id}" {if $item.sel}checked="checked"{/if} />
																		</td>
																		<td style="padding-right:20px;" class="txtblack">
																			<label for="gender_{$item.id}" title="{$lang.mm_gender[$item.id]}">{$item.name}</label>
																		</td>
																	{/if}
																{/foreach}
															</tr>
														</table>
													</td>
												</tr>
											{/if}
											{* couple *}
											{if $use_field.couple & SB_EDIT}
												<tr>
													<td class="col1 label">
														{if $err_field.couple}<span class="error">{/if}
														{$lang.users.single_couple}
														{if $err_field.couple}</span>{/if}
														{if $mandatory.couple & SB_EDIT} <span class="mandatory">*</span>{/if}:
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
																			{$lang.users.couple_link}: <br>
																			<a href="{$data.couple_link}" target="_blank"><b>{$data.couple_login}</b></a>
																			 {$data.couple_gender} {$data.couple_age} {$lang.home_page.ans}<br>
																			<input type="checkbox" value="1" name="couple_delete" />
																			 {$lang.users.couple_delete}<br>
																			{if ! $data.couple_accept}{$lang.users.couple_accept}{/if}
																		{else}
																			<table cellspacing="0" cellpadding="0" border="0">
																				<tr>
																					<td class="txtblack">{$lang.users.couple_login}: </td>
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
											{if $use_field.mm_marital_status & SB_EDIT}
												<tr>
													<td class="col1 label">
														<label title="{$lang.marital_status_thai}">
															{if $err_field.mm_marital_status}<span class="error">{/if}
															{$lang.users.mm_marital_status}
															{if $err_field.mm_marital_status}</span>{/if}
															{if $mandatory.mm_marital_status & SB_EDIT} <span class="mandatory">*</span>{/if}:
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
											{if $use_field.date_birthday & SB_EDIT}
												<tr>
													<td class="col1 label">
														<label title="{$lang.birthday_thai}">
															{if $err_field.date_birthday}<span class="error">{/if}
															{$lang.users.date_birthday}
															{if $err_field.date_birthday}</span>{/if}
															{if $mandatory.date_birthday & SB_EDIT} <span class="mandatory">*</span>{/if}:
														</label>
													</td>
													<td>
														<select name="b_{$date_part1_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:auto !important; margin-right:5px;">
															{foreach item=item from=$date_part1}
																<option value="{$item.value}" {if $item.sel}selected="selected"{/if}>{$item.name}</option>
															{/foreach}
														</select>
														<select name="b_{$date_part2_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:auto !important; margin-right:5px;">
															{foreach item=item from=$date_part2}
																<option value="{$item.value}" {if $item.sel}selected="selected"{/if}>{$item.name}</option>
															{/foreach}
														</select>
														<select name="b_{$date_part3_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:auto !important;">
															{foreach item=item from=$date_part3}
																<option value="{$item.value}" {if $item.sel}selected="selected"{/if}>{$item.name}</option>
															{/foreach}
														</select>
													</td>
												</tr>
											{/if}
											{* added: place of birth *}
											{*Added : Height >> Start*}
											{if $use_field.id_height & SB_EDIT}
												<tr>
													<td class="col1 label">
														<label title="{$lang.height}">
															{if $err_field.height}<span class="error">{/if}
															{$lang.users.height}
															{if $err_field.height}</span>{/if}
															{if $mandatory.height & SB_EDIT} <span class="mandatory">*</span>{/if}:
														</label>
													</td>
													<td>
														<select name="id_height" {if $data.root == 1}disabled="disabled"{/if} style="width:auto !important; margin-right:5px;">
															{foreach item=item from=$height}
															<option value={$item.id} {if $item.id eq $data.id_height} selected='selected' {/if} >{$item.value}</option>
															{/foreach}
														</select>
			
														
													</td>
												</tr>
											{/if}
											{*End*}
											{*Added : Weight >> Start*}
											{if $use_field.id_weight & SB_EDIT}
												<tr>
													<td class="col1 label">
														<label title="{$lang.weight_thai}">
															{if $err_field.weight}<span class="error">{/if}
															{$lang.users.weight}
															{if $err_field.weight}</span>{/if}
															{if $mandatory.weight & SB_EDIT} <span class="mandatory">*</span>{/if}:
														</label>
													</td>
													<td>
														<select name="id_weight" {if $data.root == 1}disabled="disabled"{/if} style="width:auto !important; margin-right:5px;">
															{foreach item=item from=$weight}
															<option value="{$item.id}" {if $item.id eq $data.id_weight} selected='selected' {/if}>{$item.value}</option>
															{/foreach}
														</select>
														
													</td>
												</tr>
											{/if}
											{*End*}
											{if $use_field.mm_place_of_birth & SB_EDIT}
												<tr>
													<td class="col1 label">
														<label title="{$lang.place_of_birth_thai}">
															{if $err_field.mm_place_of_birth}<span class="error">{/if}
															{$lang.users.mm_place_of_birth}
															{if $err_field.mm_place_of_birth}</span>{/if}
															{if $mandatory.mm_place_of_birth & SB_EDIT} <span class="mandatory">*</span>{/if}:
														</label>
													</td>
													<td><input type="text" name="mm_place_of_birth" maxlength="25" value="{$data.mm_place_of_birth}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" /></td>
												</tr>
											{/if}
											{* nationality *}
											{if $use_field.id_nationality & SB_EDIT}
												<tr>
													<td class="col1 label">
														<label title="{$lang.nationality_thai}">
															{if $err_field.id_nationality}<span class="error">{/if}
															{$lang.users.nationality}
															{if $err_field.id_nationality}</span>{/if}
															{if $mandatory.id_nationality & SB_EDIT} <span class="mandatory">*</span>{/if}:
														</label>
													</td>
													<td>
														{if $gender[0].sel == 1}
															<select name="id_nationality" {if $data.root == 1}disabled="disabled"{/if} style="width:200px">
																<option value="0">{$lang.home_page.select_default}</option>
																{foreach item=item from=$nation}
																<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
																{/foreach}
															</select>
														{else}
															{foreach item=item from=$nation}
																{if $item.sel}
																	<input type="hidden" name="id_nationality" value="{$item.id}">{$item.value}
																{/if}
															{/foreach}
														{/if}
													</td>
												</tr>
											{/if}
											{* added: identification number *}
											{if $use_field.mm_id_number & SB_EDIT && $data.gender == 2}
												<tr>
													<td class="col1 label">
														<label title="ID Number">
															{if $err_field.mm_id_number}<span class="error">{/if}
															{$lang.users.mm_id_number}
															{if $err_field.mm_id_number}</span>{/if}
															{if $mandatory.mm_id_number & SB_EDIT} <span class="mandatory">*</span>{/if}:
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
											{* added: identification type *}
											{if $use_field.mm_id_type & SB_EDIT && $data.gender == 2}
												<tr>
													<td class="col1 label">
														<label title="ID Type">
															{if $err_field.mm_id_type}<span class="error">{/if}
															{$lang.users.mm_id_type}
															{if $err_field.mm_id_type}</span>{/if}
															{if $mandatory.mm_id_type & SB_EDIT} <span class="mandatory">*</span>{/if}:
														</label>
													</td>
													<td>
														<input type="text" name="mm_id_type" maxlength="25" value="{$data.mm_id_type}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
														<label title="{$lang.application.confidential}">
															{$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
														</label>
													</td>
												</tr>
											{else}
												<tr>
													<td colspan="2">
														<input type="hidden" name="mm_id_type" value="{$data.mm_id_type}" />
													</td>
												</tr>
											{/if}
										</table>
									</div>
								</div>
								{* CONTACT INFO *}
								<br>
								<div class="block_blue">
									<div class="titlebg" style="margin-top: 0px;">
										<div class="top_showhide">
											<a href="#" onclick="$('#div_ContactInfo').slideToggle('slow'); return false;">
											Click to Show/Hide</a>
										</div>
										<div>
											<label title="{$lang.contact_info_thai}">{$lang.profile_head.contact_info}</label>
										</div>
										<div class="clear"></div>
									</div>
									<div id="div_ContactInfo" class="disp_none" style="padding:5px;">
										<table border="0" cellpadding="0" cellspacing="0">
											{* email *}
											{if $use_field.email & SB_EDIT}
												<tr>
													<td class="col1 label">
														<label title="{$lang.email_thai}">
															{if $err_field.email}<span class="error">{/if}
															{$lang.users.email}
															{if $err_field.email}</span>{/if}
															{if $mandatory.email & SB_EDIT} <span class="mandatory">*</span>{/if}:
														</label>
													</td>
													<td>
														<input type="text" name="email" maxlength="50" value="{$data.email}" {if $data.root == 1}disabled="disabled"{/if} style="width:250px" onblur="CheckValue(this);" />
														<label title="{$lang.application.confidential}">
															{$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
														</label>
													</td>
												</tr>
											{/if}
											{* added: contact phone number *}
											{if $use_field.mm_contact_phone_number & SB_EDIT}
												<tr>
													<td class="col1 label">
														<label title="{$lang.contact_phone_thai}">
															{if $err_field.mm_contact_phone_number}<span class="error">{/if}
															{$lang.users.mm_contact_phone_number}
															{if $err_field.mm_contact_phone_number}</span>{/if}
															{if $mandatory.mm_contact_phone_number & SB_EDIT} <span class="mandatory">*</span>{/if}:
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
											{* added: mobile phone number *}
											{if $use_field.mm_contact_mobile_number & SB_EDIT}
												<tr>
													<td class="col1 label">
														<label title="{$lang.contact_mobile_thai}">
															{if $err_field.mm_contact_mobile_number}<span class="error">{/if}
															{$lang.users.mm_contact_mobile_number}
															{if $err_field.mm_contact_mobile_number}</span>{/if}
															{if $mandatory.mm_contact_mobile_number & SB_EDIT} <span class="mandatory">*</span>{/if}:
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
												{if $use_field.phone & SB_EDIT}
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
								{if $use_field.gender_search & SB_EDIT
								|| $use_field.age_min & SB_EDIT
								|| $use_field.age_max & SB_EDIT
								|| $use_field.couple_search & SB_EDIT
								|| $use_field.relationship & SB_EDIT}
									<br>
									<div class="block_blue">
										<div class="titlebg" style="margin-top: 0px;">
											<div class="top_showhide">
												<a href="#" onclick="$('#div_LookingFor').slideToggle('slow'); return false;">
												Click to Show/Hide</a>
											</div>
											<div>
												{if $data.gender_search == GENDER_MALE}
													<label title="{$lang.looking_for_man_thai}">{$lang.profile_head.looking_for_man}</label>
												{else}
													<label title="{$lang.looking_for_woman_thai}">{$lang.profile_head.looking_for_woman}</label>
												{/if}
											</div>
											<div class="clear"></div>
											{if ! ($use_field.gender_search & SB_EDIT)}
												<input type="hidden" name="gender_search" value="{$data.gender_search}" />
											{/if}
										</div>
										<div id="div_LookingFor" class="disp_none" style="padding:5px;">
											<table border="0" cellpadding="0" cellspacing="0">
												{* search gender *}
												{if $use_field.gender_search & SB_EDIT}
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
												{if $use_field.age_min & SB_EDIT || $use_field.age_max & SB_EDIT}
													<tr>
														<td class="col1 label">
															<label title="{$lang.age_range_thai}">
																{if $err_field.age_min || $err_field.age_max}<span class="error">{/if}
																{$lang.users.age_range}
																{if $err_field.age_min || $err_field.age_max}</span>{/if}
																{if $mandatory.age_min & SB_EDIT || $mandatory.age_max & SB_EDIT} <span class="mandatory">*</span>{/if}:
															</label>
														</td>
														<td>
															{if $use_field.age_min & SB_EDIT}
																<span>{$lang.users.from_big}</span>
																<span style="padding-left:10px;">
																	<select name="age_min" {if $data.root == 1}disabled="disabled"{/if}>
																		{foreach item=item from=$age_min}
																		<option value="{$item}" {if $min_age_sel == $item}selected="selected"{/if}>{$item}</option>
																		{/foreach}
																	</select>
																</span>
															{/if}
															{if $use_field.age_max & SB_EDIT}
																<span style="padding-left:10px;">{$lang.users.to_big}</span>
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
												{if $use_field.couple_search & SB_EDIT}
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
												{if $use_field.id_relationship & SB_EDIT}
													<tr>
														<td class="col1 label" valign="top">
															{if $err_field.id_relationship}<span class="error">{/if}
															{$lang.users.relationship}
															{if $err_field.id_relationship}</span>{/if}:
														</td>
														<td class="txtblack">
															{if $relation_input_type == "select"}
																<select name="relation[]" {if $data.root == 1}disabled="disabled"{/if} multiple style="width: 150px; height: 80px">
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
								{if $use_field.id_country & SB_EDIT || $use_field.id_city & SB_EDIT || $use_field.mm_city & SB_EDIT || ($use_field.id_region & SB_EDIT) && $data.gender == 2}
									<br>
									<div class="block_blue">
										<div class="titlebg" style="margin-top: 0px;">
											<div class="top_showhide">
												<a href="#" onclick="$('#div_AddressInfo').slideToggle('slow'); return false;">
												Click to Show/Hide</a>
											</div>
											<div>
												<label title="{$lang.address_info_thai}">{$lang.profile_head.address_info}</label>
											</div>
											<div class="clear"></div>
										</div>
										<div id="div_AddressInfo" class="disp_none" style="padding:5px;">
											<table border="0" cellpadding="0" cellspacing="0">
												{* country *}
												{if $use_field.id_country & SB_EDIT}
													<tr>
														<td class="col1 label">
															<label title="{$lang.country_thai}">
																{if $err_field.id_country}<span class="error">{/if}
																{$lang.users.country}
																{if $err_field.id_country}</span>{/if}
																{if $mandatory.id_country & SB_EDIT} <span class="mandatory">*</span>{/if}:
															</label>
														</td>
														<td>
															{if $gender[0].sel == 1}
																<select name="id_country" {if $data.root == 1}disabled="disabled"{/if} {if $use_field.id_region & SB_EDIT && $data.gender == 2}onchange="SelectRegion('mp', this.value, document.getElementById('region_div'), document.getElementById('city_div'));"{/if}>
																	<option value="0">{$lang.home_page.select_default}</option>
																	{foreach item=item from=$country}
																	<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
																	{/foreach}
																</select>
															{else}
																{foreach item=item from=$country}
																	{if $item.sel}
																		<input type="hidden" value="{$item.id}" name="id_country">{$item.value}
																	{/if}
																{/foreach}
															{/if}
														</td>
													</tr>
												{/if}
												{* region *}
												{if $use_field.id_region & SB_EDIT && $data.gender == 2}
													<tr>
														<td class="col1 label">
															{if $err_field.id_region}<span class="error">{/if}
															{$lang.users.region}
															{if $err_field.id_region}</span>{/if}
															{if $mandatory.id_region & SB_EDIT} <span class="mandatory">*</span>{/if}:
														</td>
														<td>
															<div id="region_div">
																{if isset($region)}
																	<select name="id_region" {if $data.root == 1}disabled="disabled"{/if} {if $use_field.id_city & SB_EDIT} onchange="SelectCity('mp', this.value, document.getElementById('city_div'));" {/if} >
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
												{if $use_field.id_city & SB_EDIT && $data.gender == 2}
													<tr>
														<td class="col1 label">
															<label title="{$lang.city_thai}">
																{if $err_field.id_city}<span class="error">{/if}
																{$lang.users.city}
																{if $err_field.id_city}</span>{/if}
																{if $mandatory.id_city & SB_EDIT} <span class="mandatory">*</span>{/if}:
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
												{if $use_field.mm_city & SB_EDIT}
													<tr>
														<td class="col1 label">
															<label title="{$lang.city}">
																{if $err_field.mm_city}<span class="error">{/if}
																{$lang.users.city}
																{if $err_field.mm_city}</span>{/if}
																{if $mandatory.mm_city & SB_EDIT} <span class="mandatory">*</span>{/if}:
															</label>
														</td>
														<td>
															<input type="text" name="mm_city" maxlength="25" value="{$data.mm_city}" size="30" {if $data.root == 1}disabled="disabled"{/if} style="width: 150px" />
														</td>
													</tr>
												{/if}
												{* zipcode *}
												{if $use_field.zipcode & SB_EDIT}
													<tr>
														<td class="col1 label">
															<label title="{$lang.zipcode_thai}">
																{if $err_field.zipcode}<span class="error">{/if}
																{$lang.users.zipcode}
																{if $err_field.zipcode}</span>{/if}
																{if $mandatory.zipcode & SB_EDIT} <span class="mandatory">*</span>{/if}:
															</label>
														</td>
														<td>
															<input type="text" name="zipcode" maxlength="25" value="{$data.zipcode}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" maxlength="{$form.zip_count}" />
															<label title="{$lang.application.confidential}">
																{$lang.confidential} <img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
															</label>
															{* <font class="text_hidden">{$lang.users.us_only}</font> *}
														</td>
													</tr>
												{/if}
												{* added: address line 1 *}
												{if $use_field.mm_address_1 & SB_EDIT}
													<tr>
														<td class="col1 label">
															<label title="{$lang.address_line_1_thai}">
																{if $err_field.mm_address_1}<span class="error">{/if}
																{$lang.users.mm_address_1}
																{if $err_field.mm_address_1}</span>{/if}
																{if $mandatory.mm_address_1 & SB_EDIT} <span class="mandatory">*</span>{/if}:
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
												{if $use_field.mm_address_2 & SB_EDIT}
													<tr>
														<td class="col1 label">
															<label title="{$lang.address_line_2_thai}">
																{if $err_field.mm_address_2}<span class="error">{/if}
																{$lang.users.mm_address_2}
																{if $err_field.mm_address_2}</span>{/if}
																{if $mandatory.mm_address_2 & SB_EDIT} <span class="mandatory">*</span>{/if}:
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
								{if $use_field.id_language_1 & SB_EDIT}
									<br>
									<div class="block_blue">
										<div class="titlebg" style="margin-top: 0px;">
											<div class="top_showhide">
												<a href="#" onclick="$('#div_LanguageInfo').slideToggle('slow'); return false;">
												Click to Show/Hide</a>
											</div>
											<div>
												<label title="{$lang.language_info_thai}">{$lang.profile_head.language_info}</label>
											</div>
											<div class="clear"></div>
										</div>
										<div id="div_LanguageInfo" class="disp_none" style="padding:5px;">
											<table border="0" cellpadding="0" cellspacing="0">
												{* language *}
												{if $use_field.id_language_1 & SB_EDIT}
													<tr>
														<td class="col1 label">
															<label title="{$lang.language_thai}">
																{if $err_field.id_language_1 || $err_field.id_language_2 || $err_field.id_language_3}<span class="error">{/if}
																{$lang.users.language}
																{if $err_field.id_language_1 || $err_field.id_language_2 || $err_field.id_language_3}</span>{/if}
																{if $mandatory.id_language_1 & SB_EDIT} <span class="mandatory">*</span>{/if}:
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
												{if $use_field.mm_level_of_english & SB_EDIT}
													<tr>
														<td class="col1 label">
															<label title="{$lang.level_of_english_thai}">
																{if $err_field.mm_level_of_english}<span class="error">{/if}
																{$lang.users.mm_level_of_english}
																{if $err_field.mm_level_of_english}</span>{/if}
																{if $mandatory.mm_level_of_english & SB_EDIT} <span class="mandatory">*</span>{/if}:
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
												{if $use_field.site_language & SB_EDIT}
													<tr>
														<td class="col1 label">
															{if $err_field.site_language}<span class="error">{/if}
															{$lang.users.site_language}
															{if $err_field.site_language}</span>{/if}
															{if $mandatory.site_language & SB_EDIT} <span class="mandatory">*</span>{/if}:
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
								{if $use_field.mm_employment_status & SB_EDIT}
									<br>
									<div class="block_blue">
										<div class="titlebg" style="margin-top: 0px;">
											<div class="top_showhide">
												<a href="#" onclick="$('#div_EmploymentInfo').slideToggle('slow'); return false;">
												Click to Show/Hide</a>
											</div>
											<div>
												<label title="{$lang.employment_info_thai}">{$lang.profile_head.employment_info}</label>
											</div>
											<div class="clear"></div>
										</div>
										<div id="div_EmploymentInfo" class="disp_none" style="padding:5px;">
											<table width="100%" border="0" cellpadding="0" cellspacing="0">
												{* added: employment status *}
												{if $use_field.mm_employment_status & SB_EDIT}
													<tr>
														<td class="col1 label">
															<label title="Employment Status">
																{if $err_field.mm_employment_status || $err_field.mm_business_name || $err_field.mm_employer_name}<span class="error">{/if}
																{$lang.users.mm_employment_status}
																{if $err_field.mm_employment_status || $err_field.mm_business_name || $err_field.mm_employer_name}</span>{/if}
																{if $mandatory.mm_employment_status & SB_EDIT} <span class="mandatory">*</span>{/if}:
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
																			 <label title="{$lang.mm_employment_status[$item.value]}"> {$item.value} </label>
																		</td>
																		{if $item.id == 1}
																			<td> </td>
																			<td> </td>
																		{elseif $item.id == 2}
																			{if $use_field.mm_business_name & SB_EDIT}
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
																				<td> </td>
																				<td> </td>
																			{/if}
																		{elseif $item.id == 3}
																			{if $use_field.mm_employer_name & SB_EDIT}
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
																				<td> </td>
																				<td> </td>
																			{/if}
																		{/if}
																	</tr>
																{/foreach}
															</table>
														</td>
													</tr>
												{/if}
												{* added: job position *}
												{if $use_field.mm_job_position & SB_EDIT}
													<tr>
														<td class="col1 label">
															<label title="{$lang.job_position_thai}">
																{if $err_field.mm_job_position}<span class="error">{/if}
																{$lang.users.mm_job_position}
																{if $err_field.mm_job_position}</span>{/if}
																{if $mandatory.mm_job_position & SB_EDIT} <span class="mandatory">*</span>{/if}:
															</label>
														</td>
														<td>
															<input type="text" name="mm_job_position" maxlength="25" value="{$data.mm_job_position}" {if $data.root == 1}disabled="disabled"{/if} style="width:200px" />
														</td>
													</tr>
												{/if}
												{* added: work address *}
												{if $use_field.mm_work_address & SB_EDIT}
													<tr>
														<td class="col1 label">
															<label title="{$lang.work_address_thai}">
																{if $err_field.mm_work_address}<span class="error">{/if}
																{$lang.users.mm_work_address}
																{if $err_field.mm_work_address}</span>{/if}
																{if $mandatory.mm_work_address & SB_EDIT} <span class="mandatory">*</span>{/if}:
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
												{if $use_field.mm_work_phone_number & SB_EDIT}
													<tr>
														<td class="col1 label">
															<label title="{$lang.work_phone_thai}">
																{if $err_field.mm_work_phone_number}<span class="error">{/if}
																{$lang.users.mm_work_phone_number}
																{if $err_field.mm_work_phone_number}</span>{/if}
																{if $mandatory.mm_work_phone_number & SB_EDIT} <span class="mandatory">*</span>{/if}:
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
								{if $use_field.mm_ref_1_first_name & SB_EDIT}
									<br>
									<div class="block_blue">
										<div class="titlebg" style="margin-top: 0px;">
											<div class="top_showhide">
												<a href="#" onclick="$('#div_Reference1').slideToggle('slow'); return false;">
												Click to Show/Hide</a>
											</div>
											<div>
												<label title="{$lang.reference_1_thai}">{$lang.profile_head.reference_1}</label>
											</div>
											<div class="clear"></div>
										</div>
										<div id="div_Reference1" class="disp_none" style="padding:5px;">
											<table width="100%" border="0" cellpadding="0" cellspacing="0">
												{* added: ref 1 first name *}
												{if $use_field.mm_ref_1_first_name & SB_EDIT}
													<tr>
														<td class="col1 label">
															<label title="{$lang.first_name_thai}">
																{if $err_field.mm_ref_1_first_name}<span class="error">{/if}
																{$lang.users.fname}
																{if $err_field.mm_ref_1_first_name}</span>{/if}
																{if $mandatory.mm_ref_1_first_name & SB_EDIT} <span class="mandatory">*</span>{/if}:
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
												{if $use_field.mm_ref_1_last_name & SB_EDIT}
													<tr>
														<td class="col1 label">
															<label title="{$lang.last_name_thai}">
																{if $err_field.mm_ref_1_last_name}<span class="error">{/if}
																{$lang.users.sname}
																{if $err_field.mm_ref_1_last_name}</span>{/if}
																{if $mandatory.mm_ref_1_last_name & SB_EDIT} <span class="mandatory">*</span>{/if}:
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
												{if $use_field.mm_ref_1_relationship & SB_EDIT}
													<tr>
														<td class="col1 label">
															<label title="{$lang.reference_relationship_thai}">
																{if $err_field.mm_ref_1_relationship}<span class="error">{/if}
																{$lang.users.mm_reference_relationship}
																{if $err_field.mm_ref_1_relationship}</span>{/if}
																{if $mandatory.mm_ref_1_relationship & SB_EDIT} <span class="mandatory">*</span>{/if}:
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
												{if $use_field.mm_ref_1_phone_number & SB_EDIT}
													<tr>
														<td class="col1 label">
															<label title="{$lang.reference_phone_thai}">
																{if $err_field.mm_ref_1_phone_number}<span class="error">{/if}
																{$lang.users.mm_reference_phone_number}
																{if $err_field.mm_ref_1_phone_number}</span>{/if}
																{if $mandatory.mm_ref_1_phone_number & SB_EDIT} <span class="mandatory">*</span>{/if}:
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
								{if $use_field.mm_ref_1_first_name & SB_EDIT}
									<br>
									<div class="block_blue">
										<div class="titlebg" style="margin-top: 0px;">
											<div class="top_showhide">
												<a href="#" onclick="$('#div_Reference2').slideToggle('slow'); return false;">
												Click to Show/Hide</a>
											</div>
											<div>
												<label title="{$lang.reference_2_thai}">{$lang.profile_head.reference_2}</label>
											</div>
											<div class="clear"></div>
										</div>
										<div id="div_Reference2" class="disp_none" style="padding:5px;">
											<table width="100%" border="0" cellpadding="0" cellspacing="0">
												{* added: ref 2 first name *}
												{if $use_field.mm_ref_2_first_name & SB_EDIT}
													<tr>
														<td class="col1 label">
															<label title="{$lang.first_name_thai}">
																{if $err_field.mm_ref_2_first_name}<span class="error">{/if}
																{$lang.users.fname}
																{if $err_field.mm_ref_2_first_name}</span>{/if}
																{if $mandatory.mm_ref_2_first_name & SB_EDIT} <span class="mandatory">*</span>{/if}:
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
												{if $use_field.mm_ref_2_last_name & SB_EDIT}
													<tr>
														<td class="col1 label">
															<label title="{$lang.last_name_thai}">
																{if $err_field.mm_ref_2_last_name}<span class="error">{/if}
																{$lang.users.sname}
																{if $err_field.mm_ref_2_last_name}</span>{/if}
																{if $mandatory.mm_ref_2_last_name & SB_EDIT} <span class="mandatory">*</span>{/if}:
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
												{if $use_field.mm_ref_2_relationship & SB_EDIT}
													<tr>
														<td class="col1 label">
															<label title="{$lang.reference_relationship_thai}">
																{if $err_field.mm_ref_2_relationship}<span class="error">{/if}
																{$lang.users.mm_reference_relationship}
																{if $err_field.mm_ref_2_relationship}</span>{/if}
																{if $mandatory.mm_ref_2_relationship & SB_EDIT} <span class="mandatory">*</span>{/if}:
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
												{if $use_field.mm_ref_2_phone_number & SB_EDIT}
													<tr>
														<td class="col1 label">
															<label title="{$lang.reference_phone_thai}">
																{if $err_field.mm_ref_2_phone_number}<span class="error">{/if}
																{$lang.users.mm_reference_phone_number}
																{if $err_field.mm_ref_2_phone_number}</span>{/if}
																{if $mandatory.mm_ref_2_phone_number & SB_EDIT} <span class="mandatory">*</span>{/if}:
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
								{if $use_field.headline & SB_EDIT}
									<br>
									<div class="block_blue">
										<div class="titlebg" style="margin-top: 0px;">
											<div class="top_showhide">
												<a href="#" onclick="$('#div_Headline').slideToggle('slow'); return false;">
												Click to Show/Hide</a>
											</div>
											<div>
												<label title="{$lang.users.headline}">{$lang.users.headline}</label>
											</div>
											<div class="clear"></div>
										</div>
										<div id="div_Headline" class="disp_none" style="padding:5px;">
											<table width="100%" border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td class="col1 label" valign="top">
														{if $err_field.headline}<span class="error">{/if}
														{$lang.users.headline}
														{if $err_field.headline}</span>{/if}
														{if $mandatory.headline & SB_EDIT} <span class="mandatory">*</span>{/if}:
													</td>
													<td valign="top" height="60px">
														<textarea name="headline" rows="5" cols="80" style="width:400px; height:50px;" {if $data.root == 1}disabled="disabled"{/if}>{$data.headline}</textarea>
													</td>
												</tr>
											</table>
										</div>
									</div>
								{/if}
								{* PRIVACY SETTINGS *}
								{if $use_field.privacy_settings & SB_EDIT}
									<br>
									<div class="block_blue">
										<div class="titlebg" style="margin-top: 0px;">
											<div class="top_showhide">
												<a href="#" onclick="
													$('#div_Biography').slideUp('slow', function() {ldelim}
														$('#div_PrivacySettings').slideToggle('slow');
													{rdelim});
													return false;">
												Click to Show/Hide</a>
											</div>
											<div>
												<label title="{$lang.privacy_settings_thai}">{$lang.profile_head.privacy_settings}</label>
											</div>
											<div class="clear"></div>
										</div>
										<div id="div_PrivacySettings" class="disp_none" style="padding:5px;">
											<table width="100%" border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td colspan="2">
														<p class="label txtbig" style="padding-bottom:5px;">{$lang.users.privacy_title}</p>
														<p class="txtblack"><label title="{$lang.privacy_description_thai}">{$lang.users.privacy_desc}</label></p>
													</td>
												</tr>
												{* online privacy *}
												{if $use_field.online_privacy & SB_EDIT}
													<tr>
														<td class="label" colspan="2"><label title="{$lang.privacy_online_title_thai}">{$lang.users.privacy_online_title}:</label></td>
													</tr>
													<tr>
														<td width="100"></td>
														<td>
															<table border="0" cellpadding="0" cellspacing="0">
																<tr>
																	<td>
																		<input type="checkbox" name="hide_online" id="hide_online" {if $data.hide_online==1}checked="checked"{/if} value="1" />
																	</td>
																	<td class="txtblack"> 
																		<label for="hide_online" title="{$lang.privacy_online_hide_thai}">
																			{$lang.users.privacy_online_hide}
																		</label>
																	</td>
																	<td>
																		<label title="{$lang.privacy_online_hide_thai}">
																			<img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
																		</label>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												{/if}
												{* female privacy *}
												{if $use_field.privacy_female & SB_EDIT}
													<tr>
														<td class="label" colspan="2"><label title="{$lang.privacy_female_thai}">{$lang.users.privacy_female}:</label></td>
													</tr>
													<tr>
														<td width="100"> </td>
														<td>
															<table border="0" cellpadding="0" cellspacing="0">
																<tr>
																	<td>
																		<input type="radio" onclick="SetLadiesVisibleToNone();" name="visible_lady" id="visible_to_no_ladies" {if $data.visible_lady==0}checked="checked"{/if} value="0" />
																	</td>
																	<td class="txtblack"> 
																		<label for="visible_to_no_ladies" title="{$lang.visible_to_no_ladies_thai}">
																			{$lang.users.privacy_lady_none}
																		</label>
																	</td>
																	<td>
																		<label title="{$lang.visible_to_no_ladies_thai}">
																			<img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
																		</label>
																	</td>
																</tr>
															</table>
															<table border="0" cellpadding="0" cellspacing="0">
																<tr>
																	<td>
																		<input type="radio" onclick="SetLadiesVisibleToAll();" name="visible_lady" id="visible_to_all_ladies" {if $data.visible_lady==1}checked="checked"{/if} value="1" />
																	</td>
																	<td class="txtblack"> 
																		<label for="visible_to_all_ladies" title="{$lang.visible_to_all_ladies_thai}">
																			{$lang.users.privacy_lady_all}
																		</label>
																	</td>
																	<td>
																		<label title="{$lang.visible_to_all_ladies_thai}">
																			<img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
																		</label>
																	</td>
																</tr>
															</table>
															<table border="0" cellpadding="0" cellspacing="0">
																<tr>
																	<td>
																		<input type="radio" onclick="SetLadiesVisibleToSelected();" name="visible_lady" id="visible_to_selected_ladies" {if $data.visible_lady==2}checked="checked"{/if} value="2" />
																	</td>
																	<td class="txtblack"> 
																		<label for="visible_to_selected_ladies" title="{$lang.visible_to_selected_ladies_thai}">
																			{$lang.users.privacy_lady_selected}
																		</label>
																	</td>
																	<td>
																		<label title="{$lang.visible_to_selected_ladies_thai}">
																			<img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
																		</label>
																	</td>
																</tr>
															</table>
															<div style="padding-left:30px;">
																<table cellpadding="0" cellspacing="0" {* id="tbl55" *}>
																	<tr>
																		<td>
																			<input type="checkbox" onclick="CheckSelectedOptions(this,1);" name="vis_lady_1" id="vis_lady_1" {if $data.visible_lady < 2}disabled="disabled"{/if} {if $data.vis_lady_1==1}checked="checked"{/if} value="1" />
																		</td>
																		<td class="txtblack"> 
																			<label for="vis_lady_1" title="{$lang.visible_to_trial_ladies_thai}">
																				{$lang.users.privacy_lady_trial}
																			</label>
																		</td>
																		<td>
																			<label title="{$lang.visible_to_trial_ladies_thai}">
																				<img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
																			</label>
																		</td>
																	</tr>
																</table>
																<table cellpadding="0" cellspacing="0" {* id="tbl55" *}>
																	<tr>
																		<td>
																			<input type="checkbox" onclick="CheckSelectedOptions(this,1);" name="vis_lady_2" id="vis_lady_2" {if $data.visible_lady < 2}disabled="disabled"{/if} {if $data.vis_lady_2==1}checked="checked"{/if} value="1" />
																		</td>
																		<td class="txtblack"> 
																			<label for="vis_lady_2" title="{$lang.visible_to_regular_ladies_thai}">
																				{$lang.users.privacy_lady_regular}
																			</label>
																		</td>
																		<td>
																			<label title="{$lang.visible_to_regular_ladies_thai}">
																				<img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
																			</label>
																		</td>
																	</tr>
																</table>
																<table cellpadding="0" cellspacing="0" {* id="tbl55" *}>
																	<tr>
																		<td>
																			<input type="checkbox" onclick="CheckSelectedOptions(this,1);" name="vis_lady_3" id="vis_lady_3" {if $data.visible_lady < 2}disabled="disabled"{/if} {if $data.vis_lady_3==1}checked="checked"{/if} value="1" />
																		</td>
																		<td class="txtblack"> 
																			<label for="vis_lady_3" title="{$lang.visible_to_platinum_ladies_thai}">
																				{$lang.users.privacy_lady_platinum}
																			</label>
																		</td>
																		<td>
																			<label title="{$lang.visible_to_platinum_ladies_thai}">
																				<img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
																			</label>
																		</td>
																	</tr>
																</table>
															</div>
														</td>
													</tr>
												{/if}
												{* male privacy *}
												{if $use_field.privacy_male & SB_EDIT}
													<tr>
														<td class="label" colspan="2"><label title="{$lang.privacy_male_thai}">{$lang.users.privacy_male}:</label></td>
													</tr>
													<tr>
														<td width="100"></td>
														<td>
															<table border="0" cellpadding="0" cellspacing="0">
																<tr>
																	<td>
																		<input type="radio" onclick="SetGentsVisibleToNone();" name="visible_guy" id="visible_to_no_guys" {if $data.visible_guy==0}checked="checked"{/if} value="0" />
																	</td>
																	<td class="txtblack"> 
																		<label for="visible_to_no_guys" title="{$lang.visible_to_no_guys_thai}">
																			{$lang.users.privacy_guy_none}
																		</label>
																	</td>
																	<td>
																		<label title="{$lang.visible_to_no_guys_thai}">
																			<img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
																		</label>
																	</td>
																</tr>
															</table>
															<table border="0" cellpadding="0" cellspacing="0">
																<tr>
																	<td>
																		<input type="radio" onclick="SetGentsVisibleToAll();" name="visible_guy" id="visible_to_all_guys" {if $data.visible_guy==1}checked="checked"{/if} value="1" />
																	</td>
																	<td class="txtblack">
																		<label for="visible_to_all_guys" title="{$lang.visible_to_all_guys_thai}">
																			{$lang.users.privacy_guy_all}
																		</label>
																	</td>
																	<td>
																		<label title="{$lang.visible_to_all_guys_thai}">
																			<img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
																		</label>
																	</td>
																</tr>
															</table>
															<table border="0" cellpadding="0" cellspacing="0">
																<tr>
																	<td>
																		<input type="radio" onclick="SetGentsVisibleToSelected();" name="visible_guy" id="visible_to_selected_guys" {if $data.visible_guy==2}checked="checked"{/if} value="2" />
																	</td>
																	<td class="txtblack"> 
																		<label for="visible_to_selected_guys" title="{$lang.visible_to_selected_guys_thai}">
																			{$lang.users.privacy_guy_selected}
																		</label>
																	</td>
																	<td>
																		<label title="{$lang.visible_to_selected_guys_thai}">
																			<img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
																		</label>
																	</td>
																</tr>
															</table>
															<div style="padding-left:30px;">
																<table cellpadding="0" cellspacing="0">
																	<tr>
																		<td>
																			<input type="checkbox" onclick="CheckSelectedOptions(this,2);" name="vis_guy_1" id="vis_guy_1" {if $data.visible_guy < 2}disabled="disabled"{/if} {if $data.vis_guy_1==1}checked="checked"{/if} value="1" />
																		</td>
																		<td class="txtblack"> 
																			<label for="vis_guy_1" title="{$lang.visible_to_trial_guys_thai}">
																				{$lang.users.privacy_guy_trial}
																			</label>
																		</td>
																		<td>
																			<label title="{$lang.visible_to_trial_guys_thai}">
																				<img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
																			</label>
																		</td>
																	</tr>
																</table>
																<table cellpadding="0" cellspacing="0">
																	<tr>
																		<td>
																			<input type="checkbox" onclick="CheckSelectedOptions(this,2);" name="vis_guy_2" id="vis_guy_2" {if $data.visible_guy < 2}disabled="disabled"{/if} {if $data.vis_guy_2==1}checked="checked"{/if} value="1" />
																		</td>
																		<td class="txtblack"> 
																			<label for="vis_guy_2" title="{$lang.visible_to_regular_guys_thai}">
																				{$lang.users.privacy_guy_regular}
																			</label>
																		</td>
																		<td>
																			<label title="{$lang.visible_to_trial_guys_thai}">
																				<img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
																			</label>
																		</td>
																	</tr>
																</table>
																<table cellpadding="0" cellspacing="0">
																	<tr>
																		<td>
																			<input type="checkbox" onclick="CheckSelectedOptions(this,2);" name="vis_guy_3" id="vis_guy_3" {if $data.visible_guy < 2}disabled="disabled"{/if} {if $data.vis_guy_3==1}checked="checked"{/if} value="1" />
																		</td>
																		<td class="txtblack"> 
																			<label for="vis_guy_3" title="{$lang.visible_to_platinum_guys_thai}">
																				{$lang.users.privacy_guy_platinum}
																			</label>
																		</td>
																		<td>
																			<label title="{$lang.visible_to_platinum_guys_thai}">
																				<img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
																			</label>
																		</td>
																	</tr>
																</table>
																<table cellpadding="0" cellspacing="0">
																	<tr>
																		<td>
																			<input type="checkbox" onclick="CheckSelectedOptions(this,2);" name="vis_guy_4" id="vis_guy_4" {if $data.visible_guy < 2}disabled="disabled"{/if} {if $data.vis_guy_4==1}checked="checked"{/if} value="1" />
																		</td>
																		<td class="txtblack"> 
																			<label for="vis_guy_4" title="{$lang.visible_to_elite_guys_thai}">
																				{$lang.users.privacy_guy_elite}
																			</label>
																		</td>
																		<td>
																			<label title="{$lang.visible_to_elite_guys_thai}">
																				<img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
																			</label>
																		</td>
																	</tr>
																</table>
															</div>
														</td>
													</tr>
												{/if}
												{* promotion *}
												{if $use_field.promotion & SB_EDIT}
													<tr>
														<td></td>
													</tr>
													<tr>
														<td class="label" colspan="2"><label title="{$lang.privacy_promotion_thai}">{$lang.users.privacy_promo}:</label></td>
													</tr>
													<tr>
														<td width="100"></td>
														<td>
															{if $use_field.promote_no & SB_EDIT}
																<table border="0" cellpadding="0" cellspacing="0">
																	<tr>
																		<td>
																			<input type="checkbox" onclick="CheckPromotionOptions(this);" name="promotion_1" id="promotion_1" {if $data.promotion_1==1}checked="checked"{/if} value="1" />
																		</td>
																		<td class="txtblack"> 
																			<label for="promotion_1" title="{$lang.promote_no_thai}">
																				{$lang.users.promote_no}
																			</label>
																		</td>
																		<td>
																			<label title="{$lang.promote_no_thai}">
																				<img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
																			</label>
																		</td>
																	</tr>
																</table>
															{/if}
															{if $use_field.promote_within & SB_EDIT}
																<table border="0" cellpadding="0" cellspacing="0">
																	<tr>
																		<td>
																			<input type="checkbox" onclick="CheckPromotionOptions(this);" name="promotion_2" id="promotion_2" {if $data.promotion_2==1}checked="checked"{/if} value="1" />
																		</td>
																		<td class="txtblack"> 
																			<label for="promotion_2" title="{$lang.promote_within_thai}">
																				{$lang.users.promote_within}
																			</label>
																		</td>
																		<td>
																			<label title="{$lang.promote_within_thai}">
																				<img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
																			</label>
																		</td>
																	</tr>
																</table>
															{/if}
															{if $use_field.promote_prospective & SB_EDIT}
																<table border="0" cellpadding="0" cellspacing="0">
																	<tr>
																		<td>
																			<input type="checkbox" onclick="CheckPromotionOptions(this);" name="promotion_3" id="promotion_3" {if $data.promotion_3==1}checked="checked"{/if} value="1" />
																		</td>
																		<td class="txtblack"> 
																			<label for="promotion_3" title="{$lang.promote_prospective_thai}">
																				{$lang.users.promote_prospective}
																			</label>
																		</td>
																		<td>
																			<label title="{$lang.promote_prospective_thai}">
																				<img src="{$site_root}{$template_root}/images/qa_h_ask_q.gif" alt="" class="img_qmark" />
																			</label>
																		</td>
																	</tr>
																</table>
															{/if}
														</td>
													</tr>
												{/if}
											</table>
										</div>
									</div>
								{/if}
								{* BIOGRAPHY *}
								{if $use_field.biography & SB_EDIT}
									<br>
									<div class="block_blue" style="overflow: hidden;">
										<div class="titlebg" style="margin-top:0px;">
											<div class="top_showhide">
												<a href="#" onclick="
													$('#div_PrivacySettings').slideUp('slow', function() {ldelim}
														$('#div_Biography').slideToggle('slow');
													{rdelim});
													return false;">
												Click to Show/Hide</a>
											</div>
											<div>
												<label title="{$lang.biography_thai}">{$lang.profile_head.biography}</label>
											</div>
											<div class="clear"></div>
										</div>
										<div id="div_Biography" class="disp_none" style="padding:5px;">
											<table width="100%" border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td class="col1 label">
														<label title="{$lang.about_me_thai}">
															{if $err_field.about_me}<span class="error">{/if}
															{$lang.users.about_me}
															{if $err_field.about_me}</span>{/if}
															{if $mandatory.about_me & SB_EDIT} <span class="mandatory">*</span>{/if}:
														</label>
													</td>
													<td>
														<textarea name="about_me" rows="5" cols="5" style="width: 450px; height:60px;">{$data.about_me}</textarea>
													</td>
												</tr>
												<tr>
													<td class="col1 label">
														<label title="{$lang.what_i_do_thai}">
															{if $err_field.what_i_do}<span class="error">{/if}
															{$lang.users.what_i_do}
															{if $err_field.what_i_do}</span>{/if}
															{if $mandatory.what_i_do & SB_EDIT} <span class="mandatory">*</span>{/if}:
														</label>
													</td>
													<td>
														<textarea name="what_i_do" rows="5" cols="50" style="width:450px; height:60px;">{$data.what_i_do}</textarea>
													</td>
												</tr>
												<tr>
													<td class="col1 label">
														<label title="{$lang.my_idea_thai}">
															{if $err_field.my_idea}<span class="error">{/if}
															{$lang.users.my_idea}
															{if $err_field.my_idea}</span>{/if}
															{if $mandatory.my_idea & SB_EDIT} <span class="mandatory">*</span>{/if}:
														</label>
													</td>
													<td>
														<textarea name="my_idea" rows="5" cols="50" style="width:450px; height:60px;">{$data.my_idea}</textarea>
													</td>
												</tr>
												<tr>
													<td class="col1 label">
														<label title="{$lang.hoping_to_find_thai}">
															{if $err_field.hoping_to_find}<span class="error">{/if}
															{$lang.users.hoping_to_find}
															{if $err_field.hoping_to_find}</span>{/if}
															{if $mandatory.hoping_to_find & SB_EDIT} <span class="mandatory">*</span>{/if}:
														</label>
													</td>
													<td>
														<textarea name="hoping_to_find" rows="5" cols="50" style="width:450px; height:60px;">{$data.hoping_to_find}</textarea>
													</td>
												</tr>
											</table>
										</div>
									</div>
								{/if}
								{* notification *}
								{if $use_field.notification & SB_EDIT}
									<div class="txtblack">
										<input type="checkbox" name="use_notification" value="1" {if $data.root == 1}disabled="disabled"{/if}>
										{$lang.users.use_notification}
									</div>
								{else}
									<br>
								{/if}
						{elseif $form.num == 2}
							<!-- my description begin -->
							<form method="post" action="{$form.action}" name="profile">
								{$form.hiddens}
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td width="180" class="txtblue bold">
											{$lang.users.weight}:
										</td>
										<td>
											<select name="id_weight" {if $data.root == 1}disabled="disabled"{/if} style="width:270px">
												<option value="0">{$lang.home_page.select_default}</option>
												{foreach item=item from=$weight}
												<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
												{/foreach}
											</select>
										</td>
									</tr>
									<tr><td height="5"></td></tr>
									<tr>
										<td class="txtblue bold">
											{$lang.users.height}:
										</td>
										<td>
											<select name="id_height" {if $data.root eq 1}disabled="disabled"{/if} style="width:270px">
												<option value="0">{$lang.home_page.select_default}</option>
												{foreach item=item from=$height}
												<option value="{$item.id}" {if $item.sel}selected{/if}>{$item.value}</option>
												{/foreach}
											</select>
										</td>
									</tr>
									<tr><td height="5"></td></tr>
									{section name=f loop=$info}
										<tr>
											<td width="180" class="txtblue bold">
												{$info[f].name}:
												<input type="hidden" name="spr[{$info[f].num}]" value="{$info[f].id}" />
											</td>
											<td>
												<select id="info{$info[f].num}" name="info[{$info[f].num}][]" {if $info[f].type eq 2}multiple{/if} style="width:270px; {if $info[f].type eq 2}height: 80px{/if}" {if $data.root==1}disabled="disabled"{/if}>
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
										<tr><td height="5"></td></tr>
									{/section}
								</table>
						{elseif $form.num == 3}
							<!-- my notice begin -->
							<form method="post" action="{$form.action}" name="profile">
								{$form.hiddens}
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
						{elseif $form.num == 4}
							<!-- my personality begin -->
							<form method="post" action="{$form.action}" name="profile">
								{$form.hiddens}
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
						{elseif $form.num == 5}
							<!-- my portrait begin -->
							<form method="post" action="{$form.action}" name="profile">
								{$form.hiddens}
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									{section name=f loop=$portrait}
										<tr>
											<td width="120" class="txtblue bold">
												{$portrait[f].name}:
												<input type="hidden" name="port_spr[{$portrait[f].num}]" value="{$portrait[f].id}" />
											</td>
											<td>
												<select name="portrait[{$portrait[f].num}][]" style="width:200px" {if $data.root == 1} disabled="disabled"{/if} />
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
						{elseif $form.num == 6}
							<!-- my interests begin -->
							<form method="post" action="{$form.action}" name="profile">
								{$form.hiddens}
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
						{elseif $form.num == 7}
							<!-- my criteria begin -->
							<form method="post" action="{$form.action}" name="profile">
								{$form.hiddens}
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td width="160" class="txtblue bold">
											{$lang.users.weight}:
										</td>
										<td>
											<select name="id_weight" {if $data.root == 1}disabled="disabled"{/if} style="width: 150px">
												<option value="0">{$button.not_important}</option>
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
											<select name="id_height" {if $data.root == 1}disabled="disabled"{/if} style="width: 150px">
												<option value="0">{$button.not_important}</option>
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
											<select name="id_country[]" {if $data.root == 1}disabled="disabled"{/if} style="width: 150px; height: 80px" multiple>
												<option value="0" {if $default.id_country == 1}selected="selected"{/if}>{$button.not_important}</option>
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
											<select name="id_nation[]" {if $data.root eq 1}disabled{/if} style="width: 150px; height: 80px" multiple>
												<option value="0" {if $default.id_nation == 1}selected="selected"{/if}>{$button.not_important}</option>
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
											<select name="id_lang[]" {if $data.root eq 1}disabled{/if} style="width: 150px; height: 80px" multiple>
												<option value="0" {if $default.id_lang == 1}selected="selected"{/if}>{$button.not_important}</option>
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
												<select id="info{$info[f].num}" name="info[{$info[f].num}][]" {if $info[f].type eq 2}multiple{/if} style="width: 150px; {if $info[f].type eq 2}height: 80px{/if}" {if $data.root eq 1}disabled{/if}>
													<option value="0"{if $info[f].sel_all} selected="selected"{/if}>{$button.not_important}</option>
													{foreach item=item from=$info[f].opt}
													<option value="{$item.value}" {if $item.sel}selected="selected"{/if}>{$item.name}</option>
													{/foreach}
												</select>
											</td>
										</tr>
										<tr><td height="15"></td></tr>
									{/section}
								</table>
						{elseif $form.num == 8}
							<!-- desired partner interests begin -->
							<form method="post" action="{$form.action}" name="profile">
								{$form.hiddens}
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
						{elseif $form.num == 10}
							<!-- email confirmation begin -->
							<div class="txtblue">
								<label title="{$lang.application.confirm_instructions_thai}">
									{$lang.application.confirm_instructions}
								</label>
							</div>
							<br />
							<form name="frmConfirm" method="post" action="{$form.action}">
								<input type="hidden" name="sel" value="send_confirm" />
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td>
											<label title="{$lang.button_resend_confirm_email_thai}">
											<p class="basic-btn_here">
												<b>&nbsp;</b><span>
												<input type="submit" value="{$lang.button.resend_confirm_email}" />
												</span>
											</p>
											</label>
										</td>
										<td style="padding-left:10px;">
											<label title="{$lang.button_back_thai}">
											<p class="basic-btn_here">
												<b>&nbsp;</b><span>
												<input type="button" onclick="document.location.href='{$form.menu}';" value="{$button.my_application_page}">
												</span>
											</p>
											</label>
										</td>
									</tr>
								</table>
							</form>
							<!-- email confirmation end -->
						{elseif $form.num == 11}
							<!-- photo upload begin -->
							<script type="text/javascript" src="{$site_root}{$template_root}/js/upload_profile_photo.js?v=0000"></script>
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td valign="top" style="padding:10px 20px 0 0;">
										<img src="{if $data.icon_thumb_path}{$data.icon_thumb_path}{else}{$data.icon_path}{/if}" border="0" class="icon" alt="" />
									</td>
									<td align="left" width="90%">
										<div class="txtblack" style="padding: 20px 0 10px 0; font-weight: bold;">
											{if $data.icon_del_link}
												{$lang.profile.change_photo}
											{else}
												{$lang.profile.add_new_photo}
											{/if}
										</div>
										<div class="txtblack" style="padding: 0 0 8px 0; color: red;">
											<label title="{$lang.icon_upload_comment_thai}">{$data.icon_upload_comment}</label>
										</div>
										<form name="upload_form" id="upload_form" method="post" action="myprofile.php" enctype="multipart/form-data">
											<input type="hidden" name="sel" value="save_photo">
											{* <input type="hidden" name="upload_type" value="icon"> *}
											<input type="hidden" name="timestamp" value="{$data.timestamp}">
											<input type="hidden" name="token" value="{$data.token}">
											<div style="margin-bottom: 8px;">
												<input type="file" name="file_upload" id="file_upload" {if $data.root}disabled{/if}>
											</div>
											<div id="file_info" style="display:none;">
												<div id="file_name" style="margin-bottom: 8px;"></div>
												{* old fake progress bar
												<div id="progressbar">&nbsp;</div>
												*}
												<progress id="prog" value="0" style="width:300px;"></progress>
											</div>
											<table cellpadding="0" cellspacing="0">
												<tr>
													<td style="padding-right:7px;">
														<label title="{$lang.button_upload_thai}">
															<p class="basic-btn_here">
																<b>&nbsp;</b><span>
																<input type="submit" id="submit" value="{$button.upload}" />
																</span>
															</p>
														</label>
													</td>
													{if $data.icon_del_link}
														<td style="padding-right:7px;">
															<label title="{$lang.button_delete_thai}">
																<p class="basic-btn_here">
																	<b>&nbsp;</b><span>
																	<input type="button" onclick="window.location.href='{$data.icon_del_link}';" value="{$button.delete}" />
																	</span>
																</p>
															</label>
														</td>
													{/if}
													<td>
														<label title="{$lang.button_back_thai}">
															<p class="basic-btn_here">
																<b>&nbsp;</b><span>
																<input type="button" onclick="window.location.href='{$form.menu}';" value="{$button.my_application_page}">
																</span>
															</p>
														</label>
													</td>
												</tr>
											</table>
											{if $auth.is_applicant}
												<div style="padding-top:25px;">
													<label title="{$lang.button_next_step}">
														<p class="basic-btn_here">
															<b>&nbsp;</b><span>
															<input type="button" onclick="window.location.href='myprofile.php';" value="&nbsp;&nbsp;&nbsp; {$button.next_step} &nbsp;&nbsp;" />
															</span>
														</p>
													</label>
												</div>
											{/if}
										</form>
										<script type="text/javascript">
										$(function() {ldelim}
											custom_file_upload(
												{$data.max_file_size_bytes}, '{$data.max_file_size_string}',
												'{$data.file_exts}', '{$data.file_types}', 'Image Files', 'Select Photo', '{$form.session_id}');
										{rdelim});
										</script>
									</td>
								</tr>
								{if $data.photo_comment}
									<tr>
										<td height="40" colspan=2 class="txtblack" align="center">{$data.photo_comment}</td>
									</tr>
								{/if}
								{*
									UNFINISHED PILOT GROUP FEATURE, DISABLED
									{foreach key=key item=item from=$data.photos}
										<tr valign="top">
											<td width="80" valign="top">
												{if $item.view_link}<a href="#" onclick="window.open('{$item.view_link}','photo_view','menubar=0,resizable=1,scrollbars=0,status=0,toolbar=0,width=800,height=600');return false;">{/if}
												<img src="{if $item.thumb_file}{$item.thumb_file}{else}{$item.file}{/if}" border=0 alt="" class="icon">
												{if $item.view_link}</a>{/if}
											</td>
											<td align="left" class="txtblack">
												<form action="{$form.action}" method="post" enctype="multipart/form-data" name="upload_photo_{$key}">
													<input type="hidden" name="sel" value="save_9">
													<input type="hidden" name="id_file" value="{$item.id}">
													<input type="hidden" name="upload_type" value="f">
													<div style="padding-bottom: 10px;" class="txtblack bold">
														{if $item.del_link}{$lang.profile.change_photo}{else}{$lang.profile.add_new_photo}{/if}
													</div>
													<input type="file" name="upload" id="upload" {if $data.root}disabled{/if} />
													<div align="left" style="padding-top:10px">
														<textarea name="user_comment" cols="40" rows="2">{$item.user_comment}</textarea>
														{$lang.profile.comment_photo}
													</div>
													{$lang.users.allow_type}: 
													<select name="upload_allow" {if $data.root}disabled{/if}>
														<option value="1" {if $item.allow == 1}selected{/if}>{$lang.users.allow_1}</option>
														<option value="2" {if $item.allow == 2}selected{/if}>{$lang.users.allow_2}</option>
														<option value="3" {if $item.allow == 3}selected{/if}>{$lang.users.allow_3}</option>
													</select>
													<div align="left" style="padding-top:5px">
														<div class="center">
															<p class="basic-btn_here">
																<b>&nbsp;</b><span>
																<input type="button" onclick="document.upload_photo_{$key}.submit();" value="{$button.save}" />
																</span>
															</div>
														</div>
														{if $item.del_link}
															<div class="center">
																<p class="basic-btn_here">
																	<b>&nbsp;</b><span>
																	<input type="button" onclick="window.location.href='{$item.del_link}';" value="{$button.delete}" />
																	</span>
																</p>
															</div>
														{/if}
														<div class="center">
															<p class="basic-btn_here">
																<b>&nbsp;</b><span>
																<input type="button" onclick="window.location.href='{$form.menu}';" value="{$button.my_application_page}">
																</span>
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
								*}
							</table>
							<!-- photo upload end -->
						{/if}
						{if $form.num != 10 && $form.num != 11}
								<div align="center">
									<table cellpadding="0" cellspacing="0">
										<tr>
											<td>
												<p class="basic-btn_next">
													<span><input type="submit" value="{$button.save}"></span><b>&nbsp;</b>
												</p>
											</td>
											<td style="padding-left:10px;">
												<p class="basic-btn_here">
													<b>&nbsp;</b><span>
													<input type="button" onclick="window.location.href='{$form.menu}';" value="{$button.my_application_page}">
													</span>
												</p>
											</td>
										</tr>
									</table>
								</div>
							</form>
						{/if}
					</div>
				</div>
			</div>
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
{if $form.num == 1 && $use_field.couple & SB_EDIT}
showcouple();
{/if}
{literal}
function CheckValue(obj)
{
	return true;
	/* setting focus does not work in firefox, and might hinder user to click on cancel button in IE. We better make a second check on form submission. */
	if (obj.name=='login' && obj.value == '') {
		alert("{/literal}{$lang.err.invalid_login}{literal}");
		return false;
	}
	if (obj.name=='login' && (obj.value.length < 5 || obj.value.length > 20)) {
		alert("{/literal}{$lang.err.login_length}{literal}");
		return;
	}
	if (obj.name=='fname' && obj.value == '') {
		alert("{/literal}{$lang.err.invalid_name}{literal}");
		return;
	}
	if (obj.name=='sname' && obj.value == '') {
		alert("{/literal}{$lang.err.invalid_sname}{literal}");
		return;
	}
	if (obj.name=='mm_nickname' && obj.value == '') {
		alert("{/literal}{$lang.err.invalid_mm_nickname}{literal}");
		return;
	}
	if (obj.name=='email' && (obj.value == '' || (obj.value != '' && obj.value.search('^.+@.+\\..+$') == -1))) {
		alert("{/literal}{$lang.err.email_bad}{literal}");
		return;
	}
	{/literal}
	{if $voipcall_feature}
	{literal}
	if (obj.name=='phone' && (obj.value != '' && obj.value.search(/^\d{10,15}(x\d{1,5})?$/) == -1)) {
		alert("{/literal}{$lang.err.phone_bad}{literal}");
		return;
	}
	{/literal}
	{/if}
	{literal}
}

function CheckForm(f)
{
	/* javascript validation disabled */
	return true;
	if (f.login.value == '') {
		alert("{/literal}{$lang.err.invalid_login}{literal}");
		f.login.focus();
		return false;
	}
	if (f.login.value.length < 5 || f.login.value.length > 20) {
		alert("{/literal}{$lang.err.login_length}{literal}");
		f.login.focus();
		return false;
	}
	if (f.fname.value == '') {
		alert("{/literal}{$lang.err.invalid_name}{literal}");
		f.fname.focus();
		return false;
	}
	if (f.sname.value == '') {
		alert("{/literal}{$lang.err.invalid_sname}{literal}");
		f.sname.focus();
		return false;
	}
	if (f.mm_nickname.value == '') {
		alert("{/literal}{$lang.err.invalid_mm_nickname}{literal}");
		f.mm_nickname.focus();
		return false;
	}
	if (f.email.value == '' || f.email.value.search('^.+@.+\\..+$') == -1) {
		alert("{/literal}{$lang.err.email_bad}{literal}");
		f.email.focus();
		return false;
	}
	{/literal}
	{if $voipcall_feature}
	{literal}
	if (f.phone.value != '' && f.phone.value.search(/^\d{10,15}(x\d{1,5})?$/) == -1) {
		alert("{$lang.err.phone_bad}");
		f.phone.focus();
		return false;
	}
	{/literal}
	{/if}
	{literal}
}
{/literal}
</script>
{include file="$gentemplates/index_bottom.tpl"}