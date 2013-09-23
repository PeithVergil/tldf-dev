{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple" id="registration_edit">
	{if $form.err}
		<div class="error_msg">{$form.err}</div>
	{/if}
	<div class="upgrade-member tcxf-ch-la">
		<div>
			<div class="callchat_icons">
				<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/telephone.png" alt="Call Me" title="Call Me"></a> <a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/Chat.png" alt="Chat With Me" title="Chat With Me" ></a>
			</div>
		</div>
		<div>
			<h2 class="hdr2e">{$header.title}</h2>
			<div class="text">
				<br/>
				{if $form.confirmed}
					{$lang.confirm.help1}<br /><br />
				{/if}
				{if !$registered}
					{$lang.confirm.help2} <br/><br/>
					{$lang.confirm.login_here} <br/><br/>
				{/if}
			</div>
			<div class="basic-btn_here">
				<b></b><span>
				<input type="button" onclick="window.location.href='index.php?sel=login';" value="{$lang.top.login}" />
				</span>
			</div>
		</div>
	</div>
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}