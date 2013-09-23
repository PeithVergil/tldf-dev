<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Language" content="{$default_lang}">
<meta http-equiv="Content-Type" content="text/html; charset={$charset}">
<meta http-equiv="expires" content="0">
<meta http-equiv="pragma" content="no-cache">
<meta name="revisit-after" content="3 days">
<meta name="robot" content="All">
<meta name="Description" content="{$lang.description}">
<meta name="Keywords" content="{$lang.keywords}">
<title>{$lang.club.upload_by} {$data.login}</title>
{if $script}
	<script type="text/javascript" src="{$site_root}{$template_root}/js/{$script}.js"></script>
{/if}
<link href="{$site_root}/css.php" rel="stylesheet" type="text/css">
</head>
<body onload="javascript:resizeIM()" style="margin:10;">
<table width="100%" cellpadding="3" cellspacing="0">
<tr><td align="center"><img border=1 bordercolor=1 src="{$data.file_path}" alt=""></td></tr>
<tr><td align="center" style="font-family: Tahoma; font-size: 11px;">{$data.comment}</td></tr>
<tr><td align="center"><input type="button" class="button" onclick="javascript: opener.focus(); window.close();" value="{$button.close}"></td></tr>
</table>
{literal}
<script language="JavaScript" type="text/javascript">
function resizeIM(){
	el = document.images[0];
	height= el.height+120;
	width = el.width+50;
	window.resizeTo(width, height);
}
</script>
{/literal}
</body>
</html>