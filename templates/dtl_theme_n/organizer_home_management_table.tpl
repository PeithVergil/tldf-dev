{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
	<!-- begin main cell -->
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top">
			<div class="header" style="margin: 0px;"><div style="padding: 5px 0px">{$lang.organizer.homepage_management}
				&nbsp;|&nbsp;
				{if $type eq 'layout'}
				{$lang.organizer.my_page_layout}{elseif $type eq 'page_styles'}
				{$lang.organizer.my_page_styles}{/if}
			</div></div>
		</td>
	</tr>
	{if $form.err}
	<tr>
		<td><div class="error_msg">{$form.err}</div></td>
	</tr>
	{/if}
	<tr>
		<td style="padding: 10px 0px 3px 0px;">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td width="16"><img src="{$site_root}{$template_root}/images/btn_back.gif" hspace="0" vspace="0" border="0" alt=""></td>
				<td style="padding-left: 2px;"><a href="organizer.php">{$lang.button.back}</a>
				{if $type eq 'layout'}&nbsp;&nbsp;&nbsp;<a href="organizer.php?sel=homepage_management&type=page_styles">{$lang.organizer.my_page_styles}</a>{/if}
				{if $type eq 'page_styles'}&nbsp;&nbsp;&nbsp;<a href="organizer.php?sel=homepage_management&type=layout">{$lang.organizer.my_page_layout}</a>{/if}
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td valign="top" class="text">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td valign="top">
					<div class="content" style=" margin: 0px; padding: 10px 15px 15px 10px;">
					{if $type eq 'layout'}
					<table cellpadding="0" cellspacing="0" border="0" height="510" width="100%">
					<tr>
						<td valign="top" width="150" height="510">
							<form id="area_form" style="margin: 0px;">
							<table cellpadding="0" cellspacing="0" border="0">
							{section name=s loop=$lang.organizer.section_name}
							{if !($form.use_shoutbox_feature eq 0 && $smarty.section.s.index eq 1) && $disabled_data_ids[$smarty.section.s.index] == '0'}
							<tr>
								<td width="11"><input type="checkbox" value="1" id="area_{$smarty.section.s.index+1}" {if $data[$smarty.section.s.index] == 'true'} checked {/if} onclick="ChangeHome(document.getElementById('home_div'));"></td>
								<td><label for="area_{$smarty.section.s.index+1}" style="cursor: pointer;" onmouseover="HighLight({$smarty.section.s.index+1}, 1);" onmouseout="HighLight({$smarty.section.s.index+1}, 0);">&nbsp;{$smarty.section.s.index+1}. {$lang.organizer.section_name[s]}</label></td>
							</tr>
							{/if}
							{/section}
							<tr>
								<td style="padding-top: 10px; padding-left: 5px;" colspan="2"><a href="homepage.php">{$lang.organizer.view_result}</a></td>
							</tr>
							</table>
							</form>
						</td>
						<td width="420" height="710" style="padding-left: 10px;" valign="top">
						<div id="home_div" style="height: 510px; width: 420px;">
							{include file="$gentemplates/organizer_home_small.tpl"}
						</div>
						</td>
					</tr>
					</table>
					{elseif $type eq 'page_styles'}
					<form action="organizer.php?sel=save_home_colors" method="POST" style="margin: 0px;" enctype="multipart/form-data">
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td style="padding-top: 10px;"><font class="text_head">{$lang.organizer.link_color}:</font>&nbsp;</td>
    						<td style="padding-top: 10px;"><input type="text" name="data[link_color]" value="{$data.link_color}" size="30">&nbsp;</td>
    						<td style="padding-top: 10px;">
    							<table border="0" cellpadding="0" cellspacing="0" width="18">
    							<tr>
       								<td bgcolor="#{$data.link_color}" id="tdcolor_link_color"><a href="#" onclick="pick('pick_link_color','tdcolor_link_color','data[link_color]');return false;" name="pick_link_color" id="pick_link_color"><img src="{$site_root}{$template_root}/images/color.gif" border="0" height="17" width="18"></a></td>
    							</tr>
    							</table>
    						</td>
    						<td style="padding-top: 10px;">&nbsp;&nbsp;
    							<span onmouseover="ShowHelpDiv('help_link_color');" onmouseout="HideHelpDiv('help_link_color');" style="background-color: #ffffff; padding: 2px; border: 1px solid #666666; cursor: help;">&nbsp;<b>?</b>&nbsp;</span>
    							<div id="help_link_color" style="display: none; position: absolute;">&nbsp;<img src="{$site_root}{$template_root}/images/links_color_screen.gif"></div>
    						</td>
    					</tr>
						<tr>
							<td style="padding-top: 10px;"><font class="text_head">{$lang.organizer.header_color}:</font>&nbsp;</td>
    						<td style="padding-top: 10px;"><input type="text" name="data[header_color]" value="{$data.header_color}" size="30">&nbsp;</td>
    						<td style="padding-top: 10px;">
    							<table border="0" cellpadding="0" cellspacing="0" width="18">
    							<tr>
       								<td bgcolor="#{$data.header_color}" id="tdcolor_header_color"><a href="#" onclick="pick('pick_header_color','tdcolor_header_color','data[header_color]');return false;" name="pick_header_color" id="pick_header_color"><img src="{$site_root}{$template_root}/images/color.gif" border="0" height="17" width="18"></a></td>
    							</tr>
    							</table>
    						</td>
    						<td style="padding-top: 10px;">&nbsp;&nbsp;
    							<span onmouseover="ShowHelpDiv('help_header_color');" onmouseout="HideHelpDiv('help_header_color');" style="background-color: #ffffff; padding: 2px; border: 1px solid #666666; cursor: help;">&nbsp;<b>?</b>&nbsp;</span>
    							<div id="help_header_color" style="display: none; position: absolute;">&nbsp;<img src="{$site_root}{$template_root}/images/header_color_screen.gif"></div>
    						</td>
    					</tr>
						<tr>
							<td style="padding-top: 10px;"><font class="text_head">{$lang.organizer.home_area_color}:</font>&nbsp;</td>
    						<td style="padding-top: 10px;"><input type="text" name="data[home_area_color]" value="{$data.home_area_color}" size="30">&nbsp;</td>
    						<td style="padding-top: 10px;">
    							<table border="0" cellpadding="0" cellspacing="0" width="18">
    							<tr>
       								<td bgcolor="#{$data.home_area_color}" id="tdcolor_home_area_color"><a href="#" onclick="pick('pick_home_area_color','tdcolor_home_area_color','data[home_area_color]');return false;" name="pick_home_area_color" id="pick_home_area_color"><img src="{$site_root}{$template_root}/images/color.gif" border="0" height="17" width="18"></a></td>
    							</tr>
    							</table>
    						</td>
    						<td style="padding-top: 10px;">&nbsp;&nbsp;
    							<span onmouseover="ShowHelpDiv('help_user_area_color');" onmouseout="HideHelpDiv('help_user_area_color');" style="background-color: #ffffff; padding: 2px; border: 1px solid #666666; cursor: help;">&nbsp;<b>?</b>&nbsp;</span>
    							<div id="help_user_area_color" style="display: none; position: absolute;">&nbsp;<img src="{$site_root}{$template_root}/images/user_area_background_color_screen.gif"></div>
    						</td>
    					</tr>
						<tr>
							<td style="padding-top: 10px;"><font class="text_head">{$lang.organizer.shoutbox_color}:</font>&nbsp;</td>
    						<td style="padding-top: 10px;"><input type="text" name="data[shoutbox_color]" value="{$data.shoutbox_color}" size="30">&nbsp;</td>
    						<td style="padding-top: 10px;">
    							<table border="0" cellpadding="0" cellspacing="0" width="18">
    							<tr>
       								<td bgcolor="#{$data.shoutbox_color}" id="tdcolor_shoutbox_color"><a href="#" onclick="pick('pick_shoutbox_color','tdcolor_shoutbox_color','data[shoutbox_color]');return false;" name="pick_shoutbox_color" id="pick_shoutbox_color"><img src="{$site_root}{$template_root}/images/color.gif" border="0" height="17" width="18"></a></td>
    							</tr>
    							</table>
    						</td>
    						<td style="padding-top: 10px;">&nbsp;&nbsp;
    							<span onmouseover="ShowHelpDiv('help_shoutbox_color');" onmouseout="HideHelpDiv('help_shoutbox_color');" style="background-color: #ffffff; padding: 2px; border: 1px solid #666666; cursor: help;">&nbsp;<b>?</b>&nbsp;</span>
    							<div id="help_shoutbox_color" style="display: none; position: absolute;">&nbsp;<img src="{$site_root}{$template_root}/images/shoutbox_background_color_screen.gif"></div>
    						</td>
    					</tr>
    					<tr>
							<td style="padding-top: 10px;"><font class="text_head">{$lang.organizer.menu_back_1_color}:</font>&nbsp;</td>
    						<td style="padding-top: 10px;"><input type="text" name="data[menu_back_1_color]" value="{$data.menu_back_1_color}" size="30">&nbsp;</td>
    						<td style="padding-top: 10px;">
    							<table border="0" cellpadding="0" cellspacing="0" width="18">
    							<tr>
       								<td bgcolor="#{$data.menu_back_1_color}" id="tdcolor_menu_back_1_color"><a href="#" onclick="pick('pick_menu_back_1_color','tdcolor_menu_back_1_color','data[menu_back_1_color]');return false;" name="pick_menu_back_1_color" id="pick_menu_back_1_color"><img src="{$site_root}{$template_root}/images/color.gif" border="0" height="17" width="18"></a></td>
    							</tr>
    							</table>
    						</td>
    						<td style="padding-top: 10px;">&nbsp;&nbsp;
    							<span onmouseover="ShowHelpDiv('help_back_1');" onmouseout="HideHelpDiv('help_back_1');" style="background-color: #ffffff; padding: 2px; border: 1px solid #666666; cursor: help;">&nbsp;<b>?</b>&nbsp;</span>
    							<div id="help_back_1" style="display: none; position: absolute;">&nbsp;<img src="{$site_root}{$template_root}/images/menu_back_1_screen.gif"></div>
    						</td>
    					</tr>
    					<tr>
							<td style="padding-top: 10px;"><font class="text_head">{$lang.organizer.menu_back_2_color}:</font>&nbsp;</td>
    						<td style="padding-top: 10px;"><input type="text" name="data[menu_back_2_color]" value="{$data.menu_back_2_color}" size="30">&nbsp;</td>
    						<td style="padding-top: 10px;">
    							<table border="0" cellpadding="0" cellspacing="0" width="18">
    							<tr>
       								<td bgcolor="#{$data.menu_back_2_color}" id="tdcolor_menu_back_2_color"><a href="#" onclick="pick('pick_menu_back_2_color','tdcolor_menu_back_2_color','data[menu_back_2_color]');return false;" name="pick_menu_back_2_color" id="pick_menu_back_2_color"><img src="{$site_root}{$template_root}/images/color.gif" border="0" height="17" width="18"></a></td>
    							</tr>
    							</table>
    						</td>
    						<td style="padding-top: 10px;">&nbsp;&nbsp;
    							<span onmouseover="ShowHelpDiv('help_back_2');" onmouseout="HideHelpDiv('help_back_2');" style="background-color: #ffffff; padding: 2px; border: 1px solid #666666; cursor: help;">&nbsp;<b>?</b>&nbsp;</span>
    							<div id="help_back_2" style="display: none; position: absolute;">&nbsp;<img src="{$site_root}{$template_root}/images/menu_back_2_screen.gif"></div>
    						</td>
    					</tr>
    					<tr>
							<td style="padding-top: 10px;"><font class="text_head">{$lang.organizer.menu_back_3_color}:</font>&nbsp;</td>
    						<td style="padding-top: 10px;"><input type="text" name="data[menu_back_3_color]" value="{$data.menu_back_3_color}" size="30">&nbsp;</td>
    						<td style="padding-top: 10px;">
    							<table border="0" cellpadding="0" cellspacing="0" width="18">
    							<tr>
       								<td bgcolor="#{$data.menu_back_3_color}" id="tdcolor_menu_back_3_color"><a href="#" onclick="pick('pick_menu_back_3_color','tdcolor_menu_back_3_color','data[menu_back_3_color]');return false;" name="pick_menu_back_3_color" id="pick_menu_back_3_color"><img src="{$site_root}{$template_root}/images/color.gif" border="0" height="17" width="18"></a></td>
    							</tr>
    							</table>
    						</td>
    						<td style="padding-top: 10px;">&nbsp;&nbsp;
    							<span onmouseover="ShowHelpDiv('help_back_3');" onmouseout="HideHelpDiv('help_back_3');" style="background-color: #ffffff; padding: 2px; border: 1px solid #666666; cursor: help;">&nbsp;<b>?</b>&nbsp;</span>
    							<div id="help_back_3" style="display: none; position: absolute;">&nbsp;<img src="{$site_root}{$template_root}/images/menu_back_3_screen.gif"></div>
    						</td>
    					</tr>
    					<tr>
							<td style="padding-top: 10px;"><font class="text_head">{$lang.organizer.menu_back_4_color}:</font>&nbsp;</td>
    						<td style="padding-top: 10px;"><input type="text" name="data[menu_back_4_color]" value="{$data.menu_back_4_color}" size="30">&nbsp;</td>
    						<td style="padding-top: 10px;">
    							<table border="0" cellpadding="0" cellspacing="0" width="18">
    							<tr>
       								<td bgcolor="#{$data.menu_back_4_color}" id="tdcolor_menu_back_4_color"><a href="#" onclick="pick('pick_menu_back_4_color','tdcolor_menu_back_4_color','data[menu_back_4_color]');return false;" name="pick_menu_back_4_color" id="pick_menu_back_4_color"><img src="{$site_root}{$template_root}/images/color.gif" border="0" height="17" width="18"></a></td>
    							</tr>
    							</table>
    						</td>
    						<td style="padding-top: 10px;">&nbsp;&nbsp;
    							<span onmouseover="ShowHelpDiv('help_back_4');" onmouseout="HideHelpDiv('help_back_4');" style="background-color: #ffffff; padding: 2px; border: 1px solid #666666; cursor: help;">&nbsp;<b>?</b>&nbsp;</span>
    							<div id="help_back_4" style="display: none; position: absolute;">&nbsp;<img src="{$site_root}{$template_root}/images/menu_back_4_screen.gif"></div>
    						</td>
    					</tr>
    					<tr>
							<td style="padding-top: 10px;"><font class="text_head">{$lang.organizer.menu_font_1_color}:</font>&nbsp;</td>
    						<td style="padding-top: 10px;"><input type="text" name="data[menu_font_1_color]" value="{$data.menu_font_1_color}" size="30">&nbsp;</td>
    						<td style="padding-top: 10px;">
    							<table border="0" cellpadding="0" cellspacing="0" width="18">
    							<tr>
       								<td bgcolor="#{$data.menu_font_1_color}" id="tdcolor_menu_font_1_color"><a href="#" onclick="pick('pick_menu_font_1_color','tdcolor_menu_font_1_color','data[menu_font_1_color]');return false;" name="pick_menu_font_1_color" id="pick_menu_font_1_color"><img src="{$site_root}{$template_root}/images/color.gif" border="0" height="17" width="18"></a></td>
    							</tr>
    							</table>
    						</td>
    						<td style="padding-top: 10px;">&nbsp;&nbsp;
    							<span onmouseover="ShowHelpDiv('help_font_1');" onmouseout="HideHelpDiv('help_font_1');" style="background-color: #ffffff; padding: 2px; border: 1px solid #666666; cursor: help;">&nbsp;<b>?</b>&nbsp;</span>
    							<div id="help_font_1" style="display: none; position: absolute;">&nbsp;<img src="{$site_root}{$template_root}/images/menu_font_1_screen.gif"></div>
    						</td>
    					</tr>
    					<tr>
							<td style="padding-top: 10px;"><font class="text_head">{$lang.organizer.menu_font_2_color}:</font>&nbsp;</td>
    						<td style="padding-top: 10px;"><input type="text" name="data[menu_font_2_color]" value="{$data.menu_font_2_color}" size="30">&nbsp;</td>
    						<td style="padding-top: 10px;">
    							<table border="0" cellpadding="0" cellspacing="0" width="18">
    							<tr>
       								<td bgcolor="#{$data.menu_font_2_color}" id="tdcolor_menu_font_2_color"><a href="#" onclick="pick('pick_menu_font_2_color','tdcolor_menu_font_2_color','data[menu_font_2_color]');return false;" name="pick_menu_font_2_color" id="pick_menu_font_2_color"><img src="{$site_root}{$template_root}/images/color.gif" border="0" height="17" width="18"></a></td>
    							</tr>
    							</table>
    						</td>
    						<td style="padding-top: 10px;">&nbsp;&nbsp;
    							<span onmouseover="ShowHelpDiv('help_font_2');" onmouseout="HideHelpDiv('help_font_2');" style="background-color: #ffffff; padding: 2px; border: 1px solid #666666; cursor: help;">&nbsp;<b>?</b>&nbsp;</span>
    							<div id="help_font_2" style="display: none; position: absolute;">&nbsp;<img src="{$site_root}{$template_root}/images/menu_font_2_screen.gif"></div>
    							<div id="help_quick_search" style="display: none; position: absolute;">&nbsp;<img src="{$site_root}{$template_root}/images/quick_search_bg_screen.gif"></div>
    							<div id="help_content_bg" style="display: none; position: absolute;">&nbsp;<img src="{$site_root}{$template_root}/images/content_bg_screen.gif"></div>
    							<div id="help_main_text" style="display: none; position: absolute;">&nbsp;<img src="{$site_root}{$template_root}/images/main_text_color_screen.gif"></div>
    							<div id="help_hidden_text" style="display: none; position: absolute;">&nbsp;<img src="{$site_root}{$template_root}/images/hidden_text_screen.gif"></div>
    						</td>
    					</tr>
    					<tr>
							<td style="padding-top: 10px;"><font class="text_head">{$lang.organizer.menu_font_3_color}:</font>&nbsp;</td>
    						<td style="padding-top: 10px;"><input type="text" name="data[menu_font_3_color]" value="{$data.menu_font_3_color}" size="30">&nbsp;</td>
    						<td style="padding-top: 10px;">
    							<table border="0" cellpadding="0" cellspacing="0" width="18">
    							<tr>
       								<td bgcolor="#{$data.menu_font_3_color}" id="tdcolor_menu_font_3_color"><a href="#" onclick="pick('pick_menu_font_3_color','tdcolor_menu_font_3_color','data[menu_font_3_color]');return false;" name="pick_menu_font_3_color" id="pick_menu_font_3_color"><img src="{$site_root}{$template_root}/images/color.gif" border="0" height="17" width="18"></a></td>
    							</tr>
    							</table>
    						</td>
    						<td style="padding-top: 10px;">&nbsp;&nbsp;
    							<span onmouseover="ShowHelpDiv('help_font_3');" onmouseout="HideHelpDiv('help_font_3');" style="background-color: #ffffff; padding: 2px; border: 1px solid #666666; cursor: help;">&nbsp;<b>?</b>&nbsp;</span>
    							<div id="help_font_3" style="display: none; position: absolute;">&nbsp;<img src="{$site_root}{$template_root}/images/menu_font_3_screen.gif"></div>
    						</td>
    					</tr>
    					<tr>
							<td style="padding-top: 10px;"><font class="text_head">{$lang.organizer.menu_font_4_color}:</font>&nbsp;</td>
    						<td style="padding-top: 10px;"><input type="text" name="data[menu_font_4_color]" value="{$data.menu_font_4_color}" size="30">&nbsp;</td>
    						<td style="padding-top: 10px;">
    							<table border="0" cellpadding="0" cellspacing="0" width="18">
    							<tr>
       								<td bgcolor="#{$data.menu_font_4_color}" id="tdcolor_menu_font_4_color"><a href="#" onclick="pick('pick_menu_font_4_color','tdcolor_menu_font_4_color','data[menu_font_4_color]');return false;" name="pick_menu_font_4_color" id="pick_menu_font_4_color"><img src="{$site_root}{$template_root}/images/color.gif" border="0" height="17" width="18"></a></td>
    							</tr>
    							</table>
    						</td>
    						<td style="padding-top: 10px;">&nbsp;&nbsp;
    							<span onmouseover="ShowHelpDiv('help_font_4');" onmouseout="HideHelpDiv('help_font_4');" style="background-color: #ffffff; padding: 2px; border: 1px solid #666666; cursor: help;">&nbsp;<b>?</b>&nbsp;</span>
    							<div id="help_font_4" style="display: none; position: absolute;">&nbsp;<img src="{$site_root}{$template_root}/images/menu_font_4_screen.gif"></div>
    						</td>
    					</tr>
    					<tr>
							<td style="padding-top: 10px;"><font class="text_head">{$lang.organizer.content_color}:</font>&nbsp;</td>
    						<td style="padding-top: 10px;"><input type="text" name="data[content_color]" value="{$data.content_color}" size="30">&nbsp;</td>
    						<td style="padding-top: 10px;">
    							<table border="0" cellpadding="0" cellspacing="0" width="18">
    							<tr>
       								<td bgcolor="#{$data.content_color}" id="tdcolor_content_color"><a href="#" onclick="pick('pick_content_color','tdcolor_content_color','data[content_color]');return false;" name="pick_content_color" id="pick_content_color"><img src="{$site_root}{$template_root}/images/color.gif" border="0" height="17" width="18"></a></td>
    							</tr>
    							</table>
    						</td>
    						<td style="padding-top: 10px;">&nbsp;&nbsp;
    							<span onmouseover="ShowHelpDiv('help_content_bg');" onmouseout="HideHelpDiv('help_content_bg');" style="background-color: #ffffff; padding: 2px; border: 1px solid #666666; cursor: help;">&nbsp;<b>?</b>&nbsp;</span>
    						</td>
    					</tr>
    					<tr>
							<td style="padding-top: 10px;"><font class="text_head">{$lang.organizer.search_color}:</font>&nbsp;</td>
    						<td style="padding-top: 10px;"><input type="text" name="data[search_color]" value="{$data.search_color}" size="30">&nbsp;</td>
    						<td style="padding-top: 10px;">
    							<table border="0" cellpadding="0" cellspacing="0" width="18">
    							<tr>
       								<td bgcolor="#{$data.search_color}" id="tdcolor_search_color"><a href="#" onclick="pick('pick_search_color','tdcolor_search_color','data[search_color]');return false;" name="pick_search_color" id="pick_search_color"><img src="{$site_root}{$template_root}/images/color.gif" border="0" height="17" width="18"></a></td>
    							</tr>
    							</table>
    						</td>
    						<td style="padding-top: 10px;">&nbsp;&nbsp;
    							<span onmouseover="ShowHelpDiv('help_quick_search');" onmouseout="HideHelpDiv('help_quick_search');" style="background-color: #ffffff; padding: 2px; border: 1px solid #666666; cursor: help;">&nbsp;<b>?</b>&nbsp;</span>
    						</td>
    					</tr>
    					<tr>
							<td style="padding-top: 10px;"><font class="text_head">{$lang.organizer.main_text_color}:</font>&nbsp;</td>
    						<td style="padding-top: 10px;"><input type="text" name="data[main_text_color]" value="{$data.main_text_color}" size="30">&nbsp;</td>
    						<td style="padding-top: 10px;">
    							<table border="0" cellpadding="0" cellspacing="0" width="18">
    							<tr>
       								<td bgcolor="#{$data.main_text_color}" id="tdcolor_main_text_color"><a href="#" onclick="pick('pick_main_text_color','tdcolor_main_text_color','data[main_text_color]');return false;" name="pick_main_text_color" id="pick_main_text_color"><img src="{$site_root}{$template_root}/images/color.gif" border="0" height="17" width="18"></a></td>
    							</tr>
    							</table>
    						</td>
    						<td style="padding-top: 10px;">&nbsp;&nbsp;
    							<span onmouseover="ShowHelpDiv('help_main_text');" onmouseout="HideHelpDiv('help_main_text');" style="background-color: #ffffff; padding: 2px; border: 1px solid #666666; cursor: help;">&nbsp;<b>?</b>&nbsp;</span>
    						</td>
    					</tr>
    					<tr>
							<td style="padding-top: 10px;"><font class="text_head">{$lang.organizer.text_hidden}:</font>&nbsp;</td>
    						<td style="padding-top: 10px;"><input type="text" name="data[text_hidden]" value="{$data.text_hidden}" size="30">&nbsp;</td>
    						<td style="padding-top: 10px;">
    							<table border="0" cellpadding="0" cellspacing="0" width="18">
    							<tr>
       								<td bgcolor="#{$data.text_hidden}" id="tdcolor_text_hidden"><a href="#" onclick="pick('pick_text_hidden','tdcolor_text_hidden','data[text_hidden]');return false;" name="pick_text_hidden" id="pick_text_hidden"><img src="{$site_root}{$template_root}/images/color.gif" border="0" height="17" width="18"></a></td>
    							</tr>
    							</table>
    						</td>
    						<td style="padding-top: 10px;">&nbsp;&nbsp;
    							<span onmouseover="ShowHelpDiv('help_hidden_text');" onmouseout="HideHelpDiv('help_hidden_text');" style="background-color: #ffffff; padding: 2px; border: 1px solid #666666; cursor: help;">&nbsp;<b>?</b>&nbsp;</span>
    						</td>
    					</tr>
    					<tr>
							<td style="padding-top: 10px;"><font class="text_head">{$lang.organizer.big_bg}:</font>&nbsp;</td>
    						<td style="padding-top: 10px;" colspan="3">
	    						<table cellpadding="0" cellspacing="0" border="0">
	    							<tr>
	    								<td><input type=radio name="bg_type" value="color" id="type_color" {if $data.bg_picture_path eq ''}checked{/if}>&nbsp;</td>
	    								<td><label for="type_color"><font class="text">{$lang.organizer.big_bg_color}</font></label></td>
	    								<td style="padding-left: 10px;"><input type="text" name="data[big_bg_color]" value="{$data.big_bg_color}" size="30">&nbsp;</td>
			    						<td>
			    							<table border="0" cellpadding="0" cellspacing="0" width="18">
			    							<tr>
			       								<td bgcolor="#{$data.big_bg_color}" id="tdcolor_big_bg_color"><a href="#" onclick="pick('pick_big_bg_color','tdcolor_big_bg_color','data[big_bg_color]');return false;" name="pick_big_bg_color" id="pick_big_bg_color"><img src="{$site_root}{$template_root}/images/color.gif" border="0" height="17" width="18"></a></td>
			    							</tr>
			    							</table>
			    						</td>
	    							</tr>
	    							<tr>
	    								<td><input type=radio name="bg_type" value="image" id="type_image" {if $data.bg_picture_path ne ''}checked{/if}>&nbsp;</td>
	    								<td><label for="type_image"><font class="text">{$lang.organizer.big_bg_image}</font></label>&nbsp;</td>
										<td style="padding-left: 10px;">
										<input type="file" name="bg_picture_path">
										</td>
										<td>&nbsp;{if $data.bg_picture_path ne ''}<a href="{$data.bg_picture_path}" target="_blank">{$lang.button.view}</a>{/if}</td>
	    							</tr>
	    						</table>
	    					</td>
    					</tr>
    					<tr>
    						<td></td>
    						<td colspan="2" style="padding-top: 10px;">
    							<input type="submit" value="{$lang.button.save}">&nbsp;&nbsp;&nbsp;
    							<input type="button" value="{$lang.organizer.view_result}" onclick="document.location.href='homepage.php'">
    						</td>
    					</tr>
    				</table>
					<script type="text/javascript" src="{$site_root}{$template_root}/js/ColorPicker.js?v=0000"></script>
					<script language="JavaScript">cp.writeDiv()</script><div id="colorPickerDiv" style="position:absolute;visibility:hidden;"><table bgcolor="#cccccc" border="0" cellpadding="0" cellspacing="1"><tbody><tr><td bgcolor="#000000"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#000000',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#000000',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#000033"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#000033',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#000033',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#000066"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#000066',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#000066',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#000099"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#000099',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#000099',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#0000cc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#0000CC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#0000CC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#0000ff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#0000FF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#0000FF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#330000"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#330000',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#330000',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#330033"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#330033',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#330033',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#330066"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#330066',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#330066',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#330099"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#330099',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#330099',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#3300cc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#3300CC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#3300CC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#3300ff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#3300FF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#3300FF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#660000"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#660000',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#660000',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#660033"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#660033',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#660033',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#660066"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#660066',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#660066',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#660099"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#660099',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#660099',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#6600cc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#6600CC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#6600CC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#6600ff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#6600FF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#6600FF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td></tr><tr><td bgcolor="#990000"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#990000',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#990000',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#990033"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#990033',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#990033',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#990066"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#990066',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#990066',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#990099"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#990099',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#990099',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#9900cc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#9900CC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#9900CC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#9900ff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#9900FF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#9900FF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cc0000"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CC0000',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CC0000',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cc0033"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CC0033',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CC0033',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cc0066"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CC0066',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CC0066',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cc0099"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CC0099',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CC0099',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cc00cc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CC00CC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CC00CC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cc00ff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CC00FF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CC00FF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ff0000"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FF0000',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FF0000',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ff0033"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FF0033',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FF0033',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ff0066"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FF0066',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FF0066',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ff0099"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FF0099',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FF0099',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ff00cc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FF00CC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FF00CC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ff00ff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FF00FF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FF00FF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td></tr><tr><td bgcolor="#003300"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#003300',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#003300',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#003333"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#003333',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#003333',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#003366"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#003366',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#003366',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#003399"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#003399',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#003399',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#0033cc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#0033CC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#0033CC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#0033ff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#0033FF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#0033FF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#333300"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#333300',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#333300',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#333333"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#333333',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#333333',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#333366"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#333366',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#333366',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#333399"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#333399',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#333399',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#3333cc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#3333CC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#3333CC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#3333ff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#3333FF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#3333FF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#663300"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#663300',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#663300',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#663333"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#663333',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#663333',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#663366"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#663366',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#663366',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#663399"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#663399',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#663399',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#6633cc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#6633CC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#6633CC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#6633ff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#6633FF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#6633FF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td></tr><tr><td bgcolor="#993300"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#993300',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#993300',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#993333"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#993333',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#993333',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#993366"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#993366',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#993366',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#993399"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#993399',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#993399',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#9933cc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#9933CC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#9933CC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#9933ff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#9933FF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#9933FF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cc3300"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CC3300',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CC3300',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cc3333"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CC3333',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CC3333',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cc3366"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CC3366',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CC3366',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cc3399"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CC3399',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CC3399',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cc33cc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CC33CC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CC33CC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cc33ff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CC33FF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CC33FF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ff3300"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FF3300',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FF3300',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ff3333"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FF3333',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FF3333',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ff3366"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FF3366',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FF3366',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ff3399"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FF3399',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FF3399',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ff33cc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FF33CC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FF33CC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ff33ff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FF33FF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FF33FF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td></tr><tr><td bgcolor="#006600"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#006600',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#006600',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#006633"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#006633',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#006633',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#006666"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#006666',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#006666',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#006699"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#006699',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#006699',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#0066cc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#0066CC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#0066CC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#0066ff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#0066FF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#0066FF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#336600"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#336600',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#336600',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#336633"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#336633',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#336633',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#336666"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#336666',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#336666',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#336699"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#336699',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#336699',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#3366cc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#3366CC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#3366CC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#3366ff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#3366FF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#3366FF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#666600"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#666600',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#666600',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#666633"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#666633',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#666633',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#666666"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#666666',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#666666',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#666699"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#666699',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#666699',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#6666cc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#6666CC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#6666CC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#6666ff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#6666FF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#6666FF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td></tr><tr><td bgcolor="#996600"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#996600',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#996600',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#996633"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#996633',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#996633',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#996666"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#996666',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#996666',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#996699"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#996699',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#996699',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#9966cc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#9966CC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#9966CC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#9966ff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#9966FF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#9966FF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cc6600"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CC6600',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CC6600',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cc6633"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CC6633',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CC6633',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cc6666"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CC6666',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CC6666',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cc6699"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CC6699',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CC6699',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cc66cc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CC66CC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CC66CC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cc66ff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CC66FF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CC66FF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ff6600"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FF6600',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FF6600',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ff6633"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FF6633',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FF6633',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ff6666"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FF6666',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FF6666',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ff6699"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FF6699',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FF6699',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ff66cc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FF66CC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FF66CC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ff66ff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FF66FF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FF66FF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td></tr><tr><td bgcolor="#009900"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#009900',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#009900',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#009933"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#009933',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#009933',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#009966"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#009966',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#009966',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#009999"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#009999',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#009999',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#0099cc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#0099CC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#0099CC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#0099ff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#0099FF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#0099FF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#339900"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#339900',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#339900',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#339933"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#339933',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#339933',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#339966"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#339966',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#339966',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#339999"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#339999',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#339999',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#3399cc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#3399CC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#3399CC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#3399ff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#3399FF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#3399FF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#669900"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#669900',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#669900',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#669933"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#669933',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#669933',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#669966"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#669966',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#669966',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#669999"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#669999',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#669999',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#6699cc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#6699CC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#6699CC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#6699ff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#6699FF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#6699FF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td></tr><tr><td bgcolor="#999900"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#999900',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#999900',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#999933"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#999933',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#999933',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#999966"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#999966',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#999966',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#999999"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#999999',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#999999',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#9999cc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#9999CC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#9999CC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#9999ff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#9999FF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#9999FF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cc9900"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CC9900',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CC9900',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cc9933"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CC9933',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CC9933',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cc9966"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CC9966',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CC9966',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cc9999"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CC9999',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CC9999',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cc99cc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CC99CC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CC99CC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cc99ff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CC99FF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CC99FF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ff9900"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FF9900',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FF9900',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ff9933"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FF9933',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FF9933',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ff9966"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FF9966',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FF9966',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ff9999"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FF9999',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FF9999',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ff99cc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FF99CC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FF99CC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ff99ff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FF99FF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FF99FF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td></tr><tr><td bgcolor="#00cc00"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#00CC00',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#00CC00',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#00cc33"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#00CC33',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#00CC33',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#00cc66"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#00CC66',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#00CC66',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#00cc99"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#00CC99',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#00CC99',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#00cccc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#00CCCC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#00CCCC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#00ccff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#00CCFF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#00CCFF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#33cc00"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#33CC00',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#33CC00',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#33cc33"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#33CC33',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#33CC33',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#33cc66"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#33CC66',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#33CC66',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#33cc99"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#33CC99',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#33CC99',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#33cccc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#33CCCC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#33CCCC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#33ccff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#33CCFF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#33CCFF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#66cc00"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#66CC00',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#66CC00',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#66cc33"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#66CC33',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#66CC33',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#66cc66"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#66CC66',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#66CC66',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#66cc99"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#66CC99',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#66CC99',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#66cccc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#66CCCC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#66CCCC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#66ccff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#66CCFF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#66CCFF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td></tr><tr><td bgcolor="#99cc00"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#99CC00',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#99CC00',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#99cc33"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#99CC33',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#99CC33',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#99cc66"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#99CC66',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#99CC66',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#99cc99"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#99CC99',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#99CC99',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#99cccc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#99CCCC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#99CCCC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#99ccff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#99CCFF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#99CCFF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cccc00"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CCCC00',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CCCC00',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cccc33"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CCCC33',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CCCC33',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cccc66"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CCCC66',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CCCC66',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cccc99"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CCCC99',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CCCC99',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#cccccc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CCCCCC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CCCCCC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ccccff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CCCCFF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CCCCFF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ffcc00"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FFCC00',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FFCC00',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ffcc33"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FFCC33',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FFCC33',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ffcc66"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FFCC66',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FFCC66',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ffcc99"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FFCC99',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FFCC99',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ffcccc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FFCCCC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FFCCCC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ffccff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FFCCFF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FFCCFF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td></tr><tr><td bgcolor="#00ff00"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#00FF00',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#00FF00',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#00ff33"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#00FF33',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#00FF33',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#00ff66"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#00FF66',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#00FF66',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#00ff99"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#00FF99',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#00FF99',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#00ffcc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#00FFCC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#00FFCC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#00ffff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#00FFFF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#00FFFF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#33ff00"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#33FF00',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#33FF00',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#33ff33"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#33FF33',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#33FF33',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#33ff66"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#33FF66',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#33FF66',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#33ff99"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#33FF99',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#33FF99',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#33ffcc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#33FFCC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#33FFCC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#33ffff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#33FFFF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#33FFFF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#66ff00"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#66FF00',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#66FF00',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#66ff33"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#66FF33',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#66FF33',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#66ff66"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#66FF66',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#66FF66',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#66ff99"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#66FF99',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#66FF99',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#66ffcc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#66FFCC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#66FFCC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#66ffff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#66FFFF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#66FFFF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td></tr><tr><td bgcolor="#99ff00"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#99FF00',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#99FF00',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#99ff33"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#99FF33',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#99FF33',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#99ff66"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#99FF66',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#99FF66',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#99ff99"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#99FF99',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#99FF99',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#99ffcc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#99FFCC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#99FFCC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#99ffff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#99FFFF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#99FFFF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ccff00"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CCFF00',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CCFF00',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ccff33"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CCFF33',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CCFF33',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ccff66"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CCFF66',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CCFF66',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ccff99"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CCFF99',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CCFF99',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ccffcc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CCFFCC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CCFFCC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ccffff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#CCFFFF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#CCFFFF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ffff00"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FFFF00',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FFFF00',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ffff33"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FFFF33',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FFFF33',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ffff66"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FFFF66',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FFFF66',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ffff99"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FFFF99',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FFFF99',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ffffcc"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FFFFCC',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FFFFCC',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td><td bgcolor="#ffffff"><font face="VERDANA" size="-3"><a href="#" onclick="ColorPicker_pickColor('#FFFFFF',window.popupWindowObjects[1]);return false;" onmouseover="ColorPicker_highlightColor('#FFFFFF',window.document)" style="text-decoration: none;">&nbsp;&nbsp;&nbsp;</a></font></td></tr><tr><td style="background-color: rgb(51, 204, 0);" colspan="9" id="colorPickerSelectedColor" bgcolor="#ffffff">&nbsp;</td><td colspan="9" id="colorPickerSelectedColorValue" align="center" bgcolor="#ffffff">#33CC00</td></tr></tbody></table></div>
					</form>
					{/if}
					</div>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
	<!-- end main cell -->
</td>
{if $type eq 'layout'}
{literal}
<script type="text/javascript">
function HighLight(section, state) {
	if (document.getElementById('section_'+section)) {
		if (state == 1) {
			setElementOpacity('section_'+section, 0.4);
		} else {
			setElementOpacity('section_'+section, 1.0);
		}
	}
	return;
}

function setElementOpacity(sElemId, nOpacity)
{

  var opacityProp = getOpacityProperty();
  var elem = document.getElementById(sElemId);

  if (!elem || !opacityProp) return; // error

  if (opacityProp=="filter")  // Internet Explorer 5.5+
  {
    nOpacity *= 100;

    var oAlpha = elem.filters['DXImageTransform.Microsoft.alpha'] || elem.filters.alpha;
    if (oAlpha) oAlpha.opacity = nOpacity;
    else elem.style.filter += "progid:DXImageTransform.Microsoft.Alpha(opacity="+nOpacity+")";
  }
  else
    elem.style[opacityProp] = nOpacity;
}

function getOpacityProperty()
{
  if (typeof document.body.style.opacity == 'string') {
    return 'opacity';
  } else if (typeof document.body.style.MozOpacity == 'string') {
    return 'MozOpacity';
  } else if (typeof document.body.style.KhtmlOpacity == 'string') {
    return 'KhtmlOpacity';
  } else if (document.body.filters && navigator.appVersion.match(/MSIE ([\d.]+);/)[1]>=5.5) {
    return 'filter'
  } else {
  	return false;
  }
}

var req = null;

function InitXMLHttpRequest() {
	// Make a new XMLHttp object
	if (window.XMLHttpRequest) {
		req = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		req = new ActiveXObject("Microsoft.XMLHTTP");
	}
}

function ChangeHome(destination) {
	disabled_ids = Array(12);
	{/literal}
	{foreach name=disabled_ids from=$disabled_data_ids item=item}
	disabled_ids[{$smarty.foreach.disabled_ids.iteration}] = {$item};
	{/foreach}
	{literal}
	var statuses = new Array(12);
	var use_shout = {/literal}{$form.use_shoutbox_feature}{literal};
	for (var i=1; i<13; i++) {
		if (!(use_shout==0 && i==2) && !disabled_ids[i]) {
			statuses[i-1] = document.getElementById('area_'+i).checked;
		}
	}

	InitXMLHttpRequest();
	// Load the result from the response page
	if (req) {
		req.onreadystatechange = function() {
			if (req.readyState == 4) {
				destination.innerHTML = req.responseText;
			} else {
				destination.innerHTML = "<div align='center' style='height: 300px;'>Loading data...</div>";
			}
		}
		req.open("GET", "organizer.php?sel=ajax_req&statuses=" + statuses, true);
		req.send(null);
	} else {
		destination.innerHTML = 'Browser unable to create XMLHttp Object';
	}
}
</script>
{/literal}
{elseif $type eq 'page_styles'}
{literal}
<script type="text/javascript">
function ShowHelpDiv(div_id) {
	document.getElementById(div_id).style.display = 'inline';
	return;
}
function HideHelpDiv(div_id) {
	document.getElementById(div_id).style.display = 'none';
	return;
}

</script>
{/literal}
{/if}
{include file="$gentemplates/index_bottom.tpl"}