{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<span class="mtext">{$header.generic_e.hello} <b>{$data.fname}</b>,<br><br>
	{$header.cron_subject_e.register_comp}<br><br>
	{$header.cron_message_e.register_comp}<br><br>
	{$header.generic_e.unsubscribe}<br><br><br>
	{$header.cron_job_e.regards}</span>
</div>
{include file="$gentemplates/mail_footer_eng.tpl"}