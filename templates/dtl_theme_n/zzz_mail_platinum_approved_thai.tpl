{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
    <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td>
				<span class="mtext">{$header.generic_t.hello} <b>{$data.fname}</b>,<br><br>
				{$header.platinum_approved_t.message}<br><br>
				{$header.platinum_approved_t.details}:<br><br></span>
			</td>
        </tr>
        <tr>
            <td>
				<table cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td nowrap><span class="mtext">{$header.platinum_approved_t.login}: &nbsp;</span></td>
						<td><b class="mtext">{$data.login}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.platinum_approved_t.fname}: &nbsp;</span></td>
						<td><b class="mtext">{$data.fname}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.platinum_approved_t.sname}: &nbsp;</span></td>
						<td><b class="mtext">{$data.sname}</b></td>
					</tr>
				</table>
			</td>
        </tr>
        <tr>
            <td>
				<span class="mtext"><br><br>
				{$header.generic_t.admin_regards}<br><br>
				{$header.generic_t.company_info}</span>
			</td>
        </tr>
    </table>
</div>
{include file="$gentemplates/mail_footer_thai.tpl"}