@props( [
	'form_id'        => '',
	'class'          => '',
	'thank_you_page' => '',
	'url'            => '',
] )

@php
	$classes = [ 'form-snow-hill' ];
	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<x-form
	salesforce_object="Webform_Snow_Hill_Newsletter_Sign_up__c"
	id="{{ $form_id }}"
	thank_you_page="{{ $thank_you_page }}"
	@class( $classes )
>
	<div class="form-snow-hill__content">
		<div class="form-snow-hill__form">
			<h3 class="form-snow-hill__title">{{ __( 'Subscribe Snow Hill Newsletter', 'qrk' ) }}</h3>

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
				<x-form.field-group>
					<x-form.checkbox name="fields[Subscribe_General_Newsletter__c]" :label="__( 'In addition, I\'d also like to subscribe to Quark Expedition\'s weekly newsletter.', 'qrk' )" />
				</x-form.field-group>
			</x-form.row>
		</div>

		<x-form.buttons>
			<x-form.submit size="big" color="black">{!! __( 'Submit', 'qrk' ) !!}</x-form.submit>
		</x-form.buttons>
	</div>
</x-form>
