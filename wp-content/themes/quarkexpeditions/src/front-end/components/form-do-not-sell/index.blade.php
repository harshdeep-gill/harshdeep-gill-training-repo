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
			<p class="form-do-not-sell__instructions">
				{!!
					esc_html__(
						'Fill the form below, and we will get back to you on the same or next business day.',
						'qrk'
					)
				!!}
			</p>

			<div class="form-do-not-sell__fields">
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
						<x-form.input type="email" label="Email" placeholder="Enter Email" name="fields[Email_Address__c]" />
					</x-form.field>
				</x-form.row>
				<x-form.row>
					<x-form.field-group :validation="[ 'checkbox-group-required' ]">
						<x-form.checkbox name="fields[I_am_a_California_resident__c]" label="I am a California resident." />
					</x-form.field-group>
				</x-form.row>
			</div>
		</div>

		<x-form.buttons>
			<x-form.submit size="big" color="black">Submit</x-form.submit>
		</x-form.buttons>
	</div>
</x-form>
