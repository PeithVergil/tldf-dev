{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<span class="mtext">{$mail_generic.hello} {$mail_generic.admin_name},<br><br>
				{$mail_main.message}<br><br>
				{$mail_main.data}:<br><br></span>
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td nowrap><span class="mtext">{$mail_main.name}: &nbsp;</span></td>
						<td><b class="mtext">{$data.name}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.phone}: &nbsp;</span></td>
						<td><b class="mtext">{$data.phone}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.email}: &nbsp;</span></td>
						<td><b class="mtext">{$data.email}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.address}: &nbsp;</span></td>
						<td><b class="mtext">{$data.address}</b></td>
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