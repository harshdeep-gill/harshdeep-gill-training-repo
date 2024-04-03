@props( [
	'items' => [],
] )

@php
	if ( empty( $items ) ) {
		return;
	}

@endphp

<x-product-cards>
	@foreach ( $items as $card )
		@if ( 'product-card' === $card['type'] )
			<x-product-cards.card>
				@if ( ! empty( $card['image']['id'] ) )
					<x-product-cards.image
						:image_id="$card['image']['id']"
						:is_immersive="$card['image']['is_immersive']"
					>
						@if ( ! empty( $card['cta_badge_text'] ) )
							<x-product-cards.badge-cta :text="$card['cta_badge_text']" />
						@endif

						@if ( ! empty( $card['time_badge_text'] ) )
							<x-product-cards.badge-time :text="$card['time_badge_text']" />
						@endif

						@if ( ! empty( $card['sold_out_badge_text'] ) )
							<x-product-cards.badge-sold-out :text="$card['sold_out_badge_text']" />
						@endif

						@if( ! empty( $card['info_ribbon_text'] ) )
							<x-product-cards.info-ribbon>
								{!! $card['info_ribbon_text'] !!}
							</x-product-cards.info-ribbon>
						@endif
					</x-product-cards.image>
				@endif

				@foreach ( $card['children'] as $child_item )
					@if ( 'reviews' === $child_item['type'] )
						<x-product-cards.reviews
							:total_reviews="$child_item['total_reviews_text'] ?? ''"
							:review_rating="$child_item['rating'] ?? ''"
						/>
					@endif

					@if ( 'itinerary' === $child_item['type'] )
						<x-product-cards.itinerary
							:departure_date="$child_item['departure_date_text'] ?? ''"
							:duration="$child_item['duration_text'] ?? ''"
						/>
					@endif

					@if ( 'title' === $child_item['type'] )
						@if ( ! empty( $child_item['title'] ) )
							<x-product-cards.title :title="$child_item['title']" />
						@endif
					@endif

					@if ( 'subtitle' === $child_item['type'] )
						@if ( ! empty( $child_item['subtitle'] ) )
							<x-product-cards.subtitle :title="$child_item['subtitle']" />
						@endif
					@endif

					@if ( 'description' === $child_item['type'] )
						@if ( ! empty( $child_item['description'] ) )
							<x-product-cards.description>
								{!! $child_item['description'] !!}
							</x-product-cards.description>
						@endif
					@endif

					@if ( 'price' === $child_item['type'] )
						<x-product-cards.price
							:original_price="$child_item['original'] ?? ''"
							:discounted_price="$child_item['discounted'] ?? ''"
						/>
					@endif

					@if ( 'buttons' === $child_item['type'] )
						@if ( $child_item['slot'] )
							<x-product-cards.buttons>
								{!! $child_item['slot'] !!}
							</x-product-cards.buttons>
						@endif
					@endif
				@endforeach
			</x-product-cards.card>

			@elseif ( 'media-content-card' === $card['type'] )
				@if ( ! empty( $card['slot'] ) )
					{!! $card['slot'] !!}
				@endif
		@endif
	@endforeach
</x-product-cards>
