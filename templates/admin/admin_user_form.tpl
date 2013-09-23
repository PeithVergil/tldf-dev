{include file="$admingentemplates/admin_top.tpl"} <font class=red_header>{$header.razdel_name}</font>
<font class=red_sub_header>&nbsp;|&nbsp;{if $data.group_name}{$data.group_name}{/if}</font>
<font class=red_sub_header>&nbsp;|&nbsp;{if $form.par eq "edit"}{$header.editform}{else}{$header.add}{/if}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{if $form.par eq "edit"}{$help.users_edit}{else}{$help.users_add}{/if}</div>
<form method="post" action="{$form.action}"  enctype="multipart/form-data" name="profile">
	{$form.hiddens}
	<table id="admUserDtl" border="0" cellspacing="1" cellpadding="2" width="100%">
		<tr bgcolor="#ffffff">
			<td colspan="2" valign="middle">
				<table cellpadding="2" cellspacing="0" width="100%" >
					<tr bgcolor="#ffffff">
						<td align="right" width="25%" class="main_header_text">{if $err_field.login}<font class="error_msg">{/if}{$header.login}{if $err_field.login}</font>{/if}<font class=main_error_text>*</font>:&nbsp;</td>
						<td class="main_content_text" align="left" width="26%">
							<input type="text" name="login" value="{$data.login}" size=30 {if $data.root eq 1}disabled{/if}  style="width: 195">
						</td>
						<td class="main_header_text" align="left">
							<input type="checkbox" name="status" value="1" onchange="alertMessage(this);" {if $data.status}checked{/if} {if $data.root eq 1 || !$use_active }disabled{/if} />&nbsp;{$header.status}
						</td>
						<td>
							<input type="button" value="{$button.search_matches}" class="button" onclick="javascript: location.href='admin_user_match.php?uid={$data.id}'">
						</td>
					</tr>
					<tr bgcolor="#ffffff">
						<td align="right" width="17%" class="main_header_text">{$header.pass}{if $form.par eq 'add'}<font class=main_error_text>*</font>{/if}:&nbsp;</td>
						<td class="main_content_text" align="left">
							<input type="password" name="pass" value="{$data.pass}" size=30 {if $data.root eq 1}disabled{/if}  style="width: 195">
						</td>
						<td class="main_header_text" align="left">{if $form.par ne 'add'}
							<input type="checkbox" name="refresh" value="1" {if $data.root eq 1}disabled{/if} {if $data.refresh}checked{/if}>
							&nbsp;{$header.pass_refresh}{/if}
						</td>
					</tr>
					<tr bgcolor="#ffffff">
						<td align="right" width="17%" class="main_header_text">{$header.repass}{if $form.par eq 'add'}<font class=main_error_text>*</font>{/if}:&nbsp;</td>
						<td class="main_content_text" align="left">
							<input type="password" name="repass" value="{$data.repass}" size=30 {if $data.root eq 1}disabled{/if}  style="width: 195">
						</td>
						<td class="main_header_text" align="left">
							<input type="checkbox" name="conf" value="1" {if $data.root eq 1}disabled{/if} {if $data.conf}checked{/if}>
							&nbsp;{$header.pass_confirm}
						</td>
					</tr>
					<tr bgcolor="#ffffff">
						<td align="right" width="17%" class="main_header_text">{$header.user_group}{if $form.par eq 'add'}<font class=main_error_text>*</font>{/if}:&nbsp;</td>
						<td class="main_content_text" align="left">
							{$data.group_name}
							<input type="hidden" name="group_name" value="{$data.group_name}" />
						</td>
					</tr>
				</table>
			</td>
			<td valign="top">
				<table cellpadding="0" cellspacing="0">
					<tr>
						{* image *}
						<td align="left">
							{if $data.big_icon_path}
								<img src="../uploades/icons/{$data.big_icon_path}" alt="">
							{else}
								No image
							{/if}
							<input type="hidden" name="big_icon_path" value="{$data.big_icon_path}" />
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td colspan="3"><div class="hline"></div></td></tr>
		{* BACKGROUND CHECKS *}
		{if $data.group_id neq MM_SIGNUP_GUY_ID && $data.group_id neq MM_SIGNUP_LADY_ID}
			<tr>
				<td colspan="3" class="text_head">
					<label title="{$lang.profile_head.background_checks}">{$lang.profile_head.background_checks}</label>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="left">
					<table cellpadding="0" cellspacing="5">
						<tr>
							<td align="right" width="140">&nbsp;</td>
							<td align="center">&nbsp;{$lang.users.background_check.verified}&nbsp;</td>
							<td align="center">&nbsp;{$lang.users.background_check.pending}&nbsp;</td>
							<td align="center">&nbsp;{$lang.users.background_check.not_yet_requested}&nbsp;</td>
							<td align="center">&nbsp;{$lang.users.background_check.not_applicable}</td>
						</tr>
						{* background check *}
						<tr>
							<td align="right" class="main_header_text">
								<label title="{$lang.users.chk_background}">
									{if $err_field.chk_background}<font class="error_msg">{/if}
									{$lang.users.chk_background}
									{if $err_field.chk_background}</font>{/if}
									{if $mandatory.chk_background & $mandatory_level}<font class="mandatory">*</font>{/if}:
								</label>
							</td>
							<td align="center"><input type="radio" name="chk_background" value="VR" {if $data.chk_background=='VR'}checked="checked"{/if} /></td>
							<td align="center"><input type="radio" name="chk_background" value="PE" {if $data.chk_background=='PE'}checked="checked"{/if} /></td>
							<td align="center"><input type="radio" name="chk_background" value="NR" {if $data.chk_background=='NR'}checked="checked"{/if} /></td>
							<td align="center"><input type="radio" name="chk_background" value="NA" {if $data.chk_background=='NA'}checked="checked"{/if} /></td>
						</tr>
						{* marital status check *}
						<tr>
							<td align="right" class="main_header_text">
								<label title="{$lang.users.chk_marital_status}">
									{if $err_field.chk_marital_status}<font class="error_msg">{/if}
									{$lang.users.chk_marital_status}
									{if $err_field.chk_marital_status}</font>{/if}
									{if $mandatory.chk_marital_status & $mandatory_level}<font class="mandatory">*</font>{/if}:
								</label>
							</td>
							<td align="center"><input type="radio" name="chk_marital_status" value="VR" {if $data.chk_marital_status=='VR'}checked="checked"{/if} /></td>
							<td align="center"><input type="radio" name="chk_marital_status" value="PE" {if $data.chk_marital_status=='PE'}checked="checked"{/if} /></td>
							<td align="center"><input type="radio" name="chk_marital_status" value="NR" {if $data.chk_marital_status=='NR'}checked="checked"{/if} /></td>
							<td align="center"><input type="radio" name="chk_marital_status" value="NA" {if $data.chk_marital_status=='NA'}checked="checked"{/if} /></td>
						</tr>
						{* work history check *}
						<tr>
							<td align="right" class="main_header_text">
								<label title="{$lang.users.chk_work_history}">
									{if $err_field.chk_work_history}<font class="error_msg">{/if}
									{$lang.users.chk_work_history}
									{if $err_field.chk_work_history}</font>{/if}
									{if $mandatory.chk_work_history & $mandatory_level}<font class="mandatory">*</font>{/if}:
								</label>
							</td>
							<td align="center"><input type="radio" name="chk_work_history" value="VR" {if $data.chk_work_history=='VR'}checked="checked"{/if} /></td>
							<td align="center"><input type="radio" name="chk_work_history" value="PE" {if $data.chk_work_history=='PE'}checked="checked"{/if} /></td>
							<td align="center"><input type="radio" name="chk_work_history" value="NR" {if $data.chk_work_history=='NR'}checked="checked"{/if} /></td>
							<td align="center"><input type="radio" name="chk_work_history" value="NA" {if $data.chk_work_history=='NA'}checked="checked"{/if} /></td>
						</tr>
						{* interview photo check *}
						<tr>
							<td align="right" class="main_header_text">
								<label title="{$lang.users.chk_interview_photo}">
									{if $err_field.chk_interview_photo}<font class="error_msg">{/if}
									{$lang.users.chk_interview_photo}
									{if $err_field.chk_interview_photo}</font>{/if}
									{if $mandatory.chk_interview_photo & $mandatory_level}<font class="mandatory">*</font>{/if}:
								</label>
							</td>
							<td align="center"><input type="radio" name="chk_interview_photo" value="VR" {if $data.chk_interview_photo=='VR'}checked="checked"{/if} /></td>
							<td align="center"><input type="radio" name="chk_interview_photo" value="PE" {if $data.chk_interview_photo=='PE'}checked="checked"{/if} /></td>
							<td align="center"><input type="radio" name="chk_interview_photo" value="NR" {if $data.chk_interview_photo=='NR'}checked="checked"{/if} /></td>
							<td align="center"><input type="radio" name="chk_interview_photo" value="NA" {if $data.chk_interview_photo=='NA'}checked="checked"{/if} /></td>
						</tr>
						{if $data.is_applied && $data.group_id != MM_INACT_REGULAR_GUY_ID && $data.group_id != MM_INACT_REGULAR_LADY_ID && $data.group_id != MM_INACT_PLATINUM_GUY_ID && $data.group_id != MM_INACT_PLATINUM_LADY_ID && $data.group_id != MM_INACT_ELITE_GUY_ID}
							{* date *}
							<tr>
								<td align="right" class="main_header_text">
									<label title="{$lang.users.chk_date}">
										{if $err_field.chk_date}<font class="error_msg">{/if}
										{$lang.users.chk_date}
										{if $err_field.chk_date}</font>{/if}
										{if $mandatory.chk_date & $mandatory_level}<font class="mandatory">*</font>{/if}:
									</label>
								</td>
								<td colspan="4">
									<select name="chk_day">
										<option value="0">--</option>
										{section name=d loop=$chk_day}
											<option value="{$chk_day[d].value}" {if $chk_day[d].sel}selected{/if}>{$chk_day[d].value}</option>
										{/section}
									</select>
									&nbsp;
									<select name="chk_month">
										<option value="0">--</option>
										{section name=m loop=$chk_month}
											<option value="{$chk_month[m].value}" {if $chk_month[m].sel}selected{/if}>{$chk_month[m].name}</option>
										{/section}
									</select>
									&nbsp;
									<select name="chk_year">
										<option value="0">--</option>
										{section name=y loop=$chk_year}
											<option value="{$chk_year[y].value}" {if $chk_year[y].sel}selected{/if}>{$chk_year[y].value}</option>
										{/section}
									</select>
								</td>
							</tr>
							{* staff *}
							<tr>
								<td align="right" class="main_header_text">
									<label title="{$lang.users.chk_staff}">
										{if $err_field.chk_staff}<font class="error_msg">{/if}
										{$lang.users.chk_staff}
										{if $err_field.chk_staff}</font>{/if}
										{if $mandatory.chk_staff & $mandatory_level}<font class="mandatory">*</font>{/if}:
									</label>
								</td>
								<td colspan="4">
									<input type="text" name="chk_staff" value="{$data.chk_staff}" style="width:195px" />
								</td>
							</tr>
							{* comment *}
							<tr>
								<td align="right" class="main_header_text">
									<label title="{$lang.users.chk_comment}"> 
										{if $err_field.chk_comment}<font class="error_msg">{/if}
										{$lang.users.chk_comment}
										{if $err_field.chk_comment}</font>{/if}
										{if $mandatory.chk_comment & $mandatory_level}<font class="mandatory">*</font>{/if}:
									</label>
								</td>
								<td colspan="4">
									<textarea style="width:400px;height:70px;" name="chk_comment" >{$data.chk_comment}</textarea>
								</td>
							</tr>
							{* platinum verified *}
							<tr>
								<td align="right" class="main_header_text">
									<label title="{$lang.users.platinum_verified}"> 
										{if $err_field.platinum_verified}<font class="error_msg">{/if}
										{$lang.users.platinum_verified}
										{if $err_field.platinum_verified}</font>{/if}
										{if $mandatory.platinum_verified & $mandatory_level}<font class="mandatory">*</font>{/if}:
									</label>
								</td>
								{*changing_naren*}
								{*
								{if !in_array($data.group_id, $data.platinumArr)}
								*}
									<td colspan="3">
										<input type="radio" name="platinum_verified" value="1" {if $data.platinum_verified == 1}checked{/if} id="plat_appr" />
										<label for="plat_appr">Verified</label>
										<font size="-1" color="#ff0000">&nbsp;(Must approve the payment before Verifying user.)</font>
									</td>
								</tr>
								<tr>
									<td>
										&nbsp;
									</td>
									<td colspan="2">
										<input type="radio" name="platinum_verified" id="plat_rej" value="2" {if $data.platinum_verified == 2}checked{/if} /><label for="plat_rej">Rejected</label>
									</td>
								{*
								{else}
									<td colspan="3">
										<input type="radio" {if $data.platinum_verified}checked{/if} disabled="disabled" id="plat_appr"/>
										<label for="plat_appr">Verified</label>
										<input type="hidden" name="platinum_verified" value="{if $data.platinum_verified}1{/if}" />
									</td>
								{/if}
								*}
							</tr>
						{else}
							<tr>
								<td colspan="3">
									<input type="hidden" name="chk_day" value="{$data.chk_day}" />
									<input type="hidden" name="chk_month" value="{$data.chk_month}" />
									<input type="hidden" name="chk_year" value="{$data.chk_year}" />
									<input type="hidden" name="chk_staff" value="{$data.chk_staff}" />
									<input type="hidden" name="chk_comment" value="{$data.chk_comment}" />
									<input type="hidden" name="platinum_verified" value="{$data.platinum_verified}" />
								</td>
							</tr>
						{/if}
					</table>
				</td>
			</tr>
			<tr><td colspan="3"><div class="hline"></div></td></tr>
		{else}
			<tr>
				<td colspan="3">
					<input type="hidden" name="chk_background" value="{$data.chk_background}" />
					<input type="hidden" name="chk_marital_status" value="{$data.chk_marital_status}" />
					<input type="hidden" name="chk_work_history" value="{$data.chk_work_history}" />
					<input type="hidden" name="chk_interview_photo" value="{$data.chk_interview_photo}" />
					<input type="hidden" name="chk_day" value="{$data.chk_day}" />
					<input type="hidden" name="chk_month" value="{$data.chk_month}" />
					<input type="hidden" name="chk_year" value="{$data.chk_year}" />
					<input type="hidden" name="chk_staff" value="{$data.chk_staff}" />
					<input type="hidden" name="chk_comment" value="{$data.chk_comment}" />
					<input type="hidden" name="platinum_verified" value="{$data.platinum_verified}" />
				</td>
			</tr>
		{/if}
		
		{* PERSONAL INFO *}
		<tr>
			<td colspan="3" class="text_head">
				<label title="{$lang.personal_info_thai}">{$lang.profile_head.personal_info}</label>
			</td>
		</tr>
        {* first name *}
		{if $use_field.fname & SB_EDIT}
			<tr>
				<td align="right" width="20%" class="main_header_text">
					<label title="{$lang.first_name_thai}">
						{if $err_field.fname}<font class="error_msg">{/if}
						{$lang.users.fname}
						{if $err_field.fname}</font>{/if}
						{if $mandatory.fname & $mandatory_level}<font class="mandatory">*</font>{/if}:
					</label>
				</td>
				<td>
					<input type="text" name="fname" value="{$data.fname}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
				</td>
			</tr>
		{/if}
		{* last name *}
		{if $use_field.sname & SB_EDIT}
			<tr>
				<td align="right" class="main_header_text">
					<label title="{$lang.last_name_thai}">
						{if $err_field.sname}<font class="error_msg">{/if}
						{$lang.users.sname}
						{if $err_field.sname}</font>{/if}
						{if $mandatory.sname & $mandatory_level}<font class="mandatory">*</font>{/if}:
					</label>
				</td>
				<td>
					<input type="text" name="sname" value="{$data.sname}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
				</td>
			</tr>
		{/if}
		{* nick name *}
		{if $use_field.mm_nickname & SB_EDIT}
			<tr>
				<td align="right" class="main_header_text">
					<label title="{$lang.nickname_thai}">
						{if $err_field.mm_nickname}<font class="error_msg">{/if}
						{$lang.users.mm_nickname}
						{if $err_field.mm_nickname}</font>{/if}
						{if $mandatory.mm_nickname & $mandatory_level}<font class="mandatory">*</font>{/if}:
					</label>
				</td>
				<td>
					<input type="text" name="mm_nickname" value="{$data.mm_nickname}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
				</td>
			</tr>
		{/if}
		{* gender *}
		{if $use_field.gender & SB_EDIT}
			<tr>
				<td align="right" class="main_header_text">
					<label title="{$lang.gender_thai}">
						{if $err_field.gender}<font class="error_msg">{/if}
						{$lang.users.gender}
						{if $err_field.gender}</font>{/if}
						{if $mandatory.gender & $mandatory_level}<font class="mandatory">*</font>{/if}:
					</label>
				</td>
				<td>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							{foreach item=item from=$gender}
								{if $item.sel}
									<td><input type="radio" name="gender" value="{$item.id}" {if $item.sel}checked="checked"{/if} /></td>
									<td style="padding-right:15px;" class="text">&nbsp;{$item.name}</td>
								{/if}
							{/foreach}
						</tr>
					</table>
				</td>
			</tr>
		{/if}
		{if $use_field.couple & SB_EDIT}
			<tr>
				<td align="right" class="main_header_text">
					{if $err_field.couple}<font class="error_msg">{/if}
					{$lang.users.single_couple}
					{if $err_field.couple}</font>{/if}
					{if $mandatory.couple & $mandatory_level}&nbsp;<font class="mandatory">*</font>{/if}:
				</td>
				<td>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td>
								<input type="radio" onclick=showcouple(); name="couple" value="0" {if ! $data.couple}checked="checked"{/if} />
							</td>
							<td style="padding-right:15px;" class="text">&nbsp;{$lang.users.single}</td>
							<td>
								<input type="radio" onclick=showcouple(); name="couple" value="1" {if $data.couple}checked="checked"{/if} />
							</td>
							<td style="padding-right:15px;" class="text">&nbsp;{$lang.users.couple}</td>
							<td>
								<div id="couple_user_form" style="display:none; position:relative; top:0px"> {if $data.couple_user}
									<input type="hidden" value="{$data.couple_user}" name="couple_user" />
									{$lang.users.couple_link}:&nbsp;<br>
									<a href="{$data.couple_link}" target="_blank"><b>{$data.couple_login}</b></a>&nbsp;{$data.couple_gender}&nbsp;{$data.couple_age} {$lang.home_page.ans}<br/>
									<input type="checkbox" value="1" name="couple_delete" />
									&nbsp;{$lang.users.couple_delete}<br/>
									{if ! $data.couple_accept}{$lang.users.couple_accept}{/if}
									{else}
									<table cellspacing="0" cellpadding="0" border="0">
										<tr>
											<td class="text">{$lang.users.couple_login}:&nbsp;</td>
											<td>
												<input type="text" name="couple_login" value="{$data.couple_login}" style="width: 150px" />
												&nbsp;&nbsp;&nbsp;</td>
											<td><a href="quick_search.php">{$lang.button.search}</a></td>
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
				<td align="right" class="main_header_text">
					<label title="{$lang.marital_status_thai}">
						{if $err_field.mm_marital_status}<font class="error_msg">{/if}
						{$lang.users.mm_marital_status}
						{if $err_field.mm_marital_status}</font>{/if}
						{if $mandatory.mm_marital_status & $mandatory_level}<font class="mandatory">*</font>{/if}:
					</label>
				</td>
				<td>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							{foreach item=item from=$mm_marital_status}
								<td><input type="radio" name="mm_marital_status" value="{$item.id}" {if $item.sel}checked="checked"{/if} /></td>
								<td style="padding-right:15px;" class="text">&nbsp;{$item.value}</td>
							{/foreach}
						</tr>
					</table>
				</td>
			</tr>
		{/if}
		{* date of birth *}
		{if $use_field.date_birthday & SB_EDIT}
			<tr>
				<td align="right" class="main_header_text">
					<label title="{$lang.birthday_thai}">
						{if $err_field.date_birthday}<font class="error_msg">{/if}
						{$lang.users.date_birthday}
						{if $err_field.date_birthday}</font>{/if}
						{if $mandatory.date_birthday & $mandatory_level}<font class="mandatory">*</font>{/if}:
					</label>
				</td>
				<td>
					<select name="b_day" {if $data.root}disabled="disabled"{/if}>
						{foreach item=item from=$day}
							<option value="{$item.value}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
						{/foreach}
					</select>
					&nbsp;
					<select name="b_month" {if $data.root}disabled="disabled"{/if}>
						{foreach item=item from=$month}
							<option value="{$item.value}" {if $item.sel}selected="selected"{/if}>{$item.name}</option>
						{/foreach}
					</select>
					&nbsp;
					<select name="b_year" {if $data.root}disabled="disabled"{/if}>
						{foreach item=item from=$year}
							<option value="{$item.value}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
						{/foreach}
					</select>
				</td>
			</tr>
		{/if}
		{* added: place of birth *}
		{if $use_field.mm_place_of_birth & SB_EDIT}
			<tr>
				<td align="right" class="main_header_text">
					<label title="{$lang.place_of_birth_thai}">
						{if $err_field.mm_place_of_birth}<font class="error_msg">{/if}
						{$lang.users.mm_place_of_birth}
						{if $err_field.mm_place_of_birth}</font>{/if}
						{if $mandatory.mm_place_of_birth & $mandatory_level}<font class="mandatory">*</font>{/if}:
					</label>
				</td>
				<td>
					<input type="text" name="mm_place_of_birth" value="{$data.mm_place_of_birth}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
				</td>
			</tr>
		{/if}
		{* nationality *}
		{if $use_field.id_nationality && SB_EDIT}
			<tr>
				<td align="right" class="main_header_text">
					<label title="{$lang.nationality_thai}">
						{if $err_field.id_nationality}<font class="error_msg">{/if}
						{$lang.users.nationality}
						{if $err_field.id_nationality}</font>{/if}
						{if $mandatory.id_nationality & $mandatory_level}<font class="mandatory">*</font>{/if}:
					</label>
				</td>
				<td>
					<select name="id_nationality" {if $data.root == 1}disabled="disabled"{/if} style="width:195px">
						<option value="0">{$lang.home_page.select_default}</option>
						{foreach item=item from=$nation}
							<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
						{/foreach}
					</select>
				</td>
			</tr>
		{/if}
		{* added: identification number *}
		{if $use_field.mm_id_number & SB_EDIT && $data.gender == 2 || $data.is_applied}
			<tr>
				<td align="right" class="main_header_text">
					<label title="{$lang.id_number_thai}">
						{if $err_field.mm_id_number}<font class="error_msg">{/if}
						{$lang.users.mm_id_number}
						{if $err_field.mm_id_number}</font>{/if}
						{if $mandatory.mm_id_number & $mandatory_level}<font class="mandatory">*</font>{/if}:
					</label>
				</td>
				<td>
					<input type="text" name="mm_id_number" value="{$data.mm_id_number}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
				</td>
			</tr>
			<tr>
				<td align="right" class="main_header_text">
					<label title="{$lang.id_type_thai}">
						{if $err_field.mm_id_type}<font class="error_msg">{/if}
						{$lang.users.mm_id_type}
						{if $err_field.mm_id_type}</font>{/if}
						{if $mandatory.mm_id_type & $mandatory_level}<font class="mandatory">*</font>{/if}:
					</label>
				</td>
				<td>
					<input type="text" name="mm_id_type" value="{$data.mm_id_type}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
				</td>
			</tr>
		{else}
			<tr>
				<td colspan="2">
					<input type="hidden" name="mm_id_number" value="{$data.mm_id_number}" />
					<input type="hidden" name="mm_id_type" value="{$data.mm_id_type}" />
				</td>
			</tr>
		{/if}
		{* weight *}
		{if $use_field.id_weight & SB_EDIT}
			<tr>
				<td align="right" class="main_header_text">
					<label title="{$lang.users.weight}">
						{if $err_field.id_weight}<font class="error_msg">{/if}
						{$lang.users.weight}
						{if $err_field.id_weight}</font>{/if}
						{if $mandatory.id_weight & $mandatory_level}<font class="mandatory">*</font>{/if}:
					</label>
				</td>
				<td>
					<select name="id_weight" {if $data.root == 1}disabled="disabled"{/if} style="width:195px">
						<option value="0">{$lang.home_page.select_default}</option>
						{foreach item=item from=$weight}
							<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
						{/foreach}
					</select>
				</td>
			</tr>
		{/if}
		{* height *}
		{if $use_field.id_height & SB_EDIT}
			<tr>
				<td align="right" class="main_header_text">
					<label title="{$lang.users.height}">
						{if $err_field.id_height}<font class="error_msg">{/if}
						{$lang.users.height}
						{if $err_field.id_height}</font>{/if}
						{if $mandatory.id_height & $mandatory_level}<font class="mandatory">*</font>{/if}:
					</label>
				</td>
				<td>
					<select name="id_height" {if $data.root == 1}disabled="disabled"{/if} style="width:195px">
						<option value="0">{$lang.home_page.select_default}</option>
						{foreach item=item from=$height}
							<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
						{/foreach}
					</select>
				</td>
			</tr>
		{/if}
		{* personal headline *}
		{if $use_field.headline & SB_EDIT}
			<tr>
				<td align="right" class="main_header_text">
					<label title="{$lang.users.headline}">
						{if $err_field.headline}<font class="error_msg">{/if}
						{$lang.users.headline}
						{if $err_field.headline}</font>{/if}
						{if $mandatory.headline & $mandatory_level}<font class="mandatory">*</font>{/if}:
					</label>
				</td>
				<td colspan="2">
					<textarea style="width:500px; height:50px;" name="headline" {if $data.root eq 1}disabled{/if}>{$data.headline}</textarea>
				</td>
			</tr>
		{/if}
		<tr><td colspan="3"><div class="hline"></div></td></tr>
		
		{* CONTACT INFO *}
		{if $use_field.email & SB_EDIT}
			<tr>
				<td colspan="3" class="text_head">
					<label title="{$lang.contact_info_thai}">{$lang.profile_head.contact_info}</label>
				</td>
			</tr>
		{/if}
		{* email *}
		{if $use_field.email & SB_EDIT}
			<tr>
				<td align="right" class="main_header_text">
					<label title="{$lang.email_thai}">
						{if $err_field.email}<font class="error_msg">{/if}
						{$lang.users.email}
						{if $err_field.email}</font>{/if}
						{if $mandatory.email & $mandatory_level}<font class="mandatory">*</font>{/if}:
					</label>
				</td>
				<td>
					<input type="text" name="email" value="{$data.email}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
				</td>
			</tr>
		{/if}
		{* added: contact phone number *}
		{if $use_field.mm_contact_phone_number & SB_EDIT || $data.is_applied}
			<tr>
				<td align="right" class="main_header_text">
					<label title="{$lang.contact_phone_thai}">
						{if $err_field.mm_contact_phone_number}<font class="error_msg">{/if}
						{$lang.users.mm_contact_phone_number}
						{if $err_field.mm_contact_phone_number}</font>{/if}
						{if $mandatory.mm_contact_phone_number & $mandatory_level}<font class="mandatory">*</font>{/if}:
					</label>
				</td>
				<td>
					<input type="text" name="mm_contact_phone_number" value="{$data.mm_contact_phone_number}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
				</td>
			</tr>
		{/if}
		{* added: contact mobile number *}
		{if $use_field.mm_contact_mobile_number & SB_EDIT || $data.is_applied}
			<tr>
				<td align="right" class="main_header_text">
					<label title="{$lang.contact_mobile_thai}">
						{if $err_field.mm_contact_mobile_number}<font class="error_msg">{/if}
						{$lang.users.mm_contact_mobile_number}
						{if $err_field.mm_contact_mobile_number}</font>{/if}
						{if $mandatory.mm_contact_mobile_number & $mandatory_level}<font class="mandatory">*</font>{/if}:
					</label>
				</td>
				<td>
					<input type="text" name="mm_contact_mobile_number" value="{$data.mm_contact_mobile_number}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
				</td>
			</tr>
		{/if}
		<tr><td colspan="3"><div class="hline"></div></td></tr>
		
		{* BEST CALL TIME *}
		<tr>
			<td colspan="3" class="text_head">
				<label title="{$lang.best_call_time_thai}">{$lang.profile_head.best_call_time}</label>
			</td>
		</tr>
		{* added: best call time weekdays *}
		{if $use_field.best_call_time & SB_EDIT || $data.is_applied}
			<tr>
				<td align="right" class="main_header_text">
					<label title="Weekdays">
						{if $err_field.mm_best_call_time_weekdays}<font class="error_msg">{/if}
						{$lang.apply_platinum.best_call_time_weekdays}
						{if $err_field.mm_best_call_time_weekdays}</font>{/if}
						{if $mandatory.mm_best_call_time_weekdays & $mandatory_level}<font class="mandatory">*</font>{/if}:
					</label>
				</td>
				<td>
					<input type="text" name="mm_best_call_time_weekdays" value="{$data.mm_best_call_time_weekdays}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
				</td>
			</tr>
		{/if}
		{* added: best call time saturday *}
		{if $use_field.best_call_time & SB_EDIT || $data.is_applied}
			<tr>
				<td align="right" class="main_header_text">
					<label title="Saturdays">
						{if $err_field.mm_best_call_time_saturdays}<font class="error_msg">{/if}
						{$lang.apply_platinum.best_call_time_saturdays}
						{if $err_field.mm_best_call_time_saturdays}</font>{/if}
						{if $mandatory.mm_best_call_time_saturdays & $mandatory_level}<font class="mandatory">*</font>{/if}:
					</label>
				</td>
				<td>
					<input type="text" name="mm_best_call_time_saturdays" value="{$data.mm_best_call_time_saturdays}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
				</td>
			</tr>
		{/if}
		{* added: best call time sundays *}
		{if $use_field.best_call_time & SB_EDIT || $data.is_applied}
			<tr>
				<td align="right" class="main_header_text">
					<label title="Sundays">
						{if $err_field.mm_best_call_time_sundays}<font class="error_msg">{/if}
						{$lang.apply_platinum.best_call_time_sundays}
						{if $err_field.mm_best_call_time_sundays}</font>{/if}
						{if $mandatory.mm_best_call_time_sundays & $mandatory_level}<font class="mandatory">*</font>{/if}:
					</label>
				</td>
				<td>
					<input type="text" name="mm_best_call_time_sundays" value="{$data.mm_best_call_time_sundays}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
				</td>
			</tr>
		{/if}
		<tr>
			<td align="right" class="main_header_text" style="display:none;">
				<label title="Comments">
					{if $err_field.mm_platinum_submit_comment}<font class="error_msg">{/if}
					{$lang.apply_platinum.platinum_submit_comment}
					{if $err_field.mm_platinum_submit_comment}</font>{/if}
					{if $mandatory.mm_platinum_submit_comment & $mandatory_level}<font class="mandatory">*</font>{/if}:
				</label>
			</td>
			<td>
				<input type="hidden" name="mm_platinum_submit_comment" value="{$data.mm_platinum_submit_comment}" />
				<!--
				<input type="text" name="mm_platinum_submit_comment" value="{$data.mm_platinum_submit_comment}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
				-->
			</td>
		</tr>
		{* voip special *}
		{if $voipcall_feature == 1}
			{if $use_field.phone & SB_EDIT}
				<tr>
					<td align="right" class="main_header_text">{$lang.users.phone}:</td>
					<td height="30">
						<input type="text" name="phone" value="{$data.phone}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
					</td>
				</tr>
				<tr>
					<td colspan="2">{$lang.users.phone_notice}</td>
				</tr>
			{/if}
		{/if}
		<tr><td colspan="3"><div class="hline"></div></td></tr>
		
		{* LOOKING FOR *}
		{if $use_field.gender_search & SB_EDIT
		|| $use_field.age_min & SB_EDIT
		|| $use_field.age_max & SB_EDIT
		|| $use_field.couple_search & SB_EDIT
		|| $use_field.relationship & SB_EDIT}
			<tr>
				<td colspan="3">
					{if $data.gender_search == 1}
						<label title="{$lang.looking_for_man_thai}"><b>{$lang.profile_head.looking_for_man}</b></label>
					{else}
						<label title="{$lang.looking_for_woman_thai}"><b>{$lang.profile_head.looking_for_woman}</b></label>
					{/if}
					{*<!--
					{if !$use_field.gender_search & SB_EDIT}
						<input type="hidden" name="gender_search" value="{$data.gender_search}" />
					{/if}
					-->*}
					<input type="hidden" name="gender_search" value="{$data.gender_search}" />
				</td>
			</tr>
			{if $use_field.gender_search & SB_EDIT}
				<tr bgcolor="#ffffff">
					<td align="right" width="17%" class="main_header_text">{$header.gender_search}:&nbsp;</td>
					<td colspan="2" class="main_content_text" align="left">
						<select name="gender_search" {if $data.root eq 1}disabled{/if} style="width: 195">
							{section name=s loop=$gender}
								<option value="{$gender[s].id}" {if $gender[s].sel_search eq 1}selected{/if}>{$gender[s].name_search}</option>
							{/section}
						</select>
					</td>
				</tr>
			{/if}
			{if $use_field.id_relationship & SB_EDIT}
				<tr bgcolor="#ffffff">
					<td align="right" width="17%" class="main_header_text">{$header.for}:&nbsp;</td>
					<td colspan="2" class="main_content_text" align="left">
						<select name="relation[]" multiple style="width:195" {if $data.root eq 1}disabled{/if}>
							<option value="0" {if $relation.sel_all}selected{/if}>{$button.all}</option>
							{html_options values=$relation.opt_value selected=$relation.opt_sel output=$relation.opt_name}
						</select>
					</td>
				</tr>
			{/if}
			<tr bgcolor="#ffffff">
				<td align="right" width="17%" class="main_header_text">{$header.age_search}:&nbsp;</td>
				<td colspan=2 class="main_content_text" align="left">
					<select name="age_min" {if $data.root eq 1}disabled{/if}>
						{section name=d loop=$age_min}
							<option value="{$age_min[d]}" {if $min_age_sel eq $age_min[d]}selected{/if}>{$age_min[d]}</option>
						{/section}
					</select>
					&nbsp;-&nbsp;
					<select name="age_max" {if $data.root eq 1}disabled{/if}>
						{section name=d loop=$age_max}
							<option value="{$age_max[d]}" {if $max_age_sel eq $age_max[d]}selected{/if}>{$age_max[d]}</option>
						{/section}
					</select>
				</td>
			</tr>
			<tr><td colspan="3"><div class="hline"></div></td></tr>
		{/if}
		
		{* ADDRESS INFO *}
		{if $use_field.id_country & SB_EDIT || $data.is_applied}
			<tr>
				<td colspan="2" class="text_head">
					<label title="{$lang.address_info_thai}">{$lang.profile_head.address_info}</label>
				</td>
				{* <td class="text_hidden">{$lang.registration.country_text}</td> *}
			</tr>
			{* country *}
			{if $use_field.id_country & SB_EDIT || $data.is_applied}
				<tr>
					<td align="right" class="main_header_text">
						<label title="{$lang.country_thai}">
							{if $err_field.id_country}<font class="error_msg">{/if}
							{$lang.users.country}
							{if $err_field.id_country}</font>{/if}
							{if $mandatory.id_country & $mandatory_level}<font class="mandatory">*</font>{/if}:
						</label>
					</td>
					<td>
						<select name="id_country" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" onchange="SelectRegion('mp', this.value, document.getElementById('region_div'), document.getElementById('city_div'));">
							<option value="0">{$lang.home_page.select_default}</option>
							{foreach item=item from=$country}
								<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
							{/foreach}
						</select>
					</td>
				</tr>
			{/if}
			{* region *}
			{if $use_field.id_region & SB_EDIT || $data.is_applied}
				<tr>
					<td align="right" class="main_header_text">
						<label title="{$lang.region_thai}">
							{if $err_field.id_region}<font class="error_msg">{/if}
							{$lang.users.region}
							{if $err_field.id_region}</font>{/if}
							{if $mandatory.id_region & $mandatory_level}<font class="mandatory">*</font>{/if}:
						</label>
					</td>
					<td>
						<div id="region_div">
							{if isset($region)}
								<select name="id_region" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" onchange="SelectCity('mp', this.value, document.getElementById('city_div'));">
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
			{/if}
			{* city *}
			{if $use_field.id_city & SB_EDIT || $data.is_applied}
				<tr>
					<td align="right" class="main_header_text">
						<label title="{$lang.city_thai}">
							{if $err_field.id_city}<font class="error_msg">{/if}
							{$lang.users.city}
							{if $err_field.id_city}</font>{/if}
							{if $mandatory.id_city & $mandatory_level}<font class="mandatory">*</font>{/if}:
						</label>
					</td>
					<td>
						<div id="city_div"> {if isset($city)}
							<select name="id_city" {if $data.root == 1}disabled="disabled"{/if} style="width: 195px">
								<option value="0">{$lang.home_page.select_default}</option>
								{foreach item=item from=$city}
									<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
								{/foreach}
							</select>
							{else}
								&nbsp;
							{/if}
						</div>
					</td>
				</tr>
			{/if}
			{* added: city *}
			{if $use_field.mm_city & SB_EDIT || $data.is_applied}
				<tr>
					<td align="right" class="main_header_text">
						<label title="{$lang.city_thai}">
							{if $err_field.mm_city}<font class="error_msg">{/if}
							{$lang.users.city}
							{if $err_field.mm_city}</font>{/if}
							{if $mandatory.mm_city & $mandatory_level}<font class="mandatory">*</font>{/if}:
						</label>
					</td>
					<td>
						<input type="text" name="mm_city" value="{$data.mm_city}" size="30" {if $data.root == 1}disabled="disabled"{/if} style="width: 195px" />
					</td>
				</tr>
			{/if}
			{* zipcode *}
			{if $use_field.zipcode & SB_EDIT || $data.is_applied}
				<tr>
					<td align="right" class="main_header_text">
						<label title="{$lang.zipcode_thai}">
							{if $err_field.zipcode}<font class="error_msg">{/if}
							{$lang.users.zipcode}
							{if $err_field.zipcode}</font>{/if}
							{if $mandatory.zipcode & $mandatory_level}<font class="mandatory">*</font>{/if}:
						</label>
					</td>
					<td>
						<input type="text" name="zipcode" value="{$data.zipcode}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" maxlength="{$form.zip_count}" />
					</td>
				</tr>
			{/if}
			{* added: address line 1 *}
			{if $use_field.mm_address_1 & SB_EDIT || $data.is_applied}
				<tr>
					<td align="right" class="main_header_text">
						<label title="{$lang.address_line_1_thai}">
							{if $err_field.mm_address_1}<font class="error_msg">{/if}
							{$lang.users.mm_address_1}
							{if $err_field.mm_address_1}</font>{/if}
							{if $mandatory.mm_address_1 & $mandatory_level}<font class="mandatory">*</font>{/if}:
						</label>
					</td>
					<td>
						<input type="text" name="mm_address_1" value="{$data.mm_address_1}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
					</td>
				</tr>
			{/if}
			{* added: address line 2 *}
			{if $use_field.mm_address_2 & SB_EDIT || $data.is_applied}
				<tr>
					<td align="right" class="main_header_text">
						<label title="{$lang.address_line_2_thai}">
							{if $err_field.mm_address_2}<font class="error_msg">{/if}
							{$lang.users.mm_address_2}
							{if $err_field.mm_address_2}</font>{/if}
							{if $mandatory.mm_address_2 & $mandatory_level}<font class="mandatory">*</font>{/if}:
						</label>
					</td>
					<td>
						<input type="text" name="mm_address_2" value="{$data.mm_address_2}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
					</td>
				</tr>
			{/if}
			{* added: address line 3 *}
			{if $use_field.mm_address_3 & SB_EDIT || $data.is_applied}
				<tr>
					<td align="right" class="main_header_text">
						<label title="{$lang.address_line_3_thai}">
							{if $err_field.mm_address_3}<font class="error_msg">{/if}
							{$lang.users.mm_address_3}
							{if $err_field.mm_address_3}</font>{/if}
							{if $mandatory.mm_address_3 & $mandatory_level}<font class="mandatory">*</font>{/if}:
						</label>
					</td>
					<td>
						<input type="text" name="mm_address_3" value="{$data.mm_address_3}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
					</td>
				</tr>
			{/if}
			<tr><td colspan="3"><div class="hline"></div></td></tr>
		{/if}
		
		{* LANGUAGE INFO *}
		{if $use_field.id_language_1 & SB_EDIT}
			{* delimiter *}
			<tr>
				<td colspan="3" class="text_head">
					<label title="{$lang.language_info_thai}">{$lang.profile_head.language_info}</label>
				</td>
			</tr>
			{* site language *}
			{if $use_field.site_language & SB_EDIT}
				<tr>
					<td align="right" class="main_header_text">
						{if $err_field.site_language}<font class="error_msg">{/if}
						{$lang.users.site_language}
						{if $err_field.site_language}</font>{/if}
						{if $mandatory.site_language & $mandatory_level}<font class="mandatory">*</font>{/if}:
					</td>
					<td>
						<select name="site_language" style="width:150px">
							{foreach from=$site_langs item=item}
								<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.name}</option>
							{/foreach}
						</select>
					</td>
				</tr>
			{else}
				<tr>
					<td colspan="2">
						<input type="hidden" name="site_language" value="{$data.site_language}">
					</td>
				</tr>
			{/if}
			{* language *}
			{if $use_field.id_language_1 & SB_EDIT}
				<tr>
					<td align="right" class="main_header_text">
						<label title="{$lang.language_thai}"> 
							{if $err_field.id_language_1 || $err_field.id_language_2 || $err_field.id_language_3}<font class="error_msg">{/if}
							{$lang.users.language}
							{if $err_field.id_language_1 || $err_field.id_language_2 || $err_field.id_language_3}</font>{/if}
							{if $mandatory.id_language_1 & $mandatory_level}<font class="mandatory">*</font>{/if}:
						</label>
					</td>
					<td colspan="2">
						<select name="id_language_1" {if $data.root == 1}disabled="disabled"{/if} style="width:150px">
							<option value="0">{$lang.home_page.select_default}</option>
							{foreach item=item from=$lang_sel}
								<option value="{$item.id}" {if $item.sel1}selected="selected"{/if}>{$item.value}</option>
							{/foreach}
						</select>
						&nbsp;
						<select name="id_language_2" {if $data.root == 1}disabled="disabled"{/if} style="width:150px">
							<option value="0">{$lang.home_page.select_default}</option>
							{foreach item=item from=$lang_sel}
							<option value="{$item.id}" {if $item.sel2}selected="selected"{/if}>{$item.value}</option>
							{/foreach}
						</select>
						&nbsp;
						<select name="id_language_3" {if $data.root == 1}disabled="disabled"{/if} style="width:150px">
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
					<td align="right" class="main_header_text">
						<label title="{$lang.level_of_english_thai}">
							{if $err_field.mm_level_of_english}<font class="error_msg">{/if}
							{$lang.users.mm_level_of_english}
							{if $err_field.mm_level_of_english}</font>{/if}
							{if $mandatory.mm_level_of_english & $mandatory_level}<font class="mandatory">*</font>{/if}:
						</label>
					</td>
					<td>
						<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								{foreach item=item from=$mm_level_of_english}
									<td>
										<input type="radio" name="mm_level_of_english" value="{$item.id}" {if $item.sel}checked="checked"{/if} />
									</td>
									<td style="padding-right:15px;" class="text"> &nbsp;
										<label title="{$lang.mm_level_english[$item.value]}">{$item.value}</label>
									</td>
								{/foreach}
							</tr>
						</table>
					</td>
				</tr>
			{/if}
			<tr><td colspan="3"><div class="hline"></div></td></tr>
		{/if}
		
		{* EMPLOYMENT INFO *}
		{if $use_field.mm_employment_status & SB_EDIT}
			{* delimiter *}
			<tr>
				<td colspan="3" class="text_head">
					<label title="{$lang.employment_info_thai}">{$lang.profile_head.employment_info}</label>
				</td>
			</tr>
			{* added: employment status *}
			{if $use_field.mm_employment_status & SB_EDIT}
				<tr>
					<td align="right" class="main_header_text">
						<label title="{$lang.employment_status_thai}">
							{if $err_field.mm_employment_status || $err_field.mm_business_name || $err_field.mm_employer_name}<font class="error_msg">{/if}
							{$lang.users.mm_employment_status}
							{if $err_field.mm_employment_status || $err_field.mm_business_name || $err_field.mm_employer_name}</font>{/if}
							{if $mandatory.mm_employment_status & $mandatory_level}<font class="mandatory">*</font>{/if}:
						</label>
					</td>
					<td>
						<table border="0" cellpadding="0" cellspacing="0">
							{foreach item=item from=$mm_employment_status}
								<tr>
									<td height="30">
										<input type="radio" name="mm_employment_status" value="{$item.id}" {if $item.sel}checked="checked"{/if} />
									</td>
									<td height="30" style="padding-right:15px;" class="text"> &nbsp;
										<label title="{$lang.mm_employment_status[$item.value]}">{$item.value}</label>
									</td>
									{if $item.id == 1}
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									{elseif $item.id == 2}
										{if $use_field.mm_business_name & SB_EDIT}
											<td height="30" class="text_head">
												<label title="{$lang.business_name_thai}">
													{if $err_field.mm_business_name}<font class="error_msg">{/if}
													{$lang.users.mm_business_name}
													{if $err_field.mm_business_name}</font>{/if}:
												</label>
											</td>
											<td>
												<input type="text" name="mm_business_name" value="{$data.mm_business_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:150px" />
											</td>
										{else}
											<td>&nbsp;</td>
											<td>&nbsp;</td>
										{/if}
									{elseif $item.id == 3}
										{if $use_field.mm_employer_name & SB_EDIT}
											<td height="30" class="text_head">
												<label title="{$lang.employer_name_thai}">
													{if $err_field.mm_employer_name}<font class="error_msg">{/if}
													{$lang.users.mm_employer_name}
													{if $err_field.mm_employer_name}</font>{/if}:
												</label>
											</td>
											<td>
												<input type="text" name="mm_employer_name" value="{$data.mm_employer_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:150px" />
											</td>
										{else}
											<td>&nbsp;</td>
											<td>&nbsp;</td>
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
					<td align="right" class="main_header_text">
						<label title="{$lang.job_position_thai}">
							{if $err_field.mm_job_position}<font class="error_msg">{/if}
							{$lang.users.mm_job_position}
							{if $err_field.mm_job_position}</font>{/if}
							{if $mandatory.mm_job_position & $mandatory_level}<font class="mandatory">*</font>{/if}:
						</label>
					</td>
					<td>
						<input type="text" name="mm_job_position" value="{$data.mm_job_position}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
					</td>
				</tr>
			{/if}
			{* added: work address *}
			{if $use_field.mm_work_address & SB_EDIT}
				<tr>
					<td align="right" class="main_header_text">
						<label title="{$lang.work_address_thai}">
							{if $err_field.mm_work_address}<font class="error_msg">{/if}
							{$lang.users.mm_work_address}
							{if $err_field.mm_work_address}</font>{/if}
							{if $mandatory.mm_work_address & $mandatory_level}<font class="mandatory">*</font>{/if}:
						</label>
					</td>
					<td>
						<input type="text" name="mm_work_address" value="{$data.mm_work_address}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
					</td>
				</tr>
			{/if}
			{* added: work phone number *}
			{if $use_field.mm_work_phone_number & SB_EDIT}
				<tr>
					<td align="right" class="main_header_text">
						<label title="{$lang.work_phone_thai}">
							{if $err_field.mm_work_phone_number}<font class="error_msg">{/if}
							{$lang.users.mm_work_phone_number}
							{if $err_field.mm_work_phone_number}</font>{/if}
							{if $mandatory.mm_work_phone_number & $mandatory_level}<font class="mandatory">*</font>{/if}:
						</label>
					</td>
					<td>
						<input type="text" name="mm_work_phone_number" value="{$data.mm_work_phone_number}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
					</td>
				</tr>
			{/if}
			<tr><td colspan="3"><div class="hline"></div></td></tr>
		{/if}
		
		{* REFERENCE 1 *}
		{if $use_field.mm_ref_1_first_name & SB_EDIT}
			{* delimiter *}
			<tr>
				<td colspan="3" class="text_head">
					<label title="{$lang.reference_1_thai}">{$lang.profile_head.reference_1}</label>
				</td>
			</tr>
			{* added: ref 1 first name *}
			{if $use_field.mm_ref_1_first_name & SB_EDIT}
				<tr>
					<td align="right" class="main_header_text">
						<label title="{$lang.first_name_thai}">
							{if $err_field.mm_ref_1_first_name}<font class="error_msg">{/if}
							{$lang.users.fname}
							{if $err_field.mm_ref_1_first_name}</font>{/if}
							{if $mandatory.mm_ref_1_first_name & $mandatory_level}<font class="mandatory">*</font>{/if}:
						</label>
					</td>
					<td>
						<input type="text" name="mm_ref_1_first_name" value="{$data.mm_ref_1_first_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
					</td>
				</tr>
			{/if}
			{* added: ref 1 last name *}
			{if $use_field.mm_ref_1_last_name & SB_EDIT}
				<tr>
					<td align="right" class="main_header_text">
						<label title="{$lang.last_name_thai}">
							{if $err_field.mm_ref_1_last_name}<font class="error_msg">{/if}
							{$lang.users.sname}
							{if $err_field.mm_ref_1_last_name}</font>{/if}
							{if $mandatory.mm_ref_1_last_name & $mandatory_level}<font class="mandatory">*</font>{/if}:
						</label>
					</td>
					<td>
						<input type="text" name="mm_ref_1_last_name" value="{$data.mm_ref_1_last_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
					</td>
				</tr>
			{/if}
			{* added: ref 1 relationship *}
			{if $use_field.mm_ref_1_relationship & SB_EDIT}
				<tr>
					<td align="right" class="main_header_text">
						<label title="{$lang.reference_relationship_thai}">
							{if $err_field.mm_ref_1_relationship}<font class="error_msg">{/if}
							{$lang.users.mm_reference_relationship}
							{if $err_field.mm_ref_1_relationship}</font>{/if}
							{if $mandatory.mm_ref_1_relationship & $mandatory_level}<font class="mandatory">*</font>{/if}:
						</label>
					</td>
					<td>
						<input type="text" name="mm_ref_1_relationship" value="{$data.mm_ref_1_relationship}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
					</td>
				</tr>
			{/if}
			{* added: ref 1 phone number *}
			{if $use_field.mm_ref_1_phone_number & SB_EDIT}
				<tr>
					<td align="right" class="main_header_text">
						<label title="{$lang.reference_phone_thai}">
							{if $err_field.mm_ref_1_phone_number}<font class="error_msg">{/if}
							{$lang.users.mm_reference_phone_number}
							{if $err_field.mm_ref_1_phone_number}</font>{/if}
							{if $mandatory.mm_ref_1_phone_number & $mandatory_level}<font class="mandatory">*</font>{/if}:
						</label>
					</td>
					<td>
						<input type="text" name="mm_ref_1_phone_number" value="{$data.mm_ref_1_phone_number}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
					</td>
				</tr>
			{/if}
			<tr><td colspan="3"><div class="hline"></div></td></tr>
		{/if}
		
		{* REFERENCE 2 *}
		{if $use_field.mm_ref_1_first_name & SB_EDIT}
			{* delimiter *}
			<tr>
				<td colspan="3" class="text_head">
					<label title="{$lang.reference_2_thai}">{$lang.profile_head.reference_2}</label>
				</td>
			</tr>
			{* added: ref 2 first name *}
			{if $use_field.mm_ref_2_first_name & SB_EDIT}
				<tr>
					<td align="right" class="main_header_text">
						<label title="{$lang.first_name_thai}">
							{if $err_field.mm_ref_2_first_name}<font class="error_msg">{/if}
							{$lang.users.fname}
							{if $err_field.mm_ref_2_first_name}</font>{/if}
							{if $mandatory.mm_ref_2_first_name & $mandatory_level}<font class="mandatory">*</font>{/if}:
						</label>
					</td>
					<td>
						<input type="text" name="mm_ref_2_first_name" value="{$data.mm_ref_2_first_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
					</td>
				</tr>
			{/if}
			{* added: ref 2 last name *}
			{if $use_field.mm_ref_2_last_name & SB_EDIT}
				<tr>
					<td align="right" class="main_header_text">
						<label title="{$lang.last_name_thai}">
							{if $err_field.mm_ref_2_last_name}<font class="error_msg">{/if}
							{$lang.users.sname}
							{if $err_field.mm_ref_2_last_name}</font>{/if}
							{if $mandatory.mm_ref_2_last_name & $mandatory_level}<font class="mandatory">*</font>{/if}:
						</label>
					</td>
					<td>
						<input type="text" name="mm_ref_2_last_name" value="{$data.mm_ref_2_last_name}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
					</td>
				</tr>
			{/if}
			{* added: ref 2 relationship *}
			{if $use_field.mm_ref_2_relationship & SB_EDIT}
				<tr>
					<td align="right" class="main_header_text">
						<label title="{$lang.reference_relationship_thai}">
							{if $err_field.mm_ref_2_relationship}<font class="error_msg">{/if}
							{$lang.users.mm_reference_relationship}
							{if $err_field.mm_ref_2_relationship}</font>{/if}
							{if $mandatory.mm_ref_2_relationship & $mandatory_level}<font class="mandatory">*</font>{/if}:
						</label>
					</td>
					<td>
						<input type="text" name="mm_ref_2_relationship" value="{$data.mm_ref_2_relationship}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
					</td>
				</tr>
			{/if}
			{* added: ref 2 phone number *}
			{if $use_field.mm_ref_2_phone_number & SB_EDIT}
				<tr>
					<td align="right" class="main_header_text">
						<label title="{$lang.reference_phone_thai}">
							{if $err_field.mm_ref_2_phone_number}<font class="error_msg">{/if}
							{$lang.users.mm_reference_phone_number}
							{if $err_field.mm_ref_2_phone_number}</font>{/if}
							{if $mandatory.mm_ref_2_phone_number & $mandatory_level}<font class="mandatory">*</font>{/if}:
						</label>
					</td>
					<td>
						<input type="text" name="mm_ref_2_phone_number" value="{$data.mm_ref_2_phone_number}" {if $data.root == 1}disabled="disabled"{/if} style="width:195px" />
					</td>
				</tr>
			{/if}
			<tr><td colspan="3"><div class="hline"></div></td></tr>
		{/if}
        		
		{* MY PRIVACY SETTINGS *}
		{if $use_field.privacy_settings & SB_EDIT}
			<tr>
				<td colspan="3" class="text_head">
					<label title="{$lang.privacy_settings_thai}">{$lang.profile_head.privacy_settings}</label>
				</td>
			</tr>
			
			{* online privacy *}
			<tr>
				<td class="main_header_text" colspan="3">
					<label title="{$lang.privacy_online_title_thai}">{$lang.users.privacy_online_title}:</label>
				</td>
			</tr>
			<tr>
				<td width="150">&nbsp;</td>
				<td>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td><input type="checkbox" name="hide_online" {if $data.hide_online==1}checked="checked"{/if} value="1" /></td>
							<td class="txtblack">
								&nbsp;&nbsp;
								<label for="hide_online" title="{$lang.privacy_online_hide_thai}">
									{$lang.users.privacy_online_hide}
								</label>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			
			{* female privacy *}
			{if $use_field.privacy_female & SB_EDIT}
				<tr>
					<td class="main_header_text" colspan="3">
						<label title="{$lang.privacy_female_thai}">{$lang.users.privacy_female}:</label>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<table border="0" cellpadding="0" cellspacing="1">
							<tr>
								<td><input type="radio" onclick="SetLadiesVisibleToNone();" name="visible_lady" {if $data.visible_lady==0}checked="checked"{/if} value="0" /></td>
								<td class="txtblack">&nbsp; {$lang.users.privacy_lady_none}</td>
							</tr>
							<tr>
								<td><input type="radio" onclick="SetLadiesVisibleToAll();" name="visible_lady" {if $data.visible_lady==1}checked="checked"{/if} value="1" /></td>
								<td class="txtblack">&nbsp; {$lang.users.privacy_lady_all}</td>
							</tr>
							<tr>
								<td><input type="radio" onclick="SetLadiesVisibleToSelected();" name="visible_lady" {if $data.visible_lady==2}checked="checked"{/if} value="2" /></td>
								<td class="txtblack">&nbsp; {$lang.users.privacy_lady_selected}</td>
							</tr>
							<tr>
								<td></td>
								<td>
									<table cellpadding="0" cellspacing="0" id="tbl55">
										<tr>
											<td>&nbsp;&nbsp;<input type="checkbox" onclick="CheckSelectedOptions(this,1);" name="vis_lady_1" id="vis_lady_1" {if $data.visible_lady < 2}disabled="disabled"{/if} {if $data.vis_lady_1==1}checked="checked"{/if} value="1" /></td>
											<td class="txtblack">&nbsp; {$lang.users.privacy_lady_trial}</td>
										</tr>
										<tr>
											<td>&nbsp;&nbsp;<input type="checkbox" onclick="CheckSelectedOptions(this,1);" name="vis_lady_2" id="vis_lady_2" {if $data.visible_lady < 2}disabled="disabled"{/if} {if $data.vis_lady_2==1}checked="checked"{/if} value="1" /></td>
											<td class="txtblack">&nbsp; {$lang.users.privacy_lady_regular}</td>
										</tr>
										<tr>
											<td>&nbsp;&nbsp;<input type="checkbox" onclick="CheckSelectedOptions(this,1);" name="vis_lady_3" id="vis_lady_3" {if $data.visible_lady < 2}disabled="disabled"{/if} {if $data.vis_lady_3==1}checked="checked"{/if} value="1" /></td>
											<td class="txtblack">&nbsp; {$lang.users.privacy_lady_platinum}</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			{/if}
			
			{* male privacy *}
			{if $use_field.privacy_male & SB_EDIT}
				<tr>
					<td class="main_header_text" colspan="3">
						<label title="{$lang.privacy_male_thai}">{$lang.users.privacy_male}:</label>
					</td>
				</tr>
				<tr>
					<td width="150">&nbsp;</td>
					<td>
						<table border="0" cellpadding="0" cellspacing="1">
							<tr>
								<td><input type="radio" onclick="SetGentsVisibleToNone();" name="visible_guy" {if $data.visible_guy==0}checked="checked"{/if} value="0" /></td>
								<td class="txtblack">&nbsp; {$lang.users.privacy_guy_none}</td>
							</tr>
							<tr>
								<td><input type="radio" onclick="SetGentsVisibleToAll();" name="visible_guy" {if $data.visible_guy==1}checked="checked"{/if} value="1" /></td>
								<td class="txtblack">&nbsp; {$lang.users.privacy_guy_all}</td>
							</tr>
							<tr>
								<td><input type="radio" onclick="SetGentsVisibleToSelected();" name="visible_guy" {if $data.visible_guy==2}checked="checked"{/if} value="2" /></td>
								<td class="txtblack">&nbsp; {$lang.users.privacy_guy_selected}</td>
							</tr>
							<tr>
								<td></td>
								<td>
									<table cellpadding="0" cellspacing="0">
										<tr>
											<td>&nbsp;&nbsp;<input type="checkbox" onclick="CheckSelectedOptions(this,2);" name="vis_guy_1" id="vis_guy_1" {if $data.visible_guy < 2}disabled="disabled"{/if} {if $data.vis_guy_1==1}checked="checked"{/if} value="1" /></td>
											<td class="txtblack">&nbsp; {$lang.users.privacy_guy_trial}</td>
										</tr>
										<tr>
											<td>&nbsp;&nbsp;<input type="checkbox" onclick="CheckSelectedOptions(this,2);" name="vis_guy_2" id="vis_guy_2" {if $data.visible_guy < 2}disabled="disabled"{/if} {if $data.vis_guy_2==1}checked="checked"{/if} value="1" /></td>
											<td class="txtblack">&nbsp; {$lang.users.privacy_guy_regular}</td>
										</tr>
										<tr>
											<td>&nbsp;&nbsp;<input type="checkbox" onclick="CheckSelectedOptions(this,2);" name="vis_guy_3" id="vis_guy_3" {if $data.visible_guy < 2}disabled="disabled"{/if} {if $data.vis_guy_3==1}checked="checked"{/if} value="1" /></td>
											<td class="txtblack">&nbsp; {$lang.users.privacy_guy_platinum}</td>
										</tr>
										<tr>
											<td>&nbsp;&nbsp;<input type="checkbox" onclick="CheckSelectedOptions(this,2);" name="vis_guy_4" id="vis_guy_4" {if $data.visible_guy < 2}disabled="disabled"{/if} {if $data.vis_guy_4==1}checked="checked"{/if} value="1" /></td>
											<td class="txtblack">&nbsp; {$lang.users.privacy_guy_elite}</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			{/if}
		
			{* promotion *}
			{if $use_field.promotion & SB_EDIT}
				<tr><td></td></tr>
				<tr>
					<td class="main_header_text" colspan="3">
						<label title="{$lang.privacy_promotion_thai}">{$lang.users.privacy_promo}:</label>
					</td>
				</tr>
				<tr>
					<td width="150">&nbsp;</td>
					<td>
						<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td>
									{if $use_field.promote_no}
									<div>
										<input type="checkbox" style="position:relative; top:3px;" onclick="CheckPromotionOptions(this);" name="promotion_1" id="promotion_1" {if $data.promotion_1==1}checked="checked"{/if} value="1" />
										&nbsp;{$lang.users.promote_no}
									</div>
									{/if}
									{if $use_field.promote_within}
									<div>
										<input type="checkbox" style="position:relative; top:3px;" onclick="CheckPromotionOptions(this);" name="promotion_2" id="promotion_2" {if $data.promotion_2==1}checked="checked"{/if} value="1" />
										&nbsp;{$lang.users.promote_within}
									</div>
									{/if}
									{if $use_field.promote_prospective}
									<div>
										<input type="checkbox" style="position:relative; top:3px;" onclick="CheckPromotionOptions(this);" name="promotion_3" id="promotion_3" {if $data.promotion_3==1}checked="checked"{/if} value="1" />
										&nbsp;{$lang.users.promote_prospective}
									</div>
									{/if}
								</td>
								{if $data.group_id neq MM_SIGNUP_GUY_ID && $data.group_id neq MM_SIGNUP_LADY_ID}
								<td style="padding-left:20px;">
									<div style="padding:3px 8px 8px 3px; border:#CCC 1px solid; background-color:#EEEEEE;">
										<div>
											<input type="checkbox" style="position:relative; top:3px;" name="featured_land" {if $data.featured_land==1}checked="checked"{/if} value="1" />
											&nbsp;<b>{$lang.users.addto_featured_land}</b>
										</div>
										<div style="padding-top:5px;">
											<input type="checkbox" style="position:relative; top:3px;" name="featured_home" {if $data.featured_home==1}checked="checked"{/if} value="1" />
											&nbsp;<b>{$lang.users.addto_featured_home}</b>
										</div>
									</div>
								</td>
								{/if}
						</table>
					</td>
				</tr>
			{/if}
			
			<tr><td colspan="3"><div class="hline"></div></td></tr>
		{/if}
		
		{* BIOGRAPHY *}
		{if $use_field.biography & SB_EDIT}
			<tr>
				<td colspan="3" class="text_head">
					<label title="{$lang.biography_thai}">{$lang.profile_head.biography}</label>
				</td>
			</tr>
			{if $use_field.about_me & SB_EDIT}
				<tr>
					<td align="right" class="main_header_text">
						<label title="{$lang.about_me_thai}">
							{if $err_field.about_me}<font class="error_msg">{/if}
							{$lang.users.about_me}
							{if $err_field.about_me}</font>{/if}
							{if $mandatory.about_me & $mandatory_level}<font class="mandatory">*</font>{/if}:
						</label>
					</td>
					<td colspan="2">
						<textarea style="width:550px; height:50px;" name="about_me">{$data.about_me}</textarea>
					</td>
				</tr>
			{/if}
			{if $use_field.what_i_do & SB_EDIT}
				<tr>
					<td align="right" class="main_header_text">
						<label title="{$lang.what_i_do_thai}">
							{if $err_field.what_i_do}<font class="error_msg">{/if}
							{$lang.users.what_i_do}
							{if $err_field.what_i_do}</font>{/if}
							{if $mandatory.what_i_do & $mandatory_level}<font class="mandatory">*</font>{/if}:
						</label>
					</td>
					<td colspan="2">
						<textarea style="width:550px; height:50px;" name="what_i_do">{$data.what_i_do}</textarea>
					</td>
				</tr>
			{/if}
			{if $use_field.my_idea & SB_EDIT}
				<tr>
					<td align="right" class="main_header_text">
						<label title="{$lang.my_idea_thai}">
							{if $err_field.my_idea}<font class="error_msg">{/if}
							{$lang.users.my_idea}
							{if $err_field.my_idea}</font>{/if}
							{if $mandatory.my_idea & $mandatory_level}<font class="mandatory">*</font>{/if}:
						</label>
					</td>
					<td colspan="2">
						<textarea style="width:550px; height:50px;" name="my_idea">{$data.my_idea}</textarea>
					</td>
				</tr>
			{/if}
			{if $use_field.hoping_to_find & SB_EDIT}
				<tr>
					<td align="right" class="main_header_text">
						<label title="{$lang.hoping_to_find}">
							{if $err_field.hoping_to_find}<font class="error_msg">{/if}
							{$lang.users.hoping_to_find}
							{if $err_field.hoping_to_find}</font>{/if}
							{if $mandatory.hoping_to_find & $mandatory_level}<font class="mandatory">*</font>{/if}:
						</label>
					</td>
					<td colspan="2">
						<textarea style="width:550px; height:50px;" name="hoping_to_find">{$data.hoping_to_find}</textarea>
					</td>
				</tr>
			{/if}
			<tr><td colspan="3"><div class="hline"></div></td></tr>
		{/if}
		
		{* comment *}
		{if $use_field.comment & SB_EDIT}
			<tr>
				<td align="right" class="main_header_text">{$header.comment}:&nbsp;</td>
				<td colspan="2">
					<textarea style="width:195" name="comment" rows="10" cols="29" {if $data.root eq 1}disabled{/if}>{$data.comment}</textarea>
				</td>
			</tr>
		{/if}
    </table>
</form>
<table>
    <tr height="40">
		{if $form.par eq "edit"}
			{if $data.root  ne 1}
				<td>
					<input type="button" value="{$button.save}" class="button" onclick="javascript:document.profile.submit()">
				</td>
				<!--
				<td>
					<input type="button" value="{$button.delete}" class="button" onclick="{literal}javascript: if(confirm({/literal}'{$form.confirm}'{literal})){location.href={/literal}'{$form.delete}'{literal}}{/literal}">
				</td>
				-->
			{/if}
        {else}
			<td>
				<input type="button" value="{$button.add}" class="button" onclick="javascript:document.profile.submit()">
			</td>
        {/if}
        <td>
            <input type="button" value="{$button.back}" class="button" onclick="javascript: location.href='{$form.back}'">
        </td>
    </tr>
</table>
<script language="javascript">
{literal}
function alertMessage(pObj)
{
	if(!pObj.checked)
	{
		{/literal}
		var alertMsg = '{$alerts.user_disable}';
		{literal}
		var lobjConfirm = confirm(alertMsg);
		if (lobjConfirm != true)
		{
			pObj.checked = true;
		}
	}
}
{/literal}
</script>
{include file="$admingentemplates/admin_bottom.tpl"}