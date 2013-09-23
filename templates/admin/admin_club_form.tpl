{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$lang.club.admin.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$lang.club.admin.add_club}</font><br><br>
	<table width="100%" border="0" cellpadding="0" cellspacing="1">
	<tr>
		<td valign="top" class="header" style="padding: 5px 0px 10px 0px;"></td>
	</tr>
	<tr>
		<td valign="top" class="text">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td valign="top" style="padding-left: 12px; padding-top: 10px; padding-bottom: 10px;">
				<form name="club_form" id="club_form" action="{$form.action}" method="post" enctype="multipart/form-data">
                	{$form.hidden}
					<table border="0" cellpadding="0" cellspacing="0" class="admin_edit_form">
					<tr>
						<th>{$lang.club.club_name}:</th>
						<td><input type="text" name="club_name" value="{$data.club_name}" style="width: 150px" maxlength="500"></td>
					</tr>
					<tr>
						<th>{$lang.club.club_icon}:</th>
						<td><input type="file" name="upload"></td>
					</tr>
					<tr>
						<th>{$lang.club.club_category}:</th>
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
						<th>{$lang.club.open_join}:</th>
						<td><input type="checkbox" name="open_join" value="{$data.is_open}" checked></td>
					</tr>
					<tr>
						<th>{$lang.club.hidden_club}:</th>
						<td><input type="checkbox" name="hidden_club" value="{$data.is_hidden}"></td>
					</tr>
					<tr>
						<th>{$lang.club.members_can_invite}:</th>
						<td><input type="checkbox" name="members_can_invite" value="{$data.can_invite}" checked></td>
					</tr>
					<tr>
						<th>{$lang.club.members_can_post_images}:</th>
						<td><input type="checkbox" name="members_can_post_images" value="{$data.can_post_images}" checked></td>
					</tr>
					<tr>
						<th>{$lang.club.country}:</th>
						<td>
							<select style="width: 160px" name="country" onchange="SelectRegionAdmin(this.value, document.getElementById('region_div'), document.getElementById('city_div'));">
								<option value="0">{$lang.home_page.select_default}</option>
								{section name=s loop=$countries}
								<option value="{$countries[s].id}" {if $countries[s].sel}selected{/if}>{$countries[s].name}</option>
								{/section}
							</select>
						</td>
					</tr>
					<tr>
						<th>{$lang.club.region}:</th>
						<td>
							<div id="region_div">
							{if $regions}
							<select style="width: 160px" name="region" onchange="SelectCityAdmin(this.value, document.getElementById('city_div'));">
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
						<th>{$lang.club.city}:</th>
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
						<th>{$lang.club.description}:</th>
						<td><textarea name="description" style="width: 300px;" rows="7">{$data.description}</textarea><br>*{$lang.club.no_html}</td>
					</tr>
					<tr>
						<th>{$lang.club.use_agreement}:</th>
						<td><input type="checkbox" name="use_agreement" id="use_agreement" value="{$data.use_agreement}" {if $data.use_agreement eq 1} checked{/if} onclick="AgreementForm();"></td>
					</tr>
					<tr>
						<th>{$lang.club.user_agreement}:</th>
						<td><textarea name="agreement_text" id="agreement_text" style="width: 300px;" rows="7" {if $data.use_agreement ne 1}disabled {/if}>{$data.agreement_text}</textarea><br>*{$lang.club.no_html}</td>
					</tr>
					<tr>
						<td colspan="2"><input type="submit" value="{if $id_club}{$lang.button.save}{else}{$lang.button.add}{/if}" class="button"></td>
					</tr>
					</table>
				</form>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
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
{include file="$admingentemplates/admin_bottom.tpl"}