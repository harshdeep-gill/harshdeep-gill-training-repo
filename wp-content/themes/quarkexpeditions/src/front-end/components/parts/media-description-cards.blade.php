@props ( [
	'cards' => [],
] )

@php
	if ( empty( $cards ) ) {
		return;
	}
@endphp

<x-media-description-cards>
		@foreach ( $cards as $card )
			<x-media-description-cards.card>
				<x-media-description-cards.image :image_id="$card['image_id'] ?? 0" :alt="$card['title'] ?? ''" />
				<x-media-description-cards.content>
					<x-media-description-cards.title :title="$card['title'] ?? ''" heading_level="4" />
					<x-media-description-cards.description>
						{!! $card['description'] ?? '' !!}
					</x-media-description-cards.description>
				</x-media-description-cards.content>
				@if ( ! empty( $card['buttons'] ) )
					<div class="media-description-cards__cta-button">
						{!! $card['buttons'] !!}
					</div>
				@endif
			</x-media-description-cards.card>
		@endforeach
</x-media-description-cards>
