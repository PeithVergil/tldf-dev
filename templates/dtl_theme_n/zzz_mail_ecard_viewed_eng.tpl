{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<span class="mtext">{$header.generic_e.hello} <b>{$data.fname}</b>,<br><br>
				{$header.ecard_viewed_e.message} <b>{$data.login2}</b>.<br><br><br>
				{$header.generic_e.admin_regards}<br><br>
				{$header.generic_e.company_info}</span>
			</td>
        </tr>
	</table>
</div>
{include file="$gentemplates/mail_footer_eng.tpl"}