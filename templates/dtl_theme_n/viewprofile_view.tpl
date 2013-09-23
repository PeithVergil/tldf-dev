{if $form.page == '1' || $form.page == 'print'}
	<div>
		<!-- begin personal info -->
		<div style="padding:10px;">
			<div class="hdr2">{$lang.subsection.personal_info}</div>
			<div class="sep"></div>
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="50%" style="padding-bottom: 5px;" class="txtblack">
						<font class="txtblue">{$lang.users.mm_nickname}:</font> {$data_1.nick}
					</td>
					<td width="50%" style="padding-bottom: 5px;" class="txtblack">
						<font class="txtblue">{$header.im}:</font>&nbsp;{$data_1.gender_text}&nbsp;({if $data_1.couple}{$lang.users.couple}{else}{$lang.users.single}{/if})
						{if $data_1.couple_user && $data_1.couple_accept}<br>
						<font class="txtblue">{$lang.users.couple_link}:</font> 
						<a href="{$data_1.couple_link}" target=_blank><b>{$data_1.couple_login}</b></A>&nbsp;{$data_1.couple_gender_text}&nbsp;{$data_1.couple_age} {$lang.home_page.ans}{/if}
					</td>
				</tr>
				<tr>
					<td width="50%" style="padding-bottom: 5px;" class="txtblack">
						<font class="txtblue">{$lang.users.region}:</font> {$data_1.region}
					</td>
					<td width="50%" style="padding-bottom: 5px;" class="txtblack">
						<font class="txtblue">{$lang.users.nationality}:</font> {$data_1.nationality}
					</td>
				</tr>
				<tr>
					<td width="50%" style="padding-bottom: 5px;" class="txtblack">
						<font class="txtblue">{$lang.users.city}:</font> {$data_1.city}
					</td>
					<td width="50%" style="padding-bottom: 5px;" class="txtblack">
						<font class="txtblue">{$lang.users.language}:</font> {$data_1.languages}
					</td>
				</tr>
				{if $data_1.headline}
					<tr>
						<td width="50%" style="padding-bottom: 5px;" class=text colspan="2">
							<font class="txtblue">{$lang.users.headline}:</font> {$data_1.headline}
						</td>
					</tr>
				{/if}
			</table>
		</div>
		{*<!-- <div style="height: 1px; margin: 5px 10px" class="delimiter"></div> -->*}
		<!-- end personal info -->
		
		<!-- begin my notice -->
		{* <!-- TLDF DISABLED
		{if $data_3.complete}
			<div style="padding:10px;">
				<div class="header">{$lang.subsection.notice}</div>
				<div class="sep"></div>
				<div align="left" style="padding-left:15px;">
					<div style="margin-bottom:10px; width:320px"><font class="text_hidden">{$data_3.annonce}</font></div>
				</div>
			</div>
		{/if}
		--> *}
		<!-- end my notice -->
	</div>
{/if}

{if $form.page == '2' || $form.page == 'print'}
	<div>
		<!-- begin my description (My Fact Sheet) -->
		{*<!-- {if $data_2.complete} -->*}
		<div style="padding:10px;">
			<div class="hdr2">{$lang.subsection.description}</div>
			<div class="sep"></div>
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="50%" style="padding-bottom: 5px;" class="txtblack">
						<font class="txtblue">{$lang.users.weight}:</font> {$data_2.weight}
					</td>
					<td width="50%" style="padding-bottom: 5px;" class="txtblack">
						<font class="txtblue">{$lang.users.height}:</font> {$data_2.height}
					</td>
				</tr>
				{section name=s loop=$data_2.info}
					{if $smarty.section.s.index is div by 2}
						<tr>
					{/if}
						<td width="50%" style="padding-bottom: 5px;" class="txtblack">
							<font class="txtblue">{$data_2.info[s].spr}:</font> {$data_2.info[s].value}
						</td>
					{if $smarty.section.s.index_next is div by 2 || $smarty.section.s.last}
						</tr>
					{/if}
				{/section}
			</table>
		</div>
		{*<!-- {/if} -->*}
		<!-- end my description (My Fact Sheet) -->

		<!-- begin my personality -->
		{* <!-- TLDF DISABLED
		<div style="height:1px; margin:5px 10px" class="delimiter"></div>
		{if $data_4.complete}
			<div style="padding:10px;">
				<div class="header">{$lang.subsection.personal}</div>
				<div class="sep"></div>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					{section name=s loop=$data_4.personal}
						{if $smarty.section.s.index is div by 2}
							<tr>
						{/if}
						<td width="50%" style="padding-bottom: 5px;" class="txtblack">
							{if $data_4.personal[s].value}
								<font class="txtblue">{$data_4.personal[s].name}:</font> {$data_4.personal[s].value}
							{else}
								&nbsp;
							{/if}
						</td>
						{if $smarty.section.s.index_next is div by 2 || $smarty.section.s.last}
							</tr>
						{/if}
					{/section}
				</table>
			</div>
			<div style="height: 1px; margin: 5px 10px" class="delimiter"></div>
		{/if}
		-->*}
		<!-- end my personality -->

		<!-- begin my portrait -->
		{* <!-- TLDF DISABLED
		{if $data_5.complete}
			<div style="padding:10px;">
				<div class="header">{$lang.subsection.portreit}</div>
				<div class="sep"></div>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					{section name=s loop=$data_5.portrait}
						{if $smarty.section.s.index is div by 2}
							<tr>
						{/if}
						<td width="50%" style="padding-bottom: 5px;" class="txtblack">
							{if $data_5.portrait[s].value}
								<font class="txtblue">{$data_5.portrait[s].name}:</font> {$data_5.portrait[s].value}
							{else}
								&nbsp;
							{/if}
						</td>
						{if $smarty.section.s.index_next is div by 2 || $smarty.section.s.last}
							</tr>
						{/if}
					{/section}
				</table>
			</div>
			<div style="height: 1px; margin: 5px 10px" class="delimiter"></div>
		{/if}
		--> *}
		<!-- end my portrait -->

		<!-- begin my interests -->
		{* <!--
		{if $data_6.complete}
			<div style="padding:10px;">
				<div class="header">{$lang.subsection.interest}</div>
				<div class="sep"></div>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					{section name=s loop=$data_6.interests}
						{if $smarty.section.s.index is div by 3}
							<tr>
						{/if}
						<td width="33%" style="padding-bottom: 5px;" class="txtblack">
							{if $data_6.interests[s].value}
								<font class="txtblue">I&nbsp;<img src="{$site_root}{$template_root}/images/int_{$data_6.interests[s].value}.gif" alt="{$data_6.interests[s].lang_value}" align=middle>&nbsp;{$data_6.interests[s].name}</font>
							{else}
								&nbsp;
							{/if}
						</td>
						{if $smarty.section.s.index_next is div by 3 || $smarty.section.s.last}
							</tr>
						{/if}
					{/section}
				</table>
			</div>
		{/if}
		--> *}
		<!-- end my interests -->
	</div>
{/if}

{if $form.page == '3' || $form.page == 'print'}
	<div>
		<!-- begin my criteria -->
		{* <!-- {if $data_7.complete} --> *}
		<div style="padding:10px;">
			<div class="hdr2">{$lang.subsection.criteria}</div>
			<div class="sep"></div>
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td valign="top" width="50%" style="padding-bottom: 5px;" class="txtblack">
						<font class="txtblue">{$lang.users.weight}:</font> {$data_7.weight}
					</td>
					<td valign="top" width="50%" style="padding-bottom: 5px;" class="txtblack">
						<font class="txtblue">{$lang.users.height}:</font> {$data_7.height}
					</td>
				</tr>
				<tr>
					<td width="50%" style="padding-bottom: 5px;" class="txtblack">
						<font class="txtblue">{$lang.users.country}:</font> {$data_7.country_match}
					</td>
					<td width="50%" style="padding-bottom: 5px;" class="txtblack">
						<font class="txtblue">{$lang.users.language}:</font> {$data_7.language}
					</td>
				</tr>
				<tr>
					<td width="50%" style="padding-bottom: 5px;" class="txtblack">
						<font class="txtblue">{$lang.users.nationality}:</font> {$data_7.nationality_match}
					</td>
				</tr>
				{section name=s loop=$data_7.info}
					{if $smarty.section.s.index_next is div by 2}
						<tr>
					{/if}
					<td width="50%" style="padding-bottom: 5px;" class="txtblack">
						<font class="txtblue">{$data_7.info[s].name}:</font> {$data_7.info[s].value}
					</td>
					{if $smarty.section.s.index is div by 2 || $smarty.section.s.last}
						</tr>
					{/if}
				{/section}
			</table>
		</div>
		<!-- end my criteria -->
		{* <!-- {/if} --> *}
		
		{* <!-- {if $data_8.complete} --> *}
		<!-- begin his interests -->
		{* <!-- TLDF DISABLED
		<div style="height:1px; margin:5px 10px" class="delimiter"></div>
		<div style="padding:10px;">
			<div class="header">{$lang.subsection.match_interest}</div>
			<div class="sep"></div>
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				{section name=s loop=$data_8.interests}
					{if $smarty.section.s.index is div by 3}
						<tr>
					{/if}
					<td width="33%" style="padding-bottom: 5px;" class="txtblack">
						{if $data_8.interests[s].value_1|| $data_8.interests[s].value_2||$data_8.interests[s].value_3}<font class="txtblue">{$header.soulmate}
						{if $data_8.interests[s].value_1 eq 1}<img src="{$site_root}{$template_root}/images/int_1.gif" alt="{$lang.interests_opt.1}" align=middle>{/if}
						{if $data_8.interests[s].value_2 eq 1}<img src="{$site_root}{$template_root}/images/int_2.gif" alt="{$lang.interests_opt.2}" align=middle>{/if}
						{if $data_8.interests[s].value_3 eq 1}<img src="{$site_root}{$template_root}/images/int_3.gif" alt="{$lang.interests_opt.3}" align=middle>{/if}
						{$data_8.interests[s].name}</font>{/if}
					</td>
					{if $smarty.section.s.index_next is div by 3 || $smarty.section.s.last}
						</tr>
					{/if}
				{/section}
			</table>
		</div>
		--> *}
		<!-- end his interests -->
		{*<!-- {/if} -->*}
	</div>
{/if}

{if $form.page == '4'}
	<!-- begin submenu -->
	{strip}
	<div style="height:35px;">
		<ul class="nav-tab-inside tcxf-ch-la">
			<li id="sub_menu7" style="height:35px; text-align:center; display:none;">
				<a href="javascript:void(0);" id="sub_link7" {if $data_9.form_sub_page == '7'}class="text"{/if} onclick="ShowTab(7, './myprofile.php?sel=4&amp;sub=7&amp;action=album_list&amp;{$form.suffix}'); return false;">{$lang.section.icon}</a>
			</li>
            <li id="sub_menu8" class="sub_tab{if $data_9.form_sub_page == '8'}_active{/if}_first" style="height:35px; width:100px; text-align:center;">
				<a href="javascript:void(0);" id="sub_link8" {if $data_9.form_sub_page == '8'}class="text"{/if} onclick="ShowTab(8, './myprofile.php?sel=4&amp;sub=8&amp;action=album_list&amp;{$form.suffix}', 2); return false;">{$lang.section.photos}</a>
			</li>
			<li id="sub_menu9" class="sub_tab{if $data_9.form_sub_page == '9'}_active{/if}" style="height:35px; width:100px; text-align:center;">
				<a href="javascript:void(0);" id="sub_link9" {if $data_9.form_sub_page == '9'}class="text"{/if} onclick="ShowTab(9, './myprofile.php?sel=4&amp;sub=9&amp;action=album_list&amp;{$form.suffix}', 2); return false;">{$lang.section.audio}</a>
			</li>
            <li id="sub_menu10" class="sub_tab{if $data_9.form_sub_page == '10'}_active{/if}" style="height:35px; width:100px; text-align:center;">
				<a href="javascript:void(0);" id="sub_link10" {if $data_9.form_sub_page == '10'}class="text"{/if} onclick="ShowTab(10, './myprofile.php?sel=4&amp;sub=10&amp;action=album_list&amp;{$form.suffix}', 2); return false;">{$lang.section.video}</a>
			</li>
        </ul>
	</div>
	{/strip}
	<!-- end submenu -->
	
	<!-- MULTIMEDIA SECTION -->
	<div class="media-info">
		<div style="padding:10px;">
			<!-- ALBUM AND ITEM COUNT -->
			<div class="text">
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td style="padding-bottom:7px;">
							{if $data_9.form_sub_page == 8}
								{$lang.users.photo_album_count}
							{elseif $data_9.form_sub_page == 9}
								{$lang.users.audio_album_count}
							{elseif $data_9.form_sub_page == 10}
								{$lang.users.video_album_count}
							{/if}:&nbsp;
						</td>
						<td style="padding-bottom:7px;">{$album_page.album_count}</td>
					</tr>
					<tr>
						<td style="padding-bottom:7px;">
							{if $data_9.form_sub_page == 8}
								{$lang.users.photos_count}
							{elseif $data_9.form_sub_page == 9}
								{$lang.users.audio_count}
							{elseif $data_9.form_sub_page == 10}
								{$lang.users.video_count}
							{/if}:&nbsp;
						</td>
						<td style="padding-bottom:7px;">{$album_page.items_count}</td>
					</tr>
				</table>
			</div>
			<div style="height: 1px; margin:10px 0px 10px 0px;" class="delimiter"></div>
			<!-- /ALBUM AND ITEM COUNT -->
			
			<!-- ALBUM LIST -->
			{if $album_page.album_count > 0}
				<table cellpadding="0" cellspacing="0" width="100%" border="0">
					{foreach item=item from=$_album name=k}
						{if ($smarty.foreach.k.iteration-1) is div by 4}
							<tr>
								<td width="25%" align="left" valign="top">
						{else}
							<td width="25%" align="left" valign="top" style="padding-left:20px;">
						{/if}
						<table cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td style="padding-right:5px;" valign="top">
									<a href="javascript:void(0);" onclick="ShowTab({$data_9.form_sub_page}, './myprofile.php?sel=4&amp;sub={$data_9.form_sub_page}&amp;action=browse_album&amp;id_album={$item.id}&amp;{$form.suffix}', 2); return false;">
									<img src="{$album_page._album_icon}" class="icon"><br />&nbsp; Browse</a>
								</td>
								<td valign="top">
									<div style="padding-bottom:3px;" class="text_head">
										<a href="javascript:void(0);" onclick="ShowTab({$data_9.form_sub_page}, './myprofile.php?sel=4&amp;sub={$data_9.form_sub_page}&amp;action=browse_album&amp;id_album={$item.id}&amp;{$form.suffix}', 2); return false;">{$item.title}</a>
									</div>
									<div style="padding-bottom:3px;" class="text">{$item.description}</div>
									<div style="padding-bottom:3px;" class="text_hidden">{$lang.users.created}:&nbsp;{$item.creation_date}</div>
									<div style="padding-bottom:3px;" class="text">
										{if $album_page.upload_type == 'f'}
											{$lang.users.photos_count}
										{elseif $album_page.upload_type == 'a'}
											{$lang.users.audio_count}
										{elseif $album_page.upload_type == 'v'}
											{$lang.users.video_count}
										{/if}:&nbsp;{$item.items_count}
									</div>
								</td>
							</tr>
						</table>
						<div style="height:1px; margin:5px 0px 7px 0px;" class="delimiter"></div>
						{if $smarty.foreach.k.iteration is div by 4}
								</td>
							</tr>
						{else}
							</td>
						{/if}
					{/foreach}
				</table>
				
				{if $album_links_page}
					<!-- ALBUM LINKS -->
					<div style="margin: 0px">
						<div style="margin-left: 10px">
							{foreach item=item from=$album_links_page}
								<div class="page_div{if $item.selected eq '1'}_active{/if}">
									<div style="margin:5px">
										<a href="javascript:void(0);" onclick="ShowTab({$data_9.form_sub_page}, './myprofile.php?sel=4&amp;sub={$data_9.form_sub_page}&amp;action={$data_9.form_act}{$item.link}&amp;{$form.suffix}', 2); return false;" class="page_link{if $item.selected eq '1'}_active{/if}">{$item.name}</a>
									</div>
								</div>
							{/foreach}
						</div>
					</div>
					<!-- /ALBUM LINKS -->
				{/if}
			{/if}
			<!-- /ALBUM LIST -->
            
			<!-- ITEMS IN SELECTED ALBUM -->
			{if $album_page.show_album_items == 1}
				{if $album_page.upload_type == 'f'}
					<!-- PHOTOS -->
					<div class="hdr2" style="padding-top:10px;">{$lang.users.upload_1}-Album: {$data_9.album_title}</div>
					<div class="sep"></div>
					{if $data_9.photo}
						<div style="height:30px;">
							{foreach key=key item=item from=$data_9.photo}
								<div style="float:left; margin:4px;">
									{if $item.thumb_path}
										{if $item.view_link}
											<a href="javascript:void(0);" onclick="window.open('{$item.view_link}', 'photo_view', 'menubar=0,resizable=1,scrollbars=0,status=0,toolbar=0,width=800,height=600');return false;">
										{/if}
										<img src="{$item.thumb_path}" border="0" {$item.sizes} class="icon" alt="" />
										{if $item.view_link}
											</a>
										{/if}
									{/if}
								</div>
							{/foreach}
						</div>
						<div style="clear:both;"></div>
					{else}
						<div><b>The album is empty</b></div>
					{/if}
					<!-- /PHOTOS -->
				{elseif $album_page.upload_type == 'a'}
					<!-- AUDIO -->
					<div class="hdr2">{$lang.users.upload_2}-Album: {$data_9.album_title}</div>
					<div class="sep"></div>
					{if $data_9.audio}
						<div class="txtblack">
							{foreach key=key item=item from=$data_9.audio}
								{if $data_9.embedded_audio}
									{if $item.file_path}
										<div id="player{$key}">
											<script type="text/javascript">
											var fv = "file={$item.file_path}&autostart="+autostart+"&title={$item.user_comment|escape}&lightcolor=0xD12627";
											var FO = {ldelim}
												movie:"{$site_root}/include/mp3player/mp3player.swf",width:"300",height:"20",majorversion:"7",build:"0",bgcolor:"#FFFFFF", flashvars:fv
											{rdelim};
											UFO.create(FO, "player{$key}");
											</script>
										</div>
									{/if}
								{else}
									<div style="margin-bottom:4px">
										{if $item.thumb_file}
											{if $item.view_link}
												<a href="javascript:void(0);" onclick="window.open('{$item.view_link}', 'audio_view', 'menubar=0,resizable=1,scrollbars=0,status=0,toolbar=0,width=800,height=600');return false;">
											{/if}
											<img src="{$item.thumb_file}" border="0" {$item.sizes} class="icon" alt="" />
											{if $item.view_link}
												</a>
											{/if}
										{/if}
									</div>
								{/if}
								{if $item.user_comment}
									<div style="margin-bottom:8px" class="txtblack">{$item.user_comment}</div>
								{/if}
							{/foreach}
						</div>
					{else}
						<div><b>The album is empty</b></div>
					{/if}
					<!-- /AUDIO -->
				{elseif $album_page.upload_type == 'v'}
					<!-- VIDEO -->
					<div class="hdr2">{$lang.users.upload_3}-Album: {$data_9.album_title}</div>
					<div class="sep"></div>
					{if $data_9.video}
						{if SHOW_VIDEO_ON_PAGE}
							<table border="0">
								<tr>
									<td valign="top" width="325">
										{if $smarty.const.VIDEO_PLAYER == 'pilot-group'}
											{if $current_video.is_flv}
												<object type="application/x-shockwave-flash" data="{$site_root}{$template_root}/flash/FlowPlayer.swf" width="320" height="240" id="FlowPlayer">
													<param name="allowScriptAccess" value="always" />
													<param name="movie" value="{$site_root}{$template_root}/flash/FlowPlayer.swf" />
													<param name="quality" value="high" />
													<param name="scaleMode" value="showAll" />
													<param name="allowfullscreen" value="false" />
													<param name="wmode" value="transparent" />
													<param name="allowNetworking" value="all" />
													{literal}
													<param name="flashvars" value="config={
														autoPlay: false,
														loop: false,
														initialScale: 'scale',
														showLoopButton: false,
														showPlayListButtons: false,
														showFullScreenButton: false,
														showMenu: false,
														playList: [
															{ url: '{/literal}{$current_video.thumb_path}{literal}' },
															{ url: '{/literal}{$current_video.file_path}{literal}' },
															{ url: '', type: 'swf' }
														]
														}" />
													{/literal}
												</object>
											{else}
												<object ID="MediaPlayer1" classid="CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95" CODEBASE="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,5,715" standby="Loading MicrosoftR WindowsR Media Player components..." type="application/x-oleobject">
													<param name="AutoStart" value="True">
													<param name="FileName" value="{$current_video.file_path}">
													<param name="ShowControls" value="True">
													<param name="ShowStatusBar" value="true">
													<embed type="application/x-mplayer2" pluginspage="http://www.microsoft.com/Windows/MediaPlayer/" SRC="{$current_video.file_path}" name="MediaPlayer1" autostart=1 showcontrols=0></embed>
												</object>
											{/if}
										{elseif $smarty.const.VIDEO_PLAYER == 'flowplayer'}
											{if $current_video.html5_video}
												<video id="movie" width="320" height="240" poster="{$current_video.thumb_path}" preload="auto" controls="controls">
													<source src="{$current_video.file_path}" type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"' />
											{/if}
											<object type="application/x-shockwave-flash" data="http://releases.flowplayer.org/swf/flowplayer-3.2.16.swf" width="320" height="240" id="FlowPlayer">
												<param name="movie" value="http://releases.flowplayer.org/swf/flowplayer-3.2.16.swf" />
												<param name="allowfullscreen" value="true" />
												<param name="quality" value="high" />
												<param name="scaleMode" value="showAll" />
												<param name="wmode" value="transparent" />
												<param name="allowNetworking" value="all" />
												{* <param name="allowScriptAccess" value="always" /> *}
												<param name="flashvars" value="config={ldelim}
													{* rs: disabled for now, check with new flowplayer docu, parameters might be renamed or camelcase changed
													'loop': false,
													'initialScale': 'scale',
													'showLoopButton': false,
													'showPlayListButtons': false,
													'showFullScreenButton': false,
													'showMenu': false,
													*}
													'playlist': [
														{ldelim} 'url': '{$current_video.thumb_path}' {rdelim},
														{ldelim} 'url': '{$current_video.file_path}', 'autoPlay': false, 'autoBuffering': true {rdelim}
													]
												{rdelim}" />
												<embed type="application/x-shockwave-flash" src="http://releases.flowplayer.org/swf/flowplayer-3.2.16.swf" width="320" height="240" flashvars="config={ldelim} 'playlist': [
														{ldelim} 'url': '{$current_video.thumb_path}' {rdelim},
														{ldelim} 'url': '{$current_video.file_path}', 'autoPlay': false, 'autoBuffering': true {rdelim}
													]
												{rdelim}"></embed>
											</object>
											{if $current_video.html5_video}
												</video>
											{/if}
										{elseif $smarty.const.VIDEO_PLAYER == 'mediaelement-js' || $smarty.const.VIDEO_PLAYER == 'flowplayer-RTMP'}
											<video id="player1" width="320" height="240" src="{$current_video.file_path}" type="video/mp4" poster="{$current_video.thumb_path}" controls="controls" preload="auto"></video>
											<script type="text/javascript">
											$('audio,video').mediaelementplayer();
											</script>
										{/if}
									</td>
									<td style="padding-left:50px;" valign="top">
										{foreach key=key item=item from=$data_9.video}
											<div style="margin-bottom: 4px">
												{if $item.thumb_path}
													{if $item.view_link}
														<a href="javascript:void(0);" onclick="ShowTab(10, '{$item.view_link}&amp;sub=10&amp;action=4', 2); return false;">
														{* <a href="{$item.view_link}"> *}
													{/if}
													<img src="{$item.thumb_path}" {if $item.sel}style="border:5px #{$css_color.home_search} solid;"{else}border="0"{/if} {$item.sizes} class="icon" alt="">
													{if $item.view_link}
														</a>
													{/if}
												{else}
													file not found
												{/if}
											</div>
											{if $item.user_comment}
												<div class="txtblack">
													{if $item.view_link}
														<a href="javascript:void(0);" onclick="ShowTab(10, '{$item.view_link}&amp;sub=10&amp;action=4', 2); return false;">
														{* <a href="{$item.view_link}"> *}
													{/if}
													{$item.user_comment}
													{if $item.view_link}
														</a>
													{/if}
												</div>
											{/if}
											<div>&nbsp;</div>
										{/foreach}
									</td>
								</tr>
							</table>
						{else}
							<div class="txtblack">
								{foreach key=key item=item from=$data_9.video}
									<div style="margin-bottom: 4px">
										{if $item.view_link}
											<a href="javascript:void(0);" onclick="window.open('{$item.view_link}', 'video_view', 'menubar=0,resizable=1,scrollbars=0,status=0,toolbar=0,width=800,height=600');return false;">
										{/if}
										{if $item.thumb_path}
											<img src="{$item.thumb_path}" border="0" {$item.sizes} class="icon" alt="">
										{/if}
										{if $item.view_link}
											</a>
										{/if}
									</div>
									{if $item.user_comment}
										<div class="txtblack">{$item.user_comment}</div>
									{/if}
								{/foreach}
							</div>
						{/if}
					{else}
						<div><b>The album is empty</b></div>
					{/if}
					<!-- /VIDEO -->
				{/if}
			{/if}
			<!-- /ITEMS IN SELECTED ALBUM -->
		</div>
	</div>
	<!-- /MULTIMEDIA SECTION -->
{/if}

{if $form.page == '5'}
	<!-- begin rating -->
	<div>
		<div style="padding:10px;">
			<div class="hdr2">{$lang.subsection.rating}</div>
			<div class="sep"></div>
			<div align="center">
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td width="300" align="left">
							<font class="txtblue">{$header.current_rating}:</font> 
							<font class="txtblack">{$data_10.current_rating_bar} {$data_10.current_rating}</font> 
							<font class="txtblack">{$header.your_vote}: {$data_10.your_vote}&nbsp;&nbsp;&nbsp;{$header.all_vote}: {$data_10.all_vote}</font>
						</td>
						<td width="200">
							{if ($data_10.allow_rate) && ($form.guest_user != 1)}
                                <form name="voting" method=post action="{$form.action}" style="margin:0px">
									<input type="hidden" name="sel" value="vote">
									<table cellpadding="0" cellspacing="0">
										<tr>
											<td class="text_head" colspan="10" align="center">{$header.rate_it}:</td>
										</tr>
										<tr>
											<td class="text" align="center">
												<input type="radio" name="r" value="1" onclick="this.form.submit();" />
												<br>1
											</td>
											<td class="text" align="center">
												<input type="radio" name="r" value="2" onclick="this.form.submit();" />
												<br>2
											</td>
											<td class=text align=center>
												<input type="radio" name="r" value="3" onClick="javascript:this.form.submit();">
												<br>3
											</td>
											<td class=text align=center>
												<input type="radio" name="r" value="4" onClick="javascript:this.form.submit();">
												<br>4
											</td>
											<td class=text align=center>
												<input type="radio" name="r" value="5" onClick="javascript:this.form.submit();">
												<br>5
											</td>
											<td class=text align=center>
												<input type="radio" name="r" value="6" onClick="javascript:this.form.submit();">
												<br>6
											</td>
											<td class=text align=center>
												<input type="radio" name="r" value="7" onClick="javascript:this.form.submit();">
												<br>7
											</td>
											<td class=text align=center>
												<input type="radio" name="r" value="8" onClick="javascript:this.form.submit();">
												<br>8
											</td>
											<td class=text align=center>
												<input type="radio" name="r" value="9" onClick="javascript:this.form.submit();">
												<br>9
											</td>
											<td class=text align=center>
												<input type="radio" name="r" value="10" onClick="javascript:this.form.submit();">
												<br>10
											</td>
										</tr>
									</table>
                                </form>
							{else}
								&nbsp;
							{/if}
						</td>
					</tr>
				</table>
				<div style="height: 1px; margin: 5px 0px" class="delimiter"></div>
				<div class="header">{$lang.subsection.comments}</div>
				<div class="sep"></div>
				<div>
					<table cellpadding="0" cellspacing="0" width="100%" border="0">
						<tr>
							<td width="50%" valign="top">
								{section name=s loop=$data_10.comments start=0 max=3}
									<div style="padding-top:45px;">
										{if $data_10.comments[s].profile_link}
											<a href="{$data_10.comments[s].profile_link}">{/if}<img src="{$data_10.comments[s].icon_path}" class="icon" alt="">{if $data_10.comments[s].profile_link}</a>
										{/if}
										<div style="padding-left:14px;">
											<div>{if $data_10.comments[s].profile_link}<a href="{$data_10.comments[s].profile_link}">{/if}<b>{$data_10.comments[s].name}</b>{if $data_10.comments[s].profile_link}</a>{/if}</div>
											<div style="padding-top: 5px;"><font class="txtblue">{$data_10.comments[s].age} {$lang.home_page.ans}</font></div>
											<div style="padding-top: 5px;"><font class="{if $data_10.comments[s].status eq $lang.status.on}link{else}text{/if}_active">{$data_10.comments[s].status}</font></div>
											<div style="padding-top: 5px; width: 50px;"><font class="txtblack">{if $data_10.comments[s].city}{$data_10.comments[s].city}, {/if}{if $data_10.comments[s].region}{$data_10.comments[s].region}, {/if}{$data_10.comments[s].country}</font></div>
										</div>
										<div style="padding-left:25px;">
											<div style="background-color: #{$css_color.bg_color}; border: 1px #{$css_color.home_search} solid; width: 250px; min-height: 75px;">
												<div style="margin: 2px 5px;">
													<div align="right">
														<font class="text_hidden"><b>{$data_10.comments[s].date}</b></font>&nbsp;&nbsp;
														{if $data_10.comments[s].delete_link}
															<a href="{$data_10.comments[s].delete_link}">[{$button.delete}]</a>
														{/if}
													</div>
													{$data_10.comments[s].message}
												</div>
											</div>
										</div>
									</div>
								{/section}
							</td>
							<td width="50%" valign="top">
								{section name=s loop=$data_10.comments start=3 max=6}
									<div style="padding-top:45px;">
										{if $data_10.comments[s].profile_link}
											<a href="{$data_10.comments[s].profile_link}">{/if}<img src="{$data_10.comments[s].icon_path}" class="icon" alt="">{if $data_10.comments[s].profile_link}</a>
										{/if}
										<div style="padding-left:14px;">
											<div>{if $data_10.comments[s].profile_link}<a href="{$data_10.comments[s].profile_link}">{/if}<b>{$data_10.comments[s].name}</b>{if $data_10.comments[s].profile_link}</a>{/if}</div>
											<div style="padding-top:5px;"><font class="txtblue">{$data_10.comments[s].age} {$lang.home_page.ans}</font></div>
											<div style="padding-top:5px;"><font class="{if $data_10.comments[s].status eq $lang.status.on}link{else}text{/if}_active">{$data_10.comments[s].status}</font></div>
											<div style="padding-top:5px; width:50px;">
												<font class="txtblack">
													{if $data_10.comments[s].city}{$data_10.comments[s].city}, {/if}{if $data_10.comments[s].region}{$data_10.comments[s].region}, {/if}{$data_10.comments[s].country}
												</font>
											</div>
										</div>
										<div style="padding-left:25px;">
											<div style="background-color: #{$css_color.bg_color}; border: 1px #{$css_color.home_search} solid; width: 250px; min-height: 75px;">
												<div style="margin: 2px 5px;">
													<div align="right"><font class="text_hidden"><b>{$data_10.comments[s].date}</b></font>&nbsp;&nbsp;&nbsp;{if $data_10.comments[s].delete_link}<a href="{$data_10.comments[s].delete_link}">[{$button.delete}]</a>{/if}</div>
													{$data_10.comments[s].message}
												</div>
											</div>
										</div>
									</div>
								{/section}
							</td>
						</tr>
						{if $links}
							<tr>
								<td colspan="2">
									<div style="padding-left: 10px; padding-top: 15px;" >
										{foreach item=item from=$links}
											<span style="padding-right: 15px;">
												<a href="{$item.link}" {if $item.selected == '1'}class="text_head"{/if}>{$item.name}</a>
											</span>
										{/foreach}
									</div>
								</td>
							</tr>
						{/if}
					</table>
				</div>
				{if $form.guest_user != 1}
					<div style="padding-top:45px;">
						<form name="comment_form" method=post action="{$form.action}" style="margin: 0px;">
							<input type="hidden" name="sel" value="comment">
							<div class="header">{$header.add_comment}</div>
							<div style="margin:0px; padding-top:10px;">
								<div>
									<textarea name="message" id="message" cols=80 rows=6></textarea>
								</div>
								<div style="padding-left:30px;">
									<div style="background-color: #{$css_color.bg_color}; border: 1px #{$css_color.home_search} solid;">
										<div style="margin:0px 15px;">
											<table cellpadding="3" cellspacing="0" border="0">
												{section name=sm loop=$smiles}
													{if $smarty.section.sm.index is div by 6 || $smarty.section.sm.first}
														<tr>
													{/if}
													<td>
														<a style="cursor:pointer" onclick="document.getElementById('message').value=document.getElementById('message').value+'{$smiles[sm].value}'"><img src="{$site_root}{$template_root}/emoticons/{$smiles[sm].file}" alt=""></a>
													</td>
													{if ($smarty.section.sm.index_next is div by 6) || $smarty.section.sm.last}
														</tr>
													{/if}
												{/section}
											</table>
										</div>
									</div>
								</div>
							</div>
							<div class="center" style="padding-top:10px;">
								<div class="btnwrap" style="width:120px;">
									<span><span>
									<input type="button" class="btn_org" style="width:100px;" onclick="javascript: document.comment_form.submit();" value="{$button.gr_valider}">
									</span></span>
								</div>
							</div>
							<div style="height:10px; margin:0px">
								<img src="{$site_root}{$template_root}/images/empty.gif" height="10px" alt="" />
							</div>
						</form>
					</div>
				{/if}
			</div>
		</div>
		<!-- end rating -->
	</div>
{/if}

{if $form.page eq '6'}
	<div>
		{if $data_10.user_tags}
		<!-- begin tags list -->
		<div style="padding: 10px 10px 10px 10px;">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr valign=middle>
					<td height="25" colspan="2">
						<div class="header">{$lang.subsection.users_tag_profile}</div>
						<div class="sep"></div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						{section name=s loop=$data_10.user_tags}
							<a href="{$data_10.user_tags[s].searchlink}" title="{$data_10.user_tags[s].count}">{$data_10.user_tags[s].tag}</a>&nbsp;&nbsp;
						{/section}
					</td>
				</tr>
			</table>
		</div>
		<div style="height: 1px; margin: 5px 10px" class="delimiter"></div>
		<!-- end tags list -->
		{/if}
		{if $data_10.my_tags}
		<!-- begin my tags list -->
		<div style="padding: 10px 10px 10px 10px;">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr valign=middle>
					<td height="25" colspan="2">
						<div class="header">{$lang.subsection.i_tag_profile}</div>
						<div class="sep"></div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						{section name=s loop=$data_10.my_tags}
							{$data_10.my_tags[s].tag}&nbsp;<a href="{$data_10.my_tags[s].dellink}"><b>x</b></a>&nbsp;&nbsp;
						{/section}
					</td>
				</tr>
			</table>
		</div>
		<div style="height: 1px; margin: 5px 10px" class="delimiter"></div>
		<!-- end my tags list -->
		{/if}
		<!-- begin add tag -->
		<div style="padding: 10px 10px 10px 10px;">
			<form name="voting" method=post action="{$form.action}" style="margin:0px">
				<input type="hidden" name="sel" value="addtag">
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td><input type="text" name="tag">&nbsp;</td>
						<td><input type="submit" value="{$lang.tag}"></td>
					</tr>
				</table>
			</form>
		</div>
		<!-- end add tag -->
	</div>
{/if}