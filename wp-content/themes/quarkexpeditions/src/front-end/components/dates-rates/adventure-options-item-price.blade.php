@props( [
	'price'    => '',
	'currency' => '',
	'count'    => '',
] )

@php
	if ( empty( $price ) ) {
		return;
	}
@endphp

<p class="dates-rates__adventure-options-item-price-wrap">
	<strong class="dates-rates__adventure-options-item-price"><x-escape content="{{ $price }}" /></strong>
	<span class="dates-rates__adventure-options-item-currency"><x-escape content="{{ $currency }}" /></span>
	<span>
		@if ( ! empty( $count ) )
			({{ $count }})
		@endif
	</span>
</p>
