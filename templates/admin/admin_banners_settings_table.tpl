{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$lang.admin_menu.banners_settings}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$lang.banners.help_admin_settings}</div>
{if $form.error}<div class="main_error_text" align="left">* {$form.error}</div>{/if}

<table border=0 cellspacing=1 cellpadding=5 width="100%">
<form name="edit_prices" enctype="multipart/form-data" method="post" action="{$file_name}">
	{$form.hiddens}
	<tr>
		<td>
			{$lang.banners.choose_period}:
			<input type="text" name="amount" id="id_amount" value="{$settings.banner_period_amount}" size="5"/>
			<select name="period">
				<option value="day" {if $settings.banner_period=='day'}selected{/if}>{$lang.banners.period.day}</option>
				<option value="month" {if $settings.banner_period=='month'}selected{/if}>{$lang.banners.period.month}</option>
				<option value="year" {if $settings.banner_period=='year'}selected{/if}>{$lang.banners.period.year}</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>
			<table class="simple_table" cellspacing=1 cellpadding=0>
			<tr>
				<th colspan="2">{$lang.banners.banners_arrea}</th>
			</tr>
			<tr>
				{foreach name=a from=$areas item=item}
				<td width="50%">
					<input type="text" id="pr{$smarty.foreach.a.iteration}" name="price[{$item.id}]" value="{$item.price}" size="5"> {$settings.site_unit_costunit} {$lang.banners.for} {$item.description}
				</td>
				{if $smarty.foreach.a.last && $smarty.foreach.a.iteration is div by 1}
				<td width="50%"></td>
				{/if}
				{if $smarty.foreach.a.iteration is div by 2 }
				</tr><tr>
				{/if}
				{/foreach}
				<input type="hidden" id="count" value="{$smarty.foreach.a.iteration}" />
			</tr>	
			</table>
		</td>
	</tr>
	<tr>
		<td width="100%"><input id="submit_butt" type="button" value="{$lang.button.save}" class="button" onclick="javascript: CheckForm();"></td>
	</tr>
	</td>
</form>
</table>
<script>
{literal}

function CheckForm(){
	
	amount = parseInt(document.getElementById('id_amount').value);
	if(isNaN(amount)) amount = 0;
	if (amount == 0) return alert("{/literal}{$lang.banners.zero_period_amount}{literal}");
	
	count = document.getElementById('count').value;
	check = true;
	for(i=1;i<=count;i++){
		price = parseFloat(document.getElementById('pr'+i).value);
		if (isNaN(price)) document.getElementById('pr'+i).value = 0;
		if (document.getElementById('pr'+i).value == 0) check = false;
	}
	if (check) document.edit_prices.submit();
	else{
		if (confirm("{/literal}{$lang.banners.zero_price}{literal}")) document.edit_prices.submit(); 
	};
}  
{/literal}
</script>{include file="$admingentemplates/admin_bottom.tpl"}