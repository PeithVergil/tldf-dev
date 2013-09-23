{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td colspan="2">
				<span class="mtext">{$mail_generic.hello} <b>{$data.fname}</b>,<br><br>
				{$mail_main.message}<br><br></span>
			</td>
		</tr>
		<tr>
			<td class="mtext" width="80" align="center"><img src="{$data.from_icon}" class="icon" alt=""></td>
			<td valign="top" style="padding-left:10px;">
				<div style="margin-top:7px">
					<span class="mtext">
						{if $data.link_viewprofile}
							<a href="{$data.link_viewprofile}" class="mlink"><b>{$data.from_fname}</b></a>
						{else}
							<b>{$data.from_fname}</b>
						{/if}
					</span>
				</div>
				{if $base_lang}
					<div style="margin-top:2px">
						<span class="text_hidden">
						{if $base_lang.city[$data.from_id_city]}{$base_lang.city[$data.from_id_city]}, {/if}
						{if $base_lang.region[$data.from_id_region]}{$base_lang.region[$data.from_id_region]}, {/if}
						{$base_lang.country[$data.from_id_country]}
						</span>
					</div>
				{/if}
				{if $data.from_age}
					<div style="margin-top:2px">
						<span class="text_hidden">{$data.from_age} {$mail_generic.ans}</span>
					</div>
				{/if}
				<div style="margin-top:2px">
					<span class="mtext">
						<b>{$mail_generic.subject}:</b>&nbsp;{$data.subject}
					</span>
				</div>
				<div style="margin-top:2px">
					<span class="mtext">{$data.date}</span>
				</div>
				<div style="margin-top:7px">
					<a href="{$data.link_read}" class="mlink">{$mail_main.read} &raquo;</a>
				</div>
			</td>
		</tr>
		<tr>
            <td colspan="2">
				<span class="mtext"><br>
				{$mail_generic.admin_regards}<br><br>
				{$mail_generic.company_info}</span>
			</td>
        </tr>
	</table>
</div>
{include file="$gentemplates/mail_footer_user.tpl"}