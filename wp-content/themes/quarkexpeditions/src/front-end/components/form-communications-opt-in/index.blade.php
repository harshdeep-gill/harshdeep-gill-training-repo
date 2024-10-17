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
	extra_field_key="Departure_ID__c"
>
	<div class="form-communications-opt-in__content">
		<div class="form-communications-opt-in__form">
			<h3 class="form-communications-opt-in__title">{{ __( 'Register Your Email Now', 'qrk' ) }}</h3>
			<p class="form-communications-opt-in__instructions">
				<x-escape content="In addition to your photographic journal, voyage video, Captain’s log, bios, and daily itineraries you will receive an exciting perk for you or your friends plus other future offers." />
			</p>


			<x-form.row>
				<x-form.field :validation="[ 'required', 'no-empty-spaces' ]">
					<x-form.input type="text" label="First Name" placeholder="Enter First Name" name="fields[First_Name__c]" />
				</x-form.field>
			</x-form.row>
			<x-form.row>
				<x-form.field :validation="[ 'required', 'no-empty-spaces' ]">
					<x-form.input type="text" label="Last Name" placeholder="Enter Last Name" name="fields[Last_Name__c]" />
				</x-form.field>
			</x-form.row>
			<x-form.row>
				<x-form.field :validation="[ 'required', 'email' ]">
					<x-form.input type="email" label="Email" placeholder="Enter Email" name="fields[Email__c]" />
				</x-form.field>
			</x-form.row>
			<x-form.row>
				<x-country-selector :countries="$countries" :states="$states" />
			</x-form.row>
		</div>

		<x-form.buttons>
			<x-form.submit size="big" color="black">Submit</x-form.submit>
		</x-form.buttons>
	</div>
</x-form>
