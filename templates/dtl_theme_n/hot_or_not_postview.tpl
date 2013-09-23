{if $rated_userinfo}
<div class="content">
	<div class="header">{$lang.hotornot.postview_header}</div>
	<div class="sep"></div>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td align="left" style="padding-right:5px;">
			<a href="{$rated_userinfo.view_link}"><img src="{$rated_userinfo.icon_url}" border="0" /></a>
		</td>
		<td align="left" valign="top" style="line-height:20px;">
			<table cellpadding="0" cellspacing="0">
			<tr><td><a href="{$rated_userinfo.view_link}"><b>{$rated_userinfo.login}</b></a></td></tr>
			<tr><td>{$rated_userinfo.years} {$lang.home_page.ans}</td></tr>
			<tr><td><b>{$lang.hotornot.official_rating}:</b> {$rated_userinfo.avg_estim}</td></tr>
			<tr><td><b>{$lang.hotornot.count_votes}:</b> {$rated_userinfo.votes_count}</td></tr>
			<tr><td><b>{$lang.hotornot.you_rate}:</b> {$rated_userinfo.estimation}</td></tr>
			</table>
		</td>
	</tr>
	</table>
</div>
<div class="sep"></div>
{/if}