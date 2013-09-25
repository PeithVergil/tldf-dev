{strip}
 <div class="row">
  <div class="col-md-12">
  	{if $feature_user}
	    <h1>Featured Members</h1>
	    <div class="featured">
	    	{foreach item=item from=$feature_user}
	        	<a class='profile_pop' href="{$site_root}/frontPopup.php?id={$item.id}"><img src="{$item.icon_path}" title=""></a>
	        {/foreach}
	    </div>
    {/if}
   </div>
</div>
{/strip}

