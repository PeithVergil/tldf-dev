{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<span class="mtext">{$header.generic_e.hello} <b>{$data.fname}</b>,<br><br>
	{$header.cron_subject_e.one_week}<br><br>
	{$header.cron_message_e.one_week}<br><br><br>
	{$header.generic_e.cron_regards}</span>
</div>
{include file="$gentemplates/mail_footer_eng.tpl"}