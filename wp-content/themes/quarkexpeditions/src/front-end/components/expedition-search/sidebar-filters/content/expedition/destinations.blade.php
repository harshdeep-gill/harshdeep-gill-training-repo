@props( [
	'destinations' => [],
] )

@php
	if ( empty( $destinations ) || ! is_array( $destinations ) ) {
		return;
	}
@endphp

<x-accordion.item>
	<x-accordion.item-handle title="{{ __( 'Destinations', 'qrk' ) }}" />
	<x-accordion.item-content>
	<quark-expedition-search-filter-destinations>
		<x-form.field-group>
			@foreach ( $destinations as $destination )
				@if ( empty( $destination['label'] ) || empty( $destination['value'] ) || ! isset( $destination['count'] ) )
					@continue
				@endif

				@if ( empty( $destination['children'] ) || ! is_array( $destination['children'] ) )
					<x-expedition-search.sidebar-filters.checkbox name="destinations" :label="$destination['label']" :value="$destination['value']" :count="$destination['count']" />
				@else
					<div class="expedition-search__sidebar-filters-with-children">
						<x-expedition-search.sidebar-filters.checkbox name="destinations" :label="$destination['label']" :value="$destination['value']" :count="$destination['count']" />

						<x-form.field-group class="expedition-search__sidebar-filters-children">
							@foreach ( $destination['children'] as $child )
								@if ( empty( $child['label'] ) || empty( $child['value'] ) || ! isset( $child['count'] ) || empty( $child['parent_id'] ) )
									@continue
								@endif
								<x-expedition-search.sidebar-filters.checkbox name="destinations" :label="$child['label']" :value="$child['value']" :count="$child['count']" :parent="$child['parent_id']" />
							@endforeach
						</x-form.field-group>
					</div>
				@endif
			@endforeach
		</x-form.field-group>
	</quark-expedition-search-filter-destinations>
	</x-accordion.item-content>
</x-accordion.item>
