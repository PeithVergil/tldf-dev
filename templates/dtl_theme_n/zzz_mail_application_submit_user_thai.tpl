{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<span class="mtext">{$header.generic_e.hello} <b>{$data.fname}</b>,<br><br>
				{$header.application_submit_t.message_top}<br><br>
				{* <!--
					<a href="{$data.urls.video_thai}" target="_blank">
					<img src="{$data.server.img_root}/mail_video2.jpg" alt="{$data.urls.video_thai}" border="0"></a><br><br>
				--> *}
				{$header.application_submit_t.message}<br><br><br>
				{$header.generic_e.admin_regards}<br><br>
				{$header.generic_e.company_info}
				{if $header.application_submit_t.ps}
					<br><br>{$header.application_submit_t.ps}
				{/if}
				</span>
			</td>
		</tr>
	</table>
</div>
{include file="$gentemplates/mail_footer_thai.tpl"}