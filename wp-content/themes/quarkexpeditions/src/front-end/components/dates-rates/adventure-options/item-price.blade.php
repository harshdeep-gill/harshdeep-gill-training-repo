@props( [
	'price'    => '',
	'currency' => '',
	'count'    => 0,
] )

@php
	if ( empty( $price ) ) {
		return;
	}
@endphp

<p class="dates-rates__adventure-options-item-price-wrap">
	<strong class="dates-rates__adventure-options-item-price"><x-escape content="{{ str_replace( [ 'USD', 'CAD', 'AUD', 'EUR', 'GBP' ], '', $price ) }}" /></strong>
	<span class="dates-rates__adventure-options-item-currency"><x-escape content="{{ $currency }}" /></span>
	<span>
		({{ $count }})
	</span>
</p>
