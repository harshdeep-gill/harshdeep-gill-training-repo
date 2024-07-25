@props( [
	'class' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'product-options-cards__discounts' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<div @class( $classes )>
	<h5>Discounts Applied</h5>
	<div class="product-options-cards__discounts-container">
		{!! $slot !!}
	</div>
</div>
