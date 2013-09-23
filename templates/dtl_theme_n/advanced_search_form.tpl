{include file="$gentemplates/index_top.tpl"}
{strip}
<div class="toc page-simple">
	{if $id_err == 1}<div class="error_msg">{$lang.err.no_searchname}</div>{/if}
	{if $id_err == 2}<div class="error_msg">{$lang.err.exist_searchname}</div>{/if}
	{if $err}<div class="error_msg">{$err}</div>{/if}
	<form action="{$form.search_action}" method="get" name="search_form" style="margin:0px;">
		<input type="hidden" name="sel" value="search" />
		<div>
			<div class="hdr2e">
				<label title="ค้นหาแบบละเอียด">{$lang.section.a_search}</label>
			</div>
			<div class="det-14-2">
				<label title="เครื่องมือในการช่วยค้นหาสมาชิกชายต่างชาติจำกัดขอบเขตในการค้นหาข้อมูล<br> จะมีส่วนช่วยเชื่อมต่อกับชายต่างชาติตรงกับความต้องการของคุณ<br> เลือกคุณสมบัติได้มากกว่าหนึ่งข้อโดยกดที่ปุ่ม CTRL ค้างไว้<br> คลื๊กเลือกลักษณะชายต่างชาติในแบบที่คุณต้องการ">
					{$lang.advanced_search.help_tip.main_text}
				</label>
			</div>
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td class="text_head" style="padding:10px 0px;">
						{$lang.home_page.im} : {if $data.gender_1 == 1}{$gender[0].name_search}{elseif $data.gender_1 == 2}{$gender[1].name_search}{/if}
						<input type="hidden" name="gender_1" value="{$data.gender_1}" />
					</td>
				</tr>
				<tr>
					<td width="25%" valign="top">
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td width="25%" class="text_head">{$lang.home_page.seeking_a} :&nbsp;</td>
							</tr>
							<tr>
								<td class="text_head" style="padding-top:3px;">
									{if ADVANCED_SEARCH_GENDER}
										<select name="gender_2" class="index_select" style="width:115px;">
											{foreach item=item from=$gender}
												<option value="{$item.id}" {if $item.id != $data.gender_1}selected="selected"{/if}>{$item.name_search}</option>
											{/foreach}
										</select>
									{else}
										{if $data.gender_1 neq 1}{$gender[0].name}{elseif $data.gender_1 neq 2}{$gender[1].name}{/if}
										<input type="hidden" name="gender_2" value="{if $data.gender_1 neq 1}1{elseif $data.gender_1 neq 2}2{/if}" />
									{/if}
								</td>
							</tr>
							{if ADVANCED_SEARCH_COUPLE}
								<tr>
									<td style="padding-top:2px;">
										<table cellpadding="0" cellspacing="0">
											<tr>
												<td>
													<input type="radio" name="couple_2" value="0" {if !$data.couple_2}checked="checked"{/if} />
												</td>
												<td class="text" style="padding-right:15px;">{$lang.users.single}</td>
												<td>
													<input type="radio" name="couple_2" value="1" {if $data.couple_2}checked="checked"{/if} />
												</td>
												<td class="text" style="padding-right:15px;">{$lang.users.couple}</td>
											</tr>
										</table>
									</td>
								</tr>
							{/if}
							{if ADVANCED_SEARCH_RELATIONSHIP}
								<tr>
									<td style="padding-top:15px;" class="text_head">{$lang.home_page.looking_for}</td>
								</tr>
								<tr>
									<td style="padding-top:2px;">
										<select class="index_select" name="relation[]" {if $data.root eq 1}disabled="disabled"{/if} multiple="multiple" style="width:230px;">
											<option value="0" {if $relation.sel_all}selected{/if}>{$button.all}</option>
											{html_options values=$relation.opt_value selected=$relation.opt_sel output=$relation.opt_name}
										</select>
									</td>
								</tr>
							{else}
								<tr>
									<td><input type="hidden" name="relation[]" value="2" /></td>
								</tr>
							{/if}
						</table>
					</td>
					<td width="25%" valign="top">
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td class="text_head">{$lang.home_page.between_the_ages_of}</td>
							</tr>
							<tr>
								<td style="padding-top:3px;">
									<table cellpadding="0" cellspacing="0">
										<tr>
											<td>
												<select class="index_select" style="width:50px" name="age_min">
													{foreach item=item from=$age_min}
														<option value="{$item}" {if $item == $data.age_min}selected="selected"{/if}>{$item}</option>
													{/foreach}
												</select>
											</td>
											<td class="text">&nbsp;{$lang.home_page.and}&nbsp;</td>
											<td>
												<select class="index_select" style="width:50px" name="age_max">
													{foreach item=item from=$age_max}
														<option value="{$item}" {if $item == $data.age_max}selected="selected"{/if}>{$item}</option>
													{/foreach}
												</select>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							{if ADVANCED_SEARCH_COUNTRY}
								<tr>
									<td style="padding-top:10px;" class="text_head">{$header_perfect.country}</td>
								</tr>
								<tr>
									<td style="padding-top:2px;">
										<select class="index_select" name="id_country" style="width:150px" onchange="SelectRegion('as', this.value, document.getElementById('region_div'), document.getElementById('city_div'));">
											<option value="0">{$button.all}</option>
											{foreach item=item from=$country_match}
												<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
											{/foreach}
										</select>
									</td>
								</tr>
							{/if}
							{if ADVANCED_SEARCH_REGION}
								<tr>
									<td style="padding-top:3px;" class="text_head">{$header_perfect.region}</td>
								</tr>
								<tr>
									<td style="padding-top:2px;">
										<div id="region_div">
											{if isset($region_match)}
												<select class="index_select" name="id_region" style="width:150px" onchange="SelectCity('as', this.value, document.getElementById('city_div'));">
													<option value="0">{$button.all}</option>
													{foreach item=item from=$region_match}
														<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
													{/foreach}
												</select>
											{else}
												<select class="index_select" name="id_region" style="width:150px;">
													<option value="0">{$button.all}</option>
												</select>
											{/if}
										</div>
									</td>
								</tr>
							{/if}
							{if ADVANCED_SEARCH_CITY}
								<tr>
									<td style="padding-top:3px;" class="text_head">{$header_perfect.city}</td>
								</tr>
								<tr>
									<td style="padding-top:2px;">
										<div id="city_div">
											{if isset($city_match)}
												<select class="index_select" name="id_city" style="width:150px">
													<option value="0">{$button.all}</option>
													{foreach item=item from=$city_match}
														<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
													{/foreach}
												</select>
											{else}
												<select class="index_select" name="id_city" style="width:150px;">
													<option value="0">{$button.all}</option>
												</select>
											{/if}
										</div>
									</td>
								</tr>
							{/if}
							{if ADVANCED_SEARCH_DISTANCE}
								<tr>
									<td style="padding-top:10px;">
										<table cellpadding="0" cellspacing="0">
											<tr>
												<td><font class="text_head">{$header_s.within}</font></td>
												<td style="padding-left:5px;">
													<input type="checkbox" name="within" value="1" {if $data.within eq 1}checked="checked"{/if} onclick="if (distance.disabled) distance.disabled = false; else distance.disabled = true;" />
												</td>
												<td style="padding-left:3px;">
													<select class="index_select" id="distance" name="distance" {if $data.within eq 0}disabled="disabled"{/if}>
														{foreach item=item from=$distances}
															<option value="{$item.id}" {if $data.distance == $item.id}selected="selected"{/if}>{$item.name} {$item.type}</option>
														{/foreach}
													</select>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							{/if}
						</table>
					</td>
					<td width="25%" valign="top" style="padding-top:10px;">
						<table cellpadding="0" cellspacing="3">
							<tr>
								{if ADVANCED_SEARCH_WITH_PHOTO_SEARCH}
									<td><input type="checkbox" name="foto_only" value="1" {if $data.foto_only}checked="checked"{/if} /></td>
									<td class="text_head">{$header_s.foto}</td>
									<td>&nbsp;</td>
								{/if}
								<td><input type="checkbox" name="online_only" value="1" {if $data.online_only}checked="checked"{/if} /></td>
								<td class="text_head">{$header_s.online}</td>
							</tr>
						</table>
					</td>
					<td width="25%" valign="top">
						<p class="basic-btn_here">
							<b>&nbsp;</b><span>
							<input type="button" onclick="document.search_form.submit();" value="{$button.search}">
							</span>
						</p>
					</td>
				</tr>
			</table>
			<table cellpadding="0" cellspacing="0" width="100%" style="margin:12px 0px;">
				<tr>
					<td valign="top" height="1" bgcolor="#{$css_color.home_search}"></td>
				</tr>
			</table>
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr>
					<td width="50%" valign="top" style="padding-top:5px;">
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td class="text_head">{$header_perfect.weight}</td>
							</tr>
							<tr>
								<td style="padding-top:2px;">
									<select name="id_weight" style="width:200px" class="index_select">
										<option value="0">{$lang.home_page.any_weight}</option>
										{foreach item=item from=$weight}
											<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
										{/foreach}
									</select>
								</td>
							</tr>
						</table>
					</td>
					<td width="50%" valign="top" style="padding-top:5px;">
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td class="text_head">{$header_perfect.height}</td>
							</tr>
							<tr>
								<td style="padding-top:2px;">
									<select name="id_height" style="width:200px" class="index_select">
										<option value="0">{$lang.home_page.any_height}</option>
										{foreach item=item from=$height}
											<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
										{/foreach}
									</select>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td width="50%" valign="top" style="padding-top:10px;">
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td class="text_head">{$header_perfect.nationality}</td>
							</tr>
							<tr>
								<td style="padding-top:2px;">
									<select class="index_select" name="id_nation[]" multiple="multiple" style="width:200px;">
										<option value="0" {if $default.id_nation}selected{/if}>{$button.all}</option>
										{foreach item=item from=$nation_match}
											<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
										{/foreach}
									</select>
								</td>
							</tr>
						</table>
					</td>
					<td width="50%" style="padding-top:10px;">
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td class="text_head">{$header_perfect.language}</td>
							</tr>
							<tr>
								<td style="padding-top:2px;">
									<select class="index_select" name="id_lang[]" multiple style="width:200px;">
										<option value="0" {if $default.id_lang}selected{/if}>{$button.all}</option>
										{foreach item=item from=$lang_sel_match}
											<option value="{$item.id}" {if $item.sel}selected="selected"{/if}>{$item.value}</option>
										{/foreach}
									</select>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<table cellpadding="0" cellspacing="0" width="100%" style="margin:12px 0px 5px 0px;">
				<tr>
					<td valign="top" height="1" bgcolor="#{$css_color.home_search}"></td>
				</tr>
			</table>
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				{foreach item=item from=$info}
					<tr>
						<td width="25%" valign="top" style="padding-top:10px;">
							<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td class="text_head">
										{$item.name_1|escape}<input type="hidden" name="spr[{$item.num_1}]" value="{$item.id_1}" />
									</td>
								</tr>
								<tr>
									<td style="padding-top:2px;">
										<select class="index_select" id="info{$item.num_1}" name="info[{$item.num_1}][]" multiple="multiple" style="width:167px;">
											<option value="0" {if $item.sel_all_1}selected="selected"{/if}>{$button.all}</option>
											{html_options values=$item.opt_value_1 selected=$item.opt_sel_1 output=$item.opt_name_1}
										</select>
									</td>
								</tr>
							</table>
						</td>
						<td width="25%" valign="top" style="padding-top:10px;">
							<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td class="text_head">
										{if $item.name_2}
											{$item.name_2|escape}<input type="hidden" name="spr[{$item.num_2}]" value="{$item.id_2}" />
										{/if}
									</td>
								</tr>
								<tr>
									<td style="padding-top:2px;">
										{if $item.name_2}
											<select class="index_select" id="info{$item.num_2}" name="info[{$item.num_2}][]" multiple="multiple" style="width:167px;">
												<option value="0" {if $item.sel_all_2}selected="selected"{/if}>{$button.all}</option>
												{html_options values=$item.opt_value_2 selected=$item.opt_sel_2 output=$item.opt_name_2}
											</select>
										{/if}
									</td>
								</tr>
							</table>
						</td>
						<td width="25%" valign="top" style="padding-top:10px;">
							<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td class="text_head">
										{if $item.name_3}
											{$item.name_3|escape}<input type="hidden" name="spr[{$item.num_3}]" value="{$item.id_3}" />
										{/if}
									</td>
								</tr>
								<tr>
									<td style="padding-top:2px;">
										{if $item.name_3}
											<select class="index_select" id="info{$item.num_3}" name="info[{$item.num_3}][]" multiple="multiple" style="width:167px;">
												<option value="0" {if $item.sel_all_3}selected="selected"{/if}>{$button.all}</option>
												{html_options values=$item.opt_value_3 selected=$item.opt_sel_3 output=$item.opt_name_3}
											</select>
										{/if}
									</td>
								</tr>
							</table>
						</td>
						<td width="25%" valign="top" style="padding-top:10px;">
							<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td class="text_head">
										{if $item.name_4}
											{$item.name_4|escape}<input type="hidden" name="spr[{$item.num_4}]" value="{$item.id_4}">
										{/if}
									</td>
								</tr>
								<tr>
									<td style="padding-top:2px;">
										{if $item.name_4}
											<select class="index_select" id="info{$item.num_4}" name="info[{$item.num_4}][]" multiple="multiple" style="width:167px;">
												<option value="0" {if $item.sel_all_4}selected="selected"{/if}>{$button.all}</option>
												{html_options values=$item.opt_value_4 selected=$item.opt_sel_4 output=$item.opt_name_4}
											</select>
										{/if}
									</td>
								</tr>
							</table>
						</td>
					</tr>
				{/foreach}
			</table>
			<div style="padding:20px 0 5px 0;">
				<p class="basic-btn_here">
					<b>&nbsp;</b><span>
					<input type="button" onclick="document.search_form.submit();" value="{$button.search}">
					</span>
				</p>
			</div>
		</div>
		<!-- save\load form-->
		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td align="left" style="width:50%;" valign="top">
					<div class="content" style="padding:20px;">
						<div class="hdr2">{$lang.section.a_searchsave_header}</div>
						<div style="padding:8px 0px;">
							<table border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td valign="middle" align="left">
										<input type="text" name="search_name" value="{if $data.search_name==''}MyCustomSearch{/if}{$data.search_name}" style="width:165px" maxlength="100">
									</td>
									<td valign="middle" style="padding-left:5px;">
										<p class="basic-btn_here">
											<b>&nbsp;</b><span>
											<input type="button" onclick="document.search_form.sel.value='save';document.search_form.submit();" value="{$button.save}">
											</span>
										</p>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</td>
				<td align="left" valign="top">
					<div class="content" style="margin-top:10px; padding-bottom:10px;">
						<div class="hdr2">{$lang.section.a_searchload_header}</div>
						<div style="padding:8px 0px;">
							<table border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td align="left" valign="middle">
										<select class="index_select" name="load_id" style="width:165px;">
											<option value="0">{$lang.home_page.select_default}</option>
											{html_options values=$load_id output=$load_name}
										</select>
									</td>
									<td valign="middle" style="padding-left:10px;">			
										<p class="basic-btn_here">
											<b>&nbsp;</b><span>
											<input type="button" onclick="document.search_form.sel.value='load';document.search_form.submit();" value="{$button.load}">
											</span>
										</p>
									</td>
									<td valign="middle" style="padding-left:5px;">
										<p class="basic-btn_here">
											<b>&nbsp;</b><span>
											<input type="button" onclick="document.search_form.submit(document.search_form.sel.value='delete')" value="{$button.delete}">
											</span>
										</p>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</form>
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}