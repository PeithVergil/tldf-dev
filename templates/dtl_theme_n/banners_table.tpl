{include file="$gentemplates/index_top.tpl"}
<td>
<div class="header">{$lang.section.banners}</div>
<div class="sep"></div>
<div align="left">{$lang.banners.comments}</div>
<div style="padding:10px 0px;"><input type="button" value="{$lang.button.add}" class="button" onclick="javascript: location.href='{$file_name}?sel=add';"></div>
<table class="simple_centered_table" cellspacing=2 cellpadding=0>
{if $all_banners}
<tr>
	<th>{$lang.banners.status}</th>
	<th>{$lang.banners.banner}</th>
	<th>{$lang.banners.link}</th>
	<th>{$lang.banners.place}</th>
	<th>&nbsp;</th>
</tr>
{foreach name=b from=$all_banners item=one_banner}
<tr>
	<td>		
		{if $one_banner.payment_status == 'toaprove'}
		{$lang.banners.to_aprove}
		{elseif $one_banner.payment_status == 'topay'}
		{$lang.banners.to_pay}
		{elseif $one_banner.payment_status == 'payed'}
		{$lang.banners.aproved}<br />
		<input type="checkbox" name="comments" disabled="disabled" {if $one_banner.status eq 1}checked="checked"{/if}>
		{/if}
		{if $one_banner.payment_status == 'payed'}
		<br />
			{if $one_banner.stoped_by_date}
			  {$lang.banners.stoped_by_date}!
			{elseif $one_banner.stop_after_date neq "0000-00-00"}
			 {if $one_banner.stop_after_date neq "0000-00-00"}
			   {$lang.banners.stop_after_date}:<br />{$one_banner.stop_after_date}
			 {/if}
			{else}
			 {$lang.banners.never_stop}
			{/if}
		{/if}
	</td>
	<td align="center">
		{$one_banner.name}<br /><br />
		{if $one_banner.banner_type eq "1"}
		<b>{$lang.banners.this_is_html_code}</b><br />
		<textarea readonly="readonly" style="width:150px; height:75px;">{$one_banner.html_code}</textarea>
		{else}
		{$lang.banners.size}: {$one_banner.size_x}x{$one_banner.size_y}<br />{if $one_banner.img_file_path neq ""}<img src="{$one_banner.img_file_path}" width="{$one_banner.show_size_x}" height="{$one_banner.show_size_y}" alt="{$one_banner.alt_text}"> {/if} 
		{/if}
	</td>
	{if $one_banner.banner_type  eq "1"}
	<td></td>
	{else}
	<td><a href="{$one_banner.banner_url}">{$one_banner.banner_url}</a> </td>
	{/if}
	<td>
		{$lang.banners.position}: {if $one_banner.place eq 0}{$lang.banners.position_left}{else}{$lang.banners.position_bottom}{/if} <br /><br />
		{foreach from=$one_banner.areas item=one_area}
		{$one_area.description};&nbsp&nbsp
		{/foreach} 
	</td>
	<td>
	{if $one_banner.payment_status == 'topay'}
		<a href="{$one_banner.activate_link}">{$lang.banners.activate_link}</a><br /><br />
	{/if}
		<a href="{$one_banner.delete_link}">{$lang.button.delete}</a>
	</td>
</tr>
{if !$smarty.foreach.b.last}
<tr><td colspan="5" class="separator"></td></tr>{/if}
{/foreach}
{else}
<tr>
	<td colspan="5"><div class="error_msg">{$lang.err.no_banners}</div></td>
</tr>
{/if}
</table>
</td>
{include file="$gentemplates/index_bottom.tpl"}