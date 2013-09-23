{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<span class="mtext">{$mail_generic.hello} {$mail_generic.admin_name},<br><br>
				{$data.message}<br><br></span>
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td nowrap><span class="mtext">{$mail_main.order} &nbsp;</span></td>
						<td><b class="mtext">#{$data.order_id}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.amount} &nbsp;</span></td>
						<td><b class="mtext">{$data.amount}</b></td>
					</tr>
					<tr>
						<td colspan="2"><span class="mtext"><br><b>{$mail_main.sender}</b><br><br></span></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.login} &nbsp;</span></td>
						<td><b class="mtext">{$data.login}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.fname} &nbsp;</span></td>
						<td><b class="mtext">{$data.fname}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.sname} &nbsp;</span></td>
						<td><b class="mtext">{$data.sname}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.email} &nbsp;</span></td>
						<td><b class="mtext">{$data.email}</b></td>
					</tr>
					<tr>
						<td colspan="2"><span class="mtext"><br><b>{$mail_main.recipient}</b><br><br></span></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.login} &nbsp;</span></td>
						<td><b class="mtext">{$data.login_to}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.fname} &nbsp;</span></td>
						<td><b class="mtext">{$data.fname_to}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.sname} &nbsp;</span></td>
						<td><b class="mtext">{$data.sname_to}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.email} &nbsp;</span></td>
						<td><b class="mtext">{$data.email_to}</b></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mtext"><br>{$mail_generic.site_regards}</span>
			</td>
		</tr>
	</table>
</div>
{include file="$gentemplates/mail_footer_admin.tpl"}