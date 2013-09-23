{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple my-ecard">
	<div class="content" style="padding: 15px;">
		<div style="margin: 0px; ">
			<div class="hdr2">{$lang.cards.subcategories_in}  {$form.category_name}</div>
			<div style="padding-top: 10px;">
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td><img src="{$site_root}{$template_root}/images/btn_back.gif" alt="back"></td>
						<td><a href="ecards.php?{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}">{$lang.cards.back_to_categories_list}</a></td>
						<td style="padding-left: 20px;"><img src="{$site_root}{$template_root}/images/my_basket_icon.gif" alt="order list"></td>
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
				<b>{$form.category_descr}</b>
			</div>
			<div style="padding-top: 20px;">
				<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="49%" valign="top">
						<table cellpadding="0" cellspacing="0" width="100%">
						{section name=c loop=$subcategories start=0 max=$form.categories_1_limit}
							<tr>
								<td width="115" valign="top">
									<a href="ecards.php?sel=cards&amp;id_category={$form.id_category}&amp;id_subcategory={$subcategories[c].id}{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}"><img src="{$subcategories[c].image}" width="100" height="100" border="0" alt=""></a>
								</td>
								<td valign="top">
									<div>
										<span><a href="ecards.php?sel=cards&amp;id_category={$form.id_category}&amp;id_subcategory={$subcategories[c].id}{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}" style="font-size: 14px;"><b>{$subcategories[c].name}</b></a></span>
										<span style="padding-left: 15px;"><font class="text_hidden">{$subcategories[c].cards_count} {$lang.cards.cards}</font></span>
									</div>
									<div style="line-height: 16px; padding-top: 3px;">{$subcategories[c].descr}</div>
								</td>
							</tr>
							<tr>
								<td height="15">  </td>
							</tr>
						{/section}
						</table>
					</td>
					<td width="2%">  </td>
					<td width="49%" valign="top">
						<table cellpadding="0" cellspacing="0" width="100%">
						{section name=c loop=$subcategories start=$form.categories_1_limit max=$form.categories_2_limit}
							<tr>
								<td width="115" valign="top">
									<a href="ecards.php?sel=cards&amp;id_category={$form.id_category}&amp;id_subcategory={$subcategories[c].id}{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}"><img src="{$subcategories[c].image}" width="100" height="100" border="0" alt=""></a>
								</td>
								<td valign="top">
									<div>
										<span><a href="ecards.php?sel=cards&amp;id_category={$form.id_category}&amp;id_subcategory={$subcategories[c].id}{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}" style="font-size: 14px;"><b>{$subcategories[c].name}</b></a></span>
										<span style="padding-left: 15px;"><font class="text_hidden">{$subcategories[c].cards_count} {$lang.cards.cards}</font></span>
									</div>
									<div style="line-height: 16px; padding-top: 3px;">{$subcategories[c].descr}</div>
								</td>
							</tr>
							<tr>
								<td height="15">  </td>
							</tr>
						{/section}
						</table>
					</td>
				</tr>
				</table>
			</div>
		</div>
	</div>
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}