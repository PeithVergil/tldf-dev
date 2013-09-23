{strip}
<div class="inline_content">
	<div class="tick-mark" style="padding-bottom:5px;">
		{$mylang.intro_text}
	</div>
	<form action="express_interest.php?sel=post&amp;type=ajax" method="post" name="express_form" id="express_form">
		<div id="ajaxwrap-express">
			{* html returned from ajax is inserted here *}
			<table width="500" cellpadding="2" cellspacing="2">
				{if $form.err}
					<tr>
						<td colspan="2" style="padding:0px 11px 12px 0px;">
							<div class="error_ajax">{$form.err}</div>
						</td>
					</tr>
				{elseif $is_submit}
					<tr>
						<td colspan="2" style="padding:0px 11px 12px 0px;">
							<div class="error_ajax">You Already Expressed Your Interest in Thai Lady Dating Events&trade;, But Please Feel Free To Submit This Form Again If Some Data Has Changed.</div>
						</td>
					</tr>
				{/if}
				<tr>
					<td colspan="2"><p style="margin-top:0;">{$mylang.best_time_to_call}:</p></td>
				</tr>
				<tr>
					<td align="right">
						{if $form.err_field.best_time_weekdays}<span class="error">{/if}
						{$mylang.best_time_weekdays}
						{if $form.err_field.best_time_weekdays}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td><input id="best_time_weekdays" type="text" name="best_time_weekdays" value="{$data.best_time_weekdays}" style="width:250px;"></td>
				</tr>
				<tr>
					<td align="right">
						{if $form.err_field.best_time_saturdays}<span class="error">{/if}
						{$mylang.best_time_saturdays}
						{if $form.err_field.best_time_saturdays}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td><input id="best_time_saturdays" type="text" name="best_time_saturdays" value="{$data.best_time_saturdays}" style="width:250px;"></td>
				</tr>
				<tr>
					<td align="right">
						{if $form.err_field.best_time_sundays}<span class="error">{/if}
						{$mylang.best_time_sundays}
						{if $form.err_field.best_time_sundays}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td><input id="best_time_sundays" type="text" name="best_time_sundays" value="{$data.best_time_sundays}" style="width:250px;"></td>
				</tr>
				<tr>
					<td align="right">
						{if $form.err_field.fname}<span class="error">{/if}
						{$mylang.fname}
						{if $form.err_field.fname}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td><input id="fname" type="text" name="fname" value="{$data.fname}" style="width:250px;"></td>
				</tr>
				<tr>
					<td align="right">
						{if $form.err_field.sname}<span class="error">{/if}
						{$mylang.sname}
						{if $form.err_field.sname}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td><input id="sname" type="text" name="sname" value="{$data.sname}" style="width:250px;"></td>
				</tr>
				<tr>
					<td align="right">
						{if $form.err_field.email}<span class="error">{/if}
						{$mylang.email}
						{if $form.err_field.email}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td><input id="email" type="text" name="email" value="{$data.email}" style="width:250px;"></td>
				</tr>
				<tr>
					<td align="right">
						{if $form.err_field.date_birthday}<span class="error">{/if}
						{$mylang.date_birthday}
						{if $form.err_field.date_birthday}</span>{/if}
						<span class="mandatory">*</span>:
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
				<tr>
					<td align="right">
						{if $form.err_field.place_of_birth}<span class="error">{/if}
						{$mylang.place_of_birth}
						{if $form.err_field.place_of_birth}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td><input id="place_of_birth" type="text" name="place_of_birth" value="{$data.place_of_birth}" style="width:250px;"></td>
				</tr>
				<tr>
					<td align="right">
						{if $form.err_field.identification_number}<span class="error">{/if}
						{$mylang.identification_number}
						{if $form.err_field.identification_number}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td><input id="identification_number" type="text" name="identification_number" value="{$data.identification_number}" style="width:250px;"></td>
				</tr>
				<tr>
					<td align="right">
						{if $form.err_field.identification_type}<span class="error">{/if}
						{$mylang.identification_type}
						{if $form.err_field.identification_type}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td><input id="identification_type" type="text" name="identification_type" value="{$data.identification_type}" style="width:250px;"></td>
				</tr>
				<tr>
					<td align="right">
						{if $form.err_field.home_phone}<span class="error">{/if}
						{$mylang.home_phone}
						{if $form.err_field.home_phone}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td>
						<table cellspacing="0" cellpadding="0">
							<tr>
								<td align="center">[Country]</td>
								<td></td>
								<td align="center">[Area]</td>
								<td></td>
								<td align="center">[Your Number]</td>
							</tr>
							<tr>
								<td><input type="text" id="home_phone_cc" name="home_phone_cc" value='{$data.home_phone_cc}' style="width:40px; margin:0;" maxlength="3" /></td>
								<td>&nbsp;-&nbsp;</td>
								<td><input type="text" id="home_phone_ac" name="home_phone_ac" value='{$data.home_phone_ac}' style="width:40px; margin:0;" maxlength="3" /></td>
								<td>&nbsp;-&nbsp;</td>
								<td><input type="text" id="home_phone_ph" name="home_phone_ph" value='{$data.home_phone_ph}' style="width:100px; margin:0;" size="10" /></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td align="right">
						{if $form.err_field.mobile_phone}<span class="error">{/if}
						{$mylang.mobile_phone}
						{if $form.err_field.mobile_phone}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td>
						<input type="text" id="mobile_phone" name="mobile_phone" value='{$data.mobile_phone}' style="width:250px" size="15" />
					</td>
				</tr>
				<tr>
					<td align="right">
						{if $form.err_field.id_country}<span class="error">{/if}
						{$mylang.id_country}
						{if $form.err_field.id_country}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td>
						<select id="id_country" name="id_country" style="width:260px;">
							<option value="">Pick a country</option>
							<option value="251" {if $data.id_country == 251}selected="selected"{/if}>USA</option>
							<option value="14" {if $data.id_country == 14}selected="selected"{/if}>Australia</option>
							<option value="">--------------------</option>
							{foreach item=item from=$country_arr}
								<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
							{/foreach}
						</select>
					</td>
				</tr>
				<tr>
					<td align="right">
						{if $form.err_field.region}<span class="error">{/if}
						{$mylang.region}
						{if $form.err_field.region}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td><input type="text" id="region" name="region" value='{$data.region}' style="width:250px;"></td>
				</tr>
				<tr>
					<td align="right">
						{if $form.err_field.city}<span class="error">{/if}
						{$mylang.city}
						{if $form.err_field.city}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td><input type="text" id="city" name="city" value='{$data.city}' style="width:250px;"></td>
				</tr>
				<tr>
					<td align="right">
						{if $form.err_field.zip_code}<span class="error">{/if}
						{$mylang.zip_code}
						{if $form.err_field.zip_code}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td><input type="text" id="zip_code" name="zip_code" value='{$data.zip_code}' style="width:250px;"></td>
				</tr>
				<tr>
					<td align="right">
						{if $form.err_field.address_1}<span class="error">{/if}
						{$mylang.address_1}
						{if $form.err_field.address_1}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td><input type="text" id="address_1" name="address_1" value='{$data.address_1}' style="width:250px;"></td>
				</tr>
				<tr>
					<td align="right">{$mylang.address_2}:</td>
					<td><input type="text" id="address_2" name="address_2" value='{$data.address_2}' style="width:250px;"></td>
				</tr>
				<tr>
					<td align="right">{$mylang.address_3}:</td>
					<td><input type="text" id="address_3" name="address_3" value='{$data.address_3}' style="width:250px;"></td>
				</tr>
				<tr>
					<td align="right" valign="top">{$mylang.comments}:</td>
					<td><textarea id="comments" name="comments" style="height:120px;width:254px">{$data.comments}</textarea></td>
				</tr>
				<tr>
					<td align="right">&nbsp;</td>
					<td><input type="submit" name="btnSubmit" class="normal-btn" value="Express Interest Now" /></td>
				</tr>
			</table>
		</div>
	</form>
</div>
{/strip}
<script>
{literal}
$("#express_form").submit(function(event)
{
	event.preventDefault();
	$.post(
		this.action,
		{
			'type'					: 'ajax',
			'best_time_weekdays'	: this.best_time_weekdays.value,
			'best_time_saturdays'	: this.best_time_saturdays.value,
			'best_time_sundays'		: this.best_time_sundays.value,
			'fname'					: this.fname.value,
			'sname'					: this.sname.value,
			'email'					: this.email.value,
			'b_day'					: this.b_day.value,
			'b_month'				: this.b_month.value,
			'b_year'				: this.b_year.value,
			'place_of_birth'		: this.place_of_birth.value,
			'identification_number'	: this.identification_number.value,
			'identification_type'	: this.identification_type.value,
			'home_phone_cc'			: this.home_phone_cc.value,
			'home_phone_ac'			: this.home_phone_ac.value,
			'home_phone_ph'			: this.home_phone_ph.value,
			'mobile_phone'			: this.mobile_phone.value,
			'id_country'			: this.id_country.value,
			'region'				: this.region.value,
			'city'					: this.city.value,
			'zip_code'				: this.zip_code.value,
			'address_1'				: this.address_1.value,
			'address_2'				: this.address_2.value,
			'address_3'				: this.address_3.value,
			'comments'				: this.comments.value
		},
		function(data) {
			// alert("Data Loaded: " + data);
			var content = $(data).find('#ajaxwrap-express');
			$("#ajaxwrap-express").html(content);
			$("#cboxLoadedContent").scrollTop(0);
		}
	);
});
{/literal}
</script>