@props( [
	'modal_id'          => 'hero-inquiry-form-modal',
	'form_id'           => '',
	'title'             => '',
	'subtitle'          => '',
	'salesforce_object' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<x-inquiry-form
	class="hero__form color-context--dark"
>
	<quark-hero-form>
		<x-form.field :validation="[ 'required' ]">
			<x-form.select label="When would you like to travel?" name="fields[Sub_Region__c]" form="inquiry-form">
				<option value="">- Select -</option>
				<option value="Antarctic Peninsula">Antarctic Peninsula</option>
				<option value="Falklands &amp; South Georgia">Falklands &amp; South Georgia</option>
				<option value="Patagonia">Patagonia</option>
				<option value="Snow Hill Island">Snow Hill Island</option>
			</x-form.select>
		</x-form.field>
		<x-form.field :validation="[ 'required' ]">
			<x-form.select label="The most important factor for you?" name="fields[Most_Important_Factors__c]" form="inquiry-form">
				<option value="">- Select -</option>
				<option value="adventure_activities">Adventure Activities</option>
				<option value="budget">Budget</option>
				<option value="region">Destination</option>
				<option value="schedule">Schedule</option>
				<option value="wildlife">Wildlife</option>
			</x-form.select>
		</x-form.field>
		<x-form.field :validation="[ 'required' ]">
			<x-form.select label="How many guests?" name="fields[Pax_Count__c]" form="inquiry-form">
				<option value="">- Select -</option>
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
			</x-form.select>
		</x-form.field>
		<x-form.field>
			<x-form.select label="When would you like to go?" name="fields[Season__c]" form="inquiry-form">
				<option value="">- Select -</option>
				<option value="2023-24">Antarctic 2023/24 (Nov '23 - Mar '24)</option>
				<option value="2024-25">Antarctic 2024/25 (Nov '24 - Mar '25)</option>
				<option value="2025-26">Antarctic 2025/26 (Nov '25 - Mar '26)</option>
			</x-form.select>
		</x-form.field>
		<x-form.buttons>
			<x-modal.open-modal class="inquiry-form__open-modal" modal_id="{{ $modal_id }}">
				<x-button type="button">
					Request a Quote
					<x-button.sub-title title="It only takes 2 minutes!" />
				</x-button>
			</x-modal.open-modal>
		</x-form.buttons>
	</quark-hero-form>
	<x-inquiry-form.modal
		title="{{ $title }}"
		subtitle="{{ $subtitle }}"
		modal_id="{{ $modal_id }}"
		form_id="{{ $form_id }}"
		salesforce_object="{{ $salesforce_object }}"
	/>
</x-inquiry-form>
