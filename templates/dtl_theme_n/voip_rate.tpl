{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
	<!-- begin main cell -->
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr valign="middle">
    	<td>
			<div class="header">{$lang.section.voip_call} {$lang.voip.rate_page}</div>
			{if $error}
            	<div class="sep"></div>
                <div class="error_msg">{$error}</div>
            {/if}
        </td>
    </tr>
	<tr><td class="text" style="padding-top:5px">
	{if $from_number}
	<div class="content">
		<table class="user_vert_table" cellpadding="0" cellspacing="0">
		<tr>
			<td rowspan="8" valign="top" align="center">
				<div><a href="{$user_info.profile_link}"><img src="{$user_info.icon_path}" alt="{$user_info.login}" border="0" class="icon" /></a></div>
				<div><a href="{$user_info.profile_link}">{$user_info.login}</a></div>
				<div>{$user_info.age} {$lang.home_page.ans}</div>
			</td>
		</tr>
		<tr>
			<th>{$lang.voip.from_number}:</th>
			<td>{$from_number}</td>
		</tr>
		<tr>
			<th>{$lang.voip.calling_to}:</th>
			<td>{$rates.touser_name}{if $add_site_funds}&nbsp;&nbsp;* {$add_site_funds}{/if}</td>
		</tr>
		<tr>
			<th>{$lang.voip.calling_cost}:</th>
			<td>{if $rates.rate.rate}{$rates.rate.rate} {$rates.rate.currencyName}{else}<font class="error">* {$rate_err}</font>{/if}</td>
		</tr>
		<tr>
			<th>{$lang.voip.site_balance}:</th>
			<td>{$rates.site_balance.balance} {$rates.site_balance.currency_name}</td>
		</tr>
		{if !$add_site_funds}
		<tr>
			<th>{$lang.voip.add_call_credits}:</th>
			<td id="add_form">
				<form name="add_form" action="{$file_name}" method="post" enctype="multipart/form-data" style="margin:0px;">
					{$add_form.hiddens}
					<input type="text" name="funds" value="" size="4" /> {$rates.site_balance.currency_name}
					<input type="button" value="{$lang.button.add}" onclick="javascript: checkFunds(document.add_form.funds);" />
				</form>
			</td>
		</tr>
		{/if}
		<tr>
			<th>{$lang.voip.call_credits}:</th>
			<td>{$rates.balance.balance} {$rates.balance.currency_name} * {$lang.voip.call_credit_update_notice}</td>
		</tr>
		{*<tr>
			<th>{$lang.voip.call_rate}:</th>
			<td>{if $rate_err}{$rate_err}{else}{$rates.rate.rate} {$rates.rate.currencyName}{/if}</td>
		</tr>*}
		{if $rates.balance.balance > 0 && $rates.rate}
		<tr>
			<th></th>
			<td style="padding-bottom:3px; font-weight:bold; font-size:12px;" >
				<div id="call_status"><input type="button" name="call" value="{$lang.voip.call_button}" onclick="javascript: call({$to_id_user});" /></div>
				<input type="hidden" name="call_id" id="rc_id" value="0" />
				<input type="hidden" name="call_status_code" id="call_st_code" value="0" />
			</td>
		</tr>
		{/if}
		</table>
	</div>
	{/if}
	</td></tr>
	</table>
	<!-- end main cell -->
</td>
<script type="text/javascript">
{if $rates.site_balance.balance > 0}

max_funds = parseFloat({$rates.site_balance.balance});
{literal}
function checkFunds(funds_obj){
	funds = parseFloat(funds_obj.value);
	if (funds > max_funds){
		alert("{/literal}{$lang.voip.err.too_big_funds}{literal}");
		return false;
	}
	if (funds <= 0 || isNaN(funds)){
		alert("{/literal}{$lang.voip.err.zero_funds}{literal}");
		return false;
	}
	document.add_form.submit();
}
{/literal}
{/if}

{if $rates.balance.balance > 0}
path_to_image = '{$site_root}{$template_root}/images/';
file_name = '{$file_name}';
check_call_status_interval = 2000; //ms
{literal}
function call(to_user){
	destination_odj = document.getElementById('call_status');
	add_form_obj = document.getElementById('add_form');
	refresh_button_obj = document.getElementById('refresh_button');

	add_form_obj.style.display = '';

	str="sel=call&to_user="+to_user;
	old_dest = '<img src="'+path_to_image+'loading.gif" border="0"/>';
	asinch = false;
	ajaxRequest(file_name, str, destination_odj, old_dest, asinch);
	checkCallStatus();
	int=self.setInterval('checkCallStatus();',check_call_status_interval);
}

function checkCallStatus(){
	destination_odj = document.getElementById('call_status');
	call_status1 = document.getElementById('call_st_code').value;
	if (call_status1 == '-1') int=window.clearInterval(int);
	rc_id1 = document.getElementById('rc_id').value;
	if (rc_id1){
		str = 'sel=get_status&rs_id='+rc_id1;
		asinch = false;
		runJS = true;
		ajaxRequest(file_name, str, destination_odj, destination_odj.innerHTML, asinch);
	}
}
{/literal}
{/if}
</script>
{include file="$gentemplates/index_bottom.tpl"}