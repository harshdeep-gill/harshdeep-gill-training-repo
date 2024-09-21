@props( [
	'available_months' => [],
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	quark_enqueue_style( 'tp-slider' );
	quark_enqueue_script( 'tp-slider' );
@endphp

<quark-months-multi-select class="months-multi-select">
	<x-months-multi-select.carousel>
		{!! $slot !!}
	</x-months-multi-select.carousel>
</quark-months-multi-select>
