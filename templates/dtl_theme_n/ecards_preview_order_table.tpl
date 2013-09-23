{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple my-ecard">
	<div style="padding: 15px;">
		<div style="margin: 0px;">
			<div class="hdr2">{$data.card_header}</div>
			<div style="padding-top: 10px;">
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td><img src="{$site_root}{$template_root}/images/btn_back.gif" alt="back"></td>
						<td><a href="ecards.php?id_order={$data.id_order}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}">{$lang.cards.back_to_categories_list}</a></td>
						<td style="padding-left: 20px;"><img src="{$site_root}{$template_root}/images/btn_back.gif" alt="back"></td>
						<td><a href="ecards.php?sel=category&amp;id_category={$data.id_category}{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}">{$lang.cards.back_to}&nbsp;{$data.category_name}&nbsp;{$lang.cards.category}</a></td>
						<td style="padding-left: 20px;"><img src="{$site_root}{$template_root}/images/btn_back.gif" alt="back"></td>
						<td><a href="ecards.php?sel=cards&amp;id_category={$data.id_category}&amp;id_subcategory={$data.id_subcategory}{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}">{$lang.cards.back_to}&nbsp;{$data.subcategory_name}&nbsp;{$lang.cards.subcategory}</a></td>
						<td style="padding-left: 20px;"><img src="{$site_root}{$template_root}/images/my_basket_icon.gif" alt="order list"></td>
						<td>
							{if $data.id_user_to && $smarty.get.fixuser}
								<a href="ecards.php?sel=my_orders&amp;id_order={$data.id_order}&amp;id_user_to={$data.id_user_to}&amp;fixuser=Y">{$lang.cards.my_orders_to} {$data.user_to_fname}</a>
							{else}
								<a href="ecards.php?sel=my_orders&amp;id_order={$data.id_order}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}">{$lang.cards.my_orders}</a>
							{/if}
						</td>
						<td style="padding-left: 20px;"><img src="{$site_root}{$template_root}/images/btn_back.gif" alt="back"></td>
						<td><a href="ecards.php?sel=card&amp;id_order={$data.id_order}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}">{$lang.cards.back_to} {$button.edit_card}</a></td>
					</tr>
				</table>
			</div>
			<div style="padding-top: 15px;">
				<div>
					<table cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td valign="top"><img src="{$data.image}" alt=""></td>
							<td valign="top">
								{if $smarty.session.permissions.email_compose || true}
									<div id="message_div" style="background-color: #fff; z-index: 10; padding: 10px; margin: 0px 0px 15px 25px; border: 1px solid #ccc;">
										{$data.message}
									</div>
								{/if}
								{if $data.price_raw > 0 || $smarty.const.MM_ECARDS_FREE == 0}
									<div style="margin: 0px 0px 15px 25px;">
										<b>{$lang.cards.price}:&nbsp;{$data.price}&nbsp;{$form.cur}</b>
									</div>
								{/if}
								<div id="result_div" style="margin-left: 25px;">
									<div style="padding: 10px; margin-bottom: 15px; background-color: #ccc;">
										<table cellpadding="0" cellspacing="0">
											<tr>
												<td valign=top><img src="{$data.user_to_icon_path}" class="icon" alt=""></td>
												<td valign=top style="padding-left: 15px;"><b>{$data.user_to_fname}</b>, {$data.user_to_age} {$lang.home_page.ans}</td>
											</tr>
										</table>
									</div>
									<div style="margin-bottom:8px;" class="tchf-ch-la">
										
											<p class="basic-btn_here">
												<b></b><span>
												<input type="button" onclick="document.location.href='ecards.php?sel=order_form&amp;id_order={$data.id_order}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}'" value="{$lang.button.confirm_and_send}" />
												</span>
											</p>
										
											<p class="basic-btn_here">
												<b></b><span>
												<input type="button" onclick="document.location.href='ecards.php?sel=card&amp;id_order={$data.id_order}{if $smarty.get.fixuser}&amp;fixuser=Y{/if}'" value="{$lang.button.edit_card}" />
												</span>
											</p>
											
										
									</div>
									<div style="font-size:88%">({$lang.cards.must_connect_to_edit})</div>
								</div>
							</td>
						</tr>
					</table>
				</div>
				{if $data.song_url}
					<div style="padding-top: 15px;">
						<span id="player1">
							<script type="text/javascript">
								var fv = "file={$data.song_url}&autostart=false&title={$data.song_name|escape}&lightcolor=0xD12627&repeat=true";
								var FO = {ldelim}
									movie:"{$site_root}/include/mp3player/mp3player.swf",width:"300",height:"20",majorversion:"7",build:"0",bgcolor:"#FFFFFF", flashvars:fv
								{rdelim};
								UFO.create(FO, "player1");
							</script>
						</span>
					</div>
				{/if}
			</div>
		</div>
	</div>
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}