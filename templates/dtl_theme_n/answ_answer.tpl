{include file="$gentemplates/index_top.tpl"}
<td>
	<!-- begin main cell -->
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr valign=top><td valign=top>
			<div class="header">{$lang.answers.menu.answer}</div>
			<div class="sep"></div>
	</td></tr>
	<tr><td>{include file="$gentemplates/answ_menu.tpl"}</td></tr>
	<tr><td height="10"></td></tr>
	{if $err}
    	<tr><td><div class="error_msg">{$err}</div></td></tr>
        <tr><td height="10"></td></tr>
	{/if}
	<tr>
		<td>
			<a href='{$file_name}?sel=answer'>{$lang.answers.menu.all_qs}</a>
		</td>
	</tr>
	<tr><td height="10"></td></tr>
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0">
			<tr>
				<td><input type="radio" id="opened_qs" name="filter" {if $form.opened == '1'}checked="checked" {/if} onclick="q_page_num_obj = getById('q_page_num');	q_page_num_obj.value = '1'; refreshQuestions();" />&nbsp;{$lang.answers.opened_qs}</td>
				<td><input type="radio" id="closed_qs" name="filter" {if $form.closed == '1'}checked="checked" {/if} onclick="q_page_num_obj = getById('q_page_num');	q_page_num_obj.value = '1'; refreshQuestions();" />&nbsp;{$lang.answers.closed_qs}</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr><td height="10"></td></tr>
	<tr><td height="20">
			<b>{$lang.answers.categories}:</b><br />
	</td></tr>
	<tr>
		<td>
			<select name="cats" size="10" onchange="javascript: getSubCats(0, this.value);" style="width:250px;">
				{foreach name=category from=$categories item=item}
				<option value="{$item.id}" {if $data.cats == $item.id}selected{/if}>{$item.name}</option>
				{/foreach}
			</select>
			<div id="cats1" style="display:inline">
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<div id="questions">
				{include file="$gentemplates/answ_quest_pattern.tpl"}
			</div>
		</td>
	</tr>
	</table>
	<!-- end main cell -->
</td>
<div id='tmp' style="display:none;"></div>
<script type="text/javascript">
file_name = '{$file_name}';
tmp_text = '{$lang.ajax.tmp_text}';
{literal}

questions_obj = getById('questions');

all_qs_obj = getById('all_qs');
opened_qs_obj = getById('opened_qs');
closed_qs_obj = getById('closed_qs');

function getSubCats(parent_level, id_parent){
	str='sel=get_cats&parent_level='+parent_level+'&id_parent='+id_parent;
	destination_odj = getById('cats1');
	anisochronous = false;
	ajaxRequest(file_name, str, destination_odj, tmp_text, anisochronous);
	categs1_obj = getById('categs1');
	
	if (navigator.appName == 'Microsoft Internet Explorer'){
		categs1_obj.setAttribute("onchange", function() {refreshQuestions()});
	}else{
		categs1_obj.onchange = function(){refreshQuestions()};
	}

}

function refreshQuestions(){
	if (opened_qs_obj.checked){
		f = 'opened';	
	}
	if (closed_qs_obj.checked){
		f = 'closed';	
	}
	if (!opened_qs_obj.checked && !closed_qs_obj.checked){
		f = 'all';	
	}
	categs1_obj = getById('categs1');
	if (categs1_obj != null && categs1_obj.value){
		id_parent = categs1_obj.value;
	}else{
		id_parent='-1';	
	}
	q_page_num_obj = getById('q_page_num');
	if (q_page_num_obj != null){
		page = q_page_num_obj.value;
	}else{
		page = '1';
	}
	str = 'sel=get_q&filter='+f+'&id_parent='+id_parent+'&page='+page;
	destination_odj = questions_obj;
	anisochronous = true;
	ajaxRequest(file_name, str, destination_odj, tmp_text, anisochronous);
}

function goToPage(page){
	q_page_num_obj = getById('q_page_num');
	q_page_num_obj.value = page;
	refreshQuestions();
}


function showAnswerField(id){
	tr_answer_obj = getById('tr_answer'+id);
	add_answer_area_obj = getById('add_answer_area'+id);
	tr_answer_obj.style.display = '';
	add_answer_area_obj.style.display = '';
}

function answer(id_parent){
	text_answer_obj = getById('text_answer'+id_parent);
	
	value = text_answer_obj.value;
	str = 'sel=add_answer&id_parent='+id_parent+'&value='+value;
	destination_odj = getById('tmp');
	anisochronous = false;
	ajaxRequest(file_name, str, destination_odj, tmp_text, anisochronous);
	
	tmp_msg_obj = getById('tmp_msg');
	tmp_code_obj = getById('tmp_code');
	noticeCode(tmp_msg_obj.innerHTML);
	
	code = tmp_code_obj.innerHTML;
	if (parseInt(code) > 0){
		view_answers_link_obj = getById('view_answers_link'+id_parent);
		view_answers_link_obj.style.display='';
		text_answer_obj.value='';
		refreshAnswers(id_parent);
	}else{
		text_answer_obj.focus();
	}
}

function viewAnswers(id){
	refreshAnswers(id);
}

function refreshAnswers(id){
	tr_answer_obj = getById('tr_answer'+id);
	tr_answer_obj.style.display = '';
	answers_area_obj = getById('answers_area'+id);
	answers_area_obj.style.display = '';
	
	str_adds = '';
	a_page_obj = getById('answer_page_num'+id);
	if(a_page_obj!=null){
		str_adds +='&ans_page'+id+'='+a_page_obj.value;
	}else{
		str_adds +='&ans_page'+id+'=1';
	}
	
	str = 'sel=get_answers&id_parent='+id+str_adds;
	destination_odj = getById('answers_area'+id);
	anisochronous = true;
	ajaxRequest(file_name, str, destination_odj, tmp_text, anisochronous);
}


function goToAnswerPage(num,id){
	a_page_obj = getById('answer_page_num'+id);
	a_page_obj.value = num;
	refreshAnswers(id);
}

function makeBest(id, id_parent){
	
	str = 'sel=make_best&id='+id+'&id_parent='+id_parent;
	destination_odj = getById('tmp');
	anisochronous = false;
	ajaxRequest(file_name, str, destination_odj, tmp_text, anisochronous);
	goToAnswerPage('1', id_parent);
}

function cropAnswers(id){
	answers_area_obj = getById('answers_area'+id);
	tr_answer_obj = getById('tr_answer'+id);
	answers_area_obj.style.display = 'none';
}

function getById(id){
	return document.getElementById(id);
}

{/literal}
</script>
{include file="$gentemplates/index_bottom.tpl"}