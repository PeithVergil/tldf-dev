{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple">
	<h2 class="hdr2e">{$lang.section.contact_us}</h2>
	{if $form.err}
		<div class="error_msg">{$form.err}</div>
	{/if}
	<table width="100%" cellpadding="0" cellspacing="0" class="norm-form-table">
		<tr>
			<td valign="top">
				<form action="contact.php" method="post" name="mailbox_write">
					<input type="hidden" name="sel" value="contact" />
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td height="35" align="left" class="text_head">
								{if $form.err_field.fname}<span class="error">{/if}
								{$header.name} <span class="mandatory">*</span>:
								{if $form.err_field.fname}</span>{/if}
							</td>
							<td colspan=2 height="35" align="left">
								<input type="text" name="fname" maxlength="50" value="{$data.fname}" style="width:300px">
							</td>
						</tr>
						<tr>
							<td height="35" align="left" class="text_head">
								{if $form.err_field.email}<span class="error">{/if}
								{$header.email} <span class="mandatory">*</span>:
								{if $form.err_field.email}</span>{/if}
							</td>
							<td colspan=2 height="35" align="left">
								<input type="text" name="email" maxlength="100" value="{$data.email}" style="width:300px">
							</td>
						</tr>
						<tr>
							<td height="35" align="left" class="text_head">
								{if $form.err_field.subject}<span class="error">{/if}
								{$header.subject} <span class="mandatory">*</span>:
								{if $form.err_field.subject}</span>{/if}
							</td>
							<td colspan=2 height="35" align="left">
								<input type="text" name="subject" maxlength="100" value="{$data.subject}" style="width:300px">
							</td>
						</tr>
						<tr>
							<td class="text_head" align="left" valign="top" style="padding-top:10px;">
								{if $form.err_field.message}<span class="error">{/if}
								{$header.message} <span class="mandatory">*</span>:
								{if $form.err_field.message}</span>{/if}
							</td>
							<td colspan="2" align="left">
								<textarea name="message" class="blackborder" style="width:300px; height:150px;">{$data.message}</textarea>
							</td>
						</tr>
						<tr>
							<td class="text_head">
								{if $form.err_field.captcha}<span class="error">{/if}
								{$header.security_code} <span class="mandatory">*</span>:
								{if $form.err_field.captcha}</span>{/if}
							</td>
							<td align="left"><img src="{$form.kcaptcha}" alt="{$header.security_code}"></td>
							<td align="left">
								<input type="text" style="width:120px" name="keystring">
							</td>
						</tr>
						<tr>
							<td></td>
							<td colspan="2" style="padding:7px;" align="center">
								<p class="basic-btn_here">
									<b></b><span><input type="button" onclick="document.mailbox_write.submit();" value="{$button.send}" /></span>
								</p>
							</td>
						</tr>
					</table>
				</form>
			</td>
			<td width="50"> </td>
			<td valign="top" width="430">
				<div class="norm-box contact-box">
					<div class="det-14">
						<h3 class="hdr2">Meet Me Now Bangkok Co. Ltd.</h3>
						<p><strong>33/7 Soi Pipat 2</strong></p>
						<p><strong>Silom Road Bangkok</strong></p>
						<p><strong>Thailand 10500</strong></p>
					</div>
					<div class="det-14">
						<p>Phone: <strong>+66 (0) 2 667 0068</strong></p>
						<p>Fax: <strong>+66 (0) 2 667 0069</strong></p>
						<p>E-mail: <strong>admin@thailadydateﬁnder.com</strong></p>
					</div>
				</div>
				<div class="norm-box">
					<!-- Google Map -->
					<iframe width="430" height="230" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.co.th/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=soi+pipat+2&amp;aq=&amp;sll=13.0376,101.491373&amp;sspn=33.120413,33.618164&amp;ie=UTF8&amp;hq=&amp;hnear=Soi+Phiphat+2,+Silom,+Bang+Rak,+Bangkok+10500&amp;t=m&amp;ll=13.72569,100.532809&amp;spn=0.004086,0.004104&amp;z=14&amp;output=embed"></iframe>
					<br />
					<small><a href="http://maps.google.co.th/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q=soi+pipat+2&amp;aq=&amp;sll=13.0376,101.491373&amp;sspn=33.120413,33.618164&amp;ie=UTF8&amp;hq=&amp;hnear=Soi+Phiphat+2,+Silom,+Bang+Rak,+Bangkok+10500&amp;t=m&amp;ll=13.72569,100.532809&amp;spn=0.004086,0.004104&amp;z=14" style="color:#0000FF;text-align:left">View Larger Map</a></small>
				</div>
			</td>
		</tr>
	</table>
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}