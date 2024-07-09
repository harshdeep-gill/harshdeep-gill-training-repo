@props( [
	'thank_you_page'     => '',
	'form_id'            => 'form-two-step-compact',
	'modal_id'           => 'form-two-step-compact-modal',
	'countries'          => [],
	'states'             => [],
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
			<input type="hidden" name="fields[Polar_Region__c]" value="" class="form__polar-region-field">
			<input type="hidden" name="fields[Ship__c]" value="" class="form__ship-field">
			<input type="hidden" name="fields[Expedition__c]" value="" class="form__expedition-field">

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
						<x-country-selector :countries="$countries" :states="$states" />
					</x-form.row>
					<x-form.row>
						<x-form.field :validation="[ 'required' ]">
							<x-form.select label="I would like to" name="fields[Journey_Stage__c]">
								<x-form.option value="Dreaming" label="Learn more about Polar Travel">Learn more about Polar Travel</x-form.option>
								<x-form.option value="Planning" label="Plan a trip">Plan a trip</x-form.option>
								<x-form.option value="Booking" label="Book a trip">Book a trip</x-form.option>
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
