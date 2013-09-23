{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
    <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td>
				<span class="mtext">{$header.generic_e.hello} <b>{$data.fname}</b>,<br><br>
				{$header.platinum_approved_e.message}<br><br>
				{$header.platinum_approved_e.details}:<br><br></span>
			</td>
        </tr>
        <tr>
            <td>
				<table cellpadding="0" cellspacing="3" border="0">
					<tr>
						<td nowrap><span class="mtext">{$header.platinum_approved_e.login}: &nbsp;</span></td>
						<td><b class="mtext">{$data.login}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.platinum_approved_e.fname}: &nbsp;</span></td>
						<td><b class="mtext">{$data.fname}</b></td>
					</tr>
					<tr>
						<td nowrap><span class="mtext">{$header.platinum_approved_e.sname}: &nbsp;</span></td>
						<td><b class="mtext">{$data.sname}</b></td>
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
{include file="$gentemplates/mail_footer_eng.tpl"}