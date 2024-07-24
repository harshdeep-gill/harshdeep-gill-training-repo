@props( [
	'class' => '',
	'id'    => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'product-options-cards__card-details' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<div
	@class( $classes )
	id="{!! esc_attr( $id ) !!}"
>
	{!! $slot !!}
</div>
