{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.editform}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.banners_edit}</div>
<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
<form name="edit_banner" enctype="multipart/form-data" method="post" action="{$FILE_SELF}">
{$form.hiddens}
<tr class="table_header">
  <td colspan="2" class="main_header_text" align="center" width="20%">
   {$lang.banners.properties}
  </td>
</tr>
<tr bgcolor="#FFFFFF">
  <td class="main_content_text" align="left" width="20%">
   {$lang.banners.name}: 
  </td>
  <td class="main_content_text" align="left">
   <input type="text" name="banner_name" size="40" value="{$one_banner.name}">
  </td>
</tr>
<tr bgcolor="#FFFFFF">
  <td class="main_content_text" align="left">
   {$lang.banners.status}:
  </td>
  <td class="main_content_text" align="left">
   <input type="checkbox" name="status" {if $one_banner.status eq 1}checked="checked"{/if}>
  </td>
</tr>

</tr>
<tr bgcolor="#FFFFFF">
  <td class="main_content_text" align="left">
   {$lang.banners.type}:
  </td>
  <td class="main_content_text" align="left">
   <select name="edit" onChange="javascript:ChangeInputType()">
   <option value="image">{$lang.banners.type_image}  </option>
   <option value="html" {if $edit_type eq "html"}selected{/if}>{$lang.banners.type_html}  </option>
  </td>
</tr>

{if $edit_type eq "html"}
   <tr bgcolor="#FFFFFF"><td class="main_content_text" align="left">
    {$lang.banners.place_and_size}: 
    </td>
    <td class="main_content_text" align="left"> 
       <select name="place_size_select" onChange="javascript:ChangeBar(options[selectedIndex].value)">
         {foreach from=$posible_sizes item=one_size}
          {$one_size}
         {/foreach}     
     </td>
   </tr>
   <tr bgcolor="#FFFFFF"><td class="main_content_text" align="center">
      {$lang.banners.html_code}: 
      </td>
      <td class="main_content_text" align="left"> 
         <textarea cols="120" name="banner_html_code" rows="15" wrap="VIRTUAL">{$one_banner.html_code}</textarea>
      </td>
   </tr>    
{else}
<tr bgcolor="#FFFFFF">
  <td class="main_content_text" align="left">
   {$lang.banners.image}: 
  </td>
  <td class="main_content_text" align="left">
   {if $one_banner.img_file_path neq ""}
    <img name="banner_img" src="{$one_banner.img_file_path}" width="{$one_banner.size_x}" height="{$one_banner.size_y}" alt="{$one_banner.alt_text}"></td>
   {/if}
  </td>
</tr>

   <tr bgcolor="#FFFFFF"><td class="main_content_text" align="left">
     {$lang.banners.image_path}:
     </td>
     <td class="main_content_text" align="left">
     <input type="text" name="img_file_path" size="90" value="{$one_banner.short_img_file_path}">
     </td>
   </tr>
   <tr bgcolor="#FFFFFF"><td class="main_content_text" align="left">
     {$lang.banners.upload_image}:
     </td>
     <td class="main_content_text" align="left">
     <input name="file" type="file" value="{$site_root}/uploades/banners/{$one_banner.short_img_file_path}">
     </td>
   </tr>
   <tr bgcolor="#FFFFFF"><td class="main_content_text" align="left">
    {$lang.banners.place_and_size}: 
    </td>
    <td class="main_content_text" align="left"> 
       <select name="place_size_select" onChange="javascript:ChangeBar(options[selectedIndex].value)">
         {foreach from=$posible_sizes item=one_size}
          {$one_size}
         {/foreach}     
     </td>
   </tr>
   <tr bgcolor="#FFFFFF"><td class="main_content_text" align="left">
    {$lang.banners.link}:
    </td>
    <td class="main_content_text" align="left">
    <input type="text" name="banner_url" size="40" value="{$one_banner.banner_url}">
    </td>
   </tr>
   <tr bgcolor="#FFFFFF"><td class="main_content_text" align="left">
    {$lang.banners.alttext}:
    </td>
    <td class="main_content_text" align="left">
    <input type="text" name="alt_text" size="40" value="{$one_banner.alt_text}">
    </td>
   </tr>
   <tr bgcolor="#FFFFFF"><td class="main_content_text" align="left">
    {if $one_banner.stop_after_views neq -1}
    <input type="checkbox" name="stop_after_views_checked" checked="checked" onclick="javascript:SetStatusStopByView(checked)">
    {$lang.banners.stop_after_views}:
    <td class="main_content_text" align="left">
    <input type="text" name="stop_after_views" size="40" value="{$one_banner.stop_after_views}">
    {else}
    <input type="checkbox" name="stop_after_views_checked" onclick="javascript:SetStatusStopByView(checked)">
    {$lang.banners.stop_after_views}:
    <td class="main_content_text" align="left">
    <input type="text" name="stop_after_views" size="40" value="1" disabled="disabled">
    {/if}
    </td>
   </td></tr>
   <tr bgcolor="#FFFFFF"><td class="main_content_text" align="left">
    {if $one_banner.stop_after_hits neq -1}  
    <input type="checkbox" name="stop_after_hits_checked" checked="checked" onclick="javascript:SetStatusStopByHits(stop_after_hits_checked.checked)">
    {$lang.banners.stop_after_hits}:
    <td class="main_content_text" align="left">
    <input type="text" name="stop_after_hits" size="40" value="{$one_banner.stop_after_hits}">
    {else}
    <input type="checkbox" name="stop_after_hits_checked" onclick="javascript:SetStatusStopByHits(checked)">
    {$lang.banners.stop_after_hits}:
    <td class="main_content_text" align="left">
    <input type="text" name="stop_after_hits" size="40" value="1" disabled="disabled">
    {/if}
    </td>
   </td></tr>
   <tr bgcolor="#FFFFFF"><td class="main_content_text" align="left">
    {if $one_banner.stop_after_date neq "0000-00-00"}  
    <input type="checkbox" name="stop_after_date_checked" checked="checked" onclick="javascript:SetStatusStopByHits(stop_after_hits_checked.checked)">
    {$lang.banners.stop_after_date}:
    <td class="main_content_text" align="left">
    {else}
    <input type="checkbox" name="stop_after_date_checked" onclick="javascript:SetStatusStopByDate(checked)">
    {$lang.banners.stop_after_date}:
    <td class="main_content_text" align="left">
    {/if}
    	<select name="b_day" {if $one_banner.stop_after_date eq "0000-00-00"}disabled{/if}> 
  				{section name=d loop=$day}
  					<option value="{$day[d].value}" {if $day[d].sel}selected{/if}>{$day[d].value}</option>
				{/section}
				</select>&nbsp;
  	<select name="b_month" {if $one_banner.stop_after_date eq "0000-00-00"}disabled{/if}>
				{section name=m loop=$month}
					<option value="{$month[m].value}" {if $month[m].sel}selected{/if}>{$month[m].name}</option>
				{/section}
				</select>&nbsp;
  	<select name="b_year" {if $one_banner.stop_after_date eq "0000-00-00"}disabled{/if}>
				{section name=y loop=$year}
					<option value="{$year[y].value}" {if $year[y].sel}selected{/if}>{$year[y].value}</option>
				{/section}
				</select>&nbsp;
    </td>
   </td></tr>
   <tr bgcolor="#FFFFFF"><td class="main_content_text" align="left">
    {$lang.banners.open_new_window}:    
    </td>
    <td>
    <input type="checkbox" name="open_in_new_window" {if $one_banner.open_in_new_window eq 1} checked="checked" {/if}>
   </td></tr>
{/if}
<tr><td colspan="2">{$lang.banners.edit_comment}</td></tr>
<tr bgcolor="#FFFFFF">
	<td class="main_content_text" align="left">
		{$lang.banners.for_groups}:
	</td>
	<td>
		{foreach name=g from=$one_banner.groups item=item}
		<input type="checkbox" name="groups[]" value="{$item.id}" {if $is_add_mode}checked=checked{else}{$item.checked}{/if} /> {$item.name}<br />
		{/foreach}
	</td>
</tr>
<tr><td colspan="2" bgcolor="#FFFFFF">
<table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
<tr class="table_header">
  <td colspan="2" class="main_header_text" align="center">
   {$lang.banners.banners_arrea}
  </td>
</tr>
{foreach from=$one_banner.areas item=tow_areas}
<tr bgcolor="#FFFFFF">
  <td class="main_content_text" align="left" width="50%">
   <input type="checkbox" name="area_{$tow_areas[0].id}" {if $tow_areas[0].checked eq 1} checked="checked" {/if} {if $tow_areas[0].enabled neq 1}disabled="disabled"{/if}>
   {$tow_areas[0].description}
  </td>
  {if $tow_areas[1].id eq -1}
  <td class="main_content_text" align="left">
  </td>
  {else}
  <td class="main_content_text" align="left">
   <input type="checkbox" name="area_{$tow_areas[1].id}" {if $tow_areas[1].checked eq 1} checked="checked" {/if} {if $tow_areas[1].enabled neq 1}disabled="disabled"{/if}>
   {$tow_areas[1].description}
  </td>
  {/if}
</tr>
{/foreach}
</table>
</td></tr>

<tr><td bgcolor="#FFFFFF" colspan="2"><table bgcolor="#FFFFFF">
<tr bgcolor="#FFFFFF">
  <td class="main_content_text" align="right" width="100%">
  <input type="button" value="{$lang.button.save}" class="button" onclick="SubmitMyForm();">
  </td>
  <td class="main_content_text" align="right">
  <input type="button" value="{$lang.banners.back_to_list}" class="button" onclick="javascript:document.location='{$FILE_SELF}';">
  </td> 
</tr>
</table></tr></td>
</form>
</table>
<script>
{literal}
function CheckForm()
{
  
  if (!document.forms[0].banner_name.value)
     {
      {/literal} 
      alert('{$lang.banners.enter_name}.');
      {literal} 
      return 0;
     }
{/literal} 
  {if $edit_type eq "html"}
{literal} 

{/literal} 
  {else}
{literal} 
  if (document.forms[0].stop_after_views_checked.checked)
     {
       if ((parseInt(document.forms[0].stop_after_views.value))<=0) 
          {
            {/literal} 
            alert('{$lang.banners.invalid_stop_after_views}.');
            {literal} 
            return 0;
          }
     }
  if (document.forms[0].stop_after_views_checked.checked)
     {
       if ((parseInt(document.forms[0].stop_after_hits.value))<=0) 
          {
            {/literal} 
            alert('{$lang.banners.invalid_stop_after_hits}.');
            {literal} 
            return 0;
          }
     }
{/literal}
  {/if}
{literal} 
  return 1;
}
function SubmitMyForm() 
{
  if (CheckForm()==1)
     {
       document.forms[0].submit();
     }
}  
function SetStatusStopByView(stat) 
{
  if (stat) 
     {
       document.forms[0].stop_after_views.disabled = false;
     }
     else
     {
       document.forms[0].stop_after_views.disabled = true;
     }
}  
function SetStatusStopByHits(stat) 
{
  if (stat) 
     {
       document.forms[0].stop_after_hits.disabled = false;
     }
     else
     {
       document.forms[0].stop_after_hits.disabled = true;
     }
}  
function SetStatusStopByDate(stat)
{
  if (stat) 
     {
       document.forms[0].b_day.disabled = false;
       document.forms[0].b_month.disabled = false;
       document.forms[0].b_year.disabled = false;

     }
     else
     {
       document.forms[0].b_day.disabled = true;
       document.forms[0].b_month.disabled = true;
       document.forms[0].b_year.disabled = true;
     }
};

function ChangeInputType()
{
{/literal}
  {if $is_add_mode eq "1"}
{literal} 
  document.forms[0].sel.value="add";
  document.forms[0].submit();
{/literal}
  {else}
{literal}
  document.forms[0].sel.value="edit";
  document.forms[0].submit();
{/literal}
  {/if}
{literal}
}

{/literal}
{$javascript}
</script>
{include file="$admingentemplates/admin_bottom.tpl"}