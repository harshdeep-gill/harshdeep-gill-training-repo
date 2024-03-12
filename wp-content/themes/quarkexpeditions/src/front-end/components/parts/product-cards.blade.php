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
					:departure_date_text="$card['itinerary']['departure_date_text']"
					:duration_text="$card['itinerary']['duration_text']"
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
				<x-product-cards.buttons :columns="2">
					<x-button><x-escape :content="$card['buttons']['call_cta_text']" /></x-button>
				</x-product-cards.buttons>
			@endif
		</x-product-cards.card>
	@endforeach
</x-product-cards>