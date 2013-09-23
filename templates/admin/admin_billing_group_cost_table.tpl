{include file="$admingentemplates/admin_top.tpl"}
{strip}
<font class=red_header>{$lang.groups.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.list_group}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.groups_costs_2}</div>
<form action="admin_pays.php" name="cost" method="post">
	<input type="hidden" name="sel" value="speed">
	{if $cmd == 'edit'}
		<input type="hidden" name="cmd" value="edit">
	{else}
		<input type="hidden" name="cmd" value="add">
	{/if}
	<table border="0" class="table_main" cellspacing="1" cellpadding="5" align="center" width="90%">
		<tr class="table_header">
			<td class="main_header_text" align="center" width="10">{$header.number}</td>
			<td class="main_header_text" align="center" width="10">{$header.id}</td>
			<td class="main_header_text" align="center">{$header.group}</td>
			<td class="main_header_text" align="center" >{$header.period} - {$header.pay}</td>
		</tr>
		{foreach item=g from=$groups name=groups}
			<tr bgcolor="#ffffff">
				<td class="main_content_text" align="center">{$smarty.foreach.groups.iteration}</td>
				<td class="main_content_text" align="center">{$g.id}</td>
				<td class="main_content_text" align="center">{$g.name}</td>
				<td style="padding:0px;">
					<table width="100%" border="0" cellspacing="0" cellpadding="3">
						{foreach item=p from=$g.period name=periods}
							<tr style="{if $p.id == $edit.id}background-color:yellow;{/if}{if !$p.status}color:red;{/if}">
								<td width="8%"{if ! $smarty.foreach.periods.last} style="border-bottom: 1px solid silver;"{/if}>
									ID={$p.id}
								</td>
								<td width="10%"{if ! $smarty.foreach.periods.last} style="border-bottom: 1px solid silver;"{/if}>
									{if $p.recurring}{$lang.pays.recurring}: {elseif $p.upgrade}upgrade: {else}block: {/if}
								</td>
								<td width="33%"{if ! $smarty.foreach.periods.last} style="border-bottom: 1px solid silver;"{/if}>
									Period = {$p.amount} {$p.period}: {$p.cost} {$form.costunits} / {$p.cost_2} {$form.costunits_2}
								</td>
								<td width="33%"{if ! $smarty.foreach.periods.last} style="border-bottom: 1px solid silver;"{/if}>
									{if $p.recurring}
										Trial = {$p.trial_amount} {$p.trial_period}: {$p.trial_cost} {$form.costunits} / {$p.trial_cost_2} {$form.costunits_2}
									{/if}
								</td>
								<td width="16%" align="right"{if ! $smarty.foreach.periods.last} style="border-bottom: 1px solid silver;"{/if}>
									<input type="button" value="{$button.edit}" class="button" onclick="javascript:location.href='./admin_pays.php?sel=groups&amp;cmd=edit&amp;id={$p.id}'">
									&nbsp;
									<input type="button" value="{$button.delete}" class="button" onclick="javascript:location.href='./admin_pays.php?sel=speed&amp;cmd=delete&amp;id={$p.id}'">
								</td>
							</tr>
						{foreachelse}
							<tr><td style="color:red;">{$header.no_period_defined}</td></tr>
						{/foreach}
					</table>
				</td>
			</tr>
		{foreachelse}
			<tr bgcolor="#ffffff" height="40">
				<td class="main_error_text" align="left" colspan="3">{$header.empty_group}</td>
			</tr>
		{/foreach}
	</table>
	<br>
	{if $groups}
		<table cellpadding="0" cellspacing="0" align="center" class="main_content_text">
			<tr>
				<td style="font-weight: bold;">
					{if $cmd == 'edit'}
						{$header.edit_period}{$edit.id}:&nbsp;&nbsp;
						<input type="hidden" name="id" value="{$edit.id}">
					{else}
						{$header.add_period}:&nbsp;&nbsp;
					{/if}
				</td>
				<td colspan="4">
					<select name="id_group" style="width:150px">
						<option value="0">- Select Group -</option>
						{foreach item=item from=$groups}
							<option value="{$item.id}"{if $item.id == $edit.id_group} selected="selected"{/if}>{$item.name}</option>
						{/foreach}
					</select>&nbsp;&nbsp;&nbsp;
				</td>
				<td>{$header.period}:&nbsp;</td>
				<td><input type="text" name="amount" value="{$edit.amount}" style="width: 30px" />&nbsp;&nbsp;</td>
				<td>
					<select name="period" style="width:70px">
						{foreach item=item key=key from=$header.periods}
							<option value="{$key}"{if $key == $edit.period} selected="selected"{/if}>{$item}</option>
						{/foreach}
					</select>&nbsp;&nbsp;&nbsp;
				</td>
				<td>{$header.pay}:&nbsp;</td>
				<td><input type="text" name="cost" value="{$edit.cost}" style="width: 50px;">&nbsp;{$form.costunits}&nbsp;</td>
				<td><input type="text" name="cost_2" value="{$edit.cost_2}" style="width: 50px;">&nbsp;{$form.costunits_2}&nbsp;</td>
				<td>
					{if $cmd == 'edit'}
						<input type="submit" value="{$button.save}" class="button" /> <input type="button" class="button" value="{$button.cancel}" onclick="location.href='./admin_pays.php?sel=groups'" />
					{elseif $cmd == 'add'}
						<input type="submit" value="{$button.add}" class="button" /> <input type="button" class="button" value="{$button.cancel}" onclick="location.href='./admin_pays.php?sel=groups'" />
					{else}
						<input type="submit" value="{$button.add}" class="button" /> 
					{/if}
				</td>
			</tr>
			<tr>
				<td></td>
				<td width="1"><input type="checkbox" name="recurring" value="1"{if $edit.recurring} checked="checked"{/if}></td>
				<td>{$lang.pays.recurring}</td>
				<td width="1"><input type="checkbox" name="upgrade" value="1"{if $edit.upgrade} checked="checked"{/if}></td>
				<td>upgrade</td>
				<td>Trial:&nbsp;</td>
				<td><input type="text" name="trial_amount" value="{$edit.trial_amount}" style="width: 30px;" />&nbsp;&nbsp;</td>
				<td>
					<select name="trial_period" style="width:70px">
						{foreach item=item key=key from=$header.periods}
							<option value="{$key}"{if $key == $edit.trial_period} selected="selected"{/if}>{$item}</option>
						{/foreach}
					</select>&nbsp;&nbsp;&nbsp;
				</td>
				<td>{$header.pay}:&nbsp;</td>
				<td><input type="text" name="trial_cost" value="{$edit.trial_cost}" style="width:50px">&nbsp;{$form.costunits}&nbsp;</td>
				<td><input type="text" name="trial_cost_2" value="{$edit.trial_cost_2}" style="width:50px">&nbsp;{$form.costunits_2}&nbsp;</td>
				<td><input type="checkbox" name="status" value="1" {if $edit.status}checked="checked"{/if} style="vertical-align:middle;">{$lang.pays.status}</td>
			</tr>
		</table>
	{/if}
</form>
{/strip}
{include file="$admingentemplates/admin_bottom.tpl"}