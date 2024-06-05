@props( [
	'has_border' => true,
	'items'      => [],
] )

@php
	if ( empty( $items ) ) {
		return;
	}
@endphp

<x-accordion :full_border="$has_border">
	@foreach ( $items as $item )
		<x-accordion.item :open="$item['open'] ?? false">
			<x-accordion.item-handle :title="$item['title'] ?? ''" />
			<x-accordion.item-content>
				{!! $item['content'] ?? '' !!}
			</x-accordion.item-content>
		</x-accordion.item>
	@endforeach
</x-accordion>