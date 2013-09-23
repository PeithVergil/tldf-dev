{strip}
 <h2>Featured Users</h2>
 {if $feature_user}
 
			{foreach item=item from=$feature_user}
 <div class="list-item">
                        <ul>
                            <li><span>{$item.name}</span></li>
                            <li><label title="{$item.name}, {$item.age} Years">
                                    <a class='profile_pop' href="{$site_root}/frontPopup.php?id={$item.id}"><img src="{$item.icon_path}" title=""></a>
                                </label>
                            </li>
                        </ul>

                    </div>
                    {/foreach}
		{/if}
                    

	<script type="text/javascript">
		{literal}
		$(function() {
			$('.profile_pop').colorbox({iframe:true, innerWidth:750, height:'100%'});
		});
		{/literal}
	</script>
</div>
{/strip}