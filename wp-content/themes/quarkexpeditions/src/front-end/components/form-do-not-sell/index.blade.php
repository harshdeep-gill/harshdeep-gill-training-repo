@props( [
	'form_id'        => '',
	'class'          => '',
	'thank_you_page' => '',
] )

@php
	$classes = [ 'form-do-not-sell' ];
	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<x-form
	salesforce_object="Webform_CCPA_Do_Not_Sell_My_Information__c"
	id="{{ $form_id }}"
	thank_you_page="{{ $thank_you_page }}"
	@class( $classes )
>
	<div class="form-do-not-sell__content">
		<div class="form-do-not-sell__form">
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
					<x-form.input type="email" :label="__( 'Email', 'qrk' )" :placeholder="__( 'Enter Email', 'qrk' )" name="fields[Email_Address__c]" />
				</x-form.field>
			</x-form.row>
			<x-form.row>
				<x-form.field-group :validation="[ 'checkbox-group-required' ]">
					<x-form.checkbox name="fields[I_am_a_California_resident__c]" :label="__( 'I am a California resident.', 'qrk' )" />
				</x-form.field-group>
			</x-form.row>
		</div>

		<x-form.buttons>
			<x-form.submit size="big" color="black">{!! __( 'Submit', 'qrk' ) !!}</x-form.submit>
		</x-form.buttons>
	</div>
</x-form>
