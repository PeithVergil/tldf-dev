<form action="{$form.search_action}" method="get" name="search_form" id="search_form" style="margin:0px">
	<input type="hidden" name="sel" value="search">
	<input type="hidden" name="flag_country" value="0">
	<input type="hidden" name="search_type" id="search_type" value="{$form.search_type}">
	<div id="quick_search_block" class="quick_search">
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td valign="top" width="33%">
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td class="text_head">
								{$lang.home_page.im} : {if $data.gender_1 == 1}{$gender[0].name}{elseif $data.gender_1 == 2}{$gender[1].name}{/if}
								<input type="hidden" name="gender_1" value="{$data.gender_1}" />
							</td>
						</tr>
						<tr>
							<td style="padding: 15px 0px 3px 0px;" class="text_head">
								{$lang.home_page.seeking_a} : &nbsp;
								{if QUICK_SEARCH_GENDER}
									<select name="gender_2" class="index_select" style="width:115px;">
										{foreach item=item from=$gender}
											<option value="{$item.id}" {if $item.sel_search}selected="selected"{/if}>{$item.name_search}</option>
										{/foreach}
									</select>
								{else}
									{if $data.gender_2 eq 1}{$gender[0].name}{elseif $data.gender_2 eq 2}{$gender[1].name}{/if}
									<input type="hidden" name="gender_2" value="{$data.gender_2}" />
								{/if}
							</td>
						</tr>
						{if QUICK_SEARCH_COUPLE}
							<tr>
								<td>
									<table cellpadding="0" cellspacing="0">
										<tr>
											<td><input type="radio" name="couple_2" value="0" {if !$data.couple_2}checked="checked"{/if}></td>
											<td style="padding-right: 15px;" class="text">&nbsp;{$lang.users.single}</td>
											<td><input type="radio" name="couple_2" value="1" {if $data.couple_2}checked="checked"{/if}></td>
											<td style="padding-right: 15px;" class="text">&nbsp;{$lang.users.couple}</td>
										</tr>
									</table>
								</td>
							</tr>
						{/if}
						{if QUICK_SEARCH_RELATIONSHIP}
							<tr>
								<td style="padding: 15px 0px 3px 0px;" class="text_head">{$lang.home_page.looking_for}</td>
							</tr>
							<tr>
								<td>
									<select name="relation[]" style="width: 230px;" multiple size="5" class="index_select">
										<option value="" {if $data.arr_relationship == '0' || !$data.arr_relationship}selected="selected"{/if}>{$lang.home_page.select_default}</option>
										{foreach item=item from=$relation}
											<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.name}</option>
										{/foreach}
									</select>
								</td>
							</tr>
						{else}
							<tr>
								<td><input type="hidden" name="relation[]" value="2" /></td>
							</tr>
						{/if}
					</table>
				</td>
				<td width="33%">
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td style="padding:25px 0px 3px 0px;" class="text_head">{$lang.home_page.between_the_ages_of}</td>
						</tr>
						<tr>
							<td>
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td>
											<select name="age_min" class="index_select">
												{foreach item=item from=$age_min}
													<option value="{$item}" {if $data.age_min == $item}selected="selected"{/if}>{$item}</option>
												{/foreach}
											</select>
										</td>
										<td style="padding: 0px 5px 0px 5px;" class="text_head"> {$lang.home_page.and} </td>
										<td>
											<select name="age_max" class="index_select">
												{foreach item=item from=$age_max}
													<option value="{$item}" {if $data.age_max == $item}selected="selected"{/if}>{$item}</option>
												{/foreach}
											</select>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						{if QUICK_SEARCH_COUNTRY}
							<tr>
								<td style="padding: 15px 0px 3px 0px;" class="text_head">{$lang.home_page.country}</td>
							</tr>
							<tr>
								<td>
									<div id="country_div" style="margin-top:0;">
										<select name="country" style="width:150px;" onchange="SelectRegion('hp', this.value, document.getElementById('region_div'), document.getElementById('city_div'));" class="index_select">
											<option value="0">{$lang.home_page.select_default}</option>
											{foreach item=item from=$countries}
												<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.name}</option>
											{/foreach}
										</select>
									</div>
								</td>
							</tr>
						{/if}
						{if QUICK_SEARCH_REGION}
							<tr>
								<td style="padding: 5px 0px 3px 0px;" class="text_head">{$lang.home_page.region}</td>
							</tr>
							<tr>
								<td>
									<div id="region_div" style="margin-top:0;">
										<select name="region" style="width:150px;" class="index_select" onchange="SelectCity('qs', this.value, document.getElementById('city_div'));">
											<option value="0">{$lang.home_page.select_default}</option>
											{foreach item=item from=$regions}
												<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.name}</option>
											{/foreach}
										</select>
									</div>
								</td>
							</tr>
						{/if}
						{if QUICK_SEARCH_CITY}
							<tr>
								<td style="padding: 5px 0px 3px 0px;" class="text_head">{$lang.home_page.city}</td>
							</tr>
							<tr>
								<td>
									<div id="city_div" style="margin-top:0;">
										<select name="city" style="width:150px;" class="index_select">
											<option value="0">{$lang.home_page.select_default}</option>
											{foreach item=item from=$cities}
												<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.name}</option>
											{/foreach}
										</select>
									</div>
								</td>
							</tr>
						{/if}
						{if QUICK_SEARCH_DISTANCE}
							<tr>
								<td style="padding: 15px 0px 0px 0px;">
									<table cellpadding="0" cellspacing="0">
										<tr>
											<td class="text_head">{$lang.home_page.zipcode}</td>
											<td style="padding-left: 3px;">
												<input type="text" name="zipcode" id="zipcode" maxlength="{$form.zip_count}" style="width:60px;" onblur="ZipCodeCheck(this.value);" value="{$data.zipcode}">
											</td>
										</tr>
									</table>
								</td>
							</tr>
						{/if}
					</table>
				</td>
				<td width="33%">
					<table cellpadding="0" cellspacing="0" width="100%">
						{if QUICK_SEARCH_DISTANCE}
							<tr>
								<td style="padding: 15px 0px 3px 0px;">
									<table cellpadding="0" cellspacing="0">
										<tr>
											<td class="text_head">{$lang.home_page.within}</td>
											<td style="padding-left: 5px;">
												<input type="checkbox" id="within" name="within" value="1" {if $form.search_type eq 2}disabled{elseif $data.within eq 1}checked{/if} onclick="javascript: if (document.getElementById('distance').disabled) document.getElementById('distance').disabled = false; else document.getElementById('distance').disabled = true;">
											</td>
											<td style="padding-left: 5px;">
												<select id="distance" name="distance" {if $form.search_type eq 2}disabled{/if} class="index_select">
													{foreach item=item from=$distances}
														<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.name} {$item.type}</option>
													{/foreach}
												</select>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						{/if}
						<tr>
							<td style="padding:35px 0px 3px 0px;">
								<table cellspacing="0" cellpadding="0">
									<tr>
										{if QUICK_SEARCH_WITH_PHOTO_SEARCH}
											<td><input type="checkbox" name="foto_only" value="1" {if $data.foto}checked="checked"{/if} /></td>
											<td style="padding-left: 3px;" class="text_head">{$lang.home_page.foto}</td>
										{/if}
										<td style="padding-left: 15px;"><input type="checkbox" name="online_only" value="1" {if $data.online}checked="checked"{/if} /></td>
										<td style="padding-left: 3px;" class="text_head">{$lang.home_page.online_now}</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<p class="basic-btn_next"><span><input type="button" value="{$lang.button.search}" onclick="document.search_form.submit();"></span><b>&nbsp;</b></p>
		{* <!--
		<input type="button" class="btn_org" style="width:100px;" value="{$lang.button.search}" onclick="ZipCodeCheck(document.getElementById('zipcode').value); document.search_form.submit();">
		--> *}
	</div>
</form>