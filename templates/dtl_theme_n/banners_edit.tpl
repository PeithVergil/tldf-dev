{include file="$gentemplates/index_top.tpl"}
<td>
<div class="header">{$lang.banners.properties}</div>
<div class="sep"></div>
<div class="text"><span class="help_title">{$lang.help}:</span>{$lang.banners.help_banners_edit}</div>
<div class="error_msg">{$form.error}</div>
<table class="form_table" cellspacing=0 cellpadding=0>
<form name="edit_banner" enctype="multipart/form-data" method="post" action="banners.php">
{$form.hiddens}
<tr>
	<th>{$lang.banners.name} <font class="error">*</font>:</th>
	<td><input type="text" name="banner_name" size="40" value="{$one_banner.name}"></td>
</tr>
<tr>
	<th>{$lang.banners.type}:</th>
	<td>
		<select name="edit" onChange="javascript:ChangeInputType()">
		<option value="image">{$lang.banners.type_image}  </option>
			<option value="html" {if $edit_type eq "html"}selected{/if}>{$lang.banners.type_html}  </option>
        </select>
	</td>
</tr>
{if $edit_type eq "html"}
<tr>
	<th>{$lang.banners.place_and_size}:</th>
	<td>
		<select name="place_size_select">
		 {foreach from=$posible_sizes item=one_size}
		  {$one_size}
		 {/foreach}
        </select>
	 </td>
</tr>
<tr>
	<th>{$lang.banners.html_code} <font class="error">*</font>:</th>
    <td>
		<textarea cols="120" name="banner_html_code" rows="15" wrap="VIRTUAL">{$one_banner.html_code}</textarea>
	</td>
</tr>
{else}
<tr>
	<th>{$lang.banners.image_path} :</th>
    <td>
		<input type="text" name="img_file_path" size="90" value="{$one_banner.short_img_file_path}">
	</td>
</tr>
<tr>
	<th>{$lang.banners.upload_image} <font class="error">*</font>:</th>
	<td>
		<input name="file" type="file" value="{$site_root}/uploades/banners/{$one_banner.short_img_file_path}">
	</td>
</tr>
<tr>
	<th>{$lang.banners.place_and_size}:</th>
    <td>
       <select name="place_size_select">
         {foreach from=$posible_sizes item=one_size}
          {$one_size}
         {/foreach}
       </select>
   </td>
</tr>
<tr>
	<th>{$lang.banners.link_to_profile}:</th>
    <td>
		<input type="radio" name="link_type" value="profile_link" {if $one_banner.link_type == 'profile_link' || $one_banner.link_type == ''}checked="checked"{/if} onclick="javascript: document.getElementById('b_url1').disabled=false; document.getElementById('b_url2').disabled=true;" />
		<input type="text" name="banner_url" id="b_url1" size="40" value="{$one_banner.myprofile_link}" readonly="readonly" {if $one_banner.link_type == 'own_link'}disabled="disabled"{/if}>
	</td>
</tr>
<tr>
	<th>{$lang.banners.own_link}:</th>
	<td>
		<input type="radio" name="link_type" value="own_link" {if $one_banner.link_type == 'own_link'}checked="checked"{/if} onclick="javascript: document.getElementById('b_url1').disabled=true; document.getElementById('b_url2').disabled=false;" />
		<input type="text" name="banner_url" id="b_url2" size="40" value="{$one_banner.banner_url}" {if $one_banner.link_type == 'profile_link' || $one_banner.link_type==''}disabled=true{/if}>
	</td>
</tr>
{/if}
<tr>
	<td colspan="2">
		<input type="button" value="{$lang.button.save}" class="button" onclick="SubmitMyForm();">
		<input type="button" value="{$lang.banners.back_to_list}" class="button" onclick="javascript:document.location='banners.php';">
	</td>
</tr>
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
  return 1;
}
function SubmitMyForm()
{
  if (CheckForm()==1)
     {
       document.forms[0].submit();
     }
}

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
</script>
</td>
{include file="$gentemplates/index_bottom.tpl"}