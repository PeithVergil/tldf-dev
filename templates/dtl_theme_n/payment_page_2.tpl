{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple">
	<div class="tcxf-ch-la">
		{*
			<!-- OLD CALLCHAT BUTTONS -->
			<div>
				<div>
					<div class="callchat_icons">
						<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/telephone.png" alt="Call Me" title="Call Me"></a>
						<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/Chat.png" alt="Chat With Me" title="Chat With Me"></a>
					</div>
				</div>
			</div>
		*}
		<div style="width:600px;">
			<div class="box_info">
				<div class="hdr2e">
					{if $pay_platinum}
						<label title="{$lang.payment.pay_platinum_fee}">
							{$lang.payment.pay_platinum_fee}
						</label>
					{else}
						<label title="{$lang.payment.pay_membership_fee_tool}">
							{$lang.payment.pay_membership_fee}
						</label>
					{/if}
				</div>
				<div class="box_inn">
					{*
						<!-- OLD PLATINUM INFO -->
						<div class="txtblack" style="padding-bottom:15px;">
							{if $pay_platinum}
								<label title="{$lang.payment.toptext_pay_platinum_thai}">
									{$lang.payment.toptext_pay_platinum}
								</label>
							{/if}
						</div>
					*}
					{if $form.err}
						<div class="error_msg" style="padding-bottom:15px;">{$form.err}</div>
					{/if}
					<div style="float:left;">
						<div class="box-frame2">
							<table border="0" cellspacing="0" cellpadding="3">
								<tr valign="top">
									<td class="txtblack">{$lang.payment.chosen_groups}:&nbsp;&nbsp;&nbsp;</td>
									<td class="txtblack"><b>
										{*
										{if $pay_platinum}
											{$lang.payment.platinum_product_name}
										{else}
										*}
											{$data.chosen_group}
										{*
											{if $auth.is_regular && $auth.platinum_applied}
												&nbsp;({$lang.users.platinum_applied})
											{/if}
										{/if}
										*}
									</b></td>
								</tr>
								{*
								{if $data.chosen_amount > 0}
								*}
									<tr valign="top">
										<td class="txtblack">{$lang.payment.chosen_period}:&nbsp;&nbsp;&nbsp;</td>
										<td class="txtblack" style="font-weight: bold;">
											{if $data.chosen_period_id == $smarty.const.MM_TRIAL_GUY_PERIOD_ID || $data.chosen_period_id == $smarty.const.MM_TRIAL_LADY_PERIOD_ID}
												Unlimited
											{elseif $data.chosen_period_id == $smarty.const.MM_PLATINUM_LADY_PERIOD_ID}
												Lifetime
											{else}
												{$data.chosen_amount}&nbsp;{$data.chosen_period}
												{if $data.gender == 1 && !$pay_platinum}
													{if $data.chosen_recurring}
														&nbsp;{$lang.payment.recurring}
													{else}
														&nbsp;{$lang.payment.non_recurring}
													{/if}
													&nbsp;{$lang.payment.membership}
												{/if}
											{/if}
										</td>
									</tr>
									{if !$auth.is_applicant && !$auth.is_trial && !$data.chosen_recurring && !$data.expired && !$pay_platinum}
										<tr valign="top">
											<td class="txtblack">{$lang.payment.current_expiry_date}:&nbsp;&nbsp;&nbsp;</td>
											<td class="txtblack"><b>{$data.expiry_date}</b></td>
										</tr>
									{/if}
									{if !$auth.is_applicant && !$data.chosen_recurring && !$pay_platinum}
										<tr valign="top">
											<td class="txtblack">{$lang.payment.new_expiry_date}:&nbsp;&nbsp;&nbsp;</td>
											<td class="txtblack"><b>{$data.new_expiry_date}</b></td>
										</tr>
									{/if}
								{*
								{/if}
								*}
								<tr valign="top">
									<td class="txtblack">{$lang.payment.chosen_cost}:&nbsp;&nbsp;&nbsp;</td>
									<td class="txtblack">
										{if $data.chosen_cost_2}
											<b>{$data.chosen_cost_2_formatted}&nbsp;{$data.account_currency_2}</b>
										{else}
											<b>{$data.chosen_cost_formatted}&nbsp;{$data.account_currency}</b>
										{/if}
									</td>
								</tr>
								{if $form.use_credits_for_membership_payment}
									<tr valign="top">
										<td class="txtblack">{$lang.payment.account_count}:&nbsp;&nbsp;&nbsp;</td>
										<td class="txtblack" align="right">
											{if $data.chosen_cost_2}
												<b>{$data.count_2_formatted}&nbsp;{$data.account_currency_2}</b>
											{else}
												<b>{$data.count_formatted}&nbsp;{$data.account_currency}</b>
											{/if}
										</td>
									</tr>
									<tr valign="top">
										<td class="txtblack">{$lang.payment.chosen_forpay}:&nbsp;</td>
										<td class="txtblack" align="right">
											{if $data.chosen_cost_2}
												<b>{$data.chosen_forpay_2_formatted}&nbsp;{$data.account_currency_2}</b>
											{else}
												<b>{$data.chosen_forpay_formatted}&nbsp;{$data.account_currency}</b>
											{/if}
										</td>
									</tr>
								{/if}
							</table>
						</div>
					</div>
					{*
						<!-- OLD BACK LINKS -->
						{if !$pay_platinum}
							<div style="float:right;">
								<img src="{$site_root}{$template_root}/images/btn_back.gif" alt="back" />
								<span style="position:relative; top:-4px;">&nbsp;
								{if $auth.is_applicant || $auth.is_regular}
									<a href="payment.php">{$lang.payment.back_to_group_options}</a>
								{else}
									<a href="payment.php?sel=group&amp;group={$data.chosen_group_id}">{$lang.payment.back_to_group_selection}</a>
								{/if}
								</span>
								<br>
								<img src="{$site_root}{$template_root}/images/btn_back.gif" alt="back" />
								<span style="position:relative; top:-4px;">
								&nbsp;
								{if $auth.is_applicant}
									<a href="myprofile.php">{$lang.back_to_my_application_page}</a>
								{else}
									<a href="account.php">{$lang.account.back_to_my_account_page}</a>
								{/if}
								</span>
							</div>
						{/if}
					*}
					<br />
					<br />
					<div style="clear:both;"></div>
					{if $form.use_credits_for_membership_payment && $data.chosen_forpay == 0 && $data.chosen_recurring != '1'}
						<!-- CREDITS PAYMENT -->
						<div class="space"></div>
						<div class="txtblack">
							<div class="title" align="center">User Account Payment</div>
							<div class="center">
								<div class="btnwrap" style="width:180px;">
									<span><span>
										<input type="button" class="btn_org" style="width:160px;" onclick="document.add_pay.paysys.value='user_account'; document.add_pay.submit();" value="{$lang.account.only_change}" />
									</span></span>
								</div>
							</div>
						</div>
					{/if}
					{* DEBUG:{$data.chosen_cost_2}|{$data.chosen_forpay_2}|{$data.chosen_forpay} *}
					<form name="add_pay" action="payment.php" method="post" class="tldfpay">
						<input type="hidden" name="sel" value="">
						<input type="hidden" name="paysys" value="">
						<input type="hidden" name="group_id" value="{$data.chosen_group_id}">
						<input type="hidden" name="period_id" value="{$data.chosen_period_id}">
						<input type="hidden" name="forpay" value="{if $data.chosen_cost_2}{$data.chosen_forpay_2}{else}{$data.chosen_forpay}{/if}">
						<!-- ONLINE PAYMENT OPTIONS -->
						<div class="space"></div>
						{include file="$gentemplates/payment_online.tpl"}
						<!-- OFFLINE PAYMENT OPTIONS (ONLY FOR NON-RECURRING) -->
						{if $data.chosen_recurring != '1'}
							{assign var="opt_no" value="2"}
							<!-- ATM PAYMENT (LADIES ONLY) -->
							{if $data.gender == GENDER_FEMALE}
								<div class="space"></div>
								{include file="$gentemplates/payment_atm.tpl"}
								{math assign="opt_no" equation="$opt_no+1"}
							{/if}
							<!-- WIRE TRANSFER -->
							<div class="space"></div>
							{include file="$gentemplates/payment_wire.tpl"}
							{math assign="opt_no" equation="$opt_no+1"}
							<!-- BANK CHEQUE -->
							<div class="space"></div>
							{include file="$gentemplates/payment_cheque.tpl"}
						{/if}
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
{literal}
function payment_online_submit(paysys) {
	f = document.add_pay;
	f.sel.value = "save_2";
	f.paysys.value = paysys;
	f.submit();
}
function payment_offline_submit(paysys) {
	f = document.add_pay;
	f.sel.value = paysys;
	f.paysys.value = paysys;
	f.submit();
}
{/literal}
</script>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}