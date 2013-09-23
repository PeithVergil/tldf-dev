{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<span class="mtext">{$header.generic_e.hello} <b>{$data.fname}</b>,<br><br>
				{$header.report_a_violation_res_e.message}<br><br>
				{$header.report_a_violation_res_e.data}:<br><br></span>
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td nowrap><span class="mtext">{$header.report_a_violation_res_e.data_name}: &nbsp;</span></td>
						<td><b class="mtext">{$data.name}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.report_a_violation_res_e.data_email}: &nbsp;</span></td>
						<td><b class="mtext"><b>{$data.email}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.report_a_violation_res_e.data_phone}: &nbsp;</span></td>
						<td><b class="mtext"><b>{$data.phone}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.report_a_violation_res_e.data_description}: &nbsp;</span></td>
						<td><b class="mtext"><b>{$data.description}</b></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mtext"><br><br>
				{$header.generic_e.admin_regards}<br><br>
				{$header.generic_e.company_info}</span>
			</td>
		</tr>
	</table>
</div>
{include file="$gentemplates/mail_footer_admin.tpl"}