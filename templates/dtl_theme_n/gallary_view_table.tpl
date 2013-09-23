<!doctype html>
<html lang="{$default_lang}">
<head>
<meta charset="{$charset}">
<title>{$form.login}</title>
<script type="text/javascript" src="{$site_root}/javascript/jslibrary.js?v=0001"></script>
{literal}
<script type="text/javascript">
{/literal}{if $form.no_rating != '1'}{literal}
if (document.images) {
	var mark_show = new Image;
	mark_show.src = "{/literal}{$form.vote_icon_1_path}{literal}"
	var mark_hide = new Image;
	mark_hide.src = "{/literal}{$form.vote_icon_0_path}{literal}"
}

function marks(id,type)
{
	if (!document.images) {
		return false;
	}
	for (i = 1; i <= id; i++) {
		if (type == "show") {
			document.images["mark"+i].src=mark_show.src;
		} else if (type=="hide") {
			document.images["mark"+i].src=mark_hide.src;
		}
	}
	return;
}
{/literal}{/if}{literal}
</script>
{/literal}
<style type="text/css">
{literal}
.normal-btn { display: inline-block; padding: 2px 25px; background: #ff7a04 !important; border-radius: 5px; color: #fff !important; margin-right: 4px;}
{/literal}
</style>
</head>
<body {if $form.upload_type == 'f'}onload="resizeIM()"{/if}>
<table width="100%" cellpadding="0" cellspacing="0">
	{if $form.upload_type == 'f'}
		<tr><td align="center"><img src="{$form.image_path}"></td></tr>
	{else}
		<tr>
			<td align="center">
				{if $form.is_flv == 1}
					<object type="application/x-shockwave-flash" data="{$site_root}{$template_root}/flash/FlowPlayer.swf" width="320" height="240" id="FlowPlayer">
						<param name="allowScriptAccess" value="always" />
						<param name="movie" value="{$site_root}{$template_root}/flash/FlowPlayer.swf" />
						<param name="quality" value="high" />
						<param name="scaleMode" value="showAll" />
						<param name="allowfullscreen" value="false" />
						<param name="wmode" value="transparent" />
						<param name="allowNetworking" value="all" />
						{literal}
						<param name="flashvars" value="config={
							autoPlay: true,
							loop: false,
							initialScale: 'scale',
							showLoopButton: false,
							showPlayListButtons: false,
							showFullScreenButton: false,
							showMenu: false,
							playList: [
								{ url: '{/literal}{$form.image_path}{literal}' },
								{ url: '{/literal}{$form.file_path}{literal}' },
								{ url: '', type: 'swf' }
							]
							}" />
						{/literal}
					</object>
				{else}
					<OBJECT ID="MediaPlayer1" classid="CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95" CODEBASE="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,5,715" standby="Loading MicrosoftR WindowsR Media Player components..."  type="application/x-oleobject">
					<PARAM NAME="AutoStart" VALUE="True">
					<PARAM NAME="FileName" VALUE="{$form.file_path}">
					<PARAM NAME="ShowControls" VALUE="True">
					<PARAM NAME="ShowStatusBar" VALUE="true">
					<EMBED type="application/x-mplayer2"  pluginspage="http://www.microsoft.com/Windows/MediaPlayer/" SRC="{$form.file_path}" name="MediaPlayer1" autostart=1 showcontrols=0></EMBED>
					</OBJECT>
				{/if}
			</td>
		</tr>
	{/if}
	<tr>
		<td align="center" style="padding-top: 3px; font-family: Tahoma; font-size: 11px;">{$form.comment}</td>
	</tr>
	{if $form.no_rating == '1'}
		<tr>
			<td align="center" style="padding-top: 3px; {if $form.no_rating == 1} display:none; {else} display:inline; {/if}" id="vote_marks">
				<table border="0" cellspacing="0" cellpadding="0">
					<tr align="center">
						{foreach from=$vote_arr item=item}
						<td>
							<a onclick="marks({$item},'show'); VoteAction('{$form.id_upload}', '{$item}', '{$form.id_category}', document.getElementById('active_section'),'v'); document.getElementById('vote_marks').style.display = 'none';" href="#">
							<img id="mark{$item}" name="mark{$item}" src="{$form.vote_icon_0_path}" onmouseover="marks({$item},'show');" onmouseout="marks({$item},'hide');" border="0">
							</a>
						</td>
						{/foreach}
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align="center" style="padding-top: 3px;" class="text">
				<div id="active_section"></div>
			</td>
		</tr>
	{/if}
	<tr>
		<td align="center" style="padding-top:3px;">
			<input type="button" class="normal-btn" onclick="window.opener.document.location.href='gallary.php?sel=category&id_category={$form.id_category}&upload_type={$form.upload_type}'; window.close();" value="{$lang.button.rate_all}">
			&nbsp;&nbsp;&nbsp;
			<input type="button" class="normal-btn" onclick="window.close();" value="{$lang.button.close}">
		</td>
	</tr>
</table>
{literal}
<script type="text/javascript">
function resizeIM() {
	el = document.images[0];
	height= el.height + 150;
	width = el.width + 50;
	window.resizeTo(width, height);
}
</script>
{/literal}
</body>
</html>