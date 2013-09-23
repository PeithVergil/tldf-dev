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
					{$lang.payment.update_account_page.add_credit_points}
				</div>
				<div class="box_inn">
					{if $form.err}
						<div class="error_msg" style="padding-bottom:15px;">{$form.err}</div>
					{/if}
					<div class="credit-package">
						<div class="box-frame2" align="center">
							<p class="head"><b>{$data.pack_name}</b></p>
							<p>
								{$data.pack_points} {$lang.payment.guy.points} - {$data.pack_off}% {$lang.payment.guy.off}<br />
								<b>${$data.pack_cost}</b><br />
								{$lang.payment.guy.save} ${$data.pack_save}
							</p>
						</div>
					</div>
					<br />
					<br />
					<div style="clear:both;"></div>
					<form name="add_pay" action="payment.php" method="post" class="tldfpay">
						<input type="hidden" name="sel" value="">
						<input type="hidden" name="paysys" value="">
						<input type="hidden" name="pack_id" value="{$data.pack_id}">
						<input type="hidden" name="forpay" value="{$data.pack_cost}">
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
	f.sel.value = "save_credit_pack";
	f.paysys.value = paysys;
	f.submit();
}
function payment_offline_submit(paysys) {
	f = document.add_pay;
	f.sel.value = "pack_" + paysys;
	f.paysys.value = paysys;
	f.submit();
}
{/literal}
</script>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}