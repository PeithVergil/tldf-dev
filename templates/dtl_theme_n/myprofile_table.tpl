{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple my-profile" style="min-height:150px;">
	{* error or other message *}
	{if $form.err}
		<div class="error_msg" style="padding-bottom: 15px;">{$form.err}</div>
	{/if}
	{if $application_submit_msg}
		<div class="error_msg" style="padding-bottom: 15px;">
			{$application_submit_msg}
		</div>
	{/if}
	{if $auth.is_applicant}
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
		<!--SH
		{if $video_page}
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
				<div style="padding-left:260px; padding-top:10px;">
					<p class ="basic-btn_next">
						<span><input type="button" onclick="document.location.href='myprofile.php'" value="{$button.continue}"></span><b>&nbsp;</b>
					</p>
				</div>
			</div>
		{/if}
		-->
	{/if}
	{* heading *}
	{if $data.webrecorder_recorder}
		<table width="100%" cellpadding="5" cellspacing="0" border="0">
			<tr>
				{*
					<td align="left" style="padding-bottom:10px;">
						<span class="title2"> {if $auth.is_applicant}{$lang.section.application}{else}{$lang.section.my_profile}{/if}</span>
					</td>
				*}
				{if $data.webrecorder_recorder}
					<td nowrap>
						<a href="#" onclick="window.open('{$data.webrecorder_recorder}', 'video_record', 'menubar=0,resizable=1,scrollbars=0,status=0,toolbar=0,width=340,height=280');return false;">{$lang.recorder.save_record}</a>
					</td>
					<td valign="top">
						<div id="tool_tip_3">
							<label title="{$lang.recorder.tool_tip}"> <img src="{$site_root}{$template_root}/images/question_icon.gif"> </label>
						</div>
						<script type="text/javascript">
							$(function() {ldelim} $('#tool_tip_3 *').tooltip(); {rdelim});
						</script>
					</td>
				{/if}
				{*
					<!-- PRINT PROFILE CURRENTLY NOT IN USE -->
					{if !$auth.is_applicant}
						<td nowrap="nowrap">
							<a href="#" onclick="window.open('{$data.print_link}', '{$data.login}', 'menubar=0,resizable=1,scrollbars=1,status=0,toolbar=0,width=800,height=600');return false;">{$header.print_version}</a>
						</td>
						<td align="right">
							<div id="tool_tip">
								<label title="{$lang.profile.help_tip.print_profile}"> <img src="{$site_root}{$template_root}/images/question_icon.gif"> </label>
							</div>
							<script type="text/javascript">
							$(function() {ldelim} $('#tool_tip *').tooltip(); {rdelim});
							</script>
						</td>
					{/if}
				*}
			</tr>
		</table>
	{/if}
	{* image, age, location, photo upload *}
	{if $auth.is_applicant && !$video_page}
		<div> {include file="$gentemplates/myprofile_home.tpl"} </div>
	{/if}
	{if $auth.is_applicant}
		</div>
	{else}
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				{* image *}
				{if $data.icon_path}
					<td valign="top" width="170">
						<div class="profile-photo">
							<span><img src="{$data.icon_path}" alt=""></span>
						</div>
					</td>
				{/if}
				<td align="left" valign="top" class="det-16">
					{* login name *}
					<div class="txtblack"><b>{$data.login}</b></div>
					{* profile completion *}
					<div class="g_text">
						{if !$auth.is_applicant}
							<span class="{if $data.complete > 50}link{else}text{/if}_active">
								{if $data.complete < 5}
									{$lang.profile.not_complited}
								{elseif $data.complete >= 5 && $data.complete < 95}
									{$lang.homepage.completion}: {$data.complete}%
								{elseif $data.complete >=95}
									{$lang.profile.complited}
								{/if}
							</span>
						{/if}
					</div>
					{* location *}
					<div class="g_text">
						{$header.age}: <b>{$data.age} {$header.years}
						{if $data.city || $data.region || $data.country}, {/if}
						{if $data.city}{$data.city}, {/if}
						{if $data.region}{$data.region}, {/if}
						{$data.country}</b>
					</div>
					{* upload count and links *}
					<div style="padding-top:5px;" class="g_text">
						{if $data.addf_link}
							<span>{$data.photo_count} {$lang.users.upload_1} </span> |&nbsp;
							{if !$auth.is_applicant}
								<span>
									{if $data.adda_link}
										{$data.audio_count} {$lang.users.upload_2} |&nbsp;
									{/if}
								</span>
								<span>
									{if $data.addv_link}
										{$data.video_count} {$lang.users.upload_3} |&nbsp;
									{/if}
								</span>
							{/if}
							<a href="myprofile.php?sel=upload_photo">{$button.upload}...</a>
						{/if}
					</div>
					{* blog link *}
					{if $use_pilot_module_blog && $smarty.session.permissions.blog}
						<div style="padding-top:5px;" class="g_text">
							<table cellpadding="0" cellspacing="0">
								<tr>
									<td style="padding-top:5px;"><a href="./blog.php" target="_blank"><img title="{$lang.blog.blog_menu_1}" src="{$site_root}{$template_root}/images/view_blog.gif" border="0"></a> </td>
									<td style="padding-top:5px; padding-left: 5px;"><a href="./blog.php">{$lang.blog.blog_menu_1}</a> </td>
								</tr>
							</table>
						</div>
					{/if}
				</td>
				<td valign="top" style="padding-right:2px;">
					{if $auth.is_applicant}
						<div class="content_5 right_instructions">
							<div class="txtpurple txtbig" style="text-align:justify;">{$lang.application.instructions}</div>
							<div>
								<p class ="basic-btn_next">
									<span><input type="button" onclick="document.location.href='myprofile.php?sel=submit_application'" value="{$button.submit_application}"></span><b>&nbsp;</b>
								</p>
							</div>
						</div>
					{/if}
				</td>
			</tr>
		</table>
	{/if}
</div>
<div class="bottom-section">
	<div class="toc my-profile">
		{* section tabs *}
		{if !$auth.is_applicant}
			<div class="tab_row clear">
				<ul class="profile-nav">
					<li id="menu1" class="tab{if $form.page == '1'}_active{/if}_first">
						<a href="#" id="link1" onclick="ShowTab(1, './myprofile.php?sel=1'); return false;">{$lang.section.description}</a>
					</li>
					<li id="menu2" class="tab{if $form.page == '2'}_active{/if}">
						<a href="#" id="link2" onclick="ShowTab(2, './myprofile.php?sel=2'); return false;">{$lang.section.my_fact_sheet}</a>
					</li>
					<li id="menu3" class="tab{if $form.page == '3'}_active{/if}">
						<a href="#" id="link3" onclick="ShowTab(3, './myprofile.php?sel=3'); return false;">{$lang.section.criteria}</a>
					</li>
					<li id="menu4" class="tab{if $form.page == '4'}_active{/if}">
						<a href="#" id="link4" onclick="ShowTab(4, './myprofile.php?sel=4'); return false;">{$lang.section.my_multimedia_album}</a>
					</li>
					{*
					<li id="menu5" class="tab{if $form.page == '5'}_active{/if}">
						<a href="#" id="link5" onclick="ShowTab(5, './myprofile.php?sel=5'); return false;">{$lang.section.rating}</a>
					</li>
					<li id="menu6" class="tab{if $form.page == '6'}_active{/if}_last">
						<a href="#" id="link6" onclick="ShowTab(6, './myprofile.php?sel=6'); return false;">{$lang.section.tags}</a>
					</li>
					*}
				</ul>
			</div>
		{/if}
		{* section content *}
		{if !$auth.is_applicant}
			<div id="tab_div">
				{include file="$gentemplates/myprofile_view.tpl"}
			</div>
			<div class="clear"></div>
		{/if}
	</div>
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}