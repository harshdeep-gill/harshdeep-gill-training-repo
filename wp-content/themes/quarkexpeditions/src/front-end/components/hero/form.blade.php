@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="hero__form color-context--dark">
	{!! $slot !!}
</div>

<x-modal id="inquiry-form" :full_width_mobile="true" :close_button="false">
	<x-inquiry-form title="Almost there!" subtitle="We just need a bit more info to help personalize your itinerary." />
</x-modal>
