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
			<h3 class="form-snow-hill__title">{{ __('Subscribe Snow Hill Newsletter', 'qrk') }}</h3>

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
			<p class="form-newsletter__privacy">
						We respect your privacy. You may unsubscribe from our communications at any time. Please refer to our <a href="{{ $url }}">privacy policy</a> for full details.
			</p>
		</div>

		<x-form.buttons>
			<x-form.submit size="big" color="black">Submit</x-form.submit>
		</x-form.buttons>
	</div>
</x-form>
