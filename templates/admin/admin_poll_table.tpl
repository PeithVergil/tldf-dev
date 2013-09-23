
{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.list}</font>
{if $poll_admin_panel eq "index"}<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.poll_index}</div>{/if}
{if $poll_admin_panel eq "poll_new"}<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.poll_new}</div>{/if}
{if $poll_admin_panel eq "settings"}<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.poll_settings}</div>{/if}
{if $poll_admin_panel eq "templates"}<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.poll_templates}</div>{/if}
{if $poll_admin_panel eq "template_new"}<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.poll_template_new}</div>{/if}

  <form method="post" action="{$pollvars.SELF}" name="poll">
  <table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
  {if $poll_admin_panel eq "index"}
   <tr class="table_header">
        <td class="main_header_text" align="center">&nbsp;</td>
        <td class="main_header_text" align="center"><b>{$lang_poll.IndexQuest}</b></td>
        <td class="main_header_text" align="center"><b>{$lang_poll.IndexID}</b></td>
        <td class="main_header_text" align="center"><b>{$lang_poll.StatCrea}</b></td>
        <td class="main_header_text" align="center"><b>{$lang_poll.IndexDays}</b></td>
        <td class="main_header_text" align="center"><b>{$lang_poll.IndexFor}</b></td>
		<td class="main_header_text" align="center"><b>{$lang_poll.IndexExp}</b></td>
        <td class="main_header_text" align="center"><b>{$lang_poll.IndexStat}</b></td>
        <td class="main_header_text" align="center"><b>{$lang_poll.IndexAct}</b></td>
   </tr>

   {foreach from=$all_polls item=one_poll}
   <tr bgcolor="#FFFFFF">
        <td class="main_content_text" align="center"><img src={$one_poll.image} width="13" height="16" alt="{$one_poll.alt}"></td>
        <td class="main_content_text"><a href="admin_poll.php?sel=edit&poll_id={$one_poll.poll_id}" title="{$lang_poll.EditText}">{$one_poll.question}</a></td>
        <td class="main_content_text" align="center">{$one_poll.poll_id}</td>
        <td class="main_content_text">{$one_poll.date}</td>
        <td class="main_content_text">{$one_poll.days}</td>
        <td class="main_content_text">
             {if $one_poll.poll_for eq "0"}
             {$lang_poll.IndexBoth}
             {elseif $one_poll.poll_for eq "1"}
             {$lang_poll.IndexGuy}
             {elseif $one_poll.poll_for eq "2"}
             {$lang_poll.IndexLady}
             {/if}
        </td>
		<td class="main_content_text">
             {if $one_poll.poll_expire eq "0"}
             {$one_poll.poll_expire_date}
             {elseif $one_poll.poll_expire eq "1"}
             {$one_poll.poll_expire_date}
             {elseif $one_poll.poll_expire eq "2"}
             {$one_poll.poll_expire_date}
             {/if}
        </td>
       <td class="main_content_text" align="center"><a href="admin_poll.php?sel=stats&poll_id={$one_poll.poll_id}"><img src="{$one_poll.image2}" width="16" height="16" border="0" alt="{$lang_poll.IndexStat}"></a>&nbsp;&nbsp;
             <a href="admin_poll.php?sel=comment&poll_id={$one_poll.poll_id}"><img src="{$one_poll.image3}" width="18" height="18" border="0" alt="{$lang_poll.IndexCom}"></a>&nbsp;&nbsp;
       </td>
       <td class="main_content_text" align="center"><a href="javascript:del_entry({$one_poll.poll_id})">{$lang_poll.IndexDel}</a></td>
   </tr>
  {/foreach} 


  {elseif $poll_admin_panel eq "poll_edit"}
  <tr bgcolor="#FFFFFF">
       <td class="main_content_text" align="center" width="20%">{$lang_poll.IndexQuest}</td>                                        
       <td class="main_content_text" width="49%">                                                                          
         <input type="text" name="question" class="input" size="100%" value="{$poll_question}">           
       </td>                                                                                     
       <td class="main_content_text" width="11%">                                                              
         <select name="status" class="select">                                                   
           <option value="0" {$status_0}>{$lang_poll.EditOff}</option>                                   
           <option value="1" {$status_1}>{$lang_poll.EditOn}</option>                                    
           <option value="2" {$status_2}>{$lang_poll.EditHide}</option>                                  
         </select>                                                                               
       </td>                                                                                     
       <td class="main_content_text" align="center" colspan="2">                                                              
         <select name="logging" class="select">                                                  
           <option value="0" {$logging_0}>{$lang_poll.EditLgOff}</option>                                
           <option value="1" {$logging_1}>{$lang_poll.EditLgOn}</option>                                 
         </select>                                                                               
       </td>                                                                                     
  </tr>
  {foreach from=$all_options item=one_option}
   <tr bgcolor="#FFFFFF">
       <td class="main_content_text" align="center">{$lang_poll.NewOption} {$one_option.option_id}</td>
       <td class="main_content_text" >
           <input type="text" name="option_id{$one_option.option_id}" size="100%" value="{$one_option.option_text}">
       </td>
       <td class="main_content_text" align="center">
           <input type="text" name="votes{$one_option.option_id}" size="10" value="{$one_option.votes}">
       </td>
       <td class="main_content_text" align="center">
           <select name="color{$one_option.option_id}" onChange="javascript:ChangeBar(options[selectedIndex].value,{$one_option.option_id})">
           <option value="blank">---</option>
           {foreach from=$one_option.opt_color item=opt_color}
           {$opt_color}
           {/foreach}
		   </select>     
       </td>
       <td class="main_content_text" align="center">
           <img src={$one_option.color_file} name="bar{$one_option.option_id}" width=35 height=12></td>
   </tr>
  {/foreach}
   <tr bgcolor="#FFFFFF">                                                                                                                                                                                                    
     <td width="20%">&nbsp;</td>                                                                                                                                                                           
     <td class="main_content_text" colspan="4" height="35" class="td2" valign="top">{$lang_poll.IndexExp}: 
      <input type="text" name="exp_time" size="3" value="{$expiration}">
      {$lang_poll.IndexDays} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {$lang.IndexNever}
      <input type="checkbox" name="expire" value="0" {$poll_expire}>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	 {$lang_poll.IndexFor}:
     <select name="poll_for" class="select">
      <option value="0" {if $poll_for eq "0"} selected {/if} >{$lang_poll.IndexBoth}</option>
      <option value="1" {if $poll_for eq "1"} selected {/if} >{$lang_poll.IndexGuy}</option>
      <option value="2" {if $poll_for eq "2"} selected {/if} >{$lang_poll.IndexLady}</option>
     </select>
	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <img src="{$pollvars.base_gif}/point2.gif" width="9" height="9">{$lang_poll.EditDel}
     </td>
   </tr>                                                                                                                                                                                                   
   <tr bgcolor="#FFFFFF">                                                                                                                                                                                                    
     <td width="20%">&nbsp;</td>                                                                                                                                                                           
     <td class="main_content_text" colspan="4" height="35" valign="top">                                                                                                                                                 
     <a href="{$pollvars.SELF}?sel=edit&action=extend&poll_id={$poll_id}">{$lang_poll.EditAdd}</a>
     &nbsp;&nbsp;&nbsp;    
     <a href="javascript:ResetPoll()">{$lang_poll.EditReset}</a>
     &nbsp;&nbsp;{$lang_poll.EditCom}&nbsp;    
     <input type="checkbox" name="comments" value="1" {$poll_comments}>
     </td>                                                                                         
   </tr>                                                                                                                                                                                                   
   <tr bgcolor="#FFFFFF">                                                                                                                                                                                                    
     <td width="20%">&nbsp;</td>                                                                                                                                                                               
     <td colspan="4">                                                                                                                                                                                          
      <table bgcolor="#FFFFFF">
      <td>
	 <input type="button" value="{$lang_poll.EditSave}" class="button" onclick="SubmitMyForm();" name="apply">
      </td>
      <td>
	  <input type="button" value="{$lang_poll.FormUndo}" class="button" onclick="ResetColors();" name="apply">
          <input type="hidden" name="sel"  value="edit">
          <input type="hidden" name="action" value="save">                                                                                                                                                        
          <input type="hidden" name="poll_id" value="{$poll_id}">                                                                                                                                                   
      </td>
      </table>
     </td>                                                                                          
   </tr>


  {elseif $poll_admin_panel eq "poll_extend"}
  <tr class="table_header">
     <td class="main_header_text"  width="10%">{$lang_poll.IndexQuest}</td>
     <td class="main_content_text" width="90%" colspan="3">{$poll_question}</td>
  </tr>
  {foreach from=$ids item=id}
  <tr bgcolor="#FFFFFF">
       <td class="main_content_text" align="center">{$lang_poll.NewOption} {$id}</td>
       <td class="main_content_text" width="45%">
           <input type="text" name="option_id{$id}" size="100%" value="">
       </td>
       <td class="main_content_text" width="15%" align="center">
           <select name="color{$id}" onChange="javascript:ChangeBar(options[selectedIndex].value,{$id})">
           <option value="blank">---</option>
           {foreach from=$opt_colors item=opt_color}
           {$opt_color}
           {/foreach} 
		   </select>    
       </td>
       <td class="main_content_text" align="left">
           &nbsp;&nbsp;<img src="{$pollvars.base_gif}/blank.gif" name="bar{$id}" width=35 height=12></td>
  </tr>
  {/foreach}
  <tr bgcolor="#FFFFFF">                                                                         
    <td width="15%">&nbsp;</td>                                                
    <td colspan="3" height="35">                                               
     <table bgcolor="#FFFFFF">
     <td>
	<input type="button" value="{$lang_poll.EditAdd}" class="button" onclick="SubmitMyForm();" name="apply">
     </td>
     <td>
	<input type="button" value="{$lang_poll.FormUndo}" class="button" onclick="ResetColors();" name="apply">
        <input type="hidden" name="sel"  value="edit">
        <input type="hidden" name="action"  value="add">                           
        <input type="hidden" name="poll_id" value="{$poll_id}">                     
     </td>
     </table>
    </td>                                                                      
  </tr>                                                                        


  {elseif $poll_admin_panel eq "poll_new"}
  <tr bgcolor="#FFFFFF">
       <td class="main_content_text" align="center" width="20%">{$lang_poll.IndexQuest}</td>                                        
       <td class="main_content_text" width="49%">                                                                          
         <input type="text" name="question" class="input" size="100%" value="{$poll_question}">           
       </td>                                                                                     
       <td class="main_content_text" align="center" colspan="2">                                                              
         <select name="logging" class="select">                                                  
           <option value="0" {$logging_0}>{$lang_poll.EditLgOff}</option>                                
           <option value="1" {$logging_1}>{$lang_poll.EditLgOn}</option>                                 
         </select>                                                                               
       </td>                                                                                     
  </tr>
  {foreach from=$ids item=id}
  <tr bgcolor="#FFFFFF">
       <td class="main_content_text" align="center">{$lang_poll.NewOption} {$id}</td>
       <td class="main_content_text" width="45%">
           <input type="text" name="option_id{$id}" size="100%" value="">
       </td>
       <td class="main_content_text" width="15%" align="center">
           <select name="color{$id}" onChange="javascript:ChangeBar(options[selectedIndex].value,{$id})">
           <option value="blank">---</option>
           {foreach from=$opt_colors item=opt_color}
           {$opt_color}
           {/foreach} 
		   </select>    
       </td>
       <td class="main_content_text" align="left">
           &nbsp;&nbsp;<img src="{$pollvars.base_gif}/blank.gif" name="bar{$id}" width=35 height=12></td>
  </tr>
  {/foreach}
  <tr  bgcolor="#FFFFFF">
    <td >&nbsp;</td>
    <td colspan="3" height="35" class="main_content_text">
     {$lang_poll.EditStat}:
     <select name="status" class="select">
      <option value="0">{$lang_poll.EditOff}</option>
      <option value="1" selected>{$lang_poll.EditOn}</option>
      <option value="2">{$lang_poll.EditHide}</option>
     </select>&nbsp;&nbsp;&nbsp;&nbsp;
     {$lang_poll.IndexExp}: <input type="text" name="exp_time" class="exp_input" size="4"> {$lang_poll.IndexDays}
     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$lang_poll.IndexNever} <input type="checkbox" name="expire" value="0" checked>
     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$lang_poll.EditCom}&nbsp;<input type="checkbox" name="comments" value="1">
	 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	 {$lang_poll.IndexFor}:
     <select name="poll_for" class="select">
      <option value="0" selected="selected">{$lang_poll.IndexBoth}</option>
      <option value="1">{$lang_poll.IndexGuy}</option>
      <option value="2">{$lang_poll.IndexLady}</option>
     </select>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
       <td width="25%">&nbsp;</td>
       <td>
       <table bgcolor="#FFFFFF">
       <td>
	   <input type="button" value="{$lang_poll.NewTitle}" class="button" onclick="SubmitMyForm();" name="apply">
       </td>
       <td>
	   <input type="button" value="{$lang_poll.FormUndo}" class="button" onclick="ResetColors();" name="apply">
           <input type="hidden" name="action"  value="create">
       </td>
       </table> 
       </td>
  </tr>



  {elseif $poll_admin_panel eq "comments"}
  <tr class="table_header">
     <td class="main_header_text"  width="20%">{$lang_poll.IndexQuest}</td>
     <td class="main_content_text" width="90%" colspan="3">{$poll_question}</td>
  </tr>
  <tr bgcolor="#FFFFFF">
     <td class="main_content_text">{$lang_poll.ComTotal}</td>
     <td class="main_content_text" colspan="3">{$total_commens}</td>
  </tr>
  {foreach from=$all_comments item=one_comment}
  <tr bgcolor="#FFFFFF">
       <td class="main_content_text">{$lang_poll.PwdUser}: {$one_comment.login}<br>
                                                  Browser: <img src="{$one_comment.browser_ico}" width="16" height="16" alt="{$one_comment.browser}"><br>
                                                     Host: {$one_comment.host}</td>
       <td class="main_content_text">
           {$one_comment.message}
       </td>
       <td class="main_content_text" align="center">
          <a href="javascript:del_entry({$one_comment.com_id})">{$lang_poll.IndexDel}</a></td>
       </td>
  </tr>
  {/foreach}


  {elseif $poll_admin_panel eq "stats"}
  <tr class="table_header">
     <td class="main_header_text"  width="10%">{$lang_poll.IndexQuest}</td>
     <td class="main_content_text" width="90%">{$poll_question}</td>
  </tr>
  <tr bgcolor="#FFFFFF">
     <td width="152" class="main_content_text">{$lang_poll.StatCrea}:</td>
     <td class="main_content_text">{$newdate}</td>
  </tr>
  <tr bgcolor="#FFFFFF">
     <td class="main_content_text">{$lang_poll.StatAct}:</td>
     <td class="main_content_text">{$days} {$lang_poll.IndexDays}, {$remain} {$lang_poll.SetHours}</td>
  </tr>
  <tr bgcolor="#FFFFFF">
     <td class="main_content_text">{$lang_poll.StatTotal}:</td>
     <td class="main_content_text">{$poll_sum_total}</td>
  </tr>
  <tr bgcolor="#FFFFFF">
     <td colspan="2" class="main_content_text">
       <a href="{$pollvars.SELF}?action=reset&poll_id={$poll_id}">{$lang_poll.StatReset}</a>
     </td>
  </tr>
  <tr class="table_header">
     <td colspan="2" class="main_header_text">{$lang_poll.IndexStat}</td>
  </tr>
  {foreach from=$all_votes item=one_vote}
  <tr bgcolor="#FFFFFF">
     <td width="40%" class="main_content_text"> 
       {$lang_poll.NewOption} {$one_vote.option_id}: {$one_vote.option_text} <br>
       {$lang_poll.SetVotes}: {$one_vote.votes} ({$one_vote.percent})
     </td>
     <td class="main_content_text">
       {$one_vote.perday} {$lang_poll.StatDay}
     </td>
  </tr>
  {if $one_vote.logging eq "1"}
    <tr bgcolor="#FFFFFF">
     <td colspan="2">
      <table border=0 class="table_main" cellspacing=0 cellpadding=5 width="100%"> 
       <tr bgcolor="#FFFFFF">
          <td width="15%" class="main_content_text"><font color="#000099">{$lang_poll.IndexDate}</font></td>
          <td width="13%" class="main_content_text"><font color="#000099">IP</font></td>
          <td width="12%" class="main_content_text"><font color="#000099">Host</font></td>
          <td width="10%" class="main_content_text"><font color="#000099">User</font></td>
          <td width="50%" class="main_content_text"><font color="#000099">Browser</font></td>
       </tr>
       {foreach from=$one_vote.log_data item=l_data}
          <tr bgcolor="#FFFFFF"> 
           <td width="15%" class="main_content_text">{$l_data.log_date}</td>
           <td width="13%" class="main_content_text">{$l_data.ip_addr}</td>
           <td width="12%" class="main_content_text">{$l_data.host}</td>
           <td width="10%" class="main_content_text">{$l_data.uname}</td>
           <td width="50%" class="main_content_text">{$l_data.agent}</td>
          </tr>
       {/foreach}
      </table>
     </td>
    </tr>
  {/if}
  {/foreach}


  {elseif $poll_admin_panel eq "settings"}
  <tr bgcolor="#FFFFFF">
     <td width="27%" class="main_content_text">{$lang_poll.SetLang}</td>
     <td width="73%" class="main_content_text"> 
         <input type="text" name="cfg_lang" class="input" value="{$pollvars.lang}" size="25">
         <select name="lang_file" class="select" onChange="document.forms[0].elements[0].value=options[selectedIndex].value">
         <option value="english.php" selected>{$lang_poll.SetLang}</option>
            {$lang_polllist}
         </select>
     </td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
     <td width="27%" class="main_content_text">{$lang_poll.SetCheck}</td>
     <td width="73%" class="main_content_text"> 
         <input type="checkbox" name="cfg_check_ip" {$check_ip}> {$lang_poll.CheckIP} &nbsp; - &nbsp;{$lang_poll.SetTime}&nbsp;
         <input type="text" class="input" name="cfg_lock_timeout" value="{$pollvars.lock_timeout}" size="4">&nbsp;{$lang_poll.SetHours}<br>
         <input type="checkbox" name="cfg_check_user_name" {$check_uname}> {$lang_poll.CheckUsername}
     </td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
     <td width="27%">&nbsp;</td>
     <td>
       <table bgcolor="#FFFFFF">
       <td>
	   <input type="button" value="{$lang_poll.SetSubmit}" class="button" onclick="SubmitMyForm();" name="apply">
       </td>
       <td>
	   <input type="button" value="{$lang_poll.FormUndo}" class="button" onclick="ResetMyForm();" name="apply">
           <input type="hidden" name="sel"     value="settings">
           <input type="hidden" name="action"  value="update">
       </td>
       </table> 
     </td>
  </tr>


  {elseif $poll_admin_panel eq "templates"}
  <tr class="table_header">
     <td class="main_header_text"  width="15%">Template: </td>
     <td class="main_content_text" width="15%">
      <select name="poll_tplset" onChange="javascript:ChangeBar(options[selectedIndex].value)">
      {$select_field}
      </select>
     </td>
     <td class="main_content_text">
         <a href="javascript:openWindow('{$pollvars.SELF}?sel=templates&tpl_act=preview&poll_tplset={$poll_tplset}&tpl_type={$tpl_type}','display','560','420','toolbar=yes,scrollbars=yes')">{$lang_poll.preview}</a>&nbsp;&nbsp;&nbsp;&nbsp;
         <a href="javascript:del_entry('{$poll_tplset}')">{$lang_poll.IndexDel}</a>&nbsp;&nbsp;&nbsp;&nbsp;
         <a href="{$pollvars.SELF}?sel=templates&tpl_act=new">{$lang_poll.newtpl}</a> 
     </td>
  </tr>

  {if $tpl_type eq "result"}
  <tr bgcolor="#FFFFFF">
     <td class="main_content_text" width="30%" colspan="3">
         <a href="{$pollvars.SELF}?sel=templates&poll_tplset={$poll_tplset}&tpl_type=display">Poll View</a>&nbsp;&nbsp;&nbsp;&nbsp;
         <a href="{$pollvars.SELF}?sel=templates&poll_tplset={$poll_tplset}&tpl_type=result"><b>Poll Result</b></a>&nbsp;&nbsp;&nbsp;&nbsp;
         <a href="{$pollvars.SELF}?sel=templates&poll_tplset={$poll_tplset}&tpl_type=comment">Comment</a>
    </td>
  </tr>

  <tr bgcolor="#FFFFFF">
     <td class="main_header_text" colspan="3">Result head</td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td colspan="3"> 
      <textarea cols="120" name="tpl_{$poll_tpl_id.result_head}" rows="12" wrap="VIRTUAL">{$poll_tpl.result_head}</textarea>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
     <td class="main_header_text" colspan="3">Result loop</td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td colspan="3"> 
      <textarea cols="120" name="tpl_{$poll_tpl_id.result_loop}" rows="6" wrap="VIRTUAL" >{$poll_tpl.result_loop}</textarea>
      <br>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
     <td class="main_header_text" colspan="3">Result foot</td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td colspan="3"> 
      <textarea cols="120" name="tpl_{$poll_tpl_id.result_foot}" rows="12" wrap="VIRTUAL" class="code">{$poll_tpl.result_foot}</textarea>
      <br>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
     <td colspan="3">
       <table bgcolor="#FFFFFF">
       <td>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
       </td>
       <td>
	   <input type="button" value="{$lang_poll.tpl_save}" class="button" onclick="SubmitMyForm();" name="apply">
       </td>
       <td>
	   <input type="button" value="{$lang_poll.FromClear}" class="button" onclick="ResetMyForm();" name="apply">
           <input type="hidden" name="sel"      value="templates">
           <input type="hidden" name="tplset"   value="{$poll_tplset}">
           <input type="hidden" name="tpl_type" value="result">
           <input type="hidden" name="tpl_act"  value="save">
       </td>
       </table> 
     </td>
  </tr>         


  {elseif $tpl_type eq "comment"}
  <tr bgcolor="#FFFFFF">
     <td class="main_content_text" width="30%" colspan="3">
         <a href="{$pollvars.SELF}?sel=templates&poll_tplset={$poll_tplset}&tpl_type=display">Poll View</a>&nbsp;&nbsp;&nbsp;&nbsp;
         <a href="{$pollvars.SELF}?sel=templates&poll_tplset={$poll_tplset}&tpl_type=result">Poll Result</a>&nbsp;&nbsp;&nbsp;&nbsp;
         <a href="{$pollvars.SELF}?sel=templates&poll_tplset={$poll_tplset}&tpl_type=comment"><b>Comment</b></a>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
     <td class="main_header_text" colspan="3">comment</td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td colspan="3"> 
      <textarea cols="120" name="tpl_{$poll_tpl_id.comment}" rows="30" wrap="VIRTUAL">{$poll_tpl.comment}</textarea>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
     <td colspan="3">
       <table bgcolor="#FFFFFF">
       <td>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
       </td>
       <td>
	   <input type="button" value="{$lang_poll.tpl_save}" class="button" onclick="SubmitMyForm();" name="apply">
       </td>
       <td>
	   <input type="button" value="{$lang_poll.FromClear}" class="button" onclick="ResetMyForm();" name="apply">
              <input type="hidden" name="sel"      value="templates">
              <input type="hidden" name="tplset"   value="{$poll_tplset}">
              <input type="hidden" name="tpl_type" value="comment">
              <input type="hidden" name="tpl_act" value="save">
       </td>
       </table> 
     </td>
  </tr>         


  {else}
  <tr bgcolor="#FFFFFF">
     <td class="main_content_text" width="30%" colspan="3">
         <a href="{$pollvars.SELF}?sel=templates&poll_tplset={$poll_tplset}&tpl_type=display"><b>Poll View</b></a>&nbsp;&nbsp;&nbsp;&nbsp;
         <a href="{$pollvars.SELF}?sel=templates&poll_tplset={$poll_tplset}&tpl_type=result">Poll Result</a>&nbsp;&nbsp;&nbsp;&nbsp;
         <a href="{$pollvars.SELF}?sel=templates&poll_tplset={$poll_tplset}&tpl_type=comment">Comment</a>
    </td>
  </tr>

  <tr bgcolor="#FFFFFF">
     <td class="main_header_text" colspan="3">Display head</td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td colspan="3"> 
      <textarea cols="120" name="tpl_{$poll_tpl_id.display_head}" rows="12" wrap="VIRTUAL">{$poll_tpl.display_head}</textarea>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
     <td class="main_header_text" colspan="3">Display loop</td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td colspan="3"> 
      <textarea cols="120" name="tpl_{$poll_tpl_id.display_loop}" rows="6" wrap="VIRTUAL" >{$poll_tpl.display_loop}</textarea>
      <br>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
     <td class="main_header_text" colspan="3">Display foot</td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td colspan="3"> 
      <textarea cols="120" name="tpl_{$poll_tpl_id.display_foot}" rows="12" wrap="VIRTUAL" class="code">{$poll_tpl.display_foot}</textarea>
      <br>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
     <td colspan="3">
       <table bgcolor="#FFFFFF">
       <td>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
       </td>
       <td>
	   <input type="button" value="{$lang_poll.tpl_save}" class="button" onclick="SubmitMyForm();" name="apply">
       </td>
       <td>
	   <input type="button" value="{$lang_poll.FromClear}" class="button" onclick="ResetMyForm();" name="apply">
               <input type="hidden" name="sel"      value="templates">
               <input type="hidden" name="tplset"   value="{$poll_tplset}">
               <input type="hidden" name="tpl_type" value="display">
               <input type="hidden" name="tpl_act"  value="save">
       </td>
       </table> 
     </td>
  </tr>         
  {/if}


  {elseif $poll_admin_panel eq "template_new"}
  <tr bgcolor="#FFFFFF">
     <td class="main_header_text" width="30%">
       Template Set: <input type="text" name="new_tplsetname" maxlength="50" size="30">
     </td>
     <td>
     <input type="button" value="{$lang_poll.tpl_save}" class="button" onclick="SubmitMyForm();" name="apply">
     <input type="hidden" name="sel"     value="templates">
     <input type="hidden" name="tpl_act" value="create">
     </td>
  </tr>

  {/if}

  </table> 
  </form>
{include file="$admingentemplates/admin_bottom.tpl"}
{$smarty_script}
