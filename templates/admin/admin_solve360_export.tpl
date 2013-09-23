{include file="$admingentemplates/admin_top.tpl"}
<font class="red_header">
	{$header.razdel_name}
</font>
<font class="red_sub_header">
	&nbsp;|&nbsp;{$header.export_members}
</font>
<div class="help_text">
	<span class="help_title">{$lang.help}:</span>{$help.solve360_export}
</div>
<a href="../export/{$filename}">{$filename}</a>
<br><br>
{include file="$admingentemplates/admin_bottom.tpl"}