<table width="100%" border="0" cellpadding="5" cellspacing="0">
	{foreach name=n from=$users item=item key=key}
		<tr valign="middle">
			<td width="18px" align="center">
				<span class="mtext">{$smarty.foreach.n.iteration}</span>
			</td>
			<td width="80px" align="center"><img src="{$item.icon}" class="icon" alt=""></td>
			<td width="100%" class="text" valign="top">
				<div style="margin-top:7px">
					<a href="{$item.link_read}" class="text_head">{$item.login}</a>
				</div>
				<div style="margin-top:2px">
					<span class="mtext">{if $base_lang.city[$item.id_city]}{$base_lang.city[$item.id_city]}, {/if}{if $base_lang.region[$item.id_region]}{$base_lang.region[$item.id_region]}, {/if}{$base_lang.country[$item.id_country]}</span>
				</div>
				<div style="margin-top:2px">
					<span class="text_hidden">{$item.age} {$header.generic_e.ans}</span>
				</div>
				<div style="margin-top:2px">
					<span class="mtext">{$header.newuser.regdate}: {$item.date}</span>
				</div>
				<div style="margin-top:7px">
					<a href="{$item.link_read}" class="mlink">{$header.generic_e.viewprofile} &raquo;</a>
				</div>
			</td>
		</tr>
	{/foreach}
</table>