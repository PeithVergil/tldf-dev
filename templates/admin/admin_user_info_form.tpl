{include file="$admingentemplates/admin_top.tpl"}
	<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.editform_info}&nbsp;{$data.username}</font><br><br><br>
            <table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
				<form action="{$form.action}" method="post" name="information" enctype="multipart/form-data">
				<input type="hidden" name=sel value="infochange">
                {$form.hiddens}
						{section name=f loop=$info}
						<tr bgcolor="#ffffff">
							<td width="50%" align="center" class="main_header_text"><b><div id="spr{$info[f].id}">{$info[f].name}</div></b><input type=hidden name="spr{$smarty.section.f.index}" value="{$info[f].id}"></td>
							<td width="50%" align="left" class="main_content_text" style="padding:3;">
							<select id="info{$smarty.section.f.index}" name="info{$smarty.section.f.index}[]"  {if $info[f].type eq 2}multiple{/if}  style="width:200">
								{if $info[f].type eq 1}<option value="">{$button.nothing}</option>{/if}
								{if $info[f].type eq 2}<option value="0" {if $info[f].sel_all}selected{/if}>{$button.all}</option>{/if}
								{html_options values=$info[f].opt_value selected=$info[f].opt_sel output=$info[f].opt_name}
							</select>
							</td>
						</tr>
						{/section}
                    </form>
            </table>
			<table><tr height="40">
			{if $data.root  ne 1}<td><input type="button" value="{$button.save}" class="button" onclick="javascript:update_information();"></td>{/if}
			<td><input type="button" value="{$button.close}" class="button" onclick="javascript: window.close();opener.focus();"></td>
			</tr></table>
			{literal}
				  <script>
					function update_information(){
						var infoform = document.information;
						var info_str = '';
						var spr_str = '';
						var table_str = '<table class=main_content_text>';
						var i = 0;
						var j = 0;
						var k = 0;
						var l= 0;
						

						for(i=0;i<infoform.length;i++){
							if(infoform[i].name.substr(0,3)== "spr"){
								spr_id = infoform[i].value;
								spr_str = spr_str + '<input type=hidden name="spr['+j+']" value="'+spr_id+'">';

								info_name = 'info'+ infoform[i].name.substr(3);
								opt_len = document.all[info_name].options.length;
								k=0; l=0;
								td_str = '';
								while(k < opt_len){
									if(document.all[info_name].options(k).selected){
										info_text= document.all[info_name].options(k).text;
										info_value= document.all[info_name].options(k).value;
										if(info_value =='') break;
										td_str = td_str + info_text + '<br>';
										info_str = info_str + '<input type=hidden name="info['+j+']['+l+']" value="'+info_value+'">';
										if(info_value == 0 || info_value =='') break;
										l++;
									}
									k++;
								}
								if(td_str.length>4) td_str = td_str.substr(0, td_str.length-4);	// delete <br>
								if(td_str !='')  table_str = table_str + '<tr><td><b>'+document.all["spr"+spr_id].innerText+':</b> </td><td>'+td_str+'</td></tr>';
								
								document.all[info_name].name = 'info['+j+'][]';
								infoform[i].name='spr['+j+']';
								j++;
							}
						}
						table_str = table_str + '</table>';
						//alert(table_str + info_str+spr_str);
						opener.document.all['info_div'].innerHTML = table_str + info_str+spr_str;
						infoform.submit();
					}
				  </script>
				  {/literal}
        </div>
{include file="$admingentemplates/admin_bottom.tpl"}