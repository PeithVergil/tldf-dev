{include file="$admingentemplates/admin_top.tpl"}
	<font class=red_header>{$header.razdel_name}</font><font class=red_sub_header>&nbsp;|&nbsp;{$header.base_editform}</font><br><br><br>
        <div align="center">


		<TABLE WIDTH=500 BORDER=0 CELLSPACING=0 CELLPADDING=0>
		<TR> 
			<TD VALIGN=TOP STYLE="border: 1px solid #919B9C;">
			<TABLE WIDTH=100% HEIGHT=100% BORDER=0 CELLSPACING=1 CELLPADDING=0>
				<TR> 
				<FORM NAME=skb METHOD=POST ACTION="{$form.action}">
				<TD class="main_content_text" VALIGN=TOP BGCOLOR=#F4F3EE STYLE="FILTER: progid:DXImageTransform.Microsoft.Gradient(gradientType=0,startColorStr=#FCFBFE,endColorStr=#F4F3EE); padding: 8px 8px;"> 
					<FIELDSET>
					<LEGEND  class="main_content_text">{$header.base_title}&nbsp;</LEGEND>
					<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=2>
					<TR><TD class="main_content_text" COLSPAN=2><DIV ID=logarea STYLE="width: 100%; height: 140px; border: 1px solid #7F9DB9; padding: 3px; overflow: auto;"></DIV></TD></TR>
					<TR><TD class="main_content_text" WIDTH=31%>{$header.table_status}:</TD><TD WIDTH=69%><TABLE WIDTH=100% BORDER=1 CELLPADDING=0 CELLSPACING=0>
					<TR><TD  class="main_content_text" BGCOLOR=#FFFFFF><TABLE WIDTH=1 BORDER=0 CELLPADDING=0 CELLSPACING=0 BGCOLOR=#5555CC ID=st_tab 
					STYLE="FILTER: progid:DXImageTransform.Microsoft.Gradient(gradientType=0,startColorStr=#B7CEF2,endColorStr=#0045B5);
					border-right: 1px solid #AAAAAA"><TR><TD HEIGHT=12></TD></TR></TABLE></TD></TR></TABLE></TD></TR>
					<TR><TD  class="main_content_text">{$header.base_status}:</TD><TD><TABLE WIDTH=100% BORDER=1 CELLSPACING=0 CELLPADDING=0>
					<TR><TD BGCOLOR=#FFFFFF  class="main_content_text"><TABLE WIDTH=1 BORDER=0 CELLPADDING=0 CELLSPACING=0 BGCOLOR=#00AA00 ID=so_tab
					STYLE="FILTER: progid:DXImageTransform.Microsoft.Gradient(gradientType=0,startColorStr=#CCFFCC,endColorStr=#00AA00);
					border-right: 1px solid #AAAAAA"><TR><TD HEIGHT=12></TD></TR></TABLE></TD>
					</TR></TABLE></TD></TR></TABLE>
					</FIELDSET>
					{literal}
					<SCRIPT>
					var WidthLocked = false;
					function s(st, so){
						document.getElementById('st_tab').width = st ? st + '%' : '1';
						document.getElementById('so_tab').width = so ? so + '%' : '1';
					}
					function l(str, color){
						switch(color){
							case 2: color = 'navy'; break;
							case 3: color = 'red'; break;
							default: color = 'black';
						}
						with(document.getElementById('logarea')){
							if (!WidthLocked){
								style.width = clientWidth;
								WidthLocked = true;
							}
							str = '<FONT COLOR=' + color + '>' + str + '</FONT>';
							innerHTML += innerHTML ? "<BR>\n" + str : str;
							scrollTop += 14;
						}
					}
					</SCRIPT>
					{/literal}
					<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=2>
					<TR>
					<TD class="main_content_text" STYLE='color: #CECECE' ID=timer></TD>
					<TD ALIGN=RIGHT class="main_content_text">
					<A ID=save HREF='' STYLE='display: none;'>{$header.get_file}</A> &nbsp; <INPUT ID=back TYPE=button VALUE='{$button.back}' class=button DISABLED onClick="window.close();opener.focus();">
					</TD>
					</TR>
					</TABLE>
				</TD>
				</FORM>
				</TR>
			</TABLE>
			</TD>
		</TR>
		</TABLE>
</div>
{include file="$admingentemplates/admin_bottom.tpl"}