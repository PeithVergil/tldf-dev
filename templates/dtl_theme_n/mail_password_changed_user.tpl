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
						<td nowrap><span class="mtext">{$mail_main.login}: &nbsp;</span></td>
						<td><b class="mtext">{$data.login}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.password}: &nbsp;</span></td>
						<td><b class="mtext">{$data.new_pass}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$mail_main.email}: &nbsp;</span></td>
						<td><b class="mtext">{$data.email}</b></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mtext"><br>
				{$mail_main.admin_contact} <a href="mailto:{$data.adminemail}">{$data.adminemail}</a><br><br>
				{$mail_generic.admin_regards}<br><br>
				{$mail_generic.company_info}</span>
			</td>
		</tr>
	</table>
</div>
{/strip}
{include file="$gentemplates/mail_footer_user.tpl"}