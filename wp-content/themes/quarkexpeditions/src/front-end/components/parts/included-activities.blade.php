@props( [
	'activities'       => [],
	'show_title'       => true,
	'show_description' => true,
] )

@php
if ( empty( $activities ) ) {
	return;
}
@endphp

<x-info-cards layout="carousel">
	@foreach( $activities as $activity )
		<x-info-cards.card :url="$activity['permalink'] ?? ''">
			<x-info-cards.image image_id="{{ $activity['thumbnail'] ?? 0 }}" />
			<x-info-cards.content position="bottom">
				@if( $show_title )
					<x-info-cards.title title="{{ $activity['title'] ?? '' }}" />
				@endif
				@if( $show_description )
					<x-info-cards.description>
						{!! $activity['description'] !!}
					</x-info-cards.description>
				@endif
			</x-info-cards.content>
		</x-info-cards.card>
	@endforeach
</x-info-cards>
