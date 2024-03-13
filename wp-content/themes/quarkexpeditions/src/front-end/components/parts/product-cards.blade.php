@props( [
	'items' => [],
] )

<?php
	if ( empty( $items ) ) {
		return;
	}
?>

<x-product-cards>
	@foreach ( $items as $card )
		@if( 'product-card' === $card['type'] )
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
							<x-content :content="$card['info_ribbon_text']" />
						</x-product-cards.info-ribbon>
					@endif
				</x-product-cards.image>

				@if ( ! empty( $card['reviews'] ) )
					<x-product-cards.reviews
						:total_reviews_text="$card['reviews']['total_reviews_text']"
						:review_rating="$card['reviews']['rating']"
					/>
				@endif

				@if ( ! empty( $card['itinerary'] ) )
					<x-product-cards.itinerary
						:departure_date="$card['itinerary']['departure_date_text']"
						:duration="$card['itinerary']['duration_text']"
					/>
				@endif

				@if ( ! empty( $card['title'] ) )
					<x-product-cards.title :title="$card['title']" />
				@endif

				@if ( ! empty( $card['subtitle'] ) )
					<x-product-cards.subtitle :title="$card['subtitle']" />
				@endif

				@if ( ! empty( $card['description'] ) )
					<x-product-cards.description>
						<x-content :content="$card['description']" />
					</x-product-cards.description>
				@endif

				@if ( ! empty( $card['price'] ) )
					<x-product-cards.price
						:original_price="$card['price']['original']"
						:discounted_price="$card['price']['discounted']"
					/>
				@endif

				@if ( ! empty( $card['buttons'] ) )
					@if ( ! empty( $card['buttons']['form_modal_cta'] ) && ! empty( $card['buttons']['secondary_btn'] ) )
					<x-product-cards.buttons>
						<x-form-modal-cta form_id="inquiry-form">
							<x-button type="button" size="big">
								<x-escape :content="$card['buttons']['form_modal_cta']['text']" />
							</x-button>
						</x-form-modal-cta>
						<x-button
							size="big"
							appearance="outline"
							:href="$card['buttons']['secondary_btn']['url']"
						>
							<x-escape :content="$card['buttons']['secondary_btn']['text']" />
						</x-button>
					</x-product-cards.buttons>
					@elseif ( ! empty( $card['buttons']['call_cta_text'] ) && ! empty( $card['buttons']['call_cta_url'] ) )
						<x-product-cards.buttons>
							<x-button icon="phone" size="big" :href="$card['buttons']['call_cta_url']">
								<x-escape :content="$card['buttons']['call_cta_text']" />
							</x-button>
						</x-product-cards.buttons>
					@endif
				@endif
			</x-product-cards.card>
			@elseif( 'media-content-card' === $card['type'] )
				<x-parts.media-content-card
					:is_compact="$card['is_compact']"
					:image_id="$card['image_id']"
					:content="$card['content']"
				/>
		@endif
	@endforeach
</x-product-cards>