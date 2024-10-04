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
			<h3 class="form-do-not-selln__title">Register Your Email Now</h4>
			<p class="form-do-not-sell__instructions">
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
					<x-form.input type="email" label="Email" placeholder="Enter Email" name="fields[Email_Address__c]" />
				</x-form.field>
			</x-form.row>
			<x-form.row>
				<x-form.field-group :validation="[ 'checkbox-group-required' ]">
					<x-form.checkbox name="fields[I_am_a_California_resident__c]" label="I am a California resident." />
				</x-form.field-group>
			</x-form.row>
		</div>

		<x-form.buttons>
			<x-form.submit size="big" color="black">Submit</x-form.submit>
		</x-form.buttons>
	</div>
</x-form>
