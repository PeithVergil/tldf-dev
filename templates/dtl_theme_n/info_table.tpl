{include file="$gentemplates/index_top.tpl"}
<div class="toc page-simple">
	<!-- begin main Content -->      
	<h2 class="hdr1">{$data.name}</h2>
	{if $data.name == "Testimonials"}
		<a href="{$site_root}/send_testimonial.php" title="Send Testimonial" class="send_testimonial_btn">&nbsp;</a>
	{/if}        
	<div class="article">        
		{$data.content}
	</div>  
	<!-- end main cell -->
</div>
{include file="$gentemplates/index_bottom.tpl"}