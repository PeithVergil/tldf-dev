{include file="$gentemplates/index_top.tpl"}
 <div class="toc page-simple">
	<!-- begin main cell -->
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr valign="middle">
		<td height="25px" colspan=2>
			<div class="hdr2e">{$lang.section.advice}: {$category.category}</div>
		</td>
	</tr>
	{if $form.err}
	<tr><td colspan="2"><div class="error_msg">{$form.err}</div></td></tr>
	{/if}
	{if $advices}
	<tr><td>
		<div style="padding: 10px 0px 10px 0px">
		{section name=a loop=$advices}
			<div style="padding: 0px 0px 10px 0px" class="link">&raquo;&nbsp;&nbsp;<a href="#{$advices[a].id}">{$advices[a].title}</a></div>
		{/section}
		</div>
		{section name=a loop=$advices}
			<div style="padding: 10px 0px 5px 0px"><a name="{$advices[a].id}" class="text_head">{$advices[a].title}</a></div>
			<div style="padding: 5px 0px 5px 0px">{$advices[a].body}</div>
		{/section}
	</td></tr>
	{/if}
	<tr><td align="right">
		<input type="button" class="button" onclick="javascript: location='advice.php'" value="{$button.back}">
	</td></tr>
	</table>
	<!-- end main cell -->
</div>
{include file="$gentemplates/index_bottom.tpl"}