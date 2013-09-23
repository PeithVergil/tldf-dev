{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple my-ecard">
	<div style="padding-top: 10px;">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td><img src="{$site_root}{$template_root}/images/my_basket_icon.gif" alt="order list"></td>
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
	{if $most_ordered}
		<div>
			<div style="margin: 0px; padding-top: 10px;">
				<div class="hdr2">{$lang.cards.most_popular}</div>
				<div style="padding-top: 15px;">
					<table border="0" cellpadding="0" cellspacing="0">
					<tr>
					{foreach item=item from=$most_ordered}
						<td valign="top" width="150">
							<div><a href="ecards.php?sel=card&amp;id_card={$item.item_id}{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}"><img src="{$item.card_image_thumb}" width="100" height="100" border="0" alt=""></a></div>
							<div align="justify" style="padding-top: 5px; width: 100px;"><font class="text">{$item.card_name}</font></div>
						</td>
						<td width="15"> </td>
					{/foreach}
					</tr>
					</table>
				</div>
			</div>
		</div>
	{/if}
	<div class="content" style="margin-top: 12px; padding: 15px;">
		<div class="hdr2">{$lang.cards.ecards_categories}</div>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td style="padding-top: 15px;">
					<table border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="31%" valign="top">
							{section name=c loop=$categories start=0 max=$form.categories_1_limit}
							<table cellpadding="0" cellspacing="0">
							<tr>
								<td valign="top"><a href="ecards.php?sel=category&amp;id_category={$categories[c].id}{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}"><img src="{$categories[c].card_image}" width="100" height="100" border="0" alt="" ></a></td>
								<td valign="top" style="padding-left: 10px;">
									<div>
										<span><a href="ecards.php?sel=category&amp;id_category={$categories[c].id}{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}" style="font-size: 14px;"><b>{$categories[c].name}</b></a></span>
										<span style="padding-left: 15px;"><font class="text_hidden">{$categories[c].cards_count} {$lang.cards.cards}</font></span>
									</div>
									<div style="line-height: 16px; padding-top: 3px;">
									{foreach item=item from=$categories[c].subcategories}
										<a href="ecards.php?sel=cards&amp;id_category={$categories[c].id}&amp;id_subcategory={$item.id}{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}">{$item.name}</a>   
									{/foreach}
									</div>
								</td>
							</tr>
							<tr>
								<td colspan="2" height="20"> </td>
							</tr>
							</table>
							{/section}
						</td>
						<td width="2%"> </td>
						<td width="31%" valign="top">
							{section name=c loop=$categories start=$form.categories_2_start max=$form.categories_2_limit}
							<table cellpadding="0" cellspacing="0">
							<tr>
								<td valign="top"><a href="ecards.php?sel=category&amp;id_category={$categories[c].id}{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}"><img src="{$categories[c].card_image}" width="100" height="100" border="0" alt=""></a></td>
								<td valign="top" style="padding-left: 10px;">
									<div>
										<span><a href="ecards.php?sel=category&amp;id_category={$categories[c].id}{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}" style="font-size: 14px;"><b>{$categories[c].name}</b></a></span>
										<span style="padding-left: 15px;"><font class="text_hidden">{$categories[c].cards_count} {$lang.cards.cards}</font></span>
									</div>
									<div style="line-height: 16px; padding-top: 3px;">
									{foreach item=item from=$categories[c].subcategories}
										<a href="ecards.php?sel=cards&amp;id_category={$categories[c].id}&amp;id_subcategory={$item.id}{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}">{$item.name}</a>   
									{/foreach}
									</div>
								</td>
							</tr>
							<tr>
								<td colspan="2" height="20"> </td>
							</tr>
							</table>
							{/section}
						</td>
						<td width="2%"> </td>
						<td width="31%" valign="top">
							{section name=c loop=$categories start=$form.categories_3_start}
							<table cellpadding="0" cellspacing="0">
								<tr>
									<td valign="top"><a href="ecards.php?sel=category&amp;id_category={$categories[c].id}{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}"><img src="{$categories[c].card_image}" width="100" height="100" border="0" alt=""></a></td>
									<td valign="top" style="padding-left: 10px;">
										<div>
											<span><a href="ecards.php?sel=category&amp;id_category={$categories[c].id}{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}" style="font-size: 14px;"><b>{$categories[c].name}</b></a></span>
											<span style="padding-left: 15px;"><font class="text_hidden">{$categories[c].cards_count} {$lang.cards.cards}</font></span>
										</div>
										<div style="line-height: 16px; padding-top: 3px;">
										{foreach item=item from=$categories[c].subcategories}
											<a href="ecards.php?sel=cards&amp;id_category={$categories[c].id}&amp;id_subcategory={$item.id}{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}">{$item.name}</a>   
										{/foreach}
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="2" height="20"> </td>
								</tr>
							</table>
							{/section}
						</td>
					</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}