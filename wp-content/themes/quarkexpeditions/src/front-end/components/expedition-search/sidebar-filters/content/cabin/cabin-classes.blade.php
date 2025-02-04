@props( [
	'cabin_classes' => [],
	'is_compact'    => false,
] )

@php
	if ( empty( $cabin_classes ) || ! is_array( $cabin_classes ) ) {
		return;
	}

	$the_id = 'expedition-search-filter-cabin-classes' . ( ! empty( $is_compact ) ? '-compact' : '' );
@endphp

<x-accordion.item id="{{ $the_id }}">
	<quark-expedition-search-filter-cabin-classes>
		<x-accordion.item-handle>
			<x-escape :content="__( 'Cabin Class', 'qrk' )" /> <span class="expedition-search__filter-count"></span>
		</x-accordion-item.handle>
		<x-accordion.item-content>
			<x-form.field-group>
				@foreach ( $cabin_classes as $cabin_class )
					@if ( empty( $cabin_class['label'] ) || empty( $cabin_class['value'] ) )
						@continue
					@endif

					<x-expedition-search.sidebar-filters.checkbox name="cabin-classes" :label="$cabin_class['label']" :value="$cabin_class['label']" />
				@endforeach
			</x-form.field-group>
		</x-accordion.item-content>
	</quark-expedition-search-filter-cabin-classes>
</x-accordion.item>
