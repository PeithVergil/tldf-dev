{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple">
	<!-- begin main cell -->
	{if $form.err}
		<div class="error_msg">{$form.err}</div>
	{/if}
	{if $form.res}
		<div class="error_msg">{$form.res}</div>
	{/if}
	<div class="upgrade-member tcxf-ch-la">
		<div>
			<div class="callchat_icons">
				<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/telephone.png" alt="Call Me" title="Call Me"></a> 
				<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/Chat.png" alt="Chat With Me" title="Chat With Me"></a>
			</div>
		</div>
		<div>
			<div class="_pleft20">
				<div class="hdr2e">
					{if $user_gender == 1}
						{$lang.section.request_info}
					{else}
						{$lang.section.request_info_t}
					{/if}
				</div>
				<p class="purple_text">
					{if $user_gender == 1}
						{$lang.request_info.box_instruction_1_e}
					{else}
						{$lang.request_info.box_instruction_1_t}
					{/if}
				</p>
				<p class="det-16">
					<b>
					{if $user_gender == 1}
						{$lang.request_info.box_instruction_2_e}
					{else}
						{$lang.request_info.box_instruction_2_t}
					{/if}
					</b>
				</p>
				<p class="txtblack">
					<b>
					{if $user_gender == 1}
						{$lang.request_info.box_instruction_3_e}
					{else}
						{$lang.request_info.box_instruction_3_t}
					{/if}
					</b>
				</p>
				<div class="cd_img clear">
					<p>
						<!--
						<img align="left" style="padding-right:10px;" src="{$site_root}{$template_root}/images/cd.png" alt="Thai Lady Date Finder CD Offer" />
						-->
						<b class="det-14">
							{if $user_gender == 1}
								{$lang.request_info.box_instruction_4_e}
							{else}
								{$lang.request_info.box_instruction_4_t}
							{/if}
						</b>
					</p>
				</div>
				<div class="chw-40-60 tcxf-ch-la">
					<div>
						<div class="_pright30">
							<div class="yellow_box_btm">
								<div class="yellow_box_mid">
									<div id="request_info_form">
										<form method="post" class="af-form-wrapper" action="http://www.aweber.com/scripts/addlead.pl">
											<div style="display: none;">
												<input type="hidden" name="meta_web_form_id" value="1903423597" />
												<input type="hidden" name="meta_split_id" value="" />
												<input type="hidden" name="listname" value="datethailadies" />
												<input type="hidden" name="redirect" value="http://www.aweber.com/thankyou-coi.htm?m=text" id="redirect_b69b5746fcb0780177edf38d02c53841" />
												<input type="hidden" name="meta_adtracking" value="DateThaiLadies_Info_" />
												<input type="hidden" name="meta_message" value="1" />
												<input type="hidden" name="meta_required" value="name,email" />
												<input type="hidden" name="meta_forward_vars" value="" />
												<input type="hidden" name="meta_tooltip" value="" />
											</div>
											<div id="af-form-1903423597" class="af-form">
												<div id="af-body-1903423597" class="af-body af-standards">
													<div class="af-element">
														<label class="previewLabel" for="awf_field-4824877">Name:</label>
														<div class="af-textWrap">
															<input id="awf_field-4824877" type="text" name="name" class="text" value="" style="width:220px;" />
														</div>
														<div class="af-clear"></div>
													</div>
													<div class="af-element">
														<label class="previewLabel" for="awf_field-4824878">Email: </label>
														<div class="af-textWrap">
															<input class="text" id="awf_field-4824878" type="text" name="email" value="" style="width:220px;" />
														</div>
														<div class="af-clear"></div>
													</div>
													<div class="af-element">
														<label class="previewLabel" for="awf_field-4824879">Street 1:</label>
														<div class="af-textWrap">
															<input type="text" id="awf_field-4824879" class="text" name="custom Street 1"value="" style="width:220px;" />
														</div>
														<div class="af-clear"></div>
													</div>
													<div class="af-element">
														<label class="previewLabel" for="awf_field-4824880">Street 2:</label>
														<div class="af-textWrap">
															<input type="text" id="awf_field-4824880" class="text" name="custom Street 2" value="" style="width:220px;" />
														</div>
														<div class="af-clear"></div>
													</div>
													<div class="af-element">
														<label class="previewLabel" for="awf_field-4824881">Town Or City:</label>
														<div class="af-textWrap">
															<input type="text" id="awf_field-4824881" class="text" name="custom Town Or City" value="" style="width:220px;" />
														</div>
														<div class="af-clear"></div>
													</div>
													<div class="af-element">
														<label class="previewLabel" for="awf_field-4824882">State:</label>
														<div class="af-textWrap">
															<input type="text" id="awf_field-4824882" class="text" name="custom State" value="" style="width:220px;" />
														</div>
														<div class="af-clear"></div>
													</div>
													<div class="af-element">
														<label class="previewLabel" for="awf_field-4824883">Country:</label>
														<div class="af-textWrap">
															<input type="text" id="awf_field-4824883" class="text" name="custom Country" value="" style="width:220px;" />
														</div>
														<div class="af-clear"></div>
													</div>
													<div class="af-element">
														<label class="previewLabel" for="awf_field-4824884">ZIP or Post Code:</label>
														<div class="af-textWrap">
															<input type="text" id="awf_field-4824884" class="text" name="custom ZIP or Post Code" value="" style="width:220px;" />
														</div>
														<div class="af-clear"></div>
													</div>
													<div class="af-element">
														<label class="previewLabel" for="awf_field-4824885">Phone - Include Country Code:</label>
														<div class="af-textWrap">
															<input type="text" id="awf_field-4824885" class="text" name="custom Phone - Include Country Code" value="" style="width:220px;" />
														</div>
														<div class="af-clear"></div>
													</div>
													<p class="basic-btn_here">
														<b></b><span>
															<input type="submit" name="submit" value="Submit" />
														</span>
													</p>
												</div>
											</div>
											<div style="display: none;"><img src="http://forms.aweber.com/form/displays.htm?id=jJwMzCxMzKyc7A==" alt="" /></div>
										</form>
									</div>
									<div class="stop_icon justify">{$lang.index_page.note}</div>
									{* <!--
									<div style="text-align:justify;">
										<p>{$lang.request_info.box_btm_text}</p>
									</div>
                                    -->*}
								</div>
							</div>
						</div> 
					</div>
					<div>
						<div style="padding-top:10px; padding-bottom:5px;">
							<b>
							{if $user_gender == 1}
								{$lang.request_info.intro_heading_e}
							{else}
								{$lang.request_info.intro_heading_t}
							{/if}
							</b>
						</div>
                        <div style="padding-top:10px;">
                           	{if $user_gender == 1}
                               	{$lang.request_info.intro_text_e}
                            {else}
                               	{$lang.request_info.intro_text_t}
                            {/if}
                        </div>
						<div align="center" style="padding:20px 0px;">
							<p><img src="{$site_root}{$template_root}/images/watch_presentation.png" alt="Watch the Dating Presentation" /></p>
							<p align="center" style="padding-top:20px;">
								<a href="dating_events.php" title="Thai Lady Dating Events&trade;"><b>Watch The Presentation To Learn More About Thai Lady Dating Events&trade;</b></a> <img src="{$site_root}{$template_root}/images/pointer.png" alt="" />
							</p>
						</div>
					</div>
					<!-- end main cell -->
				</div>
                <p>
					<input class="normal-btn" type="button" onclick="window.location.href='{$site_root}/events_booking.php'" value="{$lang.dating_events.book_now_package}" />
				</p>
			</div>
		</div>
	</div>
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}