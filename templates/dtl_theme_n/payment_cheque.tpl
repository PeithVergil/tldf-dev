{strip}
<div class="block_blue">
	<div class="titlebg" style="margin-bottom:10px; margin-top:0px; padding:3px 5px;" onclick="payment_cheque_toggle();">
		<div class="top_showhide">
			<a href="#" onclick="return false;{* payment_cheque_toggle(); *}">{$lang.payment.click_to_show_hide}</a>
		</div>
		<div><label title="{$lang.payment.bank_cheque_payment_option_thai}" class="det-14">Option {$opt_no}: Bank Cheque</label></div>
	</div>
	<div id="bank_cheque_div" style="display:{if $paysystem == 'bank_cheque'}block{else}none{/if}; padding-bottom:10px;">
		<div style="padding-left:5px;">
			<p style="margin-bottom: 10px;">Make Bank Cheque Payable To:</p>
			<p style="margin-bottom: 10px;">Meet Me Now Bangkok Co. Ltd.</p>
			<p style="margin-bottom: 10px;">Post Cheque To:</p>
			<p style="margin-bottom: 10px;">Meet Me Now Bangkok Co., Ltd.<br />
33/7 Soi Pipat 2<br />
Convent Road, Silom<br />
Bangkok<br />
Thailand<br />
10500</p>
			<p class="basic-btn_here"><b>&nbsp;</b><span><input type="button" class="normal-btn" value="Print These Details" onclick="alert('Print Routine Pending');" /></span></p>
		</div>
		<table border="0" cellspacing="5" cellpadding="0">
			<tr>
				<td nowrap="nowrap"><input type="checkbox" name="cheque_cb_1" {if $data.cheque_cb_1}checked="checked"{/if} /></td>
				<td nowrap="nowrap" class="txtblack">1.</td>
				<td class="txtblack">Cheque was posted on:</td>
				<td>
					{html_select_date prefix="cheque_" time=$cheque_datetime start_year=-1 end_year=+1 month_value_format="%m" reverse_years=false month_empty="Month" day_empty="DD" year_empty="YYYY"}
					&nbsp;at&nbsp;{html_select_time prefix="cheque_" time=$cheque_datetime use_24_hours=true display_seconds=false}
				</td>
			</tr>
			<tr>
				<td nowrap="nowrap"><input type="checkbox" name="cheque_cb_2" {if $data.cheque_cb_2}checked="checked"{/if} /></td>
				<td nowrap="nowrap" class="txtblack">2.</td>
				<td class="txtblack">Amount of Cheque:</td>
				<td class="txtblack">
					<input type="text" name="cheque_payamount" value="{$data.cheque_payamount}" maxlength="10" />&nbsp;
					{if $data.chosen_forpay_2}{$data.account_currency_2}{else}{$data.account_currency}{/if}
				</td>
			</tr>
			<tr>
				<td nowrap="nowrap"><input type="checkbox" name="cheque_cb_3" {if $data.cheque_cb_3}checked="checked"{/if} /></td>
				<td nowrap="nowrap" class="txtblack">3.</td>
				<td class="txtblack">Originating Bank:</td>
				<td><input type="text" name="cheque_bank_name" value="{$data.cheque_bank_name}" maxlength="100" style="width:300px;" /></td>
			</tr>
		</table>
		<div style="float:left;">
			<p class="basic-btn_here">
				<b>&nbsp;</b><span>
				<input type="button" class="normal-btn" value="Submit Cheque Payment" onclick="payment_offline_submit('bank_cheque');" />
				</span>
			</p>
		</div>
		<div style="clear:left;"></div>
	</div>
</div>
<script type="text/javascript">
{literal}
function payment_cheque_toggle() {
	/* does not look good in all browser
	$('#online_payment_div').slideUp('slow', function() {
		$('#atm_payment_div').slideUp('slow', function() {
			$('#wire_transfer_div').slideUp('slow', function() {
				$('#bank_cheque_div').slideToggle('slow');
			});
		});
	});
	*/
	$('#online_payment_div').hide();
	$('#atm_payment_div').hide();
	$('#wire_transfer_div').hide();
	$('#bank_cheque_div').toggle();
	return false;
}
{/literal}
</script>
{/strip}