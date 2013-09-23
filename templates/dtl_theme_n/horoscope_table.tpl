{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
	<!-- begin main cell -->
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr valign="middle"><td height="25px" colspan=2>
			<div class="header">{$lang.section.horoscope}</div>
	</td></tr>
	{if $form.err}
	<tr><td height="25px" colspan="2"><div class="error_msg">{$form.err}</div></td></tr>
	{/if}
	{if $form.page == "main"}
	<tr><td colspan=2 class="text">
		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
		{section name=s loop=$signs}
			{if $smarty.section.s.index is div by 3}<td style="padding-right:10px">{/if}
			<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td height="70px" width="60px"><a href="{$signs[s].sign_link}"><img src="{$site_root}{$template_root}/horoicons/{$signs[s].sign}.gif" width="47px" height="47px" border="0" alt=""></a></td>
				<td height="70px" width="100px" class="text"><a href="{$signs[s].sign_link}" {if $signs[s].my_sign}class="link_active"{/if}><u>{$signs[s].sign_name}</u></a> {if $signs[s].my_sign}<sup>{$header.your}</sup>{/if}</td>
			</tr>
			</table>
			{if $smarty.section.s.index_next is div by 3}</td>{/if}
		{/section}
		</tr>
		</table>
	</td></tr>
	{elseif $form.page == "view"}
	<tr><td colspan=2 class="text" width="100%">
		<div style="height: 2px; margin: 0px"><img src="{$site_root}{$template_root}/images/empty.gif" height="2px" alt=""></div>
		<div class="content_active" style="height: 35px; margin: 0px">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr valign="middle">
				<td width="30px" height="35px" style="padding-left:10px"><img src="{$site_root}{$template_root}/horoicons/{$sign_info.sign}_small.gif" width="24px" height="24px" alt=""></td>
				<td width="200px" class="text" height="35px"><b>{$sign_info.sign_name}</b> ({$sign_info.sign_dates})</td>
				<td align="right" style="padding-right:10px">
					{if $form.back_link}
					<a href="{$form.back_link}">{$header.back_to_profile}</a>
					{else}
					<a href="{$form.horoscope_link}">{$header.back_to_list}</a>
					{/if}

				</td>
			</tr>
			</table>
		</div>
		<div class="content" style="margin-top: 10px;">
		<div class="header">{$header.weekly_scope}</div>
			<div style="padding: 5px 10px">
				{$sign_info.weekly_text}
				{$sign_info.weekly_horo_text}
				<br><a href="{$sign_info.weekly_horo_link}" target="blank">{$sign_info.weekly_horo_title}</a>
			</div>
		</div>
		<div class="content" style="margin-top: 10px;">
		<div class="header">{$header.love_scope}</div>
			<div style="padding: 5px 10px">
				{$sign_info.love_text}
			</div>
		</div>
	</td></tr>
	{elseif $form.page == "match"}
	{/if}
	</table>
	<!-- end main cell -->
</td>
{include file="$gentemplates/index_bottom.tpl"}
