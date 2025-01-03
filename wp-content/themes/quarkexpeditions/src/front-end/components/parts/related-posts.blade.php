@props( [
	'cards' => [],
] )

@php
	if ( empty( $cards ) ) {
		return;
	}
@endphp

<x-info-cards>
	@foreach ( $cards as $card )
		@if ( $card['post'] instanceof WP_Post )
			<x-info-cards.card size="big" :url="$card['permalink'] ?? ''">
				<x-info-cards.image :image_id="$card['featured_image'] ?? 0" />
				<x-info-cards.content position="top">
					<x-info-cards.title :title="$card['title'] ?? ''" />
					<x-info-cards.cta :text="__( 'Read Post', 'qrk' )" />
				</x-info-cards.content>
			</x-info-cards.card>
		@endif
	@endforeach
</x-info-cards>
