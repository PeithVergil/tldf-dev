{if !$VideoPopup}
	<div id="popup-menu">
		<div><a class="close-popup" title="Close" href="javascript:void();">&nbsp;</a></div>
		{if $howWorks}
			<ul class="popup-list">
				{foreach item=item from=$howWorks}
					<li>
						<div class="title hdr5">{if $how_works_lang eq 1}{$item.title}{else}{$item.title_t}{/if}</div>
						<div class="content">
							{if $how_works_lang eq 1}
								<div>{$item.description}</div>
								{if $item.video}
									<a class="iframe_colorbox video-item" href="how_it_works.php?sel=video&id={$item.id}">Click To View</a>
								{/if}
							{else}
								{$item.description_t}
								{if $item.video_t}
									<p class="video-item">
										<a class="iframe_colorbox" href="how_it_works.php?sel=video&id={$item.id}">
											<span><img src="{$site_root}{$template_root}/imgs/video-icon-2.png" alt="Video" /></span>
											<span>Click To View</span>
										</a>
									</p>
									<div class="clear"></div>
								{/if}
							{/if}
						</div>
					</li>
				{/foreach}
			</ul>
		{/if}
	</div>
{else}
	<!doctype html>
	<html>
	<head>
	<link rel="stylesheet" type="text/css" href="{$site_root}{$template_root}/css/tldf_style.css?v=0001" media="all">
	</head>
	<body>
		<div id="video-popup">
			<div class="hdr5">
				{if $how_works_lang eq 1}{$data.title}{else}{$data.title_t}{/if}
			</div>
			<div class="inline_content">
				{if $how_works_lang eq 1}{$data.video}{else}{$data.video_t}{/if}
			</div>
		</div>
	</body>
	</html>
{/if}