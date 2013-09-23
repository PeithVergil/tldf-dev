{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
	<!-- begin main cell -->
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top" class="header" style="padding: 5px 0px 10px 0px;">{$lang.section.clubs}</td>
		<td valign="top" align="right" style="padding: 5px 0px 10px 0px;">
			<table cellpadding="0" cellspacing="0">
			<tr>
				<td><img src="{$site_root}{$template_root}/images/create_my_club.gif"></td>
				<td style="padding-left: 2px;"><a href="club.php?sel=create"><b>{$header_s.club_menu_3}</b></a></td>
				<td style="padding-left: 15px;"><img src="{$site_root}{$template_root}/images/my_clubs_icon.gif"></td>
				<td style="padding-left: 2px;"><a href="club.php?sel=my_club"><b>{$lang.club.club_menu_1}</b></a></td>
			</tr>
			</table>
		</td>
	</tr>
	{if $form.err}
	<tr>
		<td colspan="2"><div class="error_msg">{$form.err}</div></td>
	</tr>
	{/if}
	<tr>
		<td colspan="2" valign="top">{strip}
			<div id="index_quick_search" class="index_quick_search">
				<form action="club.php?sel=search" method="post" style="margin: 10px;" id="s_form">
				<table cellpadding="0" cellspacing="0">
				<tr>
					<td valign="top" style="padding-left: 15px;">
						<table cellpadding="0" cellspacing="0">
							<tr>
								<td class="text_head" valign="top">{$lang.club.search_keyword}</td>
							</tr>
							<tr>
								<td valign="top" style="padding-top: 2px;"><input type="text" name="keywords" id="keywords" value="{$keywords}" maxlength="200" style="width: 335px;"></td>
							</tr>
							<tr>
								<td valign="top" style="padding-top: 2px; padding-bottom: 2px;">
									<font style="font-family: Tahoma; font-size: 10px; color:#7f7f7f;">{$lang.club.sample}:</font>&nbsp;
									<span onclick="PasteSample('{$lang.club.sample_word}'); return false;" style="cursor: pointer;"><font style=" border-bottom: 1px dashed #7f7f7f; font-family: Tahoma; font-size: 10px; color: #7f7f7f; ">{$lang.club.sample_word}</font></span>
								</td>
							</tr>
						</table>
					</td>
					<td valign="middle" style="padding-left: 5px;"><input type="submit" class="big_button" value="{$button.search}"></td>
				</tr>
				</table>
				</form>
			</div>
		</td>{/strip}
	</tr>
	</table>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top" class="text" style="padding-top: 5px;">
			<div class="content" style=" margin: 0px;">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td class="header" style="padding: 10px 0px 0px 12px;" valign="top">{$lang.club.club_menu_2}</td>
			</tr>
			<tr>
				<td valign="top" style="padding: 10px 10px 5px 12px;">
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="50%" valign="top">
							<table cellpadding="0" cellspacing="0" width="100%">
							{section name=s loop=$club_categories start=0 max=$half_num_cat}
							<tr>
								<td valign="top" width="180" style="padding-bottom: 10px;">{if $club_categories[s].link}<a href="{$club_categories[s].link}"><b>{/if}{$club_categories[s].name}{if $club_categories[s].link}</b></a>{/if}</td>
								<td valign="top" style="font-family: Tahoma; font-size: 10px; {if $club_categories[s].link} color: #000000; font-weight: bold; {else} color: #7f7f7f; {/if} text-decoration: none;" >{$club_categories[s].clubs_num}&nbsp;{$lang.club.clubs}</td>
							</tr>
							{/section}
							</table>
						</td>
						<td width="50%" valign="top"><table cellpadding="0" cellspacing="0">
						{section name=s loop=$club_categories start=$half_num_cat}
							<tr>
								<td valign="top" width="180" style="padding-bottom: 10px;">{if $club_categories[s].link}<a href="{$club_categories[s].link}"><b>{/if}{$club_categories[s].name}{if $club_categories[s].link}</b></a>{/if}</td>
								<td valign="top" style="font-family: Tahoma; font-size: 10px; {if $club_categories[s].link} color: #000000; font-weight: bold; {else} color: #7f7f7f; {/if} text-decoration: none;" >{$club_categories[s].clubs_num}&nbsp;{$lang.club.clubs}</td>
							</tr>
						{/section}
						</table></td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
			</div>
		</td>
	</tr>
	</table>
	<!-- end main cell -->
</td>
{literal}
<script type="text/javascript">
	function PasteSample(word) {
		document.forms['s_form'].keywords.value = word;
		return;
	}
</script>
{/literal}
{include file="$gentemplates/index_bottom.tpl"}