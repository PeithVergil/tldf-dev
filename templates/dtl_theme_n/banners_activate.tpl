{include file="$gentemplates/index_top.tpl"}
<div class="header">{$lang.banners.pages}</div>
{if $form.error}
	<div class="error_msg">{$form.error}</div>
{/if}
<table border=0 cellspacing=1 cellpadding=5 width="100%">
<form name="edit_resols" enctype="multipart/form-data" method="post" action="{$file_name}">
	{$form.hiddens}
	<tr>
		<td colspan="2" bgcolor="#FFFFFF">
			<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">			
			<tr bgcolor="#FFFFFF">
				{foreach name=a from=$areas item=item}
				<td align="left" width="6%">
					{$item.price} {$settings.site_unit_costunit}
				</td>
				<td width="6%">
					<input type="checkbox" id="ar{$smarty.foreach.a.iteration}" name="area[]" value="{$item.id}" {if $item.checked eq 1} checked="checked" {/if} onclick="javascript: CalcForm();" style="position:relative; top:4px;">
				</td>
				<td width="36%">
					{$item.description}
					<input type="hidden" id="pr{$smarty.foreach.a.iteration}" value="{$item.price}" />
				</td>
				{if $smarty.foreach.a.last && $smarty.foreach.a.iteration is div by 1}
				<td class="main_content_text" align="left" width="50%" colspan="3"></td>
				{/if}
				{if $smarty.foreach.a.iteration is div by 2 }
				</tr><tr bgcolor="#FFFFFF">
				{/if}
				{/foreach}
				<input type="hidden" id="count" value="{$smarty.foreach.a.iteration}" />
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0">
			<tr>
				<td>{$lang.banners.count_account}: <b>{$curr_count} {$settings.site_unit_costunit}</b></td>
			</tr>
			<tr>
				<td>{$lang.banners.count}: <b><span id="curr_count">0</span> {$settings.site_unit_costunit}</b></td>
			</tr>
			<tr>
				<td>{$lang.banners.total_pages}: <b><span id="pages">0</span></b></td>
			</tr>
			<tr>
				<td>{$lang.banners.for_period}: <b>{$settings.banner_period_amount} {$settings.banner_period}</b></td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td>{$lang.banners.renew_account}: <a href="{$form.account_link}">{$lang.section.account}</a></td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="main_content_text" width="100%"><input id="submit_butt" type="submit" value="{$lang.banners.activate_link}" class="button" disabled="disabled"></td>
	</tr>
</form>
</table>
<script>
{literal}

count = document.getElementById('count').value;
account = ownParseFloat(document.getElementById('id_account').value);
disableExtraAreas(0);

function CalcForm(){
	summ = 0;
	count_pages = 0;
	for(i=1;i<=count;i++){
		if (document.getElementById('ar'+i).checked){
			checked=1;
			count_pages++;
			summ = ownParseFloat(parseFloat(summ) + parseFloat(document.getElementById('pr'+i).value));
		}
	}
	disableExtraAreas(summ);
	document.getElementById('curr_count').innerHTML = summ;
	document.getElementById('pages').innerHTML = count_pages;
	if (count_pages>0) document.getElementById('submit_butt').disabled = false;
	else document.getElementById('submit_butt').disabled = true;
}

function disableExtraAreas(summ){
	for(i=1;i<=count;i++){
		new_summ = summ + ownParseFloat(document.getElementById('pr'+i).value);
		if (new_summ>account){
			if (document.getElementById('ar'+i).checked==false)
				document.getElementById('ar'+i).disabled=true;
		}else{
			document.getElementById('ar'+i).disabled=false;
		}
	}
}

function CheckMyForm(){
	butt = document.getElementById('submit_butt');
	if (CheckForm()) butt.disabled = false;
	else butt.disabled = true;
}

function ownParseFloat(value){
	return Math.round(parseFloat(value)*100)/100;
}

{/literal}
</script>
{include file="$gentemplates/index_bottom.tpl"}