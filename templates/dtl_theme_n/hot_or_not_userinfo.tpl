<div  class="content">
<table cellpadding="0" cellspacing="5" width="100%">
{if $user_info.photos}
<tr id="all_photos" style="display: none; padding-top:10px;">
	<td>
		<div style="display:inline; cursor:pointer;"><img id="small_icon" src="{$user_info.small_icon_url}" border="0" onclick="javascript: showPhoto('{$user_info.upload_url}','small_icon');" /></div>
		{foreach from=$user_info.photos item=item}
		<div style="display:inline; cursor:pointer;"><img id="small_photo_{$item.array_index}" src="{$item.small_photo}" onclick="javacript: showPhoto('{$item.photo}','small_photo_{$item.array_index}');" style="margin-left:2px;"/></div>
		{/foreach}
		<input id="firs_photo_path" type="hidden" value="{$user_info.photos.0.photo}" />
		<input id="photo_count_id" type="hidden" value="{$user_info.photos_count}" />
	</td>
</tr>
<tr id="show_photos_str"><td align="right"><a href="#" onclick="javascript: showPhotosBar();">{$user_info.photos_count} {$lang.hotornot.more_photos}</a></td></tr>
<tr><td height="10"></td></tr>
{/if}
<tr><td align="right"><a href="#" onclick="javascript: changeView(0);" style="text-decoration:none;"><b>{$lang.hotornot.skip}</b></a></td></tr>
<tr>
	<td align="center" id="main_image_td">
		<a href="{$user_info.view_link}"><img id="main_image" border="0" /></a>
		<input id="id_upload" type="hidden" name="id_uplad" value="{$user_info.id_upload}" />
		<input id="img_url" type="hidden" value="{$user_info.upload_url}" />
		<input id="id_user" type="hidden" value="{$user_info.id_user}" />
	</td>
</tr>
<tr><td height="10"></td></tr>
<tr>
	<td>
		<a href="{$user_info.view_link}"><b>{$user_info.login}</b></a> , {$user_info.years} {$lang.home_page.ans}
	</td>
</tr>
<tr><td height="10"></td></tr>
<tr>
	<td>
		<font id="user_comment">{$user_info.comment}</font>
	</td>
</tr>
<tr><td height="20"></td></tr>
<tr>
	<td align="center">
		{include file="$gentemplates/user_links.tpl"}
	</td>
</tr>
</table>
</div>
{if $user_info.hotlist}
<div class="sep"></div>
<div class="content">
	<div class="header">{$lang.hotornot.hotlist}</div>
	<div class="sep"></div>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
			{foreach from=$user_info.hotlist item=item }
			<div style=" float:left; padding-right:6px;"><a href="{$item.view_link}"><img src="{$item.icon_url}" border="0" /></a><br><a href="{$item.view_link}"><b>{$item.login}</b></a></div>
			{/foreach}
		</td>
	</tr>
	</table>
</div>
{/if}