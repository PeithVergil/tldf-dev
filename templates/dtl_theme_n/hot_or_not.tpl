{include file="$gentemplates/index_top.tpl"}
<td>
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	{if $err}
    	<tr>
        	<td colspan="2">
            	<div class="error_msg">{$err}</div>
            </td>
        </tr>
    {/if}
	{if $user_info}
	<tr id="marks">
		<td colspan="2" align="center">
			<table border="0" cellspacing="0" cellpadding="0">
			<tr align="center">
			<td style="font-family: arial black; font-size:16px; font-weight:bold; color:#000066; padding-right:5px;">{$lang.hotornot.not}</td>
			{foreach from=$vote_arr item=item}
			<td>
				<a onclick="VoteUser('{$item}');" href="#">
				<img id="mark{$item}" name="mark{$item}" src="{$site_root}{$template_root}/images/vote_icon_0.gif" onmouseover="marks({$item},'show');" onmouseout="marks({$item},'hide');" border="0">
				</a>
			</td>
			{/foreach}
			<td style="font-family: arial black;font-size:16px; font-weight:bold; color:#FF0000; padding-left:5px;">{$lang.hotornot.hot}</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr><td height="20"></td></tr>
	<tr>
		<td width="70%" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td id="user_area">
					<div id="user_td">
						{include file="$gentemplates/hot_or_not_userinfo.tpl"}
					</div>
				</td>
			</tr>			
			</table>
		</td>
		<td width="1%"></td>
		<td width="29%" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td id="preview_td" height="100" width="100%" valign="top">
					{include file="$gentemplates/hot_or_not_preview.tpl"}
				</td>
			</tr>
			<tr>
				<td id="postview_td" valign="middle" width="100%">
				</td>
			</tr>
			<tr>
				<td id="tops">
					{include file="$gentemplates/hot_or_not_tops.tpl"}
				</td>
			</tr>
			<tr>
				<td id="user_stats">
					{include file="$gentemplates/hot_or_not_stats.tpl"}
				</td>
			</tr>
			<tr style="display:none;">
				<td id="info_td"></td>
			</tr>
			</table>
		</td>
	</tr>
	{/if}
	</table>
</td>
{if $user_info}
<div id="tmp"></div>
<script type="text/javascript">
{literal}
//initial
user_td_obj = document.getElementById('user_td');
show_photos_str_obj = document.getElementById('show_photos_str');
all_photos_obj = document.getElementById('all_photos');
firs_photo_path_obj = document.getElementById('firs_photo_path');
tops_obj = document.getElementById('tops');
user_stats_obj = document.getElementById('user_stats');

icon_url_obj = document.getElementById('icon_url');
main_image_obj = document.getElementById('main_image');
img_width = '{/literal}{$user_info.width}{literal}';

user_area_obj = document.getElementById('user_area');
////

this.preview_arr = new Array();
{/literal}
{foreach from=$preview item=item name=preview}
preview_obj_{$item.array_index} = document.getElementById('preview_{$item.array_index}');
this.preview_arr[{$item.array_index}] = new Array();
this.preview_arr[{$item.array_index}]['id'] = {$item.id};
this.preview_arr[{$item.array_index}]['upload_url'] = "{$item.upload_url}";
count_preview = {$item.array_index};
{/foreach}
{literal}



function checkMainImageWidth(img_width){
	if (img_width > user_area_obj.offsetWidth) img_width = user_td_obj.offsetWidth;
	return img_width;
}

if (document.images)
{
	var mark_show=new Image;
	mark_show.src="{/literal}{$site_root}{$template_root}/images/vote_icon_1.gif{literal}"
	var mark_hide=new Image;
	mark_hide.src="{/literal}{$site_root}{$template_root}/images/vote_icon_0.gif{literal}"
}

function marks(id,type)
{
	if (!document.images) return false;
	for (i=1; i<=id; i++){
		if (type=="show") document.images["mark"+i].src=mark_show.src;
		else if (type=="hide") document.images["mark"+i].src=mark_hide.src;
	}
	return;
}

function changeView(array_index){
	
	if (!getMainPart(array_index)) return;
	
	this.preview_arr.reverse();
	last_id = this.preview_arr[0]['id'];
	this.preview_arr.reverse();
	
	from_id = last_id;
	getAdditionPhotos(array_index, from_id);
	
	fillPreviewArray();
	
	deleteOldElements(array_index);
	
	assignArrayToPreview();
	
	getTops();
	
	getUserStats();
	
	return true;
}

function getTops(){
	str = 'sel=return_tops';
	asinch = false;
	ajaxRequest('hot_or_not.php', str, tops_obj, '{/literal}{$lang.ajax.tmp_text}{literal}', asinch);
	//Nifty("div.content_alloc");
}

function getUserStats(){
	str = 'sel=return_stats';
	asinch = false;
	ajaxRequest('hot_or_not.php', str, user_stats_obj, '{/literal}{$lang.ajax.tmp_text}{literal}', asinch);
	//Nifty("div.content");
}

function getMainPart(array_index){
	if (this.preview_arr.length == 0){
		document.getElementById('marks').innerHTML = '';
		document.getElementById('preview_div').innerHTML = '';
		user_td_obj.innerHTML='';
		alert('{/literal}{$lang.hotornot.end_reached}{literal}');
		location.href='quick_search.php?sel=search_top';
		return false
	}
	id = this.preview_arr[array_index]["id"];
	str = 'sel=return_main_part&id_user='+id;
	asinch = false;
	user_td_obj = document.getElementById('user_td');
	ajaxRequest('hot_or_not.php', str, user_td_obj, '{/literal}{$lang.ajax.tmp_text}{literal}', asinch);
	//Nifty("div.content");
	init();
	return true;
}

function getAdditionPhotos(array_index, from_id){
	addition_preview_data_obj = document.getElementById('addition_preview_data');
	count = array_index+1;
	str = 'sel=get_addition_preview&from_id='+from_id+'&count='+count;
	asinch = false;
	ajaxRequest('hot_or_not.php', str, addition_preview_data_obj, '', asinch);
	
}

function fillPreviewArray(){
	this.count_added = parseInt(document.getElementById('count_added').value);
	y=0;
	start = count_preview+1;
	end = count_preview+this.count_added;
	for (i=start; i<=end; i++){
		this.preview_arr[i] = new Array();
		this.preview_arr[i]['id'] = parseInt(document.getElementById('addition_preview_id_'+y).value);
		this.preview_arr[i]['upload_url'] = document.getElementById('addition_preview_upload_url_'+y).value;
		y++;
	}
}

function deleteOldElements(array_index){
	for(i=0;i<=array_index;i++){
		this.preview_arr.shift();
    }
	count_listed = 0;
	for(i=0;i<=count_preview;i++){
		if (document.getElementById('preview_div_'+i).style.display == 'inline') count_listed++;
	}
	for(i=0;i<array_index+1-this.count_added;i++){
		div_index = count_listed-i-1;
		document.getElementById('preview_div_'+div_index).style.display='none';
	}
}

function assignArrayToPreview(){
	i=0;
	for(el in this.preview_arr){
		document.getElementById('preview_'+i).src = this.preview_arr[i]["upload_url"];
		i++;
    }
}

function VoteUser(estim){
	postview_td_obj = document.getElementById('postview_td');
	id = parseInt(document.getElementById('id_user').value);
	asinch = false;
	str = 'sel=rate&id_user='+id+'&estim='+estim;
	ajaxRequest('hot_or_not.php', str, postview_td_obj, '{/literal}{$lang.ajax.tmp_text}{literal}', asinch);
	//Nifty("div.content");
	changeView(0);
	/*if (this.preview_arr.length == 0){
		document.getElementById('marks').innerHTML = '';
		document.getElementById('preview_div').innerHTML = '';
		user_td_obj.innerHTML='';
		alert('{/literal}{$lang.hotornot.end_reached}{literal}');
		//return false
	}*/
}


function showPhotosBar(){
	show_photos_str_obj.style.display = 'none';
	all_photos_obj.style.display = '';
	showPhoto(firs_photo_path_obj.value, 'small_photo_0');
}

function showPhoto(path, id){
	small_photo_object = document.getElementById(id);
	photo_count = document.getElementById('photo_count_id').value;
	small_icon_object = document.getElementById('small_icon');
	small_icon_object.style.border='';
	for(i=0; i<photo_count; i++)document.getElementById('small_photo_'+i).style.border='';
	small_photo_object.style.border = '3px solid #000000';
	main_image_obj.src = path;
}


//////////////////////////////////
function init(){
	show_photos_str_obj = document.getElementById('show_photos_str');
	all_photos_obj = document.getElementById('all_photos');
	firs_photo_path_obj = document.getElementById('firs_photo_path');
	icon_url_obj = document.getElementById('icon_url');
	main_image_obj = document.getElementById('main_image');
	main_image_td_obj = document.getElementById('main_image_td');
	
	main_image_obj.width = checkMainImageWidth(img_width);
	
	main_image_obj.src = document.getElementById('img_url').value;
	main_image_td_obj.style.height = main_image_obj.height;
}
/* wait until window loaded */
/* for Mozilla */
if (document.addEventListener) {
	document.addEventListener("DOMContentLoaded", init, false);
}

/* for Internet Explorer */
/*@cc_on @*/
/*@if (@_win32)
	document.write("<script id=__ie_onload defer src=javascript:void(0)><\/script>");
	var script = document.getElementById("__ie_onload");
	script.onreadystatechange = function() {
		if (this.readyState == "complete") {
			init(); // call the onload handler
		}
	};
/*@end @*/

/* for Safari */
if (/WebKit/i.test(navigator.userAgent)) { // sniff
	var _timer = setInterval(function() {
		if (/loaded|complete/.test(document.readyState)) {
			init(); // call the onload handler
		}
	}, 10);
}

/* for other browsers */

window.onload = init;

setInterval(function(){ajaxRequest('hot_or_not.php', 'qwe=1', '', '', true);},1000*60*45); //session not die;
{/literal}
</script>
{/if}
{include file="$gentemplates/index_bottom.tpl"}