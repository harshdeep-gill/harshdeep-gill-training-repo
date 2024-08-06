@props( [
	'class'            => '',
	'original_price'   => '',
	'discounted_price' => '',
] )

@php
	if ( empty( $original_price ) || empty( $discounted_price ) ) {
		return;
	}

	$classes = [ 'product-options-cards__room-prices' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<div @class( $classes )>
	<div class="product-options-cards__room-prices-info">
		<div class="product-options-cards__room-prices-discounted">
			<x-escape :content="$original_price" />
		</div>
		<div class="product-options-cards__room-prices-original">
			<x-escape :content="$discounted_price" />
		</div>
	</div>
</div>
