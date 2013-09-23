{include file="$gentemplates/index_top.tpl"}
<div class="toc page-simple shoutbox">
    <div class="hdr2">{$lang.section.shoutbox}</div>
     {if $form.err}
          <div class="error_msg">{$form.err}</div>
         
        {/if}
        {if $empty eq 1}
            <div class="error_msg">{$header_s.empty_result}</div>
        {/if}
    <!-- begin main cell -->
 

       
        {if $search_res}
     
                <div class="text">{$section.search_result}: <font class="text_head">{$form.pages_count} {$lang.pages}</font></div>
       
            <!-- begin results list -->
            {strip}
          
                {foreach key=key item=item from=$search_res name=s}
					<div style="margin: 0px; padding-top:15px;">
						<table border="0" cellspacing="0" cellpadding="0" width="100%">
							<tr>
								<td width="15" class="text" align="center" valign="middle">{$item.number}</td>
								<td style="padding-left:5px;" valign="top">
									<table cellpadding="0" width="100%" cellspacing="0" border="0">
										<tr>
											<td width="50%">
												<font class="text_head">{$item.text}</font>
											</td>
											<td width="50%">
												{if $form.guest_user != 1}
													<a href="{$item.edit_link}">{$header_s.edit_shout}</a>
													&nbsp;&nbsp;|&nbsp;&nbsp;
													{if $item.status eq '0'}<span class="pending_b">{$lang.shoutbox.approval_pending}</span>{/if}
													{if $item.status eq '1'}<span class="verified">{$lang.shoutbox.approved}</span>{/if}
													{*
													&nbsp;&nbsp;|&nbsp;&nbsp;
													<a href="#" onclick="javascript:DeleteShout({$item.delete_id});">{$header_s.del_from_shoutlist}</a>
													*}
												{else}
													&nbsp;
												{/if}
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
                {/foreach}
                <ol class="page-nation">
					{foreach item=item from=$links}
						<li><a href="{$item.link}" {if $item.selected eq '1'} class="selected"{/if}>{$item.name}</a></li>
					{/foreach}
				</ol>
           
            {/strip}
            <!-- end results list -->
      
        {/if}

    <!-- end main cell -->
</div>
{include file="$gentemplates/index_bottom.tpl"}