{include file="$gentemplates/index_top.tpl"}
 <div class="toc page-simple">
	<!-- begin main cell -->
	<div class="upgrade-member tcxf-ch-la">
		<div>
			<div class="callchat_icons">
				<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/telephone.png" alt="Call Me" title="Call Me"></a>
				<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/Chat.png" alt="Chat With Me" title="Chat With Me" ></a>
			</div>
		</div>
		<div>
			<div class="hdr2e">{$lang.payment.lift_up_service}</div>
			<div style="padding-top: 10px;">
				{if $form.page eq 'list'}
					<div style="padding-bottom: 10px;" class="text">
						{$lang.pays.service_price}: {$form.price}&nbsp;{$form.cur}
					</div>
					{if $form.service_available eq 1}
						<table cellpadding="5" cellspacing="0">
							<tr>
								<td><font class="text_head">{$lang.payment.your_credit}:</font></td>
								<td><font class="text">&nbsp;{$form.user_account}&nbsp;{$form.cur}</font></td>
							</tr>
							<tr>
								<td><font class="text_head">{$lang.payment.service_costs}:</font></td>
								<td><font class="text">&nbsp;{$form.price}&nbsp;{$form.cur}</font></td>
							</tr>
							<tr>
								<td colspan="2"><input type="button" value="{$lang.button.lift_up_now}" onclick="document.location.href='payment.php?sel=service&service=lift_up_act'"></td>
							</tr>
						</table>
					{else}
						<div>{$lang.payment.service.lift_up.not_enough_account}</div>
					{/if}
				{elseif $form.page eq 'done'}
					<div>{$lang.payment.service.lift_up.your_profile_lifted}</div>
				{/if}
			</div>
		</div>
	</div>
	<!-- end main cell -->
</div>
{include file="$gentemplates/index_bottom.tpl"}