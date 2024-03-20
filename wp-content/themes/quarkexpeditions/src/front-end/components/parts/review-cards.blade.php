@props( [
	'is_carousel' => 'true',
	'items'       => [],
] )

@php
	if ( empty( $items ) ) {
		return;
	}
@endphp

<x-review-cards :is_carousel="$is_carousel">
	@foreach ( $items as $card )
		@if ( 'review-card' === $card['type'] )
			<x-review-cards.card>
				@foreach ( $card['children'] as $child_item )
					@if ( 'rating' === $child_item['type'] )
						@if ( ! empty( $child_item['rating'] ) )
							<x-review-cards.rating :rating="$child_item['rating']"/>
						@endif
					@endif

					@if ( 'title' === $child_item['type'] )
						@if ( ! empty( $child_item['title'] ) )
							<x-review-cards.title :title="$child_item['title']" />
						@endif
					@endif

					@if ( 'review' === $child_item['type'] )
						@if ( ! empty( $child_item['review'] ) )
							<x-review-cards.content>
								{!! $child_item['review'] !!}
							</x-review-cards.content>
						@endif
					@endif

					@if ( 'author' === $child_item['type'] )
						@if ( ! empty( $child_item['author'] ) )
							<x-review-cards.author :name="$child_item['author']" />
						@endif
					@endif

					@if ( 'author-details' === $child_item['type'] )
						@if ( ! empty( $child_item['author_details'] ) )
							<x-review-cards.author-details :text="$child_item['author_details']" />
						@endif
					@endif
				@endforeach
			</x-review-cards.card>
		@endif
	@endforeach
</x-review-cards>