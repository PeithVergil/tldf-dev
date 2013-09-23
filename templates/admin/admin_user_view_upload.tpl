{include file="$admingentemplates/admin_top.tpl"}
<font class="red_header">{$header.razdel_name}</font><font class="red_sub_header">&nbsp;|&nbsp;{$header.editform_upload}&nbsp;{$data.username}</font><br>
<div align="center">
	<table bordercolor="#FFFFFF" cellspacing="0" cellpadding="0" border="1" id="table4" width="100%">
		{if $data.upload_type == "f" || $data.upload_type == "club"}
			<tr>
				<td align="center" width="100%" class="main_header_text">
					<img border=1 bordercolor=1 src="{$data.file_path}">
				</td>
			</tr>
		{else}
			<tr>
				<td align="center" width="100%" class="main_header_text">
					{if $smarty.session.videoplay == 'RTMP'}
						{if $smarty.const.VIDEO_PLAYER_RTMP == 'flowplayer_scripted'}
							{*<!--
								swf can also be loaded from local files:
								{$site_root}/javascript/flowplayer-3.2.16/flowplayer-3.2.16.swf
								{$site_root}/javascript/flowplayer-3.2.16/flowplayer.rtmp-3.2.12.swf
								rtmp falls back to rtmpt automatically
								wmode=transparent for immediately hiding flowplayer when colorbox is closed
							-->*}
							<a id="player" style="display:block;width:{$settings.flv_player_width}px;height:{$settings.flv_player_height}px"></a>
							<script type="text/javascript">
								flowplayer('player', {ldelim}
										'src' : 'http://releases.flowplayer.org/swf/flowplayer-3.2.16.swf',
										'wmode' : 'transparent'
									{rdelim},
									{ldelim}
										'playlist': [
											{ldelim} 'url': '{$data.image_path}' {rdelim},
											{ldelim} 'url': 'rtmp://www.dev.thailadydatefinder.com/vod-devtldf/mp4:{$data.file_name}', 'provider': 'rtmp', 'autoPlay': false {rdelim}
										],
										'plugins': {ldelim}
											'rtmp': {ldelim}
												'url': 'http://releases.flowplayer.org/swf/flowplayer.rtmp-3.2.12.swf'
											{rdelim}
									{rdelim}
								{rdelim});
							</script>
						{elseif $smarty.const.VIDEO_PLAYER_RTMP == 'flowplayer_hardcoded'}
							{*<!--
								swf can also be loaded from local files:
								{$site_root}/javascript/flowplayer-3.2.16/flowplayer-3.2.16.swf
								{$site_root}/javascript/flowplayer-3.2.16/flowplayer.rtmp-3.2.12.swf
								rtmp falls back to rtmpt automatically
								wmode=transparent for immediately hiding flowplayer when colorbox is closed
							-->*}
							<object width="{$settings.flv_player_width}" height="{$settings.flv_player_height}" type="application/x-shockwave-flash" data="http://releases.flowplayer.org/swf/flowplayer-3.2.16.swf">
								<param name="movie" value="http://releases.flowplayer.org/swf/flowplayer-3.2.16.swf" />
								<param name="allowfullscreen" value="true" />
								<param name="allowScriptAccess" value="always" />
								<param name="quality" value="high" />
								<param name="bgcolor" value="#000000" />
								<param name="wmode" value="transparent" />
								{*<!-- not used when flowplayer is created with javascript
								<param name="scaleMode" value="showAll" />
								<param name="allowNetworking" value="all" />
								-->*}
								<param name="flashvars" value="config={ldelim}
									'playlist': [
										{ldelim} 'url': '{$data.image_path}' {rdelim},
										{ldelim} 'url': 'rtmp://www.dev.thailadydatefinder.com/vod-devtldf/mp4:{$data.file_name}', 'provider': 'rtmp', 'autoPlay': false {rdelim}
									],
									'plugins': {ldelim}
										'rtmp': {ldelim}
											'url': 'http://releases.flowplayer.org/swf/flowplayer.rtmp-3.2.12.swf'
										{rdelim}
									{rdelim}
								{rdelim}" />
								<embed type="application/x-shockwave-flash" src="http://releases.flowplayer.org/swf/flowplayer-3.2.16.swf" width="{$settings.flv_player_width}" height="{$settings.flv_player_height}"
									flashvars="config={ldelim}
										'playlist': [
											{ldelim} 'url': '{$data.image_path}' {rdelim},
											{ldelim} 'url': 'rtmp://www.dev.thailadydatefinder.com/vod-devtldf/mp4:{$data.file_name}', 'provider': 'rtmp', 'autoPlay': false {rdelim}
										],
										'plugins': {ldelim}
											'rtmp': {ldelim}
												'url': 'http://releases.flowplayer.org/swf/flowplayer.rtmp-3.2.12.swf'
											{rdelim}
										{rdelim}
									{rdelim}"></embed>
							</object>
						{/if}
					{else}
						{if $smarty.const.VIDEO_PLAYER_PROGRESSIVE_DOWNLOAD == 'microsoft'}
							<OBJECT ID="MediaPlayer1" classid="CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95" CODEBASE="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,5,715" standby="Loading MicrosoftR WindowsR Media Player components..."  type="application/x-oleobject">
								<PARAM NAME="AutoStart" VALUE="True">
								<PARAM NAME="FileName" VALUE="{$data.file_path}">
								<PARAM NAME="ShowControls" VALUE="True">
								<PARAM NAME="ShowStatusBar" VALUE="true">
								<EMBED type="application/x-mplayer2"  pluginspage="http://www.microsoft.com/Windows/MediaPlayer/" SRC="{$data.file_path}" name="MediaPlayer1" autostart=1 showcontrols=0></EMBED>
							</OBJECT>
						{elseif $smarty.const.VIDEO_PLAYER_PROGRESSIVE_DOWNLOAD == 'HTML5_flowplayer_custom'}
							{if $data.html5_video}
								<video id="movie" width="{$settings.flv_player_width}" height="{$settings.flv_player_height}" poster="{$data.image_path}" preload="auto" controls="controls">
									<source src="{$data.file_path}" type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"' />
							{/if}
							{*<!--
								swf can also be loaded from:
								{$site_root}/javascript/flowplayer-3.2.16/flowplayer-3.2.16.swf
								{$site_root}/javascript/flowplayer-3.2.16/flowplayer.rtmp-3.2.12.swf
								wmode=transparent for immediately hiding flowplayer when colorbox is closed
							-->*}
							<object type="application/x-shockwave-flash" data="http://releases.flowplayer.org/swf/flowplayer-3.2.16.swf" width="{$settings.flv_player_width}" height="{$settings.flv_player_height}" id="FlowPlayer">
								<param name="movie" value="http://releases.flowplayer.org/swf/flowplayer-3.2.16.swf" />
								<param name="allowfullscreen" value="true" />
								<param name="allowScriptAccess" value="always" />
								<param name="quality" value="high" />
								<param name="bgcolor" value="#000000" />
								<param name="wmode" value="transparent" />
								{*<!-- not used when flowplayer is created with javascript
								<param name="scaleMode" value="showAll" />
								<param name="allowNetworking" value="all" />
								-->*}
								<param name="flashvars" value="config={ldelim}
									'playlist': [
										{ldelim} 'url': '{$data.image_path}' {rdelim},
										{ldelim} 'url': '{$data.file_path}', 'autoPlay': false, 'autoBuffering': true {rdelim}
									]
								{rdelim}" />
								<embed type="application/x-shockwave-flash" src="http://releases.flowplayer.org/swf/flowplayer-3.2.16.swf" width="{$settings.flv_player_width}" height="{$settings.flv_player_height}"
									flashvars="config={ldelim}
										'playlist': [
											{ldelim} 'url': '{$data.image_path}' {rdelim},
											{ldelim} 'url': '{$data.file_path}', 'autoPlay': false, 'autoBuffering': true {rdelim}
										]
									{rdelim}"></embed>
							</object>
							{if $data.html5_video}
								</video>
							{/if}
						{elseif $smarty.const.VIDEO_PLAYER_PROGRESSIVE_DOWNLOAD == 'mediaelement-js'}
							<video id="player1" width="{$settings.flv_player_width}" height="{$settings.flv_player_height}" src="{$data.file_path}" type="video/mp4" poster="{$data.image_path}" controls="controls" preload="auto"></video>
							<script type="text/javascript">
							$('audio,video').mediaelementplayer();
							</script>
						{/if}
					{/if}
				</td>
			</tr>
			<tr>
				<td align="center">{$data.user_comment}</td>
			</tr>
			<tr>
				<td align="center">
					<form method="post" action="">
						<label><input type="radio" name="videoplay" value="RTMP" onclick="this.form.submit();" {if $data.videoplay == 'RTMP'}checked="checked"{/if} />Streaming (RTMP)</label>
						<label><input type="radio" name="videoplay" value="download" onclick="this.form.submit();" {if $data.videoplay == 'download'}checked="checked"{/if} />Progressive Download</label>
					</form>
				</td>
			</tr>
		{/if}
		{if $data.button_type == 1}
			<tr height="40">
				<td class="main_header_text" align="center">
					{if $data.button_type == 1}
						<input type="button" value="{$button.back}" class="button" onclick="javascript:window.location.href='admin_user_upload.php?id={$data.id_user}&amp;type_upload={$data.type}';" />
					{else}
						<input type="button" value="{$button.close}" onclick="window.close();opener.focus();">
					{/if}
				</td>
			</tr>
		{/if}
	</table>
</div>
{include file="$admingentemplates/admin_bottom.tpl"}