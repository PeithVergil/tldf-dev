{strip}
<ul>
{assign var='sep' value=false}

{foreach item=item from=$bottom_menu_info}
	<li><a href="{$site_root}{$item.link}">{$item.name}</a><span>|</span></li>
	
{/foreach}
{if $use_success_stories}
	<li><a href="{$site_root}/success_stories.php">{$lang.bottom.stories}</a><span>|</span></li>
	&nbsp;&nbsp;|&nbsp;&nbsp;
{/if}
			<li><a href="{$site_root}/help.php">{$lang.bottom.faq}</a><span>|</span></li>
						
			<li><a href="{$site_root}/report_a_bug.php" >{$lang.bottom.report_a_bug}</a><span>|</span></li>
							
			<li><a href="{$site_root}/request_call_back.php" >{$lang.bottom.request_call_back}</a><span>|</span></li>
							
			<li><a href="{$site_root}/send_feedback.php">{$lang.bottom.send_feedback}</a><span>|</span></li>
							
			<li><a href="{$site_root}/contact.php">{$lang.bottom.contact}</a><span>|</span></li>
								
			<li><a href="{$site_root}/map.php">{$lang.bottom.map}</a></li>
	{if $use_pilot_module_affiliate}
	    &nbsp;&nbsp;|&nbsp;&nbsp;
	    <a href="{$site_root}/affiliate/aff_index.php">{$lang.bottom.affiliate}</a>
	{/if}
</ul>														
{/strip}

