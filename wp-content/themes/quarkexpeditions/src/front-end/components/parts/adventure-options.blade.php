@props( [
	'cards'             => [],
	'carousel_overflow' => false,
] )

@php
	if ( empty( $cards ) ) {
		return;
	}
@endphp

<x-info-cards layout="carousel" :carousel_overflow="$carousel_overflow">
	@foreach ( $cards as $card )
		<x-info-cards.card size="big" :url="$card['permalink'] ?? ''">
			<x-info-cards.image :image_id="$card['featured_image'] ?? 0" />
			<x-info-cards.content position="top">
				<x-info-cards.overline>
					@if ( ! empty( $card['term'] ) )
						<x-escape :content="$card['term']" />
					@endif
				</x-info-cards.overline>
				<x-info-cards.title :title="$card['title'] ?? ''" />
				<x-info-cards.description>
					{!! $card['excerpt'] !!}
				</x-info-cards.description>
				<x-info-cards.cta text="Read Post" />
			</x-info-cards.content>
		</x-info-cards.card>
	@endforeach
</x-info-cards>
