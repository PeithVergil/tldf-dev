{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<table cellpadding="0" cellspacing="5" border="0" width="100%">
		<tr>
			<td width="80" align="center"><img src="{$data.icon}" class="icon" alt=""></td>
			<td width="100%" class="text" valign="top">
				<div style="margin-top:7px">
					<a href="{$data.link_read}" class="text_head">{$data.fname}</a>
				</div>
				<div style="margin-top:2px">
					<span class="mtext">{if $base_lang.city[$data.id_city]}{$base_lang.city[$data.id_city]}, {/if}{if $base_lang.region[$data.id_region]}{$base_lang.region[$data.id_region]}, {/if}{$base_lang.country[$data.id_country]}</span>
				</div>
				<div style="margin-top:2px">
					<span class="text_hidden">{$data.age} {$header.generic_e.ans}</span>
				</div>
				<div style="margin-top:2px">
					<span class="mtext">{$mail_main.viewdate}: {$data.date}</span>
				</div>
				<div style="margin-top:7px">
					<a href="{$data.link_read}" class="mlink">{$mail_generic.viewprofile} &raquo;</a>
				</div>
			</td>
		</tr>
	</table>
</div>
{include file="$gentemplates/mail_footer_user.tpl"}