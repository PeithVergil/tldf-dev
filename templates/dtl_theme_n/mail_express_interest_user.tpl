{include file="$gentemplates/mail_header.tpl"}
{strip}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<span class="mtext">{$mail_generic.hello} <b>{$data.fname}</b>,<br><br>
				{$mail_main.message}<br><br>
				{$mail_main.details}:<br><br>
				{$mail_main.best_time_to_call}:<br><br></span>
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td nowrap><span class="mtext">{$mail_main.best_time_weekdays}: &nbsp;</span></td>
						<td><b class="mtext">{$data.best_time_weekdays}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.best_time_saturdays}: &nbsp;</span></td>
						<td><b class="mtext">{$data.best_time_saturdays}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.best_time_sundays}: &nbsp;</span></td>
						<td><b class="mtext">{$data.best_time_sundays}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.fname}: &nbsp;</span></td>
						<td><b class="mtext">{$data.fname}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.sname}: &nbsp;</span></td>
						<td><b class="mtext">{$data.sname}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.email}: &nbsp;</span></td>
						<td><b class="mtext">{$data.email}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.date_of_birth}: &nbsp;</span></td>
						<td><b class="mtext">{$data.date_birthday_formatted}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.place_of_birth}: &nbsp;</span></td>
						<td><b class="mtext">{$data.place_of_birth}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.identification_number}: &nbsp;</span></td>
						<td><b class="mtext">{$data.identification_number}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.identification_type}: &nbsp;</span></td>
						<td><b class="mtext">{$data.identification_type}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.home_phone}: &nbsp;</span></td>
						<td><b class="mtext">{$data.home_phone}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.mobile_phone}: &nbsp;</span></td>
						<td><b class="mtext">{$data.mobile_phone}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.country}: &nbsp;</span></td>
						<td><b class="mtext">{$data.country}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.region}: &nbsp;</span></td>
						<td><b class="mtext">{$data.region}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.city}: &nbsp;</span></td>
						<td><b class="mtext">{$data.city}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.zip_code}: &nbsp;</span></td>
						<td><b class="mtext">{$data.zip_code}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.address}: &nbsp;</span></td>
						<td>
							<b class="mtext">{$data.address_1}<br>{if $data.address_2}{$data.address_2}<br>{/if}{if $data.address_3}{$data.address_3}<br>{/if}</b>
						</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td valign="top"><span class="mtext">{$mail_main.comments}: &nbsp;</span></td>
						<td><b class="mtext">{$data.comments|nl2br}</b></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mtext"><br>
				{$mail_main.contact_soon}<br><br>
				{$mail_main.admin_contact}<br><br>
				{$mail_generic.admin_regards}<br><br>
				{$mail_generic.company_info}</span>
			</td>
		</tr>
	</table>
</div>
{/strip}
{include file="$gentemplates/mail_footer_user.tpl"}