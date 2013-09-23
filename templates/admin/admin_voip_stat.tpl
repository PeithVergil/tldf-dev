{include file="$admingentemplates/admin_top.tpl"}
	<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.statistic}</font><br /><br />
	<div class="help_text"><span class="help_title">{$lang.help}:</span>{$lang.voip.statistics_help}</div>
	<table class="simple_table" cellpadding="0" cellspacing="2">
	<tr>
		<th>{$lang.voip.login}</th>
		<th>{$lang.voip.total_time}</th>
		<th>{$lang.voip.total_cost}</th>
		<th>{$lang.voip.last_date}</th>
	</tr>
	{foreach name=stat from=$stat item=item}
	{assign var="iteration" value=$smarty.foreach.stat.iteration}
	<tr>
		<td><a id="au_{$iteration}" href="#" onClick="javascript: expandUser({$item.id_user},{$iteration}); return false;"><img id="ecu_{$iteration}" src="{$site_root}{$template_root}/images/expand.gif" border="0" alt="">&nbsp;<b>{$item.login}</b></a></td>
		<td>{$item.duration}</td>
		<td>
			{foreach name=cost from=$item.cost item=item1}
			{$item1.curr_value} {$item1.curr_name}
			{if !$smarty.foreach.cost.last}/{/if}
			{/foreach}
		</td>
		<td>{$item.last_date}</td>
	</tr>
	<tr id="tru_{$iteration}" style="display: none;"><td colspan="4" id="destu_{$iteration}" style="padding:10px;"></td></tr>
	{/foreach}
	</table>
<script type="text/javascript">
file_name = '{$file_name}';
path_to_image = '{$site_root}{$template_root}/images/';
browser = navigator.appName;
{literal}
function expandUser(id_user,iter){
	au_obj = document.getElementById("au_"+iter);
	imgu_obj = document.getElementById("ecu_"+iter);
	tru_obj = document.getElementById("tru_"+iter);
	destu_odj = document.getElementById("destu_"+iter);
	imgu_obj.src = path_to_image+'collapse.gif';
	
	if (browser=="Microsoft Internet Explorer"){
		tru_obj.style.display = 'inline';
	}else{
		tru_obj.style.display = 'table-row';
	}
	
	au_obj.onclick = function(){collapseUser(iter); return false};
	str = "sel=user_stat&id_user="+id_user+"&iter="+iter;
	
	ajaxRequest(file_name, str, destu_odj, '<img src="'+path_to_image+'loading.gif" border="0"/>', true);
}

function collapseUser(iter){
	au_obj = document.getElementById("au_"+iter);
	imgu_obj = document.getElementById("ecu_"+iter);
	tru_obj = document.getElementById("tru_"+iter);
	
	au_obj.onclick = function(){expandUserWithoutAjax(iter); return false};

	imgu_obj.src = path_to_image+'expand.gif';
	tru_obj.style.display = 'none';
}

function expandUserWithoutAjax(iter){
	tru_obj = document.getElementById("tru_"+iter);
	
	imgu_obj.src = path_to_image+'collapse.gif';
	
	if (browser=="Microsoft Internet Explorer"){
		tru_obj.style.display = 'inline';
	}else{
		tru_obj.style.display = 'table-row';
	}
	
	au_obj.onclick = function(){collapseUser(iter); return false};
}

{/literal}
</script>
{include file="$admingentemplates/admin_bottom.tpl"}