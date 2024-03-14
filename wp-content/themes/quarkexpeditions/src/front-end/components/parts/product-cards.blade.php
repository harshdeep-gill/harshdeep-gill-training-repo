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

				@foreach ( $card['children'] as $child_item )
					@if ( 'reviews' === $child_item['type'] )
						<x-product-cards.reviews
							:total_reviews="$child_item['total_reviews_text']"
							:review_rating="$child_item['rating']"
						/>
					@endif

					@if ( 'itinerary' === $child_item['type'] )
						<x-product-cards.itinerary
							:departure_date="$child_item['departure_date_text']"
							:duration="$child_item['duration_text']"
						/>
					@endif

					@if ( 'title' === $child_item['type'] )
						<x-product-cards.title :title="$child_item['title']" />
					@endif

					@if ( 'subtitle' === $child_item['type'] )
						<x-product-cards.subtitle :title="$child_item['subtitle']" />
					@endif

					@if ( 'description' === $child_item['type'] )
						<x-product-cards.description>
							{!! $child_item['description'] !!}
						</x-product-cards.description>
					@endif

					@if ( 'price' === $child_item['type'] )
						<x-product-cards.price
							:original_price="$child_item['original']"
							:discounted_price="$child_item['discounted']"
						/>
					@endif

					@if ( 'buttons' === $child_item['type'] )
						@if ( ! empty( $child_item['form_modal_cta'] ) && ! empty( $child_item['secondary_btn'] ) )
						<x-product-cards.buttons>
							<x-form-modal-cta form_id="inquiry-form">
								<x-button type="button" size="big">
									<x-escape :content="$child_item['form_modal_cta']['text']" />
								</x-button>
							</x-form-modal-cta>
							<x-button
								size="big"
								appearance="outline"
								:href="$child_item['secondary_btn']['url']"
							>
								<x-escape :content="$child_item['secondary_btn']['text']" />
							</x-button>
						</x-product-cards.buttons>
						@elseif ( ! empty( $child_item['call_cta_text'] ) && ! empty( $child_item['call_cta_url'] ) )
							<x-product-cards.buttons>
								<x-button icon="phone" size="big" :href="$child_item['call_cta_url']">
									<x-escape :content="$child_item['call_cta_text']" />
								</x-button>
							</x-product-cards.buttons>
						@endif
					@endif
				@endforeach
			</x-product-cards.card>

			@elseif ( 'media-content-card' === $card['type'] )
				{!! $card['slot'] !!}
		@endif
	@endforeach
</x-product-cards>
