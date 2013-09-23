{include file="$gentemplates/index_top.tpl"}
{strip}
<td class="main_cell">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td height="25">
                <div class="header">{$lang.section.send_testimonial}</div>
            </td>
        </tr>
        {if $form.err}
			<tr>
				<td><div class="error_msg">{$form.err}</div></td>
			</tr>
        {/if}
		{if $form.res}
			<tr>
				<td height="100">&nbsp;</td>
			</tr>
		{else}
			<tr>
				<td>
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td valign="top" width="120">
								<div id="left_section" style="margin-top:20px;"> 
									<img src="{$site_root}{$template_root}/images/nathamon.png" alt="" class="nathamon_img">
									<br />
									<div class="callchat_icons">
										<a href="{$site_root}/contact.php">
											<img src="{$site_root}{$template_root}/images/telephone.png" alt="Call Me" title="Call Me">
										</a>&nbsp;
										<a href="{$site_root}/contact.php">
											<img src="{$site_root}{$template_root}/images/Chat.png" alt="Chat With Me" title="Chat With Me">
										</a>
									</div>
								</div>
							</td>
							<td valign="top">
								<div style="padding:10px 10px;" class="text">
									<div class="txtpurple txtbig"><b>{$lang.send_testimonial.intro_text}</b></div>
									<div align="center" style="padding-top:20px;"><img src="{$site_root}{$template_root}/images/happy_couples.png" /></div>
								</div>
							</td>
							<td valign="top" width="400" align="right">
								<div class="yellow_box_top">
									<div class="yellow_box_btm">
										<div class="yellow_box_mid">
											<p style="padding:10px; font-size:14px;" class="txtred" align="center"><b>"{$lang.send_testimonial.intro_text_box}"</b></p>
											<div align="left" style="padding:2px 10px;">
												<form method="post" action="send_testimonial.php">
													<div>
														<div style="padding-bottom:5px;">
															<label title="Name">
																{if $form.err_field.name}<font class="error">{/if}
																	{$lang.send_testimonial.name}
																{if $form.err_field.name}</font>{/if}:
															</label><br />
															<input type="text" name="name" class="text" value="{$data.name}" style="width:180px;" />
														</div>
														<div style="padding-bottom:8px;">
															<label title="Email">
																{if $form.err_field.email}<font class="error">{/if}
																	{$lang.send_testimonial.email}
																{if $form.err_field.email}</font>{/if}:
															</label><br />
															<input type="text" name="email" class="text" value="{$data.email}" style="width:180px;" />
														</div>
														<div style="padding-bottom:5px;">
															<label title="What I would Like To Know Is">
																{if $form.err_field.testimonial}<font class="error">{/if}
																	{$lang.send_testimonial.testimonial}
																{if $form.err_field.testimonial}</font>{/if}:
															</label><br />
															<textarea name="testimonial" style="width:318px; height:150px;" >{$data.testimonial}</textarea>
														</div>
														<div class="center">
															<div class="btnwrap" style="width:100px;">
																<span><span>
																	<input type="submit" name="submit" value="Submit" class="btn_org" style="width:80px;" />
																</span></span>
															</div>
														</div>
													</div>
												</form>
											</div>
										</div>
									</div>
								</div>
							</td>
						</tr>
					{/if}
				</table>
			</td>
        </tr>
    </table>
    <!-- end main cell -->
</td>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}