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
	<p>{{ __( 'From', 'qrk' ) }}</p>
	<div class="product-options-cards__price-info">
		<div class="product-options-cards__price-discounted">
			<h5>
				<x-escape :content="$original_price" />
			</h5>{{ __( 'per person', 'qrk' ) }}
		</div>
		<div class="product-options-cards__price-original">
			<x-escape :content="$discounted_price" />
		</div>
	</div>
</div>
