@props( [
	'name'     => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	quark_enqueue_style( 'tp-slider' );
	quark_enqueue_script( 'tp-slider' );
@endphp

<x-section class="collage">
	<x-collage.carousel>
		{!! $slot !!}
	</x-collage.carousel>
</x-section>
