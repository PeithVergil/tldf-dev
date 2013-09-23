{strip}
{if $registered}
	<div id="reg_steps" style="padding-top:10px;">
		<div class="tcxf-ch-la chw-60-40">
			<div>
            	
				<div class="varified-step">
                		{if $pass}
                        	<div class="how-it-work-text">
                                	<p>Congrats, You have successfully Registered with us these are your login credentials.</p>
                                	<p>Username:  {$auth.login}</p>
                                    <p>Password:  {$pass}</p>
                                    
                                </div>
                        {/if}
                        
                    {if  $steps.upload_photo && $steps.confirm_email}
                    
                    {else}
					<ul>
                    {if $steps.upload_photo}{else}
						<li>
							<div class="{if $steps.upload_photo}step_comp{else}step_1{/if}">
                          		
								<label title="{$button.upload_photo_thai}">
									<a href="myprofile.php?sel=upload_photo">
										<span>{$button.upload_photo}</span>
									</a>
								</label>
							</div>
                            
						</li>{/if}
						{if $smarty.const.USE_PROFILE_EDIT_IN_SIGNUP_SANDBOX}
							<li>
								<div class="{if $steps.edit_application}step_comp{else}step_2{/if}">
									<label title="{$button.edit_application_thai}">
										<a href="myprofile.php?sel=edit_application">
											<span>{$button.edit_application}</span>
										</a>
									</label>
								</div>
							</li>
                            
                       
						{/if}
                        
                        <li></li>
                        {if $steps.confirm_email}
						
                        
                        {else} <li>
							<div class="{if $steps.confirm_email}step_comp{else}step_3{/if}">
								<label title="{$button.confirm_registration_thai}">
									<a href="myprofile.php?sel=confirm_email">
										<span>{$button.confirm_registration}</span>
									</a>
								</label>
                                
							</div>
                            
						</li>{/if}
					</ul>
                    
                    {/if}
					<div style="float:left; width:100px;" align="right">
						<div style="display:{if $steps.upload_photo && $steps.confirm_email}block{else}none{/if}">
							<img src="{$site_root}{$template_root}/images/arrow_animated-2.gif" alt="Click Here" title="Click Here">
						</div>
						&nbsp;
					</div>
					<div style="float:left; width:160px;padding-top:5px; " class="{if $steps.app_submit || $auth.application_submitted}step_comp{else}step_pen{/if}">
						<p class="varified-btn tooltip" title="{$button.submit_application_thai}">
							<a href="myprofile.php?sel=submit_application">
								{$button.submit_application}
							</a>
						</p>
					</div>
					<div class="clear"></div>
					<br><br>
					<div class="special-bonus">
						<label title="{$lang.section.special_introductory_bonus_thai}">
							{$lang.section.special_introductory_bonus}
						</label>
					</div>
					<br><br>
				</div>
				<br><br>
			</div>
			<div>
				<div class="how-it-work-text">
					{if $auth.application_submitted || $steps.app_submit}
						{$lang.section.applicant_inst_submitted}
					{else}
						<label title="{$lang.section.applicant_instructions_thai}">
							{$lang.section.applicant_instructions}
						</label>
					{/if}
				</div>
				<div style="padding-top:20px; padding-bottom:20px;">
					<img src="{$site_root}{$template_root}/images/happy_couples.png">
				</div>
			</div>
		</div>
	</div>
{/if}
{/strip}