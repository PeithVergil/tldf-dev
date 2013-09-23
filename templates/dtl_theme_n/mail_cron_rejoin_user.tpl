{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<span class="mtext">{$mail_generic.hello} <b>{$data.fname}</b>,<br><br>
	{$data.subject}<br><br>
	{$data.message}<br><br>
	{$mail_generic.admin_regards}<br><br>
	{$mail_generic.company_info}<br><br><br>
	{$mail_generic.unsubscribe}<br>
	<a target="_blank" href="{$data.unsubscribe_url}">{$data.unsubscribe_url}</a></span>
</div>
{include file="$gentemplates/mail_footer_user.tpl"}