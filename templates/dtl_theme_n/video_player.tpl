{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple">
	{if $form.err}
		<div class="error_msg">{$form.err}</div>
		<div style="height:250px;">&nbsp;</div>
	{else}
		<div class="upgrade-member tcxf-ch-la">
			<div>
				<div class="callchat_icons3">
					<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/telephone.png" alt="Call Me" title="Call Me"></a>
					<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/Chat.png" alt="Chat With Me" title="Chat With Me" ></a>
				</div>
			</div>
			<div>
				<div class="_pleft20">
					<h2 class="hdr2e">{$form.video_title}</h2>
					<div>&nbsp;</div>
					<div class="filmbox">
					<script type="text/javascript">
						var playerhost = (("https:" == document.location.protocol) ? "https://meetmenowbangkok.s3.amazonaws.com/ezs3js/secure/" : "http://meetmenowbangkok.s3.amazonaws.com/ezs3js/player/");
						document.write(unescape("%3Cscript src='" + playerhost + "flv/{$form.video_id}.js?t="+(Math.random() * 99999999)+"' type='text/javascript'%3E%3C/script%3E"));
					</script>
					</div>
				</div>
			</div>
		</div>
	{/if}
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}