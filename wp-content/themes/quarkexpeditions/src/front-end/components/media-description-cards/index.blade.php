@php
	if ( empty( $slot ) ) {
		return;
	}

	// TP Slider.
	quark_enqueue_style( 'tp-slider' );
	quark_enqueue_script( 'tp-slider' );
@endphp

<x-section>
	<div class="media-description-cards">
		<x-media-description-cards.carousel>
			{!! $slot !!}
		</x-media-description-cards.carousel>
	</div>
</x-section>
