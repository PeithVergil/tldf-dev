{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc">
	<p class="_extra-back"><a href="account.php">{$lang.account.back_to_my_account_page}</a></p>
	<div class="page-simple my-profile _extra upgrade-member tcxf-ch-la">
         <div>
		 <div class="callchat_icons2">
			<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/telephone.png" alt="Call Me" title="Call Me"></a> 
			<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/Chat.png" alt="Chat With Me" title="Chat With Me" ></a>
		</div>
		{if $auth.id_group == $smarty.const.MM_SIGNUP_GUY_ID}
			<div class="flag">
				<img src="{$site_root}{$template_root}/images/UnitedKingdomFlag.png" alt="United Kingdom Flag">
			</div>
			<div class="player">
				<script type="text/javascript">
					var playerhost = (("https:" == document.location.protocol) ? "https://meetmenowbangkok.s3.amazonaws.com/ezs3js/secure/" : "http://meetmenowbangkok.s3.amazonaws.com/ezs3js/player/");
					document.write(unescape("%3Cscript src='" + playerhost + "mp3/A0551FDA-EB49-350B-10C05AB386EC8718.js' type='text/javascript'%3E%3C/script%3E"));
				</script>
			</div>
			<div class="clear"></div>
		{elseif $auth.id_group == $smarty.const.MM_SIGNUP_LADY_ID}
			<div class="flag"> 
				<img src="{$site_root}{$template_root}/images/ThailandFlag.png" alt="" align="left">
			</div>
			<div class="player">
				<script type="text/javascript">
					var playerhost = (("https:" == document.location.protocol) ? "https://meetmenowbangkok.s3.amazonaws.com/ezs3js/secure/" : "http://meetmenowbangkok.s3.amazonaws.com/ezs3js/player/");
					document.write(unescape("%3Cscript src='" + playerhost + "mp3/521F6133-C567-B7D2-07E48400A8A93ED1.js' type='text/javascript'%3E%3C/script%3E"));
				</script>
			</div>
			<div class="clear"></div>
		{else}
			{*
				<div class="flag"><img src="{$site_root}{$template_root}/images/ThailandFlag.png" alt="Thailand Flag" align="left"></div>
				<div class="player">
					<script type="text/javascript">
						var playerhost = (("https:" == document.location.protocol) ? "https://meetmenowbangkok.s3.amazonaws.com/ezs3js/secure/" : "http://meetmenowbangkok.s3.amazonaws.com/ezs3js/player/");
						document.write(unescape("%3Cscript src='" + playerhost + "mp3/521DF658-F0E6-4B06-843F14379B26F655.js' type='text/javascript'%3E%3C/script%3E"));
					</script>
				</div>
			*}
		{/if}
 	</div>
	<div>
		<div class="box_info">			
			{*
				<div class="hdr2" title="{$header.update_account_page.buy_credits}">
					{$header.update_account_page.buy_credits}
					&nbsp;&nbsp;
					<span style="font-size:16px;">{$header.my_points}: <b>{$data.count}</b></span>
				</div>
			*}
			<div class="hdr2" title="{$header.update_account_page.add_credit_points}">
				{$header.update_account_page.add_credit_points}
			</div>
			<div class="box_inn">
				{*
					<div class="det-14-2" style="padding-bottom:10px;">
						{$header.update_account_page.toptext}
					</div>
				*}
				{if $form.err}
					<div class="error_msg">{$form.err}</div><br />
				{/if}
				{*
					<div style="position:relative;">
						<img title="Paypal" src="{$site_root}{$template_root}/images/paypal_verified.png" style="position:absolute; top:-60px; right:15px;" />
						{if $header.update_account_page.online_payment}
							<div><b>{$header.update_account_page.online_payment}</b></div>
						{/if}
				*}
				<div>
					<form action="payment.php" method="post" name="account_form" id="account_form">
						<input type="hidden" name="sel" id="sel" value="">
						<input type="hidden" name="paysys" id="paysys" value="">
						{*
							<p class="det-14-2" style="padding-right:10px;">
								{$header.update_account_page.point_to_usd}
								<span style="padding-left:30px;">{$header.update_account_page.one_connection}</span>
							</p>
							<p class="text" style="padding-right:10px;">
								{$header.update_account_page.add}:
								&nbsp;&nbsp;&nbsp;
								<input type="text" name="account_to_add" id="account_to_add" value="{$data.account_to_add}">&nbsp;&nbsp;&nbsp;
							</p>
							<br><br>
						*}
						<div class="credit-package">
							<div class="box-frame2">
								<div align="center">
									{if $cre_pack == 'custom'}
										<p class="head">Buy</p>
										<p>{$data.account_to_add} Points</p>
										<p><b>$ {$data.account_to_add}.00</b></p>
										<input type="hidden" name="account_to_add" id="account_to_add" value="{$data.account_to_add}">
									{elseif $cre_pack == 'bronze'}
										<p class="head">{$header.guy.buycon_bot_head2}</p>
										<p>{$header.guy.buycon_bot_head2_1}</p>
										<input type="hidden" name="pack_id" id="pack_id" value="1">
									{elseif $cre_pack == 'silver'}
										<p class="head">{$header.guy.buycon_bot_head3}</p>
										<p>{$header.guy.buycon_bot_head3_1}</p>
										<input type="hidden" name="pack_id" id="pack_id" value="2">
									{elseif $cre_pack == 'gold'}
										<p class="head">{$header.guy.buycon_bot_head4}</p>
										<p>{$header.guy.buycon_bot_head4_1}</p>
										<input type="hidden" name="pack_id" id="pack_id" value="3">
									{/if}
								</div>
							</div>
						</div>
						<div class="clear">&nbsp;</div>
						{* ONLINE PAYMENT OPTIONS*}
						<div class="space"></div>
						{include file="$gentemplates/payment_online.tpl"}
						{* OFFLINE PAYMENT OPTIONS *}
						{if $data.chosen_recurring != '1'}
							{assign var="opt_no" value="2"}
							{* ATM PAYMENT (LADIES ONLY) *}
							{if $data.gender == GENDER_FEMALE}
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
						{/if}
					</form>
					{* SMS PAYMENT *}
					{if $smssystems == 'yes'}
						<div style="height:1px; margin:10px 0px" class="delimiter"></div>
						<div><a href="sms_payment.php">{$lang.sms_payments.sms_payment}</a></div>
					{/if}
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
{literal}
function payment_online_submit(paysys) {
	f = document.account_form;
	f.sel.value = "save_account";
	f.paysys.value = paysys;
	f.submit();
}
function payment_offline_submit(paysys) {
	f = document.account_form;
	f.sel.value = 'account_' + paysys;
	f.paysys.value = paysys;
	f.submit();
}
{/literal}
</script>
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}