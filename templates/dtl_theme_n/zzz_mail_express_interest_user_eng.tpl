{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<span class="mtext">{$header.generic_e.hello} <b>{$data.fname}</b>,<br><br>
				{$header.express_interest_e.message}<br><br>
				{$header.express_interest_e.details}:<br><br>
				{$header.express_interest_e.best_time_to_call}:<br><br></span>
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td nowrap><span class="mtext">{$header.express_interest_e.best_time_weekdays}: &nbsp;</span></td>
						<td><b class="mtext">{$data.best_time_weekdays}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.express_interest_e.best_time_saturdays}: &nbsp;</span></td>
						<td><b class="mtext">{$data.best_time_saturdays}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.express_interest_e.best_time_sundays}: &nbsp;</span></td>
						<td><b class="mtext">{$data.best_time_sundays}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.express_interest_e.fname}: &nbsp;</span></td>
						<td><b class="mtext">{$data.fname}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.express_interest_e.sname}: &nbsp;</span></td>
						<td><b class="mtext">{$data.sname}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.express_interest_e.email}: &nbsp;</span></td>
						<td><b class="mtext">{$data.email}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.express_interest_e.date_of_birth}: &nbsp;</span></td>
						<td><b class="mtext">{$data.date_birthday_formatted}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.express_interest_e.place_of_birth}: &nbsp;</span></td>
						<td><b class="mtext">{$data.place_of_birth}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.express_interest_e.identification_number}: &nbsp;</span></td>
						<td><b class="mtext">{$data.identification_number}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.express_interest_e.identification_type}: &nbsp;</span></td>
						<td><b class="mtext">{$data.identification_type}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.express_interest_e.home_phone}: &nbsp;</span></td>
						<td><b class="mtext">{$data.home_phone}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.express_interest_e.mobile_phone}: &nbsp;</span></td>
						<td><b class="mtext">{$data.mobile_phone}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.express_interest_e.country}: &nbsp;</span></td>
						<td><b class="mtext">{$data.country}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.express_interest_e.region}: &nbsp;</span></td>
						<td><b class="mtext">{$data.region}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.express_interest_e.city}: &nbsp;</span></td>
						<td><b class="mtext">{$data.city}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.express_interest_e.zip_code}: &nbsp;</span></td>
						<td><b class="mtext">{$data.zip_code}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.express_interest_e.address_1}: &nbsp;</span></td>
						<td><b class="mtext">{$data.address_1}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.express_interest_e.address_2}: &nbsp;</span></td>
						<td><b class="mtext">{$data.address_2}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.express_interest_e.address_3}: &nbsp;</span></td>
						<td><b class="mtext">{$data.address_3}</b></td>
					</tr>
					<tr>
						<td nowrap valign="top">{$header.express_interest_e.comments}: &nbsp;</span></td>
						<td><b class="mtext">{$data.comments|nl2br}</b></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mtext"><br>
				{$header.express_interest_e.contact_soon}<br><br><br>
				{$header.express_interest_e.admin_contact} <a href="mailto:{$data.adminemail}">{$data.adminemail}</a><br><br><br>
				{$header.generic_e.admin_regards}<br><br>
				{$header.generic_e.company_info}</span>
			</td>
		</tr>
	</table>
</div>
{include file="$gentemplates/mail_footer_eng.tpl"}