@props( [
	'offer'      => '',
	'offer_text' => '',
	'sold_out'   => false,
] )

@php
	if ( empty( $offer ) || empty( $offer_text ) ) {
		return;
	}
@endphp

<div class="product-departures-card__offer-wrap">
	<span class="product-departures-card__offer h5">
		<x-escape :content="$offer" />
	</span>

	<span class="product-departures-card__offer-text">
		<x-escape :content="$offer_text" />
	</span>

	@if ( true === $sold_out )
		<div class="product-departures-card__badge-sold-out h5">
			{{ __( 'Sold Out', 'qrk' ) }}
		</div>
	@endif
</div>
