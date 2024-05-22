@props( [
	'related_posts' => [],
] )

@php
	if ( empty( $related_posts ) ) {
		return;
	}
@endphp

<x-info-cards>
	@foreach ( $related_posts as $related_post )
		@if ( $related_post['post'] instanceof WP_Post )
			<x-info-cards.card size="big" :url="$related_post['permalink'] ?: ''">
				<x-info-cards.image :image_id="$related_post['post_thumbnail'] ?: 0" />
				<x-info-cards.content position="top">
					<x-info-cards.title :title="$related_post['post']?->post_title ?? ''" />
					<x-info-cards.cta text="Read Post" />
				</x-info-cards.content>
			</x-info-cards.card>
		@endif
	@endforeach
</x-info-cards>
