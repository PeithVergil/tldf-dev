{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.list}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.banner_module_edit}</font>
<div style="padding-top: 10px;">
<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
<tr class="table_header">
	<td class="main_header_text" align="center">{$header.banners_title.image}</td>
	<td width="200" class="main_header_text" align="center">{$header.banners_title.url}</td>
	<td width="30" class="main_header_text" align="center">{$header.banners_title.target}</td>
	<td width="30" class="main_header_text" align="center">{$header.banners_title.fading}</td>
	<td width="30">&nbsp;</td>
	<td width="30">&nbsp;</td>
</tr>
{foreach item=item from=$banners}
<tr bgcolor="#FFFFFF">
	<td class="main_content_text" align="center">
	{if $item.ext eq 'swf'}
	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0"
		type="application/x-shockwave-flash">
		<param name="movie" value="{$site_root}/flash_chat/modules/banner/{$item.src}" />
		<param name="quality" value="high" />
		<param name="pluginurl" value="http://www.macromedia.com/go/getflashplayer" />
		<embed src="{$site_root}/flash_chat/modules/banner/{$item.src}" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>
	</object>
	{else}
	<img src="{$site_root}/flash_chat/modules/banner/{$item.src}" alt="" border="0">
	{/if}
	</td>
	<td valign="top" class="main_content_text" align="center">{$item.url}</td>
	<td valign="top" class="main_content_text" align="center">{$item.target}</td>
	<td valign="top" class="main_content_text" align="center">{if $item.fading eq 'true'}{$header.banners_module.true}{else}{$header.banners_module.false}{/if}</td>
	<td valign="top" align="center"><input type="button" value="{$lang.button.edit}" onclick="document.location.href='modules.php?sel=edit_banner&num_banner={$item.num}'"></td>
	<td valign="top" align="center"><input type="button" value="{$lang.button.delete}"  onclick="{literal} if (confirm('{/literal}{$header.del_confirm}{literal}')) {document.location.href='modules.php?sel=delete_banner&num_banner={/literal}{$item.num}{literal}'}{/literal}">
	</td>
</tr>
{/foreach}
<tr bgcolor="#FFFFFF">
	<td colspan="6"><input type="submit" value="{$lang.button.add}" onclick="document.location.href='modules.php?sel=edit_banner&num_banner=0'"></td>
</tr>
</table>
</div>
<p align="right" class="main_header_text">&copy;<a href="http://tufat.com/" target="_blank">TUFaT.com</a> </p>
{include file="$admingentemplates/admin_bottom.tpl"}