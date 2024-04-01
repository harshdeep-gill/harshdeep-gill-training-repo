@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<x-lp-form-modal-cta class="hero__form-modal-cta color-context--dark" form_id="inquiry-form">
	<x-button type="button" size="big">
		<x-content :content="$slot" />
	</x-button>
</x-lp-form-modal-cta>
