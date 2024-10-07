@props( [
	'class'      => '',
	'details_id' => '',
	'status'     => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'product-options-cards__card' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}

	if ( ! empty( $status ) && in_array( $status, [ 'R', 'S' ] ) ) {
		$classes[] = 'product-options-cards__card--' . match ( $status ) {
			'R' => 'please-call',
			'S' => 'sold-out',
		};
	}
@endphp

<tp-slider-slide
	@class( $classes )
	@if ( ! empty( $details_id ) )
		data-details-id="{!! esc_attr( $details_id ) !!}"
	@endif
>
	{!! $slot !!}
</tp-slider-slide>
