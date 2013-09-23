{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple my-ecard">
	<div class="content" style="padding: 15px;">
		<div style="margin: 0px;">
			<div class="hdr2">
				{if $form.user_to_fname}
					{$lang.cards.my_orders_to} {$form.user_to_fname}
				{else}
					{$lang.cards.my_orders}
				{/if}
			</div>
			<div style="padding: 10px 0px;">
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td><img src="{$site_root}{$template_root}/images/btn_back.gif" alt="back"></td>
						<td><a href="ecards.php?{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}">{$lang.cards.back_to_categories_list}</a></td>
						{if $data.id_category}
							<td style="padding-left: 20px;"><img src="{$site_root}{$template_root}/images/btn_back.gif" alt="back"></td>
							<td><a href="ecards.php?sel=category&amp;id_category={$data.id_category}{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}">{$lang.cards.back_to}  {$data.category_name}  {$lang.cards.category}</a></td>
						{/if}
						{if $data.id_category && $data.id_subcategory}
							<td style="padding-left: 20px;"><img src="{$site_root}{$template_root}/images/btn_back.gif" alt="back"></td>
							<td><a href="ecards.php?sel=cards&amp;id_category={$data.id_category}&amp;id_subcategory={$data.id_subcategory}{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}">{$lang.cards.back_to}  {$data.subcategory_name}  {$lang.cards.subcategory}</a></td>
						{/if}
						{if $data.id_order}
							<td style="padding-left: 20px;"><img src="{$site_root}{$template_root}/images/btn_back.gif" alt="back"></td>
							<td><a href="ecards.php?sel=card&amp;id_order={$data.id_order}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}">{$lang.cards.back_to} {$button.edit_card}</a></td>
						{/if}
					</tr>
				</table>
			</div>
			{if $sent == 1}
				<div class="error_msg">{$lang.cards.successfully_sent}</div>
			{/if}
			{if $smarty.get.deleted == 1}
            	<div class="error_msg">{$lang.cards.successfully_deleted}</div>
			{/if}
			{if $orders}
				<div style="padding-top: 5px; padding-bottom: 10px;">
					<table border="0" cellpadding="3" cellspacing="0" width="100%" class="ecard-list">
					<thead>	
                                            <tr>
							<th align="center"><b>{$lang.cards.admin.order.card_header}</b></th>
							<th align="center"><b>{$lang.cards.admin.order.card_price}</b></th>
							<th align="center"><b>{$lang.cards.admin.order.recipient}</b></th>
							<th align="center"><b>{$lang.cards.admin.order.card_image}</b></th>
							<th align="center"><b>{$lang.cards.admin.order.order_status}</b></th>
							<th align="center"><b>{$lang.cards.admin.order.action}</b></th>
						</tr>
					</thead>
                                        <tbody>
						{foreach item=item from=$orders}
							<tr>
								<td style="padding-top: 5px;" class="hdr2x">{$item.card_header}</td>
								<td align="center" style="padding-top: 5px;">
									{if $item.card_price_raw == 0}
										{$lang.pays.free}
									{else}
										{$item.card_price}  {$form.cur}
									{/if}
								</td>
								<td align="center" style="padding-top: 5px;">
									{if $smarty.get.fixuser == 'Y'}
										{$item.user_to_fname}
									{else}
										<a href="viewprofile.php?id={$item.id_user_to}" target="_blank">{$item.user_to_fname}</a>
									{/if}
								</td>
								<td align="center" style="padding-top: 5px;">
									<a href="#" onclick="return GB_showImage('{$item.name_unslashed}', '{$item.card_image_big}')">
										<img src="{$item.card_image_thumb}" height="100" width="100" alt="" style="border: 1px solid #cccccc;">
									</a>
								</td>
								<td align="center" style="padding-top: 5px;">
									{if $item.status == 'temped'}
										<a href="ecards.php?sel=card&amp;id_order={$item.id_order}{if $item.id_user_to}&amp;id_user_to={$item.id_user_to}{if $smarty.get.fixuser && $item.id_user_to == $data.id_user_to}&amp;fixuser=Y{/if}{/if}">{$item.status_lang}</a>
									{else}
										{$item.status_lang}
									{/if}
								</td>
								<td align="center" style="padding-top: 5px;">
									<input class="normal-btn" type="button" value="{$lang.button.delete}" onclick="document.location.href='ecards.php?sel=order_delete&amp;id_order={$item.id_order}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}';" />
								</td>
							</tr>
						{/foreach}
                                        </tbody>
                                        </table>
				</div>
				{if $links}
					<ol class="page-nation">
						{foreach item=item from=$links}
							<li><a href="{$item.link}" {if $item.selected eq '1'} class="selected"{/if}>{$item.name}</a></li>
						{/foreach}
					</ol>
				{/if}
			{/if}
		</div>
	</div>
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}