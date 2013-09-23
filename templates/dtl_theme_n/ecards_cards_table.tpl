{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple my-ecard">
	<div class="content" style="padding: 15px;">
		<div style="margin: 0px; ">
			<div class="hdr2">{$lang.cards.cards_in}&nbsp;{$form.subcategory_name}</div>
			<div style="padding-top: 10px;">
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td><img src="{$site_root}{$template_root}/images/btn_back.gif" alt="back"></td>
						<td><a href="ecards.php?{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}">{$lang.cards.back_to_categories_list}</a></td>
						<td style="padding-left: 20px;"><img src="{$site_root}{$template_root}/images/btn_back.gif" alt="back"></td>
						<td><a href="ecards.php?sel=category&amp;id_category={$form.id_category}{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}">{$lang.cards.back_to}&nbsp;{$form.category_name}&nbsp;{$lang.cards.category}</a></td>
						<td style="padding-left: 20px;"><img src="{$site_root}{$template_root}/images/my_basket_icon.gif" alt="back"></td>
						<td>
							{if $data.id_user_to && $smarty.get.fixuser}
								<a href="ecards.php?sel=my_orders{if $data.id_order}&amp;id_order={$data.id_order}{/if}&amp;id_user_to={$data.id_user_to}&amp;fixuser=Y">{$lang.cards.my_orders_to} {$data.user_to_fname}</a>
							{else}
								<a href="ecards.php?sel=my_orders{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}">{$lang.cards.my_orders}</a>
							{/if}
						</td>
						{if $data.id_order}
							<td style="padding-left: 20px;"><img src="{$site_root}{$template_root}/images/btn_back.gif" alt="back"></td>
							<td><a href="ecards.php?sel=card&amp;id_order={$data.id_order}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}">{$lang.cards.back_to} {$button.edit_card}</a></td>
						{/if}
					</tr>
				</table>
			</div>
			<div style="padding-top: 15px;">
				<b>{$form.subcategory_descr}</b>
			</div>
			<div style="padding-top: 5px; padding-bottom: 10px;">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
				{section name=c loop=$cards}
					{if $smarty.section.c.index is div by 3}<tr>{/if}
					<td style="padding-top: 15px;" valign="top" width="33%">
						<table cellpadding="0" cellspacing="0">
							<tr>
								<td valign="top">
									<a href="#" onclick="return GB_showImage('{$cards[c].name_unslashed}', '{$cards[c].card_image_big}')"><img src="{$cards[c].card_image}" width="100" height="100" alt="" border="0"></a>
								</td>
								<td style="padding-left: 15px;" valign="top">
									<div><a href="ecards.php?sel=card&amp;id_card={$cards[c].id}{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}"><b>{$cards[c].name}</b></a></div>
									{if $cards[c].card_price_raw > 0 || $smarty.const.MM_ECARDS_FREE == 0}
										<div style="padding-top: 5px;">
											<b>{$lang.cards.price}</b>:<font class="text">{$cards[c].card_price}&nbsp;{$form.cur}</font>
										</div>
									{/if}
								</td>
								<td width="10">&nbsp;</td>
							</tr>
						</table>
					</td>
					{if $smarty.section.c.index_next is div by 3 || $smarty.section.c.last}</tr>{/if}
				{/section}
				</table>
			</div>
			{if $links}
				<div style="margin-left: 0px; padding-top: 15px;">
					{foreach item=item from=$links}
						<span style="padding-right: 15px;"><a href="{$item.link}" {if $item.selected eq '1'} class="text_head"{/if}>{$item.name}</a></span>
					{/foreach}
				</div>
			{/if}
		</div>
	</div>
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}