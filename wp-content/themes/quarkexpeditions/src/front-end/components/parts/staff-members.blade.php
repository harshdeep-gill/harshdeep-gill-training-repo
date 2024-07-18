@props( [
	'cards'  => [],
	'layout' => 'grid',
] )

@php
	if ( empty( $cards ) ) {
		return;
	}
@endphp

<x-info-cards :layout="$layout">
	@foreach ( $cards as $card )
		<x-info-cards.card size="big" :url="$card['permalink'] ?? ''">
			<x-info-cards.image :image_id="$card['featured_image'] ?? 0" />
			<x-info-cards.content position="bottom">
			<x-info-cards.overline>
				@if ( ! empty( $card['season'] ) )
					<x-escape :content="$card['season'] ?? ''" />
				@endif
			</x-info-cards.overline>
				<x-info-cards.title :title="$card['title'] ?? ''" />
				<x-info-cards.description>
					<x-escape :content="$card['role'] ?? ''" />
				</x-info-cards.description>
				<x-info-cards.cta text="Read more" />
			</x-info-cards.content>
		</x-info-cards.card>
	@endforeach
</x-info-cards>
