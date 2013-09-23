<div>
	<div class="tick-mark" style="padding-bottom:7px;">
		{$lang.platinum_match.$usr_gender.form_head}
	</div>
	<form action="platinum_match.php?sel=ajaxsend" method="post" name="platinum_form" id="platinum_form">
		<div id="ajaxwrap-plat">
			<table cellpadding="3" cellspacing="3">
				{if $form.err}
					<tr>
						<td colspan="2" style="padding:0px 11px 12px 0px;">
							<div class="error_ajax">{$form.err}</div>
						</td>
					</tr>
				{elseif $is_submit}
					<tr>
						<td colspan="2" style="padding:0px 11px 12px 0px;">
							<div class="error_ajax">You Already Applied For Platinum Matching&trade;, But Please Feel Free To Submit This Form Again If Some Data Has Changed.</div>
						</td>
					</tr>
				{/if}
				<tr>
					<td width="140">
						{if $form.err_field.fname}<span class="error">{/if}
						{$lang.platinum_match.$usr_gender.fname}
						{if $form.err_field.fname}</span>{/if}
						<span class="mandatory">*</span>:</td>
					<td><input type="text" id="fname" name="fname" value="{$data.fname}" style="width:250px;"></td>
				</tr>
				<tr>
					<td>
						{if $form.err_field.sname}<span class="error">{/if}
						{$lang.platinum_match.$usr_gender.sname}
						{if $form.err_field.sname}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td><input type="text" id="sname" name="sname" value="{$data.sname}" style="width:250px;"></td>
				</tr>
				<tr>
					<td>
						{if $form.err_field.city}<span class="error">{/if}
						{$lang.platinum_match.$usr_gender.city}
						{if $form.err_field.city}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td><input type="text" id="city" name="city" value='{$data.city}' style="width:250px;"></td>
				</tr>
				<tr>
					<td>
						{if $form.err_field.id_country}<span class="error">{/if}
						{$lang.platinum_match.$usr_gender.id_country}
						{if $form.err_field.id_country}</span>{/if}
						<span class="mandatory">*</font>:
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
					<td>
						{if $form.err_field.phone}<span class="error">{/if}
						{$lang.platinum_match.$usr_gender.phone}
						{if $form.err_field.phone}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td>
						<input type="text" id="phone" name="phone" value='{$data.phone}' style="width:250px" size="15" />
					</td>
				</tr>
				<tr>
					<td>
						{if $form.err_field.email}<span class="error">{/if}
						{$lang.platinum_match.$usr_gender.email}
						{if $form.err_field.email}</span>{/if}
						<span class="mandatory">*</span>:
					</td>
					<td><input type="text" id="email" name="email" value="{$data.email}" style="width:250px;"></td>
				</tr>
				<tr>
					<td colspan="2">
						{if $form.err_field.calltime}<span class="error">{/if}
						{$lang.platinum_match.$usr_gender.calltime}
						{if $form.err_field.calltime}</span>{/if}
						<span class="mandatory">*</span>:<br />
						<textarea id="calltime" name="calltime" style="height:60px;width:402px">{$data.calltime}</textarea>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						{$lang.platinum_match.$usr_gender.comments}:<br />
						<textarea id="comments" name="comments" style="height:60px;width:402px">{$data.comments}</textarea>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<input type="submit" class="normal-btn" value="Send" />
					</td>
				</tr>
			</table>
		</div>
	</form>
</div>
<script>
{literal}
/* attach a submit handler to the form */
$("#platinum_form").submit(function(event) {
	/* stop form from submitting normally */
	event.preventDefault();
	/* get some values from elements on the page: */
	var url			= this.action,
		fname		= this.fname.value,
		sname		= this.sname.value,
		city 		= this.city.value,
		id_country	= this.id_country.value,
		phone		= this.phone.value,
		email		= this.email.value,
		calltime	= this.calltime.value,
		comments	= this.comments.value;
	/* Send the data using post and put the results in a div */
	$.post( url, { 'fname':fname, 'sname':sname, 'city':city, 'id_country':id_country, 'phone':phone, 'email':email, 'calltime':calltime, 'comments':comments },
	   function( data ) {
		   //alert("Data Loaded: " + data);
		   var content = $(data).find('#ajaxwrap-plat');
		   $("#ajaxwrap-plat").html(content);
		   $("#cboxLoadedContent").scrollTop(0);
		}
	);
});
{/literal}
</script>