@props( [
	'class'      => '',
	'details_id' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'product-options-cards__card' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
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
