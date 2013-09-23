{include file="$gentemplates/index_top.tpl"}
<td>
	<!-- begin main cell -->
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr valign=top><td valign=top>
			<div class="header">{$lang.answers.menu.search}</div>
			<div class="sep"></div>
	</td></tr>
	<tr><td>{include file="$gentemplates/answ_menu.tpl"}</td></tr>
	<tr><td height="10"></td></tr>
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" width="100%" class="qa_form">
			<tr><th>{$lang.answers.keyword} :</th></tr>
			<tr>
				<td><input id="keyword" type="text" onkeypress="if(event.keyCode == 13) startNewSearch();" /></td>
			</tr>
			<tr><th>{$lang.answers.search_in} :</th></tr>
			<tr><td>
				<input id="s_in_all" type="radio" checked="checked" name="type_filter"/> {$lang.answers.s_in_qa} <input id="s_in_q" type="radio" name="type_filter" /> {$lang.answers.s_in_q}
			</td></tr>
			<tr><th>{$lang.answers.categories} :</th></tr>
			<tr>
				<td>
					<select name="categs" size="10" onchange="javascript: getSubCats(0, this.value);" style="width:250px;">
						<option value="-1">All</option>
						{foreach name=category from=$categories item=item}
						<option value="{$item.id}" {if $data.cats == $item.id}selected{/if}>{$item.name}</option>
						{/foreach}
					</select>
					<div id="cats" style="display:inline">
					</div>
				</td>
			</tr>
			<tr><td><div class="sep"></div><input type="button" value="{$lang.answers.buttons.search}" onclick="startNewSearch();" /></td></tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<div id="search_results"></div>
		</td>
	</tr>
	</table>
</td>
<div id="tmp" style="display:none;"></div>
<script type="text/javascript">
file_name = '{$file_name}';
tmp_text = '{$lang.ajax.tmp_text}';
keyword_obj = getById('keyword');
s_in_all_obj = getById('s_in_all');
s_in_q_obj = getById('s_in_q');
{literal}
function getSubCats(parent_level, id_parent){
	destination_odj = getById('cats');
	if (id_parent == -1){
		destination_odj.innerHTML = '';
		return false;
	}
	str='sel=get_cats&parent_level='+parent_level+'&id_parent='+id_parent;
	anisochronous = false;
	ajaxRequest(file_name, str, destination_odj, tmp_text, anisochronous);
	categs1_obj = getById('categs1');
	categs1_obj.selectedIndex = 0;
}

function startNewSearch(){
	q_page_num_obj = getById('q_page_num');
	if (q_page_num_obj != null){
		q_page_num_obj.value = 1;
	}
	getSearchResult();
}

function getSearchResult(){
	
	keyword_str = keyword_obj.value;
	
	if (s_in_q_obj.checked)
		filter = 'q';
	else
		filter = '';
	categs1_obj = getById('categs1');
	if (categs1_obj != null && categs1_obj.selectedIndex >= 0)
		id_parent = categs1_obj.value;
	else
		id_parent = 0;
		
	q_page_num_obj = getById('q_page_num');
	if (q_page_num_obj != null){
		page = q_page_num_obj.value;
	}else{
		page = '1';
	}
	
	str='sel=get_searched&keyword='+keyword_str+'&filter='+filter+'&id_parent='+id_parent+'&page='+page;
	destination_odj = getById('search_results');
	anisochronous = false;
	ajaxRequest(file_name, str, destination_odj, tmp_text, anisochronous);
	
	
}

function goToPage(page){
	q_page_num_obj = getById('q_page_num');
	q_page_num_obj.value = page;
	getSearchResult();
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
	if (keyword_obj.value != ''){
		str_adds +='&fs_k='+keyword_obj.value;	
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