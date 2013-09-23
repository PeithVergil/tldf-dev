{strip}
<div class="block_blue">
	<div class="titlebg" style="margin-bottom:10px; margin-top:0px; padding:3px 5px;" onclick="payment_online_toggle();">
		<div class="top_showhide">
			<a href="#" onclick="return false;{* payment_online_toggle(); *}">{$lang.payment.click_to_show_hide}</a>
		</div>
		<div><label title="{$lang.payment.online_payment_option_thai}" class="det-14">{$lang.payment.online_payment_option}</label></div>
	</div>
	<div class="clear"></div>
	<div id="online_payment_div" style="display:{if $paysystem == 'online_payment'}block{else}none{/if}; padding-bottom:10px;">
		{foreach item=item from=$paysys}
			<div class="paypal_button">
				{if $item.template_name == 'paypal'}
					<img src="{$site_root}{$template_root}/images/btn_paypal.gif" class="pointer" onclick="payment_online_submit('{$item.template_name}'); {* document.add_pay.paysys.value='{$item.template_name}'; document.add_pay.submit(); *}" alt="{$item.name}" />
				{else}
					<p class="basic-btn_here"><b></b><span><input type="button" class="normal-btn" onclick="document.add_pay.paysys.value='{$item.template_name}'; document.add_pay.submit();" /></span></p>
				{/if}
			</div>
		{/foreach}
	</div>
</div>
<script type="text/javascript">
{literal}
function payment_online_toggle() {
	/* slide does not look good in all browsers
	$('#atm_payment_div').slideUp('slow', function() {
		$('#wire_transfer_div').slideUp('slow', function() {
			$('#bank_cheque_div').slideUp('slow', function() {
				$('#online_payment_div').slideToggle('slow');
			});
		});
	});
	*/
	$('#atm_payment_div').hide();
	$('#wire_transfer_div').hide();
	$('#bank_cheque_div').hide();
	$('#online_payment_div').toggle();
	return false;
}
{/literal}
</script>
{/strip}