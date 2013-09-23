{strip}
{if $registered}
	{if $auth.is_applicant}
		<div>
			<a class="menu_block_1_link" href="myprofile.php">{$lang.top_menu.registration_home}</a>
			{if $smarty.session.permissions.account}
				&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_1">|</font>&nbsp;&nbsp;&nbsp;
				<a class="menu_block_1_link" href="account.php">{$lang.top_menu.my_account}</a>
				&nbsp;&nbsp;&nbsp;<font class="menu_delimiter_1">|</font>&nbsp;&nbsp;&nbsp;
				<a class="menu_block_1_link" href="account.php?sel=passw&amp;from=myprofile">{$lang.account.subheader_changepass}</a>
			{/if}
		</div>
	{else}
		<div id="main-navigation">
			<div>
				<ul>
					<li class="menu_first menu_block_1{if $sub_menu_num == 1}_active{/if}">
						<div onclick="window.location.href='{$site_root}/homepage.php';">
							{$lang.index_top_big_1}
						</div>
					</li>
					<li class="menu_inner menu_block_2{if $sub_menu_num == 2}_active{/if}">
						<div class="placeholder" onclick="">
							{$lang.index_top_big_2}
						</div>
					</li>
					{*
					<li class="menu_inner menu_block_3{if $sub_menu_num == 3}_active{/if}">
						<div onclick="">
							{$lang.index_top_big_3}
						</div>
					</li>
					*}
					{if !in_array($auth.id_group, array(MM_PLATINUM_LADY_FIRST_INS_ID,MM_PLATINUM_LADY_SECOND_INS_ID,MM_PLATINUM_LADY_ID))}
						<li style="width:240px;">&nbsp;</li>
						<li class="menu_inner menu_block_4{if $sub_menu_num == 4}_active{/if}">
							<div onclick="window.location.href='payment.php?sel=buy_connection';">
								{$lang.index_top_big_4}
							</div>
						</li>
					{else}
						<li style="width:414px;">&nbsp;</li>
					{/if}
					<li class="menu_inner menu_block_5{if $sub_menu_num == 5}_active{/if}">
						<div onclick="window.location.href='{$site_root}/platinum_match.php';">
							{$lang.index_top_big_5}
						</div>
					</li>
					<li class="menu_last menu_block_6{if $sub_menu_num == 6}_active{/if}">
						<div onclick="window.location.href='{$site_root}/dating_events.php';">
							{$lang.index_top_big_6}
						</div>
					</li>
				</ul>
			</div>
		</div>
		{include file="$gentemplates/how_it_works.tpl"}
	{/if}
{/if}
{/strip}