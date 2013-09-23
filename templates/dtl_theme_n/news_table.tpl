{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc">
	<p class="_extra-back"><a href="index.php">{$lang.back_to_home_page}</a></p>
	<div class="page-simple">
		<div class="hdr2">
			{*<!--{$lang.section.news}-->*}
			Thai Lady Date Finder Features
		</div>
		<div class="error_msg">{$form.err}</div>
		<div id="news-page" class="tcxf-ch-la">
			<div style="width:580px;">
				{if !$news}
					<p>{$header_n.empty_news}</p>
				{else}
					{section name=s loop=$news}
						{*<!--
						<p class="title">
							{if $news[s].channel_name}<a href="{$news[s].channel_link}">{$news[s].channel_name}/</a>{/if}
							{if $news[s].title}<a href="{$news[s].news_link}">{$news[s].title}</a>{/if}
						</p>
						<p class="date">{$news[s].date_add}</p>
						-->*}
						<div class="news">
							{if $news[s].image}<img src="{$news[s].image}" border=1 vspace=5 hspace=5 style="float: left;" alt="">{/if}
							{$news[s].news_text}
						</div>
						<div class="divider">&nbsp;</div>
					{/section}
					{if $links}
						<div class="links">
							{foreach item=item from=$links}
								<span style="padding-right: 15px;"><a href="{$item.link}" {if $item.selected eq '1'} class="text_head"{/if}>{$item.name}</a></span>
							{/foreach}
						</div>
					{/if}
				{/if}
			</div>
			<div style="width:365px; margin-left:20px;">
				<div class="box-frame2">
					{* <h2 class="hdr2e">Right Widget</h2> *}
					<div class="regis-home-title" style="padding-left:10px;">
						<span style="color:#E13E8D">FREE</span> To Register & Connect<br>
						<span style="padding-left:95px;">With Singles</span>
					</div>
					<br>
					<div id="register-form-sidebar">
						{include file="$gentemplates/registration_form.tpl"}
						<script type="text/javascript">
						$.alerts.horizontalOffset = -200;
						$.alerts.verticalOffset = 25;
						</script>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}