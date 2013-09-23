{strip}
{include file="$gentemplates/index_top.tpl"}
<div class="toc page-simple2 my-store">
	<div class="clear">
		{if $form.subpage == 'view_categories'}
			<h1 class="hdr1">{$lang.section.giftshop}</h1>
			<div class="_fright my-store-nav">
				<div><a href="help.php?sel=list_item&amp;id=8" class="link"><b>{$lang.giftshop.faq}</b></a> | 
				<a href="giftshop.php?sel=wishlist" class="link"><b>{$lang.giftshop.wish_list}</b></a> | 
				<a href="giftshop.php?sel=order_history" class="link"><b>{$lang.giftshop.section_orders}</b></a> | 
				<a href="payment.php?sel=update_account" class="link"><b>{$lang.giftshop.add_credits}</b></a> | 
				<a href="giftshop.php?sel=view_basket" class="link"><b>{$lang.giftshop.section_basket}</b></a></div>
				<div><span class="text">{$lang.giftshop.basket_count_all}:</span> <span class="text_head">{$basket_info.total_quantity}</span> | 
				<span class="text">{$lang.giftshop.basket_total}:</span> <span class="text_head">{$basket_info.total_amount_format} {$form.currency}</span></div>
			</div>
		{else}
			<h2 class="hdr2">{$lang.section.giftshop}</h2>
			<div class="back_to">
				<a href="giftshop.php">{$lang.giftshop.back_to_categories}</a> 
				{if $form.subpage == 'view_item'}
					<span><img src="{$site_root}{$template_root}/images/btn_back.gif" border="0" alt="" style="vertical-align:middle;"> 
					<a href="giftshop.php?sel=items&amp;category={$category_active.id}">{$lang.giftshop.back_to_items}</a></span>
				{/if}
			</div>
			
			{* if $form.subpage != 'view_basket' *}
				<div class="_fright my-store-nav">
					<div><a href="help.php?sel=list_item&amp;id=8" class="link"><b>{$lang.giftshop.faq}</b></a> | 
					<a href="giftshop.php?sel=wishlist" class="link"><b>{$lang.giftshop.wish_list}</b></a> | 
					<a href="giftshop.php?sel=order_history" class="link"><b>{$lang.giftshop.section_orders}</b></a> | 
					<a href="payment.php?sel=update_account" class="link"><b>{$lang.giftshop.add_credits}</b></a> | 
					<a href="giftshop.php?sel=view_basket" class="link"><b>{$lang.giftshop.section_basket}</b></a></div>
					<div><span class="text">{$lang.giftshop.basket_count_all}:</span> <span class="text_head">{$basket_info.total_quantity}</span> | 
					<span class="text">{$lang.giftshop.basket_total}:</span> <span class="text_head">{$basket_info.total_amount_format} {$form.currency}</span></div>
				</div>
			{* /if *}
		{/if}
	</div>
	{if $form.err}
		<div class="error_msg">{$form.err}</div>
	{/if}
</div>
<div>
	{include file="$gentemplates/giftshop_content.tpl"}
</div>
{include file="$gentemplates/index_bottom.tpl"}
{/strip}