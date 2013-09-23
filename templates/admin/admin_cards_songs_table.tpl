{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.name}</font><font class=red_sub_header>&nbsp;|&nbsp;{if $form.par eq 'songs'}{$header.songs_list}{else}{$header.ecards_import}{/if}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{if $form.par eq 'songs'}{$help.cards_songs_list}{else}{$help.ecards_import}{/if}</div>
{if $form.par eq 'songs'}
<div>
	<div style="padding-bottom: 20px;">
		<a href="{$form.add_link}"><b>{$header.add_song}</b></a>
	</div>
	<div style="padding-top: 10px;">
	<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
	{if $songs}
		<tr bgcolor="#ffffff">
			<td colspan=4 class="main_content_text">
				{if $links}{$links}{/if}
			</td>
		</tr>
		<tr class="table_header">
			<td class="main_header_text" align="center">{$header.song_name}</td>
			<td class="main_header_text" align="center" width="400"></td>
			<td class="main_header_text" align="center" width="80">{$header.status}</td>
			<td class="main_header_text" align="center" width="230">&nbsp;</td>
		</tr>
		{foreach item=item from=$songs key=key}
		<tr bgcolor="#FFFFFF">
			<td class="main_content_text" align="center">{$item.name}</td>
			<td class="main_content_text" align="center">
			{*			{if $item.file}
				<embed id="sound_{$key}" src="{$item.file}" loop=false autostart=false height="30" width="300" pluginspage="http://www.apple.com/ru/quicktime/download/">
			{else}
				{$header.no_song}
			{/if}
			*}
			{if $item.file}
				<span id="player{$key}" align=absmiddle>
					<script type="text/javascript">
						var fv = "file={$item.file}&autostart="+false+"&title={$item.name|escape}&lightcolor=0xD12627";
						{literal}var FO = {{/literal}
							movie:"{$site_root}/include/mp3player/mp3player.swf",width:"300",height:"20",majorversion:"7",build:"0",bgcolor:"#FFFFFF", flashvars:fv
						{literal}};{/literal}
						UFO.create(FO, "player{$key}");
					</script>
				</span>
			{else}
				{$header.no_song}
			{/if}
			</td>
			<td class="main_content_text" align="center">
			{if $item.status eq 1}+{else}-{/if}
			</td>
			<td class="main_content_text" align="center">
				<a href="{$item.editlink}">{$button.edit}</a>
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<a href="#" onclick="if(confirm('{$header.confirm_song}')){literal}{{/literal}document.location.href='{$item.deletelink}'{literal}}{/literal} else return false;">{$button.delete}</a>
			</td>
		</tr>
		{/foreach}
		<tr bgcolor="#ffffff">
			<td colspan=4 class="main_content_text">{$links}</td>
		</tr>
	{else}
		<tr height="40">
			<td class="main_error_text" align="left" colspan="4" bgcolor="#FFFFFF">{$header.empty_songs}</td>
		</tr>
	{/if}
	</table>
	</div>
</div>
{else}
<div>
<a href="http://pilotgroup.net/support/contact.php" target="_blank">Contact Support Team</a>
</div>
{/if}
{include file="$admingentemplates/admin_bottom.tpl"}