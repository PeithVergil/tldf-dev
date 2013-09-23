{include file="$gentemplates/index_top_popup.tpl"}
{*strip*}
			<td colspan="2" align="left">
				<div align="center">
					{if $data.upload_type == "f"}
						{if $data.not_user == 1}
							<div align="center">
								<font class="link"><b>{$data.login}</b></font>&nbsp;&nbsp;
								<font class="text">{$lang.users.age}</font>:&nbsp;
								<font class="text_head">{$data.age}&nbsp;{$lang.home_page.ans}</font>
								{if $data.country},&nbsp;<font class="text">{$data.country}</font>{/if}
							</div>
							<div style="height:1px; margin:10px 0px;" class="delimiter"></div>
						{/if}
						<div align="center" style="height:{$sizes[1]+10}px;">
							<table cellpadding="0" cellspacing="0" align="center" border="0">
								<tr>
									{section loop=$data.photos name=p}
										<td align="center" width="{$sizes[0]+20}">
											<img id="image_{$data.photos[p].id}" src="{$data.photos[p].thumb_file}" {if $data.photos[p].sel}style="cursor:pointer; border:solid 5px #{$css_color.home_search};"{else}style="cursor:pointer; border:solid 2px #{$css_color.content};"{/if} width="{$sizes[0]}" height="{$sizes[1]}" alt="" onclick="ChangeBorder(this); ImageShow('{$data.photos[p].id}', document.getElementById('img_dest'), '{$view}');" align="middle">
										</td>
									{/section}
								</tr>
							</table>
						</div>
						<div style="height:1px; margin:10px 0px;" class="delimiter"></div>
						<div align="center" id="img_dest">
							<table cellpadding="0" cellspacing="0" align="center">
								<tr>
									<td><img border="1" bordercolor="1" src="{$data.file_path}" alt=""></td>
								</tr>
								<tr>
									<td align="center">{$data.user_comment}</td>
								</tr>
							</table>
						</div>
					{else}
						{*<!-- VIDEO -->*}
						<div align="center" style="padding:5px;">
							{if $data.videoplay == 'RTMP'}
								{if $smarty.const.VIDEO_PLAYER_RTMP == 'flowplayer_scripted'}
									{*<!--
										swf can also be loaded from:
										{$site_root}/javascript/flowplayer-3.2.16/flowplayer-3.2.16.swf
										{$site_root}/javascript/flowplayer-3.2.16/flowplayer.rtmp-3.2.12.swf
										rtmp falls back to rtmpt automatically
										wmode=transparent for immediately hiding flowplayer when colorbox is closed
									-->*}
									<a id="player" style="display:block; width:{$settings.flv_player_width}px; height:{$settings.flv_player_height}px"></a>
									<script type="text/javascript">
										flowplayer('player', {ldelim}
												'src' : 'http://releases.flowplayer.org/swf/flowplayer-3.2.16.swf',
												'wmode' : 'transparent'
											{rdelim},
											{ldelim}
												'playlist': [
													{ldelim} 'url': '{$data.image_path}' {rdelim},
													{ldelim} 'url': 'rtmp://{$smarty.server.SERVER_NAME}/vod-{if $smarty.const.IS_DEV_SERVER}dev{/if}tldf/mp4:{$data.file_name}', 'provider': 'rtmp', 'autoPlay': false {rdelim}
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
										swf can also be loaded from:
										{$site_root}/javascript/flowplayer-3.2.16/flowplayer-3.2.16.swf
										{$site_root}/javascript/flowplayer-3.2.16/flowplayer.rtmp-3.2.12.swf
										rtmp falls back to rtmpt automatically
										wmode=transparent for immediately hiding flowplayer when colorbox is closed
									-->*}
									<object width="{$settings.flv_player_width}" height="{$settings.flv_player_height}" type="application/x-shockwave-flash" data="http://releases.flowplayer.org/swf/flowplayer-3.2.16.swf">
										<param name="movie" value="http://releases.flowplayer.org/swf/flowplayer-3.2.16.swf">
										<param name="allowfullscreen" value="true">
										<param name="allowScriptAccess" value="always">
										<param name="quality" value="high">
										<param name="bgcolor" value="#000000">
										<param name="wmode" value="transparent">
										{*<!-- not used when flowplayer is created with javascript
										<param name="scaleMode" value="showAll">
										<param name="allowNetworking" value="all">
										-->*}
										<param name="flashvars" value="config={ldelim}
											'playlist': [
												{ldelim} 'url': '{$data.image_path}' {rdelim},
												{ldelim} 'url': 'rtmp://{$smarty.server.SERVER_NAME}/vod-{if $smarty.const.IS_DEV_SERVER}dev{/if}tldf/mp4:{$data.file_name}', 'provider': 'rtmp', 'autoPlay': false {rdelim}
											],
											'plugins': {ldelim}
												'rtmp': {ldelim}
													'url': 'http://releases.flowplayer.org/swf/flowplayer.rtmp-3.2.12.swf'
												{rdelim}
											{rdelim}
										{rdelim}">
										<embed type="application/x-shockwave-flash" src="http://releases.flowplayer.org/swf/flowplayer-3.2.16.swf" width="{$settings.flv_player_width}" height="{$settings.flv_player_height}"
											flashvars="config={ldelim}
												'playlist': [
													{ldelim} 'url': '{$data.image_path}' {rdelim},
													{ldelim} 'url': 'rtmp://{$smarty.server.SERVER_NAME}/vod-{if $smarty.const.IS_DEV_SERVER}dev{/if}tldf/mp4:{$data.file_name}', 'provider': 'rtmp', 'autoPlay': false {rdelim}
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
											<source src="{$data.file_path}" type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"'>
									{/if}
									{*<!--
										swf can also be loaded from:
										{$site_root}/javascript/flowplayer-3.2.16/flowplayer-3.2.16.swf
										{$site_root}/javascript/flowplayer-3.2.16/flowplayer.rtmp-3.2.12.swf
										wmode=transparent for immediately hiding flowplayer when colorbox is closed
									-->*}
									<object type="application/x-shockwave-flash" data="http://releases.flowplayer.org/swf/flowplayer-3.2.16.swf" width="{$settings.flv_player_width}" height="{$settings.flv_player_height}" id="FlowPlayer">
										<param name="movie" value="http://releases.flowplayer.org/swf/flowplayer-3.2.16.swf">
										<param name="allowfullscreen" value="true">
										<param name="allowScriptAccess" value="always">
										<param name="quality" value="high">
										<param name="bgcolor" value="#000000">
										<param name="wmode" value="transparent">
										{*<!-- not used when flowplayer is created with javascript
										<param name="scaleMode" value="showAll">
										<param name="allowNetworking" value="all">
										-->*}
										<param name="flashvars" value="config={ldelim}
											'playlist': [
												{ldelim} 'url': '{$data.image_path}' {rdelim},
												{ldelim} 'url': '{$data.file_path}', 'autoPlay': false, 'autoBuffering': true {rdelim}
											]
										{rdelim}">
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
						</div>
						<div align="center">{$data.user_comment}</div>
						<div style="margin-top:10px;">
							<form method="post" action="">
								<label><input type="radio" name="videoplay" value="RTMP" onclick="this.form.submit();" {if $data.videoplay == 'RTMP'}checked="checked"{/if}>Streaming (RTMP)</label>
								<label><input type="radio" name="videoplay" value="download" onclick="this.form.submit();" {if $data.videoplay == 'download'}checked="checked"{/if}>Progressive Download</label>
							</form>
						</div>
					{/if}
				</div>
			</td>
		</tr>
	</table>
</div>
{*/strip*}
{literal}
<script type="text/javascript">
var req = null;

function InitXMLHttpRequest()
{
	if (window.XMLHttpRequest) {
		req = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		req = new ActiveXObject("Microsoft.XMLHTTP");
	}
}

function ChangeBorder(obj)
{
	{/literal}
	{section loop=$data.photos name=p}
		document.getElementById('image_{$data.photos[p].id}').style.border = "solid 2px #{$css_color.content}";
	{/section}
	{literal}
	obj.style.border = "solid 5px {/literal}#{$css_color.home_search}";{literal}
	return;
}

function ImageShow(image_id, destination, view)
{
	InitXMLHttpRequest();
	// Load the result from the response page
	if (req) {
		req.onreadystatechange = function() {
			if (req.readyState == 4) {
				destination.innerHTML = req.responseText;
			} else {
				destination.innerHTML = "Loading image...";
			}
		}
		if (view  == '1') {
			req.open("GET", "viewprofile.php?sel=ajax_image&id_file="+image_id, true);
		} else {
			req.open("GET", "myprofile.php?sel=ajax_image&id_file="+image_id, true);
		}
		req.send(null);
	} else {
		destination.innerHTML = 'Browser unable to create XMLHttp Object';
	}
}
</script>
{/literal}
</body>
</html>