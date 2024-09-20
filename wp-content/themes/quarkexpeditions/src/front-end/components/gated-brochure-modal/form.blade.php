@props( [
	'thank_you_page' => '',
	'form_id'        => 'download-gated-brochure',
	'countries'      => [],
	'states'         => [],
	'modal_title'    => '',
	'brochure_id'    => '',
	'brochure_url'   => '',
] )

<x-form id="{{ $form_id }}">
	<div class="gated-brochure-modal__form-content">
		@if ( ! empty( $modal_title ) )
			<div class="gated-brochure-modal__form-title">
				<h3><x-escape :content="$modal_title" /></h3>
			</div>
		@endif

		<x-form.row>
			<x-form.field :validation="[ 'required', 'no-empty-spaces' ]">
				<x-form.input type="text" label="{{ __( 'First Name', 'qrk' ) }}" placeholder="{{ __( 'Enter First Name', 'qrk' ) }}" name="fields[FirstName__c]" />
			</x-form.field>
			<x-form.field :validation="[ 'required', 'no-empty-spaces' ]">
				<x-form.input type="text" label="{{ __( 'Last Name', 'qrk' ) }}" placeholder="{{ __( 'Enter Last Name', 'qrk' ) }}" name="fields[LastName__c]" />
			</x-form.field>
		</x-form.row>

		<x-form.row>
			<x-form.field :validation="[ 'required', 'email' ]">
				<x-form.input type="email" label="{{ __( 'Email', 'qrk' ) }}" placeholder="{{ __( 'Enter Email', 'qrk' ) }}" name="fields[Email__c]" />
			</x-form.field>
		</x-form.row>

		<x-form.row>
			<x-country-selector :countries="$countries" :states="$states" />
		</x-form.row>

		<x-form.row>
			<x-form.field :validation="[ 'required' ]">
				<x-form.checkbox name="checkbox-example" label="{{ __( 'I am a travel agent', 'qrk' ) }}" />
			</x-form.field>
		</x-form.row>

		<x-form.row>
			<x-form.field :validation="[ 'required' ]">
				<x-form.checkbox name="checkbox-example" label="{{ __( 'I agree to receive digital communications for Quark Expeditions regarding offers and promotions', 'qrk' ) }}" />
			</x-form.field>
		</x-form.row>

		<x-form.buttons>
			<x-form.submit size="big">{{ __( 'View and Download Brochure', 'qrk' ) }}</x-form.submit>

			@if ( ! empty( $brochure_url ) )
				<x-button class="gated-brochure-modal__skip-brochure-cta" size="big" href="{{ $brochure_url }}">{{ __( 'Skip to Brochure', 'qrk' ) }}</x-button>
			@endif
		</x-form.buttons>
	</div>

	<x-toast-message type="error" message="{{ __( 'Fields marked with an asterisk (*) are required', 'qrk' ) }}" />
</x-form>
