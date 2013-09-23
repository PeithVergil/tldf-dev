{include file="$gentemplates/index_top.tpl"}
<td>
	<!-- begin main cell -->
	<div class="header">{$lang.answers.ask_your_question}</div>
	<div class="sep"></div>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr><td>{include file="$gentemplates/answ_menu.tpl"}</td></tr>
	{if $err}
    	<tr>
        	<td><div class="error_msg">{$err}</div></td>
        </tr>	
	{/if}
	<tr>
		<td>
			<form name="add_question" action="{$file_name}" method="post">
			{$form.hiddens}
			<table cellpadding="0" cellspacing="0" width="100%" class="qa_form">
			<tr>
				<th>{$lang.answers.your_question}:</th>
			</tr>
			<tr>
				<td><textarea name="question" style="width:250px; height:100px;">{$data.question}</textarea></td>
			</tr>
			<tr>
				<th>{$lang.answers.details}:</th>
			</tr>
			<tr>
				<td><textarea name="details" style="width:250px; height:100px;">{$data.details}</textarea></td>
			</tr>
			<tr>
				<th>{$lang.answers.categories}:</th>
			</tr>
			<tr>
				<td>
					<select name="categs" size="10" onchange="javascript: getSubCats(0, this.value); return false;" style="width:250px;">
						{foreach name=category from=$categories item=item}
						<option value="{$item.id}" {if $data.cats == $item.id}selected{/if}>{$item.name}</option>
						{/foreach}
					</select>
					<div id="cats" style="display:inline">
					{if $categories1}
						<select name="cats1" size="10" style="width:250px;">
							{foreach name=category1 from=$categories1 item=item}
							<option value="{$item.id}" {if $data.cats1 == $item.id}selected{/if}>{$item.name}</option>
							{/foreach}
						</select>
					{/if}
					</div>
				</td>
			</tr>
			<tr><td class="sep"></td></tr>
			<tr>
				<td>
					<input type="button" value="{$lang.answers.add_q}" onclick="addQ(document.add_question);" />
				</td>
			</tr>
			</table>
			</form>
		</td>
	</tr>
	</table>
	<!-- end main cell -->
</td>
<script type="text/javascript">
file_name = '{$file_name}';
tmp_text = '{$lang.ajax.tmp_text}';
{literal}
function getSubCats(parent_level, id_parent){
	str='sel=get_cats&parent_level='+parent_level+'&id_parent='+id_parent;
	destination_odj = document.getElementById('cats');
	anisochronous = true;
	ajaxRequest(file_name, str, destination_odj, tmp_text, anisochronous);
}

function addQ(form){
	if (form.question.value == ''){
		alert('{/literal}{$lang.answers.empty_q_field}{literal}');
		return false;
	}
	/*if (form.details.value == ''){
		alert('{/literal}{$lang.answers.empty_detailes_field}{literal}');
		return false;
	}*/
	if (form.categs.value == ''){
		alert('{/literal}{$lang.answers.choose_cat}{literal}');
		return false;
	}
	if (form.cats1.value == ''){
		alert('{/literal}{$lang.answers.choose_subcat}{literal}');
		return false;
	}
	
	form.submit();
}

{/literal}
</script>
{include file="$gentemplates/index_bottom.tpl"}