{include file="$gentemplates/index_top_popup.tpl"}
				<td width="100%" valign="top">
					<div class="hdr2e">{$lang.home_page.lost_password}</div>
					<div class="det-14-2">{$header.toptext}</div>
					<br>
					{if $form.err}
						<div class="error_ajax">{$form.err}</div>
						<br>
					{/if}
					<form method="post" action="lost_pass.php" enctype="multipart/form-data" name="lost_pass">
						<input type="hidden" name="sel" value="send">
						<table cellspacing="0" cellpadding="0">
							<tr>
								<td height="35px" class="text_head">&nbsp;{$header.email}:&nbsp;&nbsp;&nbsp;</td>
								<td height="35px">
									<input type="text" style="width:300px" name="email">
								</td>
							</tr>
							<tr>
								<td height="35px" align="left" colspan="2"><a href="mailto:{$form.site_email}">{$header.contact}</a></td>
							</tr>
							<tr>
								<td colspan="2" align="center">
									<input type=hidden name=send value=1>
									<table cellpadding="0" cellspacing="0">
										<tr>
											<td>
												<div class="center">
													<div class="btnwrap" style="width:90px;">
														<span><span>
															<input type="button" class="btn_org" style="width:70px;" onclick="document.lost_pass.submit();" value="{$button.send}" />
														</span></span>
													</div>
												</div>
											</td>
											<td style="padding-left:10px;">
												<div class="center">
													<div class="btnwrap" style="width:90px;">
														<span><span>
															<input type="button" class="btn_org" style="width:70px;" onclick="opener.focus();window.close();" value="{$button.close}" />
														</span></span>
													</div>
												</div>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</form>
				</td>
			</tr>
			<tr>
				<td height="100%">&nbsp;</td>
			</tr>
		</table>
	</div>
</body>
</html>