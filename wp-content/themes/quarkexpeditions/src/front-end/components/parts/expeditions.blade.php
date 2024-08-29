@props( [
	'cards' => [],
] )

@php
	if ( empty( $cards ) ) {
		return;
	}
@endphp

<x-product-cards>
	@foreach ( $cards as $card )
		<x-product-cards.card>
			@if ( ! empty( $card['image_id'] ) )
				<x-product-cards.image :image_id="$card['image_id']" />
			@endif
			@if ( ! empty( $card['review_rating'] ) && ! empty( $card['total_reviews'] ) )
				<x-product-cards.reviews :review_rating="$card['review_rating']" :total_reviews="$card['total_reviews']" />
			@endif
			@if ( ! empty(  $card['itinerary_days'] ) )
				@php
					$duration = sprintf( __( '%d day itinerary', 'qrk' ), $card['itinerary_days'] )
				@endphp
				<x-product-cards.itinerary :duration="$duration" />
			@endif

			@if ( ! empty( $card['title'] ) )
				<x-product-cards.title :title="$card['title']" />
			@endif

			@if ( ! empty( $card['original_price'] ) && ! empty( $card['discounted_price'] ) )
				<x-product-cards.price
					:original_price="$card['original_price'] ?? ''"
					:discounted_price="$card['discounted_price'] ?? ''"
				/>
			@endif
		</x-product-cards.card>
	@endforeach
</x-product-cards>
