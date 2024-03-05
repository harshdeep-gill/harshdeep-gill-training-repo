@props( [
	'title'             => '',
	'subtitle'          => '',
	'form_id'           => '',
	'modal_id'          => '',
	'salesforce_object' => '',
	'thank_you_page'    => '',
] )

@php
	if ( empty( $form_id ) || empty( $modal_id ) ) {
		return;
	}
@endphp

<x-modal
	class="inquiry-form__modal"
	id="{{ $modal_id }}"
	title="{{ $title }}"
	subtitle="{{ $subtitle }}"
>
	<quark-inquiry-form-modal>
		<x-form id="{{ $form_id }}"
			salesforce_object="{{ $salesforce_object }}"
			thank_you_page="{{ $thank_you_page }}"
		>
			<div class="inquiry-form__content">
				@if( ! empty( $title ) || ! empty( $subtitle ) )
					<x-modal.header>
						@if ( ! empty( $title ) )
							<h3>{{ $title }}</h3>
						@endif
						@if ( ! empty( $subtitle ) )
							<p>{{ $subtitle }}</p>
						@endif
					</x-modal.header>
				@endif
				<x-modal.body>
					<x-form.row>
						<x-form.field :validation="[ 'required' ]">
							<x-form.input type="text" label="First Name" placeholder="Enter First Name" name="fields[FirstName__c]" />
						</x-form.field>
						<x-form.field :validation="[ 'required' ]">
							<x-form.input type="text" label="Last Name" placeholder="Enter Last Name" name="fields[LastName__c]" />
						</x-form.field>
					</x-form.row>
					<x-form.row>
						<x-form.field :validation="[ 'required', 'email' ]">
							<x-form.input type="email" label="Email" placeholder="Enter Email" name="fields[Email__c]" />
						</x-form.field>
						<x-form.field :validation="[ 'required' ]">
							<x-form.input type="tel" label="Phone Number" placeholder="eg. (123) 456 7890" name="fields[Phone__c]" />
						</x-form.field>
					</x-form.row>
					<x-form.row>
						<x-form.field :validation="[ 'required' ]" class="inquiry-form__country">
							<x-form.select label="Country" name="fields[Country_Code__c]">
								<option value="">- Select -</option>
								<option value="AU">Australia</option>
								<option value="CA">Canada</option>
								<option value="US">United States</option>
								<option value="GB">United Kingdom</option>
								<option value="AD">Andorra</option>
								<option value="AE">United Arab Emirates</option>
								<option value="AF">Afghanistan</option>
								<option value="AG">Antigua and Barbuda</option>
								<option value="AI">Anguilla</option>
								<option value="AL">Albania</option>
								<option value="AM">Armenia</option>
								<option value="AN">Netherlands Antilles</option>
								<option value="AO">Angola</option>
								<option value="AQ">Antarctica</option>
								<option value="AR">Argentina</option>
								<option value="AS">American Samoa</option>
								<option value="AT">Austria</option>
								<option value="AW">Aruba</option>
								<option value="AX">Aland Islands</option>
								<option value="AZ">Azerbaijan</option>
								<option value="BA">Bosnia and Herzegovina</option>
								<option value="BB">Barbados</option>
								<option value="BD">Bangladesh</option>
								<option value="BE">Belgium</option>
								<option value="BF">Burkina Faso</option>
								<option value="BG">Bulgaria</option>
								<option value="BH">Bahrain</option>
								<option value="BI">Burundi</option>
								<option value="BJ">Benin</option>
								<option value="BL">Saint Barthélemy</option>
								<option value="BM">Bermuda</option>
								<option value="BN">Brunei Darussalam</option>
								<option value="BO">Bolivia, Plurinational State of</option>
								<option value="BQ">Bonaire, Sint Eustatius and Saba</option>
								<option value="BR">Brazil</option>
								<option value="BS">Bahamas</option>
								<option value="BT">Bhutan</option>
								<option value="BV">Bouvet Island</option>
								<option value="BW">Botswana</option>
								<option value="BY">Belarus</option>
								<option value="BZ">Belize</option>
								<option value="CC">Cocos (Keeling) Islands</option>
								<option value="CD">Congo, the Democratic Republic of the</option>
								<option value="CF">Central African Republic</option>
								<option value="CG">Congo</option>
								<option value="CH">Switzerland</option>
								<option value="CI">Cote d'Ivoire</option>
								<option value="CK">Cook Islands</option>
								<option value="CL">Chile</option>
								<option value="CM">Cameroon</option>
								<option value="CN">China</option>
								<option value="CO">Colombia</option>
								<option value="CR">Costa Rica</option>
								<option value="CU">Cuba</option>
								<option value="CV">Cape Verde</option>
								<option value="CW">Curaçao</option>
								<option value="CX">Christmas Island</option>
								<option value="CY">Cyprus</option>
								<option value="CZ">Czech Republic</option>
								<option value="DE">Germany</option>
								<option value="DJ">Djibouti</option>
								<option value="DK">Denmark</option>
								<option value="DM">Dominica</option>
								<option value="DO">Dominican Republic</option>
								<option value="DZ">Algeria</option>
								<option value="EC">Ecuador</option>
								<option value="EE">Estonia</option>
								<option value="EG">Egypt</option>
								<option value="EH">Western Sahara</option>
								<option value="ER">Eritrea</option>
								<option value="ES">Spain</option>
								<option value="ET">Ethiopia</option>
								<option value="FI">Finland</option>
								<option value="FJ">Fiji</option>
								<option value="FK">Falkland Islands (Malvinas)</option>
								<option value="FM">Federated States of Micronesia</option>
								<option value="FO">Faroe Islands</option>
								<option value="FR">France</option>
								<option value="GA">Gabon</option>
								<option value="GD">Grenada</option>
								<option value="GE">Georgia</option>
								<option value="GF">French Guiana</option>
								<option value="GG">Guernsey</option>
								<option value="GH">Ghana</option>
								<option value="GI">Gibraltar</option>
								<option value="GL">Greenland</option>
								<option value="GM">Gambia</option>
								<option value="GN">Guinea</option>
								<option value="GP">Guadeloupe</option>
								<option value="GQ">Equatorial Guinea</option>
								<option value="GR">Greece</option>
								<option value="GS">South Georgia and the South Sandwich Islands</option>
								<option value="GT">Guatemala</option>
								<option value="GU">Guam</option>
								<option value="GW">Guinea-Bissau</option>
								<option value="GY">Guyana</option>
								<option value="HK">Hong Kong</option>
								<option value="HM">Heard Island and McDonald Islands</option>
								<option value="HN">Honduras</option>
								<option value="HR">Croatia</option>
								<option value="HT">Haiti</option>
								<option value="HU">Hungary</option>
								<option value="ID">Indonesia</option>
								<option value="IE">Ireland</option>
								<option value="IL">Israel</option>
								<option value="IM">Isle of Man</option>
								<option value="IN">India</option>
								<option value="IO">British Indian Ocean Territory</option>
								<option value="IQ">Iraq</option>
								<option value="IR">Iran, Islamic Republic of</option>
								<option value="IS">Iceland</option>
								<option value="IT">Italy</option>
								<option value="JE">Jersey</option>
								<option value="JM">Jamaica</option>
								<option value="JO">Jordan</option>
								<option value="JP">Japan</option>
								<option value="KE">Kenya</option>
								<option value="KG">Kyrgyzstan</option>
								<option value="KH">Cambodia</option>
								<option value="KI">Kiribati</option>
								<option value="KM">Comoros</option>
								<option value="KN">Saint Kitts and Nevis</option>
								<option value="KP">North Korea</option>
								<option value="KR">South Korea</option>
								<option value="KW">Kuwait</option>
								<option value="KY">Cayman Islands</option>
								<option value="KZ">Kazakhstan</option>
								<option value="LA">Lao People's Democratic Republic</option>
								<option value="LB">Lebanon</option>
								<option value="LC">Saint Lucia</option>
								<option value="LI">Liechtenstein</option>
								<option value="LK">Sri Lanka</option>
								<option value="LR">Liberia</option>
								<option value="LS">Lesotho</option>
								<option value="LT">Lithuania</option>
								<option value="LU">Luxembourg</option>
								<option value="LV">Latvia</option>
								<option value="LY">Libya</option>
								<option value="MA">Morocco</option>
								<option value="MC">Monaco</option>
								<option value="MD">Moldova, Republic of</option>
								<option value="ME">Montenegro</option>
								<option value="MF">Saint Martin (French part)</option>
								<option value="MG">Madagascar</option>
								<option value="MH">Marshall Islands</option>
								<option value="MK">Macedonia, the former Yugoslav Republic of</option>
								<option value="ML">Mali</option>
								<option value="MM">Myanmar</option>
								<option value="MN">Mongolia</option>
								<option value="MO">Macao</option>
								<option value="MP">Northern Mariana Islands</option>
								<option value="MQ">Martinique</option>
								<option value="MR">Mauritania</option>
								<option value="MS">Montserrat</option>
								<option value="MT">Malta</option>
								<option value="MU">Mauritius</option>
								<option value="MV">Maldives</option>
								<option value="MW">Malawi</option>
								<option value="MX">Mexico</option>
								<option value="MY">Malaysia</option>
								<option value="MZ">Mozambique</option>
								<option value="NA">Namibia</option>
								<option value="NC">New Caledonia</option>
								<option value="NE">Niger</option>
								<option value="NF">Norfolk Island</option>
								<option value="NG">Nigeria</option>
								<option value="NI">Nicaragua</option>
								<option value="NL">Netherlands</option>
								<option value="NO">Norway</option>
								<option value="NP">Nepal</option>
								<option value="NR">Nauru</option>
								<option value="NU">Niue</option>
								<option value="NZ">New Zealand</option>
								<option value="OM">Oman</option>
								<option value="PA">Panama</option>
								<option value="PE">Peru</option>
								<option value="PF">French Polynesia</option>
								<option value="PG">Papua New Guinea</option>
								<option value="PH">Philippines</option>
								<option value="PK">Pakistan</option>
								<option value="PL">Poland</option>
								<option value="PM">Saint Pierre and Miquelon</option>
								<option value="PN">Pitcairn</option>
								<option value="PR">Puerto Rico</option>
								<option value="PS">Palestine</option>
								<option value="PT">Portugal</option>
								<option value="PW">Palau</option>
								<option value="PY">Paraguay</option>
								<option value="QA">Qatar</option>
								<option value="RE">Reunion</option>
								<option value="RO">Romania</option>
								<option value="RS">Serbia</option>
								<option value="RU">Russian Federation</option>
								<option value="RW">Rwanda</option>
								<option value="SA">Saudi Arabia</option>
								<option value="SB">Solomon Islands</option>
								<option value="SC">Seychelles</option>
								<option value="SD">Sudan</option>
								<option value="SE">Sweden</option>
								<option value="SG">Singapore</option>
								<option value="SH">Saint Helena, Ascension and Tristan da Cunha</option>
								<option value="SI">Slovenia</option>
								<option value="SJ">Svalbard and Jan Mayen</option>
								<option value="SK">Slovakia</option>
								<option value="SL">Sierra Leone</option>
								<option value="SM">San Marino</option>
								<option value="SN">Senegal</option>
								<option value="SO">Somalia</option>
								<option value="SR">Suriname</option>
								<option value="SS">South Sudan</option>
								<option value="ST">Sao Tome and Principe</option>
								<option value="SV">El Salvador</option>
								<option value="SX">Sint Maarten (Dutch part)</option>
								<option value="SY">Syrian Arab Republic</option>
								<option value="SZ">Swaziland</option>
								<option value="TC">Turks and Caicos Islands</option>
								<option value="TD">Chad</option>
								<option value="TF">French Southern Territories</option>
								<option value="TG">Togo</option>
								<option value="TH">Thailand</option>
								<option value="TJ">Tajikistan</option>
								<option value="TK">Tokelau</option>
								<option value="TL">Timor-Leste</option>
								<option value="TM">Turkmenistan</option>
								<option value="TN">Tunisia</option>
								<option value="TO">Tonga</option>
								<option value="TR">Turkey</option>
								<option value="TT">Trinidad and Tobago</option>
								<option value="TV">Tuvalu</option>
								<option value="TW">Taiwan</option>
								<option value="TZ">Tanzania, United Republic of</option>
								<option value="UA">Ukraine</option>
								<option value="UG">Uganda</option>
								<option value="UM">United States Minor Outlying Islands</option>
								<option value="UY">Uruguay</option>
								<option value="UZ">Uzbekistan</option>
								<option value="VA">Holy See (Vatican City State)</option>
								<option value="VC">Saint Vincent and the Grenadines</option>
								<option value="VE">Venezuela, Bolivarian Republic of</option>
								<option value="VG">Virgin Islands, British</option>
								<option value="VI">Virgin Islands, U.S.</option>
								<option value="VN">Viet Nam</option>
								<option value="VU">Vanuatu</option>
								<option value="WF">Wallis and Futuna</option>
								<option value="WS">Samoa</option>
								<option value="YE">Yemen</option>
								<option value="YT">Mayotte</option>
								<option value="ZA">South Africa</option>
								<option value="ZM">Zambia</option>
								<option value="ZW">Zimbabwe</option>
							</x-form.select>
						</x-form.field>

						<x-form.field :validation="[ 'required' ]" data-country="AU" class="inquiry-form__state" data-name="fields[State_Code__c]">
							<x-form.select label="State/Province">
								<option value="">- Select -</option>
								<option value="ACT">Australian Capital Territory</option>
								<option value="JBT">Jervis Bay Territory</option>
								<option value="NSW">New South Wales</option>
								<option value="NT">Northern Territory</option>
								<option value="QLD">Queensland</option>
								<option value="SA">South Australia</option>
								<option value="TAS">Tasmania</option>
								<option value="VIC">Victoria</option>
								<option value="WA">Western Australia</option>
							</x-form.select>
						</x-form.field>

						<x-form.field :validation="[ 'required' ]" data-country="US" class="inquiry-form__state" data-name="fields[State_Code__c]">
							<x-form.select label="State/Province">
								<option value="">- Select -</option>
								<option value="AA">Armed Forces Americas</option>
								<option value="AE">Armed Forces Europe</option>
								<option value="AK">Alaska</option>
								<option value="AL">Alabama</option>
								<option value="AP">Armed Forces Pacific</option>
								<option value="AR">Arkansas</option>
								<option value="AS">American Samoa</option>
								<option value="AZ">Arizona</option>
								<option value="CA">California</option>
								<option value="CO">Colorado</option>
								<option value="CT">Connecticut</option>
								<option value="DC">District of Columbia</option>
								<option value="DE">Delaware</option>
								<option value="FL">Florida</option>
								<option value="FM">Federated Micronesia</option>
								<option value="GA">Georgia</option>
								<option value="GU">Guam</option>
								<option value="HI">Hawaii</option>
								<option value="IA">Iowa</option>
								<option value="ID">Idaho</option>
								<option value="IL">Illinois</option>
								<option value="IN">Indiana</option>
								<option value="KS">Kansas</option>
								<option value="KY">Kentucky</option>
								<option value="LA">Louisiana</option>
								<option value="MA">Massachusetts</option>
								<option value="MD">Maryland</option>
								<option value="ME">Maine</option>
								<option value="MH">Marshall Islands</option>
								<option value="MI">Michigan</option>
								<option value="MN">Minnesota</option>
								<option value="MO">Missouri</option>
								<option value="MP">Northern Mariana Islands</option>
								<option value="MS">Mississippi</option>
								<option value="MT">Montana</option>
								<option value="NC">North Carolina</option>
								<option value="ND">North Dakota</option>
								<option value="NE">Nebraska</option>
								<option value="NH">New Hampshire</option>
								<option value="NJ">New Jersey</option>
								<option value="NM">New Mexico</option>
								<option value="NV">Nevada</option>
								<option value="NY">New York</option>
								<option value="OH">Ohio</option>
								<option value="OK">Oklahoma</option>
								<option value="OR">Oregon</option>
								<option value="PA">Pennsylvania</option>
								<option value="PR">Puerto Rico</option>
								<option value="PW">Palau</option>
								<option value="RI">Rhode Island</option>
								<option value="SC">South Carolina</option>
								<option value="SD">South Dakota</option>
								<option value="TN">Tennessee</option>
								<option value="TX">Texas</option>
								<option value="UM">United States Minor Outlying Islands</option>
								<option value="UT">Utah</option>
								<option value="VA">Virginia</option>
								<option value="VI">US Virgin Islands</option>
								<option value="VT">Vermont</option>
								<option value="WA">Washington</option>
								<option value="WI">Wisconsin</option>
								<option value="WV">West Virginia</option>
							</x-form.select>
						</x-form.field>

						<x-form.field :validation="[ 'required' ]" data-country="CA" class="inquiry-form__state" data-name="fields[State_Code__c]">
							<x-form.select label="State/Province">
								<option value="">- Select -</option>
								<option value="AB">Alberta</option>
								<option value="BC">British Columbia</option>
								<option value="MB">Manitoba</option>
								<option value="NB">New Brunswick</option>
								<option value="NL">Newfoundland and Labrador</option>
								<option value="NS">Nova Scotia</option>
								<option value="NT">Northwest Territories</option>
								<option value="NU">Nunavut</option>
								<option value="ON">Ontario</option>
								<option value="PE">Prince Edward Island</option>
								<option value="QC">Quebec</option>
								<option value="SK">Saskatchewan</option>
								<option value="YT">Yukon Territories</option>
							</x-form.select>
						</x-form.field>
					</x-form.row>
					<x-form.row>
						<x-form.field :validation="[ 'required' ]">
							<x-form.select label="I would like to" name="fields[Journey_Stage__c]">
							<option value="Dreaming">Learn more about Polar Travel</option>
							<option value="Planning">Plan a trip</option>
							<option value="Booking">Book a trip</option>
							</x-form.select>
						</x-form.field>
					</x-form.row>

					<x-form.row>
						<x-form.field>
							<x-form.textarea label="What else would you like us to know?" placeholder="eg. Lorem ipsum" name="fields[Comments__c]" />
						</x-form.field>
					</x-form.row>

					{!! $slot !!}
				</x-modal.body>
				<x-modal.footer>
					<x-form.buttons>
						<x-form.submit>Request a Quote</x-form.submit>
					</x-form.buttons>
				</x-modal.footer>
			</div>
			<x-toast-message type="error" message="Fields marked with an asterisk (*) are required" />
		</x-form>

		@if ( empty( $thank_you_page ) )
			<div class="inquiry-form__thank-you">
				<x-svg name="logo" />
				<div class="inquiry-form__thank-you-text">
					<h4 class="inquiry-form__thank-you-text-heading">Thank you!</h4>
					<p class="inquiry-form__thank-you-text-body">A Quark Expeditions Polar Travel Advisor will be in touch with you shortly.</p>
				</div>
			</div>
		@endif
	</quark-inquiry-form-modal>
</x-modal>
