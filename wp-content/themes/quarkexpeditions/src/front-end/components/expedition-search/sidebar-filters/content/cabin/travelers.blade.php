@props( [
	'travelers' => [],
] )

@php
	if ( empty( $travelers ) || ! is_array( $travelers ) ) {
		return;
	}
@endphp

<quark-expedition-search-filter-travelers>
	<x-accordion.item>
		<x-accordion.item-handle>
			<x-escape :content=" __( 'Travelers', 'qrk' ) " /> <span class="expedition-search__filter-count"></span>
		</x-accordion-item.handle>
		<x-accordion.item-content>
			<x-form.field-group>
				@foreach ( $travelers as $traveler )
					@if ( empty( $traveler['label'] ) || empty( $traveler['value'] ) )
						@continue
					@endif

					<x-expedition-search.sidebar-filters.checkbox name="travelers" :label="$traveler['label']" :value="$traveler['value']" :count="$traveler['count']" />
				@endforeach
			</x-form.field-group>
		</x-accordion.item-content>
	</x-accordion.item>
</quark-expedition-search-filter-travelers>
