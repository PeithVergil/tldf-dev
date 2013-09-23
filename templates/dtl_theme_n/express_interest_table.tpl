{include file="$gentemplates/index_top.tpl"}
{strip}
{if $form.congrat}
	<div>
		{literal}
		<script type="text/javascript">
			$(document).ready(function(){ create_popup(); });
		</script>
		{/literal}
		<div id="popupContact">
			<a id="popupContactClose" title="Close" href="index.php">x</a>
			<h1>{$lang.popup_congrat_title}</h1>
			<p id="contactArea">{$lang.express_interest.congrat_text}</p>
		</div>
		<div id="backgroundPopup"></div>
	</div>
{/if}
<div class="toc page-simple">
{if $form.err}
	<div class="error_msg">{$form.err}</div>
{/if}
{if $form.res}
	<div class="error_msg">{$form.res}</div>
{/if}
<div class="upgrade-member tcxf-ch-la">
	<div>
		<p style="text-align:center">"We Match Discerning & <br /> Professional Guys With Genuine,<br /> Eligible & Trustworthy Thai Ladies...<br /> Or Your Money Back"</p>
		<div class="callchat_icons2">
			<p style="padding-left:5px; padding-bottom:5px;">
				<b>Nathamon Madison</b><br />
				Owner Meet Me Now Bangkok<br /> & Thai Lady Dating Events&trade;
			</p>
		</div>
		<p>
			<a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/telephone.png" alt="Call Me" title="Call Me"></a> <a href="{$site_root}/contact.php"><img src="{$site_root}{$template_root}/images/Chat.png" alt="Chat With Me" title="Chat With Me" ></a>
		</p>
	</div>
	<div>
		<div class="_pleft20">
			<h2 class="hdr2e">{$lang.section.express_interest}</h2>
			<!-- begin main cell -->
			<div class="tcxf-ch-la">
				<div style="width:610px;">
					<p>&nbsp;Fill In The Form Today And Our Team Will Contact You Shortly.</p>
				</div>
				<div id="yellowbox">
					<div class="top">
						<div class="btm">
							<div class="mid">
								<div style="margin:0px 30px 0px 25px;text-align:left;"><br/>
									<p style="color:#FF0000;font-weight:bold; padding:0px;margin:0px; line-height:18px;">
										<img src="{$site_root}{$template_root}images/check-box2.gif" align="left" style="border:0px;vertical-align:middle;padding:0px;padding-bottom:60px; margin:0px;" title="ThaiLadyDatingEvents" alt="ThaiLadyDatingEvents" />
										YES! I&#8217;m Serious About Finding The Right Sort Of Partner In My Life.
										<br>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										Here&#8217;s My Expression Of Interest In Become A Thai Lady Dating Events&trade;
										<br>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										Platinum Plus Member.
										<br>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										I Give My Permission To Be Contacted By Phone To Discuss This
										<br>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										Application And For a Suitable Character Check To Be Performed.
									</p>
									<br />
									<div class="clear"></div>
									<p style="color:#000000;text-align:left;font-style:bold">The Best Time To Call Me Is:</p>
								</div>
								<form name="form1" method="post" >
									<div>
										<table cellpadding="3" cellspacing="3">
											<tr>
												<td align="right">Weekdays:</td>
												<td><input id="best_time_weekdays" type="text" name="best_time_weekdays" value="{$data.best_time_weekdays}" style="width:250px;"></td>
											</tr>
											<tr>
												<td align="right">Saturdays:</td>
												<td><input id="best_time_saturdays" type="text" name="best_time_saturdays" value="{$data.best_time_saturdays}" style="width:250px;"></td>
											</tr>
											<tr>
												<td align="right">Sundays:</td>
												<td><input id="best_time_sundays" type="text" name="best_time_sundays" value="{$data.best_time_sundays}" style="width:250px;"></td>
											</tr>
											<tr>
												<td align="right">First Name:</td>
												<td><input id="first_name" type="text" name="first_name" value="{$data.first_name}" style="width:250px;"></td>
											</tr>
											<tr>
												<td align="right">Last Name:</td>
												<td><input id="last_name" type="text" name="last_name" value="{$data.last_name}" style="width:250px;"></td>
											</tr>
											<tr>
												<td align="right">Email:</td>
												<td><input id="email" type="text" name="email" value="{$data.email}" style="width:250px;"></td>
											</tr>
											<tr>
												<td align="right">Date of Birth*:</td>
												<td><input id="date_birthday" type="text" name="date_birthday" value="{$data.date_birthday}" style="width:250px;"></td>
											</tr>
											<tr>
												<td align="right">Place of Birth*:</td>
												<td><input id="place_of_birth" type="text" name="place_of_birth" value="{$data.place_of_birth}" style="width:250px;"></td>
											</tr>
											<tr>
												<td align="right">Identification Number*:</td>
												<td><input id="identification_number" type="text" name="identification_number" value="{$data.identification_number}" style="width:250px;"></td>
											</tr>
											<tr>
												<td align="right">Identification Type*:</td>
												<td><input id="identification_type" type="text" name="identification_type" value="{$data.identification_type}" style="width:250px;"></td>
											</tr>
											<tr>
												<td align="right">Home Phone*:</td>
												<td>
													[Country Code]-[Area Code]-[Your Number]<br />
													<input type="text" id="home_phone_cc" name="home_phone_cc" value='{$data.home_phone_cc}' style="width:30px" maxlength="3" />
													-
													<input type="text" id="home_phone_ac" name="home_phone_ac" value='{$data.home_phone_ac}' style="width:30px" maxlength="3"  />
													-
													<input type="text" id="home_phone_ph" name="home_phone_ph" value='{$data.home_phone_ph}' style="width:100px" size="10" />
												</td>
											</tr>
											<tr>
												<td align="right">Mobile Phone*:</td>
												<td>
													<input type="text" id="mobile_phone" name="mobile_phone" value='{$data.mobile_phone}' style="width:250px" size="15" />
												</td>
											</tr>
											
											
											<tr>
												<td align="right">Country:</td>
												<td>
													<select id="country" name="country" style="width:260px;">
														<option selected="selected" class="multiChoice" value="">Pick a country</option>
														<option class="multiChoice" value="USA">USA</option>
														<option class="multiChoice" value="Afghanistan">Afghanistan</option>
														<option class="multiChoice" value="Albania">Albania</option>
														<option class="multiChoice" value="Algeria">Algeria</option>
														<option class="multiChoice" value="Andorra">Andorra</option>
														<option class="multiChoice" value="Angola">Angola</option>
														<option class="multiChoice" value="Anguilla">Anguilla</option>
														<option class="multiChoice" value="Antigua Barbuda">Antigua Barbuda</option>
														<option class="multiChoice" value="Argentina">Argentina</option>
														<option class="multiChoice" value="Aruba">Aruba</option>
														<option class="multiChoice" value="Australia">Australia</option>
														<option class="multiChoice" value="Austria">Austria</option>
														<option class="multiChoice" value="Azerbaijan">Azerbaijan</option>
														<option class="multiChoice" value="Bahamas">Bahamas</option>
														<option class="multiChoice" value="Bahrain">Bahrain</option>
														<option class="multiChoice" value="Bangladesh">Bangladesh</option>
														<option class="multiChoice" value="Barbados">Barbados</option>
														<option class="multiChoice" value="Belarus">Belarus</option>
														<option class="multiChoice" value="Belgium">Belgium</option>
														<option class="multiChoice" value="Belize">Belize</option>
														<option class="multiChoice" value="Benin">Benin</option>
														<option class="multiChoice" value="Bermuda">Bermuda</option>
														<option class="multiChoice" value="Bhutan">Bhutan</option>
														<option class="multiChoice" value="Bolivia">Bolivia</option>
														<option class="multiChoice" value="Bosnia-Herzegovina">Bosnia-Herzegovina</option>
														<option class="multiChoice" value="Botswana">Botswana</option>
														<option class="multiChoice" value="Brazil">Brazil</option>
														<option class="multiChoice" value="British Virgin Islands">British Virgin Islands</option>
														<option class="multiChoice" value="Brunei">Brunei</option>
														<option class="multiChoice" value="Bulgaria">Bulgaria</option>
														<option class="multiChoice" value="Burkina">Burkina</option>
														<option class="multiChoice" value="Burkina Faso">Burkina Faso</option>
														<option class="multiChoice" value="Burma">Burma</option>
														<option class="multiChoice" value="Burundi">Burundi</option>
														<option class="multiChoice" value="Cambodia">Cambodia</option>
														<option class="multiChoice" value="Cameroon">Cameroon</option>
														<option class="multiChoice" value="Canada">Canada</option>
														<option class="multiChoice" value="Cape Verde">Cape Verde</option>
														<option class="multiChoice" value="Cayman Islands">Cayman Islands</option>
														<option class="multiChoice" value="Chad">Chad</option>
														<option class="multiChoice" value="Chile">Chile</option>
														<option class="multiChoice" value="China">China</option>
														<option class="multiChoice" value="Colombia">Colombia</option>
														<option class="multiChoice" value="Comoros">Comoros</option>
														<option class="multiChoice" value="Congo">Congo</option>
														<option class="multiChoice" value="Costa Rica">Costa Rica</option>
														<option class="multiChoice" value="Cote D'Ivoire">Cote D'Ivoire</option>
														<option class="multiChoice" value="Croatia">Croatia</option>
														<option class="multiChoice" value="Cuba">Cuba</option>
														<option class="multiChoice" value="Cyprus">Cyprus</option>
														<option class="multiChoice" value="Czech Republic">Czech Republic</option>
														<option class="multiChoice" value="Denmark">Denmark</option>
														<option class="multiChoice" value="Djibouti">Djibouti</option>
														<option class="multiChoice" value="Dominica">Dominica</option>
														<option class="multiChoice" value="Dominican Republic">Dominican Republic</option>
														<option class="multiChoice" value="Ecuador">Ecuador</option>
														<option class="multiChoice" value="Egypt">Egypt</option>
														<option class="multiChoice" value="El Salvador">El Salvador</option>
														<option class="multiChoice" value="Equatorial Guinea">Equatorial Guinea</option>
														<option class="multiChoice" value="Eritrea">Eritrea</option>
														<option class="multiChoice" value="Estonia">Estonia</option>
														<option class="multiChoice" value="Ethiopia">Ethiopia</option>
														<option class="multiChoice" value="Falkland Islands">Falkland Islands</option>
														<option class="multiChoice" value="Faroe Islands">Faroe Islands</option>
														<option class="multiChoice" value="Fiji">Fiji</option>
														<option class="multiChoice" value="Finland">Finland</option>
														<option class="multiChoice" value="France">France</option>
														<option class="multiChoice" value="French Polynesia">French Polynesia</option>
														<option class="multiChoice" value="Gabon">Gabon</option>
														<option class="multiChoice" value="Germany">Germany</option>
														<option class="multiChoice" value="Ghana">Ghana</option>
														<option class="multiChoice" value="Gibraltar">Gibraltar</option>
														<option class="multiChoice" value="Greece">Greece</option>
														<option class="multiChoice" value="Grenada">Grenada</option>
														<option class="multiChoice" value="Guam">Guam</option>
														<option class="multiChoice" value="Guatemala">Guatemala</option>
														<option class="multiChoice" value="Guinea">Guinea</option>
														<option class="multiChoice" value="Guinea-Bissau">Guinea-Bissau</option>
														<option class="multiChoice" value="Guyana">Guyana</option>
														<option class="multiChoice" value="Haiti">Haiti</option>
														<option class="multiChoice" value="Honduras">Honduras</option>
														<option class="multiChoice" value="Hong Kong">Hong Kong</option>
														<option class="multiChoice" value="Hungary">Hungary</option>
														<option class="multiChoice" value="Iceland">Iceland</option>
														<option class="multiChoice" value="India">India</option>
														<option class="multiChoice" value="Indonesia">Indonesia</option>
														<option class="multiChoice" value="Iran">Iran</option>
														<option class="multiChoice" value="Iraq">Iraq</option>
														<option class="multiChoice" value="Ireland">Ireland</option>
														<option class="multiChoice" value="Israel">Israel</option>
														<option class="multiChoice" value="Italy">Italy</option>
														<option class="multiChoice" value="Jamaica">Jamaica</option>
														<option class="multiChoice" value="Japan">Japan</option>
														<option class="multiChoice" value="Jordan">Jordan</option>
														<option class="multiChoice" value="Kazakstan">Kazakstan</option>
														<option class="multiChoice" value="Kenya">Kenya</option>
														<option class="multiChoice" value="Kiribati">Kiribati</option>
														<option class="multiChoice" value="Kuwait">Kuwait</option>
														<option class="multiChoice" value="Kyrgyzstan">Kyrgyzstan</option>
														<option class="multiChoice" value="Laos">Laos</option>
														<option class="multiChoice" value="Latvia">Latvia</option>
														<option class="multiChoice" value="Lebanon">Lebanon</option>
														<option class="multiChoice" value="Lesotho">Lesotho</option>
														<option class="multiChoice" value="Liberia">Liberia</option>
														<option class="multiChoice" value="Libya">Libya</option>
														<option class="multiChoice" value="Liechtenstein">Liechtenstein</option>
														<option class="multiChoice" value="Lithuania">Lithuania</option>
														<option class="multiChoice" value="Luxembourg">Luxembourg</option>
														<option class="multiChoice" value="Macedonia">Macedonia</option>
														<option class="multiChoice" value="Madagascar">Madagascar</option>
														<option class="multiChoice" value="Malawi">Malawi</option>
														<option class="multiChoice" value="Malaysia">Malaysia</option>
														<option class="multiChoice" value="Maldives">Maldives</option>
														<option class="multiChoice" value="Mali">Mali</option>
														<option class="multiChoice" value="Malta">Malta</option>
														<option class="multiChoice" value="Marshall Islands">Marshall Islands</option>
														<option class="multiChoice" value="Mauritania">Mauritania</option>
														<option class="multiChoice" value="Mauritius">Mauritius</option>
														<option class="multiChoice" value="Mexico">Mexico</option>
														<option class="multiChoice" value="Micronesia">Micronesia</option>
														<option class="multiChoice" value="Monaco">Monaco</option>
														<option class="multiChoice" value="Mongolia">Mongolia</option>
														<option class="multiChoice" value="Montenegro">Montenegro</option>
														<option class="multiChoice" value="Montserrat">Montserrat</option>
														<option class="multiChoice" value="Morocco">Morocco</option>
														<option class="multiChoice" value="Mozambique">Mozambique</option>
														<option class="multiChoice" value="Namibia">Namibia</option>
														<option class="multiChoice" value="Nauru">Nauru</option>
														<option class="multiChoice" value="Nepal">Nepal</option>
														<option class="multiChoice" value="Netherlands">Netherlands</option>
														<option class="multiChoice" value="Netherlands Antilles">Netherlands Antilles</option>
														<option class="multiChoice" value="New Zealand">New Zealand</option>
														<option class="multiChoice" value="Nicaragua">Nicaragua</option>
														<option class="multiChoice" value="Niger">Niger</option>
														<option class="multiChoice" value="Nigeria">Nigeria</option>
														<option class="multiChoice" value="North Korea">North Korea</option>
														<option class="multiChoice" value="Northern Mariana Islands">Northern Mariana Islands</option>
														<option class="multiChoice" value="Norway">Norway</option>
														<option class="multiChoice" value="Oman">Oman</option>
														<option class="multiChoice" value="Pakistan">Pakistan</option>
														<option class="multiChoice" value="Palua">Palua</option>
														<option class="multiChoice" value="Panama">Panama</option>
														<option class="multiChoice" value="Papua New Guinea">Papua New Guinea</option>
														<option class="multiChoice" value="Paraguay">Paraguay</option>
														<option class="multiChoice" value="Peru">Peru</option>
														<option class="multiChoice" value="Philippines">Philippines</option>
														<option class="multiChoice" value="Pitcairn Island">Pitcairn Island</option>
														<option class="multiChoice" value="Poland">Poland</option>
														<option class="multiChoice" value="Portugal">Portugal</option>
														<option class="multiChoice" value="Puerto Rico">Puerto Rico</option>
														<option class="multiChoice" value="Qatar">Qatar</option>
														<option class="multiChoice" value="Romania">Romania</option>
														<option class="multiChoice" value="Russia">Russia</option>
														<option class="multiChoice" value="Rwanda">Rwanda</option>
														<option class="multiChoice" value="Samoa">Samoa</option>
														<option class="multiChoice" value="San Marino">San Marino</option>
														<option class="multiChoice" value="Sao Tome and Principe">Sao Tome and Principe</option>
														<option class="multiChoice" value="Saudi Arabia">Saudi Arabia</option>
														<option class="multiChoice" value="Senegal">Senegal</option>
														<option class="multiChoice" value="Serbia">Serbia</option>
														<option class="multiChoice" value="Seychelles">Seychelles</option>
														<option class="multiChoice" value="Sierra Leone">Sierra Leone</option>
														<option class="multiChoice" value="Singapore">Singapore</option>
														<option class="multiChoice" value="Slovakia">Slovakia</option>
														<option class="multiChoice" value="Slovenia">Slovenia</option>
														<option class="multiChoice" value="Solomon Islands">Solomon Islands</option>
														<option class="multiChoice" value="Somalia">Somalia</option>
														<option class="multiChoice" value="South Africa">South Africa</option>
														<option class="multiChoice" value="South Georgia">South Georgia</option>
														<option class="multiChoice" value="South Korea">South Korea</option>
														<option class="multiChoice" value="Spain">Spain</option>
														<option class="multiChoice" value="Sri Lanka">Sri Lanka</option>
														<option class="multiChoice" value="St Helena">St Helena</option>
														<option class="multiChoice" value="St Kitts Nevis">St Kitts Nevis</option>
														<option class="multiChoice" value="St Lucia">St Lucia</option>
														<option class="multiChoice" value="St Vincent The Grenadines">St Vincent The Grenadines</option>
														<option class="multiChoice" value="Sudan">Sudan</option>
														<option class="multiChoice" value="Suriname">Suriname</option>
														<option class="multiChoice" value="Swaziland">Swaziland</option>
														<option class="multiChoice" value="Sweden">Sweden</option>
														<option class="multiChoice" value="Switzerland">Switzerland</option>
														<option class="multiChoice" value="Syria">Syria</option>
														<option class="multiChoice" value="Taiwan">Taiwan</option>
														<option class="multiChoice" value="Tanzania">Tanzania</option>
														<option class="multiChoice" value="Thailand">Thailand</option>
														<option class="multiChoice" value="The Gambia">The Gambia</option>
														<option class="multiChoice" value="Togo">Togo</option>
														<option class="multiChoice" value="Tonga">Tonga</option>
														<option class="multiChoice" value="Trinidad and Tobago">Trinidad and Tobago</option>
														<option class="multiChoice" value="Tunisia">Tunisia</option>
														<option class="multiChoice" value="Turkey">Turkey</option>
														<option class="multiChoice" value="Turks and Caicos Islands">Turks and Caicos Islands</option>
														<option class="multiChoice" value="Tuvalu">Tuvalu</option>
														<option class="multiChoice" value="Uganda">Uganda</option>
														<option class="multiChoice" value="Ukraine">Ukraine</option>
														<option class="multiChoice" value="United Arab Emirates">United Arab Emirates</option>
														<option class="multiChoice" value="United Kingdom">United Kingdom</option>
														<option class="multiChoice" value="Uruguay">Uruguay</option>
														<option class="multiChoice" value="Uzbekistan">Uzbekistan</option>
														<option class="multiChoice" value="Vanuatu">Vanuatu</option>
														<option class="multiChoice" value="Vatican City State">Vatican City State</option>
														<option class="multiChoice" value="Venezuela">Venezuela</option>
														<option class="multiChoice" value="Vietnam">Vietnam</option>
														<option class="multiChoice" value="West Indies">West Indies</option>
														<option class="multiChoice" value="Western Samoa">Western Samoa</option>
														<option class="multiChoice" value="Yemen">Yemen</option>
														<option class="multiChoice" value="Zambia">Zambia</option>
														<option class="multiChoice" value="Zimbabwe">Zimbabwe</option>
													</select>
												</td>
											</tr>
											<tr>
												<td align="right">Region/State:</td>
												<td><input type="text" id="region" name="region" value='{$data.region}' style="width:250px;"></td>
											</tr>
											<tr>
												<td align="right">City*:</td>
												<td><input type="text" id="city" name="city" value='{$data.city}' style="width:250px;"></td>
											</tr>
											<tr>
												<td align="right">Zip Code*:</td>
												<td><input type="text" id="zip_code" name="zip_code" value='{$data.zip_code}' style="width:250px;"></td>
											</tr>
											<tr>
												<td align="right">Address (Line 1)*:</td>
												<td><input type="text" id="address_1" name="address_1" value='{$data.address_1}' style="width:250px;"></td>
											</tr>
											<tr>
												<td align="right">Address (Line 2):</td>
												<td><input type="text" id="address_2" name="address_2" value='{$data.address_2}' style="width:250px;"></td>
											</tr>
											<tr>
												<td align="right">Address (Line 3):</td>
												<td><input type="text" id="address_3" name="address_3" value='{$data.address_3}' style="width:250px;"></td>
											</tr>
											<tr>
												<td align="right">My Question or Comments:</td>
												<td><textarea id="comments" name="comments" style="height:120px;width:254px">{$data.comments}</textarea></td>
											</tr>
											<tr>
												<td align="right">&nbsp;</td>
												<td><input type="submit" name="btnSubmit" class="normal-btn" value="Submit" /></td>
											</tr>
										</table>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- end main cell -->
		</div>
	</div>
</div>
{/strip}
{include file="$gentemplates/index_bottom.tpl"}