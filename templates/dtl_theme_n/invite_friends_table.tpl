{include file="$gentemplates/index_top.tpl"}
<td class="main_cell">
	<!-- begin main cell -->
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr valign="middle"><td height="25px" colspan=2>
			<div class="header" style="margin: 0px; height: 25px;"><div style="padding: 5px 0px">{$header.header_text}</div></div>
	</td></tr>
	<tr><td colspan=2 class="text">{$header.top2_text}<br>&nbsp;</td></tr>
	{if $contacts}
	<tr><td colspan=2>
		<div style="margin: 0px">
			<form name="invite" method="post" action="{$form.action}">
			{$form.hidden}
				<table cellspacing=0 cellpadding=5>
				<tr>
					<td colspan="2" align="right"><a href="#" onclick="javascript:setCheckboxes(1)">{$header.select_all}</a></td>
					<td colspan="2" align="left"><a href="#" onclick="javascript:setCheckboxes(0)">{$header.unselect_all}</a></td>
				</tr>
				{section name=c loop=$contacts}
				{if $smarty.section.c.index is div by 2}<tr>{/if}
					<td width="20" align="left"><input type="checkbox" name="emails[{$smarty.section.c.index}]" id="emails[{$smarty.section.c.index}]" value="{$contacts[c].email}"></td>
					<td align=left class="text">{if $contacts[c].name}{$contacts[c].name}&nbsp;-&nbsp;{/if}{$contacts[c].email}</td>
				{if $smarty.section.c.index_next is div by 2 || $smarty.section.c.last}</tr>{/if}
				{/section}
				</table>
			</form>
		</div>
		<div style="margin: 0px">
			<input type="button" class="button" onclick="javascript: location.href='{$form.action}';" value="{$button.back}">
			<input type="button" class="button" onclick="javascript: document.invite.submit();" value="{$button.send}">
		</div>
	</td></tr>
	{else}
	<tr><td colspan=2 class="error_msg">{$header.no_contacts}<br>&nbsp;</td></tr>
	{/if}
	</table>
	<!-- end main cell -->

{literal}
<script language="JavaScript" type="text/javascript">
	function setCheckboxes(do_check)
	{
	    for (var i = 0; i < {/literal}{$count_contacts}{literal}; i++) {
			elts = document.invite.elements['emails['+ i +']'];
			if (typeof(elts) != 'undefined') elts.checked = do_check;
	    }
	    return true;
	}
</script>
{/literal}

</td>
{include file="$gentemplates/index_bottom.tpl"}