@props( [
	'thank_you_page'     => '',
	'form_id'            => 'form-two-step-compact',
	'modal_id'           => 'form-two-step-compact-modal',
	'show_hidden_fields' => false,
	'countries'          => \Quark\Leads\Forms\get_countries(),
] )

@php
	$title             = 'Almost there!';
	$subtitle          = 'We just need a bit more info to help personalize your itinerary.';
	$salesforce_object = 'Webform_Landing_Page__c';
@endphp

<x-modal
	class="form-two-step-compact__modal"
	id="{{ $modal_id }}"
	title="{{ $title }}"
	subtitle="{{ $subtitle }}"
>
	<quark-form-two-step-compact-modal>
		<x-form id="{{ $form_id }}"
			salesforce_object="{{ $salesforce_object }}"
			thank_you_page="{{ $thank_you_page }}"
		>
			@if ( true === $show_hidden_fields )
				<input type="hidden" name="fields[Polar_Region__c]" value="" class="form__polar-region-field">
				<input type="hidden" name="fields[Ship__c]" value="" class="form__ship-field">
				<input type="hidden" name="fields[Expedition__c]" value="" class="form__expedition-field">
			@endif

			<div class="form-two-step-compact__content">
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
						<x-form.field :validation="[ 'required', 'no-empty-spaces' ]">
							<x-form.input type="text" label="First Name" placeholder="Enter First Name" name="fields[FirstName__c]" />
						</x-form.field>
						<x-form.field :validation="[ 'required', 'no-empty-spaces' ]">
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
						<x-form.field :validation="[ 'required' ]" class="form-two-step-compact__country">
							<x-form.select label="Country" name="fields[Country_Code__c]">
								<option value="">- Select -</option>
								@foreach ( $countries as $country_code => $country_name )
									<option value={{ $country_code }}>{{ $country_name }}</option>
								@endforeach
							</x-form.select>
						</x-form.field>

						<x-form.field :validation="[ 'required' ]" data-country="AU" class="form-two-step-compact__state" data-name="fields[State_Code__c]">
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

						<x-form.field :validation="[ 'required' ]" data-country="US" class="form-two-step-compact__state" data-name="fields[State_Code__c]">
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

						<x-form.field :validation="[ 'required' ]" data-country="CA" class="form-two-step-compact__state" data-name="fields[State_Code__c]">
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
			<div class="form-two-step-compact__thank-you">
				<x-svg name="logo" />
				<div class="form-two-step-compact__thank-you-text">
					<h4 class="form-two-step-compact__thank-you-text-heading">Thank you!</h4>
					<p class="form-two-step-compact__thank-you-text-body">A Quark Expeditions Polar Travel Advisor will be in touch with you shortly.</p>
				</div>
			</div>
		@endif
	</quark-form-two-step-compact-modal>
</x-modal>
