{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.list}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.groups_list}</div>
<br>
<font class=red_sub_header>{$header.list_2}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.groups_list_2}</div>
<div>
	<table class="main_table" id="gender_groups_div">
		<tr>
			<td class="main_header_table" width="5%">{$header.number}</td>
			<td class="main_header_table" width="60%">{$header.name}</td>
			<td class="main_header_table" width="15%">{$header.type}</td>
			<td class="main_header_table" width="10%">&nbsp;</td>
			<td class="main_header_table" width="10%">&nbsp;</td>
		</tr>
		{if $group_arr}
			{assign var="foo" value=0}
			{foreach item=item from=$group_arr}
				{if $item.is_gender_group eq '1' || $item.type eq 'r' || $item.type eq 'd' || $item.type eq 'g' || $item.type eq 'm'}
					{assign var="foo" value=$foo+1}
					<tr>
						<td class="main_content_table" width="5%">{$foo}</td>
						<td class="main_content_table" width="60%"><a {if $form.use_gender_membership eq 1} href="{$item.editlink}" {else} href="#" onclick="return false;" {/if}>{$item.name}</a></td>
						<td class="main_content_table" width="15%">{$item.type_name}</td>
						<td class="main_content_table" width="10%">{if $item.type ne 'm'}<input type="button" value="{$header.permission}" class="button" {if $form.use_gender_membership eq 1} onclick="location.href='{$item.editlink}'"{else}onclick="return false;"{/if}>{/if}</td>
						<td class="main_content_table" width="10%">{if $item.type ne 'm'}<input type="button" value="{$header.user_list}" class="button" {if $form.use_gender_membership eq 1} onclick="location.href='{$item.userlink}'"{else}onclick="return false;"{/if}>{/if}</td>
					</tr>
				{/if}
			{/foreach}
		{else}
			<tr height="40">
				<td class="main_error_text" align="left" colspan="5" bgcolor="#FFFFFF">{$header.empty}</td>
			</tr>
		{/if}
	</table>
</div>
<br>
<font class=red_sub_header>{$header.list_1}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.groups_list_1}</div>
<div>
	<table class="main_table" id="main_groups_div_1">
		{if $links}
			<tr bgcolor="#ffffff">
				<td height="20" colspan=5 align="left" class="main_content_text" >{$links}</td>
			</tr>
		{/if}
		<tr>
			<td class="main_header_table" width="5%">{$header.number}</td>
			<td class="main_header_table" width="60%">{$header.name}</td>
			<td class="main_header_table" width="15%">{$header.type}</td>
			<td class="main_header_table" width="10%">&nbsp;</td>
			<td class="main_header_table" width="10%">&nbsp;</td>
		</tr>
		{if $group_arr}
			{assign var="foo" value=0}
			{foreach item=item from=$group_arr}
				{if $item.is_gender_group ne '1'}
					{assign var="foo" value=$foo+1}
					<tr>
						<td class="main_content_table" width="5%">{$foo}</td>
						<td class="main_content_table" width="60%"><a {if $form.use_gender_membership ne 1} href="{$item.editlink}" {else} href="#" onclick="return false;" {/if}>{$item.name}</a></td>
						<td class="main_content_table" width="15%">{$item.type_name}</td>
						<td class="main_content_table" width="10%">{if $item.type ne 'm'}<input type="button" value="{$header.permission}" class="button" {if $form.use_gender_membership ne 1} onclick="location.href='{$item.editlink}'"{else}onclick="return false;"{/if}>{/if}</td>
						<td class="main_content_table" width="10%">{if $item.type ne 'm'}<input type="button" value="{$header.user_list}" class="button" {if $form.use_gender_membership ne 1} onclick="location.href='{$item.userlink}'"{else}onclick="return false;"{/if}>{/if}</td>
					</tr>
				{/if}
			{/foreach}
		{else}
			<tr>
				<td height="40" class="main_error_text" align="left" colspan="5" bgcolor="#FFFFFF">{$header.empty}</td>
			</tr>
		{/if}
		{if $links}
			<tr bgcolor="#ffffff">
				<td height="20" colspan=5 align="left" class="main_content_text" >{$links}</td>
			</tr>
		{/if}
	</table>
	<table id="main_groups_div_2">
		<tr>
			<td height="40">
				<input type="button" value="{$header.add}" class="button" {if $form.use_gender_membership ne 1}onclick="location.href='{$add_link}'"{else}onclick="return false;"{/if}>
			</td>
		</tr>
	</table>
</div>
<br>
{* disabled for TLDF
<br>
<font class=red_sub_header>{if $form.free_site eq '1'}{$header.site_free_header}{else}{$header.make_site_free_header}{/if}</font><br><br>
<table border="0" class="table_main" cellspacing=1 cellpadding=5 width="75%">
	<tr bgcolor="#FFFFFF">
		<td class="main_content_text">
			{if $form.free_site eq '1'}
				{$header.site_free_text}
			{else}
				{$header.make_site_free_text}
				{if $form.payed_count}<br>{$header.make_site_free_note}{/if}
			{/if}
		</td>
	</tr>
	{if $form.free_site neq '1'}
		<tr bgcolor="#FFFFFF">
			<td><input type="button" value="{$header.delete_groups}" class="button" onclick="javascript: if (window.confirm('{$header.make_site_free_confirm}')) document.location='{$del_groups_link}'; else return false;"></td>
		</tr>
	{/if}
</table>
*}
{literal}
<script type="text/javascript">
function HighLight(section, state) {
	if (document.getElementById(section)) {
		if (state == 1) {
			setElementOpacity(section, 0.4);
		} else {
			setElementOpacity(section, 1.0);
		}
	}
	return;
}

function setElementOpacity(sElemId, nOpacity)
{
	var opacityProp = getOpacityProperty();
	var elem = document.getElementById(sElemId);
	if (!elem || !opacityProp) return; // error
	if (opacityProp == "filter") {
		// Internet Explorer 5.5+
		nOpacity *= 100;
		var oAlpha = elem.filters['DXImageTransform.Microsoft.alpha'] || elem.filters.alpha;
		if (oAlpha) {
			oAlpha.opacity = nOpacity;
		} else {
			elem.style.filter += "progid:DXImageTransform.Microsoft.Alpha(opacity="+nOpacity+")";
		}
	} else {
		elem.style[opacityProp] = nOpacity;
	}
	return;
}

function getOpacityProperty()
{
	if (typeof document.body.style.opacity == 'string') {
		// CSS3 compliant (Moz 1.7+, Safari 1.2+, Opera 9, IE7)
		return 'opacity';
	} else if (typeof document.body.style.MozOpacity == 'string') {
		// Mozilla 1.6, Firefox 0.8
		return 'MozOpacity';
	} else if (typeof document.body.style.KhtmlOpacity == 'string') {
		// Konqueror 3.1, Safari 1.1
		return 'KhtmlOpacity';
	} else if (document.body.filters && navigator.appVersion.match(/MSIE ([\d.]+);/)[1]>=5.5) {
		// Internet Explorer 5.5+
		return 'filter'
	} else {
		return false;
	}
}
</script>
{/literal}
<script type="text/javascript">
{if $form.use_gender_membership eq 1}
HighLight('main_groups_div_1', 1);
HighLight('main_groups_div_2', 1);
{else}
HighLight('gender_groups_div', 1);
{/if}
</script>
{include file="$admingentemplates/admin_bottom.tpl"}
