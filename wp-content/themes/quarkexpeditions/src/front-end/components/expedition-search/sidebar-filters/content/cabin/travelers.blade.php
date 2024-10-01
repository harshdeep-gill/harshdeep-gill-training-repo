@props( [
	'travelers' => [],
] )

@php
	if ( empty( $travelers ) || ! is_array( $travelers ) ) {
		return;
	}
@endphp

<x-accordion.item>
	<x-accordion.item-handle title="{{ __( 'Travelers', 'qrk' ) }}" />
	<x-accordion.item-content>
	<quark-expedition-search-filter-travelers>
		<x-form.field-group>
			@foreach ( $travelers as $traveler )
				@if ( empty( $traveler['label'] ) || empty( $traveler['value'] ) )
					@continue
				@endif

				<x-expedition-search.sidebar-filters.checkbox name="travelers" :label="$traveler['label']" :value="$traveler['value']" :count="$traveler['count']" />
			@endforeach
		</x-form.field-group>
	</quark-expedition-search-filter-travelers>
	</x-accordion.item-content>
</x-accordion.item>
