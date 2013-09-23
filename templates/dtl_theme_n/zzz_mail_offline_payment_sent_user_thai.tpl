{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<span class="mtext">{$header.generic_t.hello} <b>{$data.fname}</b>,<br><br>
				{$data.message}<br><br>
				{$header.offline_payment_sent_user_t.contact_me}<br><br><br>
				{$header.generic_t.admin_regards}<br><br>
				{$header.generic_t.company_info}</span>
			</td>
		</tr>
	</table>
</div>
{include file="$gentemplates/mail_footer_thai.tpl"}