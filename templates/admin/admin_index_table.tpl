{include file="$admingentemplates/admin_top.tpl"}
<div>
	<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.razdel_name_home}</font>
</div>
{if $form.err}
	<div>{$form.err}</div>
{else}
	<div class="help_text">
		<span class="help_title">{$lang.help}:</span>{$help.site_stat}
	</div>
	<div style="padding-bottom:10px;">
		<font class="forumtitletext">{$header.list}</font>
	</div>
	<div class="statistics">
		<div class="left_col">
			<table class="table_main" cellpadding="5" cellspacing="1" width="100%">
				<tr class="header">
					<td align="right"><b>{$header.stat_head_lady}</b></td>
					<td align="center" width="70"><b>{$header.stat_head_col1}</b></td>
					<td align="center" width="70"><b>{$header.stat_head_col2}</b></td>
					<td align="center" width="70"><b>{$header.stat_head_col3}</b></td>
					<td align="center" width="70"><b>{$header.stat_head_col4}</b></td>
				</tr>
				<tr>
					<td align="right">{$header.stat_lady_signup}:</td>
					<td align="center"><b>{$stat.lady_signup.now}</b></td>
					<td align="center"><b>{$stat.lady_signup.seven_days}</b></td>
					<td align="center"><b>{$stat.lady_signup.thirty_days}</b></td>
					<td align="center"><b>{$stat.lady_signup.all_time}</b></td>
				</tr>
				<tr>
					<td align="right">{$header.stat_lady_trial}:</td>
					<td align="center"><b>{$stat.lady_trial.now}</b></td>
					<td align="center"><b>{$stat.lady_trial.seven_days}</b></td>
					<td align="center"><b>{$stat.lady_trial.thirty_days}</b></td>
					<td align="center"><b>{$stat.lady_trial.all_time}</b></td>
				</tr>
				{*<!--
				<tr>
					<td align="right">{$header.stat_lady_trial_pending}:</td>
					<td align="center"><b>{$stat.lady_trial_pending.now}</b></td>
					<td align="center"><b>{$stat.lady_trial_pending.seven_days}</b></td>
					<td align="center"><b>{$stat.lady_trial_pending.thirty_days}</b></td>
					<td align="center"><b>{$stat.lady_trial_pending.all_time}</b></td>
				</tr>
				<tr>
					<td align="right">{$header.stat_lady_trial_onhold}:</td>
					<td align="center"><b>{$stat.lady_trial_onhold.now}</b></td>
					<td align="center"><b>{$stat.lady_trial_onhold.seven_days}</b></td>
					<td align="center"><b>{$stat.lady_trial_onhold.thirty_days}</b></td>
					<td align="center"><b>{$stat.lady_trial_onhold.all_time}</b></td>
				</tr>
				-->*}
				<tr>
					<td align="right">{$header.stat_lady_trial_cancelled}:</td>
					<td align="center" class="disable_cell"><b>{$stat.lady_trial_cancelled.now}</b></td>
					<td align="center"><b>{$stat.lady_trial_cancelled.seven_days}</b></td>
					<td align="center"><b>{$stat.lady_trial_cancelled.thirty_days}</b></td>
					<td align="center"><b>{$stat.lady_trial_cancelled.all_time}</b></td>
				</tr>
				<tr>
					<td align="right">{$header.stat_lady_regular}:</td>
					<td align="center"><b>{$stat.lady_regular.now}</b></td>
					<td align="center"><b>{$stat.lady_regular.seven_days}</b></td>
					<td align="center"><b>{$stat.lady_regular.thirty_days}</b></td>
					<td align="center"><b>{$stat.lady_regular.all_time}</b></td>
				</tr>
				{*<!--
				<tr>
					<td align="right">{$header.stat_lady_regular_pending}:</td>
					<td align="center"><b>{$stat.lady_regular_pending.now}</b></td>
					<td align="center"><b>{$stat.lady_regular_pending.seven_days}</b></td>
					<td align="center"><b>{$stat.lady_regular_pending.thirty_days}</b></td>
					<td align="center"><b>{$stat.lady_regular_pending.all_time}</b></td>
				</tr>
				<tr>
					<td align="right">{$header.stat_lady_regular_onhold}:</td>
					<td align="center"><b>{$stat.lady_regular_onhold.now}</b></td>
					<td align="center"><b>{$stat.lady_regular_onhold.seven_days}</b></td>
					<td align="center"><b>{$stat.lady_regular_onhold.thirty_days}</b></td>
					<td align="center"><b>{$stat.lady_regular_onhold.all_time}</b></td>
				</tr>
				-->*}
				<tr>
					<td align="right">{$header.stat_lady_regular_cancelled}:</td>
					<td align="center" class="disable_cell"><b>{$stat.lady_regular_cancelled.now}</b></td>
					<td align="center"><b>{$stat.lady_regular_cancelled.seven_days}</b></td>
					<td align="center"><b>{$stat.lady_regular_cancelled.thirty_days}</b></td>
					<td align="center"><b>{$stat.lady_regular_cancelled.all_time}</b></td>
				</tr>
				<tr>
					<td align="right">{$header.stat_lady_platinum_pending}:</td>
					<td align="center"><b>{$stat.lady_platinum_pending.now}</b></td>
					<td align="center"><b>{$stat.lady_platinum_pending.seven_days}</b></td>
					<td align="center"><b>{$stat.lady_platinum_pending.thirty_days}</b></td>
					<td align="center"><b>{$stat.lady_platinum_pending.all_time}</b></td>
				</tr>
				<tr>
					<td align="right">{$header.stat_lady_platinum}:</td>
					<td align="center"><b>{$stat.lady_platinum.now}</b></td>
					<td align="center"><b>{$stat.lady_platinum.seven_days}</b></td>
					<td align="center"><b>{$stat.lady_platinum.thirty_days}</b></td>
					<td align="center"><b>{$stat.lady_platinum.all_time}</b></td>
				</tr>
				{*<!--
				<tr>
					<td align="right">{$header.stat_lady_platinum_onhold}:</td>
					<td align="center"><b>{$stat.lady_platinum_onhold.now}</b></td>
					<td align="center"><b>{$stat.lady_platinum_onhold.seven_days}</b></td>
					<td align="center"><b>{$stat.lady_platinum_onhold.thirty_days}</b></td>
					<td align="center"><b>{$stat.lady_platinum_onhold.all_time}</b></td>
				</tr>
				-->*}
				<tr>
					<td align="right">{$header.stat_lady_platinum_cancelled}:</td>
					<td align="center" class="disable_cell"><b>{$stat.lady_platinum_cancelled.now}</b></td>
					<td align="center"><b>{$stat.lady_platinum_cancelled.seven_days}</b></td>
					<td align="center"><b>{$stat.lady_platinum_cancelled.thirty_days}</b></td>
					<td align="center"><b>{$stat.lady_platinum_cancelled.all_time}</b></td>
				</tr>
				<tr>
					<td align="right">{$header.stat_lady_total_cancelled}:</td>
					<td align="center" class="disable_cell"><b>{$stat.lady_total_cancelled.now}</b></td>
					<td align="center"><b>{$stat.lady_total_cancelled.seven_days}</b></td>
					<td align="center"><b>{$stat.lady_total_cancelled.thirty_days}</b></td>
					<td align="center"><b>{$stat.lady_total_cancelled.all_time}</b></td>
				</tr>
				<tr>
					<td align="right">{$header.stat_lady_total}:</td>
					<td align="center"><b>{$stat.lady_total.now}</b></td>
					<td align="center"><b>{$stat.lady_total.seven_days}</b></td>
					<td align="center"><b>{$stat.lady_total.thirty_days}</b></td>
					<td align="center"><b>{$stat.lady_total.all_time}</b></td>
				</tr>
			</table>
		</div>
		<div class="right_col">
			<table class="table_main" cellpadding="5" cellspacing="1" width="100%">
				<tr class="header">
					<td align="right"><b>{$header.stat_head_guy}</b></td>
					<td align="center" width="70"><b>{$header.stat_head_col1}</b></td>
					<td align="center" width="70"><b>{$header.stat_head_col2}</b></td>
					<td align="center" width="70"><b>{$header.stat_head_col3}</b></td>
					<td align="center" width="70"><b>{$header.stat_head_col4}</b></td>
				</tr>
				<tr>
					<td align="right">{$header.stat_guy_signup}:</td>
					<td align="center"><b>{$stat.guy_signup.now}</b></td>
					<td align="center"><b>{$stat.guy_signup.seven_days}</b></td>
					<td align="center"><b>{$stat.guy_signup.thirty_days}</b></td>
					<td align="center"><b>{$stat.guy_signup.all_time}</b></td>
				</tr>
				<tr>
					<td align="right">{$header.stat_guy_trial}:</td>
					<td align="center"><b>{$stat.guy_trial.now}</b></td>
					<td align="center"><b>{$stat.guy_trial.seven_days}</b></td>
					<td align="center"><b>{$stat.guy_trial.thirty_days}</b></td>
					<td align="center"><b>{$stat.guy_trial.all_time}</b></td>
				</tr>
				{*<!--
				<tr>
					<td align="right">{$header.stat_guy_trial_pending}:</td>
					<td align="center"><b>{$stat.guy_trial_pending.now}</b></td>
					<td align="center"><b>{$stat.guy_trial_pending.seven_days}</b></td>
					<td align="center"><b>{$stat.guy_trial_pending.thirty_days}</b></td>
					<td align="center"><b>{$stat.guy_trial_pending.all_time}</b></td>
				</tr>
				<tr>
					<td align="right">{$header.stat_guy_trial_onhold}:</td>
					<td align="center"><b>{$stat.guy_trial_onhold.now}</b></td>
					<td align="center"><b>{$stat.guy_trial_onhold.seven_days}</b></td>
					<td align="center"><b>{$stat.guy_trial_onhold.thirty_days}</b></td>
					<td align="center"><b>{$stat.guy_trial_onhold.all_time}</b></td>
				</tr>
				-->*}
				<tr>
					<td align="right">{$header.stat_guy_trial_cancelled}:</td>
					<td align="center" class="disable_cell"><b>{$stat.guy_trial_cancelled.now}</b></td>
					<td align="center"><b>{$stat.guy_trial_cancelled.seven_days}</b></td>
					<td align="center"><b>{$stat.guy_trial_cancelled.thirty_days}</b></td>
					<td align="center"><b>{$stat.guy_trial_cancelled.all_time}</b></td>
				</tr>
				<tr>
					<td align="right">{$header.stat_guy_regular}:</td>
					<td align="center"><b>{$stat.guy_regular.now}</b></td>
					<td align="center"><b>{$stat.guy_regular.seven_days}</b></td>
					<td align="center"><b>{$stat.guy_regular.thirty_days}</b></td>
					<td align="center"><b>{$stat.guy_regular.all_time}</b></td>
				</tr>
				{*<!--
				<tr>
					<td align="right">{$header.stat_guy_regular_pending}:</td>
					<td align="center"><b>{$stat.guy_regular_pending.now}</b></td>
					<td align="center"><b>{$stat.guy_regular_pending.seven_days}</b></td>
					<td align="center"><b>{$stat.guy_regular_pending.thirty_days}</b></td>
					<td align="center"><b>{$stat.guy_regular_pending.all_time}</b></td>
				</tr>
				<tr>
					<td align="right">{$header.stat_guy_regular_onhold}:</td>
					<td align="center"><b>{$stat.guy_regular_onhold.now}</b></td>
					<td align="center"><b>{$stat.guy_regular_onhold.seven_days}</b></td>
					<td align="center"><b>{$stat.guy_regular_onhold.thirty_days}</b></td>
					<td align="center"><b>{$stat.guy_regular_onhold.all_time}</b></td>
				</tr>
				-->*}
				<tr>
					<td align="right">{$header.stat_guy_regular_cancelled}:</td>
					<td align="center" class="disable_cell"><b>{$stat.guy_regular_cancelled.now}</b></td>
					<td align="center"><b>{$stat.guy_regular_cancelled.seven_days}</b></td>
					<td align="center"><b>{$stat.guy_regular_cancelled.thirty_days}</b></td>
					<td align="center"><b>{$stat.guy_regular_cancelled.all_time}</b></td>
				</tr>
				<tr>
					<td align="right">{$header.stat_guy_platinum_pending}:</td>
					<td align="center"><b>{$stat.guy_platinum_pending.now}</b></td>
					<td align="center"><b>{$stat.guy_platinum_pending.seven_days}</b></td>
					<td align="center"><b>{$stat.guy_platinum_pending.thirty_days}</b></td>
					<td align="center"><b>{$stat.guy_platinum_pending.all_time}</b></td>
				</tr>
				<tr>
					<td align="right">{$header.stat_guy_platinum}:</td>
					<td align="center"><b>{$stat.guy_platinum.now}</b></td>
					<td align="center"><b>{$stat.guy_platinum.seven_days}</b></td>
					<td align="center"><b>{$stat.guy_platinum.thirty_days}</b></td>
					<td align="center"><b>{$stat.guy_platinum.all_time}</b></td>
				</tr>
				{*<!--
				<tr>
					<td align="right">{$header.stat_guy_platinum_onhold}:</td>
					<td align="center"><b>{$stat.guy_platinum_onhold.now}</b></td>
					<td align="center"><b>{$stat.guy_platinum_onhold.seven_days}</b></td>
					<td align="center"><b>{$stat.guy_platinum_onhold.thirty_days}</b></td>
					<td align="center"><b>{$stat.guy_platinum_onhold.all_time}</b></td>
				</tr>
				-->*}
				<tr>
					<td align="right">{$header.stat_guy_platinum_cancelled}:</td>
					<td align="center" class="disable_cell"><b>{$stat.guy_platinum_cancelled.now}</b></td>
					<td align="center"><b>{$stat.guy_platinum_cancelled.seven_days}</b></td>
					<td align="center"><b>{$stat.guy_platinum_cancelled.thirty_days}</b></td>
					<td align="center"><b>{$stat.guy_platinum_cancelled.all_time}</b></td>
				</tr>
				<tr>
					<td align="right">{$header.stat_guy_total_cancelled}:</td>
					<td align="center" class="disable_cell"><b>{$stat.guy_total_cancelled.now}</b></td>
					<td align="center"><b>{$stat.guy_total_cancelled.seven_days}</b></td>
					<td align="center"><b>{$stat.guy_total_cancelled.thirty_days}</b></td>
					<td align="center"><b>{$stat.guy_total_cancelled.all_time}</b></td>
				</tr>
				<tr>
					<td align="right">{$header.stat_guy_total}:</td>
					<td align="center"><b>{$stat.guy_total.now}</b></td>
					<td align="center"><b>{$stat.guy_total.seven_days}</b></td>
					<td align="center"><b>{$stat.guy_total.thirty_days}</b></td>
					<td align="center"><b>{$stat.guy_total.all_time}</b></td>
				</tr>
			</table>
		</div>
		<div class="clear"></div>
		<div style="padding-top:30px;">
			<table class="table_main" cellpadding="5" cellspacing="1">
				<tr>
					<td align="right" width="250">{$header.stat_all}:</td>
					<td align="center"><b>{$form.stat_all}</b></td>
				</tr>
				<tr>
					<td align="right">{$header.stat_men}:</td>
					<td align="center"><b>{$form.stat_men}</b></td>
				</tr>
				<tr>
					<td align="right">{$header.stat_women}:</td>
					<td align="center"><b>{$form.stat_women}</b></td>
				</tr>
				<tr>
					<td align="right"><a href="{$form.stat_active_link}">{$header.stat_active}:</a></td>
					<td align="center"><b>{$form.stat_active}</b></td>
				</tr>
				<tr>
					<td align="right"><a href="{$form.stat_chat_link}">{$header.stat_chat}:</a></td>
					<td align="center"><b>{$form.stat_chat}</b></td>
				</tr>
				<tr>
					<td align="right"><a href="{$form.stat_reg_today_link}">{$header.stat_reg_today}:</a></td>
					<td align="center"><b>{$form.stat_reg_today}</b></td>
				</tr>
				<tr>
					<td align="right"><a href="{$form.stat_last_week_link}">{$header.stat_last_week}:</a></td>
					<td align="center"><b>{$form.stat_last_week}</b></td>
				</tr>
				<tr>
					<td align="right"><a href="{$form.stat_last_month_link}">{$header.stat_last_month}:</a></td>
					<td align="center"><b>{$form.stat_last_month}</b></td>
				</tr>
				<tr>
					<td align="right">{$header.stat_most_active}:</td>
					<td align="center"><b>{$form.stat_most_active}</b></td>
				</tr>
				<tr>
					<td align="right">{$header.stat_most_popular}:</td>
					<td align="center"><b>{$form.stat_most_popular}</b></td>
				</tr>
				<tr>
					<td align="right">{$header.stat_most_popular_module}:</td>
					<td align="center"><b>{$form.stat_most_popular_module}</b></td>
				</tr>
				<tr>
					<td align="right">{$header.stat_not_seen_module}:</td>
					<td align="center"><b>{$form.stat_not_seen_module}</b></td>
				</tr>
			</table>
		</div>
	</div>
{/if}
{include file="$admingentemplates/admin_bottom.tpl"}