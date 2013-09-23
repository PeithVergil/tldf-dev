{include file="$admingentemplates/admin_top_popup.tpl"}
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$lang.banners.help_admin_aprove_table}</div>
{if $form.error}
<div class="main_error_text">* {$form.error}</div>
{/if}
<form name="edit_resols" enctype="multipart/form-data" method="post" action="{$file_name}">
	{$form.hiddens}
	<table class="simple_table" cellpadding="0" cellspacing="1">
	<tr>
		<th colspan="2">{$lang.banners.banners_arrea}</th>
	</tr>
	<tr> {foreach name=a from=$areas item=item}
		<td width="50%"><input type="checkbox" id="ar{$smarty.foreach.a.iteration}" name="area[]" value="{$item.id}" {if $item.checked eq 1} checked="checked" {/if} onclick="javascript: CheckMyForm();"> {$item.description}</td>
		{if $smarty.foreach.a.last && $smarty.foreach.a.iteration is not div by 2}
		<td width="50%"></td>
		{/if}
		{if $smarty.foreach.a.iteration is div by 2 && !$smarty.foreach.a.last}</tr><tr>{/if}
		{/foreach}
		<input type="hidden" id="count" value="{$smarty.foreach.a.iteration}" />
	</tr>
	</table><br />
	<table cellpadding="0" cellspacing="0">
	<tr><td style="padding-bottom:10px;"><a href="#" onclick="javascript: checkuncheckAll(1);">{$lang.button.checkall}</a>/<a href="#" onclick="javascript: checkuncheckAll(0);">{$lang.button.uncheckall}</a></td></tr>
	<tr>
		<td><input id="submit_butt" type="submit" value="{$lang.banners.aprove_link}" class="button" disabled="disabled"><input type="button" value="{$lang.button.close}" class="button" onclick="javascript: window.close();">
		</td>
	</tr>
	</table>
</form>
<script>
{literal}
count = document.getElementById('count').value;
function CheckForm(){
	count = document.getElementById('count').value;
	for(i=1;i<=count;i++){
		if (document.getElementById('ar'+i).checked) return true;
	}
	return false;
}

function CheckMyForm(){
	butt = document.getElementById('submit_butt');
	if (CheckForm()) butt.disabled = false;
	else butt.disabled = true;
} 
 
function checkuncheckAll(ch){
	for(i=1;i<=count;i++){
		if (document.getElementById('ar'+i).disabled==false){
			switch (ch){
				case 0: document.getElementById('ar'+i).checked=false; break;
				case 1:	document.getElementById('ar'+i).checked=true; break;
			}
		}
	}
	CheckMyForm();
}
{/literal}
</script>
{include file="$admingentemplates/admin_bottom_popup.tpl"}