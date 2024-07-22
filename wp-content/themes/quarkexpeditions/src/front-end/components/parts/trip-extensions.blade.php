@props( [
	'pre_post_trips' => [],
	'show_title'        => true,
	'show_description'  => false,
] )

@php
	if ( empty( $pre_post_trips ) ) {
		return;
	}
@endphp

<x-info-cards layout="carousel">
	@foreach( $pre_post_trips as $pre_post_trip )
		<x-info-cards.card>
			<x-info-cards.image image_id="{{ $pre_post_trip['featured_image'] ?? 0 }}" />
			<x-info-cards.content position="bottom">
				@if( $show_title )
					<x-info-cards.title title="{{ $pre_post_trip['title'] ?? '' }}" />
				@endif
				@if( $show_description )
					<x-info-cards.description>
						{{ $pre_post_trip['description'] ?? '' }}
					</x-info-cards.description>
				@endif
			</x-info-cards.content>
		</x-info-cards.card>
	@endforeach
</x-info-cards>
