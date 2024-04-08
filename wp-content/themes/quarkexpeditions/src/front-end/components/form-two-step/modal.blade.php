@props( [
	'thank_you_page'     => '',
	'form_id'            => 'form-two-step',
	'modal_id'           => 'form-two-step-modal',
	'show_hidden_fields' => false,
	'countries'          => \Quark\Leads\Forms\get_countries(),
	'states'             => \Quark\Leads\Forms\get_states(),
] )

@php
	$title             = 'Almost there!';
	$subtitle          = 'We just need a bit more info to help personalize your itinerary.';
	$salesforce_object = 'Webform_Landing_Page__c';
@endphp

<x-modal
	class="form-two-step__modal"
	id="{{ $modal_id }}"
	title="{{ $title }}"
	subtitle="{{ $subtitle }}"
>
	<quark-form-two-step-modal>
		<x-form id="{{ $form_id }}"
			salesforce_object="{{ $salesforce_object }}"
			thank_you_page="{{ $thank_you_page }}"
		>
			@if ( true === $show_hidden_fields )
				<input type="hidden" name="fields[Polar_Region__c]" value="" class="form__polar-region-field">
				<input type="hidden" name="fields[Ship__c]" value="" class="form__ship-field">
				<input type="hidden" name="fields[Expedition__c]" value="" class="form__expedition-field">
			@endif

			<div class="form-two-step__content">
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
						<x-form.field :validation="[ 'required' ]" class="form-two-step__country">
							<x-form.select label="Country" name="fields[Country_Code__c]">
								<option value="">- Select -</option>
								@foreach ( $countries as $country_code => $country_name )
									<option value={{ $country_code }}>{{ $country_name }}</option>
								@endforeach
							</x-form.select>
						</x-form.field>

						@foreach ( $states as $country_code => $country_states )
							<x-form.field :validation="[ 'required' ]" data-country="{{ $country_code }}" class="form-two-step__state" data-name="fields[State_Code__c]">
								<x-form.select label="State/Province">
									<option value="">- Select -</option>
									@foreach ( $country_states as $state_code => $state_name )
										<option value={{ $state_code }}>{{ $state_name }}</option>
									@endforeach
								</x-form.select>
							</x-form.field>
						@endforeach
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
			<div class="form-two-step__thank-you">
				<x-svg name="logo" />
				<div class="form-two-step__thank-you-text">
					<h4 class="form-two-step__thank-you-text-heading">Thank you!</h4>
					<p class="form-two-step__thank-you-text-body">A Quark Expeditions Polar Travel Advisor will be in touch with you shortly.</p>
				</div>
			</div>
		@endif
	</quark-form-two-step-modal>
</x-modal>
