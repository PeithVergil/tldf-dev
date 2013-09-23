<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Language" content="{$mail_lang}">
<meta http-equiv="Content-Type" content="text/html; charset={$mail_charset}">
<style type="text/css">
<!--
{ $mail_css }
-->
</style>
</head>
<body>
{strip}$lang_mail['cron_birthday']['subject']
<div class="wrapper">
	<div>
		<a href="{$data.server.root}" target="_blank"><img src="{$data.server.img_root}/mail_header.jpg" alt="{$data.server.root}"></a>
	</div>
	<div style="padding:12px;">
		<div>
			<span class="mtext">{$mail_generic.hello} <b>{$data.user.fname}</b>,<br><br>
			{$mail_main.message}</span>
		</div>
		<div class="mail_content">
			{assign var="search_res" value=$data.search_res}
			{foreach key=key item=item from=$search_res name=s}
				<table cellpadding="0" cellspacing="0" width="100%" border="0">
					<tr>
						<td width="{$icon_width+15}" valign="top">
							<a href="{$data.server.root}/{$item.profile_link}"><img src="{$data.server.url}/{$item.icon_path}" class="icon" alt="{$item.name}"></a>
						</td>
						<td valign="top" width="95">
							<div><a href="{$item.profile_link}"><b>{$item.name}</b></a></div>
							{if $base_lang.city[$item.id_city] || $base_lang.region[$item.id_region] || $base_lang.country[$item.id_country]}
								<div style="padding-top:2px;">{if $base_lang.city[$item.id_city]}{$base_lang.city[$item.id_city]}, {/if}{if $base_lang.region[$item.id_region]}{$base_lang.region[$item.id_region]}, {/if}{$base_lang.country[$item.id_country]}</div>
							{/if}
							<div style="padding-top:2px;"><span class="text_head">{$item.age} {$lang.home_page.ans}</span></div>
							<div style="padding-top:2px;"><span class="text_hidden">{$item.photo_count} {$lang.users.upload_1}</span></div>
						</td>
						<td valign="top" width="170" style="padding-left:10px;">
							<div>{if $form.show_users_group_str == '1'}<span class="text_head">{$item.group}</span>{/if}</div>
							<div style="padding-top:5px;"><span class="text_hidden">{$lang.users.gender_search}&nbsp;{$item.gender_search} {$lang.users.from} {$item.age_min} {$lang.users.to} {$item.age_max}</span></div>
							{*<!--
							<div style="padding-top:2px;"><span class="mtext">{$lang.homepage.completion}:&nbsp;{$item.completion}%</span></div>
							-->*}
							<div style="padding-top:2px;"><span class="mtext">{$lang.matches.concurrence}:&nbsp;{$item.percent}%</span></div>
						</td>
						<td valign="top" width="180" align="left" style="padding-left:10px;">
							{if $form.show_users_comments == '1'}
								<span class="text_hidden" style="font-size:10px">{$item.annonce}</span>
							{/if}
						</td>
						<td valign="top" width="40" align="right" style="padding-left:10px;">
							{if $item.hotlisted}
								<img src="{$data.server.img_root}/hotlist_icon.gif" alt="{$lang.search.added_to_hotlist}">
							{else}
								&nbsp;
							{/if}
						</td>
					</tr>
				</table>
				{if !$smarty.foreach.s.last}<div class="delimiter"></div>{/if}
			{/foreach}
		</div>
		<div>
			<span class="mtext">{$mail_main.message_2}<br><br>
			<a href="{$data.server.root}/index.php?sel=login"><img src="{$data.server.img_root}/login_now.png" alt="LOGIN NOW"></a><br><br>
			{$mail_generic.admin_regards}<br><br>
			{$mail_generic.company_info}</span>
		</div>
	</div>
</div>
<div>&nbsp;</div>
{/strip}
</body>
</html>