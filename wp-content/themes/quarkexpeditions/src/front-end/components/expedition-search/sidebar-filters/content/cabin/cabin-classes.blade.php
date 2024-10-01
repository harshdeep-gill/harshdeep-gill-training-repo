@props( [
	'cabin_classes' => [],
] )

@php
	if ( empty( $cabin_classes ) || ! is_array( $cabin_classes ) ) {
		return;
	}
@endphp

<x-accordion.item>
	<x-accordion.item-handle title="{{ __( 'Cabin Class', 'qrk' ) }}" />
	<x-accordion.item-content>
	<quark-expedition-search-filter-cabin-classes>
		<x-form.field-group>
			@foreach ( $cabin_classes as $cabin_class )
				@if ( empty( $cabin_class['label'] ) || empty( $cabin_class['value'] ) )
					@continue
				@endif

				<x-expedition-search.sidebar-filters.checkbox name="cabin-classes" :label="$cabin_class['label']" :value="$cabin_class['value']" :count="$cabin_class['count']" />
			@endforeach
		</x-form.field-group>
	</quark-expedition-search-filter-cabin-classes>
	</x-accordion.item-content>
</x-accordion.item>
