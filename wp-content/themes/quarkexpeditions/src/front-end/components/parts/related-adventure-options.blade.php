@props( [
	'adventure_options' => [],
	'show_title'        => true,
	'show_description'  => false,
] )

@php
	if ( empty( $adventure_options ) ) {
		return;
	}
@endphp

<x-info-cards layout="carousel">
	@foreach( $adventure_options as $adventure_option )
		<x-info-cards.card>
			<x-info-cards.image image_id="{{ $adventure_option['thumbnail'] ?? 0 }}" />
			<x-info-cards.content position="bottom">
				@if( $show_title )
					<x-info-cards.title title="{{ $adventure_option['title'] ?? '' }}" />
				@endif
				@if( $show_description )
					<x-info-cards.description>
						<x-content :content="$adventure_option['description'] ?? ''" />
					</x-info-cards.description>
				@endif
			</x-info-cards.content>
		</x-info-cards.card>
	@endforeach
</x-info-cards>
