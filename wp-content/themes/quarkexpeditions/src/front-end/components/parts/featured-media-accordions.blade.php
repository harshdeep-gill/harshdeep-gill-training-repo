@props( [
	'items' => [],
] )

@php
	if ( empty( $items ) ) {
		return;
	}
@endphp

<x-featured-media-accordions>
		<x-featured-media-accordions.media>
			@foreach( $items as $item )
				@if ( empty( $item['image_id'] ) )
					@continue
				@endif
				<x-featured-media-accordions.featured-image :image_id="$item['image_id'] ?? ''" :id="$item['id'] ?? ''" />
			@endforeach
		</x-featured-media-accordions.media>

		<x-featured-media-accordions.accordions>
			@foreach( $items as $item )
				@if ( empty( $item['title'] ) )
					@continue
				@endif
				<x-featured-media-accordions.accordion :title="$item['title'] ?? ''" :id="$item['id'] ?? ''">
					{!! $item['content'] ?? '' !!}
					@if ( ! empty( $item['image_id'] ) )
						<x-featured-media-accordions.featured-image :image_id="$item['image_id'] ?? ''" />
					@endif
				</x-featured-media-accordions.accordion>
			@endforeach
		</x-featured-media-accordions.accordions>
</x-featured-media-accordions>
