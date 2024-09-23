@props( [
	'form_id'        => '',
	'class'          => '',
	'thank_you_page' => '',
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
			<p class="form-snow-hill__instructions">
				{!!
					esc_html__(
						'Subscribe to our Snow Hill Newsletter.',
						'qrk'
					)
				!!}
			</p>

			<div class="form-snow-hill__fields">
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
					<x-form.field-group>
						<x-form.checkbox name="fields[Subscribe_General_Newsletter__c]" label="In addition, I'd also like to subscribe to Quark Expedition's weekly newsletter." />
					</x-form.field-group>
				</x-form.row>
				{!! $slot !!}
			</div>
		</div>

		<x-form.buttons>
			<x-form.submit size="big" color="black">Submit</x-form.submit>
		</x-form.buttons>
	</div>
</x-form>
