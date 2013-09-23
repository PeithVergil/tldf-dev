<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Language" content="{$mail_lang}">
<meta http-equiv="Content-Type" content="text/html; charset={$mail_charset}">
<link href="{$site_root}{$template_root}/css/mail.css" rel="stylesheet" type="text/css" media="screen">
</head>
<body>
{strip}
<div class="wrapper">
	<div>
    	<a href="{$data.server.root}" target="_blank">
        	<img src="{$data.server.img_root}/mail_header.jpg" alt="{$data.server.root}">
        </a>
    </div>
    <div style="padding:12px;">
        <div class="text">
            Hello <b>{$data.user.fname} {$data.user.sname},</b><br><br>
            Here are your latest matches on <a href="http://thailadydatefinder.com" target="_blank">ThaiLadyDateFinder.com</a><br><br>
            These members are all recently active and have matched your desired criteria on <a href="http://thailadydatefinder.com" target="_blank">ThaiLadyDateFinder.com</a><br><br>
            Why not show interest or send them a message?
        </div>
        <!--
        <div class="header">{$lang.section.perfect_match}</div>
        <div class="text">{$header_s.toptext_perfect}</div>
        -->
        <div class="mail_content">
            <!-- begin results list -->
            {assign var="search_res" value=$data.search_res}
            {foreach key=key item=item from=$search_res name=s}
                <table cellpadding="0" cellspacing="0" width="100%" border="0">
                    <tr>
                        <td width="{$icon_width+15}" valign="top"><a href="{$item.profile_link}"><img src="{$item.icon_path}" class="icon" alt="{$item.name}" ></a></td>
                        <td valign="top" width="95">
                            <div><a href="{$item.profile_link}"><b>{$item.name}</b></a></div>
                            {if $base_lang.city[$item.id_city] || $base_lang.region[$item.id_region] || $base_lang.country[$item.id_country]}
                            <div style="padding-top:2px;">{if $base_lang.city[$item.id_city]}{$base_lang.city[$item.id_city]}, {/if}{if $base_lang.region[$item.id_region]}{$base_lang.region[$item.id_region]}, {/if}{$base_lang.country[$item.id_country]}</div>
                            {/if}
                            <div style="padding-top:2px;"><font class="text_head">{$item.age} {$lang.home_page.ans}</font></div>
                            <div style="padding-top:2px;"><font class="text_hidden">{$item.photo_count} {$lang.users.upload_1}</font></div>
                        </td>
                        <td valign="top" width="170" style="padding-left: 10px;">
                            <div>{if $form.show_users_group_str eq '1'}<font class="text_head">{$item.group}</font>{/if}</div>
                            <div style="padding-top:5px;"><font class="text_hidden">{$lang.users.gender_search}&nbsp;{$item.gender_search} {$lang.users.from} {$item.age_min} {$lang.users.to} {$item.age_max}</font></div>
                            {*<!--
							<div style="padding-top:2px;"><font class="text">{$lang.homepage.completion}:&nbsp;{$item.completion}%</font></div>
                            -->*}
							<div style="padding-top:2px;"><font class="text">{$lang.matches.concurrence}:&nbsp;{$item.percent}%</font></div>
                        </td>
                        <td valign="top" width="180" align="left" style="padding-left: 10px;"> {if $form.show_users_comments eq '1'} <font class="text_hidden" style="font-size: 10px">{$item.annonce}</font> {/if} </td>
                        <td valign="top" width="40" align="right" style="padding-left: 10px;"> {if $item.hotlisted} <img src="{$site_root}{$template_root}/images/hotlist_icon.gif" alt="{$lang.search.added_to_hotlist}"> {else}&nbsp;{/if} </td>
                    </tr>
                </table>
                {if !$smarty.foreach.s.last}<div class="delimiter"></div>{/if}
            {/foreach}
            <!-- end results list -->
        </div>
        <div class="text">
            If you would like a more tailored match alert list, be sure to update your match information.
            Login now to browse many more new profiles on <a href="{$data.server.root}" target="_blank">ThaiLadyDateFinder.com</a>
            <br><br>
            <a href="{$data.server.root}/index.php?sel=login"><img src="{$data.server.img_root}/login_now.png" alt="LOGIN NOW"></a>
            <br><br>
            Kind Regards,<br>
            The ThaiLadyDateFinder&trade; Team
        </div>
    </div>
	<div>
    	<img src="{$data.server.img_root}/mail_footer.jpg" alt="{$data.server.root}">
	</div>
</div>
{/strip}
</body>
</html>