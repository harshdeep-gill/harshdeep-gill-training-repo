@props( [
	'original_price'   => '',
	'discounted_price' => '',
] )

@php
	if ( empty( $original_price ) ) {
		return;
	}
@endphp

<div class="product-cards__price-wrap">
	<div class="product-cards__price-title">{{ __( 'Sale price from', 'qrk' ) }}</div>

	@if ( ! empty( $discounted_price ) )
		@if ( $discounted_price !== $original_price )
			<strong class="product-cards__price product-cards__price-now h4">{{ $discounted_price }}</strong>
			<strong><del class="product-cards__price product-cards__price--original">{{ $original_price }}</del></strong>
		@else
			<span class="product-cards__price">{{ $original_price }}</span>
		@endif
	@endif

	@if ( empty( $discounted_price ) )
		<strong class="product-cards__price">{{ $original_price }}</strong>
	@endif
</div>
