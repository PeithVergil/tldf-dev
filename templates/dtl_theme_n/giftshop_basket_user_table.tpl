{strip}
{include file="$gentemplates/index_top_popup.tpl"}
			<td height="100%" width="100%">
				<div style="margin:5px" align="left">
					<form action="giftshop.php" method="get" name="user_form" style="margin:0px">
						<input type="hidden" name="sel" value="users_form">
						<div class="header" style="margin:0px;">
							<div style="padding:5px 0px">{$header.find_user}</div>
						</div>
						<table cellpadding="0" cellspacing="0">
							<tr>
								<td height="40" class="text_head">{$header.find_user_fname}</td>
								<td height="40" style="padding-left:10px">
									<input type="text" name="search_str" id="search_str" style="width:150px" value="{$form.search_str}">
								</td>
								<td height="40" style="padding-left:10px">
									<div class="btnwrap" style="width:110px; margin-top:0px;">
										<span><span>
											<input type="submit" class="btn_org" style="width:90px;" value="{$header.find_user_search}">
										</span></span>
									</div>
								</td>
							</tr>
						</table>
						{foreach item=item from=$users}
							<div style="height:1px; margin:10px 0px" class="delimiter"></div>
							<table width="100%" border="0" cellpadding="5" cellspacing="0">
								<tr valign="middle">
									<td width="18" class="text" align="right">{$item.number}</td>
									<td width="80" class="text" align="center"><a href="#" onclick="UserUpdate({$item.id});"><img src="{$item.icon_path}" class="icon" alt=""></a></td>
									<td width="100%" class="text" valign="top" style="padding-left:10px;">
										<div class="text" style="margin-top:7px; font-weight:bold;">
											{$item.fname}
										</div>
										<div class="text" style="margin-top:2px;">
											{if $base_lang.city[$item.id_city]}{$base_lang.city[$item.id_city]}, {/if}{if $base_lang.region[$item.id_region]}{$base_lang.region[$item.id_region]}, {/if}{$base_lang.country[$item.id_country]}
										</div>
										<div class="text_hidden" style="margin-top:2px;">
											{$item.age} {$lang.home_page.ans}
										</div>
										<div style="margin-top:7px;">
											<a href="#" onclick="UserUpdate({$item.id});">{$header.basket_select_user}</a>
										</div>
									</td>
								</tr>
							</table>
						{/foreach}
					</form>
				</div>
			</td>
		</tr>
	</table>
</div>
<script type="text/javascript">
function UserUpdate(id) {ldelim}
	opener.focus();
	opener.location.href='giftshop.php?sel=users_add&id_user='+id;
	window.close();
{rdelim}
</script>
<script type="text/javascript">
{literal}
$(function() {
/*
	function log( message ) {
		$( "<div/>" ).text( message ).prependTo( "#log" );
		$( "#log" ).attr( "scrollTop", 0 );
	}
*/
	$("#search_str").autocomplete({
		source: "giftshop.php?sel=users_autocomplete",
		minLength: 2,
		select: function(event, ui) {
			if (ui.item) {
				UserUpdate(ui.item.id);
			} else {
				alert("Nothing selected, input was " + this.value);
			}
		}
	});
	$("#search_str").focus();
});
{/literal}
</script>
</body>
</html>
{/strip}