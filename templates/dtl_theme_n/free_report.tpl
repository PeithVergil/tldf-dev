{include file="$gentemplates/index_top.tpl"}
{strip}
<td class="main_cell">
    <!-- begin main cell -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td height="25px">
                <div class="header">{$lang.section.free_report}</div>
            </td>
        </tr>
        {if $form.err}
        <tr height="25">
            <td><div class="error_msg">{$form.err}</div></td>
		</tr>
        {/if}
        <tr>
            <td>
				<table width="100%" cellpadding="0" cellspacing="0">
					<tr>
						<td class="text" valign="top">
							{$lang.free_report.intro_text}
						</td>
						<td width="350" valign="top" style="padding-left:15px;">
							<img src="{$site_root}{$template_root}/images/happy_couples.png" />
						</td>
					</tr>
				</table>
			</td>
        </tr>
    </table>
    <!-- end main cell -->
</td>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}