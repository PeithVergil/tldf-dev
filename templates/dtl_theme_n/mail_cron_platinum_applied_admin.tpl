{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<span class="mtext">{$mail_generic.hello} {$mail_generic.admin_name},<br><br>
				{$mail_main.message}<br><br></span>
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td align="left" nowrap><span class="mtext">{$mail_main.fname}: &nbsp;</span></td>
						<td align="left"><b class="mtext">{$data.fname}</b></td>
					</tr>
					<tr>
						<td align="left" nowrap><span class="mtext">{$mail_main.sname}: &nbsp;</span></td>
						<td align="left"><b class="mtext">{$data.sname}</b></td>
					</tr>
					<tr>
						<td align="left" nowrap><span class="mtext">{$mail_main.login}: &nbsp;</span></td>
						<td align="left"><b class="mtext">{$data.login}</b></td>
					</tr>
					<tr>
						<td align="left" nowrap><span class="mtext">{$mail_main.email}: &nbsp;</span></td>
						<td align="left"><b class="mtext"><a href="mailto:{$data.email}" class="mtext">{$data.email}</a></b></td>
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