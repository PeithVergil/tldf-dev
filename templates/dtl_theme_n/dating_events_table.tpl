{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple">
	{if $form.err}
		<div class="error_msg">{$form.err}</div>
	{/if}
	{*
		<div class="chekoutfilm_icons" style="padding:0px 0px 0px 255px;">
			<h2 class="hdr2e">{$lang.section.dating_events}</h2>
			<div class="filmbox">
				<script type="text/javascript">
					var playerhost = (("https:" == document.location.protocol) ? "https://meetmenowbangkok.s3.amazonaws.com/ezs3js/secure/" : "http://meetmenowbangkok.s3.amazonaws.com/ezs3js/player/");
					document.write(unescape("%3Cscript src='" + playerhost + "flv/0C0D9C2E-E04D-9C78-D7213CC8E3816D30.js?t="+(Math.random() * 99999999)+"' type='text/javascript'%3E%3C/script%3E"));
				</script>
			</div>
			<p class="_acenter" style="padding-right:90px; padding-top:10px;">
				<input type="button" class="normal-btn" value="Express Your Interest Now" onclick="window.location.href='{$form.express_interest}'">
			</p>
			<div class="tcxf-ch-la" style="padding:20px 0px;"> {$lang.dating_events.intro_text} </div>
		</div>
	*}
	<h2 class="hdr2e">{$lang.section.dating_events}</h2>
	<div class="tcxf-ch-la upgrade-member">
		<div>
			<div class="callchat_icons2">
				&nbsp;
			</div>
		</div>
		<div>
			<div class="det-14-2">Go Beyond Online Dating...</div>
			<div id="dating-wrap">
			<iframe width="740" height="580" src="http://www.youtube.com/embed/IrsCL0LpsCY" frameborder="0" allowfullscreen="" 
				style="
				    position: relative;
				    left: -40px;
				    top: -49px; ">
			</iframe>
				{* <div id="dating-slide">
				
					<ul>
						<li class="tcxf-ch-la" style="background: url({$site_root}{$template_root}/images/DatingEventsSlide.jpg) 40px -20px no-repeat;">
							<div class="hdr3">
								"Did You Know You Can Meet & Date<br />
								Selected Platinum Ladies On Your Own<br />
								Personalized Dating Events Tour?"
							</div>
						</li>
						<li class="tcxf-ch-la" style="background: url({$site_root}{$template_root}/images/DatingEventsSlide.jpg) 40px -20px no-repeat;">
							<div class="hdr3">
								"For Those Guys Who Want To Effortlessly<br />
								Find The Most Compatible & Beautiful<br />
								Partner & Enjoy The Process...<br />
								It All Starts Here..."
							</div>
						</li>
						<li class="tcxf-ch-la" style="background: url({$site_root}{$template_root}/images/DatingEventsSlide.jpg) 40px -20px no-repeat;">
							<div class="hdr3">
								"A Simple Coffee Or A Romantic Evening.<br />
								Whether You Want To Meet One Or One<br />
								Hundred Ladies - We Have A Program To<br />
								Suit You."
							</div>
						</li>
					</ul>
				</div>*}
			</div>
			<p class="_acenter" style="padding-right:20px;">
				<a class="express_interest_colorbox normal-btn" href="express_interest.php?type=ajax">Express Interest Now</a>
			</p>
		</div>
	</div>
	<div id="dating-bot" class="bottom-section trans">
		<div class="tcxf-ch-la">
			<div class="dating-faq-wrap">
				<table width="98%" cellpadding="0" cellspacing="0">
					<tr>
						<td><div class="hdr3">FAQ's</div></td>
						<td align="right">
							<b><a class="ajax_colorbox" href="dating_events.php?sel=question">Ask Your Own Question</a></b>
						</td>
					</tr>
				</table>
				<ul id="dating-faq">
					{foreach item=item from=$datingfaqs}
						<li style="padding-top:5px;">
							<div class="title hdr5">{$item.title}</div>
							<div class="content">{$item.body}</div>
						</li>
					{/foreach}
				</ul>
			</div>
			<div class="dating-vid-wrap">
				<div class="video-item">
					<div>
						<a class="video_dating_events" href="http://player.vimeo.com/video/48432379">
							<img src="{$site_root}{$template_root}/imgs/dating-events-video1-150.jpg" alt="Video" />
						</a>
						<p class="hdr5">{$lang.dating_video.title1}</p>
						<p class="comment">
							<a class="ajax_colorbox" href="dating_events.php?sel=comment&vid=1">{$lang.dating_video.leave_comment}</a>
						</p>
						<div class="clear"></div>
					</div>
				</div>
				<div class="video-divider"></div>
				<div class="video-item">
					<div>
						<a class="video_dating_events" href="http://player.vimeo.com/video/48432378">
							<img src="{$site_root}{$template_root}/imgs/dating-events-video2-150.jpg" alt="Video" />
						</a>
						<p class="hdr5">{$lang.dating_video.title2}</p>
						<p class="comment">
							<a class="ajax_colorbox" href="dating_events.php?sel=comment&vid=2">{$lang.dating_video.leave_comment}</a>
						</p>
						<div class="clear"></div>
					</div>
				</div>
				<div class="video-divider"></div>
				<div class="video-item">
					<div>
						<a class="video_dating_events" href="http://player.vimeo.com/video/49138868">
							<img src="{$site_root}{$template_root}/imgs/dating-events-video3-150.jpg" alt="Video" />
						</a>
						<p class="hdr5">{$lang.dating_video.title3}</p>
						<p class="comment">
							<a class="ajax_colorbox" href="dating_events.php?sel=comment&vid=3">{$lang.dating_video.leave_comment}</a>
						</p>
						<div class="clear"></div>
					</div>
				</div>
				<div class="video-divider"></div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
{literal}
$("#dating-slide").easySlider({
	auto: true,
	pause: 10000,
	speed: 1000,
	animateFade: true,
	continuous: true,
	numeric: true
});
$(document).ready(function()
{
	$("#dating-faq").msAccordion({defaultid:'', vertical:true});
});
{/literal}
</script>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}