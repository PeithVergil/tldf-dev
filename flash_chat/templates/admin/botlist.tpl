{include file="$admingentemplates/admin_top.tpl"}
<font class=red_header>{$header.list}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.bots}</font>
<div class="help_text"><span class="help_title">{$lang.help}:</span>{$help.chat_bots}</div>
{if $form_type eq '1'}
	{if $enableBots}
	<form name="botlist" id="botlist" action="botlist.php" method="post">
		<input type="hidden" id="sort" name="sort" value="none">
	</form>
	<table><tr height="40"><td>
		<input type="button" onclick="javascript:location.href='bot.php?id=0';" value="Add New Bot">
	</tr></table>
	<br>
	<table border=0 class="table_main" cellspacing=1 cellpadding=3 width="100%">
	{if $botnames}
	<tr class="table_header">
		<th class="main_header_text" align="center"><a href="javascript:my_getbyid('sort').value = 'login'; my_getbyid('botlist').submit()">Bot Name</a></th>
		<th class="main_header_text" align="center">Delete</th>
	</tr>
	{foreach from=$botnames item=bot}
	{assign var="on_click" value="javascript: decision('Do you really want delete the bot?','botlist.php?id=`$bot.id`')"}
	<tr bgcolor="#FFFFFF">
		<td class="main_content_text" align=center><a href="bot.php?id={$bot.id}">{$bot.login}</a></td>
		<td class="main_content_text" align="center">
		<input type="button" id="{$button_pass_id}" onclick="{$on_click}" value="Delete"></td>
	</tr>
	{/foreach}
	{else}
		<tr height="40" bgcolor="#FFFFFF">
			<td class="main_error_text" align="left" colspan="2">No bots found</td>
		</tr>
	{/if}
	</table>
	{else}
	<table><tr height="40"><td>
		<input type="button" onclick="javascript: location.href='./botlist.php?sel=install_form';" value="Install Bots">
	</tr></table>
	<table border=0 class="table_main" cellspacing=1 cellpadding=3 width="100%">
		<tr height="40" bgcolor="#FFFFFF">
			<td class="main_error_text" align="left" colspan="2">The bot feature is currently disabled.</td>
		</tr>
	</table>
	{/if}
{elseif $form_type eq '2'}
	<font color="red">{$errmsg}</font>
	<FORM  method="post" align="center" name="installInfo" action="./botlist.php">
	<input type="hidden" name=sel value="install">
	<table border=0 class="table_main" cellspacing=1 cellpadding=3 width="100%">
	<tr bgcolor="#FFFFFF">
		<td class="main_content_text" align="left" colspan="3">
			<font class="main_header_text"> Integrating an AI bot with FlashChat</font><br>
			<p>You may chat with an artificial intelligence entity known as a "bot" in FlashChat.
				In addition to the standard bot knowledge base, you may add custom bot knowledge
				bases to make your bot smarter. You may read more about the technology behind this
				feature at <a href="http://www.alicebot.org" target="_blank">Alicebot.org</a> . FlashChat uses a specific Alicebot variant called <a href="http://sourceforge.net/projects/programe" target="_blank">Program E</a> .
			</p>
			<p>
			After installation, you may start the bot by logging into FlashChat as a moderator, then issue the following commands in sequence:<br>
			{literal}
			/addbot {botname}<br>
			/startbot {botname}<br>
			/killbot {botname}<br>
			</p>
			<p>
			For example:<br>
			/addbot Alice<br>
			/startbot Alice<br>
			/killbot Alice</p>
			{/literal}
			<p>
			Additional bot startup options can be found in the "bots" section of the FlashChat admin panel.
			</p>
		</td>
	</tr>
	{if $errmsg}
	<tr height="40" bgcolor="#FFFFFF">
		<td class="main_error_text" align="left" colspan="3">{$errmsg}</td>
	</tr>
	{/if}
	<tr height="40" bgcolor="#FFFFFF">
		<td class="main_content_text" align="left" colspan="3"><b>Bot knowledge bases:</b><br>Complete bot installation requires about 10 MB of database storage space.</td>
	</tr>
	<tr bgcolor="#ECECEC">
		<td class="main_content_text" align="center" colspan="3">
			<table  border="0" cellspacing="5" width="100%">
									{section name=s loop=$learnfiles}
									{if $smarty.section.s.index is div by 3}<tr>{/if}
									<td nowrap class="main_content_text"><INPUT type="checkbox" name="fld_{$learnfiles[s]}" value="1" checked style='border: none'>&nbsp;{$learnfiles[s]}</td>
									{if $smarty.section.s.index_next is div by 3 || $smarty.section.s.last}</tr>{/if}
									{/section}
			</table>
		</td>
	</tr>
	</table>
	<table><tr height="40"><td>
		<input type="button" onclick="javascript: document.installInfo.submit();" value="Continue >>">
	</tr></table>
	</form>
{elseif $form_type eq '3'}
	<font color="red">{$errmsg}</font>
	<input type="hidden" name=sel value="install">
	<table border=0 class="table_main" cellspacing=1 cellpadding=3 width="100%">
	{if $errmsg}
	<tr height="40" bgcolor="#FFFFFF">
		<td class="main_error_text" align="left" colspan="3">{$errmsg}</td>
	</tr>
	{/if}
	<tr height="40" bgcolor="#FFFFFF">
		<td class="main_content_text" align="left" colspan="3">
		<font class="main_header_text"> Integrating an AI bot with FlashChat</font><br>
		<p>Please wait while the knowledge bases are loaded. This procedure can take a few minutes.
		</td>
	</tr>
	<tr bgcolor="#ECECEC">
		<td class="main_content_text" align="center" colspan="3">
			<iframe application="Loader" title="Loader" scrolling="yes" frameborder="0" marginwidth="0" hspace="0" name="Loader"
	    		align="left" id="Loader" src="../bot/programe/src/botinst/botloader.php"  width="100%" height="600">
	  		</iframe>
		</td>
	</tr>
	</table>
{/if}
<br><br>
<p align="right" class="main_header_text">&copy;<a href="http://tufat.com/" target="_blank">TUFaT.com</a> </p>
{include file="$admingentemplates/admin_bottom.tpl"}