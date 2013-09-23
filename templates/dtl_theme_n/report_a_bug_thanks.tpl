{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple">
	<div class="hdr2">{$lang.section.report_a_bug}</div>
	{if $form.err}
		<div class="error_msg">{$form.err}</div>
	{/if}
	{if $form.res}
		<div class="error_msg">{$form.res}</div>
	{/if} 
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
		<div class="tcxf-ch-la chw-60-40">                                      
			<div>	
				<div>
					<div style="padding-bottom:7px;"><b>{$lang.report_a_bug.thanks_text_1}</b></div>
					<div style="padding-bottom:12px;line-height:17px;">{$lang.report_a_bug.thanks_text_2}</div>
					<div style="padding-bottom:5px;"><b><a href="index.php">&raquo; Click Here To Go To The Home Page</a></b></div>
					<div align="center" style="padding-top:20px;"><img src="{$site_root}{$template_root}/images/happy_couples_med.png" /></div>
				</div>
			</div>		
			<div>
				<div>
					<p class="hdr2e">Meet Me Now Bangkok Co. Ltd.</p>
					<p style="padding-bottom:3px;">PO Box 1057</p>
					<p style="padding-bottom:3px;">Silom Post Office</p>
					<p style="padding-bottom:3px;">Bangkok</p>
					<p style="padding-bottom:3px;">THAILAND</p>
					<p style="padding-bottom:10px;">10504</p>
				</div>
				<div>
					<div>
						<div>											
							<p>USA: 1-866-601-7197 (Toll Free)</p>
							<p>Australia: 1300 912 009 (Toll Free)</p>
							<p>Thailand: +66 8 4921 8355</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}