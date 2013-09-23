{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple">
	{if $form.err}<div class="error_msg">{$form.err}</div>{/if}
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
				<h2 class="hdr2e">{$lang.section.report_a_bug}</h2>
				<p><b class="det-14">"{$lang.report_a_bug.box_text_2}"</b></p>
				<form method="post" action="report_a_bug.php" class="violation-label">
					<div>
						<div style="padding-bottom:7px;">
							<label title="Name">
								{if $form.err_field.name}<span class="error">{/if}
								{$lang.report_a_bug.name} <span class="mandatory">*</span>:
								{if $form.err_field.name}</span>{/if}
							</label>
							<br />
							<input type="text" name="name" class="text" value="{$data.name}" style="width:180px;" />
						</div>
						<div style="padding-bottom:5px;">
							<label title="Email">
								{if $form.err_field.email}<span class="error">{/if}
								{$lang.report_a_bug.email} <span class="mandatory">*</span>:
								{if $form.err_field.email}</span>{/if}
							</label>
							<br />
							<input type="text" name="email" class="text" value="{$data.email}" style="width:180px;" />
						</div>
						<div style="padding-bottom:5px;">
							<label title="Phone Number incl Country Code">
								{if $form.err_field.phone}<span class="error">{/if}
								{$lang.report_a_bug.phone}:
								{if $form.err_field.phone}</span>{/if}
							</label>
							<br>
							<input type="text" name="phone" class="text" value="{$data.phone}" style="width:180px;" />
						</div>
						<div style="padding-bottom:8px;">
							<label title="Bug Description">
								{if $form.err_field.description}<span class="error">{/if}
								{$lang.report_a_bug.description} <span class="mandatory">*</span>:
								{if $form.err_field.description}</span>{/if}
							</label>
							<br>
							<textarea name="description" style="width:330px; height:150px;">{$data.description}</textarea>
						</div>
						<p class="basic-btn_here _mleft15">
							<b></b><span><input type="submit" name="submit" value="Submit" /></span>
						</p>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}