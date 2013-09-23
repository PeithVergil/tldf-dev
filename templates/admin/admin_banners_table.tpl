{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.list}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.banners}</div>
        <table border=0 class="table_main" cellspacing=1 cellpadding=5 width="100%">
              <tr class="table_header">
                <td class="main_header_text" align="middle">{$lang.banners.status}</td>
                <td class="main_header_text" align="center">{$lang.banners.banner}</td>
                <td class="main_header_text" align="middle">{$lang.banners.link}</td>
                <td class="main_header_text" align="middle">{$lang.banners.place}</td>
                <td class="main_header_text" align="middle" width="120" style="white-space:nowrap;">{$lang.banners.stop_after}</td>
                <td class="main_header_text" align="middle" style="white-space:nowrap;">{$lang.banners.shown_for}</td>
                <td>&nbsp;</td>
              </tr>
              {foreach from=$all_banners item=one_banner}
              <tr bgcolor="#FFFFFF">
                <td class="main_content_text" align="center">
                    <input type="checkbox" name="comments" disabled="disabled" {if $one_banner.status eq 1}checked="checked"{/if}>
                </td>
                <td class="main_content_text" align="center">
                  <table bgcolor="#FFFFFF">
                   <tr><td class="main_content_text" align="center">
                    {$one_banner.name}
                   </td></tr>
                   {if $one_banner.banner_type  eq "1"}
                     <tr><td class="main_header_text" align="center">
                       {$lang.banners.this_is_html_code}
                     </td></tr>
                   {else}
                     <tr><td class="main_content_text" align="center">
                       {$lang.banners.size}: {$one_banner.size_x}x{$one_banner.size_y}
                     </td></tr>
                     <tr><td align="center">
                      {if $one_banner.img_file_path neq ""}
                        <img src="{$one_banner.img_file_path}" width="{$one_banner.show_size_x}" height="{$one_banner.show_size_y}" alt="{$one_banner.alt_text}">
                      {/if}
                     </td></tr>
                   {/if}
                  </table>
                </td>
                {if $one_banner.banner_type  eq "1"}
                <td>
                </td>
                {else}
                <td class="main_content_text" align="center">
                 <a href="{$one_banner.banner_url}">{$one_banner.banner_url}</a>
                </td>
                {/if}
                <td class="main_content_text" align="center">
                  <table bgcolor="#FFFFFF">
                    <tr><td class="main_content_text" align="center">
                       {$lang.banners.position}: {if $one_banner.place eq 0}{$lang.banners.position_left}{else}{$lang.banners.position_bottom}{/if} <br>
                    </td></tr>
                    <tr><td class="main_content_text" align="center">

                    {foreach from=$one_banner.areas item=one_area}
                      {$one_area.description};&nbsp&nbsp
                    {/foreach}

                    </td></tr>
                  </table>
                </td>
                <td class="main_content_text" align="center">
                {if $one_banner.stop_after_views eq 0}
                 {$lang.banners.stoped_by_views}!
                {elseif $one_banner.stop_after_hits eq 0}
                  {$lang.banners.stoped_by_hits}!
                {elseif $one_banner.stoped_by_date}
                  {$lang.banners.stoped_by_date}!
                {elseif $one_banner.stop_after_views neq -1 or $one_banner.stop_after_hits neq -1 or $one_banner.stop_after_date neq "0000-00-00"}
                 {if $one_banner.stop_after_views neq -1}
                   {$lang.banners.stop_after_views}: {$one_banner.stop_after_views} <br><br>
                 {/if}
                 {if $one_banner.stop_after_hits neq -1}
                   {$lang.banners.stop_after_hits}: {$one_banner.stop_after_hits}
                 {/if}
                 {if $one_banner.stop_after_date neq "0000-00-00"}
                   {$lang.banners.stop_after_date}: {$one_banner.stop_after_date}
                 {/if}
                {else}
                 {$lang.banners.never_stop}
                {/if}
                </td>
				<td>
					{$one_banner.groups}
				</td>
                <td class="main_content_text" align="center">
                <a href="admin_banners.php?sel=statistics&id={$one_banner.id}">{$lang.button.statistic}</a>&nbsp;<br><br>
                <a href="{$FILE_SELF}?sel=edit&id={$one_banner.id}">{$lang.button.edit}</a>&nbsp;<br><br>
                <a href="#" onclick="javascript: if (window.confirm('{$lang.banners.realy_delete}')){literal}{{/literal}location.href='{$FILE_SELF}?sel=delete&id={$one_banner.id}';{literal}}{/literal}">{$lang.button.delete}</a>&nbsp;
		</td>
              </tr>
              {/foreach}
              <tr bgcolor="#FFFFFF">
                <td colspan="7" align="right"><input type="button" value="{$lang.button.add}" class="button" onclick="javascript: location.href='admin_banners.php?sel=add';"></td>
              </tr>
        </table><br><br>
	<font class=red_sub_header>{$header.parameters}</font>
	<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.banners_rotate}</div>
        <form method="post" action="{$FILE_SELF}?sel=save_rotate" name="banner_form">
        <table border=0 class="table_main" cellspacing=1 cellpadding=5 width="350">
              <tr class="table_header">
                <td class="main_header_text" align="center">{$lang.banners.rotate_area}</td>
                <td class="main_header_text" align="center">{$lang.banners.rotate_status}</td>
                <td class="main_header_text" align="center">{$lang.banners.rotate_time}</td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td class="main_content_text" align="center">{$lang.banners.position_left}</td>
                <td class="main_content_text" align="center">
                 <input type="checkbox" name="rotate_left_flag" {if $rotate_left_flag eq 1}checked="checked"{/if}>
                </td>
                <td class="main_content_text" align="center">
                 <input type="text" name="rotate_left_time" value="{$rotate_left_time}" size="10"> {$lang.banners.ms}
                </td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td class="main_content_text" align="center">{$lang.banners.position_bottom}</td>
                <td class="main_content_text" align="center">
                 <input type="checkbox" name="rotate_bottom_flag" {if $rotate_bottom_flag eq 1}checked="checked"{/if}>
                </td>
                <td class="main_content_text" align="center">
                 <input type="text" name="rotate_bottom_time" value="{$rotate_bottom_time}" size="10"> {$lang.banners.ms}
                </td>
              </tr>
              <tr bgcolor="#FFFFFF">
                <td colspan="3" align="right"><input type="button" value="{$lang.button.save}" class="button" onclick="javascript: document.banner_form.submit();"></td>
              </tr>
        </table>
        </form>
{include file="$admingentemplates/admin_bottom.tpl"}