{include file="$gentemplates/index_top_popup.tpl"}
{strip}
			<td width="100%" valign="top">
				<div class="hdr2x">{$lang.cards.choose_a_song}</div>
				<table cellpadding="0" cellspacing="0">
					{foreach item=item from=$songs key=key}
						<tr>
							<td height="30">
								{if $item.file}
									{* <embed id="sound_{$key}" src="{$item.file}" loop=false autostart=false height="30" width="250" pluginspage="http://www.apple.com/ru/quicktime/download/"> *}
									<div id="player{$key}">
										<script type="text/javascript">
											var fv = "file={$item.file}&autostart=false&title={$item.name|escape}&lightcolor=0xD12627&repeat=true";
											var FO = {ldelim}
												movie:"{$site_root}/include/mp3player/mp3player.swf",width:"300",height:"20",majorversion:"7",build:"0",bgcolor:"#FFFFFF", flashvars:fv
											{rdelim};
											UFO.create(FO, "player{$key}");
										</script>
									</div>
								{else}
									{$lang.cards.admin.no_song}
								{/if}
							</td>
							<td style="padding-left: 3px;">
								<input type="radio" name="song_id" value="{$item.id}" id="song_{$item.id}" onclick="ChooseAct(this.value, '{$item.name_unslashed}','{$item.file}');">
							</td>
							<td style="padding-left: 3px;"><label for="song_{$item.id}">{$item.name}</label></td>
						</tr>
					{/foreach}
					<tr>
						<td></td>
						<td colspan="2" style="padding-top: 10px; padding-left: 3px;">
						<input type="submit" class="button" value="{$lang.button.choose}" onclick="parent.GB_hide();">
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
{/strip}
{literal}
<script type="text/javascript">
var choosen = 0;
function ChooseAct(id_song, song_name, song_file) {
	parent.document.getElementById('id_song').value=id_song;
	parent.document.getElementById('song_span').innerHTML=song_name+'&nbsp;&nbsp;&nbsp;';
	choosen = 1;
	return;
}
</script>
{/literal}
</body>
</html>