{* PAGE ONE *}
{strip}
{if $form.page == '1'}
	<div>
		{* personal data *}
		<div>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				{if $auth.is_applicant}
					<tr>
						<td height="25" valign="middle">
							<div class="hdr2" style="padding-bottom:15px;">
								{$lang.subsection.application_info}
							</div>
						</td>
						<td width="10%"></td>
						<td></td>
					</tr>
				{else}
					<tr>
						<td align="left" width="40%">
							<div>
								<p class ="basic-btn_next">
									<span><input type="button" onclick="window.location.href='./myprofile.php?sel=edit_1';" value="{$button.edit_this_section}"></span><b>&nbsp;</b>
								</p>
							</div>
						</td>
						<td width="40%" colspan="2">
							{if !$auth.is_applicant}
								<span class="{if $data_1.complete > 50}link{else}text{/if}_active">
								{if $data_1.complete < 5}
									{$lang.profile.not_complited}
								{elseif $data_1.complete >= 5 && $data_1.complete < 95}
									{$lang.profile.filled_on} {$data_1.complete}%
								{elseif $data_1.complete >=95}
									{$lang.profile.complited}
								{/if}
								</span>
							{/if}
						</td>
					</tr>
				{/if}
				<tr class="edit-info">
					<td width="45%" valign="top">
						
						{* LOGIN *}
						<div class="titlebg">
							<div class="fleft"><label title="{$lang.mylogin_thai}">{$lang.profile_head.login}</label></div>
							<div class="edit_link"><a href="{$edit_link}">edit</a></div>
							<div class="clear"></div>
						</div>
						<div style="padding-bottom:5px;" class="txtblack">
							<label title="{$lang.username_thai}">
								<font class="txtblue">{$lang.users.login}:</font> {$data_1.login}
							</label>
						</div>
						
						{* PERSONAL INFO *}
						<br>
						<div class="titlebg">
							<div class="fleft"><label title="{$lang.personal_info_thai}"> {$lang.profile_head.personal_info} </label></div>
							<div class="edit_link"><a href="{$edit_link}">edit</a></div>
							<div class="clear"></div>
						</div>
						{* first name *}
						{if $use_field.fname & SB_EDIT}
							<div style="padding-bottom:5px;" class="txtblack">
								<label title="{$lang.first_name_thai}">
									<font class="txtblue">{$lang.users.fname}:</font> {if $data_1.fname}{$data_1.fname}{elseif $mandatory.fname & SB_EDIT}<font color="red">{$lang.mm_missing} <em>Not Public</em></font>{/if}
								</label>
							</div>
						{/if}
						{* last name *}
						{if $use_field.sname & SB_EDIT}
							<div style="padding-bottom:5px;" class="txtblack">
								<label title="{$lang.last_name_thai}">
									<font class="txtblue">{$lang.users.sname}:</font> {if $data_1.sname}{$data_1.sname}{elseif $mandatory.sname & SB_EDIT}<font color="red">{$lang.mm_missing} <em>Not Public</em></font>{/if}
								</label>
							</div>
						{/if}
						{* nick name *}
						{if $use_field.mm_nickname & SB_EDIT}
							<div style="padding-bottom:5px;" class="txtblack">
								<label title="{$lang.nickname_thai}">
									<font class="txtblue">{$lang.users.mm_nickname}:</font> {if $data_1.mm_nickname}{$data_1.mm_nickname}{elseif $mandatory.mm_nickname & SB_EDIT}<font color="red">{$lang.mm_missing}</font>{/if}
								</label>
							</div>
						{/if}
						{* gender *}
						{if $use_field.gender & SB_EDIT}
							<div style="padding-bottom:5px;" class="txtblack">
								<label title="{$lang.gender_thai}">
									<font class="txtblue">{$lang.users.gender}:</font> {if $data_1.gender_text}{$data_1.gender_text}{elseif $mandatory.gender & SB_EDIT}<font color="red">{$lang.mm_missing}</font>{/if}
								</label>
							</div>
						{/if}
						{* marital status *}
						{if $use_field.mm_marital_status & SB_EDIT}
							<div style="padding-bottom:5px;" class="txtblack">
								<label title="{$lang.marital_status_thai}">
									<font class="txtblue">{$lang.users.mm_marital_status}:</font> {if $data_1.mm_marital_status_text}{$data_1.mm_marital_status_text}{elseif $mandatory.mm_marital_status & SB_EDIT}<font color="red">{$lang.mm_missing}</font>{/if}
								</label>
							</div>
						{/if}
						{* birthday *}
						{if $use_field.date_birthday & SB_EDIT}
							<div style="padding-bottom:5px;" class="txtblack">
								<label title="{$lang.birthday_thai}">
									<font class="txtblue">{$lang.users.date_birthday}:</font> {if $data_1.date_birthday}{$data_1.date_birthday}{elseif $mandatory.date_birthday & SB_EDIT}<font color="red">{$lang.mm_missing}</font>{/if}
								</label>
							</div>
						{/if}
						{* place of birth *}
						{if $use_field.mm_place_of_birth & SB_EDIT}
							<div style="padding-bottom:5px;" class="txtblack">
								<label title="{$lang.place_of_birth_thai}">
									<font class="txtblue">{$lang.users.mm_place_of_birth}:</font> {if $data_1.mm_place_of_birth}{$data_1.mm_place_of_birth}{elseif $mandatory.mm_place_of_birth & SB_EDIT}<font color="red">{$lang.mm_missing}</font>{/if}
								</label>
							</div>
						{/if}
						{* height *}
						{if $use_field.id_height & SB_EDIT}
							<div style="padding-bottom:5px;" class="txtblack">
								<label title="{$lang.height_thai}">
									<font class="txtblue">{$lang.users.height}:</font> {if $data_2.height}{$data_2.height}{else}<font color="red">{$lang.mm_missing}</font>{/if}
								</label>
							</div>
						{/if}
						{* weight *}
						{if $use_field.id_weight & SB_EDIT}
							<div style="padding-bottom:5px;" class="txtblack">
								<label title="{$lang.weight_thai}">
									<font class="txtblue">{$lang.users.weight}:</font> {if $data_2.weight}{$data_2.weight}{else}<font color="red">{$lang.mm_missing}</font>{/if}
								</label>
							</div>
						{/if}
						{* nationality *}
						{if $use_field.id_nationality & SB_EDIT}
							<div style="padding-bottom:5px;" class="txtblack">
								<label title="{$lang.nationality_thai}">
									<font class="txtblue">{$lang.users.nationality}:</font> {if $data_1.nationality}{$data_1.nationality}{elseif $mandatory.id_nationality & SB_EDIT}<font color="red">{$lang.mm_missing}</font>{/if}
								</label>
							</div>
						{/if}
						{* id number *}
						{if $use_field.mm_id_number & SB_EDIT && $data_1.gender == 2}
							<div style="padding-bottom:5px;" class="txtblack">
								<label title="{$lang.id_number_thai}">
									<font class="txtblue">{$lang.users.mm_id_number}:</font> {if $data_1.mm_id_number}{$data_1.mm_id_number}{elseif $mandatory.mm_id_number & SB_EDIT}<font color="red">{$lang.mm_missing}</font>{/if}
								</label>
							</div>
						{/if}
						
						{* CONTACT INFO *}
						<br>
						<div class="titlebg">
							<div class="fleft"><label title="{$lang.contact_info_thai}">{$lang.profile_head.contact_info}</label></div>
							<div class="edit_link"><a href="{$edit_link}">edit</a></div>
							<div class="clear"></div>
						</div>
						{* email *}
						{if $use_field.email & SB_EDIT}
							<div style="padding-bottom:5px;" class="txtblack">
								<label title="{$lang.email_thai}">
									<font class="txtblue">{$lang.users.email}:</font> {if $data_1.email}{$data_1.email}{elseif $mandatory.email & SB_EDIT}<font color="red">{$lang.mm_missing} <em>Not Public</em></font>{/if}
								</label>
							</div>
						{/if}
						{* contact phone *}
						{if $use_field.mm_contact_phone_number & SB_EDIT}
							<div style="padding-bottom:5px;" class="txtblack">
								<label title="{$lang.contact_phone_thai}">
									<font class="txtblue">{$lang.users.mm_contact_phone_number}:</font> {if $data_1.mm_contact_phone_number}{$data_1.mm_contact_phone_number}{elseif $mandatory.mm_contact_phone_number & SB_EDIT}<font color="red">{$lang.mm_missing} <em>Not Public</em></font>{/if}
								</label>
							</div>
						{/if}
						{* mobile phone *}
						{if $use_field.mm_contact_mobile_number & SB_EDIT}
							<div style="padding-bottom:5px;" class="txtblack">
								<label title="{$lang.contact_mobile_thai}">
									<font class="txtblue">{$lang.users.mm_contact_mobile_number}:</font> {if $data_1.mm_contact_mobile_number}{$data_1.mm_contact_mobile_number}{elseif $mandatory.mm_contact_mobile_number & SB_EDIT}<font color="red">{$lang.mm_missing} <em>Not Public</em></font>{/if}
								</label>
							</div>
						{/if}
						{* voip phone *}
						{if $use_field.voipcall_feature & SB_EDIT}
							<div style="padding-bottom:5px;" class="txtblack">
								<font class="txtblue">{$lang.users.phone}:</font> {$data_1.phone}
							</div>
						{/if}
						
						{* ADDRESS INFO *}
						{if $use_field.id_country & SB_EDIT || $use_field.mm_city & SB_EDIT || ($use_field.id_region & SB_EDIT) && $data_1.gender == 2}
							<div class="titlebg">
								<div class="fleft"><label title="{$lang.address_info_thai}">{$lang.profile_head.address_info}</label></div>
								<div class="edit_link"><a href="{$edit_link}">edit</a></div>
								<div class="clear"></div>
							</div>
							{* country *}
							{if $use_field.id_country & SB_EDIT}
								<div style="padding-bottom:5px;" class="txtblack">
									<label title="{$lang.country_thai}">
										<font class="txtblue">{$lang.users.country}:</font> {if $data_1.country}{$data_1.country}{elseif $mandatory.id_country & SB_EDIT}<font color="red">{$lang.mm_missing}</font>{/if}
									</label>
								</div>
							{/if}
							{* region *}
							{if $use_field.id_region & SB_EDIT && $data_1.gender == 2}
								<div style="padding-bottom:5px;" class="txtblack">
									<font class="txtblue">{$lang.users.region}:</font> {if $data_1.region}
										{$data_1.region}
									{elseif $mandatory.id_region & SB_EDIT}
										<font color="red">{$lang.mm_missing}</font>
									{/if}
								</div>
							{/if}
							{* city *}
							{if $use_field.mm_city & SB_EDIT}
								<div style="padding-bottom:5px;" class="txtblack">
									<label title="{$lang.city_thai}">
										<font class="txtblue">{$lang.users.city}:</font> {if $data_1.mm_city}{$data_1.mm_city}{elseif $mandatory.mm_city & SB_EDIT}<font color="red">{$lang.mm_missing}</font>{/if}
									</label>
								</div>
							{/if}
							{* zip code *}
							{if $use_field.zipcode & SB_EDIT}
								<div style="padding-bottom:5px;" class="txtblack">
									<label title="{$lang.zipcode_thai}">
										<font class="txtblue">{$lang.users.zipcode}:</font> {if $data_1.zipcode}{$data_1.zipcode}{elseif $mandatory.zipcode & SB_EDIT}<font color="red">{$lang.mm_missing}</font>{/if}
									</label>
								</div>
							{/if}
							{* address 1 *}
							{if $use_field.mm_address_1 & SB_EDIT}
								<div style="padding-bottom:5px;" class="txtblack">
									<label title="{$lang.address_line_1_thai}">
										<font class="txtblue">{$lang.users.mm_address_1}:</font> {if $data_1.mm_address_1}{$data_1.mm_address_1}{elseif $mandatory.mm_address_1 & SB_EDIT}<font color="red">{$lang.mm_missing}</font>{/if}
									</label>
								</div>
							{/if}
							{* address 2 *}
							{if $use_field.mm_address_2 & SB_EDIT}
								<div style="padding-bottom:5px;" class="txtblack">
									<label title="{$lang.address_line_2_thai}">
										<font class="txtblue">{$lang.users.mm_address_2}:</font> {if $data_1.mm_address_2}{$data_1.mm_address_2}{elseif $mandatory.mm_address_2 & SB_EDIT}<font color="red">{$lang.mm_missing}</font>{/if}
									</label>
								</div>
							{/if}
						{/if}
						
						{* LANGUAGE INFO *}
						<div class="titlebg">
							<div class="fleft"><label title="{$lang.language_info_thai}">{$lang.profile_head.language_info}</label></div>
							<div class="edit_link"><a href="{$edit_link}">edit</a></div>
							<div class="clear"></div>
						</div>
						{* languages *}
						{if $use_field.id_language_1 & SB_EDIT}
							<div style="padding-bottom:5px;" class="txtblack">
								<label title="{$lang.language_thai}">
									<font class="txtblue">{$lang.users.language}:</font> {if $data_1.languages}{$data_1.languages}{elseif $mandatory.id_language_1 & SB_EDIT}<font color="red">{$lang.mm_missing}</font>{/if}
								</label>
							</div>
						{/if}
						{* level of english *}
						{if $use_field.mm_level_of_english & SB_EDIT}
							<div style="padding-bottom:5px;" class="txtblack">
								<label title="{$lang.level_of_english_thai}">
									<font class="txtblue">{$lang.users.mm_level_of_english}:</font>&nbsp;
								</label> {if $data_1.mm_level_of_english_text}
									<label title="{$lang.mm_level_english[$data_1.mm_level_of_english_text]}">
										{$data_1.mm_level_of_english_text}
									</label>
								{elseif $mandatory.mm_level_of_english & SB_EDIT}
									<font color="red">{$lang.mm_missing}</font>
								{/if}
							</div>
						{/if}
						{* site language *}
						{if $use_field.site_lang & SB_EDIT}
							<div style="padding-bottom:5px;" class="txtblack">
								<font class="txtblue">{$lang.users.site_language}:</font> {$data_1.site_language_text}
							</div>
						{/if}
					</td>
					<td width="10%"></td>
					<td width="45%" valign="top">
						{* EMPLOYMENT INFO *}
						{if $use_field.mm_employment_status & SB_EDIT}
							<div class="titlebg">
								<div class="fleft"><label title="{$lang.employment_info_thai}">{$lang.profile_head.employment_info}</label></div>
								<div class="edit_link"><a href="{$edit_link}">edit</a></div>
								<div class="clear"></div>
							</div>
						{/if}
						{* employment status *}
						{if $use_field.mm_employment_status & SB_EDIT}
							<div style="padding-bottom:5px;" class="txtblack">
								<label title="{$label.employment_status_thai}">
									<font class="txtblue">{$lang.users.mm_employment_status}:</font>&nbsp;
								</label> {if $data_1.mm_employment_status_text}
									<label title="{$lang.mm_employment_status[$data_1.mm_employment_status_text]}">
										{$data_1.mm_employment_status_text}
									</label>
								{elseif $mandatory.mm_employment_status & SB_EDIT}
									<font color="red">{$lang.mm_missing}</font>
								{/if}
							</div>
						{/if}
						{* business name *}
						{if $use_field.mm_business_name & SB_EDIT}
							<div style="padding-bottom:5px;" class="txtblack">
								<label title="{$lang.business_name_thai}">
									<font class="txtblue">{$lang.users.mm_business_name}:</font> {if $data_1.mm_business_name}
										{$data_1.mm_business_name}
									{elseif $mandatory.mm_business_name & SB_EDIT && ($data_1.mm_employment_status == 2 || !$data_1.mm_employment_status)}
										<font color="red">{$lang.mm_missing}</font>
									{else}
										{$lang.mm_not_applicable}
									{/if}
								</label>
							</div>
						{/if}
						{* employer name *}
						{if $use_field.mm_employer_name & SB_EDIT}
							<div style="padding-bottom:5px;" class="txtblack">
								<label title="{$lang.employer_name_thai}">
									<font class="txtblue">{$lang.users.mm_employer_name}:</font> {if $data_1.mm_employer_name}
										{$data_1.mm_employer_name}
									{elseif $mandatory.mm_employer_name & SB_EDIT && ($data_1.mm_employment_status == 3 || !$data_1.mm_employment_status)}
										<font color="red">{$lang.mm_missing}</font>
									{else}
										{$lang.mm_not_applicable}
									{/if}
								</label>
							</div>
						{/if}
						{* job position *}
						{if $use_field.mm_job_position & SB_EDIT}
							<div style="padding-bottom:5px;" class="txtblack">
								<label title="{$lang.job_position_thai}">
									<font class="txtblue">{$lang.users.mm_job_position}:</font> {if $data_1.mm_job_position}
										{$data_1.mm_job_position}
									{elseif $mandatory.mm_job_position & SB_EDIT && $data_1.mm_employment_status != 1}
										<font color="red">{$lang.mm_missing}</font>
									{else}
										{$lang.mm_not_applicable}
									{/if}
								</label>
							</div>
						{/if}
						{* work address *}
						{if $use_field.mm_work_address & SB_EDIT}
							<div style="padding-bottom:5px;" class="txtblack">
								<label title="{$lang.work_address_thai}">
									<font class="txtblue">{$lang.users.mm_work_address}:</font> {if $data_1.mm_work_address}
										{$data_1.mm_work_address}
									{elseif $mandatory.mm_work_address & SB_EDIT && $data_1.mm_employment_status != 1}
										<font color="red">{$lang.mm_missing}</font>
									{else}
										{$lang.mm_not_applicable}
									{/if}
								</label>
							</div>
						{/if}
						{* work phone number *}
						{if $use_field.mm_work_phone_number & SB_EDIT}
							<div style="padding-bottom:5px;" class="txtblack">
								<label title="{$lang.work_phone_thai}">
									<font class="txtblue">{$lang.users.mm_work_phone_number}:</font> {if $data_1.mm_work_phone_number}
										{$data_1.mm_work_phone_number}
									{elseif $mandatory.mm_work_phone_number & SB_EDIT && $data_1.mm_employment_status != 1}
										<font color="red">{$lang.mm_missing}</font>
									{else}
										{$lang.mm_not_applicable}
									{/if}
								</label>
							</div>
						{/if}
						
						{* REFERENCE 1 *}
						{if $use_field.mm_ref_1_first_name & SB_EDIT || $use_field.mm_ref_1_last_name & SB_EDIT || $use_field.mm_ref_1_relationship & SB_EDIT || $use_field.mm_ref_1_phone_number & SB_EDIT}
							<br>
							<div class="titlebg">
								<div class="fleft"><label title="บุคคลที่ 1 ที่สามารถยืนยันข้อมูลของคุณได้"> {$lang.profile_head.reference_1} </label></div>
								<div class="edit_link"><a href="{$edit_link}">edit</a></div>
								<div class="clear"></div>
							</div>
							{* ref 1 first name *}
							{if $use_field.mm_ref_1_first_name & SB_EDIT}
								<div style="padding-bottom:5px;" class="txtblack">
									<label title="ชื่อ">
										<font class="txtblue">{$lang.users.fname}:</font> {if $data_1.mm_ref_1_first_name}
											{$data_1.mm_ref_1_first_name}
										{elseif $mandatory.mm_ref_1_first_name & SB_EDIT}
											<font color="red">{$lang.mm_missing}</font>
										{/if}
									</label>
								</div>
							{/if}
							{* ref 1 last name *}
							{if $use_field.mm_ref_1_last_name & SB_EDIT}
								<div style="padding-bottom:5px;" class="txtblack">
									<label title="นามสกุล">
										<font class="txtblue">{$lang.users.sname}:</font> {if $data_1.mm_ref_1_last_name}
											{$data_1.mm_ref_1_last_name}
										{elseif $mandatory.mm_ref_1_last_name & SB_EDIT}
											<font color="red">{$lang.mm_missing}</font>
										{/if}
									</label>
								</div>
							{/if}
							{* ref 1 relationship *}
							{if $use_field.mm_ref_1_relationship & SB_EDIT}
								<div style="padding-bottom:5px;" class="txtblack">
									<label title="ความสัมพันธ์">
										<font class="txtblue">{$lang.users.mm_reference_relationship}:</font> {if $data_1.mm_ref_1_relationship}
											{$data_1.mm_ref_1_relationship}
										{elseif $mandatory.mm_ref_1_relationship & SB_EDIT}
											<font color="red">{$lang.mm_missing}</font>
										{/if}
									</label>
								</div>
							{/if}
							{* ref 1 phone *}
							{if $use_field.mm_ref_1_phone_number & SB_EDIT}
								<div style="padding-bottom:5px;" class="txtblack">
									<label title="หมายเลขโทรศัพท์">
										<font class="txtblue">{$lang.users.mm_reference_phone_number}:</font> {if $data_1.mm_ref_1_phone_number}
											{$data_1.mm_ref_1_phone_number}
										{elseif $mandatory.mm_ref_1_phone_number & SB_EDIT}
											<font color="red">{$lang.mm_missing}</font>
										{/if}
									</label>
								</div>
							{/if}
						{/if}
						
						{* REFERENCE 2 *}
						{if $use_field.mm_ref_2_first_name & SB_EDIT || $use_field.mm_ref_2_last_name & SB_EDIT || $use_field.mm_ref_2_relationship & SB_EDIT || $use_field.mm_ref_2_phone_number & SB_EDIT}
							<br>
							<div class="titlebg">
								<div class="fleft"><label title="บุคคลที่ 2 ที่สามารถยืนยันข้อมูลของคุณได้">{$lang.profile_head.reference_2}</label></div>
								<div class="edit_link"><a href="{$edit_link}">edit</a></div>
								<div class="clear"></div>
							</div>
							{* ref 2 first name *}
							{if $use_field.mm_ref_2_first_name & SB_EDIT}
								<div style="padding-bottom:5px;" class="txtblack">
									<label title="ชื่อ">
										<font class="txtblue">{$lang.users.fname}:</font> {if $data_1.mm_ref_2_first_name}
											{$data_1.mm_ref_2_first_name}
										{elseif $mandatory.mm_ref_2_first_name & SB_EDIT}
											<font color="red">{$lang.mm_missing}</font>
										{/if}
									</label>
								</div>
							{/if}
							{* ref 2 last name *}
							{if $use_field.mm_ref_2_last_name & SB_EDIT}
								<div style="padding-bottom:5px;" class="txtblack">
									<label title="นามสกุล">
										<font class="txtblue">{$lang.users.sname}:</font> {if $data_1.mm_ref_2_last_name}
											{$data_1.mm_ref_2_last_name}
										{elseif $mandatory.mm_ref_2_last_name & SB_EDIT}
											<font color="red">{$lang.mm_missing}</font>
										{/if}
									</label>
								</div>
							{/if}
							{* ref 2 relationship *}
							{if $use_field.mm_ref_2_relationship & SB_EDIT}
								<div style="padding-bottom:5px;" class="txtblack">
									<label title="ความสัมพันธ์">
										<font class="txtblue">{$lang.users.mm_reference_relationship}:</font> {if $data_1.mm_ref_2_relationship}
											{$data_1.mm_ref_2_relationship}
										{elseif $mandatory.mm_ref_2_relationship & SB_EDIT}
											<font color="red">{$lang.mm_missing}</font>
										{/if}
									</label>
								</div>
							{/if}
							{* ref 2 phone number *}
							{if $use_field.mm_ref_2_phone_number & SB_EDIT}
								<div style="padding-bottom:5px;" class="txtblack">
									<label title="หมายเลขโทรศัพท์">
										<font class="txtblue">{$lang.users.mm_reference_phone_number}:</font> {if $data_1.mm_ref_2_phone_number}
											{$data_1.mm_ref_2_phone_number}
										{elseif $mandatory.mm_ref_2_phone_number & SB_EDIT}
											<font color="red">{$lang.mm_missing}</font>
										{/if}
									</label>
								</div>
							{/if}
						{/if}
						
						{* I'M LOOKING FOR A MAN / WOMAN *}
						<div class="titlebg">
							<div class="fleft">
								{if $data_1.gender == '1'}
									<label title="{$lang.looking_for_woman_thai}">{$lang.profile_head.looking_for_woman}</label>
								{else}
									<label title="{$lang.looking_for_man_thai}">{$lang.profile_head.looking_for_man}</label>
								{/if}
							</div>
							<div class="edit_link"><a href="{$edit_link}">edit</a></div>
							<div class="clear"></div>
						</div>
						{* {$data_1.search_gender} ({if $data_1.search_couple}{$lang.users.couple}{else}{$lang.users.single}{/if}) *}
						<div style="padding-bottom:0px;" class="txtblack">
							<label title="{$lang.age_range_thai}">
								<font class="txtblue">{$lang.users.age_range}:</font> {$data_1.min_age} - {$data_1.max_age} {$lang.profile.years}
							</label>
						</div>
						{if $use_field.id_relationship & SB_EDIT}
							<div style="padding-bottom:5px;" class="txtblack">
								<label title="Relationship">
									<font class="txtblue">{$lang.users.relationship}:</font> {$data_1.relationship}
								</label>
							</div>
						{/if}
						
						{* OTHER *}
						{if $use_field.id_region & SB_EDIT && $data.gender == 2 || $use_field.id_city & SB_EDIT || $use_field.headline & SB_EDIT}
							<br>
							<div class="titlebg">
								<div class="fleft">{$lang.profile_head.other}</div>
								<div class="edit_link"><a href="{$edit_link}">edit</a></div>
								<div class="clear"></div>
							</div>
							{* city *}
							{if $use_field.id_city & SB_EDIT}
								<div style="padding-bottom:5px;" class="txtblack">
									<font class="txtblue">{$lang.users.city}:</font> {if $data_1.city}
										{$data_1.city}
									{elseif $mandatory.id_city & SB_EDIT}
										<font color="red">{$lang.mm_missing}</font>
									{/if}
								</div>
							{/if}
							{* headline *}
							{if $use_field.headline & SB_EDIT}
								<div style="padding-bottom:5px;" class="txtblack">
									<font class="txtblue">{$lang.users.headline}:</font> {if $data_1.headline}
										{$data_1.headline}
									{elseif $mandatory.headline & SB_EDIT}
										<font color="red">{$lang.mm_missing}</font>
									{/if}
								</div>
							{/if}
						{/if}
						
						{* PRIVACY SETTINGS *}
						{if $use_field.privacy_settings & SB_EDIT}
							<br>
							<div class="titlebg">
								<div class="fleft"><label title="{$lang.privacy_settings_thai}">{$lang.profile_head.privacy_settings}</label></div>
								<div class="edit_link"><a href="{$edit_link}">edit</a></div>
								<div class="clear"></div>
							</div>
							{* online privacy *}
							{if $use_field.online_privacy & SB_EDIT}
								<div style="padding-bottom:5px;" class="txtblack">
									<font class="txtblue"><label title="{$lang.privacy_online_title_thai}">{$lang.users.privacy_online_title}:</label></font><br>
									{if $data_1.hide_online == 1}
										<label title="{$lang.privacy_online_hide_thai}">{$lang.users.privacy_online_hide}</label>
									{else}
										<label title="{$lang.privacy_online_show_thai}">{$lang.users.privacy_online_show}</label>
									{/if}
								</div>
							{/if}
							{* privacy female *}
							{if $use_field.privacy_female & SB_EDIT}
								<div style="padding-bottom:5px;" class="txtblack">
									<font class="txtblue"><label title="{$lang.privacy_female_thai}">{$lang.users.privacy_female}:</label></font><br>
									{if $data_1.visible_lady == 0 || $data_1.visible_lady == 1 || $data_1.visible_lady == 2}
										{if $data_1.visible_lady==0}<label title="{$lang.visible_to_no_ladies_thai}">{$lang.users.privacy_lady_none}</label>{/if}
										{if $data_1.visible_lady==1}<label title="{$lang.visible_to_all_ladies_thai}">{$lang.users.privacy_lady_all}</label>{/if}
										{if $data_1.visible_lady==2}
											{if $data_1.vis_lady_1==1}<label title="{$lang.visible_to_trial_ladies_thai}">{$lang.users.privacy_lady_trial}</label><br>{/if}
											{if $data_1.vis_lady_2==1}<label title="{$lang.visible_to_regular_ladies_thai}">{$lang.users.privacy_lady_regular}</label><br>{/if}
											{if $data_1.vis_lady_3==1}<label title="{$lang.visible_to_platinum_ladies_thai}">{$lang.users.privacy_lady_platinum}</label>{/if}
										{/if}
									{elseif $mandatory.privacy_female & SB_EDIT}
										<font color="red">{$lang.mm_missing}</font>
									{/if}
								</div>
							{/if}
							{* privacy male *}
							{if $use_field.privacy_male & SB_EDIT}
								<div style="padding-bottom:5px;" class="txtblack">
									<font class="txtblue"><label title="{$lang.privacy_male_thai}">{$lang.users.privacy_male}:</label></font><br>
									{if $data_1.visible_guy == 0 || $data_1.visible_guy == 1 || $data_1.visible_guy == 2}
										{if $data_1.visible_guy==0}<label title="{$lang.visible_to_no_guys_thai}">{$lang.users.privacy_guy_none}</label>{/if}
										{if $data_1.visible_guy==1}<label title="{$lang.visible_to_all_guys_thai}">{$lang.users.privacy_guy_all}</label>{/if}
										{if $data_1.visible_guy==2}
											{if $data_1.vis_guy_1==1}<label title="{$lang.visible_to_trial_guys_thai}">{$lang.users.privacy_guy_trial}</label><br>{/if}
											{if $data_1.vis_guy_2==1}<label title="{$lang.visible_to_regular_guys_thai}">{$lang.users.privacy_guy_regular}</label><br>{/if}
											{if $data_1.vis_guy_3==1}<label title="{$lang.visible_to_platinum_guys_thai}">{$lang.users.privacy_guy_platinum}</label><br>{/if}
											{if $data_1.vis_guy_4==1}<label title="{$lang.visible_to_elite_guys_thai}">{$lang.users.privacy_guy_elite}</label>{/if}
										{/if}
									{elseif $mandatory.privacy_male & SB_EDIT}
										<font color="red">{$lang.mm_missing}</font>
									{/if}
								</div>
							{/if}
							{* promotion *}
							{if $use_field.promotion & SB_EDIT}
								<div style="padding-bottom:5px;" class="txtblack">
									<font class="txtblue"><label title="{$lang.privacy_promotion_thai}">{$lang.users.privacy_promo}:</label></font><br>
									{if $data_1.promotion_1 != "" || $data_1.promotion_2 != "" || $data_1.promotion_3 != ""}
										{if $data_1.promotion_1 == 1}<label title="{$lang.promote_no_thai}">{$lang.users.promote_no}</label><br>{/if}
										{if $data_1.promotion_2 == 1}<label title="{$lang.promote_within_thai}">{$lang.users.promote_within}</label><br>{/if}
										{if $data_1.promotion_3 == 1}<label title="{$lang.promote_prospective_thai}">{$lang.users.promote_prospective}</label>{/if}
									{elseif $mandatory.promotion & SB_EDIT}
										<font color="red">{$lang.mm_missing}</font>
									{else}
										{$lang.no_selection}
									{/if}
								</div>
							{/if}	
						{/if}
						
						{* BIOGRAPHY *}
						{if $use_field.biography & SB_EDIT}
							<br>
							<div class="titlebg">
								<div class="fleft"><label title="{$lang.biography_thai}">{$lang.profile_head.biography}</label></div>
								<div class="edit_link"><a href="{$edit_link}">edit</a></div>
								<div class="clear"></div>
							</div>
							{* about me *}
							{if $use_field.about_me & SB_EDIT}
								<div style="padding-bottom:5px;" class="txtblack">
									<label title="{$lang.about_me_thai}">
										<font class="txtblue">{$lang.users.about_me}:</font> {if $data_1.about_me}
											<br>{$data_1.about_me|nl2br}
										{elseif $mandatory.about_me & SB_EDIT}
											<font color="red">{$lang.mm_missing}</font>
										{/if}
									</label>
								</div>
							{/if}
							{* what I do *}
							{if $use_field.what_i_do & SB_EDIT}
								<div style="padding-bottom:5px;" class="txtblack">
									<label title="{$lang.what_i_do_thai}">
										<font class="txtblue">{$lang.users.what_i_do}:</font> {if $data_1.what_i_do}
											<br>{$data_1.what_i_do|nl2br}
										{elseif $mandatory.what_i_do & SB_EDIT}
											<font color="red">{$lang.mm_missing}</font>
										{/if}
									</label>
								</div>
							{/if}
							{* perfect weekend *}
							{if $use_field.my_idea & SB_EDIT}
								<div style="padding-bottom:5px;" class="txtblack">
									<label title="{$lang.my_idea_thai}">
										<font class="txtblue">{$lang.users.my_idea}:</font> {if $data_1.my_idea}
											<br>{$data_1.my_idea|nl2br}
										{elseif $mandatory.my_idea & SB_EDIT}
														v\<font color="red">{$lang.mm_missing}</font>
										{/if}
									</label>
								</div>
							{/if}
							{* hoping to find *}
							{if $use_field.hoping_to_find & SB_EDIT}
								<div style="padding-bottom:5px;" class="txtblack">
									<label title="{$lang.hoping_to_find_thai}">
										<font class="txtblue">{$lang.users.hoping_to_find}:</font> {if $data_1.hoping_to_find}
											<br>{$data_1.hoping_to_find|nl2br}
										{elseif $mandatory.hoping_to_find & SB_EDIT}
											<font color="red">{$lang.mm_missing}</font>
										{/if}
									</label>
								</div>
							{/if}
						{/if}
					</td>
				</tr>
			</table>
		</div>
		
		{* my notice *}
		{* <!-- VP Not required now
			{if !$auth.is_applicant}
				<div style="height:1px; margin:5px 10px" class="delimiter"></div>
				<div style="margin:10px;">
					<div class="header">{$lang.subsection.notice}</div>
					<div class="sep"></div>
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td align="left">
								<div class="center" style="width:140px;">
									<div class="btnwrap" style="width:140px;">
										<span><span>
										<input type="button" class="button" style="width:120px;" onclick="window.location.href='./myprofile.php?sel=edit_3';" value="{$button.edit_this_section}">
										</span></span>
									</div>
								</div>
							</td>
							<td align="right">
								<font class="{if $data_3.complete > 50}link{else}text{/if}_active">
									{if $data_3.complete < 5}
										{$lang.profile.not_complited}
									{elseif $data_3.complete >= 5 && $data_3.complete < 95}
										{$lang.profile.filled_on} {$data_3.complete}%
									{elseif $data_3.complete >=95}
										{$lang.profile.complited}
									{/if}
								</font>
							</td>
						</tr>
					</table>
					{if $data_3.complete < 50}
						<div align="center">
							<div style="padding-bottom:10px; width:320px;">
								<font class="txtblack">{$lang.subsection.notice_text}</font>
							</div>
						</div>
					{else}
						<div style="padding-top:7px;padding-bottom:5px;">
							<font class="text_hidden">{$data_3.annonce}</font>
						</div>
					{/if}
				</div>
			{/if}
		--> *}
	</div>
{/if}

{* PAGE TWO *}
{if $form.page == '2'}
	<div>
		{* my description *}
		{if !$auth.is_applicant}
			<div style="margin:10px;">
				{* <!-- <div class="header">{$lang.subsection.description}</div> --> *}
				<div class="sep"></div>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td align="left">
							<p class="basic-btn_next">
								<span><input type="button" onclick="window.location.href='./myprofile.php?sel=edit_2';" value="{$button.edit_this_section}"></span><b>&nbsp;</b>
							</p>
						</td>
						<td align="right">
							<font class="{if $data_2.complete > 50}link{else}text{/if}_active">
								{if $data_2.complete < 5}
									{$lang.profile.not_complited}
								{elseif $data_2.complete >= 5 && $data_2.complete < 95}
									{$lang.profile.filled_on} {$data_2.complete}%
								{elseif $data_2.complete >=95}
									{$lang.profile.complited}
								{/if}
							</font>
						</td>
					</tr>
				</table>
				{if $data_2.complete < 50}
					<div align="center">
						<div style="padding-bottom:10px; width:320px;">
							<font class="txtblack">{$lang.subsection.description_text}</font>
						</div>
					</div>
				{/if}
				<div style="padding:5px 0px;">
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr class="edit-info">
							<td width="50%" style="padding-bottom:5px;" class="txtblack" align="left">
								<font class="txtblue">{$lang.users.weight}:</font> {$data_2.weight}
							</td>
							<td width="50%" style="padding-bottom:5px;" class="txtblack" align="left">
								<font class="txtblue">{$lang.users.height}:</font> {$data_2.height}
							</td>
						</tr>
						{foreach item=item from=$data_2.info name=a}
							{if $smarty.foreach.a.index is div by 2}
								<tr class="edit-info">
							{/if}
							<td width="50%" style="padding-bottom:5px;" class="txtblack" align="left">
								<font class="txtblue">{$item.spr|escape}:</font> {$item.value|escape}
							</td>
							{if $smarty.foreach.a.index is not div by 2 || $smarty.foreach.a.last}
								</tr>
							{/if}
						{/foreach}
					</table>
				</div>
			</div>
		{/if}
		
		{* my personality *}
		{*< !-- VP not required now
		<div style="height:1px; margin:5px 10px" class="delimiter"></div>
		<div style="padding:10px;">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr valign="middle">
					<td height="25" colspan="2">
						<div class="header">{$lang.subsection.personal}</div>
						<div class="sep"></div>
					</td>
				</tr>
				<tr>
					<td align="left">
						<div class="center" style="width:140px;">
							<div class="btnwrap" style="width:140px;">
								<span><span>
								<input type="button" class="button" style="width:120px;" onclick="window.location.href='./myprofile.php?sel=edit_4';" value="{$button.edit_this_section}">
								</span></span>
							</div>
						</div>
					</td>
					<td align="right">
						<font class="{if $data_4.complete > 50}link{else}text{/if}_active">
							{if $data_4.complete < 5}
								{$lang.profile.not_complited}
							{elseif $data_4.complete >= 5 && $data_4.complete < 95}
								{$lang.profile.filled_on} {$data_4.complete}%
							{elseif $data_4.complete >= 95}
								{$lang.profile.complited}
							{/if}
						</font>
					</td>
				</tr>
				{if $data_4.complete < 50}
					<tr>
						<td colspan="2" align="center">
							<div style="margin:10px 0px;">
								<font class="txtblack">{$lang.subsection.personal_text}</font>
							</div>
						</td>
					</tr>
				{else}
					<tr>
						<td colspan="2">
							<div style="margin:5px 0px;">
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									{foreach item=item from=$data_4.personal name=a}
										{if $smarty.foreach.a.index is div by 2}
											<tr>
										{/if}
										<td width="50%" style="padding-bottom:5px;" class=text align="left">
											<font class="txtblue">{$item.name|escape}:</font> {$item.value|escape}
										</td>
										{if $smarty.foreach.a.index is not div by 2 || $smarty.foreach.a.last}
											</tr>
										{/if}
									{/foreach}
								</table>
							</div>
						</td>
					</tr>
				{/if}
			</table>
		</div>
		<div style="height:1px; margin:5px 10px;" class="delimiter"></div>
		-->*}
		
		{* my portrait *}
		{* <!-- VP not required now
		<div style=" margin:10px;">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr valign="middle">
					<td height="25" colspan="2">
						<div class="header">{$lang.subsection.portreit}</div>
						<div class="sep"></div>
					</td>
				</tr>
				<tr>
					<td align="left">
						<div class="center" style="width:140px;">
							<div class="btnwrap" style="width:140px;">
								<span><span>
								<input type="button" class="button" style="width:120px;" onclick="window.location.href='./myprofile.php?sel=edit_5';" value="{$button.edit_this_section}">
								</span></span>
							</div>
						</div>
					</td>
					<td align="right">
						<font class="{if $data_5.complete > 50}link{else}text{/if}_active">
							{if $data_5.complete < 5}
								{$lang.profile.not_complited}
							{elseif $data_5.complete >= 5 && $data_5.complete < 95}
								{$lang.profile.filled_on} {$data_5.complete}%
							{elseif $data_5.complete >=95}
								{$lang.profile.complited}
							{/if}
						</font>
					</td>
				</tr>
				{if $data_5.complete < 50}
					<tr>
						<td colspan="2" align="center">
							<div style="margin:10px 0px;">
								<font class="txtblack">{$lang.subsection.portreit_text}</font>
							</div>
						</td>
					</tr>
				{else}
					<tr>
						<td colspan="2">
							<div style="margin:5px 0px;">
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									{section name=s loop=$data_5.portrait}
										{if $smarty.section.s.index is div by 2}
											<tr>
										{/if}
										<td width="50%" style="padding-bottom:5px;" class="txtblack" align="left">
											<font class="txtblue">{$data_5.portrait[s].name}:</font> {$data_5.portrait[s].value}
										</td>
										{if $smarty.section.s.index_next is div by 2 || $smarty.section.s.last}
											</tr>
										{/if}
									{/section}
								</table>
							</div>
						</td>
					</tr>
				{/if}
			</table>
		</div>
		<div style="height:1px; margin:5px 10px;" class="delimiter"></div>
		-->*}
		
		{* my interests *}
		{*< !-- VP not required now
		<div style=" margin:10px;">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr valign="middle">
					<td height="25" colspan="2">
						<div class="header">{$lang.subsection.interest}</div>
						<div class="sep"></div>
					</td>
				</tr>
				<tr>
					<td align="left">
						<div class="center" style="width:140px;">
							<div class="btnwrap" style="width:140px;">
								<span><span>
								<input type="button" class="button" style="width:120px;" onclick="window.location.href='./myprofile.php?sel=edit_6';" value="{$button.edit_this_section}">
								</span></span>
							</div>
						</div>
					</td>
					<td align="right">
						<font class="{if $data_6.complete > 50}link{else}text{/if}_active">
							{if $data_6.complete < 5}
								{$lang.profile.not_complited}
							{elseif $data_6.complete >= 5 && $data_6.complete < 95}
								{$lang.profile.filled_on} {$data_6.complete}%
							{elseif $data_6.complete >=95}
								{$lang.profile.complited}
							{/if}
						</font>
					</td>
				</tr>
				{if $data_6.complete < 50}
					<tr>
						<td colspan="2" align="center">
							<div style="margin:10px 0px;">
								<font class="txtblack">{$lang.subsection.interest_text}</font>
							</div>
						</td>
					</tr>
				{else}
					<tr>
						<td colspan="2">
							<div style="margin:5px 0px;">
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									{section name=s loop=$data_6.interests}
										{if $smarty.section.s.index is div by 3}
											<tr>
										{/if}
										<td width="33%" style="padding-bottom:5px;" class="txtblack" align="left">
											<font class="txtblue">I <img src="{$site_root}{$template_root}/images/int_{$data_6.interests[s].value}.gif" alt="{$data_6.interests[s].lang_value}" align="middle"> {$data_6.interests[s].name}</font>
										</td>
										{if $smarty.section.s.index_next is div by 3 || $smarty.section.s.last}
											</tr>
										{/if}
									{/section}
								</table>
							</div>
						</td>
					</tr>
				{/if}
			</table>
		</div>
		-->*}
	</div>
{/if}

{* PAGE THREE *}
{if $form.page == '3'}
	<div>
		{* my criteria *}
		<div class="hdr2">{$lang.subsection.criteria}</div>
		<div>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td align="left">
						<p class="basic-btn_next">
							<span><input type="button" onclick="window.location.href='./myprofile.php?sel=edit_7';" value="{$button.edit_this_section}"></span><b>&nbsp;</b>
						</p>
					</td>
					<td align="right">
						<font class="{if $data_7.complete > 50}link{else}text{/if}_active">
							{if $data_7.complete < 5}
								{$lang.profile.not_complited}
							{elseif $data_7.complete >= 5 && $data_7.complete < 95}
								{$lang.profile.filled_on} {$data_7.complete}%
							{elseif $data_7.complete >=95}
								{$lang.profile.complited}
							{/if}
						</font>
					</td>
				</tr>
				{if $data_7.complete < 50}
					<tr>
						<td colspan="2" align="center">
							<div style="margin:10px 0px;">
								<font class="txtblack">{$lang.subsection.criteria_text}</font>
							</div>
						</td>
					</tr>
				{/if}
				<tr>
					<td colspan="2" align="center">
						<div style="margin:5px 0px;">
							<table width="100%" border="0" cellpadding="1" cellspacing="0">
								<tr class="edit-info">
									<td width="50%" style="padding-bottom:5px;" class="txtblack" align="left">
										<font class="txtblue">{$lang.users.weight}:</font> {$data_7.weight}
									</td>
									<td width="50%" style="padding-bottom:5px;" class="txtblack" align="left">
										<font class="txtblue">{$lang.users.country}:</font> {$data_7.country_match}
									</td>
								</tr>
								<tr class="edit-info">
									<td width="50%" style="padding-bottom:5px;" class="txtblack" align="left">
										<font class="txtblue">{$lang.users.height}:</font> {$data_7.height}
									</td>
									<td width="50%" style="padding-bottom:5px;" class="txtblack" align="left">
										<font class="txtblue">{$lang.users.language}:</font> {$data_7.language}
									</td>
								</tr>
								<tr class="edit-info">
									<td width="50%" style="padding-bottom:5px;" class="txtblack" align="left">
										<font class="txtblue">{$lang.users.nationality}:</font> {$data_7.nationality_match}
									</td>
								{* missing tr is intentional, foreach loop starts in column 2 *}
								{foreach item=item from=$data_7.info name=a}
									{if $smarty.foreach.a.index is not div by 2}
										<tr class="edit-info">
									{/if}
									<td width="50%" style="padding-bottom:5px;" class="txtblack" align="left">
										<font class="txtblue">{$item.name|escape}:</font> {$item.value|escape}
									</td>
									{if $smarty.foreach.a.index is div by 2 || $smarty.foreach.a.last}
										</tr>
									{/if}
								{/foreach}
							</table>
						</div>
					</td>
				</tr>
			</table>
		</div>
		
		{* his interests *}
		{* <!--VP
		<div style="height:1px; margin: 5px 10px;" class="delimiter"></div>
		<div style=" margin:10px;">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr valign="middle">
					<td height="25" colspan="2">
						<div class="header">{$lang.subsection.match_interest}</div>
						<div class="sep"></div>
					</td>
				</tr>
				<tr>
					<td align="left">
						<div class="center" style="width:140px;">
							<div class="btnwrap" style="width:140px;">
								<span><span>
								<input type="button" class="button" style="width:120px;" onclick="window.location.href='./myprofile.php?sel=edit_8';" value="{$button.edit_this_section}">
								</span></span>
							</div>
						</div>
					</td>
					<td align="right">
						<font class="{if $data_8.complete > 50}link{else}text{/if}_active">
							{if $data_8.complete < 5}
								{$lang.profile.not_complited}
							{elseif $data_8.complete >= 5 && $data_8.complete < 95}
								{$lang.profile.filled_on} {$data_8.complete}%
							{elseif $data_8.complete >=95}
								{$lang.profile.complited}
							{/if}
						</font>
					</td>
				</tr>
				{if $data_8.complete < 50}
					<tr>
						<td colspan="2" align="center">
							<div style="margin:10px 0px;">
								<font class="txtblack">{$lang.subsection.match_interest_text}</font>
							</div>
						</td>
					</tr>
				{else}
					<tr>
						<td colspan="2" align="center">
							<div style="margin:5px 0px;">
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									{section name=s loop=$data_8.interests}
										{if $smarty.section.s.index is div by 3}
											<tr>
										{/if}
										<td width="33%" style="padding-bottom:5px;" class="txtblack" align="left">
											{if $data_8.interests[s].value_1 || $data_8.interests[s].value_2 || $data_8.interests[s].value_3}
												<font class="txtblue">
													{$lang.profile.soulmate}
													{if $data_8.interests[s].value_1 == 1}
														<img src="{$site_root}{$template_root}/images/int_1.gif" alt="{$lang.interests_opt.1}" align="middle">
													{/if}
													{if $data_8.interests[s].value_2 == 1}
														<img src="{$site_root}{$template_root}/images/int_2.gif" alt="{$lang.interests_opt.2}" align="middle">
													{/if}
													{if $data_8.interests[s].value_3 == 1}
														<img src="{$site_root}{$template_root}/images/int_3.gif" alt="{$lang.interests_opt.3}" align="middle">
													{/if}
													{$data_8.interests[s].name}
												</font>
											{/if}
										</td>
										{if $smarty.section.s.index_next is div by 3 || $smarty.section.s.last}
											</tr>
										{/if}
									{/section}
								</table>
							</div>
						</td>
					</tr>
				{/if}
			</table>
		</div>
		-->*}
	</div>
{/if}

{* PAGE FOUR *}
{if $form.page == '4'}
	{*
		MULTIMEDIA: ICON, PHOTOS, AUDIO, VIDEO
	*}
	<script type="text/javascript" src="{$site_root}{$template_root}/js/upload_multimedia.js?v=0001"></script>
	<script type="text/javascript">
	if ('{$form.err}' != '') jAlert('{$form.err}');
	</script>
	<div>
		<ul class="nav-tab-inside tcxf-ch-la">
			<li id="sub_menu7" style="height:35px; width:100px; text-align:center;" class="sub_tab{if $data_9.form_sub_page == '7'}_active{/if}_first">
				<a href="javascript:void(0);" id="sub_link7" onclick="ShowTab(7, './myprofile.php?sel=4&amp;sub=7&amp;action=album_list'); return false;">{$lang.section.icon}</a>
			</li>
			<li id="sub_menu8" style="height:35px; width:100px; text-align:center;" class="sub_tab{if $data_9.form_sub_page == '8'}_active{/if}">
				<a href="javascript:void(0);" id="sub_link8" onclick="ShowTab(8, './myprofile.php?sel=4&amp;sub=8&amp;action=album_list'); return false;">{$lang.section.photos}</a>
			</li>
			<li id="sub_menu9" style="height:35px; width:100px; text-align:center;" class="sub_tab{if $data_9.form_sub_page == '9'}_active{/if}">
				<a href="javascript:void(0);" id="sub_link9" onclick="ShowTab(9, './myprofile.php?sel=4&amp;sub=9&amp;action=album_list'); return false;">{$lang.section.audio}</a>
			</li>
			<li id="sub_menu10" style="height:35px; width:100px; text-align:center;" class="sub_tab{if $data_9.form_sub_page == '10'}_active{/if}">
				<a href="javascript:void(0);" id="sub_link10" onclick="ShowTab(10, './myprofile.php?sel=4&amp;sub=10&amp;action=album_list'); return false;">{$lang.section.video}</a>
			</li>
		</ul>
	</div>
	{* upload sections *}
	<div class="media-info">
		<div style=" padding:10px;">
			{if $data_9.form_sub_page == '7'}
				{*
					ICON UPLOAD
				*}
				{* <p class="hdr2x"> {$lang.profile.upload_text}</p> *}
				<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td valign="top" width="1%" style="padding-right:10px;">
							<img src="{if $data_9.icon_thumb_path}{$data_9.icon_thumb_path}{else}{$data_9.icon_path}{/if}" border="0" class="icon" alt="">
						</td>
						<td valign="top" height="30" align="left">
							<p> <b>{$lang.profile.help_tip.icon}</b> </p>
							<form name="upload_form" id="upload_form" action="myprofile.php?sel=save_9&amp;upload_type=icon" method="post" enctype="multipart/form-data">
								<input type="hidden" name="MAX_FILE_SIZE" value="{$data_9.max_file_size_bytes}">
								<input type="hidden" name="timestamp" value="{$data.timestamp}">
								<input type="hidden" name="token" value="{$data.token}">
								<p class="txtblue" style="font-size:18px;">
									{if $data_9.icon_del_link}
										{$lang.profile.change_icon}
									{else}
										{$lang.profile.add_new_icon}
									{/if}
								</p>
								<div style="margin-bottom: 8px;">
									<input type="file" name="file_upload" id="file_upload" {if $data_9.root}disabled{/if}>
								</div>
								<div id="file_info" style="display:none;">
									<div id="file_name" style="margin-bottom:8px; font-size:12px;"></div>
									<progress id="prog" value="0" style="width:300px;"></progress>
								</div>
								<div {if $data_9.icon_del_link}class="tcxf-ch-la"{/if}>
									<label title="{$lang.button_upload_thai}">
										<p class="basic-btn_here">
											<b>&nbsp;</b>
											<span><input type="submit" id="submit" value="{$button.save}"></span>
										</p>
									</label>
									{if $data_9.icon_del_link}
										<label title="{$lang.button_delete_thai}">
											<p class="basic-btn_here" style="margin-left:10px;">
												<b>&nbsp;</b>
												<span><input type="button" value="{$button.delete}" onclick="location.href='{$data_9.icon_del_link}';"></span>
											</p>
										</label>
									{/if}
								</div>
							</form>
							<script type="text/javascript">
							$(function() {ldelim}
								custom_file_upload(
									'', 7,
									{$data_9.max_file_size_bytes}, '{$data_9.max_file_size_string}',
									'{$data_9.file_exts}', '{$data_9.file_types}', 'Image Files', 'Select Photo', 'add', '{$form.session_id}');
							{rdelim});
							</script>
						</td>
					</tr>
				</table>
			{elseif $data_9.form_sub_page == 8 || $data_9.form_sub_page == 9 || $data_9.form_sub_page == 10}
				{*
					PHOTOS, AUDIO, VIDEO
				*}
				<div class="txtblack" align="left">
					<table cellpadding="0" cellspacing="0">
						{* INSTRUCTIONS *}
						<tr>
							<td style="padding-bottom:7px; font-weight:bold;" colspan="2">
								{if $data_9.form_sub_page == 8}
									{$lang.profile.help_tip.photo}
								{elseif $data_9.form_sub_page == 9}
									{$lang.profile.help_tip.audio}
								{elseif $data_9.form_sub_page == 10}
									{$lang.profile.help_tip.video}
								{/if}
							</td>
						</tr>
						{* ALBUM COUNT *}
						<tr>
							<td style="padding-bottom:7px;" class="txtblack">
								{if $data_9.form_sub_page == 8}
									{$lang.users.photo_album_count}
								{elseif $data_9.form_sub_page == 9}
									{$lang.users.audio_album_count}
								{elseif $data_9.form_sub_page == 10}
									{$lang.users.video_album_count}
								{/if}:
							</td>
							<td style="padding-bottom:7px;" class="txtblack">
								{if $album_page.show_more_album_link == 1}
									<a href="javascript:void(0);" onclick="ShowTab({$data_9.form_sub_page}, './myprofile.php?sel=4&amp;sub={$data_9.form_sub_page}&amp;action=album_list'); return false;">{$album_page.album_count}</a>
								{else}
									{$album_page.album_count}
								{/if}
							</td>
						</tr>
						{* ITEMS COUNT *}
						<tr>
							<td style="padding-bottom:7px;" class="txtblack">
								{if $data_9.form_sub_page == 8}
									{$lang.users.photos_count}
								{elseif $data_9.form_sub_page == 9}
									{$lang.users.audio_count}
								{elseif $data_9.form_sub_page == 10}
									{$lang.users.video_count}
								{/if}:
							</td>
							<td style="padding-bottom:7px;" class="txtblack">
								{$album_page.items_count}
							</td>
						</tr>
						{* CREATE ALBUM BUTTON *}
						<tr>
							<td colspan="2" style="padding-bottom:7px; padding-top:12px;"> 
								<div class="basic-btn_here tooltip" title="{$lang.users.create_album_thai}" style="float:left; margin-right:10px;">
									<b>&nbsp;</b><span>
									<input type="button" style="width:120px;" onclick="ShowTab({$data_9.form_sub_page}, './myprofile.php?sel=4&amp;sub={$data_9.form_sub_page}&amp;action=create_album');" value="{$lang.users.create_album}">
									</span>
								</div>
								<label title="
									{if $album_page.upload_type == 'f'}
										{$lang.profile.help_tip.create_photo_album}
									{elseif $album_page.upload_type == 'a'}
										{$lang.profile.help_tip.create_audio_album}
									{elseif $album_page.upload_type == 'v'}
										{$lang.profile.help_tip.create_video_album}
									{/if}
									">
									<img src="{$site_root}{$template_root}/images/question_icon.gif">
								</label>
							</td>
						</tr>
					</table>
				</div>
				<div style="height:1px; margin:10px 0px 10px 0px;" class="delimiter"></div>
				{if $album_page.album_count > 0 && $album_page.show_create_form == 0 && $album_page.show_album_items == 0}
					{*
						ALBUM LIST
					*}
					<table cellpadding="0" cellspacing="0" width="100%" border="0">
						{foreach item=item from=$_album name=k}
							{if $smarty.foreach.k.index is div by 2}
								<tr>
									<td width="50%" align="left">
							{else}
								<td width="50%" style="padding-left:20px;" align="left">
							{/if}
							<table cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td style="padding-right:5px;padding-top:0px;" valign="top" align="center">
										<a href="javascript:void(0);" onclick="ShowTab({$data_9.form_sub_page}, './myprofile.php?sel=4&amp;sub={$data_9.form_sub_page}&amp;action=browse_album&amp;id_album={$item.id}'); return false;">
										<img src="{$album_page._album_icon}" class="icon"><br>Browse</a>
									</td>
									<td valign="top">
										<div style="padding-bottom:7px;" class="hdr2x">
											<a href="javascript:void(0);" onclick="ShowTab({$data_9.form_sub_page}, './myprofile.php?sel=4&amp;sub={$data_9.form_sub_page}&amp;action=browse_album&amp;id_album={$item.id}'); return false;">{$item.title}</a>
										</div>
										<div style="padding-bottom:7px;" class="txtblack">
											{$item.description|nl2br}
										</div>
										<div style="padding-bottom:7px;" class="text_hidden">
											{$lang.users.created}: {$item.creation_date}
										</div>
										<div style="padding-bottom:7px;" class="txtblack">
											{if $album_page.upload_type == 'f'}
												{$lang.users.photos_count}
											{elseif $album_page.upload_type == 'a'}
												{$lang.users.audio_count}
											{elseif $album_page.upload_type == 'v'}
												{$lang.users.video_count}
											{/if}
											: {$item.items_count}
										</div>
										<div style="padding-bottom:7px;" class="txtblack">
											{* EDIT / DELETE ALBUM BUTTONS *}
											<a href="javascript:void(0);" class="normal-btn" onclick="ShowTab({$data_9.form_sub_page}, './myprofile.php?sel=4&amp;sub={$data_9.form_sub_page}&amp;action=edit_album&amp;id_album={$item.id}'); return false;">{$lang.users.edit}</a> &nbsp;
											<a class="normal-btn" href="myprofile.php?sel=del_album&amp;id_album={$item.id}&amp;sub={$data_9.form_sub_page}">{$lang.users.delete}</a>
										</div>
									</td>
								</tr>
							</table>
							<div style="height:1px; margin:5px 0px 7px 0px;" class="delimiter"></div>
							{if $smarty.foreach.k.index is not div by 2}
									</td>
								</tr>
							{else}
								</td>
							{/if}
						{/foreach}
					</table>
					{if $links_page}
						<div style="margin-left:10px">
							{foreach item=item from=$links_page}
								<div class="page_div{if $item.selected == '1'}_active{/if}">
									<div style="margin:5px">
										<a href="javascript:void(0);" onclick="ShowTab({$data_9.form_sub_page}, './myprofile.php?sel=4&amp;sub={$data_9.form_sub_page}&amp;action={$data_9.form_act}{$item.link}'); return false;" class="page_link{if $item.selected eq '1'}_active{/if}">{$item.name}</a>
									</div>
								</div>
							{/foreach}
						</div>
					{/if}
				{elseif $album_page.show_album_items == 1}
					{if $data_9.form_sub_page == 8}
						{*
							PHOTOS IN SELECTED ALBUM
						*}
						<div class="hdr2x">
							{$data_9.album_title}
						</div>
						<div class="txtblack">
							{$lang.users.photos_in_album}: {$data_9.num_items}
						</div>
						{if $photos}
							<div class="txtblack" style="font-weight:bold;">
								{$data_9.photo_comment}
							</div>
							<table cellpadding="0" cellspacing="0" border="0">
								<tr><td height="1" colspan="2" bgcolor="#CCCCCC"></td></tr>
								<tr><td height="10" colspan="2"></td></tr>
								{foreach item=item from=$photos name=photos}
									<tr valign="top">
										<td width="80" style="padding-top:35px;">
											{if $item.thumb_file}
												{if $item.view_link}
													<a href="javascript:void(0);" onclick="window.open('{$item.view_link}', 'photo_view', 'menubar=0,resizable=1,scrollbars=0,status=0,toolbar=0,width=1024,height=800');return false;">
												{/if}
												<img src="{$item.thumb_file}" border="0" {$item.sizes} class="icon" alt="">
												{if $item.view_link}
													</a>
												{/if}
											{/if}
										</td>
										<td>
											<form id="upload_form_{$smarty.foreach.photos.index}" action="myprofile.php?sel=save_9&amp;upload_type=f" method="post" enctype="multipart/form-data">
												<input type="hidden" name="MAX_FILE_SIZE" value="{$data_9.max_file_size_bytes}">
												<input type="hidden" name="timestamp" value="{$data_9.timestamp}">
												<input type="hidden" name="token" value="{$data_9.token}">
												<input type="hidden" name="id_album" value="{$data_9.id_album}">
												<input type="hidden" name="id_file" value="{$item.id}">
												<div style="padding-bottom:10px; font-size:18px;" class="txtblue">
													{$lang.profile.change_photo}
												</div>
												<div class="txtblack">
													<input type="file" name="file_upload" id="file_upload_{$smarty.foreach.photos.index}" {if $data.root}disabled{/if}>
													<div id="file_info_{$smarty.foreach.photos.index}" style="display:none;">
														<div id="file_name_{$smarty.foreach.photos.index}" style="margin-bottom:8px; font-size:12px;"></div>
														<progress id="prog_{$smarty.foreach.photos.index}" value="0" style="width:300px;"></progress>
													</div>
												</div>
												{* COMMENT *}
												<div class="txtblack">
													{$lang.profile.comment_photo}:<br>
													<textarea name="user_comment" cols="40" rows="2">{$item.user_comment}</textarea>
												</div>
												{* PERMITTED FOR *}
												<div class="txtblack">
													{$lang.users.allow_type}:&nbsp;
													<select name="upload_allow" {if $data.root}disabled{/if}>
														{*
														<option value="1" {if $item.allow == 1}selected{/if}>{$lang.users.allow_1}</option>
														*}
														<option value="2" {if $item.allow == 2}selected{/if}>{$lang.users.allow_2}</option>
														<option value="3" {if $item.allow == 3}selected{/if}>{$lang.users.allow_3}</option>
													</select>
												</div>
												{* GALLERY *}
												<div class="txtblack">
													<input type="checkbox" name="is_gallary" value="1" {if $item.is_gallary == 1}checked{/if} onclick="$('#categories_div_{$item.id}').slideToggle();">
													{$lang.profile.show_in_photo_gallery}:&nbsp;
												</div>
												<div class="txtblack" id="categories_div_{$item.id}" style="{if $item.is_gallary == 1}display:block;{else}display:none;{/if}">
													<select name="id_gallery" id="id_gallery">
														{foreach item=item2 from=$data_9.categories}
															<option value="{$item2.id}" {if $item2.id == $item.id_gallery && $item.is_gallary == 1}selected{/if}>{$item2.name}</option>
														{/foreach}
													</select>
												</div>
												<div class="tcxf-ch-la">
													<p class="basic-btn_here">
														<b>&nbsp;</b><span>
														<input type="submit" id="submit_{$smarty.foreach.photos.index}" value="{$button.save}" style="width:80px;">
														</span>
													</p>
													{if $item.del_link}
														<p class="basic-btn_here" style="margin-left:10px;">
															<b>&nbsp;</b><span>
															<input type="button" onclick="jConfirm('Do you really want to delete this photo?', 'Alert', function(result) {ldelim} if (result) ShowTab({$data_9.form_sub_page}, '{$item.del_link}'); {rdelim}); return false;" value="{$button.delete}" style="width:80px;">
															{* <input type="button" value="{$button.delete}" onclick="window.location.href='{$item.del_link}';" style="width:80px;"> *}
															</span>
														</p>
													{/if}
													{if $item.crop_link}
														<p class="basic-btn_here" style="margin-left:10px;">
															<b>&nbsp;</b><span>
															<input type="button" value="{$lang.profile.recrop}" onclick="window.open('{$item.crop_link}','crop', 'menubar=0,resizable=1,scrollbars=1,status=1,toolbar=0,width=1000,height=600');" style="width:80px;">
															</span>
														</p>
													{/if}
												</div>
											</form>
											<script type="text/javascript">
											$(function() {ldelim}
												custom_file_upload(
													'{$smarty.foreach.photos.index}', 8,
													{$data_9.max_file_size_bytes}, '{$data_9.max_file_size_string}',
													'{$data_9.file_exts}', '{$data_9.file_types}', 'Photo Files', 'Select Photo', 'update', '{$form.session_id}');
											{rdelim});
											</script>
										</td>
									</tr>
									<tr><td height="1" colspan="2" bgcolor="#CCCCCC"></td></tr>
									<tr><td height="10" colspan="2"></td></tr>
								{/foreach}
							</table>
						{else}
							<div class="txtblack" style="font-weight:bold;">
								No Photos Found
							</div>
						{/if}
					{elseif $data_9.form_sub_page == 9}
						{*
							AUDIO IN SELECTED ALBUM
						*}
						<div class="hdr2x">
							{$data_9.album_title}
						</div>
						<div class="txtblack">
							{$lang.users.audios_in_album}: {$data_9.num_items}
						</div>
						{if $audios}
							<div class="txtblack" style="font-weight:bold;">
								{$data_9.audio_comment}
							</div>
							<table cellpadding="0" cellspacing="0" border="0">
								<tr><td height="1" colspan="2" bgcolor="#CCCCCC"></td></tr>
								<tr><td height="10" colspan="2"></td></tr>
								{foreach key=key item=item from=$audios name=audios}
									<tr valign="top">
										{if !$data_9.embedded_audio}
											<td width="80" style="padding-top:35px;">
												{if $item.view_link}
													<a href="javascript:void(0);" onclick="window.open('{$item.view_link}', 'audio_view', 'menubar=0,resizable=1,scrollbars=0,status=0,toolbar=0,width=800,height=600');return false;">
												{/if}
												{if $item.thumb_file}
													<img src="{$item.thumb_file}" border=0 {$item.sizes} class="icon" alt="">
												{/if}
												{if $item.view_link}
													Click To Play</a>
												{/if}
											</td>
										{/if}
										<td>
											<form id="upload_form_{$smarty.foreach.audios.index}" action="myprofile.php?sel=save_9&amp;upload_type=a" method="post" enctype="multipart/form-data">
												<input type="hidden" name="MAX_FILE_SIZE" value="{$data_9.max_file_size_bytes}">
												<input type="hidden" name="timestamp" value="{$data_9.timestamp}">
												<input type="hidden" name="token" value="{$data_9.token}">
												<input type="hidden" name="id_album" value="{$data_9.id_album}">
												<input type="hidden" name="id_file" value="{$item.id}">
												<div style="padding-bottom:10px; font-size:18px;" class="txtblue">
													{$lang.profile.change_audio}
												</div>
												<div class="txtblack">
													<input type="file" name="file_upload" id="file_upload_{$smarty.foreach.audios.index}" {if $data.root}disabled{/if}>
													<div id="file_info_{$smarty.foreach.audios.index}" style="display:none;">
														<div id="file_name_{$smarty.foreach.audios.index}" style="margin-bottom:8px; font-size:12px;"></div>
														<progress id="prog_{$smarty.foreach.audios.index}" value="0" style="width:400px;"></progress>
													</div>
												</div>
												{* COMMENT *}
												<div class="txtblack">
													{$lang.profile.comment_audio}:<br>
													<textarea name="user_comment" cols="40" rows="4" style="width:400px;">{$item.user_comment}</textarea>
												</div>
												{* PERMITTED FOR *}
												<div class="txtblack">
													{$lang.users.allow_type}:&nbsp;
													<select name="upload_allow" {if $data.root}disabled{/if}>
														{*
														<option value="1" {if $item.allow == 1}selected{/if}>{$lang.users.allow_1}</option>
														*}
														<option value="2" {if $item.allow == 2}selected="selected"{/if}>{$lang.users.allow_2}</option>
														<option value="3" {if $item.allow == 3}selected="selected"{/if}>{$lang.users.allow_3}</option>
													</select>
												</div>
												{if $data_9.embedded_audio && $item.file_path}
													<div class="txtblack">
														<div style="float:left;">
															Play:&nbsp;
														</div>
														<div id="player{$key}" style="padding-bottom:10px; float:left;">
															<script type="text/javascript">
															var fv = "file={$item.file_path}&autostart="+autostart+"&title={$item.user_comment|escape}&lightcolor=0xD12627";
															var FO = {ldelim}
																movie:"{$site_root}/include/mp3player/mp3player.swf",width:"300",height:"20",majorversion:"7",build:"0",bgcolor:"#FFFFFF", flashvars:fv
															{rdelim};
															UFO.create(FO, "player{$key}");
															</script>
														</div>
													</div>
												{/if}
												<div class="tcxf-ch-la">
													<p class="basic-btn_here">
														<b>&nbsp;</b><span>
														<input type="submit" id="submit_{$smarty.foreach.audios.index}" value="{$button.save}" style="width:80px;">
														</span>
													</p>
													{if $item.del_link}
														<p class="basic-btn_here" style="margin-left:10px;">
															<b>&nbsp;</b><span>
															<input type="button" onclick="jConfirm('Do you really want to delete this audio?', 'Alert', function(result) {ldelim} if (result) ShowTab({$data_9.form_sub_page}, '{$item.del_link}'); {rdelim}); return false;" value="{$button.delete}" style="width:80px;">
															{* <input type="button" onclick="window.location.href='{$item.del_link}';" value="{$button.delete}" style="width:80px;"> *}
															</span>
														</p>
													{/if}
												</div>
											</form>
											<script type="text/javascript">
											$(function() {ldelim}
												custom_file_upload(
													'{$smarty.foreach.audios.index}', 9,
													{$data_9.max_file_size_bytes}, '{$data_9.max_file_size_string}',
													'{$data_9.file_exts}', '{$data_9.file_types}', 'Audio Files', 'Select Audio', 'update', '{$form.session_id}');
											{rdelim});
											</script>
										</td>
									</tr>
									<tr><td height="1" colspan="2" bgcolor="#CCCCCC"></td></tr>
									<tr><td height="10" colspan="2"></td></tr>
								{/foreach}
							</table>
						{else}
							<div class="txtblack" style="font-weight:bold;">
								No Audio-Files Found
							</div>
						{/if}
					{elseif $data_9.form_sub_page == 10}
						{*
							VIDEOS IN SELECTED ALBUM
						*}
						<div class="hdr2x">
							{$data_9.album_title}
						</div>
						<div class="txtblack">
							{$lang.users.videos_in_album}: {$data_9.num_items}
						</div>
						{if $videos}
							<div class="txtblack" style="font-weight:bold;">
								{$data_9.video_comment}
							</div>
							<table cellpadding="0" cellspacing="0" border="0">
								<tr><td height="1" colspan="2" bgcolor="#CCCCCC"></td></tr>
								<tr><td height="10" colspan="2"></td></tr>
								{foreach item=item from=$videos name=videos}
									<tr valign="top">
										<td width="140" align="center" style="padding-top:35px;">
											{if $item.view_link}
												<a class="video_colorbox" href="{$item.view_link}">
											{/if}
											{if $item.thumb_file}
												<img src="{$item.thumb_file}" border="0" {$item.sizes} class="icon" alt=""><br>
											{/if}
											{if $item.view_link}
												Click To Play</a>
											{/if}
										</td>
										<td>
											<form id="upload_form_{$smarty.foreach.videos.index}" action="myprofile.php?sel=save_9&amp;upload_type=v" method="post" enctype="multipart/form-data">
												<input type="hidden" name="MAX_FILE_SIZE" value="{$data_9.max_file_size_bytes}">
												<input type="hidden" name="timestamp" value="{$data_9.timestamp}">
												<input type="hidden" name="token" value="{$data_9.token}">
												<input type="hidden" name="id_album" value="{$data_9.id_album}">
												<input type="hidden" name="id_file" value="{$item.id}">
												<div style="padding-bottom:10px; font-size:18px;" class="txtblue">
													{$lang.profile.change_video}
												</div>
												<div class="txtblack">
													<input type="file" name="file_upload" id="file_upload_{$smarty.foreach.videos.index}" {if $data.root}disabled{/if}>
													<div id="file_info_{$smarty.foreach.videos.index}" style="display:none;">
														<div id="file_name_{$smarty.foreach.videos.index}" style="margin-bottom:8px; font-size:12px;"></div>
														<progress id="prog_{$smarty.foreach.videos.index}" value="0" style="width:300px;"></progress>
													</div>
												</div>
												{* COMMENT *}
												<div class="txtblack">
													{$lang.profile.comment_video}:<br>
													<textarea name="user_comment" cols="40" rows="2">{$item.user_comment}</textarea>
												</div>
												{* PERMITTED FOR *}
												<div class="txtblack">
													{$lang.users.allow_type}:&nbsp;
													<select name="upload_allow" {if $data.root}disabled{/if}>
														{*
														<option value="1" {if $item.allow == 1}selected{/if}>{$lang.users.allow_1}</option>
														*}
														<option value="2" {if $item.allow == 2}selected{/if}>{$lang.users.allow_2}</option>
														<option value="3" {if $item.allow == 3}selected{/if}>{$lang.users.allow_3}</option>
													</select>
												</div>
												{* GALLERY *}
												<div class="txtblack">
													<input type="checkbox" name="is_gallary" value="1" {if $item.is_gallary == 1}checked{/if} onclick="$('#categories_div_{$item.id}').slideToggle();">
													{$lang.profile.show_in_video_gallery}:&nbsp;
												</div>
												<div class="txtblack" id="categories_div_{$item.id}" style="{if $item.is_gallary == 1}display:block;{else}display:none;{/if}">
													<select name="id_gallery" id="id_gallery">
														{foreach item=item2 from=$data_9.categories}
															<option value="{$item2.id}" {if $item2.id == $item.id_gallery && $item.is_gallary == 1}selected{/if}>{$item2.name}</option>
														{/foreach}
													</select>
												</div>
												<div class="tcxf-ch-la">
													<p class="basic-btn_here">
														<b>&nbsp;</b><span>
														<input type="submit" id="submit_{$smarty.foreach.videos.index}" value="{$button.save}" style="width:80px;">
														</span>
													</p>
													{if $item.del_link}
														<p class="basic-btn_here" style="margin-left:10px;">
															<b>&nbsp;</b><span>
															<input type="button" onclick="jConfirm('Do you really want to delete this video?', 'Alert', function(result) {ldelim} if (result) ShowTab({$data_9.form_sub_page}, '{$item.del_link}'); {rdelim}); return false;" value="{$button.delete}" style="width:80px;">
															{* <input type="button" onclick="window.location.href='{$item.del_link}';" value="{$button.delete}" style="width:80px;"> *}
															</span>
														</p>
													{/if}
												</div>
											</form>
											<script type="text/javascript">
											$(function() {ldelim}
												custom_file_upload(
													'{$smarty.foreach.videos.index}', 10,
													{$data_9.max_file_size_bytes}, '{$data_9.max_file_size_string}',
													'{$data_9.file_exts}', '{$data_9.file_types}', 'Video Files', 'Select Video', 'update', '{$form.session_id}');
											{rdelim});
											</script>
										</td>
									</tr>
									<tr><td height="1" colspan="2" bgcolor="#CCCCCC"></td></tr>
									<tr><td height="10" colspan="2"></td></tr>
								{/foreach}
							</table>
						{else}
							<div class="txtblack" style="font-weight:bold;">
								No Videos Found
							</div>
						{/if}
					{/if}
					{*
						GENERIC LINKS AND ITEM UPLOAD
					*}
					{if $links_page}
						{* GENERIC LINKS *}
						<div style="margin-left:10px; padding-bottom:10px;">
							{foreach item=item from=$links_page}
								<div class="page_div{if $item.selected == '1'}_active{/if}">
									<div style="margin:5px">
										<a href="javascript:void(0);" onclick="ShowTab({$data_9.form_sub_page}, './myprofile.php?sel=4&amp;sub={$data_9.form_sub_page}&amp;action={$data_9.form_act}{$item.link}'); return false;" class="page_link{if $item.selected eq '1'}_active{/if}">{$item.name}</a>
									</div>
								</div>
							{/foreach}
						</div>
					{/if}
					{if $album_page.show_add_item_link == 1}
						{* ADD ITEM BUTTON TO SHOW UPLOAD FORM *}
						<div>
							<p class="basic-btn_here">
								<b>&nbsp;</b><span>
								<input type="button" style="width:100px;" onclick="$('#upload_div').slideToggle();" value="
								{if $album_page.upload_type == 'f'}
									{$lang.users.add_photo}
								{elseif $album_page.upload_type == 'a'}
									{$lang.users.add_audio}
								{elseif $album_page.upload_type == 'v'}
									{$lang.users.add_video}
								{/if}
								">
								</span>
							</p>
						</div>
						{* ADD ITEM UPLOAD FORM *}
						<div id="upload_div" class="txtblack" style="display:none;">
							<div class="txtblack" style="font-weight:bold;">
								{if $album_page.upload_type == 'f'}
									{$data_9.photo_comment}
								{elseif $album_page.upload_type == 'a'}
									{$data_9.audio_comment}
								{elseif $album_page.upload_type == 'v'}
									{$data_9.video_comment}
								{/if}
							</div>
							<form id="upload_form" action="myprofile.php?sel=save_9&amp;upload_type={$album_page.upload_type}" method="post" enctype="multipart/form-data">
								<input type="hidden" name="MAX_FILE_SIZE" value="{$data_9.max_file_size_bytes}">
								<input type="hidden" name="timestamp" value="{$data_9.timestamp}">
								<input type="hidden" name="token" value="{$data_9.token}">
								<input type="hidden" name="id_album" value="{$data_9.id_album}">
								<div style="padding:10px 0px 10px; font-size:18px;" class="txtblue">
									{if $album_page.upload_type == 'f'}
										{$lang.profile.add_new_photo}
									{elseif $album_page.upload_type == 'a'}
										{$lang.profile.add_new_audio}
									{elseif $album_page.upload_type == 'v'}
										{$lang.profile.add_new_video}
									{/if}
								</div>
								<input type="file" name="file_upload" id="file_upload" {if $data.root}disabled{/if}>
								<div id="file_info" style="display:none;">
									<div id="file_name" style="margin-bottom:8px; font-size:12px;"></div>
									<progress id="prog" value="0" style="width:300px;"></progress>
								</div>
								<div align="left" style="padding-top:10px">
									{* COMMENT *}
									{if $album_page.upload_type == 'f'}
										{$lang.profile.comment_photo}:
									{elseif $album_page.upload_type == 'a'}
										{$lang.profile.comment_audio}:
									{elseif $album_page.upload_type == 'v'}
										{$lang.profile.comment_video}:
									{/if}
									<br>
									<textarea name="user_comment" cols="40" rows="2"></textarea>
								</div>
								<div style="padding-top:10px;">
									{* PERMITTED FOR *}
									{$lang.users.allow_type}:&nbsp;
									<select name="upload_allow" {if $data.root}disabled{/if}>
										{*
										<option value="1" selected>{$lang.users.allow_1}</option>
										*}
										<option value="2" selected>{$lang.users.allow_2}</option>
										<option value="3">{$lang.users.allow_3}</option>
									</select>
								</div>
								{if $album_page.upload_type == 'f' || $album_page.upload_type == 'v'}
									{* GALLERY *}
									<div align="left" style="padding-top:10px">
										<input type="checkbox" name="is_gallary" value="1" checked onclick="$('#categories_div').slideToggle();">
										{if $album_page.upload_type == 'f'}
											{$lang.profile.show_in_photo_gallery}:&nbsp;
										{elseif $album_page.upload_type == 'v'}
											{$lang.profile.show_in_video_gallery}:&nbsp;
										{/if}
									</div>
									<div id="categories_div" align="left" style="padding-top:5px;">
										<select name="id_gallery" id="id_gallery">
											{foreach item=item from=$data_9.categories}
												<option value="{$item.id}">{$item.name}</option>
											{/foreach}
										</select>
									</div>
								{/if}
								{* UPLOAD BUTTON *}
								<div align="left" style="padding-top:5px">
									<p class="basic-btn_here">
										<b>&nbsp;</b><span>
										<input type="submit" id="submit" value="{$button.save}" style="width:80px;">
										</span>
									</p>
								</div>
							</form>
							<script type="text/javascript">
							$(function() {ldelim}
								custom_file_upload(
									'', {$data_9.form_sub_page},
									{$data_9.max_file_size_bytes}, '{$data_9.max_file_size_string}',
									'{$data_9.file_exts}', '{$data_9.file_types}',
									'{if $album_page.upload_type == 'f'}Image{elseif $album_page.upload_type == 'a'}Audio{elseif $album_page.upload_type == 'v'}Video{/if} Files',
									'Select {if $album_page.upload_type == 'f'}Photo{elseif $album_page.upload_type == 'a'}Audio{elseif $album_page.upload_type == 'v'}Video{/if}',
									'add', '{$form.session_id}');
							{rdelim});
							</script>
						</div>
					{/if}
				{elseif $album_page.show_create_link == 1}
					{* NO ALBUM YET *}
					<div class="txtblack">
						{$lang.users.you_have_no_album} <a href="javascript:void(0);" onclick="ShowTab({$data_9.form_sub_page}, './myprofile.php?sel=4&amp;sub={$data_9.form_sub_page}&amp;action=create_album'); return false;">{$lang.users.now}</a>
					</div>
				{elseif $album_page.show_create_form == 1}
					{* ADD / EDIT ALBUM FORM *}
					<div class="txtblack" style="font-weight:bold;">
						{if $data_9.id_album}
							Edit Album
						{else}
							Add Album
						{/if}
					</div>
					<form id="album_form" action="myprofile.php?sel=save_album" method="post" onsubmit="if ($('#album_title').val().trim() == '') {ldelim} jAlert('Please enter the album title.', 'Alert', function() {ldelim} $('#album_title').focus(); {rdelim}); return false; {rdelim}">
						<input type="hidden" name="album_type" value="{$album_page.album_type}">
						{if $data_9.id_album}
							<input type="hidden" name="id_album" value="{$data_9.id_album}">
						{/if}
						<table cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td class="txtblack" style="padding:10px 10px 10px 0;">{$lang.users.album_title}:</td>
								<td><input type="text" name="album_title" id="album_title" value="{$data_9.album_title}" style="width:400px;"></td>
							</tr>
							<tr>
								<td valign="top" class="txtblack" style="padding:5px 10px 10px 0;">{$lang.users.album_description}:</td>
								<td><textarea name="album_description" rows="8" cols="50" style="width:400px;">{$data_9.album_description}</textarea></td>
							</tr>
							<tr>
								<td class="txtblack" style="padding:10px 10px 10px 0;">{$lang.users.album_allow_type}:</td>
								<td>
									<select name="album_upload_allow" >
										{*
										<option value="1" {if $data_9.album_upload_allow == 1}selected="selected"{/if}>{$lang.users.album_allow_1}</option>
										*}
										<option value="2" {if $data_9.album_upload_allow == 2}selected="selected"{/if}>{$lang.users.album_allow_2}</option>
										<option value="3" {if $data_9.album_upload_allow == 3}selected="selected"{/if}>{$lang.users.album_allow_3}</option>
									</select>
								</td>
							</tr>
							<tr>
								<td></td>
								<td>
									<p class="basic-btn_here">
										<b>&nbsp;</b><span>
										<input type="submit" value="{$lang.button.save} Album">
										</span>
									</p>
								</td>
							</tr>
						</table>
					</form>
				{/if}
			{/if}
		</div>
	</div>
{/if}

{* PAGE FIVE *}
{if $form.page == '5'}
	<div>
		{* RATING *}
		<div style="padding:10px;">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr valign="middle">
					<td height="25" colspan="2">
						<div class="hdr2">{$lang.subsection.rating}</div>
						<div class="sep"></div>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<table cellpadding="0" cellspacing="0">
							<tr>
								<td width="300" align="left">
									<font class="txtblue">{$header.current_rating}:</font>
									<font class="txtblack">{$data_10.current_rating_bar} {$data_10.current_rating}</font>
									<font class="txtblack">{$header.all_vote}: {$data_10.all_vote}</font>
								</td>
								<td> </td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height="1" colspan="2">
						<div style="height:1px; margin:5px 0px" class="delimiter"></div>
					</td>
				</tr>
				<tr valign="middle">
					<td height="25" colspan="2">
						<div class="header">{$lang.subsection.comments}</div>
						<div class="sep"></div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div style="height:5px; margin:0px">
							<img src="{$site_root}{$template_root}/images/empty.gif" height="5px" alt="">
						</div>
						{section name=s loop=$data_10.comments}
							<div style="min-height:100px; margin:0px">
								<table width="100%" border="0" cellpadding="5" cellspacing="0">
									<tr valign="middle">
										<td height="100" width="18" class="txtblack" align="center" valign="top">
											<div style="margin-top:7px"> {$smarty.section.s.index_next} </div>
										</td>
										<td height="100" width="80" class="txtblack" align="center" valign="top">
											{if $data_10.comments[s].profile_link}
												<a href="{$data_10.comments[s].profile_link}">
											{/if}
											<img src="{$data_10.comments[s].icon_path}" class="icon" alt="">
											{if $data_10.comments[s].profile_link}
												</a>
											{/if}
										</td>
										<td height="100" width="100%" class="txtblack" valign="top">
											<div style="margin-top:7px">
												{if $data_10.comments[s].profile_link}
													<a href="{$data_10.comments[s].profile_link}">
												{/if}
												<b>{$data_10.comments[s].name}</b>
												{if $data_10.comments[s].profile_link}
													</a>
												{/if}
												<font class="txtblue">{$data_10.comments[s].age} {$lang.home_page.ans}</font>
												<font class="{if $data_10.comments[s].status eq $lang.status.on}link{else}text{/if}_active">{$data_10.comments[s].status}</font>
												<font class="txtblack">
													{if $data_10.comments[s].city}{$data_10.comments[s].city}, {/if}
													{if $data_10.comments[s].region}{$data_10.comments[s].region}, {/if}
													{$data_10.comments[s].country}
												</font>
											</div>
											<div style="margin-top:5px">
												<font class="txtblack">{$data_10.comments[s].message}</font>
											</div>
											<div style="margin-top:5px">
												<font class="text_hidden">{$data_10.comments[s].date}</font>
												{if $data_10.comments[s].delete_link}
													<a href="{$data_10.comments[s].delete_link}">[{$button.delete}]</a>
												{/if}
											</div>
										</td>
									</tr>
								</table>
							</div>
							<div style="height:2px; margin:0px">
								<img src="{$site_root}{$template_root}/images/empty.gif" height="2px" alt="">
							</div>
						{/section}
					</td>
				</tr>
			</table>
		</div>
	</div>
{/if}

{* PAGE SIX *}
{if $form.page == '6'}
	<div>
		{if $data_10.user_tags}
			<div style="padding:10px;">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr valign="middle">
						<td height="25" colspan="2">
							<div class="header">{$lang.subsection.users_tag_myprofile}</div>
							<div class="sep"></div>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							{section name=s loop=$data_10.user_tags}
								<a href="{$data_10.user_tags[s].searchlink}" title="{$data_10.user_tags[s].count}">{$data_10.user_tags[s].tag}</a>
							{/section}
						</td>
					</tr>
				</table>
			</div>
			<div style="height:1px; margin:5px 10px" class="delimiter"></div>
		{/if}
		{if $data_10.my_tags}
			{* <!--VP
			<div style="padding:10px;">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr valign="middle">
						<td height="25" colspan="2">
							<div class="header">{$lang.subsection.i_tag_myprofile}</div>
							<div class="sep"></div>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							{section name=s loop=$data_10.my_tags}
								{$data_10.my_tags[s].tag} <a href="{$data_10.my_tags[s].dellink}"><b>x</b></a>
							{/section}
						</td>
					</tr>
				</table>
			</div>
			<div style="height:1px; margin:5px 10px" class="delimiter"></div>
			-->*}
		{/if}
		<div style="padding:10px;">
			<form name="voting" action="myprofile.php" method="post">
				<input type="hidden" name="sel" value="addtag">
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td><input type="text" name="tag"></td>
						<td><input type="submit" value="{$lang.tag}"></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
{/if}
{/strip}
{* PAGE SEVEN *}
{if $form.page == '7'}
	<div>
		<div style="padding:10px;">
			<div class="error_msg" style="padding:15px;">* email_confirm_missing</div>
			<div style="padding:0px 15px 15px 15px;">
				<form name="voting" action="myprofile.php" method="post">
					<div class="center" style="width:100px;">
						<div class="btnwrap" style="width:100px;">
							<span><span>
							<input type="submit" class="button" style="width:80px;" value="{$lang.button.resend_confirm_email}">
							</span></span>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
{/if}
{*
{literal}
<script type="text/javascript">
$(document).ready(function(){
	$(".video_colorbox").colorbox({iframe:true, innerWidth:700, innerHeight:450});
});
</script>
{/literal}
*}