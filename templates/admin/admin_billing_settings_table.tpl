{include file="$admingentemplates/admin_top.tpl"}
<font class="red_header">{$header.razdel_name}</font><font class="red_sub_header">&nbsp;|&nbsp;{$header.list_settings}</font>
<div class="help_text">
<span class="help_title">{$lang.help}:</span>{if $settype == "general"}{$help.billing_settings_general}{else}{$help.billing_settings_paysystem}{/if}
</div>
<form name="money_form" action="admin_pays.php" method="post" {if $settype == "general"}onsubmit="javascript: return checkMoneyForm();"{/if}>
	<input type="hidden" name="sel" value="saveset">
	<input type="hidden" name="settype" value="{$settype}">
	<table border="0" cellspacing="1" cellpadding="5" width="100%">
		{if $settype == 'general'}
			<tr bgcolor="#ffffff">
				<td colspan="3" height="30" align="left" class="main_content_text">
					&nbsp;<b>{$header.settings}:</b>&nbsp;&nbsp;&nbsp;
					{section name=s loop=$paysystems}
						<a href="admin_pays.php?sel=settings&amp;settype={$paysystems[s].value}">{$paysystems[s].name_orig}</a>&nbsp;&nbsp;&nbsp;
					{/section}
				</td>
			</tr>
			<tr bgcolor="#ffffff">
				<td colspan="3" height="30" align="left" class="main_content_text">
					&nbsp;<b>{$header.services}:</b>&nbsp;&nbsp;&nbsp;
					<a href="admin_pays.php?sel=service&amp;par=list&amp;service=lift_up">{$header.service.lift_up}</a>&nbsp;&nbsp;&nbsp;
				</td>
			</tr>
			<tr bgcolor="#ffffff">
				<td align="right" width="15%" class="main_header_text">{$header.currency} 1:&nbsp;</td>
				<td class="main_content_text" align="left">
					<select name="currency" onchange="javascript: getCurseForm(this.value);">
					{section name=s loop=$currency}
						<option value="{$currency[s].value}"{if $currency[s].value == $data.site_unit_costunit} selected="selected"{/if}>{$currency[s].value}</option>
					{/section}
					</select>
				</td>
				<td class="main_header_text" align="left" width="70%">&nbsp;</td>
			</tr>
			<tr bgcolor="#ffffff">
				<td align="right" width="15%" class="main_header_text">{$header.currency} 2:&nbsp;</td>
				<td class="main_content_text" align="left">
					<select name="currency_2">
					<option value="">---</option>
					{section name=s loop=$currency}
						<option value="{$currency[s].value}"{if $currency[s].value == $data.site_unit_costunit_2} selected="selected"{/if}>{$currency[s].value}</option>
					{/section}
					</select>
				</td>
				<td class="main_header_text" align="left" width="70%">&nbsp;</td>
			</tr>
		</table>
		<div id="exch_table" style="display:none;">
			<table border="0" cellspacing="1" cellpadding="5" width="100%">
				<tr>
					<td align="right" width="15%">{$lang.pays.exchange_rate} 1 {$data.site_unit_costunit} =</td>
					<td><input id="dct" type="text" name="curr_rate" value="1" style="width: 100px;" onKeyup="javascript: changeByCurr(1, this.value);">&nbsp;&nbsp;<span id="new_curr"></span></td>
				</tr>
				<tr>
					<td align="right" width="15%">{$lang.pays.or} 1 <span id="new_curr2"></span> =</td>
					<td><input id="inv" type="text" name="curr_rate2" value="1" style="width: 100px;" onKeyup="javascript: changeByCurr(2, this.value);">&nbsp;&nbsp;{$data.site_unit_costunit}</td>
				</tr>
			</table>
		</div>
		<table border="0" cellspacing="1" cellpadding="5" width="100%">
			<tr>
				<td width="15%"></td>
				<td><input type="submit" value="{$button.save}" class="button"></td>
			</tr>
		{else}
			{$data.table_options}
		{/if}
		<tr bgcolor="#ffffff">
			<td colspan="2" height="10" align="right">
				{if $settype != "general"}
					<input type="submit" value="{$button.save}" class="button"> &nbsp;
					<input type="button" value="{$button.back}" class="button" onclick="location.href='admin_pays.php?sel=settings';">
				{/if}
{* 				<input id="money_form_butt_save" type="submit" value="{$button.save}" class="button" {if $settype == "general"}style="display:none;"{/if}> *}
			</td>
			<td align="left"></td>
		</tr>
	</table>
</form>
{if $settype == 'ccbill'}
	<form name="ccbill_form" action="admin_pays.php" method="post">
		{$form.hiddens}
		<input type="hidden" name="sel" value="saveset">
		<input type="hidden" name="settype" value="{$settype}">
		<input type="hidden" name="groups_periods" value="1">
		<table border="0" cellspacing="1" cellpadding="5" width="100%">
			<tr bgcolor="#ffffff">
				<td colspan="3" style="padding-top: 10px;">&nbsp;</td>
			</tr>
			<tr bgcolor="#ffffff">
				<td colspan="2" align="right" class="main_header_text">{$header.ccbill_groups_periods_header}</td>
				<td class="main_header_text">{$header.ccbill_subscription_types_header}</td>
			</tr>
			{section name=gp loop=$group_periods}
				<tr bgcolor="#ffffff">
					<td colspan="2" align="right" class="main_content_text">{$group_periods[gp].amount}&nbsp;{$group_periods[gp].period} - {$group_periods[gp].group_name} - {$group_periods[gp].cost}&nbsp;{$group_periods[gp].currency}</td>
					<td class="main_content_text"><input type="text" size="30" name="ccbill_group_sub_id[]" value="{$group_periods[gp].ccbill_sub_id}"><input type="hidden" name="groups_periods_id[]" value="{$group_periods[gp].id}"><input type="hidden" name="ccbill_group_id[]" value="{$group_periods[gp].ccbill_group_id}"></td>
				</tr>
			{/section}
			<tr bgcolor="#ffffff">
				<td colspan="2" align="right"><input type="button" value="{$button.save}" class="button" onclick="javascript: document.ccbill_form.submit();"></td>
				<td>&nbsp;</td>
			</tr>
		</table>
	</form>
{/if}
{if $settype eq 'allopass'}
	<form name="allopass_form" action="admin_pays.php" method="post">
		{$form.hiddens}
		<input type="hidden" name="sel" value="saveset">
		<input type="hidden" name="settype" value="{$settype}">
		<input type="hidden" name="groups_periods" value="1">
		<table border="0" cellspacing="1" cellpadding="5" width="100%">
			<tr bgcolor="#ffffff">
				<td colspan="3" style="padding-top: 10px;">&nbsp;</td>
			</tr>
			<tr bgcolor="#ffffff">
				<td colspan="2" align="right" class="main_header_text">{$header.allopass_groups_periods_header}</td>
				<td class="main_header_text">{$header.allopass_docs_header}</td>
			</tr>
			{section name=gp loop=$group_periods}
				<tr bgcolor="#ffffff">
					<td colspan="2" align="right" class="main_content_text">{$group_periods[gp].amount}&nbsp;{$group_periods[gp].period} - {$group_periods[gp].group_name} - {$group_periods[gp].cost}&nbsp;{$group_periods[gp].currency}</td>
					<td class="main_content_text">
						<input type="text" size="20" name="allopass_group_doc_id_mobile[]" value="{$group_periods[gp].allopass_doc_id_mobile}"> ({$header.allopass_docs_mobile}) &nbsp;
						<input type="text" size="20" name="allopass_group_doc_id_credit[]" value="{$group_periods[gp].allopass_doc_id_credit}"> ({$header.allopass_docs_credit}) &nbsp;
						<input type="hidden" name="groups_periods_id[]" value="{$group_periods[gp].id}">
						<input type="hidden" name="allopass_group_id[]" value="{$group_periods[gp].allopass_group_id}">
					</td>
				</tr>
			{/section}
			<tr bgcolor="#ffffff">
				<td colspan="2" align="right"><input type="button" value="{$button.save}" class="button" onclick="javascript: document.allopass_form.submit();"></td>
				<td>&nbsp;</td>
			</tr>
		</table>
	</form>
{/if}
{if $settype == "general"}
<script type="text/javascript">
{literal}
///globals
dct_obj = document.getElementById("dct");
inv_obj = document.getElementById("inv");

function getCurseForm(curr)
{
	exch_table_obj = document.getElementById("exch_table");
	if (curr != 'USD') {
		if (exch_table_obj.style.display == 'none') {
			exch_table_obj.style.display = 'block';
		}
		/*
		money_form_butt_save_obj = document.getElementById("money_form_butt_save");
		if (money_form_butt_save_obj.style.display == 'none') {
			money_form_butt_save_obj.style.display = 'block';
		}
		*/
		new_curr_obj = document.getElementById("new_curr");
		new_curr2_obj = document.getElementById("new_curr2");
		new_curr_obj.innerHTML = curr;
		new_curr2_obj.innerHTML = curr;
	}
	else {
		exch_table_obj.style.display = 'none';
		/*
		money_form_butt_save_obj = document.getElementById("money_form_butt_save");
		money_form_butt_save_obj.style.display = 'none';
		*/
	}
}
function changeByCurr(direction,value)
{
	switch (direction) {
		case 1:
			dct_obj.value = extParseFloat(dct_obj.value);
			inv_obj.value = 1/extParseFloat(dct_obj.value);
		break;
		case 2:
			inv_obj.value = extParseFloat(inv_obj.value);
			dct_obj.value = 1/extParseFloat(inv_obj.value);
		break;
	}
}
function extParseFloat(value)
{
	while(isNaN(value)) {
		value = value.substr(0,value.length-1);
	}
	if (value == "") {
		value = 1;
	}
	return value;
}
function checkMoneyForm()
{
	substr_slash_n = "[slash_n]";
	substr_first_curr = "[first_curr]";
	substr_value = "[value]";
	substr_second_curr = "[second_curr]";
	notify = "{/literal}{$lang.pays.notify_money_form}{literal}";
	currency_obj = document.money_form.currency;
	value = document.getElementById("dct").value;
	value = Math.round(value*1000)/1000				// rounding to 0.xx
	document.getElementById("dct").value = value;
	
	if (isNaN(value) || value == 0) {
		dct_obj.value = 1;
		inv_obj.value = 1;
		{/literal}
		alert("{$lang.pays.wrong_format}");
		{literal}
		document.money_form.curr_rate.focus();
		return false;
	}
	
	if (dct_obj.value == 1) {
		return true;
	}
	
	notify = notify.replace(substr_slash_n,"\n");
	notify = notify.replace(substr_first_curr,"{/literal}{$data.site_unit_costunit}{literal}");
	notify = notify.replace(substr_value,value);
	notify = notify.replace(substr_second_curr,currency_obj.options[currency_obj.selectedIndex].text);
	if (confirm(notify)) {
		return true;
	}
	return false;
}
{/literal}
</script>
{/if}
{include file="$admingentemplates/admin_bottom.tpl"}