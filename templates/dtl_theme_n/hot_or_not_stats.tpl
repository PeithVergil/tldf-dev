{if $user_stats}
<div class="content">
	<div class="header">{$lang.hotornot.stats}</div>
	<div class="sep"></div>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="50%">{if $user_stats.big_icon_url}<img src="{$user_stats.big_icon_url}" border="0" />{/if}</td>
		<td width="100%" valign="bottom" nowrap="nowrap"> 
			{assign var="height" value="150"}
			{assign var="width" value="7"}
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
			{foreach name=vote_stats from=$user_stats.stats item=item}
			<td valign="bottom" align="center" >
			<table cellpadding="0" cellspacing="0" style="display:inline">
			<tr>
				<td height="{math equation=x*y x=$item.height_ratio y=$height}" width="{$width}" class="user_rate_tab_{$smarty.foreach.vote_stats.iteration}" style="font-size:1px;">&nbsp;
					
				</td>
				<td width="1"></td>
			</tr>
			<tr><td class="user_rate_bottom">{$smarty.foreach.vote_stats.iteration}</td><td width="1"></td></tr>
			</table>
			</td>
			{/foreach}
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="padding-top:10px;"><b>{$lang.hotornot.your_rating}: <font class="error">{$user_stats.rating}</font></b></td>
		<td class="text_hidden" style="padding-top:10px;">{$user_stats.votes_count_phrase}</td>
	</tr>
	</table>
</div>
<div class="sep"></div>
{/if}