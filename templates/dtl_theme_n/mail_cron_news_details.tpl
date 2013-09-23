{section name=n loop=$news}
	<p><b>{$news[n].date}</b> &nbsp; {$news[n].text} &nbsp; <a href="{$news[n].link_read}" class="mlink">{$header.news.read} &raquo;</a>&nbsp;</p>
{/section}