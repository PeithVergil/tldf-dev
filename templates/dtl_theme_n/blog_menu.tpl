<div style="height: 30px; margin-top: 10px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="30" align="center" class="tab{if $form.blog_page eq '1'}_active{else}_first{/if}" nowrap>
			<a href="blog.php?{if $form.view eq 1}sel=view_user&id_blog={$blog_info.id}{else}sel=my_blog{/if}" {if $form.blog_page eq '1'}class="text"{/if}>{if $form.view eq 1}{$blog_info.user_login} {$header_s.header}{else}{$header_s.blog_menu_1}{/if}</a>
		</td>
		<td align="center" class="tab{if $form.blog_page eq '2'}_active{/if}" nowrap>
			<a href="blog_calendar.php" {if $form.blog_page eq '2'}class="text"{/if}>{$header_s.blog_menu_2}</a>
				<div id="tool_tip_6"><label title="{$lang.blog.help_tip.calendar}"><img src="{$site_root}{$template_root}/images/question_icon.gif"></label></div>
				{literal}
				<script type="text/javascript">
				$(function() {
				$('#tool_tip_6 *').tooltip();
				});
				</script>
				{/literal}
		</td>
		<td align="center" class="tab{if $form.blog_page eq '3'}_active{/if}" nowrap>
			<a href="blog.php?sel=friends" {if $form.blog_page eq '3'}class="text"{/if}>{$header_s.blog_menu_4}</a>
			<div id="tool_tip_7"><label title="{$lang.blog.help_tip.my_friends}"><img src="{$site_root}{$template_root}/images/question_icon.gif"></label></div>
			{literal}
			<script type="text/javascript">
			$(function() {
			$('#tool_tip_7 *').tooltip();
			});
			</script>
			{/literal}
		</td>
		<td align="center" class="tab{if $form.blog_page eq '4'}_active{/if}" nowrap>
			<a href="blog.php?sel=all_blogs" {if $form.blog_page eq '4'}class="text"{/if}>{$header_s.blog_menu_5}</a>
		</td>
	</tr>
</table>
</div>
<div style="margin: 0px; padding: 0px; height: 1px; background-color: #{$css_color.home_search}; font-size: 0px;"></div>