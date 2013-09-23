{strip}
{if $form.subpage == 'view_categories'}

	<div class="toc my-store my-store-top-section">
		<div class="my-store-header-space">
			<div class="text_header">{$lang.giftshop.intro_text.$user_gender}</div>	
			{include file="$gentemplates/giftshop_recipient.tpl"}
			{if $giftshop_comment}
				{include file="$gentemplates/giftshop_comment.tpl"}
			{/if}
		</div>
	</div>
	<div class="bottom-section">
		<div class="toc my-store">
			<div>
				<div class="hdr2">{$lang.giftshop.section_categories} / Select a Category</div>
				<div class="tcxf-ch-la store-sep-2">
					{* left column start *}
					<div>
						<ul class="tcxf-ch-la my-store-gift-list">
							{foreach key=key item=item from=$categories name=categories}
								<li>
									<div {if $item.thumb_icon_path}class="tcxf-ch-la has_icon"{/if}>
									{if $item.thumb_icon_path}
									<div><a href="giftshop.php?sel=items&amp;category={$item.id}"><img src="{$item.thumb_icon_path}" class="icon" alt="" align="top"></a></div>
									{/if}
									<div class="gift-description">
										<div><strong><a href="giftshop.php?sel=items&amp;category={$item.id}">{$item.name}</a></strong></div>
										<div>{$item.comment_all}</div>
									</div>							
									</div>
								</li>
							{/foreach}
						</ul>
						<div>
							<div class="hdr2">{$lang.giftshop.item_bestseller}</div>
							<ul class="tcxf-ch-la my-store-gift-list">
								{foreach key=key item=item from=$bestsellers name=bestsellers}
									<li>
										<div {if $item.thumb_icon_path}class="tcxf-ch-la has_icon"{/if}>
											{if $item.thumb_icon_path}
												<div>
													<a href="giftshop.php?sel=view&amp;item={$item.id}"><img src="{$item.thumb_icon_path}" class="icon" alt=""></a>
												</div>
											{/if}
											<div>
												<div class="gift-description">
													<div><a href="giftshop.php?sel=view&amp;item={$item.id}"><b>{$item.name}</b></a></div>
													<div>{$item.comment}</div>
												</div>
											</div>
											<div style="clear:both;float:none;">
												<div class="add-to-cart tcxf-ch-la">
													<p><strong>{$item.price} {$form.currency}</strong></p>
													<p class="basic-btn_here">
														<b></b><span>
														<input type="button" onclick="window.location.href='giftshop.php?sel=basket_add&amp;item={$item.id}';" value="{$lang.giftshop.item_add_to_basket}"><br>
														</span>
													</p>
												</div>
											</div>
										</div>
									</li>
								{/foreach}
							</ul>
						</div>
						<div>
							<div class="hdr2">{$lang.giftshop.gift_ideas}</div>
							<ul class="tcxf-ch-la my-store-gift-list">
								{foreach key=key item=item from=$promoted name=promoted}
									<li>
										<div{if $item.thumb_icon_path} class="tcxf-ch-la has_icon"{/if}>
											{if $item.thumb_icon_path}
												<div>
													<a href="giftshop.php?sel=view&amp;item={$item.id}"><img src="{$item.thumb_icon_path}" class="icon" alt=""></a>
												</div>
											{/if}
											<div>
												<div class="gift-description">
													<div><a href="giftshop.php?sel=view&amp;item={$item.id}"><b>{$item.name}</b></a></div>
													<div>{$item.comment}</div>
												</div>
											</div>
											<div style="clear:both;float:none;">
												<div class="add-to-cart tcxf-ch-la">
													<p><strong>{$item.price} {$form.currency}</strong></p>
													<p class="basic-btn_here">
														<b></b><span>
														<input type="button" onclick="window.location.href='giftshop.php?sel=basket_add&amp;item={$item.id}';" value="{$lang.giftshop.item_add_to_basket}"><br>
														</span>
													</p>
												</div>
											</div>
										</div>
									</li>
								{/foreach}
							</ul>
						</div>
					</div>
					{* left column end, right column start *}
					<div>
						<div>
							<h2 class="hdr2">{$lang.giftshop.categories}:</h2>
							{foreach item=item from=$categories}
								<p><a href="giftshop.php?sel=items&amp;category={$item.id}">{$item.name}</a></p>
							{/foreach}
							{if $bestsellers}
								<h2 class="hdr2">{$lang.giftshop.item_bestseller}:</h2>
								{foreach item=item from=$bestsellers}
									<p><a href="giftshop.php?sel=view&amp;item={$item.id}">{$item.name}</a></p>
								{/foreach}
							{/if}
							{if $last_viewed}
								<h2 class="hdr2">{$lang.giftshop.item_recent}:</h2>
								{foreach item=item from=$last_viewed}
									<p>
										<a href="giftshop.php?sel=view&amp;item={$item.id}">{$item.name}</a>
									</p>
								{/foreach}
							{/if}
						</div>
					</div>
					{* right column end *}
				</div>
			</div>
		</div>
	</div>

{elseif $form.subpage == 'view_items'}
	
	<div class="toc my-store my-store-top-section">
		<div class="my-store-header-space"> 
			<div>
				{include file="$gentemplates/giftshop_recipient.tpl"}
				{if $giftshop_comment}
					{include file="$gentemplates/giftshop_comment.tpl"}
				{/if}
			</div>
		</div>
	</div>
	<div class="bottom-section">
		<div class="toc my-store">
			<div>
				<div class="hdr2">{$category_name} / Select an Item</div>
				<div class="tcxf-ch-la store-sep-2">
					<div>
						<ul class="tcxf-ch-la my-store-gift-list">
							{foreach key=key item=item from=$items_list name=te}
								<li>
									<div {if $item.thumb_icon_path} class="tcxf-ch-la has_icon"{/if}>
									{if $item.thumb_icon_path}
										<div>
											<a href="giftshop.php?sel=view&amp;item={$item.id}"><img src="{$item.thumb_icon_path}" class="icon" alt=""></a>
										</div>
									{/if}
									<div>
										<div class="gift-description">
										<div><a href="giftshop.php?sel=view&amp;item={$item.id}"><b>{$item.name}</b></a></div>
											<div class="text" style="height:118px; max-height:118px; overflow:hidden; text-align:justify; font-size:11px;">{$item.comment}</div>
										</div>
									</div>
									<div style="clear:both;float:none;">
										<div class="add-to-cart tcxf-ch-la">
											<p><strong>{$item.price} {$form.currency}</strong></p>
											<p class="basic-btn_here">
												<b></b><span>
												<input type="button" onclick="window.location.href='giftshop.php?sel=basket_add&amp;item={$item.id}&amp;category={$category_active.id}';" value="{$lang.giftshop.item_add_to_basket}"><br>
												</span>
											</p>
										</div>
									</div>
								</li>
							{/foreach}
						</ul>
						{if $form.links}
							<ol class="page-nation">
								{foreach item=item from=$form.links}
									<li><a href="{$item.link}" {if $item.selected eq '1'}class="selected"{/if}>{$item.name}</a></li>
								{/foreach}
							</ol>
						{/if}
					</div>
					<div>
						<div>
							<div class="hdr2">{$lang.giftshop.categories}:</div>
							{foreach item=item from=$categories}
								<p>
									<a href="giftshop.php?sel=items&amp;category={$item.id}">{$item.name}</a>
								</p>
							{/foreach}
							{if $bestsellers}
								<div class="hdr2">{$lang.giftshop.item_bestseller}:</div>
								{foreach item=item from=$bestsellers}
									<p>
										<a href="giftshop.php?sel=view&amp;item={$item.id}">{$item.name}</a>
									</p>
								{/foreach}
							{/if}
							{if $last_viewed}
								<div class="hdr2">{$lang.giftshop.item_recent}:</div>
								{foreach item=item from=$last_viewed}
									<p>
										<a href="giftshop.php?sel=view&amp;item={$item.id}">{$item.name}</a>
									</p>
								{/foreach}
							{/if}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

{elseif $form.subpage == 'view_item'}

	<div class="toc my-store my-store-top-section">
		<div class="my-store-header-space"> 
			<div>
				{include file="$gentemplates/giftshop_recipient.tpl"}
				{if $giftshop_comment}
					{include file="$gentemplates/giftshop_comment.tpl"}
				{/if}
			</div>
		</div>
	</div>
	<div class="bottom-section">
		<div class="toc my-store">
			<div class="tcxf-ch-la store-sep-2">
				<div>
					<div {if $item.thumb_icon_path} class="tcxf-ch-la has_icon"{/if}>
						{if $item_data.thumb_icon_path}
							<div>
								<img src="{$item_data.icon_path}" class="icon" alt="">{* or {$item_data.thumb_icon_path} *}
							</div>
						{/if}
						<div>
							<div class="hdr2x">{$item_data.name}</div>
							<div class="text">{$item_data.comment}</div>
							<p><strong class="det-16">{$item_data.price} {$form.currency}</strong></p>
							{if $smarty.get.order}
								<p class="basic-btn_here">
									<b></b><span>
									<input type="button" onclick="window.location.href='giftshop.php?sel=view_order&amp;order={$smarty.get.order}';" value="{$lang.giftshop.return_to_order}">
									</span>
								</p>
							{else}
								<p class="basic-btn_here">
									<b></b><span>
									<input type="button" class="btn_org" style="width:120px;" onclick="window.location.href='giftshop.php?sel=basket_add&amp;item={$item_data.id}&amp;category={$category_active.id}';" value="{$lang.giftshop.item_add_to_basket}">
									</span>
								</p>
								<p>You Can Still Remove Later.</p>
							{/if}
						</div>
					</div>
					{if $item_data.gallery}
						<div style="padding:40px 10px 10px 10px;">
							{foreach item=photo from=$item_data.gallery}
								<img src="{$photo.thumb_image_path}" class="icon" alt="" style="cursor:pointer; padding-right:5px;" onclick="window.open('giftshop.php?sel=gallery&amp;id={$item_data.id}&amp;id_image={$photo.id}', 'photo_w', 'height=200,scrollbars=no,resizable=no,width=300, toolbar=no,menubar=no,personalbar=no');"> 
							{/foreach}
						</div>
					{/if}
					{if $form.links}
						<ol class="page-nation">
							{foreach item=item from=$form.links}
								<li><a href="{$item.link}" {if $item.selected == '1'}class="selected"{/if}>{$item.name}</a></li>
							{/foreach}
						</ol>
					{/if}
				</div>
				<div>
					<div>
						<div class="hdr2">{$lang.giftshop.categories}:</div>
						{foreach item=item from=$categories}
							<p>
								<a href="giftshop.php?sel=items&amp;category={$item.id}">{$item.name}</a>
							</p>
						{/foreach}
						{if $items_list}
							<div class="hdr2" style="margin-bottom:4px;">{$lang.giftshop.item_other}:</div>
							{foreach item=item from=$items_list}
								{if $item_data.id != $item.id}
									<p><a href="giftshop.php?sel=view&amp;item={$item.id}">{$item.name}</a></p>
								{/if}
							{/foreach}
							<br />
						{/if}
						{if $bestsellers}
							<div class="hdr2" style="margin-bottom:4px;">{$lang.giftshop.item_bestseller}:</div>
							{foreach item=item from=$bestsellers}
								<p><a href="giftshop.php?sel=view&amp;item={$item.id}">{$item.name}</a></p>
							{/foreach}
						{/if}
						{if $last_viewed}
							<div class="hdr2" style="margin-bottom:4px;">{$lang.giftshop.item_recent}:</div>
							{foreach item=item from=$last_viewed}
								<p><a href="giftshop.php?sel=view&amp;item={$item.id}">{$item.name}</a></p>
							{/foreach}
						{/if}
						{if $same_items}
							<div class="hdr2" style="margin-bottom:4px;"><font>{$lang.giftshop.item_with_buy}:</font></div>
							{foreach item=item from=$same_items}
								{if $item_data.id != $item.id}
									<p><a href="giftshop.php?sel=view&amp;item={$item.id}">{$item.name}</a></p>
								{/if}
							{/foreach}
						{/if}
					</div>
				</div>
			</div>
		</div>
	</div>

{elseif $form.subpage == 'view_basket'}

	<div class="toc my-store my-store-top-section">
		<div class="my-store-header-space"> 
			<div>
				{include file="$gentemplates/giftshop_recipient.tpl"}
				{if $giftshop_comment}
					{include file="$gentemplates/giftshop_comment.tpl"}
				{/if}
			</div>
		</div>
	</div>
	<div class="bottom-section">
		<div class="toc my-store">
			<div>
				<div class="hdr2">{$lang.giftshop.section_basket}</div>
				{if $basket_data.positions}
					<form name="basket_form" method="post" action="giftshop.php" style="margin:0px;">
						<input type="hidden" name="sel" value="basket_refresh">
						<input type="hidden" name="category" value="{$item.shop.id_category}">
						<table width="100%" border="0" cellspacing="0" cellpadding="0" class="basket_list">
							<thead>
								<tr>
									<th align="left" class="text_head" style="padding-right:10px;"></th>
									<th align="left" class="text_head" style="padding-right:10px;">
										{$lang.giftshop.basket_item}
									</th>
									<th align="center" class="text_head" style="padding-right:10px;">
										{$lang.giftshop.basket_quantity}
									</th>
									<th align="right" class="text_head" style="padding-right:10px;">
										{$lang.giftshop.basket_price}
									</th>
									<th align="right" class="text_head" style="padding-right:10px;">
										{$lang.giftshop.basket_sum}
									</th>
								</tr>
							</thead>
							<tbody>
								{foreach key=key item=item from=$basket_data.positions name=basket}
									<tr>
										<td style="padding:5px 10px 5px 0px; text-align:center;">
											{if $item.shop.thumb_icon_path}
												<img src="{$item.shop.thumb_icon_path}" class="icon" alt="">
											{/if}
										</td>
										<td style="padding:5px 10px 5px 0px; text-align:left;">
											<a href="giftshop.php?sel=view&amp;item={$item.shop.id}">{$item.shop.name}</a>
										</td>
										<td style="padding:5px 10px 5px 0px; text-align:center;">
											<input name="quantity[{$item.shop.id}]" value="{$item.quantity}" style="width:50px; text-align:right;">
										</td>
										<td style="padding:5px 10px 5px 0px; text-align:right;">
											{$item.shop.price} {$form.currency}
										</td>
										<td style="padding:5px 10px 5px 0px; text-align:right;">
											{$item.sum} {$form.currency}
										</td>
									</tr>
								{/foreach}
							</tbody>
							<tfoot>
								<tr>
									<td height="35" colspan="2"></td>
									<td style="padding-right:10px;">
										<p class="basic-btn_here">
											<b></b><span>
											<input type="button" onclick="this.form.submit();" value="{$lang.giftshop.basket_refresh}">
											</span>
										</p>
									</td>
									<td></td>
									<td colspan="2">
										<div class="total">
											{$lang.giftshop.basket_total}: <b>{$basket_data.total_amount_format} {$form.currency}</b>
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="4">
										<div style="float:left; width:600px;">
											<div class="hdr2x" style="font-weight: bold; margin:50px 0px 5px 5px;">&#8220;{$lang.giftshop.basket_info_nathamon}&#8221;</div>
											<p class="text_head"><strong>{$lang.giftshop.basket_info}:</strong></p>
											<textarea name="comment" style="width:580px; height:100px; margin:5px;">{$smarty.session.basket_comment}</textarea>
											<p class="basic-btn_here">
												<b></b><span>
												<input type="submit" class="btn_org" style="width:80px;" value="{$lang.button.save}">
												</span>
											</p>
										</div>
										<div style="clear:both;"></div>
									</td>
									<td align="right" valign="top" style="padding-right:10px; padding-top:10px;">
										{if $recipient_data}
											<p class="basic-btn_here">
												<b></b><span>
												<input type="button" onclick="this.form.sel.value='confirm_order'; this.form.submit();" value="{$lang.button.confirm_and_order}">
												</span>
											</p>
										{/if}
										<p class="basic-btn_here">
											<b></b><span>
											<input type="button" id="btn_cancel_basket" value="{$lang.button.empty_cart_and_go_back}">
											</span>
										</p>
										<div style="clear:right;"></div>
									</td>
								</tr>
							</tfoot>
						</table>
					</form>
				{else}
					<p style="text-align:center; font-weight:bold;">{$lang.giftshop.basket_empty}</p>
				{/if}
			</div>
		</div>
	</div>
	{* jquery dialog start *}
	<div id="cancel_basket_dialog" title="Confirm Delete Basket">
		{$lang.giftshop.confirm_delete_basket}
	</div>
	<script type="text/javascript">
	{literal}
	$(document).ready(function() {
		$("#cancel_basket_dialog").dialog({
			autoOpen: false, modal: true, width: 400,
			buttons: {
				Delete: function() { f=document.basket_form; f.sel.value='basket_clear'; f.submit(); },
				Cancel: function() { $(this).dialog('close'); }
			}
		});
	});
	$('#btn_cancel_basket').click(function() { $('#cancel_basket_dialog').dialog('open'); return false; });
	{/literal}
	</script>
	{* jquery dialog end *}

{elseif $form.subpage == 'view_order'}

	<div class="toc my-store my-store-top-section">
		<div class="my-store-header-space"> 
			<div>
				{include file="$gentemplates/giftshop_recipient.tpl"}
				{if $giftshop_comment}
					{include file="$gentemplates/giftshop_comment.tpl"}
				{/if}
			</div>
		</div>
	</div>
	<div class="bottom-section">
		<div class="toc my-store">
			<div>
				<div class="hdr2">{$lang.giftshop.view_order_title}{$order_data.id}</div>
				{if $order_data.items}
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="basket_list">
						<thead>
							<tr>
								<th style="padding-right:10px;"></th>
								<th align="left" class="text_head" style="padding-right:10px;">{$lang.giftshop.basket_item}</th>
								<th align="center" class="text_head" style="padding-right:10px;">{$lang.giftshop.basket_quantity}</th>
								<th align="right" class="text_head" style="padding-right:10px;">{$lang.giftshop.basket_price}</th>
								<th align="right" class="text_head" style="padding-right:10px;">{$lang.giftshop.basket_sum}</th>
							</tr>
						</thead>
						<tbody>
							{foreach key=key item=item from=$order_data.items}
								<tr>
									<td style="padding-right:10px;">
										{if $item.shop.thumb_icon_path}
											<img src="{$item.shop.thumb_icon_path}" class="icon" alt="">
										{/if}
									</td>
									<td align="left" class="text" style="padding-right:10px;">
										<a href="giftshop.php?sel=view&amp;item={$item.shop.id}&amp;order={$order_data.id}">{$item.shop.name}</a>
									</td>
									<td align="center" class="text" style="padding-right:10px;">{$item.quantity}</td>
									<td align="right" class="text" style="padding-right:10px;">{$item.currency} {$form.currency}</td>
									<td align="right" class="text" style="padding-right:10px;">{$item.sum} {$form.currency}</td>
								</tr>
							{/foreach}
						</tbody>
						<tfoot>
							<tr>
								<td height="35" align="left" colspan="3"> </td>
								<td height="35" align="right">
									{if $order_data.paid_status != '1' && ! $order_data.offline_payment_pending}
										<p class="basic-btn_here">
											<b></b><span><input type="button" id="btn_edit_order" value="Edit Order" /></span>
										</p>
									{/if}
								</td>
								<td colspan="2">
									<div class="total">
										{$lang.giftshop.basket_total}: <b>{$order_data.total_amount_format} {$form.currency}</b>
									</div>
								</td>
							</tr>
						</tfoot>
					</table>
					{* payment options *}
					<div class="tcxf-ch-la">
						<div style="width: 300px;">
							<br /><br />
							<img title="Paypal" src="{$site_root}{$template_root}/images/paypal_verified.png" />
							<br /><br />
							<div align="center">
								<!-- PayPal Buyer Protection -->
								{literal}
								<script type="text/javascript">
								function openBpWindow() {
									window.open('https://www.paypal-apac.com/buyer-protection.html?size=180x113&url=' + escape(window.location.hostname) + '&page=' + escape(window.location.pathname),'olcwhatispaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=400, height=260');
								};
								document.write('<a href="javascript:openBpWindow()"><img src="https://www.paypal-apac.com/images/logos/PayPal-Buyer-Protection-Logo-180p-x-113p.gif" border="0" alt="Acceptance Mark"></a>');
								document.write('<img src="https://www.paypal-apac.com/tracking/?size=180x113&url=' + escape(window.location.hostname) + '&page=' + escape(window.location.pathname) + '" border="0" alt="" width="1" height="1">');
								</script>
								{/literal}
								<!-- PayPal Buyer Protection -->
							</div>
						</div>
						<div style="width:600px;">
							{if $order_data.paid_status == '1'}
								<div class="header" style="padding:10px 10px 0px 0px; font-size:24px; text-align:right;">
									{$lang.giftshop.paid|upper}
								</div>
								{if $order_data.delivery_status == '1'}
									<div class="header" style="padding:10px 10px 0px 0px; font-size:24px; text-align:right;">
										{$lang.giftshop.delivery|upper}
									</div>
								{elseif $order_data.shipped_status == '1'}
									<div class="header" style="padding:10px 10px 0px 0px; font-size:24px; text-align:right;">
										{$lang.giftshop.shipped|upper}
									</div>
								{elseif $order_data.procured_status == '1'}
									<div class="header" style="padding:10px 10px 0px 0px; font-size:24px; text-align:right;">
										{$lang.giftshop.procured|upper}
									</div>
								{/if}
							{elseif $order_data.offline_payment_pending}
								<div class="header" style="padding:10px 10px 0px 0px; font-size:24px; text-align:right;">
									{if $order_data.offline_payment_pending == 'atm_payment'}
										{$lang.giftshop.atm_payment_pending|upper}
									{elseif $order_data.offline_payment_pending == 'wire_transfer'}
										{$lang.giftshop.wire_transfer_pending|upper}
									{else}
										{$lang.giftshop.bank_cheque_pending|upper}
									{/if}
								</div>
							{else}
								<form name="pay" action="giftshop.php" method="post">
									<input type="hidden" name="sel" value="">
									<input type="hidden" name="order_id" value="{$order_data.id}">
									<input type="hidden" name="paysys" value="">
									<br />
									{if $account_data.account_curr >= $order_data.total_amount}
										<div class="text" align="right">
											{$lang.pays.current_account_credit}: <b>{$account_data.account_curr_format}</b> {$form.currency}<br />
											<br />
											<input type="button" id="btn_pay_credits" value="{$lang.button.from_current_account}" style="cursor:pointer;" />
											<br />
										</div>
									{/if}
									{* ONLINE PAYMENT OPTIONS*}
									<div class="space"></div>
									{include file="$gentemplates/payment_online.tpl"}
									{* OFFLINE PAYMENT OPTIONS *}
									{assign var="opt_no" value="2"}
									{* ATM PAYMENT (FEMALES ONLY) *}
									{if $auth.gender == $smarty.const.GENDER_FEMALE}
										<div class="space"></div>
										{include file="$gentemplates/payment_atm.tpl"}
										{math assign="opt_no" equation="$opt_no+1"}
									{/if}
									{* WIRE TRANSFER *}
									<div class="space"></div>
									{include file="$gentemplates/payment_wire.tpl"}
									{math assign="opt_no" equation="$opt_no+1"}
									{* BANK CHEQUE *}
									<div class="space"></div>
									{include file="$gentemplates/payment_cheque.tpl"}
								</form>
								<script type="text/javascript">
								{literal}
								function payment_online_submit(paysys) {
									f = document.pay;
									f.sel.value = "pay_with_service";
									f.paysys.value = paysys;
									f.submit();
								}
								function payment_offline_submit(paysys) {
									f = document.pay;
									f.sel.value = paysys;
									f.paysys.value = paysys;
									f.submit();
								}
								{/literal}
								</script>
							{/if}
						</div>
					</div>
				{else}
					<div style="margin:20px 0px; text-align:center;">
						{$lang.giftshop.order_empty}
					</div>
				{/if}
			</div>
		</div>
	</div>
	{* jquery dialog start *}
	<div id="edit_order_dialog" title="Confirm Edit Order">
		{$lang.giftshop.confirm_edit_order_1}<br /><br />{$lang.giftshop.confirm_edit_order_2}
	</div>
	<div id="pay_credits_dialog" title="Confirm Credits Payment">
		{$lang.giftshop.confirm_credits_payment}
	</div>
	<script type="text/javascript">
	{literal}
	$(document).ready(function() {
		$("#edit_order_dialog").dialog({
			autoOpen: false, modal: true, width: 400,
			buttons: {
				Confirm: function() { window.location.href='giftshop.php?sel=edit_order&order={/literal}{$order_data.id}{literal}'; },
				Cancel: function() { $(this).dialog('close'); }
			}
		});
		$("#pay_credits_dialog").dialog({
			autoOpen: false, modal: true, width: 400,
			buttons: {
				Confirm: function() { f=document.pay; f.sel.value='pay_from_account'; f.submit(); },
				Cancel: function() { $(this).dialog('close'); }
			}
		});
		$('#btn_edit_order').click(function() { $('#edit_order_dialog').dialog('open'); return false; });
		$('#btn_pay_credits').click(function() { $('#pay_credits_dialog').dialog('open'); return false; });
	});
	{/literal}
	</script>
	{* jquery dialog end *}

{elseif $form.subpage == 'order_history'}

	<div class="bottom-section">	 
		<div class="toc my-store"> 
			<div class="hdr2">{$lang.giftshop.section_orders}</div>
			{if $order_data}
				<table width="100%" border="0" class="basket_list" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th align="left" class="text_head" style="padding-right:10px;">{$lang.giftshop.order_number}</th>
							<th align="center" class="text_head" style="padding-right:10px;">{$lang.giftshop.basket_quantity}</th>
							<th align="right" class="text_head" style="padding-right:10px;">{$lang.giftshop.basket_price}</th>
							<th align="right" class="text_head" style="padding-right:10px;">{$lang.giftshop.basket_sum}</th>
							<th align="left" class="text_head" style="padding-right:10px;">{$lang.giftshop.recipient}</th>
							<th align="center" class="text_head" style="padding-right:10px;">{$lang.giftshop.order_status}</th>
							<th align="center" class="text_head" style="width:1px;">{$lang.giftshop.action}</th>
						</tr>
					</thead>
					<tbody>
						{foreach key=key item=item from=$order_data}
							<tr>
								<td style="padding-right:10px;">
									<a href="giftshop.php?sel=view_order&amp;order={$item.order_info.id}">{$lang.giftshop.order_number} #{$item.order_info.id}</a> <span class="text_hidden">({$item.order_info.date_order})</span>
								</td>
								<td align="center" class="text" style="padding-right:10px; font-weight:bold;">{$item.total_quantity}</td>
								<td align="right" class="text" style="padding-right:10px;"> </td>
								<td align="right" class="text" style="padding-right:10px; font-weight:bold;">{$item.total_amount_format} {$form.currency}</td>
								<td class="text" style="padding-right:10px;">{$item.order_info.fname_to}</td>
								<td align="center" class="text" style="padding-right:10px;">
									{if $item.order_info.paid_status}
										{if $item.order_info.procured_status}
											{if $item.order_info.shipped_status}
												{if $item.order_info.delivery_status}
													{$lang.giftshop.delivery}
												{else}
													{$lang.giftshop.shipped}
												{/if}
											{else}
												{$lang.giftshop.procured}
											{/if}
										{else}
											{$lang.giftshop.paid}
										{/if}
									{else}
										{if $item.order_info.offline_payment_pending == 'atm_payment'}
											{$lang.giftshop.atm_payment_pending}
										{elseif $item.order_info.offline_payment_pending == 'wire_transfer'}
											{$lang.giftshop.wire_transfer_pending}
										{elseif $item.order_info.offline_payment_pending == 'bank_cheque'}
											{$lang.giftshop.bank_cheque_pending}
										{else}
											{$lang.giftshop.unpaid}
										{/if}
									{/if}
								</td>
								<td nowrap="nowrap">
									{if $item.order_info.paid_status != '1' && ! $item.order_info.offline_payment_pending}
										<input type="button" class="text" id="btn_edit_{$item.order_info.id}" style="cursor:pointer;" value="{$lang.button.edit}" />
										<input type="button" class="text" id="btn_delete_{$item.order_info.id}" style="cursor:pointer;" value="{$lang.button.delete}" />
										{* old click-handler: onclick="if (confirm('{$lang.giftshop.confirm_delete_order}')) window.location.href='giftshop.php?sel=delete_order&amp;order={$item.order_info.id}';" *}
										{* jquery dialog start *}
										<div id="edit_order_dialog_{$item.order_info.id}" title="Confirm Edit Order">
											{$lang.giftshop.confirm_edit_order_1}<br /><br />{$lang.giftshop.confirm_edit_order_2}
										</div>
										<div id="delete_order_dialog_{$item.order_info.id}" title="Confirm Delete Order">
											{$lang.giftshop.confirm_delete_order}
										</div>
										<script type="text/javascript">
										$(document).ready(function() {ldelim}
											$("#edit_order_dialog_{$item.order_info.id}").dialog({ldelim}
												autoOpen: false, modal: true, width: 400,
												buttons: {ldelim}
													Confirm: function() {ldelim} window.location.href='giftshop.php?sel=edit_order&order={$item.order_info.id}'; {rdelim},
													Cancel: function() {ldelim} $(this).dialog('close'); {rdelim}
												{rdelim}
											{rdelim});
											$("#delete_order_dialog_{$item.order_info.id}").dialog({ldelim}
												autoOpen: false, modal: true, width: 400,
												buttons: {ldelim}
													Delete: function() {ldelim} window.location.href='giftshop.php?sel=delete_order&order={$item.order_info.id}'; {rdelim},
													Cancel: function() {ldelim} $(this).dialog('close'); {rdelim}
												{rdelim}
											{rdelim});
											$('#btn_edit_{$item.order_info.id}').click(function() {ldelim} $('#edit_order_dialog_{$item.order_info.id}').dialog('open'); return false; {rdelim});
											$('#btn_delete_{$item.order_info.id}').click(function() {ldelim} $('#delete_order_dialog_{$item.order_info.id}').dialog('open'); return false; {rdelim});
										{rdelim});
										</script>
										{* jquery dialog end *}
									{/if}
								</td>
							</tr>
							{foreach item=item_pos from=$item.order_info.items}
								<tr>
									<td class="text" style="padding:0px 10px;">{$item_pos.shop.name}</td>
									<td align="center" class="text" style="padding-right:10px;">{$item_pos.quantity}</td>
									<td align="right" class="text" style="padding-right:10px;">{$item_pos.currency} {$form.currency}</td>
									<td align="right" class="text" style="padding-right:10px;">{$item_pos.sum} {$form.currency}</td>
									<td> </td>
									<td> </td>
									<td> </td>
								</tr>
							{/foreach}
						{/foreach}
					</tbody>
				</table>
			{else}
				<div style="margin:20px 0px; text-align:center;">
					{$lang.giftshop.order_list_empty}
				</div>
			{/if}
		</div>
	</div>

{elseif $form.subpage == 'account_payment_success'}

	<div class="toc my-store"> 
		<div style="padding:10px;">
			<p><strong>{$lang.pays.amount_was_taken_text_1} {$form.amount} {$form.currency} {$lang.pays.amount_was_taken_text_2} {$form.account_curr} {$form.currency} {$lang.pays.amount_was_taken_text_3}</strong></p>
			<div class="back_to"><a href="giftshop.php">{$lang.button.back_to_shop}</a></div>
		</div>
	</div>

{elseif $form.subpage == 'wishlist'}

	<div class="bottom-section">	 
		<div class="toc my-store"> 
			<div class="upgrade-member tcxf-ch-la"> 
				<div>
					<div class="callchat_icons2">
						<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/telephone.png" alt="Call Me" title="Call Me"></a> 
						<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/Chat.png" alt="Chat With Me" title="Chat With Me" ></a>
					</div>
				</div>
				<div>
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td valign="top">
								<div style="padding:10px 10px;" class="text">
									<div>{$lang.giftshop_wishes.intro_text}</div>
									<div align="center" style="padding-top:20px;">
										<img src="{$site_root}{$template_root}/images/happy_couples.png" />
									</div>
								</div>
							</td>
							<td valign="top" width="400" align="right">
								<div class="yellow_box_top">
									<div class="yellow_box_btm">
										<div class="yellow_box_mid">
											<p class="purple_text">{$lang.giftshop_wishes.box_text_1}</p>
											<p style="padding:7px; font-size:14px;" class="txtred" align="center"><b>&#8220;{$lang.giftshop_wishes.box_text_2}&#8221;</b></p>
											<p style="padding:7px; font-size:16px;" class="txtpurple" align="center"><b>&#8220;{$lang.giftshop_wishes.box_text_3}&#8221;</b></p>
											<div align="left" style="padding:2px 5px;">
												<form method="post" action="giftshop.php">
													<input type="hidden" name="sel" value="wishlist" />
													<div style="padding-bottom:8px;">
														<label title="Item Name">
															{if $err_field.product_name}<font class="error">{/if}
															{$lang.giftshop_wishes.product_name}
															{if $err_field.product_name}</font>{/if}:
														</label><br />
														<input type="text" name="product_name" value="{$data.product_name}" style="width:330px;" />
													</div>
													<div style="padding-bottom:8px;">
														<label title="Description">
															{if $err_field.description}<font class="error">{/if}
															{$lang.giftshop_wishes.description}
															{if $err_field.description}</font>{/if}:
														</label><br />
														<textarea name="description" style="width:330px; height:150px;">{$data.description}</textarea>
													</div>
													<div style="padding-bottom:8px;">
														<label title="Notes">
															{if $err_field.notes}<font class="error">{/if}
															{$lang.giftshop_wishes.notes}
															{if $err_field.notes}</font>{/if}:
														</label><br />
														<textarea name="notes" style="width:330px; height:150px;">{$data.notes}</textarea>
													</div>
													<p class="basic-btn_next">
														<span><input type="submit" name="submit" value="Submit" /></span><b></b>
													</p>
												</form>
											</div>
										</div>
									</div>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>

{elseif $form.subpage == 'wishlist_thanks'}

	<div class="bottom-section">
		<div class="toc my-store"> 
			<div class="upgrade-member tcxf-ch-la">
				<div>
					<div class="callchat_icons2">
						<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/telephone.png" alt="Call Me" title="Call Me"></a> 
						<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/Chat.png" alt="Chat With Me" title="Chat With Me" ></a>
					</div>
				</div>
				<div>
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td valign="top">
								<div style="padding:10px 10px;" class="text">
									<div style="padding-bottom:7px;"><b>{$lang.giftshop_wishes.thanks_text_1}</b></div>
									<div style="padding-bottom:12px;line-height:17px;">{$lang.giftshop_wishes.thanks_text_2}</div>
									<div style="padding-bottom:7px;"><b>{$lang.giftshop_wishes.product_name}:</b><br/>{$data.product_name}</div>
									<div style="padding-bottom:7px;"><b>{$lang.giftshop_wishes.description}:</b><br/>{$data.description|nl2br}</div>
									<div style="padding-bottom:12px;"><b>{$lang.giftshop_wishes.notes}:</b><br/>{$data.notes|nl2br}</div>
									<div style="padding-bottom:5px;"><b><a href="index.php">&raquo; Click Here To Go To The Home Page</a></b></div>
									<div align="center" style="padding-top:20px;"><img src="{$site_root}{$template_root}/images/happy_couples_med.png" /></div>
								</div>
							</td>
							<td valign="top" width="360" align="right">
								<div class="content_2">
									<div style="font-weight:bold; text-align:center;">
										<p style="padding-bottom:3px; font-size:13px;">Meet Me Now Bangkok Co. Ltd.</p>
										<p style="padding-bottom:3px;">PO Box 1057</p>
										<p style="padding-bottom:3px;">Silom Post Office</p>
										<p style="padding-bottom:3px;">Bangkok</p>
										<p style="padding-bottom:3px;">THAILAND</p>
										<p style="padding-bottom:10px;">10504</p>
									</div>
									<div class="call_us_box">
										<div>
											<div>
												<img align="right" src="{$site_root}{$template_root}/images/call_us.png" alt="Call In To Date" />
												<p>USA: 1-866-601-7197 (Toll Free)</p>
												<p>Australia: 1300 912 009 (Toll Free)</p>
												<p>Thailand: +66 8 4921 8355</p>
											</div>
										</div>
									</div>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>

{/if}
{/strip}