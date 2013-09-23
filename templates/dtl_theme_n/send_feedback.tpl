{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple">
	{if $form.err}
		<div class="error_msg">{$form.err}</div>
	{/if}
	{if $form.res}
		<div>
			<div style="padding-top:10px;padding-bottom:10px;"><b>{$lang.report_a_bug.thanks_text}</b></div>
			<div style="padding-bottom:15px;"><b><a href="index.php">&raquo; Click Here To Go To The Home Page</a></b></div>
		</div>
	{else}
		<div class="upgrade-member tcxf-ch-la">
			<div>
				<div class="callchat_icons">
					<a href="{$site_root}/contact.php">
						<img src="{$site_root}{$template_root}/images/telephone.png" alt="Call Me" title="Call Me">
					</a>&nbsp;
					<a href="{$site_root}/contact.php">
						<img src="{$site_root}{$template_root}/images/Chat.png" alt="Chat With Me" title="Chat With Me">
					</a>
				</div>
			</div>
			<div>
				<div class="_pleft20">
					<h2 class="hdr2e">{$lang.section.send_feedback}</h2>
					<p style="padding:10px; font-size:14px;" class="txtred"><b>"{$lang.send_feedback.intro_text_box}"</b></p>
					<form method="post" action="send_feedback.php" class="violation-label">
						<div>
							<div style="padding-bottom:5px;">
								<label title="Name">
									{if $err_field.name}<span class="error">{/if}
									{$lang.send_feedback.name}
									{if $err_field.name}</span>{/if}:
								</label>
								<br />
								<input type="text" name="name" class="text" value="{$data.name}" style="width:180px;" />
							</div>
							<div style="padding-bottom:8px;">
								<label title="Email">
									{if $err_field.email}<span class="error">{/if}
									{$lang.send_feedback.email}
									{if $err_field.email}</span>{/if}:
								</label>
								<br />
								<input type="text" name="email" class="text" value="{$data.email}" style="width:180px;" />
							</div>
							<div style="padding-bottom:10px;">
								<p title="Please rate each of the following"> <b class="det-14">{$lang.send_feedback.rate}</b> </p>
								<div>
									<table cellpadding="0" cellspacing="3" class="first-bold">
										<tr>
											<td style="padding-bottom:8px">
												{if $err_field.question_1}<span class="error">{/if}
												{$lang.send_feedback.question_1}
												{if $err_field.question_1}</span>{/if}
											</td>
											<td valign="top">
												<select name="question_1" style="width:45px;">
													{section name=ques1 loop=6 step=1}
														{if $smarty.section.ques1.index == 0}
															<option value="">--</option>
														{else}
															<option value="{$smarty.section.ques1.index}" {if $data.question_1 == $smarty.section.ques1.index}selected="selected"{/if}>&nbsp;{$smarty.section.ques1.index}</option>
														{/if}
													{/section}
												</select>
											</td>
										</tr>
										<tr>
											<td style="padding-bottom:8px">
												{if $err_field.question_2}<span class="error">{/if}
												{$lang.send_feedback.question_2}
												{if $err_field.question_2}</span>{/if}
											</td>
											<td valign="top">
												<select name="question_2" style="width:45px;">
													{section name=ques2 loop=6 step=1}
														{if $smarty.section.ques2.index == 0}
															<option value="">--</option>
														{else}
															<option value="{$smarty.section.ques2.index}" {if $data.question_2 == $smarty.section.ques2.index}selected="selected"{/if}>&nbsp;{$smarty.section.ques2.index}</option>
														{/if}
													{/section}
												</select>
											</td>
										</tr>
										<tr>
											<td style="padding-bottom:8px">
												{if $err_field.question_3}<span class="error">{/if}
												{$lang.send_feedback.question_3}
												{if $err_field.question_3}</span>{/if}
											</td>
											<td valign="top">
												<select name="question_3" style="width:45px;">
													{section name=ques3 loop=6 step=1}
														{if $smarty.section.ques3.index eq 0}
															<option value="">--</option>
														{else}
															<option value="{$smarty.section.ques3.index}" {if $data.question_3 == $smarty.section.ques3.index}selected="selected"{/if}>&nbsp;{$smarty.section.ques3.index}</option>
														{/if}
													{/section}
												</select>
											</td>
										</tr>
										<tr>
											<td style="padding-bottom:8px">
												{if $err_field.question_4}<span class="error">{/if}
												{$lang.send_feedback.question_4}
												{if $err_field.question_4}</span>{/if} </td>
											<td valign="top">
												<select name="question_4" style="width:45px;">
													{section name=ques4 loop=6 step=1}
														{if $smarty.section.ques4.index eq 0}
															<option value="">--</option>
														{else}
															<option value="{$smarty.section.ques4.index}" {if $data.question_4 == $smarty.section.ques4.index}selected="selected"{/if}>&nbsp;{$smarty.section.ques4.index}</option>
														{/if}
													{/section}
												</select>
											</td>
										</tr>
										<tr>
											<td style="padding-bottom:5px">
												{if $err_field.question_5}<span class="error">{/if}
												{$lang.send_feedback.question_5}
												{if $err_field.question_5}</span>{/if}
											</td>
											<td valign="top">
												<select name="question_5">
													<option value="">--</option>
													<option value="Yes" {if $data.question_5 == 'Yes'}selected="selected"{/if}>Yes</option>
													<option value="No" {if $data.question_5 == 'No'}selected="selected"{/if}>No</option>
												</select>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div style="padding-bottom:5px;">
								<label title="How Can We Improve?">
									{if $err_field.comments}<span class="error">{/if}
									{$lang.send_feedback.comments}
									{if $err_field.comments}</span>{/if}:
								</label>
								<br />
								<textarea name="comments" rows="8" cols="60">{$data.comments}</textarea>
							</div>
							<div class="basic-btn_here _mleft30">
								<b></b>
								<span><input type="submit" name="submit" value="Submit" /></span>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	{/if}
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}