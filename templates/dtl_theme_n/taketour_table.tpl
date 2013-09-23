{include file="$gentemplates/index_top_popup.tpl"}	
	<td width="100%" valign=middle>
		<br><br>
		<form name="taketour" action="">
		<table width="100%" cellspacing=0 cellpadding=0>
		<tr>
			<td height="20" align="center" colspan=2>&nbsp;</td>
		</tr>
		{if $data.file_type == 'p'}
	        <tr valign="top">
			<td align="center" colspan=2><img src="{$data.file_path}" border=1 alt=""></td>
	        </tr>
		{elseif $data.file_type == 'f'}
	        <tr valign="top">
			<td align="center" colspan=2>
			<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.#version=5,0,30,0" height="300" width="300">
				<param name="movie" value="{$data.file_path}">
				<param name="quality" value="best">
				<param name="play" value="true">
				<embed height="300" width="300" pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" src="{$data.file_path}" type="application/x-shockwave-flash" quality="best" play="true"> 
			</object>
			</td>
		</tr>
		{elseif $data.file_type == 'a' || $data.file_type == 'v'}
		<tr valign="top">
			<td align="center"  colspan=2>
				<object id="mediaplayer1" classid="clsid:22d6f312-b0f6-11d0-94ab-0080c74c7e95" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#version=6,4,5,715" standby="loading microsoftr windowsr media player components..."  type="application/x-oleobject">
					<param name="autostart" value="false">
					<param name="filename" value="{$data.file_path}">
					<param name="showcontrols" value="true">
					<param name="showstatusbar" value="false">
					<embed type="application/x-mplayer2"  pluginspage="http://www.microsoft.com/windows/mediaplayer/" src="{$data.file_path}" name="mediaplayer1" autostart=1 showcontrols=0></embed>
				</object> 
			</td>
		</tr>
		{/if}
		{if !$data.comment}
		<tr>
			<td colspan="2"><div class="error_msg">{$header.empty}</div></td>
		</tr>
		{else}
		<tr>
			<td colspan=2 align="center" class="text">&nbsp;{$data.comment}&nbsp;</td>
		</tr>
		{/if}
		<tr>
			<td height="20" align="left">&nbsp;{if $data.prev_link}<input type="button" name="prev" value="{$header.prev}" onclick="javascript:location.href='{$data.prev_link}'">{/if}&nbsp;</td>
			<td height="20" align="right">&nbsp;{if $data.next_link}<input type="button" name="next" value="{$header.next}" onclick="javascript:location.href='{$data.next_link}'">{/if}&nbsp;</td>
		</tr>
		</table>
		</form>
	</td>
</tr>
<tr><td height="30%">&nbsp;</td></tr>
</table>
</div>
</body>
</html>