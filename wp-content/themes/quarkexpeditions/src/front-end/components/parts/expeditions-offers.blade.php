@props( [
	'cards' => [],
] )

@php
	if ( empty( $cards ) ) {
		return;
	}
@endphp

<x-product-cards carousel_overflow="true">
	@foreach ( $cards as $card )
		<x-product-cards.card>
			@if ( ! empty( $card['image_id'] ) )
				<x-product-cards.image
					:is_immersive="true"
					:image_id="$card['image_id']"
				/>
			@endif
			@if ( ! empty( $card['itinerary_days'] ) && ! empty ( $card['start_date'] ) )
				@php
					$departure_date = sprintf( __( ' Departs %s', 'qrk' ), $card['start_date'] );
					$duration       = sprintf( __( '%d Days', 'qrk' ), $card['itinerary_days'] );
				@endphp
				<x-product-cards.itinerary
					:departure_date="$departure_date"
					:duration="$duration"
				/>
			@endif

			@if ( ! empty( $card['title'] ) && ! empty( $card['subtitle'] ) )
				<x-maybe-link href="{{ $card['url'] }}">
					<x-product-cards.title :title="$card['title']" />
				</x-maybe-link>
				<x-product-cards.subtitle :title="$card['subtitle']" />
			@endif

			@if ( ! empty( $card['discounted_price'] ) )
				@php
					$price_text = sprintf( __( 'From %s', 'qrk' ), $card['discounted_price'] );
				@endphp
				<x-product-cards.price-content
					:title="__( 'Sale price from', 'qrk' )"
					:text="$price_text"
				/>
			@endif
		</x-product-cards.card>
	@endforeach
</x-product-cards>
