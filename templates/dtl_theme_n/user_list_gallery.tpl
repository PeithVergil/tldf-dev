{strip}
{foreach key=key item=item from=$search_res}
	<div style="width:130px; height:270px; float:left; margin:5px">
		<div style="margin:10px">
			<div style="margin-top:2px"><a href="{$item.profile_link}"><img src="{$item.big_icon_path}" class="big_icon" alt=""></a></div>
			<div style="margin-top:2px"><a href="{$item.profile_link}"><b>{$item.name}</b></a></div>
			<div style="margin-top:2px" class="text">{if $base_lang.city[$item.id_city]}{$base_lang.city[$item.id_city]}, {/if}{if $base_lang.region[$item.id_region]}{$base_lang.region[$item.id_region]}, {/if}{$base_lang.country[$item.id_country]}</div>
			<div style="margin-top:2px" class="text"><b>{$item.age} {$lang.home_page.ans}</b></div>
			<div style="margin-top:2px" class="text_hidden">{$item.photo_count} {$lang.users.upload_1}</div>
		</div>
	</div>
{/foreach}
<div style="clear:both"></div>
{/strip}