@props( [
	'is_carousel' => 'true',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	quark_enqueue_style( 'tp-slider' );
	quark_enqueue_script( 'tp-slider' );
@endphp

<x-section class="review-cards" :full_width="true" :wrap="true">
	<x-review-cards.carousel :is_carousel="$is_carousel">
		{!! $slot !!}
	</x-review-cards.carousel>
</x-section>
