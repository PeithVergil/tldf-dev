{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
	<!-- begin main cell -->
	<table width="100%" border="0" cellpadding="0" cellspacing="1">
	<tr>
		<td valign="top" class="header" style="padding: 5px 0px 10px 0px;">{$lang.section.club}</td>
	</tr>
	<tr>
		<td valign="top" style="padding: 5px 0px 10px 0px;">
			<table cellpadding="0" cellspacing="0">
			<tr>
				<td width="16"><img src="{$site_root}{$template_root}/images/btn_back.gif" hspace="0" vspace="0" border="0" alt=""></td>
				<td style="padding-left: 2px;"><a href="club.php">{$lang.club.back_to_all_club}</a></td>
			</tr>
			</table>
		</td>
	</tr>
	{if $form.err}
	<tr>
		<td><div class="error_msg">{$form.err}</div></td>
	{/if}
	<tr>
		<td valign="top" class="text">
			<div class="content" style=" margin: 0px;">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td valign="top" style="padding-left: 12px; padding-top: 10px; padding-bottom: 10px;">
				<form name="club_form" id="club_form" action="club.php?sel=save_club" method="post" enctype="multipart/form-data">
					<input type="hidden" name="id_club" value="{$data.id}">
					<table width="100%" border="0" cellpadding="3" cellspacing="0">
					<tr>
						<td height="30" width="200" class="text_head">{$lang.club.club_name}:</td>
						<td><input type="text" name="club_name" value="{$data.name}" style="width: 150px" maxlength="500"></td>
					</tr>
					<tr>
						<td height="30" class="text_head">{$lang.club.club_icon}:</td>
						<td height="30"><input type="file" name="upload"></td>
					</tr>
					<tr>
						<td height="30" class="text_head">{$lang.club.club_category}:</td>
						<td height="30">
							<select style="width: 160px" name="club_category">
								<option value="0">{$lang.home_page.select_default}</option>
								{section name=s loop=$club_categories}
								<option value="{$club_categories[s].id}" {if $club_categories[s].sel}selected{/if}>{$club_categories[s].name}</option>
								{/section}
							</select>
						</td>
					</tr>
					<tr>
						<td height="30" class="text_head">{$lang.club.open_join}:</td>
						<td><input type="checkbox" name="open_join" value="1" {if $data.is_open}checked{/if}></td>
					</tr>
					<tr>
						<td height="30" class="text_head">{$lang.club.hidden_club}:</td>
						<td><input type="checkbox" name="hidden_club" value="1"  {if $data.is_hidden}checked{/if}></td>
					</tr>
					<tr>
						<td height="30" class="text_head">{$lang.club.members_can_invite}:</td>
						<td><input type="checkbox" name="members_can_invite" value="1"  {if $data.can_invite}checked{/if}></td>
					</tr>
					<tr>
						<td height="30" class="text_head">{$lang.club.members_can_post_images}:</td>
						<td><input type="checkbox" name="members_can_post_images" value="1"  {if $data.can_post_images}checked{/if}></td>
					</tr>
					<tr>
						<td height="30" class="text_head">{$lang.club.country}:</td>
						<td height="30">
							<select style="width: 160px" name="country" onchange="SelectRegion('qs', this.value, document.getElementById('region_div'), document.getElementById('city_div'));">
								<option value="0">{$lang.home_page.select_default}</option>
								{section name=s loop=$countries}
								<option value="{$countries[s].id}" {if $countries[s].sel}selected{/if}>{$countries[s].name}</option>
								{/section}
							</select>
						</td>
					</tr>
					<tr>
						<td height="30" class="text_head">{$lang.club.region}:</td>
						<td height="30">
							<div id="region_div">
							{if $regions}
							<select style="width: 160px" name="region" onchange="SelectCity('qs', this.value, document.getElementById('city_div'));">
								<option value="0">{$lang.home_page.select_default}</option>
								{section name=s loop=$regions}
								<option value="{$regions[s].id}" {if $regions[s].sel}selected{/if}>{$regions[s].name}</option>
								{/section}
							</select>
							{/if}
							</div>
						</td>
					</tr>
					<tr>
						<td height="30" class="text_head">{$lang.club.city}:</td>
						<td height="30">
							<div id="city_div">
							{if $cities}
							<select style="width: 160px" name="city">
								<option value="0">{$lang.home_page.select_default}</option>
								{section name=s loop=$cities}
								<option value="{$cities[s].id}" {if $cities[s].sel}selected{/if}>{$cities[s].name}</option>
								{/section}
							</select>
							{/if}
							</div>
						</td>
					</tr>
					<tr>
						<td>
						<table cellpadding="0" cellspacing="0">
							<tr><td class="text_head">{$lang.club.description}:</td></tr>
							<tr><td class="text">{$lang.club.no_html}</td></tr>
						</table>
						</td>
						<td><textarea name="description" style="width: 300px;" rows="7">{$data.description}</textarea></td>
					</tr>
					<tr>
						<td height="30" width="200" class="text_head">{$lang.club.use_agreement}:</td>
						<td><input type="checkbox" name="use_agreement" id="use_agreement" value="1" {if $data.use_agreement eq 1} checked{/if} onclick="AgreementForm();"></td>
					</tr>
					<tr>
						<td>
						<table cellpadding="0" cellspacing="0">
							<tr><td class="text_head">{$lang.club.user_agreement}:</td></tr>
							<tr><td class="text">{$lang.club.no_html}</td></tr>
						</table>
						</td>
						<td><textarea name="agreement_text" id="agreement_text" style="width: 300px;" rows="7" {if $data.use_agreement ne 1}disabled {/if}>{$data.agreement_text}</textarea></td>
					</tr>
					<tr>
						<td height="30" colspan="2"><input type="submit" value="{$lang.button.save}" class="button"></td>
					</tr>
					</table>
				</form>
				</td>
			</tr>
			</table>
			</div>
		</td>
	</tr>
	</table>
	<!-- end main cell -->
</td>
{literal}
<script type="text/javascript">
function AgreementForm() {
	if (document.getElementById('use_agreement').checked) {
		document.getElementById('agreement_text').disabled = false;
	} else {
		document.getElementById('agreement_text').disabled = true;
	}
	return;
}
</script>
{/literal}
{include file="$gentemplates/index_bottom.tpl"}