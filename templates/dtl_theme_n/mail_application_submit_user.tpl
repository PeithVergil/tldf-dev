{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<span class="mtext">{$mail_generic.hello} <b>{$data.fname}</b>,<br><br>
				{$mail_main.message_top}<br><br>
				{*
				{if $gender == 1}
					<a href="{$data.urls.video_eng}" target="_blank">
					<img src="{$data.server.img_root}/mail_video1.jpg" alt="{$data.urls.video_eng}" border="0"></a><br><br>
				{else}
					<a href="{$data.urls.video_thai}" target="_blank">
					<img src="{$data.server.img_root}/mail_video2.jpg" alt="{$data.urls.video_thai}" border="0"></a><br><br>
				{/if}
				*}
				{$mail_main.message}<br><br>
				{$mail_generic.admin_regards}<br><br>
				{$mail_generic.company_info}
				{if $mail_main.ps}
					<br><br>{$mail_main.ps}
				{/if}
				</span>
			</td>
		</tr>
	</table>
</div>
{include file="$gentemplates/mail_footer_user.tpl"}