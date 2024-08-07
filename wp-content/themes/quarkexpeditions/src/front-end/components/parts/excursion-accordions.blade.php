@props( [
	'items' => [],
] )

@php
	if ( empty( $items ) ) {
		return;
	}
@endphp

<x-accordion>
	@foreach ( $items as $item )
		<x-accordion.item>
			<x-accordion.item-handle :title="$item['accordion_title'] ?? ''" />
			@if ( ! empty( $item['accordion_items'] ) )
				<x-accordion.item-content>
					@foreach ( $item['accordion_items'] as $accordion_item )
						<strong>{!! $accordion_item['title'] ?? '' !!}</strong>
						{!! $accordion_item['description'] ?? '' !!}
					@endforeach
				</x-accordion.item-content>
			@endif
		</x-accordion.item>
	@endforeach
</x-accordion>
