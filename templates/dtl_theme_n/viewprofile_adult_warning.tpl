<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Language" content="en-us">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="expires" content="0">
	<meta http-equiv="pragma" content="no-cache">
	<meta name="revisit-after" content="3 days">
	<meta name="robot" content="All">
	<meta name="Description" content="{$lang.description}">
	<meta name="Keywords" content="{$lang.keywords}">
	<title>{$lang.main_title}</title>
	<link href="{$site_root}/css.php" rel="stylesheet" type="text/css">
</head>
<body>
{if $not_allow eq 1}
	<div style="padding-top: 15px;" align="center">
    	<div class="error_msg">{$lang.adult_not_allow_text}</div>
    </div>
    <div align="center" style="padding-top: 10px;">
        <input type="button" value="{$lang.button.close}" onClick="window.close();">
    </div>
{else}
<form method="post" action="viewprofile.php">
<input type="hidden" name="id_file" id="id_file" value="{$id_file}">
<input type="hidden" name="sel" id="sel" value="agreement">
<input type="hidden" name="gallary" id="gallary" value="{$gallary}">
<input type="hidden" name="upload_type" id="upload_type" value="{$upload_type}">
<div style="padding-top: 15px;" align="center">
	<div class="error_msg">{$lang.adult_warning_text}</div>
</div>
<div align="center" style="padding-top: 10px;">
<table cellpadding="0" cellspacing="0">
<tr>
	<td><input type="radio" name="agree" value="yes"></td>
	<td>&nbsp;{$lang.i_agree}</td>
	<td style="padding-left: 15px;"><input type="radio" name="agree" value="no"></td>
	<td>&nbsp;{$lang.i_not_agree}</td>
</tr>
</table>
</div>
<div align="center" style="padding-top: 10px;">
	<input type="submit" value="{$lang.button.confirm}">
</div>
</form>
{/if}
</body>
</html>