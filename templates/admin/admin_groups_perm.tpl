{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.groups_perm}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.groups_perm_list}</div>
<div class="statistics">
	<table class="table_main" cellpadding="5" cellspacing="1" width="100%">
		<tr class="header">
			<td align="right" style="border-right: 1px solid silver;"><b>{$header.module_name}</b></td>
			<td align="center" width="33"><a href="admin_groups.php?sel=edit&id={$smarty.const.MM_ELITE_GUY_ID}"><b>{$header.elite_guy}</b></a></td>
			<td align="center" width="33"><a href="admin_groups.php?sel=edit&id={$smarty.const.MM_ELITE_LADY_ID}"><b>{$header.elite_lady}</b></a></td>
			<td align="center" width="33"><a href="admin_groups.php?sel=edit&id={$smarty.const.MM_INACT_ELITE_GUY_ID}"><b>{$header.inact_elite_guy}</b></a></td>
			<td align="center" width="33" style="border-right: 1px solid silver;"><a href="admin_groups.php?sel=edit&id={$smarty.const.MM_INACT_ELITE_LADY_ID}"><b>{$header.inact_elite_lady}</b></a></td>
			<td align="center" width="33"><a href="admin_groups.php?sel=edit&id={$smarty.const.MM_PLATINUM_GUY_ID}"><b>{$header.plat_guy}</b></a></td>
			<td align="center" width="33"><a href="admin_groups.php?sel=edit&id={$smarty.const.MM_PLATINUM_LADY_ID}"><b>{$header.plat_lady}</b></a></td>
			<td align="center" width="33"><a href="admin_groups.php?sel=edit&id={$smarty.const.MM_INACT_PLATINUM_GUY_ID}"><b>{$header.inact_plat_guy}</b></a></td>
			<td align="center" width="33" style="border-right: 1px solid silver;"><a href="admin_groups.php?sel=edit&id={$smarty.const.MM_INACT_PLATINUM_LADY_ID}"><b>{$header.inact_plat_lady}</b></a></td>
			<td align="center" width="33"><a href="admin_groups.php?sel=edit&id={$smarty.const.MM_REGULAR_GUY_ID}"><b>{$header.reg_guy}</b></a></td>
			<td align="center" width="33"><a href="admin_groups.php?sel=edit&id={$smarty.const.MM_REGULAR_LADY_ID}"><b>{$header.reg_lady}</b></a></td>
			<td align="center" width="33"><a href="admin_groups.php?sel=edit&id={$smarty.const.MM_INACT_REGULAR_GUY_ID}"><b>{$header.inact_reg_guy}</b></a></td>
			<td align="center" width="33" style="border-right: 1px solid silver;"><a href="admin_groups.php?sel=edit&id={$smarty.const.MM_INACT_REGULAR_LADY_ID}"><b>{$header.inact_reg_lady}</b></a></td>
			<td align="center" width="33"><a href="admin_groups.php?sel=edit&id={$smarty.const.MM_TRIAL_GUY_ID}"><b>{$header.trial_guy}</b></a></td>
			<td align="center" width="33"><a href="admin_groups.php?sel=edit&id={$smarty.const.MM_TRIAL_LADY_ID}"><b>{$header.trial_lady}</b></a></td>
			<td align="center" width="33"><a href="admin_groups.php?sel=edit&id={$smarty.const.MM_INACT_TRIAL_GUY_ID}"><b>{$header.inact_trial_guy}</b></a></td>
			<td align="center" width="33" style="border-right: 1px solid silver;"><a href="admin_groups.php?sel=edit&id={$smarty.const.MM_INACT_TRIAL_LADY_ID}"><b>{$header.inact_trial_lady}</b></a></td>
			<td align="center" width="33"><a href="admin_groups.php?sel=edit&id={$smarty.const.MM_SIGNUP_GUY_ID}"><b>{$header.signup_guy}</b></a></td>
			<td align="center" width="33"><a href="admin_groups.php?sel=edit&id={$smarty.const.MM_SIGNUP_LADY_ID}"><b>{$header.signup_lady}</b></a></td>
		</tr>
		{foreach item=item from=$perm name=p}
		<tr>
			<td align="right" style="border-right: 1px solid silver;"><b>{$item.name|escape}</b></td>
			<td align="center"><b>{$item.eg.active}</b></td>
			<td align="center"><b>{$item.el.active}</b></td>
			<td align="center"><b>{$item.egh.active}</b></td>
			<td align="center" style="border-right: 1px solid silver;"><b>{$item.elh.active}</b></td>
			<td align="center"><b>{$item.pg.active}</b></td>
			<td align="center"><b>{$item.pl.active}</b></td>
			<td align="center"><b>{$item.pgh.active}</b></td>
			<td align="center" style="border-right: 1px solid silver;"><b>{$item.plh.active}</b></td>
			<td align="center"><b>{$item.rg.active}</b></td>
			<td align="center"><b>{$item.rl.active}</b></td>
			<td align="center"><b>{$item.rgh.active}</b></td>
			<td align="center" style="border-right: 1px solid silver;"><b>{$item.rlh.active}</b></td>
			<td align="center"><b>{$item.tg.active}</b></td>
			<td align="center"><b>{$item.tl.active}</b></td>
			<td align="center"><b>{$item.tgh.active}</b></td>
			<td align="center" style="border-right: 1px solid silver;"><b>{$item.tlh.active}</b></td>
			<td align="center"><b>{$item.sg.active}</b></td>
			<td align="center"><b>{$item.sl.active}</b></td>
		</tr>
		{/foreach}
		<tr>
			<td style="height:5px;" colspan="100%"></td>
		</tr>
		{foreach item=item from=$add_perm name=a}
		<tr>
			<td align="right" style="border-right: 1px solid silver;"><b>{$item.name|escape}</b></td>
			<td align="center"><b>{$item.eg.value}</b></td>
			<td align="center"><b>{$item.el.value}</b></td>
			<td align="center"><b>{$item.egh.value}</b></td>
			<td align="center" style="border-right: 1px solid silver;"><b>{$item.elh.value}</b></td>
			<td align="center"><b>{$item.pg.value}</b></td>
			<td align="center"><b>{$item.pl.value}</b></td>
			<td align="center"><b>{$item.pgh.value}</b></td>
			<td align="center" style="border-right: 1px solid silver;"><b>{$item.plh.value}</b></td>
			<td align="center"><b>{$item.rg.value}</b></td>
			<td align="center"><b>{$item.rl.value}</b></td>
			<td align="center"><b>{$item.rgh.value}</b></td>
			<td align="center" style="border-right: 1px solid silver;"><b>{$item.rlh.value}</b></td>
			<td align="center"><b>{$item.tg.value}</b></td>
			<td align="center"><b>{$item.tl.value}</b></td>
			<td align="center"><b>{$item.tgh.value}</b></td>
			<td align="center" style="border-right: 1px solid silver;"><b>{$item.tlh.value}</b></td>
			<td align="center"><b>{$item.sg.value}</b></td>
			<td align="center"><b>{$item.sl.value}</b></td>
		</tr>
		{/foreach}
		{if $more_modules}
		<tr>
			<td style="height:5px;" colspan="100%"></td>
		</tr>
		<tr>
			<td align="right" valign="top"><b>{$header.more_avilable}</b></td>
			<td align="left" colspan="18" width="594">{$more_modules}</td>
		</tr>
		{/if}
	</table>
</div>
{include file="$admingentemplates/admin_bottom.tpl"}