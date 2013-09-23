{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<span class="mtext">{$header.generic_t.hello} <b>{$data.fname}</b>,<br><br>
	{$header.cron_subject_t.last_day}<br><br>
	{$header.cron_message_t.last_day}<br><br><br>
	{$header.generic_t.cron_regards}</span>
</div>
{include file="$gentemplates/mail_footer_thai.tpl"}