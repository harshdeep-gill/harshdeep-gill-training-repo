@props( [
	'original_price'   => '',
	'discounted_price' => '',
] )

@php
	if ( empty( $original_price ) ) {
		return;
	}
@endphp

<div class="product-cards__price-container">

	@if ( ! empty( $discounted_price ) )
		@if ( $discounted_price !== $original_price )
			<strong class="product-cards__price product-cards__price-now">{{ $discounted_price }}</strong>
			<strong><del class="product-cards__price price-was">{{ $original_price }}</del></strong>
		@else
			<span class="product-cards__price">{{ $original_price }}</span>
		@endif
	@endif

	@if ( empty( $discounted_price ) )
		<strong class="product-cards__price">{{ $original_price }}</strong>
	@endif
</div>
