@props( [
	'class'            => '',
	'original_price'   => '',
	'discounted_price' => '',
] )

@php
	if ( empty( $original_price ) || empty( $discounted_price ) ) {
		return;
	}

	$classes = [ 'product-options-cards__price' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<div @class( $classes )>
	<p>From</p>
	<div class="product-options-cards__price-info">
		<div class="product-options-cards__price-discounted">
			<x-escape :content="$original_price" />
		</div>
		<div class="product-options-cards__price-original">
			<x-escape :content="$discounted_price" />
		</div>
	</div>
</div>
