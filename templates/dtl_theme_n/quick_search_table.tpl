{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple my-profile">
	<div class="hdr2">
		{*<!--{$lang.section.q_search}:-->*}
		{$lang.subsection.search_result}
	</div>
	{if $form.err}
		<div class="error_msg">{$form.err}</div>
	{/if}
	{include file="$gentemplates/user_list_top.tpl"}
	{if $empty}
		<div class="error_msg" style="margin-top:20px;">{$header_s.empty_result}</div>
	{/if}
	<div style="padding:20px 0px 0px 15px;">
		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td valign="middle">
					<span style="display: inline;" id="hide_image"><img src="{$site_root}{$template_root}/images/btn_up.gif" alt="" vspace="0" hspace="0" align="middle"></span>
					<span style="display: none;" id="show_image"><img src="{$site_root}{$template_root}/images/btn_down.gif" alt="" vspace="0" hspace="0" align="middle"></span>
				</td>
				<td valign="middle" style="padding-left: 5px;">
					<span style="display: inline;" id="hide_link"><a href="#" onclick="ShowSearchForm('1'); return false;">{$lang.users.hide_search_form}</a></span>
					<span style="display: none;" id="show_link"><a href="#" onclick="ShowSearchForm('2'); return false;">{$lang.users.show_search_form}</a></span>
				</td>
			</tr>
		</table>
		<div>
			{include file="$gentemplates/quick_search_small_form.tpl"}
		</div>
		<div>&nbsp;</div>
	</div>
	{if $search_res}
		{if $form.view == 'gallery'}
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
<script type="text/javascript">
{literal}
function ZipCodeCheck(zip_value)
{
	if (zip_value == '') {
		document.getElementById('within').disabled = false;
		document.getElementById('search_type').value = 1;
	} else {
		document.getElementById('search_type').value = 2;
		document.getElementById('within').disabled = true;
	}
}
function ShowSearchForm(par)
{
	if (par == '1') {
		document.getElementById('hide_image').style.display = 'none';
		document.getElementById('hide_link').style.display = 'none';
		document.getElementById('show_image').style.display = 'inline';
		document.getElementById('show_link').style.display = 'inline';
		document.getElementById('quick_search_block').style.display = 'none';
	} else {
		document.getElementById('hide_image').style.display = 'inline';
		document.getElementById('hide_link').style.display = 'inline';
		document.getElementById('show_image').style.display = 'none';
		document.getElementById('show_link').style.display = 'none';
		document.getElementById('quick_search_block').style.display = 'block';
	}
}
{/literal}
</script>
{include file="$gentemplates/index_bottom.tpl"}