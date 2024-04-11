@props( [
	'class'          => '',
	'thank_you_page' => '',
	'form_id'        => 'form-two-step-compact',
	'countries'      => [],
	'states'         => [],
	'hidden_fields'  => [],
] )

@php
	$classes = [ 'form-two-step-compact' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<quark-form-two-step-compact @class( $classes )>
	<x-form.field :validation="[ 'required' ]">
		<x-form.select label="When would you like to go?" name="fields[Season__c]" form="{{ $form_id }}">
			<option value="">- Select -</option>
			<option value="2024-25">Antarctic 2024/25 (Nov '24 - Mar '25)</option>
			<option value="2025-26">Antarctic 2025/26 (Nov '25 - Mar '26)</option>
		</x-form.select>
	</x-form.field>
	<x-form.field :validation="[ 'required' ]">
		<x-form.select label="The most important factor for you?" name="fields[Most_Important_Factors__c]" form="{{ $form_id }}">
			<option value="">- Select -</option>
			<option value="Adventure Activities">Adventure Activities</option>
			<option value="Budget">Budget</option>
			<option value="Region">Destination</option>
			<option value="Schedule">Schedule</option>
			<option value="Wildlife">Wildlife</option>
		</x-form.select>
	</x-form.field>
	<x-form.field :validation="[ 'required' ]">
		<x-form.select label="How many guests?" name="fields[Pax_Count__c]" form="{{ $form_id }}">
			<option value="">- Select -</option>
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
		</x-form.select>
	</x-form.field>
	<x-form.buttons>
		<x-form-two-step-compact.modal-cta
			class="form-two-step-compact__modal-open"
			form_id="{{ $form_id }}"
			thank_you_page="{{ $thank_you_page }}"
			:hidden_fields="$hidden_fields"
			:countries="$countries"
			:states="$states"
		>
			<x-button type="button">
				Request a Quote
				<x-button.sub-title title="It only takes 2 minutes!" />
			</x-button>
		</x-form-two-step-compact.modal-cta>
	</x-form.buttons>
</quark-form-two-step-compact>
