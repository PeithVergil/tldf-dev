{include file="$gentemplates/mail_header.tpl"}
{strip}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<span class="mtext">{$header.generic_t.hello} <b>{$data.fname}</b>,<br><br>
				{$header.pass_changed_t.message}<br><br>
				{$header.pass_changed_t.details}:<br><br>
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td nowrap><span class="mtext">{$header.pass_changed_t.login}: &nbsp;</span></td>
						<td><b class="mtext">{$data.login}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.pass_changed_t.password}: &nbsp;</span></td>
						<td><b class="mtext">{$data.new_pass}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.pass_changed_t.email}: &nbsp;</span></td>
						<td><b class="mtext">{$data.email}</b></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mtext"><br><br>
				{$header.pass_changed_t.admin_contact} <a href="mailto:{$data.adminemail}">{$data.adminemail}</a><br><br><br>
				{$header.generic_t.admin_regards}<br><br>
				{$header.generic_t.company_info}</span>
			</td>
		</tr>
	</table>
</div>
{/strip}
{include file="$gentemplates/mail_footer_thai.tpl"}