{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
    <!-- begin main cell -->
    <table width="100%" border="0" cellpadding="0" cellspacing="1">
        <tr>
            <td valign="top">
                <div class="header" style="margin: 0px; height: 25px;"> {$lang.section.shout_edit} </div>
            </td>
        </tr>
        {if $form.err}
        <tr>
            <td><div class="error_msg">{$form.err}</div></td>
        </tr>
        {/if}
        <tr>
            <td valign="top" class="text">
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr>
						<td valign="top" style="padding-left: 15px; padding-top: 15px;">
							<form name="shout_form" id="shout_form" action="shoutbox.php?sel=save" method="post" enctype="multipart/form-data">
								<input type="hidden" name="id" value="{$form.id}" />
								<table border="0" cellpadding="3" cellspacing="0">
									<tr>
										<td height="30" class="text_head">{$lang.shout.shout_text}:&nbsp;&nbsp;</td>
										<td>
											<input type="text" name="text" value="{$form.text}" style="width:300px" maxlength="30">
										</td>
									</tr>
									<tr>
										<td>&nbsp;</td>
										<td>
											<table cellpadding="0" cellspacing="0" align="center">
												<tr>
													<td>
														<div class="center">
															<div class="btnwrap" style="width:100px;padding:5px;">
																<span><span>
																	<input type="submit" class="btn_org" style="width:80px;" value="{$lang.button.submit}" />
																</span></span>
															</div>
														</div>
													</td>
													<td>
														<div class="center">
															<div class="btnwrap" style="width:100px;padding:5px;">
																<span><span>
																	<input type="button" class="btn_org" style="width:80px;" value="{$lang.button.cancel}" onclick="javascript:document.location.href='shoutbox.php'" />
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
				</table>
            </td>
        </tr>
    </table>
    <!-- end main cell -->
</td>
{include file="$gentemplates/index_bottom.tpl"}