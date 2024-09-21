@props( [
	'available_months' => [],
	'is_multi_select'  => true,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	quark_enqueue_style( 'tp-slider' );
	quark_enqueue_script( 'tp-slider' );
@endphp

<quark-months-multi-select
	class="months-multi-select"
	available-months="{{ wp_json_encode( $available_months ) }}"
	is-multi-select="{{ $is_multi_select }}"
>
	<x-months-multi-select.carousel>
		{!! $slot !!}
	</x-months-multi-select.carousel>
</quark-months-multi-select>
