@props( [
	'class'          => '',
	'thank_you_page' => '',
	'form_id'        => 'form-two-step',
	'countries'      => [],
	'states'         => [],
	'hidden_fields'  => [],
] )

@php
	$classes = [ 'form-two-step' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<quark-form-two-step @class( $classes )>
	<x-form.field :validation="[ 'required' ]">
		<x-form.select label="Where would you like to travel?" name="fields[Sub_Region__c]" form="{{ $form_id }}">
			<x-form.option value="">- Select -</x-form.option>
			<x-form.option value="Antarctic Peninsula" label="Antarctic Peninsula">Antarctic Peninsula</x-form.option>
			<x-form.option value="Falklands & South Georgia" label="Falklands & South Georgia">Falklands & South Georgia</x-form.option>
			<x-form.option value="Patagonia" label="Patagonia">Patagonia</x-form.option>
			<x-form.option value="Snow Hill Island" label="Snow Hill Island">Snow Hill Island</x-form.option>
		</x-form.select>
	</x-form.field>
	<x-form.field :validation="[ 'required' ]">
		<x-form.select label="The most important factor for you?" name="fields[Most_Important_Factors__c]" form="{{ $form_id }}">
			<x-form.option value="">- Select -</x-form.option>
			<x-form.option value="Adventure Activities" label="Adventure Activities">Adventure Activities</x-form.option>
			<x-form.option value="Budget" label="Budget">Budget</x-form.option>
			<x-form.option value="Region" label="Destination">Destination</x-form.option>
			<x-form.option value="Schedule" label="Schedule">Schedule</x-form.option>
			<x-form.option value="Wildlife" label="Wildlife">Wildlife</x-form.option>
		</x-form.select>
	</x-form.field>
	<x-form.field :validation="[ 'required' ]">
		<x-form.select label="How many guests?" name="fields[Pax_Count__c]" form="{{ $form_id }}">
			<x-form.option value="">- Select -</x-form.option>
			<x-form.option value="1" label="1">1</x-form.option>
			<x-form.option value="2" label="2">2</x-form.option>
			<x-form.option value="3" label="3">3</x-form.option>
			<x-form.option value="4" label="4">4</x-form.option>
		</x-form.select>
	</x-form.field>
	<x-form.field>
		<x-form.select label="When would you like to go?" name="fields[Season__c]" form="{{ $form_id }}">
			<x-form.option value="">- Select -</x-form.option>
			<x-form.option value="2024-25" label="Antarctic 2024/25 (Nov '24 - Mar '25)">Antarctic 2024/25 (Nov '24 - Mar '25)</x-form.option>
			<x-form.option value="2025-26" label="Antarctic 2025/26 (Nov '25 - Mar '26)">Antarctic 2025/26 (Nov '25 - Mar '26)</x-form.option>
		</x-form.select>
	</x-form.field>
	<x-form.buttons>
		<x-form-two-step.modal-cta
			class="form-two-step__modal-open"
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
		</x-form-two-step.modal-cta>
	</x-form.buttons>
</quark-form-two-step>
