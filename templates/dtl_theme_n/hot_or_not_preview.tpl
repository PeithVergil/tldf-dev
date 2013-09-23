<div class="content" id="preview_div">
	<div class="header">{$lang.hotornot.next}</div>
	<div class="sep"></div>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td align="left">
			{foreach from=$preview item=item name=preview}
			<span style="display: inline; cursor:pointer; padding:0px 3px 3px 0px" id="preview_div_{$item.array_index}"><img style="margin:0px 0px 0px 0px;" src="{$item.upload_url}" id="preview_{$item.array_index}" onClick="javascript: changeView({$item.array_index});" border="0"></span>
			{/foreach}
			<div id="addition_preview_data"></div>
		</td>
	</tr>
	</table>
</div>
<div class="sep"></div>