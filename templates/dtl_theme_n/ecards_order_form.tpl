{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple my-ecard">
	<div class="content" style="padding: 15px;">
		<div style="margin: 0px;">
			<div class="header">{$lang.cards.confirm_order}</div>
			<div style="padding-top: 10px;">
				<table cellpadding="0" cellspacing="0">
					<tr>
						{*
						<td><img src="{$site_root}{$template_root}/images/btn_back.gif" alt="back"></td>
						<td><a href="ecards.php?id_order={$order.id_order}&amp;id_user_to={$order.id_user_to}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}">{$lang.cards.back_to_categories_list}</a></td>
						<td style="padding-left: 20px;"><img src="{$site_root}{$template_root}/images/my_basket_icon.gif" alt="order list"></td>
						<td>
							{if $order.id_user_to && $smarty.get.fixuser}
								<a href="ecards.php?sel=my_orders&amp;id_user_to={$order.id_user_to}&amp;fixuser=Y">{$lang.cards.my_orders_to} {$order.user_to_fname}</a>
							{else}
								<a href="ecards.php?sel=my_orders{if $order.id_user_to}&amp;id_user_to={$order.id_user_to}{/if}">{$lang.cards.my_orders}</a>
							{/if}
						</td>
						*}
						<td><img src="{$site_root}{$template_root}/images/btn_back.gif" alt="back"></td>
						<td><a href="ecards.php?sel=card&amp;id_order={$order.id_order}&amp;id_user_to={$order.id_user_to}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}">{$lang.cards.back_to} {$button.edit_card}</a></td>
					</tr>
				</table>
			</div>
			<div style="padding-top: 15px;">
				<table cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td valign="top"><img src="{$order.thumb_image}" alt=""></td>
						<td valign="top" style="padding-left: 25px;">
							<div><b>{$lang.cards.for_user}:</b>  </div>
							<div style="padding-top: 10px;">
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td valign=top><img src="{$order.user_to_icon_path}" class="icon" alt=""></td>
										<td valign=top style="padding-left: 15px;"><b>{$order.user_to_fname}</b>, {$order.user_to_age} {$lang.home_page.ans}</td>
									</tr>
								</table>
							</div>
						</td>
						<td>
							<div style="background-color: #fff; z-index: 10; padding: 10px; margin: 0px 0px 15px 25px; border: 1px solid #ccc;">
								{$order.message}
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div style="padding-top: 10px;"><b>{$lang.cards.price}:</b>  {$order.price}  {$form.cur}</div>
			<div style="padding-top: 10px;"><b>{$lang.cards.current_account}:</b>  {$form.count}  {$form.cur}{if $form.count >= $order.price_raw}      <a href="ecards.php?sel=pay_from_account&amp;id_order={$order.id_order}"><b>{$lang.cards.pay_from_account}</b></a>{/if}</div>
			<div style="margin: 10px 0px">
			{if $order.price_raw > 0}
				{foreach name=s item=item from=$paysys}
					<div class="center" style="width: 130px;">
						<p class="basic-btn_next">
							<span>
							<input type="button" onclick="document.location.href='ecards.php?sel=pay_by_paysys&amp;paysys={$item.template_name}&amp;id_order={$order.id_order}'" value="{$item.name}">
							</span><b></b>
						</p>
					</div>
				{/foreach}
			{/if}
			</div>
		</div>
	</div>
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}