@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<x-hero.form class="hero__form-modal-cta">
	<x-inquiry-form
		form_id="inquiry-form"
		title="Almost there!"
		subtitle="We just need a bit more info to help personalize your itinerary."
		salesforce_object="Webform_Landing_Page__c"
		:cta_text="$slot"
		:has_outer_fields="false"
	/>
</x-hero.form>
