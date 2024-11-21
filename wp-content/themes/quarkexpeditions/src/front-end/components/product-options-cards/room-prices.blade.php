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
	@if ( ! empty( $discounted_price ) )
		@if ( $discounted_price !== $original_price )
			<div class="product-options-cards__room-prices-info">
				<div class="product-options-cards__room-prices--discounted">
					<x-escape :content="$discounted_price" />
				</div>
				<div class="product-options-cards__room-prices--original">
					<x-escape :content="$original_price" />
				</div>
			</div>
		@else
			<div class="product-options-cards__room-prices">
					<x-escape :content="$original_price" />
			</div>
		@endif
	@endif

	@if ( empty( $discounted_price ) )
		<div class="product-options-cards__room-prices">
			<x-escape :content="$original_price" />
		</div>
	@endif
</div>
