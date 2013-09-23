<table cellpadding="0" cellspacing="1" width="100%" class="qs_table">
{foreach name=exp from=$experts item=item}
<tr>
	<td width="2%" valign="top">{$item.place}</td>
	<td width="10%" valign="top">
		{if !$item.root_user}<a href="viewprofile.php?id={$item.id_user}"><img src="{$item.icon_url}" border="0"/></a>
		{else}<img src="{$item.icon_url}" border="0"/>{/if}
		<br />
		{if !$item.root_user}<a href="viewprofile.php?id={$item.id_user}">{$item.login}</a>{else}
		{$item.login}{/if}
		<br />
		{$item.age} {$lang.answers.ans}
	</td>
	<td valign="top" width="73%" valign="top">
		<b>{$lang.answers.best_answ_count}:</b> {$item.answers_count}<br />
		<b>{$lang.answers.best_answ_in}:</b>
		{foreach name=cats from=$item.count_answ_in_cats item=subitem}
		{$subitem.name} ({$subitem.answ_count}){if !$smarty.foreach.cats.last},{/if}
		{/foreach}
	</td>
	<td width="15%" valign="top">
		<div align="right" style="vertical-align: bottom; height:80px;"><img src="{$site_root}{$template_root}/images/expert.gif" /></div>
		<a id="view_exp_answers_link{$item.id_user}" href="#" onclick=" viewExpAnswers({$item.id_user}); return false;">{$lang.answers.view_exp_answ}</a>
	</td>
</tr>
<tr id="tr_exp_answer{$item.id_user}" style="display:none;">
	<td colspan="4" style="padding-left:50px;">
		<div id="exp_answers_area{$item.id_user}"></div>
	</td>
</tr>
{foreachelse}
<tr><td colspan="4">{$lang.answers.no_experts}</td></tr>
{/foreach}
{if $link_arr}
<tr>
	<td colspan="4">
		{foreach name=links_exp from=$link_arr item=item}
		<a href="#" onclick="goToExpPage({$item.page}); return false;" {if $item.selected} style="font-weight:bold; text-decoration:none;"{/if}>{$item.name}</a>
		{/foreach}
		<input type="hidden" id="page_num{$form.id_user}" value="{$form.page}" />
	</td>
</tr>
{/if}
</table>