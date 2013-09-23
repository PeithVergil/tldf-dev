{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<span class="mtext">{$mail_generic.hello} <b>{$data.name}</b>,<br><br>
				{$mail_main.message}<br><br>
				{$mail_main.details}<br><br></span>
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td nowrap valign="top"><span class="mtext">{$mail_main.name}: &nbsp;</span></td>
						<td><b class="mtext">{$data.name}</b></td>
					</tr>
					<tr>
						<td nowrap valign="top"><span class="mtext">{$mail_main.email}: &nbsp;</span></td>
						<td><b class="mtext">{$data.email}</b></td>
					</tr>
					<tr>
						<td nowrap valign="top"><span class="mtext">{$mail_main.phone}: &nbsp;</span></td>
						<td><b class="mtext">{$data.phone}</b></td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td valign="top"><span class="mtext">{$mail_main.description}: &nbsp;</span></td>
						<td><b class="mtext">{$data.description|nl2br}</b></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mtext"><br>
				{$mail_main.admin_contact}<br><br>
				{$mail_generic.admin_regards},<br><br>
				{$mail_generic.company_info}
			</td>
		</tr>
	</table>
</div>
{include file="$gentemplates/mail_footer_user.tpl"}