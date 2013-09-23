{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<span class="mtext">{$header.generic_t.hello} <b>{$data.fname}</b>,<br><br>
	{$header.cron_subject_t.two_days}<br><br>
	{$header.cron_message_t.two_days}<br><br><br>
	{$header.cron_job_t.regards}</span>
</div>
{include file="$gentemplates/mail_footer_thai.tpl"}