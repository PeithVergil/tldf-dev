{strip}
<div class="user-recip">
	<div>
		<div class="recip-header">{$lang.giftshop.basket_for}:</div>
		<div class="recip-photo">
			<span>
				{if $recipient_data.icon_path}
					<img src="{$recipient_data.big_icon_path}" class="icon" alt="">
				{elseif $auth.gender == $smarty.const.GENDER_MALE}
					<img src="{$site_root}/uploades/icons/default_icon_female.gif" class="icon" alt="">
				{else}
					<img src="{$site_root}/uploades/icons/default_icon_male.gif" class="icon" alt="">
				{/if}    
			</span>
		</div>
		<div class="recip-info">
			<strong>{$recipient_data.fname}</strong>
			<span>
				{*
				{if $recipient_data.city} {$recipient_data.city}{/if}
				{if $recipient_data.region} $recipient_data.region}{/if}
				*}
				{$recipient_data.country}
			</span>
			{if $recipient_data.age}
				<span>{$recipient_data.age} {$lang.home_page.ans}</span>
			{/if}
		</div>
		{if $order_data.paid_status != '1' && ! $order_data.offline_payment_pending}
			<p class="basic-btn_here">
				<b></b>
				<span><input type="button" onclick="w=window.open('giftshop.php?sel=users_form', 'user_add', 'center=yes,height=450,width=610,resizable=no,scrollbars=yes,menubar=no,status=no'); w.focus();" value="{$lang.giftshop.basket_select_user}"></span>
			</p>
		{/if}
	</div>
</div>
{/strip}