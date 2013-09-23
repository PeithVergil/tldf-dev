{include file="$gentemplates/index_top.tpl"}
 <div class="toc page-simple">
	<!-- begin main cell -->
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr valign="middle"><td height="25px" colspan=2>
			<div class="hdr2e">{$lang.section.advice}</div>
	</td></tr>
	{if $form.err}
		<tr><td colspan="2"><div class="error_msg">{$form.err}</div></td></tr>
	{/if}
	<tr><td>
		{if $categories}
		{section name=c loop=$categories}
			<div style="padding: 10px 0px 5px 0px"><a href="{$categories[c].item_link}">{$categories[c].name}</a></div>
			<div style="padding: 5px 0px 5px 0px" class="text">{$categories[c].descr}</div>
		{/section}
		{/if}
	</td></tr>

	</table>
	<!-- end main cell -->
</div>
{include file="$gentemplates/index_bottom.tpl"}