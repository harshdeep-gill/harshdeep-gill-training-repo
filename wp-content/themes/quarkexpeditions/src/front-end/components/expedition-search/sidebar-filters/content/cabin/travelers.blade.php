@props( [
	'travelers'  => [],
	'is_compact' => false,
] )

@php
	if ( empty( $travelers ) || ! is_array( $travelers ) ) {
		return;
	}

	$the_id = 'expedition-search-filter-travelers' . ( ! empty( $is_compact ) ? '-compact' : '' );
@endphp

<x-accordion.item id="{{ $the_id }}">
	<quark-expedition-search-filter-travelers>
		<x-accordion.item-handle>
			<x-escape :content=" __( 'Travelers', 'qrk' ) " /> <span class="expedition-search__filter-count"></span>
		</x-accordion-item.handle>
		<x-accordion.item-content>
			<x-form.field-group>
				@foreach ( $travelers as $traveler )
					@if ( empty( $traveler['label'] ) || empty( $traveler['value'] ) )
						@continue
					@endif

					<x-expedition-search.sidebar-filters.checkbox name="travelers" :label="$traveler['label']" :value="$traveler['value']" />
				@endforeach
			</x-form.field-group>
		</x-accordion.item-content>
	</quark-expedition-search-filter-travelers>
</x-accordion.item>
