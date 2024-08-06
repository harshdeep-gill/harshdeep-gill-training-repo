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
			<x-accordion.item-content>
				@foreach ( $item['accordion_items'] as $accordion_item )
					<br />
					<strong>{{ $accordion_item['title'] ?? '' }}</strong>
					<br />
					<x-escape :content="$accordion_item['description'] ?? ''" />
					<br />
				@endforeach
			</x-accordion.item-content>
		</x-accordion.item>
	@endforeach
</x-accordion>
