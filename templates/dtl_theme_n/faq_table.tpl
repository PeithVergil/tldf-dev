{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple">
    <!-- begin main cell -->
    <h2 class="hdr2">{$lang.faq.questions_in_topic}:&nbsp;{$category.category}</h2>
    
        {if $form.err}
      
                <div class="error_msg">{$form.err}</div>
       
        {/if}
        {if $faqs}
      
            	<ol id="accordion3" class="accordionWrapper">
                    {section name=a loop=$faqs}
                        <li>
                            <div class="title hdr5">{$faqs[a].title}</div>
                            <div class="content">{$faqs[a].body}</div>
                        </li>
                    {/section}
                </ol>
      
        {/if}
      
               
        <p class="basic-btn_back"> <b></b><span>
                        <input type="button" class="btn_org" onclick="javascript: location='help.php'" value="{$button.back_to_topics}"></span></p>
                      
                    
                </div>
            
    <!-- end main cell -->
    {literal}
	<script language="javascript" type="text/javascript">
		$(document).ready(function()
			{
				$("#accordion3").msAccordion({defaultid:'', vertical:true});
			}
		)
	</script>
    {/literal}
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}