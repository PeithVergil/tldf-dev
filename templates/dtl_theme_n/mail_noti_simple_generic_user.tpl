{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<span class="mtext">{$mail_generic.hello} <b>{$data.fname}</b>,<br><br>
				{$data.message}<br><br>
				{$mail_generic.admin_regards}<br><br>
				{$mail_generic.company_info}</span>
			</td>
        </tr>
	</table>
</div>
{include file="$gentemplates/mail_footer_user.tpl"}