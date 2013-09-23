{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.banner_statistics}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.banner_statistics_help}</div>
<div>
	<table cellpadding="0" cellspacing="0" width="100%" style="margin: 0px; margin-bottom: 10px;">
		<tr>
			<td>{strip}
				<b>{$lang.banners.view_stat_by}:</b>&nbsp;&nbsp;
				{foreach from=$period_arr item=period}
				{assign var="name" value='by_'|cat:$period}
				{if $period==$period_type}
					<b>{$lang.banners[$name]}</b>
				{else}
					<a href="{$sort_order_link}&period_type={$period}">{$lang.banners[$name]}</a>
				{/if}&nbsp;&nbsp;
				{/foreach}
				{/strip}
			</td>
			<td align="right">
				<form name="rows_per_page" action="{$file_name}">
				{foreach from=$form.hiddens item=hidden}
					<input type="hidden" name="{$hidden.name}" value="{$hidden.value}">
				{/foreach}
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td><b>{$lang.banners.rows_per_page}:</b></td>
						<td style="padding-left: 10px;">
							<select name="rows_num_page" onchange="document.forms.rows_per_page.submit();">
							{foreach from=$rows_per_page item=row}
								<option value="{$row}" {if $row==$rows_num_page}selected{/if}>{$row}</option>
							{/foreach}
							</select>
						</td>
					</tr>
				</table>
				</form>
			</td>
		</tr>
	</table>
	<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
		<tr>
		{if $period_type == "day"}
			<td class="main_header_text" align="center">
				<div style="cursor: pointer;" onclick="javascript:location.href='{$sort_order_link}day'">{$lang.banners.day}{if $sorter==day}{$order_icon}{/if}</div>
			</td>
		{elseif $period_type == "week"}
			<td class="main_header_text" align="center">
				<div style="cursor: pointer;" onclick="javascript:location.href='{$sort_order_link}week'">{$lang.banners.week}{if $sorter==week}{$order_icon}{/if}</div>
			</td>
			<td class="main_header_text" align="center">
				<div style="cursor: pointer;" onclick="javascript:location.href='{$sort_order_link}month'">{$lang.banners.month}{if $sorter==month}{$order_icon}{/if}</div>
			</td>
			<td class="main_header_text" align="center">
				<div style="cursor: pointer;" onclick="javascript:location.href='{$sort_order_link}year'">{$lang.banners.year}{if $sorter==year}{$order_icon}{/if}</div>
			</td>
		{elseif $period_type == "month"}
			<td class="main_header_text" align="center">
				<div style="cursor: pointer;" onclick="javascript:location.href='{$sort_order_link}month'">{$lang.banners.month}{if $sorter==month}{$order_icon}{/if}</div>
			</td>
			<td class="main_header_text" align="center">
				<div style="cursor: pointer;" onclick="javascript:location.href='{$sort_order_link}year'">{$lang.banners.year}{if $sorter==year}{$order_icon}{/if}</div>
			</td>
		{elseif $period_type == "year"}
			<td class="main_header_text" align="center">
				<div style="cursor: pointer;" onclick="javascript:location.href='{$sort_order_link}year'">{$lang.banners.year}{if $sorter==year}{$order_icon}{/if}</div>
			</td>
		{/if}
			<td class="main_header_text" align="center">
			<div style="cursor: pointer;" onclick="javascript:location.href='{$sort_order_link}views'">{$lang.banners.views_cnt}{if $sorter==views}{$order_icon}{/if}</div>
			</td>
		{if $type == "image"}
			<td class="main_header_text" align="center">
			<div style="cursor: pointer;" onclick="javascript:location.href='{$sort_order_link}hits'">{$lang.banners.hits_cnt}{if $sorter==hits}{$order_icon}{/if}</div>
			</td>
			<td class="main_header_text" align="center">
			<div style="cursor: pointer;" onclick="javascript:location.href='{$sort_order_link}ctr'">{$lang.banners.ctr}{if $sorter==ctr}{$order_icon}{/if}</div>
			</td>
		{/if}
		</tr>
	{foreach from=$statistics item=stat}
		<tr>
		{if $period_type == "day"}
			<td align="center">{$stat.date_format}</td>
		{elseif $period_type == "week"}
			<td align="center">{$stat.week}</td>
			<td align="center">{$month_name[$stat.month]}</td>
			<td align="center">{$stat.year}</td>
		{elseif $period_type == "month"}
			<td align="center">{$month_name[$stat.month]}</td>
			<td align="center">{$stat.year}</td>
		{elseif $period_type == "year"}
			<td align="center">{$stat.year}</td>
		{/if}
			<td align="center">{$stat.views}</td>
		{if $type == "image"}
			<td align="center">{$stat.hits}</td>
			<td align="center">{$stat.ctr}</td>
		{/if}
		</tr>
	{/foreach}
	</table>
	<table cellpadding="0" cellspacing="0" style="margin: 0px; margin-bottom: 10px;" width="100%">
		<tr>
			<td align="left">
			{if $links}
				{$links}
			{else}
				&nbsp;
			{/if}
			</td>
			<td align="right">
			{strip}
				<b>{$lang.banners.all_period_stat}:</b> {$lang.banners.views_cnt} - <b>{if $total_stat.views}{$total_stat.views}{else}0{/if}</b>
			{if $type == "image"}
				, {$lang.banners.hits_cnt} - <b>{if $total_stat.hits}{$total_stat.hits}{else}0{/if}</b>, {$lang.banners.ctr} - <b>{if $total_stat.ctr}{$total_stat.ctr}{else}0{/if}</b>
			{/if}.
			{/strip}
			</td>
		</tr>
		<tr>
			<td colspan="2" align="left" style="padding-top: 10px;"><input type="button" value="{$lang.banners.back_to_list}" class="button" onclick="javascript:document.location='{$file_name}';"></td>
		</tr>
	</table>
</div>
{include file="$admingentemplates/admin_bottom.tpl"}