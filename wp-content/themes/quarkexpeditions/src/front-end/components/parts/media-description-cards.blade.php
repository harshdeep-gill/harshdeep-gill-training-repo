@props ( [
	'cards'       => [],
	'is_carousel' => false,
] )

@php
	if ( empty( $cards ) ) {
		return;
	}
@endphp

<x-media-description-cards :desktop_carousel="$is_carousel">
		@foreach ( $cards as $card )
			<x-media-description-cards.card>
				<x-media-description-cards.image :image_id="$card['image_id'] ?? 0" :alt="$card['title'] ?? ''" />
				<x-media-description-cards.content>
					<x-media-description-cards.title :title="$card['title'] ?? ''" heading_level="4" />
					<x-media-description-cards.description>
						{!! $card['description'] ?? '' !!}
					</x-media-description-cards.description>
				</x-media-description-cards.content>
			</x-media-description-cards.card>
		@endforeach
</x-media-description-cards>
