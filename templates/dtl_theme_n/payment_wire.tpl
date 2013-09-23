{strip}
<div class="block_blue">
	<div class="titlebg" style="margin-bottom:10px; margin-top:0px; padding:3px 5px;" onclick="payment_wire_toggle();">
		<div class="top_showhide">
			<a href="#" onclick="return false;{* payment_wire_toggle(); *}">{$lang.payment.click_to_show_hide}</a>
		</div>
		<div><label title="{$lang.payment.wire_transfer_payment_option_thai}" class="det-14">Option {$opt_no}: Wire Transfer</label></div>
	</div>
	<div class="clear"></div>
	<div id="wire_transfer_div" style="display:{if $paysystem == 'wire_transfer'}block{else}none{/if}; padding-bottom:10px;">
		<div style="padding-left:5px;">
			<p style="margin-bottom:10px;">Wire Transfer Funds To:</p>
			<p style="margin-bottom:10px;">Meet Me Now Bangkok Co. Ltd.<br />
			Account No. 608-2-04363-0<br />
			Kasikorn Bank<br />
			Thanon Narathiwat Rat Nakharin Sub Branch</p>
			{*<!--<p style="margin-bottom:10px;">100/72 Sathorn Tower<br />
			Silom Road<br />
			Bangrak<br />
			Bangkok<br />
			Thailand<br />
			10500</p>-->*}
			<p style="margin-bottom:10px;">Swift Address: KASITHBK<br />
			Telex No: 81159 FARMERS TH</p>
			<p class="basic-btn_here"><b>&nbsp;</b><span><input type="button" value="Print These Details" class="normal-btn" onclick="alert('Print Routine Pending');" /></span></p>
		</div>
		<table border="0" cellspacing="5" cellpadding="0">
			<tr>
				<td nowrap="nowrap"><input type="checkbox" name="wire_cb_1" {if $data.wire_cb_1}checked="checked"{/if} /></td>
				<td nowrap="nowrap" class="txtblack">1.</td>
				<td class="txtblack">Wire Transfer was made on:</td>
				<td>
					{html_select_date prefix="wire_" time=$wire_datetime start_year=-1 end_year=+1 month_value_format="%m" reverse_years=false month_empty="Month" day_empty="DD" year_empty="YYYY"}
					&nbsp;at&nbsp;{html_select_time prefix="wire_" time=$wire_datetime use_24_hours=true display_seconds=false}
				</td>
			</tr>
			<tr>
				<td nowrap="nowrap"><input type="checkbox" name="wire_cb_2" {if $data.wire_cb_2}checked="checked"{/if} /></td>
				<td nowrap="nowrap" class="txtblack">2.</td>
				<td class="txtblack">Amount transferred was:</td>
				<td class="txtblack">
					<input type="text" name="wire_payamount" value="{$data.wire_payamount}" maxlength="10" />&nbsp;
					{if $data.chosen_forpay_2}{$data.account_currency_2}{else}{$data.account_currency}{/if}
				</td>
			</tr>
			<tr>
				<td nowrap="nowrap"><input type="checkbox" name="wire_cb_3" {if $data.wire_cb_3}checked="checked"{/if} /></td>
				<td nowrap="nowrap" class="txtblack">3.</td>
				<td class="txtblack">Transfer No. of Original Bank:</td>
				<td><input type="text" name="wire_transfer_no" value="{$data.wire_transfer_no}" maxlength="50" /></td>
			</tr>
		</table>
		<div style="float:left;">
			<p class="basic-btn_here">
				<b>&nbsp;</b><span><input type="button" class="normal-btn" value="Submit Wire Transfer" onclick="payment_offline_submit('wire_transfer');" /></span>
			</p>
		</div>
		<div style="clear:left;"></div>
	</div>
</div>
<script type="text/javascript">
{literal}
function payment_wire_toggle() {
	/* slide does not look good in all browsers
	$('#online_payment_div').slideUp('slow', function() {
		$('#atm_payment_div').slideUp('slow', function() {
			$('#bank_cheque_div').slideUp('slow', function() {
				$('#wire_transfer_div').slideToggle('slow');
			});
		});
	});
	*/
	$('#online_payment_div').hide();
	$('#atm_payment_div').hide();
	$('#bank_cheque_div').hide();
	$('#wire_transfer_div').toggle();
	return false;
}
{/literal}
</script>
{/strip}