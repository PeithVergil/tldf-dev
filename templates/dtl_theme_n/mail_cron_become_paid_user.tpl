{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<span class="mtext">{$mail_generic.hello} <b>{$data.fname}</b>,<br><br>
	{$mail_main.subject}<br><br>
	{$mail_main.message}<br><br>
	{$mail_generic.admin_regards}<br><br>
	{$mail_generic.company_info}</span>
</div>
{include file="$gentemplates/mail_footer_user.tpl"}