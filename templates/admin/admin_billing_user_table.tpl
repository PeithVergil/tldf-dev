{include file="$admingentemplates/admin_top.tpl"}
<font class="red_header">{$header.razdel_name}</font><font class="red_sub_header">&nbsp;|&nbsp;{$header.list_user}</font><br><br>
<table width="100%">
	<tr class="main_content_text">
		<td>
			&nbsp;<b>{$header.name}</b>:&nbsp;{$data.name}<br>
			<br>
			&nbsp;<b>{$header.account_status}</b>:&nbsp;<b><font color="red">{$data.account}</font>&nbsp;{$form.costunits}</b><br>
			<br>&nbsp;<b>{$header.days_remain}:</b>&nbsp;<b><font color="blue">{$days_remain}</font>&nbsp;{$header.periods_plural.day}</b><br>
			<br>
			{*
				<table>
					<tr>
						<td><b>{$header.membership_payments}:</b></td>
						<td align="right"><b><font color="red">{$data.membership_payments}</font>&nbsp;{$form.costunits}</b></td>
					</tr>
					<tr>
						<td><b>- by user:</b></td>
						<td align="right"><b><font color="red">{$data.membership_payments_user}</font>&nbsp;{$form.costunits}</b></td>
					</tr>
					<tr>
						<td><b>- by admin:</b></td>
						<td align="right"><b><font color="red">{$data.membership_payments_admin}</font>&nbsp;{$form.costunits}</b></td>
					</tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr>
						<td><b>Account Chargings:</b></td>
						<td align="right"><b><font color="red">{$data.account_payments}</font>&nbsp;{$form.costunits}</b></td>
					</tr>
					<tr>
						<td><b>- by user:</b></td>
						<td align="right"><b><font color="red">{$data.account_payments_user}</font>&nbsp;{$form.costunits}</b></td>
					</tr>
					<tr>
						<td><b>- by admin:</b></td>
						<td align="right"><b><font color="red">{$data.account_payments_admin}</font>&nbsp;{$form.costunits}</b></td>
					</tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr>
						<td><b>{$header.total_amount}:</b></td>
						<td align="right"><b><font color="red">{$data.total_payments_user}</font>&nbsp;{$form.costunits}</b></td>
					</tr>
					<tr>
						<td><b>Total amount paid by admin:</b></td>
						<td align="right"><b><font color="red">{$data.total_payments_admin}</font>&nbsp;{$form.costunits}</b></td>
					</tr>
				</table>
			*}
			<table border="1" cellpadding="3" cellspacing="0" style="border-collapse: collapse;">
				<tr>
					<td></td>
					<td align="right" width="140"><b>{$header.membership_payments}</b></td>
					<td align="right" width="140"><b>Account Chargings</b></td>
					<td align="right" width="140"><b>Ecards</b></td>
					<td align="right" width="140"><b>My Store</b></td>
					<td align="right" width="140"><b>Total</b></td>
				</tr>
				<tr>
					<td><b>user</b></td>
					<td align="right">
						<b><font color="red">{$data.membership_payments_user}</font>&nbsp;{$form.costunits}</b><br>
						<b><font color="red">{$data.membership_payments_2_user}</font>&nbsp;{$form.costunits_2}</b>
					</td>
					<td align="right">
						<b><font color="red">{$data.account_payments_user}</font>&nbsp;{$form.costunits}</b><br>
						<b><font color="red">{$data.account_payments_2_user}</font>&nbsp;{$form.costunits_2}</b>
					</td>
					<td align="right">
						<b><font color="red">{$data.ecards_payments_user}</font>&nbsp;{$form.costunits}</b><br>
						<b><font color="red">{$data.ecards_payments_2_user}</font>&nbsp;{$form.costunits_2}</b>
					</td>
					<td align="right">
						<b><font color="red">{$data.store_payments_user}</font>&nbsp;{$form.costunits}</b><br>
						<b><font color="red">{$data.store_payments_2_user}</font>&nbsp;{$form.costunits_2}</b>
					</td>
					<td align="right">
						<b><font color="red">{$data.total_payments_user}</font>&nbsp;{$form.costunits}</b><br>
						<b><font color="red">{$data.total_payments_2_user}</font>&nbsp;{$form.costunits_2}</b>
					</td>
				</tr>
				<tr>
					<td><b>admin</b></td>
					<td align="right">
						<b><font color="red">{$data.membership_payments_admin}</font>&nbsp;{$form.costunits}</b><br>
						<b><font color="red">{$data.membership_payments_2_admin}</font>&nbsp;{$form.costunits_2}</b>
					</td>
					<td align="right">
						<b><font color="red">{$data.account_payments_admin}</font>&nbsp;{$form.costunits}</b><br>
						<b><font color="red">{$data.account_payments_2_admin}</font>&nbsp;{$form.costunits_2}</b>
					</td>
					<td align="right">
						<b><font color="red">{$data.ecards_payments_admin}</font>&nbsp;{$form.costunits}</b><br>
						<b><font color="red">{$data.ecards_payments_2_admin}</font>&nbsp;{$form.costunits_2}</b>
					</td>
					<td align="right">
						<b><font color="red">{$data.store_payments_admin}</font>&nbsp;{$form.costunits}</b><br>
						<b><font color="red">{$data.store_payments_2_admin}</font>&nbsp;{$form.costunits_2}</b>
					</td>
					<td align="right">
						<b><font color="red">{$data.total_payments_admin}</font>&nbsp;{$form.costunits}</b><br>
						<b><font color="red">{$data.total_payments_2_admin}</font>&nbsp;{$form.costunits_2}</b>
					</td>
				</tr>
				<tr>
					<td><b>Total</b></td>
					<td align="right">
						<b><font color="red">{$data.total_membership_payments}</font>&nbsp;{$form.costunits}</b><br>
						<b><font color="red">{$data.total_membership_payments_2}</font>&nbsp;{$form.costunits_2}</b>
					</td>
					<td align="right">
						<b><font color="red">{$data.total_account_payments}</font>&nbsp;{$form.costunits}</b><br>
						<b><font color="red">{$data.total_account_payments_2}</font>&nbsp;{$form.costunits_2}</b>
					</td>
					<td align="right">
						<b><font color="red">{$data.total_ecards_payments}</font>&nbsp;{$form.costunits}</b><br>
						<b><font color="red">{$data.total_ecards_payments_2}</font>&nbsp;{$form.costunits_2}</b>
					</td>
					<td align="right">
						<b><font color="red">{$data.total_store_payments}</font>&nbsp;{$form.costunits}</b><br>
						<b><font color="red">{$data.total_store_payments_2}</font>&nbsp;{$form.costunits_2}</b>
					</td>
					<td align="right">
						<b><font color="red">{$data.total_payments}</font>&nbsp;{$form.costunits}</b><br>
						<b><font color="red">{$data.total_payments_2}</font>&nbsp;{$form.costunits_2}</b>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<br>
<table border="0" class="table_main" cellspacing="1" cellpadding="5" width="100%">
	{if $links}
		<tr bgcolor="#ffffff">
			<td height="20" colspan="8" align="left" class="main_content_text">{$links}</td>
		</tr>
	{/if}
	<tr class="table_header">
		<td class="main_header_text" align="center" width="10">{$header.number}</td>
		<td class="main_header_text" align="center" width="10">{$header.id}</td>
		<td class="main_header_text" align="center" colspan="2">{$header.entry}</td>
		<td class="main_header_text" align="center">{$header.payment_type}</td>
		<td class="main_header_text" align="center">{$header.date_entry}</td>
		<td class="main_header_text" align="center">{$header.type}</td>
		<td class="main_header_text" align="center">&nbsp;</td>
	</tr>
	{if $entry}
		{foreach item=item from=$entry name=entry}
			<tr bgcolor="#ffffff">
				<td class="main_content_text" align="center">{$smarty.foreach.entry.iteration}</td>
				<td class="main_content_text" align="center">{$item.id}</td>
				<td class="main_content_text" align="right" width="75">
					{if $item.currency == $form.costunits}{$item.billing_amount}&nbsp;{$item.currency}{else}&nbsp;{/if}
				</td>
				<td class="main_content_text" align="right" width="75">
					{if $item.currency == $form.costunits_2}{$item.billing_amount}&nbsp;{$item.currency}{else}&nbsp;{/if}
				</td>
				<td class="main_content_text" align="center">{$item.pay_type}</td>
				<td class="main_content_text" align="center">{$item.date_entry}</td>
				<td class="main_content_text" align="center">{if $item.type == "refer_friend"}{$lang.refer_friend.referred}&nbsp;<a href="{$item.invited_profile_link}">{$item.invited_login}</a>{else}{$item.type}{/if}</td>
				<td class="main_content_text" align="center">
					{if $item.del_entry_link}<a href="javascript:DeleteBillingEntry('{$item.del_entry_link}');">delete</a>{/if}
				</td>
			</tr>
		{/foreach}
	{else}
		<tr bgcolor="#ffffff">
			<td colspan="8" height="40" align="left" class="main_error_text">{$header.empty_user}</td>
		</tr>
	{/if}
	{if $links}
		<tr bgcolor="#ffffff">
			<td colspan="8" height="20" align="left" class="main_content_text">{$links}</td>
		</tr>
	{/if}
</table>
<br>
<table>
<tr>
<td>
	<form method="post" action="admin_pays.php" style="margin: 0; padding: 0;" >
	{$form.hiddens}
	<input type="hidden" name="add_type" value="membership">
	<table cellSpacing="0" cellPadding="3" border="0">
		<tr>
			<td class="main_content_text" width="135" align="right">
				{$header.record_payment_for}:&nbsp;
			</td>
			<td width="220">
				<select name="period">
					{foreach item=item from=$periods}
						<option value="{$item.id}">{$item.name}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td class="main_content_text" width="135" align="right">
				{$header.discount}:&nbsp;
			</td>
			<td>
				<input type="text" name="discount" id="discount">
			</td>
		</tr>
		<tr>
			<td class="main_content_text" width="135" align="right">
				&nbsp;
			</td>
			<td>
				<input type="submit" value="{$header.record_payment}" class="button">
			</td>
		</tr>
	</table>
</form>
<br>
<form method="post" action="admin_pays.php" style="margin: 0; padding: 0;" id="adminUserMoveForm">
	{$form.hiddens}
	<input type="hidden" name="add_type" value="move_user">
	<table>
		<tr>
			<td class="main_content_text" width="135" align="right">
				{$header.add_to_period}:&nbsp;
			</td>
			<td width="220">
				<select name="period">
					{foreach item=item from=$periods}
						<option value="{$item.id}">{$item.name}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td class="main_content_text" width="135" align="right">
				{$header.comment}:&nbsp;
			</td>
			<td>
				<textarea name="comment" id="comment"></textarea>
			</td>
		</tr>
		<tr>
			<td class="main_content_text" width="135" align="right">
				{$header.staff}:&nbsp;
			</td>
			<td>
				<input type="text" name="staff" id="staff">
			</td>
		</tr>
		<tr>
			<td class="main_content_text" width="135" align="right">
				{$header.add_days}:&nbsp;
			</td>
			<td>
				<select name="add_days_move_user" id="add_days_move_user">
					<option value=''>--Select Add Days--</option>
					<option value='yes'>Yes</option>
					<option value='no'>No</option>
				</select>
			</td>
		</tr>
		{*RESET DATE*}
		<tr class="resetDate" style="display:none">
			<td class="resetDate" width="135" align="right">
				{$header.reset_date}: &nbsp;
			</td>
			<td>
				<select name="reset_date" id="reset_date" onchange="resetDateOption(this.value);">
					<option value="">--Select Reset Date--</option>
					<option value="yes">Yes</option>
					<option value="no">No</option>
				</select>
			</td>
		</tr>
		<tr id="reset_date_option" style="display: none;">
			<td align="left"></td>
			<td width="135" align="left">
				 &nbsp; {$header.date}:
				<input type="text" id="datep" name="newDate">
			</td>
			<td class="radioValidate"></td>
		</tr>
		{*end of RESET DATE*}
		<tr>
			<td class="main_content_text" width="135" align="right">
				&nbsp;
			</td>
			<td>
				<input type="submit" value="{$button.move_user}" class="button">
			</td>
		</tr>
	</table>
</form>
<form method="post" action="admin_pays.php" style="margin:0; padding:0;">
	{$form.hiddens}
	<input type="hidden" name="add_type" value="add_days">
	<table cellspacing="0" cellpadding="3" border="0">
		<tr>
			<td class="main_content_text" width="135" align="right">
				{$header.add_days}:&nbsp;
			</td>
			<td width="220">
				<input type="text" name="days_to_add" id="days_to_add" style="width:50px;">
			</td>
			<td class="main_content_text">
				<input type="submit" value="{$button.add_days}" class="button">
			</td>
		</tr>
	</table>
</form>
<br>
<form method="post" action="admin_pays.php" style="margin: 0; padding: 0;">
	{$form.hiddens}
	<input type="hidden" name="add_type" value="account">
	<table cellspacing="0" cellpadding="3" border="0">
		<tr>
			<td class="main_content_text" width="135" align="right">
				{$header.add_to_user_account}:&nbsp;
			</td>
			<td width="220">
				<input type="text" name="account_to_add" id="account_to_add" style="width: 150px;">
			</td>
			<td class="main_content_text">
				<input type="submit" value="{$button.add_to_account}" class="button">
			</td>
		</tr>
		<tr>
			<td></td>
			<td colspan="2">
				<i>{$header.update_account_page.account_to_add_hint}</i>
			</td>
		</tr>
	</table>
</form>
<br>
<form method="post" action="admin_pays.php" style="margin: 0; padding: 0;">
	{$form.hiddens}
	<input type="hidden" name="add_type" value="del_credit">
	<table cellspacing="0" cellpadding="3" border="0">
		<tr>
			<td class="main_content_text" width="135" align="right">
				{$header.delete_from_user_account}:&nbsp;
			</td>
			<td width="220">
				<input type="text" name="account_to_delete" id="account_to_delete" style="width: 150px;">
			</td>
			<td class="main_content_text">
				<input type="submit" value="{$button.delete_from_account}" class="button">
			</td>
		</tr>
		<tr>
			<td></td>
			<td colspan="2">
				<i>{$header.update_account_page.account_to_add_hint}</i>
			</td>
		</tr>
	</table>
</form>
<input type="button" value="{$header.back}" class="button" onclick="javascript: location.href='{$form.back_link}'">
</td>
<td>
	<div id="note" style="border:1px solid blue; background-color:snow; padding:6px;">{$header.reset_note}</div>
</td>
</tr>
</table>

<script type="text/javascript">
{literal}
formData = {"staff" : 1, "comment" : 1,"add_days_move_user" : 1};
exception ='';

$('#datep').datepicker({
	dateFormat : "yy-mm-dd"	
});

$('#add_days_move_user').live('change',function(){
	exception = $(this).val();
	
	if (exception == 'no') {
		$('.resetDate').show();
		$('#reset_date').show();
		$('#reset_date_option').hide();
		formData.reset_date = 1;
		formData.datep = 1;
	} else {
		formData.reset_date = 0;
		formData.datep = 0;
		$('#reset_date_option').val('');
		$('.resetDate').hide();
		$('#reset_date').hide();
		$('#reset_date_option').hide();
		$('#datep').val('');
		//$('#datep').hide();
	}
});

function resetDateOption(option) {
	if (option == 'yes') {
		$('#reset_date_option').show();
		$('#datep').val('');
		//formData.reset_date_option = 1; 
	} else if (option == 'no') {
		formData.reset_date_option = 0; 
		$('#reset_date_option').hide();
	} else {
		formData.reset_date_option = 0; 
		$('#reset_date_option').hide();
	}
}

function DeleteBillingEntry(delUrl)
{
	if(confirm("{/literal}{$lang.confirm.delete_billing_entry}{literal}"))
	{
		//alert(delUrl);
		location.href = delUrl;
	}
}


$(function(){
	$("#adminUserMoveForm").submit(function(e){
		console.log(formData);
		$(".validationError").remove();
		var empties = 0;
		
		$.each(formData, function (k,v) {
			if (v == 1) {
				if ($("#" + k).val() == '') {
					$("#" + k).after("<span style='color:saddlebrown;padding-left:10px;' class='validationError'>This Field is Required.</span>");
					empties++
				}
			}
		});
		
		if (empties > 0) {
			e.preventDefault();
		}
	});
});	
{/literal}
</script>
{include file="$admingentemplates/admin_bottom.tpl"}