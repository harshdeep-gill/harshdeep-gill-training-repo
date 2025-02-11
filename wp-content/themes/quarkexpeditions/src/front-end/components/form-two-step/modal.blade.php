@props( [
	'thank_you_page' => '',
	'form_id'        => 'form-two-step',
	'modal_id'       => 'form-two-step-modal',
	'countries'      => [],
	'states'         => [],
] )

@php
	$title             = __( 'Almost there', 'qrk' );
	$subtitle          = __( 'We just need a bit more info to help personalize your itinerary.', 'qrk' );
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
			<input type="hidden" name="fields[Polar_Region__c]" value="" class="form__polar-region-field">
			<input type="hidden" name="fields[Ship__c]" value="" class="form__ship-field">
			<input type="hidden" name="fields[Expedition__c]" value="" class="form__expedition-field">
			<input type="hidden" name="fields[Season__c]" value="" class="form__season-field">

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
							<x-form.input type="text" :label="__( 'First Name', 'qrk' )" :placeholder="__( 'Enter First Name', 'qrk' )" name="fields[FirstName__c]" />
						</x-form.field>
						<x-form.field :validation="[ 'required', 'no-empty-spaces' ]">
							<x-form.input type="text" :label="__( 'Last Name', 'qrk' )" :placeholder="__( 'Enter Last Name', 'qrk' )" name="fields[LastName__c]" />
						</x-form.field>
					</x-form.row>
					<x-form.row>
						<x-form.field :validation="[ 'required', 'email' ]">
							<x-form.input type="email" :label="__( 'Email', 'qrk' )" :placeholder="__( 'Enter Email', 'qrk' )" name="fields[Email__c]" />
						</x-form.field>
						<x-form.field :validation="[ 'required' ]">
							<x-form.input type="tel" :label="__( 'Phone Number', 'qrk' )" :placeholder="__( 'eg. (123) 456 7890', 'qrk' )" name="fields[Phone__c]" />
						</x-form.field>
					</x-form.row>
					<x-form.row>
						<x-country-selector :countries="$countries" :states="$states" />
					</x-form.row>
					<x-form.row>
						<x-form.field :validation="[ 'required' ]">
							<x-form.select :label="__( 'I would like to', 'qrk' )" name="fields[Journey_Stage__c]">
							<x-form.option value="Dreaming" :label="__( 'Learn more about Polar Travel', 'qrk' )">{!! __( 'Learn more about Polar Travel', 'qrk' ) !!}</x-form.option>
							<x-form.option value="Planning" :label="__( 'Plan a trip', 'qrk' )">{!! __( 'Plan a trip', 'qrk' ) !!}</x-form.option>
							<x-form.option value="Booking" :label="__( 'Book a trip', 'qrk' )">{!! __( 'Book a trip', 'qrk' ) !!}</x-form.option>
							</x-form.select>
						</x-form.field>
					</x-form.row>

					<x-form.row>
						<x-form.field>
							<x-form.textarea :label="__( 'What else would you like us to know?', 'qrk' )" :placeholder="__( 'eg. Lorem ipsum', 'qrk' )" name="fields[Comments__c]" />
						</x-form.field>
					</x-form.row>
				</x-modal.body>
				<x-modal.footer>
					<x-form.buttons>
						<x-form.submit>{!! __( 'Request a Quote', 'qrk' ) !!}</x-form.submit>
					</x-form.buttons>
				</x-modal.footer>
			</div>
			<x-toast-message type="error" :message="__( 'Fields marked with an asterisk (*) are required', 'qrk' )" />
		</x-form>

		@if ( empty( $thank_you_page ) )
			<div class="form-two-step__thank-you">
				<x-svg name="logo" />
				<div class="form-two-step__thank-you-text">
					<h4 class="form-two-step__thank-you-text-heading">{!! __( 'Thank you', 'qrk' ) !!}</h4>
					<p class="form-two-step__thank-you-text-body">{!! __( 'A Quark Expeditions Polar Travel Advisor will be in touch with you shortly.', 'qrk' ) !!}</p>
				</div>
			</div>
		@endif
	</quark-form-two-step-modal>
</x-modal>
