{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="container">	
	<div class="mainDescription">
		{$lang.payment.$usr_gender.buycon_mainDescription}
	</div>
	<div class="secondaryDescription">
		<div class="contentTable">
			<div class="tableHeader">
				<div class="column1 fl">
					<h2>{$lang.payment.$usr_gender.platinum_connections_header}</h2>
				</div>
				<div class="column2 tableHeaderImg fr">
				</div>
			</div>
			<ul class="tableListings">
				<li>
					<div class="column1 gray fl">
						<span>{$lang.platinum_match.$usr_gender.feature_1}</span>
					</div>
					<div class="column2 lightYellow tableTick fr">
					</div>
				</li>
				<li>
					<div class="column1 fl">
						<span>{$lang.platinum_match.$usr_gender.feature_2}</span>
					</div>
					<div class="column2 tableTick fr">
					</div>
				</li>
				<li>
					<div class="column1 gray fl">
						<span>{$lang.platinum_match.$usr_gender.feature_3}</span>
					</div>
					<div class="column2 lightYellow tableTick fr">
					</div>
				</li>
				<li>
					<div class="column1 fl">
						<span>{$lang.platinum_match.$usr_gender.feature_4}</span>
					</div>
					<div class="column2 tableTick fr">
					</div>
				</li>
				<li>
					<div class="column1 gray fl">
						<span>{$lang.platinum_match.$usr_gender.feature_5}</span>
					</div>
					<div class="column2 lightYellow tableTick fr">
					</div>
				</li>
				<li>
					<div class="column1 fl">
						<span>{$lang.platinum_match.$usr_gender.feature_6}</span>
					</div>
					<div class="column2 tableTick fr" style="background-position: 130px 13px;">
					</div>
				</li>
				<li>
					<div class="column1 gray fl">
						<span>{$lang.platinum_match.$usr_gender.feature_7}</span>
					</div>
					<div class="column2 lightYellow tableTick fr">
					</div>
				</li>
				<li>
					<div class="column1 fl">
						<span>{$lang.platinum_match.$usr_gender.feature_8}</span>
					</div>
					<div class="column2 tableTick fr">
					</div>
				</li>
				{if $usr_gender == 'lady'}
					<li>
						<div class="column1 gray fl">
							<span>{$lang.platinum_match.$usr_gender.feature_9}</span>
						</div>
						<div class="column2 lightYellow tableTick fr">
						</div>
					</li>
					<li>
						<div class="column1 fl">
							<span>{$lang.platinum_match.$usr_gender.feature_10}</span>
						</div>
						<div class="column2 tableTick fr">
						</div>
					</li>
					<li>
						<div class="column1 gray fl">
							<span>{$lang.platinum_match.$usr_gender.feature_11}</span>
						</div>
						<div class="column2 lightYellow tableTick fr extraHeight">
						</div>
					</li>
					<li>
						<div class="column1 fl">
							<span>{$lang.platinum_match.$usr_gender.feature_12}</span>
						</div>
						<div class="column2 tableTick fr">
						</div>
					</li>
					<li>
						<div class="column1 gray fl">
							<span>{$lang.platinum_match.$usr_gender.feature_13}</span>
						</div>
						<div class="column2 lightYellow tableTick fr">
						</div>
					</li>
					<li>
						<div class="column1 fl">
							<span>{$lang.platinum_match.$usr_gender.feature_14}</span>
						</div>
						<div class="column2 tableTick fr">
						</div>
					</li>
					<li>
						<div class="column1 gray fl">
							<span>{$lang.platinum_match.$usr_gender.feature_15}</span>
						</div>
						<div class="column2 lightYellow tableTick fr">
						</div>
					</li>
					<li>
						<div class="column1 fl">
							<span>{$lang.platinum_match.$usr_gender.feature_16}</span>
						</div>
						<div class="column2 tableTick fr">
						</div>
					</li>
					<li>
						<div class="column1 gray fl">
							<span>{$lang.platinum_match.$usr_gender.feature_17}</span>
						</div>
						<div class="column2 lightYellow tableTick fr extraHeight" >
						</div>
					</li>
					<li>
						<div class="column1 fl">
							<span>{$lang.platinum_match.$usr_gender.feature_18}</span>
							</div>
							<div class="column2 tableTick fr extraHeight" style="background-position: 130px 20px;">
							</div>
					</li>
					<li>
						<div class="column1 gray fl">
							<span>{$lang.platinum_match.$usr_gender.feature_19}</span>
						</div>
						<div class="column2 lightYellow tableTick fr">
						</div>
					</li>
					<li>
						<div class="column1 fl">
							<span>{$lang.platinum_match.$usr_gender.feature_20}</span>
						</div>
						<div class="column2 tableTick fr">
						</div>
					</li>
				{/if}
			</ul>
		</div>
	</div><!-- end of secondary description -->
</div><!-- end of container -->
<div class="bottom-sectionOuter">
	<div class="bottom-sectionWrapper">
		<div class="paymentMethods">
			<div class="paymentBoxOne">
				<img src="{$site_root}{$template_root}/images/paypal-star.png" alt="">
			</div>
			<div class="paymentBoxTwo">
				<img src="{$site_root}{$template_root}/images/paypal.png" alt="">
			</div>
			<div class="paymentBoxThree">
				<div class="addCartbox">
					<p>{$lang.platinum_match.$usr_gender.feature_head_1}</p>
					<div class="yellowBtn">
						{if $disable_platinum}
							<a href="javascript:;">Apply Now</a>
						{else}
							<a href="payment.php?sel=pay_platinum">Apply Now</a>
						{/if}
					</div> 
					<p class="numbersApply">{$lang.platinum_match.$usr_gender.feature_head_1_1}</p>
				</div>
				{if $usr_gender == 'lady'}
					<div class="addCartbox">
						<p>{$installment_cnt}</p>
						<div class="yellowBtn">
							<a href="payment.php?sel=pay_installments">Apply Now</a>
						</div> 
						<p class="numbersApply">{$lang.platinum_match.$usr_gender.feature_head_2_1}</p>
					</div>
				{/if}
			</div><!-- end of payment box three -->
		</div><!--end of payment methods -->
		<div class="yellowBigBox">
			<div class="yellowBoxWrap">
				<ul>
					<li><p>{$lang.payment.$usr_gender.buycon_pop_head1}</p></li>
					<li>
						<img src="{$site_root}{$template_root}/images/kbankLogo.png" alt="Kasikorn Bank" title="Kasikorn Bank" />
					</li>
					<li>{$lang.payment.$usr_gender.buycon_pop_text1}</li>
				</ul> 
			</div>
			<div class="yellowBoxWrap">
				<ul>
					<li><p>{$lang.payment.$usr_gender.buycon_pop_head2}</p></li>
					<li style="font-size: 18px;">{$lang.payment.$usr_gender.buycon_pop_head2_1}</li>
					<li>{$lang.payment.$usr_gender.buycon_pop_head2_2}</li>
					<li>{$lang.payment.$usr_gender.buycon_pop_head2_3}</li>
					<li>{$lang.payment.$usr_gender.buycon_pop_head2_4}<a href="mailto:admin@thailadydatefinder.com">{$lang.payment.$usr_gender.buycon_pop_head2_5}</a></li>
				</ul>
			</div>
		</div>
	</div> <!-- end of bottom-sectionWrapper -->
</div><!-- end of bottom-sectionOuter -->
<!-- colorbox -->
<div style="display:none">
	<div id="inline_application" class="inline_content">
		{include file="$gentemplates/inline_platinum_match.tpl"}
	</div>
</div>
<div style="display:none">
	<div id="inline_here_why" class="inline_content">
		<div style="padding-right:15px;">
			<p>In traditional times, Thai ladies were often introduced to their potential partners by family, friends and mentors. Today, in Thai culture a personal introduction by a trusted friend is still the best way to open doors and make the right impression.</p>
			<p>Platinum Matching&trade; gives you a great advantage in getting the information you need to make the right decision for your future.</p>
			<p>And with my team in your corner, you'll be introduced to your ideal ladies in the best possible way.</p>
			<br />
			<div align="center">
				{if $usr_gender == 'guy'}
					<div class="btn-new">
						{*<!--<a href="payment.php?sel=save_1&period_id={$smarty.const.MM_PLATINUM_GUY_PERIOD_ID}&x=112&y=29">-->*}
						<a href="payment.php?sel=pay_platinum"><span>Add To Cart</span></a>
					</div>
				{else}
					<div class="btn-new">
						{*<!--<a href="payment.php?sel=save_1&period_id={$smarty.const.MM_PLATINUM_LADY_PERIOD_ID}">-->*}
						<a href="payment.php?sel=pay_platinum"><span>Add To Cart</span></a>
					</div>
				{/if}
			</div>
		</div>
	</div>
</div>
<div style="display:none">
	<div id="inline_contact" class="inline_content">
		{include file="$gentemplates/inline_contact.tpl"}
	</div>
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}