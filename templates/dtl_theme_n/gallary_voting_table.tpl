{include file="$gentemplates/index_top.tpl"}
<div class="toc page-simple gallery">
{strip}
	<!-- begin main cell -->
	<div class="content" style="margin: 0px; padding: 10px 15px 10px 10px;">
		<div style="padding-top: 5px;">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td width="16"><img src="{$site_root}{$template_root}/images/btn_back.gif"></td>
				<td><a href="gallary.php?upload_type={$form.upload_type}">{$lang.gallary.back_to_categories_list}</a>&nbsp;&nbsp;&nbsp;</td>
			</tr>
		</table>
		</div>
		<div style="margin: 0px; padding-top: 7px;">
			<div class="hdr2">{$lang.gallary.voting}</div>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		{if $upload ne 'empty'}
		<tr>
			<td valign="top" style="padding-top: 10px;">
				<div id="active_section">
				{include file="$gentemplates/gallary_vote_photo_section.tpl"}
				</div>
			</td>
		</tr>
		{else}
		<tr>
			<td valign="top" style="padding-top: 10px;">{$lang.gallary.no_photos}</td>
		</tr>
		{/if}
		</table>
		</div>
	</div>
	<!-- end main cell -->
{/strip}
</div>
{literal}
<script type="text/javascript">
if (document.images)
{
	var mark_show=new Image;
	mark_show.src="{/literal}{$site_root}{$template_root}/images/vote_icon_1.gif{literal}"
	var mark_hide=new Image;
	mark_hide.src="{/literal}{$site_root}{$template_root}/images/vote_icon_0.gif{literal}"
}

function marks(id,type)
{
	if (!document.images)
	{
		return false;
	}
	for (i=1; i<=id; i++)
	{
		if (type=="show")
		{
			document.images["mark"+i].src=mark_show.src;
		}
		else if (type=="hide")
		{
			document.images["mark"+i].src=mark_hide.src;
		}
	}
	return;
}
</script>
{/literal}
{include file="$gentemplates/index_bottom.tpl"}