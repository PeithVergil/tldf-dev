{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.list}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.banner_module_edit}</font>
<div style="padding-top: 10px;">
<form method="post" action="modules.php" enctype="multipart/form-data">
<input type="hidden" name="sel" value="save_banner">
<input type="hidden" name="num" value="{$banner.num}">
<table border=0 class="table_main" cellspacing=1 cellpadding=5>
<tr bgcolor="#FFFFFF">
	<td class="main_header_text" valign="top" align="right">{$header.banners_title.image}:&nbsp;</td>
	<td>
	{if $banner.num ne 0}
		{if $banner.ext eq 'swf'}
		<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0"
			type="application/x-shockwave-flash">
			<param name="movie" value="{$site_root}/flash_chat/modules/banner/{$banner.src}" />
			<param name="quality" value="high" />
			<param name="pluginurl" value="http://www.macromedia.com/go/getflashplayer" />
			<embed src="{$site_root}/flash_chat/modules/banner/{$banner.src}" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>
		</object>
		{else}
		<img src="{$site_root}/flash_chat/modules/banner/{$banner.src}" alt="" border="0">
		{/if}
	{/if}
	</td>
	<td><input type="file" name="banner_file"></td>
</tr>
<tr bgcolor="#FFFFFF">
	<td valign="top" class="main_header_text" align="right">{$header.banners_title.url}:&nbsp;</td>
	<td><input type="text" name="url" value="{$banner.url}" style="width: 200px;"></td>
	<td></td>
</tr>
<tr bgcolor="#FFFFFF">
	<td valign="top" class="main_header_text" align="right">{$header.banners_title.target}:&nbsp;</td>
	<td><input type="text" name="target" value="{$banner.target}" style="width: 200px;"></td>
	<td></td>
</tr>
<tr bgcolor="#FFFFFF">
	<td valign="top" class="main_header_text" align="right">{$header.banners_title.fading}:&nbsp;</td>
	<td><input type="checkbox" name="fading" value="1" {if $banner.fading eq 'true'}checked{/if} style="width: 200px;"></td>
	<td></td>
</tr>
<tr bgcolor="#FFFFFF">
	<td colspan="3"><input type="submit" value="{$lang.button.save}">&nbsp;
	{if $banner.num ne 0}<input type="button" value="{$lang.button.delete}"  onclick="{literal} if (confirm('{/literal}{$header.del_confirm}{literal}')) {document.location.href='modules.php?sel=delete_banner&num_banner={/literal}{$banner.num}{literal}'}{/literal}">&nbsp;{/if}
	<input type="button" value="{$lang.button.back}" onclick="document.location.href='modules.php?sel=edit&id_module=1'">
	</td>
</tr>
</table>
</form>
</div>
<p align="right" class="main_header_text">&copy;<a href="http://tufat.com/" target="_blank">TUFaT.com</a> </p>
{include file="$admingentemplates/admin_bottom.tpl"}