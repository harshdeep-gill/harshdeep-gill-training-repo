@php
	if ( empty( $slot ) ) {
		return;
	}

	// TP Slider.
	quark_enqueue_style( 'tp-slider' );
	quark_enqueue_script( 'tp-slider' );
@endphp

<x-section>
	<div class="icon-info-grid">
		<x-icon-info-grid.carousel>
			{!! $slot !!}
		</x-icon-info-grid.carousel>
	</div>
</x-section>
