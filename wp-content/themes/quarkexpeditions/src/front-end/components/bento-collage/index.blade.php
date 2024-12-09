@php
	if ( empty( $slot ) ) {
		return;
	}

	// Slider assets.
	quark_enqueue_style( 'tp-slider' );
	quark_enqueue_script( 'tp-slider' );
@endphp

<x-section class="bento-collage">
	<x-bento-collage.carousel>
		{!! $slot !!}
	</x-bento-collage.carousel>
</x-section>
