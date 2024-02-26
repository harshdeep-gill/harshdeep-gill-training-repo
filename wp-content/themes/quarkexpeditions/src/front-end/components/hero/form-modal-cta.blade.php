@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<x-modal.modal-open class="hero__form-modal-cta color-context--dark" modal_id="hero-inquiry-form-modal">
	<x-button type="button" size="big">
		<x-content :content="$slot" />
	</x-button>
</x-modal.modal-open>
<x-inquiry-form.modal
	form_id="inquiry-form"
	title="Almost there!"
	subtitle="We just need a bit more info to help personalize your itinerary."
	modal_id="hero-inquiry-form-modal"
	salesforce_object="Webform_Landing_Page__c"
/>
