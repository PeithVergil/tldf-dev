{strip}
<div class="block_blue">
	<div class="titlebg" style="margin-bottom:10px; margin-top:0px; padding:3px 5px;" onclick="payment_atm_toggle();">
		<div class="top_showhide">
			<a href="#" onclick="return false;{* payment_atm_toggle(); *}">{$lang.payment.click_to_show_hide}</a>
		</div>
		<div><label title="{$lang.payment.atm_payment_option_thai}" class="det-14">Option {$opt_no}: ATM Payment</label></div>
	</div>
	<div class="clear"></div>
	<div id="atm_payment_div" style="display:{if $paysystem == 'atm_payment'}block{else}none{/if}; padding-bottom:10px;">
		<table border="0" cellspacing="5" cellpadding="0">
			<tr valign="top">
				<td nowrap="nowrap"><input type="checkbox" name="atm_cb_1" {if $data.atm_cb_1}checked="checked"{/if} /></td>
				<td nowrap="nowrap" class="txtblack" style="padding:0;">1.</td>
				<td colspan="2" class="txtblack" style="padding:0;">I have sent my Application Fee to Meet Me Now Bangkok Co. Ltd.<br/>Kasikorn Bank. Account Number 608-2-04363-0</td>
			</tr>
			<tr>
				<td nowrap="nowrap"><input type="checkbox" name="atm_cb_2" {if $data.atm_cb_2}checked="checked"{/if} /></td>
				<td nowrap="nowrap" class="txtblack">2.</td>
				<td nowrap="nowrap" width="1" class="txtblack">ATM Transfer was made on:</td>
				<td>
					{html_select_date prefix="atm_" time=$atm_datetime start_year=2009 end_year=2015 month_value_format="%m" reverse_years=false month_empty="Month" day_empty="DD" year_empty="YYYY"}
					&nbsp;at&nbsp;{html_select_time prefix="atm_" time=$atm_datetime use_24_hours=true display_seconds=false}
				</td>
			</tr>
			<tr>
				<td nowrap="nowrap"><input type="checkbox" name="atm_cb_3" {if $data.atm_cb_3}checked="checked"{/if} /></td>
				<td nowrap="nowrap" class="txtblack">3.</td>
				<td nowrap="nowrap" width="1" class="txtblack">Amount transferred was:</td>
				<td class="txtblack">
					<input type="text" name="atm_payamount" value="{$data.atm_payamount}" maxlength="10" />&nbsp;
					{if $data.chosen_forpay_2}{$data.account_currency_2}{else}{$data.account_currency}{/if}
				</td>
			</tr>
			<tr valign="top">
				<td nowrap="nowrap"><input type="checkbox" name="atm_cb_4" {if $data.atm_cb_4}checked="checked"{/if} /></td>
				<td nowrap="nowrap" class="txtblack" style="padding-top:0;">4.</td>
				<td colspan="2" class="txtblack" style="padding-top:0;">I have faxed the ATM slip to Meet Me Now Bangkok Co. Ltd on fax number 02-667 0069, with my name and email included and clearly written on the fax.</td>
			</tr>
		</table>
		<div style="float:left;">
			<p class="basic-btn_here">
				<b>&nbsp;</b><span>
				<input type="button" class="normal-btn" value="Submit ATM Payment" onclick="payment_offline_submit('atm_payment');" />
				</span>
			</p>
		</div>
		<div style="clear:left;"></div>
	</div>
</div>
<script type="text/javascript">
{literal}
function payment_atm_toggle() {
	/* does not look good in all browsers
	$('#online_payment_div').slideUp('slow', function() {
		$('#wire_transfer_div').slideUp('slow', function() {
			$('#bank_cheque_div').slideUp('slow', function() {
				$('#atm_payment_div').slideToggle('slow');
			});
		});
	});
	*/
	$('#online_payment_div').hide();
	$('#wire_transfer_div').hide();
	$('#bank_cheque_div').hide();
	$('#atm_payment_div').toggle();
	return false;
}
{/literal}
</script>
{/strip}