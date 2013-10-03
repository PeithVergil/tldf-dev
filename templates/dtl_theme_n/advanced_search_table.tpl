
{include file="$gentemplates/index_top.tpl"}

{strip}
<div class="toc page-simple"> {if $id_err == 1}
  <div class="error_msg">{$lang.err.no_searchname}</div>
  {/if}
  {if $id_err == 2}
  <div class="error_msg">{$lang.err.exist_searchname}</div>
  {/if}
  {if $err}
  <div class="error_msg">{$err}</div>
  {/if}
  <form action="{$form.search_action}" method="get" name="search_form" style="margin:0px;">
    <input type="hidden" name="sel" value="search" />
    <div>
    <div class="hdr2e">
      <label title="ค้นหาแบบละเอียด">{$lang.section.a_search}</label>
    </div>
    <div class="det-14-2">
      <label title="เครื่องมือในการช่วยค้นหาสมาชิกชายต่างชาติจำกัดขอบเขตในการค้นหาข้อมูล<br> จะมีส่วนช่วยเชื่อมต่อกับชายต่างชาติตรงกับความต้องการของคุณ<br> เลือกคุณสมบัติได้มากกว่าหนึ่งข้อโดยกดที่ปุ่ม CTRL ค้างไว้<br> คลื๊กเลือกลักษณะชายต่างชาติในแบบที่คุณต้องการ"> {$lang.advanced_search.help_tip.main_text} </label>
    </div>
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
      <tr>
        <td class="text_head" style="padding:10px 0px;"> {$lang.home_page.im} : {if $data.gender_1 == 1}{$gender[0].name_search}{elseif $data.gender_1 == 2}{$gender[1].name_search}{/if}
          <input type="hidden" name="gender_1" value="{$data.gender_1}" /></td>
      </tr>
      <tr>
        <td width="25%" valign="top"><table cellpadding="0" cellspacing="0" width="100%">
            <tr>
              <td width="25%" class="text_head">{$lang.home_page.seeking_a} :&nbsp;</td>
            </tr>
            <tr>
              <td class="text_head" style="padding-top:3px;"> {if ADVANCED_SEARCH_GENDER}
                <select name="gender_2" class="index_select" style="width:115px;">
                  
											{foreach item=item from=$gender}
												
                  <option value="{$item.id}" {if $item.id != $data.gender_1}selected="selected"{/if}>{$item.name_search}</option>
                  
											{/foreach}
										
                </select>
                {else}
                {if $data.gender_1 neq 1}{$gender[0].name}{elseif $data.gender_1 neq 2}{$gender[1].name}{/if}
                <input type="hidden" name="gender_2" value="{if $data.gender_1 neq 1}1{elseif $data.gender_1 neq 2}2{/if}" />
                {/if} </td>
            </tr>
            {if ADVANCED_SEARCH_COUPLE}
            <tr>
              <td style="padding-top:2px;"><table cellpadding="0" cellspacing="0">
                  <tr>
                    <td><input type="radio" name="couple_2" value="0" {if !$data.couple_2}checked="checked"{/if} /></td>
                    <td class="text" style="padding-right:15px;">{$lang.users.single}</td>
                    <td><input type="radio" name="couple_2" value="1" {if $data.couple_2}checked="checked"{/if} /></td>
                    <td class="text" style="padding-right:15px;">{$lang.users.couple}</td>
                  </tr>
                </table></td>
            </tr>
            {/if}
            {if ADVANCED_SEARCH_RELATIONSHIP}
            <tr>
              <td style="padding-top:15px;" class="text_head">{$lang.home_page.looking_for}</td>
            </tr>
            <tr>
              <td style="padding-top:2px;"><select class="index_select" name="relation[]" {if $data.root eq 1}disabled="disabled"{/if} multiple="multiple" style="width:230px;">
                  <option value="0" {if $relation.sel_all}selected{/if}>{$button.all}</option>
                  
											{html_options values=$relation.opt_value selected=$relation.opt_sel output=$relation.opt_name}
										
                </select></td>
            </tr>
            {else}
            <tr>
              <td><input type="hidden" name="relation[]" value="2" /></td>
            </tr>
            {/if}
          </table></td>
        <td width="25%" valign="top"><table cellpadding="0" cellspacing="0" width="100%">
            <tr>
              <td class="text_head">{$lang.home_page.between_the_ages_of}</td>
            </tr>
            <tr>
              <td style="padding-top:3px;"><table cellpadding="0" cellspacing="0">
                  <tr>
                    <td><select class="index_select" style="width:50px" name="age_min">
                        
													{foreach item=item from=$age_min}
														
                        <option value="{$item}" {if $item == $data.age_min}selected="selected"{/if}>{$item}</option>
                        
													{/foreach}
												
                      </select></td>
                    <td class="text">&nbsp;{$lang.home_page.and}&nbsp;</td>
                    <td><select class="index_select" style="width:50px" name="age_max">
                        
													{foreach item=item from=$age_max}
														
                        <option value="{$item}" {if $item == $data.age_max}selected="selected"{/if}>{$item}</option>
                        
													{/foreach}
												
                      </select></td>
                  </tr>
                </table></td>
            </tr>
            {if ADVANCED_SEARCH_COUNTRY}
            <tr>
              <td style="padding-top:10px;" class="text_head">{$header_perfect.country}</td>
            </tr>
            <tr>
              <td style="padding-top:2px;"><select class="index_select" name="id_country" style="width:150px" onchange="SelectRegion('as', this.value, document.getElementById('region_div'), document.getElementById('city_div'));">
                  <option value="0">{$button.all}</option>
                  
											{foreach item=item from=$country_match}
												
                  <option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
                  
											{/foreach}
										
                </select></td>
            </tr>
            {/if}
            {if ADVANCED_SEARCH_REGION}
            <tr>
              <td style="padding-top:3px;" class="text_head">{$header_perfect.region}</td>
            </tr>
            <tr>
              <td style="padding-top:2px;"><div id="region_div"> {if isset($region_match)}
                  <select class="index_select" name="id_region" style="width:150px" onchange="SelectCity('as', this.value, document.getElementById('city_div'));">
                    <option value="0">{$button.all}</option>
                    
													{foreach item=item from=$region_match}
														
                    <option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
                    
													{/foreach}
												
                  </select>
                  {else}
                  <select class="index_select" name="id_region" style="width:150px;">
                    <option value="0">{$button.all}</option>
                  </select>
                  {/if} </div></td>
            </tr>
            {/if}
            {if ADVANCED_SEARCH_CITY}
            <tr>
              <td style="padding-top:3px;" class="text_head">{$header_perfect.city}</td>
            </tr>
            <tr>
              <td style="padding-top:2px;"><div id="city_div"> {if isset($city_match)}
                  <select class="index_select" name="id_city" style="width:150px">
                    <option value="0">{$button.all}</option>
                    
													{foreach item=item from =$city_match}
														
                    <option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
                    
													{/foreach}
												
                  </select>
                  {else}
                  <select class="index_select" name="id_city" style="width:150px;">
                    <option value="0">{$button.all}</option>
                  </select>
                  {/if} </div></td>
            </tr>
            {/if}
            {if ADVANCED_SEARCH_DISTANCE}
            <tr>
              <td style="padding-top:10px;"><table cellpadding="0" cellspacing="0">
                  <tr>
                    <td><font class="text_head">{$header_s.within}</font></td>
                    <td style="padding-left:5px;"><input type="checkbox" name="within" value="1" {if $data.within eq 1}checked="checked"{/if} onclick="if (distance.disabled) distance.disabled = false; else distance.disabled = true;" /></td>
                    <td style="padding-left:3px;"><select class="index_select" id="distance" name="distance" {if $data.within eq 0}disabled="disabled"{/if}>
                        
														{foreach item=item from=$distances}
															
                        <option value="{$item.id}" {if $data.distance == $item.id}selected="selected"{/if}>{$item.name} {$item.type}</option>
                        
														{/foreach}
													
                      </select></td>
                  </tr>
                </table></td>
            </tr>
            {/if}
          </table></td>
        <td width="25%" valign="top" style="padding-top:10px;"><table cellpadding="0" cellspacing="3">
            <tr> {if ADVANCED_SEARCH_WITH_PHOTO_SEARCH}
              <td><input type="checkbox" name="foto_only" value="1" {if $data.foto_only}checked="checked"{/if} /></td>
              <td class="text_head">{$header_s.foto}</td>
              <td>&nbsp;</td>
              {/if}
              <td><input type="checkbox" name="online_only" value="1" {if $data.online_only}checked="checked"{/if} /></td>
              <td class="text_head">{$header_s.online}</td>
            </tr>
          </table></td>
        <td width="25%" valign="top"><p class="basic-btn_here"> <b>&nbsp;</b><span>
            <input type="button" onclick="document.search_form.submit();" value="{$button.search}">
            </span> </p></td>
      </tr>
    </table>
    
    {* SH 2 style here just for testing to be included in stylesheet when the template is updated with new css*}
    {literal}
    <style>
		.filter label{
			display:inline-block;
			width:300px;
			vertical-align:top;
			color:#7D7D7D;
  			font-size:13px;
 			 font-weight:bold;
		}
		.filter{
			border:1px solid #ccc;
			width:550px;
			margin:5px 0px;
			padding:10px;
			border-radius:5px;
			background:linear-gradient(to bottom, #ffffff,#E9E9E9);
			display:none;
			
		}
		
		.add-filter #plus
		{
			width:30px;
			height:30px;
		}
	</style>
    
   
    
    {/literal}
    
    <div class='filter'>
    <label> {$header_perfect.weight}</label>
      <select name="id_weight" style="width:200px" class="index_select">
        <option value="0">{$lang.home_page.any_weight}</option>
        
										{foreach item =item from=$weight}
											
        <option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
        
										{/foreach}
									
      </select>
      
      </div>
      <div class='filter'>
      <label>{$header_perfect.height}</label>
      <select name="id_height" style="width:200px" class="index_select">
        <option value="0">{$lang.home_page.any_height}</option>
        
										{foreach item=item from=$height}
											
        <option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
        
										{/foreach}
									
      </select>
      </div>
      <div class='filter'>
      <label>{$header_perfect.nationality}</label>
      <select class="index_select" name="id_nation[]" multiple="multiple" style="width:200px;">
        <option value="0" {if $default.id_nation}selected{/if}>{$button.all}</option>
        
										{foreach item = item from = $nation_match}
											
        <option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
        
										{/foreach}
									
      </select>
      </div>
      <div class='filter'>
      <label>{$header_perfect.language}</label>
      <select class="index_select" name="id_lang[]" multiple style="width:200px;">
        <option value="0" {if $default.id_lang}selected{/if}>{$button.all}</option>
        
		{foreach item=item from=$lang_sel_match}
        <option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
		{/foreach}
									
      </select>
      </div>
      
      {foreach item=item from=$info}
      <div class='filter'>
        <label>{$item.name_1|escape}</label>
        <input type="hidden" name="spr[{$item.num_1}]" value="{$item.id_1}" />
        <select class="index_select" id="info{$item.num_1}" name="info[{$item.num_1}][]" multiple="multiple" style="width:167px;">
          <option value="0" {if $item.sel_all_1}selected="selected"{/if}>{$button.all}</option>
		{html_options values=$item.opt_value_1 selected=$item.opt_sel_1 output=$item.opt_name_1}								
        </select>
      </div>
      
      {if $item.name_2}
      <div class='filter'> 
        <label>{$item.name_2|escape}</label>
        <input type="hidden" name="spr[{$item.num_2}]" value="{$item.id_2}" />
        {/if}
        {if $item.name_2}
        <select class="index_select" id="info{$item.num_2}" name="info[{$item.num_2}][]" multiple="multiple" style="width:167px;">
          <option value="0" {if $item.sel_all_2}selected="selected"{/if}>{$button.all}</option>
          
												{html_options values=$item.opt_value_2 selected=$item.opt_sel_2 output=$item.opt_name_2}
											
        </select>
        </div> {/if}
        
       {if $item.name_3}
       <div class='filter'>
       <label> {$item.name_3|escape}</label>
        <input type="hidden" name="spr[{$item.num_3}]" value="{$item.id_3}" />
        {/if} 
      {if $item.name_3}
        <select class="index_select" id="info{$item.num_3}" name="info[{$item.num_3}][]" multiple="multiple" style="width:167px;">
          <option value="0" {if $item.sel_all_3}selected="selected"{/if}>{$button.all}</option>
          
												{html_options values=$item.opt_value_3 selected=$item.opt_sel_3 output=$item.opt_name_3}
											
        </select>
        </div>
        {/if}
     {if $item.name_4}
     <div class='filter'>
        <label>{$item.name_4|escape}</label>
        <input type="hidden" name="spr[{$item.num_4}]" value="{$item.id_4}">
        {/if} 
 {if $item.name_4}
        <select class="index_select" id="info{$item.num_4}" name="info[{$item.num_4}][]" multiple="multiple" style="width:167px;">
          <option value="0" {if $item.sel_all_4}selected="selected"{/if}>{$button.all}</option>
          
												{html_options values=$item.opt_value_4 selected=$item.opt_sel_4 output=$item.opt_name_4}
											
        </select>
        </div>
        {/if} 
      {/foreach}
      
      <div id="add-filter">
   
     <input type="button" id="plus" value="+" /> Add a Filter.
     
     <select id="filterlist" style="display:none">
     <option value="">Select a filter from list</option>
     <option value="0">Weight</option>
     <option value="1">Height</option>
     <option value="2">Nationality</option>
     <option value="3">Languages</option>
     <option value="4">Marital Status</option>
     <option value="5">Body</option>
     <option value="6">Hair</option>
     <option value="7">Hair Length</option>
     <option value="8">Eyes</option>
     <option value="9">Ethnic Origin</option>
     <option value="10">Relegion</option>
     <option value="11">Education</option>
     <option value="12">Profession</option>
     <option value="13">Current Children</option>
     <option value="14">Future Children</option>
     <option value="15">Smoking Habits</option>
     <option value="16">Horoscopes</option>
     <option value="17">Drinking Habbits </option>
     <option value="18">Hoping to find</option>
     <option value="19">English Level</option>
     <option value="20">Dependents</option>
     <option value="21">Sport & Exercise Activity</option>
     <option value="22">For The Right Relationship, They Would Relocate</option>
     
     </select>
     
     </div>
     
      <div style="padding:20px 0 5px 0;">
        <p class="basic-btn_here"> <b>&nbsp;</b><span>
          <input type="button" onclick="document.search_form.submit();" value="{$button.search}">
          </span> </p>
      </div>
    </div>
    <!-- MODIFICATIONS SH2-->

    <div class="hdr2">{$lang.section.a_search}: {$lang.subsection.search_result}</div>
	{if $form.err}
		<div class="error_msg">{$form.err}</div>
	{/if}
	<div style="padding-bottom:10px;" class="det-14-2">{$header_s.toptext}</div>
	{include file="$gentemplates/user_list_top.tpl"}
	{if $empty}
		<div class="error_msg" style="margin-top:20px;">{$header_s.empty_result}</div>
	{/if}
	{if $search_res}
		{if $form.view == 'gallery'}
			<div class="delimiter">&nbsp;</div>
			{include file="$gentemplates/user_list_gallery.tpl"}
		{else}
			<div class="user-list">
				{include file="$gentemplates/user_list.tpl"}
			</div>
		{/if}
		{include file="$gentemplates/user_list_bottom.tpl"}
	{/if}
    

    <!--MODIFICATIONS END-->
  
  </form>
</div>

{literal}

	 <script>
		var filters = new Array();
		var titles = new Array();
		$(document).ready(function(e) {
        
		 filters = document.getElementsByClassName('filter');
		 titles
			for (i=0; i < filters.length; i++){
				
				filters[i].setAttribute("id","div"+i );
				
			}

	    });
	
		$(document).on('click','#plus',function(){
		
			$('#filterlist').show();
		
		});
		
		$(document).on('change','#filterlist',function(){
			
			n = $(this).val();
			$('#div'+n).slideDown(500);
			if(n!=''){
			$(this).find('option[value='+n+']').remove();
			}
			$(this).val('');
		})
		
	</script>
{/literal}
{/strip}
{include file="$gentemplates/index_bottom.tpl"}



{*
SH 2 Old content.

{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple my-profile">
	<div class="hdr2">{$lang.section.a_search}: {$lang.subsection.search_result}</div>
	{if $form.err}
		<div class="error_msg">{$form.err}</div>
	{/if}
	<div style="padding-bottom:10px;" class="det-14-2">{$header_s.toptext}</div>
	{include file="$gentemplates/user_list_top.tpl"}
	{if $empty}
		<div class="error_msg" style="margin-top:20px;">{$header_s.empty_result}</div>
	{/if}
	{if $search_res}
		{if $form.view == 'gallery'}
			<div class="delimiter">&nbsp;</div>
			{include file="$gentemplates/user_list_gallery.tpl"}
		{else}
			<div class="user-list">
				{include file="$gentemplates/user_list.tpl"}
			</div>
		{/if}
		{include file="$gentemplates/user_list_bottom.tpl"}
	{/if}
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}


*}