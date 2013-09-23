<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="content-language" content="en">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Video</title>
</head>
<body>
<table cellspacing="0" cellpadding="5" border="1" width="100%" height="580">
	<tr>
		<td align="center" valign="middle">
			{if $is_flv == 1}
				<object type="application/x-shockwave-flash" data="{$site_root}{$template_root}/flash/FlowPlayer.swf" width="320" height="240" id="FlowPlayer">
					<param name="allowScriptAccess" value="always" />
					<param name="movie" value="{$site_root}{$template_root}/flash/FlowPlayer.swf" />
					<param name="quality" value="high" />
					<param name="scaleMode" value="showAll" />
					<param name="allowfullscreen" value="false" />
					<param name="wmode" value="transparent" />
					<param name="allowNetworking" value="all" />
					<param name="flashvars" value="config={ldelim}
						autoPlay: true,
						loop: false,
						initialScale: 'scale',
						showLoopButton: false,
						showPlayListButtons: false,
						showFullScreenButton: false,
						showMenu: false,
						playList: [
						{ldelim} url: '{$data.image_path}' {rdelim},
						{ldelim} url: '{$data.file_path}' {rdelim},
						{ldelim} url: '', type: 'swf' {rdelim}
						]
					{rdelim}" />
				</object>
			{else}
				<object id="MediaPlayer1" width="700" height="550"
					classid="CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95" 
					codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,5,715"
					standby="Loading MicrosoftR WindowsR Media Player components..." 
					type="application/x-oleobject">
					<param name="FileName" value="{$data.file_path}">
					<param name="AutoStart" value="false">
					<param name="AutoSize" value="true">
					<param name="StretchToFit" value="false">
					<param name="DisplaySize" value="false">
					<param name="ShowControls" value="false">
					<param name="ShowStatusbar" value="false">
					<param name="ShowDisplay" value="false">
					<param name="FullScreen" value="false">
					<embed name="MediaPlayer1" type="application/x-mplayer2" pluginspage="http://www.microsoft.com/Windows/MediaPlayer/"
						width="700" height="550" src="{$data.file_path}" 
						AutoStart="0" AutoSize="1" StretchToFit="0" DisplaySize="0" ShowControls="1" ShowStatusBar="0" ShowDisplay="0" FullScreen="0">
					</embed>
				</object>
			{/if}
		</td>
	</tr>
</table>
</body>
</html>