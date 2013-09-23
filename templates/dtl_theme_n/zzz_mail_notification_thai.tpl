{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<span class="mtext">{$header.generic_t.hello} <b>{$data.fname}</b>,<br><br>
				{$data.message}<br><br><br>
				{$header.notification_t.regards}</span>
			</td>
		</tr>
	</table>
</div>
{include file="$gentemplates/mail_footer_thai.tpl"}