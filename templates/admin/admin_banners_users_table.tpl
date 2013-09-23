{include file="$admingentemplates/admin_top.tpl"} <font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.list}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$lang.banners.help_admin_users_table}</div>
{if $error}<div class="error_msg">{$error}</div>{/if}
<table class="simple_centered_table" cellspacing="1" cellpadding="0">
<tr>
	<th>{$lang.banners.username}</th>
	<th>{$lang.banners.status}</th>
	<th>{$lang.banners.banner}</th>
	<th>{$lang.banners.link}</th>
	<th>{$lang.banners.place}</th>
	<th>{$lang.banners.stop_after}</th>
	<th>&nbsp;</th>
</tr>
{foreach from=$all_banners item=one_banner}
<tr>
	<td><a href="{$one_banner.profile_link}">{$one_banner.login}</a></td>
	<td> 
		{if $one_banner.payment_status == 'toaprove'}
		{$lang.banners.to_aprove}
		{elseif $one_banner.payment_status == 'topay'}
		{$lang.banners.to_pay}
		{elseif $one_banner.payment_status == 'payed'}
		{$lang.banners.aproved}<br />
		<input type="checkbox" name="comments" disabled="disabled" {if $one_banner.status eq 1}checked="checked"{/if}>
		{/if} 
	</td>
	<td>
		<table cellspacing="1" cellpadding="0">
		<tr>
			<td>{$one_banner.name}</td>
		</tr>
		<tr>
			<td>
				{if $one_banner.banner_type  eq "1"}
				<b>{$lang.banners.this_is_html_code}</b><br />
				<textarea readonly="readonly" style="width:150px; height:75px;">{$one_banner.html_code}</textarea>
				{else}
				{$lang.banners.size}: {$one_banner.size_x}x{$one_banner.size_y}<br />
				{if $one_banner.img_file_path neq ""}<img src="{$one_banner.img_file_path}" width="{$one_banner.show_size_x}" height="{$one_banner.show_size_y}" alt="{$one_banner.alt_text}" />{/if}
				{/if}
			</td>
		</tr>
		</table>
	</td>
	<td>{if $one_banner.banner_type neq "1"}<a href="{$one_banner.banner_url}">{$one_banner.banner_url}</a>{/if}</td>
	<td>
		{$lang.banners.position}: {if $one_banner.place eq '0'}{$lang.banners.position_left}{elseif $one_banner.place eq '1'}{$lang.banners.position_bottom}{/if}
		{if $one_banner.areas}
		<br /><br />
		{/if}
		{foreach from=$one_banner.areas item=one_area}
		{$one_area.description};&nbsp&nbsp
		{/foreach} 
	</td>
	<td>
	{if $one_banner.payment_status == 'payed'}
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
	<td>
		{if $one_banner.payment_status == 'toaprove'}
		<a href="#" onclick="window.open('{$file_name}?sel=get_resols&id={$one_banner.id}', '1sdc','height=520, width=800, resizable=1, scrollbars=1')">{$lang.banners.aprove_link}</a><br /><br />
		{/if}
		{if $one_banner.stoped_by_date || $one_banner.payment_status != 'payed'}
		<a href="#" onclick="javascript: if (window.confirm('{$lang.banners.realy_delete}')){literal}{{/literal}location.href='{$file_name}?sel=delete&id={$one_banner.id}';{literal}}{/literal}">{$lang.button.delete}</a>
		{/if}
	</td>
</tr>
{/foreach}
</table>
{include file="$admingentemplates/admin_bottom.tpl"}