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
				<x-product-cards.image
					:is_immersive="true"
					:image_id="$card['image_id']"
					url="{{ $card['url'] }}"
				/>
			@endif
			@if ( ! empty(  $card['itinerary_days'] ) )
				@php
					$duration = sprintf( __( '%d DAYS', 'qrk' ), $card['itinerary_days'] )
				@endphp
				<x-product-cards.overline :text="$duration" />
			@endif

			@if ( ! empty( $card['title'] ) )
				<x-maybe-link href="{{ $card['url'] }}">
					<x-product-cards.title :title="$card['title']" />
				</x-maybe-link>
			@endif

			@if ( ! empty( $card['subtitle'] ) )
				<x-product-cards.subtitle :title="$card['subtitle']" />
			@endif

			@if ( ! empty ( $card['is_fly_cruise'] ) )
				<x-product-cards.icon-content
					icon="fly-express"
				>
					{!! __( 'Fly/Cruise Express', 'qrk' ) !!}
				</x-product-cards.icon-content>
			@endif

			@if ( ! empty( $card['discounted_price'] ) )
				@php
					$price_text = sprintf( __( 'From %s per person', 'qrk' ), $card['discounted_price'] )
				@endphp
				<x-product-cards.price-content :text="$price_text" />
			@endif
		</x-product-cards.card>
	@endforeach
</x-product-cards>
