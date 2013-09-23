{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple">
	{if $form.err}
		<div class="error_msg">{$form.err}</div>
	{/if}
	<div class="upgrade-member tcxf-ch-la">
		<div>
			<div class="callchat_icons">
				<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/telephone.png" alt="Call Me" title="Call Me"></a> 
				<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/Chat.png" alt="Chat With Me" title="Chat With Me" ></a>
			</div>
		</div>
		<div>
			<div class="_pleft20">
				<div class="hdr2">{$lang.section.request_call_back}</div>
				<p style="font-size:14px;" class="txtred"><b>"{$lang.request_call.box_text_2}"</b></p>
				<p style="font-size:16px;" class="txtpurple"><b>"{$lang.request_call.box_text_3}"</b></p>
				<div>
					<form method="post" action="request_call_back.php" class="violation-label">
						<div>
							<div style="padding-bottom:5px;">
								<label title="Name">
									{if $form.err_field.name}<span class="error">{/if}
									{$lang.request_call.name}:
									{if $form.err_field.name}</span>{/if}
								</label><br />
								<input type="text" name="name" class="text" value="{$data.name}" style="width:180px;" />
							</div>
							<div style="padding-bottom:5px;">
								<label title="Email">
									{if $form.err_field.email}<span class="error">{/if}
									{$lang.request_call.email}:
									{if $form.err_field.email}</span>{/if}
								</label><br />
								<input type="text" name="email" class="text" value="{$data.email}" style="width:180px;" />
							</div>
							<div style="padding-bottom:5px;">
								<label title="City or Town">
									{if $form.err_field.city}<span class="error">{/if}
									{$lang.request_call.city}:
									{if $form.err_field.city}</span>{/if}
								</label><br />
								<input type="text" name="city" class="text" value="{$data.city}" style="width:180px;" />
							</div>
							<div style="padding-bottom:5px;">
								<label title="Country">
									{if $form.err_field.country}<span class="error">{/if}
									{$lang.request_call.country}:
									{if $form.err_field.country}</span>{/if}
								</label><br />
								<input type="text" name="country" class="text" value="{$data.country}" style="width:180px;" />
							</div>
							<div style="padding-bottom:5px;">
								<label title="Phone Number incl Country Code">
									{if $form.err_field.phone}<span class="error">{/if}
									{$lang.request_call.phone}:
									{if $form.err_field.phone}</span>{/if}
								</label><br />
								<input type="text" name="phone" class="text" value="{$data.phone}" style="width:180px;" />
							</div>
							<div style="padding-bottom:7px;">
								<label title="Best Times To Call">
									{if $form.err_field.best_times}<span class="error">{/if}
									{$lang.request_call.best_times}:
									{if $form.err_field.best_times}</span>{/if}
								</label><br />
								<input type="text" name="best_times" class="text" value="{$data.best_times}" style="width:180px;" />
							</div>
							<div style="padding-bottom:10px;">
								<label title="I Am Interested In">
									{if $form.err_field.interest}<font class="error">{/if}
									{$lang.request_call.interest}:
									{if $form.err_field.interest}</font>{/if}
								</label><br />
								<div style="padding-left:5px;">
									<table cellpadding="0" cellspacing="5">
										<tr>
											<td><input type="radio" name="interest" value="Being" {if $data.interest == 'Being'}checked="checked"{/if} style="position:relative;top:1px;" /></td>
											<td><label title="Being A Member Of Thai Lady Date Finder">Being A Member Of Thai Lady Date Finder</label></td>
										</tr>
										<tr>
											<td valign="top"><input type="radio" name="interest" value="Coming" {if $data.interest == 'Coming'}checked="checked"{/if} style="position:relative;top:1px;" /></td>
											<td><label title="Coming To Bangkok And Meeting Eligible, Prescreened Thai Ladies Through Thai Lady Dating Events">Coming To Bangkok And Meeting Eligible, Prescreened Thai Ladies Through Thai Lady Dating Events</label></td>
										</tr>
										<tr>
											<td><input type="radio" name="interest" value="Both" {if $data.interest == 'Both'}checked="checked"{/if} style="position:relative;top:1px;" /></td>
											<td><label title="Both Programs">Both Programs</label></td>
										</tr>
									</table>
								</div>
							</div>
							<div style="padding-bottom:10px;">
								<label title="My Current Marital Status Is">
									{if $form.err_field.marital}<span class="error">{/if}
									{$lang.request_call.marital}:
									{if $form.err_field.marital}</span>{/if}
								</label><br />
								<div style="padding-left:5px;">
									<table cellpadding="0" cellspacing="5" class="norm-form-table">
										<tr>
											<td><input type="radio" name="marital" value="Single" {if $data.marital == 'Single'}checked="checked"{/if} style="position:relative;top:1px;" /></td>
											<td width="50"><label title="Single">Single</label></td>
											<td><input type="radio" name="marital" value="Divorced" {if $data.marital == 'Divorced'}checked="checked"{/if} style="position:relative;top:1px;" /></td>
											<td width="60"><label title="Divorced">Divorced</label></td>
											<td><input type="radio" name="marital" value="Separated" {if $data.marital == 'Separated'}checked="checked"{/if} style="position:relative;top:1px;" /></td>
											<td><label title="Separated">Separated</label></td>
										</tr>
									</table>
								</div>
							</div>
							<div style="padding-bottom:5px;">
								<label title="The Main Thing I Would Like To Know Is">
									{if $form.err_field.main_thing}<font class="error">{/if}
									{$lang.request_call.main_thing}:
									{if $form.err_field.main_thing}</font>{/if}
								</label><br />
								<textarea name="main_thing" style="width:300px; height:80px;">{$data.main_thing}</textarea>
							</div>
							{*
								<div style="padding-bottom:8px;">
									<label title="About Me And Why I am interested in these programs">
										{if $form.err_field.about_me}<font class="error">{/if}
										{$lang.request_call.about_me}:
										{if $form.err_field.about_me}</font>{/if}
									</label><br />
									<textarea name="about_me" style="width:300px; height:80px;">{$data.about_me}</textarea>
								</div>
							*}
							<p class="basic-btn_here _mleft15">
								<b></b><span><input type="submit" name="submit" value="Submit" /></span>
							</p>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}