{include file="$admingentemplates/admin_top_popup.tpl"}
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$lang.lang_ident_feature.help_list_coutries}</div>
<form action="" method="post" name="countries">
{$form.hiddens}
{if !$form.empty}
<table class="simple_table" cellpadding="0" cellspacing="1">
<tr>
	{assign var="columns" value="4"}
	{foreach name=c from=$countries item=item}
	<td {if $item.id_lang && $item.id_lang == $form.id_lang}class="alloc_bg"{/if}><input type="checkbox" name="code[]" value="{$item.code}" {if $item.id_lang}checked="checked" {/if} {if $item.id_lang > 0 && $item.id_lang != $form.id_lang}disabled="disabled"{/if}/>&nbsp;{$item.name}</td>
	{if $smarty.foreach.c.last && $smarty.foreach.c.iteration is not div by $columns}<td colspan="{math equation="y-x%y" x=$smarty.foreach.c.iteration y=$columns}" ></td>{/if}
	{if $smarty.foreach.c.iteration is div by $columns && !$smarty.foreach.c.last}</tr><tr>{/if}
	{/foreach}
</tr>
</table>
{else}
<font class="error_msg">{$form.error}</font>
{/if}
<br />
<table cellpadding="0" cellspacing="0">
<tr>
	<td>{if !$form.empty}<input id="submit_butt" type="submit" value="{$lang.button.save}" class="button_left" >{/if}<input type="button" value="{$lang.button.close}" class="button" onclick="javascript: window.close();">
	</td>
</tr>
</table>
</form>
{if $form.empty}
{literal}
<script type="text/javascript">
{/literal}
install_link = '{$form.install_link}';
{literal}
function goToInstall(){
	opener.window.location=install_link;
	opener.window.focus();
	window.close();
}
</script>
{/literal}
{/if}
{include file="$admingentemplates/admin_bottom_popup.tpl}