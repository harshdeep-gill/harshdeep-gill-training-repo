@props( [
	'form_id'        => '',
	'class'          => '',
	'countries'      => [],
	'states'         => [],
	'thank_you_page' => '',
] )

@php
	$classes = [ 'form-communications-opt-in' ];
	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<x-form
	salesforce_object="Onboard_Email_Opt_In__c"
	id="{{ $form_id }}"
	thank_you_page="{{ $thank_you_page }}"
	@class( $classes )
	marketing_fields=false
	webform_url=false
	ga_client_id=false
>
   {{--
		Need to update this input field ( From line: 29) in future as we are not sure about the value for this field.
		After some discution on this ticket with Ankur as conclusion we depriority this form changes.
		Need to updated post lounch based on the description from ticket QE-885 [https://tuispecialist.atlassian.net/browse/QE-885];
    --}}
	{{-- <input type="hidden" name="fields[Departure_ID__c]" value="" class="form__departure-id"> --}}
	<div class="form-communications-opt-in__content">
		<div class="form-communications-opt-in__form">
			<h3 class="form-communications-opt-in__title">{{ __( 'Register Your Email Now', 'qrk' ) }}</h3>
			<p class="form-communications-opt-in__instructions">
				<x-escape :content="__( 'In addition to your photographic journal, voyage video, Captain’s log, bios, and daily itineraries you will receive an exciting perk for you or your friends plus other future offers.', 'qrk' )" />
			</p>


			<x-form.row>
				<x-form.field :validation="[ 'required', 'no-empty-spaces' ]">
					<x-form.input type="text" :label="__( 'First Name', 'qrk' )" :placeholder="__( 'Enter First Name', 'qrk' )" name="fields[First_Name__c]" />
				</x-form.field>
			</x-form.row>
			<x-form.row>
				<x-form.field :validation="[ 'required', 'no-empty-spaces' ]">
					<x-form.input type="text" :label="__( 'Last Name', 'qrk' )" :placeholder="__( 'Enter Last Name', 'qrk' )" name="fields[Last_Name__c]" />
				</x-form.field>
			</x-form.row>
			<x-form.row>
				<x-form.field :validation="[ 'required', 'email' ]">
					<x-form.input type="email" :label="__( 'Email', 'qrk' )" :placeholder="__( 'Enter Email', 'qrk' )" name="fields[Email__c]" />
				</x-form.field>
			</x-form.row>
			<x-form.row>
				<x-country-selector :countries="$countries" :states="$states" />
			</x-form.row>
		</div>

		<x-form.buttons>
			<x-form.submit size="big" color="black">{{ __( 'Submit', 'qrk' ) }}</x-form.submit>
		</x-form.buttons>
	</div>
</x-form>
