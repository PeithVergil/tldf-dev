<div class="user-link">
	{if $link_item != null}
		{assign var="item" value="$link_item"}
	{/if}
	{if $form.guest_user != 1 && $form.user != $item.id}
		<ul>
			{if $item.add_hotlist_link}
				<li><a href="{$item.add_hotlist_link}">{$lang.user_link.add_to_hotlist}</a></li>
			{elseif $item.del_hotlist_link}
				<li><a href="{$item.del_hotlist_link}">{$lang.user_link.del_from_hotlist}</a></li>
			{/if}
			{if $item.kiss_link}
				<li><a href="{$item.kiss_link}">{$lang.user_link.send_kiss}</a></li>
			{/if}
			{if $item.ecard_link}
				<li><a href="{$item.ecard_link}">{$lang.user_link.send_ecard}</a></li>
			{/if}
			{if $item.gift_link}
				<li><a href="{$item.gift_link}">{$lang.user_link.send_gift}</a></li>
			{/if}
			{if $item.add_connection_link}
				<li><a href="{$item.add_connection_link}">{$lang.user_link.add_to_connections}</a></li>
			{*<!--
			{elseif $item.del_connection_link}
				<li><a href="{$item.del_connection_link}">{$lang.user_link.del_from_connections}</a></li>
			-->*}
			{/if}
			{if $item.email_link}
				{if $item.connected_status == CS_CONNECTED}
					<li><a href="{$item.email_link}">{$lang.user_link.send_email}</a></li>
				{elseif $item.connected_status == CS_RECEIVED}
					{if $item.gender == GENDER_MALE}
						<li><a href="javascript:void(0);" onclick="jAlert('{$lang.err.email_not_connected}<br><br>{$lang.err.connection_invite_already_received_from_guy|escape:'javascript'}');return false;">{$lang.user_link.send_email}</a></li>
					{else}
						<li><a href="javascript:void(0);" onclick="jAlert('{$lang.err.email_not_connected}<br><br>{$lang.err.connection_invite_already_received_from_lady|escape:'javascript'}');return false;">{$lang.user_link.send_email}</a></li>
					{/if}
				{elseif $item.connected_status == CS_SENT}
					{if $item.gender == GENDER_MALE}
						<li><a href="javascript:void(0);" onclick="jAlert('{$lang.err.email_not_connected}<br><br>{$lang.err.connection_invite_already_sent_to_guy}');return false;">{$lang.user_link.send_email}</a></li>
					{else}
						<li><a href="javascript:void(0);" onclick="jAlert('{$lang.err.email_not_connected}<br><br>{$lang.err.connection_invite_already_sent_to_lady}');return false;">{$lang.user_link.send_email}</a></li>
					{/if}
				{else}
					<li><a href="javascript:void(0);" onclick="jAlert('{$lang.err.email_not_connected}<br><br>{$lang.err.not_connected_connect_now|escape:'javascript'|replace:'#URL#':$item.add_connection_link}');return false;">{$lang.user_link.send_email}</a></li>
				{/if}
			{/if}
			{if !$item.block_chat_webcam}
				{*<!-- <li><a href="javascript:void(0)" onclick="open_im_window({$item.id});return false;">{$lang.user_link.chat}</a></li> -->*}
				{if $item.connected_status == CS_CONNECTED}
					{if $item.status == 'Online'}
						<li><a href="javascript:void(0);" onclick="invite_chat({$item.id}, 'text');">{$lang.user_link.chat}</a></li>
						<li><a href="javascript:void(0);" onclick="invite_chat({$item.id}, 'video');">{$lang.user_link.webcam}</a></li>
					{else}
						<li><a href="javascript:void(0);" onclick="jAlert('{$lang.err.user_offline}');return false;">{$lang.user_link.chat}</a></li>
						<li><a href="javascript:void(0);" onclick="jAlert('{$lang.err.user_offline}');return false;">{$lang.user_link.webcam}</a></li>
					{/if}
				{elseif $item.connected_status == CS_RECEIVED}
					{if $item.gender == GENDER_MALE}
						<li><a href="javascript:void(0);" onclick="jAlert('{$lang.err.textchat_not_connected}<br><br>{$lang.err.connection_invite_already_received_from_guy|escape:'javascript'}');return false;">{$lang.user_link.chat}</a></li>
						<li><a href="javascript:void(0);" onclick="jAlert('{$lang.err.videochat_not_connected}<br><br>{$lang.err.connection_invite_already_received_from_guy|escape:'javascript'}');return false;">{$lang.user_link.webcam}</a></li>
					{else}
						<li><a href="javascript:void(0);" onclick="jAlert('{$lang.err.textchat_not_connected}<br><br>{$lang.err.connection_invite_already_received_from_lady|escape:'javascript'}');return false;">{$lang.user_link.chat}</a></li>
						<li><a href="javascript:void(0);" onclick="jAlert('{$lang.err.videochat_not_connected}<br><br>{$lang.err.connection_invite_already_received_from_lady|escape:'javascript'}');return false;">{$lang.user_link.webcam}</a></li>
					{/if}
				{elseif $item.connected_status == CS_SENT}
					{if $item.gender == GENDER_MALE}
						<li><a href="javascript:void(0);" onclick="jAlert('{$lang.err.textchat_not_connected}<br><br>{$lang.err.connection_invite_already_sent_to_guy}');return false;">{$lang.user_link.chat}</a></li>
						<li><a href="javascript:void(0);" onclick="jAlert('{$lang.err.videochat_not_connected}<br><br>{$lang.err.connection_invite_already_sent_to_guy}');return false;">{$lang.user_link.webcam}</a></li>
					{else}
						<li><a href="javascript:void(0);" onclick="jAlert('{$lang.err.textchat_not_connected}<br><br>{$lang.err.connection_invite_already_sent_to_lady}');return false;">{$lang.user_link.chat}</a></li>
						<li><a href="javascript:void(0);" onclick="jAlert('{$lang.err.videochat_not_connected}<br><br>{$lang.err.connection_invite_already_sent_to_lady}');return false;">{$lang.user_link.webcam}</a></li>
					{/if}
				{else}
					<li><a href="javascript:void(0);" onclick="jAlert('{$lang.err.textchat_not_connected}<br><br>{$lang.err.not_connected_connect_now|escape:'javascript'|replace:'#URL#':$item.add_connection_link}');return false;">{$lang.user_link.chat}</a></li>
					<li><a href="javascript:void(0);" onclick="jAlert('{$lang.err.videochat_not_connected}<br><br>{$lang.err.not_connected_connect_now|escape:'javascript'|replace:'#URL#':$item.add_connection_link}');return false;">{$lang.user_link.webcam}</a></li>
				{/if}
			{/if}
			{if $item.add_blacklist_link}
				<li><a href="{$item.add_blacklist_link}">{$lang.user_link.add_to_blacklist}</a></li>
			{elseif $item.del_blacklist_link}
				<li><a href="{$item.del_blacklist_link}">{$lang.user_link.del_from_blacklist}</a></li>
			{/if}
			{* <!--
				{if $smarty.const.MM_ENABLE_COUPLES && $is_coupled != 1}<li><a href="{$item.be_couple_link}">{$lang.user_link.be_my_couple}</a></li>{/if}
				{if $smarty.const.MM_ENABLE_RATE}<li><a href="viewprofile.php?id={$item.id}&amp;sel=5">{$lang.user_link.rate}</a></li>{/if}
				<li><script type="text/javascript">up.render( "badge", {ldelim}userid: "{$item.id}", displayName: "{$item.name}"{rdelim} );</script></li>
				{if $voipcall_feature}
					{if $item.phone}
						<li><a href="{$item.call_link}">{$lang.voip.call_request}</a></li>
					{else}
						<li>{$lang.voip.no_number}</li>
					{/if}
				{/if}
			--> *}
		</ul>
	{/if}
	<div class="clear"></div>
</div>
<script type="text/javascript">
{literal}
{/literal}
</script>