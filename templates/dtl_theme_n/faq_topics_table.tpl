{include file="$gentemplates/index_top.tpl"}
{strip}

<div class="toc page-simple">
	<!-- begin main cell -->
        <h1 class="hdr1">{$lang.section.help}&nbsp;({$lang.faq.by_topic})</h1>

	{if $form.err}
	<div class="error_msg">{$form.err}</div>
	{/if}
	<dl class="faq">
		{if $topics}
		{section name=c loop=$topics}
			<dt><a href="{$topics[c].item_link}">{$topics[c].name}</a></dt>
			<dd>{$topics[c].descr}</dd>
		{/section}
		{/if}
	</dl>
	<!-- end main cell -->
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}