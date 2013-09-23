{include file="$admingentemplates/admin_top.tpl"}
<font class="red_header">
	{$header.razdel_name}
</font>
<font class="red_sub_header">
	&nbsp;|&nbsp;{$header.load_contacts}
</font>
<div class="help_text">
	<span class="help_title">{$lang.help}:</span>{$help.solve360_load_contacts}
</div>
{* <input type="button" value="{$header.load_contacts}" onclick="if (confirm('Did you delete all old TLDF contacts in Solve360 ?')) window.location.href='admin_solve360.php?sel=load_contacts&amp;load=1';"> *}
<input type="button" value="{$header.load_contacts}" onclick="alert('TLDF contacts were already loaded into Solve360. Please run the load script manually if you want to load them again AFTER deleting all TLDF Contacts from Solve360.');">
<br><br>
{if $smarty.get.load == '1'}
	<div style="padding:10px;">
		<iframe src="admin_solve360.php?sel=load_contacts_run&amp;show_progress=1" scrolling="auto" frameborder="0" style="border:1px solid silver; width:600px; height:500px;">
		<center>Sorry, your browser doesn't support frames.</center>
		</iframe>
	</div>
{/if}
{include file="$admingentemplates/admin_bottom.tpl"}