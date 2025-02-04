@props( [
	'expeditions' => [],
	'is_compact'  => false,
] )

@php
	if ( empty( $expeditions ) || ! is_array( $expeditions ) ) {
		return;
	}

	$the_id = 'expedition-search-filter-expeditions' . ( ! empty( $is_compact ) ? '-compact' : '' );
@endphp

<x-accordion.item id="{{ $the_id }}">
	<quark-expedition-search-filter-expeditions>
		<x-accordion.item-handle>
			<x-escape :content=" __( 'Expeditions', 'qrk' ) " /> <span class="expedition-search__filter-count"></span>
		</x-accordion-item.handle>
		<x-accordion.item-content>
			<x-form.field-group>
				@foreach ( $expeditions as $expedition )
					@if ( empty( $expedition['label'] ) || empty( $expedition['value'] ) || ! isset( $expedition['count'] ) )
						@continue
					@endif

					<x-expedition-search.sidebar-filters.checkbox name="expeditions" :label="$expedition['label']" :value="$expedition['value']" :count="$expedition['count']" />
				@endforeach
			</x-form.field-group>
		</x-accordion.item-content>
	</quark-expedition-search-filter-expeditions>
</x-accordion.item>
