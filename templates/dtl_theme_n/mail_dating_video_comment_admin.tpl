{include file="$gentemplates/mail_header.tpl"}
{strip}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<span class="mtext">{$mail_generic.hello} {$mail_generic.admin_name},<br><br>
				{$mail_main.message}:<br><br></span>
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td nowrap valign="top"><span class="mtext">{$mail_main.video}: &nbsp;</span></td>
						<td><b class="mtext">{$data.video}</b></td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td nowrap valign="top"><span class="mtext">{$mail_main.fname}: &nbsp;</span></td>
						<td><b class="mtext">{$data.fname}</b></td>
					</tr>
					<tr>
						<td nowrap valign="top"><span class="mtext">{$mail_main.sname}: &nbsp;</span></td>
						<td><b class="mtext">{$data.sname}</b></td>
					</tr>
					<tr>
						<td nowrap valign="top"><span class="mtext">{$mail_main.email}: &nbsp;</span></td>
						<td><b class="mtext">{$data.email}</b></td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td nowrap valign="top"><span class="mtext">{$mail_main.comment}: &nbsp;</span></td>
						<td><b class="mtext">{$data.comment|nl2br}</b></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td><span class="mtext"><br>{$mail_generic.site_regards}</span></td>
		</tr>
	</table>
</div>
{/strip}
{include file="$gentemplates/mail_footer_admin.tpl"}