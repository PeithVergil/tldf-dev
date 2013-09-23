{include file="$gentemplates/mail_header.tpl"}
{strip}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<span class="mtext">{$mail_generic.hello} <b>{$data.fname}</b>,<br><br>
				{$mail_main.message}<br><br>
				{$mail_main.details}:<br><br></span>
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td nowrap><span class="mtext">{$mail_main.username}: &nbsp;</span></td>
						<td><b class="mtext">{$data.login}</b></td>
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
						<td nowrap><span class="mtext">{$mail_main.id_num}: &nbsp;</span></td>
						<td><b class="mtext">{$data.id_num}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.id_type}: &nbsp;</span></td>
						<td><b class="mtext">{$data.id_type}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.phone}: &nbsp;</span></td>
						<td><b class="mtext">{$data.phone}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.mobile}: &nbsp;</span></td>
						<td><b class="mtext">{$data.mobile}</b></td>
					</tr>
					<tr>
						<td nowrap valign="top"><span class="mtext">{$mail_main.comments}: &nbsp;</span></td>
						<td><b class="mtext">{$data.comments|nl2br}</b></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mtext"><br>
				{$mail_generic.admin_regards}<br><br>
				{$mail_generic.company_info}</span>
			</td>
		</tr>
	</table>
</div>
{/strip}
{include file="$gentemplates/mail_footer_user.tpl"}