{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<span class="mtext">{$header.generic_t.hello} <b>{$data.fname}</b>,<br><br>
	{$header.cron_subject_t.register_comp}<br><br>
	{$header.cron_message_t.register_comp}<br><br>
	{$header.generic_t.unsubscribe}<br><br><br>
	{$header.cron_job_t.regards}</span>
</div>
{include file="$gentemplates/mail_footer_thai.tpl"}