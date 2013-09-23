{include file="$admingentemplates/admin_top.tpl"}
{strip}
<font class=red_header>{$header.name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.songs_edit}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.cards_songs_edit}</div>
<div>
<form method="post" action="{$form.action}" enctype="multipart/form-data">
<input type="hidden" name="par" value="{$form.par}">
{if $form.par eq 'edit'}
<input type="hidden" name="id" value="{$data.id}">
{/if}
<table class="table_main" cellspacing=1 cellpadding=5 width="100%">
	<tr bgcolor="#ffffff">
		<td width="200" class="main_header_text">{$header.song_name} <font class=main_error_text>*</font>:&nbsp;</td>
		<td><input name="song_name" value="{$data.song_name}" style="width: 195px;"></td>
	</tr>
	<tr bgcolor="#ffffff">
		<td class="main_header_text" valign="top">{$header.song_status}:&nbsp;</td>
		<td>
			<input type="checkbox" name="song_status" {if $data.song_status eq 1}checked{/if} value="1">
		</td>
	</tr>
	<tr bgcolor="#ffffff">
		<td class="main_header_text" valign="top">{$header.song_file}:&nbsp;</td>
		<td>
			<div>{if $data.song_file}
			{*<embed id="sound_{$key}" src="{$data.song_file}" loop=false autostart=false height="30" width="300" pluginspage="http://www.apple.com/ru/quicktime/download/">*}
			<span id="player0" align=absmiddle>
				<script type="text/javascript">
					var fv = "file={$data.song_file}&autostart="+false+"&title={$data.song_name|escape}&lightcolor=0xD12627";
					{literal}var FO = {{/literal}
						movie:"{$site_root}/include/mp3player/mp3player.swf",width:"300",height:"20",majorversion:"7",build:"0",bgcolor:"#FFFFFF", flashvars:fv
					{literal}};{/literal}
					UFO.create(FO, "player0");
				</script>
			</span>
			{else}{$header.no_file}{/if}</div>
			<div style="padding-top: 5px;"><input type="file" name="song_file"></div>
		</td>
	</tr>
	<tr bgcolor="#ffffff">
		<td>&nbsp;</td>
		<td>
			{if $form.par eq 'edit'}<input type="submit" value="{$button.save}" class="button">{else}<input type="submit" value="{$button.add}" class="button">{/if}&nbsp;&nbsp;
			<input type="button" value="{$button.back}" class="button" onclick="document.location.href='{$form.back}'">
		</td>
	</tr>
</table>
</form>
</div>
{/strip}
{include file="$admingentemplates/admin_bottom.tpl"}