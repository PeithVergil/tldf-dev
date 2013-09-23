{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple my-ecard">
	<script type="text/javascript" src="{$site_root}/javascript/tiny_mce-3.5.8/tiny_mce.js"></script>
	<script type="text/javascript">
	{literal}
	tinyMCE.init({
		/* General options */
		mode : "textareas",
		theme : "advanced",
		plugins : "emotions,inlinepopups",
		/* Theme options */
		theme_advanced_buttons1 : "bold,italic,underline,|,forecolor,backcolor,|,fontselect,fontsizeselect,|,emotions,|,undo,redo",
		theme_advanced_buttons2 :"",
		theme_advanced_buttons3 :"",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		smiles_path: "{/literal}{$server}{$template_root}{literal}/emoticons/"
	});
	{/literal}
	</script>
	<div class="content" style="padding: 15px;">
		<div style="margin: 0px; ">
			<div class="hdr2">{$data.card_name}</div>
			<div style="padding-top: 10px;">
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td><img src="{$site_root}{$template_root}/images/btn_back.gif" alt="back"></td>
						<td><a href="ecards.php?{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.request.fixuser}&amp;fixuser=Y{/if}">{$lang.cards.back_to_categories_list}</a></td>
						<td style="padding-left: 20px;"><img src="{$site_root}{$template_root}/images/btn_back.gif" alt="back"></td>
						<td><a href="ecards.php?sel=category&amp;id_category={$data.id_category}{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.request.fixuser}&amp;fixuser=Y{/if}">{$lang.cards.back_to}  {$data.category_name}  {$lang.cards.category}</a></td>
						<td style="padding-left: 20px;"><img src="{$site_root}{$template_root}/images/btn_back.gif" alt="back"></td>
						<td><a href="ecards.php?sel=cards&amp;id_category={$data.id_category}&amp;id_subcategory={$data.id_subcategory}{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}{if $smarty.request.fixuser}&amp;fixuser=Y{/if}">{$lang.cards.back_to}  {$data.subcategory_name}  {$lang.cards.subcategory}</a></td>
						<td style="padding-left: 20px;"><img src="{$site_root}{$template_root}/images/my_basket_icon.gif" alt="order list"></td>
						<td>
							{if $data.id_user_to && $smarty.request.fixuser}
								<a href="ecards.php?sel=my_orders{if $data.id_order}&amp;id_order={$data.id_order}{/if}&amp;id_user_to={$data.id_user_to}&amp;fixuser=Y">{$lang.cards.my_orders_to} {$data.user_to_fname}</a>
							{else}
								<a href="ecards.php?sel=my_orders{if $data.id_order}&amp;id_order={$data.id_order}{/if}{if $data.id_user_to}&amp;id_user_to={$data.id_user_to}{/if}">{$lang.cards.my_orders}</a>
							{/if}
						</td>
					</tr>
				</table>
			</div>
			{if $form.err}
            	<div class="error_msg">{$form.err}</div>
			{/if}
			<div style="padding-top: 15px;">
				<form action="ecards.php?sel=save_card" method="post">
				<input type="hidden" name="id_song" id="id_song" value="{$data.id_song}">
				<input type="hidden" name="id_card" id="id_card" value="{$data.id_card}">
				<input type="hidden" name="id_user_to" id="id_user_to" value="{$data.id_user_to}">
				{if $smarty.request.fixuser}<input type="hidden" name="fixuser" value="Y">{/if}
				{if $data.id_order}<input type="hidden" name="id_order" value="{$data.id_order}">{/if}
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td valign="top" class="text_head" style="padding-right: 5px;">{$lang.cards.card_image}:</td>
						<td valign="top">
							<a href="#" onclick="return GB_showImage('{$data.card_name|addslashes}', '{$data.image}')"><img src="{$data.thumb_image}" {* width="100" height="100" *} border="0" alt=""></a>
						</td>
					</tr>
					<tr>
						<td valign="top" class="text_head" style="padding: 10px 10px 0px 0px;">Recipient:</td>
						<td valign="top" style="padding: 10px 0px 0px 0px;">
							{if $smarty.get.fixuser != 'Y'}
                                                            <a href="#" class="normal-btn" onclick="javascript: return GB_show('', 'ecards.php?sel=select_user&amp;id_card={$data.id_card}&amp;id_order={$data.id_order}', 400, 500);">{$lang.cards.select_user}</a>
							{/if}
							<div id="result_div">
								{if $data.user_to_fname}
									<div style="padding: 10px; margin: 5px 0px 15px 0px; background-color: #ccc;">
										<table cellpadding="0" cellspacing="0">
											<tr>
												<td valign="middle"><img src="{$data.user_to_icon_path}" class="icon" alt=""></td>
												<td valign="middle" style="padding-left: 15px;"><b>{$data.user_to_fname}</b>, {$data.user_to_age} {$lang.home_page.ans}</td>
											</tr>
										</table>
									</div>
								{/if}
							</div>
						</td>
					</tr>
					{if $smarty.session.permissions.email_compose && $connected_status == CS_CONNECTED}
						<tr>
							<td valign="top" class="text_head" style="padding: 10px 10px 0px 0px;">{$lang.cards.card_header}:</td>
							<td valign="top" style="padding: 10px 0px 0px 0px;"><input type="text" name="card_header" value="{$data.card_header}" style="width: 300px;"></td>
						</tr>
						<tr>
							<td valign="top" class="text_head" style="padding: 10px 10px 0px 0px;">{$lang.cards.message}:</td>
							<td valign="top" style="padding: 10px 0px 0px 0px;">
								<div id="message_div">
									<textarea name="message" rows="5" cols="5" style="width: 500px; height: 300px;">{$data.message}</textarea>
								</div>
							</td>
						</tr>
					{else}
						<tr>
							<td valign="middle" class="text_head" style="padding: 10px 10px 0px 0px;">{$lang.cards.card_header}:</td>
							<td valign="middle" style="padding: 10px 0px 0px 0px;">
								<div style="background-color: #fff; z-index: 10; padding: 5px 5px 5px 10px; margin: 0px 0px 0px 0px; border: 1px solid #ccc;">
									{$data.card_header}
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" class="text_head" style="padding: 10px 10px 0px 0px;">{$lang.cards.message}:</td>
							<td valign="top" style="padding: 10px 0px 0px 0px;">
								<div id="message_div" style="background-color: #fff; z-index: 10; padding: 10px 10px 20px 10px; margin: 0px 0px 0px 0px; border: 1px solid #ccc;">
									{$data.message}
								</div>
								<div style="padding-top:3px;font-size:88%">({$lang.cards.must_connect_to_edit})</div>
							</td>
						</tr>
					{/if}
					{if $smarty.const.MM_ECARDS_MUSIC}
					<tr>
						<td valign="top" class="text_head" style="padding: 10px 10px 0px 0px;">{$lang.cards.card_music}:</td>
						<td valign="top" style="padding: 10px 0px 0px 0px;"><span id="song_span">{if $data.song_name}{$data.song_name}      {/if}</span><a href="#" onclick="javascript:return GB_show('', 'ecards.php?sel=choose_music', 600, 800);">{$lang.button.choose}</a></td>
					</tr>
					{/if}
					{if $data.price_raw > 0 || $smarty.const.MM_ECARDS_FREE == 0}
						<tr>
							<td valign="top" class="text_head" style="padding: 10px 10px 0px 0px;">{$lang.cards.price}:</td>
							<td valign="top" style="padding: 10px 0px 0px 0px;">{$data.price}  {$form.cur}</td>
						</tr>
					{/if}
					<tr>
						<td></td>
						<td valign="top">
							
								<div>
									<p class="basic-btn_next">
										<span>
										<input type="submit" value="{$lang.button.continue}" title="{$lang.button.continue}" />
										</span><b></b>
									</p>
									<div class="clear"></div>
								</div>
							
						</td>
					</tr>
				</table>
			</form>
			</div>
		</div>
	</div>
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}