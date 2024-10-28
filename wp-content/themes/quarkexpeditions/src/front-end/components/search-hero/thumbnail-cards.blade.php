@props( [
	'items'               => [],
	'hero_details_slider' => [],
] )

<div class="search-hero__thumbnail-cards">
	{!! $slot !!}
</div>

@if ( ! empty( $items ) )
	<div class="search-hero__thumbnail-cards search-hero__thumbnail-cards-mobile">
		<x-thumbnail-cards :is_carousel="true" :full_width="true">
			@if ( ! empty( $hero_details_slider['items'] ) && ! empty( $hero_details_slider['items'][0] ) )
				<x-thumbnail-cards.card
					size="large"
					url="{{ $hero_details_slider['items'][0]['cta']['url'] ?? '' }}"
					:image_id="$hero_details_slider['items'][0]['media_id'] ?? 0"
				>
					<x-thumbnail-cards.title :title="$hero_details_slider['items'][0]['title'] ?? ''" />
				</x-thumbnail-cards.card>
			@endif
			@foreach ( $items as $item)
				<x-thumbnail-cards.card
					size="large"
					url="{{ $item['url'] ?? '' }}"
					:image_id="$item['image_id'] ?? 0"
				>
					<x-thumbnail-cards.title :title="$item['title'] ?? ''" />
				</x-thumbnail-cards.card>
			@endforeach
		</x-thumbnail-cards>
	</div>
@endif