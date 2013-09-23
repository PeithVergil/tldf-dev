{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple account">
	{*
		<div class="text">{$header.toptext}</div>
	*}
	{if $form.err}
		<div class="error_msg" style="padding-bottom:10px;">{$form.err}</div>
	{/if}
	<div class="text" style="padding-top:0px; padding-bottom:12px;">
		{* REGISTRATION INFORMATION *}
		<div class="content">
			<div class="hdr2" style="padding-top:0px;">{$lang.account.subheader_info}</div>
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="150" style="padding-top:0px;" class="text" valign="top">{$header.info_login}:&nbsp;</td>
					<td style="padding-top:0px;" class="text" valign="top"><b>{$data.login}</b></td>
				</tr>
				<tr>
					<td style="padding-top:12px;" class="text" valign="top">{$header.info_email}:&nbsp;</td>
					<td style="padding-top:12px;" class="text" valign="top"><b>{$data.email}</b></td>
				</tr>
				<tr>
					<td style="padding-top:12px;" class="text" valign="top">{$header.info_password}:&nbsp;</td>
					<td style="padding-top:12px;" class="text" valign="top"><a href="account.php?sel=passw">{$header.change_password}</a></td>
				</tr>
			</table>
		</div>
		{if ! $form.free_site}
			<div class="content" style="padding-top:12px;">
				<div class="hdr2">{$lang.account.subheader_account}</div>
				<table cellpadding="0" cellspacing="0">
					{* CURRENT GROUP WITH UPGRADE OR EXTEND LINK *}
					<tr>
						<td width="150" valign="top" class="text" style="padding-top:0px;">{$header.my_group}:</td>
						<td valign="top" class="text" style="padding-top:0px;">							
							<b>{$active_user_info.user_group}</b>&nbsp;&nbsp;&nbsp; 
							{* KEEP IN SYNC WITH HOMEPAGE_TABLE.TPL : START *}
							{if $auth.is_applicant}
								{*<!-- no upgrade or extend link, signup user is automatically moved to Trial group -->*}
							{elseif $auth.is_trial}
								{if ! $data.offline_paysystem_regular_upgrade_send}
									<a href="payment.php?sel=buy_connection">{$lang.button.get_more_connections}</a>
								{/if}
								{if !$data.platinum_paid}
									{if ! $data.offline_paysystem_regular_upgrade_send}
										&nbsp; | &nbsp;
									{/if}
									<a href="platinum_match.php">{$header.apply_for_platinum_membership}</a>
								{/if}
							{elseif $active_user_info.recurring && ! $active_user_info.canceled}
								{*<!-- Running subscription. Not in use right now. -->*}
								{if $auth.is_regular && ! $data.platinum_paid}
									<a href="platinum_match.php">{$header.apply_for_platinum_membership}</a>
								{/if}
							{elseif $auth.is_regular}
								{if !$data.platinum_paid}
									<a href="platinum_match.php">{$header.apply_for_platinum_membership}</a>
								{/if}
							{elseif $data.onInstallments}
								|&nbsp; <a href="platinum_match.php">{$data.installment}</a>
							{/if}
							{* KEEP IN SYNC WITH HOMEPAGE_TABLE.TPL : END *}
						</td>
					</tr>
					{if !$auth.is_applicant}
						<!-- PERIOD INFO (CURRENT OR LAST ACTIVE) -->
						<tr>
							<td valign="top" class="text" style="padding-top:12px;">
								{if $active_user_info.days_remain >= 0}{$header.account_period}{else}{$header.last_active_period}{/if}:
							</td>
							<td valign="top" class="text" style="padding-top:12px;">
								<b>{$active_user_info.period}</b> 
							</td>
						</tr>
						<!-- DAYS REMAINING -->
						<tr>
							<td valign="top" class="text" style="padding-top:12px;">{$header.account_period_rest}:</td>
							<td valign="top" class="text" style="padding-top:12px;">
								{* KEEP IN SYNC WITH ACCOUNT_TABLE.TPL : START *}
								{if $active_user_info.unlimited}
									<b>{$header.unlimited}</b>
								{elseif $active_user_info.recurring}
									<b>{$header.until_canceled}</b>
								{elseif $active_user_info.days_remain < 0}
									{*<!-- <b class="txtred">{$header.expired}</b> -->*}
									<b class="txtred">0&nbsp;{$header.account_period_day}</b>
								{elseif $active_user_info.days_remain < 8}
									<b class="txtred">{$active_user_info.days_remain}&nbsp;{$header.account_period_day}</b>
								{else}
									<b>{$active_user_info.days_remain}&nbsp;{$header.account_period_day}</b>
								{/if}
								&nbsp;&nbsp;&nbsp;
								{if ! $active_user_info.recurring}
									{if $auth.gender == GENDER_FEMALE}
										{if !in_array($auth.id_group, array(MM_PLATINUM_LADY_FIRST_INS_ID,MM_PLATINUM_LADY_SECOND_INS_ID,MM_PLATINUM_LADY_ID))}
											<a href="payment.php?sel=buy_connection">{$header.get_more_days}</a>
										{/if}
									{/if}
								{/if}
								{* KEEP IN SYNC WITH HOMEPAGE_TABLE.TPL : START *}
							</td>
						</tr>
						<!-- CREDITS POINTS -->
						<tr>
							<td valign="top" class="text" style="padding-top:12px;">{$header.account_count}:</td>
							<td valign="top" class="text" style="padding-top:12px;">
								<b>{$data.count} {$data.account_currency}</b>
								{* KEEP IN SYNC WITH HOMEPAGE_TABLE.TPL : START *}
								{*<!-- &nbsp;&nbsp;&nbsp;<a href="payment.php?sel=update_account">{$lang.button.update_account}</a> -->*}
								{if !$auth.is_applicant}
									{if $auth.gender == GENDER_MALE}
										&nbsp;&nbsp;&nbsp;<a href="payment.php?sel=buy_connection">{$header.get_more_points}</a>
									{else}
										{if $auth.is_trial && ! $data.offline_paysystem_regular_upgrade_send}
											&nbsp;&nbsp;&nbsp;<a href="payment.php?sel=buy_connection">{$lang.button.get_more_connections}</a>
										{/if}
									{/if}
								{/if}
								{* KEEP IN SYNC WITH HOMEPAGE_TABLE.TPL : END *}
							</td>
						</tr>
						{if $auth.gender == GENDER_MALE}
							<tr>
								<td colspan="2" class="text_hidden" style="padding-top:12px;">
									{$header.account_count_hint}
								</td>
							</tr>
						{/if}
						<!-- RECURRING INFO -->
						{if $active_user_info.recurring}
							<tr>
								<td valign="top" class="text" style="padding-top:12px;">{$header.additional_info_label}:</td>
								<td valign="top" class="text" style="padding-top:12px; font-weight:bold;">
									{if $active_user_info.canceled}
										{$header.canceled_info_text} {$active_user_info.date_end}
									{else}
										{$header.cancel_info_text}
									{/if}
								</td>
							</tr>
						{/if}
						<!-- PLATINUM INFO -->
						{if $auth.is_trial || $auth.is_regular}
							{if $data.offline_paysystem_platinum_approve}
								<tr>
									<td valign="top" class="text" style="padding-top:12px;">{$header.platinum_info_label}:</td>
									<td valign="top" class="text" style="padding-top:12px; font-weight:bold; color:red;">
										{if $data.offline_paysystem_platinum_approve == 'atm_payment'}
											{$header.atm_payment_platinum_approve}
										{elseif $data.offline_paysystem_platinum_approve == 'wire_transfer'}
											{$header.wire_transfer_platinum_approve}
										{elseif $data.offline_paysystem_platinum_approve == 'bank_cheque'}
											{$header.bank_cheque_platinum_approve}
										{/if}
										<br>{$header.platinum_approve_wait}
									</td>
								</tr>
							{elseif $data.offline_paysystem_platinum_send}
								<tr>
									<td valign="top" class="text" style="padding-top:12px;">{$header.platinum_info_label}:</td>
									<td valign="top" class="text" style="padding-top:12px; font-weight:bold; color:red;">
										{if $data.offline_paysystem_platinum_send == 'atm_payment'}
											{$header.atm_payment_platinum_send}
										{elseif $data.offline_paysystem_platinum_send == 'wire_transfer'}
											{$header.wire_transfer_platinum_send}
										{elseif $data.offline_paysystem_platinum_send == 'bank_cheque'}
											{$header.bank_cheque_platinum_send}
										{/if}
									</td>
								</tr>
							{elseif $data.platinum_paid}
								<tr>
									<td valign="top" class="text" style="padding-top:12px;">{$header.platinum_info_label}:</td>
									<td valign="top" class="text" style="padding-top:12px; font-weight:bold;">
										{$header.platinum_paid_info_text}
										<br>{$header.platinum_approve_wait}
									</td>
								</tr>
							{/if}
						{/if}
						<!-- OFFLINE PAYMENT INFO -->
						{if $data.offline_paysystem_buy_days_send || $data.offline_paysystem_regular_upgrade_send || $data.offline_paysystem_credit_points_send}
							<tr>
								<td valign="top" class="text" style="padding-top:12px;">{$header.additional_info_label}:</td>
								<td valign="top" class="text" style="font-weight:bold; color:red;">
									{if $data.offline_paysystem_regular_upgrade_send}
										<div style="padding-top:12px;">
											{if $data.offline_paysystem_regular_upgrade_send == 'atm_payment'}
												{$header.atm_payment_regular_upgrade_send}
											{elseif $data.offline_paysystem_regular_upgrade_send == 'wire_transfer'}
												{$header.wire_transfer_regular_upgrade_send}
											{elseif $data.offline_paysystem_regular_upgrade_send == 'bank_cheque'}
												{$header.bank_cheque_regular_upgrade_send}
											{/if}
										</div>
									{/if}
									{if $data.offline_paysystem_buy_days_send}
										<div style="padding-top:12px;">
											{if $data.offline_paysystem_buy_days_send == 'atm_payment'}
												{$header.atm_payment_buy_days_send}
											{elseif $data.offline_paysystem_buy_days_send == 'wire_transfer'}
												{$header.wire_transfer_buy_days_send}
											{elseif $data.offline_paysystem_buy_days_send == 'bank_cheque'}
												{$header.bank_cheque_buy_days_send}
											{/if}
										</div>
									{/if}
									{if $data.offline_paysystem_credit_points_send}
										<div style="padding-top:12px;">
											{if $data.offline_paysystem_credit_points_send == 'atm_payment'}
												{$header.atm_payment_credit_points_send}
											{elseif $data.offline_paysystem_credit_points_send == 'wire_transfer'}
												{$header.wire_transfer_credit_points_send}
											{elseif $data.offline_paysystem_credit_points_send == 'bank_cheque'}
												{$header.bank_cheque_credit_points_send}
											{/if}
										</div>
									{/if}
								</td>
							</tr>
						{/if}
					{/if}
				</table>
			</div>
			{* hide profile and delete profile *}
			<div class="content" style="padding-top:12px;">
				<div class="hdr2">{$lang.account.subheader_account_options}</div>
				<table cellpadding="0" cellspacing="0" border="0">
					{if !$auth.is_applicant && $data.use_hide_profile_feature}
						<tr>
							<td valign="top" class="text" style="padding-top:0px;">
								<a href="account.php?sel=visible_change">{$data.info_switch}</a>
							</td>
						</tr>
						<tr>
							<td valign="top" class="text" style="padding-top:5px;">{$data.info_switch_comment}</td>
						</tr>
					{/if}
					<tr>
						<td valign="top" class="text" style="padding-top:12px;">
							<a href="#" onclick="if (window.confirm('{$header.info_delete_confirm}')) document.location.href='account.php?sel=delete'; else return false;">{$header.info_delete}</a>
						</td>
					</tr>
					<tr>
						<td valign="top" class="text" style="padding-top:5px;">{$header.info_delete_text}</td>
					</tr>
				</table>
			</div>
		{/if}
		{if !$auth.is_applicant}
			{* email and sms alerts *}
			<div class="content" style="padding-top:12px;">
				<div class="hdr2">{$lang.account.subheader_myalerts}</div>
				<form name="alerts" action="account.php" method="post" style="margin:0px;">
					<input type="hidden" name="sel" value="alerts" />
					<table cellpadding="0" cellspacing="0">
						{section name=n loop=$my_alerts_email}
							<tr>
								<td class="text" style="padding-top:5px;">
									<input type="checkbox" id="my_alerts_email_{$smarty.section.n.index}" name="my_alerts_email[{$smarty.section.n.index}]" value="{$my_alerts_email[n].id}" {if $my_alerts_email[n].check}checked{/if}>
								</td>
								<td class="text" style="padding-top:5px;">
									&nbsp;<label for="my_alerts_email[{$smarty.section.n.index}]">{$my_alerts_email[n].name}</label>
								</td>
							</tr>
						{/section}
					</table>
					<p class="basic-btn_next">
						<span><input type="button" class="index_btn" onclick="document.alerts.submit();" value="{$button.gr_valider}" /></span><b></b>
					</p>
				</form>
			</div>
			{* subscriptions *}
			{if $adm_subscr}
				<div class="content" style="padding-top:12px;">
					<div class="hdr2">{$lang.account.subheader_subscribe}</div>
					<div>
						<form name="subscriber" action="account.php" method="post" style="margin:0px">
							<input type="hidden" name="sel" value="subscr" />
							<table cellpadding="0" cellspacing="0">
								{section name=n loop=$adm_subscr}
									<tr>
										<td class="text" style="padding-top:5px;">
											<input type="checkbox" name="a_subscr[{$smarty.section.n.index}]" value="{$adm_subscr[n].id}" {if $adm_subscr[n].check}checked{/if}>
										</td>
										<td class="text" style="padding-top:5px;">
											&nbsp;{$adm_subscr[n].name}
										</td>
									</tr>
								{/section}
							</table>
							<div style="padding-top:12px; padding-left:12px;">
								<input type="button" class="button" onclick="document.subscriber.submit();" value="{$button.gr_valider}">
							</div>
						</form>
					</div>
				</div>
			{/if}
			{* system alerts *}
			{if $alerts}
				<div class="content" style="padding-top:12px;">
					<h3 class="hdr2">{$lang.account.subheader_alerts}</h3>
					{foreach item=item from=$alerts}
						<div style="padding-bottom:5px; margin-bottom:5px; border-bottom:1px solid #DBDBDB">
							<div class="text">{$item.date}</div>
							<div class="text"">{$item.text}<br>({$item.type})</div>
						</div>
					{/foreach}
				</div>
			{/if}
		{/if}
		{* <!--
		{if $refer_comment}
			<br>
			<b>{$refer_comment}</b>
		{/if}
		--> *}
	</div>
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}