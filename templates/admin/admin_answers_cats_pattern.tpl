				
				<table cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="2">
						<select id="cat{$level}" name="categories" onchange="javascript: initButtons({$level});" size="2" style="width:250px; height:100px;">
							{foreach name=category from=$categories item=item}
							<option value="{$item.id}" onclick="javascript: initButtons({$level});" >{$item.name}</option>
							{/foreach}
						</select>
					</td>
				</tr>
				<tr>
					<td align="right">
						<table cellpadding="0" cellspacing="0" class="answ_cats_butts">
						<tr>
							<td style="padding:0px;"><input id="add_text{$level}" type="text" style="display:none;" class="input_text"/></td>
							<td align="right">
								<input id="add_button{$level}" type="button" value="{$lang.button.add}" onclick="javadcript: showField('add',{$level});"/>
								<input id="save_added_button{$level}" type="button" value="{$lang.button.save}" onclick="javascript: addCat({$level});" style="display:none;"/>
							</td>
						</tr>
						<tr>
							<td style="padding:0px;"><input id="edit_text{$level}" type="text" value="" style="display:none;" class="input_text"/></td>
							<td align="right">
								<input id="edit_button{$level}" type="button" value="{$lang.button.edit}" onclick="javascript: showField('edit',{$level});" style="display:none;"/>
								<input id="save_edited_button{$level}" type="button" value="{$lang.button.save}" onclick="javascript: saveCat({$level});" style="display:none;"/>
							</td>
						</tr>
						<tr>
							<td></td>
							<td align="right">
								<input id="del_button{$level}" type="button" value="{$lang.button.delete}" onclick="javascript: delCat({$level});" style="display:none;"/>
							</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
				<!--/category level {$level} -->