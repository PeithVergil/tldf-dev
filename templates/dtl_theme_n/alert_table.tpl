{include file="$gentemplates/index_top.tpl"}
<div class="toc page-simple">
	<!-- begin main cell -->
	{if $form.err}
		<div class="error_msg">{$form.err}</div>
	{/if}
	{if $group || $form.register_link || $form.homepage_link}
		<div class="hdr2">{$form.table_alert_header}</div>
		<div>
			{section name=f loop=$group}
				<a href="{$group[f].credit_link}">{$group[f].name}</a><br>
			{/section}
			{if $form.register_link}
				<font class="text"> {$header.if_not_registered}<a href="{$form.register_link}">{$header.register}</a> </font><br><br>
				<font class="text"> {$header.if_registered}<a href="{$form.login_link}">{$header.login}</a> </font><br>
			{/if}
			{if $form.homepage_link}
				{if $form.alert_header_confirm}
					<font class="text"> {$form.alert_header_confirm}</font><br><br>
				{/if}
				<font class="text"> <a href="{$form.homepage_link}">{$lang.home}</a> </font><br><br>
				<font class="text"> <a href="{$form.logoff_link}">{$lang.logoff}</a> </font><br>
			{/if}
		</div>
	{/if}
	<!-- end main cell -->
</div>
{include file="$gentemplates/index_bottom.tpl"}