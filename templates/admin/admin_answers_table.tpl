 {include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$lang.answers.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$lang.answers.categories}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.a}</div>
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td>
		<!-- categ manag -->
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td valign="top">
				{include file="$admingentemplates/admin_answers_cats_pattern.tpl"}
			</td>
			<td valign="top" style="padding-left:30px;">
				<div id="cats1" ></div>
			</td>
			<td valign="top" width="100%" style="padding-left:30px;">
				<div id="question_field" style="display:none;">
					<div>
						<b>{$lang.answers.your_question}:</b>
						<textarea id="question" style="width:100%; height:50px;"></textarea>
					</div>
					<div style="height:10px;"></div>
					<div>
						<b>{$lang.answers.details}:</b>
						<textarea id="details" style="width:100%; height:65px;"></textarea>
					</div>
					<div style="height:10px;"></div>
					<div>
						<input type="button" value="{$lang.answers.add_q}" onclick="addQuestion();">
					</div>
				</div>
			</td>
		</tr>
		</table>
		<!-- /categ manag -->
	</td>
</tr>
<tr>
	<td style="padding:10px ;">
		<a id="all_opened_link" href="#" onclick="viewAll('opened'); return false;">{$lang.answers.all_opened}</a><font style="padding-right:30px;">&nbsp;</font>
		<a id="all_closed_link" href="#" onclick="viewAll('closed'); return false;">{$lang.answers.all_closed}</a><font style="padding-right:30px;">&nbsp;</font>
		<a id="all_yours_link" href="#" onclick="viewAll('yours'); return false;">{$lang.answers.all_yours}</a>	
		<input type="hidden" id="all_opened_flag"/>
		<input type="hidden" id="all_closed_flag"/>
		<input type="hidden" id="all_yours_flag"/>
	</td>
</tr>
<tr><td class="sep"></td></tr>
<tr>
	<td>
		<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td valign="middle">
				<img id="question_arrow" src="{$site_root}{$template_root}/images/collapse.gif" alt="" vspace="0" hspace="0" align="middle" onclick="switchQuestions(); return false">
				<input type="hidden" id="question_flag" value="1" />
			</td>
			<td valign="middle" style="padding-left: 5px;">
				<a href="#" onclick="switchQuestions(); return false">{$lang.answers.questions}</a>
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr><td class="sep"></td></tr>
<tr><td><div id="questions"></div></td></tr>
</table>
<div id="tmp" style="display:none"></div>
<script type="text/javascript">
file_name = '{$file_name}';
tmp_text = '{$lang.ajax.tmp_text}';
max_level = {$max_level};
up_img_src = '{$site_root}{$template_root}/images/expand.gif';
down_img_src = '{$site_root}{$template_root}/images/collapse.gif';
{literal}

question_field_obj = getById('question_field');
question_obj = getById('question');
question_flag_obj = getById('question_flag');

details_obj = getById('details');

all_opened_link_obj = getById('all_opened_link');
all_closed_link_obj = getById('all_closed_link');
all_yours_link_obj = getById('all_yours_link');

all_opened_flag_obj = getById('all_opened_flag');
all_closed_flag_obj = getById('all_closed_flag');
all_yours_flag_obj = getById('all_yours_flag');

function initButtons(level){
	select_obj = getById('cat'+level);
	
	add_text_obj = getById('add_text'+level);
	add_button_obj = getById('add_button'+level);
	save_added_button_obj = getById('save_added_button'+level);
	edit_text_obj = getById('edit_text'+level);
	edit_button_obj = getById('edit_button'+level);
	save_edited_button_obj = getById('save_edited_button'+level);
	del_button_obj = getById('del_button'+level);
	
	all_opened_flag_obj.value = '0';
	all_closed_flag_obj.value = '0';
	all_yours_flag_obj.value = '0';
	
	add_text_obj.style.display = 'none';
	add_button_obj.style.display = '';
	save_added_button_obj.style.display = 'none';
	
	if (edit_button_obj.style.display == 'none' && save_edited_button_obj.style.display == 'none' && select_obj.length > 0)
		edit_button_obj.style.display= '';
	//save_edited_button_obj.style.display = 'none';\
	if (select_obj.length > 0){
		del_button_obj.style.display='';
		edit_text_obj.value = select_obj.options[select_obj.selectedIndex].innerHTML;
		id_parent = select_obj.options[select_obj.selectedIndex].value;
	}else{
		edit_text_obj.style.display = 'none';
		edit_button_obj.style.display = 'none';
		save_edited_button_obj.style.display = 'none';
		del_button_obj.style.display = 'none';
		id_parent = 0;
	}
	
	
	next_level = level+1;
	
	str = 'sel=cats_request&id_parent='+id_parent+'&level='+next_level;
	destination_odj = getById('cats'+next_level);
	anisochronous = true;
	if (destination_odj){
		cropAddQForm();
		ajaxRequest(file_name, str, destination_odj, tmp_text, anisochronous);
	}else{
		showAddQForm(level);
		change_filter = 1;
		refreshQuestions(change_filter);
	}
}

function showField(act,level){
	select_obj = getById('cat'+level);
	add_button_obj = getById('add_button'+level);
	add_text_obj = getById('add_text'+level);
	save_added_button_obj = getById('save_added_button'+level);	
	edit_text_obj = getById('edit_text'+level);
	edit_button_obj = getById('edit_button'+level);
	save_edited_button_obj = getById('save_edited_button'+level);
	switch (act){
		case 'add':		
			add_button_obj.style.display = 'none';
			add_text_obj.style.display = '';
			add_text_obj.focus();
			save_added_button_obj.style.display = '';
			if (select_obj.selectedIndex > -1){
				edit_text_obj.style.display = 'none';
				edit_button_obj.style.display = '';
				save_edited_button_obj.style.display = 'none';
			}
		break;
		case 'edit':
			add_button_obj.style.display = '';
			add_text_obj.style.display = 'none';
			save_added_button_obj.style.display = 'none';
			
			edit_text_obj.style.display = '';
			edit_button_obj.style.display = 'none';
			save_edited_button_obj.style.display = '';
		break;
	}
}

function addCat(level){
	add_text_obj = getById('add_text'+level);
	value = add_text_obj.value;
	if (value == ''){
		alert('{/literal}{$lang.answers.empty_cat_name}{literal}');
		add_text_obj.focus();
		return false;
	}
	
	if (level > 0){
		parent_level = level-1;
		select_obj = getById('cat'+parent_level);
		id_parent = select_obj.options[select_obj.selectedIndex].value;
	}else
		id_parent = 0;
	str = 'sel=add_cat&id_parent='+id_parent+'&value='+value;
	destination_odj = getById('tmp');
	anisochronous = false;
	ajaxRequest(file_name, str, destination_odj, '', anisochronous);

	tmp_msg_obj = getById('tmp_msg');
	tmp_code_obj = getById('tmp_code');
	noticeCode(tmp_msg_obj.innerHTML);
	
	code = tmp_code_obj.innerHTML;
	
	if (parseInt(code) > 0){
		add_select_obj = getById('cat'+level);
		addCatToSelect(add_text_obj, add_select_obj, code, level);
		add_text_obj.value='';
		initButtons(level);
	}else{
		add_text_obj.focus();
	}
}

function addCatToSelect(add_text_obj, add_select_obj, code, level){

	text = add_text_obj.value;
	value = code;
	defaultSelected = false;
	selected = true
	
	opt_obj = new Option(text, value, defaultSelected, selected);
	opt_obj.setAttribute('onclick', 'javascript: initButtons('+level+');');
	try{
		add_select_obj.add(opt_obj,null); // standards compliant
	}catch(ex){
		add_select_obj.add(opt_obj); // IE only
	}
	//add_select_obj.options[add_select_obj.selectedIndex].setAttribute('onclick', 'javascript: initButtons('+level+');');
}
function saveCat(level){
	edit_text_obj = getById('edit_text'+level);
	edit_select_obj = getById('cat'+level);
	
	id = edit_select_obj.options[edit_select_obj.selectedIndex].value;
	value = edit_text_obj.value;
	if (value == ''){
		alert('{/literal}{$lang.answers.empty_cat_name}{literal}');
		edit_text_obj.focus();
		return false;
	}
	str = 'sel=edit_cat&id='+id+'&value='+value;
	destination_odj = getById('tmp');
	anisochronous = false;
	ajaxRequest(file_name, str, destination_odj, tmp_text, anisochronous);

	tmp_msg_obj = getById('tmp_msg');
	tmp_code_obj = getById('tmp_code');
	noticeCode(tmp_msg_obj.innerHTML);
	
	code = tmp_code_obj.innerHTML;
	
	if (parseInt(code) > 0){
		updateCatToSelect(edit_text_obj, edit_select_obj);
		initButtons(level);
	}else{
		edit_text_obj.focus();
	}
}

function updateCatToSelect(edit_text_obj, edit_select_obj){
	edit_select_obj.options[edit_select_obj.selectedIndex].innerHTML = edit_text_obj.value;
}

function delCat(level){
	select_obj = getById('cat'+level);
	
	id = select_obj.options[select_obj.selectedIndex].value;
	
	str = 'sel=del_cat&id='+id;
	destination_odj = getById('tmp');
	anisochronous = false;
	ajaxRequest(file_name, str, destination_odj, tmp_text, anisochronous);
	
	tmp_msg_obj = getById('tmp_msg');
	tmp_code_obj = getById('tmp_code');
	
	noticeCode(tmp_msg_obj.innerHTML);
	
	code = tmp_code_obj.innerHTML;
	
	if (parseInt(code) > 0){
		index = select_obj.selectedIndex;
		deleteCatToSelect(select_obj, index)
		initButtons(level);
	}else{
		edit_text_obj.focus();
	}
}

function deleteCatToSelect(select_obj, index){
	select_obj.remove(index);
	if (select_obj.length > 0)
		select_obj.selectedIndex = 0;		
}
////questions
function showAddQForm(level){
	question_field_obj.style.display = '';
	question_obj.focus();
}

function cropAddQForm(){
	question_field_obj.style.display = 'none';
}

function addQuestion(){
	select_obj = getById('cat'+max_level);
	if (select_obj == 'undefined'){
		alert('system error!!!');
		return false;
	}
	id_parent = select_obj.options[select_obj.selectedIndex].value;
	str = 'sel=add_q&id_parent='+id_parent+'&value='+question_obj.value+'&details='+details_obj.value;

	destination_odj = getById('tmp');
	anisochronous = false;
	ajaxRequest(file_name, str, destination_odj, tmp_text, anisochronous);
	
	tmp_msg_obj = getById('tmp_msg');
	tmp_code_obj = getById('tmp_code');
	noticeCode(tmp_msg_obj.innerHTML);	
	code = tmp_code_obj.innerHTML;
	if (parseInt(code) > 0 ){
		question_obj.value = '';
		details_obj.value = '';
		all_opened_flag_obj.value = '0';
		all_closed_flag_obj.value = '0';
		all_yours_flag_obj.value = '0';
		change_filter = 0;
		refreshQuestions(change_filter);
		question_obj.focus();
	}else{
		question_obj.focus();
		return false;
	}
}

function switchQuestions(){
	question_arrow_obj = getById('question_arrow');
	questions_obj = getById('questions');
	
	if (question_flag_obj.value == '1'){
		question_arrow_obj.src = up_img_src;
		questions_obj.style.display = 'none';
		question_flag_obj.value = '0'
	}else{
		question_arrow_obj.src = down_img_src;
		questions_obj.style.display = '';
		question_flag_obj.value = '1';
		change_filter = 0;
		refreshQuestions(change_filter);
	}
}

function refreshQuestions(change_filter){
	if (question_flag_obj.value != '1') return false;
	str_adds='';
	q_page_obj = getById('q_page_num');
	
	opened_obj = getById('opened');
	closed_obj = getById('closed');
	yours_obj = getById('yours');
	
	select_obj = getById('cat'+max_level);
	if (select_obj != null && select_obj.selectedIndex > -1)
		id_parent = select_obj.options[select_obj.selectedIndex].value;
		
	if (change_filter == 1){
		str_adds += '&change_filter=1';	
	}
	
	if (all_opened_flag_obj.value == '1' || all_closed_flag_obj.value == '1' || all_yours_flag_obj.value == '1'){
		yours_obj = null;
		opened_obj = null;
		closed_obj = null;
		id_parent = null;
		if (all_opened_flag_obj.value == '1') str_adds += '&view_all=opened';
		if (all_closed_flag_obj.value == '1') str_adds += '&view_all=closed';
		if (all_yours_flag_obj.value == '1') str_adds += '&view_all=yours';
	}else{
		all_opened_link_obj.className = '';
		all_closed_link_obj.className = '';
		all_yours_link_obj.className = '';
	}
	
	if (q_page_obj !=null){
		str_adds +='&page='+q_page_obj.value;
	}
	
	if (id_parent != null){
		str_adds += '&id_parent='+id_parent	
	}
	
	if (opened_obj != null){
		if (opened_obj.checked==true)
			str_adds +='&open_sort=1';
		else
			str_adds +='&open_sort=0';
	}
	if (closed_obj != null){
		if (closed_obj.checked==true)
			str_adds +='&closed_sort=1';
		else
			str_adds +='&closed_sort=0';
	}
	if (yours_obj != null){
		if (yours_obj.checked==true)
			str_adds +='&yours_sort=1';
		else
			str_adds +='&yours_sort=0';
	}
	
	str = 'sel=get_questions'+str_adds;

	destination_odj = getById('questions');
	anisochronous = true;
	ajaxRequest(file_name, str, destination_odj, tmp_text, anisochronous);
}

function goToPage(num){
	q_page_obj = getById('q_page_num');
	q_page_obj.value = num;
	change_filter = 0;
	refreshQuestions(change_filter);
}

function viewAll(flag){
	question_field_obj.style.display = 'none';
	
	all_opened_flag_obj.value = '0';
	all_closed_flag_obj.value = '0';
	all_yours_flag_obj.value = '0';
	
	all_opened_link_obj.className = '';
	all_closed_link_obj.className = '';
	all_yours_link_obj.className = '';
	
	question_arrow_obj = getById('question_arrow');
	questions_obj = getById('questions');
	question_arrow_obj.src = down_img_src;
	questions_obj.style.display = '';
	question_flag_obj.value = '1'
	
	switch(flag){
		case 'opened': 
			all_opened_flag_obj.value = '1'; 
			all_opened_link_obj.className = 'alloc_link';
		break;
		case 'closed': 
			all_closed_flag_obj.value = '1'; 
			all_closed_link_obj.className = 'alloc_link';
		break;
		case 'yours' : 
			all_yours_flag_obj.value = '1'; 
			all_yours_link_obj.className = 'alloc_link';
		break;
	}
	i=0;
	while(true){
		try{
			select_obj = getById('cat'+i);
			if (select_obj != null)
				select_obj.selectedIndex=-1;
			else
				break;
		}catch(ex){
			break;
		}
		i++;
	}
	change_filter = 1;
	refreshQuestions(change_filter);
} 

function editQ(id){
	inform_obj = getById('inform'+id);
	quest_obj = getById('quest'+id);
	details_obj = getById('details'+id);
	edit_area_obj = getById('edit_area'+id);
	text_q_obj = getById('text_q'+id);
	text_details_obj = getById('text_details'+id);
	
	text_q_obj.innerHTML = quest_obj.innerHTML;
	text_details_obj.innerHTML = details_obj.innerHTML;
	
	inform_obj.style.display = 'none';
	edit_area_obj.style.display = '';
}

function saveQ(id){
	inform_obj = getById('inform'+id);
	quest_obj = getById('quest'+id);
	details_obj = getById('details'+id);
	edit_area_obj = getById('edit_area'+id);
	
	text_q_obj = getById('text_q'+id);
	text_details_obj = getById('text_details'+id);
	
	str = 'sel=saveQ&id='+id+'&value='+text_q_obj.value+'&details='+text_details_obj.value;
	
	destination_odj = getById('tmp');
	anisochronous = false;
	ajaxRequest(file_name, str, destination_odj, tmp_text, anisochronous);
	
	tmp_msg_obj = getById('tmp_msg');
	tmp_code_obj = getById('tmp_code');
	noticeCode(tmp_msg_obj.innerHTML);
	
	code = tmp_code_obj.innerHTML;
	if (parseInt(code) > 0){
		edit_area_obj.style.display = 'none';
		quest_obj.innerHTML = text_q_obj.value;
		details_obj.innerHTML = text_details_obj.value;
		inform_obj.style.display = '';
	}else{
		text_q_obj.focus();
	}
}

function delQ(id){
	text_q_obj = getById('text_q'+id);
	
	str = 'sel=delQ&id='+id;
	destination_odj = getById('tmp');
	anisochronous = false;
	ajaxRequest(file_name, str, destination_odj, tmp_text, anisochronous);
	
	tmp_msg_obj = getById('tmp_msg');
	tmp_code_obj = getById('tmp_code');
	noticeCode(tmp_msg_obj.innerHTML);
	
	code = tmp_code_obj.innerHTML;
	if (parseInt(code) > 0){
		//tr_q_obj = getById('tr_q'+id);
		//tr_q_obj.style.display = 'none';
		change_filter = 0;
		refreshQuestions(change_filter);
	}else{
		text_q_obj.focus();
	}
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

function cropAnswers(id){
	answers_area_obj = getById('answers_area'+id);
	add_answer_area_obj = getById('add_answer_area'+id);
	tr_answer_obj = getById('tr_answer'+id);
	
	answers_area_obj.style.display = 'none';
	if(add_answer_area_obj.style.display == 'none'){
			tr_answer_obj.style.display = 'none';
	}
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

function delAnswer(id, id_parent){
	
	str = 'sel=del_answer&id='+id+'&id_parent='+id_parent;
	destination_odj = getById('tmp');
	anisochronous = false;
	ajaxRequest(file_name, str, destination_odj, tmp_text, anisochronous);
	refreshAnswers(id_parent);
}

function makeBest(id, id_parent){
	
	str = 'sel=make_best&id='+id+'&id_parent='+id_parent;
	destination_odj = getById('tmp');
	anisochronous = false;
	ajaxRequest(file_name, str, destination_odj, tmp_text, anisochronous);
	goToAnswerPage('1', id_parent);
}

function goToAnswerPage(num,id){
	a_page_obj = getById('answer_page_num'+id);
	a_page_obj.value = num;
	refreshAnswers(id);
}

function getById(id){
	return document.getElementById(id);
}
</script>
{/literal}
{include file="$admingentemplates/admin_bottom.tpl"}