@props( [
	'original_price'   => '',
	'discounted_price' => '',
] )

@php
	if ( empty( $original_price ) ) {
		return;
	}
@endphp

<div class="departure-cards__price-wrap">
	<div class="departure-cards__price-title">{{ __( 'from', 'qrk' ) }}</div>

	@if ( ! empty( $discounted_price ) )
		@if ( $discounted_price !== $original_price )
			<strong class="departure-cards__price departure-cards__price-now h4">{{ $discounted_price }}</strong>
			<del class="departure-cards__price departure-cards__price--original">{{ $original_price }}</del>
		@else
			<span class="departure-cards__price h4">{{ $original_price }}</span>
		@endif
	@endif

	@if ( empty( $discounted_price ) )
		<strong class="departure-cards__price h4">{{ $original_price }}</strong>
	@endif
</div>
