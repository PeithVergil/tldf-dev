{include file="$gentemplates/mail_header.tpl"}
<div class="mail_content">
	<table cellpadding="5" cellspacing="0">
		<tr>
			<td>
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td valign="top" style="padding-right:25px;">
							<img src="{$data.server.img_root}/nathamon.png" alt="Nathamon" border="0">
						</td>
						<td valign="top">
							<span class="mtext">{$header.admin_promo.hello} <b>{$user.fname}</b>,<br><br>
							{$data.body_text}<br><br></span>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<div class="promo_mail" style="background-color:#e1c7da; padding:10px 10px 5px 10px;">
					{section name=f loop=$promo_user}
						<table cellpadding="5" cellspacing="5" width="100%" border="0">
							<tr>
								<td bgcolor="#fbeffb" class="list_item" width="{$icon_width+15}" valign="top">
									<a href="{$promo_user[f].profile_link}" target="_blank"><img src="{$promo_user[f].icon_path}" class="icon" alt="{$promo_user[f].name}" border="0"></a>
								</td>
								<td bgcolor="#fbeffb" class="list_item" valign="top" width="150">
									<div><a href="{$promo_user[f].profile_link}" target="_blank"><b>{$promo_user[f].name}</b></a></div>
									{assign var=str_id_city value=$promo_user[f].id_city}
									{assign var=str_id_region value=$promo_user[f].id_region}
									{assign var=str_id_country value=$promo_user[f].id_country}
									<div style="padding-top:0px;">{if $base_lang.city[$str_id_city]}{$base_lang.city[$str_id_city]}, {/if}{if $base_lang.region[$str_id_region]}{$base_lang.region[$str_id_region]}, {/if}{$base_lang.country[$str_id_country]}</div>
									<div style="padding-top:0px;"><span class="text_head">{$promo_user[f].age} {$lang.home_page.ans}</span></div>
									<div style="padding-top:0px; font-size:90%">{$lang.users.gender_search}&nbsp;{$promo_user[f].gender_search} {$lang.users.from} {$promo_user[f].age_min} {$lang.users.to} {$promo_user[f].age_max}</div>
								</td>
								<td bgcolor="#fbeffb" class="list_item" valign="top" style="padding-left: 10px;">
									<div>{$promo_user[f].about_me}..</div>
									<div align="right"><a href="{$promo_user[f].profile_link}" target="_blank"><b>{$lang.users.read_more}</b></a></div>
								</td>
							</tr>
						</table>
						<div style="height:2px; background-color:#e1c7da;"></div>
					{/section}
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div class="justify">
					<span class="mtext">{$data.footer_text}</span>
				</div>
				<div>
					<span class="mtext"><br>
					{$header.generic_e.admin_regards}<br><br>
					{$header.generic_e.company_info}</span>
				</div>
			</td>
		</tr>
	</table>
</div>
{include file="$gentemplates/mail_footer_eng.tpl"}